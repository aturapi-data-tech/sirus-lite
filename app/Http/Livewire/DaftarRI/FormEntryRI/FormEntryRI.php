<?php

namespace App\Http\Livewire\DaftarRI\FormEntryRI;

use App\Http\Traits\EmrRI\EmrRITrait;
use Illuminate\Support\Facades\DB;

use Livewire\Component;

class FormEntryRI extends Component
{
    use EmrRITrait;

    // listener from blade////////////////
    protected $listeners = [];


    public bool $isOpen = false;
    public int $riHdrNo;

    public  $dataPasien = [];
    public $dataDaftarRi = [];




    //////////////////////////////////////////////////////////////////////
    private function openModal(): void
    {
        $this->isOpen = true;
    }
    public function create()
    {
        $klaimStatus = DB::table('rsmst_klaimtypes')
            ->where('klaim_id', $this->dataDaftarRi['klaimId'] ?? '')
            ->value('klaim_status') ?? 'UMUM';

        if ($klaimStatus !== 'BPJS') {
            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addWarning('Form hanya dapat dibuka jika jenis klaim adalah Jaminan (JM).');
            return;
        }

        $this->openModal();
    }

    public function closeModal(): void
    {
        $this->isOpen = false;
    }
    public function cetakSEP(): void {}



    private function findData($riHdrNo): void
    {
        $this->dataDaftarRi = $this->findDataRI($riHdrNo);
        dd($this->dataDaftarRi);
        $this->emit('listenerRegNo', $this->dataDaftarRi['regNo']);
    }
    public function mount()
    {

        if ($this->riHdrNo) {
            $this->findData($this->riHdrNo);
        }
    }

    public function render()
    {
        return view('livewire.daftar-r-i.form-entry-r-i.form-entry-r-i');
    }
}
