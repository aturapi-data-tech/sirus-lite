<?php

namespace App\Http\Livewire\EmrRI\MrRI\Perencanaan;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;

use Carbon\Carbon;
use App\Http\Traits\BPJS\VclaimTrait;
use App\Http\Traits\EmrRI\EmrRITrait;
use App\Http\Traits\MasterPasien\MasterPasienTrait;


class Perencanaan extends Component
{
    use WithPagination, EmrRITrait, MasterPasienTrait;

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
    public array $dataPasien = [];

    public array $perencanaan = [
        "tindakLanjut" => [
            "tindakLanjut" => "",
            "tindakLanjutKode"      => "", // Kode SNOMED CT, seperti "419099009"
            "keteranganTindakLanjut" => "",

            "noSep"             => "",
            "statusPulang"      => "", // 1,3,4,5
            "noSuratMeninggal"  => "",
            "tglMeninggal"      => "",
            "tglPulang"         => "",
            "noLPManual"        => "",
            "user"              => "",
        ],

        "dischargePlanning" => [
            "pelayananBerkelanjutan" => [
                "pelayananBerkelanjutan" => "Tidak Ada",
                "ketPelayananBerkelanjutan" => "",
                "pelayananBerkelanjutanData" => [],
            ],
            "penggunaanAlatBantu" => [
                "penggunaanAlatBantu" => "Tidak Ada",
                "ketPenggunaanAlatBantu" => "",
                "penggunaanAlatBantuData" => [],
            ],
        ],
    ];

    protected $rules = [
        // Rules untuk tindakLanjut
        'dataDaftarRi.perencanaan.tindakLanjut.tindakLanjut' => 'required|string|max:255',
        'dataDaftarRi.perencanaan.tindakLanjut.tindakLanjutKode' => 'required|string|max:50',
        'dataDaftarRi.perencanaan.tindakLanjut.keteranganTindakLanjut' => 'nullable|string|max:200',

        // Rules untuk dischargePlanning.pelayananBerkelanjutan
        'dataDaftarRi.perencanaan.dischargePlanning.pelayananBerkelanjutan.pelayananBerkelanjutan' => 'required|in:Ada,Tidak Ada',
        'dataDaftarRi.perencanaan.dischargePlanning.pelayananBerkelanjutan.ketPelayananBerkelanjutan' => [
            'nullable',
            'string',
            'max:200',
            'required_if:dataDaftarRi.perencanaan.dischargePlanning.pelayananBerkelanjutan.pelayananBerkelanjutan,Ada',
        ],
        'dataDaftarRi.perencanaan.dischargePlanning.pelayananBerkelanjutan.pelayananBerkelanjutanData' => [
            'nullable',
            'array',
            'required_if:dataDaftarRi.perencanaan.dischargePlanning.pelayananBerkelanjutan.pelayananBerkelanjutan,Ada',
        ],
        'dataDaftarRi.perencanaan.dischargePlanning.pelayananBerkelanjutan.pelayananBerkelanjutanData.*.status' => [
            'boolean',
        ],

        // Rules untuk dischargePlanning.penggunaanAlatBantu
        'dataDaftarRi.perencanaan.dischargePlanning.penggunaanAlatBantu.penggunaanAlatBantu' => 'required|in:Ada,Tidak Ada',
        'dataDaftarRi.perencanaan.dischargePlanning.penggunaanAlatBantu.ketPenggunaanAlatBantu' => [
            'nullable',
            'string',
            'max:200',
            'required_if:dataDaftarRi.perencanaan.dischargePlanning.penggunaanAlatBantu.penggunaanAlatBantu,Ada',
        ],
        'dataDaftarRi.perencanaan.dischargePlanning.penggunaanAlatBantu.penggunaanAlatBantuData' => [
            'nullable',
            'array',
            'required_if:dataDaftarRi.perencanaan.dischargePlanning.penggunaanAlatBantu.penggunaanAlatBantu,Ada',
        ],
        'dataDaftarRi.perencanaan.dischargePlanning.penggunaanAlatBantu.penggunaanAlatBantuData.*.status' => [
            'boolean',
        ],
    ];

