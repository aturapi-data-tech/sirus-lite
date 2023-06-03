<?php

namespace App\Http\Livewire\PendaftaranMandiriPasienPoli;

use Livewire\Component;
use App\Models\Regency;
use App\Models\Province;

use App\Models\Pasien;

use Livewire\WithPagination;

class PendaftaranMandiriPasienPoli extends Component
{
    use WithPagination;

    //  table data////////////////
    public $regNo;
    public $dataPasien = [
        "regNo" => "",
        "regName" => "",
        "sex" => "",
        "birthDate" => "",
        "thn" => "",
        "birthPlace" => "",
        "maritalStatus" => "",
        "address" => ""
    ];
    public $steperStatus = 1;




    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////





    // resert input private////////////////
    private function resetInputFields(): void
    {
        $this->reset([
            'dataPasien'
        ]);

    }




    // Find data from table start////////////////
    private function findData($value)
    {
        $findData = Pasien::find($value);
        return $findData;
    }
    // Find data from table end////////////////

    // add SteperStatus number
    public function counterSteper()
    {

        if ($this->dataPasien["regNo"] !== "") {
            $this->steperStatus++;
            $this->emit('toastr-error', "Data Pasien dgn No Reg " . $this->steperStatus . " tidak ditemukan.");
        } else {
            $this->emit('toastr-error', "Data Pasien tidak ditemukan, tempelkan kartu pasien anda ke mesin pemindai.");
        }

    }



    // logic stepper 1 start///// cari data pasien if not resert///////////
    public function cariDataPasien($id)
    {

        $pasien = $this->findData($id);
        if ($pasien) {
            $this->dataPasien["regNo"] = $pasien->reg_no;
            $this->dataPasien["regName"] = $pasien->reg_name;
            $this->dataPasien["sex"] = $pasien->sex;
            $this->dataPasien["birthDate"] = $pasien->birth_date;
            $this->dataPasien["thn"] = $pasien->thn;
            $this->dataPasien["birthPlace"] = $pasien->birth_place;
            $this->dataPasien["maritalStatus"] = $pasien->marital_status;
            $this->dataPasien["address"] = $pasien->address;

        } else {
            $this->resetInputFields();
            $this->emit('toastr-error', "Data Pasien dgn No Reg " . $id . " tidak ditemukan.");
        }

    }

    public function updatedRegNo(): void
    {
        $this->cariDataPasien($this->regNo);

    }
    // logic stepper 1 end////////////////




    // select data start////////////////
    public function render()
    {
        return view(
            'livewire.pendaftaran-mandiri-pasien-poli.pendaftaran-mandiri-pasien-poli',
            [
                'myTitle' => 'Pendaftaran Mandiri',
                'mySnipt' => 'Pasien dapat mendaftar secara mandiri pada setiap poli',
                'myProgram' => 'Poli',
            ]
        );
    }
    // select data end////////////////
}