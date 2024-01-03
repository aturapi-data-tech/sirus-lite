<?php

namespace App\Http\Livewire\EmrUGD\MrUGD\Pemeriksaan;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

use Spatie\ArrayToXml\ArrayToXml;


class Pemeriksaan extends Component
{
    use WithPagination;

    // listener from blade////////////////
    protected $listeners = [];

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
    //////////////////////////////////////////////////////////////////////




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

    // /////////tingkatKesadaran////////////
    public function clicktingkatKesadaranlov()
    {
        $this->tingkatKesadaranLovStatus = true;
        $this->tingkatKesadaranLov = $this->dataDaftarUgd['pemeriksaan']['tandaVital']['tingkatKesadaranOptions'];
    }
    public function updatedtingkatKesadaranlovsearch()
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
    }
    // LOV selected end
    // /////////////////////




    // ////////////////
    // RJ Logic
    // ////////////////


    // validate Data RJ//////////////////////////////////////////////////
    private function validateDataRJ(): void
    {
        // customErrorMessages
        // $messages = customErrorMessagesTrait::messages();
        $messages = [];

        //Cek Usia Anak dibawah 13th tidak di cek tekanan darah
        $sql = "select birth_date from rsmst_pasiens where reg_no=:regNo";
        $birthDate = DB::scalar($sql, [
            "regNo" => $this->dataDaftarUgd['regNo'],
        ]);
        $cekUsia = Carbon::createFromFormat('Y-m-d H:i:s', $birthDate)->diff(Carbon::now())->format('%y');

        if ($cekUsia > 13) {
            $this->rules['dataDaftarUgd.pemeriksaan.tandaVital.sistolik'] = 'required|numeric';
            $this->rules['dataDaftarUgd.pemeriksaan.tandaVital.distolik'] = 'required|numeric';
        }

        // $rules = [];



        // Proses Validasi///////////////////////////////////////////
        try {
            $this->validate($this->rules, $messages);
        } catch (\Illuminate\Validation\ValidationException $e) {

            $this->emit('toastr-error', "Lakukan Pengecekan kembali Input Data.");
            $this->validate($this->rules, $messages);
        }
    }


    // insert and update record start////////////////
    public function store()
    {
        // set data RJno / NoBooking / NoAntrian / klaimId / kunjunganId
        $this->setDataPrimer();

        // Validate RJ
        $this->validateDataRJ();

        // Logic update mode start //////////
        $this->updateDataRJ($this->dataDaftarUgd['rjNo']);
    }

    private function updateDataRJ($rjNo): void
    {

        // update table trnsaksi
        DB::table('rstxn_ugdhdrs')
            ->where('rj_no', $rjNo)
            ->update([
                'datadaftarUgd_json' => json_encode($this->dataDaftarUgd, true),
                'datadaftarUgd_xml' => ArrayToXml::convert($this->dataDaftarUgd),
            ]);

        $this->emit('toastr-success', "Pemeriksaan berhasil disimpan.");
    }
    // insert and update record end////////////////


    private function findData($rjno): void
    {


        $findData = DB::table('rsview_ugdkasir')
            ->select('datadaftarugd_json', 'vno_sep')
            ->where('rj_no', $rjno)
            ->first();

        $datadaftarugd_json = isset($findData->datadaftarugd_json) ? $findData->datadaftarugd_json : null;
        // if meta_data_pasien_json = null
        // then cari Data Pasien By Key Collection (exception when no data found)
        // 
        // else json_decode
        if ($datadaftarugd_json) {
            $this->dataDaftarUgd = json_decode($findData->datadaftarugd_json, true);

            // jika pemeriksaan tidak ditemukan tambah variable pemeriksaan pda array
            if (isset($this->dataDaftarUgd['pemeriksaan']) == false) {
                $this->dataDaftarUgd['pemeriksaan'] = $this->pemeriksaan;
            }
        } else {

            $this->emit('toastr-error', "Data tidak dapat di proses json.");
            $dataDaftarUgd = DB::table('rsview_ugdkasir')
                ->select(
                    DB::raw("to_char(rj_date,'dd/mm/yyyy hh24:mi:ss') AS rj_date"),
                    DB::raw("to_char(rj_date,'yyyymmddhh24miss') AS rj_date1"),
                    'rj_no',
                    'reg_no',
                    'reg_name',
                    'sex',
                    'address',
                    'thn',
                    DB::raw("to_char(birth_date,'dd/mm/yyyy') AS birth_date"),
                    'poli_id',
                    // 'poli_desc',
                    'dr_id',
                    'dr_name',
                    'klaim_id',
                    'entry_id',
                    'shift',
                    'vno_sep',
                    'no_antrian',

                    'nobooking',
                    'push_antrian_bpjs_status',
                    'push_antrian_bpjs_json',
                    // 'kd_dr_bpjs',
                    // 'kd_poli_bpjs',
                    'rj_status',
                    'txn_status',
                    'erm_status',
                )
                ->where('rj_no', '=', $rjno)
                ->first();

            $this->dataDaftarUgd = [
                "regNo" =>  $dataDaftarUgd->reg_no,

                "drId" =>  $dataDaftarUgd->dr_id,
                "drDesc" =>  $dataDaftarUgd->dr_name,

                "poliId" =>  $dataDaftarUgd->poli_id,
                "klaimId" => $dataDaftarUgd->klaim_id,
                // "poliDesc" =>  $dataDaftarUgd->poli_desc ,

                // "kddrbpjs" =>  $dataDaftarUgd->kd_dr_bpjs ,
                // "kdpolibpjs" =>  $dataDaftarUgd->kd_poli_bpjs ,

                "rjDate" =>  $dataDaftarUgd->rj_date,
                "rjNo" =>  $dataDaftarUgd->rj_no,
                "shift" =>  $dataDaftarUgd->shift,
                "noAntrian" =>  $dataDaftarUgd->no_antrian,
                "noBooking" =>  $dataDaftarUgd->nobooking,
                "slCodeFrom" => "02",
                "passStatus" => "",
                "rjStatus" =>  $dataDaftarUgd->rj_status,
                "txnStatus" =>  $dataDaftarUgd->txn_status,
                "ermStatus" =>  $dataDaftarUgd->erm_status,
                "cekLab" => "0",
                "kunjunganInternalStatus" => "0",
                "noReferensi" =>  $dataDaftarUgd->reg_no,
                "postInap" => [],
                "internal12" => "1",
                "internal12Desc" => "Faskes Tingkat 1",
                "internal12Options" => [
                    [
                        "internal12" => "1",
                        "internal12Desc" => "Faskes Tingkat 1"
                    ],
                    [
                        "internal12" => "2",
                        "internal12Desc" => "Faskes Tingkat 2 RS"
                    ]
                ],
                "kontrol12" => "1",
                "kontrol12Desc" => "Faskes Tingkat 1",
                "kontrol12Options" => [
                    [
                        "kontrol12" => "1",
                        "kontrol12Desc" => "Faskes Tingkat 1"
                    ],
                    [
                        "kontrol12" => "2",
                        "kontrol12Desc" => "Faskes Tingkat 2 RS"
                    ],
                ],
                "taskIdPelayanan" => [
                    "taskId1" => "",
                    "taskId2" => "",
                    "taskId3" =>  $dataDaftarUgd->rj_date,
                    "taskId4" => "",
                    "taskId5" => "",
                    "taskId6" => "",
                    "taskId7" => "",
                    "taskId99" => "",
                ],
                'sep' => [
                    "noSep" =>  $dataDaftarUgd->vno_sep,
                    "reqSep" => [],
                    "resSep" => [],
                ]
            ];


            // jika pemeriksaan tidak ditemukan tambah variable pemeriksaan pda array
            if (isset($this->dataDaftarUgd['pemeriksaan']) == false) {
                $this->dataDaftarUgd['pemeriksaan'] = $this->pemeriksaan;
            }
        }
    }


    // set data RJno / NoBooking / NoAntrian / klaimId / kunjunganId
    private function setDataPrimer(): void
    {
    }

    private function scoringIMT(): void
    {
        $bb = (isset($this->dataDaftarUgd['pemeriksaan']['nutrisi']['bb'])
            ? ($this->dataDaftarUgd['pemeriksaan']['nutrisi']['bb']
                ? $this->dataDaftarUgd['pemeriksaan']['nutrisi']['bb']
                : 1)
            : 1);
        $tb = (isset($this->dataDaftarUgd['pemeriksaan']['nutrisi']['tb'])
            ? ($this->dataDaftarUgd['pemeriksaan']['nutrisi']['tb']
                ? $this->dataDaftarUgd['pemeriksaan']['nutrisi']['tb']
                : 1)
            : 1);;


        $this->dataDaftarUgd['pemeriksaan']['nutrisi']['imt'] = round($bb / (($tb / 100) * ($tb / 100)), 2);
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
