<?php

namespace App\Http\Livewire\EmrRJ\MrRJ\Perencanaan;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;

use Carbon\Carbon;

use Spatie\ArrayToXml\ArrayToXml;
use App\Http\Traits\EmrRJ\EmrRJTrait;
use Exception;

class Perencanaan extends Component
{
    use WithPagination, EmrRJTrait;

    // listener from blade////////////////
    protected $listeners = [
        'syncronizeAssessmentPerawatRJFindData' => 'mount',
    ];

    //////////////////////////////
    // Ref on top bar
    //////////////////////////////
    public $rjNoRef;

    // dataDaftarPoliRJ RJ
    public array $dataDaftarPoliRJ = [];

    // data SKDP / perencanaan=>[]
    public array $perencanaan = [
        'terapiTab' => 'Terapi',
        'terapi' => [
            'terapi' => '',
        ],

        'tindakLanjutTab' => 'Tindak Lanjut',
        'tindakLanjut' => [
            'tindakLanjut' => '',
            'keteranganTindakLanjut' => '',
            'tindakLanjutOptions' => [['tindakLanjut' => 'MRS'], ['tindakLanjut' => 'Kontrol'], ['tindakLanjut' => 'Rujuk'], ['tindakLanjut' => 'Perawatan Selesai'], ['tindakLanjut' => 'PRB'], ['tindakLanjut' => 'Lain-lain']],
        ],

        'pengkajianMedisTab' => 'Petugas Medis',
        'pengkajianMedis' => [
            'waktuPemeriksaan' => '',
            'selesaiPemeriksaan' => '',
            'drPemeriksa' => '',
        ],
        // Kontrol pakai program lama

        'rawatInapTab' => 'Rawat Inap',
        'rawatInap' => [
            'noRef' => '',
            'tanggal' => '', //dd/mm/yyyy
            'keterangan' => '',
        ],

        'dischargePlanningTab' => 'Discharge Planning',
        'dischargePlanning' => [
            'pelayananBerkelanjutan' => [
                'pelayananBerkelanjutan' => 'Tidak Ada',
                'pelayananBerkelanjutanOption' => [['pelayananBerkelanjutan' => 'Tidak Ada'], ['pelayananBerkelanjutan' => 'Ada']],
            ],
            'pelayananBerkelanjutanOpsi' => [
                'rawatLuka' => [],
                'dm' => [],
                'ppok' => [],
                'hivAids' => [],
                'dmTerapiInsulin' => [],
                'ckd' => [],
                'tb' => [],
                'stroke' => [],
                'kemoterapi' => [],
            ],

            'penggunaanAlatBantu' => [
                'penggunaanAlatBantu' => 'Tidak Ada',
                'penggunaanAlatBantuOption' => [['penggunaanAlatBantu' => 'Tidak Ada'], ['penggunaanAlatBantu' => 'Ada']],
            ],
            'penggunaanAlatBantuOpsi' => [
                'kateterUrin' => [],
                'ngt' => [],
                'traechotomy' => [],
                'colostomy' => [],
            ],
        ],
    ];
    //////////////////////////////////////////////////////////////////////

    protected $rules = [
        // 'dataDaftarPoliRJ.perencanaan.pengkajianMedis.waktuPemeriksaan' => 'required|date_format:d/m/Y H:i:s',
        // 'dataDaftarPoliRJ.perencanaan.pengkajianMedis.selesaiPemeriksaan' => 'required|date_format:d/m/Y H:i:s'
        'dataDaftarPoliRJ.perencanaan.pengkajianMedis.drPemeriksa' => '',
    ];

    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////
    public function updated($propertyName)
    {
        // dd($propertyName);
        $this->validateOnly($propertyName);
        if ($propertyName != 'activeTabRacikanNonRacikan') {
            $this->store();
        }
    }

    // resert input private////////////////
    private function resetInputFields(): void
    {
        // resert validation
        $this->resetValidation();
        // resert input kecuali
        $this->resetExcept(['rjNoRef']);
    }

    // ////////////////
    // RJ Logic
    // ////////////////

