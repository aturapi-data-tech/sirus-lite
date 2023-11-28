<?php

namespace App\Http\Livewire\EmrUGD\MrUGD\Diagnosis;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

use App\Http\Traits\customErrorMessagesTrait;

use Illuminate\Support\Str;
use Spatie\ArrayToXml\ArrayToXml;


class Diagnosis extends Component
{
    use WithPagination;


    // listener from blade////////////////
    protected $listeners = [];

    //////////////////////////////
    // Ref on top bar
    //////////////////////////////
    public $rjNoRef;



    // dataDaftarUgd RJ
    public array $dataDaftarUgd = [];

    // data SKDP / kontrol=>[] 
    public array $diagnosis = [];
    public array $procedure = [];
    //////////////////////////////////////////////////////////////////////


    //  table LOV////////////////

    public $dataDiagnosaICD10Lov = [];
    public $dataDiagnosaICD10LovStatus = 0;
    public $dataDiagnosaICD10LovSearch = '';

    public $dataProcedureICD9CmLov = [];
    public $dataProcedureICD9CmLovStatus = 0;
    public $dataProcedureICD9CmLovSearch = '';






    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////



    // ////////////////
    // RJ Logic
    // ////////////////

    //////////////////////////////////////////////
    // updated when change Record ////////////////
    ///////////////////////////////////////////////



    //////////////////////////////////////////////
    // updated when change Record ////////////////
    ///////////////////////////////////////////////

    /////////////////////////////////////////////////
    // Lov dataDiagnosaICD10SEP //////////////////////
    ////////////////////////////////////////////////
    public function clickdataDiagnosaICD10lov()
    {
        $this->dataDiagnosaICD10LovStatus = true;
        $this->dataDiagnosaICD10Lov = [];
    }

