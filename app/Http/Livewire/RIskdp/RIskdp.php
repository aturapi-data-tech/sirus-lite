<?php

namespace App\Http\Livewire\RIskdp;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

use App\Http\Traits\EmrRI\EmrRITrait;
use App\Http\Traits\MasterPasien\MasterPasienTrait;

class RIskdp extends Component
{
    use WithPagination, EmrRITrait, MasterPasienTrait;

    //////////////////////////////
    // Ref on top bar
    //////////////////////////////

    public $regNoRef;
    public array $dataPasien = [];

    // dataDaftarRi
    public array $dataDaftarRi = [];
    public int $limitPerPage = 10;

    //  modal status////////////////
    public $isOpen = 0;
    public $isOpenMode = 'insert';

    // search logic -resetExcept////////////////
    protected $queryString = [
        'page' => ['except' => 1, 'as' => 'p'],
    ];



    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////



    // open and close modal start////////////////

    private function openModalEdit(): void
    {
        $this->isOpen = true;
        $this->isOpenMode = 'update';
    }


    public function closeModal(): void
    {
        $this->reset(['isOpen', 'isOpenMode']);
    }
    // open and close modal end////////////////




    // setLimitPerpage////////////////
    public function setLimitPerPage($value): void
    {
        $this->limitPerPage = $value;
    }


    // is going to edit data/////////////////
    public function edit($id)
    {
        $this->openModalEdit();
        $this->findData($id);
    }


    private function findData($riHdrNo): void
    {
        $this->dataDaftarRi = $this->findDataRI($riHdrNo);
        $this->dataPasien = $this->findDataMasterPasien($this->dataDaftarRi['regNo'] ?? '');
    }

    // select data start////////////////
    public function render()
    {
        //////////////////////////////////////////
        // Query ///////////////////////////////
        //////////////////////////////////////////
        $query = DB::table('rsview_rihdrs')
            ->select(
                DB::raw("to_char(exit_date,'dd/mm/yyyy hh24:mi:ss') AS rj_date"),
                DB::raw("to_char(exit_date,'yyyymmddhh24miss') AS rj_date1"),
                'rihdr_no',
                'reg_no',
                'reg_name',
                'sex',
                'address',
                'thn',
                DB::raw("to_char(birth_date,'dd/mm/yyyy') AS birth_date"),
                DB::raw("'poli_id' AS poli_id"),
                DB::raw("'poli_desc' AS poli_desc"),
                DB::raw("'dr_id' AS dr_id"),
                DB::raw("'dr_name' AS dr_name"),

                'klaim_id',
                DB::raw("'1' AS shift"),
                'vno_sep',
                DB::raw("'123' AS no_antrian"),
                'ri_status',
                DB::raw("'123' AS nobooking"),
                'push_antrian_bpjs_status',
                'push_antrian_bpjs_json',
                'datadaftarri_json'
            )
            // ->whereNotIn('rj_status', ['A', 'F'])
            ->where('ri_status', 'P')
            ->where('reg_no', '=', $this->regNoRef)
            ->orderBy('rj_date1',  'desc')
            ->orderBy('dr_name',  'desc');

        ////////////////////////////////////////////////
        // end Query
        ///////////////////////////////////////////////


        return view(
            'livewire.r-iskdp.r-iskdp',
            [
                'PasienRI' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Data SKDP Pasien Rawat Inap',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Pasien Rawat Inap',
                'myLimitPerPages' => [5, 10, 15, 20, 100],
            ]
        );
    }
    // select data end////////////////


}
