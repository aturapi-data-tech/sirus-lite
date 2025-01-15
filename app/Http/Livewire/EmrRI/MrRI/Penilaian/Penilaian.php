<?php

namespace App\Http\Livewire\EmrRI\MrRI\Penilaian;

use Livewire\Component;
use Livewire\WithPagination;

use App\Http\Traits\EmrRI\EmrRITrait;


class Penilaian extends Component
{
    use WithPagination, EmrRITrait;
    // listener from blade////////////////
    protected $listeners = [
        'syncronizeAssessmentPerawatRIFindData' => 'mount'
    ];

    //////////////////////////////
    // Ref on top bar
    //////////////////////////////
    public $riHdrNoRef;

    // dataDaftarRi RJ
    public array $dataDaftarRi = [];

    public array $penilaian = [
        "nyeriTab" => "Nyeri",
        "nyeri" => [
            "vas" => [
                "vas" => "", // Nilai yang dipilih (misalnya, "5")
                "vasScore" => 0, // Skor berdasarkan nilai yang dipilih
            ],
            "nyeri" => [
                "nyeri" => "", // Nilai yang dipilih (misalnya, "Ya")
                "nyeriScore" => 0, // Skor berdasarkan nilai yang dipilih
            ],
            "nyeriKet" => [
                "nyeriKet" => "", // Nilai yang dipilih (misalnya, "Akut")
                "nyeriKetScore" => 0, // Skor berdasarkan nilai yang dipilih
            ],
            "skalaNyeri" => "", // Skala nyeri yang digunakan (misalnya, "NRS")
            "nyeriMetode" => [
                "nyeriMetode" => "", // Metode yang dipilih (misalnya, "NRS")
                "nyeriMetodeScore" => 0, // Skor berdasarkan metode yang dipilih
            ],
            "pencetus" => "", // Pencetus nyeri
            "gambar" => "", // Gambar atau ilustrasi terkait nyeri
            "durasi" => "", // Durasi nyeri
            "lokasi" => "", // Lokasi nyeri
        ],

        "statusPediatrikTab" => "Status Pediatrik",
        "statusPediatrik" => [
            "statusGizi" => [
                "statusGizi" => "", // Nilai yang dipilih (misalnya, "Gizi Normal")
                "statusGiziScore" => 0, // Skor berdasarkan nilai yang dipilih
            ],
            "perkembangan" => [
                "perkembangan" => "", // Nilai yang dipilih (misalnya, "Sesuai Usia")
                "perkembanganScore" => 0, // Skor berdasarkan nilai yang dipilih
            ],
            "kesehatanUmum" => [
                "kesehatanUmum" => "", // Nilai yang dipilih (misalnya, "Sehat")
                "kesehatanUmumScore" => 0, // Skor berdasarkan nilai yang dipilih
            ],
        ],

        "diagnosisTab" => "Diagnosis",
        "diagnosis" => [
            "diagnosis" => "", // Diagnosis pasien
        ],

        "resikoJatuhTab" => "Resiko Jatuh",
        "resikoJatuh" => [
            "skalaMorse" => [
                "skalaMorseTab" => "Skala Morse Score",
                "skalaMorseScore" => 0, // Total skor Skala Morse
                "skalaMorseDesc" => "", // Deskripsi risiko berdasarkan skor
                "riwayatJatuh3blnTerakhir" => [
                    "riwayatJatuh3blnTerakhir" => "", // Nilai yang dipilih (misalnya, "Ya")
                    "riwayatJatuh3blnTerakhirScore" => 0, // Skor berdasarkan nilai yang dipilih
                ],
                "diagSekunder" => [
                    "diagSekunder" => "", // Nilai yang dipilih (misalnya, "Tidak")
                    "diagSekunderScore" => 0, // Skor berdasarkan nilai yang dipilih
                ],
                "alatBantu" => [
                    "alatBantu" => "", // Nilai yang dipilih (misalnya, "Tongkat")
                    "alatBantuScore" => 0, // Skor berdasarkan nilai yang dipilih
                ],
                "heparin" => [
                    "heparin" => "", // Nilai yang dipilih (misalnya, "Ya")
                    "heparinScore" => 0, // Skor berdasarkan nilai yang dipilih
                ],
                "gayaBerjalan" => [
                    "gayaBerjalan" => "", // Nilai yang dipilih (misalnya, "Lemah")
                    "gayaBerjalanScore" => 0, // Skor berdasarkan nilai yang dipilih
                ],
                "statusMental" => [
                    "statusMental" => "", // Nilai yang dipilih (misalnya, "Baik")
                    "statusMentalScore" => 0, // Skor berdasarkan nilai yang dipilih
                ],
            ],

            "skalaHumptyDumpty" => [
                "skalaHumptyDumptyTab" => "Skala Humpty Dumpty Score",
                "skalaHumptyDumptyScore" => 0, // Total skor Skala Humpty Dumpty
                "skalaHumptyDumptyDesc" => "", // Deskripsi risiko berdasarkan skor
                "umur" => [
                    "umur" => "", // Nilai yang dipilih (misalnya, "3-7 tahun")
                    "umurScore" => 0, // Skor berdasarkan nilai yang dipilih
                ],
                "jenisKelamin" => [
                    "jenisKelamin" => "", // Nilai yang dipilih (misalnya, "Perempuan")
                    "jenisKelaminScore" => 0, // Skor berdasarkan nilai yang dipilih
                ],
                "diagnosis" => [
                    "diagnosis" => "", // Nilai yang dipilih (misalnya, "Diagnosis neurologis")
                    "diagnosisScore" => 0, // Skor berdasarkan nilai yang dipilih
                ],
                "gangguanKognitif" => [
                    "gangguanKognitif" => "", // Nilai yang dipilih (misalnya, "Gangguan ringan")
                    "gangguanKognitifScore" => 0, // Skor berdasarkan nilai yang dipilih
                ],
                "faktorLingkungan" => [
                    "faktorLingkungan" => "", // Nilai yang dipilih (misalnya, "Lingkungan berisiko sedang")
                    "faktorLingkunganScore" => 0, // Skor berdasarkan nilai yang dipilih
                ],
                "responObat" => [
                    "responObat" => "", // Nilai yang dipilih (misalnya, "Tidak ada efek samping")
                    "responObatScore" => 0, // Skor berdasarkan nilai yang dipilih
                ],
            ],
        ],

        "dekubitus" => [
            "dekubitusTab" => "Dekubitus",
            "sensoryPerception" => [
                "sensoryPerception" => "", // Nilai yang dipilih (misalnya, "Tidak ada gangguan sensorik")
                "sensoryPerceptionScore" => 0, // Skor berdasarkan nilai yang dipilih
            ],
            "moisture" => [
                "moisture" => "", // Nilai yang dipilih (misalnya, "Kulit kering")
                "moistureScore" => 0, // Skor berdasarkan nilai yang dipilih
            ],
            "activity" => [
                "activity" => "", // Nilai yang dipilih (misalnya, "Berjalan secara teratur")
                "activityScore" => 0, // Skor berdasarkan nilai yang dipilih
            ],
            "mobility" => [
                "mobility" => "", // Nilai yang dipilih (misalnya, "Mobilitas penuh")
                "mobilityScore" => 0, // Skor berdasarkan nilai yang dipilih
            ],
            "nutrition" => [
                "nutrition" => "", // Nilai yang dipilih (misalnya, "Asupan nutrisi baik")
                "nutritionScore" => 0, // Skor berdasarkan nilai yang dipilih
            ],
            "frictionShear" => [
                "frictionShear" => "", // Nilai yang dipilih (misalnya, "Tidak ada masalah gesekan")
                "frictionShearScore" => 0, // Skor berdasarkan nilai yang dipilih
            ],
            "totalScore" => 0, // Total skor dekubitus
            "riskDescription" => "", // Deskripsi risiko berdasarkan skor
        ],

        "giziTab" => "Gizi",
        "gizi" => [
            "skriningGiziAwal" => [
                "perubahanBeratBadan" => [
                    "perubahanBeratBadan" => "", // Nilai yang dipilih (misalnya, "Turun 5-10%")
                    "perubahanBeratBadanScore" => 0, // Skor berdasarkan nilai yang dipilih
                ],
                "asupanMakanan" => [
                    "asupanMakanan" => "", // Nilai yang dipilih (misalnya, "Cukup")
                    "asupanMakananScore" => 0, // Skor berdasarkan nilai yang dipilih
                ],
                "penyakit" => [
                    "penyakit" => "", // Nilai yang dipilih (misalnya, "Tidak ada")
                    "penyakitScore" => 0, // Skor berdasarkan nilai yang dipilih
                ],
            ],
            "penilaianGiziLengkap" => [
                "antropometri" => [
                    "antropometri" => "", // Nilai yang dipilih (misalnya, "Berat badan normal")
                    "antropometriScore" => 0, // Skor berdasarkan nilai yang dipilih
                ],
                "biokimia" => [
                    "biokimia" => "", // Nilai yang dipilih (misalnya, "Albumin normal")
                    "biokimiaScore" => 0, // Skor berdasarkan nilai yang dipilih
                ],
                "klinis" => [
                    "klinis" => "", // Nilai yang dipilih (misalnya, "Tidak ada tanda malnutrisi")
                    "klinisScore" => 0, // Skor berdasarkan nilai yang dipilih
                ],
            ],
            "pemantauanGizi" => [
                "perubahanBeratBadan" => [
                    "perubahanBeratBadan" => "", // Nilai yang dipilih (misalnya, "Stabil")
                    "perubahanBeratBadanScore" => 0, // Skor berdasarkan nilai yang dipilih
                ],
                "asupanMakanan" => [
                    "asupanMakanan" => "", // Nilai yang dipilih (misalnya, "Cukup")
                    "asupanMakananScore" => 0, // Skor berdasarkan nilai yang dipilih
                ],
            ],
            "intervensiGizi" => [
                "diet" => [
                    "diet" => "", // Nilai yang dipilih (misalnya, "Diet tinggi protein")
                ],
                "suplementasi" => [
                    "suplementasi" => "", // Nilai yang dipilih (misalnya, "Susu tinggi kalori")
                ],
                "nutrisiEnteral" => [
                    "nutrisiEnteral" => "", // Nilai yang dipilih (misalnya, "Nutrisi enteral standar")
                ],
                "nutrisiParenteral" => [
                    "nutrisiParenteral" => "", // Nilai yang dipilih (misalnya, "Nutrisi parenteral total")
                ],
            ],
        ],
    ];
    //////////////////////////////////////////////////////////////////////
    public array $nyeriOptions = [
        ["nyeri" => "Ya"],
        ["nyeri" => "Tidak"],
    ];

