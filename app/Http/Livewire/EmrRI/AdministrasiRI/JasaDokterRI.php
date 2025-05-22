<?php

namespace App\Http\Livewire\EmrRI\AdministrasiRI;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Traits\LOV\LOVJasaDokter\LOVJasaDokterTrait;
use App\Http\Traits\LOV\LOVDokter\LOVDokterTrait;

use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

use App\Http\Traits\EmrRI\EmrRITrait;


use Exception;


class JasaDokterRI extends Component
{
    use WithPagination, EmrRITrait, LOVJasaDokterTrait, LOVDokterTrait;


    // listener from blade////////////////
    protected $listeners = ['SyncAdministrasiRI' => 'mount'];


    //////////////////////////////
    // Ref on top bar
    //////////////////////////////
    public $riHdrNoRef;
    public array $dataDaftarRi = [];

    // LOV Nested
    public array $jasaDokter;
    public array $dokter;
    // LOV Nested

    //////////////////////////////////////////////////////////////////////
    public array $formEntryJasaDokter = [
        'jasaDokterDate' => '', // Format tanggal wajib: dd/mm/yyyy hh24:mi:ss
        'drId' => '',
        'jasaDokterId' => '',
        'jasaDokterPrice' => '', // Harga kunjungan minimal 0
        'jasaDokterQty' => '',
    ];
    public array $dataJasaDokter = [];
    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////

    public function insertJasaDokter(): void
    {
        // validate
        $this->checkRiStatus();

        $rules = [
            'formEntryJasaDokter.jasaDokterDate' => 'required|date_format:d/m/Y H:i:s',
            'formEntryJasaDokter.drId' => 'required|exists:rsmst_doctors,dr_id',
            'formEntryJasaDokter.jasaDokterId' => 'required|exists:rsmst_accdocs,accdoc_id',
            'formEntryJasaDokter.jasaDokterPrice' => 'required|numeric|min:0',
            'formEntryJasaDokter.jasaDokterQty' => 'required|numeric|min:1',
        ];

        $messages = [
            'formEntryJasaDokter.jasaDokterDate.required' => 'Tanggal jasa dokter wajib diisi.',
            'formEntryJasaDokter.jasaDokterDate.date_format' => 'Format tanggal jasa dokter harus dd/mm/yyyy hh24:mi:ss.',
            'formEntryJasaDokter.drId.required' => 'ID dokter wajib diisi.',
            'formEntryJasaDokter.drId.exists' => 'ID dokter tidak valid atau tidak ditemukan.',
            'formEntryJasaDokter.jasaDokterId.required' => 'ID jasa dokter wajib diisi.',
            'formEntryJasaDokter.jasaDokterId.exists' => 'ID jasa dokter tidak valid atau tidak ditemukan.',
            'formEntryJasaDokter.jasaDokterPrice.required' => 'Harga jasa dokter wajib diisi.',
            'formEntryJasaDokter.jasaDokterPrice.numeric' => 'Harga jasa dokter harus berupa angka.',
            'formEntryJasaDokter.jasaDokterPrice.min' => 'Harga jasa dokter minimal 0.',
            'formEntryJasaDokter.jasaDokterQty.required' => 'Jumlah jasa dokter wajib diisi.',
            'formEntryJasaDokter.jasaDokterQty.numeric' => 'Jumlah jasa dokter harus berupa angka.',
            'formEntryJasaDokter.jasaDokterQty.min' => 'Jumlah jasa dokter minimal 1.',
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

            $lastInserted = DB::table('rstxn_riactdocs')
                ->select(DB::raw("nvl(max(actd_no)+1,1) as actd_no_max"))
                ->first();
            // insert into table transaksi
            DB::table('rstxn_riactdocs')
                ->insert([
                    'actd_date' => DB::raw("to_date('" . $this->formEntryJasaDokter['jasaDokterDate'] . "','dd/mm/yyyy hh24:mi:ss')"),
                    'accdoc_id' =>  $this->formEntryJasaDokter['jasaDokterId'],
                    'actd_price' =>  $this->formEntryJasaDokter['jasaDokterPrice'],
                    'actd_qty' =>  $this->formEntryJasaDokter['jasaDokterQty'],
                    'dr_id' =>  $this->formEntryJasaDokter['drId'],
                    'rihdr_no' =>  $this->riHdrNoRef,
                    'actd_no' =>  $lastInserted->actd_no_max,
                ]);


            $this->administrasiRIuserLog($this->riHdrNoRef, 'JasaDokter ' . $this->formEntryJasaDokter['jasaDokterDesc'] . ' Tarif:' . $this->formEntryJasaDokter['jasaDokterPrice'] . 'Jml' . $this->formEntryJasaDokter['jasaDokterQty'] . ' Txn No:' . $lastInserted->actd_no_max);
            $this->emit('SyncAdministrasiRI');
            $this->resetformEntryJasaDokter();
            //
        } catch (Exception $e) {
            // display an error to user
            dd($e->getMessage());
        }
        // goto start;
    }


