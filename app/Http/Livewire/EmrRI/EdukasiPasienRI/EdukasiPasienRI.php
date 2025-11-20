<?php

namespace App\Http\Livewire\EmrRI\EdukasiPasienRI;


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


class EdukasiPasienRI extends Component
{
    use EmrRITrait, MasterPasienTrait;


    // listener from blade////////////////
    protected $listeners = [
        'syncronizeAssessmentDokterRIFindData' => 'mount',
        'syncronizeAssessmentPerawatRIFindData' => 'mount'
    ];

    //////////////////////////////
    // Ref on top bar
    //////////////////////////////
    public $riHdrNoRef;
    public array $dataDaftarRi = [];

    /**
     * ============================================
     * FORM EDUKASI PASIEN (Livewire)
     * --------------------------------------------
     * Digunakan untuk mencatat edukasi medis kepada pasien / keluarga.
     * ============================================
     */
    public array $formEntryEdukasiPasien = [
        "tglEdukasi" => "", // 📅 Tanggal dan jam edukasi (format: d/m/Y H:i:s)

        // =========================
        // 👨‍⚕️ Dokter pelaksana
        // =========================
        "dokterPelaksanaTindakan" => [
            "drId"   => "",  // ID dokter (opsional)
            "drName" => "",  // Nama dokter wajib diisi
        ],

        // =========================
        // 👩‍⚕️ Pemberi informasi (petugas)
        // =========================
        "pemberiInformasi" => [
            "petugasCode" => "", // Kode petugas
            "petugasName" => "", // Nama petugas
        ],

        // =========================
        // 👨‍👩‍🦱 Penerima informasi
        // =========================
        "penerimaInformasi" => [
            "name"      => "", // Nama penerima (pasien/keluarga)
            "hubungan"  => "", // Hubungan dengan pasien
            "signature" => "", // Base64 tanda tangan penerima
        ],

        // =========================
        // 📋 Detail edukasi medis
        // =========================
        "detailInformasi" => [
            "diagnosis"  => ["desc" => ""],  // Diagnosis kerja / banding
            "dasar"      => ["desc" => ""],  // Dasar diagnosis
            "rencana" => ["desc" => ""],  // Rencana pengobatan
            "indikasi" => ["desc" => ""], // Indikasi tindakan
            "tujuan"     => ["desc" => ""],  // Tujuan tindakan
            "risiko"     => ["desc" => ""],  // Risiko tindakan
            "komplikasi" => ["desc" => ""],  // Komplikasi yang mungkin
            "prognosis"  => ["desc" => ""],  // Prognosis / hasil akhir
            "alternatif" => ["desc" => ""],  // Alternatif lain & risikonya
            "tanpaTindakan"   => ["desc" => ""],  // Tanpa tindakan
        ],
    ];


    /**
     * ============================================
     * 📏 VALIDATION RULES
     * ============================================
     */
    protected array $rules = [
        // Tanggal edukasi
        'formEntryEdukasiPasien.tglEdukasi' => 'required|date_format:d/m/Y H:i:s',

        // Dokter pelaksana
        'formEntryEdukasiPasien.dokterPelaksanaTindakan.drId'   => 'nullable|string|max:50',
        'formEntryEdukasiPasien.dokterPelaksanaTindakan.drName' => 'required|string|max:100',

        // Pemberi informasi
        'formEntryEdukasiPasien.pemberiInformasi.petugasCode' => 'required|string|max:50',
        'formEntryEdukasiPasien.pemberiInformasi.petugasName' => 'required|string|max:100',

        // Penerima informasi
        'formEntryEdukasiPasien.penerimaInformasi.name'      => 'required|string|max:100',
        'formEntryEdukasiPasien.penerimaInformasi.hubungan'  => 'required|string|max:100',
        'formEntryEdukasiPasien.penerimaInformasi.signature' => 'required|string',

        // Detail edukasi (catatan tiap poin)
        'formEntryEdukasiPasien.detailInformasi'                              => 'required|array',
        'formEntryEdukasiPasien.detailInformasi.diagnosis.desc'               => 'nullable|string|max:1000',
        'formEntryEdukasiPasien.detailInformasi.dasar.desc'                   => 'nullable|string|max:1000',
        'formEntryEdukasiPasien.detailInformasi.rencana.desc'                 => 'nullable|string|max:1000',
        'formEntryEdukasiPasien.detailInformasi.indikasi.desc'                => 'nullable|string|max:1000',
        'formEntryEdukasiPasien.detailInformasi.tanpaTindakan.desc'           => 'nullable|string|max:1000',
        'formEntryEdukasiPasien.detailInformasi.tujuan.desc'                  => 'nullable|string|max:1000',
        'formEntryEdukasiPasien.detailInformasi.risiko.desc'                  => 'nullable|string|max:1000',
        'formEntryEdukasiPasien.detailInformasi.komplikasi.desc'              => 'nullable|string|max:1000',
        'formEntryEdukasiPasien.detailInformasi.prognosis.desc'               => 'nullable|string|max:1000',
        'formEntryEdukasiPasien.detailInformasi.alternatif.desc'              => 'nullable|string|max:1000',
    ];


