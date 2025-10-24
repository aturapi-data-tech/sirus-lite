<?php

namespace App\Http\Livewire\EmrUGD\EresepUGD;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Cache\LockTimeoutException;

use Livewire\Component;

use App\Http\Traits\customErrorMessagesTrait;
use App\Http\Traits\EmrUGD\EmrUGDTrait;

use Illuminate\Support\Facades\Validator;



class EresepUGDRacikan extends Component
{
    use  EmrUGDTrait;


    // listener from blade////////////////
    protected $listeners = [
        'storeAssessmentDokterUGD' => 'store'
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

    public array $dataProductLov = [];
    public int $dataProductLovStatus = 0;
    public $dataProductLovSearch = '';
    public int $selecteddataProductLovIndex = 0;

    public array $collectingMyProduct = [];

    public string $noRacikan = 'R1';

    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////


    public function clickdataProductLov()
    {
        $this->dataProductLovStatus = true;
        $this->dataProductLov = [];
    }

    public function updatedDataProductLovSearch()
    {
        $this->reset(['selecteddataProductLovIndex', 'dataProductLov']);
        $search = $this->dataProductLovSearch;

        // exact product_id
        $row = DB::table('immst_products')
            ->select(
                'product_id',
                'product_name',
                'sales_price',
                DB::raw("(select replace(string_agg(cont_desc),',','')||product_name
                           from immst_productcontents z, immst_contents x
                          where z.product_id=immst_products.product_id
                            and z.cont_id=x.cont_id) as elasticSearch"),
                DB::raw("(select string_agg(cont_desc)
                           from immst_productcontents z, immst_contents x
                          where z.product_id=immst_products.product_id
                            and z.cont_id=x.cont_id) as product_content")
            )
            ->where('active_status', '1')
            ->where('product_id', $search)
            ->first();

        if ($row) {
            $this->addProduct($row->product_id, $row->product_name, $row->sales_price);
            $this->resetdataProductLov();
            return;
        }

        if (strlen($search) < 3) {
            $this->dataProductLov = [];
        } else {
            $res = DB::select(
                "select * from (
                    select a.product_id,
                           a.product_name,
                           a.sales_price,

                           (select replace(string_agg(cont_desc),',','')||a.product_name
                              from immst_productcontents z, immst_contents x
                             where z.product_id=a.product_id
                               and z.cont_id=x.cont_id) elasticsearch,

                           (select string_agg(cont_desc)
                              from immst_productcontents z, immst_contents x
                             where z.product_id=a.product_id
                               and z.cont_id=x.cont_id) product_content

                      from immst_products a
                     where a.active_status='1'
                     group by a.product_id, a.product_name, a.sales_price
                     order by a.product_name
                )
                where upper(elasticsearch) like '%'||:search||'%'
                ",
                ['search' => strtoupper($search)],
            );
            $this->dataProductLov = json_decode(json_encode($res, true), true);
        }

        $this->dataProductLovStatus = true;
    }

    public function setMydataProductLov($id)
    {
        if (!$this->checkUgdStatus()) return;

        $pid = $this->dataProductLov[$id]['product_id'] ?? null;
        if (!$pid) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Produk tidak valid.');
            return;
        }

        $row = DB::table('immst_products')
            ->select('product_id', 'product_name', 'sales_price')
            ->where('active_status', '1')
            ->where('product_id', $pid)
            ->first();

