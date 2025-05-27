<?php

namespace App\Http\Livewire\EmrRJ\PostInacbgRJ;

use App\Http\Traits\EmrRJ\EmrRJTrait;
use App\Http\Traits\MasterPasien\MasterPasienTrait;
use App\Http\Traits\INACBG\InacbgTrait;

use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;



use Livewire\Component;

class PostInacbgRJ extends Component
{
    use EmrRJTrait,
        MasterPasienTrait,
        InacbgTrait;


    public $rjNoRef;

    protected $listeners = [];

    // private function findData($rjno): void
    // {
    //     // Ambil data daftar kunjungan (fallback ke array kosong)
    //     $dataDaftarPoliRJ = $this->findDataRJ($rjno)['dataDaftarRJ'] ?? [];

    //     // Ambil array UUID, atau empty array
    //     $uuids = $dataDaftarPoliRJ['satuSehatUuidRJ'] ?? [];
    //     $this->EncounterID = $uuids['encounter']['uuid'] ?? '';
    // }




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

            // 8. Simpan hasil ke dataDaftarPoliRJ & database
            $dataDaftarPoliRJ['inacbg']['nomor_sep'] =  $resp['data']['nomor_sep'] ?? $nomorSEP;
            $this->updateJsonRJ($this->rjNoRef, $dataDaftarPoliRJ);

            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addSuccess("Klaim INA-CBG terkirim (SEP: {$dataDaftarPoliRJ['inacbg']['nomor_sep']}).");
        } catch (\Exception $e) {
            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError("Gagal kirim klaim INA-CBG: " . $e->getMessage());
        }
    }


    public function setClaimDataToInaCbg()
    {
        // 1. Ambil data kunjungan & pasien
        $find = $this->findDataRJ($this->rjNoRef);
        $dataDaftarPoliRJ = $find['dataDaftarRJ'] ?? [];

        // 2. Cek: apakah set_claim_data sudah pernah dijalankan
        if (! empty($dataDaftarPoliRJ['inacbg']['set_claim_data_done'] ?? false)) {
            toastr()->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addInfo("Detail klaim INA-CBG sudah pernah dikirim untuk SEP: {$dataDaftarPoliRJ['inacbg']['nomor_sep']}.");
            return;
        }

        dd(json_encode($dataDaftarPoliRJ, JSON_PRETTY_PRINT));

        // 3. Ekstrak SEP dan waktu masuk/pulang
        $nomorSEP  = $dataDaftarPoliRJ['inacbg']['nomor_sep'] ?? null;
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
        $diagnosa = [
            [
                'code' => $dataDaftarPoliRJ['sep']['reqSep']['request']['t_sep']['diagAwal'],
                'system' => 'ICD-10'
            ]
        ];
        $prosedur = []; // Isi jika ada data prosedur


        // 5. Tarif, payor, coder
        $tarif     = $dataDaftarPoliRJ['klaimTarif'] ?? 0;
        $payor     = $dataDaftarPoliRJ['sep']['reqSep']['request']['t_sep']['jnsPelayanan'] ?? 'JKN';
        $coderNik  = auth()->user()->nip ?? '';

        // 6. Panggil wrapper di trait
        try {
            $resp = $this->sendClaimDataToInaCbg(
                $nomorSEP,
                $tglMasuk,
                $tglPulang,
                $diagnosa,
                $prosedur,
                (float) $tarif,
                $payor,
                $coderNik
            );

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
