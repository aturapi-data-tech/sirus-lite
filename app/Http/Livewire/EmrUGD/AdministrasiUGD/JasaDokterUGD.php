<?php

namespace App\Http\Livewire\EmrUGD\AdministrasiUGD;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Cache\LockTimeoutException;

use App\Http\Traits\customErrorMessagesTrait;
use App\Http\Traits\EmrUGD\EmrUGDTrait;
use App\Http\Traits\LOV\LOVDokter\LOVDokterTrait;
use App\Http\Traits\LOV\LOVJasaDokter\LOVJasaDokterTrait;


use Exception;


class JasaDokterUGD extends Component
{
    use WithPagination, EmrUGDTrait, LOVDokterTrait, LOVJasaDokterTrait;


    // listener from blade////////////////
    protected $listeners = [
        'storeAssessmentDokterUGD' => 'store',
    ];


    //////////////////////////////
    // Ref on top bar
    //////////////////////////////
    public $rjNoRef;



    // dataDaftarUgd RJ
    public array $dataDaftarUgd = [];

    public array $formEntryJasaDokter = [
        'drId' => '',
        'jasaDokterId' => '',
        'jasaDokterPrice' => '', // Harga kunjungan minimal 0
    ];
    //////////////////////////////////////////////////////////////////////


    //  table LOV////////////////
    // LOV Nested
    public array $dokter;
    public array $jasaDokter;

    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////
    public function store()
    {
        // pastikan RJ aktif
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
                    // FRESH dari DB agar tidak menimpa subtree lain
                    $fresh = $this->findDataUGD($rjNo) ?: [];

                    // pastikan subtree ada
                    if (!isset($fresh['JasaDokter']) || !is_array($fresh['JasaDokter'])) {
                        $fresh['JasaDokter'] = [];
                    }
                    if (!isset($fresh['LainLain']) || !is_array($fresh['LainLain'])) {
                        $fresh['LainLain'] = [];
                    }


                    // PATCH hanya bagian yang dikelola modul ini
                    $fresh['JasaDokter'] = array_values($this->dataDaftarUgd['JasaDokter'] ?? []);
                    $fresh['LainLain']   = array_values($this->dataDaftarUgd['LainLain'] ?? []);

                    // commit JSON
                    $this->updateJsonUGD($rjNo, $fresh);
                    $this->emit('ugd:refresh-summary');

                    // sinkronkan state lokal
                    $this->dataDaftarUgd = $fresh;
                });
            });

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Jasa Dokter berhasil disimpan.");
        } catch (LockTimeoutException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Sistem sibuk, gagal memperoleh lock. Coba lagi.');
            return;
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Gagal menyimpan data.');
            return;
        }
    }

    // insert and update record end////////////////


    private function findData($rjno): void
    {
        $findDataUGD = $this->findDataUGD($rjno);
        $this->dataDaftarUgd  = $findDataUGD;

        // jika JasaDokter tidak ditemukan tambah variable JasaDokter pda array
        if (isset($this->dataDaftarUgd['JasaDokter']) == false) {
            $this->dataDaftarUgd['JasaDokter'] = [];
        }
    }

    public function insertJasaDokter(): void
    {
        if (!$this->checkUgdStatus()) return;

        // Validasi
        $messages = [
            'formEntryJasaDokter.jasaDokterId.required'    => 'ID jasa dokter harus diisi.',
            'formEntryJasaDokter.jasaDokterId.exists'      => 'ID jasa dokter tidak valid.',
            'formEntryJasaDokter.jasaDokterPrice.required' => 'Harga jasa dokter harus diisi.',
            'formEntryJasaDokter.jasaDokterPrice.numeric'  => 'Harga jasa dokter harus berupa angka.',
            'formEntryJasaDokter.drId.exists'              => 'ID dokter tidak valid.',
        ];
        $rules = [
            'formEntryJasaDokter.jasaDokterId'    => 'bail|required|exists:rsmst_accdocs,accdoc_id',
            'formEntryJasaDokter.jasaDokterPrice' => 'bail|required|numeric',
            'formEntryJasaDokter.drId'            => 'bail|nullable|exists:rsmst_doctors,dr_id',
        ];
        $this->validate($rules, $messages);

        $rjNo = $this->rjNoRef;
        $lockKey = "ugd:{$rjNo}";
        $lockAccdoc = "ugdaccdocs:counter";

        try {
            Cache::lock($lockKey, 5)->block(3, function () use ($rjNo, $lockAccdoc) {
                Cache::lock($lockAccdoc, 5)->block(3, function () use ($rjNo) {
                    DB::transaction(function () use ($rjNo) {
                        // 1) HEADER JASA DOKTER (ambil counter terkunci)
                        $nextAccDtl = (int) DB::table('rstxn_ugdaccdocs')
                            ->max(DB::raw('nvl(to_number(rjhn_dtl),0)')) + 1;

                        DB::table('rstxn_ugdaccdocs')->insert([
                            'dr_id'        => $this->formEntryJasaDokter['drId'] ?: null,
                            'rjhn_dtl'     => $nextAccDtl,
                            'rj_no'        => $rjNo,
                            'accdoc_id'    => $this->formEntryJasaDokter['jasaDokterId'],
                            'accdoc_price' => $this->formEntryJasaDokter['jasaDokterPrice'],
                        ]);

                        // Patch state lokal (untuk UI)
                        $this->dataDaftarUgd['JasaDokter'][] = [
                            'DokterId'        => $this->formEntryJasaDokter['drId'] ?? null,
                            'DokterName'      => $this->formEntryJasaDokter['drName'] ?? null,
                            'JasaDokterId'    => $this->formEntryJasaDokter['jasaDokterId'],
                            'JasaDokterDesc'  => $this->formEntryJasaDokter['jasaDokterDesc'] ?? '',
                            'JasaDokterPrice' => $this->formEntryJasaDokter['jasaDokterPrice'],
                            'rjaccdocDtl'     => $nextAccDtl,
                            'rjNo'            => $rjNo,
                            'userLog'         => auth()->user()->myuser_name ?? '',
                            'userLogDate'     => \Carbon\Carbon::now(config('app.timezone'))->format('d/m/Y H:i:s'),
                        ];

                        // 2) LAIN-LAIN (batch)
                        $others = DB::table('rsmst_accdocothers')
                            ->select('other_id', 'accdother_price')
                            ->where('accdoc_id', $this->formEntryJasaDokter['jasaDokterId'])
                            ->orderBy('accdoc_id')
                            ->get();

                        if ($others->isNotEmpty()) {
                            $maxOther = (int) DB::table('rstxn_ugdothers')
                                ->max(DB::raw('nvl(to_number(rjo_dtl),0)'));

                            $toInsertOthers = [];
                            foreach ($others as $o) {
                                $maxOther++;
                                $toInsertOthers[] = [
                                    'rjo_dtl'     => $maxOther,
                                    'rjhn_dtl'    => $nextAccDtl,
                                    'rj_no'       => $rjNo,
                                    'other_id'    => $o->other_id,
                                    'other_price' => $o->accdother_price,
                                ];
                                $this->dataDaftarUgd['LainLain'][] = [
                                    'LainLainId'    => $o->other_id,
                                    'LainLainDesc'  => 'Paket JD',
                                    'LainLainPrice' => $o->accdother_price,
                                    'rjotherDtl'    => $maxOther,
                                    'rjNo'          => $rjNo,
                                    'rjhn_dtl'      => $nextAccDtl,
                                ];
                            }
                            DB::table('rstxn_ugdothers')->insert($toInsertOthers);
                        }

                        // 3) OBAT PAKET (batch)
                        $prods = DB::table('rsmst_accdocproducts as p')
                            ->join('immst_products as m', 'm.product_id', '=', 'p.product_id')
                            ->where('p.accdoc_id', $this->formEntryJasaDokter['jasaDokterId'])
                            ->orderBy('p.accdoc_id')
                            ->get(['m.product_id', 'm.product_name', 'm.sales_price', 'p.accdprod_qty']);

                        if ($prods->isNotEmpty()) {
                            $maxDtl = (int) DB::table('rstxn_ugdobats')
                                ->max(DB::raw('nvl(to_number(rjobat_dtl),0)'));

                            $toInsertDrugs = [];
                            foreach ($prods as $p) {
                                $maxDtl++;
                                $toInsertDrugs[] = [
                                    'rjobat_dtl'     => $maxDtl,
                                    'rjhn_dtl'       => $nextAccDtl,
                                    'rj_no'          => $rjNo,
                                    'product_id'     => $p->product_id,
                                    'qty'            => $p->accdprod_qty,
                                    'price'          => $p->sales_price,
                                    // NOTE: untuk UGD kolomnya biasanya ugd_*; sesuaikan dengan skema kamu
                                    'ugd_carapakai'  => 1,
                                    'ugd_kapsul'     => 1,
                                    'ugd_takar'      => 'Tablet',
                                    'catatan_khusus' => '-',
                                    'exp_date'       => DB::raw("to_date('" . $this->dataDaftarUgd['rjDate'] . "','dd/mm/yyyy hh24:mi:ss')+30"),
                                    'etiket_status'  => 0,
                                ];
                            }
                            DB::table('rstxn_ugdobats')->insert($toInsertDrugs);
                        }

                        // 4) FRESH-MERGE JSON + COMMIT (tanpa memanggil store())
                        $fresh = $this->findDataUGD($rjNo) ?: [];
                        $fresh['JasaDokter'] = array_values($this->dataDaftarUgd['JasaDokter'] ?? ($fresh['JasaDokter'] ?? []));
                        $fresh['LainLain']   = array_values($this->dataDaftarUgd['LainLain']   ?? ($fresh['LainLain']   ?? []));

                        $this->updateJsonUGD($rjNo, $fresh);
                        $this->emit('ugd:refresh-summary');
                        $this->dataDaftarUgd = $fresh;
                    });
                });
            });

            $this->resetformEntryJasaDokter();
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('Jasa Dokter berhasil ditambahkan.');
        } catch (LockTimeoutException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Sistem sibuk, gagal memperoleh lock. Coba lagi.');
            return;
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Gagal menambah Jasa Dokter.');
            return;
        }
    }

    public function resetformEntryJasaDokter()
    {
        $this->reset([
            'formEntryJasaDokter',
            'collectingMyDokter',
            'collectingMyJasaDokter'
        ]);
        $this->resetValidation();
    }


    public function removeJasaDokter($rjaccdocDtl)
    {
        if (!$this->checkUgdStatus()) return;

        $rjNo = $this->rjNoRef;
        $lockKey = "ugd:{$rjNo}";

        try {
            Cache::lock($lockKey, 5)->block(3, function () use ($rjNo, $rjaccdocDtl) {
                DB::transaction(function () use ($rjNo, $rjaccdocDtl) {
                    // 1) Hapus paket lain-lain
                    DB::table('rstxn_ugdothers')
                        ->where('rjhn_dtl', $rjaccdocDtl)
                        ->delete();

                    // 2) Hapus paket obat
                    DB::table('rstxn_ugdobats')
                        ->where('rjhn_dtl', $rjaccdocDtl)
                        ->delete();

                    // 3) Hapus header jasa dokter
                    DB::table('rstxn_ugdaccdocs')
                        ->where('rjhn_dtl', $rjaccdocDtl)
                        ->delete();

                    // 4) Patch state lokal untuk UI
                    $this->dataDaftarUgd['JasaDokter'] = collect($this->dataDaftarUgd['JasaDokter'] ?? [])
                        ->reject(fn($i) => (string)($i['rjaccdocDtl'] ?? '') === (string)$rjaccdocDtl)
                        ->values()->all();

                    $this->dataDaftarUgd['LainLain'] = collect($this->dataDaftarUgd['LainLain'] ?? [])
                        ->reject(fn($i) => (string)($i['rjhn_dtl'] ?? '') === (string)$rjaccdocDtl)
                        ->values()->all();


                    // 5) Fresh-merge JSON dari DB lalu commit
                    $fresh = $this->findDataUGD($rjNo) ?: [];
                    $fresh['JasaDokter'] = array_values($this->dataDaftarUgd['JasaDokter'] ?? ($fresh['JasaDokter'] ?? []));
                    $fresh['LainLain']   = array_values($this->dataDaftarUgd['LainLain']   ?? ($fresh['LainLain']   ?? []));
                    // jika tidak menyentuh subtree lain, biarkan apa adanya (jangan overwrite)

                    $this->updateJsonUGD($rjNo, $fresh);
                    $this->emit('ugd:refresh-summary');
                    $this->dataDaftarUgd = $fresh; // keep UI in sync
                });
            });

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('Jasa Dokter dihapus.');
        } catch (LockTimeoutException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Sistem sibuk, gagal memperoleh lock. Coba lagi.');
            return;
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Gagal menghapus Jasa Dokter.');
            return;
        }
    }



    // Paket JasaDokter -> Lain lain
    private function paketLainLainJasaDokter($accdocId, $rjNo, $accdocDtl): void
    {
        // Loop tetap, tapi biarkan insertLainLain yang pegang lock & tx
        $collection = DB::table('rsmst_accdocothers')
            ->select('other_id', 'accdother_price')
            ->where('accdoc_id', $accdocId)
            ->orderBy('accdoc_id')
            ->get();

        foreach ($collection as $item) {
            $this->insertLainLain($accdocId, $rjNo, $accdocDtl, $item->other_id, 'Paket JD', $item->accdother_price);
        }
    }

    private function insertLainLain($accdocId, $rjNo, $accdocDtl, $otherId, $otherDesc, $otherPrice): void
    {
        // 1) Validasi
        $messages = customErrorMessagesTrait::messages();
        $payload = [
            'LainLainId'    => $otherId,
            'LainLainDesc'  => $otherDesc,
            'LainLainPrice' => $otherPrice,
            'accdocId'      => $accdocId,
            'accdocDtl'     => $accdocDtl,
            'rjNo'          => $rjNo,
        ];
        $rules = [
            'LainLainId'    => 'bail|required|exists:rsmst_others,other_id',
            'LainLainDesc'  => 'bail|required',
            'LainLainPrice' => 'bail|required|numeric',
            'accdocId'      => 'bail|required',
            'accdocDtl'     => 'bail|required|numeric',
            'rjNo'          => 'bail|required|numeric',
        ];
        $validator = Validator::make($payload, $rules, $messages);
        if ($validator->fails()) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError($validator->errors()->first());
            return;
        }
        if (!$this->checkUgdStatus()) return;

        // 2) Locks
        $lockKey     = "ugd:{$rjNo}";
        $lockOthers = "ugdothers:counter";

        try {
            Cache::lock($lockKey, 5)->block(3, function () use ($payload, $lockOthers) {
                Cache::lock($lockOthers, 5)->block(3, function () use ($payload) {
                    DB::transaction(function () use ($payload) {
                        // 3) Counter aman (global counter per tabel)
                        $nextDtl = (int) DB::table('rstxn_ugdothers')
                            ->max(DB::raw('nvl(to_number(rjo_dtl),0)')) + 1;

                        // 4) Insert row
                        DB::table('rstxn_ugdothers')->insert([
                            'rjo_dtl'     => $nextDtl,
                            'rjhn_dtl'    => $payload['accdocDtl'],
                            'rj_no'       => $payload['rjNo'],
                            'other_id'    => $payload['LainLainId'],
                            'other_price' => $payload['LainLainPrice'],
                        ]);

                        // 5) Patch state lokal (UI)
                        $this->dataDaftarUgd['LainLain'][] = [
                            'LainLainId'    => $payload['LainLainId'],
                            'LainLainDesc'  => $payload['LainLainDesc'],
                            'LainLainPrice' => $payload['LainLainPrice'],
                            'rjotherDtl'    => $nextDtl,
                            'rjNo'          => $payload['rjNo'],
                            'rjhn_dtl'      => $payload['accdocDtl'],
                        ];

                        // 6) Fresh-merge JSON + commit
                        $fresh = $this->findDataUGD($payload['rjNo']) ?: [];
                        $fresh['LainLain'] = array_values($this->dataDaftarUgd['LainLain'] ?? ($fresh['LainLain'] ?? []));
                        $this->updateJsonUGD($payload['rjNo'], $fresh);
                        $this->emit('ugd:refresh-summary');
                        $this->dataDaftarUgd = $fresh;
                    });
                });
            });

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addSuccess('Item Lain-lain ditambahkan.');
        } catch (\Illuminate\Contracts\Cache\LockTimeoutException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Sistem sibuk, gagal memperoleh lock. Coba lagi.');
            return;
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Gagal menambah Lain-lain.');
            return;
        }
    }

    private function removepaketLainLainJasaDokter($rjaccdocDtl): void
    {
        // Ambil rjNo dari state (method signature tidak diubah)
        $rjNo = $this->rjNoRef;
        $lockKey = "ugd:{$rjNo}";

        try {
            Cache::lock($lockKey, 5)->block(3, function () use ($rjNo, $rjaccdocDtl) {
                DB::transaction(function () use ($rjNo, $rjaccdocDtl) {
                    // Kumpulkan detail yang akan dihapus (untuk patch JSON)
                    $detailIds = DB::table('rstxn_ugdothers')
                        ->where('rj_no', $rjNo)
                        ->where('rjhn_dtl', $rjaccdocDtl)
                        ->pluck('rjo_dtl')
                        ->all();

                    if (!empty($detailIds)) {
                        DB::table('rstxn_ugdothers')
                            ->where('rj_no', $rjNo)
                            ->where('rjhn_dtl', $rjaccdocDtl)
                            ->delete();

                        // Patch state lokal
                        $this->dataDaftarUgd['LainLain'] = collect($this->dataDaftarUgd['LainLain'] ?? [])
                            ->reject(fn($i) => in_array((int)($i['rjhn_dtl'] ?? 0), [$rjaccdocDtl], true))
                            ->values()
                            ->all();

                        // Fresh-merge JSON + commit
                        $fresh = $this->findDataUGD($rjNo) ?: [];
                        $fresh['LainLain'] = array_values($this->dataDaftarUgd['LainLain'] ?? ($fresh['LainLain'] ?? []));
                        $this->updateJsonUGD($rjNo, $fresh);
                        $this->emit('ugd:refresh-summary');
                        $this->dataDaftarUgd = $fresh;
                    }
                });
            });
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Gagal menghapus paket Lain-lain.');
            return;
        }
    }

    private function removeLainLain($rjotherDtl): void
    {
        $rjNo = $this->rjNoRef;
        if (!$this->checkUgdStatus()) return;

        $lockKey = "ugd:{$rjNo}";
        try {
            Cache::lock($lockKey, 5)->block(3, function () use ($rjNo, $rjotherDtl) {
                DB::transaction(function () use ($rjNo, $rjotherDtl) {
                    DB::table('rstxn_ugdothers')
                        ->where('rj_no', $rjNo)         // filter rj_no supaya tidak salah hapus
                        ->where('rjo_dtl', $rjotherDtl)
                        ->delete();

                    $this->dataDaftarUgd['LainLain'] = collect($this->dataDaftarUgd['LainLain'] ?? [])
                        ->reject(fn($i) => (string)($i['rjotherDtl'] ?? '') === (string)$rjotherDtl)
                        ->values()->all();

                    // Fresh-merge JSON + commit
                    $fresh = $this->findDataUGD($rjNo) ?: [];
                    $fresh['LainLain'] = array_values($this->dataDaftarUgd['LainLain'] ?? ($fresh['LainLain'] ?? []));
                    $this->updateJsonUGD($rjNo, $fresh);
                    $this->emit('ugd:refresh-summary');
                    $this->dataDaftarUgd = $fresh;
                });
            });

            // Notifikasi sukses (store sudah “diin-line” lewat updateJsonUGD di atas)
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('Lain-lain dihapus.');
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Gagal menghapus Lain-lain.');
            return;
        }
    }

    // Paket JasaDokter -> Lain lain


    // /////////////////////////////////////////////////////////////////
    // Paket JasaDokter -> Obat
    private function paketObatJasaDokter($accdocId, $rjNo, $accdocDtl): void
    {
        // Loop tetap – insert per item lewat helper yang sudah ber-lock
        $collection = DB::table('rsmst_accdocproducts')
            ->select(
                'immst_products.product_id as product_id',
                'accdoc_id',
                'accdprod_qty',
                'immst_products.product_name as product_name',
                'immst_products.sales_price as sales_price',
            )
            ->where('accdoc_id', $accdocId)
            ->join('immst_products', 'immst_products.product_id', '=', 'rsmst_accdocproducts.product_id')
            ->orderBy('accdoc_id')
            ->get();

        foreach ($collection as $item) {
            $this->insertObat(
                $accdocId,
                $rjNo,
                $accdocDtl,
                $item->product_id,
                'Paket JD' . $item->product_name,
                $item->sales_price,
                $item->accdprod_qty
            );
        }
    }

    private function insertObat($accdocId, $rjNo, $accdocDtl, $ObatId, $ObatDesc, $ObatPrice, $Obatqty): void
    {
        // Validasi
        $messages = customErrorMessagesTrait::messages();
        $payload = [
            "productId"    => $ObatId,
            "productName"  => $ObatDesc,
            "signaX"       => 1,
            "signaHari"    => 1,
            "qty"          => $Obatqty,
            "productPrice" => $ObatPrice,
            "catatanKhusus" => '-',
            "accdocDtl"    => $accdocDtl,
            "accdocId"     => $accdocId,
            "rjNo"         => $rjNo,
        ];
        $rules = [
            "productId"    => 'bail|required|exists:immst_products,product_id',
            "productName"  => 'bail|required',
            "signaX"       => 'bail|required|numeric|min:1|max:5',
            "signaHari"    => 'bail|required|numeric|min:1|max:5',
            "qty"          => 'bail|required|digits_between:1,3',
            "productPrice" => 'bail|required|numeric',
            "catatanKhusus" => 'bail|nullable',
            "accdocDtl"    => 'bail|required|numeric',
            "accdocId"     => 'bail|required|numeric',
            "rjNo"         => 'bail|required|numeric',
        ];
        $validator = Validator::make($payload, $rules, $messages);
        if ($validator->fails()) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError($validator->errors()->first());
            return;
        }
        if (!$this->checkUgdStatus()) return;

        $lockKey = "ugd:{$rjNo}";
        $lockDrug = "ugdobats:counter";
        try {
            Cache::lock($lockKey, 5)->block(3, function () use ($payload, $lockDrug) {
                Cache::lock($lockDrug, 5)->block(3, function () use ($payload) {
                    DB::transaction(function () use ($payload) {
                        $nextDtl = (int) DB::table('rstxn_ugdobats')
                            ->max(DB::raw('nvl(to_number(rjobat_dtl),0)')) + 1;
                        // NOTE: di schema UGD biasanya kolomnya ugd_* (bukan rj_*)
                        DB::table('rstxn_ugdobats')->insert([
                            'rjobat_dtl'     => $nextDtl,
                            'rjhn_dtl'       => $payload['accdocDtl'],
                            'rj_no'          => $payload['rjNo'],
                            'product_id'     => $payload['productId'],
                            'qty'            => $payload['qty'],
                            'price'          => $payload['productPrice'],
                            'ugd_carapakai'  => $payload['signaX'],
                            'ugd_kapsul'     => $payload['signaHari'],
                            'ugd_takar'      => 'Tablet',
                            'catatan_khusus' => $payload['catatanKhusus'],
                            'exp_date'       => DB::raw("to_date('" . ($this->dataDaftarUgd['rjDate'] ?? date('d/m/Y H:i:s')) . "','dd/mm/yyyy hh24:mi:ss')+30"),
                            'etiket_status'  => 0,
                        ]);

                        // Tidak memodifikasi subtree JSON (eresep) di sini -> hindari overwrite data lain.
                        // Jika suatu saat perlu mirror ke JSON, lakukan fresh-merge ala modul lain.
                    });
                });
            });
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Gagal menambah obat paket.');
            return;
        }
    }

    private function removepaketObatJasaDokter($rjaccdocDtl): void
    {
        if (!$this->checkUgdStatus()) return;

        $rjNo = $this->rjNoRef;
        $lockKey = "ugd:{$rjNo}";

        try {
            Cache::lock($lockKey, 5)->block(3, function () use ($rjNo, $rjaccdocDtl) {
                DB::transaction(function () use ($rjNo, $rjaccdocDtl) {
                    DB::table('rstxn_ugdobats')
                        ->where('rj_no', $rjNo)           // filter rj_no untuk keamanan
                        ->where('rjhn_dtl', $rjaccdocDtl)
                        ->delete();

                    // Tidak menyentuh JSON di sini (kecuali kamu memang mirror ke JSON)
                });
            });

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('Paket obat dihapus.');
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Gagal menghapus paket obat.');
            return;
        }
    }

    private function removeObat($rjObatDtl): void
    {
        if (!$this->checkUgdStatus()) return;

        $rjNo = $this->rjNoRef;
        $lockKey = "ugd:{$rjNo}";

        try {
            Cache::lock($lockKey, 5)->block(3, function () use ($rjNo, $rjObatDtl) {
                DB::transaction(function () use ($rjNo, $rjObatDtl) {
                    DB::table('rstxn_ugdobats')
                        ->where('rj_no', $rjNo)           // penting agar tidak salah hapus cross-RJ
                        ->where('rjobat_dtl', $rjObatDtl)
                        ->delete();

                    // Kalau kamu tidak menaruh obat paket ke JSON, tidak perlu patch JSON di sini.
                });
            });

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('Obat dihapus.');
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Gagal menghapus obat.');
            return;
        }
    }
    // Paket JasaDokter -> Obat
    // /////////////////////////////////////////////////////////////////


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

    private function syncDataFormEntry(): void
    {
        $this->formEntryJasaDokter['drId'] = $this->dokter['DokterId'] ?? '';
        $this->formEntryJasaDokter['drName'] = $this->dokter['DokterDesc'] ?? '';

        $this->formEntryJasaDokter['jasaDokterId'] = $this->jasaDokter['JasaDokterId'] ?? '';
        $this->formEntryJasaDokter['jasaDokterDesc'] = $this->jasaDokter['JasaDokterDesc'] ?? '';

        // $this->formEntryJasaDokter['jasaDokterPrice'] = $this->jasaDokter['JasaDokterPrice'] ?? '';

        // Jika 'jasaDokterPrice' belum tersedia atau kosong, tentukan harga berdasarkan status klaim
        if (!isset($this->formEntryJasaDokter['jasaDokterPrice']) || empty($this->formEntryJasaDokter['jasaDokterPrice'])) {
            // Ambil klaim_status dari rsmst_klaimtypes dengan default 'UMUM' jika NULL
            $klaimStatus = DB::table('rsmst_klaimtypes')
                ->where('klaim_id', $this->dataDaftarUgd['klaimId'] ?? '')
                ->value('klaim_status') ?? 'UMUM';

            // Berdasarkan status klaim, ambil harga yang sesuai dari tabel rsmst_accdocs
            if ($klaimStatus === 'BPJS') {
                $JasaDokterPrice = DB::table('rsmst_accdocs')
                    ->where('accdoc_id', $this->jasaDokter['JasaDokterId'] ?? '')
                    ->value('accdoc_price_bpjs');
            } else {
                $JasaDokterPrice = DB::table('rsmst_accdocs')
                    ->where('accdoc_id', $this->jasaDokter['JasaDokterId'] ?? '')
                    ->value('accdoc_price');
            }

            // Set JasaDokterPrice jika ditemukan, jika tidak set ke 0
            $this->formEntryJasaDokter['jasaDokterPrice'] = $JasaDokterPrice ?? 0;
        }
    }
    private function syncLOV(): void
    {
        $this->dokter = $this->collectingMyDokter;
        $this->jasaDokter = $this->collectingMyJasaDokter;
    }






    // select data start////////////////
    public function render()
    {

        // LOV
        $this->syncLOV();
        // FormEntry
        $this->syncDataFormEntry();

        return view(
            'livewire.emr-u-g-d.administrasi-u-g-d.jasa-dokter-u-g-d',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Data Pasien Unit Gawat Darurat',
                'mySnipt' => 'Rekam Dokter Pasien',
                'myProgram' => 'Jasa Karyawan',
            ]
        );
    }
    // select data end////////////////


}
