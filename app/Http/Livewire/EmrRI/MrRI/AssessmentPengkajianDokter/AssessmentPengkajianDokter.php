<?php

namespace App\Http\Livewire\EmrRI\MrRI\AssessmentPengkajianDokter;

use Livewire\Component;
use Livewire\WithPagination;

use App\Http\Traits\EmrRI\EmrRITrait;
use App\Http\Traits\customErrorMessagesTrait;
use App\Http\Traits\MasterPasien\MasterPasienTrait;


class AssessmentPengkajianDokter extends Component
{
    use WithPagination, EmrRITrait, customErrorMessagesTrait, MasterPasienTrait;

    // listener from blade////////////////
    protected $listeners = [
        'syncronizeAssessmentPerawatRIFindData' => 'mount',
        'laboratSelectedText' => 'appendLaboratText',
        'requestCopyAssessmentFromUGDDokter' => 'handleCopyAssessmentFromUGDDokter',

    ];


    public function appendLaboratText(string $text): void
    {
        // Ambil isi sekarang (kalau belum ada, pakai string kosong)
        $current = data_get(
            $this->dataDaftarRi,
            'pengkajianDokter.hasilPemeriksaanPenunjang.laboratorium',
            ''
        );

        // Pilih separator baris baru hanya jika sudah ada isi sebelumnya
        $sep = $current !== '' ? "\n" : '';

        // Simpan kembali ke struktur array $dataDaftarRi
        data_set(
            $this->dataDaftarRi,
            'pengkajianDokter.hasilPemeriksaanPenunjang.laboratorium',
            $current . $sep . $text
        );
    }

    public function handleCopyAssessmentFromUGDDokter($dataDaftarTxn): void
    {
        $data = $dataDaftarTxn;
        // mapping
        $this->dataDaftarRi['pengkajianDokter']['anamnesa']['keluhanUtama'] =
            data_get($data, 'anamnesa.keluhanUtama.keluhanUtama') ?? '';

        $this->dataDaftarRi['pengkajianDokter']['anamnesa']['keluhanTambahan'] =
            data_get($data, 'anamnesa.keluhanTambahan') ?? '';

        $this->dataDaftarRi['pengkajianDokter']['anamnesa']['riwayatPenyakit']['sekarang'] =
            data_get($data, 'anamnesa.riwayatPenyakitSekarangUmum.riwayatPenyakitSekarangUmum') ?? '';

        $this->dataDaftarRi['pengkajianDokter']['anamnesa']['riwayatPenyakit']['dahulu'] =
            data_get($data, 'anamnesa.riwayatPenyakitDahulu.riwayatPenyakitDahulu') ?? '';


        $penyakitKeluarga = data_get($data, 'anamnesa.penyakitKeluarga', []);

        if (is_array($penyakitKeluarga)) {
            $penyakitKeluargaFiltered = collect($penyakitKeluarga)
                ->filter(fn($val) => !empty($val)) // ambil yang ada isinya (array berisi atau string tidak kosong)
                ->map(function ($val, $key) {
                    // Kalau "lainLain" ada isi string → tampilkan stringnya
                    if ($key === 'lainLain') {
                        return is_string($val) ? $val : null;
                    }
                    // Kalau bukan "lainLain" cukup pakai nama key
                    return $key;
                })
                ->filter() // buang null
                ->implode(', ');

            $this->dataDaftarRi['pengkajianDokter']['anamnesa']['riwayatPenyakit']['keluarga'] =
                $penyakitKeluargaFiltered;
        } else {
            $this->dataDaftarRi['pengkajianDokter']['anamnesa']['riwayatPenyakit']['keluarga'] = '';
        }

        $this->dataDaftarRi['pengkajianDokter']['anamnesa']['jenisAlergi'] =
            data_get($data, 'anamnesa.alergi.alergi') ?? '';

        $this->dataDaftarRi['pengkajianDokter']['fisik'] =
            data_get($data, 'pemeriksaan.fisik', '');



        // Laboratorium
        $currentLab = data_get($this->dataDaftarRi, 'pengkajianDokter.hasilPemeriksaanPenunjang.laboratorium', '');
        $newLab = trim(data_get($data, 'pemeriksaan.penunjang.laboratorium', ''));

        if ($newLab !== '') {
            $this->dataDaftarRi['pengkajianDokter']['hasilPemeriksaanPenunjang']['laboratorium'] =
                $currentLab !== '' ? $currentLab . "\n" . $newLab : $newLab;
        }

        // Radiologi
        $currentRad = data_get($this->dataDaftarRi, 'pengkajianDokter.hasilPemeriksaanPenunjang.radiologi', '');
        $newRad = trim(data_get($data, 'pemeriksaan.penunjang.radiologi', ''));

        if ($newRad !== '') {
            $this->dataDaftarRi['pengkajianDokter']['hasilPemeriksaanPenunjang']['radiologi'] =
                $currentRad !== '' ? $currentRad . "\n" . $newRad : $newRad;
        }

        // Penunjang lain
        $currentOther = data_get($this->dataDaftarRi, 'pengkajianDokter.hasilPemeriksaanPenunjang.penunjangLain', '');
        $newOther = trim(data_get($data, 'pemeriksaan.penunjang.penunjangLain', ''));

        if ($newOther !== '') {
            $this->dataDaftarRi['pengkajianDokter']['hasilPemeriksaanPenunjang']['penunjangLain'] =
                $currentOther !== '' ? $currentOther . "\n" . $newOther : $newOther;
        }






        // 1) Diagnosa Awal <- diagnosisFreeText (UGD)
        $diagnosisFreeText = trim((string) data_get($data, 'diagnosisFreeText', ''));

        // kalau kosong, coba juga data_get($data, 'diagnosisFreeText') etc.
        // set ke struktur pengkajianDokter //replace
        $this->dataDaftarRi['pengkajianDokter']['diagnosaAssesment']['diagnosaAwal'] =
            $diagnosisFreeText !== '' ? $diagnosisFreeText : ($this->dataDaftarRi['pengkajianDokter']['diagnosaAssesment']['diagnosaAwal'] ?? '');

        // 2) Terapi <- perencanaan.terapi (preferensi) atau fallback ke procedureFreeText
        // beberapa struktur yang mungkin ada di data UGD:
        // - perencanaan.terapi.terapi
        // - procedureFreeText (contoh: "P:\ninf ...\ninj ...")
        // - perencanaan.terapi (bila langsung string/array)
        $newTherapy = trim((string) data_get($data, 'perencanaan.terapi.terapi', ''));
        if ($newTherapy !== '') {
            // jika kamu ingin menyimpan hanya isi terapi dari UGD (replace):
            $this->dataDaftarRi['pengkajianDokter']['rencana']['terapi'] = $newTherapy;
        }

        $this->store();
        $this->dispatchBrowserEvent('copyDone');
    }

