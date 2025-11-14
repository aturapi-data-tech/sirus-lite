<?php

namespace App\Http\Livewire\EmrUGD;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class EmrUGD extends Component
{
    use WithPagination;

    /* =========================================================================
     |  Display & Program Info
     * ========================================================================= */
    public string $myTitle   = 'Rekam Medis UGD';
    public string $mySnipt   = 'Rekam Medis Pasien';
    public string $myProgram = 'Pasien UGD';

    /* =========================================================================
     |  Pagination & Search
     * ========================================================================= */
    public array $myLimitPerPages = [5, 10, 15, 20, 100];
    public int   $limitPerPage    = 10;

    public string $refFilter = '';

    // Tampilkan ?cariData=...&p=... di URL
    protected $queryString = [
        'refFilter' => ['except' => '', 'as' => 'cariData'],
        'page'      => ['except' => 1, 'as' => 'p'],
    ];

    /* =========================================================================
     |  Register & Flags
     * ========================================================================= */
    public string $regNo = '';
    public bool   $callMasterPasien = false;

    /* =========================================================================
     |  Top Bar (Tanggal, Shift, Status, Dokter)
     * ========================================================================= */
    public array $myTopBar = [
        'refDate'   => '',

        'refShiftId'      => '1',
        'refShiftDesc'    => '1',
        'refShiftOptions' => [
            ['refShiftId' => '1', 'refShiftDesc' => '1'],
            ['refShiftId' => '2', 'refShiftDesc' => '2'],
            ['refShiftId' => '3', 'refShiftDesc' => '3'],
        ],

        'refStatusId'      => 'A',
        'refStatusDesc'    => 'Antrian',
        'refStatusOptions' => [
            ['refStatusId' => 'A', 'refStatusDesc' => 'Antrian'],
            ['refStatusId' => 'L', 'refStatusDesc' => 'Selesai'],
            ['refStatusId' => 'I', 'refStatusDesc' => 'Transfer'],
        ],

        'drId'      => 'All',
        'drName'    => 'All',
        'drOptions' => [
            ['drId' => 'All', 'drName' => 'All'],
        ],
    ];

    /* =========================================================================
     |  Modal States
     * ========================================================================= */
    public bool   $isOpen  = false;
    public string $isOpenMode = 'insert';

    public bool   $isOpenDokter  = false;
    public string $isOpenModeDokter = 'insert';

    public bool   $isOpenInap  = false;
    public string $isOpenModeInap = 'insert';

    public bool   $isOpenGeneralConsentPasienUGD  = false;
    public string $isOpenModeGeneralConsentPasienUGD = 'insert';

    public bool   $isOpenScreening  = false;
    public string $isOpenModeScreening = 'insert';

    public $isOpenTrfUgd = false;


    public ?int    $rjNoRef  = null;
    public ?string $regNoRef = null;

    /* =========================================================================
     |  Tabs
     * ========================================================================= */
    public string $activeTab = 'rekamMedis';
    public string $activeTabDokter = 'assessmentDokter';
    public string $activeTabGeneralConsentPasienUGD = 'generalConsentPasienUGD';

    public array $EmrMenu = [
        ['ermMenuId' => 'anamnesa',     'ermMenuName' => 'Anamnesa'],
        ['ermMenuId' => 'pemeriksaan',  'ermMenuName' => 'Pemeriksaan'],
        ['ermMenuId' => 'penilaian',    'ermMenuName' => 'Penilaian'],
        ['ermMenuId' => 'observasi',    'ermMenuName' => 'Observasi'],
        ['ermMenuId' => 'diagnosis',    'ermMenuName' => 'Diagnosis (ICD)'],
        ['ermMenuId' => 'perencanaan',  'ermMenuName' => 'Perencanaan'],
        ['ermMenuId' => 'administrasi', 'ermMenuName' => 'Administrasi'],
        ['ermMenuId' => 'suket',        'ermMenuName' => 'Surat Keterangan (Sehat/Istirahat)'],
        ['ermMenuId' => 'rekamMedis',   'ermMenuName' => 'Rekam Medis'],
    ];

    public array $EmrMenuDokter = [
        ['ermMenuId' => 'assessmentDokter',   'ermMenuName' => 'Assessment Dokter'],
        ['ermMenuId' => 'observasi',          'ermMenuName' => 'Observasi'],
        ['ermMenuId' => 'pelayananPenunjang', 'ermMenuName' => 'Pelayanan Penunjang'],
        ['ermMenuId' => 'rekamMedis',         'ermMenuName' => 'Resume Medis'],
    ];

    public array $EmrMenuGeneralConsentPasienUGD = [
        ['ermMenuId' => 'generalConsentPasienUGD', 'ermMenuName' => 'General Consent Pasien UGD'],
        ['ermMenuId' => 'informConsentPasienUGD',  'ermMenuName' => 'Inform Consent Pasien UGD'],
    ];

