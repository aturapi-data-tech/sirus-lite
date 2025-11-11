<div>
    <div class="w-full mb-1">
        <div class="pt-0">
            <x-input-label for="dataDaftarUgd.penilaian.nyeri.vas.vas" :value="__('Nyeri - Skala VAS')" :required="false"
                class="pt-2 sm:text-xl" />

            <div class="flex justify-center mb-4">
                <img src="/pain_scale_level.jpg" class="object-fill w-1/2 h-auto">
            </div>

            <div class="pt-2">
                <div class="grid grid-cols-11 gap-2 mt-2 ml-2">
                    @foreach ($dataDaftarUgd['penilaian']['nyeri']['vas']['vasOptions'] as $vasOptions)
                        @php
                            $vasColor =
                                $vasOptions['vas'] >= 0 && $vasOptions['vas'] <= 3
                                    ? 'bg-blue-300'
                                    : ($vasOptions['vas'] >= 4 && $vasOptions['vas'] <= 7
                                        ? 'bg-yellow-300'
                                        : 'bg-red-300');
                        @endphp

                        <x-radio-button :label="__($vasOptions['vas'])" value="{{ $vasOptions['vas'] }}"
                            wire:model="dataDaftarUgd.penilaian.nyeri.vas.vas" class="{{ $vasColor }}" />
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 mt-4">
                <div>
                    <x-input-label for="dataDaftarUgd.penilaian.nyeri.pencetus" :value="__('Pencetus')" :required="false" />
                    <x-text-input id="dataDaftarUgd.penilaian.nyeri.pencetus" placeholder="Pencetus" class="mt-1"
                        :errorshas="$errors->has('dataDaftarUgd.penilaian.nyeri.pencetus')" :disabled="$disabledPropertyRjStatus"
                        wire:model.debounce.500ms="dataDaftarUgd.penilaian.nyeri.pencetus" />
                </div>

                <div>
                    <x-input-label for="dataDaftarUgd.penilaian.nyeri.durasi" :value="__('Durasi')" :required="false" />
                    <x-text-input id="dataDaftarUgd.penilaian.nyeri.durasi" placeholder="Durasi" class="mt-1"
                        :errorshas="$errors->has('dataDaftarUgd.penilaian.nyeri.durasi')" :disabled="$disabledPropertyRjStatus"
                        wire:model.debounce.500ms="dataDaftarUgd.penilaian.nyeri.durasi" />
                </div>

                <div>
                    <x-input-label for="dataDaftarUgd.penilaian.nyeri.lokasi" :value="__('Lokasi')" :required="false" />
                    <x-text-input id="dataDaftarUgd.penilaian.nyeri.lokasi" placeholder="Lokasi" class="mt-1"
                        :errorshas="$errors->has('dataDaftarUgd.penilaian.nyeri.lokasi')" :disabled="$disabledPropertyRjStatus"
                        wire:model.debounce.500ms="dataDaftarUgd.penilaian.nyeri.lokasi" />
                </div>
            </div>
        </div>
    </div>
</div>
