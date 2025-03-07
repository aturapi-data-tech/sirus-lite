<div>
    <!-- Tampilkan detail resep (eresep) jika ada -->
    <table class="w-full text-sm text-left text-gray-500 table-fixed dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th class="w-1/2 px-4 py-2">NonRacikan</th>
            </tr>
        </thead>
        <tbody>
            @if (isset($header['eresep']) && count($header['eresep']) > 0)
                @foreach ($header['eresep'] as $detail)
                    @php
                        $catatanKhusus = $detail['catatanKhusus'] ? ' (' . $detail['catatanKhusus'] . ')' : '';
                    @endphp

                    <tr class="border-b dark:border-gray-700">
                        <td class="w-1/2 px-4 py-2">
                            {{ 'R/' . ' ' . $detail['productName'] . ' | No. ' . $detail['qty'] . ' | S ' . $detail['signaX'] . 'dd' . $detail['signaHari'] . $catatanKhusus }}
                        </td>
                    </tr>
                @endforeach
            @else
                <tr class="border-b dark:border-gray-700">
                    <td class="w-1/2 px-4 py-2">
                        Belum ada resep non racikan.
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
