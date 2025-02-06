<div>
    <div class="w-full mb-1">

        <div class="grid grid-cols-5 gap-2">




            <div class="col-span-2">
                {{-- LOV Dokter --}}
                @if (empty($collectingMyDokter))
                    <div class="">
                        @include('livewire.component.l-o-v.list-of-value-dokter.list-of-value-dokter')
                    </div>
                @else
                    <x-input-label for="levelingDokter.drName" :value="__('Nama Dokter')" :required="__(true)" />
                    <div>
                        <x-text-input id="levelingDokter.drName" placeholder="Nama Dokter" class="mt-1 ml-2"
                            :errorshas="__($errors->has('levelingDokter.drName'))" wire:model="levelingDokter.drName" :disabled="true" />

                    </div>
                @endif
                @error('levelingDokter.drId')
                    <x-input-error :messages=$message />
                @enderror
            </div>

            <div class="col-span-2 ml-2">
                <!-- Input untuk levelDokter -->
                <x-input-label for="levelingDokter.levelDokter" :value="__('Level Dokter')" :required="__(true)" />
                <div class="grid grid-cols-2 gap-2">
                    @foreach (['Utama', 'RawatGabung'] as $option)
                        <x-radio-button :label="__($option)" value="{{ $option }}"
                            wire:model="levelingDokter.levelDokter" />
                    @endforeach
                </div>
                @error('levelingDokter.levelDokter')
                    <x-input-error :messages="$message" />
                @enderror
            </div>

            <div class="col-span-1">
                <x-input-label for="" :value="__('Hapus')" :required="__(true)" />
                <x-alternative-button class="inline-flex ml-2" wire:click.prevent="resetlevelingDokter()">
                    <svg class="w-5 h-5 text-gray-800 dark:text-white" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                        <path
                            d="M17 4h-4V2a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v2H1a1 1 0 0 0 0 2h1v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6h1a1 1 0 1 0 0-2ZM7 2h4v2H7V2Zm1 14a1 1 0 1 1-2 0V8a1 1 0 0 1 2 0v8Zm4 0a1 1 0 0 1-2 0V8a1 1 0 0 1 2 0v8Z" />
                    </svg>
                </x-alternative-button>
            </div>
        </div>

        <!-- Tombol Simpan -->
        <div class="grid grid-cols-1 ml-2">
            <div wire:loading wire:target="addLevelingDokter">
                <x-loading />
            </div>

            <x-green-button :disabled="$disabledPropertyRjStatus" wire:click.prevent="addLevelingDokter()" type="button"
                wire:loading.remove>
                DPJP
            </x-green-button>
        </div>

        <div>
            @include('livewire.emr-r-i.mr-r-i.pengkajian-awal-pasien-rawat-inap.bagian1-2-data-umum-leveling-dokter-table')
        </div>
    </div>

</div>
