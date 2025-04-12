<?php

namespace App\Http\Traits\LOV\LOVJasaKaryawan;


use Illuminate\Support\Facades\DB;

trait LOVJasaKaryawanTrait
{

    public array $dataJasaKaryawanLov = [];
    public int $dataJasaKaryawanLovStatus = 0;
    public string $dataJasaKaryawanLovSearch = '';
    public int $selecteddataJasaKaryawanLovIndex = 0;
    public array $collectingMyJasaKaryawan = [];

    /////////////////////////////////////////////////
    // Lov dataJasaKaryawanLov //////////////////////
    ////////////////////////////////////////////////

    public function updateddataJasaKaryawanLovsearch()
    {

        // Reset index of LoV
        $this->reset(['selecteddataJasaKaryawanLovIndex', 'dataJasaKaryawanLov']);
        // Variable Search
        $search = $this->dataJasaKaryawanLovSearch;

        // check LOV by acte_id rs id
        $dataJasaKaryawanLovs = DB::table('rsmst_actemps ')
            ->select(
                'acte_id',
                'acte_desc',
                'acte_price',
            )
            ->where('acte_id', '=', $search)
            // ->where('active_status', '1')
            ->first();

        if ($dataJasaKaryawanLovs) {

            // set JasaKaryawan sep
            $this->addJasaKaryawan($dataJasaKaryawanLovs->acte_id, $dataJasaKaryawanLovs->acte_desc, $dataJasaKaryawanLovs->acte_price);;
            $this->resetdataJasaKaryawanLov();
        } else {

            // if there is no id found and check (min 1 char on search)
            if (strlen($search) < 1) {
                $this->dataJasaKaryawanLov = [];
            } else {
                $this->dataJasaKaryawanLov = json_decode(
                    DB::table('rsmst_actemps')
                        ->select(
                            'acte_id',
                            'acte_desc',
                            'acte_price',
                        )
                        // ->where('active_status', '1')
                        ->Where(DB::raw('upper(acte_desc)'), 'like', '%' . strtoupper($search) . '%')
                        ->orWhere(DB::raw('upper(acte_id)'), 'like', '%' . strtoupper($search) . '%')
                        ->limit(10)
                        ->orderBy('acte_desc', 'ASC')
                        ->orderBy('acte_id', 'ASC')
                        ->get(),
                    true
                );
            }
            $this->dataJasaKaryawanLovStatus = true;
            // set doing nothing
        }
    }
    // /////////////////////
    // LOV selected start
    public function setMydataJasaKaryawanLov($id)
    {
        // $this->checkRjStatus();
        $dataJasaKaryawanLovs = DB::table('rsmst_actemps')
            ->select(
                'acte_id',
                'acte_desc',
                'acte_price',
            )
            ->where('acte_id', $this->dataJasaKaryawanLov[$id]['acte_id'])
            ->first();

        // set dokter sep
        $this->addJasaKaryawan($dataJasaKaryawanLovs->acte_id, $dataJasaKaryawanLovs->acte_desc, $dataJasaKaryawanLovs->acte_price);
        $this->resetdataJasaKaryawanLov();
    }

    public function resetdataJasaKaryawanLov()
    {
        $this->reset(['dataJasaKaryawanLov', 'dataJasaKaryawanLovStatus', 'dataJasaKaryawanLovSearch', 'selecteddataJasaKaryawanLovIndex']);
    }

    public function selectNextdataJasaKaryawanLov()
    {
        if ($this->selecteddataJasaKaryawanLovIndex === "") {
            $this->selecteddataJasaKaryawanLovIndex = 0;
        } else {
            $this->selecteddataJasaKaryawanLovIndex++;
        }

        if ($this->selecteddataJasaKaryawanLovIndex === count($this->dataJasaKaryawanLov)) {
            $this->selecteddataJasaKaryawanLovIndex = 0;
        }
    }

    public function selectPreviousdataJasaKaryawanLov()
    {

        if ($this->selecteddataJasaKaryawanLovIndex === "") {
            $this->selecteddataJasaKaryawanLovIndex = count($this->dataJasaKaryawanLov) - 1;
        } else {
            $this->selecteddataJasaKaryawanLovIndex--;
        }

        if ($this->selecteddataJasaKaryawanLovIndex === -1) {
            $this->selecteddataJasaKaryawanLovIndex = count($this->dataJasaKaryawanLov) - 1;
        }
    }

    public function enterMydataJasaKaryawanLov($id)
    {
        // dd($this->dataJasaKaryawanLov);
        // $this->checkRjStatus();
        // jika JK belum siap maka toaster error
        if (isset($this->dataJasaKaryawanLov[$id]['acte_id'])) {
            $this->addJasaKaryawan($this->dataJasaKaryawanLov[$id]['acte_id'], $this->dataJasaKaryawanLov[$id]['acte_desc'], $this->dataJasaKaryawanLov[$id]['acte_price']);
            $this->resetdataJasaKaryawanLov();
        } else {
            $this->emit('toastr-error', "Kode belum tersedia.");
            return;
        }
    }


    private function addJasaKaryawan($JasaKaryawanId, $JasaKaryawanDesc, $JasaKaryawanPrice): void
    {
        $this->collectingMyJasaKaryawan = [
            'JasaKaryawanId' => $JasaKaryawanId,
            'JasaKaryawanDesc' => $JasaKaryawanDesc,
            'JasaKaryawanPrice' => $JasaKaryawanPrice,
        ];
    }


    // LOV selected end
    /////////////////////////////////////////////////
    // Lov dataJasaKaryawanLov //////////////////////
    ////////////////////////////////////////////////
}
