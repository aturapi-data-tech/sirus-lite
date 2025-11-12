<?php

namespace App\Http\Livewire\EmrRI;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Traits\LOV\LOVDokter\LOVDokterTrait;


class EmrRI extends Component
{
    use WithPagination, LOVDokterTrait;


    // primitive Variable
    public string $myTitle = 'Rekam Medis Rawat Inap - Status Inap';
    public string $mySnipt = 'Rekam Medis Pasien';
    public string $myProgram = 'Pasien Rawat Inap';

    public array $myLimitPerPages = [5, 10, 15, 20, 100];
    // limit record per page -resetExcept////////////////
    public int $limitPerPage = 10;

    // LOV Nested
    public array $dokter;

    private function syncDataFormEntry(): void
    {
        $this->myTopBar['drId'] = $this->dokter['DokterId'] ?? '';
        $this->myTopBar['drName'] = $this->dokter['DokterDesc'] ?? '';
    }
    private function syncLOV(): void
    {
        $this->dokter = $this->collectingMyDokter;
    }
    public function resetDokter()
    {
        $this->reset([
            'collectingMyDokter', //Reset LOV / render  / empty NestLov
        ]);
        $this->resetValidation();
    }
    // LOV Nested


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
        ],
        'drId' => '',
        'drName' => '',
        'drOptions' => []
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

    public bool $isOpenCaseManagerRI = false;
    public string $isOpenModeCaseManagerRI = 'insert';

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

    private function openModalEditCaseManagerRI($riHdrNo, $regNoRef): void
    {
        $this->isOpenCaseManagerRI = true;
        $this->isOpenModeCaseManagerRI = 'update';
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

    public function closeModalCaseManagerRI(): void
    {
        $this->isOpenCaseManagerRI = false;
        $this->isOpenModeCaseManagerRI = 'insert';
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

    public function editCaseManagerRI($riHdrNo, $regNoRef)
    {
        $this->openModalEditCaseManagerRI($riHdrNo, $regNoRef);
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
    public string $activeTabCaseManagerRI = "caseManagerRI";





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

    public array $EmrMenuCaseManagerRI = [
        [
            'ermMenuId' => 'caseManagerRI',
            'ermMenuName' => 'Case Manager'
        ]
    ];

    public array $EmrMenuEdukasiPasienRI = [
        [
            'ermMenuId' => 'edukasiPasienRI',
            'ermMenuName' => 'Form Pemberian Informasi Pasien'
        ],
        [
            'ermMenuId' => 'edukasiPasienRITerintegrasi',
            'ermMenuName' => 'Form Edukasi Pasien Terintegrasi'
        ]
    ];


    // when new form instance
    public function mount() {}

    // select data start////////////////
    public function render()
    {
        $this->gettermyTopBarRoomOptions();

        // LOV
        $this->syncLOV();
        // FormEntry
        $this->syncDataFormEntry();

        // set mySearch
        $mySearch = $this->refFilter;
        $myRefstatusId = $this->myTopBar['refStatusId'];
        $myRefroomId = $this->myTopBar['roomId'];
        $myRefdrId = $this->myTopBar['drId'];



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
                'rihdr_no',
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
                'bed_no',
                'totinacbg_temp',
                'totalri_temp',
            )
            ->where(DB::raw("nvl(ri_status,'I')"), '=', $myRefstatusId);

        // Jika where dokter tidak kosong
        if ($myRefroomId != 'All') {
            $query->where('bangsal_id', $myRefroomId);
        }

        if ($myRefdrId != '') {
            $filterDataDokter = $query
                ->get()
                ->filter(function ($item) use ($myRefdrId) {
                    $datadaftarri_json = json_decode($item->datadaftarri_json, true) ?? [];
                    if (!empty($datadaftarri_json['pengkajianAwalPasienRawatInap']['levelingDokter'])) {
                        foreach ($datadaftarri_json['pengkajianAwalPasienRawatInap']['levelingDokter'] as $levelingDokterOnMyChildren) {
                            $levelingDokterOnMyChildrenValue = $levelingDokterOnMyChildren['drId'] ?? '';
                            if ($levelingDokterOnMyChildrenValue === $myRefdrId) {
                                return $item;
                            };
                        }
                    }
                })->pluck('rihdr_no')->toArray();

            $query->whereIn('rihdr_no', $filterDataDokter);
        }

        $query->where(function ($q) use ($mySearch) {
            $q->Where(DB::raw('upper(reg_name)'), 'like', '%' . strtoupper($mySearch) . '%')
                ->orWhere(DB::raw('upper(reg_no)'), 'like', '%' . strtoupper($mySearch) . '%')
                ->orWhere(DB::raw('upper(vno_sep)'), 'like', '%' . strtoupper($mySearch) . '%')
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
