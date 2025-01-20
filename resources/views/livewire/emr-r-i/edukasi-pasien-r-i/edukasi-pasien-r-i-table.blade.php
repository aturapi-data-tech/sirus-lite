<div class="h-[calc(100vh-250px)] mt-2 overflow-auto">
    <!-- Table -->
    <table class="w-full text-sm text-left text-gray-700 table-auto">
        <thead class="text-xs text-gray-900 uppercase bg-gray-100">
            <tr>
                <th scope="col" class="px-4 py-3">Tanggal & Petugas Edukasi</th>
                <th scope="col" class="px-4 py-3">Sasaran Edukasi</th>
                <th scope="col" class="px-4 py-3">Kategori & Keterangan Edukasi</th>
                <th scope="col" class="px-4 py-3">Status Edukasi</th>
                <th scope="col" class="px-4 py-3">Action</th>
            </tr>
        </thead>

        <tbody class="bg-white">
            @isset($dataDaftarRi['edukasiPasien'])
                @foreach ($dataDaftarRi['edukasiPasien'] as $key => $edukasi)
                    <tr class="border-b group dark:border-gray-700"
                        wire:click="setEdukasiPasien({{ json_encode($edukasi, true) }})">

                        <!-- Tanggal & Petugas Edukasi (Gabungan) -->
                        <td class="px-4 py-3 group-hover:bg-gray-100 whitespace-nowrap dark:text-white">
                            <div class="text-sm text-primary">
                                <strong>Tanggal:</strong> {{ $edukasi['tglEdukasi'] ?? '-' }}
                            </div>
                            <div class="text-sm">
                                <strong>Petugas:</strong> {{ $edukasi['petugasEdukasi'] ?? '-' }}
                                </br>
                                <strong>Kode:</strong> {{ $edukasi['petugasEdukasiCode'] ?? '-' }}
                            </div>
                        </td>

                        <!-- Sasaran Edukasi -->
                        <td class="px-4 py-3 group-hover:bg-gray-100">
                            <div class="text-sm">
                                <strong>Sasaran:</strong> {{ $edukasi['sasaranEdukasi'] ?? '-' }}
                                </br>
                                <strong>Hubungan:</strong> {{ $edukasi['hubunganSasaranEdukasidgnPasien'] ?? '-' }}
                            </div>
                        </td>

                        <!-- Kategori & Keterangan Edukasi (Gabungan) -->
                        <td class="px-4 py-3 group-hover:bg-gray-100">
                            <div class="text-sm">
                                @if (!empty($edukasi['edukasi']['kategoriEdukasi']))
                                    <strong>Kategori:</strong>
                                    </br>
                                    @foreach ($edukasi['edukasi']['kategoriEdukasi'] as $index => $kategori)
                                        - {{ $kategori['kategoriEdukasi'] }}<br>
                                    @endforeach
                                @else
                                    <strong>Kategori:</strong> -
                                @endif
                                <strong>Keterangan:</strong> {{ $edukasi['edukasi']['keteranganEdukasi'] ?? '-' }}
                            </div>
                        </td>

                        <!-- Status Edukasi -->
                        <td class="px-4 py-3 group-hover:bg-gray-100">
                            <div class="text-sm">
                                <strong>Status:</strong> {{ $edukasi['edukasi']['statusEdukasi'] ?? '-' }}
                                </br>
                                <strong>Re-Edukasi:</strong>
                                {{ isset($edukasi['edukasi']['reEdukasi']['perlu']) ? ($edukasi['edukasi']['reEdukasi']['perlu'] ? 'Perlu' : 'Tidak Perlu') : '-' }}
                                </br>
                                <strong>Tanggal Re-Edukasi:</strong>
                                {{ $edukasi['edukasi']['reEdukasi']['tglReEdukasi'] ?? '-' }}
                                </br>
                                <strong>Petugas Re-Edukasi:</strong>
                                {{ $edukasi['edukasi']['reEdukasi']['petugasReEdukasi'] ?? '-' }}
                            </div>
                        </td>

                        <!-- Action -->
                        <td class="px-4 py-3 group-hover:bg-gray-100 whitespace-nowrap">
                            <x-alternative-button class="inline-flex"
                                wire:click.prevent="removeEdukasiPasien({{ $key }})">
                                <svg class="w-5 h-5 text-gray-800" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                    fill="currentColor" viewBox="0 0 18 20">
                                    <path
                                        d="M17 4h-4V2a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v2H1a1 1 0 0 0 0 2h1v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6h1a1 1 0 1 0 0-2ZM7 2h4v2H7V2Zm1 14a1 1 0 1 1-2 0V8a1 1 0 0 1 2 0v8Zm4 0a1 1 0 0 1-2 0V8a1 1 0 0 1 2 0v8Z" />
                                </svg>
                                Hapus
                            </x-alternative-button>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="5" class="px-4 py-3 text-center text-gray-500">
                        Tidak ada data edukasi pasien.
                    </td>
                </tr>
            @endisset
        </tbody>
    </table>
</div>
