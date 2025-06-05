<?php

namespace App\Http\Livewire\MyAdmin\MountingControl;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Exception\ProcessFailedException;

use Livewire\Component;


class MountingControl extends Component
{

    public function mount()
    {
        $this->checkMounted();
    }

    public $shareServer = '//172.8.8.12/rad_path/';
    public $mountPoint  = '/opt/lampp/htdocs/sirus-lite/storage/penunjang/rad';
    // Pesan status yang akan ditampilkan di view
    public $statusMessage = '';
    // Boolean: apakah folder sudah ter-mount?
    public $isMounted = 1;
    public function mountShare()
    {
        $cmd = [
            'sudo',
            '/usr/bin/mount',
            '-t',
            'cifs',
            $this->shareServer,
            $this->mountPoint,
        ];

        $process = new Process($cmd);

        try {
            $process->run();

            if (! $process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            $this->statusMessage = "✓ Mount berhasil di: {$this->mountPoint}";
        } catch (ProcessFailedException $e) {
            $errorOutput = $process->getErrorOutput();
            $this->statusMessage = "✗ Mount gagal: " . trim($errorOutput);
        }

        // Setelah selesai, cek status mount kembali
        $this->checkMounted();
    }

    public function unmountShare()
    {
        $cmd = [
            'sudo',
            '/usr/bin/umount',
            $this->mountPoint,

        ];

        $process = new Process($cmd);

        try {
            $process->run();

            if (! $process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            $this->statusMessage = "✓ Unmount berhasil dari: {$this->mountPoint}";
        } catch (ProcessFailedException $e) {
            $errorOutput = $process->getErrorOutput();
            $this->statusMessage = "✗ Unmount gagal: " . trim($errorOutput);
        }

        // Cek status lagi setelah unmount
        $this->checkMounted();
    }


    /**
     * Method untuk mengecek status mount.
     * Menggunakan perintah 'mountpoint -q'.
     * Jika exitCode = 0 → sudah ter-mount; jika != 0 → belum ter-mount.
     */
    public function checkMounted()
    {
        $cmd = [
            'sudo',
            '/usr/bin/mountpoint',
            '-q',
            $this->mountPoint
        ];

        try {
            $process = new Process($cmd);

            // Set batas waktu maksimal 2 detik
            $process->setTimeout(2);

            // (Opsional) Set juga batas waktu idle, misalnya 3 detik
            // artinya jika tidak ada output baru selama 3 detik, proses juga dibatalkan
            // $process->setIdleTimeout(3);

            // Jalankan proses (harus setelah setTimeout)
            $process->run();

            // Kalau selesai sebelum timeout, periksa exit code
            if ($process->getExitCode() === 0) {
                $this->isMounted = 1;
            } else {
                $this->isMounted = 0;
            }
        }
        // Jika melebihi 3 detik, Symfony akan lempar exception ini
        catch (ProcessTimedOutException $e) {
            // Anggap belum ter‐mount (timeout)
            $this->isMounted = 0;
            // (Opsional) Kamu bisa simpan log atau beri pesan:
            // \Log::warning("checkMounted timeout: " . $e->getMessage());
        }
        // Kalau perintah dijalankan tapi exit code tidak 0, bisa ditangani di sini
        catch (ProcessFailedException $e) {
            $this->isMounted = 0;
            // \Log::warning("checkMounted gagal: " . $e->getMessage());
        }
        // Tangkap exception umum (misalnya Process tidak bisa dijalankan sama sekali)
        catch (\Throwable $e) {
            $this->isMounted = 0;
            // \Log::error("checkMounted exception: " . $e->getMessage());
        }
    }

    public function render()
    {
        return view(
            'livewire.my-admin.mounting-control.mounting-control',
            ['x' => 'x']
        );
    }
}
