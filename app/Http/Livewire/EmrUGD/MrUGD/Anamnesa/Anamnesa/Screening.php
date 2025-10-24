<?php

namespace App\Http\Livewire\EmrUGD\MrUGD\Anamnesa\Anamnesa;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Validation\ValidationException;

use Livewire\Component;
use App\Http\Traits\EmrUGD\EmrUGDTrait;


class Screening extends Component
{
    use EmrUGDTrait;

    // listener from blade////////////////
    protected $listeners = [];



    //////////////////////////////
    // Ref on top bar
    //////////////////////////////

    // dataDaftarUgd RJ
    public $rjNoRef;
    public array $dataDaftarUgd = [];

    public array $screening =
    [
        "keluhanUtama" => "",
        "pernafasan" => "",
        "pernafasanOptions" => [
            ["pernafasan" => "Nafas Normal"],
            ["pernafasan" => "Tampak Sesak"],
        ],

        "kesadaran" => "",
        "kesadaranOptions" => [
            ["kesadaran" => "Sadar Penuh"],
            ["kesadaran" => "Tampak Mengantuk"],
            ["kesadaran" => "Gelisah"],
            ["kesadaran" => "Bicara Tidak Jelas"],

        ],

        "nyeriDada" => "",
        "nyeriDadaOptions" => [
            ["nyeriDada" => "Tidak Ada"],
            ["nyeriDada" => "Ada"],
        ],

        "nyeriDadaTingkat" => "",
        "nyeriDadaTingkatOptions" => [
            ["nyeriDadaTingkat" => "Ringan"],
            ["nyeriDadaTingkat" => "Sedang"],
            ["nyeriDadaTingkat" => "Berat"],

        ],

        "prioritasPelayanan" => "",
        "prioritasPelayananOptions" => [
            ["prioritasPelayanan" => "Preventif"],
            ["prioritasPelayanan" => "Paliatif"],
            ["prioritasPelayanan" => "Kuaratif"],
            ["prioritasPelayanan" => "Rehabilitatif"],


        ],
        "tanggalPelayanan" => "",
        "petugasPelayanan" => "",

    ];
    //////////////////////////////////////////////////////////////////////
    protected $rules = [
        'dataDaftarUgd.screening.keluhanUtama' => 'required',
        'dataDaftarUgd.screening.pernafasan' => 'required',
        'dataDaftarUgd.screening.kesadaran' => 'required',
        'dataDaftarUgd.screening.nyeriDada' => 'required',
        'dataDaftarUgd.screening.tanggalPelayanan' => 'required|date_format:d/m/Y H:i:s',

        'dataDaftarUgd.screening.prioritasPelayanan' => 'required',

        'dataDaftarUgd.screening.tanggalPelayanan' => 'required',
        'dataDaftarUgd.screening.petugasPelayanan' => 'required',

    ];

    protected $messages = [
        'required' => ':attribute wajib diisi.',
    ];
    protected $attributes = [
        'dataDaftarUgd.screening.keluhanUtama'     => 'Keluhan utama',
        'dataDaftarUgd.screening.pernafasan'       => 'Pernafasan',
        'dataDaftarUgd.screening.kesadaran'        => 'Kesadaran',
        'dataDaftarUgd.screening.nyeriDada'        => 'Nyeri dada',
        'dataDaftarUgd.screening.prioritasPelayanan' => 'Prioritas pelayanan',
        'dataDaftarUgd.screening.tanggalPelayanan' => 'Tanggal pelayanan',
        'dataDaftarUgd.screening.petugasPelayanan' => 'Petugas pelayanan',
    ];





    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName, $this->rules, $this->messages, $this->attributes);
    }


    // ////////////////
    // RJ Logic
    // ////////////////


    // insert and update record start////////////////
    public function store()
    {
        if (!$this->checkUgdStatus()) return;

        $this->validate($this->rules, $this->messages, $this->attributes);

        $rjNo = $this->dataDaftarUgd['rjNo'] ?? $this->rjNoRef ?? null;
        if (!$rjNo) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('rjNo kosong.');
            return;
        }

        $lockKey = "ugd:{$rjNo}";
        try {
            Cache::lock($lockKey, 5)->block(3, function () use ($rjNo) {
                DB::transaction(function () use ($rjNo) {
                    $fresh = $this->findDataUGD($rjNo) ?: [];

                    if (!isset($fresh['screening']) || !is_array($fresh['screening'])) {
                        $fresh['screening'] = $this->screening;
                    }

                    // patch hanya subtree screening (gabungkan default -> existing -> edited)
                    $fresh['screening'] = array_replace_recursive(
                        $this->screening,
                        (array)($fresh['screening'] ?? []),
                        (array)($this->dataDaftarUgd['screening'] ?? [])
                    );

                    $this->updateJsonUGD($rjNo, $fresh);
                    $this->dataDaftarUgd = $fresh;
                });
            });

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addSuccess('Screening berhasil disimpan.');
        } catch (LockTimeoutException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Sistem sibuk, gagal memperoleh lock. Coba lagi.');
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Gagal menyimpan Screening.');
        }
    }




    private function findData($rjno): void
    {
        $this->dataDaftarUgd = $this->findDataUGD($rjno) ?: [];
        $current = (array)($this->dataDaftarUgd['screening'] ?? []);
        $this->dataDaftarUgd['screening'] = array_replace_recursive($this->screening, $current);
    }

    private function checkUgdStatus(): bool
    {
        $row = DB::table('rstxn_ugdhdrs')->select('rj_status')
            ->where('rj_no', $this->rjNoRef)->first();

        if (!$row || $row->rj_status !== 'A') {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Pasien Sudah Pulang, Transaksi Terkunci.');
            return false;
        }
        return true;
    }


    // when new form instance
    public function mount()
    {

        $this->findData($this->rjNoRef);
    }



    // select data start////////////////
    public function render()
    {

        return view(
            'livewire.emr-u-g-d.mr-u-g-d.anamnesa.anamnesa.screening',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Anamnesa',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Pasien Rawat Jalan',
            ]
        );
    }
    // select data end////////////////


}
