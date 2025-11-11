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

    protected $listeners = ['emr:ugd:store' => 'store'];

    public $rjNoRef;
    public array $dataDaftarUgd = [];

    // nonce utk reset UI radio/checkbox setelah submit
    public int $formHumptyNonce = 0;
    public int $formMorseNonce  = 0;

    // ==================== FORM TEMPLATE (ENTRY) ====================

    public array $newHumptyDumptyEntry = [
        'tanggal' => '',
        'umur' => '',
        'umurScore' => 0,
        'sex' => '',
        'sexScore' => 0,
        'diagnosa' => '',
        'diagnosaScore' => 0,
        'gangguanKognitif' => '',
        'gangguanKognitifScore' => 0,
        'faktorLingkungan' => '',
        'faktorLingkunganScore' => 0,
        'penggunaanObat' => '',
        'penggunaanObatScore' => 0,
        'responTerhadapOperasi' => '',
        'responTerhadapOperasiScore' => 0,
        'totalScore' => 0,
        'kategoriRisiko' => '',
        'keterangan' => ''
    ];

    public array $newMorseEntry = [
        'tanggal' => '',
        'riwayatJatuh3blnTerakhir' => '',
        'riwayatJatuh3blnTerakhirScore' => 0,
        'diagSekunder' => '',
        'diagSekunderScore' => 0,
        'alatBantu' => '',
        'alatBantuScore' => 0,
        'heparin' => '',
        'heparinScore' => 0,
        'gayaBerjalan' => '',
        'gayaBerjalanScore' => 0,
        'kesadaran' => '',
        'kesadaranScore' => 0,
        'totalScore' => 0,
        'kategoriRisiko' => '',
        'keterangan' => ''
    ];

    // ==================== OPTIONS DIPISAH ====================

    public array $humptyDumptyOptions = [
        "umurOptions" => [
            ["umur" => "< 3 tahun", "score" => 4],
            ["umur" => "3-7 tahun", "score" => 3],
            ["umur" => "7-13 tahun", "score" => 2],
            ["umur" => "13-18 tahun", "score" => 1],
        ],
        "sexOptions" => [
            ["sex" => "Laki-laki", "score" => 2],
            ["sex" => "Perempuan", "score" => 1],
        ],
        "diagnosaOptions" => [
            ["diagnosa" => "Kelainan Neurologi", "score" => 4],
            ["diagnosa" => "Perubahan dalam Oksigenasi (Masalah Saluran Nafas, Dehidrasi, Anemia, Anoreksi, Slokop/sakit kepala, dll)", "score" => 3],
            ["diagnosa" => "Kelamin Priksi/Perilaku", "score" => 2],
            ["diagnosa" => "Diagnosa Lain", "score" => 1],
        ],
        "gangguanKognitifOptions" => [
            ["gangguanKognitif" => "Tidak Sadar terhadap Keterbatasan", "score" => 3],
            ["gangguanKognitif" => "Lupa keterbatasan", "score" => 2],
            ["gangguanKognitif" => "Mengetahui Kemampuan Diri", "score" => 1],
        ],
        "faktorLingkunganOptions" => [
            ["faktorLingkungan" => "Riwayat jatuh dari tempat tidur saat bayi anak", "score" => 4],
            ["faktorLingkungan" => "Pasien menggunakan alat bantu box atau mobel", "score" => 3],
            ["faktorLingkungan" => "Pasien berada ditempat tidur", "score" => 2],
            ["faktorLingkungan" => "Diluar ruang rawat", "score" => 1],
        ],
        "penggunaanObatOptions" => [
            ["penggunaanObat" => "Bermacam-macam obat yang digunakan: obat sedarif (kecuali pasien ICU yang menggunakan sedasi dan paralisis), Hipnotik, Barbiturat, Fenotiazin, Antidepresan, Laksans/Diuretika,Narketik", "score" => 3],
            ["penggunaanObat" => "Salah satu pengobatan diatas", "score" => 2],
            ["penggunaanObat" => "Pengobatan lain", "score" => 1],
        ],
        "responTerhadapOperasiOptions" => [
            ["responTerhadapOperasi" => "Dalam 24 Jam", "score" => 3],
            ["responTerhadapOperasi" => "Dalam 48 jam riwayat jatuh", "score" => 2],
            ["responTerhadapOperasi" => "> 48 jam", "score" => 1],
        ],
    ];

    public array $morseOptions = [
        "riwayatJatuh3blnTerakhirOptions" => [
            ["riwayatJatuh3blnTerakhir" => "Ya", "score" => 25],
            ["riwayatJatuh3blnTerakhir" => "Tidak", "score" => 0],
        ],
        "diagSekunderOptions" => [
            ["diagSekunder" => "Ya", "score" => 15],
            ["diagSekunder" => "Tidak", "score" => 0],
        ],
        "alatBantuOptions" => [
            ["alatBantu" => "Tidak Ada / Bed Rest", "score" => 0],
            ["alatBantu" => "Tongkat / Alat Penopang / Walker", "score" => 15],
            ["alatBantu" => "Furnitur", "score" => 30],
        ],
        "heparinOptions" => [
            ["heparin" => "Ya", "score" => 20],
            ["heparin" => "Tidak", "score" => 0],
        ],
        "gayaBerjalanOptions" => [
            ["gayaBerjalan" => "Normal / Tirah Baring / Tidak Bergerak", "score" => 0],
            ["gayaBerjalan" => "Lemah", "score" => 10],
            ["gayaBerjalan" => "Terganggu", "score" => 20],
        ],
        "kesadaranOptions" => [
            ["kesadaran" => "Baik", "score" => 0],
            ["kesadaran" => "Lupa / Pelupa", "score" => 15],
        ],
    ];

    // ==================== PAYLOAD PENILAIAN (TANPA OPTIONS) ====================

    public array $penilaian = [
        "fisikTab" => "Fisik",
        "fisik" => ["fisik" => ""],

        "statusMedikTab" => "Status Medik",
        "statusMedik" => [
            "statusMedik" => "",
            "statusMedikOptions" => [
                ["statusMedik" => "Emergency Trauma"],
                ["statusMedik" => "Emergency Non Trauma"],
                ["statusMedik" => "Non Emergency Trauma"],
                ["statusMedik" => "Non Emergency Non Trauma"],
            ],
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
                ],
            ],
            "nyeri" => "",
            "nyeriOptions" => [["nyeri" => "Ya"], ["nyeri" => "Tidak"]],
            "nyeriKet" => "",
            "nyeriKetOptions" => [["nyeriKet" => "Akut"], ["nyeriKet" => "Kronis"]],
            "skalaNyeri" => "",
            "nyeriMetode" => "",
            "nyeriMetodeOptions" => [["nyeriMetode" => "NRS"], ["nyeriMetode" => "BPS"], ["nyeriMetode" => "NIPS"], ["nyeriMetode" => "FLACC"], ["nyeriMetode" => "VAS"]],
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
            ],
        ],

        "diagnosisTab" => "Diagnosis",
        "diagnosis" => ["diagnosis" => ""],

        "resikoJatuhTab" => "Resiko Jatuh",
        "resikoJatuh" => [
            // hanya ENTRIES (no options disimpan ke JSON pasien)
            "skalaHumptyDumpty" => [],
            "skalaMorse" => [],
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

    // ==================== HUMPTY: ADD/DELETE/UPDATE ====================

    public function tambahHumptyDumpty(): void
    {
        $this->newHumptyDumptyEntry['tanggal'] = now(config('app.timezone'))->format('d/m/Y H:i:s');
        $this->hitungScoreHumptyDumpty();

        if (empty($this->newHumptyDumptyEntry['umur'])) {
            toastr()->positionClass('toast-top-left')->addError('Data umur wajib diisi.');
            return;
        }
        if (empty($this->rjNoRef)) {
            toastr()->positionClass('toast-top-left')->addError('Nomor registrasi UGD tidak valid.');
            return;
        }

        try {
            DB::transaction(function () {
                $fresh = $this->findDataUGD($this->rjNoRef) ?: [];
                $fresh['penilaian']['resikoJatuh']['skalaHumptyDumpty'] ??= [];

                $this->newHumptyDumptyEntry['id'] = uniqid('humpty_');
                $fresh['penilaian']['resikoJatuh']['skalaHumptyDumpty'][] = $this->newHumptyDumptyEntry;

                $this->updateJsonUGD($this->rjNoRef, $fresh);
                $this->dataDaftarUgd = $fresh;
            });

            $this->resetHumptyDumptyForm();
            $this->resetValidation();
            $this->formHumptyNonce++;

            toastr()->positionClass('toast-top-left')->addSuccess('Data Humpty Dumpty berhasil ditambahkan dan disimpan.');
        } catch (\Exception $e) {
            toastr()->positionClass('toast-top-left')->addError('Gagal menyimpan data Humpty Dumpty: ' . $e->getMessage());
        }
    }

    public function hapusHumptyDumpty($index): void
    {
        if (!isset($this->dataDaftarUgd['penilaian']['resikoJatuh']['skalaHumptyDumpty'][$index])) {
            toastr()->positionClass('toast-top-left')->addError('Data tidak ditemukan.');
            return;
        }

        try {
            DB::transaction(function () use ($index) {
                $fresh = $this->findDataUGD($this->rjNoRef) ?: [];
                if (!isset($fresh['penilaian']['resikoJatuh']['skalaHumptyDumpty'][$index])) return;

                unset($fresh['penilaian']['resikoJatuh']['skalaHumptyDumpty'][$index]);
                $fresh['penilaian']['resikoJatuh']['skalaHumptyDumpty'] = array_values($fresh['penilaian']['resikoJatuh']['skalaHumptyDumpty']);

                $this->updateJsonUGD($this->rjNoRef, $fresh);
                $this->dataDaftarUgd = $fresh;
            });

            toastr()->positionClass('toast-top-left')->addSuccess('Data Humpty Dumpty berhasil dihapus.');
        } catch (\Exception $e) {
            toastr()->positionClass('toast-top-left')->addError('Gagal menghapus data Humpty Dumpty: ' . $e->getMessage());
        }
    }

    public function updatedNewHumptyDumptyEntry($value, $key): void
    {
        if (str_contains($key, 'umur') && $key !== 'umurScore') {
            $this->setHumptyDumptyScore('umur', $value);
        } elseif (str_contains($key, 'sex') && $key !== 'sexScore') {
            $this->setHumptyDumptyScore('sex', $value);
        } elseif (str_contains($key, 'diagnosa') && $key !== 'diagnosaScore') {
            $this->setHumptyDumptyScore('diagnosa', $value);
        } elseif (str_contains($key, 'gangguanKognitif') && $key !== 'gangguanKognitifScore') {
            $this->setHumptyDumptyScore('gangguanKognitif', $value);
        } elseif (str_contains($key, 'faktorLingkungan') && $key !== 'faktorLingkunganScore') {
            $this->setHumptyDumptyScore('faktorLingkungan', $value);
        } elseif (str_contains($key, 'penggunaanObat') && $key !== 'penggunaanObatScore') {
            $this->setHumptyDumptyScore('penggunaanObat', $value);
        } elseif (str_contains($key, 'responTerhadapOperasi') && $key !== 'responTerhadapOperasiScore') {
            $this->setHumptyDumptyScore('responTerhadapOperasi', $value);
        }

        $this->hitungScoreHumptyDumpty();
    }

    private function setHumptyDumptyScore($field, $value): void
    {
        $options = $this->humptyDumptyOptions[$field . 'Options'] ?? [];
        $score = 0;
        foreach ($options as $option) {
            if (($option[$field] ?? null) === $value) {
                $score = (int)($option['score'] ?? 0);
                break;
            }
        }
        $this->newHumptyDumptyEntry[$field . 'Score'] = $score;
    }

    private function hitungScoreHumptyDumpty(): void
    {
        $total =
            (int)$this->newHumptyDumptyEntry['umurScore'] +
            (int)$this->newHumptyDumptyEntry['sexScore'] +
            (int)$this->newHumptyDumptyEntry['diagnosaScore'] +
            (int)$this->newHumptyDumptyEntry['gangguanKognitifScore'] +
            (int)$this->newHumptyDumptyEntry['faktorLingkunganScore'] +
            (int)$this->newHumptyDumptyEntry['penggunaanObatScore'] +
            (int)$this->newHumptyDumptyEntry['responTerhadapOperasiScore'];

        $this->newHumptyDumptyEntry['totalScore'] = $total;
        $this->newHumptyDumptyEntry['kategoriRisiko'] = ($total >= 12) ? 'Risiko Tinggi' : 'Risiko Rendah';
    }

    private function resetHumptyDumptyForm(): void
    {
        $this->newHumptyDumptyEntry = [
            'tanggal' => '',
            'umur' => '',
            'umurScore' => 0,
            'sex' => '',
            'sexScore' => 0,
            'diagnosa' => '',
            'diagnosaScore' => 0,
            'gangguanKognitif' => '',
            'gangguanKognitifScore' => 0,
            'faktorLingkungan' => '',
            'faktorLingkunganScore' => 0,
            'penggunaanObat' => '',
            'penggunaanObatScore' => 0,
            'responTerhadapOperasi' => '',
            'responTerhadapOperasiScore' => 0,
            'totalScore' => 0,
            'kategoriRisiko' => '',
            'keterangan' => ''
        ];
    }

    // ==================== MORSE: ADD/DELETE/UPDATE ====================

    public function tambahMorse(): void
    {
        $this->newMorseEntry['tanggal'] = now(config('app.timezone'))->format('d/m/Y H:i:s');
        $this->hitungScoreMorse();

        if (empty($this->newMorseEntry['riwayatJatuh3blnTerakhir'])) {
            toastr()->positionClass('toast-top-left')->addError('Data riwayat jatuh wajib diisi.');
            return;
        }
        if (empty($this->rjNoRef)) {
            toastr()->positionClass('toast-top-left')->addError('Nomor registrasi UGD tidak valid.');
            return;
        }

        try {
            DB::transaction(function () {
                $fresh = $this->findDataUGD($this->rjNoRef) ?: [];
                $fresh['penilaian']['resikoJatuh']['skalaMorse'] ??= [];

                $this->newMorseEntry['id'] = uniqid('morse_');
                $fresh['penilaian']['resikoJatuh']['skalaMorse'][] = $this->newMorseEntry;

                $this->updateJsonUGD($this->rjNoRef, $fresh);
                $this->dataDaftarUgd = $fresh;
            });

            $this->resetMorseForm();
            $this->resetValidation();
            $this->formMorseNonce++;

            toastr()->positionClass('toast-top-left')->addSuccess('Data Morse berhasil ditambahkan dan disimpan.');
        } catch (\Exception $e) {
            toastr()->positionClass('toast-top-left')->addError('Gagal menyimpan data Morse: ' . $e->getMessage());
        }
    }

    public function hapusMorse($index): void
    {
        if (!isset($this->dataDaftarUgd['penilaian']['resikoJatuh']['skalaMorse'][$index])) {
            toastr()->positionClass('toast-top-left')->addError('Data tidak ditemukan.');
            return;
        }

        try {
            DB::transaction(function () use ($index) {
                $fresh = $this->findDataUGD($this->rjNoRef) ?: [];
                if (!isset($fresh['penilaian']['resikoJatuh']['skalaMorse'][$index])) return;

                unset($fresh['penilaian']['resikoJatuh']['skalaMorse'][$index]);
                $fresh['penilaian']['resikoJatuh']['skalaMorse'] = array_values($fresh['penilaian']['resikoJatuh']['skalaMorse']);

                $this->updateJsonUGD($this->rjNoRef, $fresh);
                $this->dataDaftarUgd = $fresh;
            });

            toastr()->positionClass('toast-top-left')->addSuccess('Data Morse berhasil dihapus.');
        } catch (\Exception $e) {
            toastr()->positionClass('toast-top-left')->addError('Gagal menghapus data Morse: ' . $e->getMessage());
        }
    }

    public function updatedNewMorseEntry($value, $key): void
    {
        if (str_contains($key, 'riwayatJatuh3blnTerakhir') && $key !== 'riwayatJatuh3blnTerakhirScore') {
            $this->setMorseScore('riwayatJatuh3blnTerakhir', $value);
        } elseif (str_contains($key, 'diagSekunder') && $key !== 'diagSekunderScore') {
            $this->setMorseScore('diagSekunder', $value);
        } elseif (str_contains($key, 'alatBantu') && $key !== 'alatBantuScore') {
            $this->setMorseScore('alatBantu', $value);
        } elseif (str_contains($key, 'heparin') && $key !== 'heparinScore') {
            $this->setMorseScore('heparin', $value);
        } elseif (str_contains($key, 'gayaBerjalan') && $key !== 'gayaBerjalanScore') {
            $this->setMorseScore('gayaBerjalan', $value);
        } elseif (str_contains($key, 'kesadaran') && $key !== 'kesadaranScore') {
            $this->setMorseScore('kesadaran', $value);
        }

        $this->hitungScoreMorse();
    }

    private function setMorseScore($field, $value): void
    {
        $options = $this->morseOptions[$field . 'Options'] ?? [];
        $score = 0;
        foreach ($options as $option) {
            if (($option[$field] ?? null) === $value) {
                $score = (int)($option['score'] ?? 0);
                break;
            }
        }
        $this->newMorseEntry[$field . 'Score'] = $score;
    }

    private function hitungScoreMorse(): void
    {
        $total =
            (int)$this->newMorseEntry['riwayatJatuh3blnTerakhirScore'] +
            (int)$this->newMorseEntry['diagSekunderScore'] +
            (int)$this->newMorseEntry['alatBantuScore'] +
            (int)$this->newMorseEntry['heparinScore'] +
            (int)$this->newMorseEntry['gayaBerjalanScore'] +
            (int)$this->newMorseEntry['kesadaranScore'];

        $this->newMorseEntry['totalScore'] = $total;

        if ($total <= 24) {
            $this->newMorseEntry['kategoriRisiko'] = 'Tidak Ada Risiko';
        } elseif ($total <= 50) {
            $this->newMorseEntry['kategoriRisiko'] = 'Risiko Rendah';
        } else {
            $this->newMorseEntry['kategoriRisiko'] = 'Risiko Tinggi';
        }
    }

    private function resetMorseForm(): void
    {
        $this->newMorseEntry = [
            'tanggal' => '',
            'riwayatJatuh3blnTerakhir' => '',
            'riwayatJatuh3blnTerakhirScore' => 0,
            'diagSekunder' => '',
            'diagSekunderScore' => 0,
            'alatBantu' => '',
            'alatBantuScore' => 0,
            'heparin' => '',
            'heparinScore' => 0,
            'gayaBerjalan' => '',
            'gayaBerjalanScore' => 0,
            'kesadaran' => '',
            'kesadaranScore' => 0,
            'totalScore' => 0,
            'kategoriRisiko' => '',
            'keterangan' => ''
        ];
    }

    // ==================== CORE ====================

    public function store(): void
    {
        $rjNo = $this->dataDaftarUgd['rjNo'] ?? $this->rjNoRef ?? null;
        if (!$rjNo) {
            toastr()->positionClass('toast-top-left')->addError('Nomor UGD kosong.');
            return;
        }

        $lockKey = "ugd:{$rjNo}";
        try {
            Cache::lock($lockKey, 5)->block(3, function () use ($rjNo) {
                DB::transaction(function () use ($rjNo) {
                    $fresh = $this->findDataUGD($rjNo) ?: [];

                    // Jangan sentuh skala Humpty/Morse (sudah disimpan saat tambah/hapus)
                    $fresh['penilaian']['fisik']         = $this->dataDaftarUgd['penilaian']['fisik'] ?? [];
                    $fresh['penilaian']['statusMedik']   = $this->dataDaftarUgd['penilaian']['statusMedik'] ?? [];
                    $fresh['penilaian']['nyeri']         = $this->dataDaftarUgd['penilaian']['nyeri'] ?? [];
                    $fresh['penilaian']['statusPediatrik'] = $this->dataDaftarUgd['penilaian']['statusPediatrik'] ?? [];
                    $fresh['penilaian']['diagnosis']     = $this->dataDaftarUgd['penilaian']['diagnosis'] ?? [];
                    $fresh['penilaian']['dekubitus']     = $this->dataDaftarUgd['penilaian']['dekubitus'] ?? [];

                    $this->updateJsonUGD($rjNo, $fresh);
                    $this->dataDaftarUgd = $fresh;
                });
            });

            toastr()->positionClass('toast-top-left')->addSuccess('Data penilaian berhasil disimpan.');
        } catch (LockTimeoutException $e) {
            toastr()->positionClass('toast-top-left')->addError('Sistem sibuk, gagal memperoleh lock. Coba lagi.');
        } catch (\Throwable $e) {
            toastr()->positionClass('toast-top-left')->addError('Gagal menyimpan penilaian: ' . $e->getMessage());
        }
    }

    private function defaultPenilaian(): array
    {
        return $this->penilaian;
    }

    // buang key non-indeks (kalau ada sisa schema lama yang nyimpen options di skala*)
    private function normalizeEntriesOnly(array &$arr, string $key): void
    {
        if (!isset($arr[$key]) || !is_array($arr[$key])) return;
        $arr[$key] = array_values(array_filter(
            $arr[$key],
            fn($v, $k) => is_int($k) || ctype_digit((string)$k),
            ARRAY_FILTER_USE_BOTH
        ));
    }

    private function findData($rjno): void
    {
        $this->dataDaftarUgd = $this->findDataUGD($rjno) ?: [];

        $current = (array)($this->dataDaftarUgd['penilaian'] ?? []);
        $this->dataDaftarUgd['penilaian'] = array_replace_recursive($this->defaultPenilaian(), $current);

        // pastikan array entries ada
        $rj = &$this->dataDaftarUgd['penilaian']['resikoJatuh'];
        $rj['skalaHumptyDumpty'] ??= [];
        $rj['skalaMorse']        ??= [];

        // normalisasi (hapus key bukan indeks numerik)
        $this->normalizeEntriesOnly($rj, 'skalaHumptyDumpty');
        $this->normalizeEntriesOnly($rj, 'skalaMorse');
    }

    public function mount(): void
    {
        $this->findData($this->rjNoRef);
    }

    public function render()
    {
        return view('livewire.emr-u-g-d.mr-u-g-d.penilaian.penilaian', [
            'myTitle'   => 'Penilaian UGD',
            'mySnipt'   => 'Rekam Medis Pasien UGD',
            'myProgram' => 'Pasien UGD',
        ]);
    }
}
