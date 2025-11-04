<?php

namespace App\Http\Livewire\EmrRI\PengkajianEdukasiPasienRI;

use Illuminate\Support\Facades\DB;

use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

use Spatie\ArrayToXml\ArrayToXml;
use App\Http\Traits\EmrRI\EmrRITrait;
use App\Http\Traits\customErrorMessagesTrait;


class PengkajianEdukasiPasienRI extends Component
{
    use WithPagination, EmrRITrait, customErrorMessagesTrait;


    // listener from blade////////////////
    protected $listeners = [
        'syncronizeAssessmentDokterRIFindData' => 'mount',
        'syncronizeAssessmentPerawatRIFindData' => 'mount'
    ];

    //////////////////////////////
    // Ref on top bar
    //////////////////////////////
    public $riHdrNoRef;
    public $regNoRef;
    public array $dataDaftarRi = [];
    public array $agreementOptions = [
        ["agreementId" => "1", "agreementDesc" => "Setuju"],
        ["agreementId" => "0", "agreementDesc" => "Tidak Setuju"],
    ];
    public array $edukasiPasien = [];

    public array $formEntryEdukasiPasien = [
        "tglEdukasi" => "", // Tanggal edukasi diberikan
        "petugasEdukasi" => "", // Nama petugas yang memberikan edukasi
        "petugasEdukasiCode" => "", // Kode petugas yang memberikan edukasi
        "sasaranEdukasi" => "", // Nama sasaran yang menerima edukasi (misalnya, nama keluarga atau wali pasien)
        "sasaranEdukasiSignature" => "", // Tanda tangan sasaran yang menerima edukasi
        "hubunganSasaranEdukasidgnPasien" => "", // Hubungan sasaran dengan pasien (misalnya, "Orang Tua", "Suami/Istri", "Anak")
        "edukasi" => [
            "kategoriEdukasi" => [], // Array kategori edukasi yang dipilih (misalnya, "Pengobatan", "Diet dan Nutrisi")
            "keteranganEdukasi" => "", // Keterangan atau penjelasan edukasi
            "statusEdukasi" => "", // Status edukasi (misalnya, "Mengerti", "Tidak Mengerti")
            "reEdukasi" => [ // Rekomendasi atau kebutuhan edukasi ulang
                "perlu" => false, // Apakah perlu re-edukasi? (true/false)
                "tglReEdukasi" => "", // Tanggal re-edukasi jika diperlukan
                "petugasReEdukasi" => "", // Nama petugas yang memberikan re-edukasi
            ],
        ],
    ];

    public array $edukasiOptions = [
        ['kategoriEdukasi' => 'Pengobatan'], // Sering digunakan karena terkait obat dan dosis
        ['kategoriEdukasi' => 'Rencana Perawatan'], // Penting untuk menjelaskan tindakan medis
        ['kategoriEdukasi' => 'Diagnosa Medis'], // Diberikan untuk menjelaskan kondisi pasien
        ['kategoriEdukasi' => 'Pencegahan Infeksi'], // Penting untuk pasien rawat inap
        ['kategoriEdukasi' => 'Diet dan Nutrisi'], // Sering diberikan untuk pasien dengan kondisi khusus
        ['kategoriEdukasi' => 'Perawatan Luka'], // Penting untuk pasien pasca-operasi atau luka
        ['kategoriEdukasi' => 'Aktivitas Fisik'], // Diberikan untuk pasien dengan kebutuhan rehabilitasi
        ['kategoriEdukasi' => 'Perawatan di Rumah'], // Diberikan sebelum pasien pulang
        ['kategoriEdukasi' => 'Manajemen Nyeri'], // Penting untuk pasien dengan nyeri kronis atau pasca-operasi
        ['kategoriEdukasi' => 'Dukungan Emosional dan Spiritual'], // Diberikan untuk pasien dengan kebutuhan psikologis
        ['kategoriEdukasi' => 'Lain-lain'], // Untuk kategori yang tidak termasuk di atas
    ];

    public $sasaranEdukasiSignature;

    public function setTglEdukasi($tanggal)
    {
        $this->formEntryEdukasiPasien['tglEdukasi'] = $tanggal;
    }

    public function setTglReEdukasi($tanggal)
    {
        $this->formEntryEdukasiPasien['edukasi']['reEdukasi']['tglReEdukasi'] = $tanggal;
    }


