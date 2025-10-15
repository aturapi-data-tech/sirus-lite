<div>

    {{-- Start Coding  --}}

    {{-- Canvas
    Main BgColor /
    Size H/W --}}
    <div class="w-full h-[calc(100vh-68px)] bg-white border border-gray-200 px-4 pt-6" x-data="autoScroller({ step: 1, interval: 25, waitTop: 800, waitBottom: 1200 })"
        x-init="start()">

        {{-- Title  --}}
        <div class="mb-2">
            <h3 class="text-3xl font-bold text-gray-900 ">{{ $myTitle }}</h3>
            <span class="text-base font-normal text-gray-700">{{ $mySnipt }}</span>
        </div>
        <div class="w-full text-sm text-left text-gray-700">
            <style>
                @keyframes flash-pulse {

                    0%,
                    96%,
                    100% {
                        opacity: 1;
                    }

                    /* stabil */
                    97%,
                    99% {
                        opacity: 0.3;
                    }

                    /* kedip cepat (±0.1s dari 5s) */
                }

                .blink-soft {
                    animation: flash-pulse 5s linear infinite;
                }

                @media (prefers-reduced-motion: reduce) {
                    .blink-soft {
                        animation: none !important;
                    }
                }
            </style>

            <div class="p-2 mt-2 text-xs border rounded sm:text-sm bg-amber-50 border-amber-200 text-amber-900 sm:p-3 blink-soft"
                style="animation: flash-soft 10.2s linear infinite;">
                Kepada pasien yang memiliki resep mengandung <strong>obat racikan</strong>, proses peracikan
                memerlukan
                waktu tambahan sekitar
                <strong>±15–30 menit</strong> demi ketepatan dosis dan keselamatan. Terima kasih atas kesabaran dan
                pengertiannya.
                <strong>Kami akan menginformasikan saat obat siap diambil.</strong>
            </div>
        </div>

        @if ($myTopBar['autoRefresh'] == 'Ya')
            <div wire:poll.30s="render" class="h-[calc(100vh-250px)] mt-2 overflow-auto" x-ref="scroller"
                x-on:mouseenter="pause()" x-on:mouseleave="resume()">
            @else
                <div class="h-[calc(100vh-250px)] mt-2 overflow-auto" x-ref="scroller" x-on:mouseenter="pause()"
                    x-on:mouseleave="resume()">
        @endif

        <p class="sticky top-0 z-50 text-xs text-gray-700 bg-white rounded-lg">Data Terakhir:
            {{ now()->format('d-m-y H:i:s') }}</p>

        <div class="grid w-full grid-cols-2 gap-4 overflow-hidden">

            {{-- KIRI: ANTRI --}}
            <div class="overflow-y-auto max-h-[calc(100vh-220px)]">

                <div class="sticky top-0 z-20 bg-white border-b border-gray-200">
                    <h2 class="px-4 py-2 text-base font-semibold text-red-800">
                        🕐 Proses Resep
                    </h2>
                </div>

                <table class="min-w-full text-sm text-left text-gray-700 border-collapse table-fixed">
                    {{-- Kunci lebar kolom identik di 2 tabel --}}
                    <colgroup>
                        <col class="w-[36%]">
                        <col class="w-[44%]">
                        <col class="w-[20%]">
                    </colgroup>

                    <thead class="sticky top-0 z-10 text-xs text-gray-900 uppercase bg-gray-100">
                        <tr>
                            <th class="px-4 py-3">Nama Pasien</th>
                            <th class="px-4 py-3">Dokter Peresep</th>
                            <th class="px-2 py-3">Antrian</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white">
                        @foreach ($myQueryDataAntri as $myQData)
                            @php
                                $j = $myQData->datadaftarpolirj_json;
                                $eresep = isset($j['eresep']) ? 1 : 0;
                                $antrian = $j['noAntrianApotek']['noAntrian'] ?? 0;
                                $racikan = collect($j['eresepRacikan'] ?? [])->count() > 0;
                                $jenis = $racikan ? 'racikan' : 'non racikan';
                            @endphp
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3 font-semibold text-gray-900 truncate whitespace-nowrap">
                                    {{ $myQData->reg_name }}
                                </td>
                                <td class="px-4 py-3 truncate whitespace-nowrap">
                                    <span class="text-xs ">{{ $myQData->dr_name }} </span><br>
                                    {{ $myQData->poli_desc }}
                                </td>
                                <td class="px-2 py-3">
                                    <div class="items-center gap-2">
                                        <span class="text-xl font-semibold">{{ $antrian }}</span>
                                        <br>
                                        @if ($eresep)
                                            <x-badge :badgecolor="$jenis === 'racikan' ? 'default' : 'green'">{{ $jenis }}</x-badge>
                                        @else
                                            <x-badge :badgecolor="'red'">---</x-badge>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-2">{{ $myQueryDataAntri->links() }}</div>
            </div>

            {{-- KANAN: LUNAS --}}
            <div class="overflow-y-auto max-h-[calc(100vh-220px)]">

                <div class="sticky top-0 z-20 bg-white border-b border-gray-200">
                    <h2 class="px-4 py-2 text-base font-semibold text-primary">
                        ✅ Sudah Selesai
                    </h2>
                </div>

                <table class="min-w-full text-sm text-left text-gray-700 border-collapse table-fixed">
                    <colgroup>
                        <col class="w-[36%]">
                        <col class="w-[44%]">
                        <col class="w-[20%]">
                    </colgroup>

                    <thead class="sticky top-0 z-10 text-xs text-gray-900 uppercase bg-gray-100">
                        <tr>
                            <th class="px-4 py-3">Nama Pasien</th>
                            <th class="px-4 py-3">Dokter Peresep</th>
                            <th class="px-2 py-3">Antrian</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white">
                        @foreach ($myQueryDataLunas as $myQData)
                            @php
                                $j = $myQData->datadaftarpolirj_json;
                                $eresep = isset($j['eresep']) ? 1 : 0;
                                $antrian = $j['noAntrianApotek']['noAntrian'] ?? 0;
                                $racikan = collect($j['eresepRacikan'] ?? [])->count() > 0;
                                $jenis = $racikan ? 'racikan' : 'non racikan';
                            @endphp
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3 font-semibold text-gray-900 truncate whitespace-nowrap">
                                    {{ $myQData->reg_name }}
                                </td>
                                <td class="px-4 py-3 truncate whitespace-nowrap">
                                    <span class="text-xs">{{ $myQData->dr_name }}</span><br> {{ $myQData->poli_desc }}
                                </td>
                                <td class="px-2 py-3">
                                    <div class="">
                                        <span class="text-sm font-semibold">{{ 'Selesai' }}</span>
                                        <br>
                                        @if ($eresep)
                                            <x-badge :badgecolor="$jenis === 'racikan' ? 'default' : 'green'">{{ $jenis }}</x-badge>
                                        @else
                                            <x-badge :badgecolor="'red'">---</x-badge>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-2">{{ $myQueryDataLunas->links() }}</div>
            </div>

        </div>


        {{-- no data found start --}}
        @if ($myQueryDataLunas->count() == 0)
            <div class="w-full p-4 text-sm text-center text-gray-900 dark:text-gray-400">
                {{ 'Data ' . $myProgram . ' Tidak ditemukan' }}
            </div>
        @endif
        {{-- no data found end --}}

    </div>





    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('autoScroller', (opts = {}) => ({
                // opsi
                step: opts.step ?? 1, // px per “tick”
                interval: opts.interval ?? 25, // ms per “tick”
                waitTop: opts.waitTop ?? 800, // jeda ketika baru di-top
                waitBottom: opts.waitBottom ?? 1200, // jeda ketika sampai bottom
                timer: null,
                running: false,

                start() {
                    this.running = true;
                    // restart setiap render Livewire agar tetap mulus
                    window.addEventListener('livewire:load', () => {
                        if (window.Livewire) {
                            Livewire.hook('message.processed', () => this.restart());
                        }
                    });
                    // hormati prefers-reduced-motion
                    const mql = window.matchMedia('(prefers-reduced-motion: reduce)');
                    if (mql.matches) return; // jangan gerak otomatis
                    this.scrollLoop(true); // mulai dari atas dengan jeda top
                },

                restart() {
                    // dipanggil setelah data refresh/paginate
                    this.pause();
                    const el = this.$refs.scroller;
                    if (el) el.scrollTop = 0;
                    this.resume(true);
                },

                pause() {
                    this.running = false;
                    if (this.timer) {
                        clearTimeout(this.timer);
                        this.timer = null;
                    }
                },

                resume(fromTop = false) {
                    if (this.running) return; // sudah jalan
                    this.running = true;
                    this.scrollLoop(fromTop);
                },

                scrollLoop(fromTop = false) {
                    if (!this.running) return;
                    const el = this.$refs.scroller;
                    if (!el) return;

                    // kalau baru mulai & ada permintaan jeda di top
                    if (fromTop) {
                        this.timer = setTimeout(() => this.tick(), this.waitTop);
                    } else {
                        this.tick();
                    }
                },

                tick() {
                    if (!this.running) return;
                    const el = this.$refs.scroller;

                    // kalau sudah di bawah (>=)
                    const atBottom = Math.ceil(el.scrollTop + el.clientHeight) >= el.scrollHeight;
                    if (atBottom) {
                        // jeda di bawah, lalu lompat ke atas dan lanjut lagi
                        this.timer = setTimeout(() => {
                            el.scrollTop = 0; // langsung ke atas (tanpa animasi)
                            this.timer = setTimeout(() => { // jeda kecil di top bila mau
                                if (this.running) this.tick();
                            }, this.waitTop);
                        }, this.waitBottom);
                        return;
                    }

                    // scroll turun sedikit
                    el.scrollTop += this.step;
                    this.timer = setTimeout(() => this.tick(), this.interval);
                }
            }));
        });
    </script>



</div>
