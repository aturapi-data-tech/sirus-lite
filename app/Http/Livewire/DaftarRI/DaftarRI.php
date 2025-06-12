<?php

namespace App\Http\Livewire\DaftarRI;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Traits\LOV\LOVDokter\LOVDokterTrait;


class DaftarRI extends Component
{
    use WithPagination, LOVDokterTrait;



    // primitive Variable
    public string $myTitle = 'Daftar Rawat Inap';
    public string $mySnipt = 'Daftar Pasien';
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
        $this->emit('listenerRegNo', $this->regNoRef);
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
            'livewire.daftar-r-i.daftar-r-i',
            ['myQueryData' => $query->paginate($this->limitPerPage)]
        );
    }
    // select data end////////////////


}