    /* =========================================================================
     |  Livewire: Lifecycles for Query String-bound Props
     * ========================================================================= */
    public function updatedRefFilter(): void
    {
        $this->resetPage();
    }

    public function updatedMyTopBarRefDate(): void
    {
        $this->resetPage();
    }

    public function updatedMyTopBarRefStatusId(): void
    {
        $this->resetPage();
    }

    /* =========================================================================
     |  Helpers (Top Bar)
     * ========================================================================= */
    private function setTopBarShiftAndRefDate(): void
    {
        // dd/mm/yyyy
        $this->myTopBar['refDate'] = Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y');

        // Cari shift aktif saat ini
        $findShift = DB::table('rstxn_shiftctls')
            ->select('shift')
            ->whereRaw("'" . Carbon::now(env('APP_TIMEZONE'))->format('H:i:s') . "' BETWEEN shift_start AND shift_end")
            ->first();

        $this->myTopBar['refShiftId'] = isset($findShift->shift) && $findShift->shift ? $findShift->shift : 3;
    }

    private function fillTopBarDoctorOptions(): void
    {
        $myRefdate = $this->myTopBar['refDate'];

        $query = DB::table('rsview_ugdkasir')
            ->select('dr_id', 'dr_name')
            ->where(DB::raw("to_char(rj_date,'dd/mm/yyyy')"), '=', $myRefdate)
            ->groupBy('dr_id', 'dr_name')
            ->orderBy('dr_name', 'desc')
            ->get();

        // Reset ke default "All"
        $this->myTopBar['drOptions'] = [
            ['drId' => 'All', 'drName' => 'All'],
        ];

        foreach ($query as $idx => $item) {
            $this->myTopBar['drOptions'][$idx + 1] = [
                'drId'   => $item->dr_id,
                'drName' => $item->dr_name,
            ];
        }
    }

    public function setTopBarDoctor(string $drId, string $drName): void
    {
        $this->myTopBar['drId']   = $drId;
        $this->myTopBar['drName'] = $drName;
        $this->resetPage();
    }

    /* =========================================================================
     |  Modal Open/Close
     * ========================================================================= */
    private function openModal(): void
    {
        $this->isOpen     = true;
        $this->isOpenMode = 'insert';
    }
    public function openTrfUgd()
    {
        $this->isOpenTrfUgd = true;
    }

    private function openModalEdit($rjNo, $regNoRef): void
    {
        $this->isOpen     = true;
        $this->isOpenMode = 'update';
        $this->rjNoRef    = $rjNo;
        $this->regNoRef   = $regNoRef;
    }

    private function openModalEditTrfUgd($rjNo, $regNoRef): void
    {
        $this->isOpenTrfUgd     = true;
        $this->rjNoRef    = $rjNo;
        $this->regNoRef   = $regNoRef;
    }

    private function openModalEditDokter($rjNo, $regNoRef): void
    {
        $this->isOpenDokter     = true;
        $this->isOpenModeDokter = 'update';
        $this->rjNoRef          = $rjNo;
        $this->regNoRef         = $regNoRef;
    }

    private function openModalEditGeneralConsentPasienUGD($rjNo, $regNoRef): void
    {
        $this->isOpenGeneralConsentPasienUGD     = true;
        $this->isOpenModeGeneralConsentPasienUGD = 'update';
        $this->rjNoRef                            = $rjNo;
        $this->regNoRef                           = $regNoRef;
    }

    private function openModalEditInap($rjNo, $regNoRef): void
    {
        $this->isOpenInap     = true;
        $this->isOpenModeInap = 'update';
        $this->rjNoRef        = $rjNo;
        $this->regNoRef       = $regNoRef;
    }

    private function openModalEditScreening($rjNo, $regNoRef): void
    {
        $this->isOpenScreening     = true;
        $this->isOpenModeScreening = 'update';
        $this->rjNoRef             = $rjNo;
        $this->regNoRef            = $regNoRef;
    }

    private function openModalTampil(): void
    {
        $this->isOpen     = true;
        $this->isOpenMode = 'tampil';
    }

    public function closeModal(): void
    {
        $this->isOpen     = false;
        $this->isOpenMode = 'insert';
        $this->resetInputFields();
    }

    public function closeModalTrfUgd(): void
    {
        $this->isOpenTrfUgd     = false;
        $this->resetInputFields();
    }

    public function closeTrfUgd()
    {
        $this->isOpenTrfUgd = false;
    }

    public function closeModalDokter(): void
    {
        $this->isOpenDokter     = false;
        $this->isOpenModeDokter = 'insert';
        $this->resetInputFields();
    }

    public function closeModalGeneralConsentPasienUGD(): void
    {
        $this->isOpenGeneralConsentPasienUGD     = false;
        $this->isOpenModeGeneralConsentPasienUGD = 'insert';
        $this->resetInputFields();
    }

