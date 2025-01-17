<div class="space-y-4">
    <!-- Tanggal Penilaian -->
    <div>
        <x-input-label for="formEntryStatusPediatrik.tglPenilaian" :value="__('Tanggal Penilaian')" :required="__(true)" />
        <x-text-input id="formEntryStatusPediatrik.tglPenilaian" type="date" class="mt-1"
            wire:model.debounce.500ms="formEntryStatusPediatrik.tglPenilaian" />
        @error('formEntryStatusPediatrik.tglPenilaian')
            <x-input-error :messages="$message" />
        @enderror
    </div>

    <!-- Petugas Penilai -->
    <div>
        <x-input-label for="formEntryStatusPediatrik.petugasPenilai" :value="__('Petugas Penilai')" :required="__(true)" />
        <x-text-input id="formEntryStatusPediatrik.petugasPenilai" placeholder="Nama Petugas Penilai" class="mt-1"
            wire:model.debounce.500ms="formEntryStatusPediatrik.petugasPenilai" />
        @error('formEntryStatusPediatrik.petugasPenilai')
            <x-input-error :messages="$message" />
        @enderror
    </div>

    <!-- Kode Petugas Penilai -->
    <div>
        <x-input-label for="formEntryStatusPediatrik.petugasPenilaiCode" :value="__('Kode Petugas Penilai')" :required="__(true)" />
        <x-text-input id="formEntryStatusPediatrik.petugasPenilaiCode" placeholder="Kode Petugas Penilai" class="mt-1"
            wire:model.debounce.500ms="formEntryStatusPediatrik.petugasPenilaiCode" />
        @error('formEntryStatusPediatrik.petugasPenilaiCode')
            <x-input-error :messages="$message" />
        @enderror
    </div>

    <!-- Status Gizi -->
    <div>
        <x-input-label for="formEntryStatusPediatrik.statusGizi" :value="__('Status Gizi')" :required="__(true)" />
        <select id="formEntryStatusPediatrik.statusGizi"
            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            wire:model.debounce.500ms="formEntryStatusPediatrik.statusGizi">
            <option value="">Pilih Status Gizi</option>
            <option value="Gizi Buruk">Gizi Buruk</option>
            <option value="Gizi Normal">Gizi Normal</option>
        </select>
        @error('formEntryStatusPediatrik.statusGizi')
            <x-input-error :messages="$message" />
        @enderror
    </div>

    <!-- Perkembangan -->
    <div>
        <x-input-label for="formEntryStatusPediatrik.perkembangan" :value="__('Perkembangan')" :required="__(true)" />
        <select id="formEntryStatusPediatrik.perkembangan"
            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            wire:model.debounce.500ms="formEntryStatusPediatrik.perkembangan">
            <option value="">Pilih Perkembangan</option>
            <option value="Sesuai Usia">Sesuai Usia</option>
            <option value="Menyimpang">Menyimpang</option>
        </select>
        @error('formEntryStatusPediatrik.perkembangan')
            <x-input-error :messages="$message" />
        @enderror
    </div>

    <!-- Kesehatan Umum -->
    <div>
        <x-input-label for="formEntryStatusPediatrik.kesehatanUmum" :value="__('Kesehatan Umum')" :required="__(true)" />
        <select id="formEntryStatusPediatrik.kesehatanUmum"
            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            wire:model.debounce.500ms="formEntryStatusPediatrik.kesehatanUmum">
            <option value="">Pilih Kesehatan Umum</option>
            <option value="Sehat">Sehat</option>
            <option value="Tidak Sehat">Tidak Sehat</option>
        </select>
        @error('formEntryStatusPediatrik.kesehatanUmum')
            <x-input-error :messages="$message" />
        @enderror
    </div>

    <!-- Catatan Khusus -->
    <div>
        <x-input-label for="formEntryStatusPediatrik.catatanKhusus" :value="__('Catatan Khusus')" />
        <x-text-input-area id="formEntryStatusPediatrik.catatanKhusus" placeholder="Catatan Khusus" class="mt-1"
            wire:model.debounce.500ms="formEntryStatusPediatrik.catatanKhusus" />
        @error('formEntryStatusPediatrik.catatanKhusus')
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
