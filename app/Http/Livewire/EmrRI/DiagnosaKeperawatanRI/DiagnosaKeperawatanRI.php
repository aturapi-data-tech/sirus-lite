<?php

namespace App\Http\Livewire\EmrRI\DiagnosaKeperawatanRI;

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

class DiagnosaKeperawatanRI extends Component
{
    use EmrRITrait, MasterPasienTrait;

    protected $listeners = [
        'syncronizeAssessmentDokterRIFindData' => 'mount',
        'syncronizeAssessmentPerawatRIFindData' => 'mount'
    ];

    public $riHdrNoRef;
    public array $dataDaftarRi = [];

    /* ======================================
     * 🧾 FORM A – DIAGNOSA KEPERAWATAN
     * ====================================== */
    public array $formDiagnosaKeperawatan = [
        'formDiagnosaKeperawatan_id' => '',
        'tipeForm' => 'FormA',
        'tanggal' => '',
        'dataSubyektif' => '',
        'dataObyektif' => '',
        'diagnosaKeperawatan' => '',
        'tandaTanganPetugas' => [
            'petugasCode' => '',
            'petugasName' => '',
            'jabatan' => 'Perawat', // Atau MPP, sesuaikan kebutuhan
        ],
    ];

    /* ======================================
     * 📋 FORM B – INTERVENSI & IMPLEMENTASI
     * ====================================== */
    public array $formIntervensiImplementasi = [
        'formIntervensiImplementasi_id' => '',
        'tipeForm' => 'FormB',
        'formDiagnosaKeperawatan_id' => '',
        'tanggal' => '',
        'intervensi' => '',
        'implementasi' => '',
        'tandaTanganPetugas' => [
            'petugasCode' => '',
            'petugasName' => '',
            'jabatan' => 'Perawat', // Atau MPP, sesuaikan kebutuhan
        ],
    ];

    // === Rules ===
    protected array $rulesFormA = [
        'formDiagnosaKeperawatan.tipeForm' => 'required|in:FormA',
        'formDiagnosaKeperawatan.tanggal' => 'required|date_format:d/m/Y H:i:s',
        'formDiagnosaKeperawatan.dataSubyektif' => 'nullable|string|max:2000',
        'formDiagnosaKeperawatan.dataObyektif' => 'nullable|string|max:2000',
        'formDiagnosaKeperawatan.diagnosaKeperawatan' => 'required|string|max:1000',
        'formDiagnosaKeperawatan.tandaTanganPetugas.petugasCode' => 'required|string|max:50',
        'formDiagnosaKeperawatan.tandaTanganPetugas.petugasName' => 'required|string|max:150',
        'formDiagnosaKeperawatan.tandaTanganPetugas.jabatan' => 'required|string|max:100',
    ];

    protected array $rulesFormB = [
        'formIntervensiImplementasi.tipeForm' => 'required|in:FormB',
        'formIntervensiImplementasi.formDiagnosaKeperawatan_id' => 'required|string|max:50',
        'formIntervensiImplementasi.tanggal' => 'required|date_format:d/m/Y H:i:s',
        'formIntervensiImplementasi.intervensi' => 'nullable|string|max:2000',
        'formIntervensiImplementasi.implementasi' => 'nullable|string|max:2000',
        'formIntervensiImplementasi.tandaTanganPetugas.petugasCode' => 'required|string|max:50',
        'formIntervensiImplementasi.tandaTanganPetugas.petugasName' => 'required|string|max:150',
        'formIntervensiImplementasi.tandaTanganPetugas.jabatan' => 'required|string|max:100',
    ];

    protected array $messages = [
        'required' => ':attribute wajib diisi.',
        'string' => ':attribute harus berupa teks.',
        'date' => ':attribute harus berupa tanggal yang valid.',
        'date_format' => ':attribute harus sesuai format dd/mm/yyyy HH:MM:SS.',
        'in' => ':attribute tidak valid.',
        'max' => ':attribute tidak boleh lebih dari :max karakter.',
    ];

