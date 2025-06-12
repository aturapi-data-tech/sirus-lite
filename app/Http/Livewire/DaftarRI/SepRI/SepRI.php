<?php

namespace App\Http\Livewire\DaftarRI\SepRI;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

use App\Http\Traits\BPJS\VclaimTrait;

use App\Http\Traits\EmrRI\EmrRITrait;



class SepRI extends Component
{
    use WithPagination, EmrRITrait;
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






    private function rujukanPesertaFKTL($idBpjs): array
    {
        $HttpGetBpjs =  VclaimTrait::rujukan_rs_peserta($idBpjs)->getOriginalContent();

        if ($HttpGetBpjs['metadata']['code'] == 200) {
            $dataRefBPJSLov = json_decode(json_encode($HttpGetBpjs['response']['rujukan'], true), true);
        } else {
            $dataRefBPJSLov = [];
        }

        return $dataRefBPJSLov;
    }

    public function setSEPJsonReqRI($riHdrNo): void
    {
        try {
            // 1. Ambil data pasien
            $findData = DB::table('rsview_rihdrs')
                ->join('rsmst_pasiens', 'rsview_rihdrs.reg_no', '=', 'rsmst_pasiens.reg_no')
                ->select('rsmst_pasiens.nokartu_bpjs')
                ->where('rsview_rihdrs.rihdr_no', $riHdrNo)
                ->first();

            if (!$findData || empty($findData->nokartu_bpjs)) {
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                    ->addError("Data BPJS pasien tidak ditemukan untuk RI No: {$riHdrNo}");
                return;
            }

            $idBpjs = $findData->nokartu_bpjs;
            $noSPRI = $this->dataDaftarRi['spri']['noSPRIBPJS'] ?? null;

            if (empty($noSPRI)) {
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                    ->addError("Nomor SPRI belum diisi.");
                return;
            }

            // 2. Ambil data rujukan peserta
            $allRujukan = $this->rujukanPesertaFKTL($idBpjs);
            $dataRefBPJSLov = collect($allRujukan)->where('noKunjungan', $noSPRI)->first();

            if (!$dataRefBPJSLov) {
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                    ->addError("Data rujukan peserta berdasarkan nomor SPRI {$noSPRI} tidak ditemukan.");
                return;
            }

            $kodeDPJP = $this->dataDaftarRi['spri']['drKontrolBPJS'] ?? '';
            // 4. Susun payload reqSep
            $this->dataDaftarRi['sep']['reqSep'] = [
                "request" => [
                    "t_sep" => [
                        "noKartu" => $dataRefBPJSLov['peserta']['noKartu'] ?? '',
                        "tglSep" => Carbon::createFromFormat('d/m/Y H:i:s', $this->dataDaftarRi['entryDate'], env('APP_TIMEZONE'))->format('Y-m-d'),
                        "ppkPelayanan" => "0184R006",
                        "jnsPelayanan" => "1",
                        "klsRawat" => [
                            "klsRawatHak" => $dataRefBPJSLov['peserta']['hakKelas']['kode'] ?? '',
                            "klsRawatNaik" => "",
                            "pembiayaan" => "",
                            "penanggungJawab" => ""
                        ],
                        "noMR" => $dataRefBPJSLov['peserta']['mr']['noMR'] ?? '',
                        "rujukan" => [
                            "asalRujukan" => "2",
                            "tglRujukan" => $dataRefBPJSLov['tglKunjungan'] ?? '',
                            "noRujukan" => $dataRefBPJSLov['noKunjungan'] ?? '',
                            "ppkRujukan" => $dataRefBPJSLov['provPerujuk']['kode'] ?? ''
                        ],
                        "catatan" => "-",
                        "diagAwal" => $dataRefBPJSLov['diagnosa']['kode'] ?? '',
                        "cob" => [
                            "cob" => "0"
                        ],
                        "katarak" => [
                            "katarak" => "0"
                        ],
                        "jaminan" => [
                            "lakaLantas" => "0",
                            "noLP" => "",
                            "penjamin" => [
                                "tglKejadian" => "",
                                "keterangan" => "",
                                "suplesi" => [
                                    "suplesi" => "0",
                                    "noSepSuplesi" => "",
                                    "lokasiLaka" => [
                                        "kdPropinsi" => "",
                                        "kdKabupaten" => "",
                                        "kdKecamatan" => ""
                                    ]
                                ]
                            ]
                        ],
                        "tujuanKunj" => "0",
                        "flagProcedure" => "",
                        "kdPenunjang" => "",
                        "assesmentPel" => "",
                        "skdp" => [
                            "noSurat" => $noSPRI,
                            "kodeDPJP" => $kodeDPJP
                        ],
                        "dpjpLayan" => "",
                        "noTelp" => $dataRefBPJSLov['peserta']['mr']['noTelepon'] ?? '',
                        "user" => "sirus App"
                    ]
                ]
            ];
        } catch (\Exception $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError("Gagal menyusun data SEP RI: " . $e->getMessage());
        }
    }


