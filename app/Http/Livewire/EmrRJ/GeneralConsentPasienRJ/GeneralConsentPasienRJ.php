<?php

namespace App\Http\Livewire\EmrRJ\GeneralConsentPasienRJ;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

use Spatie\ArrayToXml\ArrayToXml;
use App\Http\Traits\EmrRJ\EmrRJTrait;
use App\Http\Traits\customErrorMessagesTrait;


class GeneralConsentPasienRJ extends Component
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
    public array $generalConsentPasienRJ = [
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
        'dataDaftarPoliRJ.generalConsentPasienRJ.signature' => 'required',
        'dataDaftarPoliRJ.generalConsentPasienRJ.signatureDate' => 'required|date_format:d/m/Y H:i:s',
        'dataDaftarPoliRJ.generalConsentPasienRJ.wali' => 'required',
        'dataDaftarPoliRJ.generalConsentPasienRJ.agreement' => 'required',
        'dataDaftarPoliRJ.generalConsentPasienRJ.petugasPemeriksa' => 'required',
        'dataDaftarPoliRJ.generalConsentPasienRJ.petugasPemeriksaDate' => 'required|date_format:d/m/Y H:i:s',
        'dataDaftarPoliRJ.generalConsentPasienRJ.petugasPemeriksaCode' => 'required',
    ];

    protected $attribute = [
        'dataDaftarPoliRJ.generalConsentPasienRJ.signature' => 'Tanda Tangan',
        'dataDaftarPoliRJ.generalConsentPasienRJ.petugasPemeriksa' => 'Petugas Pemeriksa',
        'dataDaftarPoliRJ.generalConsentPasienRJ.petugasPemeriksaCode' => 'Kode Petugas Pemeriksa',
        'dataDaftarPoliRJ.generalConsentPasienRJ.wali' => 'Wali',
        'dataDaftarPoliRJ.generalConsentPasienRJ.agreement' => 'Persetujuan',
    ];

    public function submit()
    {
        $this->dataDaftarPoliRJ['generalConsentPasienRJ']['signature'] = $this->signature;
        $this->dataDaftarPoliRJ['generalConsentPasienRJ']['signatureDate'] = Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s');
        $this->validateDataGeneralConsetPasienRj();
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
        $this->updateDataUgd($this->dataDaftarPoliRJ['rjNo']);

        $this->emit('syncronizeAssessmentPerawatRJFindData');
    }

    private function updateDataUgd($rjNo): void
    {
        // update table trnsaksi
        // DB::table('rstxn_ugdhdrs')
        //     ->where('rj_no', $rjNo)
        //     ->update([
        //         'datadaftarugd_json' => json_encode($this->dataDaftarPoliRJ, true),
        //         'datadaftarUgd_xml' => ArrayToXml::convert($this->dataDaftarPoliRJ),
        //     ]);

        $this->updateJsonRJ($rjNo, $this->dataDaftarPoliRJ);

        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Signature berhasil disimpan.");
    }

    private function findData($rjno): void
    {
        $findDataRJ = $this->findDataRJ($rjno);

        $this->dataDaftarPoliRJ  = $findDataRJ['dataDaftarRJ'];
        // dd($this->dataDaftarPoliRJ);
        // jika generalConsentPasienRJ tidak ditemukan tambah variable generalConsentPasienRJ pda array
        if (isset($this->dataDaftarPoliRJ['generalConsentPasienRJ']) == false) {
            $this->dataDaftarPoliRJ['generalConsentPasienRJ'] = $this->generalConsentPasienRJ;
        }
    }

    public function setPetugasPemeriksa()
    {
        // $myRoles = json_decode(auth()->user()->roles, true);
        $myUserCodeActive = auth()->user()->myuser_code;
        $myUserNameActive = auth()->user()->myuser_name;
        // $myUserTtdActive = auth()->user()->myuser_ttd_image;


        if (!$this->dataDaftarPoliRJ['generalConsentPasienRJ']['petugasPemeriksa']) {
            $this->dataDaftarPoliRJ['generalConsentPasienRJ']['petugasPemeriksa'] = $myUserNameActive;
            $this->dataDaftarPoliRJ['generalConsentPasienRJ']['petugasPemeriksaCode'] = $myUserCodeActive;
            $this->dataDaftarPoliRJ['generalConsentPasienRJ']['petugasPemeriksaDate'] = Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s');
            $this->validateDataGeneralConsetPasienRj();
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Signature Petugas Pemeriksa sudah ada.");
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
            'livewire.emr-r-j.general-consent-pasien-r-j.general-consent-pasien-r-j',
            []
        );
    }
    // select data end////////////////


}
