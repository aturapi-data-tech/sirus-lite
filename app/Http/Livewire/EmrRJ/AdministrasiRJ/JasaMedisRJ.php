<?php

namespace App\Http\Livewire\EmrRJ\AdministrasiRJ;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
// use Carbon\Carbon;

use App\Http\Traits\customErrorMessagesTrait;

// use Illuminate\Support\Str;
use Spatie\ArrayToXml\ArrayToXml;
use Exception;


class JasaMedisRJ extends Component
{
    use WithPagination;


    // listener from blade////////////////
    protected $listeners = [
        'storeAssessmentDokterRJ' => 'store',
        'syncronizeAssessmentDokterRJFindData' => 'mount'
    ];


    //////////////////////////////z
    // Ref on top bar
    //////////////////////////////
    public $rjNoRef = 472309;



    // dataDaftarPoliRJ RJ
    public array $dataDaftarPoliRJ = [];

    //////////////////////////////////////////////////////////////////////


    //  table LOV////////////////



    public $dataJasaMedisLov = [];
    public $dataJasaMedisLovStatus = 0;
    public $dataJasaMedisLovSearch = '';
    public $selecteddataJasaMedisLovIndex = 0;

    public $collectingMyJasaMedis = [];







    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////
    public function updated($propertyName)
    {
        // dd($propertyName);
        // $this->validateOnly($propertyName);
    }




    /////////////////////////////////////////////////
    // Lov dataJasaMedisLov //////////////////////
    ////////////////////////////////////////////////
    public function clickdataJasaMedisLov()
    {
        $this->dataJasaMedisLovStatus = true;
        $this->dataJasaMedisLov = [];
    }

    public function updateddataJasaMedisLovsearch()
    {

        // Reset index of LoV
        $this->reset(['selecteddataJasaMedisLovIndex', 'dataJasaMedisLov']);
        // Variable Search
        $search = $this->dataJasaMedisLovSearch;

        // check LOV by dr_id rs id 
        $dataJasaMedisLovs = DB::table('rsmst_actparamedics  ')->select(
            'pact_id',
            'pact_desc',
            'pact_price'
        )
            ->where('pact_id', $search)
            ->where('active_status', '1')
            ->first();

        if ($dataJasaMedisLovs) {

            // set JasaMedis sep
            $this->addJasaMedis($dataJasaMedisLovs->pact_id, $dataJasaMedisLovs->pact_desc, $dataJasaMedisLovs->pact_price);
            $this->resetdataJasaMedisLov();
        } else {

            // if there is no id found and check (min 3 char on search)
            if (strlen($search) < 1) {
                $this->dataJasaMedisLov = [];
            } else {
                $this->dataJasaMedisLov = json_decode(
                    DB::table('rsmst_actparamedics ')->select(
                        'pact_id',
                        'pact_desc',
                        'pact_price'
                    )
                        ->where('active_status', '1')
                        ->where(DB::raw('upper(pact_desc)'), 'like', '%' . strtoupper($search) . '%')
                        ->limit(10)
                        ->orderBy('pact_id', 'ASC')
                        ->orderBy('pact_desc', 'ASC')
                        ->get(),
                    true
                );
            }
            $this->dataJasaMedisLovStatus = true;
            // set doing nothing
        }
    }
    // /////////////////////
    // LOV selected start
    public function setMydataJasaMedisLov($id)
    {
        $this->checkRjStatus();
        $dataJasaMedisLovs = DB::table('rsmst_actparamedics ')->select(
            'pact_id',
            'pact_desc',
            'pact_price'
        )
            ->where('active_status', '1')
            ->where('pact_id', $this->dataJasaMedisLov[$id]['pact_id'])
            ->first();

        // set dokter sep
        $this->addJasaMedis($dataJasaMedisLovs->pact_id, $dataJasaMedisLovs->pact_desc, $dataJasaMedisLovs->pact_price);
        $this->resetdataJasaMedisLov();
    }

