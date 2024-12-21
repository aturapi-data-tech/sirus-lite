<?php

namespace App\Http\Livewire\EmrRJ\MrRJ\Suket;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Traits\EmrRJ\EmrRJTrait;


use Spatie\ArrayToXml\ArrayToXml;


class Suket extends Component
{
    use WithPagination, EmrRJTrait;

    // listener from blade////////////////
    protected $listeners = [
        'syncronizeAssessmentPerawatRJFindData' => 'mount'

    ];

    //////////////////////////////
    // Ref on top bar
    //////////////////////////////
    public $rjNoRef;

    // dataDaftarPoliRJ RJ
    public array $dataDaftarPoliRJ = [];

    // data SKDP / suket=>[]
    public array $suket =
    [
        "suketSehatTab" => "Suket Sehat",
        "suketSehat" => [
            "suketSehat" => ""
        ],
        "suketIstirahatTab" => "Suket Istirahat",
        "suketIstirahat" => [
            "suketIstirahatHari" => "",
            "suketIstirahat" => ""
        ],

    ];
    //////////////////////////////////////////////////////////////////////


    protected $rules = [

        // 'dataDaftarPoliRJ.suket.pengkajianMedis.waktuPemeriksaan' => 'required|date_format:d/m/Y H:i:s',
        // 'dataDaftarPoliRJ.suket.pengkajianMedis.selesaiPemeriksaan' => 'required|date_format:d/m/Y H:i:s'
        'dataDaftarPoliRJ.suket.suketIstirahat.suketIstirahatHari' => 'numeric'


    ];



    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////
    public function updated($propertyName)
    {
        // dd($propertyName);
        $this->validateOnly($propertyName);
        $this->store();
    }




    // resert input private////////////////
    private function resetInputFields(): void
    {

        // resert validation
        $this->resetValidation();
        // resert input kecuali
        $this->resetExcept([
            'rjNoRef'
        ]);
    }





    // ////////////////
    // RJ Logic
    // ////////////////


    // validate Data RJ//////////////////////////////////////////////////
    private function validateDataRJ(): void
    {
        // customErrorMessages
        // $messages = customErrorMessagesTrait::messages();
        $messages = [];


        // $rules = [];



        // Proses Validasi///////////////////////////////////////////
        try {
            $this->validate($this->rules, $messages);
        } catch (\Illuminate\Validation\ValidationException $e) {

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Lakukan Pengecekan kembali Input Data.");
            $this->validate($this->rules, $messages);
        }
    }


    // insert and update record start////////////////
    public function store()
    {
        // set data RJno / NoBooking / NoAntrian / klaimId / kunjunganId
        $this->setDataPrimer();

        // Validate RJ
        $this->validateDataRJ();

        // Logic update mode start //////////
        $this->updateDataRJ($this->dataDaftarPoliRJ['rjNo']);
        $this->emit('syncronizeAssessmentPerawatRJFindData');
    }

    private function updateDataRJ($rjNo): void
    {
        // if ($rjNo !== $this->dataDaftarPoliRJ['rjNo']) {
        //     dd('Data Json Tidak sesuai' . $rjNo . '  /  ' . $this->dataDaftarPoliRJ['rjNo']);
        // }

        // // update table trnsaksi
        // DB::table('rstxn_rjhdrs')
        //     ->where('rj_no', $rjNo)
        //     ->update([
        //         'dataDaftarPoliRJ_json' => json_encode($this->dataDaftarPoliRJ, true),
        //         'dataDaftarPoliRJ_xml' => ArrayToXml::convert($this->dataDaftarPoliRJ),

        //     ]);
        $this->updateJsonRJ($rjNo, $this->dataDaftarPoliRJ);

        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("suket berhasil disimpan.");
    }
    // insert and update record end////////////////


    private function findData($rjno): void
    {


        $findDataRJ = $this->findDataRJ($rjno);
        $this->dataDaftarPoliRJ  = $findDataRJ['dataDaftarRJ'];

        // jika suket tidak ditemukan tambah variable suket pda array
        if (isset($this->dataDaftarPoliRJ['suket']) == false) {
            $this->dataDaftarPoliRJ['suket'] = $this->suket;
        }
    }

    // set data RJno / NoBooking / NoAntrian / klaimId / kunjunganId
    private function setDataPrimer(): void {}





    // when new form instance
    public function mount()
    {
        $this->findData($this->rjNoRef);
    }



    // select data start////////////////
    public function render()
    {

        return view(
            'livewire.emr-r-j.mr-r-j.suket.suket',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'suket',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Pasien Rawat Jalan',
            ]
        );
    }
    // select data end////////////////


}
