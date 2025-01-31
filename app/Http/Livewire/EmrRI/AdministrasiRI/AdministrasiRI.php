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

    //Admin:
    public int $sumAdminAge;
    public int $sumAdminStatus;
    //Jasa:
    public int $sumRiVisit;
    public int $sumRiKonsul;
    public int $sumRiActParams;
    public int $sumRiActDocs;
    public int $sumRiLab;
    public int $sumRiRad;
    public int $sumUgdRj;
    public int $sumRiOther;

    //Operasi:
    public int $sumRiOk;

    //Kamar:
    public int $sumRiRoom;
    public int $sumCService;
    public int $sumRiPerawatan;

    //Bon:
    public int $sumRiBonResep;

    //Lain-lain:

    public int $sumRiRtnObat;
    //Obat Pinjam
    public int $sumRsObat;

    public int $sumTotalRI;


    public string $activeTabAdministrasi = "RiVisit";
    public array $EmrMenuAdministrasi = [

        // Jasa
        [
            'ermMenuId' => 'RiVisit',
            'ermMenuName' => 'Kunjungan'
        ],
        [
            'ermMenuId' => 'RiKonsul',
            'ermMenuName' => 'Konsultasi'
        ],
        [
            'ermMenuId' => 'RiActParams',
            'ermMenuName' => 'Jasa Medis'
        ],
        [
            'ermMenuId' => 'RiActDocs',
            'ermMenuName' => 'Jasa Dokter'
        ],
        [
            'ermMenuId' => 'RiLab',
            'ermMenuName' => 'Laboratorium'
        ],
        [
            'ermMenuId' => 'RiRad',
            'ermMenuName' => 'Radiologi'
        ],
        [
            'ermMenuId' => 'UgdRj',
            'ermMenuName' => 'Trf UGD/Rawat Jalan'
        ],
        [
            'ermMenuId' => 'RiOther',
            'ermMenuName' => 'Lain-Lain'
        ],

        // Operasi
        [
            'ermMenuId' => 'RiOk',
            'ermMenuName' => 'Operasi(OK)'
        ],

        // Kamar
        [
            'ermMenuId' => 'RiRoom',
            'ermMenuName' => 'Kamar'
        ],
        [
            'ermMenuId' => 'CService',
            'ermMenuName' => 'Layanan Pelanggan'
        ],

        // Bon
        [
            'ermMenuId' => 'RiBonResep',
            'ermMenuName' => 'Bon Resep'
        ],

        // Lain-lain
        [
            'ermMenuId' => 'RiPerawatan',
            'ermMenuName' => 'Perawatan RI'
        ],
        [
            'ermMenuId' => 'RiRtnObat',
            'ermMenuName' => 'Return Obat RI'
        ],
        [
            'ermMenuId' => 'TotalRI',
            'ermMenuName' => 'Total RI'
        ]
    ];


    public function sumAll()
    {
        $this->sumAdmin();
    }

    private function sumAdmin()
    {
        $dataRawatInap = $this->findData($this->riHdrNoRef);

        // Admin:
        $this->sumAdminAge = $dataRawatInap['adminAge'] ?? 0;
        $this->sumAdminStatus = $dataRawatInap['adminStatus'] ?? 0;

        // Jasa (Services):
        $this->sumRiVisit = collect($dataRawatInap['riVisit'])->sum(function ($visit) {
            return $visit['visit_price'] * ($visit['qty'] ?? 1);  // Multiply by qty if it exists
        });
        $this->sumRiKonsul = collect($dataRawatInap['riKonsul'])->sum(function ($konsul) {
            return $konsul['konsul_price'] * ($konsul['qty'] ?? 1);  // Multiply by qty if it exists
        });
        $this->sumRiActParams = collect($dataRawatInap['riActParams'])->sum(function ($actParam) {
            return $actParam['actp_price'] * ($actParam['actp_qty'] ?? 1);  // Multiply by qty if it exists
        });
        $this->sumRiActDocs = collect($dataRawatInap['riActDocs'])->sum(function ($actDoc) {
            return $actDoc['actd_price'] * ($actDoc['actd_qty'] ?? 1);  // Multiply by qty if it exists
        });
        $this->sumRiLab = collect($dataRawatInap['riLab'])->sum(function ($lab) {
            return $lab['lab_price'] * ($lab['qty'] ?? 1);  // Multiply by qty or detail if it exists
        });
        $this->sumRiRad = collect($dataRawatInap['riRad'])->sum(function ($rad) {
            return $rad['rirad_price'] * ($rad['qty'] ?? 1);  // Multiply by qty if it exists
        });
        $this->sumUgdRj = collect($dataRawatInap['rstrfRj'])->sum(function ($rj) {
            return ($rj['rj_admin'] + $rj['poli_price']) * ($rj['qty'] ?? 1);  // Multiply by qty if it exists
        });


        // Operasi (Operation):
        $this->sumRiOk = collect($dataRawatInap['riOk'])->sum(function ($ok) {
            return $ok['ok_price'] * ($ok['qty'] ?? 1);  // Multiply by qty if it exists
        });

        // Kamar (Room):
        $this->sumRiRoom = collect($dataRawatInap['riRoom'])->sum(function ($room) {
            return $room['room_price'] * ($room['day'] ?? 1);  // Multiply by day or quantity if it exists
        });
        $this->sumCService = collect($dataRawatInap['riRoom'])->sum(function ($room) {
            return $room['common_service'] * ($room['day'] ?? 1);  // Multiply by day if it exists
        });
        $this->sumRiPerawatan = collect($dataRawatInap['riRoom'])->sum(function ($room) {
            return $room['perawatan_price'] * ($room['day'] ?? 1);  // Multiply by day if it exists
        });

        // Bon (Prescription):
        $this->sumRiBonResep = collect($dataRawatInap['riBonResep'])->sum(function ($bonResep) {
            return $bonResep['ribon_price'] * ($bonResep['qty'] ?? 1);  // Multiply by qty if it exists
        });

        // Lain-lain (Others):
        $this->sumRiOther = collect($dataRawatInap['riOther'])->sum(function ($other) {
            return $other['other_price'] * ($other['qty'] ?? 1);  // Multiply by qty if it exists
        });

        // Return Obat (Return Medicine):
        $this->sumRiRtnObat = collect($dataRawatInap['riRtnObat'])->sum(function ($obat) {
            return $obat['riobat_qty'] * $obat['riobat_price'];  // Quantity * price
        });
        //ObatPinjam
        $this->sumRsObat = collect($dataRawatInap['rsObat'])->sum(function ($obat) {
            return $obat['riobat_qty'] * $obat['riobat_price'];  // Quantity * price
        });

        // Total Rawat Inap:
        $this->sumTotalRI = $this->sumRiVisit +
            $this->sumRiKonsul +
            $this->sumRiActParams +
            $this->sumRiActDocs +
            $this->sumRiLab +
            $this->sumRiRad +
            $this->sumUgdRj + $this->sumRiOther +
            $this->sumRiOk +
            $this->sumRiRoom +
            $this->sumCService +
            $this->sumRiBonResep +
            $this->sumRiPerawatan +
            $this->sumRsObat -
            $this->sumRiRtnObat;
    }



    private function findData($riHdrNo): array
    {
        $dataRawatInap = [];

        $findDataRI = $this->findDataRI($riHdrNo);
        $dataRawatInap  = $findDataRI;

        // JD - Jasa Dokter
        $riActDocs = DB::table('rstxn_riactdocs')
            ->join('rsmst_accdocs', 'rsmst_accdocs.accdoc_id', '=', 'rstxn_riactdocs.accdoc_id')
            ->join('rsmst_doctors', 'rsmst_doctors.dr_id', '=', 'rstxn_riactdocs.dr_id')
            ->select(
                'rstxn_riactdocs.actd_date',
                'rstxn_riactdocs.dr_id',
                'rsmst_doctors.dr_name',
                'rstxn_riactdocs.accdoc_id',
                'rsmst_accdocs.accdoc_desc',
                'rstxn_riactdocs.actd_price',
                'rstxn_riactdocs.actd_qty',
                'rstxn_riactdocs.rihdr_no',
                'rstxn_riactdocs.actd_no'
            )
            ->where('rstxn_riactdocs.rihdr_no', $riHdrNo)
            ->get();
        $dataRawatInap['riActDocs'] = json_decode(json_encode($riActDocs, true), true);

        // JM - Jasa Medis
        $riActParams = DB::table('rstxn_riactparams')
            ->join('rsmst_actparamedics', 'rsmst_actparamedics.pact_id', '=', 'rstxn_riactparams.pact_id')
            ->select(
                'rstxn_riactparams.actp_date',
                'rstxn_riactparams.pact_id',
                'rsmst_actparamedics.pact_desc',
                'rstxn_riactparams.actp_price',
                'rstxn_riactparams.actp_qty',
                'rstxn_riactparams.rihdr_no',
                'rstxn_riactparams.actp_no'

            )
            ->where('rstxn_riactparams.rihdr_no', $riHdrNo)
            ->get();
        $dataRawatInap['riActParams'] = json_decode(json_encode($riActParams, true), true);

        // Konsultasi
        $riKonsul = DB::table('rstxn_rikonsuls')
            ->join('rsmst_doctors', 'rstxn_rikonsuls.dr_id', '=', 'rsmst_doctors.dr_id')
            ->select(
                'rstxn_rikonsuls.konsul_date',
                'rstxn_rikonsuls.dr_id',
                'rsmst_doctors.dr_name',
                'rstxn_rikonsuls.konsul_price',
                'rstxn_rikonsuls.rihdr_no',
                'rstxn_rikonsuls.konsul_no'

            )
            ->where('rihdr_no', $riHdrNo)
            ->get();
        $dataRawatInap['riKonsul'] = json_decode(json_encode($riKonsul, true), true);

        // Kunjungan Dokter
        $riVisit = DB::table('rstxn_rivisits')
            ->join('rsmst_doctors', 'rstxn_rivisits.dr_id', '=', 'rsmst_doctors.dr_id')
            ->select(
                'rstxn_rivisits.visit_date',
                'rstxn_rivisits.dr_id',
                'rsmst_doctors.dr_name',
                'rstxn_rivisits.visit_price',
                'rstxn_rivisits.rihdr_no',
                'rstxn_rivisits.visit_no'

            )
            ->where('rihdr_no', $riHdrNo)
            ->get();
        $dataRawatInap['riVisit'] = json_decode(json_encode($riVisit, true), true);

        // Administrasi
        $rsAdmin = DB::table('rstxn_rihdrs')
            ->select('admin_status', 'admin_age', 'rihdr_no')
            ->where('rihdr_no', $riHdrNo)
            ->first();
        $dataRawatInap['adminAge'] = $rsAdmin->admin_age ?? 0;
        $dataRawatInap['adminStatus'] = $rsAdmin->admin_status ?? 0;
        $dataRawatInap['rihdr_no'] = $rsAdmin->rihdr_no ?? 0;

        // Bon Resep
        $riBonResep = DB::table('rstxn_ribonobats')
            ->select(
                'ribon_date',
                'ribon_desc',
                'ribon_price',
                'rihdr_no',
                'ribon_no',
                'sls_no'
            )
            ->where('rihdr_no', $riHdrNo)
            ->get();
        $dataRawatInap['riBonResep'] = json_decode(json_encode($riBonResep, true), true);

        // Obat Pinjam
        $rsObat = DB::table('rstxn_riobats')
            ->join('immst_products', 'immst_products.product_id', '=', 'rstxn_riobats.product_id')
            ->select(
                'rstxn_riobats.riobat_date',
                'rstxn_riobats.product_id as product_id',
                'immst_products.product_name',
                'rstxn_riobats.riobat_qty',
                'rstxn_riobats.riobat_price',
                'rstxn_riobats.rihdr_no',
                'rstxn_riobats.riobat_no'
            )
            ->where('rihdr_no', $riHdrNo)
            ->get();
        $dataRawatInap['rsObat'] = json_decode(json_encode($rsObat, true), true);

        // Return Obat
        $riRtnObat = DB::table('rstxn_riobatrtns')
            ->join('immst_products', 'immst_products.product_id', '=', 'rstxn_riobatrtns.product_id')
            ->select(
                'rstxn_riobatrtns.riobat_date',
                'rstxn_riobatrtns.product_id as product_id',
                'immst_products.product_name',
                'rstxn_riobatrtns.riobat_qty',
                'rstxn_riobatrtns.riobat_price',
                'rstxn_riobatrtns.rihdr_no',
                'rstxn_riobatrtns.riobat_no'
            )
            ->where('rihdr_no', $riHdrNo)
            ->get();
        $dataRawatInap['riRtnObat'] = json_decode(json_encode($riRtnObat, true), true);

        // Radiologi
        $riRad = DB::table('rstxn_riradiologs')
            ->join('rsmst_radiologis', 'rstxn_riradiologs.rad_id', '=', 'rsmst_radiologis.rad_id')
            ->select(
                'rstxn_riradiologs.rirad_date',
                'rstxn_riradiologs.rad_id',
                'rsmst_radiologis.rad_desc',
                'rstxn_riradiologs.rirad_price',
                'rstxn_riradiologs.rihdr_no',
                'rstxn_riradiologs.rirad_no'
            )
            ->where('rihdr_no', $riHdrNo)
            ->get();
        $dataRawatInap['riRad'] = json_decode(json_encode($riRad, true), true);

        // Laboratorium
        $riLab = DB::table('rstxn_rilabs')
            ->select(
                'lab_date',
                'lab_desc',
                'lab_price',
                'rihdr_no',
                'lab_dtl',
                'checkup_no'
            )
            ->where('rihdr_no', $riHdrNo)
            ->get();
        $dataRawatInap['riLab'] = json_decode(json_encode($riLab, true), true);

        // Operasi
        $riOk = DB::table('rstxn_rioks')
            ->select('ok_date', 'ok_desc', 'ok_price', 'rihdr_no', 'ok_no')
            ->where('rihdr_no', $riHdrNo)
            ->get();
        $dataRawatInap['riOk'] = json_decode(json_encode($riOk, true), true);

        // Lain-lain
        $riOther = DB::table('rstxn_riothers')
            ->join('rsmst_others', 'rstxn_riothers.other_id', '=', 'rsmst_others.other_id')
            ->select(
                'rstxn_riothers.other_date',
                'rstxn_riothers.other_id',
                'rsmst_others.other_desc',
                'rstxn_riothers.other_price',
                'rstxn_riothers.rihdr_no',
                'rstxn_riothers.other_no'
            )
            ->where('rihdr_no', $riHdrNo)
            ->get();
        $dataRawatInap['riOther'] = json_decode(json_encode($riOther, true), true);

        // Tarif Rawat Jalan / UGD
        $rstrfRj = DB::table('rstxn_ritempadmins')
            ->select(
                'tempadm_date',
                'rj_admin',
                'poli_price',
                'acte_price',
                'actp_price',
                'actd_price',
                'obat',
                'lab',
                'rad',
                'other',
                'rs_admin',
                'rihdr_no',
                'tempadm_no',
                'tempadm_flag'
            )
            ->where('rihdr_no', $riHdrNo)
            ->get();
        $dataRawatInap['rstrfRj'] = json_decode(json_encode($rstrfRj, true), true);

        // Kamar Rawat Inap
        $riRoom = DB::table('rsmst_trfrooms')
            ->select('start_date', 'end_date', 'room_id', 'room_price', 'perawatan_price', 'common_service', 'day', 'rihdr_no', 'trfr_no')
            ->where('rihdr_no', $riHdrNo)
            ->get();
        $dataRawatInap['riRoom'] = json_decode(json_encode($riRoom, true), true);



        return ($dataRawatInap);
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
