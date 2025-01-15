<div class="space-y-4">
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

    <!-- Riwayat Penggunaan Obat -->
    <div>
        <x-input-label for="dataDaftarRi.pengkajianDokter.anamnesa.riwayatPenggunaanObat" :value="__('Riwayat Penggunaan Obat')"
            :required="true" />
        <div class="space-y-2">
            <x-text-input id="dataDaftarRi.pengkajianDokter.anamnesa.riwayatPenggunaanObat.alergiObat"
                placeholder="Alergi Obat" class="mt-1"
                wire:model.debounce.500ms="dataDaftarRi.pengkajianDokter.anamnesa.riwayatPenggunaanObat.alergiObat" />
            <x-text-input id="dataDaftarRi.pengkajianDokter.anamnesa.riwayatPenggunaanObat.obatKronis"
                placeholder="Obat Kronis" class="mt-1"
                wire:model.debounce.500ms="dataDaftarRi.pengkajianDokter.anamnesa.riwayatPenggunaanObat.obatKronis" />
        </div>
        @error('dataDaftarRi.pengkajianDokter.anamnesa.riwayatPenggunaanObat.*')
            <x-input-error :messages="$message" />
        @enderror
    </div>
</div>
