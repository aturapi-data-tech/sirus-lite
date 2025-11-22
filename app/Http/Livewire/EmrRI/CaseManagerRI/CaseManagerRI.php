<?php

namespace App\Http\Livewire\EmrRI\CaseManagerRI;

use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Http\Traits\EmrRI\EmrRITrait;
use Illuminate\Support\Str;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use App\Http\Traits\MasterPasien\MasterPasienTrait;

class CaseManagerRI extends Component
{
    use EmrRITrait, MasterPasienTrait;

    protected $listeners = [
        'syncronizeAssessmentDokterRIFindData' => 'mount',
        'syncronizeAssessmentPerawatRIFindData' => 'mount'
    ];

    public $riHdrNoRef;
    public array $dataDaftarRi = [];

    /* ======================================
     * 🧾 FORM A – SKRINING AWAL MPP
     * ====================================== */
    public array $formA = [
        'formA_id' => '',
        'tipeForm' => 'FormA',
        'tanggal' => '',
        'indentifikasiKasus' => '',
        'assessment' => '',
        'perencanaan' => '',
        'tandaTanganPetugas' => [
            'petugasCode' => '',
            'petugasName' => '',
            'jabatan' => 'MPP',
        ],
    ];

    /* ======================================
     * 📋 FORM B – PELAKSANAAN, MONITORING, ADVOKASI, TERMINASI
     * ====================================== */
    public array $formB = [
        'formB_id' => '',
        'tipeForm' => 'FormB',
        'formA_id' => '',
        'tanggal' => '',
        'pelaksanaanMonitoring' => '',
        'advokasiKolaborasi' => '',
        'terminasi' => '',
        'tandaTanganPetugas' => [
            'petugasCode' => '',
            'petugasName' => '',
            'jabatan' => 'MPP',
        ],
    ];

    // === Rules ===
    protected array $rulesFormA = [
        'formA.tipeForm'                  => 'required|in:FormA',
        'formA.tanggal'                   => 'required|date_format:d/m/Y H:i:s',

        'formA.indentifikasiKasus'        => 'nullable|string|max:1000',
        'formA.assessment'                => 'nullable|string|max:2000',
        'formA.perencanaan'               => 'nullable|string|max:2000',

        'formA.tandaTanganPetugas.petugasCode' => 'required|string|max:50',
        'formA.tandaTanganPetugas.petugasName' => 'required|string|max:150',
        'formA.tandaTanganPetugas.jabatan'     => 'required|string|max:100',
    ];

    protected array $rulesFormB = [
        'formB.tipeForm'                  => 'required|in:FormB',
        'formB.formA_id'                  => 'required|string|max:50',
        'formB.tanggal'                   => 'required|date_format:d/m/Y H:i:s',

        'formB.pelaksanaanMonitoring'     => 'nullable|string|max:2000',
        'formB.advokasiKolaborasi'        => 'nullable|string|max:1000',
        'formB.terminasi'                 => 'nullable|string|max:1000',

        'formB.tandaTanganPetugas.petugasCode' => 'required|string|max:50',
        'formB.tandaTanganPetugas.petugasName' => 'required|string|max:150',
        'formB.tandaTanganPetugas.jabatan'     => 'required|string|max:100',
    ];

    protected array $messages = [
        'required'    => ':attribute wajib diisi.',
        'string'      => ':attribute harus berupa teks.',
        'date'        => ':attribute harus berupa tanggal yang valid.',
        'date_format' => ':attribute harus sesuai format dd/mm/yyyy HH:MM:SS.',
        'in'          => ':attribute tidak valid.',
        'max'         => ':attribute tidak boleh lebih dari :max karakter.',
    ];

