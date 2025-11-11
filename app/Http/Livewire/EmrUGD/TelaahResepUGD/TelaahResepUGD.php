<?php

namespace App\Http\Livewire\EmrUGD\TelaahResepUGD;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Cache\LockTimeoutException;

use Livewire\Component;

use Livewire\WithPagination;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

use Carbon\Carbon;
use App\Http\Traits\EmrUGD\EmrUGDTrait;


class TelaahResepUGD extends Component
{
    use  WithPagination, EmrUGDTrait;

    protected $listeners = [
        'ugd:refresh-summary' => 'sumAll',
    ];

    // primitive Variable
    public string $myTitle = 'Resep UGD';
    public string $mySnipt = 'Rekam Medis Pasien';
    public string $myProgram = 'Pasien UGD';

    public array $myLimitPerPages = [5, 10, 15, 20, 100];
    // limit record per page -resetExcept////////////////
    public int $limitPerPage = 10;

    public array $dataDaftarUgd = [];

    public array $telaahResep = [
        "kejelasanTulisanResep" => [
            "kejelasanTulisanResep" => "Ya",
            "kejelasanTulisanResepOptions" => [
                ["kejelasanTulisanResep" => "Ya"],
                ["kejelasanTulisanResep" => "Tidak"],
            ],
            "desc" => "",
        ],
        "tepatObat" => [
            "tepatObat" => "Ya",
            "tepatObatOptions" => [
                ["tepatObat" => "Ya"],
                ["tepatObat" => "Tidak"],
            ],
            "desc" => "",
        ],
        "tepatDosis" => [
            "tepatDosis" => "Ya",
            "tepatDosisOptions" => [
                ["tepatDosis" => "Ya"],
                ["tepatDosis" => "Tidak"],
            ],
            "desc" => "",
        ],
        "tepatRute" => [
            "tepatRute" => "Ya",
            "tepatRuteOptions" => [
                ["tepatRute" => "Ya"],
                ["tepatRute" => "Tidak"],
            ],
            "desc" => "",
        ],
        "tepatWaktu" => [
            "tepatWaktu" => "Ya",
            "tepatWaktuOptions" => [
                ["tepatWaktu" => "Ya"],
                ["tepatWaktu" => "Tidak"],
            ],
            "desc" => "",
        ],
        "duplikasi" => [
            "duplikasi" => "Tidak",
            "duplikasiOptions" => [
                ["duplikasi" => "Ya"],
                ["duplikasi" => "Tidak"],
            ],
            "desc" => "",
        ],
        "alergi" => [
            "alergi" => "Tidak",
            "alergiOptions" => [
                ["alergi" => "Ya"],
                ["alergi" => "Tidak"],
            ],
            "desc" => "",
        ],
        "interaksiObat" => [
            "interaksiObat" => "Tidak",
            "interaksiObatOptions" => [
                ["interaksiObat" => "Ya"],
                ["interaksiObat" => "Tidak"],
            ],
            "desc" => "",
        ],
        "bbPasienAnak" => [
            "bbPasienAnak" => "Ya",
            "bbPasienAnakOptions" => [
                ["bbPasienAnak" => "Ya"],
                ["bbPasienAnak" => "Tidak"],
            ],
            "desc" => "",
        ],
        "kontraIndikasiLain" => [
            "kontraIndikasiLain" => "Tidak",
            "kontraIndikasiLainOptions" => [
                ["kontraIndikasiLain" => "Ya"],
                ["kontraIndikasiLain" => "Tidak"],
            ],
            "desc" => "",
        ],
    ];

    public array $telaahObat = [
        "obatdgnResep" => [
            "obatdgnResep" => "Ya",
            "obatdgnResepOptions" => [
                ["obatdgnResep" => "Ya"],
                ["obatdgnResep" => "Tidak"],
            ],
            "desc" => "",
        ],
        "jmlDosisdgnResep" => [
            "jmlDosisdgnResep" => "Ya",
            "jmlDosisdgnResepOptions" => [
                ["jmlDosisdgnResep" => "Ya"],
                ["jmlDosisdgnResep" => "Tidak"],
            ],
            "desc" => "",
        ],
        "rutedgnResep" => [
            "rutedgnResep" => "Ya",
            "rutedgnResepOptions" => [
                ["rutedgnResep" => "Ya"],
                ["rutedgnResep" => "Tidak"],
            ],
            "desc" => "",
        ],
        "waktuFrekPemberiandgnResep" => [
            "waktuFrekPemberiandgnResep" => "Ya",
            "waktuFrekPemberiandgnResepOptions" => [
                ["waktuFrekPemberiandgnResep" => "Ya"],
                ["waktuFrekPemberiandgnResep" => "Tidak"],
            ],
            "desc" => "",
        ],
    ];

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
        $this->myTopBar['refDate'] = Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y');
        // dd(Carbon::now(env('APP_TIMEZONE'))->format('H:i:s'));

