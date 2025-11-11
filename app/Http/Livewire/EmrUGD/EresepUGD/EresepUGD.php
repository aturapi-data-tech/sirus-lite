<?php

namespace App\Http\Livewire\EmrUGD\EresepUGD;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Cache\LockTimeoutException;
use App\Http\Traits\EmrUGD\EmrUGDTrait;
use App\Http\Traits\LOV\LOVProduct\LOVProductTrait;
use Illuminate\Support\Facades\Validator;

class EresepUGD extends Component
{
    use  EmrUGDTrait, LOVProductTrait;


    //////////////////////////////
    // Ref on top bar
    //////////////////////////////
    public $rjNoRef;
    public string $rjStatusRef;

    // dataDaftarUgd RJ
    public array $dataDaftarUgd = [];


    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////



    // insert and update record start////////////////
    public function store()
    {
        if (!$this->checkUgdStatus($this->rjNoRef)) return;

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
                    $fresh = $this->findDataUGD($rjNo) ?: [];
                    if (!isset($fresh['eresep']) || !is_array($fresh['eresep'])) {
                        $fresh['eresep'] = [];
                    }
                    $fresh['eresep'] = array_values((array)($this->dataDaftarUgd['eresep'] ?? []));
                    $this->updateJsonUGD($rjNo, $fresh);
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

        if (isset($this->dataDaftarUgd['eresep']) == false) {
            $this->dataDaftarUgd['eresep'] = [];
        }
    }

    private function syncLovToForm(): void
    {
        // Jika trait sudah memilih produk (punya ProductId)
        if (!empty($this->collectingMyProduct['ProductId'] ?? null)) {
            $pid   = (string) ($this->collectingMyProduct['ProductId']   ?? '');
            $pname = (string) ($this->collectingMyProduct['ProductName'] ?? '');
            $price = (float)  ($this->collectingMyProduct['ProductPrice'] ?? 0);

            // Bentuk ulang struktur form yang dipakai metode insert UGD
            $this->collectingMyProduct = [
                'productId'      => $pid,
                'productName'    => $pname,
                'jenisKeterangan' => 'NonRacikan',
                'signaX'         => $this->collectingMyProduct['signaX'] ?? '',
                'signaHari'      => $this->collectingMyProduct['signaHari'] ?? '',
                'qty'            => $this->collectingMyProduct['qty'] ?? '',
                'productPrice'   => $price,
                'catatanKhusus'  => $this->collectingMyProduct['catatanKhusus'] ?? '',
            ];
        }
    }



