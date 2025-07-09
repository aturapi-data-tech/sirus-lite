<?php

namespace App\Http\Livewire\GroupingBPJS;


use Illuminate\Support\Facades\DB;
use App\Http\Traits\EmrRI\EmrRITrait;

use Livewire\Component;

use Livewire\WithFileUploads;
// use PhpOffice\PhpSpreadsheet\IOFactory;
use Livewire\WithPagination;
use Carbon\Carbon;

use Spatie\PdfToText\Pdf;


class GroupingBPJSRI extends Component
{
    use WithPagination, WithFileUploads, EmrRITrait;

    // primitive Variable
    public string $myTitle = 'FIle Grouping BPJS Rawat Inap';
    public string $mySnipt = 'Grouping BPJS Rawat Inap';
    public string $myProgram = 'Grouping BPJS Rawat Inap';

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
        $text = Pdf::getText($path, '/usr/local/bin/pdftotext-wrapper');


        // Normalisasi teks
        $text = str_replace("\xC2\xA0", ' ', $text);
        $text = preg_replace('/[ ]{2,}/', ' ', $text);
        $lines = array_values(array_filter(array_map('trim', explode("\n", $text))));
        // Hapus baris header
        $filtered = array_filter($lines, function ($line) {
            $line = trim(str_replace("\f", '', $line));
            return !preg_match('/^(Hal\.\d+\/\d+|RINCIAN DATA HASIL VERIFIKASI|Nama RS|Tingkat Pelayanan|Bulan Pelayanan|^: .+|: RITL|No$|No\.SEP|Tgl\. Verifikasi|Biaya$|Riil RS|Diajukan|Disetujui)$/i', $line);
        });

        $lines = array_values(array_filter($filtered)); // Reset index
        $data = [];
        $chunks = array_chunk($lines, 6); // tiap data pasien: 6 baris
        $myRefdate = $this->myTopBar['refDate'];

        $dataRjLookup = DB::table('rsview_rihdrs')
            ->select('vno_sep', 'rihdr_no')
            ->where(DB::raw("nvl(ri_status,'I')"), '=', 'P')
            ->whereIn('klaim_id', function ($klaimData) {
                $klaimData->select('klaim_id')
                    ->from('rsmst_klaimtypes')
                    ->where('klaim_status', 'BPJS');
            })
            ->where(DB::raw("to_char(exit_date,'mm/yyyy')"), '=', $myRefdate)
            ->pluck('rihdr_no', 'vno_sep') // hasil: ['0184R006xxxx' => 'RI2025xxxx']
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
                    $rihdr_no = $dataRjLookup[$no_sep];

                    $findDataRI = $this->findDataRI($rihdr_no);

                    $dataDaftarRI = $findDataRI;

                    $dataDaftarRI['umbalBpjs'] = $umbal;

                    $this->updateJsonRI($rihdr_no, $dataDaftarRI);
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
        $query = DB::table('rsview_rihdrs')
            ->select(
                DB::raw("to_char(entry_date,'dd/mm/yyyy hh24:mi:ss') AS entry_date"),
                DB::raw("to_char(entry_date,'yyyymmddhh24miss') AS entry_date1"),
                DB::raw("to_char(exit_date,'dd/mm/yyyy hh24:mi:ss') AS exit_date"),
                DB::raw("to_char(exit_date,'yyyymmddhh24miss') AS exit_date1"),
                'vno_sep',
                'rihdr_no',
                'reg_no',
                'reg_name',
                'datadaftarri_json',


                DB::raw("(SELECT SUM(NVL(actd_price, 0) * NVL(actd_qty, 0)) FROM rstxn_riactdocs WHERE rihdr_no = rsview_rihdrs.rihdr_no) as jasa_dokter"),
                DB::raw("(SELECT SUM(NVL(actp_price, 0) * NVL(actp_qty, 0)) FROM rstxn_riactparams WHERE rihdr_no = rsview_rihdrs.rihdr_no) as jasa_medis"),
                DB::raw("(SELECT SUM(NVL(konsul_price, 0)) FROM rstxn_rikonsuls WHERE rihdr_no = rsview_rihdrs.rihdr_no) as konsultasi"),
                DB::raw("(SELECT SUM(NVL(visit_price, 0)) FROM rstxn_rivisits WHERE rihdr_no = rsview_rihdrs.rihdr_no) as visit"),
                'admin_age',
                'admin_status',
                DB::raw("(SELECT SUM(NVL(ribon_price, 0)) FROM rstxn_ribonobats WHERE rihdr_no = rsview_rihdrs.rihdr_no) as bon_resep"),
                DB::raw("(SELECT SUM(NVL(riobat_qty, 0) * NVL(riobat_price, 0)) FROM rstxn_riobats WHERE rihdr_no = rsview_rihdrs.rihdr_no) as obat_pinjam"),
                DB::raw("(SELECT SUM(NVL(riobat_qty, 0) * NVL(riobat_price, 0)) FROM rstxn_riobatrtns WHERE rihdr_no = rsview_rihdrs.rihdr_no) as return_obat"),
                DB::raw("(SELECT SUM(NVL(rirad_price, 0)) FROM rstxn_riradiologs WHERE rihdr_no = rsview_rihdrs.rihdr_no) as radiologi"),
                DB::raw("(SELECT SUM(NVL(lab_price, 0)) FROM rstxn_rilabs WHERE rihdr_no = rsview_rihdrs.rihdr_no) as laboratorium"),
                DB::raw("(SELECT SUM(NVL(ok_price, 0)) FROM rstxn_rioks WHERE rihdr_no = rsview_rihdrs.rihdr_no) as operasi"),
                DB::raw("(SELECT SUM(NVL(other_price, 0)) FROM rstxn_riothers WHERE rihdr_no = rsview_rihdrs.rihdr_no) as lain_lain"),
                DB::raw("(SELECT SUM(
                        NVL(rj_admin, 0) + NVL(poli_price, 0) + NVL(acte_price, 0) +
                        NVL(actp_price, 0) + NVL(actd_price, 0) + NVL(obat, 0) +
                        NVL(lab, 0) + NVL(rad, 0) + NVL(other, 0) + NVL(rs_admin, 0)
                    )
                    FROM rstxn_ritempadmins WHERE rihdr_no = rsview_rihdrs.rihdr_no) as rawat_jalan"),

