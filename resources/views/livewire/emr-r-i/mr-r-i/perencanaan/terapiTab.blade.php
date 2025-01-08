<div>
    <div class="w-full mb-1">

        <div>
            <x-input-label for="dataDaftarRi.perencanaan.terapi.terapi" :value="__('Terapi')" :required="__(true)" />

            <div class="mb-2 ">
                <x-text-input-area id="dataDaftarRi.perencanaan.terapi.terapi" placeholder="Terapi" class="mt-1 ml-2"
                    :errorshas="__($errors->has('dataDaftarRi.perencanaan.terapi.terapi'))" :disabled=$disabledPropertyRjStatus :rows=7
                    wire:model.debounce.500ms="dataDaftarRi.perencanaan.terapi.terapi" />

            </div>
            @error('dataDaftarRi.perencanaan.terapi.terapi')
                <x-input-error :messages=$message />
            @enderror
        </div>
        @role(['Dokter', 'Admin'])
            <div class="grid grid-cols-1 gap-2 ">
                <x-yellow-button :disabled=false wire:click.prevent="openModalEresepRI()" type="button" wire:loading.remove>
                    E-resep
                </x-yellow-button>


            </div>
            @if ($isOpenEresepRI)
                @include('livewire.emr-r-i.create-emr-r-i-racikan-nonracikan')
            @endif
        @endrole

    </div>
</div>
