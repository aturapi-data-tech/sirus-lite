<?php

namespace App\Http\Livewire\EmrRI\TelaahResepRI;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;

use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

use Carbon\Carbon;
use App\Http\Traits\EmrRI\EmrRITrait;


class TelaahResepRI extends Component
{
    use WithPagination, EmrRITrait;

    protected $listeners = [];
    public string $myTitle = 'Resep RI';
    public string $mySnipt = 'Rekam Medis Pasien';
    public string $myProgram = 'Pasien RI';

    public array $myLimitPerPages = [5, 10, 15, 20, 100];    // limit record per page -resetExcept////////////////
    // limit record per page -resetExcept////////////////
    public int $limitPerPage = 10;

    public array $dataDaftarRi = [];

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
    public function updatedRefFilter()
    {
        $this->resetPage();
    }

    public function updatedMyTopBarRefDate()
    {
        $this->resetPage();
    }

    public function updatedMyTopBarRefStatusId()
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
        $query = DB::table('imview_slshdrs')
            ->select(
                'dr_id',
                'dr_name',
            )
            ->where(DB::raw("to_char(sls_date,'dd/mm/yyyy')"), '=', $myRefdate)
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


    public bool $isOpenTelaahResep = false;
    public string $isOpenModeTelaahResep = 'insert';



    public string $regNoRef;





    public ?string $riHdrNoRefForEdit = null;
    public ?int    $resepHeaderIndexForEdit = null;
    private function openModalEditTelaahResep(string $riHdrNo, int $headerIndex, string $regNoRef): void
    {
        $this->isOpenTelaahResep        = true;
        $this->isOpenModeTelaahResep    = 'update';
        $this->riHdrNoRefForEdit        = $riHdrNo;
        $this->resepHeaderIndexForEdit  = $headerIndex;
        $this->regNoRef                 = $regNoRef;
    }


    public function closeModalTelaahResep(): void
    {
        $this->isOpenTelaahResep = false;
        $this->isOpenModeTelaahResep = 'insert';
        $this->resetInputFields();
    }

    public function editTelaahResep($hasEresep, $slsNo, $riHdrNo): void
    {
        // validasi minimal
        if (!(int)$hasEresep) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('E-Resep tidak ditemukan pada header ini.');
            return;
        }

