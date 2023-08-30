<?php

namespace App\Http\Livewire\PelayananRJ;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

use App\Http\Traits\BPJS\AntrianTrait;


use Spatie\ArrayToXml\ArrayToXml;


class PelayananRJ extends Component
{
    use WithPagination;



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



    // 



    // search logic -resetExcept////////////////
    public $search;
    protected $queryString = [
        'search' => ['except' => '', 'as' => 'cariData'],
        'page' => ['except' => 1, 'as' => 'p'],
    ];


    // sort logic -resetExcept////////////////
    public $sortField = 'reg_no';
    public $sortAsc = true;


    // listener from blade////////////////
    protected $listeners = [
        'confirm_batal_poli_taskId' => 'batalPoli',
    ];



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
        // $this->emit('toastr-error', "search.");

        $this->resetPage();
        $this->resetValidation();
        $this->resetInputFields();
    }
    // date
    public function updatedDaterjref(): void
    {
        // $this->emit('toastr-error', "date.");

        $this->resetPage();
        $this->resetValidation();
        $this->resetInputFields();
    }
    // status
    public function updatedStatusrjref(): void
    {
        // $this->emit('toastr-error', "status.");

        $this->resetPage();
        $this->resetValidation();
        $this->resetInputFields();
    }
    // dr
    public function setdrRjRef($id, $name): void
    {
        // $this->emit('toastr-error', "dr.");

        $this->drRjRef['drId'] = $id;
        $this->drRjRef['drName'] = $name;
        $this->resetPage();
        $this->resetValidation();
        $this->resetInputFields();
    }
    // shift
    public function setShift($id, $desc): void
    {
        // $this->emit('toastr-error', "shift.");

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

        $this->emit('toastr-success', "Json Berhasil di update.");
    }

    private function findData($rjNo): void
    {


        $findData = DB::table('rsview_rjkasir')
            ->select('datadaftarpolirj_json', 'vno_sep', DB::raw("to_char(rj_date,'dd/mm/yyyy hh24:mi:ss') AS rj_date"))
            ->where('rj_no', $rjNo)
            ->first();


        if ($findData->datadaftarpolirj_json) {
            $this->dataDaftarPoliRJ = json_decode($findData->datadaftarpolirj_json, true);

            // jika taskId3 tidak ditemukan tambah variable kontrol pda array
            if (isset($this->dataDaftarPoliRJ['taskIdPelayanan']) == false) {
                $this->dataDaftarPoliRJ['taskIdPelayanan'] = $this->taskIdPelayanan;
                // update DB
                $this->updateDataRJ($rjNo);
            }

            $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId3'] = $findData->rj_date;
        } else {

            $this->emit('toastr-error', "Json Tidak ditemukan, Data sedang diproses ulang.");
            $dataDaftarPoliRJ = DB::table('rsview_rjkasir')
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

                    'nobooking',
                    'push_antrian_bpjs_status',
                    'push_antrian_bpjs_json',
                    'kd_dr_bpjs',
                    'kd_poli_bpjs',
                    'rj_status',
                    'txn_status',
                    'erm_status',
                )
                ->where('rj_no', '=', $rjNo)
                ->first();

            $this->dataDaftarPoliRJ = [
                "regNo" => "" . $dataDaftarPoliRJ->reg_no . "",

                "drId" => "" . $dataDaftarPoliRJ->dr_id . "",
                "drDesc" => "" . $dataDaftarPoliRJ->dr_name . "",

                "poliId" => "" . $dataDaftarPoliRJ->poli_id . "",
                "poliDesc" => "" . $dataDaftarPoliRJ->poli_desc . "",

                "kddrbpjs" => "" . $dataDaftarPoliRJ->kd_dr_bpjs . "",
                "kdpolibpjs" => "" . $dataDaftarPoliRJ->kd_poli_bpjs . "",

                "rjDate" => "" . $dataDaftarPoliRJ->rj_date . "",
                "rjNo" => "" . $dataDaftarPoliRJ->rj_no . "",
                "shift" => "" . $dataDaftarPoliRJ->shift . "",
                "noAntrian" => "" . $dataDaftarPoliRJ->no_antrian . "",
                "noBooking" => "" . $dataDaftarPoliRJ->nobooking . "",
                "slCodeFrom" => "02",
                "passStatus" => "",
                "rjStatus" => "" . $dataDaftarPoliRJ->rj_status . "",
                "txnStatus" => "" . $dataDaftarPoliRJ->txn_status . "",
                "ermStatus" => "" . $dataDaftarPoliRJ->erm_status . "",
                "cekLab" => "0",
                "kunjunganInternalStatus" => "0",
                "noReferensi" => "" . $dataDaftarPoliRJ->reg_no . "",
                "taskIdPelayanan" => [
                    "taskId1" => "",
                    "taskId2" => "",
                    "taskId3" => "" . $dataDaftarPoliRJ->rj_date . "",
                    "taskId4" => "",
                    "taskId5" => "",
                    "taskId6" => "",
                    "taskId7" => "",
                    "taskId99" => "",
                ],
                'sep' => [
                    "noSep" => "" . $dataDaftarPoliRJ->vno_sep . "",
                    "reqSep" => [],
                    "resSep" => [],
                ]
            ];

            $this->dataDaftarPoliRJ['klaimId'] = $dataDaftarPoliRJ->klaim_id == 'JM' ? 'JM' : 'UM';
            $this->dataDaftarPoliRJ['JenisKlaimDesc'] = $dataDaftarPoliRJ->klaim_id == 'JM' ? 'BPJS' : 'UMUM';

            $this->dataDaftarPoliRJ['kunjunganId'] = '1';
            $this->dataDaftarPoliRJ['JenisKunjunganDesc'] = 'Rujukan FKTP';

            // jika kontrol tidak ditemukan tambah variable kontrol pda array
            // jika taskId3 tidak ditemukan tambah variable kontrol pda array
            if (isset($this->dataDaftarPoliRJ['taskIdPelayanan']) == false) {
                $this->dataDaftarPoliRJ['taskIdPelayanan'] = $this->taskIdPelayanan;
            }

            $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId3'] = $findData->rj_date;
            // update DB
            $this->updateDataRJ($rjNo);
        }
    }

    public function masukPoli($rjNo)
    {

        $this->findData($rjNo);

        $sql = "select waktu_masuk_poli from rstxn_rjhdrs where rj_no=:rjNo";
        $cek_waktu_masuk_poli = DB::scalar($sql, ['rjNo' => $rjNo]);

        $waktuMasukPoli = Carbon::now()->format('d/m/Y H:i:s');

        // ketika cek_waktu_masuk_poli kosong lalu update
        if (!$cek_waktu_masuk_poli) {
            DB::table('rstxn_rjhdrs')
                ->where('rj_no', $rjNo)
                ->update([
                    'waktu_masuk_poli' => DB::raw("to_date('" . $waktuMasukPoli . "','dd/mm/yyyy hh24:mi:ss')"), //waktu masuk = rjdate
                ]);
        }

        /////////////////////////
        // Update TaskId 4
        /////////////////////////

        $waktu = Carbon::createFromFormat('d/m/Y H:i:s', $waktuMasukPoli)->timestamp * 1000; //waktu dalam timestamp milisecond
        // DB Json
        $sql = "select datadaftarpolirj_json from rstxn_rjhdrs where rj_no=:rjNo";
        $datadaftarpolirj_json = DB::scalar($sql, ['rjNo' => $rjNo]);

        if ($datadaftarpolirj_json) {
            $this->dataDaftarPoliRJ = json_decode($datadaftarpolirj_json, true);

            if (!$this->dataDaftarPoliRJ['taskIdPelayanan']['taskId4']) {
                $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId4'] = $waktuMasukPoli;
                // update DB
                $this->updateDataRJ($rjNo);

                $this->emit('toastr-success', "Masuk Poli " . $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId4']);
            } else {
                $this->emit('toastr-error', "Masuk Poli " . $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId4']);
            }
        }

        // cari no Booking
        $sql = "select nobooking from rstxn_rjhdrs where rj_no=:rjNo";
        $noBooking =  DB::scalar($sql, ['rjNo' => $rjNo]);

        $this->pushDataTaskId($noBooking, 4, $waktu);
    }


    public function keluarPoli($rjNo)
    {
        $sql = "select waktu_masuk_poli from rstxn_rjhdrs where rj_no=:rjNo";
        $cek_waktu_masuk_poli = DB::scalar($sql, ['rjNo' => $rjNo]);

        // Jika waktu masuk poli sudah terisi jalankan keluarPoli jika belum send err message
        if ($cek_waktu_masuk_poli) {

            $sql = "select waktu_masuk_apt from rstxn_rjhdrs where rj_no=:rjNo";
            $cek_waktu_masuk_apt = DB::scalar($sql, ['rjNo' => $rjNo]);

            $waktuMasukApotek = Carbon::now()->format('d/m/Y H:i:s');

            // ketika cek_waktu_masuk_apt kosong lalu update
            if (!$cek_waktu_masuk_apt) {
                DB::table('rstxn_rjhdrs')
                    ->where('rj_no', $rjNo)
                    ->update([
                        'waktu_masuk_apt' => DB::raw("to_date('" . $waktuMasukApotek . "','dd/mm/yyyy hh24:mi:ss')"), //waktu masuk = rjdate
                    ]);
            }

            /////////////////////////
            // Update TaskId 5
            /////////////////////////

            $waktu = Carbon::createFromFormat('d/m/Y H:i:s', $waktuMasukApotek)->timestamp * 1000; //waktu dalam timestamp milisecond
            // DB Json
            $sql = "select datadaftarpolirj_json from rstxn_rjhdrs where rj_no=:rjNo";
            $datadaftarpolirj_json = DB::scalar($sql, ['rjNo' => $rjNo]);
            if ($datadaftarpolirj_json) {
                $this->dataDaftarPoliRJ = json_decode($datadaftarpolirj_json, true);

                if (!$this->dataDaftarPoliRJ['taskIdPelayanan']['taskId5']) {
                    $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId5'] = $waktuMasukApotek;

                    // update DB
                    $this->updateDataRJ($rjNo);

                    $this->emit('toastr-success', "Keluar Poli " . $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId5']);
                } else {
                    $this->emit('toastr-error', "Keluar Poli " . $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId5']);
                }

                // cari no Booking
                $sql = "select nobooking from rstxn_rjhdrs where rj_no=:rjNo";
                $noBooking =  DB::scalar($sql, ['rjNo' => $rjNo]);

                $this->pushDataTaskId($noBooking, 5, $waktu);

                $this->emit('toastr-success', "Keluar Poli $waktuMasukApotek");
            } else {
                $this->emit('toastr-error', "Satus Pasien Belum melalui pelayanan Poli");
            }
        }
    }


    public function batalPoli($rjNo, $regName): void
    {

        $waktuBatalPoli = Carbon::now()->format('d/m/Y H:i:s');

        /////////////////////////
        // Update TaskId 99 jika task id 5 sudah terisi maka tidak dapat dilakukan pembatalan
        /////////////////////////

        $waktu = Carbon::createFromFormat('d/m/Y H:i:s', $waktuBatalPoli)->timestamp * 1000; //waktu dalam timestamp milisecond
        // DB Json
        $sql = "select datadaftarpolirj_json from rstxn_rjhdrs where rj_no=:rjNo";
        $datadaftarpolirj_json = DB::scalar($sql, ['rjNo' => $rjNo]);
        if ($datadaftarpolirj_json) {
            if (!$this->dataDaftarPoliRJ['taskIdPelayanan']['taskId5']) {
                $this->dataDaftarPoliRJ = json_decode($datadaftarpolirj_json, true);
                $this->dataDaftarPoliRJ['taskIdPelayanan']['taskId99'] = $waktuBatalPoli;
                // update DB
                $this->updateDataRJ($rjNo);
            } else {
                $this->emit('toastr-error', "Pembatalan tidak dapat dilakukan, " . $regName . " sudak melakukan pelayanan Poli.");
            }
        }

        // cari no Booking
        $sql = "select nobooking from rstxn_rjhdrs where rj_no=:rjNo";
        $noBooking =  DB::scalar($sql, ['rjNo' => $rjNo]);

        $this->pushDataTaskId($noBooking, 99, $waktu);


        $this->emit('toastr-success', "Pembatalan " . $regName . " pelayanan Poli berhasil dilakukan.");
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
            $this->emit('toastr-success', 'Task Id' . $taskId . ' ' . $HttpGetBpjs['metadata']['code'] . ' ' . $HttpGetBpjs['metadata']['message']);
        } else {
            $this->emit('toastr-error', 'Task Id' . $taskId . ' ' .  $HttpGetBpjs['metadata']['code'] . ' ' . $HttpGetBpjs['metadata']['message']);

            // Ulangi Proses pushTaskId;
            // $this->emit('rePush_Data_TaskId_Confirmation');
        }
    }




    // when new form instance
    public function mount()
    {

        // set date
        $this->dateRjRef = Carbon::now()->format('d/m/Y');
        // set shift
        $findShift = DB::table('rstxn_shiftctls')->select('shift')
            ->whereRaw("'" . Carbon::now()->format('H:i:s') . "' between shift_start and shift_end")
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
            ->where('shift', '=', $this->shiftRjRef['shiftId'])
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
            ->orderBy('dr_name',  'desc')
            ->orderBy('poli_desc',  'desc')
            ->orderBy('no_antrian',  'asc')
            ->orderBy('rj_date1',  'desc');

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
