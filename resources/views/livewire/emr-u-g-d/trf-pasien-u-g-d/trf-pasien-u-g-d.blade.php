<div class="w-full mb-1">

    <div class="w-full p-4 text-sm">
        <h2 class="text-2xl font-bold text-center">FORM TRANSFER PASIEN UGD</h2>
    </div>

    {{-- RINGKASAN KLINIS DARI UGD --}}
    <div class="grid grid-cols-2 gap-2 px-4">
        <div class="w-full p-3 mx-auto mb-2 bg-white rounded-lg shadow-md">
            <h3 class="mb-2 text-lg font-semibold">Keluhan Utama & Alergi</h3>

            <div class="mb-2">
                <x-input-label value="Keluhan Utama" />
                <x-text-input-area rows="3" class="w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm"
                    wire:model="dataDaftarUgd.trfUgd.keluhanUtama" readonly></x-text-input-area>
            </div>

            <div class="mb-2">
                <x-input-label value="Alergi" />
                <x-text-input-area rows="2" class="w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm"
                    wire:model="dataDaftarUgd.trfUgd.alergi" readonly></x-text-input-area>
            </div>
        </div>

        <div class="w-full p-3 mx-auto mb-2 bg-white rounded-lg shadow-md">
            <h3 class="mb-2 text-lg font-semibold">Diagnosis & Terapi UGD</h3>

            <div class="mb-2">
                <x-input-label value="Diagnosis (Free Text)" />
                <x-text-input-area rows="3" class="w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm"
                    wire:model="dataDaftarUgd.trfUgd.diagnosisFreeText" readonly></x-text-input-area>
            </div>

            <div class="mb-2">
                <x-input-label value="Terapi UGD" />
                @php
                    $terapiUgd = $dataDaftarUgd['trfUgd']['terapiUgd'] ?? [];
                @endphp
                @if (!empty($terapiUgd))
                    <ul class="pl-4 mt-1 text-sm list-disc">
                        @foreach ($terapiUgd as $row)
                            @if (trim($row) !== '')
                                <li>{{ $row }}</li>
                            @endif
                        @endforeach
                    </ul>
                @else
                    <p class="mt-1 text-sm italic text-gray-500">Belum ada terapi terekam.</p>
                @endif
            </div>
        </div>
    </div>


    <x-theme-line></x-theme-line>

    {{-- LEVELING DOKTER --}}
    <div>
        <div class="w-full mx-8 my-4">

            <div class="grid grid-cols-5 gap-2">

                {{-- LOV Dokter --}}
                <div class="col-span-2">
                    @if (empty($collectingMyDokter))
                        <div>
                            @include('livewire.component.l-o-v.list-of-value-dokter.list-of-value-dokter')
                        </div>
                    @else
                        <x-input-label for="levelingDokter.drName" :value="__('Nama Dokter')" :required="true" />
                        <div>
                            <x-text-input id="levelingDokter.drName" placeholder="Nama Dokter" class="mt-1 ml-2"
                                :errorshas="$errors->has('levelingDokter.drName')" wire:model="levelingDokter.drName" :disabled="true" />
                        </div>
                    @endif

                    @error('levelingDokter.drId')
                        <x-input-error :messages="$message" />
                    @enderror
                </div>

                {{-- Level Dokter --}}
                <div class="col-span-2 ml-2">
                    <x-input-label for="levelingDokter.levelDokter" :value="__('Level Dokter')" :required="true" />
                    <div class="grid grid-cols-2 gap-2">
                        @foreach (['Utama', 'RawatGabung'] as $option)
                            <x-radio-button :label="$option" value="{{ $option }}"
                                wire:model="levelingDokter.levelDokter" />
                        @endforeach
                    </div>

                    @error('levelingDokter.levelDokter')
                        <x-input-error :messages="$message" />
                    @enderror
                </div>

                {{-- Tombol Hapus / Reset --}}
                <div class="col-span-1">
                    <x-input-label :value="__('Hapus')" :required="true" />
                    <x-alternative-button class="inline-flex ml-2" wire:click.prevent="resetLevelingDokter()">
                        <svg class="w-5 h-5 text-gray-800 dark:text-white" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                            <path
                                d="M17 4h-4V2a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v2H1a1 1 0 0 0 0 2h1v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6h1a1 1 0 1 0 0-2ZM7 2h4v2H7V2Zm1 14a1 1 0 1 1-2 0V8a1 1 0 0 1 2 0v8Zm4 0a1 1 0 0 1-2 0V8a1 1 0 0 1 2 0v8Z" />
                        </svg>
                    </x-alternative-button>
                </div>

            </div>

            {{-- Tombol Simpan --}}
            <div class="grid grid-cols-1 ml-2">
                <div wire:loading wire:target="addLevelingDokter">
                    <x-loading />
                </div>

                <x-green-button wire:click.prevent="addLevelingDokter()" type="button" wire:loading.remove>
                    SIMPAN LEVELING DOKTER
                </x-green-button>
            </div>

            {{-- Tabel dokter TRF UGD --}}
            <div>
                @include('livewire.emr-u-g-d.trf-pasien-u-g-d.trf-u-g-d-list-dokter-table')
            </div>
        </div>
    </div>

    <x-theme-line></x-theme-line>

    {{-- DATA PEMINDAHAN PASIEN --}}
    <div class="grid grid-cols-1 gap-2 px-4 md:grid-cols-2">
        <div class="w-full p-3 mx-auto mb-2 bg-white rounded-lg shadow-md">
            <h3 class="mb-2 text-lg font-semibold">Data Pemindahan Pasien</h3>

            <div class="mb-2">
                <x-input-label for="dataDaftarUgd.trfUgd.pindahDariRuangan" :value="__('Pindah dari Ruangan')" />
                <x-text-input id="dataDaftarUgd.trfUgd.pindahDariRuangan" class="w-full mt-1"
                    wire:model.defer="dataDaftarUgd.trfUgd.pindahDariRuangan" />
            </div>

            <div class="mb-2">
                <x-input-label for="dataDaftarUgd.trfUgd.pindahKeRuangan" :value="__('Pindah ke Ruangan')" />
                <x-text-input id="dataDaftarUgd.trfUgd.pindahKeRuangan" class="w-full mt-1"
                    wire:model.defer="dataDaftarUgd.trfUgd.pindahKeRuangan" />
            </div>

            <div class="mb-2">
                <x-input-label for="dataDaftarUgd.trfUgd.tglPindah" :value="__('Tanggal / Jam Pindah')" />

                <div class="flex items-center gap-2">
                    <x-text-input id="dataDaftarUgd.trfUgd.tglPindah" placeholder="dd/mm/yyyy hh:mm:ss"
                        class="w-full mt-1" wire:model.defer="dataDaftarUgd.trfUgd.tglPindah" />

                    <x-green-button wire:click="setTglPindah" class="mt-1 text-xs">
                        Set Tgl Pindah
                    </x-green-button>
                </div>
            </div>

        </div>

        <div class="w-full p-3 mx-auto mb-2 bg-white rounded-lg shadow-md">
            <h3 class="mb-2 text-lg font-semibold">Kondisi & Fasilitas</h3>

            <div class="grid grid-cols-1 gap-4 p-3 mb-4 border rounded-md bg-gray-50">

                {{-- Kondisi Klinis --}}
                <div>
                    <x-input-label :value="__('Kondisi Klinis (Derajat 0–3)')" :required="true" />

                    <div class="grid grid-cols-4 gap-2 mt-2">

                        @foreach ([0, 1, 2, 3] as $d)
                            <x-radio-button :label="'Derajat ' . $d" value="{{ $d }}"
                                wire:model="dataDaftarUgd.trfUgd.kondisiKlinis" />
                        @endforeach
                    </div>

                    {{-- Keterangan otomatis --}}
                    <div class="p-2 mt-2 text-xs text-gray-700 bg-white border rounded-md">
                        <strong>Keterangan:</strong>
                        <span>
                            @php
                                $keterangan = [
                                    0 => 'Stabil, tanpa keluhan berat.',
                                    1 => 'Keluhan ringan-sedang, perlu observasi.',
                                    2 => 'Kondisi sedang, risiko memburuk, perlu tindakan.',
                                    3 => 'Gawat Darurat, mengancam jiwa, perlu tindakan segera.',
                                ];

                                // warna background by derajat
                                $warna = [
                                    0 => 'bg-green-100 border-green-300 text-green-800',
                                    1 => 'bg-yellow-100 border-yellow-300 text-yellow-800',
                                    2 => 'bg-orange-100 border-orange-300 text-orange-800',
                                    3 => 'bg-red-100 border-red-300 text-red-800',
                                ];

                                $derajat = $dataDaftarUgd['trfUgd']['kondisiKlinis'] ?? 0;
                            @endphp

                            <div class="p-2 mt-2 text-xs border rounded-md {{ $warna[$derajat] }}">
                                <strong>Keterangan:</strong>
                                <span>{{ $keterangan[$derajat] }}</span>
                            </div>
                        </span>
                    </div>

                    @error('dataDaftarUgd.trfUgd.kondisiKlinis')
                        <x-input-error :messages="$message" />
                    @enderror
                </div>

                {{-- Tambahan Fasilitas Jika Ada --}}
                <div>
                    <x-input-label :value="__('Fasilitas Pendukung')" />
                    <x-text-input-area rows="2"
                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary"
                        wire:model.defer="dataDaftarUgd.trfUgd.fasilitasPendukung"></x-text-input-area>
                </div>

            </div>


            <div class="mb-2">
                <x-input-label :value="__('Fasilitas yang Dibutuhkan')" />
                <x-text-input-area rows="2" class="w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm"
                    wire:model.defer="dataDaftarUgd.trfUgd.fasilitas"></x-text-input-area>
            </div>

            <div class="mb-2">
                <x-input-label :value="__('Alasan Pindah')" />
                <x-text-input-area rows="2" class="w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm"
                    wire:model.defer="dataDaftarUgd.trfUgd.alasanPindah"></x-text-input-area>
            </div>

            <div class="mb-2">
                <x-input-label :value="__('Metode Pemindahan Pasien')" />
                <x-text-input-area rows="2" class="w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm"
                    placeholder="Brankar / Kursi roda / Jalan sendiri / dll."
                    wire:model.defer="dataDaftarUgd.trfUgd.metodePemindahanPasien"></x-text-input-area>
            </div>
        </div>
    </div>

    <x-theme-line></x-theme-line>

    {{-- RENCANA PERAWATAN --}}
    <div class="w-full p-4 mx-auto mb-2 bg-white rounded-lg shadow-md">
        <h3 class="mb-3 text-lg font-semibold">Rencana Perawatan</h3>

        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
            <div>
                <x-input-label :value="__('Observasi')" />
                <x-text-input-area rows="2" class="w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm"
                    wire:model.defer="dataDaftarUgd.trfUgd.rencanaPerawatan.observasi"></x-text-input-area>
            </div>

            <div>
                <x-input-label :value="__('Pembatasan Cairan')" />
                <x-text-input-area rows="2" class="w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm"
                    wire:model.defer="dataDaftarUgd.trfUgd.rencanaPerawatan.pembatasanCairan"></x-text-input-area>
            </div>

            <div>
                <x-input-label :value="__('Balance Cairan')" />
                <x-text-input-area rows="2" class="w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm"
                    wire:model.defer="dataDaftarUgd.trfUgd.rencanaPerawatan.balanceCairan"></x-text-input-area>
            </div>

            <div>
                <x-input-label :value="__('Diet')" />
                <x-text-input-area rows="2" class="w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm"
                    wire:model.defer="dataDaftarUgd.trfUgd.rencanaPerawatan.diet"></x-text-input-area>
            </div>

            <div class="md:col-span-2">
                <x-input-label :value="__('Lain-lain')" />
                <x-text-input-area rows="2" class="w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm"
                    wire:model.defer="dataDaftarUgd.trfUgd.rencanaPerawatan.lainLain"></x-text-input-area>
            </div>
        </div>
    </div>


    <x-theme-line></x-theme-line>

    {{-- ALAT YANG TERPASANG --}}
    <div class="w-full p-4 mx-auto mb-2 bg-white rounded-lg shadow-md">
        <h3 class="mb-3 text-lg font-semibold">Alat yang Terpasang</h3>

        {{-- Form input alat --}}
        <div class="grid grid-cols-1 gap-2 mb-3 md:grid-cols-4">
            <div>
                <x-input-label :value="__('Jenis Alat')" />
                <x-text-input placeholder="mis: IV Line / Kateter" class="w-full mt-1"
                    wire:model.defer="alat.jenis" />
            </div>
            <div>
                <x-input-label :value="__('Lokasi')" />
                <x-text-input placeholder="mis: Tangan kanan, NGT, dll." class="w-full mt-1"
                    wire:model.defer="alat.lokasi" />
            </div>
            <div>
                <x-input-label :value="__('Ukuran')" />
                <x-text-input placeholder="mis: 20G / 10Fr" class="w-full mt-1" wire:model.defer="alat.ukuran" />
            </div>
            <div>
                <x-input-label :value="__('Keterangan')" />
                <x-text-input placeholder="mis: terpasang baik" class="w-full mt-1"
                    wire:model.defer="alat.keterangan" />
            </div>
        </div>

        <div class="mb-4">
            <x-green-button wire:click.prevent="addAlatTerpasang()" type="button" wire:loading.attr="disabled">
                TAMBAH ALAT TERPASANG
            </x-green-button>
        </div>

        {{-- List alat --}}
        @php
            $alatList = $dataDaftarUgd['trfUgd']['alatYangTerpasang'] ?? [];
        @endphp

        @if (!empty($alatList))
            <div class="mt-2 space-y-2">
                @foreach ($alatList as $index => $item)
                    <div class="flex items-center justify-between p-2 text-sm border rounded-md bg-gray-50">
                        <div>
                            <div class="font-semibold">
                                {{ $item['jenis'] ?? '-' }}
                                @if (!empty($item['ukuran']))
                                    ({{ $item['ukuran'] }})
                                @endif
                            </div>
                            <div class="text-gray-600">
                                @if (!empty($item['lokasi']))
                                    Lokasi: {{ $item['lokasi'] }}
                                @endif
                            </div>
                            @if (!empty($item['keterangan']))
                                <div class="text-xs text-gray-500">
                                    {{ $item['keterangan'] }}
                                </div>
                            @endif
                        </div>

                        <x-alternative-button class="ml-2"
                            wire:click.prevent="removeAlatTerpasang({{ $index }})">
                            Hapus
                        </x-alternative-button>
                    </div>
                @endforeach
            </div>
        @else
            <p class="mt-1 text-sm italic text-gray-500">
                Belum ada alat terpasang yang dicatat.
            </p>
        @endif
    </div>

    <x-theme-line></x-theme-line>

    {{-- PETUGAS PENGIRIM & PENERIMA --}}
    <div class="grid content-center grid-cols-1 gap-2 p-2 m-2 md:grid-cols-2">
        {{-- PETUGAS PENGIRIM --}}
        <div class="flex items-end justify-center w-full p-2 m-2 mx-auto bg-white rounded-lg shadow-md">
            <div class="w-full mb-3">
                <x-input-label for="dataDaftarUgd.trfUgd.petugasPengirim" :value="__('Petugas Pengirim')" />

                @php
                    $petugasPengirim = $dataDaftarUgd['trfUgd']['petugasPengirim'] ?? '';
                    $petugasPengirimDate = $dataDaftarUgd['trfUgd']['petugasPengirimDate'] ?? '';
                @endphp

                @if (empty($petugasPengirim))
                    <x-text-input id="dataDaftarUgd.trfUgd.petugasPengirim" placeholder="Petugas Pengirim"
                        class="mt-1 mb-2" :disabled="true" wire:model="dataDaftarUgd.trfUgd.petugasPengirim" />

                    <x-yellow-button :disabled="false" wire:click.prevent="setPetugasPengirim()" type="button"
                        wire:loading.remove>
                        TTD Petugas Pengirim
                    </x-yellow-button>
                @else
                    <div class="w-full p-2 mt-2 text-center border rounded-md">
                        <div class="text-sm font-semibold">
                            {{ $petugasPengirim }}
                        </div>
                        <div class="mt-1 text-sm text-gray-600">
                            {{ $petugasPengirimDate }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- PETUGAS PENERIMA --}}
        <div class="flex items-end justify-center w-full p-2 m-2 mx-auto bg-white rounded-lg shadow-md">
            <div class="w-full mb-3">
                <x-input-label for="dataDaftarUgd.trfUgd.petugasPenerima" :value="__('Petugas Penerima')" />

                @php
                    $petugasPenerima = $dataDaftarUgd['trfUgd']['petugasPenerima'] ?? '';
                    $petugasPenerimaDate = $dataDaftarUgd['trfUgd']['petugasPenerimaDate'] ?? '';
                @endphp

                @if (empty($petugasPenerima))
                    <x-text-input id="dataDaftarUgd.trfUgd.petugasPenerima" placeholder="Petugas Penerima"
                        class="mt-1 mb-2" :disabled="true" wire:model="dataDaftarUgd.trfUgd.petugasPenerima" />

                    <x-yellow-button :disabled="false" wire:click.prevent="setPetugasPenerima()" type="button"
                        wire:loading.remove>
                        TTD Petugas Penerima
                    </x-yellow-button>
                @else
                    <div class="w-full p-2 mt-2 text-center border rounded-md">
                        <div class="text-sm font-semibold">
                            {{ $petugasPenerima }}
                        </div>
                        <div class="mt-1 text-sm text-gray-600">
                            {{ $petugasPenerimaDate }}
                        </div>
                    </div>
                @endif
            </div>
        </div>



    </div>
    <div class="grid w-full grid-cols-1 px-4 my-8">

        {{-- Loading indikator --}}


        <x-green-button wire:click.prevent="cetakTrfPasienUgd" type="button" wire:loading.attr="disabled"
            wire:target="cetakTrfPasienUgd">

            {{-- Saat tidak loading → tampilkan ikon printer + teks --}}
            <span class="inline-flex items-center" wire:loading.remove wire:target="cetakTrfPasienUgd">
                <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M6 9V4h12v5m-2 4h2a2 2 0 0 0 2-2v-1a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v1a2 2 0 0 0 2 2h2m8 0v5H8v-5h8z" />
                </svg>
                CETAK FORM TRF PASIEN UGD
            </span>

            {{-- Saat loading → ganti dengan loader --}}
            <div wire:loading wire:target="cetakTrfPasienUgd" class="flex items-center justify-center">
                <x-loading />
            </div>
        </x-green-button>

    </div>
</div>