    protected array $attributes = [
        // Form Diagnosa Keperawatan
        'formDiagnosaKeperawatan.tanggal' => 'Tanggal',
        'formDiagnosaKeperawatan.dataSubyektif' => 'Data subyektif',
        'formDiagnosaKeperawatan.dataObyektif' => 'Data obyektif',
        'formDiagnosaKeperawatan.diagnosaKeperawatan' => 'Diagnosa keperawatan',
        'formDiagnosaKeperawatan.tandaTanganPetugas.petugasCode' => 'Kode petugas (Form Diagnosa Keperawatan)',
        'formDiagnosaKeperawatan.tandaTanganPetugas.petugasName' => 'Nama petugas (Form Diagnosa Keperawatan)',
        'formDiagnosaKeperawatan.tandaTanganPetugas.jabatan' => 'Jabatan petugas (Form Diagnosa Keperawatan)',

        // Intervensi / Implementasi
        'formIntervensiImplementasi.tanggal' => 'Tanggal',
        'formIntervensiImplementasi.formDiagnosaKeperawatan_id' => 'Referensi Diagnosa (Form Diagnosa Keperawatan)',
        'formIntervensiImplementasi.intervensi' => 'Intervensi',
        'formIntervensiImplementasi.implementasi' => 'Implementasi',
        'formIntervensiImplementasi.tandaTanganPetugas.petugasCode' => 'Kode petugas (Intervensi / Implementasi)',
        'formIntervensiImplementasi.tandaTanganPetugas.petugasName' => 'Nama petugas (Intervensi / Implementasi)',
        'formIntervensiImplementasi.tandaTanganPetugas.jabatan' => 'Jabatan petugas (Intervensi / Implementasi)',
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
        // Auto set petugas info untuk Form Diagnosa Keperawatan
        $this->formDiagnosaKeperawatan['tandaTanganPetugas']['petugasCode'] = auth()->user()->myuser_code ?? '';
        $this->formDiagnosaKeperawatan['tandaTanganPetugas']['petugasName'] = auth()->user()->myuser_name ?? '';
        $this->formDiagnosaKeperawatan['tandaTanganPetugas']['jabatan'] = 'Perawat';

        // Auto set petugas info untuk Intervensi / Implementasi
        $this->formIntervensiImplementasi['tandaTanganPetugas']['petugasCode'] = auth()->user()->myuser_code ?? '';
        $this->formIntervensiImplementasi['tandaTanganPetugas']['petugasName'] = auth()->user()->myuser_name ?? '';
        $this->formIntervensiImplementasi['tandaTanganPetugas']['jabatan'] = 'Perawat';
    }

    /* ======================================
     * 🔄 RESET FORM CUSTOM
     * ====================================== */
    private function resetFormA(): void
    {
        $this->formDiagnosaKeperawatan = [
            'formDiagnosaKeperawatan_id' => '',
            'tipeForm' => 'FormA',
            'tanggal' => '',
            'dataSubyektif' => '',
            'dataObyektif' => '',
            'diagnosaKeperawatan' => '',
            'tandaTanganPetugas' => [
                'petugasCode' => auth()->user()->myuser_code ?? '',
                'petugasName' => auth()->user()->myuser_name ?? '',
                'jabatan' => 'Perawat',
            ],
        ];
    }

    private function resetFormB(): void
    {
        $this->formIntervensiImplementasi = [
            'formIntervensiImplementasi_id' => '',
            'tipeForm' => 'FormB',
            'formDiagnosaKeperawatan_id' => '',
            'tanggal' => '',
            'intervensi' => '',
            'implementasi' => '',
            'tandaTanganPetugas' => [
                'petugasCode' => auth()->user()->myuser_code ?? '',
                'petugasName' => auth()->user()->myuser_name ?? '',
                'jabatan' => 'Perawat',
            ],
        ];
    }

    // Method untuk set tanggal otomatis
    public function setTanggalFormA(): void
    {
        $this->formDiagnosaKeperawatan['tanggal'] = Carbon::now()->format('d/m/Y H:i:s');
    }

