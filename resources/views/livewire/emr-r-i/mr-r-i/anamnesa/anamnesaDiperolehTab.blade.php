<div>
    <div class="w-full mb-1">

        <div class="grid grid-cols-2 gap-2 mt-2 ml-2">
            <x-check-box value='1' :label="__('Auto-anamnesa')"
                wire:model.debounce.500ms="dataDaftarRi.anamnesa.anamnesaDiperoleh.autoanamnesa" />
            <x-check-box value='1' :label="__('Allon-anamnesa')"
                wire:model.debounce.500ms="dataDaftarRi.anamnesa.anamnesaDiperoleh.allonanamnesa" />
        </div>

        <div>
            <x-input-label for="dataDaftarRi.anamnesa.anamnesaDiperoleh.anamnesaDiperolehDari" :value="__('Dari')"
                :required="__(false)" />

            <div class="mb-2 ">
                <x-text-input-area id="dataDaftarRi.anamnesa.anamnesaDiperoleh.anamnesaDiperolehDari" placeholder="Dari"
                    class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarRi.anamnesa.anamnesaDiperoleh.anamnesaDiperolehDari'))" :disabled=$disabledPropertyRjStatus :rows=7
                    wire:model.debounce.500ms="dataDaftarRi.anamnesa.anamnesaDiperoleh.anamnesaDiperolehDari" />

            </div>
            @error('dataDaftarRi.anamnesa.anamnesaDiperoleh.anamnesaDiperolehDari')
                <x-input-error :messages=$message />
            @enderror
        </div>

    </div>
</div>
