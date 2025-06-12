@php
    $disabledProperty = true;
    $disabledPropertyRiStatus = $statusRiRef['statusId'] !== 'I' ? true : false;

@endphp

<div>
    {{-- Stop trying to control. --}}
    {{-- @if ($formRujukanRefBPJSStatus)
        @include('livewire.daftar-r-i.form-entry-r-i.form-rujukanRefBpjs')
    @endif --}}

    {{-- Transasi Rawat Jalan --}}
    <div id="TransaksiRawatInap" class="px-4">
        <x-border-form :title="'Pendaftaran RI'" :align="'start'" :bgcolor="'bg-white'" class="mr-0">

            <div class="grid grid-cols-2 gap-2">
                <div>
                    {{-- Tanggal Masuk --}}
                    <x-input-label :value="'Tanggal Masuk'" />
                    <div class="px-3 py-2 mt-1 ml-2 text-sm text-gray-800 bg-gray-100 border rounded">
                        {{ $dataDaftarRi['entryDate'] ?? '-' }}
                    </div>
                </div>
                <div>
                    <x-input-label :value="'Tanggal Keluar'" />
                    <div class="px-3 py-2 mt-1 ml-2 text-sm text-gray-800 bg-gray-100 border rounded">
                        {{ $dataDaftarRi['exitDate'] ?? '-' }}
                    </div>
                </div>
            </div>

            {{-- Pasien --}}
            <x-input-label :value="'Nomor Registrasi Pasien'" />
            <div class="px-3 py-2 mt-1 ml-2 text-sm text-gray-800 bg-gray-100 border rounded">
                {{ $dataDaftarRi['regNo'] ?? '-' }}
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    {{-- Jenis Klaim --}}
                    <x-input-label :value="'Jenis Klaim'" />
                    <div class="px-3 py-2 mt-1 ml-2 text-sm text-gray-800 bg-gray-100 border rounded">
                        {{ $JenisKlaim['JenisKlaimId'] ?? '-' }} - {{ $JenisKlaim['JenisKlaimDesc'] ?? '-' }}
                    </div>
                </div>
                <div>
                    {{-- No Referensi --}}
                    <x-input-label :value="'No Referensi'" />
                    <div class="px-3 py-2 mt-1 ml-2 text-sm text-gray-800 bg-gray-100 border rounded">
                        {{ $dataDaftarRi['noReferensi'] ?? '-' }}
                    </div>
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

            <div class="grid grid-cols-3 gap-2">
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
                <div>
                    {{-- Nomor Bed --}}
                    <x-input-label :value="'Nomor Bed'" />
                    <div class="px-3 py-2 mt-1 ml-2 text-sm text-gray-800 bg-gray-100 border rounded">
                        {{ $dataDaftarRi['bedNo'] ?? '-' }}
                    </div>
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
                <x-yellow-button wire:click.prevent="cetakSEP()" type="button" wire:loading.remove>Cetak
                    SEP</x-yellow-button>
                <div wire:loading wire:target="cetakSEP"><x-loading /></div>
            </div>

        </x-border-form>
    </div>





</div>
