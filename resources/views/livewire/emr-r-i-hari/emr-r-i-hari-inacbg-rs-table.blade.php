<div class="grid grid-cols-2 gap-2">
    @php
        $datadaftar_json = json_decode($myQData->datadaftarri_json, true) ?? [];
    @endphp
    {{-- LEFT: Diagnosa & Tarif RS --}}
    <div class="p-2 space-y-2 overflow-x-auto bg-white rounded-lg shadow">
        {{-- Section title --}}
        <h5 class="text-sm font-semibold text-gray-700">Detail RS</h5>

        {{-- Diagnosa & Procedure --}}
        <div class="space-y-2 text-xs text-gray-700">
            <section class="space-y-1">
                <span class="font-semibold text-gray-700">Diagnosa:</span>
                <p class="ml-2 break-words whitespace-normal">
                    {{ !empty($datadaftar_json['diagnosis']) && is_array($datadaftar_json['diagnosis'])
                        ? implode('# ', array_column($datadaftar_json['diagnosis'], 'icdX'))
                        : '-' }}
                </p>
                <p class="ml-2 text-gray-700 break-words whitespace-normal">
                    {{ $datadaftar_json['diagnosisFreeText'] ?? '-' }}
                </p>
            </section>

            <section class="space-y-1">
                <span class="font-semibold text-gray-700">Procedure:</span>
                <p class="ml-2 break-words whitespace-normal">
                    {{ !empty($datadaftar_json['procedure']) && is_array($datadaftar_json['procedure'])
                        ? implode('# ', array_column($datadaftar_json['procedure'], 'procedureId'))
                        : '-' }}
                </p>
                <p class="ml-2 text-gray-700 break-words whitespace-normal">
                    {{ $datadaftar_json['procedureFreeText'] ?? '-' }}
                </p>
            </section>
        </div>

        {{-- Tarif RS Table --}}
        @php
            $tarif_rs = [
                'Dokter' => $myQData->jasa_dokter ?? 0,
                'Medis' => $myQData->jasa_medis ?? 0,
                'Konsul' => $myQData->konsultasi ?? 0,
                'Visit' => $myQData->visit ?? 0,
                'Operasi' => $myQData->operasi ?? 0,
                'Adm Umur' => $myQData->admin_age ?? 0,
                'Adm Sts' => $myQData->admin_status ?? 0,
                'Obat' => ($myQData->obat_pinjam ?? 0) - ($myQData->return_obat ?? 0) + ($myQData->bon_resep ?? 0),
                'Rad' => $myQData->radiologi ?? 0,
                'Lab' => $myQData->laboratorium ?? 0,
                'Kamar' => $myQData->total_room_price ?? 0,
                'Rawat' => $myQData->total_perawatan_price ?? 0,
                'Umum' => $myQData->total_common_service ?? 0,
                'Lain' => $myQData->lain_lain ?? 0,
                'TrfRJ' => $myQData->rawat_jalan ?? 0,
            ];

            $total_all = array_sum($tarif_rs);
        @endphp

        <div class="relative overflow-x-auto">
            <table class="min-w-full text-xs text-left border border-gray-200 rounded-lg table-auto">
                <thead class="text-gray-700 bg-gray-50">
                    <tr>
                        <th class="px-2 py-1 font-semibold">Jenis Layanan</th>
                        <th class="px-2 py-1 font-semibold text-right">Tarif (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($tarif_rs as $layanan => $nominal)
                        @if ((float) $nominal > 0)
                            <tr>
                                <td class="px-2 py-1 text-gray-700">{{ $layanan }}</td>
                                <td class="px-2 py-1 text-right text-gray-700">
                                    {{ number_format($nominal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    <tr class="font-semibold text-gray-700 bg-gray-100">
                        <td class="px-2 py-1">Total</td>
                        <td class="px-2 py-1 text-right">
                            {{ number_format($total_all, 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>


    {{-- RIGHT: Detail Klaim INA-CBG --}}
    @if (
        !empty($datadaftar_json['inacbg']['set_claim_data_done']) &&
            is_array($datadaftar_json['inacbg']['set_claim_data_done']))
        @php
            $data = $datadaftar_json['inacbg']['set_claim_data_done'];
        @endphp
        <div class="p-2 space-y-2 overflow-x-auto bg-white rounded-lg shadow">
            {{-- Section title --}}
            <h5 class="text-sm font-semibold text-gray-700">Detail Klaim INA-CBG</h5>

            {{-- Diagnosa & Procedure --}}
            <div class="space-y-2 text-xs text-gray-700">
                <div>
                    <span class="font-semibold text-gray-700">Diagnosa:</span>
                    <p class="ml-2 break-words whitespace-normal">{{ $data['diagnosa'] ?? '-' }}</p>
                    @if (!empty($data['diagnosisFreeText']))
                        <p class="ml-4 text-gray-600">{{ $data['diagnosisFreeText'] }}</p>
                    @endif
                </div>

                <div>
                    <span class="font-semibold text-gray-700">Procedure:</span>
                    <p class="ml-2 break-words whitespace-normal">{{ $data['procedure'] ?? '-' }}</p>
                    @if (!empty($data['procedureFreeText']))
                        <p class="ml-4 text-gray-600">{{ $data['procedureFreeText'] }}</p>
                    @endif
                </div>

                <div>
                    <span class="font-semibold text-gray-700">Description:</span>
                    <p class="ml-2 break-words whitespace-normal">
                        {{ $data['grouper']['response']['cbg']['description'] ?? '-' }}</p>
                </div>
                <div>
                    <span class="font-semibold text-gray-700">Base Tarif:</span>
                    <p class="ml-2 text-gray-700">Rp
                        {{ number_format($data['grouper']['response']['cbg']['base_tariff'] ?? 0, 0, ',', '.') }}
                    </p>
                </div>
            </div>

            {{-- Detail DRG --}}
            @if (!empty($data['grouper']['response']['response_inagrouper']))
                <div class="text-xs text-gray-700">
                    <span class="font-semibold text-gray-700">Detail DRG:</span>
                    <ul class="ml-4 space-y-1 list-disc list-inside">
                        @foreach ([
        'mdc_number' => 'MDC Number',
        'mdc_description' => 'MDC Description',
        'drg_code' => 'DRG Code',
        'drg_description' => 'DRG Description',
    ] as $key => $label)
                            <li>
                                <span class="font-medium">{{ $label }}:</span>
                                <span
                                    class="text-gray-700">{{ $data['grouper']['response']['response_inagrouper'][$key] ?? '-' }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Tarif CBG-RS --}}
            @if (!empty($data['tarif_rs']))
                @php($total_all_cb = array_sum($data['tarif_rs']))
                <div class="relative overflow-x-auto">
                    <table class="min-w-full text-xs text-left border border-gray-200 rounded-lg table-auto">
                        <thead class="text-gray-700 bg-gray-50">
                            <tr>
                                <th class="px-2 py-1 font-semibold">Jenis Layanan</th>
                                <th class="px-2 py-1 font-semibold text-right">Tarif (Rp)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($data['tarif_rs'] as $layanan => $nominal)
                                @if ((float) $nominal > 0)
                                    <tr>
                                        <td class="px-2 py-1 text-gray-700">
                                            {{ ucwords(str_replace('_', ' ', $layanan)) }}</td>
                                        <td class="px-2 py-1 text-right text-gray-700">
                                            {{ number_format($nominal, 0, ',', '.') }}</td>
                                    </tr>
                                @endif
                            @endforeach
                            <tr class="font-semibold text-gray-700 bg-gray-100">
                                <td class="px-2 py-1">Total</td>
                                <td class="px-2 py-1 text-right">{{ number_format($total_all_cb, 0, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endif

            {{-- Grouper CBG --}}
            <div class="space-y-2 text-xs text-gray-700">
                <span class="font-semibold text-gray-700">Grouper CBG</span>
                <div class="space-y-1">
                    <div>
                        <span class="font-medium">CBG Code:</span>
                        <p class="ml-2 break-words whitespace-normal">
                            {{ $data['grouper']['response']['cbg']['code'] ?? '-' }}</p>
                    </div>

                    <div>
                        <span class="font-medium">Kelas:</span>
                        <p class="ml-2 text-gray-700">{{ $data['grouper']['response']['kelas'] ?? '-' }}</p>
                    </div>
                </div>
            </div>

            {{-- Status Pengiriman --}}
            <div class="space-y-2 text-xs text-gray-700">
                <div>
                    <span class="font-semibold text-gray-700">Status Kemenkes:</span>
                    <p class="ml-2">{{ ucfirst($data['kemenkes_dc_status_cd'] ?? '-') }}
                        @if (!empty($data['kemenkes_dc_sent_dttm']) && $data['kemenkes_dc_sent_dttm'] !== '-')
                            ({{ $data['kemenkes_dc_sent_dttm'] }})
                        @endif
                    </p>
                </div>
                <div>
                    <span class="font-semibold text-gray-700">Status BPJS:</span>
                    <p class="ml-2">{{ ucfirst($data['klaim_status_cd'] ?? '-') }}
                        @if (!empty($data['bpjs_dc_sent_dttm']) && $data['bpjs_dc_sent_dttm'] !== '-')
                            ({{ $data['bpjs_dc_sent_dttm'] }})
                        @endif
                        @if (!empty($data['bpjs_klaim_status_nm']) && $data['bpjs_klaim_status_nm'] !== '-')
                            — {{ $data['bpjs_klaim_status_nm'] }}
                        @endif
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>
