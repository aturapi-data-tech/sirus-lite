<?php

namespace App\Http\Livewire\EmrUGD\MrUGD\Pemeriksaan;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

use App\Http\Traits\EmrUGD\EmrUGDTrait;

use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;


class Pemeriksaan extends Component
{
    use WithPagination, WithFileUploads, EmrUGDTrait;

    // listener from blade////////////////
    protected $listeners = [
        'storeAssessmentDokterUGD' => 'store',
    ];


    //////////////////////////////
    // Ref on top bar
    //////////////////////////////
    public $rjNoRef;

    // dataDaftarUgd RJ
    public array $dataDaftarUgd = [];

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
        // 'dataDaftarUgd.pemeriksaan.tandaVital.sistolik' => 'required|numeric',
        // 'dataDaftarUgd.pemeriksaan.tandaVital.distolik' => 'required|numeric',
        'dataDaftarUgd.pemeriksaan.tandaVital.frekuensiNadi' => 'required|numeric',
        'dataDaftarUgd.pemeriksaan.tandaVital.frekuensiNafas' => 'required|numeric',
        'dataDaftarUgd.pemeriksaan.tandaVital.suhu' => 'required|numeric',
        'dataDaftarUgd.pemeriksaan.tandaVital.spo2' => 'numeric',
        'dataDaftarUgd.pemeriksaan.tandaVital.gda' => 'numeric',

