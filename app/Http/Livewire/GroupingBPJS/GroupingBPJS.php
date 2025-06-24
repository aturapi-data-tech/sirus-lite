<?php

namespace App\Http\Livewire\GroupingBPJS;


use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Livewire\WithPagination;


class GroupingBPJS extends Component
{
    use WithPagination, WithFileUploads;

    // primitive Variable
    public string $myTitle = 'Upload FIle Grouping BPJS';
    public string $mySnipt = 'Grouping BPJS';
    public string $myProgram = 'Grouping BPJS';

    public array $myLimitPerPages = [5, 10, 15, 20, 100];
    // limit record per page -resetExcept////////////////
    public int $limitPerPage = 10;

    public $file;
    public $rows = [];

    public function updatedFile()
    {
        $this->readXlsx();
    }

    public function readXlsx()
    {
        $path = $this->file->getRealPath();
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray();

        // Buang header jika perlu
        $this->rows = array_slice($data, 1); // Mulai dari baris ke-2
    }


    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////

    // is going to insert data////////////////

    // when new form instance
    public function mount() {}

    // select data start////////////////
    public function render()
    {

        return view('livewire.grouping-b-p-j-s.grouping-b-p-j-s');
    }
    // select data end////////////////


}
