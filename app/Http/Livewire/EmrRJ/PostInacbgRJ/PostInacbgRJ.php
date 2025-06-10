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
        // 1. Ambil data kunjungan & pasien
        $dataDaftarPoliRJ = $this->findDataRJ($this->rjNoRef);
        $dataDaftarPoliRJ = $dataDaftarPoliRJ['dataDaftarRJ'] ?? [];
        $dataPasien = $this->findDataMasterPasien($dataDaftarPoliRJ['regNo'] ?? '')['pasien'] ?? [];

        // 2. Cek: apakah klaim sudah pernah dikirim?
        // if (! empty($dataDaftarPoliRJ['inacbg']['nomor_sep'])) {
        //     toastr()
        //         ->closeOnHover(true)
        //         ->closeDuration(3)
        //         ->positionClass('toast-top-left')
        //         ->addInfo("Klaim INA-CBG sudah pernah dikirim (SEP: {$dataDaftarPoliRJ['inacbg']['nomor_sep']}).");
        //     // return;
        // }

        // 3. Ekstrak data peserta & demografi
        $nomorSEP   = $dataDaftarPoliRJ['sep']['resSep']['noSep'] ?? null;
        $nomorKartu = $dataPasien['identitas']['idbpjs'] ?? null;
        $rawBirthDate     = $dataPasien['tglLahir']   ?? null;
        $coderNik   = '123123123123';

        try {
            $tglLahir = Carbon::createFromFormat('d/m/Y', $rawBirthDate)
                ->format('Y-m-d 00:00:00');
        } catch (\Exception $e) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('Format tanggal lahir tidak valid.');
            return;
        }

        $genderDesc = strtolower($dataDaftarPoliRJ['dataPasienRJ']['jenisKelamin']['jenisKelaminDesc'] ?? '');
        $gender = in_array($genderDesc, ['l', 'laki', 'laki-laki']) ? '1' : '2';

        // 4. Validasi input wajib
        $validator = Validator::make(compact('nomorSEP', 'nomorKartu', 'tglLahir', 'gender'), [
            'nomorSEP'   => 'required',
            'nomorKartu' => 'required',
            'tglLahir'   => 'required',
            'gender' => 'required|in:1,2',
        ], [
            'nomorSEP.required'   => 'No. SEP belum tersedia.',
            'nomorKartu.required' => 'No. kartu peserta belum tersedia.',
            'tglLahir.required'   => 'Tanggal lahir tidak valid.',
            'gender.required' => 'Jenis kelamin tidak valid.',
        ]);

        if ($validator->fails()) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError($validator->errors()->first());
            return;
        }

        // 5. Siapkan payload
        $metadata = [];
        $data = [
            'nomor_sep'   => $nomorSEP,
            'nomor_kartu' => $nomorKartu,
            'nomor_rm' => $dataDaftarPoliRJ['regNo'] ?? null,
            'nama_pasien' => $dataPasien['regName']   ?? null,
            'tgl_lahir'   => $tglLahir,
            'gender'  => $gender,
            'coder_nik'   => $coderNik,
        ];

        // 6. Kirim new_claim dan tangani duplikasi
        try {
            $resp   = $this->newClaim($metadata, $data);
            $statusCode = $resp['metadata']['code'] ?? null;
            $message = $resp['metadata']['message'] ?? '';

            if ($statusCode == 400) {
                // retry dengan klaim baru
                toastr()
                    ->closeOnHover(true)
                    ->closeDuration(3)
                    ->positionClass('toast-top-left')
                    ->addError("Duplikasi klaim, generate nomor klaim baru… (SEP: {$nomorSEP})");
                return;

                $gen = $this->generateClaimNumber(
                    ['method' => 'generate_claim_number', 'nomor_sep' => $nomorSEP],
                    []
                );
                $newCode = (string)($gen['metadata']['code'] ?? '');
                $newId   = ($gen['response']['claim_number'] ?? null);

                if ($newCode != '200' || !$newId) {
                    toastr()
                        ->closeOnHover(true)
                        ->closeDuration(3)
                        ->positionClass('toast-top-left')
                        ->addError("Gagal generate klaim baru: " . ($gen['metadata']['message'] ?? ''));
                    return;
                }
                // simpan klaimId baru & retry
                $dataDaftarPoliRJ['inacbg']['klaimId'] = $newId;
                $this->updateJsonRJ($this->rjNoRef, $dataDaftarPoliRJ);
                $resp   = $this->newClaim(
                    ['method' => 'new_claim', 'nomor_sep' => $nomorSEP, 'nomor_klaim' => $newId],
                    array_merge($data, ['nomor_klaim' => $newId])
                );

                // dd($resp);
                $statusCode = $resp['metadata']['code'] ?? null;
                $message = $resp['metadata']['message'] ?? '';
                // $this->setClaimDataToInaCbg();
                return;
            }

            // 7. Final check
            if ($statusCode == 200) {
                $dataDaftarPoliRJ['inacbg']['nomor_sep'] = $nomorSEP;
                $this->updateJsonRJ($this->rjNoRef, $dataDaftarPoliRJ);

                toastr()
                    ->closeOnHover(true)
                    ->closeDuration(3)
                    ->positionClass('toast-top-left')
                    ->addSuccess("Klaim INA-CBG berhasil terkirim (SEP: {$nomorSEP}).");
                return;
            }

            // 8. Error selain duplikasi
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError("Gagal kirim klaim: {$message}");
        } catch (\Exception $e) {
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError("Error saat kirim klaim: " . $e->getMessage());
        }
    }



    public function setClaimDataToInaCbg()
    {
        // 1. Ambil data kunjungan & pasien
        $dataDaftarPoliRJ = $this->findDataRJ($this->rjNoRef);
        $dataDaftarPoliRJ = $dataDaftarPoliRJ['dataDaftarRJ'] ?? [];
        $drName = $dataDaftarPoliRJ['drDesc'] ?? '';
        // 2. Cek: apakah set_claim_data dikirim ulang
        if (!empty($dataDaftarPoliRJ['inacbg']['set_claim_data'] ?? false)) {
            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addInfo("Detail klaim INA-CBG dikirim ulang untuk SEP: {$dataDaftarPoliRJ['sep']['resSep']['noSep']}.");
            // return;
        }

        if (empty($drName)) {
            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addInfo("Nama Dokter belum tersedia.");
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
            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')->addError("Format waktu tidak valid: " . $e->getMessage());
            return;
        }


        // 4. Susun diagnosa & procedure sesuai format
        $diagnosaString = collect($dataDaftarPoliRJ['diagnosis'] ?? [])
            ->sortBy(fn($item) => match ($item['kategoriDiagnosa'] ?? null) {
                'Primary'   => 1,
                'Secondary' => 2,
                default => 3,
            })
            ->pluck('icdX')
            ->filter()
            ->implode('#')
            ?: '#';


        $procedureString = collect($dataDaftarPoliRJ['procedure'] ?? [])
            ->pluck('procedureId')
            ->filter()
            ->implode('#')
            ?: '#';

        $jnsPelayanan = $dataDaftarPoliRJ['sep']['reqSep']['request']['t_sep']['jnsPelayanan'] ?? '2';
        $klsRawatHak = $dataDaftarPoliRJ['sep']['reqSep']['request']['t_sep']['klsRawat']['klsRawatHak'] ?? '3';


        $coderNik  = '123123123123';
        // 6. Panggil wrapper di trait
        try {
            $metadata = [
                'method' => 'set_claim_data',
                'nomor_sep' => $nomorSEP, // identifier klaim
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
            //— jika di view belum ada kategori khusus, bisa 0 atau ambil logika terpisah
            // $obatKronis = 0;

            $tarifRs = [
                'tenaga_ahli'   => (float) $jk + $up,
                'keperawatan'   => (float) $jm + $jd,
                'penunjang' => (float) $rsAdmin + $lain,
                'radiologi' => (float) $radiologi,
                'laboratorium'  => (float) $laboratorium,
                'obat' => (float) $obat,
                // 'obat_kronis'   => (float) $obatKronis,
            ];

            $data = [
                'nomor_sep'   => $nomorSEP, // identifier klaim
                'tgl_masuk'   => $tglMasuk,  // 'YYYY-MM-DD HH:MM:SS'
                'tgl_pulang'  => $tglPulang, // 'YYYY-MM-DD HH:MM:SS'
                'jenis_rawat' => $jnsPelayanan,   // 1=inap,2=jalan,3=both
                'kelas_rawat' => $klsRawatHak,   // kelas tarif faskes
                'nama_dokter' => $drName,
                'tarif_rs' => $tarifRs,
                // 'tarif_rs'=> (float) $tarif,  // total tarif RS
                'diagnosa' => $diagnosaString,  // array ICD-10
                'diagnosa_inagrouper' => $diagnosaString,
                'procedure' => $procedureString,  // array ICD-9CM/PCS
                'procedure_inagrouper' => $procedureString,
                'coder_nik'   => $coderNik,  // NIK coder (mandatory)
                'payor_id' => '00003',
                'payor_cd' => 'JKN',
                // opsi:
                'cob_cd' => '0',
                'add_payment_pct' => 0,
                'kode_tarif' => 'DS'
            ];


            $resp = $this->setClaimData($metadata, $data);
            $metaDataCode = $resp['metadata']['code'] ?? '';
            // dd($resp);

            if ($metaDataCode == '200') {
                // tandai sudah selesai
                // di tempat sebelum kamu set nomor_sep, atau di awal method:


                $dataDaftarPoliRJ['inacbg']['set_claim_data'] = true;
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

    public function groupingStage1ToInaCbg()
    {
        $dataDaftarPoliRJ = $this->findDataRJ($this->rjNoRef);
        $dataDaftarPoliRJ = $dataDaftarPoliRJ['dataDaftarRJ'] ?? [];
        $nomorSEP = $dataDaftarPoliRJ['sep']['resSep']['noSep'] ?? null;

        if (!$nomorSEP) {
            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('Nomor SEP belum tersedia. Jalankan setClaimData terlebih dahulu.');
            return;
        }

        try {


            $metadata = ['nomor_sep' => $nomorSEP];
            $data  = ['nomor_sep' => $nomorSEP];
            $resp = $this->grouperStage1($metadata, $data);

            $code = $resp['metadata']['code'] ?? '';

            if ($code == '200') {
                $dataDaftarPoliRJ['inacbg']['stage1'] = $resp['response'] ?? [];
                $this->updateJsonRJ($this->rjNoRef, $dataDaftarPoliRJ);

                toastr()->closeOnHover(true)
                    ->closeDuration(3)
                    ->positionClass('toast-top-left')
                    ->addSuccess('Grouping INA-CBG Tahap 1 berhasil.');

                return $resp['response'];
            }

            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError($resp['metadata']['message'] ?? 'Error pada Stage 1');
        } catch (\Exception $e) {
            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('Gagal Grouping Stage 1: ' . $e->getMessage());
        }
    }

    public function groupingStage2ToInaCbg()
    {
        $dataDaftarPoliRJ = $this->findDataRJ($this->rjNoRef);
        $dataDaftarPoliRJ = $dataDaftarPoliRJ['dataDaftarRJ'] ?? [];
        $nomorSEP = $dataDaftarPoliRJ['sep']['resSep']['noSep'] ?? null;

        if (!$nomorSEP) {
            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('Nomor SEP belum tersedia. Jalankan Stage 1 terlebih dahulu.');
            return;
        }

        // Ambil opsi special CMG dari hasil stage1
        $options = $dataDaftarPoliRJ['inacbg']['stage1']['special_cmg_option'] ?? [];
        $specialCmg = collect($options)
            ->pluck('code')
            ->filter()
            ->implode('#');


        try {
            $metadata = ['nomor_sep' => $nomorSEP, 'stage' => 2];
            $data = ['nomor_sep' => $nomorSEP, 'special_cmg' => $specialCmg];
            $resp = $this->grouperStage2($metadata, $data);

            $code = $resp['metadata']['code'] ?? '';

            if ($code == '200') {
                $dataDaftarPoliRJ['inacbg']['stage2'] = $resp['response']['special_cmg'] ?? [];
                $this->updateJsonRJ($this->rjNoRef, $dataDaftarPoliRJ);

                toastr()->closeOnHover(true)
                    ->closeDuration(3)
                    ->positionClass('toast-top-left')
                    ->addSuccess('Grouping INA-CBG Tahap 2 berhasil.');

                return;
            }

            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError($resp['metadata']['message'] ?? 'Error pada Stage 2');
        } catch (\Exception $e) {
            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('Gagal Grouping Stage 2: ' . $e->getMessage());
        }
    }

    public function finalizeClaimToInaCbg()
    {
        $dataDaftarPoliRJ = $this->findDataRJ($this->rjNoRef);
        $dataDaftarPoliRJ = $dataDaftarPoliRJ['dataDaftarRJ'] ?? [];
        $nomorSEP = $dataDaftarPoliRJ['sep']['resSep']['noSep']  ?? null;
        $coderNik  = '123123123123';

        if (!$nomorSEP) {
            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('Nomor SEP belum tersedia. Jalankan proses sebelumnya.');
            return;
        }

        try {
            $metadata = ['nomor_sep' => $nomorSEP];
            $data = [
                'nomor_sep' => $nomorSEP,
                'coder_nik'   => $coderNik,  // NIK coder (mandatory)
            ];
            $resp = $this->claimFinal($metadata, $data);

            $code = $resp['metadata']['code'] ?? '';
            if ($code == '200') {
                $dataDaftarPoliRJ['inacbg']['claim_final'] = true;
                $this->updateJsonRJ($this->rjNoRef, $dataDaftarPoliRJ);

                $this->getClaimDataToInaCbg();

                toastr()->closeOnHover(true)
                    ->closeDuration(3)
                    ->positionClass('toast-top-left')
                    ->addSuccess('Klaim INA-CBG berhasil difinalisasi.');

                return;
            }

            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError($resp['metadata']['message'] ?? 'Error pada claim_final');
        } catch (\Exception $e) {
            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('Gagal finalisasi klaim: ' . $e->getMessage());
        }
    }


    public function deleteDiagnosisAndProcedureDataToInaCbg()
    {
        // 1. Ambil data kunjungan & pasien
        $dataDaftarPoliRJ = $this->findDataRJ($this->rjNoRef);
        $dataDaftarPoliRJ = $dataDaftarPoliRJ['dataDaftarRJ'] ?? [];
        $drName = $dataDaftarPoliRJ['drDesc'] ?? '';
        // 2. Cek: apakah set_claim_data dikirim ulang
        if (!empty($dataDaftarPoliRJ['inacbg']['set_claim_data'] ?? false)) {
            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addInfo("Detail klaim INA-CBG dikirim ulang untuk SEP: {$dataDaftarPoliRJ['sep']['resSep']['noSep']}.");
            // return;
        }


        // 3. Ekstrak SEP dan waktu masuk/pulang
        $nomorSEP   = $dataDaftarPoliRJ['sep']['resSep']['noSep'] ?? null;

        $coderNik  = '123123123123';
        // 6. Panggil wrapper di trait
        try {
            $metadata = [
                'method' => 'set_claim_data',
                'nomor_sep' => $nomorSEP, // identifier klaim
            ];
            $data = [
                'nama_dokter' => '',
                'diagnosa' => '#',  // array ICD-10
                'diagnosa_inagrouper' => '#',
                'procedure' => '#',  // array ICD-9CM/PCS
                'procedure_inagrouper' => '#',
                'coder_nik'   => $coderNik,  // NIK coder (mandatory)
            ];


            $resp = $this->setClaimData($metadata, $data);
            $metaDataCode = $resp['metadata']['code'] ?? '';
            // dd($resp);

            if ($metaDataCode == '200') {
                // tandai sudah selesai
                // di tempat sebelum kamu set nomor_sep, atau di awal method:


                $dataDaftarPoliRJ['inacbg']['set_claim_data'] = true;
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

    public function editClaimToInaCbg()
    {
        $dataDaftarPoliRJ = $this->findDataRJ($this->rjNoRef);
        $dataDaftarPoliRJ = $dataDaftarPoliRJ['dataDaftarRJ'] ?? [];
        $nomorSEP = $dataDaftarPoliRJ['sep']['resSep']['noSep']  ?? null;
        $coderNik  = '123123123123';

        if (!$nomorSEP) {
            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('Nomor SEP belum tersedia. Jalankan proses sebelumnya.');
            return;
        }

        try {
            // 2. Siapkan metadata & data payload
            $metadata = [
                'nomor_sep' => $nomorSEP,
            ];
            $data = [
                'nomor_sep' => $nomorSEP,
                'coder_nik' => $coderNik,
            ];

            // 3. Panggil WS reedit_claim
            $resp = $this->reeditClaim($metadata, $data);

            // 4. Cek kode response
            $code = $resp['metadata']['code'] ?? null;
            $message = $resp['metadata']['message'] ?? 'Unknown error';

            if ($code == '200') {
                // 5. Tandai di JSON lokal
                $dataDaftarPoliRJ['inacbg']['claim_edited'] = true;
                $this->updateJsonRJ($this->rjNoRef, $dataDaftarPoliRJ);

                toastr()
                    ->closeOnHover(true)
                    ->closeDuration(3)
                    ->positionClass('toast-top-left')
                    ->addSuccess('Edit klaim INA-CBG berhasil.');
                return;
            }

            // 6. Gagal dengan kode lain
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError("Gagal edit klaim: {$message}");
        } catch (\Exception $e) {
            // 7. Tangani exception
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('Error saat edit klaim: ' . $e->getMessage());
        }
    }

    public function deleteClaimToInaCbg()
    {
        $dataDaftarPoliRJ = $this->findDataRJ($this->rjNoRef);
        $dataDaftarPoliRJ = $dataDaftarPoliRJ['dataDaftarRJ'] ?? [];
        $nomorSEP = $dataDaftarPoliRJ['sep']['resSep']['noSep']  ?? null;
        $coderNik  = '123123123123';

        if (!$nomorSEP) {
            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('Nomor SEP belum tersedia. Jalankan proses sebelumnya.');
            return;
        }

        try {
            $metadata = ['nomor_sep' => $nomorSEP];
            $data = [
                'nomor_sep' => $nomorSEP,
                'coder_nik'   => $coderNik,  // NIK coder (mandatory)
            ];
            $resp = $this->deleteClaim($metadata, $data);

            // 4. Cek kode response
            $code = $resp['metadata']['code'] ?? null;
            $message = $resp['metadata']['message'] ?? 'Unknown error';

            if ($code == '200') {
                $dataDaftarPoliRJ['inacbg']['claim_deleted'] = true;
                $this->updateJsonRJ($this->rjNoRef, $dataDaftarPoliRJ);

                toastr()
                    ->closeOnHover(true)
                    ->closeDuration(3)
                    ->positionClass('toast-top-left')
                    ->addSuccess('Delete klaim INA-CBG berhasil.');

                return;
            }

            // Jika bukan 200, tampilkan pesan dari server
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError("Gagal delete klaim: {$message}");
        } catch (\Exception $e) {
            // Tangani exception unexpected
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('Error saat menghapus klaim: ' . $e->getMessage());
        }
    }

    public function groupingAllToInaCbg()
    {
        $this->sendNewClaimToInaCbg();
        $this->setClaimDataToInaCbg();
        $this->groupingStage1ToInaCbg();
        $this->groupingStage2ToInaCbg();
        $this->finalizeClaimToInaCbg();
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


            if (($resp['metadata']['code'] ?? '') == '200') {
                // tandai sudah selesai
                $dataDaftarPoliRJ['inacbg']['set_claim_data_done'] = $resp['response']['data'] ?? [];
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
