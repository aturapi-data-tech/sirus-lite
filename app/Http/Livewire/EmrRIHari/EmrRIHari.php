<?php

namespace App\Http\Livewire\EmrRIHari;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Traits\LOV\LOVDokter\LOVDokterTrait;


class EmrRIHari extends Component
{
    use WithPagination, LOVDokterTrait;


    // primitive Variable
    public string $myTitle = 'Upload FIle Rawat Inap Harian';
    public string $mySnipt = 'Rekam Rawat Inap Pasien';
    public string $myProgram = 'Pasien Rawat Inap Harian';

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
        'drOptions' => [],

        'klaimStatusId' => 'BPJS',
        'klaimStatusName' => 'BPJS',
        'klaimStatusOptions' => [
            [
                'klaimStatusId' => 'UMUM',
                'klaimStatusName' => 'UMUM'
            ],
            [
                'klaimStatusId' => 'BPJS',
                'klaimStatusName' => 'BPJS'
            ],
            [
                'klaimStatusId' => 'KRONIS',
                'klaimStatusName' => 'KRONIS'
            ],
        ],
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




    //  modal status////////////////

    public int $riHdrNoRef;
    public string $regNoRef;

    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////

    // is going to insert data////////////////


    public function settermyTopBarklaimStatusOptions($klaimStatusId, $klaimStatusName): void
    {

        $this->myTopBar['klaimStatusId'] = $klaimStatusId;
        $this->myTopBar['klaimStatusName'] = $klaimStatusName;
        $this->resetPage();
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
        $myRefklaimStatusId = $this->myTopBar['klaimStatusId'];




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

                DB::raw("(SELECT SUM(NVL(actd_price, 0) * NVL(actd_qty, 0)) FROM rstxn_riactdocs WHERE rihdr_no = rsview_rihdrs.rihdr_no) as jasa_dokter"),
                DB::raw("(SELECT SUM(NVL(actp_price, 0) * NVL(actp_qty, 0)) FROM rstxn_riactparams WHERE rihdr_no = rsview_rihdrs.rihdr_no) as jasa_medis"),
                DB::raw("(SELECT SUM(NVL(konsul_price, 0)) FROM rstxn_rikonsuls WHERE rihdr_no = rsview_rihdrs.rihdr_no) as konsultasi"),
                DB::raw("(SELECT SUM(NVL(visit_price, 0)) FROM rstxn_rivisits WHERE rihdr_no = rsview_rihdrs.rihdr_no) as visit"),
                'admin_age',
                'admin_status',
                DB::raw("(SELECT SUM(NVL(ribon_price, 0)) FROM rstxn_ribonobats WHERE rihdr_no = rsview_rihdrs.rihdr_no) as bon_resep"),
                DB::raw("(SELECT SUM(NVL(riobat_qty, 0) * NVL(riobat_price, 0)) FROM rstxn_riobats WHERE rihdr_no = rsview_rihdrs.rihdr_no) as obat_pinjam"),
                DB::raw("(SELECT SUM(NVL(riobat_qty, 0) * NVL(riobat_price, 0)) FROM rstxn_riobatrtns WHERE rihdr_no = rsview_rihdrs.rihdr_no) as return_obat"),
                DB::raw("(SELECT SUM(NVL(rirad_price, 0)) FROM rstxn_riradiologs WHERE rihdr_no = rsview_rihdrs.rihdr_no) as radiologi"),
                DB::raw("(SELECT SUM(NVL(lab_price, 0)) FROM rstxn_rilabs WHERE rihdr_no = rsview_rihdrs.rihdr_no) as laboratorium"),
                DB::raw("(SELECT SUM(NVL(ok_price, 0)) FROM rstxn_rioks WHERE rihdr_no = rsview_rihdrs.rihdr_no) as operasi"),
                DB::raw("(SELECT SUM(NVL(other_price, 0)) FROM rstxn_riothers WHERE rihdr_no = rsview_rihdrs.rihdr_no) as lain_lain"),
                DB::raw("(SELECT SUM(
                        NVL(rj_admin, 0) + NVL(poli_price, 0) + NVL(acte_price, 0) +
                        NVL(actp_price, 0) + NVL(actd_price, 0) + NVL(obat, 0) +
                        NVL(lab, 0) + NVL(rad, 0) + NVL(other, 0) + NVL(rs_admin, 0)
                    )
                    FROM rstxn_ritempadmins WHERE rihdr_no = rsview_rihdrs.rihdr_no) as rawat_jalan"),

                DB::raw("(SELECT SUM(NVL(room_price, 0) * ROUND(NVL(day, NVL(end_date, SYSDATE+1) - NVL(start_date, SYSDATE)))) FROM rsmst_trfrooms WHERE rihdr_no = rsview_rihdrs.rihdr_no) as total_room_price"),
                DB::raw("(SELECT SUM(NVL(perawatan_price, 0) * ROUND(NVL(day, NVL(end_date, SYSDATE+1) - NVL(start_date, SYSDATE)))) FROM rsmst_trfrooms WHERE rihdr_no = rsview_rihdrs.rihdr_no) as total_perawatan_price"),
                DB::raw("(SELECT SUM(NVL(common_service, 0) * ROUND(NVL(day, NVL(end_date, SYSDATE+1) - NVL(start_date, SYSDATE)))) FROM rsmst_trfrooms WHERE rihdr_no = rsview_rihdrs.rihdr_no) as total_common_service"),
            )
            ->where(DB::raw("nvl(ri_status,'I')"), '=', $myRefstatusId)
            ->whereIn('klaim_id', function ($query) use ($myRefklaimStatusId) {
                $query->select('klaim_id')
                    ->from('rsmst_klaimtypes')
                    ->where('klaim_status', '=', $myRefklaimStatusId);
            });


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
            'livewire.emr-r-i-hari.emr-r-i-hari',
            ['myQueryData' => $query->paginate($this->limitPerPage)]
        );
    }
    // select data end////////////////


}
