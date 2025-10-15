<?php

namespace App\Http\Livewire\EmrRJ\EresepRJ;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

use App\Http\Traits\customErrorMessagesTrait;
use App\Http\Traits\EmrRJ\EmrRJTrait;

// use Illuminate\Support\Str;
// use Spatie\ArrayToXml\ArrayToXml;
use Exception;
use Illuminate\Support\Facades\Validator;

class EresepRJ extends Component
{
    use WithPagination, EmrRJTrait;

    // listener from blade////////////////
    protected $listeners = [
        'storeAssessmentDokterRJ' => 'store',
        'syncronizeAssessmentDokterRJFindData' => 'mount',
        'syncronizeAssessmentPerawatRJFindData' => 'mount'

    ];

    //////////////////////////////
    // Ref on top bar
    //////////////////////////////
    public $rjNoRef = 472309;
    public string $rjStatusRef;

    // dataDaftarPoliRJ RJ
    public array $dataDaftarPoliRJ = [];

    //////////////////////////////////////////////////////////////////////

    //  table LOV////////////////

    public $dataProductLov = [];
    public $dataProductLovStatus = 0;
    public $dataProductLovSearch = '';
    public $selecteddataProductLovIndex = 0;

    public $collectingMyProduct = [];









    /** ======= State Obat Kronis (untuk UI & guard) ======= */
    public bool $isChronic = false;
    public $maxQty = 0;
    public ?string $lastTebusDate = null;   // 'Y-m-d'
    public ?int $daysSince = null;          // selisih hari dari rjDate sekarang
    public $qty30d = 0;             // total qty di 30 hari (tanpa kunjungan sekarang)
    public bool $warnRepeatUnder30d = false;
    public bool $warnOverMaxQty = false;
    public ?string $kronisMessage = null;


    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////
    public function updated($propertyName)
    {
        // dd($propertyName);
        // $this->validateOnly($propertyName);
    }

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
        $this->checkRjStatus();
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
        $this->checkRjStatus();
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
        // set data RJno / NoBooking / NoAntrian / klaimId / kunjunganId
        $this->setDataPrimer();

