<?php

namespace App\Http\Livewire\DaftarRJ;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class DaftarRJ extends Component
{
    use WithPagination;




    //  table data////////////////
    // variable data pasien dan rawat jalan
    //  table data//////////////// 
    public $dataPasien = [
        "pasien" => [
            "cariDataPasien" => "",
            "pasientidakdikenal" => [],  //status pasien tdak dikenal 0 false 1 true
            "regNo" => "", //harus diisi
            "gelarDepan" => "",
            "regName" => "", //harus diisi / (Sesuai KTP)
            "gelarBelakang" => "",
            "namaPanggilan" => "",
            "tempatLahir" => "", //harus diisi
            "tglLahir" => "", //harus diisi / (dd/mm/yyyy)
            "thn" => "",
            "bln" => "",
            "hari" => "",
            "jenisKelamin" => [ //harus diisi (saveid)
                "jenisKelaminId" => "",
                "jenisKelaminDesc" => "",

            ],
            "agama" => [ //harus diisi (save id+nama)
                "agamaId" => "",
                "agamaDesc" => "",

            ],
            "statusPerkawinan" => [ //harus diisi (save id)
                "statusPerkawinanId" => "",
                "statusPerkawinanDesc" => "",
            ],


            "pendidikan" =>  [ //harus diisi (save id)
                "pendidikanId" => "",
                "pendidikanDesc" => "",

            ],

            "pekerjaan" => [ //harus diisi (save id)
                "pekerjaanId" => "",
                "pekerjaanDesc" => "",
            ],


            "golonganDarah" => [ //harus diisi (save id+nama) (default Tidak Tahu)
                "golonganDarahId" => "",
                "golonganDarahDesc" => "",
            ],

            "kewarganegaraan" => '', //Free text (defult INDONESIA)
            "suku" => '', //Free text (defult Jawa)
            "bahasa" => '', //Free text (defult Indonesia / Jawa)
            "status" => [
                "statusId" => "",
                "statusDesc" => "",

            ],
            "identitas" => [
                "nik" => "", //harus diisi
                "idbpjs" => "",
                "pasport" => "", //untuk WNA / WNI yang memiliki passport
                "alamat" => "", //harus diisi
                "rt" => "", //harus diisi
                "rw" => "", //harus diisi
                "kodepos" => "", //harus diisi
                "desaId" => "", //harus diisi (Kode data Kemendagri)
                "kecamatanId" => "", //harus diisi (Kode data Kemendagri)
                "kotaId" => "", //harus diisi (Kode data Kemendagri)
                "propinsiId" => "", //harus diisi (Kode data Kemendagri)
                "desaName" => "", //harus diisi (Kode data Kemendagri)
                "kecamatanName" => "", //harus diisi (Kode data Kemendagri)
                "kotaName" => "", //harus diisi (Kode data Kemendagri)
                "propinsiName" => "", //harus diisi (Kode data Kemendagri)
                "negara" => "" //harus diisi (ISO 3166) ID 	IDN 	360 	ISO 3166-2:ID 	.id
            ],
            "kontak" => [
                "kodenegara" => "", //+(62) Indonesia 
                "nomerTelponSelulerPasien" => "", //+(kode negara) no telp
                "nomerTelponLain" => "" //+(kode negara) no telp
            ],

            ///////////
            //transaksi
            ///////////
            'rawatJalan' =>
            [
                "noAntrian" => "",
                "rjDate" => "",
                "jenisKlaim" => [ //Jenis klaim ada 4 UMUM / BPJS / Asuransi Lain / Kronis
                    "jenisKlaimId" => "",
                    "jenisKlaimDesc" => ""
                ],
                "shift" => [
                    'shiftId' => '1',
                    'shiftDesc' => '1',
                ],
                "PasienBaru" => "", //O Old N New
                "dokter" => [
                    "dokterId" => "",
                    "dokterDesc" => ""
                ],
                "poli" => [
                    "poliId" => "",
                    "poliDesc" => ""
                ],
                "bpjs" => ""
            ]

        ],

    ];

