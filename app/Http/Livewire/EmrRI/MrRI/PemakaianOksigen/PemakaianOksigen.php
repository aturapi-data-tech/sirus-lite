<?php

namespace App\Http\Livewire\EmrRI\MrRI\PemakaianOksigen;


use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

use App\Http\Traits\EmrRI\EmrRITrait;
use App\Http\Traits\customErrorMessagesTrait;

class PemakaianOksigen extends Component
{
    use WithPagination, EmrRITrait, customErrorMessagesTrait;

    // listener from blade////////////////
    protected $listeners = [
        'syncronizeAssessmentPerawatRIFindData' => 'mount'
    ];

    //////////////////////////////
    // Ref on top bar
    //////////////////////////////



    // dataDaftarRi RJ
    public $riHdrNoRef;

    public array $dataDaftarRi = [];

    public array $pemakaianOksigen = [
        "jenisAlatOksigen" => "Nasal Kanul", // Jenis alat oksigen yang digunakan
        "jenisAlatOksigenDetail" => "", // Jenis alat oksigen yang digunakan
        "dosisOksigen" => "1-2 L/menit", // Dosis oksigen yang diberikan
        "dosisOksigenDetail" => "", // Dosis oksigen yang diberikan
        "durasiPenggunaan" => "Kontinu", // Durasi penggunaan (Kontinu atau Intermiten)
        "tanggalWaktuMulai" => "", // Tanggal dan waktu mulai penggunaan (format: dd/mm/yyyy hh24:mi:ss)
        "tanggalWaktuSelesai" => "", // Tanggal dan waktu selesai penggunaan (format: dd/mm/yyyy hh24:mi:ss)
    ];

    public array $observasi =
    [
        "pemakaianOksigenTab" => "Pemakaian Oksigen",
        "pemakaianOksigen" => [],

    ];
    //////////////////////////////////////////////////////////////////////


    protected $rules = [
        'pemakaianOksigen.jenisAlatOksigen' => 'required|in:Nasal Kanul,Masker Sederhana,Ventilator Non-Invasif,Lainnya',
        'pemakaianOksigen.jenisAlatOksigenDetail' => 'required_if:pemakaianOksigen.jenisAlatOksigen,Lainnya',
        'pemakaianOksigen.dosisOksigen' => 'required|in:1-2 L/menit,3-4 L/menit,2-6 L/menit (Nasal Kanul),5-10 L/menit (Masker),Lainnya',
        'pemakaianOksigen.dosisOksigenDetail' => 'required_if:pemakaianOksigen.dosisOksigen,Lainnya',
        'pemakaianOksigen.durasiPenggunaan' => 'nullable|in:Kontinu,Intermiten',
        'pemakaianOksigen.tanggalWaktuMulai' => 'required|date_format:d/m/Y H:i:s',
        'pemakaianOksigen.tanggalWaktuSelesai' => 'nullable|date_format:d/m/Y H:i:s',
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
    private function validateDataPemakaianOksigenRi(): void
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

        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("PemakaianOksigen berhasil disimpan.");
    }
    // insert and update record end////////////////


    private function findData($riHdrNo): void
    {

        $this->dataDaftarRi = $this->findDataRI($riHdrNo);
        // dd($this->dataDaftarRi);
        // jika observasi tidak ditemukan tambah variable observasi pda array
        if (isset($this->dataDaftarRi['observasi']['pemakaianOksigen']) == false) {
            $this->dataDaftarRi['observasi']['pemakaianOksigen'] = $this->observasi;
        }
    }



