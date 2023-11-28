<div>
    <div class="w-full mb-1">

        <div>
            <x-input-label for="dataDaftarUgd.anamnesia.pengkajianPerawatan.perawatPenerima" :value="__('Perawat Penerima')"
                :required="__(true)" class="pt-2 sm:text-xl" />

            <div class="mb-2 ">
                <div class="mb-2 ">
                    <x-text-input id="dataDaftarUgd.anamnesa.PengkajianPerawatan.perawatPenerima"
                        placeholder="Perawat Penerima" class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarUgd.anamnesa.PengkajianPerawatan.perawatPenerima'))"
                        :disabled=$disabledPropertyRjStatus
                        wire:model.debounce.500ms="dataDaftarUgd.anamnesa.PengkajianPerawatan.perawatPenerima" />

                </div>

            </div>
            @error('dataDaftarUgd.anamnesia.pengkajianPerawatan.perawatPenerima')
                <x-input-error :messages=$message />
            @enderror
        </div>

        <div>
            <x-input-label for="dataDaftarUgd.anamnesia.pengkajianPerawatan.jamDatang" :value="__('Jam Datang')"
                :required="__(true)" />

            <div class="mb-2 ">
                <div class="mb-2 ">
                    <x-text-input id="dataDaftarUgd.anamnesa.PengkajianPerawatan.jamDatang"
                        placeholder="Jam Datang [hh24:mi]" class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarUgd.anamnesa.PengkajianPerawatan.jamDatang'))"
                        :disabled=$disabledPropertyRjStatus
                        wire:model.debounce.500ms="dataDaftarUgd.anamnesa.PengkajianPerawatan.jamDatang" />

                </div>

            </div>
            @error('dataDaftarUgd.anamnesia.pengkajianPerawatan.jamDatang')
                <x-input-error :messages=$message />
            @enderror
        </div>

        <div class="mb-2">
            <x-input-label for="dataDaftarUgd.anamnesa.pengkajianPerawatan.caraMasukIgd" :value="__('Cara Masuk IGD')"
                :required="__(true)" class="pt-2 sm:text-xl" />

            <div class="mb-2 ">

                <div class="grid grid-cols-4 gap-2 mt-2 ml-2">
                    @foreach ($dataDaftarUgd['anamnesa']['pengkajianPerawatan']['caraMasukIgdOption'] as $caraMasukIgdOption)
                        {{-- @dd($sRj) --}}
                        <x-radio-button :label="__($caraMasukIgdOption['caraMasukIgd'])" value="{{ $caraMasukIgdOption['caraMasukIgd'] }}"
                            wire:model="dataDaftarUgd.anamnesa.pengkajianPerawatan.caraMasukIgd" />
                    @endforeach


                    <x-text-input id="dataDaftarUgd.anamnesa.pengkajianPerawatan.caraMasukIgdDesc"
                        placeholder="Keterangan Cara Masuk IGD" class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarUgd.anamnesa.pengkajianPerawatan.caraMasukIgdDesc'))"
                        :disabled=$disabledPropertyRjStatus
                        wire:model.debounce.500ms="dataDaftarUgd.anamnesa.pengkajianPerawatan.caraMasukIgdDesc" />


                </div>
            </div>

        </div>

        <div class="mb-2">
            <x-input-label for="dataDaftarUgd.anamnesa.pengkajianPerawatan.tingkatKegawatan" :value="__('Tingkat Kegawatan')"
                :required="__(true)" class="pt-2 sm:text-xl" />

            <div class="mb-2 ">

                <div class="grid grid-cols-4 gap-2 mt-2 ml-2">
                    @foreach ($dataDaftarUgd['anamnesa']['pengkajianPerawatan']['tingkatKegawatanOption'] as $tingkatKegawatanOption)
                        {{-- @dd($sRj) --}}
                        <x-radio-button :label="__($tingkatKegawatanOption['tingkatKegawatan'])" value="{{ $tingkatKegawatanOption['tingkatKegawatan'] }}"
                            wire:model="dataDaftarUgd.anamnesa.pengkajianPerawatan.tingkatKegawatan" />
                    @endforeach
                </div>
            </div>

        </div>


    </div>
</div>
