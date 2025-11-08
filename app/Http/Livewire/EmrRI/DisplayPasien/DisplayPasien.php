<?php

namespace App\Http\Livewire\EmrRI\DisplayPasien;

use Livewire\Component;
use Carbon\Carbon;

use App\Http\Traits\EmrRI\EmrRITrait;
use App\Http\Traits\MasterPasien\MasterPasienTrait;

class DisplayPasien extends Component
{
    use EmrRITrait, MasterPasienTrait;

    public $riHdrNoRef;

    public  array $dataDaftarRi = [];
    public  array $dataPasien = [];


    private function findData($riHdrNo): void
    {
        $this->dataDaftarRi = $this->findDataRI($riHdrNo);

        $this->dataPasien = $this->findDataMasterPasien($this->dataDaftarRi['regNo'] ?? '');
        $this->dataPasien['pasien']['thn'] = Carbon::createFromFormat('d/m/Y', $this->dataPasien['pasien']['tglLahir'])->diff(Carbon::now(env('APP_TIMEZONE')))->format('%y Thn, %m Bln %d Hr'); //$findData->thn;

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
