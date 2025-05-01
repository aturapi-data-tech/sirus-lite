<?php

namespace App\Http\Livewire\EmrRJ\PostEncounterRJ;

use App\Http\Traits\EmrRJ\EmrRJTrait;
use App\Http\Traits\MasterPasien\MasterPasienTrait;
use App\Http\Traits\SATUSEHAT\EncounterTrait;
use App\Http\Traits\SATUSEHAT\PatientTrait;
use App\Http\Traits\SATUSEHAT\ConditionTrait;


use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\DB;

// use Illuminate\Support\Str;

// use App\Http\Livewire\SatuSehat\Encounter\Encounter;
// use App\Http\Livewire\SatuSehat\Condition\Condition;
// use App\Http\Livewire\SatuSehat\Bundle\Bundle;

// use App\Http\Traits\BPJS\SatuSehatTrait;
// use App\Http\Traits\customErrorMessagesTrait;


use Livewire\Component;

class PostEncounterRJ extends Component
{
    use EmrRJTrait, MasterPasienTrait, EncounterTrait, PatientTrait, ConditionTrait;


    public $rjNoRef;

    public array $dataDaftarPoliRJ = [];
    public array $dataPasienRJ = [];
    public string $EncounterID;

    protected $listeners = [
        'syncronizePostEncounterRJ' => 'mount',
    ];

    private function findData($rjno): void
    {
        // Ambil data daftar kunjungan (fallback ke array kosong)
        $this->dataDaftarPoliRJ = $this->findDataRJ($rjno)['dataDaftarRJ'] ?? [];

        // Ambil array UUID, atau empty array
        $uuids = $this->dataDaftarPoliRJ['satuSehatUuidRJ'] ?? [];
        $this->EncounterID = $uuids['encounter']['uuid'] ?? '';
    }




    public function postEncounterRJ()
    {
        // 1. Validasi minimal
        // $this->validate();

        // 2. Ambil data kunjungan & pasien
        $find = $this->findDataRJ($this->rjNoRef);
        $this->dataDaftarPoliRJ = $find['dataDaftarRJ'] ?? [];
        $this->dataPasienRJ     = $find['dataPasienRJ'] ?? [];

        // --- CEK: apakah encounter sudah pernah dikirim? ---
        if (!empty($this->dataDaftarPoliRJ['satuSehatUuidRJ']['encounter']['uuid'] ?? null)) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addInfo(
                    'Encounter sudah pernah dikirim (ID: '
                        . $this->dataDaftarPoliRJ['satuSehatUuidRJ']['encounter']['uuid']
                        . ').'
                );
            return;
        }

        // 3. Tentukan class_code sesuai jenis layanan
        $classMap = [
            'RAJAL' => 'AMB',    // Rawat Jalan → ambulatory
            'IGD'   => 'EMER',   // UGD         → emergency
            'RANAP' => 'IMP',    // Rawat Inap  → inpatient
        ];
        $pelayananType = 'RAJAL';
        $classCode     = $classMap[$pelayananType] ?? 'AMB';

