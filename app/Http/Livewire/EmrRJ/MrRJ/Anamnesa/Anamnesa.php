<?php

namespace App\Http\Livewire\EmrRJ\MrRJ\Anamnesa;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

use Spatie\ArrayToXml\ArrayToXml;
use App\Http\Traits\EmrRJ\EmrRJTrait;
use App\Http\Traits\LOV\LOVSnomed\LOVSnomedTrait;

class Anamnesa extends Component
{
    use WithPagination, EmrRJTrait, LOVSnomedTrait;

    // listener from blade////////////////
    protected $listeners = [
        'syncronizeAssessmentPerawatRJFindData' => 'mount',

        'lovRJKeluhanUtama'           => 'setlovRJKeluhanUtama',
        'lovRJRiwayatPenyakitSekarangUmum'  => 'setlovRJRiwayatPenyakitSekarangUmum',
        'lovRJRiwayatPenyakitDahulu'  => 'setlovRJRiwayatPenyakitDahulu',
        'lovRJAlergi'                 => 'setlovRJAlergi',
    ];



    //////////////////////////////
    // Ref on top bar
    //////////////////////////////



    // dataDaftarPoliRJ RJ
    public $rjNoRef;

    public array $dataDaftarPoliRJ = [];

    public  array $dataPasien =
    [
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


    public array $rekonsiliasiObat = ["namaObat" => "", "dosis" => "", "rute" => ""];

    public array $anamnesa =
    [
        "pengkajianPerawatanTab" => "Pengkajian Perawatan",
        "pengkajianPerawatan" => [
            "perawatPenerima" => "",
            "jamDatang" => "",
            // "caraMasukRj" => "",
            // "caraMasukRjDesc" => "",
            // "caraMasukRjOption" => [
            //     ["caraMasukRj" => "Sendiri"],
            //     ["caraMasukRj" => "Rujuk"],
            //     ["caraMasukRj" => "Kasus Polisi"],
            // ],

            // "tingkatKegawatan" => "",
            // "tingkatKegawatanOption" => [
            //     ["tingkatKegawatan" => "P1"],
            //     ["tingkatKegawatan" => "P2"],
            //     ["tingkatKegawatan" => "P3"],
            //     ["tingkatKegawatan" => "P0"],
            // ],
        ],

        "keluhanUtamaTab" => "Keluhan Utama",
        "keluhanUtama" => [
            "keluhanUtama" => ""
        ],

        "anamnesaDiperolehTab" => "Anamnesa Diperoleh",
        "anamnesaDiperoleh" => [
            "autoanamnesa" => [],
            "allonanamnesa" => [],
            "anamnesaDiperolehDari" => ""
        ],

        "riwayatPenyakitSekarangUmumTab" => "Riwayat Penyakit Sekarang (Umum)",
        "riwayatPenyakitSekarangUmum" => [
            "riwayatPenyakitSekarangUmum" => ""
        ],

        "riwayatPenyakitDahuluTab" => "Riwayat Penyakit (Dahulu)",
        "riwayatPenyakitDahulu" => [
            "riwayatPenyakitDahulu" => ""
        ],

        "alergiTab" => "Alergi",
        "alergi" => [
            "alergi" => ""
        ],

        "rekonsiliasiObatTab" => "Rekonsiliasi Obat",
        "rekonsiliasiObat" => [],

        "lainLainTab" => "lain-Lain",
        "lainLain" => [
            "merokok" => [],
            "terpaparRokok" => []

        ],

        "faktorResikoTab" => "Faktor Resiko",
        "faktorResiko" => [
            "hipertensi" => [],
            "diabetesMelitus" => [],
            "penyakitJantung" => [],
            "asma" => [],
            "stroke" => [],
            "liver" => [],
            "tuberculosisParu" => [],
            "rokok" => [],
            "minumAlkohol" => [],
            "ginjal" => [],
            "lainLain" => ""
        ],

        "penyakitKeluargaTab" => "Riwayat Penyakit Keluarga",
        "penyakitKeluarga" => [
            "hipertensi" => [],
            "diabetesMelitus" => [],
            "penyakitJantung" => [],
            "asma" => [],
            "lainLain" => ""
        ],

        "statusFungsionalTab" => "Status Fungsional",
        "statusFungsional" => [
            "tongkat" => [],
            "kursiRoda" => [],
            "brankard" => [],
            "walker" => [],
            "lainLain" => ""
        ],

        "cacatTubuhTab" => "Cacat Tubuh",
        "cacatTubuh" => [
            "cacatTubuh" => [],
            "sebutCacatTubuh" => ""
        ],

        "statusPsikologisTab" => "Status Psikologis",
        "statusPsikologis" => [
            "tidakAdaKelainan" => [],
            "marah" => [],
            "ccemas" => [],
            "takut" => [],
            "sedih" => [],
            "cenderungBunuhDiri" => [],
            "sebutstatusPsikologis" => ""
        ],

        "statusMentalTab" => "Status Mental",
        "statusMental" => [
            "statusMental" => "",
            "statusMentalOption" => [
                ["statusMental" => "Sadar dan Orientasi Baik"],
                ["statusMental" => "Ada Masalah Perilaku"],
                ["statusMental" => "Perilaku Kekerasan yang dialami sebelumnya"],
            ],
            "keteranganStatusMental" => "",
        ],

        "hubunganDgnKeluargaTab" => "Sosial",
        "hubunganDgnKeluarga" => [
            "hubunganDgnKeluarga" => "",
            "hubunganDgnKeluargaOption" => [
                ["hubunganDgnKeluarga" => "Baik"],
                ["hubunganDgnKeluarga" => "Tidak Baik"],
            ],
        ],

        "tempatTinggalTab" => "Tempat Tinggal",
        "tempatTinggal" => [
            "tempatTinggal" => "",
            "tempatTinggalOption" => [
                ["tempatTinggal" => "Rumah"],
                ["tempatTinggal" => "Panti"],
                ["tempatTinggal" => "Lain-lain"],
            ],
            "keteranganTempatTinggal" => ""

        ],

        "spiritualTab" => "Spiritual",
        "spiritual" => [
            "spiritual" => "Islam",
            "ibadahTeratur" => "",
            "ibadahTeraturOptions" => [
                ["ibadahTeratur" => "Ya"],
                ["ibadahTeratur" => "Tidak"],
            ],

            "nilaiKepercayaan" => "",
            "nilaiKepercayaanOptions" => [
                ["nilaiKepercayaan" => "Ya"],
                ["nilaiKepercayaan" => "Tidak"],
            ],

            "keteranganSpiritual" => ""

        ],

        "ekonomiTab" => "Ekonomi",
        "ekonomi" => [
            "pengambilKeputusan" => "Ayah",
            "pekerjaan" => "Swasta",
            "penghasilanBln" => "",
            "penghasilanBlnOptions" => [
                ["penghasilanBln" => "< 5Jt"],
                ["penghasilanBln" => "5Jt - 10Jt"],
                ["penghasilanBln" => ">10Jt"],

            ],
            "keteranganEkonomi" => ""

        ],

        "edukasiTab" => "Edukasi",
        "edukasi" => [
            "pasienKeluargaMenerimaInformasi" => "",
            "pasienKeluargaMenerimaInformasiOptions" => [
                ["pasienKeluargaMenerimaInformasi" => "Ya"],
                ["pasienKeluargaMenerimaInformasi" => "Tidak"],
            ],

            "hambatanEdukasi" => "",
            "keteranganHambatanEdukasi" => "",
            "hambatanEdukasiOptions" => [
                ["hambatanEdukasi" => "Ya"],
                ["hambatanEdukasi" => "Tidak"],
            ],

            "penerjemah" => "",
            "keteranganPenerjemah" => "",
            "penerjemahOptions" => [
                ["penerjemah" => "Ya"],
                ["penerjemah" => "Tidak"],
            ],

            "diagPenyakit" => [],
            "obat" => [],
            "dietNutrisi" => [],
            "rehabMedik" => [],
            "managemenNyeri" => [],
            "penggunaanAlatMedis" => [],
            "hakKewajibanPasien" => [],

            "edukasiFollowUp" => "",
            "segeraKembaliRjjika" => "",
            "informedConsent" => "", //uploadfile pdf dll
            "keteranganEdukasi" => ""
        ],

        "screeningGiziTab" => "Screening Gizi",
        "screeningGizi" => [
            "perubahanBB3Bln" => "",
            "perubahanBB3BlnScore" => "0",
            "perubahanBB3BlnOptions" => [
                ["perubahanBB3Bln" => "Ya (1)"],
                ["perubahanBB3Bln" => "Tidak (0)"],

            ],

            "jmlPerubahabBB" => "",
            "jmlPerubahabBBScore" => "0",
            "jmlPerubahabBBOptions" => [
                ["jmlPerubahabBB" => "0,5Kg-1Kg (1)"],
                ["jmlPerubahabBB" => ">5Kg-10Kg (2)"],
                ["jmlPerubahabBB" => ">10Kg-15Kg (3)"],
                ["jmlPerubahabBB" => ">15Kg-20Kg (4)"],
            ],

            "intakeMakanan" => "",
            "intakeMakananScore" => "0",
            "intakeMakananOptions" => [
                ["intakeMakanan" => "Ya (1)"],
                ["intakeMakanan" => "Tidak (0)"],

            ],
            "keteranganScreeningGizi" => "",
            "scoreTotalScreeningGizi" => "0",
            "tglScreeningGizi" => ""
        ],

        "batukTab" => "Batuk",
        "batuk" => [
            "riwayatDemam" => [],
            "keteranganRiwayatDemam" => "",

            "berkeringatMlmHari" => [],
            "keteranganBerkeringatMlmHari" => "",

            "bepergianDaerahWabah" => [],
            "keteranganBepergianDaerahWabah" => "",

            "riwayatPakaiObatJangkaPanjangan" => [],
            "keteranganRiwayatPakaiObatJangkaPanjangan" => "",

            "BBTurunTanpaSebab" => [],
            "keteranganBBTurunTanpaSebab" => "",

            "pembesaranGetahBening" => [],
            "keteranganPembesaranGetahBening" => "",

        ],
    ];
    //////////////////////////////////////////////////////////////////////


    protected $rules = [

        'dataDaftarPoliRJ.anamnesa.pengkajianPerawatan.jamDatang' => 'required|date_format:d/m/Y H:i:s',

    ];



    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////
    public function updated($propertyName)
    {
        if ($propertyName != 'dataSnomedLovSearch') {
            $this->validateOnly($propertyName);
            $this->store();
        }

        // LOV: cek apakah properti yang ter-`updated` adalah salah satu LOV key
        $lovConfig = collect($this->LOVParent)->firstWhere('field', $propertyName);
        $field = $lovConfig['field'] ?? null;
        if ($field) {
            $this->LOVParentStatus = $field;
        }
    }

    /////////////////////////////////////
    // lov
    public string $lovRJKeluhanUtama = '';
    public string $lovRJRiwayatPenyakitSekarangUmum = '';
    public string $lovRJRiwayatPenyakitDahulu = '';
    public string $lovRJAlergi = '';

    public function updatedLovRJKeluhanUtama(string $value): void
    {
        $this->dataSnomedLovSearch = $value;
        $this->updateddataSnomedLovsearch();
    }

    public function updatedLovRJRiwayatPenyakitSekarangUmum(string $value): void
    {
        $this->dataSnomedLovSearch = $value;
        $this->updateddataSnomedLovsearch();
    }

    public function updatedLovRJRiwayatPenyakitDahulu(string $value): void
    {
        $this->dataSnomedLovSearch = $value;
        $this->updateddataSnomedLovsearch();
    }

    public function updatedLovRJAlergi(string $value): void
    {
        $this->dataSnomedLovSearch = $value;
        $this->updateddataSnomedLovsearch();
    }

    public function setlovRJKeluhanUtama($snomedCode, $snomedDisplay): void
    {
        // Synk Lov Snomed
        $this->dataDaftarPoliRJ['anamnesa']['keluhanUtama']['snomedCode'] = $snomedCode ?? '';
        $this->dataDaftarPoliRJ['anamnesa']['keluhanUtama']['snomedDisplay'] = $snomedDisplay ?? '';
        $this->store();
        $this->resetdataSnomedLov();
    }

    public function setlovRJRiwayatPenyakitSekarangUmum($snomedCode, $snomedDisplay): void
    {
        // Synk Lov Snomed
        $this->dataDaftarPoliRJ['anamnesa']['riwayatPenyakitSekarangUmum']['snomedCode'] = $snomedCode ?? '';
        $this->dataDaftarPoliRJ['anamnesa']['riwayatPenyakitSekarangUmum']['snomedDisplay'] = $snomedDisplay ?? '';
        $this->store();
        $this->resetdataSnomedLov();
    }

    public function setlovRJRiwayatPenyakitDahulu($snomedCode, $snomedDisplay): void
    {
        // Synk Lov Snomed
        $this->dataDaftarPoliRJ['anamnesa']['riwayatPenyakitDahulu']['snomedCode'] = $snomedCode ?? '';
        $this->dataDaftarPoliRJ['anamnesa']['riwayatPenyakitDahulu']['snomedDisplay'] = $snomedDisplay ?? '';
        $this->store();
        $this->resetdataSnomedLov();
    }

    public function setlovRJAlergi($snomedCode, $snomedDisplay): void
    {
        // Synk Lov Snomed
        $this->formEntryAlergiSnomed['snomedCode'] = $snomedCode ?? '';
        $this->formEntryAlergiSnomed['snomedDisplay'] = $snomedDisplay ?? '';
        //jangan disimpan dulu simpan setelah addDataAlergi
        $this->resetdataSnomedLov();
    }

    public function resetLovRJKeluhanUtama(): void
    {
        $this->dataDaftarPoliRJ['anamnesa']['keluhanUtama']['snomedCode'] = '';
        $this->dataDaftarPoliRJ['anamnesa']['keluhanUtama']['snomedDisplay'] =  '';
        $this->store();
        $this->resetdataSnomedLov();
        $this->resetAllLovs();
    }

    public function resetLovRJRiwayatPenyakitSekarangUmum(): void
    {
        $this->dataDaftarPoliRJ['anamnesa']['riwayatPenyakitSekarangUmum']['snomedCode'] = '';
        $this->dataDaftarPoliRJ['anamnesa']['riwayatPenyakitSekarangUmum']['snomedDisplay'] =  '';
        $this->store();
        $this->resetdataSnomedLov();
        $this->resetAllLovs();
    }

    public function resetLovRJRiwayatPenyakitDahulu(): void
    {
        $this->dataDaftarPoliRJ['anamnesa']['riwayatPenyakitDahulu']['snomedCode'] = '';
        $this->dataDaftarPoliRJ['anamnesa']['riwayatPenyakitDahulu']['snomedDisplay'] =  '';
        $this->store();
        $this->resetdataSnomedLov();
        $this->resetAllLovs();
    }

    public function resetLovRJAlergi(): void
    {
        $this->formEntryAlergiSnomed['snomedCode'] =  '';
        $this->formEntryAlergiSnomed['snomedDisplay'] = '';
        //jangan disimpan dulu simpan setelah removeDataAlergi
        $this->resetdataSnomedLov();
        $this->resetAllLovs();
    }

    public function resetAllLovs(): void
    {
        $this->reset([
            'lovRJKeluhanUtama',
            'lovRJRiwayatPenyakitSekarangUmum',
            'lovRJRiwayatPenyakitDahulu',
            'lovRJAlergi',

            'formEntryAlergiSnomed',
        ]);
        $this->resetdataSnomedLov();
    }

    /////////////////////////////////////
    // lov

    public array $formEntryAlergiSnomed = [
        'snomedCode' => '',
        'snomedDisplay' => '',
    ];
    public function addAlergiSnomed(): void
    {
        $rules = [
            'formEntryAlergiSnomed.snomedCode'    => 'required|string|regex:/^\d+$/',
            'formEntryAlergiSnomed.snomedDisplay' => 'required|string',
        ];

        $messages = [
            'formEntryAlergiSnomed.snomedCode.required'    => 'Kode SNOMED untuk alergi wajib diisi.',
            'formEntryAlergiSnomed.snomedCode.string'      => 'Kode SNOMED harus berupa teks.',
            'formEntryAlergiSnomed.snomedCode.regex'       => 'Kode SNOMED hanya boleh berisi angka.',
            'formEntryAlergiSnomed.snomedDisplay.required' => 'Deskripsi alergi (display) wajib diisi.',
            'formEntryAlergiSnomed.snomedDisplay.string'   => 'Deskripsi alergi harus berupa teks.',
        ];

        // Proses validasi
        try {
            $this->validate($rules, $messages);
        } catch (\Illuminate\Validation\ValidationException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Periksa kembali input data. " . $e->getMessage());
            $this->validate($rules, $messages);
        }

        // start:
        try {
            $this->dataDaftarPoliRJ['anamnesa']['alergi']['alergiSnomed'][] = [
                'snomedCode' => $this->formEntryAlergiSnomed['snomedCode'],
                'snomedDisplay' => $this->formEntryAlergiSnomed['snomedDisplay']
            ];
            $this->store();
            $this->resetAllLovs();
            //
        } catch (\Exception $e) {
            // display an error to user
            dd($e->getMessage());
        }
        // goto start;
    }

    public function removeAlergiSnomed($index)
    {
        $newList = collect($this->dataDaftarPoliRJ['anamnesa']['alergi']['alergiSnomed'])
            ->except($index)
            ->values()
            ->all();

        // Simpan kembali ke model
        $this->dataDaftarPoliRJ['anamnesa']['alergi']['alergiSnomed'] = $newList;

        $this->store();
    }





    // resert input private////////////////
    // private function resetInputFields(): void
    // {

    //     // resert validation
    //     $this->resetValidation();
    //     // resert input kecuali
    //     $this->reset(['collectingMySnomed']);
    // }





    // ////////////////
    // RJ Logic
    // ////////////////


    // validate Data RJ//////////////////////////////////////////////////
    private function validateDataAnamnesaRj(): void
    {
        // customErrorMessages
        // $messages = customErrorMessagesTrait::messages();
        $messages = [];


        // $rules = [];



        // Proses Validasi///////////////////////////////////////////
        try {
            $this->validate($this->rules, $messages);
        } catch (\Illuminate\Validation\ValidationException $e) {

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Lakukan Pengecekan kembali Input Data.");
            $this->validate($this->rules, $messages);
        }
    }


    // insert and update record start////////////////
    public function store()
    {
        // Validate RJ
        $this->validateDataAnamnesaRj();

        // Logic update mode start //////////
        $this->updateDataRj($this->dataDaftarPoliRJ['rjNo']);

        // Update data Alergi ke master Pasien
        $this->updateDataPasien($this->dataDaftarPoliRJ['regNo']);


        $this->emit('syncronizeAssessmentPerawatRJFindData');
    }

    private function updateDataRj($rjNo): void
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

        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Anamnesa berhasil disimpan.");
    }
    // insert and update record end////////////////


