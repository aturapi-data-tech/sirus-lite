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
                                        Room
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

                            @isset($dataRoom['riRoom'])
                                @foreach ($dataRoom['riRoom'] as $key => $Room)
                                    <tr class="border-b group dark:border-gray-700">

                                        <td
                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                            {{ $Room['start_date'] }}
                                            {{ $Room['end_date'] }}
                                        </td>

                                        <td
                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                            {{ $Room['room_id'] }}
                                        </td>


                                        <td
                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                            Kamar:{{ number_format($Room['room_price']) }}X
                                            {{ number_format($Room['day']) }}=
                                            {{ number_format($Room['room_price'] * $Room['day']) }}
                                            <br>
                                            Perawatan:{{ number_format($Room['perawatan_price']) }}X
                                            {{ number_format($Room['day']) }}=
                                            {{ number_format($Room['perawatan_price'] * $Room['day']) }}
                                            <br>
                                            Umum:{{ number_format($Room['common_service']) }}X
                                            {{ number_format($Room['day']) }}=
                                            {{ number_format($Room['common_service'] * $Room['day']) }}


                                        </td>

                                    </tr>
                                @endforeach
                            @endisset


                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>

</div>
