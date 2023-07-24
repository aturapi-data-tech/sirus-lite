<?php

namespace App\Http\Livewire\SetupHfisBpjs;

use Livewire\Component;
use App\Models\Province;
use Livewire\WithPagination;

use Carbon\Carbon;

use App\Http\Traits\BPJS\AntrianTrait;
use App\Http\Traits\BPJS\VclaimTrait;




class SetupHfisBpjs extends Component
{
    use WithPagination;

    //  table data////////////////
    public $name, $province_id;


    // limit record per page -resetExcept////////////////
    public $limitPerPage = 5;

    //  table LOV////////////////
    public $hfisLov = [];
    public $hfisLovStatus = 0;
    public $hfisLovSearch = '';


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
    public $sortField = 'id';
    public $sortAsc = true;


    // listener from blade////////////////
    protected $listeners = [
        'confirm_remove_record_province' => 'delete',
    ];


    //////////////////////////////
    // Ref on top bar
    //////////////////////////////
    public $dateRjRef = '';

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
        $this->hfisLovStatus = true;

        $PushDataref_poli =  VclaimTrait::ref_poliklinik($this->search)->getOriginalContent();
        dd($PushDataref_poli);
        if ($PushDataref_poli['metadata']['code'] == 200) {
            $this->emit('toastr-success', $PushDataref_poli['metadata']['code'] . ' ' . $PushDataref_poli['metadata']['message']);
        } else {
            $this->emit('toastr-error', $PushDataref_poli['metadata']['code'] . ' ' . $PushDataref_poli['metadata']['message']);
        }
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
        $findData = Province::findOrFail($value);
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

        $province = $this->findData($id);
        $this->province_id = $id;
        $this->name = $province->name;
    }
    // tampil record end////////////////



    // delete record start////////////////
    public function delete($id, $name)
    {
        Province::find($id)->delete();
        $this->emit('toastr-success', "Hapus data " . $name . " berhasil.");
    }
    // delete record end////////////////

    // /////////LOV////////////
    // /////////hfis////////////
    public function clickhfislov()
    {
        $this->hfisLovStatus = true;
        $this->hfisLov = $this->dataPasien['pasien']['hfis']['hfisOptions'];
    }
    public function updatedhfislovsearch()
    {
        // Variable Search
        $search = $this->hfisLovSearch;

        // check LOV by id 
        $hfis = collect($this->dataPasien['pasien']['hfis']['hfisOptions'])
            ->where('hfisId', '=', $search)
            ->first();

        if ($hfis) {
            $this->dataPasien['pasien']['hfis']['hfisId'] = $hfis['hfisId'];
            $this->dataPasien['pasien']['hfis']['hfisDesc'] = $hfis['hfisDesc'];
            $this->hfisLovStatus = false;
            $this->hfisLovSearch = '';
        } else {
            // if there is no id found and check (min 3 char on search)
            if (strlen($search) < 3) {
                $this->hfisLov = $this->dataPasien['pasien']['hfis']['hfisOptions'];
            } else {
                $this->hfisLov = collect($this->dataPasien['pasien']['hfis']['hfisOptions'])
                    ->filter(function ($item) use ($search) {
                        return false !== stristr($item['hfisDesc'], $search);
                    });
            }
            $this->hfisLovStatus = true;
            $this->dataPasien['pasien']['hfis']['hfisId'] = '';
            $this->dataPasien['pasien']['hfis']['hfisDesc'] = '';
        }
    }
    // /////////////////////
    // LOV selected start
    public function setMyhfisLov($id, $name)
    {
        $this->dataPasien['pasien']['hfis']['hfisId'] = $id;
        $this->dataPasien['pasien']['hfis']['hfisDesc'] = $name;
        $this->hfisLovStatus = false;
        $this->hfisLovSearch = '';
    }
    // LOV selected end
    // /////////////////////


    // when new form instance
    public function mount()
    {
        $this->dateRjRef = Carbon::now()->format('d/m/Y');
    }



    // select data start////////////////
    public function render()
    {
        return view(
            'livewire.setup-hfis-bpjs.setup-hfis-bpjs',
            [
                'hfis' => collect([]),
                'myTitle' => 'HFIS BPJS',
                'mySnipt' => 'Data HFIS BPJS',
                'myProgram' => 'HFIS BPJS',
                'myLimitPerPages' => [5, 10, 15, 20, 100]
            ]
        );
    }
    // select data end////////////////
}
