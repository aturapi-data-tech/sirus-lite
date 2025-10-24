<?php

namespace App\Http\Livewire\EmrUGD\AdministrasiUGD;

use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Livewire\Component;

use App\Http\Traits\EmrUGD\EmrUGDTrait;


class AdministrasiUGD extends Component
{
    use  EmrUGDTrait;

    protected $listeners = [
        'ugd:refresh-summary' => 'sumAll',
    ];

    // dataDaftarUgd RJ
    public $rjNoRef;
    public $dataDaftarUgd = [];

    public int $sumRsAdmin;
    public int $sumRjAdmin;
    public int $sumPoliPrice;

    public int $sumJasaKaryawan;
    public int $sumJasaDokter;
    public int $sumJasaMedis;

    public int $sumObat;
    public int $sumLaboratorium;
    public int $sumRadiologi;

    public int $sumLainLain;
    public int $sumtrfRJ;

    public int $sumTotalRJ;


    public string $activeTabAdministrasi = "JasaKaryawan";
    public array $EmrMenuAdministrasi = [
        [
            'ermMenuId' => 'JasaKaryawan',
            'ermMenuName' => 'Jasa Karyawan'
        ],
        [
            'ermMenuId' => 'JasaDokter',
            'ermMenuName' => 'Jasa Dokter'
        ],
        [
            'ermMenuId' => 'JasaMedis',
            'ermMenuName' => 'Jasa Medis'
        ],
        [
            'ermMenuId' => 'Obat',
            'ermMenuName' => 'Obat'
        ],
        [
            'ermMenuId' => 'Laboratorium',
            'ermMenuName' => 'Laboratorium'
        ],
        [
            'ermMenuId' => 'Radiologi',
            'ermMenuName' => 'Radiologi'
        ],
        [
            'ermMenuId' => 'LainLain',
            'ermMenuName' => 'Lain-Lain'
        ],

    ];


    public function sumAll()
    {
        $this->sumAdmin();
    }

    private function sumAdmin()
    {
        $sumAdmin = $this->findData($this->rjNoRef);

        $this->sumRsAdmin = $sumAdmin['rsAdmin'] ? $sumAdmin['rsAdmin'] : 0;
        $this->sumRjAdmin = $sumAdmin['rjAdmin'] ? $sumAdmin['rjAdmin'] : 0;
        $this->sumPoliPrice = $sumAdmin['poliPrice'] ? $sumAdmin['poliPrice'] : 0;

        $this->sumJasaKaryawan = isset($sumAdmin['JasaKaryawan']) ? collect($sumAdmin['JasaKaryawan'])->sum('JasaKaryawanPrice') : 0;
        $this->sumJasaMedis = isset($sumAdmin['JasaMedis']) ? collect($sumAdmin['JasaMedis'])->sum('JasaMedisPrice') : 0;
        $this->sumJasaDokter = isset($sumAdmin['JasaDokter']) ? collect($sumAdmin['JasaDokter'])->sum('JasaDokterPrice') : 0;


        $this->sumObat = collect($sumAdmin['rjObat'])->sum((function ($obat) {
            return $obat['qty'] * $obat['price'];
        }));

        $this->sumLaboratorium = collect($sumAdmin['rjLab'])->sum((function ($rjLab) {
            return $rjLab['lab_price'];
        }));

        $this->sumRadiologi = collect($sumAdmin['rjRad'])->sum((function ($rjRad) {
            return $rjRad['rad_price'];
        }));


        $this->sumLainLain = isset($sumAdmin['LainLain']) ? collect($sumAdmin['LainLain'])->sum('LainLainPrice') : 0;

        $this->sumtrfRJ = collect($sumAdmin['rjtrfRj'])->sum((function ($trfRj) {
            return $trfRj['rj_admin'] + $trfRj['poli_price'] + $trfRj['acte_price'] + $trfRj['actp_price'] + $trfRj['actd_price'] + $trfRj['obat'] + $trfRj['lab'] + $trfRj['rad'] + $trfRj['other'] + $trfRj['rs_admin'];
        }));




        $this->sumTotalRJ = $this->sumPoliPrice + $this->sumRjAdmin + $this->sumRsAdmin  + $this->sumJasaKaryawan + $this->sumJasaDokter + $this->sumJasaMedis + $this->sumLainLain + $this->sumObat + $this->sumLaboratorium + $this->sumRadiologi + $this->sumtrfRJ;
    }


