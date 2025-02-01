<?php

namespace App\Http\Livewire\EmrRI\AdministrasiRI;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

use App\Http\Traits\EmrRI\EmrRITrait;


use Exception;


class TrfUgdRjRI extends Component
{
    use WithPagination, EmrRITrait;


    // listener from blade////////////////
    protected $listeners = ['SyncAdministrasiRI' => 'mount'];


    //////////////////////////////
    // Ref on top bar
    //////////////////////////////
    public $riHdrNoRef;
    public array $dataTrfUgdRj = [];
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
        $riTrfUgdRj = DB::table('rstxn_ritempadmins')
            ->select(
                'tempadm_date',
                'rj_admin',
                'poli_price',
                'acte_price',
                'actp_price',
                'actd_price',
                'obat',
                'lab',
                'rad',
                'other',
                'rs_admin',
                'rihdr_no',
                'tempadm_no',
                'tempadm_flag'
            )
            ->where('rihdr_no', $riHdrNo)
            ->get();
        $this->dataTrfUgdRj['riTrfUgdRj'] = json_decode(json_encode($riTrfUgdRj, true), true);
    }




    // select data start////////////////
    public function render()
    {

        return view(
            'livewire.emr-r-i.administrasi-r-i.trf-ugd-rj-r-i',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Data Pasien Inap',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'TrfUgdRj',
            ]
        );
    }
    // select data end////////////////


}
