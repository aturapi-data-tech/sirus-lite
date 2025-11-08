<?php

namespace App\Http\Livewire\EmrRJ\DisplayPasien;

use Livewire\Component;
use Carbon\Carbon;
use App\Http\Traits\EmrRJ\EmrRJTrait;
use App\Http\Traits\MasterPasien\MasterPasienTrait;


class DisplayPasien extends Component
{
    use MasterPasienTrait, EmrRJTrait;

    public $rjNoRef;

    public array $dataDaftarPoliRJ = [];
    public  array $dataPasien = [];


    private function findData($rjno): void
    {

        $findDataRJ = $this->findDataRJ($rjno);
        $this->dataDaftarPoliRJ  = $findDataRJ['dataDaftarRJ'];
        $this->dataPasien = $this->findDataMasterPasien($this->dataDaftarPoliRJ['regNo'] ?? '');
        $this->dataPasien['pasien']['thn'] = Carbon::createFromFormat('d/m/Y', $this->dataPasien['pasien']['tglLahir'])->diff(Carbon::now(env('APP_TIMEZONE')))->format('%y Thn, %m Bln %d Hr'); //$findData->thn;

    }




    public function mount()
    {
        $this->findData($this->rjNoRef);
    }

    public function render()
    {
        return view('livewire.emr-r-j.display-pasien.display-pasien');
    }
}