        if (!$row) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Produk tidak ditemukan.');
            return;
        }

        $this->addProduct($row->product_id, $row->product_name, $row->sales_price);
        $this->resetdataProductLov();
    }

    public function resetdataProductLov()
    {
        $this->reset(['dataProductLov', 'dataProductLovStatus', 'dataProductLovSearch', 'selecteddataProductLovIndex']);
    }

    public function selectNextdataProductLov()
    {
        $this->selecteddataProductLovIndex = $this->selecteddataProductLovIndex === ''
            ? 0 : $this->selecteddataProductLovIndex + 1;

        if ($this->selecteddataProductLovIndex >= count($this->dataProductLov)) {
            $this->selecteddataProductLovIndex = 0;
        }
    }

    public function selectPreviousdataProductLov()
    {
        $this->selecteddataProductLovIndex = $this->selecteddataProductLovIndex === ''
            ? (count($this->dataProductLov) - 1) : $this->selecteddataProductLovIndex - 1;

        if ($this->selecteddataProductLovIndex < 0) {
            $this->selecteddataProductLovIndex = count($this->dataProductLov) - 1;
        }
    }

    public function enterMydataProductLov($id)
    {
        if (!$this->checkUgdStatus()) return;

        if (isset($this->dataProductLov[$id]['product_id'])) {
            $this->addProduct(
                $this->dataProductLov[$id]['product_id'],
                $this->dataProductLov[$id]['product_name'],
                $this->dataProductLov[$id]['sales_price']
            );
            $this->resetdataProductLov();
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Data Obat belum tersedia.');
        }
    }

    /* ============================== STORE (patch subtree) ============================== */

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
                    // ambil fresh supaya subtree lain aman
                    $fresh = $this->findDataUGD($rjNo) ?: [];

                    if (!isset($fresh['eresepRacikan']) || !is_array($fresh['eresepRacikan'])) {
                        $fresh['eresepRacikan'] = [];
                    }

                    // patch hanya bagian racikan
                    $fresh['eresepRacikan'] = array_values((array)($this->dataDaftarUgd['eresepRacikan'] ?? []));

                    // commit json
                    $this->updateJsonUGD($rjNo, $fresh);

                    // sinkron ke state lokal
                    $this->dataDaftarUgd = $fresh;
                });
            });

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addSuccess('Eresep Racikan berhasil disimpan.');
        } catch (LockTimeoutException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Sistem sibuk, gagal memperoleh lock. Coba lagi.');
            return;
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Gagal menyimpan eresep racikan.');
            return;
        }
    }

    /* ============================== CRUD Racikan ============================== */

    private function addProduct($productId, $productName, $salesPrice): void
    {
        $this->collectingMyProduct = [
            'productId'       => $productId,
            'productName'     => $productName,
            'jenisKeterangan' => 'Racikan',
            'signaX'          => 1,
            'signaHari'       => 1,
            'qty'             => null,
            'sedia'           => 1,
            'dosis'           => '',
            'productPrice'    => $salesPrice,
            'catatanKhusus'   => '',
            'catatan'         => '',
        ];
    }

    public function insertProduct(): void
    {
        if (!$this->checkUgdStatus()) return;

        $messages = customErrorMessagesTrait::messages();
        $rules = [
            "collectingMyProduct.productName"   => 'bail|required',
            "collectingMyProduct.dosis"         => 'bail|required|max:150',
            "collectingMyProduct.sedia"         => 'bail|nullable|max:150',
            "collectingMyProduct.qty"           => 'bail|nullable|digits_between:1,3|numeric|min:1',
            "collectingMyProduct.productPrice"  => 'bail|nullable|numeric|min:0',
            "collectingMyProduct.catatanKhusus" => 'bail|nullable|max:150',
            "collectingMyProduct.catatan"       => 'bail|nullable|max:150',
        ];
        $this->validate($rules, $messages);

        $rjNo = $this->rjNoRef;
        $lockKey = "ugd:{$rjNo}";

        try {
            Cache::lock($lockKey, 5)->block(3, function () use ($rjNo) {
                DB::transaction(function () use ($rjNo) {
                    // next detail (aman)
                    $max = DB::table('rstxn_ugdobatracikans')
                        ->max(DB::raw('nvl(to_number(rjobat_dtl),0)'));
                    $nextDtl = ((int)$max) + 1;

                    $productTakar = null;
                    if (!empty($this->collectingMyProduct['productId'])) {
                        $productTakar = DB::table('immst_products')
                            ->where('product_id', $this->collectingMyProduct['productId'])
                            ->value('takar');
                    }
                    $ugdTakar = $productTakar ?: 'Tablet';

                    DB::table('rstxn_ugdobatracikans')->insert([
                        'rjobat_dtl'     => $nextDtl,
                        'rj_no'          => $rjNo,
                        'product_name'   => $this->collectingMyProduct['productName'],
                        'sedia'          => $this->collectingMyProduct['sedia'],
                        'dosis'          => $this->collectingMyProduct['dosis'],
                        'qty'            => $this->collectingMyProduct['qty'],
                        'catatan'        => $this->collectingMyProduct['catatan'] ?: null,
                        'catatan_khusus' => $this->collectingMyProduct['catatanKhusus'] ?: null,
                        'no_racikan'     => $this->noRacikan,
                        'ugd_takar'      => $ugdTakar,
                        'exp_date'       => DB::raw("to_date('" . $this->dataDaftarUgd['rjDate'] . "','dd/mm/yyyy hh24:mi:ss')+30"),
                        'etiket_status'  => 1,
                    ]);

                    // patch lokal (UI instant)
                    $this->dataDaftarUgd['eresepRacikan'][] = [
                        'jenisKeterangan' => 'Racikan',
                        'productName'     => $this->collectingMyProduct['productName'],
                        'sedia'           => $this->collectingMyProduct['sedia'],
                        'dosis'           => $this->collectingMyProduct['dosis'],
                        'catatan'         => $this->collectingMyProduct['catatan'] ?: '',
                        'qty'             => $this->collectingMyProduct['qty'] ?: '',
                        'catatanKhusus'   => $this->collectingMyProduct['catatanKhusus'] ?: '',
                        'noRacikan'       => $this->noRacikan,
                        'signaX'          => 1,
                        'signaHari'       => 1,
                        'productPrice'    => 0,
                        'rjObatDtl'       => $nextDtl,
                        'rjNo'            => $rjNo,
                    ];
                });
            });

            $this->store();
            $this->reset(['collectingMyProduct']);
        } catch (LockTimeoutException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Sistem sibuk, gagal memperoleh lock. Coba lagi.');
            return;
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Gagal menambahkan racikan.');
            return;
        }
    }

    public function updateProduct($rjobat_dtl, $dosis = null, $qty = null, $catatan = null, $catatanKhusus = null): void
    {
        if (!$this->checkUgdStatus()) return;

        $payload = [
            'qty'           => $qty,
            'dosis'         => $dosis,
            'catatan'       => $catatan,
            'catatanKhusus' => $catatanKhusus,
        ];

        $messages = customErrorMessagesTrait::messages();
        $rules = [
            "qty"           => 'bail|nullable|digits_between:1,3|numeric|min:1',
            "dosis"         => 'bail|required|max:150',
            "catatan"       => 'bail|nullable|max:150',
            "catatanKhusus" => 'bail|nullable|max:150',
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
                    $affected = DB::table('rstxn_ugdobatracikans')
                        ->where('rjobat_dtl', $rjobat_dtl)
                        ->update([
                            'qty'            => $payload['qty'],
                            'dosis'          => $payload['dosis'],
                            'catatan'        => $payload['catatan'],
                            'catatan_khusus' => $payload['catatanKhusus'],
                        ]);

                    if (!$affected) {
                        throw new \RuntimeException('Data racikan tidak ditemukan.');
                    }

                    // patch lokal
                    if (!empty($this->dataDaftarUgd['eresepRacikan'])) {
                        foreach ($this->dataDaftarUgd['eresepRacikan'] as &$it) {
                            if (($it['rjObatDtl'] ?? null) == $rjobat_dtl) {
                                $it['qty']           = $payload['qty'];
                                $it['dosis']         = $payload['dosis'];
                                $it['catatan']       = $payload['catatan'];
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
                ->addSuccess('Racikan diperbarui.');
        } catch (LockTimeoutException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Sistem sibuk, gagal memperoleh lock. Coba lagi.');
            return;
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError($e->getMessage() ?: 'Gagal memperbarui racikan.');
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
                    $deleted = DB::table('rstxn_ugdobatracikans')
                        ->where('rjobat_dtl', $rjObatDtl)
                        ->delete();

                    if (!$deleted) {
                        throw new \RuntimeException('Data racikan tidak ditemukan atau sudah dihapus.');
                    }

                    $this->dataDaftarUgd['eresepRacikan'] = collect($this->dataDaftarUgd['eresepRacikan'] ?? [])
                        ->reject(fn($i) => (string)($i['rjObatDtl'] ?? '') === (string)$rjObatDtl)
                        ->values()->all();
                });
            });

            $this->store();
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addSuccess('Racikan dihapus.');
        } catch (LockTimeoutException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Sistem sibuk, gagal memperoleh lock. Coba lagi.');
            return;
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError($e->getMessage() ?: 'Gagal menghapus racikan.');
            return;
        }
    }

    /* ============================== Utilities ============================== */

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

    private function findData($rjno): void
    {
        $row = DB::table('rstxn_ugdhdrs')->select('rj_status')->where('rj_no', $rjno)->first();
        $this->rjStatusRef = $row->rj_status ?? '';

        $this->dataDaftarUgd = $this->findDataUGD($rjno) ?: [];
        if (!isset($this->dataDaftarUgd['eresepRacikan'])) {
            $this->dataDaftarUgd['eresepRacikan'] = [];
        }
    }

    public function mount()
    {
        $this->findData($this->rjNoRef);
    }

    public function render()
    {
        return view('livewire.emr-u-g-d.eresep-u-g-d.eresep-u-g-d-racikan', [
            'myTitle'   => 'Data Pasien Rawat Jalan',
            'mySnipt'   => 'Rekam Medis Pasien',
            'myProgram' => 'Eresep Racikan',
        ]);
    }
}
