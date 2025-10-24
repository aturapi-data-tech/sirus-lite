<?php

namespace App\Http\Livewire\EmrUGD\MrUGD\Observasi;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Cache\LockTimeoutException;
use \Illuminate\Validation\ValidationException;

use Livewire\Component;
use Livewire\WithPagination;

use App\Http\Traits\EmrUGD\EmrUGDTrait;
use App\Http\Traits\customErrorMessagesTrait;

class Observasi extends Component
{
    use WithPagination, EmrUGDTrait, customErrorMessagesTrait;

    // listener from blade////////////////
    protected $listeners = [];

    //////////////////////////////
    // Ref on top bar
    //////////////////////////////

    // dataDaftarUgd RJ
    public $rjNoRef;

    public array $dataDaftarUgd = [];

    public array $observasiLanjutan = [
        "cairan" => "",
        "tetesan" => "",
        "sistolik" => "", //number
        "distolik" => "", //number
        "frekuensiNafas" => "", //number
        "frekuensiNadi" => "", //number
        "suhu" => "", //number
        "spo2" => "", //number
        "gda" => "", //number
        "gcs" => "", //number
        "waktuPemeriksaan" => "", //date dd/mm/yyyy hh24:mi:ss
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

    public array $observasi =
    [
        "tandaVitalTab" => "Observasi Lanjutan",
        "tandaVital" => [],

    ];
    //////////////////////////////////////////////////////////////////////


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



    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////





    // resert input private////////////////
    private function resetInputFields(): void
    {

        // resert validation
        $this->resetValidation();
        // resert input kecuali
        $this->reset(['']);
    }





    // ////////////////
    // RJ Logic
    // ////////////////


    // validate Data RJ//////////////////////////////////////////////////
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


    // insert and update record start////////////////
    public function store()
    {
        if (!$this->checkUgdStatus()) return;

        $rjNo = $this->dataDaftarUgd['rjNo'] ?? $this->rjNoRef ?? null;
        if (!$rjNo) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('rjNo kosong.');
            return;
        }

        $lockKey = "ugd:{$rjNo}";
        try {
            Cache::lock($lockKey, 5)->block(3, function () use ($rjNo) {
                DB::transaction(function () use ($rjNo) {
                    $fresh = $this->findDataUGD($rjNo) ?: [];

                    if (!isset($fresh['observasi']) || !is_array($fresh['observasi'])) {
                        $fresh['observasi'] = [];
                    }
                    // patch hanya subtree kita
                    if (isset($this->dataDaftarUgd['observasi']['observasiLanjutan'])) {
                        $fresh['observasi']['observasiLanjutan'] = $this->dataDaftarUgd['observasi']['observasiLanjutan'];
                    }

                    $this->updateJsonUGD($rjNo, $fresh);
                    $this->dataDaftarUgd = $fresh;
                });
            });

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Observasi berhasil disimpan.");
        } catch (LockTimeoutException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Sistem sibuk, gagal memperoleh lock. Coba lagi.');
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Gagal menyimpan.');
        }
    }

    private function updateDataUgd($rjNo): void
    {
        // update table trnsaksi
        // DB::table('rstxn_ugdhdrs')
        //     ->where('rj_no', $rjNo)
        //     ->update([
        //         'datadaftarugd_json' => json_encode($this->dataDaftarUgd, true),
        //         'datadaftarUgd_xml' => ArrayToXml::convert($this->dataDaftarUgd),
        //     ]);

        $this->updateJsonUGD($rjNo, $this->dataDaftarUgd);

        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Observasi berhasil disimpan.");
    }
    // insert and update record end////////////////


    private function findData($rjno): void
    {
        $this->dataDaftarUgd = $this->findDataUGD($rjno) ?: [];

        if (!isset($this->dataDaftarUgd['observasi']) || !is_array($this->dataDaftarUgd['observasi'])) {
            $this->dataDaftarUgd['observasi'] = [];
        }
        if (!isset($this->dataDaftarUgd['observasi']['observasiLanjutan']) || !is_array($this->dataDaftarUgd['observasi']['observasiLanjutan'])) {
            $this->dataDaftarUgd['observasi']['observasiLanjutan'] = $this->observasi; // punya keys: tandaVitalTab, tandaVital
        }
        if (!isset($this->dataDaftarUgd['observasi']['observasiLanjutan']['tandaVital']) || !is_array($this->dataDaftarUgd['observasi']['observasiLanjutan']['tandaVital'])) {
            $this->dataDaftarUgd['observasi']['observasiLanjutan']['tandaVital'] = [];
        }
    }



