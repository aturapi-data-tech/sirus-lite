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

                            // Hitung total RS & INA

                            $totalrs = $myQData->totalri_temp ?? 0;

                            $totalinacbgRs = $myQData->totinacbg_temp ?: $totalrs;

                            $totalinacbgSirus = data_get(
                                $datadaftar_json,
                                'inacbg.set_claim_data_done.grouper.response.cbg.base_tariff',
                                $totalrs,
                            );

                            $persentasiRs = $totalinacbgRs ? round(($totalrs / $totalinacbgRs) * 100) : 0;
                            $persentasiSirus = $totalinacbgSirus ? round(($totalrs / $totalinacbgSirus) * 100) : 0;

                            // Warna badge berdasarkan persentase
                            $badgeColorPersentasiRs =
                                $persentasiRs < 50 ? 'green' : ($persentasiRs < 80 ? 'yellow' : 'red');

                            $badgeColorPersentasiSirus =
                                $persentasiSirus < 50 ? 'green' : ($persentasiSirus < 80 ? 'yellow' : 'red');

                            // Kontrol BPJS
                            $tglKontrol = data_get($datadaftar_json, 'kontrol.tglKontrol', '');
                            $noSkdp = data_get($datadaftar_json, 'kontrol.noSKDPBPJS', '');

                            // 🔎 Ambil data Case Manager dari Form A & Form B
                            $formMPP = $datadaftar_json['formMPP'] ?? [];
                            $formAs = $formMPP['formA'] ?? [];
                            $formBs = $formMPP['formB'] ?? [];

                            // Nama CM dari Form A (Skrining)
                            $cmNamesA = collect($formAs)
                                ->pluck('tandaTanganPetugas.petugasName')
                                ->filter()
                                ->unique()
                                ->implode(', ');

                            // Nama CM dari Form B (Tindak Lanjut)
                            $cmNamesB = collect($formBs)
                                ->pluck('tandaTanganPetugas.petugasName')
                                ->filter()
                                ->unique()
                                ->implode(', ');
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

                                    {{-- Badges: Klaim, SEP, Laborat, Radiologi --}}
                                    <div class="grid grid-cols-2 gap-2">
                                        @if ($isBpjs)
                                            <span @class([
                                                'inline-flex items-center px-2 py-0.5 text-xs font-medium rounded',
                                                'bg-green-100 text-green-800' => $badgeColorPersentasiRs === 'green',
                                                'bg-yellow-100 text-yellow-800' => $badgeColorPersentasiRs === 'yellow',
                                                'bg-red-100 text-red-800' => $badgeColorPersentasiRs === 'red',
                                            ])>
                                                RS {{ number_format($totalrs, 0, ',', '.') }} |
                                                INA {{ number_format($totalinacbgRs, 0, ',', '.') }} |
                                                {{ $persentasiRs }}%
                                            </span>

                                            <span @class([
                                                'inline-flex items-center px-2 py-0.5 text-xs font-medium rounded',
                                                'bg-green-100 text-green-800' => $badgeColorPersentasiSirus === 'green',
                                                'bg-yellow-100 text-yellow-800' => $badgeColorPersentasiSirus === 'yellow',
                                                'bg-red-100 text-red-800' => $badgeColorPersentasiSirus === 'red',
                                            ])>
                                                RS {{ number_format($totalrs, 0, ',', '.') }} |
                                                INA {{ number_format($totalinacbgSirus, 0, ',', '.') }} |
                                                {{ $persentasiRs }}%
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Diagnosa & Procedure --}}
                                    <div class="text-xs text-gray-700 rounded-lg bg-gray-50">
                                        {{-- Diagnosa --}}
                                        <div class="">
                                            <div class="font-semibold">Diagnosa:</div>
                                            <div class="">
                                                {{ !empty($datadaftar_json['diagnosis']) && is_array($datadaftar_json['diagnosis'])
                                                    ? implode('# ', array_column($datadaftar_json['diagnosis'], 'icdX'))
                                                    : '-' }}
                                            </div>
                                            <div class="pl-4 text-gray-600">
                                                <span class="font-semibold">FreeText:</span>
                                                <span>{{ $datadaftar_json['diagnosisFreeText'] ?? '-' }}</span>
                                            </div>
                                        </div>

                                        {{-- Procedure --}}
                                        <div class="">
                                            <div class="font-semibold">Procedure:</div>
                                            <div class="">
                                                {{ !empty($datadaftar_json['procedure']) && is_array($datadaftar_json['procedure'])
                                                    ? implode('# ', array_column($datadaftar_json['procedure'], 'procedureId'))
                                                    : '-' }}
                                            </div>
                                            <div class="pl-4 text-gray-600">
                                                <span class="font-semibold">FreeText:</span>
                                                <span>{{ $datadaftar_json['procedureFreeText'] ?? '-' }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-xs text-gray-700">
                                        @if ($cmNamesA)
                                            <div class="text-xs text-gray-700">
                                                <span class="font-semibold">Case Manager Skrining:</span>
                                                <span>{{ $cmNamesA }}</span>
                                            </div>
                                        @endif

                                        @if ($cmNamesB)
                                            <div class="text-xs text-gray-700">
                                                <span class="font-semibold">Case Manager Tindak Lanjut:</span>
                                                <span>{{ $cmNamesB }}</span>
                                            </div>
                                        @endif
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

                                                    {{-- Laborat --}}
                                                    @if ($myQData->lab_status)
                                                        <span
                                                            class="inline-flex items-center px-2 py-0.5 rounded bg-blue-100 text-blue-800 text-xs font-medium">
                                                            Laborat
                                                        </span>
                                                    @endif

                                                    {{-- Radiologi --}}
                                                    @if ($myQData->rad_status)
                                                        <span
                                                            class="inline-flex items-center px-2 py-0.5 rounded bg-blue-100 text-blue-800 text-xs font-medium">
                                                            Radiologi
                                                        </span>
                                                    @endif

                                                    {{-- Tgl Kontrol & No SKDP (muncul di bawah SEP) --}}
                                                    @if ($tglKontrol)
                                                        <span
                                                            class="inline-flex items-center px-2 py-0.5 rounded bg-blue-50 text-blue-800 text-xs">
                                                            Tgl Kontrol: {{ $tglKontrol }}
                                                        </span>
                                                    @endif
                                                    @if ($noSkdp)
                                                        <span
                                                            class="inline-flex items-center px-2 py-0.5 rounded bg-blue-50 text-blue-800 text-xs">
                                                            No SKDP: {{ $noSkdp }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </td>



                            <td class="px-4 py-3 align-top group-hover:bg-gray-100 group-hover:text-primary">
                                <div class="flex justify-end w-1/2 pt-8">
                                    <x-dropdown align="right" width="64"
                                        contentclasses="overflow-auto max-h-[250px] py-1 bg-white dark:bg-gray-700">
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

                                            @role('Admin')
                                                <li>
                                                    <x-dropdown-link
                                                        wire:click="editDokter('{{ $myQData->rihdr_no }}','{{ $myQData->reg_no }}')">
                                                        {{ __('Assessment Dokter') }}
                                                    </x-dropdown-link>
                                                </li>

                                                <li>
                                                    <x-dropdown-link
                                                        wire:click="edit('{{ $myQData->rihdr_no }}','{{ $myQData->reg_no }}')">
                                                        {{ __('Assessment Perawat') }}
                                                    </x-dropdown-link>
                                                </li>

                                                <li>
                                                    <x-dropdown-link
                                                        wire:click="editGeneralConsentPasienRI('{{ $myQData->rihdr_no }}','{{ $myQData->reg_no }}')">
                                                        {{ __('Form Persetujuan Pasien') }}
                                                    </x-dropdown-link>
                                                </li>
                                                <li>
                                                    <x-dropdown-link
                                                        wire:click="editEdukasiPasienRI('{{ $myQData->rihdr_no }}','{{ $myQData->reg_no }}')">
                                                        {{ __('Form Edukasi Pasien') }}
                                                    </x-dropdown-link>
                                                </li>
                                                <li>
                                                    <x-dropdown-link
                                                        wire:click="editCaseManagerRI('{{ $myQData->rihdr_no }}','{{ $myQData->reg_no }}')">
                                                        {{ __('Case Manager') }}
                                                    </x-dropdown-link>
                                                </li>
                                            @endrole
                                            @role('Mr')
                                                <li>
                                                    <x-dropdown-link
                                                        wire:click="edit('{{ $myQData->rihdr_no }}','{{ $myQData->reg_no }}')">
                                                        {{ __('Assessment Perawat') }}
                                                    </x-dropdown-link>
                                                </li>
                                            @endrole
                                            @role('Tu')
                                                <li>
                                                    <x-dropdown-link
                                                        wire:click="edit('{{ $myQData->rihdr_no }}','{{ $myQData->reg_no }}')">
                                                        {{ __('Assessment Perawat') }}
                                                    </x-dropdown-link>
                                                </li>
                                            @endrole
                                            @role(['Perawat', 'Gizi', 'Casemix'])
                                                <li>
                                                    <x-dropdown-link
                                                        wire:click="edit('{{ $myQData->rihdr_no }}','{{ $myQData->reg_no }}')">
                                                        {{ __('Assessment Perawat') }}
                                                    </x-dropdown-link>
                                                </li>
                                                <li>
                                                    <x-dropdown-link
                                                        wire:click="editGeneralConsentPasienRI('{{ $myQData->rihdr_no }}','{{ $myQData->reg_no }}')">
                                                        {{ __('Form Persetujuan Pasien') }}
                                                    </x-dropdown-link>
                                                </li>
                                                <li>
                                                    <x-dropdown-link
                                                        wire:click="editEdukasiPasienRI('{{ $myQData->rihdr_no }}','{{ $myQData->reg_no }}')">
                                                        {{ __('Form Edukasi Pasien') }}
                                                    </x-dropdown-link>
                                                </li>
                                                <li>
                                                    <x-dropdown-link
                                                        wire:click="editCaseManagerRI('{{ $myQData->rihdr_no }}','{{ $myQData->reg_no }}')">
                                                        {{ __('Case Manager') }}
                                                    </x-dropdown-link>
                                                </li>
                                            @endrole
                                            @role('Dokter')
                                                <li>
                                                    <x-dropdown-link
                                                        wire:click="editDokter('{{ $myQData->rihdr_no }}','{{ $myQData->reg_no }}')">
                                                        {{ __('Assessment Dokter') }}
                                                    </x-dropdown-link>
                                                </li>
                                                <li>
                                                    <x-dropdown-link
                                                        wire:click="editGeneralConsentPasienRI('{{ $myQData->rihdr_no }}','{{ $myQData->reg_no }}')">
                                                        {{ __('Form Persetujuan Pasien') }}
                                                    </x-dropdown-link>
                                                </li>
                                                <li>
                                                    <x-dropdown-link
                                                        wire:click="editEdukasiPasienRI('{{ $myQData->rihdr_no }}','{{ $myQData->reg_no }}')">
                                                        {{ __('Form Edukasi Pasien') }}
                                                    </x-dropdown-link>
                                                </li>
                                            @endrole
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

        <script src="assets/js/signature_pad.umd.min.js"></script>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('signaturePad', (value) => ({
                    signaturePadInstance: null,
                    value: value,
                    init() {

                        this.signaturePadInstance = new SignaturePad(this.$refs.signature_canvas, {
                                minWidth: 2,
                                maxWidth: 2,
                                penColor: "rgb(11, 73, 182)"
                            }

                        );
                        this.signaturePadInstance.addEventListener("endStroke", () => {
                            // this.value = this.signaturePadInstance.toDataURL('image/png');signaturePad.toSVG()
                            // https://github.com/aturapi-data-tech/signature_pad
                            // https://gist.github.com/jonneroelofs/a4a372fe4b55c5f9c0679d432f2c0231
                            this.value = this.signaturePadInstance.toSVG();

                            // console.log(this.signaturePadInstance)
                        });
                    },
                }))
            })
        </script>
    @endpush













    @push('styles')
        {{-- stylesheet start --}}
        <link rel="stylesheet" href="{{ url('assets/plugins/toastr/toastr.min.css') }}">

        {{-- stylesheet end --}}
    @endpush
    {{-- push end --}}

</div>
