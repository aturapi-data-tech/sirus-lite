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
                        "tglSep" => Carbon::createFromFormat('d/m/Y H:i:s', $this->dataDaftarRi['entryDate'], env('APP_TIMEZONE'))->format('Y-m-d'),
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

    private function pushUpdateSEP(array $SEPJsonReqUpdate): void
    {

        $req = $SEPJsonReqUpdate ?? [];
        /* 1. Panggil endpoint UPDATE */

        $SEPJsonReq = [
            'request' => [
                't_sep' => [
                    'noSep'     => $req['t_sep']['noSep'] ?? '',
                    'klsRawat'  => [
                        'klsRawatHak' => $req['t_sep']['klsRawat']['klsRawatHak'] ?? '',
                        'klsRawatNaik' => $req['t_sep']['klsRawat']['klsRawatNaik'] ?? '',
                        'pembiayaan' => $req['t_sep']['klsRawat']['pembiayaan'] ?? '',
                        'penanggungJawab' => $req['t_sep']['klsRawat']['penanggungJawab'] ?? '',
                    ],
                    'noMR'      => $req['t_sep']['noMR'] ?? '',
                    'catatan'   => $req['t_sep']['catatan'] ?? '',
                    'diagAwal'  => $req['t_sep']['diagAwal'] ?? '',
                    'poli'      => [
                        'tujuan'    => $req['t_sep']['poli']['tujuan'] ?? '',
                        'eksekutif' => $req['t_sep']['poli']['eksekutif'] ?? '0',
                    ],
                    'cob'       => [
                        'cob' => $req['t_sep']['cob']['cob'] ?? '0',
                    ],
                    'katarak'   => [
                        'katarak' => $req['t_sep']['katarak']['katarak'] ?? '0',
                    ],
                    'jaminan' => [
                        'lakaLantas' => $req['t_sep']['jaminan']['lakaLantas'] ?? '0',
                        'penjamin' => [
                            'tglKejadian' => $req['t_sep']['jaminan']['penjamin']['tglKejadian'] ?? '',
                            'keterangan' => $req['t_sep']['jaminan']['penjamin']['keterangan'] ?? '',
                            'suplesi' => [
                                'suplesi' => $req['t_sep']['jaminan']['penjamin']['suplesi']['suplesi'] ?? '0',
                                'noSepSuplesi' => $req['t_sep']['jaminan']['penjamin']['suplesi']['noSepSuplesi'] ?? '',
                                'lokasiLaka' => [
                                    'kdPropinsi' => $req['t_sep']['jaminan']['penjamin']['suplesi']['lokasiLaka']['kdPropinsi'] ?? '',
                                    'kdKabupaten' => $req['t_sep']['jaminan']['penjamin']['suplesi']['lokasiLaka']['kdKabupaten'] ?? '',
                                    'kdKecamatan' => $req['t_sep']['jaminan']['penjamin']['suplesi']['lokasiLaka']['kdKecamatan'] ?? '',
                                ]
                            ]
                        ]
                    ],
                    'dpjpLayan' => $req['t_sep']['dpjpLayan'] ?? '',
                    'noTelp'    => $req['t_sep']['noTelp'] ?? '',
                    'user'      => 'siRUS',
                ]
            ]
        ];

        $res = VclaimTrait::sep_update($SEPJsonReq)->getOriginalContent();   // ← ganti sep_insert

        /* 2. Tangani respons */
        if (($res['metadata']['code'] ?? 500) == 200) {

            // // --- simpan respons SEP terbaru ke state -------------
            // $this->dataDaftarRi['sep']['resSep'] = $res['response']['sep'] ?? [];
            // $this->dataDaftarRi['sep']['noSep']  = $res['response']['sep']['noSep'] ?? '';

            // // --- contoh: update kolom lain jika ada perubahan ----
            // DB::table('rstxn_rihdrs')
            //     ->where('rihdr_no', $this->riHdrNoRef)
            //     ->update([
            //         'vno_sep'     => $this->dataDaftarRi['sep']['noSep'],
            //         'vsep_update' => now(),          // timestamp audit
            //     ]);

            toastr()
                ->closeOnHover(true)->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addSuccess('Update SEP berhasil: '
                    . $res['metadata']['code'] . ' '
                    . $res['metadata']['message']);
        } else {
            toastr()
                ->closeOnHover(true)->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('Update SEP gagal: '
                    . ($res['metadata']['code'] ?? '??') . ' '
                    . ($res['metadata']['message'] ?? 'Tidak ada pesan'));
        }
    }

    public function deleteSep(string $noSep): void
    {
        // 1. Panggil trait sep_delete dengan try/catch untuk menghindari exception
        try {
            $response = VclaimTrait::sep_delete($noSep)
                ->getOriginalContent();
        } catch (\Throwable $e) {
            toastr()
                ->closeOnHover(true)->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError('Delete SEP gagal: ' . $e->getMessage());
            return;
        }

        // 2. Ambil kode & pesan dengan helper data_get agar lebih aman
        $code    = data_get($response, 'metadata.code');
        $message = data_get($response, 'metadata.message', 'Tidak ada pesan');

        if ($code == 200 || $code == 201) {
            // 3a. Clear state SEP (resSep, noSep, reqSep)
            $this->dataDaftarRi['sep']['resSep']  = [];
            $this->dataDaftarRi['sep']['noSep']   = '';
            unset($this->dataDaftarRi['sep']['reqSep']);

            // 3b. Update kolom vno_sep jadi kosong
            DB::table('rstxn_rihdrs')
                ->where('rihdr_no', $this->riHdrNoRef)
                ->update(['vno_sep' => '']);

            // 3c. Refresh data RI
            $this->updateDataRi($this->riHdrNoRef);

            // 3d. Tampilkan notifikasi sukses
            toastr()
                ->closeOnHover(true)->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addSuccess("Delete SEP berhasil: {$code} {$message}");
        } else {
            // 4. Tampilkan notifikasi gagal
            toastr()
                ->closeOnHover(true)->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError("Delete SEP gagal: {$code} {$message}");
        }
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
