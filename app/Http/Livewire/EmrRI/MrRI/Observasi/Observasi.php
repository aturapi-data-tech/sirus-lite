<?php

namespace App\Http\Livewire\EmrRI\MrRI\Observasi;


use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

use App\Http\Traits\EmrRI\EmrRITrait;
use App\Http\Traits\customErrorMessagesTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Cache\LockTimeoutException;

class Observasi extends Component
{
    use WithPagination, EmrRITrait, customErrorMessagesTrait;

    // listener from blade////////////////
    protected $listeners = [
        'syncronizeAssessmentPerawatRIFindData' => 'mount'
    ];

    //////////////////////////////
    // Ref on top bar
    //////////////////////////////



    // dataDaftarRi RJ
    public $riHdrNoRef;

    public array $dataDaftarRi = [];

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

    public array $observasi =
    [
        "tandaVitalTab" => "Observasi Lanjutan",
        "tandaVital" => [],

    ];
    //////////////////////////////////////////////////////////////////////


    protected $rules = [
        'observasiLanjutan.cairan'          => 'nullable|string',
        'observasiLanjutan.tetesan'         => 'nullable|string',
        'observasiLanjutan.sistolik'        => 'required|numeric',
        'observasiLanjutan.distolik'        => 'required|numeric',
        'observasiLanjutan.frekuensiNafas'  => 'required|numeric',
        'observasiLanjutan.frekuensiNadi'   => 'required|numeric',
        'observasiLanjutan.suhu'            => 'required|numeric',
        'observasiLanjutan.spo2'            => 'required|numeric',
        'observasiLanjutan.gda'             => 'nullable|numeric',
        'observasiLanjutan.gcs'             => 'nullable|numeric',
        'observasiLanjutan.waktuPemeriksaan' => 'required|date_format:d/m/Y H:i:s',
        'observasiLanjutan.pemeriksa'       => 'required|string',
    ];

    protected $validationAttributes = [
        'observasiLanjutan.sistolik' => 'tekanan darah sistolik',
        'observasiLanjutan.distolik' => 'tekanan darah diastolik',
        'observasiLanjutan.frekuensiNafas' => 'frekuensi napas',
        'observasiLanjutan.frekuensiNadi'  => 'frekuensi nadi',
        'observasiLanjutan.suhu' => 'suhu tubuh',
        'observasiLanjutan.spo2' => 'SpO₂',
        'observasiLanjutan.waktuPemeriksaan' => 'waktu pemeriksaan',
        'observasiLanjutan.pemeriksa' => 'pemeriksa',
    ];

    protected $messages = [
        'observasiLanjutan.sistolik.required'        => 'Tekanan darah sistolik wajib diisi.',
        'observasiLanjutan.sistolik.numeric'         => 'Tekanan darah sistolik harus berupa angka.',

        'observasiLanjutan.distolik.required'        => 'Tekanan darah diastolik wajib diisi.',
        'observasiLanjutan.distolik.numeric'         => 'Tekanan darah diastolik harus berupa angka.',

        'observasiLanjutan.frekuensiNafas.required'  => 'Frekuensi napas wajib diisi.',
        'observasiLanjutan.frekuensiNafas.numeric'   => 'Frekuensi napas harus berupa angka.',

        'observasiLanjutan.frekuensiNadi.required'   => 'Frekuensi nadi wajib diisi.',
        'observasiLanjutan.frekuensiNadi.numeric'    => 'Frekuensi nadi harus berupa angka.',

        'observasiLanjutan.suhu.required'            => 'Suhu tubuh wajib diisi.',
        'observasiLanjutan.suhu.numeric'             => 'Suhu tubuh harus berupa angka.',

        'observasiLanjutan.spo2.required'            => 'Saturasi O₂ (SpO₂) wajib diisi.',
        'observasiLanjutan.spo2.numeric'             => 'SpO₂ harus berupa angka.',

        'observasiLanjutan.waktuPemeriksaan.required' => 'Waktu pemeriksaan wajib diisi.',
        'observasiLanjutan.waktuPemeriksaan.date_format' => 'Format waktu pemeriksaan harus d/m/Y H:i:s.',

        'observasiLanjutan.pemeriksa.required'       => 'Nama pemeriksa wajib diisi.',
    ];



    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////
    public function updated($propertyName)
    {
        // dd($propertyName);
        // $this->validateOnly($propertyName);
        // $this->store();
    }




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
    private function validateDataObservasiRi(): void
    {


        // Proses Validasi///////////////////////////////////////////
        try {
            $this->validate($this->rules, $this->messages, $this->validationAttributes);
        } catch (\Illuminate\Validation\ValidationException $e) {
            toastr()->positionClass('toast-top-left')->addError("Lakukan Pengecekan kembali Input Data.");
            throw $e;
        }
    }


