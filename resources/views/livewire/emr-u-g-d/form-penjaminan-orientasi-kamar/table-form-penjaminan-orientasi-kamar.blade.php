<div class="h-[calc(100vh-250px)] mt-2 overflow-auto">
    <table class="w-full text-sm text-left text-gray-700 table-auto">
        <thead class="text-xs text-gray-900 uppercase bg-gray-100">
            <tr>
                <th scope="col" class="px-4 py-3">
                    Penjaminan & Kelas Kamar
                </th>
                <th scope="col" class="px-4 py-3">
                    Atas Nama
                </th>
                <th scope="col" class="px-4 py-3">
                    Action
                </th>
            </tr>
        </thead>

        <tbody class="bg-white">
            @isset($dataDaftarUgd['formPenjaminanOrientasiKamar'])
                @foreach ($dataDaftarUgd['formPenjaminanOrientasiKamar'] as $row)
                    @php
                        // Safeguard kalau key tidak ada
                        $jenisPenjaminCode = $row['jenisPenjamin'] ?? null;
                        $jenisPenjaminLabel = $jenisPenjaminCode;

                        if (isset($jenisPenjaminOptions)) {
                            $jenis = collect($jenisPenjaminOptions)->firstWhere('id', $jenisPenjaminCode);
                            if ($jenis) {
                                $jenisPenjaminLabel = $jenis['desc'];
                            }
                        }

                        $kelasKode = $row['kelasKamar'] ?? null;
                        $kelasData = $kelasKamarOptions[$kelasKode] ?? null;
                    @endphp

                    <tr class="border-b group dark:border-gray-700">

                        {{-- Kolom 1: Penjaminan & Kamar --}}
                        <td class="px-4 py-3 group-hover:bg-gray-100 whitespace-nowrap dark:text-white">
                            <div class="text-sm text-primary">
                                <span class="font-semibold">Tanggal Form:</span>
                                {{ $row['tanggalFormPenjaminan'] ?? '-' }}
                                <br>

                                <span class="font-semibold">Jenis Penjamin:</span>
                                {{ $jenisPenjaminLabel ?? '-' }}
                                <br>

                                @if (!empty($row['asuransiLain'] ?? null))
                                    <span class="font-semibold">Asuransi Lain:</span>
                                    {{ $row['asuransiLain'] }}
                                    <br>
                                @endif

                                <span class="font-semibold">Kelas Kamar:</span>
                                {{ $kelasData['nama'] ?? ($row['kelasKamar'] ?? '-') }}
                                <br>

                                @if (!empty($kelasData['tarifLabel'] ?? null))
                                    <span class="font-semibold">Tarif:</span>
                                    {{ $kelasData['tarifLabel'] }}
                                    <br>
                                @endif

                                @if (!empty($kelasData['fasilitas'] ?? null) && is_array($kelasData['fasilitas']))
                                    <span class="font-semibold">Fasilitas:</span>
                                    {{ implode(', ', $kelasData['fasilitas']) }}
                                    <br>
                                @endif

                                <span class="font-semibold">Orientasi Kamar Dijelaskan:</span>
                                {{ !empty($row['orientasiKamarDijelaskan']) ? 'Ya' : 'Belum' }}
                                <br>
                            </div>
                        </td>

                        {{-- Kolom 2: Atas Nama --}}
                        <td class="px-4 py-3 group-hover:bg-gray-100 group-hover:text-primary">
                            <span class="font-semibold">Pembuat Pernyataan:</span>
                            {{ $row['pembuatNama'] ?? '-' }}
                            <br>

                            <span class="font-semibold">Umur Pembuat:</span>
                            {{ $row['pembuatUmur'] ?? '-' }}
                            <br>

                            <span class="font-semibold">Jenis Kelamin Pembuat:</span>
                            @php
                                $jkPembuat = $row['pembuatJenisKelamin'] ?? null;
                            @endphp
                            {{ $jkPembuat === 'L' ? 'Laki-laki' : ($jkPembuat === 'P' ? 'Perempuan' : '-') }}
                            <br>

                            <span class="font-semibold">Hubungan dengan Pasien:</span>
                            {{ $row['hubunganDenganPasien'] ?? '-' }}
                            <br>

                            <span class="font-semibold">Pasien:</span>
                            {{ $row['pasienNama'] ?? '-' }}
                            <br>

                            <span class="font-semibold">Umur Pasien:</span>
                            {{ $row['pasienUmur'] ?? '-' }}
                            <br>

                            <span class="font-semibold">Alamat Pasien:</span>
                            {{ $row['pasienAlamat'] ?? '-' }}
                            <br>

                            <span class="font-semibold">Tanggal Ttd Pembuat:</span>
                            {{ $row['signaturePembuatDate'] ?? '-' }}
                            <br>
                        </td>

                        {{-- Kolom 3: Action --}}
                        <td class="px-4 py-3 group-hover:bg-gray-100 group-hover:text-primary">
                            <div class="grid w-full grid-cols-1 px-4 pb-4">
                                @if (!empty($row['signaturePembuatDate'] ?? null))
                                    <x-primary-button
                                        wire:click.stop="cetakFormPenjaminan('{{ $row['signaturePembuatDate'] }}')"
                                        wire:loading.attr="disabled"
                                        class="relative flex items-center justify-center gap-2 text-white">
                                        <div wire:loading wire:target="cetakFormPenjaminan">
                                            <x-loading />
                                        </div>
                                        <span wire:loading.remove wire:target="cetakFormPenjaminan">
                                            Cetak Form Penjaminan & Orientasi Kamar
                                        </span>
                                    </x-primary-button>
                                @else
                                    <span class="text-xs text-gray-500">
                                        Belum ada tanda tangan pembuat.
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            @endisset
        </tbody>
    </table>
</div>