    public $ruleDataPasien =
    [
        'pasien' => [
            'regNo' => false,
            'gelarDepan' => false,
            'regName' => false,
            'gelarBelakang' => false,
            'namaPanggilan' => false,
            'tempatLahir' => false,
            'tglLahir' => false,
            'thn' => false,
            'bln' => false,
            'hari' => false,

            'jenisKelamin' => [
                'jenisKelaminId' => false,
                'jenisKelaminDesc' => false,
            ],
            'agama' => [
                'agamaId' => false,
                'agamaagamaDesc' => false,
            ],

            'statusPerkawinan' => [
                'statusPerkawinanId' => false,
                'statusPerkawinanDesc' => false,
            ],

            'pendidikan' => [
                'pendidikanId' => false,
                'pendidikanDesc' => false,
            ],

            'pekerjaan' => [
                'pekerjaanId' => false,
                'pekerjaanDesc' => false,
            ],

            'golonganDarah' => [
                'golonganDarahId' => false,
                'golonganDarahDesc' => false,
            ],

            'kewarganegaraan' => false,
            'suku' => false,
            'bahasa' => false,
            'status' => [
                'statusId' => false,
                'statusDesc' => false,
            ],

            'domisil' => [
                'alamat' => false,
                'rt' => false,
                'rw' => false,
                'kodepos' => false,
                'desaId' => false,
                'kecamatanId' => false,
                'kotaId' => false,
                'propinsiId' => false,
                'desaName' => false,
                'kecamatanName' => false,
                'kotaName' => false,
                'propinsiName' => false,
            ],

            'identitas' => [
                'nik' => false,
                'idbpjs' => false,
                'pasport' => false,
                'alamat' => false,
                'rt' => false,
                'rw' => false,
                'kodepos' => false,
                'desaId' => false,
                'kecamatanId' => false,
                'kotaId' => false,
                'propinsiId' => false,
                'desaName' => false,
                'kecamatanName' => false,
                'kotaName' => false,
                'propinsiName' => false,
                'negara' => false,
            ],

            'kontak' => [
                'kodenegara' => false,
                'nomerTelponSelulerPasien' => false,
                'nomerTelponLain' => false,
            ],

            'hubungan' => [
                'namaAyah' => false,
                'kodenegaraAyah' => false,
                'nomerTelponSelulerAyah' => false,

                'namaIbu' => false,
                'kodenegaraIbu' => false,
                'nomerTelponSelulerIbu' => false,

                'namaPenanggungJawab' => false,
                'kodenegaraPenanggungJawab' => false,
                'nomerTelponSelulerPenanggungJawab' => false,
                'hubunganDgnPasien' => [
                    'hubunganDgnPasienId' => false,
                    'hubunganDgnPasienDesc' => false,
                ]
            ]
        ]
    ];

    //////////////////////////////
    // Ref on top bar
    //////////////////////////////
    public $dateRjRef = '';

    public $shiftRjRef = [
        'shiftId' => '1',
        'shiftDesc' => '1',
        'shiftOptions' => [
            ['shiftId' => '1', 'shiftDesc' => '1'],
            ['shiftId' => '2', 'shiftDesc' => '2'],
            ['shiftId' => '3', 'shiftDesc' => '3'],
        ]
    ];

    public $statusRjRef = [
        'statusId' => 'A',
        'statusDesc' => 'Antrian',
        'statusOptions' => [
            ['statusId' => 'A', 'statusDesc' => 'Antrian'],
            ['statusId' => 'L', 'statusDesc' => 'Selesai'],
            ['statusId' => 'I', 'statusDesc' => 'Transfer'],
        ]
    ];

    public $JenisKlaim = [
        'JenisKlaimId' => 'UM',
        'JenisKlaimDesc' => 'UMUM',
        'JenisKlaimOptions' => [
            ['JenisKlaimId' => 'UM', 'JenisKlaimDesc' => 'UMUM'],
            ['JenisKlaimId' => 'JM', 'JenisKlaimDesc' => 'BPJS'],
            ['JenisKlaimId' => 'JML', 'JenisKlaimDesc' => 'Asuransi Lain'],
            ['JenisKlaimId' => 'KR', 'JenisKlaimDesc' => 'Kronis'],

        ]
    ];

    public $JenisKunjungan = [
        'JenisKunjunganId' => '1',
        'JenisKunjunganDesc' => 'Rujukan FKTP',
        'JenisKunjunganOptions' => [
            ['JenisKunjunganId' => '1', 'JenisKunjunganDesc' => 'Rujukan FKTP'],
            ['JenisKunjunganId' => '2', 'JenisKunjunganDesc' => 'Rujukan Internal'],
            ['JenisKunjunganId' => '3', 'JenisKunjunganDesc' => 'Kontrol'],
            ['JenisKunjunganId' => '4', 'JenisKunjunganDesc' => 'Rujukan Antar RS'],

        ]
    ];
    //////////////////////////////



