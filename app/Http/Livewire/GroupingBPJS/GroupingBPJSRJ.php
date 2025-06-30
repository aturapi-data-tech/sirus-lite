<?php

namespace App\Http\Livewire\GroupingBPJS;


use Illuminate\Support\Facades\DB;
use App\Http\Traits\EmrRJ\EmrRJTrait;

use Livewire\Component;

use Livewire\WithFileUploads;
// use PhpOffice\PhpSpreadsheet\IOFactory;
use Livewire\WithPagination;
use Carbon\Carbon;

use Spatie\PdfToText\Pdf;


class GroupingBPJSRJ extends Component
{
    use WithPagination, WithFileUploads, EmrRJTrait;

    // primitive Variable
    public string $myTitle = 'FIle Grouping BPJS Rawat Jalan';
    public string $mySnipt = 'Grouping BPJS Rawat Jalan';
    public string $myProgram = 'Grouping BPJS Rawat Jalan';

    public array $myLimitPerPages = [10, 50, 100, 150, 200, 1000];
    // limit record per page -resetExcept////////////////
    public int $limitPerPage = 10;



    // my Top Bar
    public array $myTopBar = ['refDate' => ''];

    public string $refFilter = '';
    // search logic -resetExcept////////////////
    protected $queryString = [
        'refFilter' => ['except' => '', 'as' => 'cariData'],
        'page' => ['except' => 1, 'as' => 'p'],
    ];

