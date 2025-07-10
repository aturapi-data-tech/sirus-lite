<?php

namespace App\Http\Livewire\EmrRIPulang;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;


class EmrRIPulang extends Component
{
    use WithPagination;


    // primitive Variable
    public string $myTitle = 'Rekam Medis RI Pulang';
    public string $mySnipt = 'Rekam Medis Pasien';
    public string $myProgram = 'Pasien RI';

    public array $myLimitPerPages = [5, 10, 15, 20, 100];
    // limit record per page -resetExcept////////////////
    public int $limitPerPage = 10;

    // my Top Bar
    public array $myTopBar = [


        'refStatusId' => 'P',
        'refStatusDesc' => 'Pulang',
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


    // open and close modal start////////////////
    //  modal status////////////////
    public bool $isOpen = false;
    public string $isOpenMode = 'insert';

    public bool $isOpenDokter = false;
    public string $isOpenModeDokter = 'insert';

    public bool $isOpenInap = false;
    public string $isOpenModeInap = 'insert';

    public bool $isOpenGeneralConsentPasienRI = false;
    public string $isOpenModeGeneralConsentPasienRI = 'insert';

    public bool $isOpenEdukasiPasienRI = false;
    public string $isOpenModeEdukasiPasienRI = 'insert';

    public bool $isOpenScreening = false;
    public string $isOpenModeScreening = 'insert';

    public bool $forceInsertRecord = false;

    public int $riHdrNoRef;
    public string $regNoRef;

    //
    private function openModal(): void
    {
        $this->isOpen = true;
        $this->isOpenMode = 'insert';
    }
    private function openModalEdit($riHdrNo, $regNoRef): void
    {
        $this->isOpen = true;
        $this->isOpenMode = 'update';
        $this->riHdrNoRef = $riHdrNo;
        $this->regNoRef = $regNoRef;
    }

    private function openModalEditDokter($riHdrNo, $regNoRef): void
    {
        $this->isOpenDokter = true;
        $this->isOpenModeDokter = 'update';
        $this->riHdrNoRef = $riHdrNo;
        $this->regNoRef = $regNoRef;
    }

    private function openModalEditGeneralConsentPasienRI($riHdrNo, $regNoRef): void
    {
        $this->isOpenGeneralConsentPasienRI = true;
        $this->isOpenModeGeneralConsentPasienRI = 'update';
        $this->riHdrNoRef = $riHdrNo;
        $this->regNoRef = $regNoRef;
    }

    private function openModalEditEdukasiPasienRI($riHdrNo, $regNoRef): void
    {
        $this->isOpenEdukasiPasienRI = true;
        $this->isOpenModeEdukasiPasienRI = 'update';
        $this->riHdrNoRef = $riHdrNo;
        $this->regNoRef = $regNoRef;
    }

    private function openModalEditInap($riHdrNo, $regNoRef): void
    {
        $this->isOpenInap = true;
        $this->isOpenModeInap = 'update';
        $this->riHdrNoRef = $riHdrNo;
        $this->regNoRef = $regNoRef;
    }

    private function openModalEditScreening($riHdrNo, $regNoRef): void
    {
        $this->isOpenScreening = true;
        $this->isOpenModeScreening = 'update';
        $this->riHdrNoRef = $riHdrNo;
        $this->regNoRef = $regNoRef;
    }

    private function openModalTampil(): void
    {
        $this->isOpen = true;
        $this->isOpenMode = 'tampil';
    }

    public function closeModal(): void
    {
        $this->isOpen = false;
        $this->isOpenMode = 'insert';
        $this->resetInputFields();
    }

    public function closeModalDokter(): void
    {
        $this->isOpenDokter = false;
        $this->isOpenModeDokter = 'insert';
        $this->resetInputFields();
    }

    public function closeModalGeneralConsentPasienRI(): void
    {
        $this->isOpenGeneralConsentPasienRI = false;
        $this->isOpenModeGeneralConsentPasienRI = 'insert';
        $this->resetInputFields();
    }

    public function closeModalEdukasiPasienRI(): void
    {
        $this->isOpenEdukasiPasienRI = false;
        $this->isOpenModeEdukasiPasienRI = 'insert';
        $this->resetInputFields();
    }

    public function closeModalInap(): void
    {
        $this->isOpenInap = false;
        $this->isOpenModeInap = 'insert';
        $this->resetInputFields();
    }

    public function closeModalScreening(): void
    {
        $this->isOpenScreening = false;
        $this->isOpenModeScreening = 'insert';
        $this->resetInputFields();
    }
    // open and close modal end////////////////


    // resert input private////////////////
    private function resetInputFields(): void
    {
        // resert validation
        $this->resetValidation();
        // resert input

    }
    // resert input private////////////////

    // is going to edit data/////////////////
    public function edit($riHdrNo, $regNoRef)
    {
        $this->openModalEdit($riHdrNo, $regNoRef);
        // $this->findData($id);
    }

    public function editDokter($riHdrNo, $regNoRef)
    {
        $this->openModalEditDokter($riHdrNo, $regNoRef);
        // $this->findData($id);
    }

    public function editGeneralConsentPasienRI($riHdrNo, $regNoRef)
    {
        $this->openModalEditGeneralConsentPasienRI($riHdrNo, $regNoRef);
        // $this->findData($id);
    }

    public function editEdukasiPasienRI($riHdrNo, $regNoRef)
    {
        $this->openModalEditEdukasiPasienRI($riHdrNo, $regNoRef);
        // $this->findData($id);
    }

    // is going to edit data/////////////////
    public function editInap($riHdrNo, $regNoRef)
    {
        $this->openModalEditInap($riHdrNo, $regNoRef);
        // $this->findData($id);
    }

    public function editScreening($riHdrNo, $regNoRef)
    {
        $this->openModalEditScreening($riHdrNo, $regNoRef);
        // $this->findData($id);
    }

    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////

    // is going to insert data////////////////
    public function create()
    {
        $this->openModal();
    }

    public function tampil()
    {
        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Fitur dalam masa pengembangan');
    }

    public function delete()
    {
        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Fitur dalam masa pengembangan');
    }


    public string $activeTab = "rekamMedis";
    public string $activeTabDokter = "pengkajianDokter";
    public string $activeTabGeneralConsentPasienRI = "generalConsentPasienRI";
    public string $activeTabEdukasiPasienRI = "edukasiPasienRI";




    public array $EmrMenu = [
        [
            'ermMenuId' => 'pengkajianAwalPasienRawatInap',
            'ermMenuName' => 'Pengkajian Pasien Rawat Inap'
        ],
        [
            'ermMenuId' => 'pengkajianDokter',
            'ermMenuName' => 'Pengkajian Dokter'
        ],
        [
            'ermMenuId' => 'pemeriksaan',
            'ermMenuName' => 'Pemeriksaan'
        ],
        [
            'ermMenuId' => 'penilaian',
            'ermMenuName' => 'Penilaian'
        ],
        [
            'ermMenuId' => 'observasi',
            'ermMenuName' => 'Observasi'
        ],
        [
            'ermMenuId' => 'asuhanKeperawatan',
            'ermMenuName' => 'Asuhan Keperawatan'
        ],
        [
            'ermMenuId' => 'cppt',
            'ermMenuName' => 'CPPT'
        ],
        [
            'ermMenuId' => 'diagnosis',
            'ermMenuName' => 'Diagnosis (ICD)'
        ],

        [
            'ermMenuId' => 'perencanaan',
            'ermMenuName' => 'Perencanaan'
        ],
        [
            'ermMenuId' => 'administrasi',
            'ermMenuName' => 'Administrasi'
        ],

        [
            'ermMenuId' => 'rekamMedis',
            'ermMenuName' => 'Rekam Medis'
        ],

    ];

    public array $EmrMenuDokter = [
        [
            'ermMenuId' => 'pengkajianDokter',
            'ermMenuName' => 'Pengkajian Dokter'
        ],
        [
            'ermMenuId' => 'observasi',
            'ermMenuName' => 'Observasi'
        ],
        [
            'ermMenuId' => 'cppt',
            'ermMenuName' => 'CPPT'
        ],
        [
            'ermMenuId' => 'pemeriksaanPenunjang',
            'ermMenuName' => 'Pemeriksaan Penunjang'
        ],
        [
            'ermMenuId' => 'pelayananPenunjang',
            'ermMenuName' => 'Pelayanan Penunjang'
        ],
        [
            'ermMenuId' => 'rekamMedis',
            'ermMenuName' => 'Resume Medis'
        ],

    ];

    public array $EmrMenuGeneralConsentPasienRI = [
        [
            'ermMenuId' => 'generalConsentPasienRI',
            'ermMenuName' => 'General Consent Pasien RI'
        ],
        [
            'ermMenuId' => 'informConsentPasienRI',
            'ermMenuName' => 'Inform Consent Pasien RI'
        ]
    ];

    public array $EmrMenuEdukasiPasienRI = [
        [
            'ermMenuId' => 'edukasiPasienRI',
            'ermMenuName' => 'Form Edukasi Pasien RI'
        ]
    ];


    // when new form instance
    public function mount() {}

    // select data start////////////////
    public function render()
    {

        // set mySearch
        $mySearch = $this->refFilter;
        $myRefstatusId = $this->myTopBar['refStatusId'];




        //untuk membuat where clause yang berada pada table json kita pakai metode
        // Query Builder → Collection → Filter → Query Builder Lagi
        // 1️⃣ Ambil data dari database (Query Builder)
        // 2️⃣ Gunakan filter() untuk menyaring data dalam Collection
        // 3️⃣ Ambil kembali drId hasil filter, lalu kembalikan ke Query Builder
        // Ambil hanya ID dokter yang terfilter
        // 4️⃣ Jalankan ulang Query Builder dengan whereIn()
        //////////////////////////////////////////
        // Query ///////////////////////////////
        //////////////////////////////////////////
        $query = DB::table('rsview_rihdrs')
            ->select(
                DB::raw("to_char(entry_date,'dd/mm/yyyy hh24:mi:ss') AS entry_date"),
                DB::raw("to_char(entry_date,'yyyymmddhh24miss') AS entry_date1"),
                DB::raw("to_char(exit_date,'dd/mm/yyyy hh24:mi:ss') AS exit_date"),
                DB::raw("to_char(exit_date,'yyyymmddhh24miss') AS exit_date1"),
                'rihdr_no',
                'reg_no',
                'reg_name',
                'sex',
                'address',
                'thn',
                DB::raw("to_char(birth_date,'dd/mm/yyyy') AS birth_date"),
                'dr_id',
                'dr_name',
                'klaim_id',
                'vno_sep',
                'ri_status',
                'datadaftarri_json',
                'room_id',
                'room_name',
                'bangsal_id',
                'bangsal_name',
                'bed_no',
            )
            ->where(DB::raw("nvl(ri_status,'I')"), '=', $myRefstatusId)
            ->where(function ($q) use ($mySearch) {
                $q->Where(DB::raw('upper(reg_name)'), 'like', '%' . strtoupper($mySearch) . '%')
                    ->orWhere(DB::raw('upper(reg_no)'), 'like', '%' . strtoupper($mySearch) . '%')
                    ->orWhere(DB::raw('upper(vno_sep)'), 'like', '%' . strtoupper($mySearch) . '%')
                    ->orWhere(DB::raw('upper(dr_name)'), 'like', '%' . strtoupper($mySearch) . '%');
            })
            ->orderBy('exit_date1',  'desc')
            ->orderBy('dr_name',  'desc')
            ->paginate($this->limitPerPage);
        ////////////////////////////////////////////////
        // end Query
        ///////////////////////////////////////////////



        return view(
            'livewire.emr-r-i-pulang.emr-r-i-pulang',
            ['myQueryData' => $query]
        );
    }
    // select data end////////////////


}
