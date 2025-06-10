<?php

namespace App\Http\Livewire\EmrRI\AdministrasiRI;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Traits\LOV\LOVJasaMedis\LOVJasaMedisTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

use App\Http\Traits\EmrRI\EmrRITrait;


use Exception;


class JasaMedisRI extends Component
{
    use WithPagination, EmrRITrait, LOVJasaMedisTrait;


    // listener from blade////////////////
    protected $listeners = ['SyncAdministrasiRI' => 'mount'];


    //////////////////////////////
    // Ref on top bar
    //////////////////////////////
    public $riHdrNoRef;
    public array $dataDaftarRi = [];

    // LOV Nested
    public array $jasaMedis;
    // LOV Nested

    //////////////////////////////////////////////////////////////////////
    public array $formEntryJasaMedis = [
        'jasaMedisDate' => '', // Format tanggal wajib: dd/mm/yyyy hh24:mi:ss
        'jasaMedisId' => '',
        'jasaMedisPrice' => '', // Harga kunjungan minimal 0
        'jasaMedisQty' => '',
    ];
    public array $dataJasaMedis = [];
    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////

    public function insertJasaMedis(): void
    {
        // validate
        $this->checkRiStatus();

        $rules = [
            'formEntryJasaMedis.jasaMedisDate' => 'required|date_format:d/m/Y H:i:s', // Format tanggal wajib: dd/mm/yyyy hh24:mi:ss
            'formEntryJasaMedis.jasaMedisId' => 'required|exists:rsmst_actparamedics,pact_id', // ID jasaMedis harus ada di tabel rsmst_actparamedics kolom pact_id
            'formEntryJasaMedis.jasaMedisPrice' => 'required|numeric|min:0', // Harga kunjungan minimal 0
            'formEntryJasaMedis.jasaMedisQty' => 'required|numeric|min:1', // Jumlah minimal 1
        ];

        $messages = [
            'formEntryJasaMedis.jasaMedisDate.required' => 'Tanggal jasa medis wajib diisi.',
            'formEntryJasaMedis.jasaMedisDate.date_format' => 'Format tanggal jasa medis harus dd/mm/yyyy hh24:mi:ss.',
            'formEntryJasaMedis.jasaMedisId.required' => 'ID jasa medis wajib diisi.',
            'formEntryJasaMedis.jasaMedisId.exists' => 'ID jasa medis tidak valid atau tidak ditemukan.',
            'formEntryJasaMedis.jasaMedisPrice.required' => 'Harga jasa medis wajib diisi.',
            'formEntryJasaMedis.jasaMedisPrice.numeric' => 'Harga jasa medis harus berupa angka.',
            'formEntryJasaMedis.jasaMedisPrice.min' => 'Harga jasa medis minimal 0.',
            'formEntryJasaMedis.jasaMedisQty.required' => 'Jumlah jasa medis wajib diisi.',
            'formEntryJasaMedis.jasaMedisQty.numeric' => 'Jumlah jasa medis harus berupa angka.',
            'formEntryJasaMedis.jasaMedisQty.min' => 'Jumlah jasa medis minimal 1.',
        ];

        // Proses validasi
        try {
            $this->validate($rules, $messages);
        } catch (\Illuminate\Validation\ValidationException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Periksa kembali input data. " . $e->getMessage());
            $this->validate($rules, $messages);
        }



        // start:
        try {

            $lastInserted = DB::table('rstxn_riactparams')
                ->select(DB::raw("nvl(max(actp_no)+1,1) as actp_no_max"))
                ->first();
            // insert into table transaksi
            DB::table('rstxn_riactparams')
                ->insert([
                    'actp_date' => DB::raw("to_date('" . $this->formEntryJasaMedis['jasaMedisDate'] . "','dd/mm/yyyy hh24:mi:ss')"),
                    'pact_id' =>  $this->formEntryJasaMedis['jasaMedisId'],
                    'actp_price' =>  $this->formEntryJasaMedis['jasaMedisPrice'],
                    'actp_qty' =>  $this->formEntryJasaMedis['jasaMedisQty'],
                    'rihdr_no' =>  $this->riHdrNoRef,
                    'actp_no' =>  $lastInserted->actp_no_max,
                ]);

            //Loop Paket Sesuai Jml
            foreach (range(1, $this->formEntryJasaMedis['jasaMedisQty']) as $i) {
                $this->paketLainLainJasaMedis($this->formEntryJasaMedis['jasaMedisId'], $this->riHdrNoRef);
            }


            $this->administrasiRIuserLog($this->riHdrNoRef, 'JasaMedis ' . $this->formEntryJasaMedis['jasaMedisDesc'] . ' Tarif:' . $this->formEntryJasaMedis['jasaMedisPrice'] . 'Jml' . $this->formEntryJasaMedis['jasaMedisQty'] . ' Txn No:' . $lastInserted->actp_no_max);
            $this->emit('SyncAdministrasiRI');
            $this->resetformEntryJasaMedis();
            //
        } catch (Exception $e) {
            // display an error to user
            dd($e->getMessage());
        }
        // goto start;
    }


