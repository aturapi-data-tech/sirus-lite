<?php

namespace App\Http\Traits\LOV\LOVJasaDokter;


use Illuminate\Support\Facades\DB;

trait LOVJasaDokterTrait
{

    public array $dataJasaDokterLov = [];
    public int $dataJasaDokterLovStatus = 0;
    public string $dataJasaDokterLovSearch = '';
    public int $selecteddataJasaDokterLovIndex = 0;
    public array $collectingMyJasaDokter = [];

    /////////////////////////////////////////////////
    // Lov dataJasaDokterLov //////////////////////
    ////////////////////////////////////////////////

    public function updateddataJasaDokterLovsearch()
    {

        // Reset index of LoV
        $this->reset(['selecteddataJasaDokterLovIndex', 'dataJasaDokterLov']);
        // Variable Search
        $search = $this->dataJasaDokterLovSearch;

        // check LOV by accdoc_id rs id
        $dataJasaDokterLovs = DB::table('rsmst_accdocs ')
            ->select(
                'accdoc_id',
                'accdoc_desc',
                'accdoc_price',
            )
            ->where('accdoc_id', '=', $search)
            // ->where('active_status', '1')
            ->first();

        if ($dataJasaDokterLovs) {

            // set JasaDokter sep
            $this->addJasaDokter($dataJasaDokterLovs->accdoc_id, $dataJasaDokterLovs->accdoc_desc, $dataJasaDokterLovs->accdoc_price);;
            $this->resetdataJasaDokterLov();
        } else {

            // if there is no id found and check (min 1 char on search)
            if (strlen($search) < 1) {
                $this->dataJasaDokterLov = [];
            } else {
                $this->dataJasaDokterLov = json_decode(
                    DB::table('rsmst_accdocs')
                        ->select(
                            'accdoc_id',
                            'accdoc_desc',
                            'accdoc_price',
                        )
                        // ->where('active_status', '1')
                        ->Where(DB::raw('upper(accdoc_desc)'), 'like', '%' . strtoupper($search) . '%')
                        ->orWhere(DB::raw('upper(accdoc_id)'), 'like', '%' . strtoupper($search) . '%')
                        ->limit(10)
                        ->orderBy('accdoc_desc', 'ASC')
                        ->orderBy('accdoc_id', 'ASC')
                        ->get(),
                    true
                );
            }
            $this->dataJasaDokterLovStatus = true;
            // set doing nothing
        }
    }
    // /////////////////////
    // LOV selected start
    public function setMydataJasaDokterLov($id)
    {
        // $this->checkRjStatus();
        $dataJasaDokterLovs = DB::table('rsmst_accdocs')
            ->select(
                'accdoc_id',
                'accdoc_desc',
                'accdoc_price',
            )
            ->where('accdoc_id', $this->dataJasaDokterLov[$id]['accdoc_id'])
            ->first();

        // set dokter sep
        $this->addJasaDokter($dataJasaDokterLovs->accdoc_id, $dataJasaDokterLovs->accdoc_desc, $dataJasaDokterLovs->accdoc_price);
        $this->resetdataJasaDokterLov();
    }

    public function resetdataJasaDokterLov()
    {
        $this->reset(['dataJasaDokterLov', 'dataJasaDokterLovStatus', 'dataJasaDokterLovSearch', 'selecteddataJasaDokterLovIndex']);
    }

    public function selectNextdataJasaDokterLov()
    {
        if ($this->selecteddataJasaDokterLovIndex === "") {
            $this->selecteddataJasaDokterLovIndex = 0;
        } else {
            $this->selecteddataJasaDokterLovIndex++;
        }

        if ($this->selecteddataJasaDokterLovIndex === count($this->dataJasaDokterLov)) {
            $this->selecteddataJasaDokterLovIndex = 0;
        }
    }

    public function selectPreviousdataJasaDokterLov()
    {

        if ($this->selecteddataJasaDokterLovIndex === "") {
            $this->selecteddataJasaDokterLovIndex = count($this->dataJasaDokterLov) - 1;
        } else {
            $this->selecteddataJasaDokterLovIndex--;
        }

        if ($this->selecteddataJasaDokterLovIndex === -1) {
            $this->selecteddataJasaDokterLovIndex = count($this->dataJasaDokterLov) - 1;
        }
    }

    public function enterMydataJasaDokterLov($id)
    {
        // dd($this->dataJasaDokterLov);
        // $this->checkRjStatus();
        // jika JK belum siap maka toaster error
        if (isset($this->dataJasaDokterLov[$id]['accdoc_id'])) {
            $this->addJasaDokter($this->dataJasaDokterLov[$id]['accdoc_id'], $this->dataJasaDokterLov[$id]['accdoc_desc'], $this->dataJasaDokterLov[$id]['accdoc_price']);
            $this->resetdataJasaDokterLov();
        } else {
            $this->emit('toastr-error', "Kode belum tersedia.");
            return;
        }
    }


    private function addJasaDokter($JasaDokterId, $JasaDokterDesc, $JasaDokterPrice): void
    {
        $this->collectingMyJasaDokter = [
            'JasaDokterId' => $JasaDokterId,
            'JasaDokterDesc' => $JasaDokterDesc,
            'JasaDokterPrice' => $JasaDokterPrice,
        ];
    }


    // LOV selected end
    /////////////////////////////////////////////////
    // Lov dataJasaDokterLov //////////////////////
    ////////////////////////////////////////////////
}
