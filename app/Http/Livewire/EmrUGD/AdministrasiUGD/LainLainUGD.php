<?php

namespace App\Http\Livewire\EmrUGD\AdministrasiUGD;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Cache\LockTimeoutException;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

use App\Http\Traits\customErrorMessagesTrait;
use App\Http\Traits\EmrUGD\EmrUGDTrait;



class LainLainUGD extends Component
{
    use WithPagination, EmrUGDTrait;




    //////////////////////////////
    // Ref on top bar
    //////////////////////////////
    public $rjNoRef;



    // dataDaftarUgd RJ
    public array $dataDaftarUgd = [];

    //////////////////////////////////////////////////////////////////////


    //  table LOV////////////////



    public $dataLainLainLov = [];
    public $dataLainLainLovStatus = 0;
    public $dataLainLainLovSearch = '';
    public $selecteddataLainLainLovIndex = 0;

    public $collectingMyLainLain = [];







    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////
    public function updated($propertyName)
    {
        // dd($propertyName);
        // $this->validateOnly($propertyName);
    }




    /////////////////////////////////////////////////
    // Lov dataLainLainLov //////////////////////
    ////////////////////////////////////////////////
    public function clickdataLainLainLov()
    {
        $this->dataLainLainLovStatus = true;
        $this->dataLainLainLov = [];
    }

    public function updatedDataLainLainLovSearch()
    {

        // Reset index of LoV
        $this->reset(['selecteddataLainLainLovIndex', 'dataLainLainLov']);
        // Variable Search
        $search = $this->dataLainLainLovSearch;

        // check LOV by dr_id rs id
        $dataLainLainLovs = DB::table('rsmst_others  ')->select(
            'other_id',
            'other_desc',
            'other_price'
        )
            ->where('other_id', $search)
            ->where('active_status', '1')
            ->first();

        if ($dataLainLainLovs) {

            // set LainLain sep
            $this->addLainLain($dataLainLainLovs->other_id, $dataLainLainLovs->other_desc, $dataLainLainLovs->other_price);
            $this->resetdataLainLainLov();
        } else {

            // if there is no id found and check (min 3 char on search)
            if (strlen($search) < 1) {
                $this->dataLainLainLov = [];
            } else {
                $this->dataLainLainLov = json_decode(
                    DB::table('rsmst_others ')->select(
                        'other_id',
                        'other_desc',
                        'other_price'
                    )
                        ->where('active_status', '1')
                        ->where(DB::raw('upper(other_desc)'), 'like', '%' . strtoupper($search) . '%')
                        ->limit(10)
                        ->orderBy('other_id', 'ASC')
                        ->orderBy('other_desc', 'ASC')
                        ->get(),
                    true
                );
            }
            $this->dataLainLainLovStatus = true;
            // set doing nothing
        }
    }
    // /////////////////////
    // LOV selected start
    public function setMydataLainLainLov($id)
    {
        if (!$this->checkUgdStatus($this->rjNoRef)) return;
        $dataLainLainLovs = DB::table('rsmst_others ')->select(
            'other_id',
            'other_desc',
            'other_price'
        )
            ->where('active_status', '1')
            ->where('other_id', $this->dataLainLainLov[$id]['other_id'])
            ->first();

        // set dokter sep
        $this->addLainLain($dataLainLainLovs->other_id, $dataLainLainLovs->other_desc, $dataLainLainLovs->other_price);
        $this->resetdataLainLainLov();
    }

    public function resetdataLainLainLov()
    {
        $this->reset(['dataLainLainLov', 'dataLainLainLovStatus', 'dataLainLainLovSearch', 'selecteddataLainLainLovIndex']);
    }

    public function selectNextdataLainLainLov()
    {
        if ($this->selecteddataLainLainLovIndex === "") {
            $this->selecteddataLainLainLovIndex = 0;
        } else {
            $this->selecteddataLainLainLovIndex++;
        }

        if ($this->selecteddataLainLainLovIndex === count($this->dataLainLainLov)) {
            $this->selecteddataLainLainLovIndex = 0;
        }
    }

    public function selectPreviousdataLainLainLov()
    {

        if ($this->selecteddataLainLainLovIndex === "") {
            $this->selecteddataLainLainLovIndex = count($this->dataLainLainLov) - 1;
        } else {
            $this->selecteddataLainLainLovIndex--;
        }

        if ($this->selecteddataLainLainLovIndex === -1) {
            $this->selecteddataLainLainLovIndex = count($this->dataLainLainLov) - 1;
        }
    }

    public function enterMydataLainLainLov($id)
    {
        if (!$this->checkUgdStatus($this->rjNoRef)) return;
        // jika JK belum siap maka toaster error
        if (isset($this->dataLainLainLov[$id]['other_id'])) {
            $this->addLainLain($this->dataLainLainLov[$id]['other_id'], $this->dataLainLainLov[$id]['other_desc'], $this->dataLainLainLov[$id]['other_price']);
            $this->resetdataLainLainLov();
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Lain-Lain belum tersedia.");
        }
    }


    // LOV selected end
    /////////////////////////////////////////////////
    // Lov dataLainLainLov //////////////////////
    ////////////////////////////////////////////////







