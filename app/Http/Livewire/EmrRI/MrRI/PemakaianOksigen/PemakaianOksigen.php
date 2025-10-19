<?php

namespace App\Http\Livewire\EmrRI\MrRI\PemakaianOksigen;


use Livewire\Component;

use Carbon\Carbon;
use App\Http\Traits\EmrRI\EmrRITrait;
use App\Http\Traits\customErrorMessagesTrait;
use Illuminate\Support\Facades\Validator;
use \Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Cache;
use \Illuminate\Support\Facades\DB;

class PemakaianOksigen extends Component
{
    use  EmrRITrait, customErrorMessagesTrait;

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

    public array $pemakaianOksigen = [
        "jenisAlatOksigen" => "Nasal Kanul", // Jenis alat oksigen yang digunakan
        "jenisAlatOksigenDetail" => "", // Jenis alat oksigen yang digunakan
        "dosisOksigen" => "1-2 L/menit", // Dosis oksigen yang diberikan
        "dosisOksigenDetail" => "", // Dosis oksigen yang diberikan
        "modelPenggunaan" => "Kontinu", // Durasi penggunaan (Kontinu atau Intermiten)
        "durasiPenggunaan" => "", // Durasi penggunaan (Kontinu atau Intermiten)
        "tanggalWaktuMulai" => "", // Tanggal dan waktu mulai penggunaan (format: dd/mm/yyyy hh24:mi:ss)
        "tanggalWaktuSelesai" => "", // Tanggal dan waktu selesai penggunaan (format: dd/mm/yyyy hh24:mi:ss)
    ];

    public array $observasi =
    [
        "pemakaianOksigenTab" => "Pemakaian Oksigen",
        "pemakaianOksigenData" => [],

    ];
    //////////////////////////////////////////////////////////////////////


    protected $rules = [
        'pemakaianOksigen.jenisAlatOksigen' => 'required|in:Nasal Kanul,Masker Sederhana,Ventilator Non-Invasif,Lainnya',
        'pemakaianOksigen.jenisAlatOksigenDetail' => 'required_if:pemakaianOksigen.jenisAlatOksigen,Lainnya',
        'pemakaianOksigen.dosisOksigen' => 'required|in:1-2 L/menit,3-4 L/menit,2-6 L/menit (Nasal Kanul),5-10 L/menit (Masker),Lainnya',
        'pemakaianOksigen.dosisOksigenDetail' => 'required_if:pemakaianOksigen.dosisOksigen,Lainnya',
        'pemakaianOksigen.modelPenggunaan' => 'nullable|in:Kontinu,Intermiten',
        'pemakaianOksigen.durasiPenggunaan' => 'nullable',
        'pemakaianOksigen.tanggalWaktuMulai' => 'required|date_format:d/m/Y H:i:s',
        'pemakaianOksigen.tanggalWaktuSelesai' => 'nullable|date_format:d/m/Y H:i:s',
    ];

    protected array $messages = [
        'pemakaianOksigen.jenisAlatOksigen.required' => 'Jenis alat oksigen wajib diisi.',
        'pemakaianOksigen.jenisAlatOksigen.in'       => 'Jenis alat oksigen tidak valid.',

        'pemakaianOksigen.jenisAlatOksigenDetail.required_if'
        => 'Detail jenis alat oksigen wajib diisi.',

        'pemakaianOksigen.dosisOksigen.required' => 'Dosis oksigen wajib diisi.',
        'pemakaianOksigen.dosisOksigen.in'       => 'Dosis oksigen tidak valid.',

        'pemakaianOksigen.dosisOksigenDetail.required_if'
        => 'Detail dosis oksigen wajib diisi.',

        'pemakaianOksigen.tanggalWaktuMulai.required'     => 'Tanggal mulai wajib diisi.',
        'pemakaianOksigen.tanggalWaktuMulai.date_format'  => 'Format tanggal mulai harus d/m/Y H:i:s.',

        'pemakaianOksigen.tanggalWaktuSelesai.date_format' => 'Format tanggal selesai harus d/m/Y H:i:s.',
    ];

    protected array $validationAttributes = [
        'pemakaianOksigen.jenisAlatOksigen'       => 'Jenis alat oksigen',
        'pemakaianOksigen.jenisAlatOksigenDetail' => 'Detail alat oksigen',
        'pemakaianOksigen.dosisOksigen'           => 'Dosis oksigen',
        'pemakaianOksigen.dosisOksigenDetail'     => 'Detail dosis oksigen',
        'pemakaianOksigen.modelPenggunaan'        => 'Model penggunaan',
        'pemakaianOksigen.tanggalWaktuMulai'      => 'Tanggal mulai',
        'pemakaianOksigen.tanggalWaktuSelesai'    => 'Tanggal selesai',
    ];




    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////



