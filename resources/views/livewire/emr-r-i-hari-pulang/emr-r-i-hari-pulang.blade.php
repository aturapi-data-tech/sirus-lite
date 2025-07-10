<div>


    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


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

            <div class="flex items-end w-full">
                {{-- Cari Data --}}
                <div class="relative w-1/3 py-2 mr-2 pointer-events-auto">
                    <div class="absolute inset-y-0 left-0 flex items-center p-5 pl-3 pointer-events-none ">
                        <svg width="24" height="24" fill="none" aria-hidden="true" class="flex-none mr-3 ">
                            <path d="m19 19-3.5-3.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"></path>
                            <circle cx="11" cy="11" r="6" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round"></circle>
                        </svg>
                    </div>

                    <x-text-input type="text" class="w-full p-2 pl-10" placeholder="Cari Data" autofocus
                        wire:model="refFilter" />
                </div>





                {{-- Status Klaim --}}
                <div class="ml-4">
                    <x-dropdown align="right" :width="__('80')" :contentclasses="__('overflow-auto max-h-[150px] py-1 bg-white dark:bg-gray-700')">
                        <x-slot name="trigger">
                            {{-- Button Status Klaim --}}
                            <x-alternative-button class="inline-flex whitespace-nowrap">
                                <svg class="-ml-1 mr-1.5 w-5 h-5" fill="currentColor" viewbox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path clip-rule="evenodd" fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                                </svg>
                                <span class="font-semibold">{{ $myTopBar['klaimStatusName'] }}</span>
                            </x-alternative-button>
                        </x-slot>
                        {{-- Open shiftcontent --}}
                        <x-slot name="content">

                            @foreach ($myTopBar['klaimStatusOptions'] as $dr)
                                <x-dropdown-link
                                    wire:click="settermyTopBarklaimStatusOptions('{{ $dr['klaimStatusId'] }}','{{ addslashes($dr['klaimStatusName']) }}')">
                                    {{ __($dr['klaimStatusName']) }}
                                </x-dropdown-link>
                            @endforeach
                        </x-slot>
                    </x-dropdown>
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
                        <th scope="col" class="w-1/3 px-4 py-3 ">
                            Pasien
                        </th>
                        <th scope="col" class="w-2/3 px-4 py-3 ">
                            Status Layanan
                        </th>
                        <th scope="col" class="w-32 px-4 py-3 ">
                            Action
                        </th>
                    </tr>
                </thead>

                <tbody class="bg-white ">

                    @foreach ($myQueryData as $myQData)
                        @php
                            $datadaftar_json = json_decode($myQData->datadaftarri_json, true) ?? [];

                            // Cek klaim BPJS
                            $klaim = DB::table('rsmst_klaimtypes')
                                ->where('klaim_id', $myQData->klaim_id)
                                ->select('klaim_status', 'klaim_desc')
                                ->first();
                            // Boolean BPJS
                            $isBpjs = optional($klaim)->klaim_status === 'BPJS';

                            // Deskripsi klaim (fallback jika null)
                            $klaimDesc = $klaim->klaim_desc ?? 'Asuransi Lain';

                            $badgecolorKlaim = match (true) {
                                $myQData->klaim_id === 'UM' => 'green', // Umum
                                $isBpjs => 'yellow', // BPJS
                                $myQData->klaim_id === 'KR' => 'red', // Kronis
                                default => 'slate', // Asuransi Lain
                            };

                        @endphp


                        <tr class="border-b group">


                            <td class="px-4 py-3 group-hover:bg-gray-100 whitespace-nowrap ">
                                <div class="p-2 space-y-2 bg-white rounded-lg shadow">
                                    {{-- Header: Registrasi & Pasien --}}
                                    <div class="space-y-1">
                                        <div class="text-lg font-semibold text-primary">
                                            {{ $myQData->reg_no }}
                                        </div>
                                        <div class="font-semibold text-gray-900">
                                            {{ $myQData->reg_name }} / ({{ $myQData->sex }}) / {{ $myQData->thn }}
                                        </div>
                                        <div class="text-sm text-gray-700">
                                            {{ $myQData->address }}
                                        </div>
                                    </div>


                                    {{-- inacbg --}}
                                    <div class="p-2 m-2 bg-gray-200 rounded-lg">
                                        @include('livewire.emr-r-i-hari.emr-r-i-hari-inacbg-rs-table')
                                    </div>

                                </div>

                            </td>


                            <td class="px-4 py-3 align-top group-hover:bg-gray-100 whitespace-nowrap ">
                                <div class="grid grid-cols-4 gap-2">
                                    <div class="col-span-3">

                                        @include('livewire.emr-r-i.emr-r-i-leveling-dokter-table')

                                        <div class="grid grid-cols-1 gap-2 mt-2">
                                            {{-- Tanggal Masuk --}}
                                            <div class="grid grid-cols-2 gap-2 text-xs text-gray-700">
                                                <div class="flex items-center">
                                                    <span class="mr-1 font-semibold">Tgl Masuk:</span>
                                                    <span>{{ $myQData->entry_date ?? '-' }}</span>
                                                </div>

                                                <div class="flex items-center">
                                                    <span class="mr-1 font-semibold">Tgl Pulang:</span>
                                                    <span>{{ $myQData->exit_date ?? '-' }}</span>
                                                </div>
                                            </div>

                                            <div class="grid gap-2 text-xs text-gray-700">

                                                {{-- Bangsal, Kamar & Bed --}}
                                                <div class="space-y-0.5">
                                                    <div class="font-medium">{{ $myQData->bangsal_name }}</div>
                                                    <div class="text-gray-600">
                                                        {{ $myQData->room_name }} &middot; Bed:
                                                        {{ $myQData->bed_no }}
                                                    </div>
                                                </div>

                                                {{-- Badges: Klaim / SEP / Laborat / Radiologi --}}
                                                <div class="flex flex-wrap items-center gap-2 pt-1">
                                                    {{-- Klaim --}}
                                                    @if (optional($klaim)->klaim_status === 'BPJS')
                                                        <span
                                                            class="inline-flex items-center px-2 py-0.5 rounded bg-yellow-100 text-yellow-800 text-xs font-medium">
                                                            {{ $klaim->klaim_status }} – {{ $klaimDesc }}
                                                        </span>
                                                    @else
                                                        <span
                                                            class="inline-flex items-center px-2 py-0.5 rounded bg-gray-100 text-gray-800 text-xs font-medium">
                                                            {{ $klaimDesc }}
                                                        </span>
                                                    @endif

                                                    {{-- Nomor SEP --}}
                                                    @if ($myQData->vno_sep)
                                                        <span
                                                            class="inline-flex items-center px-2 py-0.5 rounded bg-blue-50 text-blue-800 text-xs">
                                                            SEP: {{ $myQData->vno_sep }}
                                                        </span>
                                                    @endif


                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </td>



                            <td class="px-4 py-3 align-top group-hover:bg-gray-100 group-hover:text-primary">

                                @if ($myTopBar['klaimStatusId'] === 'BPJS')
                                    <div class="flex justify-end w-1/2 pt-8">
                                        <x-dropdown align="right" width="500"
                                            contentclasses="overflow-auto max-h-[500px] py-1 bg-white dark:bg-gray-700">
                                            <x-slot name="trigger">
                                                {{-- Button myLimitPerPage --}}
                                                <x-alternative-button class="inline-flex">
                                                    <svg class="w-6 h-6 text-gray-800 dark:text-white"
                                                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                        width="24" height="24" fill="none"
                                                        viewBox="0 0 24 24">
                                                        <path stroke="currentColor" stroke-linecap="round"
                                                            stroke-linejoin="round" stroke-width="2"
                                                            d="M15 17h3a3 3 0 0 0 0-6h-.025a5.56 5.56 0 0 0 .025-.5A5.5 5.5 0 0 0 7.207 9.021C7.137 9.017 7.071 9 7 9a4 4 0 1 0 0 8h2.167M12 19v-9m0 0-2 2m2-2 2 2" />
                                                    </svg>
                                                </x-alternative-button>
                                            </x-slot>
                                            {{-- Open myLimitPerPagecontent --}}
                                            <x-slot name="content">
                                                <div class="grid grid-cols-1 gap-2 p-2">
                                                    <livewire:emr-r-i.post-inacbg-r-i.post-inacbg-r-i :riHdrNoRef="$myQData->rihdr_no"
                                                        :groupingCount="$myQData->riuploadbpjs_grouping_count ?? 0"
                                                        :wire:key="'post-inacbg-r-i-'.$myQData->rihdr_no">
                                                </div>
                                            </x-slot>
                                        </x-dropdown>
                                    </div>
                                @endif

                            </td>
                        </tr>
                    @endforeach

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

        {{ $myQueryData->links() }}








    </div>



    {{-- Canvas
    Main BgColor /
    Size H/W --}}

    {{-- End Coding --}}


</div>
