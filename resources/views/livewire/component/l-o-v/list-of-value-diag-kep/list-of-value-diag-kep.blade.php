@if (!$collectingMyDiagKep)
    <div>
        <x-input-label for="dataDiagKepLovSearch" :value="__('Cari DiagKep')" :required="__(true)" />

        {{-- Lov dataDiagKepLov --}}
        <div x-data="{ selecteddataDiagKepLovIndex: @entangle('selecteddataDiagKepLovIndex') }" @click.outside="$wire.dataDiagKepLovSearch = ''">
            <x-text-input id="dataDiagKepLovSearch" placeholder="Cari DiagKep" class="mt-1 ml-2" :errorshas="__($errors->has('dataDiagKepLovSearch'))"
                :disabled=false wire:model="dataDiagKepLovSearch" x-on:click.outside="$wire.resetdataDiagKepLov()"
                x-on:keyup.escape="$wire.resetdataDiagKepLov()" x-on:keyup.down="$wire.selectNextdataDiagKepLov()"
                x-on:keyup.up="$wire.selectPreviousdataDiagKepLov()"
                x-on:keyup.enter="$wire.enterMydataDiagKepLov(selecteddataDiagKepLovIndex)"
                x-ref="dataDiagKepLovSearchfocus" x-init="$watch('selecteddataDiagKepLovIndex', (value, oldValue) => $refs.dataDiagKepLovSearch.children[selecteddataDiagKepLovIndex + 1].scrollIntoView({
                    block: 'nearest'
                }))" />

            {{-- Lov --}}
            <div class="py-2 mt-1 overflow-y-auto bg-white border rounded-md shadow-lg max-h-64"
                x-show="$wire.dataDiagKepLovSearch.length>1 && $wire.dataDiagKepLov.length>0" x-transition
                x-ref="dataDiagKepLovSearch">


                {{-- livewire --}}
                @foreach ($dataDiagKepLov as $key => $lov)
                    <li wire:key='dataDiagKepLov{{ $lov['diagkep_id'] }}'>
                        <x-dropdown-link wire:click="setMydataDiagKepLov('{{ $key }}')"
                            class="text-base font-normal {{ $key === $selecteddataDiagKepLovIndex ? 'bg-gray-100 outline-none' : '' }}">
                            <div>
                                {{ $lov['diagkep_id'] . '/ ' . $lov['diagkep_desc'] }}
                            </div>
                            <div>
                                </span>
                                :{{ $lov['diagkep_json'] }}
                                <span>
                            </div>
                        </x-dropdown-link>
                    </li>
                @endforeach

            </div>


            {{-- Start Lov exceptions --}}

            @if (strlen($dataDiagKepLovSearch) > 0 && strlen($dataDiagKepLovSearch) < 1 && count($dataDiagKepLov) == 0)
                <div class="w-full p-2 text-sm text-center text-gray-500 dark:text-gray-400">
                    {{ 'Masukkan minimal lebih dari 1  karakter' }}
                </div>
            @elseif(strlen($dataDiagKepLovSearch) >= 1 && count($dataDiagKepLov) == 0)
                <div class="w-full p-2 text-sm text-center text-gray-500 dark:text-gray-400">
                    {{ 'Data Tidak ditemukan' }}
                </div>
            @endif
            {{-- End Lov exceptions --}}

            @error('dataDiagKepLovSearch')
                <x-input-error :messages=$message />
            @enderror
        </div>
        {{-- Lov dataDiagKepLov --}}
    </div>
@endif
