<?php

namespace App\Http\Livewire\ErmRJ;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use App\Models\Province;
use Livewire\WithPagination;
use Carbon\Carbon;

class ErmRJ extends Component
{
    use WithPagination;

    //  table data////////////////
    public $name, $province_id;
    public $dataPasienPoli = [
        "regNo" => "",
        "regName" => "",
        "sex" => "",
        "birthDate" => "",
        "thn" => "",
        "birthPlace" => "",
        "maritalStatus" => "",
        "address" => "",
        "drId" => "",
        "drName" => "",
        "poliId" => "",
        "poliDesc" => "",
        "klaimId" => "",
        "klaimDesc" => "",
        "rjDate" => "",
        "rjNo" => "",
        "shift" => "",
        "noAntrian" => "",
        "noBooking" => "",
        "slCodeFrom" => "02",
        "passStatus" => "O",
        "rjStatus" => "A",
        "txnStatus" => "A",



    ];
    public $ermStatusRef = 'A';
    public $rjDateRef = '';



    // limit record per page -resetExcept////////////////
    public $limitPerPage = 5;



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
            'name',
            'province_id',

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



    // insert record start////////////////
    public function store()
    {

        $customErrorMessages = [
            'name.required' => 'Nama tidak boleh kosong',
            'province_id.required' => 'Kode tidak boleh kosong'
        ];

        $this->validate([
            'name' => 'required',
            'province_id' => 'required'
        ], $customErrorMessages);

        Province::updateOrCreate(['id' => $this->province_id], [
            'name' => $this->name
        ]);


        $this->closeModal();
        $this->resetInputFields();
        $this->emit('toastr-success', "Data " . $this->name . " berhasil disimpan.");

    }
    // insert record end////////////////



    // Find data from table start////////////////
    private function findData($value)
    {
        $findData = DB::table('rsview_rjkasir')
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
            ->where('rj_no', '=', $value)
            ->first();
        return $findData;
    }
    // Find data from table end////////////////



    // show edit record start////////////////
    public function edit($id)
    {
        $this->openModalEdit();

        $province = $this->findData($id);
        $this->province_id = $id;
        $this->name = $province->name;

    }
    // show edit record end////////////////



    // tampil record start////////////////
    public function tampil($id)
    {
        $this->openModalTampil();

        $dataPasienPoli = $this->findData($id);
        $this->dataPasienPoli['regNo'] = $dataPasienPoli->reg_no;
        $this->dataPasienPoli['regName'] = $dataPasienPoli->reg_name;

    }
    // tampil record end////////////////



    // delete record start////////////////
    public function delete($id, $name)
    {
        Province::find($id)->delete();
        $this->emit('toastr-success', "Hapus data " . $name . " berhasil.");
    }
    // delete record end////////////////





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