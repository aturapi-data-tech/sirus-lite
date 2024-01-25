<div>

    @php
        $disabledProperty = true;
        $disabledPropertyRjStatus = false;
    @endphp

    <div class="w-full mb-1 ">

        <div class="grid grid-cols-1">

            <div id="TransaksiRawatJalan" class="px-4">
                <x-input-label for="" :value="__($myTitle)" :required="__(false)" class="pt-2 sm:text-xl" />



                <!-- Table -->
                <div class="flex flex-col my-2">
                    <div class="overflow-x-auto rounded-lg">
                        <div class="inline-block min-w-full align-middle">
                            <div class="overflow-hidden shadow sm:rounded-lg">
                                <table class="w-full text-sm text-left text-gray-500 table-auto dark:text-gray-400">
                                    <thead
                                        class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                                        <tr>
                                            <th scope="col" class="w-1/3 px-4 py-3 overflow-auto">

                                                <x-sort-link :active=false wire:click.prevent="" role="button"
                                                    href="#">
                                                    resumemedis
                                                </x-sort-link>
                                            </th>

                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800">

                                        @foreach ($myQueryData as $myQData)
                                            <tr class="border-b group dark:border-gray-700">



                                                @php
                                                    $datadaftar_json = json_decode($myQData->datadaftar_json, true);
                                                @endphp

                                                <td
                                                    class="px-4 py-3 text-gray-900 group-hover:bg-gray-100 whitespace-nowrap">
                                                    <div class="">
                                                        <div class="font-semibold text-primary">
                                                            {{ $myQData->layanan_status === 'RI'
                                                                ? 'Rawat Inap'
                                                                : ($myQData->layanan_status === 'UGD'
                                                                    ? 'UGD'
                                                                    : ($myQData->layanan_status === 'RJ'
                                                                        ? 'Rawat Jalan'
                                                                        : '-')) }}
                                                        </div>
                                                        <div class="font-semibold text-gray-900">
                                                            {{ $myQData->txn_date . ' / (' . $myQData->reg_no . ')' }}
                                                        </div>
                                                        <div class="font-normal text-gray-900">
                                                            {{ $myQData->poli }}
                                                        </div>
                                                    </div>
                                                    <br>
                                                    <div class="ml-8">
                                                        {{-- <span class="font-semibold">TTV:</span>
                                                        <br>
                                                        TD :
                                                        {{ isset($datadaftar_json['pemeriksaan']['tandaVital']['sistolik'])
                                                            ? $datadaftar_json['pemeriksaan']['tandaVital']['sistolik']
                                                            : '' }}
                                                        /
                                                        {{ isset($datadaftar_json['pemeriksaan']['tandaVital']['distolik'])
                                                            ? $datadaftar_json['pemeriksaan']['tandaVital']['distolik']
                                                            : '' }}
                                                        (mmHg)
                                                        <br>
                                                        SPO2 :
                                                        {{ isset($datadaftar_json['pemeriksaan']['tandaVital']['spo2'])
                                                            ? $datadaftar_json['pemeriksaan']['tandaVital']['spo2']
                                                            : '' }}
                                                        (%)
                                                        <br>
                                                        GDA :
                                                        {{ isset($datadaftar_json['pemeriksaan']['tandaVital']['gda'])
                                                            ? $datadaftar_json['pemeriksaan']['tandaVital']['gda']
                                                            : '' }}
                                                        (mg/dL)
                                                        <br> --}}
                                                        <br>
                                                        <span class="font-semibold"> Diagnosis :</span>
                                                        <br>
                                                        @isset($datadaftar_json['diagnosis'])
                                                            @foreach ($datadaftar_json['diagnosis'] as $diagnosis)
                                                                {!! nl2br(e($diagnosis['diagId'] . ' ' . $diagnosis['diagDesc'])) . '</br>' !!}
                                                            @endforeach
                                                        @endisset
                                                        <span class="font-semibold"> Terapi :</span>
                                                        <br>
                                                        {!! nl2br(
                                                            e(
                                                                isset($datadaftar_json['perencanaan']['terapi']['terapi'])
                                                                    ? $datadaftar_json['perencanaan']['terapi']['terapi']
                                                                    : '',
                                                            ),
                                                        ) !!}
                                                    </div>
                                                </td>




                                            </tr>
                                        @endforeach


                                    </tbody>
                                </table>
                                {{-- no data found start --}}
                                @if ($myQueryData->count() == 0)
                                    <div class="w-full p-4 text-sm text-center text-gray-900 dark:text-gray-400">
                                        {{ 'Data Layanan Tidak ditemukan' }}
                                    </div>
                                @endif
                                {{-- no data found end --}}

                            </div>

                            {{ $myQueryData->links('vendor.livewire.simple-tailwind') }}

                        </div>
                    </div>


                </div>
            </div>



        </div>
    </div>
</div>
