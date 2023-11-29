<?php

namespace App\Http\Livewire\EmrUGD\MrUGD\Anamnesa\Anamnesa;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

use Spatie\ArrayToXml\ArrayToXml;


class Screening extends Component
{
    use WithPagination;

    // listener from blade////////////////
    protected $listeners = [];



    //////////////////////////////
    // Ref on top bar
    //////////////////////////////



    // dataDaftarUgd RJ
    public $rjNoRef;

    public array $dataDaftarUgd = [];

    public array $screening =
    [
        "keluhanUtama" => "",
        "pernafasan" => "",
        "pernafasanOptions" => [
            ["pernafasan" => "Nafas Normal"],
            ["pernafasan" => "Tampak Sesak"],
        ],

        "kesadaran" => "",
        "kesadaranOptions" => [
            ["kesadaran" => "Sadar Penuh"],
            ["kesadaran" => "Tampak Mengantuk"],
            ["kesadaran" => "Gelisah"],
            ["kesadaran" => "Bicara Tidak Jelas"],

        ],

        "nyeriDada" => "",
        "nyeriDadaOptions" => [
            ["nyeriDada" => "Tidak Ada"],
            ["nyeriDada" => "Ada"],
        ],

        "nyeriDadaTingkat" => "",
        "nyeriDadaTingkatOptions" => [
            ["nyeriDadaTingkat" => "Ringan"],
            ["nyeriDadaTingkat" => "Sedang"],
            ["nyeriDadaTingkat" => "Berat"],

        ],

        "prioritasPelayanan" => "",
        "prioritasPelayananOptions" => [
            ["prioritasPelayanan" => "Preventif"],
            ["prioritasPelayanan" => "Paliatif"],
            ["prioritasPelayanan" => "Kuaratif"],
            ["prioritasPelayanan" => "Rehabilitatif"],


        ],
        "tanggalPelayanan" => "",
        "petugasPelayanan" => "",

    ];
    //////////////////////////////////////////////////////////////////////






    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////





    // resert input private////////////////
    private function resetInputFields(): void
    {

        // resert validation
        $this->resetValidation();
        // resert input kecuali
        $this->reset(['']);
    }





    // ////////////////
    // RJ Logic
    // ////////////////


    // validate Data RJ//////////////////////////////////////////////////
    private function validateDataAnamnesaUgd(): void
    {
        // customErrorMessages
        // $messages = customErrorMessagesTrait::messages();

        // require nik ketika pasien tidak dikenal



        // $rules = [];



        // Proses Validasi///////////////////////////////////////////
        // try {
        //     $this->validate($rules, $messages);
        // } catch (\Illuminate\Validation\ValidationException $e) {

        //     $this->emit('toastr-error', "Lakukan Pengecekan kembali Input Data.");
        //     $this->validate($rules, $messages);
        // }
    }


    // insert and update record start////////////////
    public function store()
    {
        // Validate RJ
        $this->validateDataAnamnesaUgd();

        // Logic update mode start //////////
        $this->updateDataUgd($this->dataDaftarUgd['rjNo']);
    }

    private function updateDataUgd($rjNo): void
    {

        // update table trnsaksi
        DB::table('rstxn_ugdhdrs')
            ->where('rj_no', $rjNo)
            ->update([
                'datadaftarugd_json' => json_encode($this->dataDaftarUgd, true),
                'datadaftarUgd_xml' => ArrayToXml::convert($this->dataDaftarUgd),
            ]);

        $this->emit('toastr-success', "Anamnesa berhasil disimpan.");
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

            // jika anamnesa tidak ditemukan tambah variable screening pda array
            if (isset($this->dataDaftarUgd['screening']) == false) {
                $this->dataDaftarUgd['screening'] = $this->screening;
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
                "regNo" => "" . $dataDaftarUgd->reg_no . "",

                "drId" => "" . $dataDaftarUgd->dr_id . "",
                "drDesc" => "" . $dataDaftarUgd->dr_name . "",

                "poliId" => "" . $dataDaftarUgd->poli_id . "",
                // "poliDesc" => "" . $dataDaftarUgd->poli_desc . "",

                // "kddrbpjs" => "" . $dataDaftarUgd->kd_dr_bpjs . "",
                // "kdpolibpjs" => "" . $dataDaftarUgd->kd_poli_bpjs . "",

                "rjDate" => "" . $dataDaftarUgd->rj_date . "",
                "rjNo" => "" . $dataDaftarUgd->rj_no . "",
                "shift" => "" . $dataDaftarUgd->shift . "",
                "noAntrian" => "" . $dataDaftarUgd->no_antrian . "",
                "noBooking" => "" . $dataDaftarUgd->nobooking . "",
                "slCodeFrom" => "02",
                "passStatus" => "",
                "rjStatus" => "" . $dataDaftarUgd->rj_status . "",
                "txnStatus" => "" . $dataDaftarUgd->txn_status . "",
                "ermStatus" => "" . $dataDaftarUgd->erm_status . "",
                "cekLab" => "0",
                "kunjunganInternalStatus" => "0",
                "noReferensi" => "" . $dataDaftarUgd->reg_no . "",
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
                    "taskId3" => "" . $dataDaftarUgd->rj_date . "",
                    "taskId4" => "",
                    "taskId5" => "",
                    "taskId6" => "",
                    "taskId7" => "",
                    "taskId99" => "",
                ],
                'sep' => [
                    "noSep" => "" . $dataDaftarUgd->vno_sep . "",
                    "reqSep" => [],
                    "resSep" => [],
                ]
            ];


            // jika anamnesa tidak ditemukan tambah variable screening pda array
            if (isset($this->dataDaftarUgd['screening']) == false) {
                $this->dataDaftarUgd['screening'] = $this->screening;
            }
        }
    }



    public function addRekonsiliasiObat()
    {
        if ($this->rekonsiliasiObat['namaObat']) {

            // check exist
            $cekRekonsiliasiObat = collect($this->dataDaftarUgd['anamnesa']['rekonsiliasiObat'])
                ->where("namaObat", '=', $this->rekonsiliasiObat['namaObat'])
                ->count();

            if (!$cekRekonsiliasiObat) {
                $this->dataDaftarUgd['anamnesa']['rekonsiliasiObat'][] = [
                    "namaObat" => $this->rekonsiliasiObat['namaObat'],
                    "dosis" => $this->rekonsiliasiObat['dosis'],
                    "rute" => $this->rekonsiliasiObat['rute']
                ];

                // reset rekonsiliasiObat
                $this->reset(['rekonsiliasiObat']);
            } else {
                $this->emit('toastr-error', "Nama Obat Sudah ada.");
            }
        } else {
            $this->emit('toastr-error', "Nama Obat Kosong.");
        }
    }

    public function removeRekonsiliasiObat($namaObat)
    {

        $rekonsiliasiObat = collect($this->dataDaftarUgd['anamnesa']['rekonsiliasiObat'])->where("namaObat", '!=', $namaObat)->toArray();
        $this->dataDaftarUgd['anamnesa']['rekonsiliasiObat'] = $rekonsiliasiObat;
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
            'livewire.emr-u-g-d.mr-u-g-d.anamnesa.anamnesa.screening',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Anamnesa',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Pasien Rawat Jalan',
            ]
        );
    }
    // select data end////////////////


}
