<?php

namespace App\Http\Traits\LOV\LOVLain;


use Illuminate\Support\Facades\DB;

trait LOVLainTrait
{

    public array $dataLainLov = [];
    public int $dataLainLovStatus = 0;
    public string $dataLainLovSearch = '';
    public int $selecteddataLainLovIndex = 0;
    public array $collectingMyLain = [];

    /////////////////////////////////////////////////
    // Lov dataLainLov //////////////////////
    ////////////////////////////////////////////////

    public function updateddataLainLovsearch()
    {

        // Reset index of LoV
        $this->reset(['selecteddataLainLovIndex', 'dataLainLov']);
        // Variable Search
        $search = $this->dataLainLovSearch;

        // check LOV by other_id rs id
        $dataLainLovs = DB::table('rsmst_others ')
            ->select(
                'other_id',
                'other_desc',
                'other_price',
            )
            ->where('other_id', '=', $search)
            // ->where('active_status', '1')
            ->first();

        if ($dataLainLovs) {

            // set Lain sep
            $this->addLain($dataLainLovs->other_id, $dataLainLovs->other_desc, $dataLainLovs->other_price);;
            $this->resetdataLainLov();
        } else {

            // if there is no id found and check (min 1 char on search)
            if (strlen($search) < 1) {
                $this->dataLainLov = [];
            } else {
                $this->dataLainLov = json_decode(
                    DB::table('rsmst_others')
                        ->select(
                            'other_id',
                            'other_desc',
                            'other_price',
                        )
                        // ->where('active_status', '1')
                        ->Where(DB::raw('upper(other_desc)'), 'like', '%' . strtoupper($search) . '%')
                        ->orWhere(DB::raw('upper(other_id)'), 'like', '%' . strtoupper($search) . '%')
                        ->limit(10)
                        ->orderBy('other_desc', 'ASC')
                        ->orderBy('other_id', 'ASC')
                        ->get(),
                    true
                );
            }
            $this->dataLainLovStatus = true;
            // set doing nothing
        }
    }
    // /////////////////////
    // LOV selected start
    public function setMydataLainLov($id)
    {
        // $this->checkRjStatus();
        $dataLainLovs = DB::table('rsmst_others')
            ->select(
                'other_id',
                'other_desc',
                'other_price',
            )
            ->where('other_id', $this->dataLainLov[$id]['other_id'])
            ->first();

        // set lain sep
        $this->addLain($dataLainLovs->other_id, $dataLainLovs->other_desc, $dataLainLovs->other_price);
        $this->resetdataLainLov();
    }

    public function resetdataLainLov()
    {
        $this->reset(['dataLainLov', 'dataLainLovStatus', 'dataLainLovSearch', 'selecteddataLainLovIndex']);
    }

    public function selectNextdataLainLov()
    {
        if ($this->selecteddataLainLovIndex === "") {
            $this->selecteddataLainLovIndex = 0;
        } else {
            $this->selecteddataLainLovIndex++;
        }

        if ($this->selecteddataLainLovIndex === count($this->dataLainLov)) {
            $this->selecteddataLainLovIndex = 0;
        }
    }

    public function selectPreviousdataLainLov()
    {

        if ($this->selecteddataLainLovIndex === "") {
            $this->selecteddataLainLovIndex = count($this->dataLainLov) - 1;
        } else {
            $this->selecteddataLainLovIndex--;
        }

        if ($this->selecteddataLainLovIndex === -1) {
            $this->selecteddataLainLovIndex = count($this->dataLainLov) - 1;
        }
    }

    public function enterMydataLainLov($id)
    {
        // dd($this->dataLainLov);
        // $this->checkRjStatus();
        // jika JK belum siap maka toaster error
        if (isset($this->dataLainLov[$id]['other_id'])) {
            $this->addLain($this->dataLainLov[$id]['other_id'], $this->dataLainLov[$id]['other_desc'], $this->dataLainLov[$id]['other_price']);
            $this->resetdataLainLov();
        } else {
            $this->emit('toastr-error', "Kode belum tersedia.");
            return;
        }
    }


    private function addLain($LainId, $LainDesc, $LainPrice): void
    {
        $this->collectingMyLain = [
            'LainId' => $LainId,
            'LainDesc' => $LainDesc,
            'LainPrice' => $LainPrice,
        ];
    }


    // LOV selected end
    /////////////////////////////////////////////////
    // Lov dataLainLov //////////////////////
    ////////////////////////////////////////////////
}
