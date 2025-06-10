<div>

    @php
        $disabledProperty = true;
        $disabledPropertyRjStatus = false;
    @endphp

    <div class="w-full mb-1">
        <div id="TransaksiRawatInap">
            <div class="grid grid-cols-5 gap-2" x-data>
                <div class="">
                    <x-input-label for="formEntryVisit.visitDate" :value="__('Tanggal Kunjungan')" :required="__(true)" />
                    <div>
                        <div class="flex items-center mb-2">
                            @if (!$formEntryVisit['visitDate'])
                                <div class="w-full mt-2 ml-2">
                                    <div wire:loading wire:target="setVisitDate">
                                        <x-loading />
                                    </div>

                                    <x-yellow-button :disabled="false"
                                        wire:click.prevent="setVisitDate('{{ date('d/m/Y H:i:s') }}')" type="button"
                                        wire:loading.remove class="w-full">
                                        <div wire:poll.20s>
                                            {{ date('d/m/Y H:i:s') }}
                                        </div>
                                    </x-yellow-button>
                                </div>
                            @else
                                <x-text-input id="formEntryVisit.visitDate" type="text"
                                    placeholder="dd/mm/yyyy hh24:mi:ss" class="mt-1 ml-2" :errorshas="__($errors->has('formEntryVisit.visitDate'))"
                                    wire:model="formEntryVisit.visitDate" :disabled="$disabledPropertyRjStatus" />
                            @endif
                        </div>
                        @error('formEntryVisit.visitDate')
                            <x-input-error :messages=$message />
                        @enderror
                    </div>
                </div>

                <div class="">
                    {{-- LOV Dokter --}}
                    @if (empty($collectingMyDokter))
                        <div class="">
                            @include('livewire.component.l-o-v.list-of-value-dokter.list-of-value-dokter')
                        </div>
                    @else
                        <x-input-label for="formEntryVisit.drName" :value="__('Nama Dokter')" :required="__(true)" />
                        <div>
                            <x-text-input id="formEntryVisit.drName" placeholder="Nama Dokter" class="mt-1 ml-2"
                                :errorshas="__($errors->has('formEntryVisit.drName'))" wire:model="formEntryVisit.drName" :disabled="true" />

                        </div>
                    @endif
                    @error('formEntryVisit.drId')
                        <x-input-error :messages=$message />
                    @enderror
                </div>

                <div class="">
                    <x-input-label for="formEntryVisit.visitPrice" :value="__('Tarif Kunjungan')" :required="__(true)" />
                    <div>
                        <x-text-input id="formEntryVisit.visitPrice" placeholder="Tarif Kunjungan" class="mt-1 ml-2"
                            :errorshas="__($errors->has('formEntryVisit.visitPrice'))" wire:model="formEntryVisit.visitPrice" :disabled="$disabledPropertyRjStatus"
                            x-on:keyup.enter="$wire.insertVisit()" />
                        @error('formEntryVisit.visitPrice')
                            <x-input-error :messages=$message />
                        @enderror
                    </div>
                </div>

                <div class="">
                    <x-input-label for="" :value="__('Hapus')" :required="__(true)" />
                    <x-alternative-button class="inline-flex ml-2" wire:click.prevent="resetformEntryVisit()">
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
                <div wire:loading wire:target="insertVisit">
                    <x-loading />
                </div>

                <x-primary-button :disabled="false" wire:click.prevent="insertVisit()" type="button"
                    wire:loading.remove class="w-full">
                    Simpan Visit
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
                                        Tgl Kunjungan
                                    </x-sort-link>
                                </th>

                                <th scope="col" class="px-4 py-3 ">
                                    <x-sort-link :active=false wire:click.prevent="" role="button" href="#">
                                        Dokter
                                    </x-sort-link>
                                </th>

                                <th scope="col" class="px-4 py-3 ">
                                    <x-sort-link :active=false wire:click.prevent="" role="button" href="#">
                                        Tarif Kunjungan
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

                                $sortedRiVisit = collect($dataVisit['riVisit'] ?? [])
                                    ->sortByDesc(function ($item) {
                                        $date = $item['visit_date'] ?? '';
                                        if (!$date) {
                                            return 0; // kosong, paling bawah
                                        }
                                        try {
                                            return Carbon::createFromFormat(
                                                'd/m/Y H:i:s',
                                                $date,
                                                env('APP_TIMEZONE'),
                                            )->timestamp;
                                        } catch (\Exception $e) {
                                            return 0; // format salah, juga paling bawah
                                        }
                                    })
                                    ->values();
                            @endphp

                            @if ($sortedRiVisit->isNotEmpty())
                                @foreach ($sortedRiVisit as $key => $Visit)
                                    @php

                                        $adminLogs = $dataDaftarRi['AdministrasiRI']['userLogs'] ?? [];
                                        // bungkus array jadi Collection
                                        $adminLogsColl = collect($adminLogs);

                                        // filter userLogDesc yang diawali "Visist"
                                        // dan mengandung "Txn No:{$Visist['visit_no']}"
                                        $filteredLogs = $adminLogsColl
                                            ->filter(function ($log) use ($Visit) {
                                                return Str::startsWith($log['userLogDesc'], 'Visist') &&
                                                    Str::contains($log['userLogDesc'], 'Txn No:' . $Visit['visit_no']);
                                            })
                                            ->values();
                                    @endphp
                                    <tr class="border-b group dark:border-gray-700">

                                        <td
                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                            {{ $Visit['visit_date'] }}
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
                                            {{ $Visit['dr_id'] }} {{ $Visit['dr_name'] }}
                                        </td>


                                        <td
                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                            {{ number_format($Visit['visit_price']) }}
                                        </td>


                                        <td
                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">

                                            <x-alternative-button class="inline-flex"
                                                wire:click.prevent="removeVisit('{{ $Visit['visit_no'] }}')">
                                                <svg class="w-5 h-5 text-gray-800 dark:text-white" aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                    viewBox="0 0 18 20">
                                                    <path
                                                        d="M17 4h-4V2a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v2H1a1 1 0 0 0 0 2h1v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6h1a1 1 0 1 0 0-2ZM7 2h4v2H7V2Zm1 14a1 1 0 1 1-2 0V8a1 1 0 0 1 2 0v8Zm4 0a1 1 0 0 1-2 0V8a1 1 0 0 1 2 0v8Z" />
                                                </svg>
                                                {{ 'Hapus ' . $Visit['visit_no'] }}
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