    public array $nyeriKetOptions = [
        ["nyeriKet" => "Akut"],
        ["nyeriKet" => "Kronis"],
    ];

    public array $nyeriMetodeOptions = [
        ["nyeriMetode" => "NRS"],
        ["nyeriMetode" => "BPS"],
        ["nyeriMetode" => "NIPS"],
        ["nyeriMetode" => "FLACC"],
        ["nyeriMetode" => "VAS"],
    ];

    //1. Nyeri
    public array $vasOptions = [
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
    ];

    public array $nrsOptions = [
        ["nrs" => "0", "description" => "Tidak ada nyeri"],
        ["nrs" => "1", "description" => "Nyeri sangat ringan"],
        ["nrs" => "2", "description" => "Nyeri ringan"],
        ["nrs" => "3", "description" => "Nyeri ringan hingga sedang"],
        ["nrs" => "4", "description" => "Nyeri sedang"],
        ["nrs" => "5", "description" => "Nyeri sedang hingga berat"],
        ["nrs" => "6", "description" => "Nyeri berat"],
        ["nrs" => "7", "description" => "Nyeri berat sekali"],
        ["nrs" => "8", "description" => "Nyeri sangat berat"],
        ["nrs" => "9", "description" => "Nyeri sangat berat dan tidak tertahankan"],
        ["nrs" => "10", "description" => "Nyeri terburuk yang pernah dirasakan"],
    ];

