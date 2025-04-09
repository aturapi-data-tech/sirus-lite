<?php

namespace App\Http\Livewire\Cetak;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Http\Traits\EmrUGD\EmrUGDTrait;
use App\Http\Traits\MasterPasien\MasterPasienTrait;


use Livewire\Component;

class CetakEresepUGD extends Component
{
    use EmrUGDTrait, MasterPasienTrait;

    // listener from blade////////////////
    protected $listeners = [
        'syncronizeAssessmentDokterUGDFindData' => 'mount',
        'syncronizeAssessmentPerawatUGDFindData' => 'mount'
    ];

    // dataDaftarUgd RJ
    public $rjNoRef;

    public array $dataDaftarUgd = [];
    public  array $dataPasien = [];



    private function findData($rjno): void
    {

        $this->dataDaftarUgd = $this->findDataUGD($rjno);
        // dd($this->dataDaftarUgd);

        // jika eresep tidak ditemukan tambah variable eresep pda array
        if (isset($this->dataDaftarUgd['eresep']) == false) {
            $this->dataDaftarUgd['eresep'] = [];
        }

        // jika eresepRacikan tidak ditemukan tambah variable eresepRacikan pda array
        if (isset($this->dataDaftarUgd['eresepRacikan']) == false) {
            $this->dataDaftarUgd['eresepRacikan'] = [];
        }

        $this->dataPasien = $this->findDataMasterPasien($this->dataDaftarUgd['regNo'] ?? '');
    }

    public function cetak()
    {
        $queryIdentitas = DB::table('rsmst_identitases')
            ->select(
                'int_name',
                'int_phone1',
                'int_phone2',
                'int_fax',
                'int_address',
                'int_city',
            )
            ->first();

        if (isset($this->dataDaftarUgd['perencanaan']['pengkajianMedis']['drPemeriksa']) && $this->dataDaftarUgd['perencanaan']['pengkajianMedis']['drPemeriksa']) {
            if ($this->dataDaftarUgd['eresep'] || $this->dataDaftarUgd['eresepRacikan']) {
                // cetak PDF
                $data = [
                    'myQueryIdentitas' => $queryIdentitas,
                    'dataPasien' => $this->dataPasien,
                    'dataDaftarUgd' => $this->dataDaftarUgd,

                ];
                $pdfContent = PDF::loadView('livewire.cetak.cetak-eresep-u-g-d-print', $data)->output();
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('Cetak Eresep UGD');


                return response()->streamDownload(
                    fn() => print($pdfContent),
                    "eresep.pdf"
                );
            } else {
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Data Resep Tidak ditemukan');
            }
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Belum ada TTD pada Data Resep');
        }
    }


    public function mount()
    {
        // setDataPasien
        $this->findData($this->rjNoRef);
    }
    public function render()
    {
        return view('livewire.cetak.cetak-eresep-u-g-d');
    }
}
