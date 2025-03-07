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

    public function setDokterPeresep()
    {
        // ttd Dokter
        foreach ($this->dataDaftarRi['eresepHdr'] as $index => $header) {
            if (isset($header['resepNo'])) {
                if ($header['resepNo'] == $this->formEntryEresepRIHdr['resepNo']) {
                    $myUserCodeActive = auth()->user()->myuser_code;
                    $myUserNameActive = auth()->user()->myuser_name;
                    if (auth()->user()->hasRole('Dokter')) {
                        $this->dataDaftarRi['eresepHdr'][$index]['tandaTanganDokter']['dokterPeresep'] = $myUserNameActive;
                        $this->dataDaftarRi['eresepHdr'][$index]['tandaTanganDokter']['dokterPeresepCode'] = $myUserCodeActive;
                        // Simpan perubahan
                        $this->store();
                        // Notifikasi sukses
                        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("TTD Dokter berhasil diisi oleh " . $myUserNameActive);
                    } else {
                        // Notifikasi error jika peran tidak sesuai
                        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Anda tidak dapat melakukan TTD karena User Role " . $myUserNameActive . ' bukan Dokter.');
                        return;
                    }
                    break;
                }
            }
        }

        // Ambil data user yang sedang login





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
