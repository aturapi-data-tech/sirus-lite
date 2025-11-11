<?php

namespace App\Http\Livewire\EmrUGD\MrUGD\Observasi;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Cache\LockTimeoutException;
use \Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

use App\Http\Traits\EmrUGD\EmrUGDTrait;
use App\Http\Traits\customErrorMessagesTrait;

class Observasi extends Component
{
    use WithPagination, EmrUGDTrait, customErrorMessagesTrait;

    protected $listeners = [];

    public $rjNoRef;
    public array $dataDaftarUgd = [];
    public $disabledPropertyRjStatus = false;

    public array $observasiLanjutan = [
        "cairan" => "",
        "tetesan" => "",
        "sistolik" => "",
        "distolik" => "",
        "frekuensiNafas" => "",
        "frekuensiNadi" => "",
        "suhu" => "",
        "spo2" => "",
        "gda" => "",
        "gcs" => "",
        "waktuPemeriksaan" => "",
        "pemeriksa" => ""
    ];

    protected array $messages = [
        'required'    => ':attribute wajib diisi.',
        'numeric'     => ':attribute harus berupa angka.',
        'date_format' => ':attribute harus dengan format dd/mm/yyyy hh:mm:ss',
    ];

    protected array $validationAttributes = [
        'observasiLanjutan.cairan'            => 'Cairan',
        'observasiLanjutan.tetesan'           => 'Tetesan',
        'observasiLanjutan.sistolik'          => 'TD sistolik',
        'observasiLanjutan.distolik'          => 'TD diastolik',
        'observasiLanjutan.frekuensiNafas'    => 'Frekuensi napas',
        'observasiLanjutan.frekuensiNadi'     => 'Frekuensi nadi',
        'observasiLanjutan.suhu'              => 'Suhu',
        'observasiLanjutan.spo2'              => 'SpO₂',
        'observasiLanjutan.gda'               => 'GDA',
        'observasiLanjutan.gcs'               => 'GCS',
        'observasiLanjutan.waktuPemeriksaan'  => 'Waktu pemeriksaan',
        'observasiLanjutan.pemeriksa'         => 'Pemeriksa',
    ];

    public array $observasi = [
        "tandaVitalTab" => "Observasi Lanjutan",
        "tandaVital" => [],
    ];

    protected $rules = [
        'observasiLanjutan.cairan' => '',
        'observasiLanjutan.tetesan' => '',
        'observasiLanjutan.sistolik' => 'required|numeric',
        'observasiLanjutan.distolik' => 'required|numeric',
        'observasiLanjutan.frekuensiNafas' => 'required|numeric',
        'observasiLanjutan.frekuensiNadi' => 'required|numeric',
        'observasiLanjutan.suhu' => 'required|numeric',
        'observasiLanjutan.spo2' => 'required|numeric',
        'observasiLanjutan.gda' => '',
        'observasiLanjutan.gcs' => '',
        'observasiLanjutan.waktuPemeriksaan' => 'required|date_format:d/m/Y H:i:s',
        'observasiLanjutan.pemeriksa' => 'required',
    ];

    private function resetInputFields(): void
    {
        $this->resetValidation();
        $this->reset(['observasiLanjutan']);
    }

    private function validateDataObservasiUgd(): void
    {
        try {
            $this->validate($this->rules, $this->messages, $this->validationAttributes);
        } catch (ValidationException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError($e->validator->errors()->first());
            throw $e;
        }
    }

    public function addObservasiLanjutan(): void
    {

        $this->observasiLanjutan['pemeriksa'] = auth()->user()->myuser_name ?? '';
        $this->validateDataObservasiUgd();

        $rjNo = $this->dataDaftarUgd['rjNo'] ?? $this->rjNoRef ?? null;
        if (!$rjNo) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Nomor registrasi UGD tidak valid.');
            return;
        }

