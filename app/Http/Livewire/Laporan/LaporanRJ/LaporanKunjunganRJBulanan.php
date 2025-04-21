<?php

namespace App\Http\Livewire\Laporan\LaporanRJ;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Traits\LOV\LOVDokter\LOVDokterTrait;


class LaporanKunjunganRJBulanan extends Component
{

    use WithPagination, LOVDokterTrait;

    public array $myTopBar = [
        'drId' => '',
        'drName' => '',
    ];
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


    public string $myMonth = '';
    public string $myYear = '';

    public array $jenisKunjungan = [
        'jenisKunjunganId' => 'All',
        'jenisKunjunganDesc' => 'All',
        'jenisKunjunganOptions' => [
            ['jenisKunjunganId' => 'All', 'jenisKunjunganDesc' => 'All'],
            ['jenisKunjunganId' => '1', 'jenisKunjunganDesc' => 'Rujukan FKTP'],
            ['jenisKunjunganId' => '2', 'jenisKunjunganDesc' => 'Rujukan Internal'],
            ['jenisKunjunganId' => '3', 'jenisKunjunganDesc' => 'Kontrol'],
            ['jenisKunjunganId' => '4', 'jenisKunjunganDesc' => 'Rujukan Antar RS'],

        ]
    ];

    public array $postInap = [];

    // public function updatedPostInap($value)
    // {
    //     dd($this->postInap);
    // }

    public function render()
    {
        // LOV
        $this->syncLOV();
        // FormEntry
        $this->syncDataFormEntry();

        $myRefjenisKunjunganId = $this->jenisKunjungan['jenisKunjunganId'];
        $myRefpostInap = $this->postInap;
        $myRefdrId = $this->myTopBar['drId'];

        //////////////////////////////////////////
        // Query ///////////////////////////////
        //////////////////////////////////////////
        $queryData = DB::table('rsview_rjkasir')
            ->select(
                DB::raw("to_char(rj_date,'dd/mm/yyyy hh24:mi:ss') AS rj_date"),
                DB::raw("to_char(rj_date,'yyyymmddhh24miss') AS rj_date1"),
                'rj_no',
                'reg_no',
                'reg_name',
                'sex',
                'address',
                'thn',
                DB::raw("to_char(birth_date,'dd/mm/yyyy') AS birth_date"),
                'poli_id',
                'poli_desc',
                'dr_id',
                'dr_name',
                'klaim_id',
                'shift',
                'vno_sep',
                'no_antrian',
                'rj_status',
                'nobooking',
                'datadaftarpolirj_json',
            )
            // ->where(DB::raw("nvl(erm_status,'A')"), '=', $myRefstatusId)
            ->where('rj_status', '!=', 'F')
            ->where('klaim_id', '!=', 'KR')

            // ->where('shift', '=', $myRefshift)
            ->where(DB::raw("to_char(rj_date,'mm/yyyy')"), '=', "{$this->myMonth}/{$this->myYear}");

        if ($myRefdrId != '') {
            $queryData->where('dr_id', $myRefdrId);
        }

        if ($myRefjenisKunjunganId != 'All') {
            $filterDataJenisKunjungan = $queryData
                ->get()
                ->filter(function ($item) use ($myRefjenisKunjunganId, $myRefpostInap) {
                    $dataDaftarPoliRJ = json_decode($item->datadaftarpolirj_json, true) ?? [];
                    // Jika bukan kontrol (bukan 3) → return langsung
                    if (
                        !empty($dataDaftarPoliRJ['kunjunganId'])
                        && $dataDaftarPoliRJ['kunjunganId'] !== '3'
                        && $dataDaftarPoliRJ['kunjunganId'] === $myRefjenisKunjunganId
                    ) {
                        return $item;
                    }

                    // // Jika kontrol (3) → cek postInap
                    if (
                        !empty($dataDaftarPoliRJ['kunjunganId'])
                        && $dataDaftarPoliRJ['kunjunganId'] === '3'
                        && $dataDaftarPoliRJ['kunjunganId'] === $myRefjenisKunjunganId
                        && $dataDaftarPoliRJ['postInap'] === $myRefpostInap
                    ) {
                        return $item;
                    }
                })->pluck('rj_no')->toArray();

            $queryData->whereIn('rj_no', $filterDataJenisKunjungan);
        }

        $queryData
            ->orderBy('dr_name',  'asc')
            // ->orderBy('no_antrian',  'desc')
            ->orderBy('rj_date1',  'desc');

        ////////////////////////////////////////////////
        // end Query
        ///////////////////////////////////////////////
        // $this->queryData = $queryData;

        return view(
            'livewire.laporan.laporan-r-j.laporan-kunjungan-r-j-bulanan',
            ['queryData' => $queryData->paginate(10)]
        );
    }
}
