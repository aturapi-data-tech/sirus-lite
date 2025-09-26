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

        @if ($myTopBar['autoRefresh'] == 'Ya')
            <div wire:poll.30s="render" class="h-[calc(100vh-250px)] mt-2 overflow-auto" x-ref="scroller"
                x-on:mouseenter="pause()" x-on:mouseleave="resume()">
            @else
                <div class="h-[calc(100vh-250px)] mt-2 overflow-auto" x-ref="scroller" x-on:mouseenter="pause()"
                    x-on:mouseleave="resume()">
        @endif

        <p class="text-xs text-gray-700">Data Terakhir: {{ now()->format('d-m-y H:i:s') }}</p>

        <div class="grid grid-cols-2 gap-4">
            <!-- Table -->
            <table class="w-full text-sm text-left text-gray-700 table-auto ">
                <thead class="sticky top-0 text-xs text-gray-900 uppercase bg-gray-100 ">
                    <tr>
                        <th scope="col" class="w-1/4 px-4 py-3 ">
                            Nama Pasien
                        </th>

                        <th scope="col" class="w-1/4 px-4 py-3 ">
                            Dokter Peresep
                        </th>
                        <th scope="col" class="w-1/4 px-2 py-3 ">
                            Antrian
                        </th>

                    </tr>
                </thead>

                <tbody class="bg-white ">

                    @foreach ($myQueryData as $myQData)
                        @php
                            $datadaftar_json = $myQData->datadaftarpolirj_json;

                            $eresep = isset($datadaftar_json['eresep']) ? 1 : 0;
                            $noAntrianFarmasi = isset($datadaftar_json['noAntrianApotek']['noAntrian'])
                                ? $datadaftar_json['noAntrianApotek']['noAntrian']
                                : 0;

                            $eresepRacikan = collect(
                                isset($datadaftar_json['eresepRacikan']) ? $datadaftar_json['eresepRacikan'] : [],
                            )->count();
                            $jenisResep = $eresepRacikan ? 'racikan' : 'non racikan';

                            $prosentaseEMR = ($eresep / 1) * 100;

                        @endphp


                        <tr class="border-b group ">


                            <td class="px-4 py-3 group-hover:bg-gray-100 whitespace-nowrap ">
                                <div class="">

                                    <div class="font-semibold text-gray-900">
                                        {{ $myQData->reg_name }}
                                    </div>

                                </div>
                            </td>

                            <td class="px-4 py-3 group-hover:bg-gray-100 whitespace-nowrap ">
                                <div class="flex gap-2">

                                    <div class="text-gray-700 ">
                                        {{ $myQData->dr_name }}
                                    </div>
                                    /
                                    <div class="text-gray-700 ">
                                        {{ $myQData->poli_desc }}
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-3 group-hover:bg-gray-100 whitespace-nowrap ">
                                <div class="flex gap-2">
                                    <div>
                                        <span class="text-xl font-semibold text-gray-700">
                                            {{ $noAntrianFarmasi }}
                                        </span>
                                    </div>

                                    <div>
                                        @if ($jenisResep == 'racikan' && $eresep > 0)
                                            <x-badge :badgecolor="__('default')"> {{ $jenisResep }}</x-badge>
                                        @elseif($jenisResep == 'non racikan' && $eresep > 0)
                                            <x-badge :badgecolor="__('green')"> {{ $jenisResep }}</x-badge>
                                        @else
                                            <x-badge :badgecolor="__('red')"> {{ '---' }}</x-badge>
                                        @endif
                                    </div>
                                </div>

                            </td>


                        </tr>
                    @endforeach

                </tbody>
            </table>

            <div class="sticky top-0 z-10 self-start w-full text-sm text-left text-gray-700">
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
        </div>
        {{-- no data found start --}}
        @if ($myQueryData->count() == 0)
            <div class="w-full p-4 text-sm text-center text-gray-900 dark:text-gray-400">
                {{ 'Data ' . $myProgram . ' Tidak ditemukan' }}
            </div>
        @endif
        {{-- no data found end --}}

    </div>


    {{ $myQueryData->links() }}




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
