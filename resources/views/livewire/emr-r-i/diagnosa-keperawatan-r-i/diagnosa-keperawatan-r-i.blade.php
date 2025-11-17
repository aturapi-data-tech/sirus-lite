<div>
    <div class="w-full mb-1">
        <div class="w-full p-4 text-sm">

            <!-- Alert untuk status penyimpanan -->
            @if (session()->has('message'))
                <div class="p-4 mb-4 text-green-700 bg-green-100 rounded-lg">
                    {{ session('message') }}
                </div>
            @endif

            <div class="w-full p-2 m-2 mx-auto bg-white rounded-lg shadow-md">

                <!-- FORM DIAGNOSA KEPERAWATAN -->
                <div class="mb-8">
                    <form wire:submit.prevent="simpanFormA">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <!-- Tanggal -->
                            <div>
                                <x-input-label for="formDiagnosaKeperawatan.tanggal" :value="__('Tanggal')"
                                    :required="true" />
                                <div class="grid items-center grid-cols-3 mt-1">
                                    <div class="col-span-2">
                                        <x-text-input id="formDiagnosaKeperawatan.tanggal" type="text"
                                            placeholder="dd/mm/yyyy HH:MM:SS" class="w-full"
                                            wire:model="formDiagnosaKeperawatan.tanggal" />
                                    </div>
                                    @if (empty($formDiagnosaKeperawatan['tanggal']))
                                        <div class="col-span-1 ml-2">
                                            <div wire:loading wire:target="setTanggalFormA">
                                                <x-loading />
                                            </div>
                                            <x-green-button :disabled="false" wire:click.prevent="setTanggalFormA"
                                                type="button" wire:loading.remove>
                                                Set Tanggal
                                            </x-green-button>
                                        </div>
                                    @endif
                                </div>
                                @error('formDiagnosaKeperawatan.tanggal')
                                    <x-input-error :messages="$message" />
                                @enderror
                            </div>

                            <!-- Data Subyektif -->
                            <div class="md:col-span-2">
                                <x-input-label for="formDiagnosaKeperawatan.dataSubyektif" :value="__('Data Subyektif')" />
                                <x-text-input-area rows="3" class="w-full mt-1"
                                    wire:model="formDiagnosaKeperawatan.dataSubyektif"
                                    placeholder="Keluhan / data subyektif pasien..."></x-text-input-area>
                                @error('formDiagnosaKeperawatan.dataSubyektif')
                                    <x-input-error :messages="$message" />
                                @enderror
                            </div>

                            <!-- Data Obyektif -->
                            <div class="md:col-span-2">
                                <x-input-label for="formDiagnosaKeperawatan.dataObyektif" :value="__('Data Obyektif')" />
                                <x-text-input-area rows="3" class="w-full mt-1"
                                    wire:model="formDiagnosaKeperawatan.dataObyektif"
                                    placeholder="Temuan pemeriksaan fisik / penunjang..."></x-text-input-area>
                                @error('formDiagnosaKeperawatan.dataObyektif')
                                    <x-input-error :messages="$message" />
                                @enderror
                            </div>

                            <!-- Diagnosa Keperawatan -->
                            <div class="md:col-span-2">
                                <x-input-label for="formDiagnosaKeperawatan.diagnosaKeperawatan" :value="__('Diagnosa Keperawatan')"
                                    :required="true" />
                                <x-text-input-area rows="3" class="w-full mt-1"
                                    wire:model="formDiagnosaKeperawatan.diagnosaKeperawatan"
                                    placeholder="Tuliskan diagnosa keperawatan..."></x-text-input-area>
                                @error('formDiagnosaKeperawatan.diagnosaKeperawatan')
                                    <x-input-error :messages="$message" />
                                @enderror
                            </div>
                        </div>

                        <!-- TANDA TANGAN PETUGAS FORM DIAGNOSA -->
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
                                    <x-text-input value="Perawat" class="w-full mt-1" disabled />
                                </div>
                            </div>
                        </div> --}}

                        <!-- TOMBOL SIMPAN FORM DIAGNOSA -->
                        <div class="flex justify-end mt-6">
                            <x-primary-button type="submit" class="px-6 py-2">
                                Simpan
                            </x-primary-button>
                        </div>
                    </form>
                </div>

                <!-- FORM INTERVENSI / IMPLEMENTASI -->
                @if ($showFormB)
                    @include('livewire.emr-r-i.diagnosa-keperawatan-r-i.create-diagnosa-keperawatan-implementasi')
                @endif
            </div>

            <!-- RIWAYAT DIAGNOSA & INTERVENSI -->
            <div class="mt-8">
                <h3 class="mb-4 text-xl font-semibold">Diagnosa Keperawatan & Intervensi Implementasi</h3>

                @if (!empty($dataDaftarRi['diagKeperawatan']['formDiagnosaKeperawatan']))
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

                                @foreach ($dataDaftarRi['diagKeperawatan']['formDiagnosaKeperawatan'] as $diag)
                                    <!-- Baris Diagnosa -->
                                    <tr class="border-b group hover:bg-blue-50">
                                        <td class="px-4 py-3 font-semibold">{{ $counter++ }}</td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2 mb-4">
                                                <span class="font-semibold text-green-700">Diagnosa Keperawatan</span>
                                            </div>

                                            <div class="space-y-2">
                                                <!-- Tombol Hapus Diagnosa -->
                                                <x-danger-button
                                                    wire:click="hapusForm('formDiagnosaKeperawatan', '{{ $diag['formDiagnosaKeperawatan_id'] }}')"
                                                    wire:confirm="Apakah Anda yakin ingin menghapus diagnosa ini?"
                                                    wire:loading.attr="disabled" wire:target="hapusForm">

                                                    <span wire:loading.remove wire:target="hapusForm"
                                                        class="flex items-center">
                                                        <svg class="w-6 h-6 mr-1" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                        Hapus Diagnosa
                                                    </span>

                                                    <span wire:loading wire:target="hapusForm"
                                                        class="flex items-center">
                                                        <x-loading class="w-6 h-6 mr-1" />
                                                    </span>
                                                </x-danger-button>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="font-medium text-gray-900">
                                                {{ $diag['tanggal'] ?? '-' }}
                                            </div>

                                            @if (!empty($diag['dataSubyektif']))
                                                <div class="mt-2">
                                                    <div class="text-sm font-semibold text-blue-600">Data Subyektif:
                                                    </div>
                                                    <p class="text-sm text-gray-700">
                                                        {{ $diag['dataSubyektif'] }}
                                                    </p>
                                                </div>
                                            @endif

                                            @if (!empty($diag['dataObyektif']))
                                                <div class="mt-2">
                                                    <div class="text-sm font-semibold text-purple-600">Data Obyektif:
                                                    </div>
                                                    <p class="text-sm text-gray-700">
                                                        {{ $diag['dataObyektif'] }}
                                                    </p>
                                                </div>
                                            @endif

                                            <div class="mt-2">
                                                <div class="text-sm font-semibold text-red-600">Diagnosa Keperawatan:
                                                </div>
                                                <p class="text-sm text-gray-700">
                                                    {{ $diag['diagnosaKeperawatan'] }}
                                                </p>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $diag['tandaTanganPetugas']['petugasName'] ?? '' }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $diag['tandaTanganPetugas']['jabatan'] ?? '' }}
                                            </div>
                                            <div class="text-xs text-gray-400">
                                                {{ $diag['tandaTanganPetugas']['petugasCode'] ?? '' }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex flex-col gap-2">
                                                <div class="grid grid-cols-1 gap-2">
                                                    <!-- Tombol Tambah Intervensi -->
                                                    <x-green-button
                                                        wire:click="tambahFormB('{{ $diag['formDiagnosaKeperawatan_id'] }}')"
                                                        wire:loading.attr="disabled" wire:target="tambahFormB"
                                                        title="Tambah Intervensi">

                                                        <span wire:loading.remove wire:target="tambahFormB"
                                                            class="flex items-center">
                                                            <svg class="w-6 h-6 mr-1" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                            </svg>
                                                            Tambah Intervensi / Implementasi
                                                        </span>

                                                        <span wire:loading wire:target="tambahFormB"
                                                            class="flex items-center">
                                                            <x-loading class="w-6 h-6 mr-1" />
                                                        </span>
                                                    </x-green-button>

                                                    <!-- Tombol Cetak Diagnosa -->
                                                    {{-- <x-yellow-button
                                                        wire:click="cetakFormA('{{ $diag['formDiagnosaKeperawatan_id'] }}')"
                                                        wire:loading.attr="disabled" wire:target="cetakFormA"
                                                        class="flex-1 px-2 py-1" title="Cetak Diagnosa">

                                                        <span wire:loading.remove wire:target="cetakFormA"
                                                            class="flex items-center">
                                                            <svg class="w-6 h-6 mr-1" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                            </svg>
                                                            Cetak Diagnosa
                                                        </span>

                                                        <span wire:loading wire:target="cetakFormA"
                                                            class="flex items-center">
                                                            <x-loading class="w-6 h-6 mr-1" />
                                                        </span>
                                                    </x-yellow-button> --}}
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Baris Intervensi / Implementasi terkait -->
                                    @php
                                        $relatedFormBs = collect(
                                            $dataDaftarRi['diagKeperawatan']['formIntervensiImplementasi'] ?? [],
                                        )
                                            ->where('formDiagnosaKeperawatan_id', $diag['formDiagnosaKeperawatan_id'])
                                            ->values();
                                    @endphp

                                    @foreach ($relatedFormBs as $formB)
                                        <tr class="border-b group hover:bg-green-50">
                                            <td class="px-4 py-3 font-semibold">{{ $counter++ }}</td>
                                            <td class="px-4 py-3">
                                                <div class="flex items-center gap-2 mb-4">
                                                    <span class="font-semibold text-green-700">Intervensi /
                                                        Implementasi</span>
                                                </div>

                                                <div>
                                                    <!-- Tombol Hapus Intervensi -->
                                                    <x-danger-button
                                                        wire:click="hapusForm('formIntervensiImplementasi', '{{ $formB['formIntervensiImplementasi_id'] }}')"
                                                        wire:confirm="Apakah Anda yakin ingin menghapus intervensi ini?"
                                                        wire:loading.attr="disabled" wire:target="hapusForm">

                                                        <span wire:loading.remove wire:target="hapusForm"
                                                            class="flex items-center">
                                                            <svg class="w-4 h-4 mr-1" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                            Hapus
                                                        </span>

                                                        <span wire:loading wire:target="hapusForm"
                                                            class="flex items-center">
                                                            <x-loading class="w-4 h-4 mr-1" />
                                                        </span>
                                                    </x-danger-button>
                                                </div>
                                            </td>

                                            <td class="px-4 py-3">
                                                <div class="font-medium text-gray-900">{{ $formB['tanggal'] ?? '-' }}
                                                </div>

                                                @if (!empty($formB['intervensi']))
                                                    <div class="mt-2">
                                                        <div class="text-sm font-semibold text-blue-600">Intervensi:
                                                        </div>
                                                        <p class="text-sm text-gray-700">
                                                            {{ $formB['intervensi'] }}
                                                        </p>
                                                    </div>
                                                @endif

                                                @if (!empty($formB['implementasi']))
                                                    <div class="mt-2">
                                                        <div class="text-sm font-semibold text-green-600">Implementasi:
                                                        </div>
                                                        <p class="text-sm text-gray-700">
                                                            {{ $formB['implementasi'] }}
                                                        </p>
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $formB['tandaTanganPetugas']['petugasName'] ?? '' }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $formB['tandaTanganPetugas']['jabatan'] ?? '' }}
                                                </div>
                                                <div class="text-xs text-gray-400">
                                                    {{ $formB['tandaTanganPetugas']['petugasCode'] ?? '' }}
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="grid grid-cols-1 gap-2">
                                                    <!-- Tombol Cetak Intervensi -->
                                                    {{-- <x-yellow-button
                                                        wire:click="cetakFormB('{{ $formB['formIntervensiImplementasi_id'] }}')"
                                                        wire:loading.attr="disabled" wire:target="cetakFormB"
                                                        title="Cetak Intervensi / Implementasi">

                                                        <span wire:loading.remove wire:target="cetakFormB"
                                                            class="flex items-center">
                                                            <svg class="w-4 h-4 mr-1" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                            </svg>
                                                            Cetak
                                                        </span>

                                                        <span wire:loading wire:target="cetakFormB"
                                                            class="flex items-center">
                                                            <x-loading class="w-4 h-4 mr-1" />
                                                        </span>
                                                    </x-yellow-button> --}}
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach

                                    @if (!$loop->last)
                                        <tr>
                                            <td colspan="6" class="px-4 py-2">
                                                <div class="border-t border-gray-300"></div>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-8 text-center text-gray-500 bg-gray-100 rounded-lg">
                        Belum ada data Diagnosa Keperawatan. Silakan isi form diagnosa terlebih dahulu.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
