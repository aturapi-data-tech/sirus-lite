<?php

namespace App\Http\Livewire\EmrUGD\GeneralConsentPasienUGD;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

use App\Http\Traits\customErrorMessagesTrait;

use Illuminate\Support\Str;
use Spatie\ArrayToXml\ArrayToXml;


class GeneralConsentPasienUGD extends Component
{
    use WithPagination;


    // listener from blade////////////////
    protected $listeners = [];

    //////////////////////////////
    // Ref on top bar
    //////////////////////////////
    public $rjNoRef;
    public $regNoRef;
    public $signature;


    public function storeAssessmentDokterUGD()
    {
        $this->emit('storeAssessmentDokterUGD');
    }
    public function submit()
    {
        dd($this->signature);
        // \Storage::put('signatures/signature.png', base64_decode(Str::of($this->signature)->after(',')));
    }

    // select data start////////////////
    public function render()
    {

        return view(
            'livewire.emr-u-g-d.general-consent-pasien-u-g-d.general-consent-pasien-u-g-d',
            []
        );
    }
    // select data end////////////////


}
