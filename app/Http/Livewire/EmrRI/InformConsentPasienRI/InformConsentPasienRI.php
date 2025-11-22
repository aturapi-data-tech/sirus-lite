<?php

namespace App\Http\Livewire\EmrRI\InformConsentPasienRI;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Validation\ValidationException;

use Livewire\Component;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Http\Traits\EmrRI\EmrRITrait;
use App\Http\Traits\MasterPasien\MasterPasienTrait;

class InformConsentPasienRI extends Component
{
    use EmrRITrait, MasterPasienTrait;

    // listener from blade////////////////
    protected $listeners = [
        'syncronizeAssessmentDokterRIFindData'  => 'mount',
        'syncronizeAssessmentPerawatRIFindData' => 'mount',
    ];

    //////////////////////////////
    // Ref on top bar
    //////////////////////////////
    public $riHdrNoRef;
    public $regNoRef;

    public array $dataDaftarRi = [];

    public array $agreementOptions = [
        ["agreementId" => "1", "agreementDesc" => "Setuju"],
        ["agreementId" => "0", "agreementDesc" => "Tidak Setuju"],
    ];

    // Satu entri consent RI
    public array $informConsentPasienRI = [
        'tindakan'   => '',
        'tujuan'     => '',
        'resiko'     => '',
        'alternatif' => '',
        'dokter'     => '',

        'signature'     => '',
        'signatureDate' => '',
        'wali'          => '',

        'signatureSaksi'     => '',
        'signatureSaksiDate' => '',
        'saksi'              => '',

        'agreement'            => '1',
        'petugasPemeriksa'     => '',
        'petugasPemeriksaDate' => '',
        'petugasPemeriksaCode' => '',
    ];

    public $signature;
    public $signatureSaksi;

    protected $rules = [
        'informConsentPasienRI.tindakan'   => 'required',
        'informConsentPasienRI.tujuan'     => 'required',
        'informConsentPasienRI.resiko'     => 'required',
        'informConsentPasienRI.alternatif' => 'required',
        'informConsentPasienRI.dokter'     => 'required',

        'informConsentPasienRI.signature'     => 'required',
        'informConsentPasienRI.signatureDate' => 'required|date_format:d/m/Y H:i:s',
        'informConsentPasienRI.wali'          => 'required',

        'informConsentPasienRI.signatureSaksi'     => 'required',
        'informConsentPasienRI.signatureSaksiDate' => 'required|date_format:d/m/Y H:i:s',
        'informConsentPasienRI.saksi'              => 'required',

        'informConsentPasienRI.agreement'            => 'required|in:0,1',
        'informConsentPasienRI.petugasPemeriksa'     => 'required',
        'informConsentPasienRI.petugasPemeriksaDate' => 'required|date_format:d/m/Y H:i:s',
        'informConsentPasienRI.petugasPemeriksaCode' => 'required',
    ];

    protected $messages = [
        'required'    => ':attribute wajib diisi.',
        'in'          => ':attribute tidak valid.',
        'date_format' => ':attribute harus dengan format dd/mm/yyyy hh:mm:ss',
    ];

    protected $attributes = [
        'informConsentPasienRI.tindakan'   => 'Tindakan',
        'informConsentPasienRI.tujuan'     => 'Tujuan',
        'informConsentPasienRI.resiko'     => 'Risiko',
        'informConsentPasienRI.alternatif' => 'Alternatif',
        'informConsentPasienRI.dokter'     => 'Dokter',

        'informConsentPasienRI.signature'     => 'Tanda tangan pasien/wali',
        'informConsentPasienRI.signatureDate' => 'Waktu tanda tangan pasien/wali',
        'informConsentPasienRI.wali'          => 'Nama wali',

        'informConsentPasienRI.signatureSaksi'     => 'Tanda tangan saksi',
        'informConsentPasienRI.signatureSaksiDate' => 'Waktu tanda tangan saksi',
        'informConsentPasienRI.saksi'              => 'Nama saksi',

        'informConsentPasienRI.agreement'            => 'Persetujuan',
        'informConsentPasienRI.petugasPemeriksa'     => 'Petugas pemeriksa',
        'informConsentPasienRI.petugasPemeriksaDate' => 'Waktu tanda tangan petugas',
        'informConsentPasienRI.petugasPemeriksaCode' => 'Kode petugas pemeriksa',
    ];

    public function submit()
    {
        if (!$this->signature) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Tanda tangan pasien/wali belum diisi.');
            return;
        }

