<?php

namespace App\Http\Livewire\MasterPoli\FormEntryPoli;

use Illuminate\Support\Facades\DB;
use Exception;
use App\Http\Traits\customErrorMessagesTrait;

use App\Http\Traits\SATUSEHAT\LocationTrait;

use Livewire\Component;

class FormEntryPoli extends Component
{
    use LocationTrait;



    // listener from blade////////////////
    protected $listeners = [];

    public string $poliId;
    public string $isOpenMode = 'insert';

    public array $FormEntryPoli = [];




    // rules///////////////////
    protected $rules = [
        'FormEntryPoli.poliId' => 'required|numeric|digits_between:1,3',
        'FormEntryPoli.poliDesc' => 'required',
        'FormEntryPoli.poliIdBPJS' => '',
        'FormEntryPoli.poliUuid' => '',
        'FormEntryPoli.spesialisStatus' => '',

    ];

    protected $messages = [];

    protected $validationAttributes = [
        'FormEntryPoli.poliId' => 'Kode Poliklinik',
        'FormEntryPoli.poliDesc' => 'Nama Poliklinik'

    ];
    // rules///////////////////





    public function closeModal(): void
    {
        $this->emit('masterPoliCloseModal');
    }

    private function findData($poliId): void
    {
        try {
            $findData = DB::table('rsmst_polis')
                ->where('poli_id', $poliId)
                ->first();


            if ($findData) {
                $this->FormEntryPoli = [
                    'poliId' => $findData->poli_id,
                    'poliDesc' => $findData->poli_desc,
                    'poliIdBPJS' => $findData->kd_poli_bpjs,
                    'poliUuid' => $findData->poli_uuid,
                    'spesialisStatus' => $findData->spesialis_status,


                ];
            } else {

                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Data tidak ditemukan.");
                $this->FormEntryPoli = [
                    'poliId' => null,
                    'poliDesc' => null,
                    'poliIdBPJS' => null,
                    'poliUuid' => null,
                    'spesialisStatus' => null,

                ];
            }
        } catch (Exception $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError($e->getMessage());
            $this->FormEntryPoli = [
                'poliId' => null,
                'poliDesc' => null,
                'poliIdBPJS' => null,
                'poliUuid' => null,
                'spesialisStatus' => null,

            ];
        }
    }


    private function update($poliId): void
    {
        // update table trnsaksi
        DB::table('rsmst_polis')
            ->where('poli_id', $poliId)
            ->update([
                'poli_id' => isset($this->FormEntryPoli['poliId']) ? $this->FormEntryPoli['poliId'] : null,
                'poli_desc' => isset($this->FormEntryPoli['poliDesc']) ? $this->FormEntryPoli['poliDesc'] : '',
                'kd_poli_bpjs' => isset($this->FormEntryPoli['poliIdBPJS']) ? $this->FormEntryPoli['poliIdBPJS'] : '',
                'poli_uuid' => isset($this->FormEntryPoli['poliUuid']) ? $this->FormEntryPoli['poliUuid'] : '',
                'spesialis_status' => isset($this->FormEntryPoli['spesialisStatus']) ? $this->FormEntryPoli['spesialisStatus'] : ''

            ]);

        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Data berhasil diupdate.");
    }


    private function insert(): void
    {
        // update table trnsaksi
        DB::table('rsmst_polis')
            ->insert([
                'poli_id' => isset($this->FormEntryPoli['poliId']) ? $this->FormEntryPoli['poliId'] : null,
                'poli_desc' => isset($this->FormEntryPoli['poliDesc']) ? $this->FormEntryPoli['poliDesc'] : '',
                'kd_poli_bpjs' => isset($this->FormEntryPoli['poliIdBPJS']) ? $this->FormEntryPoli['poliIdBPJS'] : '',
                'poli_uuid' => isset($this->FormEntryPoli['poliUuid']) ? $this->FormEntryPoli['poliUuid'] : '',
                'spesialis_status' => isset($this->FormEntryPoli['spesialisStatus']) ? $this->FormEntryPoli['spesialisStatus'] : ''

            ]);

        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Data berhasil dimasukkan.");
    }