    protected array $attributes = [
        // Form A
        'formA.tanggal'                        => 'Tanggal Form A',
        'formA.indentifikasiKasus'             => 'Identifikasi kasus',
        'formA.assessment'                     => 'Assessment',
        'formA.perencanaan'                    => 'Perencanaan',
        'formA.tandaTanganPetugas.petugasCode' => 'Kode petugas (Form A)',
        'formA.tandaTanganPetugas.petugasName' => 'Nama petugas (Form A)',
        'formA.tandaTanganPetugas.jabatan'     => 'Jabatan petugas (Form A)',

        // Form B
        'formB.tanggal'                        => 'Tanggal kegiatan (Form B)',
        'formB.formA_id'                       => 'Referensi Form A',
        'formB.pelaksanaanMonitoring'          => 'Pelaksanaan dan monitoring',
        'formB.advokasiKolaborasi'             => 'Advokasi / kolaborasi',
        'formB.terminasi'                      => 'Ringkasan terminasi',
        'formB.tandaTanganPetugas.petugasCode' => 'Kode petugas (Form B)',
        'formB.tandaTanganPetugas.petugasName' => 'Nama petugas (Form B)',
        'formB.tandaTanganPetugas.jabatan'     => 'Jabatan petugas (Form B)',
    ];

    public $showFormB = false;

    /* ======================================
     * 🧩 SET PETUGAS DI MOUNT
     * ====================================== */
    public function mount()
    {
        $this->findData($this->riHdrNoRef);
        $this->setPetugasData();
    }

    /* ======================================
     * 🔄 SET DATA PETUGAS
     * ====================================== */
    private function setPetugasData(): void
    {
        // Auto set petugas info untuk Form A
        $this->formA['tandaTanganPetugas']['petugasCode'] = auth()->user()->myuser_code ?? '';
        $this->formA['tandaTanganPetugas']['petugasName'] = auth()->user()->myuser_name ?? '';
        $this->formA['tandaTanganPetugas']['jabatan'] = 'MPP';

        // Auto set petugas info untuk Form B
        $this->formB['tandaTanganPetugas']['petugasCode'] = auth()->user()->myuser_code ?? '';
        $this->formB['tandaTanganPetugas']['petugasName'] = auth()->user()->myuser_name ?? '';
        $this->formB['tandaTanganPetugas']['jabatan'] = 'MPP';
    }

    /* ======================================
     * 🔄 RESET FORM CUSTOM
     * ====================================== */
    private function resetFormA(): void
    {
        $this->formA = [
            'formA_id' => '',
            'tipeForm' => 'FormA',
            'tanggal' => '',
            'indentifikasiKasus' => '',
            'assessment' => '',
            'perencanaan' => '',
            'tandaTanganPetugas' => [
                'petugasCode' => auth()->user()->myuser_code ?? '',
                'petugasName' => auth()->user()->myuser_name ?? '',
                'jabatan' => 'MPP',
            ],
        ];
    }

    private function resetFormB(): void
    {
        $this->formB = [
            'formB_id' => '',
            'tipeForm' => 'FormB',
            'formA_id' => '',
            'tanggal' => '',
            'pelaksanaanMonitoring' => '',
            'advokasiKolaborasi' => '',
            'terminasi' => '',
            'tandaTanganPetugas' => [
                'petugasCode' => auth()->user()->myuser_code ?? '',
                'petugasName' => auth()->user()->myuser_name ?? '',
                'jabatan' => 'MPP',
            ],
        ];
    }

    // Method untuk set tanggal otomatis
    public function setTanggalFormA(): void
    {
        $this->formA['tanggal'] = Carbon::now()->format('d/m/Y H:i:s');
    }

    public function setTanggalFormB(): void
    {
        $this->formB['tanggal'] = Carbon::now()->format('d/m/Y H:i:s');
    }

    /* ======================================
     * 🧩 SIMPAN FORM A
     * ====================================== */
    public function simpanFormA(): void
    {
        try {
            $this->validate($this->rulesFormA, $this->messages, $this->attributes);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Tampilkan error validation pertama sebagai toast
            $firstError = collect($e->errors())->first()[0] ?? 'Terjadi kesalahan validasi.';
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError($firstError);
            return;
        }

        $riHdrNo = $this->dataDaftarRi['riHdrNo'] ?? $this->riHdrNoRef ?? null;
        if (!$riHdrNo) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Nomor RI tidak ditemukan.');
            return;
        }

