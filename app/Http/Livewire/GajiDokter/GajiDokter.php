<?php

namespace App\Http\Livewire\GajiDokter;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Traits\LOV\LOVDokter\LOVDokterTrait;
use Carbon\Carbon;

class GajiDokter extends Component
{
    use WithPagination, LOVDokterTrait;


    // primitive Variable
    public string $myTitle = 'Pendapatan Jasa Dokter';
    public string $mySnipt = 'Pendapatan Jasa Dokter';
    public string $myProgram = 'Pendapatan Jasa Dokter';

    public array $myLimitPerPages = [5, 10, 15, 20, 100];
    // limit record per page -resetExcept////////////////
    public int $limitPerPage = 10;

    // LOV Nested
    public array $dokter;

    private function syncDataFormEntry(): void
    {
        $this->myTopBar['drId'] = $this->dokter['DokterId'] ?? '';
        $this->myTopBar['drName'] = $this->dokter['DokterDesc'] ?? '';
    }
    private function syncLOV(): void
    {
        $this->dokter = $this->collectingMyDokter;
    }
    public function resetDokter()
    {
        $this->reset([
            'collectingMyDokter', //Reset LOV / render  / empty NestLov
        ]);
        $this->resetValidation();
    }
    // LOV Nested


    // my Top Bar
    public array $myTopBar = [

        'refBulan' => '',

        'drId' => '',
        'drName' => '',
        'drOptions' => [],
    ];

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


    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////

    public function mount()
    {
        // Set refBulan ke format mm/YYYY untuk bulan sekarang
        $this->myTopBar['refBulan'] = Carbon::now()->format('m/Y');
    }


