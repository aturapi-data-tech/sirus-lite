<?php

namespace App\Http\Livewire\EmrUGD\AdministrasiUGD;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;


use App\Http\Traits\customErrorMessagesTrait;
use App\Http\Traits\EmrUGD\EmrUGDTrait;


// use Illuminate\Support\Str;
use Spatie\ArrayToXml\ArrayToXml;
use Exception;


class JasaKaryawanUGD extends Component
{
    use WithPagination, EmrUGDTrait;


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

    //////////////////////////////////////////////////////////////////////


    //  table LOV////////////////



    public $dataJasaKaryawanLov = [];
    public $dataJasaKaryawanLovStatus = 0;
    public $dataJasaKaryawanLovSearch = '';
    public $selecteddataJasaKaryawanLovIndex = 0;

    public $collectingMyJasaKaryawan = [];







    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////
    public function updated($propertyName)
    {
        // dd($propertyName);
        // $this->validateOnly($propertyName);
    }




    /////////////////////////////////////////////////
    // Lov dataJasaKaryawanLov //////////////////////
    ////////////////////////////////////////////////
    public function clickdataJasaKaryawanLov()
    {
        $this->dataJasaKaryawanLovStatus = true;
        $this->dataJasaKaryawanLov = [];
    }

    public function updateddataJasaKaryawanLovsearch()
    {

        // Reset index of LoV
        $this->reset(['selecteddataJasaKaryawanLovIndex', 'dataJasaKaryawanLov']);
        // Variable Search
        $search = $this->dataJasaKaryawanLovSearch;

        // check LOV by dr_id rs id
        $dataJasaKaryawanLovs = DB::table('rsmst_actemps ')->select(
            'acte_id',
            'acte_desc',
            'acte_price'
        )
            ->where('acte_id', $search)
            ->where('active_status', '1')
            ->first();

        if ($dataJasaKaryawanLovs) {

            // set JasaKaryawan sep
            $this->addJasaKaryawan($dataJasaKaryawanLovs->acte_id, $dataJasaKaryawanLovs->acte_desc, $dataJasaKaryawanLovs->acte_price);
            $this->resetdataJasaKaryawanLov();
        } else {

            // if there is no id found and check (min 3 char on search)
            if (strlen($search) < 1) {
                $this->dataJasaKaryawanLov = [];
            } else {
                $this->dataJasaKaryawanLov = json_decode(
                    DB::table('rsmst_actemps')->select(
                        'acte_id',
                        'acte_desc',
                        'acte_price'
                    )
                        ->where('active_status', '1')
                        ->where(DB::raw('upper(acte_desc)'), 'like', '%' . strtoupper($search) . '%')
                        ->limit(10)
                        ->orderBy('acte_id', 'ASC')
                        ->orderBy('acte_desc', 'ASC')
                        ->get(),
                    true
                );
            }
            $this->dataJasaKaryawanLovStatus = true;
            // set doing nothing
        }
    }
    // /////////////////////
    // LOV selected start
    public function setMydataJasaKaryawanLov($id)
    {
        $this->checkUgdStatus();
        $dataJasaKaryawanLovs = DB::table('rsmst_actemps')->select(
            'acte_id',
            'acte_desc',
            'acte_price'
        )
            ->where('active_status', '1')
            ->where('acte_id', $this->dataJasaKaryawanLov[$id]['acte_id'])
            ->first();

        // set dokter sep
        $this->addJasaKaryawan($dataJasaKaryawanLovs->acte_id, $dataJasaKaryawanLovs->acte_desc, $dataJasaKaryawanLovs->acte_price);
        $this->resetdataJasaKaryawanLov();
    }

    public function resetdataJasaKaryawanLov()
    {
        $this->reset(['dataJasaKaryawanLov', 'dataJasaKaryawanLovStatus', 'dataJasaKaryawanLovSearch', 'selecteddataJasaKaryawanLovIndex']);
    }

