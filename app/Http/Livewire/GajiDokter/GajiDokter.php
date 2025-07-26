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

    private function perhitunganTarifDisetujui(object $row)
    {
        dd($row);
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
            foreach ($txnNoList as $txn) {
                if ($txn->klaim_status === 'BPJS') {
                    // Pilih sumber JSON berdasarkan group_doc
                    $datadaftar = null;

                    switch ($txn->desc_doc) {
                        // Operasi (OK)
                        case 'OPERATOR':
                        case 'ANASTESI':
                            $datadaftar = DB::table('rstxn_oks as a')
                                ->join('rstxn_rihdrs as b', 'a.rihdr_no', '=', 'b.rihdr_no')
                                ->where('a.ok_reg', $txn->txn_no)
                                ->value('b.datadaftarri_json');
                            break;

                        // Rawat Inap
                        case 'VISIT':
                            $datadaftar = DB::table('rstxn_rivisits as v')
                                ->join('rstxn_rihdrs as ri', 'v.rihdr_no', '=', 'ri.rihdr_no')
                                ->where('v.visit_no', $txn->txn_no)
                                ->value('ri.datadaftarri_json');
                            break;
                        case 'KONSUL':
                            $datadaftar = DB::table('rstxn_rikonsuls as k')
                                ->join('rstxn_rihdrs as ri', 'k.rihdr_no', '=', 'ri.rihdr_no')
                                ->where('k.konsul_no', $txn->txn_no)
                                ->value('ri.datadaftarri_json');
                            break;
                        case 'JD RI':
                            $datadaftar = DB::table('rstxn_riactdocs as a')
                                ->join('rstxn_rihdrs as ri', 'a.rihdr_no', '=', 'ri.rihdr_no')
                                ->where('a.actd_no', $txn->txn_no)
                                ->value('ri.datadaftarri_json');
                            break;

                        // UGD
                        case 'UP UGD':
                            $datadaftar = DB::table('rstxn_ugdhdrs as u')
                                ->where('u.rj_no', $txn->txn_no)
                                ->value('u.datadaftarugd_json');
                            break;
                        case 'JD UGD':
                            $datadaftar = DB::table('rstxn_ugdaccdocs as a')
                                ->join('rstxn_ugdhdrs as u', 'a.rj_no', '=', 'u.rj_no')
                                ->where('a.rjhn_dtl', $txn->txn_no)
                                ->value('u.datadaftarugd_json');
                            break;

                        // UGD Transfer (UGDTRF) → pakai rihdr_no
                        case 'UP UGDTRF':
                        case 'JD UGDTRF':
                            $datadaftar = DB::table('rstxn_rihdrs as ri')
                                ->where('ri.rihdr_no', $txn->txn_no)
                                ->value('ri.datadaftarri_json');
                            break;

                        // Rawat Jalan
                        case 'UP RJ':
                            $datadaftar = DB::table('rstxn_rjhdrs as rj')
                                ->where('rj.rj_no', $txn->txn_no)
                                ->value('rj.datadaftarpolirj_json');
                            break;
                        case 'JD RJ':
                            $datadaftar = DB::table('rstxn_rjaccdocs as a')
                                ->join('rstxn_rjhdrs as rj', 'a.rj_no', '=', 'rj.rj_no')
                                ->where('a.rjhn_dtl', $txn->txn_no)
                                ->value('rj.datadaftarpolirj_json');
                            break;

                        // RJ Transfer (RJTRF) → pakai rihdr_no
                        case 'UP RJTRF':
                        case 'JD RJTRF':
                            $datadaftar = DB::table('rstxn_rihdrs as ri')
                                ->where('ri.rihdr_no', $txn->txn_no)
                                ->value('ri.datadaftarri_json');
                            break;

                        // Klinik (RJK)
                        case 'UP KLINIK':
                        case 'JD KLINIK':
                            $datadaftar = DB::table('rstxn_rjhdrks as rjk')
                                ->where('rjk.rj_no', $txn->txn_no)
                                ->value('rjk.datadaftarpolirj_json');
                            break;

                        default:
                            // Radiologi atau lainnya: tidak ada JSON
                            $datadaftar = null;
                            break;
                    }

                    // Decode JSON
                    $json = json_decode($datadaftar, true);

                    if (!empty($json['umbalBpjs']['disetujui'])) {
                        $disetujuiBpjs += (int) $txn->doc_nominal;
                    }
                } elseif ($txn->klaim_status === 'UMUM') {
                    // Jika klaim UMUM, langsung tambah nominal tanpa cek JSON
                    $disetujuiBpjs += (int) $txn->doc_nominal;
                }
            }

            // Tambahkan ke total per desc_doc
            if (!isset($disetujuiPerDesc[$row->desc_doc])) {
                $disetujuiPerDesc[$row->desc_doc] = 0;
            }
            $disetujuiPerDesc[$row->desc_doc] += $disetujuiBpjs;

            // Simpan pada row juga
            $row->disetujui = $disetujuiBpjs;
        }



        ////////////////////////////////////////////////
        // end Query
        ///////////////////////////////////////////////



        return view(
            'livewire.gaji-dokter.gaji-dokter',
            // ['myQueryData' => $query->paginate($this->limitPerPage)]
            ['myQueryData' => $query]

        );
    }
    // select data end////////////////


}
