<div>
    @php
        $disabledProperty = true;
        $disabledPropertyRjStatus = false;
    @endphp
    {{-- jika observasi kosong ngak usah di render --}}
    @if (isset($dataDaftarUgd['observasi']['observasiLanjutan']))
        <div class="w-full mb-1">

            <div id="TransaksiRawatJalan" class="px-2">
                <div id="TransaksiRawatJalan" x-data="{ activeTab: false }">

                    <div class="px-2 mb-2 border-b border-gray-200 dark:border-gray-700">
                        <ul
                            class="flex flex-wrap -mb-px text-xs font-medium text-center text-gray-500 dark:text-gray-400">

                            <li class="mr-2">
                                <label
                                    class="inline-block p-2 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                    :class="activeTab === '{{ $dataDaftarUgd['observasi']['observasiLanjutan']['tandaVitalTab'] }}'
                                        ?
                                        'text-primary border-primary bg-gray-100' : ''"
                                    @click="activeTab =!activeTab?'{{ $dataDaftarUgd['observasi']['observasiLanjutan']['tandaVitalTab'] }}':false">{{ $dataDaftarUgd['observasi']['observasiLanjutan']['tandaVitalTab'] }}</label>
                            </li>
                    </div>

                    <div class="p-2 rounded-lg bg-gray-50"
                        :class="{
                            'active': activeTab === '{{ $dataDaftarUgd['observasi']['observasiLanjutan']['tandaVitalTab'] }}'
                        }"
                        x-show.transition.in.opacity.duration.600="activeTab === '{{ $dataDaftarUgd['observasi']['observasiLanjutan']['tandaVitalTab'] }}'"
                        @click.outside="activeTab=false">
                        @include('livewire.emr-u-g-d.mr-u-g-d.observasi.tanda-vital-tab')

                    </div>

                    <div>
                        {{-- Grafik UGD --}}
                        <div wire:ignore>
                            <div class="mb-1 text-sm font-semibold">Grafik Garis Suhu dan Nadi (UGD)</div>
                            <canvas id="ugdChart"></canvas>
                        </div>

                        @php
                            // Ambil & urutkan data TTV UGD berdasarkan waktu (ascending biar grafik runut)
                            $sortedTandaVitalUgd = collect(
                                $dataDaftarUgd['observasi']['observasiLanjutan']['tandaVital'] ?? [],
                            )
                                ->sortBy(function ($item) {
                                    try {
                                        return \Carbon\Carbon::createFromFormat(
                                            'd/m/Y H:i:s',
                                            $item['waktuPemeriksaan'],
                                            env('APP_TIMEZONE'),
                                        );
                                    } catch (\Exception $e) {
                                        return \Carbon\Carbon::now();
                                    }
                                })
                                ->values();

                            $labelsUgd = [];
                            $suhuUgd = [];
                            $nadiUgd = [];

                            foreach ($sortedTandaVitalUgd as $item) {
                                try {
                                    $labelsUgd[] = \Carbon\Carbon::createFromFormat(
                                        'd/m/Y H:i:s',
                                        $item['waktuPemeriksaan'],
                                        env('APP_TIMEZONE'),
                                    )->format('d/m/Y H:i:s');
                                } catch (\Exception $e) {
                                    $labelsUgd[] = $item['waktuPemeriksaan'] ?? '-';
                                }

                                $suhuUgd[] = is_numeric($item['suhu'] ?? null) ? (float) $item['suhu'] : null;
                                $nadiUgd[] = is_numeric($item['frekuensiNadi'] ?? null)
                                    ? (int) $item['frekuensiNadi']
                                    : null;
                            }
                        @endphp

                        <script>
                            const ctx = document.getElementById('ugdChart');
                            // Mengambil data dari PHP ke JavaScript
                            const labels = {!! json_encode($labelsUgd) !!};
                            const suhuData = {!! json_encode($suhuUgd) !!};
                            const nadiData = {!! json_encode($nadiUgd) !!};

                            new Chart(ctx, {
                                type: 'line', // Ubah type dari 'bar' menjadi 'line'
                                data: {
                                    labels: labels,
                                    datasets: [{
                                            label: 'Suhu', // Label untuk dataset pertama
                                            data: suhuData, // Data untuk dataset pertama
                                            borderColor: 'rgba(54, 162, 235, 1)', // Warna garis
                                            borderWidth: 2, // Ketebalan garis
                                            fill: false // Tidak mengisi area di bawah garis
                                        },
                                        {
                                            label: 'Nadi', // Label untuk dataset kedua
                                            data: nadiData, // Data untuk dataset kedua
                                            borderColor: 'rgba(255, 99, 132, 1)', // Warna garis
                                            borderWidth: 2, // Ketebalan garis
                                            fill: false // Tidak mengisi area di bawah garis
                                        }
                                    ]
                                },
                                options: {
                                    scales: {
                                        y: {
                                            beginAtZero: true
                                        }
                                    }
                                }
                            });
                        </script>
                    </div>

                </div>
            </div>



            {{-- <div class="sticky bottom-0 flex justify-between px-4 py-3 bg-gray-50 sm:px-6">

                <div class="">

                </div>
                <div>
                    <div wire:loading wire:target="store">
                        <x-loading />
                    </div>

                    <x-green-button :disabled=false wire:click.prevent="store()" type="button" wire:loading.remove>
                        Simpan
                    </x-green-button>
                </div>
            </div> --}}


        </div>
    @endif

</div>
