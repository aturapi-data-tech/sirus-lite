<?php

namespace App\Http\Livewire\GajiDokter;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Traits\LOV\LOVDokter\LOVDokterTrait;
use Carbon\Carbon;

class GajiDokter extends Component
{
    use WithPagination, LOVDokterTrait;


    // primitive Variable
    public string $myTitle = 'Pendapatan Jasa Dokter';
    public string $mySnipt = 'Pendapatan Jasa Dokter';
    public string $myProgram = 'Pendapatan Jasa Dokter';

    public array $myLimitPerPages = [5, 10, 15, 20, 100];
    // limit record per page -resetExcept////////////////
    public int $limitPerPage = 10;

    // LOV Nested
    public array $dokter;

    private function syncDataFormEntry(): void
    {
        $this->myTopBar['drId'] = $this->dokter['DokterId'] ?? '';
        $this->myTopBar['drName'] = $this->dokter['DokterDesc'] ?? '';
    }
    private function syncLOV(): void
    {
        $this->dokter = $this->collectingMyDokter;
    }
    public function resetDokter()
    {
        $this->reset([
            'collectingMyDokter', //Reset LOV / render  / empty NestLov
        ]);
        $this->resetValidation();
    }
    // LOV Nested


    // my Top Bar
    public array $myTopBar = [

        'refBulan' => '',

        'drId' => '',
        'drName' => '',
        'drOptions' => [],
    ];

    public string $refFilter = '';
    // search logic -resetExcept////////////////
    protected $queryString = [
        'refFilter' => ['except' => '', 'as' => 'cariData'],
        'page' => ['except' => 1, 'as' => 'p'],
    ];

    // reset page when myTopBar Change
    public function updatedReffilter()
    {
        $this->resetPage();
    }

    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////

    public function mount()
    {
        // Set refBulan ke format mm/YYYY untuk bulan sekarang
        $this->myTopBar['refBulan'] = Carbon::now()->format('m/Y');
    }

    // select data start////////////////
    public function render()
    {

        // LOV
        $this->syncLOV();
        // FormEntry
        $this->syncDataFormEntry();

        // set mySearch
        $myRefBulan = $this->myTopBar['refBulan'];
        $myRefdrId = $this->myTopBar['drId'];


        //////////////////////////////////////////
        // Query ///////////////////////////////
        //////////////////////////////////////////
        $query = DB::table('rsview_newdocsalaries as v')
            ->select([
                'v.group_doc',
                'v.desc_doc',
                'v.dr_id',
                'v.dr_name',
                DB::raw('SUM(v.doc_nominal) AS doc_nominal'),
                DB::raw('k.klaim_status AS klaim_status'),
            ])
            ->join('rsmst_klaimtypes as k', 'v.klaim_id', '=', 'k.klaim_id');

        if ($myRefdrId) {
            $query->where('dr_id', $myRefdrId);
        }

        $query->whereRaw(
            "to_char(to_date(doc_date, 'dd/mm/yyyy'), 'mm/yyyy') = ?",
            [$myRefBulan]
        )
            ->groupBy(
                'v.group_doc',
                'v.desc_doc',
                'v.dr_id',
                'v.dr_name',
                'k.klaim_status'                  // harus di–group juga
            )
            ->orderBy('v.dr_id')
            ->orderBy('klaim_status')
            ->orderBy('v.group_doc')
            ->orderBy('v.desc_doc');

        ////////////////////////////////////////////////
        // end Query
        ///////////////////////////////////////////////



        return view(
            'livewire.gaji-dokter.gaji-dokter',
            // ['myQueryData' => $query->paginate($this->limitPerPage)]
            ['myQueryData' => $query->get()]

        );
    }
    // select data end////////////////


}
