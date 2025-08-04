<?php

namespace App\Http\Livewire\GroupingBPJS;


use Illuminate\Support\Facades\DB;
use App\Http\Traits\EmrRJ\EmrRJTrait;
use App\Http\Traits\EmrUGD\EmrUGDTrait;


use Livewire\Component;
use Illuminate\Support\Collection;

use Livewire\WithFileUploads;
use Livewire\WithPagination;


class GroupingBPJSRJperDokter extends Component
{
    use WithPagination, WithFileUploads, EmrRJTrait, EmrUGDTrait;

    // primitive Variable
    public string $myTitle = 'FIle Grouping BPJS Rawat Jalan';
    public string $mySnipt = 'Grouping BPJS Rawat Jalan';
    public string $myProgram = 'Grouping BPJS Rawat Jalan';

    public array $myLimitPerPages = [10, 50, 100, 150, 200, 1000];
    // limit record per page -resetExcept////////////////
    public int $limitPerPage = 10;

    protected $listeners = [
        'syncronizeDataGroupingBPJSRJperDokter' => 'render'

    ];

    // my Top Bar
    public array $allSepPerDokter = [];

    public array $myQueryDataSum = [];

    // search logic -resetExcept////////////////
    protected $queryRJString = [
        'page' => ['except' => 1, 'as' => 'p'],
    ];


    public function updatedMyTopBarRefDate()
    {
        $this->hitungTotallAll($this->allSepPerDokter);
    }



    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////

    private function buatTarifRawatJalan(object $row): array
    {
        return [
            'Admin UP'           => $row->admin_up            ?? 0,
            'Jasa Karyawan'      => $row->jasa_karyawan       ?? 0,
            'Jasa Dokter'        => $row->jasa_dokter         ?? 0,
            'Jasa Medis'         => $row->jasa_medis          ?? 0,
            'Admin RJ'           => $row->admin_rawat_jalan   ?? 0,
            'Radiologi'          => $row->radiologi           ?? 0,
            'Laboratorium'       => $row->laboratorium        ?? 0,
            'Obat'               => $row->obat                ?? 0,
            'Lain-lain'          => $row->lain_lain           ?? 0,
        ];
    }

    private function buatTarifUGD(object $row): array
    {
        return [
            'Admin UGD'       => $row->admin_ugd      ?? 0,
            'Admin UP'        => $row->admin_up       ?? 0,
            'Jasa Karyawan'   => $row->jasa_karyawan  ?? 0,
            'Jasa Dokter'     => $row->jasa_dokter    ?? 0,
            'Jasa Medis'      => $row->jasa_medis     ?? 0,
            'Radiologi'       => $row->radiologi      ?? 0,
            'Laboratorium'    => $row->laboratorium   ?? 0,
            'Obat'            => $row->obat           ?? 0,
            'Lain-lain'       => $row->lain_lain      ?? 0,
            'Trf RJ'          => $row->trf_rj         ?? 0,
        ];
    }

