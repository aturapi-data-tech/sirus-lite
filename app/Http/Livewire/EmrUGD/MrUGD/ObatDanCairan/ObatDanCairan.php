<?php

namespace App\Http\Livewire\EmrUGD\MrUGD\ObatDanCairan;

use Illuminate\Support\Facades\DB;
use \Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

use App\Http\Traits\EmrUGD\EmrUGDTrait;
use App\Http\Traits\LOV\LOVProduct\LOVProductTrait;

class ObatDanCairan extends Component
{
    use WithPagination, EmrUGDTrait, LOVProductTrait;

    public $rjNoRef;
    public array $dataDaftarUgd = [];

    // Template untuk entry baru
    public array $obatDanCairan = [
        "namaObatAtauJenisCairan" => "",
        "jumlah" => "",
        "dosis" => "",
        "rute" => "",
        "keterangan" => "",
        "waktuPemberian" => "",
        "pemeriksa" => ""
    ];

    public array $observasi = [
        "pemberianObatDanCairanTab" => "Pemberian Obat Dan Cairan",
        "pemberianObatDanCairan" => [],
    ];

    protected $rules = [
        'obatDanCairan.namaObatAtauJenisCairan' => 'required',
        'obatDanCairan.jumlah'                  => 'required|numeric',
        'obatDanCairan.dosis'                   => 'required',
        'obatDanCairan.rute'                    => 'required',
        'obatDanCairan.keterangan'              => 'required',
        'obatDanCairan.waktuPemberian'          => 'required|date_format:d/m/Y H:i:s',
        'obatDanCairan.pemeriksa'               => 'required',
    ];

    protected $messages = [
        'required'   => ':attribute wajib diisi.',
        'numeric'    => ':attribute harus berupa angka.',
        'date_format' => ':attribute harus dengan format dd/mm/yyyy hh:mm:ss',
    ];

    protected $validationAttributes = [
        'obatDanCairan.namaObatAtauJenisCairan' => 'Nama obat / jenis cairan',
        'obatDanCairan.jumlah'                  => 'Jumlah',
        'obatDanCairan.dosis'                   => 'Dosis',
        'obatDanCairan.rute'                    => 'Rute pemberian',
        'obatDanCairan.keterangan'              => 'Keterangan',
        'obatDanCairan.waktuPemberian'          => 'Waktu pemberian',
        'obatDanCairan.pemeriksa'               => 'Pemeriksa',
    ];

    // ==================== MAIN METHODS ====================

    public function addObatDanCairan(): void
    {

        // Set pemeriksa otomatis
        $this->obatDanCairan['pemeriksa'] = auth()->user()->myuser_name ?? '';

        // Validasi form
        $this->validateDataObatDanCairanUgd();

        $rjNo = $this->dataDaftarUgd['rjNo'] ?? $this->rjNoRef ?? null;
        if (!$rjNo) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Nomor registrasi UGD tidak valid.');
            return;
        }

