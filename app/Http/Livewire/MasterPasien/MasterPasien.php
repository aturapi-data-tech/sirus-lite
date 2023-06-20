<?php

namespace App\Http\Livewire\MasterPasien;

use App\Models\Pasien;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class MasterPasien extends Component
{
    use WithPagination;

    //  table data////////////////
    public
        $reg_no,
        $reg_name,
        $nik_bpjs,
        $nokartu_bpjs,
        $no_jkn,
        $sex,
        $thn,
        $bln,
        $hari,
        $birth_date,
        $birth_place,
        $blood,
        $marital_status,
        $rel_id,
        $rel_desc,
        $edu_id,
        $edu_desc,
        $job_id,
        $job_name,
        $kk,
        $nyonya,
        $no_kk,
        $address,
        $des_id,
        $des_name,
        $rt,
        $rw,
        $kec_id,
        $kec_name,
        $kab_id,
        $kab_name,
        $prop_id,
        $prop_name,
        $phone;

    // public $dataPasien = [
    //     'reg_no' => "",
    //     'reg_name' => "",
    //     'nik_bpjs' => "",
    //     'no_jkn' => "",
    //     'sex' => "",
    //     'birth_date' => "",
    //     'birth_place' => "",
    //     'blood' => "",
    //     'rel_id' => "",
    //     'edu_id' => "",
    //     'job_id' => "",
    //     'kk' => "",
    //     'nyonya' => "",
    //     'no_kk' => "",
    //     'address' => "",
    //     'des_id' => "",
    //     'rt' => "",
    //     'rw' => "",
    //     'kec_id' => "",
    //     'kab_id' => "",
    //     'prop_id' => "",
    //     'phone' => "",
    // ];



    // Lov Desa
    public $desLov = []; // Menampilkan data yg kita cari
    public $desLovStatus = 0;
    // End Lov Desa

    // Lov Kecamatan
    public $kecLov = []; // Menampilkan data yg kita cari
    public $kecLovStatus = 0;
    // End Lov Kecamatan

    // Lov Kabupaten
    public $kabLov = []; // Menampilkan data yg kita cari
    public $kabLovStatus = 0;
    // End Lov Kabupaten

    // Lov Provinsi
    public $propLov = []; // Menampilkan data yg kita cari
    public $propLovStatus = 0;
    // End Lov Provinsi

    // Lov Agama
    // Table LOV
    public $relLov = []; // Menampilkan data yg kita cari
    public $relLovStatus = 0;
    // End Lov Agama

    // Lov Pendidikan
    public $eduLov = []; // Menampilkan data yg kita cari
    public $eduLovStatus = 0;
    // End Lov Pendidikan

    // Lov Pekerjaan
    public $jobLov = []; // Menampilkan data yg kita cari
    public $jobLovStatus = 0;
    // End Lov Pekerjaan

    // 

    // limit record per page -resetExcept////////////////
    public $limitPerPage = 10;



    //  modal status////////////////
    public $isOpen = 0;
    public $isOpenMode = 'insert';
    public $tampilIsOpen = 0;


    // search logic -resetExcept////////////////
    public $search;
    protected $queryString = [
        'search' => ['except' => '', 'as' => 'cariData'],
        'page' => ['except' => 1, 'as' => 'p'],
    ];


    // sort logic -resetExcept////////////////
    public $sortField = 'reg_date';
    public $sortAsc = true;


    // listener from blade////////////////
    protected $listeners = [
        'confirm_remove_record_pasien' => 'delete'
    ];




    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////





    // resert input private////////////////
    private function resetInputFields(): void
    {
        $this->reset([
            'reg_no',
            'reg_name',
            'nik_bpjs',
            'nokartu_bpjs',
            'sex',
            'thn',
            'bln',
            'hari',
            'birth_date',
            'birth_place',
            'blood',
            'marital_status',
            'rel_id',
            'rel_desc',
            'edu_id',
            'edu_desc',
            'job_id',
            'job_name',
            'kk',
            'nyonya',
            'no_kk',
            'address',
            'des_id',
            'des_name',
            'rt',
            'rw',
            'kec_id',
            'kec_name',
            'kab_id',
            'kab_name',
            'prop_id',
            'prop_name',
            'phone',

            'desLov',
            'desLovStatus',
            'kecLov',
            'kecLovStatus',
            'kabLov',
            'kabLovStatus',
            'propLov',
            'propLovStatus',
            'relLov',
            'relLovStatus',
            'eduLov',
            'eduLovStatus',
            'jobLov',
            'jobLovStatus',
            'isOpen',
            'tampilIsOpen',
            'isOpenMode'
        ]);
    }




    // open and close modal start////////////////
    private function openModal(): void
    {
        $this->resetInputFields();
        $this->isOpen = true;
        $this->isOpenMode = 'insert';
    }
    private function openModalEdit(): void
    {
        $this->resetInputFields();
        $this->isOpen = true;
        $this->isOpenMode = 'update';
    }

    private function openModalTampil(): void
    {
        $this->resetInputFields();
        $this->isOpen = true;
        $this->isOpenMode = 'tampil';
    }

    public function closeModal(): void
    {
        $this->resetInputFields();
    }
    // open and close modal end////////////////




    // setLimitPerpage////////////////
    public function setLimitPerPage($value)
    {
        $this->limitPerPage = $value;
    }




    // resert page pagination when coloumn search change ////////////////
    public function updatedSearch(): void
    {
        $this->resetPage();
    }




    // logic ordering record (shotby)////////////////
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortAsc = !$this->sortAsc;
        } else {
            $this->sortAsc = true;
        }

        $this->sortField = $field;
    }



    // is going to insert data////////////////
    public function create()
    {
        $this->openModal();
    }



    // insert record start////////////////
    public function store()
    {
        $this->validate(
            [
                'reg_no' => 'required',
                'reg_name' => 'required',
                'nik_bpjs' => 'required',
                'nokartu_bpjs' => 'required',
                'sex' => 'required',
                'thn' => 'required',
                'bln' => 'required',
                'hari' => 'required',
                'birth_date' => 'required',
                'birth_place' => 'required',
                'blood' => 'required',
                'marital_status' => 'required',
                'rel_id' => 'required',
                'edu_id' => 'required',
                'job_id' => 'required',
                'kk' => 'required',
                'nyonya' => 'required',
                'no_kk' => 'required',
                'address' => 'required',
                'des_id' => 'required',
                'rt' => 'required',
                'rw' => 'required',
                'kec_id' => 'required',
                'kab_id' => 'required',
                'prop_id' => 'required',
                'phone' => 'required'
            ]
        );


        Pasien::updateOrCreate(['reg_no' => $this->reg_no], [
            'reg_name' => $this->reg_name,
            'nik_bpjs' => $this->nik_bpjs,
            'nokartu_bpjs' => $this->nokartu_bpjs,
            'sex' => $this->sex,
            'thn' => $this->thn,
            'bln' => $this->bln,
            'hari' => $this->hari,
            'birth_date' => DB::raw("to_date( '" . $this->birth_date . "', 'DD/MM/YYYY')"),
            'birth_place' => $this->birth_place,
            'blood' => $this->blood,
            'marital_status' => $this->marital_status,
            'rel_id' => $this->rel_id,
            'edu_id' => $this->edu_id,
            'job_id' => $this->job_id,
            'kk' => $this->kk,
            'nyonya' => $this->nyonya,
            'no_kk' => $this->no_kk,
            'address' => $this->address,
            'des_id' => $this->des_id,
            'rt' => $this->rt,
            'rw' => $this->rw,
            'kec_id' => $this->kec_id,
            'kab_id' => $this->kab_id,
            'prop_id' => $this->prop_id,
            'phone' => $this->phone
        ]);


        $this->closeModal();
        $this->resetInputFields();
        $this->emit('toastr-success', "Data " . $this->reg_name . " berhasil disimpan.");
    }
    // insert record end////////////////

    // fungsi custom error message
    private function customErrorMessage()
    {
        return [
            'reg_no.required' => 'Nomor Registrasi tidak boleh kosong',
            'reg_name.required' => 'Nama Tidak Boleh Kosong',
            'nokartu_bpjs.required' => 'No. Kartu BPJS Tidak Boleh Kosong',
            'nik_bpjs.required' => 'NIK Tidak Boleh Kosong',
            'sex.required' => 'Jenis Kelamin Tidak Boleh Kosong',
            'birth_date.required' => 'Tgl. Lahir Tidak Boleh Kosong',
            'birth_place.required' => 'Tempat Lahir Tidak Boleh Kosong',
            'blood.required' => 'Gol. Darah Tidak Boleh Kosong',
            'marital_status.required' => 'Status Tidak Boleh Kosong',
            'rel_id.required' => 'Agama Tidak Boleh Kosong',
            'edu_id.required' => 'Pendidikan Tidak Boleh Kosong',
            'job_id.required' => 'Pekerjaan Tidak Boleh Kosong',
            'kk.required' => 'Kepala Keluarga Tidak Boleh Kosong',
            'nyonya.required' => 'Nyonya Tidak Boleh Kosong',
            'no_kk.required' => 'No. KK Tidak Boleh Kosong',
            'address.required' => 'Alamat Tidak Boleh Kosong',
            'des_id.required' => 'Desa Tidak Boleh Kosong',
            'rt.required' => 'RT Tidak Boleh Kosong',
            'rw.required' => 'RW Tidak Boleh Kosong',
            'kec_id.required' => 'Kecamatan Tidak Boleh Kosong',
            'kab_id.required' => 'Kabupaten Tidak Boleh Kosong',
            'prop_id.required' => 'Provinsi Tidak Boleh Kosong',
            'phone.required' => 'No. HP Tidak Boleh Kosong'
        ];
    }

    // end fungsi message


    // Find data from table start////////////////
    private function findData($value)
    {
        $findData = DB::table('rsmst_pasiens')
            ->select(
                'reg_no',
                'reg_name',
                'nik_bpjs',
                'nokartu_bpjs',
                'sex',
                'thn',
                'bln',
                'hari',
                DB::raw("to_char(birth_date, 'DD/MM/YYYY') as birth_date"),
                'birth_place',
                'blood',
                'marital_status',
                'kk',
                'nyonya',
                'no_kk',
                'address',
                'rt',
                'rw',
                'phone',
                'des_name',
                'kec_name',
                'kab_name',
                'prop_name',
                'rel_desc',
                'edu_desc',
                'job_name',
                'rsmst_desas.des_id',
                'rsmst_kecamatans.kec_id',
                'rsmst_kabupatens.kab_id',
                'rsmst_propinsis.prop_id',
                'rsmst_religions.rel_id',
                'rsmst_educations.edu_id',
                'rsmst_jobs.job_id'
            )
            ->join('rsmst_desas', 'rsmst_desas.des_id', 'rsmst_pasiens.des_id')
            ->join('rsmst_kecamatans', 'rsmst_kecamatans.kec_id', 'rsmst_pasiens.kec_id')
            ->join('rsmst_kabupatens', 'rsmst_kabupatens.kab_id', 'rsmst_pasiens.kab_id')
            ->join('rsmst_propinsis', 'rsmst_propinsis.prop_id', 'rsmst_pasiens.prop_id')
            ->join('rsmst_religions', 'rsmst_religions.rel_id', 'rsmst_pasiens.rel_id')
            ->join('rsmst_educations', 'rsmst_educations.edu_id', 'rsmst_pasiens.edu_id')
            ->join('rsmst_jobs', 'rsmst_jobs.job_id', 'rsmst_pasiens.job_id')


            ->where('reg_no', $value)->first();
        return $findData;
    }
    // Find data from table end////////////////



    // show edit record start////////////////
    public function edit($id)
    {
        $this->openModalEdit();

        $pasien = $this->findData($id);

        $this->reg_no = $id;
        $this->reg_name = $pasien->reg_name;
        $this->nik_bpjs = $pasien->nik_bpjs;
        $this->nokartu_bpjs = $pasien->nokartu_bpjs;
        $this->sex = $pasien->sex;
        $this->thn = $pasien->thn;
        $this->bln = $pasien->bln;
        $this->hari = $pasien->hari;
        $this->birth_date = $pasien->birth_date;
        $this->birth_place = $pasien->birth_place;
        $this->blood = $pasien->blood;
        $this->marital_status = $pasien->marital_status;
        $this->rel_id = $pasien->rel_id;
        $this->rel_desc = $pasien->rel_desc;
        $this->edu_id = $pasien->edu_id;
        $this->edu_desc = $pasien->edu_desc;
        $this->job_id = $pasien->job_id;
        $this->job_name = $pasien->job_name;
        $this->kk = $pasien->kk;
        $this->nyonya = $pasien->nyonya;
        $this->no_kk = $pasien->no_kk;
        $this->address = $pasien->address;
        $this->des_id = $pasien->des_id;
        $this->des_name = $pasien->des_name;
        $this->rt = $pasien->rt;
        $this->rw = $pasien->rw;
        $this->kec_id = $pasien->kec_id;
        $this->kec_name = $pasien->kec_name;
        $this->kab_id = $pasien->kab_id;
        $this->kab_name = $pasien->kab_name;
        $this->prop_id = $pasien->prop_id;
        $this->prop_name = $pasien->prop_name;
        $this->phone = $pasien->phone;
    }
    // show edit record end////////////////



    // tampil record start////////////////
    public function tampil($id)
    {
        $this->openModalTampil();

        $pasien = $this->findData($id);
        $this->reg_no = $id;
        // $this->reg_name = $pasien->reg_name;
        // $this->nik_bpjs = $pasien->nik_bpjs;
        // $this->nokartu_bpjs = $pasien->nokartu_bpjs;
        // $this->sex = $pasien->sex;
        // $this->thn = $pasien->thn;
        // $this->bln = $pasien->bln;
        // $this->hari = $pasien->hari;
        // $this->birth_date = $pasien->birth_date;
        // $this->birth_place = $pasien->birth_place;
        // $this->blood = $pasien->blood;
        // $this->marital_status = $pasien->marital_status;
        // $this->rel_id = $pasien->rel_id;
        // $this->rel_desc = $pasien->rel_desc;
        // $this->edu_id = $pasien->edu_id;
        // $this->job_id = $pasien->job_id;
        // $this->kk = $pasien->kk;
        // $this->nyonya = $pasien->nyonya;
        // $this->no_kk = $pasien->no_kk;
        // $this->address = $pasien->address;
        // $this->des_id = $pasien->des_id;
        // $this->kec_id = $pasien->kec_id;
        // $this->kab_id = $pasien->kab_id;
        // $this->prop_id = $pasien->prop_id;
        // $this->phone = $pasien->phone;
        // $this->emit('toastr-success', "Data " . $this->reg_name . " berhasil disimpan.");
    }
    // tampil record end////////////////



    // delete record start////////////////
    public function delete($reg_no, $reg_name)
    {
        Pasien::find($reg_no)->delete();
        $this->emit('toastr-success', "Hapus data " . $reg_name . " berhasil.");
    }
    // delete record end////////////////


    /////////////////////////////////////////////////////////////////   LOGIC LOV   ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // logic LOV Desa start////////////////
    public function updatedDesid()
    {
        // dd('x');
        // check LOV by id 
        $desa = DB::table('rsmst_desas')
            ->select('des_id', 'des_name')
            ->where(DB::raw("to_char(des_id)"), $this->des_id)
            ->first();
        if ($desa) {
            $this->des_id = $desa->des_id;
            $this->des_name = $desa->des_name;
            $this->desLovStatus = false;
        } else {
            // if there is no id found and check (min 3 char on search)
            if (strlen($this->des_id) < 3) {
                $this->desLov = [];
            } else {
                $this->desLov = DB::table('rsmst_desas')
                    ->select('des_id', 'des_name')
                    ->where('des_name', 'like', '%' . $this->des_id . '%')
                    ->orderBy('des_name', 'asc')->get();
            }
            $this->desLovStatus = true;
            $this->des_name = '';
        }
    }
    // /////////////////////

    // /////////////////////
    // LOV selected start
    public function setMyDesLov($des_id, $des_name)
    {
        $this->des_id = $des_id;
        $this->des_name = $des_name;
        $this->desLovStatus = 0;
    }
    // LOV selected end
    // /////////////////////

    // logic LOV Desa end



    // logic LOV Kecamatan start////////////////
    public function updatedKecid()
    {
        // dd('x');
        // check LOV by id 
        $kecamatan = DB::table('rsmst_kecamatans')
            ->select('kec_id', 'kec_name')
            ->where(DB::raw("to_char(kec_id)"), $this->kec_id)
            ->first();
        if ($kecamatan) {
            $this->kec_id = $kecamatan->kec_id;
            $this->kec_name = $kecamatan->kec_name;
            $this->kecLovStatus = false;
        } else {
            // if there is no id found and check (min 3 char on search)
            if (strlen($this->kec_id) < 3) {
                $this->kecLov = [];
            } else {
                $this->kecLov = DB::table('rsmst_kecamatans')
                    ->select('kec_id', 'kec_name')
                    ->where('kec_name', 'like', '%' . $this->kec_id . '%')
                    ->orderBy('kec_name', 'asc')->get();
            }
            $this->kecLovStatus = true;
            $this->kec_name = '';
        }
    }
    // /////////////////////

    // /////////////////////
    // LOV selected start
    public function setMyKecLov($kec_id, $kec_name)
    {
        $this->kec_id = $kec_id;
        $this->kec_name = $kec_name;
        $this->kecLovStatus = 0;
    }
    // LOV selected end
    // /////////////////////

    // logic LOV Kecamatan end



    // logic LOV Kabupaten start////////////////
    public function updatedKabid()
    {
        // dd('x');
        // check LOV by id 
        $kabupaten = DB::table('rsmst_kabupatens')
            ->select('kab_id', 'kab_name')
            ->where(DB::raw("to_char(kab_id)"), $this->kab_id)
            ->first();
        if ($kabupaten) {
            $this->kab_id = $kabupaten->kab_id;
            $this->kab_name = $kabupaten->kab_name;
            $this->kabLovStatus = false;
        } else {
            // if there is no id found and check (min 3 char on search)
            if (strlen($this->kab_id) < 3) {
                $this->kabLov = [];
            } else {
                $this->kabLov = DB::table('rsmst_kabupatens')
                    ->select('kab_id', 'kab_name')
                    ->where('kab_name', 'like', '%' . $this->kab_id . '%')
                    ->orderBy('kab_name', 'asc')->get();
            }
            $this->kabLovStatus = true;
            $this->kab_name = '';
        }
    }
    // /////////////////////

    // /////////////////////
    // LOV selected start
    public function setMyKabLov($kab_id, $kab_name)
    {
        $this->kab_id = $kab_id;
        $this->kab_name = $kab_name;
        $this->kabLovStatus = 0;
    }
    // LOV selected end
    // /////////////////////

    // logic LOV Kabupaten end



    // logic LOV Provinsi start////////////////
    public function updatedPropid()
    {
        // dd('x');
        // check LOV by id 
        $provinsi = DB::table('rsmst_propinsis')
            ->select('prop_id', 'prop_name')
            ->where(DB::raw("to_char(prop_id)"), $this->prop_id)
            ->first();
        if ($provinsi) {
            $this->prop_id = $provinsi->prop_id;
            $this->prop_name = $provinsi->prop_name;
            $this->propLovStatus = false;
        } else {
            // if there is no id found and check (min 3 char on search)
            if (strlen($this->prop_id) < 3) {
                $this->propLov = [];
            } else {
                $this->propLov = DB::table('rsmst_propinsis')
                    ->select('prop_id', 'prop_name')
                    ->where('prop_name', 'like', '%' . $this->prop_id . '%')
                    ->orderBy('prop_name', 'asc')->get();
            }
            $this->propLovStatus = true;
            $this->prop_name = '';
        }
    }
    // /////////////////////

    // /////////////////////
    // LOV selected start
    public function setMyPropLov($prop_id, $prop_name)
    {
        $this->prop_id = $prop_id;
        $this->prop_name = $prop_name;
        $this->propLovStatus = 0;
    }
    // LOV selected end
    // /////////////////////

    // logic LOV Provinsi end



    // logic LOV Agama start////////////////
    public function updatedRelid()
    {
        // dd('x');
        // check LOV by id 
        $religion = DB::table('rsmst_religions')
            ->select('rel_id', 'rel_desc')
            ->where(DB::raw("to_char(rel_id)"), $this->rel_id)
            ->first();
        if ($religion) {
            $this->rel_id = $religion->rel_id;
            $this->rel_desc = $religion->rel_desc;
            $this->relLovStatus = false;
        } else {
            // if there is no id found and check (min 3 char on search)
            if (strlen($this->rel_id) < 3) {
                $this->relLov = [];
            } else {
                $this->relLov = DB::table('rsmst_religions')
                    ->select('rel_id', 'rel_desc')
                    ->where('rel_desc', 'like', '%' . $this->rel_id . '%')
                    ->orderBy('rel_desc', 'asc')->get();
            }
            $this->relLovStatus = true;
            $this->rel_desc = '';
        }
    }
    // /////////////////////

    // /////////////////////
    // LOV selected start
    public function setMyRelLov($rel_id, $rel_desc)
    {
        $this->rel_id = $rel_id;
        $this->rel_desc = $rel_desc;
        $this->relLovStatus = 0;
    }
    // LOV selected end
    // /////////////////////

    // logic LOV Agama end



    // logic LOV Pendidikan start////////////////
    public function updatedEduid()
    {
        // dd('x');
        // check LOV by id 
        $education = DB::table('rsmst_educations')
            ->select('edu_id', 'edu_desc')
            ->where(DB::raw("to_char(edu_id)"), $this->edu_id)
            ->first();
        if ($education) {
            $this->edu_id = $education->edu_id;
            $this->edu_desc = $education->edu_desc;
            $this->eduLovStatus = false;
        } else {
            // if there is no id found and check (min 3 char on search)
            if (strlen($this->edu_id) < 3) {
                $this->eduLov = [];
            } else {
                $this->eduLov = DB::table('rsmst_educations')
                    ->select('edu_id', 'edu_desc')
                    ->where('edu_desc', 'like', '%' . $this->edu_id . '%')
                    ->orderBy('edu_desc', 'asc')->get();
            }
            $this->eduLovStatus = true;
            $this->edu_desc = '';
        }
    }
    // /////////////////////

    // /////////////////////
    // LOV selected start
    public function setMyEduLov($edu_id, $edu_desc)
    {
        $this->edu_id = $edu_id;
        $this->edu_desc = $edu_desc;
        $this->eduLovStatus = 0;
    }
    // LOV selected end
    // /////////////////////

    // logic LOV Pendidikan end




    // logic LOV Pekerjaan start////////////////
    public function updatedJobid()
    {
        // dd('x');
        // check LOV by id 
        $job = DB::table('rsmst_jobs')
            ->select('job_id', 'job_name')
            ->where(DB::raw("to_char(job_id)"), $this->job_id)
            ->first();
        if ($job) {
            $this->job_id = $job->job_id;
            $this->job_name = $job->job_name;
            $this->jobLovStatus = false;
        } else {
            // if there is no id found and check (min 3 char on search)
            if (strlen($this->job_id) < 3) {
                $this->jobLov = [];
            } else {
                $this->jobLov = DB::table('rsmst_jobs')
                    ->select('job_id', 'job_name')
                    ->where('job_name', 'like', '%' . $this->job_id . '%')
                    ->orderBy('job_name', 'asc')->get();
            }
            $this->jobLovStatus = true;
            $this->job_name = '';
        }
    }
    // /////////////////////

    // /////////////////////
    // LOV selected start
    public function setMyJobLov($job_id, $job_name)
    {
        $this->job_id = $job_id;
        $this->job_name = $job_name;
        $this->jobLovStatus = 0;
    }
    // LOV selected end
    // /////////////////////

    // logic LOV Pekerjaan end




    ////////////////////////////////////////////////////////////////////////    END LOGIC LOV   ////////////////////////////////////////////////////////////////////////////////////////////////////////////




    // select data start////////////////
    public function render()
    {
        $sql = "SELECT * FROM rsmst_pasiens WHERE rownum <= 5";
        return view(
            'livewire.master-pasien.master-pasien',
            [
                'pasiens' => DB::table('rsmst_pasiens')
                    ->select(
                        'reg_no',
                        'reg_name',
                        'nik_bpjs',
                        'nokartu_bpjs',
                        'sex',
                        'thn',
                        'bln',
                        'hari',
                        DB::raw("to_char(birth_date, 'DD-MM-YYYY') as birth_date"),
                        'birth_place',
                        'blood',
                        'marital_status',
                        'kk',
                        'nyonya',
                        'no_kk',
                        'address',
                        'rt',
                        'rw',
                        'phone',
                        'des_name',
                        'kec_name',
                        'kab_name',
                        'prop_name',
                        'rel_desc',
                        'edu_desc',
                        'job_name',
                        'rsmst_desas.des_id',
                        'rsmst_kecamatans.kec_id',
                        'rsmst_kabupatens.kab_id',
                        'rsmst_propinsis.prop_id',
                        'rsmst_religions.rel_id',
                        'rsmst_educations.edu_id',
                        'rsmst_jobs.job_id'
                    )
                    ->join('rsmst_desas', 'rsmst_desas.des_id', 'rsmst_pasiens.des_id')
                    ->join('rsmst_kecamatans', 'rsmst_kecamatans.kec_id', 'rsmst_pasiens.kec_id')
                    ->join('rsmst_kabupatens', 'rsmst_kabupatens.kab_id', 'rsmst_pasiens.kab_id')
                    ->join('rsmst_propinsis', 'rsmst_propinsis.prop_id', 'rsmst_pasiens.prop_id')
                    ->join('rsmst_religions', 'rsmst_religions.rel_id', 'rsmst_pasiens.rel_id')
                    ->join('rsmst_educations', 'rsmst_educations.edu_id', 'rsmst_pasiens.edu_id')
                    ->join('rsmst_jobs', 'rsmst_jobs.job_id', 'rsmst_pasiens.job_id')


                    // ->where('reg_name', 'like', '%' . $this->search . '%')
                    // ->orWhere('reg_no', 'like', '%' . $this->search . '%')
                    // ->orWhere('address', 'like', '%' . $this->search . '%')
                    // ->orWhere('birth_place', 'like', '%' . $this->search . '%')

                    ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
                    ->paginate($this->limitPerPage),
                'myTitle' => 'Master Pasien',
                'mySnipt' => 'Tambah Data Master Pasien',
                'myProgram' => 'Pasien',
                'myLimitPerPages' => [5, 10, 15, 20, 100]
            ]
        );
    }
    // select data end////////////////
}
