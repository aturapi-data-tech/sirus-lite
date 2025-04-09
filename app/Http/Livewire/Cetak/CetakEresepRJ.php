<?php

namespace App\Http\Livewire\Cetak;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Http\Traits\EmrRJ\EmrRJTrait;
use App\Http\Traits\MasterPasien\MasterPasienTrait;


use Livewire\Component;

class CetakEresepRJ extends Component
{
    use EmrRJTrait, MasterPasienTrait;

    // listener from blade////////////////
    protected $listeners = [
        'syncronizeAssessmentDokterRJFindData' => 'mount',
        'syncronizeAssessmentPerawatRJFindData' => 'mount'
    ];

    // dataDaftarPoliRJ RJ
    public $rjNoRef;

    public array $dataDaftarPoliRJ = [];
    public  array $dataPasien = [];



    private function findData($rjno): void
    {



        $findDataRJ = $this->findDataRJ($rjno);

        $this->dataDaftarPoliRJ  = $findDataRJ['dataDaftarRJ'];


        // jika eresep tidak ditemukan tambah variable eresep pda array
        if (isset($this->dataDaftarPoliRJ['eresep']) == false) {
            $this->dataDaftarPoliRJ['eresep'] = [];
        }

        // jika eresepRacikan tidak ditemukan tambah variable eresepRacikan pda array
        if (isset($this->dataDaftarPoliRJ['eresepRacikan']) == false) {
            $this->dataDaftarPoliRJ['eresepRacikan'] = [];
        }

        $this->dataPasien = $this->findDataMasterPasien($this->dataDaftarPoliRJ['regNo'] ?? '');
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

        if (isset($this->dataDaftarPoliRJ['perencanaan']['pengkajianMedis']['drPemeriksa']) && $this->dataDaftarPoliRJ['perencanaan']['pengkajianMedis']['drPemeriksa']) {
            if ($this->dataDaftarPoliRJ['eresep'] || $this->dataDaftarPoliRJ['eresepRacikan']) {
                // cetak PDF
                $data = [
                    'myQueryIdentitas' => $queryIdentitas,
                    'dataPasien' => $this->dataPasien,
                    'dataDaftarPoliRJ' => $this->dataDaftarPoliRJ,

                ];
                $pdfContent = PDF::loadView('livewire.cetak.cetak-eresep-r-j-print', $data)->output();
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('Cetak Eresep RJ');


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
        return view('livewire.cetak.cetak-eresep-r-j');
    }
}
