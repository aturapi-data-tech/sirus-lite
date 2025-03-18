<?php

namespace App\Http\Livewire\Cetak;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Http\Traits\EmrRI\EmrRITrait;
use App\Http\Traits\MasterPasien\MasterPasienTrait;
use Exception;


use Livewire\Component;

class CetakEresepRI extends Component
{
    use EmrRITrait, MasterPasienTrait;

    // listener from blade////////////////
    protected $listeners = [
        'syncronizeAssessmentDokterRIFindData' => 'mount',
        'syncronizeAssessmentPerawatRIFindData' => 'mount'
    ];

    // dataDaftarRi RJ
    public $riHdrNoRef;
    public $resepNoRef;
    public $resepIndexRef;
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
            foreach ($this->dataDaftarRi['eresepHdr'] as $index => $header) {
                // Lewati jika resepNo tidak ada atau tidak sama dengan referensi
                if (!isset($header['resepNo']) || $header['resepNo'] !== $this->resepNoRef) {
                    continue;
                }

                // Ambil header yang valid
                $currentHeader = $this->dataDaftarRi['eresepHdr'][$index] ?? null;
                if (!$currentHeader) {
                    continue;
                }

                // Cek apakah TTD dokter sudah ada
                if (empty($currentHeader['tandaTanganDokter']['dokterPeresepCode'])) {
                    toastr()->closeOnHover(true)
                        ->closeDuration(3)
                        ->positionClass('toast-top-left')
                        ->addError('Belum ada TTD pada Data Resep');
                    return;
                }

                // Siapkan data untuk cetak PDF
                $data = [
                    'myQueryIdentitas' => $queryIdentitas,
                    'dataPasien'        => $this->dataPasien,
                    'dataDaftarRi'      => $this->dataDaftarRi,
                    'resepIndexRef'     => $index,
                ];

                $pdfContent = PDF::loadView('livewire.cetak.cetak-eresep-r-i-print', $data)->output();

                toastr()->closeOnHover(true)
                    ->closeDuration(3)
                    ->positionClass('toast-top-left')
                    ->addSuccess('Cetak Eresep RI');

                return response()->streamDownload(
                    fn() => print($pdfContent),
                    "eresep.pdf"
                );
            }
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
        return view('livewire.cetak.cetak-eresep-r-i');
    }
}
