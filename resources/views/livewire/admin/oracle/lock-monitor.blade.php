<div>
    <div class="px-1 pt-7">
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="w-full mb-1">
                <div id="TopBarMenuMaster" class="">
                    <div class="mb-2">
                        <h3 class="text-2xl font-bold text-midnight dark:text-white">Oracle Session Monitor</h3>
                        <span class="text-base font-normal text-gray-500 dark:text-gray-400">
                            Locks, Long-Running SQL & Kill
                        </span>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="flex items-center gap-2 mt-2">



                    <button wire:click="setTab('locks')"
                        class="px-3 py-1 rounded-md border text-sm {{ $tab === 'locks' ? 'bg-gray-900 text-white' : 'bg-gray-100' }}">
                        Locks
                    </button>
                    <button wire:click="setTab('heavy')"
                        class="px-3 py-1 rounded-md border text-sm {{ $tab === 'heavy' ? 'bg-gray-900 text-white' : 'bg-gray-100' }}">
                        Long-Running
                    </button>
                    <button wire:click="setTab('longops')"
                        class="px-3 py-1 rounded-md border text-sm {{ $tab === 'longops' ? 'bg-gray-900 text-white' : 'bg-gray-100' }}">
                        Long Ops
                    </button>
                    <div class="ml-auto text-xs text-gray-500">
                        Auto-refresh <span class="font-mono">
                            @if ($tab === 'locks' || $tab === 'longops')
                                5s
                            @else
                                15s
                            @endif
                        </span>
                    </div>
                    @if ($tab === 'heavy')
                        <div wire:poll.3s="refreshPerf"></div>
                        <div wire:poll.3s="refreshHeavy"></div>
                    @endif
                </div>

                <!-- Filters + Charts (contextual) -->
                <div class="grid grid-cols-1 gap-2 mt-3 md:grid-cols-3">

                    {{-- === HEAVY: Charts + filters === --}}
                    @if ($tab === 'heavy')
                        @once
                            <script>
                                window.addEventListener('perf-sample', (ev) => {
                                    console.log('perf-sample:', ev.detail); // harus muncul tiap 3s
                                });
                            </script>

                            <script>
                                (function() {
                                    let perfChart = null;
                                    let heavyChart = null;

                                    // --- Loader: pastikan Chart.js ter-load sebelum pakai
                                    function loadChartJs() {
                                        return new Promise((resolve, reject) => {
                                            if (window.Chart) return resolve();
                                            // kalau sudah ada tag script-nya, tunggu load
                                            let tag = document.querySelector('script[data-chartjs]');
                                            if (tag) {
                                                tag.addEventListener('load', () => resolve());
                                                tag.addEventListener('error', reject);
                                                return;
                                            }
                                            // sisipkan script Chart.js
                                            tag = document.createElement('script');
                                            tag.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js';
                                            tag.defer = true;
                                            tag.async = true;
                                            tag.dataset.chartjs = '';
                                            tag.onload = () => resolve();
                                            tag.onerror = reject;
                                            document.head.appendChild(tag);
                                        });
                                    }

                                    function ensurePerfChart() {
                                        const el = document.getElementById('perfChart');
                                        if (!el || perfChart) return;
                                        perfChart = new Chart(el.getContext('2d'), {
                                            type: 'line',
                                            data: {
                                                labels: [],
                                                datasets: [{
                                                        label: 'Average Active Sessions',
                                                        data: [],
                                                        tension: 0.3
                                                    },
                                                    {
                                                        label: 'DB CPU Time Ratio (%)',
                                                        data: [],
                                                        yAxisID: 'y1',
                                                        tension: 0.3
                                                    },
                                                    {
                                                        label: 'Host CPU Util (%)',
                                                        data: [],
                                                        yAxisID: 'y1',
                                                        tension: 0.3
                                                    },
                                                ]
                                            },
                                            options: {
                                                responsive: true,
                                                animation: false,
                                                plugins: {
                                                    legend: {
                                                        position: 'bottom'
                                                    }
                                                },
                                                scales: {
                                                    y: {
                                                        title: {
                                                            display: true,
                                                            text: 'AAS'
                                                        }
                                                    },
                                                    y1: {
                                                        position: 'right',
                                                        title: {
                                                            display: true,
                                                            text: '%'
                                                        },
                                                        min: 0,
                                                        max: 100,
                                                        grid: {
                                                            drawOnChartArea: false
                                                        }
                                                    }
                                                }
                                            }
                                        });
                                    }

                                    function ensureHeavyChart() {
                                        const el = document.getElementById('heavyChart');
                                        if (!el || heavyChart) return;
                                        heavyChart = new Chart(el.getContext('2d'), {
                                            type: 'bar',
                                            data: {
                                                labels: [],
                                                datasets: [{
                                                    label: 'Seconds Active',
                                                    data: []
                                                }]
                                            },
                                            options: {
                                                responsive: true,
                                                animation: false,
                                                plugins: {
                                                    legend: {
                                                        display: false
                                                    },
                                                    tooltip: {
                                                        callbacks: {
                                                            afterLabel: (ctx) => ctx?.raw?.event ? `\n${ctx.raw.event}` : ''
                                                        }
                                                    }
                                                },
                                                parsing: false,
                                                scales: {
                                                    y: {
                                                        beginAtZero: true,
                                                        title: {
                                                            display: true,
                                                            text: 'sec'
                                                        }
                                                    }
                                                }
                                            }
                                        });
                                    }

                                    // Event dari Livewire → pastikan Chart.js sudah ada dulu
                                    window.addEventListener('perf-sample', async (ev) => {
                                        try {
                                            await loadChartJs();
                                            // re-grab canvas tiap kali (DOM bisa berubah)
                                            if (!perfChart) ensurePerfChart();
                                            if (!perfChart) return;
                                            const {
                                                labels,
                                                series
                                            } = ev.detail;
                                            perfChart.data.labels = labels;
                                            perfChart.data.datasets[0].data = series.aas;
                                            perfChart.data.datasets[1].data = series.dbcpuRatio;
                                            perfChart.data.datasets[2].data = series.hostCpu;
                                            perfChart.update('none');
                                        } catch {}
                                    });

                                    window.addEventListener('heavy-top', async (ev) => {
                                        try {
                                            await loadChartJs();
                                            if (!heavyChart) ensureHeavyChart();
                                            if (!heavyChart) return;
                                            const bars = ev.detail.bars || [];
                                            heavyChart.data.labels = bars.map(b => b.label);
                                            heavyChart.data.datasets[0].data = bars.map(b => ({
                                                x: b.label,
                                                y: b.value,
                                                event: b.event
                                            }));
                                            heavyChart.update('none');
                                        } catch {}
                                    });

                                })
                                ();
                            </script>
                        @endonce

                        {{-- Charts row --}}
                        <div class="grid grid-cols-1 gap-4 mt-4 lg:col-span-3 lg:grid-cols-2">
                            {{-- Line chart: AAS, DB CPU Ratio, Host CPU --}}
                            <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm" wire:ignore>
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="font-semibold">Database Performance (rolling)</h3>
                                    <span class="text-xs text-gray-500">Live</span>
                                </div>
                                <canvas id="perfChart" height="140"></canvas>
                                {{-- polling khusus sampel perf --}}
                            </div>

                            {{-- Bar chart: Top Active Sessions by seconds_active --}}
                            <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm" wire:ignore>
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="font-semibold">Top Active Sessions (by seconds active)</h3>
                                    <span class="text-xs text-gray-500">Live</span>
                                </div>
                                <canvas id="heavyChart" height="140"></canvas>
                                {{-- polling khusus heavy (ini juga mengisi tabel heavyRows) --}}
                            </div>
                        </div>

                        {{-- Filters heavy --}}
                        <div class="flex items-center gap-2 lg:col-span-3">
                            <label class="text-sm">Active ≥</label>
                            <input type="number" min="0" class="w-20 px-2 py-1 border rounded"
                                wire:model.lazy="minSecondsActive" />
                            <span class="text-sm">s</span>
                            <label class="ml-4 text-sm">Exclude Idle</label>
                            <input type="checkbox" class="ml-1" wire:model="excludeIdle" />
                        </div>
                    @endif

                    {{-- === LOCKS filters === --}}
                    @if ($tab === 'locks')
                        <div class="flex items-center gap-2">
                            <label class="text-sm">Only Waiting ≥</label>
                            <input type="number" min="0" class="w-20 px-2 py-1 border rounded"
                                wire:model.lazy="minSecondsInWait" />
                            <span class="text-sm">s</span>
                            <label class="ml-4 text-sm">User</label>
                            <input type="text" class="px-2 py-1 border rounded" placeholder="SCOTT..."
                                wire:model.debounce.500ms="filterUser" />
                            <label class="ml-2 text-sm">Program</label>
                            <input type="text" class="px-2 py-1 border rounded" placeholder="JDBC..."
                                wire:model.debounce.500ms="filterProgram" />
                        </div>
                    @endif

                    {{-- === LONGOPS filters === --}}
                    @if ($tab === 'longops')
                        <div class="flex items-center gap-2">
                            <label class="text-sm">Min Progress ≥</label>
                            <input type="number" min="0" max="100" class="w-20 px-2 py-1 border rounded"
                                wire:model.lazy="minLongopsPct" />
                            <span class="text-sm">%</span>
                        </div>
                    @endif
                </div>

                <!-- Data Tables (poll per-tab) -->
                <div class="flex flex-col mt-4">
                    <div class="overflow-x-auto rounded-lg">
                        <div class="inline-block min-w-full align-middle">
                            <div class="overflow-hidden shadow sm:rounded-lg">

                                <div class="overflow-auto border rounded"
                                    @if ($tab === 'locks') wire:poll.keep-alive.3s="refreshLocks"
                                    @elseif($tab === 'longops') wire:poll.keep-alive.3s="refreshLongops" @endif>


                                    {{-- LOCKS table --}}
                                    @if ($tab === 'locks')
                                        <table class="w-full text-sm table-auto">
                                            <thead class="text-left bg-gray-100">
                                                <tr>
                                                    <th class="px-2 py-2">Waiter (SID,SER)</th>
                                                    <th class="px-2 py-2">Waiter User</th>
                                                    <th class="px-2 py-2">Waiter Program</th>
                                                    <th class="px-2 py-2">Wait Event</th>
                                                    <th class="px-2 py-2">Wait (s)</th>
                                                    <th class="px-2 py-2">Blocker (SID,SER)</th>
                                                    <th class="px-2 py-2">Blocker User</th>
                                                    <th class="px-2 py-2">Blocker Program</th>
                                                    <th class="px-2 py-2">Locked Object</th>
                                                    <th class="px-2 py-2">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($rows as $r)
                                                    @php
                                                        $bOk =
                                                            isset($r['blocker_sid'], $r['blocker_serial']) &&
                                                            is_numeric($r['blocker_sid']) &&
                                                            is_numeric($r['blocker_serial']);
                                                        $wOk =
                                                            isset($r['waiter_sid'], $r['waiter_serial']) &&
                                                            is_numeric($r['waiter_sid']) &&
                                                            is_numeric($r['waiter_serial']);
                                                    @endphp
                                                    <tr class="border-t">
                                                        <td class="px-2 py-2 font-mono">
                                                            {{ $r['waiter_sid'] ?? '' }},{{ $r['waiter_serial'] ?? '' }}
                                                        </td>
                                                        <td class="px-2 py-2">{{ $r['waiter_user'] ?? '-' }}</td>
                                                        <td class="px-2 py-2">{{ $r['waiter_program'] ?? '-' }}</td>
                                                        <td class="px-2 py-2">{{ $r['waiter_event'] ?? '-' }}</td>
                                                        <td class="px-2 py-2">{{ $r['waiter_seconds_wait'] ?? 0 }}</td>
                                                        <td class="px-2 py-2 font-mono">
                                                            {{ $r['blocker_sid'] ?? '' }},{{ $r['blocker_serial'] ?? '' }}
                                                        </td>
                                                        <td class="px-2 py-2">{{ $r['blocker_user'] ?? '-' }}</td>
                                                        <td class="px-2 py-2">{{ $r['blocker_program'] ?? '-' }}</td>
                                                        <td class="px-2 py-2">{{ $r['locked_object'] ?? '-' }}</td>
                                                        <td class="px-2 py-2">
                                                            <div class="flex gap-2">
                                                                <button
                                                                    class="px-3 py-1 text-white bg-red-600 rounded disabled:opacity-50"
                                                                    wire:click="confirmKill('{{ $r['blocker_sid'] ?? null }}','{{ $r['blocker_serial'] ?? null }}')"
                                                                    @if (!$bOk) disabled @endif
                                                                    title="{{ $bOk ? 'Kill blocker' : 'SID/SERIAL tidak tersedia' }}">
                                                                    Kill Blocker
                                                                </button>
                                                                <button
                                                                    class="px-3 py-1 bg-gray-200 rounded disabled:opacity-50"
                                                                    wire:click="confirmKill('{{ $r['waiter_sid'] ?? null }}','{{ $r['waiter_serial'] ?? null }}')"
                                                                    @if (!$wOk) disabled @endif
                                                                    title="{{ $wOk ? 'Kill waiter' : 'SID/SERIAL tidak tersedia' }}">
                                                                    Kill Waiter
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="10"
                                                            class="px-2 py-6 text-center text-gray-500">
                                                            Tidak ada blocking rows terdeteksi.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    @endif

                                    {{-- HEAVY table --}}
                                    @if ($tab === 'heavy')

                                        <table class="w-full text-sm table-auto">
                                            <thead class="text-left bg-gray-100">
                                                <tr>
                                                    <th class="px-2 py-2">(SID,SER)</th>
                                                    <th class="px-2 py-2">User</th>
                                                    <th class="px-2 py-2">Program</th>
                                                    <th class="px-2 py-2">Wait Class / Event</th>
                                                    <th class="px-2 py-2">Active (s)</th>
                                                    <th class="px-2 py-2">SQL_ID</th>
                                                    <th class="px-2 py-2">Elapsed (s)</th>
                                                    <th class="px-2 py-2">CPU (s)</th>
                                                    <th class="px-2 py-2">Buffers</th>
                                                    <th class="px-2 py-2">Disk Reads</th>
                                                    <th class="px-2 py-2">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($heavyRows as $r)
                                                    @php $ok = isset($r['sid'],$r['serial']) && is_numeric($r['sid']) && is_numeric($r['serial']); @endphp
                                                    <tr class="align-top border-t">
                                                        <td class="px-2 py-2 font-mono">
                                                            {{ $r['sid'] ?? '' }},{{ $r['serial'] ?? '' }}</td>
                                                        <td class="px-2 py-2">{{ $r['username'] ?? '-' }}</td>
                                                        <td class="px-2 py-2">{{ $r['program'] ?? '-' }}</td>
                                                        <td class="px-2 py-2">
                                                            <div>{{ $r['wait_class'] ?? '-' }}</div>
                                                            <div class="text-xs text-gray-500">
                                                                {{ $r['event'] ?? '-' }}</div>
                                                        </td>
                                                        <td class="px-2 py-2">{{ $r['seconds_active'] ?? 0 }}</td>
                                                        <td class="px-2 py-2 font-mono">{{ $r['sql_id'] ?? '-' }}</td>
                                                        <td class="px-2 py-2">
                                                            {{ number_format((float) ($r['elapsed_sec'] ?? 0), 2) }}
                                                        </td>
                                                        <td class="px-2 py-2">
                                                            {{ number_format((float) ($r['cpu_sec'] ?? 0), 2) }}</td>
                                                        <td class="px-2 py-2">{{ $r['buffer_gets'] ?? 0 }}</td>
                                                        <td class="px-2 py-2">{{ $r['disk_reads'] ?? 0 }}</td>
                                                        <td class="px-2 py-2">
                                                            <div class="flex gap-2">
                                                                <button
                                                                    class="px-3 py-1 text-white bg-red-600 rounded disabled:opacity-50"
                                                                    wire:click="confirmKill('{{ $r['sid'] ?? null }}','{{ $r['serial'] ?? null }}')"
                                                                    @if (!$ok) disabled @endif
                                                                    title="Kill session">
                                                                    Kill Session
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr class="border-b">
                                                        <td colspan="11" class="px-2 pb-3 text-xs text-gray-600">
                                                            <div class="font-mono whitespace-pre-wrap">
                                                                {{ $r['sql_text'] ?? '' }}</div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="11"
                                                            class="px-2 py-6 text-center text-gray-500">
                                                            Tidak ada sesi ACTIVE yang melebihi ambang waktu.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    @endif

                                    {{-- LONGOPS table --}}
                                    @if ($tab === 'longops')
                                        <table class="w-full text-sm table-auto">
                                            <thead class="text-left bg-gray-100">
                                                <tr>
                                                    <th class="px-2 py-2">(SID,SER)</th>
                                                    <th class="px-2 py-2">User</th>
                                                    <th class="px-2 py-2">Program</th>
                                                    <th class="px-2 py-2">Opname / Target</th>
                                                    <th class="px-2 py-2">Progress (%)</th>
                                                    <th class="px-2 py-2">Elapsed (s)</th>
                                                    <th class="px-2 py-2">ETA (s)</th>
                                                    <th class="px-2 py-2">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($longopsRows as $r)
                                                    @php $ok = isset($r['sid'],$r['serial']) && is_numeric($r['sid']) && is_numeric($r['serial']); @endphp
                                                    <tr class="border-t">
                                                        <td class="px-2 py-2 font-mono">
                                                            {{ $r['sid'] ?? '' }},{{ $r['serial'] ?? '' }}</td>
                                                        <td class="px-2 py-2">{{ $r['username'] ?? '-' }}</td>
                                                        <td class="px-2 py-2">{{ $r['program'] ?? '-' }}</td>
                                                        <td class="px-2 py-2">
                                                            <div>{{ $r['opname'] ?? '-' }}</div>
                                                            <div class="text-xs text-gray-500 break-all">
                                                                {{ $r['target'] ?? '-' }}</div>
                                                        </td>
                                                        <td class="px-2 py-2">{{ $r['pct'] ?? 0 }}</td>
                                                        <td class="px-2 py-2">{{ $r['elapsed_seconds'] ?? 0 }}</td>
                                                        <td class="px-2 py-2">{{ $r['time_remaining'] ?? 0 }}</td>
                                                        <td class="px-2 py-2">
                                                            <button
                                                                class="px-3 py-1 text-white bg-red-600 rounded disabled:opacity-50"
                                                                wire:click="confirmKill('{{ $r['sid'] ?? null }}','{{ $r['serial'] ?? null }}')"
                                                                @if (!$ok) disabled @endif
                                                                title="Kill session">
                                                                Kill Session
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="8"
                                                            class="px-2 py-6 text-center text-gray-500">
                                                            Tidak ada long operations yang berjalan.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    @endif
                                </div>

                                {{-- Modal konfirmasi --}}
                                @if ($showConfirm)
                                    <div class="fixed inset-0 z-40 flex items-center justify-center bg-black/40">
                                        <div class="w-full max-w-md p-4 bg-white shadow-xl rounded-xl">
                                            <div class="text-lg font-semibold">Konfirmasi Kill Session</div>
                                            <div class="mt-2 text-sm">
                                                Anda akan menghentikan sesi
                                                <span
                                                    class="font-mono">{{ $killSid }},{{ $killSerial }}</span>.
                                                Lanjutkan?
                                            </div>
                                            <div class="flex justify-end gap-2 mt-4">
                                                <x-secondary-button
                                                    wire:click="$set('showConfirm', false)">Batal</x-secondary-button>
                                                <x-red-button wire:click="$emit('confirmKill')">Kill</x-red-button>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>

            </div> <!-- /w-full -->
        </div> <!-- /card -->
    </div> <!-- /px -->
</div>
