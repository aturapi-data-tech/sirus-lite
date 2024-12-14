<?php

namespace App\Http\Livewire\MasterDokter\FormEntryTemplateResepDokter;

use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Validator;

use App\Http\Traits\customErrorMessagesTrait;


use Livewire\Component;

class FormEntryTemplateResepDokter extends Component
{
    // listener from blade////////////////

    public string $dokterId = '';
    public string $temprId = '';

    public string $isOpenMode = 'insert';

    public array $FormEntry = [];

    public string $activeTabRacikanNonRacikan = 'NonRacikan';
    public array $EmrMenuRacikanNonRacikan = [
        [
            'ermMenuId' => 'NonRacikan',
            'ermMenuName' => 'NonRacikan',
        ],
        [
            'ermMenuId' => 'Racikan',
            'ermMenuName' => 'Racikan',
        ],
    ];



    // rules///////////////////
    protected $rules = [
        'FormEntry.dokterId' => 'required|numeric|digits_between:1,3',
        'FormEntry.temprId' => 'required',
        'FormEntry.temprDesc' => 'required',
    ];

    protected $messages = [];

    protected $validationAttributes = [
        'FormEntry.dokterId' => 'Kode Dokter',
        'FormEntry.temprId' => 'Template Resep Id',
        'FormEntry.temprDesc' => 'Keterangan Template Resep',
    ];
    // rules///////////////////





    public function closeModal(): void
    {
        $this->emit('masterDokterCloseModal');
    }

    private function findData($dokterId): void
    {
        // $dokterId
        // belokkan data  $dokterId agar ketika di buka data selalu berstatus insert
        $dokterId = '';

        try {
            $findData = DB::table('rsmst_doctortempreseps')
                ->where('dr_id', $dokterId)
                ->first();


            if ($findData) {
                $this->FormEntry = [
                    'dokterId' => $findData->dr_id,
                    'temprId' => $findData->tempr_id,
                    'temprDesc' => $findData->tempr_desc,
                    'tempJsonRacikan' => $findData->temp_json_racikan,
                    'tempJsonNonRacikan' => $findData->temp_json_nonracikan,


                ];

                $this->isOpenMode = 'update';
            } else {
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Data tidak ditemukan.");
                $this->FormEntry = [
                    'dokterId' => $dokterId,
                    'temprId' => null,
                    'temprDesc' => null,
                    'tempJsonRacikan' => null,
                    'tempJsonNonRacikan' => null,

                ];
                $this->isOpenMode = 'insert';
            }
        } catch (Exception $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError($e->getMessage());
            $this->FormEntry = [
                'dokterId' => $dokterId,
                'temprId' => null,
                'temprDesc' => null,
                'tempJsonRacikan' => null,
                'tempJsonNonRacikan' => null,

            ];
            $this->isOpenMode = 'insert';
        }
    }

    private function update($dokterId): void
    {
        // update table trnsaksi
        DB::table('rsmst_doctortempreseps')
            ->where('dr_id', $dokterId)
            ->update([
                'dr_id' => isset($this->dokterId) ? $this->dokterId : null,
                'tempr_id' => isset($this->FormEntry['temprId']) ? $this->FormEntry['temprId'] : '',
                'tempr_desc' => isset($this->FormEntry['temprDesc']) ? $this->FormEntry['temprDesc'] : '',
            ]);

        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Data berhasil diupdate.");
    }

    private function insert(): void
    {
        // update table trnsaksi
        DB::table('rsmst_doctortempreseps')
            ->insert([
                'dr_id' => isset($this->dokterId) ? $this->dokterId : null,
                'tempr_id' => isset($this->FormEntry['temprId']) ? $this->FormEntry['temprId'] : '',
                'tempr_desc' => isset($this->FormEntry['temprDesc']) ? $this->FormEntry['temprDesc'] : '',
            ]);

        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Data berhasil dimasukkan.");
    }

    public function store()
    {

        // validate
        $this->validateData();

        // Jika mode data //insert
        if ($this->isOpenMode == 'insert') {
            $this->insert();
            $this->isOpenMode = 'update';
        } else {
            // Jika mode data //update
            $this->update($this->dokterId);
        }

        // $this->closeModal();
    }


    // validate Data RJ//////////////////////////////////////////////////
    private function validateData(): void
    {
        // Proses Validasi///////////////////////////////////////////
        try {

            // tambahkan exists counstrain
            if ($this->isOpenMode == 'insert') {
                $this->rules['FormEntry.dokterId'] = 'numeric|digits_between:1,3';
                // $this->rules['FormEntry.dokterId'] = 'required|numeric|digits_between:1,3|exists:rsmst_doctors,dr_id';

            }

            $this->validate($this->rules, customErrorMessagesTrait::messages());
        } catch (\Illuminate\Validation\ValidationException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError($e->getMessage());
            $this->validate($this->rules, customErrorMessagesTrait::messages());
        }
    }

    public function setTemprId($id): void
    {
        $this->temprId = $id;
        $this->emit('syncronizeTemplateResepDokterFindData',  $this->dokterId, $this->temprId);


        // $this->emit('syncronizeTemplateResepDokterFindData');
    }

    public function delete($temprId, $drId): void
    {
        // Proses Validasi///////////////////////////////////////////
        $r = ['dokterId' => $drId, 'temprId' => $temprId];
        $rules = ['dokterId' => 'required', 'temprId' => 'required'];
        $customErrorMessagesTrait = customErrorMessagesTrait::messages();
        $customErrorMessagesTrait['unique'] = 'Data :attribute sudah dipakai pada transaksi Rawat Jalan.';
        $attribute = ['dokterId' => 'Dokter', 'temprId' => 'Template Resep'];

        $validator = Validator::make($r, $rules, $customErrorMessagesTrait, $attribute);

        if ($validator->fails()) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError($validator->messages()->all());
            return;
        }
        // Proses Validasi///////////////////////////////////////////

        // delete table trnsaksi
        DB::table('rsmst_doctortempreseps')
            ->where('dr_id', $drId)
            ->where('tempr_id', $temprId)
            ->delete();

        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Data " . $temprId . " berhasil dihapus.");
    }

    public function mount()
    {
        // cek status insert atu delete ada di findData
        $this->findData($this->dokterId);
    }

    public function render()
    {
        // set mySearch
        //////////////////////////////////////////
        // Query ///////////////////////////////
        //////////////////////////////////////////
        $query = DB::table('rsmst_doctortempreseps')
            ->select(
                'dr_id',
                'tempr_id',
                'tempr_desc',
            )
            ->where('dr_id', '=', $this->dokterId)
            ->orderBy('dr_id',  'asc')
            ->orderBy('tempr_desc',  'asc');
        ////////////////////////////////////////////////
        // end Query
        ///////////////////////////////////////////////

        return view('livewire.master-dokter.form-entry-template-resep-dokter.form-entry-template-resep-dokter', ['myQueryData' => $query->paginate(100)]);
    }
}
