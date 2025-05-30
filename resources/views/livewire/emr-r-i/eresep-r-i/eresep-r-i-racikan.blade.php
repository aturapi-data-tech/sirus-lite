<div>
    @php
        $headerResep = isset($dataDaftarRi['eresepHdr'][$resepIndexRef]['tandaTanganDokter']['dokterPeresep']);
        $disabledPropertyResepTtdDokter = $headerResep ? true : false;
        $headerResepTtd = isset($dataDaftarRi['eresepHdr'][$resepIndexRef]['tandaTanganDokter']['dokterPeresep']);
    @endphp

    <div class="w-full mb-1">
        <div id="TransaksiRawatInap" class="p-2">
            <div class="p-2 rounded-lg bg-gray-50">
                <div id="TransaksiRawatInap" class="px-4">
                    @role(['Dokter', 'Admin'])
                        {{-- Jika belum ada produk racikan dipilih --}}
                        @if (empty($headerResepTtd))
                            @if (empty($collectingMyProduct))
                                <div>
                                    @include('livewire.component.l-o-v.list-of-value-product.list-of-value-product')
                                </div>
                            @else
                                {{-- Jika produk racikan sudah dipilih, tampilkan input detail --}}
                                <div class="inline-flex space-x-0.5" x-data>
                                    <div class="basis-1/4">
                                        <x-input-label for="formEntryEresepRIRacikan.noRacikan" :value="__('Racikan')"
                                            :required="true" />
                                        <div>
                                            <x-text-input id="formEntryEresepRIRacikan.noRacikan" placeholder="Racikan"
                                                class="mt-1 ml-2" :errorshas="$errors->has('formEntryEresepRIRacikan.noRacikan')" :disabled="$disabledPropertyResepTtdDokter" wire:model="noRacikan"
                                                x-ref="formEntryEresepRIRacikanNoRacikan"
                                                x-on:keyup.enter="$refs.formEntryEresepRIRacikanProductName.focus()" />
                                            @error('formEntryEresepRIRacikan.noRacikan')
                                                <x-input-error :messages="$message" />
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="hidden">
                                        <x-input-label for="formEntryEresepRIRacikan.productId" :value="__('Kode Obat')"
                                            :required="true" />
                                        <div>
                                            <x-text-input id="formEntryEresepRIRacikan.productId" placeholder="Kode Obat"
                                                class="mt-1 ml-2" :errorshas="$errors->has('formEntryEresepRIRacikan.productId')" :disabled="true"
                                                wire:model="formEntryEresepRIRacikan.productId" />
                                            @error('formEntryEresepRIRacikan.productId')
                                                <x-input-error :messages="$message" />
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="basis-3/6">
                                        <x-input-label for="formEntryEresepRIRacikan.productName" :value="__('Nama Obat')"
                                            :required="true" />
                                        <div>
                                            <x-text-input id="formEntryEresepRIRacikan.productName" placeholder="Nama Obat"
                                                class="mt-1 ml-2" :errorshas="$errors->has('formEntryEresepRIRacikan.productName')" :disabled="true"
                                                wire:model="formEntryEresepRIRacikan.productName" />
                                            @error('formEntryEresepRIRacikan.productName')
                                                <x-input-error :messages="$message" />
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="basis-1/4">
                                        <x-input-label for="formEntryEresepRIRacikan.dosis" :value="__('Dosis')"
                                            :required="true" />
                                        <div>
                                            <x-text-input id="formEntryEresepRIRacikan.dosis" placeholder="Dosis"
                                                class="mt-1 ml-2" :errorshas="$errors->has('formEntryEresepRIRacikan.dosis')" :disabled="$disabledPropertyResepTtdDokter"
                                                x-ref="formEntryEresepRIRacikanDosis" x-init="$refs.formEntryEresepRIRacikanDosis.focus()"
                                                x-on:keyup.enter="$refs.formEntryEresepRIRacikanQty.focus()"
                                                wire:model="formEntryEresepRIRacikan.dosis" />
                                            @error('formEntryEresepRIRacikan.dosis')
                                                <x-input-error :messages="$message" />
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="basis-2/4">
                                        <x-input-label for="formEntryEresepRIRacikan.qty" :value="__('Jml Racikan')"
                                            :required="false" />
                                        <div>
                                            <x-text-input id="formEntryEresepRIRacikan.qty" placeholder="Jml Racikan"
                                                class="mt-1 ml-2" :errorshas="$errors->has('formEntryEresepRIRacikan.qty')" :disabled="$disabledPropertyResepTtdDokter"
                                                wire:model="formEntryEresepRIRacikan.qty"
                                                x-ref="formEntryEresepRIRacikanQty"
                                                x-on:keyup.enter="$refs.formEntryEresepRIRacikanCatatan.focus()" />
                                            @error('formEntryEresepRIRacikan.qty')
                                                <x-input-error :messages="$message" />
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="basis-1/4">
                                        <x-input-label for="formEntryEresepRIRacikan.catatan" :value="__('Catatan')"
                                            :required="false" />
                                        <div>
                                            <x-text-input id="formEntryEresepRIRacikan.catatan" placeholder="Catatan"
                                                class="mt-1 ml-2" :errorshas="$errors->has('formEntryEresepRIRacikan.catatan')" :disabled="$disabledPropertyResepTtdDokter"
                                                x-ref="formEntryEresepRIRacikanCatatan"
                                                x-on:keyup.enter="$refs.formEntryEresepRIRacikanCatatanKhusus.focus()"
                                                wire:model="formEntryEresepRIRacikan.catatan" />
                                            @error('formEntryEresepRIRacikan.catatan')
                                                <x-input-error :messages="$message" />
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="basis-3/4">
                                        <x-input-label for="formEntryEresepRIRacikan.catatanKhusus" :value="__('Signa')"
                                            :required="false" />
                                        <div>
                                            <x-text-input id="formEntryEresepRIRacikan.catatanKhusus" placeholder="Signa"
                                                class="mt-1 ml-2" :errorshas="$errors->has('formEntryEresepRIRacikan.catatanKhusus')" :disabled="$disabledPropertyResepTtdDokter"
                                                wire:model="formEntryEresepRIRacikan.catatanKhusus"
                                                x-ref="formEntryEresepRIRacikanCatatanKhusus"
                                                x-on:keyup.enter="$wire.insertProduct(); $nextTick(() => {
                                                if ($refs.formEntryEresepRIRacikanProductName) {
                                                    $refs.formEntryEresepRIRacikanProductName.focus();
                                                }
                                            })" />
                                            @error('formEntryEresepRIRacikan.catatanKhusus')
                                                <x-input-error :messages="$message" />
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="basis-1/4">
                                        <x-input-label for="" :value="__('Hapus')" :required="false" />
                                        <x-alternative-button class="inline-flex ml-2"
                                            wire:click="resetFormEntryEresepRIRacikan()"
                                            x-on:click="$refs.formEntryEresepRIRacikanProductName.focus()">
                                            <svg class="w-5 h-5 text-gray-800 dark:text-white" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                                                <path
                                                    d="M17 4h-4V2a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v2H1a1 1 0 0 0 0 2h1v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6h1a1 1 0 1 0 0-2ZM7 2h4v2H7V2Zm1 14a1 1 0 1 1-2 0V8a1 1 0 0 1 2 0v8Zm4 0a1 1 0 0 1-2 0V8a1 1 0 0 1 2 0v8Z" />
                                            </svg>
                                        </x-alternative-button>
                                    </div>
                                </div>
                            @endif
                        @endif
                    @endrole

                    {{-- Tampilkan Tabel Data Racikan yang sudah ditambahkan --}}
                    <div class="flex flex-col my-2">
                        <div class="overflow-x-auto rounded-lg">
                            <div class="inline-block min-w-full align-middle">
                                <div class="overflow-hidden shadow sm:rounded-lg">
                                    <table class="w-full text-sm text-left text-gray-500 table-auto dark:text-gray-400">
                                        <thead
                                            class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                                            <tr>
                                                <th scope="col" class="px-4 py-3">
                                                    <x-sort-link :active="false" role="button" href="#">
                                                        Racikan
                                                    </x-sort-link>
                                                </th>
                                                <th scope="col" class="px-4 py-3">
                                                    <x-sort-link :active="false" role="button" href="#">
                                                        Obat
                                                    </x-sort-link>
                                                </th>
                                                <th scope="col" class="w-8 px-4 py-3 text-center">
                                                    Action
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-gray-800">
                                            @isset($dataDaftarRi['eresepHdr'][$resepIndexRef]['eresepRacikan'])
                                                @php
                                                    $myPreviousRow = '';
                                                @endphp
                                                @foreach ($dataDaftarRi['eresepHdr'][$resepIndexRef]['eresepRacikan'] as $key => $eresep)
                                                    @isset($eresep['jenisKeterangan'])
                                                        @php
                                                            $myRacikanBorder =
                                                                $myPreviousRow !== $eresep['noRacikan']
                                                                    ? 'border-t-2 '
                                                                    : '';

                                                            if (isset($eresep['jenisKeterangan'])) {
                                                                $catatan = $eresep['catatan'] ?? '';
                                                                $catatanKhusus = $eresep['catatanKhusus'] ?? '';
                                                                $noRacikan = $eresep['noRacikan'] ?? '';
                                                                $productName = $eresep['productName'] ?? '';
                                                                $qty = $eresep['qty'] ?? null;

                                                                $jmlRacikan = $qty
                                                                    ? 'Jml Racikan ' .
                                                                        $qty .
                                                                        ' | ' .
                                                                        $catatan .
                                                                        ' | S ' .
                                                                        $catatanKhusus .
                                                                        PHP_EOL
                                                                    : '';

                                                                $dosis = $eresep['dosis'] ?? '';

                                                                // Inisialisasi $eresepRacikan jika belum ada
                                                                $eresepRacikan =
                                                                    $noRacikan .
                                                                    '/ ' .
                                                                    $productName .
                                                                    ' - ' .
                                                                    $dosis .
                                                                    PHP_EOL .
                                                                    $jmlRacikan;
                                                            } else {
                                                                $eresepRacikan = '';
                                                            }
                                                        @endphp
                                                        <tr wire:key="racikan-{{ $key }}"
                                                            class="{{ $myRacikanBorder }} group">
                                                            <td
                                                                class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                                                {{ $eresep['jenisKeterangan'] . ' (' . $eresep['noRacikan'] . ')' }}
                                                            </td>

                                                            <td
                                                                class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">

                                                                <div>
                                                                    {{ $eresepRacikan }}
                                                                </div>
                                                            </td>

                                                            <td
                                                                class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                                                @role(['Dokter', 'Admin'])
                                                                    <x-alternative-button class="inline-flex" :disabled="$disabledPropertyResepTtdDokter"
                                                                        wire:click="removeProduct('{{ $eresep['riObatDtl'] }}','{{ $resepIndexRef }}')">
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
                        {{-- End Tabel --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
