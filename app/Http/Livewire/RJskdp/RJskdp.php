<?php

namespace App\Http\Livewire\RJskdp;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

use App\Http\Traits\customErrorMessagesTrait;
use App\Http\Traits\BPJS\AntrianTrait;
use App\Http\Traits\BPJS\VclaimTrait;


use Illuminate\Support\Str;
use Spatie\ArrayToXml\ArrayToXml;


class RJskdp extends Component
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
    public $regNoRef = '060931Z';


    public $drRjRef = [
        'drId' => 'All',
        'drName' => 'All',
        'drOptions' => [
            [
                'drId' => 'All',
                'drName' => 'All'
            ]
        ]
    ];


    // dataDaftarPoliRJ RJ
    public $dataDaftarPoliRJ = [];


    //////////////////////////////////////////////////////////////////////





    // limit record per page -resetExcept////////////////
    public $limitPerPage = 10;



    // 

    //  modal status////////////////
    public $isOpen = 0;
    public $isOpenMode = 'insert';


    // search logic -resetExcept////////////////
    public $search;
    protected $queryString = [
        'search' => ['except' => '', 'as' => 'cariData'],
        'page' => ['except' => 1, 'as' => 'p'],
    ];



    // listener from blade////////////////
    protected $listeners = [
        'xxxxx' => 'xxxxx',
    ];



    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////





    // resert input private////////////////
    private function resetInputFields(): void
    {

        // resert validation
        $this->resetValidation();
        // resert input kecuali
        $this->resetExcept([
            'limitPerPage',
            'search',
            'drRjRef',
            'regNoRef'


        ]);
    }




    // open and close modal start////////////////

    private function openModalEdit(): void
    {
        $this->resetInputFields();
        $this->isOpen = true;
        $this->isOpenMode = 'update';
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



    private function optionsdrRjRef(): void
    {

        // Query
        $query = DB::table('rsview_rjkasir')
            ->select(
                'dr_id',
                'dr_name',
            )
            ->where('rj_status', '=', 'L')
            ->where('reg_no', '=', $this->regNoRef)

            ->groupBy('dr_id')
            ->groupBy('dr_name')
            ->orderBy('dr_name', 'desc')
            ->get();

        // loop and set Ref
        $query->each(function ($item, $key) {
            $this->drRjRef['drOptions'][$key + 1]['drId'] = $item->dr_id;
            $this->drRjRef['drOptions'][$key + 1]['drName'] = $item->dr_name;
        })->toArray();
    }





    /////////////////////////////////////////////////////////////////////
    // resert page pagination when coloumn search change ////////////////
    // tabular Ref topbar
    /////////////////////////////////////////////////////////////////////

    // search
    public function updatedSearch(): void
    {
        // $this->emit('toastr-error', "search.");

        $this->resetPage();
        $this->resetValidation();
        $this->resetInputFields();
    }

    // dr
    public function setdrRjRef($id, $name): void
    {
        // $this->emit('toastr-error', "dr.");

        $this->drRjRef['drId'] = $id;
        $this->drRjRef['drName'] = $name;
        $this->resetPage();
        $this->resetValidation();
        $this->resetInputFields();
    }
    /////////////////////////////////////////////////////////////////////






    // is going to edit data/////////////////
    public function edit($id)
    {
        $this->emit('toastr-error', "Pembuatan Data SKDP dalam proses pengerjaan.");

        // $this->openModalEdit();
        // $this->findData($id);
    }











    // ////////////////
    // RJ Logic
    // ////////////////

    //////////////////////////////////////////////
    // updated when change Record ////////////////
    ///////////////////////////////////////////////



    //////////////////////////////////////////////
    // updated when change Record ////////////////
    ///////////////////////////////////////////////




    // validate Data RJ//////////////////////////////////////////////////
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

            "dataDaftarPoliRJ.noReferensi" => "",

        ];

        // gabunga array noReferensi jika BPJS harus di isi
        if ($this->JenisKlaim['JenisKlaimId'] == 'JM') {
            $rules['dataDaftarPoliRJ.noReferensi'] =  'bail|required|min:3|max:19';
        } else {
            $rules['dataDaftarPoliRJ.noReferensi'] =  'bail|min:3|max:19';
        }

        // Proses Validasi///////////////////////////////////////////
        try {
            $this->validate($rules, $messages);
        } catch (\Illuminate\Validation\ValidationException $e) {

            $this->emit('toastr-error', "Lakukan Pengecekan kembali Input Data Pasien.");
            $this->validate($rules, $messages);
        }
    }


    // insert and update record start////////////////
    public function store()
    {
        // set data RJno / NoBooking / NoAntrian / klaimId / kunjunganId
        $this->setDataPrimer();

        // Validate RJ
        $this->validateDataRJ();

        // Logic update mode start //////////
        $this->updateDataRJ($this->dataDaftarPoliRJ['rjNo']);
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

                'vno_sep' => isset($this->dataDaftarPoliRJ['sep']['noSep']) ? $this->dataDaftarPoliRJ['sep']['noSep'] : "",
            ]);

        $this->emit('toastr-success', "Pembuatan SKDP berhasil disimpan.");
    }
    // insert and update record end////////////////


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
    ///////////cariDataPasienByKey/////////////////////////////////

    private function findData($rjno): void
    {

        $findData = DB::table('rsview_rjkasir')
            ->select('datadaftarpolirj_json', 'vno_sep')
            ->where('rj_no', $rjno)
            ->first();

        if ($findData->datadaftarpolirj_json) {
            $this->dataDaftarPoliRJ = json_decode($findData->datadaftarpolirj_json, true);

            // jika sep tidak ditemukan tambah variable sep pda array
            if (isset($this->dataDaftarPoliRJ['sep']) == false) {
                $this->dataDaftarPoliRJ['sep'] = [
                    "noSep" => $findData->vno_sep,
                    "reqSep" => [],
                    "resSep" => [],
                ];
            }

            $this->setDataPasien($this->dataDaftarPoliRJ['regNo']);

            $this->dataPasienLovSearch = $this->dataDaftarPoliRJ['regNo'];
            $this->JenisKlaim['JenisKlaimId'] = $this->dataDaftarPoliRJ['klaimId'];
            $this->JenisKunjungan['JenisKunjunganId'] = $this->dataDaftarPoliRJ['kunjunganId'];
        } else {

            $this->emit('toastr-error', "Data tidak dapat di proses.");
            $dataDaftarPoliRJ = DB::table('rsview_rjkasir')
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

                    'nobooking',
                    'push_antrian_bpjs_status',
                    'push_antrian_bpjs_json',
                    'kd_dr_bpjs',
                    'kd_poli_bpjs',
                    'rj_status',
                    'txn_status',
                    'erm_status',
                )
                ->where('rj_no', '=', $rjno)
                ->first();

            $this->dataDaftarPoliRJ = [
                "regNo" => "" . $dataDaftarPoliRJ->reg_no . "",

                "drId" => "" . $dataDaftarPoliRJ->dr_id . "",
                "drDesc" => "" . $dataDaftarPoliRJ->dr_name . "",

                "poliId" => "" . $dataDaftarPoliRJ->poli_id . "",
                "poliDesc" => "" . $dataDaftarPoliRJ->poli_desc . "",

                "kddrbpjs" => "" . $dataDaftarPoliRJ->kd_dr_bpjs . "",
                "kdpolibpjs" => "" . $dataDaftarPoliRJ->kd_poli_bpjs . "",

                "rjDate" => "" . $dataDaftarPoliRJ->rj_date . "",
                "rjNo" => "" . $dataDaftarPoliRJ->rj_no . "",
                "shift" => "" . $dataDaftarPoliRJ->shift . "",
                "noAntrian" => "" . $dataDaftarPoliRJ->no_antrian . "",
                "noBooking" => "" . $dataDaftarPoliRJ->nobooking . "",
                "slCodeFrom" => "02",
                "passStatus" => "",
                "rjStatus" => "" . $dataDaftarPoliRJ->rj_status . "",
                "txnStatus" => "" . $dataDaftarPoliRJ->txn_status . "",
                "ermStatus" => "" . $dataDaftarPoliRJ->erm_status . "",
                "cekLab" => "0",
                "kunjunganInternalStatus" => "0",
                "noReferensi" => "" . $dataDaftarPoliRJ->reg_no . "",
                "taskIdPelayanan" => [
                    "taskId1" => "",
                    "taskId2" => "",
                    "taskId3" => "" . $dataDaftarPoliRJ->rj_date . "",
                    "taskId4" => "",
                    "taskId5" => "",
                    "taskId6" => "",
                    "taskId7" => "",
                    "taskId99" => "",
                ],
                'sep' => [
                    "noSep" => "" . $dataDaftarPoliRJ->vno_sep . "",
                    "reqSep" => [],
                    "resSep" => [],
                ]
            ];

            $this->setDataPasien($this->dataDaftarPoliRJ['regNo']);
            $this->dataPasienLovSearch = $this->dataDaftarPoliRJ['regNo'];
            $this->JenisKlaim['JenisKlaimId'] = $dataDaftarPoliRJ->klaim_id == 'JM' ? 'JM' : 'UM';
            $this->JenisKlaim['JenisKlaimDesc'] = $dataDaftarPoliRJ->klaim_id == 'JM' ? 'BPJS' : 'UMUM';

            $this->JenisKunjungan['JenisKunjunganId'] = '1';
            $this->JenisKunjungan['JenisKunjunganDesc'] = 'Rujukan FKTP';
        }


        // 

        // return $findData;
    }

















    // when new form instance
    public function mount()
    {
        // set data dokter ref
        $this->optionsdrRjRef();
    }



    // select data start////////////////
    public function render()
    {
        // render drRjRef
        // set data dokter ref
        $this->optionsdrRjRef();

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
                'push_antrian_bpjs_json'
            )
            ->where('rj_status', '=', 'L')
            ->where('reg_no', '=', $this->regNoRef);



        //Jika where dokter tidak kosong
        if ($this->drRjRef['drId'] != 'All') {
            $query->where('dr_id', $this->drRjRef['drId']);
        }

        $query->where(function ($q) {
            $q->Where(DB::raw('upper(reg_name)'), 'like', '%' . strtoupper($this->search) . '%')
                ->orWhere(DB::raw('upper(reg_no)'), 'like', '%' . strtoupper($this->search) . '%')
                ->orWhere(DB::raw('upper(dr_name)'), 'like', '%' . strtoupper($this->search) . '%')
                ->orWhere(DB::raw('upper(poli_desc)'), 'like', '%' . strtoupper($this->search) . '%');
        })
            ->orderBy('rj_date1',  'desc')
            ->orderBy('dr_name',  'desc')
            ->orderBy('poli_desc',  'desc')
            ->orderBy('no_antrian',  'asc');

        ////////////////////////////////////////////////
        // end Query
        ///////////////////////////////////////////////


        return view(
            'livewire.r-jskdp.r-jskdp',
            [
                'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Data SKDP Pasien Rawat Jalan',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Pasien Rawat Jalan',
                'myLimitPerPages' => [5, 10, 15, 20, 100],
            ]
        );
    }
    // select data end////////////////


}
