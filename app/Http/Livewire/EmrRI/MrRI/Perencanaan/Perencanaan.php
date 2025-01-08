<?php

namespace App\Http\Livewire\EmrRI\MrRI\Perencanaan;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;

use Carbon\Carbon;

use App\Http\Traits\EmrRI\EmrRITrait;


class Perencanaan extends Component
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

    // data SKDP / perencanaan=>[]
    public array $perencanaan =
    [
        "terapiTab" => "Terapi",
        "terapi" => [
            "terapi" => ""
        ],

        "tindakLanjutTab" => "Tindak Lanjut",
        "tindakLanjut" => [
            "tindakLanjut" => "",
            "keteranganTindakLanjut" => "",
            "tindakLanjutOptions" => [
                ["tindakLanjut" => "MRS"],
                ["tindakLanjut" => "KRS"],
                ["tindakLanjut" => "APS"],
                ["tindakLanjut" => "Rujuk"],
                ["tindakLanjut" => "Meninggal"],
                ["tindakLanjut" => "Lain-lain"],
            ],

        ],

        "pengkajianMedisTab" => "Petugas Medis",
        "pengkajianMedis" => [
            "waktuPemeriksaan" => "",
            "selesaiPemeriksaan" => "",
            "drPemeriksa" => "",
        ],
        // Kontrol pakai program lama

        "rawatInapTab" => "Rawat Inap",
        "rawatInap" => [
            "noRef" => "",
            "tanggal" => "", //dd/mm/yyyy
            "keterangan" => "",
        ],

        "dischargePlanningTab" => "Discharge Planning",
        "dischargePlanning" => [
            "pelayananBerkelanjutan" => [
                "pelayananBerkelanjutan" => "Tidak Ada",
                "pelayananBerkelanjutanOption" => [
                    ["pelayananBerkelanjutan" => "Tidak Ada"],
                    ["pelayananBerkelanjutan" => "Ada"],
                ],
            ],
            "pelayananBerkelanjutanOpsi" => [
                "rawatLuka" => [],
                "dm" => [],
                "ppok" => [],
                "hivAids" => [],
                "dmTerapiInsulin" => [],
                "ckd" => [],
                "tb" => [],
                "stroke" => [],
                "kemoterapi" => [],
            ],

            "penggunaanAlatBantu" => [
                "penggunaanAlatBantu" => "Tidak Ada",
                "penggunaanAlatBantuOption" => [
                    ["penggunaanAlatBantu" => "Tidak Ada"],
                    ["penggunaanAlatBantu" => "Ada"],
                ],
            ],
            "penggunaanAlatBantuOpsi" => [
                "kateterUrin" => [],
                "ngt" => [],
                "traechotomy" => [],
                "colostomy" => [],
            ],
        ]
    ];
    //////////////////////////////////////////////////////////////////////


    protected $rules = [

        'dataDaftarRi.perencanaan.pengkajianMedis.waktuPemeriksaan' => 'required|date_format:d/m/Y H:i:s',
        'dataDaftarRi.perencanaan.pengkajianMedis.selesaiPemeriksaan' => 'required|date_format:d/m/Y H:i:s'

    ];



    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////
    public function updated($propertyName)
    {
        // dd($propertyName);
        $this->validateOnly($propertyName);
        if ($propertyName != 'activeTabRacikanNonRacikan') {
            $this->store();
        }
    }




    // resert input private////////////////
    private function resetInputFields(): void
    {

        // resert validation
        $this->resetValidation();
        // resert input kecuali
        $this->resetExcept([
            'riHdrNoRef'
        ]);
    }





    // ////////////////
    // RJ Logic
    // ////////////////


    // validate Data RJ//////////////////////////////////////////////////
    private function validateDataRi(): void
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

        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Perencanaan berhasil disimpan.");
    }
    // insert and update record end////////////////


    private function findData($riHdrNo): void
    {

        $this->dataDaftarRi = $this->findDataRI($riHdrNo);
        // dd($this->dataDaftarRi);
        // jika perencanaan tidak ditemukan tambah variable perencanaan pda array
        if (isset($this->dataDaftarRi['perencanaan']) == false) {
            $this->dataDaftarRi['perencanaan'] = $this->perencanaan;
        }
    }

    // set data RJno / NoBooking / NoAntrian / klaimId / kunjunganId
    private function setDataPrimer(): void {}

    public function setWaktuPemeriksaan($myTime)
    {
        $this->dataDaftarRi['perencanaan']['pengkajianMedis']['waktuPemeriksaan'] = $myTime;
    }

    public function setSelesaiPemeriksaan($myTime)
    {
        $this->dataDaftarRi['perencanaan']['pengkajianMedis']['selesaiPemeriksaan'] = $myTime;
    }

    private function validateDrPemeriksa()
    {
        // Validasi dulu
        $messages = [];
        $myRules = [
            // 'dataDaftarRi.pemeriksaan.tandaVital.sistolik' => 'required|numeric',
            // 'dataDaftarRi.pemeriksaan.tandaVital.distolik' => 'required|numeric',
            'dataDaftarRi.pemeriksaan.tandaVital.frekuensiNadi' => 'required|numeric',
            'dataDaftarRi.pemeriksaan.tandaVital.frekuensiNafas' => 'required|numeric',
            'dataDaftarRi.pemeriksaan.tandaVital.suhu' => 'required|numeric',
            'dataDaftarRi.pemeriksaan.tandaVital.spo2' => 'numeric',
            'dataDaftarRi.pemeriksaan.tandaVital.gda' => 'numeric',

            'dataDaftarRi.pemeriksaan.nutrisi.bb' => 'required|numeric',
            'dataDaftarRi.pemeriksaan.nutrisi.tb' => 'required|numeric',
            'dataDaftarRi.pemeriksaan.nutrisi.imt' => 'required|numeric',
            'dataDaftarRi.pemeriksaan.nutrisi.lk' => 'numeric',
            'dataDaftarRi.pemeriksaan.nutrisi.lila' => 'numeric',

            'dataDaftarRi.anamnesa.pengkajianPerawatan.jamDatang' => 'required|date_format:d/m/Y H:i:s',
        ];
        // Proses Validasi///////////////////////////////////////////
        try {
            $this->validate($myRules, $messages);
        } catch (\Illuminate\Validation\ValidationException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Anda tidak dapat melakukan TTD-E karena data pemeriksaan belum lengkap.');
            $this->validate($myRules, $messages);
        }
        // Validasi dulu
    }
    public function setDrPemeriksa()
    {
        // $myRoles = json_decode(auth()->user()->roles, true);
        $myUserCodeActive = auth()->user()->myuser_code;
        $myUserNameActive = auth()->user()->myuser_name;
        // $myUserTtdActive = auth()->user()->myuser_ttd_image;

        // Validasi dulu
        // cek apakah data pemeriksaan sudah dimasukkan atau blm
        $this->validateDrPemeriksa();

        if (auth()->user()->hasRole('Dokter')) {
            if ($this->dataDaftarRi['drId'] == $myUserCodeActive) {
                if (isset($this->dataDaftarRi['perencanaan']['pengkajianMedis']['drPemeriksa'])) {
                    if (!$this->dataDaftarRi['perencanaan']['pengkajianMedis']['drPemeriksa']) {
                        $this->dataDaftarRi['perencanaan']['pengkajianMedis']['drPemeriksa'] = isset($this->dataDaftarRi['drDesc']) ? ($this->dataDaftarRi['drDesc'] ? $this->dataDaftarRi['drDesc'] : 'Dokter pemeriksa') : 'Dokter pemeriksa-';
                    }
                } else {
                    $this->dataDaftarRi['perencanaan']['pengkajianMedisTab'] = 'Pengkajian Medis';
                    $this->dataDaftarRi['perencanaan']['pengkajianMedis']['drPemeriksa'] = isset($this->dataDaftarRi['drDesc']) ? ($this->dataDaftarRi['drDesc'] ? $this->dataDaftarRi['drDesc'] : 'Dokter pemeriksa') : 'Dokter pemeriksa-';
                }
                $this->store();
            } else {
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Anda tidak dapat melakukan TTD-E karena Bukan Pasien ' . $myUserNameActive);
            }
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Anda tidak dapat melakukan TTD-E karena User Role ' . $myUserNameActive . ' Bukan Dokter');
        }
    }

    // /////////////////eresep open////////////////////////
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

    public string $activeTabRacikanNonRacikan = 'NonRacikan';
    public array $EmrMenuRacikanNonRacikan = [
        [
            'ermMenuId' => 'NonRacikan',
            'ermMenuName' => 'NonRacikan',
        ],
        [
            'ermMenuId' => 'Racikan',
            'ermMenuName' => 'Racikan',
        ],
    ];

    public function simpanTerapi(): void
    {
        $eresep = '' . PHP_EOL;
        if (isset($this->dataDaftarRi['eresep'])) {

            foreach ($this->dataDaftarRi['eresep'] as $key => $value) {
                // NonRacikan
                $catatanKhusus = ($value['catatanKhusus']) ? ' (' . $value['catatanKhusus'] . ')' : '';
                $eresep .=  'R/' . ' ' . $value['productName'] . ' | No. ' . $value['qty'] . ' | S ' .  $value['signaX'] . 'dd' . $value['signaHari'] . $catatanKhusus . PHP_EOL;
            }
        }

        $eresepRacikan = '' . PHP_EOL;
        if (isset($this->dataDaftarRi['eresepRacikan'])) {
            // Racikan
            foreach ($this->dataDaftarRi['eresepRacikan'] as $key => $value) {
                $jmlRacikan = ($value['qty']) ? 'Jml Racikan ' . $value['qty'] . ' | ' . $value['catatan'] . ' | S ' . $value['catatanKhusus'] . PHP_EOL : '';
                $dosis = isset($value['dosis']) ? ($value['dosis'] ? $value['dosis'] : '') : '';
                $eresepRacikan .= $value['noRacikan'] . '/ ' . $value['productName'] . ' - ' . $dosis .  PHP_EOL . $jmlRacikan;
            };
        }
        $this->dataDaftarRi['perencanaan']['terapi']['terapi'] = $eresep . $eresepRacikan;

        $this->store();
        $this->closeModalEresepRI();
    }

    // /////////////////////////////////////////


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
