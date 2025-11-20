<?php

namespace App\Http\Livewire\EmrRI\EdukasiPasienRI;

use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use App\Http\Traits\EmrRI\EmrRITrait;
use Illuminate\Validation\ValidationException;
use App\Http\Traits\MasterPasien\MasterPasienTrait;

class EdukasiPasienRITerintegrasi extends Component
{
    use EmrRITrait, MasterPasienTrait;

    protected $listeners = [
        'syncronizeAssessmentDokterRIFindData'  => 'mount',
        'syncronizeAssessmentPerawatRIFindData' => 'mount',
    ];

    public $riHdrNoRef;
    public array $dataDaftarRi = [];

    // Signature dari <x-signature-pad />
    public $sasaranEdukasiSignature;

    /** ================== STATE ================== */
    public array $formEdukasiPasienTerintegrasi = [];

    /** Default schema (dipakai untuk init & reset) */
    private function defaultForm(): array
    {
        return [
            // HEADER
            'tglEdukasi' => '', // dd/mm/YYYY HH:ii:ss
            'pemberiInformasi' => [
                'petugasCode' => '',
                'petugasName' => '',
            ],

            // 1) Tujuan Edukasi (multi pilih)
            'tujuan' => [
                'opsi'    => [],   // ['penyakit','obat','nutrisi','aktivitas','perawatanRumah','pencegahan','lainnya']
                'lainnya' => '',
            ],

            // 2) Evaluasi Awal Kemampuan & Nilai
            'evaluasiAwal' => [
                'literasi' => null,               // 'Baik'|'Cukup'|'Kurang'|null
                'bahasaAtauPendidikan' => '',
                'hambatanEmosional'         => ['ada' => null, 'keterangan' => ''],
                'keterbatasanFisikKognitif' => ['ada' => null, 'keterangan' => ''],
                'nilaiKeyakinanBudaya'      => ['ada' => null, 'deskripsi' => ''],
                'preferensiInformasi'       => [
                    'opsi'    => [],           // ['lisan','tulisan','demonstrasi','video','poster','lainnya']
                    'lainnya' => '',
                ],
            ],

            // 3) Kebutuhan Edukasi (multi pilih)
            'kebutuhan' => [
                'opsi'    => [],  // ['penyakitHasil','prosedur','rencanaAsuhan','obatEfek','cuciTangan','alatRumah','warningSign','lainnya']
                'lainnya' => '',
            ],

            // 4) Metode & Media (multi pilih)
            'metodeMedia' => [
                'opsi'    => [],   // ['lisan','demonstrasi','leaflet','video','poster','lainnya']
                'lainnya' => '',
            ],

            // 5) Hasil Edukasi
            'hasil' => [
                'paham'             => ['ya' => null, 'keterangan' => ''],
                'mampuMengulang'    => ['ya' => null, 'keterangan' => ''],
                'tunjukkanSkill'    => ['ya' => null, 'keterangan' => ''],
                'sesuaiNilai'       => ['ya' => null, 'keterangan' => ''],
                'perluEdukasiUlang' => ['ya' => null, 'keterangan' => ''],
            ],

            // 6) Tindak Lanjut
            'tindakLanjut' => [
                'edukasiLanjutanTanggal' => '', // dd/mm/YYYY
                'dirujukKe'              => [], // ['dietisien','farmasi','rehabilitasi', ...]
                'tidakPerluTL'           => false,
            ],

            // 7) Tanda Tangan
            'ttd' => [
                'pasienKeluargaNama' => '',
                'pasienKeluargaTTD'  => '', // data:image/png;base64,...
            ],
        ];
    }

