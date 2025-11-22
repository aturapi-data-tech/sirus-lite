<?php

namespace App\Http\Livewire\EmrRI\GeneralConsentPasienRI;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Http\Traits\EmrRI\EmrRITrait;
use App\Http\Traits\MasterPasien\MasterPasienTrait;

class GeneralConsentPasienRI extends Component
{
    use EmrRITrait, MasterPasienTrait;

    // listener dari blade (kalau memang dipakai modul lain)
    protected $listeners = [
        'syncronizeAssessmentDokterRIFindData'  => 'mount',
        'syncronizeAssessmentPerawatRIFindData' => 'mount',
    ];

    //////////////////////////////
    // Ref di top bar
    //////////////////////////////
    public $riHdrNoRef;
    public $regNoRef;

    public array $dataDaftarRi = [];

    public array $agreementOptions = [
        ["agreementId" => "1", "agreementDesc" => "Setuju"],
        ["agreementId" => "0", "agreementDesc" => "Tidak Setuju"],
    ];

    public array $generalConsentPasienRI = [
        'signature'             => '',
        'signatureDate'         => '',
        'wali'                  => '',
        'agreement'             => '1',
        'petugasPemeriksa'      => '',
        'petugasPemeriksaDate'  => '',
        'petugasPemeriksaCode'  => '',
    ];

    public $signature;

    protected $rules = [
        'dataDaftarRi.generalConsentPasienRI.signature'            => 'required',
        'dataDaftarRi.generalConsentPasienRI.signatureDate'        => 'required|date_format:d/m/Y H:i:s',
        'dataDaftarRi.generalConsentPasienRI.wali'                 => 'required',
        'dataDaftarRi.generalConsentPasienRI.agreement'            => 'required|in:0,1',
        'dataDaftarRi.generalConsentPasienRI.petugasPemeriksa'     => 'required',
        'dataDaftarRi.generalConsentPasienRI.petugasPemeriksaDate' => 'required|date_format:d/m/Y H:i:s',
        'dataDaftarRi.generalConsentPasienRI.petugasPemeriksaCode' => 'required',
    ];

    protected $messages = [
        'required'    => ':attribute wajib diisi.',
        'in'          => ':attribute tidak valid.',
        'date_format' => ':attribute harus dengan format dd/mm/yyyy hh:mm:ss',
    ];

    protected $attributes = [
        'dataDaftarRi.generalConsentPasienRI.signature'            => 'Tanda tangan pasien/wali',
        'dataDaftarRi.generalConsentPasienRI.signatureDate'        => 'Waktu tanda tangan',
        'dataDaftarRi.generalConsentPasienRI.wali'                 => 'Nama wali',
        'dataDaftarRi.generalConsentPasienRI.agreement'            => 'Persetujuan',
        'dataDaftarRi.generalConsentPasienRI.petugasPemeriksa'     => 'Petugas pemeriksa',
        'dataDaftarRi.generalConsentPasienRI.petugasPemeriksaDate' => 'Waktu tanda tangan petugas',
        'dataDaftarRi.generalConsentPasienRI.petugasPemeriksaCode' => 'Kode petugas pemeriksa',
    ];

    public function submit()
    {
        // pastikan tanda tangan dari canvas sudah ada
        if (empty($this->signature)) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Tanda tangan pasien/wali belum diisi.');
            return;
        }

        // set tanda tangan dari canvas/signpad di UI
        $this->dataDaftarRi['generalConsentPasienRI']['signature']     = (string)($this->signature ?? '');
        $this->dataDaftarRi['generalConsentPasienRI']['signatureDate'] = Carbon::now(config('app.timezone'))->format('d/m/Y H:i:s');

        // validasi sebelum simpan
        try {
            $this->validate($this->rules, $this->messages, $this->attributes);
        } catch (ValidationException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError($e->validator->errors()->first());
            return;
        }