    public function addObservasiLanjutan()
    {
        if (!$this->checkUgdStatus()) return;

        $this->observasiLanjutan['pemeriksa'] = auth()->user()->myuser_name ?? '';
        $this->validateDataObservasiUgd();

        $rjNo = $this->dataDaftarUgd['rjNo'] ?? $this->rjNoRef ?? null;
        if (!$rjNo) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('rjNo kosong.');
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

                    // idempoten berdasarkan waktu
                    $exists = collect($list)->firstWhere('waktuPemeriksaan', $this->observasiLanjutan['waktuPemeriksaan']);
                    if ($exists) {
                        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addInfo('Data pada waktu tersebut sudah ada.');
                        return;
                    }

                    $list[] = [
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

                    $fresh['observasi']['observasiLanjutan']['tandaVital'] = array_values($list);
                    $fresh['observasi']['observasiLanjutan']['tandaVitalLog'] = [
                        'userLogDesc' => 'Form Entry Observasi Lanjutan',
                        'userLog'     => auth()->user()->myuser_name ?? '',
                        'userLogDate' => now(config('app.timezone'))->format('d/m/Y H:i:s'),
                    ];

                    $this->updateJsonUGD($rjNo, $fresh);
                    $this->dataDaftarUgd = $fresh;
                });
            });

            $this->reset(['observasiLanjutan']);
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('Observasi Lanjutan berhasil ditambahkan.');
        } catch (LockTimeoutException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Sistem sibuk, gagal memperoleh lock. Coba lagi.');
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Gagal menambah Observasi Lanjutan.');
        }
    }
    public function removeObservasiLanjutan($waktuPemeriksaan)
    {
        if (!$this->checkUgdStatus()) return;

        $rjNo = $this->dataDaftarUgd['rjNo'] ?? $this->rjNoRef ?? null;
        if (!$rjNo) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('rjNo kosong.');
            return;
        }

        $lockKey = "ugd:{$rjNo}";
        try {
            Cache::lock($lockKey, 5)->block(3, function () use ($rjNo, $waktuPemeriksaan) {
                DB::transaction(function () use ($rjNo, $waktuPemeriksaan) {
                    $fresh = $this->findDataUGD($rjNo) ?: [];

                    if (!isset($fresh['observasi']['observasiLanjutan']['tandaVital'])) {
                        return;
                    }

                    $list = collect($fresh['observasi']['observasiLanjutan']['tandaVital'])
                        ->reject(fn($row) => (string)($row['waktuPemeriksaan'] ?? '') === (string)$waktuPemeriksaan)
                        ->values()->all();

                    $fresh['observasi']['observasiLanjutan']['tandaVital'] = $list;
                    $fresh['observasi']['observasiLanjutan']['tandaVitalLog'] = [
                        'userLogDesc' => 'Hapus Observasi Lanjutan',
                        'userLog'     => auth()->user()->myuser_name ?? '',
                        'userLogDate' => now(config('app.timezone'))->format('d/m/Y H:i:s'),
                    ];

                    $this->updateJsonUGD($rjNo, $fresh);
                    $this->dataDaftarUgd = $fresh;
                });
            });

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('Observasi Lanjutan dihapus.');
        } catch (LockTimeoutException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Sistem sibuk, gagal memperoleh lock. Coba lagi.');
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Gagal menghapus Observasi Lanjutan.');
        }
    }

    public function setWaktuPemeriksaan($myTime)
    {
        $this->observasiLanjutan['waktuPemeriksaan'] = $myTime;
    }

    private function checkUgdStatus(): bool
    {
        $row = DB::table('rstxn_ugdhdrs')->select('rj_status')
            ->where('rj_no', $this->rjNoRef)->first();

        if (!$row || $row->rj_status !== 'A') {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Pasien Sudah Pulang, Transaksi Terkunci.');
            return false;
        }
        return true;
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
            'livewire.emr-u-g-d.mr-u-g-d.observasi.observasi',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Observasi',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Pasien UGD',
            ]
        );
    }
    // select data end////////////////


}