    //////////////////////////////////////////////////////////////////////
    // Opsi-opsi untuk tindak lanjut
    public array $tindakLanjutOptions = [
        [
            "tindakLanjut"         => "Pulang Sehat",
            "tindakLanjutKode"     => "371827001",
            "tindakLanjutKodeBpjs" => 1,
        ],
        [
            "tindakLanjut"         => "Pulang dengan Permintaan Sendiri",
            "tindakLanjutKode"     => "266707007",
            "tindakLanjutKodeBpjs" => 3,
        ],
        [
            "tindakLanjut"         => "Pulang Pindah / Rujuk",
            "tindakLanjutKode"     => "306206005",
            "tindakLanjutKodeBpjs" => 5, // tidak ada padanan di BPJS SEP
        ],
        [
            "tindakLanjut"         => "Pulang Tanpa Perbaikan",
            "tindakLanjutKode"     => "371828006",
            "tindakLanjutKodeBpjs" => 5, // tidak ada padanan di BPJS SEP
        ],
        [
            "tindakLanjut"         => "Meninggal",
            "tindakLanjutKode"     => "419099009",
            "tindakLanjutKodeBpjs" => 4,
        ],
        [
            "tindakLanjut"         => "Lain-lain",
            "tindakLanjutKode"     => "74964007",
            "tindakLanjutKodeBpjs" => 5,
        ],
    ];

    // Opsi-opsi untuk pelayanan berkelanjutan
    public array $pelayananBerkelanjutanOptions = [
        ["pelayananBerkelanjutan" => "Ada"],
        ["pelayananBerkelanjutan" => "Tidak Ada"],
    ];

    // Opsi-opsi untuk penggunaan alat bantu
    public array $penggunaanAlatBantuOptions = [
        ["penggunaanAlatBantu" => "Ada"],
        ["penggunaanAlatBantu" => "Tidak Ada"],
    ];

    public array $pelayananBerkelanjutan = [
        [
            "deskripsi" => "Perawatan luka",
            "status" => false
        ],
        [
            "deskripsi" => "Diabetes melitus",
            "status" => false
        ],
        [
            "deskripsi" => "Penyakit paru obstruktif kronis (PPOK)",
            "status" => false
        ],
        [
            "deskripsi" => "Infeksi HIV/AIDS",
            "status" => false
        ],
        [
            "deskripsi" => "Penyakit ginjal kronis",
            "status" => false
        ],
        [
            "deskripsi" => "Tuberkulosis (TB)",
            "status" => false
        ],
        [
            "deskripsi" => "Stroke",
            "status" => false
        ],
        [
            "deskripsi" => "Kemoterapi",
            "status" => false
        ],
        [
            "deskripsi" => "Rehabilitasi medis",
            "status" => false
        ],
        [
            "deskripsi" => "Pemantauan jantung",
            "status" => false
        ],
        [
            "deskripsi" => "Pemantauan tekanan darah",
            "status" => false
        ],
        [
            "deskripsi" => "Terapi pernapasan",
            "status" => false
        ],
        [
            "deskripsi" => "Pemantauan gula darah",
            "status" => false
        ],
    ];

    // Opsi-opsi untuk penggunaan alat bantu (detail)
    public array $penggunaanAlatBantu = [
        [
            "deskripsi" => "Alat bantu jalan (tongkat, walker, kruk)",
            "status" => false
        ],
        [
            "deskripsi" => "Terapi oksigen (tabung oksigen, konsentrator oksigen)",
            "status" => false
        ],
        [
            "deskripsi" => "Infus intravena (untuk pemberian cairan atau obat di rumah)",
            "status" => false
        ],
        [
            "deskripsi" => "Kateter urine menetap (untuk pasien dengan gangguan buang air kecil)",
            "status" => false
        ],
        [
            "deskripsi" => "Selang nasogastrik (untuk pemberian nutrisi atau obat melalui hidung)",
            "status" => false
        ],
        [
            "deskripsi" => "Tabung trakeostomi (untuk pasien dengan gangguan pernapasan)",
            "status" => false
        ],
        [
            "deskripsi" => "Kantong kolostomi (untuk pasien pasca-operasi kolostomi)",
            "status" => false
        ],
        [
            "deskripsi" => "Alat bantu pernapasan (CPAP, ventilator portabel)",
            "status" => false
        ],
        [
            "deskripsi" => "Kursi roda (untuk pasien dengan gangguan mobilitas berat)",
            "status" => false
        ],
        [
            "deskripsi" => "Alat bantu dengar (untuk pasien dengan gangguan pendengaran)",
            "status" => false
        ],
    ];

    // Method untuk mereset data pelayanan berkelanjutan
    public function updatedDataDaftarRiPerencanaanDischargePlanningPelayananBerkelanjutanPelayananBerkelanjutan($value)
    {
        if ($value === 'Tidak Ada') {
            // Reset keterangan
            $this->dataDaftarRi['perencanaan']['dischargePlanning']['pelayananBerkelanjutan']['ketPelayananBerkelanjutan'] = null;

            // Reset opsi pelayanan berkelanjutan
            foreach ($this->pelayananBerkelanjutan as $index => $opsi) {
                $this->dataDaftarRi['perencanaan']['dischargePlanning']['pelayananBerkelanjutanData'][$index]['status'] = false;
            }
        }
    }

