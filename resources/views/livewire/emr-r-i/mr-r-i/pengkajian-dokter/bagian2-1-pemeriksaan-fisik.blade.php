<div class="space-y-4">
    <div>
        <x-input-label for="dataDaftarRi.pengkajianDokter.fisik" :value="__('Fisik')" :required="true" />
        <x-text-input-area id="dataDaftarRi.pengkajianDokter.fisik" placeholder="Fisik" class="mt-1" :errorshas="__($errors->has('dataDaftarRi.pengkajianDokter.fisik'))"
            :disabled="$disabledPropertyRjStatus" :rows="7" wire:model.debounce.500ms="dataDaftarRi.pengkajianDokter.fisik" />
        @error('dataDaftarRi.pengkajianDokter.fisik')
            <x-input-error :messages="$message" />
        @enderror
    </div>
</div>
