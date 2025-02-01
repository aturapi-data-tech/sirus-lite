<div>
    <div class="space-y-4">
        <!-- Catatan Umum -->
        <div>
            <x-input-label for="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian5CatatanDanTandaTangan.catatanUmum"
                :value="__('Catatan Umum')" :required="__(false)" />
            <x-text-input-area id="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian5CatatanDanTandaTangan.catatanUmum"
                placeholder="Catatan Umum" class="mt-1" :errorshas="__(
                    $errors->has('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian5CatatanDanTandaTangan.catatanUmum'),
                )" :disabled="$disabledPropertyRjStatus"
                wire:model.debounce.500ms="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian5CatatanDanTandaTangan.catatanUmum" />
            @error('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian5CatatanDanTandaTangan.catatanUmum')
                <x-input-error :messages="$message" />
            @enderror
        </div>


        <!-- Jam Pengkaji -->
        <div>
            <x-input-label for="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian5CatatanDanTandaTangan.jamPengkaji"
                :value="__('Jam Pengkaji')" :required="__(true)" />

            <div class="mb-2">
                <div class="flex items-center mb-2">
                    <x-text-input
                        id="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian5CatatanDanTandaTangan.jamPengkaji"
                        placeholder="dd/mm/yyyy hh24:mi:ss" class="mt-1 ml-2" :errorshas="__(
                            $errors->has(
                                'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian5CatatanDanTandaTangan.jamPengkaji',
                            ),
                        )" :disabled="$disabledPropertyRjStatus"
                        wire:model.debounce.500ms="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian5CatatanDanTandaTangan.jamPengkaji" />

                    @if (!$dataDaftarRi['pengkajianAwalPasienRawatInap']['bagian5CatatanDanTandaTangan']['jamPengkaji'])
                        <div class="w-1/2 ml-2">
                            <div wire:loading wire:target="setJamPengkaji">
                                <x-loading />
                            </div>

                            <x-green-button :disabled="false"
                                wire:click.prevent="setJamPengkaji('{{ date('d/m/Y H:i:s') }}')" type="button"
                                wire:loading.remove>
                                <div wire:poll.20s>
                                    Set Jam Pengkaji: {{ date('d/m/Y H:i:s') }}
                                </div>
                            </x-green-button>
                        </div>
                    @endif
                </div>
            </div>

            @error('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian5CatatanDanTandaTangan.jamPengkaji')
                <x-input-error :messages="$message" />
            @enderror
        </div>

        <!-- Petugas Pengkaji  -->
        <div>
            <x-input-label for="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian5CatatanDanTandaTangan.petugasPengkaji"
                :value="__('Petugas Pengkaji')" :required="__(true)" />
            <x-text-input id="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian5CatatanDanTandaTangan.petugasPengkaji"
                placeholder="Petugas Pengkaji" class="mt-1" :errorshas="__(
                    $errors->has(
                        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian5CatatanDanTandaTangan.petugasPengkaji',
                    ),
                )" :disabled="true"
                wire:model.debounce.500ms="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian5CatatanDanTandaTangan.petugasPengkaji" />
            @error('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian5CatatanDanTandaTangan.petugasPengkaji')
                <x-input-error :messages="$message" />
            @enderror
        </div>

        <!-- Tombol TTD Petugas Pengkaji -->
        <div>
            <x-yellow-button :disabled="false" wire:click.prevent="setPetugasPengkaji()" type="button"
                wire:loading.remove>
                TTD Petugas Pengkaji
            </x-yellow-button>
        </div>

    </div>
</div>
