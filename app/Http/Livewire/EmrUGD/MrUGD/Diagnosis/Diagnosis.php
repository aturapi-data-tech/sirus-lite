<?php

namespace App\Http\Livewire\EmrUGD\MrUGD\Diagnosis;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Validation\ValidationException;
use App\Http\Traits\customErrorMessagesTrait;
use App\Http\Traits\EmrUGD\EmrUGDTrait;


class Diagnosis extends Component
{
    use WithPagination, EmrUGDTrait;


    // listener from blade////////////////
    protected $listeners = ['emr:ugd:store' => 'store'];


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
    public $selecteddataDiagnosaICD10LovIndex = 0;
    public $collectingMyDiagnosaICD10 = [];

    public $dataProcedureICD9CmLov = [];
    public $dataProcedureICD9CmLovStatus = 0;
    public $dataProcedureICD9CmLovSearch = '';
    public $selecteddataProcedureICD9CmLovIndex = 0;
    public $collectingMyProcedureICD9Cm = [];



    protected $rules = ["dataDaftarUgd.diagnosis" => ""];



    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////


    /////////////////////////////////////////////////
    // Lov dataDiagnosaICD10Lov //////////////////////
    ////////////////////////////////////////////////
    public function clickdataDiagnosaICD10Lov()
    {
        $this->dataDiagnosaICD10LovStatus = true;
        $this->dataDiagnosaICD10Lov = [];
    }

    public function updatedDataDiagnosaICD10LovSearch()
    {

        // Reset index of LoV
        $this->reset(['selecteddataDiagnosaICD10LovIndex', 'dataDiagnosaICD10Lov']);
        // Variable Search
        $search = $this->dataDiagnosaICD10LovSearch;

        // check LOV by dr_id rs id
        $dataDiagnosaICD10Lovs = DB::table('rsmst_mstdiags')->select(
            'diag_id',
            'diag_desc',
            'icdx'
        )
            ->where('icdx', $search)
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
        $rules = [
            "collectingMyDiagnosaICD10.DiagnosaICD10Id"   => 'bail|required|exists:rsmst_mstdiags,diag_id',
            "collectingMyDiagnosaICD10.DiagnosaICD10Desc" => 'bail|required',
            "collectingMyDiagnosaICD10.DiagnosaICD10icdx" => 'bail|required',
        ];

        $attributes = [
            'collectingMyDiagnosaICD10.DiagnosaICD10Id'   => 'Kode ICD-10',
            'collectingMyDiagnosaICD10.DiagnosaICD10Desc' => 'Nama Diagnosa',
            'collectingMyDiagnosaICD10.DiagnosaICD10icdx' => 'Kode ICD-X',
        ];

        $messages = [
            'collectingMyDiagnosaICD10.DiagnosaICD10Id.required'   => 'Kode ICD-10 wajib diisi.',
            'collectingMyDiagnosaICD10.DiagnosaICD10Id.exists'     => 'Kode ICD-10 tidak terdaftar di master.',
            'collectingMyDiagnosaICD10.DiagnosaICD10Desc.required' => 'Nama diagnosa wajib diisi.',
            'collectingMyDiagnosaICD10.DiagnosaICD10icdx.required' => 'Kode ICD-X wajib diisi.',
        ];

        $this->validate(
            $rules,
            $messages,
            $attributes
        );


        $rjNo = $this->dataDaftarUgd['rjNo'] ?? $this->rjNoRef ?? null;
        if (!$rjNo) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("rjNo kosong.");
            return;
        }

        $lockKey = "ugd:{$rjNo}";

