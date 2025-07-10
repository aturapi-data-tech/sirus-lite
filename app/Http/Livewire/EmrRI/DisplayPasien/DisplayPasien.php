<?php

namespace App\Http\Livewire\EmrRI\DisplayPasien;

use Illuminate\Support\Facades\DB;

use Livewire\Component;

use App\Http\Traits\EmrRI\EmrRITrait;
use App\Http\Traits\MasterPasien\MasterPasienTrait;

class DisplayPasien extends Component
{
    use EmrRITrait, MasterPasienTrait;

    protected $listeners = [];

    public $riHdrNoRef;
    public bool $isOpen = false;
    public string $isOpenMode = 'insert';
    public bool $forceInsertRecord = false;


    public  $myQData = '{}';
    public  array $dataPasien = [];


    private function findData($riHdrNo): void
    {
        $this->myQData = $this->findDataRI($riHdrNo);

        $this->dataPasien = $this->findDataMasterPasien($this->myQData['regNo'] ?? '');
    }

    public function mount()
    {
        $this->findData($this->riHdrNoRef);
    }

    public function render()
    {
        return view('livewire.emr-r-i.display-pasien.display-pasien');
    }
}
