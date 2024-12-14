<?php

namespace App\Http\Livewire\MasterDokter\FormEntryTemplateResepDokter\TemplateResepDokter;

use App\Http\Traits\customErrorMessagesTrait;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Exception;


class TemplateResepDokter extends Component
{

    // listener from blade////////////////
    protected $listeners = [
        'syncronizeTemplateResepDokterFindData' => 'mount',
    ];

    //////////////////////////////
    // Ref on top bar
    //////////////////////////////
    public $temprIdRef;
    public $drIdRef;

    public array $tempJsonNonRacikan = [];
    //////////////////////////////////////////////////////////////////////

    //  table LOV////////////////

    public $dataProductLov = [];
    public $dataProductLovStatus = 0;
    public $dataProductLovSearch = '';
    public $selecteddataProductLovIndex = 0;

    public $collectingMyProduct = [];

    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////


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
        $this->updateJsonTempJsonNonRacikan();
    }

    private function findData($dokterId, $temprId): void
    {

        $findData = DB::table('rsmst_doctortempreseps')
            ->select('temp_json_nonracikan')
            ->where('dr_id', $dokterId)
            ->where('tempr_id', $temprId)
            ->first();

        $this->tempJsonNonRacikan = json_decode($findData->temp_json_nonracikan ?? '{}', true);
    }

    private function addProduct($productId, $productName): void
    {
        $this->collectingMyProduct = [
            'productId' => $productId,
            'productName' => $productName,
            'jenisKeterangan' => 'NonRacikan', //Racikan non racikan
            'signaX' => 1,
            'signaHari' => 1,
            'qty' => 1,
            // 'productPrice' => $salesPrice,
            'catatanKhusus' => '',
        ];
    }

    public function insertProduct(): void
    {

        // validate
        // customErrorMessages
        $messages = customErrorMessagesTrait::messages();
        $rules = [
            'collectingMyProduct.productId' => 'bail|required|',
            'collectingMyProduct.productName' => 'bail|required|',
            'collectingMyProduct.signaX' => 'bail|required|',
            'collectingMyProduct.signaHari' => 'bail|required|',
            'collectingMyProduct.qty' => 'bail|required|digits_between:1,3|',
            // 'collectingMyProduct.productPrice' => 'bail|required|numeric|',
            'collectingMyProduct.catatanKhusus' => 'bail|',
        ];

        // Proses Validasi///////////////////////////////////////////
        $this->validate($rules, $messages);


        // pengganti race condition
        // start:

        try {

            $this->tempJsonNonRacikan['eresep'][] = [
                'productId' => $this->collectingMyProduct['productId'],
                'productName' => $this->collectingMyProduct['productName'],
                'jenisKeterangan' => 'NonRacikan', //Racikan non racikan
                'signaX' => $this->collectingMyProduct['signaX'],
                'signaHari' => $this->collectingMyProduct['signaHari'],
                'qty' => $this->collectingMyProduct['qty'],
                // 'productPrice' => $this->collectingMyProduct['productPrice'],
                'catatanKhusus' => $this->collectingMyProduct['catatanKhusus'],
                'temprId' => $this->temprIdRef,
            ];

            // insert into table transaksi
            $this->store();
            $this->reset(['collectingMyProduct']);
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('Data Obat berhasil disimpan.');


            //
        } catch (Exception $e) {
            // display an error to user
            dd($e->getMessage());
        }
        // goto start;
    }

    public function updateProduct($key, $qty = null, $signaX = null, $signaHari = null, $catatanKhusus = null): void
    {
        // validate

        $r = [
            'qty' => $qty,
            'signaX' => $signaX,
            'signaHari' => $signaHari,
            'catatanKhusus' => $catatanKhusus,
        ];

        // customErrorMessages
        $messages = customErrorMessagesTrait::messages();
        $rules = [
            'qty' => 'bail|required|digits_between:1,3|',
            'signaX' => 'bail|required|',
            'signaHari' => 'bail|required|',
            'catatanKhusus' => 'bail|',
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
            $this->tempJsonNonRacikan['eresep'][$key] = [
                'productId' => $this->tempJsonNonRacikan['eresep'][$key]['productId'],
                'productName' => $this->tempJsonNonRacikan['eresep'][$key]['productName'],
                'jenisKeterangan' => 'NonRacikan', //Racikan non racikan
                'signaX' => $signaX,
                'signaHari' => $signaHari,
                'qty' => $qty,
                // 'productPrice' => $this->collectingMyProduct['productPrice'],
                'catatanKhusus' => $catatanKhusus,
                'temprId' => $this->tempJsonNonRacikan['eresep'][$key]['temprId'],
            ];

            // insert into table transaksi
            $this->store();
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('Data Obat berhasil diupdate.');

            //
        } catch (Exception $e) {
            // display an error to user
            dd($e->getMessage());
        }
        // goto start;
    }

    public function removeProduct($productId)
    {

        // pengganti race condition
        // start:
        try {
            // remove into table transaksi
            $Product = collect($this->tempJsonNonRacikan['eresep'])
                ->where('productId', '!=', $productId)
                ->toArray();
            $this->tempJsonNonRacikan['eresep'] = $Product;
            $this->store();
        } catch (Exception $e) {
            // display an error to user
            dd($e->getMessage());
        }
        // goto start;
    }

    public function resetcollectingMyProduct()
    {
        $this->reset(['collectingMyProduct']);
    }



    private function updateJsonTempJsonNonRacikan(): void
    {
        DB::table('rsmst_doctortempreseps')
            ->where('dr_id', $this->drIdRef)
            ->where('tempr_id', $this->temprIdRef)
            ->update([
                'temp_json_nonracikan' => json_encode($this->tempJsonNonRacikan, true)
            ]);
    }
    // when new form instance
    public function mount($drIdRef = '', $temprIdRef = '')
    {
        $this->drIdRef = $drIdRef;
        $this->temprIdRef = $temprIdRef;
        $this->findData($this->drIdRef, $this->temprIdRef);
    }

    // select data start////////////////
    public function render()
    {
        return view('livewire.master-dokter.form-entry-template-resep-dokter.template-resep-dokter.template-resep-dokter');
    }
    // select data end////////////////
}
