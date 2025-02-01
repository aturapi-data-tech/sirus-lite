<?php

namespace App\Http\Livewire\EmrRI\AdministrasiRI;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

use App\Http\Traits\EmrRI\EmrRITrait;


use Exception;


class RadiologiRI extends Component
{
    use WithPagination, EmrRITrait;


    // listener from blade////////////////
    protected $listeners = ['SyncAdministrasiRI' => 'mount'];


    //////////////////////////////
    // Ref on top bar
    //////////////////////////////
    public $riHdrNoRef;
    public array $dataRadiologi = [];
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
        $riRadiologi = DB::table('rstxn_riradiologs')
            ->join('rsmst_radiologis', 'rstxn_riradiologs.rad_id', '=', 'rsmst_radiologis.rad_id')
            ->select(
                'rstxn_riradiologs.rirad_date',
                'rstxn_riradiologs.rad_id',
                'rsmst_radiologis.rad_desc',
                'rstxn_riradiologs.rirad_price',
                'rstxn_riradiologs.rihdr_no',
                'rstxn_riradiologs.rirad_no'
            )
            ->where('rihdr_no', $riHdrNo)
            ->get();
        $this->dataRadiologi['riRadiologi'] = json_decode(json_encode($riRadiologi, true), true);
    }




    // select data start////////////////
    public function render()
    {

        return view(
            'livewire.emr-r-i.administrasi-r-i.radiologi-r-i',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Data Pasien Inap',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Radiologi',
            ]
        );
    }
    // select data end////////////////


}
