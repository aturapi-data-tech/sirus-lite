<?php

namespace App\Http\Livewire\EmrUGD\MrUGD\Suket;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Cache\LockTimeoutException;

use Livewire\Component;
use App\Http\Traits\EmrUGD\EmrUGDTrait;


class Suket extends Component
{
    use EmrUGDTrait;

    // listener from blade////////////////
    protected $listeners = ['emr:ugd:store' => 'store'];

    public $rjNoRef;
    public array $dataDaftarUgd = [];

    public array $suket =
    [
        "suketSehatTab" => "Suket Sehat",
        "suketSehat" => [
            "suketSehat" => ""
        ],
        "suketIstirahatTab" => "Suket Istirahat",
        "suketIstirahat" => [
            "suketIstirahatHari" => "",
            "suketIstirahat" => ""
        ],

    ];
    //////////////////////////////////////////////////////////////////////


    protected $rules = [
        'dataDaftarUgd.suket.suketIstirahat.suketIstirahatHari' => 'nullable|numeric',
    ];

    protected $messages = [
        'numeric' => ':attribute harus berupa angka.',
    ];

    protected $attributes = [
        'dataDaftarUgd.suket.suketIstirahat.suketIstirahatHari' => 'Lama istirahat (hari)',
    ];




    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////
    public function updated($propertyName)
    {
        // validasi ringan per-field (tanpa auto-save tiap ketik)
        if ($propertyName === 'dataDaftarUgd.suket.suketIstirahat.suketIstirahatHari') {
            $this->validateOnly($propertyName, $this->rules, $this->messages, $this->attributes);
        }
    }


    // insert and update record start////////////////
    public function store()
    {

        $rjNo = $this->dataDaftarUgd['rjNo'] ?? $this->rjNoRef ?? null;
        if (!$rjNo) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('rjNo kosong.');
            return;
        }

        // validasi penuh (jika ada rules lain tinggal tambah)
        $this->validate($this->rules, $this->messages, $this->attributes);

        $lockKey = "ugd:{$rjNo}";
        try {
            Cache::lock($lockKey, 5)->block(3, function () use ($rjNo) {
                DB::transaction(function () use ($rjNo) {
                    $fresh = $this->findDataUGD($rjNo) ?: [];

                    // pastikan subtree ada
                    if (!isset($fresh['suket']) || !is_array($fresh['suket'])) {
                        $fresh['suket'] = $this->suket;
                    }

                    // patch hanya subtree suket
                    $fresh['suket'] = array_replace_recursive(
                        $this->suket,
                        (array)($fresh['suket'] ?? []),
                        (array)($this->dataDaftarUgd['suket'] ?? [])
                    );

                    $this->updateJsonUGD($rjNo, $fresh);
                    $this->dataDaftarUgd = $fresh;
                });
            });

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addSuccess("Suket berhasil disimpan.");
        } catch (LockTimeoutException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Sistem sibuk, gagal memperoleh lock. Coba lagi.');
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Gagal menyimpan Suket.');
        }
    }


    // insert and update record end////////////////


    private function findData($rjno): void
    {
        $this->dataDaftarUgd = $this->findDataUGD($rjno) ?: [];
        // siapkan minimal structure tanpa menimpa data existing
        $current = (array)($this->dataDaftarUgd['suket'] ?? []);
        $this->dataDaftarUgd['suket'] = array_replace_recursive($this->suket, $current);
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
            'livewire.emr-u-g-d.mr-u-g-d.suket.suket',
            [
                'myTitle' => 'Pasien UGD',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Pasien Rawat Jalan',
            ]
        );
    }
    // select data end////////////////


}