    private function findData($rjno): void
    {


        $findDataRJ = $this->findDataRJ($rjno);

        $this->dataDaftarPoliRJ  = $findDataRJ['dataDaftarRJ'];

        // jika anamnesa tidak ditemukan tambah variable anamnesa pda array
        if (isset($this->dataDaftarPoliRJ['anamnesa']) == false) {
            $this->dataDaftarPoliRJ['anamnesa'] = $this->anamnesa;
        }

        // menyamakan Variabel
        $this->matchingMyVariable();
    }



    public function addRekonsiliasiObat()
    {
        if ($this->rekonsiliasiObat['namaObat']) {

            // check exist
            $cekRekonsiliasiObat = collect($this->dataDaftarPoliRJ['anamnesa']['rekonsiliasiObat'])
                ->where("namaObat", '=', $this->rekonsiliasiObat['namaObat'])
                ->count();

            if (!$cekRekonsiliasiObat) {
                $this->dataDaftarPoliRJ['anamnesa']['rekonsiliasiObat'][] = [
                    "namaObat" => $this->rekonsiliasiObat['namaObat'],
                    "dosis" => $this->rekonsiliasiObat['dosis'],
                    "rute" => $this->rekonsiliasiObat['rute']
                ];

                $this->store();
                // reset rekonsiliasiObat
                $this->reset(['rekonsiliasiObat']);
            } else {
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Nama Obat Sudah ada.");
            }
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Nama Obat Kosong.");
        }
    }

