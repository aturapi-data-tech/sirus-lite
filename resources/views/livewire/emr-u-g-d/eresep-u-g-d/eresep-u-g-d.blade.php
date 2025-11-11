<div>
    @php
        $disabledProperty = true;
        $disabledPropertyRjStatus = $rjStatusRef == 'A' ? false : true;
    @endphp

    <div class="w-full mb-1 ">
        <div id="TransaksiRawatJalan" class="p-2">
            <div class="p-2 rounded-lg bg-gray-50">
                <div id="TransaksiRawatJalan" class="px-4">
                    <x-input-label for="" :value="__('Non Racikan')" :required="false" class="pt-2 sm:text-xl" />

                    @role(['Dokter', 'Admin'])
                        @if (!$collectingMyProduct)
                            <div>
                                @include('livewire.component.l-o-v.list-of-value-product.list-of-value-product')
                            </div>
                        @endif

                        @if ($collectingMyProduct)
                            {{-- Form draft item non-racikan --}}
                            <div class="flex items-baseline space-x-2" x-data>
                                {{-- hidden productId --}}
                                <div class="hidden">
                                    <x-input-label for="collectingMyProduct.productId" :value="__('Kode Obat')"
                                        :required="true" />
                                    <x-text-input id="collectingMyProduct.productId" placeholder="Kode Obat"
                                        class="mt-1 ml-2" :errorshas="__($errors->has('collectingMyProduct.productId'))" :disabled="true"
                                        wire:model="collectingMyProduct.productId" />
                                    @error('collectingMyProduct.productId')
                                        <x-input-error :messages="$message" />
                                    @enderror
                                </div>

                                {{-- Nama obat --}}
                                <div class="basis-3/6">
                                    <x-input-label for="collectingMyProduct.productName" :value="__('Nama Obat')"
                                        :required="true" />
                                    <x-text-input id="collectingMyProduct.productName" placeholder="Nama Obat"
                                        class="mt-1 ml-2" :errorshas="__($errors->has('collectingMyProduct.productName'))" :disabled="true"
                                        wire:model="collectingMyProduct.productName" />
                                    @error('collectingMyProduct.productName')
                                        <x-input-error :messages="$message" />
                                    @enderror
                                </div>

                                {{-- Qty --}}
                                <div class="basis-1/12">
                                    <x-input-label for="collectingMyProduct.qty" :value="__('Jml')" :required="true" />
                                    <x-text-input id="collectingMyProduct.qty" placeholder="Jml Obat" class="mt-1 ml-2"
                                        :errorshas="__($errors->has('collectingMyProduct.qty'))" :disabled="$disabledPropertyRjStatus" wire:model="collectingMyProduct.qty"
                                        data-seq="1"
                                        x-on:keydown.enter.prevent="
                                            $el.closest('.flex')?.querySelector('[data-seq=&quot;2&quot;]')?.focus()
                                        " />
                                    @error('collectingMyProduct.qty')
                                        <x-input-error :messages="$message" />
                                    @enderror
                                </div>

                                {{-- hidden price --}}
                                <div class="hidden">
                                    <x-input-label for="collectingMyProduct.productPrice" :value="__('Harga Obat')"
                                        :required="true" />
                                    <x-text-input id="collectingMyProduct.productPrice" placeholder="Harga Obat"
                                        class="mt-1 ml-2" :errorshas="__($errors->has('collectingMyProduct.productPrice'))" :disabled="true"
                                        wire:model="collectingMyProduct.productPrice" />
                                    @error('collectingMyProduct.productPrice')
                                        <x-input-error :messages="$message" />
                                    @enderror
                                </div>

                                {{-- Signa X --}}
                                <div class="basis-1/12">
                                    <x-input-label for="collectingMyProduct.signaX" :value="__('Signa')" :required="false" />
                                    <x-text-input id="collectingMyProduct.signaX" placeholder="Signa1" class="mt-1 ml-2"
                                        :errorshas="__($errors->has('collectingMyProduct.signaX'))" :disabled="$disabledPropertyRjStatus" wire:model="collectingMyProduct.signaX"
                                        data-seq="2"
                                        x-on:keydown.enter.prevent="
                                            $el.closest('.flex')?.querySelector('[data-seq=&quot;3&quot;]')?.focus()
                                        " />
                                    @error('collectingMyProduct.signaX')
                                        <x-input-error :messages="$message" />
                                    @enderror
                                </div>

                                <div class="basis-[4%]">
                                    <x-input-label for="" :value="__('*')" :required="false" />
                                    <span class="text-sm">dd</span>
                                </div>

                                {{-- Signa Hari --}}
                                <div class="basis-1/12">
                                    <x-input-label for="collectingMyProduct.signaHari" :value="__('*')"
                                        :required="false" />
                                    <x-text-input id="collectingMyProduct.signaHari" placeholder="Signa2" class="mt-1 ml-2"
                                        :errorshas="__($errors->has('collectingMyProduct.signaHari'))" :disabled="$disabledPropertyRjStatus" wire:model="collectingMyProduct.signaHari"
                                        data-seq="3"
                                        x-on:keydown.enter.prevent="
                                            $el.closest('.flex')?.querySelector('[data-seq=&quot;4&quot;]')?.focus()
                                        " />
                                    @error('collectingMyProduct.signaHari')
                                        <x-input-error :messages="$message" />
                                    @enderror
                                </div>

                                {{-- Catatan Khusus --}}
                                <div class="basis-3/6">
                                    <x-input-label for="collectingMyProduct.catatanKhusus" :value="__('Catatan Khusus')"
                                        :required="true" />
                                    <x-text-input id="collectingMyProduct.catatanKhusus" placeholder="Catatan Khusus"
                                        class="mt-1 ml-2" :errorshas="__($errors->has('collectingMyProduct.catatanKhusus'))" :disabled="$disabledPropertyRjStatus"
                                        wire:model="collectingMyProduct.catatanKhusus" data-seq="4"
                                        x-on:keydown.enter.prevent="
                                            $wire.insertProduct();
                                            $nextTick(() => { $el.closest('.flex')?.querySelector('[data-seq=&quot;1&quot;]')?.focus() })
                                        " />
                                    @error('collectingMyProduct.catatanKhusus')
                                        <x-input-error :messages="$message" />
                                    @enderror
                                </div>

                                {{-- Hapus draft --}}
                                <div class="basis-1/6">
                                    <x-input-label for="" :value="__('Hapus')" :required="true" />
                                    <x-alternative-button class="inline-flex ml-2"
                                        wire:click.prevent="resetcollectingMyProduct()">
                                        <svg class="w-5 h-5 text-gray-800 dark:text-white" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                                            <path
                                                d="M17 4h-4V2a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v2H1a1 1 0 0 0 0 2h1v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6h1a1 1 0 1 0 0-2ZM7 2h4v2H7V2Zm1 14a1 1 0 1 1-2 0V8a1 1 0 0 1 2 0v8Zm4 0a1 1 0 0 1-2 0V8a1 1 0 0 1 2 0v8Z" />
                                        </svg>
                                    </x-alternative-button>
                                </div>
                            </div>
                        @endif
                    @endrole

                    {{-- Tabel item tersimpan --}}
                    <div class="flex flex-col my-2">
                        <div class="overflow-x-auto rounded-lg">
                            <div class="inline-block min-w-full align-middle">
                                <div class="overflow-hidden shadow sm:rounded-lg">
                                    <table class="w-full text-sm text-left text-gray-500 table-auto dark:text-gray-400">
                                        <thead
                                            class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                                            <tr>
                                                <th scope="col" class="px-4 py-3">
                                                    <x-sort-link :active="false" role="button"
                                                        href="#">NonRacikan</x-sort-link>
                                                </th>
                                                <th scope="col" class="px-4 py-3 ">
                                                    <x-sort-link :active="false" role="button"
                                                        href="#">Obat</x-sort-link>
                                                </th>
                                                <th scope="col" class="px-4 py-3 ">
                                                    <x-sort-link :active="false" role="button"
                                                        href="#">Jumlah</x-sort-link>
                                                </th>
                                                <th scope="col" class="px-4 py-3 ">
                                                    <x-sort-link :active="false" role="button"
                                                        href="#">Signa</x-sort-link>
                                                </th>
                                                <th scope="col" class="w-8 px-4 py-3 text-center">Action</th>
                                            </tr>
                                        </thead>

                                        <tbody class="bg-white dark:bg-gray-800">
                                            @isset($dataDaftarUgd['eresep'])
                                                @foreach ($dataDaftarUgd['eresep'] as $key => $eresep)
                                                    <tr class="border-b group dark:border-gray-700">
                                                        <td
                                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                                            {{ $eresep['jenisKeterangan'] }}
                                                        </td>

                                                        <td
                                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                                            {{ $eresep['productName'] }}
                                                        </td>

                                                        {{-- Qty (data-seq=1) --}}
                                                        <td
                                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                                            <x-text-input placeholder="Jml" class="w-24"
                                                                :disabled="$disabledPropertyRjStatus"
                                                                wire:model="dataDaftarUgd.eresep.{{ $key }}.qty"
                                                                data-seq="1"
                                                                x-on:keydown.enter.prevent="
                                                                    $el.closest('tr')?.querySelector('[data-seq=&quot;2&quot;]')?.focus()
                                                                " />
                                                        </td>

                                                        {{-- Signa (2,3,4) --}}
                                                        <td
                                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                                            <div class="flex items-baseline space-x-2">
                                                                <div class="basis-[20%]">
                                                                    <x-text-input placeholder="Signa1" class="mt-1 ml-2"
                                                                        :disabled="$disabledPropertyRjStatus"
                                                                        wire:model="dataDaftarUgd.eresep.{{ $key }}.signaX"
                                                                        data-seq="2"
                                                                        x-on:keydown.enter.prevent="
                                                                            $el.closest('tr')?.querySelector('[data-seq=&quot;3&quot;]')?.focus()
                                                                        " />
                                                                </div>

                                                                <div class="basis-[4%]">
                                                                    <span class="text-sm">dd</span>
                                                                </div>

                                                                <div class="basis-[20%]">
                                                                    <x-text-input placeholder="Signa2" class="mt-1 ml-2"
                                                                        :disabled="$disabledPropertyRjStatus"
                                                                        wire:model="dataDaftarUgd.eresep.{{ $key }}.signaHari"
                                                                        data-seq="3"
                                                                        x-on:keydown.enter.prevent="
                                                                            $el.closest('tr')?.querySelector('[data-seq=&quot;4&quot;]')?.focus()
                                                                        " />
                                                                </div>

                                                                <div class="flex-1">
                                                                    <x-text-input placeholder="Catatan Khusus"
                                                                        class="mt-1 ml-2" :disabled="$disabledPropertyRjStatus"
                                                                        wire:model="dataDaftarUgd.eresep.{{ $key }}.catatanKhusus"
                                                                        data-seq="4"
                                                                        x-on:keydown.enter.prevent="
                                                                            $wire.updateProductRow({{ $key }});
                                                                            $nextTick(() => {
                                                                                $el.closest('tr')?.querySelector('[data-seq=&quot;1&quot;]')?.focus()
                                                                            })
                                                                        " />
                                                                </div>
                                                            </div>
                                                        </td>

                                                        {{-- Action --}}
                                                        <td
                                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                                            @role(['Dokter', 'Admin'])
                                                                <x-alternative-button class="inline-flex" :disabled="$disabledPropertyRjStatus"
                                                                    wire:click.prevent="removeProduct('{{ $eresep['rjObatDtl'] }}')">
                                                                    <svg class="w-5 h-5 text-gray-800 dark:text-white"
                                                                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                                        fill="currentColor" viewBox="0 0 18 20">
                                                                        <path
                                                                            d="M17 4h-4V2a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v2H1a1 1 0 0 0 0 2h1v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6h1a1 1 0 1 0 0-2ZM7 2h4v2H7V2Zm1 14a1 1 0 1 1-2 0V8a1 1 0 0 1 2 0v8Z" />
                                                                    </svg>
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
                        {{-- End Tabel --}}
                    </div>
                    {{-- ///////////////////////////////// --}}
                </div>
            </div>
        </div>
    </div>
</div>