    private function perhitunganTarifRawatJalanUGD(\Illuminate\Pagination\LengthAwarePaginator $paginator)
    {
        // get raw collection & IDs
        $collection = $paginator->getCollection();
        $ids        = $collection->pluck('rj_no')->all();

        // 2. Hitung semua subtotal Rawat Jalan
        $totalsRJ = DB::table('rsview_rjstrs')
            ->select(
                'rj_no',
                // 'h.datadaftarpolirj_json',
                DB::raw("SUM(CASE WHEN txn_id = 'ADMIN UP'          THEN txn_nominal ELSE 0 END) as admin_up"),
                DB::raw("SUM(CASE WHEN txn_id = 'JASA KARYAWAN'     THEN txn_nominal ELSE 0 END) as jasa_karyawan"),
                DB::raw("SUM(CASE WHEN txn_id = 'JASA DOKTER'       THEN txn_nominal ELSE 0 END) as jasa_dokter"),
                DB::raw("SUM(CASE WHEN txn_id = 'JASA MEDIS'        THEN txn_nominal ELSE 0 END) as jasa_medis"),
                DB::raw("SUM(CASE WHEN txn_id = 'ADMIN RAWAT JALAN' THEN txn_nominal ELSE 0 END) as admin_rawat_jalan"),
                DB::raw("SUM(CASE WHEN txn_id = 'LAIN-LAIN'         THEN txn_nominal ELSE 0 END) as lain_lain"),
                DB::raw("SUM(CASE WHEN txn_id = 'RADIOLOGI'         THEN txn_nominal ELSE 0 END) as radiologi"),
                DB::raw("SUM(CASE WHEN txn_id = 'LABORAT'           THEN txn_nominal ELSE 0 END) as laboratorium"),
                DB::raw("SUM(CASE WHEN txn_id = 'OBAT'              THEN txn_nominal ELSE 0 END) as obat")
            )
            ->whereIn('rj_no', $ids)
            ->groupBy('rj_no')
            // ->groupBy('h.datadaftarpolirj_json')
            ->get()
            ->keyBy('rj_no');
        // 3. Hitung semua subtotal UGD
        $totalsUGD = DB::table('rsview_ugdstrs')
            ->select(
                'rj_no',
                // 'h.datadaftarugd_json',
                DB::raw("SUM(CASE WHEN txn_id = 'ADMIN UGD'       THEN txn_nominal ELSE 0 END) as admin_ugd"),
                DB::raw("SUM(CASE WHEN txn_id = 'ADMIN UP'        THEN txn_nominal ELSE 0 END) as admin_up"),
                DB::raw("SUM(CASE WHEN txn_id = 'JASA KARYAWAN'   THEN txn_nominal ELSE 0 END) as jasa_karyawan"),
                DB::raw("SUM(CASE WHEN txn_id = 'JASA DOKTER'     THEN txn_nominal ELSE 0 END) as jasa_dokter"),
                DB::raw("SUM(CASE WHEN txn_id = 'JASA MEDIS'      THEN txn_nominal ELSE 0 END) as jasa_medis"),
                DB::raw("SUM(CASE WHEN txn_id = 'RADIOLOGI'       THEN txn_nominal ELSE 0 END) as radiologi"),
                DB::raw("SUM(CASE WHEN txn_id = 'LABORAT'         THEN txn_nominal ELSE 0 END) as laboratorium"),
                DB::raw("SUM(CASE WHEN txn_id = 'OBAT'            THEN txn_nominal ELSE 0 END) as obat"),
                DB::raw("SUM(CASE WHEN txn_id = 'LAIN-LAIN'       THEN txn_nominal ELSE 0 END) as lain_lain"),
                DB::raw("SUM(CASE WHEN txn_id = 'TRF RJ'          THEN txn_nominal ELSE 0 END) as trf_rj")
            )
            ->whereIn('rj_no', $ids)
            ->groupBy('rj_no')
            // ->groupBy('h.datadaftarugd_json')
            ->get()
            ->keyBy('rj_no');


        $jsonRJ = DB::table('rsview_rjkasir')
            ->whereIn('rj_no', $ids)
            ->pluck('datadaftarpolirj_json', 'rj_no');   // [ rj_no => json ]

        $jsonUGD = DB::table('rsview_ugdkasir')
            ->whereIn('rj_no', $ids)
            ->pluck('datadaftarugd_json', 'rj_no');

        // 2) Hitung subtotal seperti sebelumnya (tanpa JSON)



        // 4. Map tiap baris: kalau poli_id='UGD' pakai UGD, selain itu RJ
        $new = $collection->map(function ($row) use ($totalsRJ, $totalsUGD, $jsonRJ, $jsonUGD) {
            $key = $row->rj_no;
            $totalsRJ = $totalsRJ->keyBy('rj_no');
            $totalsUGD = $totalsUGD->keyBy('rj_no');

            if (strtoupper($row->poli_desc) == 'UGD') {
                // ambil dari UGD
                $t = $totalsUGD->get($key, (object)[]);
                $row->datadaftarugd_json = $jsonUGD[$key] ?? null;
                $row->admin_ugd        = $t->admin_ugd      ?? 0;
                $row->admin_up         = $t->admin_up       ?? 0;
                $row->jasa_karyawan    = $t->jasa_karyawan  ?? 0;
                $row->jasa_dokter      = $t->jasa_dokter    ?? 0;
                $row->jasa_medis       = $t->jasa_medis     ?? 0;
                $row->radiologi        = $t->radiologi      ?? 0;
                $row->laboratorium     = $t->laboratorium   ?? 0;
                $row->obat             = $t->obat           ?? 0;
                $row->lain_lain        = $t->lain_lain      ?? 0;
                $row->trf_rj           = $t->trf_rj         ?? 0;
                // kalkulasi tarif UGD
                $row->tarif_detail = $this->buatTarifUGD($row);
            } else {
                // ambil dari RJ
                $t = $totalsRJ->get($key, (object)[]);
                $row->datadaftarpolirj_json = $jsonRJ[$key] ?? null;
                $row->admin_up           = $t->admin_up            ?? 0;
                $row->jasa_karyawan      = $t->jasa_karyawan       ?? 0;
                $row->jasa_dokter        = $t->jasa_dokter         ?? 0;
                $row->jasa_medis         = $t->jasa_medis          ?? 0;
                $row->admin_rawat_jalan  = $t->admin_rawat_jalan   ?? 0;
                $row->lain_lain          = $t->lain_lain           ?? 0;
                $row->radiologi          = $t->radiologi           ?? 0;
                $row->laboratorium       = $t->laboratorium        ?? 0;
                $row->obat               = $t->obat                ?? 0;

                // kalkulasi tarif Rawat Jalan
                $row->tarif_detail = $this->buatTarifRawatJalan($row);
            }

            // 5. Hitung total semua komponen
            $row->tarif_total = array_sum($row->tarif_detail);
            return $row;
        });


        $paginator->setCollection($new);

        return $paginator;
    }

