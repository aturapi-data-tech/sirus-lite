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
        "nyeri" => [
            // ... (data nyeri)
        ],

        // Bagian lainnya tetap sama...
        "statusPediatrik" => [
            // ... (data status pediatrik)
        ],

        "diagnosis" => [
            // ... (data diagnosis)
        ],

        "resikoJatuh" => [
            // ... (data risiko jatuh)
        ],

        "dekubitus" => [
            // ... (data dekubitus)
        ],

        "gizi" => [
            // ... (data gizi)
        ],
    ];




    public array $formEntryStatusPediatrik = [
        "tglPenilaian" => "", // Tanggal penilaian
        "petugasPenilai" => "", // Nama petugas penilai
        "petugasPenilaiCode" => "", // Kode petugas penilai
        "statusGizi" => "", // Status gizi (misalnya, "Gizi Buruk", "Gizi Normal")
        "perkembangan" => "", // Perkembangan (misalnya, "Sesuai Usia", "Menyimpang")
        "kesehatanUmum" => "", // Kesehatan umum (misalnya, "Sehat", "Tidak Sehat")
        "catatanKhusus" => "", // Catatan khusus terkait status pediatrik
    ];

    public array $formEntryDiagnosis = [
        "tglDiagnosis" => "", // Tanggal diagnosis
        "dokter" => "", // Nama dokter yang mendiagnosis
        "dokterCode" => "", // Kode dokter
        "diagnosisUtama" => "", // Diagnosis utama
        "diagnosisSekunder" => "", // Diagnosis sekunder
        "catatan" => "", // Catatan tambahan terkait diagnosis
    ];









    //////RESIKO JATUH/////////////////////////////////////////////////////////////////////////////////////
    public array $formEntryResikoJatuh = [
        "tglPenilaian" => "", // Tanggal penilaian
        "petugasPenilai" => "", // Nama petugas penilai
        "petugasPenilaiCode" => "", // Kode petugas penilai
        "resikoJatuh" => [
            "resikoJatuh" => "Tidak", // Apakah pasien memiliki risiko jatuh? (misalnya, "Ya" atau "Tidak")
            "resikoJatuhMetode" => [ // Array untuk menyimpan metode penilaian risiko jatuh
                "resikoJatuhMetode" => "", // Metode yang dipilih (misalnya, "Skala Morse", "Skala Humpty Dumpty")
                "resikoJatuhMetodeScore" => 0, // Skor berdasarkan metode yang dipilih
                "dataResikoJatuh" => [ // Data detail risiko jatuh sesuai metode
                    // Data akan diisi berdasarkan metode yang dipilih
                ],
            ],
            "kategoriResiko" => "", // Kategori risiko (misalnya, "Rendah", "Sedang", "Tinggi")
            "rekomendasi" => "", // Rekomendasi penanganan
        ],
    ];

    //Resiko Jatuh (Skala Morse)
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


    //Resiko Jatuh (Skala Humpty Dumpty)
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


    public function hitungSkorDanKategoriSkalaMorse()
    {
        $dataResikoJatuh = $this->formEntryResikoJatuh['resikoJatuh']['resikoJatuhMetode']['dataResikoJatuh'];
        $skor = 0;

        // Hitung skor berdasarkan pilihan pengguna
        foreach ($this->skalaMorseOptions as $kategori => $options) {
            if (isset($dataResikoJatuh[$kategori])) {
                foreach ($options as $option) {
                    if ($option[$kategori] === $dataResikoJatuh[$kategori]) {
                        $skor += $option['score'];
                        break;
                    }
                }
            }
        }

        // Update skor
        $this->formEntryResikoJatuh['resikoJatuh']['resikoJatuhMetode']['resikoJatuhMetodeScore'] = $skor;

        // Tentukan kategori risiko
        if ($skor >= 45) {
            $kategori = 'Tinggi';
        } elseif ($skor >= 25) {
            $kategori = 'Sedang';
        } else {
            $kategori = 'Rendah';
        }

        // Update kategori risiko
        $this->formEntryResikoJatuh['resikoJatuh']['kategoriResiko'] = $kategori;
    }

    public function hitungSkorDanKategoriSkalaHumptyDumpty()
    {
        // Ambil data risiko jatuh dari form
        $dataResikoJatuh = $this->formEntryResikoJatuh['resikoJatuh']['resikoJatuhMetode']['dataResikoJatuh'];
        $skor = 0;

        // Hitung skor berdasarkan pilihan pengguna
        foreach ($this->humptyDumptyOptions as $kategori => $options) {
            if (isset($dataResikoJatuh[$kategori])) {
                foreach ($options as $option) {
                    if ($option[$kategori] === $dataResikoJatuh[$kategori]) {
                        $skor += $option['score'];
                        break;
                    }
                }
            }
        }

        // Update skor
        $this->formEntryResikoJatuh['resikoJatuh']['resikoJatuhMetode']['resikoJatuhMetodeScore'] = $skor;

        // Tentukan kategori risiko
        if ($skor >= 16) {
            $kategori = 'Tinggi';
        } elseif ($skor >= 12) {
            $kategori = 'Sedang';
        } else {
            $kategori = 'Rendah';
        }

        // Update kategori risiko
        $this->formEntryResikoJatuh['resikoJatuh']['kategoriResiko'] = $kategori;
    }

    public function updatedFormEntryResikoJatuhResikoJatuhResikoJatuhMetodeResikoJatuhMetode($value)
    {
        // Reset seluruh dataResikoJatuh
        $this->formEntryResikoJatuh['resikoJatuh']['resikoJatuhMetode']['dataResikoJatuh'] = [];

        // Isi dataResikoJatuh berdasarkan metode yang dipilih
        $this->defaultFormEntryResikoJatuh($value);

        // Reset nilai resikoJatuhMetodeScore menjadi 0
        $this->formEntryResikoJatuh['resikoJatuh']['resikoJatuhMetode']['resikoJatuhMetodeScore'] = 0;

        // Reset kategori risiko
        $this->formEntryResikoJatuh['resikoJatuh']['kategoriResiko'] = '';
    }

    protected function defaultFormEntryResikoJatuh($metode)
    {
        if ($metode === 'Skala Morse') {
            $this->formEntryResikoJatuh['resikoJatuh']['resikoJatuhMetode']['dataResikoJatuh'] = $this->skalaMorseOptions;
        } elseif ($metode === 'Skala Humpty Dumpty') {
            $this->formEntryResikoJatuh['resikoJatuh']['resikoJatuhMetode']['dataResikoJatuh'] = $this->humptyDumptyOptions;
        }
    }

    public function setTglPenilaianResikoJatuh($tanggal)
    {
        // Set tanggal penilaian ke nilai yang diterima
        $this->formEntryResikoJatuh['tglPenilaian'] = $tanggal;
    }

    // ADD REMOVE RESIKO JATUH////////////////////////////////////
    public function addAssessmentResikoJatuh(): void
    {
        // Aturan validasi
        $rules = [
            'formEntryResikoJatuh.tglPenilaian' => 'required|date_format:d/m/Y H:i:s', // Tanggal penilaian harus diisi dan berupa tanggal
            'formEntryResikoJatuh.petugasPenilai' => 'required|string|max:100', // Nama petugas penilai harus diisi, berupa string, maksimal 100 karakter
            'formEntryResikoJatuh.petugasPenilaiCode' => 'required|string|max:50', // Kode petugas penilai harus diisi, berupa string, maksimal 50 karakter
            'formEntryResikoJatuh.resikoJatuh.resikoJatuh' => 'required|in:Ya,Tidak', // Risiko jatuh harus diisi dan hanya boleh "Ya" atau "Tidak"
            'formEntryResikoJatuh.resikoJatuh.resikoJatuhMetode.resikoJatuhMetode' => 'required_if:formEntryResikoJatuh.resikoJatuh.resikoJatuh,Ya|string|max:50', // Metode penilaian harus diisi jika risiko jatuh "Ya", berupa string, maksimal 50 karakter
            'formEntryResikoJatuh.resikoJatuh.resikoJatuhMetode.resikoJatuhMetodeScore' => 'required_if:formEntryResikoJatuh.resikoJatuh.resikoJatuh,Ya|numeric|min:0|max:100', // Skor risiko jatuh harus diisi jika risiko jatuh "Ya", berupa angka, minimal 0, maksimal 100
            'formEntryResikoJatuh.resikoJatuh.kategoriResiko' => 'nullable|string|max:100', // Kategori risiko opsional, berupa string, maksimal 100 karakter
            'formEntryResikoJatuh.resikoJatuh.rekomendasi' => 'nullable|string|max:500', // Rekomendasi opsional, berupa string, maksimal 500 karakter
        ];

        $messages = [];

        // Proses Validasi
        try {
            $this->validate($rules, $messages);
        } catch (\Illuminate\Validation\ValidationException $e) {

            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError("Lakukan pengecekan kembali input data. " . $e->getMessage());
            $this->validate($rules, $messages);
        }

        // Tambahkan data risiko jatuh ke dalam $dataDaftarRi
        $this->dataDaftarRi['penilaian']['resikoJatuh'][] = $this->formEntryResikoJatuh;

        // Simpan perubahan ke penyimpanan (misalnya, database atau session)
        $this->store();

        // Reset form entry risiko jatuh setelah data berhasil ditambahkan
        $this->reset(['formEntryResikoJatuh']);

        // Tampilkan pesan sukses
        toastr()
            ->closeOnHover(true)
            ->closeDuration(3)
            ->positionClass('toast-top-left')
            ->addSuccess("Data risiko jatuh berhasil ditambahkan.");
    }


    public function removeAssessmentResikoJatuh($index): void
    {
        // Pastikan data assessment risiko jatuh ada dan merupakan array
        if (isset($this->dataDaftarRi['penilaian']['resikoJatuh']) && is_array($this->dataDaftarRi['penilaian']['resikoJatuh'])) {
            // Hapus data berdasarkan index
            unset($this->dataDaftarRi['penilaian']['resikoJatuh'][$index]);

            // Reset indeks array setelah penghapusan
            $this->dataDaftarRi['penilaian']['resikoJatuh'] = array_values($this->dataDaftarRi['penilaian']['resikoJatuh']);
        }

        // Simpan perubahan ke penyimpanan (misalnya, database atau session)
        $this->store();
    }

    //////RESIKO JATUH/////////////////////////////////////////////////////////////////////////////////////
















    public array $formEntryDekubitus = [
        "tglPenilaian" => "", // Tanggal penilaian
        "petugasPenilai" => "", // Nama petugas penilai
        "petugasPenilaiCode" => "", // Kode petugas penilai
        "lokasiDekubitus" => "", // Lokasi dekubitus
        "stadium" => "", // Stadium dekubitus (misalnya, "Stadium 1", "Stadium 2")
        "ukuran" => "", // Ukuran luka (misalnya, "5x5 cm")
        "penanganan" => "", // Penanganan yang dilakukan
    ];

    public array $formEntryGizi = [
        "tglPenilaian" => "", // Tanggal penilaian
        "petugasPenilai" => "", // Nama petugas penilai
        "petugasPenilaiCode" => "", // Kode petugas penilai
        "beratBadan" => "", // Berat badan (number)
        "tinggiBadan" => "", // Tinggi badan (number)
        "imt" => "", // Indeks Massa Tubuh (IMT)
        "kebutuhanGizi" => "", // Kebutuhan gizi harian
        "catatan" => "", // Catatan tambahan terkait gizi
    ];
























    ////NYERI//////////////////////////////////////////////////////////////////
    public array $nyeriOptions = [
        ["nyeri" => "Ya"],
        ["nyeri" => "Tidak"],
    ];
    private function defaultFormEntryNyeri($value)
    {
        // Isi dataNyeri berdasarkan metode yang dipilih
        if ($value === 'VAS') {
            $this->formEntryNyeri['nyeri']['nyeriMetode']['dataNyeri'] = $this->vasOptions;
        } elseif ($value === 'FLACC') {
            $this->formEntryNyeri['nyeri']['nyeriMetode']['dataNyeri'] = $this->flaccOptions;
        }
    }

    public array $formEntryNyeri = [
        "tglPenilaian" => "", // Tanggal penilaian
        "petugasPenilai" => "", // Nama petugas penilai
        "petugasPenilaiCode" => "", // Kode petugas penilai
        "nyeri" => [
            "nyeri" => "Tidak", // Apakah pasien mengalami nyeri? (misalnya, "Ya" atau "Tidak")
            "nyeriMetode" => [ // Array untuk menyimpan metode penilaian nyeri
                "nyeriMetode" => "", // Metode yang dipilih (misalnya, "NRS", "BPS", dll.)
                "nyeriMetodeScore" => 0, // Skor berdasarkan metode yang dipilih
                "dataNyeri" => [], // Data detail nyeri sesuai metode (kosong, siap diisi)
            ],
            "nyeriKet" => "", // Jenis nyeri (misalnya, "Akut" atau "Kronis")
            "pencetus" => "", // Pencetus nyeri
            "durasi" => "", // Durasi nyeri
            "lokasi" => "", // Lokasi nyeri
            "waktuNyeri" => "", // Waktu terjadinya nyeri (misalnya, "Pagi", "Siang", "Malam")
            "tingkatKesadaran" => "", // Tingkat kesadaran pasien (misalnya, "Sadar", "Bingung")
            "tingkatAktivitas" => "", // Tingkat aktivitas pasien (misalnya, "Aktif", "Pasif")
            "sistolik" => "", // Tekanan darah sistolik (number)
            "distolik" => "", // Tekanan darah diastolik (number)
            "frekuensiNafas" => "", // Frekuensi nafas (number)
            "frekuensiNadi" => "", // Frekuensi nadi (number)
            "suhu" => "", // Suhu tubuh (number)
            "ketIntervensiFarmakologi" => "", // Keterangan intervensi farmakologi
            "ketIntervensiNonFarmakologi" => "", // Keterangan intervensi non-farmakologi
            "catatanTambahan" => "", // Catatan tambahan terkait nyeri
        ],
    ];

    public function setTglPenilaianNyeri($tanggal)
    {
        $this->formEntryNyeri['tglPenilaian'] = $tanggal;
    }

    public array $nyeriMetodeOptions = [
        ["nyeriMetode" => "NRS"],
        ["nyeriMetode" => "BPS"],
        ["nyeriMetode" => "NIPS"],
        ["nyeriMetode" => "FLACC"],
        ["nyeriMetode" => "VAS"],
    ];

    //////METODE PENILAIAN NYERI////////////////////////////////////////////////////////////////
    public function updatedFormEntryNyeriNyeriNyeriMetodeNyeriMetode($value)
    {
        // Reset seluruh dataNyeri
        $this->formEntryNyeri['nyeri']['nyeriMetode']['dataNyeri'] = [];

        // Isi dataNyeri berdasarkan metode yang dipilih
        $this->defaultFormEntryNyeri($value);

        // Reset nilai nyeriMetodeScore menjadi 0
        $this->formEntryNyeri['nyeri']['nyeriMetode']['nyeriMetodeScore'] = 0;
        $this->formEntryNyeri['nyeri']['nyeriKet'] = 'Tidak Nyeri';
    }




    ////VAS///////////////////////////////////////////////////////////////////
    public array $vasOptions = [
        ["vas" => "0", "active" => true],
        ["vas" => "1", "active" => false],
        ["vas" => "2", "active" => false],
        ["vas" => "3", "active" => false],
        ["vas" => "4", "active" => false],
        ["vas" => "5", "active" => false],
        ["vas" => "6", "active" => false],
        ["vas" => "7", "active" => false],
        ["vas" => "8", "active" => false],
        ["vas" => "9", "active" => false],
        ["vas" => "10", "active" => false],
    ];

    public function updateVasNyeriScore($score)
    {

        // Reset semua nilai `active` menjadi false
        foreach ($this->formEntryNyeri['nyeri']['nyeriMetode']['dataNyeri'] as &$option) {
            $option['active'] = false;
        }

        // Set nilai `active` menjadi true untuk skor yang dipilih
        foreach ($this->formEntryNyeri['nyeri']['nyeriMetode']['dataNyeri'] as &$option) {
            if ($option['vas'] == $score) {
                $option['active'] = true;
                break;
            }
        }
        // Simpan skor yang dipilih
        $this->formEntryNyeri['nyeri']['nyeriMetode']['nyeriMetodeScore'] = $score;
        $this->formEntryNyeri['nyeri']['nyeriKet'] = $this->getJenisNyeriVas($score);
    }

    private function getJenisNyeriVas($score)
    {
        if ($score == 0) {
            return "Tidak Nyeri";
        } elseif ($score >= 1 && $score <= 3) {
            return "Nyeri Ringan";
        } elseif ($score >= 4 && $score <= 6) {
            return "Nyeri Sedang";
        } elseif ($score >= 7 && $score <= 10) {
            return "Nyeri Berat";
        }
        return "";
    }




    ////NRS///////////////////////////////////////////////////////////////////
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




    ////BPS///////////////////////////////////////////////////////////////////
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




    ////NIPS///////////////////////////////////////////////////////////////////
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




    ////FLACC///////////////////////////////////////////////////////////////////
    public array $flaccOptions = [
        "face" => [
            ["score" => 0, "description" => "Ekspresi wajah netral atau tersenyum", "active" => false],
            ["score" => 1, "description" => "Ekspresi wajah sedikit cemberut, menarik diri", "active" => false],
            ["score" => 2, "description" => "Ekspresi wajah meringis, rahang mengatup rapat", "active" => false],
        ],
        "legs" => [
            ["score" => 0, "description" => "Posisi normal atau relaks", "active" => false],
            ["score" => 1, "description" => "Gelisah, tegang, atau menarik kaki", "active" => false],
            ["score" => 2, "description" => "Menendang, atau kaki ditarik ke arah tubuh", "active" => false],
        ],
        "activity" => [
            ["score" => 0, "description" => "Berbaring tenang, posisi normal, bergerak dengan mudah", "active" => false],
            ["score" => 1, "description" => "Menggeliat, bergerak bolak-balik, tegang", "active" => false],
            ["score" => 2, "description" => "Melengkungkan tubuh, kaku, atau menggeliat hebat", "active" => false],
        ],
        "cry" => [
            ["score" => 0, "description" => "Tidak menangis (tertidur atau terjaga)", "active" => false],
            ["score" => 1, "description" => "Merintih atau mengerang, sesekali menangis", "active" => false],
            ["score" => 2, "description" => "Menangis terus-menerus, berteriak, atau merintih", "active" => false],
        ],
        "consolability" => [
            ["score" => 0, "description" => "Tenang, tidak perlu ditenangkan", "active" => false],
            ["score" => 1, "description" => "Dapat ditenangkan dengan sentuhan atau pelukan", "active" => false],
            ["score" => 2, "description" => "Sulit ditenangkan, terus menangis atau merintih", "active" => false],
        ],
    ];

    // Update Skor FLACC
    public function updateFlaccScore($category, $score)
    {
        foreach ($this->formEntryNyeri['nyeri']['nyeriMetode']['dataNyeri'][$category] as $key => &$item) {
            if ($score !== $item['score']) {
                $item['active'] = false;
            }
        }

        // // Hitung total skor FLACC
        $this->calculateFlaccTotalScore();
    }

    // Hitung Total Skor FLACC
    private function calculateFlaccTotalScore()
    {
        $totalScore = 0;
        foreach ($this->formEntryNyeri['nyeri']['nyeriMetode']['dataNyeri'] as $category => $options) {
            foreach ($options as $option) {
                if ($option['active']) {
                    $totalScore += $option['score'];
                    break;
                }
            }
        }
        $this->formEntryNyeri['nyeri']['nyeriMetode']['nyeriMetodeScore'] = $totalScore;

        $this->formEntryNyeri['nyeri']['nyeriKet'] = $this->getJenisNyeriFlacc($totalScore);
    }

    private function getJenisNyeriFlacc($score)
    {
        if ($score == 0) {
            return "Santai dan nyaman";
        } elseif ($score >= 1 && $score <= 3) {
            return "Ketidaknyamanan ringan";
        } elseif ($score >= 4 && $score <= 6) {
            return "Nyeri sedang";
        } elseif ($score >= 7 && $score <= 10) {
            return "Nyeri berat";
        }
        return "";
    }
    ////////ADD/REMOVE ASSESSMENT NYERI///////////////////////////////////////////////////////
    public function addAssessmentNyeri(): void
    {
        $rules = [
            'formEntryNyeri.tglPenilaian' => 'required|date_format:d/m/Y H:i:s', // Tanggal penilaian harus diisi dan berupa tanggal
            'formEntryNyeri.petugasPenilai' => 'required|string|max:100', // Nama petugas penilai harus diisi, berupa string, maksimal 100 karakter
            'formEntryNyeri.petugasPenilaiCode' => 'required|string|max:50', // Kode petugas penilai harus diisi, berupa string, maksimal 50 karakter
            'formEntryNyeri.nyeri.nyeri' => 'required|in:Ya,Tidak', // Nyeri harus diisi dan hanya boleh "Ya" atau "Tidak"
            'formEntryNyeri.nyeri.nyeriMetode.nyeriMetode' => 'required_if:formEntryNyeri.nyeri.nyeri,Ya|string|max:50', // Metode nyeri harus diisi jika nyeri "Ya", berupa string, maksimal 50 karakter
            'formEntryNyeri.nyeri.nyeriMetode.nyeriMetodeScore' => 'required_if:formEntryNyeri.nyeri.nyeri,Ya|numeric|min:0|max:100', // Skor nyeri harus diisi jika nyeri "Ya", berupa angka, minimal 0, maksimal 10
            'formEntryNyeri.nyeri.nyeriMetode.dataNyeri' => 'nullable|array', // Data nyeri opsional, harus berupa array
            'formEntryNyeri.nyeri.nyeriKet' => 'nullable|string|max:100', // Jenis nyeri opsional, berupa string, maksimal 100 karakter
            'formEntryNyeri.nyeri.pencetus' => 'nullable|string|max:255', // Pencetus nyeri opsional, berupa string, maksimal 255 karakter
            'formEntryNyeri.nyeri.durasi' => 'nullable|string|max:50', // Durasi nyeri opsional, berupa string, maksimal 50 karakter
            'formEntryNyeri.nyeri.lokasi' => 'nullable|string|max:100', // Lokasi nyeri opsional, berupa string, maksimal 100 karakter
            'formEntryNyeri.nyeri.waktuNyeri' => 'nullable|string|max:50', // Waktu nyeri opsional, berupa string, maksimal 50 karakter
            'formEntryNyeri.nyeri.tingkatKesadaran' => 'nullable|string|max:50', // Tingkat kesadaran opsional, berupa string, maksimal 50 karakter
            'formEntryNyeri.nyeri.tingkatAktivitas' => 'nullable|string|max:50', // Tingkat aktivitas opsional, berupa string, maksimal 50 karakter
            'formEntryNyeri.nyeri.sistolik' => 'required|numeric|min:0|max:300', // Tekanan darah sistolik opsional, berupa angka, minimal 0, maksimal 300
            'formEntryNyeri.nyeri.distolik' => 'required|numeric|min:0|max:200', // Tekanan darah diastolik opsional, berupa angka, minimal 0, maksimal 200
            'formEntryNyeri.nyeri.frekuensiNafas' => 'required|numeric|min:0|max:100', // Frekuensi nafas opsional, berupa angka, minimal 0, maksimal 100
            'formEntryNyeri.nyeri.frekuensiNadi' => 'required|numeric|min:0|max:200', // Frekuensi nadi opsional, berupa angka, minimal 0, maksimal 200
            'formEntryNyeri.nyeri.suhu' => 'required|numeric|min:30|max:45', // Suhu tubuh opsional, berupa angka, minimal 30, maksimal 45
            'formEntryNyeri.nyeri.ketIntervensiFarmakologi' => 'nullable|string|max:255', // Keterangan intervensi farmakologi opsional, berupa string, maksimal 255 karakter
            'formEntryNyeri.nyeri.ketIntervensiNonFarmakologi' => 'nullable|string|max:255', // Keterangan intervensi non-farmakologi opsional, berupa string, maksimal 255 karakter
            'formEntryNyeri.nyeri.catatanTambahan' => 'nullable|string|max:500', // Catatan tambahan opsional, berupa string, maksimal 500 karakter
        ];

        $messages = [];

        // Proses Validasi///////////////////////////////////////////
        try {
            $this->validate($rules, $messages);
        } catch (\Illuminate\Validation\ValidationException $e) {

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Lakukan Pengecekan kembali Input Data." . $e->getMessage());
            $this->validate($rules, $messages);
        }


        // Tambahkan data nyeri ke dalam $dataDaftarRi
        $this->dataDaftarRi['penilaian']['nyeri'][] = $this->formEntryNyeri;

        // Simpan perubahan ke penyimpanan
        $this->store();

        // Reset form entry nyeri setelah data berhasil ditambahkan
        $this->reset(['formEntryNyeri']);

        // Tampilkan pesan sukses
        toastr()
            ->closeOnHover(true)
            ->closeDuration(3)
            ->positionClass('toast-top-left')
            ->addSuccess("Data nyeri berhasil ditambahkan.");
    }

    public function removeAssessmentNyeri($index)
    {

        // Pastikan data assessment nyeri ada dan merupakan array
        if (isset($this->dataDaftarRi['penilaian']['nyeri']) && is_array($this->dataDaftarRi['penilaian']['nyeri'])) {
            // Hapus data berdasarkan index
            unset($this->dataDaftarRi['penilaian']['nyeri'][$index]);

            // Reset indeks array setelah penghapusan
            $this->dataDaftarRi['penilaian']['nyeri'] = array_values($this->dataDaftarRi['penilaian']['nyeri']);
        }

        // Simpan perubahan ke penyimpanan (misalnya, database atau session)
        $this->store();
    }
    ////NYERI//////////////////////////////////////////////////////////////////
















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


    public function updated($propertyName) {}



    // ////////////////
    // RJ Logic
    // ////////////////


    // validate Data RJ//////////////////////////////////////////////////



    // insert and update record start////////////////
    public function store()
    {
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
                'myTitle' => 'Penilaian',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Pasien Rawat Inap',
            ]
        );
    }
    // select data end////////////////


}
