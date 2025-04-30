<div>
    <div class="w-full mb-1">

        <div>
            <x-input-label for="dataDaftarPoliRJ.anamnesa.keluhanUtama.keluhanUtama" :value="__('Keluhan Utama')" :required="__(true)"
                class="pt-2 sm:text-xl" />

            <div class="mb-2 ">
                <x-text-input-area id="dataDaftarPoliRJ.anamnesa.keluhanUtama.keluhanUtama" placeholder="Keluhan Utama"
                    class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarPoliRJ.anamnesa.keluhanUtama.keluhanUtama'))" :disabled=$disabledPropertyRjStatus :rows=7
                    wire:model.debounce.500ms="dataDaftarPoliRJ.anamnesa.keluhanUtama.keluhanUtama" />

            </div>
            @error('dataDaftarPoliRJ.anamnesa.keluhanUtama.keluhanUtama')
                <x-input-error :messages=$message />
            @enderror
        </div>

        @role(['Perawat', 'Mr', 'Admin'])
            {{-- LOV Snomed --}}
            <div class="">
                @if (empty($dataDaftarPoliRJ['anamnesa']['keluhanUtama']['snomedCode']))
                    @if (empty($dataSnomedLovSearch))
                        <div>
                            <x-input-label for="lovRJKeluhanUtama" :value="__('Cari Kode Snomed Keluhan Utama')" :required="__(true)" />
                            <x-text-input id="lovRJKeluhanUtama" placeholder="Cari Snomed" class="mt-1 ml-2"
                                :errorshas="__($errors->has('lovRJKeluhanUtama'))" :disabled=false wire:model.debounce.500ms="lovRJKeluhanUtama" />
                        </div>
                    @else
                        @if ($LOVParentStatus === 'lovRJKeluhanUtama')
                            <div class="col-span-2">
                                @include('livewire.component.l-o-v.list-of-value-snomed.list-of-value-snomed')
                            </div>
                        @endif
                    @endif
                @else
                    <x-input-label for="dataDaftarPoliRJ.anamnesa.keluhanUtama.snomedDisplay" :value="__('Display Snomed Keluhan Utama')"
                        :required="__(true)" wire:click="resetLovRJKeluhanUtama()" />
                    <div>
                        <x-text-input id="dataDaftarPoliRJ.anamnesa.keluhanUtama.snomedDisplay" placeholder="Display Snomed"
                            class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarPoliRJ.anamnesa.keluhanUtama.snomedDisplay'))"
                            wire:model="dataDaftarPoliRJ.anamnesa.keluhanUtama.snomedDisplay" :disabled="true" />

                    </div>
                @endif

                @error('dataDaftarPoliRJ.anamnesa.keluhanUtama.snomedCode')
                    <x-input-error :messages=$message />
                @enderror
            </div>
        @endrole



    </div>
</div>
