<div class="h-[calc(100vh-250px)] mt-2 overflow-auto">
    <table class="w-full text-sm text-left text-gray-700 table-auto">
        <thead class="text-xs text-gray-900 uppercase bg-gray-100">
            <tr>
                <th scope="col" class="px-4 py-3">Tanggal & Pemberi Informasi</th>
                <th scope="col" class="px-4 py-3">Penerima Informasi</th>
                <th scope="col" class="px-4 py-3">Dokter Pelaksana</th>
                <th scope="col" class="px-4 py-3">Ringkasan Informasi</th>
                <th scope="col" class="px-4 py-3">Action</th>
            </tr>
        </thead>

        <tbody class="bg-white">
            @php
                $labels = [
                    'diagnosis' => 'Diagnosis (WD & DD)',
                    'dasar' => 'Dasar Diagnosis',
                    'tindakan' => 'Tindakan Kedokteran',
                    'indikasi' => 'Indikasi Tindakan',
                    'tatacara' => 'Tata Cara',
                    'tujuan' => 'Tujuan',
                    'risiko' => 'Risiko',
                    'komplikasi' => 'Komplikasi',
                    'prognosis' => 'Prognosis',
                    'alternatif' => 'Alternatif & Risiko',
                ];
            @endphp

            @if (!empty($dataDaftarRi['edukasiPasien']) && is_array($dataDaftarRi['edukasiPasien']))
                @foreach ($dataDaftarRi['edukasiPasien'] as $key => $row)
                    <tr class="border-b group dark:border-gray-700"
                        wire:click="setEdukasiPasien({{ json_encode($row, JSON_HEX_APOS | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) }})">

                        {{-- Tanggal & Pemberi Informasi --}}
                        <td class="px-4 py-3 group-hover:bg-gray-100 whitespace-nowrap">
                            <div class="text-sm text-primary">
                                <strong>Tanggal:</strong> {{ $row['tglEdukasi'] ?? '-' }}
                            </div>
                            <div class="text-sm">
                                <strong>Petugas:</strong> {{ data_get($row, 'pemberiInformasi.petugasName', '-') }}<br>
                                <strong>Kode:</strong> {{ data_get($row, 'pemberiInformasi.petugasCode', '-') }}<br>
                                <span class="text-xs text-gray-500">
                                    {{ !empty($row['created_at']) ? 'Created: ' . $row['created_at'] : '' }}
                                </span>
                            </div>
                        </td>

                        {{-- Penerima Informasi --}}
                        <td class="px-4 py-3 group-hover:bg-gray-100">
                            <div class="mb-2 text-sm">
                                <strong>Nama:</strong> {{ data_get($row, 'penerimaInformasi.name', '-') }}<br>
                                <strong>Hubungan:</strong> {{ data_get($row, 'penerimaInformasi.hubungan', '-') }}
                            </div>

                            {{-- Signature (jika ada) --}}
                            @php
                                $sig = data_get($row, 'penerimaInformasi.signature');
                            @endphp
                            @if (!empty($sig))
                                <div class="flex flex-col items-center mt-2">
                                    <div
                                        class="flex items-center justify-center w-32 h-16 p-1 overflow-hidden bg-white border border-gray-300 rounded-md shadow-sm">
                                        <div class="w-full h-full">
                                            {!! str_replace(
                                                ['<svg ', 'width="300"', 'height="150"'],
                                                ['<svg class="object-contain w-full h-full" ', 'width="100%"', 'height="100%"'],
                                                $sig,
                                            ) !!}
                                        </div>
                                    </div>
                                    <span class="mt-1 text-xs text-gray-500">Tanda Tangan</span>
                                </div>
                            @endif
                        </td>

                        {{-- Dokter Pelaksana --}}
                        <td class="px-4 py-3 group-hover:bg-gray-100">
                            <div class="text-sm">
                                <strong>Dokter:</strong> {{ data_get($row, 'dokterPelaksanaTindakan.drName', '-') }}<br>
                                <strong>ID:</strong> {{ data_get($row, 'dokterPelaksanaTindakan.drId', '-') }}
                            </div>
                        </td>

                        {{-- Ringkasan Informasi (tampilkan yang terisi) --}}
                        <td class="px-4 py-3 group-hover:bg-gray-100">
                            @php
                                $detail = data_get($row, 'detailInformasi', []);
                                $filled = [];
                                foreach ($detail as $k => $v) {
                                    $desc = is_array($v) ? $v['desc'] ?? '' : '';
                                    if (trim((string) $desc) !== '') {
                                        $filled[] =
                                            '<strong>' . e($labels[$k] ?? ucfirst($k)) . ':</strong> ' . e($desc);
                                    }
                                }
                            @endphp
                            <div class="text-sm">{!! $filled ? implode('<br>', $filled) : '-' !!}</div>
                        </td>

                        {{-- Action --}}
                        <td class="px-4 py-3 group-hover:bg-gray-100 whitespace-nowrap" x-data>
                            @php $id = $row['id'] ?? null; @endphp

                            <div class="flex items-center gap-2">
                                {{-- Tombol Cetak --}}
                                @if ($id)
                                    <x-primary-button class="inline-flex items-center gap-1" wire:loading.remove
                                        wire:click.stop.prevent="cetakEdukasiPasienById('{{ $id }}')"
                                        wire:loading.attr="disabled" wire:target="cetakEdukasiPasienById">
                                        <svg class="w-5 h-5 text-white" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 9V2h12v7M6 18h12v4H6v-4zM6 14h12a2 2 0 002-2V9H4v3a2 2 0 002 2z" />
                                        </svg>
                                        Cetak
                                    </x-primary-button>

                                    {{-- Loading indicator saat cetak --}}
                                    <div wire:loading wire:target="cetakEdukasiPasienById('{{ $id }}')">
                                        <x-loading />
                                    </div>

                                    {{-- Tombol Hapus --}}
                                    <x-red-button class="inline-flex items-center gap-1" wire:loading.remove
                                        wire:click.stop.prevent="removeEdukasiPasienById('{{ $id }}')"
                                        wire:loading.attr="disabled" wire:target="removeEdukasiPasienById">
                                        <svg class="w-5 h-5 text-white" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                                            <path
                                                d="M17 4h-4V2a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v2H1a1 1 0 0 0 0 2h1v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6h1a1 1 0 1 0 0-2ZM7 2h4v2H7V2Zm1 14a1 1 0 1 1-2 0V8a1 1 0 0 1 2 0v8Zm4 0a1 1 0 0 1-2 0V8a1 1 0 0 1 2 0v8Z" />
                                        </svg>
                                        Hapus
                                    </x-red-button>
                                @else
                                    {{-- fallback data lama tanpa id --}}
                                    <x-red-button class="inline-flex items-center gap-1" wire:loading.remove
                                        wire:click.stop.prevent="removeEdukasiPasien({{ $key }})">
                                        <svg class="w-5 h-5 text-white" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                                            <path
                                                d="M17 4h-4V2a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v2H1a1 1 0 0 0 0 2h1v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6h1a1 1 0 1 0 0-2ZM7 2h4v2H7V2Zm1 14a1 1 0 1 1-2 0V8a1 1 0 0 1 2 0v8Zm4 0a1 1 0 0 1-2 0V8a1 1 0 0 1 2 0v8Z" />
                                        </svg>
                                        Hapus
                                    </x-red-button>
                                @endif
                            </div>
                        </td>

                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="5" class="px-4 py-3 text-center text-gray-500">
                        Tidak ada data edukasi pasien.
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