    /**
     * ============================================
     * 💬 VALIDATION MESSAGES
     * ============================================
     */
    protected array $messages = [
        // Tanggal
        'formEntryEdukasiPasien.tglEdukasi.required'     => 'Tanggal edukasi wajib diisi.',
        'formEntryEdukasiPasien.tglEdukasi.date_format'  => 'Format tanggal harus dd/mm/yyyy hh24:mi:ss.',

        // Dokter pelaksana
        'formEntryEdukasiPasien.dokterPelaksanaTindakan.drName.required' => 'Nama dokter pelaksana wajib diisi.',
        'formEntryEdukasiPasien.dokterPelaksanaTindakan.drName.max'      => 'Nama dokter maksimal 100 karakter.',

        // Pemberi informasi
        'formEntryEdukasiPasien.pemberiInformasi.petugasCode.required' => 'Kode petugas wajib diisi.',
        'formEntryEdukasiPasien.pemberiInformasi.petugasName.required' => 'Nama petugas wajib diisi.',

        // Penerima informasi
        'formEntryEdukasiPasien.penerimaInformasi.name.required'      => 'Nama penerima informasi wajib diisi.',
        'formEntryEdukasiPasien.penerimaInformasi.hubungan.required'  => 'Hubungan penerima dengan pasien wajib diisi.',
        'formEntryEdukasiPasien.penerimaInformasi.signature.required' => 'Tanda tangan penerima wajib diisi.',

        // Detail edukasi
        'formEntryEdukasiPasien.detailInformasi.required' => 'Detail informasi wajib diisi (boleh kosong per baris).',
    ];



    public $sasaranEdukasiSignature;

    public function setTglEdukasi(): void
    {
        $this->formEntryEdukasiPasien['tglEdukasi'] = Carbon::now()->format('d/m/Y H:i:s');
    }

    public function addEdukasiPasien(): void
    {
        // Auto isi pemberi informasi & tgl edukasi & ttd penerima
        $this->formEntryEdukasiPasien['pemberiInformasi']['petugasName'] = auth()->user()->myuser_name;
        $this->formEntryEdukasiPasien['pemberiInformasi']['petugasCode'] = auth()->user()->myuser_code;
        $this->formEntryEdukasiPasien['penerimaInformasi']['signature']  = $this->sasaranEdukasiSignature;
        if (empty($this->formEntryEdukasiPasien['tglEdukasi'])) {
            $this->formEntryEdukasiPasien['tglEdukasi'] = Carbon::now()->format('d/m/Y H:i:s');
        }
        // Validasi form
        $this->validate($this->rules, $this->messages);
        // Dapatkan RI header
        $riHdrNo = $this->dataDaftarRi['riHdrNo'] ?? $this->riHdrNoRef ?? null;
        if (!$riHdrNo) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError("riHdrNo kosong.");
            return;
        }

        $lockKey = "ri:{$riHdrNo}"; // shared lock antar modul