    //////////////////////////////////////////////////////////////////////





    // limit record per page -resetExcept////////////////
    public $limitPerPage = 10;


    //  table LOV////////////////
    public $cariDataPasienLov = [];
    public $cariDataPasienLovStatus = 0;
    public $cariDataPasienLovSearch = '';

    // 

    //  modal status////////////////
    public $isOpen = 0;
    public $isOpenMode = 'insert';

    // call MasterPasien Form
    public $callMasterPasien = 0;



    // search logic -resetExcept////////////////
    public $search;
    protected $queryString = [
        'search' => ['except' => '', 'as' => 'cariData'],
        'page' => ['except' => 1, 'as' => 'p'],
    ];


    // sort logic -resetExcept////////////////
    public $sortField = 'reg_no';
    public $sortAsc = true;


    // listener from blade////////////////
    protected $listeners = [
        'confirm_remove_record_province' => 'delete',
    ];




    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////





    // resert input private////////////////
    private function resetInputFields(): void
    {

        // resert validation
        $this->resetValidation();
        // resert input
        $this->resetExcept([
            'limitPerPage',
            'search',

        ]);
    }




    // open and close modal start////////////////
    private function openModal(): void
    {
        $this->resetInputFields();
        $this->isOpen = true;
        $this->isOpenMode = 'insert';
        $this->setShiftnCurrentDate();
    }
    private function openModalEdit(): void
    {
        $this->resetInputFields();
        $this->isOpen = true;
        $this->isOpenMode = 'update';
    }

