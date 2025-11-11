<?php

namespace App\Http\Livewire\Emr\RekamMedis;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Traits\MasterPasien\MasterPasienTrait;




class RekamMedis extends Component
{
    use WithPagination, MasterPasienTrait;


    // listener from blade////////////////
    protected $listeners = [];

    // primitive Variable
    public string $myTitle = 'Data Pasien';
    public string $mySnipt = 'Rekam Medis Pasien';
    public string $myProgram = 'Pasien';


    //////////////////////////////
    // Ref on top bar
    //////////////////////////////
    public $regNoRef;

    public array $dataDaftarTxn;
    public array $dataPasien;



    public bool $isOpenRekamMedisUGD;
    public bool $isOpenRekamMedisRJ;
    public bool $isOpenRekamMedisRI;


    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////

    // Layanan RJ/RI/UGD
    public ?string $currentTxnNo = null;
    public ?string $currentLayananStatus = null;
    public function openModalLayanan($txnNo = null, $layananStatus = null): void
    {
        if (!$txnNo || !$layananStatus) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Parameter tidak lengkap');
            return;
        }
        $this->currentTxnNo = $txnNo;
        $this->currentLayananStatus = $layananStatus;

        // Selalu ambil data terbaru dari DB
        $this->dataDaftarTxn = $this->fetchDatadaftarJsonFresh($txnNo, $layananStatus);
        // Set data pasien bila ada regNo
        if (isset($this->dataDaftarTxn['regNo'])) {
            $this->dataPasien = $this->findDataMasterPasien($this->dataDaftarTxn['regNo']);
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addWarning('regNo tidak ditemukan di data transaksi.');
            return;
        }