    public array $bpsOptions = [
        "ekspresiWajah" => [
            ["score" => 1, "description" => "Relaks, ekspresi netral"],
            ["score" => 2, "description" => "Sedikit tegang (mengerutkan kening, alis mengerut)"],
            ["score" => 3, "description" => "Sangat tegang (kelopak mata tertutup rapat)"],
            ["score" => 4, "description" => "Meringis (ekspresi wajah menunjukkan ketidaknyamanan ekstrem)"],
        ],
        "gerakanTubuh" => [
            ["score" => 1, "description" => "Tidak ada gerakan"],
            ["score" => 2, "description" => "Gerakan perlahan, hati-hati"],
            ["score" => 3, "description" => "Gerakan gelisah, sering berganti posisi"],
            ["score" => 4, "description" => "Gerakan agresif, mencoba melepaskan diri"],
        ],
        "kepatuhanVentilator" => [
            ["score" => 1, "description" => "Toleransi baik terhadap ventilator"],
            ["score" => 2, "description" => "Batuk sesekali, tetapi toleransi baik"],
            ["score" => 3, "description" => "Batuk berulang, sering melawan ventilator"],
            ["score" => 4, "description" => "Tidak dapat mentolerir ventilator, sering melawan"],
        ],
    ];

    public array $nipsOptions = [
        "ekspresiWajah" => [
            ["score" => 0, "description" => "Relaks, ekspresi netral"],
            ["score" => 1, "description" => "Meringis, ekspresi wajah tegang"],
        ],
        "menangis" => [
            ["score" => 0, "description" => "Tidak menangis"],
            ["score" => 1, "description" => "Merintih, mengerang"],
            ["score" => 2, "description" => "Menangis kencang"],
        ],
        "polaPernapasan" => [
            ["score" => 0, "description" => "Pernapasan normal"],
            ["score" => 1, "description" => "Pernapasan tidak teratur, cepat, atau tersedak"],
        ],
        "lengan" => [
            ["score" => 0, "description" => "Relaks, tidak ada gerakan"],
            ["score" => 1, "description" => "Lengan kaku, menekuk, atau gerakan menyentak"],
        ],
        "kaki" => [
            ["score" => 0, "description" => "Relaks, tidak ada gerakan"],
            ["score" => 1, "description" => "Kaki kaku, menekuk, atau gerakan menyentak"],
        ],
        "keadaanSadar" => [
            ["score" => 0, "description" => "Tenang, tidur, atau terjaga dengan tenang"],
            ["score" => 1, "description" => "Gelisah, rewel, atau menangis"],
        ],
    ];