        if (!$this->signatureSaksi) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Tanda tangan saksi belum diisi.');
            return;
        }

        // set tanda tangan dari UI
        $this->informConsentPasienRI['signature']     = (string)($this->signature ?? '');
        $this->informConsentPasienRI['signatureDate'] = Carbon::now(config('app.timezone'))->format('d/m/Y H:i:s');

        $this->informConsentPasienRI['signatureSaksi']     = (string)($this->signatureSaksi ?? '');
        $this->informConsentPasienRI['signatureSaksiDate'] = Carbon::now(config('app.timezone'))->format('d/m/Y H:i:s');

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

                    // siapkan list consent RI
                    if (!isset($fresh['informConsentPasienRI']) || !is_array($fresh['informConsentPasienRI'])) {
                        $fresh['informConsentPasienRI'] = [];
                    }

                    // idempoten sederhana (berdasar timestamp tanda tangan pasien)
                    $exists = collect($fresh['informConsentPasienRI'])
                        ->firstWhere('signatureDate', $this->informConsentPasienRI['signatureDate']);

                    if (!$exists) {
                        $fresh['informConsentPasienRI'][] = $this->informConsentPasienRI;
                    }

                    $this->updateJsonRI($riHdrNo, $fresh);
                    $this->dataDaftarRi = $fresh;
                });
            });

            // reset form entry
            $this->informConsentPasienRI = $this->defaultConsent();
            $this->signature = null;
            $this->signatureSaksi = null;

            // kalau mau sinkron ke form lain di RI
            $this->emit('syncronizeAssessmentPerawatRIFindData');
            $this->emit('syncronizeAssessmentDokterRIFindData');

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addSuccess('Inform consent Rawat Inap tersimpan.');
        } catch (LockTimeoutException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Sistem sibuk, gagal memperoleh lock. Coba lagi.');
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Gagal menyimpan Inform Consent Rawat Inap.');
        }
    }

    private function findData($riHdrNo): void
    {
        $this->dataDaftarRi = $this->findDataRI($riHdrNo) ?: [];

        // pastikan berbentuk list (array) of consent
        if (!isset($this->dataDaftarRi['informConsentPasienRI']) || !is_array($this->dataDaftarRi['informConsentPasienRI'])) {
            $this->dataDaftarRi['informConsentPasienRI'] = [];
        }
    }

    public function setPetugasPemeriksa()
    {
        $code = auth()->user()->myuser_code ?? '';
        $name = auth()->user()->myuser_name ?? '';

        if (empty($this->informConsentPasienRI['petugasPemeriksa'])) {
            $this->informConsentPasienRI['petugasPemeriksa']     = $name;
            $this->informConsentPasienRI['petugasPemeriksaCode'] = $code;
            $this->informConsentPasienRI['petugasPemeriksaDate'] = Carbon::now(config('app.timezone'))->format('d/m/Y H:i:s');
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError("Signature Petugas Pemeriksa sudah ada.");
        }
    }

    private function defaultConsent(): array
    {
        return [
            'tindakan'   => '',
            'tujuan'     => '',
            'resiko'     => '',
            'alternatif' => '',
            'dokter'     => '',

            'signature'     => '',
            'signatureDate' => '',
            'wali'          => '',

            'signatureSaksi'     => '',
            'signatureSaksiDate' => '',
            'saksi'              => '',

            'agreement'            => '1',
            'petugasPemeriksa'     => '',
            'petugasPemeriksaDate' => '',
            'petugasPemeriksaCode' => '',
        ];
    }

    public function cetakInformConsentPasienRi(string $signatureDate)
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

        // Cek regNo
        $regNo = $this->dataDaftarRi['regNo'] ?? $this->regNoRef ?? null;
        if (!$regNo) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Nomor rekam medis tidak ditemukan.');
            return;
        }

        // Ambil list consent dari data RI
        $listConsent = $this->dataDaftarRi['informConsentPasienRI'] ?? [];

        if (!is_array($listConsent) || count($listConsent) === 0) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Data Inform Consent Rawat Inap belum tersedia.');
            return;
        }

        // Cari consent berdasarkan signatureDate
        $consent = collect($listConsent)->firstWhere('signatureDate', $signatureDate);

        if (!$consent) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Data persetujuan yang dipilih tidak ditemukan.');
            return;
        }

        try {
            // Identitas RS
            $identitasRs = DB::table('rsmst_identitases')
                ->select('int_name', 'int_phone1', 'int_phone2', 'int_fax', 'int_address', 'int_city')
                ->first();

            // Data master pasien
            $dataPasien = $this->findDataMasterPasien($regNo) ?? [];

            $data = [
                'identitasRs' => $identitasRs,
                'dataPasien'  => $dataPasien,
                'dataRi'      => $this->dataDaftarRi,
                'consent'     => $consent,
            ];

            $pdfContent = Pdf::loadView(
                'livewire.cetak.cetak-inform-consent-r-i-print',
                $data
            )->output();

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addSuccess('Berhasil mencetak Formulir Persetujuan / Penolakan Tindakan Medis Rawat Inap.');

            return response()->streamDownload(function () use ($pdfContent) {
                echo $pdfContent;
            }, 'inform-consent-ri-' . $riHdrNo . '.pdf');
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

    // select data start////////////////
    public function render()
    {
        return view(
            'livewire.emr-r-i.inform-consent-pasien-r-i.inform-consent-pasien-r-i',
            []
        );
    }
    // select data end////////////////
}
