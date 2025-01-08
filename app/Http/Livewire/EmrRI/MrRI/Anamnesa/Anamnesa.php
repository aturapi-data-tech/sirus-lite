<?php

namespace App\Http\Livewire\EmrRI\MrRI\Anamnesa;

use Livewire\Component;
use Livewire\WithPagination;

use App\Http\Traits\EmrRI\EmrRITrait;
use App\Http\Traits\customErrorMessagesTrait;

class Anamnesa extends Component
{
    use WithPagination, EmrRITrait, customErrorMessagesTrait;

    // listener from blade////////////////
    protected $listeners = [
        'syncronizeAssessmentPerawatRIFindData' => 'mount'
    ];



    //////////////////////////////
    // Ref on top bar
    //////////////////////////////



    // dataDaftarRi RJ
    public $riHdrNoRef;

    public array $dataDaftarRi = [];

    public array $rekonsiliasiObat = ["namaObat" => "", "dosis" => "", "rute" => ""];

    public array $anamnesa =
    [
        "pengkajianPerawatanTab" => "Pengkajian Perawatan",
        "pengkajianPerawatan" => [
            "perawatPenerima" => "",
            "jamDatang" => "",
            "caraMasukIgd" => "",
            "caraMasukIgdDesc" => "",
            "caraMasukIgdOption" => [
                ["caraMasukIgd" => "Sendiri"],
                ["caraMasukIgd" => "Rujuk"],
                ["caraMasukIgd" => "Kasus Polisi"],
            ],

            "tingkatKegawatan" => "",
            "tingkatKegawatanOption" => [
                ["tingkatKegawatan" => "P1"],
                ["tingkatKegawatan" => "P2"],
                ["tingkatKegawatan" => "P3"],
                ["tingkatKegawatan" => "P0"],
            ],
            "saranaTransportasiId" => "4",
            "saranaTransportasiDesc" => "Lain-lain",
            "saranaTransportasiKet" => "",
            "saranaTransportasiOptions" => [
                ["saranaTransportasiId" => "1", "saranaTransportasiDesc" => "Ambulans"],
                ["saranaTransportasiId" => "2", "saranaTransportasiDesc" => "Mobil"],
                ["saranaTransportasiId" => "3", "saranaTransportasiDesc" => "Motor"],
                ["saranaTransportasiId" => "4", "saranaTransportasiDesc" => "Lain-lain"],
            ],
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
            "segeraKembaliIGDjika" => "",
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
        'dataDaftarRi.anamnesa.pengkajianPerawatan.jamDatang' => 'required|date_format:d/m/Y H:i:s',
        'dataDaftarRi.anamnesa.pengkajianPerawatan.caraMasukIgd' => 'required',
        'dataDaftarRi.anamnesa.pengkajianPerawatan.tingkatKegawatan' => 'required',
        'dataDaftarRi.anamnesa.keluhanUtama.keluhanUtama' => 'required',
    ];



    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////
    public function updated($propertyName)
    {
        // dd($propertyName);
        $this->validateOnly($propertyName);
        $this->store();
    }




    // resert input private////////////////
    private function resetInputFields(): void
    {

        // resert validation
        $this->resetValidation();
        // resert input kecuali
        $this->reset(['']);
    }





    // ////////////////
    // RJ Logic
    // ////////////////


    // validate Data RJ//////////////////////////////////////////////////
    private function validateDataAnamnesadRi(): void
    {
        // customErrorMessages
        // $messages = customErrorMessagesTrait::messages();
        $messages = [];

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
        $this->validateDataAnamnesadRi();

        // Logic update mode start //////////
        $this->updateDataRi($this->dataDaftarRi['riHdrNo']);

        $this->emit('syncronizeAssessmentPerawatRIFindData');
    }

    private function updateDataRi($riHdrNo): void
    {
        $this->updateJsonRI($riHdrNo, $this->dataDaftarRi);

        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Anamnesa berhasil disimpan.");
    }
    // insert and update record end////////////////


    private function findData($rjno): void
    {

        $this->dataDaftarRi = $this->findDataRI($rjno);
        // dd($this->dataDaftarRi);
        // jika anamnesa tidak ditemukan tambah variable anamnesa pda array
        if (isset($this->dataDaftarRi['anamnesa']) == false) {
            $this->dataDaftarRi['anamnesa'] = $this->anamnesa;
        }

        // menyamakan Variabel keluhan utama
        $this->matchingMyVariable();
    }



    public function addRekonsiliasiObat()
    {
        if ($this->rekonsiliasiObat['namaObat']) {

            // check exist
            $cekRekonsiliasiObat = collect($this->dataDaftarRi['anamnesa']['rekonsiliasiObat'])
                ->where("namaObat", '=', $this->rekonsiliasiObat['namaObat'])
                ->count();

            if (!$cekRekonsiliasiObat) {
                $this->dataDaftarRi['anamnesa']['rekonsiliasiObat'][] = [
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

        $rekonsiliasiObat = collect($this->dataDaftarRi['anamnesa']['rekonsiliasiObat'])->where("namaObat", '!=', $namaObat)->toArray();
        $this->dataDaftarRi['anamnesa']['rekonsiliasiObat'] = $rekonsiliasiObat;
        $this->store();
    }


    private function matchingMyVariable()
    {

        // keluhanUtama
        $this->dataDaftarRi['anamnesa']['keluhanUtama']['keluhanUtama'] =
            ($this->dataDaftarRi['anamnesa']['keluhanUtama']['keluhanUtama'])
            ? $this->dataDaftarRi['anamnesa']['keluhanUtama']['keluhanUtama']
            : ((isset($this->dataDaftarRi['screening']['keluhanUtama']) && !$this->dataDaftarRi['anamnesa']['keluhanUtama']['keluhanUtama'])
                ? $this->dataDaftarRi['screening']['keluhanUtama']
                : "");
    }


    public function setJamDatang($myTime)
    {
        $this->dataDaftarRi['anamnesa']['pengkajianPerawatan']['jamDatang'] = $myTime;
    }

    private function validatePerawatPenerima()
    {
        // Validasi dulu
        $messages = [];

        // Proses Validasi///////////////////////////////////////////
        try {
            $this->validate($this->rules, $messages);
        } catch (\Illuminate\Validation\ValidationException $e) {

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Anda tidak dapat melakukan TTD-E karena data pemeriksaan belum lengkap." . $e->getMessage());
            $this->validate($this->rules, $messages);
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
            $this->dataDaftarRi['anamnesa']['pengkajianPerawatan']['perawatPenerima'] = $myUserNameActive;
            $this->dataDaftarRi['anamnesa']['pengkajianPerawatan']['perawatPenerimaCode'] = $myUserCodeActive;
            $this->store();
        } else {

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Anda tidak dapat melakukan TTD-E karena User Role " . $myUserNameActive . ' Bukan Perawat.');
            return;
        }
    }

    // when new form instance
    public function mount()
    {

        $this->findData($this->riHdrNoRef);
    }



    // select data start////////////////
    public function render()
    {

        return view(
            'livewire.emr-r-i.mr-r-i.anamnesa.anamnesa',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Anamnesa',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Pasien Rawat Inap',
            ]
        );
    }
    // select data end////////////////


}
