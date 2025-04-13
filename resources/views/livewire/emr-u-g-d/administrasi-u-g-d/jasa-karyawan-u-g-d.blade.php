<div>

    @php
        $disabledProperty = true;
        $disabledPropertyRjStatus = false;
    @endphp

    {{-- jika anamnesa kosong ngak usah di render --}}
    {{-- @if (isset($dataDaftarUgd['diagnosis'])) --}}
    <div class="w-full mb-1 ">

        <div id="TransaksiRawatJalan" class="">

            @if (!$collectingMyJasaKaryawan)
                <div>
                    @include('livewire.component.l-o-v.list-of-value-jasa-karyawan.list-of-value-jasa-karyawan')
                </div>
            @else
                {{-- collectingMyJasaKaryawan / obat --}}
                <div class="grid grid-cols-12 gap-2 " x-data>
                    <div class="col-span-1">
                        <x-input-label for="formEntryJasaKaryawan.jasaKaryawanId" :value="__('Kode')" :required="__(true)" />

                        <div>
                            <x-text-input id="formEntryJasaKaryawan.jasaKaryawanId" placeholder="Kode" class="mt-1 ml-2"
                                :errorshas="__($errors->has('formEntryJasaKaryawan.jasaKaryawanId'))" :disabled=true wire:model="formEntryJasaKaryawan.jasaKaryawanId" />

                            @error('formEntryJasaKaryawan.jasaKaryawanId')
                                <x-input-error :messages=$message />
                            @enderror
                        </div>
                    </div>

                    <div class="col-span-3">
                        <x-input-label for="formEntryJasaKaryawan.jasaKaryawanDesc" :value="__('Jasa Karyawan')"
                            :required="__(true)" />

                        <div>
                            <x-text-input id="formEntryJasaKaryawan.jasaKaryawanDesc" placeholder="Jasa Karyawan"
                                class="mt-1 ml-2" :errorshas="__($errors->has('formEntryJasaKaryawan.jasaKaryawanDesc'))" :disabled=true
                                wire:model="formEntryJasaKaryawan.jasaKaryawanDesc" />

                            @error('formEntryJasaKaryawan.jasaKaryawanDesc')
                                <x-input-error :messages=$message />
                            @enderror
                        </div>
                    </div>

                    <div class="col-span-3">
                        <x-input-label for="formEntryJasaKaryawan.jasaKaryawanPrice" :value="__('Tarif')"
                            :required="__(true)" />

                        <div>
                            <x-text-input id="formEntryJasaKaryawan.jasaKaryawanPrice" placeholder="Tarif"
                                class="mt-1 ml-2" :errorshas="__($errors->has('formEntryJasaKaryawan.jasaKaryawanPrice'))" :disabled=$disabledPropertyRjStatus
                                wire:model="formEntryJasaKaryawan.jasaKaryawanPrice" x-init="$refs.formEntryJasaKaryawanJasaKaryawanPrice.focus()"
                                x-ref="formEntryJasaKaryawanJasaKaryawanPrice"
                                x-on:keyup.enter="$wire.insertJasaKaryawan()
                                $refs.formEntryJasaKaryawanJasaKaryawanPrice.focus()" />

                            @error('formEntryJasaKaryawan.jasaKaryawanPrice')
                                <x-input-error :messages=$message />
                            @enderror
                        </div>
                    </div>



                    <div class="col-span-1">
                        <x-input-label for="" :value="__('Hapus')" :required="__(true)" />

                        <x-alternative-button class="inline-flex ml-2"
                            wire:click.prevent="resetformEntryJasaKaryawan()">
                            <svg class="w-5 h-5 text-gray-800 dark:text-white" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                                <path
                                    d="M17 4h-4V2a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v2H1a1 1 0 0 0 0 2h1v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6h1a1 1 0 1 0 0-2ZM7 2h4v2H7V2Zm1 14a1 1 0 1 1-2 0V8a1 1 0 0 1 2 0v8Zm4 0a1 1 0 0 1-2 0V8a1 1 0 0 1 2 0v8Z" />
                            </svg>
                        </x-alternative-button>
                    </div>

                </div>
                {{-- collectingMyJasaKaryawan / obat --}}
            @endif



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
                                                Kode
                                            </x-sort-link>
                                        </th>

                                        <th scope="col" class="px-4 py-3 ">
                                            <x-sort-link :active=false wire:click.prevent="" role="button"
                                                href="#">
                                                Jasa Karyawan
                                            </x-sort-link>
                                        </th>

                                        <th scope="col" class="px-4 py-3 ">
                                            <x-sort-link :active=false wire:click.prevent="" role="button"
                                                href="#">
                                                Tarif Jasa Karyawan
                                            </x-sort-link>
                                        </th>


                                        <th scope="col" class="w-8 px-4 py-3 text-center">
                                            Action
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800">

                                    @isset($dataDaftarUgd['JasaKaryawan'])
                                        @foreach ($dataDaftarUgd['JasaKaryawan'] as $key => $JasaKaryawan)
                                            <tr class="border-b group dark:border-gray-700">

                                                <td
                                                    class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                                    {{ $JasaKaryawan['JasaKaryawanId'] }}
                                                </td>

                                                <td
                                                    class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                                    {{ $JasaKaryawan['JasaKaryawanDesc'] }}
                                                </td>


                                                <td
                                                    class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                                    {{ $JasaKaryawan['JasaKaryawanPrice'] }}
                                                </td>


                                                <td
                                                    class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">

                                                    <x-alternative-button class="inline-flex"
                                                        wire:click.prevent="removeJasaKaryawan('{{ $JasaKaryawan['rjActeDtl'] }}')">
                                                        <svg class="w-5 h-5 text-gray-800 dark:text-white"
                                                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                            fill="currentColor" viewBox="0 0 18 20">
                                                            <path
                                                                d="M17 4h-4V2a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v2H1a1 1 0 0 0 0 2h1v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6h1a1 1 0 1 0 0-2ZM7 2h4v2H7V2Zm1 14a1 1 0 1 1-2 0V8a1 1 0 0 1 2 0v8Zm4 0a1 1 0 0 1-2 0V8a1 1 0 0 1 2 0v8Z" />
                                                        </svg>
                                                        {{ 'Hapus ' . $JasaKaryawan['JasaKaryawanId'] }}
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
