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

    public array $rekonsiliasiObat = ["namaObat" => "", "dosis" => "", "rute" => ""];

    public array $pengkajianDokter = [

        "anamnesa" => [
            "keluhanUtama" => "",
            "keluhanTambahan" => "",
            "riwayatPenyakit" => [
                "sekarang" => "",
                "dahulu" => "",
                "keluarga" => ""
            ],
            "riwayatPenggunaanObat" => [
                "alergiObat" => "",
                "obatKronis" => ""
            ]
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
        "tandaTanganDokter" => [
            "dokterPengkaji" => "",      // Nama dokter yang melakukan pengkajian
            "dokterPengkajiCode" => "",  // Kode unik atau identifikasi dokter
            "jamDokterPengkaji" => ""
        ]
    ];


    //////////////////////////////////////////////////////////////////////


    protected $rules = [
        'pengkajianDokter.anamnesa.keluhanUtama' => 'required|string|max:255',
        'pengkajianDokter.anamnesa.keluhanTambahan' => 'nullable|string|max:255',
        'pengkajianDokter.anamnesa.riwayatPenyakit.sekarang' => 'nullable|string|max:255',
        'pengkajianDokter.anamnesa.riwayatPenyakit.dahulu' => 'nullable|string|max:255',
        'pengkajianDokter.anamnesa.riwayatPenyakit.keluarga' => 'nullable|string|max:255',
        'pengkajianDokter.anamnesa.riwayatPenggunaanObat.alergiObat' => 'nullable|string|max:255',
        'pengkajianDokter.anamnesa.riwayatPenggunaanObat.obatKronis' => 'nullable|string|max:255',

        'pengkajianDokter.pemeriksaanFisik.*.parameter' => 'required|string|max:255',
        'pengkajianDokter.pemeriksaanFisik.*.status' => 'required|in:Normal,Abnormal,Tidak Diperiksa',
        'pengkajianDokter.pemeriksaanFisik.*.keterangan' => 'nullable|string|max:255',

        'pengkajianDokter.statusLokalis.deskripsiGambar' => 'nullable|string|max:1000',

        'pengkajianDokter.tandaTanganDokter.namaDokter' => 'required|string|max:255',
        'pengkajianDokter.tandaTanganDokter.tandaTangan' => 'nullable|string|max:1000',
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
