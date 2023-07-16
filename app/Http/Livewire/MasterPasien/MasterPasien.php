<?php

namespace App\Http\Livewire\MasterPasien;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use Carbon\Carbon;


class MasterPasien extends Component
{
    use WithPagination;

    //  table data////////////////
    public $name, $province_id;
    public $dataPasien = [
        "pasien" => [
            "pasientidakdikenal" => "",  //status pasien tdak dikenal 0 false 1 true
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
                "jenisKelaminId" => 1,
                "jenisKelaminDesc" => "Laki-laki",
                "jenisKelaminOptions" => [
                    ["jenisKelaminId" => 0, "jenisKelaminDesc" => "Tidak diketaui"],
                    ["jenisKelaminId" => 1, "jenisKelaminDesc" => "Laki-laki"],
                    ["jenisKelaminId" => 2, "jenisKelaminDesc" => "Perempuan"],
                    ["jenisKelaminId" => 3, "jenisKelaminDesc" => "Tidak dapat di tentukan"],
                    ["jenisKelaminId" => 4, "jenisKelaminDesc" => "Tidak Mengisi"],
                ],
            ],
            "agama" => [ //harus diisi (save id+nama)
                "agamaId" => "1",
                "agamaDesc" => "Islam",
                "agamaOptions" => [
                    ["agamaId" => 1, "agamaDesc" => "Islam"],
                    ["agamaId" => 2, "agamaDesc" => "Kristen (Protestan)"],
                    ["agamaId" => 3, "agamaDesc" => "Katolik"],
                    ["agamaId" => 4, "agamaDesc" => "Hindu"],
                    ["agamaId" => 5, "agamaDesc" => "Budha"],
                    ["agamaId" => 6, "agamaDesc" => "Konghucu"],
                    ["agamaId" => 7, "agamaDesc" => "Penghayat"],
                    ["agamaId" => 8, "agamaDesc" => "Lain-lain"], //Free text
                ],
            ],
            "statusPerkawinan" => [ //harus diisi (save id)
                "statusPerkawinanId" => "1",
                "statusPerkawinanDesc" => "Belum Kawin",
                "statusPerkawinanOptions" => [
                    ["statusPerkawinanId" => 1, "statusPerkawinanDesc" => "Belum Kawin"],
                    ["statusPerkawinanId" => 2, "statusPerkawinanDesc" => "Kawin"],
                    ["statusPerkawinanId" => 3, "statusPerkawinanDesc" => "Cerai Hidup"],
                    ["statusPerkawinanId" => 4, "statusPerkawinanDesc" => "Cerai Mati"],
                ],
            ],
            "pendidikan" =>  [ //harus diisi (save id)
                "pendidikanId" => "3",
                "pendidikanDesc" => "SLTA Sederajat",
                "pendidikanOptions" => [
                    ["pendidikanId" => 0, "pendidikanDesc" => "Tidak Sekolah"],
                    ["pendidikanId" => 1, "pendidikanDesc" => "SD"],
                    ["pendidikanId" => 2, "pendidikanDesc" => "SLTP Sederajat"],
                    ["pendidikanId" => 3, "pendidikanDesc" => "SLTA Sederajat"],
                    ["pendidikanId" => 4, "pendidikanDesc" => "D1-D3"],
                    ["pendidikanId" => 5, "pendidikanDesc" => "D4"],
                    ["pendidikanId" => 6, "pendidikanDesc" => "S1"],
                    ["pendidikanId" => 7, "pendidikanDesc" => "S2"],
                    ["pendidikanId" => 8, "pendidikanDesc" => "S3"],
                ],
            ],
            "pekerjaan" => [ //harus diisi (save id)
                "pekerjaanId" => "4",
                "pekerjaanDesc" => "Pegawai Swasta/ Wiraswasta",
                "pekerjaanOptions" => [
                    ["pekerjaanId" => 0, "pekerjaanDesc" => "Tidak Bekerja"],
                    ["pekerjaanId" => 1, "pekerjaanDesc" => "PNS"],
                    ["pekerjaanId" => 2, "pekerjaanDesc" => "TNI/POLRI"],
                    ["pekerjaanId" => 3, "pekerjaanDesc" => "BUMN"],
                    ["pekerjaanId" => 4, "pekerjaanDesc" => "Pegawai Swasta/ Wiraswasta"],
                    ["pekerjaanId" => 5, "pekerjaanDesc" => "Lain-Lain"], //Free text
                ],
            ],
            "golonganDarah" => [ //harus diisi (save id+nama) (default Tidak Tahu)
                "golonganDarahId" => "13",
                "golonganDarahDesc" => "Tidak Tahu",
                "golonganDarahOptions" => [
                    ["golonganDarahId" => 1, "golonganDarahDesc" => "A"],
                    ["golonganDarahId" => 2, "golonganDarahDesc" => "B"],
                    ["golonganDarahId" => 3, "golonganDarahDesc" => "AB"],
                    ["golonganDarahId" => 4, "golonganDarahDesc" => "O"],
                    ["golonganDarahId" => 5, "golonganDarahDesc" => "A+"],
                    ["golonganDarahId" => 6, "golonganDarahDesc" => "A-"],
                    ["golonganDarahId" => 7, "golonganDarahDesc" => "B+"],
                    ["golonganDarahId" => 8, "golonganDarahDesc" => "B-"],
                    ["golonganDarahId" => 9, "golonganDarahDesc" => "AB+"],
                    ["golonganDarahId" => 10, "golonganDarahDesc" => "AB-"],
                    ["golonganDarahId" => 11, "golonganDarahDesc" => "O+"],
                    ["golonganDarahId" => 12, "golonganDarahDesc" => "O-"],
                    ["golonganDarahId" => 13, "golonganDarahDesc" => "Tidak Tahu"],
                    ["golonganDarahId" => 14, "golonganDarahDesc" => "O Rhesus"],
                    ["golonganDarahId" => 15, "golonganDarahDesc" => "#"],
                ],
            ],

            "kewarganegaraan" => 'INDONESIA', //Free text (defult INDONESIA)
            "suku" => 'Jawa', //Free text (defult Jawa)
            "bahasa" => 'Indonesia / Jawa', //Free text (defult Indonesia / Jawa)
            "status" => [
                "statusId" => "1",
                "statusDesc" => "Aktif / Hidup",
                "statusOptions" => [
                    ["statusId" => 0, "statusDesc" => "Tidak Aktif / Batal"],
                    ["statusId" => 1, "statusDesc" => "Aktif / Hidup"],
                    ["statusId" => 2, "statusDesc" => "Meninggal"],
                ]
            ],
            "domisil" => [
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
                "propinsiName" => "" //harus diisi (Kode data Kemendagri)

            ],
            "identitas" => [
                "samadgndomisil" => "", //status samadgn domisil 0 false 1 true (auto entry = domisil)
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
                "negara" => "ID" //harus diisi (ISO 3166) ID 	IDN 	360 	ISO 3166-2:ID 	.id
            ],
            "kontak" => [
                "kodenegara" => "62", //+(62) Indonesia 
                "nomerTelponSelulerPasien" => "", //+(kode negara) no telp
                "nomerTelponLain" => "" //+(kode negara) no telp
            ],
            "hubungan" => [
                "namaAyah" => "", //
                "kodenegaraAyah" => "62", //+(62) Indonesia 
                "nomerTelponSelulerAyah" => "", //+(kode negara) no telp
                "namaIbu" => "", //
                "kodenegaraIbu" => "62", //+(62) Indonesia 
                "nomerTelponSelulerIbu" => "", //+(kode negara) no telp

                "namaPenanggungJawab" => "", // di isi untuk pasien (Tidak dikenal / Hal Lain)
                "kodenegaraPenanggungJawab" => "62", //+(62) Indonesia 
                "nomerTelponSelulerPenanggungJawab" => "", //+(kode negara) no telp
                "hubunganDgnPasien" => [
                    "hubunganDgnPasienId" => 5, //Default 5 Kerabat / Saudara
                    "hubunganDgnPasienDesc" => "Kerabat / Saudara",
                    "hubunganDgnPasienOptions" => [
                        ["hubunganDgnPasienId" => 1, "hubunganDgnPasienDesc" => "Diri Sendiri"],
                        ["hubunganDgnPasienId" => 2, "hubunganDgnPasienDesc" => "Orang Tua"],
                        ["hubunganDgnPasienId" => 3, "hubunganDgnPasienDesc" => "Anak"],
                        ["hubunganDgnPasienId" => 4, "hubunganDgnPasienDesc" => "Suami / Istri"],
                        ["hubunganDgnPasienId" => 5, "hubunganDgnPasienDesc" => "Kerabaat / Saudara"],
                        ["hubunganDgnPasienId" => 6, "hubunganDgnPasienDesc" => "Lain-lain"] //Free text
                    ]
                ]
            ],

        ]
    ];

    // limit record per page -resetExcept////////////////
    public $limitPerPage = 10;

    //  table LOV////////////////
    public $jenisKelaminLov = [];
    public $jenisKelaminLovStatus = 0;
    public $jenisKelaminLovSearch = '';

    public $agamaLov = [];
    public $agamaLovStatus = 0;
    public $agamaLovSearch = '';

    public $statusPerkawinanLov = [];
    public $statusPerkawinanLovStatus = 0;
    public $statusPerkawinanLovSearch = '';

    public $pendidikanLov = [];
    public $pendidikanLovStatus = 0;
    public $pendidikanLovSearch = '';

    public $pekerjaanLov = [];
    public $pekerjaanLovStatus = 0;
    public $pekerjaanLovSearch = '';

    public $golonganDarahLov = [];
    public $golonganDarahLovStatus = 0;
    public $golonganDarahLovSearch = '';

    public $statusLov = [];
    public $statusLovStatus = 0;
    public $statusLovSearch = '';

    public $hubunganDgnPasienLov = [];
    public $hubunganDgnPasienLovStatus = 0;
    public $hubunganDgnPasienLovSearch = '';

    //  table LOV////////////////

    //  modal status////////////////
    public $isOpen = 0;
    public $isOpenMode = 'insert';
    public $tampilIsOpen = 0;


    // search logic -resetExcept////////////////
    public $search;
    protected $queryString = [
        'search' => ['except' => '', 'as' => 'cariData'],
        'page' => ['except' => 1, 'as' => 'p'],
    ];


    // sort logic -resetExcept////////////////
    public $sortField = 'reg_date';
    public $sortAsc = false;


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
        $this->reset([
            'name',
            'province_id',

            'isOpen',
            'tampilIsOpen',
            'isOpenMode'
        ]);
    }




    // open and close modal start////////////////
    private function openModal(): void
    {
        $this->resetInputFields();
        $this->isOpen = true;
        $this->isOpenMode = 'insert';
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
    public function setLimitPerPage($value)
    {
        $this->limitPerPage = $value;
    }




    // resert page pagination when coloumn search change ////////////////
    public function updatedSearch(): void
    {
        $this->resetPage();
    }




    // logic ordering record (shotby)////////////////
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortAsc = !$this->sortAsc;
        } else {
            $this->sortAsc = true;
        }

        $this->sortField = $field;
    }


    // logic LOV start////////////////
    // jenis kelamin LOV
    // nested
    // Datapasien->Pasien->Jeniskelamin->Jeniskelaminid

    // /////////Jeniskelamin////////////
    public function clickJeniskelaminlov()
    {
        $this->jenisKelaminLovStatus = true;
        $this->jenisKelaminLov = $this->dataPasien['pasien']['jenisKelamin']['jenisKelaminOptions'];
    }
    public function updatedJeniskelaminlovsearch()
    {
        // Variable Search
        $search = $this->jenisKelaminLovSearch;

        // check LOV by id 
        $Jeniskelamin = collect($this->dataPasien['pasien']['jenisKelamin']['jenisKelaminOptions'])
            ->where('jenisKelaminId', '=', $search)
            ->first();

        if ($Jeniskelamin) {
            $this->dataPasien['pasien']['jenisKelamin']['jenisKelaminId'] = $Jeniskelamin['jenisKelaminId'];
            $this->dataPasien['pasien']['jenisKelamin']['jenisKelaminDesc'] = $Jeniskelamin['jenisKelaminDesc'];
            $this->jenisKelaminLovStatus = false;
            $this->jenisKelaminLovSearch = '';
        } else {
            // if there is no id found and check (min 3 char on search)
            if (strlen($search) < 3) {
                $this->jenisKelaminLov = $this->dataPasien['pasien']['jenisKelamin']['jenisKelaminOptions'];
            } else {
                $this->jenisKelaminLov = collect($this->dataPasien['pasien']['jenisKelamin']['jenisKelaminOptions'])
                    ->filter(function ($item) use ($search) {
                        return false !== stristr($item['jenisKelaminDesc'], $search);
                    });
            }
            $this->jenisKelaminLovStatus = true;
            $this->dataPasien['pasien']['jenisKelamin']['jenisKelaminId'] = '';
            $this->dataPasien['pasien']['jenisKelamin']['jenisKelaminDesc'] = '';
        }
    }
    // /////////////////////
    // LOV selected start
    public function setMyjenisKelaminLov($id, $name)
    {
        $this->dataPasien['pasien']['jenisKelamin']['jenisKelaminId'] = $id;
        $this->dataPasien['pasien']['jenisKelamin']['jenisKelaminDesc'] = $name;
        $this->jenisKelaminLovStatus = false;
        $this->jenisKelaminLovSearch = '';
    }
    // LOV selected end
    // /////////////////////


    // /////////Agama////////////
    public function clickagamalov()
    {
        $this->agamaLovStatus = true;
        $this->agamaLov = $this->dataPasien['pasien']['agama']['agamaOptions'];
    }
    public function updatedagamalovsearch()
    {
        // Variable Search
        $search = $this->agamaLovSearch;

        // check LOV by id 
        $agama = collect($this->dataPasien['pasien']['agama']['agamaOptions'])
            ->where('agamaId', '=', $search)
            ->first();

        if ($agama) {
            $this->dataPasien['pasien']['agama']['agamaId'] = $agama['agamaId'];
            $this->dataPasien['pasien']['agama']['agamaDesc'] = $agama['agamaDesc'];
            $this->agamaLovStatus = false;
            $this->agamaLovSearch = '';
        } else {
            // if there is no id found and check (min 3 char on search)
            if (strlen($search) < 3) {
                $this->agamaLov = $this->dataPasien['pasien']['agama']['agamaOptions'];
            } else {
                $this->agamaLov = collect($this->dataPasien['pasien']['agama']['agamaOptions'])
                    ->filter(function ($item) use ($search) {
                        return false !== stristr($item['agamaDesc'], $search);
                    });
            }
            $this->agamaLovStatus = true;
            $this->dataPasien['pasien']['agama']['agamaId'] = '';
            $this->dataPasien['pasien']['agama']['agamaDesc'] = '';
        }
    }
    // /////////////////////
    // LOV selected start
    public function setMyagamaLov($id, $name)
    {
        $this->dataPasien['pasien']['agama']['agamaId'] = $id;
        $this->dataPasien['pasien']['agama']['agamaDesc'] = $name;
        $this->agamaLovStatus = false;
        $this->agamaLovSearch = '';
    }
    // LOV selected end
    // /////////////////////

    // /////////statusPerkawinan////////////
    public function clickstatusPerkawinanlov()
    {
        $this->statusPerkawinanLovStatus = true;
        $this->statusPerkawinanLov = $this->dataPasien['pasien']['statusPerkawinan']['statusPerkawinanOptions'];
    }
    public function updatedstatusPerkawinanlovsearch()
    {
        // Variable Search
        $search = $this->statusPerkawinanLovSearch;

        // check LOV by id 
        $statusPerkawinan = collect($this->dataPasien['pasien']['statusPerkawinan']['statusPerkawinanOptions'])
            ->where('statusPerkawinanId', '=', $search)
            ->first();

        if ($statusPerkawinan) {
            $this->dataPasien['pasien']['statusPerkawinan']['statusPerkawinanId'] = $statusPerkawinan['statusPerkawinanId'];
            $this->dataPasien['pasien']['statusPerkawinan']['statusPerkawinanDesc'] = $statusPerkawinan['statusPerkawinanDesc'];
            $this->statusPerkawinanLovStatus = false;
            $this->statusPerkawinanLovSearch = '';
        } else {
            // if there is no id found and check (min 3 char on search)
            if (strlen($search) < 3) {
                $this->statusPerkawinanLov = $this->dataPasien['pasien']['statusPerkawinan']['statusPerkawinanOptions'];
            } else {
                $this->statusPerkawinanLov = collect($this->dataPasien['pasien']['statusPerkawinan']['statusPerkawinanOptions'])
                    ->filter(function ($item) use ($search) {
                        return false !== stristr($item['statusPerkawinanDesc'], $search);
                    });
            }
            $this->statusPerkawinanLovStatus = true;
            $this->dataPasien['pasien']['statusPerkawinan']['statusPerkawinanId'] = '';
            $this->dataPasien['pasien']['statusPerkawinan']['statusPerkawinanDesc'] = '';
        }
    }
    // /////////////////////
    // LOV selected start
    public function setMystatusPerkawinanLov($id, $name)
    {
        $this->dataPasien['pasien']['statusPerkawinan']['statusPerkawinanId'] = $id;
        $this->dataPasien['pasien']['statusPerkawinan']['statusPerkawinanDesc'] = $name;
        $this->statusPerkawinanLovStatus = false;
        $this->statusPerkawinanLovSearch = '';
    }
    // LOV selected end
    // /////////////////////

    // /////////pendidikan////////////
    public function clickpendidikanlov()
    {
        $this->pendidikanLovStatus = true;
        $this->pendidikanLov = $this->dataPasien['pasien']['pendidikan']['pendidikanOptions'];
    }
    public function updatedpendidikanlovsearch()
    {
        // Variable Search
        $search = $this->pendidikanLovSearch;

        // check LOV by id 
        $pendidikan = collect($this->dataPasien['pasien']['pendidikan']['pendidikanOptions'])
            ->where('pendidikanId', '=', $search)
            ->first();

        if ($pendidikan) {
            $this->dataPasien['pasien']['pendidikan']['pendidikanId'] = $pendidikan['pendidikanId'];
            $this->dataPasien['pasien']['pendidikan']['pendidikanDesc'] = $pendidikan['pendidikanDesc'];
            $this->pendidikanLovStatus = false;
            $this->pendidikanLovSearch = '';
        } else {
            // if there is no id found and check (min 3 char on search)
            if (strlen($search) < 3) {
                $this->pendidikanLov = $this->dataPasien['pasien']['pendidikan']['pendidikanOptions'];
            } else {
                $this->pendidikanLov = collect($this->dataPasien['pasien']['pendidikan']['pendidikanOptions'])
                    ->filter(function ($item) use ($search) {
                        return false !== stristr($item['pendidikanDesc'], $search);
                    });
            }
            $this->pendidikanLovStatus = true;
            $this->dataPasien['pasien']['pendidikan']['pendidikanId'] = '';
            $this->dataPasien['pasien']['pendidikan']['pendidikanDesc'] = '';
        }
    }
    // /////////////////////
    // LOV selected start
    public function setMypendidikanLov($id, $name)
    {
        $this->dataPasien['pasien']['pendidikan']['pendidikanId'] = $id;
        $this->dataPasien['pasien']['pendidikan']['pendidikanDesc'] = $name;
        $this->pendidikanLovStatus = false;
        $this->pendidikanLovSearch = '';
    }
    // LOV selected end
    // /////////////////////

    // /////////pekerjaan////////////
    public function clickpekerjaanlov()
    {
        $this->pekerjaanLovStatus = true;
        $this->pekerjaanLov = $this->dataPasien['pasien']['pekerjaan']['pekerjaanOptions'];
    }
    public function updatedpekerjaanlovsearch()
    {
        // Variable Search
        $search = $this->pekerjaanLovSearch;

        // check LOV by id 
        $pekerjaan = collect($this->dataPasien['pasien']['pekerjaan']['pekerjaanOptions'])
            ->where('pekerjaanId', '=', $search)
            ->first();

        if ($pekerjaan) {
            $this->dataPasien['pasien']['pekerjaan']['pekerjaanId'] = $pekerjaan['pekerjaanId'];
            $this->dataPasien['pasien']['pekerjaan']['pekerjaanDesc'] = $pekerjaan['pekerjaanDesc'];
            $this->pekerjaanLovStatus = false;
            $this->pekerjaanLovSearch = '';
        } else {
            // if there is no id found and check (min 3 char on search)
            if (strlen($search) < 3) {
                $this->pekerjaanLov = $this->dataPasien['pasien']['pekerjaan']['pekerjaanOptions'];
            } else {
                $this->pekerjaanLov = collect($this->dataPasien['pasien']['pekerjaan']['pekerjaanOptions'])
                    ->filter(function ($item) use ($search) {
                        return false !== stristr($item['pekerjaanDesc'], $search);
                    });
            }
            $this->pekerjaanLovStatus = true;
            $this->dataPasien['pasien']['pekerjaan']['pekerjaanId'] = '';
            $this->dataPasien['pasien']['pekerjaan']['pekerjaanDesc'] = '';
        }
    }
    // /////////////////////
    // LOV selected start
    public function setMypekerjaanLov($id, $name)
    {
        $this->dataPasien['pasien']['pekerjaan']['pekerjaanId'] = $id;
        $this->dataPasien['pasien']['pekerjaan']['pekerjaanDesc'] = $name;
        $this->pekerjaanLovStatus = false;
        $this->pekerjaanLovSearch = '';
    }
    // LOV selected end
    // /////////////////////

    // /////////golonganDarah////////////
    public function clickgolonganDarahlov()
    {
        $this->golonganDarahLovStatus = true;
        $this->golonganDarahLov = $this->dataPasien['pasien']['golonganDarah']['golonganDarahOptions'];
    }
    public function updatedgolonganDarahlovsearch()
    {
        // Variable Search
        $search = $this->golonganDarahLovSearch;

        // check LOV by id 
        $golonganDarah = collect($this->dataPasien['pasien']['golonganDarah']['golonganDarahOptions'])
            ->where('golonganDarahId', '=', $search)
            ->first();

        if ($golonganDarah) {
            $this->dataPasien['pasien']['golonganDarah']['golonganDarahId'] = $golonganDarah['golonganDarahId'];
            $this->dataPasien['pasien']['golonganDarah']['golonganDarahDesc'] = $golonganDarah['golonganDarahDesc'];
            $this->golonganDarahLovStatus = false;
            $this->golonganDarahLovSearch = '';
        } else {
            // if there is no id found and check (min 3 char on search)
            if (strlen($search) < 3) {
                $this->golonganDarahLov = $this->dataPasien['pasien']['golonganDarah']['golonganDarahOptions'];
            } else {
                $this->golonganDarahLov = collect($this->dataPasien['pasien']['golonganDarah']['golonganDarahOptions'])
                    ->filter(function ($item) use ($search) {
                        return false !== stristr($item['golonganDarahDesc'], $search);
                    });
            }
            $this->golonganDarahLovStatus = true;
            $this->dataPasien['pasien']['golonganDarah']['golonganDarahId'] = '';
            $this->dataPasien['pasien']['golonganDarah']['golonganDarahDesc'] = '';
        }
    }
    // /////////////////////
    // LOV selected start
    public function setMygolonganDarahLov($id, $name)
    {
        $this->dataPasien['pasien']['golonganDarah']['golonganDarahId'] = $id;
        $this->dataPasien['pasien']['golonganDarah']['golonganDarahDesc'] = $name;
        $this->golonganDarahLovStatus = false;
        $this->golonganDarahLovSearch = '';
    }
    // LOV selected end
    // /////////////////////

    // /////////status////////////
    public function clickstatuslov()
    {
        $this->statusLovStatus = true;
        $this->statusLov = $this->dataPasien['pasien']['status']['statusOptions'];
    }
    public function updatedstatuslovsearch()
    {
        // Variable Search
        $search = $this->statusLovSearch;

        // check LOV by id 
        $status = collect($this->dataPasien['pasien']['status']['statusOptions'])
            ->where('statusId', '=', $search)
            ->first();

        if ($status) {
            $this->dataPasien['pasien']['status']['statusId'] = $status['statusId'];
            $this->dataPasien['pasien']['status']['statusDesc'] = $status['statusDesc'];
            $this->statusLovStatus = false;
            $this->statusLovSearch = '';
        } else {
            // if there is no id found and check (min 3 char on search)
            if (strlen($search) < 3) {
                $this->statusLov = $this->dataPasien['pasien']['status']['statusOptions'];
            } else {
                $this->statusLov = collect($this->dataPasien['pasien']['status']['statusOptions'])
                    ->filter(function ($item) use ($search) {
                        return false !== stristr($item['statusDesc'], $search);
                    });
            }
            $this->statusLovStatus = true;
            $this->dataPasien['pasien']['status']['statusId'] = '';
            $this->dataPasien['pasien']['status']['statusDesc'] = '';
        }
    }
    // /////////////////////
    // LOV selected start
    public function setMystatusLov($id, $name)
    {
        $this->dataPasien['pasien']['status']['statusId'] = $id;
        $this->dataPasien['pasien']['status']['statusDesc'] = $name;
        $this->statusLovStatus = false;
        $this->statusLovSearch = '';
    }
    // LOV selected end
    // /////////////////////

    // /////////hubunganDgnPasien////////////
    public function clickhubunganDgnPasienlov()
    {
        $this->hubunganDgnPasienLovStatus = true;
        $this->hubunganDgnPasienLov = $this->dataPasien['pasien']['hubungan']['hubunganDgnPasien']['hubunganDgnPasienOptions'];
    }
    public function updatedhubunganDgnPasienlovsearch()
    {
        // Variable Search
        $search = $this->hubunganDgnPasienLovSearch;

        // check LOV by id 
        $hubunganDgnPasien = collect($this->dataPasien['pasien']['hubungan']['hubunganDgnPasien']['hubunganDgnPasienOptions'])
            ->where('hubunganDgnPasienId', '=', $search)
            ->first();

        if ($hubunganDgnPasien) {
            $this->dataPasien['pasien']['hubungan']['hubunganDgnPasien']['hubunganDgnPasienId'] = $hubunganDgnPasien['hubunganDgnPasienId'];
            $this->dataPasien['pasien']['hubungan']['hubunganDgnPasien']['hubunganDgnPasienDesc'] = $hubunganDgnPasien['hubunganDgnPasienDesc'];
            $this->hubunganDgnPasienLovStatus = false;
            $this->hubunganDgnPasienLovSearch = '';
        } else {
            // if there is no id found and check (min 3 char on search)
            if (strlen($search) < 3) {
                $this->hubunganDgnPasienLov = $this->dataPasien['pasien']['hubungan']['hubunganDgnPasien']['hubunganDgnPasienOptions'];
            } else {
                $this->hubunganDgnPasienLov = collect($this->dataPasien['pasien']['hubungan']['hubunganDgnPasien']['hubunganDgnPasienOptions'])
                    ->filter(function ($item) use ($search) {
                        return false !== stristr($item['hubunganDgnPasienDesc'], $search);
                    });
            }
            $this->hubunganDgnPasienLovStatus = true;
            $this->dataPasien['pasien']['hubungan']['hubunganDgnPasien']['hubunganDgnPasienId'] = '';
            $this->dataPasien['pasien']['hubungan']['hubunganDgnPasien']['hubunganDgnPasienDesc'] = '';
        }
    }
    // /////////////////////
    // LOV selected start
    public function setMyhubunganDgnPasienLov($id, $name)
    {
        $this->dataPasien['pasien']['hubungan']['hubunganDgnPasien']['hubunganDgnPasienId'] = $id;
        $this->dataPasien['pasien']['hubungan']['hubunganDgnPasien']['hubunganDgnPasienDesc'] = $name;
        $this->hubunganDgnPasienLovStatus = false;
        $this->hubunganDgnPasienLovSearch = '';
    }
    // LOV selected end
    // /////////////////////

    // logic LOV end




    // is going to insert data////////////////
    public function create()
    {
        $this->openModal();
    }



    // insert record start////////////////
    public function store()
    {

        $customErrorMessages = [
            'dataPasien.pasien.regNo.required' => 'No Rekamedis tidak boleh kosong',
            'dataPasien.pasien.regName.required' => 'Nama Pasien tidak boleh kosong'
        ];

        $this->validate([
            'dataPasien.pasien.regNo' => 'required',
            'dataPasien.pasien.regName' => 'required'
        ], $customErrorMessages);

        // Province::updateOrCreate(['id' => $this->province_id], [
        //     'name' => $this->name
        // ]);


        $this->closeModal();
        $this->resetInputFields();
        $this->emit('toastr-success', "Data " . $this->name . " berhasil disimpan.");
    }
    // insert record end////////////////



    // Find data from table start////////////////
    private function findData($value)
    {
        $findData = Province::findOrFail($value);
        return $findData;
    }
    // Find data from table end////////////////



    // show edit record start////////////////
    public function edit($id)
    {
        $this->openModalEdit();

        $province = $this->findData($id);
        $this->province_id = $id;
        $this->name = $province->name;
    }
    // show edit record end////////////////



    // tampil record start////////////////
    public function tampil($id)
    {
        $this->openModalTampil();

        $province = $this->findData($id);
        $this->province_id = $id;
        $this->name = $province->name;
    }
    // tampil record end////////////////



    // delete record start////////////////
    public function delete($id, $name)
    {
        Province::find($id)->delete();
        $this->emit('toastr-success', "Hapus data " . $name . " berhasil.");
    }
    // delete record end////////////////



    // select data start////////////////
    public function render()
    {
        return view(
            'livewire.master-pasien.master-pasien',
            [
                'pasiens' => DB::table('rsmst_pasiens')
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
                        'des_id',
                        'rt',
                        'rw',
                        'kec_id',
                        'kab_id',
                        'prop_id',
                        'phone'
                    )
                    ->where('reg_name', 'like', '%' . strtoupper($this->search) . '%')
                    ->orWhere('reg_no', 'like', '%' . strtoupper($this->search) . '%')
                    ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
                    ->paginate($this->limitPerPage),
                'myTitle' => 'Master Pasien',
                'mySnipt' => 'Tambah Data Pasien',
                'myProgram' => 'Pasien',
                'myLimitPerPages' => [5, 10, 15, 20, 100]
            ]
        );
    }
    // select data end////////////////
}
