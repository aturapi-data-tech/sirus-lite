<?php

namespace App\Http\Livewire\DaftarRI\FormEntryRI;

use App\Http\Traits\EmrRI\EmrRITrait;

use Livewire\Component;

class FormEntryRI extends Component
{
    use EmrRITrait;

    // listener from blade////////////////
    protected $listeners = [];


    public bool $isOpen = false;
    public string $isOpenMode = 'insert';
    public int $riHdrNo;

    public  $dataPasien = [];
    public $dataDaftarRi = [];




    //////////////////////////////////////////////////////////////////////

    public function closeModal(): void
    {
        $this->isOpen = false;
        $this->isOpenMode = 'insert';
    }




    private function findData($riHdrNo): void
    {
        $this->dataDaftarRi = $this->findDataRI($riHdrNo);
        $this->emit('listenerRegNo', $this->dataDaftarRi['regNo']);
    }
    public function mount()
    {

        if ($this->riHdrNo) {
            $this->findData($this->riHdrNo);
            $this->isOpenMode = 'update';
        }
    }

    public function render()
    {
        return view('livewire.daftar-r-i.form-entry-r-i.form-entry-r-i');
    }
}
