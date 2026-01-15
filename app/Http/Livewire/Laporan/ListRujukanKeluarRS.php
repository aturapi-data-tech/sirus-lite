<?php

namespace App\Http\Livewire\Laporan;

use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use App\Http\Traits\BPJS\VclaimTrait;


class ListRujukanKeluarRS extends Component
{
    public string $myTitle = 'List Rujukan Keluar RS';
    public string $mySnipt = 'Laporan List Data Rujukan Keluar RS';

    // Data hasil API
    public array $queryData = [];
    public array $rekapByRs = [];
    public int $totalRujukan = 0;

    // Input tanggal (Parameter 1 & 2)
    public string $tglMulai = '';
    public string $tglAkhir = '';

    // Optional: status UI
    public bool $isLoading = false;
    public ?string $errorMessage = null;

    public function mount()
    {
        $now = Carbon::now(config('app.timezone'));
        // Default parameter:
        // tglMulai = awal bulan (DD-MM-YYYY)
        // tglAkhir = hari ini (DD-MM-YYYY)
        $this->tglMulai = $now->copy()->startOfMonth()->format('d-m-Y');
        $this->tglAkhir = $now->format('d-m-Y');
    }

    public function fetchDataRujukanKeluarRS(): void
    {
        $this->isLoading = true;
        $this->errorMessage = null;

        // reset data
        $this->queryData = [];
        $this->rekapByRs = [];
        $this->totalRujukan = 0;

        try {
            // Validasi tanggal input (d-m-Y)
            $this->validate([
                'tglMulai' => 'required|date_format:d-m-Y',
                'tglAkhir' => 'required|date_format:d-m-Y|after_or_equal:tglMulai',
            ], [
                'tglMulai.required' => 'Tgl Mulai wajib diisi.',
                'tglMulai.date_format' => 'Format Tgl Mulai harus DD-MM-YYYY.',
                'tglAkhir.required' => 'Tgl Akhir wajib diisi.',
                'tglAkhir.date_format' => 'Format Tgl Akhir harus DD-MM-YYYY.',
                'tglAkhir.after_or_equal' => 'Tgl Akhir tidak boleh lebih kecil dari Tgl Mulai.',
            ]);

            // Convert input (d-m-Y) -> (Y-m-d)
            $ymdMulai = Carbon::createFromFormat('d-m-Y', $this->tglMulai, config('app.timezone'))->format('Y-m-d');
            $ymdAkhir = Carbon::createFromFormat('d-m-Y', $this->tglAkhir, config('app.timezone'))->format('Y-m-d');

            // Panggil trait
            $response = VclaimTrait::rujukan_keluar_list_rs($ymdMulai, $ymdAkhir)->getOriginalContent();

            if (($response['metadata']['code'] ?? null) != 200) {
                $this->errorMessage = $response['metadata']['message'] ?? 'Gagal memuat data';
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                    ->addError('Error: ' . ($response['metadata']['code'] ?? '-') . ' ' . $this->errorMessage);
                return;
            }

            // Ambil list
            $list = $response['response']['list'] ?? $response['response'] ?? [];
            if (!is_array($list)) $list = [];

            // ==== PROSES: format tgl + sorting desc ====
            $processed = collect($list)
                ->map(function ($row) {
                    $tgl = $row['tglRujukan'] ?? null; // umumnya Y-m-d

                    $row['tglRujukanKey'] = $tgl;
                    $row['tglRujukanDisplay'] = $tgl
                        ? Carbon::parse($tgl)->format('d/m/Y')
                        : '-';

                    // Ambil nama RS tujuan secara fleksibel (coba beberapa kemungkinan field)
                    $row['rsTujuanNama'] =
                        $row['ppkRujukan']['nama'] ??          // umum
                        $row['ppkDirujuk']['nama'] ??          // kemungkinan lain
                        $row['namaPpkDirujuk'] ??              // versi flat (punyamu)
                        $row['namaPPKDirujuk'] ??              // kemungkinan beda kapital
                        'RS Tidak Diketahui';

                    return $row;
                })
                ->sortByDesc(function ($row) {
                    $tgl = $row['tglRujukanKey'] ?? null;
                    return $tgl ? Carbon::parse($tgl)->timestamp : 0;
                })
                ->values()
                ->toArray();

            $this->queryData = $processed;
            $this->totalRujukan = count($processed);

            // ==== REKAP PER RS TUJUAN ====
            $this->rekapByRs = collect($processed)
                ->groupBy(fn($row) => $row['rsTujuanNama'] ?? 'RS Tidak Diketahui')
                ->map(function ($items, $rsNama) {
                    return [
                        'rsNama' => $rsNama,
                        'jumlah' => $items->count(),
                    ];
                })
                ->sortByDesc('jumlah')
                ->values()
                ->toArray();

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addSuccess('OK: ' . ($response['metadata']['message'] ?? 'Berhasil') . ' | Total: ' . $this->totalRujukan);
        } catch (ValidationException $e) {
            // Livewire akan handle error validasi (pesan tampil di blade jika dipasang),
            // cukup toastr dan stop.
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError($e->validator->errors()->first());
            return;
        } catch (\Throwable $e) {
            $this->errorMessage = $e->getMessage();
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Exception: ' . $this->errorMessage);
        } finally {
            $this->isLoading = false;
        }
    }