        $lockKey = "ugd:{$rjNo}";
        try {
            Cache::lock($lockKey, 5)->block(3, function () use ($rjNo) {
                DB::transaction(function () use ($rjNo) {
                    $fresh = $this->findDataUGD($rjNo) ?: [];

                    if (!isset($fresh['observasi']['observasiLanjutan']) || !is_array($fresh['observasi']['observasiLanjutan'])) {
                        $fresh['observasi']['observasiLanjutan'] = [
                            'tandaVitalTab' => 'Observasi Lanjutan',
                            'tandaVital'    => [],
                        ];
                    }

                    $list = $fresh['observasi']['observasiLanjutan']['tandaVital'];

                    // Cek duplikasi berdasarkan waktu
                    $exists = collect($list)->firstWhere('waktuPemeriksaan', $this->observasiLanjutan['waktuPemeriksaan']);
                    if ($exists) {
                        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addInfo('Data pada waktu tersebut sudah ada.');
                        return;
                    }

                    // Tambahkan data baru dengan ID unik
                    $newEntry = [
                        'id' => uniqid('observasi_'),
                        "cairan"            => $this->observasiLanjutan['cairan'],
                        "tetesan"           => $this->observasiLanjutan['tetesan'],
                        "sistolik"          => $this->observasiLanjutan['sistolik'],
                        "distolik"          => $this->observasiLanjutan['distolik'],
                        "frekuensiNafas"    => $this->observasiLanjutan['frekuensiNafas'],
                        "frekuensiNadi"     => $this->observasiLanjutan['frekuensiNadi'],
                        "suhu"              => $this->observasiLanjutan['suhu'],
                        "spo2"              => $this->observasiLanjutan['spo2'],
                        "gda"               => $this->observasiLanjutan['gda'],
                        "gcs"               => $this->observasiLanjutan['gcs'],
                        "waktuPemeriksaan"  => $this->observasiLanjutan['waktuPemeriksaan'],
                        "pemeriksa"         => $this->observasiLanjutan['pemeriksa'],
                    ];

                    $list[] = $newEntry;
                    $fresh['observasi']['observasiLanjutan']['tandaVital'] = array_values($list);

                    // Tambahkan log
                    $fresh['observasi']['observasiLanjutan']['tandaVitalLog'] = [
                        'userLogDesc' => 'Form Entry Observasi Lanjutan',
                        'userLog'     => auth()->user()->myuser_name ?? '',
                        'userLogDate' => Carbon::now(config('app.timezone'))->format('d/m/Y H:i:s'),
                    ];

                    $this->updateJsonUGD($rjNo, $fresh);
                    $this->dataDaftarUgd = $fresh;
                });
            });

            $this->resetInputFields();
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('Observasi Lanjutan berhasil ditambahkan.');
        } catch (LockTimeoutException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Sistem sibuk, gagal memperoleh lock. Coba lagi.');
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Gagal menambah Observasi Lanjutan: ' . $e->getMessage());
        }
    }

    public function removeObservasiLanjutan($waktuPemeriksaan): void
    {

        $rjNo = $this->dataDaftarUgd['rjNo'] ?? $this->rjNoRef ?? null;
        if (!$rjNo) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Nomor registrasi UGD tidak valid.');
            return;
        }

        $lockKey = "ugd:{$rjNo}";
        try {
            Cache::lock($lockKey, 5)->block(3, function () use ($rjNo, $waktuPemeriksaan) {
                DB::transaction(function () use ($rjNo, $waktuPemeriksaan) {
                    $fresh = $this->findDataUGD($rjNo) ?: [];

                    if (!isset($fresh['observasi']['observasiLanjutan']['tandaVital'])) {
                        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Data tidak ditemukan.');
                        return;
                    }

                    // Hapus data berdasarkan waktu pemeriksaan
                    $list = collect($fresh['observasi']['observasiLanjutan']['tandaVital'])
                        ->reject(fn($row) => (string)($row['waktuPemeriksaan'] ?? '') === (string)$waktuPemeriksaan)
                        ->values()
                        ->all();

                    $fresh['observasi']['observasiLanjutan']['tandaVital'] = $list;

                    // Update log
                    $fresh['observasi']['observasiLanjutan']['tandaVitalLog'] = [
                        'userLogDesc' => 'Hapus Observasi Lanjutan',
                        'userLog'     => auth()->user()->myuser_name ?? '',
                        'userLogDate' => Carbon::now(config('app.timezone'))->format('d/m/Y H:i:s'),
                    ];

                    $this->updateJsonUGD($rjNo, $fresh);
                    $this->dataDaftarUgd = $fresh;
                });
            });

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('Observasi Lanjutan berhasil dihapus.');
        } catch (LockTimeoutException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Sistem sibuk, gagal memperoleh lock. Coba lagi.');
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Gagal menghapus Observasi Lanjutan: ' . $e->getMessage());
        }
    }

    /**
     * Set waktu pemeriksaan dengan Carbon::now()
     */
    public function setWaktuPemeriksaan(): void
    {
        $this->observasiLanjutan['waktuPemeriksaan'] = Carbon::now()->format('d/m/Y H:i:s');
    }

    /**
     * Set waktu pemeriksaan custom (jika diperlukan)
     */
    public function setCustomWaktuPemeriksaan($myTime): void
    {
        $this->observasiLanjutan['waktuPemeriksaan'] = $myTime;
    }



    private function findData($rjno): void
    {
        $this->dataDaftarUgd = $this->findDataUGD($rjno) ?: [];

        if (!isset($this->dataDaftarUgd['observasi']) || !is_array($this->dataDaftarUgd['observasi'])) {
            $this->dataDaftarUgd['observasi'] = [];
        }

        if (!isset($this->dataDaftarUgd['observasi']['observasiLanjutan'])) {
            $this->dataDaftarUgd['observasi']['observasiLanjutan'] = $this->observasi;
        }

        if (!isset($this->dataDaftarUgd['observasi']['observasiLanjutan']['tandaVital'])) {
            $this->dataDaftarUgd['observasi']['observasiLanjutan']['tandaVital'] = [];
        }

        // Generate ID untuk data yang sudah ada jika belum ada ID
        $this->generateIdsForExistingObservasi();
    }

    private function generateIdsForExistingObservasi(): void
    {
        if (isset($this->dataDaftarUgd['observasi']['observasiLanjutan']['tandaVital'])) {
            foreach ($this->dataDaftarUgd['observasi']['observasiLanjutan']['tandaVital'] as &$observasi) {
                if (!isset($observasi['id'])) {
                    $observasi['id'] = uniqid('observasi_');
                }
            }
        }
    }

    public function mount(): void
    {
        $this->findData($this->rjNoRef);
        // Set waktu default saat mount
        $this->setWaktuPemeriksaan();
    }

    public function render()
    {
        return view(
            'livewire.emr-u-g-d.mr-u-g-d.observasi.observasi',
            [
                'myTitle' => 'Observasi',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Pasien UGD',
            ]
        );
    }
}
