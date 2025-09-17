<?php

namespace App\Http\Livewire\EmrRI\MrRI\Diagnosis;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;

use App\Http\Traits\customErrorMessagesTrait;
use App\Http\Traits\EmrRI\EmrRITrait;

use Spatie\ArrayToXml\ArrayToXml;
use Exception;


class Diagnosis extends Component
{
    use WithPagination, EmrRITrait;


    // listener from blade////////////////
    protected $listeners = [
        'syncronizeAssessmentPerawatRIFindData' => 'mount'
    ];

    //////////////////////////////
    // Ref on top bar
    //////////////////////////////
    public $riHdrNoRef;



    // dataDaftarRi RJ
    public array $dataDaftarRi = [];

    // data SKDP / kontrol=>[]
    public array $diagnosis = [];
    public array $procedure = [];
    //////////////////////////////////////////////////////////////////////


    //  table LOV////////////////

    public $dataDiagnosaICD10Lov = [];
    public $dataDiagnosaICD10LovStatus = 0;
    public $dataDiagnosaICD10LovSearch = '';
    public $selecteddataDiagnosaICD10LovIndex = 0;
    public $collectingMyDiagnosaICD10 = [];

    public $dataProcedureICD9CmLov = [];
    public $dataProcedureICD9CmLovStatus = 0;
    public $dataProcedureICD9CmLovSearch = '';
    public $selecteddataProcedureICD9CmLovIndex = 0;
    public $collectingMyProcedureICD9Cm = [];



    protected $rules = ["dataDaftarRi.diagnosis" => ""];



    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////

    public function updateddataDaftarRidiagnosisFreeText()
    {
        $this->store();
    }

    public function updateddataDaftarRiprocedureFreeText()
    {
        $this->store();
    }


    /////////////////////////////////////////////////
    // Lov dataDiagnosaICD10Lov //////////////////////
    ////////////////////////////////////////////////
    public function clickdataDiagnosaICD10Lov()
    {
        $this->dataDiagnosaICD10LovStatus = true;
        $this->dataDiagnosaICD10Lov = [];
    }

    public function updateddataDiagnosaICD10Lovsearch()
    {

        // Reset index of LoV
        $this->reset(['selecteddataDiagnosaICD10LovIndex', 'dataDiagnosaICD10Lov']);
        // Variable Search
        $search = $this->dataDiagnosaICD10LovSearch;

        // check LOV by dr_id rs id
        $dataDiagnosaICD10Lovs = DB::table('rsmst_mstdiags ')->select(
            'diag_id',
            'diag_desc',
            'icdx'
        )
            ->where('icdx', $search . 'xxx')
            // ->where('active_status', '1')
            ->first();

        if ($dataDiagnosaICD10Lovs) {

            // set DiagnosaICD10 sep
            $this->addDiagnosaICD10($dataDiagnosaICD10Lovs->diag_id, $dataDiagnosaICD10Lovs->diag_desc, $dataDiagnosaICD10Lovs->icdx);
            $this->resetdataDiagnosaICD10Lov();
        } else {

            // if there is no id found and check (min 3 char on search)
            if (strlen($search) < 1) {
                $this->dataDiagnosaICD10Lov = [];
            } else {
                $this->dataDiagnosaICD10Lov = json_decode(
                    DB::table('rsmst_mstdiags')->select(
                        'diag_id',
                        'diag_desc',
                        'icdx'
                    )
                        // ->where('active_status', '1')
                        ->Where(DB::raw('upper(diag_desc)'), 'like', '%' . strtoupper($search) . '%')
                        ->orWhere(DB::raw('upper(diag_id)'), 'like', '%' . strtoupper($search) . '%')
                        ->orWhere(DB::raw('upper(icdx)'), 'like', '%' . strtoupper($search) . '%')
                        ->limit(10)
                        ->orderBy('diag_id', 'ASC')
                        ->orderBy('diag_desc', 'ASC')
                        ->get(),
                    true
                );
            }
            $this->dataDiagnosaICD10LovStatus = true;
            // set doing nothing
        }
    }
    // /////////////////////
    // LOV selected start
    public function setMydataDiagnosaICD10Lov($id)
    {
        // $this->checkRjStatus();
        $dataDiagnosaICD10Lovs = DB::table('rsmst_mstdiags')->select(
            'diag_id',
            'diag_desc',
            'icdx'
        )
            // ->where('active_status', '1')
            ->where('diag_id', $this->dataDiagnosaICD10Lov[$id]['diag_id'])
            ->first();

        // set dokter sep
        $this->addDiagnosaICD10($dataDiagnosaICD10Lovs->diag_id, $dataDiagnosaICD10Lovs->diag_desc, $dataDiagnosaICD10Lovs->icdx);
        $this->resetdataDiagnosaICD10Lov();
    }

