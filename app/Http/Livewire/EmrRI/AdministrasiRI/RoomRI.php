<?php

namespace App\Http\Livewire\EmrRI\AdministrasiRI;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use App\Http\Traits\BPJS\AplicaresTrait;

use App\Http\Traits\EmrRI\EmrRITrait;
use App\Http\Traits\LOV\LOVRoom\LOVRoomTrait;

use Exception;


class RoomRI extends Component
{
    use WithPagination, EmrRITrait, LOVRoomTrait, AplicaresTrait;


    // listener from blade////////////////
    protected $listeners = ['SyncAdministrasiRI' => 'mount'];


    //////////////////////////////
    // Ref on top bar
    //////////////////////////////
    public $riHdrNoRef;
    public array $dataDaftarRi = [];

    // LOV Nested
    public array $room;
    // LOV Nested

    //////////////////////////////////////////////////////////////////////
    public array $formEntryRoom = [
        'roomStartDate'    => '', // Format tanggal wajib: dd/mm/yyyy hh24:mi:ss
        'roomId'           => '',
        'roomBedNo'        => '',
        'roomPrice'        => '', // Harga kunjungan minimal 0
        'perawatanPrice'   => '', // Harga perawatan minimal 0
        'commonService'    => '', // Harga common minimal 0
        'roomDay'          => '', // Jumlah minimal 1
    ];
    public array $dataRoom = [];
    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////

    public function insertRoom(): void
    {
        // validate
        $this->checkRiStatus();

        $rules = [
            'formEntryRoom.roomStartDate' => 'required|date_format:d/m/Y H:i:s', // Format tanggal wajib: dd/mm/yyyy hh24:mi:ss
            'formEntryRoom.roomId' => 'required|exists:rsmst_rooms,room_id', // ID room harus ada di tabel rsmst_rooms kolom room_id
            'formEntryRoom.roomBedNo' => 'required', // ID room harus ada di tabel rsmst_rooms kolom room_id
            'formEntryRoom.roomPrice' => 'required|numeric|min:0', // Harga kunjungan minimal 0
            'formEntryRoom.perawatanPrice' => 'required|numeric|min:0', // Harga kunjungan minimal 0
            'formEntryRoom.commonService' => 'required|numeric|min:0', // Harga kunjungan minimal 0
            'formEntryRoom.roomDay' => 'required|numeric|min:1', // Jumlah minimal 1
        ];

        $messages = [
            'formEntryRoom.roomStartDate.required'  => 'Tanggal mulai room wajib diisi.',
            'formEntryRoom.roomStartDate.date_format' => 'Format tanggal mulai room harus dd/mm/yyyy hh24:mi:ss.',
            'formEntryRoom.roomId.required'           => 'ID room wajib diisi.',
            'formEntryRoom.roomId.exists'             => 'ID room tidak valid atau tidak ditemukan.',
            'formEntryRoom.roomBedNo.required'        => 'Nomor tempat tidur room wajib diisi.',
            'formEntryRoom.roomPrice.required'        => 'Harga room wajib diisi.',
            'formEntryRoom.roomPrice.numeric'         => 'Harga room harus berupa angka.',
            'formEntryRoom.roomPrice.min'             => 'Harga room minimal 0.',
            'formEntryRoom.perawatanPrice.required'   => 'Harga perawatan wajib diisi.',
            'formEntryRoom.perawatanPrice.numeric'    => 'Harga perawatan harus berupa angka.',
            'formEntryRoom.perawatanPrice.min'        => 'Harga perawatan minimal 0.',
            'formEntryRoom.commonService.required'      => 'Harga common service wajib diisi.',
            'formEntryRoom.commonService.numeric'       => 'Harga common service harus berupa angka.',
            'formEntryRoom.commonService.min'           => 'Harga common service minimal 0.',
            'formEntryRoom.roomDay.required'          => 'Jumlah hari room wajib diisi.',
            'formEntryRoom.roomDay.numeric'           => 'Jumlah hari room harus berupa angka.',
            'formEntryRoom.roomDay.min'               => 'Jumlah hari room minimal 1.',
        ];

        // Proses validasi
        try {
            $this->validate($rules, $messages);
        } catch (\Illuminate\Validation\ValidationException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Periksa kembali input data. " . $e->getMessage());
            $this->validate($rules, $messages);
        }



        // start:
        try {


            $lastInsertedFromRI = DB::table('rsmst_trfrooms')
                ->select(DB::raw("nvl(max(trfr_no),1) as trfr_no_max"))
                ->where('rihdr_no', '=', $this->riHdrNoRef)
                ->first();

            if (!empty($lastInsertedFromRI->trfr_no_max)) {

                $longDay = DB::table('rsmst_trfrooms')
                    ->select(DB::raw("ROUND(nvl(day, nvl(end_date,sysdate+1)-nvl(start_date,sysdate))) as day"))
                    ->where('rihdr_no', '=', $this->riHdrNoRef)
                    ->where('trfr_no', '=', $lastInsertedFromRI->trfr_no_max)
                    ->first();

                // update into table transaksi
                DB::table('rsmst_trfrooms')
                    ->where('rihdr_no', '=', $this->riHdrNoRef)
                    ->where('trfr_no', '=', $lastInsertedFromRI->trfr_no_max)
                    ->update([
                        'end_date' => DB::raw("to_date('" . $this->formEntryRoom['roomStartDate'] . "','dd/mm/yyyy hh24:mi:ss')"),
                        'day' =>  $longDay->day ?? 1,
                    ]);
            }

            $lastInserted = DB::table('rsmst_trfrooms')
                ->select(DB::raw("nvl(max(trfr_no)+1,1) as trfr_no_max"))
                ->first();


            // insert into table transaksi
            DB::table('rsmst_trfrooms')
                ->insert([
                    'start_date' => DB::raw("to_date('" . $this->formEntryRoom['roomStartDate'] . "','dd/mm/yyyy hh24:mi:ss')"),
                    'room_id' =>  $this->formEntryRoom['roomId'],
                    'bed_no' => $this->formEntryRoom['roomBedNo'],
                    'room_price' =>  $this->formEntryRoom['roomPrice'],
                    'perawatan_price' =>  $this->formEntryRoom['perawatanPrice'],
                    'common_service' =>  $this->formEntryRoom['commonService'],
                    'rihdr_no' =>  $this->riHdrNoRef,
                    'trfr_no' =>  $lastInserted->trfr_no_max,
                ]);

            // update into table transaksi
            DB::table('rstxn_rihdrs')
                ->where('rihdr_no', '=', $this->riHdrNoRef)
                ->update([
                    'room_id' => $this->formEntryRoom['roomId'],
                    'bed_no' =>  $this->formEntryRoom['roomBedNo'],
                ]);

            $this->updateKelas($this->formEntryRoom['roomId']);

            $this->administrasiRIuserLog($this->riHdrNoRef, 'Room ' . $this->formEntryRoom['roomName'] . ' Tarif Room/Pereawatan/Umum:' . $this->formEntryRoom['roomPrice'] . '/' . $this->formEntryRoom['perawatanPrice'] . '/' . $this->formEntryRoom['commonService'] . ' Txn No:' . $lastInserted->trfr_no_max);
            $this->emit('SyncAdministrasiRI');
            $this->resetformEntryRoom();
            //
        } catch (Exception $e) {
            // display an error to user
            dd($e->getMessage());
        }
        // goto start;
    }

