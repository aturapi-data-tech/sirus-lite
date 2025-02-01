<?php

namespace App\Http\Traits\LOV\LOVJasaMedis;


use Illuminate\Support\Facades\DB;

trait LOVJasaMedisTrait
{

    public array $dataJasaMedisLov = [];
    public int $dataJasaMedisLovStatus = 0;
    public string $dataJasaMedisLovSearch = '';
    public int $selecteddataJasaMedisLovIndex = 0;
    public array $collectingMyJasaMedis = [];

    /////////////////////////////////////////////////
    // Lov dataJasaMedisLov //////////////////////
    ////////////////////////////////////////////////

    public function updateddataJasaMedisLovsearch()
    {

        // Reset index of LoV
        $this->reset(['selecteddataJasaMedisLovIndex', 'dataJasaMedisLov']);
        // Variable Search
        $search = $this->dataJasaMedisLovSearch;

        // check LOV by pact_id rs id
        $dataJasaMedisLovs = DB::table('rsmst_actparamedics ')
            ->select(
                'pact_id',
                'pact_desc',
                'pact_price',
            )
            ->where('pact_id', '=', $search)
            // ->where('active_status', '1')
            ->first();

        if ($dataJasaMedisLovs) {

            // set JasaMedis sep
            $this->addJasaMedis($dataJasaMedisLovs->pact_id, $dataJasaMedisLovs->pact_desc, $dataJasaMedisLovs->pact_price);;
            $this->resetdataJasaMedisLov();
        } else {

            // if there is no id found and check (min 1 char on search)
            if (strlen($search) < 1) {
                $this->dataJasaMedisLov = [];
            } else {
                $this->dataJasaMedisLov = json_decode(
                    DB::table('rsmst_actparamedics')
                        ->select(
                            'pact_id',
                            'pact_desc',
                            'pact_price',
                        )
                        // ->where('active_status', '1')
                        ->Where(DB::raw('upper(pact_desc)'), 'like', '%' . strtoupper($search) . '%')
                        ->orWhere(DB::raw('upper(pact_id)'), 'like', '%' . strtoupper($search) . '%')
                        ->limit(10)
                        ->orderBy('pact_desc', 'ASC')
                        ->orderBy('pact_id', 'ASC')
                        ->get(),
                    true
                );
            }
            $this->dataJasaMedisLovStatus = true;
            // set doing nothing
        }
    }
    // /////////////////////
    // LOV selected start
    public function setMydataJasaMedisLov($id)
    {
        // $this->checkRjStatus();
        $dataJasaMedisLovs = DB::table('rsmst_actparamedics')
            ->select(
                'pact_id',
                'pact_desc',
                'pact_price',
            )
            ->where('pact_id', $this->dataJasaMedisLov[$id]['pact_id'])
            ->first();

        // set dokter sep
        $this->addJasaMedis($dataJasaMedisLovs->pact_id, $dataJasaMedisLovs->pact_desc, $dataJasaMedisLovs->pact_price);
        $this->resetdataJasaMedisLov();
    }

    public function resetdataJasaMedisLov()
    {
        $this->reset(['dataJasaMedisLov', 'dataJasaMedisLovStatus', 'dataJasaMedisLovSearch', 'selecteddataJasaMedisLovIndex']);
    }

    public function selectNextdataJasaMedisLov()
    {
        if ($this->selecteddataJasaMedisLovIndex === "") {
            $this->selecteddataJasaMedisLovIndex = 0;
        } else {
            $this->selecteddataJasaMedisLovIndex++;
        }

        if ($this->selecteddataJasaMedisLovIndex === count($this->dataJasaMedisLov)) {
            $this->selecteddataJasaMedisLovIndex = 0;
        }
    }

    public function selectPreviousdataJasaMedisLov()
    {

        if ($this->selecteddataJasaMedisLovIndex === "") {
            $this->selecteddataJasaMedisLovIndex = count($this->dataJasaMedisLov) - 1;
        } else {
            $this->selecteddataJasaMedisLovIndex--;
        }

        if ($this->selecteddataJasaMedisLovIndex === -1) {
            $this->selecteddataJasaMedisLovIndex = count($this->dataJasaMedisLov) - 1;
        }
    }

    public function enterMydataJasaMedisLov($id)
    {
        // dd($this->dataJasaMedisLov);
        // $this->checkRjStatus();
        // jika JK belum siap maka toaster error
        if (isset($this->dataJasaMedisLov[$id]['pact_id'])) {
            $this->addJasaMedis($this->dataJasaMedisLov[$id]['pact_id'], $this->dataJasaMedisLov[$id]['pact_desc'], $this->dataJasaMedisLov[$id]['pact_price']);
            $this->resetdataJasaMedisLov();
        } else {
            $this->emit('toastr-error', "Kode belum tersedia.");
            return;
        }
    }


    private function addJasaMedis($JasaMedisId, $JasaMedisDesc, $JasaMedisPrice): void
    {
        $this->collectingMyJasaMedis = [
            'JasaMedisId' => $JasaMedisId,
            'JasaMedisDesc' => $JasaMedisDesc,
            'JasaMedisPrice' => $JasaMedisPrice,
        ];
    }


    // LOV selected end
    /////////////////////////////////////////////////
    // Lov dataJasaMedisLov //////////////////////
    ////////////////////////////////////////////////
}
