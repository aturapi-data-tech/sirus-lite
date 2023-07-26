<?php

namespace App\Http\Livewire\DaftarRJ;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

use App\Http\Traits\customErrorMessagesTrait;
use App\Http\Traits\BPJS\AntrianTrait;
use App\Http\Traits\BPJS\VclaimTrait;


use Illuminate\Support\Str;
use Spatie\ArrayToXml\ArrayToXml;


class DaftarRJ extends Component
{
    use WithPagination;




    //  table data////////////////
    // variable data pasien dan rawat jalan
    //  table data//////////////// 
    public  $dataPasien = [
        "pasien" => [
            "pasientidakdikenal" => [],  //status pasien tdak dikenal 0 false 1 true
            "regNo" => '', //harus diisi
            "gelarDepan" => '',
            "regName" => '', //harus diisi / (Sesuai KTP)
            "gelarBelakang" => '',
            "namaPanggilan" => '',
            "tempatLahir" => '', //harus diisi
            "tglLahir" => '', //harus diisi / (dd/mm/yyyy)
            "thn" => '',
            "bln" => '',
            "hari" => '',
            "jenisKelamin" => [ //harus diisi (saveid)
                "jenisKelaminId" => 1,
                "jenisKelaminDesc" => "Laki-laki",

            ],
            "agama" => [ //harus diisi (save id+nama)
                "agamaId" => "1",
                "agamaDesc" => "Islam",

            ],
            "statusPerkawinan" => [ //harus diisi (save id)
                "statusPerkawinanId" => "1",
                "statusPerkawinanDesc" => "Belum Kawin",

            ],
            "pendidikan" =>  [ //harus diisi (save id)
                "pendidikanId" => "3",
                "pendidikanDesc" => "SLTA Sederajat",

            ],
            "pekerjaan" => [ //harus diisi (save id)
                "pekerjaanId" => "4",
                "pekerjaanDesc" => "Pegawai Swasta/ Wiraswasta",

            ],
            "golonganDarah" => [ //harus diisi (save id+nama) (default Tidak Tahu)
                "golonganDarahId" => "13",
                "golonganDarahDesc" => "Tidak Tahu",

            ],

            "kewarganegaraan" => 'INDONESIA', //Free text (defult INDONESIA)
            "suku" => 'Jawa', //Free text (defult Jawa)
            "bahasa" => 'Indonesia / Jawa', //Free text (defult Indonesia / Jawa)
            "status" => [
                "statusId" => "1",
                "statusDesc" => "Aktif / Hidup",

            ],
            "domisil" => [
                "samadgnidentitas" => [], //status samadgn domisil 0 false 1 true (auto entry = domisil)
                "alamat" => '', //harus diisi
                "rt" => '', //harus diisi
                "rw" => '', //harus diisi
                "kodepos" => '', //harus diisi
                "desaId" => '', //harus diisi (Kode data Kemendagri)
                "kecamatanId" => '', //harus diisi (Kode data Kemendagri)
                "kotaId" => "3504", //harus diisi (Kode data Kemendagri)
                "propinsiId" => "35", //harus diisi (Kode data Kemendagri)
                "desaName" => '', //harus diisi (Kode data Kemendagri)
                "kecamatanName" => '', //harus diisi (Kode data Kemendagri)
                "kotaName" => "TULUNGAGUNG", //harus diisi (Kode data Kemendagri)
                "propinsiName" => "JAWA TIMUR", //harus diisi (Kode data Kemendagri)

            ],
            "identitas" => [
                "nik" => '', //harus diisi
                "idbpjs" => '',
                "pasport" => '', //untuk WNA / WNI yang memiliki passport
                "alamat" => '', //harus diisi
                "rt" => '', //harus diisi
                "rw" => '', //harus diisi
                "kodepos" => '', //harus diisi
                "desaId" => '', //harus diisi (Kode data Kemendagri)
                "kecamatanId" => '', //harus diisi (Kode data Kemendagri)
                "kotaId" => "3504", //harus diisi (Kode data Kemendagri)
                "propinsiId" => "35", //harus diisi (Kode data Kemendagri)
                "desaName" => '', //harus diisi (Kode data Kemendagri)
                "kecamatanName" => '', //harus diisi (Kode data Kemendagri)
                "kotaName" => "TULUNGAGUNG", //harus diisi (Kode data Kemendagri)
                "propinsiName" => "JAWA TIMUR", //harus diisi (Kode data Kemendagri)
                "negara" => "ID" //harus diisi (ISO 3166) ID 	IDN 	360 	ISO 3166-2:ID 	.id
            ],
            "kontak" => [
                "kodenegara" => "62", //+(62) Indonesia 
                "nomerTelponSelulerPasien" => '', //+(kode negara) no telp
                "nomerTelponLain" => '' //+(kode negara) no telp
            ],
            "hubungan" => [
                "namaAyah" => '', //
                "kodenegaraAyah" => "62", //+(62) Indonesia 
                "nomerTelponSelulerAyah" => '', //+(kode negara) no telp
                "namaIbu" => '', //
                "kodenegaraIbu" => "62", //+(62) Indonesia 
                "nomerTelponSelulerIbu" => '', //+(kode negara) no telp

                "namaPenanggungJawab" => '', // di isi untuk pasien (Tidak dikenal / Hal Lain)
                "kodenegaraPenanggungJawab" => "62", //+(62) Indonesia 
                "nomerTelponSelulerPenanggungJawab" => '', //+(kode negara) no telp
                "hubunganDgnPasien" => [
                    "hubunganDgnPasienId" => 5, //Default 5 Kerabat / Saudara
                    "hubunganDgnPasienDesc" => "Kerabat / Saudara",

                ]
            ],

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
                'agamaDesc' => false,
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

    // Pendaftaran RJ
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

    public $dataDaftarPoliRJ = [
        "regNo" => '',

        "drId" => '',
        "drDesc" => '',

        "poliId" => '',
        "poliDesc" => '',

        "kddrbpjs" => '',
        "kdpolibpjs" => '',

        "rjDate" => '',
        "rjNo" => '',
        "shift" => '',
        "noAntrian" => '',
        "noBooking" => '',
        "slCodeFrom" => "02",
        "passStatus" => "",
        "rjStatus" => "A",
        "txnStatus" => "A",
        "ermStatus" => "A",
        "cekLab" => "0",
        "kunjunganInternalStatus" => "0",
        "noReferensi" => '',
        "taskIdPelayanan" => [
            "taskId1" => "",
            "taskId2" => "",
            "taskId3" => "",
            "taskId4" => "",
            "taskId5" => "",
            "taskId6" => "",
            "taskId7" => "",
            "taskId99" => "",
        ],
    ];

    // http status Push antrian BPJS
    public $HttpGetBpjsStatus; //status push antrian 200 /201/ 400
    public $HttpGetBpjsJson; // response json


    //////////////////////////////



    //////////////////////////////////////////////////////////////////////





    // limit record per page -resetExcept////////////////
    public $limitPerPage = 10;


    //  table LOV////////////////
    public $dataPasienLov = [];
    public $dataPasienLovStatus = 0;
    public $dataPasienLovSearch = '';

    public $dataDokterLov = [];
    public $dataDokterLovStatus = 0;
    public $dataDokterLovSearch = '';

    public $dataRujukanLov = [];
    public $dataRujukanLovStatus = 0;
    public $dataRujukanLovSearch = '';



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
        'rePush_Data_Antrian' => 'store',
        // 'rePush_Data_TaskId' => '', //blm di pakai ->model fitur blm diemukan (repush data to URL)
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
            'dateRjRef',
            'shiftRjRef',
            'statusRjRef',

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

    // setShiftRJ////////////////
    public function setShiftRJ($id, $desc): void
    {
        $this->dataDaftarPoliRJ['shift'] = $id;
        $this->resetValidation();
    }


    // update dataDaftarPoliRJ klaimId
    public function updatedJenisklaimJenisklaimid()
    {
        $this->dataDaftarPoliRJ['klaimId'] = $this->JenisKlaim['JenisKlaimId'];
    }

    // update dataDaftarPoliRJ kunjunganId
    public function updatedJeniskunjunganJeniskunjunganid()
    {
        $this->dataDaftarPoliRJ['kunjunganId'] = $this->JenisKunjungan['JenisKunjunganId'];
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

    ////////////////////////////////////////////////
    // Lov Pasien //////////////////////
    ////////////////////////////////////////////////
    public function updateddataPasienlovsearch()
    {
        // Variable Search
        $search = $this->dataPasienLovSearch;

        // check LOV by id 

        // set Call MasterPasien False
        $this->callMasterPasien = false;

        // Open Lov Pasien Status
        $this->dataPasienLovStatus = true;

        // if there is no id found and check (min 3 char on search)

        if (strlen($search) < 3) {
            $this->dataPasienLov = [];
        } else {


            // Proses Cari Data


            // 1.Cari berdasarkan nik ->if null DB
            // 2.Cari berdasarkan reg_no ->if null DB
            // 3.Cari berdasarkan reg_name ->if null DB

            // 4. Goto Pasien Baru berdasarkan nik apiBPJS ->if null 
            // 5. Entry Manual Pasien Baru

            // by reg_no
            $cariDataPasienRegNo = $this->cariDataPasienByKeyArr('reg_no', $search);
            if ($cariDataPasienRegNo) {
                $this->dataPasienLov = $cariDataPasienRegNo;
            } else {

                // by nik
                $cariDataPasienNik = $this->cariDataPasienByKeyArr('nik_bpjs', $search);
                if ($cariDataPasienNik) {
                    $this->dataPasienLov = $cariDataPasienNik;
                }
                // by name
                else {
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
                        ->where(DB::raw('upper(reg_name)'), 'like', '%' . strtoupper($search) . '%')
                        ->orWhere('reg_no', 'like', '%' . strtoupper($search) . '%')
                        ->orderBy('reg_name', 'desc')
                        ->limit(50)
                        ->get(), true);
                    if ($cariDataPasienName) {
                        $this->dataPasienLov = $cariDataPasienName;
                    } else {
                        // If Confirmation
                        $this->dataPasienLov = [];
                        $this->emit('cari_Data_Pasien_Tidak_Ditemukan_Confirmation', $search);
                        // $this->callMasterPasien = true;
                    }
                }
            }
        }
    }
    // /////////////////////
    // LOV selected start
    public function setMydataPasienLov($id)
    {
        $this->setDataPasien($id);
        $this->dataDaftarPoliRJ['regNo'] = $id;

        $this->dataPasienLovStatus = false;
        $this->dataPasienLov = [];
        $this->dataPasienLovSearch = $id;
    }
    ////////////////////////////////////////////////
    // Lov Pasien //////////////////////
    ////////////////////////////////////////////////


    /////////////////////////////////////////////////
    // Lov dataDokter //////////////////////
    ////////////////////////////////////////////////
    public function clickdataDokterlov()
    {
        $this->dataDokterLovStatus = true;
        $this->dataDokterLov = [];
    }
    public function updateddataDokterlovsearch()
    {
        // Variable Search
        $search = $this->dataDokterLovSearch;

        // check LOV by id 
        $dataDokter = DB::table('rsmst_doctors')->select(
            'rsmst_doctors.dr_id as dr_id',
            'rsmst_doctors.dr_name as dr_name',
            'kd_dr_bpjs',

            'rsmst_polis.poli_id as poli_id',
            'rsmst_polis.poli_desc as poli_desc',
            'kd_poli_bpjs'
        )
            ->Join('rsmst_polis', 'rsmst_polis.poli_id', 'rsmst_doctors.poli_id')
            ->where('rsmst_doctors.dr_id', $search)
            ->first();

        if ($dataDokter) {
            $this->dataDaftarPoliRJ['drId'] = $dataDokter->dr_id;
            $this->dataDaftarPoliRJ['drDesc'] = $dataDokter->dr_name;

            $this->dataDaftarPoliRJ['poliId'] = $dataDokter->poli_id;
            $this->dataDaftarPoliRJ['poliDesc'] = $dataDokter->poli_desc;

            $this->dataDaftarPoliRJ['kddrbpjs'] = $dataDokter->kd_dr_bpjs;
            $this->dataDaftarPoliRJ['kdpolibpjs'] = $dataDokter->kd_poli_bpjs;

            $this->dataDokterLovStatus = false;
            $this->dataDokterLovSearch = '';
        } else {
            // if there is no id found and check (min 3 char on search)
            if (strlen($search) < 3) {
                $this->dataDokterLov = [];
            } else {
                $this->dataDokterLov = json_decode(
                    DB::table('rsmst_doctors')->select(
                        'rsmst_doctors.dr_id as dr_id',
                        'rsmst_doctors.dr_name as dr_name',
                        'kd_dr_bpjs',

                        'rsmst_polis.poli_id as poli_id',
                        'rsmst_polis.poli_desc as poli_desc',
                        'kd_poli_bpjs'

                    )
                        ->Join('rsmst_polis', 'rsmst_polis.poli_id', 'rsmst_doctors.poli_id')

                        ->Where(DB::raw('upper(dr_name)'), 'like', '%' . strtoupper($search) . '%')
                        ->orWhere('poli_desc', 'like', '%' . strtoupper($search) . '%')
                        ->limit(10)
                        ->orderBy('dr_name', 'ASC')
                        ->orderBy('poli_desc', 'ASC')
                        ->get(),
                    true
                );
            }
            $this->dataDokterLovStatus = true;
            $this->dataDaftarPoliRJ['drId'] = '';
            $this->dataDaftarPoliRJ['drDesc'] = '';
            $this->dataDaftarPoliRJ['poliId'] = '';
            $this->dataDaftarPoliRJ['poliDesc'] = '';
            $this->dataDaftarPoliRJ['kddrbpjs'] = '';
            $this->dataDaftarPoliRJ['kdpolibpjs'] = '';
        }
    }
    // /////////////////////
    // LOV selected start
    public function setMydataDokterLov($id, $name)
    {
        $dataDokter = DB::table('rsmst_doctors')->select(
            'rsmst_doctors.dr_id as dr_id',
            'rsmst_doctors.dr_name as dr_name',
            'kd_dr_bpjs',

            'rsmst_polis.poli_id as poli_id',
            'rsmst_polis.poli_desc as poli_desc',
            'kd_poli_bpjs'
        )
            ->Join('rsmst_polis', 'rsmst_polis.poli_id', 'rsmst_doctors.poli_id')
            ->where('rsmst_doctors.dr_id', $id)
            ->first();
        $this->dataDaftarPoliRJ['drId'] = $dataDokter->dr_id;
        $this->dataDaftarPoliRJ['drDesc'] = $dataDokter->dr_name;

        $this->dataDaftarPoliRJ['poliId'] = $dataDokter->poli_id;
        $this->dataDaftarPoliRJ['poliDesc'] = $dataDokter->poli_desc;

        $this->dataDaftarPoliRJ['kddrbpjs'] = $dataDokter->kd_dr_bpjs;
        $this->dataDaftarPoliRJ['kdpolibpjs'] = $dataDokter->kd_poli_bpjs;

        $this->dataDokterLovStatus = false;
        $this->dataDokterLovSearch = '';
    }
    // LOV selected end
    /////////////////////////////////////////////////
    // Lov dataDokter //////////////////////
    ////////////////////////////////////////////////


    /////////////////////////////////////////////////
    // Lov dataRujukan //////////////////////
    ////////////////////////////////////////////////
    public function clickdataRujukanlov()
    {
        $this->dataRujukanLovStatus = true;
        $this->dataRujukanLov = [];
    }
    public function updateddataRujukanlovsearch()
    {
        // Variable Search
        $search = $this->dataRujukanLovSearch;

        // check LOV by id 
        $dataRujukan = DB::table('rsmst_doctors')->select(
            'rsmst_doctors.dr_id as dr_id',
            'rsmst_doctors.dr_name as dr_name',
            'kd_dr_bpjs',

            'rsmst_polis.poli_id as poli_id',
            'rsmst_polis.poli_desc as poli_desc',
            'kd_poli_bpjs'
        )
            ->Join('rsmst_polis', 'rsmst_polis.poli_id', 'rsmst_doctors.poli_id')
            ->where('rsmst_doctors.dr_id', $search)
            ->first();

        if ($dataRujukan) {
            $this->dataDaftarPoliRJ['drId'] = $dataRujukan->dr_id;
            $this->dataDaftarPoliRJ['drDesc'] = $dataRujukan->dr_name;

            $this->dataDaftarPoliRJ['poliId'] = $dataRujukan->poli_id;
            $this->dataDaftarPoliRJ['poliDesc'] = $dataRujukan->poli_desc;

            $this->dataDaftarPoliRJ['kddrbpjs'] = $dataRujukan->kd_dr_bpjs;
            $this->dataDaftarPoliRJ['kdpolibpjs'] = $dataRujukan->kd_poli_bpjs;

            $this->dataRujukanLovStatus = false;
            $this->dataRujukanLovSearch = '';
        } else {
            // if there is no id found and check (min 3 char on search)
            if (strlen($search) < 3) {
                $this->dataRujukanLov = [];
            } else {
                $this->dataRujukanLov = json_decode(
                    DB::table('rsmst_doctors')->select(
                        'rsmst_doctors.dr_id as dr_id',
                        'rsmst_doctors.dr_name as dr_name',
                        'kd_dr_bpjs',

                        'rsmst_polis.poli_id as poli_id',
                        'rsmst_polis.poli_desc as poli_desc',
                        'kd_poli_bpjs'

                    )
                        ->Join('rsmst_polis', 'rsmst_polis.poli_id', 'rsmst_doctors.poli_id')

                        ->Where(DB::raw('upper(dr_name)'), 'like', '%' . strtoupper($search) . '%')
                        ->orWhere('poli_desc', 'like', '%' . strtoupper($search) . '%')
                        ->limit(10)
                        ->orderBy('dr_name', 'ASC')
                        ->orderBy('poli_desc', 'ASC')
                        ->get(),
                    true
                );
            }
            $this->dataRujukanLovStatus = true;
            $this->dataDaftarPoliRJ['drId'] = '';
            $this->dataDaftarPoliRJ['drDesc'] = '';
            $this->dataDaftarPoliRJ['poliId'] = '';
            $this->dataDaftarPoliRJ['poliDesc'] = '';
            $this->dataDaftarPoliRJ['kddrbpjs'] = '';
            $this->dataDaftarPoliRJ['kdpolibpjs'] = '';
        }
    }
    // /////////////////////
    // LOV selected start
    public function setMydataRujukanLov($id, $name)
    {
        $dataRujukan = DB::table('rsmst_doctors')->select(
            'rsmst_doctors.dr_id as dr_id',
            'rsmst_doctors.dr_name as dr_name',
            'kd_dr_bpjs',

            'rsmst_polis.poli_id as poli_id',
            'rsmst_polis.poli_desc as poli_desc',
            'kd_poli_bpjs'
        )
            ->Join('rsmst_polis', 'rsmst_polis.poli_id', 'rsmst_doctors.poli_id')
            ->where('rsmst_doctors.dr_id', $id)
            ->first();
        $this->dataDaftarPoliRJ['drId'] = $dataRujukan->dr_id;
        $this->dataDaftarPoliRJ['drDesc'] = $dataRujukan->dr_name;

        $this->dataDaftarPoliRJ['poliId'] = $dataRujukan->poli_id;
        $this->dataDaftarPoliRJ['poliDesc'] = $dataRujukan->poli_desc;

        $this->dataDaftarPoliRJ['kddrbpjs'] = $dataRujukan->kd_dr_bpjs;
        $this->dataDaftarPoliRJ['kdpolibpjs'] = $dataRujukan->kd_poli_bpjs;

        $this->dataRujukanLovStatus = false;
        $this->dataRujukanLovSearch = '';
    }
    // LOV selected end
    /////////////////////////////////////////////////
    // Lov dataRujukan //////////////////////
    ////////////////////////////////////////////////


    ////////////////////////////logic lain/////////////////////////////////////

    /////////////////////////////////////////////////////////////////
    ///////////cariDataPasienByKey/////////////////////////////////
    /////////////////////////////////////////////////////////////////
    private function cariDataPasienByKeyArr($key, $search)
    {
        $cariDataPasienByKeyArr = json_decode(DB::table('rsmst_pasiens')
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

        return  $cariDataPasienByKeyArr;
    }
    ////////////////////////////////////////////////////////////////////////

    private function cariDataPasienByKeyCollection($key, $search)
    {
        $findData = DB::table('rsmst_pasiens')
            ->select(
                DB::raw("to_char(reg_date,'dd/mm/yyyy hh24:mi:ss') as reg_date"),
                DB::raw("to_char(reg_date,'yyyymmddhh24miss') as reg_date1"),
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
                'rsmst_religions.rel_id as rel_id',
                'rel_desc',
                'rsmst_educations.edu_id as edu_id',
                'edu_desc',
                'rsmst_jobs.job_id as job_id',
                'job_name',
                'kk',
                'nyonya',
                'no_kk',
                'address',
                'rsmst_desas.des_id as des_id',
                'des_name',
                'rt',
                'rw',
                'rsmst_kecamatans.kec_id as kec_id',
                'kec_name',
                'rsmst_kabupatens.kab_id as kab_id',
                'kab_name',
                'rsmst_propinsis.prop_id as prop_id',
                'prop_name',
                'phone'
            )->join('rsmst_religions', 'rsmst_religions.rel_id', 'rsmst_pasiens.rel_id')
            ->join('rsmst_educations', 'rsmst_educations.edu_id', 'rsmst_pasiens.edu_id')
            ->join('rsmst_jobs', 'rsmst_jobs.job_id', 'rsmst_pasiens.job_id')
            ->join('rsmst_desas', 'rsmst_desas.des_id', 'rsmst_pasiens.des_id')
            ->join('rsmst_kecamatans', 'rsmst_kecamatans.kec_id', 'rsmst_pasiens.kec_id')
            ->join('rsmst_kabupatens', 'rsmst_kabupatens.kab_id', 'rsmst_pasiens.kab_id')
            ->join('rsmst_propinsis', 'rsmst_propinsis.prop_id', 'rsmst_pasiens.prop_id')
            ->where($key, $search)
            ->first();
        return $findData;
    }
    /////////////////////////////////////////////////////////////////
    ///////////cariDataPasienByKey/////////////////////////////////
    /////////////////////////////////////////////////////////////////




    // resert input private////////////////
    /////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////
    private function setShiftnCurrentDate(): void
    {
        // dd/mm/yyyy hh24:mi:ss
        $this->dataDaftarPoliRJ['rjDate'] = Carbon::now()->format('d/m/Y H:i:s');

        // shift
        $findShift = DB::table('rstxn_shiftctls')->select('shift')
            ->whereRaw("'" . Carbon::now()->format('H:i:s') . "' between
            shift_start and shift_end")
            ->first();
        $this->dataDaftarPoliRJ['shift'] = $findShift->shift ? $findShift->shift : 3;
    }

    // Cari Data Pasien Rawat Jalan ////////////////
    //////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////

    // Find data from table start////////////////
    // syncronize array and table_json Pasien
    private function setDataPasien($value): void
    {
        $findData = DB::table('rsmst_pasiens')
            ->select('meta_data_pasien_json')
            ->where('reg_no', $value)
            ->first();

        if ($findData->meta_data_pasien_json == null) {

            $findData = $this->cariDataPasienByKeyCollection('reg_no', $value);

            $this->dataPasien['pasien']['regDate'] = $findData->reg_date;
            $this->dataPasien['pasien']['regNo'] = $findData->reg_no;
            $this->dataPasien['pasien']['regName'] = $findData->reg_name;
            $this->dataPasien['pasien']['identitas']['idbpjs'] = $findData->nokartu_bpjs;
            $this->dataPasien['pasien']['identitas']['nik'] = $findData->nik_bpjs;
            $this->dataPasien['pasien']['jenisKelamin']['jenisKelaminId'] = ($findData->sex == 'L') ? 1 : 2;
            $this->dataPasien['pasien']['jenisKelamin']['jenisKelaminDesc'] = ($findData->sex == 'L') ? 'Laki-laki' : 'Perempuan';
            $this->dataPasien['pasien']['tglLahir'] = $findData->birth_date;
            $this->dataPasien['pasien']['thn'] = $findData->thn;
            $this->dataPasien['pasien']['bln'] = $findData->bln;
            $this->dataPasien['pasien']['hari'] = $findData->hari;
            $this->dataPasien['pasien']['tempatLahir'] = $findData->birth_place;
            $this->dataPasien['pasien']['golonganDarah']['golonganDarahId'] = '13';
            $this->dataPasien['pasien']['golonganDarah']['golonganDarahDesc'] = 'Tidak Tahu';
            $this->dataPasien['pasien']['statusPerkawinan']['statusPerkawinanId'] = '1';
            $this->dataPasien['pasien']['statusPerkawinan']['statusPerkawinanDesc'] = 'Belum Kawin';

            $this->dataPasien['pasien']['agama']['agamaId'] = $findData->rel_id;
            $this->dataPasien['pasien']['agama']['agamaDesc'] = $findData->rel_desc;

            $this->dataPasien['pasien']['pendidikan']['pendidikanId'] = $findData->edu_id;
            $this->dataPasien['pasien']['pendidikan']['pendidikanDesc'] = $findData->edu_desc;

            $this->dataPasien['pasien']['pekerjaan']['pekerjaanId'] = $findData->job_id;
            $this->dataPasien['pasien']['pekerjaan']['pekerjaanDesc'] = $findData->job_name;


            $this->dataPasien['pasien']['hubungan']['namaPenanggungJawab'] = $findData->reg_no;
            $this->dataPasien['pasien']['hubungan']['namaIbu'] = $findData->reg_no;

            $this->dataPasien['pasien']['identitas']['nik'] = $findData->nik_bpjs;
            $this->dataPasien['pasien']['identitas']['idBpjs'] = $findData->nokartu_bpjs;


            $this->dataPasien['pasien']['identitas']['alamat'] = $findData->address;

            $this->dataPasien['pasien']['identitas']['desaId'] = $findData->des_id;
            $this->dataPasien['pasien']['identitas']['desaName'] = $findData->des_name;

            $this->dataPasien['pasien']['identitas']['rt'] = $findData->rt;
            $this->dataPasien['pasien']['identitas']['rw'] = $findData->rw;
            $this->dataPasien['pasien']['identitas']['kecamatanId'] = $findData->kec_id;
            $this->dataPasien['pasien']['identitas']['kecamatanName'] = $findData->kec_name;

            $this->dataPasien['pasien']['identitas']['kotaId'] = $findData->kab_id;
            $this->dataPasien['pasien']['identitas']['kotaName'] = $findData->kab_name;

            $this->dataPasien['pasien']['identitas']['propinsiId'] = $findData->prop_id;
            $this->dataPasien['pasien']['identitas']['propinsiName'] = $findData->prop_name;

            $this->dataPasien['pasien']['kontak']['nomerTelponSelulerPasien'] = $findData->phone;

            $this->dataPasien['pasien']['hubungan']['namaPenanggungJawab'] = $findData->kk;
            $this->dataPasien['pasien']['hubungan']['namaIbu'] = $findData->nyonya;


            // $this->dataPasien['pasien']['hubungan']['noPenanggungJawab'] = $findData->no_kk;


            // dd($this->dataPasien);
        } else {
            // ubah data Pasien
            $this->dataPasien = json_decode($findData->meta_data_pasien_json, true);
        }
    }

    // Find data from table end////////////////

    // validate Data RJ//////////////////////////////////////////////////
    // ///////////////////////////////////////////////////////////////
    // ///////////////////////////////////////////////////////////////

    private function validateDataRJ(): void
    {
        // customErrorMessages
        $messages = customErrorMessagesTrait::messages();

        // require nik ketika pasien tidak dikenal



        $rules = [

            "dataDaftarPoliRJ.regNo" => "bail|required|exists:rsmst_pasiens,reg_no",

            "dataDaftarPoliRJ.drId" => "required",
            "dataDaftarPoliRJ.drDesc" => "required",

            "dataDaftarPoliRJ.poliId" => "required",
            "dataDaftarPoliRJ.poliDesc" => "required",

            "dataDaftarPoliRJ.kddrbpjs" => '',
            "dataDaftarPoliRJ.kdpolibpjs" => '',


            "dataDaftarPoliRJ.rjDate" => "required",
            "dataDaftarPoliRJ.rjNo" => "required",

            "dataDaftarPoliRJ.shift" => "required",

            "dataDaftarPoliRJ.noAntrian" => "required",
            "dataDaftarPoliRJ.noBooking" => "required",

            "dataDaftarPoliRJ.slCodeFrom" => "required",
            "dataDaftarPoliRJ.passStatus" => "",

            "dataDaftarPoliRJ.rjStatus" => "required",
            "dataDaftarPoliRJ.txnStatus" => "required",
            "dataDaftarPoliRJ.ermStatus" => "required",

            "dataDaftarPoliRJ.cekLab" => "required",

            "dataDaftarPoliRJ.kunjunganInternalStatus" => "required",

            "dataDaftarPoliRJ.noReferensi" => "bail|min:3|max:19",

        ];

        // gabunga array nik jika pasien tidak dikenal
        // if ($this->dataPasien['pasien']['pasientidakdikenal']) {
        //     $rules['dataPasien.pasien.identitas.nik'] =  'digits:16';
        // } else {
        //     $rules['dataPasien.pasien.identitas.nik'] =  'required|digits:16';
        // }

        // Proses Validasi///////////////////////////////////////////
        try {
            $this->validate($rules, $messages);
        } catch (\Illuminate\Validation\ValidationException $e) {

            $this->emit('toastr-error', "Lakukan Pengecekan kembali Input Data Pasien.");
            $this->validate($rules, $messages);
        }
    }


    // insert record start////////////////
    public function store()
    {
        // set data RJno / NoBooking / NoAntrian / klaimId / kunjunganId
        $this->setDataPrimer();

        // Validate RJ
        $this->validateDataRJ();

        // Logic push data ke BPJS


        // Push data antrian dan Task ID 3
        $this->pushDataAntrian();



        // Logic insert and update mode start //////////
        if ($this->isOpenMode == 'insert') {
            $this->insertDataRJ();
            $this->isOpenMode = 'update';
        } else if ($this->isOpenMode == 'update') {
            $this->updateDataRJ($this->dataDaftarPoliRJ['rjNo']);
        }




        // Opstional (Jika ingin fast Entry resert setelah proses diatas)
        // Jika ingin auto close resert dan close aktifkan
        // $this->resetInputFields();
        // $this->closeModal();
    }


    private function insertDataRJ(): void
    {

        // insert into table transaksi
        DB::table('rstxn_rjhdrs')->insert([
            'rj_no' => $this->dataDaftarPoliRJ['rjNo'],
            'rj_date' => DB::raw("to_date('" . $this->dataDaftarPoliRJ['rjDate'] . "','dd/mm/yyyy hh24:mi:ss')"),
            'reg_no' => $this->dataDaftarPoliRJ['regNo'],
            'nobooking' => $this->dataDaftarPoliRJ['noBooking'],
            'no_antrian' => $this->dataDaftarPoliRJ['noAntrian'],

            'klaim_id' => $this->dataDaftarPoliRJ['klaimId'],
            'poli_id' => $this->dataDaftarPoliRJ['poliId'],
            'dr_id' => $this->dataDaftarPoliRJ['drId'],
            'shift' => $this->dataDaftarPoliRJ['shift'],

            'txn_status' => $this->dataDaftarPoliRJ['txnStatus'],
            'rj_status' => $this->dataDaftarPoliRJ['rjStatus'],
            'erm_status' => $this->dataDaftarPoliRJ['ermStatus'],

            'pass_status' => $this->dataDaftarPoliRJ['passStatus'] == 'N' ? 'N' : 'O', //Baru lama

            'cek_lab' => $this->dataDaftarPoliRJ['cekLab'],
            'sl_codefrom' => $this->dataDaftarPoliRJ['slCodeFrom'],
            'kunjungan_internal_status' => $this->dataDaftarPoliRJ['kunjunganInternalStatus'],
            'push_antrian_bpjs_status' => $this->HttpGetBpjsStatus, //status push antrian 200 /201/ 400
            'push_antrian_bpjs_json' => $this->HttpGetBpjsJson,  // response json
            'datadaftarpolirj_json' => json_encode($this->dataDaftarPoliRJ, true),
            'datadaftarpolirj_xml' => ArrayToXml::convert($this->dataDaftarPoliRJ),

            'waktu_masuk_pelayanan' => DB::raw("to_date('" . $this->dataDaftarPoliRJ['rjDate'] . "','dd/mm/yyyy hh24:mi:ss')"), //waktu masuk = rjdate

        ]);

        $this->emit('toastr-success', "Data sudah tersimpan.");
    }

    private function updateDataRJ($rjNo): void
    {

        // update table trnsaksi
        DB::table('rstxn_rjhdrs')
            ->where('rj_no', $rjNo)
            ->update([
                // 'rj_no' => $this->dataDaftarPoliRJ['rjNo'],
                'rj_date' => DB::raw("to_date('" . $this->dataDaftarPoliRJ['rjDate'] . "','dd/mm/yyyy hh24:mi:ss')"),
                'reg_no' => $this->dataDaftarPoliRJ['regNo'],
                'nobooking' => $this->dataDaftarPoliRJ['noBooking'],
                'no_antrian' => $this->dataDaftarPoliRJ['noAntrian'],

                'klaim_id' => $this->dataDaftarPoliRJ['klaimId'],
                'poli_id' => $this->dataDaftarPoliRJ['poliId'],
                'dr_id' => $this->dataDaftarPoliRJ['drId'],
                'shift' => $this->dataDaftarPoliRJ['shift'],

                'txn_status' => $this->dataDaftarPoliRJ['txnStatus'],
                'rj_status' => $this->dataDaftarPoliRJ['rjStatus'],
                'erm_status' => $this->dataDaftarPoliRJ['ermStatus'],

                'pass_status' => $this->dataDaftarPoliRJ['passStatus'] == 'N' ? 'N' : 'O', //Baru lama

                'cek_lab' => $this->dataDaftarPoliRJ['cekLab'],
                'sl_codefrom' => $this->dataDaftarPoliRJ['slCodeFrom'],
                'kunjungan_internal_status' => $this->dataDaftarPoliRJ['kunjunganInternalStatus'],
                'push_antrian_bpjs_status' => $this->HttpGetBpjsStatus, //status push antrian 200 /201/ 400
                'push_antrian_bpjs_json' => $this->HttpGetBpjsJson,  // response json
                'datadaftarpolirj_json' => json_encode($this->dataDaftarPoliRJ, true),
                'datadaftarpolirj_xml' => ArrayToXml::convert($this->dataDaftarPoliRJ),

                'waktu_masuk_pelayanan' => DB::raw("to_date('" . $this->dataDaftarPoliRJ['rjDate'] . "','dd/mm/yyyy hh24:mi:ss')"), //waktu masuk = rjdate

            ]);

        $this->emit('toastr-success', "Data berhasil diupdate.");
    }

    // set data RJno / NoBooking / NoAntrian / klaimId / kunjunganId
    private function setDataPrimer(): void
    {
        // Klaim & Kunjungan 
        // dd($this->dataDaftarPoliRJ['klaimId']);
        $this->dataDaftarPoliRJ['klaimId'] = $this->JenisKlaim['JenisKlaimId'];
        $this->dataDaftarPoliRJ['kunjunganId'] = $this->JenisKunjungan['JenisKunjunganId'];

        // JenisKunjungan Internal 
        if ($this->JenisKunjungan['JenisKunjunganId'] == 2) {
            $this->dataDaftarPoliRJ['kunjunganInternalStatus'] = "1";
        }

        // noBooking
        if (!$this->dataDaftarPoliRJ['noBooking']) {
            $this->dataDaftarPoliRJ['noBooking'] = Carbon::now()->format('YmdHis') . 'RSIM';
        }

        // rjNoMax
        if (!$this->dataDaftarPoliRJ['rjNo']) {
            $sql = "select nvl(max(rj_no)+1,1) rjno_max from rstxn_rjhdrs";
            $this->dataDaftarPoliRJ['rjNo'] = DB::scalar($sql);
        }

        // noUrutAntrian (count all kecuali KRonis) if KR 999
        $sql = "select count(*) no_antrian 
         from rstxn_rjhdrs 
         where dr_id=:drId
         and to_char(rj_date,'ddmmyyyy')=:tgl
         and klaim_id!='KR'";

        // Antrian ketika data antrian kosong
        if (!$this->dataDaftarPoliRJ['noAntrian']) {
            // proses antrian
            if ($this->dataDaftarPoliRJ['klaimId'] != 'KR') {
                $noUrutAntrian = DB::scalar($sql, [
                    "tgl" => Carbon::createFromFormat('d/m/Y H:i:s', $this->dataDaftarPoliRJ['rjDate'])->format('dmY'),
                    "drId" => $this->dataDaftarPoliRJ['drId']
                ]);
                if ($noUrutAntrian == 0) {
                    $noAntrian = 4;
                } else if ($noUrutAntrian == 1) {
                    $noAntrian = 5;
                } else if ($noUrutAntrian == 2) {
                    $noAntrian = 6;
                } else if ($noUrutAntrian > 2) {
                    $noAntrian = $noUrutAntrian + 3 + 1;
                }
            } else {
                // Kronis
                $noAntrian = 999;
            }

            $this->dataDaftarPoliRJ['noAntrian'] = $noAntrian;
        }
    }

    private function pushDataAntrian()
    {
        //push data antrian UMUM BPJS kecuali 'kronis'
        if ($this->dataDaftarPoliRJ['klaimId'] != 'KR') {
            $rjdate = Carbon::createFromFormat('d/m/Y H:i:s', $this->dataDaftarPoliRJ['rjDate']); //Tgl RJ

            $dayDesc = $rjdate->isoFormat('dddd'); //Senin Selasa Rabu ...

            $dayid = ($dayDesc == 'Senin') ? 1 //kode Senin Selasa Rabu ...
                : ($dayDesc == 'Selasa' ? 2
                    : ($dayDesc == 'Rabu' ? 3
                        : ($dayDesc == 'Kamis' ? 4
                            : ($dayDesc == 'Jumat' ? 5
                                : 6
                            )
                        )
                    )
                );

            $JadwalPraktek = json_decode(json_encode(DB::table('scmst_scpolis')
                ->select(
                    //Poli RS
                    'scmst_scpolis.dr_id as dr_id',
                    'rsmst_doctors.dr_name as dr_name',
                    'scmst_scpolis.sc_poli_ket as sc_poli_ket',
                    'scmst_scpolis.poli_id as poli_id',
                    'rsmst_polis.poli_desc as poli_desc',

                    //Poli BPJS
                    'rsmst_doctors.kd_dr_bpjs as kd_dr_bpjs',
                    'rsmst_polis.kd_poli_bpjs as kd_poli_bpjs',

                    DB::raw("nvl(mulai_praktek,'00:00:00') as mulai_praktek"),
                    DB::raw("nvl(selesai_praktek,'00:00:00') as selesai_praktek"),

                    DB::raw('nvl(kuota,35) as kuota'),
                )
                ->Join('rsmst_doctors', 'rsmst_doctors.dr_id', 'scmst_scpolis.dr_id')
                ->Join('rsmst_polis', 'rsmst_polis.poli_id', 'scmst_scpolis.poli_id')
                ->Where('day_id', $dayid)
                ->Where('scmst_scpolis.dr_id', $this->dataDaftarPoliRJ['drId']) //Cek Poli RS
                ->Where('sc_poli_status_', 1)
                ->orderBy('scmst_scpolis.no_urut', 'ASC')
                ->orderBy('scmst_scpolis.poli_id', 'ASC')
                ->first(), true), true);


            if (!$JadwalPraktek) {
                $JadwalPraktek['mulai_praktek'] = '07:00:00';
                $JadwalPraktek['selesai_praktek'] = '13:00:00';

                $JadwalPraktek['kuota'] = 30;
            }

            // Pelayanan (Poli Tgl + Jam Layanan)
            $hariLayananJamLayanan = Carbon::createFromFormat('d/m/Y H:i:s', $rjdate->format('d/m/Y') . ' ' . $JadwalPraktek['mulai_praktek']);
            // $timestamp = $hariLayananJamLayanan->timestamp; //Timestemp Layanan
            $jadwal_estimasi = $hariLayananJamLayanan->addMinutes(10 * ($this->dataDaftarPoliRJ['noAntrian'] + 1)); // Hari Layanan||JamPraktek + 10menit
            // JamPraktek
            $jampraktek = Str::substr($JadwalPraktek['mulai_praktek'], 0, 5) . '-' . Str::substr($JadwalPraktek['selesai_praktek'], 0, 5); //'00:00-00:00'

            $antreanadd = [
                "kodebooking" => $this->dataDaftarPoliRJ['noBooking'],
                "jenispasien" => ($this->dataDaftarPoliRJ['klaimId'] == 'JM') ? 'JKN' : 'NON JKN', //Layanan UMUM BPJS
                "nomorkartu" => ($this->dataDaftarPoliRJ['klaimId'] == 'JM') ? $this->dataPasien['pasien']['identitas']['idbpjs'] : '',
                "nik" => $this->dataPasien['pasien']['identitas']['nik'],
                "nohp" =>  $this->dataPasien['pasien']['kontak']['nomerTelponSelulerPasien'],
                "kodepoli" => $this->dataDaftarPoliRJ['kdpolibpjs'] ? $this->dataDaftarPoliRJ['kdpolibpjs'] : $this->dataDaftarPoliRJ['poliId'], //if null poliidRS
                "namapoli" => $this->dataDaftarPoliRJ['poliDesc'],
                "pasienbaru" => 0,
                "norm" => $this->dataDaftarPoliRJ['regNo'],
                "tanggalperiksa" => Carbon::createFromFormat('d/m/Y H:i:s', $this->dataDaftarPoliRJ['rjDate'])->format('Y-m-d'),
                "kodedokter" => $this->dataDaftarPoliRJ['kddrbpjs'] ? $this->dataDaftarPoliRJ['kddrbpjs'] : $this->dataDaftarPoliRJ['drId'], //if Null dridRS
                "namadokter" => $this->dataDaftarPoliRJ['drDesc'],
                "jampraktek" => $jampraktek,
                "jeniskunjungan" => $this->dataDaftarPoliRJ['kunjunganId'], //FKTP/FKTL/Kontrol/Internal
                "nomorreferensi" => $this->dataDaftarPoliRJ['noReferensi'],
                "nomorantrean" => $this->dataDaftarPoliRJ['noAntrian'],
                "angkaantrean" => $this->dataDaftarPoliRJ['noAntrian'],
                "estimasidilayani" => $jadwal_estimasi->timestamp,
                "sisakuotajkn" => $JadwalPraktek['kuota'] - $this->dataDaftarPoliRJ['noAntrian'],
                "kuotajkn" => $JadwalPraktek['kuota'],
                "sisakuotanonjkn" => $JadwalPraktek['kuota'] - $this->dataDaftarPoliRJ['noAntrian'],
                "kuotanonjkn" => $JadwalPraktek['kuota'],
                "keterangan" => "Peserta harap 30 menit lebih awal guna pencatatan administrasi.",
            ];
        }

        // http Post


        // Tambah Antrean
        $HttpGetBpjs =  AntrianTrait::tambah_antrean($antreanadd)->getOriginalContent();
        // set http response to public
        $this->HttpGetBpjsStatus = $HttpGetBpjs['metadata']['code']; //status 200 201 400 ..
        $this->HttpGetBpjsJson = json_encode($HttpGetBpjs, true); //Return Response Tambah Antrean


        // metadata d kecil
        if ($HttpGetBpjs['metadata']['code'] == 200) {
            $this->emit('toastr-success', 'Tambah Antrian ' .  $HttpGetBpjs['metadata']['code'] . ' ' . $HttpGetBpjs['metadata']['message']);
        } else {
            $this->emit('toastr-error', 'Tambah Antrian ' . $HttpGetBpjs['metadata']['code'] . ' ' . $HttpGetBpjs['metadata']['message']);

            // Ulangi Proses pushDataAntrian;
            $this->emit('rePush_Data_Antrian_Confirmation');
        }

        /////////////////////////
        // Update TaskId 3
        /////////////////////////

        $waktu = Carbon::createFromFormat('d/m/Y H:i:s', $this->dataDaftarPoliRJ['rjDate'])->timestamp * 1000; //waktu dalam timestamp milisecond
        // DB Json
        $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId3'] = $this->dataDaftarPoliRJ['rjDate'];
        $noBooking = $this->dataDaftarPoliRJ['noBooking'];

        $this->pushDataTaskId($noBooking, "3", $waktu);
    }

    private function pushDataTaskId($noBooking, $taskId, $time): void
    {
        //////////////////////////////////////////////////////////
        /////////////////////////////////////////////////////////
        // Update Task Id $kodebooking, $taskid, $waktu, $jenisresep

        $waktu = $time;
        $HttpGetBpjs =  AntrianTrait::update_antrean($noBooking, 3, $waktu, "")->getOriginalContent();

        // set http response to public
        $this->HttpGetBpjsStatus = $HttpGetBpjs['metadata']['code']; //status 200 201 400 ..
        $this->HttpGetBpjsJson = json_encode($HttpGetBpjs, true); //Return Response Tambah Antrean

        // metadata d kecil
        if ($HttpGetBpjs['metadata']['code'] == 200) {
            $this->emit('toastr-success', 'Task Id' . $taskId . ' ' . $HttpGetBpjs['metadata']['code'] . ' ' . $HttpGetBpjs['metadata']['message']);
        } else {
            $this->emit('toastr-error', 'Task Id' . $taskId . ' ' .  $HttpGetBpjs['metadata']['code'] . ' ' . $HttpGetBpjs['metadata']['message']);

            // Ulangi Proses pushTaskId;
            // $this->emit('rePush_Data_TaskId_Confirmation');
        }
    }












    private function rujukanPeserta($idBpjs): void
    {
        // sini--------------------------------------------------------------------------------------------------------
        $HttpGetBpjs =  VclaimTrait::rujukan_peserta($idBpjs)->getOriginalContent();

        // metadata d kecil
        if ($HttpGetBpjs['metadata']['code'] == 200) {
            $this->dataRujukanLovStatus = true;
            $this->dataRujukanLov = json_decode(json_encode($HttpGetBpjs['response']['rujukan'], true), true);
            dd($this->dataRujukanLov);
            $this->emit('toastr-success', $HttpGetBpjs['metadata']['code'] . ' ' . $HttpGetBpjs['metadata']['message']);
        } else {
            $this->dataRujukanLovStatus = false;
            $this->dataRujukanLov = [];
            $this->emit('toastr-error', $HttpGetBpjs['metadata']['code'] . ' ' . $HttpGetBpjs['metadata']['message']);
        }
    }
    public function CarirujukanPeserta(): void
    {
        if ($this->JenisKlaim['JenisKlaimId'] != 'JM') {
            $this->emit('toastr-error', 'Jenis Klaim ' . $this->JenisKlaim['JenisKlaimDesc']);
        }

        // if jenis klaim BPJS dan Kunjungan = FKTP (1)
        if ($this->JenisKlaim['JenisKlaimId'] == 'JM' && $this->JenisKunjungan['JenisKunjunganId'] == 1) {
            $this->rujukanPeserta($this->dataPasien['pasien']['identitas']['idbpjs']);
        }
    }

    public function callFormPasien(): void
    {
        // set Call MasterPasien True
        $this->callMasterPasien = true;
    }






    // when new form instance
    public function mount()
    {


        $this->dateRjRef = Carbon::now()->format('d/m/Y');

        $findShift = DB::table('rstxn_shiftctls')->select('shift')
            ->whereRaw("'" . Carbon::now()->format('H:i:s') . "' between shift_start and shift_end")
            ->first();
        $this->shiftRjRef['shiftId'] = isset($findShift->shift) ? $findShift->shift : 3;
        $this->shiftRjRef['shiftDesc'] = isset($findShift->shift) ? $findShift->shift : 3;
    }


    // select data start////////////////
    public function render()
    {
        // dd(Carbon::createFromTimestamp(1690181400)->toDateTimeString());

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
                        'vno_sep',
                        'no_antrian'
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
