<?php

namespace App\Http\Livewire\EmrUGD\InformConsentPasienUGD;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

use Spatie\ArrayToXml\ArrayToXml;
use App\Http\Traits\EmrUGD\EmrUGDTrait;
use App\Http\Traits\customErrorMessagesTrait;


class InformConsentPasienUGD extends Component
{
    use WithPagination, EmrUGDTrait, customErrorMessagesTrait;


    // listener from blade////////////////
    protected $listeners = [
        'syncronizeAssessmentDokterUGDFindData' => 'mount',
        'syncronizeAssessmentPerawatUGDFindData' => 'mount'
    ];

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
    public array $informConsentPasienUGD = [
        'tindakan' => '',
        'tujuan' => '',
        'resiko' => '',
        'alternatif' => '',
        'dokter' => '',



        'signature' => '',
        'signatureDate' => '',
        'wali' => '',

        'signature' => '',
        'signatureDate' => '',
        'wali' => '',

        'signatureSaksi' => '',
        'signatureSaksiDate' => '',
        'saksi' => '',

        'agreement' => '1',
        'petugasPemeriksa' => '',
        'petugasPemeriksaDate' => '',
        'petugasPemeriksaCode' => '',
    ];
    public $signature;
    public $signatureSaksi;



    protected $rules = [
        'informConsentPasienUGD.tindakan' => 'required',
        'informConsentPasienUGD.tujuan' => 'required',
        'informConsentPasienUGD.resiko' => 'required',
        'informConsentPasienUGD.alternatif' => 'required',
        'informConsentPasienUGD.dokter' => 'required',

        'informConsentPasienUGD.signature' => 'required',
        'informConsentPasienUGD.signatureDate' => 'required|date_format:d/m/Y H:i:s',
        'informConsentPasienUGD.wali' => 'required',

        'informConsentPasienUGD.signatureSaksi' => 'required',
        'informConsentPasienUGD.signatureSaksiDate' => 'required|date_format:d/m/Y H:i:s',
        'informConsentPasienUGD.saksi' => 'required',

        'informConsentPasienUGD.agreement' => 'required',
        'informConsentPasienUGD.petugasPemeriksa' => 'required',
        'informConsentPasienUGD.petugasPemeriksaDate' => 'required|date_format:d/m/Y H:i:s',
        'informConsentPasienUGD.petugasPemeriksaCode' => 'required',
    ];

    protected $attribute = [
        'informConsentPasienUGD.signature' => 'Tanda Tangan Pasien / Wali',
        'informConsentPasienUGD.signatureSaksi' => 'Tanda Tangan Saksi',
        'informConsentPasienUGD.petugasPemeriksa' => 'Petugas Pemeriksa',
        'informConsentPasienUGD.petugasPemeriksaCode' => 'Kode Petugas Pemeriksa',
        'informConsentPasienUGD.wali' => 'Wali',
        'informConsentPasienUGD.agreement' => 'Persetujuan',
    ];

    public function submit()
    {
        $this->informConsentPasienUGD['signature'] = $this->signature;
        $this->informConsentPasienUGD['signatureDate'] = Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s');

        $this->informConsentPasienUGD['signatureSaksi'] = $this->signatureSaksi;
        $this->informConsentPasienUGD['signatureSaksiDate'] = Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s');

        $this->validateDataGeneralConsetPasienRj();
        $this->dataDaftarUgd['informConsentPasienUGD'][]  = $this->informConsentPasienUGD;
        $this->store();
    }
    private function validateDataGeneralConsetPasienRj(): void
    {
        // customErrorMessages
        // $messages = customErrorMessagesTrait::messages();
        $messages = [];

        // Proses Validasi///////////////////////////////////////////
        try {
            $this->validate($this->rules, $messages);
        } catch (\Illuminate\Validation\ValidationException $e) {

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Lakukan Pengecekan kembali Input Data.");
            $this->validate($this->rules, $messages, $this->attribute);
        }
    }
    public function store()
    {
        // Validate UGD

        // Logic update mode start //////////
        $this->updateDataUgd($this->dataDaftarUgd['rjNo']);

        $this->emit('syncronizeAssessmentPerawatUGDFindData');
    }

    private function updateDataUgd($rjNo): void
    {
        $this->updateJsonUGD($rjNo, $this->dataDaftarUgd);

        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Signature berhasil disimpan.");
    }

    private function findData($rjno): void
    {
        $this->dataDaftarUgd = $this->findDataUGD($rjno);

        // dd($this->dataDaftarUgd);
        // jika informConsentPasienUGD tidak ditemukan tambah variable informConsentPasienUGD pda array
        // if (isset($this->informConsentPasienUGD) == false) {
        //     $this->informConsentPasienUGD = $this->informConsentPasienUGD;
        // }
    }

    public function setPetugasPemeriksa()
    {
        // $myRoles = json_decode(auth()->user()->roles, true);
        $myUserCodeActive = auth()->user()->myuser_code;
        $myUserNameActive = auth()->user()->myuser_name;
        // $myUserTtdActive = auth()->user()->myuser_ttd_image;


        if (!$this->informConsentPasienUGD['petugasPemeriksa']) {
            $this->informConsentPasienUGD['petugasPemeriksa'] = $myUserNameActive;
            $this->informConsentPasienUGD['petugasPemeriksaCode'] = $myUserCodeActive;
            $this->informConsentPasienUGD['petugasPemeriksaDate'] = Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s');
            $this->validateDataGeneralConsetPasienRj();
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Signature Petugas Pemeriksa sudah ada.");
        }
    }

    public function setInformConsentPasienUGD($dataInfromConsentPasienUGD)
    {
        $this->informConsentPasienUGD = $dataInfromConsentPasienUGD;
        $dataInfromConsentPasienUGD;
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
