<?php

namespace App\Http\Livewire\EmrRI\MrRI\Pemeriksaan;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

use App\Http\Traits\EmrRI\EmrRITrait;
use App\Http\Traits\customErrorMessagesTrait;

use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;


class Pemeriksaan extends Component
{
    use WithPagination, EmrRITrait, customErrorMessagesTrait, WithFileUploads;

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

    // data pemeriksaan=>[]
    public array $pemeriksaan = [
        "umumTab" => "Umum",
        "tandaVital" => [
            "keadaanUmum" => "",
            "tingkatKesadaran" => "",
            "tingkatKesadaranOptions" => [
                ["tingkatKesadaran" => "Sadar Baik / Alert"],
                ["tingkatKesadaran" => "Berespon Dengan Kata-Kata / Voice"],
                ["tingkatKesadaran" => "Hanya Beresponse Jika Dirangsang Nyeri / Pain"],
                ["tingkatKesadaran" => "Pasien Tidak Sadar / Unresponsive"],
                ["tingkatKesadaran" => "Gelisah Atau Bingung"],
                ["tingkatKesadaran" => "Acute Confusional States"],
            ],
            // jalan nafas
            "jalanNafas" => [
                "jalanNafas" => "",
                "jalanNafasOptions" => [
                    ["jalanNafas" => "Paten"],
                    ["jalanNafas" => "Obstruksi Partial"],
                    ["jalanNafas" => "Obstruksi Total"],
                    ["jalanNafas" => "Stridor"],
                ]
            ],

            // pernafasan
            "pernafasan" => [
                "pernafasan" => "",
                "pernafasanOptions" => [
                    ["pernafasan" => "Normal"],
                    ["pernafasan" => "Kusmaul"],
                    ["pernafasan" => "Takipneu"],
                    ["pernafasan" => "Retraktif"],
                    ["pernafasan" => "Dangkal"],

                ]
            ],

            "gerakDada" => [
                "gerakDada" => "",
                "gerakDadaOptions" => [
                    ["gerakDada" => "Simetris"],
                    ["gerakDada" => "Asimetris"],
                ]
            ],

            // sirkulasi
            "sirkulasi" => [
                "sirkulasi" => "",
                "sirkulasiOptions" => [
                    ["sirkulasi" => "Normal"],
                    ["sirkulasi" => "Sianosis"],
                    ["sirkulasi" => "Berkeringat"],
                    ["sirkulasi" => "Joundise"],
                    ["sirkulasi" => "Pucat"],

                ]
            ],

            // neurologis

            "e" => "", //number
            "m" => "", //number
            "v" => "", //number
            "gcs" => "", //number
            "sistolik" => "", //number
            "distolik" => "", //number
            "frekuensiNafas" => "", //number
            "frekuensiNadi" => "", //number
            "suhu" => "", //number
            // "saturasiO2" => "", //number
            "spo2" => "", //number
            "gda" => "", //number
            "waktuPemeriksaan" => "", //date dd/mm/yyyy hh24:mi:ss
        ],

        "nutrisi" => [
            "bb" => "", //number
            "tb" => "", //number
            "imt" => "", //number
            "lk" => "", //number
            "lila" => "" //number
        ],
        "fungsional" => [
            "alatBantu" => "",
            "prothesa" => "",
            "cacatTubuh" => "",
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

        "fungsional" => [
            "alatBantu" => "",
            "prothesa" => "",
            "cacatTubuh" => "",
        ],
        "eeg" => [
            "hasilPemeriksaan" => "",
            "hasilPemeriksaanSebelumnya" => "",
            "mriKepala" => "",
            "hasilPerekaman" => "",
            "kesimpulan" => "",
            "saran" => "",

        ],
        "emg" => [
            "keluhanPasien" => "",
            "pengobatan     " => "",
            "td" => "", //number
            "rr" => "", //number
            "hr" => "", //number
            "s" => "", //number
            "gcs" => "",
            "fkl" => "",
            "nprs" => "",
            "rclRctl" => "",
            "nnCr" => "",
            "nnCrLain" => "",
            "motorik" => "",
            "pergerakan" => "",
            "kekuatan" => "",
            "extremitasSuperior" => "",
            "extremitasInferior" => "",
            "tonus" => "",
            "refleksFisiologi" => "",
            "refleksPatologis" => "",
            "sensorik" => "",
            "otonom" => "",
            "emcEmgFindings" => "",
            "impresion" => "",
        ],
        "ravenTest" => [
            "skoring" => "", //number
            "presentil" => "", //number
            "interpretasi" => "",
            "anjuran" => "",


        ],
        "penunjang" => ""

    ];

    public $filePDF, $descPDF;
    public bool $isOpenRekamMedisuploadpenunjangHasil;


    //////////////////////////////////////////////////////////////////////

    // open and close modal start////////////////
    //  modal status////////////////

    public bool $isOpenLaboratorium = false;
    public string $isOpenModeLaboratorium = 'insert';

    public  $isPemeriksaanLaboratorium = [];
    public  $isPemeriksaanLaboratoriumSelected = [];
    public int $isPemeriksaanLaboratoriumSelectedKeyHdr = 0;
    public int $isPemeriksaanLaboratoriumSelectedKeyDtl = 0;



    public bool $isOpenRadiologi = false;
    public string $isOpenModeRadiologi = 'insert';

    public  $isPemeriksaanRadiologi = [];
    public  $isPemeriksaanRadiologiSelected = [];
    public int $isPemeriksaanRadiologiSelectedKeyHdr = 0;
    public int $isPemeriksaanRadiologiSelectedKeyDtl = 0;


    // open and close modal start////////////////
    //  modal status////////////////

    public $tingkatKesadaranLov = [];
    public $tingkatKesadaranLovStatus = 0;
    public $tingkatKesadaranLovSearch = '';



    protected $rules = [
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
    ];

    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////

    public function updated($propertyName)
    {
        // dd($propertyName);
        $this->validateOnly($propertyName);
        $this->scoringIMT();
        $this->store();
    }

    // lab
    private function openModalLaboratorium(): void
    {
        $this->isOpenLaboratorium = true;
        $this->isOpenModeLaboratorium = 'insert';
    }

    public function pemeriksaanLaboratorium()
    {
        $this->openModalLaboratorium();
        $this->renderisPemeriksaanLaboratorium();
        // $this->findData($id);
    }

    public function closeModalLaboratorium(): void
    {
        $this->reset(['isOpenLaboratorium', 'isOpenModeLaboratorium', 'isPemeriksaanLaboratorium', 'isPemeriksaanLaboratoriumSelected', 'isPemeriksaanLaboratoriumSelectedKeyHdr', 'isPemeriksaanLaboratoriumSelectedKeyDtl']);
    }

    private function renderisPemeriksaanLaboratorium()
    {
        if (empty($this->isPemeriksaanLaboratorium)) {
            $isPemeriksaanLaboratorium = DB::table('lbmst_clabitems')
                ->select('clabitem_desc', 'clabitem_id', 'price', 'clabitem_group', 'item_code')
                ->whereNull('clabitem_group')
                ->whereNotNull('clabitem_desc')
                ->orderBy('clabitem_desc', 'asc')
                ->get();

            $this->isPemeriksaanLaboratorium = json_decode(
                $isPemeriksaanLaboratorium->map(function ($isPemeriksaanLaboratorium) {
                    $isPemeriksaanLaboratorium->labStatus = 0;

                    return $isPemeriksaanLaboratorium;
                }),
                true
            );
        }
    }

    public function PemeriksaanLaboratoriumIsSelectedFor($key): void
    {
        $this->isPemeriksaanLaboratorium[$key]['labStatus'] = $this->isPemeriksaanLaboratorium[$key]['labStatus'] ? 0 : 1;
        $this->renderPemeriksaanLaboratoriumIsSelected($key);
    }

    public function RemovePemeriksaanLaboratoriumIsSelectedFor($key): void
    {
        $this->isPemeriksaanLaboratorium[$key]['labStatus'] = $this->isPemeriksaanLaboratorium[$key]['labStatus'] ? 0 : 1;
        $this->renderPemeriksaanLaboratoriumIsSelected($key);
    }

    private function renderPemeriksaanLaboratoriumIsSelected($key): void
    {
        $this->isPemeriksaanLaboratoriumSelected = collect($this->isPemeriksaanLaboratorium)
            ->where('labStatus', 1);
    }

    public function kirimLaboratorium()
    {
        $sql = "select ri_status  from rstxn_rihdrs where rihdr_no=:riHdrNo";
        $checkStatusRI = DB::scalar($sql, [
            "riHdrNo" => $this->dataDaftarRi['riHdrNo'],
        ]);

        if ($checkStatusRI == 'I') {
            // hasil Key = 0 atau urutan pemeriksan lab lebih dari 1
            $this->isPemeriksaanLaboratoriumSelectedKeyHdr = collect(isset($this->dataDaftarRi['pemeriksaan']['pemeriksaanPenunjang']['lab']) ? $this->dataDaftarRi['pemeriksaan']['pemeriksaanPenunjang']['lab'] : [])->count();

            $sql = "select nvl(max(to_number(checkup_no))+1,1) from lbtxn_checkuphdrs";
            $checkupNo = DB::scalar($sql);

            // array Hdr
            $this->dataDaftarRi['pemeriksaan']['pemeriksaanPenunjang']['lab'][$this->isPemeriksaanLaboratoriumSelectedKeyHdr]['labHdr']['labHdrNo'] =  $checkupNo;
            $this->dataDaftarRi['pemeriksaan']['pemeriksaanPenunjang']['lab'][$this->isPemeriksaanLaboratoriumSelectedKeyHdr]['labHdr']['labHdrDate'] = Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s');


            // insert Hdr
            DB::table('lbtxn_checkuphdrs')->insert([
                'reg_no' => $this->dataDaftarRi['regNo'],
                'dr_id' => $this->dataDaftarRi['drId'],
                'checkup_date' => DB::raw("to_date('" . Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s') . "','dd/mm/yyyy hh24:mi:ss')"),
                'status_rjri' => 'RI',
                'checkup_status' => 'P',
                'ref_no' => $this->dataDaftarRi['riHdrNo'],
                'checkup_no' => $checkupNo,

            ]);


            // hasil Key Dtl dari jml yang selected -1 karena array dimulai dari 0
            $this->isPemeriksaanLaboratoriumSelectedKeyDtl = collect($this->isPemeriksaanLaboratoriumSelected)->count() - 1;


            // array Dtl
            $this->dataDaftarRi['pemeriksaan']['pemeriksaanPenunjang']['lab'][$this->isPemeriksaanLaboratoriumSelectedKeyHdr]['labHdr']['labDtl'] =  collect($this->isPemeriksaanLaboratorium)
                ->where('labStatus', 1)
                ->toArray();

            // insert Dtl
            foreach ($this->dataDaftarRi['pemeriksaan']['pemeriksaanPenunjang']['lab'][$this->isPemeriksaanLaboratoriumSelectedKeyHdr]['labHdr']['labDtl'] as $labDtl) {
                $sql = "select nvl(to_number(max(checkup_dtl))+1,1) from LBTXN_CHECKUPDTLS";
                $checkupDtl = DB::scalar($sql);

                // insert Prise checkup dtl
                DB::table('lbtxn_checkupdtls')->insert([
                    'clabitem_id' => $labDtl['clabitem_id'],
                    'checkup_no' => $checkupNo,
                    'checkup_dtl' => $checkupDtl,
                    'lab_item_code' => $labDtl['item_code'],
                    'price' => $labDtl['price']
                ]);

                foreach ($this->isPemeriksaanLaboratoriumSelected as $isPemeriksaanLaboratoriumSelected) {

                    // insert No Prise checkup dtl
                    $items = DB::table('lbmst_clabitems')->select('clabitem_desc', 'clabitem_id', 'price', 'clabitem_group', 'item_code')
                        ->where('clabitem_group', $labDtl['clabitem_id'])
                        ->orderBy('item_seq', 'asc')
                        ->orderBy('clabitem_desc', 'asc')
                        ->get();

                    foreach ($items as $item) {
                        $sql = "select nvl(to_number(max(checkup_dtl))+1,1) from LBTXN_CHECKUPDTLS";
                        $checkupDtl = DB::scalar($sql);

                        DB::table('lbtxn_checkupdtls')->insert([
                            'clabitem_id' => $item->clabitem_id,
                            'checkup_no' => $checkupNo,
                            'checkup_dtl' => $checkupDtl,
                            'lab_item_code' => $item->item_code,
                            'price' => $item->price
                        ]);

                        $this->isPemeriksaanLaboratoriumSelectedKeyDtl = $this->isPemeriksaanLaboratoriumSelectedKeyDtl + 1;
                    }
                }


                $this->isPemeriksaanLaboratoriumSelectedKeyDtl = $this->isPemeriksaanLaboratoriumSelectedKeyDtl + 1;
            }



            $this->updateDataRi($this->dataDaftarRi['riHdrNo']);
            $this->closeModalLaboratorium();
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Pasien Sudah Pulang, Anda tidak bisa meneruskan pemeriksaan ini.");
            return;
        }
    }
    // Lab

    // Rad
    private function openModalRadiologi(): void
    {
        $this->isOpenRadiologi = true;
        $this->isOpenModeRadiologi = 'insert';
    }

    public function pemeriksaanRadiologi()
    {
        $this->openModalRadiologi();
        $this->renderisPemeriksaanRadiologi();
        // $this->findData($id);
    }

    public function closeModalRadiologi(): void
    {
        $this->reset(['isOpenRadiologi', 'isOpenModeRadiologi', 'isPemeriksaanRadiologi', 'isPemeriksaanRadiologiSelected', 'isPemeriksaanRadiologiSelectedKeyHdr', 'isPemeriksaanRadiologiSelectedKeyDtl']);
    }

    private function renderisPemeriksaanRadiologi()
    {
        if (empty($this->isPemeriksaanRadiologi)) {
            $isPemeriksaanRadiologi = DB::table('rsmst_radiologis ')
                ->select('rad_desc', 'rad_price', 'rad_id')
                ->orderBy('rad_desc', 'asc')
                ->get();

            $this->isPemeriksaanRadiologi = json_decode(
                $isPemeriksaanRadiologi->map(function ($isPemeriksaanRadiologi) {
                    $isPemeriksaanRadiologi->radStatus = 0;

                    return $isPemeriksaanRadiologi;
                }),
                true
            );
        }
    }

    public function PemeriksaanRadiologiIsSelectedFor($key): void
    {
        $this->isPemeriksaanRadiologi[$key]['radStatus'] = $this->isPemeriksaanRadiologi[$key]['radStatus'] ? 0 : 1;
        $this->renderPemeriksaanRadiologiIsSelected($key);
    }

    public function RemovePemeriksaanRadiologiIsSelectedFor($key): void
    {
        $this->isPemeriksaanRadiologi[$key]['radStatus'] = $this->isPemeriksaanRadiologi[$key]['radStatus'] ? 0 : 1;
        $this->renderPemeriksaanRadiologiIsSelected($key);
    }

    private function renderPemeriksaanRadiologiIsSelected($key): void
    {
        $this->isPemeriksaanRadiologiSelected = collect($this->isPemeriksaanRadiologi)
            ->where('radStatus', 1);
    }

    public function kirimRadiologi()
    {

        $sql = "select ri_status  from rstxn_rihdrs where rihdr_no=:riHdrNo";
        $checkStatusRI = DB::scalar($sql, [
            "riHdrNo" => $this->dataDaftarRi['riHdrNo'],
        ]);

        if ($checkStatusRI == 'I') {
            // hasil Key = 0 atau urutan pemeriksan lab lebih dari 1
            $this->isPemeriksaanRadiologiSelectedKeyHdr = collect(isset($this->dataDaftarRi['pemeriksaan']['pemeriksaanPenunjang']['rad']) ? $this->dataDaftarRi['pemeriksaan']['pemeriksaanPenunjang']['rad'] : [])->count();

            // $sql = "select nvl(max(rirad_no)+1,1) from rstxn_riradiologs";
            // $checkupNo = DB::scalar($sql);

            // array Hdr
            $this->dataDaftarRi['pemeriksaan']['pemeriksaanPenunjang']['rad'][$this->isPemeriksaanRadiologiSelectedKeyHdr]['radHdr']['radHdrNo'] =  $this->dataDaftarRi['riHdrNo'];
            $this->dataDaftarRi['pemeriksaan']['pemeriksaanPenunjang']['rad'][$this->isPemeriksaanRadiologiSelectedKeyHdr]['radHdr']['radHdrDate'] = Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s');


            // insert Hdr (Radiologi tidak di insert header / ikut txn rj)
            // DB::table('lbtxn_checkuphdrs')->insert([
            //     'reg_no' => $this->dataDaftarRi['regNo'],
            //     'dr_id' => $this->dataDaftarRi['drId'],
            //     'checkup_date' => DB::raw("to_date('" . Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s') . "','dd/mm/yyyy hh24:mi:ss')"),
            //     'status_rjri' => 'RJ',
            //     'checkup_status' => 'P',
            //     'ref_no' => $this->dataDaftarRi['riHdrNo'],
            //     'checkup_no' => $checkupNo,

            // ]);


            // hasil Key Dtl dari jml yang selected -1 karena array dimulai dari 0
            $this->isPemeriksaanRadiologiSelectedKeyDtl = collect($this->isPemeriksaanRadiologiSelected)->count() - 1;


            // array Dtl
            $this->dataDaftarRi['pemeriksaan']['pemeriksaanPenunjang']['rad'][$this->isPemeriksaanRadiologiSelectedKeyHdr]['radHdr']['radDtl'] =  collect($this->isPemeriksaanRadiologi)
                ->where('radStatus', 1)
                ->toArray();

            // insert Dtl
            foreach ($this->dataDaftarRi['pemeriksaan']['pemeriksaanPenunjang']['rad'][$this->isPemeriksaanRadiologiSelectedKeyHdr]['radHdr']['radDtl'] as $radDtl) {
                $sql = "select nvl(max(rirad_no)+1,1) from rstxn_riradiologs";
                $checkupDtl = DB::scalar($sql);
                // insert Prise checkup dtl
                DB::table('rstxn_riradiologs')->insert([
                    'rirad_no' => $checkupDtl,
                    'rad_id' => $radDtl['rad_id'],
                    'rihdr_no' => $this->dataDaftarRi['riHdrNo'],
                    'rirad_price' => $radDtl['rad_price'],
                    'dr_radiologi' => 'dr. M.A. Budi Purwito, Sp.Rad.',
                    'waktu_entry' => DB::raw("to_date('" . Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s') . "','dd/mm/yyyy hh24:mi:ss')"),
                    'rirad_date' => DB::raw("to_date('" . Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s') . "','dd/mm/yyyy hh24:mi:ss')"),
                ]);


                $this->isPemeriksaanRadiologiSelectedKeyDtl = $this->isPemeriksaanRadiologiSelectedKeyDtl + 1;
            }



            $this->updateDataRi($this->dataDaftarRi['riHdrNo']);
            $this->closeModalRadiologi();
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Pasien Sudah Pulang, Anda tidak bisa meneruskan pemeriksaan ini.");
            return;
        }
    }
    // Rad



    // /////////tingkatKesadaran////////////
    public function clicktingkatKesadaranlov()
    {
        $this->tingkatKesadaranLovStatus = true;
        $this->tingkatKesadaranLov = $this->dataDaftarRi['pemeriksaan']['tandaVital']['tingkatKesadaranOptions'];
    }
    public function updatedtingkatKesadaranlovsearch()
    {
        // Variable Search
        $search = $this->tingkatKesadaranLovSearch;

        // check LOV by id
        $tingkatKesadaran = collect($this->dataDaftarRi['pemeriksaan']['tandaVital']['tingkatKesadaranOptions'])
            ->where('tingkatKesadaran', '=', $search)
            ->first();

        if ($tingkatKesadaran) {
            $this->dataDaftarRi['pemeriksaan']['tandaVital']['tingkatKesadaranOptions'] = $tingkatKesadaran['tingkatKesadaran'];

            $this->tingkatKesadaranLovStatus = false;
            $this->tingkatKesadaranLovSearch = '';
        } else {
            // if there is no id found and check (min 3 char on search)
            if (strlen($search) < 3) {
                $this->tingkatKesadaranLov = $this->dataDaftarRi['pemeriksaan']['tandaVital']['tingkatKesadaranOptions'];
            } else {
                $this->tingkatKesadaranLov = collect($this->dataDaftarRi['pemeriksaan']['tandaVital']['tingkatKesadaranOptions'])
                    ->filter(function ($item) use ($search) {
                        return false !== stristr($item['tingkatKesadaran'], $search);
                    });
            }
            $this->tingkatKesadaranLovStatus = true;
            $this->dataDaftarRi['pemeriksaan']['tandaVital']['tingkatKesadaran'] = '';
        }
    }
    // /////////////////////
    // LOV selected start
    public function setMytingkatKesadaranLov($id)
    {
        $this->dataDaftarRi['pemeriksaan']['tandaVital']['tingkatKesadaran'] = $id;
        $this->tingkatKesadaranLovStatus = false;
        $this->tingkatKesadaranLovSearch = '';
    }
    // LOV selected end
    // /////////////////////




    // ////////////////
    // RJ Logic
    // ////////////////


    // validate Data RJ//////////////////////////////////////////////////
    private function validateDataRi(): void
    {
        // customErrorMessages
        // $messages = customErrorMessagesTrait::messages();
        $messages = [];

        //Cek Usia Anak dibawah 13th tidak di cek tekanan darah
        $sql = "select birth_date from rsmst_pasiens where reg_no=:regNo";
        $birthDate = DB::scalar($sql, [
            "regNo" => $this->dataDaftarRi['regNo'],
        ]);
        $cekUsia = Carbon::createFromFormat('Y-m-d H:i:s', $birthDate)->diff(Carbon::now(env('APP_TIMEZONE')))->format('%y');

        if ($cekUsia > 13) {
            $this->rules['dataDaftarRi.pemeriksaan.tandaVital.sistolik'] = 'required|numeric';
            $this->rules['dataDaftarRi.pemeriksaan.tandaVital.distolik'] = 'required|numeric';
        }

        // $rules = [];



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

        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Pemeriksaan berhasil disimpan.");
    }
    // insert and update record end////////////////


    private function findData($rjno): void
    {

        $this->dataDaftarRi = $this->findDataRI($rjno);
        // dd($this->dataDaftarRi);
        // jika pemeriksaan tidak ditemukan tambah variable pemeriksaan pda array
        if (isset($this->dataDaftarRi['pemeriksaan']) == false) {
            $this->dataDaftarRi['pemeriksaan'] = $this->pemeriksaan;
        }
    }


    // set data RJno / NoBooking / NoAntrian / klaimId / kunjunganId
    private function setDataPrimer(): void {}

    private function scoringIMT(): void
    {
        $bb = (isset($this->dataDaftarRi['pemeriksaan']['nutrisi']['bb'])
            ? ($this->dataDaftarRi['pemeriksaan']['nutrisi']['bb']
                ? $this->dataDaftarRi['pemeriksaan']['nutrisi']['bb']
                : 1)
            : 1);
        $tb = (isset($this->dataDaftarRi['pemeriksaan']['nutrisi']['tb'])
            ? ($this->dataDaftarRi['pemeriksaan']['nutrisi']['tb']
                ? $this->dataDaftarRi['pemeriksaan']['nutrisi']['tb']
                : 1)
            : 1);;


        $this->dataDaftarRi['pemeriksaan']['nutrisi']['imt'] = round($bb / (($tb / 100) * ($tb / 100)), 2);
    }


    public function uploadHasilPenunjang(): void
    {
        // validate
        // cek status transaksi
        $checkRIStatus = $this->checkRIStatus($this->riHdrNoRef);
        if ($checkRIStatus) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Pasien Sudah Pulang, Trasaksi Terkunci.");
            return;
        }

        // cek status transaksi
        $this->validateDataRi($this->riHdrNoRef);


        // customErrorMessages
        $messages = [];
        // require nik ketika pasien tidak dikenal
        $rules = [
            "filePDF" => "bail|required|mimes:pdf|max:10240",
            "descPDF" => "bail|required|max:255"
        ];

        // Proses Validasi///////////////////////////////////////////
        $this->validate($rules, $messages);
        // upload photo
        $uploadHasilPenunjangfile = $this->filePDF->store('uploadHasilPenunjang');

        $this->dataDaftarRi['pemeriksaan']['uploadHasilPenunjang'][] = [
            'file' => $uploadHasilPenunjangfile,
            'desc' => $this->descPDF,
            'tglUpload' => Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s'),
            'penanggungJawab' => [
                'userLog' => auth()->user()->myuser_name,
                'userLogDate' => Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s'),
                'userLogCode' => auth()->user()->myuser_code
            ]
        ];
        $this->reset(['filePDF', 'descPDF'],);
        $this->store();
    }

    public function deleteHasilPenunjang($file): void
    {
        // Foto/////////////////////////////////////////////////////////////////////////
        Storage::delete($file);
        $deleteHasilpenunjang = collect($this->dataDaftarRi['pemeriksaan']['uploadHasilPenunjang'])->where("file", '!=', $file)->toArray();
        $this->dataDaftarRi['pemeriksaan']['uploadHasilPenunjang'] = $deleteHasilpenunjang;
        $this->store();
        //
    }

    public function openModalHasilPenunjang($file)
    {

        if (Storage::exists($file)) {
            $this->isOpenRekamMedisuploadpenunjangHasil = true;
            $this->filePDF = $file;
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('File tidak ditemukan');
            return;
        }
    }

    public function closeModalHasilPenunjang()
    {
        $this->isOpenRekamMedisuploadpenunjangHasil = false;
        $this->reset(['filePDF']);
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
            'livewire.emr-r-i.mr-r-i.pemeriksaan.pemeriksaan',
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