    public function removeRekonsiliasiObat($namaObat)
    {

        $rekonsiliasiObat = collect($this->dataDaftarPoliRJ['anamnesa']['rekonsiliasiObat'])->where("namaObat", '!=', $namaObat)->toArray();
        $this->dataDaftarPoliRJ['anamnesa']['rekonsiliasiObat'] = $rekonsiliasiObat;
        $this->store();
    }


    private function matchingMyVariable()
    {

        // keluhanUtama
        $this->dataDaftarPoliRJ['anamnesa']['keluhanUtama']['keluhanUtama'] =
            ($this->dataDaftarPoliRJ['anamnesa']['keluhanUtama']['keluhanUtama'])
            ? $this->dataDaftarPoliRJ['anamnesa']['keluhanUtama']['keluhanUtama']
            : ((isset($this->dataDaftarPoliRJ['screening']['keluhanUtama']) && !$this->dataDaftarPoliRJ['anamnesa']['keluhanUtama']['keluhanUtama'])
                ? $this->dataDaftarPoliRJ['screening']['keluhanUtama']
                : "");

        // Alergi from master Pasien
        $this->setDataPasien($this->dataDaftarPoliRJ['regNo']);
        // Alergi from master Pasien dan update data alergi ketika ada update terbaru pada funct updateDataPasien
        $this->updateDataPasien($this->dataDaftarPoliRJ['regNo']);


        $this->dataDaftarPoliRJ['anamnesa']['alergi']['alergi'] =
            ($this->dataDaftarPoliRJ['anamnesa']['alergi']['alergi'])
            ? $this->dataDaftarPoliRJ['anamnesa']['alergi']['alergi']
            : (($this->dataPasien['pasien']['alergi'] != $this->dataDaftarPoliRJ['anamnesa']['alergi']['alergi'])
                ? $this->dataPasien['pasien']['alergi']
                : $this->dataDaftarPoliRJ['anamnesa']['alergi']['alergi']);
        // Alergi from master Pasien
    }


