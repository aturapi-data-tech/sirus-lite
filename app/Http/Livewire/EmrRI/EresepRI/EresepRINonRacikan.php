<?php

namespace App\Http\Livewire\EmrRI\EresepRI;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;

use App\Http\Traits\EmrRI\EmrRITrait;
use App\Http\Traits\LOV\LOVProduct\LOVProductTrait;


use Exception;


class EresepRINonRacikan extends Component
{
    use WithPagination, EmrRITrait, LOVProductTrait;

    // listener from blade////////////////
    protected $listeners = [
        'syncronizeAssessmentDokterRIFindData' => 'mount',
        'syncronizeAssessmentPerawatRIFindData' => 'mount',
        'resepNoUpdated' => 'updateResepNoFromEmit'
    ];



    //////////////////////////////
    // Ref on top bar
    //////////////////////////////
    public $riHdrNoRef;
    public $resepNoRef;
    public $resepIndexRef;
    public $riStatusRef;

    // dataDaftarRi RI
    public array $dataDaftarRi = [];

    // LOV Nested
    public array $product;
    // LOV Nested

    public $formEntryEresepRINonRacikan = [
        'productId' => '',
        'productName' => '',
        'jenisKeterangan' => '', //Racikan non racikan
        'signaX' => '',
        'signaHari' => '',
        'qty' => '',
        'productPrice' => '',
        'catatanKhusus' => '',
    ];

    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////

    public function insertProduct(): void
    {
        $rules = [
            'formEntryEresepRINonRacikan.productId'   => 'bail|required|exists:immst_products,product_id',
            'formEntryEresepRINonRacikan.productName' => 'bail|required',
            'formEntryEresepRINonRacikan.signaX'        => 'bail|required',
            'formEntryEresepRINonRacikan.signaHari'     => 'bail|required',
            'formEntryEresepRINonRacikan.qty'           => 'bail|required|digits_between:1,3',
            'formEntryEresepRINonRacikan.productPrice'  => 'bail|required|numeric',
            'formEntryEresepRINonRacikan.catatanKhusus' => 'bail',
        ];

        $messages = [
            'formEntryEresepRINonRacikan.productId.required'   => 'Product ID wajib diisi.',
            'formEntryEresepRINonRacikan.productId.exists'     => 'Product ID tidak valid atau tidak ditemukan di database.',
            'formEntryEresepRINonRacikan.productName.required' => 'Nama produk wajib diisi.',
            'formEntryEresepRINonRacikan.signaX.required'      => 'Signa X wajib diisi.',
            'formEntryEresepRINonRacikan.signaHari.required'   => 'Signa Hari wajib diisi.',
            'formEntryEresepRINonRacikan.qty.required'         => 'Kuantitas wajib diisi.',
            'formEntryEresepRINonRacikan.qty.digits_between'   => 'Kuantitas harus terdiri antara :min sampai :max digit.',
            'formEntryEresepRINonRacikan.productPrice.required' => 'Harga produk wajib diisi.',
            'formEntryEresepRINonRacikan.productPrice.numeric' => 'Harga produk harus berupa angka.',
        ];
        // Proses Validasi///////////////////////////////////////////
        try {
            $this->validate($rules, $messages);
        } catch (\Illuminate\Validation\ValidationException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Periksa kembali input data. " . $e->getMessage());
            $this->validate($rules, $messages);
        }

        // pengganti race condition
        // start:
        try {
            foreach ($this->dataDaftarRi['eresepHdr'] as $index => $header) {
                if (isset($header['resepNo'])) {
                    if ($header['resepNo'] == $this->resepNoRef) {
                        // setIndex Untuk membantu status record
                        $this->resepIndexRef = $index;

                        // Pastikan key 'eresep' sudah diinisialisasi sebagai array
                        if (!isset($this->dataDaftarRi['eresepHdr'][$index]['eresep'])) {
                            $this->dataDaftarRi['eresepHdr'][$index]['eresep'] = [];
                        }

                        $lastInserted = isset($this->dataDaftarRi['eresepHdr'][$index]['eresep'])
                            ? count($this->dataDaftarRi['eresepHdr'][$index]['eresep'])
                            : 0;
                        // Tambahkan data detail resep
                        $this->dataDaftarRi['eresepHdr'][$index]['eresep'][] = [
                            'productId'      => $this->formEntryEresepRINonRacikan['productId'],
                            'productName'    => $this->formEntryEresepRINonRacikan['productName'],
                            'jenisKeterangan' => 'NonRacikan',
                            'signaX'         => $this->formEntryEresepRINonRacikan['signaX'],
                            'signaHari'      => $this->formEntryEresepRINonRacikan['signaHari'],
                            'qty'            => $this->formEntryEresepRINonRacikan['qty'],
                            'productPrice'   => $this->formEntryEresepRINonRacikan['productPrice'],
                            'catatanKhusus'  => $this->formEntryEresepRINonRacikan['catatanKhusus'],
                            'riObatDtl'      => $lastInserted + 1,
                            'riHdrNo'        => $this->riHdrNoRef,
                            'resepNo'        => $this->resepNoRef,
                        ];
                        break;
                    }
                }
            }

            $this->store();
            $this->reset(['formEntryEresepRINonRacikan', 'collectingMyProduct']);

            //
        } catch (Exception $e) {
            // display an error to user
            dd($e->getMessage());
        }
        // goto start;
    }

