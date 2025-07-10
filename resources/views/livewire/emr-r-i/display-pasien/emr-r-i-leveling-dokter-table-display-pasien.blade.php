<div class="relative overflow-x-auto shadow-md sm:rounded-lg">
    <table class="w-full text-xs text-left text-gray-900 table-auto dark:text-gray-400">
        <thead class="text-sm text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-4 py-2 text-center">
                    DPJP
                </th>
                <th scope="col" class="px-4 py-2 text-center">
                    Level
                </th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
            @php
                $levelingDokter = data_get(
                    json_decode($myQData, true),
                    'pengkajianAwalPasienRawatInap.levelingDokter',
                    [],
                );
            @endphp

            @forelse($levelingDokter as $lvl)
                @if (!empty($lvl['drName']))
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="px-4 py-2 font-medium text-center">
                            {{ $lvl['drName'] }}
                        </td>
                        <td class="px-4 py-2 text-center">
                            {{ $lvl['levelDokter'] }}
                        </td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="2" class="px-4 py-2 text-center text-gray-500">
                        Tidak ada data leveling dokter yang tersedia.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
