<?php

namespace App\Http\Livewire\EmrUGD\MrUGD\Observasi;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

use Spatie\ArrayToXml\ArrayToXml;
use App\Http\Traits\EmrUGD\EmrUGDTrait;
use App\Http\Traits\customErrorMessagesTrait;

class Observasi extends Component
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
    private function validateDataObservasiUgd(): void
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

        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Observasi berhasil disimpan.");
    }
    // insert and update record end////////////////


    private function findData($rjno): void
    {

        $this->dataDaftarUgd = $this->findDataUGD($rjno);
        // dd($this->dataDaftarUgd);
        // jika observasi tidak ditemukan tambah variable observasi pda array
        if (isset($this->dataDaftarUgd['observasi']['observasiLanjutan']) == false) {
            $this->dataDaftarUgd['observasi']['observasiLanjutan'] = $this->observasi;
        }
    }



    public function addObservasiLanjutan()
    {
        // entry Pemeriksa
        $this->observasiLanjutan['pemeriksa'] = auth()->user()->myuser_name;

        // validasi
        $this->validateDataObservasiUgd();
        // check exist
        $cekObservasiLanjutan = collect($this->dataDaftarUgd['observasi']['observasiLanjutan']['tandaVital'])
            ->where("waktuPemeriksaan", '=', $this->observasiLanjutan['waktuPemeriksaan'])
            ->count();
        if (!$cekObservasiLanjutan) {
            $this->dataDaftarUgd['observasi']['observasiLanjutan']['tandaVital'][] = [
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

            $this->dataDaftarUgd['observasi']['observasiLanjutan']['tandaVitalLog'] =
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

        $observasiLanjutan = collect($this->dataDaftarUgd['observasi']['observasiLanjutan']['tandaVital'])->where("waktuPemeriksaan", '!=', $waktuPemeriksaan)->toArray();
        $this->dataDaftarUgd['observasi']['observasiLanjutan']['tandaVital'] = $observasiLanjutan;

        $this->dataDaftarUgd['observasi']['observasiLanjutan']['tandaVitalLog'] =
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
        $this->findData($this->rjNoRef);
    }



    // select data start////////////////
    public function render()
    {

        return view(
            'livewire.emr-u-g-d.mr-u-g-d.observasi.observasi',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Observasi',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Pasien UGD',
            ]
        );
    }
    // select data end////////////////


}
