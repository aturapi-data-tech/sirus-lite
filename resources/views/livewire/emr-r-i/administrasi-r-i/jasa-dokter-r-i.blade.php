<div>

    @php
        $disabledProperty = true;
        $disabledPropertyRjStatus = false;
    @endphp

    <div class="w-full mb-1">
        <div id="TransaksiRawatInap">
            <div class="grid grid-cols-10 gap-2" x-data>
                <div class="col-span-2">
                    <x-input-label for="formEntryJasaDokter.jasaDokterDate" :value="__('Tanggal Kunjungan')" :required="__(true)" />
                    <div>
                        <div class="flex items-center mb-2">
                            @if (!$formEntryJasaDokter['jasaDokterDate'])
                                <div class="w-full mt-2 ml-2">
                                    <div wire:loading wire:target="setJasaDokterDate">
                                        <x-loading />
                                    </div>

                                    <x-yellow-button :disabled="false"
                                        wire:click.prevent="setJasaDokterDate('{{ date('d/m/Y H:i:s') }}')" type="button"
                                        wire:loading.remove class="w-full">
                                        <div wire:poll.20s>
                                            {{ date('d/m/Y H:i:s') }}
                                        </div>
                                    </x-yellow-button>
                                </div>
                            @else
                                <x-text-input id="formEntryJasaDokter.jasaDokterDate" type="text"
                                    placeholder="dd/mm/yyyy hh24:mi:ss" class="mt-1 ml-2" :errorshas="__($errors->has('formEntryJasaDokter.jasaDokterDate'))"
                                    wire:model="formEntryJasaDokter.jasaDokterDate" :disabled="$disabledPropertyRjStatus" />
                            @endif
                        </div>
                        @error('formEntryJasaDokter.jasaDokterDate')
                            <x-input-error :messages=$message />
                        @enderror
                    </div>
                </div>

                <div class="col-span-2">
                    {{-- LOV Dokter --}}
                    @if (empty($collectingMyDokter))
                        <div class="">
                            @include('livewire.component.l-o-v.list-of-value-dokter.list-of-value-dokter')
                        </div>
                    @else
                        <x-input-label for="formEntryJasaDokter.drName" :value="__('Nama Dokter')" :required="__(true)" />
                        <div>
                            <x-text-input id="formEntryJasaDokter.drName" placeholder="Nama Dokter" class="mt-1 ml-2"
                                :errorshas="__($errors->has('formEntryJasaDokter.drName'))" wire:model="formEntryJasaDokter.drName" :disabled="true" />

                        </div>
                    @endif
                    @error('formEntryJasaDokter.drId')
                        <x-input-error :messages=$message />
                    @enderror
                </div>


                <div class="col-span-2">
                    {{-- LOV JasaDokter) --}}
                    @if (empty($collectingMyJasaDokter))
                        <div class="">
                            @include('livewire.component.l-o-v.list-of-value-jasa-dokter.list-of-value-jasa-dokter')
                        </div>
                    @else
                        <x-input-label for="formEntryJasaDokter.jasaDokterDesc" :value="__('Nama JasaDokter')" :required="__(true)" />
                        <div>
                            <x-text-input id="formEntryJasaDokter.jasaDokterDesc" placeholder="Nama JasaDokter"
                                class="mt-1 ml-2" :errorshas="__($errors->has('formEntryJasaDokter.jasaDokterDesc'))" wire:model="formEntryJasaDokter.jasaDokterDesc"
                                :disabled="true" />

                        </div>
                    @endif
                    @error('formEntryJasaDokter.jasaDokterId')
                        <x-input-error :messages=$message />
                    @enderror
                </div>

                <div class="col-span-1">
                    <x-input-label for="formEntryJasaDokter.jasaDokterQty" :value="__('Jml')" :required="__(true)" />
                    <div>
                        <x-text-input id="formEntryJasaDokter.jasaDokterQty" placeholder="Jml" class="mt-1 ml-2"
                            :errorshas="__($errors->has('formEntryJasaDokter.jasaDokterQty'))" wire:model="formEntryJasaDokter.jasaDokterQty" :disabled="$disabledPropertyRjStatus" />
                        @error('formEntryJasaDokter.jasaDokterQty')
                            <x-input-error :messages=$message />
                        @enderror
                    </div>
                </div>

                <div class="col-span-1">
                    <x-input-label for="formEntryJasaDokter.jasaDokterPrice" :value="__('Tarif')" :required="__(true)" />
                    <div>
                        <x-text-input id="formEntryJasaDokter.jasaDokterPrice" placeholder="Tarif" class="mt-1 ml-2"
                            :errorshas="__($errors->has('formEntryJasaDokter.jasaDokterPrice'))" wire:model="formEntryJasaDokter.jasaDokterPrice" :disabled="$disabledPropertyRjStatus"
                            x-on:keyup.enter="$wire.insertJasaDokter()" />
                        @error('formEntryJasaDokter.jasaDokterPrice')
                            <x-input-error :messages=$message />
                        @enderror
                    </div>
                </div>

                <div class="col-span-1">
                    <x-input-label for="formEntryJasaDokter.jasaDokterTotal" :value="__('Total')" :required="__(true)" />
                    <div>
                        <x-text-input id="formEntryJasaDokter.jasaDokterTotal" placeholder="Total" class="mt-1 ml-2"
                            :errorshas="__($errors->has('formEntryJasaDokter.jasaDokterTotal'))"
                            value="{{ number_format($formEntryJasaDokter['jasaDokterPrice'] * $formEntryJasaDokter['jasaDokterQty']) }}"
                            :disabled="true" />
                    </div>
                </div>

                <div class="col-span-1">
                    <x-input-label for="" :value="__('Hapus')" :required="__(true)" />
                    <x-alternative-button class="inline-flex ml-2" wire:click.prevent="resetformEntryJasaDokter()">
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
                <div wire:loading wire:target="insertJasaDokter">
                    <x-loading />
                </div>

                <x-primary-button :disabled="false" wire:click.prevent="insertJasaDokter()" type="button"
                    wire:loading.remove class="w-full">
                    Simpan JasaDokter
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
                                        JasaDokter
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

                            @isset($dataJasaDokter['riJasaDokter'])
                                @foreach ($dataJasaDokter['riJasaDokter'] as $key => $JasaDokter)
                                    <tr class="border-b group dark:border-gray-700">

                                        <td
                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                            {{ $JasaDokter['actd_date'] }}
                                        </td>

                                        <td
                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                            {{ $JasaDokter['dr_id'] }} {{ $JasaDokter['dr_name'] }}
                                            </br>
                                            {{ $JasaDokter['accdoc_id'] }} {{ $JasaDokter['accdoc_desc'] }}
                                        </td>


                                        <td
                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                            {{ number_format($JasaDokter['actd_qty']) }}x
                                            {{ number_format($JasaDokter['actd_price']) }}=
                                            {{ number_format($JasaDokter['actd_price'] * $JasaDokter['actd_qty']) }}

                                        </td>


                                        <td
                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">

                                            <x-alternative-button class="inline-flex"
                                                wire:click.prevent="removeJasaDokter('{{ $JasaDokter['actd_no'] }}')">
                                                <svg class="w-5 h-5 text-gray-800 dark:text-white" aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                    viewBox="0 0 18 20">
                                                    <path
                                                        d="M17 4h-4V2a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v2H1a1 1 0 0 0 0 2h1v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6h1a1 1 0 1 0 0-2ZM7 2h4v2H7V2Zm1 14a1 1 0 1 1-2 0V8a1 1 0 0 1 2 0v8Zm4 0a1 1 0 0 1-2 0V8a1 1 0 0 1 2 0v8Z" />
                                                </svg>
                                                {{ 'Hapus ' . $JasaDokter['actd_no'] }}
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
