<?php

namespace App\Http\Livewire\EmrRI\MrRI\PengkajianDokter;

use Livewire\Component;
use Livewire\WithPagination;

use App\Http\Traits\EmrRI\EmrRITrait;
use App\Http\Traits\customErrorMessagesTrait;

class PengkajianDokter extends Component
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
        'dataDaftarRi.pengkajianDokter.anamnesa.keluhanUtama' => 'required|string|max:255',
        'dataDaftarRi.pengkajianDokter.anamnesa.keluhanTambahan' => 'nullable|string|max:255',
        'dataDaftarRi.pengkajianDokter.anamnesa.riwayatPenyakit.sekarang' => 'nullable|string|max:255',
        'dataDaftarRi.pengkajianDokter.anamnesa.riwayatPenyakit.dahulu' => 'nullable|string|max:255',
        'dataDaftarRi.pengkajianDokter.anamnesa.riwayatPenyakit.keluarga' => 'nullable|string|max:255',
        'dataDaftarRi.pengkajianDokter.anamnesa.jenisAlergi' => 'nullable|string|max:255',

        // Rekonsiliasi Obat
        'dataDaftarRi.pengkajianDokter.anamnesa.rekonsiliasiObat.*.namaObat' => 'required|string|max:255',
        'dataDaftarRi.pengkajianDokter.anamnesa.rekonsiliasiObat.*.dosis' => 'required|string|max:255',
        'dataDaftarRi.pengkajianDokter.anamnesa.rekonsiliasiObat.*.rute' => 'required|string|max:255',

        // Fisik
        'dataDaftarRi.pengkajianDokter.fisik' => 'nullable|string|max:255',

        // Anatomi
        'dataDaftarRi.pengkajianDokter.anatomi.*.kelainan' => 'nullable|string|in:Tidak Diperiksa,Tidak Ada Kelainan,Ada',
        'dataDaftarRi.pengkajianDokter.anatomi.*.desc' => 'nullable|string|max:255|required_if:dataDaftarRi.pengkajianDokter.anatomi.*.kelainan,Ada',

        // Status Lokalis
        'dataDaftarRi.pengkajianDokter.statusLokalis.deskripsiGambar' => 'nullable|string|max:255',

        // Hasil Pemeriksaan Penunjang
        'dataDaftarRi.pengkajianDokter.hasilPemeriksaanPenunjang.laboratorium' => 'nullable|string|max:255',
        'dataDaftarRi.pengkajianDokter.hasilPemeriksaanPenunjang.radiologi' => 'nullable|string|max:255',
        'dataDaftarRi.pengkajianDokter.hasilPemeriksaanPenunjang.penunjangLain' => 'nullable|string|max:255',

        // Diagnosa/Assesment
        'dataDaftarRi.pengkajianDokter.diagnosaAssesment.diagnosaAwal' => 'nullable|string|max:255',

        // Rencana
        'dataDaftarRi.pengkajianDokter.rencana.penegakanDiagnosa' => 'nullable|string|max:255',
        'dataDaftarRi.pengkajianDokter.rencana.terapi' => 'nullable|string|max:255',
        'dataDaftarRi.pengkajianDokter.rencana.diet' => 'nullable|string|max:255',
        'dataDaftarRi.pengkajianDokter.rencana.edukasi' => 'nullable|string|max:255',
        'dataDaftarRi.pengkajianDokter.rencana.monitoring' => 'nullable|string|max:255',

        // Tanda Tangan Dokter
        'dataDaftarRi.pengkajianDokter.tandaTanganDokter.dokterPengkaji' => 'nullable|string|max:255',
        'dataDaftarRi.pengkajianDokter.tandaTanganDokter.dokterPengkajiCode' => 'nullable|string|max:255',
        'dataDaftarRi.pengkajianDokter.tandaTanganDokter.jamDokterPengkaji' => 'nullable|date_format:d/m/Y H:i:s',
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
        $this->validatePengkajianDokter();

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
        $myUserCodeActive = auth()->user()->myuserCode;
        $myUserNameActive = auth()->user()->myuserName;

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




    // when new form instance
    public function mount()
    {

        $this->findData($this->riHdrNoRef);
    }



    // select data start////////////////
    public function render()
    {

        return view(
            'livewire.emr-r-i.mr-r-i.pengkajian-dokter.pengkajian-dokter',
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
