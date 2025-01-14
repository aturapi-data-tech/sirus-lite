<?php

namespace App\Http\Livewire\EmrRI;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;



class EmrRI extends Component
{
    use WithPagination;

    // primitive Variable
    public string $myTitle = 'Rekam Medis RI';
    public string $mySnipt = 'Rekam Medis Pasien';
    public string $myProgram = 'Pasien RI';

    public array $myLimitPerPages = [5, 10, 15, 20, 100];
    // limit record per page -resetExcept////////////////
    public int $limitPerPage = 10;

    // my Top Bar
    public array $myTopBar = [


        'refStatusId' => 'I',
        'refStatusDesc' => 'Antrian',

        'roomId' => 'All',
        'roomName' => 'All',
        'roomOptions' => [
            [
                'roomId' => 'All',
                'roomName' => 'All'
            ]
        ]
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

    private function gettermyTopBarRoomOptions(): void
    {

        // // Query
        $query = DB::table('rsmst_bangsals')
            ->select(
                'bangsal_id',
                'bangsal_name',
            )
            ->orderBy('bangsal_name', 'desc')
            ->get();

        // // loop and set Ref
        $query->each(function ($item, $key) {
            $this->myTopBar['roomOptions'][$key + 1]['roomId'] = $item->bangsal_id;
            $this->myTopBar['roomOptions'][$key + 1]['roomName'] = $item->bangsal_name;
        })->toArray();
    }

    public function settermyTopBarroomOptions($roomId, $roomName): void
    {

        $this->myTopBar['roomId'] = $roomId;
        $this->myTopBar['roomName'] = $roomName;
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
    public string $activeTabDokter = "assessmentDokter";
    public string $activeTabGeneralConsentPasienRI = "generalConsentPasienRI";



    public array $EmrMenu = [
        // [
        //     'ermMenuId' => 'keperawatan',
        //     'ermMenuName' => 'Keperawatan'
        // ],
        [
            'ermMenuId' => 'pengkajianAwalPasienRawatInap',
            'ermMenuName' => 'Pengkajian Awal Pasien Rawat Inap'
        ],
        [
            'ermMenuId' => 'pengkajianDokter',
            'ermMenuName' => 'Pengkajian Dokter'
        ],
        [
            'ermMenuId' => 'anamnesa',
            'ermMenuName' => 'Anamnesa'
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
            'ermMenuId' => 'diagnosis',
            'ermMenuName' => 'Diagnosis (ICD)'
        ],
        // [
        //     'ermMenuId' => 'penandaanGbr',
        //     'ermMenuName' => 'Penandaan Gambar'
        // ],
        [
            'ermMenuId' => 'perencanaan',
            'ermMenuName' => 'Perencanaan'
        ],
        [
            'ermMenuId' => 'administrasi',
            'ermMenuName' => 'Administrasi'
        ],
        // [
        //     'ermMenuId' => 'cppt',
        //     'ermMenuName' => 'CPPT'
        // ],
        // [
        //     'ermMenuId' => 'resumeMds',
        //     'ermMenuName' => 'Resume Medis'
        // ],
        // [
        //     'ermMenuId' => 'penerbitanSrt',
        //     'ermMenuName' => 'Penerbitan Surat'
        // ],
        [
            'ermMenuId' => 'rekamMedis',
            'ermMenuName' => 'Rekam Medis'
        ],

    ];

    public array $EmrMenuDokter = [
        [
            'ermMenuId' => 'assessmentDokter',
            'ermMenuName' => 'Assessment Dokter'
        ],
        [
            'ermMenuId' => 'observasi',
            'ermMenuName' => 'Observasi'
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


    // when new form instance
    public function mount() {}

    // select data start////////////////
    public function render()
    {
        $this->gettermyTopBarRoomOptions();

        // set mySearch
        $mySearch = $this->refFilter;
        $myRefstatusId = $this->myTopBar['refStatusId'];
        $myRefroomId = $this->myTopBar['roomId'];



        //////////////////////////////////////////
        // Query ///////////////////////////////
        //////////////////////////////////////////
        $query = DB::table('rsview_rihdrs')
            ->select(
                DB::raw("to_char(entry_date,'dd/mm/yyyy hh24:mi:ss') AS entry_date"),
                DB::raw("to_char(entry_date,'yyyymmddhh24miss') AS entry_date1"),
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
                DB::raw("(select count(*) from lbtxn_checkuphdrs where status_rjri='RI' and checkup_status!='B' and ref_no = rsview_rihdrs.rihdr_no) AS lab_status"),
                DB::raw("(select count(*) from rstxn_riradiologs where rihdr_no = rsview_rihdrs.rihdr_no) AS rad_status"),
                'room_id',
                'room_name',
                'bangsal_id',
                'bangsal_name',
                'bed_no'
            )
            ->where(DB::raw("nvl(ri_status,'I')"), '=', $myRefstatusId);

        // Jika where dokter tidak kosong
        if ($myRefroomId != 'All') {
            $query->where('bangsal_id', $myRefroomId);
        }


        $query->where(function ($q) use ($mySearch) {
            $q->Where(DB::raw('upper(reg_name)'), 'like', '%' . strtoupper($mySearch) . '%')
                ->orWhere(DB::raw('upper(reg_no)'), 'like', '%' . strtoupper($mySearch) . '%')
                ->orWhere(DB::raw('upper(dr_name)'), 'like', '%' . strtoupper($mySearch) . '%');
        })
            ->orderBy('entry_date1',  'desc')
            ->orderBy('dr_name',  'desc');

        ////////////////////////////////////////////////
        // end Query
        ///////////////////////////////////////////////



        return view(
            'livewire.emr-r-i.emr-r-i',
            ['myQueryData' => $query->paginate($this->limitPerPage)]
        );
    }
    // select data end////////////////


}
