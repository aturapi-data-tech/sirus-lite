<div>
    <div class="w-full mb-1">

        <div>
            <x-input-label for="dataDaftarUgd.anamnesia.keluhanUtama.keluhanUtama" :value="__('Keluhan Utama')" :required="__(true)"
                class="pt-2 sm:text-xl" />

            <div class="mb-2 ">
                <x-text-input-area id="dataDaftarUgd.anamnesia.keluhanUtama.keluhanUtama" placeholder="Keluhan Utama"
                    class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarUgd.anamnesia.keluhanUtama.keluhanUtama'))" :disabled=$disabledPropertyRjStatus :rows=7
                    wire:model.debounce.500ms="dataDaftarUgd.anamnesia.keluhanUtama.keluhanUtama" />

            </div>
            @error('dataDaftarUgd.anamnesia.keluhanUtama.keluhanUtama')
                <x-input-error :messages=$message />
            @enderror
        </div>

    </div>
</div>