    //////////////////////////////
    // Ref on top bar
    //////////////////////////////



    // dataDaftarRi RJ
    public $riHdrNoRef;

    public array $dataDaftarRi = [];
    public array $dataPasien = [];

    public array $pengkajianDokter = [
        "anamnesa" => [
            "keluhanUtama" => "",
            "keluhanTambahan" => "",
            "riwayatPenyakit" => [
                "sekarang" => "",
                "dahulu" => "",
                "keluarga" => ""
            ],
            "jenisAlergi" => "", // Mengubah "alergiObat" menjadi "jenisAlergi"
            "rekonsiliasiObat" => [], // Menambahkan rekonsiliasi obat
        ],
        "fisik" => "",
        "anatomi" => [
            "kepala" => [
                "kelainan" => "Tidak Diperiksa",
                "kelainanOptions" => [
                    ["kelainan" => "Tidak Diperiksa"],
                    ["kelainan" => "Tidak Ada Kelainan"],
                    ["kelainan" => "Ada"],
                ],
                "desc" => "",
            ],
            "mata" => [
                "kelainan" => "Tidak Diperiksa",
                "kelainanOptions" => [
                    ["kelainan" => "Tidak Diperiksa"],
                    ["kelainan" => "Tidak Ada Kelainan"],
                    ["kelainan" => "Ada"],
                ],
                "desc" => "",
            ],
            "telinga" => [
                "kelainan" => "Tidak Diperiksa",
                "kelainanOptions" => [
                    ["kelainan" => "Tidak Diperiksa"],
                    ["kelainan" => "Tidak Ada Kelainan"],
                    ["kelainan" => "Ada"],
                ],
                "desc" => "",
            ],
            "hidung" => [
                "kelainan" => "Tidak Diperiksa",
                "kelainanOptions" => [
                    ["kelainan" => "Tidak Diperiksa"],
                    ["kelainan" => "Tidak Ada Kelainan"],
                    ["kelainan" => "Ada"],
                ],
                "desc" => "",
            ],
            "rambut" => [
                "kelainan" => "Tidak Diperiksa",
                "kelainanOptions" => [
                    ["kelainan" => "Tidak Diperiksa"],
                    ["kelainan" => "Tidak Ada Kelainan"],
                    ["kelainan" => "Ada"],
                ],
                "desc" => "",
            ],
            "bibir" => [
                "kelainan" => "Tidak Diperiksa",
                "kelainanOptions" => [
                    ["kelainan" => "Tidak Diperiksa"],
                    ["kelainan" => "Tidak Ada Kelainan"],
                    ["kelainan" => "Ada"],
                ],
                "desc" => "",
            ],
            "gigiGeligi" => [
                "kelainan" => "Tidak Diperiksa",
                "kelainanOptions" => [
                    ["kelainan" => "Tidak Diperiksa"],
                    ["kelainan" => "Tidak Ada Kelainan"],
                    ["kelainan" => "Ada"],
                ],
                "desc" => "",
            ],
            "lidah" => [
                "kelainan" => "Tidak Diperiksa",
                "kelainanOptions" => [
                    ["kelainan" => "Tidak Diperiksa"],
                    ["kelainan" => "Tidak Ada Kelainan"],
                    ["kelainan" => "Ada"],
                ],
                "desc" => "",
            ],
            "langitLangit" => [
                "kelainan" => "Tidak Diperiksa",
                "kelainanOptions" => [
                    ["kelainan" => "Tidak Diperiksa"],
                    ["kelainan" => "Tidak Ada Kelainan"],
                    ["kelainan" => "Ada"],
                ],
                "desc" => "",
            ],
            "leher" => [
                "kelainan" => "Tidak Diperiksa",
                "kelainanOptions" => [
                    ["kelainan" => "Tidak Diperiksa"],
                    ["kelainan" => "Tidak Ada Kelainan"],
                    ["kelainan" => "Ada"],
                ],
                "desc" => "",
            ],
            "tenggorokan" => [
                "kelainan" => "Tidak Diperiksa",
                "kelainanOptions" => [
                    ["kelainan" => "Tidak Diperiksa"],
                    ["kelainan" => "Tidak Ada Kelainan"],
                    ["kelainan" => "Ada"],
                ],
                "desc" => "",
            ],
            "tonsil" => [
                "kelainan" => "Tidak Diperiksa",
                "kelainanOptions" => [
                    ["kelainan" => "Tidak Diperiksa"],
                    ["kelainan" => "Tidak Ada Kelainan"],
                    ["kelainan" => "Ada"],
                ],
                "desc" => "",
            ],
            "dada" => [
                "kelainan" => "Tidak Diperiksa",
                "kelainanOptions" => [
                    ["kelainan" => "Tidak Diperiksa"],
                    ["kelainan" => "Tidak Ada Kelainan"],
                    ["kelainan" => "Ada"],
                ],
                "desc" => "",
            ],
            "payudarah" => [
                "kelainan" => "Tidak Diperiksa",
                "kelainanOptions" => [
                    ["kelainan" => "Tidak Diperiksa"],
                    ["kelainan" => "Tidak Ada Kelainan"],
                    ["kelainan" => "Ada"],
                ],
                "desc" => "",
            ],
            "punggung" => [
                "kelainan" => "Tidak Diperiksa",
                "kelainanOptions" => [
                    ["kelainan" => "Tidak Diperiksa"],
                    ["kelainan" => "Tidak Ada Kelainan"],
                    ["kelainan" => "Ada"],
                ],
                "desc" => "",
            ],
            "perut" => [
                "kelainan" => "Tidak Diperiksa",
                "kelainanOptions" => [
                    ["kelainan" => "Tidak Diperiksa"],
                    ["kelainan" => "Tidak Ada Kelainan"],
                    ["kelainan" => "Ada"],
                ],
                "desc" => "",
            ],
            "genital" => [
                "kelainan" => "Tidak Diperiksa",
                "kelainanOptions" => [
                    ["kelainan" => "Tidak Diperiksa"],
                    ["kelainan" => "Tidak Ada Kelainan"],
                    ["kelainan" => "Ada"],
                ],
                "desc" => "",
            ],
            "anus" => [
                "kelainan" => "Tidak Diperiksa",
                "kelainanOptions" => [
                    ["kelainan" => "Tidak Diperiksa"],
                    ["kelainan" => "Tidak Ada Kelainan"],
                    ["kelainan" => "Ada"],
                ],
                "desc" => "",
            ],
            "lenganAtas" => [
                "kelainan" => "Tidak Diperiksa",
                "kelainanOptions" => [
                    ["kelainan" => "Tidak Diperiksa"],
                    ["kelainan" => "Tidak Ada Kelainan"],
                    ["kelainan" => "Ada"],
                ],
                "desc" => "",
            ],
            "lenganBawah" => [
                "kelainan" => "Tidak Diperiksa",
                "kelainanOptions" => [
                    ["kelainan" => "Tidak Diperiksa"],
                    ["kelainan" => "Tidak Ada Kelainan"],
                    ["kelainan" => "Ada"],
                ],
                "desc" => "",
            ],
            "jariTangan" => [
                "kelainan" => "Tidak Diperiksa",
                "kelainanOptions" => [
                    ["kelainan" => "Tidak Diperiksa"],
                    ["kelainan" => "Tidak Ada Kelainan"],
                    ["kelainan" => "Ada"],
                ],
                "desc" => "",
            ],
            "kukuTangan" => [
                "kelainan" => "Tidak Diperiksa",
                "kelainanOptions" => [
                    ["kelainan" => "Tidak Diperiksa"],
                    ["kelainan" => "Tidak Ada Kelainan"],
                    ["kelainan" => "Ada"],
                ],
                "desc" => "",
            ],
            "persendianTangan" => [
                "kelainan" => "Tidak Diperiksa",
                "kelainanOptions" => [
                    ["kelainan" => "Tidak Diperiksa"],
                    ["kelainan" => "Tidak Ada Kelainan"],
                    ["kelainan" => "Ada"],
                ],
                "desc" => "",
            ],
            "tungkaiAtas" => [
                "kelainan" => "Tidak Diperiksa",
                "kelainanOptions" => [
                    ["kelainan" => "Tidak Diperiksa"],
                    ["kelainan" => "Tidak Ada Kelainan"],
                    ["kelainan" => "Ada"],
                ],
                "desc" => "",
            ],
            "tungkaiBawah" => [
                "kelainan" => "Tidak Diperiksa",
                "kelainanOptions" => [
                    ["kelainan" => "Tidak Diperiksa"],
                    ["kelainan" => "Tidak Ada Kelainan"],
                    ["kelainan" => "Ada"],
                ],
                "desc" => "",
            ],
            "jariKaki" => [
                "kelainan" => "Tidak Diperiksa",
                "kelainanOptions" => [
                    ["kelainan" => "Tidak Diperiksa"],
                    ["kelainan" => "Tidak Ada Kelainan"],
                    ["kelainan" => "Ada"],
                ],
                "desc" => "",
            ],
            "kukuKaki" => [
                "kelainan" => "Tidak Diperiksa",
                "kelainanOptions" => [
                    ["kelainan" => "Tidak Diperiksa"],
                    ["kelainan" => "Tidak Ada Kelainan"],
                    ["kelainan" => "Ada"],
                ],
                "desc" => "",
            ],
            "persendianKaki" => [
                "kelainan" => "Tidak Diperiksa",
                "kelainanOptions" => [
                    ["kelainan" => "Tidak Diperiksa"],
                    ["kelainan" => "Tidak Ada Kelainan"],
                    ["kelainan" => "Ada"],
                ],
                "desc" => "",
            ],
            "faring" => [
                "kelainan" => "Tidak Diperiksa",
                "kelainanOptions" => [
                    ["kelainan" => "Tidak Diperiksa"],
                    ["kelainan" => "Tidak Ada Kelainan"],
                    ["kelainan" => "Ada"],
                ],
                "desc" => "",
            ],
        ],
        "statusLokalis" => [
            "deskripsiGambar" => ""
        ],
        "hasilPemeriksaanPenunjang" => [
            "laboratorium" => "",
            "radiologi" => "",
            "penunjangLain" => ""
        ],
        "diagnosaAssesment" => [
            "diagnosaAwal" => ""
        ],
        "rencana" => [
            "penegakanDiagnosa" => "",
            "terapi" => "",
            "terapiPulang" => "",
            "diet" => "",
            "edukasi" => "",
            "monitoring" => ""
        ],
        "tandaTanganDokter" => [
            "dokterPengkaji" => "",      // Nama dokter yang melakukan pengkajian
            "dokterPengkajiCode" => "",  // Kode unik atau identifikasi dokter
            "jamDokterPengkaji" => ""
        ]
    ];