    // validate Data RJ//////////////////////////////////////////////////
    private function validateDataRJ(): void
    {
        // customErrorMessages
        // $messages = customErrorMessagesTrait::messages();
        $messages = [];

        // $rules = [];

        // Proses Validasi///////////////////////////////////////////
        try {
            $this->validate($this->rules, $messages);
        } catch (\Illuminate\Validation\ValidationException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Lakukan Pengecekan kembali Input Data.');
            $this->validate($this->rules, $messages);
        }
    }

    // insert and update record start////////////////
    public function store()
    {
        // set data RJno / NoBooking / NoAntrian / klaimId / kunjunganId
        $this->setDataPrimer();

        // Validate RJ
        $this->validateDataRJ();

        // Logic update mode start //////////
        $this->updateDataRj($this->dataDaftarPoliRJ['rjNo'], $this->dataDaftarPoliRJ);
        $this->emit('syncronizeAssessmentPerawatRJFindData');
    }


    // insert and update record end////////////////

    private function findData($rjno): void
    {
        $findDataRJ = $this->findDataRJ($rjno);
        $this->dataDaftarPoliRJ  = $findDataRJ['dataDaftarRJ'];

        // jika perencanaan tidak ditemukan tambah variable perencanaan pda array
        if (isset($this->dataDaftarPoliRJ['perencanaan']) == false) {
            $this->dataDaftarPoliRJ['perencanaan'] = $this->perencanaan;
        }
    }

    // set data RJno / NoBooking / NoAntrian / klaimId / kunjunganId
    private function setDataPrimer(): void {}

    public function setWaktuPemeriksaan($myTime)
    {
        $this->dataDaftarPoliRJ['perencanaan']['pengkajianMedis']['waktuPemeriksaan'] = $myTime;
    }

    public function setSelesaiPemeriksaan($myTime)
    {
        $this->dataDaftarPoliRJ['perencanaan']['pengkajianMedis']['selesaiPemeriksaan'] = $myTime;
    }

    private function validateDrPemeriksa()
    {
        // Validasi dulu
        $messages = [];
        $myRules = [
            // 'dataDaftarPoliRJ.pemeriksaan.tandaVital.sistolik' => 'required|numeric',
            // 'dataDaftarPoliRJ.pemeriksaan.tandaVital.distolik' => 'required|numeric',
            'dataDaftarPoliRJ.pemeriksaan.tandaVital.frekuensiNadi' => 'required|numeric',
            'dataDaftarPoliRJ.pemeriksaan.tandaVital.frekuensiNafas' => 'required|numeric',
            'dataDaftarPoliRJ.pemeriksaan.tandaVital.suhu' => 'required|numeric',
            'dataDaftarPoliRJ.pemeriksaan.tandaVital.spo2' => 'numeric',
            'dataDaftarPoliRJ.pemeriksaan.tandaVital.gda' => 'numeric',

            'dataDaftarPoliRJ.pemeriksaan.nutrisi.bb' => 'required|numeric',
            'dataDaftarPoliRJ.pemeriksaan.nutrisi.tb' => 'required|numeric',
            'dataDaftarPoliRJ.pemeriksaan.nutrisi.imt' => 'required|numeric',
            'dataDaftarPoliRJ.pemeriksaan.nutrisi.lk' => 'numeric',
            'dataDaftarPoliRJ.pemeriksaan.nutrisi.lila' => 'numeric',

            'dataDaftarPoliRJ.anamnesa.pengkajianPerawatan.jamDatang' => 'required|date_format:d/m/Y H:i:s',
        ];
        // Proses Validasi///////////////////////////////////////////
        try {
            $this->validate($myRules, $messages);
        } catch (\Illuminate\Validation\ValidationException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Anda tidak dapat melakukan TTD-E karena data pemeriksaan belum lengkap.');
            $this->validate($myRules, $messages);
        }
        // Validasi dulu
    }
    public function setDrPemeriksa()
    {
        // $myRoles = json_decode(auth()->user()->roles, true);
        $myUserCodeActive = auth()->user()->myuser_code;
        $myUserNameActive = auth()->user()->myuser_name;
        // $myUserTtdActive = auth()->user()->myuser_ttd_image;

        // Validasi dulu
        // cek apakah data pemeriksaan sudah dimasukkan atau blm
        $this->validateDrPemeriksa();

        if (auth()->user()->hasRole('Dokter')) {
            if ($this->dataDaftarPoliRJ['drId'] == $myUserCodeActive) {
                if (isset($this->dataDaftarPoliRJ['perencanaan']['pengkajianMedis']['drPemeriksa'])) {
                    if (!$this->dataDaftarPoliRJ['perencanaan']['pengkajianMedis']['drPemeriksa']) {
                        $this->dataDaftarPoliRJ['perencanaan']['pengkajianMedis']['drPemeriksa'] = isset($this->dataDaftarPoliRJ['drDesc']) ? ($this->dataDaftarPoliRJ['drDesc'] ? $this->dataDaftarPoliRJ['drDesc'] : 'Dokter pemeriksa') : 'Dokter pemeriksa-';
                    }
                } else {
                    $this->dataDaftarPoliRJ['perencanaan']['pengkajianMedisTab'] = 'Pengkajian Medis';
                    $this->dataDaftarPoliRJ['perencanaan']['pengkajianMedis']['drPemeriksa'] = isset($this->dataDaftarPoliRJ['drDesc']) ? ($this->dataDaftarPoliRJ['drDesc'] ? $this->dataDaftarPoliRJ['drDesc'] : 'Dokter pemeriksa') : 'Dokter pemeriksa-';
                }

                // updateDB
                $this->dataDaftarPoliRJ['ermStatus'] = 'L';
                DB::table('rstxn_rjhdrs')
                    ->where('rj_no', '=', $this->rjNoRef)
                    ->update(['erm_status' => $this->dataDaftarPoliRJ['ermStatus']]);
                $this->store();
            } else {
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Anda tidak dapat melakukan TTD-E karena Bukan Pasien ' . $myUserNameActive);
            }
        } else {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Anda tidak dapat melakukan TTD-E karena User Role ' . $myUserNameActive . ' Bukan Dokter');
        }
    }

