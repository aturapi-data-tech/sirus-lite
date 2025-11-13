<div>
    @php
        $disabledProperty = true;
        $disabledPropertyRjStatus = false;
    @endphp

    {{-- jika observasi pengeluaran cairan kosong ngak usah di render --}}
    @if (isset($dataDaftarRi['observasi']['pengeluaranCairan']))
        <div class="w-full mb-1">

            <div id="PengeluaranCairan" class="px-2">
                <div id="PengeluaranCairan" x-data="{ activeTab: false }">

                    <div class="px-2 mb-2 border-b border-gray-200 dark:border-gray-700">
                        <ul
                            class="flex flex-wrap -mb-px text-xs font-medium text-center text-gray-500 dark:text-gray-400">

                            <li class="mr-2">
                                <label
                                    class="inline-block p-2 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                    :class="activeTab === '{{ $dataDaftarRi['observasi']['pengeluaranCairan']['pengeluaranCairanTab'] }}'
                                        ?
                                        'text-primary border-primary bg-gray-100' :
                                        ''"
                                    @click="activeTab = !activeTab ? '{{ $dataDaftarRi['observasi']['pengeluaranCairan']['pengeluaranCairanTab'] }}' : false">
                                    {{ $dataDaftarRi['observasi']['pengeluaranCairan']['pengeluaranCairanTab'] }}
                                </label>
                            </li>
                        </ul>
                    </div>

                    <div class="p-2 rounded-lg bg-gray-50"
                        :class="{
                            'active': activeTab === '{{ $dataDaftarRi['observasi']['pengeluaranCairan']['pengeluaranCairanTab'] }}'
                        }"
                        x-show.transition.in.opacity.duration.600="activeTab === '{{ $dataDaftarRi['observasi']['pengeluaranCairan']['pengeluaranCairanTab'] }}'"
                        @click.outside="activeTab=false">

                        @include('livewire.emr-r-i.mr-r-i.pengeluaran-cairan.pengeluaran-cairan-tab')
                    </div>

                </div>
            </div>

        </div>
    @endif

</div>
