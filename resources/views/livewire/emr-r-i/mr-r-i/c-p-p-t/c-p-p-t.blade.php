<div>
    @php
        $disabledProperty = true;
        $disabledPropertyRjStatus = false;
    @endphp
    {{-- Render hanya jika data CPPT ada --}}
    @if (isset($dataDaftarRi['cppt']))
        <div class="w-full mb-1">
            <div class="grid grid-cols-1">
                {{-- Tab Container --}}
                <div id="TabCPPT" class="px-2">
                    <div x-data="{ activeTab: 'CPPT' }">
                        <!-- Navigasi Tab -->
                        <div class="px-2 mb-2 border-b border-gray-200 dark:border-gray-700">
                            <ul
                                class="flex flex-wrap -mb-px text-xs font-medium text-center text-gray-500 dark:text-gray-400">
                                <!-- Tab CPPT -->
                                <li class="mr-2">
                                    <label
                                        class="inline-block p-4 border-b-2 border-transparent rounded-t-lg cursor-pointer hover:text-gray-600 hover:border-gray-300"
                                        :class="activeTab === 'CPPT' ? 'text-primary border-primary bg-gray-100' : ''"
                                        @click="activeTab = 'CPPT'">
                                        Catatan Perkembangan Pasien Terintegrasi (CPPT)
                                    </label>
                                </li>

                                <li class="mr-2">
                                    <label
                                        class="inline-block p-4 border-b-2 border-transparent rounded-t-lg cursor-pointer hover:text-gray-600 hover:border-gray-300"
                                        :class="activeTab === 'CaseManager' ? 'text-primary border-primary bg-gray-100' : ''"
                                        @click="activeTab = 'CaseManager'">
                                        MPP
                                    </label>
                                </li>
                            </ul>
                        </div>

                        <!-- Konten Tab CPPT -->
                        <div class="p-2 rounded-lg bg-gray-50 dark:bg-gray-800">
                            <div x-show="activeTab === 'CPPT'" x-transition:enter.opacity.duration.600>
                                @include('livewire.emr-r-i.mr-r-i.c-p-p-t.form-entry-c-p-p-t1')
                            </div>
                        </div>

                        <div class="p-2 rounded-lg bg-gray-50 dark:bg-gray-800">
                            <div x-show="activeTab === 'CaseManager'" x-transition:enter.opacity.duration.600>
                                @include('livewire.emr-r-i.mr-r-i.c-p-p-t.form-entry-case-manager')
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tombol Simpan --}}
                {{-- <div class="sticky bottom-0 flex justify-between px-4 py-3 bg-gray-50 sm:px-6">
                    <div></div>
                    <div>
                        <div wire:loading wire:target="store">
                            <x-loading />
                        </div>
                        <x-green-button :disabled="$disabledPropertyRjStatus" wire:click.prevent="store()" type="button"
                            wire:loading.remove>
                            Simpan
                        </x-green-button>
                    </div>
                </div> --}}
            </div>
        </div>
    @endif
</div>