    //////////////////////////////////////////////////////////////////////


    protected $rules = [
        // Anamnesa
        'dataDaftarRi.pengkajianDokter.anamnesa.keluhanUtama' => 'required|string|max:500',
        'dataDaftarRi.pengkajianDokter.anamnesa.keluhanTambahan' => 'nullable|string|max:500',
        'dataDaftarRi.pengkajianDokter.anamnesa.riwayatPenyakit.sekarang' => 'nullable|string|max:500',
        'dataDaftarRi.pengkajianDokter.anamnesa.riwayatPenyakit.dahulu' => 'nullable|string|max:500',
        'dataDaftarRi.pengkajianDokter.anamnesa.riwayatPenyakit.keluarga' => 'nullable|string|max:500',
        'dataDaftarRi.pengkajianDokter.anamnesa.jenisAlergi' => 'nullable|string|max:500',

        // Rekonsiliasi Obat
        'dataDaftarRi.pengkajianDokter.anamnesa.rekonsiliasiObat.*.namaObat' => 'required|string|max:500',
        'dataDaftarRi.pengkajianDokter.anamnesa.rekonsiliasiObat.*.dosis' => 'required|string|max:500',
        'dataDaftarRi.pengkajianDokter.anamnesa.rekonsiliasiObat.*.rute' => 'required|string|max:500',

        // Fisik
        'dataDaftarRi.pengkajianDokter.fisik' => 'nullable|string|max:500',

        // Anatomi
        'dataDaftarRi.pengkajianDokter.anatomi.*.kelainan' => 'nullable|string|in:Tidak Diperiksa,Tidak Ada Kelainan,Ada',
        'dataDaftarRi.pengkajianDokter.anatomi.*.desc' => 'nullable|string|max:500|required_if:dataDaftarRi.pengkajianDokter.anatomi.*.kelainan,Ada',

        // Status Lokalis
        'dataDaftarRi.pengkajianDokter.statusLokalis.deskripsiGambar' => 'nullable|string|max:500',

        // Hasil Pemeriksaan Penunjang
        'dataDaftarRi.pengkajianDokter.hasilPemeriksaanPenunjang.laboratorium' => 'nullable|string|max:500',
        'dataDaftarRi.pengkajianDokter.hasilPemeriksaanPenunjang.radiologi' => 'nullable|string|max:500',
        'dataDaftarRi.pengkajianDokter.hasilPemeriksaanPenunjang.penunjangLain' => 'nullable|string|max:500',

        // Diagnosa/Assesment
        'dataDaftarRi.pengkajianDokter.diagnosaAssesment.diagnosaAwal' => 'nullable|string|max:500',

        // Rencana
        'dataDaftarRi.pengkajianDokter.rencana.penegakanDiagnosa' => 'nullable|string|max:500',
        'dataDaftarRi.pengkajianDokter.rencana.terapi' => 'nullable|string|max:500',
        'dataDaftarRi.pengkajianDokter.rencana.terapiPulang' => 'nullable|string|max:500',

        'dataDaftarRi.pengkajianDokter.rencana.diet' => 'nullable|string|max:500',
        'dataDaftarRi.pengkajianDokter.rencana.edukasi' => 'nullable|string|max:500',
        'dataDaftarRi.pengkajianDokter.rencana.monitoring' => 'nullable|string|max:500',

        // Tanda Tangan Dokter
        'dataDaftarRi.pengkajianDokter.tandaTanganDokter.dokterPengkaji' => 'nullable|string|max:500',
        'dataDaftarRi.pengkajianDokter.tandaTanganDokter.dokterPengkajiCode' => 'nullable|string|max:500',
        'dataDaftarRi.pengkajianDokter.tandaTanganDokter.jamDokterPengkaji' => 'nullable|date_format:d/m/Y H:i:s',
    ];

