<?php

namespace App\Http\Livewire\Cetak;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Http\Traits\EmrRI\EmrRITrait;
use App\Http\Traits\MasterPasien\MasterPasienTrait;
use Exception;


use Livewire\Component;

class CetakRingkasanPasienPulangRI extends Component
{
    use EmrRITrait, MasterPasienTrait;

    // listener from blade////////////////
    protected $listeners = [
        'syncronizeAssessmentDokterRIFindData' => 'mount',
        'syncronizeAssessmentPerawatRIFindData' => 'mount'
    ];

    // dataDaftarRi RJ
    public $riHdrNoRef;
    public array $dataDaftarRi = [];
    public  array $dataPasien = [];



    private function findData($riHdrNoRef): void
    {

        $this->dataDaftarRi = $this->findDataRI($riHdrNoRef);
        $this->dataPasien = $this->findDataMasterPasien($this->dataDaftarRi['regNo'] ?? '');
    }



    public function cetak()
    {
        $queryIdentitas = DB::table('rsmst_identitases')
            ->select('int_name', 'int_phone1', 'int_phone2', 'int_fax', 'int_address', 'int_city')
            ->first();

        try {
            // Siapkan data untuk cetak PDF
            $data = [
                'myQueryIdentitas' => $queryIdentitas,
                'dataPasien'        => $this->dataPasien,
                'dataDaftarRi'      => $this->dataDaftarRi,
            ];

            $pdfContent = PDF::loadView('livewire.cetak.cetak-ringkasan-pasien-pulang-r-i-print', $data)->output();

            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addSuccess('Cetak Ringkasan Pasien Pulang RI');

            return response()->streamDownload(
                fn() => print($pdfContent),
                "ringkasan-pasien-pulang-ri.pdf"
            );
        } catch (Exception $e) {
            dd($e);
        }
    }

    public function mount()
    {
        $this->findData($this->riHdrNoRef);
    }

    public function render()
    {
        return view('livewire.cetak.cetak-ringkasan-pasien-pulang-r-i');
    }
}
