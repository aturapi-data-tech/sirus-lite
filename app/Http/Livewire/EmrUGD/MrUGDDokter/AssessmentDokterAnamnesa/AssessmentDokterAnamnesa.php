<?php

namespace App\Http\Livewire\EmrUGD\MrUGDDokter\AssessmentDokterAnamnesa;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use App\Http\Traits\EmrUGD\EmrUGDTrait;



class AssessmentDokterAnamnesa extends Component
{
    use WithPagination, EmrUGDTrait;

    protected $listeners = ['emr:ugd:store' => 'store'];

    public $rjNoRef;
    public array $dataDaftarUgd = [];
    public array $rekonsiliasiObat = ["namaObat" => "", "dosis" => "", "rute" => ""];

    public array $anamnesa = [
        "pengkajianPerawatanTab" => "Pengkajian Perawatan",
        "pengkajianPerawatan" => [
            "perawatPenerima" => "",
            "perawatPenerimaCode" => "",
            "jamDatang" => "",
            "caraMasukIgd" => "",
            "caraMasukIgdDesc" => "",
            "caraMasukIgdOption" => [
                ["caraMasukIgd" => "Sendiri"],
                ["caraMasukIgd" => "Rujuk"],
                ["caraMasukIgd" => "Kasus Polisi"],
            ],
            "tingkatKegawatan" => "",
            "tingkatKegawatanOption" => [
                ["tingkatKegawatan" => "P1"],
                ["tingkatKegawatan" => "P2"],
                ["tingkatKegawatan" => "P3"],
                ["tingkatKegawatan" => "P0"],
            ],
            "saranaTransportasiId" => "4",
            "saranaTransportasiDesc" => "Lain-lain",
            "saranaTransportasiKet" => "",
            "saranaTransportasiOptions" => [
                ["saranaTransportasiId" => "1", "saranaTransportasiDesc" => "Ambulans"],
                ["saranaTransportasiId" => "2", "saranaTransportasiDesc" => "Mobil"],
                ["saranaTransportasiId" => "3", "saranaTransportasiDesc" => "Motor"],
                ["saranaTransportasiId" => "4", "saranaTransportasiDesc" => "Lain-lain"],
            ],
        ],

        "keluhanUtamaTab" => "Keluhan Utama",
        "keluhanUtama" => [
            "keluhanUtama" => ""
        ],

        "anamnesaDiperolehTab" => "Anamnesa Diperoleh",
        "anamnesaDiperoleh" => [
            "autoanamnesa" => [],
            "allonanamnesa" => [],
            "anamnesaDiperolehDari" => ""
        ],

        "riwayatPenyakitSekarangUmumTab" => "Riwayat Penyakit Sekarang (Umum)",
        "riwayatPenyakitSekarangUmum" => [
            "riwayatPenyakitSekarangUmum" => ""
        ],

        "riwayatPenyakitDahuluTab" => "Riwayat Penyakit (Dahulu)",
        "riwayatPenyakitDahulu" => [
            "riwayatPenyakitDahulu" => ""
        ],

        "alergiTab" => "Alergi",
        "alergi" => [
            "alergi" => ""
        ],

        "rekonsiliasiObatTab" => "Rekonsiliasi Obat",
        "rekonsiliasiObat" => [],

        "lainLainTab" => "Lain-Lain",
        "lainLain" => [
            "merokok" => [],
            "terpaparRokok" => []
        ],

        "faktorResikoTab" => "Faktor Resiko",
        "faktorResiko" => [
            "hipertensi" => [],
            "diabetesMelitus" => [],
            "penyakitJantung" => [],
            "asma" => [],
            "stroke" => [],
            "liver" => [],
            "tuberculosisParu" => [],
            "rokok" => [],
            "minumAlkohol" => [],
            "ginjal" => [],
            "lainLain" => ""
        ],

        "penyakitKeluargaTab" => "Riwayat Penyakit Keluarga",
        "penyakitKeluarga" => [
            "hipertensi" => [],
            "diabetesMelitus" => [],
            "penyakitJantung" => [],
            "asma" => [],
            "lainLain" => ""
        ],

        "statusFungsionalTab" => "Status Fungsional",
        "statusFungsional" => [
            "tongkat" => [],
            "kursiRoda" => [],
            "brankard" => [],
            "walker" => [],
            "lainLain" => ""
        ],

        "cacatTubuhTab" => "Cacat Tubuh",
        "cacatTubuh" => [
            "cacatTubuh" => [],
            "sebutCacatTubuh" => ""
        ],

        "statusPsikologisTab" => "Status Psikologis",
        "statusPsikologis" => [
            "tidakAdaKelainan" => [],
            "marah" => [],
            "cemas" => [],
            "takut" => [],
            "sedih" => [],
            "cenderungBunuhDiri" => [],
            "sebutstatusPsikologis" => ""
        ],

        "statusMentalTab" => "Status Mental",
        "statusMental" => [
            "statusMental" => "",
            "statusMentalOption" => [
                ["statusMental" => "Sadar dan Orientasi Baik"],
                ["statusMental" => "Ada Masalah Perilaku"],
                ["statusMental" => "Perilaku Kekerasan yang dialami sebelumnya"],
            ],
            "keteranganStatusMental" => "",
        ],

        "hubunganDgnKeluargaTab" => "Sosial",
        "hubunganDgnKeluarga" => [
            "hubunganDgnKeluarga" => "",
            "hubunganDgnKeluargaOption" => [
                ["hubunganDgnKeluarga" => "Baik"],
                ["hubunganDgnKeluarga" => "Tidak Baik"],
            ],
        ],

        "tempatTinggalTab" => "Tempat Tinggal",
        "tempatTinggal" => [
            "tempatTinggal" => "",
            "tempatTinggalOption" => [
                ["tempatTinggal" => "Rumah"],
                ["tempatTinggal" => "Panti"],
                ["tempatTinggal" => "Lain-lain"],
            ],
            "keteranganTempatTinggal" => ""
        ],

        "spiritualTab" => "Spiritual",
        "spiritual" => [
            "spiritual" => "Islam",
            "ibadahTeratur" => "",
            "ibadahTeraturOptions" => [
                ["ibadahTeratur" => "Ya"],
                ["ibadahTeratur" => "Tidak"],
            ],
            "nilaiKepercayaan" => "",
            "nilaiKepercayaanOptions" => [
                ["nilaiKepercayaan" => "Ya"],
                ["nilaiKepercayaan" => "Tidak"],
            ],
            "keteranganSpiritual" => ""
        ],

        "ekonomiTab" => "Ekonomi",
        "ekonomi" => [
            "pengambilKeputusan" => "Ayah",
            "pekerjaan" => "Swasta",
            "penghasilanBln" => "",
            "penghasilanBlnOptions" => [
                ["penghasilanBln" => "< 5Jt"],
                ["penghasilanBln" => "5Jt - 10Jt"],
                ["penghasilanBln" => ">10Jt"],
            ],
            "keteranganEkonomi" => ""
        ],

        "edukasiTab" => "Edukasi",
        "edukasi" => [
            "pasienKeluargaMenerimaInformasi" => "",
            "pasienKeluargaMenerimaInformasiOptions" => [
                ["pasienKeluargaMenerimaInformasi" => "Ya"],
                ["pasienKeluargaMenerimaInformasi" => "Tidak"],
            ],
            "hambatanEdukasi" => "",
            "keteranganHambatanEdukasi" => "",
            "hambatanEdukasiOptions" => [
                ["hambatanEdukasi" => "Ya"],
                ["hambatanEdukasi" => "Tidak"],
            ],
            "penerjemah" => "",
            "keteranganPenerjemah" => "",
            "penerjemahOptions" => [
                ["penerjemah" => "Ya"],
                ["penerjemah" => "Tidak"],
            ],
            "diagPenyakit" => [],
            "obat" => [],
            "dietNutrisi" => [],
            "rehabMedik" => [],
            "managemenNyeri" => [],
            "penggunaanAlatMedis" => [],
            "hakKewajibanPasien" => [],
            "edukasiFollowUp" => "",
            "segeraKembaliIGDjika" => "",
            "informedConsent" => "",
            "keteranganEdukasi" => ""
        ],

        "screeningGiziTab" => "Screening Gizi",
        "screeningGizi" => [
            "perubahanBB3Bln" => "",
            "perubahanBB3BlnScore" => "0",
            "perubahanBB3BlnOptions" => [
                ["perubahanBB3Bln" => "Ya (1)"],
                ["perubahanBB3Bln" => "Tidak (0)"],
            ],
            "jmlPerubahanBB" => "",
            "jmlPerubahanBBScore" => "0",
            "jmlPerubahanBBOptions" => [
                ["jmlPerubahanBB" => "0,5Kg-1Kg (1)"],
                ["jmlPerubahanBB" => ">5Kg-10Kg (2)"],
                ["jmlPerubahanBB" => ">10Kg-15Kg (3)"],
                ["jmlPerubahanBB" => ">15Kg-20Kg (4)"],
            ],
            "intakeMakanan" => "",
            "intakeMakananScore" => "0",
            "intakeMakananOptions" => [
                ["intakeMakanan" => "Ya (1)"],
                ["intakeMakanan" => "Tidak (0)"],
            ],
            "keteranganScreeningGizi" => "",
            "scoreTotalScreeningGizi" => "0",
            "tglScreeningGizi" => ""
        ],

        "batukTab" => "Batuk",
        "batuk" => [
            "riwayatDemam" => [],
            "keteranganRiwayatDemam" => "",
            "berkeringatMlmHari" => [],
            "keteranganBerkeringatMlmHari" => "",
            "bepergianDaerahWabah" => [],
            "keteranganBepergianDaerahWabah" => "",
            "riwayatPakaiObatJangkaPanjangan" => [],
            "keteranganRiwayatPakaiObatJangkaPanjangan" => "",
            "BBTurunTanpaSebab" => [],
            "keteranganBBTurunTanpaSebab" => "",
            "pembesaranGetahBening" => [],
            "keteranganPembesaranGetahBening" => "",
        ],
    ];

