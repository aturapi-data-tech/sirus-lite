<div>
    <div class="grid grid-cols-2 gap-2">
        {{-- Diagnosa / Assessment --}}
        <div>
            <x-input-label for="diagnosa_awal" :value="__('Diagnosa Awal')" :required="false"
                class="block text-sm font-medium text-gray-700" />
            <x-text-input-area id="diagnosa_awal"
                wire:model.debounce.500ms="dataDaftarRi.pengkajianDokter.diagnosaAssesment.diagnosaAwal"
                :errorshas="$errors->has('dataDaftarRi.pengkajianDokter.diagnosaAssesment.diagnosaAwal')" :disabled="false" rows="3"
                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary sm:text-sm" />
            @error('dataDaftarRi.pengkajianDokter.diagnosaAssesment.diagnosaAwal')
                <x-input-error :messages="__($message)" />
            @enderror
        </div>

        {{-- Rencana - Penegakan Diagnosa --}}
        <div>
            <x-input-label for="penegakan_diagnosa" :value="__('Penegakan Diagnosa')" :required="false"
                class="block text-sm font-medium text-gray-700" />
            <x-text-input-area id="penegakan_diagnosa"
                wire:model.debounce.500ms="dataDaftarRi.pengkajianDokter.rencana.penegakanDiagnosa" :errorshas="$errors->has('dataDaftarRi.pengkajianDokter.rencana.penegakanDiagnosa')"
                :disabled="false" rows="3"
                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary sm:text-sm" />
            @error('dataDaftarRi.pengkajianDokter.rencana.penegakanDiagnosa')
                <x-input-error :messages="__($message)" />
            @enderror
        </div>

        {{-- Rencana - Terapi --}}
        <div>
            <x-input-label for="terapi" :value="__('Terapi')" :required="false"
                class="block text-sm font-medium text-gray-700" />
            <x-text-input-area id="terapi" wire:model.debounce.500ms="dataDaftarRi.pengkajianDokter.rencana.terapi"
                :errorshas="$errors->has('dataDaftarRi.pengkajianDokter.rencana.terapi')" :disabled="false" rows="3"
                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary sm:text-sm" />
            @error('dataDaftarRi.pengkajianDokter.rencana.terapi')
                <x-input-error :messages="__($message)" />
            @enderror

            @role(['Dokter', 'Admin'])
                <div class="grid grid-cols-1 gap-2 mt-2">
                    <x-yellow-button :disabled="false" wire:click="openModalEresepRI" type="button" wire:loading.remove>
                        E-resep
                    </x-yellow-button>

                    <div wire:loading wire:target="openModalEresepRI">
                        <x-loading />
                    </div>

                    @if ($isOpenEresepRI)
                        @include('livewire.emr-r-i.create-emr-r-i-racikan-nonracikan')
                    @endif
                </div>
            @endrole
        </div>

        {{-- Rencana - Terapi Pulang --}}
        <div>
            <x-input-label for="terapiPulang" :value="__('Terapi Pulang')" :required="false"
                class="block text-sm font-medium text-gray-700" />
            <x-text-input-area id="terapiPulang"
                wire:model.debounce.500ms="dataDaftarRi.pengkajianDokter.rencana.terapiPulang" :errorshas="$errors->has('dataDaftarRi.pengkajianDokter.rencana.terapiPulang')"
                :disabled="false" rows="3"
                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary sm:text-sm" />
            @error('dataDaftarRi.pengkajianDokter.rencana.terapiPulang')
                <x-input-error :messages="__($message)" />
            @enderror
        </div>

        {{-- Rencana - Diet --}}
        <div>
            <x-input-label for="diet" :value="__('Diet')" :required="false"
                class="block text-sm font-medium text-gray-700" />
            <x-text-input-area id="diet" wire:model.debounce.500ms="dataDaftarRi.pengkajianDokter.rencana.diet"
                :errorshas="$errors->has('dataDaftarRi.pengkajianDokter.rencana.diet')" :disabled="false" rows="3"
                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary sm:text-sm" />
            @error('dataDaftarRi.pengkajianDokter.rencana.diet')
                <x-input-error :messages="__($message)" />
            @enderror
        </div>

        {{-- Rencana - Edukasi --}}
        <div>
            <x-input-label for="edukasi" :value="__('Edukasi')" :required="false"
                class="block text-sm font-medium text-gray-700" />
            <x-text-input-area id="edukasi" wire:model.debounce.500ms="dataDaftarRi.pengkajianDokter.rencana.edukasi"
                :errorshas="$errors->has('dataDaftarRi.pengkajianDokter.rencana.edukasi')" :disabled="false" rows="3"
                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary sm:text-sm" />
            @error('dataDaftarRi.pengkajianDokter.rencana.edukasi')
                <x-input-error :messages="__($message)" />
            @enderror
        </div>

        {{-- Rencana - Monitoring --}}
        <div>
            <x-input-label for="monitoring" :value="__('Monitoring')" :required="false"
                class="block text-sm font-medium text-gray-700" />
            <x-text-input-area id="monitoring"
                wire:model.debounce.500ms="dataDaftarRi.pengkajianDokter.rencana.monitoring" :errorshas="$errors->has('dataDaftarRi.pengkajianDokter.rencana.monitoring')"
                :disabled="false" rows="3"
                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary sm:text-sm" />
            @error('dataDaftarRi.pengkajianDokter.rencana.monitoring')
                <x-input-error :messages="__($message)" />
            @enderror
        </div>
    </div>
</div>
