<?php

namespace App\Http\Livewire\EmrRI\MrRI\PengeluaranCairan;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Cache\LockTimeoutException;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

use App\Http\Traits\EmrRI\EmrRITrait;
use App\Http\Traits\customErrorMessagesTrait;
use Illuminate\Validation\ValidationException;

class PengeluaranCairan extends Component
{
    use WithPagination, EmrRITrait, customErrorMessagesTrait;

    protected $listeners = [
        'syncronizeAssessmentPerawatRIFindData' => 'mount'
    ];

    //////////////////////////////
    // Ref on top bar
    //////////////////////////////
    public $riHdrNoRef;
    public array $dataDaftarRi = [];

    public array $pengeluaranCairan = [
        "waktuPengeluaran" => "", // date dd/mm/yyyy hh24:mi:ss
        "jenisOutput" => "", // contoh: Urine / Feses / Muntah / Drain / NGT Output / Darah
        "volume" => "", // contoh: 250 ml
        "warnaKarakteristik" => "",
        "keterangan" => "",
        "pemeriksa" => ""
    ];

    public array $observasi = [
        "pengeluaranCairanTab" => "Pengeluaran Cairan",
        "pengeluaranCairan" => [],
    ];

    protected $rules = [
        'pengeluaranCairan.waktuPengeluaran'   => 'required|date_format:d/m/Y H:i:s',
        'pengeluaranCairan.jenisOutput'        => 'required',
        'pengeluaranCairan.volume'             => 'required|numeric',
        'pengeluaranCairan.warnaKarakteristik' => 'required',
        'pengeluaranCairan.keterangan'         => 'required',
        'pengeluaranCairan.pemeriksa'          => 'required',
    ];

    protected $messages = [
        'pengeluaranCairan.waktuPengeluaran.required'   => 'Waktu pengeluaran harus diisi.',
        'pengeluaranCairan.waktuPengeluaran.date_format' => 'Format waktu pengeluaran tidak sesuai (dd/mm/yyyy hh:mm:ss).',
        'pengeluaranCairan.jenisOutput.required'        => 'Jenis output harus diisi.',
        'pengeluaranCairan.volume.required'             => 'Volume harus diisi.',
        'pengeluaranCairan.volume.numeric'              => 'Volume harus berupa angka.',
        'pengeluaranCairan.warnaKarakteristik.required' => 'Warna atau karakteristik harus diisi.',
        'pengeluaranCairan.keterangan.required'         => 'Keterangan harus diisi.',
        'pengeluaranCairan.pemeriksa.required'          => 'Nama pemeriksa harus diisi.',
    ];

    protected $validationAttributes = [
        'pengeluaranCairan.waktuPengeluaran'   => 'waktu pengeluaran',
        'pengeluaranCairan.jenisOutput'        => 'jenis output',
        'pengeluaranCairan.volume'             => 'volume',
        'pengeluaranCairan.warnaKarakteristik' => 'warna/karakteristik',
        'pengeluaranCairan.keterangan'         => 'keterangan',
        'pengeluaranCairan.pemeriksa'          => 'pemeriksa',
    ];

    // Reset input fields
    private function resetInputFields(): void
    {
        $this->resetValidation();
        $this->reset(['pengeluaranCairan']);
        $this->pengeluaranCairan['pemeriksa'] = auth()->user()->myuser_name ?? '';
    }