    // reset page when myTopBar Change
    public function updatedReffilter()
    {
        $this->resetPage();
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
        $text = Pdf::getText($path, '/usr/bin/pdftotext');

        // Normalisasi teks
        $text = str_replace("\xC2\xA0", ' ', $text);
        $text = preg_replace('/[ ]{2,}/', ' ', $text);
        $lines = array_values(array_filter(array_map('trim', explode("\n", $text))));

        // Hapus baris header
        $filtered = array_filter($lines, function ($line) {
            $line = trim(str_replace("\f", '', $line));
            return !preg_match('/^(Hal\.\d+\/\d+|RINCIAN DATA HASIL VERIFIKASI|Nama RS|Tingkat Pelayanan|Bulan Pelayanan|^: .+|: RJTL|No$|No\.SEP|Tgl\. Verifikasi|Biaya$|Riil RS|Diajukan|Disetujui)$/i', $line);
        });

        $lines = array_values(array_filter($filtered)); // Reset index
        $data = [];
        $chunks = array_chunk($lines, 6); // tiap data pasien: 6 baris

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

        foreach ($chunks as $index => $chunk) {

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
                } else {
                    $this->dataUmbalBPJSTidakAdaDiRS[] = $umbal;
                }
            }
        }
        // 3. Bersihkan input & error
        $this->reset(['file', 'dataUmbalBPJSTidakAdaDiRS']);          // kosongkan input–nya
        $this->resetValidation();      // hapus semua pesan error
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
    public function mount()
    {
        $this->setterTopBarrefDate();
    }

    // select data start////////////////
    public function render()
    {
        $mySearch = $this->refFilter;
        $myRefdate = $this->myTopBar['refDate'];

        //////////////////////////////////////////
        // Query Khusus BPJS///////////////////////////////
        //////////////////////////////////////////
        $query = DB::table('rsview_rjkasir')
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
                'datadaftarpolirj_json',
                DB::raw("(select sum(txn_nominal) from rsview_rjstrs where rj_no = rsview_rjkasir.rj_no and txn_id = 'ADMIN UP') as admin_up"),
                DB::raw("(select sum(txn_nominal) from rsview_rjstrs where rj_no = rsview_rjkasir.rj_no and txn_id = 'JASA KARYAWAN') as jasa_karyawan"),
                DB::raw("(select sum(txn_nominal) from rsview_rjstrs where rj_no = rsview_rjkasir.rj_no and txn_id = 'JASA DOKTER') as jasa_dokter"),
                DB::raw("(select sum(txn_nominal) from rsview_rjstrs where rj_no = rsview_rjkasir.rj_no and txn_id = 'JASA MEDIS')  as jasa_medis"),
                DB::raw("(select sum(txn_nominal) from rsview_rjstrs where rj_no = rsview_rjkasir.rj_no and txn_id = 'ADMIN RAWAT JALAN') as admin_rawat_jalan"),
                DB::raw("(select sum(txn_nominal) from rsview_rjstrs where rj_no = rsview_rjkasir.rj_no and txn_id = 'LAIN-LAIN') as lain_lain"),
                DB::raw("(select sum(txn_nominal) from rsview_rjstrs where rj_no = rsview_rjkasir.rj_no and txn_id = 'RADIOLOGI')  as radiologi"),
                DB::raw("(select sum(txn_nominal) from rsview_rjstrs where rj_no = rsview_rjkasir.rj_no and txn_id = 'LABORAT')  as laboratorium"),
                DB::raw("(select sum(txn_nominal) from rsview_rjstrs where rj_no = rsview_rjkasir.rj_no and txn_id = 'OBAT') as obat"),
            )
            ->where(DB::raw("nvl(rj_status,'A')"), '=', 'L')
            ->whereIn('klaim_id', function ($klaimData) {
                $klaimData->select('klaim_id')
                    ->from('rsmst_klaimtypes')
                    ->where('klaim_status', 'BPJS');
            })
            ->where(DB::raw("to_char(rj_date,'mm/yyyy')"), '=', $myRefdate);

        $query->where(function ($q) use ($mySearch) {
            $q->Where(DB::raw('upper(reg_name)'), 'like', '%' . strtoupper($mySearch) . '%')
                ->orWhere(DB::raw('upper(reg_no)'), 'like', '%' . strtoupper($mySearch) . '%')
                ->orWhere(DB::raw('upper(vno_sep)'), 'like', '%' . strtoupper($mySearch) . '%')
                ->orWhere(DB::raw('upper(dr_name)'), 'like', '%' . strtoupper($mySearch) . '%');
        })
            ->orderBy('rj_date1',  'desc');

        $detail = $query->get();
        $myQueryDataSum = [
            'admin_up'         => $detail->sum('admin_up'),
            'jasa_karyawan'    => $detail->sum('jasa_karyawan'),
            'jasa_dokter'      => $detail->sum('jasa_dokter'),
            'jasa_medis'       => $detail->sum('jasa_medis'),
            'admin_rawat_jalan' => $detail->sum('admin_rawat_jalan'),
            'lain_lain'        => $detail->sum('lain_lain'),
            'radiologi'        => $detail->sum('radiologi'),
            'laboratorium'     => $detail->sum('laboratorium'),
            'obat'             => $detail->sum('obat'),

        ];
        // sumall
        $myQueryDataSum['total_all'] = array_sum($myQueryDataSum);
        $myQueryDataSum['jml_all'] = count($detail);
        $jumlahDisetujui = 0;

        // BPJS
        $jumlahDisetujui = 0;
        $jmlKlaimDisetujui = 0;
        foreach ($detail as $row) {
            $json = json_decode($row->datadaftarpolirj_json, true);
            if (!empty($json['umbalBpjs']['disetujui'])) {
                $jumlahDisetujui += (int) $json['umbalBpjs']['disetujui'];
                $jmlKlaimDisetujui++;
            }
        }

        $myQueryDataSum['disetujui_bpjs'] = $jumlahDisetujui;
        $myQueryDataSum['jml_disetujui_bpjs'] = $jmlKlaimDisetujui;




        return view(
            'livewire.grouping-b-p-j-s.grouping-b-p-j-s-r-j',
            [
                'myQueryData' => $query->paginate($this->limitPerPage),
                'myQueryDataSum' => $myQueryDataSum
            ]
        );
    }
    // select data end////////////////


}