    // validate Data RJ//////////////////////////////////////////////////
    private function validateDataPemakaianOksigenRi(): bool
    {
        try {
            $this->validate($this->rules, $this->messages, $this->validationAttributes);
            return true;
        } catch (ValidationException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError("Lakukan Pengecekan kembali Input Data." . $e->getMessage());
            return false;
        }
    }





    private function findData($riHdrNo): void
    {
        $this->dataDaftarRi = $this->findDataRI($riHdrNo) ?: [];
        $this->dataDaftarRi['riHdrNo'] = $this->dataDaftarRi['riHdrNo'] ?? $riHdrNo;

        if (!isset($this->dataDaftarRi['observasi']) || !is_array($this->dataDaftarRi['observasi'])) {
            $this->dataDaftarRi['observasi'] = [];
        }
        if (
            !isset($this->dataDaftarRi['observasi']['pemakaianOksigen']) ||
            !is_array($this->dataDaftarRi['observasi']['pemakaianOksigen'])
        ) {
            $this->dataDaftarRi['observasi']['pemakaianOksigen'] = $this->observasi;
        }

        // Normalisasi array item agar tidak ada stdClass
        $list = $this->dataDaftarRi['observasi']['pemakaianOksigen']['pemakaianOksigenData'] ?? [];
        $this->dataDaftarRi['observasi']['pemakaianOksigen']['pemakaianOksigenData'] =
            collect(is_array($list) ? $list : [])
            ->map(fn($r) => is_array($r) ? $r : (array)$r)
            ->values()
            ->all();
    }


