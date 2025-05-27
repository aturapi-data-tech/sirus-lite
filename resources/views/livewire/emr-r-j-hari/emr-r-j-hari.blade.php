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

            <div class="flex w-full">
                {{-- Cari Data --}}
                <div class="relative w-1/3 mr-2 pointer-events-auto">
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
                {{-- Cari Data --}}

                {{-- Tanggal --}}
                <div class="relative w-[150px] mr-2">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg aria-hidden="true" class="w-5 h-5 text-gray-900 dark:text-gray-400" fill="currentColor"
                            viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </div>

                    <x-text-input type="text" class="p-2 pl-10 " placeholder="[dd/mm/yyyy]"
                        wire:model="myTopBar.refDate" />
                </div>


                {{-- Dokter --}}
                <div>
                    <x-dropdown align="right" :width="__('80')" :contentclasses="__('overflow-auto max-h-[150px] py-1 bg-white dark:bg-gray-700')">
                        <x-slot name="trigger">
                            {{-- Button Dokter --}}
                            <x-alternative-button class="inline-flex whitespace-nowrap">
                                <svg class="-ml-1 mr-1.5 w-5 h-5" fill="currentColor" viewbox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path clip-rule="evenodd" fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                                </svg>
                                <span>{{ $myTopBar['drName'] }}</span>
                            </x-alternative-button>
                        </x-slot>
                        {{-- Open shiftcontent --}}
                        <x-slot name="content">

                            @foreach ($myTopBar['drOptions'] as $dr)
                                <x-dropdown-link
                                    wire:click="settermyTopBardrOptions('{{ $dr['drId'] }}','{{ addslashes($dr['drName']) }}')">
                                    {{ __($dr['drName']) }}
                                </x-dropdown-link>
                            @endforeach
                        </x-slot>
                    </x-dropdown>
                </div>

                {{-- Status Klaim --}}
                <div>
                    <x-dropdown align="right" :width="__('80')" :contentclasses="__('overflow-auto max-h-[150px] py-1 bg-white dark:bg-gray-700')">
                        <x-slot name="trigger">
                            {{-- Button Status Klaim --}}
                            <x-alternative-button class="inline-flex whitespace-nowrap">
                                <svg class="-ml-1 mr-1.5 w-5 h-5" fill="currentColor" viewbox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path clip-rule="evenodd" fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                                </svg>
                                <span>{{ $myTopBar['klaimStatusName'] }}</span>
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



            <div class="flex justify-end w-1/2">

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
            <table class="w-full text-sm text-left text-gray-700 table-auto ">
                <thead class="sticky top-0 text-xs text-gray-900 uppercase bg-gray-100 ">
                    <tr>
                        <th scope="col" class="w-1/5 px-4 py-3 ">
                            Pasien
                        </th>
                        <th scope="col" class="w-1/5 px-4 py-3 ">
                            Poli
                        </th>
                        <th scope="col" class="w-1/5 px-4 py-3 ">
                            Status Layanan
                        </th>
                        <th scope="col" class="w-1/5 px-4 py-3 ">
                            Action
                        </th>
                    </tr>
                </thead>

                <tbody class="bg-white ">
                    @foreach ($myQueryData as $key => $myQData)
                        @php
                            $datadaftar_json = json_decode($myQData->datadaftarpolirj_json, true);
                            $anamnesa = isset($datadaftar_json['anamnesa']) ? 1 : 0;
                            $pemeriksaan = isset($datadaftar_json['pemeriksaan']) ? 1 : 0;
                            $penilaian = isset($datadaftar_json['penilaian']) ? 1 : 0;
                            $procedure = isset($datadaftar_json['procedure']) ? 1 : 0;
                            $diagnosis = isset($datadaftar_json['diagnosis']) ? 1 : 0;
                            $perencanaan = isset($datadaftar_json['perencanaan']) ? 1 : 0;
                            $prosentaseEMR =
                                (($anamnesa + $pemeriksaan + $penilaian + $procedure + $diagnosis + $perencanaan) / 6) *
                                100;

                            $bgSelesaiPemeriksaan = isset(
                                $datadaftar_json['perencanaan']['pengkajianMedis']['drPemeriksa'],
                            )
                                ? ($datadaftar_json['perencanaan']['pengkajianMedis']['drPemeriksa']
                                    ? 'bg-green-100'
                                    : '')
                                : '';

                            $badgecolorEmr = $prosentaseEMR >= 80 ? 'green' : 'red';

                            $badgecolorStatus = isset($myQData->rj_status)
                                ? ($myQData->rj_status === 'A'
                                    ? 'red'
                                    : ($myQData->rj_status === 'L'
                                        ? 'green'
                                        : ($myQData->rj_status === 'I'
                                            ? 'green'
                                            : ($myQData->rj_status === 'F'
                                                ? 'yellow'
                                                : 'default'))))
                                : '';

                            $badgecolorKlaim =
                                $myQData->klaim_id == 'UM'
                                    ? 'green'
                                    : ($myQData->klaim_id == 'JM'
                                        ? 'default'
                                        : ($myQData->klaim_id == 'KR'
                                            ? 'yellow'
                                            : 'red'));
                        @endphp


                        <tr class="border-b group {{ $bgSelesaiPemeriksaan }}">

                            <td class="px-4 py-3 group-hover:bg-gray-100">
                                <div class="">
                                    <div class="font-semibold text-primary">
                                        <span class="text-2xl text-gray-700">{{ $myQueryData->firstItem() + $key }}
                                        </span>{{ $myQData->reg_no }}
                                    </div>
                                    <div class="font-semibold text-gray-900">
                                        {{ $myQData->reg_name . ' / (' . $myQData->sex . ')' . ' / ' . $myQData->thn }}
                                    </div>
                                    <div class="font-normal text-gray-900">
                                        {{ $myQData->address }}
                                    </div>
                                </div>
                            </td>


                            <td class="px-4 py-3 group-hover:bg-gray-100">
                                <div class="">
                                    <div class="font-semibold text-primary">{{ $myQData->poli_desc }}
                                    </div>
                                    <div class="font-semibold text-gray-900">
                                        {{ $myQData->dr_name . ' / ' }}
                                        <x-badge :badgecolor="__($badgecolorKlaim)">
                                            {{ $myQData->klaim_id == 'UM'
                                                ? 'UMUM'
                                                : ($myQData->klaim_id == 'JM'
                                                    ? 'BPJS'
                                                    : ($myQData->klaim_id == 'KR'
                                                        ? 'Kronis'
                                                        : 'Asuransi Lain')) }}
                                        </x-badge>

                                    </div>
                                    <div class="font-normal text-gray-900">
                                        {{ 'Nomer Pelayanan ' . $myQData->no_antrian }}
                                    </div>
                                    <div class="font-normal">
                                        {{ $myQData->vno_sep }}
                                    </div>
                                    <div class="flex my-2 space-x-2">
                                        @if ($myQData->lab_status)
                                            <x-badge :badgecolor="__('default')">
                                                {{ 'Laborat' }}
                                            </x-badge>
                                        @endif

                                        @if ($myQData->rad_status)
                                            <x-badge :badgecolor="__('default')">
                                                {{ 'Radiologi' }}
                                            </x-badge>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-3 group-hover:bg-gray-100">
                                <div class="">
                                    <div class="font-semibold text-primary">
                                        {{ $myQData->rj_date }}
                                    </div>
                                    <div class="flex italic font-semibold text-gray-900">
                                        <x-badge :badgecolor="__($badgecolorStatus)">
                                            {{ isset($myQData->rj_status)
                                                ? ($myQData->rj_status === 'A'
                                                    ? 'Pelayanan'
                                                    : ($myQData->rj_status === 'L'
                                                        ? 'Selesai Pelayanan'
                                                        : ($myQData->rj_status === 'I'
                                                            ? 'Transfer Inap'
                                                            : ($myQData->rj_status === 'F'
                                                                ? 'Batal Transaksi'
                                                                : ''))))
                                                : '' }}
                                        </x-badge>
                                        <x-badge :badgecolor="__($badgecolorEmr)">
                                            Emr: {{ $prosentaseEMR . '%' }}
                                        </x-badge>
                                    </div>
                                    <div class="font-normal text-gray-900">
                                        {{ '' . $myQData->nobooking }}
                                    </div>

                                    <div class="mt-1 font-normal">
                                        Tindak Lanjut :
                                        {{ isset($datadaftar_json['perencanaan']['tindakLanjut']['tindakLanjut'])
                                            ? ($datadaftar_json['perencanaan']['tindakLanjut']['tindakLanjut']
                                                ? $datadaftar_json['perencanaan']['tindakLanjut']['tindakLanjut']
                                                : '-')
                                            : '-' }}
                                        </br>
                                        Tanggal :
                                        {{ isset($datadaftar_json['kontrol']['tglKontrol'])
                                            ? ($datadaftar_json['kontrol']['tglKontrol']
                                                ? $datadaftar_json['kontrol']['tglKontrol']
                                                : '-')
                                            : '-' }}
                                        </br>
                                        {{ isset($datadaftar_json['kontrol']['noSKDPBPJS'])
                                            ? ($datadaftar_json['kontrol']['noSKDPBPJS']
                                                ? $datadaftar_json['kontrol']['noSKDPBPJS']
                                                : '-')
                                            : '-' }}
                                    </div>

                                </div>
                            </td>


                            <td class="px-4 py-3 group-hover:bg-gray-100 group-hover:text-primary">

                                <!-- Dropdown Action menu Flowbite-->
                                <div>
                                    <x-light-button id="dropdownButtonSendData{{ $myQData->rj_no }}"
                                        class="inline-flex"
                                        wire:click="$emit('pressDropdownButtonSendData','{{ $myQData->rj_no }}')">
                                        <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round"
                                                stroke-linejoin="round" stroke-width="2"
                                                d="M15 17h3a3 3 0 0 0 0-6h-.025a5.56 5.56 0 0 0 .025-.5A5.5 5.5 0 0 0 7.207 9.021C7.137 9.017 7.071 9 7 9a4 4 0 1 0 0 8h2.167M12 19v-9m0 0-2 2m2-2 2 2" />
                                        </svg>

                                    </x-light-button>

                                    <!-- Dropdown Action Open menu -->
                                    <div id="dropdownMenuSendData{{ $myQData->rj_no }}"
                                        class="z-10 hidden w-auto bg-white divide-y divide-gray-100 rounded-lg shadow dark:bg-gray-700">

                                        <ul class="py-2 text-sm text-gray-700 dark:text-gray-200"
                                            aria-labelledby="dropdownButtonSendData{{ $myQData->rj_no }}">

                                            <div class="grid grid-cols-1 gap-2">
                                                <livewire:emr-r-j.post-inacbg-r-j.post-inacbg-r-j :rjNoRef="$myQData->rj_no"
                                                    :wire:key="'post-inacbg-r-j-'.$myQData->rj_no">
                                            </div>

                                        </ul>
                                    </div>
                                </div>
                                <!-- End Dropdown Action Open menu -->

                                @if ($myTopBar['klaimStatusId'] === 'BPJS')
                                    <div class="inline-flex">
                                        @if (!$myQData->rjuploadbpjs_rm_count)
                                            <x-primary-button
                                                wire:click.prevent="uploadRekamMedisRJGrid('{{ $myQData->rj_no }}')"
                                                type="button" wire:loading.remove>
                                                Upload Resume Medis
                                            </x-primary-button>
                                        @else
                                            <x-light-button
                                                wire:click.prevent="uploadRekamMedisRJGrid('{{ $myQData->rj_no }}')"
                                                type="button" wire:loading.remove>
                                                Update Resume Medis
                                            </x-light-button>
                                        @endif
                                        <div wire:loading wire:target="uploadRekamMedisRJGrid">
                                            <x-loading />
                                        </div>

                                    </div>

                                    @if (isset($datadaftar_json['poliId']) && $datadaftar_json['poliId'] == '12')
                                        <div class="inline-flex">
                                            @if (!$myQData->rjuploadbpjs_lain_count)
                                                <x-primary-button
                                                    wire:click.prevent="uploadRekamMedisFisioRJGrid('{{ $myQData->rj_no }}')"
                                                    type="button" wire:loading.remove>
                                                    Upload Resume Medis Fisio
                                                </x-primary-button>
                                            @else
                                                <x-light-button
                                                    wire:click.prevent="uploadRekamMedisFisioRJGrid('{{ $myQData->rj_no }}')"
                                                    type="button" wire:loading.remove>
                                                    Update Resume Medis Fisio
                                                </x-light-button>
                                            @endif
                                            <div wire:loading wire:target="uploadRekamMedisFisioRJGrid">
                                                <x-loading />
                                            </div>

                                        </div>

                                        <div class="inline-flex">
                                            @if (!$myQData->rjuploadbpjs_lain_count)
                                                <x-primary-button
                                                    wire:click.prevent="uploadRekamMedisFisioLembarHasilRJGrid('{{ $myQData->rj_no }}')"
                                                    type="button" wire:loading.remove>
                                                    Upload Resume Medis FisioLembarHasil
                                                </x-primary-button>
                                            @else
                                                <x-light-button
                                                    wire:click.prevent="uploadRekamMedisFisioLembarHasilRJGrid('{{ $myQData->rj_no }}')"
                                                    type="button" wire:loading.remove>
                                                    Update Resume Medis FisioLembarHasil
                                                </x-light-button>
                                            @endif
                                            <div wire:loading wire:target="uploadRekamMedisFisioLembarHasilRJGrid">
                                                <x-loading />
                                            </div>

                                        </div>
                                    @endif

                                    <div class="inline-flex">
                                        @if (!$myQData->rjuploadbpjs_sep_count)
                                            <x-primary-button
                                                wire:click.prevent="uploadSepRJGrid('{{ $myQData->rj_no }}')"
                                                type="button" wire:loading.remove>
                                                Upload SEP
                                            </x-primary-button>
                                        @else
                                            <x-light-button
                                                wire:click.prevent="uploadSepRJGrid('{{ $myQData->rj_no }}')"
                                                type="button" wire:loading.remove>
                                                Update SEP
                                            </x-light-button>
                                        @endif
                                        <div wire:loading wire:target="uploadSepRJGrid">
                                            <x-loading />
                                        </div>
                                    </div>

                                    <div class="inline-flex">
                                        @if (!$myQData->rjuploadbpjs_skdp_count)
                                            <x-primary-button
                                                wire:click.prevent="uploadSkdpRJGrid('{{ $myQData->rj_no }}')"
                                                type="button" wire:loading.remove>
                                                Upload SKDP
                                            </x-primary-button>
                                        @else
                                            <x-light-button
                                                wire:click.prevent="uploadSkdpRJGrid('{{ $myQData->rj_no }}')"
                                                type="button" wire:loading.remove>
                                                Update SKDP
                                            </x-light-button>
                                        @endif
                                        <div wire:loading wire:target="uploadSkdpRJGrid">
                                            <x-loading />
                                        </div>

                                    </div>


                                    {{-- KRONIS --}}
                                @elseif ($myTopBar['klaimStatusId'] === 'KRONIS')
                                    <div class="inline-flex">
                                        @if (!$myQData->rjuploadbpjs_sep_count)
                                            <x-primary-button
                                                wire:click.prevent="uploadSepRJGrid('{{ $myQData->rj_no }}')"
                                                type="button" wire:loading.remove>
                                                Upload SEP
                                            </x-primary-button>
                                        @else
                                            <x-light-button
                                                wire:click.prevent="uploadSepRJGrid('{{ $myQData->rj_no }}')"
                                                type="button" wire:loading.remove>
                                                Update SEP
                                            </x-light-button>
                                        @endif
                                        <div wire:loading wire:target="uploadSepRJGrid">
                                            <x-loading />
                                        </div>
                                    </div>

                                    <div class="inline-flex">
                                        @if (!$myQData->rjuploadbpjs_sep_count)
                                            <x-primary-button
                                                wire:click.prevent="uploadSepRJGrid('{{ $myQData->rj_no }}')"
                                                type="button" wire:loading.remove>
                                                Upload Resep
                                            </x-primary-button>
                                        @else
                                            <x-light-button
                                                wire:click.prevent="uploadSepRJGrid('{{ $myQData->rj_no }}')"
                                                type="button" wire:loading.remove>
                                                Update Resep
                                            </x-light-button>
                                        @endif
                                        <div wire:loading wire:target="uploadSepRJGrid">
                                            <x-loading />
                                        </div>
                                    </div>


                                    <div class="inline-flex">
                                        @if (!$myQData->rjuploadbpjs_sep_count)
                                            <x-primary-button
                                                wire:click.prevent="uploadSepRJGrid('{{ $myQData->rj_no }}')"
                                                type="button" wire:loading.remove>
                                                Upload Kwitansi
                                            </x-primary-button>
                                        @else
                                            <x-light-button
                                                wire:click.prevent="uploadSepRJGrid('{{ $myQData->rj_no }}')"
                                                type="button" wire:loading.remove>
                                                Update Kwitansi
                                            </x-light-button>
                                        @endif
                                        <div wire:loading wire:target="uploadSepRJGrid">
                                            <x-loading />
                                        </div>
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




    {{-- push start ///////////////////////////////// --}}
    @push('scripts')
        {{-- Global Livewire JavaScript Object start --}}
        <script type="text/javascript">
            // press_dropdownButtonSendData flowbite
            window.Livewire.on('pressDropdownButtonSendData', (key) => {
                    // set the dropdown menu element
                    const $targetEl = document.getElementById('dropdownMenuSendData' + key);

                    // set the element that trigger the dropdown menu on click
                    const $triggerEl = document.getElementById('dropdownButtonSendData' + key);

                    // options with default values
                    const options = {
                        placement: 'left',
                        triggerType: 'click',
                        offsetSkidding: 0,
                        offsetDistance: 10,
                        delay: 300,
                        onHide: () => {
                            console.log('dropdown has been hidden');

                        },
                        onShow: () => {
                            console.log('dropdown has been shown');
                        },
                        onToggle: () => {
                            console.log('dropdown has been toggled');
                        }
                    };

                    /*
                     * $targetEl: required
                     * $triggerEl: required
                     * options: optional
                     */
                    const dropdown = new Dropdown($targetEl, $triggerEl, options);

                    dropdown.show();

                }

            );
        </script>
    @endpush







</div>