    // /////////////////eresep open////////////////////////
    public bool $isOpenEresepRJ = false;
    public string $isOpenModeEresepRJ = 'insert';

    public function openModalEresepRJ(): void
    {
        $this->isOpenEresepRJ = true;
        $this->isOpenModeEresepRJ = 'insert';
    }

    public function closeModalEresepRJ(): void
    {
        $this->isOpenEresepRJ = false;
        $this->isOpenModeEresepRJ = 'insert';
    }

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

    public function simpanTerapi(): void
    {
        $eresep = '' . PHP_EOL;
        if (isset($this->dataDaftarPoliRJ['eresep'])) {

            foreach ($this->dataDaftarPoliRJ['eresep'] as $key => $value) {
                // NonRacikan
                $catatanKhusus = ($value['catatanKhusus']) ? ' (' . $value['catatanKhusus'] . ')' : '';
                $eresep .=  'R/' . ' ' . $value['productName'] . ' | No. ' . $value['qty'] . ' | S ' .  $value['signaX'] . 'dd' . $value['signaHari'] . $catatanKhusus . PHP_EOL;
            }
        }

        $eresepRacikan = '' . PHP_EOL;
        if (isset($this->dataDaftarPoliRJ['eresepRacikan'])) {
            // Racikan
            foreach ($this->dataDaftarPoliRJ['eresepRacikan'] as $key => $value) {
                if (isset($value['jenisKeterangan'])) {
                    $catatan = isset($value['catatan']) ? $value['catatan'] : '';
                    $catatanKhusus = isset($value['catatanKhusus']) ? $value['catatanKhusus'] : '';
                    $noRacikan = isset($value['noRacikan']) ? $value['noRacikan'] : '';
                    $productName = isset($value['productName']) ? $value['productName'] : '';

                    $jmlRacikan = ($value['qty']) ? 'Jml Racikan ' . $value['qty'] . ' | ' . $catatan . ' | S ' . $catatanKhusus . PHP_EOL : '';
                    $dosis = isset($value['dosis']) ? ($value['dosis'] ? $value['dosis'] : '') : '';
                    $eresepRacikan .= $noRacikan . '/ ' . $productName . ' - ' . $dosis .  PHP_EOL . $jmlRacikan;
                }
            };
        }
        $this->dataDaftarPoliRJ['perencanaan']['terapi']['terapi'] = $eresep . $eresepRacikan;

        $this->store();
        $this->closeModalEresepRJ();
    }