    public function insertProduct(): void
    {
        // pastikan hasil LOV (jika ada) sudah disinkronkan jadi struktur form UGD
        $this->syncLovToForm();

        $messages = [
            'required'        => ':attribute wajib diisi.',
            'numeric'         => ':attribute harus berupa angka.',
            'digits_between'  => ':attribute harus memiliki panjang antara :min dan :max digit.',
            'digits'          => ':attribute harus terdiri dari :digits digit.',
            'max'             => ':attribute tidak boleh lebih dari :max karakter.',
            'min'             => ':attribute minimal bernilai :min.',
        ];

        $attributes = [
            'collectingMyProduct.productId'     => 'Obat',
            'collectingMyProduct.productName'   => 'Nama Obat',
            'collectingMyProduct.signaX'        => 'Signa X',
            'collectingMyProduct.signaHari'     => 'Signa Hari',
            'collectingMyProduct.qty'           => 'Jumlah',
            'collectingMyProduct.productPrice'  => 'Harga Obat',
            'collectingMyProduct.catatanKhusus' => 'Catatan Khusus',
        ];

        $rules = [
            'collectingMyProduct.productId'     => 'bail|required|exists:immst_products,product_id',
            'collectingMyProduct.productName'   => 'bail|required',
            'collectingMyProduct.signaX'        => 'bail|required|max:25',
            'collectingMyProduct.signaHari'     => 'bail|required|max:25',
            'collectingMyProduct.qty'           => 'bail|required|digits_between:1,3|numeric|min:1',
            'collectingMyProduct.productPrice'  => 'bail|required|numeric|min:0',
            'collectingMyProduct.catatanKhusus' => 'bail|nullable',
        ];

        $this->validate($rules, $messages, $attributes);

        if (!$this->checkUgdStatus($this->rjNoRef)) return;

        $rjNo = $this->rjNoRef;
        $lockKey = "ugd:{$rjNo}";

        try {
            Cache::lock($lockKey, 5)->block(3, function () use ($rjNo) {
                DB::transaction(function () use ($rjNo) {
                    $max = DB::table('rstxn_ugdobats')->max(DB::raw('nvl(to_number(rjobat_dtl),0)'));
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
            $this->reset(['collectingMyProduct']); // reset form
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Gagal menambahkan obat.');
            return;
        }
    }

    public function updateProductRow(int $key): void
    {
        $row = $this->dataDaftarUgd['eresep'][$key] ?? null;
        if (!$row) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Baris resep tidak ditemukan.');
            return;
        }

        $this->updateProduct(
            $row['rjObatDtl']    ?? null,
            $row['qty']          ?? null,
            $row['signaX']       ?? null,
            $row['signaHari']    ?? null,
            $row['catatanKhusus'] ?? null
        );
    }
    public function updateProduct($rjobat_dtl, $qty = null, $signaX = null, $signaHari = null, $catatanKhusus = null): void
    {
        if (!$this->checkUgdStatus($this->rjNoRef)) return;

        $payload = [
            'qty'           => $qty,
            'signaX'        => $signaX,
            'signaHari'     => $signaHari,
            'catatanKhusus' => $catatanKhusus,
        ];

        $messages = [
            'qty.required'          => 'Qty wajib diisi.',
            'qty.digits_between'    => 'Qty harus 1-3 digit.',
            'qty.numeric'           => 'Qty harus berupa angka.',
            'qty.min'               => 'Qty minimal 1.',

            'signaX.required'       => 'Signa X wajib diisi.',
            'signaX.regex'          => 'Format Signa X tidak valid.',

            'signaHari.required'    => 'Signa Hari wajib diisi.',
            'signaHari.regex'       => 'Format Signa Hari tidak valid.',

            // optional
            'catatanKhusus.max'     => 'Catatan khusus maksimal 200 karakter.',
        ];

        $attributes = [
            'qty'           => 'jumlah obat',
            'signaX'        => 'signa',
            'signaHari'     => 'signa',
            'catatanKhusus' => 'catatan khusus',
        ];

        $rules = [
            'qty'           => 'bail|required|digits_between:1,3|numeric|min:1',

            'signaX'        => [
                'bail',
                'required',
                'string',
                'min:1',
                'max:30',
                'regex:/^[\pL\pN\s\.\,\+\-\/\(\)]+$/u'
            ],

            'signaHari'     => [
                'bail',
                'required',
                'string',
                'min:1',
                'max:30',
                'regex:/^[\pL\pN\s\.\,\+\-\/\(\)]+$/u'
            ],

            'catatanKhusus' => 'bail|nullable|string|max:200',
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
        if (!$this->checkUgdStatus($this->rjNoRef)) return;

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



    // when new form instance
    public function mount()
    {

        $this->findData($this->rjNoRef);
        // set data dokter ref
    }

    // select data start////////////////
    public function render()
    {
        $this->syncLovToForm();
        return view('livewire.emr-u-g-d.eresep-u-g-d.eresep-u-g-d', [
            // 'RJpasiens' => $query->paginate($this->limitPerPage),
            'myTitle' => 'Data Pasien Rawat Jalan',
            'mySnipt' => 'Rekam Medis Pasien',
            'myProgram' => 'Eresep',
        ]);
    }
    // select data end////////////////
}
