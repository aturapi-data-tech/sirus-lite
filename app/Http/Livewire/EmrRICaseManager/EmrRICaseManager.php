<?php

namespace App\Http\Livewire\EmrRICaseManager;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Traits\LOV\LOVDokter\LOVDokterTrait;


class EmrRICaseManager extends Component
{
    use WithPagination, LOVDokterTrait;


    // primitive Variable
    public string $myTitle   = 'Case Manager Rawat Inap';
    public string $mySnipt   = 'Form MPP & Monitoring';
    public string $myProgram = 'Case Manager RI';

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


    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////



    public function render()
    {
        $this->gettermyTopBarRoomOptions();

        // LOV
        $this->syncLOV();
        // FormEntry
        $this->syncDataFormEntry();

        // set mySearch
        $mySearch      = $this->refFilter;
        $myRefstatusId = $this->myTopBar['refStatusId']; // default 'I'
        $myRefroomId   = $this->myTopBar['roomId'];      // 'All' atau bangsal_id
        $myRefdrId     = $this->myTopBar['drId'];        // filter dokter dari LOV

        /**
         * 1️⃣ Ambil dulu rihdr_no yang:
         *    - masih INAP
         *    - entry_date bulan ini (mm/yyyy)
         *    - (optional) filter bangsal
         *    - punya Case Manager (formMPP → formA/formB)
         *    - (optional) filter dokter berdasarkan levelingDokter di JSON
         */
        $baseQuery = DB::table('rsview_rihdrs')
            ->select(
                'rihdr_no',
                'datadaftarri_json'
            )
            ->where(DB::raw("nvl(ri_status,'I')"), '=', $myRefstatusId);
        // 🔒 Entry bulan ini saja (mm/yyyy)
        // ->whereRaw("to_char(entry_date,'mm/yyyy') = to_char(sysdate,'mm/yyyy')");

        // Filter bangsal kalau tidak 'All'
        if ($myRefroomId !== 'All') {
            $baseQuery->where('bangsal_id', $myRefroomId);
        }

        // Ambil semua dulu untuk difilter di level Collection (JSON)
        $rows = $baseQuery->get();

        $filteredRiHdrNo = $rows->filter(function ($item) use ($myRefdrId) {
            $json = json_decode($item->datadaftarri_json, true) ?? [];

            // ✅ WAJIB: harus punya Case Manager (Form A / B)
            $formMPP   = $json['formMPP'] ?? [];
            $hasFormA  = !empty($formMPP['formA'] ?? []);
            $hasFormB  = !empty($formMPP['formB'] ?? []);
            $hasCmForm = $hasFormA || $hasFormB;

            if (!$hasCmForm) {
                return false;
            }

            // Jika tidak ada filter dokter → cukup sampai sini
            if ($myRefdrId === '') {
                return true;
            }

            // Jika ada filter dokter → cek di pengkajianAwalPasienRawatInap.levelingDokter
            $levelingDokter = $json['pengkajianAwalPasienRawatInap']['levelingDokter'] ?? [];
            if (!is_array($levelingDokter) || empty($levelingDokter)) {
                return false;
            }

            foreach ($levelingDokter as $level) {
                if (($level['drId'] ?? '') === $myRefdrId) {
                    return true;
                }
            }

            return false;
        })
            ->pluck('rihdr_no')
            ->toArray();

        //////////////////////////////////////////
        // 2️⃣ Query utama untuk ditampilkan (dengan search + paginate)
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
            ->where(DB::raw("nvl(ri_status,'I')"), '=', $myRefstatusId)
            // 🔒 tetap jaga hanya bulan ini (redundan, tapi aman)
            // ->whereRaw("to_char(entry_date,'mm/yyyy') = to_char(sysdate,'mm/yyyy')")
            // 🔒 hanya rihdr_no yang sudah difilter di atas (punya Case Manager + filter dokter + bangsal)
            ->whereIn('rihdr_no', $filteredRiHdrNo);

        // Search bar
        $query->where(function ($q) use ($mySearch) {
            $q->where(DB::raw('upper(reg_name)'), 'like', '%' . strtoupper($mySearch) . '%')
                ->orWhere(DB::raw('upper(reg_no)'), 'like', '%' . strtoupper($mySearch) . '%')
                ->orWhere(DB::raw('upper(vno_sep)'), 'like', '%' . strtoupper($mySearch) . '%')
                ->orWhere(DB::raw('upper(dr_name)'), 'like', '%' . strtoupper($mySearch) . '%');
        })
            ->orderBy('entry_date1', 'desc')
            ->orderBy('dr_name', 'desc');

        return view(
            'livewire.emr-r-i-case-manager.emr-r-i-case-manager',
            ['myQueryData' => $query->paginate($this->limitPerPage)]
        );
    }

    // select data end////////////////


}
