<?php

namespace App\Http\Livewire\EmrRI\AdministrasiRI;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

use App\Http\Traits\EmrRI\EmrRITrait;
use App\Http\Traits\LOV\LOVProduct\LOVProductTrait;


use Exception;


class ObatPinjamRI extends Component
{
    use WithPagination, EmrRITrait, LOVProductTrait;


    // listener from blade////////////////
    protected $listeners = ['SyncAdministrasiRI' => 'mount'];


    //////////////////////////////
    // Ref on top bar
    //////////////////////////////
    public $riHdrNoRef;
    public array $dataDaftarRi = [];

    // LOV Nested
    public array $product;
    // LOV Nested

    //////////////////////////////////////////////////////////////////////
    public array $formEntryProduct = [
        'productDate' => '', // Format tanggal wajib: dd/mm/yyyy hh24:mi:ss
        'productId' => '',
        'productPrice' => '', // Harga kunjungan minimal 0
        'productQty' => '',
    ];

    public array $dataObatPinjam = [];
    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////
    public function insertProduct(): void
    {
        // validate
        $this->checkRiStatus();

        $rules = [
            'formEntryProduct.productDate' => 'required|date_format:d/m/Y H:i:s', // Format tanggal wajib: dd/mm/yyyy hh24:mi:ss
            'formEntryProduct.productId' => 'required|exists:immst_products,product_id', // ID product harus ada di tabel rsmst_actparamedics kolom product_id
            'formEntryProduct.productPrice' => 'required|numeric|min:0', // Harga kunjungan minimal 0
            'formEntryProduct.productQty' => 'required|numeric|min:1', // Jumlah minimal 1
        ];

        $messages = [
            'formEntryProduct.productDate.required' => 'Tanggal obat wajib diisi.',
            'formEntryProduct.productDate.date_format' => 'Format tanggal obat harus dd/mm/yyyy hh24:mi:ss.',
            'formEntryProduct.productId.required' => 'ID obat wajib diisi.',
            'formEntryProduct.productId.exists' => 'ID obat tidak valid atau tidak ditemukan.',
            'formEntryProduct.productPrice.required' => 'Harga obat wajib diisi.',
            'formEntryProduct.productPrice.numeric' => 'Harga obat harus berupa angka.',
            'formEntryProduct.productPrice.min' => 'Harga obat minimal 0.',
            'formEntryProduct.productQty.required' => 'Jumlah obat wajib diisi.',
            'formEntryProduct.productQty.numeric' => 'Jumlah obat harus berupa angka.',
            'formEntryProduct.productQty.min' => 'Jumlah obat minimal 1.',
        ];

        // Proses validasi
        try {
            $this->validate($rules, $messages);
        } catch (\Illuminate\Validation\ValidationException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Periksa kembali input data. " . $e->getMessage());
            $this->validate($rules, $messages);
        }



        // start:
        try {

            $lastInserted = DB::table('rstxn_riobats')
                ->select(DB::raw("nvl(max(riobat_no)+1,1) as riobat_no_max"))
                ->first();
            // insert into table transaksi
            DB::table('rstxn_riobats')
                ->insert([
                    'riobat_date' => DB::raw("to_date('" . $this->formEntryProduct['productDate'] . "','dd/mm/yyyy hh24:mi:ss')"),
                    'product_id' =>  $this->formEntryProduct['productId'],
                    'riobat_price' =>  $this->formEntryProduct['productPrice'],
                    'riobat_qty' =>  $this->formEntryProduct['productQty'],
                    'rihdr_no' =>  $this->riHdrNoRef,
                    'riobat_no' =>  $lastInserted->riobat_no_max,
                ]);



            $this->administrasiRIuserLog($this->riHdrNoRef, 'Obat Pinjam' . $this->formEntryProduct['productName'] . ' Tarif:' . $this->formEntryProduct['productPrice'] . 'Jml' . $this->formEntryProduct['productQty'] . ' Txn No:' . $lastInserted->riobat_no_max);
            $this->emit('SyncAdministrasiRI');
            $this->resetformEntryProduct();
            //
        } catch (Exception $e) {
            // display an error to user
            dd($e->getMessage());
        }
        // goto start;
    }

