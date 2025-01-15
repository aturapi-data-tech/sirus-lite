<div class="space-y-4">

    <!-- Jam Dokter Pengkaji -->
    <div>
        <x-input-label for="dataDaftarRi.pengkajianDokter.tandaTanganDokter.jamDokterPengkaji" :value="__('Jam Dokter Pengkaji')"
            :required="__(true)" />

        <div class="mb-2">
            <div class="flex items-center mb-2">
                <x-text-input id="dataDaftarRi.pengkajianDokter.tandaTanganDokter.jamDokterPengkaji"
                    placeholder="dd/mm/yyyy hh24:mi:ss" class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarRi.pengkajianDokter.tandaTanganDokter.jamDokterPengkaji'))" :disabled="$disabledPropertyRjStatus"
                    wire:model.debounce.500ms="dataDaftarRi.pengkajianDokter.tandaTanganDokter.jamDokterPengkaji" />

                @if (!$dataDaftarRi['pengkajianDokter']['tandaTanganDokter']['jamDokterPengkaji'])
                    <div class="w-1/2 ml-2">
                        <div wire:loading wire:target="setJamDokterPengkaji">
                            <x-loading />
                        </div>

                        <x-green-button :disabled="false"
                            wire:click.prevent="setJamDokterPengkaji('{{ date('d/m/Y H:i:s') }}')" type="button"
                            wire:loading.remove>
                            <div wire:poll>
                                Set Jam Dokter Pengkaji: {{ date('d/m/Y H:i:s') }}
                            </div>
                        </x-green-button>
                    </div>
                @endif
            </div>
        </div>

        @error('dataDaftarRi.pengkajianDokter.tandaTanganDokter.jamDokterPengkaji')
            <x-input-error :messages="$message" />
        @enderror
    </div>

    <!-- Dokter Pengkaji -->
    <div>
        <x-input-label for="dataDaftarRi.pengkajianDokter.tandaTanganDokter.dokterPengkaji" :value="__('Dokter Pengkaji')"
            :required="__(true)" />
        <x-text-input id="dataDaftarRi.pengkajianDokter.tandaTanganDokter.dokterPengkaji" placeholder="Dokter Pengkaji"
            class="mt-1" :disabled="true"
            wire:model.debounce.500ms="dataDaftarRi.pengkajianDokter.tandaTanganDokter.dokterPengkaji" />
        @error('dataDaftarRi.pengkajianDokter.tandaTanganDokter.dokterPengkaji')
            <x-input-error :messages="$message" />
        @enderror
    </div>
    <!-- Tombol TTD Dokter -->
    <div>
        <x-yellow-button :disabled="false" wire:click.prevent="setDokterPengkaji()" type="button" wire:loading.remove>
            TTD Dokter
        </x-yellow-button>
    </div>
</div>
