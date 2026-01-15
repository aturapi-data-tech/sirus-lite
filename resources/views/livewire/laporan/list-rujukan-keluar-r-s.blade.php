<div>
    {{-- ================= CANVAS ================= --}}
    <div class="w-full h-[calc(100vh-68px)] bg-white border border-gray-200 px-4 pt-6">

        {{-- ================= TITLE ================= --}}
        <div class="mb-4">
            <h3 class="text-3xl font-bold text-gray-900">{{ $myTitle }}</h3>
            <span class="text-sm text-gray-600">{{ $mySnipt }}</span>
        </div>

        {{-- ================= FILTER BAR ================= --}}
        <div class="grid items-end grid-cols-1 gap-3 mb-4 md:grid-cols-5">

            {{-- Tanggal Mulai --}}
            <div>
                <x-input-label value="Tanggal Mulai" />
                <x-text-input type="text" class="w-full" placeholder="DD-MM-YYYY" wire:model.defer="tglMulai" />
            </div>

            {{-- Tanggal Akhir --}}
            <div>
                <x-input-label value="Tanggal Akhir" />
                <x-text-input type="text" class="w-full" placeholder="DD-MM-YYYY" wire:model.defer="tglAkhir" />
            </div>

            {{-- Tombol Cari --}}
            <div class="relative flex justify-center flex-auto md:col-span-1">
                <x-primary-button wire:click="fetchDataRujukanKeluarRS" wire:loading.attr="disabled"
                    wire:target="fetchDataRujukanKeluarRS" class="flex items-center justify-center w-full">
                    <svg class="w-5 h-5 mr-2 -ml-1" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                            clip-rule="evenodd">
                        </path>
                    </svg>

                    <span wire:loading.remove wire:target="fetchDataRujukanKeluarRS">
                        Cari Data Rujukan Keluar RS
                    </span>

                    <span wire:loading wire:target="fetchDataRujukanKeluarRS">
                        Memproses...
                    </span>
                </x-primary-button>

                {{-- Overlay loading --}}
                <div wire:loading wire:target="fetchDataRujukanKeluarRS"
                    class="absolute inset-0 z-10 flex items-center justify-center rounded-md bg-white/70">
                    <x-loading />
                    <span class="ml-2 text-sm text-gray-600">
                        Mengambil data BPJS...
                    </span>
                </div>
            </div>
        </div>

        {{-- ================= ERROR MESSAGE ================= --}}
        @if ($errorMessage)
            <div class="p-3 mb-3 text-red-700 bg-red-100 rounded">
                {{ $errorMessage }}
            </div>
        @endif

        {{-- ================= REKAP (RS / POLI / DIAGNOSA) ================= --}}
        @if (!empty($detailRows))
            <div class="grid grid-cols-1 gap-4 mb-4 lg:grid-cols-3">

                {{-- Rekap RS --}}
                <div class="bg-white border rounded-lg">
                    <div class="flex items-center justify-between px-4 py-2 font-semibold border-b">
                        <div>
                            Rekap RS Tujuan
                            <span class="ml-2 text-sm font-normal text-gray-500">
                                (Detail: {{ count($detailRows) }})
                            </span>
                        </div>
                    </div>
                    <div class="max-h-[220px] overflow-auto">
                        <table class="w-full text-sm border-collapse">
                            <thead class="sticky top-0 bg-gray-100 border-b">
                                <tr>
                                    <th class="w-12 px-3 py-2 text-left border">No</th>
                                    <th class="px-3 py-2 text-left border">RS Tujuan</th>
                                    <th class="w-24 px-3 py-2 text-right border">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($rekapByRs as $i => $r)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2 border">{{ $i + 1 }}</td>
                                        <td class="px-3 py-2 font-medium border">{{ $r['rsNama'] ?? '-' }}</td>
                                        <td class="px-3 py-2 font-semibold text-right border">{{ $r['jumlah'] ?? 0 }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-3 py-4 text-center text-gray-500">Belum ada rekap
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Rekap Poli --}}
                <div class="bg-white border rounded-lg">
                    <div class="flex items-center justify-between px-4 py-2 font-semibold border-b">
                        <div>Rekap Poli</div>
                        <div class="text-sm font-normal text-gray-500">{{ count($rekapByPoli) }} poli</div>
                    </div>
                    <div class="max-h-[220px] overflow-auto">
                        <table class="w-full text-sm border-collapse">
                            <thead class="sticky top-0 bg-gray-100 border-b">
                                <tr>
                                    <th class="w-12 px-3 py-2 text-left border">No</th>
                                    <th class="px-3 py-2 text-left border">Poli</th>
                                    <th class="w-24 px-3 py-2 text-right border">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($rekapByPoli as $i => $p)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2 border">{{ $i + 1 }}</td>
                                        <td class="px-3 py-2 font-medium border">{{ $p['poliNama'] ?? '-' }}</td>
                                        <td class="px-3 py-2 font-semibold text-right border">{{ $p['jumlah'] ?? 0 }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-3 py-4 text-center text-gray-500">Belum ada rekap
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Rekap Diagnosa --}}
                <div class="bg-white border rounded-lg">
                    <div class="flex items-center justify-between px-4 py-2 font-semibold border-b">
                        <div>Rekap Diagnosa</div>
                        <div class="text-sm font-normal text-gray-500">{{ count($rekapByDiagnosa) }} diagnosa</div>
                    </div>
                    <div class="max-h-[220px] overflow-auto">
                        <table class="w-full text-sm border-collapse">
                            <thead class="sticky top-0 bg-gray-100 border-b">
                                <tr>
                                    <th class="w-12 px-3 py-2 text-left border">No</th>
                                    <th class="px-3 py-2 text-left border">Diagnosa</th>
                                    <th class="w-24 px-3 py-2 text-right border">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($rekapByDiagnosa as $i => $d)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2 border">{{ $i + 1 }}</td>
                                        <td class="px-3 py-2 font-medium border">{{ $d['diagNama'] ?? '-' }}</td>
                                        <td class="px-3 py-2 font-semibold text-right border">{{ $d['jumlah'] ?? 0 }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-3 py-4 text-center text-gray-500">Belum ada rekap
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        @endif

        {{-- ================= DETAIL RUJUKAN TERPILIH ================= --}}
        @if (!empty($detailRujukan))
            <div class="p-4 mb-4 bg-white border rounded-lg">
                <div class="flex items-center justify-between mb-2">
                    <div class="font-semibold">Detail Rujukan Terpilih</div>
                    <div class="text-xs text-gray-500">No: {{ $detailRujukan['noRujukan'] ?? '-' }}</div>
                </div>

                <div class="grid grid-cols-1 gap-2 text-sm md:grid-cols-2">
                    <div>No Rujukan: <span class="font-medium">{{ $detailRujukan['noRujukan'] ?? '-' }}</span></div>
                    <div>No SEP: <span class="font-medium">{{ $detailRujukan['noSep'] ?? '-' }}</span></div>

                    <div>No Kartu: <span class="font-medium">{{ $detailRujukan['noKartu'] ?? '-' }}</span></div>
                    <div>Nama: <span class="font-medium">{{ $detailRujukan['nama'] ?? '-' }}</span></div>

                    <div>RS Tujuan: <span class="font-medium">{{ $detailRujukan['namaPpkDirujuk'] ?? '-' }}</span>
                    </div>
                    <div>Poli: <span class="font-medium">{{ $detailRujukan['namaPoliRujukan'] ?? '-' }}</span></div>

                    <div>Tgl Rujukan: <span class="font-medium">{{ $detailRujukan['tglRujukanDisplay'] ?? '-' }}</span>
                    </div>
                    <div>
                        Diagnosa:
                        <span class="font-medium">
                            {{ $detailRujukan['diagRujukan'] ?? '-' }} -
                            {{ $detailRujukan['namaDiagRujukan'] ?? '-' }}
                        </span>
                    </div>
                </div>
            </div>
        @endif

        {{-- ================= LIST TABLE (SCROLL) ================= --}}
        <div class="h-[calc(100vh-420px)] overflow-auto border rounded">
            <table class="w-full text-sm border-collapse">
                <thead class="sticky top-0 z-10 bg-gray-100 border-b">
                    <tr>
                        <th class="px-3 py-2 text-left border w-14">No</th>
                        <th class="px-3 py-2 text-left border">No SEP</th>
                        <th class="px-3 py-2 text-left border">No Kartu</th>
                        <th class="px-3 py-2 text-left border">Nama Peserta</th>
                        <th class="px-3 py-2 text-left border">RS Tujuan</th>
                        <th class="w-32 px-3 py-2 text-left border">Tgl Rujukan</th>
                        <th class="px-3 py-2 text-center border w-28">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($queryData as $index => $row)
                        @php
                            $noRujukan = $row['noRujukan'] ?? '';
                        @endphp

                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 border">{{ $index + 1 }}</td>

                            <td class="px-3 py-2 font-mono text-xs border">
                                {{ $row['noSep'] ?? '-' }}
                            </td>

                            <td class="px-3 py-2 font-mono text-xs border">
                                {{ $row['noKartu'] ?? '-' }}
                            </td>

                            <td class="px-3 py-2 font-semibold border">
                                {{ $row['nama'] ?? '-' }}
                            </td>

                            {{-- Pakai field hasil proses: rsTujuanNama --}}
                            <td class="px-3 py-2 border">
                                {{ $row['rsTujuanNama'] ?? ($row['namaPpkDirujuk'] ?? '-') }}
                            </td>

                            {{-- Pakai field hasil proses: tglRujukanDisplay --}}
                            <td class="px-3 py-2 border">
                                {{ $row['tglRujukanDisplay'] ?? ($row['tglRujukan'] ?? '-') }}
                            </td>

                            <td class="px-3 py-2 text-center border">
                                <x-primary-button wire:click="pilihRujukan('{{ $noRujukan }}')"
                                    wire:loading.attr="disabled" wire:target="pilihRujukan('{{ $noRujukan }}')"
                                    class="px-3 py-1 text-xs">
                                    <span wire:loading.remove wire:target="pilihRujukan('{{ $noRujukan }}')">
                                        Pilih
                                    </span>
                                    <span wire:loading wire:target="pilihRujukan('{{ $noRujukan }}')">
                                        Memproses...
                                    </span>
                                </x-primary-button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-gray-500">
                                Belum ada data rujukan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
    {{-- ================= END CANVAS ================= --}}
</div>
