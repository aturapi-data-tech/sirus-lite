<?php

namespace App\Http\Livewire\MasterDokter\FormEntryTemplateResepDokter\TemplateResepDokter;

use App\Http\Traits\customErrorMessagesTrait;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Exception;



class TemplateResepDokterRacikan extends Component
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

    public array $tempJsonRacikan = [];
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
            $this->emit('toastr-error', 'Data Obat belum tersedia.');
        }
    }

    // LOV selected end
    /////////////////////////////////////////////////
    // Lov dataProductLov //////////////////////
    ////////////////////////////////////////////////


    // insert and update record start////////////////
    public function store()
    {
        $this->updateJsonTempJsonRacikan();
    }


    private function findData($dokterId, $temprId): void
    {
        $findData = DB::table('rsmst_doctortempreseps')
            ->select('temp_json_racikan')
            ->where('dr_id', $dokterId)
            ->where('tempr_id', $temprId)
            ->first();

        $this->tempJsonRacikan = json_decode($findData->temp_json_racikan ?? '{}', true);
    }

    private function addProduct($productId, $productName,): void
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
            "collectingMyProduct.productId" => 'bail|',
            "collectingMyProduct.productName" => 'bail|required|',
            "collectingMyProduct.signaX" => 'bail|numeric|min:1|max:5',
            "collectingMyProduct.signaHari" => 'bail|numeric|min:1|max:5',
            "collectingMyProduct.qty" => 'bail|digits_between:1,3|',
            // "collectingMyProduct.productPrice" => 'bail|numeric|',
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
            $this->tempJsonRacikan['eresepRacikan'][] = [
                'productId' => $this->collectingMyProduct['productId'],
                'productName' => $this->collectingMyProduct['productName'],
                'jenisKeterangan' => 'Racikan', //Racikan non racikan
                'signaX' => 1,
                'sedia' => $this->collectingMyProduct['sedia'],
                'dosis' => $this->collectingMyProduct['dosis'],
                'catatan' => isset($this->collectingMyProduct['catatan']) ? $this->collectingMyProduct['catatan'] : '',
                'qty' => isset($this->collectingMyProduct['qty']) ? $this->collectingMyProduct['qty'] : '',
                'noRacikan' => $this->noRacikan,
                'signaHari' => 1,
                'catatanKhusus' => isset($this->collectingMyProduct['catatanKhusus']) ? $this->collectingMyProduct['catatanKhusus'] : '',
                'temprId' => $this->temprIdRef,

                // 'productPrice' => 0,
            ];

            $this->store();
            $this->reset(['collectingMyProduct']);
            $this->emit('toastr-success', 'Data Obat berhasil disimpan.');


            //
        } catch (Exception $e) {
            // display an error to user
            dd($e->getMessage());
        }
        // goto start;
    }

    public function updateProduct($key, $dosis = null, $qty = null, $catatan = null, $catatanKhusus = null): void
    {
        // validate
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
            $this->tempJsonRacikan['eresepRacikan'][$key] = [
                'productId' => $this->tempJsonRacikan['eresepRacikan'][$key]['productId'],
                'productName' => $this->tempJsonRacikan['eresepRacikan'][$key]['productName'],
                'jenisKeterangan' => $this->tempJsonRacikan['eresepRacikan'][$key]['jenisKeterangan'], //Racikan non racikan
                'signaX' => $this->tempJsonRacikan['eresepRacikan'][$key]['signaX'],
                'sedia' => $this->tempJsonRacikan['eresepRacikan'][$key]['sedia'],
                'dosis' => $dosis,
                'catatan' =>  $catatan,
                'qty' =>  $qty,
                'noRacikan' => $this->tempJsonRacikan['eresepRacikan'][$key]['noRacikan'],
                'signaHari' => $this->tempJsonRacikan['eresepRacikan'][$key]['signaHari'],
                'catatanKhusus' => $catatanKhusus,
                'temprId' => $this->tempJsonRacikan['eresepRacikan'][$key]['temprId'],
            ];

            $this->store();
            $this->emit('toastr-success', 'Data Obat berhasil diupdate.');

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

            $Product = collect($this->tempJsonRacikan['eresepRacikan'])
                ->where("productId", '!=', $productId)
                ->toArray();
            $this->tempJsonRacikan['eresepRacikan'] = $Product;
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

    private function updateJsonTempJsonRacikan(): void
    {
        DB::table('rsmst_doctortempreseps')
            ->where('dr_id', $this->drIdRef)
            ->where('tempr_id', $this->temprIdRef)
            ->update([
                'temp_json_racikan' => json_encode($this->tempJsonRacikan, true)
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

        return view('livewire.master-dokter.form-entry-template-resep-dokter.template-resep-dokter.template-resep-dokter-racikan');
    }
    // select data end////////////////


}
