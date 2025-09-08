<?php

namespace App\Http\Livewire\MasterDokter\FormEntryDokter;

use Illuminate\Support\Facades\DB;
use Exception;

use App\Http\Traits\customErrorMessagesTrait;

use App\Http\Traits\SATUSEHAT\PractitionerTrait;


use Livewire\Component;

class FormEntryDokter extends Component
{
    use PractitionerTrait;
    // listener from blade////////////////
    protected $listeners = [];

    public string $dokterId;
    public string $isOpenMode = 'insert';

    public array $FormEntryDokter = [];




    // rules///////////////////
    protected $rules = [
        'FormEntryDokter.dokterId' => 'required|numeric|digits_between:1,4',
        'FormEntryDokter.dokterName' => 'required',
        'FormEntryDokter.dokterIdBPJS' => '',
        'FormEntryDokter.dokterUuid' => '',
    ];

    protected $messages = [];

    protected $validationAttributes = [
        'FormEntryDokter.dokterId' => 'Kode Dokter',
        'FormEntryDokter.dokterName' => 'Nama Dokter'

    ];
    // rules///////////////////





    public function closeModal(): void
    {
        $this->emit('masterDokterCloseModal');
    }

    private function findData($dokterId): void
    {
        try {
            $findData = DB::table('rsmst_doctors')
                ->where('dr_id', $dokterId)
                ->first();


            if ($findData) {
                $this->FormEntryDokter = [
                    'dokterId' => $findData->dr_id,
                    'dokterName' => $findData->dr_name,
                    'dokterIdBPJS' => $findData->kd_dr_bpjs,
                    'dokterUuid' => $findData->dr_uuid,
                    'dokterNik' => $findData->dr_nik,


                ];
            } else {

                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Data tidak ditemukan.");
                $this->FormEntryDokter = [
                    'dokterId' => null,
                    'dokterName' => null,
                    'dokterIdBPJS' => null,
                    'dokterUuid' => null,
                    'dokterNik' => null,

                ];
            }
        } catch (Exception $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError($e->getMessage());
            $this->FormEntryDokter = [
                'dokterId' => null,
                'dokterName' => null,
                'dokterIdBPJS' => null,
                'dokterUuid' => null,
                'dokterNik' => null,

            ];
        }
    }


    private function update($dokterId): void
    {
        // update table trnsaksi
        DB::table('rsmst_doctors')
            ->where('dr_id', $dokterId)
            ->update([
                'dr_id' => isset($this->FormEntryDokter['dokterId']) ? $this->FormEntryDokter['dokterId'] : null,
                'dr_name' => isset($this->FormEntryDokter['dokterName']) ? $this->FormEntryDokter['dokterName'] : '',
                'kd_dr_bpjs' => isset($this->FormEntryDokter['dokterIdBPJS']) ? $this->FormEntryDokter['dokterIdBPJS'] : '',
                'dr_uuid' => isset($this->FormEntryDokter['dokterUuid']) ? $this->FormEntryDokter['dokterUuid'] : '',
                'dr_nik' => isset($this->FormEntryDokter['dokterNik']) ? $this->FormEntryDokter['dokterNik'] : '',
            ]);

        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Data berhasil diupdate.");
    }

    private function insert(): void
    {
        // update table trnsaksi
        DB::table('rsmst_doctors')
            ->insert([
                'dr_id' => isset($this->FormEntryDokter['dokterId']) ? $this->FormEntryDokter['dokterId'] : null,
                'dr_name' => isset($this->FormEntryDokter['dokterName']) ? $this->FormEntryDokter['dokterName'] : '',
                'kd_dr_bpjs' => isset($this->FormEntryDokter['dokterIdBPJS']) ? $this->FormEntryDokter['dokterIdBPJS'] : '',
                'dr_uuid' => isset($this->FormEntryDokter['dokterUuid']) ? $this->FormEntryDokter['dokterUuid'] : '',
                'dr_nik' => isset($this->FormEntryDokter['dokterNik']) ? $this->FormEntryDokter['dokterNik'] : '',
            ]);

        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Data berhasil dimasukkan.");
    }

    public function store()
    {
        // validate
        $this->validateData();

        // Jika mode data //insert
        if ($this->isOpenMode == 'insert') {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("masih dikembangkan Master Level 2.");
            return;
            $this->insert();
            $this->isOpenMode = 'update';
        } else {
            // Jika mode data //update
            $this->update($this->dokterId);
        }

        // $this->closeModal();
    }

    // public function UpdatePractitionerUuid(string $nik = '')
    // {
    //     // Proses Validasi///////////////////////////////////////////
    //     $r = ['nik' => $nik];
    //     $rules = ['nik' => 'bail|required|digits:16'];
    //     $customErrorMessagesTrait = customErrorMessagesTrait::messages();
    //     $attribute = ['nik' => 'Data NIK'];

