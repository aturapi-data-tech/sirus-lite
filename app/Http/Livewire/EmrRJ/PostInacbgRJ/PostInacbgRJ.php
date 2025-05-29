<?php

namespace App\Http\Livewire\EmrRJ\PostInacbgRJ;

use App\Http\Traits\EmrRJ\EmrRJTrait;
use App\Http\Traits\MasterPasien\MasterPasienTrait;
use App\Http\Traits\INACBG\InacbgTrait;

use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;


use Livewire\Component;

class PostInacbgRJ extends Component
{
    use EmrRJTrait,
        MasterPasienTrait,
        InacbgTrait;


    public $rjNoRef;



    public function sendNewClaimToInaCbg()
    {
        // 1. Validasi minimal
        // $this->validate();

        // 2. Ambil data kunjungan & pasien
        $find = $this->findDataRJ($this->rjNoRef);
        $dataDaftarPoliRJ = $find['dataDaftarRJ'] ?? [];
        $dataPasienRJ  = $find['dataPasienRJ'] ?? [];
        $dataPasien = $this->findDataMasterPasien($dataDaftarPoliRJ['regNo'] ?? '') ?? [];

        // 2. Cek: apakah klaim sudah pernah dikirim?
        if (!empty($dataDaftarPoliRJ['inacbg']['nomor_sep'] ?? null)) {
            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addInfo("Klaim INA-CBG sudah pernah dikirim (SEP: {$dataDaftarPoliRJ['inacbg']['nomor_sep']}).");
            return;
        }

        // 3. Ekstrak data peserta
        $nomorKartu = $dataDaftarPoliRJ['sep']['reqSep']['request']['t_sep']['noKartu'] ?? null;
        $nomorSEP   = $dataDaftarPoliRJ['sep']['resSep']['noSep'] ?? null;
        $nomorRM    = $dataDaftarPoliRJ['regNo'] ?? null;
        $namaPasien = $dataPasienRJ['regName']   ?? null;


        // 4. Format tanggal lahir jadi “YYYY-MM-DD HH:MM:SS”
        $rawDob = $dataPasien['pasien']['tglLahir'] ?? null;            // ex: “01/12/1966”
        try {
            $tglLahir = Carbon::createFromFormat('d/m/Y', $rawDob)
                ->format('Y-m-d 00:00:00');
        } catch (\Exception $e) {
            $tglLahir = null;
        }
        // 5. Gender: 1=Laki, 2=Perempuan
        $sex = strtolower($dataPasienRJ['jenisKelamin']['jenisKelaminDesc'] ?? '');
        $gender = in_array($sex, ['l', 'laki', 'laki-laki']) ? '1' : '2';



        // 6. Validasi input wajib
        $validator = Validator::make(compact(
            'nomorKartu',
            'nomorSEP',
            'nomorRM',
            'namaPasien',
            'tglLahir',
            'gender'
        ), [
            'nomorKartu' => 'required',
            'nomorSEP'   => 'required',
            'nomorRM'    => 'required',
            'namaPasien' => 'required',
            'tglLahir'   => 'required',
            'gender'     => 'required|in:1,2',
        ], [
            'nomorKartu.required' => 'No. kartu peserta belum tersedia.',
            'nomorSEP.required'   => 'No. SEP belum tersedia.',
            'nomorRM.required'    => 'No. RM belum tersedia.',
            'namaPasien.required' => 'Nama pasien belum tersedia.',
            'tglLahir.required'   => 'Tanggal lahir tidak valid.',
            'gender.required'     => 'Jenis kelamin tidak valid.',
        ]);

        if ($validator->fails()) {
            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError($validator->errors()->first());
            return;
        }

        // Siapkan metadata (opsional — bisa kosong jika tidak ada override)
        $metadata = [];

        //  Siapkan payload data sesuai dokumentasi INA-CBG
        $data = [
            'nomor_kartu' => $nomorKartu,   // ex: '0002271584439'
            'nomor_sep'   => $nomorSEP,     // ex: '0184R0060125V002743'
            'nomor_rm'    => $nomorRM,      // ex: '080310Z'
            'nama_pasien' => $namaPasien,   // ex: 'KARJI'
            'tgl_lahir'   => $tglLahir,     // 'YYYY-MM-DD HH:MM:SS'
            'gender'      => $gender,       // '1' atau '2'
        ];
        // dd($data);
        // 7. Kirim new_claim ke INA-CBG
        try {
            $resp = $this->newClaim($metadata, $data);

            // 8. Periksa duplikasi dari response
            // Misal: INA-CBG mengembalikan kode HTTP 400, atau metadata.code == '400', atau pesan berisi 'Duplikasi'
            $statusCode = $resp['metadata']['code'] ?? null;
            $message    = $resp['metadata']['message'] ?? '';

            if ($statusCode == 400 || Str::contains(strtolower($message), 'Duplikasi')) {
                toastr()->closeOnHover(true)
                    ->closeDuration(3)
                    ->positionClass('toast-top-left')
                    ->addInfo("Klaim INA-CBG sudah pernah dikirim (SEP: {$nomorSEP}).");
                // return;
            }

            // 9. Jika benar-benar sukses
            $dataDaftarPoliRJ['inacbg']['nomor_sep'] = $nomorSEP;
            $this->updateJsonRJ($this->rjNoRef, $dataDaftarPoliRJ);

            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addSuccess("Klaim INA-CBG berhasil terkirim (SEP: {$nomorSEP}).");
        } catch (\Exception $e) {
            // 10. Tangani error sewaktu request: bisa duplikasi juga
            $errMsg = $e->getMessage();

            if (Str::contains(strtolower($errMsg), 'Duplikasi') || Str::contains($errMsg, '400')) {
                toastr()->closeOnHover(true)
                    ->closeDuration(3)
                    ->positionClass('toast-top-left')
                    ->addInfo("Klaim INA-CBG sudah pernah dikirim (SEP: {$nomorSEP}).");
            } else {
                toastr()->closeOnHover(true)
                    ->closeDuration(3)
                    ->positionClass('toast-top-left')
                    ->addError("Gagal kirim klaim INA-CBG: " . $errMsg);
            }
        }
    }


