<?php

namespace App\Http\Livewire\EmrUGD\FormPenjaminanOrientasiKamar;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Validation\ValidationException;

use Livewire\Component;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Http\Traits\EmrUGD\EmrUGDTrait;
use App\Http\Traits\MasterPasien\MasterPasienTrait;

class FormPenjaminanOrientasiKamar extends Component
{
    use EmrUGDTrait, MasterPasienTrait;

    //////////////////////////////
    // Ref on top bar
    //////////////////////////////
    public $rjNoRef;
    public $regNoRef;
    public array $dataDaftarUgd = [];
    public array $dataPasien    = [];
    // opsi jenis penjamin (sesuai form)
    public array $jenisPenjaminOptions = [
        ['id' => 'BPJS_KESEHATAN',       'desc' => 'BPJS Kesehatan'],
        ['id' => 'BPJS_KETENAGAKERJAAN', 'desc' => 'BPJS Ketenagakerjaan'],
        ['id' => 'ASABRI_TASPEN',       'desc' => 'ASABRI / TASPEN'],
        ['id' => 'JASA_RAHARJA',        'desc' => 'Jasa Raharja'],
        ['id' => 'ASURANSI_LAIN',       'desc' => 'Asuransi Lain'],
        ['id' => 'TANPA_KARTU',         'desc' => 'Tidak memiliki Kartu Penjaminan'],
    ];

    // master orientasi kamar
    public array $kelasKamarOptions = [
        'VIP' => [
            'nama'       => 'VIP',
            'tarif'      => 700000,
            'tarifLabel' => 'Rp 700.000 / hari',
            'fasilitas'  => [
                '1 tempat tidur pasien',
                'AC',
                'Kamar mandi di dalam',
                'Sofa bed penunggu',
                'Kulkas',
                'Televisi LED',
                'Almari',
                'Overbed table',
                'Dispenser air minum',
                'Makan siang 1 penunggu',
            ],
        ],
        'KELAS_I' => [
            'nama'       => 'Kelas I',
            'tarif'      => 275000,
            'tarifLabel' => 'Rp 275.000 / hari (tarif tertinggi Kelas I)',
            'fasilitas'  => [
                '1 tempat tidur pasien',
                'Kamar mandi di dalam',
                'Sofa bed penunggu',
                'Kulkas',
                'Televisi LED',
                'Almari',
                'Kipas angin',
                'Makan siang 1 penunggu',
            ],
        ],
        'KELAS_II' => [
            'nama'       => 'Kelas II',
            'tarif'      => 175000,
            'tarifLabel' => 'Rp 175.000 / hari (tarif tertinggi Kelas II)',
            'fasilitas'  => [
                '2 tempat tidur pasien',
                'Kamar mandi di dalam',
                'Kursi penunggu',
                'Televisi',
                'Almari',
                'Kipas angin',
                'Makan siang 1 penunggu',
            ],
        ],
        'KELAS_III' => [
            'nama'       => 'Kelas III',
            'tarif'      => 175000,
            'tarifLabel' => 'Rp 175.000 / hari',
            'fasilitas'  => [
                '4 tempat tidur pasien',
                'Kamar mandi di dalam',
                'Televisi di luar ruangan',
                'Kursi',
                'Almari',
                'Kipas angin',
            ],
        ],
    ];

    /**
     * Satu entri form pernyataan kepemilikan kartu penjaminan biaya
     */
    public array $formPenjaminanOrientasiKamar = [
        // Tanggal form (supaya mudah tracking)
        'tanggalFormPenjaminan' => '', // format: d/m/Y H:i:s

        // Data yang bertandatangan
        'pembuatNama'         => '',
        // Terhadap diri: saya sendiri / istri / anak / dst dari pasien
        'hubunganDenganPasien' => '',
        'pembuatUmur'         => '',
        'pembuatJenisKelamin' => '', // L / P
        'pembuatAlamat'       => '',


        // Kepemilikan kartu penjaminan
        'jenisPenjamin'  => '',
        'asuransiLain'   => '',

        // Orientasi & pilihan kelas kamar
        'kelasKamar'               => '',   // VIP / KELAS_I / KELAS_II / KELAS_III
        'orientasiKamarDijelaskan' => false,

        // Tanda tangan pembuat pernyataan
        'signaturePembuat'     => '',
        'signaturePembuatDate' => '',

        // Tanda tangan saksi keluarga
        'signatureSaksiKeluarga'     => '',
        'signatureSaksiKeluargaDate' => '',
        'namaSaksiKeluarga'          => '',

        // Petugas rumah sakit (tanpa canvas, pakai user yang login)
        'namaPetugas'   => '',
        'kodePetugas'   => '',
        'petugasDate'   => '',
    ];