    public array $flaccOptions = [
        "face" => [
            ["score" => 0, "description" => "Ekspresi wajah netral atau tersenyum"],
            ["score" => 1, "description" => "Ekspresi wajah sedikit cemberut, menarik diri"],
            ["score" => 2, "description" => "Ekspresi wajah meringis, rahang mengatup rapat"],
        ],
        "legs" => [
            ["score" => 0, "description" => "Posisi normal atau relaks"],
            ["score" => 1, "description" => "Gelisah, tegang, atau menarik kaki"],
            ["score" => 2, "description" => "Menendang, atau kaki ditarik ke arah tubuh"],
        ],
        "activity" => [
            ["score" => 0, "description" => "Berbaring tenang, posisi normal, bergerak dengan mudah"],
            ["score" => 1, "description" => "Menggeliat, bergerak bolak-balik, tegang"],
            ["score" => 2, "description" => "Melengkungkan tubuh, kaku, atau menggeliat hebat"],
        ],
        "cry" => [
            ["score" => 0, "description" => "Tidak menangis (tertidur atau terjaga)"],
            ["score" => 1, "description" => "Merintih atau mengerang, sesekali menangis"],
            ["score" => 2, "description" => "Menangis terus-menerus, berteriak, atau merintih"],
        ],
        "consolability" => [
            ["score" => 0, "description" => "Tenang, tidak perlu ditenangkan"],
            ["score" => 1, "description" => "Dapat ditenangkan dengan sentuhan atau pelukan"],
            ["score" => 2, "description" => "Sulit ditenangkan, terus menangis atau merintih"],
        ],
    ];