    public function selectNextdataJasaKaryawanLov()
    {
        if ($this->selecteddataJasaKaryawanLovIndex === "") {
            $this->selecteddataJasaKaryawanLovIndex = 0;
        } else {
            $this->selecteddataJasaKaryawanLovIndex++;
        }

        if ($this->selecteddataJasaKaryawanLovIndex === count($this->dataJasaKaryawanLov)) {
            $this->selecteddataJasaKaryawanLovIndex = 0;
        }
    }

    public function selectPreviousdataJasaKaryawanLov()
    {

        if ($this->selecteddataJasaKaryawanLovIndex === "") {
            $this->selecteddataJasaKaryawanLovIndex = count($this->dataJasaKaryawanLov) - 1;
        } else {
            $this->selecteddataJasaKaryawanLovIndex--;
        }

        if ($this->selecteddataJasaKaryawanLovIndex === -1) {
            $this->selecteddataJasaKaryawanLovIndex = count($this->dataJasaKaryawanLov) - 1;
        }
    }

    public function enterMydataJasaKaryawanLov($id)
    {
        $this->checkUgdStatus();
        // jika JK belum siap maka toaster error
        if (isset($this->dataJasaKaryawanLov[$id]['acte_id'])) {
            $this->addJasaKaryawan($this->dataJasaKaryawanLov[$id]['acte_id'], $this->dataJasaKaryawanLov[$id]['acte_desc'], $this->dataJasaKaryawanLov[$id]['acte_price']);
            $this->resetdataJasaKaryawanLov();
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Jasa Karyawan belum tersedia.");
        }
    }


    // LOV selected end
    /////////////////////////////////////////////////
    // Lov dataJasaKaryawanLov //////////////////////
    ////////////////////////////////////////////////







    // insert and update record start////////////////
    public function store()
    {
        // set data RJno / NoBooking / NoAntrian / klaimId / kunjunganId
        $this->setDataPrimer();

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


        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Jasa Karyawan berhasil disimpan.");
    }
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


    private function setDataPrimer(): void {}



    private function addJasaKaryawan($JasaKaryawanId, $JasaKaryawanDesc, $salesPrice): void
    {

        $this->collectingMyJasaKaryawan = [
            'JasaKaryawanId' => $JasaKaryawanId,
            'JasaKaryawanDesc' => $JasaKaryawanDesc,
            'JasaKaryawanPrice' => $salesPrice,
        ];
    }

    public function insertJasaKaryawan(): void
    {

        // validate
        $this->checkUgdStatus();
        // customErrorMessages
        $messages = customErrorMessagesTrait::messages();
        // require nik ketika pasien tidak dikenal
        $rules = [
            "collectingMyJasaKaryawan.JasaKaryawanId" => 'bail|required|exists:rsmst_actemps,acte_id',
            "collectingMyJasaKaryawan.JasaKaryawanDesc" => 'bail|required|',
            "collectingMyJasaKaryawan.JasaKaryawanPrice" => 'bail|required|numeric|',

        ];

        // Proses Validasi///////////////////////////////////////////
        $this->validate($rules, $messages);

        // validate


        // pengganti race condition
        // start:
        try {

            $lastInserted = DB::table('rstxn_ugdactemps')
                ->select(DB::raw("nvl(max(acte_dtl)+1,1) as acte_dtl_max"))
                ->first();
            // insert into table transaksi
            DB::table('rstxn_ugdactemps')
                ->insert([
                    'acte_dtl' => $lastInserted->acte_dtl_max,
                    'rj_no' => $this->rjNoRef,
                    'acte_id' => $this->collectingMyJasaKaryawan['JasaKaryawanId'],
                    'acte_price' => $this->collectingMyJasaKaryawan['JasaKaryawanPrice'],
                ]);


            $this->dataDaftarUgd['JasaKaryawan'][] = [
                'JasaKaryawanId' => $this->collectingMyJasaKaryawan['JasaKaryawanId'],
                'JasaKaryawanDesc' => $this->collectingMyJasaKaryawan['JasaKaryawanDesc'],
                'JasaKaryawanPrice' => $this->collectingMyJasaKaryawan['JasaKaryawanPrice'],
                'rjActeDtl' => $lastInserted->acte_dtl_max,
                'rjNo' => $this->rjNoRef,
                'userLog' => auth()->user()->myuser_name,
                'userLogDate' => Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s')
            ];

            $this->paketLainLainJasaKaryawan($this->collectingMyJasaKaryawan['JasaKaryawanId'], $this->rjNoRef, $lastInserted->acte_dtl_max);
            $this->paketObatJasaKaryawan($this->collectingMyJasaKaryawan['JasaKaryawanId'], $this->rjNoRef, $lastInserted->acte_dtl_max);

            $this->store();
            $this->reset(['collectingMyJasaKaryawan']);


            //
        } catch (Exception $e) {
            // display an error to user
            dd($e->getMessage());
        }
        // goto start;
    }

