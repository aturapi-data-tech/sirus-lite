<div>

    @php
        $disabledProperty = true;
        $disabledPropertyRjStatus = $rjStatusRef == 'A' ? false : true;
    @endphp

    {{-- jika anamnesa kosong ngak usah di render --}}
    <div class="w-full mb-1 ">
        <div id="TransaksiRawatJalan" class="p-2">
            <div class="p-2 rounded-lg bg-gray-50">
                <div id="TransaksiRawatJalan" class="px-4">
                    <x-input-label for="" :value="__('Non Racikan')" :required="false" class="pt-2 sm:text-xl" />

                    {{-- ⬇⬇⬇ Peringatan Obat Kronis --}}
                    @if ($isChronic && ($warnRepeatUnder30d || $warnOverMaxQty))
                        <div class="p-3 my-2 text-sm border rounded bg-amber-50 border-amber-300 text-amber-900">
                            <div class="font-semibold">Peringatan Obat Kronis</div>
                            <ul class="pl-5 mt-1 space-y-1 list-disc">
                                @if ($warnRepeatUnder30d)
                                    <li>
                                        Pengambilan obat terakhir:
                                        <span class="font-medium">{{ $lastTebusDate }}</span>
                                        ({{ $daysSince }} hari lalu).
                                    </li>
                                @endif
                                @if ($warnOverMaxQty)
                                    <li>
                                        Pemberian dalam 30 hari terakhir adalah:
                                        <span class="font-medium">
                                            {{ $qty30d + (float) data_get($collectingMyProduct, 'qty', 0) }}
                                        </span>
                                        / Disarankan maksimal pemberian obat :
                                        <span class="font-medium">{{ $maxQty }}</span>.
                                    </li>
                                @endif
                            </ul>
                            @if ($kronisMessage)
                                <div class="mt-2 text-xs opacity-80">{{ $kronisMessage }}</div>
                            @endif
                        </div>
                    @endif
                    {{-- ⬆⬆⬆ end warning --}}

                    @role(['Dokter', 'Admin'])
                        {{-- ================== LOV NAMA OBAT ================== --}}
                        @if (!$collectingMyProduct)
                            <div>
                                <x-input-label for="dataProductLovSearch" :value="__('Nama Obat')" :required="true" />

                                {{-- Lov dataProductLov --}}
                                <div x-data="{ selecteddataProductLovIndex: @entangle('selecteddataProductLovIndex') }" x-init="$watch('selecteddataProductLovIndex', (val) => {
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
                                    <x-text-input id="dataProductLovSearchMain" placeholder="Nama Obat" class="mt-1 ml-2"
                                        :errorshas="__($errors->has('dataProductLovSearchMain'))" :disabled="$disabledPropertyRjStatus" wire:model.debounce.500ms="dataProductLovSearch"
                                        x-on:keyup.escape="$wire.resetdataProductLov()"
                                        x-on:keydown.down.prevent="$wire.selectNextdataProductLov()"
                                        x-on:keydown.up.prevent="$wire.selectPreviousdataProductLov()"
                                        x-on:keydown.enter.prevent="
                                            if (($wire.dataProductLov?.length ?? 0) > 0 && (selecteddataProductLovIndex ?? -1) >= 0) {
                                                $wire.enterMydataProductLov(selecteddataProductLovIndex)
                                            }
                                        "
                                        x-init="$nextTick(() => { if (!$el.disabled) $el.focus() })" data-lov-search />

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
                                </div>
                                {{-- Lov dataProductLov --}}
                            </div>
                        @endif
                        {{-- ============== END LOV NAMA OBAT ================= --}}

                        @if ($collectingMyProduct)
                            {{-- collectingMyProduct / obat --}}
                            <div class="flex items-baseline space-x-2" x-data data-form="rjNonRacikHeader">

                                {{-- hidden productId --}}
                                <div class="hidden">
                                    <x-input-label for="collectingMyProduct.productId" :value="__('Kode Obat')"
                                        :required="true" />
                                    <div>
                                        <x-text-input id="collectingMyProduct.productId" placeholder="Kode Obat"
                                            class="mt-1 ml-2" :errorshas="__($errors->has('collectingMyProduct.productId'))" :disabled="true"
                                            wire:model.debounce.500ms="collectingMyProduct.productId" />
                                        @error('collectingMyProduct.productId')
                                            <x-input-error :messages="$message" />
                                        @enderror
                                    </div>
                                </div>

                                {{-- Nama obat --}}
                                <div class="basis-3/6">
                                    <x-input-label for="collectingMyProduct.productName" :value="__('Nama Obat')"
                                        :required="true" />
                                    <div>
                                        <x-text-input id="collectingMyProduct.productName" placeholder="Nama Obat"
                                            class="mt-1 ml-2" :errorshas="__($errors->has('collectingMyProduct.productName'))" :disabled="true"
                                            wire:model.debounce.500ms="collectingMyProduct.productName" />
                                        @error('collectingMyProduct.productName')
                                            <x-input-error :messages="$message" />
                                        @enderror
                                    </div>
                                </div>

                                {{-- Qty --}}
                                <div class="basis-1/12">
                                    <x-input-label for="collectingMyProduct.qty" :value="__('Jml')" :required="true" />
                                    <div>
                                        <x-text-input id="collectingMyProduct.qty" placeholder="Jml Obat" class="mt-1 ml-2"
                                            :errorshas="__($errors->has('collectingMyProduct.qty'))" :disabled="$disabledPropertyRjStatus"
                                            wire:model.debounce.500ms="collectingMyProduct.qty" data-seq="1"
                                            x-init="$nextTick(() => {
                                                $el.closest('[data-form=&quot;rjNonRacikHeader&quot;]')
                                                    ?.querySelector('[data-seq=&quot;1&quot;]')?.focus()
                                            })"
                                            x-on:keydown.enter.prevent="
                                                $el.closest('[data-form=&quot;rjNonRacikHeader&quot;]')?.querySelector('[data-seq=&quot;2&quot;]')?.focus()
                                            " />
                                        @error('collectingMyProduct.qty')
                                            <x-input-error :messages="$message" />
                                        @enderror
                                    </div>
                                </div>

                                {{-- hidden price --}}
                                <div class="hidden">
                                    <x-input-label for="collectingMyProduct.productPrice" :value="__('Harga Obat')"
                                        :required="true" />
                                    <div>
                                        <x-text-input id="collectingMyProduct.productPrice" placeholder="Harga Obat"
                                            class="mt-1 ml-2" :errorshas="__($errors->has('collectingMyProduct.productPrice'))" :disabled="true"
                                            wire:model.debounce.500ms="collectingMyProduct.productPrice" />
                                        @error('collectingMyProduct.productPrice')
                                            <x-input-error :messages="$message" />
                                        @enderror
                                    </div>
                                </div>

                                {{-- Signa X --}}
                                <div class="basis-1/12">
                                    <x-input-label for="collectingMyProduct.signaX" :value="__('Signa')" :required="false" />
                                    <div>
                                        <x-text-input id="collectingMyProduct.signaX" placeholder="Signa1" class="mt-1 ml-2"
                                            :errorshas="__($errors->has('collectingMyProduct.signaX'))" :disabled="$disabledPropertyRjStatus" wire:model="collectingMyProduct.signaX"
                                            data-seq="2"
                                            x-on:keydown.enter.prevent="
                                                $el.closest('[data-form=&quot;rjNonRacikHeader&quot;]')?.querySelector('[data-seq=&quot;3&quot;]')?.focus()
                                            " />
                                        @error('collectingMyProduct.signaX')
                                            <x-input-error :messages="$message" />
                                        @enderror
                                    </div>
                                </div>

                                <div class="basis-[4%]">
                                    <x-input-label for="" :value="__('*')" :required="false" />
                                    <div>
                                        <span class="text-sm">dd</span>
                                    </div>
                                </div>

                                {{-- Signa Hari --}}
                                <div class="basis-1/12">
                                    <x-input-label for="collectingMyProduct.signaHari" :value="__('*')"
                                        :required="false" />
                                    <div>
                                        <x-text-input id="collectingMyProduct.signaHari" placeholder="Signa2"
                                            class="mt-1 ml-2" :errorshas="__($errors->has('collectingMyProduct.signaHari'))" :disabled="$disabledPropertyRjStatus"
                                            wire:model="collectingMyProduct.signaHari" data-seq="3"
                                            x-on:keydown.enter.prevent="
                                                $el.closest('[data-form=&quot;rjNonRacikHeader&quot;]')?.querySelector('[data-seq=&quot;4&quot;]')?.focus()
                                            " />
                                        @error('collectingMyProduct.signaHari')
                                            <x-input-error :messages="$message" />
                                        @enderror
                                    </div>
                                </div>

                                {{-- Catatan Khusus --}}
                                <div class="basis-3/6">
                                    <x-input-label for="collectingMyProduct.catatanKhusus" :value="__('Catatan Khusus')"
                                        :required="true" />
                                    <div>
                                        <x-text-input id="collectingMyProduct.catatanKhusus" placeholder="Catatan Khusus"
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
                                <div class="basis-1/6">
                                    <x-input-label for="" :value="__('Hapus')" :required="true" />
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
                            {{-- collectingMyProduct / obat --}}
                        @endif
                    @endrole

                    {{-- ================== TABEL E-RESEP ================== --}}
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
                                                        NonRacikan
                                                    </x-sort-link>
                                                </th>
                                                <th scope="col" class="px-4 py-3">
                                                    <x-sort-link :active="false" wire:click.prevent=""
                                                        role="button" href="#">
                                                        Obat
                                                    </x-sort-link>
                                                </th>
                                                <th scope="col" class="px-4 py-3">
                                                    <x-sort-link :active="false" wire:click.prevent=""
                                                        role="button" href="#">
                                                        Jumlah
                                                    </x-sort-link>
                                                </th>
                                                <th scope="col" class="px-4 py-3">
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
                                            @isset($dataDaftarPoliRJ['eresep'])
                                                @foreach ($dataDaftarPoliRJ['eresep'] as $key => $eresep)
                                                    <tr class="border-b group dark:border-gray-700">
                                                        <td
                                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                                            {{ $eresep['jenisKeterangan'] }}
                                                        </td>

                                                        <td
                                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                                            {{ $eresep['productName'] }}
                                                        </td>

                                                        {{-- Qty (seq=1) --}}
                                                        <td
                                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                                            <x-text-input placeholder="Jml" :disabled="$disabledPropertyRjStatus"
                                                                wire:model="dataDaftarPoliRJ.eresep.{{ $key }}.qty"
                                                                data-seq="1"
                                                                x-on:keydown.enter.prevent="
                                                                    $el.closest('tr')?.querySelector('[data-seq=&quot;2&quot;]')?.focus()
                                                                " />
                                                        </td>

                                                        {{-- Signa (2,3,4) --}}
                                                        <td
                                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                                            <div class="flex items-baseline space-x-2">
                                                                <div class="basis-1/5">
                                                                    <x-text-input placeholder="Signa1" :disabled="$disabledPropertyRjStatus"
                                                                        wire:model="dataDaftarPoliRJ.eresep.{{ $key }}.signaX"
                                                                        data-seq="2"
                                                                        x-on:keydown.enter.prevent="
                                                                            $el.closest('tr')?.querySelector('[data-seq=&quot;3&quot;]')?.focus()
                                                                        " />
                                                                </div>

                                                                <div class="flex-none">
                                                                    <span class="text-sm">dd</span>
                                                                </div>

                                                                <div class="basis-1/5">
                                                                    <x-text-input placeholder="Signa2" :disabled="$disabledPropertyRjStatus"
                                                                        wire:model="dataDaftarPoliRJ.eresep.{{ $key }}.signaHari"
                                                                        data-seq="3"
                                                                        x-on:keydown.enter.prevent="
                                                                            $el.closest('tr')?.querySelector('[data-seq=&quot;4&quot;]')?.focus()
                                                                        " />
                                                                </div>

                                                                <div class="flex-1">
                                                                    <x-text-input placeholder="Catatan Khusus"
                                                                        :disabled="$disabledPropertyRjStatus"
                                                                        wire:model="dataDaftarPoliRJ.eresep.{{ $key }}.catatanKhusus"
                                                                        data-seq="4"
                                                                        x-on:keydown.enter.prevent="
                                                                            $wire.updateProduct(
                                                                                '{{ $eresep['rjObatDtl'] ?? null }}',
                                                                                '{{ $eresep['qty'] ?? null }}',
                                                                                '{{ $eresep['signaX'] ?? null }}',
                                                                                '{{ $eresep['signaHari'] ?? null }}',
                                                                                '{{ $eresep['catatanKhusus'] ?? null }}'
                                                                            );
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
                                                                    {{ '' }}
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
                    {{-- ================ END TABEL ================== --}}

                </div>
            </div>
        </div>
    </div>
</div>
