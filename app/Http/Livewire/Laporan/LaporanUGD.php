<?php

namespace App\Http\Livewire\Laporan;

use Carbon\Carbon;
use Livewire\Component;


class LaporanUGD extends Component
{
    public string $myTitle = 'Laporan UGD';
    public string $mySnipt = 'Laporan UGD';
    public array $queryData = [];

    public string $myMonth = '';
    public string $myYear = '';

    public function mount()
    {
        $now = Carbon::now();
        $this->myMonth = $now->format('m'); // format 2 digit: 01–12
        $this->myYear  = $now->format('Y'); // format 4 digit: 2025

    }
    public function render()
    {
        return view('livewire.laporan.laporan-u-g-d');
    }
}