    public function removeRoom($trfRoomNo)
    {
        $this->checkRiStatus();
        // pengganti race condition
        // start:
        try {

            // remove into table transaksi
            DB::table('rsmst_trfrooms')
                ->where('trfr_no', $trfRoomNo)
                ->delete();
            //
            $this->administrasiRIuserLog($this->riHdrNoRef, 'Room Remove Txn No:' . $trfRoomNo);
            $this->emit('SyncAdministrasiRI');

            $this->resetformEntryRoom();
        } catch (Exception $e) {
            // display an error to user
            dd($e->getMessage());
        }
        // goto start;


    }

    public function setRoomStartDate($date)
    {
        $this->formEntryRoom['roomStartDate'] = $date;
    }

    public function resetformEntryRoom()
    {
        $this->reset([
            'formEntryRoom',
            'collectingMyRoom' //Reset LOV / render  / empty NestLov
        ]);
        $this->resetValidation();
    }

    public function checkRiStatus()
    {
        $lastInserted = DB::table('rstxn_rihdrs')
            ->select('ri_status')
            ->where('rihdr_no', $this->riHdrNoRef)
            ->first();

        if ($lastInserted->ri_status !== 'I') {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Pasien Sudah Pulang, Trasaksi Terkunci.");
            return (dd('Pasien Sudah Pulang, Trasaksi Terkuncixx.' . $this->riHdrNoRef));
        }
    }

    private function syncDataPrimer(): void
    {
        // sync data primer untuk LOV
        // Jika data RoomId ada
        if ($this->formEntryRoom['roomId']) {
            $this->addRoom($this->formEntryRoom['roomId'] ?? '', $this->formEntryRoom['roomName'] ?? '', $this->formEntryRoom['roomBedNo'] ?? '', $this->formEntryRoom['roomPrice'] ?? '', $this->formEntryRoom['perawatanPrice'] ?? '', $this->formEntryRoom['commonService'] ?? '');
        }
    }