        // shift
        $findShift = DB::table('rstxn_shiftctls')->select('shift')
            ->whereRaw("'" . Carbon::now(env('APP_TIMEZONE'))->format('H:i:s') . "' between
             shift_start and shift_end")
            ->first();
        $this->myTopBar['refShiftId'] = isset($findShift->shift) && $findShift->shift ? $findShift->shift : 3;
    }


    private function gettermyTopBardrOptions(): void
    {
        $myRefdate = $this->myTopBar['refDate'];

        // Query
        $query = DB::table('rsview_ugdkasir')
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

    public bool $isOpenTelaahResep = false;
    public string $isOpenModeTelaahResep = 'insert';



    public bool $forceInsertRecord = false;

    public int $eresepRacikan;
    public int $eresep;

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
        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Program dalam masa proses pengembangan');
        return;
        $this->openModalEditAdministrasi($rjNo, $regNoRef);
        // $this->findData($id);
    }


    private function openModalEditTelaahResep($rjNo, $regNoRef): void
    {
        $this->isOpenTelaahResep = true;
        $this->isOpenModeTelaahResep = 'update';
        $this->rjNoRef = $rjNo;
        $this->regNoRef = $regNoRef;
        $this->sumAll();
    }


    public function closeModalTelaahResep(): void
    {
        $this->isOpenTelaahResep = false;
        $this->isOpenModeTelaahResep = 'insert';
        $this->resetInputFields();
    }

