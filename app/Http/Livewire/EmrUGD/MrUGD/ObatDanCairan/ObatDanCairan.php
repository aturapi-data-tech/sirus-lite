<?php

namespace App\Http\Livewire\EmrUGD\MrUGD\ObatDanCairan;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Cache\LockTimeoutException;
use \Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithPagination;

use App\Http\Traits\EmrUGD\EmrUGDTrait;
use App\Http\Traits\customErrorMessagesTrait;

class ObatDanCairan extends Component
{
    use WithPagination, EmrUGDTrait, customErrorMessagesTrait;

    // listener from blade////////////////
    protected $listeners = [];



    //////////////////////////////
    // Ref on top bar
    //////////////////////////////



    // dataDaftarUgd RJ
    public $rjNoRef;

    public array $dataDaftarUgd = [];

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
        'obatDanCairan.jumlah'                  => 'required|numeric',
        'obatDanCairan.dosis'                   => 'required',
        'obatDanCairan.rute'                    => 'required',
        'obatDanCairan.keterangan'              => 'required',
        'obatDanCairan.waktuPemberian'          => 'required|date_format:d/m/Y H:i:s',
        'obatDanCairan.pemeriksa'               => 'required',
    ];

    protected $messages = [
        'required'   => ':attribute wajib diisi.',
        'numeric'    => ':attribute harus berupa angka.',
        'date_format' => ':attribute harus dengan format dd/mm/yyyy hh:mm:ss',
    ];

    protected $validationAttributes = [
        'obatDanCairan.namaObatAtauJenisCairan' => 'Nama obat / jenis cairan',
        'obatDanCairan.jumlah'                  => 'Jumlah',
        'obatDanCairan.dosis'                   => 'Dosis',
        'obatDanCairan.rute'                    => 'Rute pemberian',
        'obatDanCairan.keterangan'              => 'Keterangan',
        'obatDanCairan.waktuPemberian'          => 'Waktu pemberian',
        'obatDanCairan.pemeriksa'               => 'Pemeriksa',
    ];



    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////
    public function updated($propertyName)
    {
        // reset LOV Product ketika namaObatAtauJenisCairan kosong
        if (empty($this->obatDanCairan['namaObatAtauJenisCairan'] ?? null)) {
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
    private function validateDataObatDanCairanUgd(): void
    {
        // Proses Validasi///////////////////////////////////////////
        try {
            $this->validate($this->rules, $this->messages, $this->validationAttributes);
        } catch (ValidationException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError($e->validator->errors()->first()); // tampilkan 1 error paling jelas
            throw $e; // stop eksekusi
        }
    }


    // insert and update record start////////////////
    public function store()
    {

        if (!$this->checkUgdStatus()) return;

        $rjNo = $this->dataDaftarUgd['rjNo'] ?? $this->rjNoRef ?? null;
        if (!$rjNo) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('rjNo kosong.');
            return;
        }

        $lockKey = "ugd:{$rjNo}";
        try {
            Cache::lock($lockKey, 5)->block(3, function () use ($rjNo) {
                DB::transaction(function () use ($rjNo) {
                    $fresh = $this->findDataUGD($rjNo) ?: [];

                    if (!isset($fresh['observasi']) || !is_array($fresh['observasi'])) {
                        $fresh['observasi'] = [];
                    }

                    if (isset($this->dataDaftarUgd['observasi']['obatDanCairan'])) {
                        $fresh['observasi']['obatDanCairan'] = $this->dataDaftarUgd['observasi']['obatDanCairan'];
                    }

                    $this->updateJsonUGD($rjNo, $fresh);
                    $this->dataDaftarUgd = $fresh;
                });
            });

            $this->resetcollectingMyProduct();
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('Obat & Cairan tersimpan.');
        } catch (LockTimeoutException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Sistem sibuk, gagal memperoleh lock. Coba lagi.');
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Gagal menyimpan.');
        }
    }

    // insert and update record end////////////////


    private function findData($rjno): void
    {

        $this->dataDaftarUgd = $this->findDataUGD($rjno) ?: [];

        if (!isset($this->dataDaftarUgd['observasi']) || !is_array($this->dataDaftarUgd['observasi'])) {
            $this->dataDaftarUgd['observasi'] = [];
        }
        if (!isset($this->dataDaftarUgd['observasi']['obatDanCairan'])) {
            $this->dataDaftarUgd['observasi']['obatDanCairan'] = $this->observasi;
        }
    }



    public function addObatDanCairan()
    {
        if (!$this->checkUgdStatus()) return;

        $this->obatDanCairan['pemeriksa'] = auth()->user()->myuser_name ?? '';

        // validasi form
        $this->validateDataObatDanCairanUgd();

        $rjNo = $this->dataDaftarUgd['rjNo'] ?? $this->rjNoRef ?? null;
        if (!$rjNo) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('rjNo kosong.');
            return;
        }

        $lockKey = "ugd:{$rjNo}";
        try {
            Cache::lock($lockKey, 5)->block(3, function () use ($rjNo) {
                DB::transaction(function () use ($rjNo) {
                    // ambil data terbaru dari DB agar tidak menimpa modul lain
                    $fresh = $this->findDataUGD($rjNo) ?: [];

                    // siapkan subtree
                    if (!isset($fresh['observasi']['obatDanCairan']) || !is_array($fresh['observasi']['obatDanCairan'])) {
                        $fresh['observasi']['obatDanCairan'] = [
                            "pemberianObatDanCairanTab" => "Pemberian Obat Dan Cairan",
                            "pemberianObatDanCairan"    => [],
                        ];
                    }

                    $list = $fresh['observasi']['obatDanCairan']['pemberianObatDanCairan'];

                    // idempoten berdasarkan waktuPemberian
                    $exists = collect($list)->firstWhere('waktuPemberian', $this->obatDanCairan['waktuPemberian']);
                    if ($exists) {
                        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addInfo('Data pada waktu tersebut sudah ada.');
                        return;
                    }

                    $list[] = [
                        "namaObatAtauJenisCairan" => $this->obatDanCairan['namaObatAtauJenisCairan'],
                        "jumlah"        => $this->obatDanCairan['jumlah'],
                        "dosis"         => $this->obatDanCairan['dosis'],
                        "rute"          => $this->obatDanCairan['rute'],
                        "keterangan"    => $this->obatDanCairan['keterangan'],
                        "waktuPemberian" => $this->obatDanCairan['waktuPemberian'],
                        "pemeriksa"     => $this->obatDanCairan['pemeriksa'],
                    ];

                    $fresh['observasi']['obatDanCairan']['pemberianObatDanCairan'] = array_values($list);
                    $fresh['observasi']['obatDanCairan']['pemberianObatDanCairanLog'] = [
                        'userLogDesc' => 'Form Entry Obat & Cairan',
                        'userLog'     => auth()->user()->myuser_name ?? '',
                        'userLogDate' => now(config('app.timezone'))->format('d/m/Y H:i:s'),
                    ];

                    // commit JSON
                    $this->updateJsonUGD($rjNo, $fresh);

                    // sinkronkan state lokal
                    $this->dataDaftarUgd = $fresh;
                });
            });

            // beres: reset LOV & form
            $this->resetcollectingMyProduct();
            $this->reset(['obatDanCairan']);
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('Obat & Cairan berhasil ditambahkan.');
        } catch (LockTimeoutException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Sistem sibuk, gagal memperoleh lock. Coba lagi.');
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Gagal menambah Obat & Cairan.');
        }
    }


    public function removeObatDanCairan($waktuPemberian)
    {

        if (!$this->checkUgdStatus()) return;

        $rjNo = $this->dataDaftarUgd['rjNo'] ?? $this->rjNoRef ?? null;
        if (!$rjNo) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('rjNo kosong.');
            return;
        }

        $lockKey = "ugd:{$rjNo}";
        try {
            Cache::lock($lockKey, 5)->block(3, function () use ($rjNo, $waktuPemberian) {
                DB::transaction(function () use ($rjNo, $waktuPemberian) {
                    $fresh = $this->findDataUGD($rjNo) ?: [];

                    if (!isset($fresh['observasi']['obatDanCairan']['pemberianObatDanCairan'])) {
                        return; // tidak ada yang dihapus
                    }

                    $list = $fresh['observasi']['obatDanCairan']['pemberianObatDanCairan'];
                    $list = collect($list)
                        ->reject(fn($row) => (string)($row['waktuPemberian'] ?? '') === (string)$waktuPemberian)
                        ->values()->all();

                    $fresh['observasi']['obatDanCairan']['pemberianObatDanCairan'] = $list;
                    $fresh['observasi']['obatDanCairan']['pemberianObatDanCairanLog'] = [
                        'userLogDesc' => 'Hapus Obat & Cairan',
                        'userLog'     => auth()->user()->myuser_name ?? '',
                        'userLogDate' => now(config('app.timezone'))->format('d/m/Y H:i:s'),
                    ];

                    $this->updateJsonUGD($rjNo, $fresh);
                    $this->dataDaftarUgd = $fresh;
                });
            });

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('Obat & Cairan dihapus.');
        } catch (LockTimeoutException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Sistem sibuk, gagal memperoleh lock. Coba lagi.');
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Gagal menghapus Obat & Cairan.');
        }
    }

    public function setWaktuPemberian($myTime)
    {
        $this->obatDanCairan['waktuPemberian'] = $myTime;
    }





    /////////////////////////////////////////////////
    // Lov dataProductLov //////////////////////
    ////////////////////////////////////////////////

    public array $dataProductLov = [];
    public bool $dataProductLovStatus = false;
    public $dataProductLovSearch = '';
    public int $selecteddataProductLovIndex = 0;

    public array $collectingMyProduct = [];


    public function clickdataProductLov()
    {
        $this->dataProductLovStatus = true;
        $this->dataProductLov = [];
    }

    public function updatedDataProductLovSearch()
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

        if (!isset($this->dataProductLov[$id]['product_id'])) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Item tidak valid.');
            return;
        }

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

        if (!isset($this->dataProductLov[$id]['product_id'])) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Data Obat belum tersedia.');
            return;
        }

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



    private function checkUgdStatus(): bool
    {
        $row = DB::table('rstxn_ugdhdrs')
            ->select('rj_status')
            ->where('rj_no', $this->rjNoRef)
            ->first();

        if (!$row || $row->rj_status !== 'A') {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Pasien Sudah Pulang, Transaksi Terkunci.');
            return false;
        }
        return true;
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
            'livewire.emr-u-g-d.mr-u-g-d.obat-dan-cairan.obat-dan-cairan',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Obat Dan Cairan',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Pasien UGD',
            ]
        );
    }
    // select data end////////////////


}
