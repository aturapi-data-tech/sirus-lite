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


class JasaDokterRJ extends Component
{
    use WithPagination;


    // listener from blade////////////////
    protected $listeners = [
        'storeAssessmentDokterRJ' => 'store',
        'syncronizeAssessmentDokterRJFindData' => 'mount'
    ];


    //////////////////////////////
    // Ref on top bar
    //////////////////////////////
    public $rjNoRef = 472309;



    // dataDaftarPoliRJ RJ
    public array $dataDaftarPoliRJ = [];

    //////////////////////////////////////////////////////////////////////


    //  table LOV////////////////



    public $dataJasaDokterLov = [];
    public $dataJasaDokterLovStatus = 0;
    public $dataJasaDokterLovSearch = '';
    public $selecteddataJasaDokterLovIndex = 0;

    public $collectingMyJasaDokter = [];







    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////
    public function updated($propertyName)
    {
        // dd($propertyName);
        // $this->validateOnly($propertyName);
    }




    /////////////////////////////////////////////////
    // Lov dataJasaDokterLov //////////////////////
    ////////////////////////////////////////////////
    public function clickdataJasaDokterLov()
    {
        $this->dataJasaDokterLovStatus = true;
        $this->dataJasaDokterLov = [];
    }

    public function updateddataJasaDokterLovsearch()
    {

        // Reset index of LoV
        $this->reset(['selecteddataJasaDokterLovIndex', 'dataJasaDokterLov']);
        // Variable Search
        $search = $this->dataJasaDokterLovSearch;

        // check LOV by dr_id rs id 
        $dataJasaDokterLovs = DB::table('rsmst_accdocs  ')->select(
            'accdoc_id',
            'accdoc_desc',
            'accdoc_price'
        )
            ->where('accdoc_id', $search)
            ->where('active_status', '1')
            ->first();

        if ($dataJasaDokterLovs) {

            // set JasaDokter sep
            $this->addJasaDokter($dataJasaDokterLovs->accdoc_id, $dataJasaDokterLovs->accdoc_desc, $dataJasaDokterLovs->accdoc_price);
            $this->resetdataJasaDokterLov();
        } else {

            // if there is no id found and check (min 3 char on search)
            if (strlen($search) < 1) {
                $this->dataJasaDokterLov = [];
            } else {
                $this->dataJasaDokterLov = json_decode(
                    DB::table('rsmst_accdocs ')->select(
                        'accdoc_id',
                        'accdoc_desc',
                        'accdoc_price'
                    )
                        ->where('active_status', '1')
                        ->where(DB::raw('upper(accdoc_desc)'), 'like', '%' . strtoupper($search) . '%')
                        ->limit(10)
                        ->orderBy('accdoc_id', 'ASC')
                        ->orderBy('accdoc_desc', 'ASC')
                        ->get(),
                    true
                );
            }
            $this->dataJasaDokterLovStatus = true;
            // set doing nothing
        }
    }
    // /////////////////////
    // LOV selected start
    public function setMydataJasaDokterLov($id)
    {
        $this->checkRjStatus();
        $dataJasaDokterLovs = DB::table('rsmst_accdocs ')->select(
            'accdoc_id',
            'accdoc_desc',
            'accdoc_price'
        )
            ->where('active_status', '1')
            ->where('accdoc_id', $this->dataJasaDokterLov[$id]['accdoc_id'])
            ->first();

        // set dokter sep
        $this->addJasaDokter($dataJasaDokterLovs->accdoc_id, $dataJasaDokterLovs->accdoc_desc, $dataJasaDokterLovs->accdoc_price);
        $this->resetdataJasaDokterLov();
    }

    public function resetdataJasaDokterLov()
    {
        $this->reset(['dataJasaDokterLov', 'dataJasaDokterLovStatus', 'dataJasaDokterLovSearch', 'selecteddataJasaDokterLovIndex']);
    }

    public function selectNextdataJasaDokterLov()
    {
        if ($this->selecteddataJasaDokterLovIndex === "") {
            $this->selecteddataJasaDokterLovIndex = 0;
        } else {
            $this->selecteddataJasaDokterLovIndex++;
        }

        if ($this->selecteddataJasaDokterLovIndex === count($this->dataJasaDokterLov)) {
            $this->selecteddataJasaDokterLovIndex = 0;
        }
    }

    public function selectPreviousdataJasaDokterLov()
    {

        if ($this->selecteddataJasaDokterLovIndex === "") {
            $this->selecteddataJasaDokterLovIndex = count($this->dataJasaDokterLov) - 1;
        } else {
            $this->selecteddataJasaDokterLovIndex--;
        }

        if ($this->selecteddataJasaDokterLovIndex === -1) {
            $this->selecteddataJasaDokterLovIndex = count($this->dataJasaDokterLov) - 1;
        }
    }

