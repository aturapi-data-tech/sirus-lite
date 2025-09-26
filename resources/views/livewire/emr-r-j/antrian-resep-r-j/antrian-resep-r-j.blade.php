<div>

    {{-- Start Coding  --}}

    {{-- Canvas
    Main BgColor /
    Size H/W --}}
    <div class="w-full h-[calc(100vh-68px)] bg-white border border-gray-200 px-4 pt-6">

        {{-- Title  --}}
        <div class="mb-2">
            <h3 class="text-3xl font-bold text-gray-900 ">{{ $myTitle }}</h3>
            <span class="text-base font-normal text-gray-700">{{ $mySnipt }}</span>
        </div>
        {{-- Title --}}

        @if ($myTopBar['autoRefresh'] == 'Ya')
            <div wire:poll.30s="render" class="h-[calc(100vh-250px)] mt-2 overflow-auto">
            @else
                <div class="h-[calc(100vh-250px)] mt-2 overflow-auto">
        @endif

        <p class="text-xs text-gray-700">Data Terakhir: {{ now()->format('d-m-y H:i:s') }}</p>

        <div class="grid grid-cols-2 gap-4">
            <!-- Table -->
            <table class="w-full text-sm text-left text-gray-700 table-auto ">
                <thead class="sticky top-0 text-xs text-gray-900 uppercase bg-gray-100 ">
                    <tr>
                        <th scope="col" class="w-1/4 px-4 py-3 ">
                            Nama Pasien
                        </th>

                        <th scope="col" class="w-1/4 px-4 py-3 ">
                            Dokter Peresep
                        </th>
                        <th scope="col" class="w-1/4 px-2 py-3 ">
                            Antrian
                        </th>

                    </tr>
                </thead>

                <tbody class="bg-white ">

                    @foreach ($myQueryData as $myQData)
                        @php
                            $datadaftar_json = json_decode($myQData->datadaftarpolirj_json, true);

                            $eresep = isset($datadaftar_json['eresep']) ? 1 : 0;
                            $noAntrianFarmasi = isset($datadaftar_json['noAntrianApotek']['noAntrian'])
                                ? $datadaftar_json['noAntrianApotek']['noAntrian']
                                : 0;

                            $eresepRacikan = collect(
                                isset($datadaftar_json['eresepRacikan']) ? $datadaftar_json['eresepRacikan'] : [],
                            )->count();
                            $jenisResep = $eresepRacikan ? 'racikan' : 'non racikan';

                            $prosentaseEMR = ($eresep / 1) * 100;

                            $badgecolorStatus = isset($myQData->rj_status)
                                ? ($myQData->rj_status === 'A'
                                    ? 'red'
                                    : ($myQData->rj_status === 'L'
                                        ? 'green'
                                        : ($myQData->rj_status === 'I'
                                            ? 'green'
                                            : ($myQData->rj_status === 'F'
                                                ? 'yellow'
                                                : 'default'))))
                                : '';

                            $badgecolorEresep = $eresep ? 'green' : 'red';

                            $badgecolorKlaim =
                                $myQData->klaim_id == 'UM'
                                    ? 'green'
                                    : ($myQData->klaim_id == 'JM'
                                        ? 'default'
                                        : ($myQData->klaim_id == 'KR'
                                            ? 'yellow'
                                            : 'red'));

                            $badgecolorAdministrasiRj = isset($datadaftar_json['AdministrasiRj']) ? 'green' : 'red';

                            $taskId5 = $datadaftar_json['taskIdPelayanan']['taskId5'] ?? 'xxxx-xx-xx xx:xx:xx';
                            $taskId6 = $datadaftar_json['taskIdPelayanan']['taskId6'] ?? 'xxxx-xx-xx xx:xx:xx';
                            $taskId7 = $datadaftar_json['taskIdPelayanan']['taskId7'] ?? 'xxxx-xx-xx xx:xx:xx';

                            $telaahResepStatus = isset($datadaftar_json['telaahResep']['penanggungJawab'])
                                ? ($datadaftar_json['telaahResep']['penanggungJawab']
                                    ? true
                                    : false)
                                : false;
                            $telaahObatStatus = isset($datadaftar_json['telaahObat']['penanggungJawab'])
                                ? ($datadaftar_json['telaahObat']['penanggungJawab']
                                    ? true
                                    : false)
                                : false;

                            $statusPRB = $datadaftar_json['statusPRB']['penanggungJawab']['statusPRB'] ?? 0;
                        @endphp


                        <tr class="border-b group ">


                            <td class="px-4 py-3 group-hover:bg-gray-100 whitespace-nowrap ">
                                <div class="">

                                    <div class="font-semibold text-gray-900">
                                        {{ $myQData->reg_name }}
                                    </div>

                                </div>
                            </td>

                            <td class="px-4 py-3 group-hover:bg-gray-100 whitespace-nowrap ">
                                <div class="flex gap-2">

                                    <div class="text-gray-700 ">
                                        {{ $myQData->dr_name }}
                                    </div>
                                    /
                                    <div class="text-gray-700 ">
                                        {{ $myQData->poli_desc }}
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-3 group-hover:bg-gray-100 whitespace-nowrap ">
                                <div class="flex gap-2">
                                    <div>
                                        <span class="text-xl font-semibold text-gray-700">
                                            {{ $noAntrianFarmasi }}
                                        </span>
                                    </div>

                                    <div>
                                        @if ($jenisResep == 'racikan' && $eresep > 0)
                                            <x-badge :badgecolor="__('default')"> {{ $jenisResep }}</x-badge>
                                        @elseif($jenisResep == 'non racikan' && $eresep > 0)
                                            <x-badge :badgecolor="__('green')"> {{ $jenisResep }}</x-badge>
                                        @else
                                            <x-badge :badgecolor="__('red')"> {{ '---' }}</x-badge>
                                        @endif
                                    </div>
                                </div>

                            </td>


                        </tr>
                    @endforeach

                </tbody>
            </table>

            <div class="w-full text-sm text-left text-gray-700">
                <style>
                    @keyframes flash-pulse {

                        0%,
                        96%,
                        100% {
                            opacity: 1;
                        }

                        /* stabil */
                        97%,
                        99% {
                            opacity: 0.3;
                        }

                        /* kedip cepat (±0.1s dari 5s) */
                    }

                    .blink-soft {
                        animation: flash-pulse 5s linear infinite;
                    }

                    @media (prefers-reduced-motion: reduce) {
                        .blink-soft {
                            animation: none !important;
                        }
                    }
                </style>

                <div class="p-2 mt-2 text-xs border rounded sm:text-sm bg-amber-50 border-amber-200 text-amber-900 sm:p-3 blink-soft"
                    style="animation: flash-soft 10.2s linear infinite;">
                    Kepada pasien yang memiliki resep mengandung <strong>obat racikan</strong>, proses peracikan
                    memerlukan
                    waktu tambahan sekitar
                    <strong>±15–30 menit</strong> demi ketepatan dosis dan keselamatan. Terima kasih atas kesabaran dan
                    pengertiannya.
                    <strong>Kami akan menginformasikan saat obat siap diambil.</strong>
                </div>
            </div>
        </div>
        {{-- no data found start --}}
        @if ($myQueryData->count() == 0)
            <div class="w-full p-4 text-sm text-center text-gray-900 dark:text-gray-400">
                {{ 'Data ' . $myProgram . ' Tidak ditemukan' }}
            </div>
        @endif
        {{-- no data found end --}}

    </div>


    {{ $myQueryData->links() }}








</div>