    private function perhitunganTarifRawatJalanUGDAll(Collection $collection): Collection
    {

        $ids        = $collection->pluck('rj_no')->all();

        // 2. Hitung semua subtotal Rawat Jalan
        $totalsRJ = DB::table('rsview_rjstrs')
            ->select(
                'rj_no',
                // 'h.datadaftarpolirj_json',
                DB::raw("SUM(CASE WHEN txn_id = 'ADMIN UP'          THEN txn_nominal ELSE 0 END) as admin_up"),
                DB::raw("SUM(CASE WHEN txn_id = 'JASA KARYAWAN'     THEN txn_nominal ELSE 0 END) as jasa_karyawan"),
                DB::raw("SUM(CASE WHEN txn_id = 'JASA DOKTER'       THEN txn_nominal ELSE 0 END) as jasa_dokter"),
                DB::raw("SUM(CASE WHEN txn_id = 'JASA MEDIS'        THEN txn_nominal ELSE 0 END) as jasa_medis"),
                DB::raw("SUM(CASE WHEN txn_id = 'ADMIN RAWAT JALAN' THEN txn_nominal ELSE 0 END) as admin_rawat_jalan"),
                DB::raw("SUM(CASE WHEN txn_id = 'LAIN-LAIN'         THEN txn_nominal ELSE 0 END) as lain_lain"),
                DB::raw("SUM(CASE WHEN txn_id = 'RADIOLOGI'         THEN txn_nominal ELSE 0 END) as radiologi"),
                DB::raw("SUM(CASE WHEN txn_id = 'LABORAT'           THEN txn_nominal ELSE 0 END) as laboratorium"),
                DB::raw("SUM(CASE WHEN txn_id = 'OBAT'              THEN txn_nominal ELSE 0 END) as obat")
            )
            ->whereIn('rj_no', $ids)
            ->groupBy('rj_no')
            // ->groupBy('h.datadaftarpolirj_json')
            ->get()
            ->keyBy('rj_no');
        // 3. Hitung semua subtotal UGD
        $totalsUGD = DB::table('rsview_ugdstrs')
            ->select(
                'rj_no',
                // 'h.datadaftarugd_json',
                DB::raw("SUM(CASE WHEN txn_id = 'ADMIN UGD'       THEN txn_nominal ELSE 0 END) as admin_ugd"),
                DB::raw("SUM(CASE WHEN txn_id = 'ADMIN UP'        THEN txn_nominal ELSE 0 END) as admin_up"),
                DB::raw("SUM(CASE WHEN txn_id = 'JASA KARYAWAN'   THEN txn_nominal ELSE 0 END) as jasa_karyawan"),
                DB::raw("SUM(CASE WHEN txn_id = 'JASA DOKTER'     THEN txn_nominal ELSE 0 END) as jasa_dokter"),
                DB::raw("SUM(CASE WHEN txn_id = 'JASA MEDIS'      THEN txn_nominal ELSE 0 END) as jasa_medis"),
                DB::raw("SUM(CASE WHEN txn_id = 'RADIOLOGI'       THEN txn_nominal ELSE 0 END) as radiologi"),
                DB::raw("SUM(CASE WHEN txn_id = 'LABORAT'         THEN txn_nominal ELSE 0 END) as laboratorium"),
                DB::raw("SUM(CASE WHEN txn_id = 'OBAT'            THEN txn_nominal ELSE 0 END) as obat"),
                DB::raw("SUM(CASE WHEN txn_id = 'LAIN-LAIN'       THEN txn_nominal ELSE 0 END) as lain_lain"),
                DB::raw("SUM(CASE WHEN txn_id = 'TRF RJ'          THEN txn_nominal ELSE 0 END) as trf_rj")
            )
            ->whereIn('rj_no', $ids)
            ->groupBy('rj_no')
            // ->groupBy('h.datadaftarugd_json')
            ->get()
            ->keyBy('rj_no');


        $jsonRJ = DB::table('rsview_rjkasir')
            ->whereIn('rj_no', $ids)
            ->pluck('datadaftarpolirj_json', 'rj_no');   // [ rj_no => json ]

        $jsonUGD = DB::table('rsview_ugdkasir')
            ->whereIn('rj_no', $ids)
            ->pluck('datadaftarugd_json', 'rj_no');

        $new = $collection->map(function ($row) use ($totalsRJ, $totalsUGD, $jsonRJ, $jsonUGD) {
            $key = $row->rj_no;
            $totalsRJ = $totalsRJ->keyBy('rj_no');
            $totalsUGD = $totalsUGD->keyBy('rj_no');
            if (strtoupper($row->poli_desc) == 'UGD') {
                // ambil dari UGD
                $t = $totalsUGD->get($key, (object)[]);
                $row->datadaftarugd_json = $jsonUGD[$key] ?? null;
                $row->admin_ugd        = $t->admin_ugd      ?? 0;
                $row->admin_up         = $t->admin_up       ?? 0;
                $row->jasa_karyawan    = $t->jasa_karyawan  ?? 0;
                $row->jasa_dokter      = $t->jasa_dokter    ?? 0;
                $row->jasa_medis       = $t->jasa_medis     ?? 0;
                $row->radiologi        = $t->radiologi      ?? 0;
                $row->laboratorium     = $t->laboratorium   ?? 0;
                $row->obat             = $t->obat           ?? 0;
                $row->lain_lain        = $t->lain_lain      ?? 0;
                $row->trf_rj           = $t->trf_rj         ?? 0;
                // kalkulasi tarif UGD
                $row->tarif_detail = $this->buatTarifUGD($row);
            } else {
                // ambil dari RJ
                $t = $totalsRJ->get($key, (object)[]);
                $row->datadaftarpolirj_json = $jsonRJ[$key] ?? null;
                $row->admin_up           = $t->admin_up            ?? 0;
                $row->jasa_karyawan      = $t->jasa_karyawan       ?? 0;
                $row->jasa_dokter        = $t->jasa_dokter         ?? 0;
                $row->jasa_medis         = $t->jasa_medis          ?? 0;
                $row->admin_rawat_jalan  = $t->admin_rawat_jalan   ?? 0;
                $row->lain_lain          = $t->lain_lain           ?? 0;
                $row->radiologi          = $t->radiologi           ?? 0;
                $row->laboratorium       = $t->laboratorium        ?? 0;
                $row->obat               = $t->obat                ?? 0;
                // kalkulasi tarif Rawat Jalan
                $row->tarif_detail = $this->buatTarifRawatJalan($row);
            }
            // 5. Hitung total semua komponen
            $row->tarif_total = array_sum($row->tarif_detail);
            return $row;
        });

        return $new;
    }