    public function addEdukasiPasien(): void
    {
        $this->formEntryEdukasiPasien['petugasEdukasi'] = auth()->user()->myuser_name;
        $this->formEntryEdukasiPasien['petugasEdukasiCode'] = auth()->user()->myuser_code;

        $this->formEntryEdukasiPasien['sasaranEdukasiSignature'] = $this->sasaranEdukasiSignature;


        $rules = [
            'formEntryEdukasiPasien.tglEdukasi' => 'required|date_format:d/m/Y H:i:s',
            'formEntryEdukasiPasien.petugasEdukasi' => 'required|string|max:100',
            'formEntryEdukasiPasien.petugasEdukasiCode' => 'required|string|max:50',
            'formEntryEdukasiPasien.edukasi.kategoriEdukasi' => 'required|array',
            'formEntryEdukasiPasien.edukasi.keteranganEdukasi' => 'required|string|max:255',
            'formEntryEdukasiPasien.edukasi.statusEdukasi' => 'required|string|max:100',
            'formEntryEdukasiPasien.edukasi.reEdukasi.perlu' => 'required|boolean',
            'formEntryEdukasiPasien.edukasi.reEdukasi.tglReEdukasi' => 'required_if:formEntryEdukasiPasien.edukasi.reEdukasi.perlu,true|date_format:d/m/Y H:i:s',
            'formEntryEdukasiPasien.edukasi.reEdukasi.petugasReEdukasi' => 'required_if:formEntryEdukasiPasien.edukasi.reEdukasi.perlu,true|string|max:100',
            'formEntryEdukasiPasien.sasaranEdukasi' => 'required|string|max:100',
            'formEntryEdukasiPasien.hubunganSasaranEdukasidgnPasien' => 'required|string|max:100',
            'formEntryEdukasiPasien.sasaranEdukasiSignature' => 'required|string',
        ];

        $messages = [
            'formEntryEdukasiPasien.tglEdukasi.required' => 'Tanggal edukasi wajib diisi.',
            'formEntryEdukasiPasien.tglEdukasi.date_format' => 'Format tanggal edukasi harus dd/mm/yyyy hh24:mi:ss.',
            'formEntryEdukasiPasien.petugasEdukasi.required' => 'Nama petugas edukasi wajib diisi.',
            'formEntryEdukasiPasien.petugasEdukasi.string' => 'Nama petugas edukasi harus berupa teks.',
            'formEntryEdukasiPasien.petugasEdukasi.max' => 'Nama petugas edukasi tidak boleh lebih dari 100 karakter.',
            'formEntryEdukasiPasien.petugasEdukasiCode.required' => 'Kode petugas edukasi wajib diisi.',
            'formEntryEdukasiPasien.petugasEdukasiCode.string' => 'Kode petugas edukasi harus berupa teks.',
            'formEntryEdukasiPasien.petugasEdukasiCode.max' => 'Kode petugas edukasi tidak boleh lebih dari 50 karakter.',
            'formEntryEdukasiPasien.edukasi.kategoriEdukasi.required' => 'Kategori edukasi wajib diisi.',
            'formEntryEdukasiPasien.edukasi.keteranganEdukasi.required' => 'Keterangan edukasi wajib diisi.',
            'formEntryEdukasiPasien.edukasi.keteranganEdukasi.string' => 'Keterangan edukasi harus berupa teks.',
            'formEntryEdukasiPasien.edukasi.keteranganEdukasi.max' => 'Keterangan edukasi tidak boleh lebih dari 255 karakter.',
            'formEntryEdukasiPasien.edukasi.statusEdukasi.required' => 'Status edukasi wajib diisi.',
            'formEntryEdukasiPasien.edukasi.statusEdukasi.string' => 'Status edukasi harus berupa teks.',
            'formEntryEdukasiPasien.edukasi.statusEdukasi.max' => 'Status edukasi tidak boleh lebih dari 100 karakter.',
            'formEntryEdukasiPasien.edukasi.reEdukasi.perlu.required' => 'Perlu re-edukasi wajib diisi.',
            'formEntryEdukasiPasien.edukasi.reEdukasi.perlu.boolean' => 'Perlu re-edukasi harus berupa boolean.',
            'formEntryEdukasiPasien.edukasi.reEdukasi.tglReEdukasi.required_if' => 'Tanggal re-edukasi wajib diisi jika perlu re-edukasi.',
            'formEntryEdukasiPasien.edukasi.reEdukasi.tglReEdukasi.date_format' => 'Format tanggal re-edukasi harus dd/mm/yyyy hh24:mi:ss.',
            'formEntryEdukasiPasien.edukasi.reEdukasi.petugasReEdukasi.required_if' => 'Petugas re-edukasi wajib diisi jika perlu re-edukasi.',
            'formEntryEdukasiPasien.edukasi.reEdukasi.petugasReEdukasi.string' => 'Petugas re-edukasi harus berupa teks.',
            'formEntryEdukasiPasien.edukasi.reEdukasi.petugasReEdukasi.max' => 'Petugas re-edukasi tidak boleh lebih dari 100 karakter.',
            'formEntryEdukasiPasien.sasaranEdukasi.required' => 'Sasaran edukasi wajib diisi.',
            'formEntryEdukasiPasien.sasaranEdukasi.string' => 'Sasaran edukasi harus berupa teks.',
            'formEntryEdukasiPasien.sasaranEdukasi.max' => 'Sasaran edukasi tidak boleh lebih dari 100 karakter.',
            'formEntryEdukasiPasien.hubunganSasaranEdukasidgnPasien.required' => 'Hubungan sasaran edukasi dengan pasien wajib diisi.',
            'formEntryEdukasiPasien.hubunganSasaranEdukasidgnPasien.string' => 'Hubungan sasaran edukasi dengan pasien harus berupa teks.',
            'formEntryEdukasiPasien.hubunganSasaranEdukasidgnPasien.max' => 'Hubungan sasaran edukasi dengan pasien tidak boleh lebih dari 100 karakter.',
            'formEntryEdukasiPasien.sasaranEdukasiSignature.required' => 'Tanda tangan sasaran edukasi wajib diisi.',
            'formEntryEdukasiPasien.sasaranEdukasiSignature.string' => 'Tanda tangan sasaran edukasi harus berupa teks.',
        ];

        try {
            $this->validate($rules, $messages);
        } catch (\Illuminate\Validation\ValidationException $e) {
            toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addError("Lakukan pengecekan kembali input data." . $e->getMessage());
            $this->validate($rules, $messages);
        }

        // Tambahkan data edukasi ke dalam $dataDaftarRi
        $this->dataDaftarRi['edukasiPasien'][] = $this->formEntryEdukasiPasien;

        // Simpan perubahan ke penyimpanan
        $this->store();

        // Reset form entry edukasi setelah data berhasil ditambahkan
        // $this->reset(['formEntryEdukasiPasien']);


        // Tampilkan pesan sukses
        toastr()
            ->closeOnHover(true)
            ->closeDuration(3)
            ->positionClass('toast-top-left')
            ->addSuccess("Data edukasi pasien berhasil ditambahkan.");
    }

