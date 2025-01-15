<div>
    @php
        $disabledProperty = true;
        $disabledPropertyRjStatus = false;
    @endphp

    {{-- Render hanya jika data penilaian ada --}}
    @if (isset($dataDaftarRi['penilaian']))
        <div class="w-full mb-1">
            <div class="grid grid-cols-1">
                {{-- Tab Container --}}
                <div id="TransaksiRawatJalan" class="px-2">
                    <div id="TransaksiRawatJalan" x-data="{ activeTab: 'Nyeri' }">
                        {{-- Tab Navigation --}}
                        <div class="px-2 mb-2 border-b border-gray-200 dark:border-gray-700">
                            <ul
                                class="flex flex-wrap -mb-px text-xs font-medium text-center text-gray-500 dark:text-gray-400">
                                {{-- Tab Nyeri --}}
                                <li class="mr-2">
                                    <label
                                        class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                        :class="activeTab === '{{ $dataDaftarRi['penilaian']['nyeriTab'] }}' ?
                                            'text-primary border-primary bg-gray-100' : ''"
                                        @click="activeTab ='{{ $dataDaftarRi['penilaian']['nyeriTab'] }}'">
                                        {{ $dataDaftarRi['penilaian']['nyeriTab'] }}
                                    </label>
                                </li>

                                {{-- Tab Status Pediatrik --}}
                                <li class="mr-2">
                                    <label
                                        class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                        :class="activeTab === '{{ $dataDaftarRi['penilaian']['statusPediatrikTab'] }}' ?
                                            'text-primary border-primary bg-gray-100' : ''"
                                        @click="activeTab ='{{ $dataDaftarRi['penilaian']['statusPediatrikTab'] }}'">
                                        {{ $dataDaftarRi['penilaian']['statusPediatrikTab'] }}
                                    </label>
                                </li>

                                {{-- Tab Resiko Jatuh (Skala Morse) --}}
                                <li class="mr-2">
                                    <label
                                        class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                        :class="activeTab === '{{ $dataDaftarRi['penilaian']['resikoJatuhTab'] . $dataDaftarRi['penilaian']['resikoJatuh']['skalaMorse']['skalaMorseTab'] }}'
                                            ?
                                            'text-primary border-primary bg-gray-100' : ''"
                                        @click="activeTab ='{{ $dataDaftarRi['penilaian']['resikoJatuhTab'] . $dataDaftarRi['penilaian']['resikoJatuh']['skalaMorse']['skalaMorseTab'] }}'">
                                        {{ $dataDaftarRi['penilaian']['resikoJatuhTab'] . ' / ' . $dataDaftarRi['penilaian']['resikoJatuh']['skalaMorse']['skalaMorseTab'] }}
                                    </label>
                                </li>

                                {{-- Tab Resiko Jatuh (Skala Humpty Dumpty) --}}
                                <li class="mr-2">
                                    <label
                                        class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                        :class="activeTab === '{{ $dataDaftarRi['penilaian']['resikoJatuhTab'] . $dataDaftarRi['penilaian']['resikoJatuh']['skalaHumptyDumpty']['skalaHumptyDumptyTab'] }}'
                                            ?
                                            'text-primary border-primary bg-gray-100' : ''"
                                        @click="activeTab ='{{ $dataDaftarRi['penilaian']['resikoJatuhTab'] . $dataDaftarRi['penilaian']['resikoJatuh']['skalaHumptyDumpty']['skalaHumptyDumptyTab'] }}'">
                                        {{ $dataDaftarRi['penilaian']['resikoJatuhTab'] . ' / ' . $dataDaftarRi['penilaian']['resikoJatuh']['skalaHumptyDumpty']['skalaHumptyDumptyTab'] }}
                                    </label>
                                </li>

                                {{-- Tab Dekubitus --}}
                                <li class="mr-2">
                                    <label
                                        class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                        :class="activeTab === '{{ $dataDaftarRi['penilaian']['dekubitus']['dekubitusTab'] }}' ?
                                            'text-primary border-primary bg-gray-100' : ''"
                                        @click="activeTab ='{{ $dataDaftarRi['penilaian']['dekubitus']['dekubitusTab'] }}'">
                                        {{ $dataDaftarRi['penilaian']['dekubitus']['dekubitusTab'] }}
                                    </label>
                                </li>
                            </ul>
                        </div>

                        {{-- Konten Tab --}}
                        <div class="p-2 rounded-lg bg-gray-50 dark:bg-gray-800"
                            :class="{ 'active': activeTab === '{{ $dataDaftarRi['penilaian']['nyeriTab'] }}' }"
                            x-show.transition.in.opacity.duration.600="activeTab === '{{ $dataDaftarRi['penilaian']['nyeriTab'] }}'">
                            @include('livewire.emr-r-i.mr-r-i.penilaian.nyeriTab')
                        </div>

                        <div class="p-2 rounded-lg bg-gray-50 dark:bg-gray-800"
                            :class="{ 'active': activeTab === '{{ $dataDaftarRi['penilaian']['statusPediatrikTab'] }}' }"
                            x-show.transition.in.opacity.duration.600="activeTab === '{{ $dataDaftarRi['penilaian']['statusPediatrikTab'] }}'">
                            @include('livewire.emr-r-i.mr-r-i.penilaian.statusPediatrikTab')
                        </div>

                        <div class="p-2 rounded-lg bg-gray-50 dark:bg-gray-800"
                            :class="{ 'active': activeTab === '{{ $dataDaftarRi['penilaian']['resikoJatuhTab'] . $dataDaftarRi['penilaian']['resikoJatuh']['skalaMorse']['skalaMorseTab'] }}' }"
                            x-show.transition.in.opacity.duration.600="activeTab === '{{ $dataDaftarRi['penilaian']['resikoJatuhTab'] . $dataDaftarRi['penilaian']['resikoJatuh']['skalaMorse']['skalaMorseTab'] }}'">
                            @include('livewire.emr-r-i.mr-r-i.penilaian.skalaMorseTab')
                        </div>

                        <div class="p-2 rounded-lg bg-gray-50 dark:bg-gray-800"
                            :class="{ 'active': activeTab === '{{ $dataDaftarRi['penilaian']['resikoJatuhTab'] . $dataDaftarRi['penilaian']['resikoJatuh']['skalaHumptyDumpty']['skalaHumptyDumptyTab'] }}' }"
                            x-show.transition.in.opacity.duration.600="activeTab === '{{ $dataDaftarRi['penilaian']['resikoJatuhTab'] . $dataDaftarRi['penilaian']['resikoJatuh']['skalaHumptyDumpty']['skalaHumptyDumptyTab'] }}'">
                            @include('livewire.emr-r-i.mr-r-i.penilaian.skalaHumptyDumptyTab')
                        </div>

                        <div class="p-2 rounded-lg bg-gray-50 dark:bg-gray-800"
                            :class="{ 'active': activeTab === '{{ $dataDaftarRi['penilaian']['dekubitus']['dekubitusTab'] }}' }"
                            x-show.transition.in.opacity.duration.600="activeTab === '{{ $dataDaftarRi['penilaian']['dekubitus']['dekubitusTab'] }}'">
                            @include('livewire.emr-r-i.mr-r-i.penilaian.dekubitusTab')
                        </div>
                    </div>
                </div>

                {{-- Tombol Simpan --}}
                <div class="sticky bottom-0 flex justify-between px-4 py-3 bg-gray-50 sm:px-6">
                    <div></div>
                    <div>
                        <div wire:loading wire:target="store">
                            <x-loading />
                        </div>
                        <x-green-button :disabled=$disabledPropertyRjStatus wire:click.prevent="store()" type="button"
                            wire:loading.remove>
                            Simpan
                        </x-green-button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
