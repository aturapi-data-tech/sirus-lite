<?php

namespace App\Http\Traits\EmrUGD;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Spatie\ArrayToXml\ArrayToXml;
use Exception;


trait EmrUGDTrait
{

    protected function findDataUGD($rjno): array
    {
        try {
            $findData = DB::table('rsview_ugdkasir')
                ->select('datadaftarugd_json', 'vno_sep')
                ->where('rj_no', '=', $rjno)
                ->first();

            $datadaftarugd_json = isset($findData->datadaftarugd_json) ? $findData->datadaftarugd_json : null;
            // if meta_data_pasien_json = null
            // then cari Data Pasien By Key Collection (exception when no data found)
            //
            // else json_decode
            if ($datadaftarugd_json) {
                $dataDaftarUgd = json_decode($findData->datadaftarugd_json, true);
            } else {

                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Data tidak dapat di proses json.");
                $dataDaftarUgd = DB::table('rsview_ugdkasir')
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
                        // 'poli_desc',
                        'dr_id',
                        'dr_name',
                        'klaim_id',
                        'entry_id',
                        'shift',
                        'vno_sep',
                        'no_antrian',

                        'nobooking',
                        'push_antrian_bpjs_status',
                        'push_antrian_bpjs_json',
                        // 'kd_dr_bpjs',
                        // 'kd_poli_bpjs',
                        'rj_status',
                        'txn_status',
                        'erm_status',
                    )
                    ->where('rj_no', '=', $rjno)
                    ->first();

                $dataDaftarUgd = [
                    "regNo" => "" . $dataDaftarUgd->reg_no . "",

                    "drId" => "" . $dataDaftarUgd->dr_id . "",
                    "drDesc" => "" . $dataDaftarUgd->dr_name . "",

                    "poliId" => "" . $dataDaftarUgd->poli_id . "",
                    "klaimId" => "" . $dataDaftarUgd->klaim_id == 'JM' ? 'JM' : 'UM' . "",
                    // "poliDesc" => "" . $dataDaftarUgd->poli_desc . "",

                    // "kddrbpjs" => "" . $dataDaftarUgd->kd_dr_bpjs . "",
                    // "kdpolibpjs" => "" . $dataDaftarUgd->kd_poli_bpjs . "",

                    "rjDate" => "" . $dataDaftarUgd->rj_date . "",
                    "rjNo" => "" . $dataDaftarUgd->rj_no . "",
                    "shift" => "" . $dataDaftarUgd->shift . "",
                    "noAntrian" => "" . $dataDaftarUgd->no_antrian . "",
                    "noBooking" => "" . $dataDaftarUgd->nobooking . "",
                    "slCodeFrom" => "02",
                    "passStatus" => "",
                    "rjStatus" => "" . $dataDaftarUgd->rj_status . "",
                    "txnStatus" => "" . $dataDaftarUgd->txn_status . "",
                    "ermStatus" => "" . $dataDaftarUgd->erm_status . "",
                    "cekLab" => "0",
                    "kunjunganInternalStatus" => "0",
                    "noReferensi" => "" . $dataDaftarUgd->reg_no . "",
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
                        "taskId3" => "" . $dataDaftarUgd->rj_date . "",
                        "taskId4" => "",
                        "taskId5" => "",
                        "taskId6" => "",
                        "taskId7" => "",
                        "taskId99" => "",
                    ],
                    'sep' => [
                        "noSep" => "" . $dataDaftarUgd->vno_sep . "",
                        "reqSep" => [],
                        "resSep" => [],
                    ]
                ];
            }

            // Update data array terkini untuk RJ
            $updatedDataDaftarUgd = DB::table('rsview_ugdkasir')
                ->select(
                    DB::raw("to_char(rj_date, 'dd/mm/yyyy hh24:mi:ss') AS rj_date"),
                    'poli_id',
                    // 'poli_desc',
                    'dr_id',
                    'dr_name',
                    'klaim_id',
                    // 'klaim_desc',
                    'rj_status',
                    // 'kd_dr_bpjs',
                    // 'kd_poli_bpjs',
                    'shift'
                )
                ->where('rj_no', '=', $rjno)
                ->first();

            // Update field-field dataDaftarUgd dengan data terkini
            $dataDaftarUgd['poliId']     = $updatedDataDaftarUgd->poli_id;
            // $dataDaftarUgd['poliDesc']   = $updatedDataDaftarUgd->poli_desc;
            $dataDaftarUgd['drId']       = $updatedDataDaftarUgd->dr_id;
            $dataDaftarUgd['drDesc']     = $updatedDataDaftarUgd->dr_name;
            $dataDaftarUgd['klaimId']    = $updatedDataDaftarUgd->klaim_id;
            $dataDaftarUgd['rjDate']     = $updatedDataDaftarUgd->rj_date;
            $dataDaftarUgd['rjStatus']   = $updatedDataDaftarUgd->rj_status;
            // $dataDaftarUgd['kddrbpjs']   = $updatedDataDaftarUgd->kd_dr_bpjs;
            // $dataDaftarUgd['kdpolibpjs'] = $updatedDataDaftarUgd->kd_poli_bpjs;
            $dataDaftarUgd['shift']      = $updatedDataDaftarUgd->shift;

            return $dataDaftarUgd;
        } catch (Exception $e) {
            dd($e->getMessage());
            return ["errorMessages" => $e->getMessage()];
        }
    }

    protected function checkUgdStatus($rjNo): bool
    {
        $row = DB::table('rstxn_ugdhdrs')
            ->select('rj_status')
            ->where('rj_no', $rjNo)
            ->first();

        if (!$row || $row->rj_status !== 'A') {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Pasien Sudah Pulang, Transaksi Terkunci.');
            return false;
        }
        return true;
    }

    public static function updateJsonUGD($rjNo, array $rjArr): void
    {
        if (intval($rjNo) !== intval($rjArr['rjNo'])) {
            dd('Data Json Tidak sesuai ' . $rjNo . '  /  ' . $rjArr['rjNo']);
        }

        DB::table('rstxn_ugdhdrs')
            ->where('rj_no', $rjNo)
            ->update([
                'datadaftarugd_json' => json_encode($rjArr, true),
            ]);
    }
}
