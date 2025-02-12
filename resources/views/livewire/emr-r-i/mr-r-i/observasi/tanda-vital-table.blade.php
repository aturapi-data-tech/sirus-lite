<div>
    <div class="w-full mb-1">


        <table class="w-full text-sm text-left text-gray-900 table-auto dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="text-center ">
                        Tanggal / Jam
                    </th>
                    <th scope="col" class="text-center ">
                        Cairan
                    </th>
                    <th scope="col" class="text-center ">
                        Tetesan
                    </th>
                    <th scope="col" class="text-center ">
                        TD
                    </th>
                    <th scope="col" class="text-center ">
                        Nadi
                    </th>
                    <th scope="col" class="text-center ">
                        Pernafasan
                    </th>
                    <th scope="col" class="text-center ">
                        Suhu
                    </th>
                    <th scope="col" class="text-center ">
                        SPO2
                    </th>
                    <th scope="col" class="text-center ">
                        GDA
                    </th>
                    <th scope="col" class="text-center ">
                        GCS
                    </th>
                    <th scope="col" class="text-center ">
                        Action
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800">

                @php
                    use Carbon\Carbon;

                    $sortedTandaVital = collect(
                        $dataDaftarRi['observasi']['observasiLanjutan']['tandaVital'] ?? [],
                    )->sortByDesc(function ($item) {
                        return Carbon::createFromFormat('d/m/Y H:i:s', $item['waktuPemeriksaan'], env('APP_TIMEZONE'));
                    });
                @endphp

                @foreach ($sortedTandaVital ?? [] as $tandaVital)
                    <tr class="border-b group dark:border-gray-700 ">

                        <td class="text-center group-hover:bg-gray-100 group-hover:text-primary">
                            {{ $tandaVital['waktuPemeriksaan'] }}
                            <br>
                            {{ $tandaVital['pemeriksa'] }}
                        </td>
                        <td class="text-center group-hover:bg-gray-100 group-hover:text-primary">
                            {{ $tandaVital['cairan'] }}
                        </td>
                        <td class="text-center group-hover:bg-gray-100 group-hover:text-primary">
                            {{ $tandaVital['tetesan'] }}
                        </td>
                        <td class="text-center group-hover:bg-gray-100 group-hover:text-primary">
                            {{ $tandaVital['sistolik'] . '/' . $tandaVital['distolik'] . ' mmhg' }}

                        </td>
                        <td class="text-center group-hover:bg-gray-100 group-hover:text-primary">
                            {{ $tandaVital['frekuensiNadi'] . ' x/mnt' }}
                        </td>
                        <td class="text-center group-hover:bg-gray-100 group-hover:text-primary">
                            {{ $tandaVital['frekuensiNafas'] . ' x/mnt' }}
                        </td>
                        <td class="text-center group-hover:bg-gray-100 group-hover:text-primary">
                            {{ $tandaVital['suhu'] . ' °C' }}
                        </td>
                        <td class="text-center group-hover:bg-gray-100 group-hover:text-primary">
                            {{ $tandaVital['spo2'] . ' %' }}
                        </td>
                        <td class="text-center group-hover:bg-gray-100 group-hover:text-primary">
                            {{ $tandaVital['gda'] . ' mg/dL' }}
                        </td>
                        <td class="text-center group-hover:bg-gray-100 group-hover:text-primary">
                            {{ $tandaVital['gcs'] }}
                        </td>
                        <td class="text-center group-hover:bg-gray-100 group-hover:text-primary">
                            <x-alternative-button class="inline-flex"
                                wire:click.prevent="removeObservasiLanjutan('{{ $tandaVital['waktuPemeriksaan'] }}')">
                                <svg class="w-5 h-5 text-gray-800" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                    fill="currentColor" viewBox="0 0 18 20">
                                    <path
                                        d="M17 4h-4V2a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v2H1a1 1 0 0 0 0 2h1v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6h1a1 1 0 1 0 0-2ZM7 2h4v2H7V2Zm1 14a1 1 0 1 1-2 0V8a1 1 0 0 1 2 0v8Zm4 0a1 1 0 0 1-2 0V8a1 1 0 0 1 2 0v8Z" />
                                </svg>
                                {{ $tandaVital['waktuPemeriksaan'] }}
                            </x-alternative-button>
                        </td>
                    </tr>
                @endforeach



            </tbody>
        </table>

    </div>


</div>
