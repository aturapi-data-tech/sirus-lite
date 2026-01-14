<?php

namespace App\Http\Livewire\EmrUGD\TrfPasienUGD;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Carbon\Carbon;
use App\Http\Traits\LOV\LOVDokter\LOVDokterTrait;

use App\Http\Traits\EmrUGD\EmrUGDTrait;

use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use App\Http\Traits\MasterPasien\MasterPasienTrait;

class TrfPasienUGD extends Component
{
    use  EmrUGDTrait, LOVDokterTrait, MasterPasienTrait;


    //////////////////////////////
    // Ref on top bar
    //////////////////////////////

    public $rjNoRef;
    public $regNoRef;

    public array $dataDaftarUgd = [];

    public array $dokter = [];

    public array $alat = [
        'jenis'      => '',
        'lokasi'     => '',
        'ukuran'     => '',
        'keterangan' => '',
    ];

    private function syncLOV(): void
    {
        // ambil hasil LOV Dokter (misal dari modal / pencarian)
        $this->dokter = $this->collectingMyDokter;
    }
    private function syncDataFormEntry(): void
    {
        // mapping hasil LOV -> form levelingDokter TRF UGD
        $this->levelingDokter['drId']     = $this->dokter['DokterId']   ?? '';
        $this->levelingDokter['drName']   = $this->dokter['DokterDesc'] ?? '';
        $this->levelingDokter['poliId']   = $this->dokter['PoliId']     ?? '';
        $this->levelingDokter['poliDesc'] = $this->dokter['PoliDesc']   ?? '';

        // kalau mau simpan kode BPJS (opsional):
        $this->levelingDokter['kdpolibpjs'] = $this->dokter['kdPoliBpjs']   ?? '';
        $this->levelingDokter['kddrbpjs']   = $this->dokter['kdDokterBpjs'] ?? '';
    }

    // =============================
    // TRF UGD
    // =============================
    public array $levelingDokter = [
        'drId'        => '',
        'drName'      => '',
        'poliId'      => '',
        'poliDesc'    => '',
        'tglEntry'    => '',
        'levelDokter' => 'Utama',
    ];




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

                    $fresh = $this->findDataUGD($rjNo) ?: [];

                    if (!isset($fresh['trfUgd']) || !is_array($fresh['trfUgd'])) {
                        $fresh['trfUgd'] = $this->dataDaftarUgd['trfUgd'] ?? [];
                    }

                    // patch trfUgd
                    $fresh['trfUgd'] = array_replace(
                        $fresh['trfUgd'],
                        (array)($this->dataDaftarUgd['trfUgd'] ?? [])
                    );

                    $this->updateJsonUGD($rjNo, $fresh);

