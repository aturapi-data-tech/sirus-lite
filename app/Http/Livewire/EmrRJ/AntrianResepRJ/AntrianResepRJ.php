<?php

namespace App\Http\Livewire\EmrRJ\AntrianResepRJ;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;

use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

use Carbon\Carbon;

use Spatie\ArrayToXml\ArrayToXml;

use App\Http\Traits\EmrRJ\EmrRJTrait;
use App\Http\Traits\BPJS\AntrianTrait;
use Exception;

class AntrianResepRJ extends Component
{
    use WithPagination, EmrRJTrait;

    protected $listeners = [
        'syncronizeAssessmentDokterRJFindData' => 'sumAll',
        'syncronizeAssessmentPerawatRJFindData' => 'sumAll'
    ];

    // primitive Variable
    public string $myTitle = 'Antrian Resep Rawat Jalan';
    public string $mySnipt = 'Antrian Pasien';
    public string $myProgram = 'Pasien Rawat Jalan';

    public array $myLimitPerPages = [5, 10, 15, 20, 100];
    // limit record per page -resetExcept////////////////
    public int $limitPerPage = 10;

    public array $dataDaftarPoliRJ = [];



    // my Top Bar
    public array $myTopBar = [
        'refDate' => '',

        'refShiftId' => '1',
        'refShiftDesc' => '1',
        'refShiftOptions' => [
            ['refShiftId' => '1', 'refShiftDesc' => '1'],
            ['refShiftId' => '2', 'refShiftDesc' => '2'],
            ['refShiftId' => '3', 'refShiftDesc' => '3'],
        ],

        'refStatusId' => 'A',
        'refStatusDesc' => 'Antrian',
        'refStatusOptions' => [
            ['refStatusId' => 'A', 'refStatusDesc' => 'Antrian'],
            ['refStatusId' => 'L', 'refStatusDesc' => 'Selesai'],
            ['refStatusId' => 'I', 'refStatusDesc' => 'Transfer'],
        ],

        'drId' => 'All',
        'drName' => 'All',
        'drOptions' => [
            [
                'drId' => 'All',
                'drName' => 'All'
            ]
        ],
        'autoRefresh' => 'Ya',
        'autoRefreshOptions' => [
            ['autoRefresh' => 'Ya'],
            ['autoRefresh' => 'Tidak']
        ]
    ];

    public string $refFilter = '';
    // search logic -resetExcept////////////////
    protected $queryString = [
        'refFilter' => ['except' => '', 'as' => 'cariData'],
        'page' => ['except' => 1, 'as' => 'p'],
    ];

    // reset page when myTopBar Change