        Cache::lock($lockKey, 5)->block(3, function () use ($rjNo) {
            DB::transaction(function () use ($rjNo) {
                // hitung rjdtl_dtl berikutnya UNTUK rj_no INI
                $next = DB::table('rstxn_ugddtls')
                    ->max('rjdtl_dtl');
                $next = ($next ?? 0) + 1;

                DB::table('rstxn_ugddtls')->insert([
                    'rjdtl_dtl' => $next,
                    'rj_no'     => $rjNo,
                    'diag_id'   => $this->collectingMyDiagnosaICD10['DiagnosaICD10Id'],
                ]);

                DB::table('rstxn_ugdhdrs')->where('rj_no', $rjNo)
                    ->update(['rj_diagnosa' => 'D']);

                // PATCH json lokal → panggil store() agar konsisten
                $count = count($this->dataDaftarUgd['diagnosis'] ?? []);
                $kategori = $count ? 'Secondary' : 'Primary';

                $this->dataDaftarUgd['diagnosis'][] = [
                    'diagId'        => $this->collectingMyDiagnosaICD10['DiagnosaICD10Id'],
                    'diagDesc'      => $this->collectingMyDiagnosaICD10['DiagnosaICD10Desc'],
                    'icdX'          => $this->collectingMyDiagnosaICD10['DiagnosaICD10icdx'],
                    'ketdiagnosa'   => 'Keterangan Diagnosa',
                    'kategoriDiagnosa' => $kategori,
                    'rjDtlDtl'      => $next,
                    'rjNo'          => $rjNo,
                ];
            });
        });