    // select data start////////////////
    public function render()
    {

        // LOV
        $this->syncLOV();
        // FormEntry
        $this->syncDataFormEntry();

        // set mySearch
        $myRefBulan = $this->myTopBar['refBulan'];
        $myRefdrId = $this->myTopBar['drId'];


        //////////////////////////////////////////
        // Query ///////////////////////////////
        //////////////////////////////////////////
        $query = DB::table('rsview_newdocsalaries as v')
            ->select([
                'v.group_doc',
                'v.desc_doc',
                'v.dr_id',
                'v.dr_name',
                DB::raw('SUM(v.doc_nominal) AS doc_nominal'),
                DB::raw('k.klaim_status AS klaim_status'),
            ])
            ->join('rsmst_klaimtypes as k', 'v.klaim_id', '=', 'k.klaim_id')
            ->where('dr_id', $myRefdrId)
            ->whereRaw(
                "to_char(to_date(doc_date, 'dd/mm/yyyy'), 'mm/yyyy') = ?",
                [$myRefBulan]
            )
            ->groupBy(
                'v.group_doc',
                'v.desc_doc',
                'v.dr_id',
                'v.dr_name',
                'k.klaim_status'
            )
            ->orderBy('v.dr_id')
            ->orderBy('klaim_status')
            ->orderBy('v.group_doc')
            ->orderBy('v.desc_doc')
            ->get();


        foreach ($query as $row) {
            $txnNoList = DB::table('rsview_newdocsalaries as v')
                ->select(
                    'v.txn_no',
                    'v.group_doc',
                    'v.desc_doc',
                    'k.klaim_status',
                    DB::raw('SUM(v.doc_nominal) AS doc_nominal')
                )
                ->join('rsmst_klaimtypes as k', 'v.klaim_id', '=', 'k.klaim_id')
                ->whereRaw(
                    "to_char(to_date(doc_date, 'dd/mm/yyyy'), 'mm/yyyy') = ?",
                    [$myRefBulan]
                )
                ->where('v.group_doc', $row->group_doc)
                ->where('v.desc_doc', $row->desc_doc)
                ->where('v.dr_id', $row->dr_id)
                ->where('v.dr_name', $row->dr_name)
                ->where('k.klaim_status', $row->klaim_status)
                ->orderBy('v.txn_no')
                ->groupBy(
                    'v.txn_no',
                    'v.group_doc',
                    'v.desc_doc',
                    'k.klaim_status',
                )
                ->get();

            $disetujuiBpjs = 0;
            $notApprovedRihdr = [];
            foreach ($txnNoList as $txn) {
                if ($txn->klaim_status === 'BPJS') {
                    // Pilih sumber JSON berdasarkan group_doc
                    $datadaftar = null;

                    switch ($txn->desc_doc) {
                        // Operasi (OK)
                        case 'OPERATOR':
                        case 'ANASTESI':
                            $datadaftar = DB::table('rstxn_oks as a')
                                ->select('b.datadaftarri_json', 'vno_sep')
                                ->join('rstxn_rihdrs as b', 'a.rihdr_no', '=', 'b.rihdr_no')
                                ->where('a.ok_reg', $txn->txn_no)
                                ->first();
                            break;

                        // Rawat Inap
                        case 'VISIT':
                            $datadaftar = DB::table('rstxn_rivisits as v')
                                ->select('ri.datadaftarri_json', 'vno_sep')
                                ->join('rstxn_rihdrs as ri', 'v.rihdr_no', '=', 'ri.rihdr_no')
                                ->where('v.visit_no', $txn->txn_no)
                                ->first();
                            break;
                        case 'KONSUL':
                            $datadaftar = DB::table('rstxn_rikonsuls as k')
                                ->select('ri.datadaftarri_json', 'vno_sep')
                                ->join('rstxn_rihdrs as ri', 'k.rihdr_no', '=', 'ri.rihdr_no')
                                ->where('k.konsul_no', $txn->txn_no)
                                ->first();
                            break;
                        case 'JD RI':
                            $datadaftar = DB::table('rstxn_riactdocs as a')
                                ->select('ri.datadaftarri_json', 'vno_sep')
                                ->join('rstxn_rihdrs as ri', 'a.rihdr_no', '=', 'ri.rihdr_no')
                                ->where('a.actd_no', $txn->txn_no)
                                ->first();
                            break;

                        // UGD
                        case 'UP UGD':
                            $datadaftar = DB::table('rstxn_ugdhdrs as u')
                                ->select('u.datadaftarugd_json', 'vno_sep')
                                ->where('u.rj_no', $txn->txn_no)
                                ->first();
                            break;
                        case 'JD UGD':
                            $datadaftar = DB::table('rstxn_ugdaccdocs as a')
                                ->select('u.datadaftarugd_json', 'vno_sep')
                                ->join('rstxn_ugdhdrs as u', 'a.rj_no', '=', 'u.rj_no')
                                ->where('a.rjhn_dtl', $txn->txn_no)
                                ->first();
                            break;

                        // UGD Transfer (UGDTRF) → pakai rihdr_no
                        case 'UP UGDTRF':
                        case 'JD UGDTRF':
                            $datadaftar = DB::table('rstxn_rihdrs as ri')
                                ->select('ri.datadaftarri_json', 'vno_sep')
                                ->where('ri.rihdr_no', $txn->txn_no)
                                ->first();
                            break;

                        // Rawat Jalan
                        case 'UP RJ':
                            $datadaftar = DB::table('rstxn_rjhdrs as rj')
                                ->select('rj.datadaftarpolirj_json', 'vno_sep')
                                ->where('rj.rj_no', $txn->txn_no)
                                ->first();
                            break;
                        case 'JD RJ':
                            $datadaftar = DB::table('rstxn_rjaccdocs as a')
                                ->select('rj.datadaftarpolirj_json', 'vno_sep')
                                ->join('rstxn_rjhdrs as rj', 'a.rj_no', '=', 'rj.rj_no')
                                ->where('a.rjhn_dtl', $txn->txn_no)
                                ->first();
                            break;

                        // RJ Transfer (RJTRF) → pakai rihdr_no
                        case 'UP RJTRF':
                        case 'JD RJTRF':
                            $datadaftar = DB::table('rstxn_rihdrs as ri')
                                ->select('ri.datadaftarri_json', 'vno_sep')
                                ->where('ri.rihdr_no', $txn->txn_no)
                                ->first();
                            break;

                        // Klinik (RJK)
                        case 'UP KLINIK':
                        case 'JD KLINIK':
                            $datadaftar = DB::table('rstxn_rjhdrks as rjk')
                                ->select('rjk.datadaftarpolirj_json', 'vno_sep')
                                ->where('rjk.rj_no', $txn->txn_no)
                                ->first();
                            break;

                        default:
                            // Radiologi atau lainnya: tidak ada JSON
                            $datadaftar = null;
                            break;
                    }

                    // Decode JSON
                    $jsonString = $datadaftar->datadaftarri_json
                        ?? $datadaftar->datadaftarpolirj_json
                        ?? $datadaftar->datadaftarugd_json
                        ?? '{}';

                    // Decode JSON ke array
                    $json = json_decode($jsonString, true);

                    //Kumpulkan semua SEP
                    $vno_sep = $datadaftar->vno_sep ?? null;
                    if (!empty($vno_sep)) {
                        $allSepPerDokter[]     = $vno_sep;
                    }

                    $approved = (isset($json['umbalBpjs']['disetujui']));

                    if ($approved) {
                        $disetujuiBpjs += (int) $txn->doc_nominal;
                    } else {
                        $notApprovedRihdr[] = [
                            'txn_no'   => $txn->txn_no,
                            'desc_doc' => $txn->desc_doc,
                            'doc_nominal' => $txn->doc_nominal,

                        ];
                        $debugJson[] = [
                            'txn_no'   => $txn->txn_no,
                            'desc_doc' => $txn->desc_doc,
                            'doc_nominal' => $txn->doc_nominal,
                            'json'     => $json,  // simpan JSON mentah untuk debug
                        ];
                    }
                } elseif ($txn->klaim_status === 'UMUM') {
                    $disetujuiBpjs += (int) $txn->doc_nominal;
                }
            }


            if (!isset($disetujuiPerDesc[$row->desc_doc])) {
                $disetujuiPerDesc[$row->desc_doc] = 0;
            }
            $disetujuiPerDesc[$row->desc_doc] += $disetujuiBpjs;

            $row->disetujui = $disetujuiBpjs;
            $row->tidak_disetujui = $notApprovedRihdr;
            $row->debug_json = $debugJson;
        }
        $allSepPerDokter     = array_values(array_unique($allSepPerDokter ?? []));



        ////////////////////////////////////////////////
        // end Query
        ///////////////////////////////////////////////



        return view(
            'livewire.gaji-dokter.gaji-dokter',
            // ['myQueryData' => $query->paginate($this->limitPerPage)]
            [
                'myQueryData' => $query,
                'allSepPerDokter' => $allSepPerDokter
            ]

        );
    }
    // select data end////////////////


}
