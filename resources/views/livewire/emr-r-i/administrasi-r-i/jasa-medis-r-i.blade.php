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

                            @isset($dataJasaMedis['riJasaMedis'])
                                @foreach ($dataJasaMedis['riJasaMedis'] as $key => $JasaMedis)
                                    <tr class="border-b group dark:border-gray-700">

                                        <td
                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                            {{ $JasaMedis['actp_date'] }}
                                        </td>

                                        <td
                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                            {{ $JasaMedis['pact_id'] }} {{ $JasaMedis['pact_desc'] }}
                                        </td>


                                        <td
                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                            {{ number_format($JasaMedis['actp_qty']) }}x
                                            {{ number_format($JasaMedis['actp_price']) }}=
                                            {{ number_format($JasaMedis['actp_price'] * $JasaMedis['actp_qty']) }}

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
                            @endisset


                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>

</div>