    public function closeModalInap(): void
    {
        $this->isOpenInap     = false;
        $this->isOpenModeInap = 'insert';
        $this->resetInputFields();
    }

    public function closeModalScreening(): void
    {
        $this->isOpenScreening     = false;
        $this->isOpenModeScreening = 'insert';
        $this->resetInputFields();
    }

    private function resetInputFields(): void
    {
        $this->resetValidation();
        // Tambahkan reset input lain jika diperlukan
    }

    /* =========================================================================
     |  Actions
     * ========================================================================= */
    public function edit($rjNo, $regNoRef): void
    {
        $this->openModalEdit($rjNo, $regNoRef);
    }

    public function editTrfUgd($rjNo, $regNoRef): void
    {
        $this->openModalEditTrfUgd($rjNo, $regNoRef);
    }

    public function editDokter($rjNo, $regNoRef): void
    {
        $this->openModalEditDokter($rjNo, $regNoRef);
    }

    public function editGeneralConsentPasienUGD($rjNo, $regNoRef): void
    {
        $this->openModalEditGeneralConsentPasienUGD($rjNo, $regNoRef);
    }

    public function editInap($rjNo, $regNoRef): void
    {
        $this->openModalEditInap($rjNo, $regNoRef);
    }

    public function editScreening($rjNo, $regNoRef): void
    {
        $this->openModalEditScreening($rjNo, $regNoRef);
    }

    public function create(): void
    {
        $this->openModal();
    }

    public function tampil(): void
    {
        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
            ->addError('Fitur dalam masa pengembangan');
    }

    public function delete(): void
    {
        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
            ->addError('Fitur dalam masa pengembangan');
    }

    public function callFormPasien(): void
    {
        $this->callMasterPasien = true;
    }

    public function storeAssessmentPerawat(): void
    {
        $this->emit('emr:ugd:store');
    }

    /* =========================================================================
     |  Lifecycle
     * ========================================================================= */
    public function mount(): void
    {
        $this->setTopBarShiftAndRefDate();
    }

    /* =========================================================================
     |  Render (Query)
     * ========================================================================= */
    public function render()
    {
        $this->fillTopBarDoctorOptions();

        $mySearch     = $this->refFilter;
        $myRefdate    = $this->myTopBar['refDate'];
        $myRefstatusId = $this->myTopBar['refStatusId'];
        $myRefdrId    = $this->myTopBar['drId'];

        $query = DB::table('rsview_ugdkasir')
            ->select(
                DB::raw("to_char(rj_date,'dd/mm/yyyy hh24:mi:ss') AS rj_date"),
                DB::raw("to_char(rj_date,'yyyymmddhh24miss')     AS rj_date1"),
                'rj_no',
                'reg_no',
                'reg_name',
                'sex',
                'address',
                DB::raw("
                    CASE
                        WHEN birth_date IS NOT NULL THEN
                            trunc(months_between(sysdate, birth_date) / 12) || ' Thn, ' ||
                            trunc(mod(months_between(sysdate, birth_date), 12)) || ' Bln, ' ||
                            trunc(sysdate - add_months(birth_date, trunc(months_between(sysdate, birth_date)))) || ' Hr'
                        ELSE
                            '0 Thn, 0 Bln, 0 Hr'
                    END AS thn
                "),
                DB::raw("to_char(birth_date,'dd/mm/yyyy') AS birth_date"),
                DB::raw("'UGD' AS poli_id"),
                DB::raw("'UGD' AS poli_desc"),
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
                'datadaftarugd_json',
                DB::raw("(SELECT COUNT(*) FROM lbtxn_checkuphdrs WHERE status_rjri='UGD' AND checkup_status!='B' AND ref_no = rsview_ugdkasir.rj_no) AS lab_status"),
                DB::raw("(SELECT COUNT(*) FROM rstxn_ugdrads WHERE rj_no = rsview_ugdkasir.rj_no) AS rad_status")
            )
            ->where(DB::raw("nvl(erm_status,'A')"), '=', $myRefstatusId)
            ->where('rj_status', '!=', 'F')
            ->where(DB::raw("to_char(rj_date,'dd/mm/yyyy')"), '=', $myRefdate);

        if ($myRefdrId !== 'All') {
            $query->where('dr_id', $myRefdrId);
        }

        $query->where(function ($q) use ($mySearch) {
            $u = strtoupper($mySearch);
            $q->where(DB::raw('upper(reg_name)'), 'like', "%{$u}%")
                ->orWhere(DB::raw('upper(reg_no)'),  'like', "%{$u}%")
                ->orWhere(DB::raw('upper(dr_name)'), 'like', "%{$u}%");
        })
            ->orderBy('rj_date1', 'desc')
            ->orderBy('dr_name',  'desc')
            ->orderBy('no_antrian', 'asc');

        return view('livewire.emr-u-g-d.emr-u-g-d', [
            'myQueryData' => $query->paginate($this->limitPerPage),
        ]);
    }
}
