<?php

namespace App\Http\Livewire\SetupHfisBpjs;

use Livewire\Component;
use Livewire\WithPagination;

use Carbon\Carbon;

use App\Http\Traits\BPJS\AntrianTrait;
use App\Http\Traits\BPJS\VclaimTrait;
use Illuminate\Support\Facades\DB;





class SetupHfisBpjs extends Component
{
    use WithPagination;


    //  table LOV////////////////
    public $hfisLov = [];
    public $hfisLovStatus = 0;
    public $hfisLovSearch = '';

    // get Jadwal Dokter BPJS
    public $jadwal_dokter = [];
    public $search;
    public $dateRef = '';

    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////







    // resert page pagination when coloumn search change ////////////////
    public function updatedSearch(): void
    {
        // updatedhfisLovSearch->run
        $this->hfisLovSearch = $this->search;
        $this->updatedHfislovsearch();
    }



    // is going to insert data////////////////
    public function create()
    {
        $this->openModal();
    }



    // /////////LOV////////////
    // /////////hfis////////////
    public function updatedHfislovsearch()
    {
        // Variable Search
        $search = $this->hfisLovSearch;

        $this->hfisLovStatus = true;

        // if there is no id found and check (min 3 char on search)
        if (strlen($search) < 3) {
            $this->hfisLov = [];
        } else {

            // Variable Search Get data BPJS
            $HttpGetBpjs =  VclaimTrait::ref_poliklinik($search)->getOriginalContent();

            if ($HttpGetBpjs['metadata']['code'] == 200) {
                $this->hfisLov = $HttpGetBpjs['response']['poli'];
                $this->emit('toastr-success', $HttpGetBpjs['metadata']['code'] . ' ' . $HttpGetBpjs['metadata']['message']);
            } else {
                $this->hfisLov = [];
                $this->emit('toastr-error', $HttpGetBpjs['metadata']['code'] . ' ' . $HttpGetBpjs['metadata']['message']);
            }
        }
    }
    // /////////////////////
    // LOV selected start
    public function setMyhfisLov($id)
    {

        // Variable Search Get data BPJS
        $dateRef = Carbon::createFromFormat('d/m/Y', $this->dateRef)->format('Y-m-d');
        $HttpGetBpjs =  AntrianTrait::ref_jadwal_dokter($id, $dateRef)->getOriginalContent();

        // Variable Search Get data BPJS
        if ($HttpGetBpjs['metadata']['code'] == 200) {
            $this->jadwal_dokter = $HttpGetBpjs['response'];
            // dd($this->jadwal_dokter);

            $this->emit('toastr-success', $HttpGetBpjs['metadata']['code'] . ' ' . $HttpGetBpjs['metadata']['message']);
        } else {
            $this->jadwal_dokter = [];
            $this->emit('toastr-error', $HttpGetBpjs['metadata']['code'] . ' ' . $HttpGetBpjs['metadata']['message']);
        }


        $this->hfisLovStatus = false;
        // $this->hfisLovSearch = '';
    }
    // LOV selected end
    // /////////////////////



