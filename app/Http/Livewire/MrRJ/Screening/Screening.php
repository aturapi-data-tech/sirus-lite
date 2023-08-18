<?php

namespace App\Http\Livewire\MrRJ\Screening;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

use App\Http\Traits\customErrorMessagesTrait;
use App\Http\Traits\BPJS\AntrianTrait;
use App\Http\Traits\BPJS\VclaimTrait;


use Illuminate\Support\Str;
use Spatie\ArrayToXml\ArrayToXml;


class Screening extends Component
{
    use WithPagination;


    //////////////////////////////
    // Ref on top bar
    //////////////////////////////
    public $rjNoRef = '430269';



    // dataDaftarPoliRJ RJ
    public $dataDaftarPoliRJ = [];

    // data SKDP / kontrol=>[] 
    public $screeningQuestions = [
        [
            "sc_seq" => "1",
            "sc_desc" => "Apakah Pasien Sadar?",
            "active_status" => "1",
            "sc_score" => 1,
            "sc_value" => 1,
            "sc_option" =>
            [
                [
                    "option_label" => "Sadar Penuh",
                    "option_value" => 1,
                    "option_score" => 1
                ],
                [
                    "option_label" => "Tampak (Mengantuk / Gelisah / Bicara Tida Jelas)",
                    "option_value" => 2,
                    "option_score" => 2
                ],
                [
                    "option_label" => "Tidak Sadar",
                    "option_value" => 3,
                    "option_score" => 3
                ]
            ],


        ],
        [
            "sc_seq" => "2",
            "sc_desc" => "Apakah Pasien Merasa Sesak Nafas?",
            "active_status" => "1",
            "sc_score" => 1,
            "sc_value" => 1,
            "sc_option" =>
            [
                [
                    "option_label" => "Nafas Normal",
                    "option_value" => 1,
                    "option_score" => 1
                ],
                [
                    "option_label" => "Tampak (Sesak)",
                    "option_value" => 2,
                    "option_score" => 2
                ],
                [
                    "option_label" => "Tidak Bernafas",
                    "option_value" => 3,
                    "option_score" => 3
                ]
            ],


        ],
        [
            "sc_seq" => "3",
            "sc_desc" => "Apakah Pasien Beresiko Jatuh?",
            "active_status" => "1",
            "sc_score" => 1,
            "sc_value" => 1,
            "sc_option" =>
            [
                [
                    "option_label" => "Resiko Rendah",
                    "option_value" => 1,
                    "option_score" => 1
                ],
                [
                    "option_label" => "Resiko (Sedang)",
                    "option_value" => 2,
                    "option_score" => 2
                ],
                [
                    "option_label" => "Resiko Tinggi",
                    "option_value" => 3,
                    "option_score" => 3
                ]
            ],


        ],
        [
            "sc_seq" => "4",
            "sc_desc" => "Apakah Pasien Nyeri Pada Dada?",
            "active_status" => "1",
            "sc_score" => 1,
            "sc_value" => 1,
            "sc_option" =>
            [
                [
                    "option_label" => "Tidak Ada",
                    "option_value" => 1,
                    "option_score" => 1
                ],
                [
                    "option_label" => "Ada (Tingkat Rendah)",
                    "option_value" => 2,
                    "option_score" => 2
                ],
                [
                    "option_label" => "Nyeri Dada Kiri Tembis Punggung",
                    "option_value" => 3,
                    "option_score" => 3
                ]
            ],


        ],
        [
            "sc_seq" => "5",
            "sc_desc" => "Apakah Pasien Batuk?",
            "active_status" => "1",
            "sc_score" => 1,
            "sc_value" => 1,
            "sc_option" =>
            [
                [
                    "option_label" => "Tidak Ada",
                    "option_value" => 1,
                    "option_score" => 1
                ],
                [
                    "option_label" => "Batuk < 2 Minggu",
                    "option_value" => 2,
                    "option_score" => 2
                ],
                [
                    "option_label" => "Batuk > 2 Minggu",
                    "option_value" => 3,
                    "option_score" => 3
                ]
            ],


        ],
        [
            "sc_seq" => "2",
            "sc_desc" => "Tingkat Nyeri Pasien",
            "active_status" => "1",
            "sc_score" => 0,
            "sc_value" => 0,
            "sc_image" => "pain_scale_level.jpg",
            "sc_option" =>
            [
                [
                    "option_label" => "0 (Tidak Nyeri)",
                    "option_value" => 0,
                    "option_score" => 0
                ],
                [
                    "option_label" => "1-3 (Sedikit Nyeri)",
                    "option_value" => 1,
                    "option_score" => 1
                ],
                [
                    "option_label" => "4-7 (Nyeri)",
                    "option_value" => 2,
                    "option_score" => 2
                ],
                [
                    "option_label" => "8-10 (Sangat Nyeri)",
                    "option_value" => 3,
                    "option_score" => 3
                ],

            ],


        ],
    ];

