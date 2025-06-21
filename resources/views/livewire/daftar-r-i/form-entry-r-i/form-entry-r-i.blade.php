@php
    $disabledPropertyRiStatus = false;
@endphp
<div>
    {{-- Stop trying to control. --}}
    {{-- @if ($formRujukanRefBPJSStatus)
        @include('livewire.daftar-r-i.form-entry-r-i.form-rujukanRefBpjs')
    @endif --}}

    {{-- Transasi Rawat Jalan --}}
    <div id="TransaksiRawatInap" class="px-4">
        <x-border-form :title="'Pendaftaran RI'" :align="'start'" :bgcolor="'bg-white'" class="mr-0">

            <div class="grid grid-cols-3 gap-2">
                <div>
                    {{-- Tanggal Masuk --}}
                    <x-input-label :value="'Tanggal Masuk'" />
                    <div class="px-3 py-2 mt-1 ml-2 text-sm text-gray-800 bg-gray-100 border rounded">
                        {{ $dataDaftarRi['entryDate'] ?? '-' }}
                    </div>
                </div>
                {{-- <div>
                    <x-input-label :value="'Tanggal Keluar'" />
                    <div class="px-3 py-2 mt-1 ml-2 text-sm text-gray-800 bg-gray-100 border rounded">
                        {{ $dataDaftarRi['exitDate'] ?? '-' }}
                    </div>
                </div> --}}

                <div>
                    {{-- Status Umur di Bawah 14 Tahun --}}
                    <x-input-label :value="'Status Umur < 14 Tahun'" />
                    <div class="px-3 py-2 mt-1 ml-2 text-sm text-gray-800 bg-gray-100 border rounded">
                        {{ ($dataDaftarRi['k14th']['status'] ?? '-') === 'Y' ? 'Ya (di bawah 14 tahun)' : 'Tidak' }}
                    </div>
                </div>

                <div>
                    {{-- Kasus Polisi --}}
                    <x-input-label :value="'Kasus Polisi'" />
                    <div class="px-3 py-2 mt-1 ml-2 text-sm text-gray-800 bg-gray-100 border rounded">
                        {{ ($dataDaftarRi['kPolisi']['status'] ?? '-') === 'Y' ? 'Ya (kasus polisi)' : 'Tidak' }}
                    </div>
                </div>
            </div>

            {{-- Pasien --}}
            {{-- <x-input-label :value="'Nomor Registrasi Pasien'" />
            <div class="px-3 py-2 mt-1 ml-2 text-sm text-gray-800 bg-gray-100 border rounded">
                {{ $dataDaftarRi['regNo'] ?? '-' }}
            </div> --}}

            <div class="grid grid-cols-2 gap-2">
                <div>
                    {{-- Jenis Klaim --}}
                    <x-input-label :value="'Jenis Klaim'" />
                    <div class="px-3 py-2 mt-1 ml-2 text-sm text-gray-800 bg-gray-100 border rounded">
                        {{ $dataDaftarRi['klaimId'] ?? '-' }} - {{ $dataDaftarRi['klaimDesc'] ?? '-' }}
                    </div>
                </div>
                <div>
                    {{-- No Referensi --}}
                    {{-- Nomor SPRI --}}
                    <x-input-label :value="'Nomor SPRI'" :required="true" />

                    <div class="flex items-center mb-2">
                        <x-text-input placeholder="Nomor SPRI (SKDP Dokter)" class="mt-1 ml-2" :errorshas="$errors->has('dataDaftarRi.noReferensi')"
                            :disabled="$disabledPropertyRiStatus || filled($dataDaftarRi['sep']['noSep'] ?? null)" wire:model.debounce.500ms="dataDaftarRi.noReferensi"
                            wire:loading.attr="disabled" />
                    </div>

                    {{-- @if (empty($dataDaftarRi['sep']['noSep'])) --}}
                    <div class="flex justify-between">
                        <x-green-button :disabled="$disabledPropertyRiStatus" wire:click.prevent="create()" type="button">
                            Ambil Nomor SPRI
                        </x-green-button>
                        <div wire:loading wire:target="create">
                            <x-loading />
                        </div>
                    </div>

                    <p class="mt-1 text-xs text-gray-500">
                        Diisi dengan: Nomor SPRI (Surat Perintah Rawat Inap) dari dokter yang mencetak di aplikasi
                        P-Care atau Edklaim.
                    </p>
                    @if ($isOpen)
                        @include('livewire.daftar-r-i.form-entry-r-i.create')
                    @endif
                    {{-- @endif --}}

                </div>
            </div>

            {{-- Dokter --}}
            <x-input-label :value="'Dokter'" />
            <div class="px-3 py-2 mt-1 ml-2 text-sm text-gray-800 bg-gray-100 border rounded">
                {{ $dataDaftarRi['drId'] ?? '-' }} - {{ $dataDaftarRi['drDesc'] ?? '-' }}
            </div>

            {{-- Keterangan Masuk --}}
            <x-input-label :value="'Keterangan Masuk'" />
            <div class="px-3 py-2 mt-1 ml-2 text-sm text-gray-800 bg-gray-100 border rounded">
                {{ $dataDaftarRi['entryId'] ?? '-' }} - {{ $dataDaftarRi['entryDesc'] ?? '-' }}
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    {{-- Bangsal --}}
                    <x-input-label :value="'Bangsal'" />
                    <div class="px-3 py-2 mt-1 ml-2 text-sm text-gray-800 bg-gray-100 border rounded">
                        {{ $dataDaftarRi['bangsalId'] ?? '-' }} - {{ $dataDaftarRi['bangsalDesc'] ?? '-' }}
                    </div>
                </div>
                <div>

                    {{-- Ruangan --}}
                    <x-input-label :value="'Ruangan'" />
                    <div class="px-3 py-2 mt-1 ml-2 text-sm text-gray-800 bg-gray-100 border rounded">
                        {{ $dataDaftarRi['roomId'] ?? '-' }} - {{ $dataDaftarRi['roomDesc'] ?? '-' }}
                    </div>
                </div>
            </div>

            <div>
                {{-- Nomor Bed --}}
                <x-input-label :value="'Nomor Bed'" />
                <div class="px-3 py-2 mt-1 ml-2 text-sm text-gray-800 bg-gray-100 border rounded">
                    {{ $dataDaftarRi['bedNo'] ?? '-' }}
                </div>
            </div>

            {{-- No SEP --}}
            <x-input-label :value="'No SEP'" />
            <div class="px-3 py-2 mt-1 ml-2 text-sm text-gray-800 bg-gray-100 border rounded">
                {{ $dataDaftarRi['sep']['noSep'] ?? '-' }}
            </div>

            {{-- Tombol Cetak SEP --}}
            <div class="flex items-center justify-end my-4">
                @php
                    $dataPasienRegNo = $dataPasien['pasien']['regNo'] ?? '110750Z';
                @endphp
                <livewire:cetak.cetak-etiket :regNo="$dataPasienRegNo" :wire:key="$dataPasienRegNo" />
                <x-yellow-button wire:click.prevent="cetakSEPRI()" type="button" wire:loading.remove>Cetak
                    SEP</x-yellow-button>
                <div wire:loading wire:target="cetakSEPRI">
                    <x-loading />
                </div>
            </div>

        </x-border-form>
    </div>





</div>
