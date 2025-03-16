<?php

namespace App\Http\Livewire\MasterKamarAplicares;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\BPJS\AplicaresTrait;

use App\Http\Traits\customErrorMessagesTrait;

class MasterKamarAplicares extends Component
{

    use WithPagination, AplicaresTrait;
    protected $listeners = ['masterKamarAplicaresCloseModal' => 'closeModal'];



    // primitive Variable
    public string $myTitle = 'Data KamarAplicares';
    public string $mySnipt = 'Master KamarAplicares / Ruang KamarAplicares';
    public string $myProgram = 'Data KamarAplicares';

    // ID
    public string $kamarAplicaresId;
    public array $kamarLists = [];

    public function createKelas(): void
    {
        $this->displayKelas();

        // Daftar kodekelas dari data kamarLists
        $kodeKelasKamarRS = collect($this->kamarLists['response']['list'] ?? [])
            ->pluck('kodekelas')->toArray();


        $listKamar = [
            ["namakelas" => "VIP",       "kodekelas" => "VIP"],
            ["namakelas" => "KELAS I",   "kodekelas" => "KL1"],
            ["namakelas" => "KELAS II",  "kodekelas" => "KL2"],
            ["namakelas" => "KELAS III", "kodekelas" => "KL3"],
            ["namakelas" => "ICU",       "kodekelas" => "ICU"],
            ["namakelas" => "NICU",      "kodekelas" => "NIC"],
            ["namakelas" => "PICU",      "kodekelas" => "PIC"],
        ];

        // Daftar kodekelas yang diizinkan dari listKamar
        $kodeKelasKamarBaru = collect($listKamar)->whereNotIn('kodekelas', $kodeKelasKamarRS)->pluck('kodekelas')->toArray();
        // Dapatkan kodekelas yang ada di kamarLists, tetapi tidak ada di listKamar
        if ($kodeKelasKamarBaru) {
            foreach ($kodeKelasKamarBaru as $kode) {
                // Cari data default dari $listKamar berdasarkan kodekelas
                $data = collect($listKamar)->firstWhere('kodekelas', $kode);
                // Buat item baru dengan struktur lengkap
                $ruangBaru = [
                    'kodekelas'          => $data['kodekelas'] ?? '1',
                    'koderuang'          => $data['kodekelas'] ?? '1', // default kosong, bisa diisi nilai sesuai kebutuhan
                    'namaruang'          => $data['namakelas'] ?? '', // asumsikan namaruang sama dengan namakelas jika tidak ada data lain
                    'kapasitas'          => 1,  // default 0; sesuaikan dengan kebutuhan
                    'tersedia'           => 0,
                    'tersediapria'       => 0,
                    'tersediawanita'     => 0,
                    'tersediapriawanita' => 0,
                ];
                $this->ruanganBaru($ruangBaru);
            }
        }
        $this->displayKelas();
    }

    public function removeKelas($kelas, $ruang): void
    {
        $x = $this->hapusRuangan($kelas, $ruang);
        $this->displayKelas();
    }

    public function displayKelas(): void
    {
        $ketersediaanKamarRS = $this->ketersediaanKamarRS();
        $this->kamarLists = $ketersediaanKamarRS->getOriginalContent();
    }

    public function updateKelasAll(): void
    {

        $mappingListKamar = [
            ["namakelas" => "VIP",       "kodekelas" => "VIP", "kodekelasRs" => "VIP"],
            ["namakelas" => "KELAS I",   "kodekelas" => "KL1", "kodekelasRs" => "1"],
            ["namakelas" => "KELAS II",  "kodekelas" => "KL2", "kodekelasRs" => "2"],
            ["namakelas" => "KELAS III", "kodekelas" => "KL3", "kodekelasRs" => "3"],
            ["namakelas" => "ICU",       "kodekelas" => "ICU", "kodekelasRs" => "ICU"],
            ["namakelas" => "NICU",      "kodekelas" => "NIC", "kodekelasRs" => "NIC"],
            ["namakelas" => "PICU",      "kodekelas" => "PIC", "kodekelasRs" => "PIC"],
        ];

        $this->displayKelas();
        // Daftar kodekelas dari data kamarLists
        $kodeKelasKamarRS = $this->kamarLists['response']['list'] ?? [];

        if ($kodeKelasKamarRS) {
            foreach ($kodeKelasKamarRS as $kode) {
                // Buat item baru dengan struktur lengkap
                $kodekelasRs = collect($mappingListKamar)->firstWhere('kodekelas', $kode['kodekelas']);

                $kapasitas = DB::table('rsmst_rooms as a')
                    ->where(DB::raw("to_char(a.class_id)"), $kodekelasRs['kodekelasRs'] ?? '')
                    ->count();

                $terpakai = DB::table('rsmst_rooms as a')
                    ->join('rstxn_rihdrs as b', 'a.room_id', '=', 'b.room_id')
                    ->where('b.ri_status', 'I')
                    ->where(DB::raw("to_char(a.class_id)"), $kodekelasRs['kodekelasRs'] ?? '')
                    ->count();

                $tersedia = $kapasitas - $terpakai;

                $updateKelas = [
                    'kodekelas'          => $kode['kodekelas'] ?? '1',
                    'koderuang'          => $kode['kodekelas'] ?? '1', // default kosong, bisa diisi nilai sesuai kebutuhan
                    'namaruang'          => $kode['namakelas'] ?? '', // asumsikan namaruang sama dengan namakelas jika tidak ada data lain
                    'kapasitas'          => $kapasitas,  // default 0; sesuaikan dengan kebutuhan
                    'tersedia'           => $tersedia,
                    'tersediapria'       => 0,
                    'tersediawanita'     => 0,
                    'tersediapriawanita' => $tersedia,
                ];
                $this->updateKetersediaanTempatTidur($updateKelas);
            }
        }
        $this->displayKelas();
    }


    public function render()
    {

        if (isset($this->kamarLists['response']['list'])) {
            // Konversi array ke Collection, urutkan, lalu kembalikan sebagai array
            $this->kamarLists['response']['list'] = collect($this->kamarLists['response']['list'])
                ->sortBy('koderuang')
                ->values()
                ->all();
        }

        return view('livewire.master-kamar-aplicares.master-kamar-aplicares', ['myQueryData' => $this->kamarLists]);
    }
}
