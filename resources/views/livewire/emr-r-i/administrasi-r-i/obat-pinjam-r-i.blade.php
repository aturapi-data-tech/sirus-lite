<div>

    @php
        $disabledProperty = true;
        $disabledPropertyRjStatus = false;
    @endphp

    <div class="w-full mb-1">
        <div id="TransaksiProduct">
            <div class="grid grid-cols-10 gap-2" x-data>
                <!-- Tanggal -->
                <div class="col-span-2">
                    <x-input-label for="formEntryProduct.productDate" :value="__('Tanggal')" :required="__(true)" />
                    <div>
                        <div class="flex items-center mb-2">
                            @if (!$formEntryProduct['productDate'])
                                <div class="w-full mt-2 ml-2">
                                    <div wire:loading wire:target="setProductDate">
                                        <x-loading />
                                    </div>
                                    <x-yellow-button :disabled="false"
                                        wire:click.prevent="setProductDate('{{ date('d/m/Y H:i:s') }}')" type="button"
                                        wire:loading.remove class="w-full">
                                        <div wire:poll.20s>
                                            {{ date('d/m/Y H:i:s') }}
                                        </div>
                                    </x-yellow-button>
                                </div>
                            @else
                                <x-text-input id="formEntryProduct.productDate" type="text"
                                    placeholder="dd/mm/yyyy hh24:mi:ss" class="mt-1 ml-2" :errorshas="__($errors->has('formEntryProduct.productDate'))"
                                    wire:model="formEntryProduct.productDate" :disabled="$disabledPropertyRjStatus" />
                            @endif
                        </div>
                        @error('formEntryProduct.productDate')
                            <x-input-error :messages="$message" />
                        @enderror
                    </div>
                </div>

                <!-- LOV Product / Nama Product -->
                <div class="col-span-2">
                    @if (empty($collectingMyProduct))
                        <div class="">
                            @include('livewire.component.l-o-v.list-of-value-product.list-of-value-product')
                        </div>
                    @else
                        <x-input-label for="formEntryProduct.productName" :value="__('Nama Obat')" :required="__(true)" />
                        <div>
                            <x-text-input id="formEntryProduct.productName" placeholder="Nama Obat" class="mt-1 ml-2"
                                :errorshas="__($errors->has('formEntryProduct.productId'))" wire:model="formEntryProduct.productName" :disabled="true" />
                        </div>
                    @endif
                    @error('formEntryProduct.productId')
                        <x-input-error :messages="$message" />
                    @enderror
                </div>

                <!-- Jumlah -->
                <div class="col-span-1">
                    <x-input-label for="formEntryProduct.productQty" :value="__('Jml')" :required="__(true)" />
                    <div>
                        <x-text-input id="formEntryProduct.productQty" placeholder="Jml" class="mt-1 ml-2"
                            :errorshas="__($errors->has('formEntryProduct.productQty'))" wire:model="formEntryProduct.productQty" :disabled="$disabledPropertyRjStatus" />
                        @error('formEntryProduct.productQty')
                            <x-input-error :messages="$message" />
                        @enderror
                    </div>
                </div>

                <!-- Tarif -->
                <div class="col-span-2">
                    <x-input-label for="formEntryProduct.productPrice" :value="__('Tarif')" :required="__(true)" />
                    <div>
                        <x-text-input id="formEntryProduct.productPrice" placeholder="Tarif" class="mt-1 ml-2"
                            :errorshas="__($errors->has('formEntryProduct.productPrice'))" wire:model="formEntryProduct.productPrice" :disabled="$disabledPropertyRjStatus"
                            x-on:keyup.enter="$wire.insertProduct()" />
                        @error('formEntryProduct.productPrice')
                            <x-input-error :messages="$message" />
                        @enderror
                    </div>
                </div>

                <!-- Total (Price x Qty) -->
                <div class="col-span-2">
                    <x-input-label for="formEntryProduct.productTotal" :value="__('Total')" :required="__(true)" />
                    <div>
                        <x-text-input id="formEntryProduct.productTotal" placeholder="Total" class="mt-1 ml-2"
                            :errorshas="__($errors->has('formEntryProduct.productTotal'))"
                            value="{{ number_format($formEntryProduct['productPrice'] * $formEntryProduct['productQty']) }}"
                            :disabled="true" />
                    </div>
                </div>

                <!-- Tombol Reset / Hapus -->
                <div class="col-span-1">
                    <x-input-label for="" :value="__('Hapus')" :required="__(true)" />
                    <x-alternative-button class="inline-flex ml-2" wire:click.prevent="resetformEntryProduct()">
                        <svg class="w-5 h-5 text-gray-800 dark:text-white" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                            <path
                                d="M17 4h-4V2a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v2H1a1 1 0 0 0 0 2h1v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6h1a1 1 0 1 0 0-2ZM7 2h4v2H7V2Zm1 14a1 1 0 1 1-2 0V8a1 1 0 0 1 2 0v8Zm4 0a1 1 0 0 1-2 0V8a1 1 0 0 1 2 0v8Z" />
                        </svg>
                    </x-alternative-button>
                </div>
            </div>

            <!-- Tombol Simpan Product -->
            <div class="w-full">
                <div wire:loading wire:target="insertProduct">
                    <x-loading />
                </div>
                <x-primary-button :disabled="false" wire:click.prevent="insertProduct()" type="button"
                    wire:loading.remove class="w-full">
                    Simpan Obat Pinjam
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
                                <th scope="col" class="px-4 py-3">
                                    <x-sort-link :active="false" wire:click.prevent="" role="button" href="#">
                                        Tgl
                                    </x-sort-link>
                                </th>
                                <th scope="col" class="px-4 py-3">
                                    <x-sort-link :active="false" wire:click.prevent="" role="button" href="#">
                                        Product
                                    </x-sort-link>
                                </th>
                                <th scope="col" class="px-4 py-3">
                                    <x-sort-link :active="false" wire:click.prevent="" role="button" href="#">
                                        Tarif
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

                                $sortedRiObatPinjam = collect($dataObatPinjam['riObatPinjam'] ?? [])
                                    ->sortByDesc(function ($item) {
                                        $date = $item['riobat_date'] ?? '';

                                        // Jika kosong, jadikan paling bawah
                                        if (!$date) {
                                            return 0;
                                        }

                                        try {
                                            return Carbon::createFromFormat(
                                                'd/m/Y H:i:s',
                                                $date,
                                                env('APP_TIMEZONE'),
                                            )->timestamp;
                                        } catch (\Exception $e) {
                                            // Jika parsing gagal (format salah/trailling data), juga jadikan paling bawah
                                            return 0;
                                        }
                                    })
                                    ->values();
                            @endphp

                            @if ($sortedRiObatPinjam->isNotEmpty())
                                @foreach ($sortedRiObatPinjam as $key => $Product)
                                    <tr class="border-b group dark:border-gray-700">
                                        <td
                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                            {{ $Product['riobat_date'] }}
                                        </td>
                                        <td
                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                            {{ $Product['product_name'] }}
                                        </td>
                                        @php
                                            // Ambil qty & price, default 0 jika bukan angka
                                            $qty = is_numeric($Product['riobat_qty'] ?? null)
                                                ? $Product['riobat_qty']
                                                : 0;
                                            $price = is_numeric($Product['riobat_price'] ?? null)
                                                ? $Product['riobat_price']
                                                : 0;
                                            $total = $qty * $price;
                                        @endphp
                                        <td
                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                            {{ number_format($qty) }} x
                                            {{ number_format($price) }} =
                                            {{ number_format($total) }}
                                        </td>
                                        <td
                                            class="px-4 py-3 font-normal text-center text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                            <x-alternative-button class="inline-flex"
                                                wire:click.prevent="removeProduct('{{ $Product['riobat_no'] }}')">
                                                <svg class="w-5 h-5 text-gray-800 dark:text-white" aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                    viewBox="0 0 18 20">
                                                    <path
                                                        d="M17 4h-4V2a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v2H1a1 1 0 0 0 0 2h1v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6h1a1 1 0 1 0 0-2ZM7 2h4v2H7V2Zm1 14a1 1 0 1 1-2 0V8a1 1 0 0 1 2 0v8Zm4 0a1 1 0 0 1-2 0V8a1 1 0 0 1 2 0v8Z" />
                                                </svg>
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
