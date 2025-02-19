@if (!$collectingMyLain)
    <div class="">
        <x-input-label for="dataLainLovSearch" :value="__('Cari Lain')" :required="__(true)" />
        <div class="relative">
            {{-- Lov dataLainLov --}}
            <div x-data="{ selecteddataLainLovIndex: @entangle('selecteddataLainLovIndex') }" @click.outside="$wire.dataLainLovSearch = ''" class="relative">
                <x-text-input id="dataLainLovSearch" placeholder="Cari Lain" class="mt-1 ml-2" :errorshas="__($errors->has('dataLainLovSearch'))"
                    :disabled=false wire:model="dataLainLovSearch" x-on:click.outside="$wire.resetdataLainLov()"
                    x-on:keyup.escape="$wire.resetdataLainLov()" x-on:keyup.down="$wire.selectNextdataLainLov()"
                    x-on:keyup.up="$wire.selectPreviousdataLainLov()"
                    x-on:keyup.enter="$wire.enterMydataLainLov(selecteddataLainLovIndex)" x-ref="dataLainLovSearchfocus"
                    x-init="$watch('selecteddataLainLovIndex', (value, oldValue) => $refs.dataLainLovSearch.children[selecteddataLainLovIndex + 1].scrollIntoView({
                        block: 'nearest'
                    }))" />

                {{-- Lov --}}
                <div class="absolute z-50 py-2 mt-1 overflow-y-auto bg-white border rounded-md shadow-lg max-h-64"
                    x-show="$wire.dataLainLovSearch.length>1 && $wire.dataLainLov.length>0" x-transition
                    x-ref="dataLainLovSearch">


                    {{-- livewire --}}
                    @foreach ($dataLainLov as $key => $lov)
                        <li wire:key='dataLainLov{{ $lov['other_id'] }}'>
                            <x-dropdown-link wire:click="setMydataLainLov('{{ $key }}')"
                                class="text-base font-normal {{ $key === $selecteddataLainLovIndex ? 'bg-gray-100 outline-none' : '' }}">
                                <div>
                                    {{ $lov['other_id'] . '/ ' . $lov['other_desc'] }}
                                </div>
                                <div>
                                    {{ number_format($lov['other_price']) }}
                                </div>
                            </x-dropdown-link>
                        </li>
                    @endforeach

                </div>


                {{-- Start Lov exceptions --}}

                @if (strlen($dataLainLovSearch) > 0 && strlen($dataLainLovSearch) < 1 && count($dataLainLov) == 0)
                    <div class="w-full p-2 text-sm text-center text-gray-500 dark:text-gray-400">
                        {{ 'Masukkan minimal lebih dari 1  karakter' }}
                    </div>
                @elseif(strlen($dataLainLovSearch) >= 1 && count($dataLainLov) == 0)
                    <div class="w-full p-2 text-sm text-center text-gray-500 dark:text-gray-400">
                        {{ 'Data Tidak ditemukan' }}
                    </div>
                @endif
                {{-- End Lov exceptions --}}

                @error('dataLainLovSearch')
                    <x-input-error :messages=$message />
                @enderror
            </div>
            {{-- Lov dataLainLov --}}
        </div>
    </div>
@endif
