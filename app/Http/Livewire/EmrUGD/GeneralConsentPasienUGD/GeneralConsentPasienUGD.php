<?php

namespace App\Http\Livewire\EmrUGD\GeneralConsentPasienUGD;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Carbon\Carbon;

use App\Http\Traits\EmrUGD\EmrUGDTrait;


class GeneralConsentPasienUGD extends Component
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
    public array $generalConsentPasienUGD = [
        'signature' => '',
        'signatureDate' => '',
        'wali' => '',
        'agreement' => '1',
        'petugasPemeriksa' => '',
        'petugasPemeriksaDate' => '',
        'petugasPemeriksaCode' => '',
    ];
    public $signature;

    protected $rules = [
        'dataDaftarUgd.generalConsentPasienUGD.signature'            => 'required',
        'dataDaftarUgd.generalConsentPasienUGD.signatureDate'        => 'required|date_format:d/m/Y H:i:s',
        'dataDaftarUgd.generalConsentPasienUGD.wali'                 => 'required',
        'dataDaftarUgd.generalConsentPasienUGD.agreement'            => 'required|in:0,1',
        'dataDaftarUgd.generalConsentPasienUGD.petugasPemeriksa'     => 'required',
        'dataDaftarUgd.generalConsentPasienUGD.petugasPemeriksaDate' => 'required|date_format:d/m/Y H:i:s',
        'dataDaftarUgd.generalConsentPasienUGD.petugasPemeriksaCode' => 'required',
    ];

    protected $messages = [
        'required'    => ':attribute wajib diisi.',
        'in'          => ':attribute tidak valid.',
        'date_format' => ':attribute harus dengan format dd/mm/yyyy hh:mm:ss',
    ];

    // pakai nama $attributes (param ke-3 validate)
    protected $attributes = [
        'dataDaftarUgd.generalConsentPasienUGD.signature'            => 'Tanda tangan pasien/wali',
        'dataDaftarUgd.generalConsentPasienUGD.signatureDate'        => 'Waktu tanda tangan',
        'dataDaftarUgd.generalConsentPasienUGD.wali'                 => 'Nama wali',
        'dataDaftarUgd.generalConsentPasienUGD.agreement'            => 'Persetujuan',
        'dataDaftarUgd.generalConsentPasienUGD.petugasPemeriksa'     => 'Petugas pemeriksa',
        'dataDaftarUgd.generalConsentPasienUGD.petugasPemeriksaDate' => 'Waktu tanda tangan petugas',
        'dataDaftarUgd.generalConsentPasienUGD.petugasPemeriksaCode' => 'Kode petugas pemeriksa',
    ];

    public function submit()
    {

        if (empty($this->signature)) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Tanda tangan pasien/wali belum diisi.');
            return;
        }

        // set tanda tangan dari canvas/signpad di UI
        $this->dataDaftarUgd['generalConsentPasienUGD']['signature']     = (string)($this->signature ?? '');
        $this->dataDaftarUgd['generalConsentPasienUGD']['signatureDate'] = Carbon::now(config('app.timezone'))->format('d/m/Y H:i:s');

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


        $rjNo = $this->dataDaftarUgd['rjNo'] ?? $this->rjNoRef ?? null;
        if (!$rjNo) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('rjNo kosong.');
            return;
        }

        $lockKey = "ugd:{$rjNo}";
        try {
            Cache::lock($lockKey, 5)->block(3, function () use ($rjNo) {
                DB::transaction(function () use ($rjNo) {
                    // ambil data TERBARU agar tidak menimpa modul lain
                    $fresh = $this->findDataUGD($rjNo) ?: [];

                    if (!isset($fresh['generalConsentPasienUGD']) || !is_array($fresh['generalConsentPasienUGD'])) {
                        $fresh['generalConsentPasienUGD'] = $this->generalConsentPasienUGD;
                    }

                    // patch hanya subtree general consent
                    $fresh['generalConsentPasienUGD'] = array_replace(
                        $fresh['generalConsentPasienUGD'],
                        (array)($this->dataDaftarUgd['generalConsentPasienUGD'] ?? [])
                    );

                    $this->updateJsonUGD($rjNo, $fresh);
                    $this->dataDaftarUgd = $fresh;
                });
            });

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addSuccess("Signature berhasil disimpan.");
        } catch (LockTimeoutException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Sistem sibuk, gagal memperoleh lock. Coba lagi.');
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Gagal menyimpan General Consent.');
        }
    }

    private function findData($rjno): void
    {
        $this->dataDaftarUgd = $this->findDataUGD($rjno) ?: [];

        if (!isset($this->dataDaftarUgd['generalConsentPasienUGD']) || !is_array($this->dataDaftarUgd['generalConsentPasienUGD'])) {
            $this->dataDaftarUgd['generalConsentPasienUGD'] = $this->generalConsentPasienUGD;
        }
    }

    public function setPetugasPemeriksa()
    {
        $code = auth()->user()->myuser_code ?? '';
        $name = auth()->user()->myuser_name ?? '';

        if (empty($this->dataDaftarUgd['generalConsentPasienUGD']['petugasPemeriksa'])) {
            $this->dataDaftarUgd['generalConsentPasienUGD']['petugasPemeriksa']     = $name;
            $this->dataDaftarUgd['generalConsentPasienUGD']['petugasPemeriksaCode'] = $code;
            $this->dataDaftarUgd['generalConsentPasienUGD']['petugasPemeriksaDate'] = Carbon::now(config('app.timezone'))->format('d/m/Y H:i:s');
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError("Signature Petugas Pemeriksa sudah ada.");
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
            'livewire.emr-u-g-d.general-consent-pasien-u-g-d.general-consent-pasien-u-g-d',
            []
        );
    }
    // select data end////////////////


}
