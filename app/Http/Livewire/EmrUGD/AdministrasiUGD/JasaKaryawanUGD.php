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


use App\Http\Traits\LOV\LOVJasaKaryawan\LOVJasaKaryawanTrait;
use Exception;


class JasaKaryawanUGD extends Component
{
    use WithPagination, EmrUGDTrait, LOVJasaKaryawanTrait;




    //////////////////////////////
    // Ref on top bar
    //////////////////////////////
    public $rjNoRef;



    // dataDaftarUgd RJ
    public array $dataDaftarUgd = [];

    //////////////////////////////////////////////////////////////////////



    //////////////////////////////////////////////////////////////////////
    // LOV Nested
    public array $jasaKaryawan;
    // LOV Nested

    public $formEntryJasaKaryawan = [
        'jasaKaryawanId' => '',
        'jasaKaryawanDesc' => '',
        'jasaKaryawanPrice' => '',
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


    // insert and update record end////////////////


    private function findData($rjno): void
    {
        $findDataUGD = $this->findDataUGD($rjno);
        $this->dataDaftarUgd  = $findDataUGD;

        // jika JasaKaryawan tidak ditemukan tambah variable JasaKaryawan pda array
        if (isset($this->dataDaftarUgd['JasaKaryawan']) == false) {
            $this->dataDaftarUgd['JasaKaryawan'] = [];
        }
    }



    public function insertJasaKaryawan(): void
    {
        if (!$this->checkUgdStatus($this->rjNoRef)) return;

        $rules = [
            "formEntryJasaKaryawan.jasaKaryawanId"    => 'bail|required|exists:rsmst_actemps,acte_id',
            "formEntryJasaKaryawan.jasaKaryawanDesc"  => 'bail|required',
            "formEntryJasaKaryawan.jasaKaryawanPrice" => 'bail|required|numeric',
        ];
        $messages = [
            'formEntryJasaKaryawan.jasaKaryawanId.required'   => 'ID karyawan harus diisi.',
            'formEntryJasaKaryawan.jasaKaryawanId.exists'     => 'ID karyawan tidak valid atau tidak ditemukan.',
            'formEntryJasaKaryawan.jasaKaryawanDesc.required' => 'Deskripsi jasa harus diisi.',
            'formEntryJasaKaryawan.jasaKaryawanPrice.required' => 'Harga jasa harus diisi.',
            'formEntryJasaKaryawan.jasaKaryawanPrice.numeric' => 'Harga jasa harus berupa angka.',
        ];
        $this->validate($rules, $messages);

        $rjNo = $this->rjNoRef;
        $lockRj  = "ugd:{$rjNo}";
        $lockHdr = "ugdactemps:counter";

        try {
            Cache::lock($lockRj, 5)->block(3, function () use ($rjNo, $lockHdr) {
                Cache::lock($lockHdr, 5)->block(3, function () use ($rjNo) {
                    DB::transaction(function () use ($rjNo) {
                        $nextActeDtl = (int) DB::table('rstxn_ugdactemps')
                            ->max(DB::raw('nvl(to_number(acte_dtl),0)')) + 1;

                        DB::table('rstxn_ugdactemps')->insert([
                            'acte_dtl'  => $nextActeDtl,
                            'rj_no'     => $rjNo,
                            'acte_id'   => $this->formEntryJasaKaryawan['jasaKaryawanId'],
                            'acte_price' => $this->formEntryJasaKaryawan['jasaKaryawanPrice'],
                        ]);

                        $this->dataDaftarUgd['JasaKaryawan'][] = [
                            'JasaKaryawanId'    => $this->formEntryJasaKaryawan['jasaKaryawanId'],
                            'JasaKaryawanDesc'  => $this->formEntryJasaKaryawan['jasaKaryawanDesc'],
                            'JasaKaryawanPrice' => $this->formEntryJasaKaryawan['jasaKaryawanPrice'],
                            'rjActeDtl'         => $nextActeDtl,
                            'rjNo'              => $rjNo,
                            'userLog'           => auth()->user()->myuser_name ?? '',
                            'userLogDate'       => Carbon::now(config('app.timezone'))->format('d/m/Y H:i:s'),
                        ];

                        $this->paketLainLainJasaKaryawan($this->formEntryJasaKaryawan['jasaKaryawanId'], $rjNo, $nextActeDtl);
                        $this->paketObatJasaKaryawan($this->formEntryJasaKaryawan['jasaKaryawanId'], $rjNo, $nextActeDtl);

                        $fresh = $this->findDataUGD($rjNo) ?: [];
                        $fresh['JasaKaryawan'] = array_values($this->dataDaftarUgd['JasaKaryawan'] ?? ($fresh['JasaKaryawan'] ?? []));
                        $fresh['LainLain']     = array_values($this->dataDaftarUgd['LainLain']     ?? ($fresh['LainLain']     ?? []));
                        $this->updateJsonUGD($rjNo, $fresh);
                        $this->emit('ugd:refresh-summary');
                        $this->dataDaftarUgd = $fresh;
                    });
                });
            });

            $this->resetformEntryJasaKaryawan();
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Jasa Karyawan berhasil ditambahkan.");
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Gagal menambah Jasa Karyawan.');
        }
    }

    public function removeJasaKaryawan($rjActeDtl)
    {
        if (!$this->checkUgdStatus($this->rjNoRef)) return;

        $rjNo = $this->rjNoRef;
        $lockKey = "ugd:{$rjNo}";

        try {
            Cache::lock($lockKey, 5)->block(3, function () use ($rjNo, $rjActeDtl) {
                DB::transaction(function () use ($rjNo, $rjActeDtl) {
                    DB::table('rstxn_ugdothers')->where('rj_no', $rjNo)->where('acte_dtl', $rjActeDtl)->delete();
                    DB::table('rstxn_ugdobats')->where('rj_no', $rjNo)->where('acte_dtl', $rjActeDtl)->delete();
                    DB::table('rstxn_ugdactemps')->where('rj_no', $rjNo)->where('acte_dtl', $rjActeDtl)->delete();

                    $this->dataDaftarUgd['JasaKaryawan'] = collect($this->dataDaftarUgd['JasaKaryawan'] ?? [])
                        ->reject(fn($i) => (string)($i['rjActeDtl'] ?? '') === (string)$rjActeDtl)
                        ->values()->all();
                    $this->dataDaftarUgd['LainLain'] = collect($this->dataDaftarUgd['LainLain'] ?? [])
                        ->reject(fn($i) => (string)($i['acte_dtl'] ?? '') === (string)$rjActeDtl)
                        ->values()->all();

                    $fresh = $this->findDataUGD($rjNo) ?: [];
                    $fresh['JasaKaryawan'] = array_values($this->dataDaftarUgd['JasaKaryawan']);
                    $fresh['LainLain']     = array_values($this->dataDaftarUgd['LainLain']);
                    $this->updateJsonUGD($rjNo, $fresh);
                    $this->emit('ugd:refresh-summary');
                    $this->dataDaftarUgd = $fresh;
                });
            });

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('Jasa Karyawan dihapus.');
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Gagal menghapus Jasa Karyawan.');
        }
    }
    // /////////////////////////////////////////////////////////////////
    // Paket Jasa Karyawan -> Lain lain
    private function paketLainLainJasaKaryawan($acteId, $rjNo, $acteDtl): void
    {
        $collection = DB::table('rsmst_acteothers')
            ->select('other_id', 'acteother_price')
            ->where('acte_id', $acteId)
            ->orderBy('acte_id')
            ->get();

        foreach ($collection as $item) {
            $this->insertLainLain($acteId, $rjNo, $acteDtl, $item->other_id, 'Paket JK', $item->acteother_price);
        }
    }

    private function insertLainLain($acteId, $rjNo, $acteDtl, $otherId, $otherDesc, $otherPrice): void
    {
        $messages = customErrorMessagesTrait::messages();
        $payload = [
            "LainLainId" => $otherId,
            "LainLainDesc" => $otherDesc,
            "LainLainPrice" => $otherPrice,
            "acteId" => $acteId,
            "acteDtl" => $acteDtl,
            "rjNo" => $rjNo,
        ];
        $rules = [
            "LainLainId" => 'bail|required|exists:rsmst_others,other_id',
            "LainLainDesc" => 'bail|required',
            "LainLainPrice" => 'bail|required|numeric',
            "acteId" => 'bail|required',
            "acteDtl" => 'bail|required|numeric',
            "rjNo" => 'bail|required|numeric',
        ];
        $validator = Validator::make($payload, $rules, $messages);
        if ($validator->fails() || !$this->checkUgdStatus($this->rjNoRef)) return;

        $lockRj = "ugd:{$rjNo}";
        $lockOthers = "ugdothers:counter";

        try {
            Cache::lock($lockRj, 5)->block(3, function () use ($payload, $lockOthers) {
                Cache::lock($lockOthers, 5)->block(3, function () use ($payload) {
                    DB::transaction(function () use ($payload) {
                        $nextDtl = (int) DB::table('rstxn_ugdothers')
                            ->max(DB::raw('nvl(to_number(rjo_dtl),0)')) + 1;

                        DB::table('rstxn_ugdothers')->insert([
                            'rjo_dtl' => $nextDtl,
                            'acte_dtl' => $payload['acteDtl'],
                            'rj_no' => $payload['rjNo'],
                            'other_id' => $payload['LainLainId'],
                            'other_price' => $payload['LainLainPrice'],
                        ]);

                        $this->dataDaftarUgd['LainLain'][] = [
                            'LainLainId' => $payload['LainLainId'],
                            'LainLainDesc' => $payload['LainLainDesc'],
                            'LainLainPrice' => $payload['LainLainPrice'],
                            'rjotherDtl' => $nextDtl,
                            'rjNo' => $payload['rjNo'],
                            'acte_dtl' => $payload['acteDtl'],
                        ];

                        $fresh = $this->findDataUGD($payload['rjNo']) ?: [];
                        $fresh['LainLain'] = array_values($this->dataDaftarUgd['LainLain'] ?? ($fresh['LainLain'] ?? []));
                        $this->updateJsonUGD($payload['rjNo'], $fresh);


                        $this->dataDaftarUgd = $fresh;
                    });
                });
            });
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Gagal menambah Lain-lain.');
        }
    }

    private function removepaketLainLainJasaKaryawan($rjActeDtl): void
    {
        $collection = DB::table('rstxn_ugdothers')
            ->select('rjo_dtl')
            ->where('acte_dtl', $rjActeDtl)
            ->orderBy('acte_dtl')
            ->get();

        foreach ($collection as $item) {
            $this->removeLainLain($item->rjo_dtl);
        }
    }

    private function removeLainLain($rjotherDtl): void
    {
        if (!$this->checkUgdStatus($this->rjNoRef)) return;

        $rjNo = $this->rjNoRef;
        $lockKey = "ugd:{$rjNo}";

        try {
            Cache::lock($lockKey, 5)->block(3, function () use ($rjNo, $rjotherDtl) {
                DB::transaction(function () use ($rjNo, $rjotherDtl) {
                    DB::table('rstxn_ugdothers')->where('rj_no', $rjNo)->where('rjo_dtl', $rjotherDtl)->delete();

                    $this->dataDaftarUgd['LainLain'] = collect($this->dataDaftarUgd['LainLain'] ?? [])
                        ->reject(fn($i) => (string)($i['rjotherDtl'] ?? '') === (string)$rjotherDtl)
                        ->values()->all();

                    $fresh = $this->findDataUGD($rjNo) ?: [];
                    $fresh['LainLain'] = array_values($this->dataDaftarUgd['LainLain']);
                    $this->updateJsonUGD($rjNo, $fresh);


                    $this->dataDaftarUgd = $fresh;
                });
            });

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('Lain-lain dihapus.');
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Gagal menghapus Lain-lain.');
        }
    }
    // Paket Jasa Karyawan -> Lain lain


    // /////////////////////////////////////////////////////////////////
    // Paket Jasa Karyawan -> Obat
    private function paketObatJasaKaryawan($acteId, $rjNo, $acteDtl): void
    {
        $collection = DB::table('rsmst_acteprods')
            ->select(
                'immst_products.product_id as product_id',
                'acte_id',
                'acteprod_qty',
                'immst_products.product_name as product_name',
                'immst_products.sales_price as sales_price',

            )
            ->where('acte_id', $acteId)
            ->join('immst_products', 'immst_products.product_id', 'rsmst_acteprods.product_id')
            ->orderBy('acte_id')
            ->get();

        foreach ($collection as $item) {
            $this->insertObat($acteId, $rjNo, $acteDtl, $item->product_id, 'Paket JK' . $item->product_name, $item->sales_price, $item->acteprod_qty);
        }
    }

    private function insertObat($acteId, $rjNo, $acteDtl, $ObatId, $ObatDesc, $ObatPrice, $Obatqty): void
    {
        $messages = customErrorMessagesTrait::messages();
        $payload = [
            "productId" => $ObatId,
            "productName" => $ObatDesc,
            "signaX" => 1,
            "signaHari" => 1,
            "qty" => $Obatqty,
            "productPrice" => $ObatPrice,
            "catatanKhusus" => '-',
            "acteDtl" => $acteDtl,
            "acteId" => $acteId,
            "rjNo" => $rjNo
        ];
        $rules = [
            "productId" => 'bail|required|exists:immst_products,product_id',
            "productName" => 'bail|required',
            "signaX" => 'bail|required|numeric|min:1|max:5',
            "signaHari" => 'bail|required|numeric|min:1|max:5',
            "qty" => 'bail|required|digits_between:1,3',
            "productPrice" => 'bail|required|numeric',
            "acteDtl" => 'bail|required|numeric',
            "acteId" => 'bail|required',
            "rjNo" => 'bail|required|numeric',
        ];
        $validator = Validator::make($payload, $rules, $messages);
        if ($validator->fails() || !$this->checkUgdStatus($this->rjNoRef)) return;

        $lockRj = "ugd:{$rjNo}";
        $lockDrug = "ugdobats:counter";

        try {
            Cache::lock($lockRj, 5)->block(3, function () use ($payload, $lockDrug) {
                Cache::lock($lockDrug, 5)->block(3, function () use ($payload) {
                    DB::transaction(function () use ($payload) {
                        $nextDtl = (int) DB::table('rstxn_ugdobats')
                            ->max(DB::raw('nvl(to_number(rjobat_dtl),0)')) + 1;

                        DB::table('rstxn_ugdobats')->insert([
                            'rjobat_dtl' => $nextDtl,
                            'acte_dtl' => $payload['acteDtl'],
                            'rj_no' => $payload['rjNo'],
                            'product_id' => $payload['productId'],
                            'qty' => $payload['qty'],
                            'price' => $payload['productPrice'],
                            'rj_carapakai' => $payload['signaX'],
                            'rj_kapsul' => $payload['signaHari'],
                            'rj_takar' => 'Tablet',
                            'catatan_khusus' => $payload['catatanKhusus'],
                            'exp_date' => DB::raw("to_date('" . ($this->dataDaftarUgd['rjDate'] ?? date('d/m/Y H:i:s')) . "','dd/mm/yyyy hh24:mi:ss')+30"),
                            'etiket_status' => 0,
                        ]);
                    });
                });
            });
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Gagal menambah obat.');
        }
    }

    private function removepaketObatJasaKaryawan($rjActeDtl): void
    {
        $collection = DB::table('rstxn_ugdobats')
            ->select('rjobat_dtl')
            ->where('acte_dtl', $rjActeDtl)
            ->orderBy('acte_dtl')
            ->get();

        foreach ($collection as $item) {
            $this->removeObat($item->rjobat_dtl);
        }
    }

    private function removeObat($rjObatDtl): void
    {
        if (!$this->checkUgdStatus($this->rjNoRef)) return;

        $rjNo = $this->rjNoRef;
        $lockKey = "ugd:{$rjNo}";

        try {
            Cache::lock($lockKey, 5)->block(3, function () use ($rjNo, $rjObatDtl) {
                DB::transaction(function () use ($rjNo, $rjObatDtl) {
                    DB::table('rstxn_ugdobats')->where('rj_no', $rjNo)->where('rjobat_dtl', $rjObatDtl)->delete();
                });
            });

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('Obat dihapus.');
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Gagal menghapus obat.');
        }
    }
    // Paket Jasa Karyawan -> Obat
    // /////////////////////////////////////////////////////////////////


    public function resetformEntryJasaKaryawan()
    {
        $this->reset([
            'formEntryJasaKaryawan',
            'collectingMyJasaKaryawan'
        ]);
        $this->resetValidation();
    }




    // when new form instance
    public function mount()
    {
        $this->findData($this->rjNoRef);
    }

    private function syncDataFormEntry(): void
    {
        // Synk Lov JasaKaryawan
        $this->formEntryJasaKaryawan['jasaKaryawanId'] = $this->jasaKaryawan['JasaKaryawanId'] ?? '';
        $this->formEntryJasaKaryawan['jasaKaryawanDesc'] = $this->jasaKaryawan['JasaKaryawanDesc'] ?? '';
        // $this->formEntryJasaKaryawan['jasaKaryawanPrice'] = $this->jasaKaryawan['JasaKaryawanPrice'] ?? '';

        // Jika 'jasaKaryawanPrice' belum tersedia atau kosong, tentukan harga berdasarkan status klaim
        if (!isset($this->formEntryJasaKaryawan['jasaKaryawanPrice']) || empty($this->formEntryJasaKaryawan['jasaKaryawanPrice'])) {
            // Ambil klaim_status dari rsmst_klaimtypes dengan default 'UMUM' jika NULL
            $klaimStatus = DB::table('rsmst_klaimtypes')
                ->where('klaim_id', $this->dataDaftarUgd['klaimId'] ?? '')
                ->value('klaim_status') ?? 'UMUM';

            // Berdasarkan status klaim, ambil harga yang sesuai dari tabel rsmst_actemps
            if ($klaimStatus === 'BPJS') {
                $JasaKaryawanPrice = DB::table('rsmst_actemps')
                    ->where('acte_id', $this->jasaKaryawan['JasaKaryawanId'] ?? '')
                    ->value('acte_price_bpjs');
            } else {
                $JasaKaryawanPrice = DB::table('rsmst_actemps')
                    ->where('acte_id', $this->jasaKaryawan['JasaKaryawanId'] ?? '')
                    ->value('acte_price');
            }

            // Set JasaKaryawanPrice jika ditemukan, jika tidak set ke 0
            $this->formEntryJasaKaryawan['jasaKaryawanPrice'] = $JasaKaryawanPrice ?? 0;
        }
    }
    private function syncLOV(): void
    {
        $this->jasaKaryawan = $this->collectingMyJasaKaryawan;
    }


    // select data start////////////////
    public function render()
    {

        // LOV
        $this->syncLOV();
        // FormEntry
        $this->syncDataFormEntry();

        return view(
            'livewire.emr-u-g-d.administrasi-u-g-d.jasa-karyawan-u-g-d',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Data Pasien Unit Gawat Darurat',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Jasa Karyawan',
            ]
        );
    }
    // select data end////////////////


}
