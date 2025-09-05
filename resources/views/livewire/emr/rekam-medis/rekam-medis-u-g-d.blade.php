<div>

    @php
        $disabledProperty = true;
        $disabledPropertyRjStatus = false;
    @endphp

    <div class="w-full mb-1 ">

        <div class="grid grid-cols-1">

            <div id="TransaksiRawatJalan" class="px-4">
                <x-input-label for="" :value="__('Rekam Medis Pasien')" :required="__(false)" class="pt-2 sm:text-xl" />



                <!-- Table -->
                <div class="flex flex-col my-2">
                    <div class="overflow-x-auto rounded-lg">
                        <div class="inline-block min-w-full align-middle">
                            <div class="overflow-hidden shadow sm:rounded-lg">
                                <table class="w-full text-sm text-left text-gray-500 table-auto dark:text-gray-400">
                                    <thead
                                        class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                                        <tr>
                                            <th class="px-2 py-2">Layanan</th>
                                            <th class="px-2 py-2">Anamnesa & Pemeriksaan Fisik</th>
                                            <th class="px-2 py-2">TTV & Kegawatan</th>
                                            <th class="px-2 py-2">Diagnosis & Terapi</th>
                                            <th class="w-8 px-2 py-2 text-center">Copy Rekam Medis</th>
                                        </tr>
                                    </thead>

                                    <tbody class="bg-white dark:bg-gray-800">
                                        @foreach ($myQueryData as $myQData)
                                            @php
                                                // decode json secara aman
                                                $datadaftar_json = null;
                                                if (
                                                    is_string($myQData->datadaftar_json) &&
                                                    strlen($myQData->datadaftar_json) > 0
                                                ) {
                                                    $datadaftar_json = json_decode($myQData->datadaftar_json, true);
                                                    if (json_last_error() !== JSON_ERROR_NONE) {
                                                        // jika error decode, fallback ke array kosong supaya view tidak crash
                                                        $datadaftar_json = [];
                                                    }
                                                } else {
                                                    $datadaftar_json = is_array($myQData->datadaftar_json)
                                                        ? $myQData->datadaftar_json
                                                        : [];
                                                }

                                                // mapping fields (disesuaikan dengan contoh JSON)
                                                $terapi = data_get($datadaftar_json, 'perencanaan.terapi.terapi', '');

                                                $diagnosisList = data_get($datadaftar_json, 'diagnosis', []);

                                                $ttv = data_get($datadaftar_json, 'pemeriksaan.tandaVital', []);

                                                // Tingkat kegawatan pada contoh: anamnesa.pengkajianPerawatan.tingkatKegawatan
                                                $kegawatan =
                                                    data_get(
                                                        $datadaftar_json,
                                                        'anamnesa.pengkajianPerawatan.tingkatKegawatan',
                                                    ) ??
                                                    (data_get($datadaftar_json, 'pengkajianPerawat.tingkatKegawatan') ?? // jaga2 variasi
                                                        '-');

                                                // Anamnesa: keluhanUtama + riwayat sekarang
                                                $keluhan = data_get(
                                                    $datadaftar_json,
                                                    'anamnesa.keluhanUtama.keluhanUtama',
                                                    '',
                                                );
                                                $riwayat = data_get(
                                                    $datadaftar_json,
                                                    'anamnesa.riwayatPenyakitSekarangUmum.riwayatPenyakitSekarangUmum',
                                                    '',
                                                );

                                                $anamnesaFull = trim(
                                                    ($keluhan ?: '') . ($riwayat ? "\n\n" . $riwayat : ''),
                                                );
                                                $anamnesaFull = $anamnesaFull ?: '-';

                                                // Pemeriksaan fisik sesuai contoh ada di pemeriksaan.fisik
                                                $fisik =
                                                    data_get($datadaftar_json, 'pemeriksaan.fisik') ??
                                                    (data_get($datadaftar_json, 'pemeriksaan_fisik') ?? '-');

                                                // short versions
                                                $limit = 160;
                                                $anamnesaShort =
                                                    strlen($anamnesaFull) > $limit
                                                        ? substr($anamnesaFull, 0, $limit) . '...'
                                                        : $anamnesaFull;
                                                $fisikShort =
                                                    strlen($fisik) > $limit
                                                        ? substr($fisik, 0, $limit) . '...'
                                                        : $fisik;

                                                // badge class
                                                $k_upper = strtoupper((string) $kegawatan);
                                                $badgeClass = match (true) {
                                                    str_contains($k_upper, 'P1') ||
                                                        str_contains($k_upper, '1') ||
                                                        str_contains($k_upper, 'MERAH')
                                                        => 'bg-red-100 text-red-700 px-2 py-0.5 rounded-full',
                                                    str_contains($k_upper, 'P2') ||
                                                        str_contains($k_upper, '2') ||
                                                        str_contains($k_upper, 'KUNING')
                                                        => 'bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full',
                                                    str_contains($k_upper, 'P3') ||
                                                        str_contains($k_upper, '3') ||
                                                        str_contains($k_upper, 'HIJAU')
                                                        => 'bg-green-100 text-green-700 px-2 py-0.5 rounded-full',
                                                    default => 'bg-gray-100 text-gray-700 px-2 py-0.5 rounded-full',
                                                };
                                            @endphp

                                            <tr class="border-b group dark:border-gray-700" x-data="{ openDetails: false }">
                                                {{-- Layanan --}}
                                                <td class="w-1/5 px-2 py-2 align-top group-hover:bg-gray-100">
                                                    <div class="overflow-auto w-52 ">
                                                        <div class="font-semibold text-primary">
                                                            {{ $myQData->layanan_status === 'RI' ? 'Rawat Inap' : ($myQData->layanan_status === 'UGD' ? 'UGD' : ($myQData->layanan_status === 'RJ' ? 'Rawat Jalan' : '-')) }}
                                                        </div>
                                                        <div class="font-semibold text-gray-900">
                                                            {{ $myQData->txn_date . ' / (' . $myQData->reg_no . ')' }}
                                                        </div>
                                                        <div class="font-normal text-gray-900">
                                                            {{ $myQData->poli }}
                                                        </div>
                                                    </div>
                                                </td>

                                                {{-- Anamnesa & Pemeriksaan Fisik (gabung, toggle satu) --}}
                                                <td class="max-w-md px-2 py-2 align-top group-hover:bg-gray-100">
                                                    <div class="text-sm text-gray-800">
                                                        <div x-show="!openDetails">
                                                            <div class="font-medium">Anamnesa</div>
                                                            <div class="mb-2 text-xs">{!! nl2br(e($anamnesaShort)) !!}</div>

                                                            <div class="font-medium">Pemeriksaan Fisik</div>
                                                            <div class="text-xs">{!! nl2br(e($fisikShort)) !!}</div>
                                                        </div>

                                                        <div x-show="openDetails">
                                                            <div class="font-medium">Anamnesa</div>
                                                            <div class="mb-2 text-xs whitespace-pre-line">
                                                                {!! nl2br(e($anamnesaFull)) !!}</div>

                                                            <div class="font-medium">Pemeriksaan Fisik</div>
                                                            <div class="text-xs whitespace-pre-line">
                                                                {!! nl2br(e($fisik)) !!}</div>
                                                        </div>

                                                        @if ($anamnesaShort !== $anamnesaFull || $fisikShort !== $fisik)
                                                            <button type="button"
                                                                class="mt-2 text-xs text-blue-600 underline"
                                                                @click="openDetails = !openDetails"
                                                                x-text="openDetails ? 'Sembunyikan' : 'Lihat selengkapnya'"></button>
                                                        @endif
                                                    </div>
                                                </td>


                                                {{-- TTV & Kegawatan (gabung) --}}
                                                <td class="px-2 py-2 text-gray-900 align-top group-hover:bg-gray-100">
                                                    <div class="text-xs">
                                                        <div class="mt-1">
                                                            <span class="{{ $badgeClass }}">{{ $kegawatan }}</span>
                                                        </div>

                                                        <div class="mb-1">
                                                            <strong>TD</strong> : {{ data_get($ttv, 'sistolik', '') }} /
                                                            {{ data_get($ttv, 'distolik', '') }} mmHg<br>
                                                            <strong>Nadi</strong> :
                                                            {{ data_get($ttv, 'frekuensiNadi', '') }} &nbsp;
                                                            <strong>RR</strong> :
                                                            {{ data_get($ttv, 'frekuensiNafas', '') }}<br>
                                                            <strong>SPO2</strong> : {{ data_get($ttv, 'spo2', '') }}%
                                                            &nbsp; <strong>Suhu</strong> :
                                                            {{ data_get($ttv, 'suhu', '') }}°C<br>
                                                            <strong>GDA</strong> : {{ data_get($ttv, 'gda', '') }}
                                                            mg/dL &nbsp; <strong>GCS</strong> :
                                                            {{ data_get($ttv, 'gcs', '') }}
                                                        </div>


                                                    </div>
                                                </td>


                                                {{-- Diagnosis & Terapi (gabung) --}}
                                                <td
                                                    class="max-w-xs px-2 py-2 text-gray-900 align-top group-hover:bg-gray-100">
                                                    <div class="text-sm">
                                                        {{-- Diagnosis --}}
                                                        <div class="mb-1 font-medium">
                                                            @if (!empty($diagnosisList) && is_array($diagnosisList))
                                                                @foreach ($diagnosisList as $d)
                                                                    <div class="text-xs whitespace-normal">
                                                                        {{ ($d['diagId'] ?? '-') . ' - ' . ($d['diagDesc'] ?? '-') }}
                                                                    </div>
                                                                @endforeach
                                                            @else
                                                                <div class="text-xs text-gray-600">-</div>
                                                            @endif
                                                        </div>

                                                        <hr class="my-1 border-t border-gray-200">

                                                        {{-- Terapi --}}
                                                        <div class="text-xs whitespace-pre-line">
                                                            {!! nl2br(e($terapi ?: '-')) !!}
                                                        </div>
                                                    </div>
                                                </td>




                                                {{-- Rekam Medis (aksi) --}}
                                                <td
                                                    class="w-1/5 px-2 py-2 text-gray-900 align-top group-hover:bg-gray-100">
                                                    <div class="grid grid-cols-1 gap-2">
                                                        {{-- Admin: bisa lihat kedua tombol --}}
                                                        @role('Admin')
                                                            <div>
                                                                <x-yellow-button
                                                                    wire:click.prevent="copyAssessmentDokterLayananUGD('{{ $myQData->layanan_status }}', {{ $myQData->datadaftar_json }})"
                                                                    type="button" wire:loading.remove>
                                                                    Copy Assessment (Dokter)
                                                                </x-yellow-button>
                                                                <div wire:loading
                                                                    wire:target="copyAssessmentDokterLayananUGD">
                                                                    <x-loading />
                                                                </div>
                                                            </div>

                                                            <div>
                                                                <x-yellow-button
                                                                    wire:click.prevent="copyAssessmentPerawatLayananUGD('{{ $myQData->layanan_status }}', {{ $myQData->datadaftar_json }})"
                                                                    type="button" wire:loading.remove>
                                                                    Copy Assessment (Perawat)
                                                                </x-yellow-button>
                                                                <div wire:loading
                                                                    wire:target="copyAssessmentPerawatLayananUGD">
                                                                    <x-loading />
                                                                </div>
                                                            </div>
                                                        @endrole

                                                        {{-- Dokter hanya tombol Dokter --}}
                                                        @role('Dokter')
                                                            <div>
                                                                <x-yellow-button
                                                                    wire:click.prevent="copyAssessmentDokterLayananUGD('{{ $myQData->layanan_status }}', {{ $myQData->datadaftar_json }})"
                                                                    type="button" wire:loading.remove>
                                                                    Copy Assessment (Dokter)
                                                                </x-yellow-button>
                                                                <div wire:loading
                                                                    wire:target="copyAssessmentDokterLayananUGD">
                                                                    <x-loading />
                                                                </div>
                                                            </div>
                                                        @endrole

                                                        {{-- Perawat hanya tombol Perawat --}}
                                                        @role('Perawat')
                                                            <div>
                                                                <x-yellow-button
                                                                    wire:click.prevent="copyAssessmentPerawatLayananUGD('{{ $myQData->layanan_status }}', {{ $myQData->datadaftar_json }})"
                                                                    type="button" wire:loading.remove>
                                                                    Copy Assessment (Perawat)
                                                                </x-yellow-button>
                                                                <div wire:loading
                                                                    wire:target="copyAssessmentPerawatLayananUGD">
                                                                    <x-loading />
                                                                </div>
                                                            </div>
                                                        @endrole
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                @if ($myQueryData->count() == 0)
                                    <div class="w-full p-4 text-sm text-center text-gray-900 dark:text-gray-400">
                                        {{ 'Data Layanan Tidak ditemukan' }}
                                    </div>
                                @endif

                            </div>

                            {{ $myQueryData->links() }}

                        </div>
                    </div>

                </div>
            </div>



        </div>
    </div>
</div>
