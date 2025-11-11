<div>
    @php
        $disabledProperty = true;
        $disabledPropertyRjStatus = false;
    @endphp

    @if (isset($dataDaftarUgd['pemeriksaan']))
        <div class="w-full mb-1">
            <div class="grid grid-cols-1">
                <div id="TransaksiRawatJalan" class="px-2">
                    <div x-data="{ activeTab: 'Umum' }">
                        <div class="px-2 border-b border-gray-200 dark:border-gray-700">
                            <ul
                                class="flex flex-wrap -mb-px text-xs font-medium text-center text-gray-500 dark:text-gray-400">
                                <li class="mr-2">
                                    <button type="button"
                                        class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                        :class="activeTab === 'Umum' ? 'text-primary border-primary bg-gray-100' : ''"
                                        @click="activeTab = 'Umum'">
                                        Umum
                                    </button>
                                </li>
                                <li class="mr-2">
                                    <button type="button"
                                        class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                        :class="activeTab === 'Fisik' ? 'text-primary border-primary bg-gray-100' : ''"
                                        @click="activeTab = 'Fisik'">
                                        Fisik
                                    </button>
                                </li>
                                <li class="mr-2">
                                    <button type="button"
                                        class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                        :class="activeTab === 'Anatomi' ? 'text-primary border-primary bg-gray-100' : ''"
                                        @click="activeTab = 'Anatomi'">
                                        Anatomi
                                    </button>
                                </li>
                                <li class="mr-2">
                                    <button type="button"
                                        class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                        :class="activeTab === 'Penunjang' ? 'text-primary border-primary bg-gray-100' : ''"
                                        @click="activeTab = 'Penunjang'">
                                        Penunjang
                                    </button>
                                </li>
                                <li class="mr-2">
                                    <button type="button"
                                        class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                        :class="activeTab === 'PenunjangHasil' ? 'text-primary border-primary bg-gray-100' : ''"
                                        @click="activeTab = 'PenunjangHasil'">
                                        Pelayanan Penunjang
                                    </button>
                                </li>
                            </ul>
                        </div>

                        <div class="p-2 rounded-lg bg-gray-50 dark:bg-gray-800" x-show="activeTab === 'Umum'">
                            @include('livewire.emr-u-g-d.mr-u-g-d.pemeriksaan.umumTab')
                        </div>

                        <div class="p-2 rounded-lg bg-gray-50 dark:bg-gray-800" x-show="activeTab === 'Fisik'">
                            @include('livewire.emr-u-g-d.mr-u-g-d.pemeriksaan.fisikTab')
                        </div>

                        <div class="p-2 rounded-lg bg-gray-50 dark:bg-gray-800" x-show="activeTab === 'Anatomi'">
                            @include('livewire.emr-u-g-d.mr-u-g-d.pemeriksaan.anatomiTab')
                        </div>

                        <div class="p-2 rounded-lg bg-gray-50 dark:bg-gray-800" x-show="activeTab === 'Penunjang'">
                            @include('livewire.emr-u-g-d.mr-u-g-d.pemeriksaan.penunjangTab')
                        </div>

                        <div class="p-2 rounded-lg bg-gray-50 dark:bg-gray-800" x-show="activeTab === 'PenunjangHasil'">
                            @include('livewire.emr-u-g-d.mr-u-g-d.pemeriksaan.penunjangHasilTab')
                        </div>
                    </div>
                </div>

                {{-- <div class="sticky bottom-0 flex justify-between px-4 py-3 bg-gray-50 sm:px-6">
                    <div></div>
                    <div>
                        <div wire:loading wire:target="store">
                            <x-loading />
                        </div>
                        <x-green-button :disabled="$disabledPropertyRjStatus" wire:click.prevent="store()" type="button"
                            wire:loading.remove>
                            Simpan Pemeriksaan
                        </x-green-button>
                    </div>
                </div> --}}
            </div>
        </div>
    @endif
</div>