    // canvas dari UI
    public $signature;       // pembuat pernyataan
    public $signatureSaksi;  // saksi keluarga

    protected $rules = [
        'formPenjaminanOrientasiKamar.tanggalFormPenjaminan' => 'required|date_format:d/m/Y H:i:s',

        'formPenjaminanOrientasiKamar.pembuatNama'         => 'required',
        'formPenjaminanOrientasiKamar.pembuatUmur'         => 'required|numeric',
        'formPenjaminanOrientasiKamar.pembuatJenisKelamin' => 'required|in:L,P',
        'formPenjaminanOrientasiKamar.pembuatAlamat'       => 'required',

        'formPenjaminanOrientasiKamar.hubunganDenganPasien' => 'required',


        'formPenjaminanOrientasiKamar.jenisPenjamin' => 'required|in:BPJS_KESEHATAN,BPJS_KETENAGAKERJAAN,ASABRI_TASPEN,JASA_RAHARJA,ASURANSI_LAIN,TANPA_KARTU',
        'formPenjaminanOrientasiKamar.asuransiLain'  => 'required_if:formPenjaminanOrientasiKamar.jenisPenjamin,ASURANSI_LAIN',

        'formPenjaminanOrientasiKamar.kelasKamar'               => 'required|in:VIP,KELAS_I,KELAS_II,KELAS_III',
        'formPenjaminanOrientasiKamar.orientasiKamarDijelaskan' => 'accepted',

        'formPenjaminanOrientasiKamar.signaturePembuat'     => 'required',
        'formPenjaminanOrientasiKamar.signaturePembuatDate' => 'required|date_format:d/m/Y H:i:s',

        'formPenjaminanOrientasiKamar.signatureSaksiKeluarga'     => 'required',
        'formPenjaminanOrientasiKamar.signatureSaksiKeluargaDate' => 'required|date_format:d/m/Y H:i:s',
        'formPenjaminanOrientasiKamar.namaSaksiKeluarga'          => 'required',

        'formPenjaminanOrientasiKamar.namaPetugas' => 'required',
        'formPenjaminanOrientasiKamar.kodePetugas' => 'required',
        'formPenjaminanOrientasiKamar.petugasDate' => 'required|date_format:d/m/Y H:i:s',
    ];

    protected $messages = [
        'required'     => ':attribute wajib diisi.',
        'required_if'  => ':attribute wajib diisi.',
        'numeric'      => ':attribute harus berupa angka.',
        'in'           => ':attribute tidak valid.',
        'accepted'     => ':attribute wajib disetujui.',
        'date_format'  => ':attribute harus dengan format dd/mm/yyyy hh24:mi:ss',
    ];

    protected $attributes = [
        'formPenjaminanOrientasiKamar.tanggalFormPenjaminan' => 'Tanggal Form Pernyataan Penjaminan',

        'formPenjaminanOrientasiKamar.pembuatNama'         => 'Nama pembuat pernyataan',
        'formPenjaminanOrientasiKamar.pembuatUmur'         => 'Umur pembuat pernyataan',
        'formPenjaminanOrientasiKamar.pembuatJenisKelamin' => 'Jenis kelamin pembuat pernyataan',
        'formPenjaminanOrientasiKamar.pembuatAlamat'       => 'Alamat pembuat pernyataan',

        'formPenjaminanOrientasiKamar.hubunganDenganPasien' => 'Hubungan dengan pasien',


        'formPenjaminanOrientasiKamar.jenisPenjamin' => 'Jenis kartu penjaminan',
        'formPenjaminanOrientasiKamar.asuransiLain'  => 'Nama asuransi lain',

        'formPenjaminanOrientasiKamar.kelasKamar'               => 'Kelas kamar yang dipilih',
        'formPenjaminanOrientasiKamar.orientasiKamarDijelaskan' => 'Orientasi fasilitas kamar',

        'formPenjaminanOrientasiKamar.signaturePembuat'           => 'Tanda tangan pembuat pernyataan',
        'formPenjaminanOrientasiKamar.signaturePembuatDate'       => 'Waktu tanda tangan pembuat pernyataan',
        'formPenjaminanOrientasiKamar.signatureSaksiKeluarga'     => 'Tanda tangan saksi keluarga',
        'formPenjaminanOrientasiKamar.signatureSaksiKeluargaDate' => 'Waktu tanda tangan saksi keluarga',
        'formPenjaminanOrientasiKamar.namaSaksiKeluarga'          => 'Nama saksi keluarga',

        'formPenjaminanOrientasiKamar.namaPetugas' => 'Nama petugas rumah sakit',
        'formPenjaminanOrientasiKamar.kodePetugas' => 'Kode petugas rumah sakit',
        'formPenjaminanOrientasiKamar.petugasDate' => 'Waktu pengesahan petugas',
    ];