    public function resetdataDiagnosaICD10Lov()
    {
        $this->reset(['dataDiagnosaICD10Lov', 'dataDiagnosaICD10LovStatus', 'dataDiagnosaICD10LovSearch', 'selecteddataDiagnosaICD10LovIndex']);
    }

    public function selectNextdataDiagnosaICD10Lov()
    {
        if ($this->selecteddataDiagnosaICD10LovIndex === "") {
            $this->selecteddataDiagnosaICD10LovIndex = 0;
        } else {
            $this->selecteddataDiagnosaICD10LovIndex++;
        }

        if ($this->selecteddataDiagnosaICD10LovIndex === count($this->dataDiagnosaICD10Lov)) {
            $this->selecteddataDiagnosaICD10LovIndex = 0;
        }
    }

    public function selectPreviousdataDiagnosaICD10Lov()
    {

        if ($this->selecteddataDiagnosaICD10LovIndex === "") {
            $this->selecteddataDiagnosaICD10LovIndex = count($this->dataDiagnosaICD10Lov) - 1;
        } else {
            $this->selecteddataDiagnosaICD10LovIndex--;
        }

        if ($this->selecteddataDiagnosaICD10LovIndex === -1) {
            $this->selecteddataDiagnosaICD10LovIndex = count($this->dataDiagnosaICD10Lov) - 1;
        }
    }

    public function enterMydataDiagnosaICD10Lov($id)
    {
        // $this->checkRjStatus();
        // jika JK belum siap maka toaster error
        if (isset($this->dataDiagnosaICD10Lov[$id]['diag_id'])) {
            $this->addDiagnosaICD10($this->dataDiagnosaICD10Lov[$id]['diag_id'], $this->dataDiagnosaICD10Lov[$id]['diag_desc'], $this->dataDiagnosaICD10Lov[$id]['icdx']);
            $this->resetdataDiagnosaICD10Lov();
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Kode Diagnosa belum tersedia.");
        }
    }


    private function addDiagnosaICD10($DiagnosaICD10Id, $DiagnosaICD10Desc, $icdx): void
    {
        $this->collectingMyDiagnosaICD10 = [
            'DiagnosaICD10Id' => $DiagnosaICD10Id,
            'DiagnosaICD10Desc' => $DiagnosaICD10Desc,
            'DiagnosaICD10icdx' => $icdx,
        ];

        $this->insertDiagnosaICD10();
    }

