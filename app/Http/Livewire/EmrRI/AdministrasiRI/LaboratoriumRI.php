<?php

namespace App\Http\Livewire\EmrRI\AdministrasiRI;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

use App\Http\Traits\EmrRI\EmrRITrait;


use Exception;


class LaboratoriumRI extends Component
{
    use WithPagination, EmrRITrait;


    // listener from blade////////////////
    protected $listeners = ['SyncAdministrasiRI' => 'mount'];


    //////////////////////////////
    // Ref on top bar
    //////////////////////////////
    public $riHdrNoRef;
    public array $dataLaboratorium = [];
    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////


    // when new form instance
    public function mount()
    {
        $this->findData($this->riHdrNoRef);
    }

    private function findData($riHdrNo): void
    {
        $riLaboratorium = DB::table('rstxn_rilabs')
            ->select(
                'lab_date',
                'lab_desc',
                'lab_price',
                'lab_dtl',
                'rihdr_no',
                'checkup_no',
            )
            ->where('rihdr_no', $riHdrNo)
            ->get();
        $this->dataLaboratorium['riLaboratorium'] = json_decode(json_encode($riLaboratorium, true), true);
    }




    // select data start////////////////
    public function render()
    {

        return view(
            'livewire.emr-r-i.administrasi-r-i.laboratorium-r-i',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Data Pasien Inap',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Laboratorium',
            ]
        );
    }
    // select data end////////////////


}
