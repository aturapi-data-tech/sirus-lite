<?php

namespace App\Http\Livewire\EmrRJ\EresepRJ;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
// use Carbon\Carbon;

use App\Http\Traits\customErrorMessagesTrait;

// use Illuminate\Support\Str;
use Spatie\ArrayToXml\ArrayToXml;
use Exception;


class EresepRJRacikan extends Component
{
    use WithPagination;


    // listener from blade////////////////
    protected $listeners = [
        'storeAssessmentDokterRJ' => 'store',
        'syncronizeAssessmentDokterRJFindData' => 'mount'
    ];


    //////////////////////////////
    // Ref on top bar
    //////////////////////////////
    public $rjNoRef = 472309;



    // dataDaftarPoliRJ RJ
    public array $dataDaftarPoliRJ = [];

    //////////////////////////////////////////////////////////////////////


    //  table LOV////////////////



    public array $dataProductLov = [];
    public int $dataProductLovStatus = 0;
    public $dataProductLovSearch = '';
    public int $selecteddataProductLovIndex = 0;

    public array $collectingMyProduct = [];

    public string $noRacikan = 'R1';







    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////
    public function updated($propertyName)
    {
        // dd($propertyName);
        // $this->validateOnly($propertyName);
    }












    // insert and update record start////////////////
    public function store()
    {
        // set data RJno / NoBooking / NoAntrian / klaimId / kunjunganId
        $this->setDataPrimer();

        // Logic update mode start //////////
        $this->updateDataRJ($this->dataDaftarPoliRJ['rjNo']);
        $this->emit('syncronizeAssessmentDokterRJFindData');
    }

    private function updateDataRJ($rjNo): void
    {

        // update table trnsaksi
        DB::table('rstxn_rjhdrs')
            ->where('rj_no', $rjNo)
            ->update([
                'dataDaftarPoliRJ_json' => json_encode($this->dataDaftarPoliRJ, true),
                'dataDaftarPoliRJ_xml' => ArrayToXml::convert($this->dataDaftarPoliRJ),
            ]);

        $this->emit('toastr-success', "Eresep Racikan berhasil disimpan.");
    }
    // insert and update record end////////////////


    private function findData($rjno): void
    {
        $findData = DB::table('rsview_rjkasir')
            ->select('datadaftarpolirj_json', 'vno_sep')
            ->where('rj_no', $rjno)
            ->first();

        $dataDaftarPoliRJ_json = isset($findData->datadaftarpolirj_json) ? $findData->datadaftarpolirj_json   : null;
        // if meta_data_pasien_json = null
        // then cari Data Pasien By Key Collection (exception when no data found)
        // 
        // else json_decode
        if ($dataDaftarPoliRJ_json) {
            $this->dataDaftarPoliRJ = json_decode($findData->datadaftarpolirj_json, true);

            // jika eresep tidak ditemukan tambah variable eresep pda array
            if (isset($this->dataDaftarPoliRJ['eresep']) == false) {
                $this->dataDaftarPoliRJ['eresep'] = [];
            }
        } else {

            $this->emit('toastr-error', "Data tidak dapat di proses json.");
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
                    // 'entry_id',
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
                "regNo" =>  $dataDaftarPoliRJ->reg_no,

                "drId" =>  $dataDaftarPoliRJ->dr_id,
                "drDesc" =>  $dataDaftarPoliRJ->dr_name,

                "poliId" =>  $dataDaftarPoliRJ->poli_id,
                "klaimId" => $dataDaftarPoliRJ->klaim_id,
                // "poliDesc" =>  $dataDaftarPoliRJ->poli_desc ,

                // "kddrbpjs" =>  $dataDaftarPoliRJ->kd_dr_bpjs ,
                // "kdpolibpjs" =>  $dataDaftarPoliRJ->kd_poli_bpjs ,

                "rjDate" =>  $dataDaftarPoliRJ->rj_date,
                "rjNo" =>  $dataDaftarPoliRJ->rj_no,
                "shift" =>  $dataDaftarPoliRJ->shift,
                "noAntrian" =>  $dataDaftarPoliRJ->no_antrian,
                "noBooking" =>  $dataDaftarPoliRJ->nobooking,
                "slCodeFrom" => "02",
                "passStatus" => "",
                "rjStatus" =>  $dataDaftarPoliRJ->rj_status,
                "txnStatus" =>  $dataDaftarPoliRJ->txn_status,
                "ermStatus" =>  $dataDaftarPoliRJ->erm_status,
                "cekLab" => "0",
                "kunjunganInternalStatus" => "0",
                "noReferensi" =>  $dataDaftarPoliRJ->reg_no,
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
                    "taskId3" =>  $dataDaftarPoliRJ->rj_date,
                    "taskId4" => "",
                    "taskId5" => "",
                    "taskId6" => "",
                    "taskId7" => "",
                    "taskId99" => "",
                ],
                'sep' => [
                    "noSep" =>  $dataDaftarPoliRJ->vno_sep,
                    "reqSep" => [],
                    "resSep" => [],
                ]
            ];