    // Validate data
    private function validateDataPengeluaranCairan(): void
    {
        try {
            // pastikan messages & attributes kepakai
            $this->validate($this->rules, $this->messages, $this->validationAttributes);
        } catch (ValidationException $e) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError("Lakukan pengecekan kembali input data." . $e->getMessage());
            throw $e;
        }
    }


    // Find data
    private function findData($riHdrNo): void
    {
        $this->dataDaftarRi = $this->findDataRI($riHdrNo) ?: [];
        $this->dataDaftarRi['riHdrNo'] = $this->dataDaftarRi['riHdrNo'] ?? $riHdrNo;

        if (!isset($this->dataDaftarRi['observasi']) || !is_array($this->dataDaftarRi['observasi'])) {
            $this->dataDaftarRi['observasi'] = [];
        }

        if (!isset($this->dataDaftarRi['observasi']['pengeluaranCairan']) || !is_array($this->dataDaftarRi['observasi']['pengeluaranCairan'])) {
            $this->dataDaftarRi['observasi']['pengeluaranCairan'] = $this->observasi;
        }
    }

    // Add pengeluaran cairan
    public function addPengeluaranCairan()
    {
        $riHdrNo = $this->dataDaftarRi['riHdrNo'] ?? $this->riHdrNoRef ?? null;
        if (!$riHdrNo) {
            toastr()->positionClass('toast-top-left')->addError("riHdrNo kosong.");
            return;
        }

        // Set pemeriksa & validasi input
        $this->pengeluaranCairan['pemeriksa'] = auth()->user()->myuser_name;
        $this->validateDataPengeluaranCairan();

        $target = trim((string)$this->pengeluaranCairan['waktuPengeluaran']);

        try {
            $this->withRiLock($riHdrNo, function () use ($riHdrNo, $target) {
                // 1) Fresh data
                $fresh = $this->findDataRI($riHdrNo) ?: [];
                $fresh['riHdrNo'] = $fresh['riHdrNo'] ?? $riHdrNo;

                if (!isset($fresh['observasi']) || !is_array($fresh['observasi'])) {
                    $fresh['observasi'] = [];
                }
                if (!isset($fresh['observasi']['pengeluaranCairan']) || !is_array($fresh['observasi']['pengeluaranCairan'])) {
                    $fresh['observasi']['pengeluaranCairan'] = [
                        'pengeluaranCairanTab' => 'Pengeluaran Cairan',
                        'pengeluaranCairan'    => [],
                    ];
                }

                // 2) Normalisasi + cek duplikat
                $list = $fresh['observasi']['pengeluaranCairan']['pengeluaranCairan'] ?? [];
                $list = collect($list)->map(fn($r) => is_array($r) ? $r : (array)$r)->values()->all();

                $dup = collect($list)->contains(function ($r) use ($target) {
                    return trim((string)($r['waktuPengeluaran'] ?? '')) === $target;
                });
                if ($dup) {
                    throw new \RuntimeException("Data pengeluaran cairan dengan waktu tersebut sudah ada.");
                }

                // 3) Append item
                $list[] = [
                    "waktuPengeluaran"   => (string)$this->pengeluaranCairan['waktuPengeluaran'],
                    "jenisOutput"        => (string)$this->pengeluaranCairan['jenisOutput'],
                    "volume"             => (float)$this->pengeluaranCairan['volume'],
                    "warnaKarakteristik" => (string)$this->pengeluaranCairan['warnaKarakteristik'],
                    "keterangan"         => (string)$this->pengeluaranCairan['keterangan'],
                    "pemeriksa"          => (string)$this->pengeluaranCairan['pemeriksa'],
                ];

                // 4) Tulis balik hanya subtree + log
                $fresh['observasi']['pengeluaranCairan']['pengeluaranCairan'] = array_values($list);
                $fresh['observasi']['pengeluaranCairan']['pengeluaranCairanLog'] = [
                    'userLogDesc' => 'Form Entry Pengeluaran Cairan',
                    'userLog'     => auth()->user()->myuser_name,
                    'userLogDate' => Carbon::now(config('app.timezone'))->format('d/m/Y H:i:s'),
                ];

                // 5) Simpan & sinkronkan state komponen
                $this->updateJsonRI($riHdrNo, $fresh);
                $this->dataDaftarRi = $fresh;
            });

            // Cleanup UI state
            $this->resetInputFields();
            $this->emit('syncronizeAssessmentPerawatRIFindData');
            toastr()->positionClass('toast-top-left')->addSuccess("Data pengeluaran cairan berhasil disimpan.");
        } catch (LockTimeoutException $e) {
            toastr()->positionClass('toast-top-left')->addError('Sistem sibuk, gagal memperoleh lock. Coba lagi.');
        } catch (\RuntimeException $e) {
            toastr()->positionClass('toast-top-left')->addError($e->getMessage());
        } catch (\Throwable $e) {
            toastr()->positionClass('toast-top-left')->addError('Gagal menyimpan: ' . $e->getMessage());
        }
    }

    // Remove pengeluaran cairan
    public function removePengeluaranCairan($waktuPengeluaran)
    {
        $riHdrNo = $this->dataDaftarRi['riHdrNo'] ?? $this->riHdrNoRef ?? null;
        if (!$riHdrNo) {
            toastr()->positionClass('toast-top-left')->addError("riHdrNo kosong.");
            return;
        }

        $target = trim((string) $waktuPengeluaran);

        try {
            $this->withRiLock($riHdrNo, function () use ($riHdrNo, $target) {
                // 1) Ambil fresh data
                $fresh = $this->findDataRI($riHdrNo) ?: [];
                $fresh['riHdrNo'] = $fresh['riHdrNo'] ?? $riHdrNo;

                if (!isset($fresh['observasi']) || !is_array($fresh['observasi'])) {
                    $fresh['observasi'] = [];
                }
                if (!isset($fresh['observasi']['pengeluaranCairan']) || !is_array($fresh['observasi']['pengeluaranCairan'])) {
                    $fresh['observasi']['pengeluaranCairan'] = [
                        'pengeluaranCairanTab' => 'Pengeluaran Cairan',
                        'pengeluaranCairan'    => [],
                    ];
                }

                $list = $fresh['observasi']['pengeluaranCairan']['pengeluaranCairan'] ?? [];
                $list = collect($list)->map(fn($r) => is_array($r) ? $r : (array)$r)->values()->all();

                // 2) Hapus hanya match pertama
                $removed  = false;
                $filtered = [];
                foreach ($list as $row) {
                    $rowTime = trim((string) ($row['waktuPengeluaran'] ?? ''));
                    if (!$removed && $rowTime === $target) {
                        $removed = true;
                        continue;
                    }
                    $filtered[] = $row;
                }

                if (!$removed) {
                    throw new \RuntimeException('Data dengan waktu pengeluaran tersebut tidak ditemukan.');
                }

                // 3) Tulis balik subtree + log
                $fresh['observasi']['pengeluaranCairan']['pengeluaranCairan'] = array_values($filtered);
                $fresh['observasi']['pengeluaranCairan']['pengeluaranCairanLog'] = [
                    'userLogDesc' => 'Hapus Pengeluaran Cairan',
                    'userLog'     => auth()->user()->myuser_name,
                    'userLogDate' => Carbon::now(config('app.timezone'))->format('d/m/Y H:i:s'),
                ];

                // 4) Simpan & sinkronkan state
                $this->updateJsonRI($riHdrNo, $fresh);
                $this->dataDaftarRi = $fresh;
            });

            $this->emit('syncronizeAssessmentPerawatRIFindData');
            toastr()->positionClass('toast-top-left')->addSuccess('Data pengeluaran cairan berhasil dihapus.');
        } catch (LockTimeoutException $e) {
            toastr()->positionClass('toast-top-left')->addError('Sistem sibuk, gagal memperoleh lock. Coba lagi.');
        } catch (\RuntimeException $e) {
            toastr()->positionClass('toast-top-left')->addError($e->getMessage());
        } catch (\Throwable $e) {
            toastr()->positionClass('toast-top-left')->addError('Gagal menghapus: ' . $e->getMessage());
        }
    }

    // Set waktu pengeluaran ke waktu sekarang
    public function setWaktuPengeluaran()
    {
        $this->pengeluaranCairan['waktuPengeluaran'] = Carbon::now(config('app.timezone'))->format('d/m/Y H:i:s');
    }

    // Lock mechanism
    private function withRiLock(string $riHdrNo, callable $fn): void
    {
        $key = "ri:{$riHdrNo}";
        Cache::lock($key, 10)->block(5, function () use ($fn) {
            DB::transaction(function () use ($fn) {
                $fn();
            });
        });
    }

    // Mount component
    public function mount()
    {
        $this->findData($this->riHdrNoRef);
    }

    // Render view
    public function render()
    {
        return view(
            'livewire.emr-r-i.mr-r-i.pengeluaran-cairan.pengeluaran-cairan',
            [
                'myTitle' => 'Pengeluaran Cairan',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Pasien RI',
            ]
        );
    }
}
