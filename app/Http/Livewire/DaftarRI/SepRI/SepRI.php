<?php

namespace App\Http\Livewire\DaftarRI\SepRI;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

use App\Http\Traits\BPJS\VclaimTrait;
use App\Http\Traits\MasterPasien\MasterPasienTrait;

use App\Http\Traits\EmrRI\EmrRITrait;



class SepRI extends Component
{
    use WithPagination, EmrRITrait, MasterPasienTrait;
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






    private function rujukanPesertaSPRI($noSPRI): array
    {
        $HttpGetBpjs =  VclaimTrait::suratkontrol_nomor($noSPRI)->getOriginalContent();
        if ($HttpGetBpjs['metadata']['code'] == 200) {
            $dataRefRujukanSPRIBPJS = json_decode(json_encode($HttpGetBpjs['response'], true), true);
        } else {
            $dataRefRujukanSPRIBPJS = [];
        }

        return $dataRefRujukanSPRIBPJS;
    }

    private function pesertaNomorKartu($idBpjs, $tanggal): array
    {
        $HttpGetBpjs =  VclaimTrait::peserta_nomorkartu($idBpjs, $tanggal)->getOriginalContent();
        // metadata d kecil
        if ($HttpGetBpjs['metadata']['code'] == 200) {
            $dataRefPeserta = json_decode(json_encode($HttpGetBpjs['response'], true), true);
        } else {
            $dataRefPeserta = [];
        }
        return $dataRefPeserta;
    }


    public function setSEPJsonReqRI($riHdrNo): void
    {
        try {

            $noSPRI = $this->dataDaftarRi['spri']['noSPRIBPJS'] ?? null;
            $noKartu = $this->dataDaftarRi['spri']['noKartu'] ?? null;

            if (empty($noSPRI)) {
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                    ->addError("Nomor SPRI belum diisi.");
                return;
            }

            // 2. Ambil data rujukan peserta
            $dataRefRujukanSPRIBPJS = $this->rujukanPesertaSPRI($noSPRI);

            if (!$dataRefRujukanSPRIBPJS) {
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                    ->addError("Data rujukan peserta berdasarkan nomor SPRI {$noSPRI} tidak ditemukan.");
                return;
            }

            $tanggal = Carbon::now(env('APP_TIMEZONE'))->format('Y-m-d');
            $dataRefPesertaBPJS = $this->pesertaNomorKartu($noKartu, $tanggal);

            if (!$dataRefPesertaBPJS) {
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                    ->addError("Data peserta berdasarkan nomor kartu {$noKartu} tidak ditemukan.");
                return;
            }

            $dataPasien = $this->findDataMasterPasien($this->dataDaftarRi['regNo'] ?? '');
            // 4. Susun payload reqSep
            $this->dataDaftarRi['sep']['reqSep'] = [
                "request" => [
                    "t_sep" => [
                        "noKartu" => $noKartu ?? '',
                        "tglSep" => Carbon::parse($this->dataDaftarRi['entryDate'])->format('Y-m-d'),
                        "ppkPelayanan" => "0184R006",
                        "jnsPelayanan" => "1",
                        "klsRawat" => [
                            "klsRawatHak" => $dataRefPesertaBPJS['peserta']['hakKelas']['kode'] ?? '',
                            "klsRawatNaik" => "",
                            "pembiayaan" => "",
                            "penanggungJawab" => ""
                        ],
                        "noMR" => $dataPasien['pasien']['regNo'] ?? '', // fungsi ambil dari master pasien
                        "rujukan" => [
                            "asalRujukan" => "2",
                            "tglRujukan" => $dataRefRujukanSPRIBPJS['tglRencanaKontrol'] ?? '',
                            "noRujukan" => $dataRefRujukanSPRIBPJS['noSuratKontrol'] ?? '',
                            "ppkRujukan" => "0184R006"
                        ],
                        "catatan" => "-",
                        "diagAwal" => "",
                        "poli" => [
                            "tujuan" => "",
                            "eksekutif" => "0"
                        ],
                        "cob" => ["cob" => "0"],
                        "katarak" => ["katarak" => "0"],
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
                            "noSurat" => $dataRefRujukanSPRIBPJS['noSuratKontrol'],
                            "kodeDPJP" => $dataRefRujukanSPRIBPJS['kodeDokter']
                        ],
                        "dpjpLayan" => "",
                        "noTelp" => $dataPasien['pasien']['kontak']['nomerTelponSelulerPasien'] ?? '',
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
        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('SEP Update dlm proses pengembangan xxxx');

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

            if (empty($this->dataDaftarRi['sep']['reqSep'])) {
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Form SEP Masih Kosong');
                return;
            }
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
        if (empty($this->dataDaftarRi['sep']['noSep']) && empty($this->dataDaftarRi['sep']['reqSep'])) {
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
