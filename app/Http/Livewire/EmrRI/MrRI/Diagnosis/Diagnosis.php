<?php

namespace App\Http\Livewire\EmrRI\MrRI\Diagnosis;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;

use App\Http\Traits\EmrRI\EmrRITrait;
use Illuminate\Support\Facades\Cache;


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

    public function updatedDataDaftarRiDiagnosisFreeText()
    {
        $this->updateRiJsonFields(['diagnosisFreeText' => (string) ($this->dataDaftarRi['diagnosisFreeText'] ?? '')]);
    }

    /*************  ✨ Windsurf Command ⭐  *************/
    /**
     * Update field procedureFreeText in dataDaftarRi.
     *
     * @return void
     */
    /*******  b2eb9f16-7710-484a-b61a-eae895749420  *******/
    public function updatedDataDaftarRiProcedureFreeText()
    {
        $this->updateRiJsonFields(['procedureFreeText' => (string) ($this->dataDaftarRi['procedureFreeText'] ?? '')]);
    }

    // 2) Helper umum untuk update sebagian field JSON, aman dari race
    private function updateRiJsonFields(array $patch): void
    {
        $riHdrNo = $this->riHdrNoRef ?? ($this->dataDaftarRi['riHdrNo'] ?? null);
        if (!$riHdrNo) {
            toastr()->positionClass('toast-top-left')->addError('riHdrNo kosong.');
            return;
        }

        // (Opsional) validasi ringan untuk free-text
        $rules = [
            'diagnosisFreeText'  => 'nullable|string|max:2000',
            'procedureFreeText'  => 'nullable|string|max:2000',
        ];
        // Ambil subset yang ingin divalidasi saja
        $toValidate = array_intersect_key($patch, $rules);
        if (!empty($toValidate)) {
            $this->validate(
                array_intersect_key($rules, $toValidate),
                [], // pakai default / custom messages-mu
                ['diagnosisFreeText' => 'Catatan Diagnosis', 'procedureFreeText' => 'Catatan Prosedur']
            );
        }

        try {
            // Pakai lock yang sama supaya semua penulisan JSON berbaris
            $this->withDiagnosisLock($riHdrNo, function () use ($riHdrNo, $patch) {
                // Refresh data terbaru (hindari lost update)
                $fresh = $this->findDataRI($riHdrNo) ?: [];
                $fresh['riHdrNo'] = $fresh['riHdrNo'] ?? $riHdrNo;

                // Pastikan key ada
                $fresh['diagnosisFreeText'] = $fresh['diagnosisFreeText'] ?? '';
                $fresh['procedureFreeText'] = $fresh['procedureFreeText'] ?? '';

                // Merge minimal fields yang diubah
                foreach ($patch as $k => $v) {
                    $fresh[$k] = $v;
                }

                // Persist atomik
                $this->updateJsonRI($riHdrNo, $fresh);

                // Sinkron state komponen
                $this->dataDaftarRi = $fresh;
            });

            // Tidak perlu store() / toastr spam
        } catch (\Throwable $e) {
            toastr()->positionClass('toast-top-left')->addError('Gagal menyimpan catatan: ' . $e->getMessage());
        }
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
        $dataDiagnosaICD10Lovs = DB::table('rsmst_mstdiags')->select(
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
                        ->limit(15)
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
        // 1) Field-level validation (di luar lock)
        $rules = [
            'collectingMyDiagnosaICD10.DiagnosaICD10Id'   => 'bail|required|exists:rsmst_mstdiags,diag_id',
            'collectingMyDiagnosaICD10.DiagnosaICD10Desc' => 'bail|required|string|max:255',
            'collectingMyDiagnosaICD10.DiagnosaICD10icdx' => 'bail|required|string|max:20',
        ];

        $messages = [
            'collectingMyDiagnosaICD10.DiagnosaICD10Id.required' => 'Kode diagnosis wajib diisi.',
            'collectingMyDiagnosaICD10.DiagnosaICD10Id.exists'   => 'Kode diagnosis tidak ditemukan di master ICD10.',

            'collectingMyDiagnosaICD10.DiagnosaICD10Desc.required' => 'Deskripsi diagnosis wajib diisi.',
            'collectingMyDiagnosaICD10.DiagnosaICD10Desc.string'   => 'Deskripsi diagnosis tidak valid.',
            'collectingMyDiagnosaICD10.DiagnosaICD10Desc.max'      => 'Deskripsi diagnosis maksimal 255 karakter.',

            'collectingMyDiagnosaICD10.DiagnosaICD10icdx.required' => 'Kode ICD-X wajib diisi.',
            'collectingMyDiagnosaICD10.DiagnosaICD10icdx.string'   => 'Kode ICD-X tidak valid.',
            'collectingMyDiagnosaICD10.DiagnosaICD10icdx.max'      => 'Kode ICD-X maksimal 20 karakter.',
        ];

        $attributes = [
            'collectingMyDiagnosaICD10.DiagnosaICD10Id'   => 'Kode diagnosis',
            'collectingMyDiagnosaICD10.DiagnosaICD10Desc' => 'Deskripsi diagnosis',
            'collectingMyDiagnosaICD10.DiagnosaICD10icdx' => 'Kode ICD-X',
        ];

        $this->validate($rules, $messages, $attributes);

        try {
            $riHdrNo = $this->riHdrNoRef;

            $this->withDiagnosisLock($riHdrNo, function () use ($riHdrNo) {
                // 2) Business rules yang perlu konsistensi DB (di dalam lock)

                // a) Cegah duplikat diagnosis dengan diag_id yang sama pada header yang sama
                $dup = DB::table('rstxn_ridtls')
                    ->where('rihdr_no', $riHdrNo)
                    ->where('diag_id', $this->collectingMyDiagnosaICD10['DiagnosaICD10Id'])
                    ->lockForUpdate()
                    ->exists();

                if ($dup) {
                    throw new \RuntimeException('Diagnosis sudah tercatat untuk pasien/riHdrNo ini.');
                }

                // b) Aturan hanya 1 "Primary" (opsional)
                //   kalau kamu set otomatis Primary saat pertama kali, cek dulu apakah sudah ada primary
                $existingJson = $this->findDataRI($riHdrNo) ?: [];
                $existingJson['diagnosis'] = $existingJson['diagnosis'] ?? [];
                $sudahAdaPrimary = collect($existingJson['diagnosis'])
                    ->contains(fn($d) => ($d['kategoriDiagnosa'] ?? '') === 'Primary');

                $isSecondary = count($existingJson['diagnosis']) > 0 || $sudahAdaPrimary; // jika sudah ada primary/entry, set Secondary

                // c) Dapatkan next ridtl_dtl secara aman
                $next = DB::table('rstxn_ridtls')
                    ->max('ridtl_dtl');

                $nextDtl = is_null($next) ? 1 : $next + 1;

                // d) Insert detail row
                DB::table('rstxn_ridtls')->insert([
                    'rihdr_no'  => $riHdrNo,
                    'ridtl_dtl' => $nextDtl,
                    'diag_id'   => $this->collectingMyDiagnosaICD10['DiagnosaICD10Id'],
                ]);

                // e) Mutasi JSON di memori + persist dalam transaksi
                $newRow = [
                    'diagId'           => $this->collectingMyDiagnosaICD10['DiagnosaICD10Id'],
                    'diagDesc'         => $this->collectingMyDiagnosaICD10['DiagnosaICD10Desc'],
                    'icdX'             => $this->collectingMyDiagnosaICD10['DiagnosaICD10icdx'],
                    'ketdiagnosa'      => 'Keterangan Diagnosa',
                    'kategoriDiagnosa' => $isSecondary ? 'Secondary' : 'Primary',
                    'riDtlDtl'         => $nextDtl,
                    'riHdrNo'          => $riHdrNo,
                ];

                $existingJson['diagnosis'][] = $newRow;

                // Simpan JSON atomik
                $this->updateJsonRI($riHdrNo, $existingJson);
                $this->dataDaftarRi = $existingJson;
            });

            $this->reset(['collectingMyDiagnosaICD10']);
            toastr()->positionClass('toast-top-left')->addSuccess('Diagnosis berhasil ditambahkan.');
        } catch (\Throwable $e) {
            toastr()->positionClass('toast-top-left')->addError('Gagal menambah diagnosis: ' . $e->getMessage());
        }
    }

    public function removeDiagnosaICD10($riDtlDtl)
    {
        try {
            $riHdrNo = $this->riHdrNoRef ?? ($this->dataDaftarRi['riHdrNo'] ?? null);
            if (!$riHdrNo) {
                toastr()->positionClass('toast-top-left')->addError('riHdrNo kosong.');
                return;
            }

            $riDtlDtl = (int) $riDtlDtl;

            $this->withDiagnosisLock($riHdrNo, function () use ($riHdrNo, $riDtlDtl) {
                // Muat JSON TERBARU agar tidak overwrite perubahan pihak lain
                $fresh = $this->findDataRI($riHdrNo) ?: [];
                $fresh['riHdrNo']    = $fresh['riHdrNo'] ?? $riHdrNo;
                $fresh['diagnosis']  = isset($fresh['diagnosis']) && is_array($fresh['diagnosis']) ? $fresh['diagnosis'] : [];

                // Cari item yang dihapus di JSON
                $idxToRemove = null;
                $deletedWasPrimary = false;
                foreach ($fresh['diagnosis'] as $i => $row) {
                    if ((int)($row['riDtlDtl'] ?? 0) === $riDtlDtl) {
                        $idxToRemove = $i;
                        $deletedWasPrimary = (($row['kategoriDiagnosa'] ?? '') === 'Primary');
                        break;
                    }
                }
                if ($idxToRemove === null) {
                    throw new \RuntimeException('Diagnosis tidak ditemukan di data.');
                }

                // Hapus di tabel detail (cek affected rows)
                $affected = DB::table('rstxn_ridtls')
                    ->where('rihdr_no', $riHdrNo)
                    ->where('ridtl_dtl', $riDtlDtl)
                    ->delete();

                if ($affected === 0) {
                    // Jika DB tidak punya, anggap sudah dihapus oleh proses lain
                    // Sinkronkan saja JSON lokal dengan DB (tanpa throw)
                }

                // Hapus di JSON
                array_splice($fresh['diagnosis'], $idxToRemove, 1);
                $fresh['diagnosis'] = array_values($fresh['diagnosis']); // reindex

                // (Opsional) Promosikan Primary baru bila yang dihapus Primary
                if ($deletedWasPrimary && count($fresh['diagnosis']) > 0) {
                    // reset semua ke Secondary dulu
                    foreach ($fresh['diagnosis'] as &$d) {
                        $d['kategoriDiagnosa'] = 'Secondary';
                    }
                    unset($d);
                    // pilih satu jadi Primary (strategi: ambil yang paling awal)
                    $fresh['diagnosis'][0]['kategoriDiagnosa'] = 'Primary';
                }

                // Persist JSON atomik
                $this->updateJsonRI($riHdrNo, $fresh);

                // sinkronkan state komponen
                $this->dataDaftarRi = $fresh;
            });

            $this->emit('syncronizeAssessmentPerawatRIFindData');
            toastr()->positionClass('toast-top-left')->addSuccess('Diagnosis berhasil dihapus.');
        } catch (\Throwable $e) {
            toastr()->positionClass('toast-top-left')->addError('Gagal menghapus diagnosis: ' . $e->getMessage());
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

    public function updateddataProcedureICD9CmLovsearch()
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
                        ->limit(15)
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
        // 1) Field-level validation
        $rules = [
            'collectingMyProcedureICD9Cm.ProcedureICD9CmId'   => 'bail|required|exists:rsmst_mstprocedures,proc_id',
            'collectingMyProcedureICD9Cm.ProcedureICD9CmDesc' => 'bail|required|string|max:255',
        ];
        $messages = [
            'collectingMyProcedureICD9Cm.ProcedureICD9CmId.required'   => 'Kode prosedur wajib diisi.',
            'collectingMyProcedureICD9Cm.ProcedureICD9CmId.exists'     => 'Kode prosedur tidak ditemukan di master ICD-9-CM.',
            'collectingMyProcedureICD9Cm.ProcedureICD9CmDesc.required' => 'Deskripsi prosedur wajib diisi.',
            'collectingMyProcedureICD9Cm.ProcedureICD9CmDesc.string'   => 'Deskripsi prosedur tidak valid.',
            'collectingMyProcedureICD9Cm.ProcedureICD9CmDesc.max'      => 'Deskripsi prosedur maksimal 255 karakter.',
        ];
        $attributes = [
            'collectingMyProcedureICD9Cm.ProcedureICD9CmId'   => 'Kode prosedur',
            'collectingMyProcedureICD9Cm.ProcedureICD9CmDesc' => 'Deskripsi prosedur',
        ];
        $this->validate($rules, $messages, $attributes);

        // Normalisasi ringan (opsional)
        $this->collectingMyProcedureICD9Cm['ProcedureICD9CmDesc'] =
            trim((string)($this->collectingMyProcedureICD9Cm['ProcedureICD9CmDesc'] ?? ''));

        $riHdrNo = $this->riHdrNoRef ?? ($this->dataDaftarRi['riHdrNo'] ?? null);
        if (!$riHdrNo) {
            toastr()->positionClass('toast-top-left')->addError('riHdrNo kosong.');
            return;
        }

        try {
            // Pakai lock yang sama dengan diagnosis supaya semua penulisan JSON per header berbaris
            $this->withDiagnosisLock($riHdrNo, function () use ($riHdrNo) {
                // Ambil JSON terbaru agar tidak overwrite perubahan pihak lain
                $fresh = $this->findDataRI($riHdrNo) ?: [];
                $fresh['riHdrNo']   = $fresh['riHdrNo'] ?? $riHdrNo;
                $fresh['procedure'] = isset($fresh['procedure']) && is_array($fresh['procedure']) ? $fresh['procedure'] : [];

                $procId  = (string)$this->collectingMyProcedureICD9Cm['ProcedureICD9CmId'];
                $procDesc = (string)$this->collectingMyProcedureICD9Cm['ProcedureICD9CmDesc'];

                // Cek duplikat procedureId (opsional, tapi umumnya diinginkan)
                $dup = collect($fresh['procedure'])->contains(fn($p) => ($p['procedureId'] ?? null) === $procId);
                if ($dup) {
                    throw new \RuntimeException('Prosedur sudah tercatat untuk pasien/riHdrNo ini.');
                }


                // Tambahkan ke JSON
                $fresh['procedure'][] = [
                    'procedureId'   => $procId,
                    'procedureDesc' => $procDesc,
                    'ketProcedure'  => 'Keterangan Procedure',
                    'riHdrNo'       => $riHdrNo,
                ];

                // Simpan atomik & sinkronkan state komponen
                $this->updateJsonRI($riHdrNo, $fresh);
                $this->dataDaftarRi = $fresh;
            });

            $this->reset(['collectingMyProcedureICD9Cm']);
            toastr()->positionClass('toast-top-left')->addSuccess('Prosedur berhasil ditambahkan.');
        } catch (\Throwable $e) {
            toastr()->positionClass('toast-top-left')->addError('Gagal menambah prosedur: ' . $e->getMessage());
        }
    }

    public function removeProcedureICD9Cm($procedureId)
    {
        $riHdrNo = $this->riHdrNoRef ?? ($this->dataDaftarRi['riHdrNo'] ?? null);
        if (!$riHdrNo) {
            toastr()->positionClass('toast-top-left')->addError('riHdrNo kosong.');
            return;
        }

        try {
            $procId = (string)$procedureId;

            $this->withDiagnosisLock($riHdrNo, function () use ($riHdrNo, $procId) {
                // Muat data terbaru
                $fresh = $this->findDataRI($riHdrNo) ?: [];
                $fresh['riHdrNo']   = $fresh['riHdrNo'] ?? $riHdrNo;
                $fresh['procedure'] = isset($fresh['procedure']) && is_array($fresh['procedure']) ? $fresh['procedure'] : [];

                $before = count($fresh['procedure']);
                $fresh['procedure'] = collect($fresh['procedure'])
                    ->reject(fn($p) => (string)($p['procedureId'] ?? '') === $procId)
                    ->values()
                    ->all();

                if (count($fresh['procedure']) === $before) {
                    // tidak ditemukan
                    throw new \RuntimeException('Prosedur tidak ditemukan.');
                }

                // Persist & sinkronkan
                $this->updateJsonRI($riHdrNo, $fresh);
                $this->dataDaftarRi = $fresh;
            });

            $this->emit('syncronizeAssessmentPerawatRIFindData');
            toastr()->positionClass('toast-top-left')->addSuccess('Prosedur berhasil dihapus.');
        } catch (\Throwable $e) {
            toastr()->positionClass('toast-top-left')->addError('Gagal menghapus prosedur: ' . $e->getMessage());
        }
    }
    // LOV selected end
    /////////////////////////////////////////////////
    // Lov dataProcedureICD9CmLov //////////////////////
    ////////////////////////////////////////////////


    private function findData($riHdrNo): void
    {
        $this->dataDaftarRi = $this->findDataRI($riHdrNo) ?: [];
        $this->dataDaftarRi['riHdrNo'] = $this->dataDaftarRi['riHdrNo'] ?? $riHdrNo;
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

    private function withDiagnosisLock(string $riHdrNo, callable $fn): void
    {
        $key = "ri:{$riHdrNo}:json";
        Cache::lock($key, 10)->block(5, function () use ($fn) {
            DB::transaction(function () use ($fn) {
                $fn();
            }, 5);
        });
    }

    // when new form instance
    public function mount()
    {
        $this->findData($this->riHdrNoRef);
        // set data dokter ref
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
