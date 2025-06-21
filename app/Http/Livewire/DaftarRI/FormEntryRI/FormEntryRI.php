<?php

namespace App\Http\Livewire\DaftarRI\FormEntryRI;

use App\Http\Traits\EmrRI\EmrRITrait;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Traits\BPJS\VclaimTrait;

use Livewire\Component;

class FormEntryRI extends Component
{
    use EmrRITrait;

    // listener from blade////////////////
    protected $listeners = [
        'syncronizeAssessmentPerawatRIFindData' => 'mount'
    ];


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

    public function cetakSEPRI()
    {
        /* 1. Pastikan klaim BPJS */
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

        /* 2. Jika SEP belum tersimpan, panggil WS BPJS lagi */
        if (empty($this->dataDaftarRi['sep']['resSep'])) {

            $HttpGetBpjs = VclaimTrait::sep_nomor(
                $this->dataDaftarRi['sep']['noSep'] ?? ''
            )->getOriginalContent();

            if ($HttpGetBpjs['metadata']['code'] == 200) {

                // simpan response SEP & nomor SEP ter-update
                $this->dataDaftarRi['sep']['resSep'] = $HttpGetBpjs['response'];
                $this->dataDaftarRi['sep']['noSep']  = $HttpGetBpjs['response']['noSep'];

                // ⇢ update JSON di tabel rstxn_rihdrs (atau koleksi lain)
                $this->updateJsonRI(
                    $this->riHdrNo,    // primary key header RI
                    $this->dataDaftarRi
                );

                $this->emit('syncronizeAssessmentPerawatRIFindData');


                toastr()->closeOnHover(true)
                    ->closeDuration(3)
                    ->positionClass('toast-top-left')
                    ->addSuccess(
                        'Cetak SEP ' . $HttpGetBpjs['metadata']['code'] . ' ' . $HttpGetBpjs['metadata']['message']
                    );
            } else {

                toastr()->closeOnHover(true)
                    ->closeDuration(3)
                    ->positionClass('toast-top-left')
                    ->addError(
                        'Cetak SEP ' . $HttpGetBpjs['metadata']['code'] . ' ' . $HttpGetBpjs['metadata']['message']
                    );
            }
            return;   // selesai - user bisa klik ulang untuk cetak
        }

        /* 3. resSep sudah ada ⇒ langsung cetak PDF */
        $data = [
            'data'    => $this->dataDaftarRi['sep']['resSep'],
            'reqData' => $this->dataDaftarRi['sep']['reqSep'],
            // opsional: kirim nomor cetakan ke-n
            'cetakKe' => ($this->dataDaftarRi['sep']['resSep']['cetakKe'] ?? 1) + 1,
        ];

        $pdfContent = PDF::loadView(
            'livewire.daftar-r-i.cetak-sep',   // view Blade khusus SEP RI
            $data
        )->output();

        toastr()->closeOnHover(true)
            ->closeDuration(3)
            ->positionClass('toast-top-left')
            ->addSuccess('Cetak SEP');

        return response()->streamDownload(
            fn() => print($pdfContent),
            'SEP_RI_' . ($this->dataDaftarRi['sep']['noSep'] ?? 'unknown') . '.pdf'
        );
    }




    private function findData($riHdrNo): void
    {
        $this->dataDaftarRi = $this->findDataRI($riHdrNo);
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
