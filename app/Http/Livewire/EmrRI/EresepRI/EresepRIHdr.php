<?php

namespace App\Http\Livewire\EmrRI\EresepRI;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use App\Http\Traits\EmrRI\EmrRITrait;
use Exception;


class EresepRIHdr extends Component
{
    use WithPagination, EmrRITrait;

    // listener from blade////////////////
    protected $listeners = [
        'syncronizeAssessmentDokterRIFindData' => 'mount',
        'syncronizeAssessmentPerawatRIFindData' => 'mount'

    ];

    //////////////////////////////
    // Ref on top bar
    //////////////////////////////
    public $riHdrNoRef;
    public $riStatusRef;
    public $resepIndexRef;

    // dataDaftarRi RI
    public array $dataDaftarRi = [];

    // LOV Nested
    public array $product;
    // LOV Nested

    public $formEntryEresepRIHdr = [
        'regNo'     => '',
        'riHdrNo'   => '',
        'resepDate' => '',
        'resepNo'   => '',
    ];


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

    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////

    public function insertResepHdr(): void
    {
        $this->checkRiStatus();

        // Proses Validasi///////////////////////////////////////////
        $rules = [
            'formEntryEresepRIHdr.regNo'    => 'bail|required|exists:rsmst_pasiens,reg_no',
            'formEntryEresepRIHdr.riHdrNo'  => 'bail|required|exists:rstxn_rihdrs,rihdr_no',
            'formEntryEresepRIHdr.resepDate' => 'bail|required|date_format:"d/m/Y H:i:s"',
            'formEntryEresepRIHdr.resepNo' => 'bail|required',
        ];

        $messages = [
            'formEntryEresepRIHdr.regNo.required'    => 'Nomor registrasi pasien wajib diisi.',
            'formEntryEresepRIHdr.regNo.exists'      => 'Nomor registrasi pasien tidak terdaftar.',
            'formEntryEresepRIHdr.riHdrNo.required'  => 'Nomor header RI wajib diisi.',
            'formEntryEresepRIHdr.riHdrNo.exists'    => 'Nomor header RI tidak ditemukan.',
            'formEntryEresepRIHdr.resepDate.required'    => 'Tanggal resep wajib diisi.',
            'formEntryEresepRIHdr.resepDate.date_format' => 'Format tanggal resep harus "d/m/Y H:i:s".',
            'formEntryEresepRIHdr.resepNo.required'  => 'Nomor resep wajib diisi.',
        ];

        // Proses validasi
        try {
            $this->validate($rules, $messages);
        } catch (\Illuminate\Validation\ValidationException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Periksa kembali input data. " . $e->getMessage());
            $this->validate($rules, $messages);
        }

        // start:
        try {

            $lastInserted = count($this->dataDaftarRi['eresepHdr'] ?? []) + 1;
            $this->dataDaftarRi['eresepHdr'][] = [
                'regNo' => $this->formEntryEresepRIHdr['regNo'],
                'riHdrNo' => $this->riHdrNoRef,
                'resepDate' => $this->formEntryEresepRIHdr['resepDate'],
                'resepNo' => $this->formEntryEresepRIHdr['resepNo'] ?? $lastInserted,
            ];

            $this->store();
            // $this->reset(['formEntryEresepRIHdr']);

            //
        } catch (Exception $e) {
            // display an error to user
            dd($e->getMessage());
        }
    }

