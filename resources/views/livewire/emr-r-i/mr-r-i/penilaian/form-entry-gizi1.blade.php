<div class="space-y-4">
    <!-- Tanggal Penilaian -->
    <div>
        <x-input-label for="formEntryGizi.tglPenilaian" :value="__('Tanggal Penilaian')" :required="__(true)" />
        <x-text-input id="formEntryGizi.tglPenilaian" type="date" class="mt-1"
            wire:model.debounce.500ms="formEntryGizi.tglPenilaian" />
        @error('formEntryGizi.tglPenilaian')
            <x-input-error :messages="$message" />
        @enderror
    </div>

    <!-- Petugas Penilai -->
    <div>
        <x-input-label for="formEntryGizi.petugasPenilai" :value="__('Petugas Penilai')" :required="__(true)" />
        <x-text-input id="formEntryGizi.petugasPenilai" placeholder="Nama Petugas Penilai" class="mt-1"
            wire:model.debounce.500ms="formEntryGizi.petugasPenilai" />
        @error('formEntryGizi.petugasPenilai')
            <x-input-error :messages="$message" />
        @enderror
    </div>

    <!-- Kode Petugas Penilai -->
    <div>
        <x-input-label for="formEntryGizi.petugasPenilaiCode" :value="__('Kode Petugas Penilai')" :required="__(true)" />
        <x-text-input id="formEntryGizi.petugasPenilaiCode" placeholder="Kode Petugas Penilai" class="mt-1"
            wire:model.debounce.500ms="formEntryGizi.petugasPenilaiCode" />
        @error('formEntryGizi.petugasPenilaiCode')
            <x-input-error :messages="$message" />
        @enderror
    </div>

    <!-- Berat Badan -->
    <div>
        <x-input-label for="formEntryGizi.beratBadan" :value="__('Berat Badan (kg)')" :required="__(true)" />
        <x-text-input id="formEntryGizi.beratBadan" type="number" placeholder="Berat Badan" class="mt-1"
            wire:model.debounce.500ms="formEntryGizi.beratBadan" />
        @error('formEntryGizi.beratBadan')
            <x-input-error :messages="$message" />
        @enderror
    </div>

    <!-- Tinggi Badan -->
    <div>
        <x-input-label for="formEntryGizi.tinggiBadan" :value="__('Tinggi Badan (cm)')" :required="__(true)" />
        <x-text-input id="formEntryGizi.tinggiBadan" type="number" placeholder="Tinggi Badan" class="mt-1"
            wire:model.debounce.500ms="formEntryGizi.tinggiBadan" />
        @error('formEntryGizi.tinggiBadan')
            <x-input-error :messages="$message" />
        @enderror
    </div>

    <!-- Indeks Massa Tubuh (IMT) -->
    <div>
        <x-input-label for="formEntryGizi.imt" :value="__('Indeks Massa Tubuh (IMT)')" :required="__(true)" />
        <x-text-input id="formEntryGizi.imt" type="number" placeholder="IMT" class="mt-1"
            wire:model.debounce.500ms="formEntryGizi.imt" />
        @error('formEntryGizi.imt')
            <x-input-error :messages="$message" />
        @enderror
    </div>

    <!-- Kebutuhan Gizi -->
    <div>
        <x-input-label for="formEntryGizi.kebutuhanGizi" :value="__('Kebutuhan Gizi Harian')" :required="__(true)" />
        <x-text-input-area id="formEntryGizi.kebutuhanGizi" placeholder="Kebutuhan Gizi Harian" class="mt-1"
            wire:model.debounce.500ms="formEntryGizi.kebutuhanGizi" />
        @error('formEntryGizi.kebutuhanGizi')
            <x-input-error :messages="$message" />
        @enderror
    </div>

    <!-- Catatan -->
    <div>
        <x-input-label for="formEntryGizi.catatan" :value="__('Catatan Tambahan')" />
        <x-text-input-area id="formEntryGizi.catatan" placeholder="Catatan Tambahan" class="mt-1"
            wire:model.debounce.500ms="formEntryGizi.catatan" />
        @error('formEntryGizi.catatan')
            <x-input-error :messages="$message" />
        @enderror
    </div>

    <!-- Tombol Simpan -->
    <div class="mt-6">
        <button type="button" class="px-4 py-2 text-white bg-blue-500 rounded-md hover:bg-blue-600" wire:click="save">
            Simpan
        </button>
    </div>
</div>