    private function insertDiagnosaICD10(): void
    {

        // validate
        // $this->checkRjStatus();
        // customErrorMessages
        $messages = customErrorMessagesTrait::messages();
        // require nik ketika pasien tidak dikenal
        $rules = [
            "collectingMyDiagnosaICD10.DiagnosaICD10Id" => 'bail|required|exists:rsmst_mstdiags,diag_id',
            "collectingMyDiagnosaICD10.DiagnosaICD10Desc" => 'bail|required|',
            "collectingMyDiagnosaICD10.DiagnosaICD10icdx" => 'bail|required|',

        ];

        // Proses Validasi///////////////////////////////////////////
        $this->validate($rules, $messages);

        // validate


        // pengganti race condition
        // start:
        try {

            $lastInserted = DB::table('rstxn_ridtls')
                ->select(DB::raw("nvl(max(ridtl_dtl)+1,1) as ridtl_dtl_max"))
                ->first();
            // insert into table transaksi
            DB::table('rstxn_ridtls')
                ->insert([
                    'ridtl_dtl' => $lastInserted->ridtl_dtl_max,
                    'rihdr_no' => $this->riHdrNoRef,
                    'diag_id' => $this->collectingMyDiagnosaICD10['DiagnosaICD10Id'],
                ]);

            // update status diagnosa rstxn_rihdrs
            // DB::table('rstxn_rihdrs')
            //     ->where('rihdr_no',  $this->riHdrNoRef)
            //     ->update([
            //         'ri_diagnosa' => 'D',
            //     ]);

            $checkDiagnosaCount = collect($this->dataDaftarRi['diagnosis'])->count();
            $kategoriDiagnosa = $checkDiagnosaCount ? 'Secondary' : 'Primary';

            $this->dataDaftarRi['diagnosis'][] = [
                'diagId' => $this->collectingMyDiagnosaICD10['DiagnosaICD10Id'],
                'diagDesc' => $this->collectingMyDiagnosaICD10['DiagnosaICD10Desc'],
                'icdX' => $this->collectingMyDiagnosaICD10['DiagnosaICD10icdx'],
                'ketdiagnosa' => 'Keterangan Diagnosa',
                'kategoriDiagnosa' => $kategoriDiagnosa,
                'riDtlDtl' => $lastInserted->ridtl_dtl_max,
                'riHdrNo' => $this->riHdrNoRef,
            ];

            $this->store();
            $this->reset(['collectingMyDiagnosaICD10']);


            //
        } catch (Exception $e) {
            // display an error to user
            dd($e->getMessage());
        }
        // goto start;
    }

    public function removeDiagnosaICD10($riDtlDtl)
    {

        // $this->checkRjStatus();


        // pengganti race condition
        // start:
        try {


            // remove into table transaksi
            DB::table('rstxn_ridtls')
                ->where('ridtl_dtl', $riDtlDtl)
                ->delete();


            $DiagnosaICD10 = collect($this->dataDaftarRi['diagnosis'])->where("riDtlDtl", '!=', $riDtlDtl)->toArray();
            $this->dataDaftarRi['diagnosis'] = $DiagnosaICD10;


            $this->store();


            //
        } catch (Exception $e) {
            // display an error to user
            dd($e->getMessage());
        }
        // goto start;


    }
    // LOV selected end
    /////////////////////////////////////////////////
    // Lov dataDiagnosaICD10Lov //////////////////////
    ////////////////////////////////////////////////






    /////////////////////////////////////////////////
    // Lov dataProcedureICD9CmLov //////////////////////
    ////////////////////////////////////////////////
    public function clickdataProcedureICD9CmLov()
    {
        $this->dataProcedureICD9CmLovStatus = true;
        $this->dataProcedureICD9CmLov = [];
    }

    public function updateddataProcedureICD9CmLovsearch()
    {

        // Reset index of LoV
        $this->reset(['selecteddataProcedureICD9CmLovIndex', 'dataProcedureICD9CmLov']);
        // Variable Search
        $search = $this->dataProcedureICD9CmLovSearch;

        // check LOV by dr_id rs id
        $dataProcedureICD9CmLovs = DB::table('rsmst_mstprocedures ')->select(
            'proc_id',
            'proc_desc',

        )
            ->where('proc_id', $search . 'xxx')
            // ->where('active_status', '1')
            ->first();

        if ($dataProcedureICD9CmLovs) {

            // set ProcedureICD9Cm sep
            $this->addProcedureICD9Cm($dataProcedureICD9CmLovs->proc_id, $dataProcedureICD9CmLovs->proc_desc);
            $this->resetdataProcedureICD9CmLov();
        } else {

            // if there is no id found and check (min 3 char on search)
            if (strlen($search) < 1) {
                $this->dataProcedureICD9CmLov = [];
            } else {
                $this->dataProcedureICD9CmLov = json_decode(
                    DB::table('rsmst_mstprocedures')->select(
                        'proc_id',
                        'proc_desc',

                    )
                        // ->where('active_status', '1')
                        ->Where(DB::raw('upper(proc_desc)'), 'like', '%' . strtoupper($search) . '%')
                        ->orWhere(DB::raw('upper(proc_id)'), 'like', '%' . strtoupper($search) . '%')
                        ->limit(10)
                        ->orderBy('proc_id', 'ASC')
                        ->orderBy('proc_desc', 'ASC')
                        ->get(),
                    true
                );
            }
            $this->dataProcedureICD9CmLovStatus = true;
            // set doing nothing
        }
    }
    // /////////////////////
    // LOV selected start
    public function setMydataProcedureICD9CmLov($id)
    {
        // $this->checkRjStatus();
        $dataProcedureICD9CmLovs = DB::table('rsmst_mstprocedures')->select(
            'proc_id',
            'proc_desc',

        )
            // ->where('active_status', '1')
            ->where('proc_id', $this->dataProcedureICD9CmLov[$id]['proc_id'])
            ->first();

        // set dokter sep
        $this->addProcedureICD9Cm($dataProcedureICD9CmLovs->proc_id, $dataProcedureICD9CmLovs->proc_desc);
        $this->resetdataProcedureICD9CmLov();
    }