    private function paketLainLainJasaMedis($pactId, $riHdrNo): void
    {
        $classId = DB::table('rstxn_rihdrs')
            ->join('rsmst_rooms', 'rstxn_rihdrs.room_id', '=', 'rsmst_rooms.room_id')
            ->where('rihdr_no', $riHdrNo)
            ->value('class_id');

        // Jika class_id ditemukan
        if ($classId) {
            $collection = DB::table('rsmst_actpoclasses')
                ->join('rsmst_actpclasses', 'rsmst_actpoclasses.id', '=', 'rsmst_actpclasses.id')
                ->select('other_id', 'actpo_price')
                ->where('pact_id', $pactId)
                ->where('class_id', $classId)
                ->orderBy('pact_id')
                ->get();

            foreach ($collection as $item) {
                $this->insertLainLain($pactId, $riHdrNo, $item->other_id, $item->actpo_price);
            }
        }
    }

    private function insertLainLain($pactId, $riHdrNo, $otherId, $otherPrice): void
    {
        // require nik ketika pasien tidak dikenal
        $collectingMyLainLain =
            [
                "LainLainId" => $otherId,
                "LainLainPrice" => $otherPrice,
                "pactId" => $pactId,
                "riHdrNo" => $riHdrNo,

            ];

        $rules = [
            'LainLainId' => 'required|exists:rsmst_actpoclasses,other_id', // Wajib diisi dan harus ada di tabel rsmst_lain_lain kolom id
            'LainLainPrice' => 'required|numeric|min:0', // Wajib diisi, harus angka, dan minimal 0
            'pactId' => 'required|exists:rsmst_actparamedics,pact_id', // Wajib diisi dan harus ada di tabel rsmst_actparamedics kolom pact_id
            'riHdrNo' => 'required|exists:rstxn_rihdrs,rihdr_no', // Wajib diisi dan harus ada di tabel rstxn_rihdrs kolom rihdr_no
        ];

        $messages = [
            'LainLainId.required' => 'ID Lain-Lain wajib diisi.',
            'LainLainId.exists' => 'ID Lain-Lain tidak valid atau tidak ditemukan.',
            'LainLainPrice.required' => 'Harga Lain-Lain wajib diisi.',
            'LainLainPrice.numeric' => 'Harga Lain-Lain harus berupa angka.',
            'LainLainPrice.min' => 'Harga Lain-Lain minimal 0.',
            'pactId.required' => 'ID Pact wajib diisi.',
            'pactId.exists' => 'ID Pact tidak valid atau tidak ditemukan.',
            'riHdrNo.required' => 'Nomor RIHDR wajib diisi.',
            'riHdrNo.exists' => 'Nomor RIHDR tidak valid atau tidak ditemukan.',
        ];


        // Proses Validasi///////////////////////////////////////////
        $validator = Validator::make($collectingMyLainLain, $rules, $messages);

        if ($validator->fails()) {
            dd($validator->errors());
        }


        // pengganti race condition
        // start:
        try {

            $lastInserted = DB::table('rstxn_riothers')
                ->select(DB::raw("nvl(max(other_no)+1,1) as other_no_max"))
                ->first();
            // insert into table transaksi
            DB::table('rstxn_riothers')
                ->insert([
                    'other_no' => $lastInserted->other_no_max,
                    'rihdr_no' => $collectingMyLainLain['riHdrNo'],
                    'other_id' => $collectingMyLainLain['LainLainId'],
                    'other_price' => $collectingMyLainLain['LainLainPrice'],
                    'other_date' => DB::raw("to_date('" . Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s') . "','dd/mm/yyyy hh24:mi:ss')"),
                ]);
            //
        } catch (Exception $e) {
            // display an error to user
            dd($e->getMessage());
        }
        // goto start;
    }



