<?php

namespace App\Http\Livewire\ErmRJ;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class ErmRJ extends Component
{
    use WithPagination;

    //  table data////////////////
    public $name, $province_id;

    // variable pasien dan screening di gabung pada collect data screening
    public $dataPasienPoli = [
        "regNo" => "",
        "regName" => "",
        "sex" => "",
        "address" => "",
        "rjDate" => "",
        "rjNo" => "",
    ];
    public $screeningQuestions = [
        [
            "sc_seq" => "1",
            "sc_desc" => "Apakah Pasien Sadar?",
            "active_status" => "1",
            "sc_score" => 1,
            "sc_value" => 1,
            "sc_option" =>
            [
                [
                    "option_label" => "Sadar Penuh",
                    "option_value" => 1,
                    "option_score" => 1
                ],
                [
                    "option_label" => "Tampak (Mengantuk / Gelisah / Bicara Tida Jelas)",
                    "option_value" => 2,
                    "option_score" => 2
                ],
                [
                    "option_label" => "Tidak Sadar",
                    "option_value" => 3,
                    "option_score" => 3
                ]
            ],


        ],
        [
            "sc_seq" => "2",
            "sc_desc" => "Apakah Pasien Merasa Sesak Nafas?",
            "active_status" => "1",
            "sc_score" => 1,
            "sc_value" => 1,
            "sc_option" =>
            [
                [
                    "option_label" => "Nafas Normal",
                    "option_value" => 1,
                    "option_score" => 1
                ],
                [
                    "option_label" => "Tampak (Sesak)",
                    "option_value" => 2,
                    "option_score" => 2
                ],
                [
                    "option_label" => "Tidak Bernafas",
                    "option_value" => 3,
                    "option_score" => 3
                ]
            ],


        ],
        [
            "sc_seq" => "3",
            "sc_desc" => "Apakah Pasien Beresiko Jatuh?",
            "active_status" => "1",
            "sc_score" => 1,
            "sc_value" => 1,
            "sc_option" =>
            [
                [
                    "option_label" => "Resiko Rendah",
                    "option_value" => 1,
                    "option_score" => 1
                ],
                [
                    "option_label" => "Resiko (Sedang)",
                    "option_value" => 2,
                    "option_score" => 2
                ],
                [
                    "option_label" => "Resiko Tinggi",
                    "option_value" => 3,
                    "option_score" => 3
                ]
            ],


        ],
        [
            "sc_seq" => "4",
            "sc_desc" => "Apakah Pasien Nyeri Pada Dada?",
            "active_status" => "1",
            "sc_score" => 1,
            "sc_value" => 1,
            "sc_option" =>
            [
                [
                    "option_label" => "Tidak Ada",
                    "option_value" => 1,
                    "option_score" => 1
                ],
                [
                    "option_label" => "Ada (Tingkat Rendah)",
                    "option_value" => 2,
                    "option_score" => 2
                ],
                [
                    "option_label" => "Nyeri Dada Kiri Tembis Punggung",
                    "option_value" => 3,
                    "option_score" => 3
                ]
            ],


        ],
        [
            "sc_seq" => "5",
            "sc_desc" => "Apakah Pasien Batuk?",
            "active_status" => "1",
            "sc_score" => 1,
            "sc_value" => 1,
            "sc_option" =>
            [
                [
                    "option_label" => "Tidak Ada",
                    "option_value" => 1,
                    "option_score" => 1
                ],
                [
                    "option_label" => "Batuk < 2 Minggu",
                    "option_value" => 2,
                    "option_score" => 2
                ],
                [
                    "option_label" => "Batuk > 2 Minggu",
                    "option_value" => 3,
                    "option_score" => 3
                ]
            ],


        ],
        [
            "sc_seq" => "2",
            "sc_desc" => "Tingkat Nyeri Pasien",
            "active_status" => "1",
            "sc_score" => 0,
            "sc_value" => 0,
            "sc_image" => "pain_scale_level.jpg",
            "sc_option" =>
            [
                [
                    "option_label" => "0 (Tidak Nyeri)",
                    "option_value" => 0,
                    "option_score" => 0
                ],
                [
                    "option_label" => "1-3 (Sedikit Nyeri)",
                    "option_value" => 1,
                    "option_score" => 1
                ],
                [
                    "option_label" => "4-7 (Nyeri)",
                    "option_value" => 2,
                    "option_score" => 2
                ],
                [
                    "option_label" => "8-10 (Sangat Nyeri)",
                    "option_value" => 3,
                    "option_score" => 3
                ],

            ],


        ],
    ];
    public $screeningKesimpulan = [
        "sck_value" => 1,
        "sck_score" => 1,
        "sck_option" =>
        [
            [
                "option_id" => "1",
                "option_label" => "SESUAI ANTRIAN",
                "option_value" => 1,
                "option_score" => 1,
            ],
            [
                "option_id" => "2",
                "option_label" => "DISEGERAKAN",
                "option_value" => 2,
                "option_score" => 2,
            ],
            [
                "option_id" => "3",
                "option_label" => "IGD",
                "option_value" => 3,
                "option_score" => 3,
            ],
        ]
    ];
    public $collectDataScreening = [];
    //////////////////////////////////////////////////////////////////////
    public $dataTandaVital = [
        [
            "tv_seq" => "1",
            "tv_label" => "Tekanan Darah",
            "tv_mou" => "mmHg",
            "tv_value" => 0
        ],
        [
            "tv_seq" => "2",
            "tv_label" => "Frekuensi Nadi",
            "tv_mou" => "x/menit",
            "tv_value" => 0
        ],
        [
            "tv_seq" => "3",
            "tv_label" => "Suhu",
            "tv_mou" => "C",
            "tv_value" => 0
        ],
        [
            "tv_seq" => "4",
            "tv_label" => "Frekuansi Nafas",
            "tv_mou" => "x/menit",
            "tv_value" => 0
        ],
        [
            "tv_seq" => "5",
            "tv_label" => "Skor Nyeri",
            "tv_mou" => "",
            "tv_value" => 0
        ],
        [
            "tv_seq" => "6",
            "tv_label" => "Skor Jatuh",
            "tv_mou" => "",
            "tv_value" => 0
        ],
    ];
    public $dataAntropometri = [
        [
            "ap_seq" => "1",
            "ap_label" => "Berat Badan",
            "ap_mou" => "Kg",
            "ap_value" => 0
        ],
        [
            "ap_seq" => "2",
            "ap_label" => "Tinggi Badan",
            "ap_mou" => "Cm",
            "ap_value" => 0
        ],
        [
            "ap_seq" => "3",
            "ap_label" => "Lingkar Kepala",
            "ap_mou" => "Cm",
            "ap_value" => 0
        ],
        [
            "ap_seq" => "4",
            "ap_label" => "IMT",
            "ap_mou" => "",
            "ap_value" => 0
        ],
        [
            "ap_seq" => "5",
            "ap_label" => "Lingkar Lengan Atas",
            "ap_mou" => "Cm",
            "ap_value" => 0
        ],
    ];
    public $dataFungsional = [
        [
            "fu_seq" => "1",
            "fu_label" => "Alat Bantu",
            "fu_mou" => "",
            "fu_value" => "-"
        ],
        [
            "fu_seq" => "2",
            "fu_label" => "Prothesa",
            "fu_mou" => "",
            "fu_value" => "-"
        ],
        [
            "fu_seq" => "3",
            "fu_label" => "Cacat Tubuh",
            "fu_mou" => "",
            "fu_value" => "-"
        ],
        [
            "fu_seq" => "4",
            "fu_label" => "ADL",
            "fu_mou" => "",
            "fu_value" => "-"
        ],
        [
            "fu_seq" => "5",
            "fu_label" => "Riwayat",
            "fu_mou" => "",
            "fu_value" => "-"
        ],
    ];


