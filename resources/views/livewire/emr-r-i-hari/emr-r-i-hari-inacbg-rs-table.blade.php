<div class="grid grid-cols-2 gap-2">
    <div>
        {{-- RS --}}
        <div class="font-normal text-left text-gray-900">
            <span class="font-semibold">
                Diagnosa:
            </span>
            <br>
            {{ !empty($datadaftar_json['diagnosis']) && is_array($datadaftar_json['diagnosis'])
                ? implode('# ', array_column($datadaftar_json['diagnosis'], 'icdX'))
                : '-' }}
            <br>
            <span class="pl-2">
                FreeText: {{ $datadaftar_json['diagnosisFreeText'] ?? '-' }}
            </span>
            <br>
            <span class="font-semibold">
                Procedure:
            </span>
            <br>
            {{ !empty($datadaftar_json['procedure']) && is_array($datadaftar_json['procedure'])
                ? implode('# ', array_column($datadaftar_json['procedure'], 'procedureId'))
                : '-' }}
            <br>
            <span class="pl-2">
                FreeText: {{ $datadaftar_json['procedureFreeText'] ?? '-' }}
            </span>
        </div>
        <table class="table mb-3 text-xs table-sm table-bordered bg-red">
            <thead>
                <tr>
                    <th>Jenis Layanan</th>
                    <th class="text-right">Tarif (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $tarif_rs = [
                        'jasa_dokter' => $myQData->jasa_dokter ?? 0,
                        'jasa_medis' => $myQData->jasa_medis ?? 0,
                        'konsultasi' => $myQData->konsultasi ?? 0,
                        'visit' => $myQData->visit ?? 0,
                        'admin_age' => $myQData->admin_age ?? 0,
                        'admin_status' => $myQData->admin_status ?? 0,
                        'bon_resep' => $myQData->bon_resep ?? 0,
                        'obat_pinjam' => $myQData->obat_pinjam ?? 0,
                        'return_obat' => $myQData->return_obat ?? 0,
                        'radiologi' => $myQData->radiologi ?? 0,
                        'laboratorium' => $myQData->laboratorium ?? 0,
                        'operasi' => $myQData->operasi ?? 0,
                        'lain_lain' => $myQData->lain_lain ?? 0,
                        'rawat_jalan' => $myQData->rawat_jalan ?? 0,
                        'total_room_price' => $myQData->total_room_price ?? 0,
                        'total_perawatan_price' => $myQData->total_perawatan_price ?? 0,
                        'total_common_service' => $myQData->total_common_service ?? 0,
                    ];

                    $total_all = array_sum($tarif_rs);
                @endphp

                @foreach ($tarif_rs as $key => $nominal)
                    @if ((float) $nominal > 0)
                        <tr>
                            <td>{{ ucwords(str_replace('_', ' ', $key)) }}</td>
                            <td class="text-right">
                                {{ number_format($nominal, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endif
                @endforeach
                <tr class="font-semibold">
                    <td>Total</td>
                    <td class="text-right">
                        {{ number_format($total_all, 0, ',', '.') }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- INACBGCek dulu apakah set_claim_data_done ada --}}
    <div>
        @if (isset($datadaftar_json['inacbg']['set_claim_data_done']) &&
                is_array($datadaftar_json['inacbg']['set_claim_data_done']))
            @php
                $data = $datadaftar_json['inacbg']['set_claim_data_done'];
            @endphp

            <div class="mb-4 text-xs card">
                <div class="card-header">
                    <h5>Detail Klaim INA-CBG</h5>
                </div>
                <div class="card-body">
                    {{-- Diagnosa & Procedure --}}
                    <div class="mb-3 row">
                        <div class="col-sm-6">
                            <span class="font-semibold">Diagnosa:</span>
                            <p>{{ $data['diagnosa'] ?? '-' }}</p>
                        </div>
                        <div class="col-sm-6">
                            <span class="font-semibold">Procedure:</span>
                            <p>{{ $data['procedure'] ?? '-' }}</p>
                        </div>
                    </div>
                    {{-- Grouper CBG --}}
                    <div class="mb-3 row">
                        <div class="col-sm-3">
                            <span class="font-semibold">CBG Code:</span>
                            <p>{{ $data['grouper']['response']['cbg']['code'] ?? '-' }}</p>
                        </div>
                        <div class="col-sm-5">
                            <span class="font-semibold">Description:</span>
                            <p>{{ $data['grouper']['response']['cbg']['description'] ?? '-' }}
                            </p>
                        </div>
                        <div class="col-sm-2">
                            <span class="font-semibold">Base Tarif:</span>
                            <p>Rp
                                {{ number_format($data['grouper']['response']['cbg']['base_tariff'] ?? 0, 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="col-sm-2">
                            <span class="font-semibold">Kelas:</span>
                            <p>{{ $data['grouper']['response']['kelas'] ?? '-' }}</p>
                        </div>
                    </div>

                    {{-- Response Inagrouper --}}
                    @if (isset($data['grouper']['response']['response_inagrouper']) &&
                            is_array($data['grouper']['response']['response_inagrouper']))
                        <div class="mb-3">
                            <h6>Detail DRG (Inagrouper)</h6>
                            <ul class="list-unstyled">
                                <li><span class="font-semibold">MDC Number:</span>
                                    {{ $data['grouper']['response']['response_inagrouper']['mdc_number'] }}
                                </li>
                                <li><span class="font-semibold">MDC Description:</span>
                                    {{ $data['grouper']['response']['response_inagrouper']['mdc_description'] }}
                                </li>
                                <li><span class="font-semibold">DRG Code:</span>
                                    {{ $data['grouper']['response']['response_inagrouper']['drg_code'] }}
                                </li>
                                <li><span class="font-semibold">DRG Description:</span>
                                    {{ $data['grouper']['response']['response_inagrouper']['drg_description'] }}
                                </li>
                            </ul>
                        </div>
                    @endif

                    {{-- Tarif RS --}}
                    @if (isset($data['tarif_rs']) && is_array($data['tarif_rs']))
                        @php
                            // Hitung total semua tarif
                            $total_all = array_sum($data['tarif_rs']);
                        @endphp

                        <table class="table mb-3 table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Jenis Layanan</th>
                                    <th class="text-right">Tarif (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data['tarif_rs'] as $layanan => $nominal)
                                    @if ((float) $nominal > 0)
                                        <tr>
                                            <td>{{ ucwords(str_replace('_', ' ', $layanan)) }}
                                            </td>
                                            <td class="text-right">
                                                {{ number_format($nominal, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach

                                {{-- Baris Total --}}
                                <tr class="font-semibold">
                                    <td>Total</td>
                                    <td class="text-right">
                                        {{ number_format($total_all, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    @endif

                    {{-- Status Pengiriman --}}
                    <div class="row">
                        <div class="col-sm-6">
                            <span class="font-semibold">Status Kemenkes:</span>
                            <p>
                                {{ ucfirst($data['kemenkes_dc_status_cd'] ?? '-') }}
                                @if (!empty($data['kemenkes_dc_sent_dttm']) && $data['kemenkes_dc_sent_dttm'] !== '-')
                                    ({{ $data['kemenkes_dc_sent_dttm'] }})
                                @endif
                            </p>
                        </div>
                        <div class="col-sm-6">
                            <span class="font-semibold">Status BPJS:</span>
                            <p>
                                {{ ucfirst($data['klaim_status_cd'] ?? '-') }}
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
            </div>
        @endif
    </div>
</div>
