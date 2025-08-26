<?php

namespace App\Http\Livewire\MrRJ\SkdpRI;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

use App\Http\Traits\customErrorMessagesTrait;
// use App\Http\Traits\BPJS\AntrianTrait;
use App\Http\Traits\BPJS\VclaimTrait;

use App\Http\Traits\EmrRI\EmrRITrait;

// use Illuminate\Support\Str;
// use Spatie\ArrayToXml\ArrayToXml;
use Barryvdh\DomPDF\Facade\Pdf;


class SkdpRI extends Component
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

    // data SKDP / kontrol=>[]
    public $kontrol = [
        'noKontrolRS' => "",
        'noSKDPBPJS' => "",
        'noAntrian' => "",
        'tglKontrol' => "",
        'poliKontrol' => "",
        'poliKontrolBPJS' => "",
        'poliKontrolDesc' => "",
        'drKontrol' => "",
        'drKontrolBPJS' => "",
        'drKontrolDesc' => "",
        'catatan' => "",
        'noSEP' => "",
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
            $this->dataDaftarRi['kontrol']['drKontrol'] = $dataDokter->dr_id;
            $this->dataDaftarRi['kontrol']['drKontrolDesc'] = $dataDokter->dr_name;

            $this->dataDaftarRi['kontrol']['poliKontrol'] = $dataDokter->poli_id;
            $this->dataDaftarRi['kontrol']['poliKontrolDesc'] = $dataDokter->poli_desc;

            $this->dataDaftarRi['kontrol']['drKontrolBPJS'] = $dataDokter->kd_dr_bpjs;
            $this->dataDaftarRi['kontrol']['poliKontrolBPJS'] = $dataDokter->kd_poli_bpjs;

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
            $this->dataDaftarRi['kontrol']['drKontrol'] = "";
            $this->dataDaftarRi['kontrol']['drKontrolDesc'] = "";

            $this->dataDaftarRi['kontrol']['poliKontrol'] = "";
            $this->dataDaftarRi['kontrol']['poliKontrolDesc'] = "";

            $this->dataDaftarRi['kontrol']['drKontrolBPJS'] = "";
            $this->dataDaftarRi['kontrol']['poliKontrolBPJS'] = "";
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
        $this->dataDaftarRi['kontrol']['drKontrol'] = $dataDokter->dr_id;
        $this->dataDaftarRi['kontrol']['drKontrolDesc'] = $dataDokter->dr_name;

        $this->dataDaftarRi['kontrol']['poliKontrol'] = $dataDokter->poli_id;
        $this->dataDaftarRi['kontrol']['poliKontrolDesc'] = $dataDokter->poli_desc;

        $this->dataDaftarRi['kontrol']['drKontrolBPJS'] = $dataDokter->kd_dr_bpjs;
        $this->dataDaftarRi['kontrol']['poliKontrolBPJS'] = $dataDokter->kd_poli_bpjs;

        $this->dataDokterLovStatus = false;
        $this->dataDokterLovSearch = '';
    }
    // LOV selected end
    /////////////////////////////////////////////////
    // Lov dataDokterRJ //////////////////////
    ////////////////////////////////////////////////


    // validate Data RJ//////////////////////////////////////////////////
    private function validateDataRJ(): void
    {
        // customErrorMessages
        $messages = customErrorMessagesTrait::messages();

        // require nik ketika pasien tidak dikenal



        $rules = [

            "dataDaftarRi.kontrol.noKontrolRS" => "required",

            "dataDaftarRi.kontrol.noSKDPBPJS" => "",
            "dataDaftarRi.kontrol.noAntrian" => "",

            "dataDaftarRi.kontrol.tglKontrol" => "bail|required|date_format:d/m/Y",

            "dataDaftarRi.kontrol.drKontrol" => "required",
            "dataDaftarRi.kontrol.drKontrolDesc" => "required",
            "dataDaftarRi.kontrol.drKontrolBPJS" => "",


            "dataDaftarRi.kontrol.poliKontrol" => "required",
            "dataDaftarRi.kontrol.poliKontrolDesc" => "required",
            "dataDaftarRi.kontrol.poliKontrolBPJS" => "",

            "dataDaftarRi.kontrol.catatan" => "",

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

        //  off sementara
        $this->pushSuratKontrolBPJS();

        // Validate RJ
        $this->validateDataRJ();

        // Logic update mode start //////////
        $this->updateDataRI($this->dataDaftarRi['riHdrNo']);
        $this->emit('syncronizeAssessmentPerawatRJFindData');
        $this->emit('syncronizeAssessmentPerawatRIFindData');
    }

    private function updateDataRI($riHdrNo): void
    {

        $this->updateJsonRI($this->dataDaftarRi['riHdrNo'], $this->dataDaftarRi);
        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Surat Kontrol berhasil disimpan.");
    }
    // insert and update record end////////////////


    private function findData($riHdrNo): void
    {

        $findData = DB::table('rsview_rihdrs')
            ->select('vno_sep')
            ->where('rihdr_no', $riHdrNo)
            ->first();

        $this->dataDaftarRi = $this->findDataRI($riHdrNo);

        // jika kontrol tidak ditemukan tambah variable kontrol pda array
        if (isset($this->dataDaftarRi['kontrol']) == false) {
            $this->dataDaftarRi['kontrol']['tglKontrol'] =  Carbon::now(env('APP_TIMEZONE'))->addDays(8)->format('d/m/Y');
            $this->dataDaftarRi['kontrol']['drKontrol'] =  (isset($this->dataDaftarRi['drId'])
                ? ($this->dataDaftarRi['drId']
                    ? $this->dataDaftarRi['drId']
                    : '')
                : '');
            $this->dataDaftarRi['kontrol']['drKontrolDesc'] = (isset($this->dataDaftarRi['drDesc'])
                ? ($this->dataDaftarRi['drDesc']
                    ? $this->dataDaftarRi['drDesc']
                    : '')
                : '');
            $this->dataDaftarRi['kontrol']['drKontrolBPJS'] = (isset($this->dataDaftarRi['kddrbpjs'])
                ? ($this->dataDaftarRi['kddrbpjs']
                    ? $this->dataDaftarRi['kddrbpjs']
                    : '')
                : '');
            $this->dataDaftarRi['kontrol']['poliKontrol'] = (isset($this->dataDaftarRi['poliId'])
                ? ($this->dataDaftarRi['poliId']
                    ? $this->dataDaftarRi['poliId']
                    : '')
                : '');
            $this->dataDaftarRi['kontrol']['poliKontrolDesc'] = (isset($this->dataDaftarRi['poliDesc'])
                ? ($this->dataDaftarRi['poliDesc']
                    ? $this->dataDaftarRi['poliDesc']
                    : '')
                : '');
            $this->dataDaftarRi['kontrol']['poliKontrolBPJS'] = (isset($this->dataDaftarRi['kdpolibpjs'])
                ? ($this->dataDaftarRi['kdpolibpjs']
                    ? $this->dataDaftarRi['kdpolibpjs']
                    : '')
                : '');
            $this->dataDaftarRi['kontrol']['noSEP'] = (isset($findData->vno_sep)
                ? ($findData->vno_sep
                    ? $findData->vno_sep
                    : '')
                : '');
        }
    }

    // set data RiHdrNo / NoBooking / NoAntrian / klaimId / kunjunganId
    private function setDataPrimer(): void
    {
        $noKontrol = Carbon::now(env('APP_TIMEZONE'))->addDays(8)->format('dmY') . $this->dataDaftarRi['kontrol']['drKontrol'] . $this->dataDaftarRi['kontrol']['poliKontrol'];
        $this->dataDaftarRi['kontrol']['noKontrolRS'] = (isset($this->dataDaftarRi['kontrol']['noKontrolRS'])
            ? ($this->dataDaftarRi['kontrol']['noKontrolRS']
                ? $this->dataDaftarRi['kontrol']['noKontrolRS']
                : $noKontrol)
            : $noKontrol);
    }




    // ////////////////
    // Antrol Logic
    // ////////////////


    private function pushSuratKontrolBPJS(): void
    {


        //push data SuratKontrolBPJS
        if ($this->dataDaftarRi['klaimId'] = 'JM') {

            // jika SKDP kosong lakukan push data
            // insert
            if (
                !isset($this->dataDaftarRi['kontrol']['noSKDPBPJS']) ||
                empty($this->dataDaftarRi['kontrol']['noSKDPBPJS'])
            ) {

                $this->dataDaftarRi['kontrol']['noSKDPBPJS'] = '';

                if (!$this->dataDaftarRi['kontrol']['noSKDPBPJS']) {
                    $HttpGetBpjs =  VclaimTrait::suratkontrol_insert($this->dataDaftarRi['kontrol'])->getOriginalContent();

                    // 2 cek proses pada getHttp
                    if ($HttpGetBpjs['metadata']['code'] == 200) {
                        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('KONTROL Post Inap' .  $HttpGetBpjs['metadata']['code'] . ' ' . $HttpGetBpjs['metadata']['message']);
                        $this->dataDaftarRi['kontrol']['noSKDPBPJS'] = $HttpGetBpjs['response']['noSuratKontrol']; //status 200 201 400 ..

                        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('KONTROL Post Inap' .  $HttpGetBpjs['metadata']['code'] . ' ' . $HttpGetBpjs['metadata']['message']);
                    } else {
                        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('KONTROL Post Inap' . $HttpGetBpjs['metadata']['code'] . ' ' . $HttpGetBpjs['metadata']['message']);
                    }
                }
            } else {
                // update
                $HttpGetBpjs =  VclaimTrait::suratkontrol_update($this->dataDaftarRi['kontrol'])->getOriginalContent();

                if ($HttpGetBpjs['metadata']['code'] == 200) {
                    toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('UPDATEKONTROL ' .  $HttpGetBpjs['metadata']['code'] . ' ' . $HttpGetBpjs['metadata']['message']);
                    // $this->dataDaftarRi['kontrol']['noSKDPBPJS'] = $HttpGetBpjs['metadata']['response']['noSuratKontrol']; //status 200 201 400 ..

                    toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('UPDATEKONTROL ' .  $HttpGetBpjs['metadata']['code'] . ' ' . $HttpGetBpjs['metadata']['message']);
                } else {
                    toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('UPDATEKONTROL ' . $HttpGetBpjs['metadata']['code'] . ' ' . $HttpGetBpjs['metadata']['message']);
                }
            }
        }
    }

    public function cetakSKDP()
    {
        $queryIdentitas = DB::table('rsmst_identitases')
            ->select(
                'int_name',
                'int_phone1',
                'int_phone2',
                'int_fax',
                'int_address',
                'int_city',
            )
            ->first();

        // cek ada resSep ada atau tidak
        if (isset($this->dataDaftarRi['kontrol'])) {
            if ($this->dataDaftarRi['kontrol']) {

                if (isset($this->dataDaftarRi['kontrol']['regNo'])) {
                    $this->setDataPasien($this->dataDaftarRi['kontrol']['regNo']);
                }

                $data = [
                    'myQueryIdentitas' => $queryIdentitas,
                    'dataPasien' => $this->dataPasien,
                    'dataDaftarTxn' => $this->dataDaftarRi['kontrol'],

                ];
                $pdfContent = PDF::loadView('livewire.mr-r-j.skdp-r-i.cetak-skdp-ri', $data)->output();
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('CetakSKDP');

                return response()->streamDownload(
                    fn() => print($pdfContent),
                    "filename.pdf"
                );
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
            'livewire.mr-r-j.skdp-r-i.skdp-r-i',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Data SKDP Pasien Post Rawat Inap',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Pasien Post Rawat Inap',
            ]
        );
    }
    // select data end////////////////


}
