<div>

    @php
        // Sesuaikan properti disable sesuai dengan logika aplikasi Anda
        $disabledProperty = true;
        $disabledPropertyOtherStatus = false;
    @endphp

    <!-- Form Entry Lain -->
    <div class="w-full mb-1">
        <div id="TransaksiLain">
            <div class="grid grid-cols-10 gap-2" x-data>
                <!-- Tanggal Lain -->
                <div class="col-span-2">
                    <x-input-label for="formEntryLain.lainDate" :value="__('Tanggal Lain')" :required="__(true)" />
                    <div>
                        <div class="flex items-center mb-2">
                            @if (!$formEntryLain['lainDate'])
                                <div class="w-full mt-2 ml-2">
                                    <div wire:loading wire:target="setLainDate">
                                        <x-loading />
                                    </div>
                                    <x-yellow-button :disabled="false"
                                        wire:click.prevent="setLainDate('{{ date('d/m/Y H:i:s') }}')" type="button"
                                        wire:loading.remove class="w-full">
                                        <div wire:poll.20s>
                                            {{ date('d/m/Y H:i:s') }}
                                        </div>
                                    </x-yellow-button>
                                </div>
                            @else
                                <x-text-input id="formEntryLain.lainDate" type="text"
                                    placeholder="dd/mm/yyyy hh24:mi:ss" class="mt-1 ml-2" :errorshas="__($errors->has('formEntryLain.lainDate'))"
                                    wire:model="formEntryLain.lainDate" :disabled="$disabledPropertyOtherStatus" />
                            @endif
                        </div>
                        @error('formEntryLain.lainDate')
                            <x-input-error :messages="$message" />
                        @enderror
                    </div>
                </div>

                <!-- Pilih Dokumen Lain (LOV) -->
                <div class="col-span-2">
                    @if (empty($collectingMyLain))
                        <div class="">
                            {{-- Komponen LOV untuk data Lain, sesuaikan path-nya --}}
                            @include('livewire.component.l-o-v.list-of-value-lain.list-of-value-lain')
                        </div>
                    @else
                        <x-input-label for="formEntryLain.lainDesc" :value="__('Dokumen Lain')" :required="__(true)" />
                        <div>
                            <x-text-input id="formEntryLain.lainDesc" placeholder="Dokumen Lain" class="mt-1 ml-2"
                                :errorshas="__($errors->has('formEntryLain.lainDesc'))" wire:model="formEntryLain.lainDesc" :disabled="true" />
                        </div>
                    @endif
                    @error('formEntryLain.lainId')
                        <x-input-error :messages="$message" />
                    @enderror
                </div>

                <!-- Tarif Lain -->
                <div class="col-span-2">
                    <x-input-label for="formEntryLain.lainPrice" :value="__('Tarif')" :required="__(true)" />
                    <div>
                        <x-text-input id="formEntryLain.lainPrice" placeholder="Tarif" class="mt-1 ml-2"
                            :errorshas="__($errors->has('formEntryLain.lainPrice'))" wire:model="formEntryLain.lainPrice" :disabled="$disabledPropertyOtherStatus"
                            x-on:keyup.enter="$wire.insertLain()" />
                        @error('formEntryLain.lainPrice')
                            <x-input-error :messages="$message" />
                        @enderror
                    </div>
                </div>

                <!-- Total (sama dengan Tarif jika tidak ada qty) -->
                <div class="col-span-2">
                    <x-input-label for="formEntryLain.lainTotal" :value="__('Total')" :required="__(true)" />
                    <div>
                        <x-text-input id="formEntryLain.lainTotal" placeholder="Total" class="mt-1 ml-2"
                            :errorshas="__($errors->has('formEntryLain.lainTotal'))" value="{{ number_format($formEntryLain['lainPrice']) }}"
                            :disabled="true" />
                    </div>
                </div>

                <!-- Tombol Hapus Entry -->
                <div class="col-span-2">
                    <x-input-label for="" :value="__('Hapus')" :required="__(true)" />
                    <x-alternative-button class="inline-flex ml-2" wire:click.prevent="resetformEntryLain()">
                        <svg class="w-5 h-5 text-gray-800 dark:text-white" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                            <path
                                d="M17 4h-4V2a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v2H1a1 1 0 0 0 0 2h1v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6h1a1 1 0 1 0 0-2ZM7 2h4v2H7V2Zm1 14a1 1 0 1 1-2 0V8a1 1 0 0 1 2 0v8Zm4 0a1 1 0 0 1-2 0V8a1 1 0 0 1 2 0v8Z" />
                        </svg>
                    </x-alternative-button>
                </div>
            </div>

            <!-- Tombol Simpan -->
            <div class="w-full">
                <div wire:loading wire:target="insertLain">
                    <x-loading />
                </div>
                <x-primary-button :disabled="false" wire:click.prevent="insertLain()" type="button"
                    wire:loading.remove class="w-full">
                    Simpan Lain
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
                                <th scope="col" class="px-4 py-3 ">
                                    <x-sort-link :active="false" wire:click.prevent="" role="button" href="#">
                                        Tgl
                                    </x-sort-link>
                                </th>
                                <th scope="col" class="px-4 py-3 ">
                                    <x-sort-link :active="false" wire:click.prevent="" role="button" href="#">
                                        Lain-Lain
                                    </x-sort-link>
                                </th>
                                <th scope="col" class="px-4 py-3 ">
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
                            @php
                                use Carbon\Carbon;

                                $sortedRiLain = collect($dataLain['riLain'] ?? [])
                                    ->sortByDesc(function ($item) {
                                        $date = $item['other_date'] ?? '';

                                        // Jika kosong, kembalikan 0 agar muncul paling bawah
                                        if (!$date) {
                                            return 0;
                                        }

                                        try {
                                            return Carbon::createFromFormat(
                                                'd/m/Y H:i:s',
                                                $date,
                                                env('APP_TIMEZONE'),
                                            )->timestamp;
                                        } catch (\Exception $e) {
                                            // Jika parsing gagal atau format tidak sesuai, juga kembalikan 0
                                            return 0;
                                        }
                                    })
                                    ->values();
                            @endphp

                            @if ($sortedRiLain->isNotEmpty())
                                @foreach ($sortedRiLain as $key => $Lain)
                                    <tr class="border-b group dark:border-gray-700">
                                        <td
                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                            {{ $Lain['other_date'] }}
                                        </td>
                                        <td
                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                            {{ $Lain['other_desc'] }}
                                        </td>
                                        <td
                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                            {{ number_format($Lain['other_price']) }}
                                        </td>
                                        <td
                                            class="px-4 py-3 font-normal text-center text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                            <x-alternative-button class="inline-flex"
                                                wire:click.prevent="removeLain('{{ $Lain['other_no'] }}')">
                                                <svg class="w-5 h-5 text-gray-800 dark:text-white" aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                    viewBox="0 0 18 20">
                                                    <path
                                                        d="M17 4h-4V2a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v2H1a1 1 0 0 0 0 2h1v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6h1a1 1 0 1 0 0-2ZM7 2h4v2H7V2Zm1 14a1 1 0 1 1-2 0V8a1 1 0 0 1 2 0v8Zm4 0a1 1 0 0 1-2 0V8a1 1 0 0 1 2 0v8Z" />
                                                </svg>
                                                {{ 'Hapus ' . $Lain['other_no'] }}
                                            </x-alternative-button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


</div>