    public function removeProduct($riObatDtl, $index)
    {
        $this->checkRiStatus();

        // pengganti race condition
        // start:
        try {
            // remove into table transaksi
            $Product = collect($this->dataDaftarRi['eresepHdr'][$index]['eresep'])
                ->where('riObatDtl', '!=', $riObatDtl)
                ->toArray();
            $this->dataDaftarRi['eresepHdr'][$index]['eresep'] = $Product;
            $this->store();

            //
        } catch (Exception $e) {
            // display an error to user
            dd($e->getMessage());
        }
        // goto start;
    }


    private function updateDataRI($riHdrNo): void
    {

        $this->updateJsonRI($riHdrNo, $this->dataDaftarRi);
        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('Eresep berhasil disimpan.');
    }
    public function store()
    {
        // Logic update mode start //////////
        $this->updateDataRI($this->dataDaftarRi['riHdrNo']);
        $this->emit('syncronizeAssessmentDokterRIFindData');
        $this->emit('syncronizeAssessmentPerawatRIFindData');
    }

    public function resetFormEntryEresepRINonRacikan()
    {
        $this->reset(['formEntryEresepRINonRacikan', 'collectingMyProduct']);
    }

    private function checkRiStatus()
    {
        $lastInserted = DB::table('rstxn_rihdrs')
            ->select('ri_status')
            ->where('rihdr_no', $this->riHdrNoRef)
            ->first();

        if ($lastInserted->ri_status != 'I') {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Pasien Sudah Pulang, Trasaksi Terkunci.');
            return dd('Pasien Sudah Pulang, Trasaksi Terkunci.');
        }
    }


    public function updateResepNoFromEmit($newResepNo, $newResepIndex)
    {

        $this->resepNoRef = $newResepNo;
        // setIndex Untuk membantu status record
        $this->resepIndexRef = $newResepIndex;
        // toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError($newResepIndex . ' x  ' . $this->resepNoRef);
    }


    private function syncDataFormEntry(): void
    {
        // Synk Lov Product
        $this->formEntryEresepRINonRacikan['productId'] = $this->product['ProductId'] ?? '';
        $this->formEntryEresepRINonRacikan['productName'] = $this->product['ProductName'] ?? '';
        // $this->formEntryEresepRINonRacikan['productPrice'] = $this->product['ProductPrice'] ?? '';

        //qty
        if (!isset($this->formEntryEresepRINonRacikan['qty']) || empty($this->formEntryEresepRINonRacikan['qty'])) {
            $this->formEntryEresepRINonRacikan['qty'] = 1;
        }

        //price
        if (!isset($this->formEntryEresepRINonRacikan['productPrice']) || empty($this->formEntryEresepRINonRacikan['productPrice'])) {
            // Jika class_id ditemukan, ambil sales_price dari tabel immst_products
            $productPrice = DB::table('immst_products')
                ->where('product_id', $this->product['ProductId'] ?? '')
                ->value('sales_price');

            // Set productPrice jika ditemukan, jika tidak set ke 0
            $this->formEntryEresepRINonRacikan['productPrice'] = $productPrice ?? 0;
        }
    }

    private function syncLOV(): void
    {
        $this->product = $this->collectingMyProduct;
    }

    private function findData($riHdrNo): void
    {
        $this->riStatusRef = DB::table('rstxn_rihdrs')->select('ri_status')->where('rihdr_no', $riHdrNo)->first()->ri_status;
        $this->dataDaftarRi  = $this->findDataRI($riHdrNo);
    }




    // when new form instance
    public function mount()
    {
        $this->findData($this->riHdrNoRef);
    }

    // select data start////////////////
    public function render()
    {

        // LOV
        $this->syncLOV();
        // FormEntry
        $this->syncDataFormEntry();

        return view('livewire.emr-r-i.eresep-r-i.eresep-r-i-non-racikan', [
            'myTitle' => 'Data Pasien Rawat Jalan',
            'mySnipt' => 'Rekam Medis Pasien',
            'myProgram' => 'Eresep',
        ]);
    }
    // select data end////////////////
}
