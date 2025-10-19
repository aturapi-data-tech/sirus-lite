<?php

namespace App\Http\Livewire\EmrRI\MrRI\Pemeriksaan;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Cache\LockTimeoutException;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

use App\Http\Traits\EmrRI\EmrRITrait;
use App\Http\Traits\customErrorMessagesTrait;

use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;


class Pemeriksaan extends Component
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
        // $this->store();
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
            $rows = DB::table('lbmst_clabitems')
                ->select('clabitem_desc', 'clabitem_id', 'price', 'clabitem_group', 'item_code')
                ->whereNull('clabitem_group')
                ->whereNotNull('clabitem_desc')
                ->orderBy('clabitem_desc', 'asc')
                ->get();

            $this->isPemeriksaanLaboratorium = $rows
                ->map(function ($r) {
                    return [
                        'clabitem_desc'  => $r->clabitem_desc,
                        'clabitem_id'    => $r->clabitem_id,
                        'price'          => $r->price,
                        'clabitem_group' => $r->clabitem_group,
                        'item_code'      => $r->item_code,
                        'labStatus'      => 0,
                    ];
                })
                ->values()
                ->all();
        }
    }

    public function PemeriksaanLaboratoriumIsSelectedFor($key): void
    {
        $this->isPemeriksaanLaboratorium[$key]['labStatus'] = $this->isPemeriksaanLaboratorium[$key]['labStatus'] ? 0 : 1;
        $this->renderPemeriksaanLaboratoriumIsSelected();
    }

    public function RemovePemeriksaanLaboratoriumIsSelectedFor($key): void
    {
        $this->isPemeriksaanLaboratorium[$key]['labStatus'] = $this->isPemeriksaanLaboratorium[$key]['labStatus'] ? 0 : 1;
        $this->renderPemeriksaanLaboratoriumIsSelected();
    }

    private function renderPemeriksaanLaboratoriumIsSelected(): void
    {
        $this->isPemeriksaanLaboratoriumSelected = collect($this->isPemeriksaanLaboratorium)
            ->where('labStatus', 1)->values()->all();
    }

    public function kirimLaboratorium()
    {
        $selected = collect($this->isPemeriksaanLaboratorium)->where('labStatus', 1)->values()->all();
        if (empty($selected)) {
            toastr()->positionClass('toast-top-left')->addError('Pilih minimal satu item laboratorium.');
            return;
        }

        $riHdrNo = $this->dataDaftarRi['riHdrNo'] ?? null;
        if (!$riHdrNo) {
            toastr()->positionClass('toast-top-left')->addError("riHdrNo kosong.");
            return;
        }

        try {
            $this->withRiLock($riHdrNo, function () use ($riHdrNo) {
                $checkStatusRI = DB::scalar(
                    "select ri_status from rstxn_rihdrs where rihdr_no=:riHdrNo",
                    ["riHdrNo" => $riHdrNo]
                );
                if ($checkStatusRI !== 'I') {
                    throw new \RuntimeException("Pasien Sudah Pulang, Anda tidak bisa meneruskan pemeriksaan ini.");
                }

                // generate nomor aman dalam transaksi+lock
                $checkupNo = DB::scalar("select nvl(max(to_number(checkup_no))+1,1) from lbtxn_checkuphdrs");

                // insert hdr
                DB::table('lbtxn_checkuphdrs')->insert([
                    'reg_no'        => $this->dataDaftarRi['regNo'],
                    'dr_id'         => $this->dataDaftarRi['drId'],
                    'checkup_date'  => DB::raw("to_date('" . Carbon::now(config('app.timezone'))->format('d/m/Y H:i:s') . "','dd/mm/yyyy hh24:mi:ss')"),
                    'status_rjri'   => 'RI',
                    'checkup_status' => 'P',
                    'ref_no'        => $riHdrNo,
                    'checkup_no'    => $checkupNo,
                ]);

                // daftar terpilih → array
                $selected = collect($this->isPemeriksaanLaboratorium)->where('labStatus', 1)->values()->all();

                // insert dtl utama + paket
                foreach ($selected as $labDtl) {
                    $checkupDtl = DB::scalar("select nvl(to_number(max(checkup_dtl))+1,1) from LBTXN_CHECKUPDTLS");
                    DB::table('lbtxn_checkupdtls')->insert([
                        'clabitem_id'  => $labDtl['clabitem_id'],
                        'checkup_no'   => $checkupNo,
                        'checkup_dtl'  => $checkupDtl,
                        'lab_item_code' => $labDtl['item_code'],
                        'price'        => $labDtl['price'],
                    ]);

                    // item paket (jika ada)
                    $items = DB::table('lbmst_clabitems')
                        ->select('clabitem_id', 'item_code', 'price')
                        ->where('clabitem_group', $labDtl['clabitem_id'])
                        ->orderBy('item_seq', 'asc')->orderBy('clabitem_desc', 'asc')
                        ->get();

                    foreach ($items as $item) {
                        $checkupDtl = DB::scalar("select nvl(to_number(max(checkup_dtl))+1,1) from LBTXN_CHECKUPDTLS");
                        DB::table('lbtxn_checkupdtls')->insert([
                            'clabitem_id'  => $item->clabitem_id,
                            'checkup_no'   => $checkupNo,
                            'checkup_dtl'  => $checkupDtl,
                            'lab_item_code' => $item->item_code,
                            'price'        => $item->price,
                        ]);
                    }
                }

                // patch JSON (subtree pemeriksaan) di state komponen
                $hdrIdx = collect($this->dataDaftarRi['pemeriksaan']['pemeriksaanPenunjang']['lab'] ?? [])->count();
                $this->dataDaftarRi['pemeriksaan']['pemeriksaanPenunjang']['lab'][$hdrIdx]['labHdr'] = [
                    'labHdrNo'   => $checkupNo,
                    'labHdrDate' => Carbon::now(config('app.timezone'))->format('d/m/Y H:i:s'),
                    'labDtl'     => $selected,
                ];

                // tulis JSON via store() tetapi JANGAN membuat lock baru: panggil langsung update di sini
                $fresh = $this->findDataRI($riHdrNo) ?: [];
                $fresh['pemeriksaan'] = $this->dataDaftarRi['pemeriksaan'] ?? [];
                $this->updateJsonRI($riHdrNo, $fresh);
                $this->dataDaftarRi = $fresh;
            });

            $this->closeModalLaboratorium();
            $this->emit('syncronizeAssessmentPerawatRIFindData');
            toastr()->positionClass('toast-top-left')->addSuccess("Pemeriksaan Lab terkirim.");
        } catch (\RuntimeException $e) {
            toastr()->positionClass('toast-top-left')->addError($e->getMessage());
        } catch (LockTimeoutException $e) {
            toastr()->positionClass('toast-top-left')->addError('Sistem sibuk, gagal memperoleh lock. Coba lagi.');
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
            $rows = DB::table('rsmst_radiologis')
                ->select('rad_desc', 'rad_price', 'rad_id')
                ->orderBy('rad_desc', 'asc')
                ->get();

            $this->isPemeriksaanRadiologi = $rows
                ->map(function ($r) {
                    return [
                        'rad_desc'  => $r->rad_desc,
                        'rad_price' => $r->rad_price,
                        'rad_id'    => $r->rad_id,
                        'radStatus' => 0,
                    ];
                })
                ->values()
                ->all();
        }
    }
    public function PemeriksaanRadiologiIsSelectedFor($key): void
    {
        $this->isPemeriksaanRadiologi[$key]['radStatus'] = $this->isPemeriksaanRadiologi[$key]['radStatus'] ? 0 : 1;
        $this->renderPemeriksaanRadiologiIsSelected();
    }

    public function RemovePemeriksaanRadiologiIsSelectedFor($key): void
    {
        $this->isPemeriksaanRadiologi[$key]['radStatus'] = $this->isPemeriksaanRadiologi[$key]['radStatus'] ? 0 : 1;
        $this->renderPemeriksaanRadiologiIsSelected();
    }

    private function renderPemeriksaanRadiologiIsSelected(): void
    {
        $this->isPemeriksaanRadiologiSelected = collect($this->isPemeriksaanRadiologi)
            ->where('radStatus', 1)->values()->all();
    }

    public function kirimRadiologi()
    {

        $selected = collect($this->isPemeriksaanRadiologi)->where('radStatus', 1)->values()->all();
        if (empty($selected)) {
            toastr()->positionClass('toast-top-left')->addError('Pilih minimal satu item radiologi.');
            return;
        }

        $riHdrNo = $this->dataDaftarRi['riHdrNo'] ?? null;
        if (!$riHdrNo) {
            toastr()->positionClass('toast-top-left')->addError("riHdrNo kosong.");
            return;
        }

        try {
            $this->withRiLock($riHdrNo, function () use ($riHdrNo) {
                $checkStatusRI = DB::scalar(
                    "select ri_status from rstxn_rihdrs where rihdr_no=:riHdrNo",
                    ["riHdrNo" => $riHdrNo]
                );
                if ($checkStatusRI !== 'I') {
                    throw new \RuntimeException("Pasien Sudah Pulang, Anda tidak bisa meneruskan pemeriksaan ini.");
                }

                $selected = collect($this->isPemeriksaanRadiologi)->where('radStatus', 1)->values()->all();

                foreach ($selected as $radDtl) {
                    $riradNo = DB::scalar("select nvl(max(rirad_no)+1,1) from rstxn_riradiologs");
                    DB::table('rstxn_riradiologs')->insert([
                        'rirad_no'     => $riradNo,
                        'rad_id'       => $radDtl['rad_id'],
                        'rihdr_no'     => $riHdrNo,
                        'rirad_price'  => $radDtl['rad_price'],
                        'dr_radiologi' => 'dr. M.A. Budi Purwito, Sp.Rad.',
                        'waktu_entry'  => DB::raw("to_date('" . Carbon::now(config('app.timezone'))->format('d/m/Y H:i:s') . "','dd/mm/yyyy hh24:mi:ss')"),
                        'rirad_date'   => DB::raw("to_date('" . Carbon::now(config('app.timezone'))->format('d/m/Y H:i:s') . "','dd/mm/yyyy hh24:mi:ss')"),
                    ]);
                }

                // patch JSON
                $hdrIdx = collect($this->dataDaftarRi['pemeriksaan']['pemeriksaanPenunjang']['rad'] ?? [])->count();
                $this->dataDaftarRi['pemeriksaan']['pemeriksaanPenunjang']['rad'][$hdrIdx]['radHdr'] = [
                    'radHdrNo'   => $riHdrNo,
                    'radHdrDate' => Carbon::now(config('app.timezone'))->format('d/m/Y H:i:s'),
                    'radDtl'     => $selected,
                ];

                $fresh = $this->findDataRI($riHdrNo) ?: [];
                $fresh['pemeriksaan'] = $this->dataDaftarRi['pemeriksaan'] ?? [];
                $this->updateJsonRI($riHdrNo, $fresh);
                $this->dataDaftarRi = $fresh;
            });

            $this->closeModalRadiologi();
            $this->emit('syncronizeAssessmentPerawatRIFindData');
            toastr()->positionClass('toast-top-left')->addSuccess("Pemeriksaan Radiologi terkirim.");
        } catch (\RuntimeException $e) {
            toastr()->positionClass('toast-top-left')->addError($e->getMessage());
        } catch (LockTimeoutException $e) {
            toastr()->positionClass('toast-top-left')->addError('Sistem sibuk, gagal memperoleh lock. Coba lagi.');
        }
    }
    // Rad




    // ////////////////
    // RJ Logic
    // ////////////////




    // insert and update record start////////////////
    public function store()
    {

        // Ambil riHdrNo dari state sekarang (fallback ke ref)
        $riHdrNo = $this->dataDaftarRi['riHdrNo'] ?? $this->riHdrNoRef ?? null;
        if (!$riHdrNo) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError("riHdrNo kosong.");
            return;
        }

        $lockKey = "ri:{$riHdrNo}";

        try {
            // TTL 60s, tunggu hingga 10s
            Cache::lock($lockKey, 60)->block(10, function () use ($riHdrNo) {
                $fresh = $this->findDataRI($riHdrNo) ?: [];

                // Guard: pastikan bentuk array
                $freshPemeriksaan   = is_array($fresh['pemeriksaan'] ?? null) ? $fresh['pemeriksaan'] : [];
                $incomingPemeriksaan = is_array($this->dataDaftarRi['pemeriksaan'] ?? null) ? $this->dataDaftarRi['pemeriksaan'] : [];

                // Deep merge (overwrite hanya key yang dikirim user)
                $fresh['pemeriksaan'] = array_replace_recursive($freshPemeriksaan, $incomingPemeriksaan);

                DB::transaction(fn() => $this->updateJsonRI($riHdrNo, $fresh));

                // Sinkronkan state komponen
                $this->dataDaftarRi = $fresh;
            });
        } catch (LockTimeoutException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Sistem sibuk, gagal memperoleh lock. Coba lagi.');
            return;
        } catch (\Throwable $e) { // opsional, biar aman
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Gagal menyimpan: ' . $e->getMessage());
            return;
        }

        // Broadcast ke komponen lain agar reload dari DB
        $this->emit('syncronizeAssessmentPerawatRIFindData');

        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
            ->addSuccess("Pemeriksaan berhasil disimpan.");
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
        $bb = (float) data_get($this->dataDaftarRi, 'pemeriksaan.nutrisi.bb', 0);
        $tb = (float) data_get($this->dataDaftarRi, 'pemeriksaan.nutrisi.tb', 0);
        if ($bb > 0 && $tb > 0) {
            $m = $tb / 100;
            $this->dataDaftarRi['pemeriksaan']['nutrisi']['imt'] = round($bb / ($m * $m), 2);
        } else {
            $this->dataDaftarRi['pemeriksaan']['nutrisi']['imt'] = null;
        }
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
            'tglUpload' => Carbon::now(config('app.timezone'))->format('d/m/Y H:i:s'),
            'penanggungJawab' => [
                'userLog' => auth()->user()->myuser_name,
                'userLogDate' => Carbon::now(config('app.timezone'))->format('d/m/Y H:i:s'),
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

    private function withRiLock(string $riHdrNo, callable $fn): void
    {
        $lockKey = "ri:{$riHdrNo}";
        Cache::lock($lockKey, 60)->block(10, function () use ($fn) {
            DB::transaction(function () use ($fn) {
                $fn();
            });
        });
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
            'livewire.emr-r-i.mr-r-i.pemeriksaan.pemeriksaan',
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
