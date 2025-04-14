<?php

namespace App\Http\Livewire\EmrUGD\AdministrasiUGD;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;


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
        'syncronizeAssessmentDokterUGDFindData' => 'mount',
        'syncronizeAssessmentPerawatUGDFindData' => 'mount'
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
    public function updated($propertyName)
    {
        // dd($propertyName);
        // $this->validateOnly($propertyName);
    }

    // insert and update record start////////////////
    public function store()
    {

        // Logic update mode start //////////
        $this->updateDataUGD($this->dataDaftarUgd['rjNo']);
        $this->emit('syncronizeAssessmentDokterUGDFindData');
        $this->emit('syncronizeAssessmentPerawatUGDFindData');
    }

    private function updateDataUGD($rjNo): void
    {

        // update table trnsaksi
        // DB::table('rstxn_ugdhdrs')
        //     ->where('rj_no', $rjNo)
        //     ->update([
        //         'datadaftarugd_json' => json_encode($this->dataDaftarUgd, true),
        //         'datadaftarugd_xml' => ArrayToXml::convert($this->dataDaftarUgd),
        //     ]);

        $this->updateJsonUGD($rjNo, $this->dataDaftarUgd);


        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Jasa Dokter berhasil disimpan.");
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

        // validate
        $this->checkUgdStatus();
        // customErrorMessages
        $messages = [
            'formEntryJasaDokter.jasaDokterId.required'   => 'ID jasa dokter harus diisi.',
            'formEntryJasaDokter.jasaDokterId.exists'     => 'ID jasa dokter tidak valid atau tidak ditemukan.',
            'formEntryJasaDokter.jasaDokterPrice.required' => 'Harga jasa dokter harus diisi.',
            'formEntryJasaDokter.jasaDokterPrice.numeric'  => 'Harga jasa dokter harus berupa angka.',
            'formEntryJasaDokter.drId.exists'             => 'ID dokter tidak valid atau tidak ditemukan.',
        ];
        // require nik ketika pasien tidak dikenal
        $rules = [
            'formEntryJasaDokter.jasaDokterId' => 'bail|required|exists:rsmst_accdocs ,accdoc_id',
            'formEntryJasaDokter.jasaDokterPrice' => 'bail|required|numeric|',
            'formEntryJasaDokter.drId' => 'bail|nullable|exists:rsmst_doctors ,dr_id',
        ];

        // Proses Validasi///////////////////////////////////////////
        $this->validate($rules, $messages);

        // validate


        // pengganti race condition
        // start:
        try {

            $lastInserted = DB::table('rstxn_ugdaccdocs')
                ->select(DB::raw("nvl(max(rjhn_dtl)+1,1) as rjhn_dtl_max"))
                ->first();
            // insert into table transaksi
            DB::table('rstxn_ugdaccdocs')
                ->insert([
                    'dr_id' => $this->formEntryJasaDokter['drId'],
                    'rjhn_dtl' => $lastInserted->rjhn_dtl_max,
                    'rj_no' => $this->rjNoRef,
                    'accdoc_id' => $this->formEntryJasaDokter['jasaDokterId'],
                    'accdoc_price' => $this->formEntryJasaDokter['jasaDokterPrice'],
                ]);


            $this->dataDaftarUgd['JasaDokter'][] = [
                'DokterId' => $this->formEntryJasaDokter['drId'],
                'DokterName' => $this->formEntryJasaDokter['drName'],
                'JasaDokterId' => $this->formEntryJasaDokter['jasaDokterId'],
                'JasaDokterDesc' => $this->formEntryJasaDokter['jasaDokterDesc'],
                'JasaDokterPrice' => $this->formEntryJasaDokter['jasaDokterPrice'],
                'rjaccdocDtl' => $lastInserted->rjhn_dtl_max,
                'rjNo' => $this->rjNoRef,
                'userLog' => auth()->user()->myuser_name,
                'userLogDate' => Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s')
            ];

            $this->paketLainLainJasaDokter($this->formEntryJasaDokter['jasaDokterId'], $this->rjNoRef, $lastInserted->rjhn_dtl_max);
            $this->paketObatJasaDokter($this->formEntryJasaDokter['jasaDokterId'], $this->rjNoRef, $lastInserted->rjhn_dtl_max);

            $this->store();
            $this->resetformEntryJasaDokter();



            //
        } catch (Exception $e) {
            // display an error to user
            dd($e->getMessage());
        }
        // goto start;
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

        $this->checkUgdStatus();


        // pengganti race condition
        // start:
        try {

            $this->removepaketLainLainJasaDokter($rjaccdocDtl);
            $this->removepaketObatJasaDokter($rjaccdocDtl);

            // remove into table transaksi
            DB::table('rstxn_ugdaccdocs')
                ->where('rjhn_dtl', $rjaccdocDtl)
                ->delete();


            $JasaDokter = collect($this->dataDaftarUgd['JasaDokter'])->where("rjaccdocDtl", '!=', $rjaccdocDtl)->toArray();
            $this->dataDaftarUgd['JasaDokter'] = $JasaDokter;
            $this->store();


            //
        } catch (Exception $e) {
            // display an error to user
            dd($e->getMessage());
        }
        // goto start;


    }


    // Paket JasaDokter -> Lain lain
    private function paketLainLainJasaDokter($accdocId, $rjNo, $accdocDtl): void
    {
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

        // validate
        // customErrorMessages
        $messages = customErrorMessagesTrait::messages();
        // require nik ketika pasien tidak dikenal
        $collectingMyLainLain =
            [
                "LainLainId" => $otherId,
                "LainLainDesc" => $otherDesc,
                "LainLainPrice" => $otherPrice,
                "accdocId" => $accdocId,
                "accdocDtl" => $accdocDtl,
                "rjNo" => $rjNo,

            ];

        $rules = [
            "LainLainId" => 'bail|required|exists:rsmst_others ,other_id',
            "LainLainDesc" => 'bail|required|',
            "LainLainPrice" => 'bail|required|numeric|',
            "accdocId" => 'bail|required||',
            "accdocDtl" => 'bail|required|numeric|',
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
                    'rjhn_dtl' => $collectingMyLainLain['accdocDtl'],
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
                'rjhn_dtl' => $collectingMyLainLain['accdocDtl']
            ];

            $this->store();
            //
        } catch (Exception $e) {
            // display an error to user
            dd($e->getMessage());
        }
        // goto start;
    }

    private function removepaketLainLainJasaDokter($rjaccdocDtl): void
    {
        $collection = DB::table('rstxn_ugdothers')
            ->select('rjo_dtl')
            ->where('rjhn_dtl', $rjaccdocDtl)
            ->orderBy('rjhn_dtl')
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
    // Paket JasaDokter -> Lain lain


    // /////////////////////////////////////////////////////////////////
    // Paket JasaDokter -> Obat
    private function paketObatJasaDokter($accdocId, $rjNo, $accdocDtl): void
    {
        $collection = DB::table('rsmst_accdocproducts')
            ->select(
                'immst_products.product_id as product_id',
                'accdoc_id',
                'accdprod_qty',
                'immst_products.product_name as product_name',
                'immst_products.sales_price as sales_price',

            )
            ->where('accdoc_id', $accdocId)
            ->join('immst_products', 'immst_products.product_id', 'rsmst_accdocproducts.product_id')
            ->orderBy('accdoc_id')
            ->get();

        foreach ($collection as $item) {
            $this->insertObat($accdocId, $rjNo, $accdocDtl, $item->product_id, 'Paket JD' . $item->product_name, $item->sales_price, $item->accdprod_qty);
        }
    }

    private function insertObat($accdocId, $rjNo, $accdocDtl, $ObatId, $ObatDesc, $ObatPrice, $Obatqty): void
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
            "accdocDtl" => $accdocDtl,
            "accdocId" => $accdocId,
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
            "accdocDtl" => 'bail|required|numeric|',
            "accdocId" => 'bail|required|numeric|',
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
                    'rjhn_dtl' => $collectingMyObat['accdocDtl'],
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

    private function removepaketObatJasaDokter($rjaccdocDtl): void
    {
        $collection = DB::table('rstxn_ugdobats')
            ->select('rjobat_dtl')
            ->where('rjhn_dtl', $rjaccdocDtl)
            ->orderBy('rjhn_dtl')
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
    // Paket JasaDokter -> Obat
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
        $this->formEntryJasaDokter['drId'] = $this->dokter['DokterId'] ?? '';
        $this->formEntryJasaDokter['drName'] = $this->dokter['DokterDesc'] ?? '';

        $this->formEntryJasaDokter['jasaDokterId'] = $this->jasaDokter['JasaDokterId'] ?? '';
        $this->formEntryJasaDokter['jasaDokterDesc'] = $this->jasaDokter['JasaDokterDesc'] ?? '';

        // $this->formEntryJasaDokter['jasaDokterPrice'] = $this->jasaDokter['JasaDokterPrice'] ?? '';

        // Jika 'jasaDokterPrice' belum tersedia atau kosong, tentukan harga berdasarkan status klaim
        if (!isset($this->formEntryJasaDokter['jasaDokterPrice']) || empty($this->formEntryJasaDokter['jasaDokterPrice'])) {
            // Ambil klaim_status dari rsmst_klaimtypes dengan default 'UMUM' jika NULL
            $klaimStatus = DB::table('rsmst_klaimtypes')
                ->where('klaim_id', $this->dataDaftarPoliRJ['klaimId'] ?? '')
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