        // simpan JSON besar lewat store() (store sudah pakai lock yang sama)
        $this->store();
        $this->reset(['collectingMyDiagnosaICD10']);
    }

    public function removeDiagnosaICD10($rjDtlDtl)
    {
        $rjNo = $this->dataDaftarUgd['rjNo'] ?? $this->rjNoRef ?? null;
        if (!$rjNo || !$rjDtlDtl) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Parameter tidak lengkap.');
            return;
        }

        $lockKey = "ugd:{$rjNo}";

        try {
            // 1) DB ops & patch state LOKAL di dalam lock
            Cache::lock($lockKey, 5)->block(3, function () use ($rjNo, $rjDtlDtl) {
                DB::transaction(function () use ($rjNo, $rjDtlDtl) {
                    $affected = DB::table('rstxn_ugddtls')
                        ->where('rj_no', $rjNo)
                        ->where('rjdtl_dtl', $rjDtlDtl)
                        ->delete();

                    // Patch state LOKAL diagnosis
                    $list = (array) ($this->dataDaftarUgd['diagnosis'] ?? []);
                    $list = collect($list)
                        ->reject(fn($d) => (string)($d['rjDtlDtl'] ?? '') === (string)$rjDtlDtl)
                        ->values()
                        ->all();

                    // Relabel primary/secondary
                    if (!empty($list)) {
                        $list[0]['kategoriDiagnosa'] = 'Primary';
                        for ($i = 1; $i < count($list); $i++) $list[$i]['kategoriDiagnosa'] = 'Secondary';
                    }

                    $this->dataDaftarUgd['diagnosis'] = $list;

                    // Update header ringan (opsional, boleh juga dibiarkan store() yg set)
                    DB::table('rstxn_ugdhdrs')
                        ->where('rj_no', $rjNo)
                        ->update(['rj_diagnosa' => count($list) ? 'D' : null]);

                    if ($affected === 0) {
                        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                            ->addInfo('Item diagnosa tidak ditemukan atau sudah dihapus.');
                    }
                });
            }); // lock dilepas ✅

            // 2) Commit JSON via store()
            $this->store();

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addSuccess('Diagnosa berhasil dihapus.');
        } catch (LockTimeoutException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Sistem sibuk, gagal memperoleh lock. Coba lagi.');
            return;
        } catch (\Throwable $e) {
            report($e);
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Gagal menghapus diagnosa.');
            return;
        }
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

    public function updatedDataProcedureICD9CmLovSearch()
    {

        // Reset index of LoV
        $this->reset(['selecteddataProcedureICD9CmLovIndex', 'dataProcedureICD9CmLov']);
        // Variable Search
        $search = $this->dataProcedureICD9CmLovSearch;

        // check LOV by dr_id rs id
        $dataProcedureICD9CmLovs = DB::table('rsmst_mstprocedures')->select(
            'proc_id',
            'proc_desc',

        )
            ->where('proc_id', $search)
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
        // Validasi
        $rules = [
            "collectingMyProcedureICD9Cm.ProcedureICD9CmId"   => 'bail|required|exists:rsmst_mstprocedures,proc_id',
            "collectingMyProcedureICD9Cm.ProcedureICD9CmDesc" => 'bail|required',
        ];
        $messages = customErrorMessagesTrait::messages();
        $this->validate($rules, $messages);

        $rjNo = $this->dataDaftarUgd['rjNo'] ?? $this->rjNoRef ?? null;
        if (!$rjNo) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("rjNo kosong.");
            return;
        }

        $lockKey = "ugd:{$rjNo}";

        // 1) Patch subtree 'procedure' berbasis FRESH state di dalam lock (tanpa update JSON)
        Cache::lock($lockKey, 5)->block(3, function () use ($rjNo) {
            // Ambil fresh
            $fresh = $this->findDataUGD($rjNo) ?: [];
            $list  = (array)($fresh['procedure'] ?? []);

            // Cegah duplikat (berdasar procedureId)
            $procId = $this->collectingMyProcedureICD9Cm['ProcedureICD9CmId'];
            $exists = collect($list)->firstWhere('procedureId', $procId);
            if (!$exists) {
                $list[] = [
                    'procedureId'   => $procId,
                    'procedureDesc' => $this->collectingMyProcedureICD9Cm['ProcedureICD9CmDesc'],
                    'ketProcedure'  => 'Keterangan Procedure',
                    'rjNo'          => $rjNo,
                ];
            } else {
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                    ->addInfo('Tindakan sudah ada, tidak ditambahkan ulang.');
            }

            // Tulis ke state lokal (bukan ke DB JSON)
            $this->dataDaftarUgd['procedure'] = array_values($list);
        }); // lock dilepas ✅

        // 2) Commit JSON via store()
        $this->store();
        $this->reset(['collectingMyProcedureICD9Cm']);
    }


    public function removeProcedureICD9Cm($procedureId)
    {
        $rjNo = $this->dataDaftarUgd['rjNo'] ?? $this->rjNoRef ?? null;
        if (!$rjNo || !$procedureId) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Parameter tidak lengkap.');
            return;
        }

        $lockKey = "ugd:{$rjNo}";

        try {
            // 1) Patch subtree 'procedure' berbasis FRESH state di dalam lock
            Cache::lock($lockKey, 5)->block(3, function () use ($rjNo, $procedureId) {
                DB::transaction(function () use ($rjNo, $procedureId) {
                    // Ambil FRESH JSON dari DB
                    $fresh = $this->findDataUGD($rjNo) ?: [];
                    $list  = (array)($fresh['procedure'] ?? []);

                    // Buang item yang dihapus
                    $list = collect($list)
                        ->reject(fn($p) => (string)($p['procedureId'] ?? '') === (string)$procedureId)
                        ->values()
                        ->all();

                    // Tulis ke state lokal (nanti di-commit via store())
                    $this->dataDaftarUgd['procedure'] = $list;

                    // (opsional) Kalau ada kolom status header terkait procedure, update di sini.
                    // DB::table('rstxn_ugdhdrs')->where('rj_no', $rjNo)->update([...]);
                });
            }); // lock dilepas ✅

            // 2) Commit JSON via store()
            $this->store();

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addSuccess('Tindakan berhasil dihapus.');
        } catch (LockTimeoutException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Sistem sibuk, gagal memperoleh lock. Coba lagi.');
            return;
        } catch (\Throwable $e) {
            report($e);
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Gagal menghapus tindakan.');
            return;
        }
    }


    // LOV selected end
    /////////////////////////////////////////////////
    // Lov dataProcedureICD9CmLov //////////////////////
    ////////////////////////////////////////////////




    // validate Data RJ//////////////////////////////////////////////////
    private function validateDataUgd(): void
    {
        // customErrorMessages
        $messages = customErrorMessagesTrait::messages();

        // Proses Validasi///////////////////////////////////////////
        try {
            $this->validate($this->rules, $messages);
        } catch (ValidationException $e) {

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Lakukan Pengecekan kembali Input Data.");
            $this->validate($this->rules, $messages);
        }
    }


    // insert and update record start////////////////
    public function store()
    {

        // Validasi (kalau rules-mu perlu)
        $this->validateDataUgd();

        // RJ number
        $rjNo = $this->dataDaftarUgd['rjNo'] ?? $this->rjNoRef ?? null;
        if (!$rjNo) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("rjNo kosong.");
            return;
        }

        $lockKey = "ugd:{$rjNo}"; // shared lock antar modul UGD

        try {
            Cache::lock($lockKey, 5)->block(3, function () use ($rjNo) {

                // 1) Ambil FRESH state dari DB di dalam lock
                $fresh = $this->findDataUGD($rjNo) ?: [];

                // 2) Pastikan struktur yang dikelola komponen ini ada
                if (!isset($fresh['diagnosis']) || !is_array($fresh['diagnosis'])) {
                    $fresh['diagnosis'] = [];
                }
                if (!isset($fresh['procedure']) || !is_array($fresh['procedure'])) {
                    $fresh['procedure'] = [];
                }
                if (!array_key_exists('diagnosisFreeText', $fresh)) {
                    $fresh['diagnosisFreeText'] = '';
                }
                if (!array_key_exists('procedureFreeText', $fresh)) {
                    $fresh['procedureFreeText'] = '';
                }

                // 3) PATCH hanya subtree milik Diagnosis (hindari menimpa bagian lain)
                $fresh['diagnosis']        = (array) ($this->dataDaftarUgd['diagnosis']        ?? []);
                $fresh['procedure']        = (array) ($this->dataDaftarUgd['procedure']        ?? []);
                $fresh['diagnosisFreeText'] = (string)($this->dataDaftarUgd['diagnosisFreeText'] ?? '');
                $fresh['procedureFreeText'] = (string)($this->dataDaftarUgd['procedureFreeText'] ?? '');

                // Opsional: status header rj_diagnosa
                $hasDiagnosis = count($fresh['diagnosis']) > 0;
                $rj_diagnosa  = $hasDiagnosis ? 'D' : null;

                // 4) Commit atomik dalam TRANSACTION (row lock agar konsisten)
                DB::transaction(function () use ($rjNo, $fresh, $rj_diagnosa) {
                    // kunci baris header
                    DB::table('rstxn_ugdhdrs')->where('rj_no', $rjNo)->first();

                    if (!is_null($rj_diagnosa)) {
                        DB::table('rstxn_ugdhdrs')
                            ->where('rj_no', $rjNo)
                            ->update(['rj_diagnosa' => $rj_diagnosa]);
                    } else {
                        // opsional: kosongkan kembali jika semua diagnosa dihapus
                        DB::table('rstxn_ugdhdrs')
                            ->where('rj_no', $rjNo)
                            ->update(['rj_diagnosa' => null]);
                    }

                    // simpan JSON UGD terkini
                    $this->updateJsonUGD($rjNo, $fresh);
                });

                // 5) Sinkronkan state komponen sendiri
                $this->dataDaftarUgd = $fresh;
            });
        } catch (LockTimeoutException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Sistem sibuk, gagal memperoleh lock. Coba lagi.');
            return;
        }

        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Diagnosa berhasil disimpan.");
    }


    private function findData($rjno): void
    {
        $this->dataDaftarUgd = $this->findDataUGD($rjno);
        // dd($this->dataDaftarUgd);
        // jika diagnosis tidak ditemukan tambah variable diagnosis pda array
        if (isset($this->dataDaftarUgd['diagnosis']) == false) {
            $this->dataDaftarUgd['diagnosis'] = $this->diagnosis;
        }
        // jika procedure tidak ditemukan tambah variable procedure pda array
        if (isset($this->dataDaftarUgd['procedure']) == false) {
            $this->dataDaftarUgd['procedure'] = $this->procedure;
        }

        // free text
        if (isset($this->dataDaftarUgd['diagnosisFreeText']) == false) {
            $this->dataDaftarUgd['diagnosisFreeText'] = '';
        }

        // jika procedure tidak ditemukan tambah variable procedure pda array
        if (isset($this->dataDaftarUgd['procedureFreeText']) == false) {
            $this->dataDaftarUgd['procedureFreeText'] = '';
        }
    }




    // when new form instance
    public function mount()
    {
        $this->findData($this->rjNoRef);
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
