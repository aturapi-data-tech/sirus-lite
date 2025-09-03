<?php

namespace App\Http\Livewire\EmrRJ\EresepRJ;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
// use Carbon\Carbon;

use App\Http\Traits\customErrorMessagesTrait;
use App\Http\Traits\EmrRJ\EmrRJTrait;

// use Illuminate\Support\Str;
use Spatie\ArrayToXml\ArrayToXml;
use Exception;
use Illuminate\Support\Facades\Validator;



class EresepRJRacikan extends Component
{
    use WithPagination, EmrRJTrait;


    // listener from blade////////////////
    protected $listeners = [
        'storeAssessmentDokterRJ' => 'store',
        'syncronizeAssessmentDokterRJFindData' => 'mount',
        'syncronizeAssessmentPerawatRJFindData' => 'mount'
    ];


    //////////////////////////////
    // Ref on top bar
    //////////////////////////////
    public $rjNoRef = 472309;
    public string $rjStatusRef;



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


    /////////////////////////////////////////////////
    // Lov dataProductLov //////////////////////
    ////////////////////////////////////////////////
    public function clickdataProductLov()
    {
        $this->dataProductLovStatus = true;
        $this->dataProductLov = [];
    }

    public function updateddataProductLovsearch()
    {
        // Reset index of LoV
        $this->reset(['selecteddataProductLovIndex', 'dataProductLov']);
        // Variable Search
        $search = $this->dataProductLovSearch;

        // check LOV by dr_id rs id
        $dataProductLovs = DB::table('immst_products')
            ->select(
                'product_id',
                'product_name',
                'sales_price',
                DB::raw("(select replace(string_agg(cont_desc),',','')||product_name
                                            from immst_productcontents z,immst_contents x
                                            where z.product_id=immst_products.product_id
                                            and z.cont_id=x.cont_id) as elasticSearch"),
                DB::raw("(select string_agg(cont_desc)
                                            from immst_productcontents z,immst_contents x
                                            where z.product_id=immst_products.product_id
                                            and z.cont_id=x.cont_id) as product_content"),
            )
            ->where('active_status', '1')
            ->where('product_id', $search)
            ->first();

        if ($dataProductLovs) {
            // set product sep
            $this->addProduct($dataProductLovs->product_id, $dataProductLovs->product_name, $dataProductLovs->sales_price);
            $this->resetdataProductLov();
        } else {
            // if there is no id found and check (min 3 char on search)
            if (strlen($search) < 3) {
                $this->dataProductLov = [];
            } else {
                $this->dataProductLov = DB::select(
                    "select * from (
                    select product_id,
                    product_name,
                    sales_price,

                    (select replace(string_agg(cont_desc),',','')||product_name
                    from immst_productcontents z,immst_contents x
                    where z.product_id=a.product_id
                    and z.cont_id=x.cont_id)elasticsearch,

                    (select string_agg(cont_desc)
                    from immst_productcontents z,immst_contents x
                    where z.product_id=a.product_id
                    and z.cont_id=x.cont_id)product_content

                    from immst_products a
                    where active_status='1'
                    group by product_id,product_name, sales_price
                    order by product_name)

                where upper(elasticsearch) like '%'||:search||'%'
                    ",
                    ['search' => strtoupper($search)],
                );

                $this->dataProductLov = json_decode(json_encode($this->dataProductLov, true), true);
            }
            $this->dataProductLovStatus = true;
            // set doing nothing
        }
    }
    // /////////////////////
    // LOV selected start
    public function setMydataProductLov($id)
    {
        $this->checkRjStatus();
        $dataProductLovs = DB::table('immst_products')
            ->select(
                'product_id',
                'product_name',
                'sales_price',
                DB::raw("(select replace(string_agg(cont_desc),',','')||product_name
                                                            from immst_productcontents z,immst_contents x
                                                            where z.product_id=immst_products.product_id
                                                            and z.cont_id=x.cont_id) as elasticSearch"),
                DB::raw("(select string_agg(cont_desc)
                                                            from immst_productcontents z,immst_contents x
                                                            where z.product_id=immst_products.product_id
                                                            and z.cont_id=x.cont_id) as product_content"),
            )
            ->where('active_status', '1')
            ->where('product_id', $this->dataProductLov[$id]['product_id'])
            ->first();

        // set dokter sep
        $this->addProduct($dataProductLovs->product_id, $dataProductLovs->product_name, $dataProductLovs->sales_price);
        $this->resetdataProductLov();
    }

    public function resetdataProductLov()
    {
        $this->reset(['dataProductLov', 'dataProductLovStatus', 'dataProductLovSearch', 'selecteddataProductLovIndex']);
    }

    public function selectNextdataProductLov()
    {
        if ($this->selecteddataProductLovIndex === '') {
            $this->selecteddataProductLovIndex = 0;
        } else {
            $this->selecteddataProductLovIndex++;
        }

        if ($this->selecteddataProductLovIndex === count($this->dataProductLov)) {
            $this->selecteddataProductLovIndex = 0;
        }
    }

    public function selectPreviousdataProductLov()
    {
        if ($this->selecteddataProductLovIndex === '') {
            $this->selecteddataProductLovIndex = count($this->dataProductLov) - 1;
        } else {
            $this->selecteddataProductLovIndex--;
        }

        if ($this->selecteddataProductLovIndex === -1) {
            $this->selecteddataProductLovIndex = count($this->dataProductLov) - 1;
        }
    }

    public function enterMydataProductLov($id)
    {
        $this->checkRjStatus();
        // jika data obat belum siap maka toaster error
        if (isset($this->dataProductLov[$id]['product_id'])) {
            $this->addProduct($this->dataProductLov[$id]['product_id'], $this->dataProductLov[$id]['product_name'], $this->dataProductLov[$id]['sales_price']);
            $this->resetdataProductLov();
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Data Obat belum tersedia.');
        }
    }

    // LOV selected end
    /////////////////////////////////////////////////
    // Lov dataProductLov //////////////////////
    ////////////////////////////////////////////////


    // insert and update record start////////////////
    public function store()
    {
        // set data RJno / NoBooking / NoAntrian / klaimId / kunjunganId
        $this->setDataPrimer();

        // Logic update mode start //////////
        $this->updateDataRJ($this->dataDaftarPoliRJ['rjNo']);
        $this->emit('syncronizeAssessmentDokterRJFindData');
        $this->emit('syncronizeAssessmentPerawatRJFindData');
    }

    private function updateDataRJ($rjNo): void
    {

        // if ($rjNo !== $this->dataDaftarPoliRJ['rjNo']) {
        //     dd('Data Json Tidak sesuai' . $rjNo . '  /  ' . $this->dataDaftarPoliRJ['rjNo']);
        // }

        // // update table trnsaksi
        // DB::table('rstxn_rjhdrs')
        //     ->where('rj_no', $rjNo)
        //     ->update([
        //         'dataDaftarPoliRJ_json' => json_encode($this->dataDaftarPoliRJ, true),
        //         'dataDaftarPoliRJ_xml' => ArrayToXml::convert($this->dataDaftarPoliRJ),
        //     ]);

        $this->updateJsonRJ($rjNo, $this->dataDaftarPoliRJ);

        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Eresep Racikan berhasil disimpan.");
    }
    // insert and update record end////////////////


    private function findData($rjno): void
    {
        $this->rjStatusRef = DB::table('rstxn_rjhdrs')->select('rj_status')->where('rj_no', $rjno)->first()->rj_status;
        $findDataRJ = $this->findDataRJ($rjno);
        $this->dataDaftarPoliRJ  = $findDataRJ['dataDaftarRJ'];

        // jika eresep tidak ditemukan tambah variable eresep pda array
        if (isset($this->dataDaftarPoliRJ['eresep']) == false) {
            $this->dataDaftarPoliRJ['eresep'] = [];
        }
    }


    private function setDataPrimer(): void {}

    private function addProduct($productId, $productName, $salesPrice): void
    {
        $this->collectingMyProduct = [
            'productId' => $productId,
            'productName' => $productName,
            'jenisKeterangan' => 'NonRacikan', //Racikan non racikan
            'signaX' => 1,
            'signaHari' => 1,
            'qty' => '',
            'sedia' => 1,
            'dosis' => '',
            'productPrice' => $salesPrice,
            'catatanKhusus' => '',
        ];
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
            "collectingMyProduct.qty" => 'bail|digits_between:1,3|',
            "collectingMyProduct.productPrice" => 'bail|numeric|',
            "collectingMyProduct.catatanKhusus" => 'bail|max:150',
            "collectingMyProduct.catatan" => 'bail|max:150',
            "collectingMyProduct.sedia" => 'bail|max:150',
            "collectingMyProduct.dosis" => 'bail|required|max:150',




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

            $productTakar = DB::table('immst_products')
                ->where('product_id', $this->collectingMyProduct['productId'])
                ->value('takar');

            $rjTakar = $productTakar ?: 'Tablet';
            // insert into table transaksi
            DB::table('rstxn_rjobatracikans')
                ->insert([
                    'rjobat_dtl' => $lastInserted->rjobat_dtl_max,
                    'rj_no' => $this->rjNoRef,
                    // 'product_id' => $this->collectingMyProduct['productId'],
                    'product_name' => $this->collectingMyProduct['productName'],
                    'sedia' => $this->collectingMyProduct['sedia'],
                    'dosis' => $this->collectingMyProduct['dosis'],
                    'qty' => isset($this->collectingMyProduct['qty']) ? $this->collectingMyProduct['qty'] : null,
                    // 'price' => $this->collectingMyProduct['productPrice'],
                    // 'rj_carapakai' => $this->collectingMyProduct['signaX'],
                    // 'rj_kapsul' => $this->collectingMyProduct['signaHari'],
                    'catatan' => isset($this->collectingMyProduct['catatan']) ? $this->collectingMyProduct['catatan'] : null,
                    'catatan_khusus' => isset($this->collectingMyProduct['catatanKhusus']) ? $this->collectingMyProduct['catatanKhusus'] : null,
                    'no_racikan' => $this->noRacikan,

                    'rj_takar' => $rjTakar,
                    'exp_date' => DB::raw("to_date('" . $this->dataDaftarPoliRJ['rjDate'] . "','dd/mm/yyyy hh24:mi:ss')+30"),
                    'etiket_status' => 1,
                ]);


            $this->dataDaftarPoliRJ['eresepRacikan'][] = [
                'jenisKeterangan' => 'Racikan', //Racikan non racikan
                // 'productId' => $this->collectingMyProduct['productId'],
                'productName' => $this->collectingMyProduct['productName'],
                'sedia' => $this->collectingMyProduct['sedia'],
                'dosis' => $this->collectingMyProduct['dosis'],
                'catatan' => isset($this->collectingMyProduct['catatan']) ? $this->collectingMyProduct['catatan'] : '',
                'qty' => isset($this->collectingMyProduct['qty']) ? $this->collectingMyProduct['qty'] : '',
                'catatanKhusus' => isset($this->collectingMyProduct['catatanKhusus']) ? $this->collectingMyProduct['catatanKhusus'] : '',
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

    public function updateProduct($rjobat_dtl, $dosis = null, $qty = null, $catatan = null, $catatanKhusus = null): void
    {

        // validate
        // $this->checkRjStatus();

        $r = [
            'qty' => $qty,
            'catatanKhusus' => $catatanKhusus,
            "catatan" => $catatan,
            "dosis" => $dosis,
        ];
        // customErrorMessages
        $messages = customErrorMessagesTrait::messages();
        // require nik ketika pasien tidak dikenal
        $rules = [
            "qty" => 'bail|digits_between:1,3|',
            "dosis" => 'bail|required|max:150',
            "catatan" => 'bail|max:150',
            "catatanKhusus" => 'bail|max:150',
        ];

        // Proses Validasi///////////////////////////////////////////
        $validator = Validator::make($r, $rules, $messages);

        if ($validator->fails()) {
            dd($validator->errors()->first());
        }

        // validate


        // pengganti race condition
        // start:
        try {

            // insert into table transaksi
            DB::table('rstxn_rjobatracikans')
                ->where('rjobat_dtl', $rjobat_dtl)
                ->update([
                    'qty' => $r['qty'],
                    'dosis' => $r['dosis'],
                    'catatan' => $r['catatan'],
                    'catatan_khusus' => $r['catatanKhusus'],
                ]);

            $this->store();

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
        // $this->resetValidation();



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
        // $this->resetValidation();
    }

    public function checkRjStatus()
    {
        $lastInserted = DB::table('rstxn_rjhdrs')
            ->select('rj_status')
            ->where('rj_no', $this->rjNoRef)
            ->first();

        if ($lastInserted->rj_status !== 'A') {
            // throw new Exception('Pasien Sudah Pulang, Trasaksi Terkunci.');
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Pasien Sudah Pulang, Trasaksi Terkunci.xx");
            // exit();
            return (dd('Pasien Sudah Pulang, Trasaksi Terkunci.'));
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
