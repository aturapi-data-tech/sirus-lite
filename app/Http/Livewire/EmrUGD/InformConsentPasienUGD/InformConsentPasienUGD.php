<?php

namespace App\Http\Livewire\EmrUGD\InformConsentPasienUGD;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Validation\ValidationException;

use Livewire\Component;
use Carbon\Carbon;

use App\Http\Traits\EmrUGD\EmrUGDTrait;


class InformConsentPasienUGD extends Component
{
    use  EmrUGDTrait;


    //////////////////////////////
    // Ref on top bar
    //////////////////////////////
    public $rjNoRef;
    public $regNoRef;
    public array $dataDaftarUgd = [];


    public array $agreementOptions = [
        ["agreementId" => "1", "agreementDesc" => "Setuju"],
        ["agreementId" => "0", "agreementDesc" => "Tidak Setuju"],
    ];

    // Satu entri consent
    public array $informConsentPasienUGD = [
        'tindakan'  => '',
        'tujuan'    => '',
        'resiko'    => '',
        'alternatif' => '',
        'dokter'    => '',

        'signature'      => '',
        'signatureDate'  => '',
        'wali'           => '',

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
        'informConsentPasienUGD.tindakan'   => 'required',
        'informConsentPasienUGD.tujuan'     => 'required',
        'informConsentPasienUGD.resiko'     => 'required',
        'informConsentPasienUGD.alternatif' => 'required',
        'informConsentPasienUGD.dokter'     => 'required',

        'informConsentPasienUGD.signature'     => 'required',
        'informConsentPasienUGD.signatureDate' => 'required|date_format:d/m/Y H:i:s',
        'informConsentPasienUGD.wali'          => 'required',

        'informConsentPasienUGD.signatureSaksi'     => 'required',
        'informConsentPasienUGD.signatureSaksiDate' => 'required|date_format:d/m/Y H:i:s',
        'informConsentPasienUGD.saksi'              => 'required',

        'informConsentPasienUGD.agreement'            => 'required|in:0,1',
        'informConsentPasienUGD.petugasPemeriksa'     => 'required',
        'informConsentPasienUGD.petugasPemeriksaDate' => 'required|date_format:d/m/Y H:i:s',
        'informConsentPasienUGD.petugasPemeriksaCode' => 'required',
    ];

    protected $messages = [
        'required'    => ':attribute wajib diisi.',
        'in'          => ':attribute tidak valid.',
        'date_format' => ':attribute harus dengan format dd/mm/yyyy hh:mm:ss',
    ];

    protected $attributes = [
        'informConsentPasienUGD.tindakan'   => 'Tindakan',
        'informConsentPasienUGD.tujuan'     => 'Tujuan',
        'informConsentPasienUGD.resiko'     => 'Risiko',
        'informConsentPasienUGD.alternatif' => 'Alternatif',
        'informConsentPasienUGD.dokter'     => 'Dokter',

        'informConsentPasienUGD.signature'     => 'Tanda tangan pasien/wali',
        'informConsentPasienUGD.signatureDate' => 'Waktu tanda tangan pasien/wali',
        'informConsentPasienUGD.wali'          => 'Nama wali',

        'informConsentPasienUGD.signatureSaksi'     => 'Tanda tangan saksi',
        'informConsentPasienUGD.signatureSaksiDate' => 'Waktu tanda tangan saksi',
        'informConsentPasienUGD.saksi'              => 'Nama saksi',

        'informConsentPasienUGD.agreement'            => 'Persetujuan',
        'informConsentPasienUGD.petugasPemeriksa'     => 'Petugas pemeriksa',
        'informConsentPasienUGD.petugasPemeriksaDate' => 'Waktu tanda tangan petugas',
        'informConsentPasienUGD.petugasPemeriksaCode' => 'Kode petugas pemeriksa',
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
        $this->informConsentPasienUGD['signature']     = (string)($this->signature ?? '');
        $this->informConsentPasienUGD['signatureDate'] = Carbon::now(config('app.timezone'))->format('d/m/Y H:i:s');

        $this->informConsentPasienUGD['signatureSaksi']     = (string)($this->signatureSaksi ?? '');
        $this->informConsentPasienUGD['signatureSaksiDate'] = Carbon::now(config('app.timezone'))->format('d/m/Y H:i:s');

        try {
            $this->validate($this->rules, $this->messages, $this->attributes);
        } catch (ValidationException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError($e->validator->errors()->first());
            return;
        }

        // masukkan satu entri ke list
        $this->store();
    }


