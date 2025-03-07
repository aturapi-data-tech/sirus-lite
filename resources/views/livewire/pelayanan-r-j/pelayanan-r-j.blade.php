<div>

    <div class="px-1 pt-7">
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
            <!-- Card header -->



            <div class="w-full mb-1">
                <div class="">


                    {{-- text --}}
                    <div class="mb-5">
                        <h3 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $myTitle }}</h3>
                        <span class="text-base font-normal text-gray-900 dark:text-gray-400">{{ $mySnipt }}</span>
                    </div>
                    {{-- end text --}}



                    <div class="md:flex md:justify-between">

                        {{-- search --}}
                        <div class="relative pointer-events-auto md:w-1/2">
                            <div class="absolute inset-y-0 left-0 flex items-center p-5 pl-3 pointer-events-none">
                                <svg width="24" height="24" fill="none" aria-hidden="true"
                                    class="flex-none mr-3">
                                    <path d="m19 19-3.5-3.5" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"></path>
                                    <circle cx="11" cy="11" r="6" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"></circle>
                                </svg>
                            </div>
                            <x-text-input id="simpleSearch" name="namesimpleSearch" type="text" class="p-2 pl-10"
                                autofocus autocomplete="simpleSearch" placeholder="Cari Data"
                                wire:model.lazy="search" />
                        </div>
                        {{-- end search --}}

                        {{-- two button --}}
                        <div class="flex justify-between mt-2 md:mt-0">

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
                                        <x-dropdown-link wire:click="setLimitPerPage({{ $myLimitPerPage }})">
                                            {{ __($myLimitPerPage) }}
                                        </x-dropdown-link>
                                    @endforeach
                                </x-slot>
                            </x-dropdown>
                        </div>
                        {{-- end two button --}}

                    </div>


                    <div class="flex rounded-lg bg-gray-50">

                        {{-- date --}}
                        <div class="relative w-1/5 mt-2 ">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg aria-hidden="true" class="w-5 h-5 text-gray-900 dark:text-gray-400"
                                    fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </div>


                            <x-text-input id="dateRjRef" datepicker datepicker-autohide datepicker-format="dd/mm/yyyy"
                                type="text" class="p-2 pl-10 " placeholder="dd/mm/yyyy" wire:model="dateRjRef" />
                        </div>

                        {{-- radio --}}
                        <div class="flex mt-2 ml-2">
                            @foreach ($statusRjRef['statusOptions'] as $sRj)
                                {{-- @dd($sRj) --}}
                                <x-radio-button :label="__($sRj['statusDesc'])" value="{{ $sRj['statusId'] }}"
                                    wire:model="statusRjRef.statusId" />
                            @endforeach
                        </div>



                        {{-- Dokter --}}
                        <div class="mt-2 ml-0">
                            <x-dropdown align="right" :width="__('80')" :contentclasses="__('overflow-auto max-h-[150px] py-1 bg-white dark:bg-gray-700')">
                                <x-slot name="trigger">
                                    {{-- Button Dokter --}}
                                    <x-alternative-button class="inline-flex">
                                        <svg class="-ml-1 mr-1.5 w-5 h-5" fill="currentColor" viewbox="0 0 20 20"
                                            xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                            <path clip-rule="evenodd" fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                                        </svg>
                                        <span>{{ 'Dokter ( ' . $drRjRef['drName'] . ' )' }}</span>
                                    </x-alternative-button>
                                </x-slot>
                                {{-- Open shiftcontent --}}
                                <x-slot name="content">

                                    @foreach ($drRjRef['drOptions'] as $dr)
                                        <x-dropdown-link
                                            wire:click="setdrRjRef('{{ $dr['drId'] }}','{{ $dr['drName'] }}')">
                                            {{ __($dr['drName']) }}
                                        </x-dropdown-link>
                                    @endforeach
                                </x-slot>
                            </x-dropdown>
                        </div>

                    </div>





                </div>



                <!-- Table -->
                <div class="flex flex-col mt-2">
                    <div class="overflow-x-auto rounded-lg">
                        <div class="inline-block min-w-full align-middle">
                            <div class="overflow-hidden shadow sm:rounded-lg">
                                <table class="w-full text-sm text-left text-gray-900 table-auto dark:text-gray-400">
                                    <thead
                                        class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                        <tr>
                                            <th scope="col" class="px-4 py-3 ">
                                                <x-sort-link :active=false wire:click.prevent="sortBy('RJp_id')"
                                                    role="button" href="#">
                                                    Pasien / Poli
                                                </x-sort-link>
                                            </th>

                                            <th scope="col" class="px-4 py-3 ">
                                                <x-sort-link :active=false wire:click.prevent="" role="button"
                                                    href="#">
                                                    SEP
                                                </x-sort-link>
                                            </th>


                                            <th scope="col" class="px-4 py-3 ">
                                                <x-sort-link :active=false wire:click.prevent="" role="button"
                                                    href="#">
                                                    Status Layanan
                                                </x-sort-link>
                                            </th>




                                            <th scope="col" class="px-4 py-3 text-center ">Action
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800">


                                        @foreach ($RJpasiens as $RJp)
                                            @php
                                                $statusLayananBgcolor = '';
                                            @endphp

                                            <tr
                                                class="border-b group dark:border-gray-700 {{ $statusLayananBgcolor }}">


                                                <td
                                                    class="px-4 py-3 break-all whitespace-nowrap group-hover:bg-gray-100 dark:text-white ">

                                                    <div class="pl-3">
                                                        <span
                                                            class="text-5xl font-semibold text-gray-700">{{ $RJp->no_antrian }}
                                                        </span>
                                                        <div class="text-base text-gray-700">
                                                            {{ $RJp->rj_date }}{{ '/ shift ' }}{{ $RJp->shift }}
                                                        </div>
                                                        <div class="text-base font-semibold text-gray-700">
                                                            {{ $RJp->reg_no }}</div>
                                                        <div class="font-semibold text-primary">
                                                            {{ $RJp->reg_name . ' / (' . $RJp->sex . ')' . ' / ' . $RJp->thn }}
                                                        </div>
                                                        <div class="font-normal text-gray-900">
                                                            {{ $RJp->address }}
                                                        </div>

                                                        <div class="pt-2 font-semibold text-gray-700">
                                                            {{ $RJp->poli_desc }}
                                                        </div>
                                                        <div class="font-semibold text-gray-900">
                                                            {{ $RJp->dr_name . ' / ' }}
                                                            {{ $RJp->klaim_id == 'UM'
                                                                ? 'UMUM'
                                                                : ($RJp->klaim_id == 'JM'
                                                                    ? 'BPJS'
                                                                    : ($RJp->klaim_id == 'KR'
                                                                        ? 'Kronis'
                                                                        : 'Asuransi Lain')) }}
                                                        </div>
                                                        <div class="font-normal text-gray-900">
                                                            {{ 'Nomer Pelayanan ' . $RJp->no_antrian }}
                                                        </div>
                                                    </div>

                                                </td>

                                                <td
                                                    class="px-4 py-3 overflow-auto break-all group-hover:bg-gray-100 dark:text-white w-52">

                                                    <div class="mb-2 font-semibold text-primary">
                                                        {{ $RJp->vno_sep }}
                                                    </div>
                                                    <div>
                                                        {{ '' . $RJp->push_antrian_bpjs_status . $RJp->push_antrian_bpjs_json }}
                                                    </div>

                                                </td>

                                                <td
                                                    class="px-4 py-3 group-hover:bg-gray-100 whitespace-nowrap dark:text-white">
                                                    <div class="overflow-auto ">
                                                        <div class="font-semibold text-primary">
                                                            {{ $RJp->waktu_masuk_poli == null && $RJp->waktu_masuk_apt == null
                                                                ? 'Pendaftaran'
                                                                : ($RJp->waktu_masuk_poli != null && $RJp->waktu_masuk_apt == null
                                                                    ? 'Pelayanan ' . $RJp->poli_desc
                                                                    : ($RJp->waktu_masuk_poli != null && $RJp->waktu_masuk_apt != null
                                                                        ? 'Menunggu Resep'
                                                                        : '--')) }}
                                                        </div>

                                                        @if ($RJp->datadaftarpolirj_json)
                                                            <div class="font-normal text-gray-900">
                                                                {{ '' . $RJp->nobooking }}
                                                            </div>
                                                            <div class="italic font-normal text-gray-900">
                                                                {{ 'TaskId1 ' . json_decode($RJp->datadaftarpolirj_json)->taskIdPelayanan->taskId1 }}
                                                            </div>
                                                            <div class="italic font-normal text-gray-900">
                                                                {{ 'TaskId2 ' . json_decode($RJp->datadaftarpolirj_json)->taskIdPelayanan->taskId2 }}
                                                            </div>
                                                            <div class="italic font-normal text-gray-900">
                                                                {{ 'TaskId3 ' . json_decode($RJp->datadaftarpolirj_json)->taskIdPelayanan->taskId3 }}
                                                            </div>
                                                            <div class="italic font-normal text-gray-900">
                                                                {{ 'TaskId4 ' . json_decode($RJp->datadaftarpolirj_json)->taskIdPelayanan->taskId4 }}
                                                            </div>
                                                            <div class="italic font-normal text-gray-900">
                                                                {{ 'TaskId5 ' . json_decode($RJp->datadaftarpolirj_json)->taskIdPelayanan->taskId5 }}
                                                            </div>
                                                            <div class="italic font-normal text-gray-900">
                                                                {{ 'TaskId6 ' . json_decode($RJp->datadaftarpolirj_json)->taskIdPelayanan->taskId6 }}
                                                            </div>
                                                            <div class="italic font-normal text-gray-900">
                                                                {{ 'TaskId7 ' . json_decode($RJp->datadaftarpolirj_json)->taskIdPelayanan->taskId7 }}
                                                            </div>
                                                            <div class="italic font-normal text-gray-900">
                                                                {{ 'TaskId99 ' . json_decode($RJp->datadaftarpolirj_json)->taskIdPelayanan->taskId99 }}
                                                            </div>
                                                        @endif

                                                    </div>
                                                </td>

                                                <td class="px-4 py-3 group-hover:bg-gray-100 group-hover:text-primary">

                                                    <div class="grid grid-cols-1">

                                                        <!-- Dropdown Action menu Flowbite-->
                                                        <div class="grid grid-cols-3">

                                                            <x-light-button
                                                                wire:click="masukAdmisi('{{ addslashes($RJp->rj_no) }}')"
                                                                wire:loading.remove>
                                                                1.Masuk Admisi
                                                            </x-light-button>
                                                            <div wire:loading wire:target="masukAdmisi">
                                                                <x-loading />
                                                            </div>

                                                            <x-light-button
                                                                wire:click="keluarAdmisi('{{ addslashes($RJp->rj_no) }}')"
                                                                wire:loading.remove>
                                                                2.Keluar Admisi
                                                            </x-light-button>
                                                            <div wire:loading wire:target="keluarAdmisi">
                                                                <x-loading />
                                                            </div>

                                                            <x-light-button
                                                                wire:click="daftarPoli('{{ addslashes($RJp->rj_no) }}')"
                                                                wire:loading.remove>
                                                                3.Daftar Poli
                                                            </x-light-button>
                                                            <div wire:loading wire:target="daftarPoli">
                                                                <x-loading />
                                                            </div>

                                                        </div>





                                                        <div class="grid grid-cols-2">

                                                            <x-light-button
                                                                wire:click="masukPoli('{{ addslashes($RJp->rj_no) }}')"
                                                                wire:loading.remove>
                                                                4.Masuk Poli
                                                            </x-light-button>
                                                            <div wire:loading wire:target="masukPoli">
                                                                <x-loading />
                                                            </div>

                                                            <x-light-button
                                                                wire:click="keluarPoli('{{ addslashes($RJp->rj_no) }}')"
                                                                wire:loading.remove>
                                                                5.Keluar Poli
                                                            </x-light-button>
                                                            <div wire:loading wire:target="keluarPoli">
                                                                <x-loading />
                                                            </div>

                                                            {{-- delete Modal --}}
                                                            @include('livewire.pelayanan-r-j.delete-confirmation')



                                                        </div>

                                                        <div class="grid grid-cols-2">

                                                            <x-light-button
                                                                wire:click="masukApotek('{{ addslashes($RJp->rj_no) }}')"
                                                                wire:loading.remove>
                                                                6.Masuk Apotek
                                                            </x-light-button>
                                                            <div wire:loading wire:target="masukApotek">
                                                                <x-loading />
                                                            </div>

                                                            <x-light-button
                                                                wire:click="keluarApotek('{{ addslashes($RJp->rj_no) }}')"
                                                                wire:loading.remove>
                                                                7.Keluar Apotek
                                                            </x-light-button>
                                                            <div wire:loading wire:target="keluarApotek">
                                                                <x-loading />
                                                            </div>

                                                            <x-primary-button
                                                                wire:click="getListTaskId('{{ addslashes($RJp->nobooking) }}')"
                                                                wire:loading.remove>
                                                                Get List Task Id BPJS
                                                            </x-primary-button>
                                                            <div wire:loading wire:target="getListTaskId">
                                                                <x-loading />
                                                            </div>

                                                        </div>

                                                        <!-- End Dropdown Action Open menu -->
                                                    </div>

                                                </td>
                                            </tr>
                                        @endforeach



                                    </tbody>
                                </table>





                                {{-- no data found start --}}
                                @if ($RJpasiens->count() == 0)
                                    <div class="w-full p-4 text-sm text-center text-gray-900 dark:text-gray-400">
                                        {{ 'Data ' . $myProgram . ' Tidak ditemukan' }}
                                    </div>
                                @endif
                                {{-- no data found end --}}



                            </div>
                        </div>
                    </div>
                </div>



                <!-- Pagination start -->
                <div class="flex items-center justify-end pt-3 sm:pt-6">
                    {{-- {{ $RJpasiens->links() }} --}}
                    {{ $RJpasiens->links('vendor.livewire.tailwind') }}
                </div>
                <!-- Pagination end -->



            </div>
        </div>
    </div>


    <div class="mx-4 my-2 md:flex md:justify-between">
        <div>

        </div>

        <div class="flex justify-between mt-2 md:mt-0">
            <x-primary-button wire:click="getDashboardWaktuTungguPerbulanBPJS()" class="flex justify-center flex-auto"
                wire:loading.remove>
                <svg class="w-5 h-5 mr-2 -ml-1" fill="currentColor" viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                        d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                        clip-rule="evenodd"></path>
                </svg>
                Dashboard Per Bulan BPJS
            </x-primary-button>

            <div wire:loading wire:target="getDashboardWaktuTungguPerbulanBPJS">
                <x-loading />
            </div>
        </div>
    </div>

    <div class="mb-5">
        <h3 class="text-3xl font-bold text-gray-900 dark:text-white">{{ 'Rekap Waktu Tunggu Bulanan' }}</h3>
    </div>
    <div class="flex flex-col mt-2">
        <div class="overflow-x-auto rounded-lg">
            <div class="inline-block min-w-full align-middle">
                <div class="overflow-hidden shadow sm:rounded-lg">
                    <table class="w-full text-sm text-left text-gray-900 table-auto dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-4 py-3">Kode Poli</th>
                                <th scope="col" class="px-4 py-3">Nama Poli</th>
                                <th scope="col" class="px-4 py-3">Bulan</th>
                                <th scope="col" class="px-4 py-3 text-center">Jumlah Antrean</th>
                                <th scope="col" class="px-4 py-3 text-center">Rata-Rata Waktu (Menit)</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800">
                            @if (isset($rekapDashboardWaktutungguBulanan) && !empty($rekapDashboardWaktutungguBulanan))
                                @foreach ($rekapDashboardWaktutungguBulanan as $data)
                                    <tr class="border-b dark:border-gray-700">
                                        <td class="px-4 py-3">{{ $data['kodePoli'] ?? '-' }}</td>
                                        <td class="px-4 py-3">{{ $data['namaPoli'] ?? '-' }}</td>
                                        <td class="px-4 py-3">{{ $data['bulan'] ?? '-' }}</td>
                                        <td class="px-4 py-3 text-center">{{ $data['total_antrean'] ?? 0 }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <div class="flex flex-col items-start">
                                                <span><strong>Waktu Tunggu Admisi:</strong>
                                                    {{ $data['rata_rata_waktu']['waktuTungguAdmisi'] ?? 0 }}
                                                    menit</span>
                                                <span><strong>Waktu Layan Admisi:</strong>
                                                    {{ $data['rata_rata_waktu']['waktuLayanAdmisi'] ?? 0 }}
                                                    menit</span>
                                                <span><strong>Waktu Tunggu Poli:</strong>
                                                    {{ $data['rata_rata_waktu']['waktuTungguPoli'] ?? 0 }}
                                                    menit</span>
                                                <span><strong>Waktu Layan Poli:</strong>
                                                    {{ $data['rata_rata_waktu']['waktuLayanPoli'] ?? 0 }}
                                                    menit</span>
                                                <span><strong>Waktu Tunggu Farmasi:</strong>
                                                    {{ $data['rata_rata_waktu']['waktuTungguFarmasi'] ?? 0 }}
                                                    menit</span>
                                                <span><strong>Waktu Layan Farmasi:</strong>
                                                    {{ $data['rata_rata_waktu']['waktuLayanFarmasi'] ?? 0 }}
                                                    menit</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5"
                                        class="w-full p-4 text-sm text-center text-gray-900 dark:text-gray-400">
                                        Data tidak ditemukan.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-5">
        <h3 class="text-3xl font-bold text-gray-900 dark:text-white">{{ 'Waktu Tunggu Harian' }}</h3>
    </div>

    <div class="flex flex-col mt-2">
        <div class="overflow-x-auto rounded-lg">
            <div class="inline-block min-w-full align-middle">
                <div class="overflow-hidden shadow sm:rounded-lg">
                    <table class="w-full text-sm text-left text-gray-900 table-auto dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-4 py-3">Nama PPK</th>
                                <th scope="col" class="px-4 py-3">Tanggal</th>
                                <th scope="col" class="px-4 py-3">Nama Poli</th>
                                <th scope="col" class="px-4 py-3 text-center">Jumlah Antrean</th>
                                <th scope="col" class="px-4 py-3 text-center">Waktu Task & AVG Waktu (Menit)</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800">
                            @if (isset($dashboardWaktutungguBulanan['response']['list']) && !empty($dashboardWaktutungguBulanan['response']['list']))
                                @foreach ($dashboardWaktutungguBulanan['response']['list'] as $data)
                                    <tr class="border-b dark:border-gray-700">
                                        <td class="px-4 py-3">{{ $data['nmppk'] ?? '-' }}</td>
                                        <td class="px-4 py-3">
                                            {{ isset($data['tanggal']) ? \Carbon\Carbon::parse($data['tanggal'])->format('d M Y') : '-' }}
                                        </td>
                                        <td class="px-4 py-3">{{ $data['namapoli'] ?? '-' }}</td>
                                        <td class="px-4 py-3 text-center">{{ $data['jumlah_antrean'] ?? 0 }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <div class="flex flex-col items-start">
                                                <span><strong>Waktu Tunggu Admisi:</strong>
                                                    {{ round(($data['waktu_task1'] ?? 0) / 60, 2) }} menit (AVG:
                                                    {{ round(($data['avg_waktu_task1'] ?? 0) / 60, 2) }})</span>
                                                <span><strong>Waktu Layan Admisi:</strong>
                                                    {{ round(($data['waktu_task2'] ?? 0) / 60, 2) }} menit (AVG:
                                                    {{ round(($data['avg_waktu_task2'] ?? 0) / 60, 2) }})</span>
                                                <span><strong>Waktu Tunggu Poli:</strong>
                                                    {{ round(($data['waktu_task3'] ?? 0) / 60, 2) }} menit (AVG:
                                                    {{ round(($data['avg_waktu_task3'] ?? 0) / 60, 2) }})</span>
                                                <span><strong>Waktu Layan Poli:</strong>
                                                    {{ round(($data['waktu_task4'] ?? 0) / 60, 2) }} menit (AVG:
                                                    {{ round(($data['avg_waktu_task4'] ?? 0) / 60, 2) }})</span>
                                                <span><strong>Waktu Tunggu Farmasi:</strong>
                                                    {{ round(($data['waktu_task5'] ?? 0) / 60, 2) }} menit (AVG:
                                                    {{ round(($data['avg_waktu_task5'] ?? 0) / 60, 2) }})</span>
                                                <span><strong>Waktu Layan Farmasi:</strong>
                                                    {{ round(($data['waktu_task6'] ?? 0) / 60, 2) }} menit (AVG:
                                                    {{ round(($data['avg_waktu_task6'] ?? 0) / 60, 2) }})</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="8"
                                        class="w-full p-4 text-sm text-center text-gray-900 dark:text-gray-400">
                                        Data tidak ditemukan.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>





















    {{-- push start ///////////////////////////////// --}}
    @push('scripts')
        {{-- script start --}}
        <script src="{{ url('assets/js/jquery.min.js') }}"></script>
        <script src="{{ url('assets/plugins/toastr/toastr.min.js') }}"></script>
        <script src="{{ url('assets/flowbite/dist/datepicker.js') }}"></script>

        {{-- script end --}}



        {{-- Global Livewire JavaScript Object start --}}
        <script type="text/javascript">
            window.livewire.on('toastr-success', message => toastr.success(message));
            window.Livewire.on('toastr-info', (message) => {
                toastr.info(message)
            });
            window.livewire.on('toastr-error', message => toastr.error(message));


            // press_dropdownButton flowbite
            window.Livewire.on('pressDropdownButton', (key) => {
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

        {{-- Global Livewire JavaScript Object end --}}
    @endpush













    @push('styles')
        {{-- stylesheet start --}}
        <link rel="stylesheet" href="{{ url('assets/plugins/toastr/toastr.min.css') }}">

        {{-- stylesheet end --}}
    @endpush
    {{-- push end --}}
</div>
