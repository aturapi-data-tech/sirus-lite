<?php

namespace App\Http\Livewire\EmrUGD\AdministrasiUGD;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;


class RadiologiUGD extends Component
{
    use WithPagination;


    // listener from blade////////////////
    protected $listeners = [
        'ugd:refresh-data-admin' => 'mount'
    ];


    //////////////////////////////z
    // Ref on top bar
    //////////////////////////////
    public $rjNoRef;



    // dataDaftarUgd RJ
    public array $dataDaftarUgd = [];

    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////

    private function findData($rjno): void
    {
        $findData = DB::table('rstxn_ugdrads')
            ->join('rsmst_radiologis', 'rsmst_radiologis.rad_id', 'rstxn_ugdrads.rad_id')
            ->select('rad_desc', 'rstxn_ugdrads.rad_price as rad_price', 'rad_dtl')
            ->where('rj_no', $rjno)
            ->get();



        if ($findData) {
            $this->dataDaftarUgd['rjRad'] = json_decode(json_encode($findData, true), true);
        } else {

            $this->dataDaftarUgd['rjRad'] = [];
        }
    }


    // when new form instance
    public function mount()
    {
        $this->findData($this->rjNoRef);
    }



    // select data start////////////////
    public function render()
    {

        return view(
            'livewire.emr-u-g-d.administrasi-u-g-d.radiologi-u-g-d',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Data Pasien Unit Gawat Darurat',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Radiologi',
            ]
        );
    }
    // select data end////////////////


}
