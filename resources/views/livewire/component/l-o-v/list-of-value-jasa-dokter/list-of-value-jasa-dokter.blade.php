@if (!$collectingMyJasaDokter)
    <div>
        <x-input-label for="dataJasaDokterLovSearch" :value="__('Cari JasaDokter')" :required="__(true)" />

        {{-- Lov dataJasaDokterLov --}}
        <div x-data="{ selecteddataJasaDokterLovIndex: @entangle('selecteddataJasaDokterLovIndex') }" @click.outside="$wire.dataJasaDokterLovSearch = ''">
            <x-text-input id="dataJasaDokterLovSearch" placeholder="Cari JasaDokter" class="mt-1 ml-2" :errorshas="__($errors->has('dataJasaDokterLovSearch'))"
                :disabled=false wire:model="dataJasaDokterLovSearch" x-on:click.outside="$wire.resetdataJasaDokterLov()"
                x-on:keyup.escape="$wire.resetdataJasaDokterLov()" x-on:keyup.down="$wire.selectNextdataJasaDokterLov()"
                x-on:keyup.up="$wire.selectPreviousdataJasaDokterLov()"
                x-on:keyup.enter="$wire.enterMydataJasaDokterLov(selecteddataJasaDokterLovIndex)"
                x-ref="dataJasaDokterLovSearchfocus" x-init="$watch('selecteddataJasaDokterLovIndex', (value, oldValue) => $refs.dataJasaDokterLovSearch.children[selecteddataJasaDokterLovIndex + 1].scrollIntoView({
                    block: 'nearest'
                }))" />

            {{-- Lov --}}
            <div class="py-2 mt-1 overflow-y-auto bg-white border rounded-md shadow-lg max-h-64"
                x-show="$wire.dataJasaDokterLovSearch.length>1 && $wire.dataJasaDokterLov.length>0" x-transition
                x-ref="dataJasaDokterLovSearch">


                {{-- livewire --}}
                @foreach ($dataJasaDokterLov as $key => $lov)
                    <li wire:key='dataJasaDokterLov{{ $lov['accdoc_id'] }}'>
                        <x-dropdown-link wire:click="setMydataJasaDokterLov('{{ $key }}')"
                            class="text-base font-normal {{ $key === $selecteddataJasaDokterLovIndex ? 'bg-gray-100 outline-none' : '' }}">
                            <div>
                                {{ $lov['accdoc_id'] . '/ ' . $lov['accdoc_desc'] }}
                            </div>
                            <div>
                                {{ number_format($lov['accdoc_price']) }}
                            </div>
                        </x-dropdown-link>
                    </li>
                @endforeach

            </div>


            {{-- Start Lov exceptions --}}

            @if (strlen($dataJasaDokterLovSearch) > 0 && strlen($dataJasaDokterLovSearch) < 1 && count($dataJasaDokterLov) == 0)
                <div class="w-full p-2 text-sm text-center text-gray-500 dark:text-gray-400">
                    {{ 'Masukkan minimal lebih dari 1  karakter' }}
                </div>
            @elseif(strlen($dataJasaDokterLovSearch) >= 1 && count($dataJasaDokterLov) == 0)
                <div class="w-full p-2 text-sm text-center text-gray-500 dark:text-gray-400">
                    {{ 'Data Tidak ditemukan' }}
                </div>
            @endif
            {{-- End Lov exceptions --}}

            @error('dataJasaDokterLovSearch')
                <x-input-error :messages=$message />
            @enderror
        </div>
        {{-- Lov dataJasaDokterLov --}}
    </div>
@endif
