<div class="grid grid-cols-2 gap-2">
    <!-- Keluhan Utama -->
    <div>
        <x-input-label for="dataDaftarRi.pengkajianDokter.anamnesa.keluhanUtama" :value="__('Keluhan Utama')" :required="true" />
        <x-text-input-area id="dataDaftarRi.pengkajianDokter.anamnesa.keluhanUtama" placeholder="Keluhan Utama"
            class="mt-1" wire:model.debounce.500ms="dataDaftarRi.pengkajianDokter.anamnesa.keluhanUtama" />
        @error('dataDaftarRi.pengkajianDokter.anamnesa.keluhanUtama')
            <x-input-error :messages="$message" />
        @enderror
    </div>

    <!-- Keluhan Tambahan -->
    <div>
        <x-input-label for="dataDaftarRi.pengkajianDokter.anamnesa.keluhanTambahan" :value="__('Keluhan Tambahan')"
            :required="true" />
        <x-text-input-area id="dataDaftarRi.pengkajianDokter.anamnesa.keluhanTambahan" placeholder="Keluhan Tambahan"
            class="mt-1" wire:model.debounce.500ms="dataDaftarRi.pengkajianDokter.anamnesa.keluhanTambahan" />
        @error('dataDaftarRi.pengkajianDokter.anamnesa.keluhanTambahan')
            <x-input-error :messages="$message" />
        @enderror
    </div>

    <!-- Riwayat Penyakit -->
    <div>
        <x-input-label for="dataDaftarRi.pengkajianDokter.anamnesa.riwayatPenyakit" :value="__('Riwayat Penyakit')"
            :required="true" />
        <div class="space-y-2">
            <x-text-input-area id="dataDaftarRi.pengkajianDokter.anamnesa.riwayatPenyakit.sekarang"
                placeholder="Sekarang" class="mt-1"
                wire:model.debounce.500ms="dataDaftarRi.pengkajianDokter.anamnesa.riwayatPenyakit.sekarang" />
            <x-text-input-area id="dataDaftarRi.pengkajianDokter.anamnesa.riwayatPenyakit.dahulu" placeholder="Dahulu"
                class="mt-1"
                wire:model.debounce.500ms="dataDaftarRi.pengkajianDokter.anamnesa.riwayatPenyakit.dahulu" />
            <x-text-input-area id="dataDaftarRi.pengkajianDokter.anamnesa.riwayatPenyakit.keluarga"
                placeholder="Keluarga" class="mt-1"
                wire:model.debounce.500ms="dataDaftarRi.pengkajianDokter.anamnesa.riwayatPenyakit.keluarga" />
        </div>
        @error('dataDaftarRi.pengkajianDokter.anamnesa.riwayatPenyakit.*')
            <x-input-error :messages="$message" />
        @enderror
    </div>

    <!-- Jenis Alergi -->
    <div>
        <x-input-label for="dataDaftarRi.pengkajianDokter.anamnesa.jenisAlergi" :value="__('Jenis Alergi / Alergi [Makanan / Obat / Udara]')" :required="true" />
        <div class="space-y-2">
            <x-text-input-area id="dataDaftarRi.pengkajianDokter.anamnesa.jenisAlergi"
                placeholder="Jenis Alergi / Alergi [Makanan / Obat / Udara]" class="mt-1"
                wire:model.debounce.500ms="dataDaftarRi.pengkajianDokter.anamnesa.jenisAlergi" />
        </div>
        @error('dataDaftarRi.pengkajianDokter.anamnesa.jenisAlergi')
            <x-input-error :messages="$message" />
        @enderror
    </div>

    <div>
        @include('livewire.emr-r-i.mr-r-i.pengkajian-dokter.bagian1-rekonsiliasi-obat')
    </div>
</div>