    public function removeJasaKaryawan($rjActeDtl)
    {

        $this->checkUgdStatus();


        // pengganti race condition
        // start:
        try {

            $this->removepaketLainLainJasaKaryawan($rjActeDtl);
            $this->removepaketObatJasaKaryawan($rjActeDtl);

            // remove into table transaksi
            DB::table('rstxn_ugdactemps')
                ->where('acte_dtl', $rjActeDtl)
                ->delete();


            $JasaKaryawan = collect($this->dataDaftarUgd['JasaKaryawan'])->where("rjActeDtl", '!=', $rjActeDtl)->toArray();
            $this->dataDaftarUgd['JasaKaryawan'] = $JasaKaryawan;


            $this->store();


            //
        } catch (Exception $e) {
            // display an error to user
            dd($e->getMessage());
        }
        // goto start;


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

        // validate
        // customErrorMessages
        $messages = customErrorMessagesTrait::messages();
        // require nik ketika pasien tidak dikenal
        $collectingMyLainLain =
            [
                "LainLainId" => $otherId,
                "LainLainDesc" => $otherDesc,
                "LainLainPrice" => $otherPrice,
                "acteId" => $acteId,
                "acteDtl" => $acteDtl,
                "rjNo" => $rjNo,

            ];

        $rules = [
            "LainLainId" => 'bail|required|exists:rsmst_others ,other_id',
            "LainLainDesc" => 'bail|required|',
            "LainLainPrice" => 'bail|required|numeric|',
            "acteId" => 'bail|required||',
            "acteDtl" => 'bail|required|numeric|',
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
                    'acte_dtl' => $collectingMyLainLain['acteDtl'],
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
                'acte_dtl' => $collectingMyLainLain['acteDtl']
            ];

            $this->store();
            //
        } catch (Exception $e) {
            // display an error to user
            dd($e->getMessage());
        }
        // goto start;
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
            "acteDtl" => $acteDtl,
            "acteId" => $acteId,
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
            "acteDtl" => 'bail|required|numeric|',
            "acteId" => 'bail|required|',
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
                    'acte_dtl' => $collectingMyObat['acteDtl'],
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
    // Paket Jasa Karyawan -> Obat
    // /////////////////////////////////////////////////////////////////


    public function resetcollectingMyJasaKaryawan()
    {
        $this->reset(['collectingMyJasaKaryawan']);
    }

    public function checkUgdStatus()
    {
        $lastInserted = DB::table('rstxn_ugdhdrs')
            ->select('rj_status')
            ->where('rj_no', $this->rjNoRef)
            ->first();

        if ($lastInserted->rj_status !== 'A') {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Pasien Sudah Pulang, Trasaksi Terkunci.");
            return (dd('Pasien Sudah Pulang, Trasaksi Terkuncixx.' . $this->rjNoRef));
        }
    }


    // when new form instance
    public function mount()
    {
        $this->findData($this->rjNoRef);
    }



    // select data start////////////////
    public function render()
    {

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