    public function enterMydataJasaDokterLov($id)
    {
        $this->checkRjStatus();
        // jika JK belum siap maka toaster error
        if (isset($this->dataJasaDokterLov[$id]['accdoc_id'])) {
            $this->addJasaDokter($this->dataJasaDokterLov[$id]['accdoc_id'], $this->dataJasaDokterLov[$id]['accdoc_desc'], $this->dataJasaDokterLov[$id]['accdoc_price']);
            $this->resetdataJasaDokterLov();
        } else {
            $this->emit('toastr-error', "Jasa Dokter belum tersedia.");
        }
    }


    // LOV selected end
    /////////////////////////////////////////////////
    // Lov dataJasaDokterLov //////////////////////
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

        $this->emit('toastr-success', "Jasa Dokter berhasil disimpan.");
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

            // jika JasaDokter tidak ditemukan tambah variable JasaDokter pda array
            if (isset($this->dataDaftarPoliRJ['JasaDokter']) == false) {
                $this->dataDaftarPoliRJ['JasaDokter'] = [];
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


            // jika JasaDokter tidak ditemukan tambah variable JasaDokter pda array
            if (isset($this->dataDaftarPoliRJ['JasaDokter']) == false) {
                $this->dataDaftarPoliRJ['JasaDokter'] = [];
            }
        }
    }


    private function setDataPrimer(): void
    {
    }



    private function addJasaDokter($JasaDokterId, $JasaDokterDesc, $salesPrice): void
    {

        $this->collectingMyJasaDokter = [
            'JasaDokterId' => $JasaDokterId,
            'JasaDokterDesc' => $JasaDokterDesc,
            'JasaDokterPrice' => $salesPrice,
        ];
    }

    public function insertJasaDokter(): void
    {

        // validate
        // customErrorMessages
        $messages = customErrorMessagesTrait::messages();
        // require nik ketika pasien tidak dikenal
        $rules = [
            "collectingMyJasaDokter.JasaDokterId" => 'bail|required|exists:rsmst_accdocs ,accdoc_id',
            "collectingMyJasaDokter.JasaDokterDesc" => 'bail|required|',
            "collectingMyJasaDokter.JasaDokterPrice" => 'bail|required|numeric|',

        ];

        // Proses Validasi///////////////////////////////////////////
        $this->validate($rules, $messages);

        // validate


        // pengganti race condition
        // start:
        try {

            $lastInserted = DB::table('rstxn_rjaccdocs')
                ->select(DB::raw("nvl(max(rjhn_dtl)+1,1) as rjhn_dtl_max"))
                ->first();
            // insert into table transaksi
            DB::table('rstxn_rjaccdocs')
                ->insert([
                    'rjhn_dtl' => $lastInserted->rjhn_dtl_max,
                    'rj_no' => $this->rjNoRef,
                    'accdoc_id' => $this->collectingMyJasaDokter['JasaDokterId'],
                    'accdoc_price' => $this->collectingMyJasaDokter['JasaDokterPrice'],
                ]);


            $this->dataDaftarPoliRJ['JasaDokter'][] = [
                'JasaDokterId' => $this->collectingMyJasaDokter['JasaDokterId'],
                'JasaDokterDesc' => $this->collectingMyJasaDokter['JasaDokterDesc'],
                'JasaDokterPrice' => $this->collectingMyJasaDokter['JasaDokterPrice'],
                'rjaccdocDtl' => $lastInserted->rjhn_dtl_max,
                'rjNo' => $this->rjNoRef,
            ];

            $this->store();
            $this->reset(['collectingMyJasaDokter']);


            //
        } catch (Exception $e) {
            // display an error to user
            dd($e->getMessage());
        }
        // goto start;
    }

    public function removeJasaDokter($rjaccdocDtl)
    {

        $this->checkRjStatus();


        // pengganti race condition
        // start:
        try {
            // remove into table transaksi
            DB::table('rstxn_rjaccdocs')
                ->where('rjhn_dtl', $rjaccdocDtl)
                ->delete();


            $JasaDokter = collect($this->dataDaftarPoliRJ['JasaDokter'])->where("rjaccdocDtl", '!=', $rjaccdocDtl)->toArray();
            $this->dataDaftarPoliRJ['JasaDokter'] = $JasaDokter;
            $this->store();


            //
        } catch (Exception $e) {
            // display an error to user
            dd($e->getMessage());
        }
        // goto start;


    }

    public function resetcollectingMyJasaDokter()
    {
        $this->reset(['collectingMyJasaDokter']);
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
            'livewire.emr-r-j.administrasi-r-j.jasa-dokter-r-j',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Data Pasien Rawat Jalan',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Jasa Karyawan',
            ]
        );
    }
    // select data end////////////////


}
