<?php

namespace App\Http\Livewire\EmrRI\PostInacbgRI;

use App\Http\Traits\EmrRI\EmrRITrait;
use App\Http\Traits\MasterPasien\MasterPasienTrait;
use App\Http\Traits\INACBG\InacbgTrait;

use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
// use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


use Livewire\Component;

class PostInacbgRI extends Component
{
    use EmrRITrait,
        MasterPasienTrait,
        InacbgTrait;


    public $riHdrNoRef;
    public $groupingCount;



    public function sendNewClaimToInaCbg()
    {
        // 1. Ambil data kunjungan & pasien
        $dataDaftarRi = $this->findDataRI($this->riHdrNoRef) ?? [];

        $dataPasien = $this->findDataMasterPasien($dataDaftarRi['regNo'] ?? '')['pasien'] ?? [];

        // 2. Cek: apakah klaim sudah pernah dikirim?
        // if (! empty($dataDaftarRi['inacbg']['nomor_sep'])) {
        //     toastr()
        //         ->closeOnHover(true)
        //         ->closeDuration(3)
        //         ->positionClass('toast-top-left')
        //         ->addInfo("Klaim INA-CBG sudah pernah dikirim (SEP: {$dataDaftarRi['inacbg']['nomor_sep']}).");
        //     // return;
        // }

        // 3. Ekstrak data peserta & demografi
        $nomorSEP   = $dataDaftarRi['sep']['noSep'] ?? null;
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
        $genderDesc = strtolower($dataPasien['jenisKelamin']['jenisKelaminDesc'] ?? '');
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
            'nomor_rm' => $dataDaftarRi['regNo'] ?? null,
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
                $dataDaftarRi['inacbg']['klaimId'] = $newId;
                $this->updateJsonRI($this->riHdrNoRef, $dataDaftarRi);
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
                $dataDaftarRi['inacbg']['nomor_sep'] = $nomorSEP;
                $this->updateJsonRI($this->riHdrNoRef, $dataDaftarRi);

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
        $dataDaftarRi = $this->findDataRI($this->riHdrNoRef) ?? [];

        $drName =
            collect($dataDaftarRi['pengkajianAwalPasienRawatInap']['levelingDokter'] ?? [])
                ->firstWhere('levelDokter', 'Utama')['drName'] ?? '';

        // 2. Cek: apakah set_claim_data dikirim ulang
        if (!empty($dataDaftarRi['inacbg']['set_claim_data'] ?? false)) {
            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addInfo("Detail klaim INA-CBG dikirim ulang untuk SEP: {$dataDaftarRi['sep']['noSep']}.");
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
        $nomorSEP   = $dataDaftarRi['sep']['noSep'] ?? null;
        try {
            // 1) Tanggal Masuk
            $tglMasuk = empty($dataDaftarRi['entryDate'])
                ? Carbon::now()->format('Y-m-d H:i:s')
                : Carbon::createFromFormat('d/m/Y H:i:s', $dataDaftarRi['entryDate'])
                ->format('Y-m-d H:i:s');

            // 2) Tanggal Pulang
            $tglPulang = empty($dataDaftarRi['exitDate'])
                ? Carbon::now()->format('Y-m-d H:i:s')
                : Carbon::createFromFormat('d/m/Y H:i:s', $dataDaftarRi['exitDate'])
                ->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')->addError("Format waktu tidak valid: " . $e->getMessage());
            return;
        }


        // 4. Susun diagnosa & procedure sesuai format
        $diagnosaString = collect($dataDaftarRi['diagnosis'] ?? [])
            ->sortBy(fn($item) => match ($item['kategoriDiagnosa'] ?? null) {
                'Primary'   => 1,
                'Secondary' => 2,
                default => 3,
            })
            ->pluck('icdX')
            ->filter()
            ->implode('#')
            ?: '#';


        $procedureString = collect($dataDaftarRi['procedure'] ?? [])
            ->pluck('procedureId')
            ->filter()
            ->implode('#')
            ?: '#';

        $jnsPelayanan = $dataDaftarRi['sep']['reqSep']['request']['t_sep']['jnsPelayanan'] ?? '2';
        $klsRawatHak = $dataDaftarRi['sep']['reqSep']['request']['t_sep']['klsRawat']['klsRawatHak'] ?? '3';

        $statusPulang =  $dataDaftarRi['perencanaan']['tindakLanjut']['statusPulang'] ?? 0;
        // pastikan dalam 1..5
        $statusPulang = in_array($statusPulang, [1, 2, 3, 4, 5])
            ? $statusPulang
            : 5; // fallback ke “Lain-lain”


        $coderNik  = '123123123123';
        // 6. Panggil wrapper di trait
        try {
            $metadata = [
                'method' => 'set_claim_data',
                'nomor_sep' => $nomorSEP, // identifier klaim
            ];

            $totalJasaDokter = DB::table('rstxn_riactdocs')
                ->where('rihdr_no', $this->riHdrNoRef)
                ->selectRaw('SUM(NVL(actd_price, 0) * NVL(actd_qty, 0)) as total')
                ->value('total');


            $totalJasaMedis = DB::table('rstxn_riactparams')
                ->where('rihdr_no', $this->riHdrNoRef)
                ->selectRaw('SUM(NVL(actp_price, 0) * NVL(actp_qty, 0)) as total')
                ->value('total');

            $totalKonsul = DB::table('rstxn_rikonsuls')
                ->where('rihdr_no', $this->riHdrNoRef)
                ->selectRaw('SUM(NVL(konsul_price, 0)) as total')
                ->value('total');

            $totalVisit = DB::table('rstxn_rivisits')
                ->where('rihdr_no', $this->riHdrNoRef)
                ->selectRaw('SUM(NVL(visit_price, 0)) as total')
                ->value('total');

            // Administrasi
            $rsAdmin = DB::table('rstxn_rihdrs')
                ->select('admin_status', 'admin_age')
                ->where('rihdr_no', $this->riHdrNoRef)
                ->first();
            $adminAge = $rsAdmin->admin_age ?? 0;
            $adminStatus = $rsAdmin->admin_status ?? 0;

            $totalBonResep = DB::table('rstxn_ribonobats')
                ->where('rihdr_no', $this->riHdrNoRef)
                ->selectRaw('SUM(NVL(ribon_price, 0)) as total')
                ->value('total');

            $totalObatPinjam = DB::table('rstxn_riobats')
                ->where('rihdr_no', $this->riHdrNoRef)
                ->selectRaw('SUM(NVL(riobat_qty, 0) * NVL(riobat_price, 0)) as total')
                ->value('total');

            $totalReturnObat = DB::table('rstxn_riobatrtns')
                ->where('rihdr_no', $this->riHdrNoRef)
                ->selectRaw('SUM(NVL(riobat_qty, 0) * NVL(riobat_price, 0)) as total')
                ->value('total');

            $totalRadiologi = DB::table('rstxn_riradiologs')
                ->where('rihdr_no', $this->riHdrNoRef)
                ->selectRaw('SUM(NVL(rirad_price, 0)) as total')
                ->value('total');

            $totalLaboratorium = DB::table('rstxn_rilabs')
                ->where('rihdr_no', $this->riHdrNoRef)
                ->selectRaw('SUM(NVL(lab_price, 0)) as total')
                ->value('total');

            $totalOperasi = DB::table('rstxn_rioks')
                ->where('rihdr_no', $this->riHdrNoRef)
                ->selectRaw('SUM(NVL(ok_price, 0)) as total')
                ->value('total');

            $totalOther = DB::table('rstxn_riothers')
                ->where('rihdr_no', $this->riHdrNoRef)
                ->selectRaw('SUM(NVL(other_price, 0)) as total')
                ->value('total');

            $totalRjTemp = DB::table('rstxn_ritempadmins')
                ->where('rihdr_no', $this->riHdrNoRef)
                ->selectRaw("
                                SUM(
                                    NVL(rj_admin, 0) +
                                    NVL(poli_price, 0) +
                                    NVL(acte_price, 0) +
                                    NVL(actp_price, 0) +
                                    NVL(actd_price, 0) +
                                    NVL(obat, 0) +
                                    NVL(lab, 0) +
                                    NVL(rad, 0) +
                                    NVL(other, 0) +
                                    NVL(rs_admin, 0)
                                ) as total
                            ")
                ->value('total');

            $roomSummary = DB::table('rsmst_trfrooms')
                ->where('rihdr_no', $this->riHdrNoRef)
                ->selectRaw("
                    SUM(NVL(room_price, 0) * ROUND(NVL(day, NVL(end_date, sysdate+1) - NVL(start_date, sysdate)))) as total_room_price,
                    SUM(NVL(perawatan_price, 0) * ROUND(NVL(day, NVL(end_date, sysdate+1) - NVL(start_date, sysdate)))) as total_perawatan_price,
                    SUM(NVL(common_service, 0) * ROUND(NVL(day, NVL(end_date, sysdate+1) - NVL(start_date, sysdate)))) as total_common_service
                ")
                ->first();


            $roomPriceTotal      = $roomSummary->total_room_price ?? 0;
            $perawatanPriceTotal = $roomSummary->total_perawatan_price ?? 0;
            $commonServiceTotal  = $roomSummary->total_common_service ?? 0;

            // 9) Obat Kronis
            //— jika di view belum ada kategori khusus, bisa 0 atau ambil logika terpisah
            // $obatKronis = 0;
            $tarifRs = [
                // 0. Siapkan variabel dulu — sesuaikan logika hitung Anda
                'prosedur_non_bedah' => '0',        // baru
                'prosedur_bedah'     => (string) ($totalOperasi ?? 0),
                'konsultasi'         => (string) ($totalKonsul ?? 0),
                'tenaga_ahli'        => (string) ((float) ($totalVisit ?? 0) + (float) ($totalJasaDokter ?? 0)),
                'keperawatan'        => (string) ((float) ($totalJasaMedis ?? 0) + (float) ($perawatanPriceTotal ?? 0)),
                'penunjang'          => (string) ((float) ($adminAge ?? 0) + (float) ($adminStatus ?? 0) + (float) ($commonServiceTotal ?? 0)),
                'radiologi'          => (string) ($totalRadiologi ?? 0),
                'laboratorium'       => (string) ($totalLaboratorium ?? 0),
                'pelayanan_darah'    => '0',
                'rehabilitasi'       => '0',
                'kamar'              => (string) ($roomPriceTotal ?? 0),
                'rawat_intensif'     => '0',
                'obat'               => (string) ((float) ($totalBonResep ?? 0) + (float) ($totalObatPinjam ?? 0) - (float) ($totalReturnObat ?? 0)),
                'obat_kemoterapi'    => '0',
                'obat_kronis'        => '0',
                'alkes'              => '0',
                'bmhp'               => '0',
                'sewa_alat'          => (string) ((float) ($totalOther ?? 0) + (float) ($totalRjTemp ?? 0)),
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
                'kode_tarif' => 'DS',
                // **baru**: discharge_status
                // 1=Atas persetujuan dokter, 2=Dirujuk, 3=Atas permintaan sendiri,
                // 4=Meninggal, 5=Lain-lain
                'discharge_status'        => $statusPulang,
            ];

            $resp = $this->setClaimData($metadata, $data);
            $metaDataCode = $resp['metadata']['code'] ?? '';
            // dd($resp);

            if ($metaDataCode == '200') {
                // tandai sudah selesai
                // di tempat sebelum kamu set nomor_sep, atau di awal method:


                $dataDaftarRi['inacbg']['set_claim_data'] = true;
                $this->updateJsonRI($this->riHdrNoRef, $dataDaftarRi);

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
        $dataDaftarRi = $this->findDataRI($this->riHdrNoRef) ?? [];

        $nomorSEP = $dataDaftarRi['sep']['noSep'] ?? null;

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
                $dataDaftarRi['inacbg']['stage1'] = $resp['response'] ?? [];
                $this->updateJsonRI($this->riHdrNoRef, $dataDaftarRi);

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
        $dataDaftarRi = $this->findDataRI($this->riHdrNoRef) ?? [];

        $nomorSEP = $dataDaftarRi['sep']['noSep'] ?? null;

        if (!$nomorSEP) {
            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('Nomor SEP belum tersedia. Jalankan Stage 1 terlebih dahulu.');
            return;
        }

        // Ambil opsi special CMG dari hasil stage1
        $options = $dataDaftarRi['inacbg']['stage1']['special_cmg_option'] ?? [];
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
                $dataDaftarRi['inacbg']['stage2'] = $resp['response']['special_cmg'] ?? [];
                $this->updateJsonRI($this->riHdrNoRef, $dataDaftarRi);

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
        $dataDaftarRi = $this->findDataRI($this->riHdrNoRef) ?? [];

        $nomorSEP = $dataDaftarRi['sep']['noSep']  ?? null;
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
                $dataDaftarRi['inacbg']['claim_final'] = true;
                $this->updateJsonRI($this->riHdrNoRef, $dataDaftarRi);

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
        $dataDaftarRi = $this->findDataRI($this->riHdrNoRef) ?? [];

        $drName = $dataDaftarRi['drDesc'] ?? '';
        // 2. Cek: apakah set_claim_data dikirim ulang
        if (!empty($dataDaftarRi['inacbg']['set_claim_data'] ?? false)) {
            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addInfo("Detail klaim INA-CBG dikirim ulang untuk SEP: {$dataDaftarRi['sep']['noSep']}.");
            // return;
        }


        // 3. Ekstrak SEP dan waktu masuk/pulang
        $nomorSEP   = $dataDaftarRi['sep']['noSep'] ?? null;

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


                $dataDaftarRi['inacbg']['set_claim_data'] = true;
                $this->updateJsonRI($this->riHdrNoRef, $dataDaftarRi);

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
        $dataDaftarRi = $this->findDataRI($this->riHdrNoRef) ?? [];

        $nomorSEP = $dataDaftarRi['sep']['noSep']  ?? null;
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
                $dataDaftarRi['inacbg']['claim_edited'] = true;
                $this->updateJsonRI($this->riHdrNoRef, $dataDaftarRi);

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
        $dataDaftarRi = $this->findDataRI($this->riHdrNoRef) ?? [];

        $nomorSEP = $dataDaftarRi['sep']['noSep']  ?? null;
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
                $dataDaftarRi['inacbg']['claim_deleted'] = true;
                $this->updateJsonRI($this->riHdrNoRef, $dataDaftarRi);

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


    /**
     * Cetak klaim via WS claim_print
     */
    public function printClaimToInaCbg()
    {
        $daftar = $this->findDataRI($this->riHdrNoRef) ?? [];
        $sep    = $daftar['sep']['noSep'] ?? null;

        if (! $sep) {
            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('Nomor SEP belum tersedia.');
            return;
        }

        try {
            $printResp = $this->claimPrint(
                ['method' => 'claim_print', 'nomor_sep' => $sep],
                ['nomor_sep' => $sep]
            );

            if (($printResp['metadata']['code'] ?? '') != '200') {
                toastr()->closeOnHover(true)
                    ->closeDuration(3)
                    ->positionClass('toast-top-left')
                    ->addError($printResp['metadata']['message'] ?? 'Gagal cetak klaim');
                return;
            }
            $base64 = $printResp['data'] ?? null;
            if (! $base64) {
                toastr()->closeOnHover(true)
                    ->closeDuration(3)
                    ->positionClass('toast-top-left')
                    ->addError('Data PDF tidak ditemukan.');
                return;
            }

            $pdfContent = base64_decode($base64);

            $filename = Carbon::now(env('APP_TIMEZONE'))->format('dmYhis');
            $filePath = 'bpjs/' . $filename . '.pdf'; // Adjust the path as needed


            $cekFile = DB::table('rstxn_riuploadbpjses')
                ->where('rihdr_no', $this->riHdrNoRef)
                ->where('seq_file', 2)
                ->first();

            if ($cekFile) {
                Storage::disk('local')->delete('bpjs/' . $cekFile->uploadbpjs);
                Storage::disk('local')->put($filePath, $pdfContent);
                if (Storage::disk('local')->exists($filePath)) {
                    DB::table('rstxn_riuploadbpjses')
                        ->where('rihdr_no', $this->riHdrNoRef)
                        ->where('uploadbpjs', $cekFile->uploadbpjs)
                        ->where('seq_file', 2)
                        ->update([
                            'uploadbpjs' => $filename . '.pdf',
                            'rihdr_no' => $this->riHdrNoRef,
                            'jenis_file' => 'pdf'
                        ]);
                    toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Data berhasil diupdate " . $cekFile->uploadbpjs);
                } else {
                    toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Data tidak berhasil diupdate " . $cekFile->uploadbpjs);
                }
            } else {
                Storage::disk('local')->put($filePath, $pdfContent);
                if (Storage::disk('local')->exists($filePath)) {
                    DB::table('rstxn_riuploadbpjses')
                        ->insert([
                            'seq_file' => 2,
                            'uploadbpjs' => $filename . '.pdf',
                            'rihdr_no' => $this->riHdrNoRef,
                            'jenis_file' => 'pdf'
                        ]);
                    toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Data berhasil diupload " . $filename . '.pdf');
                } else {
                    toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Data tidak berhasil diupdate " . $filename . '.pdf');
                }
            }




















            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addSuccess('Klaim berhasil dicetak via WS.');
        } catch (\Exception $e) {
            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('Error saat cetak klaim: ' . $e->getMessage());
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
        $find = $this->findDataRI($this->riHdrNoRef);
        $dataDaftarRi = $find ?? [];
        $nomorSEP   = $dataDaftarRi['sep']['noSep'] ?? null;
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
                $dataDaftarRi['inacbg']['set_claim_data_done'] = $resp['response']['data'] ?? [];
                $this->updateJsonRI($this->riHdrNoRef, $dataDaftarRi);

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
        return view('livewire.emr-r-i.post-inacbg-r-i.post-inacbg-r-i');
    }
}
