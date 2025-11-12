<div>
    <div class="w-full mb-1">
        {{-- Keluhan Utama --}}
        <div class="mb-4">
            <x-input-label for="dataDaftarUgd.anamnesa.keluhanUtama.keluhanUtama" :value="__('Keluhan Utama')" :required="__(true)"
                class="pt-2 text-lg font-semibold" />
            <div class="mt-1">
                <x-text-input-area id="dataDaftarUgd.anamnesa.keluhanUtama.keluhanUtama"
                    placeholder="Masukkan keluhan utama pasien" class="w-full" :errorshas="__($errors->has('dataDaftarUgd.anamnesa.keluhanUtama.keluhanUtama'))"
                    :disabled=$disabledPropertyRjStatus :rows=4
                    wire:model.debounce.500ms="dataDaftarUgd.anamnesa.keluhanUtama.keluhanUtama" />
            </div>
            @error('dataDaftarUgd.anamnesa.keluhanUtama.keluhanUtama')
                <x-input-error :messages=$message class="mt-1" />
            @enderror
        </div>

        {{-- Tingkat Kegawatan --}}
        <div class="mb-4">
            <x-input-label for="dataDaftarUgd.anamnesa.pengkajianPerawatan.tingkatKegawatan" :value="__('Tingkat Kegawatan')"
                :required="__(true)" class="text-lg font-semibold" />
            <div class="mt-2">
                <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
                    @foreach ($dataDaftarUgd['anamnesa']['pengkajianPerawatan']['tingkatKegawatanOption'] ?? [['tingkatKegawatan' => 'P1'], ['tingkatKegawatan' => 'P2'], ['tingkatKegawatan' => 'P3'], ['tingkatKegawatan' => 'P0']] as $tingkatKegawatanOption)
                        <x-radio-button :label="__($tingkatKegawatanOption['tingkatKegawatan'])" value="{{ $tingkatKegawatanOption['tingkatKegawatan'] }}"
                            wire:model="dataDaftarUgd.anamnesa.pengkajianPerawatan.tingkatKegawatan" />
                    @endforeach
                </div>
            </div>
            @error('dataDaftarUgd.anamnesa.pengkajianPerawatan.tingkatKegawatan')
                <x-input-error :messages=$message class="mt-1" />
            @enderror
        </div>

        {{-- Waktu Datang --}}
        <div class="mb-4">
            <x-input-label for="dataDaftarUgd.anamnesa.pengkajianPerawatan.jamDatang" :value="__('Waktu Datang')"
                :required="__(true)" class="text-lg font-semibold" />
            <div class="flex flex-col gap-3 mt-1 md:flex-row">
                <div class="flex-1">
                    <x-text-input id="dataDaftarUgd.anamnesa.pengkajianPerawatan.jamDatang"
                        placeholder="Waktu Datang [dd/mm/yyyy HH:MM:SS]" class="w-full" :errorshas="__($errors->has('dataDaftarUgd.anamnesa.pengkajianPerawatan.jamDatang'))"
                        :disabled=$disabledPropertyRjStatus
                        wire:model.debounce.500ms="dataDaftarUgd.anamnesa.pengkajianPerawatan.jamDatang" />
                </div>
                @empty($dataDaftarUgd['anamnesa']['pengkajianPerawatan']['jamDatang'])
                    <div class="md:w-1/3">
                        <div wire:loading wire:target="setAutoJamDatang">
                            <x-loading />
                        </div>
                        <x-primary-button :disabled=$disabledPropertyRjStatus wire:click.prevent="setAutoJamDatang()"
                            type="button" wire:loading.remove class="justify-center w-full">
                            <i class="fas fa-clock me-2"></i>Waktu Sekarang
                        </x-primary-button>
                    </div>
                @endempty
            </div>
            @error('dataDaftarUgd.anamnesa.pengkajianPerawatan.jamDatang')
                <x-input-error :messages=$message class="mt-1" />
            @enderror
        </div>

        {{-- Cara Masuk IGD --}}
        <div class="mb-4">
            <x-input-label for="dataDaftarUgd.anamnesa.pengkajianPerawatan.caraMasukIgd" :value="__('Cara Masuk IGD')"
                :required="__(true)" class="text-lg font-semibold" />
            <div class="mt-2">
                <div class="grid grid-cols-1 gap-3 mb-3 md:grid-cols-3">
                    @foreach ($dataDaftarUgd['anamnesa']['pengkajianPerawatan']['caraMasukIgdOption'] ?? [['caraMasukIgd' => 'Sendiri'], ['caraMasukIgd' => 'Rujuk'], ['caraMasukIgd' => 'Kasus Polisi']] as $caraMasukIgdOption)
                        <x-radio-button :label="__($caraMasukIgdOption['caraMasukIgd'])" value="{{ $caraMasukIgdOption['caraMasukIgd'] }}"
                            wire:model="dataDaftarUgd.anamnesa.pengkajianPerawatan.caraMasukIgd" />
                    @endforeach
                </div>
                <x-text-input id="dataDaftarUgd.anamnesa.pengkajianPerawatan.caraMasukIgdDesc"
                    placeholder="Keterangan Cara Masuk IGD" class="w-full" :errorshas="__($errors->has('dataDaftarUgd.anamnesa.pengkajianPerawatan.caraMasukIgdDesc'))"
                    :disabled=$disabledPropertyRjStatus
                    wire:model.debounce.500ms="dataDaftarUgd.anamnesa.pengkajianPerawatan.caraMasukIgdDesc" />
            </div>
            @error('dataDaftarUgd.anamnesa.pengkajianPerawatan.caraMasukIgd')
                <x-input-error :messages=$message class="mt-1" />
            @enderror
        </div>

        {{-- Sarana Transportasi --}}
        <div class="mb-4">
            <x-input-label for="dataDaftarUgd.anamnesa.pengkajianPerawatan.saranaTransportasiId" :value="__('Sarana Transportasi Kedatangan')"
                :required="__(true)" class="text-lg font-semibold" />
            <div class="mt-2">
                <div class="grid grid-cols-2 gap-3 mb-3 md:grid-cols-4">
                    @foreach ($dataDaftarUgd['anamnesa']['pengkajianPerawatan']['saranaTransportasiOptions'] ?? [['saranaTransportasiId' => '1', 'saranaTransportasiDesc' => 'Ambulans'], ['saranaTransportasiId' => '2', 'saranaTransportasiDesc' => 'Mobil'], ['saranaTransportasiId' => '3', 'saranaTransportasiDesc' => 'Motor'], ['saranaTransportasiId' => '4', 'saranaTransportasiDesc' => 'Lain-lain']] as $saranaTransportasi)
                        <x-radio-button :label="__($saranaTransportasi['saranaTransportasiDesc'])" value="{{ $saranaTransportasi['saranaTransportasiId'] }}"
                            wire:model="dataDaftarUgd.anamnesa.pengkajianPerawatan.saranaTransportasiId"
                            wire:click="$set('dataDaftarUgd.anamnesa.pengkajianPerawatan.saranaTransportasiDesc','{{ $saranaTransportasi['saranaTransportasiDesc'] }}')" />
                    @endforeach
                </div>
                <x-text-input id="dataDaftarUgd.anamnesa.pengkajianPerawatan.saranaTransportasiKet"
                    placeholder="Keterangan Sarana Transportasi" class="w-full" :errorshas="__($errors->has('dataDaftarUgd.anamnesa.pengkajianPerawatan.saranaTransportasiKet'))"
                    :disabled=$disabledPropertyRjStatus
                    wire:model.debounce.500ms="dataDaftarUgd.anamnesa.pengkajianPerawatan.saranaTransportasiKet" />
            </div>
            @error('dataDaftarUgd.anamnesa.pengkajianPerawatan.saranaTransportasiId')
                <x-input-error :messages=$message class="mt-1" />
            @enderror
        </div>

        {{-- Perawat Penerima --}}
        <div class="mb-4">
            <x-input-label for="dataDaftarUgd.anamnesa.pengkajianPerawatan.perawatPenerima" :value="__('Perawat Penerima')"
                :required="__(true)" class="text-lg font-semibold" />
            <div class="flex flex-col gap-3 mt-1 md:flex-row">
                <div class="flex-1">
                    <x-text-input id="dataDaftarUgd.anamnesa.pengkajianPerawatan.perawatPenerima"
                        placeholder="Perawat Penerima" class="w-full" :errorshas="__($errors->has('dataDaftarUgd.anamnesa.pengkajianPerawatan.perawatPenerima'))" :disabled=true
                        wire:model.debounce.500ms="dataDaftarUgd.anamnesa.pengkajianPerawatan.perawatPenerima" />
                </div>
                <div class="md:w-1/3">
                    <x-primary-button :disabled=$disabledPropertyRjStatus wire:click.prevent="setPerawatPenerima()"
                        type="button" class="justify-center w-full">
                        <i class="fas fa-signature me-2"></i>TTD Perawat
                    </x-primary-button>
                </div>
            </div>
            @error('dataDaftarUgd.anamnesa.pengkajianPerawatan.perawatPenerima')
                <x-input-error :messages=$message class="mt-1" />
            @enderror
        </div>
    </div>
</div>
