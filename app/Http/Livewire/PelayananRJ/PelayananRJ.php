<?php

namespace App\Http\Livewire\PelayananRJ;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use App\Http\Traits\EmrRJ\EmrRJTrait;
use App\Http\Traits\BPJS\AntrianTrait;

use Spatie\ArrayToXml\ArrayToXml;


class PelayananRJ extends Component
{
    use WithPagination, EmrRJTrait;



    //////////////////////////////
    // Ref on top bar start
    //////////////////////////////
    public $dateRjRef = '';

    public $shiftRjRef = [
        'shiftId' => '1',
        'shiftDesc' => '1',
        'shiftOptions' => [
            ['shiftId' => '1', 'shiftDesc' => '1'],
            ['shiftId' => '2', 'shiftDesc' => '2'],
            ['shiftId' => '3', 'shiftDesc' => '3'],
        ]
    ];

    public $statusRjRef = [
        'statusId' => 'A',
        'statusDesc' => 'Antrian',
        'statusOptions' => [
            ['statusId' => 'A', 'statusDesc' => 'Antrian'],
            ['statusId' => 'L', 'statusDesc' => 'Selesai'],
            ['statusId' => 'I', 'statusDesc' => 'Transfer'],
        ]
    ];

    public $drRjRef = [
        'drId' => 'All',
        'drName' => 'All',
        'drOptions' => [
            [
                'drId' => 'All',
                'drName' => 'All'
            ]
        ]
    ];
    //////////////////////////////
    // Ref on top bar end
    //////////////////////////////

    public $dataDaftarPoliRJ = [];
    public $taskIdPelayanan = [
        "taskId1" => "",
        "taskId2" => "",
        "taskId3" => "",
        "taskId4" => "",
        "taskId5" => "",
        "taskId6" => "",
        "taskId7" => "",
        "taskId99" => "",
    ];


    // limit record per page -resetExcept////////////////
    public $limitPerPage = 10;


    // search logic -resetExcept////////////////
    public $search;
    protected $queryString = [
        'search' => ['except' => '', 'as' => 'cariData'],
        'page' => ['except' => 1, 'as' => 'p'],
    ];


    // sort logic -resetExcept////////////////
    public $sortField = 'reg_no';
    public $sortAsc = true;



    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////





    // resert input private////////////////
    private function resetInputFields(): void
    {

        // resert validation
        $this->resetValidation();
        // resert input
        $this->resetExcept([
            'limitPerPage',
            'search',
            'dateRjRef',
            'shiftRjRef',
            'statusRjRef',
            'drRjRef'

        ]);
    }





    // setLimitPerpage////////////////
    public function setLimitPerPage($value): void
    {
        $this->limitPerPage = $value;
        $this->resetValidation();
    }


    private function optionsdrRjRef(): void
    {
        // Query
        $query = DB::table('rsview_rjkasir')
            ->select(
                'dr_id',
                'dr_name',
            )
            ->where('shift', '=', $this->shiftRjRef['shiftId'])
            ->where(DB::raw("to_char(rj_date,'dd/mm/yyyy')"), '=', $this->dateRjRef)
            ->groupBy('dr_id')
            ->groupBy('dr_name')
            ->orderBy('dr_name', 'desc')
            ->get();

        // loop and set Ref
        $query->each(function ($item, $key) {
            $this->drRjRef['drOptions'][$key + 1]['drId'] = $item->dr_id;
            $this->drRjRef['drOptions'][$key + 1]['drName'] = $item->dr_name;
        })->toArray();
    }






    /////////////////////////////////////////////////////////////////////
    // resert page pagination when coloumn search change ////////////////
    // tabular Ref topbar
    /////////////////////////////////////////////////////////////////////

