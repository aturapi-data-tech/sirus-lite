<div>

    @php
        $disabledProperty = true;
        $disabledPropertyRjStatus = false;
    @endphp

    <div class="w-full mb-1">
        <div id="TransaksiRawatInap">
            <div class="grid grid-cols-10 gap-2" x-data>
                <div class="col-span-2">
                    <x-input-label for="formEntryJasaMedis.jasaMedisDate" :value="__('Tanggal Kunjungan')" :required="__(true)" />
                    <div>
                        <div class="flex items-center mb-2">
                            @if (!$formEntryJasaMedis['jasaMedisDate'])
                                <div class="w-full mt-2 ml-2">
                                    <div wire:loading wire:target="setJasaMedisDate">
                                        <x-loading />
                                    </div>

                                    <x-yellow-button :disabled="false"
                                        wire:click.prevent="setJasaMedisDate('{{ date('d/m/Y H:i:s') }}')" type="button"
                                        wire:loading.remove class="w-full">
                                        <div wire:poll.20s>
                                            {{ date('d/m/Y H:i:s') }}
                                        </div>
                                    </x-yellow-button>
                                </div>
                            @else
                                <x-text-input id="formEntryJasaMedis.jasaMedisDate" type="text"
                                    placeholder="dd/mm/yyyy hh24:mi:ss" class="mt-1 ml-2" :errorshas="__($errors->has('formEntryJasaMedis.jasaMedisDate'))"
                                    wire:model="formEntryJasaMedis.jasaMedisDate" :disabled="$disabledPropertyRjStatus" />
                            @endif
                        </div>
                        @error('formEntryJasaMedis.jasaMedisDate')
                            <x-input-error :messages=$message />
                        @enderror
                    </div>
                </div>

                <div class="col-span-2">
                    {{-- LOV JasaMedis) --}}
                    @if (empty($collectingMyJasaMedis))
                        <div class="">
                            @include('livewire.component.l-o-v.list-of-value-jasa-medis.list-of-value-jasa-medis')
                        </div>
                    @else
                        <x-input-label for="formEntryJasaMedis.jasaMedisDesc" :value="__('Nama JasaMedis')" :required="__(true)" />
                        <div>
                            <x-text-input id="formEntryJasaMedis.jasaMedisDesc" placeholder="Nama JasaMedis"
                                class="mt-1 ml-2" :errorshas="__($errors->has('formEntryJasaMedis.jasaMedisDesc'))" wire:model="formEntryJasaMedis.jasaMedisDesc"
                                :disabled="true" />

                        </div>
                    @endif
                    @error('formEntryJasaMedis.jasaMedisId')
                        <x-input-error :messages=$message />
                    @enderror
                </div>

                <div class="col-span-1">
                    <x-input-label for="formEntryJasaMedis.jasaMedisQty" :value="__('Jml')" :required="__(true)" />
                    <div>
                        <x-text-input id="formEntryJasaMedis.jasaMedisQty" placeholder="Jml" class="mt-1 ml-2"
                            :errorshas="__($errors->has('formEntryJasaMedis.jasaMedisQty'))" wire:model="formEntryJasaMedis.jasaMedisQty" :disabled="$disabledPropertyRjStatus" />
                        @error('formEntryJasaMedis.jasaMedisQty')
                            <x-input-error :messages=$message />
                        @enderror
                    </div>
                </div>

                <div class="col-span-2">
                    <x-input-label for="formEntryJasaMedis.jasaMedisPrice" :value="__('Tarif')" :required="__(true)" />
                    <div>
                        <x-text-input id="formEntryJasaMedis.jasaMedisPrice" placeholder="Tarif" class="mt-1 ml-2"
                            :errorshas="__($errors->has('formEntryJasaMedis.jasaMedisPrice'))" wire:model="formEntryJasaMedis.jasaMedisPrice" :disabled="$disabledPropertyRjStatus"
                            x-on:keyup.enter="$wire.insertJasaMedis()" />
                        @error('formEntryJasaMedis.jasaMedisPrice')
                            <x-input-error :messages=$message />
                        @enderror
                    </div>
                </div>

                <div class="col-span-2">
                    <x-input-label for="formEntryJasaMedis.jasaMedisTotal" :value="__('Total')" :required="__(true)" />
                    <div>
                        <x-text-input id="formEntryJasaMedis.jasaMedisTotal" placeholder="Total" class="mt-1 ml-2"
                            :errorshas="__($errors->has('formEntryJasaMedis.jasaMedisTotal'))"
                            value="{{ number_format($formEntryJasaMedis['jasaMedisPrice'] * $formEntryJasaMedis['jasaMedisQty']) }}"
                            :disabled="true" />
                    </div>
                </div>

                <div class="col-span-1">
                    <x-input-label for="" :value="__('Hapus')" :required="__(true)" />
                    <x-alternative-button class="inline-flex ml-2" wire:click.prevent="resetformEntryJasaMedis()">
                        <svg class="w-5 h-5 text-gray-800 dark:text-white" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                            <path
                                d="M17 4h-4V2a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v2H1a1 1 0 0 0 0 2h1v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6h1a1 1 0 1 0 0-2ZM7 2h4v2H7V2Zm1 14a1 1 0 1 1-2 0V8a1 1 0 0 1 2 0v8Zm4 0a1 1 0 0 1-2 0V8a1 1 0 0 1 2 0v8Z" />
                        </svg>
                    </x-alternative-button>
                </div>
            </div>

            {{-- Simpan --}}
            <div class="w-full">
                <div wire:loading wire:target="insertJasaMedis">
                    <x-loading />
                </div>

                <x-primary-button :disabled="false" wire:click.prevent="insertJasaMedis()" type="button"
                    wire:loading.remove class="w-full">
                    Simpan JasaMedis
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
                                    <x-sort-link :active=false wire:click.prevent="" role="button" href="#">
                                        Tgl
                                    </x-sort-link>
                                </th>

                                <th scope="col" class="px-4 py-3 ">
                                    <x-sort-link :active=false wire:click.prevent="" role="button" href="#">
                                        JasaMedis
                                    </x-sort-link>
                                </th>

                                <th scope="col" class="px-4 py-3 ">
                                    <x-sort-link :active=false wire:click.prevent="" role="button" href="#">
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

                                $sortedRiJasaMedis = collect($dataJasaMedis['riJasaMedis'] ?? [])
                                    ->sortByDesc(function ($item) {
                                        $date = $item['actp_date'] ?? '';

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
                                            // Jika parsing gagal, juga kembalikan 0
                                            return 0;
                                        }
                                    })
                                    ->values();

                            @endphp

                            @if ($sortedRiJasaMedis->isNotEmpty())
                                @foreach ($sortedRiJasaMedis as $key => $JasaMedis)
                                    @php

                                        $adminLogs = $dataDaftarRi['AdministrasiRI']['userLogs'] ?? [];
                                        // bungkus array jadi Collection
                                        $adminLogsColl = collect($adminLogs);

                                        // filter userLogDesc yang diawali "JasaMedis"
                                        // dan mengandung "Txn No:{$JasaMedis['actp_no']}"
                                        $filteredLogs = $adminLogsColl
                                            ->filter(function ($log) use ($JasaMedis) {
                                                return Str::startsWith($log['userLogDesc'], 'JasaMedis') &&
                                                    Str::contains(
                                                        $log['userLogDesc'],
                                                        'Txn No:' . $JasaMedis['actp_no'],
                                                    );
                                            })
                                            ->values();
                                    @endphp
                                    <tr class="border-b group dark:border-gray-700">

                                        <td
                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                            {{ $JasaMedis['actp_date'] }}

                                            @if ($filteredLogs->isNotEmpty())
                                                @foreach ($filteredLogs as $log)
                                                    <br>
                                                    <span class="text-xs italic text-gray-600">
                                                        {{ 'Log ' }}{{ $log['userLogDate'] }} --
                                                        {{ $log['userLog'] }}</span>
                                                @endforeach
                                            @else
                                                <br>
                                                <span class="text-xs italic">
                                                    — no matching log —
                                                </span>
                                            @endif
                                        </td>

                                        <td
                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                            {{ $JasaMedis['pact_id'] }} {{ $JasaMedis['pact_desc'] }}
                                        </td>
                                        @php
                                            // Ambil data, default 0 jika bukan numeric
                                            $qty = is_numeric($JasaMedis['actp_qty'] ?? null)
                                                ? $JasaMedis['actp_qty']
                                                : 0;
                                            $price = is_numeric($JasaMedis['actp_price'] ?? null)
                                                ? $JasaMedis['actp_price']
                                                : 0;
                                            $total = $qty * $price;
                                        @endphp

                                        <td
                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                            {{ number_format($qty) }}x
                                            {{ number_format($price) }} =
                                            {{ number_format($total) }}

                                        </td>


                                        <td
                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">

                                            <x-alternative-button class="inline-flex"
                                                wire:click.prevent="removeJasaMedis('{{ $JasaMedis['actp_no'] }}')">
                                                <svg class="w-5 h-5 text-gray-800 dark:text-white" aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                    viewBox="0 0 18 20">
                                                    <path
                                                        d="M17 4h-4V2a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v2H1a1 1 0 0 0 0 2h1v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6h1a1 1 0 1 0 0-2ZM7 2h4v2H7V2Zm1 14a1 1 0 1 1-2 0V8a1 1 0 0 1 2 0v8Zm4 0a1 1 0 0 1-2 0V8a1 1 0 0 1 2 0v8Z" />
                                                </svg>
                                                {{ 'Hapus ' . $JasaMedis['actp_no'] }}
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
