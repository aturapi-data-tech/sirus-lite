<div>
    @php
        $disabledPropertyRjStatus = $rjStatusRef == 'A' ? false : true;
    @endphp

    <div class="w-full mb-1">
        <div id="TransaksiRawatJalan" class="p-2">
            <div class="p-2 rounded-lg bg-gray-50">
                <div id="TransaksiRawatJalan" class="px-4">
                    <x-input-label for="" :value="__('Racikan')" :required="false" class="pt-2 sm:text-xl" />

                    @role(['Dokter', 'Admin'])

                        {{-- ========== LOV pakai include (sama seperti Non-Racikan) ========== --}}
                        @if (!$collectingMyProduct)
                            <div class="grid grid-cols-8 gap-4">
                                {{-- No Racikan --}}
                                <div class="col-span-1">
                                    <x-input-label for="collectingMyProduct.noRacikan" :value="__('Racikan')"
                                        :required="true" />
                                    <x-text-input id="collectingMyProduct.noRacikan" placeholder="Racikan" class="mt-1 ml-2"
                                        :errorshas="__($errors->has('collectingMyProduct.noRacikan'))" :disabled="$disabledPropertyRjStatus" wire:model="noRacikan" />
                                    @error('collectingMyProduct.noRacikan')
                                        <x-input-error :messages="$message" />
                                    @enderror
                                </div>

                                {{-- INCLUDE: LOV Product reusable --}}
                                <div class="col-span-7" x-data
                                    x-on:focus-lov-product.window="$nextTick(() => document.getElementById('dataProductLovSearch')?.focus())">
                                    @include('livewire.component.l-o-v.list-of-value-product.list-of-value-product')
                                </div>
                            </div>
                        @endif

                        {{-- ========== Form draft item Racikan ========== --}}
                        @if ($collectingMyProduct)
                            <div class="inline-flex w-full space-x-2" x-data>
                                {{-- No Racikan --}}
                                <div class="basis-1/6">
                                    <x-input-label for="collectingMyProduct.noRacikan" :value="__('Racikan')"
                                        :required="true" />
                                    <x-text-input id="collectingMyProduct.noRacikan" placeholder="Racikan" class="mt-1 ml-2"
                                        :errorshas="__($errors->has('collectingMyProduct.noRacikan'))" :disabled="$disabledPropertyRjStatus" wire:model="noRacikan" />
                                    @error('collectingMyProduct.noRacikan')
                                        <x-input-error :messages="$message" />
                                    @enderror
                                </div>

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

                                {{-- Nama obat (readonly) --}}
                                <div class="basis-2/6">
                                    <x-input-label for="collectingMyProduct.productName" :value="__('Nama Obat')"
                                        :required="true" />
                                    <x-text-input id="collectingMyProduct.productName" placeholder="Nama Obat"
                                        class="mt-1 ml-2" :errorshas="__($errors->has('collectingMyProduct.productName'))" :disabled="true"
                                        wire:model="collectingMyProduct.productName" />
                                    @error('collectingMyProduct.productName')
                                        <x-input-error :messages="$message" />
                                    @enderror
                                </div>

                                {{-- Dosis (seq 1) --}}
                                <div class="basis-1/6">
                                    <x-input-label for="collectingMyProduct.dosis" :value="__('Dosis')" :required="true" />
                                    <x-text-input id="collectingMyProduct.dosis" placeholder="Dosis" class="mt-1 ml-2"
                                        :errorshas="__($errors->has('collectingMyProduct.dosis'))" :disabled="$disabledPropertyRjStatus" wire:model="collectingMyProduct.dosis"
                                        data-seq="1"
                                        x-on:keydown.enter.prevent="$el.closest('.inline-flex')?.querySelector('[data-seq=&quot;2&quot;]')?.focus()" />
                                    @error('collectingMyProduct.dosis')
                                        <x-input-error :messages="$message" />
                                    @enderror
                                </div>

                                {{-- Qty (seq 2) --}}
                                <div class="basis-1/6">
                                    <x-input-label for="collectingMyProduct.qty" :value="__('Jml Racikan')" :required="false" />
                                    <x-text-input id="collectingMyProduct.qty" placeholder="Jml Racikan" class="mt-1 ml-2"
                                        :errorshas="__($errors->has('collectingMyProduct.qty'))" :disabled="$disabledPropertyRjStatus" wire:model="collectingMyProduct.qty"
                                        data-seq="2"
                                        x-on:keydown.enter.prevent="$el.closest('.inline-flex')?.querySelector('[data-seq=&quot;3&quot;]')?.focus()" />
                                    @error('collectingMyProduct.qty')
                                        <x-input-error :messages="$message" />
                                    @enderror
                                </div>

                                {{-- Catatan (seq 3) --}}
                                <div class="basis-1/6">
                                    <x-input-label for="collectingMyProduct.catatan" :value="__('Catatan')"
                                        :required="false" />
                                    <x-text-input id="collectingMyProduct.catatan" placeholder="Catatan" class="mt-1 ml-2"
                                        :errorshas="__($errors->has('collectingMyProduct.catatan'))" :disabled="$disabledPropertyRjStatus" wire:model="collectingMyProduct.catatan"
                                        data-seq="3"
                                        x-on:keydown.enter.prevent="$el.closest('.inline-flex')?.querySelector('[data-seq=&quot;4&quot;]')?.focus()" />
                                    @error('collectingMyProduct.catatan')
                                        <x-input-error :messages="$message" />
                                    @enderror
                                </div>

                                {{-- Signa (catatanKhusus) (seq 4) --}}
                                <div class="basis-2/6">
                                    <x-input-label for="collectingMyProduct.catatanKhusus" :value="__('Signa')"
                                        :required="false" />
                                    <x-text-input id="collectingMyProduct.catatanKhusus" placeholder="Signa"
                                        class="mt-1 ml-2" :errorshas="__($errors->has('collectingMyProduct.catatanKhusus'))" :disabled="$disabledPropertyRjStatus"
                                        wire:model="collectingMyProduct.catatanKhusus" data-seq="4"
                                        x-on:keydown.enter.prevent="
                                                    $wire.insertProduct();
                                                    $nextTick(() => { $el.closest('.inline-flex')?.querySelector('[data-seq=&quot;1&quot;]')?.focus() })
                                                  " />
                                    @error('collectingMyProduct.catatanKhusus')
                                        <x-input-error :messages="$message" />
                                    @enderror
                                </div>

                                {{-- Hapus draft --}}
                                <div class="basis-1/6">
                                    <x-input-label for="" :value="__('Hapus')" :required="false" />
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

                    {{-- ========== Tabel Racikan Tersimpan ========== --}}
                    <div class="flex flex-col my-2">
                        <div class="overflow-x-auto rounded-lg">
                            <div class="inline-block min-w-full align-middle">
                                <div class="overflow-hidden shadow sm:rounded-lg">
                                    <table class="w-full text-sm text-left text-gray-500 table-auto dark:text-gray-400">
                                        <thead
                                            class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                                            <tr>
                                                <th class="px-4 py-3"><x-sort-link :active="false" href="#"
                                                        role="button">Racikan</x-sort-link></th>
                                                <th class="px-4 py-3 "><x-sort-link :active="false" href="#"
                                                        role="button">Obat</x-sort-link></th>
                                                <th class="px-4 py-3 "><x-sort-link :active="false" href="#"
                                                        role="button">Dosis</x-sort-link></th>
                                                <th class="px-4 py-3 "><x-sort-link :active="false" href="#"
                                                        role="button">Jml Racikan</x-sort-link></th>
                                                <th class="px-4 py-3 "><x-sort-link :active="false" href="#"
                                                        role="button">Catatan</x-sort-link></th>
                                                <th class="px-4 py-3 "><x-sort-link :active="false" href="#"
                                                        role="button">Signa</x-sort-link></th>
                                                <th class="w-8 px-4 py-3 text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-gray-800">
                                            @isset($dataDaftarUgd['eresepRacikan'])
                                                @php $myPreviousRow = ''; @endphp
                                                @foreach ($dataDaftarUgd['eresepRacikan'] as $key => $eresep)
                                                    @php
                                                        $myRacikanBorder =
                                                            $myPreviousRow !== $eresep['noRacikan']
                                                                ? 'border-t-2 '
                                                                : '';
                                                    @endphp
                                                    <tr class="{{ $myRacikanBorder }} group">
                                                        <td
                                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                                            {{ $eresep['jenisKeterangan'] . ' (' . $eresep['noRacikan'] . ')' }}
                                                        </td>

                                                        <td
                                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                                            {{ $eresep['productName'] }}
                                                        </td>

                                                        {{-- Dosis (seq 1) --}}
                                                        <td
                                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                                            <x-text-input placeholder="Dosis" :disabled="$disabledPropertyRjStatus"
                                                                wire:model="dataDaftarUgd.eresepRacikan.{{ $key }}.dosis"
                                                                data-seq="1"
                                                                x-on:keydown.enter.prevent="$el.closest('tr')?.querySelector('[data-seq=&quot;2&quot;]')?.focus()" />
                                                        </td>

                                                        {{-- Qty (seq 2) --}}
                                                        <td
                                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                                            <x-text-input placeholder="Jml Racikan" :disabled="$disabledPropertyRjStatus"
                                                                wire:model="dataDaftarUgd.eresepRacikan.{{ $key }}.qty"
                                                                data-seq="2"
                                                                x-on:keydown.enter.prevent="$el.closest('tr')?.querySelector('[data-seq=&quot;3&quot;]')?.focus()" />
                                                        </td>

                                                        {{-- Catatan (seq 3) --}}
                                                        <td
                                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                                            <x-text-input placeholder="Catatan" :disabled="$disabledPropertyRjStatus"
                                                                wire:model="dataDaftarUgd.eresepRacikan.{{ $key }}.catatan"
                                                                data-seq="3"
                                                                x-on:keydown.enter.prevent="$el.closest('tr')?.querySelector('[data-seq=&quot;4&quot;]')?.focus()" />
                                                        </td>

                                                        {{-- Signa (catatanKhusus) (seq 4) --}}
                                                        <td
                                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                                            <x-text-input placeholder="Signa" :disabled="$disabledPropertyRjStatus"
                                                                wire:model="dataDaftarUgd.eresepRacikan.{{ $key }}.catatanKhusus"
                                                                data-seq="4"
                                                                x-on:keydown.enter.prevent="
                                                                    $wire.updateProductRow({{ $key }});
                                                                    $nextTick(() => { $el.closest('tr')?.querySelector('[data-seq=&quot;1&quot;]')?.focus() })
                                                                " />
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
                                                                            d="M17 4h-4V2a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v2H1a1 1 0 0 0 0 2h1v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6h1a1 1 0 1 0 0-2ZM7 2h4v2H7V2Zm1 14a1 1 0 1 1-2 0V8a1 1 0 0 1 2 0v8Zm4 0a1 1 0 0 1-2 0V8a1 1 0 0 1 2 0v8Z" />
                                                                    </svg>
                                                                </x-alternative-button>
                                                            @endrole
                                                        </td>
                                                    </tr>
                                                    @php $myPreviousRow = $eresep['noRacikan']; @endphp
                                                @endforeach
                                            @endisset
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div> {{-- /px-4 --}}
            </div>
        </div>
    </div>
</div>
