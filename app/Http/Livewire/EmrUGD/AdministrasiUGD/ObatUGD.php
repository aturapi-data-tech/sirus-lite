<?php

namespace App\Http\Livewire\EmrUGD\AdministrasiUGD;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;


class ObatUGD extends Component
{
    use WithPagination;


    // listener from blade////////////////
    protected $listeners = [];


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
        $rows = DB::table('rstxn_ugdobats')
            ->join('immst_products', 'immst_products.product_id', '=', 'rstxn_ugdobats.product_id')
            ->select([
                'rstxn_ugdobats.product_id as product_id',
                'immst_products.product_name',
                'rstxn_ugdobats.qty',
                'rstxn_ugdobats.price',
                'rstxn_ugdobats.rjobat_dtl',
            ])
            ->where('rstxn_ugdobats.rj_no', $rjno)
            ->orderBy('rjobat_dtl')   // optional: urutkan
            ->get();

        // cukup toArray(); tidak perlu json_encode/decode
        $this->dataDaftarUgd['rjObat'] = json_decode(json_encode($rows), true);
    }






    // when new form instance
    public function mount()
    {
        if (!$this->rjNoRef) {
            $this->dataDaftarUgd['rjObat'] = [];
            return;
        }
        $this->findData($this->rjNoRef);
    }



    // select data start////////////////
    public function render()
    {

        return view(
            'livewire.emr-u-g-d.administrasi-u-g-d.obat-u-g-d',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Data Pasien Unit Gawat Darurat',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Obat',
            ]
        );
    }
    // select data end////////////////


}