    public function setTanggalFormB(): void
    {
        $this->formIntervensiImplementasi['tanggal'] = Carbon::now()->format('d/m/Y H:i:s');
    }

    /* ======================================
     * 🧩 SIMPAN FORM A
     * ====================================== */
    public function simpanFormA(): void
    {
        try {
            $this->validate($this->rulesFormA, $this->messages, $this->attributes);
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

        $this->formDiagnosaKeperawatan['formDiagnosaKeperawatan_id'] = (string) Str::uuid();

        $entry = $this->formDiagnosaKeperawatan;
        $entry['created_at'] = now()->format('Y-m-d H:i:s');

        try {
            Cache::lock("ri:{$riHdrNo}", 5)->block(3, function () use ($riHdrNo, $entry) {
                $fresh = $this->findDataRI($riHdrNo);
                if (!isset($fresh['diagKeperawatan'])) {
                    $fresh['diagKeperawatan'] = [];
                }
                if (!isset($fresh['diagKeperawatan']['formDiagnosaKeperawatan'])) {
                    $fresh['diagKeperawatan']['formDiagnosaKeperawatan'] = [];
                }

                $fresh['diagKeperawatan']['formDiagnosaKeperawatan'][] = $entry;


                DB::transaction(fn() => $this->updateJsonRI($riHdrNo, $fresh));
                $this->dataDaftarRi = $fresh;
            });
        } catch (LockTimeoutException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Sistem sibuk, coba lagi.');
            return;
        }

        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('Diagnosa keperawatan (Form Diagnosa Keperawatan) berhasil disimpan.');
        $this->resetFormA();
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

        $this->formIntervensiImplementasi['formIntervensiImplementasi_id'] = (string) Str::uuid();

        $entry = $this->formIntervensiImplementasi;
        $entry['created_at'] = now()->format('Y-m-d H:i:s');

        try {
            Cache::lock("ri:{$riHdrNo}", 5)->block(3, function () use ($riHdrNo, $entry) {
                $fresh = $this->findDataRI($riHdrNo);

                if (!isset($fresh['diagKeperawatan'])) {
                    $fresh['diagKeperawatan'] = [];
                }
                if (!isset($fresh['diagKeperawatan']['formIntervensiImplementasi'])) {
                    $fresh['diagKeperawatan']['formIntervensiImplementasi'] = [];
                }

                $fresh['diagKeperawatan']['formIntervensiImplementasi'][] = $entry;


                DB::transaction(fn() => $this->updateJsonRI($riHdrNo, $fresh));
                $this->dataDaftarRi = $fresh;
            });
        } catch (LockTimeoutException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Sistem sibuk, coba lagi.');
            return;
        }

        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('Intervensi & implementasi (Intervensi / Implementasi) berhasil disimpan.');
        $this->resetFormB();
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
                $fresh = $this->findDataRI($riHdrNo);

                $list = $fresh['diagKeperawatan'][$tipe] ?? [];

                $newList = array_values(array_filter(
                    $list,
                    fn($e) => ($e[$tipe . '_id'] ?? null) !== $id
                ));

                $fresh['diagKeperawatan'][$tipe] = $newList; // ✅ cukup ini saja

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
    public function tambahFormB($formDiagnosaKeperawatan_id): void
    {
        // Set formDiagnosaKeperawatan_id yang dipilih
        $this->formIntervensiImplementasi['formDiagnosaKeperawatan_id'] = $formDiagnosaKeperawatan_id;

        // Auto set tanggal Intervensi / Implementasi
        $this->formIntervensiImplementasi['tanggal'] = Carbon::now()->format('d/m/Y H:i:s');

        // Tampilkan Intervensi / Implementasi
        $this->showFormB = true;

        // Scroll ke Intervensi / Implementasi
        $this->dispatchBrowserEvent('scroll-to-form-b');
    }

    /* ======================================
     * 🖨️ CETAK FORM A
     * ====================================== */
    public function cetakFormA($formDiagnosaKeperawatan_id)
    {
        $riHdrNo = $this->dataDaftarRi['riHdrNo'] ?? $this->riHdrNoRef ?? null;
        if (!$riHdrNo) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Nomor RI tidak ditemukan.');
            return;
        }

        try {
            $queryIdentitas = DB::table('rsmst_identitases')
                ->select('int_name', 'int_phone1', 'int_phone2', 'int_fax', 'int_address', 'int_city')
                ->first();

            // Cari data Form Diagnosa Keperawatan berdasarkan ID
            $formDiagnosaKeperawatan = collect(
                $this->dataDaftarRi['diagKeperawatan']['formDiagnosaKeperawatan'] ?? []
            )
                ->firstWhere('formDiagnosaKeperawatan_id', $formDiagnosaKeperawatan_id);

            if (!$formDiagnosaKeperawatan) {
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Data Form Diagnosa Keperawatan tidak ditemukan.');
                return;
            }

            $dataPasien = $this->findDataMasterPasien($this->dataDaftarRi['regNo'] ?? '');

            $data = [
                'identitasRs'  => $queryIdentitas,
                'dataPasien'   => $dataPasien ?? [],
                'dataDaftarRi' => $this->dataDaftarRi,
                'dataFormA'    => $formDiagnosaKeperawatan,
            ];

            // NOTE: ubah view cetak sesuai struktur baru
            $pdfContent = Pdf::loadView('livewire.cetak.cetak-form-a-print', $data)->output();

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('Berhasil mencetak Form Diagnosa Keperawatan.');

            return response()->streamDownload(
                fn() => print($pdfContent),
                'form-a-' . $formDiagnosaKeperawatan_id . '.pdf'
            );
        } catch (Exception $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Gagal mencetak Form Diagnosa Keperawatan: ' . $e->getMessage());
        }
    }

    /* ======================================
     * 🖨️ CETAK FORM B
     * ====================================== */
    public function cetakFormB($formIntervensiImplementasi_id)
    {
        $riHdrNo = $this->dataDaftarRi['riHdrNo'] ?? $this->riHdrNoRef ?? null;
        if (!$riHdrNo) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Nomor RI tidak ditemukan.');
            return;
        }

        try {
            $queryIdentitas = DB::table('rsmst_identitases')
                ->select('int_name', 'int_phone1', 'int_phone2', 'int_fax', 'int_address', 'int_city')
                ->first();

            // Cari data Intervensi / Implementasi berdasarkan ID
            $formIntervensiImplementasi = collect(
                $this->dataDaftarRi['diagKeperawatan']['formIntervensiImplementasi'] ?? []
            )
                ->firstWhere('formIntervensiImplementasi_id', $formIntervensiImplementasi_id);

            if (!$formIntervensiImplementasi) {
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Data Intervensi / Implementasi tidak ditemukan.');
                return;
            }

            $dataPasien = $this->findDataMasterPasien($this->dataDaftarRi['regNo'] ?? '');

            $data = [
                'identitasRs'  => $queryIdentitas,
                'dataPasien'   => $dataPasien ?? [],
                'dataDaftarRi' => $this->dataDaftarRi,
                'dataFormB'    => $formIntervensiImplementasi,
            ];

            // NOTE: ubah view cetak sesuai struktur baru
            $pdfContent = Pdf::loadView('livewire.cetak.cetak-form-b-print', $data)->output();

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('Berhasil mencetak Intervensi / Implementasi.');

            return response()->streamDownload(
                fn() => print($pdfContent),
                'form-b-' . $formIntervensiImplementasi_id . '.pdf'
            );
        } catch (Exception $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Gagal mencetak Intervensi / Implementasi: ' . $e->getMessage());
        }
    }

    /* ======================================
     * 🔍 Fetch data awal
     * ====================================== */
    private function findData($riHdrNo): void
    {
        $this->dataDaftarRi = $this->findDataRI($riHdrNo);
    }

    public function render()
    {
        return view('livewire.emr-r-i.diagnosa-keperawatan-r-i.diagnosa-keperawatan-r-i');
    }
}
