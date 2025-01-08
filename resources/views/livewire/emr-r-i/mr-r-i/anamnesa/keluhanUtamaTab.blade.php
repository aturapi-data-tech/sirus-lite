<div>
    <div class="w-full mb-1">

        <div>
            <x-input-label for="dataDaftarRi.anamnesa.keluhanUtama.keluhanUtama" :value="__('Keluhan Utama')" :required="__(true)"
                class="pt-2 sm:text-xl" />

            <div class="mb-2 ">
                <x-text-input-area id="dataDaftarRi.anamnesa.keluhanUtama.keluhanUtama" placeholder="Keluhan Utama"
                    class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarRi.anamnesa.keluhanUtama.keluhanUtama'))" :disabled=$disabledPropertyRjStatus :rows=7
                    wire:model.debounce.500ms="dataDaftarRi.anamnesa.keluhanUtama.keluhanUtama" />

            </div>
            @error('dataDaftarRi.anamnesa.keluhanUtama.keluhanUtama')
                <x-input-error :messages=$message />
            @enderror
        </div>

    </div>
</div>