    public function editTelaahResep($eresep, $rjNo, $regNoRef)
    {
        if (!$eresep) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('E-Resep Tidak ditemukan');
        } else {
            $this->openModalEditTelaahResep($rjNo, $regNoRef);
        }
    }

    // open and close modal end////////////////


    // resert input private////////////////
    private function resetInputFields(): void
    {
        $this->resetValidation();
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




    public function sumAll()
    {
        $this->sumAdmin();
    }

    private function sumAdmin(): void
    {
        $sumAdmin = $this->findData($this->rjNoRef);


        $this->eresepRacikan = collect(isset($sumAdmin['eresepRacikan']) ? $sumAdmin['eresepRacikan'] : [])->count();
        $this->eresep = collect(isset($sumAdmin['eresep']) ? $sumAdmin['eresep'] : [])->count();


        $this->sumRsAdmin = $sumAdmin['rsAdmin'] ? $sumAdmin['rsAdmin'] : 0;
        $this->sumRjAdmin = $sumAdmin['rjAdmin'] ? $sumAdmin['rjAdmin'] : 0;
        $this->sumPoliPrice = $sumAdmin['poliPrice'] ? $sumAdmin['poliPrice'] : 0;

        $this->sumJasaKaryawan = isset($sumAdmin['JasaKaryawan']) ? collect($sumAdmin['JasaKaryawan'])->sum('JasaKaryawanPrice') : 0;
        $this->sumJasaMedis = isset($sumAdmin['JasaMedis']) ? collect($sumAdmin['JasaMedis'])->sum('JasaMedisPrice') : 0;
        $this->sumJasaDokter = isset($sumAdmin['JasaDokter']) ? collect($sumAdmin['JasaDokter'])->sum('JasaDokterPrice') : 0;


        $this->sumObat = collect($sumAdmin['rjObat'])->sum((function ($obat) {
            return $obat['qty'] * $obat['price'];
        }));

        $this->sumLaboratorium = collect($sumAdmin['rjLab'])->sum((function ($obat) {
            return $obat['lab_price'];
        }));

        $this->sumRadiologi = collect($sumAdmin['rjRad'])->sum((function ($obat) {
            return $obat['rad_price'];
        }));


        $this->sumLainLain = isset($sumAdmin['LainLain']) ? collect($sumAdmin['LainLain'])->sum('LainLainPrice') : 0;


        $this->sumTotalRJ = $this->sumPoliPrice + $this->sumRjAdmin + $this->sumRsAdmin  + $this->sumJasaKaryawan + $this->sumJasaDokter + $this->sumJasaMedis + $this->sumLainLain + $this->sumObat + $this->sumLaboratorium + $this->sumRadiologi;
    }

    public function setttdTelaahResep($rjNo)
    {
        $this->setTtdSection($rjNo, 'telaahResep');
    }

    public function setttdTelaahObat($rjNo)
    {
        $this->setTtdSection($rjNo, 'telaahObat');
    }

    private function setTtdSection(string $rjNo, string $section): void
    {
        $user = auth()->user();
        $userName = $user->myuser_name ?? '-';

        // role gate
        if (!$user->hasRole('Apoteker')) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError("Anda tidak dapat melakukan TTD-E karena user {$userName} bukan Apoteker.");
            return;
        }

        // opsional: cek status UGD aktif


        if (!$rjNo) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('rjNo kosong.');
            return;
        }

        $lockKey = "ugd:{$rjNo}";

        try {
            Cache::lock($lockKey, 5)->block(3, function () use ($rjNo, $section, $user) {
                DB::transaction(function () use ($rjNo, $section, $user) {

                    // 1) Ambil FRESH JSON dari DB agar tidak menimpa subtree lain
                    $fresh = $this->findDataUGD($rjNo) ?: [];

                    // 2) Pastikan subtree ada
                    if (!isset($fresh[$section]) || !is_array($fresh[$section])) {
                        $fresh[$section] = [];
                    }

                    // 3) Jika sudah ada penanggungJawab → informasikan & skip
                    if (isset($fresh[$section]['penanggungJawab'])) {
                        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                            ->addInfo('Sudah ditandatangani sebelumnya.');
                        // tetap sinkronkan state lokal agar UI up-to-date
                        $this->dataDaftarUgd = $fresh;
                        return;
                    }

                    // 4) Set TTD pada subtree yang dimaksud
                    $fresh[$section]['penanggungJawab'] = [
                        'userLog'     => $user->myuser_name,
                        'userLogDate' => Carbon::now(config('app.timezone'))->format('d/m/Y H:i:s'),
                        'userLogCode' => $user->myuser_code,
                    ];

                    // 5) Commit atomik: simpan JSON
                    $this->updateJsonUGD($rjNo, $fresh);

                    // 6) Sinkronkan state lokal agar UI langsung update
                    $this->dataDaftarUgd = $fresh;

                    toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                        ->addSuccess('TTD-E berhasil disimpan.');
                });
            });
        } catch (LockTimeoutException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Sistem sibuk, gagal memperoleh lock. Coba lagi.');
            return;
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Gagal menyimpan TTD-E.');
            return;
        }
    }

    private function findData($rjNo): array
    {

        $findDataUGD = $this->findDataUGD($rjNo);
        $dataUGD = $findDataUGD;


        $rsAdmin = DB::table('rstxn_ugdhdrs')
            ->select('rs_admin', 'rj_admin', 'poli_price', 'klaim_id', 'pass_status')
            ->where('rj_no', $rjNo)
            ->first();

        $rsObat = DB::table('rstxn_ugdobats')
            ->join('immst_products', 'immst_products.product_id', 'rstxn_ugdobats.product_id')
            ->select('rstxn_ugdobats.product_id as product_id', 'product_name', 'qty', 'price', 'rjobat_dtl')
            ->where('rj_no', $rjNo)
            ->get();

        $rsLab = DB::table('rstxn_ugdlabs')
            ->select('lab_desc', 'lab_price', 'lab_dtl')
            ->where('rj_no', $rjNo)
            ->get();

        $rsRad = DB::table('rstxn_ugdrads')
            ->join('rsmst_radiologis', 'rsmst_radiologis.rad_id', 'rstxn_ugdrads.rad_id')
            ->select('rad_desc', 'rstxn_ugdrads.rad_price as rad_price', 'rad_dtl')
            ->where('rj_no', $rjNo)
            ->get();


        // RJ Admin
        if ($rsAdmin->pass_status == 'N') {
            $rsAdminParameter = DB::table('rsmst_parameters')
                ->select('par_value')
                ->where('par_id', '1')
                ->first();
            if (isset($dataUGD['rjAdmin'])) {
                $dataUGD['rjAdmin'] = $rsAdmin->rj_admin;
            } else {
                $dataUGD['rjAdmin'] = $rsAdminParameter->par_value;
                // update table trnsaksi
                DB::table('rstxn_ugdhdrs')
                    ->where('rj_no', $rjNo)
                    ->update([
                        'rj_admin' => $dataUGD['rjAdmin'],
                    ]);
            }
        } else {
            $dataUGD['rjAdmin'] = 0;
        }

        // RS Admin
        $rsAdminDokter = DB::table('rsmst_doctors')
            ->select('rs_admin', 'ugd_price', 'ugd_price_bpjs')
            ->where('dr_id', $dataUGD['drId'])
            ->first();


        if (isset($dataUGD['rsAdmin'])) {
            $dataUGD['rsAdmin'] = $rsAdmin->rs_admin ? $rsAdmin->rs_admin : 0;
        } else {
            $dataUGD['rsAdmin'] = $rsAdminDokter->rs_admin ? $rsAdminDokter->rs_admin : 0;
            // update table trnsaksi
            DB::table('rstxn_ugdhdrs')
                ->where('rj_no', $rjNo)
                ->update([
                    'rs_admin' => $dataUGD['rsAdmin'],
                ]);
        }

        // PoliPrice
        // 1) Ambil klaim status (default 'UMUM' jika NULL)
        $klaimStatus = DB::table('rsmst_klaimtypes')
            ->where('klaim_id', $dataUGD['klaimId'] ?? '')
            ->value('klaim_status') ?? 'UMUM';

        // 2) Tentukan harga dokter berdasarkan status klaim
        if ($klaimStatus === 'BPJS') {
            $dokterUgdPrice = $rsAdminDokter->ugd_price_bpjs ?? 0;
        } else {
            $dokterUgdPrice = $rsAdminDokter->ugd_price ?? 0;
        }

        // 3) Set poliPrice & simpan ke transaksi Rawat Jalan
        if (isset($dataUGD['poliPrice'])) {
            // sudah ada → pakai harga admin/front-office
            $dataUGD['poliPrice'] = $rsAdmin->poli_price ? $rsAdmin->poli_price : 0;
        } else {
            // belum ada → pakai tarif dokter sesuai klaim
            $dataUGD['poliPrice'] = $dokterUgdPrice;

            DB::table('rstxn_ugdhdrs')
                ->where('rj_no', $rjNo)
                ->update([
                    'poli_price' => $dataUGD['poliPrice'],
                ]);
        }

        // Ketika Kronis
        if ($rsAdmin->klaim_id == 'KR') {
            $dataUGD['rjAdmin'] = 0;
            $dataUGD['rsAdmin'] = 0;
            $dataUGD['poliPrice'] = 0;
            // update table trnsaksi
            DB::table('rstxn_ugdhdrs')
                ->where('rj_no', $rjNo)
                ->update([
                    'rj_admin' => $dataUGD['rjAdmin'],
                    'rs_admin' => $dataUGD['rsAdmin'],
                    'poli_price' => $dataUGD['poliPrice'],
                ]);
        }



        $dataUGD['rjObat'] = json_decode(json_encode($rsObat, true), true);
        $dataUGD['rjLab'] = json_decode(json_encode($rsLab, true), true);
        $dataUGD['rjRad'] = json_decode(json_encode($rsRad, true), true);


        if (!isset($dataUGD['telaahResep'])) {
            $dataUGD['telaahResep'] = $this->telaahResep;
        }

        if (!isset($dataUGD['telaahObat'])) {
            $dataUGD['telaahObat'] = $this->telaahObat;
        }

        $this->dataDaftarUgd = $dataUGD;
        return ($dataUGD);
    }

    public string $activeTabRacikanNonRacikan = "NonRacikan";
    public array $EmrMenuRacikanNonRacikan = [
        [
            'ermMenuId' => 'NonRacikan',
            'ermMenuName' => 'NonRacikan'
        ],
        [
            'ermMenuId' => 'Racikan',
            'ermMenuName' => 'Racikan'
        ],
    ];

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

    public function masukApotek($rjNo)
    {
        $this->findData($rjNo);

        $masukApotek = Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s');

        //////updateDB/////////////////////
        $sql = "select waktu_masuk_apt from rstxn_ugdhdrs where rj_no=:rjNo";
        $cek_waktu_masuk_apt = DB::scalar($sql, ['rjNo' => $rjNo]);


        // ketika cek_waktu_masuk_apt kosong lalu update
        if (!$cek_waktu_masuk_apt) {
            DB::table('rstxn_ugdhdrs')
                ->where('rj_no', $rjNo)
                ->update([
                    'waktu_masuk_apt' => DB::raw("to_date('" . $masukApotek . "','dd/mm/yyyy hh24:mi:ss')"), //waktu masuk = rjdate
                ]);
        }
        //////////////////////////////////

        // add antrian Apotek

        // update no antrian Apotek

        // cek
        // if (!$this->dataDaftarUgd['taskIdPelayanan']['taskId5']) {
        //      toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError( "Anda tidak dapat melakukan taskId6 ketika taskId5 Kosong");
        //     return;
        // }

        $noBooking =  $this->dataDaftarUgd['noBooking'];
        //////PushDataAntrianApotek////////////////////

        // cekNoantrian Apotek sudah ada atau belum
        if (!isset($this->dataDaftarUgd['noAntrianApotek'])) {
            $cekAntrianEresep = $this->findData($rjNo);
            $eresepRacikan = collect(isset($cekAntrianEresep['eresepRacikan']) ? $cekAntrianEresep['eresepRacikan'] : [])->count();
            $jenisResep = $eresepRacikan ? 'racikan' : 'non racikan';

            $query = DB::table('rstxn_ugdhdrs')
                ->select(
                    DB::raw("to_char(rj_date,'dd/mm/yyyy') AS rj_date"),
                    DB::raw("to_char(rj_date,'yyyymmdd') AS rj_date1"),
                    'datadaftarugd_json'
                )
                ->where('rj_status', '!=', ['F'])
                ->where('klaim_id', '!=', 'KR')
                ->where(DB::raw("to_char(rj_date,'dd/mm/yyyy')"), '=', $this->myTopBar['refDate'])
                ->get();

            $nomerAntrian = $query->filter(function ($item) {
                try {
                    $datadaftarugd_json = json_decode($item->datadaftarugd_json, true);
                } catch (\Exception $e) {
                    $datadaftarugd_json = [];
                }

                $noAntrianApotek = isset($datadaftarugd_json['noAntrianApotek']) ? 1 : 0;
                if ($noAntrianApotek > 0) {
                    return 'x';
                }
            })->count();


            // Antrian ketika data antrian kosong
            // proses antrian
            if ($this->dataDaftarUgd['klaimId'] != 'KR') {
                $noAntrian = $nomerAntrian + 1;
            } else {
                // Kronis
                $noAntrian = 999;
            }
            $this->dataDaftarUgd['noAntrianApotek'] = [
                'noAntrian' => $noAntrian,
                'jenisResep' => $jenisResep
            ];

            $this->updateJsonUGD($rjNo, $this->dataDaftarUgd);
        }
        // cekNoantrian Apotek sudah ada atau belum

        // update taskId6
        if (!$this->dataDaftarUgd['taskIdPelayanan']['taskId6']) {
            $this->dataDaftarUgd['taskIdPelayanan']['taskId6'] = $masukApotek;
            // update DB
            $this->updateJsonUGD($rjNo, $this->dataDaftarUgd);

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("masuk Apotek " . $this->dataDaftarUgd['taskIdPelayanan']['taskId6']);
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("masuk Apotek " . $this->dataDaftarUgd['taskIdPelayanan']['taskId6']);
        }
    }

    public function keluarApotek($rjNo)
    {
        $this->findData($rjNo);
        $keluarApotek = Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s');

        //////updateDB/////////////////////
        $sql = "select waktu_selesai_pelayanan from rstxn_ugdhdrs where rj_no=:rjNo";
        $cek_waktu_selesai_pelayanan = DB::scalar($sql, ['rjNo' => $rjNo]);


        // ketika cek_waktu_selesai_pelayanan kosong lalu update
        if (!$cek_waktu_selesai_pelayanan) {
            DB::table('rstxn_ugdhdrs')
                ->where('rj_no', $rjNo)
                ->update([
                    'waktu_selesai_pelayanan' => DB::raw("to_date('" . $keluarApotek . "','dd/mm/yyyy hh24:mi:ss')"), //waktu masuk = rjdate
                ]);
        }
        //////////////////////////////////
        // add antrian Apotek
        // update no antrian Apotek
        // cek
        if (!$this->dataDaftarUgd['taskIdPelayanan']['taskId6']) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Anda tidak dapat melakukan taskId7 ketika taskId6 Kosong");
            return;
        }

        // update taskId7
        if (!$this->dataDaftarUgd['taskIdPelayanan']['taskId7']) {
            $this->dataDaftarUgd['taskIdPelayanan']['taskId7'] = $keluarApotek;
            // update DB
            $this->updateJsonUGD($rjNo, $this->dataDaftarUgd);

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("keluar Apotek " . $this->dataDaftarUgd['taskIdPelayanan']['taskId7']);
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("keluar Apotek " . $this->dataDaftarUgd['taskIdPelayanan']['taskId7']);
        }
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
        $query = DB::table('rsview_ugdkasir')
            ->select(
                DB::raw("to_char(rj_date,'dd/mm/yyyy hh24:mi:ss') AS rj_date"),
                DB::raw("to_char(rj_date,'yyyymmddhh24miss') AS rj_date1"),
                'rj_no',
                'reg_no',
                'reg_name',
                'sex',
                'address',
                DB::raw("
            CASE
                WHEN birth_date IS NOT NULL THEN
                    trunc(months_between(sysdate, birth_date) / 12) || ' Thn, ' ||
                    trunc(mod(months_between(sysdate, birth_date), 12)) || ' Bln, ' ||
                    trunc(sysdate - add_months(birth_date, trunc(months_between(sysdate, birth_date)))) || ' Hr'
                ELSE
                    '0 Thn, 0 Bln, 0 Hr'
            END AS thn
        "),
                DB::raw("to_char(birth_date,'dd/mm/yyyy') AS birth_date"),
                'poli_id',
                // 'poli_desc',
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
                'datadaftarugd_json',
                DB::raw("(select count(*) from lbtxn_checkuphdrs where status_rjri='UGD' and checkup_status!='B' and ref_no = rsview_ugdkasir.rj_no) AS lab_status"),
                DB::raw("(select count(*) from rstxn_ugdrads where rj_no = rsview_ugdkasir.rj_no) AS rad_status")
            )
            ->where(DB::raw("nvl(rj_status,'A')"), '=', $myRefstatusId)
            // ->where('rj_status', '!=', 'F')
            // ->where('klaim_id', '!=', 'KR')

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
            // ->orderBy('rj_date1',  'desc')
            // ->orderBy('rj_no',  'desc')
        ;

        // 1 urutkan berdasarkan json table
        $myQueryPagination = $query->get()
            ->sortByDesc(
                function ($mySortByJson) {
                    $datadaftar_json = json_decode($mySortByJson->datadaftarugd_json, true);
                    $myQueryAntrianFarmasi = isset($datadaftar_json['noAntrianApotek']['noAntrian']) ? $datadaftar_json['noAntrianApotek']['noAntrian'] : 0;
                    $myQueryPagination = isset($datadaftar_json['eresep']) ? 1 : 0;
                    $myQueryPagination1 = isset($datadaftar_json['AdministrasiRj']) ? 1 : 0;
                    return ($myQueryAntrianFarmasi . $myQueryPagination . $myQueryPagination1 . $mySortByJson->rj_date1);
                }
            )->sortBy(
                function ($mySortByJson) {
                    $datadaftar_json = json_decode($mySortByJson->datadaftarugd_json, true);
                    $myQueryAntrianFarmasi = isset($datadaftar_json['noAntrianApotek']['noAntrian']) ? $datadaftar_json['noAntrianApotek']['noAntrian'] : 9999;
                    return ($myQueryAntrianFarmasi);
                }
            );


        $myQueryPagination = $this->paginate($myQueryPagination, $this->limitPerPage);


        ////////////////////////////////////////////////
        // end Query
        ///////////////////////////////////////////////



        return view(
            'livewire.emr-u-g-d.telaah-resep-u-g-d.telaah-resep-u-g-d',
            // ['myQueryData' => $query->paginate($this->limitPerPage)],
            ['myQueryData' => $myQueryPagination],


        );
    }
    // select data end////////////////


}
