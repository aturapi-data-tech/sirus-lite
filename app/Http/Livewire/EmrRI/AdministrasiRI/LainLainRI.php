<?php

namespace App\Http\Livewire\EmrRI\AdministrasiRI;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

use App\Http\Traits\EmrRI\EmrRITrait;
use App\Http\Traits\LOV\LOVLain\LOVLainTrait;


use Exception;


class LainLainRI extends Component
{
    use WithPagination, EmrRITrait, LOVLainTrait;


    // listener from blade////////////////
    protected $listeners = ['SyncAdministrasiRI' => 'mount'];


    //////////////////////////////
    // Ref on top bar
    //////////////////////////////
    public $riHdrNoRef;
    public array $dataDaftarRi = [];

    // LOV Nested
    public array $lain;
    // LOV Nested

    //////////////////////////////////////////////////////////////////////
    public array $formEntryLain = [
        'lainDate' => '', // Format tanggal wajib: dd/mm/yyyy hh24:mi:ss
        'lainId' => '',
        'lainPrice' => '', // Harga kunjungan minimal 0
    ];

    public array $dataLain = [];

    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////

    public function insertLain(): void
    {
        // validate
        $this->checkRiStatus();

        $rules = [
            'formEntryLain.lainDate' => 'required|date_format:d/m/Y H:i:s',
            'formEntryLain.lainId' => 'required|exists:rsmst_others,other_id',
            'formEntryLain.lainPrice' => 'required|numeric|min:0',
        ];

        $messages = [
            'formEntryLain.lainDate.required'      => 'Tanggal lain wajib diisi.',
            'formEntryLain.lainDate.date_format'     => 'Format tanggal lain harus dd/mm/yyyy hh24:mi:ss.',
            'formEntryLain.lainId.required'          => 'ID lain wajib diisi.',
            'formEntryLain.lainId.exists'            => 'ID lain tidak valid atau tidak ditemukan.',
            'formEntryLain.lainPrice.required'       => 'Harga lain wajib diisi.',
            'formEntryLain.lainPrice.numeric'        => 'Harga lain harus berupa angka.',
            'formEntryLain.lainPrice.min'            => 'Harga lain minimal 0.',
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

            $lastInserted = DB::table('rstxn_riothers')
                ->select(DB::raw("nvl(max(other_no)+1,1) as other_no_max"))
                ->first();
            // insert into table transaksi
            DB::table('rstxn_riothers')
                ->insert([
                    'other_date' => DB::raw("to_date('" . $this->formEntryLain['lainDate'] . "','dd/mm/yyyy hh24:mi:ss')"),
                    'other_price' =>  $this->formEntryLain['lainPrice'],
                    'other_id' =>  $this->formEntryLain['lainId'],
                    'rihdr_no' =>  $this->riHdrNoRef,
                    'other_no' =>  $lastInserted->other_no_max,
                ]);


            $this->administrasiRIuserLog($this->riHdrNoRef, 'Lain ' . $this->formEntryLain['lainDesc'] . ' Tarif:' . $this->formEntryLain['lainPrice'] . ' Txn No:' . $lastInserted->other_no_max);
            $this->emit('SyncAdministrasiRI');
            $this->resetformEntryLain();
            //
        } catch (Exception $e) {
            // display an error to user
            dd($e->getMessage());
        }
        // goto start;
    }

    public function removeLain($LainNo)
    {
        $this->checkRiStatus();
        // pengganti race condition
        // start:
        try {

            // remove into table transaksi
            DB::table('rstxn_riothers')
                ->where('other_no', $LainNo)
                ->delete();
            //
            $this->administrasiRIuserLog($this->riHdrNoRef, 'Lain Remove Txn No:' . $LainNo);
            $this->emit('SyncAdministrasiRI');

            $this->resetformEntryLain();
        } catch (Exception $e) {
            // display an error to user
            dd($e->getMessage());
        }
        // goto start;


    }

    public function setLainDate($date)
    {
        $this->formEntryLain['lainDate'] = $date;
    }

    public function resetformEntryLain()
    {
        $this->reset([
            'formEntryLain',
            'collectingMyLain', //Reset LOV / render  / empty NestLov
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
        // Jika data LainId ada
        if ($this->formEntryLain['lainId']) {
            $this->addLain($this->formEntryLain['lainId'] ?? '', $this->formEntryLain['lainDesc'] ?? '', $this->formEntryLain['lainPrice'] ?? '');
        }
    }

    // when new form instance
    public function mount()
    {
        $this->findData($this->riHdrNoRef);
    }

    private function findData($riHlainNo): void
    {
        $riLain = DB::table('rstxn_riothers')
            ->join('rsmst_others', 'rstxn_riothers.other_id', '=', 'rsmst_others.other_id')
            ->select(
                DB::raw("to_char(rstxn_riothers.other_date, 'dd/mm/yyyy hh24:mi:ss') as other_date"),
                'rstxn_riothers.other_id',
                'rsmst_others.other_desc',
                'rstxn_riothers.other_price',
                'rstxn_riothers.rihdr_no',
                'rstxn_riothers.other_no'
            )
            ->where('rihdr_no', $riHlainNo)
            ->get();
        $this->dataLain['riLain'] = json_decode(json_encode($riLain, true), true);
        $this->syncDataPrimer();
    }

    private function administrasiRIuserLog($riHlainNo, $logs): void
    {
        $this->dataDaftarRi = $this->findDataRI($riHlainNo);

        $this->dataDaftarRi['AdministrasiRI']['userLogs'][] =
            [
                'userLogDesc' => $logs,
                'userLog' => auth()->user()->myuser_name,
                'userLogDate' => Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s')
            ];
        $this->updateJsonRI($riHlainNo, $this->dataDaftarRi);
        $this->reset(['dataDaftarRi']);
    }

    private function syncDataFormEntry(): void
    {
        // Synk Lov Lain
        $this->formEntryLain['lainId'] = $this->lain['LainId'] ?? '';
        $this->formEntryLain['lainDesc'] = $this->lain['LainDesc'] ?? '';
        // $this->formEntryLain['lainPrice'] = $this->lain['LainPrice'] ?? '';

        //price
        if (!isset($this->formEntryLain['lainPrice']) || empty($this->formEntryLain['lainPrice'])) {

            // Jika class_id ditemukan, ambil lain_price dari tabel rsmst_others
            $lainPrice = DB::table('rsmst_others')
                ->where('other_id', $this->lain['LainId'] ?? '')
                ->value('other_price');

            // Set lainPrice jika ditemukan, jika tidak set ke 0
            $this->formEntryLain['lainPrice'] = $lainPrice ?? 0;
        }
    }

    private function syncLOV(): void
    {
        $this->lain = $this->collectingMyLain;
    }
    // select data start////////////////


    // select data start////////////////
    public function render()
    {

        // LOV
        $this->syncLOV();
        // FormEntry
        $this->syncDataFormEntry();


        return view(
            'livewire.emr-r-i.administrasi-r-i.lain-lain-r-i',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Data Pasien Inap',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Lain',
            ]
        );
    }
    // select data end////////////////


}
