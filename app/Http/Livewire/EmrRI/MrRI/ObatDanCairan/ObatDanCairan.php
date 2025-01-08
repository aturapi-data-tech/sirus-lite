<?php

namespace App\Http\Livewire\EmrRI\MrRI\ObatDanCairan;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

use Spatie\ArrayToXml\ArrayToXml;
use App\Http\Traits\EmrRI\EmrRITrait;
use App\Http\Traits\customErrorMessagesTrait;

class ObatDanCairan extends Component
{
    use WithPagination, EmrRITrait, customErrorMessagesTrait;

    // listener from blade////////////////
    protected $listeners = [
        'syncronizeAssessmentPerawatRIFindData' => 'mount'
    ];



    //////////////////////////////
    // Ref on top bar
    //////////////////////////////



    // dataDaftarRi RJ
    public $riHdrNoRef;

    public array $dataDaftarRi = [];

    public array $obatDanCairan = [
        "namaObatAtauJenisCairan" => "",
        "jumlah" => "", //number
        "dosis" => "", //number
        "rute" => "", //number
        "keterangan" => "",
        "waktuPemberian" => "", //date dd/mm/yyyy hh24:mi:ss
        "pemeriksa" => ""
    ];

    public array $observasi =
    [
        "pemberianObatDanCairanTab" => "Pemberian Obat Dan Cairan",
        "pemberianObatDanCairan" => [],

    ];
    //////////////////////////////////////////////////////////////////////


    protected $rules = [
        'obatDanCairan.namaObatAtauJenisCairan' => 'required',
        'obatDanCairan.jumlah' => 'required|numeric',
        'obatDanCairan.dosis' => 'required',
        'obatDanCairan.rute' => 'required',
        'obatDanCairan.keterangan' => 'required',
        'obatDanCairan.waktuPemberian' => 'required|date_format:d/m/Y H:i:s',
        'obatDanCairan.pemeriksa' => 'required',
    ];



    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////
    public function updated($propertyName)
    {
        // dd($propertyName);
        // $this->validateOnly($propertyName);
        // $this->store();

        // reset LOV Product ketika namaObatAtauJenisCairan kosong
        if (!$this->obatDanCairan['namaObatAtauJenisCairan']) {
            $this->resetcollectingMyProduct();
        }
    }




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
    private function validateDataObatDanCairanRi(): void
    {
        // customErrorMessages
        // $messages = customErrorMessagesTrait::messages();
        $messages = [];

        // Proses Validasi///////////////////////////////////////////
        try {
            $this->validate($this->rules, $messages);
        } catch (\Illuminate\Validation\ValidationException $e) {

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Lakukan Pengecekan kembali Input Data." . $e->getMessage());
            $this->validate($this->rules, $messages);
        }
    }


    // insert and update record start////////////////
    public function store()
    {
        // Validate RJ

        // Logic update mode start //////////
        $this->updateDataRi($this->dataDaftarRi['riHdrNo']);

        // reset LOV Product ketika proses simpan selesai
        $this->resetcollectingMyProduct();

        $this->emit('syncronizeAssessmentPerawatRIFindData');
    }

    private function updateDataRi($riHdrNo): void
    {

        $this->updateJsonRI($riHdrNo, $this->dataDaftarRi);

        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("ObatDanCairan berhasil disimpan.");
    }
    // insert and update record end////////////////


    private function findData($riHdrNo): void
    {

        $this->dataDaftarRi = $this->findDataRI($riHdrNo);
        // dd($this->dataDaftarRi);
        // jika observasi tidak ditemukan tambah variable observasi pda array
        if (isset($this->dataDaftarRi['observasi']['obatDanCairan']) == false) {
            $this->dataDaftarRi['observasi']['obatDanCairan'] = $this->observasi;
        }
    }



    public function addObatDanCairan()
    {

        // entry Pemeriksa
        $this->obatDanCairan['pemeriksa'] = auth()->user()->myuser_name;

        // validasi
        $this->validateDataObatDanCairanRi();
        // check exist
        $cekdObatDanCairan = collect($this->dataDaftarRi['observasi']['obatDanCairan']['pemberianObatDanCairan'])
            ->where("waktuPemberian", '=', $this->obatDanCairan['waktuPemberian'])
            ->count();
        if (!$cekdObatDanCairan) {
            $this->dataDaftarRi['observasi']['obatDanCairan']['pemberianObatDanCairan'][] = [
                "namaObatAtauJenisCairan" => $this->obatDanCairan['namaObatAtauJenisCairan'],
                "jumlah" => $this->obatDanCairan['jumlah'],
                "dosis" => $this->obatDanCairan['dosis'],
                "rute" => $this->obatDanCairan['rute'],
                "keterangan" => $this->obatDanCairan['keterangan'],
                "waktuPemberian" => $this->obatDanCairan['waktuPemberian'],
                "pemeriksa" => $this->obatDanCairan['pemeriksa'],
            ];

            $this->dataDaftarRi['observasi']['obatDanCairan']['pemberianObatDanCairanLog'] =
                [
                    'userLogDesc' => 'Form Entry obatDanCairan',
                    'userLog' => auth()->user()->myuser_name,
                    'userLogDate' => Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s')
                ];

            $this->store();
            // reset obatDanCairan
            $this->reset(['obatDanCairan']);
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("ObatDanCairan Sudah ada.");
        }
    }

    public function removeObatDanCairan($waktuPemberian)
    {

        $obatDanCairan = collect($this->dataDaftarRi['observasi']['obatDanCairan']['pemberianObatDanCairan'])->where("waktuPemberian", '!=', $waktuPemberian)->toArray();
        $this->dataDaftarRi['observasi']['obatDanCairan']['pemberianObatDanCairan'] = $obatDanCairan;

        $this->dataDaftarRi['observasi']['obatDanCairan']['pemberianObatDanCairanLog'] =
            [
                'userLogDesc' => 'Hapus obatDanCairan',
                'userLog' => auth()->user()->myuser_name,
                'userLogDate' => Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s')
            ];
        $this->store();
    }

    public function setWaktuPemberian($myTime)
    {
        $this->obatDanCairan['waktuPemberian'] = $myTime;
    }





    /////////////////////////////////////////////////
    // Lov dataProductLov //////////////////////
    ////////////////////////////////////////////////

    public array $dataProductLov = [];
    public int $dataProductLovStatus = 0;
    public $dataProductLovSearch = '';
    public int $selecteddataProductLovIndex = 0;

    public array $collectingMyProduct = [];


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


    public function setMydataProductLov($id)
    {
        // $this->checkRjStatus();
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

        $this->obatDanCairan['namaObatAtauJenisCairan'] = $this->collectingMyProduct['productName'];
    }

    public function resetcollectingMyProduct()
    {
        $this->reset(['collectingMyProduct']);
    }
    // LOV selected end
    /////////////////////////////////////////////////
    // Lov dataProductLov //////////////////////
    ////////////////////////////////////////////////




    // when new form instance
    public function mount()
    {
        $this->findData($this->riHdrNoRef);
    }



    // select data start////////////////
    public function render()
    {

        return view(
            'livewire.emr-r-i.mr-r-i.obat-dan-cairan.obat-dan-cairan',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Obat Dan Cairan',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Pasien RI',
            ]
        );
    }
    // select data end////////////////


}