    public string $selectedNoRujukan = '';
    public array $detailRujukan = [];

    public function pilihRujukan(string $noRujukan): void
    {
        if (empty($noRujukan)) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('No Rujukan tidak valid.');
            return;
        }

        $this->selectedNoRujukan = $noRujukan;
        $this->detailRujukan = [];

        try {
            $resp = VclaimTrait::rujukan_keluar_detail_by_no_rujukan($noRujukan)->getOriginalContent();
            // NOTE: BPJS pakai metaData (bukan metadata)
            $code = (string)($resp['metadata']['code'] ?? '');
            if ($code !== '200') {
                $msg = $resp['metaData']['message'] ?? 'Gagal mengambil detail rujukan';
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                    ->addError("Error: {$code} {$msg}");
                return;
            }

            // Data detail ada di response.rujukan
            $rujukan = $resp['response']['rujukan'] ?? null;
            if (!is_array($rujukan) || empty($rujukan)) {
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                    ->addError('Detail rujukan tidak ditemukan.');
                return;
            }

            // Normalisasi: tambah field display date (dd/mm/yyyy)
            $tglRujukan = $rujukan['tglRujukan'] ?? null; // Y-m-d
            $tglSep     = $rujukan['tglSep'] ?? null;     // Y-m-d
            $tglLahir   = $rujukan['tglLahir'] ?? null;   // Y-m-d

            $this->detailRujukan = [
                // identitas utama
                'noRujukan' => $rujukan['noRujukan'] ?? $noRujukan,
                'noSep'     => $rujukan['noSep'] ?? '',
                'noKartu'   => $rujukan['noKartu'] ?? '',
                'nama'      => $rujukan['nama'] ?? '',
                'kelamin'   => $rujukan['kelamin'] ?? '',
                'kelasRawat' => $rujukan['kelasRawat'] ?? '',

                // tanggal (raw + display)
                'tglRujukan'        => $tglRujukan ?? '',
                'tglRujukanDisplay' => $tglRujukan ? Carbon::createFromFormat('Y-m-d', $tglRujukan)->format('d/m/Y') : '-',
                'tglSep'            => $tglSep ?? '',
                'tglSepDisplay'     => $tglSep ? Carbon::createFromFormat('Y-m-d', $tglSep)->format('d/m/Y') : '-',
                'tglLahir'          => $tglLahir ?? '',
                'tglLahirDisplay'   => $tglLahir ? Carbon::createFromFormat('Y-m-d', $tglLahir)->format('d/m/Y') : '-',

                // RS tujuan
                'ppkDirujuk'      => $rujukan['ppkDirujuk'] ?? '',
                'namaPpkDirujuk'  => $rujukan['namaPpkDirujuk'] ?? '',

                // poli/diagnosa/tipe
                'poliRujukan'      => $rujukan['poliRujukan'] ?? '',
                'namaPoliRujukan'  => $rujukan['namaPoliRujukan'] ?? '',
                'diagRujukan'      => $rujukan['diagRujukan'] ?? '',
                'namaDiagRujukan'  => $rujukan['namaDiagRujukan'] ?? '',
                'tipeRujukan'      => $rujukan['tipeRujukan'] ?? '',
                'namaTipeRujukan'  => $rujukan['namaTipeRujukan'] ?? '',

                // lainnya
                'jnsPelayanan'     => $rujukan['jnsPelayanan'] ?? '',
                'catatan'          => $rujukan['catatan'] ?? '',
                'tglRencanaKunjungan' => $rujukan['tglRencanaKunjungan'] ?? '',
            ];

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addSuccess("Rujukan dipilih: {$this->detailRujukan['noRujukan']}");

            // Optional: emit event untuk buka modal detail
            // $this->emit('openModalDetailRujukan');

        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Exception: ' . $e->getMessage());
        }
    }


    public function render()
    {
        return view('livewire.laporan.list-rujukan-keluar-r-s');
    }
}