    //2. Gizi
    public array $skriningGiziAwalOptions = [
        "perubahanBeratBadan" => [
            ["perubahan" => "Tidak ada perubahan", "score" => 0],
            ["perubahan" => "Turun 5-10%", "score" => 1],
            ["perubahan" => "Turun >10%", "score" => 2],
        ],
        "asupanMakanan" => [
            ["asupan" => "Cukup", "score" => 0],
            ["asupan" => "Kurang", "score" => 1],
            ["asupan" => "Sangat kurang", "score" => 2],
        ],
        "penyakit" => [
            ["penyakit" => "Tidak ada", "score" => 0],
            ["penyakit" => "Ringan", "score" => 1],
            ["penyakit" => "Berat", "score" => 2],
        ],
    ];

    public array $penilaianGiziLengkapOptions = [
        "antropometri" => [
            ["kategori" => "Berat badan normal", "score" => 3],
            ["kategori" => "Berat badan kurang", "score" => 2],
            ["kategori" => "Berat badan sangat kurang", "score" => 1],
        ],
        "biokimia" => [
            ["kategori" => "Albumin normal", "score" => 3],
            ["kategori" => "Albumin rendah", "score" => 2],
            ["kategori" => "Albumin sangat rendah", "score" => 1],
        ],
        "klinis" => [
            ["kategori" => "Tidak ada tanda malnutrisi", "score" => 3],
            ["kategori" => "Tanda malnutrisi ringan", "score" => 2],
            ["kategori" => "Tanda malnutrisi berat", "score" => 1],
        ],
    ];

    public array $pemantauanGiziOptions = [
        "perubahanBeratBadan" => [
            ["perubahan" => "Stabil", "score" => 3],
            ["perubahan" => "Turun sedikit", "score" => 2],
            ["perubahan" => "Turun signifikan", "score" => 1],
        ],
        "asupanMakanan" => [
            ["asupan" => "Cukup", "score" => 3],
            ["asupan" => "Kurang", "score" => 2],
            ["asupan" => "Sangat kurang", "score" => 1],
        ],
    ];

    public array $intervensiGiziOptions = [
        "diet" => [
            ["jenis" => "Diet biasa"],
            ["jenis" => "Diet tinggi protein"],
            ["jenis" => "Diet rendah garam"],
        ],
        "suplementasi" => [
            ["jenis" => "Susu tinggi kalori"],
            ["jenis" => "Suplemen vitamin"],
        ],
        "nutrisiEnteral" => [
            ["jenis" => "Nutrisi enteral standar"],
            ["jenis" => "Nutrisi enteral khusus"],
        ],
        "nutrisiParenteral" => [
            ["jenis" => "Nutrisi parenteral parsial"],
            ["jenis" => "Nutrisi parenteral total"],
        ],
    ];

    // Contoh Alur Penilaian Gizi di Rawat Inap
    // Hari 1 (Skrining Awal):
    //     Lakukan skrining gizi menggunakan alat seperti MUST atau NRS-2002.
    //     Identifikasi pasien yang berisiko malnutrisi.
    // Hari 2-3 (Penilaian Lengkap):
    //     Jika skrining menunjukkan risiko, lakukan penilaian gizi lengkap.
    //     Tentukan intervensi gizi yang sesuai.
    // Hari 4-7 (Pemantauan Berkala):
    //     Pantau perubahan berat badan dan asupan makanan.
    //     Evaluasi respons terhadap intervensi gizi.
    // Hari 8 dan Seterusnya:
    //     Lanjutkan pemantauan dan sesuaikan intervensi gizi jika diperlukan.


