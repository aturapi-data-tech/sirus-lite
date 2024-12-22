<?php

namespace App\Http\Livewire\EmrUGD\MrUGD\ObatDanCairan;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

use Spatie\ArrayToXml\ArrayToXml;
use App\Http\Traits\EmrUGD\EmrUGDTrait;
use App\Http\Traits\customErrorMessagesTrait;

class ObatDanCairan extends Component
{
    use WithPagination, EmrUGDTrait, customErrorMessagesTrait;

    // listener from blade////////////////
    protected $listeners = [
        'syncronizeAssessmentPerawatUGDFindData' => 'mount'
    ];



    //////////////////////////////
    // Ref on top bar
    //////////////////////////////



    // dataDaftarUgd RJ
    public $rjNoRef;

    public array $dataDaftarUgd = [];

    public array $obatDanCairan = [
        "namaObatAtauJenisCairan" => "",
        "jumlah" => "", //number
        "dosis" => "", //number
        "rute" => "", //number
        "keterangan" => "",
        "waktuPemberian" => "", //date dd/mm/yyyy hh24:mi:ss
        "pemeriksa" => ""
    ];

    public array $observasi =
    [
        "pemberianObatDanCairanTab" => "Pemberian Obat Dan Cairan",
        "pemberianObatDanCairan" => [],

    ];
    //////////////////////////////////////////////////////////////////////


    protected $rules = [
        'obatDanCairan.namaObatAtauJenisCairan' => 'required',
        'obatDanCairan.jumlah' => 'required|numeric',
        'obatDanCairan.dosis' => 'required',
        'obatDanCairan.rute' => 'required',
        'obatDanCairan.keterangan' => 'required',
        'obatDanCairan.waktuPemberian' => 'required|date_format:d/m/Y H:i:s',
        'obatDanCairan.pemeriksa' => 'required',
    ];



    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////
    public function updated($propertyName)
    {
        // dd($propertyName);
        // $this->validateOnly($propertyName);
        // $this->store();
    }




    // resert input private////////////////
    private function resetInputFields(): void
    {

        // resert validation
        $this->resetValidation();
        // resert input kecuali
        $this->reset(['']);
    }





    // ////////////////
    // RJ Logic
    // ////////////////


    // validate Data RJ//////////////////////////////////////////////////
    private function validateDataObatDanCairanUgd(): void
    {
        // customErrorMessages
        // $messages = customErrorMessagesTrait::messages();
        $messages = [];

        // Proses Validasi///////////////////////////////////////////
        try {
            $this->validate($this->rules, $messages);
        } catch (\Illuminate\Validation\ValidationException $e) {

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Lakukan Pengecekan kembali Input Data." . $e->getMessage());
            $this->validate($this->rules, $messages);
        }
    }


    // insert and update record start////////////////
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

        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("ObatDanCairan berhasil disimpan.");
    }
    // insert and update record end////////////////


    private function findData($rjno): void
    {

        $this->dataDaftarUgd = $this->findDataUGD($rjno);
        // dd($this->dataDaftarUgd);
        // jika observasi tidak ditemukan tambah variable observasi pda array
        if (isset($this->dataDaftarUgd['observasi']['obatDanCairan']) == false) {
            $this->dataDaftarUgd['observasi']['obatDanCairan'] = $this->observasi;
        }
    }



    public function addObatDanCairan()
    {

        // entry Pemeriksa
        $this->obatDanCairan['pemeriksa'] = auth()->user()->myuser_name;

        // validasi
        $this->validateDataObatDanCairanUgd();
        // check exist
        $cekdObatDanCairan = collect($this->dataDaftarUgd['observasi']['obatDanCairan']['pemberianObatDanCairan'])
            ->where("waktuPemberian", '=', $this->obatDanCairan['waktuPemberian'])
            ->count();
        if (!$cekdObatDanCairan) {
            $this->dataDaftarUgd['observasi']['obatDanCairan']['pemberianObatDanCairan'][] = [
                "namaObatAtauJenisCairan" => $this->obatDanCairan['namaObatAtauJenisCairan'],
                "jumlah" => $this->obatDanCairan['jumlah'],
                "dosis" => $this->obatDanCairan['dosis'],
                "rute" => $this->obatDanCairan['rute'],
                "keterangan" => $this->obatDanCairan['keterangan'],
                "waktuPemberian" => $this->obatDanCairan['waktuPemberian'],
                "pemeriksa" => $this->obatDanCairan['pemeriksa'],
            ];

            $this->dataDaftarUgd['observasi']['obatDanCairan']['pemberianObatDanCairanLog'] =
                [
                    'userLogDesc' => 'Form Entry obatDanCairan',
                    'userLog' => auth()->user()->myuser_name,
                    'userLogDate' => Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s')
                ];

            $this->store();
            // reset obatDanCairan
            $this->reset(['obatDanCairan']);
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("ObatDanCairan Sudah ada.");
        }
    }

    public function removeObatDanCairan($waktuPemberian)
    {

        $obatDanCairan = collect($this->dataDaftarUgd['observasi']['obatDanCairan']['pemberianObatDanCairan'])->where("waktuPemberian", '!=', $waktuPemberian)->toArray();
        $this->dataDaftarUgd['observasi']['obatDanCairan']['pemberianObatDanCairan'] = $obatDanCairan;

        $this->dataDaftarUgd['observasi']['obatDanCairan']['pemberianObatDanCairanLog'] =
            [
                'userLogDesc' => 'Hapus obatDanCairan',
                'userLog' => auth()->user()->myuser_name,
                'userLogDate' => Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s')
            ];
        $this->store();
    }

    public function setWaktuPemberian($myTime)
    {
        $this->obatDanCairan['waktuPemberian'] = $myTime;
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
            'livewire.emr-u-g-d.mr-u-g-d.obat-dan-cairan.obat-dan-cairan',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Obat Dan Cairan',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Pasien UGD',
            ]
        );
    }
    // select data end////////////////


}
