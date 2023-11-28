<div>
    <div class="w-full mb-1">

        <div>
            <x-input-label for="dataDaftarUgd.anamnesia.riwayatPenyakitDahulu.riwayatPenyakitDahulu" :value="__('Riwayat Penyakit Dahulu')"
                :required="__(true)" class="pt-2 sm:text-xl" />

            <div class="mb-2 ">
                <x-text-input-area id="dataDaftarUgd.anamnesia.riwayatPenyakitDahulu.riwayatPenyakitDahulu"
                    placeholder="Riwayat Perjalanan Penyakit" class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarUgd.anamnesia.riwayatPenyakitDahulu.riwayatPenyakitDahulu'))"
                    :disabled=$disabledPropertyRjStatus :rows=7
                    wire:model.debounce.500ms="dataDaftarUgd.anamnesia.riwayatPenyakitDahulu.riwayatPenyakitDahulu" />

            </div>
            @error('dataDaftarUgd.anamnesia.riwayatPenyakitDahulu.riwayatPenyakitDahulu')
                <x-input-error :messages=$message />
            @enderror
        </div>

        <div>
            <x-input-label for="dataDaftarUgd.anamnesia.alergi.alergi" :value="__('Alergi')" :required="__(false)"
                class="pt-2 sm:text-xl" />

            <div class="mb-2 ">
                <x-text-input-area id="dataDaftarUgd.anamnesia.alergi.alergi"
                    placeholder="Jenis Alergi / Alergi [Makanan / Obat / Udara]" class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarUgd.anamnesia.alergi.alergi'))"
                    :disabled=$disabledPropertyRjStatus :rows=7
                    wire:model.debounce.500ms="dataDaftarUgd.anamnesia.alergi.alergi" />

            </div>
            @error('dataDaftarUgd.anamnesia.alergi.alergi')
                <x-input-error :messages=$message />
            @enderror
        </div>


        <div class="pt-2">
            {{-- <x-input-label for="dataDaftarUgd.anamnesia.obat.obat" :value="__('Pemberian Obat')" :required="__(false)" /> --}}
            <x-input-label for="dataDaftarUgd.anamnesia.obat.obat" :value="__('Rekonsiliasi Obat')" :required="__(false)"
                class="pt-2 sm:text-xl" />

            @include('livewire.emr-u-g-d.mr-u-g-d.anamnesa.rekonsiliasiObat')


        </div>

        {{-- <div class="pt-2">
            <x-input-label for="dataDaftarUgd.anamnesia.lainLain.lainLain" :value="__('Lain-Lain')" :required="__(false)" />

            <div class="grid grid-cols-2 pt-2">
                <x-check-box value='1' :label="__('Merokok')"
                    wire:model.debounce.500ms="dataDaftarUgd.anamnesia.lainLain.merokok" />
                <x-check-box value='1' :label="__('Terpapar Rokok')"
                    wire:model.debounce.500ms="dataDaftarUgd.anamnesia.lainLain.terpaparRokok" />
            </div>
        </div>

        <div class="pt-2">
            <x-input-label for="dataDaftarUgd.anamnesia.faktorResiko.faktorResiko" :value="__('Faktor Resiko')"
                :required="__(false)" />

            <div class="grid grid-cols-5 gap-2 pt-2">
                <x-check-box value='1' :label="__('hipertensi')"
                    wire:model.debounce.500ms="dataDaftarUgd.anamnesia.faktorResiko.hipertensi" />
                <x-check-box value='1' :label="__('diabetesMelitus')"
                    wire:model.debounce.500ms="dataDaftarUgd.anamnesia.faktorResiko.diabetesMelitus" />


                <x-check-box value='1' :label="__('penyakitJantung')"
                    wire:model.debounce.500ms="dataDaftarUgd.anamnesia.faktorResiko.penyakitJantung" />
                <x-check-box value='1' :label="__('asma')"
                    wire:model.debounce.500ms="dataDaftarUgd.anamnesia.faktorResiko.asma" />
                <x-check-box value='1' :label="__('stroke')"
                    wire:model.debounce.500ms="dataDaftarUgd.anamnesia.faktorResiko.stroke" />
                <x-check-box value='1' :label="__('liver')"
                    wire:model.debounce.500ms="dataDaftarUgd.anamnesia.faktorResiko.liver" />

                <x-check-box value='1' :label="__('tuberculosisParu')"
                    wire:model.debounce.500ms="dataDaftarUgd.anamnesia.faktorResiko.tuberculosisParu" />
                <x-check-box value='1' :label="__('rokok')"
                    wire:model.debounce.500ms="dataDaftarUgd.anamnesia.faktorResiko.rokok" />
                <x-check-box value='1' :label="__('minumAlkohol')"
                    wire:model.debounce.500ms="dataDaftarUgd.anamnesia.faktorResiko.minumAlkohol" />
                <x-check-box value='1' :label="__('ginjal')"
                    wire:model.debounce.500ms="dataDaftarUgd.anamnesia.faktorResiko.ginjal" />

            </div>
            <div class="mb-2 ">
                <x-text-input id="dataDaftarUgd.anamnesia.faktorResiko.lainLain"
                    placeholder="Jenis Faktor Resiko Lain-Lain" class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarUgd.anamnesia.faktorResiko.lainLain'))"
                    :disabled=$disabledPropertyRjStatus
                    wire:model.debounce.500ms="dataDaftarUgd.anamnesia.faktorResiko.lainLain" />

            </div>
        </div>

        <div class="pt-2">
            <x-input-label for="dataDaftarUgd.anamnesia.penyakitKeluarga.penyakitKeluarga" :value="__('Penyakit Keluarga')"
                :required="__(false)" />

            <div class="grid grid-cols-5 gap-2 pt-2">
                <x-check-box value='1' :label="__('hipertensi')"
                    wire:model.debounce.500ms="dataDaftarUgd.anamnesia.penyakitKeluarga.hipertensi" />
                <x-check-box value='1' :label="__('diabetesMelitus')"
                    wire:model.debounce.500ms="dataDaftarUgd.anamnesia.penyakitKeluarga.diabetesMelitus" />
                <x-check-box value='1' :label="__('penyakitJantung')"
                    wire:model.debounce.500ms="dataDaftarUgd.anamnesia.penyakitKeluarga.penyakitJantung" />
                <x-check-box value='1' :label="__('asma')"
                    wire:model.debounce.500ms="dataDaftarUgd.anamnesia.penyakitKeluarga.asma" />
            </div>
            <div class="mb-2 ">
                <x-text-input id="dataDaftarUgd.anamnesia.penyakitKeluarga.lainLain"
                    placeholder="Jenis Faktor Resiko Lain-Lain" class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarUgd.anamnesia.penyakitKeluarga.lainLain'))"
                    :disabled=$disabledPropertyRjStatus
                    wire:model.debounce.500ms="dataDaftarUgd.anamnesia.penyakitKeluarga.lainLain" />

            </div>
        </div> --}}



    </div>
</div>
