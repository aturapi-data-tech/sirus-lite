<?php

namespace App\Http\Livewire\EmrUGD\MrUGD\RekamMedis;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

use App\Http\Traits\customErrorMessagesTrait;

use Illuminate\Support\Str;
use Spatie\ArrayToXml\ArrayToXml;


class RekamMedis extends Component
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





    // validate Data RJ//////////////////////////////////////////////////
    private function validateDataRJ(): void
    {
        // customErrorMessages
        $messages = customErrorMessagesTrait::messages();

        // require nik ketika pasien tidak dikenal



        $rules = [
            "regNoRef" => "",
        ];



        // Proses Validasi///////////////////////////////////////////
        try {
            $this->validate($rules, $messages);
        } catch (\Illuminate\Validation\ValidationException $e) {

            $this->emit('toastr-error', "Lakukan Pengecekan kembali Input Data.");
            $this->validate($rules, $messages);
        }
    }




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

            )
            ->where('reg_no', '039446Z')
            ->orderBy('txn_date1',  'desc')
            ->orderBy('layanan_status',  'desc')
            ->orderBy('poli',  'asc');

        ////////////////////////////////////////////////
        // end Query
        ///////////////////////////////////////////////

        return view(
            'livewire.emr-u-g-d.mr-u-g-d.rekam-medis.rekam-medis',
            ['myQueryData' => $query->paginate(3)]


        );
    }
    // select data end////////////////


}