    protected $rules = [
        'dataDaftarUgd.anamnesa.pengkajianPerawatan.jamDatang' => 'required|date_format:d/m/Y H:i:s',
        'dataDaftarUgd.anamnesa.pengkajianPerawatan.caraMasukIgd' => 'required',
        'dataDaftarUgd.anamnesa.pengkajianPerawatan.tingkatKegawatan' => 'required',
        'dataDaftarUgd.anamnesa.keluhanUtama.keluhanUtama' => 'required',
    ];

    protected $messages = [
        'required' => ':attribute wajib diisi.',
        'date_format' => ':attribute harus dalam format dd/mm/yyyy HH:ii:ss',
    ];

    protected $validationAttributes = [
        'dataDaftarUgd.anamnesa.pengkajianPerawatan.jamDatang' => 'Jam Datang',
        'dataDaftarUgd.anamnesa.pengkajianPerawatan.caraMasukIgd' => 'Cara Masuk IGD',
        'dataDaftarUgd.anamnesa.pengkajianPerawatan.tingkatKegawatan' => 'Tingkat Kegawatan',
        'dataDaftarUgd.anamnesa.keluhanUtama.keluhanUtama' => 'Keluhan Utama',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function store(): void
    {
        $this->validate();

        $rjNo = $this->dataDaftarUgd['rjNo'] ?? $this->rjNoRef ?? null;
        if (!$rjNo) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Nomor UGD kosong.');
            return;
        }



        $lockKey = "ugd:{$rjNo}";
        try {
            Cache::lock($lockKey, 5)->block(3, function () use ($rjNo) {
                DB::transaction(function () use ($rjNo) {
                    $fresh = $this->findDataUGD($rjNo) ?: [];

                    if (!isset($fresh['anamnesa']) || !is_array($fresh['anamnesa'])) {
                        $fresh['anamnesa'] = $this->anamnesa;
                    }

                    // Merge data anamnesa
                    $fresh['anamnesa'] = array_merge(
                        $fresh['anamnesa'],
                        $this->dataDaftarUgd['anamnesa'] ?? []
                    );

                    // Update header fields
                    [$p_status, $waktu_pasien_datang, $waktu_pasien_dilayani] = $this->deriveHeaderFieldsFrom($fresh);

                    DB::table('rstxn_ugdhdrs')
                        ->where('rj_no', $rjNo)
                        ->update([
                            'p_status'              => $p_status,
                            'waktu_pasien_datang'   => DB::raw("to_date('{$waktu_pasien_datang}','dd/mm/yyyy hh24:mi:ss')"),
                            'waktu_pasien_dilayani' => DB::raw("to_date('{$waktu_pasien_dilayani}','dd/mm/yyyy hh24:mi:ss')"),
                        ]);

                    $this->updateJsonUGD($rjNo, $fresh);
                    $this->dataDaftarUgd = $fresh;
                });
            });

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addSuccess('Anamnesa berhasil disimpan.');
        } catch (LockTimeoutException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Sistem sibuk, gagal memperoleh lock. Coba lagi.');
        } catch (\Exception $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Gagal menyimpan anamnesa: ' . $e->getMessage());
        }
    }

