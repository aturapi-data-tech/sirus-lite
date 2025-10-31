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
                                <div class="overflow-x-auto">
                                    <table
                                        class="w-full text-sm text-left text-gray-700 border border-gray-200 rounded-md table-auto dark:text-gray-300 dark:border-gray-600">
                                        <thead
                                            class="text-xs font-semibold text-gray-600 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-300">
                                            <tr>
                                                <th class="w-1/4 px-3 py-2">Layanan</th>
                                                <th class="w-1/6 px-3 py-2">TTV</th>
                                                <th class="w-1/3 px-3 py-2">Diagnosis & Terapi</th>
                                                <th class="w-1/6 px-3 py-2 text-center">Resume Medis</th>
                                            </tr>
                                        </thead>

                                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach ($myQueryData as $myQData)
                                                @php
                                                    $datadaftar_json = json_decode($myQData->datadaftar_json, true);

                                                    $isRJ = $myQData->layanan_status === 'RJ';
                                                    $isUGD = $myQData->layanan_status === 'UGD';
                                                    $isRI = $myQData->layanan_status === 'RI';

                                                    $resumeLabel = $isRJ
                                                        ? 'Resume Medis Rawat Jalan'
                                                        : ($isUGD
                                                            ? 'Resume Medis UGD'
                                                            : ($isRI
                                                                ? 'Ringkasan Pasien Pulang Inap'
                                                                : 'Resume Medis'));

                                                    $vital = $datadaftar_json['pemeriksaan']['tandaVital'] ?? [];
                                                    $sistolik = $vital['sistolik'] ?? '-';
                                                    $distolik = $vital['distolik'] ?? '-';
                                                    $spo2 = $vital['spo2'] ?? '-';
                                                    $gda = $vital['gda'] ?? '-';
                                                @endphp

                                                <tr
                                                    class="align-top transition hover:bg-gray-50 dark:hover:bg-gray-800">
                                                    {{-- Layanan --}}
                                                    <td class="px-3 py-3 align-top">
                                                        <div class="space-y-0.5">
                                                            <div class="text-lg font-semibold text-primary">
                                                                {{ $isRI ? 'Rawat Inap' : ($isUGD ? 'UGD' : ($isRJ ? 'Rawat Jalan' : '-')) }}
                                                            </div>
                                                            <div class="font-medium text-gray-800 dark:text-gray-100">
                                                                {{ $myQData->txn_date }} / ({{ $myQData->reg_no }})
                                                            </div>
                                                            <div class="text-gray-600 dark:text-gray-400">
                                                                {{ $myQData->poli ?? '-' }}
                                                            </div>
                                                        </div>
                                                    </td>

                                                    {{-- TTV --}}
                                                    <td class="px-3 py-3 text-gray-800 align-top dark:text-gray-200">
                                                        <div class="space-y-0.5">
                                                            <div><span class="font-semibold">TD:</span>
                                                                {{ $sistolik }}/{{ $distolik }} mmHg</div>
                                                            <div><span class="font-semibold">SPO₂:</span>
                                                                {{ $spo2 }}%</div>
                                                            <div><span class="font-semibold">GDA:</span>
                                                                {{ $gda }} mg/dL</div>
                                                        </div>
                                                    </td>

                                                    {{-- Diagnosis & Terapi (gabung) --}}
                                                    <td class="px-3 py-3 text-gray-800 align-top dark:text-gray-200">
                                                        {{-- Diagnosis --}}
                                                        <div>
                                                            <span class="font-semibold">Diagnosis:</span>
                                                            <div class="mt-1 space-y-0.5">
                                                                @if (!empty($datadaftar_json['diagnosis']))
                                                                    @foreach ($datadaftar_json['diagnosis'] as $diagnosis)
                                                                        <div>{{ $diagnosis['diagId'] ?? '' }} -
                                                                            {{ $diagnosis['diagDesc'] ?? '' }}</div>
                                                                    @endforeach
                                                                @else
                                                                    <div class="text-gray-400">-</div>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        @if ($isRI)
                                                            {{-- RI: ambil dari pengkajianDokter.rencana (terapi & terapiPulang) --}}
                                                            <div class="pl-6 mt-2 text-sm">
                                                                <div class="font-medium text-gray-800">Terapi Rawat Inap
                                                                </div>
                                                                <div class="mt-1 ml-1 text-gray-700">
                                                                    {!! nl2br(e((string) data_get($datadaftar_json, 'pengkajianDokter.rencana.terapi', '-'))) !!}
                                                                </div>

                                                                <div class="mt-3">
                                                                    <div class="font-medium text-gray-800">Terapi Pulang
                                                                    </div>
                                                                    <div class="mt-1 ml-1 text-gray-700">
                                                                        {!! nl2br(e((string) data_get($datadaftar_json, 'pengkajianDokter.rencana.terapiPulang', '-'))) !!}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @else
                                                            {{-- RJ / UGD --}}
                                                            <div class="pl-6 mt-2 text-sm">
                                                                <div class="font-semibold text-gray-800">Terapi:</div>
                                                                <div class="mt-1 ml-1 text-gray-700">
                                                                    {!! nl2br(e($datadaftar_json['perencanaan']['terapi']['terapi'] ?? '-')) !!}
                                                                </div>

                                                                <div class="mt-3 text-gray-600 dark:text-gray-400">
                                                                    <span
                                                                        class="font-semibold text-gray-700 dark:text-gray-300">Tindak
                                                                        Lanjut:</span>
                                                                    <div class="mt-1 ml-1">
                                                                        {{ $datadaftar_json['perencanaan']['tindakLanjut']['tindakLanjut'] ?? '-' }}
                                                                        /
                                                                        {{ $datadaftar_json['perencanaan']['tindakLanjut']['keteranganTindakLanjut'] ?? '-' }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </td>

                                                    {{-- Rekam Medis --}}
                                                    <td class="px-3 py-3 text-center align-top">
                                                        <div class="grid grid-cols-1">
                                                            <x-light-button
                                                                wire:click.prevent="openModalLayanan('{{ $myQData->txn_no }}', '{{ $myQData->layanan_status }}', {{ $myQData->datadaftar_json }})"
                                                                type="button" wire:loading.attr="disabled"
                                                                wire:target="openModalLayanan('{{ $myQData->txn_no }}', '{{ $myQData->layanan_status }}', {{ $myQData->datadaftar_json }})">

                                                                <span wire:loading.remove
                                                                    wire:target="openModalLayanan('{{ $myQData->txn_no }}', '{{ $myQData->layanan_status }}', {{ $myQData->datadaftar_json }})">
                                                                    {{ $resumeLabel }}
                                                                </span>
                                                                <span wire:loading
                                                                    wire:target="openModalLayanan('{{ $myQData->txn_no }}', '{{ $myQData->layanan_status }}', {{ $myQData->datadaftar_json }})">
                                                                    <x-loading />
                                                                </span>
                                                            </x-light-button>
                                                        </div>

                                                        {{-- CETAK (sesuai layanan) --}}
                                                        @if ($myQData->layanan_status === 'RJ')
                                                            <div class="grid grid-cols-2 gap-2">
                                                                <x-green-button
                                                                    wire:click.prevent="cetakRekamMedisRJ('{{ $myQData->txn_no }}', 'RJ')"
                                                                    wire:loading.attr="disabled"
                                                                    wire:target="cetakRekamMedisRJ({{ $myQData->txn_no }}, 'RJ')">
                                                                    <span wire:loading.remove
                                                                        wire:target="cetakRekamMedisRJ({{ $myQData->txn_no }}, 'RJ')">Cetak
                                                                        RJ</span>
                                                                    <span wire:loading
                                                                        wire:target="cetakRekamMedisRJ({{ $myQData->txn_no }}, 'RJ')"><x-loading /></span>
                                                                </x-green-button>

                                                                <x-green-button
                                                                    wire:click.prevent="cetakRekamMedisRJ('{{ $myQData->txn_no }}', 'RJ1')"
                                                                    wire:loading.attr="disabled"
                                                                    wire:target="cetakRekamMedisRJ({{ $myQData->txn_no }}, 'RJ1')">
                                                                    <span wire:loading.remove
                                                                        wire:target="cetakRekamMedisRJ({{ $myQData->txn_no }}, 'RJ1')">Cetak
                                                                        RJ v1</span>
                                                                    <span wire:loading
                                                                        wire:target="cetakRekamMedisRJ({{ $myQData->txn_no }}, 'RJ1')"><x-loading /></span>
                                                                </x-green-button>
                                                                @if (data_get($myQData->datadaftar_json, 'poliId') == '12')
                                                                    <x-green-button
                                                                        wire:click.prevent="cetakRekamMedisRJ('{{ $myQData->txn_no }}', 'FISIO')"
                                                                        wire:loading.attr="disabled"
                                                                        wire:target="cetakRekamMedisRJ({{ $myQData->txn_no }}, 'FISIO')">
                                                                        <span wire:loading.remove
                                                                            wire:target="cetakRekamMedisRJ({{ $myQData->txn_no }}, 'FISIO')">Fisio</span>
                                                                        <span wire:loading
                                                                            wire:target="cetakRekamMedisRJ({{ $myQData->txn_no }}, 'FISIO')"><x-loading /></span>
                                                                    </x-green-button>
                                                                @endif

                                                                <x-green-button
                                                                    wire:click.prevent="cetakRekamMedisRJ('{{ $myQData->txn_no }}', 'SUKET_ISTIRAHAT')"
                                                                    wire:loading.attr="disabled"
                                                                    wire:target="cetakRekamMedisRJ({{ $myQData->txn_no }}, 'SUKET_ISTIRAHAT')">
                                                                    <span wire:loading.remove
                                                                        wire:target="cetakRekamMedisRJ({{ $myQData->txn_no }}, 'SUKET_ISTIRAHAT')">Suket
                                                                        Istirahat</span>
                                                                    <span wire:loading
                                                                        wire:target="cetakRekamMedisRJ({{ $myQData->txn_no }}, 'SUKET_ISTIRAHAT')"><x-loading /></span>
                                                                </x-green-button>

                                                                <x-green-button
                                                                    wire:click.prevent="cetakRekamMedisRJ('{{ $myQData->txn_no }}', 'SUKET_SEHAT')"
                                                                    wire:loading.attr="disabled"
                                                                    wire:target="cetakRekamMedisRJ({{ $myQData->txn_no }}, 'SUKET_SEHAT')">
                                                                    <span wire:loading.remove
                                                                        wire:target="cetakRekamMedisRJ({{ $myQData->txn_no }}, 'SUKET_SEHAT')">Suket
                                                                        Sehat</span>
                                                                    <span wire:loading
                                                                        wire:target="cetakRekamMedisRJ({{ $myQData->txn_no }}, 'SUKET_SEHAT')"><x-loading /></span>
                                                                </x-green-button>


                                                            </div>
                                                        @elseif ($myQData->layanan_status === 'UGD')
                                                            <div class="grid grid-cols-2 gap-2">
                                                                <x-green-button class="ml-2"
                                                                    wire:click.prevent="cetakRekamMedisUGD('{{ $myQData->txn_no }}', 'UGD')"
                                                                    wire:loading.attr="disabled"
                                                                    wire:target="cetakRekamMedisUGD('{{ $myQData->txn_no }}', 'UGD')">
                                                                    <span wire:loading.remove
                                                                        wire:target="cetakRekamMedisUGD('{{ $myQData->txn_no }}', 'UGD')">
                                                                        Cetak UGD
                                                                    </span>
                                                                    <span wire:loading
                                                                        wire:target="cetakRekamMedisUGD('{{ $myQData->txn_no }}', 'UGD')">
                                                                        <x-loading />
                                                                    </span>
                                                                </x-green-button>
                                                            </div>
                                                        @endif


                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                {{-- no data found start --}}
                                @if ($myQueryData->count() == 0)
                                    <div class="w-full p-4 text-sm text-center text-gray-900 dark:text-gray-400">
                                        {{ 'Data Layanan Tidak ditemukan' }}
                                    </div>
                                @endif
                                {{-- no data found end --}}

                            </div>

                            {{ $myQueryData->links() }}

                        </div>
                    </div>
                    @if ($isOpenRekamMedisUGD)
                        @include('livewire.emr.rekam-medis.create-rekam-medis-u-g-d')
                    @endif
                    @if ($isOpenRekamMedisRJ)
                        @include('livewire.emr.rekam-medis.create-rekam-medis-r-j')
                    @endif
                    @if ($isOpenRekamMedisRI)
                        @include('livewire.emr.rekam-medis.create-rekam-medis-r-i')
                    @endif

                </div>
            </div>



        </div>
    </div>
</div>