                DB::raw("(
                        SELECT SUM(
                          NVL(room_price,0)
                          * NVL(
                              day,
                              CEIL(
                                DECODE(
                                  NVL(end_date, SYSDATE) - NVL(start_date, SYSDATE),
                                  0, 1,
                                  NVL(end_date, SYSDATE) - NVL(start_date, SYSDATE)
                                )
                              )
                            )
                        )
                        FROM rsmst_trfrooms
                        WHERE rihdr_no = rsview_rihdrs.rihdr_no
                    ) AS total_room_price"),

                DB::raw("(
                        SELECT SUM(
                          NVL(perawatan_price,0)
                          * NVL(
                              day,
                              CEIL(
                                DECODE(
                                  NVL(end_date, SYSDATE) - NVL(start_date, SYSDATE),
                                  0, 1,
                                  NVL(end_date, SYSDATE) - NVL(start_date, SYSDATE)
                                )
                              )
                            )
                        )
                        FROM rsmst_trfrooms
                        WHERE rihdr_no = rsview_rihdrs.rihdr_no
                    ) AS total_perawatan_price"),

                DB::raw("(
                        SELECT SUM(
                          NVL(common_service,0)
                          * NVL(
                              day,
                              CEIL(
                                DECODE(
                                  NVL(end_date, SYSDATE) - NVL(start_date, SYSDATE),
                                  0, 1,
                                  NVL(end_date, SYSDATE) - NVL(start_date, SYSDATE)
                                )
                              )
                            )
                        )
                        FROM rsmst_trfrooms
                        WHERE rihdr_no = rsview_rihdrs.rihdr_no
                    ) AS total_common_service"),

            )
            ->where(DB::raw("nvl(ri_status,'I')"), '=', 'P')
            ->whereIn('klaim_id', function ($klaimData) {
                $klaimData->select('klaim_id')
                    ->from('rsmst_klaimtypes')
                    ->where('klaim_status', 'BPJS');
            })
            ->where(DB::raw("to_char(exit_date,'mm/yyyy')"), '=', $myRefdate);

        $query->where(function ($q) use ($mySearch) {
            $q->Where(DB::raw('upper(reg_name)'), 'like', '%' . strtoupper($mySearch) . '%')
                ->orWhere(DB::raw('upper(reg_no)'), 'like', '%' . strtoupper($mySearch) . '%')
                ->orWhere(DB::raw('upper(vno_sep)'), 'like', '%' . strtoupper($mySearch) . '%')
                ->orWhere(DB::raw('upper(dr_name)'), 'like', '%' . strtoupper($mySearch) . '%');
        })
            ->orderBy('exit_date1',  'desc');

        $detail = $query->get();
        $myQueryDataSum = [
            'jasa_dokter'           => $detail->sum('jasa_dokter'),
            'jasa_medis'            => $detail->sum('jasa_medis'),
            'konsultasi'            => $detail->sum('konsultasi'),
            'visit'                 => $detail->sum('visit'),
            'admin_age'             => $detail->sum('admin_age'),
            'admin_status'          => $detail->sum('admin_status'),
            'bon_resep'             => $detail->sum('bon_resep'),
            'obat_pinjam'           => $detail->sum('obat_pinjam'),
            'return_obat'           => $detail->sum('return_obat'),
            'radiologi'             => $detail->sum('radiologi'),
            'laboratorium'          => $detail->sum('laboratorium'),
            'operasi'               => $detail->sum('operasi'),
            'lain_lain'             => $detail->sum('lain_lain'),
            'rawat_jalan'           => $detail->sum('rawat_jalan'),
            'total_room_price'      => $detail->sum('total_room_price'),
            'total_perawatan_price' => $detail->sum('total_perawatan_price'),
            'total_common_service'  => $detail->sum('total_common_service'),
        ];
        // sumall
        $myQueryDataSum['total_all'] = array_sum($myQueryDataSum);
        $myQueryDataSum['jml_all'] = count($detail);
        $jumlahDisetujui = 0;

        // BPJS
        $jumlahDisetujui = 0;
        $jmlKlaimDisetujui = 0;
        foreach ($detail as $row) {
            $json = json_decode($row->datadaftarri_json, true);
            if (!empty($json['umbalBpjs']['disetujui'])) {
                $jumlahDisetujui += (int) $json['umbalBpjs']['disetujui'];
                $jmlKlaimDisetujui++;
            }
        }

        $myQueryDataSum['disetujui_bpjs'] = $jumlahDisetujui;
        $myQueryDataSum['jml_disetujui_bpjs'] = $jmlKlaimDisetujui;




        return view(
            'livewire.grouping-b-p-j-s.grouping-b-p-j-s-r-i',
            [
                'myQueryData' => $query->paginate($this->limitPerPage),
                'myQueryDataSum' => $myQueryDataSum
            ]
        );
    }
    // select data end////////////////


}
