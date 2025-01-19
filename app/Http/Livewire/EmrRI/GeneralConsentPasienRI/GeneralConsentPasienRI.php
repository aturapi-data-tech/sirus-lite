<?php

namespace App\Http\Livewire\EmrRI\GeneralConsentPasienRI;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

use Spatie\ArrayToXml\ArrayToXml;
use App\Http\Traits\EmrRI\EmrRITrait;
use App\Http\Traits\customErrorMessagesTrait;


class GeneralConsentPasienRI extends Component
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
    public array $generalConsentPasienRI = [
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
        'dataDaftarRi.generalConsentPasienRI.signature' => 'required',
        'dataDaftarRi.generalConsentPasienRI.signatureDate' => 'required|date_format:d/m/Y H:i:s',
        'dataDaftarRi.generalConsentPasienRI.wali' => 'required',
        'dataDaftarRi.generalConsentPasienRI.agreement' => 'required',
        'dataDaftarRi.generalConsentPasienRI.petugasPemeriksa' => 'required',
        'dataDaftarRi.generalConsentPasienRI.petugasPemeriksaDate' => 'required|date_format:d/m/Y H:i:s',
        'dataDaftarRi.generalConsentPasienRI.petugasPemeriksaCode' => 'required',


    ];

    protected $attribute = [
        'dataDaftarRi.generalConsentPasienRI.signature' => 'Tanda Tangan',
        'dataDaftarRi.generalConsentPasienRI.petugasPemeriksa' => 'Petugas Pemeriksa',
        'dataDaftarRi.generalConsentPasienRI.petugasPemeriksaCode' => 'Kode Petugas Pemeriksa',
        'dataDaftarRi.generalConsentPasienRI.wali' => 'Wali',
        'dataDaftarRi.generalConsentPasienRI.agreement' => 'Persetujuan',
    ];

    public function submit()
    {
        $this->dataDaftarRi['generalConsentPasienRI']['signature'] = $this->signature;
        $this->dataDaftarRi['generalConsentPasienRI']['signatureDate'] = Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s');
        $this->validateDataGeneralConsetPasienRi();
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
        // jika generalConsentPasienRI tidak ditemukan tambah variable generalConsentPasienRI pda array
        if (isset($this->dataDaftarRi['generalConsentPasienRI']) == false) {
            $this->dataDaftarRi['generalConsentPasienRI'] = $this->generalConsentPasienRI;
        }
    }

    public function setPetugasPemeriksa()
    {
        // $myRoles = json_decode(auth()->user()->roles, true);
        $myUserCodeActive = auth()->user()->myuser_code;
        $myUserNameActive = auth()->user()->myuser_name;
        // $myUserTtdActive = auth()->user()->myuser_ttd_image;


        if (!$this->dataDaftarRi['generalConsentPasienRI']['petugasPemeriksa']) {
            $this->dataDaftarRi['generalConsentPasienRI']['petugasPemeriksa'] = $myUserNameActive;
            $this->dataDaftarRi['generalConsentPasienRI']['petugasPemeriksaCode'] = $myUserCodeActive;
            $this->dataDaftarRi['generalConsentPasienRI']['petugasPemeriksaDate'] = Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s');
            $this->validateDataGeneralConsetPasienRi();
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Signature Petugas Pemeriksa sudah ada.");
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
            'livewire.emr-r-i.general-consent-pasien-r-i.general-consent-pasien-r-i',
            []
        );
    }
    // select data end////////////////


}
