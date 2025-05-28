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
    /**
     * Inisialisasi konfigurasi dari .env
     */
    public function initializeInacbg(): void
    {
        $this->wsUrl          = env('INACBG_WS_URL');
        $this->encryptionKey  = env('INACBG_KEY');
        $this->hospitalCode   = env('INACBG_HOSPITAL_CODE');
        $this->consumerId     = env('INACBG_CONSUMER_ID', '');
        $this->consumerSecret = env('INACBG_CONSUMER_SECRET', '');
        $this->debugMode      = env('INACBG_DEBUG', false);

        // Validasi konfigurasi penting
        if (empty($this->wsUrl)) {
            throw new \RuntimeException('INA-CBG WS URL belum dikonfigurasi');
        }
        if (empty($this->encryptionKey)) {
            throw new \RuntimeException('INA-CBG encryption key belum dikonfigurasi');
        }
    }

    function mcEncrypt($data)
    {
        $key = hex2bin($this->encryptionKey);
        if (mb_strlen($key, "8bit") !== 32) {
            throw new Exception("Needs a 256-bit key!");
        }

        $iv_size = openssl_cipher_iv_length("aes-256-cbc");
        $iv = openssl_random_pseudo_bytes($iv_size);
        $encrypted = openssl_encrypt($data, "aes-256-cbc", $key, OPENSSL_RAW_DATA, $iv);
        $signature = mb_substr(hash_hmac("sha256", $encrypted, $key, true), 0, 10, "8bit");
        $encoded = chunk_split(base64_encode($signature . $iv . $encrypted));
        return $encoded;
    }

    function mcDecrypt($str)
    {
        $key = hex2bin($this->encryptionKey);
        if (mb_strlen($key, "8bit") !== 32) {
            throw new Exception("Needs a 256-bit key!");
        }

        $iv_size = openssl_cipher_iv_length("aes-256-cbc");
        $decoded = base64_decode($str);
        $signature = mb_substr($decoded, 0, 10, "8bit");
        $iv = mb_substr($decoded, 10, $iv_size, "8bit");
        $encrypted = mb_substr($decoded, $iv_size + 10, NULL, "8bit");
        $calc_signature = mb_substr(hash_hmac("sha256", $encrypted, $key, true), 0, 10, "8bit");
        if (!$this->mcCompare($signature, $calc_signature)) {
            return "SIGNATURE_NOT_MATCH";
        }

        $decrypted = openssl_decrypt($encrypted, "aes-256-cbc", $key, OPENSSL_RAW_DATA, $iv);
        return $decrypted;
    }

    function mcCompare($a, $b)
    {
        if (strlen($a) !== strlen($b)) {
            return false;
        }
        $result = 0;
        for ($i = 0; $i < strlen($a); $i++) {
            $result |= ord($a[$i]) ^ ord($b[$i]);
        }
        return $result == 0;
    }


    protected function callWs(string $method, array $metadata = [], array $data = []): array
    {
        $this->initializeInacbg();

        // 1) Siapkan payload sama seperti sebelumnya
        $payload = [
            'metadata' => array_merge(
                ['method' => $method],
                $metadata
            ),
            'data'     => $data,
        ];
        $jsonEncrypted = $this->mcEncrypt(json_encode($payload, JSON_THROW_ON_ERROR));

        // 2) Kirim request via Laravel HTTP Client
        $response = Http::withHeaders([
            'Content-Type' => 'application/x-www-form-urlencoded',
        ])
            ->timeout(30)  // atur timeout sesuai kebutuhan
            // jika endpoint benar-benar mengharapkan body “mentah” berisi string terenkripsi:
            ->withBody($jsonEncrypted, 'application/x-www-form-urlencoded')
            ->post($this->wsUrl);

        // 4) Buang baris pertama & terakhir, lalu decrypt
        $first = strpos($response, "\n") + 1;
        $last = strrpos($response, "\n") - 1;
        $middle = substr($response, $first, strlen($response) - $first - $last);
        $decrypted   = $this->mcDecrypt($middle);
        // 5) Decode JSON hasil decrypt
        return json_decode($decrypted, true) ?? [];
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
