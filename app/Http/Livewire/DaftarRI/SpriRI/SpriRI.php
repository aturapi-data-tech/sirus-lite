<?php

namespace App\Http\Livewire\DaftarRI\SpriRI;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

use App\Http\Traits\customErrorMessagesTrait;
use App\Http\Traits\BPJS\VclaimTrait;

use App\Http\Traits\EmrRI\EmrRITrait;



class SpriRI extends Component
{
    use WithPagination, EmrRITrait;
    // listener from blade////////////////
    protected $listeners = [
        'syncronizeAssessmentPerawatRIFindData' => 'mount',
        'syncronizeAssessmentPerawatRJFindData' => 'mount'


    ];

    //////////////////////////////
    // Ref on top bar
    //////////////////////////////
    public $riHdrNoRef;



    // dataDaftarRi RJ
    public $dataDaftarRi = [];

    // data SKDP / spri=>[]
    public $spri = [
        'noKontrolRS' => "",
        'noSPRIBPJS' => "",
        'noAntrian' => "",
        'tglKontrol' => "",
        'poliKontrol' => "",
        'poliKontrolBPJS' => "",
        'poliKontrolDesc' => "",
        'drKontrol' => "",
        'drKontrolBPJS' => "",
        'drKontrolDesc' => "",
        'catatan' => "",
        'noKartu' => "",
    ];
    //////////////////////////////////////////////////////////////////////


    //  table LOV////////////////

    public $dataDokterLov = [];
    public $dataDokterLovStatus = 0;
    public $dataDokterLovSearch = '';






    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////





    // resert input private////////////////
    private function resetInputFields(): void
    {

        // resert validation
        $this->resetValidation();
        // resert input kecuali
        $this->resetExcept([
            'riHdrNoRef'
        ]);
    }





    // ////////////////
    // RJ Logic
    // ////////////////

    //////////////////////////////////////////////
    // updated when change Record ////////////////
    ///////////////////////////////////////////////



    //////////////////////////////////////////////
    // updated when change Record ////////////////
    ///////////////////////////////////////////////

    /////////////////////////////////////////////////
    // Lov dataDokterRJ //////////////////////
    ////////////////////////////////////////////////
    public function clickdataDokterlov()
    {
        $this->dataDokterLovStatus = true;
        $this->dataDokterLov = [];
    }

    public function updateddataDokterlovsearch()
    {
        // Variable Search
        $search = $this->dataDokterLovSearch;

        // check LOV by dr_id rs id
        $dataDokter = DB::table('rsmst_doctors')->select(
            'rsmst_doctors.dr_id as dr_id',
            'rsmst_doctors.dr_name as dr_name',
            'kd_dr_bpjs',

            'rsmst_polis.poli_id as poli_id',
            'rsmst_polis.poli_desc as poli_desc',
            'kd_poli_bpjs'
        )
            ->Join('rsmst_polis', 'rsmst_polis.poli_id', 'rsmst_doctors.poli_id')
            ->where('rsmst_doctors.dr_id', $search)
            ->where('rsmst_doctors.active_status', '1')

            ->first();

        if ($dataDokter) {
            $this->dataDaftarRi['spri']['drKontrol'] = $dataDokter->dr_id;
            $this->dataDaftarRi['spri']['drKontrolDesc'] = $dataDokter->dr_name;

            $this->dataDaftarRi['spri']['poliKontrol'] = $dataDokter->poli_id;
            $this->dataDaftarRi['spri']['poliKontrolDesc'] = $dataDokter->poli_desc;

            $this->dataDaftarRi['spri']['drKontrolBPJS'] = $dataDokter->kd_dr_bpjs;
            $this->dataDaftarRi['spri']['poliKontrolBPJS'] = $dataDokter->kd_poli_bpjs;

            $this->dataDokterLovStatus = false;
            $this->dataDokterLovSearch = '';
        } else {
            // if there is no id found and check (min 3 char on search)
            if (strlen($search) < 3) {
                $this->dataDokterLov = [];
            } else {
                $this->dataDokterLov = json_decode(
                    DB::table('rsmst_doctors')->select(
                        'rsmst_doctors.dr_id as dr_id',
                        'rsmst_doctors.dr_name as dr_name',
                        'kd_dr_bpjs',

                        'rsmst_polis.poli_id as poli_id',
                        'rsmst_polis.poli_desc as poli_desc',
                        'kd_poli_bpjs'

                    )
                        ->Join('rsmst_polis', 'rsmst_polis.poli_id', 'rsmst_doctors.poli_id')

                        ->where('rsmst_doctors.active_status', '1')
                        ->Where(DB::raw('upper(dr_name)'), 'like', '%' . strtoupper($search) . '%')
                        ->orWhere('poli_desc', 'like', '%' . strtoupper($search) . '%')
                        ->limit(10)
                        ->orderBy('dr_name', 'ASC')
                        ->orderBy('poli_desc', 'ASC')
                        ->get(),
                    true
                );
            }
            $this->dataDokterLovStatus = true;
            $this->dataDaftarRi['spri']['drKontrol'] = "";
            $this->dataDaftarRi['spri']['drKontrolDesc'] = "";

            $this->dataDaftarRi['spri']['poliKontrol'] = "";
            $this->dataDaftarRi['spri']['poliKontrolDesc'] = "";

            $this->dataDaftarRi['spri']['drKontrolBPJS'] = "";
            $this->dataDaftarRi['spri']['poliKontrolBPJS'] = "";
        }
    }
    // /////////////////////
    // LOV selected start
    public function setMydataDokterLov($id, $name)
    {
        $dataDokter = DB::table('rsmst_doctors')->select(
            'rsmst_doctors.dr_id as dr_id',
            'rsmst_doctors.dr_name as dr_name',
            'kd_dr_bpjs',

            'rsmst_polis.poli_id as poli_id',
            'rsmst_polis.poli_desc as poli_desc',
            'kd_poli_bpjs'
        )
            ->Join('rsmst_polis', 'rsmst_polis.poli_id', 'rsmst_doctors.poli_id')
            ->where('rsmst_doctors.dr_id', $id)
            ->first();
        $this->dataDaftarRi['spri']['drKontrol'] = $dataDokter->dr_id;
        $this->dataDaftarRi['spri']['drKontrolDesc'] = $dataDokter->dr_name;

        $this->dataDaftarRi['spri']['poliKontrol'] = $dataDokter->poli_id;
        $this->dataDaftarRi['spri']['poliKontrolDesc'] = $dataDokter->poli_desc;

        $this->dataDaftarRi['spri']['drKontrolBPJS'] = $dataDokter->kd_dr_bpjs;
        $this->dataDaftarRi['spri']['poliKontrolBPJS'] = $dataDokter->kd_poli_bpjs;

        $this->dataDokterLovStatus = false;
        $this->dataDokterLovSearch = '';
    }
    // LOV selected end
    /////////////////////////////////////////////////
    // Lov dataDokterRJ //////////////////////
    ////////////////////////////////////////////////