    // search
    public function updatedSearch(): void
    {
        //  toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError( "search.");

        $this->resetPage();
        $this->resetValidation();
        $this->resetInputFields();
    }
    // date
    public function updatedDaterjref(): void
    {
        //  toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError( "date.");

        $this->resetPage();
        $this->resetValidation();
        $this->resetInputFields();
    }
    // status
    public function updatedStatusrjref(): void
    {
        //  toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError( "status.");

        $this->resetPage();
        $this->resetValidation();
        $this->resetInputFields();
    }
    // dr
    public function setdrRjRef($id, $name): void
    {
        //  toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError( "dr.");

        $this->drRjRef['drId'] = $id;
        $this->drRjRef['drName'] = $name;
        $this->resetPage();
        $this->resetValidation();
        $this->resetInputFields();
    }
    // shift
    public function setShift($id, $desc): void
    {
        //  toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError( "shift.");

        $this->shiftRjRef['shiftId'] = $id;
        $this->shiftRjRef['shiftDesc'] = $desc;
        $this->resetPage();
        $this->resetValidation();
        $this->resetInputFields();
    }
    /////////////////////////////////////////////////////////////////////


    /////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////
    private function updateDataRJ($rjNo): void
    {
        // if ($rjNo !== $this->dataDaftarPoliRJ['rjNo']) {
        //     dd('Data Json Tidak sesuai' . $rjNo . '  /  ' . $this->dataDaftarPoliRJ['rjNo']);
        // }

        // // update table trnsaksi
        // DB::table('rstxn_rjhdrs')
        //     ->where('rj_no', $rjNo)
        //     ->update([
        //         'datadaftarpolirj_json' => json_encode($this->dataDaftarPoliRJ, true),
        //         'datadaftarpolirj_xml' => ArrayToXml::convert($this->dataDaftarPoliRJ),
        //     ]);
        $this->updateJsonRJ($rjNo, $this->dataDaftarPoliRJ);
    }

    private function findData($rjNo): void
    {

        $findDataRJ = $this->findDataRJ($rjNo);
        $this->dataDaftarPoliRJ  = $findDataRJ['dataDaftarRJ'];
    }



    public function masukAdmisi($rjNo)
    {
        // Ambil data terkait rjNo
        $this->findData($rjNo);

        // Ambil noBooking dari data yang telah di-load
        $noBooking = $this->dataDaftarPoliRJ['noBooking'];

        // Ambil taskId1 jika ada
        $taskId1 = $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId1'] ?? null;

        if ($taskId1) {
            // Konversi waktu dari format d/m/Y H:i:s ke timestamp dalam milisecond
            $waktu = Carbon::createFromFormat('d/m/Y H:i:s', $taskId1, env('APP_TIMEZONE'))->timestamp * 1000;
            $cekPoliSpesialis = DB::table('rsmst_polis')
                ->where('spesialis_status', '1')
                ->where('poli_id', $this->dataDaftarPoliRJ['poliId'])
                ->exists();

            if ($cekPoliSpesialis) {
                $this->pushDataTaskId($noBooking, 1, $waktu);
            }
        } else {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError("Waktu masuk Admisi kosong, tidak dapat dikirim");
        }
    }


    public function keluarAdmisi($rjNo)
    {
        // Ambil data terkait rjNo dan noBooking
        $this->findData($rjNo);
        $noBooking = $this->dataDaftarPoliRJ['noBooking'];

        // Cek dan set taskId1 jika kosong
        $taskId1 = $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId1'] ?? null;
        if (!$taskId1) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError("Waktu masuk Admisi kosong, tidak dapat dikirim");
            return;
        }

