<?php

namespace App\Http\Livewire\EmrUGD\EresepUGD;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Cache\LockTimeoutException;

use App\Http\Traits\customErrorMessagesTrait;
use App\Http\Traits\EmrUGD\EmrUGDTrait;

use Illuminate\Support\Facades\Validator;

class EresepUGD extends Component
{
    use  EmrUGDTrait;

    // listener from blade////////////////
    protected $listeners = [
        'storeAssessmentDokterUGD' => 'store',
    ];

    //////////////////////////////
    // Ref on top bar
    //////////////////////////////
    public $rjNoRef;
    public string $rjStatusRef;

    // dataDaftarUgd RJ
    public array $dataDaftarUgd = [];

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
    // /////////////////////
    // LOV selected start
    public function setMydataProductLov($id)
    {
        if (!$this->checkUgdStatus()) return;
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
        if (!$this->checkUgdStatus()) return;
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
        // optionally, kunci pasien masih aktif
        if (!$this->checkUgdStatus()) return;

        // pastikan RJ No ada
        $rjNo = $this->dataDaftarUgd['rjNo'] ?? $this->rjNoRef ?? null;
        if (!$rjNo) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('rjNo kosong.');
            return;
        }

        $lockKey = "ugd:{$rjNo}";

        try {
            Cache::lock($lockKey, 5)->block(3, function () use ($rjNo) {
                DB::transaction(function () use ($rjNo) {
                    // Ambil FRESH dari DB biar tidak menimpa subtree lain
                    $fresh = $this->findDataUGD($rjNo) ?: [];

                    // Pastikan subtree yang dikelola komponen ini ada
                    if (!isset($fresh['eresep']) || !is_array($fresh['eresep'])) {
                        $fresh['eresep'] = [];
                    }

                    // PATCH hanya bagian 'eresep' dari state lokal
                    $fresh['eresep'] = array_values((array)($this->dataDaftarUgd['eresep'] ?? []));

                    // Commit JSON besar sekali jalan
                    $this->updateJsonUGD($rjNo, $fresh);

                    // Sinkronkan state lokal supaya UI langsung up-to-date
                    $this->dataDaftarUgd = $fresh;
                });
            });



            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addSuccess('Eresep berhasil disimpan.');
        } catch (LockTimeoutException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Sistem sibuk, gagal memperoleh lock. Coba lagi.');
            return;
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Gagal menyimpan eresep.');
            return;
        }
    }

    private function findData($rjno): void
    {
        $row = DB::table('rstxn_ugdhdrs')->select('rj_status')->where('rj_no', $rjno)->first();
        $this->rjStatusRef = $row->rj_status ?? '';

        $this->dataDaftarUgd = $this->findDataUGD($rjno);

        // jika eresep tidak ditemukan tambah variable eresep pda array
        if (isset($this->dataDaftarUgd['eresep']) == false) {
            $this->dataDaftarUgd['eresep'] = [];
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
            'qty' => 1,
            'productPrice' => $salesPrice,
            'catatanKhusus' => '',
        ];
    }

    public function insertProduct(): void
    {
        // Validasi
        $messages = customErrorMessagesTrait::messages();
        $rules = [
            'collectingMyProduct.productId'     => 'bail|required',
            'collectingMyProduct.productName'   => 'bail|required',
            'collectingMyProduct.signaX'        => 'bail|required|numeric|min:1',
            'collectingMyProduct.signaHari'     => 'bail|required|numeric|min:1',
            'collectingMyProduct.qty'           => 'bail|required|digits_between:1,3|numeric|min:1',
            'collectingMyProduct.productPrice'  => 'bail|required|numeric|min:0',
            'collectingMyProduct.catatanKhusus' => 'bail|nullable',
        ];
        $this->validate($rules, $messages);

        if (!$this->checkUgdStatus()) return;

        $rjNo = $this->rjNoRef;
        $lockKey = "ugd:{$rjNo}";

        try {
            Cache::lock($lockKey, 5)->block(3, function () use ($rjNo) {
                DB::transaction(function () use ($rjNo) {
                    $max = DB::table('rstxn_ugdobats')
                        ->max(DB::raw('nvl(to_number(rjobat_dtl),0)'));
                    $nextDtl = (int)$max + 1;

                    $productTakar = DB::table('immst_products')
                        ->where('product_id', $this->collectingMyProduct['productId'])
                        ->value('takar');
                    $ugdTakar = $productTakar ?: 'Tablet';

                    DB::table('rstxn_ugdobats')->insert([
                        'rjobat_dtl'     => $nextDtl,
                        'rj_no'          => $rjNo,
                        'product_id'     => $this->collectingMyProduct['productId'],
                        'qty'            => $this->collectingMyProduct['qty'],
                        'price'          => $this->collectingMyProduct['productPrice'],
                        'ugd_carapakai'  => $this->collectingMyProduct['signaX'],
                        'ugd_kapsul'     => $this->collectingMyProduct['signaHari'],
                        'ugd_takar'      => $ugdTakar,
                        'catatan_khusus' => $this->collectingMyProduct['catatanKhusus'],
                        'ugd_ket'        => $this->collectingMyProduct['catatanKhusus'],
                        'exp_date'       => DB::raw("to_date('" . $this->dataDaftarUgd['rjDate'] . "','dd/mm/yyyy hh24:mi:ss')+330"),
                        'etiket_status'  => 1,
                    ]);

                    // patch state lokal biar UI instant
                    $this->dataDaftarUgd['eresep'][] = [
                        'productId'       => $this->collectingMyProduct['productId'],
                        'productName'     => $this->collectingMyProduct['productName'],
                        'jenisKeterangan' => 'NonRacikan',
                        'signaX'          => $this->collectingMyProduct['signaX'],
                        'signaHari'       => $this->collectingMyProduct['signaHari'],
                        'qty'             => $this->collectingMyProduct['qty'],
                        'productPrice'    => $this->collectingMyProduct['productPrice'],
                        'catatanKhusus'   => $this->collectingMyProduct['catatanKhusus'],
                        'rjObatDtl'       => $nextDtl,
                        'rjNo'            => $rjNo,
                    ];
                });
            });

            $this->store();
            $this->reset(['collectingMyProduct']);
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Gagal menambahkan obat.');
            return;
        }
    }


    public function updateProduct($rjobat_dtl, $qty = null, $signaX = null, $signaHari = null, $catatanKhusus = null): void
    {
        if (!$this->checkUgdStatus()) return;

        $payload = [
            'qty'           => $qty,
            'signaX'        => $signaX,
            'signaHari'     => $signaHari,
            'catatanKhusus' => $catatanKhusus,
        ];

        $messages = customErrorMessagesTrait::messages();
        $rules = [
            'qty'           => 'bail|required|digits_between:1,3|numeric|min:1',
            'signaX'        => 'bail|required|numeric|min:1',
            'signaHari'     => 'bail|required|numeric|min:1',
            'catatanKhusus' => 'bail|nullable',
        ];
        $validator = Validator::make($payload, $rules, $messages);
        if ($validator->fails()) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError($validator->errors()->first());
            return;
        }

        $rjNo = $this->rjNoRef;
        $lockKey = "ugd:{$rjNo}";

        try {
            Cache::lock($lockKey, 5)->block(3, function () use ($rjobat_dtl, $payload) {
                DB::transaction(function () use ($rjobat_dtl, $payload) {
                    $affected = DB::table('rstxn_ugdobats')
                        ->where('rjobat_dtl', $rjobat_dtl)
                        ->update([
                            'qty'            => $payload['qty'],
                            'ugd_carapakai'  => $payload['signaX'],
                            'ugd_kapsul'     => $payload['signaHari'],
                            'catatan_khusus' => $payload['catatanKhusus'],
                            'ugd_ket'        => $payload['catatanKhusus'],
                        ]);

                    if (!$affected) {
                        throw new \RuntimeException('Data obat tidak ditemukan.');
                    }

                    // patch state lokal agar UI langsung update
                    if (!empty($this->dataDaftarUgd['eresep'])) {
                        foreach ($this->dataDaftarUgd['eresep'] as &$it) {
                            if (($it['rjObatDtl'] ?? null) == $rjobat_dtl) {
                                $it['qty']           = $payload['qty'];
                                $it['signaX']        = $payload['signaX'];
                                $it['signaHari']     = $payload['signaHari'];
                                $it['catatanKhusus'] = $payload['catatanKhusus'];
                                break;
                            }
                        }
                        unset($it);
                    }
                });
            });

            $this->store();
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addSuccess('Resep diperbarui.');
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError($e->getMessage() ?: 'Gagal memperbarui obat.');
            return;
        }
    }

    public function removeProduct($rjObatDtl)
    {
        if (!$this->checkUgdStatus()) return;

        $rjNo = $this->rjNoRef;
        $lockKey = "ugd:{$rjNo}";

        try {
            Cache::lock($lockKey, 5)->block(3, function () use ($rjObatDtl) {
                DB::transaction(function () use ($rjObatDtl) {
                    $deleted = DB::table('rstxn_ugdobats')
                        ->where('rjobat_dtl', $rjObatDtl)
                        ->delete();

                    if (!$deleted) {
                        throw new \RuntimeException('Data obat tidak ditemukan atau sudah dihapus.');
                    }

                    $this->dataDaftarUgd['eresep'] = collect($this->dataDaftarUgd['eresep'] ?? [])
                        ->reject(fn($i) => (string)($i['rjObatDtl'] ?? '') === (string)$rjObatDtl)
                        ->values()->all();
                });
            });

            $this->store();
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addSuccess('Obat dihapus.');
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError($e->getMessage() ?: 'Gagal menghapus obat.');
            return;
        }
    }

    public function resetcollectingMyProduct()
    {
        $this->reset(['collectingMyProduct']);
    }

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
        // set data dokter ref
    }

    // select data start////////////////
    public function render()
    {
        return view('livewire.emr-u-g-d.eresep-u-g-d.eresep-u-g-d', [
            // 'RJpasiens' => $query->paginate($this->limitPerPage),
            'myTitle' => 'Data Pasien Rawat Jalan',
            'mySnipt' => 'Rekam Medis Pasien',
            'myProgram' => 'Eresep',
        ]);
    }
    // select data end////////////////
}
