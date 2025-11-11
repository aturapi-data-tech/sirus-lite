<?php

namespace App\Http\Livewire\EmrUGD\MrUGDDokter\AssessmentDokterPerencanaan;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Traits\EmrUGD\EmrUGDTrait;

use Carbon\Carbon;

class AssessmentDokterPerencanaan extends Component
{
    use WithPagination, EmrUGDTrait;

    // listener from blade////////////////
    protected $listeners = ['emr:ugd:store' => 'store'];

    //////////////////////////////
    // Ref on top bar
    //////////////////////////////
    public $rjNoRef;

    // dataDaftarUgd RJ
    public array $dataDaftarUgd = [];

    public array $perencanaan =
    [
        "terapiTab" => "Terapi",
        "terapi" => [
            "terapi" => ""
        ],

        "tindakLanjutTab" => "Tindak Lanjut",
        "tindakLanjut" => [
            "tindakLanjut" => "",
            "keteranganTindakLanjut" => "",
            "tindakLanjutOptions" => [
                ["tindakLanjut" => "MRS"],
                ["tindakLanjut" => "KRS"],
                ["tindakLanjut" => "APS"],
                ["tindakLanjut" => "Rujuk"],
                ["tindakLanjut" => "Meninggal"],
                ["tindakLanjut" => "Lain-lain"],
            ],

        ],

        "pengkajianMedisTab" => "Petugas Medis",
        "pengkajianMedis" => [
            "waktuPemeriksaan" => "",
            "selesaiPemeriksaan" => "",
            "drPemeriksa" => "",
        ],
        // Kontrol pakai program lama

        "rawatInapTab" => "Rawat Inap",
        "rawatInap" => [
            "noRef" => "",
            "tanggal" => "", //dd/mm/yyyy
            "keterangan" => "",
        ],

        "dischargePlanningTab" => "Discharge Planning",
        "dischargePlanning" => [
            "pelayananBerkelanjutan" => [
                "pelayananBerkelanjutan" => "Tidak Ada",
                "pelayananBerkelanjutanOption" => [
                    ["pelayananBerkelanjutan" => "Tidak Ada"],
                    ["pelayananBerkelanjutan" => "Ada"],
                ],
            ],
            "pelayananBerkelanjutanOpsi" => [
                "rawatLuka" => [],
                "dm" => [],
                "ppok" => [],
                "hivAids" => [],
                "dmTerapiInsulin" => [],
                "ckd" => [],
                "tb" => [],
                "stroke" => [],
                "kemoterapi" => [],
            ],

            "penggunaanAlatBantu" => [
                "penggunaanAlatBantu" => "Tidak Ada",
                "penggunaanAlatBantuOption" => [
                    ["penggunaanAlatBantu" => "Tidak Ada"],
                    ["penggunaanAlatBantu" => "Ada"],
                ],
            ],
            "penggunaanAlatBantuOpsi" => [
                "kateterUrin" => [],
                "ngt" => [],
                "traechotomy" => [],
                "colostomy" => [],
            ],
        ]
    ];
    //////////////////////////////////////////////////////////////////////


    protected $rules = [
        'dataDaftarUgd.perencanaan.pengkajianMedis.waktuPemeriksaan' => 'required|date_format:d/m/Y H:i:s',
        'dataDaftarUgd.perencanaan.pengkajianMedis.selesaiPemeriksaan' => 'required|date_format:d/m/Y H:i:s',
    ];

    protected $messages = [
        'dataDaftarUgd.perencanaan.pengkajianMedis.waktuPemeriksaan.required' => 'Waktu pemeriksaan wajib diisi.',
        'dataDaftarUgd.perencanaan.pengkajianMedis.waktuPemeriksaan.date_format' => 'Format waktu pemeriksaan harus dd/mm/yyyy hh:mm:ss.',
        'dataDaftarUgd.perencanaan.pengkajianMedis.selesaiPemeriksaan.required' => 'Selesai pemeriksaan wajib diisi.',
        'dataDaftarUgd.perencanaan.pengkajianMedis.selesaiPemeriksaan.date_format' => 'Format selesai pemeriksaan harus dd/mm/yyyy hh:mm:ss.',
    ];

