<div>
    <div class="w-full mb-1">


        @include('livewire.emr-r-i.mr-r-i.anamnesa.keluhanUtamaTab')

        <div class="mb-2">
            <x-input-label for="dataDaftarRi.anamnesa.pengkajianPerawatan.tingkatKegawatan" :value="__('Tingkat Kegawatan')"
                :required="__(true)" class="pt-2 sm:text-xl" />

            <div class="mb-2 ">

                <div class="grid grid-cols-4 gap-2 mt-2 ml-2">
                    @foreach ($dataDaftarRi['anamnesa']['pengkajianPerawatan']['tingkatKegawatanOption'] as $tingkatKegawatanOption)
                        {{-- @dd($sRj) --}}
                        <x-radio-button :label="__($tingkatKegawatanOption['tingkatKegawatan'])" value="{{ $tingkatKegawatanOption['tingkatKegawatan'] }}"
                            wire:model="dataDaftarRi.anamnesa.pengkajianPerawatan.tingkatKegawatan" />
                    @endforeach
                </div>
            </div>

            @error('dataDaftarRi.anamnesa.pengkajianPerawatan.tingkatKegawatan')
                <x-input-error :messages=$message />
            @enderror

        </div>

        <div>
            <x-input-label for="dataDaftarRi.anamnesa.pengkajianPerawatan.jamDatang" :value="__('Waktu Datang')"
                :required="__(true)" />

            <div class="mb-2 ">
                <div class="flex items-center mb-2 ">
                    <x-text-input id="dataDaftarRi.anamnesa.pengkajianPerawatan.jamDatang"
                        placeholder="Waktu Datang [dd/mm/yyyy hh24:mi:ss]" class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarRi.anamnesa.pengkajianPerawatan.jamDatang'))"
                        :disabled=$disabledPropertyRjStatus
                        wire:model.debounce.500ms="dataDaftarRi.anamnesa.pengkajianPerawatan.jamDatang" />

                    @if (!$dataDaftarRi['anamnesa']['pengkajianPerawatan']['jamDatang'])
                        <div class="w-1/2 ml-2">
                            <div wire:loading wire:target="setJamDatang">
                                <x-loading />
                            </div>

                            <x-green-button :disabled=false
                                wire:click.prevent="setJamDatang('{{ date('d/m/Y H:i:s') }}')" type="button"
                                wire:loading.remove>
                                <div wire:poll>

                                    Set Jam Datang: {{ date('d/m/Y H:i:s') }}

                                </div>
                            </x-green-button>
                        </div>
                    @endif
                </div>

            </div>
            @error('dataDaftarRi.anamnesa.pengkajianPerawatan.jamDatang')
                <x-input-error :messages=$message />
            @enderror
        </div>

        <div class="mb-2">
            <x-input-label for="dataDaftarRi.anamnesa.pengkajianPerawatan.caraMasukIgd" :value="__('Cara Masuk IGD')"
                :required="__(true)" class="pt-2 sm:text-xl" />

            <div class="mb-2 ">

                <div class="grid grid-cols-5 gap-2 mt-2 ml-2">
                    @foreach ($dataDaftarRi['anamnesa']['pengkajianPerawatan']['caraMasukIgdOption'] as $caraMasukIgdOption)
                        {{-- @dd($sRj) --}}
                        <x-radio-button :label="__($caraMasukIgdOption['caraMasukIgd'])" value="{{ $caraMasukIgdOption['caraMasukIgd'] }}"
                            wire:model="dataDaftarRi.anamnesa.pengkajianPerawatan.caraMasukIgd" />
                    @endforeach



                    <x-text-input id="dataDaftarRi.anamnesa.pengkajianPerawatan.caraMasukIgdDesc"
                        placeholder="Keterangan Cara Masuk IGD" class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarRi.anamnesa.pengkajianPerawatan.caraMasukIgdDesc'))"
                        :disabled=$disabledPropertyRjStatus
                        wire:model.debounce.500ms="dataDaftarRi.anamnesa.pengkajianPerawatan.caraMasukIgdDesc" />


                </div>
                @error('dataDaftarRi.anamnesa.pengkajianPerawatan.caraMasukIgd')
                    <x-input-error :messages=$message />
                @enderror
            </div>

        </div>

        <div class="mb-2">
            <x-input-label for="dataDaftarRi.anamnesa.pengkajianPerawatan.saranaTransportasiId" :value="__('Sarana Transportasi Kedatangan ')"
                :required="__(true)" />
            <div class="grid grid-cols-5 gap-2 mt-2 ml-2">
                @foreach ($dataDaftarRi['anamnesa']['pengkajianPerawatan']['saranaTransportasiOptions'] ?? [] as $saranaTransportasi)
                    {{-- @dd($saranaTransportasi) --}}
                    <x-radio-button :label="__($saranaTransportasi['saranaTransportasiDesc'])" value="{{ $saranaTransportasi['saranaTransportasiId'] }}"
                        wire:model="dataDaftarRi.anamnesa.pengkajianPerawatan.saranaTransportasiId"
                        wire:click="$set('dataDaftarRi.anamnesa.pengkajianPerawatan.saranaTransportasiDesc','{{ $saranaTransportasi['saranaTransportasiDesc'] }}')" />
                @endforeach

                <x-text-input id="dataDaftarRi.anamnesa.pengkajianPerawatan.saranaTransportasiKet"
                    placeholder="Keterangan Sarana Transportasi" class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarRi.anamnesa.pengkajianPerawatan.saranaTransportasiKet'))"
                    :disabled=$disabledPropertyRjStatus
                    wire:model.debounce.500ms="dataDaftarRi.anamnesa.pengkajianPerawatan.saranaTransportasiKet" />
            </div>
            @error('saranaTransportasi.saranaTransportasiId')
                <x-input-error :messages=$message />
            @enderror
        </div>

        @include('livewire.emr-r-i.mr-r-i.anamnesa.statusPsikologisTab')



        <div>
            <x-input-label for="dataDaftarRi.anamnesa.pengkajianPerawatan.perawatPenerima" :value="__('Perawat Penerima')"
                :required="__(true)" class="pt-2 sm:text-xl" />

            <div class="mb-2 ">
                <div class="grid gap-2 mb-2">
                    <x-text-input id="dataDaftarRi.anamnesa.pengkajianPerawatan.perawatPenerima"
                        name="dataDaftarRi.anamnesa.pengkajianPerawatan.perawatPenerima" placeholder="Perawat Penerima"
                        class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarRi.anamnesa.pengkajianPerawatan.perawatPenerima'))" :disabled=true
                        wire:model.debounce.500ms="dataDaftarRi.anamnesa.pengkajianPerawatan.perawatPenerima"
                        autocomplete="dataDaftarRi.anamnesa.pengkajianPerawatan.perawatPenerima" />

                    <x-yellow-button :disabled=false wire:click.prevent="setPerawatPenerima()" type="button"
                        wire:loading.remove>
                        ttd Perawat
                    </x-yellow-button>

                </div>

            </div>
            @error('dataDaftarRi.anamnesa.pengkajianPerawatan.perawatPenerima')
                <x-input-error :messages=$message />
            @enderror
        </div>


    </div>
</div>