    public function removeJasaDokter($JasaDokterNo)
    {
        $this->checkRiStatus();
        // pengganti race condition
        // start:
        try {

            // remove into table transaksi
            DB::table('rstxn_riactdocs')
                ->where('actd_no', $JasaDokterNo)
                ->delete();
            //
            $this->administrasiRIuserLog($this->riHdrNoRef, 'JasaDokter Remove Txn No:' . $JasaDokterNo);
            $this->emit('SyncAdministrasiRI');

            $this->resetformEntryJasaDokter();
        } catch (Exception $e) {
            // display an error to user
            dd($e->getMessage());
        }
        // goto start;


    }

    public function setJasaDokterDate($date)
    {
        $this->formEntryJasaDokter['jasaDokterDate'] = $date;
    }

    public function resetformEntryJasaDokter()
    {
        $this->reset([
            'formEntryJasaDokter',
            'collectingMyJasaDokter', //Reset LOV / render  / empty NestLov
            'collectingMyDokter', //Reset LOV / render  / empty NestLov

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
        // Jika data JasaDokterId ada
        if ($this->formEntryJasaDokter['jasaDokterId']) {
            $this->addJasaDokter($this->formEntryJasaDokter['jasaDokterId'] ?? '', $this->formEntryJasaDokter['jasaDokterDesc'] ?? '', $this->formEntryJasaDokter['jasaDokterPrice'] ?? '');
        }
    }


    // when new form instance
    public function mount()
    {
        $this->findData($this->riHdrNoRef);
    }

    private function findData($riHdrNo): void
    {


        $riJasaDokter = DB::table('rstxn_riactdocs')
            ->join('rsmst_accdocs', 'rsmst_accdocs.accdoc_id', '=', 'rstxn_riactdocs.accdoc_id')
            ->join('rsmst_doctors', 'rsmst_doctors.dr_id', '=', 'rstxn_riactdocs.dr_id')
            ->select(
                DB::raw("to_char(rstxn_riactdocs.actd_date, 'dd/mm/yyyy hh24:mi:ss') as actd_date"),
                'rstxn_riactdocs.dr_id',
                'rsmst_doctors.dr_name',
                'rstxn_riactdocs.accdoc_id',
                'rsmst_accdocs.accdoc_desc',
                'rstxn_riactdocs.actd_price',
                'rstxn_riactdocs.actd_qty',
                'rstxn_riactdocs.rihdr_no',
                'rstxn_riactdocs.actd_no'
            )
            ->where('rihdr_no', $riHdrNo)
            ->get();
        $this->dataJasaDokter['riJasaDokter'] = json_decode(json_encode($riJasaDokter, true), true);
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
        $this->reset(['dataDaftarRi']);
    }

    private function syncDataFormEntry(): void
    {
        // Synk Lov JasaDokter
        $this->formEntryJasaDokter['jasaDokterId'] = $this->jasaDokter['JasaDokterId'] ?? '';
        $this->formEntryJasaDokter['jasaDokterDesc'] = $this->jasaDokter['JasaDokterDesc'] ?? '';
        // $this->formEntryJasaDokter['jasaDokterPrice'] = $this->jasaDokter['JasaDokterPrice'] ?? '';

        //qty
        if (!isset($this->formEntryJasaDokter['jasaDokterQty']) || empty($this->formEntryJasaDokter['jasaDokterQty'])) {
            $this->formEntryJasaDokter['jasaDokterQty'] = 1;
        }

        //price
        if (!isset($this->formEntryJasaDokter['jasaDokterPrice']) || empty($this->formEntryJasaDokter['jasaDokterPrice'])) {
            // Ambil class_id dari tabel rstxn_rihdrs
            $classId = DB::table('rstxn_rihdrs')
                ->join('rsmst_rooms', 'rstxn_rihdrs.room_id', '=', 'rsmst_rooms.room_id')
                ->where('rihdr_no', $this->riHdrNoRef)
                ->value('class_id');

            // Jika class_id ditemukan, ambil jasaDokter_price dari tabel rsmst_actdclasses
            if ($classId) {
                $this->dataDaftarRi = $this->findDataRI($this->riHdrNoRef);
                // Ambil status klaim dari rsmst_klaimtypes (default ke 'UMUM' jika tidak ditemukan)
                $klaimStatus = DB::table('rsmst_klaimtypes')
                    ->where('klaim_id', $this->dataDaftarRi['klaimId'] ?? '')
                    ->value('klaim_status') ?? 'UMUM';

                if ($klaimStatus === 'BPJS') {

                    $jasaDokterPrice = DB::table('rsmst_actdclasses')
                        ->where('accdoc_id', $this->jasaDokter['JasaDokterId'] ?? '')
                        ->where('class_id', $classId)
                        ->value('actd_price_bpjs');
                } else {

                    $jasaDokterPrice = DB::table('rsmst_actdclasses')
                        ->where('accdoc_id', $this->jasaDokter['JasaDokterId'] ?? '')
                        ->where('class_id', $classId)
                        ->value('actd_price');
                }
                $this->reset(['dataDaftarRi']);

                // Set jasaDokterPrice jika ditemukan, jika tidak set ke 0
                $this->formEntryJasaDokter['jasaDokterPrice'] = $jasaDokterPrice ?? 0;
            } else {
                // Jika class_id tidak ditemukan, set jasaDokterPrice ke 0
                $this->formEntryJasaDokter['jasaDokterPrice'] = 0;
            }
        }


        // Synk Lov Dokter
        $this->formEntryJasaDokter['drId'] = $this->dokter['DokterId'] ?? '';
        $this->formEntryJasaDokter['drName'] = $this->dokter['DokterDesc'] ?? '';
        $this->formEntryJasaDokter['poliId'] = $this->dokter['PoliId'] ?? '';
        $this->formEntryJasaDokter['poliDesc'] = $this->dokter['PoliDesc'] ?? '';
        $this->formEntryJasaDokter['kdpolibpjs'] =  $this->dokter['kdPoliBpjs'] ?? '';
        $this->formEntryJasaDokter['kddrbpjs'] =  $this->dokter['kdDokterBpjs'] ?? '';
    }

    private function syncLOV(): void
    {
        $this->jasaDokter = $this->collectingMyJasaDokter;
        $this->dokter = $this->collectingMyDokter;
    }
    // select data start////////////////
    public function render()
    {

        // LOV
        $this->syncLOV();
        // FormEntry
        $this->syncDataFormEntry();

        return view(
            'livewire.emr-r-i.administrasi-r-i.jasa-dokter-r-i',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Data Pasien Inap',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'JasaDokter',
            ]
        );
    }
    // select data end////////////////


}
