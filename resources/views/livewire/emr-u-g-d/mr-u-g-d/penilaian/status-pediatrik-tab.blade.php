<div>
    <div class="w-full mb-1">
        <div class="pt-0">
            <x-input-label for="dataDaftarUgd.penilaian.statusPediatrik.statusPediatrik" :value="__('Status Pediatrik')"
                :required="false" class="pt-2 sm:text-xl" />

            <div class="grid grid-cols-1 gap-2 mt-2 ml-2">
                @foreach ($dataDaftarUgd['penilaian']['statusPediatrik']['statusPediatrikOptions'] as $statusPediatrikOptions)
                    <x-radio-button :label="__($statusPediatrikOptions['statusPediatrik'])" value="{{ $statusPediatrikOptions['statusPediatrik'] }}"
                        wire:model="dataDaftarUgd.penilaian.statusPediatrik.statusPediatrik" />
                @endforeach
            </div>
        </div>
    </div>
</div>
