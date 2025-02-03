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
                                        AdminLog
                                    </x-sort-link>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800">

                            @isset($dataAdminLog['riAdminLog'])
                                @foreach ($dataAdminLog['riAdminLog'] as $key => $AdminLog)
                                    <tr class="border-b group dark:border-gray-700">

                                        <td
                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                            {{ $AdminLog['userLogDate'] }}




                                        </td>

                                        <td
                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                            @if (str_contains(strtolower($AdminLog['userLogDesc']), 'remove'))
                                                <span class="text-red-600">{{ 'User Log: ' . $AdminLog['userLog'] }}</span>
                                                </br>
                                                <span class="text-red-600">{{ $AdminLog['userLogDesc'] }}</span>
                                            @else
                                                <span class="text-gray-700">{{ 'User Log: ' . $AdminLog['userLog'] }}</span>
                                                </br>
                                                <span class="text-gray-700">{{ $AdminLog['userLogDesc'] }}</span>
                                            @endif
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
