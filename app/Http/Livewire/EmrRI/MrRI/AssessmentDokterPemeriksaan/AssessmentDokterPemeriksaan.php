<?php

namespace App\Http\Livewire\EmrRI\MrRI\AssessmentDokterPemeriksaan;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

use App\Http\Traits\EmrRI\EmrRITrait;
use App\Http\Traits\customErrorMessagesTrait;

use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;


class AssessmentDokterPemeriksaan extends Component
{
    use WithPagination, EmrRITrait, customErrorMessagesTrait, WithFileUploads;

    // listener from blade////////////////
    protected $listeners = [
        'syncronizeAssessmentPerawatRIFindData' => 'mount'
    ];



    //////////////////////////////
    // Ref on top bar
    //////////////////////////////
    public $riHdrNoRef;

    // dataDaftarRi RJ
    public array $dataDaftarRi = [];

    // data pemeriksaan=>[]
    public array $pemeriksaan = [];

    public $filePDF, $descPDF;
    public bool $isOpenRekamMedisuploadpenunjangHasil;


    //////////////////////////////////////////////////////////////////////

    // open and close modal start////////////////
    //  modal status////////////////

    public bool $isOpenLaboratorium = false;
    public string $isOpenModeLaboratorium = 'insert';

    public  $isPemeriksaanLaboratorium = [];
    public  $isPemeriksaanLaboratoriumSelected = [];
    public int $isPemeriksaanLaboratoriumSelectedKeyHdr = 0;
    public int $isPemeriksaanLaboratoriumSelectedKeyDtl = 0;



    public bool $isOpenRadiologi = false;
    public string $isOpenModeRadiologi = 'insert';

    public  $isPemeriksaanRadiologi = [];
    public  $isPemeriksaanRadiologiSelected = [];
    public int $isPemeriksaanRadiologiSelectedKeyHdr = 0;
    public int $isPemeriksaanRadiologiSelectedKeyDtl = 0;


    // open and close modal start////////////////
    //  modal status////////////////

    public $tingkatKesadaranLov = [];




    protected $rules = [
        'dataDaftarRi.pemeriksaan.penunjang' => 'nullable|string|max:255',
    ];

    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////

    public function updated($propertyName)
    {
        // dd($propertyName);
        $this->validateOnly($propertyName);
        $this->scoringIMT();
        $this->store();
    }

    // lab
    private function openModalLaboratorium(): void
    {
        $this->isOpenLaboratorium = true;
        $this->isOpenModeLaboratorium = 'insert';
    }

    public function pemeriksaanLaboratorium()
    {
        $this->openModalLaboratorium();
        $this->renderisPemeriksaanLaboratorium();
        // $this->findData($id);
    }

    public function closeModalLaboratorium(): void
    {
        $this->reset(['isOpenLaboratorium', 'isOpenModeLaboratorium', 'isPemeriksaanLaboratorium', 'isPemeriksaanLaboratoriumSelected', 'isPemeriksaanLaboratoriumSelectedKeyHdr', 'isPemeriksaanLaboratoriumSelectedKeyDtl']);
    }

    private function renderisPemeriksaanLaboratorium()
    {
        if (empty($this->isPemeriksaanLaboratorium)) {
            $isPemeriksaanLaboratorium = DB::table('lbmst_clabitems')
                ->select('clabitem_desc', 'clabitem_id', 'price', 'clabitem_group', 'item_code')
                ->whereNull('clabitem_group')
                ->whereNotNull('clabitem_desc')
                ->orderBy('clabitem_desc', 'asc')
                ->get();

            $this->isPemeriksaanLaboratorium = json_decode(
                $isPemeriksaanLaboratorium->map(function ($isPemeriksaanLaboratorium) {
                    $isPemeriksaanLaboratorium->labStatus = 0;

                    return $isPemeriksaanLaboratorium;
                }),
                true
            );
        }
    }

    public function PemeriksaanLaboratoriumIsSelectedFor($key): void
    {
        $this->isPemeriksaanLaboratorium[$key]['labStatus'] = $this->isPemeriksaanLaboratorium[$key]['labStatus'] ? 0 : 1;
        $this->renderPemeriksaanLaboratoriumIsSelected($key);
    }

