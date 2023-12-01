<?php

namespace App\Http\Livewire\EmrUGD\MrUGD\RekamMedis;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

use App\Http\Traits\customErrorMessagesTrait;

use Illuminate\Support\Str;
use Spatie\ArrayToXml\ArrayToXml;


class Layanan extends Component
{
    use WithPagination;


    // listener from blade////////////////
    protected $listeners = [];

    //////////////////////////////
    // Ref on top bar
    //////////////////////////////
    public $regNoRef;


    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////



    // when new form instance
    public function mount()
    {
    }



    // select data start////////////////
    public function render()
    {

        //////////////////////////////////////////
        // Query ///////////////////////////////
        //////////////////////////////////////////
        $query = DB::table('rsview_ermstatus')
            ->select(
                DB::raw("to_char(txn_date,'dd/mm/yyyy hh24:mi:ss') AS txn_date"),
                DB::raw("to_char(txn_date,'yyyymmddhh24miss') AS txn_date1"),
                'txn_no',
                'reg_no',
                'erm_status',
                'layanan_status',
                'poli',
                DB::raw("(CASE WHEN layanan_status='RJ' THEN (select datadaftarpolirj_json from rsview_rjkasir where rj_no=txn_no)
                                        WHEN layanan_status='UGD' THEN (select datadaftarugd_json from rsview_ugdkasir where rj_no=txn_no)
                                            ELSE null END) as datadaftar_json")

            )
            ->where('reg_no', $this->regNoRef)
            ->orderBy('txn_date1',  'desc')
            ->orderBy('layanan_status',  'desc')
            ->orderBy('poli',  'asc');

        ////////////////////////////////////////////////
        // end Query
        ///////////////////////////////////////////////

        return view(
            'livewire.emr-u-g-d.mr-u-g-d.rekam-medis.layanan',
            ['myQueryData' => $query->paginate(3)]


        );
    }
    // select data end////////////////


}
