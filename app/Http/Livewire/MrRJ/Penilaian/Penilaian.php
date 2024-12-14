<?php

namespace App\Http\Livewire\MrRJ\Penilaian;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;

use App\Http\Traits\EmrRJ\EmrRJTrait;


use Spatie\ArrayToXml\ArrayToXml;


class Penilaian extends Component
{
    use WithPagination, EmrRJTrait;


    //////////////////////////////
    // Ref on top bar
    //////////////////////////////
    public $rjNoRef;



    // dataDaftarPoliRJ RJ
    public $dataDaftarPoliRJ = [];

    // data SKDP / penilaian=>[]
    public $penilaian =
    [
        "fisikTab" => "Fisik",
        "fisik" => [
            "fisik" => ""
        ],

        "nyeriTab" => "Nyeri",
        "nyeri" => [
            "nyeri" => "Tidak",
            "nyeriOptions" => [
                ["nyeri" => "Ya"],
                ["nyeri" => "Tidak"],
            ],
            "nyeriKet" => "",
            "nyeriKetOptions" => [
                ["nyeriKet" => "Akut"],
                ["nyeriKet" => "Kronis"],
            ],
            "skalaNyeri" => "",
            "nyeriMetode" => "",
            "nyeriMetodeOptions" => [
                ["nyeriMetode" => "NRS"],
                ["nyeriMetode" => "BPS"],
                ["nyeriMetode" => "NIPS"],
                ["nyeriMetode" => "FLACC"],
                ["nyeriMetode" => "VAS"],
            ],
            "pencetus" => "",
            "gambar" => "",
            "durasi" => "",
            "lokasi" => "",
        ],

        "statusPediatrikTab" => "Status Pediatrik",
        "statusPediatrik" => [
            "statusPediatrik" => "",
            "statusPediatrikOptions" => [
                ["statusPediatrik" => "Gizi Kurang"],
                ["statusPediatrik" => "Gizi Cukup"],
                ["statusPediatrik" => "Gizi Lebih"],
            ]
        ],

        "diagnosisTab" => "Diagnosis",
        "diagnosis" => [
            "diagnosis" => "",
        ],

        "resikoJatuhTab" => "Resiko Jatuh",
        "resikoJatuh" => [
            "skalaMorse" => [
                "skalaMorseTab" => "Skala Morse Score",
                "skalaMorseScore" => "",

                "riwayatJatuh3blnTerakhir" => "",
                "riwayatJatuh3blnTerakhirOptions" => [
                    ["riwayatJatuh3blnTerakhir" => "Ya"],
                    ["riwayatJatuh3blnTerakhir" => "Tidak"],
                ],
                "diagSekunder" => "",
                "diagSekunderOptions" => [
                    ["diagSekunder" => "Ya"],
                    ["diagSekunder" => "Tidak"],
                ],
                "alatBantu" => "",
                "alatBantuOptions" => [
                    ["alatBantu" => "Tidak Ada / Bed Rest"],
                    ["alatBantu" => "Tongkat / Alat Penopang / Walker"],
                    ["alatBantu" => "Furnitur"],

                ],
                "heparin" => "",
                "heparinOptions" => [
                    ["heparin" => "Ya"],
                    ["heparin" => "Tidak"],
                ],
                "gayaBerjalan" => "",
                "gayaBerjalanOptions" => [
                    ["gayaBerjalan" => "Lemah"],
                    ["gayaBerjalan" => "Terganggu"],
                ],
                "kesadaran" => "",
                "kesadaranOptions" => [
                    ["kesadaran" => "Baik"],
                    ["kesadaran" => "Lupa / Pelupa"],
                ],
            ],

            "skalaHumptyDumpty" => [
                "skalaHumptyDumptyTab" => "Skala Humpty Dumpty Score",
                "skalaHumptyDumptyScore" => "",
                "skalaHumptyDumptyket" => ">= 13 Tahun",
                "skalaHumptyDumptysex" => "Perempuan",

                "diagnosa" => "",
                "diagnosaOptions" => [
                    ["diagnosa" => "Kelainan Neurologi"],
                    ["diagnosa" => "Perubahan dalam Oksigenasi (Masalah Saluran Nafas, Dehidrasi, Anemia, Anoreksi, Slokop/sakit kepala, dll)"],
                    ["diagnosa" => "Kelamin Priksi/Perilaku"],
                    ["diagnosa" => "Diagnosa Lain"],
                ],
                "gangguanKognitif" => "",
                "gangguanKognitifOptions" => [
                    ["gangguanKognitif" => "Tidak Sadar terhadap Keterbatasan"],
                    ["gangguanKognitif" => "Lupa keterbatasan"],
                    ["gangguanKognitif" => "Mengetahui Kemampuan Diri"],
                ],
                "faktorLingkungan" => "",
                "faktorLingkunganOptions" => [
                    ["faktorLingkungan" => "Riwayat jatuh dari tempat tidur saat bayi anak"],
                    ["faktorLingkungan" => "Pasien menggunakan alat bantu box atau mobel"],
                    ["faktorLingkungan" => "Pasien berada ditempat tidur"],
                    ["faktorLingkungan" => "Diluar ruang rawat"],
                ],
                "penggunaanObat" => "",
                "penggunaanObatOptions" => [
                    ["penggunaanObat" => "Bermacam-macam obat yang digunakan: obat sedarif (kecuali pasien ICU yang menggunakan sedasi dan paralisis), Hipnotik, Barbiturat, Fenotiazin, Antidepresan, Laksans/Diuretika,Narketik"],
                    ["penggunaanObat" => "Salah satu pengobatan diatas"],
                    ["penggunaanObat" => "Pengobatan lain"],
                ],
                "responTerhadapOperasi" => "",
                "responTerhadapOperasiOptions" => [
                    ["responTerhadapOperasi" => "Dalam 24 Jam"],
                    ["responTerhadapOperasi" => "Dalam 48 jam riwayat jatuh"],
                    ["responTerhadapOperasi" => "> 48 jam"],
                ],
            ],

            "edmonson" => [
                "edmonsonTab" => "Edmonson Psychiatric Fall Risk Assesment",
                "edmonsonScore" => "",
                "edmonsonUsia" => "< 50 Tahun",

                "statusMental" => "",
                "statusMentalOptions" => [
                    ["statusMental" => "Sadar penuh dan orientasi waktu baik"],
                    ["statusMental" => "Agitasi / Cemas"],
                    ["statusMental" => "Sering bingung"],
                    ["statusMental" => "Bingung dan disorientasi"],
                ],

                "eliminasi" => "",
                "eliminasiOptions" => [
                    ["eliminasi" => "Mandiri untuk BAB dan BAK"],
                    ["eliminasi" => "Memakai Kateter / Ostomy"],
                    ["eliminasi" => "BAB dan BAK dengan bantuan"],
                    ["eliminasi" => "Gangguan eliminasi (inkontinensia, banyak BAK di malam hari, sering BAB dan BAK)"],
                    ["eliminasi" => "Inkontinensia tetapi bisa ambulasi mandiri"],
                ],

                "medikasi" => "",
                "medikasiOptions" => [
                    ["medikasi" => "Tidak ada pengobatan yang diberikan"],
                    ["medikasi" => "Obat-obatan jantung"],
                    ["medikasi" => "Obat psikiatri termasuk benzodiazepin dan anti depresan"],
                    ["medikasi" => "Meningkatnya dosis obat yang dikonsumsi / ditambahkan dalam 24 jam terakhir"],
                ],

                "diagnosis" => "",
                "diagnosisOptions" => [
                    ["diagnosis" => "Bipolar / gangguan scizo affective"],
                    ["diagnosis" => "Penyalahgunaan zat terlarang dan alkohol"],
                    ["diagnosis" => "Gangguan depresi mayor"],
                    ["diagnosis" => "Dimensia / Delirium"],
                ],

                "ambulasi" => "",
                "ambulasiOptions" => [
                    ["ambulasi" => "Ambulasi mandiri dan langkah stabil atau pasien imobil"],
                    ["ambulasi" => "Penggunaan alat bantu yang tepat (tongkat, walker, tripod, dll)"],
                    ["ambulasi" => "Vertigo / Hipotensi Ortostatik / Kelemahan"],
                    ["ambulasi" => "Langkah tidak stabil, butuh bantuan dan menyadari kemampuannya"],
                ],

                "nutrisi" => "",
                "nutrisiOptions" => [
                    ["nutrisi" => "Hanya sedikit mendapatkan asupan makanan / minum dalam 24 jam terakhir"],
                    ["nutrisi" => "Nafsu makan baik"],
                ],

                "ganguanTidur" => "",
                "ganguanTidurOptions" => [
                    ["ganguanTidur" => "Tidak ada gangguan tidur"],
                    ["ganguanTidur" => "Ada gangguan tidur yang dilaporkan keluarga pasien / staf"],
                ],

                "riwayatJatuh" => "",
                "riwayatJatuhOptions" => [
                    ["riwayatJatuh" => "Tidak ada riwayat jatuh"],
                    ["riwayatJatuh" => "Ada riwayat jatuh dalam 3 bulan terakhir"],
                ],


                "faktorLingkungan" => "",
                "faktorLingkunganOptions" => [
                    ["faktorLingkungan" => "Riwayat jatuh dari tempat tidur saat bayi anak"],
                    ["faktorLingkungan" => "Pasien menggunakan alat bantu box atau mobel"],
                    ["faktorLingkungan" => "Pasien berada ditempat tidur"],
                    ["faktorLingkungan" => "Diluar ruang rawat"],
                ],
                "penggunaanObat" => "",
                "penggunaanObatOptions" => [
                    ["penggunaanObat" => "Bermacam-macam obat yang digunakan: obat sedarif (kecuali pasien ICU yang menggunakan sedasi dan paralisis), Hipnotik, Barbiturat, Fenotiazin, Antidepresan, Laksans/Diuretika,Narketik"],
                    ["penggunaanObat" => "Salah satu pengobatan diatas"],
                    ["penggunaanObat" => "Pengobatan lain"],
                ],
                "responTerhadapOperasi" => "",
                "responTerhadapOperasiOptions" => [
                    ["responTerhadapOperasi" => "Dalam 24 Jam"],
                    ["responTerhadapOperasi" => "Dalam 48 jam riwayat jatuh"],
                    ["responTerhadapOperasi" => "> 48 jam"],
                ],
            ],

        ],

        "dekubitus" => [
            "dekubitusTab" => "Dekubitus",
            "kodisiFisik" => "",
            "kodisiFisikOptions" => [
                ["kodisiFisik" => "Baik"],
                ["kodisiFisik" => "Lumayan"],
                ["kodisiFisik" => "Buruk"],
                ["kodisiFisik" => "Sangat Buruk"],
            ],

            "kesadaran" => "",
            "kesadaranOptions" => [
                ["kesadaran" => "Kompos Mentis"],
                ["kesadaran" => "Apatis"],
                ["kesadaran" => "Konfus/Soporis"],
                ["kesadaran" => "Stupor/Koma"],
            ],

            "aktifitas" => "",
            "aktifitasOptions" => [
                ["aktifitas" => "Dapat Berpindah"],
                ["aktifitas" => "Berjalan Dengan Bantuan"],
                ["aktifitas" => "Terbatas di Kursi"],
                ["aktifitas" => "Terbatas di Tempat Tidur"],
            ],

            "mobilitas" => "",
            "mobilitasOptions" => [
                ["mobilitas" => "Bergerak Bebas"],
                ["mobilitas" => "Sedikit Terbatas"],
                ["mobilitas" => "Sangat Terbatas"],
                ["mobilitas" => "Tak Bisa Bergerak"],
            ],

            "inkontinensia" => "",
            "inkontinensiaOptions" => [
                ["inkontinensia" => "Tidak Ngompol"],
                ["inkontinensia" => "Kadang-kadang"],
                ["inkontinensia" => "Sering Inkontinensia Urin"],
                ["inkontinensia" => "Sering Inkontinensia Alvi dan Urin"],
            ],
        ],
    ];
    //////////////////////////////////////////////////////////////////////


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
            'rjNoRef'
        ]);
    }





    // ////////////////
    // RJ Logic
    // ////////////////


    // validate Data RJ//////////////////////////////////////////////////
    private function validateDataRJ(): void
    {
        // customErrorMessages
        // $messages = customErrorMessagesTrait::messages();

        // require nik ketika pasien tidak dikenal



        // $rules = [];



        // Proses Validasi///////////////////////////////////////////
        // try {
        //     $this->validate($rules, $messages);
        // } catch (\Illuminate\Validation\ValidationException $e) {

        //      toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError( "Lakukan Pengecekan kembali Input Data.");
        //     $this->validate($rules, $messages);
        // }
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
                'datadaftarpolirj_json' => json_encode($this->dataDaftarPoliRJ, true),
                'datadaftarpolirj_xml' => ArrayToXml::convert($this->dataDaftarPoliRJ),
            ]);

        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Penilaian Fisik berhasil disimpan.");
    }
    // insert and update record end////////////////


    private function findData($rjno): void
    {
        $findDataRJ = $this->findDataRJ($rjno);
        $this->dataDaftarPoliRJ  = $findDataRJ['dataDaftarRJ'];

        // jika kontrol tidak ditemukan tambah variable kontrol pda array
        if (isset($this->dataDaftarPoliRJ['penilaian']) == false) {
            $this->dataDaftarPoliRJ['penilaian'] = $this->penilaian;
        }
    }

    // set data RJno / NoBooking / NoAntrian / klaimId / kunjunganId
    private function setDataPrimer(): void {}


    // when new form instance
    public function mount()
    {
        $this->findData($this->rjNoRef);
    }



    // select data start////////////////
    public function render()
    {

        return view(
            'livewire.mr-r-j.penilaian.penilaian',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Anamnesia',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Pasien Rawat Jalan',
            ]
        );
    }
    // select data end////////////////


}