    public function updateddataDiagnosaICD10lovsearch()
    {
        // Variable Search
        $search = $this->dataDiagnosaICD10LovSearch;

        // check LOV by dr_id rs id 
        $dataDiagnosaICD10 = DB::table('rsmst_mstdiags')->select(
            'diag_id',
            'diag_desc',
            'icdx',
        )
            ->where('diag_id', $search)
            ->first();

        if ($dataDiagnosaICD10) {

            // set dokter sep
            $this->addDiagICD10($dataDiagnosaICD10->diag_id, $dataDiagnosaICD10->diag_desc, $dataDiagnosaICD10->icdx);


            $this->dataDiagnosaICD10LovStatus = false;
            $this->dataDiagnosaICD10LovSearch = '';
        } else {
            // if there is no id found and check (min 3 char on search)
            if (strlen($search) < 3) {
                $this->dataDiagnosaICD10Lov = [];
            } else {
                $this->dataDiagnosaICD10Lov = json_decode(
                    DB::table('rsmst_mstdiags')->select(
                        'diag_id',
                        'diag_desc',
                        'icdx',
                    )

                        ->Where(DB::raw('upper(diag_desc)'), 'like', '%' . strtoupper($search) . '%')
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
    public function setMydataDiagnosaICD10Lov($id, $name)
    {
        $dataDiagnosaICD10 = DB::table('rsmst_mstdiags')->select(
            'diag_id',
            'diag_desc',
            'icdx',
        )
            ->where('diag_id', $id)
            ->first();

        // set dokter sep
        $this->addDiagICD10($dataDiagnosaICD10->diag_id, $dataDiagnosaICD10->diag_desc, $dataDiagnosaICD10->icdx);



        $this->dataDiagnosaICD10LovStatus = false;
        $this->dataDiagnosaICD10LovSearch = '';
    }
    // LOV selected end
    /////////////////////////////////////////////////
    // Lov dataDiagnosaRJ //////////////////////
    ////////////////////////////////////////////////







    /////////////////////////////////////////////////
    // Lov dataProcedureICD9CmSEP //////////////////////
    ////////////////////////////////////////////////
    public function clickdataProcedureICD9Cmlov()
    {
        $this->dataProcedureICD9CmLovStatus = true;
        $this->dataProcedureICD9CmLov = [];
    }

    public function updateddataProcedureICD9Cmlovsearch()
    {
        // Variable Search
        $search = $this->dataProcedureICD9CmLovSearch;

        // check LOV by dr_id rs id 
        $dataProcedureICD9Cm = DB::table('rsmst_mstprocedures')->select(
            'proc_id',
            'proc_desc',
        )
            ->where('proc_id', $search)
            ->first();

        if ($dataProcedureICD9Cm) {

            // set dokter sep
            $this->addProcedureICD9Cm($dataProcedureICD9Cm->proc_id, $dataProcedureICD9Cm->proc_desc);


            $this->dataProcedureICD9CmLovStatus = false;
            $this->dataProcedureICD9CmLovSearch = '';
        } else {
            // if there is no id found and check (min 3 char on search)
            if (strlen($search) < 3) {
                $this->dataProcedureICD9CmLov = [];
            } else {
                $this->dataProcedureICD9CmLov = json_decode(
                    DB::table('rsmst_mstprocedures')->select(
                        'proc_id',
                        'proc_desc',
                    )

                        ->Where(DB::raw('upper(proc_desc)'), 'like', '%' . strtoupper($search) . '%')
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
    public function setMydataProcedureICD9CmLov($id, $name)
    {
        $dataProcedureICD9Cm = DB::table('rsmst_mstprocedures')->select(
            'proc_id',
            'proc_desc',
        )
            ->where('proc_id', $id)
            ->first();

        // set dokter sep
        $this->addProcedureICD9Cm($dataProcedureICD9Cm->proc_id, $dataProcedureICD9Cm->proc_desc);



        $this->dataProcedureICD9CmLovStatus = false;
        $this->dataProcedureICD9CmLovSearch = '';
    }
    // LOV selected end
    /////////////////////////////////////////////////
    // Lov dataDiagnosaRJ //////////////////////
    ////////////////////////////////////////////////



    // validate Data RJ//////////////////////////////////////////////////
    private function validateDataRJ(): void
    {
        // customErrorMessages
        $messages = customErrorMessagesTrait::messages();

        // require nik ketika pasien tidak dikenal



        $rules = [
            "dataDaftarUgd.diagnosis" => "",
        ];



        // Proses Validasi///////////////////////////////////////////
        try {
            $this->validate($rules, $messages);
        } catch (\Illuminate\Validation\ValidationException $e) {

            $this->emit('toastr-error', "Lakukan Pengecekan kembali Input Data.");
            $this->validate($rules, $messages);
        }
    }


    // insert and update record start////////////////
    public function store()
    {
        // set data RJno / NoBooking / NoAntrian / klaimId / kunjunganId
        $this->setDataPrimer();

        // Validate RJ
        $this->validateDataRJ();

        // Logic update mode start //////////
        $this->updateDataRJ($this->dataDaftarUgd['rjNo']);
    }

    private function updateDataRJ($rjNo): void
    {

        // update table trnsaksi
        DB::table('rstxn_ugdhdrs')
            ->where('rj_no', $rjNo)
            ->update([
                'dataDaftarUgd_json' => json_encode($this->dataDaftarUgd, true),
                'dataDaftarUgd_xml' => ArrayToXml::convert($this->dataDaftarUgd),
            ]);

        $this->emit('toastr-success', "Diagnosa berhasil disimpan.");
    }
    // insert and update record end////////////////


    private function findData($rjno): void
    {


        $findData = DB::table('rsview_ugdkasir')
            ->select('datadaftarugd_json', 'vno_sep')
            ->where('rj_no', $rjno)
            ->first();

        $datadaftarugd_json = isset($findData->datadaftarugd_json) ? $findData->datadaftarugd_json : null;
        // if meta_data_pasien_json = null
        // then cari Data Pasien By Key Collection (exception when no data found)
        // 
        // else json_decode
        if ($datadaftarugd_json) {
            $this->dataDaftarUgd = json_decode($findData->datadaftarugd_json, true);

            // jika diagnosis tidak ditemukan tambah variable diagnosis pda array
            if (isset($this->dataDaftarUgd['diagnosis']) == false) {
                $this->dataDaftarUgd['diagnosis'] = $this->diagnosis;
            }

            // jika procedure tidak ditemukan tambah variable procedure pda array
            if (isset($this->dataDaftarUgd['procedure']) == false) {
                $this->dataDaftarUgd['procedure'] = $this->procedure;
            }
        } else {

            $this->emit('toastr-error', "Data tidak dapat di proses json.");
            $dataDaftarUgd = DB::table('rsview_ugdkasir')
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
                    // 'poli_desc',
                    'dr_id',
                    'dr_name',
                    'klaim_id',
                    'entry_id',
                    'shift',
                    'vno_sep',
                    'no_antrian',

                    'nobooking',
                    'push_antrian_bpjs_status',
                    'push_antrian_bpjs_json',
                    // 'kd_dr_bpjs',
                    // 'kd_poli_bpjs',
                    'rj_status',
                    'txn_status',
                    'erm_status',
                )
                ->where('rj_no', '=', $rjno)
                ->first();

            $this->dataDaftarUgd = [
                "regNo" => "" . $dataDaftarUgd->reg_no . "",

                "drId" => "" . $dataDaftarUgd->dr_id . "",
                "drDesc" => "" . $dataDaftarUgd->dr_name . "",

                "poliId" => "" . $dataDaftarUgd->poli_id . "",
                // "poliDesc" => "" . $dataDaftarUgd->poli_desc . "",

                // "kddrbpjs" => "" . $dataDaftarUgd->kd_dr_bpjs . "",
                // "kdpolibpjs" => "" . $dataDaftarUgd->kd_poli_bpjs . "",

                "rjDate" => "" . $dataDaftarUgd->rj_date . "",
                "rjNo" => "" . $dataDaftarUgd->rj_no . "",
                "shift" => "" . $dataDaftarUgd->shift . "",
                "noAntrian" => "" . $dataDaftarUgd->no_antrian . "",
                "noBooking" => "" . $dataDaftarUgd->nobooking . "",
                "slCodeFrom" => "02",
                "passStatus" => "",
                "rjStatus" => "" . $dataDaftarUgd->rj_status . "",
                "txnStatus" => "" . $dataDaftarUgd->txn_status . "",
                "ermStatus" => "" . $dataDaftarUgd->erm_status . "",
                "cekLab" => "0",
                "kunjunganInternalStatus" => "0",
                "noReferensi" => "" . $dataDaftarUgd->reg_no . "",
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
                    "taskId3" => "" . $dataDaftarUgd->rj_date . "",
                    "taskId4" => "",
                    "taskId5" => "",
                    "taskId6" => "",
                    "taskId7" => "",
                    "taskId99" => "",
                ],
                'sep' => [
                    "noSep" => "" . $dataDaftarUgd->vno_sep . "",
                    "reqSep" => [],
                    "resSep" => [],
                ]
            ];


            // jika diagnosis tidak ditemukan tambah variable diagnosis pda array
            if (isset($this->dataDaftarUgd['diagnosis']) == false) {
                $this->dataDaftarUgd['diagnosis'] = $this->diagnosis;
            }
            // jika procedure tidak ditemukan tambah variable procedure pda array
            if (isset($this->dataDaftarUgd['procedure']) == false) {
                $this->dataDaftarUgd['procedure'] = $this->procedure;
            }
        }
    }


    private function setDataPrimer(): void
    {
    }

    private function addDiagICD10($diagId, $diagDesc, $icdX): void
    {
        $checkDiagnosaCount = collect($this->dataDaftarUgd['diagnosis'])->count();
        $kategoriDiagnosa = $checkDiagnosaCount ? 'Secondary' : 'Primary';

        $this->dataDaftarUgd['diagnosis'][] = ['diagId' => $diagId, 'diagDesc' => $diagDesc, 'icdX' => $icdX, 'ketdiagnosa' => 'Keterangan Diagnosa', 'kategoriDiagnosa' => $kategoriDiagnosa];
    }

    public function removeDiagICD10($diagId)
    {

        $diagnosis = collect($this->dataDaftarUgd['diagnosis'])->where("diagId", '!=', $diagId)->toArray();
        $this->dataDaftarUgd['diagnosis'] = $diagnosis;
    }





    private function addProcedureICD9Cm($procedureId, $procedureDesc): void
    {
        $checkProcedurenosaCount = collect($this->dataDaftarUgd['procedure'])->count();

        $this->dataDaftarUgd['procedure'][] = ['procedureId' => $procedureId, 'procedureDesc' => $procedureDesc, 'ketProcedure' => 'Keterangan Procedure'];
    }

    public function removeProcedureICD9Cm($procedureId)
    {

        $procedure = collect($this->dataDaftarUgd['procedure'])->where("procedureId", '!=', $procedureId)->toArray();
        $this->dataDaftarUgd['procedure'] = $procedure;
    }




    // when new form instance
    public function mount()
    {
        $this->findData($this->rjNoRef);
        // set data dokter ref
        // $this->store();
    }



    // select data start////////////////
    public function render()
    {

        return view(
            'livewire.emr-u-g-d.mr-u-g-d.diagnosis.diagnosis',
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