    protected $messages = [
        // Anamnesa
        'dataDaftarRi.pengkajianDokter.anamnesa.keluhanUtama.required' => 'Keluhan utama wajib diisi.',
        'dataDaftarRi.pengkajianDokter.anamnesa.keluhanUtama.string' => 'Keluhan utama harus berupa teks.',
        'dataDaftarRi.pengkajianDokter.anamnesa.keluhanUtama.max' => 'Keluhan utama tidak boleh lebih dari 500 karakter.',

        'dataDaftarRi.pengkajianDokter.anamnesa.keluhanTambahan.string' => 'Keluhan tambahan harus berupa teks.',
        'dataDaftarRi.pengkajianDokter.anamnesa.keluhanTambahan.max' => 'Keluhan tambahan tidak boleh lebih dari 500 karakter.',

        'dataDaftarRi.pengkajianDokter.anamnesa.riwayatPenyakit.sekarang.string' => 'Riwayat penyakit sekarang harus berupa teks.',
        'dataDaftarRi.pengkajianDokter.anamnesa.riwayatPenyakit.sekarang.max' => 'Riwayat penyakit sekarang tidak boleh lebih dari 500 karakter.',

        'dataDaftarRi.pengkajianDokter.anamnesa.riwayatPenyakit.dahulu.string' => 'Riwayat penyakit dahulu harus berupa teks.',
        'dataDaftarRi.pengkajianDokter.anamnesa.riwayatPenyakit.dahulu.max' => 'Riwayat penyakit dahulu tidak boleh lebih dari 500 karakter.',

        'dataDaftarRi.pengkajianDokter.anamnesa.riwayatPenyakit.keluarga.string' => 'Riwayat penyakit keluarga harus berupa teks.',
        'dataDaftarRi.pengkajianDokter.anamnesa.riwayatPenyakit.keluarga.max' => 'Riwayat penyakit keluarga tidak boleh lebih dari 500 karakter.',

        'dataDaftarRi.pengkajianDokter.anamnesa.jenisAlergi.string' => 'Jenis alergi harus berupa teks.',
        'dataDaftarRi.pengkajianDokter.anamnesa.jenisAlergi.max' => 'Jenis alergi tidak boleh lebih dari 500 karakter.',

        // Rekonsiliasi Obat
        'dataDaftarRi.pengkajianDokter.anamnesa.rekonsiliasiObat.*.namaObat.required' => 'Nama obat wajib diisi.',
        'dataDaftarRi.pengkajianDokter.anamnesa.rekonsiliasiObat.*.namaObat.string' => 'Nama obat harus berupa teks.',
        'dataDaftarRi.pengkajianDokter.anamnesa.rekonsiliasiObat.*.namaObat.max' => 'Nama obat tidak boleh lebih dari 500 karakter.',

        'dataDaftarRi.pengkajianDokter.anamnesa.rekonsiliasiObat.*.dosis.required' => 'Dosis obat wajib diisi.',
        'dataDaftarRi.pengkajianDokter.anamnesa.rekonsiliasiObat.*.dosis.string' => 'Dosis obat harus berupa teks.',
        'dataDaftarRi.pengkajianDokter.anamnesa.rekonsiliasiObat.*.dosis.max' => 'Dosis obat tidak boleh lebih dari 500 karakter.',

        'dataDaftarRi.pengkajianDokter.anamnesa.rekonsiliasiObat.*.rute.required' => 'Rute penggunaan obat wajib diisi.',
        'dataDaftarRi.pengkajianDokter.anamnesa.rekonsiliasiObat.*.rute.string' => 'Rute penggunaan obat harus berupa teks.',
        'dataDaftarRi.pengkajianDokter.anamnesa.rekonsiliasiObat.*.rute.max' => 'Rute penggunaan obat tidak boleh lebih dari 500 karakter.',

        // Fisik
        'dataDaftarRi.pengkajianDokter.fisik.string' => 'Pemeriksaan fisik harus berupa teks.',
        'dataDaftarRi.pengkajianDokter.fisik.max' => 'Pemeriksaan fisik tidak boleh lebih dari 500 karakter.',

        // Anatomi
        'dataDaftarRi.pengkajianDokter.anatomi.*.kelainan.string' => 'Nilai kelainan harus berupa teks.',
        'dataDaftarRi.pengkajianDokter.anatomi.*.kelainan.in' => 'Nilai kelainan tidak valid. Pilihan yang tersedia: Tidak Diperiksa, Tidak Ada Kelainan, atau Ada.',
        'dataDaftarRi.pengkajianDokter.anatomi.*.desc.required_if' => 'Deskripsi wajib diisi jika terdapat kelainan.',
        'dataDaftarRi.pengkajianDokter.anatomi.*.desc.string' => 'Deskripsi harus berupa teks.',
        'dataDaftarRi.pengkajianDokter.anatomi.*.desc.max' => 'Deskripsi tidak boleh lebih dari 500 karakter.',

        // Status Lokalis
        'dataDaftarRi.pengkajianDokter.statusLokalis.deskripsiGambar.string' => 'Deskripsi gambar harus berupa teks.',
        'dataDaftarRi.pengkajianDokter.statusLokalis.deskripsiGambar.max' => 'Deskripsi gambar tidak boleh lebih dari 500 karakter.',

        // Hasil Pemeriksaan Penunjang
        'dataDaftarRi.pengkajianDokter.hasilPemeriksaanPenunjang.laboratorium.string' => 'Hasil laboratorium harus berupa teks.',
        'dataDaftarRi.pengkajianDokter.hasilPemeriksaanPenunjang.laboratorium.max' => 'Hasil laboratorium tidak boleh lebih dari 500 karakter.',

        'dataDaftarRi.pengkajianDokter.hasilPemeriksaanPenunjang.radiologi.string' => 'Hasil radiologi harus berupa teks.',
        'dataDaftarRi.pengkajianDokter.hasilPemeriksaanPenunjang.radiologi.max' => 'Hasil radiologi tidak boleh lebih dari 500 karakter.',

        'dataDaftarRi.pengkajianDokter.hasilPemeriksaanPenunjang.penunjangLain.string' => 'Hasil penunjang lain harus berupa teks.',
        'dataDaftarRi.pengkajianDokter.hasilPemeriksaanPenunjang.penunjangLain.max' => 'Hasil penunjang lain tidak boleh lebih dari 500 karakter.',

        // Diagnosa/Assesment
        'dataDaftarRi.pengkajianDokter.diagnosaAssesment.diagnosaAwal.string' => 'Diagnosa awal harus berupa teks.',
        'dataDaftarRi.pengkajianDokter.diagnosaAssesment.diagnosaAwal.max' => 'Diagnosa awal tidak boleh lebih dari 500 karakter.',

        // Rencana
        'dataDaftarRi.pengkajianDokter.rencana.penegakanDiagnosa.string' => 'Penegakan diagnosa harus berupa teks.',
        'dataDaftarRi.pengkajianDokter.rencana.penegakanDiagnosa.max' => 'Penegakan diagnosa tidak boleh lebih dari 500 karakter.',

        'dataDaftarRi.pengkajianDokter.rencana.terapi.string' => 'Terapi harus berupa teks.',
        'dataDaftarRi.pengkajianDokter.rencana.terapi.max' => 'Terapi tidak boleh lebih dari 500 karakter.',

        'dataDaftarRi.pengkajianDokter.rencana.terapiPulang.string' => 'Terapi Pulang harus berupa teks.',
        'dataDaftarRi.pengkajianDokter.rencana.terapiPulang.max' => 'Terapi Pulang tidak boleh lebih dari 500 karakter.',

        'dataDaftarRi.pengkajianDokter.rencana.diet.string' => 'Diet harus berupa teks.',
        'dataDaftarRi.pengkajianDokter.rencana.diet.max' => 'Diet tidak boleh lebih dari 500 karakter.',

        'dataDaftarRi.pengkajianDokter.rencana.edukasi.string' => 'Edukasi harus berupa teks.',
        'dataDaftarRi.pengkajianDokter.rencana.edukasi.max' => 'Edukasi tidak boleh lebih dari 500 karakter.',

        'dataDaftarRi.pengkajianDokter.rencana.monitoring.string' => 'Monitoring harus berupa teks.',
        'dataDaftarRi.pengkajianDokter.rencana.monitoring.max' => 'Monitoring tidak boleh lebih dari 500 karakter.',

        // Tanda Tangan Dokter
        'dataDaftarRi.pengkajianDokter.tandaTanganDokter.dokterPengkaji.string' => 'Nama dokter pengkaji harus berupa teks.',
        'dataDaftarRi.pengkajianDokter.tandaTanganDokter.dokterPengkaji.max' => 'Nama dokter pengkaji tidak boleh lebih dari 500 karakter.',

        'dataDaftarRi.pengkajianDokter.tandaTanganDokter.dokterPengkajiCode.string' => 'Kode dokter pengkaji harus berupa teks.',
        'dataDaftarRi.pengkajianDokter.tandaTanganDokter.dokterPengkajiCode.max' => 'Kode dokter pengkaji tidak boleh lebih dari 500 karakter.',

        'dataDaftarRi.pengkajianDokter.tandaTanganDokter.jamDokterPengkaji.date_format' => 'Format jam dokter pengkaji harus d/m/Y H:i:s.',
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
    private function validatePengkajianDokter(): void
    {
        // customErrorMessages


        // Proses Validasi///////////////////////////////////////////
        try {
            $this->validate($this->rules, $this->messages);
        } catch (\Illuminate\Validation\ValidationException $e) {

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Lakukan Pengecekan kembali Input Data.");
            $this->validate($this->rules, $this->messages);
        }
    }

    // insert and update record start////////////////
    public function store()
    {
        // Validate RJ
        $this->validatePengkajianDokter();

        // Logic update mode start //////////
        $this->updateDataRi($this->dataDaftarRi['riHdrNo']);

        $this->emit('syncronizeAssessmentPerawatRIFindData');
    }

    private function updateDataRi($riHdrNo): void
    {
        $this->updateJsonRI($riHdrNo, $this->dataDaftarRi);

        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Pengkajian dokter berhasil disimpan.");
    }
    // insert and update record end////////////////


    private function findData($riHdrNo): void
    {

        $this->dataDaftarRi = $this->findDataRI($riHdrNo);
        $this->dataPasien = $this->findDataMasterPasien($this->dataDaftarRi['regNo'] ?? '');

        // dd($this->dataDaftarRi);
        // jika pengkajianDokter tidak ditemukan tambah variable pengkajianDokter pda array
        if (isset($this->dataDaftarRi['pengkajianDokter']) == false) {
            $this->dataDaftarRi['pengkajianDokter'] = $this->pengkajianDokter;
        }

        // menyamakan Variabel keluhan utama
        $this->matchingMyVariable();
    }


    private function matchingMyVariable()
    {

        // keluhanUtama
        // $this->dataDaftarRi['pengkajianDokter']['keluhanUtama']['keluhanUtama'] =
        //     ($this->dataDaftarRi['pengkajianDokter']['keluhanUtama']['keluhanUtama'])
        //     ? $this->dataDaftarRi['pengkajianDokter']['keluhanUtama']['keluhanUtama']
        //     : ((isset($this->dataDaftarRi['screening']['keluhanUtama']) && !$this->dataDaftarRi['pengkajianDokter']['keluhanUtama']['keluhanUtama'])
        //         ? $this->dataDaftarRi['screening']['keluhanUtama']
        //         : "");
    }


    public function setJamDokterPengkaji($jam)
    {
        $this->dataDaftarRi['pengkajianDokter']['tandaTanganDokter']['jamDokterPengkaji'] = $jam;
    }


    public function setDokterPengkaji()
    {
        // Ambil data user yang sedang login
        $myUserCodeActive = auth()->user()->myuser_code;
        $myUserNameActive = auth()->user()->myuser_name;

        // Validasi apakah pengguna memiliki peran yang sesuai
        if (auth()->user()->hasRole('Dokter')) {
            // Isi data dokter pengkaji, kode dokter pengkaji, dan jam dokter pengkaji
            $this->dataDaftarRi['pengkajianDokter']['tandaTanganDokter']['dokterPengkaji'] = $myUserNameActive;
            $this->dataDaftarRi['pengkajianDokter']['tandaTanganDokter']['dokterPengkajiCode'] = $myUserCodeActive;

            // Simpan perubahan
            $this->store();

            // Notifikasi sukses
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("TTD Dokter berhasil diisi oleh " . $myUserNameActive);
        } else {
            // Notifikasi error jika peran tidak sesuai
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Anda tidak dapat melakukan TTD karena User Role " . $myUserNameActive . ' bukan Dokter.');
            return;
        }
    }




    public array $rekonsiliasiObat = ["namaObat" => "", "dosis" => "", "rute" => ""];

    public function addRekonsiliasiObat(): void
    {
        // Pastikan nama obat tidak kosong
        if (empty($this->rekonsiliasiObat['namaObat'])) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError("Nama Obat Kosong.");
            return;
        }

        // Cek apakah nama obat sudah ada di dalam array rekonsiliasiObat
        $cekRekonsiliasiObat = collect($this->dataDaftarRi['pengkajianDokter']['anamnesa']['rekonsiliasiObat'])
            ->where("namaObat", '=', $this->rekonsiliasiObat['namaObat'])
            ->count();

        // Jika nama obat sudah ada, tampilkan pesan error
        if ($cekRekonsiliasiObat > 0) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError("Nama Obat Sudah ada.");
            return;
        }