    public function resetdataProcedureICD9CmLov()
    {
        $this->reset(['dataProcedureICD9CmLov', 'dataProcedureICD9CmLovStatus', 'dataProcedureICD9CmLovSearch', 'selecteddataProcedureICD9CmLovIndex']);
    }

    public function selectNextdataProcedureICD9CmLov()
    {
        if ($this->selecteddataProcedureICD9CmLovIndex === "") {
            $this->selecteddataProcedureICD9CmLovIndex = 0;
        } else {
            $this->selecteddataProcedureICD9CmLovIndex++;
        }

        if ($this->selecteddataProcedureICD9CmLovIndex === count($this->dataProcedureICD9CmLov)) {
            $this->selecteddataProcedureICD9CmLovIndex = 0;
        }
    }

    public function selectPreviousdataProcedureICD9CmLov()
    {

        if ($this->selecteddataProcedureICD9CmLovIndex === "") {
            $this->selecteddataProcedureICD9CmLovIndex = count($this->dataProcedureICD9CmLov) - 1;
        } else {
            $this->selecteddataProcedureICD9CmLovIndex--;
        }

        if ($this->selecteddataProcedureICD9CmLovIndex === -1) {
            $this->selecteddataProcedureICD9CmLovIndex = count($this->dataProcedureICD9CmLov) - 1;
        }
    }

    public function enterMydataProcedureICD9CmLov($id)
    {
        // $this->checkRjStatus();
        // jika JK belum siap maka toaster error
        if (isset($this->dataProcedureICD9CmLov[$id]['proc_id'])) {
            $this->addProcedureICD9Cm($this->dataProcedureICD9CmLov[$id]['proc_id'], $this->dataProcedureICD9CmLov[$id]['proc_desc']);
            $this->resetdataProcedureICD9CmLov();
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Kode Diagnosa belum tersedia.");
        }
    }


    private function addProcedureICD9Cm($ProcedureICD9CmId, $ProcedureICD9CmDesc): void
    {
        $this->collectingMyProcedureICD9Cm = [
            'ProcedureICD9CmId' => $ProcedureICD9CmId,
            'ProcedureICD9CmDesc' => $ProcedureICD9CmDesc,
        ];

        $this->insertProcedureICD9Cm();
    }

    private function insertProcedureICD9Cm(): void
    {

        // validate
        // $this->checkRjStatus();
        // customErrorMessages
        $messages = customErrorMessagesTrait::messages();
        // require nik ketika pasien tidak dikenal
        $rules = [
            "collectingMyProcedureICD9Cm.ProcedureICD9CmId" => 'bail|required|exists:rsmst_mstprocedures,proc_id',
            "collectingMyProcedureICD9Cm.ProcedureICD9CmDesc" => 'bail|required|',
        ];

        // Proses Validasi///////////////////////////////////////////
        $this->validate($rules, $messages);

        // validate


        // pengganti race condition

        $this->dataDaftarRi['procedure'][] = [
            'procedureId' => $this->collectingMyProcedureICD9Cm['ProcedureICD9CmId'],
            'procedureDesc' => $this->collectingMyProcedureICD9Cm['ProcedureICD9CmDesc'],
            'ketProcedure' => 'Keterangan Procedure',
            'riHdrNo' => $this->riHdrNoRef,
        ];

        $this->store();
        $this->reset(['collectingMyProcedureICD9Cm']);


        //

        // goto start;
    }