    public function removeProduct($ProductNo)
    {
        $this->checkRiStatus();
        // pengganti race condition
        // start:
        try {

            // remove into table transaksi
            DB::table('rstxn_riobats')
                ->where('riobat_no', $ProductNo)
                ->delete();
            //
            $this->administrasiRIuserLog($this->riHdrNoRef, 'Obat Pinjam Remove Txn No:' . $ProductNo);
            $this->emit('SyncAdministrasiRI');

            $this->resetformEntryProduct();
        } catch (Exception $e) {
            // display an error to user
            dd($e->getMessage());
        }
        // goto start;


    }

    public function setProductDate($date)
    {
        $this->formEntryProduct['productDate'] = $date;
    }

    public function resetformEntryProduct()
    {
        $this->reset([
            'formEntryProduct',
            'collectingMyProduct' //Reset LOV / render  / empty NestLov
        ]);
        $this->resetValidation();
    }

    public function checkRiStatus()
    {
        $lastInserted = DB::table('rstxn_rihdrs')
            ->select('ri_status')
            ->where('rihdr_no', $this->riHdrNoRef)
            ->first();

        if ($lastInserted->ri_status !== 'I') {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Pasien Sudah Pulang, Trasaksi Terkunci.");
            return (dd('Pasien Sudah Pulang, Trasaksi Terkuncixx.' . $this->riHdrNoRef));
        }
    }

    private function syncDataPrimer(): void
    {
        // sync data primer untuk LOV
        // Jika data ProductId ada
        if ($this->formEntryProduct['productId']) {
            $this->addProduct($this->formEntryProduct['productId'] ?? '', $this->formEntryProduct['productName'] ?? '', $this->formEntryProduct['productPrice'] ?? '');
        }
    }
    // when new form instance
    public function mount()
    {
        $this->findData($this->riHdrNoRef);
    }

    private function findData($riHdrNo): void
    {
        $riObatPinjam = DB::table('rstxn_riobats')
            ->join('immst_products', 'immst_products.product_id', '=', 'rstxn_riobats.product_id')
            ->select(
                'rstxn_riobats.riobat_date',
                'rstxn_riobats.product_id as product_id',
                'immst_products.product_name',
                'rstxn_riobats.riobat_qty',
                'rstxn_riobats.riobat_price',
                'rstxn_riobats.rihdr_no',
                'rstxn_riobats.riobat_no'
            )
            ->where('rihdr_no', $riHdrNo)
            ->get();
        $this->dataObatPinjam['riObatPinjam'] = json_decode(json_encode($riObatPinjam, true), true);
        $this->syncDataPrimer();
    }

    private function administrasiRIuserLog($riHdrNo, $logs): void
    {
        $this->dataDaftarRi = $this->findDataRI($riHdrNo);

        $this->dataDaftarRi['AdministrasiRI']['userLogs'][] =
            [
                'userLogDesc' => $logs,
                'userLog' => auth()->user()->myuser_name,
                'userLogDate' => Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s')
            ];
        $this->updateJsonRI($riHdrNo, $this->dataDaftarRi);
        $this->reset(['dataDaftarRi']);
    }

    private function syncDataFormEntry(): void
    {
        // Synk Lov Product
        $this->formEntryProduct['productId'] = $this->product['ProductId'] ?? '';
        $this->formEntryProduct['productName'] = $this->product['ProductName'] ?? '';
        // $this->formEntryProduct['productPrice'] = $this->product['ProductPrice'] ?? '';

        //qty
        if (!isset($this->formEntryProduct['productQty']) || empty($this->formEntryProduct['productQty'])) {
            $this->formEntryProduct['productQty'] = 1;
        }

        //price
        if (!isset($this->formEntryProduct['productPrice']) || empty($this->formEntryProduct['productPrice'])) {
            // Jika class_id ditemukan, ambil sales_price dari tabel immst_products
            $productPrice = DB::table('immst_products')
                ->where('product_id', $this->product['ProductId'] ?? '')
                ->value('sales_price');

            // Set productPrice jika ditemukan, jika tidak set ke 0
            $this->formEntryProduct['productPrice'] = $productPrice ?? 0;
        }
    }

    private function syncLOV(): void
    {
        $this->product = $this->collectingMyProduct;
    }
    // select data start////////////////
    public function render()
    {

        // LOV
        $this->syncLOV();
        // FormEntry
        $this->syncDataFormEntry();


        return view(
            'livewire.emr-r-i.administrasi-r-i.obat-pinjam-r-i',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Data Pasien Inap',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'ObatPinjam',
            ]
        );
    }
    // select data end////////////////


}