        // Ambil taskId2 (waktu keluar Admisi)
        // Cek taskId2 (waktu keluar Admisi)
        $taskId2 = $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId2'] ?? null;
        if (!$taskId2) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError("Waktu keluar Admisi kosong, tidak dapat dikirim");
            return;
        }

        // Konversi taskId2 ke timestamp (milisecond)
        $waktu = Carbon::createFromFormat('d/m/Y H:i:s', $taskId2, env('APP_TIMEZONE'))->timestamp * 1000;

        // Cek apakah poli termasuk spesialis
        $cekPoliSpesialis = DB::table('rsmst_polis')
            ->where('spesialis_status', '1')
            ->where('poli_id', $this->dataDaftarPoliRJ['poliId'])
            ->exists();

        if ($cekPoliSpesialis) {
            $this->pushDataTaskId($noBooking, 2, $waktu);
        }
    }


    public function daftarPoli($rjNo)
    {
        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("untuk update taskID3 Daftar Poli memakai Pendaftaran Rawat Jalan");
    }


    public function masukPoli($rjNo)
    {
        // Ambil data terkait rjNo
        $this->findData($rjNo);

        // Cek apakah kolom waktu_masuk_poli sudah terisi di DB
        $waktuMasukPoliDB = DB::table('rstxn_rjhdrs')
            ->where('rj_no', $rjNo)
            ->value('waktu_masuk_poli');

        // Jika belum ada, update dengan waktu saat ini
        if (!$waktuMasukPoliDB) {
            $currentTime = Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s');
            DB::table('rstxn_rjhdrs')
                ->where('rj_no', $rjNo)
                ->update([
                    'waktu_masuk_poli' => DB::raw("to_date('" . $currentTime . "', 'dd/mm/yyyy hh24:mi:ss')")
                ]);
        }

        // Ambil waktu_masuk_poli dalam format string dari DB
        $formattedWaktuMasukPoli = DB::table('rstxn_rjhdrs')
            ->where('rj_no', $rjNo)
            ->select(DB::raw("to_char(waktu_masuk_poli, 'dd/mm/yyyy hh24:mi:ss') as waktu_masuk_poli"))
            ->value('waktu_masuk_poli');

        // Jika taskId4 belum terisi, set dengan nilai waktu_masuk_poli dan update data
        if (empty($this->dataDaftarPoliRJ['taskIdPelayanan']['taskId4'])) {
            $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId4'] = $formattedWaktuMasukPoli;
            $this->updateDataRJ($rjNo);
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addSuccess("Masuk Poli " . $formattedWaktuMasukPoli);
        } else {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError("Masuk Poli " . $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId4']);
        }

        // Cari noBooking
        $noBooking = $this->dataDaftarPoliRJ['noBooking'];

        // Konversi taskId4 ke timestamp (milisecond)
        $timestamp = Carbon::createFromFormat(
            'd/m/Y H:i:s',
            $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId4'],
            env('APP_TIMEZONE')
        )->timestamp * 1000;

        // Push data taskId4 ke sistem
        $cekPoliSpesialis = DB::table('rsmst_polis')
            ->where('spesialis_status', '1')
            ->where('poli_id', $this->dataDaftarPoliRJ['poliId'])
            ->exists();

        if ($cekPoliSpesialis) {
            $this->pushDataTaskId($noBooking, 4, $timestamp);
        }
    }



    public function keluarPoli($rjNo)
    {
        // Ambil data terkait rjNo
        $this->findData($rjNo);

        // Pastikan pasien sudah melalui pelayanan Poli (taskId4 harus sudah terisi)
        if (empty($this->dataDaftarPoliRJ['taskIdPelayanan']['taskId4'])) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError("Status Pasien Belum melalui pelayanan Poli");
            return;
        }

        // Dapatkan waktu keluar poli saat ini
        $keluarPoli = Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s');

        // Jika taskId5 belum tercatat, set dengan waktu keluar poli dan update data
        if (empty($this->dataDaftarPoliRJ['taskIdPelayanan']['taskId5'])) {
            $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId5'] = $keluarPoli;
            $this->updateDataRJ($rjNo);
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addSuccess("Keluar Poli " . $keluarPoli);
        } else {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError("Keluar Poli " . $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId5'] . " sudah tercatat");
        }

        // Ambil noBooking dari data yang telah di-load
        $noBooking = $this->dataDaftarPoliRJ['noBooking'];

        // Konversi taskId5 ke timestamp dalam milisecond
        $waktu = Carbon::createFromFormat(
            'd/m/Y H:i:s',
            $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId5'],
            env('APP_TIMEZONE')
        )->timestamp * 1000;

        // Push data taskId5 ke sistem
        $cekPoliSpesialis = DB::table('rsmst_polis')
            ->where('spesialis_status', '1')
            ->where('poli_id', $this->dataDaftarPoliRJ['poliId'])
            ->exists();

        if ($cekPoliSpesialis) {
            $this->pushDataTaskId($noBooking, 5, $waktu);
        }
    }



    public function batalPoli($rjNo, $regName): void
    {
        // Ambil data terkait rjNo
        $this->findData($rjNo);

        // Dapatkan waktu pembatalan saat ini dengan format yang sesuai
        $waktuBatalPoli = Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s');

        // Jika taskId5 sudah terisi, pembatalan tidak dapat dilakukan
        if (!empty($this->dataDaftarPoliRJ['taskIdPelayanan']['taskId5'])) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError("Pembatalan tidak dapat dilakukan, {$regName} sudah melakukan pelayanan Poli.");
            return;
        }

        // Set taskId99 dengan waktu pembatalan dan perbarui data di database
        $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId99'] = $waktuBatalPoli;
        $this->updateDataRJ($rjNo);

        toastr()
            ->closeOnHover(true)
            ->closeDuration(3)
            ->positionClass('toast-top-left')
            ->addSuccess("Pembatalan {$regName} pelayanan Poli berhasil dilakukan.");

        // Dapatkan noBooking dan konversi waktu pembatalan ke timestamp (milisecond)
        $noBooking = $this->dataDaftarPoliRJ['noBooking'];
        $waktuTimestamp = Carbon::createFromFormat('d/m/Y H:i:s', $waktuBatalPoli, env('APP_TIMEZONE'))->timestamp * 1000;

        // Push data taskId99 ke sistem
        $cekPoliSpesialis = DB::table('rsmst_polis')
            ->where('spesialis_status', '1')
            ->where('poli_id', $this->dataDaftarPoliRJ['poliId'])
            ->exists();

        if ($cekPoliSpesialis) {
            $this->pushDataTaskId($noBooking, 99, $waktuTimestamp);
        }
    }


    public function masukApotek($rjNo)
    {
        // Load data terkait rjNo
        $this->findData($rjNo);

        // Pastikan taskIdPelayanan.taskId5 (waktu keluar Poli) sudah ada;
        // jika tidak, pembaruan taskId6 tidak bisa dilakukan
        if (empty($this->dataDaftarPoliRJ['taskIdPelayanan']['taskId5'])) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError("Anda tidak dapat melakukan taskId6 ketika taskId5 Kosong");
            return;
        }

        // Dapatkan waktu masuk apotek saat ini dengan format yang sesuai
        $masukApotek = Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s');

        // Cek apakah kolom waktu_masuk_apt sudah terisi di DB
        $waktuMasukApt = DB::table('rstxn_rjhdrs')
            ->where('rj_no', $rjNo)
            ->value('waktu_masuk_apt');

        // Jika belum terisi, update kolom tersebut dengan waktu saat ini
        if (!$waktuMasukApt) {
            DB::table('rstxn_rjhdrs')
                ->where('rj_no', $rjNo)
                ->update([
                    'waktu_masuk_apt' => DB::raw("to_date('{$masukApotek}','dd/mm/yyyy hh24:mi:ss')")
                ]);
        }



        // Ambil noBooking dari data yang telah di-load
        $noBooking = $this->dataDaftarPoliRJ['noBooking'];

        // --- Proses pembuatan antrian Apotek ---
        // Cek apakah data noAntrianApotek sudah ada
        if (!isset($this->dataDaftarPoliRJ['noAntrianApotek'])) {
            // Gunakan data yang sudah di-load untuk menghitung jumlah resep racikan
            $eresepRacikanCount = count($this->dataDaftarPoliRJ['eresepRacikan'] ?? []);
            $jenisResep = $eresepRacikanCount > 0 ? 'racikan' : 'non racikan';

            // Query untuk menghitung jumlah antrian Apotek hari ini
            $queueRecords = DB::table('rstxn_rjhdrs')
                ->select(
                    DB::raw("to_char(rj_date, 'dd/mm/yyyy') AS rj_date"),
                    DB::raw("to_char(rj_date, 'yyyymmdd') AS rj_date1"),
                    'datadaftarpolirj_json'
                )
                ->where('rj_status', '!=', 'F')
                ->where('klaim_id', '!=', 'KR')
                ->where(DB::raw("to_char(rj_date, 'dd/mm/yyyy')"), '=', $this->dateRjRef)
                ->get();

            // Hitung jumlah record yang sudah memiliki antrian Apotek
            $nomerAntrian = $queueRecords->filter(function ($item) {
                $dataJson = json_decode($item->datadaftarpolirj_json, true);
                return isset($dataJson['noAntrianApotek']);
            })->count();

            // Jika klaim bukan 'KR', nomor antrian bertambah; jika 'KR', nomor antrian tetap 999
            $noAntrian = $this->dataDaftarPoliRJ['klaimId'] != 'KR' ? $nomerAntrian + 1 : 999;

            // Simpan data antrian Apotek dan update ke DB
            $this->dataDaftarPoliRJ['noAntrianApotek'] = [
                'noAntrian'  => $noAntrian,
                'jenisResep' => $jenisResep
            ];
            $this->updateDataRJ($rjNo);
        }
        // --- Akhir proses pembuatan antrian Apotek ---

        // Push data antrean Apotek ke sistem
        // Kirim antrean Apotek
        $cekPoliSpesialis = DB::table('rsmst_polis')
            ->where('spesialis_status', '1')
            ->where('poli_id', $this->dataDaftarPoliRJ['poliId'])
            ->exists();

        if ($cekPoliSpesialis) {
            $this->pushAntreanApotek(
                $noBooking,
                $this->dataDaftarPoliRJ['noAntrianApotek']['jenisResep'],
                $this->dataDaftarPoliRJ['noAntrianApotek']['noAntrian']
            );
        }

        // --- Proses Update taskId6 (waktu masuk Apotek) ---
        if (empty($this->dataDaftarPoliRJ['taskIdPelayanan']['taskId6'])) {
            // Set taskId6 dengan waktu masuk apotek dan update ke DB
            $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId6'] = $masukApotek;
            $this->updateDataRJ($rjNo);
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addSuccess("Masuk Apotek " . $masukApotek);
        } else {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError("Masuk Apotek " . $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId6']);
        }
        // --- Akhir proses update taskId6 ---

        // Jika taskId6 tersedia, konversi ke timestamp (milisecond) dan push ke sistem
        if (!empty($this->dataDaftarPoliRJ['taskIdPelayanan']['taskId6'])) {
            $waktuTimestamp = Carbon::createFromFormat(
                'd/m/Y H:i:s',
                $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId6'],
                env('APP_TIMEZONE')
            )->timestamp * 1000;

            $cekPoliSpesialis = DB::table('rsmst_polis')
                ->where('spesialis_status', '1')
                ->where('poli_id', $this->dataDaftarPoliRJ['poliId'])
                ->exists();

            if ($cekPoliSpesialis) {
                $this->pushDataTaskId($noBooking, 6, $waktuTimestamp);
            }
        } else {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError("Waktu Masuk Apotek kosong, tidak dapat dikirim");
        }
    }


    public function keluarApotek($rjNo)
    {
        // Ambil data terkait rjNo
        $this->findData($rjNo);

        // Pastikan taskId6 (waktu masuk Apotek) sudah tercatat sebelum melanjutkan ke taskId7
        if (empty($this->dataDaftarPoliRJ['taskIdPelayanan']['taskId6'])) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError("Anda tidak dapat melakukan taskId7 ketika taskId6 Kosong");
            return;
        }

        // Dapatkan waktu keluar Apotek saat ini dengan format d/m/Y H:i:s
        $keluarApotek = Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s');

        // Cek apakah kolom waktu_selesai_pelayanan sudah terisi di DB
        $waktuSelesaiPelayanan = DB::table('rstxn_rjhdrs')
            ->where('rj_no', $rjNo)
            ->value('waktu_selesai_pelayanan');

        // Jika belum terisi, update dengan waktu keluar Apotek
        if (!$waktuSelesaiPelayanan) {
            DB::table('rstxn_rjhdrs')
                ->where('rj_no', $rjNo)
                ->update([
                    'waktu_selesai_pelayanan' => DB::raw("to_date('{$keluarApotek}', 'dd/mm/yyyy hh24:mi:ss')")
                ]);
        }

        // Update taskId7 dengan waktu keluar Apotek jika belum ada
        if (empty($this->dataDaftarPoliRJ['taskIdPelayanan']['taskId7'])) {
            $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId7'] = $keluarApotek;
            $this->updateDataRJ($rjNo);
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addSuccess("Keluar Apotek " . $keluarApotek);
        } else {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError("Keluar Apotek " . $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId7'] . " sudah tercatat");
        }

        // Ambil noBooking dari data yang sudah di-load
        $noBooking = $this->dataDaftarPoliRJ['noBooking'];

        // Jika taskId7 sudah ada, konversi ke timestamp (milisecond) dan push data
        if (!empty($this->dataDaftarPoliRJ['taskIdPelayanan']['taskId7'])) {
            $timestamp = Carbon::createFromFormat(
                'd/m/Y H:i:s',
                $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId7'],
                env('APP_TIMEZONE')
            )->timestamp * 1000;

            // Kirim antrean Apotek
            $cekPoliSpesialis = DB::table('rsmst_polis')
                ->where('spesialis_status', '1')
                ->where('poli_id', $this->dataDaftarPoliRJ['poliId'])
                ->exists();

            if ($cekPoliSpesialis) {
                $this->pushDataTaskId($noBooking, 7, $timestamp);
            }
        } else {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError("Waktu Keluar Apotek kosong, tidak dapat dikirim");
        }
    }



    public function getListTaskId($noBooking): void
    {

        $HttpGetBpjs =  AntrianTrait::taskid_antrean($noBooking)->getOriginalContent();

        dd($HttpGetBpjs);
        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('Task Id' . $noBooking . ' ' . $HttpGetBpjs);
    }

    private function pushDataTaskId($noBooking, $taskId, $time): void
    {
        //////////////////////////////////////////////////////////
        /////////////////////////////////////////////////////////
        // Update Task Id $kodebooking, $taskid, $waktu, $jenisresep

        $waktu = $time;
        $HttpGetBpjs =  AntrianTrait::update_antrean($noBooking, $taskId, $waktu, "")->getOriginalContent();

        // set http response to public
        // $this->HttpGetBpjsStatus = $HttpGetBpjs['metadata']['code']; //status 200 201 400 ..
        // $this->HttpGetBpjsJson = json_encode($HttpGetBpjs, true); //Return Response Tambah Antrean

        // metadata d kecil
        if ($HttpGetBpjs['metadata']['code'] == 200) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('Task Id' . $taskId . ' ' . $HttpGetBpjs['metadata']['code'] . ' ' . $HttpGetBpjs['metadata']['message']);
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Task Id' . $taskId . ' ' .  $HttpGetBpjs['metadata']['code'] . ' ' . $HttpGetBpjs['metadata']['message']);

            // Ulangi Proses pushTaskId;
            // $this->emit('rePush_Data_TaskId_Confirmation');
        }
    }

    private function pushAntreanApotek($noBooking, $jenisResep, $nomerAntrean): void
    {
        $HttpGetBpjs =  AntrianTrait::tambah_antrean_farmasi($noBooking, $jenisResep, $nomerAntrean, "")->getOriginalContent();

        if ($HttpGetBpjs['metadata']['code'] == 200) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('NoBooking' . $noBooking . ' ' . $HttpGetBpjs['metadata']['code'] . ' ' . $HttpGetBpjs['metadata']['message']);
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('NoBooking' . $noBooking . ' ' .  $HttpGetBpjs['metadata']['code'] . ' ' . $HttpGetBpjs['metadata']['message']);
        }
    }


    // when new form instance
    public function mount()
    {

        // set date
        $this->dateRjRef = Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y');
        // set shift
        $findShift = DB::table('rstxn_shiftctls')->select('shift')
            ->whereRaw("'" . Carbon::now(env('APP_TIMEZONE'))->format('H:i:s') . "' between shift_start and shift_end")
            ->first();
        $this->shiftRjRef['shiftId'] = isset($findShift->shift) && $findShift->shift ? $findShift->shift : 3;
        $this->shiftRjRef['shiftDesc'] = isset($findShift->shift) && $findShift->shift ? $findShift->shift : 3;
        // set data dokter ref
        $this->optionsdrRjRef();
    }


    // select data start////////////////
    public function render()
    {

        // render drRjRef
        // set data dokter ref
        $this->optionsdrRjRef();

        //////////////////////////////////////////
        // Query ///////////////////////////////
        //////////////////////////////////////////
        $query = DB::table('rsview_rjkasir')
            ->select(
                DB::raw("to_char(rj_date,'dd/mm/yyyy hh24:mi:ss') AS rj_date"),
                DB::raw("to_char(rj_date,'yyyymmddhh24miss') AS rj_date1"),
                'rj_no',
                'reg_no',
                'reg_name',
                'sex',
                'address',
                'thn',
                DB::raw("to_char(birth_date,'dd/mm/yyyy') AS birth_date"),
                'poli_id',
                'poli_desc',
                'dr_id',
                'dr_name',
                'klaim_id',
                'shift',
                'vno_sep',
                'no_antrian',
                'rj_status',
                'nobooking',
                'push_antrian_bpjs_status',
                'push_antrian_bpjs_json',
                'waktu_masuk_poli',
                'waktu_masuk_apt',
                'datadaftarpolirj_json'
            )
            ->where('rj_status', '=', $this->statusRjRef['statusId'])
            // ->where('shift', '=', $this->shiftRjRef['shiftId'])
            ->where('klaim_id', '!=', 'KR')
            ->where(DB::raw("to_char(rj_date,'dd/mm/yyyy')"), '=', $this->dateRjRef);

        //Jika where dokter tidak kosong
        if ($this->drRjRef['drId'] != 'All') {
            $query->where('dr_id', $this->drRjRef['drId']);
        }

        $query->where(function ($q) {
            $q->Where(DB::raw('upper(reg_name)'), 'like', '%' . strtoupper($this->search) . '%')
                ->orWhere(DB::raw('upper(reg_no)'), 'like', '%' . strtoupper($this->search) . '%')
                ->orWhere(DB::raw('upper(dr_name)'), 'like', '%' . strtoupper($this->search) . '%')
                ->orWhere(DB::raw('upper(poli_desc)'), 'like', '%' . strtoupper($this->search) . '%');
        })
            ->orderBy('shift',  'desc')
            ->orderBy('rj_date1',  'desc')
            ->orderBy('no_antrian',  'desc')
            ->orderBy('dr_name',  'desc')
            ->orderBy('poli_desc',  'desc')
        ;

        ////////////////////////////////////////////////
        // end Query
        ///////////////////////////////////////////////



        return view(
            'livewire.pelayanan-r-j.pelayanan-r-j',
            [
                'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Antrian Pasien Rawat Jalan',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Pasien Rawat Jalan',
                'myLimitPerPages' => [5, 10, 15, 20, 100],
            ]
        );
    }
    // select data end////////////////
}