        // Tambahkan data obat ke dalam rekonsiliasiObat
        $this->dataDaftarRi['pengkajianDokter']['anamnesa']['rekonsiliasiObat'][] = [
            "namaObat" => $this->rekonsiliasiObat['namaObat'],
            "dosis" => $this->rekonsiliasiObat['dosis'],
            "rute" => $this->rekonsiliasiObat['rute']
        ];

        // Simpan perubahan ke dalam penyimpanan (misalnya, database atau session)
        $this->updateDataRi($this->dataDaftarRi['riHdrNo']);
        $this->emit('syncronizeAssessmentPerawatRIFindData');

        // Reset input setelah data berhasil ditambahkan
        $this->reset(['rekonsiliasiObat']);

        // Tampilkan pesan sukses
        toastr()
            ->closeOnHover(true)
            ->closeDuration(3)
            ->positionClass('toast-top-left')
            ->addSuccess("Obat berhasil ditambahkan.");
    }

    public function removeRekonsiliasiObat(string $namaObat): void
    {
        // Pastikan rekonsiliasiObat ada dan merupakan array
        if (
            !isset($this->dataDaftarRi['pengkajianDokter']['anamnesa']['rekonsiliasiObat']) ||
            !is_array($this->dataDaftarRi['pengkajianDokter']['anamnesa']['rekonsiliasiObat'])
        ) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError("Data rekonsiliasiObat tidak valid.");
            return;
        }

        // Filter array rekonsiliasiObat untuk menghapus data dengan namaObat yang sesuai
        $rekonsiliasiObat = collect($this->dataDaftarRi['pengkajianDokter']['anamnesa']['rekonsiliasiObat'])
            ->reject(function ($item) use ($namaObat) {
                return $item['namaObat'] === $namaObat;
            })
            ->values() // Reset indeks array
            ->toArray();

        // Update array rekonsiliasiObat dengan data yang sudah difilter
        $this->dataDaftarRi['pengkajianDokter']['anamnesa']['rekonsiliasiObat'] = $rekonsiliasiObat;

        // Simpan perubahan ke dalam penyimpanan (misalnya, database atau session)
        $this->updateDataRi($this->dataDaftarRi['riHdrNo']);
        $this->emit('syncronizeAssessmentPerawatRIFindData');

        // Tampilkan pesan sukses
        toastr()
            ->closeOnHover(true)
            ->closeDuration(3)
            ->positionClass('toast-top-left')
            ->addSuccess("Obat berhasil dihapus.");
    }


    public bool $isOpenEresepRI = false;
    public string $isOpenModeEresepRI = 'insert';

    public function openModalEresepRI(): void
    {
        $this->isOpenEresepRI = true;
        $this->isOpenModeEresepRI = 'insert';
    }

    public function closeModalEresepRI(): void
    {
        $this->isOpenEresepRI = false;
        $this->isOpenModeEresepRI = 'insert';
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
            'livewire.emr-r-i.mr-r-i.assessment-pengkajian-dokter.assessment-pengkajian-dokter',
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