    //     $validator = Validator::make($r, $rules, $customErrorMessagesTrait, $attribute);

    //     if ($validator->fails()) {
    //         toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError($validator->messages()->all());
    //         return;
    //     }
    //     // Proses Validasi///////////////////////////////////////////


    //     // Proses Validasi///////////////////////////////////////////
    //     try {

    //         $PractitionerByNIK = SatuSehatTrait::PractitionerByNIK($nik);

    //         // Jika uuid tidak ditemukan
    //         if (!isset($PractitionerByNIK->getOriginalContent()['response']['entry'][0]['resource']['id'])) {
    //             toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('UUID tidak dapat ditemukan.' . $PractitionerByNIK->getOriginalContent()['metadata']['message']);
    //             return;
    //         }

    //         $this->validateData();
    //         $this->FormEntryDokter['dokterUuid'] = $PractitionerByNIK->getOriginalContent()['response']['entry'][0]['resource']['id'];
    //         $this->store();
    //         toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess($PractitionerByNIK->getOriginalContent()['response']['entry'][0]['resource']['id'] . ' / ' . $PractitionerByNIK->getOriginalContent()['response']['entry'][0]['resource']['name'][0]['text']);
    //         return;

    //         // dd($PractitionerByNIK->getOriginalContent());
    //         // dd($PractitionerByNIK->getOriginalContent()['response']['entry'][0]['resource']['id']);
    //         // dd($PractitionerByNIK->getOriginalContent()['response']['entry'][0]['resource']['name'][0]['text']);
    //     } catch (\Illuminate\Validation\ValidationException $e) {
    //         // dd($validator->fails());
    //         toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Errors "' . $e->getMessage());
    //         return;
    //     }
    // }

    public function UpdatePractitionerUuid(string $nik = ''): void
    {
        // 1. Inisialisasi koneksi dan cari Practitioner (dokter) berdasarkan NIK
        $this->initializeSatuSehat();
        $entries = collect(
            $this->searchPractitioner(['nik' => $nik])['entry'] ?? []
        );
        // 2. Jika tidak ada, buat dokter baru (pakai data dari $this->FormEntryDokter)
        if ($entries->isEmpty()) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addWarning("Tidak ada dokter ditemukan dengan NIK: {$nik}");


            // off kan dulu (ada mekanisme sendiri di satu sehat belum kita pelajarii)
            // $result     = $this->createPractitioner($this->FormEntryDokter);
            // $createdUuid = $result['id'] ?? null;

            // // Simpan UUID baru
            // $this->FormEntryDokter['dokterUuid'] = $createdUuid;
            // $this->store();

            // toastr()
            //     ->closeOnHover(true)
            //     ->closeDuration(3)
            //     ->positionClass('toast-top-left')
            //     ->addSuccess("Dokter baru berhasil dibuat (UUID: {$createdUuid})");

            return;
        }

        // 3. Ambil UUID dokter pertama dari hasil pencarian
        $newUuid     = $entries->pluck('resource.id')->first();
        $currentUuid = $this->FormEntryDokter['dokterUuid'] ?? null;

        // 4. Jika belum ada UUID tersimpan, set dan notify
        if (empty($currentUuid)) {
            $this->FormEntryDokter['dokterUuid'] = $newUuid;
            $this->store();

            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addSuccess("dokterUuid di-set ke {$newUuid}");
            return;
        }

        // 5. Jika UUID sudah sama, beri info
        if ($currentUuid === $newUuid) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addInfo("dokterUuid sudah sesuai dengan data terbaru");
            return;
        }

        // 6. Jika berbeda, cek apakah UUID lama masih ada dalam hasil pencarian
        $oldStillExists = $entries
            ->pluck('resource.id')
            ->contains($currentUuid);

        if ($oldStillExists) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addSuccess("dokterUuid lama ({$currentUuid}) masih ditemukan");
        } else {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addWarning("dokterUuid lama ({$currentUuid}) tidak ada di hasil terbaru");
        }
    }

    // validate Data RJ//////////////////////////////////////////////////
    private function validateData(): void
    {
        // Proses Validasi///////////////////////////////////////////
        try {

            // tambahkan unique counstrain
            if ($this->isOpenMode == 'insert') {
                $this->rules['FormEntryDokter.dokterId'] = 'required|numeric|digits_between:1,3|unique:rsmst_doctors,dr_id';
            }

            $this->validate($this->rules, customErrorMessagesTrait::messages());
        } catch (\Illuminate\Validation\ValidationException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError($e->getMessage());
            $this->validate($this->rules, customErrorMessagesTrait::messages());
        }
    }

    public function mount()
    {
        $this->findData($this->dokterId);
    }

    public function render()
    {
        return view('livewire.master-dokter.form-entry-dokter.form-entry-dokter');
    }
}