        // Buka modal sesuai layanan
        if ($layananStatus === 'RJ') {
            $this->isOpenRekamMedisRJ = true;
        } elseif ($layananStatus === 'UGD') {
            $this->isOpenRekamMedisUGD = true;
        } elseif ($layananStatus === 'RI') {
            $this->isOpenRekamMedisRI = true;
        }
    }


    public function closeModalLayanan(): void
    {
        $this->isOpenRekamMedisUGD = false;
        $this->isOpenRekamMedisRJ = false;
        $this->isOpenRekamMedisRI  = false;
    }


    public function cetakRekamMedisUGD($txnNo = null, $tipe = 'UGD', $layananStatus = 'UGD')
    {
        $txnNo         = $txnNo ?? $this->currentTxnNo;
        $layananStatus = $layananStatus ?? $this->currentLayananStatus ?? 'UGD';

        // Ambil data terbaru dari DB
        if ($txnNo && $layananStatus) {
            $this->dataDaftarTxn = $this->fetchDatadaftarJsonFresh($txnNo, $layananStatus);
            if (isset($this->dataDaftarTxn['regNo'])) {
                $this->dataPasien = $this->findDataMasterPasien($this->dataDaftarTxn['regNo']);
            } else {
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                    ->addError('Data pasien tidak ditemukan');
                return;
            }
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Parameter cetak tidak lengkap');
            return;
        }

        // Identitas RS
        $queryIdentitas = DB::table('rsmst_identitases')
            ->select('int_name', 'int_phone1', 'int_phone2', 'int_fax', 'int_address', 'int_city')
            ->first();

        // Data untuk PDF
        $data = [
            'myQueryIdentitas' => $queryIdentitas,
            'dataPasien'       => $this->dataPasien,
            'dataDaftarTxn'    => $this->dataDaftarTxn,
        ];

        // Peta view UGD (silakan sesuaikan dengan file blade yang kamu punya)
        $viewMap = [
            'UGD'             => ['livewire.emr.rekam-medis.cetak-rekam-medis-u-g-d', 'Cetak RM IGD'],
            'SUKET_ISTIRAHAT' => ['livewire.emr.rekam-medis.cetak-rekam-medis-u-g-d-suket-istirahat', 'Cetak Suket Istirahat IGD'],
            'SUKET_SEHAT'     => ['livewire.emr.rekam-medis.cetak-rekam-medis-u-g-d-suket-sehat', 'Cetak Suket Sehat IGD'],
        ];

        $tipe     = strtoupper($tipe);
        $view     = $viewMap[$tipe][0] ?? $viewMap['UGD'][0];
        $toastMsg = $viewMap[$tipe][1] ?? $viewMap['UGD'][1];

        // Generate & stream PDF
        $pdfContent = PDF::loadView($view, $data)->output();
        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
            ->addSuccess($toastMsg . ' (Fresh)');

        return response()->streamDownload(fn() => print($pdfContent), "rmUGD.pdf");
    }


    public function cetakRekamMedisRJ($txnNo = null,  $tipe = 'RJ', $layananStatus = 'RJ')
    {
        $txnNo = $txnNo ?? $this->currentTxnNo;
        $layananStatus = $layananStatus ?? $this->currentLayananStatus ?? 'RJ';

        // Ambil data terbaru dari DB
        if ($txnNo && $layananStatus) {
            $this->dataDaftarTxn = $this->fetchDatadaftarJsonFresh($txnNo, $layananStatus);
            if (isset($this->dataDaftarTxn['regNo'])) {
                $this->dataPasien = $this->findDataMasterPasien($this->dataDaftarTxn['regNo']);
            } else {
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Data pasien tidak ditemukan');
                return;
            }
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Parameter cetak tidak lengkap');
            return;
        }

        // Identitas RS
        $queryIdentitas = DB::table('rsmst_identitases')
            ->select('int_name', 'int_phone1', 'int_phone2', 'int_fax', 'int_address', 'int_city')
            ->first();

        // Data untuk PDF
        $data = [
            'myQueryIdentitas' => $queryIdentitas,
            'dataPasien'       => $this->dataPasien,
            'dataDaftarTxn'    => $this->dataDaftarTxn,
        ];

        $viewMap = [
            'RJ'               => ['livewire.emr.rekam-medis.cetak-rekam-medis-r-j', 'Cetak RM RJ'],
            'RJ1'              => ['livewire.emr.rekam-medis.cetak-rekam-medis-r-j1', 'Cetak RM RJ1'],
            'FISIO'            => ['livewire.emr.rekam-medis.cetak-rekam-medis-r-j-fisio', 'Cetak RM Fisio RJ'],
            'SUKET_ISTIRAHAT'  => ['livewire.emr.rekam-medis.cetak-rekam-medis-r-j-suket-istirahat', 'Cetak Suket Istirahat RJ'],
            'SUKET_SEHAT'      => ['livewire.emr.rekam-medis.cetak-rekam-medis-r-j-suket-sehat', 'Cetak Suket Sehat RJ'],
            'PELEPASAN_INFO'   => ['livewire.emr.rekam-medis.cetak-rekam-medis-r-j-pelepasan-informasi', 'Cetak Pelepasan Informasi RJ'],
        ];
        $tipe = strtoupper($tipe);
        $view = $viewMap[$tipe][0] ?? $viewMap['RJ'][0];
        $toastMsg = $viewMap[$tipe][1] ?? $viewMap['RJ'][1];

        // Generate & stream PDF
        $pdfContent = PDF::loadView($view, $data)->output();
        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess($toastMsg . ' (Fresh)');

        return response()->streamDownload(fn() => print($pdfContent), "rmRJ.pdf");
    }





    private function fetchDatadaftarJsonFresh(string $txnNo, string $layananStatus): array
    {
        $json = null;

        if ($layananStatus === 'RJ') {
            $json = DB::table('rsview_rjkasir')
                ->where('rj_no', $txnNo)
                ->value('datadaftarpolirj_json');
        } elseif ($layananStatus === 'UGD') {
            $json = DB::table('rsview_ugdkasir')
                ->where('rj_no', $txnNo)
                ->value('datadaftarugd_json');
        } elseif ($layananStatus === 'RI') {
            $json = DB::table('rsview_rihdrs')
                ->where('rihdr_no', $txnNo)
                ->value('datadaftarri_json');
        }

        $arr = $json ? json_decode($json, true) : [];
        return is_array($arr) ? $arr : [];
    }

    // select data start////////////////
    public function render()
    {

        //////////////////////////////////////////
        // Query ///////////////////////////////
        //////////////////////////////////////////
        $query = DB::table('rsview_ermstatus')
            ->select(
                DB::raw("to_char(txn_date,'dd/mm/yyyy hh24:mi:ss') AS txn_date"),
                DB::raw("to_char(txn_date,'yyyymmddhh24miss') AS txn_date1"),
                'txn_no',
                'reg_no',
                'erm_status',
                'layanan_status',
                'poli',
                DB::raw("(CASE
                    WHEN layanan_status='RJ' THEN (
                        select datadaftarpolirj_json
                        from rsview_rjkasir
                        where rj_no=txn_no
                    )
                    WHEN layanan_status='UGD' THEN (
                        select datadaftarugd_json
                        from rsview_ugdkasir
                        where rj_no=txn_no
                    )
                    WHEN layanan_status='RI' THEN (
                        select datadaftarri_json
                        from rsview_rihdrs
                        where rihdr_no=txn_no
                    )
                    ELSE null
                END) as datadaftar_json")
            )

            ->where('reg_no', $this->regNoRef)
            ->orderBy('txn_date1',  'desc')
            ->orderBy('layanan_status',  'desc')
            ->orderBy('poli',  'asc');


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
        ////////////////////////////////////////////////
        // end Query
        ///////////////////////////////////////////////

        return view(
            'livewire.emr.rekam-medis.rekam-medis',
            [
                'myQueryData' => $query->paginate(3),
                'myQueryIdentitas' => $queryIdentitas
            ]


        );
    }
    // select data end////////////////


}
