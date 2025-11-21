<?php

namespace App\Http\Livewire\EmrUGD\AdministrasiUGD;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;


class LaboratoriumUGD extends Component
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



    // dataDaftarUGD RJ
    public array $dataDaftarUGD = [];

    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////

    private function findData($rjno): void
    {
        $findData = DB::table('rstxn_ugdlabs')
            ->select('lab_desc', 'lab_price', 'lab_dtl')
            ->where('rj_no', $rjno)
            ->get();



        if ($findData) {
            $this->dataDaftarUGD['rjLab'] = json_decode(json_encode($findData, true), true);
        } else {

            $this->dataDaftarUGD['rjLab'] = [];
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
            'livewire.emr-u-g-d.administrasi-u-g-d.laboratorium-u-g-d',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Data Pasien Unit Gawat Darurat',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Laboratorium',
            ]
        );
    }
    // select data end////////////////


}
