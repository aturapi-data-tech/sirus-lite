<?php

namespace App\Http\Livewire\EmrRI\AdministrasiRI;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

use App\Http\Traits\EmrRI\EmrRITrait;


class AdministrasiRI extends Component
{
    use WithPagination, EmrRITrait;

    protected $listeners = [
        'syncronizeAssessmentDokterRIFindData' => 'sumAll',
        'syncronizeAssessmentPerawatRIFindData' => 'sumAll'
    ];

    // dataDaftarRi RJ
    public $riHdrNoRef;

    public int $sumRsAdmin;
    public int $sumRjAdmin;
    public int $sumPoliPrice;

    public int $sumJasaKaryawan;
    public int $sumJasaDokter;
    public int $sumJasaMedis;

    public int $sumObat;
    public int $sumLaboratorium;
    public int $sumRadiologi;

    public int $sumLainLain;
    public int $sumtrfRJ;

    public int $sumTotalRJ;


    public string $activeTabAdministrasi = "JasaKaryawan";
    public array $EmrMenuAdministrasi = [
        [
            'ermMenuId' => 'JasaKaryawan',
            'ermMenuName' => 'Jasa Karyawan'
        ],
        [
            'ermMenuId' => 'JasaDokter',
            'ermMenuName' => 'Jasa Dokter'
        ],
        [
            'ermMenuId' => 'JasaMedis',
            'ermMenuName' => 'Jasa Medis'
        ],
        [
            'ermMenuId' => 'Obat',
            'ermMenuName' => 'Obat'
        ],
        [
            'ermMenuId' => 'Laboratorium',
            'ermMenuName' => 'Laboratorium'
        ],
        [
            'ermMenuId' => 'Radiologi',
            'ermMenuName' => 'Radiologi'
        ],
        [
            'ermMenuId' => 'LainLain',
            'ermMenuName' => 'Lain-Lain'
        ],

    ];


    public function sumAll()
    {
        $this->sumAdmin();
    }

    private function sumAdmin()
    {
        $sumAdmin = $this->findData($this->riHdrNoRef);

        $this->sumRsAdmin = $sumAdmin['rsAdmin'] ? $sumAdmin['rsAdmin'] : 0;
        $this->sumRjAdmin = $sumAdmin['rjAdmin'] ? $sumAdmin['rjAdmin'] : 0;
        $this->sumPoliPrice = $sumAdmin['poliPrice'] ? $sumAdmin['poliPrice'] : 0;

        $this->sumJasaKaryawan = isset($sumAdmin['JasaKaryawan']) ? collect($sumAdmin['JasaKaryawan'])->sum('JasaKaryawanPrice') : 0;
        $this->sumJasaMedis = isset($sumAdmin['JasaMedis']) ? collect($sumAdmin['JasaMedis'])->sum('JasaMedisPrice') : 0;
        $this->sumJasaDokter = isset($sumAdmin['JasaDokter']) ? collect($sumAdmin['JasaDokter'])->sum('JasaDokterPrice') : 0;


        $this->sumObat = collect($sumAdmin['rjObat'])->sum((function ($obat) {
            return $obat['qty'] * $obat['price'];
        }));

        $this->sumLaboratorium = collect($sumAdmin['rjLab'])->sum((function ($rjLab) {
            return $rjLab['lab_price'];
        }));

        $this->sumRadiologi = collect($sumAdmin['rjRad'])->sum((function ($rjRad) {
            return $rjRad['rad_price'];
        }));


        $this->sumLainLain = isset($sumAdmin['LainLain']) ? collect($sumAdmin['LainLain'])->sum('LainLainPrice') : 0;

        $this->sumtrfRJ = collect($sumAdmin['rjtrfRj'])->sum((function ($trfRj) {
            return $trfRj['rj_admin'] + $trfRj['poli_price'] + $trfRj['acte_price'] + $trfRj['actp_price'] + $trfRj['actd_price'] + $trfRj['obat'] + $trfRj['lab'] + $trfRj['rad'] + $trfRj['other'] + $trfRj['rs_admin'];
        }));




        $this->sumTotalRJ = $this->sumPoliPrice + $this->sumRjAdmin + $this->sumRsAdmin  + $this->sumJasaKaryawan + $this->sumJasaDokter + $this->sumJasaMedis + $this->sumLainLain + $this->sumObat + $this->sumLaboratorium + $this->sumRadiologi + $this->sumtrfRJ;
    }


    public function setSelesaiAdministrasiStatus($rjNo)
    {

        $dataDaftarRi = $this->findData($rjNo);

        if (isset($dataDaftarRi['AdministrasiRj']) == false) {
            $dataDaftarRi['AdministrasiRj'] = [
                'userLog' => auth()->user()->myuser_name,
                'userLogDate' => Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s')
            ];

            // DB::table('rstxn_rihdrs')
            //     ->where('rihdr_no', $rjNo)
            //     ->update([
            //         'datadaftarri_json' => json_encode($dataDaftarRi, true),
            //         'datadaftarri_xml' => ArrayToXml::convert($dataDaftarRi),
            //     ]);

            $this->updateJsonRI($rjNo, $dataDaftarRi);


            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Administrasi berhasil disimpan.");
            $this->emit('syncronizeAssessmentDokterRIFindData');
            $this->emit('syncronizeAssessmentPerawatRIFindData');
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Administrasi sudah tersimpan oleh." . $dataDaftarRi['AdministrasiRj']['userLog']);
        }
    }

