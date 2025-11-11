<div>
    <div class="w-full mb-1">
        <div class="pt-0">
            <x-input-label for="dataDaftarUgd.penilaian.fisik.fisik" :value="__('Penilaian Fisik')" :required="false"
                class="pt-2 sm:text-xl" />

            <div class="mb-2">
                <x-text-input-area id="dataDaftarUgd.penilaian.fisik.fisik" placeholder="Keadaan Umum" class="mt-1 ml-2"
                    :errorshas="$errors->has('dataDaftarUgd.penilaian.fisik.fisik')" :disabled="$disabledPropertyRjStatus" wire:model.debounce.500ms="dataDaftarUgd.penilaian.fisik.fisik"
                    rows="10" />
            </div>
        </div>
    </div>
</div>
