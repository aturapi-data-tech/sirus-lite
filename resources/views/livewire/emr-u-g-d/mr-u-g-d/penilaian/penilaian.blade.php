<div>
    @php
        $disabledProperty = true;
        $disabledPropertyRjStatus = false;
    @endphp

    @if (isset($dataDaftarUgd['penilaian']))
        <div class="w-full mb-1">
            <div class="grid grid-cols-1">
                <div id="TransaksiRawatJalan" class="px-2">
                    <div id="TransaksiRawatJalan" x-data="{ activeTab: 'Status Medik' }">
                        <div class="px-2 mb-2 border-b border-gray-200 dark:border-gray-700">
                            <ul
                                class="flex flex-wrap -mb-px text-xs font-medium text-center text-gray-500 dark:text-gray-400">
                                <li class="mr-2">
                                    <button
                                        class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                        :class="activeTab === 'Status Medik' ? 'text-primary border-primary bg-gray-100' : ''"
                                        @click="activeTab = 'Status Medik'">
                                        Status Medik
                                    </button>
                                </li>

                                <li class="mr-2">
                                    <button
                                        class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                        :class="activeTab === 'Fisik' ? 'text-primary border-primary bg-gray-100' : ''"
                                        @click="activeTab = 'Fisik'">
                                        Fisik
                                    </button>
                                </li>

                                <li class="mr-2">
                                    <button
                                        class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                        :class="activeTab === 'Nyeri' ? 'text-primary border-primary bg-gray-100' : ''"
                                        @click="activeTab = 'Nyeri'">
                                        Nyeri
                                    </button>
                                </li>

                                <li class="mr-2">
                                    <button
                                        class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                        :class="activeTab === 'Status Pediatrik' ? 'text-primary border-primary bg-gray-100' :
                                            ''"
                                        @click="activeTab = 'Status Pediatrik'">
                                        Status Pediatrik
                                    </button>
                                </li>

                                <li class="mr-2">
                                    <button
                                        class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                        :class="activeTab === 'Diagnosis' ? 'text-primary border-primary bg-gray-100' : ''"
                                        @click="activeTab = 'Diagnosis'">
                                        Diagnosis
                                    </button>
                                </li>

                                <li class="mr-2">
                                    <button
                                        class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                        :class="activeTab === 'Skala Morse' ? 'text-primary border-primary bg-gray-100' : ''"
                                        @click="activeTab = 'Skala Morse'">
                                        Skala Morse
                                    </button>
                                </li>

                                <li class="mr-2">
                                    <button
                                        class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                        :class="activeTab === 'Skala Humpty Dumpty' ?
                                            'text-primary border-primary bg-gray-100' : ''"
                                        @click="activeTab = 'Skala Humpty Dumpty'">
                                        Skala Humpty Dumpty
                                    </button>
                                </li>

                                <li class="mr-2">
                                    <button
                                        class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                        :class="activeTab === 'Dekubitus' ? 'text-primary border-primary bg-gray-100' : ''"
                                        @click="activeTab = 'Dekubitus'">
                                        Dekubitus
                                    </button>
                                </li>
                            </ul>
                        </div>

                        <!-- Status Medik Tab -->
                        <div class="p-2 rounded-lg bg-gray-50 dark:bg-gray-800"
                            x-show.transition.in.opacity.duration.600="activeTab === 'Status Medik'">
                            @include('livewire.emr-u-g-d.mr-u-g-d.penilaian.status-medik-tab')
                        </div>

                        <!-- Fisik Tab -->
                        <div class="p-2 rounded-lg bg-gray-50 dark:bg-gray-800"
                            x-show.transition.in.opacity.duration.600="activeTab === 'Fisik'">
                            @include('livewire.emr-u-g-d.mr-u-g-d.penilaian.fisik-tab')
                        </div>

                        <!-- Nyeri Tab -->
                        <div class="p-2 rounded-lg bg-gray-50 dark:bg-gray-800"
                            x-show.transition.in.opacity.duration.600="activeTab === 'Nyeri'">
                            @include('livewire.emr-u-g-d.mr-u-g-d.penilaian.nyeri-tab')
                        </div>

                        <!-- Status Pediatrik Tab -->
                        <div class="p-2 rounded-lg bg-gray-50 dark:bg-gray-800"
                            x-show.transition.in.opacity.duration.600="activeTab === 'Status Pediatrik'">
                            @include('livewire.emr-u-g-d.mr-u-g-d.penilaian.status-pediatrik-tab')
                        </div>

                        <!-- Diagnosis Tab -->
                        <div class="p-2 rounded-lg bg-gray-50 dark:bg-gray-800"
                            x-show.transition.in.opacity.duration.600="activeTab === 'Diagnosis'">
                            @include('livewire.emr-u-g-d.mr-u-g-d.penilaian.diagnosis-tab')
                        </div>

                        <!-- Skala Morse Tab -->
                        <div class="p-2 rounded-lg bg-gray-50 dark:bg-gray-800"
                            x-show.transition.in.opacity.duration.600="activeTab === 'Skala Morse'">
                            @include('livewire.emr-u-g-d.mr-u-g-d.penilaian.skala-morse-tab')
                        </div>

                        <!-- Skala Humpty Dumpty Tab -->
                        <div class="p-2 rounded-lg bg-gray-50 dark:bg-gray-800"
                            x-show.transition.in.opacity.duration.600="activeTab === 'Skala Humpty Dumpty'">
                            @include('livewire.emr-u-g-d.mr-u-g-d.penilaian.skala-humpty-dumpty-tab')
                        </div>

                        <!-- Dekubitus Tab -->
                        <div class="p-2 rounded-lg bg-gray-50 dark:bg-gray-800"
                            x-show.transition.in.opacity.duration.600="activeTab === 'Dekubitus'">
                            @include('livewire.emr-u-g-d.mr-u-g-d.penilaian.dekubitus-tab')
                        </div>
                    </div>
                </div>

                {{-- <div class="sticky bottom-0 flex justify-between px-4 py-3 bg-gray-50 sm:px-6">
                    <div class=""></div>
                    <div>
                        <div wire:loading wire:target="store">
                            <x-loading />
                        </div>

                        <x-green-button :disabled="$disabledPropertyRjStatus" wire:click.prevent="store()" type="button"
                            wire:loading.remove>
                            Simpan Penilaian
                        </x-green-button>
                    </div>
                </div> --}}
            </div>
        </div>
    @endif
</div>
