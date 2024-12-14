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
        // update table trnsaksi
        DB::table('rstxn_rjhdrs')
            ->where('rj_no', $rjNo)
            ->update([
                'datadaftarpolirj_json' => json_encode($this->dataDaftarPoliRJ, true),
                'datadaftarpolirj_xml' => ArrayToXml::convert($this->dataDaftarPoliRJ),
            ]);
    }

    private function findData($rjNo): void
    {

        $findDataRJ = $this->findDataRJ($rjNo);
        $this->dataDaftarPoliRJ  = $findDataRJ['dataDaftarRJ'];
    }



    public function masukAdmisi($rjNo)
    {

        $this->findData($rjNo);

        //entry data taskId1 hanaya dari MasterPasien disave ketika DaftarRJ

        // cari no Booking
        $noBooking =  $this->dataDaftarPoliRJ['noBooking'];

        if ($this->dataDaftarPoliRJ['taskIdPelayanan']['taskId1']) {
            $waktu = Carbon::createFromFormat('d/m/Y H:i:s', $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId1'], env('APP_TIMEZONE'))->timestamp * 1000; //waktu dalam timestamp milisecond
            $this->pushDataTaskId($noBooking, 1, $waktu);
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("waktu masuk Admisi kosong tidak dapat dikirim");
        }
    }

    public function keluarAdmisi($rjNo)
    {

        $this->findData($rjNo);

        //entry data taskId2 hanaya dari MasterPasien disave ketika DaftarRJ

        // cari no Booking
        $noBooking =  $this->dataDaftarPoliRJ['noBooking'];

        if ($this->dataDaftarPoliRJ['taskIdPelayanan']['taskId2']) {
            $waktu = Carbon::createFromFormat('d/m/Y H:i:s', $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId2'], env('APP_TIMEZONE'))->timestamp * 1000; //waktu dalam timestamp milisecond
            $this->pushDataTaskId($noBooking, 2, $waktu);
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("waktu keluar Admisi kosong tidak dapat dikirim");
        }
    }

    public function daftarPoli($rjNo)
    {
        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("untuk update taskID3 Daftar Poli memakai Pendaftaran Rawat Jalan");
    }


    public function masukPoli($rjNo)
    {

        $this->findData($rjNo);

        $sql = "select waktu_masuk_poli from rstxn_rjhdrs where rj_no=:rjNo";
        $cek_waktu_masuk_poli = DB::scalar($sql, ['rjNo' => $rjNo]);


        // ketika cek_waktu_masuk_poli kosong lalu update
        if (!$cek_waktu_masuk_poli) {
            $waktuMasukPoli = Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s');

            DB::table('rstxn_rjhdrs')
                ->where('rj_no', $rjNo)
                ->update([
                    'waktu_masuk_poli' => DB::raw("to_date('" . $waktuMasukPoli . "','dd/mm/yyyy hh24:mi:ss')"), //waktu masuk = rjdate
                ]);
        }

        /////////////////////////
        // Update TaskId 4
        /////////////////////////
        $sqlWaktuMasukPoli = "select to_char(waktu_masuk_poli,'dd/mm/yyyy hh24:mi:ss') as waktu_masuk_poli from rstxn_rjhdrs where rj_no=:rjNo";
        $waktuMasukPoli = DB::scalar($sqlWaktuMasukPoli, ['rjNo' => $rjNo]);

        if (!$this->dataDaftarPoliRJ['taskIdPelayanan']['taskId4']) {
            $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId4'] = $waktuMasukPoli;
            // update DB
            $this->updateDataRJ($rjNo);

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Masuk Poli " . $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId4']);
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Masuk Poli " . $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId4']);
        }

        // cari no Booking
        $noBooking =  $this->dataDaftarPoliRJ['noBooking'];


        $waktu = Carbon::createFromFormat('d/m/Y H:i:s', $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId4'], env('APP_TIMEZONE'))->timestamp * 1000; //waktu dalam timestamp milisecond
        $this->pushDataTaskId($noBooking, 4, $waktu);
    }


    public function keluarPoli($rjNo)
    {
        $this->findData($rjNo);

        $keluarPoli = Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s');

        // check task Id 4 sudah dilakukan atau belum
        if ($this->dataDaftarPoliRJ['taskIdPelayanan']['taskId4']) {

            if (!$this->dataDaftarPoliRJ['taskIdPelayanan']['taskId5']) {
                $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId5'] = $keluarPoli;
                // update DB
                $this->updateDataRJ($rjNo);

                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Keluar Poli " . $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId5']);
            } else {
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Keluar Poli " . $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId5']);
            }

            // cari no Booking
            $noBooking =  $this->dataDaftarPoliRJ['noBooking'];


            $waktu = Carbon::createFromFormat('d/m/Y H:i:s', $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId5'], env('APP_TIMEZONE'))->timestamp * 1000; //waktu dalam timestamp milisecond
            $this->pushDataTaskId($noBooking, 5, $waktu);

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Keluar Poli " . $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId5']);
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Satus Pasien Belum melalui pelayanan Poli");
        }
    }


    public function batalPoli($rjNo, $regName): void
    {

        $this->findData($rjNo);

        $waktuBatalPoli = Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s');

        /////////////////////////
        // Update TaskId 99 jika task id 5 sudah terisi maka tidak dapat dilakukan pembatalan
        /////////////////////////

        if (!$this->dataDaftarPoliRJ['taskIdPelayanan']['taskId5']) {
            $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId99'] = $waktuBatalPoli;
            // update DB
            $this->updateDataRJ($rjNo);

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Pembatalan " . $regName . " pelayanan Poli berhasil dilakukan.");
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Pembatalan tidak dapat dilakukan, " . $regName . " sudak melakukan pelayanan Poli.");
            return;
        }

        // cari no Booking
        $noBooking =  $this->dataDaftarPoliRJ['noBooking'];


        $waktu = Carbon::createFromFormat('d/m/Y H:i:s', $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId99'], env('APP_TIMEZONE'))->timestamp * 1000; //waktu dalam timestamp milisecond
        $this->pushDataTaskId($noBooking, 99, $waktu);
    }

    public function masukApotek($rjNo)
    {
        $this->findData($rjNo);
        $masukApotek = Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s');

        //////updateDB/////////////////////
        $sql = "select waktu_masuk_apt from rstxn_rjhdrs where rj_no=:rjNo";
        $cek_waktu_masuk_apt = DB::scalar($sql, ['rjNo' => $rjNo]);


        // ketika cek_waktu_masuk_apt kosong lalu update
        if (!$cek_waktu_masuk_apt) {
            DB::table('rstxn_rjhdrs')
                ->where('rj_no', $rjNo)
                ->update([
                    'waktu_masuk_apt' => DB::raw("to_date('" . $masukApotek . "','dd/mm/yyyy hh24:mi:ss')"), //waktu masuk = rjdate
                ]);
        }
        //////////////////////////////////


        // add antrian Apotek

        // update no antrian Apotek

        // cek
        if (!$this->dataDaftarPoliRJ['taskIdPelayanan']['taskId5']) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Anda tidak dapat melakukan taskId6 ketika taskId5 Kosong");
            return;
        }

        $noBooking =  $this->dataDaftarPoliRJ['noBooking'];
        //////PushDataAntrianApotek////////////////////

        // cekNoantrian Apotek sudah ada atau belum
        if (!isset($this->dataDaftarPoliRJ['noAntrianApotek'])) {
            $cekAntrianEresep = $this->findData($rjNo);
            $eresepRacikan = collect(isset($cekAntrianEresep['eresepRacikan']) ? $cekAntrianEresep['eresepRacikan'] : [])->count();
            $jenisResep = $eresepRacikan ? 'racikan' : 'non racikan';

            $query = DB::table('rstxn_rjhdrs')
                ->select(
                    DB::raw("to_char(rj_date,'dd/mm/yyyy') AS rj_date"),
                    DB::raw("to_char(rj_date,'yyyymmdd') AS rj_date1"),
                    'datadaftarpolirj_json'
                )
                ->where('rj_status', '!=', ['F'])
                ->where('klaim_id', '!=', 'KR')
                ->where(DB::raw("to_char(rj_date,'dd/mm/yyyy')"), '=', $this->dateRjRef)
                ->get();

            $nomerAntrian = $query->filter(function ($item) {
                $datadaftarpolirj_json = json_decode($item->datadaftarpolirj_json, true);
                $noAntrianApotek = isset($datadaftarpolirj_json['noAntrianApotek']) ? 1 : 0;
                if ($noAntrianApotek > 0) {
                    return 'x';
                }
            })->count();


            // Antrian ketika data antrian kosong
            // proses antrian
            if ($this->dataDaftarPoliRJ['klaimId'] != 'KR') {
                $noAntrian = $nomerAntrian + 1;
            } else {
                // Kronis
                $noAntrian = 999;
            }
            $this->dataDaftarPoliRJ['noAntrianApotek'] = [
                'noAntrian' => $noAntrian,
                'jenisResep' => $jenisResep
            ];

            $this->updateDataRJ($rjNo);
        }
        // cekNoantrian Apotek sudah ada atau belum


        // tambah antrian Apotek
        $this->pushAntreanApotek($noBooking, $this->dataDaftarPoliRJ['noAntrianApotek']['jenisResep'], $this->dataDaftarPoliRJ['noAntrianApotek']['noAntrian']);
        //////////////////////////


        // update taskId6
        if (!$this->dataDaftarPoliRJ['taskIdPelayanan']['taskId6']) {
            $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId6'] = $masukApotek;
            // update DB
            $this->updateDataRJ($rjNo);

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("masuk Apotek " . $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId6']);
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("masuk Apotek " . $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId6']);
        }

        // cari no Booking

        if ($this->dataDaftarPoliRJ['taskIdPelayanan']['taskId6']) {
            $waktu = Carbon::createFromFormat('d/m/Y H:i:s', $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId6'], env('APP_TIMEZONE'))->timestamp * 1000; //waktu dalam timestamp milisecond
            $this->pushDataTaskId($noBooking, 6, $waktu);
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("waktu Masuk Apotek kosong tidak dapat dikirim");
        }
    }

    public function keluarApotek($rjNo)
    {
        $this->findData($rjNo);
        $keluarApotek = Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s');

        //////updateDB/////////////////////
        $sql = "select waktu_selesai_pelayanan from rstxn_rjhdrs where rj_no=:rjNo";
        $cek_waktu_selesai_pelayanan = DB::scalar($sql, ['rjNo' => $rjNo]);


        // ketika cek_waktu_selesai_pelayanan kosong lalu update
        if (!$cek_waktu_selesai_pelayanan) {
            DB::table('rstxn_rjhdrs')
                ->where('rj_no', $rjNo)
                ->update([
                    'waktu_selesai_pelayanan' => DB::raw("to_date('" . $keluarApotek . "','dd/mm/yyyy hh24:mi:ss')"), //waktu masuk = rjdate
                ]);
        }
        //////////////////////////////////


        // add antrian Apotek

        // update no antrian Apotek

        // cek
        if (!$this->dataDaftarPoliRJ['taskIdPelayanan']['taskId6']) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Anda tidak dapat melakukan taskId7 ketika taskId6 Kosong");
            return;
        }

        // update taskId7
        if (!$this->dataDaftarPoliRJ['taskIdPelayanan']['taskId7']) {
            $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId7'] = $keluarApotek;
            // update DB
            $this->updateDataRJ($rjNo);

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("keluar Apotek " . $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId7']);
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("keluar Apotek " . $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId7']);
        }

        // cari no Booking
        $noBooking =  $this->dataDaftarPoliRJ['noBooking'];

        if ($this->dataDaftarPoliRJ['taskIdPelayanan']['taskId7']) {
            $waktu = Carbon::createFromFormat('d/m/Y H:i:s', $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId7'], env('APP_TIMEZONE'))->timestamp * 1000; //waktu dalam timestamp milisecond
            $this->pushDataTaskId($noBooking, 7, $waktu);
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("waktu Keluar Apotek kosong tidak dapat dikirim");
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