    public function removeResepHdr($resepNo)
    {
        $this->checkRiStatus();

        try {
            // Cari header resep berdasarkan resepNo
            $header = collect($this->dataDaftarRi['eresepHdr'])->firstWhere('resepNo', $resepNo);

            // Jika header ditemukan dan sudah ada tanda tangan dokter, batalkan penghapusan
            if ($header && !empty($header['tandaTanganDokter']['dokterPeresep'] ?? null)) {
                toastr()->closeOnHover(true)
                    ->closeDuration(3)
                    ->positionClass('toast-top-left')
                    ->addError("Resep sudah ditandatangani dokter, tidak bisa dihapus.");
                return;
            }

            // Hapus header resep yang sesuai dan reindex array
            $this->dataDaftarRi['eresepHdr'] = collect($this->dataDaftarRi['eresepHdr'])
                ->where('resepNo', '!=', $resepNo)
                ->values()
                ->toArray();

            $this->store();
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }




    public function store()
    {
        // Logic update mode start //////////
        $this->updateDataRI($this->dataDaftarRi['riHdrNo']);
        $this->emit('syncronizeAssessmentDokterRIFindData');
        $this->emit('syncronizeAssessmentPerawatRIFindData');
    }

    private function updateDataRI($riHdrNo): void
    {
        $this->updateJsonRI($riHdrNo, $this->dataDaftarRi);
        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess('Eresep berhasil disimpan.');
    }
    // insert and update record end////////////////




    public function resetFormEntryEresepRIHdr()
    {
        $this->reset(['formEntryEresepRIHdr']);
    }

    public function checkRiStatus()
    {
        $lastInserted = DB::table('rstxn_rihdrs')
            ->select('ri_status')
            ->where('rihdr_no', $this->riHdrNoRef)
            ->first();

        if ($lastInserted->ri_status != 'I') {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError('Pasien Sudah Pulang, Trasaksi Terkunci.');
            return dd('Pasien Sudah Pulang, Trasaksi Terkuncixx.');
        }
    }


    public function setDokterPeresep($resepNo): void
    {


        try {
            // Cari index header sesuai $resepNo
            $idx = collect($this->dataDaftarRi['eresepHdr'] ?? [])
                ->search(fn($h) => (string)($h['resepNo'] ?? '') === (string)$resepNo);

            if ($idx === false) {
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Header resep #{$resepNo} tidak ditemukan.");
                return;
            }

            // Kalau sudah pernah kirim ke apotek (punya slsNo), jangan kirim lagi
            if (!empty($this->dataDaftarRi['eresepHdr'][$idx]['slsNo'])) {
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addInfo("Resep #{$resepNo} sudah terkirim ke apotek.");
                return;
            }

            // Validasi role
            $user = auth()->user();
            if (!$user->hasAnyRole(['Dokter', 'Admin'])) {
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("User {$user->myuser_name} bukan Dokter.");
                return;
            }

            // Set TTD dokter
            $this->dataDaftarRi['eresepHdr'][$idx]['tandaTanganDokter'] = [
                'dokterPeresep'     => $user->myuser_name,
                'dokterPeresepCode' => $user->myuser_code,
            ];

            // Ambil nilai penting
            $hdr               = $this->dataDaftarRi['eresepHdr'][$idx];
            $resepDate         = $hdr['resepDate'];
            $regNo             = $hdr['regNo'];
            $riHdrNo           = $hdr['riHdrNo'];
            $dokterPeresepCode = $this->dataDaftarRi['eresepHdr'][$idx]['tandaTanganDokter']['dokterPeresepCode'];
            $dataObat          = $hdr['eresep'] ?? [];

            // Kirim ke apotek (pastikan fungsi ini idempoten)
            $this->sendDataToApotek($resepDate, $regNo, $riHdrNo, $dokterPeresepCode, $dataObat, $idx);

            // Simpan state jika perlu
            $this->store();

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("TTD & kirim resep #{$resepNo} sukses.");
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Gagal: " . $e->getMessage());
        }
    }

    private function sendDataToApotek($resepDate, $regNo, $riHdrNo, $drId, $dataObat, $indexKe)
    {
        // Validasi: Pastikan $dataObat merupakan array dan tidak kosong
        if (!is_array($dataObat)) {
            throw new Exception("Data obat tidak valid.");
        }

        // Validasi: Pastikan regNo ada di tabel rsmst_pasiens
        $pasien = DB::table('rsmst_pasiens')->where('reg_no', $regNo)->first();
        if (!$pasien) {
            throw new Exception("Data pasien dengan reg_no {$regNo} tidak ditemukan.");
        }

        // Validasi: Pastikan drId ada di tabel rsmst_doctors
        $dokter = DB::table('rsmst_doctors')->where('dr_id', $drId)->first();
        if (!$dokter) {
            throw new Exception("Data dokter dengan dr_id {$drId} tidak ditemukan.");
        }


        // 1. Ambil nomor transaksi header (sls_no)
        $maxSlsNo = DB::table('imtxn_slshdrs')
            ->select(DB::raw("nvl(max(sls_no)+1,1) as max_sls_no"))
            ->first()
            ->max_sls_no;

        // 2. Format waktu resep dan cari shift yang sesuai
        $formattedTime = Carbon::createFromFormat('d/m/Y H:i:s', $resepDate, env('APP_TIMEZONE'))
            ->format('H:i:s');
        $shiftRecord = DB::table('rstxn_shiftctls')
            ->select('shift')
            ->whereRaw("'{$formattedTime}' between shift_start and shift_end")
            ->first();
        $shift = $shiftRecord->shift ?? 1;

        // 3. Insert header transaksi ke imtxn_slshdrs
        DB::table('imtxn_slshdrs')->insert([
            'sls_no'                => $maxSlsNo,
            'sls_date'              => DB::raw("to_date('{$resepDate}','dd/mm/yyyy hh24:mi:ss')"),
            'status'                => 'A',
            'dr_id'                 => $drId,
            'reg_no'                => $regNo,
            'shift'                 => $shift,
            'rihdr_no'              => $riHdrNo,
            'emp_id'                => '1',
            'acte_price'            => 3000,
            'waktu_masuk_pelayanan' => DB::raw('sysdate'),
        ]);
        // 4. Jika ada detail obat, baru insert ke imtxn_slsdtls
        if (!empty($dataObat)) {
            // 4. Loop untuk insert data detail obat ke imtxn_slsdtls
            foreach ($dataObat as $eresepItem) {
                // Ambil nomor detail transaksi berikutnya (sls_dtl)
                $maxSlsDtl = DB::table('imtxn_slsdtls')
                    ->select(DB::raw("nvl(max(sls_dtl)+1,1) as max_sls_dtl"))
                    ->first()
                    ->max_sls_dtl;

                // Ambil data produk untuk mendapatkan harga
                $dataProduct = DB::table('immst_products')
                    ->select('sales_price')
                    ->where('product_id', $eresepItem['productId'])
                    ->first();

                DB::table('imtxn_slsdtls')->insert([
                    'sls_dtl'         => $maxSlsDtl,
                    'qty'             => $eresepItem['qty'],
                    'exp_date'        => DB::raw("add_months(to_date('{$resepDate}','dd/mm/yyyy hh24:mi:ss'),12)"),
                    'sales_price'     => $dataProduct->sales_price ?? 0,
                    'product_id'      => $eresepItem['productId'],
                    'sls_no'          => $maxSlsNo,
                    'resep_carapakai' => $eresepItem['signaX'],
                    'resep_takar'     => 'Tablet',
                    'resep_kapsul'    => $eresepItem['signaHari'],
                    'resep_ket'       => $eresepItem['catatanKhusus'],
                    'etiket_status'   => 1,
                ]);
            }
        }
        $this->dataDaftarRi['eresepHdr'][$indexKe]['slsNo'] = $maxSlsNo;
    }

    // Memanggil ResepHdr ketika tombol di klik dan lakukan perubahan formEntryEresepRIHdr
    public function showResepHdr($resepNo)
    {
        $this->checkRiStatus();

        try {
            // Cari header resep berdasarkan resepNo
            $header = collect($this->dataDaftarRi['eresepHdr'])->firstWhere('resepNo', $resepNo);
            $this->formEntryEresepRIHdr['regNo'] = $header['regNo'];
            $this->formEntryEresepRIHdr['riHdrNo'] = $header['riHdrNo'];
            $this->formEntryEresepRIHdr['resepDate'] = $header['resepDate'];
            $this->formEntryEresepRIHdr['resepNo'] = $header['resepNo'];

            // set resepNoIndex
            $this->setResepNoIndex($this->formEntryEresepRIHdr['resepNo']);
            // Panggil event resepNoUpdated mengeubah resepNo pada child
            $this->emit('syncronizeAssessmentDokterRIFindData');
            $this->emit('syncronizeAssessmentPerawatRIFindData');
            $this->emit('resepNoUpdated',   $this->formEntryEresepRIHdr['resepNo'], $this->resepIndexRef);
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }

    public function copyResepHdrInap(int $sourceResepNo): void
    {
        $this->checkRiStatus();

        try {
            // 1) Cari header sumber berdasar resepNo
            $srcIdx = collect($this->dataDaftarRi['eresepHdr'] ?? [])
                ->search(fn($h) => ($h['resepNo'] ?? null) == $sourceResepNo);

            if ($srcIdx === false) {
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                    ->addError('Resep sumber tidak ditemukan.');
                return;
            }

            $srcHdr = $this->dataDaftarRi['eresepHdr'][$srcIdx];

            // 2) Hanya boleh copy jika SUDAH TTD
            $isSigned = !empty($srcHdr['tandaTanganDokter']['dokterPeresep'] ?? null);
            if (!$isSigned) {
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                    ->addWarning('Resep belum ditandatangani dokter, tidak dapat di-copy.');
                return;
            }

            // 3) Tentukan resepNo baru & waktu
            $newResepNo = (collect($this->dataDaftarRi['eresepHdr'] ?? [])->max('resepNo') ?? 0) + 1;
            $now        = Carbon::now()->format('d/m/Y H:i:s');

            // Helper reindex riObatDtl
            $reindexDtl = function (?array $rows, int $newResepNo) {
                $rows = array_values($rows ?? []);
                foreach ($rows as $i => &$r) {
                    $r['riObatDtl'] = $i + 1;
                    $r['riHdrNo']   = $this->riHdrNoRef;
                    $r['resepNo']   = $newResepNo;
                }
                return $rows;
            };

            // 4) Salin detail + reindex
            $copiedNonRacikan = $reindexDtl($srcHdr['eresep']        ?? [], $newResepNo);
            $copiedRacikan    = $reindexDtl($srcHdr['eresepRacikan']  ?? [], $newResepNo);

            // 5) Susun header baru (TTD & slsNo TIDAK dibawa agar bisa diedit)
            $newHdr = [
                'regNo'     => $srcHdr['regNo']     ?? ($this->dataDaftarRi['regNo'] ?? ''),
                'riHdrNo'   => $this->riHdrNoRef,
                'resepDate' => $now,
                'resepNo'   => $newResepNo,
            ];
            if (!empty($copiedNonRacikan)) $newHdr['eresep']        = $copiedNonRacikan;
            if (!empty($copiedRacikan))    $newHdr['eresepRacikan'] = $copiedRacikan;

            // 6) Tambahkan & simpan
            $this->dataDaftarRi['eresepHdr'][] = $newHdr;
            $this->store();
            // === TAMPILKAN: set sebagai aktif + sync child
            $this->showResepHdr($newResepNo); // <- ini kuncinya


            // 7) Jadikan aktif + sinkronisasi child
            $this->formEntryEresepRIHdr = [
                'regNo'     => $newHdr['regNo'],
                'riHdrNo'   => $newHdr['riHdrNo'],
                'resepDate' => $newHdr['resepDate'],
                'resepNo'   => $newHdr['resepNo'],
            ];
            $this->setResepNoIndex($newHdr['resepNo']);
            $this->emit('syncronizeAssessmentDokterRIFindData');
            $this->emit('syncronizeAssessmentPerawatRIFindData');
            $this->emit('resepNoUpdated', $newHdr['resepNo'], $this->resepIndexRef);

            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addSuccess('Resep (TTD) berhasil di-copy ke nomor: ' . $newResepNo . '. Silakan edit sebelum TTD.');
        } catch (\Throwable $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Gagal menyalin resep: ' . $e->getMessage());
        }
    }



    private function findData($riHdrNo): void
    {
        $this->riStatusRef = DB::table('rstxn_rihdrs')->select('ri_status')->where('rihdr_no', $riHdrNo)->first()->ri_status;
        $this->dataDaftarRi  = $this->findDataRI($riHdrNo);

        // jika eresep tidak ditemukan tambah variable eresep pda array
        if (isset($this->dataDaftarRi['eresepHdr']) == false) {
            $this->dataDaftarRi['eresepHdr'] = [];
        }
        $this->dataFormEntry();
    }

    private function dataFormEntry(): void
    {
        // Otomatis isi dengan data noResep Baru
        if (empty($this->formEntryEresepRIHdr['regNo'])) {
            $this->formEntryEresepRIHdr['regNo'] = $this->dataDaftarRi['regNo'];
        }

        if (empty($this->formEntryEresepRIHdr['riHdrNo'])) {
            $this->formEntryEresepRIHdr['riHdrNo'] = $this->riHdrNoRef;
        }

        if (empty($this->formEntryEresepRIHdr['resepDate'])) {
            $this->formEntryEresepRIHdr['resepDate'] = Carbon::now()->format('d/m/Y H:i:s');
        }

        $maxResepNo = collect($this->dataDaftarRi['eresepHdr'] ?? [])->max('resepNo') ?? 0;
        $lastInserted = $maxResepNo + 1;

        if (empty($this->formEntryEresepRIHdr['resepNo'])) {
            $this->formEntryEresepRIHdr['resepNo'] = $lastInserted;
        }
    }

    private function setResepNoIndex($resepNo): void
    {
        // Ketika ditemukan resepNo maka set Index
        foreach ($this->dataDaftarRi['eresepHdr'] as $index => $header) {
            if (isset($header['resepNo'])) {
                if ($header['resepNo'] == $resepNo) {
                    $this->resepIndexRef = $index;
                    break;
                }
            }
        }
    }

    public function simpanPlanCppt($resepNo)
    {
        try {
            // Filter header sesuai resepNo (Collection)
            $resepHdr = collect($this->dataDaftarRi['eresepHdr'] ?? [])
                ->firstWhere('resepNo', $resepNo);

            if (!$resepHdr) {
                toastr()
                    ->closeOnHover(true)
                    ->closeDuration(3)
                    ->positionClass('toast-top-left')
                    ->addWarning('Data resep tidak ditemukan.');
                return;
            }

            // hanya boleh simpan ke CPPT jika SUDAH TTD dokter
            $isSigned = !empty($resepHdr['tandaTanganDokter']['dokterPeresep'] ?? null);
            if (!$isSigned) {
                toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                    ->addWarning('Resep belum ditandatangani dokter, tidak dapat disimpan ke CPPT.');
                return;
            }


            $eresepNonRacikan = PHP_EOL;
            $eresepRacikan    = PHP_EOL;

            // --- Non Racikan (dari key 'eresep') ---
            foreach (($resepHdr['eresep'] ?? []) as $item) {
                $jenis = strtolower($item['jenisKeterangan'] ?? 'nonracikan');

                if ($jenis === 'nonracikan') {
                    // --- Non Racikan ---
                    $catatanKhusus = trim((string)($item['catatanKhusus'] ?? ''));
                    $catatanSuffix = $catatanKhusus !== '' ? ' (' . $catatanKhusus . ')' : '';

                    $eresepNonRacikan .=
                        'R/ ' .
                        ($item['productName'] ?? '-') .
                        ' | No. ' . ($item['qty'] ?? '-') .
                        ' | S ' . ($item['signaX'] ?? '-') . 'dd' . ($item['signaHari'] ?? '-') .
                        $catatanSuffix .
                        PHP_EOL;
                } else {
                    // --- Racikan ---
                    $noRacikan       = (string)($item['noRacikan'] ?? '');
                    $productName     = (string)($item['productName'] ?? '');
                    $dosis           = (string)($item['dosis'] ?? '');
                    $qty             = $item['qty'] ?? null;
                    $catatan         = (string)($item['catatan'] ?? '');
                    $catatanKhusus   = (string)($item['catatanKhusus'] ?? '');

                    $jmlRacikanLine  = $qty
                        ? ('Jml Racikan ' . $qty . ' | ' . $catatan . ' | S ' . $catatanKhusus . PHP_EOL)
                        : '';

                    $eresepRacikan .=
                        $noRacikan . '/ ' . $productName . ' - ' . $dosis . PHP_EOL .
                        $jmlRacikanLine;
                }
            }

            // --- Racikan (dari key 'eresepRacikan') ---
            foreach (($resepHdr['eresepRacikan'] ?? []) as $item) {
                $noRacikan     = (string)($item['noRacikan'] ?? '');
                $productName   = (string)($item['productName'] ?? '');
                $dosis         = (string)($item['dosis'] ?? '');
                $qty           = $item['qty'] ?? null;
                $catatan       = (string)($item['catatan'] ?? '');
                $catatanKhusus = (string)($item['catatanKhusus'] ?? '');

                $jmlRacikanLine = $qty
                    ? ('Jml Racikan ' . $qty . ' | ' . $catatan . ' | S ' . $catatanKhusus . PHP_EOL)
                    : '';

                $eresepRacikan .=
                    $noRacikan . '/ ' . $productName . ' - ' . $dosis . PHP_EOL .
                    $jmlRacikanLine;
            }

            // Simpan ke struktur cppt
            $this->emit('syncronizeCpptPlan', trim($eresepNonRacikan . $eresepRacikan));
            // Toast sukses
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addSuccess('Plan CPPT berhasil disimpan.');
        } catch (Exception $e) {
            // Toast error
            toastr()
                ->closeOnHover(true)
                ->closeDuration(3)
                ->positionClass('toast-top-left')
                ->addError("Gagal menyimpan Plan CPPT: " . $e->getMessage());
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
        return view('livewire.emr-r-i.eresep-r-i.eresep-r-i-hdr', [
            'myTitle' => 'Data Pasien Rawat Jalan',
            'mySnipt' => 'Rekam Medis Pasien',
            'myProgram' => 'Eresep',
        ]);
    }
    // select data end////////////////
}