    public function updateJadwalRS($poliIdBPJS, $drIdBPJS, $nmDokterBPJS, $dayId, $jamPraktek, $kuota)
    {
        // cek poli
        $kdPoliBpjsSyncRs = DB::table('rsmst_polis')->where('kd_poli_bpjs',  $poliIdBPJS ?? '')->first();

        if (!$kdPoliBpjsSyncRs) {
            dd("Poli tidak ditemukan / Data Dokter belum di syncronuze " . $poliIdBPJS);
        }

        // cek dokter
        $kdDrBpjsSyncRs = DB::table('rsmst_doctors')->where('kd_dr_bpjs',  $drIdBPJS ?? '')->first();

        if (!$kdDrBpjsSyncRs) {
            // dd("Dokter tidak ditemukan / Data Dokter belum di syncronuze " . $drIdBPJS . ' ' . $nmDokterBPJS);
            $this->emit('toastr-success', "Dokter tidak ditemukan / Data Dokter belum di syncronuze " . $drIdBPJS . ' ' . $nmDokterBPJS);
        }

        // cek dayId
        $kd_hari_bpjs = DB::table('scmst_scdays')->where('day_id',  $dayId ?? '')->first();

        // if (!$kd_hari_bpjs) {
        //     dd("Kode Hari Tidak ditemukan " . $dayId);
        // }

        if (!$kuota) {
            dd("Kuota Tidak Tersedia " . $kuota);
        }

        if (!$jamPraktek) {
            dd("Jam Praktek Tidak Tersedia " . $jamPraktek);
        }

        $jammulai   = substr($jamPraktek, 0, 5);
        $jamselesai = substr($jamPraktek, 6, 5);

        // shift
        $findShift = DB::table('rstxn_shiftctls')
            ->select('shift')
            ->whereRaw("'" . Carbon::createFromFormat('H:i', $jammulai)->format('H:i:s') . "' between
            shift_start and shift_end")
            ->first();

        // if (!$findShift) {
        //     dd("Shift Invalid");
        // }

        // cek jadwal RS
        $jadwalRS = DB::table('scmst_scpolis')
            ->where('day_id',  $dayId ? $dayId : '')
            ->where('poli_id',  $kdPoliBpjsSyncRs->poli_id ?? '')
            ->where('dr_id',  $kdDrBpjsSyncRs->dr_id ?? '')
            ->where('sc_poli_ket', $jamPraktek)
            ->first();

        if (!$jadwalRS) {
            // insert
            try {
                DB::table('scmst_scpolis')->insert([
                    'sc_poli_status_' => '1',
                    'sc_poli_ket' => $jamPraktek,
                    'day_id' => $dayId,
                    'poli_id' => $kdPoliBpjsSyncRs->poli_id ?? '',
                    'dr_id' => $kdDrBpjsSyncRs->dr_id ?? '',
                    'shift' => $findShift->shift ?? 1,
                    'mulai_praktek' => $jammulai . ':00',
                    'pelayanan_perp_asien' => '',
                    'no_urut' => '1',
                    'kuota' => $kuota,
                    'selesai_praktek' => $jamselesai . ':00',
                ]);
                $this->emit('toastr-success', 'Insert OK');
            } catch (\Exception $e) {
                // dd($e->getMessage());
                $this->emit('toastr-error', 'Insert ' . $e->getMessage());
            }
        } else {
            // update
            try {
                DB::table('scmst_scpolis')
                    ->where('day_id',  $dayId ?? '')
                    ->where('poli_id',  $kdPoliBpjsSyncRs->poli_id ?? '')
                    ->where('dr_id',  $kdDrBpjsSyncRs->dr_id ?? '')
                    ->where('sc_poli_ket', $jamPraktek)

                    ->update([
                        'sc_poli_status_' => '1',
                        'sc_poli_ket' => $jamPraktek,
                        'day_id' => $dayId,
                        'poli_id' => $kdPoliBpjsSyncRs->poli_id,
                        'dr_id' => $kdDrBpjsSyncRs->dr_id,
                        'shift' => $findShift->shift ?? 1,
                        'mulai_praktek' => $jammulai . ':00',
                        'pelayanan_perp_asien' => '',
                        'no_urut' => '1',
                        'kuota' => $kuota,
                        'selesai_praktek' => $jamselesai . ':00',
                    ]);
                $this->emit('toastr-success', 'Update OK');
            } catch (\Exception $e) {
                // dd($e->getMessage());
                $this->emit('toastr-error', 'Update ' . $e->getMessage());
            }
        }
    }


    // when new form instance
    public function mount()
    {
        $this->dateRef = Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y');
        $this->jadwal_dokter = [];
    }


    public function updateDataHfisBpjsToRsAll()
    {
        DB::table('scmst_scpolis')->delete();
        for ($i = 1; $i <= 7; $i++) {
            $jadwalDokter = DB::table('rsmst_polis')
                ->select(
                    'poli_desc',
                    'kd_poli_bpjs',
                )
                ->whereNotNull('kd_poli_bpjs')
                ->orderBy('poli_desc', 'ASC')
                ->get();

            foreach ($jadwalDokter as $item) {
                $this->dateRef = Carbon::createFromFormat('d/m/Y', $this->dateRef)->format('d/m/Y');
                $this->setMyhfisLov($item->kd_poli_bpjs);
                foreach ($this->jadwal_dokter as $key => $jadwal_dokter) {
                    $this->updateJadwalRS($jadwal_dokter['kodepoli'], $jadwal_dokter['kodedokter'], $jadwal_dokter['namadokter'], $jadwal_dokter['hari'], $jadwal_dokter['jadwal'], $jadwal_dokter['kapasitaspasien']);
                }
                $this->dateRef = Carbon::createFromFormat('d/m/Y', $this->dateRef)->addDay()->format('d/m/Y');
            }
            // dd($this->jadwal_dokter);
        }
        $this->emit('toastr-success', 'Update OK');
    }

    private function setHari(): array
    {
        $hari = [];
        for ($i = 1; $i <= 7; $i++) {
            $days = DB::table('scmst_scdays')->where('day_id', $i)->first()->day_desc;
            $jadwalDokter = DB::table('scview_scpolis')
                ->select(
                    'sc_poli_ket',
                    'day_id',
                    'dr_id',
                    'dr_name',
                    'poli_desc',
                    'poli_id',
                    'sc_poli_status_',
                    'mulai_praktek',
                    'selesai_praktek',
                    'shift',
                    'kuota',
                    'no_urut'
                )
                ->where('sc_poli_status_', '1')
                ->where('day_id', $i)
                ->orderBy('no_urut', 'ASC')
                ->orderBy('mulai_praktek', 'ASC')
                ->orderBy('shift', 'ASC')
                ->orderBy('dr_id', 'ASC')

                ->get();

            $hari[] = [
                'day_id' => $i,
                'day_desc' => $days ?? '-',
                'jadwalDokter' => json_decode(json_encode($jadwalDokter, true), true) ?? []
            ];
        }

        return $hari;
    }

    private function getDokterBlmTerJadwal(array $dokterTerJadwal = []): array
    {

        $dokterBlmTerJadwal = DB::table('rsmst_doctors')
            ->select('dr_id',                'dr_name', 'rsmst_polis.poli_id', 'rsmst_polis.poli_desc')
            ->join('rsmst_polis', 'rsmst_polis.poli_id', 'rsmst_doctors.poli_id')
            ->where('active_status', '=', '1')
            ->whereNotIn('dr_id',  $dokterTerJadwal)
            ->orderBy('dr_id', 'ASC')
            ->get();

        return (json_decode(json_encode($dokterBlmTerJadwal, true), true) ?? []);
    }

    // select data start////////////////
    public function render()
    {
        $hari = $this->setHari();
        $dataDokterTerJadwal = collect($hari)
            ->flatMap(function ($day) {
                return collect($day['jadwalDokter'])->pluck('dr_id');
            })
            ->unique()
            ->values()
            ->toArray();

        $getDokterBlmTerJadwalHari = $this->getDokterBlmTerJadwal($dataDokterTerJadwal);

        return view(
            'livewire.setup-hfis-bpjs.setup-hfis-bpjs',
            [
                'hfis' => [],
                'myTitle' => 'HFIS BPJS',
                'mySnipt' => 'Data HFIS BPJS',
                'myProgram' => 'HFIS BPJS',
                'myLimitPerPages' => [5, 10, 15, 20, 100],
                'myHari' => $hari,
                'myDokterBlmTerJadwalHari' => $getDokterBlmTerJadwalHari
            ]
        );
    }
    // select data end////////////////
}