    public function setstatusPRB()
    {

        // status PRB
        if (isset($this->dataDaftarPoliRJ['statusPRB']['penanggungJawab']['statusPRB'])) {
            if ($this->dataDaftarPoliRJ['statusPRB']['penanggungJawab']['statusPRB']) {
                $statusPRB = 0;
                $this->dataDaftarPoliRJ['perencanaan']['tindakLanjut']['tindakLanjut'] = '';
            } else {
                $statusPRB = 1;
                $this->dataDaftarPoliRJ['perencanaan']['tindakLanjut']['tindakLanjut'] = 'PRB';
            }
        } else {
            $statusPRB = 1;
        }

        // setStatusPRB
        $this->dataDaftarPoliRJ['statusPRB']['penanggungJawab'] = [
            'statusPRB' => $statusPRB,
            'userLog' => auth()->user()->myuser_name,
            'userLogDate' => Carbon::now(env('APP_TIMEZONE'))->format('d/m/Y H:i:s'),
            'userLogCode' => auth()->user()->myuser_code
        ];

        // simpan
        $this->store();
    }
    // /////////////////////////////////////////

    public function copyResepFromTemplate($rjNoRefCopyTo,  $tempJsonNonRacikan, $tempJsonRacikan)
    {
        // cek status transaksi
        $checkRjStatus = $this->checkRjStatus($rjNoRefCopyTo);
        if ($checkRjStatus) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Pasien Sudah Pulang, Trasaksi Terkunci.");
            return;
        }

        // 1 cari data to
        // 2 cari data from
        $to = $this->cariResepRJ($rjNoRefCopyTo);
        $from['eresep'] = json_decode($tempJsonNonRacikan, true)['eresep'] ?? [];
        $from['eresepRacikan'] = json_decode($tempJsonRacikan, true)['eresepRacikan'] ?? [];

        $myResepFrom = $this->prosesDataArray(isset($from['eresep']) ? $from['eresep'] : [], 'EResepFrom');

        // 3 if eresep from (true) then update data eresep to
        if ($myResepFrom) {

            // hapus data obat
            try {
                // remove into table transaksi
                DB::table('rstxn_rjobats')
                    ->where('rj_no', $rjNoRefCopyTo)
                    ->delete();
            } catch (Exception $e) {
                // display an error to user
                // dd($e->getMessage());
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError($e->getMessage());
                return;
            }

            $to['eresep'] = $myResepFrom;
            // insert data obat loop
            try {
                // ?\
                // select nvl(max(rjobat_dtl)+1,1) into :rstxn_rjobats.rjobat_dtl from rstxn_rjobats;
                foreach ($to['eresep']  as $key => $toEresep) {

                    $lastInserted = DB::table('rstxn_rjobats')
                        ->select(DB::raw("nvl(max(rjobat_dtl)+1,1) as rjobat_dtl_max"))
                        ->first();
                    $productPrice = DB::table('immst_products')
                        ->select('sales_price')
                        ->where('product_id', $toEresep['productId'])
                        ->first()->sales_price ?? 0;

                    // insert into table transaksi
                    DB::table('rstxn_rjobats')
                        ->insert([
                            'rjobat_dtl' => $lastInserted->rjobat_dtl_max,
                            'rj_no' => $rjNoRefCopyTo,
                            'product_id' => $toEresep['productId'],
                            'qty' => $toEresep['qty'],
                            'price' => $productPrice,
                            'rj_carapakai' => $toEresep['signaX'],
                            'rj_kapsul' => $toEresep['signaHari'],
                            'rj_takar' => 'Tablet',
                            'catatan_khusus' => $toEresep['catatanKhusus'],
                            'rj_ket' => $toEresep['catatanKhusus'],
                            'exp_date' => DB::raw("to_date('" . $to['rjDate'] . "','dd/mm/yyyy hh24:mi:ss')+30"),
                            'etiket_status' => 1,
                        ]);

                    // replace rjobatdtl dan rjno ke obat yang baru
                    $to['eresep'][$key]['rjObatDtl'] = $lastInserted->rjobat_dtl_max;
                    $to['eresep'][$key]['rjNo'] = $rjNoRefCopyTo;
                    unset($to['eresep'][$key]['temprId']);
                }
                //
            } catch (Exception $e) {
                // display an error to user
                // dd($e->getMessage());
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError($e->getMessage());
                return;
            }

            // 4update database to
        }


