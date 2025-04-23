<?php

namespace App\Http\Livewire\EmrUGD\MrUGD\Suket;

use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Traits\EmrUGD\EmrUGDTrait;


class Suket extends Component
{
    use WithPagination, EmrUGDTrait;

    // listener from blade////////////////
    protected $listeners = [
        'syncronizeAssessmentPerawatUGDFindData' => 'mount'

    ];

    //////////////////////////////
    // Ref on top bar
    //////////////////////////////
    public $rjNoRef;

    // dataDaftarUgd UGD
    public array $dataDaftarUgd = [];

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

        // 'dataDaftarUgd.suket.pengkajianMedis.waktuPemeriksaan' => 'required|date_format:d/m/Y H:i:s',
        // 'dataDaftarUgd.suket.pengkajianMedis.selesaiPemeriksaan' => 'required|date_format:d/m/Y H:i:s'
        'dataDaftarUgd.suket.suketIstirahat.suketIstirahatHari' => 'numeric'


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
    // UGD Logic
    // ////////////////


    // validate Data UGD//////////////////////////////////////////////////
    private function validateDataUGD(): void
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
        // set data UGDno / NoBooking / NoAntrian / klaimId / kunjunganId
        $this->setDataPrimer();

        // Validate UGD
        $this->validateDataUGD();

        // Logic update mode start //////////
        $this->updateDataUGD($this->dataDaftarUgd['rjNo']);
        $this->emit('syncronizeAssessmentPerawatUGDFindData');
    }

    private function updateDataUGD($rjNo): void
    {
        // if ($rjNo !== $this->dataDaftarUgd['rjNo']) {
        //     dd('Data Json Tidak sesuai' . $rjNo . '  /  ' . $this->dataDaftarUgd['rjNo']);
        // }

        // // update table trnsaksi
        // DB::table('rstxn_rjhdrs')
        //     ->where('rj_no', $rjNo)
        //     ->update([
        //         'dataDaftarUgd_json' => json_encode($this->dataDaftarUgd, true),
        //         'dataDaftarUgd_xml' => ArrayToXml::convert($this->dataDaftarUgd),

        //     ]);
        $this->updateJsonUGD($rjNo, $this->dataDaftarUgd);

        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("suket berhasil disimpan.");
    }
    // insert and update record end////////////////


    private function findData($rjno): void
    {


        $this->dataDaftarUgd = $this->findDataUGD($rjno);

        // jika suket tidak ditemukan tambah variable suket pda array
        if (isset($this->dataDaftarUgd['suket']) == false) {
            $this->dataDaftarUgd['suket'] = $this->suket;
        }
    }

    // set data UGDno / NoBooking / NoAntrian / klaimId / kunjunganId
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
            'livewire.emr-u-g-d.mr-u-g-d.suket.suket',
            [
                // 'UGDpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'suket',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Pasien Rawat Jalan',
            ]
        );
    }
    // select data end////////////////


}