    // insert and update record start////////////////



    // insert and update record end////////////////


    private function findData($rjno): void
    {
        $findDataUGD = $this->findDataUGD($rjno);
        $this->dataDaftarUgd  = $findDataUGD;

        // jika LainLain tidak ditemukan tambah variable LainLain pda array
        if (isset($this->dataDaftarUgd['LainLain']) == false) {
            $this->dataDaftarUgd['LainLain'] = [];
        }
    }


    private function addLainLain($LainLainId, $LainLainDesc, $salesPrice): void
    {

        $this->collectingMyLainLain = [
            'LainLainId' => $LainLainId,
            'LainLainDesc' => $LainLainDesc,
            'LainLainPrice' => $salesPrice,
        ];
    }

    public function insertLainLain(): void
    {
        if (!$this->checkUgdStatus($this->rjNoRef)) return;

        $messages = customErrorMessagesTrait::messages();
        $rules = [
            "collectingMyLainLain.LainLainId"    => 'bail|required|exists:rsmst_others,other_id',
            "collectingMyLainLain.LainLainDesc"  => 'bail|required',
            "collectingMyLainLain.LainLainPrice" => 'bail|required|numeric',
        ];
        $this->validate($rules, $messages);

        $rjNo = $this->rjNoRef;
        $lockRj = "ugd:{$rjNo}";
        $lockOthers = "ugdothers:counter";

        try {
            Cache::lock($lockRj, 5)->block(3, function () use ($rjNo, $lockOthers) {
                Cache::lock($lockOthers, 5)->block(3, function () use ($rjNo) {
                    DB::transaction(function () use ($rjNo) {
                        $nextDtl = (int) DB::table('rstxn_ugdothers')
                            ->max(DB::raw('nvl(to_number(rjo_dtl),0)')) + 1;

                        DB::table('rstxn_ugdothers')->insert([
                            'rjo_dtl'     => $nextDtl,
                            'rj_no'       => $rjNo,
                            'other_id'    => $this->collectingMyLainLain['LainLainId'],
                            'other_price' => $this->collectingMyLainLain['LainLainPrice'],
                        ]);

                        // patch state lokal
                        $this->dataDaftarUgd['LainLain'][] = [
                            'LainLainId'    => $this->collectingMyLainLain['LainLainId'],
                            'LainLainDesc'  => $this->collectingMyLainLain['LainLainDesc'],
                            'LainLainPrice' => $this->collectingMyLainLain['LainLainPrice'],
                            'rjotherDtl'    => $nextDtl,
                            'rjNo'          => $rjNo,
                            'userLog'       => auth()->user()->myuser_name ?? '',
                            'userLogDate'   => Carbon::now(config('app.timezone'))->format('d/m/Y H:i:s'),
                        ];

                        // fresh-merge JSON subtree
                        $fresh = $this->findDataUGD($rjNo) ?: [];
                        $fresh['LainLain'] = array_values($this->dataDaftarUgd['LainLain'] ?? ($fresh['LainLain'] ?? []));
                        $this->updateJsonUGD($rjNo, $fresh);
                        $this->emit('ugd:refresh-summary');

                        $this->dataDaftarUgd = $fresh;
                    });
                });
            });

            $this->reset(['collectingMyLainLain']);
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('Lain-Lain berhasil ditambahkan.');
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Gagal menambah Lain-Lain.');
        }
    }

    public function removeLainLain($rjotherDtl)
    {
        if (!$this->checkUgdStatus($this->rjNoRef)) return;

        $rjNo = $this->rjNoRef;
        $lockKey = "ugd:{$rjNo}";

        try {
            Cache::lock($lockKey, 5)->block(3, function () use ($rjNo, $rjotherDtl) {
                DB::transaction(function () use ($rjNo, $rjotherDtl) {
                    DB::table('rstxn_ugdothers')
                        ->where('rj_no', $rjNo)
                        ->where('rjo_dtl', $rjotherDtl)
                        ->delete();

                    // patch state lokal
                    $this->dataDaftarUgd['LainLain'] = collect($this->dataDaftarUgd['LainLain'] ?? [])
                        ->reject(fn($i) => (string)($i['rjotherDtl'] ?? '') === (string)$rjotherDtl)
                        ->values()->all();

                    // fresh-merge JSON subtree
                    $fresh = $this->findDataUGD($rjNo) ?: [];
                    $fresh['LainLain'] = array_values($this->dataDaftarUgd['LainLain']);
                    $this->updateJsonUGD($rjNo, $fresh);
                    $this->emit('ugd:refresh-summary');
                    $this->dataDaftarUgd = $fresh;
                });
            });

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('Lain-Lain dihapus.');
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Gagal menghapus Lain-Lain.');
        }
    }

    public function resetcollectingMyLainLain()
    {
        $this->reset(['collectingMyLainLain']);
    }




    // when new form instance
    public function mount()
    {
        $this->findData($this->rjNoRef);
    }



    // select data start////////////////
    public function render()
    {

        return view(
            'livewire.emr-u-g-d.administrasi-u-g-d.lain-lain-u-g-d',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Data Pasien Unit Gawat Darurat',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Lain Lain',
            ]
        );
    }
    // select data end////////////////


}
