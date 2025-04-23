<div>
    <div class="w-full mb-1">

        <div class="">
            <x-input-label for="dataDaftarUgd.suket.suketIstirahat.suketIstirahatHari" :value="__('Istirahat Selama ?')"
                :required="__(false)" />

            <x-text-input-mou id="dataDaftarUgd.suket.suketIstirahat.suketIstirahatHari" placeholder="Istirahat Selama ?"
                class="" :errorshas="__($errors->has('dataDaftarUgd.suket.suketIstirahat.suketIstirahatHari'))" :disabled=$disabledPropertyRjStatus :mou_label="__('Hari')"
                wire:model.debounce.500ms="dataDaftarUgd.suket.suketIstirahat.suketIstirahatHari" />
        </div>
        @error('dataDaftarUgd.suket.suketIstirahat.suketIstirahatHari')
            <x-input-error :messages=$message />
        @enderror
    </div>


    <div class="w-full mb-1">

        <div>
            <x-input-label for="dataDaftarUgd.suket.suketIstirahat.suketIstirahat" :value="__('Suket Istirahat Digunakan Untuk ?')" :required="__(true)" />

            <div class="mb-2 ">
                <x-text-input-area id="dataDaftarUgd.suket.suketIstirahat.suketIstirahat"
                    placeholder="Suket Istirahat Digunakan Untuk ?" class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarUgd.suket.suketIstirahat.suketIstirahat'))"
                    :disabled=$disabledPropertyRjStatus :rows=7
                    wire:model.debounce.500ms="dataDaftarUgd.suket.suketIstirahat.suketIstirahat" />

            </div>
            @error('dataDaftarUgd.suket.suketIstirahat.suketIstirahat')
                <x-input-error :messages=$message />
            @enderror
        </div>

    </div>


</div>