    public function removeEdukasiPasien($index)
    {

        if (isset($this->dataDaftarRi['edukasiPasien']) && is_array($this->dataDaftarRi['edukasiPasien'])) {
            unset($this->dataDaftarRi['edukasiPasien'][$index]);
            $this->dataDaftarRi['edukasiPasien'] = array_values($this->dataDaftarRi['edukasiPasien']);
        }

        $this->store();
    }

    public function setEdukasiPasien($dataEdukasiPasien)
    {
        // Menyimpan data edukasi pasien ke dalam variabel $edukasiPasien
        $this->formEntryEdukasiPasien = $dataEdukasiPasien;

        // Jika diperlukan, Anda bisa melakukan operasi lain di sini
        // Misalnya, validasi data atau logging
    }


    public function store()
    {
        // Logic update mode start //////////
        $this->updateDataRi($this->dataDaftarRi['riHdrNo']);

        $this->emit('syncronizeAssessmentPerawatRIFindData');
    }

    private function updateDataRi($riHdrNo): void
    {
        $this->updateJsonRI($riHdrNo, $this->dataDaftarRi);

        toastr()->closeOnHover(true)->closeDuration(3)->positionClass('toast-top-left')->addSuccess("Signature berhasil disimpan.");
    }

    private function findData($riHdrNo): void
    {
        $this->dataDaftarRi = $this->findDataRI($riHdrNo);
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
            'livewire.emr-r-i.pengkajian-edukasi-pasien-r-i.pengkajian-edukasi-pasien-r-i',
            []
        );
    }
    // select data end////////////////


}
