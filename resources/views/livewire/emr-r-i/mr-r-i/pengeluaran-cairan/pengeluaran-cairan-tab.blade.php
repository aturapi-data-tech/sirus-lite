{{-- livewire/emr-r-i/mr-r-i/pengeluaran-cairan/pengeluaran-cairan-tab.blade.php --}}

<div class="space-y-4">

    {{-- ======================== --}}
    {{-- FORM INPUT               --}}
    {{-- ======================== --}}

    <div class="p-4 bg-white rounded-lg shadow">
        {{-- Baris Waktu Pengeluaran + Tombol Set Waktu --}}
        <div class="grid items-end grid-cols-12 gap-2 mb-3">
            <div class="col-span-9">
                <x-input-label for="pengeluaranCairan.waktuPengeluaran" :value="__('Waktu Pengeluaran')" :required="__(true)" />
                <x-text-input id="pengeluaranCairan.waktuPengeluaran"
                    placeholder="Waktu Pengeluaran [dd/mm/yyyy hh24:mi:ss]" class="mt-1" :errorshas="__($errors->has('pengeluaranCairan.waktuPengeluaran'))"
                    wire:model.debounce.500ms="pengeluaranCairan.waktuPengeluaran" />
            </div>

            <div class="col-span-3">
                @if (!$pengeluaranCairan['waktuPengeluaran'])
                    <div wire:loading wire:target="setWaktuPengeluaran">
                        <x-loading />
                    </div>

                    <x-green-button :disabled=false wire:click.prevent="setWaktuPengeluaran" type="button"
                        wire:loading.remove>
                        Set Waktu Pengeluaran
                    </x-green-button>
                @else
                    <div class="p-2 text-sm text-center text-gray-600 bg-gray-100 rounded-md">
                        Waktu Telah Diatur
                    </div>
                @endif
            </div>
        </div>

        {{-- Baris input jenis output, volume, warna, keterangan --}}
        <div class="mb-2">
            <div class="grid grid-cols-12 gap-2">
                <div class="col-span-3">
                    <x-input-label for="pengeluaranCairan.jenisOutput" :value="__('Jenis Output')" :required="__(true)" />
                </div>
                <div class="col-span-2">
                    <x-input-label for="pengeluaranCairan.volume" :value="__('Volume (ml)')" :required="__(true)" />
                </div>
                <div class="col-span-3">
                    <x-input-label for="pengeluaranCairan.warnaKarakteristik" :value="__('Warna / Karakteristik')" :required="__(true)" />
                </div>
                <div class="col-span-4">
                    <x-input-label for="pengeluaranCairan.keterangan" :value="__('Keterangan')" :required="__(true)" />
                </div>
            </div>

            <div class="grid grid-cols-12 gap-2">
                {{-- Jenis Output --}}
                <div class="col-span-3">
                    <select id="pengeluaranCairan.jenisOutput"
                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-emerald-700 focus:ring-emerald-700"
                        wire:model.debounce.500ms="pengeluaranCairan.jenisOutput">
                        <option value="">Pilih Jenis Output</option>
                        <option value="Urine">Urine</option>
                        <option value="Feses">Feses</option>
                        <option value="Muntah">Muntah</option>
                        <option value="Drain">Drain</option>
                        <option value="NGT Output">NGT Output</option>
                        <option value="Darah">Darah</option>
                    </select>
                    @error('pengeluaranCairan.jenisOutput')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Volume --}}
                <div class="col-span-2">
                    <x-text-input id="pengeluaranCairan.volume" placeholder="Volume" class="mt-1" :errorshas="__($errors->has('pengeluaranCairan.volume'))"
                        wire:model.debounce.500ms="pengeluaranCairan.volume" />
                </div>

                {{-- Warna / Karakteristik --}}
                <div class="col-span-3">
                    <x-text-input id="pengeluaranCairan.warnaKarakteristik" placeholder="Contoh: kuning jernih"
                        class="mt-1" :errorshas="__($errors->has('pengeluaranCairan.warnaKarakteristik'))"
                        wire:model.debounce.500ms="pengeluaranCairan.warnaKarakteristik" />
                </div>

                {{-- Keterangan --}}
                <div class="col-span-4">
                    <x-text-input id="pengeluaranCairan.keterangan" placeholder="Keterangan" class="mt-1"
                        :errorshas="__($errors->has('pengeluaranCairan.keterangan'))" wire:model.debounce.500ms="pengeluaranCairan.keterangan" />
                </div>
            </div>
        </div>



        {{-- Tombol simpan --}}
        <div class="grid grid-cols-1">
            <div wire:loading wire:target="addPengeluaranCairan">
                <x-loading />
            </div>

            <x-green-button :disabled=false wire:click.prevent="addPengeluaranCairan()" type="button"
                wire:loading.remove>
                Simpan Pengeluaran Cairan
            </x-green-button>
        </div>
    </div>

    {{-- ======================== --}}
    {{-- TABEL DATA              --}}
    {{-- ======================== --}}

    @php
        $rows = $dataDaftarRi['observasi']['pengeluaranCairan']['pengeluaranCairan'] ?? [];
    @endphp

    @if (!empty($rows))
        <div class="p-4 bg-white rounded-lg shadow">
            <h3 class="mb-3 text-sm font-semibold">Daftar Pengeluaran Cairan</h3>

            <div class="overflow-x-auto">
                <table class="min-w-full text-xs divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 font-semibold text-left text-gray-600 uppercase">TANGGAL / JAM</th>
                            <th class="px-4 py-2 font-semibold text-left text-gray-600 uppercase">JENIS OUTPUT</th>
                            <th class="px-4 py-2 font-semibold text-left text-gray-600 uppercase">VOLUME (ml)</th>
                            <th class="px-4 py-2 font-semibold text-left text-gray-600 uppercase">WARNA / KARAKTERISTIK
                            </th>
                            <th class="px-4 py-2 font-semibold text-left text-gray-600 uppercase">KETERANGAN</th>
                            <th class="px-4 py-2 font-semibold text-left text-gray-600 uppercase">PEMERIKSA</th>
                            <th class="px-4 py-2 font-semibold text-left text-gray-600 uppercase">ACTION</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($rows as $item)
                            <tr>
                                <td class="px-4 py-2 whitespace-nowrap">
                                    {{ $item['waktuPengeluaran'] ?? '' }}
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap">
                                    {{ $item['jenisOutput'] ?? '' }}
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap">
                                    {{ $item['volume'] ?? '' }}
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap">
                                    {{ $item['warnaKarakteristik'] ?? '' }}
                                </td>
                                <td class="px-4 py-2">
                                    {{ $item['keterangan'] ?? '' }}
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap">
                                    {{ $item['pemeriksa'] ?? '' }}
                                </td>
                                <td class="px-4 py-2 text-right whitespace-nowrap">
                                    <x-alternative-button type="button"
                                        wire:click="removePengeluaranCairan('{{ $item['waktuPengeluaran'] ?? '' }}')"
                                        title="Hapus data">
                                        <svg class="w-5 h-5 text-gray-800" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                                            <path
                                                d="M17 4h-4V2a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v2H1a1 1 0 0 0 0 2h1v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6h1a1 1 0 1 0 0-2ZM7 2h4v2H7V2Zm1 14a1 1 0 1 1-2 0V8a1 1 0 0 1 2 0v8Zm4 0a1 1 0 0 1-2 0V8a1 1 0 0 1 2 0v8Z" />
                                        </svg>
                                    </x-alternative-button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>