    public function setClaimDataToInaCbg()
    {
        // 1. Ambil data kunjungan & pasien
        $find = $this->findDataRJ($this->rjNoRef);
        $dataDaftarPoliRJ = $find['dataDaftarRJ'] ?? [];

        // 2. Cek: apakah set_claim_data sudah pernah dijalankan
        if (!empty($dataDaftarPoliRJ['inacbg']['setClaimDataDone'] ?? false)) {
            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addInfo("Detail klaim INA-CBG sudah pernah dikirim untuk SEP: {$dataDaftarPoliRJ['inacbg']['nomor_sep']}.");
            return;
        }


        // 3. Ekstrak SEP dan waktu masuk/pulang
        $nomorSEP   = $dataDaftarPoliRJ['sep']['resSep']['noSep'] ?? null;
        try {
            $tglMasuk  = Carbon::createFromFormat(
                'd/m/Y H:i:s',
                $dataDaftarPoliRJ['taskIdPelayanan']['taskId3']
            )->format('Y-m-d H:i:s');
            $tglPulang = Carbon::createFromFormat(
                'd/m/Y H:i:s',
                $dataDaftarPoliRJ['taskIdPelayanan']['taskId5']
            )->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            toastr()->addError("Format waktu tidak valid: " . $e->getMessage());
            return;
        }

        // 4. Susun diagnosa & prosedur sesuai format
        // 1. Siapkan $diagnosa dari array diagnosis
        $diagnosa = [];
        if (!empty($dataDaftarPoliRJ['diagnosis'])) {
            foreach ($dataDaftarPoliRJ['diagnosis'] as $diag) {
                $diagnosa[] = [
                    'code'   => $diag['icdX'] ?? null,
                    'system' => 'ICD-10',
                    'display' => $diag['diagDesc'] ?? null,      // opsional, kalau butuh deskripsi
                ];
            }
        }

        // 2. Siapkan $prosedur dari array procedure
        $prosedur = [];
        if (!empty($dataDaftarPoliRJ['procedure'])) {
            foreach ($dataDaftarPoliRJ['procedure'] as $proc) {
                $prosedur[] = [
                    'code'   => $proc['procedureId'] ?? null,
                    'system' => 'ICD-9-CM',                       // sesuaikan sistem kode prosedur-mu
                    'display' => $proc['procedureDesc'] ?? null,   // opsional
                ];
            }
        }

        $jnsPelayanan     = $dataDaftarPoliRJ['sep']['reqSep']['request']['t_sep']['jnsPelayanan'] ?? '2';
        $klsRawatHak     = $dataDaftarPoliRJ['sep']['reqSep']['request']['t_sep']['klsRawat']['klsRawatHak'] ?? '3';


        $coderNik  = '123123123123';
        // 6. Panggil wrapper di trait
        try {
            $metadata = [
                'method' => 'set_claim_data',
                'nomor_sep' => $nomorSEP,      // identifier klaim
            ];

            // ambil dulu tarif masing‐masing dari variabel
            // 1) Uang Periksa Poli (ADMIN UP)
            $up = DB::table('rsview_rjstrs')
                ->where('rj_no', $this->rjNoRef)
                ->where('txn_id', 'ADMIN UP')
                ->sum('txn_nominal');

            // 2) Jasa Karyawan (JASA KARYAWAN)
            $jk = DB::table('rsview_rjstrs')
                ->where('rj_no', $this->rjNoRef)
                ->where('txn_id', 'JASA KARYAWAN')
                ->sum('txn_nominal');

            // 3) Jasa Dokter (JASA DOKTER)
            $jd = DB::table('rsview_rjstrs')
                ->where('rj_no', $this->rjNoRef)
                ->where('txn_id', 'JASA DOKTER')
                ->sum('txn_nominal');

            // 4) Jasa Medis (JASA MEDIS)
            $jm = DB::table('rsview_rjstrs')
                ->where('rj_no', $this->rjNoRef)
                ->where('txn_id', 'JASA MEDIS')
                ->sum('txn_nominal');

            // 5) Admin & Lain-lain (ADMIN RAWAT JALAN + LAIN-LAIN)
            // $rsAdmin + $adminOb
            $rsAdmin = DB::table('rsview_rjstrs')
                ->where('rj_no', $this->rjNoRef)
                ->where('txn_id', 'ADMIN RAWAT JALAN')
                ->sum('txn_nominal');

            $lain = DB::table('rsview_rjstrs')
                ->where('rj_no', $this->rjNoRef)
                ->where('txn_id', 'LAIN-LAIN')
                ->sum('txn_nominal');

            // 6) Radiologi
            $radiologi = DB::table('rsview_rjstrs')
                ->where('rj_no', $this->rjNoRef)
                ->where('txn_id', 'RADIOLOGI')
                ->sum('txn_nominal');

            // 7) Laboratorium
            $laboratorium = DB::table('rsview_rjstrs')
                ->where('rj_no', $this->rjNoRef)
                ->where('txn_id', 'LABORAT')
                ->sum('txn_nominal');

            // 8) Obat
            $obat = DB::table('rsview_rjstrs')
                ->where('rj_no', $this->rjNoRef)
                ->where('txn_id', 'OBAT')
                ->sum('txn_nominal');

            // 9) Obat Kronis
            //    — jika di view belum ada kategori khusus, bisa 0 atau ambil logika terpisah
            // $obatKronis = 0;

            $tarifRs = [
                'tenaga_ahli'        => (float) $jk + $up,
                'keperawatan'        => (float) $jm + $jd,
                'penunjang'          => (float) $rsAdmin + $lain,
                'radiologi'          => (float) $radiologi,
                'laboratorium'       => (float) $laboratorium,
                'obat'               => (float) $obat,
                // 'obat_kronis'        => (float) $obatKronis,
            ];

            $data = [
                'nomor_sep'             => $nomorSEP,      // identifier klaim
                'tgl_masuk'             => $tglMasuk,       // 'YYYY-MM-DD HH:MM:SS'
                'tgl_pulang'            => $tglPulang,      // 'YYYY-MM-DD HH:MM:SS'
                'jenis_rawat'           => $jnsPelayanan,             // 1=inap,2=jalan,3=both
                'kelas_rawat'           => $klsRawatHak,             // kelas tarif faskes

                'tarif_rs'              => $tarifRs,
                // 'tarif_rs'              => (float) $tarif,  // total tarif RS
                'diagnosa'              => $diagnosa,       // array ICD-10
                'prosedur'              => $prosedur,       // array ICD-9CM/PCS
                'coder_nik'             => $coderNik,       // NIK coder (mandatory)
                'payor_id'              => '3',
                'payor_cd'              => 'JKN',
                // opsi:
                'cob_cd'          => '0',
                'add_payment_pct' => 0,
            ];



            $resp = $this->setClaimData($metadata, $data);
            if (($resp['metadata']['code'] ?? '') === '200') {
                // tandai sudah selesai
                $dataDaftarPoliRJ['inacbg']['setClaimDataDone'] = 1;
                dd($dataDaftarPoliRJ);
                $this->updateJsonRJ($this->rjNoRef, $dataDaftarPoliRJ);

                toastr()->closeOnHover(true)
                    ->closeDuration(3)
                    ->positionClass('toast-top-left')
                    ->addSuccess('Detail klaim berhasil dikirim ke INA-CBG.');
            } else {
                throw new \Exception($resp['metadata']['message'] ?? 'Unknown error');
            }
        } catch (\Exception $e) {
            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError("Gagal kirim detail klaim: " . $e->getMessage());
        }
    }


