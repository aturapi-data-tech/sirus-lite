<?php

namespace App\Http\Livewire\EmrRI\MrRI\AsuhanKeperawatan;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

use App\Http\Traits\EmrRI\EmrRITrait;
use App\Http\Traits\LOV\LOVDiagKep\LOVDiagKepTrait;


class AsuhanKeperawatan extends Component
{
    use WithPagination, EmrRITrait, LOVDiagKepTrait;
    // listener from blade////////////////
    protected $listeners = [
        // 'syncronizeAssessmentPerawatRIFindData' => 'mount'
    ];

    //////////////////////////////
    // Ref on top bar
    //////////////////////////////
    public $riHdrNoRef;

    // LOV Nested
    public array $diagKep;

    private function syncDataFormEntry(): void
    {

        $this->formEntryAsuhanKeperawatan['diagKepId'] = $this->diagKep['DiagKepId'] ?? '';
        $this->formEntryAsuhanKeperawatan['diagKepDesc'] = $this->diagKep['DiagKepDesc'] ?? '';
        $DiagKepJson = $this->diagKep['DiagKepJson'] ?? '{}';
        $this->formEntryAsuhanKeperawatan['diagKepJson'] = json_decode($DiagKepJson, true);
    }
    private function syncLOV(): void
    {
        $this->diagKep = $this->collectingMyDiagKep;
    }
    public function resetFormEntryAsuhanKeperawatan()
    {
        $this->reset([
            'formEntryAsuhanKeperawatan',
            'collectingMyDiagKep', //Reset LOV / render  / empty NestLov
        ]);
        $this->resetValidation();
    }
    // LOV Nested

    // dataDaftarRi RJ
    public array $dataDaftarRi = [];

    public array $asuhanKeperawatan = [];

    public array $formEntryAsuhanKeperawatan = [
        "tglAsuhanKeperawatan" => "", // Tanggal AsuhanKeperawatan (kosong)
        "petugasAsuhanKeperawatan" => "", // Nama petugas AsuhanKeperawatan (kosong)
        "petugasAsuhanKeperawatanCode" => "", // Kode petugas AsuhanKeperawatan (kosong)
        "diagKepId" => "",
        "diagKepDesc" => "",
        "diagKepJson" => [],
    ];

    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////

    public function setTglAsuhanKeperawatan($tanggal)
    {
        $this->formEntryAsuhanKeperawatan['tglAsuhanKeperawatan'] = $tanggal;
    }