    public function removeJasaMedis($JasaMedisNo)
    {
        $this->checkRiStatus();
        // pengganti race condition
        // start:
        try {

            // remove into table transaksi
            DB::table('rstxn_riactparams')
                ->where('actp_no', $JasaMedisNo)
                ->delete();
            //
            $this->administrasiRIuserLog($this->riHdrNoRef, 'JasaMedis Remove Txn No:' . $JasaMedisNo);
            $this->emit('SyncAdministrasiRI');

            $this->resetformEntryJasaMedis();
        } catch (Exception $e) {
            // display an error to user
            dd($e->getMessage());
        }
        // goto start;


    }

    public function setJasaMedisDate($date)
    {
        $this->formEntryJasaMedis['jasaMedisDate'] = $date;
    }

    public function resetformEntryJasaMedis()
    {
        $this->reset([
            'formEntryJasaMedis',
            'collectingMyJasaMedis' //Reset LOV / render  / empty NestLov
        ]);
        $this->resetValidation();
    }

    public function checkRiStatus()
    {
        $lastInserted = DB::table('rstxn_rihdrs')
            ->select('ri_status')
            ->where('rihdr_no', $this->riHdrNoRef)
            ->first();

        if ($lastInserted->ri_status !== 'I') {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Pasien Sudah Pulang, Trasaksi Terkunci.");
            return (dd('Pasien Sudah Pulang, Trasaksi Terkuncixx.' . $this->riHdrNoRef));
        }
    }

    private function syncDataPrimer(): void
    {
        // sync data primer untuk LOV
        // Jika data JasaMedisId ada
        if ($this->formEntryJasaMedis['jasaMedisId']) {
            $this->addJasaMedis($this->formEntryJasaMedis['jasaMedisId'] ?? '', $this->formEntryJasaMedis['jasaMedisDesc'] ?? '', $this->formEntryJasaMedis['jasaMedisPrice'] ?? '');
        }
    }


    // when new form instance
    public function mount()
    {
        $this->findData($this->riHdrNoRef);
    }

    private function findData($riHdrNo): void
    {


        $riJasaMedis = DB::table('rstxn_riactparams')
            ->join('rsmst_actparamedics', 'rsmst_actparamedics.pact_id', '=', 'rstxn_riactparams.pact_id')
            ->select(
                DB::raw("to_char(rstxn_riactparams.actp_date, 'dd/mm/yyyy hh24:mi:ss') as actp_date"),
                'rstxn_riactparams.pact_id',
                'rsmst_actparamedics.pact_desc',
                'rstxn_riactparams.actp_price',
                'rstxn_riactparams.actp_qty',
                'rstxn_riactparams.rihdr_no',
                'rstxn_riactparams.actp_no'
            )
            ->where('rihdr_no', $riHdrNo)
            ->get();
        $this->dataJasaMedis['riJasaMedis'] = json_decode(json_encode($riJasaMedis, true), true);
        $this->syncDataPrimer();
    }

