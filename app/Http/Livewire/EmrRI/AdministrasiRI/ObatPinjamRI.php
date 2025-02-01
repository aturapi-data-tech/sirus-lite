<?php

namespace App\Http\Livewire\EmrRI\AdministrasiRI;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

use App\Http\Traits\EmrRI\EmrRITrait;


use Exception;


class ObatPinjamRI extends Component
{
    use WithPagination, EmrRITrait;


    // listener from blade////////////////
    protected $listeners = ['SyncAdministrasiRI' => 'mount'];


    //////////////////////////////
    // Ref on top bar
    //////////////////////////////
    public $riHdrNoRef;
    public array $dataObatPinjam = [];
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
        $riObatPinjam = DB::table('rstxn_riobats')
            ->join('immst_products', 'immst_products.product_id', '=', 'rstxn_riobats.product_id')
            ->select(
                'rstxn_riobats.riobat_date',
                'rstxn_riobats.product_id as product_id',
                'immst_products.product_name',
                'rstxn_riobats.riobat_qty',
                'rstxn_riobats.riobat_price',
                'rstxn_riobats.rihdr_no',
                'rstxn_riobats.riobat_no'
            )
            ->where('rihdr_no', $riHdrNo)
            ->get();
        $this->dataObatPinjam['riObatPinjam'] = json_decode(json_encode($riObatPinjam, true), true);
    }




    // select data start////////////////
    public function render()
    {

        return view(
            'livewire.emr-r-i.administrasi-r-i.obat-pinjam-r-i',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Data Pasien Inap',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'ObatPinjam',
            ]
        );
    }
    // select data end////////////////


}