    protected $attributes = [
        'dataDaftarUgd.perencanaan.pengkajianMedis.waktuPemeriksaan' => 'waktu pemeriksaan',
        'dataDaftarUgd.perencanaan.pengkajianMedis.selesaiPemeriksaan' => 'selesai pemeriksaan',
    ];



    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }



    // resert input private////////////////
    private function resetInputFields(): void
    {

        // resert validation
        $this->resetValidation();
        // resert input kecuali
        $this->resetExcept([
            'rjNoRef'
        ]);
    }





    // ////////////////
    // RJ Logic
    // ////////////////


    // validate Data RJ//////////////////////////////////////////////////
    private function validateDataUgd(): void
    {
        $rules = [
            'dataDaftarUgd.perencanaan.pengkajianMedis.waktuPemeriksaan'  => 'required|date_format:d/m/Y H:i:s',
            'dataDaftarUgd.perencanaan.pengkajianMedis.selesaiPemeriksaan' => 'required|date_format:d/m/Y H:i:s',
        ];

        $attributes = [
            'dataDaftarUgd.perencanaan.pengkajianMedis.waktuPemeriksaan'   => 'Waktu Pemeriksaan',
            'dataDaftarUgd.perencanaan.pengkajianMedis.selesaiPemeriksaan' => 'Selesai Pemeriksaan',
        ];

        $messages = [
            'required'    => ':attribute wajib diisi.',
            'date_format' => ':attribute tidak sesuai format (dd/mm/YYYY HH:mm:ss).',
        ];

        // Proses Validasi///////////////////////////////////////////
        try {
            $this->validate($rules, $messages, $attributes);
        } catch (ValidationException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError("Lakukan pengecekan kembali input data.");
            // kalau mau, bisa return; supaya gak validate 2x
            return;
        }
    }


    // insert and update record start////////////////
    public function store()
    {
        $this->validateDataUgd();

        $rjNo = $this->dataDaftarUgd['rjNo'] ?? $this->rjNoRef ?? null;
        if (!$rjNo) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("rjNo kosong.");
            return;
        }

        $lockKey = "ugd:{$rjNo}";

        try {
            Cache::lock($lockKey, 5)->block(3, function () use ($rjNo) {
                // 1) Ambil FRESH dari DB di dalam lock
                $fresh = $this->findDataUGD($rjNo) ?: [];

                // 2) Pastikan subtree ada lalu PATCH hanya 'perencanaan'
                if (!isset($fresh['perencanaan']) || !is_array($fresh['perencanaan'])) {
                    $fresh['perencanaan'] = $this->perencanaan;
                }
                $fresh['perencanaan'] = (array) ($this->dataDaftarUgd['perencanaan'] ?? $this->perencanaan);

                // 3) Hitung header fields (p_status & waktu) aman
                $p_status = 'P0';
                if (!empty($fresh['anamnesa']['pengkajianPerawatan']['tingkatKegawatan'])) {
                    $p_status = $fresh['anamnesa']['pengkajianPerawatan']['tingkatKegawatan'];
                }

                $waktu_pasien_datang =
                    $fresh['anamnesa']['pengkajianPerawatan']['jamDatang']
                    ?? Carbon::now(config('app.timezone'))->format('d/m/Y H:i:s');

                $waktu_pasien_dilayani =
                    $fresh['perencanaan']['pengkajianMedis']['waktuPemeriksaan']
                    ?? Carbon::now(config('app.timezone'))->format('d/m/Y H:i:s');

                // 4) Commit atomik
                DB::transaction(function () use ($rjNo, $fresh, $p_status, $waktu_pasien_datang, $waktu_pasien_dilayani) {
                    // kunci header
                    DB::table('rstxn_ugdhdrs')->where('rj_no', $rjNo)->first();

                    DB::table('rstxn_ugdhdrs')
                        ->where('rj_no', $rjNo)
                        ->update([
                            'p_status'             => $p_status,
                            'waktu_pasien_datang'  => DB::raw("to_date('{$waktu_pasien_datang}','dd/mm/yyyy hh24:mi:ss')"),
                            'waktu_pasien_dilayani' => DB::raw("to_date('{$waktu_pasien_dilayani}','dd/mm/yyyy hh24:mi:ss')"),
                        ]);

                    // simpan JSON besar (safe)
                    $this->updateJsonUGD($rjNo, $fresh);
                });

                // 5) sinkronkan state lokal
                $this->dataDaftarUgd = $fresh;
            });
        } catch (LockTimeoutException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Sistem sibuk, gagal memperoleh lock. Coba lagi.');
            return;
        }

        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
            ->addSuccess("Perencanaan berhasil disimpan.");
    }



    // insert and update record end////////////////


    private function findData($rjno): void
    {
        $this->dataDaftarUgd = $this->findDataUGD($rjno);
        if (!isset($this->dataDaftarUgd['perencanaan'])) {
            $this->dataDaftarUgd['perencanaan'] = $this->perencanaan;
        }
    }


    public function setWaktuPemeriksaan($myTime)
    {
        $this->dataDaftarUgd['perencanaan']['pengkajianMedis']['waktuPemeriksaan'] = $myTime;
    }

    public function setSelesaiPemeriksaan($myTime)
    {
        $this->dataDaftarUgd['perencanaan']['pengkajianMedis']['selesaiPemeriksaan'] = $myTime;
    }

    private function validateDrPemeriksa(): bool
    {
        $rules = [
            // vital
            'dataDaftarUgd.pemeriksaan.tandaVital.frekuensiNadi'   => 'required|numeric',
            'dataDaftarUgd.pemeriksaan.tandaVital.frekuensiNafas'  => 'required|numeric',
            'dataDaftarUgd.pemeriksaan.tandaVital.suhu'            => 'required|numeric',
            'dataDaftarUgd.pemeriksaan.tandaVital.spo2'            => 'nullable|numeric',
            'dataDaftarUgd.pemeriksaan.tandaVital.gda'             => 'nullable|numeric',

            // nutrisi
            'dataDaftarUgd.pemeriksaan.nutrisi.bb'                 => 'required|numeric',
            'dataDaftarUgd.pemeriksaan.nutrisi.tb'                 => 'required|numeric',
            'dataDaftarUgd.pemeriksaan.nutrisi.imt'                => 'required|numeric',
            'dataDaftarUgd.pemeriksaan.nutrisi.lk'                 => 'nullable|numeric',
            'dataDaftarUgd.pemeriksaan.nutrisi.lila'               => 'nullable|numeric',

            // waktu
            'dataDaftarUgd.anamnesa.pengkajianPerawatan.jamDatang' => 'required|date_format:d/m/Y H:i:s',
        ];

        $attributes = [
            'dataDaftarUgd.pemeriksaan.tandaVital.frekuensiNadi'   => 'Frekuensi Nadi',
            'dataDaftarUgd.pemeriksaan.tandaVital.frekuensiNafas'  => 'Frekuensi Nafas',
            'dataDaftarUgd.pemeriksaan.tandaVital.suhu'            => 'Suhu',
            'dataDaftarUgd.pemeriksaan.tandaVital.spo2'            => 'SpO₂',
            'dataDaftarUgd.pemeriksaan.tandaVital.gda'             => 'GDA',

            'dataDaftarUgd.pemeriksaan.nutrisi.bb'                 => 'Berat Badan',
            'dataDaftarUgd.pemeriksaan.nutrisi.tb'                 => 'Tinggi Badan',
            'dataDaftarUgd.pemeriksaan.nutrisi.imt'                => 'IMT',
            'dataDaftarUgd.pemeriksaan.nutrisi.lk'                 => 'Lingkar Kepala',
            'dataDaftarUgd.pemeriksaan.nutrisi.lila'               => 'Lingkar Lengan Atas',

            'dataDaftarUgd.anamnesa.pengkajianPerawatan.jamDatang' => 'Jam Datang Pasien',
        ];

        $messages = [
            '*.required' => ':attribute wajib diisi.',
            '*.numeric'  => ':attribute harus berupa angka.',
            '*.date_format' => ':attribute tidak sesuai format (dd/mm/YYYY HH:mm:ss).',
        ];

        try {
            $this->validate($rules, $messages, $attributes);
            return true; // validasi sukses
        } catch (ValidationException $e) {
            // Ambil semua pesan error dan tampilkan via toastr
            $errors = $e->validator->errors()->all();
            foreach ($errors as $msg) {
                toastr()->closeOnHover(true)
                    ->closeDuration(4)
                    ->positionClass('toast-top-left')
                    ->addError($msg);
            }

            // Pesan ringkasan
            toastr()->closeOnHover(true)
                ->closeDuration(5)
                ->positionClass('toast-top-left')
                ->addWarning('Validasi gagal. Periksa kembali data pemeriksaan pasien.');

            return false;
        }
    }

    public function setDrPemeriksa(): void
    {
        // 1) Validasi pendukung (vital, nutrisi, jam datang, dst)
        // Validasi data pemeriksaan
        if (!$this->validateDrPemeriksa()) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Validasi data pemeriksaan gagal. Lengkapi data vital, nutrisi, atau jam datang pasien terlebih dahulu.');
            return;
        }

        $userCode = auth()->user()->myuser_code ?? null;
        $userName = auth()->user()->myuser_name ?? 'User';

        // 2) Wajib role Dokter atau Admin
        if (!auth()->user()->hasAnyRole(['Dokter', 'Admin'])) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError("Anda tidak dapat melakukan TTD-E karena User Role {$userName} bukan Dokter/Admin");
            return;
        }

        // 3) Cek kepemilikan pasien (tetap dipertahankan seperti semula)
        $drIdPasien = data_get($this->dataDaftarUgd, 'drId');
        if ($drIdPasien !== $userCode) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError("Anda tidak dapat melakukan TTD-E karena bukan pasien {$userName}");
            return;
        }

        $this->dataDaftarUgd['perencanaan']['pengkajianMedisTab'] = 'Pengkajian Medis';
        $this->dataDaftarUgd['perencanaan']['pengkajianMedis']['drPemeriksa'] = $userName;

        // 4) Simpan HANYA subtree pengkajianMedis agar terapi tidak tersentuh
        $this->storePengkajianMedis();
    }

    private function storePengkajianMedis(): void
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
                    $fresh = $this->findDataUGD($rjNo) ?: [];

                    // Pastikan node ada TANPA menimpa subtree lain
                    if (!isset($fresh['perencanaan']) || !is_array($fresh['perencanaan'])) {
                        $fresh['perencanaan'] = [];
                    }
                    $fresh['perencanaan'] = $this->dataDaftarUgd['perencanaan'];

                    // Patch HANYA pengkajianMedis (+ optional tab label)
                    $fresh['perencanaan']['pengkajianMedisTab'] =
                        $this->dataDaftarUgd['perencanaan']['pengkajianMedisTab'] ?? 'Pengkajian Medis';

                    $fresh['perencanaan']['pengkajianMedis'] = (array) (
                        $this->dataDaftarUgd['perencanaan']['pengkajianMedis'] ?? []
                    );

                    // JANGAN sentuh $fresh['perencanaan']['terapi'] di sini
                    $this->updateJsonUGD($rjNo, $fresh);

                    // Sinkronkan state lokal
                    $this->dataDaftarUgd = $fresh;
                });
            });

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addSuccess('Pengkajian medis disimpan.');
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Gagal menyimpan pengkajian medis.');
        }
    }



    // /////////////////eresep open////////////////////////
    public bool $isOpenEresepUGD = false;
    public string $isOpenModeEresepUGD = 'insert';

    public function openModalEresepUGD(): void
    {
        $this->isOpenEresepUGD = true;
        $this->isOpenModeEresepUGD = 'insert';
    }

    public function closeModalEresepUGD(): void
    {
        $this->isOpenEresepUGD = false;
        $this->isOpenModeEresepUGD = 'insert';
    }

    public string $activeTabRacikanNonRacikan = 'NonRacikan';
    public array $EmrMenuRacikanNonRacikan = [
        [
            'ermMenuId' => 'NonRacikan',
            'ermMenuName' => 'NonRacikan',
        ],
        [
            'ermMenuId' => 'Racikan',
            'ermMenuName' => 'Racikan',
        ],
    ];

    public function simpanTerapi(): void
    {
        // Pastikan status pasien masih aktif

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
                    // Ambil FRESH dari DB supaya tidak menimpa subtree lain
                    $fresh = $this->findDataUGD($rjNo) ?: [];

                    // Build teks terapi dari data FRESH (bukan dari state lokal)
                    $eresepStr   = $this->formatEresep($fresh['eresep'] ?? []);
                    $racikanStr  = $this->formatEresepRacikan($fresh['eresepRacikan'] ?? []);
                    $terapiTeks  = trim($eresepStr . $racikanStr);

                    // Pastikan node perencanaan/terapi ada
                    if (!isset($fresh['perencanaan']))            $fresh['perencanaan'] = [];
                    if (!isset($fresh['perencanaan']['terapi']))  $fresh['perencanaan']['terapi'] = [];

                    // PATCH hanya field terapi
                    $fresh['perencanaan']['terapi']['terapi'] = $terapiTeks === '' ? PHP_EOL : $terapiTeks;

                    // Commit JSON
                    $this->updateJsonUGD($rjNo, $fresh);

                    // Sinkronkan state lokal agar UI up-to-date
                    $this->dataDaftarUgd = $fresh;
                });
            });

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addSuccess('Terapi berhasil disimpan.');
            $this->closeModalEresepUGD();
        } catch (LockTimeoutException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Sistem sibuk, gagal memperoleh lock. Coba lagi.');
            return;
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Gagal menyimpan terapi.');
            return;
        }
    }

    /** ===== Helpers ===== */

    private function formatEresep(array $items): string
    {
        if (empty($items)) return '';
        $lines = [];

        foreach ($items as $v) {
            $nama   = $v['productName']   ?? '';
            $qty    = $v['qty']           ?? '';
            $sx     = $v['signaX']        ?? '';
            $sh     = $v['signaHari']     ?? '';
            $cat    = isset($v['catatanKhusus']) && $v['catatanKhusus'] !== ''
                ? ' (' . $v['catatanKhusus'] . ')' : '';

            // contoh: R/ Paracetamol | No. 10 | S 3dd1 (habis makan)
            $lines[] = 'R/ ' . $nama . ' | No. ' . $qty . ' | S ' . $sx . 'dd' . $sh . $cat;
        }

        return implode(PHP_EOL, $lines) . PHP_EOL;
    }

    private function formatEresepRacikan(array $items): string
    {
        if (empty($items)) return '';
        $lines = [];

        foreach ($items as $v) {
            $noR   = $v['noRacikan']  ?? 'R?';
            $nama  = $v['productName'] ?? '';
            $dosis = isset($v['dosis']) && $v['dosis'] !== '' ? $v['dosis'] : '';
            $qty   = $v['qty'] ?? '';
            $ctt   = $v['catatan'] ?? '';
            $sig   = $v['catatanKhusus'] ?? ''; // di data kamu ini digunakan untuk instruksi S

            // header racikan: R1/ Amoxicillin - 125mg/5ml
            $header = rtrim($noR . '/ ' . $nama . ($dosis !== '' ? ' - ' . $dosis : ''), ' -');

            // detail: Jml Racikan 10 | Larutkan dahulu | S 3x sehari
            $detailParts = [];
            if ($qty !== '')  $detailParts[] = 'Jml Racikan ' . $qty;
            if ($ctt !== '')  $detailParts[] = $ctt;
            if ($sig !== '')  $detailParts[] = 'S ' . $sig;

            $detail = empty($detailParts) ? '' : implode(' | ', $detailParts);

            $lines[] = $header . PHP_EOL . ($detail !== '' ? $detail . PHP_EOL : '');
        }

        return implode('', $lines); // sudah mengandung \n di dalamnya
    }

    // /////////////////////////////////////////


    private function rebuildWithDefaults(array $defaults, $data)
    {
        // Kalau bukan array, paksa jadi array kosong agar bisa diisi default
        if (!is_array($data)) {
            $data = [];
        }

        // Mulai dari defaults
        $result = $defaults;

        foreach ($data as $key => $value) {
            if (array_key_exists($key, $defaults)) {
                // Jika default di level ini array, rekursif
                if (is_array($defaults[$key])) {
                    $result[$key] = $this->rebuildWithDefaults($defaults[$key], $value);
                } else {
                    // Kalau default adalah skalar, pakai nilai user
                    $result[$key] = $value;
                }
            } else {
                // Key ekstra yang tidak ada di default: tetapkan apa adanya (tidak dihapus)
                $result[$key] = $value;
            }
        }

        return $result;
    }


    // when new form instance
    public function mount()
    {

        $this->dataDaftarUgd = $this->findDataUGD($this->rjNoRef) ?: [];

        $current = (array) data_get($this->dataDaftarUgd, 'perencanaan', []);
        // Lengkapi struktur perencanaan sesuai default, tanpa menghapus data lain
        $this->dataDaftarUgd['perencanaan'] = $this->rebuildWithDefaults($this->perencanaan, $current);
    }



    // select data start////////////////
    public function render()
    {

        return view(
            'livewire.emr-u-g-d.mr-u-g-d-dokter.assessment-dokter-perencanaan.assessment-dokter-perencanaan',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Perencanaan',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Pasien Rawat Jalan',
            ]
        );
    }
    // select data end////////////////


}