    /** ================== RULES ================== */
    protected array $rules = [
        // HEADER
        'formEdukasiPasienTerintegrasi.tglEdukasi'                   => 'required|date_format:d/m/Y H:i:s',
        'formEdukasiPasienTerintegrasi.pemberiInformasi.petugasCode' => 'required|string|max:50',
        'formEdukasiPasienTerintegrasi.pemberiInformasi.petugasName' => 'required|string|max:250',

        // 1) Tujuan
        'formEdukasiPasienTerintegrasi.tujuan.opsi'          => 'nullable|array',
        'formEdukasiPasienTerintegrasi.tujuan.opsi.*'        => 'in:penyakit,obat,nutrisi,aktivitas,perawatanRumah,pencegahan,lainnya',
        'formEdukasiPasienTerintegrasi.tujuan.lainnya'       => 'nullable|string|max:200',

        // 2) Evaluasi Awal
        'formEdukasiPasienTerintegrasi.evaluasiAwal.literasi'               => 'nullable|in:Baik,Cukup,Kurang',
        'formEdukasiPasienTerintegrasi.evaluasiAwal.bahasaAtauPendidikan'   => 'nullable|string|max:200',
        'formEdukasiPasienTerintegrasi.evaluasiAwal.hambatanEmosional.ada'        => 'nullable|boolean',
        'formEdukasiPasienTerintegrasi.evaluasiAwal.hambatanEmosional.keterangan' => 'nullable|string|max:300',
        'formEdukasiPasienTerintegrasi.evaluasiAwal.keterbatasanFisikKognitif.ada'        => 'nullable|boolean',
        'formEdukasiPasienTerintegrasi.evaluasiAwal.keterbatasanFisikKognitif.keterangan' => 'nullable|string|max:300',
        'formEdukasiPasienTerintegrasi.evaluasiAwal.nilaiKeyakinanBudaya.ada'       => 'nullable|boolean',
        'formEdukasiPasienTerintegrasi.evaluasiAwal.nilaiKeyakinanBudaya.deskripsi' => 'nullable|string|max:500',
        'formEdukasiPasienTerintegrasi.evaluasiAwal.preferensiInformasi.opsi'       => 'nullable|array',
        'formEdukasiPasienTerintegrasi.evaluasiAwal.preferensiInformasi.opsi.*'     => 'in:lisan,tulisan,demonstrasi,video,poster,lainnya',
        'formEdukasiPasienTerintegrasi.evaluasiAwal.preferensiInformasi.lainnya'    => 'nullable|string|max:200',

        // 3) Kebutuhan
        'formEdukasiPasienTerintegrasi.kebutuhan.opsi'    => 'nullable|array',
        'formEdukasiPasienTerintegrasi.kebutuhan.opsi.*'  => 'in:penyakitHasil,prosedur,rencanaAsuhan,obatEfek,cuciTangan,alatRumah,warningSign,lainnya',
        'formEdukasiPasienTerintegrasi.kebutuhan.lainnya' => 'nullable|string|max:200',

        // 4) Metode & Media
        'formEdukasiPasienTerintegrasi.metodeMedia.opsi'    => 'nullable|array',
        'formEdukasiPasienTerintegrasi.metodeMedia.opsi.*'  => 'in:lisan,demonstrasi,leaflet,video,poster,lainnya',
        'formEdukasiPasienTerintegrasi.metodeMedia.lainnya' => 'nullable|string|max:200',

        // 5) Hasil
        'formEdukasiPasienTerintegrasi.hasil.*.ya'         => 'nullable|boolean',
        'formEdukasiPasienTerintegrasi.hasil.*.keterangan' => 'nullable|string|max:300',

        // 6) Tindak Lanjut
        'formEdukasiPasienTerintegrasi.tindakLanjut.edukasiLanjutanTanggal' => 'nullable|date_format:d/m/Y',
        'formEdukasiPasienTerintegrasi.tindakLanjut.dirujukKe'              => 'nullable|array',
        'formEdukasiPasienTerintegrasi.tindakLanjut.dirujukKe.*'            => 'string|max:50',
        'formEdukasiPasienTerintegrasi.tindakLanjut.tidakPerluTL'           => 'boolean',

        // 7) TTD
        'formEdukasiPasienTerintegrasi.ttd.pasienKeluargaNama' => 'required|string|max:150',
        'formEdukasiPasienTerintegrasi.ttd.pasienKeluargaTTD'  => 'nullable|string',
        // NOTE: tidak ada rule pemberiEdukasiTTD karena tidak ada di model
    ];

    protected array $messages = [
        'required'    => ':attribute wajib diisi.',
        'max'         => ':attribute maksimal :max karakter.',
        'in'          => ':attribute berisi nilai yang tidak valid.',
        'boolean'     => ':attribute harus bernilai ya/tidak.',
        'array'       => ':attribute harus berupa daftar.',
        'date_format' => 'Format :attribute tidak sesuai.',

        'formEdukasiPasienTerintegrasi.tglEdukasi.date_format' => 'Format tanggal edukasi harus dd/mm/yyyy hh:ii:ss.',
    ];

