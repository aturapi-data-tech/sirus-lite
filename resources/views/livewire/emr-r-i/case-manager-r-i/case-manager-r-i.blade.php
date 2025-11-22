<div>
    <div class="w-full mb-1">
        <div class="w-full p-4 text-sm">

            {{-- Alert untuk status penyimpanan --}}
            @if (session()->has('message'))
                <div class="p-4 mb-4 text-green-700 bg-green-100 rounded-lg">
                    {{ session('message') }}
                </div>
            @endif

            <div class="w-full p-2 m-2 mx-auto bg-white rounded-lg shadow-md">

                {{-- FORM A - SKRINING AWAL MPP --}}
                <div class="mb-8">
                    <h3 class="mb-4 text-xl font-semibold">Form A - Skrining Awal MPP</h3>

                    <form wire:submit.prevent="simpanFormA">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            {{-- Tanggal Skrining --}}
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

                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            {{-- Identifikasi Kasus --}}
                            <div class="p-4 mt-6 border rounded-lg bg-gray-50">
                                <h4 class="mb-3 font-semibold">Identifikasi</h4>
                                <div class="">
                                    <x-input-label for="formA.indentifikasiKasus" :value="__('Identifikasi Kasus')" />
                                    <x-text-input-area rows="12" class="w-full mt-1"
                                        wire:model="formA.indentifikasiKasus"
                                        placeholder="Uraian singkat masalah / kasus pasien..."></x-text-input-area>
                                    @error('formA.indentifikasiKasus')
                                        <x-input-error :messages="$message" />
                                    @enderror
                                </div>
                            </div>

                            {{-- ASSESSMENT & PERENCANAAN --}}
                            <div class="p-4 mt-6 border rounded-lg bg-gray-50">
                                <h4 class="mb-3 font-semibold">Assessment & Perencanaan</h4>

                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    {{-- Assessment --}}
                                    <div class="md:col-span-2">
                                        <x-input-label for="formA.assessment" :value="__('Assessment')" />
                                        <x-text-input-area id="formA.assessment" rows="3" class="w-full mt-1"
                                            wire:model="formA.assessment"
                                            placeholder="Ringkasan assessment kondisi klinis / sosial / finansial pasien...">
                                        </x-text-input-area>
                                        @error('formA.assessment')
                                            <x-input-error :messages="$message" />
                                        @enderror
                                    </div>

                                    {{-- Perencanaan --}}
                                    <div class="md:col-span-2">
                                        <x-input-label for="formA.perencanaan" :value="__('Perencanaan')" />
                                        <x-text-input-area id="formA.perencanaan" rows="3" class="w-full mt-1"
                                            wire:model="formA.perencanaan"
                                            placeholder="Rencana tindak lanjut, koordinasi, discharge planning, dll...">
                                        </x-text-input-area>
                                        @error('formA.perencanaan')
                                            <x-input-error :messages="$message" />
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>



                        {{-- TANDA TANGAN PETUGAS FORM A --}}
                        {{-- <div class="p-4 mt-6 border rounded-lg bg-gray-50">
                            <h4 class="mb-3 font-semibold">Tanda Tangan Petugas</h4>
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
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
                        </div> --}}

                        {{-- TOMBOL SIMPAN FORM A --}}
                        <div class="flex justify-end mt-6">
                            <x-primary-button type="submit" class="px-6 py-2">
                                Simpan Form A
                            </x-primary-button>
                        </div>
                    </form>
                </div>

                {{-- FORM B - PELAKSANAAN, MONITORING, ADVOKASI, TERMINASI --}}
                @if ($showFormB)
                    @include('livewire.emr-r-i.case-manager-r-i.create-case-manager-form-b')
                @endif
            </div>

            {{-- RIWAYAT FORM A & B --}}
            <div class="mt-8">
                <h3 class="mb-4 text-xl font-semibold">Data MPP</h3>

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
                                    {{-- Baris Form A --}}
                                    <tr class="border-b group hover:bg-blue-50">
                                        <td class="px-4 py-3 font-semibold">{{ $counter++ }}</td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2 mb-8">
                                                <span class="font-semibold text-green-700">Form A Skrining Awal</span>
                                            </div>

                                            <div>
                                                {{-- Tombol Hapus Form A --}}
                                                <x-danger-button
                                                    wire:click="hapusForm('formA', '{{ $formA['formA_id'] }}')"
                                                    wire:confirm="Apakah Anda yakin ingin menghapus Form A ini?"
                                                    wire:loading.attr="disabled" wire:target="hapusForm">

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

                                                    <span wire:loading wire:target="hapusForm"
                                                        class="flex items-center">
                                                        <x-loading class="w-6 h-6 mr-1" />
                                                    </span>
                                                </x-danger-button>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="font-medium text-gray-900">{{ $formA['tanggal'] }}</div>

                                            {{-- IDENTIFIKASI KASUS --}}
                                            @if (!empty($formA['indentifikasiKasus']))
                                                <div class="mb-2">
                                                    <div class="flex items-center gap-1 mb-1">
                                                        <svg class="w-4 h-4 text-red-500" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                                        </svg>
                                                        <span class="text-sm font-semibold text-red-600">Identifikasi
                                                            Kasus:</span>
                                                    </div>
                                                    <p class="pl-5 text-sm text-gray-700">
                                                        {{ $formA['indentifikasiKasus'] }}</p>
                                                </div>
                                            @endif

                                            {{-- ASSESSMENT --}}
                                            @if (!empty($formA['assessment']))
                                                <div class="mb-2">
                                                    <div class="flex items-center gap-1 mb-1">
                                                        <svg class="w-4 h-4 text-blue-500" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M9 5h6m-7 4h8m-9 4h10m-9 4h8" />
                                                        </svg>
                                                        <span
                                                            class="text-sm font-semibold text-blue-600">Assessment:</span>
                                                    </div>
                                                    <p class="pl-5 text-sm text-gray-700">
                                                        {{ $formA['assessment'] }}</p>
                                                </div>
                                            @endif

                                            {{-- PERENCANAAN --}}
                                            @if (!empty($formA['perencanaan']))
                                                <div class="mb-2">
                                                    <div class="flex items-center gap-1 mb-1">
                                                        <svg class="w-4 h-4 text-green-500" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                                        </svg>
                                                        <span
                                                            class="text-sm font-semibold text-green-600">Perencanaan:</span>
                                                    </div>
                                                    <p class="pl-5 text-sm text-gray-700">
                                                        {{ $formA['perencanaan'] }}</p>
                                                </div>
                                            @endif
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
                                                    {{-- Tombol Tambah Form B --}}
                                                    <x-green-button
                                                        wire:click="tambahFormB('{{ $formA['formA_id'] }}')"
                                                        wire:loading.attr="disabled" wire:target="tambahFormB"
                                                        title="Tambah Form B">

                                                        <span wire:loading.remove wire:target="tambahFormB"
                                                            class="flex items-center">
                                                            <svg class="w-6 h-6 mr-1" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                            </svg>
                                                            Tambah Form B
                                                        </span>

                                                        <span wire:loading wire:target="tambahFormB"
                                                            class="flex items-center">
                                                            <x-loading class="w-6 h-6 mr-1" />
                                                        </span>
                                                    </x-green-button>

                                                    {{-- Tombol Cetak Form A --}}
                                                    <x-yellow-button
                                                        wire:click="cetakFormA('{{ $formA['formA_id'] }}')"
                                                        wire:loading.attr="disabled" wire:target="cetakFormA"
                                                        class="flex-1 px-2 py-1" title="Cetak Form A">

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

                                                        <span wire:loading wire:target="cetakFormA"
                                                            class="flex items-center">
                                                            <x-loading class="w-6 h-6 mr-1" />
                                                        </span>
                                                    </x-yellow-button>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    {{-- Baris Form B yang terkait --}}
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
                                                </div>

                                                <div>
                                                    {{-- Tombol Hapus Form B --}}
                                                    <x-danger-button
                                                        wire:click="hapusForm('formB', '{{ $formB['formB_id'] }}')"
                                                        wire:confirm="Apakah Anda yakin ingin menghapus Form B ini?"
                                                        wire:loading.attr="disabled" wire:target="hapusForm">

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

                                                        <span wire:loading wire:target="hapusForm"
                                                            class="flex items-center">
                                                            <x-loading class="w-4 h-4 mr-1" />
                                                        </span>
                                                    </x-danger-button>
                                                </div>
                                            </td>

                                            <td class="px-4 py-3">
                                                <div class="font-medium text-gray-900">{{ $formB['tanggal'] }}</div>

                                                {{-- PELAKSANAAN MONITORING --}}
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

                                                {{-- ADVOKASI & KOLABORASI (STRING) --}}
                                                @if (!empty($formB['advokasiKolaborasi']))
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
                                                        <p class="pl-5 text-sm text-gray-700">
                                                            {{ $formB['advokasiKolaborasi'] }}</p>
                                                    </div>
                                                @endif

                                                {{-- TERMINASI --}}
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
                                                    <div></div>
                                                    {{-- Tombol Cetak Form B --}}
                                                    <x-yellow-button
                                                        wire:click="cetakFormB('{{ $formB['formB_id'] }}')"
                                                        wire:loading.attr="disabled" wire:target="cetakFormB"
                                                        title="Cetak Form B">

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

                                                        <span wire:loading wire:target="cetakFormB"
                                                            class="flex items-center">
                                                            <x-loading class="w-4 h-4 mr-1" />
                                                        </span>
                                                    </x-yellow-button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach

                                    {{-- Spacer antara grup --}}
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
                @else
                    <div class="p-8 text-center text-gray-500 bg-gray-100 rounded-lg">
                        Belum ada data MPP. Silakan isi Form A terlebih dahulu.
                    </div>
                @endif
            </div>
        </div>


    </div>
</div>
