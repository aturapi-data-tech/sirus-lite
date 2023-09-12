<div>
    <div class="w-full mb-1">


        <div class="pt-2">

            <x-input-label for="dataDaftarPoliRJ.tandaVital.keadaanUmum" :value="__('Tanda Vital')" :required="__(false)"
                class="pt-2 sm:text-xl" />

            <x-input-label for="dataDaftarPoliRJ.tandaVital.keadaanUmum" :value="__('Keadaan Umum')" :required="__(false)" />

            <div class="mb-2 ">
                <x-text-input id="dataDaftarPoliRJ.tandaVital.keadaanUmum" placeholder="Keadaan Umum" class="mt-1 ml-2"
                    :errorshas="__($errors->has('dataDaftarPoliRJ.tandaVital.keadaanUmum'))" :disabled=$disabledPropertyRjStatus
                    wire:model.debounce.500ms="dataDaftarPoliRJ.tandaVital.keadaanUmum" />

            </div>

            <x-input-label for="dataDaftarPoliRJ.tandaVital.tingkatKesadaran" :value="__('Tingkat Kesadaran')" :required="__(false)" />

            <div class="mt-1">
                <div class="flex ">
                    <x-text-input placeholder="Tingkat Kesadaran" class="sm:rounded-none sm:rounded-l-lg"
                        :errorshas="__($errors->has('dataDaftarPoliRJ.tandaVital.tingkatKesadaran'))" :disabled=true
                        value="{{ $dataDaftarPoliRJ['pemeriksaan']['tandaVital']['tingkatKesadaran'] }}" />



                    <x-green-button :disabled=$disabledPropertyRjStatus
                        class="sm:rounded-none sm:rounded-r-lg sm:mb-0 sm:mr-0 sm:px-2"
                        wire:click.prevent="clicktingkatKesadaranlov()">
                        <svg class="-ml-1 mr-1.5 w-5 h-5" fill="currentColor" viewbox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path clip-rule="evenodd" fill-rule="evenodd"
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                        </svg>
                    </x-green-button>
                </div>
                {{-- LOV tingkatKesadaran --}}
                @include('livewire.mr-r-j.pemeriksaan.list-of-value-tingkatKesadaran')
            </div>

            <x-input-label for="dataDaftarPoliRJ.tandaVital.e" :value="__('E [Eye]')" :required="__(false)" />

            <div class="mb-2 ">
                <x-text-input id="dataDaftarPoliRJ.tandaVital.e" placeholder="E [Eye]" class="mt-1 ml-2"
                    :errorshas="__($errors->has('dataDaftarPoliRJ.tandaVital.e'))" :disabled=$disabledPropertyRjStatus
                    wire:model.debounce.500ms="dataDaftarPoliRJ.tandaVital.e" />
            </div>

            <x-input-label for="dataDaftarPoliRJ.tandaVital.m" :value="__('M [Motorik]')" :required="__(false)" />

            <div class="mb-2 ">
                <x-text-input id="dataDaftarPoliRJ.tandaVital.m" placeholder="M [Motorik]" class="mt-1 ml-2"
                    :errorshas="__($errors->has('dataDaftarPoliRJ.tandaVital.m'))" :disabled=$disabledPropertyRjStatus
                    wire:model.debounce.500ms="dataDaftarPoliRJ.tandaVital.m" />
            </div>

            <x-input-label for="dataDaftarPoliRJ.tandaVital.v" :value="__('V [Verbal]')" :required="__(false)" />

            <div class="mb-2 ">
                <x-text-input id="dataDaftarPoliRJ.tandaVital.v" placeholder="V [Verbal]" class="mt-1 ml-2"
                    :errorshas="__($errors->has('dataDaftarPoliRJ.tandaVital.v'))" :disabled=$disabledPropertyRjStatus
                    wire:model.debounce.500ms="dataDaftarPoliRJ.tandaVital.v" />
            </div>

            <x-input-label for="dataDaftarPoliRJ.tandaVital.gcs" :value="__('GCS')" :required="__(false)" />

            <div class="mb-2 ">
                <x-text-input id="dataDaftarPoliRJ.tandaVital.gcs" placeholder="GCS" class="mt-1 ml-2"
                    :errorshas="__($errors->has('dataDaftarPoliRJ.tandaVital.gcs'))" :disabled=$disabledPropertyRjStatus
                    wire:model.debounce.500ms="dataDaftarPoliRJ.tandaVital.gcs" />
            </div>


        </div>

    </div>


</div>
</div>