    // validate Data RJ//////////////////////////////////////////////////
    private function validateDataSPRI(): void
    {
        // customErrorMessages
        $messages = customErrorMessagesTrait::messages();

        // require nik ketika pasien tidak dikenal



        $rules = [

            "dataDaftarRi.spri.noKontrolRS" => "required",

            "dataDaftarRi.spri.noSPRIBPJS" => "",
            "dataDaftarRi.spri.noAntrian" => "",

            "dataDaftarRi.spri.tglKontrol" => "bail|required|date_format:d/m/Y",

            "dataDaftarRi.spri.drKontrol" => "required",
            "dataDaftarRi.spri.drKontrolDesc" => "required",
            "dataDaftarRi.spri.drKontrolBPJS" => "",


            "dataDaftarRi.spri.poliKontrol" => "required",
            "dataDaftarRi.spri.poliKontrolDesc" => "required",
            "dataDaftarRi.spri.poliKontrolBPJS" => "",

            "dataDaftarRi.spri.catatan" => "",

        ];



        // Proses Validasi///////////////////////////////////////////
        try {
            $this->validate($rules, $messages);
        } catch (\Illuminate\Validation\ValidationException $e) {

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Lakukan Pengecekan kembali Input Data.");
            $this->validate($rules, $messages);
        }
    }


    // insert and update record start////////////////
    public function store()
    {
        // set data RiHdrNo / NoBooking / NoAntrian / klaimId / kunjunganId
        $this->setDataPrimer();

        $this->validateDataSPRI();

        $this->pushSuratSpriBPJS();

        $this->emit('syncronizeAssessmentPerawatRJFindData');
        $this->emit('syncronizeAssessmentPerawatRIFindData');
    }

    private function updateDataRI($riHdrNo): void
    {

        $this->updateJsonRI($riHdrNo, $this->dataDaftarRi);
        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Surat Kontrol berhasil disimpan.");
    }
    // insert and update record end////////////////


