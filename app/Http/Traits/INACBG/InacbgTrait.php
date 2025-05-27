<?php

namespace App\Http\Traits\INACBG;

use Illuminate\Support\Facades\Http;
use Exception;

/**
 * Trait untuk integrasi SIMRS dengan E-Klaim INA-CBG
 * Berdasarkan petunjuk teknis v5 dari Kemenkes fileciteturn1file0 dan alur dasar integrasi fileciteturn1file12
 */
trait InacbgTrait
{
    // Web service endpoint (ws.php) dan opsi debug
    protected $wsUrl;
    protected $debugMode;

    // Encryption key hex (256-bit) dari SIMRS
    protected $encryptionKey;

    // Kode rumah sakit (faskes code)
    protected $hospitalCode;

    // Consumer ID/Secret BPJS (bisa kosong jika belum tersedia)
    protected $consumerId;
    protected $consumerSecret;

    /**
     * Inisialisasi konfigurasi dari .env
     */
    public function initializeInacbg()
    {
        $this->wsUrl          = env('INACBG_WS_URL');
        $this->encryptionKey  = env('INACBG_KEY');
        $this->hospitalCode   = env('INACBG_HOSPITAL_CODE');
        $this->consumerId     = env('INACBG_CONSUMER_ID', '');
        $this->consumerSecret = env('INACBG_CONSUMER_SECRET', '');
        $this->debugMode      = env('INACBG_DEBUG', false);
    }

    /**
     * mc_encrypt: AES-256-CBC + HMAC-SHA256 signature, lalu base64
     * Sesuai dengan petunjuk teknik INA-CBG v5
     */
    protected function mcEncrypt(string $data): string
    {
        $key = hex2bin($this->encryptionKey);
        if (mb_strlen($key, '8bit') !== 32) {
            throw new Exception('Needs a 256-bit key!');
        }
        $ivSize = openssl_cipher_iv_length('aes-256-cbc');
        $iv = random_bytes($ivSize);
        $encrypted = openssl_encrypt($data, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
        $signature = substr(hash_hmac('sha256', $encrypted, $key, true), 0, 10);
        return base64_encode($signature . $iv . $encrypted);
    }

    /**
     * mc_decrypt: Proses kebalikan dari mcEncrypt
     */
    protected function mcDecrypt(string $encryptedData): string
    {
        $key = hex2bin($this->encryptionKey);
        if (mb_strlen($key, '8bit') !== 32) {
            throw new Exception('Needs a 256-bit key!');
        }
        $decoded = base64_decode($encryptedData);
        $signature = mb_substr($decoded, 0, 10, '8bit');
        $ivSize = openssl_cipher_iv_length('aes-256-cbc');
        $iv = mb_substr($decoded, 10, $ivSize, '8bit');
        $cipherText = mb_substr($decoded, 10 + $ivSize, null, '8bit');
        $calcSignature = mb_substr(hash_hmac('sha256', $cipherText, $key, true), 0, 10, '8bit');
        if (!hash_equals($signature, $calcSignature)) {
            throw new Exception('Signature does not match');
        }
        return openssl_decrypt($cipherText, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
    }

    /**
     * Kirim request ke Web Service INA-CBG
     *
     * @param string $method   Nama method (metadata.method)
     * @param array  $metadata Metadata tambahan (misal nomor_rm)
     * @param array  $data     Payload data sesuai method
     * @return array           Response ter-dekripsi (atau mentah jika debug)
     * @throws Exception       Jika gagal request atau dekripsi
     */
    protected function callWs(string $method, array $metadata = [], array $data = []): array
    {
        $this->initializeInacbg();

        // Susun request
        $payload = [
            'metadata' => array_merge(
                [
                    'method'       => $method,
                    'kode_rs'      => $this->hospitalCode,
                    'consumer_id'  => $this->consumerId,
                    'consumer_secret' => $this->consumerSecret
                ],
                $metadata
            ),
            'data' => $data
        ];
        $json = json_encode($payload);

        // Tentukan endpoint (mode debug optional) dan body
        $url = $this->wsUrl . ($this->debugMode ? '?mode=debug' : '');
        if ($this->debugMode) {
            $body = $json;
        } else {
            $body = $this->mcEncrypt($json);
        }

        // Panggil Web Service
        $response = Http::timeout(10)
            ->withHeaders(['Content-Type' => 'application/x-www-form-urlencoded'])
            ->post($url, ['rq' => $body]);

        if ($response->successful()) {
            $respBody = $response->body();
            if (!$this->debugMode) {
                $respBody = $this->mcDecrypt($respBody);
            }
            return json_decode($respBody, true) ?? [];
        }

        throw new Exception('INA-CBG WS request failed: ' . $response->body());
    }




    // ... (inialisasi, encrypt/decrypt, callWs)

    public function newClaim(array $metadata, array $data): array
    {
        return $this->callWs('new_claim', $metadata, $data);
    }

    public function setClaimData(array $metadata, array $data): array
    {
        return $this->callWs('set_claim_data', $metadata, $data);
    }

    public function grouperStage1(array $metadata, array $data): array
    {
        return $this->callWs('grouper', array_merge($metadata, ['stage' => 1]), $data);
    }

    public function grouperStage2(array $metadata, array $data): array
    {
        return $this->callWs('grouper', array_merge($metadata, ['stage' => 2]), $data);
    }

    public function claimFinal(array $metadata, array $data): array
    {
        return $this->callWs('claim_final', $metadata, $data);
    }

    public function sendClaim(array $metadata, array $data): array
    {
        return $this->callWs('send_claim', $metadata, $data);
    }

    public function sendClaimIndividual(array $metadata, array $data): array
    {
        return $this->callWs('send_claim_individual', $metadata, $data);
    }

    public function pullClaim(array $metadata, array $data): array
    {
        return $this->callWs('pull_claim', $metadata, $data);
    }

    public function getClaimData(array $metadata, array $data): array
    {
        return $this->callWs('get_claim_data', $metadata, $data);
    }

    public function getClaimStatus(array $metadata, array $data): array
    {
        return $this->callWs('get_claim_status', $metadata, $data);
    }
}