    protected array $attributes = [
        'formEdukasiPasienTerintegrasi.tglEdukasi'                   => 'Tanggal edukasi',
        'formEdukasiPasienTerintegrasi.pemberiInformasi.petugasCode' => 'Kode petugas',
        'formEdukasiPasienTerintegrasi.pemberiInformasi.petugasName' => 'Nama petugas',
        'formEdukasiPasienTerintegrasi.tujuan.opsi'     => 'Tujuan edukasi',
        'formEdukasiPasienTerintegrasi.tujuan.lainnya'  => 'Tujuan (lainnya)',
        'formEdukasiPasienTerintegrasi.evaluasiAwal.literasi'                 => 'Kemampuan membaca/menulis',
        'formEdukasiPasienTerintegrasi.evaluasiAwal.bahasaAtauPendidikan'     => 'Bahasa/pendidikan',
        'formEdukasiPasienTerintegrasi.evaluasiAwal.hambatanEmosional.ada'    => 'Hambatan emosional/motivasi',
        'formEdukasiPasienTerintegrasi.evaluasiAwal.hambatanEmosional.keterangan' => 'Keterangan hambatan emosional',
        'formEdukasiPasienTerintegrasi.evaluasiAwal.keterbatasanFisikKognitif.ada'    => 'Keterbatasan fisik/kognitif',
        'formEdukasiPasienTerintegrasi.evaluasiAwal.keterbatasanFisikKognitif.keterangan' => 'Keterangan keterbatasan fisik/kognitif',
        'formEdukasiPasienTerintegrasi.evaluasiAwal.nilaiKeyakinanBudaya.ada'       => 'Nilai/keyakinan/budaya',
        'formEdukasiPasienTerintegrasi.evaluasiAwal.nilaiKeyakinanBudaya.deskripsi' => 'Deskripsi nilai/keyakinan/budaya',
        'formEdukasiPasienTerintegrasi.evaluasiAwal.preferensiInformasi.opsi'   => 'Preferensi menerima informasi',
        'formEdukasiPasienTerintegrasi.evaluasiAwal.preferensiInformasi.lainnya' => 'Preferensi (lainnya)',
        'formEdukasiPasienTerintegrasi.kebutuhan.opsi'    => 'Kebutuhan edukasi',
        'formEdukasiPasienTerintegrasi.kebutuhan.lainnya' => 'Kebutuhan (lainnya)',
        'formEdukasiPasienTerintegrasi.metodeMedia.opsi'    => 'Metode & media edukasi',
        'formEdukasiPasienTerintegrasi.metodeMedia.lainnya' => 'Metode/media (lainnya)',
        'formEdukasiPasienTerintegrasi.hasil.paham.ya'            => 'Hasil: memahami informasi',
        'formEdukasiPasienTerintegrasi.hasil.mampuMengulang.ya'   => 'Hasil: dapat mengulang informasi',
        'formEdukasiPasienTerintegrasi.hasil.tunjukkanSkill.ya'   => 'Hasil: menunjukkan keterampilan',
        'formEdukasiPasienTerintegrasi.hasil.sesuaiNilai.ya'      => 'Hasil: sesuai nilai/keyakinan',
        'formEdukasiPasienTerintegrasi.hasil.perluEdukasiUlang.ya' => 'Hasil: perlu edukasi ulang',
        'formEdukasiPasienTerintegrasi.hasil.*.keterangan'        => 'Keterangan hasil',
        'formEdukasiPasienTerintegrasi.tindakLanjut.edukasiLanjutanTanggal' => 'Tanggal edukasi lanjutan',
        'formEdukasiPasienTerintegrasi.tindakLanjut.dirujukKe'              => 'Rujukan tenaga ahli',
        'formEdukasiPasienTerintegrasi.tindakLanjut.tidakPerluTL'           => 'Tidak perlu tindak lanjut',
        'formEdukasiPasienTerintegrasi.ttd.pasienKeluargaNama' => 'Nama pasien/keluarga',
        'formEdukasiPasienTerintegrasi.ttd.pasienKeluargaTTD'  => 'TTD pasien/keluarga',
    ];