    public $screeningKesimpulan = [
        "sck_value" => 1,
        "sck_score" => 1,
        "sck_option" =>
        [
            [
                "option_id" => "1",
                "option_label" => "SESUAI ANTRIAN",
                "option_value" => 1,
                "option_score" => 1,
            ],
            [
                "option_id" => "2",
                "option_label" => "DISEGERAKAN",
                "option_value" => 2,
                "option_score" => 2,
            ],
            [
                "option_id" => "3",
                "option_label" => "IGD",
                "option_value" => 3,
                "option_score" => 3,
            ],
        ]
    ];
    public $collectDataScreening = [];
    //////////////////////////////////////////////////////////////////////
    public $dataTandaVital = [
        [
            "tv_seq" => "1",
            "tv_label" => "Tekanan Darah",
            "tv_mou" => "mmHg",
            "tv_value" => 0
        ],
        [
            "tv_seq" => "2",
            "tv_label" => "Frekuensi Nadi",
            "tv_mou" => "x/menit",
            "tv_value" => 0
        ],
        [
            "tv_seq" => "3",
            "tv_label" => "Suhu",
            "tv_mou" => "C",
            "tv_value" => 0
        ],
        [
            "tv_seq" => "4",
            "tv_label" => "Frekuansi Nafas",
            "tv_mou" => "x/menit",
            "tv_value" => 0
        ],
        [
            "tv_seq" => "4",
            "tv_label" => "Saturasi Oksigen",
            "tv_mou" => "%",
            "tv_value" => 0
        ],
        [
            "tv_seq" => "4",
            "tv_label" => "GDA",
            "tv_mou" => "mg/dl",
            "tv_value" => 0
        ],
        [
            "tv_seq" => "5",
            "tv_label" => "Skor Nyeri",
            "tv_mou" => "",
            "tv_value" => 0
        ],
        [
            "tv_seq" => "6",
            "tv_label" => "Skor Jatuh",
            "tv_mou" => "",
            "tv_value" => 0
        ],
    ];
    public $dataAntropometri = [
        [
            "ap_seq" => "1",
            "ap_label" => "Berat Badan",
            "ap_mou" => "Kg",
            "ap_value" => 0
        ],
        [
            "ap_seq" => "2",
            "ap_label" => "Tinggi Badan",
            "ap_mou" => "Cm",
            "ap_value" => 0
        ],
        [
            "ap_seq" => "3",
            "ap_label" => "Lingkar Kepala",
            "ap_mou" => "Cm",
            "ap_value" => 0
        ],
        [
            "ap_seq" => "4",
            "ap_label" => "IMT",
            "ap_mou" => "",
            "ap_value" => 0
        ],
        [
            "ap_seq" => "5",
            "ap_label" => "Lingkar Lengan Atas",
            "ap_mou" => "Cm",
            "ap_value" => 0
        ],
    ];
    public $dataFungsional = [
        [
            "fu_seq" => "1",
            "fu_label" => "Alat Bantu",
            "fu_mou" => "",
            "fu_value" => "-"
        ],
        [
            "fu_seq" => "2",
            "fu_label" => "Prothesa",
            "fu_mou" => "",
            "fu_value" => "-"
        ],
        [
            "fu_seq" => "3",
            "fu_label" => "Cacat Tubuh",
            "fu_mou" => "",
            "fu_value" => "-"
        ],
        [
            "fu_seq" => "4",
            "fu_label" => "ADL",
            "fu_mou" => "",
            "fu_value" => "-"
        ],
        [
            "fu_seq" => "5",
            "fu_label" => "Riwayat",
            "fu_mou" => "",
            "fu_value" => "-"
        ],
    ];
    //////////////////////////////////////////////////////////////////////