    public function addPemakaianOksigen()
    {
        $riHdrNo = $this->dataDaftarRi['riHdrNo'] ?? $this->riHdrNoRef ?? null;
        if (!$riHdrNo) {
            toastr()->positionClass('toast-top-left')->addError("riHdrNo kosong.");
            return;
        }

        // set pemeriksa & validasi
        $this->pemakaianOksigen['pemeriksa'] = auth()->user()->myuser_name;

        if (!$this->validateDataPemakaianOksigenRi()) {
            return; // HENTIKAN proses simpan ketika validasi gagal
        }

        $target = trim((string)$this->pemakaianOksigen['tanggalWaktuMulai']);

        try {
            $this->withRiLock($riHdrNo, function () use ($riHdrNo, $target) {
                $fresh = $this->findDataRI($riHdrNo) ?: [];
                $fresh['riHdrNo'] = $fresh['riHdrNo'] ?? $riHdrNo;

                if (!isset($fresh['observasi']) || !is_array($fresh['observasi'])) {
                    $fresh['observasi'] = [];
                }
                if (!isset($fresh['observasi']['pemakaianOksigen']) || !is_array($fresh['observasi']['pemakaianOksigen'])) {
                    $fresh['observasi']['pemakaianOksigen'] = [
                        'pemakaianOksigenTab'  => 'Pemakaian Oksigen',
                        'pemakaianOksigenData' => [],
                    ];
                }

                $list = $fresh['observasi']['pemakaianOksigen']['pemakaianOksigenData'] ?? [];
                $list = collect($list)->map(fn($r) => is_array($r) ? $r : (array)$r)->values()->all();

                // Cek duplikat
                $dup = collect($list)->contains(
                    fn($r) =>
                    trim((string)($r['tanggalWaktuMulai'] ?? '')) === $target
                );
                if ($dup) {
                    throw new \RuntimeException("Pemakaian Oksigen sudah ada.");
                }

                // Tambah item
                $list[] = [
                    "jenisAlatOksigen"       => (string)$this->pemakaianOksigen['jenisAlatOksigen'],
                    "jenisAlatOksigenDetail" => (string)$this->pemakaianOksigen['jenisAlatOksigenDetail'],
                    "dosisOksigen"           => (string)$this->pemakaianOksigen['dosisOksigen'],
                    "dosisOksigenDetail"     => (string)$this->pemakaianOksigen['dosisOksigenDetail'],
                    "durasiPenggunaan"       => (string)$this->pemakaianOksigen['durasiPenggunaan'],
                    "modelPenggunaan"        => (string)$this->pemakaianOksigen['modelPenggunaan'],
                    "tanggalWaktuMulai"      => $target,
                    "tanggalWaktuSelesai"    => (string)($this->pemakaianOksigen['tanggalWaktuSelesai'] ?? ''),
                    "pemeriksa"              => (string)$this->pemakaianOksigen['pemeriksa'],
                ];

                $fresh['observasi']['pemakaianOksigen']['pemakaianOksigenData'] = array_values($list);
                $fresh['observasi']['pemakaianOksigen']['pemakaianOksigenLog'] = [
                    'userLogDesc' => 'Form Entry pemakaianOksigen',
                    'userLog'     => auth()->user()->myuser_name,
                    'userLogDate' => Carbon::now(config('app.timezone'))->format('d/m/Y H:i:s')
                ];

                $this->updateJsonRI($riHdrNo, $fresh);
                $this->dataDaftarRi = $fresh;
            });

            $this->reset(['pemakaianOksigen']);
            $this->emit('syncronizeAssessmentPerawatRIFindData');
            toastr()->positionClass('toast-top-left')->addSuccess("Pemakaian Oksigen berhasil disimpan.");
        } catch (LockTimeoutException $e) {
            toastr()->positionClass('toast-top-left')->addError('Sistem sibuk, gagal memperoleh lock. Coba lagi.');
        } catch (\RuntimeException $e) {
            toastr()->positionClass('toast-top-left')->addError($e->getMessage());
        } catch (\Throwable $e) {
            toastr()->positionClass('toast-top-left')->addError('Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function removePemakaianOksigen($tanggalWaktuMulai)
    {
        $riHdrNo = $this->dataDaftarRi['riHdrNo'] ?? $this->riHdrNoRef ?? null;
        if (!$riHdrNo) {
            toastr()->positionClass('toast-top-left')->addError("riHdrNo kosong.");
            return;
        }

        $target = trim((string)$tanggalWaktuMulai);

        try {
            $this->withRiLock($riHdrNo, function () use ($riHdrNo, $target) {
                $fresh = $this->findDataRI($riHdrNo) ?: [];
                $fresh['riHdrNo'] = $fresh['riHdrNo'] ?? $riHdrNo;

                if (!isset($fresh['observasi']) || !is_array($fresh['observasi'])) {
                    $fresh['observasi'] = [];
                }
                if (!isset($fresh['observasi']['pemakaianOksigen']) || !is_array($fresh['observasi']['pemakaianOksigen'])) {
                    $fresh['observasi']['pemakaianOksigen'] = [
                        'pemakaianOksigenTab'  => 'Pemakaian Oksigen',
                        'pemakaianOksigenData' => [],
                    ];
                }

                $list = $fresh['observasi']['pemakaianOksigen']['pemakaianOksigenData'] ?? [];
                $list = collect($list)->map(fn($r) => is_array($r) ? $r : (array)$r)->values()->all();

                $removed = false;
                $filtered = [];
                foreach ($list as $row) {
                    $rowTime = trim((string)($row['tanggalWaktuMulai'] ?? ''));
                    if (!$removed && $rowTime === $target) {
                        $removed = true;
                        continue;
                    }
                    $filtered[] = $row;
                }
                if (!$removed) {
                    throw new \RuntimeException('Data pemakaian oksigen tidak ditemukan.');
                }

                $fresh['observasi']['pemakaianOksigen']['pemakaianOksigenData'] = array_values($filtered);
                $fresh['observasi']['pemakaianOksigen']['pemakaianOksigenLog'] = [
                    'userLogDesc' => 'Hapus pemakaianOksigen (by tanggalWaktuMulai)',
                    'userLog'     => auth()->user()->myuser_name,
                    'userLogDate' => Carbon::now(config('app.timezone'))->format('d/m/Y H:i:s')
                ];

                $this->updateJsonRI($riHdrNo, $fresh);
                $this->dataDaftarRi = $fresh;
            });

            $this->emit('syncronizeAssessmentPerawatRIFindData');
            toastr()->positionClass('toast-top-left')->addSuccess('Item berhasil dihapus.');
        } catch (LockTimeoutException $e) {
            toastr()->positionClass('toast-top-left')->addError('Sistem sibuk, gagal memperoleh lock. Coba lagi.');
        } catch (\RuntimeException $e) {
            toastr()->positionClass('toast-top-left')->addError($e->getMessage());
        } catch (\Throwable $e) {
            toastr()->positionClass('toast-top-left')->addError('Gagal menghapus: ' . $e->getMessage());
        }
    }



    public function setTanggalWaktuMulai($myTime)
    {
        $this->pemakaianOksigen['tanggalWaktuMulai'] = $myTime;
    }
    public function setTanggalWaktuSelesai($index, $myTime)
    {
        // gunakan updateTanggalWaktuSelesai supaya validasi & hitung durasi konsisten
        $data = $this->dataDaftarRi['observasi']['pemakaianOksigen']['pemakaianOksigenData'][$index] ?? null;
        if (!$data) return;

        $this->updateTanggalWaktuSelesai($index, $data['tanggalWaktuMulai'] ?? '', $myTime);
    }

    public function updateTanggalWaktuSelesai($index, $myTimeStart, $myTimeEnd)
    {
        // Validasi sederhana lokal
        $r = ['tanggalWaktuMulai' => $myTimeStart, 'tanggalWaktuSelesai' => $myTimeEnd];
        $rules = [
            'tanggalWaktuMulai'   => 'required|date_format:d/m/Y H:i:s',
            'tanggalWaktuSelesai' => 'required|date_format:d/m/Y H:i:s|after:tanggalWaktuMulai',
        ];
        $messages = [
            'tanggalWaktuMulai.required'   => 'Waktu mulai harus diisi.',
            'tanggalWaktuMulai.date_format' => 'Format waktu mulai harus d/m/Y H:i:s.',
            'tanggalWaktuSelesai.required' => 'Waktu akhir harus diisi.',
            'tanggalWaktuSelesai.date_format' => 'Format waktu akhir harus d/m/Y H:i:s.',
            'tanggalWaktuSelesai.after'    => 'Waktu akhir harus > waktu mulai.',
        ];
        $validator = Validator::make($r, $rules, $messages);
        if ($validator->fails()) {
            toastr()->positionClass('toast-top-left')->addError($validator->errors()->first());
            return;
        }

        $riHdrNo = $this->dataDaftarRi['riHdrNo'] ?? $this->riHdrNoRef ?? null;
        if (!$riHdrNo) {
            toastr()->positionClass('toast-top-left')->addError("riHdrNo kosong.");
            return;
        }

        try {
            $this->withRiLock($riHdrNo, function () use ($riHdrNo, $index, $r) {
                $fresh = $this->findDataRI($riHdrNo) ?: [];
                $fresh['riHdrNo'] = $fresh['riHdrNo'] ?? $riHdrNo;

                if (!isset($fresh['observasi']) || !is_array($fresh['observasi'])) {
                    $fresh['observasi'] = [];
                }
                if (!isset($fresh['observasi']['pemakaianOksigen']) || !is_array($fresh['observasi']['pemakaianOksigen'])) {
                    $fresh['observasi']['pemakaianOksigen'] = [
                        'pemakaianOksigenTab'  => 'Pemakaian Oksigen',
                        'pemakaianOksigenData' => [],
                    ];
                }

                $list = $fresh['observasi']['pemakaianOksigen']['pemakaianOksigenData'] ?? [];
                $list = collect($list)->map(fn($r) => is_array($r) ? $r : (array)$r)->values()->all();

                if (!isset($list[$index])) {
                    throw new \RuntimeException('Index data tidak valid.');
                }

                // Update end time
                $list[$index]['tanggalWaktuSelesai'] = $r['tanggalWaktuSelesai'];

                // Hitung durasi
                $mulai   = Carbon::createFromFormat('d/m/Y H:i:s', $r['tanggalWaktuMulai'], config('app.timezone'));
                $selesai = Carbon::createFromFormat('d/m/Y H:i:s', $r['tanggalWaktuSelesai'], config('app.timezone'));
                $selisihJam   = $mulai->diffInHours($selesai);
                $selisihMenit = $mulai->diffInMinutes($selesai) % 60;
                $list[$index]['durasiPenggunaan'] = $selisihJam . ' jam ' . $selisihMenit . ' menit';

                $fresh['observasi']['pemakaianOksigen']['pemakaianOksigenData'] = array_values($list);
                $fresh['observasi']['pemakaianOksigen']['pemakaianOksigenLog'] = [
                    'userLogDesc' => 'Update TanggalWaktuSelesai pemakaianOksigen',
                    'userLog'     => auth()->user()->myuser_name,
                    'userLogDate' => Carbon::now(config('app.timezone'))->format('d/m/Y H:i:s')
                ];

                $this->updateJsonRI($riHdrNo, $fresh);
                $this->dataDaftarRi = $fresh;
            });

            $this->emit('syncronizeAssessmentPerawatRIFindData');
            toastr()->positionClass('toast-top-left')->addSuccess('Tanggal selesai & durasi diperbarui.');
        } catch (LockTimeoutException $e) {
            toastr()->positionClass('toast-top-left')->addError('Sistem sibuk, gagal memperoleh lock. Coba lagi.');
        } catch (\RuntimeException $e) {
            toastr()->positionClass('toast-top-left')->addError($e->getMessage());
        } catch (\Throwable $e) {
            toastr()->positionClass('toast-top-left')->addError('Gagal memperbarui: ' . $e->getMessage());
        }
    }

    private function withRiLock(string $riHdrNo, callable $fn): void
    {
        $key = "ri:{$riHdrNo}";
        Cache::lock($key, 10)->block(5, function () use ($fn) {
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
            'livewire.emr-r-i.mr-r-i.pemakaian-oksigen.pemakaian-oksigen',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Pemakaian Oksigen',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Pasien RI',
            ]
        );
    }
    // select data end////////////////


}
