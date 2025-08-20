<?php

namespace App\Http\Livewire\EmrRI\EresepRI;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;

use App\Http\Traits\LOV\LOVProduct\LOVProductTrait;
use App\Http\Traits\EmrRI\EmrRITrait;
use Illuminate\Support\Facades\Validator;

use Exception;



class EresepRIRacikan extends Component
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

    public $formEntryEresepRIRacikan = [
        'noRacikan' => '',
        'productId' => '',
        'productName' => '',
        'jenisKeterangan' => '', //Racikan non racikan
        'signaX' => '',
        'signaHari' => '',
        'qty' => '',
        'sedia' => '',
        'dosis' => '',
        'productPrice' => '',
        'catatanKhusus' => '',
        'catatan' => '',
    ];

    public string $noRacikan = 'R1';

    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////

    public function insertProduct(): void
    {
        $this->formEntryEresepRIRacikan['noRacikan'] = $this->noRacikan;
        $rules = [
            "formEntryEresepRIRacikan.noRacikan" => 'bail|required',
            "formEntryEresepRIRacikan.productId" => 'bail|required|exists:immst_products,product_id',
            "formEntryEresepRIRacikan.productName" => 'bail|required',
            "formEntryEresepRIRacikan.signaX" => 'bail|numeric|min:1|max:5',
            "formEntryEresepRIRacikan.signaHari" => 'bail|numeric|min:1|max:5',
            "formEntryEresepRIRacikan.qty" => 'bail|digits_between:1,3',
            "formEntryEresepRIRacikan.productPrice" => 'bail|numeric',
            "formEntryEresepRIRacikan.catatanKhusus" => 'bail|max:150',
            "formEntryEresepRIRacikan.catatan" => 'bail|max:150',
            "formEntryEresepRIRacikan.sedia" => 'bail|max:150',
            "formEntryEresepRIRacikan.dosis" => 'bail|required|max:150',
        ];


        $messages = [
            "formEntryEresepRIRacikan.noRacikan.required"  => "No Racikan wajib diisi.",
            "formEntryEresepRIRacikan.productId.required"  => "Product ID wajib diisi.",
            "formEntryEresepRIRacikan.productId.exists"    => "Product ID tidak ditemukan.",
            "formEntryEresepRIRacikan.productName.required" => "Nama produk wajib diisi.",
            "formEntryEresepRIRacikan.signaX.numeric"       => "Signa X harus berupa angka.",
            "formEntryEresepRIRacikan.signaX.min"           => "Signa X minimal :min.",
            "formEntryEresepRIRacikan.signaX.max"           => "Signa X maksimal :max.",
            "formEntryEresepRIRacikan.signaHari.numeric"    => "Signa Hari harus berupa angka.",
            "formEntryEresepRIRacikan.signaHari.min"        => "Signa Hari minimal :min.",
            "formEntryEresepRIRacikan.signaHari.max"        => "Signa Hari maksimal :max.",
            "formEntryEresepRIRacikan.qty.digits_between"   => "Jumlah harus antara :min dan :max digit.",
            "formEntryEresepRIRacikan.productPrice.numeric" => "Harga produk harus berupa angka.",
            "formEntryEresepRIRacikan.catatanKhusus.max"    => "Catatan khusus maksimal :max karakter.",
            "formEntryEresepRIRacikan.catatan.max"          => "Catatan maksimal :max karakter.",
            "formEntryEresepRIRacikan.sedia.max"            => "Sedia maksimal :max karakter.",
            "formEntryEresepRIRacikan.dosis.required"       => "Dosis wajib diisi.",
            "formEntryEresepRIRacikan.dosis.max"            => "Dosis maksimal :max karakter.",
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

                        // Pastikan key 'eresepRacikan' sudah diinisialisasi sebagai array
                        if (!isset($this->dataDaftarRi['eresepHdr'][$index]['eresepRacikan'])) {
                            $this->dataDaftarRi['eresepHdr'][$index]['eresepRacikan'] = [];
                        }

                        $lastInserted = isset($this->dataDaftarRi['eresepHdr'][$index]['eresepRacikan'])
                            ? count($this->dataDaftarRi['eresepHdr'][$index]['eresepRacikan'])
                            : 0;
                        // Tambahkan data detail resep
                        $this->dataDaftarRi['eresepHdr'][$index]['eresepRacikan'][] = [
                            'noRacikan'       => $this->formEntryEresepRIRacikan['noRacikan'],
                            'productId'       => $this->formEntryEresepRIRacikan['productId'],
                            'productName'     => $this->formEntryEresepRIRacikan['productName'],
                            'jenisKeterangan' => 'Racikan',
                            'signaX'          => $this->formEntryEresepRIRacikan['signaX'],
                            'signaHari'       => $this->formEntryEresepRIRacikan['signaHari'],
                            'qty'             => $this->formEntryEresepRIRacikan['qty'],
                            'productPrice'    => $this->formEntryEresepRIRacikan['productPrice'],
                            'catatanKhusus'   => $this->formEntryEresepRIRacikan['catatanKhusus'],
                            'catatan'         => $this->formEntryEresepRIRacikan['catatan'],
                            'sedia'           => $this->formEntryEresepRIRacikan['sedia'],
                            'dosis'           => $this->formEntryEresepRIRacikan['dosis'],
                            'riObatDtl'       => $lastInserted + 1,
                            'riHdrNo'         => $this->riHdrNoRef,
                            'resepNo'         => $this->resepNoRef,
                        ];
                        break;
                    }
                }
            }

            $this->store();
            $this->reset(['formEntryEresepRIRacikan', 'collectingMyProduct']);

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
            $Product = collect($this->dataDaftarRi['eresepHdr'][$index]['eresepRacikan'])
                ->where('riObatDtl', '!=', $riObatDtl)
                ->toArray();
            $this->dataDaftarRi['eresepHdr'][$index]['eresepRacikan'] = $Product;
            $this->store();

            //
        } catch (Exception $e) {
            // display an error to user
            dd($e->getMessage());
        }
        // goto start;
    }

    public function updateProductRIRacikan(int $resepIndexRef, int $key): void
    {
        $this->checkRiStatus();

        if (!isset($this->dataDaftarRi['eresepHdr'][$resepIndexRef]['eresepRacikan'][$key])) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')->addError('Baris racikan tidak ditemukan.');
            return;
        }

        $row = &$this->dataDaftarRi['eresepHdr'][$resepIndexRef]['eresepRacikan'][$key];

        if (empty($row['riObatDtl'])) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')->addError('ID detail racikan tidak valid.');
            return;
        }

        // Validasi manual (opsional: pakai Validator::make seperti sebelumnya)
        $rules = [
            'dosis'         => 'required|string|max:100',
            'qty'           => 'nullable|integer|min:1',
            'catatan'       => 'nullable|string|max:200',
            'catatanKhusus' => 'nullable|string|max:200', // dipakai sebagai "Signa"
        ];

        $messages = [
            // Dosis
            'dosis.required' => 'Dosis wajib diisi.',
            'dosis.string'   => 'Dosis harus berupa teks.',
            'dosis.max'      => 'Dosis maksimal 100 karakter.',

            // Qty
            'qty.required'   => 'Jumlah racikan wajib diisi.',
            'qty.integer'    => 'Jumlah racikan harus berupa angka.',
            'qty.min'        => 'Jumlah racikan minimal 1.',

            // Catatan
            'catatan.string' => 'Catatan harus berupa teks.',
            'catatan.max'    => 'Catatan maksimal 200 karakter.',

            // Signa
            'catatanKhusus.string' => 'Signa harus berupa teks.',
            'catatanKhusus.max'    => 'Signa maksimal 200 karakter.',
        ];
        $validator = Validator::make($row, $rules, $messages);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $err) {
                toastr()
                    ->closeOnHover(true)
                    ->closeDuration(3)
                    ->positionClass('toast-top-left')->addError($err);
            }
            return;
        }

        // Normalisasi
        $row['qty']           = max(1, (int)($row['qty'] ?? 1));
        $row['dosis']         = trim((string)($row['dosis'] ?? ''));
        $row['catatan']       = trim((string)($row['catatan'] ?? ''));
        $row['catatanKhusus'] = trim((string)($row['catatanKhusus'] ?? ''));

        try {
            // Jika perlu update ke DB detail racikan, lakukan di sini.
            // Contoh:
            // RiObatDtl::whereKey($row['riObatDtl'])->update([
            //     'dosis'          => $row['dosis'],
            //     'qty'            => $row['qty'],
            //     'catatan'        => $row['catatan'],
            //     'catatan_khusus' => $row['catatanKhusus'], // "Signa"
            // ]);

            // Simpan state (atau update JSON header)
            $this->store(); // atau $this->updateJsonRI($this->riHdrNoRef, $this->dataDaftarRi);

            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')->addSuccess('Racikan berhasil diperbarui.');
        } catch (\Throwable $e) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')->addError('Gagal memperbarui racikan: ' . $e->getMessage());
        }
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

    public function resetFormEntryEresepRIRacikan()
    {
        $this->reset(['formEntryEresepRIRacikan', 'collectingMyProduct']);
    }
    // insert and update record end////////////////

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
    }

    private function syncDataFormEntry(): void
    {
        // Synk Lov Product
        $this->formEntryEresepRIRacikan['productId'] = $this->product['ProductId'] ?? '';
        $this->formEntryEresepRIRacikan['productName'] = $this->product['ProductName'] ?? '';
        // $this->formEntryEresepRIRacikan['productPrice'] = $this->product['ProductPrice'] ?? '';

        //qty
        // if (!isset($this->formEntryEresepRIRacikan['qty']) || empty($this->formEntryEresepRIRacikan['qty'])) {
        //     $this->formEntryEresepRIRacikan['qty'] = 1;
        // }

        //price
        if (!isset($this->formEntryEresepRIRacikan['productPrice']) || empty($this->formEntryEresepRIRacikan['productPrice'])) {
            // Jika class_id ditemukan, ambil sales_price dari tabel immst_products
            $productPrice = DB::table('immst_products')
                ->where('product_id', $this->product['ProductId'] ?? '')
                ->value('sales_price');

            // Set productPrice jika ditemukan, jika tidak set ke 0
            $this->formEntryEresepRIRacikan['productPrice'] = $productPrice ?? 0;
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

        return view(
            'livewire.emr-r-i.eresep-r-i.eresep-r-i-racikan',
            [
                // 'RIpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Data Pasien Rawat Jalan',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'ICD 10',
            ]
        );
    }
    // select data end////////////////


}
