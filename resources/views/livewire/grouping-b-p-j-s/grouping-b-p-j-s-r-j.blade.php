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

            <div class="flex items-start w-full">
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

                    <x-text-input type="text" class="p-2 pl-10 " placeholder="[mm/yyyy]"
                        wire:model="myTopBar.refDate" />
                </div>

                <form wire:submit.prevent="readPdfUmbalBPJS()" enctype="multipart/form-data"
                    class="grid grid-cols-2 gap-2">

                    <div>
                        {{-- Input PDF --}}
                        <input type="file" wire:model="file" accept="application/pdf"
                            class="block w-full text-sm text-gray-500">

                        {{-- Pesan error validasi --}}
                        @error('file')
                            <span class="col-span-2 text-sm text-red-600">{{ $message }}</span>
                        @enderror

                    </div>

                    <div>

                        {{-- Tombol Proses --}}
                        <x-green-button type="submit" wire:loading.attr="disabled" wire:loading.remove
                            wire:target="file,readPdfUmbalBPJS">
                            Proses PDF
                        </x-green-button>
                        {{-- Spinner saat proses --}}
                        <div wire:loading wire:target="readPdfUmbalBPJS" class="col-span-2">
                            <x-loading />
                        </div>
                    </div>

                </form>

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

        {{-- Ringkasan biaya singkat --}}
        <div class="text-[13px] leading-5 bg-gray-50 rounded p-3 mb-3">

            {{-- <span class="font-semibold">Admin RJ:</span>
            {{ number_format($myQueryDataSum['admin_rawat_jalan'], 0, ',', '.') }} &nbsp;|&nbsp;

            <span class="font-semibold">Admin UP:</span>
            {{ number_format($myQueryDataSum['admin_up'], 0, ',', '.') }} &nbsp;|&nbsp;

            <span class="font-semibold">Jasa Dokter:</span>
            {{ number_format($myQueryDataSum['jasa_dokter'], 0, ',', '.') }} &nbsp;|&nbsp;

            <span class="font-semibold">Jasa Medis:</span>
            {{ number_format($myQueryDataSum['jasa_medis'], 0, ',', '.') }} &nbsp;|&nbsp;

            <span class="font-semibold">Jasa Karyawan:</span>
            {{ number_format($myQueryDataSum['jasa_karyawan'], 0, ',', '.') }} &nbsp;|&nbsp;

            <span class="font-semibold">Obat:</span>
            {{ number_format($myQueryDataSum['obat'], 0, ',', '.') }} &nbsp;|&nbsp;

            <span class="font-semibold">Radiologi:</span>
            {{ number_format($myQueryDataSum['radiologi'], 0, ',', '.') }} &nbsp;|&nbsp;

            <span class="font-semibold">Laborat:</span>
            {{ number_format($myQueryDataSum['laboratorium'], 0, ',', '.') }} &nbsp;|&nbsp;

            <span class="font-semibold">Lain-lain:</span>
            {{ number_format($myQueryDataSum['lain_lain'], 0, ',', '.') }} &nbsp;|&nbsp;

            <br> --}}
            <span class="font-bold text-primary">Jml Klaim:</span>
            <span class="font-bold text-primary">{{ number_format($myQueryDataSum['jml_all'], 0, ',', '.') }}
            </span>
            &nbsp;|&nbsp;
            <span class="font-bold text-primary">TOTAL:</span>
            <span class="font-bold text-primary">{{ number_format($myQueryDataSum['total_all'], 0, ',', '.') }}
            </span>

            &nbsp;|&nbsp;
            <span class="font-bold text-primary">Jml Klaim BPJS:</span>
            <span class="font-bold text-primary">{{ number_format($myQueryDataSum['jml_disetujui_bpjs'], 0, ',', '.') }}
            </span>
            &nbsp;|&nbsp;
            <span class="font-bold text-primary">Disetujui BPJS:</span>
            <span class="font-bold text-primary">{{ number_format($myQueryDataSum['disetujui_bpjs'], 0, ',', '.') }}
            </span>
        </div>

        {{-- Top Bar --}}






        <div class="h-[calc(100vh-250px)] mt-2 overflow-auto">
            <!-- Table -->
            <table class="w-full text-sm text-left text-gray-700 table-auto">
                <thead class="sticky top-0 text-xs text-gray-900 uppercase bg-gray-100 ">
                    <tr>
                        <th scope="col" class="w-[5%] px-4 py-3">No</th>
                        <th scope="col" class="w-[25%] px-4 py-3">No SEP / Pasien RS&Klaim</th>
                        <th scope="col" class="w-[20%] px-4 py-3">Tgl Rajal</th>
                        <th scope="col" class="w-[10%] px-4 py-3">Riil RS</th>
                        <th scope="col" class="w-[10%] px-4 py-3">Tgl Verifikasi</th>
                        <th scope="col" class="w-[10%] px-4 py-3">Riil RS Inacbg</th>
                        <th scope="col" class="w-[10%] px-4 py-3">Diajukan</th>
                        <th scope="col" class="w-[10%] px-4 py-3">Disetujui</th>
                    </tr>
                </thead>

                <tbody class="bg-white">
                    @foreach ($myQueryData as $index => $myQData)
                        @php
                            $datadaftar_json = json_decode($myQData->datadaftarpolirj_json, true);
                            $tarif_rs = [
                                'admin_up' => $myQData->admin_up,
                                'jasa_karyawan' => $myQData->jasa_karyawan,
                                'jasa_dokter' => $myQData->jasa_dokter,
                                'jasa_medis' => $myQData->jasa_medis,
                                'admin_rawat_jalan' => $myQData->admin_rawat_jalan,
                                'lain_lain' => $myQData->lain_lain,
                                'radiologi' => $myQData->radiologi,
                                'laboratorium' => $myQData->laboratorium,
                                'obat' => $myQData->obat,
                            ];
                            $total_all = array_sum($tarif_rs);

                            $umbal = $datadaftar_json['umbalBpjs'] ?? [];
                        @endphp
                        <tr class="border-b">
                            <td class="px-4 py-3">{{ $index + 1 }}</td>

                            <td class="px-4 py-3">
                                {{-- No SEP --}}
                                <div class="font-semibold text-primary">
                                    {{ $myQData->vno_sep ?? '—' }}
                                </div>

                                {{-- No. RM & Nama Pasien --}}
                                <div class="text-gray-800">
                                    {{ $myQData->reg_no }} | {{ $myQData->reg_name }}
                                </div>

                                {{-- Dokter --}}
                                <div class="text-sm text-gray-600">
                                    Dokter: {{ $myQData->dr_name ?? '-' }}
                                </div>

                                {{-- Poli --}}
                                <div class="text-sm text-gray-600">
                                    Poli: {{ $myQData->poli_desc ?? '-' }}
                                </div>

                                {{-- Klaim BPJS --}}
                                <x-badge :badgecolor="'green'" class="mt-1">
                                    BPJS
                                </x-badge>
                            </td>

                            <td class="px-4 py-3">{{ $myQData->rj_date }}</td>
                            <!-- Riil RS -->
                            <td class="px-4 py-3 text-right"> {{ number_format($total_all, 0, ',', '.') }}</td>
                            {{-- Persentase Selisih --}}
                            @php
                                $riil_rs = (int) ($umbal['riil_rs'] ?? 0);
                                $disetujui = (int) ($umbal['disetujui'] ?? 0);
                                $selisih = $disetujui - $riil_rs;
                                $warna = $selisih < 0 ? 'text-red-600' : 'text-green-600';
                            @endphp

                            {{-- Tgl Verifikasi --}}
                            <td class="px-4 py-3 @if (empty($umbal['tgl_verif'])) bg-red-50 @endif">
                                {{ $umbal['tgl_verif'] ?? '--' }}
                            </td>

                            {{-- Riil RS Inacbg --}}
                            <td class="px-4 py-3 text-right @if (empty($umbal['riil_rs'])) bg-red-50 @endif">
                                {{ number_format((int) ($umbal['riil_rs'] ?? 0), 0, ',', '.') }}
                            </td>

                            {{-- Diajukan --}}
                            <td class="px-4 py-3 text-right @if (empty($umbal['diajukan'])) bg-red-50 @endif">
                                {{ number_format((int) ($umbal['diajukan'] ?? 0), 0, ',', '.') }}
                            </td>

                            {{-- Disetujui --}}
                            <td class="px-4 py-3 text-right @if (empty($umbal['disetujui'])) bg-red-50 @endif">
                                {{ number_format((int) ($umbal['disetujui'] ?? 0), 0, ',', '.') }}
                                <br>
                                <span class="text-xs font-semibold {{ $warna }}">
                                    {{ number_format((int) $selisih, 0, ',', '.') }}
                                </span>
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


        <div>
            @if (!empty($dataUmbalBPJSTidakAdaDiRS))
                <h2 class="mt-8 mb-2 text-lg font-semibold text-red-600">Data SEP Tidak Ditemukan di RS</h2>

                <table class="w-full text-sm text-left text-gray-700 border border-red-300 table-auto">
                    <thead class="text-red-800 uppercase bg-red-100">
                        <tr>
                            <th class="px-4 py-2 w-[5%]">No</th>
                            <th class="px-4 py-2 w-[25%]">No SEP</th>
                            <th class="px-4 py-2 w-[15%]">Tgl Verifikasi</th>
                            <th class="px-4 py-2 w-[15%] text-right">Riil RS</th>
                            <th class="px-4 py-2 w-[15%] text-right">Diajukan</th>
                            <th class="px-4 py-2 w-[15%] text-right">Disetujui</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @foreach ($dataUmbalBPJSTidakAdaDiRS as $index => $umbal)
                            <tr class="border-b">
                                <td class="px-4 py-2">{{ $index + 1 }}</td>
                                <td class="px-4 py-2">{{ $umbal['no_sep'] }}</td>
                                <td class="px-4 py-2">{{ $umbal['tgl_verif'] }}</td>
                                <td class="px-4 py-2 text-right">{{ number_format($umbal['riil_rs'], 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-2 text-right">{{ number_format($umbal['diajukan'], 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-2 text-right">{{ number_format($umbal['disetujui'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

        </div>



    </div>



    {{-- Canvas
    Main BgColor /
    Size H/W --}}

    {{-- End Coding --}}


</div>







{{-- <div>
    <input type="file" wire:model="file" accept=".xlsx">

    @if ($myQDatas)
        <table class="w-full mt-4 border-collapse table-auto">
            <thead>
                <tr>
                    @foreach ($myQDatas[0] as $colIndex => $col)
                        <th class="px-4 py-2 border">Kolom {{ $colIndex + 1 }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($myQDatas as $myQData)
                    <tr>
                        @foreach ($myQData as $cell)
                            <td class="px-4 py-2 border">{{ $cell }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div> --}}
