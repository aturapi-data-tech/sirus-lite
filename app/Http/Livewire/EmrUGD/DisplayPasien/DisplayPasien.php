<?php

namespace App\Http\Livewire\EmrUGD\DisplayPasien;

use Livewire\Component;
use App\Http\Traits\EmrUGD\EmrUGDTrait;
use App\Http\Traits\MasterPasien\MasterPasienTrait;
use Carbon\Carbon;



class DisplayPasien extends Component
{
    use EmrUGDTrait, MasterPasienTrait;

    public $rjNoRef;

    public array $dataDaftarUgd = [];
    public  array $dataPasien = [];


    private function findData($rjno): void
    {
        $this->dataDaftarUgd = $this->findDataUGD($rjno);
        $this->dataPasien = $this->findDataMasterPasien($this->dataDaftarUgd['regNo'] ?? '');
        $this->dataPasien['pasien']['thn'] = Carbon::createFromFormat('d/m/Y', $this->dataPasien['pasien']['tglLahir'])->diff(Carbon::now(env('APP_TIMEZONE')))->format('%y Thn, %m Bln %d Hr'); //$findData->thn;

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
