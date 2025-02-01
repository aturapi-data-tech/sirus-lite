@if (!$collectingMyJasaMedis)
    <div>
        <x-input-label for="dataJasaMedisLovSearch" :value="__('Cari JasaMedis')" :required="__(true)" />

        {{-- Lov dataJasaMedisLov --}}
        <div x-data="{ selecteddataJasaMedisLovIndex: @entangle('selecteddataJasaMedisLovIndex') }" @click.outside="$wire.dataJasaMedisLovSearch = ''">
            <x-text-input id="dataJasaMedisLovSearch" placeholder="Cari JasaMedis" class="mt-1 ml-2" :errorshas="__($errors->has('dataJasaMedisLovSearch'))"
                :disabled=false wire:model="dataJasaMedisLovSearch" x-on:click.outside="$wire.resetdataJasaMedisLov()"
                x-on:keyup.escape="$wire.resetdataJasaMedisLov()" x-on:keyup.down="$wire.selectNextdataJasaMedisLov()"
                x-on:keyup.up="$wire.selectPreviousdataJasaMedisLov()"
                x-on:keyup.enter="$wire.enterMydataJasaMedisLov(selecteddataJasaMedisLovIndex)"
                x-ref="dataJasaMedisLovSearchfocus" x-init="$watch('selecteddataJasaMedisLovIndex', (value, oldValue) => $refs.dataJasaMedisLovSearch.children[selecteddataJasaMedisLovIndex + 1].scrollIntoView({
                    block: 'nearest'
                }))" />

            {{-- Lov --}}
            <div class="py-2 mt-1 overflow-y-auto bg-white border rounded-md shadow-lg max-h-64"
                x-show="$wire.dataJasaMedisLovSearch.length>1 && $wire.dataJasaMedisLov.length>0" x-transition
                x-ref="dataJasaMedisLovSearch">


                {{-- livewire --}}
                @foreach ($dataJasaMedisLov as $key => $lov)
                    <li wire:key='dataJasaMedisLov{{ $lov['pact_id'] }}'>
                        <x-dropdown-link wire:click="setMydataJasaMedisLov('{{ $key }}')"
                            class="text-base font-normal {{ $key === $selecteddataJasaMedisLovIndex ? 'bg-gray-100 outline-none' : '' }}">
                            <div>
                                {{ $lov['pact_id'] . '/ ' . $lov['pact_desc'] }}
                            </div>
                            <div>
                                {{ number_format($lov['pact_price']) }}
                            </div>
                        </x-dropdown-link>
                    </li>
                @endforeach

            </div>


            {{-- Start Lov exceptions --}}

            @if (strlen($dataJasaMedisLovSearch) > 0 && strlen($dataJasaMedisLovSearch) < 1 && count($dataJasaMedisLov) == 0)
                <div class="w-full p-2 text-sm text-center text-gray-500 dark:text-gray-400">
                    {{ 'Masukkan minimal lebih dari 1  karakter' }}
                </div>
            @elseif(strlen($dataJasaMedisLovSearch) >= 1 && count($dataJasaMedisLov) == 0)
                <div class="w-full p-2 text-sm text-center text-gray-500 dark:text-gray-400">
                    {{ 'Data Tidak ditemukan' }}
                </div>
            @endif
            {{-- End Lov exceptions --}}

            @error('dataJasaMedisLovSearch')
                <x-input-error :messages=$message />
            @enderror
        </div>
        {{-- Lov dataJasaMedisLov --}}
    </div>
@endif