        // racikan

        $myResepRacikanFrom = $this->prosesDataArray(isset($from['eresepRacikan']) ? $from['eresepRacikan'] : [], 'EResepRacikanFrom');

        // 3if eresepRacikan from (true) then update data eresepRacikan to
        if ($myResepRacikanFrom) {

            // hapus data obat
            try {
                // remove into table transaksi
                DB::table('rstxn_rjobatracikans')
                    ->where('rj_no', $rjNoRefCopyTo)
                    ->delete();
            } catch (Exception $e) {
                // display an error to user
                // dd($e->getMessage());

                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError($e->getMessage());
                return;
            }

            $to['eresepRacikan'] = $myResepRacikanFrom;
            // insert data obat loop
            try {
                // ?\
                // select nvl(max(rjobat_dtl)+1,1) into :rstxn_rjobatracikans.rjobat_dtl from rstxn_rjobatracikans;
                foreach ($to['eresepRacikan']  as $key => $toEresepRacikan) {
                    if (isset($toEresepRacikan['jenisKeterangan'])) {
                        $lastInserted = DB::table('rstxn_rjobatracikans')
                            ->select(DB::raw("nvl(max(rjobat_dtl)+1,1) as rjobat_dtl_max"))
                            ->first();
                        // insert into table transaksi
                        DB::table('rstxn_rjobatracikans')
                            ->insert([
                                'rjobat_dtl' => $lastInserted->rjobat_dtl_max,
                                'rj_no' => $rjNoRefCopyTo,
                                // 'product_id' => $toEresepRacikan['productId'],
                                'product_name' => $toEresepRacikan['productName'],
                                'sedia' => $toEresepRacikan['sedia'],
                                'dosis' => isset($toEresepRacikan['dosis']) ? ($toEresepRacikan['dosis'] ? $toEresepRacikan['dosis'] : '') : '',
                                'qty' => $toEresepRacikan['qty'],
                                // 'price' => $toEresepRacikan['productPrice'],
                                // 'rj_carapakai' => $toEresepRacikan['signaX'],
                                // 'rj_kapsul' => $toEresepRacikan['signaHari'],
                                'catatan' => $toEresepRacikan['catatan'],
                                'catatan_khusus' => $toEresepRacikan['catatanKhusus'],
                                'no_racikan' => $toEresepRacikan['noRacikan'],

                                'rj_takar' => 'Tablet',
                                'exp_date' => DB::raw("to_date('" . $to['rjDate'] . "','dd/mm/yyyy hh24:mi:ss')+30"),
                                'etiket_status' => 1,
                            ]);

                        // replace rjobatdtl dan rjno ke obat yang baru
                        $to['eresepRacikan'][$key]['rjObatDtl'] = $lastInserted->rjobat_dtl_max;
                        $to['eresepRacikan'][$key]['rjNo'] = $rjNoRefCopyTo;
                        unset($to['eresepRacikan'][$key]['temprId']);
                    }
                }
                //
            } catch (Exception $e) {
                // display an error to user
                // dd($e->getMessage());

                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError($e->getMessage());
                return;
            }

            // 4update database to
        }






        // Jika array perencanaan belum terbentuk
        if (!isset($to['perencanaan'])) {
            $this->emit('storeAssessmentDokterRJPerencanaan');
            $this->emit('syncronizeAssessmentDokterRJFindData');
            $this->emit('syncronizeAssessmentPerawatRJFindData');
            $to = $this->cariResepRJ($rjNoRefCopyTo);
        }

