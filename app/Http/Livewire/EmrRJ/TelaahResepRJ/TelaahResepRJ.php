<?php

namespace App\Http\Livewire\EmrRJ\TelaahResepRJ;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;


class TelaahResepRJ extends Component
{
    use WithPagination;

    protected $listeners = [
        'syncronizeAssessmentDokterRJFindData' => 'sumAll',
        'syncronizeAssessmentPerawatRJFindData' => 'sumAll'
    ];

    // primitive Variable
    public string $myTitle = 'Resep Rawat Jalan';
    public string $mySnipt = 'Rekam Medis Pasien';
    public string $myProgram = 'Pasien Rawat Jalan';

    public array $myLimitPerPages = [5, 10, 15, 20, 100];
    // limit record per page -resetExcept////////////////
    public int $limitPerPage = 10;


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
        ]
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

    public function updatedMytopbarRefdate()
    {
        $this->resetPage();
    }

    public function updatedMytopbarRefstatusid()
    {
        $this->resetPage();
    }



    // setter myTopBar Shift and myTopBar refDate
    private function settermyTopBarShiftandmyTopBarrefDate(): void
    {
        // dd/mm/yyyy hh24:mi:ss
        $this->myTopBar['refDate'] = Carbon::now()->format('d/m/Y');
        // dd(Carbon::now()->format('H:i:s'));

        // shift
        $findShift = DB::table('rstxn_shiftctls')->select('shift')
            ->whereRaw("'" . Carbon::now()->format('H:i:s') . "' between
             shift_start and shift_end")
            ->first();
        $this->myTopBar['refShiftId'] = isset($findShift->shift) && $findShift->shift ? $findShift->shift : 3;
    }


    private function gettermyTopBardrOptions(): void
    {
        $myRefdate = $this->myTopBar['refDate'];

        // Query
        $query = DB::table('rsview_rjkasir')
            ->select(
                'dr_id',
                'dr_name',
            )
            ->where(DB::raw("to_char(rj_date,'dd/mm/yyyy')"), '=', $myRefdate)
            ->groupBy('dr_id')
            ->groupBy('dr_name')
            ->orderBy('dr_name', 'desc')
            ->get();

        // loop and set Ref
        $query->each(function ($item, $key) {
            $this->myTopBar['drOptions'][$key + 1]['drId'] = $item->dr_id;
            $this->myTopBar['drOptions'][$key + 1]['drName'] = $item->dr_name;
        })->toArray();
    }

    public function settermyTopBardrOptions($drId, $drName): void
    {

        $this->myTopBar['drId'] = $drId;
        $this->myTopBar['drName'] = $drName;
        $this->resetPage();
    }



    // open and close modal start////////////////
    //  modal status////////////////


    public bool $isOpenAdministrasi = false;
    public string $isOpenModeAdministrasi = 'insert';



    public bool $forceInsertRecord = false;

    public int $rjNoRef;
    public string $regNoRef;

    public int $sumRsAdmin;
    public int $sumRjAdmin;
    public int $sumPoliPrice;

    public int $sumJasaKaryawan;
    public int $sumJasaDokter;
    public int $sumJasaMedis;

    public int $sumObat;
    public int $sumLaboratorium;
    public int $sumRadiologi;

    public int $sumLainLain;

    public int $sumTotalRJ;





    private function openModalEditAdministrasi($rjNo, $regNoRef): void
    {
        $this->isOpenAdministrasi = true;
        $this->isOpenModeAdministrasi = 'update';
        $this->rjNoRef = $rjNo;
        $this->regNoRef = $regNoRef;
        $this->sumAll();
    }


    public function closeModalAdministrasi(): void
    {
        $this->isOpenAdministrasi = false;
        $this->isOpenModeAdministrasi = 'insert';
        $this->resetInputFields();
    }

    public function editAdministrasi($rjNo, $regNoRef)
    {
        $this->openModalEditAdministrasi($rjNo, $regNoRef);
        // $this->findData($id);
    }

    // open and close modal end////////////////


    // resert input private////////////////
    private function resetInputFields(): void
    {

        // resert validation
        $this->resetValidation();
        // resert input

    }
    // resert input private////////////////





    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////

    // is going to insert data////////////////





    public string $activeTabAdministrasi = "JasaKaryawan";
    public array $EmrMenuAdministrasi = [
        [
            'ermMenuId' => 'JasaKaryawan',
            'ermMenuName' => 'Jasa Karyawan'
        ],
        [
            'ermMenuId' => 'JasaDokter',
            'ermMenuName' => 'Jasa Dokter'
        ],
        [
            'ermMenuId' => 'JasaMedis',
            'ermMenuName' => 'Jasa Medis'
        ],
        [
            'ermMenuId' => 'Obat',
            'ermMenuName' => 'Obat'
        ],
        [
            'ermMenuId' => 'Laboratorium',
            'ermMenuName' => 'Laboratorium'
        ],
        [
            'ermMenuId' => 'Radiologi',
            'ermMenuName' => 'Radiologi'
        ],
        [
            'ermMenuId' => 'LainLain',
            'ermMenuName' => 'Lain-Lain'
        ],

    ];


    public function checkTelaahResepStatus($eresep)
    {
        if (!$eresep) {
            $this->emit('toastr-error', 'E-Resep Tidak ditemukan');
        }
    }

    public function sumAll()
    {
        $this->sumAdmin();
    }

    private function sumAdmin()
    {
        $sumAdmin = $this->findData($this->rjNoRef);

        $this->sumRsAdmin = $sumAdmin['rsAdmin'];
        $this->sumRjAdmin = $sumAdmin['rjAdmin'];
        $this->sumPoliPrice = $sumAdmin['poliPrice'];

        $this->sumJasaKaryawan = collect($sumAdmin['JasaKaryawan'])->sum('JasaKaryawanPrice');
        $this->sumJasaMedis = collect($sumAdmin['JasaMedis'])->sum('JasaMedisPrice');
        $this->sumJasaDokter = collect($sumAdmin['JasaDokter'])->sum('JasaDokterPrice');

        $this->sumObat = collect($sumAdmin['rjObat'])->sum((function ($obat) {
            return $obat['qty'] * $obat['price'];
        }));

        $this->sumLaboratorium = collect($sumAdmin['rjLab'])->sum((function ($obat) {
            return $obat['lab_price'];
        }));

        $this->sumRadiologi = collect($sumAdmin['rjRad'])->sum((function ($obat) {
            return $obat['rad_price'];
        }));

        $this->sumLainLain = collect($sumAdmin['LainLain'])->sum('LainLainPrice');


        $this->sumTotalRJ = $this->sumPoliPrice + $this->sumRjAdmin + $this->sumRsAdmin  + $this->sumJasaKaryawan + $this->sumJasaDokter + $this->sumJasaMedis + $this->sumLainLain + $this->sumObat + $this->sumLaboratorium + $this->sumRadiologi;
    }




    private function findData($rjno): array
    {
        $findData = DB::table('rsview_rjkasir')
            ->select('datadaftarpolirj_json', 'vno_sep')
            ->where('rj_no', $rjno)
            ->first();


        $dataDaftarPoliRJ_json = isset($findData->datadaftarpolirj_json) ? $findData->datadaftarpolirj_json   : null;
        // if meta_data_pasien_json = null
        // then cari Data Pasien By Key Collection (exception when no data found)
        //
        // else json_decode
        if ($dataDaftarPoliRJ_json) {
            $rsAdmin = DB::table('rstxn_rjhdrs')
                ->select('rs_admin', 'rj_admin', 'poli_price')
                ->where('rj_no', $rjno)
                ->first();

            $rsObat = DB::table('rstxn_rjobats')
                ->join('immst_products', 'immst_products.product_id', 'rstxn_rjobats.product_id')
                ->select('rstxn_rjobats.product_id as product_id', 'product_name', 'qty', 'price', 'rjobat_dtl')
                ->where('rj_no', $rjno)
                ->get();

            $rsLab = DB::table('rstxn_rjlabs')
                ->select('lab_desc', 'lab_price', 'lab_dtl')
                ->where('rj_no', $rjno)
                ->get();

            $rsRad = DB::table('rstxn_rjrads')
                ->join('rsmst_radiologis', 'rsmst_radiologis.rad_id', 'rstxn_rjrads.rad_id')
                ->select('rad_desc', 'rstxn_rjrads.rad_price as rad_price', 'rad_dtl')
                ->where('rj_no', $rjno)
                ->get();

            $dataRawatJalan = json_decode($findData->datadaftarpolirj_json, true);
            $dataRawatJalan['rsAdmin'] = $rsAdmin->rs_admin ? $rsAdmin->rs_admin : 0;
            $dataRawatJalan['rjAdmin'] = $rsAdmin->rj_admin ? $rsAdmin->rj_admin : 0;
            $dataRawatJalan['poliPrice'] = $rsAdmin->poli_price ? $rsAdmin->poli_price : 0;
            $dataRawatJalan['rjObat'] = json_decode(json_encode($rsObat, true), true);
            $dataRawatJalan['rjLab'] = json_decode(json_encode($rsLab, true), true);
            $dataRawatJalan['rjRad'] = json_decode(json_encode($rsRad, true), true);


            return ($dataRawatJalan);
        }

        return [];
    }



    // when new form instance
    public function mount()
    {
        $this->settermyTopBarShiftandmyTopBarrefDate();
    }




    // select data start////////////////
    public function render()
    {
        $this->gettermyTopBardrOptions();

        // set mySearch
        $mySearch = $this->refFilter;
        $myRefdate = $this->myTopBar['refDate'];
        // $myRefshift = $this->myTopBar['refShiftId'];
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
                'datadaftarpolirj_json'
            )
            ->where(DB::raw("nvl(rj_status,'A')"), '=', $myRefstatusId)
            // ->where('rj_status', '!=', 'F')
            ->where('klaim_id', '!=', 'KR')

            // ->where('shift', '=', $myRefshift)
            ->where(DB::raw("to_char(rj_date,'dd/mm/yyyy')"), '=', $myRefdate);

        // Jika where dokter tidak kosong
        if ($myRefdrId != 'All') {
            $query->where('dr_id', $myRefdrId);
        }

        $query->where(function ($q) use ($mySearch) {
            $q->Where(DB::raw('upper(reg_name)'), 'like', '%' . strtoupper($mySearch) . '%')
                ->orWhere(DB::raw('upper(reg_no)'), 'like', '%' . strtoupper($mySearch) . '%')
                ->orWhere(DB::raw('upper(dr_name)'), 'like', '%' . strtoupper($mySearch) . '%');
        })
            ->orderBy('rj_date1',  'desc')
            ->orderBy('rj_no',  'desc');

        ////////////////////////////////////////////////
        // end Query
        ///////////////////////////////////////////////



        return view(
            'livewire.emr-r-j.telaah-resep-r-j.telaah-resep-r-j',
            ['myQueryData' => $query->paginate($this->limitPerPage)]
        );
    }
    // select data end////////////////


}