    public function hitungTotallAll($myrefAllSepPerDokter)
    {
        $allNoSep = collect($myrefAllSepPerDokter)->pluck('noSep')->all();
        $allTxnNo = collect($myrefAllSepPerDokter)->pluck('txnNo')->all();
        //////////////////////////////////////////
        // Query Khusus BPJS///////////////////////////////
        //////////////////////////////////////////
        $queryRJ = DB::table('rsview_rjkasir')
            ->select(
                DB::raw("to_char(rj_date,'dd/mm/yyyy hh24:mi:ss') AS rj_date"),
                DB::raw("to_char(rj_date,'yyyymmddhh24miss') AS rj_date1"),
                'vno_sep',
                'rj_no',
                'reg_no',
                'reg_name',
                'poli_id',
                'poli_desc',
                'dr_id',
                'dr_name',

            )
            ->whereIn('vno_sep', $allNoSep)
            ->whereIn('rj_no', $allTxnNo);

        $queryUGD = DB::table('rsview_ugdkasir')
            ->select(
                DB::raw("to_char(rj_date,'dd/mm/yyyy hh24:mi:ss') AS rj_date"),
                DB::raw("to_char(rj_date,'yyyymmddhh24miss') AS rj_date1"),
                'vno_sep',
                'rj_no',
                'reg_no',
                'reg_name',
                DB::raw("1000 AS poli_id"),
                DB::raw("'UGD' AS poli_desc"),
                'dr_id',
                'dr_name',

            )
            ->whereIn('vno_sep', $allNoSep)
            ->whereIn('rj_no', $allTxnNo);

        $qUnionRJUGD = $queryRJ->unionAll($queryUGD);

        // //Query Total
        $queryRJUGDGridAll =  DB::query()
            ->fromSub($qUnionRJUGD, 'u')     // Laravel≥5.5 / Yajra Oci8 mendukung
            ->orderBy('u.rj_date1', 'desc')
            ->get();
        $queryRJUGDGridAll = $this->perhitunganTarifRawatJalanUGDAll($queryRJUGDGridAll);


        $jumlahDisetujuiRJ       = 0;
        $jmlKlaimDisetujuiRJ     = 0;
        $jumlahDisetujuiUGD      = 0;
        $jmlKlaimDisetujuiUGD    = 0;

        $jml_klaim               = 0;    // total baris klaim (UGD + RJ)
        $total_klaim             = 0;    // akumulasi tarif_total

        foreach ($queryRJUGDGridAll as $row) {
            // setiap baris dihitung sebagai 1 klaim
            $jml_klaim++;
            // akumulasi tarif_total
            $total_klaim += $row->tarif_total;

            if (strtoupper($row->poli_desc) === 'UGD') {
                // UGD
                $json = json_decode($row->datadaftarugd_json, true);
                if (!empty($json['umbalBpjs']['disetujui'])) {
                    $jumlahDisetujuiUGD   += (int) $json['umbalBpjs']['disetujui'];
                    $jmlKlaimDisetujuiUGD++;
                }
            } else {
                // Rawat Jalan
                $json = json_decode($row->datadaftarpolirj_json, true);
                if (!empty($json['umbalBpjs']['disetujui'])) {
                    $jumlahDisetujuiRJ    += (int) $json['umbalBpjs']['disetujui'];
                    $jmlKlaimDisetujuiRJ++;
                }
            }
        }

        $this->myQueryDataSum['jml_all'] = $jml_klaim;
        $this->myQueryDataSum['total_all'] = $total_klaim;
        $this->myQueryDataSum['disetujui_bpjs'] = $jumlahDisetujuiUGD + $jumlahDisetujuiRJ;
        $this->myQueryDataSum['jml_disetujui_bpjs'] = $jmlKlaimDisetujuiUGD + $jmlKlaimDisetujuiRJ;
    }

