<?php

namespace App\Http\Traits\IDRG;

use Illuminate\Support\Facades\Http;
use RuntimeException;
use Exception;

trait IdrgTrait
{
    // ==== Properti yang diisi dari .env ====
    protected string $wsUrl;
    protected string $encryptionKey;     // hex 64 chars (256-bit)
    protected string $hospitalCode;
    protected string $consumerId;
    protected string $consumerSecret;
    protected bool   $debugMode = false;

    /**
     * Inisialisasi konfigurasi dari .env (IDRG_*)
     */
    public function initializeIdrg(): void
    {
        $this->wsUrl          = (string) env('IDRG_WS_URL');
        $this->encryptionKey  = (string) env('IDRG_KEY'); // hex string
        $this->hospitalCode   = (string) env('IDRG_HOSPITAL_CODE', '');
        $this->consumerId     = (string) env('IDRG_CONSUMER_ID', '');
        $this->consumerSecret = (string) env('IDRG_CONSUMER_SECRET', '');
        $this->debugMode      = (bool)   env('IDRG_DEBUG', false);

        if ($this->wsUrl === '') {
            throw new RuntimeException('IDRG WS URL belum dikonfigurasi');
        }
        if ($this->encryptionKey === '') {
            throw new RuntimeException('IDRG encryption key belum dikonfigurasi');
        }

        // Validasi panjang key 256-bit setelah hex2bin.
        $keyBin = @hex2bin($this->encryptionKey);
        if ($keyBin === false || strlen($keyBin) !== 32) {
            throw new RuntimeException('IDRG_KEY harus hex 64-karakter untuk kunci 256-bit.');
        }
    }

    // ==== ENKRIPSI / DEKRIPSI (AES-256-CBC + HMAC-SHA256 10 byte) ====