        $this->formA['formA_id'] = (string) Str::uuid();

        $entry = $this->formA;
        $entry['created_at'] = now()->format('Y-m-d H:i:s');

        try {
            Cache::lock("ri:{$riHdrNo}", 5)->block(3, function () use ($riHdrNo, $entry) {
                $fresh = $this->findDataRI($riHdrNo);
                if (!isset($fresh['formMPP'])) $fresh['formMPP'] = [];
                if (!isset($fresh['formMPP']['formA'])) $fresh['formMPP']['formA'] = [];
                $fresh['formMPP']['formA'][] = $entry;

                DB::transaction(fn() => $this->updateJsonRI($riHdrNo, $fresh));
                $this->dataDaftarRi = $fresh;
            });
        } catch (LockTimeoutException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Sistem sibuk, coba lagi.');
            return;
        }

        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('Form A berhasil disimpan.');
        $this->resetFormA(); // Gunakan custom reset
    }

    /* ======================================
     * 📋 SIMPAN FORM B
     * ====================================== */
    public function simpanFormB(): void
    {
        try {
            $this->validate($this->rulesFormB, $this->messages, $this->attributes);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $firstError = collect($e->errors())->first()[0] ?? 'Terjadi kesalahan validasi.';
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError($firstError);
            return;
        }

        $riHdrNo = $this->dataDaftarRi['riHdrNo'] ?? $this->riHdrNoRef ?? null;
        if (!$riHdrNo) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Nomor RI tidak ditemukan.');
            return;
        }

        $this->formB['formB_id'] = (string) Str::uuid();

        $entry = $this->formB;
        $entry['created_at'] = now()->format('Y-m-d H:i:s');

        try {
            Cache::lock("ri:{$riHdrNo}", 5)->block(3, function () use ($riHdrNo, $entry) {
                $fresh = $this->findDataRI($riHdrNo) ?: [];
                if (!isset($fresh['formMPP'])) $fresh['formMPP'] = [];
                if (!isset($fresh['formMPP']['formB'])) $fresh['formMPP']['formB'] = [];
                $fresh['formMPP']['formB'][] = $entry;

                DB::transaction(fn() => $this->updateJsonRI($riHdrNo, $fresh));
                $this->dataDaftarRi = $fresh;
            });
        } catch (LockTimeoutException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Sistem sibuk, coba lagi.');
            return;
        }

        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('Form B berhasil disimpan.');
        $this->resetFormB(); // Gunakan custom reset
        $this->showFormB = false;
    }

    /* ======================================
     * 🗑️ HAPUS FORM A / B
     * ====================================== */
    public function hapusForm($tipe, $id)
    {
        $riHdrNo = $this->dataDaftarRi['riHdrNo'] ?? $this->riHdrNoRef ?? null;
        if (!$riHdrNo) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Nomor RI tidak ditemukan.');
            return;
        }

        $lockKey = "ri:{$riHdrNo}";
        try {
            Cache::lock($lockKey, 5)->block(3, function () use ($riHdrNo, $id, $tipe) {
                $fresh = $this->findDataRI($riHdrNo) ?: [];
                $list = $fresh['formMPP'][$tipe] ?? [];

                $newList = array_values(array_filter($list, fn($e) => ($e[$tipe . '_id'] ?? null) !== $id));
                $fresh['formMPP'][$tipe] = $newList;

                DB::transaction(fn() => $this->updateJsonRI($riHdrNo, $fresh));
                $this->dataDaftarRi = $fresh;
            });
        } catch (LockTimeoutException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Sistem sibuk, gagal memperoleh lock.');
            return;
        }

        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Data {$tipe} berhasil dihapus.");
    }

    /* ======================================
     * ➕ TAMBAH FORM B DARI TABLE FORM A
     * ====================================== */
    public function tambahFormB($formA_id): void
    {
        // Set formA_id yang dipilih
        $this->formB['formA_id'] = $formA_id;

        // Auto set tanggal Form B
        $this->formB['tanggal'] = Carbon::now()->format('d/m/Y H:i:s');

        // Tampilkan Form B
        $this->showFormB = true;

        // Scroll ke Form B
        $this->dispatchBrowserEvent('scroll-to-form-b');
    }

    /* ======================================
 * 🖨️ CETAK FORM A
 * ====================================== */
    public function cetakFormA($formA_id)
    {
        $riHdrNo = $this->dataDaftarRi['riHdrNo'] ?? $this->riHdrNoRef ?? null;
        if (!$riHdrNo) {
            toastr()->addError('Nomor RI tidak ditemukan.');
            return;
        }

        try {
            $queryIdentitas = DB::table('rsmst_identitases')
                ->select('int_name', 'int_phone1', 'int_phone2', 'int_fax', 'int_address', 'int_city')
                ->first();

            // Cari data Form A berdasarkan ID
            $formA = collect($this->dataDaftarRi['formMPP']['formA'] ?? [])
                ->firstWhere('formA_id', $formA_id);

            if (!$formA) {
                toastr()->addError('Data Form A tidak ditemukan.');
                return;
            }

            $dataPasien = $this->findDataMasterPasien($this->dataDaftarRi['regNo'] ?? '');

            $data = [
                'identitasRs'  => $queryIdentitas,
                'dataPasien'   => $dataPasien ?? [],
                'dataDaftarRi' => $this->dataDaftarRi,
                'dataFormA'    => $formA,
            ];

            $pdfContent = Pdf::loadView('livewire.cetak.cetak-form-a-print', $data)->output();

            toastr()->addSuccess('Berhasil mencetak Form A.');

            return response()->streamDownload(
                fn() => print($pdfContent),
                'form-a-' . $formA_id . '.pdf'
            );
        } catch (Exception $e) {
            toastr()->addError('Gagal mencetak Form A: ' . $e->getMessage());
        }
    }

    /* ======================================
 * 🖨️ CETAK FORM B
 * ====================================== */
    public function cetakFormB($formB_id)
    {
        $riHdrNo = $this->dataDaftarRi['riHdrNo'] ?? $this->riHdrNoRef ?? null;
        if (!$riHdrNo) {
            toastr()->addError('Nomor RI tidak ditemukan.');
            return;
        }

        try {
            $queryIdentitas = DB::table('rsmst_identitases')
                ->select('int_name', 'int_phone1', 'int_phone2', 'int_fax', 'int_address', 'int_city')
                ->first();

            // Cari data Form B berdasarkan ID
            $formB = collect($this->dataDaftarRi['formMPP']['formB'] ?? [])
                ->firstWhere('formB_id', $formB_id);

            if (!$formB) {
                toastr()->addError('Data Form B tidak ditemukan.');
                return;
            }

            $dataPasien = $this->findDataMasterPasien($this->dataDaftarRi['regNo'] ?? '');

            $data = [
                'identitasRs'  => $queryIdentitas,
                'dataPasien'   => $dataPasien ?? [],
                'dataDaftarRi' => $this->dataDaftarRi,
                'dataFormB'    => $formB,
            ];

            $pdfContent = Pdf::loadView('livewire.cetak.cetak-form-b-print', $data)->output();

            toastr()->addSuccess('Berhasil mencetak Form B.');

            return response()->streamDownload(
                fn() => print($pdfContent),
                'form-b-' . $formB_id . '.pdf'
            );
        } catch (Exception $e) {
            toastr()->addError('Gagal mencetak Form B: ' . $e->getMessage());
        }
    }


    /* ======================================
     * 🔍 Fetch data awal
     * ====================================== */
    private function findData($riHdrNo): void
    {
        $this->dataDaftarRi = $this->findDataRI($riHdrNo) ?: [];
    }

    public function render()
    {
        return view('livewire.emr-r-i.case-manager-r-i.case-manager-r-i');
    }
}
