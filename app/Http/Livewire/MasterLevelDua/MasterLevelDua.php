<?php

namespace App\Http\Livewire\MasterLevelDua;

use Livewire\Component;
use App\Models\Regency;
use App\Models\Province;
use Livewire\WithPagination;

class MasterLevelDua extends Component
{
    use WithPagination;

    //  table data////////////////
    public $name, $regency_id, $province_id, $province_name;


    // limit record per page -resetExcept////////////////
    public $limitPerPage = 5;


    //  table LOV////////////////
    public $proviceLov = [];
    public $provinceLovStatus = 0;


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
    public $sortField = 'regencies.id';
    public $sortAsc = true;


    // listener from blade////////////////
    protected $listeners = [
        'confirm_remove_record_regency' => 'delete',
    ];




    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////





    // resert input private////////////////
    private function resetInputFields(): void
    {
        $this->reset([
            'name',
            'regency_id',
            'province_id',
            'province_name',
            'proviceLov',
            'provinceLovStatus',
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




    // logic LOV start////////////////
    public function updatedProvinceid()
    {
        // check LOV by id 
        $province = Province::select('id', 'name')
            ->where('id', '=', $this->province_id)->first();
        if ($province) {
            $this->province_id = $province->id;
            $this->province_name = $province->name;
            $this->provinceLovStatus = false;
        } else {
            // if there is no id found and check (min 3 char on search)
            if (strlen($this->province_id) < 3) {
                $this->proviceLov = [];
            } else {
                $this->proviceLov = Province::where('name', 'like', '%' . $this->province_id . '%')
                    ->orderBy('name', 'asc')->get();
            }
            $this->provinceLovStatus = true;
            $this->province_name = '';
        }
    }
    // /////////////////////
    // LOV selected start
    public function setMyProvinceLov($id, $name)
    {
        $this->province_id = $id;
        $this->province_name = $name;
        $this->provinceLovStatus = 0;
    }
    // LOV selected end
    // /////////////////////

    // logic LOV end



    // is going to insert data////////////////
    public function create()
    {
        $this->openModal();
    }



    // insert record start////////////////
    public function store()
    {
        $this->validate([
            'name' => 'required',
            'province_id' => 'required|exists:provinces,id'
        ]);

        Regency::updateOrCreate(['id' => $this->regency_id], [
            'name' => $this->name,
            'province_id' => $this->province_id
        ]);

        session()->flash(
            'message',
            $this->regency_id ? 'Regency Updated Successfully.' : 'Regency Created Successfully.'
        );

        $this->closeModal();
        $this->resetInputFields();
        $this->emit('toastr-success', "Data " . $this->name . " berhasil disimpan.");

    }
    // insert record end////////////////



    // Find data from table start////////////////
    private function findData($value)
    {
        $findData = Regency::findOrFail($value);
        return $findData;
    }
    // Find data from table end////////////////



    // show edit record start////////////////
    public function edit($id)
    {
        $this->openModalEdit();

        $regency = $this->findData($id);
        $this->regency_id = $id;
        $this->name = $regency->name;
        $this->province_id = $regency->province_id;
        $this->province_name = $regency->province->name;
    }
    // show edit record end////////////////



    // tampil record start////////////////
    public function tampil($id)
    {
        $this->openModalTampil();

        $regency = $this->findData($id);
        $this->regency_id = $id;
        $this->name = $regency->name;
        $this->province_id = $regency->province_id;
        $this->province_name = $regency->province->name;

    }
    // tampil record end////////////////



    // delete record start////////////////
    public function delete($id, $name)
    {
        Regency::find($id)->delete();
        $this->emit('toastr-success', "Hapus data " . $name . " berhasil.");
    }
    // delete record end////////////////



    // select data start////////////////
    public function render()
    {
        return view(
            'livewire.master-level-dua.master-level-dua',
            [
                'regencys' => Regency::with('province')
                    ->select('regencies.id as id', 'regencies.name as name', 'provinces.name as province_name')
                    ->join('provinces', 'provinces.id', 'regencies.province_id')
                    ->where('regencies.name', 'like', '%' . $this->search . '%')
                    ->orWhere('provinces.name', 'like', '%' . $this->search . '%')
                    ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
                    ->paginate($this->limitPerPage),
                'myTitle' => 'Master Kota',
                'mySnipt' => 'Tambah Data Master Kota',
                'myProgram' => 'Kota',
                'myLimitPerPages' => [5, 10, 15, 20, 100]
            ]
        );
    }
    // select data end////////////////
}