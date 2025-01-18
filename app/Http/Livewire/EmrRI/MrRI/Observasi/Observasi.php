<?php

namespace App\Http\Livewire\EmrRI\MrRI\Observasi;


use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

use App\Http\Traits\EmrRI\EmrRITrait;
use App\Http\Traits\customErrorMessagesTrait;

class Observasi extends Component
{
    use WithPagination, EmrRITrait, customErrorMessagesTrait;

    // listener from blade////////////////
    protected $listeners = [
        'syncronizeAssessmentPerawatRIFindData' => 'mount'
    ];

    public $labels = ['Januari', 'Februari', 'Maret', 'April', 'Mei'];
    public $data = [65, 59, 80, 81, 56];

    //////////////////////////////
    // Ref on top bar
    //////////////////////////////



    // dataDaftarRi RJ
    public $riHdrNoRef;

    public array $dataDaftarRi = [];

    public array $observasiLanjutan = [
        "cairan" => "",
        "tetesan" => "",
        "sistolik" => "", //number
        "distolik" => "", //number
        "frekuensiNafas" => "", //number
        "frekuensiNadi" => "", //number
        "suhu" => "", //number
        "spo2" => "", //number
        "gda" => "", //number
        "gcs" => "", //number
        "waktuPemeriksaan" => "", //date dd/mm/yyyy hh24:mi:ss
        "pemeriksa" => ""
    ];

    public array $observasi =
    [
        "tandaVitalTab" => "Observasi Lanjutan",
        "tandaVital" => [],

    ];
    //////////////////////////////////////////////////////////////////////


    protected $rules = [
        'observasiLanjutan.cairan' => '',
        'observasiLanjutan.tetesan' => '',
        'observasiLanjutan.sistolik' => 'required|numeric',
        'observasiLanjutan.distolik' => 'required|numeric',
        'observasiLanjutan.frekuensiNafas' => 'required|numeric',
        'observasiLanjutan.frekuensiNadi' => 'required|numeric',
        'observasiLanjutan.suhu' => 'required|numeric',
        'observasiLanjutan.spo2' => 'required|numeric',
        'observasiLanjutan.gda' => '',
        'observasiLanjutan.gcs' => '',
        'observasiLanjutan.waktuPemeriksaan' => 'required|date_format:d/m/Y H:i:s',
        'observasiLanjutan.pemeriksa' => 'required',
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
    private function validateDataObservasiRi(): void
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
        $this->updateDataRi($this->dataDaftarRi['riHdrNo']);

        $this->emit('syncronizeAssessmentPerawatRIFindData');
    }

    private function updateDataRi($riHdrNo): void
    {
        // update table trnsaksi
        // DB::table('rstxn_ugdhdrs')
        //     ->where('rj_no', $riHdrNo)
        //     ->update([
        //         'datadaftarugd_json' => json_encode($this->dataDaftarRi, true),
        //         'datadaftarRi_xml' => ArrayToXml::convert($this->dataDaftarRi),
        //     ]);

        $this->updateJsonRI($riHdrNo, $this->dataDaftarRi);

        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Observasi berhasil disimpan.");
    }
    // insert and update record end////////////////


    private function findData($riHdrNo): void
    {

        $this->dataDaftarRi = $this->findDataRI($riHdrNo);
        // dd($this->dataDaftarRi);
        // jika observasi tidak ditemukan tambah variable observasi pda array
        if (isset($this->dataDaftarRi['observasi']['observasiLanjutan']) == false) {
            $this->dataDaftarRi['observasi']['observasiLanjutan'] = $this->observasi;
        }
    }



    public function addObservasiLanjutan()
    {
        // entry Pemeriksa
        $this->observasiLanjutan['pemeriksa'] = auth()->user()->myuser_name;

        // validasi
        $this->validateDataObservasiRi();
        // check exist
        $cekObservasiLanjutan = collect($this->dataDaftarRi['observasi']['observasiLanjutan']['tandaVital'])
            ->where("waktuPemeriksaan", '=', $this->observasiLanjutan['waktuPemeriksaan'])
            ->count();
        if (!$cekObservasiLanjutan) {
            $this->dataDaftarRi['observasi']['observasiLanjutan']['tandaVital'][] = [
                "cairan" => $this->observasiLanjutan['cairan'],
                "tetesan" => $this->observasiLanjutan['tetesan'],
                "sistolik" => $this->observasiLanjutan['sistolik'],
                "distolik" => $this->observasiLanjutan['distolik'],
                "frekuensiNafas" => $this->observasiLanjutan['frekuensiNafas'],
                "frekuensiNadi" => $this->observasiLanjutan['frekuensiNadi'],
                "suhu" => $this->observasiLanjutan['suhu'],
                "spo2" => $this->observasiLanjutan['spo2'],
                "gda" => $this->observasiLanjutan['gda'],
                "gcs" => $this->observasiLanjutan['gcs'],
                "waktuPemeriksaan" => $this->observasiLanjutan['waktuPemeriksaan'],
                "pemeriksa" => $this->observasiLanjutan['pemeriksa'],
            ];

            $this->dataDaftarRi['observasi']['observasiLanjutan']['tandaVitalLog'] =
                [
                    'userLogDesc' => 'Form Entry observasiLanjutan',
                    'userLog' => auth()->user()->myuser_name,
                    'userLogDate' => Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s')
                ];

            $this->store();
            // reset observasiLanjutan
            $this->reset(['observasiLanjutan']);
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Observasi Sudah ada.");
        }
    }

    public function removeObservasiLanjutan($waktuPemeriksaan)
    {

        $observasiLanjutan = collect($this->dataDaftarRi['observasi']['observasiLanjutan']['tandaVital'])->where("waktuPemeriksaan", '!=', $waktuPemeriksaan)->toArray();
        $this->dataDaftarRi['observasi']['observasiLanjutan']['tandaVital'] = $observasiLanjutan;

        $this->dataDaftarRi['observasi']['observasiLanjutan']['tandaVitalLog'] =
            [
                'userLogDesc' => 'Hapus observasiLanjutan',
                'userLog' => auth()->user()->myuser_name,
                'userLogDate' => Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s')
            ];
        $this->store();
    }

    public function setWaktuPemeriksaan($myTime)
    {
        $this->observasiLanjutan['waktuPemeriksaan'] = $myTime;
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
            'livewire.emr-r-i.mr-r-i.observasi.observasi',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Observasi',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Pasien RI',
            ]
        );
    }
    // select data end////////////////


}