    private function updateKelas($roomId): void
    {
        $kodekelasRs = DB::table('rsmst_rooms')->select('class_id')->where('room_id', $roomId)->first();
        // Daftar kodekelas dari data kamarLists

        if ($kodekelasRs) {
            $kapasitas = DB::table('rsmst_rooms as a')
                ->where(DB::raw("to_char(a.class_id)"), $kodekelasRs->class_id ?? '')
                ->count();

            $terpakai = DB::table('rsmst_rooms as a')
                ->join('rstxn_rihdrs as b', 'a.room_id', '=', 'b.room_id')
                ->where('b.ri_status', 'I')
                ->where(DB::raw("to_char(a.class_id)"), $kodekelasRs->class_id ?? '')
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


    // when new form instance
    public function mount()
    {
        $this->findData($this->riHdrNoRef);
    }

    private function findData($riHdrNo): void
    {
        $riRoom =  DB::table('rsmst_trfrooms')
            ->join('rsmst_rooms', 'rsmst_trfrooms.room_id', '=', 'rsmst_rooms.room_id')
            ->select(
                'start_date',
                'end_date',
                'rsmst_trfrooms.room_id',
                'rsmst_rooms.room_name',
                'rsmst_trfrooms.bed_no',
                'rsmst_trfrooms.room_price',
                'rsmst_trfrooms.perawatan_price',
                'rsmst_trfrooms.common_service',
                DB::raw("ROUND(nvl(day, nvl(end_date,sysdate+1)-nvl(start_date,sysdate))) as day"),
                'rihdr_no',
                'trfr_no'
            )
            ->where('rihdr_no', $riHdrNo)
            ->get();
        $this->dataRoom['riRoom'] = json_decode(json_encode($riRoom, true), true);

        $this->syncDataPrimer();
    }

    private function administrasiRIuserLog($riHdrNo, $logs): void
    {
        $this->dataDaftarRi = $this->findDataRI($riHdrNo);

        $this->dataDaftarRi['AdministrasiRI']['userLogs'][] =
            [
                'userLogDesc' => $logs,
                'userLog' => auth()->user()->myuser_name,
                'userLogDate' => Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s')
            ];
        $this->updateJsonRI($riHdrNo, $this->dataDaftarRi);
        $this->reset(['dataDaftarRi']);
    }

    private function syncDataFormEntry(): void
    {
        // Synk Lov Room
        $this->formEntryRoom['roomId'] = $this->room['RoomId'] ?? '';
        $this->formEntryRoom['roomName'] = $this->room['RoomName'] ?? '';
        $this->formEntryRoom['roomBedNo'] = $this->room['RoomBedNo'] ?? '';

        // $this->formEntryRoom['roomPrice'] = $this->room['RoomPrice'] ?? '';
        // $this->formEntryRoom['perawatanPrice'] = $this->room['PerawatanPrice'] ?? '';
        // $this->formEntryRoom['commonService'] = $this->room['CommonService'] ?? '';


        //qty
        if (!isset($this->formEntryRoom['roomDay']) || empty($this->formEntryRoom['roomDay'])) {
            $this->formEntryRoom['roomDay'] = 1;
        }

        //price
        if (
            !isset($this->formEntryRoom['roomPrice']) || empty($this->formEntryRoom['roomPrice']) ||
            !isset($this->formEntryRoom['perawatanPrice']) || empty($this->formEntryRoom['perawatanPrice']) ||
            !isset($this->formEntryRoom['commonService']) || empty($this->formEntryRoom['commonService'])
        ) {
            // Ambil semua harga terkait dari tabel rsmst_rooms dalam satu query
            $prices = DB::table('rsmst_rooms')
                ->where('room_id', $this->room['RoomId'] ?? '')
                ->select('room_price', 'perawatan_price', 'common_service')
                ->first();

            // Set nilai default jika tidak ditemukan
            $this->formEntryRoom['roomPrice'] = $prices->room_price ?? 0;
            $this->formEntryRoom['perawatanPrice'] = $prices->perawatan_price ?? 0;
            $this->formEntryRoom['commonService'] = $prices->common_service ?? 0;
        }
    }

    private function syncLOV(): void
    {
        $this->room = $this->collectingMyRoom;
    }


    // select data start////////////////
    public function render()
    {

        // LOV
        $this->syncLOV();
        // FormEntry
        $this->syncDataFormEntry();


        return view(
            'livewire.emr-r-i.administrasi-r-i.room-r-i',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Data Pasien Inap',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Room',
            ]
        );
    }
    // select data end////////////////


}
