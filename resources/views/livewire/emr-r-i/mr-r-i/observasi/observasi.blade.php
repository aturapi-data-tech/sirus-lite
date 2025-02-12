<div>
    @php
        $disabledProperty = true;
        $disabledPropertyRjStatus = false;
    @endphp
    {{-- jika observasi kosong ngak usah di render --}}
    @if (isset($dataDaftarRi['observasi']['observasiLanjutan']))
        <div class="w-full mb-1">

            <div id="TransaksiRawatJalan" class="px-2">
                <div id="TransaksiRawatJalan" x-data="{ activeTab: false }">

                    <div class="px-2 mb-2 border-b border-gray-200 dark:border-gray-700">
                        <ul
                            class="flex flex-wrap -mb-px text-xs font-medium text-center text-gray-500 dark:text-gray-400">

                            <li class="mr-2">
                                <label
                                    class="inline-block p-2 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                    :class="activeTab === '{{ $dataDaftarRi['observasi']['observasiLanjutan']['tandaVitalTab'] }}'
                                        ?
                                        'text-primary border-primary bg-gray-100' : ''"
                                    @click="activeTab =!activeTab?'{{ $dataDaftarRi['observasi']['observasiLanjutan']['tandaVitalTab'] }}':false">{{ $dataDaftarRi['observasi']['observasiLanjutan']['tandaVitalTab'] }}</label>
                            </li>
                    </div>

                    <div class="p-2 rounded-lg bg-gray-50"
                        :class="{
                            'active': activeTab === '{{ $dataDaftarRi['observasi']['observasiLanjutan']['tandaVitalTab'] }}'
                        }"
                        x-show.transition.in.opacity.duration.600="activeTab === '{{ $dataDaftarRi['observasi']['observasiLanjutan']['tandaVitalTab'] }}'"
                        @click.outside="activeTab=false">
                        @include('livewire.emr-r-i.mr-r-i.observasi.tanda-vital-tab')

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
    <div wire:ignore>
        <div>Grafik Garis Suhu dan Nadi</div>
        <canvas id="myChart"></canvas>
    </div>
    @php
        use Carbon\Carbon;

        $sortedTandaVital = collect($dataDaftarRi['observasi']['observasiLanjutan']['tandaVital'] ?? [])->sortBy(
            function ($item) {
                return Carbon::createFromFormat('d/m/Y H:i:s', $item['waktuPemeriksaan'], env('APP_TIMEZONE'));
            },
        );
        $labelTgl = [];
        $labelSuhu = [];
        $labelNadi = [];
        foreach ($sortedTandaVital as $item) {
            $labelTgl[] = Carbon::createFromFormat(
                'd/m/Y H:i:s',
                $item['waktuPemeriksaan'],
                env('APP_TIMEZONE'),
            )->format('d/m/Y H:i:s');
            $labelSuhu[] = $item['suhu'];
            $labelNadi[] = $item['frekuensiNadi'];
        }

    @endphp


    <script>
        const ctx = document.getElementById('myChart');
        // Mengambil data dari PHP ke JavaScript
        const labels = {!! json_encode($labelTgl) !!};
        const suhuData = {!! json_encode($labelSuhu) !!};
        const nadiData = {!! json_encode($labelNadi) !!};

        new Chart(ctx, {
            type: 'line', // Ubah type dari 'bar' menjadi 'line'
            data: {
                labels: labels,
                datasets: [{
                        label: 'Suhu', // Label untuk dataset pertama
                        data: suhuData, // Data untuk dataset pertama
                        borderColor: 'rgba(255, 99, 132, 1)', // Warna garis
                        borderWidth: 2, // Ketebalan garis
                        fill: false // Tidak mengisi area di bawah garis
                    },
                    {
                        label: 'Nadi', // Label untuk dataset kedua
                        data: nadiData, // Data untuk dataset kedua
                        borderColor: 'rgba(54, 162, 235, 1)', // Warna garis
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