    public function RemovePemeriksaanLaboratoriumIsSelectedFor($key): void
    {
        $this->isPemeriksaanLaboratorium[$key]['labStatus'] = $this->isPemeriksaanLaboratorium[$key]['labStatus'] ? 0 : 1;
        $this->renderPemeriksaanLaboratoriumIsSelected($key);
    }

    private function renderPemeriksaanLaboratoriumIsSelected($key): void
    {
        $this->isPemeriksaanLaboratoriumSelected = collect($this->isPemeriksaanLaboratorium)
            ->where('labStatus', 1);
    }

    public function kirimLaboratorium()
    {
        $sql = "select ri_status  from rstxn_rihdrs where rihdr_no=:riHdrNo";
        $checkStatusRI = DB::scalar($sql, [
            "riHdrNo" => $this->dataDaftarRi['riHdrNo'],
        ]);

        if ($checkStatusRI == 'I') {
            // hasil Key = 0 atau urutan pemeriksan lab lebih dari 1
            $this->isPemeriksaanLaboratoriumSelectedKeyHdr = collect(isset($this->dataDaftarRi['pemeriksaan']['pemeriksaanPenunjang']['lab']) ? $this->dataDaftarRi['pemeriksaan']['pemeriksaanPenunjang']['lab'] : [])->count();

            $sql = "select nvl(max(to_number(checkup_no))+1,1) from lbtxn_checkuphdrs";
            $checkupNo = DB::scalar($sql);

            // array Hdr
            $this->dataDaftarRi['pemeriksaan']['pemeriksaanPenunjang']['lab'][$this->isPemeriksaanLaboratoriumSelectedKeyHdr]['labHdr']['labHdrNo'] =  $checkupNo;
            $this->dataDaftarRi['pemeriksaan']['pemeriksaanPenunjang']['lab'][$this->isPemeriksaanLaboratoriumSelectedKeyHdr]['labHdr']['labHdrDate'] = Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s');


            // insert Hdr
            DB::table('lbtxn_checkuphdrs')->insert([
                'reg_no' => $this->dataDaftarRi['regNo'],
                'dr_id' => $this->dataDaftarRi['drId'],
                'checkup_date' => DB::raw("to_date('" . Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s') . "','dd/mm/yyyy hh24:mi:ss')"),
                'status_rjri' => 'RI',
                'checkup_status' => 'P',
                'ref_no' => $this->dataDaftarRi['riHdrNo'],
                'checkup_no' => $checkupNo,

            ]);


            // hasil Key Dtl dari jml yang selected -1 karena array dimulai dari 0
            $this->isPemeriksaanLaboratoriumSelectedKeyDtl = collect($this->isPemeriksaanLaboratoriumSelected)->count() - 1;


            // array Dtl
            $this->dataDaftarRi['pemeriksaan']['pemeriksaanPenunjang']['lab'][$this->isPemeriksaanLaboratoriumSelectedKeyHdr]['labHdr']['labDtl'] =  collect($this->isPemeriksaanLaboratorium)
                ->where('labStatus', 1)
                ->toArray();

            // insert Dtl
            foreach ($this->dataDaftarRi['pemeriksaan']['pemeriksaanPenunjang']['lab'][$this->isPemeriksaanLaboratoriumSelectedKeyHdr]['labHdr']['labDtl'] as $labDtl) {
                $sql = "select nvl(to_number(max(checkup_dtl))+1,1) from LBTXN_CHECKUPDTLS";
                $checkupDtl = DB::scalar($sql);

                // insert Prise checkup dtl
                DB::table('lbtxn_checkupdtls')->insert([
                    'clabitem_id' => $labDtl['clabitem_id'],
                    'checkup_no' => $checkupNo,
                    'checkup_dtl' => $checkupDtl,
                    'lab_item_code' => $labDtl['item_code'],
                    'price' => $labDtl['price']
                ]);

                foreach ($this->isPemeriksaanLaboratoriumSelected as $isPemeriksaanLaboratoriumSelected) {

                    // insert No Prise checkup dtl
                    $items = DB::table('lbmst_clabitems')->select('clabitem_desc', 'clabitem_id', 'price', 'clabitem_group', 'item_code')
                        ->where('clabitem_group', $labDtl['clabitem_id'])
                        ->orderBy('item_seq', 'asc')
                        ->orderBy('clabitem_desc', 'asc')
                        ->get();

                    foreach ($items as $item) {
                        $sql = "select nvl(to_number(max(checkup_dtl))+1,1) from LBTXN_CHECKUPDTLS";
                        $checkupDtl = DB::scalar($sql);

                        DB::table('lbtxn_checkupdtls')->insert([
                            'clabitem_id' => $item->clabitem_id,
                            'checkup_no' => $checkupNo,
                            'checkup_dtl' => $checkupDtl,
                            'lab_item_code' => $item->item_code,
                            'price' => $item->price
                        ]);

                        $this->isPemeriksaanLaboratoriumSelectedKeyDtl = $this->isPemeriksaanLaboratoriumSelectedKeyDtl + 1;
                    }
                }


                $this->isPemeriksaanLaboratoriumSelectedKeyDtl = $this->isPemeriksaanLaboratoriumSelectedKeyDtl + 1;
            }



            $this->updateDataRi($this->dataDaftarRi['riHdrNo']);
            $this->closeModalLaboratorium();
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Pasien Sudah Pulang, Anda tidak bisa meneruskan pemeriksaan ini.");
            return;
        }
    }
    // Lab

