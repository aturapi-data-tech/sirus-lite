<?php

namespace App\Http\Livewire\EmrUGD\MrUGD\Perencanaan;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Traits\customErrorMessagesTrait;

use Carbon\Carbon;

use Illuminate\Support\Str;
use Spatie\ArrayToXml\ArrayToXml;


class Perencanaan extends Component
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
                ["tindakLanjut" => "Lain-lain"],
            ],

        ],

        "pengkajianMedisTab" => "Pengkajian Medis",
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

        'dataDaftarUgd.perencanaan.pengkajianMedis.waktuPemeriksaan' => 'required|date_format:d/m/Y H:i:s',
        'dataDaftarUgd.perencanaan.pengkajianMedis.selesaiPemeriksaan' => 'required|date_format:d/m/Y H:i:s'

    ];



    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////
    public function updated($propertyName)
    {
        // dd($propertyName);
        $this->validateOnly($propertyName);
    }




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


    // validate Data RJ//////////////////////////////////////////////////
    private function validateDataRJ(): void
    {
        // customErrorMessages
        // $messages = customErrorMessagesTrait::messages();
        $messages = [];


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
        $p_status = (isset($this->dataDaftarUgd['anamnesa']['pengkajianPerawatan']['tingkatKegawatan']) ?
            ($this->dataDaftarUgd['anamnesa']['pengkajianPerawatan']['tingkatKegawatan'] ?
                $this->dataDaftarUgd['anamnesa']['pengkajianPerawatan']['tingkatKegawatan']
                : 'P0')
            : 'P0');

        $waktu_pasien_datang = (isset($this->dataDaftarUgd['anamnesa']['pengkajianPerawatan']['jamDatang']) ?
            ($this->dataDaftarUgd['anamnesa']['pengkajianPerawatan']['jamDatang'] ?
                $this->dataDaftarUgd['anamnesa']['pengkajianPerawatan']['jamDatang']
                : Carbon::now()->format('d/m/Y H:i:s'))
            : Carbon::now()->format('d/m/Y H:i:s'));

        $waktu_pasien_dilayani = (isset($this->dataDaftarUgd['perencanaan']['pengkajianMedis']['waktuPemeriksaan']) ?
            ($this->dataDaftarUgd['perencanaan']['pengkajianMedis']['waktuPemeriksaan'] ?
                $this->dataDaftarUgd['perencanaan']['pengkajianMedis']['waktuPemeriksaan']
                : Carbon::now()->format('d/m/Y H:i:s'))
            : Carbon::now()->format('d/m/Y H:i:s'));

        // update table trnsaksi
        DB::table('rstxn_ugdhdrs')
            ->where('rj_no', $rjNo)
            ->update([
                'dataDaftarUgd_json' => json_encode($this->dataDaftarUgd, true),
                'dataDaftarUgd_xml' => ArrayToXml::convert($this->dataDaftarUgd),
                'p_status' => $p_status,
                'waktu_pasien_datang' => DB::raw("to_date('" . $waktu_pasien_datang . "','dd/mm/yyyy hh24:mi:ss')"),
                'waktu_pasien_dilayani' => DB::raw("to_date('" . $waktu_pasien_dilayani . "','dd/mm/yyyy hh24:mi:ss')"),
            ]);

        $this->emit('toastr-success', "Perencanaan berhasil disimpan.");
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

            // jika perencanaan tidak ditemukan tambah variable perencanaan pda array
            if (isset($this->dataDaftarUgd['perencanaan']) == false) {
                $this->dataDaftarUgd['perencanaan'] = $this->perencanaan;
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


            // jika perencanaan tidak ditemukan tambah variable perencanaan pda array
            if (isset($this->dataDaftarUgd['perencanaan']) == false) {
                $this->dataDaftarUgd['perencanaan'] = $this->perencanaan;
            }
        }
    }

    // set data RJno / NoBooking / NoAntrian / klaimId / kunjunganId
    private function setDataPrimer(): void
    {
    }

    public function setWaktuPemeriksaan($myTime)
    {
        $this->dataDaftarUgd['perencanaan']['pengkajianMedis']['waktuPemeriksaan'] = $myTime;
    }

    public function setSelesaiPemeriksaan($myTime)
    {
        $this->dataDaftarUgd['perencanaan']['pengkajianMedis']['selesaiPemeriksaan'] = $myTime;
    }

    public function setDrPemeriksa()
    {
        if (isset($this->dataDaftarUgd['perencanaan']['pengkajianMedis']['drPemeriksa'])) {
            if (!$this->dataDaftarUgd['perencanaan']['pengkajianMedis']['drPemeriksa']) {
                $this->dataDaftarUgd['perencanaan']['pengkajianMedis']['drPemeriksa'] = (isset($this->dataDaftarUgd['drDesc']) ?
                    ($this->dataDaftarUgd['drDesc'] ? $this->dataDaftarUgd['drDesc']
                        : 'Dokter pemeriksa')
                    : 'Dokter pemeriksa-');
            }
        } else {

            $this->dataDaftarUgd['perencanaan']['pengkajianMedisTab'] = 'Pengkajian Medis';
            $this->dataDaftarUgd['perencanaan']['pengkajianMedis']['drPemeriksa'] = (isset($this->dataDaftarUgd['drDesc']) ?
                ($this->dataDaftarUgd['drDesc'] ? $this->dataDaftarUgd['drDesc']
                    : 'Dokter pemeriksa')
                : 'Dokter pemeriksa-');
        }
    }

    // when new form instance
    public function mount()
    {
        $this->findData($this->rjNoRef);

        // set dokter pemeriksa
        $this->setDrPemeriksa();
    }



    // select data start////////////////
    public function render()
    {

        return view(
            'livewire.emr-u-g-d.mr-u-g-d.perencanaan.perencanaan',
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
