<div>
    <div class="w-full mb-1">


        <div class="pt-2">
            <x-input-label for="dataDaftarRi.anamnesa.edukasi.edukasi" :value="__('Pasien dan Keluarga')" :required="__(false)"
                class="pt-2 sm:text-xl" />

            <div class="pt-2 ">

                <div class="grid grid-cols-4 gap-2 mt-2 ml-2">
                    <x-input-label for="" :value="__('Kesediaan Pasien / Keluarga Menerima Informasi')" :required="__(false)" class="px-2" />

                    @foreach ($dataDaftarRi['anamnesa']['edukasi']['pasienKeluargaMenerimaInformasiOptions'] as $edukasiOption)
                        {{-- @dd($sRj) --}}
                        <x-radio-button :label="__($edukasiOption['pasienKeluargaMenerimaInformasi'])" value="{{ $edukasiOption['pasienKeluargaMenerimaInformasi'] }}"
                            wire:model="dataDaftarRi.anamnesa.edukasi.pasienKeluargaMenerimaInformasi" />
                    @endforeach

                </div>

                <div class="grid grid-cols-4 gap-2 mt-2 ml-2">
                    <x-input-label for="" :value="__('Terdapat Hambatan Terhadap Edukasi')" :required="__(false)" class="px-2" />

                    @foreach ($dataDaftarRi['anamnesa']['edukasi']['hambatanEdukasiOptions'] as $edukasiOption)
                        {{-- @dd($sRj) --}}
                        <x-radio-button :label="__($edukasiOption['hambatanEdukasi'])" value="{{ $edukasiOption['hambatanEdukasi'] }}"
                            wire:model="dataDaftarRi.anamnesa.edukasi.hambatanEdukasi" />
                    @endforeach

                    <x-text-input id="dataDaftarRi.anamnesa.edukasi.keteranganHambatanEdukasi"
                        placeholder="Keterangan Hambatan Terhadap Edukasi" class="mt-1 ml-2 " :errorshas="__($errors->has('dataDaftarRi.anamnesa.edukasi.keteranganHambatanEdukasi'))"
                        :disabled=$disabledPropertyRjStatus
                        wire:model.debounce.500ms="dataDaftarRi.anamnesa.edukasi.keteranganHambatanEdukasi" />
                </div>

                <div class="grid grid-cols-4 gap-2 mt-2 ml-2">
                    <x-input-label for="" :value="__('Dibutuhkan Penerjemah')" :required="__(false)" class="px-2" />

                    @foreach ($dataDaftarRi['anamnesa']['edukasi']['penerjemahOptions'] as $edukasiOption)
                        {{-- @dd($sRj) --}}
                        <x-radio-button :label="__($edukasiOption['penerjemah'])" value="{{ $edukasiOption['penerjemah'] }}"
                            wire:model="dataDaftarRi.anamnesa.edukasi.penerjemah" />
                    @endforeach

                    <x-text-input id="dataDaftarRi.anamnesa.edukasi.keteranganPenerjemah"
                        placeholder="Keterangan Penerjemah" class="mt-1 ml-2 " :errorshas="__($errors->has('dataDaftarRi.anamnesa.edukasi.keteranganPenerjemah'))"
                        :disabled=$disabledPropertyRjStatus
                        wire:model.debounce.500ms="dataDaftarRi.anamnesa.edukasi.keteranganPenerjemah" />
                </div>

            </div>

        </div>


        <div class="pt-2">
            <x-input-label for="" :value="__('Kebutuhan Edukasi')" :required="__(false)" class="pt-2 sm:text-xl" />

            <div class="grid grid-cols-4 gap-2 pt-2">
                <x-check-box value='1' :label="__('diagPenyakit')"
                    wire:model.debounce.500ms="dataDaftarRi.anamnesa.edukasi.diagPenyakit" />

                <x-check-box value='1' :label="__('obat')"
                    wire:model.debounce.500ms="dataDaftarRi.anamnesa.edukasi.obat" />

                <x-check-box value='1' :label="__('dietNutrisi')"
                    wire:model.debounce.500ms="dataDaftarRi.anamnesa.edukasi.dietNutrisi" />

                <x-check-box value='1' :label="__('rehabMedik')"
                    wire:model.debounce.500ms="dataDaftarRi.anamnesa.edukasi.rehabMedik" />

                <x-check-box value='1' :label="__('managemenNyeri')"
                    wire:model.debounce.500ms="dataDaftarRi.anamnesa.edukasi.managemenNyeri" />

                <x-check-box value='1' :label="__('penggunaanAlatMedis')"
                    wire:model.debounce.500ms="dataDaftarRi.anamnesa.edukasi.penggunaanAlatMedis" />

                <x-check-box value='1' :label="__('hakKewajibanPasien')"
                    wire:model.debounce.500ms="dataDaftarRi.anamnesa.edukasi.hakKewajibanPasien" />

            </div>

        </div>


        <x-input-label for="" :value="__('Emergensi')" :required="__(false)" class="pt-2 sm:text-xl" />

        <div class="grid grid-cols-2 gap-2 pt-2">

            <div class="pt-2 ml-2">
                <x-input-label for="dataDaftarRi.anamnesa.edukasi.edukasiFollowUp" :value="__('Edukasi Followup')"
                    :required="__(false)" />

                <div class="mb-2">
                    <x-text-input-area id="dataDaftarRi.anamnesa.edukasi.edukasiFollowUp" placeholder="Edukasi Followup"
                        class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarRi.anamnesa.edukasi.edukasiFollowUp'))" :disabled=$disabledPropertyRjStatus :rows=7
                        wire:model.debounce.500ms="dataDaftarRi.anamnesa.edukasi.edukasiFollowUp" />

                </div>
                @error('dataDaftarRi.anamnesa.edukasi.edukasiFollowUp')
                    <x-input-error :messages=$message />
                @enderror
            </div>

            <div class="pt-2 ml-2">
                <x-input-label for="dataDaftarRi.anamnesa.edukasi.segeraKembaliIGDjika" :value="__('Segera Kembali ke Gawat Darurat Jika')"
                    :required="__(false)" />

                <div class="mb-2 ">
                    <x-text-input-area id="dataDaftarRi.anamnesa.edukasi.edukasi"
                        placeholder="Segera Kembali ke Gawat Darurat Jika" class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarRi.anamnesa.edukasi.segeraKembaliIGDjika'))"
                        :disabled=$disabledPropertyRjStatus :rows=7
                        wire:model.debounce.500ms="dataDaftarRi.anamnesa.edukasi.segeraKembaliIGDjika" />

                </div>
                @error('dataDaftarRi.anamnesa.edukasi.segeraKembaliIGDjika')
                    <x-input-error :messages=$message />
                @enderror
            </div>

        </div>





    </div>
</div>
