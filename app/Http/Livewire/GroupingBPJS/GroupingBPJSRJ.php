<?php

namespace App\Http\Livewire\GroupingBPJS;


use Illuminate\Support\Facades\DB;
use App\Http\Traits\EmrRJ\EmrRJTrait;
use App\Http\Traits\EmrUGD\EmrUGDTrait;


use Livewire\Component;
use Illuminate\Support\Collection;

use Livewire\WithFileUploads;
// use PhpOffice\PhpSpreadsheet\IOFactory;
use Livewire\WithPagination;
use Carbon\Carbon;

use Spatie\PdfToText\Pdf;

class GroupingBPJSRJ extends Component
{
    use WithPagination, WithFileUploads, EmrRJTrait, EmrUGDTrait;

    // primitive Variable
    public string $myTitle = 'FIle Grouping BPJS Rawat Jalan';
    public string $mySnipt = 'Grouping BPJS Rawat Jalan';
    public string $myProgram = 'Grouping BPJS Rawat Jalan';

    public array $myLimitPerPages = [10, 50, 100, 150, 200, 1000];
    // limit record per page -resetExcept////////////////
    public int $limitPerPage = 10;



    // my Top Bar
    public array $myTopBar = ['refDate' => ''];
    public array $myQueryDataSum = [];
    public array $klaimTidakDisetujui = [];

    public string $refFilter = '';
    // search logic -resetExcept////////////////
    protected $queryRJString = [
        'refFilter' => ['except' => '', 'as' => 'cariData'],
        'page' => ['except' => 1, 'as' => 'p'],
    ];

    // reset page when myTopBar Change
    public function updatedReffilter()
    {
        $this->resetPage();
    }

    public function updatedMyTopBarRefDate()
    {
        $this->hitungTotallAll($this->myTopBar['refDate']);
    }

    //////////////////
    // upload xls laravel
    /////////////////////
    // public $file;
    // public $dataUmbalBPJS = [];

    // public function updatedFile()
    // {
    //     $this->readXlsx();
    // }

    // public function readXlsx()
    // {
    //     $path = $this->file->getRealPath();
    //     $spreadsheet = IOFactory::load($path);
    //     $sheet = $spreadsheet->getActiveSheet();
    //     $data = $sheet->toArray();

    //     // Buang header jika perlu
    //     $this->dataUmbalBPJS = array_slice($data, 1); // Mulai dari baris ke-2
    // }



    ///////////////////
    // upload pdf laravel
    ////////////////////
    public $file;
    public $dataUmbalBPJSTidakAdaDiRS = [];