                    $this->dataDaftarUgd = $fresh;
                });
            });

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addSuccess("Data TRF UGD berhasil disimpan.");
        } catch (LockTimeoutException $e) {

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Sistem sibuk, gagal memperoleh lock.');
        } catch (\Throwable $e) {

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Gagal menyimpan data TRF UGD.');
        }
    }


    // =============================
    // LEVELING DOKTER UGD
    // =============================
    private function validateDataLevelingDokterUgd(): void
    {
        $rules = [
            'levelingDokter.drId'        => 'required|string|max:10',
            'levelingDokter.drName'      => 'required|string|max:200',
            'levelingDokter.poliId'      => 'required|string|max:10',
            'levelingDokter.poliDesc'    => 'required|string|max:50',
            'levelingDokter.tglEntry'    => 'required|date_format:d/m/Y H:i:s',
            'levelingDokter.levelDokter' => 'required|in:Utama,RawatGabung',
        ];

        $messages = [
            'levelingDokter.drId.required'        => 'ID Dokter pada form Entry Leveling Dokter harus diisi.',
            'levelingDokter.drId.string'          => 'ID Dokter pada form Entry Leveling Dokter harus berupa string.',
            'levelingDokter.drId.max'             => 'ID Dokter pada form Entry Leveling Dokter maksimal 10 karakter.',
            'levelingDokter.drName.required'      => 'Nama Dokter pada form Entry Leveling Dokter harus diisi.',
            'levelingDokter.drName.string'        => 'Nama Dokter pada form Entry Leveling Dokter harus berupa string.',
            'levelingDokter.drName.max'           => 'Nama Dokter pada form Entry Leveling Dokter maksimal 200 karakter.',
            'levelingDokter.poliId.required'      => 'ID Poli pada form Entry Leveling Dokter harus diisi.',
            'levelingDokter.poliId.string'        => 'ID Poli pada form Entry Leveling Dokter harus berupa string.',
            'levelingDokter.poliId.max'           => 'ID Poli pada form Entry Leveling Dokter maksimal 10 karakter.',
            'levelingDokter.poliDesc.required'    => 'Nama Poli pada form Entry Leveling Dokter harus diisi.',
            'levelingDokter.poliDesc.string'      => 'Nama Poli pada form Entry Leveling Dokter harus berupa string.',
            'levelingDokter.poliDesc.max'         => 'Nama Poli pada form Entry Leveling Dokter maksimal 50 karakter.',
            'levelingDokter.tglEntry.required'    => 'Tanggal Entry pada form Entry Leveling Dokter harus diisi.',
            'levelingDokter.tglEntry.date_format' => 'Format Tanggal Entry tidak valid. Gunakan format: d/m/Y H:i:s.',
            'levelingDokter.levelDokter.required' => 'Level Dokter harus diisi.',
            'levelingDokter.levelDokter.in'       => 'Level Dokter hanya boleh "Utama" atau "RawatGabung".',
        ];

        try {
            $this->validate($rules, $messages);
        } catch (\Illuminate\Validation\ValidationException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError("Lakukan Pengecekan kembali Input Data." . $e->getMessage());
            $this->validate($rules, $messages);
        }
    }

    public function addLevelingDokter()
    {
        $this->levelingDokter['tglEntry'] = Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s');

        // validasi data entry
        $this->validateDataLevelingDokterUgd();

        // check exist by tglEntry
        $cekdLevelingDokter = collect($this->dataDaftarUgd['trfUgd']['levelingDokter'] ?? [])
            ->where('tglEntry', '=', $this->levelingDokter['tglEntry'])
            ->count();

        if (!$cekdLevelingDokter) {
            $this->dataDaftarUgd['trfUgd']['levelingDokter'][] = [
                "drId"        => $this->levelingDokter['drId'],
                "drName"      => $this->levelingDokter['drName'],
                "poliId"      => $this->levelingDokter['poliId'],
                "poliDesc"    => $this->levelingDokter['poliDesc'],
                "tglEntry"    => $this->levelingDokter['tglEntry'],
                "levelDokter" => $this->levelingDokter['levelDokter'],
            ];

            $this->dataDaftarUgd['trfUgd']['levelingDokterLog'] = [
                'userLogDesc' => 'Form Entry levelingDokter ' . $this->levelingDokter['drName'] . ' ' . $this->levelingDokter['levelDokter'],
                'userLog'     => auth()->user()->myuser_name,
                'userLogDate' => Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s'),
            ];

            $this->store();

            // reset form entry
            $this->reset(['levelingDokter']);
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError("LevelingDokter sudah ada.");
        }
    }


    public function removeLevelingDokter($tglEntry)
    {
        $list = collect($this->dataDaftarUgd['trfUgd']['levelingDokter'] ?? []);
        $levelingDokter = $list->where('tglEntry', '!=', $tglEntry)->values()->toArray();

        $this->dataDaftarUgd['trfUgd']['levelingDokter'] = $levelingDokter;

        $this->dataDaftarUgd['trfUgd']['levelingDokterLog'] = [
            'userLogDesc' => 'Hapus levelingDokter',
            'userLog'     => auth()->user()->myuser_name,
            'userLogDate' => Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s'),
        ];

        $this->store();
    }

    public function setLevelingDokterUtama($index, $level = "Utama")
    {
        $list = &$this->dataDaftarUgd['trfUgd']['levelingDokter'];

        if (!isset($list) || !is_array($list) || !isset($list[$index])) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError("Data leveling dokter tidak ditemukan.");
            return;
        }

        if (($list[$index]['levelDokter'] ?? null) === $level) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError("Status Dokter {$level}");
            return;
        }

        $this->dataDaftarUgd['trfUgd']['levelingDokterLog'] = [
            'userLogDesc' => 'Ubah levelingDokter ' . ($list[$index]['drName'] ?? '-') .
                ' dari ' . ($list[$index]['levelDokter'] ?? '-') . ' ke ' . $level,
            'userLog'     => auth()->user()->myuser_name,
            'userLogDate' => Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s'),
        ];

        $list[$index]['levelDokter'] = $level;

        $this->store();
    }


    public function setLevelingDokterRawatGabung($index, $level = "RawatGabung")
    {
        $list = &$this->dataDaftarUgd['trfUgd']['levelingDokter'];

        if (!isset($list) || !is_array($list)) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Data leveling dokter tidak ditemukan.');
            return;
        }

        if (!isset($list[$index])) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Item leveling dokter tidak ditemukan.');
            return;
        }

        $current = $list[$index]['levelDokter'] ?? null;
        if ($current === $level) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError("Status Dokter {$level}");
            return;
        }

        $this->dataDaftarUgd['trfUgd']['levelingDokterLog'] = [
            'userLogDesc' => 'Ubah levelingDokter ' . ($list[$index]['drName'] ?? '-') .
                ' dari ' . ($current ?? '-') . ' ke ' . $level,
            'userLog'     => auth()->user()->myuser_name,
            'userLogDate' => Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s'),
        ];

        $list[$index]['levelDokter'] = $level;

        $this->store();
    }


    public function resetLevelingDokter()
    {
        $this->reset([
            'levelingDokter',
            'collectingMyDokter'
        ]);
        $this->resetValidation();
    }

    // =============================
    // LOAD DATA UGD → TRF UGD
    // =============================
    private function findData($rjno): void
    {
        $data = $this->findDataUGD($rjno) ?: [];
        $this->dataDaftarUgd = $data;

        if (!isset($this->dataDaftarUgd['trfUgd']) || !is_array($this->dataDaftarUgd['trfUgd'])) {

            $keluhanUtama = trim((string) data_get($data, 'anamnesa.keluhanUtama.keluhanUtama', ''));
            $alergi       = trim((string) data_get($data, 'anamnesa.alergi.alergi', ''));
            $dxFree       = trim((string) data_get($data, 'diagnosisFreeText', ''));
            $terapiText   = (string) data_get($data, 'perencanaan.terapi.terapi', '');

            $terapiUgd = array_filter(
                array_map('trim', explode("\n", $terapiText))
            );

            $this->dataDaftarUgd['trfUgd'] = [
                "keluhanUtama"      => $keluhanUtama,
                "temuanSignifikan"  => '',
                "alergi"            => $alergi,
                "diagnosisFreeText" => $dxFree,
                "terapiUgd"         => $terapiUgd,
                "levelingDokter"    => $data['trfUgd']['levelingDokter']    ?? [],

                // Pindah ruangan
                "pindahDariRuangan" => '',
                "pindahKeRuangan"   => '',
                "tglPindah"         => '', // dd/mm/yyyy hh24:mi:ss (kalau mau ikutin format lain, tinggal sesuaikan)

                // Kondisi & fasilitas
                "kondisiKlinisDerajat" => '', // 0 / 1 / 2 / 3 (freetext / bisa di-validasi belakangan)
                "fasilitas"            => '', // freetext, contoh: oksigen, monitor, infus, dll.
                "alasanPindah"         => '', // freetext
                "metodePemindahanPasien" => '', // freetext, contoh: brankar, kursi roda, jalan, dll.

                "kondisiSaatDikirim" => [
                    "sistolik"        => '',
                    "diastolik"       => '',
                    "frekuensiNafas"  => '',
                    "frekuensiNadi"   => '',
                    "suhu"            => '',
                    "spo2"            => '',
                    "gda"             => '',
                    "gcs"             => '',
                    "keadaanPasien"   => '',
                ],

                "kondisiSaatDiterima" => [
                    "sistolik"        => '',
                    "diastolik"       => '',
                    "frekuensiNafas"  => '',
                    "frekuensiNadi"   => '',
                    "suhu"            => '',
                    "spo2"            => '',
                    "gda"             => '',
                    "gcs"             => '',
                    "keadaanPasien"   => '',
                ],

                // ["jenis" => "IV Line", "lokasi" => "Tangan kanan", "ukuran" => "22G", "keterangan" => "baik"]
                "alatYangTerpasang" => [],

                // Rencana Perawatan
                "rencanaPerawatan" => [
                    "observasi"         => '', // freetext
                    "pembatasanCairan"  => '', // freetext
                    "balanceCairan"     => '', // freetext
                    "lainLain"          => '', // freetext
                    "diet"              => '', // freetext
                ],

                // petugas pengirim
                "petugasPengirim"      => '',
                "petugasPengirimCode"  => '',
                "petugasPengirimDate"  => '',

                // petugas penerima
                "petugasPenerima"      => '',
                "petugasPenerimaCode"  => '',
                "petugasPenerimaDate"  => '',
            ];
        }
    }


    public function setPetugasPengirim()
    {
        $code = auth()->user()->myuser_code ?? '';
        $name = auth()->user()->myuser_name ?? '';

        if (empty($this->dataDaftarUgd['trfUgd']['petugasPengirim'])) {

            $this->dataDaftarUgd['trfUgd']['petugasPengirim']     = $name;
            $this->dataDaftarUgd['trfUgd']['petugasPengirimCode'] = $code;
            $this->dataDaftarUgd['trfUgd']['petugasPengirimDate'] = Carbon::now(config('app.timezone'))->format('d/m/Y H:i:s');

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addSuccess("Petugas Pengirim berhasil diisi.");

            $this->store();
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError("Petugas Pengirim sudah diisi sebelumnya.");
        }
    }

    public function setPetugasPenerima()
    {
        $code = auth()->user()->myuser_code ?? '';
        $name = auth()->user()->myuser_name ?? '';

        if (empty($this->dataDaftarUgd['trfUgd']['petugasPenerima'])) {

            $this->dataDaftarUgd['trfUgd']['petugasPenerima']     = $name;
            $this->dataDaftarUgd['trfUgd']['petugasPenerimaCode'] = $code;
            $this->dataDaftarUgd['trfUgd']['petugasPenerimaDate'] = Carbon::now(config('app.timezone'))->format('d/m/Y H:i:s');

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addSuccess("Petugas Penerima berhasil diisi.");

            $this->store();
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError("Petugas Penerima sudah diisi sebelumnya.");
        }
    }



    public function addAlatTerpasang()
    {
        // validasi minimal jenis harus isi
        $this->validate([
            'alat.jenis' => 'required|string|max:100',
            'alat.lokasi' => 'nullable|string|max:100',
            'alat.ukuran' => 'nullable|string|max:50',
            'alat.keterangan' => 'nullable|string|max:255',
        ]);

        // push item ke array
        $this->dataDaftarUgd['trfUgd']['alatYangTerpasang'][] = [
            "jenis"      => trim($this->alat['jenis']),
            "lokasi"     => trim($this->alat['lokasi']),
            "ukuran"     => trim($this->alat['ukuran']),
            "keterangan" => trim($this->alat['keterangan']),
        ];

        // simpan
        $this->store();

        // reset form alat
        $this->reset(['alat']);

        toastr()->closeOnHover(true)->closeDuration(3)
            ->positionClass('toast-top-left')
            ->addSuccess("Alat terpasang berhasil ditambahkan.");
    }

    public function removeAlatTerpasang($index)
    {
        $list = collect($this->dataDaftarUgd['trfUgd']['alatYangTerpasang'] ?? []);

        // hapus index
        $filtered = $list->forget($index)->values()->toArray();

        $this->dataDaftarUgd['trfUgd']['alatYangTerpasang'] = $filtered;

        $this->store();

        toastr()->closeOnHover(true)->closeDuration(3)
            ->positionClass('toast-top-left')
            ->addSuccess("Data alat terpasang berhasil dihapus.");
    }

    public function setTglPindah()
    {
        $this->dataDaftarUgd['trfUgd']['tglPindah'] =
            Carbon::now(config('app.timezone'))->format('d/m/Y H:i:s');
    }

    public function cetakTrfPasienUgd()
    {
        // Ambil identitas RS
        $identitasRs = DB::table('rsmst_identitases')
            ->select('int_name', 'int_phone1', 'int_phone2', 'int_fax', 'int_address', 'int_city')
            ->first();

        // Ambil rjNo (prioritas dari state terbaru)
        $rjNo = $this->dataDaftarUgd['rjNo'] ?? $this->rjNoRef ?? null;

        if (!$rjNo) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Nomor RJ (UGD) tidak ditemukan.');
            return;
        }

        try {
            // Data UGD lengkap (termasuk trfUgd)
            $dataDaftarUgd = $this->findDataUGD($rjNo) ?: [];

            // Ambil regNo utk data master pasien
            $regNo = $dataDaftarUgd['regNo'] ?? null;
            $dataPasien = $regNo ? $this->findDataMasterPasien($regNo) : [];

            $data = [
                'identitasRs'   => $identitasRs,
                'dataPasien'    => $dataPasien,
                'dataDaftarUgd' => $dataDaftarUgd,
            ];

            // Sesuaikan nama view dengan lokasi file blade kamu
            $pdfContent = Pdf::loadView('livewire.cetak.cetak-trf-pasien-u-g-d-print', $data)->output();

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addSuccess('Berhasil mencetak TRF Pasien UGD.');

            return response()->streamDownload(
                fn() => print($pdfContent),
                'trf-pasien-ugd-' . $rjNo . '.pdf'
            );
        } catch (Exception $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Gagal mencetak TRF UGD: ' . $e->getMessage());
        }
    }

    // when new form instance
    public function mount()
    {
        $this->findData($this->rjNoRef);
    }

    // select data start////////////////
    public function render()
    {

        // sync LOV Dokter dan isi form entry levelingDokter
        $this->syncLOV();
        $this->syncDataFormEntry();

        return view('livewire.emr-u-g-d.trf-pasien-u-g-d.trf-pasien-u-g-d');
    }
    // select data end////////////////


}
