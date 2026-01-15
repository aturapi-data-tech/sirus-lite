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
    public array $detailRows = [];      // kumpulan detail dari semua rujukan
    public array $rekapByRs = [];       // sudah ada (rekap RS dari list / atau dari detail)
    public array $rekapByPoli = [];
    public array $rekapByDiagnosa = [];


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

    private function fetchDetailRujukan(string $noRujukan): ?array
    {
        $resp = VclaimTrait::rujukan_keluar_detail_by_no_rujukan($noRujukan)->getOriginalContent();

        // BPJS: metaData
        $code = (string)($resp['metadata']['code'] ?? '');
        if ($code !== '200') {
            return null; // gagal ambil detail
        }

        $rujukan = $resp['response']['rujukan'] ?? null;
        if (!is_array($rujukan) || empty($rujukan)) {
            return null;
        }

        // Normalisasi field yang akan dipakai untuk rekap
        $tglRujukan = $rujukan['tglRujukan'] ?? null;

        return [
            'noRujukan' => $rujukan['noRujukan'] ?? $noRujukan,

            // RS tujuan
            'ppkDirujuk'     => $rujukan['ppkDirujuk'] ?? '',
            'namaPpkDirujuk' => $rujukan['namaPpkDirujuk'] ?? 'RS Tidak Diketahui',

            // Poli
            'poliRujukan'     => $rujukan['poliRujukan'] ?? '',
            'namaPoliRujukan' => $rujukan['namaPoliRujukan'] ?? 'Poli Tidak Diketahui',

            // Diagnosa
            'diagRujukan'      => $rujukan['diagRujukan'] ?? '',
            'namaDiagRujukan'  => $rujukan['namaDiagRujukan'] ?? 'Diagnosa Tidak Diketahui',

            // Tanggal (kalau mau dipakai sorting/rekap tanggal)
            'tglRujukan'        => $tglRujukan ?? '',
            'tglRujukanDisplay' => $tglRujukan
                ? Carbon::createFromFormat('Y-m-d', $tglRujukan)->format('d/m/Y')
                : '-',

            // tambahan opsional
            'jnsPelayanan' => $rujukan['jnsPelayanan'] ?? '',
            'tipeRujukan'  => $rujukan['tipeRujukan'] ?? '',
            'nama'         => $rujukan['nama'] ?? '',
            'noKartu'      => $rujukan['noKartu'] ?? '',
            'noSep'        => $rujukan['noSep'] ?? '',
        ];
    }


    public function fetchDataRujukanKeluarRS(): void
    {
        $this->isLoading = true;
        $this->errorMessage = null;

        // reset data
        $this->queryData = [];
        $this->detailRows = [];
        $this->rekapByRs = [];
        $this->rekapByPoli = [];
        $this->rekapByDiagnosa = [];
        $this->totalRujukan = 0;

        try {
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

            $ymdMulai = Carbon::createFromFormat('d-m-Y', $this->tglMulai, config('app.timezone'))->format('Y-m-d');
            $ymdAkhir = Carbon::createFromFormat('d-m-Y', $this->tglAkhir, config('app.timezone'))->format('Y-m-d');

            $response = VclaimTrait::rujukan_keluar_list_rs($ymdMulai, $ymdAkhir)->getOriginalContent();

            if ((int)($response['metadata']['code'] ?? 0) !== 200) {
                $this->errorMessage = $response['metadata']['message'] ?? 'Gagal memuat data';
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                    ->addError('Error: ' . ($response['metadata']['code'] ?? '-') . ' ' . $this->errorMessage);
                return;
            }

            $list = $response['response']['list'] ?? $response['response'] ?? [];
            if (!is_array($list)) $list = [];

            // ===== 1) PROSES LIST (format tgl + rs tujuan untuk list view) =====
            $processed = collect($list)
                ->map(function ($row) {
                    $tgl = $row['tglRujukan'] ?? null;

                    $row['tglRujukanKey'] = $tgl;
                    $row['tglRujukanDisplay'] = $tgl ? Carbon::parse($tgl)->format('d/m/Y') : '-';

                    $row['rsTujuanNama'] =
                        $row['ppkRujukan']['nama'] ??
                        $row['ppkDirujuk']['nama'] ??
                        $row['namaPpkDirujuk'] ??
                        $row['namaPPKDirujuk'] ??
                        'RS Tidak Diketahui';

                    return $row;
                })
                ->sortByDesc(fn($row) => !empty($row['tglRujukanKey']) ? Carbon::parse($row['tglRujukanKey'])->timestamp : 0)
                ->values()
                ->toArray();

            $this->queryData = $processed;
            $this->totalRujukan = count($processed);

            // ===== 2) KUMPULKAN DETAIL UNTUK SEMUA NO RUJUKAN =====
            // Sesuaikan key no rujukan sesuai data list kamu:
            // contoh: $row['noRujukan'] atau $row['noRujukanKeluar'] atau $row['noRujukan'] dsb.
            $noRujukanList = collect($processed)
                ->map(fn($row) => (string)($row['noRujukan'] ?? $row['no_rujukan'] ?? ''))
                ->filter()
                ->unique()
                ->values();

            $details = [];
            $gagal = 0;

            foreach ($noRujukanList as $noRujukan) {
                $d = $this->fetchDetailRujukan($noRujukan);
                if ($d) {
                    $details[] = $d;
                } else {
                    $gagal++;
                }
            }

            // optional sorting detail by tgl desc
            $this->detailRows = collect($details)
                ->sortByDesc(fn($r) => !empty($r['tglRujukan']) ? Carbon::parse($r['tglRujukan'])->timestamp : 0)
                ->values()
                ->toArray();

            // ===== 3) REKAP DARI DETAIL =====
            $this->rekapByRs = collect($this->detailRows)
                ->groupBy(fn($r) => $r['namaPpkDirujuk'] ?: 'RS Tidak Diketahui')
                ->map(fn($items, $key) => ['rsNama' => $key, 'jumlah' => $items->count()])
                ->sortByDesc('jumlah')
                ->values()
                ->toArray();

            $this->rekapByPoli = collect($this->detailRows)
                ->groupBy(fn($r) => $r['namaPoliRujukan'] ?: 'Poli Tidak Diketahui')
                ->map(fn($items, $key) => ['poliNama' => $key, 'jumlah' => $items->count()])
                ->sortByDesc('jumlah')
                ->values()
                ->toArray();

            $this->rekapByDiagnosa = collect($this->detailRows)
                ->groupBy(fn($r) => $r['namaDiagRujukan'] ?: 'Diagnosa Tidak Diketahui')
                ->map(fn($items, $key) => ['diagNama' => $key, 'jumlah' => $items->count()])
                ->sortByDesc('jumlah')
                ->values()
                ->toArray();

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addSuccess("OK: {$this->totalRujukan} list | Detail terkumpul: " . count($this->detailRows) . " | Gagal detail: {$gagal}");
        } catch (ValidationException $e) {
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
            $detaiRujukan = $this->fetchDetailRujukan($noRujukan);
            if (!$detaiRujukan) {
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                    ->addError('Gagal mengambil detail rujukan.');
                return;
            }

            // Kalau kamu mau detailRujukan format lengkap seperti sebelumnya,
            // tinggal mapping ulang dari response asli. Atau pakai $detaiRujukan untuk view ringkas.
            $this->detailRujukan = $detaiRujukan;

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addSuccess("Rujukan dipilih: {$this->detailRujukan['noRujukan']}");
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