    public array $statusPediatrikOptions = [
        "statusGizi" => [
            ["statusGizi" => "Gizi Buruk", "score" => 1],
            ["statusGizi" => "Gizi Kurang", "score" => 2],
            ["statusGizi" => "Gizi Normal", "score" => 3],
            ["statusGizi" => "Gizi Lebih", "score" => 4],
            ["statusGizi" => "Obesitas", "score" => 5],
        ],
        "perkembangan" => [
            ["perkembangan" => "Sesuai Usia", "score" => 3],
            ["perkembangan" => "Meragukan", "score" => 2],
            ["perkembangan" => "Menyimpang", "score" => 1],
        ],
        "kesehatanUmum" => [
            ["kesehatanUmum" => "Sehat", "score" => 3],
            ["kesehatanUmum" => "Kurang Sehat", "score" => 2],
            ["kesehatanUmum" => "Tidak Sehat", "score" => 1],
        ],
    ];
    // Baik: Total skor 7-9.
    // Kurang: Total skor 4-6.
    // Buruk: Total skor 1-3.

    //3. Resiko Jatuh (Skala Morse)
    public array $skalaMorseOptions = [
        "riwayatJatuh" => [
            ["riwayatJatuh" => "Ya", "score" => 25],
            ["riwayatJatuh" => "Tidak", "score" => 0],
        ],
        "diagnosisSekunder" => [
            ["diagnosisSekunder" => "Ya", "score" => 15],
            ["diagnosisSekunder" => "Tidak", "score" => 0],
        ],
        "alatBantu" => [
            ["alatBantu" => "Tidak Ada / Bed Rest", "score" => 0],
            ["alatBantu" => "Tongkat / Alat Penopang / Walker", "score" => 15],
            ["alatBantu" => "Furnitur", "score" => 30],
        ],
        "terapiIV" => [
            ["terapiIV" => "Ya", "score" => 20],
            ["terapiIV" => "Tidak", "score" => 0],
        ],
        "gayaBerjalan" => [
            ["gayaBerjalan" => "Normal / Tirah Baring / Tidak Bergerak", "score" => 0],
            ["gayaBerjalan" => "Lemah", "score" => 10],
            ["gayaBerjalan" => "Terganggu", "score" => 20],
        ],
        "statusMental" => [
            ["statusMental" => "Baik", "score" => 0],
            ["statusMental" => "Lupa / Pelupa", "score" => 15],
        ],
    ];

    // 0-24: Risiko rendah.
    // 25-44: Risiko sedang.
    // 45+: Risiko tinggi.


    //4. Resiko Jatuh (Skala Humpty Dumpty)
    public array $humptyDumptyOptions = [
        "umur" => [
            ["umur" => "< 3 tahun", "score" => 4],
            ["umur" => "3-7 tahun", "score" => 3],
            ["umur" => "7-13 tahun", "score" => 2],
            ["umur" => "13-18 tahun", "score" => 1],
        ],
        "jenisKelamin" => [
            ["jenisKelamin" => "Laki-laki", "score" => 2],
            ["jenisKelamin" => "Perempuan", "score" => 1],
        ],
        "diagnosis" => [
            ["diagnosis" => "Diagnosis neurologis atau perkembangan", "score" => 4],
            ["diagnosis" => "Diagnosis ortopedi", "score" => 3],
            ["diagnosis" => "Diagnosis lainnya", "score" => 2],
            ["diagnosis" => "Tidak ada diagnosis khusus", "score" => 1],
        ],
        "gangguanKognitif" => [
            ["gangguanKognitif" => "Gangguan kognitif berat", "score" => 3],
            ["gangguanKognitif" => "Gangguan kognitif sedang", "score" => 2],
            ["gangguanKognitif" => "Gangguan kognitif ringan", "score" => 1],
            ["gangguanKognitif" => "Tidak ada gangguan kognitif", "score" => 0],
        ],
        "faktorLingkungan" => [
            ["faktorLingkungan" => "Lingkungan berisiko tinggi (misalnya, lantai licin, peralatan medis)", "score" => 3],
            ["faktorLingkungan" => "Lingkungan berisiko sedang", "score" => 2],
            ["faktorLingkungan" => "Lingkungan berisiko rendah", "score" => 1],
            ["faktorLingkungan" => "Lingkungan aman", "score" => 0],
        ],
        "responObat" => [
            ["responObat" => "Efek samping obat yang meningkatkan risiko jatuh", "score" => 3],
            ["responObat" => "Efek samping obat ringan", "score" => 2],
            ["responObat" => "Tidak ada efek samping obat", "score" => 1],
        ],
    ];
    // 7-11: Risiko rendah.
    // 12-14: Risiko sedang.
    // 15-19: Risiko tinggi.

