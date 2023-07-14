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
                "jenisKelaminId" => "",
                "jenisKelaminDesc" => "",
                "jenisKelaminOptions" => [
                    ["jenisKelaminId" => 0, "jenisKelaminDesc" => "Tidak diketaui"],
                    ["jenisKelaminId" => 1, "jenisKelaminDesc" => "Laki-laki"],
                    ["jenisKelaminId" => 2, "jenisKelaminDesc" => "Perempuan"],
                    ["jenisKelaminId" => 3, "jenisKelaminDesc" => "Tidak dapat di tentukan"],
                    ["jenisKelaminId" => 4, "jenisKelaminDesc" => "Tidak Mengisi"],
                ],
            ],
            "agama" => [ //harus diisi (save id+nama)
                "agamaId" => "",
                "agamaDesc" => "",
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
                "statusPerkawinanId" => "",
                "statusPerkawinanDesc" => "",
                "statusPerkawinanOptions" => [
                    ["statusPerkawinanId" => 1, "statusPerkawinanDesc" => "Belum Kawin"],
                    ["statusPerkawinanId" => 2, "statusPerkawinanDesc" => "Kawin"],
                    ["statusPerkawinanId" => 3, "statusPerkawinanDesc" => "Cerai Hidup"],
                    ["statusPerkawinanId" => 4, "statusPerkawinanDesc" => "Cerai Mati"],
                ],
            ],
            "pendidikan" =>  [ //harus diisi (save id)
                "pendidikanId" => "",
                "pendidikanDesc" => "",
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
                "pekerjaanId" => "",
                "pekerjaanDesc" => "",
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
                "golonganDarahId" => "",
                "golonganDarahDesc" => "",
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
                "statusId" => "",
                "statusDesc" => "",
                "statusOption" => [
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
                "desa" => "", //harus diisi (Kode data Kemendagri)
                "kecamatan" => "", //harus diisi (Kode data Kemendagri)
                "kota" => "", //harus diisi (Kode data Kemendagri)
                "propinsi" => "", //harus diisi (Kode data Kemendagri)
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
                "desa" => "", //harus diisi (Kode data Kemendagri)
                "kecamatan" => "", //harus diisi (Kode data Kemendagri)
                "kota" => "", //harus diisi (Kode data Kemendagri)
                "propinsi" => "", //harus diisi (Kode data Kemendagri)
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
    public $limitPerPage = 5;



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