    private function pushInsertSEP($SEPJsonReq)
    {

        //ketika Push Tambah Antrean Berhasil buat SEP
        //////////////////////////////////////////////
        $HttpGetBpjs =  VclaimTrait::sep_insert($SEPJsonReq)->getOriginalContent();
        if ($HttpGetBpjs['metadata']['code'] == 200) {
            // dd($HttpGetBpjs);
            $this->dataDaftarRi['sep']['resSep'] = $HttpGetBpjs['response']['sep'];
            $this->dataDaftarRi['sep']['noSep'] = $HttpGetBpjs['response']['sep']['noSep'];

            // update table trnsaksi
            DB::table('rstxn_rihdrs')
                ->where('rihdr_no', $this->riHdrNoRef)
                ->update([
                    'vno_sep' => $this->dataDaftarRi['sep']['noSep'] ?? '',
                ]);

            $this->updateDataRi($this->riHdrNoRef);


            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('SEP ' .  $HttpGetBpjs['metadata']['code'] . ' ' . $HttpGetBpjs['metadata']['message']);
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('SEP ' . $HttpGetBpjs['metadata']['code'] . ' ' . $HttpGetBpjs['metadata']['message']);
        }

        // response sep value
        //ketika Push Tambah Antrean Berhasil buat SEP
        //////////////////////////////////////////////
    }

    private function pushUpdateSEP($SEPJsonReq)
    {
        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('SEP xxxx');

        //ketika Push Tambah Antrean Berhasil buat SEP
        //////////////////////////////////////////////
        // $HttpGetBpjs =  VclaimTrait::sep_insert($SEPJsonReq)->getOriginalContent();
        // if ($HttpGetBpjs['metadata']['code'] == 200) {
        //     // dd($HttpGetBpjs);
        //     $this->dataDaftarRi['sep']['resSep'] = $HttpGetBpjs['response']['sep'];
        //     $this->dataDaftarRi['sep']['noSep'] = $HttpGetBpjs['response']['sep']['noSep'];

        //     // update table trnsaksi
        //     DB::table('rstxn_rihdrs')
        //         ->where('rihdr_no', $this->riHdrNoRef)
        //         ->update([
        //             'vno_sep' => $this->dataDaftarRi['sep']['noSep'] ?? '',
        //         ]);

        //     toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('SEP ' .  $HttpGetBpjs['metadata']['code'] . ' ' . $HttpGetBpjs['metadata']['message']);
        // } else {
        //     toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('SEP ' . $HttpGetBpjs['metadata']['code'] . ' ' . $HttpGetBpjs['metadata']['message']);
        // }

        // response sep value
        //ketika Push Tambah Antrean Berhasil buat SEP
        //////////////////////////////////////////////
    }


    private function updateDataRi($riHdrNo): void
    {
        $this->updateJsonRI($riHdrNo, $this->dataDaftarRi);
    }






    public function store()
    {
        // lakukan jika noSep masih kosong
        if (empty($this->dataDaftarRi['sep']['noSep'])) {
            $this->pushInsertSEP($this->dataDaftarRi['sep']['reqSep'] ?? []);
        } else {
            $this->pushUpdateSEP($this->dataDaftarRi['sep']['reqSep'] ?? []);
        }

        // Logic update mode start //////////

        $this->emit('syncronizeAssessmentPerawatRIFindData');
    }
    private function findData($riHdrNo): void
    {

        $this->dataDaftarRi = $this->findDataRI($riHdrNo);
        // dd($this->dataDaftarRi);
        // jika reqSep tidak ditemukan tambah variable reqSep pda array
        if (empty($this->dataDaftarRi['sep']['reqSep'])) {
            $this->setSEPJsonReqRI($riHdrNo);
        }
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
            'livewire.daftar-r-i.sep-r-i.sep-r-i',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'SEP Pasien Rawat Inap',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Pasien Rawat Inap',
            ]
        );
    }
    // select data end////////////////


}
