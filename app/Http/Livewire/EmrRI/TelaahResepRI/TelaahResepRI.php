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

    // primitive Variable
    public string $myTitle = 'Resep RI';
    public string $mySnipt = 'Rekam Medis Pasien';
    public string $myProgram = 'Pasien RI';

    public array $myLimitPerPages = [5, 10, 15, 20, 100];
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

    public int $eresepRacikan;
    public int $eresep;

    public string $regNoRef;




    private function openModalEditTelaahResep($riHdrNo, $regNoRef): void
    {
        $this->isOpenTelaahResep = true;
        $this->isOpenModeTelaahResep = 'update';
        $this->dataDaftarRi = $riHdrNo;
        $this->regNoRef = $regNoRef;
    }


    public function closeModalTelaahResep(): void
    {
        $this->isOpenTelaahResep = false;
        $this->isOpenModeTelaahResep = 'insert';
        $this->resetInputFields();
    }

    public function editTelaahResep($eresep, $riHdrNo, $regNoRef)
    {
        if (!$eresep) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('E-Resep Tidak ditemukan');
        } else {
            $this->openModalEditTelaahResep($riHdrNo, $regNoRef);
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


    public function setttdTelaahResep($riHdrNo)
    {
        $myUserNameActive = auth()->user()->myuser_name;
        if (auth()->user()->hasRole('Apoteker')) {
            if (isset($this->dataDaftarRi['telaahResep']['penanggungJawab']) == false) {
                $this->dataDaftarRi['telaahResep']['penanggungJawab'] = [
                    'userLog' => auth()->user()->myuser_name,
                    'userLogDate' => Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s'),
                    'userLogCode' => auth()->user()->myuser_code
                ];

                // DB::table('imtxn_slshdrs')
                //     ->where('sls_no', $riHdrNo)
                //     ->update([
                //         'datadaftarri_json' => json_encode($this->dataDaftarRi, true),
                //         'datadaftarUgd_xml' => ArrayToXml::convert($this->dataDaftarRi),
                //     ]);

                $this->updateJsonRI($riHdrNo, $this->dataDaftarRi);


                $this->emit('syncronizeAssessmentDokterRIFindData');
                $this->emit('syncronizeAssessmentPerawatRIFindData');
            }
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Anda tidak dapat melakukan TTD-E karena User Role " . $myUserNameActive . ' Bukan Apoteker.');
            return;
        }
    }

    public function setttdTelaahObat($riHdrNo)
    {
        $myUserNameActive = auth()->user()->myuser_name;
        if (auth()->user()->hasRole('Apoteker')) {
            if (isset($this->dataDaftarRi['telaahObat']['penanggungJawab']) == false) {
                $this->dataDaftarRi['telaahObat']['penanggungJawab'] = [
                    'userLog' => auth()->user()->myuser_name,
                    'userLogDate' => Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s'),
                    'userLogCode' => auth()->user()->myuser_code
                ];

                // DB::table('imtxn_slshdrs')
                //     ->where('sls_no', $riHdrNo)
                //     ->update([
                //         'datadaftarri_json' => json_encode($this->dataDaftarRi, true),
                //         'datadaftarUgd_xml' => ArrayToXml::convert($this->dataDaftarRi),
                //     ]);

                $this->updateJsonRI($riHdrNo, $this->dataDaftarRi);


                $this->emit('syncronizeAssessmentDokterRIFindData');
                $this->emit('syncronizeAssessmentPerawatRIFindData');
            }
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Anda tidak dapat melakukan TTD-E karena User Role " . $myUserNameActive . ' Bukan Apoteker.');
            return;
        }
    }

    private function findData($riHdrNo): array
    {

        $this->dataDaftarRi = $this->findDataRI($riHdrNo);
        $dataRI = $this->dataDaftarRi;

        if (!isset($dataRI['telaahResep'])) {
            $dataRI['telaahResep'] = $this->telaahResep;
        }

        if (!isset($dataRI['telaahObat'])) {
            $dataRI['telaahObat'] = $this->telaahObat;
        }

        $this->dataDaftarRi = $dataRI;
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

    public function masukApotek($riHdrNo)
    {
        $this->findData($riHdrNo);
        $masukApotek = Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s');

        //////updateDB/////////////////////
        $sql = "select waktu_masuk_apt from imtxn_slshdrs where sls_no=:riHdrNo";
        $cek_waktu_masuk_apt = DB::scalar($sql, ['riHdrNo' => $riHdrNo]);


        // ketika cek_waktu_masuk_apt kosong lalu update
        if (!$cek_waktu_masuk_apt) {
            DB::table('imtxn_slshdrs')
                ->where('sls_no', $riHdrNo)
                ->update([
                    'waktu_masuk_apt' => DB::raw("to_date('" . $masukApotek . "','dd/mm/yyyy hh24:mi:ss')"), //waktu masuk = rjdate
                ]);
        }
        //////////////////////////////////

        // add antrian Apotek

        // update no antrian Apotek

        // cek
        // if (!$this->dataDaftarRi['taskIdPelayanan']['taskId5']) {
        //      toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError( "Anda tidak dapat melakukan taskId6 ketika taskId5 Kosong");
        //     return;
        // }

        $noBooking =  $this->dataDaftarRi['noBooking'];
        //////PushDataAntrianApotek////////////////////

        // cekNoantrian Apotek sudah ada atau belum
        if (!isset($this->dataDaftarRi['noAntrianApotek'])) {
            $cekAntrianEresep = $this->findData($riHdrNo);
            $eresepRacikan = collect(isset($cekAntrianEresep['eresepRacikan']) ? $cekAntrianEresep['eresepRacikan'] : [])->count();
            $jenisResep = $eresepRacikan ? 'racikan' : 'non racikan';

            $query = DB::table('imtxn_slshdrs')
                ->select(
                    DB::raw("to_char(sls_date,'dd/mm/yyyy') AS sls_date"),
                    DB::raw("to_char(sls_date,'yyyymmdd') AS sls_date1"),
                    'datadaftarri_json'
                )
                ->where('status', '!=', ['F'])
                ->where('klaim_id', '!=', 'KR')
                ->where(DB::raw("to_char(sls_date,'dd/mm/yyyy')"), '=', $this->myTopBar['refDate'])
                ->get();

            $nomerAntrian = $query->filter(function ($item) {
                try {
                    $datadaftarri_json = json_decode($item->datadaftarri_json, true);
                } catch (\Exception $e) {
                    $datadaftarri_json = [];
                }

                $noAntrianApotek = isset($datadaftarri_json['noAntrianApotek']) ? 1 : 0;
                if ($noAntrianApotek > 0) {
                    return 'x';
                }
            })->count();


            // Antrian ketika data antrian kosong
            // proses antrian
            if ($this->dataDaftarRi['klaimId'] != 'KR') {
                $noAntrian = $nomerAntrian + 1;
            } else {
                // Kronis
                $noAntrian = 999;
            }
            $this->dataDaftarRi['noAntrianApotek'] = [
                'noAntrian' => $noAntrian,
                'jenisResep' => $jenisResep
            ];

            $this->updateJsonRI($riHdrNo, $this->dataDaftarRi);
        }
        // cekNoantrian Apotek sudah ada atau belum

        // update taskId6
        if (!$this->dataDaftarRi['taskIdPelayanan']['taskId6']) {
            $this->dataDaftarRi['taskIdPelayanan']['taskId6'] = $masukApotek;
            // update DB
            $this->updateJsonRI($riHdrNo, $this->dataDaftarRi);

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("masuk Apotek " . $this->dataDaftarRi['taskIdPelayanan']['taskId6']);
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("masuk Apotek " . $this->dataDaftarRi['taskIdPelayanan']['taskId6']);
        }
    }

    public function keluarApotek($riHdrNo)
    {
        $this->findData($riHdrNo);
        $keluarApotek = Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s');

        //////updateDB/////////////////////
        $sql = "select waktu_selesai_pelayanan from imtxn_slshdrs where sls_no=:riHdrNo";
        $cek_waktu_selesai_pelayanan = DB::scalar($sql, ['riHdrNo' => $riHdrNo]);


        // ketika cek_waktu_selesai_pelayanan kosong lalu update
        if (!$cek_waktu_selesai_pelayanan) {
            DB::table('imtxn_slshdrs')
                ->where('sls_no', $riHdrNo)
                ->update([
                    'waktu_selesai_pelayanan' => DB::raw("to_date('" . $keluarApotek . "','dd/mm/yyyy hh24:mi:ss')"), //waktu masuk = rjdate
                ]);
        }
        //////////////////////////////////
        // add antrian Apotek
        // update no antrian Apotek
        // cek
        if (!$this->dataDaftarRi['taskIdPelayanan']['taskId6']) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Anda tidak dapat melakukan taskId7 ketika taskId6 Kosong");
            return;
        }

        // update taskId7
        if (!$this->dataDaftarRi['taskIdPelayanan']['taskId7']) {
            $this->dataDaftarRi['taskIdPelayanan']['taskId7'] = $keluarApotek;
            // update DB
            $this->updateJsonRI($riHdrNo, $this->dataDaftarRi);

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("keluar Apotek " . $this->dataDaftarRi['taskIdPelayanan']['taskId7']);
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("keluar Apotek " . $this->dataDaftarRi['taskIdPelayanan']['taskId7']);
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
        $query = DB::table('imview_slshdrs as s')
            ->join('rsview_rihdrs as r', 'r.rihdr_no', '=', 's.rihdr_no')
            ->select(
                // ambil dari S (tetap pakai nama aslinya)
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
                DB::raw("to_char(s.sls_date,'dd/mm/yyyy hh24:mi:ss') as sls_date"),
                DB::raw("to_char(s.sls_date,'yyyymmddhh24miss') as sls_date1"),
                DB::raw("to_char(s.birth_date,'dd/mm/yyyy') as birth_date"),

                // dari R: alias supaya tidak nabrak kolom S
                'r.ri_status',
                'r.room_id',
                'r.room_name',
                'r.bangsal_id',
                'r.bangsal_name',
                'r.bed_no',
                DB::raw('r.reg_no as r_reg_no'),
                DB::raw('r.reg_name as r_reg_name'),
                DB::raw("to_char(r.birth_date,'dd/mm/yyyy') as r_birth_date"),
                DB::raw('r.vno_sep as r_vno_sep'),
                DB::raw('r.klaim_id as r_klaim_id'),
                DB::raw('r.datadaftarri_json as r_datadaftarri_json'),
                DB::raw('r.thn as r_thn'),

                // counter penunjang
                DB::raw("(select count(*) from lbtxn_checkuphdrs l
                  where l.status_rjri='RI' and l.checkup_status!='B'
                    and l.ref_no = r.rihdr_no) as lab_status"),
                DB::raw("(select count(*) from rstxn_riradiologs rr
                  where rr.rihdr_no = r.rihdr_no) as rad_status")
            )

            ->where(DB::raw("nvl(s.status,'A')"), '=', $myRefstatusId)
            ->where(DB::raw("to_char(s.sls_date,'dd/mm/yyyy')"), '=', $myRefdate);


        // Jika where dokter tidak kosong
        if ($myRefdrId != 'All') {
            $query->where('s.dr_id', $myRefdrId);
        }

        $query->where(function ($q) use ($mySearch) {
            $q->Where(DB::raw('upper(s.reg_name)'), 'like', '%' . strtoupper($mySearch) . '%')
                ->orWhere(DB::raw('upper(s.reg_no)'), 'like', '%' . strtoupper($mySearch) . '%')
                ->orWhere(DB::raw('upper(s.dr_name)'), 'like', '%' . strtoupper($mySearch) . '%');
        });

        // 1 urutkan berdasarkan json table
        $myQueryPagination = $query->get()
            ->sortByDesc(
                function ($mySortByJson) {
                    $datadaftar_json = json_decode($mySortByJson->datadaftarri_json, true);
                    $myQueryAntrianFarmasi = isset($datadaftar_json['noAntrianApotek']['noAntrian']) ? $datadaftar_json['noAntrianApotek']['noAntrian'] : 0;
                    $myQueryPagination = isset($datadaftar_json['eresep']) ? 1 : 0;
                    $myQueryPagination1 = isset($datadaftar_json['AdministrasiRj']) ? 1 : 0;
                    return ($myQueryAntrianFarmasi . $myQueryPagination . $myQueryPagination1 . $mySortByJson->sls_date1);
                }
            )->sortBy(
                function ($mySortByJson) {
                    $datadaftar_json = json_decode($mySortByJson->datadaftarri_json, true);
                    $myQueryAntrianFarmasi = isset($datadaftar_json['noAntrianApotek']['noAntrian']) ? $datadaftar_json['noAntrianApotek']['noAntrian'] : 9999;
                    return ($myQueryAntrianFarmasi);
                }
            );


        $myQueryPagination = $this->paginate($myQueryPagination, $this->limitPerPage);


        ////////////////////////////////////////////////
        // end Query
        ///////////////////////////////////////////////



        return view(
            'livewire.emr-r-i.telaah-resep-r-i.telaah-resep-r-i',
            // ['myQueryData' => $query->paginate($this->limitPerPage)],
            ['myQueryData' => $myQueryPagination],


        );
    }
    // select data end////////////////


}