    public function submit()
    {
        if (!$this->signature) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Tanda tangan pembuat pernyataan belum diisi.');
            return;
        }
        if (!$this->signatureSaksi) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Tanda tangan saksi keluarga belum diisi.');
            return;
        }

        // set tanggal form kalau belum diisi manual
        if (empty($this->formPenjaminanOrientasiKamar['tanggalFormPenjaminan'])) {
            $this->formPenjaminanOrientasiKamar['tanggalFormPenjaminan'] =
                Carbon::now(config('app.timezone'))->format('d/m/Y H:i:s');
        }

        // set tanda tangan dari UI ke array
        $now = Carbon::now(config('app.timezone'))->format('d/m/Y H:i:s');

        $this->formPenjaminanOrientasiKamar['signaturePembuat']     = (string)($this->signature ?? '');
        $this->formPenjaminanOrientasiKamar['signaturePembuatDate'] = $now;

        $this->formPenjaminanOrientasiKamar['signatureSaksiKeluarga']     = (string)($this->signatureSaksi ?? '');
        $this->formPenjaminanOrientasiKamar['signatureSaksiKeluargaDate'] = $now;

        try {
            $this->validate($this->rules, $this->messages, $this->attributes);
        } catch (ValidationException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError($e->validator->errors()->first());
            return;
        }

        // simpan
        $this->store();
    }

    public function store()
    {
        $rjNo = $this->dataDaftarUgd['rjNo'] ?? $this->rjNoRef ?? null;
        if (!$rjNo) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('rjNo kosong.');
            return;
        }

        $lockKey = "ugd:{$rjNo}";
        try {
            Cache::lock($lockKey, 5)->block(3, function () use ($rjNo) {
                DB::transaction(function () use ($rjNo) {
                    // ambil data TERBARU
                    $fresh = $this->findDataUGD($rjNo) ?: [];

                    // list form penjaminan
                    if (
                        !isset($fresh['formPenjaminanOrientasiKamar'])
                        || !is_array($fresh['formPenjaminanOrientasiKamar'])
                    ) {
                        $fresh['formPenjaminanOrientasiKamar'] = [];
                    }

                    // idempoten sederhana (berdasar timestamp tanda tangan pembuat)
                    $exists = collect($fresh['formPenjaminanOrientasiKamar'])
                        ->firstWhere('signaturePembuatDate', $this->formPenjaminanOrientasiKamar['signaturePembuatDate']);

                    if (!$exists) {
                        $fresh['formPenjaminanOrientasiKamar'][] = $this->formPenjaminanOrientasiKamar;
                    }

                    $this->updateJsonUGD($rjNo, $fresh);
                    $this->dataDaftarUgd = $fresh;
                });
            });

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addSuccess("Form Pernyataan Kepemilikan Kartu Penjaminan Biaya tersimpan.");
            $this->formPenjaminanOrientasiKamar = $this->defaultForm();
            $this->signature = null;
            $this->signatureSaksi = null;
        } catch (LockTimeoutException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Sistem sibuk, gagal memperoleh lock. Coba lagi.');
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Gagal menyimpan Form Pernyataan Kepemilikan Kartu Penjaminan Biaya.');
        }
    }

    private function findData($rjno): void
    {
        $this->dataDaftarUgd = $this->findDataUGD($rjno) ?: [];

        if (
            !isset($this->dataDaftarUgd['formPenjaminanOrientasiKamar'])
            || !is_array($this->dataDaftarUgd['formPenjaminanOrientasiKamar'])
        ) {
            $this->dataDaftarUgd['formPenjaminanOrientasiKamar'] = [];
        }

        // Cari regNo dari data UGD, fallback ke regNoRef kalau belum ada
        $regNo = $this->dataDaftarUgd['regNo'] ?? $this->regNoRef ?? null;
        // Ambil data master pasien kalau regNo ada
        if ($regNo) {
            $this->dataPasien = $this->findDataMasterPasien($regNo) ?? [];
            // sekalian sync regNoRef kalau mau
            $this->regNoRef   = $regNo;
        } else {
            $this->dataPasien = [];
        }
    }

    public function setTanggalFormPenjaminan()
    {
        $this->formPenjaminanOrientasiKamar['tanggalFormPenjaminan']
            = Carbon::now(config('app.timezone'))->format('d/m/Y H:i:s');
    }

    // set petugas RS dari user yang login
    public function setPetugasPemeriksa()
    {
        $code = auth()->user()->myuser_code ?? '';
        $name = auth()->user()->myuser_name ?? '';

        if (empty($this->formPenjaminanOrientasiKamar['namaPetugas'])) {
            $this->formPenjaminanOrientasiKamar['namaPetugas'] = $name;
            $this->formPenjaminanOrientasiKamar['kodePetugas'] = $code;
            $this->formPenjaminanOrientasiKamar['petugasDate'] =
                Carbon::now(config('app.timezone'))->format('d/m/Y H:i:s');
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError("Data Petugas Rumah Sakit sudah diisi.");
        }
    }

    private function defaultForm(): array
    {
        return [
            'tanggalFormPenjaminan' => '',

            'pembuatNama'         => '',
            'pembuatUmur'         => '',
            'pembuatJenisKelamin' => '',
            'pembuatAlamat'       => '',

            'hubunganDenganPasien' => '',

            'jenisPenjamin'  => '',
            'asuransiLain'   => '',

            'kelasKamar'               => '',
            'orientasiKamarDijelaskan' => false,

            'signaturePembuat'     => '',
            'signaturePembuatDate' => '',

            'signatureSaksiKeluarga'     => '',
            'signatureSaksiKeluargaDate' => '',
            'namaSaksiKeluarga'          => '',

            'namaPetugas' => '',
            'kodePetugas' => '',
            'petugasDate' => '',
        ];
    }

    public function cetakFormPenjaminan(string $signaturePembuatDate)
    {
        // Pastikan data UGD terbaru
        if (empty($this->dataDaftarUgd)) {
            $this->findData($this->rjNoRef);
        }

        $rjNo = $this->dataDaftarUgd['rjNo'] ?? $this->rjNoRef ?? null;
        if (!$rjNo) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Data UGD (rjNo) tidak ditemukan.');
            return;
        }

        $regNo = $this->dataDaftarUgd['regNo'] ?? $this->regNoRef ?? null;
        if (!$regNo) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Nomor rekam medis tidak ditemukan.');
            return;
        }

        $listForm = $this->dataDaftarUgd['formPenjaminanOrientasiKamar'] ?? [];
        if (!is_array($listForm) || count($listForm) === 0) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Data Form Pernyataan Kepemilikan Kartu Penjaminan Biaya belum tersedia.');
            return;
        }

        $form = collect($listForm)->firstWhere('signaturePembuatDate', $signaturePembuatDate);
        if (!$form) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Data form yang dipilih tidak ditemukan.');
            return;
        }

        try {
            $identitasRs = DB::table('rsmst_identitases')
                ->select('int_name', 'int_phone1', 'int_phone2', 'int_fax', 'int_address', 'int_city')
                ->first();

            $dataPasien = $this->findDataMasterPasien($regNo) ?? [];

            $data = [
                'identitasRs' => $identitasRs,
                'dataPasien'  => $dataPasien,
                'dataUgd'     => $this->dataDaftarUgd,
                'form'        => $form,
            ];

            // Buat view cetak sendiri, mis: livewire.cetak.cetak-form-penjaminan-orientasi-kamar-print
            $pdfContent = Pdf::loadView(
                'livewire.cetak.cetak-form-penjaminan-orientasi-kamar-print',
                $data
            )->output();

            toastr()->closeOnHover(true)->closeDuration(3)->closeOnHover(true)->positionClass('toast-top-left')
                ->addSuccess('Berhasil mencetak Form Pernyataan Kepemilikan Kartu Penjaminan Biaya.');

            return response()->streamDownload(function () use ($pdfContent) {
                echo $pdfContent;
            }, 'form-penjaminan-biaya-' . $rjNo . '.pdf');
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Gagal mencetak PDF: ' . $e->getMessage());
        }
    }

    public function mount()
    {
        $this->findData($this->rjNoRef);
    }

    public function render()
    {
        return view(
            'livewire.emr-u-g-d.form-penjaminan-orientasi-kamar.form-penjaminan-orientasi-kamar',
            []
        );
    }
}
