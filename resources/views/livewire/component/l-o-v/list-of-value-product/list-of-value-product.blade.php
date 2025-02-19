@if (!$collectingMyProduct)
    <div class="">
        <x-input-label for="dataProductLovSearch" :value="__('Cari Product')" :required="__(true)" />
        <div class="relative">
            {{-- Lov dataProductLov --}}
            <div x-data="{ selecteddataProductLovIndex: @entangle('selecteddataProductLovIndex') }" @click.outside="$wire.dataProductLovSearch = ''" class="relative">
                <x-text-input id="dataProductLovSearch" placeholder="Cari Product" class="mt-1 ml-2" :errorshas="__($errors->has('dataProductLovSearch'))"
                    :disabled=false wire:model="dataProductLovSearch" x-on:click.outside="$wire.resetdataProductLov()"
                    x-on:keyup.escape="$wire.resetdataProductLov()" x-on:keyup.down="$wire.selectNextdataProductLov()"
                    x-on:keyup.up="$wire.selectPreviousdataProductLov()"
                    x-on:keyup.enter="$wire.enterMydataProductLov(selecteddataProductLovIndex)"
                    x-ref="dataProductLovSearchfocus" x-init="$watch('selecteddataProductLovIndex', (value, oldValue) => $refs.dataProductLovSearch.children[selecteddataProductLovIndex + 1].scrollIntoView({
                        block: 'nearest'
                    }))" />

                {{-- Lov --}}
                <div class="absolute z-50 py-2 mt-1 overflow-y-auto bg-white border rounded-md shadow-lg max-h-64"
                    x-show="$wire.dataProductLovSearch.length>1 && $wire.dataProductLov.length>0" x-transition
                    x-ref="dataProductLovSearch">


                    {{-- livewire --}}
                    @foreach ($dataProductLov as $key => $lov)
                        <li wire:key='dataProductLov{{ $lov['product_id'] }}'>
                            <x-dropdown-link wire:click="setMydataProductLov('{{ $key }}')"
                                class="text-base font-normal {{ $key === $selecteddataProductLovIndex ? 'bg-gray-100 outline-none' : '' }}">
                                <div>
                                    {{ $lov['product_id'] . '/ ' . $lov['product_name'] }}
                                </div>
                                <div>
                                    {{ number_format($lov['sales_price']) }}
                                </div>
                            </x-dropdown-link>
                        </li>
                    @endforeach

                </div>


                {{-- Start Lov exceptions --}}

                @if (strlen($dataProductLovSearch) > 0 && strlen($dataProductLovSearch) < 1 && count($dataProductLov) == 0)
                    <div class="w-full p-2 text-sm text-center text-gray-500 dark:text-gray-400">
                        {{ 'Masukkan minimal lebih dari 1  karakter' }}
                    </div>
                @elseif(strlen($dataProductLovSearch) >= 1 && count($dataProductLov) == 0)
                    <div class="w-full p-2 text-sm text-center text-gray-500 dark:text-gray-400">
                        {{ 'Data Tidak ditemukan' }}
                    </div>
                @endif
                {{-- End Lov exceptions --}}

                @error('dataProductLovSearch')
                    <x-input-error :messages=$message />
                @enderror
            </div>
            {{-- Lov dataProductLov --}}
        </div>
    </div>
@endif
