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

                {{-- Room --}}
                <div>
                    <x-dropdown align="right" :width="__('80')" :contentclasses="__('overflow-auto max-h-[150px] py-1 bg-white dark:bg-gray-700')">
                        <x-slot name="trigger">
                            {{-- Button Room --}}
                            <x-alternative-button class="inline-flex whitespace-nowrap">
                                <svg class="-ml-1 mr-1.5 w-5 h-5" fill="currentColor" viewbox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path clip-rule="evenodd" fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                                </svg>
                                <span>{{ $myTopBar['roomName'] }}</span>
                            </x-alternative-button>
                        </x-slot>
                        {{-- Open shiftcontent --}}
                        <x-slot name="content">

                            @foreach ($myTopBar['roomOptions'] as $room)
                                <x-dropdown-link
                                    wire:click="settermyTopBarroomOptions('{{ $room['roomId'] }}','{{ addslashes($room['roomName']) }}')">
                                    {{ __($room['roomName']) }}
                                </x-dropdown-link>
                            @endforeach
                        </x-slot>
                    </x-dropdown>
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
                    @error('levelingDokter.drId')
                        <x-input-error :messages=$message />
                    @enderror
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


            @if ($isOpen)
                @include('livewire.daftar-r-i.create')
            @endif

        </div>
        {{-- Top Bar --}}






        <div class="h-[calc(100vh-250px)] mt-2 overflow-auto">
            <!-- Table -->
            <table class="w-full text-sm text-left text-gray-700 table-auto">
                <thead class="sticky top-0 text-xs text-gray-900 uppercase bg-gray-100 ">
                    <tr>
                        <th scope="col" class="w-1/5 px-4 py-3 ">
                            Pasien
                        </th>
                        <th scope="col" class="w-3/5 px-4 py-3 ">
                            Inap
                        </th>
                        <th scope="col" class="w-1/5 px-4 py-3 ">
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
                            $datadaftar_json = json_decode($myQData->datadaftarri_json, true);
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

                            $badgecolorStatus = isset($myQData->ri_status)
                                ? ($myQData->ri_status === 'A'
                                    ? 'red'
                                    : ($myQData->ri_status === 'L'
                                        ? 'green'
                                        : ($myQData->ri_status === 'I'
                                            ? 'green'
                                            : ($myQData->ri_status === 'F'
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

                            $badgecolorAdministrasiRj = isset($datadaftar_json['AdministrasiRj']) ? 'green' : 'red';
                        @endphp


                        <tr class="border-b group {{ $bgSelesaiPemeriksaan }}">


                            <td class="px-4 py-3 group-hover:bg-gray-100 whitespace-nowrap ">
                                <div class="">
                                    {{-- <div class="font-normal text-gray-700">
                                        {{ 'No. Antrian ' }} <span
                                            class="text-5xl font-semibold text-gray-700">{{ $myQData->no_antrian }}</span>
                                    </div> --}}
                                    <div class="font-semibold text-primary">
                                        {{ $myQData->reg_no }}
                                    </div>
                                    <div class="font-semibold text-gray-900">
                                        {{ $myQData->reg_name . ' / (' . $myQData->sex . ')' . ' / ' . $myQData->thn }}
                                    </div>
                                    <div class="font-normal text-gray-900">
                                        {{ $myQData->address }}
                                    </div>

                                    @if ($myQData->klaim_id == 'JM')
                                        <div>
                                            @php
                                                $totalrs = $myQData->totalri_temp ?? 1;
                                                $totalinacbg = empty($myQData->totinacbg_temp)
                                                    ? $myQData->totalri_temp
                                                    : $myQData->totinacbg_temp;
                                                $persentasiTotalRsInacbg = round(($totalrs / $totalinacbg) * 100);

                                                // Menentukan warna berdasarkan persentase
                                                $badgecolorKlaim =
                                                    $persentasiTotalRsInacbg < 50
                                                        ? 'green'
                                                        : ($persentasiTotalRsInacbg < 80
                                                            ? 'yellow'
                                                            : 'red');

                                                // Menentukan label klaim
                                                $klaimLabel = match ($myQData->klaim_id) {
                                                    'UM' => 'UMUM',
                                                    'JM' => 'BPJS',
                                                    'KR' => 'Kronis',
                                                    default => 'Asuransi Lain',
                                                };
                                            @endphp

                                            <x-badge :badgecolor="__($badgecolorKlaim)">
                                                RS {{ $totalrs }} | INA {{ $totalinacbg }} |
                                                %{{ $persentasiTotalRsInacbg }} - {{ $klaimLabel }}
                                            </x-badge>
                                        </div>
                                    @endif

                                </div>
                            </td>


                            <td class="px-4 py-3 group-hover:bg-gray-100 whitespace-nowrap ">
                                <div class="grid grid-cols-4 gap-2">
                                    <div class="col-span-3">
                                        @include('livewire.emr-r-i.emr-r-i-leveling-dokter-table')
                                    </div>
                                    <div class="col-span-1">
                                        <div class="font-semibold text-gray-900">
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
                                </div>
                            </td>

                            <td class="px-4 py-3 group-hover:bg-gray-100 whitespace-nowrap ">
                                <div class="">
                                    <div class="text-sm font-semibold text-primary">
                                        {{ 'Tgl Masuk :' . $myQData->entry_date }}
                                    </div>


                                    <div class="flex italic font-semibold text-gray-900">
                                        <x-badge :badgecolor="__($badgecolorStatus)">
                                            @php
                                                switch ($myQData->ri_status) {
                                                    case 'I':
                                                        $riStatus = 'Inap';
                                                        break;

                                                    case 'P':
                                                        $riStatus = 'Pulang';
                                                        break;

                                                    default:
                                                        $riStatus = 'Inap';
                                                        break;
                                                }
                                            @endphp
                                            {{ $riStatus }}
                                        </x-badge>
                                        <x-badge :badgecolor="__($badgecolorEmr)">
                                            Emr: {{ $prosentaseEMR . '%' }}
                                        </x-badge>
                                    </div>

                                    <div class="font-normal text-gray-700">
                                        <x-badge :badgecolor="__($badgecolorAdministrasiRj)">
                                            Administrasi :
                                            @isset($datadaftar_json['AdministrasiRj'])
                                                {{ $datadaftar_json['AdministrasiRj']['userLog'] }}
                                            @else
                                                {{ '---' }}
                                            @endisset
                                        </x-badge>
                                    </div>
                                    <div>
                                        <span class="text-sm font-semibold">{{ $myQData->bangsal_name }}</span>
                                        </br>
                                        <span class="text-sm">{{ $myQData->room_name }}</span>
                                        <span class="text-sm">{{ 'Bed :' . $myQData->bed_no }}</span>

                                    </div>

                                </div>
                            </td>

                            <td class="px-4 py-3 group-hover:bg-gray-100 group-hover:text-primary">

                                <div class="flex justify-end w-1/2 pt-8">
                                    <x-dropdown align="right" width="64"
                                        contentclasses="overflow-auto max-h-[150px] py-1 bg-white dark:bg-gray-700">
                                        <x-slot name="trigger">
                                            {{-- Button myLimitPerPage --}}
                                            <x-alternative-button class="inline-flex">
                                                <svg class="w-5 h-5" aria-hidden="true" fill="currentColor"
                                                    viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                                </svg>
                                            </x-alternative-button>
                                        </x-slot>
                                        {{-- Open myLimitPerPagecontent --}}
                                        <x-slot name="content">
                                            <li>
                                                <x-dropdown-link
                                                    wire:click="edit('{{ $myQData->rihdr_no }}','{{ $myQData->reg_no }}')">
                                                    {{ __('Ubah | ' . $myQData->reg_name) }}

                                                </x-dropdown-link>
                                            </li>
                                        </x-slot>
                                    </x-dropdown>
                                </div>

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



















    {{-- push start ///////////////////////////////// --}}
    @push('scripts')
        {{-- script start --}}
        <script src="{{ url('assets/js/jquery.min.js') }}"></script>
        <script src="{{ url('assets/plugins/toastr/toastr.min.js') }}"></script>
        <script src="{{ url('assets/flowbite/dist/datepicker.js') }}"></script>

        {{-- script end --}}

        {{-- Global Livewire JavaScript Object start --}}
        <script type="text/javascript">
            toastr.options = {
                "closeButton": false,
                "debug": false,
                "newestOnTop": false,
                "progressBar": false,
                "positionClass": "toast-top-left",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            }

            window.livewire.on('toastr-success', message => toastr.success(message));
            window.Livewire.on('toastr-info', (message) => {
                toastr.info(message)
            });
            window.livewire.on('toastr-error', message => toastr.error(message));




            // press_dropdownButton flowbite
            window.Livewire.on('pressDropdownButtonUgd', (key) => {
                    // set the dropdown menu element
                    const $targetEl = document.getElementById('dropdownMenu' + key);

                    // set the element that trigger the dropdown menu on click
                    const $triggerEl = document.getElementById('dropdownButton' + key);

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













    @push('styles')
        {{-- stylesheet start --}}
        <link rel="stylesheet" href="{{ url('assets/plugins/toastr/toastr.min.css') }}">

        {{-- stylesheet end --}}
    @endpush
    {{-- push end --}}

</div>
