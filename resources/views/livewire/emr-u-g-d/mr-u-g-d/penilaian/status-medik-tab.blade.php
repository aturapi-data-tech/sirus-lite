<div>
    <div class="w-full mb-1">
        <div class="pt-0">
            <x-input-label for="dataDaftarUgd.penilaian.statusMedik.statusMedik" :value="__('Status Medik')" :required="false"
                class="pt-2 sm:text-xl" />

            <div class="grid grid-cols-2 gap-2 mt-2 ml-2">
                @foreach ($dataDaftarUgd['penilaian']['statusMedik']['statusMedikOptions'] as $statusMedikOptions)
                    <x-radio-button :label="__($statusMedikOptions['statusMedik'])" value="{{ $statusMedikOptions['statusMedik'] }}"
                        wire:model="dataDaftarUgd.penilaian.statusMedik.statusMedik" />
                @endforeach
            </div>
        </div>
    </div>
</div>
