<div>
    <div class="w-full mb-1">
        @role(['Perawat', 'Admin'])
            <div class="pt-0">
                {{-- Jika belum ada produk yang dipilih --}}
                @if (empty($collectingMyProduct))
                    <div>
                        @include('livewire.component.l-o-v.list-of-value-product.list-of-value-product')
                    </div>
                @else
                    {{-- Jika produk sudah dipilih, tampilkan form input --}}
                    <div class="flex items-baseline space-x-2" x-data>
                        <!-- Nama Obat / Jenis Cairan -->
                        <div class="basis-3/6">
                            <x-input-label for="obatDanCairan.namaObatAtauJenisCairan" :value="__('Nama Obat / Jenis Cairan')" :required="true" />
                            <x-text-input id="obatDanCairan.namaObatAtauJenisCairan" placeholder="Nama Obat / Jenis Cairan"
                                class="mt-1 ml-2" :errorshas="$errors->has('obatDanCairan.namaObatAtauJenisCairan')" :disabled="true"
                                wire:model="obatDanCairan.namaObatAtauJenisCairan" />
                        </div>

                        <!-- Jumlah -->
                        <div class="basis-1/12">
                            <x-input-label for="obatDanCairan.jumlah" :value="__('Jumlah')" :required="true" />
                            <x-text-input id="obatDanCairan.jumlah" placeholder="Jumlah" class="mt-1 ml-2" :errorshas="$errors->has('obatDanCairan.jumlah')"
                                :disabled="$disabledPropertyRjStatus" wire:model="obatDanCairan.jumlah" data-seq="1"
                                x-on:keydown.enter.prevent="
                                    document.querySelector('[data-seq=\\'2\\']')?.focus()
                                " />
                            @error('obatDanCairan.jumlah')
                                <span class="text-sm text-red-600">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Dosis -->
                        <div class="basis-1/12">
                            <x-input-label for="obatDanCairan.dosis" :value="__('Dosis')" :required="true" />
                            <x-text-input id="obatDanCairan.dosis" placeholder="Dosis" class="mt-1 ml-2" :errorshas="$errors->has('obatDanCairan.dosis')"
                                :disabled="$disabledPropertyRjStatus" wire:model="obatDanCairan.dosis" data-seq="2"
                                x-on:keydown.enter.prevent="
                                    document.querySelector('[data-seq=\\'3\\']')?.focus()
                                " />
                            @error('obatDanCairan.dosis')
                                <span class="text-sm text-red-600">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Rute -->
                        <div class="basis-1/12">
                            <x-input-label for="obatDanCairan.rute" :value="__('Rute')" :required="true" />
                            <x-text-input id="obatDanCairan.rute" placeholder="Rute" class="mt-1 ml-2" :errorshas="$errors->has('obatDanCairan.rute')"
                                :disabled="$disabledPropertyRjStatus" wire:model="obatDanCairan.rute" data-seq="3"
                                x-on:keydown.enter.prevent="
                                    document.querySelector('[data-seq=\\'4\\']')?.focus()
                                " />
                            @error('obatDanCairan.rute')
                                <span class="text-sm text-red-600">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Waktu Pemberian -->
                        <div class="basis-2/12">
                            <x-input-label for="obatDanCairan.waktuPemberian" :value="__('Waktu Pemberian')" :required="true" />
                            <x-text-input id="obatDanCairan.waktuPemberian" placeholder="dd/mm/yyyy hh:mm:ss"
                                class="mt-1 ml-2" :errorshas="$errors->has('obatDanCairan.waktuPemberian')" :disabled="$disabledPropertyRjStatus"
                                wire:model="obatDanCairan.waktuPemberian" data-seq="4"
                                x-on:keydown.enter.prevent="
                                    document.querySelector('[data-seq=\\'5\\']')?.focus()
                                " />
                            @error('obatDanCairan.waktuPemberian')
                                <span class="text-sm text-red-600">{{ $message }}</span>
                            @enderror

                            {{-- PERBAIKAN: Gunakan Carbon dan panggil method tanpa parameter --}}
                            @if (!$obatDanCairan['waktuPemberian'])
                                <div class="mt-1">
                                    <x-secondary-button wire:click.prevent="setWaktuPemberian" type="button"
                                        class="text-xs">
                                        Set Waktu Sekarang
                                    </x-secondary-button>
                                </div>
                            @endif
                        </div>

                        <!-- Keterangan -->
                        <div class="basis-2/12">
                            <x-input-label for="obatDanCairan.keterangan" :value="__('Keterangan')" :required="true" />
                            <x-text-input id="obatDanCairan.keterangan" placeholder="Keterangan" class="mt-1 ml-2"
                                :errorshas="$errors->has('obatDanCairan.keterangan')" :disabled="$disabledPropertyRjStatus" wire:model="obatDanCairan.keterangan" data-seq="5"
                                x-on:keydown.enter.prevent="
                                    $wire.addObatDanCairan();
                                    $nextTick(() => {
                                        if(!$wire.collectingMyProduct){
                                            document.querySelector('[data-seq=\\'1\\']')?.focus()
                                        }
                                    })
                                " />
                            @error('obatDanCairan.keterangan')
                                <span class="text-sm text-red-600">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="basis-1/12">
                            <x-input-label for="" :value="__('Aksi')" :required="false" />
                            <div class="flex space-x-2">
                                {{-- <x-green-button class="inline-flex ml-2" wire:click="addObatDanCairan"
                                    wire:loading.attr="disabled" wire:target="addObatDanCairan">
                                    <svg class="w-4 h-4 mr-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 18 18">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="M9 1v16M1 9h16" />
                                    </svg>
                                    <span wire:loading.remove wire:target="addObatDanCairan">Tambah</span>
                                    <span wire:loading wire:target="addObatDanCairan">Menambah...</span>
                                </x-green-button> --}}

                                <x-alternative-button class="inline-flex" wire:click="resetObatDanCairanForm"
                                    wire:loading.attr="disabled">
                                    <svg class="w-4 h-4 mr-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        fill="currentColor" viewBox="0 0 18 20">
                                        <path
                                            d="M17 4h-4V2a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v2H1a1 1 0 0 0 0 2h1v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6h1a1 1 0 1 0 0-2ZM7 2h4v2H7V2Zm1 14a1 1 0 1 1-2 0V8a1 1 0 0 1 2 0v8Zm4 0a1 1 0 0 1-2 0V8a1 1 0 0 1 2 0v8Z" />
                                    </svg>
                                    Batal
                                </x-alternative-button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endrole

        {{-- Tampilkan Tabel Data Obat & Cairan yang sudah ditambahkan --}}
        <div class="flex flex-col my-2">
            <div class="overflow-x-auto rounded-lg">
                <div class="inline-block min-w-full align-middle">
                    <div class="overflow-hidden shadow sm:rounded-lg">
                        <table class="w-full text-sm text-left text-gray-500 table-auto dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-4 py-3 text-center">
                                        No
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-center">
                                        Waktu Pemberian
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-center">
                                        Obat / Cairan
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-center">
                                        Jumlah
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-center">
                                        Dosis
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-center">
                                        Rute
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-center">
                                        Keterangan
                                    </th>
                                    {{-- <th scope="col" class="px-4 py-3 text-center">
                                        Pemeriksa
                                    </th> --}}
                                    <th scope="col" class="px-4 py-3 text-center">
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800">
                                @php
                                    use Carbon\Carbon;

                                    // PERBAIKAN: Handle jika data tidak ada atau null
                                    $obatDanCairanData =
                                        $dataDaftarUgd['observasi']['obatDanCairan']['pemberianObatDanCairan'] ?? [];

                                    $sortedObatDanCairan = collect($obatDanCairanData)
                                        ->sortByDesc(function ($item) {
                                            try {
                                                return Carbon::createFromFormat(
                                                    'd/m/Y H:i:s',
                                                    $item['waktuPemberian'] ?? '01/01/2000 00:00:00',
                                                );
                                            } catch (\Exception $e) {
                                                return Carbon::now();
                                            }
                                        })
                                        ->values();
                                @endphp

                                @forelse ($sortedObatDanCairan as $index => $obat)
                                    @php
                                        // PERBAIKAN: Gunakan ID yang konsisten
                                        $rowId = $obat['id'] ?? ($obat['waktuPemberian'] ?? uniqid());
                                    @endphp

                                    <tr wire:key="obat-cairan-{{ $rowId }}"
                                        class="border-b group dark:border-gray-700 hover:bg-gray-50">

                                        <td class="px-4 py-3 text-center">
                                            {{ $loop->iteration }}
                                        </td>

                                        <td class="px-4 py-3 text-center">
                                            <div class="flex flex-col items-center">
                                                <span class="font-medium text-gray-900">
                                                    {{ $obat['waktuPemberian'] ?? '-' }}
                                                </span>
                                                <span class="text-xs text-gray-500">
                                                    {{ $obat['pemeriksa'] ?? '-' }}
                                                </span>
                                            </div>
                                        </td>

                                        <td class="px-4 py-3 text-center">
                                            {{ $obat['namaObatAtauJenisCairan'] ?? '-' }}
                                        </td>

                                        <td class="px-4 py-3 text-center">
                                            {{ $obat['jumlah'] ?? '-' }}
                                        </td>

                                        <td class="px-4 py-3 text-center">
                                            {{ $obat['dosis'] ?? '-' }}
                                        </td>

                                        <td class="px-4 py-3 text-center">
                                            {{ $obat['rute'] ?? '-' }}
                                        </td>

                                        <td class="px-4 py-3 text-center">
                                            {{ $obat['keterangan'] ?? '-' }}
                                        </td>

                                        {{-- <td class="px-4 py-3 text-center">
                                            {{ $obat['pemeriksa'] ?? '-' }}
                                        </td> --}}

                                        <td class="px-4 py-3 text-center">
                                            @role(['Perawat', 'Admin'])
                                                <x-alternative-button class="inline-flex"
                                                    wire:click="removeObatDanCairan('{{ $obat['waktuPemberian'] }}')"
                                                    wire:confirm="Apakah Anda yakin ingin menghapus data ini?"
                                                    wire:loading.attr="disabled">
                                                    <svg class="w-4 h-4 mr-1" aria-hidden="true"
                                                        xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                        viewBox="0 0 18 20">
                                                        <path
                                                            d="M17 4h-4V2a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v2H1a1 1 0 0 0 0 2h1v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6h1a1 1 0 1 0 0-2ZM7 2h4v2H7V2Zm1 14a1 1 0 1 1-2 0V8a1 1 0 0 1 2 0v8Zm4 0a1 1 0 0 1-2 0V8a1 1 0 0 1 2 0v8Z" />
                                                    </svg>
                                                    <span wire:loading.remove
                                                        wire:target="removeObatDanCairan">Hapus</span>
                                                    <span wire:loading wire:target="removeObatDanCairan">...</span>
                                                </x-alternative-button>
                                            @endrole
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                                            <div class="flex flex-col items-center justify-center">
                                                <svg class="w-12 h-12 mb-2 text-gray-400" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                    </path>
                                                </svg>
                                                <p class="text-lg font-medium">Belum ada data pemberian obat & cairan
                                                </p>
                                                <p class="text-sm">Silakan tambah data pemberian baru</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