    public function setSelesaiAdministrasiStatus(string $rjNo): void
    {
        if (!$rjNo) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('rjNo kosong.');
            return;
        }

        $lockKey = "ugd:{$rjNo}"; // 1 lock per rjNo
        try {
            Cache::lock($lockKey, 5)->block(3, function () use ($rjNo) {
                DB::transaction(function () use ($rjNo) {
                    // Ambil FRESH agar tidak menimpa modul lain
                    $fresh = $this->findDataUGD($rjNo) ?: [];

                    // Sudah ada? Idempoten + info siapa yang mengunci
                    if (!empty($fresh['AdministrasiRj'])) {
                        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                            ->addError("Administrasi sudah tersimpan oleh {$fresh['AdministrasiRj']['userLog']}.");
                        // sinkronkan state lokal
                        $this->dataDaftarUgd = $fresh;
                        return;
                    }

                    // Set tanda selesai administrasi
                    $fresh['AdministrasiRj'] = [
                        'userLog'     => auth()->user()->myuser_name ?? '-',
                        'userLogCode' => auth()->user()->myuser_code ?? null,
                        'userLogDate' => Carbon::now(config('app.timezone'))->format('d/m/Y H:i:s'),
                    ];

                    // Simpan atomik
                    $this->updateJsonUGD($rjNo, $fresh);

                    // Sinkronkan state lokal untuk UI
                    $this->dataDaftarUgd = $fresh;

                    toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                        ->addSuccess("Administrasi berhasil disimpan.");

                    // Refresh ringkasan/totals
                    // Livewire v2
                    $this->emit('ugd:refresh-summary');
                });
            });
        } catch (LockTimeoutException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Sistem sibuk, gagal memperoleh lock. Coba lagi.');
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Gagal menyimpan Administrasi.');
        }
    }

    private function findData($rjNo): array
    {
        $findDataUGD = $this->findDataUGD($rjNo);
        $dataUGD  = $findDataUGD;



        $rsAdmin = DB::table('rstxn_ugdhdrs')
            ->select('rs_admin', 'rj_admin', 'poli_price', 'klaim_id', 'pass_status')
            ->where('rj_no', $rjNo)
            ->first();

        $rsObat = DB::table('rstxn_ugdobats')
            ->join('immst_products', 'immst_products.product_id', 'rstxn_ugdobats.product_id')
            ->select('rstxn_ugdobats.product_id as product_id', 'product_name', 'qty', 'price', 'rjobat_dtl')
            ->where('rj_no', $rjNo)
            ->get();

        $rsLab = DB::table('rstxn_ugdlabs')
            ->select('lab_desc', 'lab_price', 'lab_dtl')
            ->where('rj_no', $rjNo)
            ->get();

        $rsRad = DB::table('rstxn_ugdrads')
            ->join('rsmst_radiologis', 'rsmst_radiologis.rad_id', 'rstxn_ugdrads.rad_id')
            ->select('rad_desc', 'rstxn_ugdrads.rad_price as rad_price', 'rad_dtl')
            ->where('rj_no', $rjNo)
            ->get();

        $rstrfRj = DB::table('rstxn_ugdtempadmins')
            ->select('rj_no', 'tempadm_date', 'rj_admin', 'poli_price', 'acte_price', 'actp_price', 'actd_price', 'obat', 'lab', 'rad', 'other', 'rs_admin')
            ->where('rj_no', $rjNo)
            ->get();



        // RJ Admin
        if ($rsAdmin->pass_status == 'N') {
            $rsAdminParameter = DB::table('rsmst_parameters')
                ->select('par_value')
                ->where('par_id', '1')
                ->first();
            if (isset($dataUGD['rjAdmin'])) {
                $dataUGD['rjAdmin'] = $rsAdmin->rj_admin;
            } else {
                $dataUGD['rjAdmin'] = $rsAdminParameter->par_value;
                // update table trnsaksi
                DB::table('rstxn_ugdhdrs')
                    ->where('rj_no', $rjNo)
                    ->update([
                        'rj_admin' => $dataUGD['rjAdmin'],
                    ]);
            }
        } else {
            $dataUGD['rjAdmin'] = 0;
        }

        // RS Admin
        $rsAdminDokter = DB::table('rsmst_doctors')
            ->select('rs_admin', 'ugd_price', 'ugd_price_bpjs')
            ->where('dr_id', $dataUGD['drId'])
            ->first();


        if (isset($dataUGD['rsAdmin'])) {
            $dataUGD['rsAdmin'] = $rsAdmin->rs_admin ? $rsAdmin->rs_admin : 0;
        } else {
            $dataUGD['rsAdmin'] = $rsAdminDokter->rs_admin ? $rsAdminDokter->rs_admin : 0;
            // update table trnsaksi
            DB::table('rstxn_ugdhdrs')
                ->where('rj_no', $rjNo)
                ->update([
                    'rs_admin' => $dataUGD['rsAdmin'],
                ]);
        }

        // PoliPrice
        // 1) Ambil klaim status (default 'UMUM' jika NULL)
        $klaimStatus = DB::table('rsmst_klaimtypes')
            ->where('klaim_id', $dataUGD['klaimId'] ?? '')
            ->value('klaim_status') ?? 'UMUM';

        // 2) Tentukan harga admin & dokter berdasarkan status klaim
        if ($klaimStatus === 'BPJS') {
            $dokterUgdPrice = $rsAdminDokter->ugd_price_bpjs ?? 0;
        } else {
            $dokterUgdPrice = $rsAdminDokter->ugd_price ?? 0;
        }

        // 3) Set poliPrice & simpan ke transaksi UGD
        if (isset($dataUGD['poliPrice'])) {
            // sudah ada → pakai harga admin/front-office
            $dataUGD['poliPrice'] = $rsAdmin->poli_price ? $rsAdmin->poli_price : 0;
        } else {
            // belum ada → pakai harga dokter, lalu update ke tabel UGD
            $dataUGD['poliPrice'] = $dokterUgdPrice;

            DB::table('rstxn_ugdhdrs')
                ->where('rj_no', $rjNo)
                ->update([
                    'poli_price' => $dataUGD['poliPrice'],
                ]);
        }


        // Ketika Kronis
        if ($rsAdmin->klaim_id == 'KR') {
            $dataUGD['rjAdmin'] = 0;
            $dataUGD['rsAdmin'] = 0;
            $dataUGD['poliPrice'] = 0;
            // update table trnsaksi
            DB::table('rstxn_ugdhdrs')
                ->where('rj_no', $rjNo)
                ->update([
                    'rj_admin' => $dataUGD['rjAdmin'],
                    'rs_admin' => $dataUGD['rsAdmin'],
                    'poli_price' => $dataUGD['poliPrice'],
                ]);
        }

        $dataUGD['rjObat'] = json_decode(json_encode($rsObat, true), true);
        $dataUGD['rjLab'] = json_decode(json_encode($rsLab, true), true);
        $dataUGD['rjRad'] = json_decode(json_encode($rsRad, true), true);
        $dataUGD['rjtrfRj'] = json_decode(json_encode($rstrfRj, true), true);

        return ($dataUGD);
    }

    // when new form instance
    public function mount()
    {
        $this->sumAll();
    }



    // select data start////////////////
    public function render()
    {

        return view(
            'livewire.emr-u-g-d.administrasi-u-g-d.administrasi-u-g-d',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Administrasi Unit Gawat Darurat',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Pasien Unit Gawat Darurat',
            ]
        );
    }
    // select data end////////////////


}
