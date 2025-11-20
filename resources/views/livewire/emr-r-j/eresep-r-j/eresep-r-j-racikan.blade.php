<div>

    @php
        $disabledProperty = true;
        $disabledPropertyRjStatus = $rjStatusRef == 'A' ? false : true;
    @endphp

    <div class="w-full mb-1 ">
        <div id="TransaksiRawatJalan" class="p-2">
            <div class="p-2 rounded-lg bg-gray-50">
                <div id="TransaksiRawatJalan" class="px-4">
                    <x-input-label for="" :value="__('Racikan')" :required="false" class="pt-2 sm:text-xl" />

                    @role(['Dokter', 'Admin'])
                        {{-- ================== FORM RACIKAN (STEP 1: pilih no racikan + LOV obat) ================== --}}
                        @if (!$collectingMyProduct)
                            <div class="grid grid-cols-8 gap-4" x-data="{ selecteddataProductLovIndex: @entangle('selecteddataProductLovIndex') }" x-init="$watch('selecteddataProductLovIndex', (val) => {
                                const list = $el.querySelector('[data-lov-list]');
                                if (!list || typeof val !== 'number' || val < 0) return;
                                const items = list.querySelectorAll('li');
                                items[val]?.scrollIntoView({ block: 'nearest' });
                            });"
                                x-on:click.window="
                                    if (!$el.contains($event.target)) {
                                        $wire.resetdataProductLov()
                                    }
                                "
                                data-lov-wrapper>
                                {{-- No Racikan --}}
                                <div class="col-span-1">
                                    <x-input-label for="collectingMyProduct.noRacikan" :value="__('Racikan')"
                                        :required="true" />
                                    <div>
                                        <x-text-input id="collectingMyProduct.noRacikan" placeholder="Racikan"
                                            class="mt-1 ml-2" :errorshas="__($errors->has('collectingMyProduct.noRacikan'))" :disabled="$disabledPropertyRjStatus" wire:model="noRacikan"
                                            data-racikan-no
                                            x-on:keydown.enter.prevent="
                                                $el.closest('[data-lov-wrapper]')?.querySelector('[data-lov-search]')?.focus()
                                            " />
                                        @error('collectingMyProduct.noRacikan')
                                            <x-input-error :messages="$message" />
                                        @enderror
                                    </div>
                                </div>

                                {{-- LOV Nama Obat --}}
                                <div class="col-span-7">
                                    <x-input-label for="dataProductLovSearch" :value="__('Nama Obat')" :required="true" />

                                    <div>
                                        <x-text-input id="dataProductLovSearchMain" placeholder="Nama Obat"
                                            class="mt-1 ml-2" :errorshas="__($errors->has('dataProductLovSearchMain'))" :disabled="$disabledPropertyRjStatus"
                                            wire:model.debounce.500ms="dataProductLovSearch"
                                            x-on:keyup.escape="$wire.resetdataProductLov()"
                                            x-on:keydown.down.prevent="$wire.selectNextdataProductLov()"
                                            x-on:keydown.up.prevent="$wire.selectPreviousdataProductLov()"
                                            x-on:keydown.enter.prevent="
                                                if (($wire.dataProductLov?.length ?? 0) > 0 && (selecteddataProductLovIndex ?? -1) >= 0) {
                                                    $wire.enterMydataProductLov(selecteddataProductLovIndex)
                                                }
                                            "
                                            x-init="$nextTick(() => { if (!$el.disabled) $el.focus() })" data-lov-search />

                                        {{-- Dropdown LOV --}}
                                        <div class="py-2 mt-1 overflow-y-auto bg-white border rounded-md shadow-lg max-h-64"
                                            x-show="$wire.dataProductLovSearch.length > 3 && $wire.dataProductLov.length > 0"
                                            data-lov-list wire:ignore.self>
                                            @foreach ($dataProductLov as $key => $lov)
                                                <li wire:key="dataProductLov-{{ $lov['product_id'] ?? $key }}">
                                                    <x-dropdown-link wire:click="setMydataProductLov('{{ $key }}')"
                                                        class="text-base font-normal {{ $key === $selecteddataProductLovIndex ? 'bg-gray-100 outline-none' : '' }}">
                                                        <div>
                                                            {{ $lov['product_name'] . ' / ' . number_format($lov['sales_price']) }}
                                                        </div>
                                                        <div class="text-xs">
                                                            {{ '(' . $lov['product_content'] . ')' }}
                                                        </div>
                                                    </x-dropdown-link>
                                                </li>
                                            @endforeach
                                        </div>

                                        {{-- LOV exceptions --}}
                                        @if (strlen($dataProductLovSearch) > 0 && strlen($dataProductLovSearch) < 3 && count($dataProductLov) == 0)
                                            <div class="w-full p-2 text-sm text-center text-gray-500 dark:text-gray-400">
                                                {{ 'Masukkan minimal 3 karakter' }}
                                            </div>
                                        @elseif(strlen($dataProductLovSearch) >= 3 && count($dataProductLov) == 0)
                                            <div class="w-full p-2 text-sm text-center text-gray-500 dark:text-gray-400">
                                                {{ 'Data Tidak ditemukan' }}
                                            </div>
                                        @endif

                                        @error('dataProductLovSearch')
                                            <x-input-error :messages="$message" />
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- ================== FORM RACIKAN (STEP 2: detail racikan) ================== --}}
                        @if ($collectingMyProduct)
                            <div class="inline-flex space-x-0.5" x-data data-form="racikanHeader">
                                {{-- No Racikan --}}
                                <div class="basis-1/4">
                                    <x-input-label for="collectingMyProduct.noRacikan" :value="__('Racikan')"
                                        :required="true" />
                                    <div>
                                        <x-text-input id="collectingMyProduct.noRacikan" placeholder="Racikan"
                                            class="mt-1 ml-2" :errorshas="__($errors->has('collectingMyProduct.noRacikan'))" :disabled="$disabledPropertyRjStatus" wire:model="noRacikan"
                                            data-seq="0"
                                            x-on:keydown.enter.prevent="
                                                $el.closest('[data-form=&quot;racikanHeader&quot;]')?.querySelector('[data-seq=&quot;1&quot;]')?.focus()
                                            " />
                                        @error('collectingMyProduct.noRacikan')
                                            <x-input-error :messages="$message" />
                                        @enderror
                                    </div>
                                </div>

                                {{-- hidden Kode Obat --}}
                                <div class="hidden">
                                    <x-input-label for="collectingMyProduct.productId" :value="__('Kode Obat')"
                                        :required="true" />
                                    <div>
                                        <x-text-input id="collectingMyProduct.productId" placeholder="Kode Obat"
                                            class="mt-1 ml-2" :errorshas="__($errors->has('collectingMyProduct.productId'))" :disabled="true"
                                            wire:model="collectingMyProduct.productId" />
                                        @error('collectingMyProduct.productId')
                                            <x-input-error :messages="$message" />
                                        @enderror
                                    </div>
                                </div>

                                {{-- Nama Obat (readonly) --}}
                                <div class="basis-3/6">
                                    <x-input-label for="collectingMyProduct.productName" :value="__('Nama Obat')"
                                        :required="true" />
                                    <div>
                                        <x-text-input id="collectingMyProduct.productName" placeholder="Nama Obat"
                                            class="mt-1 ml-2" :errorshas="__($errors->has('collectingMyProduct.productName'))" :disabled="true"
                                            wire:model="collectingMyProduct.productName" />
                                        @error('collectingMyProduct.productName')
                                            <x-input-error :messages="$message" />
                                        @enderror
                                    </div>
                                </div>

                                {{-- Dosis --}}
                                <div class="basis-1/4">
                                    <x-input-label for="collectingMyProduct.dosis" :value="__('Dosis')" :required="true" />
                                    <div>
                                        <x-text-input id="collectingMyProduct.dosis" placeholder="dosis" class="mt-1 ml-2"
                                            :errorshas="__($errors->has('collectingMyProduct.dosis'))" :disabled="$disabledPropertyRjStatus" wire:model="collectingMyProduct.dosis"
                                            data-seq="1" x-init="$nextTick(() => { if (!$el.disabled) $el.focus() })"
                                            x-on:keydown.enter.prevent="
                                                $el.closest('[data-form=&quot;racikanHeader&quot;]')?.querySelector('[data-seq=&quot;2&quot;]')?.focus()
                                            " />
                                        @error('collectingMyProduct.dosis')
                                            <x-input-error :messages="$message" />
                                        @enderror
                                    </div>
                                </div>

                                {{-- Jml Racikan --}}
                                <div class="basis-2/4">
                                    <x-input-label for="collectingMyProduct.qty" :value="__('Jml Racikan')" :required="false" />
                                    <div>
                                        <x-text-input id="collectingMyProduct.qty" placeholder="Jml Racikan"
                                            class="mt-1 ml-2" :errorshas="__($errors->has('collectingMyProduct.qty'))" :disabled="$disabledPropertyRjStatus"
                                            wire:model="collectingMyProduct.qty" data-seq="2"
                                            x-on:keydown.enter.prevent="
                                                $el.closest('[data-form=&quot;racikanHeader&quot;]')?.querySelector('[data-seq=&quot;3&quot;]')?.focus()
                                            " />
                                        @error('collectingMyProduct.qty')
                                            <x-input-error :messages="$message" />
                                        @enderror
                                    </div>
                                </div>

                                {{-- Catatan --}}
                                <div class="basis-1/4">
                                    <x-input-label for="collectingMyProduct.catatan" :value="__('Catatan')"
                                        :required="false" />
                                    <div>
                                        <x-text-input id="collectingMyProduct.catatan" placeholder="Catatan"
                                            class="mt-1 ml-2" :errorshas="__($errors->has('collectingMyProduct.catatan'))" :disabled="$disabledPropertyRjStatus"
                                            wire:model="collectingMyProduct.catatan" data-seq="3"
                                            x-on:keydown.enter.prevent="
                                                $el.closest('[data-form=&quot;racikanHeader&quot;]')?.querySelector('[data-seq=&quot;4&quot;]')?.focus()
                                            " />
                                        @error('collectingMyProduct.catatan')
                                            <x-input-error :messages="$message" />
                                        @enderror
                                    </div>
                                </div>

                                {{-- Signa (catatanKhusus) --}}
                                <div class="basis-3/4">
                                    <x-input-label for="collectingMyProduct.catatanKhusus" :value="__('Signa')"
                                        :required="false" />
                                    <div>
                                        <x-text-input id="collectingMyProduct.catatanKhusus" placeholder="Signa"
                                            class="mt-1 ml-2" :errorshas="__($errors->has('collectingMyProduct.catatanKhusus'))" :disabled="$disabledPropertyRjStatus"
                                            wire:model="collectingMyProduct.catatanKhusus" data-seq="4"
                                            x-on:keydown.enter.prevent="
                                                $wire.insertProduct();
                                                requestAnimationFrame(() => {
                                                    document.querySelector('[data-lov-search]')?.focus();
                                                })
                                            " />
                                        @error('collectingMyProduct.catatanKhusus')
                                            <x-input-error :messages="$message" />
                                        @enderror
                                    </div>
                                </div>

                                {{-- Hapus draft --}}
                                <div class="basis-1/4">
                                    <x-input-label for="" :value="__('Hapus')" :required="false" />
                                    <x-alternative-button class="inline-flex ml-2" :disabled="$disabledPropertyRjStatus"
                                        x-on:click.prevent="
                                            $wire.resetcollectingMyProduct()
                                                .then(() => {
                                                    requestAnimationFrame(() => {
                                                        document.querySelector('[data-lov-search]')?.focus()
                                                    })
                                                })
                                        ">
                                        <svg class="w-5 h-5 text-gray-800 dark:text-white" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                                            <path
                                                d="M17 4h-4V2a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v2H1a1 1 0 0 0 0 2h1v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6h1a1 1 0 1 0 0-2ZM7 2h4v2H7V2Zm1 14a1 1 0 1 1-2 0V8a1 1 0 0 1 2 0v8Z" />
                                        </svg>
                                    </x-alternative-button>
                                </div>

                            </div>
                        @endif
                    @endrole

                    {{-- ================== TABEL RACIKAN ================== --}}
                    <div class="flex flex-col my-2">
                        <div class="overflow-x-auto rounded-lg">
                            <div class="inline-block min-w-full align-middle">
                                <div class="overflow-hidden shadow sm:rounded-lg">
                                    <table
                                        class="w-full text-sm text-left text-gray-500 table-auto dark:text-gray-400">
                                        <thead
                                            class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                                            <tr>
                                                <th scope="col" class="px-4 py-3">
                                                    <x-sort-link :active="false" wire:click.prevent=""
                                                        role="button" href="#">
                                                        Racikan
                                                    </x-sort-link>
                                                </th>
                                                <th scope="col" class="px-4 py-3 ">
                                                    <x-sort-link :active="false" wire:click.prevent=""
                                                        role="button" href="#">
                                                        Obat
                                                    </x-sort-link>
                                                </th>
                                                <th scope="col" class="px-4 py-3 ">
                                                    <x-sort-link :active="false" wire:click.prevent=""
                                                        role="button" href="#">
                                                        Dosis
                                                    </x-sort-link>
                                                </th>
                                                <th scope="col" class="px-4 py-3 ">
                                                    <x-sort-link :active="false" wire:click.prevent=""
                                                        role="button" href="#">
                                                        Jml Racikan
                                                    </x-sort-link>
                                                </th>
                                                <th scope="col" class="px-4 py-3 ">
                                                    <x-sort-link :active="false" wire:click.prevent=""
                                                        role="button" href="#">
                                                        Catatan
                                                    </x-sort-link>
                                                </th>
                                                <th scope="col" class="px-4 py-3 ">
                                                    <x-sort-link :active="false" wire:click.prevent=""
                                                        role="button" href="#">
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
                                                @php $myPreviousRow = ''; @endphp

                                                @foreach ($dataDaftarPoliRJ['eresepRacikan'] as $key => $eresep)
                                                    @isset($eresep['jenisKeterangan'])
                                                        @php
                                                            $myRacikanBorder =
                                                                $myPreviousRow !== $eresep['noRacikan']
                                                                    ? 'border-t-2 '
                                                                    : '';
                                                        @endphp

                                                        <tr class="{{ $myRacikanBorder }} group">
                                                            <td
                                                                class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                                                {{ $eresep['jenisKeterangan'] . '  (' . $eresep['noRacikan'] . ')' }}
                                                            </td>

                                                            <td
                                                                class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                                                {{ $eresep['productName'] }}
                                                            </td>

                                                            {{-- Dosis (seq=1) --}}
                                                            <td
                                                                class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                                                <x-text-input placeholder="dosis" :disabled="$disabledPropertyRjStatus"
                                                                    wire:model="dataDaftarPoliRJ.eresepRacikan.{{ $key }}.dosis"
                                                                    data-seq="1"
                                                                    x-on:keydown.enter.prevent="
                                                                        $el.closest('tr')?.querySelector('[data-seq=&quot;2&quot;]')?.focus()
                                                                    " />
                                                            </td>

                                                            {{-- Jml Racikan (seq=2) --}}
                                                            <td
                                                                class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                                                <x-text-input placeholder="Jml Racikan" :disabled="$disabledPropertyRjStatus"
                                                                    wire:model="dataDaftarPoliRJ.eresepRacikan.{{ $key }}.qty"
                                                                    data-seq="2"
                                                                    x-on:keydown.enter.prevent="
                                                                        $el.closest('tr')?.querySelector('[data-seq=&quot;3&quot;]')?.focus()
                                                                    " />
                                                            </td>

                                                            {{-- Catatan (seq=3) --}}
                                                            <td
                                                                class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                                                <x-text-input placeholder="Catatan" :disabled="$disabledPropertyRjStatus"
                                                                    wire:model="dataDaftarPoliRJ.eresepRacikan.{{ $key }}.catatan"
                                                                    data-seq="3"
                                                                    x-on:keydown.enter.prevent="
                                                                        $el.closest('tr')?.querySelector('[data-seq=&quot;4&quot;]')?.focus()
                                                                    " />
                                                            </td>

                                                            {{-- Signa (seq=4) --}}
                                                            <td
                                                                class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                                                <x-text-input placeholder="Signa" :disabled="$disabledPropertyRjStatus"
                                                                    wire:model="dataDaftarPoliRJ.eresepRacikan.{{ $key }}.catatanKhusus"
                                                                    data-seq="4"
                                                                    x-on:keydown.enter.prevent="
                                                                        $wire.updateProduct(
                                                                            '{{ $eresep['rjObatDtl'] ?? null }}',
                                                                            '{{ $eresep['dosis'] ?? null }}',
                                                                            '{{ $eresep['qty'] ?? null }}',
                                                                            '{{ $eresep['catatan'] ?? null }}',
                                                                            '{{ $eresep['catatanKhusus'] ?? null }}'
                                                                        );
                                                                        $nextTick(() => {
                                                                            $el.closest('tr')?.querySelector('[data-seq=&quot;1&quot;]')?.focus()
                                                                        })
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
                                                                                d="M17 4h-4V2a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v2H1a1 1 0 0 0 0 2h1v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6h1a1 1 0 1 0 0-2ZM7 2h4v2H7V2Zm1 14a1 1 0 1 1-2 0V8a1 1 0 0 1 2 0v8Z" />
                                                                        </svg>
                                                                    </x-alternative-button>
                                                                @endrole
                                                            </td>
                                                        </tr>

                                                        @php
                                                            $myPreviousRow = $eresep['noRacikan'];
                                                        @endphp
                                                    @endisset
                                                @endforeach
                                            @endisset
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- ================== END TABEL RACIKAN ================== --}}
                </div>
            </div>
        </div>
    </div>
</div>
