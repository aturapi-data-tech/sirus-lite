<?php

namespace App\Http\Livewire\EmrUGD\AdministrasiUGD;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
// use Carbon\Carbon;
// use Illuminate\Support\Facades\Validator;


// use App\Http\Traits\customErrorMessagesTrait;

// use Illuminate\Support\Str;
// use Spatie\ArrayToXml\ArrayToXml;
// use Exception;


class ObatUGD extends Component
{
    use WithPagination;


    // listener from blade////////////////
    protected $listeners = [
        'storeAssessmentDokterUGD' => 'store',
        'syncronizeAssessmentDokterUGDFindData' => 'mount',
        'syncronizeAssessmentPerawatUGDFindData' => 'mount'
    ];


    //////////////////////////////z
    // Ref on top bar
    //////////////////////////////
    public $rjNoRef;



    // dataDaftarUgd RJ
    public array $dataDaftarUgd = [];

    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////

    private function findData($rjno): void
    {
        $findData = DB::table('rstxn_ugdobats')
            ->join('immst_products', 'immst_products.product_id', 'rstxn_ugdobats.product_id')
            ->select('rstxn_ugdobats.product_id as product_id', 'product_name', 'qty', 'price', 'rjobat_dtl')
            ->where('rj_no', $rjno)
            ->get();



        if ($findData) {
            $this->dataDaftarUgd['rjObat'] = json_decode(json_encode($findData, true), true);
        } else {

            $this->dataDaftarUgd['rjObat'] = [];
        }
    }



    public function checkUgdStatus()
    {
        $lastInserted = DB::table('rstxn_ugdhdrs')
            ->select('rj_status')
            ->where('rj_no', $this->rjNoRef)
            ->first();

        if ($lastInserted->rj_status !== 'A') {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Pasien Sudah Pulang, Trasaksi Terkunci.");
            return (dd('Pasien Sudah Pulang, Trasaksi Terkuncixx.'));
        }
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
            'livewire.emr-u-g-d.administrasi-u-g-d.obat-u-g-d',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Data Pasien Unit Gawat Darurat',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Obat',
            ]
        );
    }
    // select data end////////////////


}
