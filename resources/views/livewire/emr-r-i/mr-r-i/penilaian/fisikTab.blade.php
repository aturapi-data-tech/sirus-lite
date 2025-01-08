<div>
    <div class="w-full mb-1">


        <div class="pt-0">

            <x-input-label for="dataDaftarRi.penilaian.fisik.fisik" :value="__('Fisik')" :required="__(false)"
                class="pt-2 sm:text-xl" />


            <div class="mb-2">
                <x-input-label for="dataDaftarRi.penilaian.fisik.fisik" :value="__('Penilaian Fisik')" :required="__(false)" />

                <x-text-input-area id="dataDaftarRi.penilaian.fisik.fisik" placeholder="Keadaan Umum" class="mt-1 ml-2"
                    :errorshas="__($errors->has('dataDaftarRi.penilaian.fisik.fisik'))" :disabled=$disabledPropertyRjStatus
                    wire:model.debounce.500ms="dataDaftarRi.penilaian.fisik.fisik" :rows="__('10')" />
            </div>







        </div>




    </div>


</div>
