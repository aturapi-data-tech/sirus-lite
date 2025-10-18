<?php

namespace App\Http\Livewire\EmrRJ\PostInacbgRJ;

use App\Http\Traits\EmrRJ\EmrRJTrait;
use App\Http\Traits\MasterPasien\MasterPasienTrait;
use App\Http\Traits\IDRG\IdrgTrait;

use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class PostInacbgRJ extends Component
{
    use EmrRJTrait, MasterPasienTrait, IdrgTrait;

    public $rjNoRef;
    public $groupingCount;

    public function sendNewClaimToInaCbg()
    {
        // 1) Ambil data RJ & pasien
        $dataRJ = $this->findDataRJ($this->rjNoRef);
        $dataDaftarPoliRJ = $dataRJ['dataDaftarRJ'] ?? [];
        $dataPasien = $this->findDataMasterPasien($dataDaftarPoliRJ['regNo'] ?? '')['pasien'] ?? [];

        // 2) Ekstrak data peserta
        $nomorSEP   = $dataDaftarPoliRJ['sep']['resSep']['noSep'] ?? null;
        $nomorKartu = $dataPasien['identitas']['idbpjs'] ?? null;
        $rawBirth   = $dataPasien['tglLahir'] ?? null;
        $coderNik   = '123123123123';

        try {
            $tglLahir = Carbon::createFromFormat('d/m/Y', $rawBirth)->format('Y-m-d 00:00:00');
        } catch (\Exception $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Format tanggal lahir tidak valid.');
            return;
        }

        $genderDesc = strtolower($dataDaftarPoliRJ['dataPasienRJ']['jenisKelamin']['jenisKelaminDesc'] ?? '');
        $gender = in_array($genderDesc, ['l', 'laki', 'laki-laki'], true) ? '1' : '2';

        // 3) Validasi wajib
        $validator = Validator::make(compact('nomorSEP', 'nomorKartu', 'tglLahir', 'gender'), [
            'nomorSEP'   => 'required',
            'nomorKartu' => 'required',
            'tglLahir'   => 'required',
            'gender'     => 'required|in:1,2',
        ], [
            'nomorSEP.required'   => 'No. SEP belum tersedia.',
            'nomorKartu.required' => 'No. kartu peserta belum tersedia.',
            'tglLahir.required'   => 'Tanggal lahir tidak valid.',
            'gender.required'     => 'Jenis kelamin tidak valid.',
        ]);

        if ($validator->fails()) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError($validator->errors()->first());
            return;
        }

        // 4) Payload
        $metadata = [];
        $data = [
            'nomor_sep'   => $nomorSEP,
            'nomor_kartu' => $nomorKartu,
            'nomor_rm'    => $dataDaftarPoliRJ['regNo'] ?? null,
            'nama_pasien' => $dataPasien['regName'] ?? null,
            'tgl_lahir'   => $tglLahir,
            'gender'      => $gender,
            'coder_nik'   => $coderNik,
        ];

        // 5) Kirim new_claim + tangani duplikasi
        try {
            $resp = $this->newClaim($metadata, $data);
            $statusCode = $resp['metadata']['code'] ?? null;
            $message    = $resp['metadata']['message'] ?? '';

            if ($statusCode == 400) {
                // Duplikasi → generate nomor klaim baru lalu retry
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                    ->addInfo("Duplikasi klaim, generate nomor klaim baru… (SEP: {$nomorSEP})");

                $gen = $this->generateClaimNumber(
                    ['method' => 'generate_claim_number', 'nomor_sep' => $nomorSEP],
                    []
                );
                $newCode = (string)($gen['metadata']['code'] ?? '');
                $newId   = ($gen['response']['claim_number'] ?? null);

                if ($newCode !== '200' || !$newId) {
                    toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                        ->addError("Gagal generate klaim baru: " . ($gen['metadata']['message'] ?? ''));
                    return;
                }

                // simpan klaimId lokal & retry new_claim
                $dataDaftarPoliRJ['inacbg']['klaimId'] = $newId;
                $this->updateJsonRJ($this->rjNoRef, $dataDaftarPoliRJ);

                $resp = $this->newClaim(
                    ['method' => 'new_claim', 'nomor_sep' => $nomorSEP, 'nomor_klaim' => $newId],
                    array_merge($data, ['nomor_klaim' => $newId])
                );

                $statusCode = $resp['metadata']['code'] ?? null;
                $message    = $resp['metadata']['message'] ?? '';
            }

            if ($statusCode == 200) {
                $dataDaftarPoliRJ['inacbg']['nomor_sep'] = $nomorSEP;
                $this->updateJsonRJ($this->rjNoRef, $dataDaftarPoliRJ);

                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                    ->addSuccess("Klaim INA-CBG berhasil terkirim (SEP: {$nomorSEP}).");
                return;
            }

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError("Gagal kirim klaim: {$message}");
        } catch (\Exception $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError("Error saat kirim klaim: " . $e->getMessage());
        }
    }

    public function setClaimDataToInaCbg()
    {
        // 1) RJ & dokter
        $dataRJ = $this->findDataRJ($this->rjNoRef);
        $dataDaftarPoliRJ = $dataRJ['dataDaftarRJ'] ?? [];
        $drName = $dataDaftarPoliRJ['drDesc'] ?? '';

        if (empty($drName)) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addInfo("Nama Dokter belum tersedia.");
            return;
        }

        // 2) SEP & waktu masuk/pulang
        $nomorSEP = $dataDaftarPoliRJ['sep']['resSep']['noSep'] ?? null;
        try {
            $tglMasuk = Carbon::createFromFormat('d/m/Y H:i:s', $dataDaftarPoliRJ['taskIdPelayanan']['taskId3'])
                ->format('Y-m-d H:i:s');
            $tglPulang = Carbon::createFromFormat('d/m/Y H:i:s', $dataDaftarPoliRJ['taskIdPelayanan']['taskId5'])
                ->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError("Format waktu tidak valid: " . $e->getMessage());
            return;
        }

        // 3) Diagnosa & Procedure (untuk iDRG juga)
        $diagnosaString = collect($dataDaftarPoliRJ['diagnosis'] ?? [])
            ->sortBy(fn($item) => match ($item['kategoriDiagnosa'] ?? null) {
                'Primary' => 1,
                'Secondary' => 2,
                default => 3,
            })
            ->pluck('icdX')->filter()->implode('#') ?: '#';

        $procedureString = collect($dataDaftarPoliRJ['procedure'] ?? [])
            ->pluck('procedureId')->filter()->implode('#') ?: '#';

        $jnsPelayanan = $dataDaftarPoliRJ['sep']['reqSep']['request']['t_sep']['jnsPelayanan'] ?? '2'; // RJ
        $klsRawatHak  = $dataDaftarPoliRJ['sep']['reqSep']['request']['t_sep']['klsRawat']['klsRawatHak'] ?? '3';
        $coderNik     = '123123123123';

        try {
            $metadata = ['method' => 'set_claim_data', 'nomor_sep' => $nomorSEP];

            // 4) Ambil tarif via single query (efisien)
            $rows = DB::table('rsview_rjstrs')
                ->select('txn_id', DB::raw('SUM(txn_nominal) as total'))
                ->where('rj_no', $this->rjNoRef)
                ->whereIn('txn_id', [
                    'ADMIN UP',
                    'JASA KARYAWAN',
                    'JASA DOKTER',
                    'JASA MEDIS',
                    'ADMIN RAWAT JALAN',
                    'LAIN-LAIN',
                    'RADIOLOGI',
                    'LABORAT',
                    'OBAT'
                ])
                ->groupBy('txn_id')
                ->pluck('total', 'txn_id');

            $up       = (float)($rows['ADMIN UP'] ?? 0);
            $jk       = (float)($rows['JASA KARYAWAN'] ?? 0);
            $jd       = (float)($rows['JASA DOKTER'] ?? 0);
            $jm       = (float)($rows['JASA MEDIS'] ?? 0);
            $rsAdmin  = (float)($rows['ADMIN RAWAT JALAN'] ?? 0);
            $lain     = (float)($rows['LAIN-LAIN'] ?? 0);
            $radiologi = (float)($rows['RADIOLOGI'] ?? 0);
            $laborat  = (float)($rows['LABORAT'] ?? 0);
            $obat     = (float)($rows['OBAT'] ?? 0);

            $tarifRs = [
                'tenaga_ahli'  => $jk + $up,
                'keperawatan'  => $jm + $jd,
                'penunjang'    => $rsAdmin + $lain,
                'radiologi'    => $radiologi,
                'laboratorium' => $laborat,
                'obat'         => $obat,
            ];

            $data = [
                'nomor_sep'             => $nomorSEP,
                'tgl_masuk'             => $tglMasuk,
                'tgl_pulang'            => $tglPulang,
                'jenis_rawat'           => $jnsPelayanan,
                'kelas_rawat'           => $klsRawatHak,
                'nama_dokter'           => $drName,
                'tarif_rs'              => $tarifRs,
                'diagnosa'              => $diagnosaString,
                'diagnosa_inagrouper'   => $diagnosaString,  // iDRG
                'procedure'             => $procedureString,
                'procedure_inagrouper'  => $procedureString, // iDRG
                'coder_nik'             => $coderNik,
                'payor_id'              => '00003',
                'payor_cd'              => 'JKN',
                'cob_cd'                => '0',
                'add_payment_pct'       => 0,
                'kode_tarif'            => 'DS'
            ];

            $resp = $this->setClaimData($metadata, $data);
            $code = $resp['metadata']['code'] ?? '';

            if ($code === '200') {
                $dataDaftarPoliRJ['inacbg']['set_claim_data'] = true;
                $this->updateJsonRJ($this->rjNoRef, $dataDaftarPoliRJ);

                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                    ->addSuccess('Detail klaim berhasil dikirim ke INA-CBG.');
            } else {
                throw new \Exception($resp['metadata']['message'] ?? 'Unknown error');
            }
        } catch (\Exception $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError("Gagal kirim detail klaim: " . $e->getMessage());
        }
    }

    public function groupingStage1ToInaCbg()
    {
        $dataRJ = $this->findDataRJ($this->rjNoRef);
        $dataDaftarPoliRJ = $dataRJ['dataDaftarRJ'] ?? [];
        $nomorSEP = $dataDaftarPoliRJ['sep']['resSep']['noSep'] ?? null;

        if (!$nomorSEP) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Nomor SEP belum tersedia. Jalankan setClaimData terlebih dahulu.');
            return;
        }

        try {
            $metadata = ['nomor_sep' => $nomorSEP];
            $data     = ['nomor_sep' => $nomorSEP];

            $resp = $this->grouperStage1($metadata, $data);
            $code = $resp['metadata']['code'] ?? '';

            if ($code === '200') {
                $response = $resp['response'] ?? [];
                $dataDaftarPoliRJ['inacbg']['stage1'] = $response;

                // Ringkas iDRG & CBG untuk dipakai UI/logika
                $idrg = $response['response_inagrouper'] ?? [];
                $dataDaftarPoliRJ['inacbg']['idrg'] = [
                    'mdc_number'      => $idrg['mdc_number'] ?? null,
                    'mdc_description' => $idrg['mdc_description'] ?? null,
                    'drg_code'        => $idrg['drg_code'] ?? null,
                    'drg_description' => $idrg['drg_description'] ?? null,
                    'severity'        => $idrg['severity'] ?? null,
                ];
                $cbg = $response['cbg'] ?? [];
                $dataDaftarPoliRJ['inacbg']['cbg'] = [
                    'code'        => $cbg['code'] ?? null,
                    'description' => $cbg['description'] ?? null,
                    'base_tariff' => $cbg['base_tariff'] ?? null,
                ];

                $this->updateJsonRJ($this->rjNoRef, $dataDaftarPoliRJ);

                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                    ->addSuccess('Grouping INA-CBG Tahap 1 berhasil.');

                return $response;
            }

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError($resp['metadata']['message'] ?? 'Error pada Stage 1');
        } catch (\Exception $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Gagal Grouping Stage 1: ' . $e->getMessage());
        }
    }

    public function groupingStage2ToInaCbg()
    {
        $dataRJ = $this->findDataRJ($this->rjNoRef);
        $dataDaftarPoliRJ = $dataRJ['dataDaftarRJ'] ?? [];
        $nomorSEP = $dataDaftarPoliRJ['sep']['resSep']['noSep'] ?? null;

        if (!$nomorSEP) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Nomor SEP belum tersedia. Jalankan Stage 1 terlebih dahulu.');
            return;
        }

        // Ambil opsi special CMG dari stage1
        $options = $dataDaftarPoliRJ['inacbg']['stage1']['special_cmg_option'] ?? [];
        $specialCmg = collect($options)->pluck('code')->filter()->implode('#');

        try {
            $metadata = ['nomor_sep' => $nomorSEP, 'stage' => 2];
            $data     = ['nomor_sep' => $nomorSEP];
            if ($specialCmg !== '') {
                $data['special_cmg'] = $specialCmg; // kirim hanya jika ada
            }

            $resp = $this->grouperStage2($metadata, $data);
            $code = $resp['metadata']['code'] ?? '';

            if ($code === '200') {
                $dataDaftarPoliRJ['inacbg']['stage2'] = $resp['response']['special_cmg'] ?? [];
                // (Opsional) sinkron ulang CBG/iDRG final jika service menaruhnya di response stage2.
                $this->updateJsonRJ($this->rjNoRef, $dataDaftarPoliRJ);

                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                    ->addSuccess('Grouping INA-CBG Tahap 2 berhasil.');
                return;
            }

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError($resp['metadata']['message'] ?? 'Error pada Stage 2');
        } catch (\Exception $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Gagal Grouping Stage 2: ' . $e->getMessage());
        }
    }

    public function finalizeClaimToInaCbg()
    {
        $dataRJ = $this->findDataRJ($this->rjNoRef);
        $dataDaftarPoliRJ = $dataRJ['dataDaftarRJ'] ?? [];
        $nomorSEP = $dataDaftarPoliRJ['sep']['resSep']['noSep'] ?? null;
        $coderNik = '123123123123';

        if (!$nomorSEP) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Nomor SEP belum tersedia. Jalankan proses sebelumnya.');
            return;
        }

        try {
            $metadata = ['nomor_sep' => $nomorSEP];
            $data     = ['nomor_sep' => $nomorSEP, 'coder_nik' => $coderNik];

            $resp = $this->claimFinal($metadata, $data);
            $code = $resp['metadata']['code'] ?? '';

            if ($code === '200') {
                $dataDaftarPoliRJ['inacbg']['claim_final'] = true;
                $this->updateJsonRJ($this->rjNoRef, $dataDaftarPoliRJ);

                // Ambil detail final (sinkron CBG/iDRG/metadata)
                $this->getClaimDataToInaCbg();

                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                    ->addSuccess('Klaim INA-CBG berhasil difinalisasi.');
                return;
            }

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError($resp['metadata']['message'] ?? 'Error pada claim_final');
        } catch (\Exception $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Gagal finalisasi klaim: ' . $e->getMessage());
        }
    }

    public function deleteDiagnosisAndProcedureDataToInaCbg()
    {
        $dataRJ = $this->findDataRJ($this->rjNoRef);
        $dataDaftarPoliRJ = $dataRJ['dataDaftarRJ'] ?? [];
        $nomorSEP = $dataDaftarPoliRJ['sep']['resSep']['noSep'] ?? null;
        $coderNik = '123123123123';

        if (!$nomorSEP) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Nomor SEP belum tersedia.');
            return;
        }

        try {
            $metadata = ['method' => 'set_claim_data', 'nomor_sep' => $nomorSEP];
            $data = [
                'nama_dokter'          => '',
                'diagnosa'             => '#',
                'diagnosa_inagrouper'  => '#',
                'procedure'            => '#',
                'procedure_inagrouper' => '#',
                'coder_nik'            => $coderNik,
            ];

            $resp = $this->setClaimData($metadata, $data);
            $code = $resp['metadata']['code'] ?? '';

            if ($code === '200') {
                $dataDaftarPoliRJ['inacbg']['set_claim_data'] = true;
                $this->updateJsonRJ($this->rjNoRef, $dataDaftarPoliRJ);

                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                    ->addSuccess('Diagnosa & Prosedur klaim berhasil di-reset.');
            } else {
                throw new \Exception($resp['metadata']['message'] ?? 'Unknown error');
            }
        } catch (\Exception $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError("Gagal kirim detail klaim: " . $e->getMessage());
        }
    }

    public function editClaimToInaCbg()
    {
        $dataRJ = $this->findDataRJ($this->rjNoRef);
        $dataDaftarPoliRJ = $dataRJ['dataDaftarRJ'] ?? [];
        $nomorSEP = $dataDaftarPoliRJ['sep']['resSep']['noSep'] ?? null;
        $coderNik = '123123123123';

        if (!$nomorSEP) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Nomor SEP belum tersedia. Jalankan proses sebelumnya.');
            return;
        }

        try {
            $metadata = ['nomor_sep' => $nomorSEP];
            $data     = ['nomor_sep' => $nomorSEP, 'coder_nik' => $coderNik];

            $resp = $this->reeditClaim($metadata, $data);
            $code = $resp['metadata']['code'] ?? null;
            $message = $resp['metadata']['message'] ?? 'Unknown error';

            if ($code === '200') {
                $dataDaftarPoliRJ['inacbg']['claim_edited'] = true;
                $this->updateJsonRJ($this->rjNoRef, $dataDaftarPoliRJ);

                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                    ->addSuccess('Edit klaim INA-CBG berhasil.');
                return;
            }

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError("Gagal edit klaim: {$message}");
        } catch (\Exception $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Error saat edit klaim: ' . $e->getMessage());
        }
    }

    public function deleteClaimToInaCbg()
    {
        $dataRJ = $this->findDataRJ($this->rjNoRef);
        $dataDaftarPoliRJ = $dataRJ['dataDaftarRJ'] ?? [];
        $nomorSEP = $dataDaftarPoliRJ['sep']['resSep']['noSep'] ?? null;
        $coderNik = '123123123123';

        if (!$nomorSEP) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Nomor SEP belum tersedia. Jalankan proses sebelumnya.');
            return;
        }

        try {
            $metadata = ['nomor_sep' => $nomorSEP];
            $data     = ['nomor_sep' => $nomorSEP, 'coder_nik' => $coderNik];

            $resp = $this->deleteClaim($metadata, $data);
            $code = $resp['metadata']['code'] ?? null;
            $message = $resp['metadata']['message'] ?? 'Unknown error';

            if ($code === '200') {
                $dataDaftarPoliRJ['inacbg']['claim_deleted'] = true;
                $this->updateJsonRJ($this->rjNoRef, $dataDaftarPoliRJ);

                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                    ->addSuccess('Delete klaim INA-CBG berhasil.');
                return;
            }

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError("Gagal delete klaim: {$message}");
        } catch (\Exception $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Error saat menghapus klaim: ' . $e->getMessage());
        }
    }

    /**
     * Cetak klaim via WS claim_print
     */
    public function printClaimToInaCbg()
    {
        $dataRJ = $this->findDataRJ($this->rjNoRef);
        $daftar = $dataRJ['dataDaftarRJ'] ?? [];
        $sep    = $daftar['sep']['resSep']['noSep'] ?? null;

        if (!$sep) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Nomor SEP belum tersedia.');
            return;
        }

        try {
            $printResp = $this->claimPrint(
                ['method' => 'claim_print', 'nomor_sep' => $sep],
                ['nomor_sep' => $sep]
            );

            if (($printResp['metadata']['code'] ?? '') != '200') {
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                    ->addError($printResp['metadata']['message'] ?? 'Gagal cetak klaim');
                return;
            }

            $base64 = $printResp['data'] ?? null;
            if (!$base64) {
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                    ->addError('Data PDF tidak ditemukan.');
                return;
            }

            $pdfContent = base64_decode($base64);

            // Pastikan folder ada, timezone pakai config
            Storage::disk('local')->makeDirectory('bpjs');
            $filename = Carbon::now(config('app.timezone'))->format('dmYhis');
            $filePath = 'bpjs/' . $filename . '.pdf';

            $cekFile = DB::table('rstxn_rjuploadbpjses')
                ->where('rj_no', $this->rjNoRef)
                ->where('seq_file', 2)
                ->first();

            if ($cekFile) {
                Storage::disk('local')->delete('bpjs/' . $cekFile->uploadbpjs);
                Storage::disk('local')->put($filePath, $pdfContent);

                if (Storage::disk('local')->exists($filePath)) {
                    DB::table('rstxn_rjuploadbpjses')
                        ->where('rj_no', $this->rjNoRef)
                        ->where('uploadbpjs', $cekFile->uploadbpjs)
                        ->where('seq_file', 2)
                        ->update([
                            'uploadbpjs' => $filename . '.pdf',
                            'rj_no'      => $this->rjNoRef,
                            'jenis_file' => 'pdf'
                        ]);

                    toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                        ->addSuccess("Data berhasil diupdate " . $cekFile->uploadbpjs);
                } else {
                    toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                        ->addError("Data tidak berhasil diupdate " . $cekFile->uploadbpjs);
                }
            } else {
                Storage::disk('local')->put($filePath, $pdfContent);

                if (Storage::disk('local')->exists($filePath)) {
                    DB::table('rstxn_rjuploadbpjses')->insert([
                        'seq_file'   => 2,
                        'uploadbpjs' => $filename . '.pdf',
                        'rj_no'      => $this->rjNoRef,
                        'jenis_file' => 'pdf'
                    ]);

                    toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                        ->addSuccess("Data berhasil diupload " . $filename . '.pdf');
                } else {
                    toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                        ->addError("Data tidak berhasil diupdate " . $filename . '.pdf');
                }
            }

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addSuccess('Klaim berhasil dicetak via WS.');
        } catch (\Exception $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
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
        $find = $this->findDataRJ($this->rjNoRef);
        $dataDaftarPoliRJ = $find['dataDaftarRJ'] ?? [];
        $nomorSEP = $dataDaftarPoliRJ['sep']['resSep']['noSep'] ?? null;

        if (empty($nomorSEP)) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addInfo("Data klaim INA-CBG tidak ditemukan.");
            return;
        }

        try {
            $metadata = [];
            $data     = ['nomor_sep' => $nomorSEP];
            $resp     = $this->getClaimData($metadata, $data);

            if (($resp['metadata']['code'] ?? '') == '200') {
                $dataDaftarPoliRJ['inacbg']['set_claim_data_done'] = $resp['response']['data'] ?? [];
                $this->updateJsonRJ($this->rjNoRef, $dataDaftarPoliRJ);

                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                    ->addSuccess('Detail klaim berhasil diambil dari INA-CBG.');
            } else {
                throw new \Exception($resp['metadata']['message'] ?? 'Unknown error');
            }
        } catch (\Exception $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError("Gagal ambil detail klaim: " . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.emr-r-j.post-inacbg-r-j.post-inacbg-r-j');
    }
}