    public function store()
    {
        // validate
        $this->validateData();

        // Jika mode data //insert
        if ($this->isOpenMode == 'insert') {
            $this->insert();
            $this->isOpenMode = 'update';
        } else {
            // Jika mode data //update
            $this->update($this->poliId);
        }

        // $this->closeModal();
    }


    public function UpdatelocationUuid(string $poliId, string $poliDesc): void
    {
        // 1. Inisialisasi koneksi ke SatuSehat dan cari lokasi berdasarkan nama
        $this->initializeSatuSehat();
        $locationEntries = collect(
            $this->searchLocation(['name' => $poliDesc])['entry'] ?? []
        );

        // 2. Kalau tidak ada hasil, tampilkan warning toast
        if ($locationEntries->isEmpty()) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addWarning('Tidak ada lokasi yang ditemukan');

            $result = $this->createLocation([
                'identifier' => $poliId,
                'name'       => $poliDesc,
                // … field lain jika perlu
            ]);

            // Pecah ke variabel yang jelas
            $createdUuid  = $result['id']           ?? null;
            $resourceType = $result['resourceType'] ?? null;
            $status       = $result['status']       ?? null;

            // Simpan UUID yang baru
            $this->FormEntryPoli['poliUuid'] = $createdUuid;
            $this->store();

            // Toast sukses
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addSuccess(
                    "Lokasi baru “{$poliDesc}” berhasil dibuat " .
                        "(UUID: {$createdUuid}, ResourceType: {$resourceType}, Status: {$status})"
                );

            return;
        }

        // 3. Ambil UUID lokasi pertama dari hasil pencarian
        $newLocationUuid = $locationEntries
            ->pluck('resource.id')
            ->first();

        // 4. Jika belum ada UUID tersimpan, simpan yang baru dan tampilkan success toast
        if (empty($this->FormEntryPoli['poliUuid'])) {
            $this->FormEntryPoli['poliUuid'] = $newLocationUuid;
            $this->store();
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addSuccess("UUID di-set ke {$newLocationUuid}");
            return;
        }

        // 5. Ambil UUID yang sudah tersimpan dan bandingkan dengan yang baru
        $currentLocationUuid = $this->FormEntryPoli['poliUuid'];
        if ($currentLocationUuid === $newLocationUuid) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addInfo('UUID sudah cocok dengan hasil terbaru');
            return;
        }

        // 6. Jika berbeda, cek apakah UUID lama masih ada dalam hasil pencarian
        $isOldUuidStillPresent = $locationEntries
            ->pluck('resource.id')
            ->contains($currentLocationUuid);

        if ($isOldUuidStillPresent) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addSuccess("UUID lama {$currentLocationUuid} masih ada di hasil pencarian");
        } else {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addWarning("UUID lama {$currentLocationUuid} tidak ada di hasil baru");
        }
    }


    // validate Data RJ//////////////////////////////////////////////////
    private function validateData(): void
    {
        // Proses Validasi///////////////////////////////////////////
        try {

            // tambahkan unique counstrain
            if ($this->isOpenMode == 'insert') {
                $this->rules['FormEntryPoli.poliId'] = 'required|numeric|digits_between:1,3|unique:rsmst_polis,poli_id';
            }

            $this->validate($this->rules, customErrorMessagesTrait::messages());
        } catch (\Illuminate\Validation\ValidationException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError($e->getMessage());
            $this->validate($this->rules, customErrorMessagesTrait::messages());
        }
    }

    public function mount()
    {
        $this->findData($this->poliId);
    }

    public function render()
    {
        return view('livewire.master-poli.form-entry-poli.form-entry-poli');
    }
}
