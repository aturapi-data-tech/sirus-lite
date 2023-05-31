<?php

namespace App\Http\Livewire\Lov;

use Livewire\Component;
use App\Models\Province;

class ListOfValue extends Component
{

    public $message;
    public $myLov = [];
    protected $listeners = ['sendMyLov' => 'parentSendMyLov'];


    public function mount()
    {
        $this->message = 'mount';
    }

    public function parentSendMyLov($value)
    {
        $this->myLov = Province::where('name', 'like', '%' . $value . '%')
            ->orderBy('name', 'asc')->get();
    }







    public function render()
    {
        return view('livewire.lov.list-of-value');
    }
}