    // insert and update record start////////////////
    public function store()
    {
        $riHdrNo = $this->dataDaftarRi['riHdrNo'] ?? $this->riHdrNoRef ?? null;
        if (!$riHdrNo) {
            toastr()->positionClass('toast-top-left')->addError("riHdrNo kosong.");
            return;
        }

        try {
            $this->withRiLock($riHdrNo, function () use ($riHdrNo) {
                $fresh = $this->findDataRI($riHdrNo) ?: [];

                // Guard: pastikan bentuk array
                $freshObservasi    = is_array($fresh['observasi'] ?? null) ? $fresh['observasi'] : [];
                $incomingObservasi = is_array($this->dataDaftarRi['observasi'] ?? null) ? $this->dataDaftarRi['observasi'] : [];

                // --- FORCE REPLACE untuk list numerik agar delete/reorder tersimpan ---
                // 1) Reindex incoming list
                if (
                    isset($incomingObservasi['observasiLanjutan']['tandaVital']) &&
                    is_array($incomingObservasi['observasiLanjutan']['tandaVital'])
                ) {
                    $incomingObservasi['observasiLanjutan']['tandaVital'] =
                        array_values($incomingObservasi['observasiLanjutan']['tandaVital']);
                }

                // 2) Hapus node list di fresh supaya tidak di-merge item lama
                if (
                    isset($freshObservasi['observasiLanjutan']) &&
                    is_array($freshObservasi['observasiLanjutan']) &&
                    array_key_exists('tandaVital', $freshObservasi['observasiLanjutan'])
                ) {
                    unset($freshObservasi['observasiLanjutan']['tandaVital']);
                }

                // Deep merge: hanya overwrite key yang dikirim user
                $fresh['observasi'] = array_replace_recursive($freshObservasi, $incomingObservasi);


                // Cukup update, transaction sudah di-handle withRiLock()
                $this->updateJsonRI($riHdrNo, $fresh);

                // Sinkronkan state komponen
                $this->dataDaftarRi = $fresh;
            });
        } catch (LockTimeoutException $e) {
            toastr()->positionClass('toast-top-left')->addError('Sistem sibuk, gagal memperoleh lock. Coba lagi.');
            return;
        } catch (\Throwable $e) {
            toastr()->positionClass('toast-top-left')->addError('Gagal menyimpan: ' . $e->getMessage());
            return;
        }

        $this->emit('syncronizeAssessmentPerawatRIFindData');
        toastr()->positionClass('toast-top-left')->addSuccess("Observasi berhasil disimpan.");
    }



    private function findData($riHdrNo): void
    {
        // Ambil dari DB (fallback array kosong)
        $this->dataDaftarRi = $this->findDataRI($riHdrNo) ?: [];

        // Pastikan riHdrNo selalu ada
        $this->dataDaftarRi['riHdrNo'] = $this->dataDaftarRi['riHdrNo'] ?? $riHdrNo;

        // Pastikan node dasar ada
        if (!isset($this->dataDaftarRi['observasi']) || !is_array($this->dataDaftarRi['observasi'])) {
            $this->dataDaftarRi['observasi'] = [];
        }
        // Pakai reference biar singkat
        $obs = &$this->dataDaftarRi['observasi'];

        // Default struktur observasiLanjutan
        $defaultObservasi = [
            'tandaVitalTab' => 'Observasi Lanjutan',
            'tandaVital'    => [],
        ];

        if (!isset($obs['observasiLanjutan']) || !is_array($obs['observasiLanjutan'])) {
            $obs['observasiLanjutan'] = $defaultObservasi;
        }
        $lanjutan = &$obs['observasiLanjutan'];

        // Pastikan tandaVital array & normalisasi item (hindari stdClass)
        $tv = $lanjutan['tandaVital'] ?? [];
        if (!is_array($tv)) {
            $tv = [];
        }
        $lanjutan['tandaVital'] = collect($tv)
            ->map(fn($row) => is_array($row) ? $row : (array) $row)
            ->values()
            ->all();

        // (Opsional) Inisialisasi form input kalau kosong
        if (empty($this->observasiLanjutan) || !is_array($this->observasiLanjutan)) {
            $this->observasiLanjutan = [
                'cairan'           => '',
                'tetesan'          => '',
                'sistolik'         => '',
                'distolik'         => '',
                'frekuensiNafas'   => '',
                'frekuensiNadi'    => '',
                'suhu'             => '',
                'spo2'             => '',
                'gda'              => '',
                'gcs'              => '',
                'waktuPemeriksaan' => '',
                'pemeriksa'        => '',
            ];
        }
    }