        // terapi
        $eresep = '';
        if (isset($to['eresep'])) {
            foreach ($to['eresep'] as  $value) {
                $catatanKhusus = ($value['catatanKhusus']) ? ' (' . $value['catatanKhusus'] . ')' : '';
                $eresep .=  'R/' . ' ' . $value['productName'] . ' | No. ' . $value['qty'] . ' | S ' .  $value['signaX'] . 'dd' . $value['signaHari'] . $catatanKhusus . PHP_EOL;
            }
            // $to['perencanaan']['terapi']['terapi'] = $eresep;
        }

        // dd($to['eresepRacikan']);
        $eresepRacikan = '' . PHP_EOL;
        if (isset($to['eresepRacikan'])) {
            foreach ($to['eresepRacikan'] as  $value) {
                if (isset($value['jenisKeterangan'])) {
                    $catatan = isset($value['catatan']) ? $value['catatan'] : '';
                    $catatanKhusus = isset($value['catatanKhusus']) ? $value['catatanKhusus'] : '';
                    $noRacikan = isset($value['noRacikan']) ? $value['noRacikan'] : '';
                    $productName = isset($value['productName']) ? $value['productName'] : '';


                    $jmlRacikan = ($value['qty']) ? 'Jml Racikan ' . $value['qty'] . ' | ' . $catatan . ' | S ' . $catatanKhusus . PHP_EOL : '';
                    $dosis = isset($value['dosis']) ? ($value['dosis'] ? $value['dosis'] : '') : '';
                    $eresepRacikan .= $noRacikan . '/ ' . $productName . ' - ' . $dosis .  PHP_EOL . $jmlRacikan;
                }
            }
            // $to['perencanaan']['terapi']['terapi'] = $eresepRacikan;
        }
        $to['perencanaan']['terapi']['terapi'] = $eresep . $eresepRacikan;


        $this->updateDataRj($rjNoRefCopyTo, $to);
    }

    private function prosesDataArray(array $arr, string $arrName)
    {
        $myArr = $arr ? $arr : [];

        if (!$myArr) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Data " . $arrName . " tidak ditemukan.");
            return;
        }

        return $myArr;
    }

    private function cariResepRJ($rjNo): array
    {
        // update table trnsaksi
        $cari = DB::table('rstxn_rjhdrs')
            ->select('datadaftarpolirj_json')
            ->where('rj_no', $rjNo)
            ->first();

        if (isset($cari->datadaftarpolirj_json)) {
            if ($cari->datadaftarpolirj_json) {
                return json_decode($cari->datadaftarpolirj_json, true);
            }
        }
        return [];
    }

    private function updateDataRj($rjNo, $dataDaftarPoliRJArr): void
    {
        // if ($rjNo !== $dataDaftarPoliRJArr['rjNo']) {
        //     dd('Data Json Tidak sesuai' . $rjNo . '  /  ' . $dataDaftarPoliRJArr['rjNo']);
        // }

        // // update table trnsaksi
        // DB::table('rstxn_rjhdrs')
        //     ->where('rj_no', $rjNo)
        //     ->update([
        //         'datadaftarpolirj_json' => json_encode($dataDaftarPoliRJArr, true),
        //         'datadaftarpolirj_xml' => ArrayToXml::convert($dataDaftarPoliRJArr),
        //     ]);

        $this->updateJsonRJ($rjNo, $dataDaftarPoliRJArr);


        $this->emit('syncronizeAssessmentDokterRJFindData');
        $this->emit('syncronizeAssessmentPerawatRJFindData');
        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Data Resep berhasil disimpan.");
    }






    // when new form instance
    public function mount()
    {
        $this->findData($this->rjNoRef);
    }

    // select data start////////////////
    public function render()
    {
        return view('livewire.emr-r-j.mr-r-j.perencanaan.perencanaan', [
            // 'RJpasiens' => $query->paginate($this->limitPerPage),
            'myTitle' => 'Perencanaan',
            'mySnipt' => 'Rekam Medis Pasien',
            'myProgram' => 'Pasien Rawat Jalan',
        ]);
    }
    // select data end////////////////
}