    protected function idrgEncrypt(string $data): string
    {
        $key = hex2bin($this->encryptionKey);
        if (mb_strlen($key, '8bit') !== 32) {
            throw new Exception('Needs a 256-bit key!');
        }

        $ivSize    = openssl_cipher_iv_length('aes-256-cbc');
        $iv        = random_bytes($ivSize);
        $encrypted = openssl_encrypt($data, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
        if ($encrypted === false) {
            throw new RuntimeException('Encrypt gagal.');
        }

        $signature = mb_substr(hash_hmac('sha256', $encrypted, $key, true), 0, 10, '8bit');
        // langsung base64_encode tanpa chunk_split agar tidak ada newline acak
        return base64_encode($signature . $iv . $encrypted);
    }

    protected function idrgDecrypt(string $payloadBase64): string
    {
        $key = hex2bin($this->encryptionKey);
        if (mb_strlen($key, '8bit') !== 32) {
            throw new Exception('Needs a 256-bit key!');
        }

        $ivSize = openssl_cipher_iv_length('aes-256-cbc');
        $decoded = base64_decode($payloadBase64, true);
        if ($decoded === false) {
            throw new RuntimeException('Base64 decode gagal.');
        }

        $signature = mb_substr($decoded, 0, 10, '8bit');
        $iv        = mb_substr($decoded, 10, $ivSize, '8bit');
        $encrypted = mb_substr($decoded, 10 + $ivSize, null, '8bit');

        $calcSignature = mb_substr(hash_hmac('sha256', $encrypted, $key, true), 0, 10, '8bit');
        if (!$this->timingSafeEquals($signature, $calcSignature)) {
            throw new RuntimeException('Signature tidak cocok (SIGNATURE_NOT_MATCH).');
        }

        $decrypted = openssl_decrypt($encrypted, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
        if ($decrypted === false) {
            throw new RuntimeException('Decrypt gagal.');
        }

        return $decrypted;
    }

    protected function timingSafeEquals(string $a, string $b): bool
    {
        if (strlen($a) !== strlen($b)) {
            return false;
        }
        $res = 0;
        for ($i = 0, $len = strlen($a); $i < $len; $i++) {
            $res |= ord($a[$i]) ^ ord($b[$i]);
        }
        return $res === 0;
    }

    // ==== CORE CALLER ====

    protected function callWs(string $method, array $metadata = [], array $data = []): array
    {
        $this->initializeIdrg();

        $payload = [
            'metadata' => array_merge(['method' => $method], $metadata),
            'data'     => $data,
        ];

        // Endpoint + debug mode
        $endpoint = $this->wsUrl;
        if ($this->debugMode) {
            $endpoint .= (str_contains($endpoint, '?') ? '&' : '?') . 'mode=debug';
        }

        if ($this->debugMode) {
            // Kirim JSON polos (tanpa enkripsi)
            $resp   = Http::withHeaders(['Content-Type' => 'application/json'])
                ->timeout(30)
                ->post($endpoint, $payload);
            $parsed = $resp->json() ?? [];
            return $this->assertOkOrThrow($parsed);
        }

        // Mode normal: kirim base64 terenkripsi sebagai body mentah (form-urlencoded)
        $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($json === false) {
            throw new RuntimeException('Gagal encode JSON payload.');
        }

        $encrypted = $this->idrgEncrypt($json);

        $resp = Http::withHeaders(['Content-Type' => 'application/x-www-form-urlencoded'])
            ->timeout(30)
            ->withBody($encrypted, 'application/x-www-form-urlencoded')
            ->post($endpoint);

        $raw = $resp->body();

        // Strip pembungkus BEGIN/END, lalu trim & decrypt
        $clean = preg_replace(
            "/^-+BEGIN ENCRYPTED DATA-+\R?|^-+END ENCRYPTED DATA-+\R?/m",
            '',
            $raw
        );
        $clean = trim($clean);

        $decrypted = $this->idrgDecrypt($clean);
        $parsed    = json_decode($decrypted, true);

        if (!is_array($parsed)) {
            throw new RuntimeException('Response JSON tidak valid.');
        }

        return $this->assertOkOrThrow($parsed);
    }

    /**
     * Pastikan metadata.code == 200; jika tidak, lempar exception dengan error_no bila ada.
     */
    protected function assertOkOrThrow(array $parsed): array
    {
        $code    = $parsed['metadata']['code']    ?? null;
        $message = $parsed['metadata']['message'] ?? null;

        if ((int) $code !== 200) {
            $errNo = $parsed['metadata']['error_no'] ?? null;
            $msg   = $message ?: 'IDRG error';
            if ($errNo !== null && $errNo !== '') {
                $msg .= " ({$errNo})";
            }
            throw new RuntimeException($msg);
        }

        return $parsed;
    }

    // ==== WRAPPERS (sesuaikan dengan katalog method di server kamu) ====

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

    // DITUTUP/DEPRECATED di banyak server – amankan dengan exception
    public function pullClaim(array $metadata = [], array $data = []): array
    {
        throw new RuntimeException('Method pull_claim sudah ditutup oleh server WS.');
    }

    public function getClaimData(array $metadata, array $data): array
    {
        return $this->callWs('get_claim_data', $metadata, $data);
    }

    public function getClaimStatus(array $metadata, array $data): array
    {
        return $this->callWs('get_claim_status', $metadata, $data);
    }

    public function claimPrint(array $metadata, array $data): array
    {
        return $this->callWs('claim_print', $metadata, $data);
    }

    public function deleteClaim(array $metadata, array $data): array
    {
        return $this->callWs('delete_claim', $metadata, $data);
    }

    public function reeditClaim(array $metadata, array $data): array
    {
        return $this->callWs('reedit_claim', $metadata, $data);
    }

    /**
     * generate_claim_number: Dapatkan nomor klaim unik
     */
    public function generateClaimNumber(array $metadata, array $data): array
    {
        return $this->callWs('generate_claim_number', $metadata, $data);
    }

    // ==== Helper opsional: ambil PDF klaim sebagai binary ====

    public function claimPrintPdf(string $nomorSep, array $extraMeta = []): ?string
    {
        $res = $this->claimPrint($extraMeta, ['nomor_sep' => $nomorSep]);
        if (($res['metadata']['code'] ?? 0) === 200 && isset($res['data'])) {
            $bin = base64_decode($res['data'], true);
            return $bin === false ? null : $bin;
        }
        return null;
    }
}