    public function addAsuhanKeperawatan(): void
    {
        // Isi data petugas AsuhanKeperawatan dari user yang sedang login
        $this->formEntryAsuhanKeperawatan['petugasAsuhanKeperawatan'] = auth()->user()->myuser_name;
        $this->formEntryAsuhanKeperawatan['petugasAsuhanKeperawatanCode'] = auth()->user()->myuser_code;

        // Aturan validasi
        $rules = [
            'formEntryAsuhanKeperawatan.tglAsuhanKeperawatan' => 'required|date_format:d/m/Y H:i:s', // Tanggal Asuhan Keperawatan wajib diisi dengan format yang benar
            'formEntryAsuhanKeperawatan.petugasAsuhanKeperawatan' => 'required|string|max:200', // Nama petugas wajib diisi, maksimal 200 karakter
            'formEntryAsuhanKeperawatan.petugasAsuhanKeperawatanCode' => 'required|string|max:50', // Kode petugas wajib diisi, maksimal 50 karakter
            'formEntryAsuhanKeperawatan.diagKepId' => 'required|string|exists:rsmst_diagkeperawatans,diagkep_id', // ID Diagnosis Keperawatan harus ada dalam tabel RSMST_DIAGKEPERAWATANS
            'formEntryAsuhanKeperawatan.diagKepDesc' => 'required|string|max:500', // Deskripsi Diagnosis Keperawatan wajib diisi, maksimal 500 karakter
            'formEntryAsuhanKeperawatan.diagKep' => 'required|array|min:1', // Diagnosis Keperawatan wajib diisi dan harus berupa array minimal 1 item
            'formEntryAsuhanKeperawatan.diagKep.*' => 'required|string|max:500', // Setiap item dalam diagKep harus berupa string dan maksimal 200 karakter
        ];

        $messages = [
            'formEntryAsuhanKeperawatan.tglAsuhanKeperawatan.required' => 'Tanggal Asuhan Keperawatan wajib diisi.',
            'formEntryAsuhanKeperawatan.tglAsuhanKeperawatan.date_format' => 'Format Tanggal Asuhan Keperawatan harus sesuai dengan format d/m/Y H:i:s.',

            'formEntryAsuhanKeperawatan.petugasAsuhanKeperawatan.required' => 'Nama petugas Asuhan Keperawatan wajib diisi.',
            'formEntryAsuhanKeperawatan.petugasAsuhanKeperawatan.string' => 'Nama petugas Asuhan Keperawatan harus berupa teks.',
            'formEntryAsuhanKeperawatan.petugasAsuhanKeperawatan.max' => 'Nama petugas Asuhan Keperawatan tidak boleh lebih dari 200 karakter.',

            'formEntryAsuhanKeperawatan.petugasAsuhanKeperawatanCode.required' => 'Kode petugas Asuhan Keperawatan wajib diisi.',
            'formEntryAsuhanKeperawatan.petugasAsuhanKeperawatanCode.string' => 'Kode petugas Asuhan Keperawatan harus berupa teks.',
            'formEntryAsuhanKeperawatan.petugasAsuhanKeperawatanCode.max' => 'Kode petugas Asuhan Keperawatan tidak boleh lebih dari 50 karakter.',

            'formEntryAsuhanKeperawatan.diagKepId.required' => 'ID Diagnosis Keperawatan wajib diisi.',
            'formEntryAsuhanKeperawatan.diagKepId.string' => 'ID Diagnosis Keperawatan harus berupa teks.',
            'formEntryAsuhanKeperawatan.diagKepId.exists' => 'ID Diagnosis Keperawatan tidak ditemukan dalam database.',

            'formEntryAsuhanKeperawatan.diagKepDesc.required' => 'Deskripsi Diagnosis Keperawatan wajib diisi.',
            'formEntryAsuhanKeperawatan.diagKepDesc.string' => 'Deskripsi Diagnosis Keperawatan harus berupa teks.',
            'formEntryAsuhanKeperawatan.diagKepDesc.max' => 'Deskripsi Diagnosis Keperawatan tidak boleh lebih dari 500 karakter.',

            'formEntryAsuhanKeperawatan.diagKep.required' => 'Diagnosis Keperawatan wajib diisi.',
            'formEntryAsuhanKeperawatan.diagKep.array' => 'Diagnosis Keperawatan harus berupa array.',
            'formEntryAsuhanKeperawatan.diagKep.min' => 'Minimal harus ada satu Diagnosis Keperawatan.',

            'formEntryAsuhanKeperawatan.diagKep.*.required' => 'Setiap item dalam Diagnosis Keperawatan wajib diisi.',
            'formEntryAsuhanKeperawatan.diagKep.*.string' => 'Setiap item dalam Diagnosis Keperawatan harus berupa teks.',
            'formEntryAsuhanKeperawatan.diagKep.*.max' => 'Setiap item dalam Diagnosis Keperawatan tidak boleh lebih dari 500 karakter.',
        ];


        // Proses validasi
        try {
            $this->validate($rules, $messages);
        } catch (\Illuminate\Validation\ValidationException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Lakukan pengecekan kembali input data. " . $e->getMessage());
            $this->validate($rules, $messages);
            return;
        }

        // Tambahkan data AsuhanKeperawatan ke dalam array
        $this->dataDaftarRi['asuhanKeperawatan'][] = $this->formEntryAsuhanKeperawatan;

        // Simpan perubahan ke penyimpanan
        $this->store();

        // Reset form setelah data berhasil ditambahkan
        $this->reset(['formEntryAsuhanKeperawatan']);

        // Tampilkan pesan sukses
        toastr()
            ->closeOnHover(true)
            ->closeDuration(3)
            ->positionClass('toast-top-left')
            ->addSuccess("Data AsuhanKeperawatan berhasil ditambahkan.");
    }

    public function removeAsuhanKeperawatan($index): void
    {
        // Pastikan data AsuhanKeperawatan ada dan merupakan array
        if (isset($this->dataDaftarRi['asuhanKeperawatan']) && is_array($this->dataDaftarRi['asuhanKeperawatan'])) {
            // Hapus data berdasarkan indeks
            unset($this->dataDaftarRi['asuhanKeperawatan'][$index]);

            // Reset indeks array setelah penghapusan
            $this->dataDaftarRi['asuhanKeperawatan'] = array_values($this->dataDaftarRi['asuhanKeperawatan']);
        }

        // Simpan perubahan ke penyimpanan
        $this->store();

        // Tampilkan pesan sukses
        toastr()
            ->closeOnHover(true)
            ->closeDuration(3)
            ->positionClass('toast-top-left')
            ->addSuccess("Data AsuhanKeperawatan berhasil dihapus.");
    }



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

        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Penilaian berhasil disimpan.");
    }
    // insert and update record end////////////////


    private function findData($rjno): void
    {


        $this->dataDaftarRi = $this->findDataRI($rjno);
        // dd($this->dataDaftarRi);
        // jika pemeriksaan tidak ditemukan tambah variable pemeriksaan pda array
        if (isset($this->dataDaftarRi['asuhanKeperawatan']) == false) {
            $this->dataDaftarRi['asuhanKeperawatan'] = $this->asuhanKeperawatan;
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


        // LOV
        $this->syncLOV();
        // FormEntry
        $this->syncDataFormEntry();


        return view(
            'livewire.emr-r-i.mr-r-i.asuhan-keperawatan.asuhan-keperawatan',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'AsuhanKeperawatan',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Pasien Rawat Inap',
            ]
        );
    }
    // select data end////////////////


}