    public $collectDataTandaVital = [];

    //////////////////////////////////////////////////////////////////////

    // Ref on top bar
    public $ermStatusRef = 'A';
    public $rjDateRef = '';



    // limit record per page -resetExcept////////////////
    public $limitPerPage = 10;



    //  modal status////////////////
    public $isOpen = 0;
    public $isOpenMode = 'insert';
    public $tampilIsOpen = 0;


    // search logic -resetExcept////////////////
    public $search;
    protected $queryString = [
        'search' => ['except' => '', 'as' => 'cariData'],
        'page' => ['except' => 1, 'as' => 'p'],
    ];


    // sort logic -resetExcept////////////////
    public $sortField = 'reg_no';
    public $sortAsc = true;


    // listener from blade////////////////
    protected $listeners = [
        'confirm_remove_record_province' => 'delete',
    ];




    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////





    // resert input private////////////////
    private function resetInputFields(): void
    {


        $this->reset([
            // 'name',
            // 'province_id',
            // 'dataPasienPoli',
            // 'screeningQuestions',
            // 'screeningKesimpulan',

            'isOpen',
            'tampilIsOpen',
            'isOpenMode'
        ]);
    }




    // open and close modal start////////////////
    private function openModal(): void
    {
        $this->resetInputFields();
        $this->isOpen = true;
        $this->isOpenMode = 'insert';
    }
    private function openModalEdit(): void
    {
        $this->resetInputFields();
        $this->isOpen = true;
        $this->isOpenMode = 'update';
    }

