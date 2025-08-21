<div>
    <!-- Tampilkan detail resep (eresep) jika ada -->
    <table class="w-full text-sm text-left text-gray-500 table-fixed dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th class="w-1/2 px-4 py-2">NonRacikan</th>
            </tr>
        </thead>
        <tbody>
            @if (isset($header['eresepRacikan']) && count($header['eresepRacikan']) > 0)
                @php
                    $myPreviousRow = '';
                @endphp
                @foreach ($header['eresepRacikan'] as $i => $detail)
                    @php
                        $rowId = $detail['riObatDtl'] ?? $i;
                        $hdrId = $header['resepNo'] ?? 'hdr';

                        $myRacikanBorder = $myPreviousRow !== $detail['noRacikan'] ? 'border-t-2 ' : '';

                        if (isset($detail['jenisKeterangan'])) {
                            $catatan = $detail['catatan'] ?? '';
                            $catatanKhusus = $detail['catatanKhusus'] ?? '';
                            $noRacikan = $detail['noRacikan'] ?? '';
                            $productName = $detail['productName'] ?? '';
                            $qty = $detail['qty'] ?? null;

                            $jmlRacikan = $qty
                                ? 'Jml Racikan ' . $qty . ' | ' . $catatan . ' | S ' . $catatanKhusus . PHP_EOL
                                : '';

                            $dosis = $detail['dosis'] ?? '';

                            // Inisialisasi $detailRacikan jika belum ada
                            $detailRacikan = $noRacikan . '/ ' . $productName . ' - ' . $dosis . PHP_EOL . $jmlRacikan;
                        } else {
                            $detailRacikan = '';
                        }
                    @endphp

                    <tr wire:key="eresep-racikan-{{ $hdrId }}-{{ $rowId }}"
                        class="{{ $myRacikanBorder }} group">
                        <td class="w-1/2 px-4 py-2">
                            {{ $detailRacikan }}
                        </td>
                    </tr>
                    @php
                        $myPreviousRow = $detail['noRacikan'];
                    @endphp
                @endforeach
            @else
                <tr class="border-b dark:border-gray-700">
                    <td class="w-1/2 px-4 py-2">
                        Belum ada resep racikan.
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