    private function deriveHeaderFieldsFrom(array $state): array
    {
        $now = Carbon::now(config('app.timezone'))->format('d/m/Y H:i:s');

        $p_status = $state['anamnesa']['pengkajianPerawatan']['tingkatKegawatan'] ?? 'P0';
        $p_status = $p_status ?: 'P0';

        $waktu_pasien_datang = $state['anamnesa']['pengkajianPerawatan']['jamDatang'] ?? '';
        $waktu_pasien_datang = $waktu_pasien_datang ?: $now;

        $waktu_pasien_dilayani = $state['perencanaan']['pengkajianMedis']['waktuPemeriksaan'] ?? '';
        $waktu_pasien_dilayani = $waktu_pasien_dilayani ?: $now;

        return [$p_status, $waktu_pasien_datang, $waktu_pasien_dilayani];
    }

    public function addRekonsiliasiObat(): void
    {
        if (empty($this->rekonsiliasiObat['namaObat'])) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Nama obat tidak boleh kosong.');
            return;
        }

        $cekRekonsiliasiObat = collect($this->dataDaftarUgd['anamnesa']['rekonsiliasiObat'])
            ->where("namaObat", $this->rekonsiliasiObat['namaObat'])
            ->count();

        if ($cekRekonsiliasiObat > 0) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Nama obat sudah ada dalam daftar.');
            return;
        }

        $this->dataDaftarUgd['anamnesa']['rekonsiliasiObat'][] = [
            "namaObat" => $this->rekonsiliasiObat['namaObat'],
            "dosis" => $this->rekonsiliasiObat['dosis'],
            "rute" => $this->rekonsiliasiObat['rute']
        ];

        $this->reset(['rekonsiliasiObat']);
        $this->store();
    }

    public function removeRekonsiliasiObat($index): void
    {
        if (isset($this->dataDaftarUgd['anamnesa']['rekonsiliasiObat'][$index])) {
            unset($this->dataDaftarUgd['anamnesa']['rekonsiliasiObat'][$index]);
            $this->dataDaftarUgd['anamnesa']['rekonsiliasiObat'] = array_values($this->dataDaftarUgd['anamnesa']['rekonsiliasiObat']);
            $this->store();
        }
    }

    public function setJamDatang($myTime): void
    {
        $this->dataDaftarUgd['anamnesa']['pengkajianPerawatan']['jamDatang'] = $myTime;
        $this->store();
    }

    public function setAutoJamDatang(): void
    {
        $this->dataDaftarUgd['anamnesa']['pengkajianPerawatan']['jamDatang'] = now()->format('d/m/Y H:i:s');
        $this->store();
    }

    private function storePengkajianPerawatan(): void
    {
        $rjNo = $this->dataDaftarUgd['rjNo'] ?? $this->rjNoRef ?? null;
        if (!$rjNo) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Nomor UGD kosong.');
            return;
        }

        $lockKey = "ugd:{$rjNo}";

        try {
            Cache::lock($lockKey, 5)->block(3, function () use ($rjNo) {
                DB::transaction(function () use ($rjNo) {
                    // Ambil data paling FRESH
                    $fresh = $this->findDataUGD($rjNo) ?: [];

                    // Ambil subtree aman (tanpa overwrite)
                    $anamnesa = (array) data_get($fresh, 'anamnesa', []);
                    $pp       = (array) data_get($anamnesa, 'pengkajianPerawatan', []);

                    // Patch HANYA field perawat penerima (+ code)
                    $pp['perawatPenerima']     = $this->dataDaftarUgd['anamnesa']['pengkajianPerawatan']['perawatPenerima']     ?? ($pp['perawatPenerima']     ?? '');
                    $pp['perawatPenerimaCode'] = $this->dataDaftarUgd['anamnesa']['pengkajianPerawatan']['perawatPenerimaCode'] ?? ($pp['perawatPenerimaCode'] ?? '');

                    // Kembalikan ke tree
                    $anamnesa['pengkajianPerawatan'] = $pp;
                    $fresh['anamnesa'] = $anamnesa;

                    // Commit ke JSON
                    $this->updateJsonUGD($rjNo, $fresh);

                    // Sinkronkan state lokal supaya UI up-to-date
                    $this->dataDaftarUgd = $fresh;
                });
            });

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addSuccess('Perawat penerima berhasil diset.');
        } catch (LockTimeoutException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Sistem sibuk, gagal memperoleh lock. Coba lagi.');
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Gagal menyimpan perawat penerima.');
        }
    }


    public function setPerawatPenerima(): void
    {
        $user = auth()->user();
        $myUserNameActive = $user?->myuser_name ?? 'Petugas';
        $myUserCodeActive = $user?->myuser_code ?? '';

        // Autorisasi ringkas
        if (!$user?->hasAnyRole(['Perawat', 'Dokter', 'Admin'])) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Anda tidak memiliki wewenang untuk menandatangani anamnesa.');
            return;
        }

        // Set ke state lokal
        $this->dataDaftarUgd['anamnesa']['pengkajianPerawatan']['perawatPenerima'] = $myUserNameActive;
        $this->dataDaftarUgd['anamnesa']['pengkajianPerawatan']['perawatPenerimaCode'] = $myUserCodeActive;

        // Simpan HANYA subtree pengkajianPerawatan (aman, tidak overwrite node lain)
        $this->storePengkajianPerawatan();
    }


    public function calculateScreeningGizi(): void
    {
        $screening = $this->dataDaftarUgd['anamnesa']['screeningGizi'] ?? [];
        $totalScore = 0;

        if (isset($screening['perubahanBB3BlnScore'])) {
            $totalScore += (int)$screening['perubahanBB3BlnScore'];
        }
        if (isset($screening['jmlPerubahanBBScore'])) {
            $totalScore += (int)$screening['jmlPerubahanBBScore'];
        }
        if (isset($screening['intakeMakananScore'])) {
            $totalScore += (int)$screening['intakeMakananScore'];
        }

        $this->dataDaftarUgd['anamnesa']['screeningGizi']['scoreTotalScreeningGizi'] = (string)$totalScore;
        $this->dataDaftarUgd['anamnesa']['screeningGizi']['tglScreeningGizi'] = now()->format('d/m/Y H:i:s');
    }

    public function toggleCheckbox($tab, $field): void
    {
        if (isset($this->dataDaftarUgd['anamnesa'][$tab][$field])) {
            $this->dataDaftarUgd['anamnesa'][$tab][$field] =
                $this->dataDaftarUgd['anamnesa'][$tab][$field] ? [] : [1];
        }
    }

    private function findData($rjno): void
    {
        $this->dataDaftarUgd = $this->findDataUGD($rjno) ?: [];

        if (!isset($this->dataDaftarUgd['anamnesa']) || !is_array($this->dataDaftarUgd['anamnesa'])) {
            $this->dataDaftarUgd['anamnesa'] = $this->anamnesa;
        }

        $this->matchingMyVariable();
    }

    private function matchingMyVariable(): void
    {
        // Sync keluhan utama dari screening jika kosong
        if (
            empty($this->dataDaftarUgd['anamnesa']['keluhanUtama']['keluhanUtama']) &&
            isset($this->dataDaftarUgd['screening']['keluhanUtama']) &&
            !empty($this->dataDaftarUgd['screening']['keluhanUtama'])
        ) {
            $this->dataDaftarUgd['anamnesa']['keluhanUtama']['keluhanUtama'] = $this->dataDaftarUgd['screening']['keluhanUtama'];
        }
    }

    public function resetForm(): void
    {
        $this->resetValidation();
        $this->dataDaftarUgd['anamnesa'] = $this->anamnesa;
        $this->reset(['rekonsiliasiObat']);
    }

    public function mount(): void
    {
        $this->findData($this->rjNoRef);
    }

    public function render()
    {

        return view(
            'livewire.emr-u-g-d.mr-u-g-d-dokter.assessment-dokter-anamnesa.assessment-dokter-anamnesa',
            [
                'myTitle' => 'Anamnesa',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Pasien Rawat Jalan',
            ]
        );
    }
}
