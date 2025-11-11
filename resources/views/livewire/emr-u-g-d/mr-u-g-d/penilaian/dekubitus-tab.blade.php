<div>
    <div class="w-full mb-1">
        <div class="pt-0">
            <x-input-label :value="__('Penilaian Dekubitus')" :required="false" class="pt-2 sm:text-xl" />

            <div class="grid grid-cols-1 gap-4 mt-4">
                <!-- Kondisi Fisik Umum -->
                <div>
                    <x-input-label for="dataDaftarUgd.penilaian.dekubitus.kodisiFisik" :value="__('Kondisi Fisik Umum')"
                        :required="false" />
                    <div class="grid grid-cols-2 gap-2 mt-2">
                        @foreach ($dataDaftarUgd['penilaian']['dekubitus']['kodisiFisikOptions'] as $option)
                            <x-radio-button :label="__($option['kodisiFisik'])" value="{{ $option['kodisiFisik'] }}"
                                wire:model="dataDaftarUgd.penilaian.dekubitus.kodisiFisik" />
                        @endforeach
                    </div>
                </div>

                <!-- Kesadaran -->
                <div>
                    <x-input-label for="dataDaftarUgd.penilaian.dekubitus.kesadaran" :value="__('Kesadaran')"
                        :required="false" />
                    <div class="grid grid-cols-2 gap-2 mt-2">
                        @foreach ($dataDaftarUgd['penilaian']['dekubitus']['kesadaranOptions'] as $option)
                            <x-radio-button :label="__($option['kesadaran'])" value="{{ $option['kesadaran'] }}"
                                wire:model="dataDaftarUgd.penilaian.dekubitus.kesadaran" />
                        @endforeach
                    </div>
                </div>

                <!-- Aktifitas -->
                <div>
                    <x-input-label for="dataDaftarUgd.penilaian.dekubitus.aktifitas" :value="__('Aktifitas')"
                        :required="false" />
                    <div class="grid grid-cols-2 gap-2 mt-2">
                        @foreach ($dataDaftarUgd['penilaian']['dekubitus']['aktifitasOptions'] as $option)
                            <x-radio-button :label="__($option['aktifitas'])" value="{{ $option['aktifitas'] }}"
                                wire:model="dataDaftarUgd.penilaian.dekubitus.aktifitas" />
                        @endforeach
                    </div>
                </div>

                <!-- Mobilitas -->
                <div>
                    <x-input-label for="dataDaftarUgd.penilaian.dekubitus.mobilitas" :value="__('Mobilitas')"
                        :required="false" />
                    <div class="grid grid-cols-2 gap-2 mt-2">
                        @foreach ($dataDaftarUgd['penilaian']['dekubitus']['mobilitasOptions'] as $option)
                            <x-radio-button :label="__($option['mobilitas'])" value="{{ $option['mobilitas'] }}"
                                wire:model="dataDaftarUgd.penilaian.dekubitus.mobilitas" />
                        @endforeach
                    </div>
                </div>

                <!-- Inkontinensia -->
                <div>
                    <x-input-label for="dataDaftarUgd.penilaian.dekubitus.inkontinensia" :value="__('Inkontinensia')"
                        :required="false" />
                    <div class="grid grid-cols-2 gap-2 mt-2">
                        @foreach ($dataDaftarUgd['penilaian']['dekubitus']['inkontinensiaOptions'] as $option)
                            <x-radio-button :label="__($option['inkontinensia'])" value="{{ $option['inkontinensia'] }}"
                                wire:model="dataDaftarUgd.penilaian.dekubitus.inkontinensia" />
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