    public function readPdfUmbalBPJS()
    {

        $this->validate([
            'file' => 'required|file|mimes:pdf|max:10240',
        ]);

        $path = $this->file->getRealPath();
        $text = Pdf::getText($path, '/usr/local/bin/pdftotext-wrapper');

        // Normalisasi teks
        $text = str_replace("\xC2\xA0", ' ', $text);
        $text = preg_replace('/[ ]{2,}/', ' ', $text);
        $lines = array_values(array_filter(array_map('trim', explode("\n", $text))));

        // Hapus baris header
        $blacklistExact = [
            'RINCIAN DATA HASIL VERIFIKASI',
            'Nama RS',
            'Tingkat Pelayanan',
            'Bulan Pelayanan',
            'Tgl. Verifikasi',
            'Riil RS',
            'RESUME',
            'Menyetujui',
            'Mengetahui',
            'Direktur RS',
            'BPJS KESEHATAN',
        ];
        $filtered = array_filter($lines, function ($line) use ($blacklistExact) {
            $line = trim(str_replace("\f", '', $line));
            // Buang jika cocok persis
            if (in_array($line, $blacklistExact, true)) return false;

            // Buang jika cocok regex (format)
            return !preg_match('/^(
                Hal\.\d+\/\d+ |        # Halaman
                :.* |                  # Baris yang diawali :
                RJTL |                 # Baris RJTL
                No$ |                  # Baris No saja
                No\.SEP |              # Baris No.SEP
                Tgl\. Verifikasi |     # Baris Tgl. Verifikasi
                Biaya$ |               # Baris Biaya
                Diajukan |             # Baris Diajukan
                Disetujui |            # Baris Disetujui
                TOTAL.* |              # TOTAL Bea
                Total Bea\.Diajukan.* |
                Total Bea\.Disetujui.* |
                dr\.\s?[A-Za-z\s\.]+ | # Nama dokter
                0184R006\s*-\s*.*      # Baris nama faskes
            )$/ix', $line);
        });
        $lines = array_values(array_filter($filtered)); // Reset index
        //reset linesCleaned
        $linesCleaned = [];
        $buffer = [];
        //reset dataUmbalBPJSTidakAdaDiRS
        $this->dataUmbalBPJSTidakAdaDiRS = [];

        foreach ($lines as $line) {
            $line = preg_replace('/\s+/u', ' ', trim($line)); // Normalisasi whitespaceP4sswordku]
            $parts = explode(' ', $line);

            if (count($parts) === 2 && is_numeric($parts[0]) && str_starts_with($parts[1], '0184R006')) {
                $linesCleaned[] = $parts[0]; // nomor urut
                $linesCleaned[] = $parts[1]; // kode SEP
            } else {
                $linesCleaned[] = $line; // fallback
            }
        }

        $lines = array_values($linesCleaned);
        $chunks = array_chunk($lines, 6);

        $myRefdate = $this->myTopBar['refDate'];

        $dataRjLookup = DB::table('rsview_rjkasir')
            ->select('vno_sep', 'rj_no')
            ->where(DB::raw("nvl(rj_status,'A')"), '=', 'L')
            ->whereIn('klaim_id', function ($klaimData) {
                $klaimData->select('klaim_id')
                    ->from('rsmst_klaimtypes')
                    ->where('klaim_status', 'BPJS');
            })
            ->where(DB::raw("to_char(rj_date,'mm/yyyy')"), '=', $myRefdate)
            ->pluck('rj_no', 'vno_sep') // hasil: ['0184R006xxxx' => 'RJ2025xxxx']
            ->toArray();



        $dataUgdLookup = DB::table('rsview_ugdkasir')
            ->select('vno_sep', 'rj_no')
            ->where(DB::raw("nvl(rj_status,'A')"), '=', 'L')
            ->whereIn('klaim_id', function ($klaimData) {
                $klaimData->select('klaim_id')
                    ->from('rsmst_klaimtypes')
                    ->where('klaim_status', 'BPJS');
            })
            ->where(DB::raw("to_char(rj_date,'mm/yyyy')"), '=', $myRefdate)
            ->pluck('rj_no', 'vno_sep') // hasil: ['0184R006xxxx' => 'RJ2025xxxx']
            ->toArray();



        // LOG NOT IN RS
        // $allNoSepRS = collect($dataRjLookup)->keys()
        //     ->merge(collect($dataUgdLookup)->keys())
        //     ->unique()
        //     ->values();


        // $notFoundInRS = collect($chunks)
        //     ->filter(function ($chunk) {
        //         // validasi format baris
        //         return count($chunk) === 6;
        //     })
        //     ->map(function ($chunk) {
        //         return [
        //             'no_sep'    => $chunk[1],
        //             'tgl_verif' => $chunk[2],
        //             'riil_rs'   => $chunk[3],
        //             'diajukan'  => $chunk[4],
        //             'disetujui' => $chunk[5],
        //         ];
        //     })
        //     ->whereNotIn('no_sep', $allNoSepRS)
        //     ->values();
        // dd($notFoundInRS);


        // $duplicateNoSep = $allNoSepRS->duplicates();

        // // Tampilkan jika ada duplikat
        // if ($duplicateNoSep->isNotEmpty()) {
        //     dd($duplicateNoSep); // akan menampilkan key => value dari duplikat
        // } else {
        //     dd('No SEP RS tidak ada yang duplikat');
        // }



        foreach ($chunks as $chunk) {

            if (count($chunk) === 6 && preg_match('/^\d{4}-\d{2}-\d{2}$/', $chunk[2])) {
                $myRefdate = $this->myTopBar['refDate'];

                $no_sep = $chunk[1];
                $umbal = [
                    'no_sep'    => $no_sep,
                    'tgl_verif' => $chunk[2],
                    'riil_rs'   => (int) str_replace(',', '', $chunk[3]),
                    'diajukan'  => (int) str_replace(',', '', $chunk[4]),
                    'disetujui' => (int) str_replace(',', '', $chunk[5]),
                ];

                if (!empty($dataRjLookup[$no_sep])) {
                    $rj_no = $dataRjLookup[$no_sep];

                    $findDataRJ = $this->findDataRJ($rj_no);

                    $dataDaftarPoliRJ = $findDataRJ['dataDaftarRJ'];

                    $dataDaftarPoliRJ['umbalBpjs'] = $umbal;

                    $this->updateJsonRJ($rj_no, $dataDaftarPoliRJ);
                } elseif (!empty($dataUgdLookup[$no_sep])) {
                    $rj_no = $dataUgdLookup[$no_sep];

                    $findDataUGD = $this->findDataUGD($rj_no);

                    $dataDaftarPoliUGD = $findDataUGD;

                    $dataDaftarPoliUGD['umbalBpjs'] = $umbal;

                    $this->updateJsonUGD($rj_no, $dataDaftarPoliUGD);
                } else {
                    $this->dataUmbalBPJSTidakAdaDiRS[] = $umbal;
                }
            } else {
                $umbal = [
                    'no_sep'    => $chunk[1] ?? '',
                    'tgl_verif' => $chunk[2] ?? '',
                    'riil_rs'   =>  $chunk[3] ?? '',
                    'diajukan'  =>  $chunk[4] ?? '',
                    'disetujui' =>  $chunk[5] ?? '',
                ];
                $this->dataUmbalBPJSTidakAdaDiRS[] = $umbal;
            }
        }
        // 3. Bersihkan input & error
        $this->reset(['file']);          // kosongkan input–nya
        $this->resetValidation();      // hapus semua pesan error
        $this->hitungTotallAll();
    }


    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////