        'dataDaftarUgd.pemeriksaan.nutrisi.bb' => 'required|numeric',
        'dataDaftarUgd.pemeriksaan.nutrisi.tb' => 'required|numeric',
        'dataDaftarUgd.pemeriksaan.nutrisi.imt' => 'required|numeric',
        'dataDaftarUgd.pemeriksaan.nutrisi.lk' => 'numeric',
        'dataDaftarUgd.pemeriksaan.nutrisi.lila' => 'numeric',
    ];

    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////

    public function updated($propertyName)
    {
        // dd($propertyName);
        $this->validateOnly($propertyName);
        $this->scoringIMT();
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
            ->where('labStatus', 1)->values()->all();
    }

    public function kirimLaboratorium()
    {
        // 0) Gate status (QB)
        $checkStatusRJ = DB::table('rstxn_ugdhdrs')
            ->where('rj_no', $this->dataDaftarUgd['rjNo'])
            ->value('rj_status');

        if ($checkStatusRJ !== 'A') {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError("Pasien Sudah Pulang, Anda tidak bisa meneruskan pemeriksaan ini.");
            return;
        }

        $rjNo = $this->dataDaftarUgd['rjNo'];

        // 1) Kumpulkan pilihan & anak (di luar lock biar cepat)
        $selected = collect($this->isPemeriksaanLaboratoriumSelected ?? [])->values();
        if ($selected->isEmpty()) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addInfo('Tidak ada item laboratorium yang dipilih.');
            return;
        }

        $selectedIds = $selected->pluck('clabitem_id')->unique()->values();

        $children = DB::table('lbmst_clabitems')
            ->select('clabitem_id', 'price', 'clabitem_group', 'item_code', 'item_seq', 'clabitem_desc')
            ->whereIn('clabitem_group', $selectedIds)
            ->orderBy('clabitem_group')
            ->orderBy('item_seq')
            ->orderBy('clabitem_desc')
            ->get()
            ->groupBy('clabitem_group');

        // 2) Critical section kecil
        $lockKey = "ugd:{$rjNo}";
        Cache::lock($lockKey, 5)->block(3, function () use ($rjNo, $selected, $children) {
            DB::transaction(function () use ($rjNo, $selected, $children) {

                // a) Ambil nomor header SEKALI
                $checkupNo = (int) DB::table('lbtxn_checkuphdrs')
                    ->max(DB::raw('nvl(to_number(checkup_no),0)')) + 1;

                // b) Insert header (QB)
                DB::table('lbtxn_checkuphdrs')->insert([
                    'reg_no'         => $this->dataDaftarUgd['regNo'],
                    'dr_id'          => $this->dataDaftarUgd['drId'],
                    'checkup_date'   => DB::raw("to_date('" . now(config('app.timezone'))->format('d/m/Y H:i:s') . "','dd/mm/yyyy hh24:mi:ss')"),
                    'status_rjri'    => 'UGD',
                    'checkup_status' => 'P',
                    'ref_no'         => $rjNo,
                    'checkup_no'     => $checkupNo,
                ]);

                // c) Susun semua baris detail di memory
                $rows = [];
                foreach ($selected as $lab) {
                    $rows[] = [
                        'clabitem_id'   => $lab['clabitem_id'],
                        'checkup_no'    => $checkupNo,
                        // checkup_dtl diisi nanti (auto-increment lokal)
                        'lab_item_code' => $lab['item_code'],
                        'price'         => $lab['price'],
                    ];

                    foreach ($children->get($lab['clabitem_id'], collect()) as $child) {
                        $rows[] = [
                            'clabitem_id'   => $child->clabitem_id,
                            'checkup_no'    => $checkupNo,
                            'lab_item_code' => $child->item_code,
                            'price'         => $child->price,
                        ];
                    }
                }

                if (empty($rows)) {
                    // Tidak ada detail, cukup patch JSON
                    $hdrIdx = collect($this->dataDaftarUgd['pemeriksaan']['pemeriksaanPenunjang']['lab'] ?? [])->count();
                    $this->dataDaftarUgd['pemeriksaan']['pemeriksaanPenunjang']['lab'][$hdrIdx]['labHdr'] = [
                        'labHdrNo'   => $checkupNo,
                        'labHdrDate' => now(config('app.timezone'))->format('d/m/Y H:i:s'),
                        'labDtl'     => [],
                    ];
                    return;
                }

                // d) Ambil MAX(detail) SEKALI, lalu auto-increment lokal
                $nextDtl = (int) DB::table('lbtxn_checkupdtls')
                    ->max(DB::raw('nvl(to_number(checkup_dtl),0)'));

                foreach ($rows as &$r) {
                    $nextDtl++;
                    $r['checkup_dtl'] = $nextDtl;
                }
                unset($r);

                // e) Bulk insert detail via QB (chunk biar aman)
                foreach (array_chunk($rows, 200) as $chunk) {
                    DB::table('lbtxn_checkupdtls')->insert($chunk);
                }

                // f) Patch JSON lokal
                $hdrIdx = collect($this->dataDaftarUgd['pemeriksaan']['pemeriksaanPenunjang']['lab'] ?? [])->count();
                $this->dataDaftarUgd['pemeriksaan']['pemeriksaanPenunjang']['lab'][$hdrIdx]['labHdr'] = [
                    'labHdrNo'   => $checkupNo,
                    'labHdrDate' => now(config('app.timezone'))->format('d/m/Y H:i:s'),
                    'labDtl'     => collect($this->isPemeriksaanLaboratorium)->where('labStatus', 1)->values()->all(),
                ];
            });
        });

        // 3) Commit JSON UGD
        $this->store();
        $this->closeModalLaboratorium();
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
            $isPemeriksaanRadiologi = DB::table('rsmst_radiologis')
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
            ->where('radStatus', 1)->values()->all();
    }

    public function kirimRadiologi()
    {
        $sql = "select rj_status from rstxn_ugdhdrs where rj_no=:rjNo";
        $checkStatusRJ = DB::scalar($sql, ["rjNo" => $this->dataDaftarUgd['rjNo']]);

        if ($checkStatusRJ !== 'A') {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError("Pasien Sudah Pulang, Anda tidak bisa meneruskan pemeriksaan ini.");
            return;
        }

        $rjNo   = $this->dataDaftarUgd['rjNo'];
        $lockKey = "ugd:{$rjNo}";

        Cache::lock($lockKey, 5)->block(3, function () use ($rjNo) {
            DB::transaction(function () use ($rjNo) {
                foreach ($this->isPemeriksaanRadiologiSelected as $radDtl) {
                    $maxDtl = DB::table('rstxn_ugdrads')->max(DB::raw('to_number(rad_dtl)'));
                    $next   = ($maxDtl ?? 0) + 1;

                    DB::table('rstxn_ugdrads')->insert([
                        'rad_dtl'     => $next,
                        'rad_id'      => $radDtl['rad_id'],
                        'rj_no'       => $rjNo,
                        'rad_price'   => $radDtl['rad_price'],
                        'dr_radiologi' => 'dr. M.A. Budi Purwito, Sp.Rad.',
                        'waktu_entry' => DB::raw("to_date('" . now(config('app.timezone'))->format('d/m/Y H:i:s') . "','dd/mm/yyyy hh24:mi:ss')"),
                    ]);
                }

                // patch JSON lokal → subtree penunjang/rad
                $hdrIdx = collect($this->dataDaftarUgd['pemeriksaan']['pemeriksaanPenunjang']['rad'] ?? [])->count();
                $this->dataDaftarUgd['pemeriksaan']['pemeriksaanPenunjang']['rad'][$hdrIdx]['radHdr'] = [
                    'radHdrNo'   => $rjNo,
                    'radHdrDate' => now(config('app.timezone'))->format('d/m/Y H:i:s'),
                    'radDtl'     => collect($this->isPemeriksaanRadiologi)->where('radStatus', 1)->values()->all(),
                ];
            });
        });

        $this->store();
        $this->closeModalRadiologi();
    }
    // Rad



    // /////////tingkatKesadaran////////////
    public function clicktingkatKesadaranlov()
    {
        $this->tingkatKesadaranLovStatus = true;
        $this->tingkatKesadaranLov = $this->dataDaftarUgd['pemeriksaan']['tandaVital']['tingkatKesadaranOptions'];
    }
    public function updatedTingkatKesadaranLovSearch()
    {
        // Variable Search
        $search = $this->tingkatKesadaranLovSearch;

        // check LOV by id
        $tingkatKesadaran = collect($this->dataDaftarUgd['pemeriksaan']['tandaVital']['tingkatKesadaranOptions'])
            ->where('tingkatKesadaran', '=', $search)
            ->first();

        if ($tingkatKesadaran) {
            $this->dataDaftarUgd['pemeriksaan']['tandaVital']['tingkatKesadaranOptions'] = $tingkatKesadaran['tingkatKesadaran'];

            $this->tingkatKesadaranLovStatus = false;
            $this->tingkatKesadaranLovSearch = '';
        } else {
            // if there is no id found and check (min 3 char on search)
            if (strlen($search) < 3) {
                $this->tingkatKesadaranLov = $this->dataDaftarUgd['pemeriksaan']['tandaVital']['tingkatKesadaranOptions'];
            } else {
                $this->tingkatKesadaranLov = collect($this->dataDaftarUgd['pemeriksaan']['tandaVital']['tingkatKesadaranOptions'])
                    ->filter(function ($item) use ($search) {
                        return false !== stristr($item['tingkatKesadaran'], $search);
                    });
            }
            $this->tingkatKesadaranLovStatus = true;
            $this->dataDaftarUgd['pemeriksaan']['tandaVital']['tingkatKesadaran'] = '';
        }
    }
    // /////////////////////
    // LOV selected start
    public function setMytingkatKesadaranLov($id)
    {
        $this->dataDaftarUgd['pemeriksaan']['tandaVital']['tingkatKesadaran'] = $id;
        $this->tingkatKesadaranLovStatus = false;
        $this->tingkatKesadaranLovSearch = '';
        $this->tingkatKesadaranLov       = [];
    }
    // LOV selected end
    // /////////////////////




    // ////////////////
    // RJ Logic
    // ////////////////


    // validate Data RJ//////////////////////////////////////////////////
    private function validateDataUgd(): void
    {
        // siapkan rules base (boleh kosong diawal)
        $rules = $this->rules;
        $messages = [];   // <— TAROH DI SINI, LOCAL SCOPE

        // ambil birth_date
        $birthDate = DB::table('rsmst_pasiens')
            ->where('reg_no', $this->dataDaftarUgd['regNo'] ?? null)
            ->value('birth_date');

        if ($birthDate) {
            try {
                $age = Carbon::parse($birthDate)
                    ->diffInYears(Carbon::now(config('app.timezone')));
            } catch (\Throwable $e) {
                $age = null;
            }

            if (!is_null($age) && $age > 13) {
                $rules['dataDaftarUgd.pemeriksaan.tandaVital.sistolik'] = 'required|numeric';
                $rules['dataDaftarUgd.pemeriksaan.tandaVital.distolik'] = 'required|numeric';

                // contoh: tambahkan messages-nya disini juga secara lokal
                $messages['dataDaftarUgd.pemeriksaan.tandaVital.sistolik.required'] =
                    'Sistolik wajib untuk usia > 13';
                $messages['dataDaftarUgd.pemeriksaan.tandaVital.distolik.required'] =
                    'Diastolik wajib untuk usia > 13';
            }
        }

        try {
            $this->validate($rules, $messages);
        } catch (ValidationException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError("Lakukan Pengecekan kembali Input Data.");
            $this->validate($rules, $messages);
        }
    }


    // insert and update record start////////////////
    public function store()
    {

        // Validate RJ
        $this->validateDataUgd();

        $rjNo = $this->dataDaftarUgd['rjNo'] ?? $this->rjNoRef ?? null;
        if (!$rjNo) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("rjNo kosong.");
            return;
        }

        $lockKey = "ugd:{$rjNo}"; // shared lock antar modul UGD

        try {
            Cache::lock($lockKey, 5)->block(3, function () use ($rjNo) {
                $fresh = $this->findDataUGD($rjNo) ?: [];

                if (!isset($fresh['pemeriksaan']) || !is_array($fresh['pemeriksaan'])) {
                    $fresh['pemeriksaan'] = $this->pemeriksaan ?? [];
                }

                $formSubtree = $this->dataDaftarUgd['pemeriksaan'] ?? [];
                if (!is_array($formSubtree)) $formSubtree = [];
                $fresh['pemeriksaan'] = $formSubtree;

                DB::transaction(function () use ($rjNo, $fresh) {
                    // (opsional tapi baik) kunci baris header
                    DB::table('rstxn_ugdhdrs')->where('rj_no', $rjNo)->first();

                    $this->updateJsonUGD($rjNo, $fresh);
                    $this->emit('ugd:refresh-summary');
                });

                $this->dataDaftarUgd = $fresh;
            });
        } catch (LockTimeoutException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Sistem sibuk, gagal memperoleh lock. Coba lagi.');
            return;
        }


        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
            ->addSuccess('Pemeriksaan berhasil disimpan.');
    }


    private function findData($rjno): void
    {

        $this->dataDaftarUgd = $this->findDataUGD($rjno);
        // dd($this->dataDaftarUgd);
        // jika pemeriksaan tidak ditemukan tambah variable pemeriksaan pda array
        if (isset($this->dataDaftarUgd['pemeriksaan']) == false) {
            $this->dataDaftarUgd['pemeriksaan'] = $this->pemeriksaan;
        }
    }


    // set data RJno / NoBooking / NoAntrian / klaimId / kunjunganId

    private function scoringIMT(): void
    {
        $bb = (float) ($this->dataDaftarUgd['pemeriksaan']['nutrisi']['bb'] ?? 0);
        $tb = (float) ($this->dataDaftarUgd['pemeriksaan']['nutrisi']['tb'] ?? 0);

        if ($bb <= 0 || $tb <= 0) {
            $this->dataDaftarUgd['pemeriksaan']['nutrisi']['imt'] = null;
            return;
        }

        $m = $tb / 100;
        $this->dataDaftarUgd['pemeriksaan']['nutrisi']['imt'] = round($bb / ($m * $m), 2);
    }



    public function uploadHasilPenunjang(): void
    {
        // validate
        // cek status transaksi
        $checkUGDStatus = $this->checkUGDStatus($this->rjNoRef);
        if ($checkUGDStatus) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Pasien Sudah Pulang, Trasaksi Terkunci.");
            return;
        }

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

        $this->dataDaftarUgd['pemeriksaan']['uploadHasilPenunjang'][] = [
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
        $deleteHasilpenunjang = collect($this->dataDaftarUgd['pemeriksaan']['uploadHasilPenunjang'])->where("file", '!=', $file)->toArray();
        $this->dataDaftarUgd['pemeriksaan']['uploadHasilPenunjang'] = $deleteHasilpenunjang;
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
        $this->findData($this->rjNoRef);
    }



    // select data start////////////////
    public function render()
    {

        return view(
            'livewire.emr-u-g-d.mr-u-g-d.pemeriksaan.pemeriksaan',
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