    // setter myTopBar Shift and myTopBar refDate
    private function settermyTopBarShiftandmyTopBarrefDate(): void
    {
        // dd/mm/yyyy hh24:mi:ss
        $this->myTopBar['refDate'] = Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y');
        // dd(Carbon::now(env('APP_TIMEZONE'))->format('H:i:s'));

        // shift
        $findShift = DB::table('rstxn_shiftctls')->select('shift')
            ->whereRaw("'" . Carbon::now(env('APP_TIMEZONE'))->format('H:i:s') . "' between
             shift_start and shift_end")
            ->first();
        $this->myTopBar['refShiftId'] = isset($findShift->shift) && $findShift->shift ? $findShift->shift : 3;
    }







    public int $rjNoRef;
    public string $regNoRef;









    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////

    // is going to insert data////////////////












    // when new form instance
    public function mount()
    {
        $this->settermyTopBarShiftandmyTopBarrefDate();
    }


    private function paginate($items, $perPage = 5, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    // select data start////////////////
    public function render()
    {

        // set mySearch
        $mySearch = $this->refFilter;
        $myRefdate = $this->myTopBar['refDate'];
        $myRefstatusId = $this->myTopBar['refStatusId'];
        $myRefdrId = $this->myTopBar['drId'];



        //////////////////////////////////////////
        // Query ///////////////////////////////
        //////////////////////////////////////////
        $query = DB::table('rsview_rjkasir')
            ->select(
                DB::raw("to_char(rj_date,'dd/mm/yyyy hh24:mi:ss') AS rj_date"),
                DB::raw("to_char(rj_date,'yyyymmddhh24miss') AS rj_date1"),
                'rj_no',
                'reg_no',
                'reg_name',
                'sex',
                'address',
                'thn',
                DB::raw("to_char(birth_date,'dd/mm/yyyy') AS birth_date"),
                'poli_id',
                'poli_desc',
                'dr_id',
                'dr_name',
                'klaim_id',
                'shift',
                'vno_sep',
                'no_antrian',
                'rj_status',
                'nobooking',
                'push_antrian_bpjs_status',
                'push_antrian_bpjs_json',
                DB::raw("CAST(DBMS_LOB.SUBSTR(datadaftarpolirj_json, 4000, 1) AS VARCHAR2(4000)) AS datadaftarpolirj_json"),
                DB::raw("(select count(*) from lbtxn_checkuphdrs where status_rjri='RJ' and checkup_status!='B' and ref_no = rsview_rjkasir.rj_no) AS lab_status"),
                DB::raw("(select count(*) from rstxn_rjrads where rj_no = rsview_rjkasir.rj_no) AS rad_status")
            )
            ->where(DB::raw("nvl(rj_status,'A')"), '=', $myRefstatusId)
            // ->where('rj_status', '!=', 'F')
            ->where('klaim_id', '!=', 'KR')

            // ->where('shift', '=', $myRefshift)
            ->where(DB::raw("to_char(rj_date,'dd/mm/yyyy')"), '=', $myRefdate);

        // 1 urutkan berdasarkan json table
        $myQueryPagination = $query->get();

        // Sort ketiga: datadaftarpolirj_json (desc)
        $myQueryPagination = $myQueryPagination->sortBy(
            function ($mySortByJson) {
                // Decode JSON payload
                $raw = $mySortByJson->datadaftarpolirj_json ?? '[]';
                // aman untuk decode
                $jsonData = json_decode($raw, true) ?: [];

                // 1) Ambil nomor antrian apotek (0 jika tidak ada)
                $pharmacyQueueNumber = (int) data_get($jsonData, 'noAntrianApotek.noAntrian', 0) ?: 1000;


                // 2) Flag untuk grouping: 1 = punya antrian, 0 = tidak
                $hasPharmacyQueue = $pharmacyQueueNumber > 0 ? 1 : 0;

                // 3) Flag transaksi selesai: 1 jika sudah ada eresep + administrasirj
                // flag masing-masing
                $hasEresep             = isset($jsonData['eresep'])             ? 1 : 0;
                $hasAdministrasiRj     = isset($jsonData['AdministrasiRj'])     ? 1 : 0;

                // 4) Parse timestamp taskId5 (fallback ke VERY LARGE agar muncul di akhir jika null)
                $task5Time = isset($jsonData['taskIdPelayanan']['taskId5'])
                    ? strtotime(str_replace('/', '-', $jsonData['taskIdPelayanan']['taskId5']))
                    : PHP_INT_MAX;

                // 5) Parse timestamp taskId6 (sama fallback)
                $task6Time = isset($jsonData['taskIdPelayanan']['taskId6'])
                    ? strtotime(str_replace('/', '-', $jsonData['taskIdPelayanan']['taskId6']))
                    : PHP_INT_MAX;

                // 6) Parse timestamp rjDateTime (fallback ke VERY LARGE agar muncul di akhir jika null)
                $rjDateTime = isset($jsonData['rjDate'])
                    ? strtotime(str_replace('/', '-', $jsonData['rjDate']))
                    : PHP_INT_MAX;


                // Composite key untuk sortByDesc:
                return [
                    $hasPharmacyQueue,                       // grup antrian
                    $hasPharmacyQueue ? $pharmacyQueueNumber : 1000, // nilai antrian (DESC)
                    $hasPharmacyQueue ? 0 : $hasEresep, // transaksi selesai (DESC)
                    $hasPharmacyQueue ? 0 : $hasAdministrasiRj, // transaksi selesai (DESC)
                    $hasPharmacyQueue ? 0 : -$task5Time,    // taskId5 pertama (ascending)
                    $hasPharmacyQueue ? 0 : -$task6Time,    // taskId6 berikutnya (ascending)
                    $hasPharmacyQueue ? 0 : -$rjDateTime,    // rjDateTime pertama (ascending)
                ];
            }
        );


        $myQueryPagination = $this->paginate($myQueryPagination, $this->limitPerPage);


        ////////////////////////////////////////////////
        // end Query
        ///////////////////////////////////////////////



        return view(
            'livewire.emr-r-j.antrian-resep-r-j.antrian-resep-r-j',
            // ['myQueryData' => $query->paginate($this->limitPerPage)],
            ['myQueryData' => $myQueryPagination],


        );
    }
    // select data end////////////////


}