    // setter myTopBar Shift and myTopBar refDate
    private function setterTopBarrefDate(): void
    {
        // dd/mm/yyyy hh24:mi:ss
        $this->myTopBar['refDate'] = Carbon::now(env('APP_TIMEZONE'))->format('m/Y');
    }

    private function buatTarifRawatJalan(object $row): array
    {
        return [
            'Admin UP'           => $row->admin_up            ?? 0,
            'Jasa Karyawan'      => $row->jasa_karyawan       ?? 0,
            'Jasa Dokter'        => $row->jasa_dokter         ?? 0,
            'Jasa Medis'         => $row->jasa_medis          ?? 0,
            'Admin RJ'           => $row->admin_rawat_jalan   ?? 0,
            'Radiologi'          => $row->radiologi           ?? 0,
            'Laboratorium'       => $row->laboratorium        ?? 0,
            'Obat'               => $row->obat                ?? 0,
            'Lain-lain'          => $row->lain_lain           ?? 0,
        ];
    }

    private function buatTarifUGD(object $row): array
    {
        return [
            'Admin UGD'       => $row->admin_ugd      ?? 0,
            'Admin UP'        => $row->admin_up       ?? 0,
            'Jasa Karyawan'   => $row->jasa_karyawan  ?? 0,
            'Jasa Dokter'     => $row->jasa_dokter    ?? 0,
            'Jasa Medis'      => $row->jasa_medis     ?? 0,
            'Radiologi'       => $row->radiologi      ?? 0,
            'Laboratorium'    => $row->laboratorium   ?? 0,
            'Obat'            => $row->obat           ?? 0,
            'Lain-lain'       => $row->lain_lain      ?? 0,
            'Trf RJ'          => $row->trf_rj         ?? 0,
        ];
    }

