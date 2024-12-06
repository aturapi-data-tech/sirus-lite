<div class="h-[calc(100vh-250px)] mt-2 overflow-auto">
    <!-- Table -->
    <table class="w-full text-sm text-left text-gray-700 table-auto ">
        <thead class="sticky top-0 text-xs text-gray-900 uppercase bg-gray-100 ">
            <tr>
                <th scope="col" class="w-3/4 px-4 py-3 ">
                    Template Resep
                </th>

                <th scope="col" class="w-1/4 px-4 py-3 ">
                    Action
                </th>
            </tr>
        </thead>

        <tbody class="bg-white ">

            @foreach ($myQueryData as $myQData)
                <tr class="border-b group dark:border-gray-700">


                    <td class="px-4 py-3 group-hover:bg-gray-100 whitespace-nowrap dark:text-white">
                        <div class="" wire:click="setTemprId('{{ $myQData->tempr_id ?? '' }}')">

                            {{-- <div class="text-sm text-primary">
                                Id :{{ $myQData->tempr_id }}
                            </div> --}}
                            <div class="text-sm text-primary">
                                {{ $myQData->tempr_desc }}
                            </div>

                        </div>
                    </td>






                    <td class="px-4 py-3 group-hover:bg-gray-100 group-hover:text-primary">
                        {{-- delete Modal --}}

                        @include('livewire.master-dokter.form-entry-template-resep-dokter.delete-confirmation')


                    </td>
                </tr>
            @endforeach

        </tbody>
    </table>

    {{-- no data found start --}}
    @if ($myQueryData->count() == 0)
        <div class="w-full p-4 text-sm text-center text-gray-900 dark:text-gray-400">
            {{ 'Data  Tidak ditemukan' }}
        </div>
    @endif
    {{-- no data found end --}}

</div>
