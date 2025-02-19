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


    public array $dataDaftarRi = [];
    public  array $dataPasien = [];


    private function findData($riHdrNo): void
    {
        $this->dataDaftarRi = $this->findDataRI($riHdrNo);
        $this->dataPasien = $this->findDataMasterPasien($this->dataDaftarRi['regNo'] ?? '');

        // upate data array terkini
        $dataDaftarRi = DB::table('rsview_rihdrs')
            ->select(
                'room_id',
                'room_name',
                'bed_no',
                'bangsal_id',
                'bangsal_name',
                'klaim_id',
            )
            ->where('rihdr_no', '=', $riHdrNo)
            ->first();

        $this->dataDaftarRi['bangsalId'] = $dataDaftarRi->bangsal_id;
        $this->dataDaftarRi['bangsalDesc'] = $dataDaftarRi->bangsal_name;
        $this->dataDaftarRi['roomId'] = $dataDaftarRi->room_id;
        $this->dataDaftarRi['roomDesc'] = $dataDaftarRi->room_name;
        $this->dataDaftarRi['bedNo'] = $dataDaftarRi->bed_no;
        $this->dataDaftarRi['klaimId'] = $dataDaftarRi->klaim_id;
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
