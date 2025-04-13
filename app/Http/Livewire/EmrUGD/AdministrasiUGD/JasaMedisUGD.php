<?php

namespace App\Http\Livewire\EmrUGD\AdministrasiUGD;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;


use App\Http\Traits\customErrorMessagesTrait;
use App\Http\Traits\EmrUGD\EmrUGDTrait;
use App\Http\Traits\LOV\LOVJasaMedis\LOVJasaMedisTrait;

use Exception;


class JasaMedisUGD extends Component
{
    use WithPagination, EmrUGDTrait, LOVJasaMedisTrait;


    // listener from blade////////////////
    protected $listeners = [
        'storeAssessmentDokterUGD' => 'store',
        'syncronizeAssessmentDokterUGDFindData' => 'mount',
        'syncronizeAssessmentPerawatUGDFindData' => 'mount'
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

        // Logic update mode start //////////
        $this->updateDataRJ($this->dataDaftarUgd['rjNo']);
        $this->emit('syncronizeAssessmentDokterUGDFindData');
        $this->emit('syncronizeAssessmentPerawatUGDFindData');
    }

    private function updateDataRJ($rjNo): void
    {

        // update table trnsaksi
        // DB::table('rstxn_ugdhdrs')
        //     ->where('rj_no', $rjNo)
        //     ->update([
        //         'datadaftarugd_json' => json_encode($this->dataDaftarUgd, true),
        //         'datadaftarugd_xml' => ArrayToXml::convert($this->dataDaftarUgd),
        //     ]);

        $this->updateJsonUGD($rjNo, $this->dataDaftarUgd);

        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Jasa Medis berhasil disimpan.");
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

        // validate
        $this->checkUgdStatus();
        // customErrorMessages
        $messages = [
            'formEntryJasaMedis.jasaMedisId.required'  => 'ID Jasa Medis wajib diisi.',
            'formEntryJasaMedis.jasaMedisId.exists'    => 'ID Jasa Medis tidak valid atau tidak ditemukan di data master.',
            'formEntryJasaMedis.jasaMedisDesc.required' => 'Deskripsi Jasa Medis wajib diisi.',
            'formEntryJasaMedis.jasaMedisPrice.required' => 'Harga Jasa Medis wajib diisi.',
            'formEntryJasaMedis.jasaMedisPrice.numeric' => 'Harga Jasa Medis harus berupa angka.',
        ];
        // require nik ketika pasien tidak dikenal
        $rules = [
            "formEntryJasaMedis.jasaMedisId" => 'bail|required|exists:rsmst_actparamedics ,pact_id',
            "formEntryJasaMedis.jasaMedisDesc" => 'bail|required|',
            "formEntryJasaMedis.jasaMedisPrice" => 'bail|required|numeric|',

        ];

        // Proses Validasi///////////////////////////////////////////
        $this->validate($rules, $messages);

        // validate


        // pengganti race condition
        // start:
        try {

            $lastInserted = DB::table('rstxn_ugdactparams')
                ->select(DB::raw("nvl(max(pact_dtl)+1,1) as pact_dtl_max"))
                ->first();
            // insert into table transaksi
            DB::table('rstxn_ugdactparams')
                ->insert([
                    'pact_dtl' => $lastInserted->pact_dtl_max,
                    'rj_no' => $this->rjNoRef,
                    'pact_id' => $this->formEntryJasaMedis['jasaMedisId'],
                    'pact_price' => $this->formEntryJasaMedis['jasaMedisPrice'],
                ]);


            $this->dataDaftarUgd['JasaMedis'][] = [
                'JasaMedisId' => $this->formEntryJasaMedis['jasaMedisId'],
                'JasaMedisDesc' => $this->formEntryJasaMedis['jasaMedisDesc'],
                'JasaMedisPrice' => $this->formEntryJasaMedis['jasaMedisPrice'],
                'rjpactDtl' => $lastInserted->pact_dtl_max,
                'rjNo' => $this->rjNoRef,
                'userLog' => auth()->user()->myuser_name,
                'userLogDate' => Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s')
            ];

            $this->paketLainLainJasaMedis($this->formEntryJasaMedis['jasaMedisId'], $this->rjNoRef, $lastInserted->pact_dtl_max);
            $this->paketObatJasaMedis($this->formEntryJasaMedis['jasaMedisId'], $this->rjNoRef, $lastInserted->pact_dtl_max);

            $this->store();
            $this->resetformEntryJasaMedis();



            //
        } catch (Exception $e) {
            // display an error to user
            dd($e->getMessage());
        }
        // goto start;
    }