    /** ================== LIFECYCLE ================== */
    public function mount()
    {
        $this->formEdukasiPasienTerintegrasi = $this->defaultForm();

        // Prefill header: petugas + waktu
        $this->formEdukasiPasienTerintegrasi['pemberiInformasi']['petugasName'] = auth()->user()->myuser_name ?? '';
        $this->formEdukasiPasienTerintegrasi['pemberiInformasi']['petugasCode'] = auth()->user()->myuser_code ?? '';
        if (empty($this->formEdukasiPasienTerintegrasi['tglEdukasi'])) {
            $this->formEdukasiPasienTerintegrasi['tglEdukasi'] = Carbon::now()->format('d/m/Y H:i:s');
        }

        $this->findData($this->riHdrNoRef);
    }

    private function findData($riHdrNo): void
    {
        $this->dataDaftarRi = $this->findDataRI($riHdrNo);
    }

    /** ================== ACTIONS ================== */

    public function setEdukasiLanjutanToday(): void
    {
        $this->formEdukasiPasienTerintegrasi['tindakLanjut']['edukasiLanjutanTanggal']
            = Carbon::now()->format('d/m/Y');
    }

    public function resetFormEdukasi(): void
    {
        $this->formEdukasiPasienTerintegrasi = $this->defaultForm();

        // isi lagi header petugas + waktu now
        $this->formEdukasiPasienTerintegrasi['pemberiInformasi']['petugasName'] = auth()->user()->myuser_name ?? '';
        $this->formEdukasiPasienTerintegrasi['pemberiInformasi']['petugasCode'] = auth()->user()->myuser_code ?? '';
        $this->formEdukasiPasienTerintegrasi['tglEdukasi'] = Carbon::now()->format('d/m/Y H:i:s');

        $this->sasaranEdukasiSignature = null;
    }

    public function addEdukasiPasien(): void
    {
        // Pastikan header terisi (autofill terakhir sebelum validasi)
        if (empty($this->formEdukasiPasienTerintegrasi['tglEdukasi'])) {
            $this->formEdukasiPasienTerintegrasi['tglEdukasi'] = Carbon::now()->format('d/m/Y H:i:s');
        }
        $this->formEdukasiPasienTerintegrasi['pemberiInformasi']['petugasName'] = auth()->user()->myuser_name ?? '';
        $this->formEdukasiPasienTerintegrasi['pemberiInformasi']['petugasCode'] = auth()->user()->myuser_code ?? '';

        // Normalisasi radio/checkbox string → boolean
        $this->normalizeBooleansOnForm();

        // Validasi dinamis (wajibkan "lainnya" jika dipilih)
        $validator = Validator::make(
            ['formEdukasiPasienTerintegrasi' => $this->formEdukasiPasienTerintegrasi],
            $this->rules,
            $this->messages,
            $this->attributes
        );

        $validator->sometimes(
            'formEdukasiPasienTerintegrasi.tujuan.lainnya',
            ['required', 'string', 'max:200'],
            fn($data) => in_array('lainnya', $data['formEdukasiPasienTerintegrasi']['tujuan']['opsi'] ?? [])
        );
        $validator->sometimes(
            'formEdukasiPasienTerintegrasi.kebutuhan.lainnya',
            ['required', 'string', 'max:200'],
            fn($data) => in_array('lainnya', $data['formEdukasiPasienTerintegrasi']['kebutuhan']['opsi'] ?? [])
        );
        $validator->sometimes(
            'formEdukasiPasienTerintegrasi.metodeMedia.lainnya',
            ['required', 'string', 'max:200'],
            fn($data) => in_array('lainnya', $data['formEdukasiPasienTerintegrasi']['metodeMedia']['opsi'] ?? [])
        );

        try {
            $validator->validate();
        } catch (ValidationException $e) {
            // Tampilkan semua pesan error sebagai toast (batasi jika ingin)
            foreach ($e->validator->errors()->all() as $msg) {
                toastr()->closeOnHover(true)->closeDuration(4)->positionClass('toast-top-left')->addError($msg);
            }
            // Isi error bag Livewire agar error di field tetap tampil
            if (method_exists($this, 'setErrorBag')) {
                $this->setErrorBag($e->validator->getMessageBag());
            }
            return;
        }

        // Nomor RI
        $riHdrNo = $this->dataDaftarRi['riHdrNo'] ?? $this->riHdrNoRef ?? null;
        if (!$riHdrNo) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("riHdrNo kosong.");
            return;
        }

