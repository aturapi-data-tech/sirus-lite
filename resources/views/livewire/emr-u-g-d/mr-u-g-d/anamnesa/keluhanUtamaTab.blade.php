<div>
    <div class="w-full mb-1">
        <div class="p-4 bg-white rounded-lg shadow-sm">
            <x-input-label for="dataDaftarUgd.anamnesa.keluhanUtama.keluhanUtama" :value="__('Keluhan Utama')" :required="__(true)"
                class="mb-3 text-lg font-semibold" />
            <div>
                <x-text-input-area id="dataDaftarUgd.anamnesa.keluhanUtama.keluhanUtama"
                    placeholder="Deskripsikan keluhan utama pasien secara detail..." class="w-full" :errorshas="__($errors->has('dataDaftarUgd.anamnesa.keluhanUtama.keluhanUtama'))"
                    :disabled=$disabledPropertyRjStatus :rows=6
                    wire:model.debounce.500ms="dataDaftarUgd.anamnesa.keluhanUtama.keluhanUtama" />
            </div>
            @error('dataDaftarUgd.anamnesa.keluhanUtama.keluhanUtama')
                <x-input-error :messages=$message class="mt-2" />
            @enderror
        </div>
    </div>
</div>
