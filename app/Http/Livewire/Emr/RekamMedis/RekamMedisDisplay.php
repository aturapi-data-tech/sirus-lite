<?php

namespace App\Http\Livewire\Emr\RekamMedis;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;


use Spatie\ArrayToXml\ArrayToXml;
use Exception;


class RekamMedisDisplay extends Component
{
    use WithPagination;


    // listener from blade////////////////
    protected $listeners = [
        'syncronizeAssessmentDokterRJFindData' => 'mount'
    ];

    // primitive Variable
    public string $myTitle = 'Resume Medis Pasien';
    public string $mySnipt = 'Resume Medis Pasien';
    public string $myProgram = 'Resume Medis Pasien';


    //////////////////////////////
    // Ref on top bar
    //////////////////////////////
    public $regNoRef;
    public $rjNoRefCopyTo; //dari sini ke sana -> $rjNoRefCopyTo


    public array $dataDaftarTxn;
    public array $dataPasien;





    public bool $isOpenRekamMedisUGD;
    public bool $isOpenRekamMedisRJ;


    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////

    public function copyResep($NoRefCopyFrom, $txnStatus)
    {

        if ($txnStatus == 'RJ') {


            // cek status transaksi
            $this->checkRjStatus($this->rjNoRefCopyTo);

            // 1 cari data to 
            // 2 cari data from
            $to = $this->cariResepRJ($this->rjNoRefCopyTo);
            $from = $this->cariResepRJ($NoRefCopyFrom);

            $myResepFrom = $this->prosesDataArray(isset($from['eresep']) ? $from['eresep'] : [], 'EResepFrom');

            // 3 if eresep from (true) then update data eresep to
            if ($myResepFrom) {

                // hapus data obat
                try {
                    // remove into table transaksi
                    DB::table('rstxn_rjobats')
                        ->where('rj_no', $this->rjNoRefCopyTo)
                        ->delete();
                } catch (Exception $e) {
                    // display an error to user
                    dd($e->getMessage());
                }

                $to['eresep'] = $myResepFrom;

                // insert data obat loop
                try {
                    // ?\
                    // select nvl(max(rjobat_dtl)+1,1) into :rstxn_rjobats.rjobat_dtl from rstxn_rjobats;
                    foreach ($to['eresep']  as $toEresep) {
                        $lastInserted = DB::table('rstxn_rjobats')
                            ->select(DB::raw("nvl(max(rjobat_dtl)+1,1) as rjobat_dtl_max"))
                            ->first();
                        // insert into table transaksi
                        DB::table('rstxn_rjobats')
                            ->insert([
                                'rjobat_dtl' => $lastInserted->rjobat_dtl_max,
                                'rj_no' => $this->rjNoRefCopyTo,
                                'product_id' => $toEresep['productId'],
                                'qty' => $toEresep['qty'],
                                'price' => $toEresep['productPrice'],
                                'rj_carapakai' => $toEresep['signaX'],
                                'rj_kapsul' => $toEresep['signaHari'],
                                'rj_takar' => 'Tablet',
                                'catatan_khusus' => $toEresep['catatanKhusus'],
                                'exp_date' => DB::raw("to_date('" . $to['rjDate'] . "','dd/mm/yyyy hh24:mi:ss')+30"),
                                'etiket_status' => 1,
                            ]);
                    }
                    //
                } catch (Exception $e) {
                    // display an error to user
                    dd($e->getMessage());
                }

                // 4update database to
            }











            // racikan

            $myResepRacikanFrom = $this->prosesDataArray(isset($from['eresepRacikan']) ? $from['eresepRacikan'] : [], 'EResepRacikanFrom');

            // 3if eresepRacikan from (true) then update data eresepRacikan to
            if ($myResepRacikanFrom) {

                // hapus data obat
                try {
                    // remove into table transaksi
                    DB::table('rstxn_rjobatracikans')
                        ->where('rj_no', $this->rjNoRefCopyTo)
                        ->delete();
                } catch (Exception $e) {
                    // display an error to user
                    dd($e->getMessage());
                }

                $to['eresepRacikan'] = $myResepRacikanFrom;

                // insert data obat loop
                try {
                    // ?\
                    // select nvl(max(rjobat_dtl)+1,1) into :rstxn_rjobatracikans.rjobat_dtl from rstxn_rjobatracikans;
                    foreach ($to['eresepRacikan']  as $toEresepRacikan) {
                        $lastInserted = DB::table('rstxn_rjobatracikans')
                            ->select(DB::raw("nvl(max(rjobat_dtl)+1,1) as rjobat_dtl_max"))
                            ->first();
                        // insert into table transaksi
                        DB::table('rstxn_rjobatracikans')
                            ->insert([
                                'rjobat_dtl' => $lastInserted->rjobat_dtl_max,
                                'rj_no' => $this->rjNoRefCopyTo,
                                // 'product_id' => $toEresepRacikan['productId'],
                                'product_name' => $toEresepRacikan['productName'],
                                'qty' => $toEresepRacikan['qty'],
                                // 'price' => $toEresepRacikan['productPrice'],
                                // 'rj_carapakai' => $toEresepRacikan['signaX'],
                                // 'rj_kapsul' => $toEresepRacikan['signaHari'],
                                'sedia' => $toEresepRacikan['sedia'],
                                'catatan' => $toEresepRacikan['catatan'],
                                'qty' => $toEresepRacikan['qty'],
                                'catatan_khusus' => $toEresepRacikan['catatanKhusus'],
                                'no_racikan' => $toEresepRacikan['noRacikan'],

                                'rj_takar' => 'Tablet',
                                'exp_date' => DB::raw("to_date('" . $to['rjDate'] . "','dd/mm/yyyy hh24:mi:ss')+30"),
                                'etiket_status' => 1,
                            ]);
                    }
                    //
                } catch (Exception $e) {
                    // display an error to user
                    dd($e->getMessage());
                }

                // 4update database to
            }








            // terapi

            if (isset($to['eresep'])) {
                $eresep = '';
                foreach ($to['eresep'] as  $value) {
                    $racikanNonRacikan = $value['jenisKeterangan'] == 'NonRacikan' ? 'N' : '';
                    $eresep .=  '(' . $racikanNonRacikan . ')' . ' ' . $value['productName'] . ' /' . $value['qty'] . ' /' . $value['catatanKhusus'] . PHP_EOL;
                }
                $to['perencanaan']['terapi']['terapi'] = $to['perencanaan']['terapi']['terapi']
                    . $eresep;
            }

            if (isset($to['eresepRacikan'])) {
                $eresepRacikan = '' . PHP_EOL;
                foreach ($to['eresepRacikan'] as  $value) {
                    $racikanNonRacikan = $value['jenisKeterangan'] == 'NonRacikan' ? 'N' : '';
                    $eresepRacikan .= $racikanNonRacikan . '(' . $value['noRacikan'] . ') ' . $value['productName'] . ' ' . $value['sedia'] . ' /' . $value['catatan'] . ' /' . $value['qty'] . ' /' . $value['catatanKhusus'] . PHP_EOL;
                }
                $to['perencanaan']['terapi']['terapi'] = $to['perencanaan']['terapi']['terapi']
                    . $eresepRacikan;
            }




            $this->updateDataRj($this->rjNoRefCopyTo, $to);
        } else {
            dd('Fitur Copy Terapi pasien masih dalam proses pengembangan');
        }
    }