    public function resetdataJasaMedisLov()
    {
        $this->reset(['dataJasaMedisLov', 'dataJasaMedisLovStatus', 'dataJasaMedisLovSearch', 'selecteddataJasaMedisLovIndex']);
    }

    public function selectNextdataJasaMedisLov()
    {
        if ($this->selecteddataJasaMedisLovIndex === "") {
            $this->selecteddataJasaMedisLovIndex = 0;
        } else {
            $this->selecteddataJasaMedisLovIndex++;
        }

        if ($this->selecteddataJasaMedisLovIndex === count($this->dataJasaMedisLov)) {
            $this->selecteddataJasaMedisLovIndex = 0;
        }
    }

    public function selectPreviousdataJasaMedisLov()
    {

        if ($this->selecteddataJasaMedisLovIndex === "") {
            $this->selecteddataJasaMedisLovIndex = count($this->dataJasaMedisLov) - 1;
        } else {
            $this->selecteddataJasaMedisLovIndex--;
        }

        if ($this->selecteddataJasaMedisLovIndex === -1) {
            $this->selecteddataJasaMedisLovIndex = count($this->dataJasaMedisLov) - 1;
        }
    }

    public function enterMydataJasaMedisLov($id)
    {
        $this->checkRjStatus();
        // jika JK belum siap maka toaster error
        if (isset($this->dataJasaMedisLov[$id]['pact_id'])) {
            $this->addJasaMedis($this->dataJasaMedisLov[$id]['pact_id'], $this->dataJasaMedisLov[$id]['pact_desc'], $this->dataJasaMedisLov[$id]['pact_price']);
            $this->resetdataJasaMedisLov();
        } else {
            $this->emit('toastr-error', "Jasa Medis belum tersedia.");
        }
    }


    // LOV selected end
    /////////////////////////////////////////////////
    // Lov dataJasaMedisLov //////////////////////
    ////////////////////////////////////////////////







    // insert and update record start////////////////
    public function store()
    {
        // set data RJno / NoBooking / NoAntrian / klaimId / kunjunganId
        $this->setDataPrimer();

        // Logic update mode start //////////
        $this->updateDataRJ($this->dataDaftarPoliRJ['rjNo']);
        $this->emit('syncronizeAssessmentDokterRJFindData');
    }

    private function updateDataRJ($rjNo): void
    {

        // update table trnsaksi
        DB::table('rstxn_rjhdrs')
            ->where('rj_no', $rjNo)
            ->update([
                'dataDaftarPoliRJ_json' => json_encode($this->dataDaftarPoliRJ, true),
                'dataDaftarPoliRJ_xml' => ArrayToXml::convert($this->dataDaftarPoliRJ),
            ]);

        $this->emit('toastr-success', "Jasa Medis berhasil disimpan.");
    }
    // insert and update record end////////////////