    // listener from blade////////////////
    protected $listeners = [
        'xxxxx' => 'xxxxx',
    ];



    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////





    // resert input private////////////////
    private function resetInputFields(): void
    {

        // resert validation
        $this->resetValidation();
        // resert input kecuali
        $this->resetExcept([
            'rjNoRef'
        ]);
    }





    // ////////////////
    // RJ Logic
    // ////////////////

    //////////////////////////////////////////////
    // updated when change Record ////////////////
    ///////////////////////////////////////////////



    //////////////////////////////////////////////
    // updated when change Record ////////////////
    ///////////////////////////////////////////////




    // validate Data RJ//////////////////////////////////////////////////
    private function validateDataRJ(): void
    {
        // customErrorMessages
        $messages = customErrorMessagesTrait::messages();

        // require nik ketika pasien tidak dikenal



        $rules = [

            "dataDaftarPoliRJ.kontrol.noKontrolRS" => "required",

            "dataDaftarPoliRJ.kontrol.noSKDPBPJS" => "",
            "dataDaftarPoliRJ.kontrol.noAntrian" => "",

            "dataDaftarPoliRJ.kontrol.tglKontrol" => "bail|required|date_format:d/m/Y",

            "dataDaftarPoliRJ.kontrol.drKontrol" => "required",
            "dataDaftarPoliRJ.kontrol.drKontrolDesc" => "required",
            "dataDaftarPoliRJ.kontrol.drKontrolBPJS" => "",


            "dataDaftarPoliRJ.kontrol.poliKontrol" => "required",
            "dataDaftarPoliRJ.kontrol.poliKontrolDesc" => "required",
            "dataDaftarPoliRJ.kontrol.poliKontrolBPJS" => "",

            "dataDaftarPoliRJ.kontrol.catatan" => "",

        ];



        // Proses Validasi///////////////////////////////////////////
        try {
            $this->validate($rules, $messages);
        } catch (\Illuminate\Validation\ValidationException $e) {

            $this->emit('toastr-error', "Lakukan Pengecekan kembali Input Data.");
            $this->validate($rules, $messages);
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
        $this->updateDataRJ($this->dataDaftarPoliRJ['rjNo']);
    }

    private function updateDataRJ($rjNo): void
    {

        // update table trnsaksi
        DB::table('rstxn_rjhdrs')
            ->where('rj_no', $rjNo)
            ->update([
                'datadaftarpolirj_json' => json_encode($this->dataDaftarPoliRJ, true),
                'datadaftarpolirj_xml' => ArrayToXml::convert($this->dataDaftarPoliRJ),
            ]);

        $this->emit('toastr-success', "Surat Kontrol berhasil disimpan.");
    }
    // insert and update record end////////////////


    private function findData($rjno): void
    {


        $findData = DB::table('rsview_rjkasir')
            ->select('datadaftarpolirj_json', 'vno_sep')
            ->where('rj_no', $rjno)
            ->first();


        if ($findData->datadaftarpolirj_json) {
            $this->dataDaftarPoliRJ = json_decode($findData->datadaftarpolirj_json, true);

            // jika kontrol tidak ditemukan tambah variable kontrol pda array
            if (isset($this->dataDaftarPoliRJ['screening']) == false) {
                $this->dataDaftarPoliRJ['screening'] = $this->screeningQuestions;
            }


            // $this->dataDaftarPoliRJ['screening']['tglKontrol'] = $this->dataDaftarPoliRJ['screening']['tglKontrol']
            //     ? $this->dataDaftarPoliRJ['screening']['tglKontrol']
            //     : Carbon::now()->addDays(8)->format('d/m/Y');
            // $this->dataDaftarPoliRJ['screening']['drKontrol'] = $this->dataDaftarPoliRJ['screening']['drKontrol']
            //     ? $this->dataDaftarPoliRJ['screening']['drKontrol']
            //     : $this->dataDaftarPoliRJ['drId'];
            // $this->dataDaftarPoliRJ['screening']['drKontrolDesc'] = $this->dataDaftarPoliRJ['screening']['drKontrolDesc']
            //     ? $this->dataDaftarPoliRJ['screening']['drKontrolDesc']
            //     : $this->dataDaftarPoliRJ['drDesc'];
            // $this->dataDaftarPoliRJ['screening']['drKontrolBPJS'] =  $this->dataDaftarPoliRJ['screening']['drKontrolBPJS']
            //     ? $this->dataDaftarPoliRJ['screening']['drKontrolBPJS']
            //     : $this->dataDaftarPoliRJ['kddrbpjs'];
            // $this->dataDaftarPoliRJ['screening']['poliKontrol'] = $this->dataDaftarPoliRJ['screening']['poliKontrol']
            //     ? $this->dataDaftarPoliRJ['screening']['poliKontrol']
            //     : $this->dataDaftarPoliRJ['poliId'];
            // $this->dataDaftarPoliRJ['screening']['poliKontrolDesc'] = $this->dataDaftarPoliRJ['screening']['poliKontrolDesc']
            //     ? $this->dataDaftarPoliRJ['screening']['poliKontrolDesc']
            //     : $this->dataDaftarPoliRJ['poliDesc'];
            // $this->dataDaftarPoliRJ['screening']['poliKontrolBPJS'] = $this->dataDaftarPoliRJ['screening']['poliKontrolBPJS']
            //     ? $this->dataDaftarPoliRJ['screening']['poliKontrolBPJS']
            //     : $this->dataDaftarPoliRJ['kdpolibpjs'];
            // $this->dataDaftarPoliRJ['screening']['noSEP'] = $this->dataDaftarPoliRJ['screening']['noSEP']
            //     ? $this->dataDaftarPoliRJ['screening']['noSEP']
            //     : $findData->vno_sep;
        } else {

            $this->emit('toastr-error', "Json Tidak ditemukan, Data sedang diproses ulang.");
            $dataDaftarPoliRJ = DB::table('rsview_rjkasir')
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
                    'poli_desc',
                    'dr_id',
                    'dr_name',
                    'klaim_id',
                    'shift',
                    'vno_sep',
                    'no_antrian',

                    'nobooking',
                    'push_antrian_bpjs_status',
                    'push_antrian_bpjs_json',
                    'kd_dr_bpjs',
                    'kd_poli_bpjs',
                    'rj_status',
                    'txn_status',
                    'erm_status',
                )
                ->where('rj_no', '=', $rjno)
                ->first();

            $this->dataDaftarPoliRJ = [
                "regNo" => "" . $dataDaftarPoliRJ->reg_no . "",

                "drId" => "" . $dataDaftarPoliRJ->dr_id . "",
                "drDesc" => "" . $dataDaftarPoliRJ->dr_name . "",

                "poliId" => "" . $dataDaftarPoliRJ->poli_id . "",
                "poliDesc" => "" . $dataDaftarPoliRJ->poli_desc . "",

                "kddrbpjs" => "" . $dataDaftarPoliRJ->kd_dr_bpjs . "",
                "kdpolibpjs" => "" . $dataDaftarPoliRJ->kd_poli_bpjs . "",

                "rjDate" => "" . $dataDaftarPoliRJ->rj_date . "",
                "rjNo" => "" . $dataDaftarPoliRJ->rj_no . "",
                "shift" => "" . $dataDaftarPoliRJ->shift . "",
                "noAntrian" => "" . $dataDaftarPoliRJ->no_antrian . "",
                "noBooking" => "" . $dataDaftarPoliRJ->nobooking . "",
                "slCodeFrom" => "02",
                "passStatus" => "",
                "rjStatus" => "" . $dataDaftarPoliRJ->rj_status . "",
                "txnStatus" => "" . $dataDaftarPoliRJ->txn_status . "",
                "ermStatus" => "" . $dataDaftarPoliRJ->erm_status . "",
                "cekLab" => "0",
                "kunjunganInternalStatus" => "0",
                "noReferensi" => "" . $dataDaftarPoliRJ->reg_no . "",
                "taskIdPelayanan" => [
                    "taskId1" => "",
                    "taskId2" => "",
                    "taskId3" => "" . $dataDaftarPoliRJ->rj_date . "",
                    "taskId4" => "",
                    "taskId5" => "",
                    "taskId6" => "",
                    "taskId7" => "",
                    "taskId99" => "",
                ],
                'sep' => [
                    "noSep" => "" . $dataDaftarPoliRJ->vno_sep . "",
                    "reqSep" => [],
                    "resSep" => [],
                ]
            ];

            // jika kontrol tidak ditemukan tambah variable kontrol pda array
            if (isset($this->dataDaftarPoliRJ['screening']) == false) {
                $this->dataDaftarPoliRJ['screening'] = $this->screeningQuestions;
            }



            // setDataKontrol
            // $this->dataDaftarPoliRJ['screening']['tglKontrol'] = $this->dataDaftarPoliRJ['screening']['tglKontrol']
            //     ? $this->dataDaftarPoliRJ['screening']['tglKontrol']
            //     : Carbon::now()->addDays(8)->format('d/m/Y');
            // $this->dataDaftarPoliRJ['screening']['drKontrol'] = $this->dataDaftarPoliRJ['screening']['drKontrol']
            //     ? $this->dataDaftarPoliRJ['screening']['drKontrol']
            //     : $dataDaftarPoliRJ->dr_id;
            // $this->dataDaftarPoliRJ['screening']['drKontrolDesc'] = $this->dataDaftarPoliRJ['screening']['drKontrolDesc']
            //     ? $this->dataDaftarPoliRJ['screening']['drKontrolDesc']
            //     : $dataDaftarPoliRJ->dr_name;
            // $this->dataDaftarPoliRJ['screening']['drKontrolBPJS'] =  $this->dataDaftarPoliRJ['screening']['drKontrolBPJS']
            //     ? $this->dataDaftarPoliRJ['screening']['drKontrolBPJS']
            //     : $dataDaftarPoliRJ->kd_dr_bpjs;
            // $this->dataDaftarPoliRJ['screening']['poliKontrol'] = $this->dataDaftarPoliRJ['screening']['poliKontrol']
            //     ? $this->dataDaftarPoliRJ['screening']['poliKontrol']
            //     : $dataDaftarPoliRJ->poli_id;
            // $this->dataDaftarPoliRJ['screening']['poliKontrolDesc'] = $this->dataDaftarPoliRJ['screening']['poliKontrolDesc']
            //     ? $this->dataDaftarPoliRJ['screening']['poliKontrolDesc']
            //     : $dataDaftarPoliRJ->poli_desc;
            // $this->dataDaftarPoliRJ['screening']['poliKontrolBPJS'] = $this->dataDaftarPoliRJ['screening']['poliKontrolBPJS']
            //     ? $this->dataDaftarPoliRJ['screening']['poliKontrolBPJS']
            //     : $dataDaftarPoliRJ->kd_poli_bpjs;
            // $this->dataDaftarPoliRJ['screening']['noSEP'] = $this->dataDaftarPoliRJ['screening']['noSEP']
            //     ? $this->dataDaftarPoliRJ['screening']['noSEP']
            //     : $dataDaftarPoliRJ->vno_sep;
        }
    }

