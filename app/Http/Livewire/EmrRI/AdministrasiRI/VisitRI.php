<?php

namespace App\Http\Livewire\EmrRI\AdministrasiRI;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Traits\LOV\LOVDokter\LOVDokterTrait;
use Carbon\Carbon;

use App\Http\Traits\EmrRI\EmrRITrait;


use Exception;


class VisitRI extends Component
{
    use WithPagination, EmrRITrait, LOVDokterTrait;


    // listener from blade////////////////
    protected $listeners = ['SyncAdministrasiRI' => 'mount'];


    //////////////////////////////
    // Ref on top bar
    //////////////////////////////
    public $riHdrNoRef;
    public array $dataDaftarRi = [];

    // LOV Nested
    public array $dokter;
    // LOV Nested

    //////////////////////////////////////////////////////////////////////
    public array $formEntryVisit = [
        'visitDate' => '', // Format tanggal wajib: dd/mm/yyyy hh24:mi:ss
        'drId' => '', // ID dokter harus ada di tabel rsmst_doctors
        'visitPrice' => '', // Harga kunjungan minimal 0
    ];
    public array $dataVisit = [];
    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////

    public function insertVisit(): void
    {
        // validate
        $this->checkRiStatus();

        $rules = [
            'formEntryVisit.visitDate' => 'required|date_format:d/m/Y H:i:s', // Format tanggal wajib: dd/mm/yyyy hh24:mi:ss
            'formEntryVisit.drId' => 'required|exists:rsmst_doctors,dr_id', // ID dokter harus ada di tabel rsmst_doctors
            'formEntryVisit.visitPrice' => 'required|numeric|min:0', // Harga kunjungan minimal 0
        ];

        $messages = [
            'formEntryVisit.visitDate.required' => 'Tanggal kunjungan wajib diisi.',
            'formEntryVisit.visitDate.date_format' => 'Format tanggal kunjungan harus dd/mm/yyyy hh24:mi:ss.',

            'formEntryVisit.drId.required' => 'ID dokter wajib diisi.',
            'formEntryVisit.drId.exists' => 'ID dokter tidak ditemukan dalam database.', // Pesan error jika ID tidak ada di rsmst_doctors

            'formEntryVisit.visitPrice.required' => 'Harga kunjungan wajib diisi.',
            'formEntryVisit.visitPrice.numeric' => 'Harga kunjungan harus berupa angka.',
            'formEntryVisit.visitPrice.min' => 'Harga kunjungan tidak boleh negatif.',
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

            $lastInserted = DB::table('rstxn_rivisits')
                ->select(DB::raw("nvl(max(visit_no)+1,1) as visit_no_max"))
                ->first();
            // insert into table transaksi
            DB::table('rstxn_rivisits')
                ->insert([
                    'visit_date' => DB::raw("to_date('" . $this->formEntryVisit['visitDate'] . "','dd/mm/yyyy hh24:mi:ss')"),
                    'dr_id' =>  $this->formEntryVisit['drId'],
                    'visit_price' =>  $this->formEntryVisit['visitPrice'],
                    'rihdr_no' =>  $this->riHdrNoRef,
                    'visit_no' =>  $lastInserted->visit_no_max,
                ]);

            $this->administrasiRIuserLog($this->riHdrNoRef, 'Visist ' . $this->formEntryVisit['drName'] . ' Tarif:' . $this->formEntryVisit['visitPrice'] . ' Txn No:' . $lastInserted->visit_no_max);
            $this->emit('SyncAdministrasiRI');
            $this->resetformEntryVisit();
            //
        } catch (Exception $e) {
            // display an error to user
            dd($e->getMessage());
        }
        // goto start;
    }

    public function removeVisit($VisitNo)
    {
        $this->checkRiStatus();
        // pengganti race condition
        // start:
        try {

            // remove into table transaksi
            DB::table('rstxn_rivisits')
                ->where('visit_no', $VisitNo)
                ->delete();
            //
            $this->administrasiRIuserLog($this->riHdrNoRef, 'Visit Remove Txn No:' . $VisitNo);
            $this->emit('SyncAdministrasiRI');

            $this->resetformEntryVisit();
        } catch (Exception $e) {
            // display an error to user
            dd($e->getMessage());
        }
        // goto start;


    }

    public function setVisitDate($date)
    {
        $this->formEntryVisit['visitDate'] = $date;
    }

