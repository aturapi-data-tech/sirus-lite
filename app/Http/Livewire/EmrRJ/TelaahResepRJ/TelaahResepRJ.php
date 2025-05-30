<?php

namespace App\Http\Livewire\EmrRJ\TelaahResepRJ;

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

class TelaahResepRJ extends Component
{
    use WithPagination, EmrRJTrait;

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

    public array $dataDaftarPoliRJ = [];

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
        $myUserNameActive = auth()->user()->myuser_name;
        if (auth()->user()->hasRole('Apoteker')) {
            if (isset($this->dataDaftarPoliRJ['telaahResep']['penanggungJawab']) == false) {
                $this->dataDaftarPoliRJ['telaahResep']['penanggungJawab'] = [
                    'userLog' => auth()->user()->myuser_name,
                    'userLogDate' => Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s'),
                    'userLogCode' => auth()->user()->myuser_code
                ];

                // if ($rjNo !== $this->dataDaftarPoliRJ['rjNo']) {
                //     dd('Data Json Tidak sesuai' . $rjNo . '  /  ' . $this->dataDaftarPoliRJ['rjNo']);
                // }

                // DB::table('rstxn_rjhdrs')
                //     ->where('rj_no', $rjNo)
                //     ->update([
                //         'datadaftarpolirj_json' => json_encode($this->dataDaftarPoliRJ, true),
                //         'datadaftarpolirj_xml' => ArrayToXml::convert($this->dataDaftarPoliRJ),
                //     ]);
                $this->updateJsonRJ($rjNo, $this->dataDaftarPoliRJ);


                $this->emit('syncronizeAssessmentDokterRJFindData');
                $this->emit('syncronizeAssessmentPerawatRJFindData');
            }
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Anda tidak dapat melakukan TTD-E karena User Role " . $myUserNameActive . ' Bukan Apoteker.');
            return;
        }
    }

    public function setttdTelaahObat($rjNo)
    {
        $myUserNameActive = auth()->user()->myuser_name;
        if (auth()->user()->hasRole('Apoteker')) {
            if (isset($this->dataDaftarPoliRJ['telaahObat']['penanggungJawab']) == false) {
                $this->dataDaftarPoliRJ['telaahObat']['penanggungJawab'] = [
                    'userLog' => auth()->user()->myuser_name,
                    'userLogDate' => Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s'),
                    'userLogCode' => auth()->user()->myuser_code
                ];

                // if ($rjNo !== $this->dataDaftarPoliRJ['rjNo']) {
                //     dd('Data Json Tidak sesuai' . $rjNo . '  /  ' . $this->dataDaftarPoliRJ['rjNo']);
                // }

                // DB::table('rstxn_rjhdrs')
                //     ->where('rj_no', $rjNo)
                //     ->update([
                //         'datadaftarpolirj_json' => json_encode($this->dataDaftarPoliRJ, true),
                //         'datadaftarpolirj_xml' => ArrayToXml::convert($this->dataDaftarPoliRJ),
                //     ]);
                $this->updateJsonRJ($rjNo, $this->dataDaftarPoliRJ);


                $this->emit('syncronizeAssessmentDokterRJFindData');
                $this->emit('syncronizeAssessmentPerawatRJFindData');
            }
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Anda tidak dapat melakukan TTD-E karena User Role " . $myUserNameActive . ' Bukan Apoteker.');
            return;
        }
    }

    private function findData($rjNo): array
    {

        $findDataRJ = $this->findDataRJ($rjNo);
        $dataRawatJalan  = $findDataRJ['dataDaftarRJ'];

        $rsAdmin = DB::table('rstxn_rjhdrs')
            ->select('rs_admin', 'rj_admin', 'poli_price', 'klaim_id', 'pass_status')
            ->where('rj_no', $rjNo)
            ->first();

        $rsObat = DB::table('rstxn_rjobats')
            ->join('immst_products', 'immst_products.product_id', 'rstxn_rjobats.product_id')
            ->select('rstxn_rjobats.product_id as product_id', 'product_name', 'qty', 'price', 'rjobat_dtl')
            ->where('rj_no', $rjNo)
            ->get();

        $rsLab = DB::table('rstxn_rjlabs')
            ->select('lab_desc', 'lab_price', 'lab_dtl')
            ->where('rj_no', $rjNo)
            ->get();

        $rsRad = DB::table('rstxn_rjrads')
            ->join('rsmst_radiologis', 'rsmst_radiologis.rad_id', 'rstxn_rjrads.rad_id')
            ->select('rad_desc', 'rstxn_rjrads.rad_price as rad_price', 'rad_dtl')
            ->where('rj_no', $rjNo)
            ->get();


        // RJ Admin
        if ($rsAdmin->pass_status == 'N') {
            $rsAdminParameter = DB::table('rsmst_parameters')
                ->select('par_value')
                ->where('par_id', '1')
                ->first();
            if (isset($dataRawatJalan['rjAdmin'])) {
                $dataRawatJalan['rjAdmin'] = $rsAdmin->rj_admin;
            } else {
                $dataRawatJalan['rjAdmin'] = $rsAdminParameter->par_value;
                // update table trnsaksi
                DB::table('rstxn_rjhdrs')
                    ->where('rj_no', $rjNo)
                    ->update([
                        'rj_admin' => $dataRawatJalan['rjAdmin'],
                    ]);
            }
        } else {
            $dataRawatJalan['rjAdmin'] = 0;
        }

        // RS Admin
        $rsAdminDokter = DB::table('rsmst_doctors')
            ->select('rs_admin', 'poli_price', 'poli_price_bpjs')
            ->where('dr_id', $dataRawatJalan['drId'])
            ->first();


        if (isset($dataRawatJalan['rsAdmin'])) {
            $dataRawatJalan['rsAdmin'] = $rsAdmin->rs_admin ? $rsAdmin->rs_admin : 0;
        } else {
            $dataRawatJalan['rsAdmin'] = $rsAdminDokter->rs_admin ? $rsAdminDokter->rs_admin : 0;
            // update table trnsaksi
            DB::table('rstxn_rjhdrs')
                ->where('rj_no', $rjNo)
                ->update([
                    'rs_admin' => $dataRawatJalan['rsAdmin'],
                ]);
        }

        // PoliPrice
        // 1) Ambil klaim status (default 'UMUM' jika NULL)
        $klaimStatus = DB::table('rsmst_klaimtypes')
            ->where('klaim_id', $dataRawatJalan['klaimId'] ?? '')
            ->value('klaim_status') ?? 'UMUM';

        // 2) Tentukan harga dokter berdasarkan status klaim
        if ($klaimStatus === 'BPJS') {
            // gunakan tarif BPJS untuk poli
            $dokterPoliPrice = $rsAdminDokter->poli_price_bpjs ?? 0;
        } else {
            // gunakan tarif umum untuk poli
            $dokterPoliPrice = $rsAdminDokter->poli_price ?? 0;
        }

        // 3) Set poliPrice & simpan ke transaksi Rawat Jalan
        if (isset($dataRawatJalan['poliPrice'])) {
            // sudah ada → pakai harga admin/front-office
            $dataRawatJalan['poliPrice'] = $rsAdmin->poli_price ? $rsAdmin->poli_price : 0;
        } else {
            // belum ada → pakai tarif dokter sesuai klaim
            $dataRawatJalan['poliPrice'] = $dokterPoliPrice;

            // update kolom poli_price pada header transaksi RJ
            DB::table('rstxn_rjhdrs')
                ->where('rj_no', $rjNo)
                ->update([
                    'poli_price' => $dataRawatJalan['poliPrice'],
                ]);
        }


        // Ketika Kronis
        if ($rsAdmin->klaim_id == 'KR') {
            $dataRawatJalan['rjAdmin'] = 0;
            $dataRawatJalan['rsAdmin'] = 0;
            $dataRawatJalan['poliPrice'] = 0;
            // update table trnsaksi
            DB::table('rstxn_rjhdrs')
                ->where('rj_no', $rjNo)
                ->update([
                    'rj_admin' => $dataRawatJalan['rjAdmin'],
                    'rs_admin' => $dataRawatJalan['rsAdmin'],
                    'poli_price' => $dataRawatJalan['poliPrice'],
                ]);
        }



        $dataRawatJalan['rjObat'] = json_decode(json_encode($rsObat, true), true);
        $dataRawatJalan['rjLab'] = json_decode(json_encode($rsLab, true), true);
        $dataRawatJalan['rjRad'] = json_decode(json_encode($rsRad, true), true);


        if (!isset($dataRawatJalan['telaahResep'])) {
            $dataRawatJalan['telaahResep'] = $this->telaahResep;
        }

        if (!isset($dataRawatJalan['telaahObat'])) {
            $dataRawatJalan['telaahObat'] = $this->telaahObat;
        }

        $this->dataDaftarPoliRJ = $dataRawatJalan;
        return ($dataRawatJalan);
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

    public function masukApotek($rjNo)
    {
        // Ambil data terkait rjNo dan set waktu masuk apotek saat ini
        $this->findData($rjNo);

        // Pastikan TaskId5 (waktu keluar poli) sudah ada sebelum melanjutkan taskId6
        if (empty($this->dataDaftarPoliRJ['taskIdPelayanan']['taskId5'])) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError("Anda tidak dapat melakukan taskId6 ketika taskId5 Kosong");
            return;
        }

        $masukApotek = Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s');

        // Cek apakah kolom waktu_masuk_apt sudah terisi
        $cekWaktuMasukApt = DB::table('rstxn_rjhdrs')
            ->where('rj_no', $rjNo)
            ->value('waktu_masuk_apt');

        // Jika belum, update kolom tersebut menggunakan fungsi to_date
        if (!$cekWaktuMasukApt) {
            DB::table('rstxn_rjhdrs')
                ->where('rj_no', $rjNo)
                ->update([
                    'waktu_masuk_apt' => DB::raw("to_date('" . $masukApotek . "','dd/mm/yyyy hh24:mi:ss')")
                ]);
        }



        $noBooking = $this->dataDaftarPoliRJ['noBooking'];

        // Inisialisasi antrian Apotek jika belum ada
        if (empty($this->dataDaftarPoliRJ['noAntrianApotek'])) {
            // Mengambil ulang data untuk memastikan ketersediaan key 'eresepRacikan'
            $data = $this->findData($rjNo);
            $eresepRacikanCount = collect($data['eresepRacikan'] ?? [])->count();
            $jenisResep = ($eresepRacikanCount > 0) ? 'racikan' : 'non racikan';

            // Query untuk mengambil data hari ini berdasarkan refDate dari myTopBar
            $query = DB::table('rstxn_rjhdrs')
                ->select(
                    DB::raw("to_char(rj_date,'dd/mm/yyyy') AS rj_date"),
                    DB::raw("to_char(rj_date,'yyyymmdd') AS rj_date1"),
                    'datadaftarpolirj_json'
                )
                ->where('rj_status', '!=', 'F')
                ->where('klaim_id', '!=', 'KR')
                ->where(DB::raw("to_char(rj_date,'dd/mm/yyyy')"), '=', $this->myTopBar['refDate'])
                ->get();

            // Hitung jumlah data yang sudah memiliki noAntrianApotek
            $nomerAntrian = $query->filter(function ($item) {
                $dataJson = json_decode($item->datadaftarpolirj_json, true) ?: [];
                return isset($dataJson['noAntrianApotek']);
            })->count();

            // Jika klaim bukan 'KR', antrian bertambah; jika 'KR' maka tetap 999
            $noAntrian = ($this->dataDaftarPoliRJ['klaimId'] != 'KR') ? $nomerAntrian + 1 : 999;

            // Simpan nilai antrian ke dataDaftarPoliRJ dan update ke DB
            $this->dataDaftarPoliRJ['noAntrianApotek'] = [
                'noAntrian'  => $noAntrian,
                'jenisResep' => $jenisResep
            ];
            $this->updateDataRJ($rjNo);
        }

        // Kirim antrean Apotek
        $cekPoliSpesialis = DB::table('rsmst_polis')
            ->where('spesialis_status', '1')
            ->where('poli_id', $this->dataDaftarPoliRJ['poliId'])
            ->exists();

        if ($cekPoliSpesialis) {
            $this->pushAntreanApotek(
                $noBooking,
                $this->dataDaftarPoliRJ['noAntrianApotek']['jenisResep'],
                $this->dataDaftarPoliRJ['noAntrianApotek']['noAntrian']
            );
        }

        // Update taskId6 (waktu masuk apotek) jika belum ada
        if (empty($this->dataDaftarPoliRJ['taskIdPelayanan']['taskId6'])) {
            $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId6'] = $masukApotek;
            $this->updateDataRJ($rjNo);
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addSuccess("Masuk Apotek " . $masukApotek);
        } else {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError("Masuk Apotek " . $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId6']);
        }

        // Jika taskId6 tersedia, konversi ke timestamp (milisecond) dan push ke BPJS
        if (!empty($this->dataDaftarPoliRJ['taskIdPelayanan']['taskId6'])) {
            $waktuTimestamp = Carbon::createFromFormat(
                'd/m/Y H:i:s',
                $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId6'],
                env('APP_TIMEZONE')
            )->timestamp * 1000;

            $cekPoliSpesialis = DB::table('rsmst_polis')
                ->where('spesialis_status', '1')
                ->where('poli_id', $this->dataDaftarPoliRJ['poliId'])
                ->exists();

            if ($cekPoliSpesialis) {
                $this->pushDataTaskId($noBooking, 6, $waktuTimestamp);
            }
        } else {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError("Waktu Masuk Apotek kosong, tidak dapat dikirim");
        }
    }

    public function keluarApotek($rjNo)
    {
        // Ambil data terkait rjNo dan waktu keluar apotek saat ini
        $this->findData($rjNo);

        // Pastikan taskId6 (waktu masuk Apotek) sudah tercatat sebelum melanjutkan ke taskId7
        if (empty($this->dataDaftarPoliRJ['taskIdPelayanan']['taskId6'])) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError("Anda tidak dapat melakukan taskId7 ketika taskId6 Kosong");
            return;
        }

        $keluarApotek = Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s');

        // Cek apakah kolom waktu_selesai_pelayanan sudah terisi
        $waktuSelesaiPelayanan = DB::table('rstxn_rjhdrs')
            ->where('rj_no', $rjNo)
            ->value('waktu_selesai_pelayanan');

        // Jika belum ada, update kolom tersebut menggunakan fungsi to_date
        if (!$waktuSelesaiPelayanan) {
            DB::table('rstxn_rjhdrs')
                ->where('rj_no', $rjNo)
                ->update([
                    'waktu_selesai_pelayanan' => DB::raw("to_date('" . $keluarApotek . "','dd/mm/yyyy hh24:mi:ss')")
                ]);
        }



        // Update taskId7 jika belum ada
        if (empty($this->dataDaftarPoliRJ['taskIdPelayanan']['taskId7'])) {
            $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId7'] = $keluarApotek;
            $this->updateDataRJ($rjNo);
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addSuccess("Keluar Apotek " . $keluarApotek);
        } else {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError("Keluar Apotek " . $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId7']);
        }

        // Ambil noBooking
        $noBooking = $this->dataDaftarPoliRJ['noBooking'];

        // Jika taskId7 tersedia, konversi ke timestamp (milisecond) dan push data
        if (!empty($this->dataDaftarPoliRJ['taskIdPelayanan']['taskId7'])) {
            $waktuTimestamp = Carbon::createFromFormat(
                'd/m/Y H:i:s',
                $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId7'],
                env('APP_TIMEZONE')
            )->timestamp * 1000;

            // Kirim antrean Apotek
            $cekPoliSpesialis = DB::table('rsmst_polis')
                ->where('spesialis_status', '1')
                ->where('poli_id', $this->dataDaftarPoliRJ['poliId'])
                ->exists();

            if ($cekPoliSpesialis) {
                $this->pushDataTaskId($noBooking, 7, $waktuTimestamp);
            }
        } else {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError("Waktu Keluar Apotek kosong, tidak dapat dikirim");
        }
    }


    private function pushAntreanApotek($noBooking, $jenisResep, $nomerAntrean): void
    {
        $HttpGetBpjs =  AntrianTrait::tambah_antrean_farmasi($noBooking, $jenisResep, $nomerAntrean, "")->getOriginalContent();

        if ($HttpGetBpjs['metadata']['code'] == 200) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('NoBooking' . $noBooking . ' ' . $HttpGetBpjs['metadata']['code'] . ' ' . $HttpGetBpjs['metadata']['message']);
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('NoBooking' . $noBooking . ' ' .  $HttpGetBpjs['metadata']['code'] . ' ' . $HttpGetBpjs['metadata']['message']);
        }
    }

    private function pushDataTaskId($noBooking, $taskId, $time): void
    {
        //////////////////////////////////////////////////////////
        /////////////////////////////////////////////////////////
        // Update Task Id $kodebooking, $taskid, $waktu, $jenisresep

        $waktu = $time;
        $HttpGetBpjs =  AntrianTrait::update_antrean($noBooking, $taskId, $waktu, "")->getOriginalContent();

        // set http response to public
        // $this->HttpGetBpjsStatus = $HttpGetBpjs['metadata']['code']; //status 200 201 400 ..
        // $this->HttpGetBpjsJson = json_encode($HttpGetBpjs, true); //Return Response Tambah Antrean

        // metadata d kecil
        if ($HttpGetBpjs['metadata']['code'] == 200) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('Task Id' . $taskId . ' ' . $HttpGetBpjs['metadata']['code'] . ' ' . $HttpGetBpjs['metadata']['message']);
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Task Id' . $taskId . ' ' .  $HttpGetBpjs['metadata']['code'] . ' ' . $HttpGetBpjs['metadata']['message']);

            // Ulangi Proses pushTaskId;
            // $this->emit('rePush_Data_TaskId_Confirmation');
        }
    }

    private function updateDataRJ($rjNo): void
    {

        // if ($rjNo !== $this->dataDaftarPoliRJ['rjNo']) {
        //     dd('Data Json Tidak sesuai' . $rjNo . '  /  ' . $this->dataDaftarPoliRJ['rjNo']);
        // }

        // // update table trnsaksi
        // DB::table('rstxn_rjhdrs')
        //     ->where('rj_no', $rjNo)
        //     ->update([
        //         'datadaftarpolirj_json' => json_encode($this->dataDaftarPoliRJ, true),
        //         'datadaftarpolirj_xml' => ArrayToXml::convert($this->dataDaftarPoliRJ),
        //     ]);
        $this->updateJsonRJ($rjNo, $this->dataDaftarPoliRJ);
        $this->emit('syncronizeAssessmentDokterRJFindData');
        $this->emit('syncronizeAssessmentPerawatRJFindData');
        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Data Telaah Resep berhasil disimpan.");
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
                'datadaftarpolirj_json',
                DB::raw("(select count(*) from lbtxn_checkuphdrs where status_rjri='RJ' and checkup_status!='B' and ref_no = rsview_rjkasir.rj_no) AS lab_status"),
                DB::raw("(select count(*) from rstxn_rjrads where rj_no = rsview_rjkasir.rj_no) AS rad_status")
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
            // ->orderBy('rj_date1',  'desc')
            // ->orderBy('rj_no',  'desc')
        ;

        // 1 urutkan berdasarkan json table
        $myQueryPagination = $query->get();

        // Sort ketiga: datadaftarpolirj_json (desc)
        $myQueryPagination = $myQueryPagination->sortByDesc(
            function ($mySortByJson) {
                // Decode JSON payload
                $jsonData = json_decode($mySortByJson->datadaftarpolirj_json, true);

                // 1) Ambil nomor antrian apotek (0 jika tidak ada)
                $pharmacyQueueNumber = $jsonData['noAntrianApotek']['noAntrian'] ?? 0;

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
                    $hasPharmacyQueue ? $pharmacyQueueNumber : 0, // nilai antrian (DESC)
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
            'livewire.emr-r-j.telaah-resep-r-j.telaah-resep-r-j',
            // ['myQueryData' => $query->paginate($this->limitPerPage)],
            ['myQueryData' => $myQueryPagination],


        );
    }
    // select data end////////////////


}
