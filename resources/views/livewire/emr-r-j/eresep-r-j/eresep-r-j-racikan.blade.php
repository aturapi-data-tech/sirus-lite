<div>

    @php
        $disabledProperty = true;
        $disabledPropertyRjStatus = false;
    @endphp

    {{-- jika anamnesa kosong ngak usah di render --}}
    {{-- @if (isset($dataDaftarPoliRJ['diagnosis'])) --}}
    <div class="w-full mb-1 ">
        <div id="TransaksiRawatJalan" class="p-2">
            <div class="p-2 rounded-lg bg-gray-50">



                <div id="TransaksiRawatJalan" class="px-4">
                    <x-input-label for="" :value="__('Racikan')" :required="__(false)" class="pt-2 sm:text-xl" />


                    @role(['Dokter', 'Admin'])
                        {{-- collectingMyProduct / obat --}}
                        <div class="inline-flex space-x-0.5" x-data>
                            <div class="basis-1/4">
                                <x-input-label for="collectingMyProduct.noRacikan" :value="__('Racikan')" :required="__(true)" />

                                <div>
                                    <x-text-input id="collectingMyProduct.noRacikan" placeholder="Racikan" class="mt-1 ml-2"
                                        :errorshas="__($errors->has('collectingMyProduct.noRacikan'))" :disabled=$disabledPropertyRjStatus
                                        wire:model.debounce.500ms="noRacikan" x-ref="collectingMyProductnoRacikan"
                                        x-on:keyup.enter="$refs.collectingMyProductproductName.focus()" />

                                    @error('collectingMyProduct.noRacikan')
                                        <x-input-error :messages=$message />
                                    @enderror
                                </div>
                            </div>

                            <div class="basis-3/4">
                                <x-input-label for="collectingMyProduct.productName" :value="__('Nama Obat')" :required="__(true)" />

                                <div>
                                    <x-text-input id="collectingMyProduct.productName" placeholder="Nama Obat"
                                        class="mt-1 ml-2" :errorshas="__($errors->has('collectingMyProduct.productName'))" :disabled=$disabledPropertyRjStatus
                                        x-init="$refs.collectingMyProductproductName.focus()" x-ref="collectingMyProductproductName"
                                        x-on:keyup.enter="$refs.collectingMyProductsedia.focus()"
                                        wire:model.debounce.500ms="collectingMyProduct.productName" />

                                    @error('collectingMyProduct.productName')
                                        <x-input-error :messages=$message />
                                    @enderror
                                </div>
                            </div>



                            <div class="basis-1/4">
                                <x-input-label for="collectingMyProduct.sedia" :value="__('Sedia')" :required="__(true)" />

                                <div>
                                    <x-text-input id="collectingMyProduct.sedia" placeholder="Sedia" class="mt-1 ml-2"
                                        :errorshas="__($errors->has('collectingMyProduct.sedia'))" :disabled=$disabledPropertyRjStatus
                                        x-ref="collectingMyProductsedia"
                                        x-on:keyup.enter="$refs.collectingMyProductqty.focus()"
                                        wire:model.debounce.500ms="collectingMyProduct.sedia" />

                                    @error('collectingMyProduct.sedia')
                                        <x-input-error :messages=$message />
                                    @enderror
                                </div>
                            </div>



                            <div class="basis-2/4">
                                <x-input-label for="collectingMyProduct.qty" :value="__('Jml Racikan')" :required="__(false)" />

                                <div>
                                    <x-text-input id="collectingMyProduct.qty" placeholder="Jml Racikan" class="mt-1 ml-2"
                                        :errorshas="__($errors->has('collectingMyProduct.qty'))" :disabled=$disabledPropertyRjStatus
                                        wire:model.debounce.500ms="collectingMyProduct.qty" x-ref="collectingMyProductqty"
                                        x-on:keyup.enter="$refs.collectingMyProductcatatan.focus()" />

                                    @error('collectingMyProduct.qty')
                                        <x-input-error :messages=$message />
                                    @enderror
                                </div>
                            </div>

                            <div class="basis-1/4">
                                <x-input-label for="collectingMyProduct.catatan" :value="__('Catatan')" :required="__(false)" />

                                <div>
                                    <x-text-input id="collectingMyProduct.catatan" placeholder="Catatan" class="mt-1 ml-2"
                                        :errorshas="__($errors->has('collectingMyProduct.catatan'))" :disabled=$disabledPropertyRjStatus
                                        x-ref="collectingMyProductcatatan"
                                        x-on:keyup.enter="$refs.collectingMyProductcatatanKhusus.focus()"
                                        wire:model.debounce.500ms="collectingMyProduct.catatan" />

                                    @error('collectingMyProduct.catatan')
                                        <x-input-error :messages=$message />
                                    @enderror
                                </div>
                            </div>

                            <div class="basis-3/4">
                                <x-input-label for="collectingMyProduct.catatanKhusus" :value="__('Signa')"
                                    :required="__(false)" />

                                <div>
                                    <x-text-input id="collectingMyProduct.catatanKhusus" placeholder="Signa"
                                        class="mt-1 ml-2" :errorshas="__($errors->has('collectingMyProduct.catatanKhusus'))" :disabled=$disabledPropertyRjStatus
                                        wire:model="collectingMyProduct.catatanKhusus"
                                        x-on:keyup.enter="$wire.insertProduct()
                                    $refs.collectingMyProductproductName.focus()"
                                        x-ref="collectingMyProductcatatanKhusus" />

                                    @error('collectingMyProduct.catatanKhusus')
                                        <x-input-error :messages=$message />
                                    @enderror
                                </div>
                            </div>

                            <div class="basis-1/4">
                                <x-input-label for="" :value="__('Hapus')" :required="__(false)" />

                                <x-alternative-button class="inline-flex ml-2"
                                    wire:click.prevent="resetcollectingMyProduct()"
                                    x-on:click="$refs.collectingMyProductproductName.focus()">
                                    <svg class="w-5 h-5 text-gray-800 dark:text-white" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                                        <path
                                            d="M17 4h-4V2a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v2H1a1 1 0 0 0 0 2h1v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6h1a1 1 0 1 0 0-2ZM7 2h4v2H7V2Zm1 14a1 1 0 1 1-2 0V8a1 1 0 0 1 2 0v8Zm4 0a1 1 0 0 1-2 0V8a1 1 0 0 1 2 0v8Z" />
                                    </svg>
                                </x-alternative-button>
                            </div>

                        </div>
                        {{-- collectingMyProduct / obat --}}
                    @endrole


                    {{-- ///////////////////////////////// --}}
                    <div class="flex flex-col my-2">
                        <div class="overflow-x-auto rounded-lg">
                            <div class="inline-block min-w-full align-middle">
                                <div class="overflow-hidden shadow sm:rounded-lg">
                                    <table class="w-full text-sm text-left text-gray-500 table-auto dark:text-gray-400">
                                        <thead
                                            class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                                            <tr>
                                                <th scope="col" class="px-4 py-3">

                                                    <x-sort-link :active=false wire:click.prevent="" role="button"
                                                        href="#">
                                                        Racikan/NonRacikan
                                                    </x-sort-link>

                                                </th>

                                                <th scope="col" class="px-4 py-3 ">
                                                    <x-sort-link :active=false wire:click.prevent="" role="button"
                                                        href="#">
                                                        Obat
                                                    </x-sort-link>
                                                </th>

                                                <th scope="col" class="px-4 py-3 ">
                                                    <x-sort-link :active=false wire:click.prevent="" role="button"
                                                        href="#">
                                                        Sediaan
                                                    </x-sort-link>
                                                </th>

                                                <th scope="col" class="px-4 py-3 ">
                                                    <x-sort-link :active=false wire:click.prevent="" role="button"
                                                        href="#">
                                                        Jml Racikan
                                                    </x-sort-link>
                                                </th>

                                                <th scope="col" class="px-4 py-3 ">
                                                    <x-sort-link :active=false wire:click.prevent="" role="button"
                                                        href="#">
                                                        Catatan
                                                    </x-sort-link>
                                                </th>

                                                <th scope="col" class="px-4 py-3 ">
                                                    <x-sort-link :active=false wire:click.prevent="" role="button"
                                                        href="#">
                                                        Signa
                                                    </x-sort-link>
                                                </th>

                                                <th scope="col" class="w-8 px-4 py-3 text-center">
                                                    Action
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-gray-800">

                                            @isset($dataDaftarPoliRJ['eresepRacikan'])
                                                @foreach ($dataDaftarPoliRJ['eresepRacikan'] as $key => $eresep)
                                                    <tr class="border-b group dark:border-gray-700">

                                                        <td
                                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                                            {{ $eresep['jenisKeterangan'] . '  (' . $eresep['noRacikan'] . ')' }}
                                                        </td>


                                                        <td
                                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                                            {{ $eresep['productName'] }}
                                                        </td>

                                                        <td
                                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                                            {{ $eresep['sedia'] }}
                                                        </td>

                                                        <td
                                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                                            {{ $eresep['qty'] }}
                                                        </td>

                                                        <td
                                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                                            {{ $eresep['catatan'] }}
                                                        </td>

                                                        <td
                                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                                            {{ $eresep['catatanKhusus'] }}
                                                        </td>


                                                        <td
                                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                                            @role(['Dokter', 'Admin'])
                                                                <x-alternative-button class="inline-flex"
                                                                    wire:click.prevent="removeProduct('{{ $eresep['rjObatDtl'] }}')">
                                                                    <svg class="w-5 h-5 text-gray-800 dark:text-white"
                                                                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                                        fill="currentColor" viewBox="0 0 18 20">
                                                                        <path
                                                                            d="M17 4h-4V2a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v2H1a1 1 0 0 0 0 2h1v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6h1a1 1 0 1 0 0-2ZM7 2h4v2H7V2Zm1 14a1 1 0 1 1-2 0V8a1 1 0 0 1 2 0v8Zm4 0a1 1 0 0 1-2 0V8a1 1 0 0 1 2 0v8Z" />
                                                                    </svg>
                                                                    {{ 'Hapus ' }}
                                                                </x-alternative-button>
                                                            @endrole
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



    </div>
</div>
