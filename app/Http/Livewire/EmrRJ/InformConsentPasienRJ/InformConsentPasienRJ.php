<?php

namespace App\Http\Livewire\EmrRJ\InformConsentPasienRJ;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

use Spatie\ArrayToXml\ArrayToXml;
use App\Http\Traits\EmrRJ\EmrRJTrait;
use App\Http\Traits\customErrorMessagesTrait;


class InformConsentPasienRJ extends Component
{
    use WithPagination, EmrRJTrait, customErrorMessagesTrait;


    // listener from blade////////////////
    protected $listeners = [
        'syncronizeAssessmentDokterRJFindData' => 'mount',
        'syncronizeAssessmentPerawatRJFindData' => 'mount'
    ];

    //////////////////////////////
    // Ref on top bar
    //////////////////////////////
    public $rjNoRef;
    public $regNoRef;
    public array $dataDaftarPoliRJ = [];
    public array $agreementOptions = [
        ["agreementId" => "1", "agreementDesc" => "Setuju"],
        ["agreementId" => "0", "agreementDesc" => "Tidak Setuju"],
    ];
    public array $informConsentPasienRJ = [
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
        'informConsentPasienRJ.tindakan' => 'required',
        'informConsentPasienRJ.tujuan' => 'required',
        'informConsentPasienRJ.resiko' => 'required',
        'informConsentPasienRJ.alternatif' => 'required',
        'informConsentPasienRJ.dokter' => 'required',

        'informConsentPasienRJ.signature' => 'required',
        'informConsentPasienRJ.signatureDate' => 'required|date_format:d/m/Y H:i:s',
        'informConsentPasienRJ.wali' => 'required',

        'informConsentPasienRJ.signatureSaksi' => 'required',
        'informConsentPasienRJ.signatureSaksiDate' => 'required|date_format:d/m/Y H:i:s',
        'informConsentPasienRJ.saksi' => 'required',

        'informConsentPasienRJ.agreement' => 'required',
        'informConsentPasienRJ.petugasPemeriksa' => 'required',
        'informConsentPasienRJ.petugasPemeriksaDate' => 'required|date_format:d/m/Y H:i:s',
        'informConsentPasienRJ.petugasPemeriksaCode' => 'required',
    ];

    protected $attribute = [
        'informConsentPasienRJ.signature' => 'Tanda Tangan Pasien / Wali',
        'informConsentPasienRJ.signatureSaksi' => 'Tanda Tangan Saksi',
        'informConsentPasienRJ.petugasPemeriksa' => 'Petugas Pemeriksa',
        'informConsentPasienRJ.petugasPemeriksaCode' => 'Kode Petugas Pemeriksa',
        'informConsentPasienRJ.wali' => 'Wali',
        'informConsentPasienRJ.agreement' => 'Persetujuan',
    ];

    public function submit()
    {
        $this->informConsentPasienRJ['signature'] = $this->signature;
        $this->informConsentPasienRJ['signatureDate'] = Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s');

        $this->informConsentPasienRJ['signatureSaksi'] = $this->signatureSaksi;
        $this->informConsentPasienRJ['signatureSaksiDate'] = Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s');

        $this->validateDataGeneralConsetPasienRj();
        $this->dataDaftarPoliRJ['informConsentPasienRJ'][]  = $this->informConsentPasienRJ;
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
        // Validate RJ

        // Logic update mode start //////////
        $this->updateDataRj($this->dataDaftarPoliRJ['rjNo']);

        $this->emit('syncronizeAssessmentPerawatRJFindData');
    }

    private function updateDataRj($rjNo): void
    {

        $this->updateJsonRJ($rjNo, $this->dataDaftarPoliRJ);

        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Signature berhasil disimpan.");
    }

    private function findData($rjno): void
    {
        $findDataRJ = $this->findDataRJ($rjno);

        $this->dataDaftarPoliRJ  = $findDataRJ['dataDaftarRJ'];
        // dd($this->dataDaftarPoliRJ);
        // jika informConsentPasienRJ tidak ditemukan tambah variable informConsentPasienRJ pda array
        // if (isset($this->informConsentPasienRJ) == false) {
        //     $this->informConsentPasienRJ = $this->informConsentPasienRJ;
        // }
    }

    public function setPetugasPemeriksa()
    {
        // $myRoles = json_decode(auth()->user()->roles, true);
        $myUserCodeActive = auth()->user()->myuser_code;
        $myUserNameActive = auth()->user()->myuser_name;
        // $myUserTtdActive = auth()->user()->myuser_ttd_image;


        if (!$this->informConsentPasienRJ['petugasPemeriksa']) {
            $this->informConsentPasienRJ['petugasPemeriksa'] = $myUserNameActive;
            $this->informConsentPasienRJ['petugasPemeriksaCode'] = $myUserCodeActive;
            $this->informConsentPasienRJ['petugasPemeriksaDate'] = Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s');
            $this->validateDataGeneralConsetPasienRj();
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Signature Petugas Pemeriksa sudah ada.");
        }
    }

    public function setInformConsentPasienRJ($dataInfromConsentPasienRJ)
    {
        $this->informConsentPasienRJ = $dataInfromConsentPasienRJ;
        $dataInfromConsentPasienRJ;
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
            'livewire.emr-r-j.inform-consent-pasien-r-j.inform-consent-pasien-r-j',
            []
        );
    }
    // select data end////////////////


}