        try {
            Cache::lock($lockKey, 5)->block(3, function () use ($riHdrNo) {
                // Re-read state paling baru DI DALAM lock
                $fresh = $this->findDataRI($riHdrNo);
                if (!is_array($fresh)) $fresh = [];
                if (!isset($fresh['edukasiPasien']) || !is_array($fresh['edukasiPasien'])) {
                    $fresh['edukasiPasien'] = [];
                }

                // Entry dengan metadata
                $entry               = $this->formEntryEdukasiPasien;
                $entry['id']         = (string) Str::uuid();
                $entry['created_at'] = Carbon::now()->format('Y-m-d H:i:s');

                // Tambahkan dan simpan atomik
                $fresh['edukasiPasien'][] = $entry;

                DB::transaction(function () use ($riHdrNo, $fresh) {
                    $this->updateJsonRI($riHdrNo, $fresh);
                });

                // Sinkronkan ke state lokal biar UI langsung update
                $this->dataDaftarRi = $fresh;
            });
        } catch (LockTimeoutException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Sistem sibuk, gagal memperoleh lock. Coba lagi.');
            return;
        }

        // Emit sinkronisasi & notif
        $this->emit('syncronizeAssessmentPerawatRIFindData');

        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
            ->addSuccess("Data edukasi pasien berhasil ditambahkan.");

        // (opsional) reset sebagian field form kalau perlu
        $this->reset(['formEntryEdukasiPasien', 'sasaranEdukasiSignature']);
    }

    public function removeEdukasiPasienById(string $id): void
    {
        $riHdrNo = $this->dataDaftarRi['riHdrNo'] ?? $this->riHdrNoRef ?? null;
        if (!$riHdrNo) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("riHdrNo kosong.");
            return;
        }

        $lockKey = "ri:{$riHdrNo}";
        try {
            Cache::lock($lockKey, 5)->block(3, function () use ($riHdrNo, $id) {
                $fresh = $this->findDataRI($riHdrNo);
                if (!is_array($fresh)) $fresh = [];
                $list = $fresh['edukasiPasien'] ?? [];

                // filter out by id
                $newList = array_values(array_filter($list, fn($e) => ($e['id'] ?? null) !== $id));

                if (count($newList) === count($list)) {
                    toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Data tidak ditemukan atau sudah dihapus.");
                    return;
                }

                $fresh['edukasiPasien'] = $newList;

                DB::transaction(function () use ($riHdrNo, $fresh) {
                    $this->updateJsonRI($riHdrNo, $fresh);
                });

                $this->dataDaftarRi = $fresh;
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Data edukasi berhasil dihapus.");
            });
        } catch (LockTimeoutException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Sistem sibuk, gagal memperoleh lock. Coba lagi.');
            return;
        }

        $this->emit('syncronizeAssessmentPerawatRIFindData');
    }

    public function cetakEdukasiPasienById(string $id)
    {
        $queryIdentitas = DB::table('rsmst_identitases')
            ->select('int_name', 'int_phone1', 'int_phone2', 'int_fax', 'int_address', 'int_city')
            ->first();

        $riHdrNo = $this->dataDaftarRi['riHdrNo'] ?? $this->riHdrNoRef ?? null;
        if (!$riHdrNo) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Nomor RI tidak ditemukan.');
            return;
        }

        try {
            $list = $this->dataDaftarRi['edukasiPasien'] ?? [];
            $dataPasien = $this->findDataMasterPasien($this->dataDaftarRi['regNo'] ?? '');

            $dataEdukasi = collect($list)->firstWhere('id', $id);

            if (!$dataEdukasi) {
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                    ->addError('Data edukasi tidak ditemukan.');
                return;
            }

            $data = [
                'identitasRs'  => $queryIdentitas,
                'dataPasien'   => $dataPasien ?? [],
                'dataDaftarRi' => $this->dataDaftarRi,
                'edukasi'      => $dataEdukasi,
            ];

            $pdfContent = Pdf::loadView('livewire.cetak.cetak-edukasi-pasien-r-i-print', $data)->output();

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addSuccess('Berhasil mencetak edukasi pasien.');


            return response()->streamDownload(
                fn() => print($pdfContent),
                'edukasi-pasien-' . $id . '.pdf'
            );
        } catch (Exception $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Gagal mencetak PDF: ' . $e->getMessage());
        }
    }




    private function findData($riHdrNo): void
    {
        $this->dataDaftarRi = $this->findDataRI($riHdrNo);
    }


    // when new form instance
    public function mount()
    {
        $this->findData($this->riHdrNoRef);
    }

    // select data start////////////////
    public function render()
    {

        return view('livewire.emr-r-i.edukasi-pasien-r-i.edukasi-pasien-r-i');
    }
    // select data end////////////////


}
