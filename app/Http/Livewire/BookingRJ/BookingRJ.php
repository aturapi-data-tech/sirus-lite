<?php

namespace App\Http\Livewire\BookingRJ;

use Illuminate\Support\Facades\DB;


use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use App\Http\Traits\LOV\LOVDokter\LOVDokterTrait;


class BookingRJ extends Component
{
    use WithPagination, LOVDokterTrait;

    // primitive Variable
    public string $myTitle = 'Booking Rawat Jalan';
    public string $mySnipt = 'Daftar Pasien yang Melakukan Booking Layanan Rawat Jalan';
    public string $myProgram = 'Pasien Rawat Jalan';

    public array $myLimitPerPages = [5, 10, 15, 20, 100];
    // limit record per page -resetExcept////////////////
    public int $limitPerPage = 10;

    // LOV Nested
    public array $dokter;
    private function syncDataFormEntry(): void
    {
        $this->myTopBar['drId'] = $this->dokter['DokterId'] ?? '';
        $this->myTopBar['drName'] = $this->dokter['DokterDesc'] ?? '';
        $this->myTopBar['kdDokterBpjs'] = $this->dokter['kdDokterBpjs'] ?? '';
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
        'refDate' => '',

        'refStatusId' => 'Belum',
        'refStatusDesc' => 'Belum',
        'refStatusOptions' => [
            ['refStatusId' => 'Belum', 'refStatusDesc' => 'Belum'],
            ['refStatusId' => 'Checkin', 'refStatusDesc' => 'Checkin'],
            ['refStatusId' => 'Batal', 'refStatusDesc' => 'Batal'],
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

    public function updatedMytopbarRefdate()
    {
        $this->resetPage();
    }

    public function updatedMytopbarRefstatusid()
    {
        $this->resetPage();
    }



    // setter myTopBar Shift and myTopBar refDate
    private function settermyTopBarmyTopBarrefDate(): void
    {
        $this->myTopBar['refDate'] = Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y');
    }


    public function setBelum($nobooking): void
    {
        DB::table('referensi_mobilejkn_bpjs')->where('nobooking', $nobooking)->update(['status' => 'Belum']);
        $this->render();
    }
    public function setCheckin($nobooking): void
    {
        DB::table('referensi_mobilejkn_bpjs')->where('nobooking', $nobooking)->update(['status' => 'Checkin']);
        $this->render();
    }
    public function setBatal($nobooking): void
    {
        DB::table('referensi_mobilejkn_bpjs')->where('nobooking', $nobooking)->update(['status' => 'Batal']);
        $this->render();
    }


    // resert input private////////////////
    private function resetInputFields(): void
    {
        $this->resetValidation();
    }
    // resert input private////////////////

    protected $listeners = [
        // 'ListenerisOpenRJ' => 'ListenerisOpenRJ',
        'confirm_remove_record_RJp' => 'delete'
    ];

    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////


    // when new form instance
    public function mount()
    {
        $this->settermyTopBarmyTopBarrefDate();
    }


    // select data start////////////////
    public function render()
    {
        // LOV
        $this->syncLOV();
        // FormEntry
        $this->syncDataFormEntry();

        //////////////////////////////////////////
        // Query ///////////////////////////////
        //////////////////////////////////////////

        $query = DB::table('referensi_mobilejkn_bpjs')
            ->select(
                // DB::raw("to_char(rj_date,'dd/mm/yyyy hh24:mi:ss') AS rj_date"),
                // DB::raw("to_char(rj_date,'yyyymmddhh24miss') AS rj_date1"),
                'nobooking',
                'no_rawat',
                'nomorkartu',
                'nik',
                'nohp',
                'kodepoli',
                DB::raw("(select poli_desc from rsmst_polis where kd_poli_bpjs=referensi_mobilejkn_bpjs.kodepoli)poli_desc"),
                'pasienbaru',
                'norm',
                'kodedokter',
                DB::raw("(select dr_name from rsmst_doctors where kd_dr_bpjs=referensi_mobilejkn_bpjs.kodedokter and rownum = 1)dr_name "),
                DB::raw("to_char(to_date(tanggalperiksa,'yyyy-mm-dd'),'dd/mm/yyyy') as tanggalperiksa"),
                'jampraktek',
                'jeniskunjungan',
                'nomorreferensi',
                'nomorantrean',
                'angkaantrean',
                'estimasidilayani',
                'sisakuotajkn',
                'kuotajkn',
                'sisakuotanonjkn',
                'kuotanonjkn',
                'status',
                'validasi',
                'statuskirim',
                'keterangan_batal',
                'tanggalbooking',
                'daftardariapp',
                'reg_name',
                'address'

            )
            ->join('rsmst_pasiens', DB::raw("upper(referensi_mobilejkn_bpjs.norm)"), '=', 'rsmst_pasiens.reg_no')
            ->where(DB::raw("to_char(to_date(tanggalperiksa,'yyyy-mm-dd'),'dd/mm/yyyy')"), '=', $this->myTopBar['refDate'])
            ->where('status', '=', $this->myTopBar['refStatusId']);

        if (!empty($this->myTopBar['kdDokterBpjs'])) {
            $query->where('kodedokter', '=', $this->myTopBar['kdDokterBpjs']);
            dd($this->myTopBar['kdDokterBpjs']);
        }


        $query->where(function ($q) {
            $q->Where(DB::raw('upper(nobooking)'), 'like', '%' . strtoupper($this->refFilter) . '%')
                ->orWhere(DB::raw('upper(norm)'), 'like', '%' . strtoupper($this->refFilter) . '%')
                ->orWhere(DB::raw('upper(nik)'), 'like', '%' . strtoupper($this->refFilter) . '%')
                ->orWhere(DB::raw('upper(reg_name)'), 'like', '%' . strtoupper($this->refFilter) . '%')
                ->orWhere(DB::raw('upper(nomorkartu)'), 'like', '%' . strtoupper($this->refFilter) . '%');
        })
            ->orderBy('tanggalperiksa',  'asc')
            ->orderBy('kodedokter',  'asc')

            ->orderBy('tanggalbooking',  'asc');


        ////////////////////////////////////////////////
        // end Query
        ///////////////////////////////////////////////


        return view(
            'livewire.booking-r-j.booking-r-j',
            ['myQueryData' => $query->paginate($this->limitPerPage)]
        );
    }
    // select data end////////////////


}
