<?php

namespace App\Http\Livewire\EmrUGD\GeneralConsentPasienUGD;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

use Spatie\ArrayToXml\ArrayToXml;
use App\Http\Traits\EmrUGD\EmrUGDTrait;
use App\Http\Traits\customErrorMessagesTrait;


class GeneralConsentPasienUGD extends Component
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
    public array $generalConsentPasienUGD = [
        'signature' => '',
        'signatureDate' => '',
        'wali' => '',
        'agreement' => '1',
    ];
    public $signature;


    protected $rules = [
        'dataDaftarUgd.generalConsentPasienUGD.signature' => 'required',
        'dataDaftarUgd.generalConsentPasienUGD.signatureDate' => 'required|date_format:d/m/Y H:i:s',
        'dataDaftarUgd.generalConsentPasienUGD.wali' => 'required',
        'dataDaftarUgd.generalConsentPasienUGD.agreement' => 'required',

    ];

    protected $attribute = ['dataDaftarUgd.generalConsentPasienUGD.signature' => 'Tanda Tangan'];

    public function submit()
    {
        $this->dataDaftarUgd['generalConsentPasienUGD']['signature'] = $this->signature;
        $this->dataDaftarUgd['generalConsentPasienUGD']['signatureDate'] = Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s');
        $this->validateDataGeneralConsetPasienUgd();
        $this->store();
    }
    private function validateDataGeneralConsetPasienUgd(): void
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
        $this->updateDataUgd($this->dataDaftarUgd['rjNo']);

        $this->emit('syncronizeAssessmentPerawatUGDFindData');
    }

    private function updateDataUgd($rjNo): void
    {
        // update table trnsaksi
        // DB::table('rstxn_ugdhdrs')
        //     ->where('rj_no', $rjNo)
        //     ->update([
        //         'datadaftarugd_json' => json_encode($this->dataDaftarUgd, true),
        //         'datadaftarUgd_xml' => ArrayToXml::convert($this->dataDaftarUgd),
        //     ]);

        $this->updateJsonUGD($rjNo, $this->dataDaftarUgd);

        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Signature berhasil disimpan.");
    }

    private function findData($rjno): void
    {
        $this->dataDaftarUgd = $this->findDataUGD($rjno);
        // dd($this->dataDaftarUgd);
        // jika generalConsentPasienUGD tidak ditemukan tambah variable generalConsentPasienUGD pda array
        if (isset($this->dataDaftarUgd['generalConsentPasienUGD']) == false) {
            $this->dataDaftarUgd['generalConsentPasienUGD'] = $this->generalConsentPasienUGD;
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