    // Rad
    private function openModalRadiologi(): void
    {
        $this->isOpenRadiologi = true;
        $this->isOpenModeRadiologi = 'insert';
    }

    public function pemeriksaanRadiologi()
    {
        $this->openModalRadiologi();
        $this->renderisPemeriksaanRadiologi();
        // $this->findData($id);
    }

    public function closeModalRadiologi(): void
    {
        $this->reset(['isOpenRadiologi', 'isOpenModeRadiologi', 'isPemeriksaanRadiologi', 'isPemeriksaanRadiologiSelected', 'isPemeriksaanRadiologiSelectedKeyHdr', 'isPemeriksaanRadiologiSelectedKeyDtl']);
    }

    private function renderisPemeriksaanRadiologi()
    {
        if (empty($this->isPemeriksaanRadiologi)) {
            $isPemeriksaanRadiologi = DB::table('rsmst_radiologis ')
                ->select('rad_desc', 'rad_price', 'rad_id')
                ->orderBy('rad_desc', 'asc')
                ->get();

            $this->isPemeriksaanRadiologi = json_decode(
                $isPemeriksaanRadiologi->map(function ($isPemeriksaanRadiologi) {
                    $isPemeriksaanRadiologi->radStatus = 0;

                    return $isPemeriksaanRadiologi;
                }),
                true
            );
        }
    }

    public function PemeriksaanRadiologiIsSelectedFor($key): void
    {
        $this->isPemeriksaanRadiologi[$key]['radStatus'] = $this->isPemeriksaanRadiologi[$key]['radStatus'] ? 0 : 1;
        $this->renderPemeriksaanRadiologiIsSelected($key);
    }

    public function RemovePemeriksaanRadiologiIsSelectedFor($key): void
    {
        $this->isPemeriksaanRadiologi[$key]['radStatus'] = $this->isPemeriksaanRadiologi[$key]['radStatus'] ? 0 : 1;
        $this->renderPemeriksaanRadiologiIsSelected($key);
    }

    private function renderPemeriksaanRadiologiIsSelected($key): void
    {
        $this->isPemeriksaanRadiologiSelected = collect($this->isPemeriksaanRadiologi)
            ->where('radStatus', 1);
    }

