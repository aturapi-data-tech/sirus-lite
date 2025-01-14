<div class="space-y-4">
    <div>
        <x-input-label for="dataDaftarRi.pengkajianDokter.statusLokalis.deskripsiGambar" :value="__('Deskripsi Gambar')"
            :required="true" />
        <x-text-input-area id="dataDaftarRi.pengkajianDokter.statusLokalis.deskripsiGambar" placeholder="Deskripsi Gambar"
            class="mt-1" :errorshas="__($errors->has('dataDaftarRi.pengkajianDokter.statusLokalis.deskripsiGambar'))" :disabled="$disabledPropertyRjStatus" :rows="7"
            wire:model.debounce.500ms="dataDaftarRi.pengkajianDokter.statusLokalis.deskripsiGambar" />
        @error('dataDaftarRi.pengkajianDokter.statusLokalis.deskripsiGambar')
            <x-input-error :messages="$message" />
        @enderror
    </div>
</div>
