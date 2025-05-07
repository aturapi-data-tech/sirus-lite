<div>

    <div class="flex flex-col my-2">
        <div class="overflow-x-auto rounded-lg">
            <div class="inline-block min-w-full align-middle">
                <div class="overflow-hidden shadow sm:rounded-lg">
                    <table class="w-full text-sm text-left text-gray-500 table-auto dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                            <tr>

                                <th scope="col" class="px-4 py-3 ">
                                    <x-sort-link :active=false wire:click.prevent="" role="button" href="#">
                                        Tgl
                                    </x-sort-link>
                                </th>

                                <th scope="col" class="px-4 py-3 ">
                                    <x-sort-link :active=false wire:click.prevent="" role="button" href="#">
                                        Ok
                                    </x-sort-link>
                                </th>

                                <th scope="col" class="px-4 py-3 ">
                                    <x-sort-link :active=false wire:click.prevent="" role="button" href="#">
                                        Tarif
                                    </x-sort-link>
                                </th>

                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800">

                            @php
                                use Carbon\Carbon;

                                $sortedRiOk = collect($dataOk['riOk'] ?? [])
                                    ->sortByDesc(function ($item) {
                                        $date = $item['ok_date'] ?? '';

                                        // Jika kosong, kembalikan 0 agar muncul paling bawah
                                        if (!$date) {
                                            return 0;
                                        }

                                        try {
                                            return Carbon::createFromFormat(
                                                'd/m/Y H:i:s',
                                                $date,
                                                env('APP_TIMEZONE'),
                                            )->timestamp;
                                        } catch (\Exception $e) {
                                            // Jika parsing gagal, juga kembalikan 0
                                            return 0;
                                        }
                                    })
                                    ->values();
                            @endphp

                            @if ($sortedRiOk->isNotEmpty())
                                @foreach ($sortedRiOk as $key => $Ok)
                                    <tr class="border-b group dark:border-gray-700">

                                        <td
                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                            {{ $Ok['ok_date'] }}
                                        </td>

                                        <td
                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                            {{ $Ok['ok_desc'] }}
                                        </td>


                                        <td
                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                            {{ number_format($Ok['ok_price']) }}
                                        </td>

                                    </tr>
                                @endforeach
                            @endif


                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>

</div>
