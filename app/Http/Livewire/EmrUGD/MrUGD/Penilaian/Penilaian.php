<?php

namespace App\Http\Livewire\EmrUGD\MrUGD\Penilaian;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

use Spatie\ArrayToXml\ArrayToXml;


class Penilaian extends Component
{
    use WithPagination;
    // listener from blade////////////////
    protected $listeners = [];

    //////////////////////////////
    // Ref on top bar
    //////////////////////////////
    public $rjNoRef;

    // dataDaftarUgd RJ
    public array $dataDaftarUgd = [];

    // data penilaian=>[] 
    public array $penilaian =
    [
        "fisikTab" => "Fisik",
        "fisik" => [
            "fisik" => ""
        ],

        "statusMedikTab" => "Status Medik",
        "statusMedik" => [
            "statusMedik" => "",
            "statusMedikOptions" => [
                ["statusMedik" => "Emergency Trauma"],
                ["statusMedik" => "Emergency Non Trauma"],
                ["statusMedik" => "Non Emergency Trauma"],
                ["statusMedik" => "Non Emergency Non Trauma"],
            ]
        ],

        "nyeriTab" => "Nyeri",
        "nyeri" => [
            "vas" => [
                "vas" => "",
                "vasOptions" => [
                    ["vas" => "0"],
                    ["vas" => "1"],
                    ["vas" => "2"],
                    ["vas" => "3"],
                    ["vas" => "4"],
                    ["vas" => "5"],
                    ["vas" => "6"],
                    ["vas" => "7"],
                    ["vas" => "8"],
                    ["vas" => "9"],
                    ["vas" => "10"],
                ]

            ],
            "nyeri" => "",
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



    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////






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

        //     $this->emit('toastr-error', "Lakukan Pengecekan kembali Input Data.");
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
        $this->updateDataRJ($this->dataDaftarUgd['rjNo']);
    }

    private function updateDataRJ($rjNo): void
    {

        // update table trnsaksi
        DB::table('rstxn_ugdhdrs')
            ->where('rj_no', $rjNo)
            ->update([
                'dataDaftarUgd_json' => json_encode($this->dataDaftarUgd, true),
                'dataDaftarUgd_xml' => ArrayToXml::convert($this->dataDaftarUgd),
            ]);

        $this->emit('toastr-success', "Penilaian Fisik berhasil disimpan.");
    }
    // insert and update record end////////////////


    private function findData($rjno): void
    {


        $findData = DB::table('rsview_ugdkasir')
            ->select('datadaftarugd_json', 'vno_sep')
            ->where('rj_no', $rjno)
            ->first();

        $datadaftarugd_json = isset($findData->datadaftarugd_json) ? $findData->datadaftarugd_json : null;
        // if meta_data_pasien_json = null
        // then cari Data Pasien By Key Collection (exception when no data found)
        // 
        // else json_decode
        if ($datadaftarugd_json) {
            $this->dataDaftarUgd = json_decode($findData->datadaftarugd_json, true);

            // jika penilaian tidak ditemukan tambah variable penilaian pda array
            if (isset($this->dataDaftarUgd['penilaian']) == false) {
                $this->dataDaftarUgd['penilaian'] = $this->penilaian;
            }
        } else {

            $this->emit('toastr-error', "Data tidak dapat di proses json.");
            $dataDaftarUgd = DB::table('rsview_ugdkasir')
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
                    // 'poli_desc',
                    'dr_id',
                    'dr_name',
                    'klaim_id',
                    'entry_id',
                    'shift',
                    'vno_sep',
                    'no_antrian',

                    'nobooking',
                    'push_antrian_bpjs_status',
                    'push_antrian_bpjs_json',
                    // 'kd_dr_bpjs',
                    // 'kd_poli_bpjs',
                    'rj_status',
                    'txn_status',
                    'erm_status',
                )
                ->where('rj_no', '=', $rjno)
                ->first();

            $this->dataDaftarUgd = [
                "regNo" =>  $dataDaftarUgd->reg_no,

                "drId" =>  $dataDaftarUgd->dr_id,
                "drDesc" =>  $dataDaftarUgd->dr_name,

                "poliId" =>  $dataDaftarUgd->poli_id,
                "klaimId" => $dataDaftarUgd->klaim_id,
                // "poliDesc" =>  $dataDaftarUgd->poli_desc ,

                // "kddrbpjs" =>  $dataDaftarUgd->kd_dr_bpjs ,
                // "kdpolibpjs" =>  $dataDaftarUgd->kd_poli_bpjs ,

                "rjDate" =>  $dataDaftarUgd->rj_date,
                "rjNo" =>  $dataDaftarUgd->rj_no,
                "shift" =>  $dataDaftarUgd->shift,
                "noAntrian" =>  $dataDaftarUgd->no_antrian,
                "noBooking" =>  $dataDaftarUgd->nobooking,
                "slCodeFrom" => "02",
                "passStatus" => "",
                "rjStatus" =>  $dataDaftarUgd->rj_status,
                "txnStatus" =>  $dataDaftarUgd->txn_status,
                "ermStatus" =>  $dataDaftarUgd->erm_status,
                "cekLab" => "0",
                "kunjunganInternalStatus" => "0",
                "noReferensi" =>  $dataDaftarUgd->reg_no,
                "postInap" => [],
                "internal12" => "1",
                "internal12Desc" => "Faskes Tingkat 1",
                "internal12Options" => [
                    [
                        "internal12" => "1",
                        "internal12Desc" => "Faskes Tingkat 1"
                    ],
                    [
                        "internal12" => "2",
                        "internal12Desc" => "Faskes Tingkat 2 RS"
                    ]
                ],
                "kontrol12" => "1",
                "kontrol12Desc" => "Faskes Tingkat 1",
                "kontrol12Options" => [
                    [
                        "kontrol12" => "1",
                        "kontrol12Desc" => "Faskes Tingkat 1"
                    ],
                    [
                        "kontrol12" => "2",
                        "kontrol12Desc" => "Faskes Tingkat 2 RS"
                    ],
                ],
                "taskIdPelayanan" => [
                    "taskId1" => "",
                    "taskId2" => "",
                    "taskId3" =>  $dataDaftarUgd->rj_date,
                    "taskId4" => "",
                    "taskId5" => "",
                    "taskId6" => "",
                    "taskId7" => "",
                    "taskId99" => "",
                ],
                'sep' => [
                    "noSep" =>  $dataDaftarUgd->vno_sep,
                    "reqSep" => [],
                    "resSep" => [],
                ]
            ];


            // jika penilaian tidak ditemukan tambah variable penilaian pda array
            if (isset($this->dataDaftarUgd['penilaian']) == false) {
                $this->dataDaftarUgd['penilaian'] = $this->penilaian;
            }
        }
    }

    // set data RJno / NoBooking / NoAntrian / klaimId / kunjunganId
    private function setDataPrimer(): void
    {
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
            'livewire.emr-u-g-d.mr-u-g-d.penilaian.penilaian',
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
