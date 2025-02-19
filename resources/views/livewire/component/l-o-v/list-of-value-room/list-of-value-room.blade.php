@if (!$collectingMyRoom)
    <div>
        <x-input-label for="dataRoomLovSearch" :value="__('Cari Room')" :required="__(true)" />

        {{-- Lov dataRoomLov --}}
        <div x-data="{ selecteddataRoomLovIndex: @entangle('selecteddataRoomLovIndex') }" @click.outside="$wire.dataRoomLovSearch = ''">
            <x-text-input id="dataRoomLovSearch" placeholder="Cari Room" class="mt-1 ml-2" :errorshas="__($errors->has('dataRoomLovSearch'))"
                :disabled=false wire:model="dataRoomLovSearch" x-on:click.outside="$wire.resetdataRoomLov()"
                x-on:keyup.escape="$wire.resetdataRoomLov()" x-on:keyup.down="$wire.selectNextdataRoomLov()"
                x-on:keyup.up="$wire.selectPreviousdataRoomLov()"
                x-on:keyup.enter="$wire.enterMydataRoomLov(selecteddataRoomLovIndex)" x-ref="dataRoomLovSearchfocus"
                x-init="$watch('selecteddataRoomLovIndex', (value, oldValue) => $refs.dataRoomLovSearch.children[selecteddataRoomLovIndex + 1].scrollIntoView({
                    block: 'nearest'
                }))" />

            {{-- Lov --}}
            <div class="py-2 mt-1 overflow-y-auto bg-white border rounded-md shadow-lg max-h-64"
                x-show="$wire.dataRoomLovSearch.length>1 && $wire.dataRoomLov.length>0" x-transition
                x-ref="dataRoomLovSearch">


                {{-- livewire --}}
                @foreach ($dataRoomLov as $key => $lov)
                    <li wire:key='dataRoomLov{{ $lov['room_id'] }}'>
                        <x-dropdown-link wire:click="setMydataRoomLov('{{ $key }}')"
                            class="text-base font-normal {{ $key === $selecteddataRoomLovIndex ? 'bg-gray-100 outline-none' : '' }}">
                            <div>
                                {{ $lov['room_id'] . '/ ' . $lov['room_name'] }}
                            </div>
                            <div>
                                </span>
                                Bed :{{ $lov['bed_no'] }}
                                <span>
                            </div>
                        </x-dropdown-link>
                    </li>
                @endforeach

            </div>


            {{-- Start Lov exceptions --}}

            @if (strlen($dataRoomLovSearch) > 0 && strlen($dataRoomLovSearch) < 1 && count($dataRoomLov) == 0)
                <div class="w-full p-2 text-sm text-center text-gray-500 dark:text-gray-400">
                    {{ 'Masukkan minimal lebih dari 1  karakter' }}
                </div>
            @elseif(strlen($dataRoomLovSearch) >= 1 && count($dataRoomLov) == 0)
                <div class="w-full p-2 text-sm text-center text-gray-500 dark:text-gray-400">
                    {{ 'Data Tidak ditemukan' }}
                </div>
            @endif
            {{-- End Lov exceptions --}}

            @error('dataRoomLovSearch')
                <x-input-error :messages=$message />
            @enderror
        </div>
        {{-- Lov dataRoomLov --}}
    </div>
@endif
