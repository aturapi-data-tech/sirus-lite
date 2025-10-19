<?php

namespace App\Http\Livewire\EmrRI\MrRI\ObatDanCairan;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Cache\LockTimeoutException;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

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
        if (
            $propertyName === 'obatDanCairan.namaObatAtauJenisCairan' &&
            empty($this->obatDanCairan['namaObatAtauJenisCairan'])
        ) {
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


    // insert and updat record start////////////////



    private function findData($riHdrNo): void
    {
        $this->dataDaftarRi = $this->findDataRI($riHdrNo) ?: [];
        $this->dataDaftarRi['riHdrNo'] = $this->dataDaftarRi['riHdrNo'] ?? $riHdrNo;

        if (!isset($this->dataDaftarRi['observasi']) || !is_array($this->dataDaftarRi['observasi'])) {
            $this->dataDaftarRi['observasi'] = [];
        }

        if (
            !isset($this->dataDaftarRi['observasi']['obatDanCairan']) ||
            !is_array($this->dataDaftarRi['observasi']['obatDanCairan'])
        ) {
            $this->dataDaftarRi['observasi']['obatDanCairan'] = $this->observasi;
        }
    }



    public function addObatDanCairan()
    {
        $riHdrNo = $this->dataDaftarRi['riHdrNo'] ?? $this->riHdrNoRef ?? null;
        if (!$riHdrNo) {
            toastr()->positionClass('toast-top-left')->addError("riHdrNo kosong.");
            return;
        }

        // set pemeriksa & validasi input
        $this->obatDanCairan['pemeriksa'] = auth()->user()->myuser_name;
        $this->validateDataObatDanCairanRi();

        $target = trim((string)$this->obatDanCairan['waktuPemberian']);

        try {
            $this->withRiLock($riHdrNo, function () use ($riHdrNo, $target) {
                // 1) Fresh
                $fresh = $this->findDataRI($riHdrNo) ?: [];
                $fresh['riHdrNo'] = $fresh['riHdrNo'] ?? $riHdrNo;

                if (!isset($fresh['observasi']) || !is_array($fresh['observasi'])) {
                    $fresh['observasi'] = [];
                }
                if (!isset($fresh['observasi']['obatDanCairan']) || !is_array($fresh['observasi']['obatDanCairan'])) {
                    $fresh['observasi']['obatDanCairan'] = [
                        'pemberianObatDanCairanTab' => 'Pemberian Obat Dan Cairan',
                        'pemberianObatDanCairan'    => [],
                    ];
                }

                // 2) Normalisasi + cek duplikat
                $list = $fresh['observasi']['obatDanCairan']['pemberianObatDanCairan'] ?? [];
                $list = collect($list)->map(fn($r) => is_array($r) ? $r : (array)$r)->values()->all();

                $dup = collect($list)->contains(function ($r) use ($target) {
                    return trim((string)($r['waktuPemberian'] ?? '')) === $target;
                });
                if ($dup) {
                    throw new \RuntimeException("ObatDanCairan sudah ada.");
                }

                // 3) Append item
                $list[] = [
                    "namaObatAtauJenisCairan" => (string)$this->obatDanCairan['namaObatAtauJenisCairan'],
                    "jumlah"                  => (float)$this->obatDanCairan['jumlah'],
                    "dosis"                   => (string)$this->obatDanCairan['dosis'],
                    "rute"                    => (string)$this->obatDanCairan['rute'],
                    "keterangan"              => (string)$this->obatDanCairan['keterangan'],
                    "waktuPemberian"          => $target,
                    "pemeriksa"               => (string)$this->obatDanCairan['pemeriksa'],
                ];

                // 4) Tulis balik hanya subtree + log
                $fresh['observasi']['obatDanCairan']['pemberianObatDanCairan'] = array_values($list);
                $fresh['observasi']['obatDanCairan']['pemberianObatDanCairanLog'] = [
                    'userLogDesc' => 'Form Entry obatDanCairan',
                    'userLog'     => auth()->user()->myuser_name,
                    'userLogDate' => Carbon::now(config('app.timezone'))->format('d/m/Y H:i:s'),
                ];

                // 5) Simpan & sinkronkan state komponen
                $this->updateJsonRI($riHdrNo, $fresh);
                $this->dataDaftarRi = $fresh;
            });

            // cleanup UI state
            $this->resetcollectingMyProduct();
            $this->reset(['obatDanCairan']);
            $this->emit('syncronizeAssessmentPerawatRIFindData');
            toastr()->positionClass('toast-top-left')->addSuccess("ObatDanCairan berhasil disimpan.");
        } catch (LockTimeoutException $e) {
            toastr()->positionClass('toast-top-left')->addError('Sistem sibuk, gagal memperoleh lock. Coba lagi.');
        } catch (\RuntimeException $e) {
            toastr()->positionClass('toast-top-left')->addError($e->getMessage());
        } catch (\Throwable $e) {
            toastr()->positionClass('toast-top-left')->addError('Gagal menyimpan: ' . $e->getMessage());
        }
    }


    public function removeObatDanCairan($waktuPemberian)
    {
        $riHdrNo = $this->dataDaftarRi['riHdrNo'] ?? $this->riHdrNoRef ?? null;
        if (!$riHdrNo) {
            toastr()->positionClass('toast-top-left')->addError("riHdrNo kosong.");
            return;
        }

        $target = trim((string) $waktuPemberian);

        try {
            $this->withRiLock($riHdrNo, function () use ($riHdrNo, $target) {
                // 1) Ambil fresh
                $fresh = $this->findDataRI($riHdrNo) ?: [];
                $fresh['riHdrNo'] = $fresh['riHdrNo'] ?? $riHdrNo;

                if (!isset($fresh['observasi']) || !is_array($fresh['observasi'])) {
                    $fresh['observasi'] = [];
                }
                if (!isset($fresh['observasi']['obatDanCairan']) || !is_array($fresh['observasi']['obatDanCairan'])) {
                    $fresh['observasi']['obatDanCairan'] = [
                        'pemberianObatDanCairanTab' => 'Pemberian Obat Dan Cairan',
                        'pemberianObatDanCairan'    => [],
                    ];
                }

                $list = $fresh['observasi']['obatDanCairan']['pemberianObatDanCairan'] ?? [];
                $list = collect($list)->map(fn($r) => is_array($r) ? $r : (array)$r)->values()->all();

                // 2) Hapus hanya match pertama (strict compare)
                $removed  = false;
                $filtered = [];
                foreach ($list as $row) {
                    $rowTime = trim((string) ($row['waktuPemberian'] ?? ''));
                    if (!$removed && $rowTime === $target) {
                        $removed = true;
                        continue;
                    }
                    $filtered[] = $row;
                }

                if (!$removed) {
                    throw new \RuntimeException('Data dengan waktu pemberian tersebut tidak ditemukan.');
                }

                // 3) Tulis balik subtree + log
                $fresh['observasi']['obatDanCairan']['pemberianObatDanCairan'] = array_values($filtered);
                $fresh['observasi']['obatDanCairan']['pemberianObatDanCairanLog'] = [
                    'userLogDesc' => 'Hapus obatDanCairan (by waktuPemberian)',
                    'userLog'     => auth()->user()->myuser_name,
                    'userLogDate' => Carbon::now(config('app.timezone'))->format('d/m/Y H:i:s'),
                ];

                // 4) Simpan & sinkronkan state
                $this->updateJsonRI($riHdrNo, $fresh);
                $this->dataDaftarRi = $fresh;
            });

            $this->emit('syncronizeAssessmentPerawatRIFindData');
            toastr()->positionClass('toast-top-left')->addSuccess('Item berhasil dihapus.');
        } catch (LockTimeoutException $e) {
            toastr()->positionClass('toast-top-left')->addError('Sistem sibuk, gagal memperoleh lock. Coba lagi.');
        } catch (\RuntimeException $e) {
            toastr()->positionClass('toast-top-left')->addError($e->getMessage());
        } catch (\Throwable $e) {
            toastr()->positionClass('toast-top-left')->addError('Gagal menghapus: ' . $e->getMessage());
        }
    }
    public function setWaktuPemberian($myTime)
    {
        $this->obatDanCairan['waktuPemberian'] = Carbon::now(config('app.timezone'))->format('d/m/Y H:i:s');
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
            toastr()->positionClass('toast-top-left')->addError('Data Obat belum tersedia.');
            return;
        }

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




    private function withRiLock(string $riHdrNo, callable $fn): void
    {
        $key = "ri:{$riHdrNo}";
        Cache::lock($key, 10)->block(5, function () use ($fn) {
            DB::transaction(function () use ($fn) {
                $fn();
            });
        });
    }
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
