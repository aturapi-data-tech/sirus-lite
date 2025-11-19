<?php

namespace App\Http\Livewire\EmrUGD\MrUGD\Anamnesa\Anamnesa;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Livewire\Component;
use App\Http\Traits\EmrUGD\EmrUGDTrait;

class Screening extends Component
{
    use EmrUGDTrait;

    // listener from blade
    protected $listeners = ['emr:ugd:store' => 'store'];

    // Ref on top bar
    public $rjNoRef;
    public array $dataDaftarUgd = [];

    public array $screening = [
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

    protected $rules = [
        'dataDaftarUgd.screening.keluhanUtama' => 'required',
        'dataDaftarUgd.screening.pernafasan' => 'required',
        'dataDaftarUgd.screening.kesadaran' => 'required',
        'dataDaftarUgd.screening.nyeriDada' => 'required',
        'dataDaftarUgd.screening.prioritasPelayanan' => 'required',
        'dataDaftarUgd.screening.tanggalPelayanan' => 'required|date_format:d/m/Y H:i:s',
        'dataDaftarUgd.screening.petugasPelayanan' => 'required',
    ];

    protected $messages = [
        'required' => ':attribute wajib diisi.',
        'date_format' => ':attribute harus dalam format dd/mm/yyyy HH:ii:ss',
    ];

    protected $validationAttributes = [
        'dataDaftarUgd.screening.keluhanUtama' => 'Keluhan utama',
        'dataDaftarUgd.screening.pernafasan' => 'Pernafasan',
        'dataDaftarUgd.screening.kesadaran' => 'Kesadaran',
        'dataDaftarUgd.screening.nyeriDada' => 'Nyeri dada',
        'dataDaftarUgd.screening.prioritasPelayanan' => 'Prioritas pelayanan',
        'dataDaftarUgd.screening.tanggalPelayanan' => 'Tanggal pelayanan',
        'dataDaftarUgd.screening.petugasPelayanan' => 'Petugas pelayanan',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function store(): void
    {
        $this->validate();

        $rjNo = $this->dataDaftarUgd['rjNo'] ?? $this->rjNoRef ?? null;
        if (!$rjNo) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Nomor UGD kosong.');
            return;
        }


        $lockKey = "ugd:{$rjNo}";
        try {
            Cache::lock($lockKey, 5)->block(3, function () use ($rjNo) {
                DB::transaction(function () use ($rjNo) {
                    $fresh = $this->findDataUGD($rjNo) ?: [];

                    // Initialize screening structure if not exists
                    if (!isset($fresh['screening']) || !is_array($fresh['screening'])) {
                        $fresh['screening'] = $this->screening;
                    }

                    // Merge current data with fresh data
                    $fresh['screening'] = array_merge(
                        $fresh['screening'],
                        $this->dataDaftarUgd['screening'] ?? []
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
        } catch (\Exception $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Gagal menyimpan screening: ' . $e->getMessage());
        }
    }

    public function setPetugasPelayanan(): void
    {
        $myUserNameActive = auth()->user()->myuser_name;

        // Check if user has permission to sign screening
        if (!auth()->user()->hasRole(['Perawat', 'Dokter', 'Admin'])) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Anda tidak memiliki wewenang untuk menandatangani screening.');
            return;
        }

        // Set petugas dan tanggal otomatis
        $this->dataDaftarUgd['screening']['petugasPelayanan'] = $myUserNameActive;
        $this->dataDaftarUgd['screening']['tanggalPelayanan'] = now()->format('d/m/Y H:i:s');
    }

    public function autoSetTanggal(): void
    {
        $this->dataDaftarUgd['screening']['tanggalPelayanan'] = now()->format('d/m/Y H:i:s');
    }

    private function findData($rjno): void
    {
        $this->dataDaftarUgd = $this->findDataUGD($rjno) ?: [];

        // Initialize screening data if not exists
        if (!isset($this->dataDaftarUgd['screening']) || !is_array($this->dataDaftarUgd['screening'])) {
            $this->dataDaftarUgd['screening'] = $this->screening;
        }
    }



    public function resetForm(): void
    {
        $this->resetValidation();
        $this->dataDaftarUgd['screening'] = $this->screening;
    }



    public function mount(): void
    {
        $this->findData($this->rjNoRef);
    }

    public function render()
    {
        return view(
            'livewire.emr-u-g-d.mr-u-g-d.anamnesa.anamnesa.screening',
            [
                'myTitle' => 'Screening UGD',
                'mySnipt' => 'Rekam Medis Pasien UGD',
                'myProgram' => 'Pasien UGD',
            ]
        );
    }
}