    private function openModalTampil(): void
    {
        $this->resetInputFields();
        $this->isOpen = true;
        $this->isOpenMode = 'tampil';
    }

    public function closeModal(): void
    {
        $this->resetInputFields();
    }
    // open and close modal end////////////////




    // setLimitPerpage////////////////
    public function setLimitPerPage($value): void
    {
        $this->limitPerPage = $value;
    }




    // resert page pagination when coloumn search change ////////////////
    public function updatedSearch(): void
    {
        $this->resetPage();
    }




    // logic ordering record (shotby)////////////////
    public function sortBy($field): void
    {
        if ($this->sortField === $field) {
            $this->sortAsc = !$this->sortAsc;
        } else {
            $this->sortAsc = true;
        }

        $this->sortField = $field;
    }



    // is going to insert data////////////////
    public function create()
    {
        $this->openModal();
    }



    // Find data from table start////////////////
    private function findData($value)
    {
        $findData = DB::table('rsview_rjkasir')
            ->select(
                'rj_no',
                'rj_date',
                'reg_no',
                'reg_name',
                'sex',
                'address',
                'thn',
                DB::raw("to_char(birth_date,'dd/mm/yyyy') AS birth_date"),
                'poli_id',
                'poli_desc',
                'dr_id',
                'dr_name',
                'klaim_id',
                'shift',
                'vno_sep'
            )
            ->where('rj_no', '=', $value)
            ->first();

        return $findData;
    }
    // Find data from table end////////////////



    // //////////////////////////////////////////////////////////////////
    // screening logic///////////////////////////////////////////////////
    // //////////////////////////////////////////////////////////////////

    // updateDataScreening
    private function updateDataScreening($id, $value): void
    {
        DB::table('rstxn_rjhdrs')
            ->where('rj_no', $id)
            ->update(['erm_screening' => $value]);
    }


    // tampil record start////////////////
    public function tampil($id)
    {
        // /////////////Bentuk array pasien dan screening start////
        $dataPasienPoli = $this->findData($id);

        $screening = DB::table('rstxn_rjhdrs')
            ->select('erm_screening')
            ->where('rj_no', '=', $id)
            ->first();

        if ($screening->erm_screening == null) {
            $this->collectDataScreening = [
                "pasien" => $this->dataPasienPoli,
                "screening" => $this->screeningQuestions,
                "kesimpulan" => $this->screeningKesimpulan
            ];
        } else {

            $this->collectDataScreening = json_decode($screening->erm_screening, true);
        }
        // /////////////Bentuk array pasien dan screening end////

        $this->collectDataScreening['pasien']['regNo'] = $dataPasienPoli->reg_no;
        $this->collectDataScreening['pasien']['regName'] = $dataPasienPoli->reg_name;
        $this->collectDataScreening['pasien']['sex'] = $dataPasienPoli->sex;
        $this->collectDataScreening['pasien']['address'] = $dataPasienPoli->address;
        $this->collectDataScreening['pasien']['rjDate'] = $dataPasienPoli->rj_date;
        $this->collectDataScreening['pasien']['rjNo'] = $dataPasienPoli->rj_no;

        // update data Screening//////////////////
        $rjNO = $this->collectDataScreening['pasien']["rjNo"];
        $collectDataScreening = json_encode($this->collectDataScreening, true);
        $this->updateDataScreening($rjNO, $collectDataScreening);
        // //////////////////////////////////////

        $this->openModalTampil();
    }
    // tampil record end////////////////

