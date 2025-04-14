<div>

    @php
        $disabledProperty = true;
        $disabledPropertyRjStatus = false;
    @endphp

    {{-- jika anamnesa kosong ngak usah di render --}}
    {{-- @if (isset($dataDaftarUgd['diagnosis'])) --}}
    <div class="w-full mb-1 ">

        <div id="TransaksiRawatJalan" class="">

            <div class="">
                {{-- LOV Dokter --}}
                @if (!$collectingMyDokter)
                    <div class="">
                        @include('livewire.component.l-o-v.list-of-value-dokter.list-of-value-dokter')
                    </div>
                @else
                    <x-input-label for="formEntryJasaDokter.drName" :value="__('Nama Dokter')" :required="__(false)"
                        wire:click="$set('collectingMyDokter',[])" />
                    <div>
                        <x-text-input id="formEntryJasaDokter.drName" placeholder="Nama Dokter" class="mt-1 ml-2"
                            :errorshas="__($errors->has('formEntryJasaDokter.drName'))" wire:model="formEntryJasaDokter.drName" :disabled="true" />

                    </div>
                @endif
                @error('formEntryJasaDokter.drId')
                    <x-input-error :messages=$message />
                @enderror
            </div>


            <div class="">
                {{-- LOV Dokter --}}
                @if (!$collectingMyJasaDokter)
                    <div class="">
                        @include('livewire.component.l-o-v.list-of-value-jasa-dokter.list-of-value-jasa-dokter')
                    </div>
                @else
                    {{-- collectingMyJasaDokter / obat --}}
                    <div class="grid grid-cols-12 gap-2 " x-data>
                        <div class="col-span-1">
                            <x-input-label for="formEntryJasaDokter.jasaDokterId" :value="__('Kode')" :required="__(true)"
                                wire:click="$set('collectingMyJasaDokter',[])" />

                            <div>
                                <x-text-input id="formEntryJasaDokter.jasaDokterId" placeholder="Kode" class="mt-1 ml-2"
                                    :errorshas="__($errors->has('formEntryJasaDokter.jasaDokterId'))" :disabled=true wire:model="formEntryJasaDokter.jasaDokterId" />

                                @error('formEntryJasaDokter.jasaDokterId')
                                    <x-input-error :messages=$message />
                                @enderror
                            </div>
                        </div>

                        <div class="col-span-3">
                            <x-input-label for="formEntryJasaDokter.jasaDokterDesc" :value="__('Jasa Dokter')" :required="__(false)"
                                wire:click="$set('collectingMyJasaDokter',[])" />

                            <div>
                                <x-text-input id="formEntryJasaDokter.jasaDokterDesc" placeholder="Jasa Dokter"
                                    class="mt-1 ml-2" :errorshas="__($errors->has('formEntryJasaDokter.jasaDokterDesc'))" wire:model="formEntryJasaDokter.jasaDokterDesc"
                                    :disabled="true" />

                                @error('formEntryJasaDokter.jasaDokterDesc')
                                    <x-input-error :messages=$message />
                                @enderror
                            </div>
                        </div>

                        <div class="col-span-3">
                            <x-input-label for="formEntryJasaDokter.jasaDokterPrice" :value="__('Tarif')"
                                :required="__(true)" />

                            <div>
                                <x-text-input id="formEntryJasaDokter.jasaDokterPrice" placeholder="Tarif"
                                    class="mt-1 ml-2" :errorshas="__($errors->has('formEntryJasaDokter.jasaDokterPrice'))" :disabled=$disabledPropertyRjStatus
                                    wire:model="formEntryJasaDokter.jasaDokterPrice" x-init="$refs.formEntryJasaDokterJasaDokterPrice.focus()"
                                    x-ref="formEntryJasaDokterJasaDokterPrice"
                                    x-on:keyup.enter="$wire.insertJasaDokter()
                                $refs.formEntryJasaDokterJasaDokterPrice.focus()" />

                                @error('formEntryJasaDokter.jasaDokterPrice')
                                    <x-input-error :messages=$message />
                                @enderror
                            </div>
                        </div>



                        <div class="col-span-1">
                            <x-input-label for="" :value="__('Hapus')" :required="__(true)" />

                            <x-alternative-button class="inline-flex ml-2"
                                wire:click.prevent="resetformEntryJasaDokter()">
                                <svg class="w-5 h-5 text-gray-800 dark:text-white" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                                    <path
                                        d="M17 4h-4V2a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v2H1a1 1 0 0 0 0 2h1v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6h1a1 1 0 1 0 0-2ZM7 2h4v2H7V2Zm1 14a1 1 0 1 1-2 0V8a1 1 0 0 1 2 0v8Zm4 0a1 1 0 0 1-2 0V8a1 1 0 0 1 2 0v8Z" />
                                </svg>
                            </x-alternative-button>
                        </div>

                    </div>
                    {{-- collectingMyJasaDokter / obat --}}
                @endif
                @error('formEntryJasaDokter.jasaDokterId')
                    <x-input-error :messages=$message />
                @enderror
            </div>


            {{-- ///////////////////////////////// --}}
            <div class="flex flex-col my-2">
                <div class="overflow-x-auto rounded-lg">
                    <div class="inline-block min-w-full align-middle">
                        <div class="overflow-hidden shadow sm:rounded-lg">
                            <table class="w-full text-sm text-left text-gray-500 table-auto dark:text-gray-400">
                                <thead
                                    class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>

                                        <th scope="col" class="px-4 py-3 ">
                                            <x-sort-link :active=false wire:click.prevent="" role="button"
                                                href="#">
                                                Dokter
                                            </x-sort-link>
                                        </th>

                                        <th scope="col" class="px-4 py-3 ">
                                            <x-sort-link :active=false wire:click.prevent="" role="button"
                                                href="#">
                                                Jasa Dokter
                                            </x-sort-link>
                                        </th>

                                        <th scope="col" class="px-4 py-3 ">
                                            <x-sort-link :active=false wire:click.prevent="" role="button"
                                                href="#">
                                                Tarif Jasa Dokter
                                            </x-sort-link>
                                        </th>


                                        <th scope="col" class="w-8 px-4 py-3 text-center">
                                            Action
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800">

                                    @isset($dataDaftarUgd['JasaDokter'])
                                        @foreach ($dataDaftarUgd['JasaDokter'] as $key => $JasaDokter)
                                            <tr class="border-b group dark:border-gray-700">

                                                <td
                                                    class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                                    {{ $JasaDokter['DokterId'] ?? '-' }}/{{ $JasaDokter['DokterName'] ?? '-' }}
                                                </td>

                                                <td
                                                    class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                                    {{ $JasaDokter['JasaDokterId'] }}/{{ $JasaDokter['JasaDokterDesc'] }}
                                                </td>


                                                <td
                                                    class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                                    {{ $JasaDokter['JasaDokterPrice'] }}
                                                </td>


                                                <td
                                                    class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">

                                                    <x-alternative-button class="inline-flex"
                                                        wire:click.prevent="removeJasaDokter('{{ $JasaDokter['rjaccdocDtl'] }}')">
                                                        <svg class="w-5 h-5 text-gray-800 dark:text-white"
                                                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                            fill="currentColor" viewBox="0 0 18 20">
                                                            <path
                                                                d="M17 4h-4V2a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v2H1a1 1 0 0 0 0 2h1v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6h1a1 1 0 1 0 0-2ZM7 2h4v2H7V2Zm1 14a1 1 0 1 1-2 0V8a1 1 0 0 1 2 0v8Zm4 0a1 1 0 0 1-2 0V8a1 1 0 0 1 2 0v8Z" />
                                                        </svg>
                                                        {{ 'Hapus ' . $JasaDokter['JasaDokterId'] }}
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
            {{-- ///////////////////////////////// --}}


        </div>

    </div>
</div>
