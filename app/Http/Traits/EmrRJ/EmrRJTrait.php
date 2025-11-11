<?php

namespace App\Http\Traits\EmrRJ;


use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;
use Spatie\ArrayToXml\ArrayToXml;

trait EmrRJTrait
{

    protected function findDataRJ($rjno): array
    {
        try {
            $findData = DB::table('rsview_rjkasir')
                ->select('datadaftarpolirj_json', 'vno_sep')
                ->where('rj_no', '=', $rjno)
                ->first();

            $datadaftarpolirj_json = isset($findData->datadaftarpolirj_json) ? $findData->datadaftarpolirj_json : null;
            // if meta_data_pasien_json = null
            // then cari Data Pasien By Key Collection (exception when no data found)
            //
            // else json_decode
            if ($datadaftarpolirj_json) {
                $dataDaftarRJ = json_decode($findData->datadaftarpolirj_json, true);
            } else {

                $dataDaftarRJ = DB::table('rsview_rjkasir')
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
                        // 'entry_id',
                        'shift',
                        'vno_sep',
                        'no_antrian',

                        'nobooking',
                        'push_antrian_bpjs_status',
                        'push_antrian_bpjs_json',
                        'kd_dr_bpjs',
                        'kd_poli_bpjs',
                        'rj_status',
                        'txn_status',
                        'erm_status',
                    )
                    ->where('rj_no', '=', $rjno)
                    ->first();

                $dataDaftarRJ = [
                    "regNo" => "" . $dataDaftarRJ->reg_no . "",

                    "drId" => "" . $dataDaftarRJ->dr_id . "",
                    "drDesc" => "" . $dataDaftarRJ->dr_name . "",

                    "poliId" => "" . $dataDaftarRJ->poli_id . "",
                    "poliDesc" => "" . $dataDaftarRJ->poli_desc . "",
                    "klaimId" => "" . $dataDaftarRJ->klaim_id == 'JM' ? 'JM' : 'UM' . "",

                    "kddrbpjs" => "" . $dataDaftarRJ->kd_dr_bpjs . "",
                    "kdpolibpjs" => "" . $dataDaftarRJ->kd_poli_bpjs . "",

                    "rjDate" => "" . $dataDaftarRJ->rj_date . "",
                    "rjNo" => "" . $dataDaftarRJ->rj_no . "",
                    "shift" => "" . $dataDaftarRJ->shift . "",
                    "noAntrian" => "" . $dataDaftarRJ->no_antrian . "",
                    "noBooking" => "" . $dataDaftarRJ->nobooking . "",
                    "slCodeFrom" => "02",
                    "passStatus" => "",
                    "rjStatus" => "" . $dataDaftarRJ->rj_status . "",
                    "txnStatus" => "" . $dataDaftarRJ->txn_status . "",
                    "ermStatus" => "" . $dataDaftarRJ->erm_status . "",
                    "cekLab" => "0",
                    "kunjunganInternalStatus" => "0",
                    "noReferensi" => "" . $dataDaftarRJ->reg_no . "",
                    "postInap" => [],
                    "internal12" => "1",
                    "internal12Desc" => "Faskes Tingkat 1",
                    "internal12Options" => [
                        [
                            "internal12" => "1",
                            "internal12Desc" => "Faskes Tingkat 1"
                        ],
                        [
                            "internal12" => "2",
                            "internal12Desc" => "Faskes Tingkat 2 RS"
                        ]
                    ],
                    "kontrol12" => "1",
                    "kontrol12Desc" => "Faskes Tingkat 1",
                    "kontrol12Options" => [
                        [
                            "kontrol12" => "1",
                            "kontrol12Desc" => "Faskes Tingkat 1"
                        ],
                        [
                            "kontrol12" => "2",
                            "kontrol12Desc" => "Faskes Tingkat 2 RS"
                        ],
                    ],
                    "taskIdPelayanan" => [
                        "taskId1" => "",
                        "taskId2" => "",
                        "taskId3" => "" . $dataDaftarRJ->rj_date . "",
                        "taskId4" => "",
                        "taskId5" => "",
                        "taskId6" => "",
                        "taskId7" => "",
                        "taskId99" => "",
                    ],
                    'sep' => [
                        "noSep" => "" . $dataDaftarRJ->vno_sep . "",
                        "reqSep" => [],
                        "resSep" => [],
                    ]
                ];
            }

            // Update data array terkini untuk RJ
            $updatedDataDaftarRJ = DB::table('rsview_rjkasir')
                ->select(
                    DB::raw("to_char(rj_date, 'dd/mm/yyyy hh24:mi:ss') AS rj_date"),
                    'poli_id',
                    'poli_desc',
                    'dr_id',
                    'dr_name',
                    'klaim_id',
                    // 'klaim_desc',
                    'rj_status',
                    'kd_dr_bpjs',
                    'kd_poli_bpjs',
                    'shift'
                )
                ->where('rj_no', '=', $rjno)
                ->first();

            // Update field-field dataDaftarRJ dengan data terkini
            $dataDaftarRJ['poliId']     = $updatedDataDaftarRJ->poli_id;
            $dataDaftarRJ['poliDesc']   = $updatedDataDaftarRJ->poli_desc;
            $dataDaftarRJ['drId']       = $updatedDataDaftarRJ->dr_id;
            $dataDaftarRJ['drDesc']     = $updatedDataDaftarRJ->dr_name;
            $dataDaftarRJ['klaimId']    = $updatedDataDaftarRJ->klaim_id;
            $dataDaftarRJ['rjDate']     = $updatedDataDaftarRJ->rj_date;
            $dataDaftarRJ['rjStatus']   = $updatedDataDaftarRJ->rj_status;
            $dataDaftarRJ['kddrbpjs']   = $updatedDataDaftarRJ->kd_dr_bpjs;
            $dataDaftarRJ['kdpolibpjs'] = $updatedDataDaftarRJ->kd_poli_bpjs;
            $dataDaftarRJ['shift']      = $updatedDataDaftarRJ->shift;


            // dataPasienRJ
            $dataPasienRJ = DB::table('rsview_rjkasir')
                ->select(

                    'rj_no',
                    'reg_no',
                    'patient_uuid',
                    'poli_uuid',
                    'dr_uuid',
                    'reg_name',
                    'sex',
                    'address',

                    'poli_id',
                    'poli_desc',
                    'dr_id',
                    'dr_name',
                    'nobooking',
                    'kd_dr_bpjs',
                    'kd_poli_bpjs',
                )
                ->where('rj_no', '=', $rjno)
                ->first();

            $dataPasien = DB::table('rsmst_pasiens')
                ->select(
                    DB::raw("to_char(reg_date,'dd/mm/yyyy hh24:mi:ss') as reg_date"),
                    DB::raw("to_char(reg_date,'yyyymmddhh24miss') as reg_date1"),
                    'reg_no',
                    'reg_name',
                    DB::raw("nvl(nokartu_bpjs,'-') as nokartu_bpjs"),
                    DB::raw("nvl(nik_bpjs,'-') as nik_bpjs"),
                    'sex',
                    DB::raw("to_char(birth_date,'dd/mm/yyyy') as birth_date"),
                    DB::raw("(select trunc( months_between( sysdate, birth_date ) /12 ) from dual) as thn"),
                    'bln',
                    'hari',
                    'birth_place',
                    'blood',
                    'marital_status',
                    'rsmst_religions.rel_id as rel_id',
                    'rel_desc',
                    'rsmst_educations.edu_id as edu_id',
                    'edu_desc',
                    'rsmst_jobs.job_id as job_id',
                    'job_name',
                    'kk',
                    'nyonya',
                    'no_kk',
                    'address',
                    'rsmst_desas.des_id as des_id',
                    'des_name',
                    'rt',
                    'rw',
                    'rsmst_kecamatans.kec_id as kec_id',
                    'kec_name',
                    'rsmst_kabupatens.kab_id as kab_id',
                    'kab_name',
                    'rsmst_propinsis.prop_id as prop_id',
                    'prop_name',
                    'phone'
                )->join('rsmst_religions', 'rsmst_religions.rel_id', 'rsmst_pasiens.rel_id')
                ->join('rsmst_educations', 'rsmst_educations.edu_id', 'rsmst_pasiens.edu_id')
                ->join('rsmst_jobs', 'rsmst_jobs.job_id', 'rsmst_pasiens.job_id')
                ->join('rsmst_desas', 'rsmst_desas.des_id', 'rsmst_pasiens.des_id')
                ->join('rsmst_kecamatans', 'rsmst_kecamatans.kec_id', 'rsmst_pasiens.kec_id')
                ->join('rsmst_kabupatens', 'rsmst_kabupatens.kab_id', 'rsmst_pasiens.kab_id')
                ->join('rsmst_propinsis', 'rsmst_propinsis.prop_id', 'rsmst_pasiens.prop_id')
                ->where('reg_no', $dataPasienRJ->reg_no)
                ->first();

            $dataPasienRJ = [
                "regNo" => $dataPasien->reg_no,
                "regName" => $dataPasien->reg_name,
                "patientUuid" => $dataPasienRJ->patient_uuid,
                "drUuid" => $dataPasienRJ->dr_uuid,
                "drName" => $dataPasienRJ->dr_name,
                "poliUuid" => $dataPasienRJ->poli_uuid,
                "poliDesc" => $dataPasienRJ->poli_desc,
                "nik" => $dataPasien->nik_bpjs,
                "address" => $dataPasien->address,
                "desa" => $dataPasien->des_name,
                "kecamatan" => $dataPasien->kec_name,

            ];


            return ([
                "dataDaftarRJ" => $dataDaftarRJ,
                "dataPasienRJ" => $dataPasienRJ
            ]);
        } catch (Exception $e) {

            dd($e->getMessage());
            return [
                "dataDaftarRJ" => [],
                "dataPasienRJ" => [],
                "errorMessages" => $e->getMessage()
            ];
        }
    }

    protected  function checkRJStatus(int $rjNo = 0): bool
    {
        $rjStatus = DB::table('rstxn_rjhdrs')
            ->select('rj_status')
            ->where('rj_no', '=', $rjNo)
            ->first();

        if (!isset($rjStatus->rj_status) || empty($rjStatus->rj_status)) {
            return false;
        }

        if ($rjStatus->rj_status !== 'A') {
            return true;
        }

        return false;
    }

    public static function updateJsonRJ($rjNo, array $rjArr): void
    {
        if (intval($rjNo) !== intval($rjArr['rjNo'])) {
            dd('Data Json Tidak sesuai ' . $rjNo . '  /  ' . $rjArr['rjNo']);
        }

        DB::table('rstxn_rjhdrs')
            ->where('rj_no', $rjNo)
            ->update([
                'datadaftarpolirj_json' => json_encode($rjArr, true),
            ]);
    }
}