        // Logic update mode start //////////
        $this->updateDataRJ($this->dataDaftarPoliRJ['rjNo']);
        $this->emit('syncronizeAssessmentDokterRJFindData');
        $this->emit('syncronizeAssessmentPerawatRJFindData');
    }

    private function updateDataRJ($rjNo): void
    {

        // if ($rjNo !== $this->dataDaftarPoliRJ['rjNo']) {
        //     dd('Data Json Tidak sesuai' . $rjNo . '  /  ' . $this->dataDaftarPoliRJ['rjNo']);
        // }

        // // update table trnsaksi
        // DB::table('rstxn_rjhdrs')
        //     ->where('rj_no', $rjNo)
        //     ->update([
        //         'dataDaftarPoliRJ_json' => json_encode($this->dataDaftarPoliRJ, true),
        //         'dataDaftarPoliRJ_xml' => ArrayToXml::convert($this->dataDaftarPoliRJ),
        //     ]);

        $this->updateJsonRJ($rjNo, $this->dataDaftarPoliRJ);

        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('Eresep berhasil disimpan.');
    }
    // insert and update record end////////////////

    private function findData($rjno): void
    {
        $this->rjStatusRef = DB::table('rstxn_rjhdrs')->select('rj_status')->where('rj_no', $rjno)->first()->rj_status;
        $findDataRJ = $this->findDataRJ($rjno);
        $this->dataDaftarPoliRJ  = $findDataRJ['dataDaftarRJ'];

        // jika eresep tidak ditemukan tambah variable eresep pda array
        if (isset($this->dataDaftarPoliRJ['eresep']) == false) {
            $this->dataDaftarPoliRJ['eresep'] = [];
        }
    }

    private function setDataPrimer(): void {}

    private function addProduct($productId, $productName, $salesPrice): void
    {
        $this->collectingMyProduct = [
            'productId' => $productId,
            'productName' => $productName,
            'jenisKeterangan' => 'NonRacikan', //Racikan non racikan
            'signaX' => "",
            'signaHari' => "",
            'qty' => "",
            'productPrice' => $salesPrice,
            'catatanKhusus' => '',
        ];

        // 2) Ambil klaim status (inline, tanpa helper)
        $isBpjsOrKronis = $this->isBpjsOrKronis();

        // 3) Hanya untuk BPJS/KRONIS jalankan guard obat kronis
        if ($isBpjsOrKronis) {
            // cek kronis segera (pakai qty default 1 dari collectingMyProduct)
            $this->checkObatKronis($productId, 1);

            if ($this->isChronic && ($this->warnRepeatUnder30d || $this->warnOverMaxQty)) {
                toastr()->closeOnHover(true)
                    ->closeDuration(5)
                    ->positionClass('toast-top-left')
                    ->addWarning($this->kronisMessage ?? 'Peringatan obat kronis.');
            }
        }
    }

    // Tambah di class (state kecil buat throttle)
    protected ?float $lastKronisCheckAt = null; // microtime

    public function updatedCollectingMyProductQty($newValue = null) // bisa tangkap value langsung
    {

        // 0) Jika form terkunci (pasien sudah pulang), stop
        if (($this->rjStatusRef ?? '') !== 'A') {
            return;
        }

        // 1) Ambil productId & qty dengan aman
        $productId  = data_get($this->collectingMyProduct, 'productId');
        $plannedQty =  ($newValue ?? data_get($this->collectingMyProduct, 'qty', 0));
        // Normalisasi qty (minimal 1; buang pecahan negatif)
        if ($plannedQty < 0) $plannedQty = 0;
        if ($plannedQty && $plannedQty < 1) $plannedQty = 1;
        $this->collectingMyProduct['qty'] = $plannedQty;

        // 2) Pastikan ada item & qty > 0
        if (!$productId || $plannedQty <= 0) {
            return;
        }

        // 3) Cek status klaim hanya BPJS/KRONIS yang butuh guard
        if (!$this->isBpjsOrKronis()) {
            return;
        }

        // 4) Throttle  (cek max 1x/250ms biar gak banjir query saat user mengetik)
        $now = microtime(true);
        if ($this->lastKronisCheckAt && ($now - $this->lastKronisCheckAt) < 0.25) {
            return;
        }
        $this->lastKronisCheckAt = $now;

        // 5) Jalankan cek kronis
        $this->checkObatKronis($productId, $plannedQty);

        // 6) (Opsional) munculkan warning realtime
        if ($this->isChronic && ($this->warnRepeatUnder30d || $this->warnOverMaxQty)) {
            toastr()->closeOnHover(true)
                ->closeDuration(4)
                ->positionClass('toast-top-left')
                ->addWarning($this->kronisMessage ?? 'Peringatan obat kronis.');
        }
    }

    public function insertProduct(): void
    {
        // validate
        // customErrorMessages
        $messages = customErrorMessagesTrait::messages();
        // require nik ketika pasien tidak dikenal
        $rules = [
            'collectingMyProduct.productId' => 'bail|required|',
            'collectingMyProduct.productName' => 'bail|required|',
            'collectingMyProduct.signaX' => 'bail|required|',
            'collectingMyProduct.signaHari' => 'bail|required|',
            'collectingMyProduct.qty' => 'bail|required|digits_between:1,3|',
            'collectingMyProduct.productPrice' => 'bail|required|numeric|',
            'collectingMyProduct.catatanKhusus' => 'bail|',
        ];

        // Proses Validasi///////////////////////////////////////////
        $this->validate($rules, $messages);

        // validate

        // === Guard kronis ===
        $this->checkObatKronis(
            $this->collectingMyProduct['productId'],
            $this->collectingMyProduct['qty'],
        );

        if ($this->isChronic && ($this->warnRepeatUnder30d || $this->warnOverMaxQty)) {
            // Mode 1 (default): hanya warning, tetap lanjut insert
            toastr()->closeOnHover(true)->closeDuration(6)->positionClass('toast-top-left')
                ->addWarning($this->kronisMessage ?? 'Peringatan obat kronis.');

            // Mode 2 (ketat): BLOK simpan — buka komentar baris di bawah bila ingin memaksa gagal
            // $this->addError('obatKronis', $this->kronisMessage ?? 'Pelanggaran obat kronis.');
            // return;
        }

        // pengganti race condition
        // start:
        try {
            // select nvl(max(rjobat_dtl)+1,1) into :rstxn_rjobats.rjobat_dtl from rstxn_rjobats;

            $lastInserted = DB::table('rstxn_rjobats')->select(DB::raw('nvl(max(rjobat_dtl)+1,1) as rjobat_dtl_max'))->first();

            $productTakar = DB::table('immst_products')
                ->where('product_id', $this->collectingMyProduct['productId'])
                ->value('takar');

            $rjTakar = $productTakar ?: 'Tablet';

            // insert into table transaksi
            DB::table('rstxn_rjobats')->insert([
                'rjobat_dtl' => $lastInserted->rjobat_dtl_max,
                'rj_no' => $this->rjNoRef,
                'product_id' => $this->collectingMyProduct['productId'],
                'qty' => $this->collectingMyProduct['qty'],
                'price' => $this->collectingMyProduct['productPrice'],
                'rj_carapakai' => $this->collectingMyProduct['signaX'],
                'rj_kapsul' => $this->collectingMyProduct['signaHari'],
                'rj_takar'     => $rjTakar,
                'catatan_khusus' => $this->collectingMyProduct['catatanKhusus'],
                'rj_ket' => $this->collectingMyProduct['catatanKhusus'],
                'exp_date' => DB::raw("to_date('" . $this->dataDaftarPoliRJ['rjDate'] . "','dd/mm/yyyy hh24:mi:ss')+30"),
                'etiket_status' => 1,
            ]);

            $this->dataDaftarPoliRJ['eresep'][] = [
                'productId' => $this->collectingMyProduct['productId'],
                'productName' => $this->collectingMyProduct['productName'],
                'jenisKeterangan' => 'NonRacikan', //Racikan non racikan
                'signaX' => $this->collectingMyProduct['signaX'],
                'signaHari' => $this->collectingMyProduct['signaHari'],
                'qty' => $this->collectingMyProduct['qty'],
                'productPrice' => $this->collectingMyProduct['productPrice'],
                'catatanKhusus' => $this->collectingMyProduct['catatanKhusus'],
                'rjObatDtl' => $lastInserted->rjobat_dtl_max,
                'rjNo' => $this->rjNoRef,
            ];

            $this->store();
            $this->reset(['collectingMyProduct']);
            $this->resetKronisState();

            //
        } catch (Exception $e) {
            // display an error to user
            dd($e->getMessage());
        }
        // goto start;
    }

    public function updateProduct($rjobat_dtl, $qty = null, $signaX = null, $signaHari = null, $catatanKhusus = null): void
    {
        // 0) Lock form kalau pasien sudah pulang
        $this->checkRjStatus();

        // 1) Normalisasi input sederhana (opsional)
        $r = [
            'qty'           => (int) $qty,
            'signaX'        => $signaX,
            'signaHari'     => $signaHari,
            'catatanKhusus' => $catatanKhusus,
        ];

        // 2) Validasi
        $messages = customErrorMessagesTrait::messages();
        $rules = [
            'qty'           => 'bail|required|integer|min:1|max:999', // lebih eksplisit dari digits_between
            'signaX'        => 'bail|required',
            'signaHari'     => 'bail|required',
            'catatanKhusus' => 'bail|',
        ];
        $validator = Validator::make($r, $rules, $messages);
        if ($validator->fails()) {
            dd($validator->errors()->first());
        }


        // 3) Ambil product_id untuk rjobat_dtl ini (dibutuhkan untuk cek kronis)
        $row = DB::table('rstxn_rjobats')
            ->select('product_id')
            ->where('rjobat_dtl', $rjobat_dtl)
            ->first();

        if (!$row || empty($row->product_id)) {
            toastr()->closeOnHover(true)->closeDuration(4)->positionClass('toast-top-left')
                ->addError('Item resep tidak ditemukan.');
            dd('Item resep tidak ditemukan: rjobat_dtl=' . $rjobat_dtl);
        }



        $productId = $row->product_id;
        // 4) Guard obat kronis hanya untuk BPJS/KRONIS
        if ($this->isBpjsOrKronis()) {
            $this->checkObatKronis($productId, $r['qty']);

            if ($this->isChronic && ($this->warnRepeatUnder30d || $this->warnOverMaxQty)) {
                // Mode default: hanya WARNING (tetap lanjut update)
                toastr()->closeOnHover(true)->closeDuration(6)->positionClass('toast-top-left')
                    ->addWarning($this->kronisMessage ?? 'Peringatan obat kronis.');

                // --- Mode ketat (opsional): BLOK simpan ---
                // $this->addError('obatKronis', $this->kronisMessage ?? 'Pelanggaran obat kronis.');
                // return;
            }
        }

        // pengganti race condition
        // start:
        try {
            // insert into table transaksi
            DB::table('rstxn_rjobats')
                ->where('rjobat_dtl', $rjobat_dtl)
                ->update([
                    'qty' => $r['qty'],
                    'rj_carapakai' => $r['signaX'],
                    'rj_kapsul' => $r['signaHari'],
                    'catatan_khusus' => $r['catatanKhusus'],
                    'rj_ket' => $r['catatanKhusus'],
                ]);


            $this->store();
            $this->resetKronisState();

            //
        } catch (Exception $e) {
            // display an error to user
            dd($e->getMessage());
        }
        // goto start;

    }

    public function removeProduct($rjObatDtl)
    {
        $this->checkRjStatus();

        // pengganti race condition
        // start:
        try {
            // remove into table transaksi
            DB::table('rstxn_rjobats')->where('rjobat_dtl', $rjObatDtl)->delete();

            $Product = collect($this->dataDaftarPoliRJ['eresep'])
                ->where('rjObatDtl', '!=', $rjObatDtl)
                ->toArray();
            $this->dataDaftarPoliRJ['eresep'] = $Product;
            $this->store();
            $this->resetKronisState();

            //
        } catch (Exception $e) {
            // display an error to user
            dd($e->getMessage());
        }
        // goto start;
    }

    public function resetcollectingMyProduct()
    {
        $this->reset(['collectingMyProduct']);
        $this->resetKronisState();
    }

    public function checkRjStatus()
    {
        $lastInserted = DB::table('rstxn_rjhdrs')
            ->select('rj_status')
            ->where('rj_no', $this->rjNoRef)
            ->first();

        if ($lastInserted->rj_status !== 'A') {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Pasien Sudah Pulang, Trasaksi Terkunci.');
            return dd('Pasien Sudah Pulang, Trasaksi Terkuncixx.');
        }
    }





















    /** Ambil RJ_DATE sekarang dari data json (format 'dd/mm/yyyy hh24:mi:ss') → Carbon */
    private function getRjDateCarbon(): ?Carbon
    {
        $str = data_get($this->dataDaftarPoliRJ, 'rjDate');
        if (!$str) return null;

        // contoh data: '25/09/2025 09:31:00'
        try {
            return Carbon::createFromFormat('d/m/Y H:i:s', $str);
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Cek aturan obat kronis untuk (productId, qty) terhadap pasien & kunjungan saat ini.
     * - < 30 hari sejak tebus terakhir → warn
     * - akumulasi qty 30 hari (termasuk rencana qty saat ini) > MAXQTY → warn
     */
    private function checkObatKronis($productId, $plannedQty): void
    {
        // ===== Reset state untuk UI/guard =====
        $this->resetKronisState();

        // ===== Data kontekstual yang wajib ada =====
        $patientRegNo       = data_get($this->dataDaftarPoliRJ, 'regNo') ?? data_get($this->dataDaftarPoliRJ, 'pasien.regNo');
        $currentVisitRjNo   = $this->rjNoRef;
        $currentVisitDate   = $this->getRjDateCarbon(); // Carbon (tanggal kunjungan sekarang)

        if (!$patientRegNo || !$currentVisitRjNo || !$currentVisitDate) {
            return; // tidak cukup data untuk melakukan pengecekan
        }

        // ===== 1) Baca master: apakah produk ini kronis? berapa MAXQTY bulanan? =====
        $chronicMaster = DB::selectOne("
            SELECT
                NVL(MAXQTY,0)    AS maxqty
            FROM RSMST_LISTOBATBPJSES
            WHERE PRODUCT_ID = :pid
        ", ['pid' => $productId]);
        $maxqty = (int) data_get($chronicMaster, 'maxqty', 0);
        $this->isChronic = $maxqty > 0;
        $this->maxQty    = $maxqty;
        if (!$this->isChronic) {
            return; // bukan obat kronis → tidak perlu cek lanjut
        }
        // ===== 2) Tentukan jendela waktu 30 hari terakhir (anchor = tanggal kunjungan saat ini) =====
        $windowEndDate   = $currentVisitDate->copy();             // batas akhir = hari ini (kunjungan sekarang)
        $windowStartDate = $currentVisitDate->copy()->subDays(30); // batas awal = 30 hari ke belakang

        // ===== 3) Ringkas pemakaian/tebus 30 hari terakhir utk obat & pasien yg sama (kecuali RJ sekarang) =====
        $recentDispenseSummary = DB::selectOne("
            SELECT
                MAX(rjh.rj_date)     AS last_date,        -- tanggal tebus terakhir untuk obat ini
                NVL(SUM(rjo.qty), 0) AS total_qty_30d     -- total QTY di window 30 hari
            FROM RSTXN_RJOBATS rjo
            JOIN RSTXN_RJHDRS  rjh ON rjh.rj_no = rjo.rj_no
            WHERE rjh.reg_no     = :regNo
              AND rjo.product_id = :pid
              AND rjh.rj_no     <> :currRjNo
              AND rjh.rj_date BETWEEN
                    TO_DATE(:startDate,'YYYY-MM-DD HH24:MI:SS')
                AND TO_DATE(:endDate,'YYYY-MM-DD HH24:MI:SS')
              AND NVL(rjh.txn_status,'X') <> 'C'  -- exclude transaksi batal (sesuaikan kodenya bila berbeda)
              AND NVL(rjh.rj_status,'?') IN ('A','L')    -- ⬅ antri & lunas saja
        ", [
            'regNo'     => $patientRegNo,
            'pid'       => $productId,
            'currRjNo'  => $currentVisitRjNo,
            'startDate' => $windowStartDate->format('Y-m-d H:i:s'),
            'endDate'   => $windowEndDate->format('Y-m-d H:i:s'),
        ]);
        $this->qty30d = (int) data_get($recentDispenseSummary, 'total_qty_30d', 0);
        if ($last = data_get($recentDispenseSummary, 'last_date')) {
            $lastDispenseDate     = Carbon::parse($last);
            $this->lastTebusDate  = $lastDispenseDate->format('Y-m-d');
            $this->daysSince      = $windowEndDate->diffInDays($lastDispenseDate);
        }

        // ===== 4) Aturan peringatan =====
        $this->warnRepeatUnder30d = $this->daysSince !== null && $this->daysSince < 30;
        $this->warnOverMaxQty     = ($this->maxQty > 0) && (($this->qty30d + $plannedQty) > $this->maxQty);
        // dd($this->maxQty);
        // ===== 5) Rangkai pesan =====
        // 5) compose message
        // 5) compose message
        $messages = [];

        $acc = ($this->qty30d ?? 0) + ($plannedQty ?? 0);     // akumulasi termasuk rencana
        $remainBefore = ($this->maxQty > 0) ? max(0, $this->maxQty - ($this->qty30d ?? 0)) : null;
        $allowedNow   = ($this->maxQty > 0) ? max(0, $this->maxQty - ($this->qty30d ?? 0)) : null;

        // Info tebus terakhir
        if ($this->lastTebusDate) {
            $sinceTxt   = $this->daysSince !== null ? " ({$this->daysSince} hari lalu)" : '';
            $messages[] = "Pengambilan obat terakhir belum 30 hari sejak : {$this->lastTebusDate}{$sinceTxt}.";
        }

        // < 30 hari sejak tebus terakhir
        if ($this->warnRepeatUnder30d) {
            $messages[] = " Rencana yang akan diberikan: {$plannedQty}, total pemberian dengan obat sebelumnya: {$acc}.";
        }

        // Melebihi kuota bulanan
        if ($this->warnOverMaxQty) {
            $accF    = number_format((int)$acc, 0, ',', '.');
            $maxF    = number_format((int)$this->maxQty, 0, ',', '.');
            $remainF = $remainBefore !== null ? number_format((int)$remainBefore, 0, ',', '.') : null;
            $allowF  = $allowedNow   !== null ? number_format((int)$allowedNow,   0, ',', '.') : null;

            $lines = [
                '⚠️ Batas obat kronis terlampaui',
                "• Akumulasi 30 hari: {$accF}",
                "• Batas bulanan (MAX): {$maxF}",
                $remainF !== null ? "• Sisa kuota saat ini: {$remainF}" : null,
                $allowF  !== null ? ($allowedNow > 0
                    ? "• Saran tebus saat ini: {$allowF}"
                    : "• Kuota habis — tidak disarankan menambah qty")
                    : null,
            ];

            $messages[] = implode(' ', array_filter($lines));
        }

        $this->kronisMessage = $messages ? implode(' ', $messages) : null;
    }

    /** Reset semua state/flag Peringatan Obat Kronis (BPJS) */
    private function resetKronisState(): void
    {
        $this->isChronic          = false;
        $this->maxQty             = 0;
        $this->lastTebusDate      = null;
        $this->daysSince          = null;
        $this->qty30d             = 0;
        $this->warnRepeatUnder30d = false;
        $this->warnOverMaxQty     = false;
        $this->kronisMessage      = null;
        $this->lastKronisCheckAt  = null; // clear throttle
    }

    private function getKlaimStatusNormalized(): string
    {
        try {
            $klaimId = data_get($this->dataDaftarPoliRJ, 'klaimId')
                ?? DB::table('rstxn_rjhdrs')->where('rj_no', $this->rjNoRef)->value('klaim_id');

            $status = DB::table('rsmst_klaimtypes')
                ->where('klaim_id', $klaimId ?? '')
                ->value('klaim_status');

            return strtoupper(trim($status ?? 'UMUM'));
        } catch (\Throwable $e) {
            return 'UMUM';
        }
    }

    private function isBpjsOrKronis(): bool
    {
        return in_array($this->getKlaimStatusNormalized(), ['UMUM', 'BPJS', 'KRONIS'], true);
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
        return view('livewire.emr-r-j.eresep-r-j.eresep-r-j', [
            // 'RJpasiens' => $query->paginate($this->limitPerPage),
            'myTitle' => 'Data Pasien Rawat Jalan',
            'mySnipt' => 'Rekam Medis Pasien',
            'myProgram' => 'ICD 10',
        ]);
    }
    // select data end////////////////
}
