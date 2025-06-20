<?php

namespace App\Http\Livewire\EmrRJHari;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use App\Http\Traits\EmrRJ\EmrRJTrait;
use App\Http\Traits\MasterPasien\MasterPasienTrait;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class EmrRJHari extends Component
{
    use WithPagination, EmrRJTrait, MasterPasienTrait;
    public $file;
    // primitive Variable
    public string $myTitle = 'Upload FIle Rawat Jalan Harian';
    public string $mySnipt = 'Rawat Jalan Pasien';
    public string $myProgram = 'Pasien Rawat Jalan Harian';

    public array $myLimitPerPages = [100, 200, 300, 400, 500];
    // limit record per page -resetExcept////////////////
    public int $limitPerPage = 100;

    // my Top Bar
    public array $myTopBar = [
        'refDate' => '',

        'refShiftId' => '1',
        'refShiftDesc' => '1',
        'refShiftOptions' => [
            ['refShiftId' => '1', 'refShiftDesc' => '1'],
            ['refShiftId' => '2', 'refShiftDesc' => '2'],
            ['refShiftId' => '3', 'refShiftDesc' => '3'],
        ],

        'drId' => 'All',
        'drName' => 'All',
        'drOptions' => [
            [
                'drId' => 'All',
                'drName' => 'All'
            ]
        ],

        'klaimStatusId' => 'BPJS',
        'klaimStatusName' => 'BPJS',
        'klaimStatusOptions' => [
            [
                'klaimStatusId' => 'UMUM',
                'klaimStatusName' => 'UMUM'
            ],
            [
                'klaimStatusId' => 'BPJS',
                'klaimStatusName' => 'BPJS'
            ],
            [
                'klaimStatusId' => 'KRONIS',
                'klaimStatusName' => 'KRONIS'
            ],
        ],

    ];

    public string $refFilter = '';
    // search logic -resetExcept////////////////
    protected $queryString = [
        'refFilter' => ['except' => '', 'as' => 'cariData'],
        'page' => ['except' => 1, 'as' => 'p'],
    ];


    // reset page when myTopBar Change
    public function updatedReffilter()
    {
        $this->resetPage();
    }

    public function updatedMytopbarRefdate()
    {
        $this->resetPage();
    }




    private function gettermyTopBardrOptions(): void
    {
        $myRefdate = $this->myTopBar['refDate'];

        // Query
        $query = DB::table('rsview_rjkasir')
            ->select(
                'dr_id',
                'dr_name',
            )
            ->where(DB::raw("to_char(rj_date,'dd/mm/yyyy')"), '=', $myRefdate)
            ->groupBy('dr_id')
            ->groupBy('dr_name')
            ->orderBy('dr_name', 'desc')
            ->get();

        // loop and set Ref
        $query->each(function ($item, $key) {
            $this->myTopBar['drOptions'][$key + 1]['drId'] = $item->dr_id;
            $this->myTopBar['drOptions'][$key + 1]['drName'] = $item->dr_name;
        })->toArray();
    }

    public function settermyTopBardrOptions($drId, $drName): void
    {

        $this->myTopBar['drId'] = $drId;
        $this->myTopBar['drName'] = $drName;
        $this->resetPage();
    }

    public function settermyTopBarklaimStatusOptions($klaimStatusId, $klaimStatusName): void
    {

        $this->myTopBar['klaimStatusId'] = $klaimStatusId;
        $this->myTopBar['klaimStatusName'] = $klaimStatusName;
        $this->resetPage();
    }




    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////

    private function settermyTopBarmyTopBarrefDate(): void
    {
        $this->myTopBar['refDate'] = Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y');
    }


    public function uploadRekamMedisRJGrid($txnNo = null)
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
        $dataDaftarTxn = $this->findDataRJ($txnNo)['dataDaftarRJ'];
        $dataPasien = $this->findDataMasterPasien($dataDaftarTxn['regNo']);
        $data = [
            'myQueryIdentitas' => $queryIdentitas,
            'dataPasien' => $dataPasien,
            'dataDaftarTxn' => $dataDaftarTxn,
        ];

        $pdfContent = PDF::loadView('livewire.emr.rekam-medis.cetak-rekam-medis-r-j', $data)->output();
        $filename = Carbon::now(env('APP_TIMEZONE'))->format('dmYhis');
        $filePath = 'bpjs/' . $filename . '.pdf'; // Adjust the path as needed


        $cekFile = DB::table('rstxn_rjuploadbpjses')
            ->where('rj_no', $txnNo)
            ->where('seq_file', 3)
            ->first();

        if ($cekFile) {
            Storage::disk('local')->delete('bpjs/' . $cekFile->uploadbpjs);
            Storage::disk('local')->put($filePath, $pdfContent);
            if (Storage::disk('local')->exists($filePath)) {
                DB::table('rstxn_rjuploadbpjses')
                    ->where('rj_no', $txnNo)
                    ->where('uploadbpjs', $cekFile->uploadbpjs)
                    ->where('seq_file', 3)
                    ->update([
                        'uploadbpjs' => $filename . '.pdf',
                        'rj_no' => $txnNo,
                        'jenis_file' => 'pdf'
                    ]);
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Data berhasil diupdate " . $cekFile->uploadbpjs);
            } else {
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Data tidak berhasil diupdate " . $cekFile->uploadbpjs);
            }
        } else {
            Storage::disk('local')->put($filePath, $pdfContent);
            if (Storage::disk('local')->exists($filePath)) {
                DB::table('rstxn_rjuploadbpjses')
                    ->insert([
                        'seq_file' => 3,
                        'uploadbpjs' => $filename . '.pdf',
                        'rj_no' => $txnNo,
                        'jenis_file' => 'pdf'
                    ]);
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Data berhasil diupload " . $filename . '.pdf');
            } else {
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Data tidak berhasil diupdate " . $filename . '.pdf');
            }
        }
    }


    public function uploadRekamMedisFisioRJGrid($txnNo = null)
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
        $dataDaftarTxn = $this->findDataRJ($txnNo)['dataDaftarRJ'];
        $dataPasien = $this->findDataMasterPasien($dataDaftarTxn['regNo']);
        $data = [
            'myQueryIdentitas' => $queryIdentitas,
            'dataPasien' => $dataPasien,
            'dataDaftarTxn' => $dataDaftarTxn,
        ];

        $pdfContent = PDF::loadView('livewire.emr.rekam-medis.cetak-rekam-medis-r-j-fisio', $data)->output();
        $filename = Carbon::now(env('APP_TIMEZONE'))->format('dmYhis');
        $filePath = 'bpjs/' . $filename . '.pdf'; // Adjust the path as needed


        $cekFile = DB::table('rstxn_rjuploadbpjses')
            ->where('rj_no', $txnNo)
            ->where('seq_file', 5)
            ->first();

        if ($cekFile) {
            Storage::disk('local')->delete('bpjs/' . $cekFile->uploadbpjs);
            Storage::disk('local')->put($filePath, $pdfContent);
            if (Storage::disk('local')->exists($filePath)) {
                DB::table('rstxn_rjuploadbpjses')
                    ->where('rj_no', $txnNo)
                    ->where('uploadbpjs', $cekFile->uploadbpjs)
                    ->where('seq_file', 5)
                    ->update([
                        'uploadbpjs' => $filename . '.pdf',
                        'rj_no' => $txnNo,
                        'jenis_file' => 'pdf'
                    ]);
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Data berhasil diupdate " . $cekFile->uploadbpjs);
            } else {
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Data tidak berhasil diupdate " . $cekFile->uploadbpjs);
            }
        } else {
            Storage::disk('local')->put($filePath, $pdfContent);
            if (Storage::disk('local')->exists($filePath)) {
                DB::table('rstxn_rjuploadbpjses')
                    ->insert([
                        'seq_file' => 5,
                        'uploadbpjs' => $filename . '.pdf',
                        'rj_no' => $txnNo,
                        'jenis_file' => 'pdf'
                    ]);
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Data berhasil diupload " . $filename . '.pdf');
            } else {
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Data tidak berhasil diupdate " . $filename . '.pdf');
            }
        }
    }

    public function uploadRekamMedisFisioLembarHasilRJGrid($txnNo = null)
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
        $dataDaftarTxn = $this->findDataRJ($txnNo)['dataDaftarRJ'];
        $dataPasien = $this->findDataMasterPasien($dataDaftarTxn['regNo']);
        $data = [
            'myQueryIdentitas' => $queryIdentitas,
            'dataPasien' => $dataPasien,
            'dataDaftarTxn' => $dataDaftarTxn,
        ];

        $pdfContent = PDF::loadView('livewire.emr.rekam-medis.cetak-rekam-medis-r-j-fisio-lembar-hasil', $data)->output();
        $filename = Carbon::now(env('APP_TIMEZONE'))->format('dmYhis');
        $filePath = 'bpjs/' . $filename . '.pdf'; // Adjust the path as needed


        $cekFile = DB::table('rstxn_rjuploadbpjses')
            ->where('rj_no', $txnNo)
            ->where('seq_file', 5)
            ->first();

        if ($cekFile) {
            Storage::disk('local')->delete('bpjs/' . $cekFile->uploadbpjs);
            Storage::disk('local')->put($filePath, $pdfContent);
            if (Storage::disk('local')->exists($filePath)) {
                DB::table('rstxn_rjuploadbpjses')
                    ->where('rj_no', $txnNo)
                    ->where('uploadbpjs', $cekFile->uploadbpjs)
                    ->where('seq_file', 5)
                    ->update([
                        'uploadbpjs' => $filename . '.pdf',
                        'rj_no' => $txnNo,
                        'jenis_file' => 'pdf'
                    ]);
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Data berhasil diupdate " . $cekFile->uploadbpjs);
            } else {
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Data tidak berhasil diupdate " . $cekFile->uploadbpjs);
            }
        } else {
            Storage::disk('local')->put($filePath, $pdfContent);
            if (Storage::disk('local')->exists($filePath)) {
                DB::table('rstxn_rjuploadbpjses')
                    ->insert([
                        'seq_file' => 5,
                        'uploadbpjs' => $filename . '.pdf',
                        'rj_no' => $txnNo,
                        'jenis_file' => 'pdf'
                    ]);
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Data berhasil diupload " . $filename . '.pdf');
            } else {
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Data tidak berhasil diupdate " . $filename . '.pdf');
            }
        }
    }


    public function uploadSepRJGrid($txnNo = null)
    {
        $dataDaftarTxn = $this->findDataRJ($txnNo)['dataDaftarRJ'];
        if (!empty($dataDaftarTxn['sep']['resSep'])) {

            // cetak PDF
            $data = [
                'data' => $dataDaftarTxn['sep']['resSep'],
                'reqData' => $dataDaftarTxn['sep']['reqSep'],

            ];

            $pdfContent = PDF::loadView('livewire.daftar-r-j.cetak-sep', $data)->output();
            $filename = Carbon::now(env('APP_TIMEZONE'))->format('dmYhis');
            $filePath = 'bpjs/' . $filename . '.pdf'; // Adjust the path as needed


            $cekFile = DB::table('rstxn_rjuploadbpjses')
                ->where('rj_no', $txnNo)
                ->where('seq_file', 1)
                ->first();

            if ($cekFile) {
                Storage::disk('local')->delete('bpjs/' . $cekFile->uploadbpjs);
                Storage::disk('local')->put($filePath, $pdfContent);
                if (Storage::disk('local')->exists($filePath)) {
                    DB::table('rstxn_rjuploadbpjses')
                        ->where('rj_no', $txnNo)
                        ->where('uploadbpjs', $cekFile->uploadbpjs)
                        ->where('seq_file', 1)
                        ->update([
                            'uploadbpjs' => $filename . '.pdf',
                            'rj_no' => $txnNo,
                            'jenis_file' => 'pdf'
                        ]);
                    toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Data berhasil diupdate " . $cekFile->uploadbpjs);
                } else {
                    toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Data tidak berhasil diupdate " . $cekFile->uploadbpjs);
                }
            } else {
                Storage::disk('local')->put($filePath, $pdfContent);
                if (Storage::disk('local')->exists($filePath)) {
                    DB::table('rstxn_rjuploadbpjses')
                        ->insert([
                            'seq_file' => 1,
                            'uploadbpjs' => $filename . '.pdf',
                            'rj_no' => $txnNo,
                            'jenis_file' => 'pdf'
                        ]);
                    toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Data berhasil diupload " . $filename . '.pdf');
                } else {
                    toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Data tidak berhasil diupdate " . $filename . '.pdf');
                }
            }
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Data SEP Tidak ditemukan");
        }
    }

    public function uploadSkdpRJGrid($txnNo = null)
    {
        $dataDaftarTxn = $this->findDataRJ($txnNo)['dataDaftarRJ'];

        // cek skdp rs->baru skdp bpjs terbit
        if (!empty($dataDaftarTxn['kontrol']['noKontrolRS'])) {

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
            $dataPasien = $this->findDataMasterPasien($dataDaftarTxn['regNo']);

            // cetak PDF
            $data = [
                'myQueryIdentitas' => $queryIdentitas,
                'dataPasien' => $dataPasien,
                'dataDaftarTxn' => $dataDaftarTxn,

            ];

            $pdfContent = PDF::loadView('livewire.emr-r-j.mr-r-j.skdp-r-j.cetak-skdp-rj', $data)->output();

            $filename = Carbon::now(env('APP_TIMEZONE'))->format('dmYhis');
            $filePath = 'bpjs/' . $filename . '.pdf'; // Adjust the path as needed


            $cekFile = DB::table('rstxn_rjuploadbpjses')
                ->where('rj_no', $txnNo)
                ->where('seq_file', 4)
                ->first();

            if ($cekFile) {
                Storage::disk('local')->delete('bpjs/' . $cekFile->uploadbpjs);
                Storage::disk('local')->put($filePath, $pdfContent);
                if (Storage::disk('local')->exists($filePath)) {
                    DB::table('rstxn_rjuploadbpjses')
                        ->where('rj_no', $txnNo)
                        ->where('uploadbpjs', $cekFile->uploadbpjs)
                        ->where('seq_file', 4)
                        ->update([
                            'uploadbpjs' => $filename . '.pdf',
                            'rj_no' => $txnNo,
                            'jenis_file' => 'pdf'
                        ]);
                    toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Data berhasil diupdate " . $cekFile->uploadbpjs);
                } else {
                    toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Data tidak berhasil diupdate " . $cekFile->uploadbpjs);
                }
            } else {
                Storage::disk('local')->put($filePath, $pdfContent);
                if (Storage::disk('local')->exists($filePath)) {
                    DB::table('rstxn_rjuploadbpjses')
                        ->insert([
                            'seq_file' => 4,
                            'uploadbpjs' => $filename . '.pdf',
                            'rj_no' => $txnNo,
                            'jenis_file' => 'pdf'
                        ]);
                    toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Data berhasil diupload " . $filename . '.pdf');
                } else {
                    toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Data tidak berhasil diupdate " . $filename . '.pdf');
                }
            }
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Data SKDP Tidak ditemukan");
        }
    }


    // when new form instance
    public function mount()
    {
        $this->settermyTopBarmyTopBarrefDate();
    }


    // select data start////////////////
    public function render()
    {
        $this->gettermyTopBardrOptions();

        $mySearch = $this->refFilter;
        $myRefdate = $this->myTopBar['refDate'];
        $myRefdrId = $this->myTopBar['drId'];
        $myRefklaimStatusId = $this->myTopBar['klaimStatusId'];




        //////////////////////////////////////////
        // Query ///////////////////////////////
        //////////////////////////////////////////
        $query = DB::table('rsview_rjkasir')
            ->select(
                DB::raw("to_char(rj_date,'dd/mm/yyyy hh24:mi:ss') AS rj_date"),
                DB::raw("to_char(rj_date,'yyyymmddhh24miss') AS rj_date1"),
                'rj_no',
                'reg_no',
                'reg_name',
                'sex',
                'address',
                'thn',
                DB::raw("to_char(birth_date,'dd/mm/yyyy') AS birth_date"),
                'poli_id',
                'poli_desc',
                'dr_id',
                'dr_name',
                'klaim_id',
                'shift',
                'vno_sep',
                'no_antrian',
                'rj_status',
                'nobooking',
                'push_antrian_bpjs_status',
                'push_antrian_bpjs_json',
                'datadaftarpolirj_json',
                DB::raw("(select count(*) from lbtxn_checkuphdrs where status_rjri='RJ' and checkup_status!='B' and ref_no = rsview_rjkasir.rj_no) AS lab_status"),
                DB::raw("(select count(*) from rstxn_rjrads where rj_no = rsview_rjkasir.rj_no) AS rad_status"),
                DB::raw("(select count(*) from rstxn_rjuploadbpjses where rj_no = rsview_rjkasir.rj_no and seq_file=1) AS rjuploadbpjs_sep_count"),
                DB::raw("(select count(*) from rstxn_rjuploadbpjses where rj_no = rsview_rjkasir.rj_no and seq_file=2) AS rjuploadbpjs_grouping_count"),
                DB::raw("(select count(*) from rstxn_rjuploadbpjses where rj_no = rsview_rjkasir.rj_no and seq_file=3) AS rjuploadbpjs_rm_count"),
                DB::raw("(select count(*) from rstxn_rjuploadbpjses where rj_no = rsview_rjkasir.rj_no and seq_file=4) AS rjuploadbpjs_skdp_count"),
                DB::raw("(select count(*) from rstxn_rjuploadbpjses where rj_no = rsview_rjkasir.rj_no and seq_file=5) AS rjuploadbpjs_lain_count"),

                DB::raw("(select sum(txn_nominal) from rsview_rjstrs where rj_no = rsview_rjkasir.rj_no and txn_id = 'ADMIN UP') as admin_up"),
                DB::raw("(select sum(txn_nominal) from rsview_rjstrs where rj_no = rsview_rjkasir.rj_no and txn_id = 'JASA KARYAWAN') as jasa_karyawan"),
                DB::raw("(select sum(txn_nominal) from rsview_rjstrs where rj_no = rsview_rjkasir.rj_no and txn_id = 'JASA DOKTER') as jasa_dokter"),
                DB::raw("(select sum(txn_nominal) from rsview_rjstrs where rj_no = rsview_rjkasir.rj_no and txn_id = 'JASA MEDIS')  as jasa_medis"),
                DB::raw("(select sum(txn_nominal) from rsview_rjstrs where rj_no = rsview_rjkasir.rj_no and txn_id = 'ADMIN RAWAT JALAN') as admin_rawat_jalan"),
                DB::raw("(select sum(txn_nominal) from rsview_rjstrs where rj_no = rsview_rjkasir.rj_no and txn_id = 'LAIN-LAIN') as lain_lain"),
                DB::raw("(select sum(txn_nominal) from rsview_rjstrs where rj_no = rsview_rjkasir.rj_no and txn_id = 'RADIOLOGI')  as radiologi"),
                DB::raw("(select sum(txn_nominal) from rsview_rjstrs where rj_no = rsview_rjkasir.rj_no and txn_id = 'LABORAT')  as laboratorium"),
                DB::raw("(select sum(txn_nominal) from rsview_rjstrs where rj_no = rsview_rjkasir.rj_no and txn_id = 'OBAT') as obat"),

            )
            ->whereNotIn('rj_status', ['A', 'F'])
            // ->where('klaim_id', '!=', 'KR')
            ->where(DB::raw("to_char(rj_date,'dd/mm/yyyy')"), '=', $myRefdate)
            ->whereIn('klaim_id', function ($query) use ($myRefklaimStatusId) {
                $query->select('klaim_id')
                    ->from('rsmst_klaimtypes')
                    ->where('klaim_status', '=', $myRefklaimStatusId);
            });

        // Jika where dokter tidak kosong
        if ($myRefdrId != 'All') {
            $query->where('dr_id', $myRefdrId);
        }

        $query->where(function ($q) use ($mySearch) {
            $q->Where(DB::raw('upper(reg_name)'), 'like', '%' . strtoupper($mySearch) . '%')
                ->orWhere(DB::raw('upper(reg_no)'), 'like', '%' . strtoupper($mySearch) . '%')
                ->orWhere(DB::raw('upper(vno_sep)'), 'like', '%' . strtoupper($mySearch) . '%')
                ->orWhere(DB::raw('upper(dr_name)'), 'like', '%' . strtoupper($mySearch) . '%');
        })
            ->orderBy('rj_date1',  'desc')
            ->orderBy('shift',  'asc')
            ->orderBy('no_antrian',  'desc')
            ->orderBy('dr_name',  'asc')
        ;

        ////////////////////////////////////////////////
        // end Query
        ///////////////////////////////////////////////
        return view(
            'livewire.emr-r-j-hari.emr-r-j-hari',
            ['myQueryData' => $query->paginate($this->limitPerPage)]
        );
    }
}