        // Simpan atomik + lock
        $lockKey = "ri:{$riHdrNo}";
        try {
            Cache::lock($lockKey, 5)->block(3, function () use ($riHdrNo) {
                $fresh = $this->findDataRI($riHdrNo);
                if (!is_array($fresh)) $fresh = [];
                if (!isset($fresh['edukasiPasienTerintegrasi']) || !is_array($fresh['edukasiPasienTerintegrasi'])) {
                    $fresh['edukasiPasienTerintegrasi'] = [];
                }

                // inject TTD base64 kalau ada
                if (!empty($this->sasaranEdukasiSignature)) {
                    $this->formEdukasiPasienTerintegrasi['ttd']['pasienKeluargaTTD'] = $this->sasaranEdukasiSignature;
                }

                $entry = [
                    'id'         => (string) Str::uuid(),
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'created_by' => [
                        'code' => auth()->user()->myuser_code ?? '',
                        'name' => auth()->user()->myuser_name ?? '',
                    ],
                    'form'       => $this->formEdukasiPasienTerintegrasi, // simpan form apa adanya
                ];

                $fresh['edukasiPasienTerintegrasi'][] = $entry;

                DB::transaction(function () use ($riHdrNo, $fresh) {
                    $this->updateJsonRI($riHdrNo, $fresh);
                });

                $this->dataDaftarRi = $fresh;
            });
        } catch (LockTimeoutException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Sistem sibuk, gagal memperoleh lock. Coba lagi.');
            return;
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(4)->positionClass('toast-top-left')
                ->addError('Terjadi kesalahan: ' . $e->getMessage());
            return;
        }

        $this->emit('syncronizeAssessmentPerawatRIFindData');
        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
            ->addSuccess("Data edukasi pasien berhasil ditambahkan.");

        // Reset form
        $this->resetFormEdukasi();
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
                $list = $fresh['edukasiPasienTerintegrasi'] ?? [];

                $newList = array_values(array_filter($list, fn($e) => ($e['id'] ?? null) !== $id));
                if (count($newList) === count($list)) {
                    toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Data tidak ditemukan atau sudah dihapus.");
                    return;
                }

                $fresh['edukasiPasienTerintegrasi'] = $newList;

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
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Nomor RI tidak ditemukan.');
            return;
        }

        try {
            $list        = $this->dataDaftarRi['edukasiPasienTerintegrasi'] ?? [];
            $dataPasien  = $this->findDataMasterPasien($this->dataDaftarRi['regNo'] ?? '');
            $dataEdukasi = collect($list)->firstWhere('id', $id);

            if (!$dataEdukasi) {
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Data edukasi tidak ditemukan.');
                return;
            }

            $data = [
                'identitasRs'  => $queryIdentitas,
                'dataPasien'   => $dataPasien ?? [],
                'dataDaftarRi' => $this->dataDaftarRi,
                'edukasi'      => $dataEdukasi, // form ada di $edukasi['form']
            ];


            $pdfContent = Pdf::loadView('livewire.cetak.cetak-edukasi-pasien-terintegrasi-r-i-print', $data)->output();

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('Berhasil mencetak edukasi pasien.');

            return response()->streamDownload(fn() => print($pdfContent), 'edukasi-pasien-' . $id . '.pdf');
        } catch (Exception $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Gagal mencetak PDF: ' . $e->getMessage());
        }
    }

    /** =============== Helpers =============== */
    private function normalizeBooleansOnForm(): void
    {
        $p = &$this->formEdukasiPasienTerintegrasi;

        foreach (['hambatanEmosional', 'keterbatasanFisikKognitif', 'nilaiKeyakinanBudaya'] as $k) {
            if (array_key_exists('ada', $p['evaluasiAwal'][$k] ?? [])) {
                $p['evaluasiAwal'][$k]['ada'] = filter_var(
                    $p['evaluasiAwal'][$k]['ada'],
                    FILTER_VALIDATE_BOOLEAN,
                    FILTER_NULL_ON_FAILURE
                );
            }
        }

        if (isset($p['hasil'])) {
            foreach ($p['hasil'] as &$row) {
                if (array_key_exists('ya', $row)) {
                    $row['ya'] = filter_var($row['ya'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                }
            }
            unset($row);
        }

        if (isset($p['tindakLanjut']['tidakPerluTL'])) {
            $p['tindakLanjut']['tidakPerluTL'] = (bool) $p['tindakLanjut']['tidakPerluTL'];
        }
    }

    /** ================== VIEW ================== */
    public function render()
    {
        return view('livewire.emr-r-i.edukasi-pasien-r-i.edukasi-pasien-r-i-terintegrasi');
    }
}