        // 4. Proses waktu masuk ruang (taskId3)
        $rawStart = $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId3'] ?? null;
        if (empty($rawStart)) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('Waktu masuk ruang (taskId3) tidak ditemukan, proses dibatalkan.');
            return;
        }

        try {
            $startDateIso = Carbon::createFromFormat('d/m/Y H:i:s', $rawStart)
                ->toIso8601String();
        } catch (\Exception $e) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError("Format waktu taskId3 tidak valid: “{$rawStart}”. Proses dibatalkan.");
            return;
        }

        // 5. Siapkan payload Encounter
        if (empty($this->dataPasienRJ['patientUuid'] ?? null)) {
            $this->updatepatientUuid($this->dataDaftarPoliRJ['regNo']);
        }


        $payload = [
            'status'           => 'arrived',                     // status awal untuk encounter baru
            'patientId'        => $this->dataPasienRJ['patientUuid'] ?? null,
            'patientName'      => $this->dataPasienRJ['regName']    ?? null,
            'practitionerId'   => $this->dataPasienRJ['drUuid']     ?? null,
            'practitionerName' => $this->dataPasienRJ['drName']     ?? null,
            'class_code'       => $classCode,
            'startDate'        => $startDateIso,
            'organizationId'   => env('SATUSEHAT_ORGANIZATION_ID') ?? null,
            'locationId'       => $this->dataPasienRJ['poliUuid'] ?? null
        ];

        // 6. Validasi kehadiran UUID pasien & dokter
        $validator = Validator::make($payload, [
            'patientId'      => 'required',
            'practitionerId' => 'required',
            'organizationId' => 'required'
        ], [
            'patientId.required'      => 'UUID pasien belum tersedia.',
            'practitionerId.required' => 'UUID dokter belum tersedia.',
            'organizationId.required' => 'UUID poli belum tersedia.',
        ]);

        if ($validator->fails()) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError($validator->errors()->first());
            return;
        }

        // 7. Kirim ke Satu Sehat
        try {
            // 1) Kirim Encounter baru
            $this->initializeSatuSehat();
            $response = $this->createNewEncounter($payload);
            $this->EncounterID = $response['id'] ?? '';

            // 2) Simpan log Encounter ke dataDaftarPoliRJ
            $this->dataDaftarPoliRJ['satuSehatUuidRJ']['encounter'] = [
                'uuid'       => $this->EncounterID,
                'status'     => 'arrived',
                'start_time' => $rawStart,
                'end_time'   => '',
            ];

            // 3) Pindahkan pasien ke ruang (in-progress) + set location
            $this->startRoomEncounter(
                $this->EncounterID,
                [
                    'locationId' => $this->dataPasienRJ['poliUuid'],
                    'startDate'        => $startDateIso
                ]
            );

            // 4) Update log in-progress
            $this->dataDaftarPoliRJ['satuSehatUuidRJ']['encounter'] = [
                'uuid'       => $this->EncounterID,
                'status'     => 'in-progress',
                'locationId' => $this->dataPasienRJ['poliUuid'],
            ];

            // 5) Persist ke database
            $this->updateJsonRJ($this->rjNoRef, $this->dataDaftarPoliRJ);
            $this->emit('syncronizePostEncounterRJ');

            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addSuccess("Encounter terkirim (ID: {$this->EncounterID})");
        } catch (\Exception $e) {
            dd($e->getMessage());
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError("Gagal kirim Encounter: " . $e->getMessage());
        }
    }

    public function getEncounterRJ($encounterId)
    {
        $this->initializeSatuSehat();
        $existing = $this->getEncounter($encounterId);
        dd($existing);
    }

    private function updatepatientUuid(string $regNo = ''): void
    {

        $dataPasien = $this->findDataMasterPasien($regNo ?? '');
        // 1. Inisialisasi koneksi dan cari Patient berdasarkan NIK
        $this->initializeSatuSehat();
        $nik = $dataPasien['pasien']['identitas']['nik'] ?? '';

        $entries = collect(
            $this->searchPatient(['nik' => $nik])['entry'] ?? []
        );

        // 2. Jika tidak ada, buat pasien baru (pakai data dari $dataPasien['pasien'])
        if ($entries->isEmpty()) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addWarning("Tidak ada pasien ditemukan dengan NIK: {$nik}");
            return;
        }

        // 3. Ambil UUID Patient pertama dari hasil pencarian
        $newUuid = $entries->pluck('resource.id')->first();
        $currentUuid = $dataPasien['pasien']['identitas']['patientUuid'] ?? null;

        // 4. Jika belum ada UUID tersimpan, set dan notify
        if (empty($currentUuid)) {
            $dataPasien['pasien']['identitas']['patientUuid'] = $newUuid;
            $this->dataPasienRJ['patientUuid'] = $newUuid;

            $this->updateJsonMasterPasien($regNo, $dataPasien);
            //    updateDB
            DB::table('rsmst_pasiens')->where('reg_no', $regNo)
                ->update([
                    'patient_uuid' => $newUuid,
                ]);

            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addSuccess("patientUuid di-set ke {$newUuid}");
            return;
        }

        // 5. Jika UUID sudah sama, beri info
        if ($currentUuid === $newUuid) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addInfo("patientUuid sudah sesuai dengan data terbaru");
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
                ->addSuccess("patientUuid lama ({$currentUuid}) masih ditemukan");
        } else {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addWarning("patientUuid lama ({$currentUuid}) tidak ada di hasil terbaru");
        }
    }










    public function postKeluhanUtamaRJ()
    {
        // 1. Validasi dan ambil data kunjungan & pasien
        $find = $this->findDataRJ($this->rjNoRef);
        $dataDaftarPoliRJ = $find['dataDaftarRJ'] ?? [];
        $dataPasienRJ = $find['dataPasienRJ'] ?? [];

        // Ambil nilai‐nilai penting
        $encounterUuid      = $dataDaftarPoliRJ['satuSehatUuidRJ']['encounter']['uuid'] ?? null;
        $chiefComplaintUuid  = $dataDaftarPoliRJ['satuSehatUuidRJ']['chiefComplaint']['uuid'] ?? null;
        $keluhanUtama     = $dataDaftarPoliRJ['anamnesa']['keluhanUtama']['keluhanUtama'] ?? null;
        $onsetDate       = $dataDaftarPoliRJ['taskIdPelayanan']['taskId3'] ?? null;

        // Pastikan encounter sudah terkirim
        $encounterUuid = $dataDaftarPoliRJ['satuSehatUuidRJ']['encounter']['uuid'] ?? null;
        if (empty($encounterUuid)) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('Encounter belum terkirim, proses keluhan utama dibatalkan.');
            return;
        }

        // Cek apakah sudah pernah kirim keluhan utama
        if (!empty($chiefComplaintUuid)) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addInfo(
                    'Keluhan utama telah dikirim (ID: ' .
                        $dataDaftarPoliRJ['satuSehatUuidRJ']['chiefComplaint']['uuid'] . ').'
                );
            return;
        }

        if (empty($keluhanUtama)) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addWarning('Keluhan utama belum diisi. Silakan lengkapi anamnesa keluhan utama sebelum mengirim.');
            return;
        }

        if (empty($onsetDate)) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('Waktu masuk ruang (taskId3) tidak ditemukan, proses dibatalkan.');
            return;
        }

        // Konversi waktu masuk ruang ke ISO8601
        try {
            $onsetDateIso = Carbon::createFromFormat('d/m/Y H:i:s', $onsetDate)
                ->toIso8601String();
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError("Format waktu “{$onsetDateIso}” tidak valid.");
            return;
        }

        $payload = [
            'patientId'       => $dataPasienRJ['patientUuid']   ?? null,
            'encounterId'     => $encounterUuid,
            'snomed_code'     => $dataDaftarPoliRJ['anamnesa']['keluhanUtama']['snomedCode'] ?? null,              // '21522001' Abdominal pain (finding)
            'snomed_display'  => $dataDaftarPoliRJ['anamnesa']['keluhanUtama']['snomedDisplay'] ?? null,
            'complaint_text'  => $dataDaftarPoliRJ['anamnesa']['keluhanUtama']['keluhanUtama'] ?? null,
            'onsetDate'       => $onsetDateIso,
            'recordedDate'    =>  Carbon::now()->toIso8601String(),
            'severity_code'   =>  $dataDaftarPoliRJ['anamnesa']['keluhanUtama']['severityCode'] ?? null,             // Tingkat keparahan
            'severity_display' =>  $dataDaftarPoliRJ['anamnesa']['keluhanUtama']['severityDisplay'] ?? null,
        ];

        // 3. Validasi payload
        $validator = Validator::make($payload, [
            'patientId'      => 'required|string',
            'encounterId'    => 'required|string',
            // 'snomed_code'    => 'required|string',
            'complaint_text' => 'required|string',
        ], [
            'patientId.required'      => 'UUID pasien wajib diisi.',
            'encounterId.required'    => 'UUID encounter wajib diisi.',
            // 'snomed_code.required'    => 'Kode SNOMED CT untuk keluhan utama wajib diisi.',
            'complaint_text.required' => 'Deskripsi keluhan utama wajib diisi.',
        ]);

        if ($validator->fails()) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError($validator->errors()->first());
            return;
        }

        try {
            $this->initializeSatuSehat();
            $result = $this->createChiefComplaint($payload);
            $conditionId = $result['id'] ?? '';

            // Simpan log keluhan utama
            $dataDaftarPoliRJ['satuSehatUuidRJ']['chiefComplaint']['uuid'] = $conditionId;
            $this->updateJsonRJ($this->rjNoRef, $dataDaftarPoliRJ);

            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addSuccess("Keluhan utama terkirim (ID: {$conditionId})");
        } catch (\Exception $e) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('Gagal kirim keluhan utama: ' . $e->getMessage());
        }
    }

    public function postRiwayatPenyakitSekarangRJ(): void
    {
        // 1. Ambil data pasien & encounter
        $find             = $this->findDataRJ($this->rjNoRef);
        $dataDaftarPoliRJ = $find['dataDaftarRJ'] ?? [];
        $dataPasienRJ     = $find['dataPasienRJ']   ?? [];

        $patientUuid      = $dataPasienRJ['patientUuid'] ?? null;
        $encounterUuid    = $dataDaftarPoliRJ['satuSehatUuidRJ']['encounter']['uuid'] ?? null;
        $currentCondUuid  = $dataDaftarPoliRJ['satuSehatUuidRJ']['currentCondition']['uuid'] ?? null;

        // 2. Validasi prasyarat
        if (empty($patientUuid)) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('UUID pasien tidak ditemukan, proses dibatalkan.');
            return;
        }
        if (empty($encounterUuid)) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('Encounter belum terkirim, proses kondisi sekarang dibatalkan.');
            return;
        }

        if (!empty($currentCondUuid)) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addInfo("Kondisi sekarang telah dikirim (ID: {$currentCondUuid}).");
            return;
        }

        // 3. Ambil input kondisi sekarang
        $code    = $dataDaftarPoliRJ['anamnesa']['riwayatPenyakitSekarangUmum']['snomedCode'] ?? '';
        $display = $dataDaftarPoliRJ['anamnesa']['riwayatPenyakitSekarangUmum']['snomedDisplay'] ?? '';
        $text    = $dataDaftarPoliRJ['anamnesa']['riwayatPenyakitSekarangUmum']['riwayatPenyakitSekarangUmum'] ?? '';
        // atau field kamu gunakan
        if (empty($code)) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('Data kondisi sekarang belum diisi.');
            return;
        }

        // 4. Konversi tanggal onset (opsional)
        try {
            if (!empty($anam['onsetDate'])) {
                $onsetDate = $dataDaftarPoliRJ['taskIdPelayanan']['taskId3'] ?? null;
                $onsetIso = Carbon::createFromFormat('d/m/Y H:i:s', $onsetDate)
                    ->toIso8601String();
            }
        } catch (\Throwable $e) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('Format tanggal onset kondisi tidak valid.');
            return;
        }

        // 5. Siapkan payload
        $payload = [
            'patientId'      => $patientUuid,
            'encounterId'    => $encounterUuid,
            'snomed_code'    => $code,
            'snomed_display' => $display,
            'complaint_text' => $text,
            'recordedDate'   => now()->toIso8601String(),
        ];
        if (!empty($onsetIso)) {
            $payload['onsetDate'] = $onsetIso;
        }
        if (!empty($note)) {
            $payload['note'] = $note ?? '';
        }

        // 6. Validasi payload
        $validator = Validator::make($payload, [
            'patientId'      => 'required|string',
            'encounterId'    => 'required|string',
            'snomed_code'    => 'nullable|string',
            'complaint_text' => 'nullable|string',
        ], [
            'patientId.required'      => 'UUID pasien wajib diisi.',
            'encounterId.required'    => 'UUID encounter wajib diisi.',
            'complaint_text.string'   => 'Deskripsi kondisi harus berupa teks.',
        ]);
        if ($validator->fails()) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('Validasi gagal: ' . $validator->errors()->first());
            return;
            return;
        }

        // 7. Kirim ke SatuSehat
        try {
            $this->initializeSatuSehat();
            $result           = $this->createCurrentCondition($payload);
            $conditionId      = $result['id'] ?? '';

            // 8. Simpan UUID ke JSON RJ
            $dataDaftarPoliRJ['satuSehatUuidRJ']['currentCondition']['uuid'] = $conditionId;
            $this->updateJsonRJ($this->rjNoRef, $dataDaftarPoliRJ);

            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addSuccess("Kondisi sekarang berhasil dikirim (ID: {$conditionId}).");
        } catch (\Exception $e) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('Gagal mengirim kondisi sekarang: ' . $e->getMessage());
        }
    }


    public function postRiwayatPenyakitDahuluRJ()
    {
        // 1. Validasi & ambil data kunjungan & pasien
        $find           = $this->findDataRJ($this->rjNoRef);
        $dataDaftarPoliRJ = $find['dataDaftarRJ'] ?? [];
        $dataPasienRJ   = $find['dataPasienRJ'] ?? [];

        $patientUuid  = $dataPasienRJ['patientUuid'] ?? null;
        $encounterUuid = $dataDaftarPoliRJ['satuSehatUuidRJ']['encounter']['uuid'] ?? null;
        $historyData  = $dataDaftarPoliRJ['anamnesa']['riwayatPenyakitDahulu']['riwayatPenyakitDahulu'] ?? null;
        $pastHistUuid = $dataDaftarPoliRJ['satuSehatUuidRJ']['pastMedicalHistory']['uuid'] ?? null;


        // 2. Validasi prasyarat
        if (empty($patientUuid)) {
            toastr()->addError('UUID pasien tidak ditemukan, proses dibatalkan.');
            return;
        }
        if (empty($encounterUuid)) {
            toastr()->addError('Encounter belum terkirim, proses dibatalkan.');
            return;
        }
        if (!empty($pastHistUuid)) {
            toastr()->addInfo("Riwayat penyakit dahulu sudah dikirim (ID: {$pastHistUuid}).");
            return;
        }

        // Pastikan minimal ada teks atau SNOMED
        $snomedCode    = $dataDaftarPoliRJ['anamnesa']['riwayatPenyakitDahulu']['snomedCode'] ?? null;
        $snomedDisplay = $dataDaftarPoliRJ['anamnesa']['riwayatPenyakitDahulu']['snomedDisplay'] ?? null;
        $historyText   = $historyData ?? null;

        if (empty($snomedCode) && empty($historyText)) {
            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addWarning('Data riwayat penyakit dahulu belum diisi.');
            return;
        }

        // 3. Konversi tanggal onset & abatement
        try {
            $onsetIso     = !empty($dataDaftarPoliRJ['taskIdPelayanan']['taskId3'])
                ? Carbon::createFromFormat('d/m/Y H:i:s', $dataDaftarPoliRJ['taskIdPelayanan']['taskId3'])->toIso8601String()
                : null;
            //isi kosong jika sembuh isi dengan tanggal pasien sembuh
            $abatementIso = !empty(null)
                ? Carbon::createFromFormat('d/m/Y H:i:s', $dataDaftarPoliRJ['taskIdPelayanan']['taskId3'])->toIso8601String()
                : null;
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('Format tanggal riwayat penyakit tidak valid.');
            return;
        }

        // 4. Siapkan payload
        $payload = [
            'patientId'      => $patientUuid,
            'encounterId'     => $encounterUuid,
            'snomed_code'    => $snomedCode,
            'snomed_display' => $snomedDisplay,
            'history_text'   => $historyText,
            'recordedDate'   => now()->toIso8601String(),
        ];


        if ($onsetIso)     $payload['onsetDate']     = $onsetIso;
        if ($abatementIso) $payload['abatementDate'] = $abatementIso;
        if (!empty($historyData['note'])) {
            $payload['note'] = $historyData['note'];
        }


        // 5. Validasi payload
        $validator = Validator::make($payload, [
            'patientId'    => 'required|string',
            'snomed_code'  => 'nullable|string',
            'history_text' => 'nullable|string',
        ], [
            'patientId.required'    => 'UUID pasien wajib diisi.',
            'history_text.required' => 'Deskripsi riwayat penyakit wajib diisi.',
        ]);

        if ($validator->fails()) {
            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError($validator->errors()->first());
            return;
        }

        // 6. Kirim ke SatuSehat
        try {
            $this->initializeSatuSehat();
            $result = $this->createPastMedicalHistory($payload);
            $historyId = $result['id'] ?? '';

            // 7. Simpan UUID ke JSON RJ
            $dataDaftarPoli['satuSehatUuidRJ']['pastMedicalHistory']['uuid'] = $historyId;
            $this->updateJsonRJ($this->rjNoRef, $dataDaftarPoliRJ);

            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addSuccess("Riwayat penyakit dahulu terkirim (ID: {$historyId}).");
        } catch (\Exception $e) {
            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('Gagal kirim riwayat penyakit dahulu: ' . $e->getMessage());
        }
    }


    public function getConditionRJ($encounterId)
    {
        $this->initializeSatuSehat();
        $existing = $this->searchConditionsByEncounter($encounterId);
        dd($existing);
    }

    public function mount()
    {
        $this->findData($this->rjNoRef);
    }
    public function render()
    {
        return view('livewire.emr-r-j.post-encounter-r-j.post-encounter-r-j');
    }
}