    // Method untuk mereset data penggunaan alat bantu
    public function updatedDataDaftarRiPerencanaanDischargePlanningPenggunaanAlatBantuPenggunaanAlatBantu($value)
    {
        if ($value === 'Tidak Ada') {
            // Reset keterangan
            $this->dataDaftarRi['perencanaan']['dischargePlanning']['penggunaanAlatBantu']['ketPenggunaanAlatBantu'] = null;

            // Reset opsi penggunaan alat bantu
            foreach ($this->penggunaanAlatBantu as $index => $opsi) {
                $this->dataDaftarRi['perencanaan']['dischargePlanning']['penggunaanAlatBantuData'][$index]['status'] = false;
            }
        }
    }



    // Fungsi untuk mengupdate status dan keterangan
    public function updatedDataDaftarRiPerencanaanDischargePlanningPenggunaanAlatBantuPenggunaanAlatBantuData($value, $index)
    {
        $index = explode('.', $index)[6]; // Ambil index dari path
        if ($value === true) {
            // Jika status true, pastikan keterangan tidak kosong
            if (empty($this->dataDaftarRi['perencanaan']['dischargePlanning']['penggunaanAlatBantu']['penggunaanAlatBantuData'][$index]['keterangan'])) {
                $this->dataDaftarRi['perencanaan']['dischargePlanning']['penggunaanAlatBantu']['penggunaanAlatBantuData'][$index]['keterangan'] = 'Keterangan default';
            }
        } else {
            // Jika status false, hapus keterangan menggunakan unset
            unset($this->dataDaftarRi['perencanaan']['dischargePlanning']['penggunaanAlatBantu']['penggunaanAlatBantuData'][$index]['keterangan']);
        }
    }

    public function updatedDataDaftarRiPerencanaanDischargePlanningPelayananBerkelanjutanPelayananBerkelanjutanData($value, $index)
    {
        $index = explode('.', $index)[6]; // Ambil index dari path
        if ($value === true) {
            // Jika status true, pastikan keterangan tidak kosong
            if (empty($this->dataDaftarRi['perencanaan']['dischargePlanning']['pelayananBerkelanjutan']['pelayananBerkelanjutanData'][$index]['keterangan'])) {
                $this->dataDaftarRi['perencanaan']['dischargePlanning']['pelayananBerkelanjutan']['pelayananBerkelanjutanData'][$index]['keterangan'] = 'Keterangan default';
            }
        } else {
            // Jika status false, hapus keterangan menggunakan unset
            unset($this->dataDaftarRi['perencanaan']['dischargePlanning']['pelayananBerkelanjutan']['pelayananBerkelanjutanData'][$index]['keterangan']);
        }
    }

    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////
    public function updated($propertyName, $value)
    {
        $this->validateOnly($propertyName);
        // Jalankan hanya jika radio SNOMED berubah
        if ($propertyName === 'dataDaftarRi.perencanaan.tindakLanjut.tindakLanjut') {
            $this->syncTindakLanjut($value);
        }
        $this->store();
    }

    private function syncTindakLanjut(string $kodeSnomed): void
    {
        // Cari opsi yang cocok
        $selected = collect($this->tindakLanjutOptions)
            ->firstWhere('tindakLanjutKode', $kodeSnomed);

        // Jika ditemukan, set kedua field sekaligus
        if ($selected) {
            $this->dataDaftarRi['perencanaan']['tindakLanjut']['tindakLanjutKode']
                = $selected['tindakLanjutKode'];

            $this->dataDaftarRi['perencanaan']['tindakLanjut']['statusPulang']
                = $selected['tindakLanjutKodeBpjs'];
        }
    }




    // ////////////////
    // RJ Logic
    // ////////////////




    // insert and update record start////////////////
    public function store()
    {

        // Logic update mode start //////////
        $this->updateDataRi($this->dataDaftarRi['riHdrNo']);

        $this->emit('syncronizeAssessmentPerawatRIFindData');
    }

    // Set tanggal meninggal (format dd/mm/yyyy)
    public function setTglMeninggal($date)
    {
        $this->dataDaftarRi['perencanaan']['tindakLanjut']['tglMeninggal'] = $date;
    }

    // Set tanggal pulang (format dd/mm/yyyy)
    public function setTglPulang($date)
    {
        $this->dataDaftarRi['perencanaan']['tindakLanjut']['tglPulang'] = $date;
    }

