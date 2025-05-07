<?php

namespace App\Http\Livewire\EmrRI\AdministrasiRI;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

use App\Http\Traits\EmrRI\EmrRITrait;


use Exception;


class RtnObatRI extends Component
{
    use WithPagination, EmrRITrait;


    // listener from blade////////////////
    protected $listeners = ['SyncAdministrasiRI' => 'mount'];


    //////////////////////////////
    // Ref on top bar
    //////////////////////////////
    public $riHdrNoRef;
    public array $dataRtnObat = [];
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
        $riRtnObat = DB::table('rstxn_riobatrtns')
            ->join('immst_products', 'immst_products.product_id', '=', 'rstxn_riobatrtns.product_id')
            ->select(
                DB::raw("to_char(rstxn_riobatrtns.riobat_date,  'dd/mm/yyyy hh24:mi:ss') as riobat_date"),
                'rstxn_riobatrtns.product_id as product_id',
                'immst_products.product_name',
                'rstxn_riobatrtns.riobat_qty',
                'rstxn_riobatrtns.riobat_price',
                'rstxn_riobatrtns.rihdr_no',
                'rstxn_riobatrtns.riobat_no'
            )
            ->where('rihdr_no', $riHdrNo)
            ->get();
        $this->dataRtnObat['riRtnObat'] = json_decode(json_encode($riRtnObat, true), true);
    }




    // select data start////////////////
    public function render()
    {

        return view(
            'livewire.emr-r-i.administrasi-r-i.rtn-obat-r-i',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Data Pasien Inap',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'RtnObat',
            ]
        );
    }
    // select data end////////////////


}