    public function getClaimDataToInaCbg()
    {
        // 1. Ambil data kunjungan & pasien
        $find = $this->findDataRJ($this->rjNoRef);
        $dataDaftarPoliRJ = $find['dataDaftarRJ'] ?? [];
        $nomorSEP   = $dataDaftarPoliRJ['sep']['resSep']['noSep'] ?? null;
        // 2. Cek: apakah set_claim_data sudah pernah dijalankan
        if (empty($nomorSEP)) {
            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addInfo("Data klaim INA-CBG tidak ditemukan.");
            return;
        }

        try {
            // Siapkan metadata (opsional — bisa kosong jika tidak ada override)
            $metadata = [];

            //  Siapkan payload data sesuai dokumentasi INA-CBG
            $data = ['nomor_sep'   => $nomorSEP];
            $resp = $this->getClaimData($metadata, $data);
            dd($resp);


            if (($resp['metadata']['code'] ?? '') === '200') {
                // tandai sudah selesai
                $dataDaftarPoliRJ['inacbg']['set_claim_data_done'] = true;
                $this->updateJsonRJ($this->rjNoRef, $dataDaftarPoliRJ);

                toastr()->closeOnHover(true)
                    ->closeDuration(3)
                    ->positionClass('toast-top-left')
                    ->addSuccess('Detail klaim berhasil dikirim ke INA-CBG.');
            } else {
                throw new \Exception($resp['metadata']['message'] ?? 'Unknown error');
            }
        } catch (\Exception $e) {
            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError("Gagal kirim detail klaim: " . $e->getMessage());
        }
    }


    public function render()
    {
        return view('livewire.emr-r-j.post-inacbg-r-j.post-inacbg-r-j');
    }
}
