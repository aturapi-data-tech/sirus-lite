<?php

namespace App\Http\Livewire\Cetak;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Http\Traits\EmrUGD\EmrUGDTrait;
use App\Http\Traits\MasterPasien\MasterPasienTrait;
use Livewire\Component;

class CetakEresepUGD extends Component
{
    use EmrUGDTrait, MasterPasienTrait;

    // listener from blade////////////////
    protected $listeners = [];

    public $rjNoRef;

    public array $dataDaftarUgd = [];
    public array $dataPasien    = [];



    private function findData(string $rjno): void
    {
        // fresh JSON UGD
        $this->dataDaftarUgd = $this->findDataUGD($rjno) ?: [];

        // pastikan key minimal ada supaya blade aman
        $this->dataDaftarUgd['eresep']        = $this->dataDaftarUgd['eresep']        ?? [];
        $this->dataDaftarUgd['eresepRacikan'] = $this->dataDaftarUgd['eresepRacikan'] ?? [];

        // fresh master pasien
        $regNo = $this->dataDaftarUgd['regNo'] ?? '';
        $this->dataPasien = $regNo ? ($this->findDataMasterPasien($regNo) ?: []) : [];
    }

    public function cetak()
    {
        if (!$this->rjNoRef) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('rjNo kosong.');
            return;
        }

        // Selalu ambil data TERBARU saat tombol dicetak
        $this->findData($this->rjNoRef);

        // Ambil identitas RS (jika ada)
        $identitas = DB::table('rsmst_identitases')
            ->select('int_name', 'int_phone1', 'int_phone2', 'int_fax', 'int_address', 'int_city')
            ->first();

        // Validasi TTD dokter & ada data resep
        $dr = $this->dataDaftarUgd['perencanaan']['pengkajianMedis']['drPemeriksa'] ?? null;
        $adaResep = !empty($this->dataDaftarUgd['eresep']) || !empty($this->dataDaftarUgd['eresepRacikan']);

        if (!$dr) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Belum ada TTD pada Data Resep.');
            return;
        }
        if (!$adaResep) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Data Resep tidak ditemukan.');
            return;
        }

        // Render PDF
        $data = [
            'myQueryIdentitas' => $identitas,
            'dataPasien'       => $this->dataPasien,
            'dataDaftarUgd'    => $this->dataDaftarUgd,
        ];

        // Catatan: gunakan facade yang di-import (Pdf), bukan PDF::
        $pdfContent = Pdf::loadView('livewire.cetak.cetak-eresep-u-g-d-print', $data)->output();

        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('Cetak Eresep UGD.');

        return response()->streamDownload(
            fn() => print($pdfContent),
            'eresep.pdf'
        );
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
