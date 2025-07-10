<?php

namespace App\Http\Livewire\EmrRIHariPulang;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;


class EmrRIHariPulang extends Component
{
    use WithPagination;


    // primitive Variable
    public string $myTitle = 'Upload FIle Rawat Inap Harian Pulang';
    public string $mySnipt = 'Rekam Rawat Inap Pasien';
    public string $myProgram = 'Pasien Rawat Inap Harian Pulang';

    public array $myLimitPerPages = [5, 10, 15, 20, 100];
    // limit record per page -resetExcept////////////////
    public int $limitPerPage = 10;

    // my Top Bar
    public array $myTopBar = [


        'refStatusId' => 'P',
        'refStatusDesc' => 'Antrian',

        'klaimStatusId' => 'BPJS',
        'klaimStatusName' => 'BPJS',
        'klaimStatusOptions' => [
            [
                'klaimStatusId' => 'UMUM',
                'klaimStatusName' => 'UMUM'
            ],
            [
                'klaimStatusId' => 'BPJS',
                'klaimStatusName' => 'BPJS'
            ],
            [
                'klaimStatusId' => 'KRONIS',
                'klaimStatusName' => 'KRONIS'
            ],
        ],
    ];

    public string $refFilter = '';
    // search logic -resetExcept////////////////
    protected $queryString = [
        'refFilter' => ['except' => '', 'as' => 'cariData'],
        'page' => ['except' => 1, 'as' => 'p'],
    ];

    // reset page when myTopBar Change
    public function updatedReffilter()
    {
        $this->resetPage();
    }

    //  modal status////////////////

    public int $riHdrNoRef;
    public string $regNoRef;

    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////
    public function settermyTopBarklaimStatusOptions($klaimStatusId, $klaimStatusName): void
    {

        $this->myTopBar['klaimStatusId'] = $klaimStatusId;
        $this->myTopBar['klaimStatusName'] = $klaimStatusName;
        $this->resetPage();
    }


    // when new form instance
    public function mount() {}

    // select data start////////////////
    public function render()
    {

        // set mySearch
        $mySearch = $this->refFilter;
        $myRefstatusId = $this->myTopBar['refStatusId'];
        $myRefklaimStatusId = $this->myTopBar['klaimStatusId'];




        //untuk membuat where clause yang berada pada table json kita pakai metode
        // Query Builder → Collection → Filter → Query Builder Lagi
        // 1️⃣ Ambil data dari database (Query Builder)
        // 2️⃣ Gunakan filter() untuk menyaring data dalam Collection
        // 3️⃣ Ambil kembali drId hasil filter, lalu kembalikan ke Query Builder
        // Ambil hanya ID dokter yang terfilter
        // 4️⃣ Jalankan ulang Query Builder dengan whereIn()
        //////////////////////////////////////////
        // Query ///////////////////////////////
        //////////////////////////////////////////
        $query = DB::table('rsview_rihdrs')
            ->select(
                DB::raw("to_char(entry_date,'dd/mm/yyyy hh24:mi:ss') AS entry_date"),
                DB::raw("to_char(entry_date,'yyyymmddhh24miss') AS entry_date1"),
                DB::raw("to_char(exit_date,'dd/mm/yyyy hh24:mi:ss') AS exit_date"),
                DB::raw("to_char(exit_date,'yyyymmddhh24miss') AS exit_date1"),
                'rihdr_no',
                'reg_no',
                'reg_name',
                'sex',
                'address',
                'thn',
                'klaim_id',
                'vno_sep',
                'ri_status',
                'datadaftarri_json',
                'room_id',
                'room_name',
                'bangsal_id',
                'bangsal_name',
                'bed_no',
            )
            ->where(DB::raw("nvl(ri_status,'I')"), '=', $myRefstatusId)
            ->whereIn('klaim_id', function ($query) use ($myRefklaimStatusId) {
                $query->select('klaim_id')
                    ->from('rsmst_klaimtypes')
                    ->where('klaim_status', '=', $myRefklaimStatusId);
            })
            ->where(function ($q) use ($mySearch) {
                $q->Where(DB::raw('upper(reg_name)'), 'like', '%' . strtoupper($mySearch) . '%')
                    ->orWhere(DB::raw('upper(reg_no)'), 'like', '%' . strtoupper($mySearch) . '%')
                    ->orWhere(DB::raw('upper(vno_sep)'), 'like', '%' . strtoupper($mySearch) . '%')
                    ->orWhere(DB::raw('upper(dr_name)'), 'like', '%' . strtoupper($mySearch) . '%');
            })
            ->orderBy('exit_date1',  'desc')
            ->orderBy('dr_name',  'desc')
            ->paginate($this->limitPerPage);
        ////////////////////////////////////////////////
        // end Query
        ///////////////////////////////////////////////



        return view(
            'livewire.emr-r-i-hari-pulang.emr-r-i-hari-pulang',
            ['myQueryData' => $query]
        );
    }
    // select data end////////////////


}
