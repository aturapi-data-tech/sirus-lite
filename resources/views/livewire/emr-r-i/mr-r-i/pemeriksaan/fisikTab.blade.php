<div>
    <div class="w-full mb-1">


        <div class="pt-0">

            <x-input-label for="dataDaftarRi.pemeriksaan.fisik" :value="__('Fisik')" :required="__(false)"
                class="pt-2 sm:text-xl" />


            <div class="mb-2">
                <x-input-label for="dataDaftarRi.pemeriksaan.fisik" :value="__('Pemeriksaan Fisik')" :required="__(false)" />

                <x-text-input-area id="dataDaftarRi.pemeriksaan.fisik" placeholder="Keadaan Umum" class="mt-1 ml-2"
                    :errorshas="__($errors->has('dataDaftarRi.pemeriksaan.fisik'))" :disabled=$disabledPropertyRjStatus
                    wire:model.debounce.500ms="dataDaftarRi.pemeriksaan.fisik" :rows="__('3')" />
            </div>



        </div>




    </div>


</div>