    public function kirimRadiologi()
    {

        $sql = "select ri_status  from rstxn_rihdrs where rihdr_no=:riHdrNo";
        $checkStatusRI = DB::scalar($sql, [
            "riHdrNo" => $this->dataDaftarRi['riHdrNo'],
        ]);

        if ($checkStatusRI == 'I') {
            // hasil Key = 0 atau urutan pemeriksan lab lebih dari 1
            $this->isPemeriksaanRadiologiSelectedKeyHdr = collect(isset($this->dataDaftarRi['pemeriksaan']['pemeriksaanPenunjang']['rad']) ? $this->dataDaftarRi['pemeriksaan']['pemeriksaanPenunjang']['rad'] : [])->count();

            // $sql = "select nvl(max(rirad_no)+1,1) from rstxn_riradiologs";
            // $checkupNo = DB::scalar($sql);

            // array Hdr
            $this->dataDaftarRi['pemeriksaan']['pemeriksaanPenunjang']['rad'][$this->isPemeriksaanRadiologiSelectedKeyHdr]['radHdr']['radHdrNo'] =  $this->dataDaftarRi['riHdrNo'];
            $this->dataDaftarRi['pemeriksaan']['pemeriksaanPenunjang']['rad'][$this->isPemeriksaanRadiologiSelectedKeyHdr]['radHdr']['radHdrDate'] = Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s');


            // insert Hdr (Radiologi tidak di insert header / ikut txn rj)
            // DB::table('lbtxn_checkuphdrs')->insert([
            //     'reg_no' => $this->dataDaftarRi['regNo'],
            //     'dr_id' => $this->dataDaftarRi['drId'],
            //     'checkup_date' => DB::raw("to_date('" . Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s') . "','dd/mm/yyyy hh24:mi:ss')"),
            //     'status_rjri' => 'RJ',
            //     'checkup_status' => 'P',
            //     'ref_no' => $this->dataDaftarRi['riHdrNo'],
            //     'checkup_no' => $checkupNo,

            // ]);


            // hasil Key Dtl dari jml yang selected -1 karena array dimulai dari 0
            $this->isPemeriksaanRadiologiSelectedKeyDtl = collect($this->isPemeriksaanRadiologiSelected)->count() - 1;


            // array Dtl
            $this->dataDaftarRi['pemeriksaan']['pemeriksaanPenunjang']['rad'][$this->isPemeriksaanRadiologiSelectedKeyHdr]['radHdr']['radDtl'] =  collect($this->isPemeriksaanRadiologi)
                ->where('radStatus', 1)
                ->toArray();

            // insert Dtl
            foreach ($this->dataDaftarRi['pemeriksaan']['pemeriksaanPenunjang']['rad'][$this->isPemeriksaanRadiologiSelectedKeyHdr]['radHdr']['radDtl'] as $radDtl) {
                $sql = "select nvl(max(rirad_no)+1,1) from rstxn_riradiologs";
                $checkupDtl = DB::scalar($sql);
                // insert Prise checkup dtl
                DB::table('rstxn_riradiologs')->insert([
                    'rirad_no' => $checkupDtl,
                    'rad_id' => $radDtl['rad_id'],
                    'rihdr_no' => $this->dataDaftarRi['riHdrNo'],
                    'rirad_price' => $radDtl['rad_price'],
                    'dr_radiologi' => 'dr. M.A. Budi Purwito, Sp.Rad.',
                    'waktu_entry' => DB::raw("to_date('" . Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s') . "','dd/mm/yyyy hh24:mi:ss')"),
                    'rirad_date' => DB::raw("to_date('" . Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s') . "','dd/mm/yyyy hh24:mi:ss')"),
                ]);


                $this->isPemeriksaanRadiologiSelectedKeyDtl = $this->isPemeriksaanRadiologiSelectedKeyDtl + 1;
            }



            $this->updateDataRi($this->dataDaftarRi['riHdrNo']);
            $this->closeModalRadiologi();
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Pasien Sudah Pulang, Anda tidak bisa meneruskan pemeriksaan ini.");
            return;
        }
    }
    // Rad




    // ////////////////
    // RJ Logic
    // ////////////////




    // insert and update record start////////////////
    public function store()
    {
        $this->updateDataRi($this->dataDaftarRi['riHdrNo']);

        $this->emit('syncronizeAssessmentPerawatRIFindData');
    }

