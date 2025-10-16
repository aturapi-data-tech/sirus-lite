<?php

namespace App\Http\Livewire\EmrRJ\AntrianResepRJ;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;

use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

use Carbon\Carbon;

use App\Http\Traits\EmrRJ\EmrRJTrait;

class AntrianResepRJ extends Component
{
    use WithPagination, EmrRJTrait;

    protected $listeners = [];

    // primitive Variable
    public string $myTitle = 'Antrian Resep Rawat Jalan';
    public string $mySnipt = 'Antrian Pasien';
    public string $myProgram = 'Pasien Rawat Jalan';
    public int $limitPerPage = 30;


    // my Top Bar
    public array $myTopBar = [];

    // search logic -resetExcept////////////////
    protected $queryString = [
        'page' => ['except' => 1, 'as' => 'p'],
    ];




    // setter myTopBar Shift and myTopBar refDate
    private function settermyTopBar(): void
    {
        // dd/mm/yyyy hh24:mi:ss
        $this->myTopBar['refDate'] = Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y');
        // shift
        $findShift = DB::table('rstxn_shiftctls')->select('shift')
            ->whereRaw("'" . Carbon::now(env('APP_TIMEZONE'))->format('H:i:s') . "' between
             shift_start and shift_end")
            ->first();
        $this->myTopBar['refShiftId'] = isset($findShift->shift) && $findShift->shift ? $findShift->shift : 3;
    }




    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////


    // when new form instance
    public function mount()
    {
        $this->settermyTopBar();
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
        $myRefdate = $this->myTopBar['refDate'];

        //////////////////////////////////////////
        // Query ///////////////////////////////
        //////////////////////////////////////////
        $query = DB::table('rsview_rjkasir')
            ->select(
                'rj_status',
                DB::raw("to_char(rj_date,'dd/mm/yyyy hh24:mi:ss') AS rj_date"),
                'reg_name',
                'poli_desc',
                'dr_name',
                DB::raw("DBMS_LOB.SUBSTR(datadaftarpolirj_json, 4000,     1) AS j1"),
                DB::raw("DBMS_LOB.SUBSTR(datadaftarpolirj_json, 4000,  4001) AS j2"),
                DB::raw("DBMS_LOB.SUBSTR(datadaftarpolirj_json, 4000,  8001) AS j3"),
                DB::raw("DBMS_LOB.SUBSTR(datadaftarpolirj_json, 4000, 12001) AS j4"),
                DB::raw("DBMS_LOB.SUBSTR(datadaftarpolirj_json, 4000, 16001) AS j5"),
                DB::raw("DBMS_LOB.SUBSTR(datadaftarpolirj_json, 4000, 20001) AS j6"),
                DB::raw("DBMS_LOB.SUBSTR(datadaftarpolirj_json, 4000, 24001) AS j7"),
                DB::raw("DBMS_LOB.SUBSTR(datadaftarpolirj_json, 4000, 28001) AS j8"),
            )
            ->whereIn(DB::raw("nvl(rj_status,'A')"), ['A', 'L'])
            ->where('klaim_id', '!=', 'KR')
            // ->where('rj_status', '!=', 'F')
            // ->where('shift', '=', $myRefshift)
            ->where(DB::raw("to_char(rj_date,'dd/mm/yyyy')"), '=', $myRefdate);



        // 1 urutkan berdasarkan json table
        $rows = $query->get()->map(function ($r) {
            // gabung hanya chunk yang ada (stop saat ketemu null/empty)
            $raw = '';
            foreach (['j1', 'j2', 'j3', 'j4', 'j5', 'j6', 'j7', 'j8'] as $k) {
                $part = $r->$k ?? '';
                if ($part === '' || $part === null) break;
                $raw .= $part;
                unset($r->$k); // bersihkan properti chunk biar objek r lebih ringkas
            }

            // aman: selalu string → tidak akan OCILob
            $r->datadaftarpolirj_json = json_decode($raw !== '' ? $raw : '[]', true, 512, JSON_PARTIAL_OUTPUT_ON_ERROR) ?? [];

            return $r;
        });

        $rowsAntri = $rows->filter(fn($r) => ($r->rj_status ?? 'A') === 'A');
        $rowsLunas = $rows->filter(fn($r) => ($r->rj_status ?? 'A') === 'L');
        // Sort ketiga: datadaftarpolirj_json (desc)
        $rowsAntri = $rowsAntri->sortBy(
            function ($mySortByJson) {
                // Decode JSON payload
                $raw = $mySortByJson->datadaftarpolirj_json ?? '[]';
                // aman untuk decode
                $jsonData = $raw ?: [];

                // 1) Ambil nomor antrian apotek (0 jika tidak ada)
                $pharmacyQueueNumber = $jsonData['noAntrianApotek']['noAntrian'] ?? 1000;


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


        $rowsLunas = $rowsLunas->sortByDesc(
            function ($mySortByJson) {
                // Decode JSON payload
                $raw = $mySortByJson->datadaftarpolirj_json ?? '[]';
                // aman untuk decode
                $jsonData = $raw ?: [];

                // 1) Ambil nomor antrian apotek (0 jika tidak ada)
                $pharmacyQueueNumber = $jsonData['noAntrianApotek']['noAntrian'] ?? 1000;


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

        $rowsAntri = $this->paginate($rowsAntri, $this->limitPerPage);
        $rowsLunas = $this->paginate($rowsLunas, $this->limitPerPage);



        ////////////////////////////////////////////////
        // end Query
        ///////////////////////////////////////////////



        return view(
            'livewire.emr-r-j.antrian-resep-r-j.antrian-resep-r-j',
            [
                'myQueryDataAntri' => $rowsAntri,
                'myQueryDataLunas' => $rowsLunas,
            ],


        );
    }
    // select data end////////////////


}