    private function findData($rjNo): array
    {
        $dataRawatJalan = [];

        $findDataRI = $this->findDataRI($rjNo);
        $dataRawatJalan  = $findDataRI;



        $rsAdmin = DB::table('rstxn_rihdrs')
            ->select('rs_admin', 'rj_admin', 'poli_price', 'klaim_id', 'pass_status')
            ->where('rihdr_no', $rjNo)
            ->first();

        $rsObat = DB::table('rstxn_riobats')
            ->join('immst_products', 'immst_products.product_id', 'rstxn_riobats.product_id')
            ->select('rstxn_riobats.product_id as product_id', 'product_name', 'qty', 'price', 'rjobat_dtl')
            ->where('rihdr_no', $rjNo)
            ->get();

        $rsLab = DB::table('rstxn_rilabs')
            ->select('lab_desc', 'lab_price', 'lab_dtl')
            ->where('rihdr_no', $rjNo)
            ->get();

        $rsRad = DB::table('rstxn_rirads')
            ->join('rsmst_radiologis', 'rsmst_radiologis.rad_id', 'rstxn_rirads.rad_id')
            ->select('rad_desc', 'rstxn_rirads.rad_price as rad_price', 'rad_dtl')
            ->where('rihdr_no', $rjNo)
            ->get();

        $rstrfRj = DB::table('rstxn_ritempadmins')
            ->select('rihdr_no', 'tempadm_date', 'rj_admin', 'poli_price', 'acte_price', 'actp_price', 'actd_price', 'obat', 'lab', 'rad', 'other', 'rs_admin')
            ->where('rihdr_no', $rjNo)
            ->get();



        // RJ Admin
        if ($rsAdmin->pass_status == 'N') {
            $rsAdminParameter = DB::table('rsmst_parameters')
                ->select('par_value')
                ->where('par_id', '1')
                ->first();
            if (isset($dataRawatJalan['rjAdmin'])) {
                $dataRawatJalan['rjAdmin'] = $rsAdmin->rj_admin;
            } else {
                $dataRawatJalan['rjAdmin'] = $rsAdminParameter->par_value;
                // update table trnsaksi
                DB::table('rstxn_rihdrs')
                    ->where('rihdr_no', $rjNo)
                    ->update([
                        'rj_admin' => $dataRawatJalan['rjAdmin'],
                    ]);
            }
        } else {
            $dataRawatJalan['rjAdmin'] = 0;
        }

        // RS Admin
        $rsAdminDokter = DB::table('rsmst_doctors')
            ->select('rs_admin', 'poli_price')
            ->where('dr_id', $dataRawatJalan['drId'])
            ->first();


        if (isset($dataRawatJalan['rsAdmin'])) {
            $dataRawatJalan['rsAdmin'] = $rsAdmin->rs_admin ? $rsAdmin->rs_admin : 0;
        } else {
            $dataRawatJalan['rsAdmin'] = $rsAdminDokter->rs_admin ? $rsAdminDokter->rs_admin : 0;
            // update table trnsaksi
            DB::table('rstxn_rihdrs')
                ->where('rihdr_no', $rjNo)
                ->update([
                    'rs_admin' => $dataRawatJalan['rsAdmin'],
                ]);
        }

        // PoliPrice
        if (isset($dataRawatJalan['poliPrice'])) {
            $dataRawatJalan['poliPrice'] = $rsAdmin->poli_price ? $rsAdmin->poli_price : 0;
        } else {
            $dataRawatJalan['poliPrice'] = $rsAdminDokter->poli_price ? $rsAdminDokter->poli_price : 0;
            // update table trnsaksi
            DB::table('rstxn_rihdrs')
                ->where('rihdr_no', $rjNo)
                ->update([
                    'poli_price' => $dataRawatJalan['poliPrice'],
                ]);
        }

        // Ketika Kronis
        if ($rsAdmin->klaim_id == 'KR') {
            $dataRawatJalan['rjAdmin'] = 0;
            $dataRawatJalan['rsAdmin'] = 0;
            $dataRawatJalan['poliPrice'] = 0;
            // update table trnsaksi
            DB::table('rstxn_rihdrs')
                ->where('rihdr_no', $rjNo)
                ->update([
                    'rj_admin' => $dataRawatJalan['rjAdmin'],
                    'rs_admin' => $dataRawatJalan['rsAdmin'],
                    'poli_price' => $dataRawatJalan['poliPrice'],
                ]);
        }

        $dataRawatJalan['rjObat'] = json_decode(json_encode($rsObat, true), true);
        $dataRawatJalan['rjLab'] = json_decode(json_encode($rsLab, true), true);
        $dataRawatJalan['rjRad'] = json_decode(json_encode($rsRad, true), true);
        $dataRawatJalan['rjtrfRj'] = json_decode(json_encode($rstrfRj, true), true);

        return ($dataRawatJalan);
    }

    // when new form instance
    public function mount()
    {
        $this->sumAll();
    }



    // select data start////////////////
    public function render()
    {

        return view(
            'livewire.emr-r-i.administrasi-r-i.administrasi-r-i',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Administrasi Unit Gawat Darurat',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Pasien Unit Gawat Darurat',
            ]
        );
    }
    // select data end////////////////


}