    public function addPemakaianOksigen()
    {
        // entry Pemeriksa
        $this->pemakaianOksigen['pemeriksa'] = auth()->user()->myuser_name;

        // validasi
        $this->validateDataPemakaianOksigenRi();
        // check exist
        $cekPemakaianOksigen = collect($this->dataDaftarRi['observasi']['pemakaianOksigen']['pemakaianOksigenData'] ?? [])
            ->where("tanggalWaktuMulai", '=', $this->pemakaianOksigen['tanggalWaktuMulai'])
            ->count();
        if (!$cekPemakaianOksigen) {
            $this->dataDaftarRi['observasi']['pemakaianOksigen']['pemakaianOksigenData'][] = [
                "jenisAlatOksigen" => $this->pemakaianOksigen['jenisAlatOksigen'],
                "jenisAlatOksigenDetail" => $this->pemakaianOksigen['jenisAlatOksigenDetail'],
                "dosisOksigen" => $this->pemakaianOksigen['dosisOksigen'],
                "dosisOksigenDetail" => $this->pemakaianOksigen['dosisOksigenDetail'],
                "durasiPenggunaan" => $this->pemakaianOksigen['durasiPenggunaan'],
                "tanggalWaktuMulai" => $this->pemakaianOksigen['tanggalWaktuMulai'],
                "tanggalWaktuSelesai" => $this->pemakaianOksigen['tanggalWaktuSelesai'],
            ];


            $this->dataDaftarRi['observasi']['pemakaianOksigen']['pemakaianOksigenLog'] =
                [
                    'userLogDesc' => 'Form Entry pemakaianOksigen',
                    'userLog' => auth()->user()->myuser_name,
                    'userLogDate' => Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s')
                ];

            $this->store();
            // reset pemakaianOksigen
            $this->reset(['pemakaianOksigen']);
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("PemakaianOksigen Sudah ada.");
        }
    }

    public function removePemakaianOksigen($tanggalWaktuMulai)
    {

        $pemakaianOksigen = collect($this->dataDaftarRi['observasi']['pemakaianOksigen']['pemakaianOksigenData'])->where("tanggalWaktuMulai", '!=', $tanggalWaktuMulai)->toArray();
        $this->dataDaftarRi['observasi']['pemakaianOksigen']['pemakaianOksigenData'] = $pemakaianOksigen;

        $this->dataDaftarRi['observasi']['pemakaianOksigen']['pemakaianOksigenLog'] =
            [
                'userLogDesc' => 'Hapus pemakaianOksigen',
                'userLog' => auth()->user()->myuser_name,
                'userLogDate' => Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s')
            ];
        $this->store();
    }

    public function setTanggalWaktuMulai($myTime)
    {
        $this->pemakaianOksigen['tanggalWaktuMulai'] = $myTime;
    }
    public function setTanggalWaktuSelesai($index, $myTime)
    {
        $this->dataDaftarRi['observasi']['pemakaianOksigen']['pemakaianOksigenData'][$index]['tanggalWaktuSelesai'] = $myTime;

        //Hitung Selisih Jam
        $tanggalWaktuMulai = $this->dataDaftarRi['observasi']['pemakaianOksigen']['pemakaianOksigenData'][$index]['tanggalWaktuMulai'];
        $tanggalWaktuSelesai = $this->dataDaftarRi['observasi']['pemakaianOksigen']['pemakaianOksigenData'][$index]['tanggalWaktuSelesai'];

        if ($tanggalWaktuMulai && $tanggalWaktuSelesai) {
            $mulai = Carbon::createFromFormat('d/m/Y H:i:s', $tanggalWaktuMulai, env('APP_TIMEZONE'));
            $selesai = Carbon::createFromFormat('d/m/Y H:i:s', $tanggalWaktuSelesai, env('APP_TIMEZONE'));

            // Hitung selisih dalam jam
            $selisihJam = $mulai->diffInHours($selesai);
            $this->dataDaftarRi['observasi']['pemakaianOksigen']['pemakaianOksigenData'][$index]['durasiPenggunaan'] = $selisihJam;
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
            'livewire.emr-r-i.mr-r-i.pemakaian-oksigen.pemakaian-oksigen',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Pemakaian Oksigen',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Pasien RI',
            ]
        );
    }
    // select data end////////////////


}