    private function openModalTampil(): void
    {
        $this->resetInputFields();
        $this->isOpen = true;
        $this->isOpenMode = 'tampil';
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


    // setShift////////////////
    public function setShift($id, $desc): void
    {
        $this->shiftRjRef['shiftId'] = $id;
        $this->shiftRjRef['shiftDesc'] = $desc;
        $this->resetValidation();
    }




    // resert page pagination when coloumn search change ////////////////
    public function updatedSearch(): void
    {
        $this->resetPage();
        $this->resetValidation();
    }





    // is going to insert data////////////////
    public function create()
    {
        $this->openModal();
    }


    // logic LOV start////////////////
    // jenis kelamin LOV

    // /////////cariDataPasien////////////
    public function clickcariDataPasienlov()
    {
        $this->cariDataPasienLovStatus = true;
        $this->cariDataPasienLov = $this->dataPasien['pasien']['cariDataPasien']['cariDataPasienOptions'];
    }
    public function updatedcariDataPasienlovsearch()
    {
        // Variable Search
        $search = $this->cariDataPasienLovSearch;

        // check LOV by id 
        $cariDataPasien = collect($this->dataPasien['pasien']['cariDataPasien']['cariDataPasienOptions'])
            ->where('cariDataPasienId', '=', $search)
            ->first();

        if ($cariDataPasien) {
            $this->dataPasien['pasien']['cariDataPasien']['cariDataPasienId'] = $cariDataPasien['cariDataPasienId'];
            $this->dataPasien['pasien']['cariDataPasien']['cariDataPasienDesc'] = $cariDataPasien['cariDataPasienDesc'];
            $this->cariDataPasienLovStatus = false;
            $this->cariDataPasienLovSearch = '';
        } else {
            // if there is no id found and check (min 3 char on search)
            if (strlen($search) < 3) {
                $this->cariDataPasienLov = $this->dataPasien['pasien']['cariDataPasien']['cariDataPasienOptions'];
            } else {
                $this->cariDataPasienLov = collect($this->dataPasien['pasien']['cariDataPasien']['cariDataPasienOptions'])
                    ->filter(function ($item) use ($search) {
                        return false !== stristr($item['cariDataPasienDesc'], $search);
                    });
            }
            $this->cariDataPasienLovStatus = true;
            $this->dataPasien['pasien']['cariDataPasien']['cariDataPasienId'] = '';
            $this->dataPasien['pasien']['cariDataPasien']['cariDataPasienDesc'] = '';
        }
    }
    // /////////////////////
    // LOV selected start
    public function setMycariDataPasienLov($id, $name)
    {
        $this->dataPasien['pasien']['cariDataPasien']['cariDataPasienId'] = $id;
        $this->dataPasien['pasien']['cariDataPasien']['cariDataPasienDesc'] = $name;
        $this->cariDataPasienLovStatus = false;
        $this->cariDataPasienLovSearch = '';
    }
    // LOV selected end
    // /////////////////////

    // logic LOV start////////////////
    // jenis kelamin LOV




    ////////////////////////////logic lain/////////////////////////////////////

    ///Daftar Pasien RJ////////////////////////////////////////////
    ////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////
    private function cariDataPasienKey($key, $search)
    {
        $cariDataPasienKey = json_decode(DB::table('rsmst_pasiens')
            ->select(
                DB::raw("to_char(reg_date,'dd/mm/yyyy hh24:mi:ss') as reg_date"),
                'reg_no',
                'reg_name',
                DB::raw("nvl(nokartu_bpjs,'-') as nokartu_bpjs"),
                DB::raw("nvl(nik_bpjs,'-') as nik_bpjs"),
                'sex',
                DB::raw("to_char(birth_date,'dd/mm/yyyy') as birth_date"),
                DB::raw("(select trunc( months_between( sysdate, birth_date ) /12 ) from dual) as thn"),
                'bln',
                'hari',
                'birth_place',
                'blood',
                'marital_status',
                'rel_id',
                'edu_id',
                'job_id',
                'kk',
                'nyonya',
                'no_kk',
                'address',
                'rsmst_desas.des_id  as des_id',
                'rsmst_kecamatans.kec_id  as kec_id',
                'rsmst_kabupatens.kab_id  as kab_id',
                'rsmst_propinsis.prop_id  as prop_id',
                'des_name  as des_name',
                'kec_name  as kec_name',
                'kab_name  as kab_name',
                'prop_name  as prop_name',
                'rt',
                'rw',
                'phone'
            )
            ->join('rsmst_desas', 'rsmst_desas.des_id', 'rsmst_pasiens.des_id')
            ->join('rsmst_kecamatans', 'rsmst_kecamatans.kec_id', 'rsmst_desas.kec_id')
            ->join('rsmst_kabupatens', 'rsmst_kabupatens.kab_id', 'rsmst_kecamatans.kab_id')
            ->join('rsmst_propinsis', 'rsmst_propinsis.prop_id', 'rsmst_kabupatens.prop_id')
            ->where($key, $search)
            ->orderBy('reg_name', 'desc')
            ->get(), true);

        return  $cariDataPasienKey;
    }
    public function updatedDatapasienPasienCaridatapasien()
    {
        // set Call MasterPasien False
        $this->callMasterPasien = false;

        if ($this->cariDataPasienLovStatus != true) {
            $this->cariDataPasienLovStatus = true;
        }

        // 1.Cari berdasarkan nik ->if null DB
        // 2.Cari berdasarkan reg_no ->if null DB
        // 3.Cari berdasarkan reg_name ->if null DB
        // 4. Goto Pasien Baru berdasarkan nik apiBPJS ->if null 
        // 5. Entry Manual Pasien Baru

        // by reg_no
        $cariDataPasienRegNo = $this->cariDataPasienKey('reg_no', $this->dataPasien['pasien']['cariDataPasien']);
        if ($cariDataPasienRegNo) {
            $this->cariDataPasienLov = $cariDataPasienRegNo;
        } else {

            // by nik
            $cariDataPasienNik = $this->cariDataPasienKey('nik_bpjs', $this->dataPasien['pasien']['cariDataPasien']);
            if ($cariDataPasienNik) {
                $this->cariDataPasienLov = $cariDataPasienNik;
            } else {

                // by name
                $cariDataPasienName = json_decode(DB::table('rsmst_pasiens')
                    ->select(
                        DB::raw("to_char(reg_date,'dd/mm/yyyy hh24:mi:ss') as reg_date"),
                        'reg_no',
                        'reg_name',
                        DB::raw("nvl(nokartu_bpjs,'-') as nokartu_bpjs"),
                        DB::raw("nvl(nik_bpjs,'-') as nik_bpjs"),
                        'sex',
                        DB::raw("to_char(birth_date,'dd/mm/yyyy') as birth_date"),
                        DB::raw("(select trunc( months_between( sysdate, birth_date ) /12 ) from dual) as thn"),
                        'bln',
                        'hari',
                        'birth_place',
                        'blood',
                        'marital_status',
                        'rel_id',
                        'edu_id',
                        'job_id',
                        'kk',
                        'nyonya',
                        'no_kk',
                        'address',
                        'rsmst_desas.des_id  as des_id',
                        'rsmst_kecamatans.kec_id  as kec_id',
                        'rsmst_kabupatens.kab_id  as kab_id',
                        'rsmst_propinsis.prop_id  as prop_id',
                        'des_name  as des_name',
                        'kec_name  as kec_name',
                        'kab_name  as kab_name',
                        'prop_name  as prop_name',
                        'rt',
                        'rw',
                        'phone'
                    )
                    ->join('rsmst_desas', 'rsmst_desas.des_id', 'rsmst_pasiens.des_id')
                    ->join('rsmst_kecamatans', 'rsmst_kecamatans.kec_id', 'rsmst_desas.kec_id')
                    ->join('rsmst_kabupatens', 'rsmst_kabupatens.kab_id', 'rsmst_kecamatans.kab_id')
                    ->join('rsmst_propinsis', 'rsmst_propinsis.prop_id', 'rsmst_kabupatens.prop_id')
                    ->where(DB::raw('upper(reg_name)'), 'like', '%' . strtoupper($this->dataPasien['pasien']['cariDataPasien']) . '%')
                    ->orWhere('reg_no', 'like', '%' . strtoupper($this->dataPasien['pasien']['cariDataPasien']) . '%')
                    ->orderBy('reg_name', 'desc')
                    ->limit(50)
                    ->get(), true);
                if ($cariDataPasienName) {
                    $this->cariDataPasienLov = $cariDataPasienName;
                } else {
                    // If Confirmation
                    $this->emit('cari_Data_Pasien_Tidak_Ditemukan_Confirmation', $this->dataPasien['pasien']);
                    // $this->callMasterPasien = true;
                }
            }
        }
    }

    // resert input private////////////////
    /////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////
    private function setShiftnCurrentDate(): void
    {
        // dd/mm/yyyy hh24:mi:ss
        $this->dataPasien['pasien']['rawatJalan']['rjDate'] = Carbon::now()->format('d/m/Y H:i:s');

        // shift
        $findShift = DB::table('rstxn_shiftctls')->select('shift')
            ->whereRaw("'" . Carbon::now()->format('H:i:s') . "' between
            shift_start and shift_end")
            ->first();
        $this->dataPasien['pasien']['rawatJalan']['shift']['shiftId'] = $findShift->shift ? $findShift->shift : 3;
        $this->dataPasien['pasien']['rawatJalan']['shift']['shiftDesc'] = $findShift->shift ? $findShift->shift : 3;
    }

    // Cari Data Pasien Rawat Jalan ////////////////
    //////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////
    private function findData($value)
    {
        $findData = DB::table('rsview_rjkasir')
            ->select(
                'rj_no',
                'rj_date',
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
                'vno_sep'
            )
            ->where('rj_no', '=', $value)
            ->first();

        return $findData;
    }
    // Find data from table end////////////////














    // when new form instance
    public function mount()
    {
        $this->dateRjRef = Carbon::now()->format('d/m/Y');
    }


    // select data start////////////////
    public function render()
    {
        return view(
            'livewire.daftar-r-j.daftar-r-j',
            [
                'RJpasiens' => DB::table('rsview_rjkasir')
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
                        'vno_sep'
                    )
                    ->where('rj_status', '=', $this->statusRjRef['statusId'])
                    ->where('shift', '=', $this->shiftRjRef['shiftId'])
                    ->where(DB::raw("to_char(rj_date,'dd/mm/yyyy')"), '=', $this->dateRjRef)
                    ->where(function ($q) {
                        $q->Where('reg_name', 'like', '%' . $this->search . '%')
                            ->orWhere('reg_no', 'like', '%' . $this->search . '%');
                    })
                    ->orderBy('rj_date1',  'desc')
                    ->paginate($this->limitPerPage),
                'myTitle' => 'Data Pasien Rawat Jalan',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Pasien Rawat Jalan',
                'myLimitPerPages' => [5, 10, 15, 20, 100],
                'thisUrl' => url()->previous()
            ]
        );
    }
    // select data end////////////////
}
