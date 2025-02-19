<?php

namespace App\Http\Traits\LOV\LOVProduct;


use Illuminate\Support\Facades\DB;

trait LOVProductTrait
{

    public array $dataProductLov = [];
    public int $dataProductLovStatus = 0;
    public string $dataProductLovSearch = '';
    public int $selecteddataProductLovIndex = 0;
    public array $collectingMyProduct = [];

    /////////////////////////////////////////////////
    // Lov dataProductLov //////////////////////
    ////////////////////////////////////////////////

    public function updateddataProductLovsearch()
    {

        // Reset index of LoV
        $this->reset(['selecteddataProductLovIndex', 'dataProductLov']);
        // Variable Search
        $search = $this->dataProductLovSearch;

        // check LOV by product_id rs id
        $dataProductLovs = DB::table('immst_products ')
            ->select(
                'product_id',
                'product_name',
                'sales_price',
            )
            ->where('product_id', '=', $search)
            // ->where('active_status', '1')
            ->first();

        if ($dataProductLovs) {

            // set Product sep
            $this->addProduct($dataProductLovs->product_id, $dataProductLovs->product_name, $dataProductLovs->sales_price);;
            $this->resetdataProductLov();
        } else {

            // if there is no id found and check (min 1 char on search)
            if (strlen($search) < 1) {
                $this->dataProductLov = [];
            } else {
                $this->dataProductLov = json_decode(
                    DB::table('immst_products')
                        ->select(
                            'product_id',
                            'product_name',
                            'sales_price',
                        )
                        // ->where('active_status', '1')
                        ->Where(DB::raw('upper(product_name)'), 'like', '%' . strtoupper($search) . '%')
                        ->orWhere(DB::raw('upper(product_id)'), 'like', '%' . strtoupper($search) . '%')
                        ->limit(10)
                        ->orderBy('product_name', 'ASC')
                        ->orderBy('product_id', 'ASC')
                        ->get(),
                    true
                );
            }
            $this->dataProductLovStatus = true;
            // set doing nothing
        }
    }
    // /////////////////////
    // LOV selected start
    public function setMydataProductLov($id)
    {
        // $this->checkRjStatus();
        $dataProductLovs = DB::table('immst_products')
            ->select(
                'product_id',
                'product_name',
                'sales_price',
            )
            ->where('product_id', $this->dataProductLov[$id]['product_id'])
            ->first();

        // set lain sep
        $this->addProduct($dataProductLovs->product_id, $dataProductLovs->product_name, $dataProductLovs->sales_price);
        $this->resetdataProductLov();
    }

    public function resetdataProductLov()
    {
        $this->reset(['dataProductLov', 'dataProductLovStatus', 'dataProductLovSearch', 'selecteddataProductLovIndex']);
    }

    public function selectNextdataProductLov()
    {
        if ($this->selecteddataProductLovIndex === "") {
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

        if ($this->selecteddataProductLovIndex === "") {
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
        // dd($this->dataProductLov);
        // $this->checkRjStatus();
        // jika JK belum siap maka toaster error
        if (isset($this->dataProductLov[$id]['product_id'])) {
            $this->addProduct($this->dataProductLov[$id]['product_id'], $this->dataProductLov[$id]['product_name'], $this->dataProductLov[$id]['sales_price']);
            $this->resetdataProductLov();
        } else {
            $this->emit('toastr-error', "Kode belum tersedia.");
            return;
        }
    }


    private function addProduct($ProductId, $ProductName, $ProductPrice): void
    {
        $this->collectingMyProduct = [
            'ProductId' => $ProductId,
            'ProductName' => $ProductName,
            'ProductPrice' => $ProductPrice,
        ];
    }


    // LOV selected end
    /////////////////////////////////////////////////
    // Lov dataProductLov //////////////////////
    ////////////////////////////////////////////////
}