    public function resetformEntryVisit()
    {
        $this->reset([
            'formEntryVisit',
            'collectingMyDokter' //Reset LOV / render  / empty NestLov
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

        // Jika data drId ada
        if ($this->formEntryVisit['drId']) {
            $this->addDokter($this->formEntryVisit['drId'] ?? '', $this->formEntryVisit['drDesc'] ?? '', $this->formEntryVisit['poliId'] ?? '', $this->formEntryVisit['poliDesc'] ?? '', $this->formEntryVisit['kdpolibpjs'] ?? '', $this->formEntryVisit['kddrbpjs'] ?? '');
        }
    }


    // when new form instance
    public function mount()
    {
        $this->findData($this->riHdrNoRef);
    }

    private function findData($riHdrNo): void
    {


        $riVisit = DB::table('rstxn_rivisits')
            ->join('rsmst_doctors', 'rstxn_rivisits.dr_id', '=', 'rsmst_doctors.dr_id')
            ->select(
                DB::raw("to_char(rstxn_rivisits.visit_date, 'dd/mm/yyyy hh24:mi:ss') AS visit_date"),
                'rstxn_rivisits.dr_id',
                'rsmst_doctors.dr_name',
                'rstxn_rivisits.visit_price',
                'rstxn_rivisits.rihdr_no',
                'rstxn_rivisits.visit_no'

            )
            ->where('rihdr_no', $riHdrNo)
            ->get();
        $this->dataVisit['riVisit'] = json_decode(json_encode($riVisit, true), true);
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
        // Synk Lov Dokter
        $this->formEntryVisit['drId'] = $this->dokter['DokterId'] ?? '';
        $this->formEntryVisit['drName'] = $this->dokter['DokterDesc'] ?? '';
        $this->formEntryVisit['poliId'] = $this->dokter['PoliId'] ?? '';
        $this->formEntryVisit['poliDesc'] = $this->dokter['PoliDesc'] ?? '';
        $this->formEntryVisit['kdpolibpjs'] =  $this->dokter['kdPoliBpjs'] ?? '';
        $this->formEntryVisit['kddrbpjs'] =  $this->dokter['kdDokterBpjs'] ?? '';


        //
        if (!isset($this->formEntryVisit['visitPrice']) || empty($this->formEntryVisit['visitPrice'])) {
            // Ambil class_id dari tabel rstxn_rihdrs
            $classId = DB::table('rstxn_rihdrs')
                ->join('rsmst_rooms', 'rstxn_rihdrs.room_id', '=', 'rsmst_rooms.room_id')
                ->where('rihdr_no', $this->riHdrNoRef)
                ->value('class_id');

            // Jika class_id ditemukan, ambil visit_price dari tabel rsmst_docvisits
            if ($classId) {
                $this->dataDaftarRi = $this->findDataRI($this->riHdrNoRef);
                // Ambil status klaim dari tabel rsmst_klaimtypes, default ke 'UMUM' bila tidak ditemukan
                $klaimStatus = DB::table('rsmst_klaimtypes')
                    ->where('klaim_id', $this->dataDaftarRi['klaimId'] ?? '')
                    ->value('klaim_status') ?? 'UMUM';

                if ($klaimStatus === 'BPJS') {
                    // Ambil visit_price dari tabel rsmst_docvisits_bpjs
                    $visitPrice = DB::table('rsmst_docvisits')
                        ->where('dr_id', $this->dokter['DokterId'] ?? '')
                        ->where('class_id', $classId)
                        ->value('visit_price_bpjs');
                } else {
                    // Ambil visit_price dari tabel rsmst_docvisits
                    $visitPrice = DB::table('rsmst_docvisits')
                        ->where('dr_id', $this->dokter['DokterId'] ?? '')
                        ->where('class_id', $classId)
                        ->value('visit_price');
                }
                // $this->reset(['dataDaftarRi']);

                // Set visitPrice jika ditemukan, jika tidak set ke 0
                $this->formEntryVisit['visitPrice'] = $visitPrice ?? 0;
            } else {
                // Jika class_id tidak ditemukan, set visitPrice ke 0
                $this->formEntryVisit['visitPrice'] = 0;
            }
        }
    }

    private function syncLOV(): void
    {
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
            'livewire.emr-r-i.administrasi-r-i.visit-r-i',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Data Pasien Inap',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Visit',
            ]
        );
    }
    // select data end////////////////


}
