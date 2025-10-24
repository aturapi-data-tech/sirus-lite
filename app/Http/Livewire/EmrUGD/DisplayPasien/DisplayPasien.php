<?php

namespace App\Http\Livewire\EmrUGD\DisplayPasien;

use Livewire\Component;
use App\Http\Traits\EmrUGD\EmrUGDTrait;
use App\Http\Traits\MasterPasien\MasterPasienTrait;



class DisplayPasien extends Component
{
    use EmrUGDTrait, MasterPasienTrait;

    protected $listeners = [];

    public $rjNoRef;

    public array $dataDaftarUgd = [];
    public  array $dataPasien = [];


    private function findData($rjno): void
    {
        $this->dataDaftarUgd = $this->findDataUGD($rjno);
        $this->dataPasien = $this->findDataMasterPasien($this->dataDaftarUgd['regNo'] ?? '');
    }


    public function mount()
    {

        $this->findData($this->rjNoRef);
    }

    public function render()
    {
        return view('livewire.emr-u-g-d.display-pasien.display-pasien');
    }
}