    // 5. Dekubitus
    public array $bradenScaleOptions = [
        "sensoryPerception" => [
            ["score" => 4, "description" => "Tidak ada gangguan sensorik"],
            ["score" => 3, "description" => "Gangguan sensorik ringan"],
            ["score" => 2, "description" => "Gangguan sensorik sedang"],
            ["score" => 1, "description" => "Gangguan sensorik berat"],
        ],
        "moisture" => [
            ["score" => 4, "description" => "Kulit kering"],
            ["score" => 3, "description" => "Kulit lembab"],
            ["score" => 2, "description" => "Kulit basah"],
            ["score" => 1, "description" => "Kulit sangat basah"],
        ],
        "activity" => [
            ["score" => 4, "description" => "Berjalan secara teratur"],
            ["score" => 3, "description" => "Berjalan dengan bantuan"],
            ["score" => 2, "description" => "Duduk di kursi"],
            ["score" => 1, "description" => "Terbaring di tempat tidur"],
        ],
        "mobility" => [
            ["score" => 4, "description" => "Mobilitas penuh"],
            ["score" => 3, "description" => "Mobilitas sedikit terbatas"],
            ["score" => 2, "description" => "Mobilitas sangat terbatas"],
            ["score" => 1, "description" => "Tidak bisa bergerak"],
        ],
        "nutrition" => [
            ["score" => 4, "description" => "Asupan nutrisi baik"],
            ["score" => 3, "description" => "Asupan nutrisi cukup"],
            ["score" => 2, "description" => "Asupan nutrisi kurang"],
            ["score" => 1, "description" => "Asupan nutrisi sangat kurang"],
        ],
        "frictionShear" => [
            ["score" => 3, "description" => "Tidak ada masalah gesekan atau geseran"],
            ["score" => 2, "description" => "Potensi masalah gesekan atau geseran"],
            ["score" => 1, "description" => "Masalah gesekan atau geseran yang signifikan"],
        ],
    ];
    // 19-23: Risiko rendah.
    // 15-18: Risiko sedang.
    // 13-14: Risiko tinggi.
    // ≤12: Risiko sangat tinggi.

    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////


    public function updated($propertyName)
    {
        // dd($propertyName);
        // $this->validateOnly($propertyName);
        $this->scoringSkalaMorse();
        $this->scoringSkalaHumptyDumpty();
        $this->store();
    }



    // ////////////////
    // RJ Logic
    // ////////////////


    // validate Data RJ//////////////////////////////////////////////////
    private function validateDataRi(): void
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
        $this->validateDataRi();

        // Logic update mode start //////////
        $this->updateDataRi($this->dataDaftarRi['riHdrNo']);