    public function updateTglPulangBPJS(): void
    {

        $tindak = $this->dataDaftarRi['perencanaan']['tindakLanjut'];

        /* ============================================================
     * 0. Cek status klaim & ketersediaan SEP
     * ========================================================== */
        $klaimStatus = DB::table('rsmst_klaimtypes')
            ->where('klaim_id', $this->dataDaftarRi['klaimId'] ?? '')
            ->value('klaim_status') ?? 'UMUM';
        $noSep = $tindak['noSep'] ?? '';
        $tglSepRaw = $this->dataDaftarRi['sep']['reqSep']['request']['t_sep']['tglSep'] ?? null;

        // --- Bukan klaim BPJS --------------------------------------
        if ($klaimStatus !== 'BPJS') {
            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('Gagal: Klaim ini bukan BPJS, tidak dapat mengirim update tanggal pulang.');
            return;
        }
        // --- No SEP belum tersedia ---------------------------------
        if (empty($noSep)) {
            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('Gagal: Nomor SEP belum tersedia, tidak dapat update tanggal pulang.');
            return;
        }

        // ───── Konversi format tanggal ─────
        try {
            $tglPulang = $tindak['tglPulang']
                ? Carbon::createFromFormat('d/m/Y', $tindak['tglPulang'])->format('Y-m-d')
                : null;

            $tglMeninggal = $tindak['tglMeninggal']
                ? Carbon::createFromFormat('d/m/Y', $tindak['tglMeninggal'])->format('Y-m-d')
                : null;
        } catch (\Exception $e) {
            // Kirim toast error & hentikan proses
            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('Format tanggal tidak valid: ' . $e->getMessage());

            return; // keluar dari fungsi pushUpdateTglPulangBPJS()
        }

        if (empty(($tglSepRaw))) {
            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('Pembuatan SEP bukan melalui siRUS, Tgl SEP belum tersedia, tidak dapat update tanggal pulang.');

            return; // hentikan proses jika tgl sep belum tersedia
        }

        $SEPJsonReq = [
            'request' => [
                't_sep' => [
                    'noSep'            => $tindak['noSep'],
                    'statusPulang'     => $tindak['statusPulang'],
                    'noSuratMeninggal' => $tindak['noSuratMeninggal'],
                    'tglMeninggal'     => $tglMeninggal,
                    'tglPulang'        => $tglPulang,
                    'noLPManual'       => $tindak['noLPManual'],
                    'user'             => 'siRUS',
                    // field bantu lain jika perlu (isKLL, tglSEP, dst.)

                    // ---------- field bantu (hanya dipakai di sisi aplikasi) ----------
                    'isKLL'            => $tindak['isKLL'] ?? 0,    // 0/1
                    'tglSep'           => $tglSepRaw, // YYYY-mm-dd
                    'isAlreadyReferred' => false // true/false
                ],
            ],
        ];
        /* ------------------------------------------------------------
     * 2. Panggil Trait  ➜  sep_updtglplg()
     * ---------------------------------------------------------- */
        $HttpGetBpjs = VclaimTrait::sep_updtglplg($SEPJsonReq)->getOriginalContent();
        /* ------------------------------------------------------------
     * 3. Tangani respon & beri notifikasi
     * ---------------------------------------------------------- */
        if ($HttpGetBpjs['metadata']['code'] == 200) {
            // Update entri lokal jika perlu
            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addSuccess('Update Tgl Pulang: ' .
                    $HttpGetBpjs['metadata']['code'] . ' ' . $HttpGetBpjs['metadata']['message']);
        } else {
            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('Update Tgl Pulang: ' .
                    $HttpGetBpjs['metadata']['code'] . ' ' . $HttpGetBpjs['metadata']['message']);
        }
    }

    private function updateDataRi($riHdrNo): void
    {

        $this->updateJsonRI($riHdrNo, $this->dataDaftarRi);

        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Perencanaan berhasil disimpan.");
    }
    // insert and update record end////////////////


    private function findData($riHdrNo): void
    {

        $this->dataDaftarRi = $this->findDataRI($riHdrNo);
        $this->dataPasien = $this->findDataMasterPasien($this->dataDaftarRi['regNo'] ?? '');

        // dd($this->dataDaftarRi);
        // jika perencanaan tidak ditemukan tambah variable perencanaan pda array
        if (isset($this->dataDaftarRi['perencanaan']) == false) {
            $this->dataDaftarRi['perencanaan'] = $this->perencanaan;
        }

        /* --------------------------------------------------------
     *  Auto-set noSep jika klaimId = 'JM'
     * ------------------------------------------------------ */
        $klaimStatus = DB::table('rsmst_klaimtypes')
            ->where('klaim_id', $this->dataDaftarRi['klaimId'] ?? '')
            ->value('klaim_status') ?? 'UMUM';
        if ($klaimStatus === 'BPJS' && empty($this->dataDaftarRi['perencanaan']['tindakLanjut']['noSep'])) {
            $this->dataDaftarRi['perencanaan']['tindakLanjut']['noSep'] =
                $this->dataDaftarRi['sep']['noSep'] ?? '';
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
            'livewire.emr-r-i.mr-r-i.perencanaan.perencanaan',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Perencanaan',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Pasien Rawat Jalan',
            ]
        );
    }
    // select data end////////////////


}
