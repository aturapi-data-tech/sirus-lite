<?php

namespace App\Http\Livewire\MasterLevelSatu;

use Livewire\Component;
use App\Models\Province;
use Livewire\WithPagination;

class MasterLevelSatu extends Component
{
    use WithPagination;

    public $name;
    public $province_id;





    public $isOpen = 0;
    public $tampilIsOpen = 0;




    public $search;
    protected $queryString = [
        'search' => ['except' => '', 'as' => 'cariData'],
        'page' => ['except' => 1, 'as' => 'p'],
    ];









    public $sortField = 'id';
    public $sortAsc = true;






    protected $listeners = [
        'confirm_remove_record_province' => 'delete',
    ];




    public $limitPerPage = 5;










    public function updatedsearch(): void
    {
        $this->resetPage();

    }














































    public function changeLimitPerPage($value)
    {

        $this->limitPerPage = $value;

    }





















    public function create()
    {
        // $this->emit('toastr-info', "openModal...");

        $this->resetInputFields();

        $this->openModal();

    }


























    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    public function openModal()
    {

        $this->isOpen = true;

    }















    public function openModalTampil()
    {

        $this->tampilIsOpen = true;

    }



    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

















    public function closeModal()
    {

        $this->isOpen = false;

    }

















    public function closeModalTampil()
    {

        $this->tampilIsOpen = false;

    }



    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */























    private function resetInputFields()
    {

        $this->name = '';

        $this->province_id = '';

    }



    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

























    public function store()
    {

        $this->validate([

            'name' => 'required',

        ]);



        province::updateOrCreate(['id' => $this->province_id], [

            'name' => $this->name,

        ]);



        session()->flash(
            'message',

            $this->province_id ? 'province Updated Successfully.' : 'province Created Successfully.'
        );



        $this->closeModal();

        $this->resetInputFields();

    }


























    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    public function edit($id)
    {

        $province = province::findOrFail($id);

        $this->province_id = $id;

        $this->name = $province->name;


        $this->openModal();

    }











    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortAsc = !$this->sortAsc;
        } else {
            $this->sortAsc = true;
        }

        $this->sortField = $field;
    }













    public function tampil($id)
    {

        $province = province::findOrFail($id);

        $this->province_id = $id;

        $this->name = $province->name;

        $this->openModalTampil();

    }


























    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    public function delete($id, $name)
    {

        province::find($id)->delete();

        $this->emit('toastr-info', "Hapus data " . $name . " berhasil.");


    }



















    public function render()
    {
        return view(
            'livewire.master-level-satu.master-level-satu',
            [
                'provinces' => Province::where('name', 'like', '%' . $this->search . '%')
                    ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
                    ->paginate($this->limitPerPage),
                'myTitle' => 'Master Provinsi',
                'mySnipt' => 'Tambah Data Master Provinsi',
                'myProgram' => 'Provinsi',
                'myLimitPerPages' => [5, 10, 15, 20, 100, 111]
            ]
        );
    }















    // Blm dipake
    public function paginationView()
    {
        return 'vendor.livewire.tailwind';
    }





}