        $this->store();
    }

    public function store()
    {
        $riHdrNo = $this->dataDaftarRi['riHdrNo'] ?? $this->riHdrNoRef ?? null;
        if (!$riHdrNo) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Nomor Rawat Inap (riHdrNo) kosong.');
            return;
        }

        $lockKey = "ri:{$riHdrNo}";

        try {
            Cache::lock($lockKey, 5)->block(3, function () use ($riHdrNo) {
                DB::transaction(function () use ($riHdrNo) {
                    // ambil data TERBARU agar tidak menimpa modul lain
                    $fresh = $this->findDataRI($riHdrNo) ?: [];

                    if (!isset($fresh['generalConsentPasienRI']) || !is_array($fresh['generalConsentPasienRI'])) {
                        $fresh['generalConsentPasienRI'] = $this->generalConsentPasienRI;
                    }

                    // patch hanya subtree general consent RI
                    $fresh['generalConsentPasienRI'] = array_replace(
                        $fresh['generalConsentPasienRI'],
                        (array)($this->dataDaftarRi['generalConsentPasienRI'] ?? [])
                    );

                    $this->updateJsonRI($riHdrNo, $fresh);
                    $this->dataDaftarRi = $fresh;
                });
            });

            // setelah sukses, sinkronkan form lain jika perlu
            $this->emit('syncronizeAssessmentPerawatRIFindData');
            $this->emit('syncronizeAssessmentDokterRIFindData');

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addSuccess("Signature berhasil disimpan.");
        } catch (LockTimeoutException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Sistem sibuk, gagal memperoleh lock. Coba lagi.');
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Gagal menyimpan General Consent Rawat Inap.');
        }
    }

    private function findData($riHdrNo): void
    {
        $this->dataDaftarRi = $this->findDataRI($riHdrNo) ?: [];

        if (!isset($this->dataDaftarRi['generalConsentPasienRI']) || !is_array($this->dataDaftarRi['generalConsentPasienRI'])) {
            $this->dataDaftarRi['generalConsentPasienRI'] = $this->generalConsentPasienRI;
        }
    }

    public function setPetugasPemeriksa()
    {
        $code = auth()->user()->myuser_code ?? '';
        $name = auth()->user()->myuser_name ?? '';

        if (empty($this->dataDaftarRi['generalConsentPasienRI']['petugasPemeriksa'])) {
            $this->dataDaftarRi['generalConsentPasienRI']['petugasPemeriksa']     = $name;
            $this->dataDaftarRi['generalConsentPasienRI']['petugasPemeriksaCode'] = $code;
            $this->dataDaftarRi['generalConsentPasienRI']['petugasPemeriksaDate'] = Carbon::now(config('app.timezone'))->format('d/m/Y H:i:s');
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError("Signature Petugas Pemeriksa sudah ada.");
        }
    }

    public function cetakGeneralConsentPasienRi()
    {
        // Pastikan data RI terbaru
        if (empty($this->dataDaftarRi)) {
            $this->findData($this->riHdrNoRef);
        }

        // Cek riHdrNo
        $riHdrNo = $this->dataDaftarRi['riHdrNo'] ?? $this->riHdrNoRef ?? null;
        if (!$riHdrNo) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Data Rawat Inap (riHdrNo) tidak ditemukan.');
            return;
        }

        // Ambil regNo untuk data pasien
        $regNo = $this->dataDaftarRi['regNo'] ?? $this->regNoRef ?? null;
        if (!$regNo) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Nomor rekam medis tidak ditemukan.');
            return;
        }

        // Ambil blok general consent RI
        $consent = $this->dataDaftarRi['generalConsentPasienRI'] ?? null;
        if (!$consent || !is_array($consent)) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Data General Consent Rawat Inap belum tersedia.');
            return;
        }

        try {
            // Identitas RS
            $identitasRs = DB::table('rsmst_identitases')
                ->select('int_name', 'int_phone1', 'int_phone2', 'int_fax', 'int_address', 'int_city')
                ->first();

            // Data master pasien
            $dataPasien = $this->findDataMasterPasien($regNo) ?? [];

            // Data untuk view cetak
            $data = [
                'identitasRs' => $identitasRs,
                'dataPasien'  => $dataPasien,
                'dataRi'      => $this->dataDaftarRi,
                'consent'     => $consent,
            ];

            $pdfContent = Pdf::loadView(
                'livewire.cetak.cetak-general-consent-r-i-print',
                $data
            )->output();

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addSuccess('Berhasil mencetak Formulir Persetujuan Umum Rawat Inap.');

            return response()->streamDownload(
                fn() => print($pdfContent),
                'general-consent-ri-' . $riHdrNo . '.pdf'
            );
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Gagal mencetak PDF: ' . $e->getMessage());
        }
    }

    // when new form instance
    public function mount()
    {
        $this->findData($this->riHdrNoRef);
    }

    public function render()
    {
        return view(
            'livewire.emr-r-i.general-consent-pasien-r-i.general-consent-pasien-r-i',
            []
        );
    }
}