    public function setJamDatang($myTime)
    {
        $this->dataDaftarPoliRJ['anamnesa']['pengkajianPerawatan']['jamDatang'] = $myTime;
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
            if ($findData) {
                $this->dataPasien['pasien']['regDate'] = $findData->reg_date;
                $this->dataPasien['pasien']['regNo'] = $findData->reg_no;
                $this->dataPasien['pasien']['regName'] = $findData->reg_name;
                $this->dataPasien['pasien']['identitas']['idbpjs'] = $findData->nokartu_bpjs;
                $this->dataPasien['pasien']['identitas']['nik'] = $findData->nik_bpjs;
                $this->dataPasien['pasien']['jenisKelamin']['jenisKelaminId'] = ($findData->sex == 'L') ? 1 : 2;
                $this->dataPasien['pasien']['jenisKelamin']['jenisKelaminDesc'] = ($findData->sex == 'L') ? 'Laki-laki' : 'Perempuan';
                $this->dataPasien['pasien']['tglLahir'] = $findData->birth_date ? $findData->birth_date : Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y');
                $this->dataPasien['pasien']['thn'] = Carbon::createFromFormat('d/m/Y', $this->dataPasien['pasien']['tglLahir'])->diff(Carbon::now(env('APP_TIMEZONE')))->format('%y Thn, %m Bln %d Hr'); //$findData->thn;
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
            } else {
                // when no data found
                $this->dataPasien['pasien']['regDate'] = '-';
                $this->dataPasien['pasien']['regNo'] = '-';
                $this->dataPasien['pasien']['regName'] = '-';
                $this->dataPasien['pasien']['identitas']['idbpjs'] = '-';
                $this->dataPasien['pasien']['identitas']['nik'] = '-';
                $this->dataPasien['pasien']['jenisKelamin']['jenisKelaminId'] = '-';
                $this->dataPasien['pasien']['jenisKelamin']['jenisKelaminDesc'] = '-';
                $this->dataPasien['pasien']['tglLahir'] = '-';
                $this->dataPasien['pasien']['thn'] = '-';
                $this->dataPasien['pasien']['bln'] = '-';
                $this->dataPasien['pasien']['hari'] = '-';
                $this->dataPasien['pasien']['tempatLahir'] = '-';
                $this->dataPasien['pasien']['golonganDarah']['golonganDarahId'] = '-';
                $this->dataPasien['pasien']['golonganDarah']['golonganDarahDesc'] = '-';
                $this->dataPasien['pasien']['statusPerkawinan']['statusPerkawinanId'] = '-';
                $this->dataPasien['pasien']['statusPerkawinan']['statusPerkawinanDesc'] = '-';

                $this->dataPasien['pasien']['agama']['agamaId'] = '-';
                $this->dataPasien['pasien']['agama']['agamaDesc'] = '-';

                $this->dataPasien['pasien']['pendidikan']['pendidikanId'] = '-';
                $this->dataPasien['pasien']['pendidikan']['pendidikanDesc'] = '-';

                $this->dataPasien['pasien']['pekerjaan']['pekerjaanId'] = '-';
                $this->dataPasien['pasien']['pekerjaan']['pekerjaanDesc'] = '-';


                $this->dataPasien['pasien']['hubungan']['namaPenanggungJawab'] = '-';
                $this->dataPasien['pasien']['hubungan']['namaIbu'] = '-';

                $this->dataPasien['pasien']['identitas']['nik'] = '-';
                $this->dataPasien['pasien']['identitas']['idBpjs'] = '-';


                $this->dataPasien['pasien']['identitas']['alamat'] = '-';

                $this->dataPasien['pasien']['identitas']['desaId'] = '-';
                $this->dataPasien['pasien']['identitas']['desaName'] = '-';

                $this->dataPasien['pasien']['identitas']['rt'] = '-';
                $this->dataPasien['pasien']['identitas']['rw'] = '-';
                $this->dataPasien['pasien']['identitas']['kecamatanId'] = '-';
                $this->dataPasien['pasien']['identitas']['kecamatanName'] = '-';

                $this->dataPasien['pasien']['identitas']['kotaId'] = '-';
                $this->dataPasien['pasien']['identitas']['kotaName'] = '-';

                $this->dataPasien['pasien']['identitas']['propinsiId'] = '-';
                $this->dataPasien['pasien']['identitas']['propinsiName'] = '-';

                $this->dataPasien['pasien']['kontak']['nomerTelponSelulerPasien'] = '-';

                $this->dataPasien['pasien']['hubungan']['namaPenanggungJawab'] = '-';
                $this->dataPasien['pasien']['hubungan']['namaIbu'] = '-';
            }
        } else {
            // ubah data Pasien
            $this->dataPasien = json_decode($findData->meta_data_pasien_json, true);
            // replace thn to age
            $this->dataPasien['pasien']['thn'] = Carbon::createFromFormat('d/m/Y', $this->dataPasien['pasien']['tglLahir'])->diff(Carbon::now(env('APP_TIMEZONE')))->format('%y Thn, %m Bln %d Hr'); //$findData->thn;
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

    private function updateDataPasien($regNo): void
    {
        // Jika Alergi belum ada pada master Pasien
        if (!isset($this->dataPasien['pasien']['alergi'])) {
            $this->dataPasien['pasien']['alergi'] = "";
        }

        // Update data Alergi ke master Pasien
        if (($this->dataPasien['pasien']['alergi']) != $this->dataDaftarPoliRJ['anamnesa']['alergi']['alergi']) {
            $this->dataPasien['pasien']['alergi'] = $this->dataDaftarPoliRJ['anamnesa']['alergi']['alergi'];

            DB::table('rsmst_pasiens')->where('reg_no', $regNo)
                ->update([
                    'meta_data_pasien_json' => json_encode($this->dataPasien, true),
                    'meta_data_pasien_xml' => ArrayToXml::convert($this->dataPasien)
                ]);

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Data Alergi " . $this->dataPasien['pasien']['regName'] . " berhasil diupdate.");
        }
    }

    private function validatePerawatPenerima()
    {
        // Validasi dulu
        $messages = [];
        $myRules = ['dataDaftarPoliRJ.pemeriksaan.tandaVital.gda' => '',];
        // Proses Validasi///////////////////////////////////////////
        try {
            $this->validate($myRules, $messages);
        } catch (\Illuminate\Validation\ValidationException $e) {

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Anda tidak dapat melakukan TTD-E karena data pemeriksaan belum lengkap.");
            $this->validate($myRules, $messages);
        }
        // Validasi dulu
    }

    public function setPerawatPenerima()
    {
        // $myRoles = json_decode(auth()->user()->roles, true);
        $myUserCodeActive = auth()->user()->myuser_code;
        $myUserNameActive = auth()->user()->myuser_name;
        // $myUserTtdActive = auth()->user()->myuser_ttd_image;

        // Validasi dulu
        // cek apakah data pemeriksaan sudah dimasukkan atau blm
        $this->validatePerawatPenerima();
        //  toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError( "Role " . $myUserNameActive);
        if (auth()->user()->hasRole('Perawat')) {
            $this->dataDaftarPoliRJ['anamnesa']['pengkajianPerawatan']['perawatPenerima'] = $myUserNameActive;
            $this->dataDaftarPoliRJ['anamnesa']['pengkajianPerawatan']['perawatPenerimaCode'] = $myUserCodeActive;
            $this->store();
        } else {

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Anda tidak dapat melakukan TTD-E karena User Role " . $myUserNameActive . ' Bukan Perawat.');
        }
    }

    // when new form instance
    public function mount()
    {
        $this->findData($this->rjNoRef);
    }





    // select data start////////////////
    public function render()
    {
        return view(
            'livewire.emr-r-j.mr-r-j.anamnesa.anamnesa',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Anamnesa',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Pasien Rawat Jalan',
            ]
        );
    }
    // select data end////////////////


}