    public function addObservasiLanjutan()
    {
        // entry Pemeriksa
        $this->observasiLanjutan['pemeriksa'] = auth()->user()->myuser_name;

        // validasi
        $this->validateDataObservasiRi();
        // check exist
        $cekObservasiLanjutan = collect($this->dataDaftarRi['observasi']['observasiLanjutan']['tandaVital'])
            ->where("waktuPemeriksaan", '=', $this->observasiLanjutan['waktuPemeriksaan'])
            ->count();
        if (!$cekObservasiLanjutan) {
            $this->dataDaftarRi['observasi']['observasiLanjutan']['tandaVital'][] = [
                'cairan'           => (string) $this->observasiLanjutan['cairan'],
                'tetesan'          => (string) $this->observasiLanjutan['tetesan'],
                'sistolik'         => (int) $this->observasiLanjutan['sistolik'],
                'distolik'         => (int) $this->observasiLanjutan['distolik'],
                'frekuensiNafas'   => (int) $this->observasiLanjutan['frekuensiNafas'],
                'frekuensiNadi'    => (int) $this->observasiLanjutan['frekuensiNadi'],
                'suhu'             => (float) $this->observasiLanjutan['suhu'],
                'spo2'             => (int) $this->observasiLanjutan['spo2'],
                'gda'              => $this->observasiLanjutan['gda'] === '' ? null : (float) $this->observasiLanjutan['gda'],
                'gcs'              => $this->observasiLanjutan['gcs'] === '' ? null : (int) $this->observasiLanjutan['gcs'],
                'waktuPemeriksaan' => $this->observasiLanjutan['waktuPemeriksaan'],
                'pemeriksa'        => (string) $this->observasiLanjutan['pemeriksa'],
            ];

            $this->dataDaftarRi['observasi']['observasiLanjutan']['tandaVitalLog'] =
                [
                    'userLogDesc' => 'Form Entry observasiLanjutan',
                    'userLog' => auth()->user()->myuser_name,
                    'userLogDate' => Carbon::now(config('app.timezone'))->format('d/m/Y H:i:s')
                ];

            $this->store();
            // reset observasiLanjutan
            $this->reset(['observasiLanjutan']);
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Observasi Sudah ada.");
        }
    }

    public function removeObservasiLanjutan($waktuPemeriksaan)
    {
        $target   = trim((string) $waktuPemeriksaan);
        $list     = $this->dataDaftarRi['observasi']['observasiLanjutan']['tandaVital'] ?? [];
        $filtered = [];
        $removed  = false;
        foreach ($list as $row) {
            $rowTime = trim((string) ($row['waktuPemeriksaan'] ?? ''));
            if (!$removed && $rowTime === $target) {
                $removed = true;
                continue;
            }

            $filtered[] = is_array($row) ? $row : (array) $row;
        }

        if (!$removed) {
            toastr()->positionClass('toast-top-left')->addError('Data observasi tidak ditemukan.');
            return;
        }
        // reindex
        $this->dataDaftarRi['observasi']['observasiLanjutan']['tandaVital'] =
            collect($filtered)->values()->all();

        $this->dataDaftarRi['observasi']['observasiLanjutan']['tandaVitalLog'] = [
            'userLogDesc' => 'Hapus observasi lanjutan',
            'userLog'     => auth()->user()->myuser_name,
            'userLogDate' => Carbon::now(config('app.timezone'))->format('d/m/Y H:i:s'),
        ];

        $this->store();
    }


    public function setWaktuPemeriksaan($myTime)
    {
        $this->observasiLanjutan['waktuPemeriksaan'] = $myTime;
    }


    private function withRiLock(string $riHdrNo, callable $fn): void
    {
        $lockKey = "ri:{$riHdrNo}";
        Cache::lock($lockKey, 60)->block(10, function () use ($fn) {
            DB::transaction(function () use ($fn) {
                $fn();
            });
        });
    }

    // when new form instance
    public function mount()
    {
        $this->findData($this->riHdrNoRef);
    }



    // select data start////////////////
    public function render()
    {

        return view(
            'livewire.emr-r-i.mr-r-i.observasi.observasi',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Observasi',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Pasien RI',
            ]
        );
    }
    // select data end////////////////


}
