<?php

namespace App\Http\Livewire\DaftarRI\FormEntryRI;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Http\Traits\customErrorMessagesTrait;

use Barryvdh\DomPDF\Facade\Pdf;


use App\Http\Traits\BPJS\VclaimTrait;
use App\Http\Traits\EmrRI\EmrRITrait;



use Livewire\Component;

class FormEntryRI extends Component
{
    use EmrRITrait;

    // listener from blade////////////////
    protected $listeners = [
        'confirm_doble_record_RIp' => 'setforceInsertRecord',
    ];



    public bool $isOpen = false;
    public string $isOpenMode = 'insert';
    public bool $forceInsertRecord = false;
    public int $riHdrNo;





    public  $dataPasien = [];


    public $shiftRiRef = [
        'shiftId' => '1',
        'shiftDesc' => '1',
        'shiftOptions' => [
            ['shiftId' => '1', 'shiftDesc' => '1'],
            ['shiftId' => '2', 'shiftDesc' => '2'],
            ['shiftId' => '3', 'shiftDesc' => '3'],
        ]
    ];

    public $statusRiRef = [
        'statusId' => 'I',
        'statusDesc' => 'Inap',
        'statusOptions' => [
            ['statusId' => 'I', 'statusDesc' => 'Inap'],
            ['statusId' => 'P', 'statusDesc' => 'Pulang'],
        ]
    ];

