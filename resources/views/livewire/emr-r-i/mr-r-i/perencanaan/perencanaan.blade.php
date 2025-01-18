<div>
    @php
        $disabledProperty = true;
        $disabledPropertyRjStatus = false;
    @endphp

    {{-- Render hanya jika data perencanaan ada --}}
    @if (isset($perencanaan))
        <div class="w-full mb-1">
            <div class="grid grid-cols-1">
                {{-- Tab Container --}}
                <div id="Perencanaan" class="px-2">
                    <div x-data="{ activeTab: 'Tindak Lanjut' }">
                        <!-- Navigasi Tab -->
                        <div class="px-2 mb-2 border-b border-gray-200 dark:border-gray-700">
                            <ul
                                class="flex flex-wrap -mb-px text-xs font-medium text-center text-gray-500 dark:text-gray-400">
                                <!-- Tab Tindak Lanjut -->
                                <li class="mr-2">
                                    <label
                                        class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                        :class="activeTab === 'Tindak Lanjut' ?
                                            'text-primary border-primary bg-gray-100' : ''"
                                        @click="activeTab = 'Tindak Lanjut'">
                                        Tindak Lanjut
                                    </label>
                                </li>

                                <!-- Tab Discharge Planning -->
                                <li class="mr-2">
                                    <label
                                        class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                        :class="activeTab === 'Discharge Planning' ? 'text-primary border-primary bg-gray-100' :
                                            ''"
                                        @click="activeTab = 'Discharge Planning'">
                                        Discharge Planning
                                    </label>
                                </li>

                                <!-- Tab Jadwal Kontrol  Post Inap -->
                                <li class="mr-2">
                                    <label
                                        class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                        :class="activeTab === 'Jadwal Kontrol  Post Inap' ?
                                            'text-primary border-primary bg-gray-100' : ''"
                                        @click="activeTab = 'Jadwal Kontrol  Post Inap'">
                                        Jadwal Kontrol Post Inap
                                    </label>
                                </li>
                            </ul>
                        </div>

                        <!-- Konten Tab -->
                        <div class="p-2 rounded-lg bg-gray-50 dark:bg-gray-800">
                            <!-- Konten Tindak Lanjut -->
                            <div x-show="activeTab === 'Tindak Lanjut'" x-transition:enter.opacity.duration.600>
                                @include('livewire.emr-r-i.mr-r-i.perencanaan.perencanaan1-tindak-lanjut')
                            </div>

                            <!-- Konten Discharge Planning -->
                            <div x-show="activeTab === 'Discharge Planning'" x-transition:enter.opacity.duration.600>
                                @include('livewire.emr-r-i.mr-r-i.perencanaan.perencanaan2-discharge-planning')
                            </div>

                            <!-- Konten Jadwal Kontrol  Post Inap -->
                            <div x-show="activeTab === 'Jadwal Kontrol  Post Inap'"
                                x-transition:enter.opacity.duration.600>
                                @include('livewire.emr-r-i.mr-r-i.perencanaan.perencanaan3-jadwal-kontrol')
                            </div>
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
