<?php

namespace App\Http\Livewire\EmrRI\MrRI\PengkajianAwalPasienRawatInap;

use Livewire\Component;
use Livewire\WithPagination;

use App\Http\Traits\EmrRI\EmrRITrait;
use App\Http\Traits\customErrorMessagesTrait;

class PengkajianAwalPasienRawatInap extends Component
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

    public array $pengkajianAwalPasienRawatInap = [
        "bagian1DataUmum" => [
            "kondisiSaatMasuk" => "",
            "asalPasien" => [
                "pilihan" => "",
                "keterangan" => "" // Tambahan untuk opsi "lainnya"
            ],
            "diagnosaMasuk" => "",
            "dpjp" => "",
            "barangBerharga" => [
                "pilihan" => "",
                "catatan" => ""
            ],
            "alatBantu" => [
                "pilihan" => "",
                "keterangan" => "", // Tambahan untuk opsi "lainnya"
                "catatan" => ""
            ]
        ],
        "bagian2RiwayatPasien" => [
            "riwayatPenyakitOperasiCedera" => [
                "pilihan" => "",
                "keterangan" => "", // Tambahan untuk opsi "lainnya"
                "deskripsi" => ""
            ],
            "kebiasaan" => [
                "merokok" => [
                    "pilihan" => "",
                    "detail" => [
                        "jenis" => "",
                        "jumlahPerHari" => ""
                    ]
                ],
                "alkoholObat" => [
                    "pilihan" => "",
                    "detail" => [
                        "jenis" => "",
                        "jumlahPerHari" => ""
                    ]
                ]
            ],
            "vaksinasi" => [
                "influenza" => [
                    "pilihan" => ""
                ],
                "pneumonia" => [
                    "pilihan" => ""
                ]
            ],
            "riwayatKeluarga" => [
                "pilihan" => "",
                "keterangan" => "" // Tambahan untuk opsi "lainnya"
            ]
        ],
        "bagian3PsikososialDanEkonomi" => [
            "agamaKepercayaan" => [
                "pilihan" => "",
                "keterangan" => "" // Tambahan untuk opsi "lainnya"
            ],
            "statusPernikahan" => [
                "pilihan" => ""
            ],
            "tempatTinggal" => [
                "pilihan" => "",
                "keterangan" => "" // Tambahan untuk opsi "lainnya"
            ],
            "aktivitas" => [
                "pilihan" => ""
            ],
            "statusEmosional" => [
                "pilihan" => "",
                "keterangan" => "" // Tambahan untuk opsi "lainnya"
            ],
            "keluargaDekat" => [
                "nama" => "",
                "hubungan" => "",
                "telp" => ""
            ],
            "informasiDidapatDari" => [
                "pilihan" => "",
                "keterangan" => "" // Tambahan untuk opsi "lainnya"
            ]
        ],
        "bagian4PemeriksaanFisik" => [
            "tandaVital" => [
                "sistolik" => "", //number
                "distolik" => "", //number
                "frekuensiNafas" => "", //number
                "frekuensiNadi" => "", //number
                "suhu" => "", //number
                "spo2" => "", //number
                "gda" => "", //number
                "tb" => "",
                "bb" => "",
            ],
            "keluhanUtama" => "",
            "pemeriksaanSistemOrgan" => [
                "mataTelingaHidungTenggorokan" => [
                    "pilihan" => "",
                    "keterangan" => "" // Tambahan untuk opsi "lainnya"
                ],
                "paru" => [
                    "pilihan" => "",
                    "keterangan" => "" // Tambahan untuk opsi "lainnya"
                ],
                "jantung" => [
                    "pilihan" => "",
                    "keterangan" => "" // Tambahan untuk opsi "lainnya"
                ],
                "neurologi" => [
                    "tingkatKesadaran" => [
                        "pilihan" => ""
                    ],
                    "gcs" => ""
                ],
                "gastrointestinal" => [
                    "pilihan" => "",
                    "keterangan" => "" // Tambahan untuk opsi "lainnya"
                ],
                "genitourinaria" => [
                    "pilihan" => "",
                    "keterangan" => "" // Tambahan untuk opsi "lainnya"
                ],
                "muskuloskeletalDanKulit" => [
                    "pilihan" => "",
                    "keterangan" => "" // Tambahan untuk opsi "lainnya"
                ]
            ]
        ],
        "bagian5CatatanDanTandaTangan" => [
            "catatanUmum" => "",
            "petugasPengkaji" => "", //
            "petugasPengkajiCode" => "", //
            "jamPengkaji" => "" //
        ]
    ];

    // Opsi untuk radio button
    public $options = [
        "kondisiSaatMasukOptions" => ["mandiri", "dibantu", "tirahBaring"],
        "asalPasienOptions" => ["poliklinik", "igd", "kamarOperasi", "lainnya"],
        "barangBerhargaOptions" => ["ada", "tidakAda"],
        "alatBantuOptions" => ["kacamata", "gigiPalsu", "alatBantuDengar", "lainnya"],
        "riwayatPenyakitOptions" => ["hipertensi", "diabetes", "asma", "stroke", "penyakitJantung", "lainnya"],
        "merokokOptions" => ["ya", "tidak", "berhenti"],
        "alkoholObatOptions" => ["ya", "tidak", "berhenti"],
        "vaksinasiOptions" => ["ya", "tidak", "menolak"],
        "riwayatKeluargaOptions" => ["penyakitJantung", "hipertensi", "diabetes", "stroke", "lainnya"],
        "agamaKepercayaanOptions" => ["islam", "kristen", "hindu", "budha", "lainnya"],
        "statusPernikahanOptions" => ["menikah", "belumMenikah", "dudaJanda"],
        "tempatTinggalOptions" => ["rumah", "panti", "lainnya"],
        "aktivitasOptions" => ["mandiri", "dibantu", "tirahBaring"],
        "statusEmosionalOptions" => ["kooperatif", "cemas", "depresi", "lainnya"],
        "informasiDidapatDariOptions" => ["pasien", "keluarga", "lainnya"],
        "mataTelingaHidungTenggorokanOptions" => ["normal", "gangguanVisus", "tuli", "lainnya"],
        "paruOptions" => ["normal", "ronki", "wheezing", "lainnya"],
        "jantungOptions" => ["normal", "takikardi", "bradikardi", "lainnya"],
        "tingkatKesadaranOptions" => ["komposMentis", "apatis", "somnolen", "sopor", "koma", "delirium"],
        "gastrointestinalOptions" => ["normal", "distensi", "diare", "konstipasi", "lainnya"],
        "genitourinariaOptions" => ["normal", "hematuria", "inkontinensia", "lainnya"],
        "muskuloskeletalDanKulitOptions" => ["normal", "deformitas", "luka", "lainnya"]
    ];
    //////////////////////////////////////////////////////////////////////


    protected $rules = [
        // Bagian 1: Data Umum
        'pengkajianAwalPasienRawatInap.bagian1DataUmum.kondisiSaatMasuk' => 'required|string|in:mandiri,dibantu,tirahBaring',
        'pengkajianAwalPasienRawatInap.bagian1DataUmum.asalPasien.pilihan' => 'required|string|in:poliklinik,gd,kamarOperasi,lainnya',
        'pengkajianAwalPasienRawatInap.bagian1DataUmum.asalPasien.keterangan' => 'required_if:pengkajianAwalPasienRawatInap.bagian1DataUmum.asalPasien.pilihan,lainnya|nullable|string',
        'pengkajianAwalPasienRawatInap.bagian1DataUmum.diagnosaMasuk' => 'required|string',
        'pengkajianAwalPasienRawatInap.bagian1DataUmum.dpjp' => 'required|string',
        'pengkajianAwalPasienRawatInap.bagian1DataUmum.barangBerharga.pilihan' => 'required|string|in:ada,tidakAda',
        'pengkajianAwalPasienRawatInap.bagian1DataUmum.barangBerharga.catatan' => 'nullable|string',
        'pengkajianAwalPasienRawatInap.bagian1DataUmum.alatBantu.pilihan' => 'required|string|in:kacamata,gigiPalsu,alatBantuDengar,lainnya',
        'pengkajianAwalPasienRawatInap.bagian1DataUmum.alatBantu.keterangan' => 'required_if:pengkajianAwalPasienRawatInap.bagian1DataUmum.alatBantu.pilihan,lainnya|nullable|string',
        'pengkajianAwalPasienRawatInap.bagian1DataUmum.alatBantu.catatan' => 'nullable|string',

        // Bagian 2: Riwayat Pasien
        'pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.riwayatPenyakitOperasiCedera.pilihan' => 'required|string|in:hipertensi,diabetes,asma,stroke,penyakitJantung,lainnya',
        'pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.riwayatPenyakitOperasiCedera.keterangan' => 'required_if:pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.riwayatPenyakitOperasiCedera.pilihan,lainnya|nullable|string',
        'pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.riwayatPenyakitOperasiCedera.deskripsi' => 'nullable|string',
        'pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.kebiasaan.merokok.pilihan' => 'required|string|in:ya,tidak,berhenti',
        'pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.kebiasaan.merokok.detail.jenis' => 'nullable|string',
        'pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.kebiasaan.merokok.detail.jumlahPerHari' => 'nullable|numeric',
        'pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.kebiasaan.alkoholObat.pilihan' => 'required|string|in:ya,tidak,berhenti',
        'pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.kebiasaan.alkoholObat.detail.jenis' => 'nullable|string',
        'pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.kebiasaan.alkoholObat.detail.jumlahPerHari' => 'nullable|numeric',
        'pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.vaksinasi.influenza.pilihan' => 'required|string|in:ya,tidak,menolak',
        'pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.vaksinasi.pneumonia.pilihan' => 'required|string|in:ya,tidak,menolak',
        'pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.riwayatKeluarga.pilihan' => 'required|string|in:penyakitJantung,hipertensi,diabetes,stroke,lainnya',
        'pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.riwayatKeluarga.keterangan' => 'required_if:pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.riwayatKeluarga.pilihan,lainnya|nullable|string',

        // Bagian 3: Psikososial dan Ekonomi
        'pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.agamaKepercayaan.pilihan' => 'required|string|in:islam,kristen,hindu,budha,lainnya',
        'pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.agamaKepercayaan.keterangan' => 'required_if:pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.agamaKepercayaan.pilihan,lainnya|nullable|string',
        'pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.statusPernikahan.pilihan' => 'required|string|in:menikah,belumMenikah,dudaJanda',
        'pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.tempatTinggal.pilihan' => 'required|string|in:rumah,panti,lainnya',
        'pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.tempatTinggal.keterangan' => 'required_if:pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.tempatTinggal.pilihan,lainnya|nullable|string',
        'pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.aktivitas.pilihan' => 'required|string|in:mandiri,dibantu,tirahBaring',
        'pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.statusEmosional.pilihan' => 'required|string|in:kooperatif,cemas,depresi,lainnya',
        'pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.statusEmosional.keterangan' => 'required_if:pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.statusEmosional.pilihan,lainnya|nullable|string',
        'pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.keluargaDekat.nama' => 'required|string',
        'pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.keluargaDekat.hubungan' => 'required|string',
        'pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.keluargaDekat.telp' => 'required|string',
        'pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.informasiDidapatDari.pilihan' => 'required|string|in:pasien,keluarga,lainnya',
        'pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.informasiDidapatDari.keterangan' => 'required_if:pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.informasiDidapatDari.pilihan,lainnya|nullable|string',

        // Bagian 4: Pemeriksaan Fisik
        'pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.td' => 'required|string',
        'pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.nadi' => 'required|string',
        'pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.nafas' => 'required|string',
        'pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.suhu' => 'required|string',
        'pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.sao2' => 'required|string',
        'pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.tb' => 'required|string',
        'pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.bb' => 'required|string',
        'pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.keluhanUtama' => 'required|string',
        'pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.mataTelingaHidungTenggorokan.pilihan' => 'required|string|in:normal,gangguanVisus,tuli,lainnya',
        'pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.mataTelingaHidungTenggorokan.keterangan' => 'required_if:pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.mataTelingaHidungTenggorokan.pilihan,lainnya|nullable|string',
        'pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.paru.pilihan' => 'required|string|in:normal,ronki,wheezing,lainnya',
        'pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.paru.keterangan' => 'required_if:pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.paru.pilihan,lainnya|nullable|string',
        'pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.jantung.pilihan' => 'required|string|in:normal,takikardi,bradikardi,lainnya',
        'pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.jantung.keterangan' => 'required_if:pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.jantung.pilihan,lainnya|nullable|string',
        'pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.neurologi.tingkatKesadaran.pilihan' => 'required|string|in:komposMentis,apatis,somnolen,sopor,koma,delirium',
        'pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.neurologi.gcs' => 'required|string',
        'pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.gastrointestinal.pilihan' => 'required|string|in:normal,distensi,diare,konstipasi,lainnya',
        'pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.gastrointestinal.keterangan' => 'required_if:pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.gastrointestinal.pilihan,lainnya|nullable|string',
        'pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.genitourinaria.pilihan' => 'required|string|in:normal,hematuria,inkontinensia,lainnya',
        'pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.genitourinaria.keterangan' => 'required_if:pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.genitourinaria.pilihan,lainnya|nullable|string',
        'pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.muskuloskeletalDanKulit.pilihan' => 'required|string|in:normal,deformitas,luka,lainnya',
        'pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.muskuloskeletalDanKulit.keterangan' => 'required_if:pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.muskuloskeletalDanKulit.pilihan,lainnya|nullable|string',

        // Bagian 5: Catatan dan Tanda Tangan
        'pengkajianAwalPasienRawatInap.bagian5CatatanDanTandaTangan.catatanUmum' => 'nullable|string',
        'pengkajianAwalPasienRawatInap.bagian5CatatanDanTandaTangan.namaPetugas' => 'required|string',
        'pengkajianAwalPasienRawatInap.bagian5CatatanDanTandaTangan.tandaTangan' => 'required|string',
        'pengkajianAwalPasienRawatInap.bagian5CatatanDanTandaTangan.tanggal' => 'required|date_format:d/m/Y H:i:s',
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





    // ////////////////
    // RJ Logic
    // ////////////////


    // validate Data RJ//////////////////////////////////////////////////
    private function validatePengkajianAwalPasienRawatInap(): void
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
        $this->validatePengkajianAwalPasienRawatInap();

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


    private function findData($riHdrNo): void
    {

        $this->dataDaftarRi = $this->findDataRI($riHdrNo);
        // dd($this->dataDaftarRi);
        // jika pengkajianAwalPasienRawatInap tidak ditemukan tambah variable pengkajianAwalPasienRawatInap pda array
        if (isset($this->dataDaftarRi['pengkajianAwalPasienRawatInap']) == false) {
            $this->dataDaftarRi['pengkajianAwalPasienRawatInap'] = $this->pengkajianAwalPasienRawatInap;
        }

        // menyamakan Variabel keluhan utama
        $this->matchingMyVariable();
    }




    private function matchingMyVariable()
    {

        // keluhanUtama
        // $this->dataDaftarRi['pengkajianAwalPasienRawatInap']['keluhanUtama']['keluhanUtama'] =
        //     ($this->dataDaftarRi['pengkajianAwalPasienRawatInap']['keluhanUtama']['keluhanUtama'])
        //     ? $this->dataDaftarRi['pengkajianAwalPasienRawatInap']['keluhanUtama']['keluhanUtama']
        //     : ((isset($this->dataDaftarRi['screening']['keluhanUtama']) && !$this->dataDaftarRi['pengkajianAwalPasienRawatInap']['keluhanUtama']['keluhanUtama'])
        //         ? $this->dataDaftarRi['screening']['keluhanUtama']
        //         : "");
    }


    public function setJamPengkaji($jam)
    {
        $this->dataDaftarRi['pengkajianAwalPasienRawatInap']['bagian5CatatanDanTandaTangan']['jamPengkaji'] = $jam;
    }


    public function setPetugasPengkaji()
    {
        // Ambil data user yang sedang login
        $myUserCodeActive = auth()->user()->myuser_code;
        $myUserNameActive = auth()->user()->myuser_name;

        // Validasi apakah pengguna memiliki peran yang sesuai
        if (auth()->user()->hasRole('Perawat')) {
            // Isi data petugas pengkaji
            $this->dataDaftarRi['pengkajianAwalPasienRawatInap']['bagian5CatatanDanTandaTangan']['petugasPengkaji'] = $myUserNameActive;
            $this->dataDaftarRi['pengkajianAwalPasienRawatInap']['bagian5CatatanDanTandaTangan']['petugasPengkajiCode'] = $myUserCodeActive;

            // Simpan perubahan
            $this->store();

            // Notifikasi sukses
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("TTD Petugas Pengkaji berhasil diisi oleh " . $myUserNameActive);
        } else {
            // Notifikasi error jika peran tidak sesuai
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Anda tidak dapat melakukan TTD karena User Role " . $myUserNameActive . ' bukan Petugas Pengkaji.');
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
            'livewire.emr-r-i.mr-r-i.pengkajian-awal-pasien-rawat-inap.pengkajian-awal-pasien-rawat-inap',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Pengkajian Awal Pasien Rawat Inap',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Pasien Rawat Inap',
            ]
        );
    }
    // select data end////////////////


}
