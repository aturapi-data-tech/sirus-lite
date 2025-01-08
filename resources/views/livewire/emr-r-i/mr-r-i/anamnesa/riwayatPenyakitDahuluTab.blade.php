<div>
    <div class="w-full mb-1">

        <div>
            <x-input-label for="dataDaftarRi.anamnesa.riwayatPenyakitDahulu.riwayatPenyakitDahulu" :value="__('Riwayat Penyakit Dahulu')"
                :required="__(true)" class="pt-2 sm:text-xl" />

            <div class="mb-2 ">
                <x-text-input-area id="dataDaftarRi.anamnesa.riwayatPenyakitDahulu.riwayatPenyakitDahulu"
                    placeholder="Riwayat Perjalanan Penyakit" class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarRi.anamnesa.riwayatPenyakitDahulu.riwayatPenyakitDahulu'))"
                    :disabled=$disabledPropertyRjStatus :rows=3
                    wire:model.debounce.500ms="dataDaftarRi.anamnesa.riwayatPenyakitDahulu.riwayatPenyakitDahulu" />

            </div>
            @error('dataDaftarRi.anamnesa.riwayatPenyakitDahulu.riwayatPenyakitDahulu')
                <x-input-error :messages=$message />
            @enderror
        </div>

        <div>
            <x-input-label for="dataDaftarRi.anamnesa.alergi.alergi" :value="__('Alergi')" :required="__(false)"
                class="pt-2 sm:text-xl" />

            <div class="mb-2 ">
                <x-text-input-area id="dataDaftarRi.anamnesa.alergi.alergi"
                    placeholder="Jenis Alergi / Alergi [Makanan / Obat / Udara]" class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarRi.anamnesa.alergi.alergi'))"
                    :disabled=$disabledPropertyRjStatus :rows=3
                    wire:model.debounce.500ms="dataDaftarRi.anamnesa.alergi.alergi" />

            </div>
            @error('dataDaftarRi.anamnesa.alergi.alergi')
                <x-input-error :messages=$message />
            @enderror
        </div>


        <div class="pt-2">
            {{-- <x-input-label for="dataDaftarRi.anamnesa.obat.obat" :value="__('Pemberian Obat')" :required="__(false)" /> --}}
            <x-input-label for="dataDaftarRi.anamnesa.obat.obat" :value="__('Rekonsiliasi Obat')" :required="__(false)"
                class="pt-2 sm:text-xl" />

            @include('livewire.emr-r-i.mr-r-i.anamnesa.rekonsiliasiObat')


        </div>

        {{-- <div class="pt-2">
            <x-input-label for="dataDaftarRi.anamnesa.lainLain.lainLain" :value="__('Lain-Lain')" :required="__(false)" />

            <div class="grid grid-cols-2 pt-2">
                <x-check-box value='1' :label="__('Merokok')"
                    wire:model.debounce.500ms="dataDaftarRi.anamnesa.lainLain.merokok" />
                <x-check-box value='1' :label="__('Terpapar Rokok')"
                    wire:model.debounce.500ms="dataDaftarRi.anamnesa.lainLain.terpaparRokok" />
            </div>
        </div>

        <div class="pt-2">
            <x-input-label for="dataDaftarRi.anamnesa.faktorResiko.faktorResiko" :value="__('Faktor Resiko')"
                :required="__(false)" />

            <div class="grid grid-cols-5 gap-2 pt-2">
                <x-check-box value='1' :label="__('hipertensi')"
                    wire:model.debounce.500ms="dataDaftarRi.anamnesa.faktorResiko.hipertensi" />
                <x-check-box value='1' :label="__('diabetesMelitus')"
                    wire:model.debounce.500ms="dataDaftarRi.anamnesa.faktorResiko.diabetesMelitus" />


                <x-check-box value='1' :label="__('penyakitJantung')"
                    wire:model.debounce.500ms="dataDaftarRi.anamnesa.faktorResiko.penyakitJantung" />
                <x-check-box value='1' :label="__('asma')"
                    wire:model.debounce.500ms="dataDaftarRi.anamnesa.faktorResiko.asma" />
                <x-check-box value='1' :label="__('stroke')"
                    wire:model.debounce.500ms="dataDaftarRi.anamnesa.faktorResiko.stroke" />
                <x-check-box value='1' :label="__('liver')"
                    wire:model.debounce.500ms="dataDaftarRi.anamnesa.faktorResiko.liver" />

                <x-check-box value='1' :label="__('tuberculosisParu')"
                    wire:model.debounce.500ms="dataDaftarRi.anamnesa.faktorResiko.tuberculosisParu" />
                <x-check-box value='1' :label="__('rokok')"
                    wire:model.debounce.500ms="dataDaftarRi.anamnesa.faktorResiko.rokok" />
                <x-check-box value='1' :label="__('minumAlkohol')"
                    wire:model.debounce.500ms="dataDaftarRi.anamnesa.faktorResiko.minumAlkohol" />
                <x-check-box value='1' :label="__('ginjal')"
                    wire:model.debounce.500ms="dataDaftarRi.anamnesa.faktorResiko.ginjal" />

            </div>
            <div class="mb-2 ">
                <x-text-input id="dataDaftarRi.anamnesa.faktorResiko.lainLain"
                    placeholder="Jenis Faktor Resiko Lain-Lain" class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarRi.anamnesa.faktorResiko.lainLain'))"
                    :disabled=$disabledPropertyRjStatus
                    wire:model.debounce.500ms="dataDaftarRi.anamnesa.faktorResiko.lainLain" />

            </div>
        </div>

        <div class="pt-2">
            <x-input-label for="dataDaftarRi.anamnesa.penyakitKeluarga.penyakitKeluarga" :value="__('Penyakit Keluarga')"
                :required="__(false)" />

            <div class="grid grid-cols-5 gap-2 pt-2">
                <x-check-box value='1' :label="__('hipertensi')"
                    wire:model.debounce.500ms="dataDaftarRi.anamnesa.penyakitKeluarga.hipertensi" />
                <x-check-box value='1' :label="__('diabetesMelitus')"
                    wire:model.debounce.500ms="dataDaftarRi.anamnesa.penyakitKeluarga.diabetesMelitus" />
                <x-check-box value='1' :label="__('penyakitJantung')"
                    wire:model.debounce.500ms="dataDaftarRi.anamnesa.penyakitKeluarga.penyakitJantung" />
                <x-check-box value='1' :label="__('asma')"
                    wire:model.debounce.500ms="dataDaftarRi.anamnesa.penyakitKeluarga.asma" />
            </div>
            <div class="mb-2 ">
                <x-text-input id="dataDaftarRi.anamnesa.penyakitKeluarga.lainLain"
                    placeholder="Jenis Faktor Resiko Lain-Lain" class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarRi.anamnesa.penyakitKeluarga.lainLain'))"
                    :disabled=$disabledPropertyRjStatus
                    wire:model.debounce.500ms="dataDaftarRi.anamnesa.penyakitKeluarga.lainLain" />

            </div>
        </div> --}}



    </div>
</div>