    private function updateDataRi($riHdrNo): void
    {

        $this->updateJsonRI($riHdrNo, $this->dataDaftarRi);

        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Pemeriksaan berhasil disimpan.");
    }
    // insert and update record end////////////////


    private function findData($rjno): void
    {

        $this->dataDaftarRi = $this->findDataRI($rjno);
        // dd($this->dataDaftarRi);
        // jika pemeriksaan tidak ditemukan tambah variable pemeriksaan pda array
        if (isset($this->dataDaftarRi['pemeriksaan']) == false) {
            $this->dataDaftarRi['pemeriksaan'] = $this->pemeriksaan;
        }
    }


    // set data RJno / NoBooking / NoAntrian / klaimId / kunjunganId

    private function scoringIMT(): void
    {
        $bb = (isset($this->dataDaftarRi['pemeriksaan']['nutrisi']['bb'])
            ? ($this->dataDaftarRi['pemeriksaan']['nutrisi']['bb']
                ? $this->dataDaftarRi['pemeriksaan']['nutrisi']['bb']
                : 1)
            : 1);
        $tb = (isset($this->dataDaftarRi['pemeriksaan']['nutrisi']['tb'])
            ? ($this->dataDaftarRi['pemeriksaan']['nutrisi']['tb']
                ? $this->dataDaftarRi['pemeriksaan']['nutrisi']['tb']
                : 1)
            : 1);;


        $this->dataDaftarRi['pemeriksaan']['nutrisi']['imt'] = round($bb / (($tb / 100) * ($tb / 100)), 2);
    }


    public function uploadHasilPenunjang(): void
    {
        // validate
        // cek status transaksi
        $checkRIStatus = $this->checkRIStatus($this->riHdrNoRef);
        if ($checkRIStatus) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Pasien Sudah Pulang, Trasaksi Terkunci.");
            return;
        }

        // cek status transaksi
        $this->validateDataRi($this->riHdrNoRef);


        // customErrorMessages
        $messages = [];
        // require nik ketika pasien tidak dikenal
        $rules = [
            "filePDF" => "bail|required|mimes:pdf|max:10240",
            "descPDF" => "bail|required|max:255"
        ];

        // Proses Validasi///////////////////////////////////////////
        $this->validate($rules, $messages);
        // upload photo
        $uploadHasilPenunjangfile = $this->filePDF->store('uploadHasilPenunjang');

        $this->dataDaftarRi['pemeriksaan']['uploadHasilPenunjang'][] = [
            'file' => $uploadHasilPenunjangfile,
            'desc' => $this->descPDF,
            'tglUpload' => Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s'),
            'penanggungJawab' => [
                'userLog' => auth()->user()->myuser_name,
                'userLogDate' => Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s'),
                'userLogCode' => auth()->user()->myuser_code
            ]
        ];
        $this->reset(['filePDF', 'descPDF'],);
        $this->store();
    }

    public function deleteHasilPenunjang($file): void
    {
        // Foto/////////////////////////////////////////////////////////////////////////
        Storage::delete($file);
        $deleteHasilpenunjang = collect($this->dataDaftarRi['pemeriksaan']['uploadHasilPenunjang'])->where("file", '!=', $file)->toArray();
        $this->dataDaftarRi['pemeriksaan']['uploadHasilPenunjang'] = $deleteHasilpenunjang;
        $this->store();
        //
    }

    public function openModalHasilPenunjang($file)
    {

        if (Storage::exists($file)) {
            $this->isOpenRekamMedisuploadpenunjangHasil = true;
            $this->filePDF = $file;
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('File tidak ditemukan');
            return;
        }
    }

    public function closeModalHasilPenunjang()
    {
        $this->isOpenRekamMedisuploadpenunjangHasil = false;
        $this->reset(['filePDF']);
    }

    // when new form instance
    public function mount()
    {
        $this->findData($this->riHdrNoRef);
    }



    // select data start////////////////
    public function render()
    {

        return view(
            'livewire.emr-r-i.mr-r-i.assessment-dokter-pemeriksaan.assessment-dokter-pemeriksaan',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'Anamnesia',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Pasien Rawat Jalan',
            ]
        );
    }
    // select data end////////////////


}
