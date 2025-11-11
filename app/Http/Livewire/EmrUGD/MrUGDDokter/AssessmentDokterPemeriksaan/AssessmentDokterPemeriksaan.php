<?php

namespace App\Http\Livewire\EmrUGD\MrUGDDokter\AssessmentDokterPemeriksaan;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Traits\EmrUGD\EmrUGDTrait;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

class AssessmentDokterPemeriksaan extends Component
{
    use WithPagination, WithFileUploads, EmrUGDTrait;

    protected $listeners = ['emr:ugd:store' => 'store'];

    public $rjNoRef;
    public array $dataDaftarUgd = [];

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
            "jalanNafas" => [
                "jalanNafas" => "",
                "jalanNafasOptions" => [
                    ["jalanNafas" => "Paten"],
                    ["jalanNafas" => "Obstruksi Partial"],
                    ["jalanNafas" => "Obstruksi Total"],
                    ["jalanNafas" => "Stridor"],
                ]
            ],
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
            "sirkulasi" => [
                "sirkulasi" => "",
                "sirkulasiOptions" => [
                    ["sirkulasi" => "Normal"],
                    ["sirkulasi" => "Sianosis"],
                    ["sirkulasi" => "Berkeringat"],
                    ["sirkulasi" => "Jaundice"],
                    ["sirkulasi" => "Pucat"],
                ]
            ],
            "e" => "",
            "m" => "",
            "v" => "",
            "gcs" => "",
            "sistolik" => "",
            "distolik" => "",
            "frekuensiNafas" => "",
            "frekuensiNadi" => "",
            "suhu" => "",
            "spo2" => "",
            "gda" => "",
            "waktuPemeriksaan" => "",
        ],

        "nutrisi" => [
            "bb" => "",
            "tb" => "",
            "imt" => "",
            "lk" => "",
            "lila" => ""
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

        "pemeriksaanPenunjang" => [
            "lab" => [],
            "rad" => [],
        ],

        "uploadHasilPenunjang" => [],
    ];

    public $filePDF, $descPDF;
    public bool $isOpenRekamMedisuploadpenunjangHasil = false;

    // Modal Laboratorium
    public bool $isOpenLaboratorium = false;
    public array $isPemeriksaanLaboratorium = [];
    public array $isPemeriksaanLaboratoriumSelected = [];

    // Modal Radiologi
    public bool $isOpenRadiologi = false;
    public array $isPemeriksaanRadiologi = [];
    public array $isPemeriksaanRadiologiSelected = [];

    // LOV Kesadaran
    public array $tingkatKesadaranLov = [];
    public bool $tingkatKesadaranLovStatus = false;
    public string $tingkatKesadaranLovSearch = '';

    protected $rules = [
        'dataDaftarUgd.pemeriksaan.tandaVital.frekuensiNadi' => 'required|numeric|max:200',
        'dataDaftarUgd.pemeriksaan.tandaVital.frekuensiNafas' => 'required|numeric|max:60',
        'dataDaftarUgd.pemeriksaan.tandaVital.suhu' => 'required|numeric|max:42',
        'dataDaftarUgd.pemeriksaan.tandaVital.spo2' => 'nullable|numeric|max:100',
        'dataDaftarUgd.pemeriksaan.tandaVital.gda' => 'nullable|numeric|max:500',
        'dataDaftarUgd.pemeriksaan.nutrisi.bb' => 'required|numeric|max:300',
        'dataDaftarUgd.pemeriksaan.nutrisi.tb' => 'required|numeric|max:250',
        'dataDaftarUgd.pemeriksaan.nutrisi.imt' => 'required|numeric|max:100',
    ];

    protected $messages = [
        'required' => ':attribute wajib diisi.',
        'numeric' => ':attribute harus berupa angka.',
        'min' => ':attribute minimal :min.',
        'max' => ':attribute maksimal :max.',
    ];

    protected $validationAttributes = [
        'dataDaftarUgd.pemeriksaan.tandaVital.frekuensiNadi' => 'Frekuensi Nadi',
        'dataDaftarUgd.pemeriksaan.tandaVital.frekuensiNafas' => 'Frekuensi Nafas',
        'dataDaftarUgd.pemeriksaan.tandaVital.suhu' => 'Suhu',
        'dataDaftarUgd.pemeriksaan.tandaVital.spo2' => 'SpO2',
        'dataDaftarUgd.pemeriksaan.tandaVital.gda' => 'Gula Darah Acak',
        'dataDaftarUgd.pemeriksaan.nutrisi.bb' => 'Berat Badan',
        'dataDaftarUgd.pemeriksaan.nutrisi.tb' => 'Tinggi Badan',
        'dataDaftarUgd.pemeriksaan.nutrisi.imt' => 'IMT',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);

        if (
            str_starts_with($propertyName, 'dataDaftarUgd.pemeriksaan.tandaVital.') ||
            str_starts_with($propertyName, 'dataDaftarUgd.pemeriksaan.nutrisi.')
        ) {
            $this->calculateGCS();
            $this->scoringIMT();
        }
    }

    public function store(): void
    {
        $this->validate($this->rules, $this->messages, $this->validationAttributes);

        $rjNo = $this->dataDaftarUgd['rjNo'] ?? $this->rjNoRef ?? null;
        if (!$rjNo) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Nomor UGD kosong.');
            return;
        }


        $lockKey = "ugd:{$rjNo}";
        try {
            Cache::lock($lockKey, 5)->block(3, function () use ($rjNo) {
                DB::transaction(function () use ($rjNo) {
                    $fresh = $this->findDataUGD($rjNo) ?: [];

                    if (!isset($fresh['pemeriksaan']) || !is_array($fresh['pemeriksaan'])) {
                        $fresh['pemeriksaan'] = $this->pemeriksaan;
                    }

                    $fresh['pemeriksaan'] = array_merge(
                        $fresh['pemeriksaan'],
                        $this->dataDaftarUgd['pemeriksaan'] ?? []
                    );

                    $this->updateJsonUGD($rjNo, $fresh);
                    $this->dataDaftarUgd = $fresh;
                });
            });

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addSuccess('Pemeriksaan berhasil disimpan.');
        } catch (LockTimeoutException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Sistem sibuk, gagal memperoleh lock. Coba lagi.');
        } catch (\Exception $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Gagal menyimpan pemeriksaan: ' . $e->getMessage());
        }
    }

    // Laboratorium Methods
    public function pemeriksaanLaboratorium(): void
    {
        $this->isOpenLaboratorium = true;
        $this->renderisPemeriksaanLaboratorium();
    }

    public function closeModalLaboratorium(): void
    {
        $this->reset([
            'isOpenLaboratorium',
            'isPemeriksaanLaboratorium',
            'isPemeriksaanLaboratoriumSelected'
        ]);
    }

    private function renderisPemeriksaanLaboratorium(): void
    {
        if (empty($this->isPemeriksaanLaboratorium)) {
            $items = DB::table('lbmst_clabitems')
                ->select('clabitem_desc', 'clabitem_id', 'price', 'clabitem_group', 'item_code')
                ->whereNull('clabitem_group')
                ->whereNotNull('clabitem_desc')
                ->orderBy('clabitem_desc', 'asc')
                ->get();

            $this->isPemeriksaanLaboratorium = $items->map(function ($item) {
                return [
                    ...(array)$item,
                    'labStatus' => false
                ];
            })->toArray();
        }
    }

    public function toggleLaboratorium($index): void
    {
        $this->isPemeriksaanLaboratorium[$index]['labStatus'] =
            !$this->isPemeriksaanLaboratorium[$index]['labStatus'];

        $this->isPemeriksaanLaboratoriumSelected = collect($this->isPemeriksaanLaboratorium)
            ->where('labStatus', true)
            ->values()
            ->toArray();
    }

    public function kirimLaboratorium(): void
    {
        $rjNo = $this->dataDaftarUgd['rjNo'] ?? null;
        if (!$rjNo) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Nomor UGD kosong.');
            return;
        }

        if (!$this->checkUgdStatus($this->rjNoRef)) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Pasien sudah dipulangkan. Tidak bisa mengirim laboratorium.');
            return;
        }

        if (empty($this->isPemeriksaanLaboratoriumSelected)) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Pilih item laboratorium terlebih dahulu.');
            return;
        }

        $lockKey = "ugd:{$rjNo}";
        try {
            Cache::lock($lockKey, 5)->block(3, function () use ($rjNo) {
                DB::transaction(function () use ($rjNo) {
                    $checkupNo = (int) DB::table('lbtxn_checkuphdrs')
                        ->max(DB::raw('nvl(to_number(checkup_no),0)')) + 1;

                    DB::table('lbtxn_checkuphdrs')->insert([
                        'reg_no'         => $this->dataDaftarUgd['regNo'],
                        'dr_id'          => $this->dataDaftarUgd['drId'],
                        'checkup_date'   => DB::raw("to_date('" . now(config('app.timezone'))->format('d/m/Y H:i:s') . "','dd/mm/yyyy hh24:mi:ss')"),
                        'status_rjri'    => 'UGD',
                        'checkup_status' => 'P',
                        'ref_no'         => $rjNo,
                        'checkup_no'     => $checkupNo,
                    ]);

                    $nextDtl = (int) DB::table('lbtxn_checkupdtls')
                        ->max(DB::raw('nvl(to_number(checkup_dtl),0)'));

                    foreach ($this->isPemeriksaanLaboratoriumSelected as $lab) {
                        $nextDtl++;
                        DB::table('lbtxn_checkupdtls')->insert([
                            'clabitem_id'   => $lab['clabitem_id'],
                            'checkup_no'    => $checkupNo,
                            'checkup_dtl'   => $nextDtl,
                            'lab_item_code' => $lab['item_code'],
                            'price'         => $lab['price'],
                        ]);
                    }

                    $fresh = $this->findDataUGD($rjNo) ?: [];

                    if (!isset($fresh['pemeriksaan']) || !is_array($fresh['pemeriksaan'])) {
                        $fresh['pemeriksaan'] = $this->pemeriksaan;
                    }

                    $fresh['pemeriksaan'] = array_merge(
                        $fresh['pemeriksaan'],
                        $this->dataDaftarUgd['pemeriksaan'] ?? []
                    );

                    if (!isset($fresh['pemeriksaan']['pemeriksaanPenunjang']['lab'])) {
                        $fresh['pemeriksaan']['pemeriksaanPenunjang']['lab'] = [];
                    }


                    $fresh['pemeriksaan']['pemeriksaanPenunjang']['lab'][] = [
                        'labHdr' => [
                            'labHdrNo'   => $checkupNo,
                            'labHdrDate' => now(config('app.timezone'))->format('d/m/Y H:i:s'),
                            'labDtl'     => $this->isPemeriksaanLaboratoriumSelected,
                        ]
                    ];

                    $this->updateJsonUGD($rjNo, $fresh);
                    $this->dataDaftarUgd = $fresh;
                });
            });

            $this->closeModalLaboratorium();
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addSuccess('Permintaan laboratorium berhasil dikirim.');
        } catch (LockTimeoutException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Sistem sibuk, gagal memperoleh lock. Coba lagi.');
        }
    }

    // Radiologi Methods
    public function pemeriksaanRadiologi(): void
    {
        $this->isOpenRadiologi = true;
        $this->renderisPemeriksaanRadiologi();
    }

    public function closeModalRadiologi(): void
    {
        $this->reset([
            'isOpenRadiologi',
            'isPemeriksaanRadiologi',
            'isPemeriksaanRadiologiSelected'
        ]);
    }

    private function renderisPemeriksaanRadiologi(): void
    {
        if (empty($this->isPemeriksaanRadiologi)) {
            $items = DB::table('rsmst_radiologis')
                ->select('rad_desc', 'rad_price', 'rad_id')
                ->orderBy('rad_desc', 'asc')
                ->get();

            $this->isPemeriksaanRadiologi = $items->map(function ($item) {
                return [
                    ...(array)$item,
                    'radStatus' => false
                ];
            })->toArray();
        }
    }

    public function toggleRadiologi($index): void
    {
        $this->isPemeriksaanRadiologi[$index]['radStatus'] =
            !$this->isPemeriksaanRadiologi[$index]['radStatus'];

        $this->isPemeriksaanRadiologiSelected = collect($this->isPemeriksaanRadiologi)
            ->where('radStatus', true)
            ->values()
            ->toArray();
    }

    public function kirimRadiologi(): void
    {
        $rjNo = $this->dataDaftarUgd['rjNo'] ?? null;
        if (!$rjNo) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Nomor UGD kosong.');
            return;
        }

        if (!$this->checkUgdStatus($this->rjNoRef)) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Pasien sudah dipulangkan. Tidak bisa mengirim radiologi.');
            return;
        }

        if (empty($this->isPemeriksaanRadiologiSelected)) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Pilih item radiologi terlebih dahulu.');
            return;
        }

        $lockKey = "ugd:{$rjNo}";
        try {
            Cache::lock($lockKey, 5)->block(3, function () use ($rjNo) {
                DB::transaction(function () use ($rjNo) {
                    foreach ($this->isPemeriksaanRadiologiSelected as $rad) {
                        $maxDtl = DB::table('rstxn_ugdrads')->max(DB::raw('nvl(to_number(rad_dtl),0)'));
                        $nextDtl = ($maxDtl ?? 0) + 1;

                        DB::table('rstxn_ugdrads')->insert([
                            'rad_dtl'     => $nextDtl,
                            'rad_id'      => $rad['rad_id'],
                            'rj_no'       => $rjNo,
                            'rad_price'   => $rad['rad_price'],
                            'dr_radiologi' => 'dr. M.A. Budi Purwito, Sp.Rad.',
                            'waktu_entry' => DB::raw("to_date('" . now(config('app.timezone'))->format('d/m/Y H:i:s') . "','dd/mm/yyyy hh24:mi:ss')"),
                        ]);
                    }

                    $fresh = $this->findDataUGD($rjNo) ?: [];

                    if (!isset($fresh['pemeriksaan']) || !is_array($fresh['pemeriksaan'])) {
                        $fresh['pemeriksaan'] = $this->pemeriksaan;
                    }

                    $fresh['pemeriksaan'] = array_merge(
                        $fresh['pemeriksaan'],
                        $this->dataDaftarUgd['pemeriksaan'] ?? []
                    );

                    if (!isset($fresh['pemeriksaan']['pemeriksaanPenunjang']['rad'])) {
                        $fresh['pemeriksaan']['pemeriksaanPenunjang']['rad'] = [];
                    }

                    $fresh['pemeriksaan']['pemeriksaanPenunjang']['rad'][] = [
                        'radHdr' => [
                            'radHdrNo'   => $rjNo,
                            'radHdrDate' => now(config('app.timezone'))->format('d/m/Y H:i:s'),
                            'radDtl'     => $this->isPemeriksaanRadiologiSelected,
                        ]
                    ];

                    $this->updateJsonUGD($rjNo, $fresh);
                    $this->dataDaftarUgd = $fresh;
                });
            });

            $this->closeModalRadiologi();
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addSuccess('Permintaan radiologi berhasil dikirim.');
        } catch (LockTimeoutException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Sistem sibuk, gagal memperoleh lock. Coba lagi.');
        }
    }

    // LOV Kesadaran
    public function clickTingkatKesadaranLov(): void
    {
        $this->tingkatKesadaranLovStatus = true;
        $this->tingkatKesadaranLov = $this->dataDaftarUgd['pemeriksaan']['tandaVital']['tingkatKesadaranOptions'];
    }

    public function setTingkatKesadaran($value): void
    {
        $this->dataDaftarUgd['pemeriksaan']['tandaVital']['tingkatKesadaran'] = $value;
        $this->tingkatKesadaranLovStatus = false;
        $this->tingkatKesadaranLovSearch = '';
    }

    // Upload Hasil Penunjang
    public function uploadHasilPenunjang(): void
    {
        $this->validate([
            "filePDF" => "required|mimes:pdf|max:10240",
            "descPDF" => "required|max:255"
        ]);

        $rjNo = $this->dataDaftarUgd['rjNo'] ?? null;
        if (!$rjNo) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Nomor UGD kosong.');
            return;
        }

        $uploadedFile = $this->filePDF->store('uploadHasilPenunjang');

        $this->dataDaftarUgd['pemeriksaan']['uploadHasilPenunjang'][] = [
            'file' => $uploadedFile,
            'desc' => $this->descPDF,
            'tglUpload' => now(config('app.timezone'))->format('d/m/Y H:i:s'),
            'penanggungJawab' => [
                'userLog' => auth()->user()->myuser_name,
                'userLogDate' => now(config('app.timezone'))->format('d/m/Y H:i:s'),
                'userLogCode' => auth()->user()->myuser_code
            ]
        ];

        $this->reset(['filePDF', 'descPDF']);
        $this->store();
    }

    public function deleteHasilPenunjang($index): void
    {
        if (isset($this->dataDaftarUgd['pemeriksaan']['uploadHasilPenunjang'][$index])) {
            $file = $this->dataDaftarUgd['pemeriksaan']['uploadHasilPenunjang'][$index]['file'];
            Storage::delete($file);

            unset($this->dataDaftarUgd['pemeriksaan']['uploadHasilPenunjang'][$index]);
            $this->dataDaftarUgd['pemeriksaan']['uploadHasilPenunjang'] = array_values(
                $this->dataDaftarUgd['pemeriksaan']['uploadHasilPenunjang']
            );

            $this->store();
        }
    }

    // Helper Methods
    private function calculateGCS(): void
    {
        $e = (int)($this->dataDaftarUgd['pemeriksaan']['tandaVital']['e'] ?? 0);
        $m = (int)($this->dataDaftarUgd['pemeriksaan']['tandaVital']['m'] ?? 0);
        $v = (int)($this->dataDaftarUgd['pemeriksaan']['tandaVital']['v'] ?? 0);

        $this->dataDaftarUgd['pemeriksaan']['tandaVital']['gcs'] = $e + $m + $v;
    }

    private function scoringIMT(): void
    {
        $bb = (float)($this->dataDaftarUgd['pemeriksaan']['nutrisi']['bb'] ?? 0);
        $tb = (float)($this->dataDaftarUgd['pemeriksaan']['nutrisi']['tb'] ?? 0);

        if ($bb > 0 && $tb > 0) {
            $tbMeter = $tb / 100;
            $this->dataDaftarUgd['pemeriksaan']['nutrisi']['imt'] = round($bb / ($tbMeter * $tbMeter), 2);
        }
    }

    public function setWaktuPemeriksaan(): void
    {
        $this->dataDaftarUgd['pemeriksaan']['tandaVital']['waktuPemeriksaan'] =
            now(config('app.timezone'))->format('d/m/Y H:i:s');
        $this->store();
    }

    private function findData($rjno): void
    {
        $this->dataDaftarUgd = $this->findDataUGD($rjno) ?: [];

        if (!isset($this->dataDaftarUgd['pemeriksaan']) || !is_array($this->dataDaftarUgd['pemeriksaan'])) {
            $this->dataDaftarUgd['pemeriksaan'] = $this->pemeriksaan;
        }
    }

    public function resetForm(): void
    {
        $this->resetValidation();
        $this->dataDaftarUgd['pemeriksaan'] = $this->pemeriksaan;
    }

    public function mount(): void
    {
        $this->findData($this->rjNoRef);
    }

    // select data start////////////////
    public function render()
    {

        return view(
            'livewire.emr-u-g-d.mr-u-g-d-dokter.assessment-dokter-pemeriksaan.assessment-dokter-pemeriksaan',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Pemeriksaan',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Pasien Rawat Jalan',
            ]
        );
    }
    // select data end////////////////


}
