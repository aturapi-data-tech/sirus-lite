<div class="space-y-4">
    <!-- Tanggal Penilaian -->
    <div>
        <x-input-label for="formEntryDekubitus.tglPenilaian" :value="__('Tanggal Penilaian')" :required="__(true)" />
        <x-text-input id="formEntryDekubitus.tglPenilaian" type="date" class="mt-1"
            wire:model.debounce.500ms="formEntryDekubitus.tglPenilaian" />
        @error('formEntryDekubitus.tglPenilaian')
            <x-input-error :messages="$message" />
        @enderror
    </div>

    <!-- Petugas Penilai -->
    <div>
        <x-input-label for="formEntryDekubitus.petugasPenilai" :value="__('Petugas Penilai')" :required="__(true)" />
        <x-text-input id="formEntryDekubitus.petugasPenilai" placeholder="Nama Petugas Penilai" class="mt-1"
            wire:model.debounce.500ms="formEntryDekubitus.petugasPenilai" />
        @error('formEntryDekubitus.petugasPenilai')
            <x-input-error :messages="$message" />
        @enderror
    </div>

    <!-- Kode Petugas Penilai -->
    <div>
        <x-input-label for="formEntryDekubitus.petugasPenilaiCode" :value="__('Kode Petugas Penilai')" :required="__(true)" />
        <x-text-input id="formEntryDekubitus.petugasPenilaiCode" placeholder="Kode Petugas Penilai" class="mt-1"
            wire:model.debounce.500ms="formEntryDekubitus.petugasPenilaiCode" />
        @error('formEntryDekubitus.petugasPenilaiCode')
            <x-input-error :messages="$message" />
        @enderror
    </div>

    <!-- Lokasi Dekubitus -->
    <div>
        <x-input-label for="formEntryDekubitus.lokasiDekubitus" :value="__('Lokasi Dekubitus')" :required="__(true)" />
        <x-text-input id="formEntryDekubitus.lokasiDekubitus" placeholder="Lokasi Dekubitus" class="mt-1"
            wire:model.debounce.500ms="formEntryDekubitus.lokasiDekubitus" />
        @error('formEntryDekubitus.lokasiDekubitus')
            <x-input-error :messages="$message" />
        @enderror
    </div>

    <!-- Stadium Dekubitus -->
    <div>
        <x-input-label for="formEntryDekubitus.stadium" :value="__('Stadium Dekubitus')" :required="__(true)" />
        <select id="formEntryDekubitus.stadium"
            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            wire:model.debounce.500ms="formEntryDekubitus.stadium">
            <option value="">Pilih Stadium</option>
            <option value="Stadium 1">Stadium 1</option>
            <option value="Stadium 2">Stadium 2</option>
            <option value="Stadium 3">Stadium 3</option>
            <option value="Stadium 4">Stadium 4</option>
        </select>
        @error('formEntryDekubitus.stadium')
            <x-input-error :messages="$message" />
        @enderror
    </div>

    <!-- Ukuran Luka -->
    <div>
        <x-input-label for="formEntryDekubitus.ukuran" :value="__('Ukuran Luka')" :required="__(true)" />
        <x-text-input id="formEntryDekubitus.ukuran" placeholder="Ukuran Luka (misalnya, 5x5 cm)" class="mt-1"
            wire:model.debounce.500ms="formEntryDekubitus.ukuran" />
        @error('formEntryDekubitus.ukuran')
            <x-input-error :messages="$message" />
        @enderror
    </div>

    <!-- Penanganan -->
    <div>
        <x-input-label for="formEntryDekubitus.penanganan" :value="__('Penanganan')" :required="__(true)" />
        <x-text-input-area id="formEntryDekubitus.penanganan" placeholder="Penanganan yang dilakukan" class="mt-1"
            wire:model.debounce.500ms="formEntryDekubitus.penanganan" />
        @error('formEntryDekubitus.penanganan')
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