    public function removeProcedureICD9Cm($procedureId)
    {

        // $this->checkRjStatus();

        $ProcedureICD9Cm = collect($this->dataDaftarRi['procedure'])->where("procedureId", '!=', $procedureId)->toArray();
        $this->dataDaftarRi['procedure'] = $ProcedureICD9Cm;
        $this->store();
    }
    // LOV selected end
    /////////////////////////////////////////////////
    // Lov dataProcedureICD9CmLov //////////////////////
    ////////////////////////////////////////////////




    // validate Data RJ//////////////////////////////////////////////////
    private function validateDataRi(): void
    {
        // customErrorMessages
        $messages = customErrorMessagesTrait::messages();

        // Proses Validasi///////////////////////////////////////////
        try {
            $this->validate($this->rules, $messages);
        } catch (\Illuminate\Validation\ValidationException $e) {

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Lakukan Pengecekan kembali Input Data.");
            $this->validate($this->rules, $messages);
        }
    }


    // insert and update record start////////////////
    public function store()
    {
        // set data RJno / NoBooking / NoAntrian / klaimId / kunjunganId
        $this->setDataPrimer();

        // Validate RJ
        $this->validateDataRi();

        // Logic update mode start //////////
        $this->updateDataRi($this->dataDaftarRi['riHdrNo']);

        $this->emit('syncronizeAssessmentPerawatRIFindData');
    }

    private function updateDataRi($riHdrNo): void
    {

        // update table trnsaksi
        // DB::table('rstxn_rihdrs')
        //     ->where('rihdr_no', $riHdrNo)
        //     ->update([
        //         'dataDaftarRi_json' => json_encode($this->dataDaftarRi, true),
        //         'dataDaftarRi_xml' => ArrayToXml::convert($this->dataDaftarRi),
        //     ]);

        $this->updateJsonRI($riHdrNo, $this->dataDaftarRi);

        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Diagnosa berhasil disimpan.");
    }
    // insert and update record end////////////////


    private function findData($riHdrNo): void
    {
        $this->dataDaftarRi = $this->findDataRI($riHdrNo);
        // dd($this->dataDaftarRi);
        // jika diagnosis tidak ditemukan tambah variable diagnosis pda array
        if (isset($this->dataDaftarRi['diagnosis']) == false) {
            $this->dataDaftarRi['diagnosis'] = $this->diagnosis;
        }
        // jika procedure tidak ditemukan tambah variable procedure pda array
        if (isset($this->dataDaftarRi['procedure']) == false) {
            $this->dataDaftarRi['procedure'] = $this->procedure;
        }

        // free text
        if (isset($this->dataDaftarRi['diagnosisFreeText']) == false) {
            $this->dataDaftarRi['diagnosisFreeText'] = '';
        }
        if (isset($this->dataDaftarRi['secondaryDiagnosisFreeText']) == false) {
            $this->dataDaftarRi['secondaryDiagnosisFreeText'] = '';
        }

        // jika procedure tidak ditemukan tambah variable procedure pda array
        if (isset($this->dataDaftarRi['procedureFreeText']) == false) {
            $this->dataDaftarRi['procedureFreeText'] = '';
        }
    }


    private function setDataPrimer(): void {}


    // when new form instance
    public function mount()
    {
        $this->findData($this->riHdrNoRef);
        // set data dokter ref
        // $this->store();
    }



    // select data start////////////////
    public function render()
    {

        return view(
            'livewire.emr-r-i.mr-r-i.diagnosis.diagnosis',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Data Pasien Rawat Jalan',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'ICD 10',
            ]
        );
    }
    // select data end////////////////


}