    private function findData($riHdrNo): void
    {

        $findData = DB::table('rsview_rihdrs')
            ->join('rsmst_pasiens', 'rsview_rihdrs.reg_no', '=', 'rsmst_pasiens.reg_no')
            ->select(
                'rsview_rihdrs.vno_sep',
                'rsmst_pasiens.nokartu_bpjs',
            )
            ->where('rsview_rihdrs.rihdr_no', $riHdrNo)
            ->first();

        $this->dataDaftarRi = $this->findDataRI($riHdrNo);

        // jika spri tidak ditemukan tambah variable spri pda array
        if (isset($this->dataDaftarRi['spri']) == false) {
            $this->dataDaftarRi['spri']['tglKontrol'] =  Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y');
            $this->dataDaftarRi['spri']['drKontrol'] =  (isset($this->dataDaftarRi['drId'])
                ? ($this->dataDaftarRi['drId']
                    ? $this->dataDaftarRi['drId']
                    : '')
                : '');
            $this->dataDaftarRi['spri']['drKontrolDesc'] = (isset($this->dataDaftarRi['drDesc'])
                ? ($this->dataDaftarRi['drDesc']
                    ? $this->dataDaftarRi['drDesc']
                    : '')
                : '');
            $this->dataDaftarRi['spri']['drKontrolBPJS'] = (isset($this->dataDaftarRi['kddrbpjs'])
                ? ($this->dataDaftarRi['kddrbpjs']
                    ? $this->dataDaftarRi['kddrbpjs']
                    : '')
                : '');
            $this->dataDaftarRi['spri']['poliKontrol'] = (isset($this->dataDaftarRi['poliId'])
                ? ($this->dataDaftarRi['poliId']
                    ? $this->dataDaftarRi['poliId']
                    : '')
                : '');
            $this->dataDaftarRi['spri']['poliKontrolDesc'] = (isset($this->dataDaftarRi['poliDesc'])
                ? ($this->dataDaftarRi['poliDesc']
                    ? $this->dataDaftarRi['poliDesc']
                    : '')
                : '');
            $this->dataDaftarRi['spri']['poliKontrolBPJS'] = (isset($this->dataDaftarRi['kdpolibpjs'])
                ? ($this->dataDaftarRi['kdpolibpjs']
                    ? $this->dataDaftarRi['kdpolibpjs']
                    : '')
                : '');
            $this->dataDaftarRi['spri']['noKartu'] = (isset($findData->nokartu_bpjs)
                ? ($findData->nokartu_bpjs
                    ? $findData->nokartu_bpjs
                    : '')
                : '');
        }
    }

    // set data RiHdrNo / NoBooking / NoAntrian / klaimId / kunjunganId
    private function setDataPrimer(): void
    {
        $noKontrol = Carbon::now(env('APP_TIMEZONE'))->addDays(8)->format('dmY') . $this->dataDaftarRi['spri']['drKontrol'] . $this->dataDaftarRi['spri']['poliKontrol'];
        $this->dataDaftarRi['spri']['noKontrolRS'] = (isset($this->dataDaftarRi['spri']['noKontrolRS'])
            ? ($this->dataDaftarRi['spri']['noKontrolRS']
                ? $this->dataDaftarRi['spri']['noKontrolRS']
                : $noKontrol)
            : $noKontrol);
    }




    // ////////////////
    // Antrol Logic
    // ////////////////


    private function pushSuratSpriBPJS(): void
    {


        //push data SuratKontrolBPJS
        if ($this->dataDaftarRi['klaimId'] = 'JM') {

            // jika SKDP kosong lakukan push data
            // insert
            if (
                !isset($this->dataDaftarRi['spri']['noSPRIBPJS']) ||
                empty($this->dataDaftarRi['spri']['noSPRIBPJS'])
            ) {

                $this->dataDaftarRi['spri']['noSPRIBPJS'] = '';

                if (!$this->dataDaftarRi['spri']['noSPRIBPJS']) {
                    $HttpGetBpjs =  VclaimTrait::spri_insert($this->dataDaftarRi['spri'])->getOriginalContent();

                    // 2 cek proses pada getHttp
                    if ($HttpGetBpjs['metadata']['code'] == 200) {
                        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('SPRI Inap' .  $HttpGetBpjs['metadata']['code'] . ' ' . $HttpGetBpjs['metadata']['message']);
                        $this->dataDaftarRi['spri']['noSPRIBPJS'] = $HttpGetBpjs['response']['noSPRI']; //status 200 201 400 ..
                        // Logic update mode start //////////
                        $this->updateDataRI($this->dataDaftarRi['riHdrNo']);

                        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('SPRI Inap' .  $HttpGetBpjs['metadata']['code'] . ' ' . $HttpGetBpjs['metadata']['message']);
                    } else {
                        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('SPRI Inap' . $HttpGetBpjs['metadata']['code'] . ' ' . $HttpGetBpjs['metadata']['message']);
                    }
                }
            } else {
                // update
                $HttpGetBpjs =  VclaimTrait::spri_update($this->dataDaftarRi['spri'])->getOriginalContent();

                if ($HttpGetBpjs['metadata']['code'] == 200) {
                    toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('UPDATESPRI ' .  $HttpGetBpjs['metadata']['code'] . ' ' . $HttpGetBpjs['metadata']['message']);
                    // $this->dataDaftarRi['spri']['noSPRIBPJS'] = $HttpGetBpjs['metadata']['response']['noSPRI']; //status 200 201 400 ..
                    // Logic update mode start //////////
                    $this->updateDataRI($this->dataDaftarRi['riHdrNo']);
                    toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('UPDATESPRI ' .  $HttpGetBpjs['metadata']['code'] . ' ' . $HttpGetBpjs['metadata']['message']);
                } else {
                    toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('UPDATESPRI ' . $HttpGetBpjs['metadata']['code'] . ' ' . $HttpGetBpjs['metadata']['message']);
                }
            }
        }
    }








    // when new form instance
    public function mount()
    {
        $this->findData($this->riHdrNoRef);
        // set data dokter ref
        // $this->store();
    }



    // select data start////////////////
    public function render()
    {

        return view(
            'livewire.daftar-r-i.spri-r-i.spri-r-i',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Data SPRI Pasien Rawat Inap',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Pasien Rawat Inap',
            ]
        );
    }
    // select data end////////////////


}