        // muat RI, cari header by SLS
        $this->dataDaftarRi = $this->findDataRI($riHdrNo);
        $idx = $this->findHeaderIndexBySlsNo($riHdrNo, (int)$slsNo);
        if ($idx === null) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Header resep untuk slsNo tersebut tidak ditemukan.');
            return;
        }

        // siapkan struktur telaahResep (DI DALAM HEADER) bila belum ada
        $this->dataDaftarRi['eresepHdr'][$idx]['telaahResep'] = ($this->dataDaftarRi['eresepHdr'][$idx]['telaahResep'] ?? []) ?: $this->telaahResep;
        $this->dataDaftarRi['eresepHdr'][$idx]['telaahObat'] = ($this->dataDaftarRi['eresepHdr'][$idx]['telaahObat'] ?? []) ?: $this->telaahObat;


        // buka modal + set pointer header yang sedang diedit
        $regNoRef = $this->dataDaftarRi['regNo'] ?? '-';
        $this->openModalEditTelaahResep($riHdrNo, $idx, $regNoRef);
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


    public function setttdTelaahResep(string $riHdrNo, ?int $idx = null): void
    {
        $user = auth()->user();

        // 1) Guard role
        if (!$user->hasAnyRole(['Apoteker', 'Admin'])) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError("Anda tidak dapat melakukan TTD-E karena {$user->myuser_name} bukan Apoteker/Admin.");
            return;
        }


        // 5) Idempotent: jika sudah TTD, jangan overwrite
        if (!empty($this->dataDaftarRi['eresepHdr'][$idx]['telaahResep']['penanggungJawab'] ?? null)) {
            $oleh = $this->dataDaftarRi['eresepHdr'][$idx]['telaahResep']['penanggungJawab']['userLog'] ?? '-';
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addInfo("Telaah Resep sudah TTD oleh {$oleh}.");
            return;
        }

        // 6) Set TTD di HEADER
        $this->dataDaftarRi['eresepHdr'][$idx]['telaahResep']['penanggungJawab'] = [
            'userLog'     => $user->myuser_name,
            'userLogCode' => $user->myuser_code,
            'userLogDate' => Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s'),
        ];

        // 7) Simpan & sync
        $this->updateJsonRI($riHdrNo, $this->dataDaftarRi);
        $this->emit('syncronizeAssessmentDokterRIFindData');
        $this->emit('syncronizeAssessmentPerawatRIFindData');

        $resepNoInfo = $this->dataDaftarRi['eresepHdr'][$idx]['resepNo'] ?? '-';
        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
            ->addSuccess("TTD Telaah Resep berhasil untuk Resep {$resepNoInfo}.");
    }

    public function setttdTelaahObat(string $riHdrNo, ?int $idx = null): void
    {
        $user = auth()->user();
        if (!$user->hasAnyRole(['Apoteker', 'Admin'])) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError("Anda tidak dapat melakukan TTD-E karena User Role {$user->myuser_name} Bukan Apoteker.");
            return;
        }


        // jika sudah TTD, jangan overwrite
        if (!empty($this->dataDaftarRi['eresepHdr'][$idx]['telaahObat']['penanggungJawab'] ?? null)) {
            $oleh = $this->dataDaftarRi['eresepHdr'][$idx]['telaahObat']['penanggungJawab']['userLog'] ?? '-';
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addInfo("Telaah Obat sudah TTD oleh {$oleh}.");
            return;
        }

        // set TTD di HEADER
        $this->dataDaftarRi['eresepHdr'][$idx]['telaahObat']['penanggungJawab'] = [
            'userLog'     => $user->myuser_name,
            'userLogCode' => $user->myuser_code,
            'userLogDate' => Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s'),
        ];

        // simpan & sync
        $this->updateJsonRI($riHdrNo, $this->dataDaftarRi);
        $this->emit('syncronizeAssessmentDokterRIFindData');
        $this->emit('syncronizeAssessmentPerawatRIFindData');

        $resepNoInfo = $this->dataDaftarRi['eresepHdr'][$idx]['resepNo'] ?? '-';
        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
            ->addSuccess("TTD Telaah Obat berhasil untuk Resep {$resepNoInfo}.");
    }


    private function findData($riHdrNo): array
    {

        $this->dataDaftarRi = $this->findDataRI($riHdrNo);
        $dataRI = $this->dataDaftarRi;
        return ($dataRI);
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

    private function findHeaderIndexBySlsNo(string $riHdrNo, int $slsNo): ?int
    {
        $ri = $this->findDataRI($riHdrNo);
        $list = $ri['eresepHdr'] ?? [];
        foreach ($list as $i => $h) {
            if (!empty($h['slsNo']) && (int)$h['slsNo'] === (int)$slsNo) return $i;
        }
        return null;
    }





    // fallback ambil slsNo dari header terbaru yang sudah TTD/terkirim
    private function findSlsNoFromRiJson(string $riHdrNo, ?int $resepNo = null): ?int
    {
        $ri = $this->findDataRI($riHdrNo);
        $headers = collect($ri['eresepHdr'] ?? []);
        if ($resepNo !== null) {
            $row = $headers->firstWhere('resepNo', $resepNo);
            return $row['slsNo'] ?? null;
        }
        $row = $headers->sortByDesc('resepNo')->first(fn($h) => !empty($h['slsNo'] ?? null));
        return $row['slsNo'] ?? null;
    }

    public function masukApotek($slsNo, $riHdrNo)
    {
        // refresh data RI
        $this->findData($riHdrNo);

        // pastikan punya slsNo (bisa dari parameter atau JSON)
        $slsNo = $slsNo ?: $this->findSlsNoFromRiJson($riHdrNo);
        if (!$slsNo) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('slsNo tidak ditemukan. Pastikan resep sudah TTD & terkirim ke apotek.');
            return;
        }

        // temukan index header resep berdasarkan slsNo
        $ri  = $this->dataDaftarRi;
        $idx = $this->findHeaderIndexBySlsNo($riHdrNo, (int)$slsNo);
        if ($idx === null) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Header resep untuk slsNo tersebut tidak ditemukan di JSON RI.');
            return;
        }

        $now = Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s');

        // isi waktu_masuk_pelayanan di DB jika kosong
        $waktuMasuk = DB::scalar(
            "select waktu_masuk_pelayanan from imtxn_slshdrs where sls_no=:slsNo",
            ['slsNo' => $slsNo]
        );
        if (!$waktuMasuk) {
            DB::table('imtxn_slshdrs')
                ->where('sls_no', $slsNo)
                ->update(['waktu_masuk_pelayanan' => DB::raw("to_date('{$now}','dd/mm/yyyy hh24:mi:ss')")]);
        }

        // jenis resep (ada racikan?)
        $adaRacikan = collect($ri['eresepHdr'][$idx]['eresepRacikan'] ?? [])->count() > 0;
        $jenisResep = $adaRacikan ? 'racikan' : 'non racikan';

        // tentukan no antrian resmi (di SLS)
        $klaimId = $ri['klaimId'] ?? '';
        if ($klaimId === 'KR') {
            $noAntrian = 999;
        } else {
            $myRefdate     = $this->myTopBar['refDate'];
            // ambil max no_antrian untuk hari ini (exclude 999)
            $max = DB::table('imtxn_slshdrs as t')
                ->whereRaw("to_char(t.sls_date,'dd/mm/yyyy') = ?", [$myRefdate]) // 'dd/mm/yyyy'
                ->whereNotNull('t.no_antrian')
                ->where('t.no_antrian', '!=', 999)
                ->max('t.no_antrian');

            $noAntrian = (int)($max ?? 0) + 1;
        }

        // simpan ke SLS (sumber kebenaran no antrian)

        $existingNoAntrian = (int) DB::table('imtxn_slshdrs')
            ->where('sls_no', $slsNo)
            ->value('no_antrian');

        if ($existingNoAntrian) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError("Sudah memiliki no antrian (Resep {$ri['eresepHdr'][$idx]['resepNo']}) • No Antrian {$existingNoAntrian}");
            return;
        }
        DB::table('imtxn_slshdrs')
            ->where('sls_no', $slsNo)
            ->update(['no_antrian' => $noAntrian]);

        // mirror ke JSON header RI
        $ri['eresepHdr'][$idx]['noAntrianApotek'] = [
            'noAntrian'  => $noAntrian,
            'jenisResep' => $jenisResep,
        ];
        $ri['eresepHdr'][$idx]['taskIdPelayanan'] = $ri['eresepHdr'][$idx]['taskIdPelayanan'] ?? [];

        // set taskId6 (masuk) kalau belum ada
        if (empty($ri['eresepHdr'][$idx]['taskIdPelayanan']['taskId6'])) {
            $ri['eresepHdr'][$idx]['taskIdPelayanan']['taskId6'] = $now;
            $this->dataDaftarRi = $ri;
            $this->updateJsonRI($riHdrNo, $this->dataDaftarRi);

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addSuccess("Masuk Apotek (Resep {$ri['eresepHdr'][$idx]['resepNo']}) • No Antrian {$noAntrian}");
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError("Masuk Apotek sudah tercatat: " . $ri['eresepHdr'][$idx]['taskIdPelayanan']['taskId6']);
        }
    }



    public function keluarApotek($slsNo, $riHdrNo)
    {
        $this->findData($riHdrNo);

        $slsNo = $slsNo ?: $this->findSlsNoFromRiJson($riHdrNo);
        if (!$slsNo) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('slsNo tidak ditemukan.');
            return;
        }

        $ri = $this->dataDaftarRi;
        $idx = $this->findHeaderIndexBySlsNo($riHdrNo, (int)$slsNo);
        if ($idx === null) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Header resep untuk slsNo tersebut tidak ditemukan di JSON RI.');
            return;
        }

        $ri['eresepHdr'][$idx]['taskIdPelayanan'] = $ri['eresepHdr'][$idx]['taskIdPelayanan'] ?? [];
        if (empty($ri['eresepHdr'][$idx]['taskIdPelayanan']['taskId6'])) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError("Tidak bisa taskId7 karena taskId6 (masuk) header resep ini belum diisi.");
            return;
        }

        $now = Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s');

        // isi waktu_selesai_pelayanan di DB jika kosong
        $waktuSelesai = DB::scalar("select waktu_selesai_pelayanan from imtxn_slshdrs where sls_no=:slsNo", ['slsNo' => $slsNo]);
        if (!$waktuSelesai) {
            DB::table('imtxn_slshdrs')
                ->where('sls_no', $slsNo)
                ->update(['waktu_selesai_pelayanan' => DB::raw("to_date('{$now}','dd/mm/yyyy hh24:mi:ss')")]);
        }

        // set taskId7 (keluar)
        if (empty($ri['eresepHdr'][$idx]['taskIdPelayanan']['taskId7'])) {
            $ri['eresepHdr'][$idx]['taskIdPelayanan']['taskId7'] = $now;
            $this->dataDaftarRi = $ri;
            $this->updateJsonRI($riHdrNo, $this->dataDaftarRi);
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addSuccess("Keluar Apotek (Resep {$ri['eresepHdr'][$idx]['resepNo']}) pada {$now}");
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError("Keluar Apotek sudah tercatat: " . $ri['eresepHdr'][$idx]['taskIdPelayanan']['taskId7']);
        }
    }


    // select data start////////////////
    public function render()
    {
        $this->gettermyTopBardrOptions();

        $mySearch      = $this->refFilter;
        $myRefdate     = $this->myTopBar['refDate'];
        $myRefstatusId = $this->myTopBar['refStatusId'];
        $myRefdrId     = $this->myTopBar['drId'];

        $query = DB::table('imview_slshdrs as s')
            ->join('rsview_rihdrs as r', 'r.rihdr_no', '=', 's.rihdr_no')
            ->select(
                // SLS
                's.sls_no',
                's.reg_no',
                's.reg_name',
                's.dr_id',
                's.dr_name',
                's.emp_id',
                's.emp_name',
                's.status',
                's.shift',
                's.klaim_id',
                's.rihdr_no',
                's.acc_id',
                's.vno_sep',
                's.datadaftarri_json',
                's.sex',
                's.address',
                's.thn',
                's.no_antrian', // <<— ambil nomor antrian resmi
                DB::raw("to_char(s.waktu_masuk_pelayanan,'dd/mm/yyyy hh24:mi:ss') as waktu_masuk_pelayanan"),
                DB::raw("to_char(s.waktu_selesai_pelayanan,'dd/mm/yyyy hh24:mi:ss') as waktu_selesai_pelayanan"),
                DB::raw("to_char(s.sls_date,'dd/mm/yyyy hh24:mi:ss')  as sls_date"),
                DB::raw("to_char(s.sls_date,'yyyymmddhh24miss')       as sls_date1"),
                DB::raw("to_char(s.birth_date,'dd/mm/yyyy')           as birth_date"),

                // RIHDR
                'r.ri_status',
                'r.room_id',
                'r.room_name',
                'r.bangsal_id',
                'r.bangsal_name',
                'r.bed_no',
                DB::raw('r.reg_no                           as r_reg_no'),
                DB::raw('r.reg_name                         as r_reg_name'),
                DB::raw("to_char(r.birth_date,'dd/mm/yyyy') as r_birth_date"),
                DB::raw('r.vno_sep                          as r_vno_sep'),
                DB::raw('r.klaim_id                         as r_klaim_id'),
                DB::raw('r.datadaftarri_json                as r_datadaftarri_json'),
                DB::raw('r.thn                              as r_thn'),

                // Penunjang
                DB::raw("(select count(*) from lbtxn_checkuphdrs l
                          where l.status_rjri='RI' and l.checkup_status!='B'
                            and l.ref_no = r.rihdr_no) as lab_status"),
                DB::raw("(select count(*) from rstxn_riradiologs rr
                          where rr.rihdr_no = r.rihdr_no) as rad_status")
            )
            ->where(DB::raw("nvl(s.status,'A')"), '=', $myRefstatusId)
            ->where(DB::raw("to_char(s.sls_date,'dd/mm/yyyy')"), '=', $myRefdate);

        if ($myRefdrId != 'All') {
            $query->where('s.dr_id', $myRefdrId);
        }

        $query->where(function ($q) use ($mySearch) {
            $q->where(DB::raw('upper(s.reg_name)'), 'like', '%' . strtoupper($mySearch) . '%')
                ->orWhere(DB::raw('upper(s.reg_no)'),  'like', '%' . strtoupper($mySearch) . '%')
                ->orWhere(DB::raw('upper(s.dr_name)'), 'like', '%' . strtoupper($mySearch) . '%');
        });

        $rows = $query->get();

        // ===== Urutan khusus RI (pakai SLS.no_antrian, fallback JSON header) =====
        $sorted = $rows->sortBy(function ($row) {
            $json = [];
            try {
                $json = json_decode($row->datadaftarri_json, true) ?: [];
            } catch (\Throwable $e) {
            }
            $headers = $json['eresepHdr'] ?? [];

            // Sumber nomor antrian: SLS > mirror header (MIN)
            $slsQueue = is_numeric($row->no_antrian ?? null) ? (int)$row->no_antrian : null;
            $headerMinQ = collect($headers)
                ->map(fn($h) => $h['noAntrianApotek']['noAntrian'] ?? null)
                ->filter(fn($n) => is_numeric($n) && (int)$n > 0) // 0 bukan nomor valid
                ->map(fn($n) => (int)$n)
                ->min();
            $effectiveQ = $slsQueue ?? $headerMinQ;

            // Flag & urutan antrian
            $hasQueue = ($effectiveQ !== null) && (($effectiveQ > 0) || ($effectiveQ === 999));
            $isKronis = ($effectiveQ === 999);
            $queueOrder = $isKronis ? PHP_INT_MAX : (int)($effectiveQ ?? PHP_INT_MAX); // non-KR ASC, KR di belakang

            // eResep?
            $hasAnyEresep = collect($headers)->contains(
                fn($h) => !empty($h['eresep']) || !empty($h['eresepRacikan'])
            );

            // taskId6 paling awal (juga dipakai sebagai tie-breaker utk nomor sama)
            $task6Times = collect($headers)
                ->map(fn($h) => $h['taskIdPelayanan']['taskId6'] ?? null)
                ->filter(fn($t) => is_string($t) && trim($t) !== '')
                ->map(function ($t) {
                    $t  = str_replace('/', '-', $t);
                    $ts = strtotime($t);
                    return $ts ?: PHP_INT_MAX;
                });
            $minTask6 = $task6Times->isNotEmpty() ? $task6Times->min() : PHP_INT_MAX;

            // sls_date terbaru (pakai nilai negatif supaya ASC → DESC)
            $sls1 = is_numeric($row->sls_date1 ?? null) ? (int)$row->sls_date1 : 0;

            return [
                $hasQueue ? 0 : 1,                   // 0 = punya antrian dulu
                $hasQueue ? ($isKronis ? 1 : 0) : 0, // queued: non-KR (0) sebelum KR (1)
                $hasQueue ? $queueOrder : PHP_INT_MAX, // queued: nomor ASC
                $hasQueue ? 0 : ($hasAnyEresep ? 0 : 1), // belum queued: sudah eResep dulu
                $hasQueue ? $minTask6 : $minTask6,      // tie-breaker: yang task6 lebih awal dulu
                -$sls1,                                  // terakhir: sls_date terbaru
            ];
        });

        $myQueryPagination = $this->paginate($sorted, $this->limitPerPage);

        return view(
            'livewire.emr-r-i.telaah-resep-r-i.telaah-resep-r-i',
            ['myQueryData' => $myQueryPagination],
        );
    }

    // select data end////////////////


}