    // set data RJno / NoBooking / NoAntrian / klaimId / kunjunganId
    private function setDataPrimer(): void
    {
        // $noKontrol = Carbon::now()->addDays(8)->format('dmY') . $this->dataDaftarPoliRJ['screening']['drKontrol'] . $this->dataDaftarPoliRJ['screening']['poliKontrol'];
        // $this->dataDaftarPoliRJ['screening']['noKontrolRS'] =  $this->dataDaftarPoliRJ['screening']['noKontrolRS'] ? $this->dataDaftarPoliRJ['screening']['noKontrolRS'] : $noKontrol;
    }

    // /prosesDataScreening/////////////
    public function prosesDataScreening($label, $value, $score, $key)
    {

        $collection = collect($this->dataDaftarPoliRJ['screening']);
        // update collectDataScreening
        if (collect($collection)->where('sc_desc', $label)->contains('sc_desc', $label)) {

            $this->dataDaftarPoliRJ['screening'][$key]['sc_value'] = $value;
            $this->dataDaftarPoliRJ['screening'][$key]['sc_score'] = $score;
        }


        // update kesimpulan dari screening otomatis
        // $KesimpulanDariScreening = $this->updateDataKesimpulanDariScreening() == 1 ? 'Sesuai Antrian' : ($this->updateDataKesimpulanDariScreening() == 2 ? 'Disegerakan' : 'IGD');

        // //////////////////////////////////////


        // $this->emit('toastr-success', "$label Kesimpulan : $KesimpulanDariScreening");
    }
    // /prosesDataScreening/////////////