    private function administrasiRIuserLog($riHdrNo, $logs): void
    {
        $this->dataDaftarRi = $this->findDataRI($riHdrNo);

        $this->dataDaftarRi['AdministrasiRI']['userLogs'][] =
            [
                'userLogDesc' => $logs,
                'userLog' => auth()->user()->myuser_name,
                'userLogDate' => Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s')
            ];
        $this->updateJsonRI($riHdrNo, $this->dataDaftarRi);
        // $this->reset(['dataDaftarRi']);
    }

    private function syncDataFormEntry(): void
    {
        // Synk Lov JasaMedis
        $this->formEntryJasaMedis['jasaMedisId'] = $this->jasaMedis['JasaMedisId'] ?? '';
        $this->formEntryJasaMedis['jasaMedisDesc'] = $this->jasaMedis['JasaMedisDesc'] ?? '';
        // $this->formEntryJasaMedis['jasaMedisPrice'] = $this->jasaMedis['JasaMedisPrice'] ?? '';

        //qty
        if (!isset($this->formEntryJasaMedis['jasaMedisQty']) || empty($this->formEntryJasaMedis['jasaMedisQty'])) {
            $this->formEntryJasaMedis['jasaMedisQty'] = 1;
        }

        //price
        if (!isset($this->formEntryJasaMedis['jasaMedisPrice']) || empty($this->formEntryJasaMedis['jasaMedisPrice'])) {
            // Ambil class_id dari tabel rstxn_rihdrs
            $classId = DB::table('rstxn_rihdrs')
                ->join('rsmst_rooms', 'rstxn_rihdrs.room_id', '=', 'rsmst_rooms.room_id')
                ->where('rihdr_no', $this->riHdrNoRef)
                ->value('class_id');

            // Jika class_id ditemukan, ambil jasaMedis_price dari tabel rsmst_actpclasses
            if ($classId) {
                $this->dataDaftarRi = $this->findDataRI($this->riHdrNoRef);
                // Ambil status klaim dari tabel rsmst_klaimtypes
                // Pastikan bahwa data klaim pada rawat inap diakses dari variabel yang tepat, contohnya $this->dataDaftarRi
                $klaimStatus = DB::table('rsmst_klaimtypes')
                    ->where('klaim_id', $this->dataDaftarRi['klaimId'] ?? '')
                    ->value('klaim_status') ?? 'UMUM';

                if ($klaimStatus === 'BPJS') {
                    $jasaMedisPrice = DB::table('rsmst_actpclasses')
                        ->where('pact_id', $this->jasaMedis['JasaMedisId'] ?? '')
                        ->where('class_id', $classId)
                        ->value('actp_price_bpjs');
                } else {
                    $jasaMedisPrice = DB::table('rsmst_actpclasses')
                        ->where('pact_id', $this->jasaMedis['JasaMedisId'] ?? '')
                        ->where('class_id', $classId)
                        ->value('actp_price');
                }
                // $this->reset(['dataDaftarRi']);

                // Set harga jasa medis; jika tidak ditemukan nilai, maka set ke 0
                $this->formEntryJasaMedis['jasaMedisPrice'] = $jasaMedisPrice ?? 0;
            } else {
                // Jika class_id tidak ditemukan, set harga jasa medis ke 0
                $this->formEntryJasaMedis['jasaMedisPrice'] = 0;
            }
        }
    }

    private function syncLOV(): void
    {
        $this->jasaMedis = $this->collectingMyJasaMedis;
    }
    // select data start////////////////
    public function render()
    {

        // LOV
        $this->syncLOV();
        // FormEntry
        $this->syncDataFormEntry();

        return view(
            'livewire.emr-r-i.administrasi-r-i.jasa-medis-r-i',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Data Pasien Inap',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'JasaMedis',
            ]
        );
    }
    // select data end////////////////


}