    public function mount()
    {
        $this->hitungTotallAll($this->allSepPerDokter);
    }




    // select data start////////////////
    public function render()
    {

        $myrefAllSepPerDokter = $this->allSepPerDokter;

        $allNoSep = collect($myrefAllSepPerDokter)->pluck('noSep')->all();
        $allTxnNo = collect($myrefAllSepPerDokter)->pluck('txnNo')->all();

        // dd($myrefAllSepPerDokter);
        //////////////////////////////////////////
        // Query Khusus BPJS///////////////////////////////
        //////////////////////////////////////////
        $queryRJ = DB::table('rsview_rjkasir')
            ->select(
                DB::raw("to_char(rj_date,'dd/mm/yyyy hh24:mi:ss') AS rj_date"),
                DB::raw("to_char(rj_date,'yyyymmddhh24miss') AS rj_date1"),
                'vno_sep',
                'rj_no',
                'reg_no',
                'reg_name',
                'poli_id',
                'poli_desc',
                'dr_id',
                'dr_name',

            )
            ->whereIn('vno_sep', $allNoSep)
            ->whereIn('rj_no', $allTxnNo);



        $queryUGD = DB::table('rsview_ugdkasir')
            ->select(
                DB::raw("to_char(rj_date,'dd/mm/yyyy hh24:mi:ss') AS rj_date"),
                DB::raw("to_char(rj_date,'yyyymmddhh24miss') AS rj_date1"),
                'vno_sep',
                'rj_no',
                'reg_no',
                'reg_name',
                DB::raw("1000 AS poli_id"),
                DB::raw("'UGD' AS poli_desc"),
                'dr_id',
                'dr_name',

            )
            ->whereIn('vno_sep', $allNoSep)
            ->whereIn('rj_no', $allTxnNo);


        $qUnionRJUGD = $queryRJ->unionAll($queryUGD);
        $queryRJUGDGridPagination = DB::query()
            ->fromSub($qUnionRJUGD, 'u')     // Laravel≥5.5 / Yajra Oci8 mendukung
            ->orderBy('u.rj_date1', 'desc')
            ->paginate($this->limitPerPage);

        $queryRJUGDGrid = $this->perhitunganTarifRawatJalanUGD($queryRJUGDGridPagination);

        return view(
            'livewire.grouping-b-p-j-s.grouping-b-p-j-s-r-jper-dokter',
            [
                'myQueryData' => $queryRJUGDGrid,
            ]
        );
    }
    // select data end////////////////


}
