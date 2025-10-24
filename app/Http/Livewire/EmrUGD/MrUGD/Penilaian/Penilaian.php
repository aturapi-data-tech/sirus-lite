<?php

namespace App\Http\Livewire\EmrUGD\MrUGD\Penilaian;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Cache\LockTimeoutException;

use Livewire\Component;
use Livewire\WithPagination;

use App\Http\Traits\EmrUGD\EmrUGDTrait;


class Penilaian extends Component
{
    use WithPagination, EmrUGDTrait;
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
                "skalaMorseScore" => 0,
                "skalaMorseDesc" => "",


                "riwayatJatuh3blnTerakhir" => "",
                "riwayatJatuh3blnTerakhirScore" => 0,
                "riwayatJatuh3blnTerakhirOptions" => [
                    ["riwayatJatuh3blnTerakhir" => "Ya", "score" => 25],
                    ["riwayatJatuh3blnTerakhir" => "Tidak", "score" => 0],
                ],
                "diagSekunder" => "",
                "diagSekunderScore" => 0,
                "diagSekunderOptions" => [
                    ["diagSekunder" => "Ya", "score" => 15],
                    ["diagSekunder" => "Tidak", "score" => 0],
                ],
                "alatBantu" => "",
                "alatBantuScore" => 0,
                "alatBantuOptions" => [
                    ["alatBantu" => "Tidak Ada / Bed Rest", "score" => 0],
                    ["alatBantu" => "Tongkat / Alat Penopang / Walker", "score" => 15],
                    ["alatBantu" => "Furnitur", "score" => 30],

                ],
                "heparin" => "",
                "heparinScore" => 0,
                "heparinOptions" => [
                    ["heparin" => "Ya", "score" => 20],
                    ["heparin" => "Tidak", "score" => 0],
                ],
                "gayaBerjalan" => "",
                "gayaBerjalanScore" => 0,
                "gayaBerjalanOptions" => [
                    ["gayaBerjalan" => "Normal / Tirah Baring / Tidak Bergerak", "score" => 0],
                    ["gayaBerjalan" => "Lemah", "score" => 10],
                    ["gayaBerjalan" => "Terganggu", "score" => 20],
                ],
                "kesadaran" => "",
                "kesadaranScore" => 0,
                "kesadaranOptions" => [
                    ["kesadaran" => "Baik", "score" => 0],
                    ["kesadaran" => "Lupa / Pelupa", "score" => 15],
                ],
            ],

            "skalaHumptyDumpty" => [
                "skalaHumptyDumptyTab" => "Skala Humpty Dumpty Score",
                "skalaHumptyDumptyScore" => 0,
                "skalaHumptyDumptyDesc" => "",

                "umur" => "",
                "umurScore" => 0,
                "umurOptions" => [
                    ["umur" => "< 3 tahun", "score" => 4],
                    ["umur" => "3-7 tahun", "score" => 3],
                    ["umur" => "7-13 tahun", "score" => 2],
                    ["umur" => "13-18 tahun", "score" => 1],

                ],

                "sex" => "",
                "sexScore" => 0,
                "sexOptions" => [
                    ["sex" => "Laki-laki", "score" => 2],
                    ["sex" => "Perempuan", "score" => 1],
                ],

                "diagnosa" => "",
                "diagnosaScore" => 0,
                "diagnosaOptions" => [
                    ["diagnosa" => "Kelainan Neurologi", "score" => 4],
                    ["diagnosa" => "Perubahan dalam Oksigenasi (Masalah Saluran Nafas, Dehidrasi, Anemia, Anoreksi, Slokop/sakit kepala, dll)", "score" => 3],
                    ["diagnosa" => "Kelamin Priksi/Perilaku", "score" => 2],
                    ["diagnosa" => "Diagnosa Lain", "score" => 1],
                ],
                "gangguanKognitif" => "",
                "gangguanKognitifScore" => 0,
                "gangguanKognitifOptions" => [
                    ["gangguanKognitif" => "Tidak Sadar terhadap Keterbatasan", "score" => 3],
                    ["gangguanKognitif" => "Lupa keterbatasan", "score" => 2],
                    ["gangguanKognitif" => "Mengetahui Kemampuan Diri", "score" => 1],
                ],
                "faktorLingkungan" => "",
                "faktorLingkunganScore" => 0,
                "faktorLingkunganOptions" => [
                    ["faktorLingkungan" => "Riwayat jatuh dari tempat tidur saat bayi anak", "score" => 4],
                    ["faktorLingkungan" => "Pasien menggunakan alat bantu box atau mobel", "score" => 3],
                    ["faktorLingkungan" => "Pasien berada ditempat tidur", "score" => 2],
                    ["faktorLingkungan" => "Diluar ruang rawat", "score" => 1],
                ],
                "penggunaanObat" => "",
                "penggunaanObatScore" => 0,
                "penggunaanObatOptions" => [
                    ["penggunaanObat" => "Bermacam-macam obat yang digunakan: obat sedarif (kecuali pasien ICU yang menggunakan sedasi dan paralisis), Hipnotik, Barbiturat, Fenotiazin, Antidepresan, Laksans/Diuretika,Narketik", "score" => 3],
                    ["penggunaanObat" => "Salah satu pengobatan diatas", "score" => 2],
                    ["penggunaanObat" => "Pengobatan lain", "score" => 1],
                ],
                "responTerhadapOperasi" => "",
                "responTerhadapOperasiScore" => 0,
                "responTerhadapOperasiOptions" => [
                    ["responTerhadapOperasi" => "Dalam 24 Jam", "score" => 3],
                    ["responTerhadapOperasi" => "Dalam 48 jam riwayat jatuh", "score" => 2],
                    ["responTerhadapOperasi" => "> 48 jam", "score" => 1],
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


    public function updated($propertyName)
    {
        $this->scoringSkalaMorse();
        $this->scoringSkalaHumptyDumpty();
    }



    // ////////////////
    // RJ Logic
    // ////////////////




    // insert and update record start////////////////
    public function store()
    {
        if (!$this->checkUgdStatus()) return;

        $rjNo = $this->dataDaftarUgd['rjNo'] ?? $this->rjNoRef ?? null;
        if (!$rjNo) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('rjNo kosong.');
            return;
        }

        $lockKey = "ugd:{$rjNo}";
        try {
            Cache::lock($lockKey, 5)->block(3, function () use ($rjNo) {
                DB::transaction(function () use ($rjNo) {
                    // ambil fresh agar tidak menimpa modul lain
                    $fresh = $this->findDataUGD($rjNo) ?: [];
                    $fresh['penilaian'] = array_replace_recursive(
                        $this->defaultPenilaian(), // default minimal
                        (array)($fresh['penilaian'] ?? []),
                        (array)($this->dataDaftarUgd['penilaian'] ?? [])
                    );

                    $this->updateJsonUGD($rjNo, $fresh);
                    $this->dataDaftarUgd = $fresh;
                });
            });

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addSuccess("Penilaian berhasil disimpan.");
        } catch (LockTimeoutException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Sistem sibuk, gagal memperoleh lock. Coba lagi.');
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Gagal menyimpan.');
        }
    }

    private function defaultPenilaian(): array
    {
        return $this->penilaian; // pakai yang sudah kamu definisikan di class
    }


    // insert and update record end////////////////

    private function findData($rjno): void
    {
        $this->dataDaftarUgd = $this->findDataUGD($rjno) ?: [];

        // siapkan penilaian minimal tanpa menimpa field tambahan
        $current = (array)($this->dataDaftarUgd['penilaian'] ?? []);
        $this->dataDaftarUgd['penilaian'] = array_replace_recursive($this->defaultPenilaian(), $current);

        // jaga-jaga inti struktur ada
        $this->dataDaftarUgd['penilaian']['resikoJatuh'] = $this->dataDaftarUgd['penilaian']['resikoJatuh'] ?? [];
        $this->dataDaftarUgd['penilaian']['resikoJatuh']['skalaMorse'] =
            $this->dataDaftarUgd['penilaian']['resikoJatuh']['skalaMorse'] ?? [
                "skalaMorseTab" => "Skala Morse Score",
                "skalaMorseScore" => 0,
                "skalaMorseDesc" => "",
            ];
        $this->dataDaftarUgd['penilaian']['resikoJatuh']['skalaHumptyDumpty'] =
            $this->dataDaftarUgd['penilaian']['resikoJatuh']['skalaHumptyDumpty'] ?? [
                "skalaHumptyDumptyTab" => "Skala Humpty Dumpty Score",
                "skalaHumptyDumptyScore" => 0,
                "skalaHumptyDumptyDesc" => "",
            ];

        // hitung skor awal biar UI konsisten
        $this->scoringSkalaMorse();
        $this->scoringSkalaHumptyDumpty();
    }
    // set data RJno / NoBooking / NoAntrian / klaimId / kunjunganId
    private function setDataPrimer(): void {}

    private function scoringSkalaMorse(): void
    {
        $morse = &$this->dataDaftarUgd['penilaian']['resikoJatuh']['skalaMorse'];

        $sum =
            (int)($morse['riwayatJatuh3blnTerakhirScore'] ?? 0) +
            (int)($morse['diagSekunderScore'] ?? 0) +
            (int)($morse['alatBantuScore'] ?? 0) +
            (int)($morse['heparinScore'] ?? 0) +
            (int)($morse['gayaBerjalanScore'] ?? 0) +
            (int)($morse['kesadaranScore'] ?? 0);

        $morse['skalaMorseScore'] = $sum;
        $morse['skalaMorseDesc'] =
            ($sum <= 24) ? 'Tidak Ada Risiko'
            : (($sum <= 50) ? 'Risiko Rendah' : 'Risiko Tinggi');
    }

    private function scoringSkalaHumptyDumpty(): void
    {
        $hd = &$this->dataDaftarUgd['penilaian']['resikoJatuh']['skalaHumptyDumpty'];

        $sum =
            (int)($hd['umurScore'] ?? 0) +
            (int)($hd['sexScore'] ?? 0) +
            (int)($hd['diagnosaScore'] ?? 0) +
            (int)($hd['gangguanKognitifScore'] ?? 0) +
            (int)($hd['faktorLingkunganScore'] ?? 0) +
            (int)($hd['penggunaanObatScore'] ?? 0) +
            (int)($hd['responTerhadapOperasiScore'] ?? 0);

        $hd['skalaHumptyDumptyScore'] = $sum;
        $hd['skalaHumptyDumptyDesc']  = ($sum >= 12) ? 'Risiko Tinggi' : 'Risiko Rendah';
    }


    private function checkUgdStatus(): bool
    {
        $row = DB::table('rstxn_ugdhdrs')->select('rj_status')
            ->where('rj_no', $this->rjNoRef)->first();

        if (!$row || $row->rj_status !== 'A') {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Pasien Sudah Pulang, Transaksi Terkunci.');
            return false;
        }
        return true;
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
                'myTitle' => 'Penilaian',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Pasien Rawat Jalan',
            ]
        );
    }
    // select data end////////////////


}