    private function findData($rjno): void
    {
        $findData = DB::table('rsview_rjkasir')
            ->select('datadaftarpolirj_json', 'vno_sep')
            ->where('rj_no', $rjno)
            ->first();

        $dataDaftarPoliRJ_json = isset($findData->datadaftarpolirj_json) ? $findData->datadaftarpolirj_json   : null;
        // if meta_data_pasien_json = null
        // then cari Data Pasien By Key Collection (exception when no data found)
        // 
        // else json_decode
        if ($dataDaftarPoliRJ_json) {
            $this->dataDaftarPoliRJ = json_decode($findData->datadaftarpolirj_json, true);

            // jika JasaMedis tidak ditemukan tambah variable JasaMedis pda array
            if (isset($this->dataDaftarPoliRJ['JasaMedis']) == false) {
                $this->dataDaftarPoliRJ['JasaMedis'] = [];
            }
        } else {

            $this->emit('toastr-error', "Data tidak dapat di proses json.");
            $dataDaftarPoliRJ = DB::table('rsview_rjkasir')
                ->select(
                    DB::raw("to_char(rj_date,'dd/mm/yyyy hh24:mi:ss') AS rj_date"),
                    DB::raw("to_char(rj_date,'yyyymmddhh24miss') AS rj_date1"),
                    'rj_no',
                    'reg_no',
                    'reg_name',
                    'sex',
                    'address',
                    'thn',
                    DB::raw("to_char(birth_date,'dd/mm/yyyy') AS birth_date"),
                    'poli_id',
                    'poli_desc',
                    'dr_id',
                    'dr_name',
                    'klaim_id',
                    // 'entry_id',
                    'shift',
                    'vno_sep',
                    'no_antrian',

                    'nobooking',
                    'push_antrian_bpjs_status',
                    'push_antrian_bpjs_json',
                    'kd_dr_bpjs',
                    'kd_poli_bpjs',
                    'rj_status',
                    'txn_status',
                    'erm_status',
                )
                ->where('rj_no', '=', $rjno)
                ->first();

            $this->dataDaftarPoliRJ = [
                "regNo" =>  $dataDaftarPoliRJ->reg_no,

                "drId" =>  $dataDaftarPoliRJ->dr_id,
                "drDesc" =>  $dataDaftarPoliRJ->dr_name,

                "poliId" =>  $dataDaftarPoliRJ->poli_id,
                "klaimId" => $dataDaftarPoliRJ->klaim_id,
                // "poliDesc" =>  $dataDaftarPoliRJ->poli_desc ,

                // "kddrbpjs" =>  $dataDaftarPoliRJ->kd_dr_bpjs ,
                // "kdpolibpjs" =>  $dataDaftarPoliRJ->kd_poli_bpjs ,

                "rjDate" =>  $dataDaftarPoliRJ->rj_date,
                "rjNo" =>  $dataDaftarPoliRJ->rj_no,
                "shift" =>  $dataDaftarPoliRJ->shift,
                "noAntrian" =>  $dataDaftarPoliRJ->no_antrian,
                "noBooking" =>  $dataDaftarPoliRJ->nobooking,
                "slCodeFrom" => "02",
                "passStatus" => "",
                "rjStatus" =>  $dataDaftarPoliRJ->rj_status,
                "txnStatus" =>  $dataDaftarPoliRJ->txn_status,
                "ermStatus" =>  $dataDaftarPoliRJ->erm_status,
                "cekLab" => "0",
                "kunjunganInternalStatus" => "0",
                "noReferensi" =>  $dataDaftarPoliRJ->reg_no,
                "postInap" => [],
                "internal12" => "1",
                "internal12Desc" => "Faskes Tingkat 1",
                "internal12Options" => [
                    [
                        "internal12" => "1",
                        "internal12Desc" => "Faskes Tingkat 1"
                    ],
                    [
                        "internal12" => "2",
                        "internal12Desc" => "Faskes Tingkat 2 RS"
                    ]
                ],
                "kontrol12" => "1",
                "kontrol12Desc" => "Faskes Tingkat 1",
                "kontrol12Options" => [
                    [
                        "kontrol12" => "1",
                        "kontrol12Desc" => "Faskes Tingkat 1"
                    ],
                    [
                        "kontrol12" => "2",
                        "kontrol12Desc" => "Faskes Tingkat 2 RS"
                    ],
                ],
                "taskIdPelayanan" => [
                    "taskId1" => "",
                    "taskId2" => "",
                    "taskId3" =>  $dataDaftarPoliRJ->rj_date,
                    "taskId4" => "",
                    "taskId5" => "",
                    "taskId6" => "",
                    "taskId7" => "",
                    "taskId99" => "",
                ],
                'sep' => [
                    "noSep" =>  $dataDaftarPoliRJ->vno_sep,
                    "reqSep" => [],
                    "resSep" => [],
                ]
            ];


            // jika JasaMedis tidak ditemukan tambah variable JasaMedis pda array
            if (isset($this->dataDaftarPoliRJ['JasaMedis']) == false) {
                $this->dataDaftarPoliRJ['JasaMedis'] = [];
            }
        }
    }