    private function cariResepRJ($rjNo)
    {
        // update table trnsaksi
        $cari = DB::table('rstxn_rjhdrs')
            ->select('datadaftarpolirj_json')
            ->where('rj_no', $rjNo)
            ->first();

        if (isset($cari->datadaftarpolirj_json)) {
            if ($cari->datadaftarpolirj_json) {
                return json_decode($cari->datadaftarpolirj_json, true);
            }
        }
        return [];
    }

    private function updateDataRj($rjNo, $dataDaftarPoliRJArr): void
    {
        // update table trnsaksi
        DB::table('rstxn_rjhdrs')
            ->where('rj_no', $rjNo)
            ->update([
                'datadaftarpolirj_json' => json_encode($dataDaftarPoliRJArr, true),
                'datadaftarpolirj_xml' => ArrayToXml::convert($dataDaftarPoliRJArr),
            ]);

        $this->emit('syncronizeAssessmentDokterRJFindData');
        $this->emit('toastr-success', "Data Resep berhasil disimpan.");
    }

    private function prosesDataArray(array $arr, string $arrName)
    {
        $myArr = $arr ? $arr : [];

        if (!$myArr) {
            $this->emit('toastr-error', "Data " . $arrName . " tidak ditemukan.");
        }

        return $myArr;
    }

    private function checkRjStatus($rjNo)
    {
        $lastInserted = DB::table('rstxn_rjhdrs')
            ->select('rj_status')
            ->where('rj_no', $rjNo)
            ->first();

        if ($lastInserted->rj_status !== 'A') {
            $this->emit('toastr-error', "Pasien Sudah Pulang, Trasaksi Terkunci.");
            return (dd('Pasien Sudah Pulang, Trasaksi Terkuncixx.'));
        }
    }


    // when new form instance
    public function mount()
    {
        $this->render();
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
                DB::raw("(CASE WHEN layanan_status='RJ' THEN (select datadaftarpolirj_json from rsview_rjkasir where rj_no=txn_no)
                                        WHEN layanan_status='UGD' THEN (select datadaftarugd_json from rsview_ugdkasir where rj_no=txn_no)
                                            ELSE null END) as datadaftar_json")

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
            'livewire.emr.rekam-medis.rekam-medis-display',
            [
                'myQueryData' => $query->paginate(3),
                'myQueryIdentitas' => $queryIdentitas
            ]


        );
    }
    // select data end////////////////


}
