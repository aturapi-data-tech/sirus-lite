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
            "sc_score" => 0,
            "sc_value" => 0,
            "sc_option" =>
            [
                [
                    "option_label" => "YA",
                    "option_value" => 1,
                    "option_score" => 10
                ],
                [
                    "option_label" => "TIDAK",
                    "option_value" => 0,
                    "option_score" => 0
                ]
            ],


        ],
        [
            "sc_seq" => "1",
            "sc_desc" => "Apakah Pasien Merasa Sesak Nafas?",
            "active_status" => "1",
            "sc_score" => 0,
            "sc_value" => 0,
            "sc_option" =>
            [
                [
                    "option_label" => "YA",
                    "option_value" => 1,
                    "option_score" => 10
                ],
                [
                    "option_label" => "TIDAK",
                    "option_value" => 0,
                    "option_score" => 0
                ]
            ],


        ],
        [
            "sc_seq" => "2",
            "sc_desc" => "Apakah Pasien Beresiko Jatuh?",
            "active_status" => "1",
            "sc_score" => 0,
            "sc_value" => 0,
            "sc_option" =>
            [
                [
                    "option_label" => "YA",
                    "option_value" => 1,
                    "option_score" => 10
                ],
                [
                    "option_label" => "TIDAK",
                    "option_value" => 0,
                    "option_score" => 0
                ]
            ],


        ],
        [
            "sc_seq" => "2",
            "sc_desc" => "Apakah Pasien Nyeri Pada Dada?",
            "active_status" => "1",
            "sc_score" => 0,
            "sc_value" => 0,
            "sc_option" =>
            [
                [
                    "option_label" => "YA",
                    "option_value" => 1,
                    "option_score" => 10
                ],
                [
                    "option_label" => "TIDAK",
                    "option_value" => 0,
                    "option_score" => 0
                ]
            ],


        ],
        [
            "sc_seq" => "2",
            "sc_desc" => "Apakah Pasien Batuk Selama 2 Minggu Terakhir?",
            "active_status" => "1",
            "sc_score" => 0,
            "sc_value" => 0,
            "sc_option" =>
            [
                [
                    "option_label" => "YA",
                    "option_value" => 1,
                    "option_score" => 10
                ],
                [
                    "option_label" => "TIDAK",
                    "option_value" => 0,
                    "option_score" => 0
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
                    "option_value" => 13,
                    "option_score" => 10
                ],
                [
                    "option_label" => "4-7 (Nyeri)",
                    "option_value" => 47,
                    "option_score" => 50
                ],
                [
                    "option_label" => "8-10 (Sangat Nyeri)",
                    "option_value" => 810,
                    "option_score" => 100
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
    public function setLimitPerPage($value)
    {
        $this->limitPerPage = $value;
    }




    // resert page pagination when coloumn search change ////////////////
    public function updatedSearch(): void
    {
        $this->resetPage();
    }




    // logic ordering record (shotby)////////////////
    public function sortBy($field)
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
        $this->updateDataScreening($rjNO, $collectDataScreening);
        // //////////////////////////////////////


        $this->emit('toastr-success', "$label Score : $score");
    }
    // /prosesDataScreening/////////////
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