    private function setDataPrimer(): void
    {
    }



    private function addJasaMedis($JasaMedisId, $JasaMedisDesc, $salesPrice): void
    {

        $this->collectingMyJasaMedis = [
            'JasaMedisId' => $JasaMedisId,
            'JasaMedisDesc' => $JasaMedisDesc,
            'JasaMedisPrice' => $salesPrice,
        ];
    }

    public function insertJasaMedis(): void
    {

        // validate
        // customErrorMessages
        $messages = customErrorMessagesTrait::messages();
        // require nik ketika pasien tidak dikenal
        $rules = [
            "collectingMyJasaMedis.JasaMedisId" => 'bail|required|exists:rsmst_actparamedics ,pact_id',
            "collectingMyJasaMedis.JasaMedisDesc" => 'bail|required|',
            "collectingMyJasaMedis.JasaMedisPrice" => 'bail|required|numeric|',

        ];

        // Proses Validasi///////////////////////////////////////////
        $this->validate($rules, $messages);

        // validate


        // pengganti race condition
        // start:
        try {

            $lastInserted = DB::table('rstxn_rjactparams')
                ->select(DB::raw("nvl(max(pact_dtl)+1,1) as pact_dtl_max"))
                ->first();
            // insert into table transaksi
            DB::table('rstxn_rjactparams')
                ->insert([
                    'pact_dtl' => $lastInserted->pact_dtl_max,
                    'rj_no' => $this->rjNoRef,
                    'pact_id' => $this->collectingMyJasaMedis['JasaMedisId'],
                    'pact_price' => $this->collectingMyJasaMedis['JasaMedisPrice'],
                ]);


            $this->dataDaftarPoliRJ['JasaMedis'][] = [
                'JasaMedisId' => $this->collectingMyJasaMedis['JasaMedisId'],
                'JasaMedisDesc' => $this->collectingMyJasaMedis['JasaMedisDesc'],
                'JasaMedisPrice' => $this->collectingMyJasaMedis['JasaMedisPrice'],
                'rjpactDtl' => $lastInserted->pact_dtl_max,
                'rjNo' => $this->rjNoRef,
            ];

            $this->store();
            $this->reset(['collectingMyJasaMedis']);


            //
        } catch (Exception $e) {
            // display an error to user
            dd($e->getMessage());
        }
        // goto start;
    }

    public function removeJasaMedis($rjpactDtl)
    {

        $this->checkRjStatus();


        // pengganti race condition
        // start:
        try {
            // remove into table transaksi
            DB::table('rstxn_rjactparams')
                ->where('pact_dtl', $rjpactDtl)
                ->delete();


            $JasaMedis = collect($this->dataDaftarPoliRJ['JasaMedis'])->where("rjpactDtl", '!=', $rjpactDtl)->toArray();
            $this->dataDaftarPoliRJ['JasaMedis'] = $JasaMedis;
            $this->store();


            //
        } catch (Exception $e) {
            // display an error to user
            dd($e->getMessage());
        }
        // goto start;


    }

    public function resetcollectingMyJasaMedis()
    {
        $this->reset(['collectingMyJasaMedis']);
    }

    public function checkRjStatus()
    {
        $lastInserted = DB::table('rstxn_rjhdrs')
            ->select('rj_status')
            ->where('rj_no', $this->rjNoRef)
            ->first();

        if ($lastInserted->rj_status !== 'A') {
            $this->emit('toastr-error', "Pasien Sudah Pulang, Trasaksi Terkunci.");
            return (dd('Pasien Sudah Pulang, Trasaksi Terkuncixx.'));
        }
    }


    // when new form instance
    public function mount()
    {
        $this->findData($this->rjNoRef);
        // set data dokter ref
    }



    // select data start////////////////
    public function render()
    {

        return view(
            'livewire.emr-r-j.administrasi-r-j.jasa-medis-r-j',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Data Pasien Rawat Jalan',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Jasa Medis',
            ]
        );
    }
    // select data end////////////////


}