    // ////////////////
    // Antrol Logic
    // ////////////////


    private function pushSuratKontrolBPJS(): void
    {


        //push data SuratKontrolBPJS
        if ($this->dataDaftarPoliRJ['klaimId'] = 'JM') {


            // jika SKDP kosong lakukan push data
            // insert
            if (!$this->dataDaftarPoliRJ['kontrol']['noSKDPBPJS']) {
                $HttpGetBpjs =  VclaimTrait::suratkontrol_insert($this->dataDaftarPoliRJ['kontrol'])->getOriginalContent();

                // 2 cek proses pada getHttp
                if ($HttpGetBpjs['metadata']['code'] == 200) {
                    $this->emit('toastr-success', 'KONTROL ' .  $HttpGetBpjs['metadata']['code'] . ' ' . $HttpGetBpjs['metadata']['message']);
                    $this->dataDaftarPoliRJ['kontrol']['noSKDPBPJS'] = $HttpGetBpjs['metadata']['response']['noSuratKontrol']; //status 200 201 400 ..

                    $this->emit('toastr-success', 'KONTROL ' .  $HttpGetBpjs['metadata']['code'] . ' ' . $HttpGetBpjs['metadata']['message']);
                } else {
                    $this->emit('toastr-error', 'KONTROL ' . $HttpGetBpjs['metadata']['code'] . ' ' . $HttpGetBpjs['metadata']['message']);
                }
            } else {
                // update
                $HttpGetBpjs =  VclaimTrait::suratkontrol_update($this->dataDaftarPoliRJ['kontrol'])->getOriginalContent();

                if ($HttpGetBpjs['metadata']['code'] == 200) {
                    $this->emit('toastr-success', 'UPDATEKONTROL ' .  $HttpGetBpjs['metadata']['code'] . ' ' . $HttpGetBpjs['metadata']['message']);
                    $this->dataDaftarPoliRJ['kontrol']['noSKDPBPJS'] = $HttpGetBpjs['metadata']['response']['noSuratKontrol']; //status 200 201 400 ..

                    $this->emit('toastr-success', 'UPDATEKONTROL ' .  $HttpGetBpjs['metadata']['code'] . ' ' . $HttpGetBpjs['metadata']['message']);
                } else {
                    $this->emit('toastr-error', 'UPDATEKONTROL ' . $HttpGetBpjs['metadata']['code'] . ' ' . $HttpGetBpjs['metadata']['message']);
                }
            }
        }
    }








    // when new form instance
    public function mount()
    {
        $this->findData($this->rjNoRef);
        // set data dokter ref
        // $this->store();
    }



    // select data start////////////////
    public function render()
    {

        return view(
            'livewire.mr-r-j.screening.screening',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Data Screening Pasien Rawat Jalan',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Pasien Rawat Jalan',
            ]
        );
    }
    // select data end////////////////


}
