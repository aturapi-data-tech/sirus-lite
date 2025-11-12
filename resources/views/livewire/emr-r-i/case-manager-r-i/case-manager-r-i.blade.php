<div>
    <div class="w-full mb-1">
        <div class="w-full p-4 text-sm">
            <h2 class="text-2xl font-bold text-center">Case Manager Rawat Inap</h2>
            <br />

            <!-- Alert untuk status penyimpanan -->
            @if (session()->has('message'))
                <div class="p-4 mb-4 text-green-700 bg-green-100 rounded-lg">
                    {{ session('message') }}
                </div>
            @endif

            <div class="w-full p-2 m-2 mx-auto bg-white rounded-lg shadow-md">

                <!-- FORM A - SKRINING AWAL MPP -->
                <div class="mb-8">
                    <h3 class="mb-4 text-xl font-semibold">Form A - Skrining Awal MPP</h3>

                    <form wire:submit.prevent="simpanFormA">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <!-- Tanggal Skrining -->
                            <div>
                                <x-input-label for="formA.tanggal" :value="__('Tanggal Skrining')" :required="true" />
                                <div class="grid items-center grid-cols-3 mt-1">
                                    <div class="col-span-2">
                                        <x-text-input id="formA.tanggal" type="text"
                                            placeholder="dd/mm/yyyy HH:MM:SS" class="w-full"
                                            wire:model="formA.tanggal" />
                                    </div>
                                    @if (empty($formA['tanggal']))
                                        <div class="col-span-1 ml-2">
                                            <div wire:loading wire:target="setTanggalFormA">
                                                <x-loading />
                                            </div>
                                            <x-green-button :disabled="false" wire:click.prevent="setTanggalFormA"
                                                type="button" wire:loading.remove>
                                                Set Tanggal Skrining
                                            </x-green-button>
                                        </div>
                                    @endif
                                </div>
                                @error('formA.tanggal')
                                    <x-input-error :messages="$message" />
                                @enderror
                            </div>

                            <!-- Masalah Potensial -->
                            <div class="md:col-span-2">
                                <x-input-label for="formA.masalahPotensial" :value="__('Masalah Potensial')" />
                                <x-text-input-area rows="3" class="w-full mt-1"
                                    wire:model="formA.masalahPotensial"
                                    placeholder="Deskripsi masalah potensial yang diidentifikasi..."></x-text-input-area>
                                @error('formA.masalahPotensial')
                                    <x-input-error :messages="$message" />
                                @enderror
                            </div>
                        </div>

                        <!-- PERENCANAAN AWAL -->
                        <div class="p-4 mt-6 border rounded-lg bg-gray-50">
                            <h4 class="mb-3 font-semibold">Perencanaan Awal</h4>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <!-- Tujuan Pendampingan -->
                                <div class="md:col-span-2">
                                    <x-input-label for="formA.perencanaanAwal.tujuanPendampingan" :value="__('Tujuan Pendampingan')"
                                        :required="true" />
                                    <x-text-input id="formA.perencanaanAwal.tujuanPendampingan" class="w-full mt-1"
                                        wire:model="formA.perencanaanAwal.tujuanPendampingan" />
                                    @error('formA.perencanaanAwal.tujuanPendampingan')
                                        <x-input-error :messages="$message" />
                                    @enderror
                                </div>

                                <!-- Target Waktu -->
                                <div>
                                    <x-input-label for="formA.perencanaanAwal.targetWaktu" :value="__('Target Waktu')" />
                                    <x-text-input id="formA.perencanaanAwal.targetWaktu" class="w-full mt-1"
                                        wire:model="formA.perencanaanAwal.targetWaktu"
                                        placeholder="Contoh: 3 hari, 1 minggu, etc." />
                                    @error('formA.perencanaanAwal.targetWaktu')
                                        <x-input-error :messages="$message" />
                                    @enderror
                                </div>

                                <!-- Kegiatan Utama -->
                                <div>
                                    <x-input-label for="formA.perencanaanAwal.kegiatanUtama" :value="__('Kegiatan Utama')" />
                                    <x-text-input id="formA.perencanaanAwal.kegiatanUtama" class="w-full mt-1"
                                        wire:model="formA.perencanaanAwal.kegiatanUtama" />
                                    @error('formA.perencanaanAwal.kegiatanUtama')
                                        <x-input-error :messages="$message" />
                                    @enderror
                                </div>

                                <!-- Unit Terkait -->
                                <div>
                                    <x-input-label for="formA.perencanaanAwal.unitTerkait" :value="__('Unit Terkait')" />
                                    <x-text-input id="formA.perencanaanAwal.unitTerkait" class="w-full mt-1"
                                        wire:model="formA.perencanaanAwal.unitTerkait" />
                                    @error('formA.perencanaanAwal.unitTerkait')
                                        <x-input-error :messages="$message" />
                                    @enderror
                                </div>

                                <!-- Indikator Keberhasilan -->
                                <div>
                                    <x-input-label for="formA.perencanaanAwal.indikatorKeberhasilan"
                                        :value="__('Indikator Keberhasilan')" />
                                    <x-text-input id="formA.perencanaanAwal.indikatorKeberhasilan" class="w-full mt-1"
                                        wire:model="formA.perencanaanAwal.indikatorKeberhasilan" />
                                    @error('formA.perencanaanAwal.indikatorKeberhasilan')
                                        <x-input-error :messages="$message" />
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- TANDA TANGAN PETUGAS FORM A -->
                        <div class="p-4 mt-6 border rounded-lg bg-gray-50">
                            <h4 class="mb-3 font-semibold">Tanda Tangan Petugas</h4>
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <!-- HAPUS HIDDEN INPUTS DI SINI -->
                                <div>
                                    <x-input-label :value="__('Nama Petugas')" />
                                    <x-text-input value="{{ auth()->user()->myuser_name ?? '' }}" class="w-full mt-1"
                                        disabled />
                                </div>
                                <div>
                                    <x-input-label :value="__('Kode Petugas')" />
                                    <x-text-input value="{{ auth()->user()->myuser_code ?? '' }}" class="w-full mt-1"
                                        disabled />
                                </div>
                                <div class="md:col-span-2">
                                    <x-input-label :value="__('Jabatan')" />
                                    <x-text-input value="MPP" class="w-full mt-1" disabled />
                                </div>
                            </div>
                        </div>

                        <!-- TOMBOL SIMPAN FORM A -->
                        <div class="flex justify-end mt-6">
                            <x-primary-button type="submit" class="px-6 py-2">
                                Simpan Form A
                            </x-primary-button>
                        </div>
                    </form>
                </div>

                <!-- FORM B - PELAKSANAAN, MONITORING, ADVOKASI, TERMINASI -->
                @if ($showFormB)
                    @include('livewire.emr-r-i.case-manager-r-i.create-case-manager-form-b')
                @endif
            </div>

            <!-- RIWAYAT FORM A & B -->
            <div class="mt-8">
                <h3 class="mb-4 text-xl font-semibold">Riwayat Case Manager</h3>

                @if (!empty($dataDaftarRi['formMPP']['formA']))
                    <div class="overflow-x-auto bg-white rounded-lg shadow">
                        <table class="w-full text-sm text-left text-gray-700 table-auto">
                            <thead class="sticky top-0 text-xs text-gray-900 uppercase bg-gray-100">
                                <tr>
                                    <th scope="col" class="px-4 py-3">No</th>
                                    <th scope="col" class="px-4 py-3">Form</th>
                                    <th scope="col" class="px-4 py-3">Keterangan</th>
                                    <th scope="col" class="px-4 py-3">Petugas</th>
                                    <th scope="col" class="px-4 py-3">Action</th>
                                </tr>
                            </thead>

                            <tbody class="bg-white">
                                @php
                                    $counter = 1;
                                @endphp

                                @foreach ($dataDaftarRi['formMPP']['formA'] as $formA)
                                    <!-- Baris Form A -->
                                    <tr class="border-b group hover:bg-blue-50">
                                        <td class="px-4 py-3 font-semibold">{{ $counter++ }}</td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2 mb-8">
                                                <span class="font-semibold text-green-700">Form A Skrining Awal</span>
                                            </div>

                                            <div>
                                                <!-- Tombol Hapus Form A -->
                                                <x-danger-button
                                                    wire:click="hapusForm('formA', '{{ $formA['formA_id'] }}')"
                                                    wire:confirm="Apakah Anda yakin ingin menghapus Form A ini?"
                                                    wire:loading.attr="disabled" wire:target="hapusForm" class="">

                                                    <!-- Konten normal -->
                                                    <span wire:loading.remove wire:target="hapusForm"
                                                        class="flex items-center">
                                                        <svg class="w-6 h-6 mr-1" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                        Hapus Form A
                                                    </span>

                                                    <!-- Loading state -->
                                                    <span wire:loading wire:target="hapusForm"
                                                        class="flex items-center">
                                                        <x-loading class="w-6 h-6 mr-1" />
                                                    </span>
                                                </x-danger-button>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="font-medium text-gray-900">{{ $formA['tanggal'] }}</div>

                                            <!-- MASALAH POTENSIAL -->
                                            @if (!empty($formA['masalahPotensial']))
                                                <div class="mb-2">
                                                    <div class="flex items-center gap-1 mb-1">
                                                        <svg class="w-4 h-4 text-red-500" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                                        </svg>
                                                        <span class="text-sm font-semibold text-red-600">Masalah
                                                            Potensial:</span>
                                                    </div>
                                                    <p class="pl-5 text-sm text-gray-700">
                                                        {{ $formA['masalahPotensial'] }}</p>
                                                </div>
                                            @endif

                                            <!-- PERENCANAAN AWAL -->
                                            <div class="space-y-2">
                                                <!-- TUJUAN PENDAMPINGAN -->
                                                <div>
                                                    <div class="flex items-center gap-1 mb-1">
                                                        <svg class="w-4 h-4 text-blue-500" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                                        </svg>
                                                        <span
                                                            class="text-sm font-semibold text-blue-600">Tujuan:</span>
                                                    </div>
                                                    <p class="pl-5 text-sm text-gray-700">
                                                        {{ $formA['perencanaanAwal']['tujuanPendampingan'] ?? '-' }}</p>
                                                </div>

                                                <!-- TARGET WAKTU -->
                                                @if (!empty($formA['perencanaanAwal']['targetWaktu']))
                                                    <div>
                                                        <div class="flex items-center gap-1 mb-1">
                                                            <svg class="w-4 h-4 text-green-500" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                            <span class="text-sm font-semibold text-green-600">Target
                                                                Waktu:</span>
                                                        </div>
                                                        <p class="pl-5 text-sm text-gray-700">
                                                            {{ $formA['perencanaanAwal']['targetWaktu'] }}</p>
                                                    </div>
                                                @endif

                                                <!-- KEGIATAN UTAMA -->
                                                @if (!empty($formA['perencanaanAwal']['kegiatanUtama']))
                                                    <div>
                                                        <div class="flex items-center gap-1 mb-1">
                                                            <svg class="w-4 h-4 text-purple-500" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                            </svg>
                                                            <span
                                                                class="text-sm font-semibold text-purple-600">Kegiatan
                                                                Utama:</span>
                                                        </div>
                                                        <p class="pl-5 text-sm text-gray-700">
                                                            {{ $formA['perencanaanAwal']['kegiatanUtama'] }}</p>
                                                    </div>
                                                @endif

                                                <!-- UNIT TERKAIT -->
                                                @if (!empty($formA['perencanaanAwal']['unitTerkait']))
                                                    <div>
                                                        <div class="flex items-center gap-1 mb-1">
                                                            <svg class="w-4 h-4 text-orange-500" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                            </svg>
                                                            <span class="text-sm font-semibold text-orange-600">Unit
                                                                Terkait:</span>
                                                        </div>
                                                        <p class="pl-5 text-sm text-gray-700">
                                                            {{ $formA['perencanaanAwal']['unitTerkait'] }}</p>
                                                    </div>
                                                @endif

                                                <!-- INDIKATOR KEBERHASILAN -->
                                                @if (!empty($formA['perencanaanAwal']['indikatorKeberhasilan']))
                                                    <div>
                                                        <div class="flex items-center gap-1 mb-1">
                                                            <svg class="w-4 h-4 text-teal-500" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                            <span class="text-sm font-semibold text-teal-600">Indikator
                                                                Keberhasilan:</span>
                                                        </div>
                                                        <p class="pl-5 text-sm text-gray-700">
                                                            {{ $formA['perencanaanAwal']['indikatorKeberhasilan'] }}
                                                        </p>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $formA['tandaTanganPetugas']['petugasName'] ?? '' }}</div>
                                            <div class="text-xs text-gray-500">
                                                {{ $formA['tandaTanganPetugas']['jabatan'] ?? '' }}</div>
                                            <div class="text-xs text-gray-400">
                                                {{ $formA['tandaTanganPetugas']['petugasCode'] ?? '' }}</div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex flex-col gap-2">
                                                <div class="grid grid-cols-2 gap-2">
                                                    <!-- Tombol Tambah Form B -->
                                                    <x-green-button
                                                        wire:click="tambahFormB('{{ $formA['formA_id'] }}')"
                                                        wire:loading.attr="disabled" wire:target="tambahFormB"
                                                        class="" title="Tambah Form B">

                                                        <!-- Konten normal -->
                                                        <span wire:loading.remove wire:target="tambahFormB"
                                                            class="flex items-center">
                                                            <svg class="w-6 h-6 mr-1" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                            </svg>
                                                            Tambah Form B
                                                        </span>

                                                        <!-- Loading state -->
                                                        <span wire:loading wire:target="tambahFormB"
                                                            class="flex items-center">
                                                            <x-loading class="w-6 h-6 mr-1" />
                                                        </span>
                                                    </x-green-button>

                                                    <!-- Tombol Cetak Form A -->
                                                    <x-yellow-button
                                                        wire:click="cetakFormA('{{ $formA['formA_id'] }}')"
                                                        wire:loading.attr="disabled" wire:target="cetakFormA"
                                                        class="flex-1 px-2 py-1" title="Cetak Form A">

                                                        <!-- Konten normal -->
                                                        <span wire:loading.remove wire:target="cetakFormA"
                                                            class="flex items-center">
                                                            <svg class="w-6 h-6 mr-1" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                            </svg>
                                                            Cetak Form A
                                                        </span>

                                                        <!-- Loading state -->
                                                        <span wire:loading wire:target="cetakFormA"
                                                            class="flex items-center">
                                                            <x-loading class="w-6 h-6 mr-1" />
                                                        </span>
                                                    </x-yellow-button>


                                                </div>

                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Baris Form B yang terkait -->
                                    @php
                                        $relatedFormBs = collect($dataDaftarRi['formMPP']['formB'] ?? [])
                                            ->where('formA_id', $formA['formA_id'])
                                            ->values();
                                    @endphp

                                    @foreach ($relatedFormBs as $formB)
                                        <tr class="border-b group hover:bg-green-50">
                                            <td class="px-4 py-3 font-semibold">{{ $counter++ }}</td>
                                            <td class="px-4 py-3">
                                                <div class="flex items-center gap-2 mb-8">
                                                    <span class="font-semibold text-green-700">Form B Tindak
                                                        Lanjut</span>

                                                    @if (!empty($formA['masalahPotensial']))
                                                        <div class="mb-2">
                                                            <div class="flex items-center gap-1 mb-1">
                                                                <svg class="w-4 h-4 text-red-500" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                                                </svg>
                                                                <span
                                                                    class="text-sm font-semibold text-red-600">Masalah
                                                                    Potensial:</span>
                                                            </div>
                                                            <p class="pl-5 text-sm text-gray-700">
                                                                {{ $formA['masalahPotensial'] }}</p>
                                                        </div>
                                                    @endif
                                                </div>

                                                <div>
                                                    <!-- Tombol Hapus Form B -->
                                                    <x-danger-button
                                                        wire:click="hapusForm('formB', '{{ $formB['formB_id'] }}')"
                                                        wire:confirm="Apakah Anda yakin ingin menghapus Form B ini?"
                                                        wire:loading.attr="disabled" wire:target="hapusForm"
                                                        class="">

                                                        <!-- Konten normal -->
                                                        <span wire:loading.remove wire:target="hapusForm"
                                                            class="flex items-center">
                                                            <svg class="w-4 h-4 mr-1" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                            Hapus B
                                                        </span>

                                                        <!-- Loading state -->
                                                        <span wire:loading wire:target="hapusForm"
                                                            class="flex items-center">
                                                            <x-loading class="w-4 h-4 mr-1" />
                                                        </span>
                                                    </x-danger-button>
                                                </div>
                                            </td>

                                            <td class="px-4 py-3">
                                                <div class="font-medium text-gray-900">{{ $formB['tanggal'] }}</div>
                                                <!-- PELAKSANAAN MONITORING -->
                                                @if (!empty($formB['pelaksanaanMonitoring']))
                                                    <div class="mb-2">
                                                        <div class="flex items-center gap-1 mb-1">
                                                            <svg class="w-4 h-4 text-blue-500" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                                            </svg>
                                                            <span
                                                                class="text-sm font-semibold text-blue-600">Monitoring:</span>
                                                        </div>
                                                        <p class="pl-5 text-sm text-gray-700">
                                                            {{ $formB['pelaksanaanMonitoring'] }}</p>
                                                    </div>
                                                @endif

                                                <!-- ADVOKASI & KOLABORASI -->
                                                @if (
                                                    !empty($formB['advokasiKolaborasi']['hambatanPasien']) ||
                                                        !empty($formB['advokasiKolaborasi']['kolaborasiDengan']) ||
                                                        !empty($formB['advokasiKolaborasi']['advokasiDilakukan']) ||
                                                        !empty($formB['advokasiKolaborasi']['eskalasi']))
                                                    <div class="mb-2">
                                                        <div class="flex items-center gap-1 mb-1">
                                                            <svg class="w-4 h-4 text-purple-500" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a2 2 0 01-2-2v-6a2 2 0 012-2h2V4l4 4h4z" />
                                                            </svg>
                                                            <span
                                                                class="text-sm font-semibold text-purple-600">Advokasi
                                                                & Kolaborasi:</span>
                                                        </div>
                                                        <div class="pl-5 space-y-1">
                                                            @if (!empty($formB['advokasiKolaborasi']['hambatanPasien']))
                                                                <p class="text-sm text-gray-700"><span
                                                                        class="font-medium">Hambatan:</span>
                                                                    {{ $formB['advokasiKolaborasi']['hambatanPasien'] }}
                                                                </p>
                                                            @endif
                                                            @if (!empty($formB['advokasiKolaborasi']['kolaborasiDengan']))
                                                                <p class="text-sm text-gray-700"><span
                                                                        class="font-medium">Kolaborasi:</span>
                                                                    {{ $formB['advokasiKolaborasi']['kolaborasiDengan'] }}
                                                                </p>
                                                            @endif
                                                            @if (!empty($formB['advokasiKolaborasi']['advokasiDilakukan']))
                                                                <p class="text-sm text-gray-700"><span
                                                                        class="font-medium">Advokasi:</span>
                                                                    {{ $formB['advokasiKolaborasi']['advokasiDilakukan'] }}
                                                                </p>
                                                            @endif
                                                            @if (!empty($formB['advokasiKolaborasi']['eskalasi']))
                                                                <p class="text-sm text-gray-700"><span
                                                                        class="font-medium">Eskalasi:</span>
                                                                    {{ $formB['advokasiKolaborasi']['eskalasi'] }}</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif

                                                <!-- TERMINASI -->
                                                @if (!empty($formB['terminasi']))
                                                    <div>
                                                        <div class="flex items-center gap-1 mb-1">
                                                            <svg class="w-4 h-4 text-green-500" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M5 13l4 4L19 7" />
                                                            </svg>
                                                            <span
                                                                class="text-sm font-semibold text-green-600">Terminasi:</span>
                                                        </div>
                                                        <p class="pl-5 text-sm text-gray-700">
                                                            {{ $formB['terminasi'] }}</p>
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $formB['tandaTanganPetugas']['petugasName'] ?? '' }}</div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $formB['tandaTanganPetugas']['jabatan'] ?? '' }}</div>
                                                <div class="text-xs text-gray-400">
                                                    {{ $formB['tandaTanganPetugas']['petugasCode'] ?? '' }}</div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="grid grid-cols-2 gap-2">
                                                    <!-- Tombol Cetak Form B -->
                                                    <x-yellow-button
                                                        wire:click="cetakFormB('{{ $formB['formB_id'] }}')"
                                                        wire:loading.attr="disabled" wire:target="cetakFormB"
                                                        class="" title="Cetak Form B">

                                                        <!-- Konten normal -->
                                                        <span wire:loading.remove wire:target="cetakFormB"
                                                            class="flex items-center">
                                                            <svg class="w-4 h-4 mr-1" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                            </svg>
                                                            Cetak B
                                                        </span>

                                                        <!-- Loading state -->
                                                        <span wire:loading wire:target="cetakFormB"
                                                            class="flex items-center">
                                                            <x-loading class="w-4 h-4 mr-1" />
                                                        </span>
                                                    </x-yellow-button>


                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach

                                    <!-- Spacer antara grup -->
                                    @if (!$loop->last)
                                        <tr>
                                            <td colspan="6" class="px-4 py-2">
                                                <div class="border-t border-gray-300"></div>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach

                                @if (empty($dataDaftarRi['formMPP']['formA']))
                                    <tr class="border-b">
                                        <td colspan="6" class="px-4 py-8 text-center text-gray-500 bg-gray-50">
                                            <div class="flex flex-col items-center justify-center">
                                                <svg class="w-12 h-12 mb-3 text-gray-400" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <p class="font-medium">Belum ada data Case Manager</p>
                                                <p class="text-sm">Silakan buat Form A terlebih dahulu</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    @if (empty($dataDaftarRi['formMPP']['formA']))
                        <div class="p-8 text-center text-gray-500 bg-gray-100 rounded-lg">
                            Belum ada data Form A. Silakan isi Form A terlebih dahulu.
                        </div>
                    @endif
                @else
                    <div class="p-8 text-center text-gray-500 bg-gray-100 rounded-lg">
                        Belum ada data Case Manager. Silakan isi Form A terlebih dahulu.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