    private function perhitunganTarifRawatJalanUGD(\Illuminate\Pagination\LengthAwarePaginator $paginator)
    {
        // get raw collection & IDs
        $collection = $paginator->getCollection();
        $ids        = $collection->pluck('rj_no')->all();

        // 2. Hitung semua subtotal Rawat Jalan
        $totalsRJ = DB::table('rsview_rjstrs')
            ->select(
                'rj_no',
                // 'h.datadaftarpolirj_json',
                DB::raw("SUM(CASE WHEN txn_id = 'ADMIN UP'          THEN txn_nominal ELSE 0 END) as admin_up"),
                DB::raw("SUM(CASE WHEN txn_id = 'JASA KARYAWAN'     THEN txn_nominal ELSE 0 END) as jasa_karyawan"),
                DB::raw("SUM(CASE WHEN txn_id = 'JASA DOKTER'       THEN txn_nominal ELSE 0 END) as jasa_dokter"),
                DB::raw("SUM(CASE WHEN txn_id = 'JASA MEDIS'        THEN txn_nominal ELSE 0 END) as jasa_medis"),
                DB::raw("SUM(CASE WHEN txn_id = 'ADMIN RAWAT JALAN' THEN txn_nominal ELSE 0 END) as admin_rawat_jalan"),
                DB::raw("SUM(CASE WHEN txn_id = 'LAIN-LAIN'         THEN txn_nominal ELSE 0 END) as lain_lain"),
                DB::raw("SUM(CASE WHEN txn_id = 'RADIOLOGI'         THEN txn_nominal ELSE 0 END) as radiologi"),
                DB::raw("SUM(CASE WHEN txn_id = 'LABORAT'           THEN txn_nominal ELSE 0 END) as laboratorium"),
                DB::raw("SUM(CASE WHEN txn_id = 'OBAT'              THEN txn_nominal ELSE 0 END) as obat")
            )
            ->whereIn('rj_no', $ids)
            ->groupBy('rj_no')
            // ->groupBy('h.datadaftarpolirj_json')
            ->get()
            ->keyBy('rj_no');
        // 3. Hitung semua subtotal UGD
        $totalsUGD = DB::table('rsview_ugdstrs')
            ->select(
                'rj_no',
                // 'h.datadaftarugd_json',
                DB::raw("SUM(CASE WHEN txn_id = 'ADMIN UGD'       THEN txn_nominal ELSE 0 END) as admin_ugd"),
                DB::raw("SUM(CASE WHEN txn_id = 'ADMIN UP'        THEN txn_nominal ELSE 0 END) as admin_up"),
                DB::raw("SUM(CASE WHEN txn_id = 'JASA KARYAWAN'   THEN txn_nominal ELSE 0 END) as jasa_karyawan"),
                DB::raw("SUM(CASE WHEN txn_id = 'JASA DOKTER'     THEN txn_nominal ELSE 0 END) as jasa_dokter"),
                DB::raw("SUM(CASE WHEN txn_id = 'JASA MEDIS'      THEN txn_nominal ELSE 0 END) as jasa_medis"),
                DB::raw("SUM(CASE WHEN txn_id = 'RADIOLOGI'       THEN txn_nominal ELSE 0 END) as radiologi"),
                DB::raw("SUM(CASE WHEN txn_id = 'LABORAT'         THEN txn_nominal ELSE 0 END) as laboratorium"),
                DB::raw("SUM(CASE WHEN txn_id = 'OBAT'            THEN txn_nominal ELSE 0 END) as obat"),
                DB::raw("SUM(CASE WHEN txn_id = 'LAIN-LAIN'       THEN txn_nominal ELSE 0 END) as lain_lain"),
                DB::raw("SUM(CASE WHEN txn_id = 'TRF RJ'          THEN txn_nominal ELSE 0 END) as trf_rj")
            )
            ->whereIn('rj_no', $ids)
            ->groupBy('rj_no')
            // ->groupBy('h.datadaftarugd_json')
            ->get()
            ->keyBy('rj_no');


        $jsonRJ = DB::table('rsview_rjkasir')
            ->whereIn('rj_no', $ids)
            ->pluck('datadaftarpolirj_json', 'rj_no');   // [ rj_no => json ]

        $jsonUGD = DB::table('rsview_ugdkasir')
            ->whereIn('rj_no', $ids)
            ->pluck('datadaftarugd_json', 'rj_no');

        // 2) Hitung subtotal seperti sebelumnya (tanpa JSON)



        // 4. Map tiap baris: kalau poli_id='UGD' pakai UGD, selain itu RJ
        $new = $collection->map(function ($row) use ($totalsRJ, $totalsUGD, $jsonRJ, $jsonUGD) {
            $key = $row->rj_no;
            $totalsRJ = $totalsRJ->keyBy('rj_no');
            $totalsUGD = $totalsUGD->keyBy('rj_no');

            if (strtoupper($row->poli_desc) == 'UGD') {
                // ambil dari UGD
                $t = $totalsUGD->get($key, (object)[]);
                $row->datadaftarugd_json = $jsonUGD[$key] ?? null;
                $row->admin_ugd        = $t->admin_ugd      ?? 0;
                $row->admin_up         = $t->admin_up       ?? 0;
                $row->jasa_karyawan    = $t->jasa_karyawan  ?? 0;
                $row->jasa_dokter      = $t->jasa_dokter    ?? 0;
                $row->jasa_medis       = $t->jasa_medis     ?? 0;
                $row->radiologi        = $t->radiologi      ?? 0;
                $row->laboratorium     = $t->laboratorium   ?? 0;
                $row->obat             = $t->obat           ?? 0;
                $row->lain_lain        = $t->lain_lain      ?? 0;
                $row->trf_rj           = $t->trf_rj         ?? 0;
                // kalkulasi tarif UGD
                $row->tarif_detail = $this->buatTarifUGD($row);
            } else {
                // ambil dari RJ
                $t = $totalsRJ->get($key, (object)[]);
                $row->datadaftarpolirj_json = $jsonRJ[$key] ?? null;
                $row->admin_up           = $t->admin_up            ?? 0;
                $row->jasa_karyawan      = $t->jasa_karyawan       ?? 0;
                $row->jasa_dokter        = $t->jasa_dokter         ?? 0;
                $row->jasa_medis         = $t->jasa_medis          ?? 0;
                $row->admin_rawat_jalan  = $t->admin_rawat_jalan   ?? 0;
                $row->lain_lain          = $t->lain_lain           ?? 0;
                $row->radiologi          = $t->radiologi           ?? 0;
                $row->laboratorium       = $t->laboratorium        ?? 0;
                $row->obat               = $t->obat                ?? 0;

                // kalkulasi tarif Rawat Jalan
                $row->tarif_detail = $this->buatTarifRawatJalan($row);
            }

            // 5. Hitung total semua komponen
            $row->tarif_total = array_sum($row->tarif_detail);
            return $row;
        });


        $paginator->setCollection($new);

        return $paginator;
    }

