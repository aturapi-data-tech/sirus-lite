<div>
    <div class="w-full mb-1">
        <div class="pt-0">
            <x-input-label for="dataDaftarUgd.penilaian.diagnosis.diagnosis" :value="__('Diagnosis')" :required="false"
                class="pt-2 sm:text-xl" />

            <div class="mb-2">
                <x-text-input-area id="dataDaftarUgd.penilaian.diagnosis.diagnosis" placeholder="Diagnosis"
                    class="mt-1 ml-2" :errorshas="$errors->has('dataDaftarUgd.penilaian.diagnosis.diagnosis')" :disabled="$disabledPropertyRjStatus"
                    wire:model.debounce.500ms="dataDaftarUgd.penilaian.diagnosis.diagnosis" rows="10" />
            </div>
        </div>
    </div>
</div>
