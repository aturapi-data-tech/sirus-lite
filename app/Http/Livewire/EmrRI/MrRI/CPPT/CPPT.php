<?php

namespace App\Http\Livewire\EmrRI\MrRI\CPPT;

use Livewire\Component;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Http\Traits\EmrRI\EmrRITrait;


class CPPT extends Component
{
    use  EmrRITrait;
    // listener from blade////////////////
    protected $listeners = [
        'syncronizeAssessmentPerawatRIFindData' => 'refreshData',
        'syncronizeCpptPlan' => 'setCpptPlan',
        'laboratSelectedText' => 'appendLaboratText',
    ];

    public function refreshData()
    {
        $this->findData($this->riHdrNoRef);
    }

    public function setCpptPlan($cpptPlan)
    {
        $this->formEntryCPPT['soap']['plan'] = $cpptPlan;
        $this->closeModalEresepRI();
    }

    public function appendLaboratText(string $text): void
    {
        // Ambil isi sekarang (kalau belum ada, pakai string kosong)
        $current = data_get(
            $this->formEntryCPPT,
            'soap.objective',
            ''
        );

        // Pilih separator baris baru hanya jika sudah ada isi sebelumnya
        $sep = $current !== '' ? "\n" : '';

        // Simpan kembali ke struktur array $formEntryCPPT
        data_set(
            $this->formEntryCPPT,
            'soap.objective',
            $current . $sep . $text
        );
    }

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
        $this->formEntryCPPT['tglCPPT'] = Carbon::now()->format('d/m/Y H:i:s');
    }

    public function addCPPT(): void
    {
        // Isi data petugas CPPT dari user yang sedang login
        $this->formEntryCPPT['petugasCPPT'] = auth()->user()->myuser_name;
        $this->formEntryCPPT['petugasCPPTCode'] = auth()->user()->myuser_code;
        $this->formEntryCPPT['profession'] = auth()->user()->roles->first()->name ?? ''; // Misalnya, profesi user disimpan di kolom 'profession'
        // Aturan validasi
        $rules = [
            'formEntryCPPT.tglCPPT' => 'required|date_format:d/m/Y H:i:s', // Tanggal CPPT wajib diisi dan sesuai format
            'formEntryCPPT.petugasCPPT' => 'required|string|max:200', // Nama petugas wajib diisi, maksimal 100 karakter
            'formEntryCPPT.petugasCPPTCode' => 'required|string|max:50', // Kode petugas wajib diisi, maksimal 50 karakter
            'formEntryCPPT.profession' => 'required|string|max:50', // Profesi wajib diisi, maksimal 50 karakter
            'formEntryCPPT.soap.subjective' => 'required|string|max:1000', // Subjective wajib diisi, maksimal 1000 karakter
            'formEntryCPPT.soap.objective' => 'required|string|max:1000', // Objective wajib diisi, maksimal 1000 karakter
            'formEntryCPPT.soap.assessment' => 'required|string|max:1000', // Assessment wajib diisi, maksimal 1000 karakter
            'formEntryCPPT.soap.plan' => 'required|string|max:1000', // Plan wajib diisi, maksimal 1000 karakter
            'formEntryCPPT.instruction' => 'nullable|string|max:1000', // Instruksi opsional, maksimal 1000 karakter
            'formEntryCPPT.review' => 'nullable|string|max:1000', // Review opsional, maksimal 1000 karakter
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
            'formEntryCPPT.soap.subjective.max' => 'Subjective tidak boleh lebih dari 1000 karakter.',

            // Objective (SOAP)
            'formEntryCPPT.soap.objective.required' => 'Objective wajib diisi.',
            'formEntryCPPT.soap.objective.string' => 'Objective harus berupa teks.',
            'formEntryCPPT.soap.objective.max' => 'Objective tidak boleh lebih dari 1000 karakter.',

            // Assessment (SOAP)
            'formEntryCPPT.soap.assessment.required' => 'Assessment wajib diisi.',
            'formEntryCPPT.soap.assessment.string' => 'Assessment harus berupa teks.',
            'formEntryCPPT.soap.assessment.max' => 'Assessment tidak boleh lebih dari 1000 karakter.',

            // Plan (SOAP)
            'formEntryCPPT.soap.plan.required' => 'Plan wajib diisi.',
            'formEntryCPPT.soap.plan.string' => 'Plan harus berupa teks.',
            'formEntryCPPT.soap.plan.max' => 'Plan tidak boleh lebih dari 1000 karakter.',

            // Instruksi
            'formEntryCPPT.instruction.string' => 'Instruksi harus berupa teks.',
            'formEntryCPPT.instruction.max' => 'Instruksi tidak boleh lebih dari 1000 karakter.',

            // Review
            'formEntryCPPT.review.string' => 'Review harus berupa teks.',
            'formEntryCPPT.review.max' => 'Review tidak boleh lebih dari 1000 karakter.',
        ];

        // Proses validasi
        try {
            $this->validate($rules, $messages);
        } catch (\Illuminate\Validation\ValidationException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Lakukan pengecekan kembali input data. " . $e->getMessage());
            return;
        }

        $riHdrNo = $this->dataDaftarRi['riHdrNo'] ?? null;
        if (!$riHdrNo) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("riHdrNo kosong.");
            return;
        }

        // ----- LOCK per RI: cegah tulis bersamaan -----
        $lockKey = "cppt:ri:{$riHdrNo}";
        $shouldEmit = false;
        $inserted   = false;
        try {
            Cache::lock($lockKey, 5)->block(3, function () use ($riHdrNo, &$shouldEmit, &$inserted) {
                // Ambil state TERBARU dari DB di dalam lock
                $fresh = $this->findDataRI($riHdrNo);
                if (!isset($fresh['cppt']) || !is_array($fresh['cppt'])) {
                    $fresh['cppt'] = [];
                }

                // Idempotency fingerprint untuk cegah duplikat
                $fingerprint = md5(json_encode([
                    $this->formEntryCPPT['tglCPPT']      ?? null,
                    $this->formEntryCPPT['soap']         ?? [],
                    $this->formEntryCPPT['instruction']  ?? null,
                    $this->formEntryCPPT['review']       ?? null,
                ], JSON_UNESCAPED_UNICODE));

                // Cek duplikat (dobel klik)
                $dupe = collect($fresh['cppt'])->first(fn($row) => ($row['fingerprint'] ?? null) === $fingerprint);
                if ($dupe) {
                    toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                        ->addInfo("CPPT yang sama sudah tersimpan.");
                    return;
                }

                // Payload final + UUID
                $payload = $this->formEntryCPPT;
                $payload['cpptId']     = (string) Str::uuid();
                $payload['fingerprint'] = $fingerprint;

                // Simpan dalam TRANSAKSI (opsional tapi bagus)
                DB::transaction(function () use ($riHdrNo, &$fresh, $payload) {
                    $fresh['cppt'][] = $payload;
                    $this->updateJsonRI($riHdrNo, $fresh); // method dari EmrRITrait milikmu
                });

                // Sinkronkan state komponen
                $this->dataDaftarRi = $fresh;
                $shouldEmit = true;
                $inserted   = true;
            });
        } catch (\Illuminate\Contracts\Cache\LockTimeoutException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Sistem sibuk, coba lagi sebentar.');
            return;
        }

        if ($shouldEmit) {
            $this->emit('syncronizeAssessmentPerawatRIFindData');
        }

        if ($inserted) {
            $this->reset(['formEntryCPPT']);
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addSuccess("Data CPPT berhasil ditambahkan.");
        }
    }

    public function removeCPPT(string $cpptId): void
    {

        $riHdrNo = $this->dataDaftarRi['riHdrNo'] ?? null;
        if (!$riHdrNo) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError("riHdrNo kosong.");
            return;
        }

        $lockKey = "cppt:ri:{$riHdrNo}";
        $shouldEmit = false;
        $deleted    = false;

        try {
            Cache::lock($lockKey, 5)->block(3, function () use ($riHdrNo, $cpptId, &$shouldEmit, &$deleted) {
                $fresh = $this->findDataRI($riHdrNo);
                $cppts = collect($fresh['cppt'] ?? []);

                $index = $cppts->search(fn($row) => ($row['cpptId'] ?? null) === $cpptId);
                if ($index === false) {
                    toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                        ->addError("Data CPPT tidak ditemukan.");
                    return;
                }

                $cppts->forget($index);
                $fresh['cppt'] = $cppts->values()->all();
                DB::transaction(function () use ($riHdrNo, $fresh) {
                    $this->updateJsonRI($riHdrNo, $fresh);
                });

                $this->dataDaftarRi = $fresh;
                $shouldEmit = true;
                $deleted    = true;
            });
        } catch (\Illuminate\Contracts\Cache\LockTimeoutException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError('Sistem sibuk, coba lagi sebentar.');
            return;
        }

        if ($shouldEmit) {
            $this->emit('syncronizeAssessmentPerawatRIFindData');
        }
        if ($deleted) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addSuccess("Data CPPT berhasil dihapus.");
        }
    }


    public function copyCPPT(string $cpptId): void
    {
        $cppts = collect($this->dataDaftarRi['cppt'] ?? []);
        $cppt  = $cppts->first(fn($row) => ($row['cpptId'] ?? null) === $cpptId);

        if (!$cppt) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
                ->addError("Data CPPT tidak ditemukan untuk dicopy.");
            return;
        }

        $this->formEntryCPPT = [
            "tglCPPT" => "",
            "petugasCPPT" => "",
            "petugasCPPTCode" => "",
            "profession" => "",
            "soap" => [
                "subjective" => $cppt['soap']['subjective'] ?? "",
                "objective"  => $cppt['soap']['objective']  ?? "",
                "assessment" => $cppt['soap']['assessment'] ?? "",
                "plan"       => $cppt['soap']['plan']       ?? "",
            ],
            "instruction" => "",
            "review"      => "",
        ];

        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')
            ->addSuccess("CPPT berhasil dicopy ke form entry.");
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
        $user = auth()->user();
        // Isi default assessment dari diagnosa awal (jika assessment masih kosong)
        if ($user->hasAnyRole(['Dokter', 'Admin'])) {
            $this->setDefaultAssessmentFromDiagnosaAwal();
        }
    }

    private function setDefaultAssessmentFromDiagnosaAwal(): void
    {
        // kalau assessment sudah diisi user / sudah ada value, jangan override
        $currentAssessment = trim((string) data_get($this->formEntryCPPT, 'soap.assessment', ''));
        if ($currentAssessment !== '') {
            return;
        }

        // ambil diagnosa awal dari dataDaftarRi
        $diagnosaAwal = trim((string) data_get(
            $this->dataDaftarRi,
            'pengkajianDokter.diagnosaAssesment.diagnosaAwal',
            ''
        ));

        if ($diagnosaAwal === '') {
            return;
        }

        // optional: rapikan, misal hilangkan leading "A:" kalau mau
        // $diagnosaAwal = preg_replace('/^\s*A\s*:\s*/i', '', $diagnosaAwal);

        data_set($this->formEntryCPPT, 'soap.assessment', $diagnosaAwal);
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