    public function store()
    {


        $rjNo = $this->dataDaftarUgd['rjNo'] ?? $this->rjNoRef ?? null;
        if (!$rjNo) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('rjNo kosong.');
            return;
        }

        $lockKey = "ugd:{$rjNo}";
        try {
            Cache::lock($lockKey, 5)->block(3, function () use ($rjNo) {
                DB::transaction(function () use ($rjNo) {
                    // ambil data TERBARU
                    $fresh = $this->findDataUGD($rjNo) ?: [];

                    // siapkan list
                    if (!isset($fresh['informConsentPasienUGD']) || !is_array($fresh['informConsentPasienUGD'])) {
                        $fresh['informConsentPasienUGD'] = [];
                    }

                    // idempoten sederhana (berdasar timestamp tanda tangan pasien)
                    $exists = collect($fresh['informConsentPasienUGD'])
                        ->firstWhere('signatureDate', $this->informConsentPasienUGD['signatureDate']);

                    if (!$exists) {
                        $fresh['informConsentPasienUGD'][] = $this->informConsentPasienUGD;
                    }

                    $this->updateJsonUGD($rjNo, $fresh);
                    $this->dataDaftarUgd = $fresh;
                });
            });

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addSuccess("Inform consent tersimpan.");
            $this->informConsentPasienUGD = $this->defaultConsent();
            $this->signature = null;
            $this->signatureSaksi = null;
        } catch (LockTimeoutException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Sistem sibuk, gagal memperoleh lock. Coba lagi.');
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Gagal menyimpan Inform Consent.');
        }
    }



    private function findData($rjno): void
    {
        $this->dataDaftarUgd = $this->findDataUGD($rjno) ?: [];

        if (!isset($this->dataDaftarUgd['informConsentPasienUGD']) || !is_array($this->dataDaftarUgd['informConsentPasienUGD'])) {
            // list of consent entries
            $this->dataDaftarUgd['informConsentPasienUGD'] = [];
        }
    }

    public function setPetugasPemeriksa()
    {
        $code = auth()->user()->myuser_code ?? '';
        $name = auth()->user()->myuser_name ?? '';

        if (empty($this->informConsentPasienUGD['petugasPemeriksa'])) {
            $this->informConsentPasienUGD['petugasPemeriksa']     = $name;
            $this->informConsentPasienUGD['petugasPemeriksaCode'] = $code;
            $this->informConsentPasienUGD['petugasPemeriksaDate'] = Carbon::now(config('app.timezone'))->format('d/m/Y H:i:s');
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError("Signature Petugas Pemeriksa sudah ada.");
        }
    }

    private function defaultConsent(): array
    {
        return [
            'tindakan'  => '',
            'tujuan'    => '',
            'resiko'    => '',
            'alternatif' => '',
            'dokter'    => '',

            'signature'      => '',
            'signatureDate'  => '',
            'wali'           => '',

            'signatureSaksi'     => '',
            'signatureSaksiDate' => '',
            'saksi'              => '',

            'agreement'            => '1',
            'petugasPemeriksa'     => '',
            'petugasPemeriksaDate' => '',
            'petugasPemeriksaCode' => '',
        ];
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
            'livewire.emr-u-g-d.inform-consent-pasien-u-g-d.inform-consent-pasien-u-g-d',
            []
        );
    }
    // select data end////////////////


}