    public $drRiRef = [
        'drId' => 'All',
        'drName' => 'All',
        'drOptions' => [
            [
                'drId' => 'All',
                'drName' => 'All'
            ]
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

    public $entryRi = [
        'entryId' => '5',
        'entryDesc' => 'Datang Sendiri',
        'entryOptions' => [
            ['entryId' => '5', 'entryDesc' => 'Datang Sendiri'],
        ]
    ];

    public $dataDaftarRi = [];

    // http status Push antrian BPJS
    public $HttpGetBpjsStatus; //status push antrian 200 /201/ 400
    public $HttpGetBpjsJson; // response json

    public $SEPJsonReq = [
        "request" =>  [
            "t_sep" =>  [
                "noKartu" => "",
                "tglSep" => "", //Y-m-d
                "ppkPelayanan" => "",
                "jnsPelayanan" => "",
                "klsRawat" =>  [
                    "klsRawatHak" => "",
                    "klsRawatNaik" => "",
                    "pembiayaan" => "",
                    "penanggungJawab" => "",
                ],
                "noMR" => "",
                "rujukan" =>  [
                    "asalRujukan" => "",
                    "tglRujukan" => "", //Y-m-d
                    "noRujukan" => "",
                    "ppkRujukan" => "",
                ],
                "catatan" => "",
                "diagAwal" => "",
                "poli" =>  [
                    "tujuan" => "",
                    "tujuanNama" => "",
                    "eksekutif" => "0",
                    "eksekutifRef" => [],
                ],
                "cob" =>  [
                    "cob" => "0",
                    "cobRef" => [],
                ],
                "katarak" =>  [
                    "katarak" => "0",
                    "katarakRef" => [],
                ],
                "jaminan" =>  [
                    "lakaLantas" => "0",
                    "lakaLantasRef" => [],
                    "noLP" => "",
                    "penjamin" =>  [
                        "tglKejadian" => "",
                        "keterangan" => "",
                        "suplesi" =>  [
                            "suplesi" => "0",
                            "noSepSuplesi" => "",
                            "lokasiLaka" =>  [
                                "kdPropinsi" => "",
                                "kdKabupaten" => "",
                                "kdKecamatan" => "",
                            ]
                        ]
                    ]
                ],
                "tujuanKunj" => "0",
                "tujuanKunjDesc" => "Normal",

                "flagProcedure" => "",
                "flagProcedureDesc" => "",

                "kdPenunjang" => "",
                "kdPenunjangDesc" => "",

                "assesmentPel" => "",
                "assesmentPelDesc" => "",


                "skdp" =>  [
                    "noSurat" => "",
                    "kodeDPJP" => "",
                ],
                "dpjpLayan" => "",
                "noTelp" => "",
                "user" => "sirus App",
            ],
        ],
    ];
    //////////////////////////////
    public $SEPQuestionnaire = [
        'tujuanKunj' => [
            ['tujuanKunjId' => '', 'tujuanKunjDesc' => ''],
            ['tujuanKunjId' => '0', 'tujuanKunjDesc' => 'Normal'],
            ['tujuanKunjId' => '1', 'tujuanKunjDesc' => 'Prosedur'],
            ['tujuanKunjId' => '2', 'tujuanKunjDesc' => 'Konsul Dokter'],
        ],
        'flagProcedure' => [
            ['flagProcedureId' => '', 'flagProcedureDesc' => ''],
            ['flagProcedureId' => '0', 'flagProcedureDesc' => 'Prosedur Tidak Berkelanjutan'],
            ['flagProcedureId' => '1', 'flagProcedureDesc' => 'Prosedur dan Terapi Berkelanjutan'],
        ],
        'kdPenunjang' => [
            ['kdPenunjangId' => '', 'kdPenunjangDesc' => ''],
            ['kdPenunjangId' => '1', 'kdPenunjangDesc' => 'Radioterapi'],
            ['kdPenunjangId' => '2', 'kdPenunjangDesc' => 'Kemoterapi'],
            ['kdPenunjangId' => '3', 'kdPenunjangDesc' => 'Rehabilitasi Medik'],
            ['kdPenunjangId' => '4', 'kdPenunjangDesc' => 'Rehabilitasi Psikososial'],
            ['kdPenunjangId' => '5', 'kdPenunjangDesc' => 'Transfusi Darah'],
            ['kdPenunjangId' => '6', 'kdPenunjangDesc' => 'Pelayanan Gigi'],
            ['kdPenunjangId' => '7', 'kdPenunjangDesc' => 'Laboratorium'],
            ['kdPenunjangId' => '8', 'kdPenunjangDesc' => 'USG'],
            ['kdPenunjangId' => '9', 'kdPenunjangDesc' => 'Farmasi'],
            ['kdPenunjangId' => '10', 'kdPenunjangDesc' => 'Lain-Lain'],
            ['kdPenunjangId' => '11', 'kdPenunjangDesc' => 'MRI'],
            ['kdPenunjangId' => '12', 'kdPenunjangDesc' => 'HEMODIALISA'],


        ],
        'assesmentPel' => [
            ['assesmentPelId' => '', 'assesmentPelDesc' => ''],
            ['assesmentPelId' => '1', 'assesmentPelDesc' => 'Poli spesialis tidak tersedia pada hari sebelumnya'],
            ['assesmentPelId' => '2', 'assesmentPelDesc' => 'Jam Poli telah berakhir pada hari sebelumnya'],
            ['assesmentPelId' => '3', 'assesmentPelDesc' => 'Dokter Spesialis yang dimaksud tidak praktek pada hari sebelumnya'],
            ['assesmentPelId' => '4', 'assesmentPelDesc' => 'Atas Instruksi RS'],
            ['assesmentPelId' => '5', 'assesmentPelDesc' => 'Tujuan Kontrol'],
        ],
    ];



    //  table LOV////////////////
    public $dataPasienLov = [];
    public $dataPasienLovStatus = 0;
    public $dataPasienLovSearch = '';

    public $dataDokterLov = [];
    public $dataDokterLovStatus = 0;
    public $dataDokterLovSearch = '';

    public $dataDokterBPJSLov = [];
    public $dataDokterBPJSLovStatus = 0;
    public $dataDokterBPJSLovSearch = '';

    public $dataRefBPJSLov = [];
    public $dataRefBPJSLovStatus = 0;
    public $dataRefBPJSLovSearch = '';


    public $formRujukanRefBPJS = [];
    public $formRujukanRefBPJSStatus = 0;
    public $formRujukanRefBPJSSearch = '';

    public $dataDiagnosaBPJSLov = [];
    public $dataDiagnosaBPJSLovStatus = 0;
    public $dataDiagnosaBPJSLovSearch = '';

    //////////////////////////////////////////////////////////////////////

    ////////////////////////////////////////////////
    // Lov Pasien //////////////////////
    ////////////////////////////////////////////////
    public function updateddataPasienlovsearch()
    {
        // Variable Search
        $search = $this->dataPasienLovSearch;

        // check LOV by id


        // Open Lov Pasien Status
        $this->dataPasienLovStatus = true;

        // if there is no id found and check (min 3 char on search)

        if (strlen($search) < 3) {
            $this->dataPasienLov = [];
        } else {


            // Proses Cari Data


            // 1.Cari berdasarkan reg_no  ->if null DB
            // 2.Cari berdasarkan nik ->if null DB
            // 3.Cari berdasarkan nokaBPJS ->if null DB
            // 4.Cari berdasarkan reg_name ->if null DB

            // 5. Goto Pasien Baru berdasarkan nik apiBPJS ->if null
            // 6. Entry Manual Pasien Baru

            // by reg_noxxx
            $cariDataPasienRegNo = $this->cariDataPasienByKeyArr('reg_no', $search);
            if ($cariDataPasienRegNo) {
                $this->dataPasienLov = $cariDataPasienRegNo;
            } else {
                // by nik
                $cariDataPasienNik = $this->cariDataPasienByKeyArr('nik_bpjs', $search);
                if ($cariDataPasienNik) {
                    $this->dataPasienLov = $cariDataPasienNik;
                } else {
                    // by nokaBPJS
                    $cariDataPasienNokaBpjs = $this->cariDataPasienByKeyArr('nokartu_bpjs', $search);
                    if ($cariDataPasienNokaBpjs) {
                        $this->dataPasienLov = $cariDataPasienNokaBpjs;
                    } else {
                        // by name
                        $cariDataPasienName = DB::table('rsmst_pasiens')
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
                            ->join('rsmst_propinsis', 'rsmst_propinsis.prop_id', 'rsmst_kabupatens.prop_id');


                        // myMultipleSearch by more than one table
                        $myMultipleSearch = explode('%', $search);

                        foreach ($myMultipleSearch as $key => $myMS) {
                            // key 0  mencari regno dan reg name
                            if ($key == 0) {
                                $cariDataPasienName->where(function ($cariDataPasienName) use ($myMS) {
                                    $cariDataPasienName
                                        ->where(DB::raw('upper(reg_no)'), 'like', '%' . strtoupper($myMS) . '%')
                                        ->orWhere(DB::raw('upper(reg_name)'), 'like', '%' . strtoupper($myMS) . '%');
                                });
                            }
                            // key 1  mencari alamat
                            if ($key == 1) {
                                $cariDataPasienName->where(function ($cariDataPasienName) use ($myMS) {
                                    $cariDataPasienName
                                        ->where(DB::raw('upper(address)'), 'like', '%' . strtoupper($myMS) . '%');
                                });
                            }
                        }

                        // limit 50 rec
                        $cariDataPasienName->orderBy('reg_name', 'desc')
                            ->limit(50);

                        $cariDataPasienName = json_decode($cariDataPasienName->get(), true);

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
    }
    // /////////////////////
    // LOV selected start
    public function setMydataPasienLov($id)
    {
        $this->emit('listenerRegNo', $id);
        $this->setDataPasien($id);

        $this->dataDaftarRi['regNo'] = $id;
        $this->dataPasienLovStatus = false;
        $this->dataPasienLov = [];
        $this->dataPasienLovSearch = $id;
    }
    ////////////////////////////////////////////////
    // Lov Pasien //////////////////////
    ////////////////////////////////////////////////

    ///////////cariDataPasienByKey/////////////////////////////////
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


    public function closeModal(): void
    {
        $this->isOpen = false;
        $this->isOpenMode = 'insert';

        $this->emit('ListenerisOpenUgd', ['isOpen' => $this->isOpen, 'isOpenMode' => $this->isOpenMode]);
    }


    /////////////////////////////////////////////////
    // Lov dataDokterRJ //////////////////////
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

        // check LOV by dr_id rs id
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
            $this->dataDaftarRi['drId'] = $dataDokter->dr_id;
            $this->dataDaftarRi['drDesc'] = $dataDokter->dr_name;

            $this->dataDaftarRi['poliId'] = $dataDokter->poli_id;
            $this->dataDaftarRi['poliDesc'] = $dataDokter->poli_desc;

            $this->dataDaftarRi['kddrbpjs'] = $dataDokter->kd_dr_bpjs;
            $this->dataDaftarRi['kdpolibpjs'] = $dataDokter->kd_poli_bpjs;

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

                        ->where('rsmst_doctors.active_status', '1')
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
            $this->dataDaftarRi['drId'] = '';
            $this->dataDaftarRi['drDesc'] = '';
            $this->dataDaftarRi['poliId'] = '';
            $this->dataDaftarRi['poliDesc'] = '';
            $this->dataDaftarRi['kddrbpjs'] = '';
            $this->dataDaftarRi['kdpolibpjs'] = '';
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
        $this->dataDaftarRi['drId'] = $dataDokter->dr_id;
        $this->dataDaftarRi['drDesc'] = $dataDokter->dr_name;

        $this->dataDaftarRi['poliId'] = $dataDokter->poli_id;
        $this->dataDaftarRi['poliDesc'] = $dataDokter->poli_desc;

        $this->dataDaftarRi['kddrbpjs'] = $dataDokter->kd_dr_bpjs;
        $this->dataDaftarRi['kdpolibpjs'] = $dataDokter->kd_poli_bpjs;

        $this->dataDokterLovStatus = false;
        $this->dataDokterLovSearch = '';
    }
    // LOV selected end
    /////////////////////////////////////////////////
    // Lov dataDokterRJ //////////////////////
    ////////////////////////////////////////////////



    public function store()
    {


        // dd($this->SEPJsonReq);
        // set data RJno / NoBooking / NoAntrian / klaimId / kunjunganId
        $this->setDataPrimer();
        // Validate RJ
        $this->validateDataRJ();

        if ($this->JenisKlaim['JenisKlaimId'] == 'JM') {
            $this->pushInsertSEP($this->SEPJsonReq);
        }


        // Logic insert and update mode start //////////
        if ($this->isOpenMode == 'insert') {

            // cari data berdasarkan regno pada tgl tsb dgn status selain 'F'
            // fungsi dasar untuk memperbaiki data EMR
            $cekDoblePendaftaran = DB::table('rsview_ugdkasir')
                ->where('rj_status', '!=', 'F')
                ->where(DB::raw("to_char(rj_date,'dd/mm/yyyy')"), '=', Carbon::createFromFormat('d/m/Y H:i:s', $this->dataDaftarRi['entryDate'], env('APP_TIMEZONE'))->format('d/m/Y'))
                ->where("reg_no", '=', $this->dataDaftarRi['regNo'])
                ->count();


            //cek Doble Pendaftaran
            if ($cekDoblePendaftaran > 0) {
                $this->emit('confirm_doble_recordRI', $this->dataDaftarRi['regNo'], $this->dataDaftarRi['regNo']);
            } else {
                $this->forceInsertRecord = '1';
            }


            // forceInsert record by default is flase true when cekDoblePendaftaran=0 or confirmation from user
            if ($this->forceInsertRecord) {
                $this->insertDataRJ();
                $this->isOpenMode = 'update';
            }
        } else if ($this->isOpenMode == 'update') {
            $this->updateDataRJ($this->dataDaftarRi['riHdrNo']);
        }




        // Opstional (Jika ingin fast Entry resert setelah proses diatas)
        // Jika ingin auto close resert dan close aktifkan
        // $this->resetInputFields();
        // $this->closeModal();

    }

    // set data RJno / NoBooking / NoAntrian / klaimId / kunjunganId
    private function setDataPrimer(): void
    {

        // Klaim & Kunjungan
        // dd($this->dataDaftarRi['klaimId']);
        $this->dataDaftarRi['klaimId'] = $this->JenisKlaim['JenisKlaimId'];
        $this->dataDaftarRi['kunjunganId'] = $this->JenisKunjungan['JenisKunjunganId'];

        $this->dataDaftarRi['entryId'] = $this->entryRi['entryId'];
        $this->dataDaftarRi['entryDesc'] = $this->entryRi['entryDesc'];

        // JenisKunjungan Internal
        if ($this->JenisKunjungan['JenisKunjunganId'] == 2) {
            $this->dataDaftarRi['kunjunganInternalStatus'] = "1";
        }

        // noBooking
        if (!$this->dataDaftarRi['noBooking']) {
            $this->dataDaftarRi['noBooking'] = Carbon::now(env('APP_TIMEZONE'))->format('YmdHis') . 'RSIM';
        }

        // riHdrNoMax
        if (!$this->dataDaftarRi['riHdrNo']) {
            $sql = "select nvl(max(rj_no)+1,1) riHdrNo_max from rstxn_ugdhdrs";
            $this->dataDaftarRi['riHdrNo'] = DB::scalar($sql);
        }

        // ermStatus
        if (!$this->dataDaftarRi['ermStatus']) {
            $this->dataDaftarRi['ermStatus'] = $this->dataDaftarRi['ermStatus'] ? $this->dataDaftarRi['ermStatus'] : 'A';
        }

        // noUrutAntrian (count all kecuali KRonis) if KR 999
        $sql = "select count(*) no_antrian
      from rstxn_ugdhdrs
      where dr_id=:drId
      and to_char(rj_date,'ddmmyyyy')=:tgl
      and klaim_id!='KR'";

        // Antrian ketika data antrian kosong
        if (!$this->dataDaftarRi['noAntrian']) {
            // proses antrian
            if ($this->dataDaftarRi['klaimId'] != 'KR') {
                $noUrutAntrian = DB::scalar($sql, [
                    "tgl" => Carbon::createFromFormat('d/m/Y H:i:s', $this->dataDaftarRi['entryDate'], env('APP_TIMEZONE'))->format('dmY'),
                    "drId" => $this->dataDaftarRi['drId']
                ]);

                $noAntrian = $noUrutAntrian + 1;
            } else {
                // Kronis
                $noAntrian = 999;
            }

            $this->dataDaftarRi['noAntrian'] = $noAntrian;
        }
    }

    private function setShiftnCurrentDate(): void
    {
        // dd/mm/yyyy hh24:mi:ss
        $this->dataDaftarRi['entryDate'] = $this->dataDaftarRi['entryDate'] ? $this->dataDaftarRi['entryDate'] : Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s');

        // shift
        $findShift = DB::table('rstxn_shiftctls')->select('shift')
            ->whereRaw("'" . Carbon::now(env('APP_TIMEZONE'))->format('H:i:s') . "' between
            shift_start and shift_end")
            ->first();
        $this->dataDaftarRi['shift'] = $this->dataDaftarRi['shift']
            ? $this->dataDaftarRi['shift']
            : (isset($findShift->shift) && $findShift->shift
                ? $findShift->shift
                : 3);
    }

    // validate Data RJ//////////////////////////////////////////////////
    private function validateDataRJ(): void
    {
        // customErrorMessages
        $messages = customErrorMessagesTrait::messages();

        // require nik ketika pasien tidak dikenal



        $rules = [

            "dataDaftarRi.regNo" => "bail|required|exists:rsmst_pasiens,reg_no",

            "dataDaftarRi.drId" => "required",
            "dataDaftarRi.drDesc" => "required",

            // "dataDaftarRi.poliId" => "required",
            // "dataDaftarRi.poliDesc" => "required",

            "dataDaftarRi.kddrbpjs" => '',
            "dataDaftarRi.kdpolibpjs" => '',


            "dataDaftarRi.entryDate" => "required",
            "dataDaftarRi.riHdrNo" => "required",

            "dataDaftarRi.shift" => "required",

            "dataDaftarRi.noAntrian" => "required",
            "dataDaftarRi.noBooking" => "required",

            "dataDaftarRi.slCodeFrom" => "required",
            "dataDaftarRi.passStatus" => "",

            "dataDaftarRi.rjStatus" => "required",
            "dataDaftarRi.txnStatus" => "required",
            "dataDaftarRi.ermStatus" => "required",

            "dataDaftarRi.cekLab" => "required",

            "dataDaftarRi.kunjunganInternalStatus" => "required",

            "dataDaftarRi.noReferensi" => "",

        ];

        // gabunga array noReferensi jika BPJS harus di isi
        if ($this->JenisKlaim['JenisKlaimId'] == 'JM') {
            $rules['dataDaftarRi.noReferensi'] =  'bail|min:3|max:19';
        } else {
            $rules['dataDaftarRi.noReferensi'] =  'bail|min:3|max:19';
        }

        // Proses Validasi///////////////////////////////////////////
        try {
            $this->validate($rules, $messages);
        } catch (\Illuminate\Validation\ValidationException $e) {

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Lakukan Pengecekan kembali Input Data Pasien." . json_encode($e->errors(), true));
            $this->validate($rules, $messages);
        }
    }

    private function insertDataRJ(): void
    {

        // insert into table transaksi
        DB::table('rstxn_ugdhdrs')->insert([
            'rj_no' => $this->dataDaftarRi['riHdrNo'],
            'rj_date' => DB::raw("to_date('" . $this->dataDaftarRi['entryDate'] . "','dd/mm/yyyy hh24:mi:ss')"),
            'reg_no' => $this->dataDaftarRi['regNo'],
            'nobooking' => $this->dataDaftarRi['noBooking'],
            'no_antrian' => $this->dataDaftarRi['noAntrian'],

            'klaim_id' => $this->dataDaftarRi['klaimId'],
            'entry_id' => $this->dataDaftarRi['entryId'],

            'poli_id' => $this->dataDaftarRi['poliId'],
            'dr_id' => $this->dataDaftarRi['drId'],
            'shift' => $this->dataDaftarRi['shift'],

            'death_on_igd_status' => 'N',
            'before_after' => 'B',
            'out_desc' => 'RAWAT',
            'status_lanjutan' => 'BS',



            'txn_status' => $this->dataDaftarRi['txnStatus'],
            'rj_status' => $this->dataDaftarRi['rjStatus'],
            'erm_status' => $this->dataDaftarRi['ermStatus'] ? $this->dataDaftarRi['ermStatus'] : 'A',

            'pass_status' => $this->dataDaftarRi['passStatus'] == 'N' ? 'N' : 'O', //Baru lama

            'cek_lab' => $this->dataDaftarRi['cekLab'],
            'sl_codefrom' => $this->dataDaftarRi['slCodeFrom'],
            'kunjungan_internal_status' => $this->dataDaftarRi['kunjunganInternalStatus'],
            'push_antrian_bpjs_status' => $this->HttpGetBpjsStatus, //status push antrian 200 /201/ 400
            'push_antrian_bpjs_json' => $this->HttpGetBpjsJson,  // response json
            // 'datadaftarUgd_json' => json_encode($this->dataDaftarRi, true),
            // 'datadaftarUgd_xml' => ArrayToXml::convert($this->dataDaftarRi),

            'waktu_masuk_pelayanan' => DB::raw("to_date('" . $this->dataDaftarRi['entryDate'] . "','dd/mm/yyyy hh24:mi:ss')"), //waktu masuk = rjdate

            'vno_sep' => isset($this->dataDaftarRi['sep']['noSep']) ? $this->dataDaftarRi['sep']['noSep'] : "",

        ]);

        $this->updateJsonRI($this->dataDaftarRi['riHdrNo'], $this->dataDaftarRi);

        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Data sudah tersimpan.");
    }

    private function updateDataRJ($riHdrNo): void
    {
        if ($riHdrNo !== $this->dataDaftarRi['riHdrNo']) {
            dd('Data Json Tidak sesuai' . $riHdrNo . '  /  ' . $this->dataDaftarRi['riHdrNo']);
        }
        // update table trnsaksi
        DB::table('rstxn_ugdhdrs')
            ->where('rj_no', $riHdrNo)
            ->update([
                // 'rj_no' => $this->dataDaftarRi['riHdrNo'],
                'rj_date' => DB::raw("to_date('" . $this->dataDaftarRi['entryDate'] . "','dd/mm/yyyy hh24:mi:ss')"),
                'reg_no' => $this->dataDaftarRi['regNo'],
                'nobooking' => $this->dataDaftarRi['noBooking'],
                'no_antrian' => $this->dataDaftarRi['noAntrian'],

                'klaim_id' => $this->dataDaftarRi['klaimId'],
                'entry_id' => $this->dataDaftarRi['entryId'],

                'poli_id' => $this->dataDaftarRi['poliId'],
                'dr_id' => $this->dataDaftarRi['drId'],
                'shift' => $this->dataDaftarRi['shift'],

                // <!-- 'death_on_igd_status' => 'N',
                // 'before_after' => 'B',
                // 'out_desc' => 'RAWAT',
                // 'status_lanjutan' => 'BS', -->

                'txn_status' => $this->dataDaftarRi['txnStatus'],
                'rj_status' => $this->dataDaftarRi['rjStatus'],
                'erm_status' => $this->dataDaftarRi['ermStatus'] ? $this->dataDaftarRi['ermStatus'] : 'A',

                'pass_status' => $this->dataDaftarRi['passStatus'] == 'N' ? 'N' : 'O', //Baru lama

                'cek_lab' => $this->dataDaftarRi['cekLab'],
                'sl_codefrom' => $this->dataDaftarRi['slCodeFrom'],
                'kunjungan_internal_status' => $this->dataDaftarRi['kunjunganInternalStatus'],
                'push_antrian_bpjs_status' => $this->HttpGetBpjsStatus, //status push antrian 200 /201/ 400
                'push_antrian_bpjs_json' => $this->HttpGetBpjsJson,  // response json
                // 'datadaftarUgd_json' => json_encode($this->dataDaftarRi, true),
                // 'datadaftarUgd_xml' => ArrayToXml::convert($this->dataDaftarRi),

                'waktu_masuk_pelayanan' => DB::raw("to_date('" . $this->dataDaftarRi['entryDate'] . "','dd/mm/yyyy hh24:mi:ss')"), //waktu masuk = rjdate

                'vno_sep' => isset($this->dataDaftarRi['sep']['noSep']) ? $this->dataDaftarRi['sep']['noSep'] : "",
            ]);

        $this->updateJsonRI($riHdrNo, $this->dataDaftarRi);


        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Data berhasil diupdate.");
    }


    public function setforceInsertRecord()
    {
        $this->forceInsertRecord = '1';

        if ($this->forceInsertRecord) {
            $this->insertDataRJ();
            $this->isOpenMode = 'update';
        }
    }




    public function resetInputFields(): void
    {

        // resert validation
        $this->resetValidation();
        // resert input
        $this->reset(
            [
                'dataPasienLovSearch',
                'isOpenMode',
                'forceInsertRecord',
                'dataPasien',
                'shiftRiRef',
                'statusRiRef',
                'drRiRef',
                'JenisKlaim',
                'JenisKunjungan',
                'dataDaftarRi',
                'HttpGetBpjsStatus',
                'HttpGetBpjsJson',
                'SEPJsonReq',
                'SEPQuestionnaire',
            ]
        );
        $this->emit('listenerRegNo', '');
        // $this->setDataPasien($this->dataDaftarRi['regNo']);
    }


    private function findData($riHdrNo): void
    {

        $this->dataDaftarRi = $this->findDataRI($riHdrNo);
        $this->emit('listenerRegNo', $this->dataDaftarRi['regNo']);
        $this->setDataPasien($this->dataDaftarRi['regNo']);

        // dd(isset($this->dataDaftarRi['klaimId']));
        if (!isset($this->dataDaftarRi['klaimId'])) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Data Klaim tidak ditemukan, Reset Data Ke UMUM");
        }
        if (!isset($this->dataDaftarRi['kunjunganId'])) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Data Kunjungan tidak ditemukan, Reset Data Ke FKTP");
        }

        $this->dataPasienLovSearch = $this->dataDaftarRi['regNo'];
        $this->JenisKlaim['JenisKlaimId'] = isset($this->dataDaftarRi['klaimId']) ? $this->dataDaftarRi['klaimId'] : "UM";
        $this->JenisKunjungan['JenisKunjunganId'] = isset($this->dataDaftarRi['kunjunganId']) ? $this->dataDaftarRi['kunjunganId'] : '1';
    }





    public function setShiftRJ($id, $desc): void
    {
        $this->dataDaftarRi['shift'] = $id;
    }



    public function clickrujukanPeserta(): void
    {
        // Cek jika bukan BPJS
        if ($this->JenisKlaim['JenisKlaimId'] != 'JM') {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Jenis Klaim ' . $this->JenisKlaim['JenisKlaimDesc']);
        } else {
            // Cek Apakah reqSep ada datanya apa blm
            // if (isset($this->dataDaftarRi['sep']['reqSep']['request']) && isset($this->dataDaftarRi['sep']['noSep'])) {
            if ($this->dataDaftarRi['sep']['noSep']) {

                $this->SEPJsonReq = $this->dataDaftarRi['sep']['reqSep'];
                // set formRujukanRefBPJSStatus true (open form)
                $this->formRujukanRefBPJSStatus = true;
            } else {

                $tanggal = Carbon::createFromFormat('d/m/Y H:i:s', $this->dataDaftarRi['entryDate'], env('APP_TIMEZONE'))->format('Y-m-d');
                $this->pesertaNomorKartu($this->dataPasien['pasien']['identitas']['idbpjs'], $tanggal);
            }
        }
    }


    private function pesertaNomorKartu($idBpjs, $tanggal): void
    {
        $HttpGetBpjs =  VclaimTrait::peserta_nomorkartu($idBpjs, $tanggal)->getOriginalContent();
        // metadata d kecil
        if ($HttpGetBpjs['metadata']['code'] == 200) {
            $peserta = $HttpGetBpjs['response'];
            $this->setSEPJsonReqUgd($peserta);
            $this->formRujukanRefBPJSStatus = true;
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess($HttpGetBpjs['metadata']['code'] . ' ' . $HttpGetBpjs['metadata']['message']);
        } else {
            $this->dataRefBPJSLovStatus = false;
            $this->dataRefBPJSLov = [];
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError($HttpGetBpjs['metadata']['code'] . ' ' . $HttpGetBpjs['metadata']['message']);
        }
    }


    private function setDataPasien($value): void
    {
        $findData = DB::table('rsmst_pasiens')
            ->select('meta_data_pasien_json')
            ->where('reg_no', $value)
            ->first();


        $meta_data_pasien_json = isset($findData->meta_data_pasien_json) ? $findData->meta_data_pasien_json : null;
        // if meta_data_pasien_json = null
        // then cari Data Pasien By Key Collection (exception when no data found)
        //
        // else json_decode
        if ($meta_data_pasien_json == null) {

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


    private function setSEPJsonReqUgd($dataRefPeserta): void
    {



        $this->SEPJsonReq = [
            "request" =>  [
                "t_sep" =>  [
                    "noKartu" => "" . $dataRefPeserta['peserta']['noKartu'] . "",
                    "tglSep" => "" . Carbon::createFromFormat('d/m/Y H:i:s', $this->dataDaftarRi['entryDate'], env('APP_TIMEZONE'))->format('Y-m-d') . "", //Y-m-d =tgl rj
                    "ppkPelayanan" => "0184R006", //ppk rs
                    "jnsPelayanan" => "2", // {jenis pelayanan = 1. r.inap 2. r.jalan}
                    "klsRawat" =>  [
                        "klsRawatHak" => "" . $dataRefPeserta['peserta']['hakKelas']['kode'] . "",
                        "klsRawatHakNama" => "" . $dataRefPeserta['peserta']['hakKelas']['keterangan'] . "",
                        "klsRawatNaik" => "", //{diisi jika naik kelas rawat, 1. VVIP, 2. VIP, 3. Kelas 1, 4. Kelas 2, 5. Kelas 3, 6. ICCU, 7. ICU, 8. Diatas Kelas 1}
                        "pembiayaan" => "", //{1. Pribadi, 2. Pemberi Kerja, 3. Asuransi Kesehatan Tambahan. diisi jika naik kelas rawat}
                        "penanggungJawab" => "", //{Contoh: jika pembiayaan 1 maka penanggungJawab=Pribadi. diisi jika naik kelas rawat}
                    ],
                    "noMR" => "" . $dataRefPeserta['peserta']['mr']['noMR'] . "",
                    "rujukan" =>  [
                        "asalRujukan" => "2", //{asal rujukan ->1.Faskes 1, 2. Faskes 2(RS)} Post inap asal rujukan 2
                        "asalRujukanNama" => "Faskes Tingkat 2 RS ", //{asal rujukan ->1.Faskes 1, 2. Faskes 2(RS)}
                        "tglRujukan" => "" . Carbon::createFromFormat('d/m/Y H:i:s', $this->dataDaftarRi['entryDate'], env('APP_TIMEZONE'))->format('Y-m-d') . "", //Y-m-d
                        "noRujukan" => "" . '' . "",
                        "ppkRujukan" => "" . '0184R006' . "", //{kode faskes rujukam -> baca di referensi faskes}
                        "ppkRujukanNama" => "" . 'MADINAH' . "", //{kode faskes rujukam -> baca di referensi faskes}
                    ],
                    "catatan" => "-",
                    "diagAwal" => "" . '' . "",
                    "diagAwalNama" => "" . '' . "",
                    "poli" =>  [
                        "tujuan" => "IGD" . '' . "", //Untuk Kunjungan Internal beda poli dgn rujukan
                        "tujuanNama" => "Instalasi Gawat Darurat" . '' . "",
                        "eksekutif" => "" . "0" . "", //{poli eksekutif -> 0. Tidak 1.Ya}
                        "eksekutifRef" =>  $this->SEPJsonReq['request']['t_sep']['poli']['eksekutifRef'], //{poli eksekutif -> 0. Tidak 1.Ya}
                    ],
                    "cob" =>  [
                        "cob" => "" . "0" . "", //{cob -> 0.Tidak 1. Ya}
                        "cobRef" => $this->SEPJsonReq['request']['t_sep']['cob']['cobRef'], //{cob -> 0.Tidak 1. Ya}

                    ],
                    "katarak" =>  [
                        "katarak" => "" . "0" . "", //{katarak --> 0.Tidak 1.Ya}
                        "katarakRef" =>  $this->SEPJsonReq['request']['t_sep']['katarak']['katarakRef'], //{katarak --> 0.Tidak 1.Ya}

                    ],

                    // fitur jaminan laka blm dikerjakan
                    "jaminan" =>  [
                        "lakaLantas" => "0", //" 0 : Bukan Kecelakaan lalu lintas [BKLL], 1 : KLL dan bukan kecelakaan Kerja [BKK], 2 : KLL dan KK, 3 : KK",
                        "noLP" => "",
                        "penjamin" =>  [
                            "tglKejadian" => "",
                            "keterangan" => "",
                            "suplesi" =>  [
                                "suplesi" => "0",
                                "noSepSuplesi" => "",
                                "lokasiLaka" =>  [
                                    "kdPropinsi" => "",
                                    "kdKabupaten" => "",
                                    "kdKecamatan" => "",
                                ]
                            ]
                        ]
                    ],
                    "tujuanKunj" => "0", //{"0": Normal,"1": Prosedur,"2": Konsul Dokter}
                    "tujuanKunjDesc" => "Normal",

                    "flagProcedure" => "",
                    "flagProcedureDesc" => "",

                    "kdPenunjang" => "",
                    "kdPenunjangDesc" => "",

                    "assesmentPel" => "",
                    "assesmentPelDesc" => "",
                    "skdp" =>  [
                        "noSurat" => "", //disi ketika wire model dan JenisKunjunganId == 3
                        "kodeDPJP" => "", //tidak di isi jika jenis kunjungan selain KONTROL
                    ],
                    "dpjpLayan" =>  "", //(tidak diisi jika jnsPelayanan = "1" (RANAP),
                    "dpjpLayanNama" => "", //(tidak diisi jika jnsPelayanan = "1" (RANAP),
                    "noTelp" => "" . $dataRefPeserta['peserta']['mr']['noTelepon'] . "",
                    "user" => "sirus App",
                ],
            ],
        ];
    }


    /////////////////////////////////////////////////
    // Lov dataDokterBPJSSEP //////////////////////
    ////////////////////////////////////////////////
    public function clickdataDokterBPJSlov()
    {
        $this->dataDokterBPJSLovStatus = true;
        $this->dataDokterBPJSLov = [];
    }

    public function updateddataDokterBPJSlovsearch()
    {
        // Variable Search
        $search = $this->dataDokterBPJSLovSearch;

        // check LOV by dr_id rs id
        $dataDokterBPJS = DB::table('rsmst_doctors')->select(
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

        if ($dataDokterBPJS) {
            // set dokter antrol
            $this->dataDaftarRi['drId'] = $dataDokterBPJS->dr_id;
            $this->dataDaftarRi['drDesc'] = $dataDokterBPJS->dr_name;

            $this->dataDaftarRi['poliId'] = $dataDokterBPJS->poli_id;
            $this->dataDaftarRi['poliDesc'] = $dataDokterBPJS->poli_desc;

            $this->dataDaftarRi['kddrbpjs'] = $dataDokterBPJS->kd_dr_bpjs;
            $this->dataDaftarRi['kdpolibpjs'] = $dataDokterBPJS->kd_poli_bpjs;

            // set dokter sep
            $this->SEPJsonReq['request']['t_sep']['dpjpLayan'] = $dataDokterBPJS->kd_dr_bpjs;
            $this->SEPJsonReq['request']['t_sep']['dpjpLayanNama'] = $dataDokterBPJS->dr_name;
            // $this->SEPJsonReq['request']['t_sep']['poli']['tujuan'] = $dataDokterBPJS->kd_poli_bpjs;
            // $this->SEPJsonReq['request']['t_sep']['poli']['tujuanNama'] = $dataDokterBPJS->poli_desc;


            $this->dataDokterBPJSLovStatus = false;
            $this->dataDokterBPJSLovSearch = '';
        } else {
            // if there is no id found and check (min 3 char on search)
            if (strlen($search) < 3) {
                $this->dataDokterBPJSLov = [];
            } else {
                $this->dataDokterBPJSLov = json_decode(
                    DB::table('rsmst_doctors')->select(
                        'rsmst_doctors.dr_id as dr_id',
                        'rsmst_doctors.dr_name as dr_name',
                        'kd_dr_bpjs',

                        'rsmst_polis.poli_id as poli_id',
                        'rsmst_polis.poli_desc as poli_desc',
                        'kd_poli_bpjs'

                    )
                        ->Join('rsmst_polis', 'rsmst_polis.poli_id', 'rsmst_doctors.poli_id')

                        ->where('rsmst_doctors.active_status', '1')
                        ->Where(DB::raw('upper(dr_name)'), 'like', '%' . strtoupper($search) . '%')
                        ->orWhere('poli_desc', 'like', '%' . strtoupper($search) . '%')
                        ->limit(10)
                        ->orderBy('dr_name', 'ASC')
                        ->orderBy('poli_desc', 'ASC')
                        ->get(),
                    true
                );
            }
            $this->dataDokterBPJSLovStatus = true;
            // set dokter antrol

            $this->dataDaftarRi['drId'] = '';
            $this->dataDaftarRi['drDesc'] = '';
            $this->dataDaftarRi['poliId'] = '';
            $this->dataDaftarRi['poliDesc'] = '';
            $this->dataDaftarRi['kddrbpjs'] = '';
            $this->dataDaftarRi['kdpolibpjs'] = '';

            // set dokter sep
            $this->SEPJsonReq['request']['t_sep']['dpjpLayan'] = '';
            $this->SEPJsonReq['request']['t_sep']['dpjpLayanNama'] = '';
            // $this->SEPJsonReq['request']['t_sep']['poli']['tujuan'] = '';
            // $this->SEPJsonReq['request']['t_sep']['poli']['tujuanNama'] = '';
        }
    }
    // /////////////////////
    // LOV selected start
    public function setMydataDokterBPJSLov($id, $name)
    {
        $dataDokterBPJS = DB::table('rsmst_doctors')->select(
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

        // set dokter antrol
        $this->dataDaftarRi['drId'] = $dataDokterBPJS->dr_id;
        $this->dataDaftarRi['drDesc'] = $dataDokterBPJS->dr_name;

        $this->dataDaftarRi['poliId'] = $dataDokterBPJS->poli_id;
        $this->dataDaftarRi['poliDesc'] = $dataDokterBPJS->poli_desc;

        $this->dataDaftarRi['kddrbpjs'] = $dataDokterBPJS->kd_dr_bpjs;
        $this->dataDaftarRi['kdpolibpjs'] = $dataDokterBPJS->kd_poli_bpjs;

        // set dokter sep
        $this->SEPJsonReq['request']['t_sep']['dpjpLayan'] = $dataDokterBPJS->kd_dr_bpjs;

        $this->SEPJsonReq['request']['t_sep']['kodeDPJP']['dpjpLayanNama'] = $dataDokterBPJS->dr_name;
        // $this->SEPJsonReq['request']['t_sep']['poli']['tujuan'] = $dataDokterBPJS->kd_poli_bpjs;
        // $this->SEPJsonReq['request']['t_sep']['poli']['tujuanNama'] = $dataDokterBPJS->poli_desc;


        $this->dataDokterBPJSLovStatus = false;
        $this->dataDokterBPJSLovSearch = '';
    }
    // LOV selected end
    /////////////////////////////////////////////////
    // Lov dataDokterRJ //////////////////////
    ////////////////////////////////////////////////


    /////////////////////////////////////////////////
    // Lov dataDiagnosaBPJSSEP //////////////////////
    ////////////////////////////////////////////////
    public function clickdataDiagnosaBPJSlov()
    {
        $this->dataDiagnosaBPJSLovStatus = true;
        $this->dataDiagnosaBPJSLov = [];
    }

    public function updateddataDiagnosaBPJSlovsearch()
    {
        // Variable Search
        $search = $this->dataDiagnosaBPJSLovSearch;

        // check LOV by dr_id rs id
        $dataDiagnosaBPJS = DB::table('rsmst_mstdiags')->select(
            'diag_id',
            'diag_desc',
            'icdx',
        )
            ->where('diag_id', $search)
            ->first();

        if ($dataDiagnosaBPJS) {

            // set dokter sep
            $this->SEPJsonReq['request']['t_sep']['diagAwal'] = $dataDiagnosaBPJS->icdx;
            $this->SEPJsonReq['request']['t_sep']['diagAwalNama'] = $dataDiagnosaBPJS->diag_desc;


            $this->dataDiagnosaBPJSLovStatus = false;
            $this->dataDiagnosaBPJSLovSearch = '';
        } else {
            // if there is no id found and check (min 3 char on search)
            if (strlen($search) < 3) {
                $this->dataDiagnosaBPJSLov = [];
            } else {
                $this->dataDiagnosaBPJSLov = json_decode(
                    DB::table('rsmst_mstdiags')->select(
                        'diag_id',
                        'diag_desc',
                        'icdx',
                    )

                        ->Where(DB::raw('upper(diag_desc)'), 'like', '%' . strtoupper($search) . '%')
                        ->orWhere(DB::raw('upper(icdx)'), 'like', '%' . strtoupper($search) . '%')
                        ->limit(10)
                        ->orderBy('diag_id', 'ASC')
                        ->orderBy('diag_desc', 'ASC')
                        ->get(),
                    true
                );
            }
            $this->dataDiagnosaBPJSLovStatus = true;
            // set dokter sep
            $this->SEPJsonReq['request']['t_sep']['diagAwal'] = '';
            $this->SEPJsonReq['request']['t_sep']['diagAwalNama'] = '';
        }
    }
    // /////////////////////
    // LOV selected start
    public function setMydataDiagnosaBPJSLov($id, $name)
    {
        $dataDiagnosaBPJS = DB::table('rsmst_mstdiags')->select(
            'diag_id',
            'diag_desc',
            'icdx',
        )
            ->where('diag_id', $id)
            ->first();

        // set dokter sep
        $this->SEPJsonReq['request']['t_sep']['diagAwal'] = $dataDiagnosaBPJS->icdx;
        $this->SEPJsonReq['request']['t_sep']['diagAwalNama'] = $dataDiagnosaBPJS->diag_desc;


        $this->dataDiagnosaBPJSLovStatus = false;
        $this->dataDiagnosaBPJSLovSearch = '';
    }
    // LOV selected end
    /////////////////////////////////////////////////
    // Lov dataDiagnosaRJ //////////////////////
    ////////////////////////////////////////////////



    public function storeDataSepReq(): void
    {
        // jika jenis klaim JM (BPJS)
        if ($this->JenisKlaim['JenisKlaimId'] == 'JM') {

            //tambah logic untuk antrol
            //jika 1 kunjungan awal set data noRujukan
            //jika 2 internal set data -----
            //jika 3 kontrol set data no SKDP
            //jika 4 kunjungan awal dari RS set data noRujukan

            $this->dataDaftarRi['noReferensi'] =
                ($this->JenisKunjungan['JenisKunjunganId'] == 1)
                ? (isset($this->SEPJsonReq['request']['t_sep']['rujukan']['noRujukan']) ? $this->SEPJsonReq['request']['t_sep']['rujukan']['noRujukan'] : "Data tidak dapat diproses")
                : (
                    ($this->JenisKunjungan['JenisKunjunganId'] == 2)
                    ? (isset($this->SEPJsonReq['request']['t_sep']['rujukan']['noRujukan']) ? substr($this->SEPJsonReq['request']['t_sep']['rujukan']['noRujukan'], 0, 16) : "Data tidak dapat diproses") . 'XXX'
                    : (
                        ($this->JenisKunjungan['JenisKunjunganId'] == 3)
                        ? (isset($this->SEPJsonReq['request']['t_sep']['skdp']['noSurat']) ? $this->SEPJsonReq['request']['t_sep']['skdp']['noSurat'] : "Data tidak dapat diproses")
                        : (
                            ($this->JenisKunjungan['JenisKunjunganId'] == 4)
                            ? (isset($this->SEPJsonReq['request']['t_sep']['rujukan']['noRujukan']) ? $this->SEPJsonReq['request']['t_sep']['rujukan']['noRujukan'] : "Data tidak dapat diproses")
                            : (isset($this->SEPJsonReq['request']['t_sep']['rujukan']['noRujukan']) ? $this->SEPJsonReq['request']['t_sep']['rujukan']['noRujukan'] : "Data tidak dapat diproses")
                        )
                    )
                );
        }
        // close Form
        $this->formRujukanRefBPJSStatus = false;

        $this->dataDaftarRi['sep']['reqSep'] = $this->SEPJsonReq;
    }


    private function pushInsertSEP($SEPJsonReq)
    {
        // if sep kosong
        if (!$this->dataDaftarRi['sep']['noSep']) {
            //ketika Push Tambah Antrean Berhasil buat SEP
            //////////////////////////////////////////////
            $HttpGetBpjs =  VclaimTrait::sep_insert($SEPJsonReq)->getOriginalContent();
            if ($HttpGetBpjs['metadata']['code'] == 200) {
                // dd($HttpGetBpjs);
                $this->dataDaftarRi['sep']['resSep'] = $HttpGetBpjs['response']['sep'];
                $this->dataDaftarRi['sep']['noSep'] = $HttpGetBpjs['response']['sep']['noSep'];

                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('SEP ' .  $HttpGetBpjs['metadata']['code'] . ' ' . $HttpGetBpjs['metadata']['message']);
            } else {
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('SEP ' . $HttpGetBpjs['metadata']['code'] . ' ' . $HttpGetBpjs['metadata']['message']);
            }
        }
        // response sep value
        //ketika Push Tambah Antrean Berhasil buat SEP
        //////////////////////////////////////////////
    }


    public function cetakSEP()
    {
        // cek BPJS atau bukan
        if ($this->JenisKlaim['JenisKlaimId'] != 'JM') {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Jenis Klaim ' . $this->JenisKlaim['JenisKlaimDesc']);
        } else {
            // cek ada resSep ada atau tidak
            if (!$this->dataDaftarRi['sep']['resSep']) {

                // Http cek sep by no SEP
                //////////////////////////////////////////////
                $HttpGetBpjs =  VclaimTrait::sep_nomor($this->dataDaftarRi['sep']['noSep'])->getOriginalContent();

                if ($HttpGetBpjs['metadata']['code'] == 200) {

                    $this->dataDaftarRi['sep']['resSep'] = $HttpGetBpjs['response'];
                    $this->dataDaftarRi['sep']['noSep'] = $HttpGetBpjs['response']['noSep'];

                    // update database
                    // DB::table('rstxn_rjhdrs')
                    //     ->where('rj_no', $this->dataDaftarRi['riHdrNo'])
                    //     ->update([
                    //         'datadaftarUgd_json' => json_encode($this->dataDaftarRi, true),
                    //         'datadaftarUgd_xml' => ArrayToXml::convert($this->dataDaftarRi),
                    //     ]);

                    $this->updateJsonRI($this->dataDaftarRi['riHdrNo'], $this->dataDaftarRi);



                    toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('CetakSEP ' .  $HttpGetBpjs['metadata']['code'] . ' ' . $HttpGetBpjs['metadata']['message']);
                } else {
                    toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('CetakSEP ' . $HttpGetBpjs['metadata']['code'] . ' ' . $HttpGetBpjs['metadata']['message']);
                }
            } else {

                // cetak PDF
                $data = [
                    'data' => $this->dataDaftarRi['sep']['resSep'],
                    'reqData' => $this->dataDaftarRi['sep']['reqSep'],

                ];
                $pdfContent = PDF::loadView('livewire.daftar-r-j.cetak-sep', $data)->output();
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('CetakSEP');

                return response()->streamDownload(
                    fn() => print($pdfContent),
                    "filename.pdf"
                );
            }
        }
    }


    public function mount()
    {

        // $this->setShiftnCurrentDate();
        if ($this->riHdrNo) {
            $this->findData($this->riHdrNo);
            $this->isOpenMode = 'update';
        }
    }

    public function render()
    {
        return view('livewire.daftar-r-i.form-entry-r-i.form-entry-r-i');
    }
}
