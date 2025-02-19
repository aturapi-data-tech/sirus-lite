<div>

    @php
        $disabledProperty = true;
        $disabledPropertyRjStatus = false;
    @endphp

    <div class="w-full mb-1">
        <div id="TransaksiRoom">
            <div class="grid grid-cols-9 gap-2" x-data>
                <!-- Tanggal Mulai Room -->
                <div class="col-span-2">
                    <x-input-label for="formEntryRoom.roomStartDate" :value="__('Tanggal Mulai Room')" :required="__(true)" />
                    <div>
                        <div class="flex items-center mb-2">
                            @if (!$formEntryRoom['roomStartDate'])
                                <div class="w-full mt-2 ml-2">
                                    <div wire:loading wire:target="setRoomStartDate">
                                        <x-loading />
                                    </div>
                                    <x-yellow-button :disabled="false"
                                        wire:click.prevent="setRoomStartDate('{{ date('d/m/Y H:i:s') }}')" type="button"
                                        wire:loading.remove class="w-full">
                                        <div wire:poll.20s>
                                            {{ date('d/m/Y H:i:s') }}
                                        </div>
                                    </x-yellow-button>
                                </div>
                            @else
                                <x-text-input id="formEntryRoom.roomStartDate" type="text"
                                    placeholder="dd/mm/yyyy hh24:mi:ss" class="mt-1 ml-2" :errorshas="__($errors->has('formEntryRoom.roomStartDate'))"
                                    wire:model="formEntryRoom.roomStartDate" :disabled="$disabledPropertyRjStatus" />
                            @endif
                        </div>
                        @error('formEntryRoom.roomStartDate')
                            <x-input-error :messages="$message" />
                        @enderror
                    </div>
                </div>

                <!-- LOV Room -->
                <div class="col-span-1">
                    @if (empty($collectingMyRoom))
                        <div class="">
                            {{-- Komponen LOV Room, sesuaikan path-nya --}}
                            @include('livewire.component.l-o-v.list-of-value-room.list-of-value-room')
                        </div>
                    @else
                        <x-input-label for="formEntryRoom.roomName" :value="__('Nama Room')" :required="__(true)" />
                        <div>
                            <x-text-input id="formEntryRoom.roomName" placeholder="Nama Room" class="mt-1 ml-2"
                                :errorshas="__($errors->has('formEntryRoom.roomId'))" wire:model="formEntryRoom.roomName" :disabled="true" />
                        </div>
                    @endif
                    @error('formEntryRoom.roomId')
                        <x-input-error :messages="$message" />
                    @enderror
                </div>

                <!-- Nomor Bed -->
                <div class="col-span-1">
                    <x-input-label for="formEntryRoom.roomBedNo" :value="__('Bed No')" :required="__(true)" />
                    <div>
                        <x-text-input id="formEntryRoom.roomBedNo" placeholder="Bed No" class="mt-1 ml-2"
                            :errorshas="__($errors->has('formEntryRoom.roomBedNo'))" wire:model="formEntryRoom.roomBedNo" :disabled="$disabledPropertyRjStatus" />
                        @error('formEntryRoom.roomBedNo')
                            <x-input-error :messages="$message" />
                        @enderror
                    </div>
                </div>

                <!-- Tarif Room -->
                <div class="col-span-1">
                    <x-input-label for="formEntryRoom.roomPrice" :value="__('Tarif Room')" :required="__(true)" />
                    <div>
                        <x-text-input id="formEntryRoom.roomPrice" placeholder="Tarif Room" class="mt-1 ml-2"
                            :errorshas="__($errors->has('formEntryRoom.roomPrice'))" wire:model="formEntryRoom.roomPrice" :disabled="$disabledPropertyRjStatus"
                            x-on:keyup.enter="$wire.insertRoom()" />
                        @error('formEntryRoom.roomPrice')
                            <x-input-error :messages="$message" />
                        @enderror
                    </div>
                </div>

                <!-- Tarif Perawatan -->
                <div class="col-span-1">
                    <x-input-label for="formEntryRoom.perawatanPrice" :value="__('Tarif Perawatan')" :required="__(true)" />
                    <div>
                        <x-text-input id="formEntryRoom.perawatanPrice" placeholder="Tarif Perawatan" class="mt-1 ml-2"
                            :errorshas="__($errors->has('formEntryRoom.perawatanPrice'))" wire:model="formEntryRoom.perawatanPrice" :disabled="$disabledPropertyRjStatus"
                            x-on:keyup.enter="$wire.insertRoom()" />
                        @error('formEntryRoom.perawatanPrice')
                            <x-input-error :messages="$message" />
                        @enderror
                    </div>
                </div>

                <!-- Tarif Common Service -->
                <div class="col-span-1">
                    <x-input-label for="formEntryRoom.commonService" :value="__('Tarif Common')" :required="__(true)" />
                    <div>
                        <x-text-input id="formEntryRoom.commonService" placeholder="Tarif Common" class="mt-1 ml-2"
                            :errorshas="__($errors->has('formEntryRoom.commonService'))" wire:model="formEntryRoom.commonService" :disabled="$disabledPropertyRjStatus"
                            x-on:keyup.enter="$wire.insertRoom()" />
                        @error('formEntryRoom.commonService')
                            <x-input-error :messages="$message" />
                        @enderror
                    </div>
                </div>

                <!-- Jumlah Hari -->
                <div class="col-span-1">
                    <x-input-label for="formEntryRoom.roomDay" :value="__('Jumlah Hari')" :required="__(true)" />
                    <div>
                        <x-text-input id="formEntryRoom.roomDay" placeholder="Jumlah Hari" class="mt-1 ml-2"
                            :errorshas="__($errors->has('formEntryRoom.roomDay'))" wire:model="formEntryRoom.roomDay" :disabled="$disabledPropertyRjStatus" />
                        @error('formEntryRoom.roomDay')
                            <x-input-error :messages="$message" />
                        @enderror
                    </div>
                </div>

                <!-- Tombol Hapus Entry -->
                <div class="col-span-1">
                    <x-input-label for="" :value="__('Hapus')" :required="__(true)" />
                    <x-alternative-button class="inline-flex ml-2" wire:click.prevent="resetformEntryRoom()">
                        <svg class="w-5 h-5 text-gray-800 dark:text-white" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                            <path
                                d="M17 4h-4V2a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v2H1a1 1 0 0 0 0 2h1v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6h1a1 1 0 1 0 0-2ZM7 2h4v2H7V2Zm1 14a1 1 0 1 1-2 0V8a1 1 0 0 1 2 0v8Zm4 0a1 1 0 0 1-2 0V8a1 1 0 0 1 2 0v8Z" />
                        </svg>
                    </x-alternative-button>
                </div>
            </div>

            <!-- Tombol Simpan Room -->
            <div class="w-full">
                <div wire:loading wire:target="insertRoom">
                    <x-loading />
                </div>
                <x-primary-button :disabled="false" wire:click.prevent="insertRoom()" type="button"
                    wire:loading.remove class="w-full">
                    Simpan Room
                </x-primary-button>
            </div>
        </div>
    </div>

    <div class="flex flex-col my-2">
        <div class="overflow-x-auto rounded-lg">
            <div class="inline-block min-w-full align-middle">
                <div class="overflow-hidden shadow sm:rounded-lg">
                    <table class="w-full text-sm text-left text-gray-500 table-auto dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-4 py-3">
                                    <x-sort-link :active="false" wire:click.prevent="" role="button" href="#">
                                        Tgl
                                    </x-sort-link>
                                </th>
                                <th scope="col" class="px-4 py-3">
                                    <x-sort-link :active="false" wire:click.prevent="" role="button" href="#">
                                        Room
                                    </x-sort-link>
                                </th>
                                <th scope="col" class="px-4 py-3">
                                    <x-sort-link :active="false" wire:click.prevent="" role="button" href="#">
                                        Tarif
                                    </x-sort-link>
                                </th>
                                <th scope="col" class="w-8 px-4 py-3 text-center">
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800">
                            @isset($dataRoom['riRoom'])
                                @foreach ($dataRoom['riRoom'] as $key => $Room)
                                    <tr class="border-b group dark:border-gray-700">
                                        <td
                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                            {{ $Room['start_date'] }}<br>
                                            {{ $Room['end_date'] }}
                                        </td>
                                        <td
                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                            {{ $Room['room_name'] }}/Bed :
                                            {{ $Room['bed_no'] }}

                                        </td>
                                        <td
                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                            Kamar: {{ number_format($Room['room_price']) }} x
                                            {{ number_format($Room['day']) }} =
                                            {{ number_format($Room['room_price'] * $Room['day']) }}
                                            <br>
                                            Perawatan: {{ number_format($Room['perawatan_price']) }} x
                                            {{ number_format($Room['day']) }} =
                                            {{ number_format($Room['perawatan_price'] * $Room['day']) }}
                                            <br>
                                            Umum: {{ number_format($Room['common_service']) }} x
                                            {{ number_format($Room['day']) }} =
                                            {{ number_format($Room['common_service'] * $Room['day']) }}
                                        </td>
                                        <td
                                            class="px-4 py-3 font-normal text-center text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                            <x-alternative-button class="inline-flex"
                                                wire:click.prevent="removeRoom('{{ $Room['trfr_no'] }}')">
                                                <svg class="w-5 h-5 text-gray-800 dark:text-white" aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                    viewBox="0 0 18 20">
                                                    <path
                                                        d="M17 4h-4V2a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v2H1a1 1 0 0 0 0 2h1v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6h1a1 1 0 1 0 0-2ZM7 2h4v2H7V2Zm1 14a1 1 0 1 1-2 0V8a1 1 0 0 1 2 0v8Zm4 0a1 1 0 0 1-2 0V8a1 1 0 0 1 2 0v8Z" />
                                                </svg>
                                            </x-alternative-button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endisset
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