    private function perhitunganTarifRawatJalanUGDAll(Collection $collection): Collection
    {

        $ids        = $collection->pluck('rj_no')->all();

        // 2. Hitung semua subtotal Rawat Jalan
        $totalsRJ = DB::table('rsview_rjstrs')
            ->select(
                'rj_no',
                // 'h.datadaftarpolirj_json',
                DB::raw("SUM(CASE WHEN txn_id = 'ADMIN UP'          THEN txn_nominal ELSE 0 END) as admin_up"),
                DB::raw("SUM(CASE WHEN txn_id = 'JASA KARYAWAN'     THEN txn_nominal ELSE 0 END) as jasa_karyawan"),
                DB::raw("SUM(CASE WHEN txn_id = 'JASA DOKTER'       THEN txn_nominal ELSE 0 END) as jasa_dokter"),
                DB::raw("SUM(CASE WHEN txn_id = 'JASA MEDIS'        THEN txn_nominal ELSE 0 END) as jasa_medis"),
                DB::raw("SUM(CASE WHEN txn_id = 'ADMIN RAWAT JALAN' THEN txn_nominal ELSE 0 END) as admin_rawat_jalan"),
                DB::raw("SUM(CASE WHEN txn_id = 'LAIN-LAIN'         THEN txn_nominal ELSE 0 END) as lain_lain"),
                DB::raw("SUM(CASE WHEN txn_id = 'RADIOLOGI'         THEN txn_nominal ELSE 0 END) as radiologi"),
                DB::raw("SUM(CASE WHEN txn_id = 'LABORAT'           THEN txn_nominal ELSE 0 END) as laboratorium"),
                DB::raw("SUM(CASE WHEN txn_id = 'OBAT'              THEN txn_nominal ELSE 0 END) as obat")
            )
            ->whereIn('rj_no', $ids)
            ->groupBy('rj_no')
            // ->groupBy('h.datadaftarpolirj_json')
            ->get()
            ->keyBy('rj_no');
        // 3. Hitung semua subtotal UGD
        $totalsUGD = DB::table('rsview_ugdstrs')
            ->select(
                'rj_no',
                // 'h.datadaftarugd_json',
                DB::raw("SUM(CASE WHEN txn_id = 'ADMIN UGD'       THEN txn_nominal ELSE 0 END) as admin_ugd"),
                DB::raw("SUM(CASE WHEN txn_id = 'ADMIN UP'        THEN txn_nominal ELSE 0 END) as admin_up"),
                DB::raw("SUM(CASE WHEN txn_id = 'JASA KARYAWAN'   THEN txn_nominal ELSE 0 END) as jasa_karyawan"),
                DB::raw("SUM(CASE WHEN txn_id = 'JASA DOKTER'     THEN txn_nominal ELSE 0 END) as jasa_dokter"),
                DB::raw("SUM(CASE WHEN txn_id = 'JASA MEDIS'      THEN txn_nominal ELSE 0 END) as jasa_medis"),
                DB::raw("SUM(CASE WHEN txn_id = 'RADIOLOGI'       THEN txn_nominal ELSE 0 END) as radiologi"),
                DB::raw("SUM(CASE WHEN txn_id = 'LABORAT'         THEN txn_nominal ELSE 0 END) as laboratorium"),
                DB::raw("SUM(CASE WHEN txn_id = 'OBAT'            THEN txn_nominal ELSE 0 END) as obat"),
                DB::raw("SUM(CASE WHEN txn_id = 'LAIN-LAIN'       THEN txn_nominal ELSE 0 END) as lain_lain"),
                DB::raw("SUM(CASE WHEN txn_id = 'TRF RJ'          THEN txn_nominal ELSE 0 END) as trf_rj")
            )
            ->whereIn('rj_no', $ids)
            ->groupBy('rj_no')
            // ->groupBy('h.datadaftarugd_json')
            ->get()
            ->keyBy('rj_no');


        $jsonRJ = DB::table('rsview_rjkasir')
            ->whereIn('rj_no', $ids)
            ->pluck('datadaftarpolirj_json', 'rj_no');   // [ rj_no => json ]

        $jsonUGD = DB::table('rsview_ugdkasir')
            ->whereIn('rj_no', $ids)
            ->pluck('datadaftarugd_json', 'rj_no');

        $new = $collection->map(function ($row) use ($totalsRJ, $totalsUGD, $jsonRJ, $jsonUGD) {
            $key = $row->rj_no;
            $totalsRJ = $totalsRJ->keyBy('rj_no');
            $totalsUGD = $totalsUGD->keyBy('rj_no');
            if (strtoupper($row->poli_desc) == 'UGD') {
                // ambil dari UGD
                $t = $totalsUGD->get($key, (object)[]);
                $row->datadaftarugd_json = $jsonUGD[$key] ?? null;
                $row->admin_ugd        = $t->admin_ugd      ?? 0;
                $row->admin_up         = $t->admin_up       ?? 0;
                $row->jasa_karyawan    = $t->jasa_karyawan  ?? 0;
                $row->jasa_dokter      = $t->jasa_dokter    ?? 0;
                $row->jasa_medis       = $t->jasa_medis     ?? 0;
                $row->radiologi        = $t->radiologi      ?? 0;
                $row->laboratorium     = $t->laboratorium   ?? 0;
                $row->obat             = $t->obat           ?? 0;
                $row->lain_lain        = $t->lain_lain      ?? 0;
                $row->trf_rj           = $t->trf_rj         ?? 0;
                // kalkulasi tarif UGD
                $row->tarif_detail = $this->buatTarifUGD($row);
            } else {
                // ambil dari RJ
                $t = $totalsRJ->get($key, (object)[]);
                $row->datadaftarpolirj_json = $jsonRJ[$key] ?? null;
                $row->admin_up           = $t->admin_up            ?? 0;
                $row->jasa_karyawan      = $t->jasa_karyawan       ?? 0;
                $row->jasa_dokter        = $t->jasa_dokter         ?? 0;
                $row->jasa_medis         = $t->jasa_medis          ?? 0;
                $row->admin_rawat_jalan  = $t->admin_rawat_jalan   ?? 0;
                $row->lain_lain          = $t->lain_lain           ?? 0;
                $row->radiologi          = $t->radiologi           ?? 0;
                $row->laboratorium       = $t->laboratorium        ?? 0;
                $row->obat               = $t->obat                ?? 0;
                // kalkulasi tarif Rawat Jalan
                $row->tarif_detail = $this->buatTarifRawatJalan($row);
            }
            // 5. Hitung total semua komponen
            $row->tarif_total = array_sum($row->tarif_detail);
            return $row;
        });

        return $new;
    }

