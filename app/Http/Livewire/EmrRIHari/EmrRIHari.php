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
    public string $myTitle = 'Upload FIle Rawat Inap - Status Inap';
    public string $mySnipt = 'Rekam Rawat Inap Pasien';
    public string $myProgram = 'Pasien Rawat Inap - Status Inap';

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

    private function buatTarifRawatInap(object $row): array
    {
        return [
            'Dokter'   => $row->jasa_dokter            ?? 0,
            'Medis'    => $row->jasa_medis             ?? 0,
            'Konsul'   => $row->konsultasi             ?? 0,
            'Visit'    => $row->visit                  ?? 0,
            'Operasi'  => $row->operasi                ?? 0,
            'Adm Umur' => $row->admin_age              ?? 0,
            'Adm Sts'  => $row->admin_status           ?? 0,
            'Obat'     => (
                ($row->obat_pinjam    ?? 0)
                - ($row->return_obat    ?? 0)
                + ($row->bon_resep      ?? 0)
            ),
            'Rad'      => $row->radiologi              ?? 0,
            'Lab'      => $row->laboratorium           ?? 0,
            'Kamar'    => $row->total_room_price       ?? 0,
            'Rawat'    => $row->total_perawatan_price  ?? 0,
            'Umum'     => $row->total_common_service   ?? 0,
            'Lain'     => $row->lain_lain              ?? 0,
            'TrfRJ'    => $row->rawat_jalan            ?? 0,
        ];
    }

    /**
     * Ambil semua metrics sekali untuk semua rihdr_no di current page,
     * lalu transform Collection-nya dengan men‐attach metrics + tarif_total.
     */
    private function perhitunganTarifRawatInap(\Illuminate\Pagination\LengthAwarePaginator $paginator)
    {
        // get raw collection & IDs
        $collection = $paginator->getCollection();
        $ids        = $collection->pluck('rihdr_no')->all();

        // 1) Ambil totinacbg_temp & totalri_temp langsung dari VIEW
        $totInacbgTemp = DB::table('rsview_rihdrs')
            ->whereIn('rihdr_no', $ids)
            ->pluck('totinacbg_temp', 'rihdr_no');

        $totalriTemp = DB::table('rsview_rihdrs')
            ->whereIn('rihdr_no', $ids)
            ->pluck('totalri_temp', 'rihdr_no');

        // 2) Batch‐fetch tiap komponen tarif
        $jasaDokter = DB::table('rstxn_riactdocs')
            ->select('rihdr_no', DB::raw('SUM(actd_price * actd_qty) AS total'))
            ->whereIn('rihdr_no', $ids)
            ->groupBy('rihdr_no')
            ->pluck('total', 'rihdr_no');

        $jasaMedis = DB::table('rstxn_riactparams')
            ->select('rihdr_no', DB::raw('SUM(actp_price * actp_qty) AS total'))
            ->whereIn('rihdr_no', $ids)
            ->groupBy('rihdr_no')
            ->pluck('total', 'rihdr_no');

        $konsultasi = DB::table('rstxn_rikonsuls')
            ->select('rihdr_no', DB::raw('SUM(konsul_price) AS total'))
            ->whereIn('rihdr_no', $ids)
            ->groupBy('rihdr_no')
            ->pluck('total', 'rihdr_no');

        $visit = DB::table('rstxn_rivisits')
            ->select('rihdr_no', DB::raw('SUM(visit_price) AS total'))
            ->whereIn('rihdr_no', $ids)
            ->groupBy('rihdr_no')
            ->pluck('total', 'rihdr_no');

        $bonResep = DB::table('rstxn_ribonobats')
            ->select('rihdr_no', DB::raw('SUM(ribon_price) AS total'))
            ->whereIn('rihdr_no', $ids)
            ->groupBy('rihdr_no')
            ->pluck('total', 'rihdr_no');

        $obatPinjam = DB::table('rstxn_riobats')
            ->select('rihdr_no', DB::raw('SUM(riobat_qty * riobat_price) AS total'))
            ->whereIn('rihdr_no', $ids)
            ->groupBy('rihdr_no')
            ->pluck('total', 'rihdr_no');

        $returnObat = DB::table('rstxn_riobatrtns')
            ->select('rihdr_no', DB::raw('SUM(riobat_qty * riobat_price) AS total'))
            ->whereIn('rihdr_no', $ids)
            ->groupBy('rihdr_no')
            ->pluck('total', 'rihdr_no');

        $radiologi = DB::table('rstxn_riradiologs')
            ->select('rihdr_no', DB::raw('SUM(rirad_price) AS total'))
            ->whereIn('rihdr_no', $ids)
            ->groupBy('rihdr_no')
            ->pluck('total', 'rihdr_no');

        $laboratorium = DB::table('rstxn_rilabs')
            ->select('rihdr_no', DB::raw('SUM(lab_price) AS total'))
            ->whereIn('rihdr_no', $ids)
            ->groupBy('rihdr_no')
            ->pluck('total', 'rihdr_no');

        $operasi = DB::table('rstxn_rioks')
            ->select('rihdr_no', DB::raw('SUM(ok_price) AS total'))
            ->whereIn('rihdr_no', $ids)
            ->groupBy('rihdr_no')
            ->pluck('total', 'rihdr_no');

        $adminStatus = DB::table('rstxn_rihdrs')
            ->select('rihdr_no', DB::raw('SUM(NVL(admin_status, 0)) AS total'))
            ->whereIn('rihdr_no', $ids)
            ->groupBy('rihdr_no')
            ->pluck('total', 'rihdr_no');

        $adminAge = DB::table('rstxn_rihdrs')
            ->select('rihdr_no', DB::raw('SUM(NVL(admin_age, 0)) AS total'))
            ->whereIn('rihdr_no', $ids)
            ->groupBy('rihdr_no')
            ->pluck('total', 'rihdr_no');

        $lainLain = DB::table('rstxn_riothers')
            ->select('rihdr_no', DB::raw('SUM(other_price) AS total'))
            ->whereIn('rihdr_no', $ids)
            ->groupBy('rihdr_no')
            ->pluck('total', 'rihdr_no');

        $rawatJalan = DB::table('rstxn_ritempadmins')
            ->select('rihdr_no', DB::raw('SUM(
                rj_admin + poli_price + acte_price +
                actp_price + actd_price + obat +
                rad + lab + other + rs_admin
            ) AS total'))
            ->whereIn('rihdr_no', $ids)
            ->groupBy('rihdr_no')
            ->pluck('total', 'rihdr_no');

        // 3) Tarif kamar / perawatan / common service dengan CEIL-DECODE model
        $roomPrice = DB::table('rsmst_trfrooms')
            ->select('rihdr_no', DB::raw("
            SUM(
                NVL(room_price,0)
                * NVL(
                    day,
                    CEIL(
                    DECODE(
                        NVL(end_date,SYSDATE) - NVL(start_date,SYSDATE),
                        0,1,
                        NVL(end_date,SYSDATE)-NVL(start_date,SYSDATE)
                    )
                    )
                )
            ) AS total
            "))
            ->whereIn('rihdr_no', $ids)
            ->groupBy('rihdr_no')
            ->pluck('total', 'rihdr_no');

        $perawatanPrice = DB::table('rsmst_trfrooms')
            ->select('rihdr_no', DB::raw("
            SUM(
                NVL(perawatan_price,0)
                * NVL(
                    day,
                    CEIL(
                    DECODE(
                        NVL(end_date,SYSDATE) - NVL(start_date,SYSDATE),
                        0,1,
                        NVL(end_date,SYSDATE)-NVL(start_date,SYSDATE)
                    )
                    )
                )
            ) AS total
            "))
            ->whereIn('rihdr_no', $ids)
            ->groupBy('rihdr_no')
            ->pluck('total', 'rihdr_no');

        $commonService = DB::table('rsmst_trfrooms')
            ->select('rihdr_no', DB::raw("
            SUM(
                NVL(common_service,0)
                * NVL(
                    day,
                    CEIL(
                    DECODE(
                        NVL(end_date,SYSDATE) - NVL(start_date,SYSDATE),
                        0,1,
                        NVL(end_date,SYSDATE)-NVL(start_date,SYSDATE)
                    )
                    )
                )
            ) AS total
            "))
            ->whereIn('rihdr_no', $ids)
            ->groupBy('rihdr_no')
            ->pluck('total', 'rihdr_no');

        $new = $collection->map(function ($row) use (
            $totInacbgTemp,
            $totalriTemp,
            $jasaDokter,
            $jasaMedis,
            $konsultasi,
            $visit,
            $bonResep,
            $obatPinjam,
            $returnObat,
            $radiologi,
            $laboratorium,
            $operasi,
            $adminStatus,
            $adminAge,
            $lainLain,
            $rawatJalan,
            $roomPrice,
            $perawatanPrice,
            $commonService,
        ) {
            $key = $row->rihdr_no;
            $row->totinacbg_temp         = $totInacbgTemp[$key]      ?? 0;
            $row->totalri_temp           = $totalriTemp[$key]        ?? 0;
            $row->jasa_dokter            = $jasaDokter[$key]         ?? 0;
            $row->jasa_medis             = $jasaMedis[$key]          ?? 0;
            $row->konsultasi             = $konsultasi[$key]         ?? 0;
            $row->visit                  = $visit[$key]              ?? 0;
            $row->bon_resep              = $bonResep[$key]           ?? 0;
            $row->obat_pinjam            = $obatPinjam[$key]         ?? 0;
            $row->return_obat            = $returnObat[$key]         ?? 0;
            $row->radiologi              = $radiologi[$key]          ?? 0;
            $row->laboratorium           = $laboratorium[$key]       ?? 0;
            $row->operasi                = $operasi[$key]            ?? 0;
            $row->admin_status           = $adminStatus[$key]        ?? 0;
            $row->admin_age              = $adminAge[$key]           ?? 0;
            $row->lain_lain              = $lainLain[$key]           ?? 0;
            $row->rawat_jalan            = $rawatJalan[$key]         ?? 0;
            $row->total_room_price       = $roomPrice[$key]          ?? 0;
            $row->total_perawatan_price  = $perawatanPrice[$key]     ?? 0;
            $row->total_common_service   = $commonService[$key]      ?? 0;

            // dan terakhir bangun tarif_rs / tarif_total seperti biasa
            $row->tarif_rs    = $this->buatTarifRawatInap($row);
            $row->tarif_total = array_sum($row->tarif_rs);

            return $row;
        });

        $paginator->setCollection($new);

        return $paginator;
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

        $query = $query->where(function ($q) use ($mySearch) {
            $q->Where(DB::raw('upper(reg_name)'), 'like', '%' . strtoupper($mySearch) . '%')
                ->orWhere(DB::raw('upper(reg_no)'), 'like', '%' . strtoupper($mySearch) . '%')
                ->orWhere(DB::raw('upper(vno_sep)'), 'like', '%' . strtoupper($mySearch) . '%')
                ->orWhere(DB::raw('upper(dr_name)'), 'like', '%' . strtoupper($mySearch) . '%');
        })
            ->orderBy('entry_date1',  'desc')
            ->orderBy('dr_name',  'desc')
            ->paginate($this->limitPerPage);
        ////////////////////////////////////////////////
        // end Query
        ///////////////////////////////////////////////
        $query = $this->perhitunganTarifRawatInap($query);



        return view(
            'livewire.emr-r-i-hari.emr-r-i-hari',
            ['myQueryData' => $query]
        );
    }
    // select data end////////////////


}