            // jika eresep tidak ditemukan tambah variable eresep pda array
            if (isset($this->dataDaftarPoliRJ['eresep']) == false) {
                $this->dataDaftarPoliRJ['eresep'] = [];
            }
        }
    }


    private function setDataPrimer(): void
    {
    }




    public function insertProduct(): void
    {

        // validate
        $this->checkRjStatus();
        // customErrorMessages
        $messages = customErrorMessagesTrait::messages();
        // require nik ketika pasien tidak dikenal
        $rules = [
            "collectingMyProduct.productId" => 'bail|',
            "collectingMyProduct.productName" => 'bail|required|',
            "collectingMyProduct.signaX" => 'bail|numeric|min:1|max:5',
            "collectingMyProduct.signaHari" => 'bail|numeric|min:1|max:5',
            "collectingMyProduct.qty" => 'bail|required|digits_between:1,3|',
            "collectingMyProduct.productPrice" => 'bail|numeric|',
            "collectingMyProduct.catatanKhusus" => 'bail|required|max:150',
            "collectingMyProduct.catatan" => 'bail|required|max:150',
            "collectingMyProduct.sedia" => 'bail|required|max:150',



        ];

        // Proses Validasi///////////////////////////////////////////
        $this->validate($rules, $messages);

        // validate


        // pengganti race condition
        // start:
        try {
            // select nvl(max(rjobat_dtl)+1,1) into :rstxn_rjobatracikans.rjobat_dtl from rstxn_rjobatracikans;

            $lastInserted = DB::table('rstxn_rjobatracikans')
                ->select(DB::raw("nvl(max(rjobat_dtl)+1,1) as rjobat_dtl_max"))
                ->first();
            // insert into table transaksi
            DB::table('rstxn_rjobatracikans')
                ->insert([
                    'rjobat_dtl' => $lastInserted->rjobat_dtl_max,
                    'rj_no' => $this->rjNoRef,
                    // 'product_id' => $this->collectingMyProduct['productId'],
                    'product_name' => $this->collectingMyProduct['productName'],
                    'qty' => $this->collectingMyProduct['qty'],
                    // 'price' => $this->collectingMyProduct['productPrice'],
                    // 'rj_carapakai' => $this->collectingMyProduct['signaX'],
                    // 'rj_kapsul' => $this->collectingMyProduct['signaHari'],
                    'rj_takar' => 'Tablet',
                    'rj_ket' => $this->collectingMyProduct['catatanKhusus'],
                    'exp_date' => DB::raw("to_date('" . $this->dataDaftarPoliRJ['rjDate'] . "','dd/mm/yyyy hh24:mi:ss')+30"),
                    'etiket_status' => 1,
                ]);


            $this->dataDaftarPoliRJ['eresepRacikan'][] = [
                'jenisKeterangan' => 'Racikan', //Racikan non racikan
                // 'productId' => $this->collectingMyProduct['productId'],
                'productName' => $this->collectingMyProduct['productName'],
                'sedia' => $this->collectingMyProduct['sedia'],
                'catatan' => $this->collectingMyProduct['catatan'],
                'qty' => $this->collectingMyProduct['qty'],
                'catatanKhusus' => $this->collectingMyProduct['catatanKhusus'],
                'noRacikan' => $this->noRacikan,
                'signaX' => 1,
                'signaHari' => 1,
                'productPrice' => 0,
                'rjObatDtl' => $lastInserted->rjobat_dtl_max,
                'rjNo' => $this->rjNoRef,
            ];

            $this->store();
            $this->reset(['collectingMyProduct']);


            //
        } catch (Exception $e) {
            // display an error to user
            dd($e->getMessage());
        }
        // goto start;
    }

    public function removeProduct($rjObatDtl)
    {

        $this->checkRjStatus();
        $this->resetValidation();



        // pengganti race condition
        // start:
        try {
            // remove into table transaksi
            DB::table('rstxn_rjobatracikans')
                ->where('rjobat_dtl', $rjObatDtl)
                ->delete();


            $Product = collect($this->dataDaftarPoliRJ['eresepRacikan'])->where("rjObatDtl", '!=', $rjObatDtl)->toArray();
            $this->dataDaftarPoliRJ['eresepRacikan'] = $Product;
            $this->store();


            //
        } catch (Exception $e) {
            // display an error to user
            dd($e->getMessage());
        }
        // goto start;


    }

    public function resetcollectingMyProduct()
    {
        $this->reset(['collectingMyProduct']);
        $this->resetValidation();
    }

    public function checkRjStatus()
    {
        $lastInserted = DB::table('rstxn_rjhdrs')
            ->select('rj_status')
            ->where('rj_no', $this->rjNoRef)
            ->first();

        if ($lastInserted->rj_status !== 'A') {
            // throw new Exception('Pasien Sudah Pulang, Trasaksi Terkunci.');
            $this->emit('toastr-error', "Pasien Sudah Pulang, Trasaksi Terkunci.xx");
            exit();
            // return (dd('Pasien Sudah Pulang, Trasaksi Terkunci.'));
        }
    }


    // when new form instance
    public function mount()
    {
        $this->findData($this->rjNoRef);
        // set data dokter ref
    }



    // select data start////////////////
    public function render()
    {

        return view(
            'livewire.emr-r-j.eresep-r-j.eresep-r-j-racikan',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Data Pasien Rawat Jalan',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'ICD 10',
            ]
        );
    }
    // select data end////////////////


}
