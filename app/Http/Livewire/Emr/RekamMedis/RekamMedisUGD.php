<?php

namespace App\Http\Livewire\Emr\RekamMedis;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Traits\MasterPasien\MasterPasienTrait;



class RekamMedisUGD extends Component
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




    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////

    // Layanan RJ/RI/UGD
    public function copyAssessmentDokterLayananUGD($layananStatus = null, $dataDaftarTxn = []): void
    {

        // validate minimal
        if ($layananStatus !== 'UGD') {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Rekam medis ini bukan dari UGD, sehingga tidak bisa disalin.');
            return;
        }
        // Emit event ke parent/komponen yang mendengarkan
        // kirim txnNo & layananStatus -> parent bisa fetch data lengkap server-side
        $this->emit('requestCopyAssessmentFromUGDDokter',  $dataDaftarTxn);
    }

    public function copyAssessmentPerawatLayananUGD($layananStatus = null, $dataDaftarTxn = []): void
    {

        // validate minimal
        if ($layananStatus !== 'UGD') {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Rekam medis ini bukan dari UGD, sehingga tidak bisa disalin.');
            return;
        }
        // Emit event ke parent/komponen yang mendengarkan
        // kirim txnNo & layananStatus -> parent bisa fetch data lengkap server-side
        $this->emit('requestCopyAssessmentFromUGDPerawat',  $dataDaftarTxn);
    }

    // when new form instance
    public function mount() {}



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
                DB::raw("(CASE WHEN layanan_status='RJ' THEN (select datadaftarpolirj_json from rsview_rjkasir where rj_no=txn_no)
                                        WHEN layanan_status='UGD' THEN (select datadaftarugd_json from rsview_ugdkasir where rj_no=txn_no)
                                            ELSE null END) as datadaftar_json")

            )
            ->where('reg_no', $this->regNoRef)
            ->where('layanan_status', 'UGD')
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
            'livewire.emr.rekam-medis.rekam-medis-u-g-d',
            [
                'myQueryData' => $query->paginate(3),
                'myQueryIdentitas' => $queryIdentitas
            ]


        );
    }
    // select data end////////////////


}
