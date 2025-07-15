<?php

namespace App\Http\Livewire\EmrRJ\AdministrasiRJ;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

use Spatie\ArrayToXml\ArrayToXml;
use App\Http\Traits\EmrRJ\EmrRJTrait;


class AdministrasiRJ extends Component
{
    use WithPagination, EmrRJTrait;

    protected $listeners = [
        'syncronizeAssessmentDokterRJFindData' => 'sumAll',
        'syncronizeAssessmentPerawatRJFindData' => 'sumAll'
    ];

    // dataDaftarPoliRJ RJ
    public $rjNoRef;

    public $statusResep = [
        'status'     => 'DITUNGGU',    // 'DITUNGGU' atau 'DITINGGAL'
        'keterangan' => '',      // catatan pasien
    ];

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

        $this->sumLaboratorium = collect($sumAdmin['rjLab'])->sum((function ($obat) {
            return $obat['lab_price'];
        }));

        $this->sumRadiologi = collect($sumAdmin['rjRad'])->sum((function ($obat) {
            return $obat['rad_price'];
        }));


        $this->sumLainLain = isset($sumAdmin['LainLain']) ? collect($sumAdmin['LainLain'])->sum('LainLainPrice') : 0;


        $this->sumTotalRJ = $this->sumPoliPrice + $this->sumRjAdmin + $this->sumRsAdmin  + $this->sumJasaKaryawan + $this->sumJasaDokter + $this->sumJasaMedis + $this->sumLainLain + $this->sumObat + $this->sumLaboratorium + $this->sumRadiologi;
    }


    public function setSelesaiAdministrasiStatus($rjNo)
    {

        $dataDaftarPoliRJ = $this->findData($rjNo);

        if (isset($dataDaftarPoliRJ['AdministrasiRj']) == false) {
            $dataDaftarPoliRJ['AdministrasiRj'] = [
                'userLog' => auth()->user()->myuser_name,
                'userLogDate' => Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s')
            ];

            // if ($rjNo !== $dataRawatJalan['rjNo']) {
            //     dd('Data Json Tidak sesuai' . $rjNo . '  /  ' . $dataDaftarPoliRJ['rjNo']);
            // }

            // DB::table('rstxn_rjhdrs')
            //     ->where('rj_no', $rjNo)
            //     ->update([
            //         'dataDaftarPoliRJ_json' => json_encode($dataDaftarPoliRJ, true),
            //         'dataDaftarPoliRJ_xml' => ArrayToXml::convert($dataDaftarPoliRJ),
            //     ]);
            $this->updateJsonRJ($rjNo, $dataDaftarPoliRJ);


            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Administrasi berhasil disimpan.");
            $this->emit('syncronizeAssessmentDokterRJFindData');
            $this->emit('syncronizeAssessmentPerawatRJFindData');
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Administrasi sudah tersimpan oleh." . $dataDaftarPoliRJ['AdministrasiRj']['userLog']);
        }
    }



    public function setObatDitungguAtauDitinggal(string $rjNo, string $status, string $keterangan)
    {

        $allowed = ['DITUNGGU', 'DITINGGAL'];
        if (! in_array($status, $allowed)) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError("Status tidak valid. Pilih antara DITUNGGU atau DITINGGAL.");
            return;
        }

        // 1. Ambil data JSON existing
        $dataDaftarPoliRJ = $this->findData($rjNo);
        // 2. Cek apakah statusResep sudah pernah diset
        // 3a. Buat struktur statusResep baru
        $dataDaftarPoliRJ['statusResep'] = [
            'status'      => $status,
            'keterangan'  => $keterangan,
            'userLog'     => auth()->user()->myuser_name,
            'userLogDate' => Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s'),
        ];

        // 3b. Simpan kembali ke DB (JSON + XML bila perlu)
        $this->updateJsonRJ($rjNo, $dataDaftarPoliRJ);

        // 3c. Notifikasi sukses
        toastr()
            ->closeOnHover(true)
            ->closeDuration(3)
            ->positionClass('toast-top-left')
            ->addSuccess("Status resep “{$status}” berhasil disimpan.");

        // 3d. (Opsional) emit event jika perlu refresh di UI lain
        $this->emit('syncronizeAssessmentDokterRJFindData');
        $this->emit('syncronizeAssessmentPerawatRJFindData');
    }


    /**
     * Dipanggil otomatis saat statusResep.status berubah.
     */
    public function updatedStatusResepStatus($value)
    {
        $this->autoSaveStatusResep();
    }

    /**
     * Dipanggil otomatis saat statusResep.keterangan berubah.
     */
    public function updatedStatusResepKeterangan($value)
    {
        $this->autoSaveStatusResep();
    }

    /**
     * Helper untuk validasi & simpan statusResep tanpa tombol.
     */
    protected function autoSaveStatusResep()
    {
        // Panggil method penyimpanan
        $this->setObatDitungguAtauDitinggal(
            $this->rjNoRef,
            $this->statusResep['status'],
            $this->statusResep['keterangan']
        );
    }



    private function findData($rjNo): array
    {
        $findDataRJ = $this->findDataRJ($rjNo);
        $dataRawatJalan  = $findDataRJ['dataDaftarRJ'];



        $rsAdmin = DB::table('rstxn_rjhdrs')
            ->select('rs_admin', 'rj_admin', 'poli_price', 'klaim_id', 'pass_status')
            ->where('rj_no', $rjNo)
            ->first();

        $rsObat = DB::table('rstxn_rjobats')
            ->join('immst_products', 'immst_products.product_id', 'rstxn_rjobats.product_id')
            ->select('rstxn_rjobats.product_id as product_id', 'product_name', 'qty', 'price', 'rjobat_dtl')
            ->where('rj_no', $rjNo)
            ->get();

        $rsLab = DB::table('rstxn_rjlabs')
            ->select('lab_desc', 'lab_price', 'lab_dtl')
            ->where('rj_no', $rjNo)
            ->get();

        $rsRad = DB::table('rstxn_rjrads')
            ->join('rsmst_radiologis', 'rsmst_radiologis.rad_id', 'rstxn_rjrads.rad_id')
            ->select('rad_desc', 'rstxn_rjrads.rad_price as rad_price', 'rad_dtl')
            ->where('rj_no', $rjNo)
            ->get();



        // RJ Admin
        if ($rsAdmin->pass_status == 'N') {
            $rsAdminParameter = DB::table('rsmst_parameters')
                ->select('par_value')
                ->where('par_id', '1')
                ->first();
            if (isset($dataRawatJalan['rjAdmin'])) {
                $dataRawatJalan['rjAdmin'] = $rsAdmin->rj_admin;
            } else {
                $dataRawatJalan['rjAdmin'] = $rsAdminParameter->par_value;
                // update table trnsaksi
                DB::table('rstxn_rjhdrs')
                    ->where('rj_no', $rjNo)
                    ->update([
                        'rj_admin' => $dataRawatJalan['rjAdmin'],
                    ]);
            }
        } else {
            $dataRawatJalan['rjAdmin'] = 0;
        }

        // RS Admin
        $rsAdminDokter = DB::table('rsmst_doctors')
            ->select('rs_admin', 'poli_price', 'poli_price_bpjs')
            ->where('dr_id', $dataRawatJalan['drId'])
            ->first();


        if (isset($dataRawatJalan['rsAdmin'])) {
            $dataRawatJalan['rsAdmin'] = $rsAdmin->rs_admin ? $rsAdmin->rs_admin : 0;
        } else {
            $dataRawatJalan['rsAdmin'] = $rsAdminDokter->rs_admin ? $rsAdminDokter->rs_admin : 0;
            // update table trnsaksi
            DB::table('rstxn_rjhdrs')
                ->where('rj_no', $rjNo)
                ->update([
                    'rs_admin' => $dataRawatJalan['rsAdmin'],
                ]);
        }

        // PoliPrice
        $klaimStatus = DB::table('rsmst_klaimtypes')
            ->where('klaim_id', $dataRawatJalan['klaimId'] ?? '')
            ->value('klaim_status') ?? 'UMUM';

        // 2) Tentukan kolom harga yang akan dipakai
        if ($klaimStatus === 'BPJS') {
            // Jika klaim BPJS, pakai harga BPJS
            $dokterPoliPrice = $rsAdminDokter->poli_price_bpjs ?? 0;
        } else {
            // Jika klaim UMUM, pakai harga umum
            $dokterPoliPrice = $rsAdminDokter->poli_price ?? 0;
        }

        // 3) Set dan simpan ke transaksi
        if (isset($dataRawatJalan['poliPrice'])) {
            // Harga dari admin (misal: registrasi, front office, dst)
            $dataRawatJalan['poliPrice'] =  $rsAdmin->poli_price ? $rsAdmin->poli_price : 0;
        } else {
            // Harga dari dokter
            $dataRawatJalan['poliPrice'] = $dokterPoliPrice;

            // Simpan ke tabel transaksi
            DB::table('rstxn_rjhdrs')
                ->where('rj_no', $rjNo)
                ->update([
                    'poli_price' => $dataRawatJalan['poliPrice'],
                ]);
        }

        // Ketika Kronis
        if ($rsAdmin->klaim_id == 'KR') {
            $dataRawatJalan['rjAdmin'] = 0;
            $dataRawatJalan['rsAdmin'] = 0;
            $dataRawatJalan['poliPrice'] = 0;
            // update table trnsaksi
            DB::table('rstxn_rjhdrs')
                ->where('rj_no', $rjNo)
                ->update([
                    'rj_admin' => $dataRawatJalan['rjAdmin'],
                    'rs_admin' => $dataRawatJalan['rsAdmin'],
                    'poli_price' => $dataRawatJalan['poliPrice'],
                ]);
        }

        $dataRawatJalan['rjObat'] = json_decode(json_encode($rsObat, true), true);
        $dataRawatJalan['rjLab'] = json_decode(json_encode($rsLab, true), true);
        $dataRawatJalan['rjRad'] = json_decode(json_encode($rsRad, true), true);


        // ─────────────── Inisialisasi statusResep ───────────────
        // Jika JSON sudah punya statusResep, pakai itu;
        // kalau belum, buat default, lalu set properti Livewire.
        if (isset($dataRawatJalan['statusResep'])) {
            $this->statusResep = $dataRawatJalan['statusResep'];
        } else {
            $dataRawatJalan['statusResep'] = [
                'status'     => null,
                'keterangan' => '',
            ];
            $this->statusResep = $dataRawatJalan['statusResep'];
        }

        return ($dataRawatJalan);
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
            'livewire.emr-r-j.administrasi-r-j.administrasi-r-j',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Administrasi RJ',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Pasien Rawat Jalan',
            ]
        );
    }
    // select data end////////////////


}
