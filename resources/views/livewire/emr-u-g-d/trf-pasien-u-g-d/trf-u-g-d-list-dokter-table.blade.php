<div class="relative overflow-x-auto shadow-md sm:rounded-lg">
    <table class="w-full text-sm text-left text-gray-900 table-auto dark:text-gray-400">
        <thead class="text-sm text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th class="px-4 py-2 text-center">DPJP / Dokter</th>
                <th class="px-4 py-2 text-center">Level</th>
                <th class="px-4 py-2 text-center">Aksi</th>
            </tr>
        </thead>

        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">

            @php
                $levelingDokterTrfUgd = data_get($dataDaftarUgd, 'trfUgd.levelingDokter', []);
            @endphp

            @forelse($levelingDokterTrfUgd as $index => $lvl)
                @if (!empty($lvl['drName']))
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-600">

                        {{-- Nama Dokter --}}
                        <td class="px-4 py-2 font-medium text-center">
                            {{ $lvl['drName'] ?? '-' }}
                        </td>

                        {{-- Level Dokter --}}
                        <td class="px-4 py-2 text-center">
                            @if (($lvl['levelDokter'] ?? '') === 'Utama')
                                <span class="px-2 py-1 text-white bg-green-600 rounded">Utama</span>
                            @elseif (($lvl['levelDokter'] ?? '') === 'RawatGabung')
                                <span class="px-2 py-1 text-white bg-green-600 rounded">Rawat Gabung</span>
                            @else
                                {{ $lvl['levelDokter'] ?? '-' }}
                            @endif
                        </td>

                        {{-- Tombol Aksi --}}
                        <td class="px-4 py-2 text-center">

                            {{-- Set Utama --}}
                            <button wire:click="setLevelingDokterUtama({{ $index }})"
                                class="px-2 py-1 text-white bg-green-500 rounded hover:bg-green-700 text-[10px]">
                                Set Utama
                            </button>

                            {{-- Set Rawat Gabung --}}
                            <button wire:click="setLevelingDokterRawatGabung({{ $index }})"
                                class="px-2 py-1 text-white bg-gray-500 rounded hover:bg-gray-700 text-[10px]">
                                Rawat Gabung
                            </button>

                            {{-- Hapus --}}
                            <button wire:click="removeLevelingDokter('{{ $lvl['tglEntry'] }}')"
                                class="px-2 py-1 text-white bg-red-500 rounded hover:bg-red-700 text-[10px]">
                                Hapus
                            </button>

                        </td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="3" class="px-4 py-2 text-center text-gray-500">
                        Tidak ada data dokter TRF UGD yang tersedia.
                    </td>
                </tr>
            @endforelse
        </tbody>

    </table>
</div>
