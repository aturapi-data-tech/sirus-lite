<?php

namespace App\Http\Traits\LOV\LOVDiagKep;


use Illuminate\Support\Facades\DB;

trait LOVDiagKepTrait
{

    public array $dataDiagKepLov = [];
    public int $dataDiagKepLovStatus = 0;
    public string $dataDiagKepLovSearch = '';
    public int $selecteddataDiagKepLovIndex = 0;
    public array $collectingMyDiagKep = [];

    /////////////////////////////////////////////////
    // Lov dataDiagKepLov //////////////////////
    ////////////////////////////////////////////////

    public function updateddataDiagKepLovsearch()
    {

        // Reset index of LoV
        $this->reset(['selecteddataDiagKepLovIndex', 'dataDiagKepLov']);
        // Variable Search
        $search = $this->dataDiagKepLovSearch;

        // check LOV by diagkep_id rs id
        $dataDiagKepLovs = DB::table('rsmst_diagkeperawatans ')
            ->select(
                'diagkep_id',
                'diagkep_desc',
                'diagkep_json',
                // 'room_price',
                // 'perawatan_price',
                // 'common_service',
            )
            ->where('diagkep_id', '=', $search)
            // ->where('active_status', '1')
            ->first();

        if ($dataDiagKepLovs) {

            // set DiagKep sep
            $this->addDiagKep($dataDiagKepLovs->diagkep_id, $dataDiagKepLovs->diagkep_desc, $dataDiagKepLovs->diagkep_json);
            $this->resetdataDiagKepLov();
        } else {

            // if there is no id found and check (min 1 char on search)
            if (strlen($search) < 1) {
                $this->dataDiagKepLov = [];
            } else {
                $this->dataDiagKepLov = json_decode(
                    DB::table('rsmst_diagkeperawatans')
                        ->select(
                            'diagkep_id',
                            'diagkep_desc',
                            'diagkep_json',

                        )
                        ->Where(DB::raw('upper(diagkep_desc)'), 'like', '%' . strtoupper($search) . '%')
                        ->orWhere(DB::raw('upper(diagkep_id)'), 'like', '%' . strtoupper($search) . '%')
                        ->limit(10)
                        ->orderBy('diagkep_desc', 'ASC')
                        // ->orderBy('diagkep_json', 'ASC')
                        ->orderBy('diagkep_id', 'ASC')
                        ->get(),
                    true
                );
            }
            $this->dataDiagKepLovStatus = true;
            // set doing nothing
        }
    }
    // /////////////////////
    // LOV selected start
    public function setMydataDiagKepLov($id)
    {
        // $this->checkRjStatus();
        $dataDiagKepLovs = DB::table('RSMST_DIAGKEPERAWATANS')
            ->select(
                'diagkep_id',
                'diagkep_desc',
                'diagkep_json',

            )
            ->where('diagkep_id', $this->dataDiagKepLov[$id]['diagkep_id'])
            ->first();

        // set dokter sep
        $this->addDiagKep($dataDiagKepLovs->diagkep_id, $dataDiagKepLovs->diagkep_desc, $dataDiagKepLovs->diagkep_json);
        $this->resetdataDiagKepLov();
    }

    public function resetdataDiagKepLov()
    {
        $this->reset(['dataDiagKepLov', 'dataDiagKepLovStatus', 'dataDiagKepLovSearch', 'selecteddataDiagKepLovIndex']);
    }

    public function selectNextdataDiagKepLov()
    {
        if ($this->selecteddataDiagKepLovIndex === "") {
            $this->selecteddataDiagKepLovIndex = 0;
        } else {
            $this->selecteddataDiagKepLovIndex++;
        }

        if ($this->selecteddataDiagKepLovIndex === count($this->dataDiagKepLov)) {
            $this->selecteddataDiagKepLovIndex = 0;
        }
    }

    public function selectPreviousdataDiagKepLov()
    {

        if ($this->selecteddataDiagKepLovIndex === "") {
            $this->selecteddataDiagKepLovIndex = count($this->dataDiagKepLov) - 1;
        } else {
            $this->selecteddataDiagKepLovIndex--;
        }

        if ($this->selecteddataDiagKepLovIndex === -1) {
            $this->selecteddataDiagKepLovIndex = count($this->dataDiagKepLov) - 1;
        }
    }

    public function enterMydataDiagKepLov($id)
    {
        // dd($this->dataDiagKepLov);
        // $this->checkRjStatus();
        // jika JK belum siap maka toaster error
        if (isset($this->dataDiagKepLov[$id]['diagkep_id'])) {
            $this->addDiagKep(
                $this->dataDiagKepLov[$id]['diagkep_id'], // diagkep_id
                $this->dataDiagKepLov[$id]['diagkep_desc'], // diagkep_desc
                $this->dataDiagKepLov[$id]['diagkep_json'] ?? null, // diagkep_json (default null jika tidak ada)
            );
            $this->resetdataDiagKepLov();
        } else {
            $this->emit('toastr-error', "Kode belum tersedia.");
            return;
        }
    }


    private function addDiagKep($DiagKepId, $DiagKepDesc, $DiagKepJson): void
    {
        $this->collectingMyDiagKep = [
            'DiagKepId' => $DiagKepId,
            'DiagKepDesc' => $DiagKepDesc,
            'DiagKepJson' => $DiagKepJson,
        ];
    }



    // LOV selected end
    /////////////////////////////////////////////////
    // Lov dataDiagKepLov //////////////////////
    ////////////////////////////////////////////////
}
