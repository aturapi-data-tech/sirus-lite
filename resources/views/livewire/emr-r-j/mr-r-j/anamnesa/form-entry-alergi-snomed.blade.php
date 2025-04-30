<div>
    <div class="grid items-end grid-cols-2 gap-2">
        {{-- LOV Snomed --}}
        <div class="">
            @if (empty($formEntryAlergiSnomed['snomedCode']))
                @if (empty($dataSnomedLovSearch))
                    <div>
                        <x-input-label for="lovRJAlergi" :value="__('Cari Kode Snomed Alergi')" :required="__(true)" />
                        <x-text-input id="lovRJAlergi" placeholder="Cari Snomed" class="mt-1 ml-2" :errorshas="__($errors->has('lovRJAlergi'))"
                            :disabled=false wire:model.debounce.500ms="lovRJAlergi" />
                    </div>
                @else
                    @if ($LOVParentStatus === 'lovRJAlergi')
                        <div>
                            @include('livewire.component.l-o-v.list-of-value-snomed.list-of-value-snomed')
                        </div>
                    @endif
                @endif
            @else
                <div>
                    <x-input-label for="formEntryAlergiSnomed.snomedDisplay" :value="__('Display Snomed Alergi')" :required="__(true)"
                        wire:click="resetLovRJAlergi()" />
                    <x-text-input id="formEntryAlergiSnomed.snomedDisplay" placeholder="Display Snomed" class="mt-1 ml-2"
                        :errorshas="__($errors->has('formEntryAlergiSnomed.snomedDisplay'))" wire:model="formEntryAlergiSnomed.snomedDisplay" :disabled="true" />
                </div>
            @endif

            @error('formEntryAlergiSnomed.snomedCode')
                <x-input-error :messages=$message />
            @enderror
        </div>

        {{-- Tombol Simpan --}}
        <div class="grid grid-cols-1 gap-2 px-2">
            <x-green-button wire:click.prevent="addAlergiSnomed" :disabled="empty($formEntryAlergiSnomed['snomedCode'])">
                Simpan Alergi
            </x-green-button>
        </div>
    </div>

    {{-- Tabel riwayat alergi --}}
    <div class="overflow-x-auto rounded-lg">
        @include('livewire.emr-r-j.mr-r-j.anamnesa.form-entry-alergi-snomed-table')
    </div>
</div>
