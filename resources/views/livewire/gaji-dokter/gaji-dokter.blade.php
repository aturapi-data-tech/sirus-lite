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

        {{-- Top Bar --}}
        <div class="flex justify-between">

            <div class="flex w-full item-end">
                {{-- Cari Data --}}
                <div class="relative w-1/3 py-2 mt-6 mr-2 pointer-events-auto">
                    <div class="absolute inset-y-0 left-0 flex p-5 pl-3 pointer-events-none item-center ">
                        <svg class="w-6 h-6 text-gray-700 dark:text-white" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                            viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 10h16m-8-3V4M7 7V4m10 3V4M5 20h14a1 1 0 0 0 1-1V7a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1Zm3-7h.01v.01H8V13Zm4 0h.01v.01H12V13Zm4 0h.01v.01H16V13Zm-8 4h.01v.01H8V17Zm4 0h.01v.01H12V17Zm4 0h.01v.01H16V17Z" />
                        </svg>
                    </div>

                    <x-text-input type="text" class="w-full p-2 pl-10" placeholder="Cari Data" autofocus
                        wire:model="myTopBar.refBulan" />
                </div>


                {{-- Dokter --}}
                <div class="py-2">
                    {{-- LOV Dokter --}}
                    @if (empty($collectingMyDokter))
                        @include('livewire.component.l-o-v.list-of-value-dokter.list-of-value-dokter')
                    @else
                        <x-input-label for="myTopBar.drName" :value="__('Nama Dokter')" :required="__(true)"
                            wire:click='resetDokter()' />
                        <div>
                            <x-text-input id="myTopBar.drName" placeholder="Nama Dokter" class="mt-1 ml-2"
                                :errorshas="__($errors->has('myTopBar.drName'))" wire:model="myTopBar.drName" :disabled="true" />

                        </div>
                    @endif
                </div>

            </div>



            <div class="flex justify-end w-1/2 pt-8">
                <x-dropdown align="right" :width="__('20')">
                    <x-slot name="trigger">
                        {{-- Button myLimitPerPage --}}
                        <x-alternative-button class="inline-flex">
                            <svg class="-ml-1 mr-1.5 w-5 h-5" fill="currentColor" viewbox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path clip-rule="evenodd" fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                            </svg>
                            Tampil ({{ $limitPerPage }})
                        </x-alternative-button>
                    </x-slot>
                    {{-- Open myLimitPerPagecontent --}}
                    <x-slot name="content">

                        @foreach ($myLimitPerPages as $myLimitPerPage)
                            <x-dropdown-link wire:click="$set('limitPerPage', '{{ $myLimitPerPage }}')">
                                {{ __($myLimitPerPage) }}
                            </x-dropdown-link>
                        @endforeach
                    </x-slot>
                </x-dropdown>
            </div>


        </div>
        {{-- Top Bar --}}






        <div class="h-[calc(100vh-250px)] mt-2 overflow-auto">
            <!-- Table -->
            <table class="w-full text-sm text-left text-gray-700 table-auto">
                <thead class="sticky top-0 text-xs text-gray-900 uppercase bg-gray-100 ">
                    <tr>
                        <th class="w-1/5 px-4 py-3 text-left">Dokter</th>
                        <th class="w-1/5 px-4 py-3 text-left">Klaim</th>
                        <th class="w-1/5 px-4 py-3 text-left">Description</th>
                        <th class="w-1/5 px-4 py-3 text-right">Jasa Dokter</th>
                        <th class="w-1/5 px-4 py-3 text-right">Disetujui</th>

                    </tr>
                </thead>

                <tbody class="bg-white">
                    @php
                        $bgColors = ['text-primary'];
                        $colorsAvailable = $bgColors;
                        $overallTotalNominal = 0;
                        $overallTotalDisetujui = 0;
                    @endphp

                    {{-- Group by doctor --}}
                    @foreach ($myQueryData->groupBy('dr_id') as $drId => $doctorRows)
                        @php
                            $drName = $doctorRows->first()->dr_name;
                            $doctorTotalNominal = $doctorRows->sum('doc_nominal');
                            $doctorTotalDisetujui = $doctorRows->sum('disetujui');
                            $overallTotalNominal += $doctorTotalNominal;
                            $overallTotalDisetujui += $doctorTotalDisetujui;

                            // zero-based index of this foreach
                            $idx = $loop->index;
                            // pick color by cycling through $bgColors
                            $txColorDokter = $bgColors[$idx % count($bgColors)];
                        @endphp

                        {{-- Doctor header --}}
                        <tr class="font-bold {{ $txColorDokter }}">
                            <td colspan="3" class="px-4 py-2">
                                Dokter: {{ $drId }} - {{ $drName }}
                            </td>
                        </tr>

                        {{-- Within each doctor, group by klaim status --}}
                        @foreach ($doctorRows->groupBy('klaim_status') as $klaimStatus => $klaimStatusRows)
                            @php
                                $klaimStatusTotalNominal = $klaimStatusRows->sum('doc_nominal');
                                $klaimStatusTotalDisetujui = $klaimStatusRows->sum('disetujui');
                            @endphp
                            {{-- Detail rows --}}
                            @foreach ($klaimStatusRows as $row)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-1 whitespace-nowrap"></td>
                                    <td class="px-4 py-1 whitespace-nowrap">{{ $klaimStatus }}</td>
                                    <td class="px-4 py-1 whitespace-nowrap">{{ $row->desc_doc }}</td>
                                    <td class="px-4 py-1 text-right whitespace-nowrap">
                                        {{ number_format($row->doc_nominal, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-1 text-right whitespace-nowrap">
                                        {{ number_format($row->disetujui, 0, ',', '.') }}
                                        <br>
                                        @forelse($row->tidak_disetujui as $tidakDisetujuiu)
                                            <div class="text-xs text-gray-500">
                                                ({{ number_format($tidakDisetujuiu['doc_nominal'], 0, ',', '.') }} /
                                                Txn No:
                                                {{ $tidakDisetujuiu['txn_no'] }})
                                            </div>
                                        @empty
                                            <em class="text-xs text-gray-500">Semua OK</em>
                                        @endforelse
                                    </td>
                                </tr>
                            @endforeach

                            {{-- Status subtotal --}}
                            <tr class="font-semibold bg-gray-50">
                                <td class="px-4 py-2"></td>
                                <td class="px-4 py-2"></td>
                                <td class="px-4 py-2 text-xs text-right" colspan="1">
                                    Subtotal {{ $klaimStatus }}
                                </td>
                                <td class="px-4 py-2 text-right">
                                    {{ number_format($klaimStatusTotalNominal, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-2 text-right">
                                    {{ number_format($klaimStatusTotalDisetujui, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach

                        {{-- Doctor subtotal --}}
                        <tr class="font-semibold bg-gray-100 {{ $txColorDokter }}">
                            <td colspan="3" class="px-4 py-2 text-right">
                                Subtotal Dokter {{ $drName }}
                            </td>
                            <td class="px-4 py-2 text-right">
                                {{ number_format($doctorTotalNominal, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-2 text-right">
                                {{ number_format($doctorTotalDisetujui, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach

                    {{-- Grand total --}}
                    <tr class="font-bold bg-gray-300">
                        <td colspan="3" class="px-4 py-2 text-right">
                            Total Semua Dokter & Klaim
                        </td>
                        <td class="px-4 py-2 text-right">
                            {{ number_format($overallTotalNominal, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-2 text-right">
                            {{ number_format($overallTotalDisetujui, 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>

            </table>

            {{-- no data found start --}}
            @if ($myQueryData->count() == 0)
                <div class="w-full p-4 text-sm text-center text-gray-900 dark:text-gray-400">
                    {{ 'Data ' . $myProgram . ' Tidak ditemukan' }}
                </div>
            @endif
            {{-- no data found end --}}

        </div>

        {{-- {{ $myQueryData->links() }} --}}


        <div class="grid grid-cols-2 gap-2">
            <div>
                <livewire:grouping-b-p-j-s.grouping-b-p-j-s-r-iper-dokter :allSepPerDokter=$allSepPerDokter
                    :wire:key="$myTopBar['refBulan'].$myTopBar['drId'].'grouping-b-p-j-s-r-iper-dokter'">
            </div>

            <div>
                <livewire:grouping-b-p-j-s.grouping-b-p-j-s-r-jper-dokter :allSepPerDokter=$allSepPerDokter
                    :wire:key="$myTopBar['refBulan'].$myTopBar['drId'].'grouping-b-p-j-s-r-jper-dokter'">
            </div>
        </div>





    </div>



    {{-- Canvas
    Main BgColor /
    Size H/W --}}

    {{-- End Coding --}}



</div>
