<?php

namespace App\Http\Livewire\EmrRI\InformConsentPasienRI;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

use Spatie\ArrayToXml\ArrayToXml;
use App\Http\Traits\EmrRI\EmrRITrait;
use App\Http\Traits\customErrorMessagesTrait;


class InformConsentPasienRI extends Component
{
    use WithPagination, EmrRITrait, customErrorMessagesTrait;


    // listener from blade////////////////
    protected $listeners = [
        'syncronizeAssessmentDokterRIFindData' => 'mount',
        'syncronizeAssessmentPerawatRIFindData' => 'mount'
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
    public array $informConsentPasienRI = [
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
        'informConsentPasienRI.tindakan' => 'required',
        'informConsentPasienRI.tujuan' => 'required',
        'informConsentPasienRI.resiko' => 'required',
        'informConsentPasienRI.alternatif' => 'required',
        'informConsentPasienRI.dokter' => 'required',

        'informConsentPasienRI.signature' => 'required',
        'informConsentPasienRI.signatureDate' => 'required|date_format:d/m/Y H:i:s',
        'informConsentPasienRI.wali' => 'required',

        'informConsentPasienRI.signatureSaksi' => 'required',
        'informConsentPasienRI.signatureSaksiDate' => 'required|date_format:d/m/Y H:i:s',
        'informConsentPasienRI.saksi' => 'required',

        'informConsentPasienRI.agreement' => 'required',
        'informConsentPasienRI.petugasPemeriksa' => 'required',
        'informConsentPasienRI.petugasPemeriksaDate' => 'required|date_format:d/m/Y H:i:s',
        'informConsentPasienRI.petugasPemeriksaCode' => 'required',
    ];

    protected $attribute = [
        'informConsentPasienRI.signature' => 'Tanda Tangan Pasien / Wali',
        'informConsentPasienRI.signatureSaksi' => 'Tanda Tangan Saksi',
        'informConsentPasienRI.petugasPemeriksa' => 'Petugas Pemeriksa',
        'informConsentPasienRI.petugasPemeriksaCode' => 'Kode Petugas Pemeriksa',
        'informConsentPasienRI.wali' => 'Wali',
        'informConsentPasienRI.agreement' => 'Persetujuan',
    ];

    public function submit()
    {
        $this->informConsentPasienRI['signature'] = $this->signature;
        $this->informConsentPasienRI['signatureDate'] = Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s');

        $this->informConsentPasienRI['signatureSaksi'] = $this->signatureSaksi;
        $this->informConsentPasienRI['signatureSaksiDate'] = Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s');

        $this->validateDataGeneralConsetPasienRi();
        $this->dataDaftarRi['informConsentPasienRI'][]  = $this->informConsentPasienRI;
        $this->store();
    }
    private function validateDataGeneralConsetPasienRi(): void
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
        // Logic update mode start //////////
        $this->updateDataRi($this->dataDaftarRi['riHdrNo']);

        $this->emit('syncronizeAssessmentPerawatRIFindData');
    }

    private function updateDataRi($riHdrNo): void
    {
        $this->updateJsonRI($riHdrNo, $this->dataDaftarRi);

        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Signature berhasil disimpan.");
    }

    private function findData($riHdrNo): void
    {
        $this->dataDaftarRi = $this->findDataRI($riHdrNo);
    }

    public function setPetugasPemeriksa()
    {
        // $myRoles = json_decode(auth()->user()->roles, true);
        $myUserCodeActive = auth()->user()->myuser_code;
        $myUserNameActive = auth()->user()->myuser_name;
        // $myUserTtdActive = auth()->user()->myuser_ttd_image;


        if (!$this->informConsentPasienRI['petugasPemeriksa']) {
            $this->informConsentPasienRI['petugasPemeriksa'] = $myUserNameActive;
            $this->informConsentPasienRI['petugasPemeriksaCode'] = $myUserCodeActive;
            $this->informConsentPasienRI['petugasPemeriksaDate'] = Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s');
            $this->validateDataGeneralConsetPasienRi();
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Signature Petugas Pemeriksa sudah ada.");
        }
    }

    public function setInformConsentPasienRI($dataInfromConsentPasienRI)
    {
        $this->informConsentPasienRI = $dataInfromConsentPasienRI;
        $dataInfromConsentPasienRI;
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