    public function hitungTotallAll()
    {

        //////////////////////////////////////////
        // Query Khusus BPJS///////////////////////////////
        //////////////////////////////////////////
        $queryRJ = DB::table('rsview_rjkasir')
            ->select(
                DB::raw("to_char(rj_date,'dd/mm/yyyy hh24:mi:ss') AS rj_date"),
                DB::raw("to_char(rj_date,'yyyymmddhh24miss') AS rj_date1"),
                'vno_sep',
                'rj_no',
                'reg_no',
                'reg_name',
                'poli_id',
                'poli_desc',
                'dr_id',
                'dr_name',

            )
            ->where(DB::raw("nvl(rj_status,'A')"), '=', 'L')
            ->whereIn('klaim_id', function ($klaimData) {
                $klaimData->select('klaim_id')
                    ->from('rsmst_klaimtypes')
                    ->where('klaim_status', 'BPJS');
            })
            ->where(DB::raw("to_char(rj_date,'mm/yyyy')"), '=', $this->myTopBar['refDate']);

        $queryUGD = DB::table('rsview_ugdkasir')
            ->select(
                DB::raw("to_char(rj_date,'dd/mm/yyyy hh24:mi:ss') AS rj_date"),
                DB::raw("to_char(rj_date,'yyyymmddhh24miss') AS rj_date1"),
                'vno_sep',
                'rj_no',
                'reg_no',
                'reg_name',
                DB::raw("1000 AS poli_id"),
                DB::raw("'UGD' AS poli_desc"),
                'dr_id',
                'dr_name',

            )
            ->where(DB::raw("nvl(rj_status,'A')"), '=', 'L')
            ->whereIn('klaim_id', function ($klaimData) {
                $klaimData->select('klaim_id')
                    ->from('rsmst_klaimtypes')
                    ->where('klaim_status', 'BPJS');
            })
            ->where(DB::raw("to_char(rj_date,'mm/yyyy')"), '=', $this->myTopBar['refDate']);



        $qUnionRJUGD = $queryRJ->unionAll($queryUGD);

        // //Query Total
        $queryRJUGDGridAll =  DB::query()
            ->fromSub($qUnionRJUGD, 'u')     // Laravel≥5.5 / Yajra Oci8 mendukung
            ->orderBy('u.rj_date1', 'desc')
            ->get();
        $queryRJUGDGridAll = $this->perhitunganTarifRawatJalanUGDAll($queryRJUGDGridAll);

        $jumlahDisetujuiRJ       = 0;
        $jmlKlaimDisetujuiRJ     = 0;
        $jumlahDisetujuiUGD      = 0;
        $jmlKlaimDisetujuiUGD    = 0;

        $jml_klaim               = 0;    // total baris klaim (UGD + RJ)
        $total_klaim             = 0;    // akumulasi tarif_total


        $this->klaimTidakDisetujui = []; //reset klaimTidakDisetujui;
        foreach ($queryRJUGDGridAll as $row) {
            // setiap baris dihitung sebagai 1 klaim
            $jml_klaim++;
            // akumulasi tarif_total
            $total_klaim += $row->tarif_total;

            if (strtoupper($row->poli_desc) === 'UGD') {
                // UGD
                $json = json_decode($row->datadaftarugd_json, true) ?? [];
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $this->klaimTidakDisetujui[] = [
                        'vno_sep'     => $row->vno_sep,
                        'rj_no'       => $row->rj_no,
                        'reg_no'      => $row->reg_no,
                        'reg_name'    => $row->reg_name,
                        'poli_desc'   => $row->poli_desc,
                        'pasien'      => $row->pasien_name ?? '-',
                        'dr_id'       => $row->dr_id,
                        'dr_name'     => $row->dr_name,
                        'tarif_total' => $row->tarif_total,
                        'rj_date'     => $row->rj_date ?? '-',
                        'status'      => 'Json Bermasalah',
                    ];
                }

                if (!empty($json['umbalBpjs']['disetujui'])) {
                    $jumlahDisetujuiUGD   += (int) $json['umbalBpjs']['disetujui'];
                    $jmlKlaimDisetujuiUGD++;
                } else {
                    $this->klaimTidakDisetujui[] = [
                        'vno_sep'     => $row->vno_sep,
                        'rj_no'       => $row->rj_no,
                        'reg_no'      => $row->reg_no,
                        'reg_name'    => $row->reg_name,
                        'poli_desc'   => $row->poli_desc,
                        'pasien'      => $row->pasien_name ?? '-',
                        'dr_id'       => $row->dr_id,
                        'dr_name'     => $row->dr_name,
                        'tarif_total' => $row->tarif_total,
                        'rj_date'     => $row->rj_date ?? '-',
                        'status'      => 'Tidak Disetujui',
                    ];
                }
            } else {
                // Rawat Jalan
                $json = json_decode($row->datadaftarpolirj_json, true) ?? [];
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $this->klaimTidakDisetujui[] = [
                        'vno_sep'     => $row->vno_sep,
                        'rj_no'       => $row->rj_no,
                        'reg_no'      => $row->reg_no,
                        'reg_name'    => $row->reg_name,
                        'poli_desc'   => $row->poli_desc,
                        'pasien'      => $row->pasien_name ?? '-',
                        'dr_id'       => $row->dr_id,
                        'dr_name'     => $row->dr_name,
                        'tarif_total' => $row->tarif_total,
                        'rj_date'     => $row->rj_date ?? '-',
                        'status'      => 'Json Bermasalah',
                    ];
                }
                if (!empty($json['umbalBpjs']['disetujui'])) {
                    $jumlahDisetujuiRJ    += (int) $json['umbalBpjs']['disetujui'];
                    $jmlKlaimDisetujuiRJ++;
                } else {
                    $this->klaimTidakDisetujui[] = [
                        'vno_sep'     => $row->vno_sep,
                        'rj_no'       => $row->rj_no,
                        'reg_no'      => $row->reg_no,
                        'reg_name'    => $row->reg_name,
                        'poli_desc'   => $row->poli_desc,
                        'pasien'      => $row->pasien_name ?? '-',
                        'dr_id'       => $row->dr_id,
                        'dr_name'     => $row->dr_name,
                        'tarif_total' => $row->tarif_total,
                        'rj_date'     => $row->rj_date ?? '-',
                        'status'      => 'Tidak Disetujui',
                    ];
                }
            }
        }

        $this->myQueryDataSum['jml_all'] = $jml_klaim;
        $this->myQueryDataSum['total_all'] = $total_klaim;
        $this->myQueryDataSum['disetujui_bpjs'] = $jumlahDisetujuiUGD + $jumlahDisetujuiRJ;
        $this->myQueryDataSum['jml_disetujui_bpjs'] = $jmlKlaimDisetujuiUGD + $jmlKlaimDisetujuiRJ;
    }

    public function mount()
    {
        $this->setterTopBarrefDate();
        $this->hitungTotallAll();
    }




    // select data start////////////////
    public function render()
    {
        $mySearch = $this->refFilter;
        $myRefdate = $this->myTopBar['refDate'];
        //////////////////////////////////////////
        // Query Khusus BPJS///////////////////////////////
        //////////////////////////////////////////
        $queryRJ = DB::table('rsview_rjkasir')
            ->select(
                DB::raw("to_char(rj_date,'dd/mm/yyyy hh24:mi:ss') AS rj_date"),
                DB::raw("to_char(rj_date,'yyyymmddhh24miss') AS rj_date1"),
                'vno_sep',
                'rj_no',
                'reg_no',
                'reg_name',
                'poli_id',
                'poli_desc',
                'dr_id',
                'dr_name',

            )
            ->where(DB::raw("nvl(rj_status,'A')"), '=', 'L')
            ->whereIn('klaim_id', function ($klaimData) {
                $klaimData->select('klaim_id')
                    ->from('rsmst_klaimtypes')
                    ->where('klaim_status', 'BPJS');
            })
            ->where(DB::raw("to_char(rj_date,'mm/yyyy')"), '=', $myRefdate);



        $queryRJ->where(function ($q) use ($mySearch) {
            $q->Where(DB::raw('upper(reg_name)'), 'like', '%' . strtoupper($mySearch) . '%')
                ->orWhere(DB::raw('upper(reg_no)'), 'like', '%' . strtoupper($mySearch) . '%')
                ->orWhere(DB::raw('upper(vno_sep)'), 'like', '%' . strtoupper($mySearch) . '%')
                ->orWhere(DB::raw('upper(dr_name)'), 'like', '%' . strtoupper($mySearch) . '%');
        });



        $queryUGD = DB::table('rsview_ugdkasir')
            ->select(
                DB::raw("to_char(rj_date,'dd/mm/yyyy hh24:mi:ss') AS rj_date"),
                DB::raw("to_char(rj_date,'yyyymmddhh24miss') AS rj_date1"),
                'vno_sep',
                'rj_no',
                'reg_no',
                'reg_name',
                DB::raw("1000 AS poli_id"),
                DB::raw("'UGD' AS poli_desc"),
                'dr_id',
                'dr_name',

            )
            ->where(DB::raw("nvl(rj_status,'A')"), '=', 'L')
            ->whereIn('klaim_id', function ($klaimData) {
                $klaimData->select('klaim_id')
                    ->from('rsmst_klaimtypes')
                    ->where('klaim_status', 'BPJS');
            })
            ->where(DB::raw("to_char(rj_date,'mm/yyyy')"), '=', $myRefdate);

        $queryUGD->where(function ($q) use ($mySearch) {
            $q->Where(DB::raw('upper(reg_name)'), 'like', '%' . strtoupper($mySearch) . '%')
                ->orWhere(DB::raw('upper(reg_no)'), 'like', '%' . strtoupper($mySearch) . '%')
                ->orWhere(DB::raw('upper(vno_sep)'), 'like', '%' . strtoupper($mySearch) . '%')
                ->orWhere(DB::raw('upper(dr_name)'), 'like', '%' . strtoupper($mySearch) . '%');
        });


        $qUnionRJUGD = $queryRJ->unionAll($queryUGD);

        $queryRJUGDGridPagination = DB::query()
            ->fromSub($qUnionRJUGD, 'u')   // Laravel≥5.5 / Yajra Oci8 mendukung
            ->orderBy('u.rj_date1', 'desc')
            ->paginate($this->limitPerPage);

        $queryRJUGDGrid = $this->perhitunganTarifRawatJalanUGD($queryRJUGDGridPagination);


        return view(
            'livewire.grouping-b-p-j-s.grouping-b-p-j-s-r-j',
            [
                'myQueryData' => $queryRJUGDGrid,
            ]
        );
    }
    // select data end////////////////


}
