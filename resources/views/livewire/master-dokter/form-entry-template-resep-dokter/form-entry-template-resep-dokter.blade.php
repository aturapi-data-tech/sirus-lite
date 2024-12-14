@php
    $disabledProperty = false;
    $disabledPropertyId = $isOpenMode == 'insert' ? false : true;
@endphp

<div>


    {{-- FormMasterPoli --}}
    <div id="FormMasterPoli" class="grid grid-cols-3 gap-2 px-4 pt-4">
        <div class="col-span-1">
            {{-- {{ $isOpenMode }}
            {{ $dokterId }}
            {{ $temprId }} --}}


            <div class="grid grid-cols-2 gap-2">
                <div>
                    <x-input-label for="FormEntry.temprId" :value="__('Kode Template Resep')" :required="__($errors->has('FormEntry.temprId'))" />
                    <div class="flex items-center mb-2">
                        <x-text-input id="FormEntry.temprId" placeholder="Kode Template" class="mt-1 ml-2"
                            :errorshas="__($errors->has('FormEntry.temprId'))" :disabled=$disabledProperty wire:model="FormEntry.temprId" />
                    </div>
                    @error('FormEntry.temprId')
                        <x-input-error :messages=$message />
                    @enderror
                </div>

                <div>
                    <x-input-label for="FormEntry.temprDesc" :value="__('Keterangan')" :required="__($errors->has('FormEntry.temprDesc'))" />
                    <div class="flex items-center mb-2">
                        <x-text-input id="FormEntry.temprDesc" placeholder="Nama Dokter" class="mt-1 ml-2"
                            :errorshas="__($errors->has('FormEntry.temprDesc'))" :disabled=$disabledProperty wire:model="FormEntry.temprDesc" />
                    </div>
                    @error('FormEntry.temprDesc')
                        <x-input-error :messages=$message />
                    @enderror
                </div>
            </div>

            <div class="">
                @include('livewire.master-dokter.form-entry-template-resep-dokter.table-template-resep-dokter')
            </div>
        </div>

        <div class="col-span-2">
            @include('livewire.master-dokter.form-entry-template-resep-dokter.tab-entry-template-resep-dokter')
        </div>



    </div>

    {{-- down bar --}}
    <div class="sticky bottom-0 flex justify-between px-4 py-3 bg-gray-50 sm:px-6">
        <div class="">

            <x-light-button wire:click="closeModal()" type="button">Keluar</x-light-button>



        </div>

        <div class="">
            <x-green-button :disabled=$disabledProperty wire:click.prevent="store()" type="button">Simpan
            </x-green-button>
        </div>
    </div>


</div>
