<?php

namespace App\Http\Traits\LOV\LOVRoom;


use Illuminate\Support\Facades\DB;

trait LOVRoomTrait
{

    public array $dataRoomLov = [];
    public int $dataRoomLovStatus = 0;
    public string $dataRoomLovSearch = '';
    public int $selecteddataRoomLovIndex = 0;
    public array $collectingMyRoom = [];

    /////////////////////////////////////////////////
    // Lov dataRoomLov //////////////////////
    ////////////////////////////////////////////////

    public function updateddataRoomLovsearch()
    {

        // Reset index of LoV
        $this->reset(['selecteddataRoomLovIndex', 'dataRoomLov']);
        // Variable Search
        $search = $this->dataRoomLovSearch;

        // check LOV by room_id rs id
        $dataRoomLovs = DB::table('rsview_roomrses ')
            ->select(
                'room_id',
                'room_name',
                'bed_no',
                // 'room_price',
                // 'perawatan_price',
                // 'common_service',
            )
            ->where('room_id', '=', $search)
            // ->where('active_status', '1')
            ->first();

        if ($dataRoomLovs) {

            // set Room sep
            $this->addRoom($dataRoomLovs->room_id, $dataRoomLovs->room_name, $dataRoomLovs->bed_no, 0, 0, 0);
            $this->resetdataRoomLov();
        } else {

            // if there is no id found and check (min 1 char on search)
            if (strlen($search) < 1) {
                $this->dataRoomLov = [];
            } else {
                $this->dataRoomLov = json_decode(
                    DB::table('rsview_roomrses')
                        ->select(
                            'room_id',
                            'room_name',
                            'bed_no',
                            // 'room_price',
                            // 'perawatan_price',
                            // 'common_service'
                        )
                        // ->where('active_status', '1')
                        ->Where(DB::raw('upper(room_name)'), 'like', '%' . strtoupper($search) . '%')
                        ->orWhere(DB::raw('upper(room_id)'), 'like', '%' . strtoupper($search) . '%')
                        ->limit(10)
                        ->orderBy('room_name', 'ASC')
                        ->orderBy('bed_no', 'ASC')
                        ->orderBy('room_id', 'ASC')
                        ->get(),
                    true
                );
            }
            $this->dataRoomLovStatus = true;
            // set doing nothing
        }
    }
    // /////////////////////
    // LOV selected start
    public function setMydataRoomLov($id)
    {
        // $this->checkRjStatus();
        $dataRoomLovs = DB::table('rsview_roomrses')
            ->select(
                'room_id',
                'room_name',
                'bed_no',
                // 'room_price',
                // 'perawatan_price',
                // 'common_service'
            )
            ->where('room_id', $this->dataRoomLov[$id]['room_id'])
            ->first();

        // set dokter sep
        $this->addRoom($dataRoomLovs->room_id, $dataRoomLovs->room_name, $dataRoomLovs->bed_no, 0, 0, 0);
        $this->resetdataRoomLov();
    }

    public function resetdataRoomLov()
    {
        $this->reset(['dataRoomLov', 'dataRoomLovStatus', 'dataRoomLovSearch', 'selecteddataRoomLovIndex']);
    }

    public function selectNextdataRoomLov()
    {
        if ($this->selecteddataRoomLovIndex === "") {
            $this->selecteddataRoomLovIndex = 0;
        } else {
            $this->selecteddataRoomLovIndex++;
        }

        if ($this->selecteddataRoomLovIndex === count($this->dataRoomLov)) {
            $this->selecteddataRoomLovIndex = 0;
        }
    }

    public function selectPreviousdataRoomLov()
    {

        if ($this->selecteddataRoomLovIndex === "") {
            $this->selecteddataRoomLovIndex = count($this->dataRoomLov) - 1;
        } else {
            $this->selecteddataRoomLovIndex--;
        }

        if ($this->selecteddataRoomLovIndex === -1) {
            $this->selecteddataRoomLovIndex = count($this->dataRoomLov) - 1;
        }
    }

    public function enterMydataRoomLov($id)
    {
        // dd($this->dataRoomLov);
        // $this->checkRjStatus();
        // jika JK belum siap maka toaster error
        if (isset($this->dataRoomLov[$id]['room_id'])) {
            $this->addRoom(
                $this->dataRoomLov[$id]['room_id'],          // room_id
                $this->dataRoomLov[$id]['room_name'],        // room_name
                $this->dataRoomLov[$id]['bed_no'] ?? null,   // bed_no (default null jika tidak ada)
                $this->dataRoomLov[$id]['room_price'] ?? 0,       // room_price
                $this->dataRoomLov[$id]['perawatan_price'] ?? 0,  // perawatan_price
                $this->dataRoomLov[$id]['common_service'] ?? 0    // common_service
            );
            $this->resetdataRoomLov();
        } else {
            $this->emit('toastr-error', "Kode belum tersedia.");
            return;
        }
    }


    private function addRoom($RoomId, $RoomName, $BedNo, $RoomPrice, $PerawatanPrice, $CommonService): void
    {
        $this->collectingMyRoom = [
            'RoomId'         => $RoomId,
            'RoomName'       => $RoomName,
            'RoomBedNo'          => $BedNo,
            'RoomPrice'      => $RoomPrice,
            'PerawatanPrice' => $PerawatanPrice,
            'CommonService'  => $CommonService,
        ];
    }



    // LOV selected end
    /////////////////////////////////////////////////
    // Lov dataRoomLov //////////////////////
    ////////////////////////////////////////////////
}
