<table class="w-full text-sm text-left text-gray-900 table-auto dark:text-gray-400">
    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
        <tr>
            <th scope="col" class="px-2 py-2 text-center">
                Nama Dokter
            </th>
            <th scope="col" class="px-2 py-2 text-center">
                Deskripsi
            </th>
            <th scope="col" class="px-2 py-2 text-center">
                Level Dokter
            </th>
        </tr>
    </thead>
    <tbody class="bg-white dark:bg-gray-800">
        @php
            $levelingDokter = $datadaftar_json['pengkajianAwalPasienRawatInap']['levelingDokter'] ?? [];
        @endphp

        @if (!empty($levelingDokter))
            @foreach ($levelingDokter as $key => $lvlDokter)
                @if (!empty($lvlDokter['drName']))
                    <tr class="border-b group dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="px-2 py-2 text-center group-hover:bg-gray-100 group-hover:text-primary">
                            {{ $lvlDokter['drName'] ?? '-' }}
                        </td>
                        <td class="px-2 py-2 text-center group-hover:bg-gray-100 group-hover:text-primary">
                            {{ $lvlDokter['poliDesc'] ?? '-' }}
                        </td>
                        <td class="px-2 py-2 text-center group-hover:bg-gray-100 group-hover:text-primary">
                            {{ $lvlDokter['levelDokter'] ?? '-' }}
                        </td>
                    </tr>
                @endif
            @endforeach
        @else
            <tr class="border-b group dark:border-gray-700">
                <td colspan="7" class="px-2 py-2 text-center group-hover:bg-gray-100 group-hover:text-primary">
                    Tidak ada data leveling dokter yang tersedia.
                </td>
            </tr>
        @endif
    </tbody>
</table>
