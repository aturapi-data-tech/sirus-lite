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
use App\Http\Traits\LOV\LOVJasaMedis\LOVJasaMedisTrait;

use Exception;


class JasaMedisUGD extends Component
{
    use WithPagination, EmrUGDTrait, LOVJasaMedisTrait;


    // listener from blade////////////////
    protected $listeners = [
        'storeAssessmentDokterUGD' => 'store'
    ];


    //////////////////////////////z
    // Ref on top bar
    //////////////////////////////
    public $rjNoRef;



    // dataDaftarUgd RJ
    public array $dataDaftarUgd = [];

    //////////////////////////////////////////////////////////////////////
    // LOV Nested
    public array $jasaMedis;
    // LOV Nested

    //////////////////////////////////////////////////////////////////////
    public array $formEntryJasaMedis = [
        'jasaMedisId' => '',
        'jasaMedisPrice' => '', // Harga kunjungan minimal 0
        'jasaMedisQty' => '',
    ];



    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////
    public function updated($propertyName)
    {
        // dd($propertyName);
        // $this->validateOnly($propertyName);
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
                    // Ambil terbaru dari DB supaya tidak menimpa subtree modul lain
                    $fresh = $this->findDataUGD($rjNo) ?: [];

                    // Pastikan subtree yang kita kelola ada
                    if (!isset($fresh['JasaMedis']) || !is_array($fresh['JasaMedis'])) $fresh['JasaMedis'] = [];
                    if (!isset($fresh['LainLain'])  || !is_array($fresh['LainLain']))  $fresh['LainLain']  = [];

                    // PATCH hanya subtree milik modul ini
                    $fresh['JasaMedis'] = array_values($this->dataDaftarUgd['JasaMedis'] ?? []);
                    $fresh['LainLain']  = array_values($this->dataDaftarUgd['LainLain']  ?? []);

                    // Commit JSON
                    $this->updateJsonUGD($rjNo, $fresh);

                    // Sinkronkan state lokal
                    $this->dataDaftarUgd = $fresh;
                });
            });

            // Emit setelah commit
            $this->emit('ugd:refresh-summary');

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Jasa Medis berhasil disimpan.");
        } catch (LockTimeoutException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Sistem sibuk, gagal memperoleh lock. Coba lagi.');
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Gagal menyimpan data.');
        }
    }


    // insert and update record end////////////////


    private function findData($rjno): void
    {
        $findDataUGD = $this->findDataUGD($rjno);
        $this->dataDaftarUgd  = $findDataUGD;

        // jika JasaMedis tidak ditemukan tambah variable JasaMedis pda array
        if (isset($this->dataDaftarUgd['JasaMedis']) == false) {
            $this->dataDaftarUgd['JasaMedis'] = [];
        }
    }

    public function insertJasaMedis(): void
    {
        if (!$this->checkUgdStatus()) return;

        $messages = [
            'formEntryJasaMedis.jasaMedisId.required'  => 'ID Jasa Medis wajib diisi.',
            'formEntryJasaMedis.jasaMedisId.exists'    => 'ID Jasa Medis tidak valid.',
            'formEntryJasaMedis.jasaMedisDesc.required' => 'Deskripsi wajib diisi.',
            'formEntryJasaMedis.jasaMedisPrice.required' => 'Harga wajib diisi.',
            'formEntryJasaMedis.jasaMedisPrice.numeric' => 'Harga harus angka.',
        ];
        $rules = [
            "formEntryJasaMedis.jasaMedisId"   => 'bail|required|exists:rsmst_actparamedics,pact_id',
            "formEntryJasaMedis.jasaMedisDesc" => 'bail|required',
            "formEntryJasaMedis.jasaMedisPrice" => 'bail|required|numeric',
        ];
        $this->validate($rules, $messages);

        $rjNo = $this->rjNoRef;
        $lockRj = "ugd:{$rjNo}";
        $lockHdr = "ugdactparams:counter";

        try {
            Cache::lock($lockRj, 5)->block(3, function () use ($rjNo, $lockHdr) {
                Cache::lock($lockHdr, 5)->block(3, function () use ($rjNo) {
                    DB::transaction(function () use ($rjNo) {
                        // counter aman
                        $nextPactDtl = (int) DB::table('rstxn_ugdactparams')
                            ->max(DB::raw('nvl(to_number(pact_dtl),0)')) + 1;

                        // insert header
                        DB::table('rstxn_ugdactparams')->insert([
                            'pact_dtl'   => $nextPactDtl,
                            'rj_no'      => $rjNo,
                            'pact_id'    => $this->formEntryJasaMedis['jasaMedisId'],
                            'pact_price' => $this->formEntryJasaMedis['jasaMedisPrice'],
                        ]);

                        // patch state lokal
                        $this->dataDaftarUgd['JasaMedis'][] = [
                            'JasaMedisId'    => $this->formEntryJasaMedis['jasaMedisId'],
                            'JasaMedisDesc'  => $this->formEntryJasaMedis['jasaMedisDesc'],
                            'JasaMedisPrice' => $this->formEntryJasaMedis['jasaMedisPrice'],
                            'rjpactDtl'      => $nextPactDtl,
                            'rjNo'           => $rjNo,
                            'userLog'        => auth()->user()->myuser_name ?? '',
                            'userLogDate'    => Carbon::now(config('app.timezone'))->format('d/m/Y H:i:s')
                        ];

                        // paket (pilih salah satu strategi):
                        // Strategi 1 (paling aman & simpel): panggil helper yang sudah ber-lock per insert
                        $this->paketLainLainJasaMedis($this->formEntryJasaMedis['jasaMedisId'], $rjNo, $nextPactDtl);
                        $this->paketObatJasaMedis($this->formEntryJasaMedis['jasaMedisId'], $rjNo, $nextPactDtl);

                        // fresh-merge JSON khusus subtree yang diubah
                        $fresh = $this->findDataUGD($rjNo) ?: [];
                        $fresh['JasaMedis'] = array_values($this->dataDaftarUgd['JasaMedis'] ?? ($fresh['JasaMedis'] ?? []));
                        $fresh['LainLain']  = array_values($this->dataDaftarUgd['LainLain']  ?? ($fresh['LainLain']  ?? []));
                        $this->updateJsonUGD($rjNo, $fresh);
                        $this->emit('ugd:refresh-summary');

                        $this->dataDaftarUgd = $fresh;
                    });
                });
            });

            $this->resetformEntryJasaMedis();
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Jasa Medis berhasil disimpan.");
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Gagal menambah Jasa Medis.');
            return;
        }
    }


    public function removeJasaMedis($rjpactDtl)
    {
        if (!$this->checkUgdStatus()) return;

        $rjNo = $this->rjNoRef;
        $lockKey = "ugd:{$rjNo}";

        try {
            Cache::lock($lockKey, 5)->block(3, function () use ($rjNo, $rjpactDtl) {
                DB::transaction(function () use ($rjNo, $rjpactDtl) {
                    // hapus paket lain-lain & obat dulu
                    DB::table('rstxn_ugdothers')->where('rj_no', $rjNo)->where('pact_dtl', $rjpactDtl)->delete();
                    DB::table('rstxn_ugdobats')->where('rj_no', $rjNo)->where('pact_dtl', $rjpactDtl)->delete();
                    // hapus header
                    DB::table('rstxn_ugdactparams')->where('rj_no', $rjNo)->where('pact_dtl', $rjpactDtl)->delete();

                    // patch state lokal
                    $this->dataDaftarUgd['JasaMedis'] = collect($this->dataDaftarUgd['JasaMedis'] ?? [])
                        ->reject(fn($i) => (string)($i['rjpactDtl'] ?? '') === (string)$rjpactDtl)
                        ->values()->all();
                    $this->dataDaftarUgd['LainLain'] = collect($this->dataDaftarUgd['LainLain'] ?? [])
                        ->reject(fn($i) => (string)($i['pact_dtl'] ?? '') === (string)$rjpactDtl)
                        ->values()->all();

                    // fresh-merge JSON + commit
                    $fresh = $this->findDataUGD($rjNo) ?: [];
                    $fresh['JasaMedis'] = array_values($this->dataDaftarUgd['JasaMedis']);
                    $fresh['LainLain']  = array_values($this->dataDaftarUgd['LainLain']);
                    $this->updateJsonUGD($rjNo, $fresh);
                    $this->emit('ugd:refresh-summary');

                    $this->dataDaftarUgd = $fresh;
                });
            });

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('Jasa Medis dihapus.');
        } catch (LockTimeoutException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Sistem sibuk, gagal memperoleh lock. Coba lagi.');
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Gagal menghapus Jasa Medis.');
        }
    }

    public function resetformEntryJasaMedis()
    {
        $this->reset([
            'formEntryJasaMedis',
            'collectingMyJasaMedis' //Reset LOV / render  / empty NestLov
        ]);
        $this->resetValidation();
    }

    // /////////////////////////////////////////////////////////////////
    // Paket JasaMedis -> Lain lain
    private function paketLainLainJasaMedis($pactId, $rjNo, $pactDtl): void
    {
        $collection = DB::table('rsmst_actparothers')
            ->select('other_id', 'acto_price')
            ->where('pact_id', $pactId)
            ->orderBy('pact_id')
            ->get();

        foreach ($collection as $item) {
            $this->insertLainLain($pactId, $rjNo, $pactDtl, $item->other_id, 'Paket JM', $item->acto_price);
        }
    }

    private function insertLainLain($pactId, $rjNo, $pactDtl, $otherId, $otherDesc, $otherPrice): void
    {
        $messages = customErrorMessagesTrait::messages();
        $payload = [
            "LainLainId"    => $otherId,
            "LainLainDesc"  => $otherDesc,
            "LainLainPrice" => $otherPrice,
            "pactId"        => $pactId,
            "pactDtl"       => $pactDtl,
            "rjNo"          => $rjNo,
        ];
        $rules = [
            "LainLainId"    => 'bail|required|exists:rsmst_others,other_id',
            "LainLainDesc"  => 'bail|required',
            "LainLainPrice" => 'bail|required|numeric',
            "pactId"        => 'bail|required',
            "pactDtl"       => 'bail|required|numeric',
            "rjNo"          => 'bail|required|numeric',
        ];
        $validator = Validator::make($payload, $rules, $messages);
        if ($validator->fails() || !$this->checkUgdStatus()) return;

        $lockRj = "ugd:{$rjNo}";
        $lockOthers = "ugdothers:counter";

        try {
            Cache::lock($lockRj, 5)->block(3, function () use ($payload, $lockOthers) {
                Cache::lock($lockOthers, 5)->block(3, function () use ($payload) {
                    DB::transaction(function () use ($payload) {
                        $nextDtl = (int) DB::table('rstxn_ugdothers')
                            ->max(DB::raw('nvl(to_number(rjo_dtl),0)')) + 1;

                        DB::table('rstxn_ugdothers')->insert([
                            'rjo_dtl'     => $nextDtl,
                            'pact_dtl'    => $payload['pactDtl'],
                            'rj_no'       => $payload['rjNo'],
                            'other_id'    => $payload['LainLainId'],
                            'other_price' => $payload['LainLainPrice'],
                        ]);

                        $this->dataDaftarUgd['LainLain'][] = [
                            'LainLainId'    => $payload['LainLainId'],
                            'LainLainDesc'  => $payload['LainLainDesc'],
                            'LainLainPrice' => $payload['LainLainPrice'],
                            'rjotherDtl'    => $nextDtl,
                            'rjNo'          => $payload['rjNo'],
                            'pact_dtl'      => $payload['pactDtl'],
                        ];

                        $fresh = $this->findDataUGD($payload['rjNo']) ?: [];
                        $fresh['LainLain'] = array_values($this->dataDaftarUgd['LainLain'] ?? ($fresh['LainLain'] ?? []));
                        $this->updateJsonUGD($payload['rjNo'], $fresh);
                        $this->emit('ugd:refresh-summary');

                        $this->dataDaftarUgd = $fresh;
                    });
                });
            });
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Gagal menambah Lain-lain.');
            return;
        }
    }

    private function removepaketLainLainJasaMedis($rjpactDtl): void
    {
        $collection = DB::table('rstxn_ugdothers')
            ->select('rjo_dtl')
            ->where('pact_dtl', $rjpactDtl)
            ->orderBy('pact_dtl')
            ->get();

        foreach ($collection as $item) {
            $this->removeLainLain($item->rjo_dtl);
        }
    }

    private function removeLainLain($rjotherDtl): void
    {
        if (!$this->checkUgdStatus()) return;

        $rjNo = $this->rjNoRef;
        $lockKey = "ugd:{$rjNo}";

        try {
            Cache::lock($lockKey, 5)->block(3, function () use ($rjNo, $rjotherDtl) {
                DB::transaction(function () use ($rjNo, $rjotherDtl) {
                    DB::table('rstxn_ugdothers')
                        ->where('rj_no', $rjNo)
                        ->where('rjo_dtl', $rjotherDtl)
                        ->delete();

                    $this->dataDaftarUgd['LainLain'] = collect($this->dataDaftarUgd['LainLain'] ?? [])
                        ->reject(fn($i) => (string)($i['rjotherDtl'] ?? '') === (string)$rjotherDtl)
                        ->values()->all();

                    $fresh = $this->findDataUGD($rjNo) ?: [];
                    $fresh['LainLain'] = array_values($this->dataDaftarUgd['LainLain']);
                    $this->updateJsonUGD($rjNo, $fresh);
                    $this->emit('ugd:refresh-summary');

                    $this->dataDaftarUgd = $fresh;
                });
            });

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('Lain-lain dihapus.');
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Gagal menghapus Lain-lain.');
        }
    }


    // /////////////////////////////////////////////////////////////////
    // Paket JasaMedis -> Obat
    private function paketObatJasaMedis($pactId, $rjNo, $pactDtl): void
    {
        $collection = DB::table('rsmst_actparproducts')
            ->select(
                'immst_products.product_id as product_id',
                'pact_id',
                'actprod_qty',
                'immst_products.product_name as product_name',
                'immst_products.sales_price as sales_price',

            )
            ->where('pact_id', $pactId)
            ->join('immst_products', 'immst_products.product_id', 'rsmst_actparproducts.product_id')
            ->orderBy('pact_id')
            ->get();

        foreach ($collection as $item) {
            $this->insertObat($pactId, $rjNo, $pactDtl, $item->product_id, 'Paket JM' . $item->product_name, $item->sales_price, $item->actprod_qty);
        }
    }

    private function insertObat($pactId, $rjNo, $pactDtl, $ObatId, $ObatDesc, $ObatPrice, $Obatqty): void
    {
        $messages = customErrorMessagesTrait::messages();
        $payload = [
            "productId"    => $ObatId,
            "productName"  => $ObatDesc,
            "signaX"       => 1,
            "signaHari"    => 1,
            "qty"          => $Obatqty,
            "productPrice" => $ObatPrice,
            "catatanKhusus" => '-',
            "pactDtl"      => $pactDtl,
            "pactId"       => $pactId,
            "rjNo"         => $rjNo
        ];
        $rules = [
            "productId"    => 'bail|required|exists:immst_products,product_id',
            "productName"  => 'bail|required',
            "signaX"       => 'bail|required|numeric|min:1|max:5',
            "signaHari"    => 'bail|required|numeric|min:1|max:5',
            "qty"          => 'bail|required|digits_between:1,3',
            "productPrice" => 'bail|required|numeric',
            "pactDtl"      => 'bail|required|numeric',
            "pactId"       => 'bail|required',
            "rjNo"         => 'bail|required|numeric',
        ];
        $validator = Validator::make($payload, $rules, $messages);
        if ($validator->fails() || !$this->checkUgdStatus()) return;

        $lockRj = "ugd:{$rjNo}";
        $lockDrug = "ugdobats:counter";

        try {
            Cache::lock($lockRj, 5)->block(3, function () use ($payload, $lockDrug) {
                Cache::lock($lockDrug, 5)->block(3, function () use ($payload) {
                    DB::transaction(function () use ($payload) {
                        $nextDtl = (int) DB::table('rstxn_ugdobats')
                            ->max(DB::raw('nvl(to_number(rjobat_dtl),0)')) + 1;

                        DB::table('rstxn_ugdobats')->insert([
                            'rjobat_dtl'     => $nextDtl,
                            'pact_dtl'       => $payload['pactDtl'],
                            'rj_no'          => $payload['rjNo'],
                            'product_id'     => $payload['productId'],
                            'qty'            => $payload['qty'],
                            'price'          => $payload['productPrice'],
                            'rj_carapakai'   => $payload['signaX'],
                            'rj_kapsul'      => $payload['signaHari'],
                            'rj_takar'       => 'Tablet',
                            'catatan_khusus' => $payload['catatanKhusus'],
                            'exp_date'       => DB::raw("to_date('" . ($this->dataDaftarUgd['rjDate'] ?? date('d/m/Y H:i:s')) . "','dd/mm/yyyy hh24:mi:ss')+30"),
                            'etiket_status'  => 0,
                        ]);
                    });
                });
            });
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Gagal menambah Obat.');
            return;
        }
    }

    private function removepaketObatJasaMedis($rjpactDtl): void
    {
        $collection = DB::table('rstxn_ugdobats')
            ->select('rjobat_dtl')
            ->where('pact_dtl', $rjpactDtl)
            ->orderBy('pact_dtl')
            ->get();

        foreach ($collection as $item) {
            $this->removeObat($item->rjobat_dtl);
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
                        ->where('rj_no', $rjNo)
                        ->where('rjobat_dtl', $rjObatDtl)
                        ->delete();
                });
            });

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('Obat dihapus.');
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Gagal menghapus Obat.');
        }
    }
    // Paket JasaMedis -> Obat
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
        // Synk Lov JasaMedis
        $this->formEntryJasaMedis['jasaMedisId'] = $this->jasaMedis['JasaMedisId'] ?? '';
        $this->formEntryJasaMedis['jasaMedisDesc'] = $this->jasaMedis['JasaMedisDesc'] ?? '';
        // $this->formEntryJasaMedis['jasaMedisPrice'] = $this->jasaMedis['JasaMedisPrice'] ?? '';

        // Jika 'jasaMedisPrice' belum tersedia atau kosong, tentukan harga berdasarkan status klaim
        if (!isset($this->formEntryJasaMedis['jasaMedisPrice']) || empty($this->formEntryJasaMedis['jasaMedisPrice'])) {
            // Ambil klaim_status dari rsmst_klaimtypes dengan default 'UMUM' jika NULL
            $klaimStatus = DB::table('rsmst_klaimtypes')
                ->where('klaim_id', $this->dataDaftarUgd['klaimId'] ?? '')
                ->value('klaim_status') ?? 'UMUM';

            // Berdasarkan status klaim, ambil harga yang sesuai dari tabel rsmst_actparamedics
            if ($klaimStatus === 'BPJS') {
                $JasaMedisPrice = DB::table('rsmst_actparamedics')
                    ->where('pact_id', $this->jasaMedis['JasaMedisId'] ?? '')
                    ->value('pact_price_bpjs');
            } else {
                $JasaMedisPrice = DB::table('rsmst_actparamedics')
                    ->where('pact_id', $this->jasaMedis['JasaMedisId'] ?? '')
                    ->value('pact_price');
            }

            // Set JasaMedisPrice jika ditemukan, jika tidak set ke 0
            $this->formEntryJasaMedis['jasaMedisPrice'] = $JasaMedisPrice ?? 0;
        }
    }
    private function syncLOV(): void
    {
        $this->jasaMedis = $this->collectingMyJasaMedis;
    }

    // select data start////////////////
    public function render()
    {

        // LOV
        $this->syncLOV();
        // FormEntry
        $this->syncDataFormEntry();

        return view(
            'livewire.emr-u-g-d.administrasi-u-g-d.jasa-medis-u-g-d',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Data Pasien Unit Gawat Darurat',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Jasa Medis',
            ]
        );
    }
    // select data end////////////////


}