        $this->emit('syncronizeAssessmentPerawatRIFindData');
    }

    private function updateDataRi($riHdrNo): void
    {
        $this->updateJsonRI($riHdrNo, $this->dataDaftarRi);

        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Penilaian berhasil disimpan.");
    }
    // insert and update record end////////////////


    private function findData($rjno): void
    {


        $this->dataDaftarRi = $this->findDataRI($rjno);
        // dd($this->dataDaftarRi);
        // jika pemeriksaan tidak ditemukan tambah variable pemeriksaan pda array
        if (isset($this->dataDaftarRi['penilaian']) == false) {
            $this->dataDaftarRi['penilaian'] = $this->penilaian;
        }
    }

    // set data RJno / NoBooking / NoAntrian / klaimId / kunjunganId
    private function setDataPrimer(): void {}

    private function scoringSkalaMorse(): void
    {
        $riwayatJatuh3blnTerakhirScore = $this->dataDaftarRi['penilaian']['resikoJatuh']['skalaMorse']['riwayatJatuh3blnTerakhirScore'];
        $diagSekunderScore = $this->dataDaftarRi['penilaian']['resikoJatuh']['skalaMorse']['diagSekunderScore'];
        $alatBantuScore = $this->dataDaftarRi['penilaian']['resikoJatuh']['skalaMorse']['alatBantuScore'];
        $heparinScore = $this->dataDaftarRi['penilaian']['resikoJatuh']['skalaMorse']['heparinScore'];
        $gayaBerjalanScore = $this->dataDaftarRi['penilaian']['resikoJatuh']['skalaMorse']['gayaBerjalanScore'];
        $kesadaranScore = $this->dataDaftarRi['penilaian']['resikoJatuh']['skalaMorse']['kesadaranScore'];

        $this->dataDaftarRi['penilaian']['resikoJatuh']['skalaMorse']['skalaMorseScore'] = $riwayatJatuh3blnTerakhirScore + $diagSekunderScore  + $alatBantuScore + $heparinScore + $gayaBerjalanScore + $kesadaranScore;
        $this->dataDaftarRi['penilaian']['resikoJatuh']['skalaMorse']['skalaMorseDesc'] =
            ($this->dataDaftarRi['penilaian']['resikoJatuh']['skalaMorse']['skalaMorseScore'] >= 0
                && $this->dataDaftarRi['penilaian']['resikoJatuh']['skalaMorse']['skalaMorseScore'] <= 24
                ? 'Tidak Ada Risiko'
                : ($this->dataDaftarRi['penilaian']['resikoJatuh']['skalaMorse']['skalaMorseScore'] >= 25
                    && $this->dataDaftarRi['penilaian']['resikoJatuh']['skalaMorse']['skalaMorseScore'] <= 50
                    ? 'Risiko Rendah'
                    : 'Risiko Tinggi'));
    }

    private function scoringSkalaHumptyDumpty(): void
    {
        $umurScore = $this->dataDaftarRi['penilaian']['resikoJatuh']['skalaHumptyDumpty']['umurScore'];
        $sexScore = $this->dataDaftarRi['penilaian']['resikoJatuh']['skalaHumptyDumpty']['sexScore'];
        $diagnosaScore = $this->dataDaftarRi['penilaian']['resikoJatuh']['skalaHumptyDumpty']['diagnosaScore'];
        $gangguanKognitifScore = $this->dataDaftarRi['penilaian']['resikoJatuh']['skalaHumptyDumpty']['gangguanKognitifScore'];
        $faktorLingkunganScore = $this->dataDaftarRi['penilaian']['resikoJatuh']['skalaHumptyDumpty']['faktorLingkunganScore'];
        $penggunaanObatScore = $this->dataDaftarRi['penilaian']['resikoJatuh']['skalaHumptyDumpty']['penggunaanObatScore'];
        $responTerhadapOperasiScore = $this->dataDaftarRi['penilaian']['resikoJatuh']['skalaHumptyDumpty']['responTerhadapOperasiScore'];

        $this->dataDaftarRi['penilaian']['resikoJatuh']['skalaHumptyDumpty']['skalaHumptyDumptyScore'] = $umurScore + $sexScore  + $diagnosaScore + $gangguanKognitifScore + $faktorLingkunganScore + $penggunaanObatScore +
            $responTerhadapOperasiScore;
        $this->dataDaftarRi['penilaian']['resikoJatuh']['skalaHumptyDumpty']['skalaHumptyDumptyDesc'] =
            ($this->dataDaftarRi['penilaian']['resikoJatuh']['skalaHumptyDumpty']['skalaHumptyDumptyScore'] >= 0
                && $this->dataDaftarRi['penilaian']['resikoJatuh']['skalaHumptyDumpty']['skalaHumptyDumptyScore'] <= 11
                ? 'Risiko Rendah'
                : ($this->dataDaftarRi['penilaian']['resikoJatuh']['skalaHumptyDumpty']['skalaHumptyDumptyScore'] >= 12
                    && $this->dataDaftarRi['penilaian']['resikoJatuh']['skalaHumptyDumpty']['skalaHumptyDumptyScore'] <= 12
                    ? 'Risiko Tinggi'
                    : 'Risiko Tinggi'));
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
            'livewire.emr-r-i.mr-r-i.penilaian.penilaian',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Anamnesia',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Pasien Rawat Inap',
            ]
        );
    }
    // select data end////////////////


}