    // /prosesDataScreening/////////////
    public function prosesDataScreening($label, $value, $score, $key)
    {

        $collection = collect($this->collectDataScreening);
        // update collectDataScreening
        if (
            collect($collection['screening'])->where('sc_desc', $label)->contains('sc_desc', $label)
        ) {

            $this->collectDataScreening['screening'][$key]['sc_value'] = $value;
            $this->collectDataScreening['screening'][$key]['sc_score'] = $score;
        }

        // update data Screening//////////////////
        $rjNO = $this->collectDataScreening['pasien']["rjNo"];
        $collectDataScreening = json_encode($this->collectDataScreening, true);

        // update kesimpulan dari screening otomatis
        $KesimpulanDariScreening = $this->updateDataKesimpulanDariScreening() == 1 ? 'Sesuai Antrian' : ($this->updateDataKesimpulanDariScreening() == 2 ? 'Disegerakan' : 'IGD');

        $this->updateDataScreening($rjNO, $collectDataScreening);
        // //////////////////////////////////////


        $this->emit('toastr-success', "$label Kesimpulan : $KesimpulanDariScreening");
    }
    // /prosesDataScreening/////////////

    ////////////////////////////////////
    // updateDataKesimpulan dari Screening
    private function updateDataKesimpulanDariScreening()
    {
        $KesimpulanDariScreening = collect($this->collectDataScreening['screening'])
            ->pluck('sc_score')
            ->max();
        $this->collectDataScreening['kesimpulan']['sck_value'] = $KesimpulanDariScreening != 0 ? $KesimpulanDariScreening : 1;

        return ($KesimpulanDariScreening);
    }

    // /prosesDataKesimpulan/////////////
    public function prosesDataKesimpulan($value, $label)
    {

        // update collectDataKesimpulan
        $this->collectDataScreening['kesimpulan']['sck_value'] = $value;


        // update data Screening//////////////////
        $rjNO = $this->collectDataScreening['pasien']["rjNo"];
        $collectDataScreening = json_encode($this->collectDataScreening, true);
        $this->updateDataScreening($rjNO, $collectDataScreening);
        // //////////////////////////////////////
        $this->emit('toastr-success', "Kesimpulan: $label");
    }
    // /prosesDataKesimpulan/////////////
    ////////////////////////////////////


    // //////////////////////////////////////////////////////////////////
    // screening logic///////////////////////////////////////////////////
    // //////////////////////////////////////////////////////////////////












    // when new form instance
    public function mount()
    {
        $this->rjDateRef = Carbon::now()->format('d/m/Y');
    }


    // select data start////////////////
    public function render()
    {

        return view(
            'livewire.erm-r-j.erm-r-j',
            [
                'RJpasiens' => DB::table('rsview_rjkasir')
                    ->select(
                        'rj_no',
                        'reg_no',
                        'reg_name',
                        'sex',
                        'address',
                        'thn',
                        DB::raw("to_char(birth_date,'dd/mm/yyyy') AS birth_date"),
                        'poli_id',
                        'poli_desc',
                        'dr_id',
                        'dr_name',
                        'klaim_id',
                        'shift',
                        'vno_sep'
                    )
                    ->where('erm_status', '=', $this->ermStatusRef)
                    ->where(DB::raw("to_char(rj_date,'dd/mm/yyyy')"), '=', $this->rjDateRef)
                    ->where(function ($q) {
                        $q->Where('reg_name', 'like', '%' . $this->search . '%')
                            ->orWhere('reg_no', 'like', '%' . $this->search . '%');
                    })
                    ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
                    ->paginate($this->limitPerPage),
                'myTitle' => 'Data Pasien Rawat Jalan',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Pasien Rawat Jalan',
                'myLimitPerPages' => [5, 10, 15, 20, 100]
            ]
        );
    }
    // select data end////////////////
}
