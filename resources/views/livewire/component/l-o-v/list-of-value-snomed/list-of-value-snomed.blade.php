@if (!empty($dataSnomedLovSearch))

    <div>
        <x-input-label for="dataSnomedLovSearch" :value="__('Cari Kode Snomed')" :required="__(true)" />

        {{-- Lov dataSnomedLov --}}
        <div x-data="{ selecteddataSnomedLovIndex: @entangle('selecteddataSnomedLovIndex') }" @click.outside="$wire.dataSnomedLovSearch = ''">
            <x-text-input id="dataSnomedLovSearch" placeholder="Cari Snomed" class="mt-1 ml-2" :errorshas="__($errors->has('dataSnomedLovSearch'))"
                :disabled=false wire:model.debounce.500ms="dataSnomedLovSearch"
                x-on:click.outside="$wire.resetdataSnomedLov()" x-on:keyup.escape="$wire.resetdataSnomedLov()"
                x-on:keyup.down="$wire.selectNextdataSnomedLov()" x-on:keyup.up="$wire.selectPreviousdataSnomedLov()"
                x-on:keyup.enter="$wire.enterMydataSnomedLov(selecteddataSnomedLovIndex)"
                x-ref="dataSnomedLovSearchfocus" x-init="$watch('selecteddataSnomedLovIndex', (value, oldValue) => $refs.dataSnomedLovSearch.children[selecteddataSnomedLovIndex + 1].scrollIntoView({
                    block: 'nearest'
                }))
                $refs.dataSnomedLovSearchfocus.focus()" />

            {{-- Lov --}}
            <div class="py-2 mt-1 overflow-y-auto bg-white border rounded-md shadow-lg max-h-64"
                x-show="$wire.dataSnomedLovSearch.length>1 && $wire.dataSnomedLov.length>0" x-transition
                x-ref="dataSnomedLovSearch">


                {{-- livewire --}}
                @foreach ($dataSnomedLov ?? [] as $key => $lov)
                    <li wire:key='dataSnomedLov{{ $lov['code'] }}'>
                        <x-dropdown-link wire:click="setMydataSnomedLov('{{ $key }}')"
                            class="text-base font-normal {{ $key === $selecteddataSnomedLovIndex ? 'bg-gray-100 outline-none' : '' }}">
                            <div>
                                {{ $lov['code'] . '/ ' . $lov['display'] }}
                            </div>
                        </x-dropdown-link>
                    </li>
                @endforeach

            </div>


            {{-- Start Lov exceptions --}}

            @if (strlen($dataSnomedLovSearch) > 0 && strlen($dataSnomedLovSearch) < 3 && count($dataSnomedLov) == 0)
                <div class="w-full p-2 text-sm text-center text-gray-500 dark:text-gray-400">
                    {{ 'Masukkan minimal lebih dari 3  karakter' }}
                </div>
            @elseif(strlen($dataSnomedLovSearch) >= 3 && count($dataSnomedLov) == [])
                <div class="w-full p-2 text-sm text-center text-gray-500 dark:text-gray-400">
                    {{ 'Data Tidak ditemukan' }}
                </div>
            @endif
            {{-- End Lov exceptions --}}

            @error('dataSnomedLovSearch')
                <x-input-error :messages=$message />
            @enderror
        </div>
        {{-- Lov dataSnomedLov --}}
    </div>
@endif
