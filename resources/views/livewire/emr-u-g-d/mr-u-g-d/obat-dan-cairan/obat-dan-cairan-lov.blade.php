<div>
    @if (!$collectingMyProduct)
        <div class="" x-data="{ selecteddataProductLovIndex: @entangle('selecteddataProductLovIndex') }" @click.outside="$wire.dataProductLovSearch = ''">
            <div class="">
                {{-- Lov dataProductLov --}}
                <div>
                    <x-text-input id="dataProductLovSearchMain" placeholder="Nama Obat Atau Jenis Cairan" class="mt-1 ml-2"
                        :errorshas="__($errors->has('dataProductLovSearchMain'))" :disabled=$disabledPropertyRjStatus wire:model="dataProductLovSearch"
                        x-on:click.outside="$wire.resetdataProductLov()" x-on:keyup.escape="$wire.resetdataProductLov()"
                        x-on:keyup.down="$wire.selectNextdataProductLov()"
                        x-on:keyup.up="$wire.selectPreviousdataProductLov()"
                        x-on:keyup.enter="$wire.enterMydataProductLov(selecteddataProductLovIndex)"
                        x-ref="dataProductLovSearchMain" x-init="$watch('selecteddataProductLovIndex', (value, oldValue) => $refs.dataProductLovSearch.children[selecteddataProductLovIndex + 1].scrollIntoView({
                            block: 'nearest'
                        }))
                        $refs.dataProductLovSearchMain.focus()" />



                    {{-- Lov --}}
                    <div class="py-2 mt-1 overflow-y-auto bg-white border rounded-md shadow-lg max-h-64"
                        x-show="$wire.dataProductLovSearch.length>3 && $wire.dataProductLov.length>0" x-transition
                        x-ref="dataProductLovSearch">

                        {{-- livewire --}}
                        @foreach ($dataProductLov as $key => $lov)
                            <li wire:key='dataProductLov{{ $lov['product_name'] }}'>
                                <x-dropdown-link wire:click="setMydataProductLov('{{ $key }}')"
                                    class="text-base font-normal {{ $key === $selecteddataProductLovIndex ? 'bg-gray-100 outline-none' : '' }}">
                                    <div>
                                        {{ $lov['product_name'] . ' / ' . number_format($lov['sales_price']) }}
                                    </div>
                                    <div class="text-xs">
                                        {{ '(' . $lov['product_content'] . ')' }}
                                    </div>
                                </x-dropdown-link>
                            </li>
                        @endforeach

                    </div>


                    {{-- Start Lov exceptions --}}

                    @if (strlen($dataProductLovSearch) > 0 && strlen($dataProductLovSearch) < 3 && count($dataProductLov) == 0)
                        <div class="w-full p-2 text-sm text-center text-gray-500 dark:text-gray-400">
                            {{ 'Masukkan minimal 3 karakter' }}
                        </div>
                    @elseif(strlen($dataProductLovSearch) >= 3 && count($dataProductLov) == 0)
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

    @if ($collectingMyProduct)
        {{-- collectingMyProduct / obat --}}
        <div class="inline-flex space-x-0.5" x-data>

            <div class="basis-3/6">
                <x-input-label for="collectingMyProduct.productName" :value="__('Nama Obat Atau Jenis Cairan')" :required="__(true)" />

                <div>
                    <x-text-input id="collectingMyProduct.productName" placeholder="Nama Obat Atau Jenis Cairan"
                        class="mt-1 ml-2" :errorshas="__($errors->has('collectingMyProduct.productName'))" :disabled=true
                        wire:model="collectingMyProduct.productName" />

                    @error('collectingMyProduct.productName')
                        <x-input-error :messages=$message />
                    @enderror
                </div>
            </div>

        </div>
        {{-- collectingMyProduct / obat --}}
    @endif
</div>
