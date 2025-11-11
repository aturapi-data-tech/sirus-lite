<?php

namespace App\Http\Livewire\EmrUGD\EresepUGD;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Livewire\Component;
use App\Http\Traits\EmrUGD\EmrUGDTrait;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\LOV\LOVProduct\LOVProductTrait;


class EresepUGDRacikan extends Component
{
    use  EmrUGDTrait, LOVProductTrait;


    //////////////////////////////
    // Ref on top bar
    //////////////////////////////
    public $rjNoRef;
    public string $rjStatusRef;



    // dataDaftarUgd RJ
    public array $dataDaftarUgd = [];

    //////////////////////////////////////////////////////////////////////


    //  table LOV////////////////



    public string $noRacikan = 'R1';

    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////




    /* ============================== STORE (patch subtree) ============================== */

    public function store()
    {
        if (!$this->checkUgdStatus($this->rjNoRef)) return;

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

        $this->syncLovToForm();

        $rules = [
            'collectingMyProduct.productId'     => 'bail|required|exists:immst_products,product_id',
            'collectingMyProduct.productName'   => 'bail|required',
            'collectingMyProduct.dosis'         => 'bail|required|max:150',
            'collectingMyProduct.sedia'         => 'bail|nullable|max:150',
            'collectingMyProduct.qty'           => 'bail|nullable|digits_between:1,3|numeric|min:1',
            'collectingMyProduct.productPrice'  => 'bail|nullable|numeric|min:0',
            'collectingMyProduct.catatanKhusus' => 'bail|nullable|max:150',
            'collectingMyProduct.catatan'       => 'bail|nullable|max:150',
        ];

        $messages = [
            // Pesan umum
            'required'        => ':attribute wajib diisi.',
            'numeric'         => ':attribute harus berupa angka.',
            'digits_between'  => ':attribute harus terdiri dari :min sampai :max digit.',
            'min'             => ':attribute minimal bernilai :min.',
            'max'             => ':attribute maksimal :max karakter.',

            // Pesan khusus per field
            'collectingMyProduct.productName.required'   => 'Nama obat harus dipilih dari daftar.',
            'collectingMyProduct.dosis.required'         => 'Dosis racikan wajib diisi.',
            'collectingMyProduct.sedia.string'           => 'Sediaan harus berupa teks.',
            'collectingMyProduct.sedia.max'              => 'Sediaan maksimal :max karakter.',
            'collectingMyProduct.qty.numeric'            => 'Jumlah racikan harus berupa angka.',
            'collectingMyProduct.qty.min'                => 'Jumlah racikan minimal 1.',
            'collectingMyProduct.qty.digits_between'     => 'Jumlah racikan maksimal 3 digit.',
            'collectingMyProduct.productPrice.numeric'   => 'Harga produk harus berupa angka.',
            'collectingMyProduct.productPrice.min'       => 'Harga produk tidak boleh bernilai negatif.',
            'collectingMyProduct.catatanKhusus.max'      => 'Signa maksimal :max karakter.',
            'collectingMyProduct.catatan.max'            => 'Catatan maksimal :max karakter.',
        ];

        $attributes = [
            'collectingMyProduct.productId'     => 'Obat',
            'collectingMyProduct.productName'   => 'Nama Obat',
            'collectingMyProduct.dosis'         => 'Dosis',
            'collectingMyProduct.sedia'         => 'Sediaan',
            'collectingMyProduct.qty'           => 'Jumlah Racikan',
            'collectingMyProduct.productPrice'  => 'Harga Obat',
            'collectingMyProduct.catatanKhusus' => 'Signa',
            'collectingMyProduct.catatan'       => 'Catatan',
        ];


        $this->validate($rules, $messages, $attributes);

        if (!$this->checkUgdStatus($this->rjNoRef)) return;

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

    public function updateProductRow(int $key): void
    {
        $row = $this->dataDaftarUgd['eresepRacikan'][$key] ?? null;
        if (!$row) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Baris racikan tidak ditemukan.');
            return;
        }

        $this->updateProduct(
            $row['rjObatDtl']     ?? null,
            $row['dosis']         ?? null,
            $row['qty']           ?? null,
            $row['catatan']       ?? null,
            $row['catatanKhusus'] ?? null
        );
    }


    public function updateProduct($rjobat_dtl, $dosis = null, $qty = null, $catatan = null, $catatanKhusus = null): void
    {
        if (!$this->checkUgdStatus($this->rjNoRef)) return;

        $payload = [
            'qty'           => $qty,
            'dosis'         => $dosis,
            'catatan'       => $catatan,
            'catatanKhusus' => $catatanKhusus,
        ];

        $rules = [
            'qty'           => 'bail|nullable|digits_between:1,3|numeric|min:1',
            'dosis'         => 'bail|required|max:150',
            'catatan'       => 'bail|nullable|max:150',
            'catatanKhusus' => 'bail|nullable|max:150',
        ];

        $messages = [
            // umum
            'required'        => ':attribute wajib diisi.',
            'numeric'         => ':attribute harus berupa angka.',
            'digits_between'  => ':attribute harus terdiri dari :min sampai :max digit.',
            'min'             => ':attribute minimal bernilai :min.',
            'max'             => ':attribute maksimal :max karakter.',

            // khusus per field (opsional)
            'qty.numeric'        => 'Jumlah racikan harus berupa angka.',
            'qty.min'            => 'Jumlah racikan minimal 1.',
            'qty.digits_between' => 'Jumlah racikan maksimal 3 digit.',
            'dosis.required'     => 'Dosis wajib diisi.',
        ];

        $attributes = [
            'qty'           => 'Jumlah Racikan',
            'dosis'         => 'Dosis',
            'catatan'       => 'Catatan',
            'catatanKhusus' => 'Signa',
        ];

        $validator = Validator::make($payload, $rules, $messages, $attributes);

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
        if (!$this->checkUgdStatus($this->rjNoRef)) return;

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

    private function syncLovToForm(): void
    {
        if (!empty($this->collectingMyProduct['ProductId'] ?? null)) {
            $pid   = (string)($this->collectingMyProduct['ProductId'] ?? '');
            $pname = (string)($this->collectingMyProduct['ProductName'] ?? '');
            $price = (float) ($this->collectingMyProduct['ProductPrice'] ?? 0);

            $this->collectingMyProduct = [
                'productId'       => $pid,
                'productName'     => $pname,
                'jenisKeterangan' => 'Racikan',
                'signaX'          => 1,
                'signaHari'       => 1,
                'qty'             => $this->collectingMyProduct['qty'] ?? null,
                'sedia'           => (string)($this->collectingMyProduct['sedia'] ?? ''), // jadi string
                'dosis'           => $this->collectingMyProduct['dosis'] ?? '',
                'productPrice'    => $price,
                'catatanKhusus'   => $this->collectingMyProduct['catatanKhusus'] ?? '',
                'catatan'         => $this->collectingMyProduct['catatan'] ?? '',
            ];
        }
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

    public function resetcollectingMyProduct()
    {
        $this->reset(['collectingMyProduct']);
    }

    public function mount()
    {
        $this->findData($this->rjNoRef);
    }

    public function render()
    {

        $this->syncLovToForm();

        return view('livewire.emr-u-g-d.eresep-u-g-d.eresep-u-g-d-racikan', [
            'myTitle'   => 'Data Pasien Rawat Jalan',
            'mySnipt'   => 'Rekam Medis Pasien',
            'myProgram' => 'Eresep Racikan',
        ]);
    }
}
