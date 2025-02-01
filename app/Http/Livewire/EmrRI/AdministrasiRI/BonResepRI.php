<?php

namespace App\Http\Livewire\EmrRI\AdministrasiRI;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

use App\Http\Traits\EmrRI\EmrRITrait;


use Exception;


class BonResepRI extends Component
{
    use WithPagination, EmrRITrait;


    // listener from blade////////////////
    protected $listeners = ['SyncAdministrasiRI' => 'mount'];


    //////////////////////////////
    // Ref on top bar
    //////////////////////////////
    public $riHdrNoRef;
    public array $dataBonResep = [];
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
        $riBonResep = DB::table('rstxn_ribonobats')
            ->select(
                'ribon_date',
                'ribon_desc',
                'ribon_price',
                'rihdr_no',
                'ribon_no',
                'sls_no'
            )
            ->where('rihdr_no', $riHdrNo)
            ->get();
        $this->dataBonResep['riBonResep'] = json_decode(json_encode($riBonResep, true), true);
    }




    // select data start////////////////
    public function render()
    {

        return view(
            'livewire.emr-r-i.administrasi-r-i.bon-resep-r-i',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Data Pasien Inap',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'BonResep',
            ]
        );
    }
    // select data end////////////////


}
