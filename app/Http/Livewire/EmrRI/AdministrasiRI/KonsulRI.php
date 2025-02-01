<?php

namespace App\Http\Livewire\EmrRI\AdministrasiRI;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Traits\LOV\LOVDokter\LOVDokterTrait;
use Carbon\Carbon;

use App\Http\Traits\EmrRI\EmrRITrait;


use Exception;


class KonsulRI extends Component
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
    public array $formEntryKonsul = [
        'konsulDate' => '', // Format tanggal wajib: dd/mm/yyyy hh24:mi:ss
        'drId' => '', // ID dokter harus ada di tabel rsmst_doctors
        'konsulPrice' => '', // Harga kunjungan minimal 0
    ];
    public array $dataKonsul = [];
    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////

    public function insertKonsul(): void
    {
        // validate
        $this->checkRiStatus();

        $rules = [
            'formEntryKonsul.konsulDate' => 'required|date_format:d/m/Y H:i:s', // Format tanggal wajib: dd/mm/yyyy hh24:mi:ss
            'formEntryKonsul.drId' => 'required|exists:rsmst_doctors,dr_id', // ID dokter harus ada di tabel rsmst_doctors
            'formEntryKonsul.konsulPrice' => 'required|numeric|min:0', // Harga kunjungan minimal 0
        ];

        $messages = [
            'formEntryKonsul.konsulDate.required' => 'Tanggal kunjungan wajib diisi.',
            'formEntryKonsul.konsulDate.date_format' => 'Format tanggal kunjungan harus dd/mm/yyyy hh24:mi:ss.',

            'formEntryKonsul.drId.required' => 'ID dokter wajib diisi.',
            'formEntryKonsul.drId.exists' => 'ID dokter tidak ditemukan dalam database.', // Pesan error jika ID tidak ada di rsmst_doctors

            'formEntryKonsul.konsulPrice.required' => 'Harga kunjungan wajib diisi.',
            'formEntryKonsul.konsulPrice.numeric' => 'Harga kunjungan harus berupa angka.',
            'formEntryKonsul.konsulPrice.min' => 'Harga kunjungan tidak boleh negatif.',
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

            $lastInserted = DB::table('rstxn_rikonsuls')
                ->select(DB::raw("nvl(max(konsul_no)+1,1) as konsul_no_max"))
                ->first();
            // insert into table transaksi
            DB::table('rstxn_rikonsuls')
                ->insert([
                    'konsul_date' => DB::raw("to_date('" . $this->formEntryKonsul['konsulDate'] . "','dd/mm/yyyy hh24:mi:ss')"),
                    'dr_id' =>  $this->formEntryKonsul['drId'],
                    'konsul_price' =>  $this->formEntryKonsul['konsulPrice'],
                    'rihdr_no' =>  $this->riHdrNoRef,
                    'konsul_no' =>  $lastInserted->konsul_no_max,
                ]);

            $this->administrasiRIuserLog($this->riHdrNoRef, 'Konsul ' . $this->formEntryKonsul['drName'] . ' Tarif:' . $this->formEntryKonsul['konsulPrice'] . ' Txn No:' . $lastInserted->konsul_no_max);
            $this->emit('SyncAdministrasiRI');
            $this->resetformEntryKonsul();
            //
        } catch (Exception $e) {
            // display an error to user
            dd($e->getMessage());
        }
        // goto start;
    }

    public function removeKonsul($KonsulNo)
    {
        $this->checkRiStatus();
        // pengganti race condition
        // start:
        try {

            // remove into table transaksi
            DB::table('rstxn_rikonsuls')
                ->where('konsul_no', $KonsulNo)
                ->delete();
            //
            $this->administrasiRIuserLog($this->riHdrNoRef, 'Konsul Remove Txn No:' . $KonsulNo);
            $this->emit('SyncAdministrasiRI');

            $this->resetformEntryKonsul();
        } catch (Exception $e) {
            // display an error to user
            dd($e->getMessage());
        }
        // goto start;


    }

    public function setKonsulDate($date)
    {
        $this->formEntryKonsul['konsulDate'] = $date;
    }

    public function resetformEntryKonsul()
    {
        $this->reset([
            'formEntryKonsul',
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
        if ($this->formEntryKonsul['drId']) {
            $this->addDokter($this->formEntryKonsul['drId'] ?? '', $this->formEntryKonsul['drDesc'] ?? '', $this->formEntryKonsul['poliId'] ?? '', $this->formEntryKonsul['poliDesc'] ?? '', $this->formEntryKonsul['kdpolibpjs'] ?? '', $this->formEntryKonsul['kddrbpjs'] ?? '');
        }
    }


    // when new form instance
    public function mount()
    {
        $this->findData($this->riHdrNoRef);
    }

    private function findData($riHdrNo): void
    {


        $riKonsul = DB::table('rstxn_rikonsuls')
            ->join('rsmst_doctors', 'rstxn_rikonsuls.dr_id', '=', 'rsmst_doctors.dr_id')
            ->select(
                'rstxn_rikonsuls.konsul_date',
                'rstxn_rikonsuls.dr_id',
                'rsmst_doctors.dr_name',
                'rstxn_rikonsuls.konsul_price',
                'rstxn_rikonsuls.rihdr_no',
                'rstxn_rikonsuls.konsul_no'

            )
            ->where('rihdr_no', $riHdrNo)
            ->get();
        $this->dataKonsul['riKonsul'] = json_decode(json_encode($riKonsul, true), true);
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
        // Synk Lov Dokter
        $this->formEntryKonsul['drId'] = $this->dokter['DokterId'] ?? '';
        $this->formEntryKonsul['drName'] = $this->dokter['DokterDesc'] ?? '';
        $this->formEntryKonsul['poliId'] = $this->dokter['PoliId'] ?? '';
        $this->formEntryKonsul['poliDesc'] = $this->dokter['PoliDesc'] ?? '';
        $this->formEntryKonsul['kdpolibpjs'] =  $this->dokter['kdPoliBpjs'] ?? '';
        $this->formEntryKonsul['kddrbpjs'] =  $this->dokter['kdDokterBpjs'] ?? '';

        //
        if (!isset($this->formEntryKonsul['konsulPrice']) || empty($this->formEntryKonsul['konsulPrice'])) {
            // Ambil class_id dari tabel rstxn_rihdrs
            $classId = DB::table('rstxn_rihdrs')
                ->join('rsmst_rooms', 'rstxn_rihdrs.room_id', '=', 'rsmst_rooms.room_id')
                ->where('rihdr_no', $this->riHdrNoRef)
                ->value('class_id');

            // Jika class_id ditemukan, ambil konsul_price dari tabel rsmst_docvisits
            if ($classId) {
                $konsulPrice = DB::table('rsmst_docvisits')
                    ->where('dr_id', $this->dokter['DokterId'] ?? '')
                    ->where('class_id', $classId)
                    ->value('konsul_price');

                // Set konsulPrice jika ditemukan, jika tidak set ke 0
                $this->formEntryKonsul['konsulPrice'] = $konsulPrice ?? 0;
            } else {
                // Jika class_id tidak ditemukan, set konsulPrice ke 0
                $this->formEntryKonsul['konsulPrice'] = 0;
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
            'livewire.emr-r-i.administrasi-r-i.konsul-r-i',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Data Pasien Inap',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Konsul',
            ]
        );
    }
    // select data end////////////////


}
