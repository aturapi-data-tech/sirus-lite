<div>
    <div class="w-full mb-1">


        <table class="w-full text-sm text-left text-gray-900 table-auto dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="text-center ">
                        Tanggal / Jam
                    </th>
                    <th scope="col" class="text-center ">
                        Nama Obat Atau Jenis Cairan
                    </th>
                    <th scope="col" class="text-center ">
                        Jumlah
                    </th>
                    <th scope="col" class="text-center ">
                        Dosis
                    </th>
                    <th scope="col" class="text-center ">
                        Route
                    </th>
                    <th scope="col" class="text-center ">
                        Keterangan
                    </th>
                    <th scope="col" class="text-center ">
                        Action
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800">

                @php
                    use Carbon\Carbon;

                    $sortedObatDanCairan = collect(
                        $this->dataDaftarRi['observasi']['obatDanCairan']['pemberianObatDanCairan'] ?? [],
                    )->sortByDesc(function ($item) {
                        return Carbon::createFromFormat('d/m/Y H:i:s', $item['waktuPemberian'], env('APP_TIMEZONE'));
                    });

                @endphp

                @foreach ($sortedObatDanCairan ?? [] as $pemberianObatDanCairan)
                    <tr class="border-b group dark:border-gray-700 ">

                        <td class="text-center group-hover:bg-gray-100 group-hover:text-primary">
                            {{ $pemberianObatDanCairan['waktuPemberian'] }}
                            <br>
                            {{ $pemberianObatDanCairan['pemeriksa'] }}
                        </td>
                        <td class="text-center group-hover:bg-gray-100 group-hover:text-primary">
                            {{ $pemberianObatDanCairan['namaObatAtauJenisCairan'] }}
                        </td>
                        <td class="text-center group-hover:bg-gray-100 group-hover:text-primary">
                            {{ $pemberianObatDanCairan['jumlah'] }}
                        </td>
                        <td class="text-center group-hover:bg-gray-100 group-hover:text-primary">
                            {{ $pemberianObatDanCairan['dosis'] }}
                        </td>
                        <td class="text-center group-hover:bg-gray-100 group-hover:text-primary">
                            {{ $pemberianObatDanCairan['rute'] }}
                        </td>
                        <td class="text-center group-hover:bg-gray-100 group-hover:text-primary">
                            {{ $pemberianObatDanCairan['keterangan'] }}

                        </td>
                        <td class="text-center group-hover:bg-gray-100 group-hover:text-primary">
                            <x-alternative-button class="inline-flex"
                                wire:click.prevent="removeObatDanCairan('{{ $pemberianObatDanCairan['waktuPemberian'] }}')">
                                <svg class="w-5 h-5 text-gray-800" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                    fill="currentColor" viewBox="0 0 18 20">
                                    <path
                                        d="M17 4h-4V2a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v2H1a1 1 0 0 0 0 2h1v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6h1a1 1 0 1 0 0-2ZM7 2h4v2H7V2Zm1 14a1 1 0 1 1-2 0V8a1 1 0 0 1 2 0v8Zm4 0a1 1 0 0 1-2 0V8a1 1 0 0 1 2 0v8Z" />
                                </svg>
                                {{ $pemberianObatDanCairan['waktuPemberian'] }}
                            </x-alternative-button>
                        </td>
                    </tr>
                @endforeach



            </tbody>
        </table>

    </div>


</div>