    public function removeJasaMedis($rjpactDtl)
    {

        $this->checkUgdStatus();


        // pengganti race condition
        // start:
        try {

            $this->removepaketLainLainJasaMedis($rjpactDtl);
            $this->removepaketObatJasaMedis($rjpactDtl);

            // remove into table transaksi
            DB::table('rstxn_ugdactparams')
                ->where('pact_dtl', $rjpactDtl)
                ->delete();


            $JasaMedis = collect($this->dataDaftarUgd['JasaMedis'])->where("rjpactDtl", '!=', $rjpactDtl)->toArray();
            $this->dataDaftarUgd['JasaMedis'] = $JasaMedis;
            $this->store();


            //
        } catch (Exception $e) {
            // display an error to user
            dd($e->getMessage());
        }
        // goto start;


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

        // validate
        // customErrorMessages
        $messages = customErrorMessagesTrait::messages();
        // require nik ketika pasien tidak dikenal
        $collectingMyLainLain =
            [
                "LainLainId" => $otherId,
                "LainLainDesc" => $otherDesc,
                "LainLainPrice" => $otherPrice,
                "pactId" => $pactId,
                "pactDtl" => $pactDtl,
                "rjNo" => $rjNo,

            ];

        $rules = [
            "LainLainId" => 'bail|required|exists:rsmst_others ,other_id',
            "LainLainDesc" => 'bail|required|',
            "LainLainPrice" => 'bail|required|numeric|',
            "pactId" => 'bail|required||',
            "pactDtl" => 'bail|required|numeric|',
            "rjNo" => 'bail|required|numeric|',



        ];

        // Proses Validasi///////////////////////////////////////////
        $validator = Validator::make($collectingMyLainLain, $rules, $messages);

        if ($validator->fails()) {
            dd($validator->validated());
        }


        // pengganti race condition
        // start:
        try {

            $lastInserted = DB::table('rstxn_ugdothers')
                ->select(DB::raw("nvl(max(rjo_dtl)+1,1) as rjo_dtl_max"))
                ->first();
            // insert into table transaksi
            DB::table('rstxn_ugdothers')
                ->insert([
                    'rjo_dtl' => $lastInserted->rjo_dtl_max,
                    'pact_dtl' => $collectingMyLainLain['pactDtl'],
                    'rj_no' => $collectingMyLainLain['rjNo'],
                    'other_id' => $collectingMyLainLain['LainLainId'],
                    'other_price' => $collectingMyLainLain['LainLainPrice'],
                ]);


            $this->dataDaftarUgd['LainLain'][] = [
                'LainLainId' => $collectingMyLainLain['LainLainId'],
                'LainLainDesc' => $collectingMyLainLain['LainLainDesc'],
                'LainLainPrice' => $collectingMyLainLain['LainLainPrice'],
                'rjotherDtl' => $lastInserted->rjo_dtl_max,
                'rjNo' => $collectingMyLainLain['rjNo'],
                'pact_dtl' => $collectingMyLainLain['pactDtl']
            ];

            $this->store();
            //
        } catch (Exception $e) {
            // display an error to user
            dd($e->getMessage());
        }
        // goto start;
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

        $this->checkUgdStatus();


        // pengganti race condition
        // start:
        try {
            // remove into table transaksi
            DB::table('rstxn_ugdothers')
                ->where('rjo_dtl', $rjotherDtl)
                ->delete();


            $LainLain = collect($this->dataDaftarUgd['LainLain'])->where("rjotherDtl", '!=', $rjotherDtl)->toArray();
            $this->dataDaftarUgd['LainLain'] = $LainLain;

            $this->store();
            //
        } catch (Exception $e) {
            // display an error to user
            dd($e->getMessage());
        }
        // goto start;
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

        // validate
        // customErrorMessages
        $messages = customErrorMessagesTrait::messages();
        // require nik ketika pasien tidak dikenal
        $collectingMyObat = [
            "productId" => $ObatId,
            "productName" => $ObatDesc,
            "signaX" => 1,
            "signaHari" => 1,
            "qty" => $Obatqty,
            "productPrice" => $ObatPrice,
            "catatanKhusus" => '-',
            "pactDtl" => $pactDtl,
            "pactId" => $pactId,
            "rjNo" => $rjNo
        ];

        $rules = [
            "productId" => 'bail|required|exists:immst_products ,product_id',
            "productName" => 'bail|required|',
            "signaX" => 'bail|required|numeric|min:1|max:5',
            "signaHari" => 'bail|required|numeric|min:1|max:5',
            "qty" => 'bail|required|digits_between:1,3|',
            "productPrice" => 'bail|required|numeric|',
            "catatanKhusus" => 'bail|',
            "pactDtl" => 'bail|required|numeric|',
            "pactId" => 'bail|required|',
            "rjNo" => 'bail|required|numeric|',
        ];

        // Proses Validasi///////////////////////////////////////////
        $validator = Validator::make($collectingMyObat, $rules, $messages);

        if ($validator->fails()) {
            dd($validator->validated());
        }


        // pengganti race condition
        // start:
        try {

            $lastInserted = DB::table('rstxn_ugdobats')
                ->select(DB::raw("nvl(max(rjobat_dtl)+1,1) as rjobat_dtl_max"))
                ->first();
            // insert into table transaksi
            DB::table('rstxn_ugdobats')
                ->insert([
                    'rjobat_dtl' => $lastInserted->rjobat_dtl_max,
                    'pact_dtl' => $collectingMyObat['pactDtl'],
                    'rj_no' => $collectingMyObat['rjNo'],
                    'product_id' => $collectingMyObat['productId'],
                    'qty' => $collectingMyObat['qty'],
                    'price' => $collectingMyObat['productPrice'],
                    'rj_carapakai' => $collectingMyObat['signaX'],
                    'rj_kapsul' => $collectingMyObat['signaHari'],
                    'rj_takar' => 'Tablet',
                    'catatan_khusus' => $collectingMyObat['catatanKhusus'],
                    'exp_date' => DB::raw("to_date('" . $this->dataDaftarUgd['rjDate'] . "','dd/mm/yyyy hh24:mi:ss')+30"),
                    'etiket_status' => 0,
                ]);


            // $this->dataDaftarUgd['eresep'][] = [
            //     'productId' => $this->collectingMyProduct['productId'],
            //     'productName' => $this->collectingMyProduct['productName'],
            //     'jenisKeterangan' => 'NonRacikan', //Racikan non racikan
            //     'signaX' => $this->collectingMyProduct['signaX'],
            //     'signaHari' => $this->collectingMyProduct['signaHari'],
            //     'qty' => $this->collectingMyProduct['qty'],
            //     'productPrice' => $this->collectingMyProduct['productPrice'],
            //     'catatanKhusus' => $this->collectingMyProduct['catatanKhusus'],
            //     'rjObatDtl' => $lastInserted->rjobat_dtl_max,
            //     'rjNo' => $this->rjNoRef,
            // ];

            $this->store();
            //
        } catch (Exception $e) {
            // display an error to user
            dd($e->getMessage());
        }
        // goto start;
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

        $this->checkUgdStatus();


        // pengganti race condition
        // start:
        try {
            // remove into table transaksi
            DB::table('rstxn_ugdobats')
                ->where('rjobat_dtl', $rjObatDtl)
                ->delete();


            // $LainLain = collect($this->dataDaftarUgd['LainLain'])->where("rjotherDtl", '!=', $rjotherDtl)->toArray();
            // $this->dataDaftarUgd['LainLain'] = $LainLain;

            $this->store();
            //
        } catch (Exception $e) {
            // display an error to user
            dd($e->getMessage());
        }
        // goto start;
    }
    // Paket JasaMedis -> Obat
    // /////////////////////////////////////////////////////////////////

    public function checkUgdStatus()
    {
        $lastInserted = DB::table('rstxn_ugdhdrs')
            ->select('rj_status')
            ->where('rj_no', $this->rjNoRef)
            ->first();

        if ($lastInserted->rj_status !== 'A') {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Pasien Sudah Pulang, Trasaksi Terkunci.");
            return (dd('Pasien Sudah Pulang, Trasaksi Terkuncixx.'));
        }
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
