<div>
    {{-- ================= CANVAS ================= --}}
    <div class="w-full h-[calc(100vh-68px)] bg-white border border-gray-200 px-4 pt-6">

        {{-- ================= TITLE ================= --}}
        <div class="mb-4">
            <h3 class="text-3xl font-bold text-gray-900">
                {{ $myTitle }}
            </h3>
            <span class="text-sm text-gray-600">
                {{ $mySnipt }}
            </span>
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
            <div class="relative flex justify-center flex-auto">
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

                <!-- Overlay loading: sibling, bukan di dalam button -->
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

        {{-- ================= TABLE ================= --}}
        <div class="h-[calc(100vh-260px)] overflow-auto border rounded">

            @if (!empty($rekapByRs))
                <div class="mb-4 bg-white border rounded-lg">
                    <div class="px-4 py-2 font-semibold border-b">
                        Rekap Rujukan per RS Tujuan
                        <span class="ml-2 text-sm text-gray-500">(Total: {{ $totalRujukan }})</span>
                    </div>

                    <table class="w-full text-sm border-collapse">
                        <thead class="bg-gray-100 border-b">
                            <tr>
                                <th class="w-12 px-3 py-2 text-left border">No</th>
                                <th class="px-3 py-2 text-left border">RS Tujuan</th>
                                <th class="px-3 py-2 text-right border w-28">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rekapByRs as $i => $r)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-2 border">{{ $i + 1 }}</td>
                                    <td class="px-3 py-2 font-medium border">{{ $r['rsNama'] }}</td>
                                    <td class="px-3 py-2 font-semibold text-right border">{{ $r['jumlah'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @if (!empty($detailRujukan))
                <div class="p-4 mt-4 bg-white border rounded-lg">
                    <div class="mb-2 font-semibold">Detail Rujukan Terpilih</div>

                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <div>No Rujukan: <span class="font-medium">{{ $detailRujukan['noRujukan'] }}</span></div>
                        <div>No SEP: <span class="font-medium">{{ $detailRujukan['noSep'] }}</span></div>

                        <div>No Kartu: {{ $detailRujukan['noKartu'] }}</div>
                        <div>Nama: {{ $detailRujukan['nama'] }}</div>

                        <div>RS Tujuan: {{ $detailRujukan['namaPpkDirujuk'] }}</div>
                        <div>Poli: {{ $detailRujukan['namaPoliRujukan'] }}</div>

                        <div>Tgl Rujukan: {{ $detailRujukan['tglRujukanDisplay'] }}</div>
                        <div>Diagnosa: {{ $detailRujukan['diagRujukan'] }} - {{ $detailRujukan['namaDiagRujukan'] }}
                        </div>
                    </div>
                </div>
            @endif


            <table class="w-full text-sm border-collapse">
                <thead class="sticky top-0 z-10 bg-gray-100 border-b">
                    <tr>
                        <th class="px-3 py-2 text-left border">No</th>
                        <th class="px-3 py-2 text-left border">No SEP</th>
                        <th class="px-3 py-2 text-left border">No Kartu</th>
                        <th class="px-3 py-2 text-left border">Nama Peserta</th>
                        <th class="px-3 py-2 text-left border">RS Tujuan</th>
                        <th class="px-3 py-2 text-left border">Tgl Rujukan</th>
                        <th class="px-3 py-2 text-center border">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($queryData as $index => $row)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 border">
                                {{ $index + 1 }}
                            </td>

                            <td class="px-3 py-2 font-mono text-xs border">
                                {{ $row['noSep'] ?? '-' }}
                            </td>

                            <td class="px-3 py-2 font-mono text-xs border">
                                {{ $row['noKartu'] ?? '-' }}
                            </td>

                            <td class="px-3 py-2 font-semibold border">
                                {{ $row['nama'] ?? '-' }}
                            </td>

                            <td class="px-3 py-2 border">
                                {{ $row['namaPpkDirujuk'] ?? '-' }}
                            </td>

                            <td class="px-3 py-2 border">
                                {{ $row['tglRujukan'] ?? '-' }}
                            </td>

                            <td class="px-3 py-2 text-center border">
                                <x-primary-button wire:click="pilihRujukan('{{ $row['noRujukan'] ?? '' }}')"
                                    wire:loading.attr="disabled"
                                    wire:target="pilihRujukan('{{ $row['noRujukan'] ?? '' }}')"
                                    class="px-3 py-1 text-xs">
                                    <span wire:loading.remove
                                        wire:target="pilihRujukan('{{ $row['noRujukan'] ?? '' }}')">
                                        Pilih
                                    </span>

                                    <span wire:loading wire:target="pilihRujukan('{{ $row['noRujukan'] ?? '' }}')">
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
