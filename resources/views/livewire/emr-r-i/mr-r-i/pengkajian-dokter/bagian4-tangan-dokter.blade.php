<div class="space-y-4">
    <div>
        <x-input-label for="dataDaftarRi.pengkajianDokter.tandaTanganDokter.namaDokter" :value="__('Nama Dokter')"
            :required="true" />
        <x-text-input id="dataDaftarRi.pengkajianDokter.tandaTanganDokter.namaDokter" placeholder="Nama Dokter"
            class="mt-1" wire:model.debounce.500ms="dataDaftarRi.pengkajianDokter.tandaTanganDokter.namaDokter" />
        @error('dataDaftarRi.pengkajianDokter.tandaTanganDokter.namaDokter')
            <x-input-error :messages="$message" />
        @enderror
    </div>

    <div>
        <x-input-label for="dataDaftarRi.pengkajianDokter.tandaTanganDokter.tandaTangan" :value="__('Tanda Tangan')"
            :required="true" />
        <x-text-input id="dataDaftarRi.pengkajianDokter.tandaTanganDokter.tandaTangan" class="mt-1"
            wire:model.debounce.500ms="dataDaftarRi.pengkajianDokter.tandaTanganDokter.tandaTangan" />
        @error('dataDaftarRi.pengkajianDokter.tandaTanganDokter.tandaTangan')
            <x-input-error :messages="$message" />
        @enderror
    </div>
</div>
