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
    public $dataDaftarRi = [];
    public $limitPerPage = 10;

    //  modal status////////////////
    public $isOpen = 0;
    public $isOpenMode = 'insert';

    // search logic -resetExcept////////////////
    public $search;
    protected $queryString = [
        'search' => ['except' => '', 'as' => 'cariData'],
        'page' => ['except' => 1, 'as' => 'p'],
    ];



    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////





    // resert input private////////////////
    private function resetInputFields(): void
    {

        // resert validation
        $this->resetValidation();
        // resert input kecuali
        $this->resetExcept([
            'limitPerPage',
            'search',
            'drRjRef',


        ]);
    }




    // open and close modal start////////////////

    private function openModalEdit(): void
    {
        $this->resetInputFields();
        $this->isOpen = true;
        $this->isOpenMode = 'update';
    }





    public function closeModal(): void
    {
        $this->resetInputFields();
    }
    // open and close modal end////////////////




    // setLimitPerpage////////////////
    public function setLimitPerPage($value): void
    {
        $this->limitPerPage = $value;
        $this->resetValidation();
    }


    // search
    public function updatedSearch(): void
    {
        //  toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError( "search.");

        $this->resetPage();
        $this->resetValidation();
        $this->resetInputFields();
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

    // set data RiHdrNo / NoBooking / NoAntrian / klaimId / kunjunganId
    private function setDataPrimer(): void
    {
        $noKontrol = Carbon::now(env('APP_TIMEZONE'))->addDays(8)->format('dmY') . $this->dataDaftarRi['kontrol']['drKontrol'] . $this->dataDaftarRi['kontrol']['poliKontrol'];
        $this->dataDaftarRi['kontrol']['noKontrolRS'] =  $this->dataDaftarRi['kontrol']['noKontrolRS'] ? $this->dataDaftarRi['kontrol']['noKontrolRS'] : $noKontrol;
    }



    // when new form instance
    public function mount() {}



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
            ->where('reg_no', '=', $this->regNoRef);



        //Jika where dokter tidak kosong
        if ($this->drRjRef['drId'] != 'All') {
            $query->where('dr_id', $this->drRjRef['drId']);
        }

        $query->where(function ($q) {
            $q->Where(DB::raw('upper(reg_name)'), 'like', '%' . strtoupper($this->search) . '%')
                ->orWhere(DB::raw('upper(reg_no)'), 'like', '%' . strtoupper($this->search) . '%');
        })
            ->orderBy('rj_date1',  'desc')
            ->orderBy('dr_name',  'desc');

        ////////////////////////////////////////////////
        // end Query
        ///////////////////////////////////////////////


        return view(
            'livewire.r-iskdp.r-iskdp',
            [
                'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Data SKDP Pasien Rawat Jalan',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Pasien Rawat Jalan',
                'myLimitPerPages' => [5, 10, 15, 20, 100],
            ]
        );
    }
    // select data end////////////////


}
