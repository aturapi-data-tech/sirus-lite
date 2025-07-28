<?php

namespace App\Http\Livewire\EmrRI\MrRI\CPPT;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

use App\Http\Traits\EmrRI\EmrRITrait;


class CPPT extends Component
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

    public array $cppt = [];

    public array $formEntryCPPT = [
        "tglCPPT" => "", // Tanggal CPPT (kosong)
        "petugasCPPT" => "", // Nama petugas CPPT (kosong)
        "petugasCPPTCode" => "", // Kode petugas CPPT (kosong)
        "profession" => "", // Profesi petugas (kosong)
        "soap" => [
            "subjective" => "", // Subjective (kosong)
            "objective" => "", // Objective (kosong)
            "assessment" => "", // Assessment (kosong)
            "plan" => "" // Plan (kosong)
        ],
        "instruction" => "", // Instruksi (kosong)
        "review" => "" // Review (kosong)
    ];

    ////////////////////////////////////////////////
    ///////////begin////////////////////////////////
    ////////////////////////////////////////////////

    public function setTglCPPT($tanggal)
    {
        $this->formEntryCPPT['tglCPPT'] = $tanggal;
    }

    public function addCPPT(): void
    {
        // Isi data petugas CPPT dari user yang sedang login
        $this->formEntryCPPT['petugasCPPT'] = auth()->user()->myuser_name;
        $this->formEntryCPPT['petugasCPPTCode'] = auth()->user()->myuser_code;
        $this->formEntryCPPT['profession'] = auth()->user()->roles->first()['name']; // Misalnya, profesi user disimpan di kolom 'profession'

        // Aturan validasi
        $rules = [
            'formEntryCPPT.tglCPPT' => 'required|date_format:d/m/Y H:i:s', // Tanggal CPPT wajib diisi dan sesuai format
            'formEntryCPPT.petugasCPPT' => 'required|string|max:200', // Nama petugas wajib diisi, maksimal 100 karakter
            'formEntryCPPT.petugasCPPTCode' => 'required|string|max:50', // Kode petugas wajib diisi, maksimal 50 karakter
            'formEntryCPPT.profession' => 'required|string|max:50', // Profesi wajib diisi, maksimal 50 karakter
            'formEntryCPPT.soap.subjective' => 'required|string|max:500', // Subjective wajib diisi, maksimal 500 karakter
            'formEntryCPPT.soap.objective' => 'required|string|max:500', // Objective wajib diisi, maksimal 500 karakter
            'formEntryCPPT.soap.assessment' => 'required|string|max:500', // Assessment wajib diisi, maksimal 500 karakter
            'formEntryCPPT.soap.plan' => 'required|string|max:500', // Plan wajib diisi, maksimal 500 karakter
            'formEntryCPPT.instruction' => 'nullable|string|max:500', // Instruksi opsional, maksimal 500 karakter
            'formEntryCPPT.review' => 'nullable|string|max:500', // Review opsional, maksimal 500 karakter
        ];

        $messages = [
            // Tanggal CPPT
            'formEntryCPPT.tglCPPT.required' => 'Tanggal CPPT wajib diisi.',
            'formEntryCPPT.tglCPPT.date_format' => 'Format tanggal CPPT harus dd/mm/yyyy hh24:mi:ss.',

            // Nama petugas CPPT
            'formEntryCPPT.petugasCPPT.required' => 'Nama petugas CPPT wajib diisi.',
            'formEntryCPPT.petugasCPPT.string' => 'Nama petugas CPPT harus berupa teks.',
            'formEntryCPPT.petugasCPPT.max' => 'Nama petugas CPPT tidak boleh lebih dari 200 karakter.',

            // Kode petugas CPPT
            'formEntryCPPT.petugasCPPTCode.required' => 'Kode petugas CPPT wajib diisi.',
            'formEntryCPPT.petugasCPPTCode.string' => 'Kode petugas CPPT harus berupa teks.',
            'formEntryCPPT.petugasCPPTCode.max' => 'Kode petugas CPPT tidak boleh lebih dari 50 karakter.',

            // Profesi petugas
            'formEntryCPPT.profession.required' => 'Profesi petugas wajib diisi.',
            'formEntryCPPT.profession.string' => 'Profesi petugas harus berupa teks.',
            'formEntryCPPT.profession.max' => 'Profesi petugas tidak boleh lebih dari 50 karakter.',

            // Subjective (SOAP)
            'formEntryCPPT.soap.subjective.required' => 'Subjective wajib diisi.',
            'formEntryCPPT.soap.subjective.string' => 'Subjective harus berupa teks.',
            'formEntryCPPT.soap.subjective.max' => 'Subjective tidak boleh lebih dari 500 karakter.',

            // Objective (SOAP)
            'formEntryCPPT.soap.objective.required' => 'Objective wajib diisi.',
            'formEntryCPPT.soap.objective.string' => 'Objective harus berupa teks.',
            'formEntryCPPT.soap.objective.max' => 'Objective tidak boleh lebih dari 500 karakter.',

            // Assessment (SOAP)
            'formEntryCPPT.soap.assessment.required' => 'Assessment wajib diisi.',
            'formEntryCPPT.soap.assessment.string' => 'Assessment harus berupa teks.',
            'formEntryCPPT.soap.assessment.max' => 'Assessment tidak boleh lebih dari 500 karakter.',

            // Plan (SOAP)
            'formEntryCPPT.soap.plan.required' => 'Plan wajib diisi.',
            'formEntryCPPT.soap.plan.string' => 'Plan harus berupa teks.',
            'formEntryCPPT.soap.plan.max' => 'Plan tidak boleh lebih dari 500 karakter.',

            // Instruksi
            'formEntryCPPT.instruction.string' => 'Instruksi harus berupa teks.',
            'formEntryCPPT.instruction.max' => 'Instruksi tidak boleh lebih dari 500 karakter.',

            // Review
            'formEntryCPPT.review.string' => 'Review harus berupa teks.',
            'formEntryCPPT.review.max' => 'Review tidak boleh lebih dari 500 karakter.',
        ];

        // Proses validasi
        try {
            $this->validate($rules, $messages);
        } catch (\Illuminate\Validation\ValidationException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Lakukan pengecekan kembali input data. " . $e->getMessage());
            $this->validate($rules, $messages);
            return;
        }

        // Tambahkan data CPPT ke dalam array
        $this->dataDaftarRi['cppt'][] = $this->formEntryCPPT;

        // Simpan perubahan ke penyimpanan
        $this->store();

        // Reset form setelah data berhasil ditambahkan
        $this->reset(['formEntryCPPT']);

        // Tampilkan pesan sukses
        toastr()
            ->closeOnHover(true)
            ->closeDuration(3)
            ->positionClass('toast-top-left')
            ->addSuccess("Data CPPT berhasil ditambahkan.");
    }

    public function removeCPPT($index): void
    {
        // Pastikan data CPPT ada dan merupakan array
        if (isset($this->dataDaftarRi['cppt']) && is_array($this->dataDaftarRi['cppt'])) {
            // Hapus data berdasarkan indeks
            unset($this->dataDaftarRi['cppt'][$index]);

            // Reset indeks array setelah penghapusan
            $this->dataDaftarRi['cppt'] = array_values($this->dataDaftarRi['cppt']);
        }

        // Simpan perubahan ke penyimpanan
        $this->store();

        // Tampilkan pesan sukses
        toastr()
            ->closeOnHover(true)
            ->closeDuration(3)
            ->positionClass('toast-top-left')
            ->addSuccess("Data CPPT berhasil dihapus.");
    }



    public bool $isOpenEresepRI = false;
    public string $isOpenModeEresepRI = 'insert';

    public function openModalEresepRI(): void
    {
        $this->isOpenEresepRI = true;
        $this->isOpenModeEresepRI = 'insert';
    }

    public function closeModalEresepRI(): void
    {
        $this->isOpenEresepRI = false;
        $this->isOpenModeEresepRI = 'insert';
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
        if (isset($this->dataDaftarRi['cppt']) == false) {
            $this->dataDaftarRi['cppt'] = $this->cppt;
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
            'livewire.emr-r-i.mr-r-i.c-p-p-t.c-p-p-t',
            [
                // 'RJpasiens' => $query->paginate($this->limitPerPage),
                'myTitle' => 'CPPT',
                'mySnipt' => 'Rekam Medis Pasien',
                'myProgram' => 'Pasien Rawat Inap',
            ]
        );
    }
    // select data end////////////////


}