        try {
            DB::transaction(function () use ($rjNo) {
                // Load data terbaru dari JSON
                $fresh = $this->findDataUGD($rjNo) ?: [];

                // Inisialisasi struktur jika belum ada
                if (!isset($fresh['observasi']['obatDanCairan']['pemberianObatDanCairan'])) {
                    $fresh['observasi']['obatDanCairan']['pemberianObatDanCairan'] = [];
                }

                $list = $fresh['observasi']['obatDanCairan']['pemberianObatDanCairan'];

                // Cek duplikasi berdasarkan waktuPemberian
                $exists = collect($list)->firstWhere('waktuPemberian', $this->obatDanCairan['waktuPemberian']);
                if ($exists) {
                    toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                        ->addInfo('Data pada waktu tersebut sudah ada.');
                    return;
                }

                // Tambahkan data baru dengan ID unik
                $newEntry = [
                    'id' => uniqid('obat_'),
                    "namaObatAtauJenisCairan" => $this->obatDanCairan['namaObatAtauJenisCairan'],
                    "jumlah"        => $this->obatDanCairan['jumlah'],
                    "dosis"         => $this->obatDanCairan['dosis'],
                    "rute"          => $this->obatDanCairan['rute'],
                    "keterangan"    => $this->obatDanCairan['keterangan'],
                    "waktuPemberian" => $this->obatDanCairan['waktuPemberian'],
                    "pemeriksa"     => $this->obatDanCairan['pemeriksa'],
                ];

                $list[] = $newEntry;
                $fresh['observasi']['obatDanCairan']['pemberianObatDanCairan'] = array_values($list);

                // Simpan ke JSON
                $this->updateJsonUGD($rjNo, $fresh);

                // Update data lokal
                $this->dataDaftarUgd = $fresh;
            });

            // Reset form dan LOV
            $this->resetObatDanCairanForm();

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addSuccess('Obat & Cairan berhasil ditambahkan dan disimpan.');
        } catch (\Exception $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Gagal menyimpan data Obat & Cairan: ' . $e->getMessage());
        }
    }

    public function removeObatDanCairan($waktuPemberian): void
    {

        $rjNo = $this->dataDaftarUgd['rjNo'] ?? $this->rjNoRef ?? null;
        if (!$rjNo) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Nomor registrasi UGD tidak valid.');
            return;
        }

        try {
            DB::transaction(function () use ($rjNo, $waktuPemberian) {
                // Load data terbaru dari JSON
                $fresh = $this->findDataUGD($rjNo) ?: [];

                if (!isset($fresh['observasi']['obatDanCairan']['pemberianObatDanCairan'])) {
                    toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                        ->addError('Data tidak ditemukan.');
                    return;
                }

                // Hapus data berdasarkan waktuPemberian
                $list = collect($fresh['observasi']['obatDanCairan']['pemberianObatDanCairan'])
                    ->reject(fn($row) => (string)($row['waktuPemberian'] ?? '') === (string)$waktuPemberian)
                    ->values()
                    ->all();

                $fresh['observasi']['obatDanCairan']['pemberianObatDanCairan'] = $list;

                // Simpan ke JSON
                $this->updateJsonUGD($rjNo, $fresh);

                // Update data lokal
                $this->dataDaftarUgd = $fresh;
            });

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addSuccess('Obat & Cairan berhasil dihapus.');
        } catch (\Exception $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Gagal menghapus data Obat & Cairan: ' . $e->getMessage());
        }
    }

    /**
     * Set waktu pemberian dengan Carbon::now() format dd/mm/yyyy H:i:s
     */
    public function setWaktuPemberian(): void
    {
        $this->obatDanCairan['waktuPemberian'] = Carbon::now()->format('d/m/Y H:i:s');
    }

    /**
     * Set waktu pemberian custom (jika masih diperlukan)
     */
    public function setCustomWaktuPemberian($myTime): void
    {
        $this->obatDanCairan['waktuPemberian'] = $myTime;
    }

    // ==================== VALIDATION METHODS ====================

    private function validateDataObatDanCairanUgd(): void
    {
        try {
            $this->validate($this->rules, $this->messages, $this->validationAttributes);
        } catch (ValidationException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError($e->validator->errors()->first());
            throw $e;
        }
    }

    // ==================== FORM MANAGEMENT ====================

    private function resetObatDanCairanForm(): void
    {

        $this->reset([
            'obatDanCairan',
            'collectingMyProduct'
        ]);
        $this->resetValidation();
    }

    // ==================== DATA LOADING ====================

    private function findData($rjno): void
    {
        $this->dataDaftarUgd = $this->findDataUGD($rjno) ?: [];

        if (!isset($this->dataDaftarUgd['observasi']) || !is_array($this->dataDaftarUgd['observasi'])) {
            $this->dataDaftarUgd['observasi'] = [];
        }

        if (!isset($this->dataDaftarUgd['observasi']['obatDanCairan'])) {
            $this->dataDaftarUgd['observasi']['obatDanCairan'] = $this->observasi;
        }

        // Generate ID untuk data yang sudah ada jika belum ada ID
        $this->generateIdsForExistingObatDanCairan();
    }

    private function generateIdsForExistingObatDanCairan(): void
    {
        if (isset($this->dataDaftarUgd['observasi']['obatDanCairan']['pemberianObatDanCairan'])) {
            foreach ($this->dataDaftarUgd['observasi']['obatDanCairan']['pemberianObatDanCairan'] as &$obat) {
                if (!isset($obat['id'])) {
                    $obat['id'] = uniqid('obat_');
                }
            }
        }
    }

    // ==================== LOV INTEGRATION METHODS ====================

    /**
     * Override method dari LOVProductTrait untuk menyesuaikan dengan form ObatDanCairan
     */
    public function clickdataProductLov(): void
    {
        $this->dataProductLovStatus = true;
        $this->dataProductLov = [];
    }

    /**
     * Override method untuk sync data dari LOV ke form ObatDanCairan
     */
    private function addProduct($ProductId, $ProductName, $ProductPrice): void
    {
        $this->collectingMyProduct = [
            'ProductId' => $ProductId,
            'ProductName' => $ProductName,
            'ProductPrice' => $ProductPrice,
        ];

        // Auto-fill form dengan data dari LOV
        $this->obatDanCairan['namaObatAtauJenisCairan'] = $ProductName;

        // Set waktu pemberian otomatis saat memilih obat
        $this->setWaktuPemberian();

        // Optional: Set nilai default untuk field lainnya
        if (empty($this->obatDanCairan['jumlah'])) {
            $this->obatDanCairan['jumlah'] = 1;
        }
    }

    /**
     * Handle perubahan pada field nama obat untuk reset LOV jika kosong
     */
    public function updated($propertyName): void
    {
        if ($propertyName === 'obatDanCairan.namaObatAtauJenisCairan') {
            if (empty($this->obatDanCairan['namaObatAtauJenisCairan'])) {
                $this->resetObatDanCairanForm();
            }
        }
    }

    /**
     * Sync data dari LOV ke form sebelum render
     */
    private function syncLOVToForm(): void
    {
        if (!empty($this->collectingMyProduct)) {
            $this->obatDanCairan['namaObatAtauJenisCairan'] = $this->collectingMyProduct['ProductName'] ?? '';
        }
    }

    // ==================== UTILITY METHODS ====================



    public function mount(): void
    {
        $this->findData($this->rjNoRef);
        // Set waktu default saat mount
        $this->setWaktuPemberian();
    }

    public function render()
    {
        // Sync data dari LOV ke form sebelum render
        $this->syncLOVToForm();

        return view(
            'livewire.emr-u-g-d.mr-u-g-d.obat-dan-cairan.obat-dan-cairan',
            [
                'myTitle' => 'Obat Dan Cairan',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Pasien UGD',
            ]
        );
    }
}
