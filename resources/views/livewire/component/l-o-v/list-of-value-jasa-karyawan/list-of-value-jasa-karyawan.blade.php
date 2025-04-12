@if (!$collectingMyJasaKaryawan)
    <div>
        <x-input-label for="dataJasaKaryawanLovSearch" :value="__('Cari JasaKaryawan')" :required="__(true)" />

        {{-- Lov dataJasaKaryawanLov --}}
        <div x-data="{ selecteddataJasaKaryawanLovIndex: @entangle('selecteddataJasaKaryawanLovIndex') }" @click.outside="$wire.dataJasaKaryawanLovSearch = ''">
            <x-text-input id="dataJasaKaryawanLovSearch" placeholder="Cari JasaKaryawan" class="mt-1 ml-2"
                :errorshas="__($errors->has('dataJasaKaryawanLovSearch'))" :disabled=false wire:model="dataJasaKaryawanLovSearch"
                x-on:click.outside="$wire.resetdataJasaKaryawanLov()" x-on:keyup.escape="$wire.resetdataJasaKaryawanLov()"
                x-on:keyup.down="$wire.selectNextdataJasaKaryawanLov()"
                x-on:keyup.up="$wire.selectPreviousdataJasaKaryawanLov()"
                x-on:keyup.enter="$wire.enterMydataJasaKaryawanLov(selecteddataJasaKaryawanLovIndex)"
                x-ref="dataJasaKaryawanLovSearchfocus" x-init="$watch('selecteddataJasaKaryawanLovIndex', (value, oldValue) => $refs.dataJasaKaryawanLovSearch.children[selecteddataJasaKaryawanLovIndex + 1].scrollIntoView({
                    block: 'nearest'
                }))" />

            {{-- Lov --}}
            <div class="py-2 mt-1 overflow-y-auto bg-white border rounded-md shadow-lg max-h-64"
                x-show="$wire.dataJasaKaryawanLovSearch.length>1 && $wire.dataJasaKaryawanLov.length>0" x-transition
                x-ref="dataJasaKaryawanLovSearch">


                {{-- livewire --}}
                @foreach ($dataJasaKaryawanLov as $key => $lov)
                    <li wire:key='dataJasaKaryawanLov{{ $lov['acte_id'] }}'>
                        <x-dropdown-link wire:click="setMydataJasaKaryawanLov('{{ $key }}')"
                            class="text-base font-normal {{ $key === $selecteddataJasaKaryawanLovIndex ? 'bg-gray-100 outline-none' : '' }}">
                            <div>
                                {{ $lov['acte_id'] . '/ ' . $lov['acte_desc'] }}
                            </div>
                            <div>
                                {{ number_format($lov['acte_price']) }}
                            </div>
                        </x-dropdown-link>
                    </li>
                @endforeach

            </div>


            {{-- Start Lov exceptions --}}

            @if (strlen($dataJasaKaryawanLovSearch) > 0 &&
                    strlen($dataJasaKaryawanLovSearch) < 1 &&
                    count($dataJasaKaryawanLov) == 0)
                <div class="w-full p-2 text-sm text-center text-gray-500 dark:text-gray-400">
                    {{ 'Masukkan minimal lebih dari 1  karakter' }}
                </div>
            @elseif(strlen($dataJasaKaryawanLovSearch) >= 1 && count($dataJasaKaryawanLov) == 0)
                <div class="w-full p-2 text-sm text-center text-gray-500 dark:text-gray-400">
                    {{ 'Data Tidak ditemukan' }}
                </div>
            @endif
            {{-- End Lov exceptions --}}

            @error('dataJasaKaryawanLovSearch')
                <x-input-error :messages=$message />
            @enderror
        </div>
        {{-- Lov dataJasaKaryawanLov --}}
    </div>
@endif
