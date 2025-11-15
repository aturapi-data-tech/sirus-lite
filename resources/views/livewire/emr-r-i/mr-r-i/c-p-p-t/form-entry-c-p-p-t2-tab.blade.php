<div>

    <div class="w-full mb-1">
        <div id="cppt-tab" class="px-2">
            <div id="cppt-tab" x-data="{ activeTab: 'CPPT Dokter' }">

                {{-- Tab Navigation --}}
                <div class="px-2 mb-2 border-b border-gray-200 dark:border-gray-700">
                    <ul class="flex flex-wrap -mb-px text-xs font-medium text-center text-gray-500 dark:text-gray-400">
                        <li class="mr-2">
                            <label
                                class="inline-block p-2 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                :class="activeTab === 'CPPT Dokter' ? 'text-primary border-primary bg-gray-100' : ''"
                                @click="activeTab = 'CPPT Dokter'">CPPT Dokter
                            </label>
                        </li>
                        <li class="mr-2">
                            <label
                                class="inline-block p-2 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                :class="activeTab === 'CPPT Perawat' ?
                                    'text-primary border-primary bg-gray-100' : ''"
                                @click="activeTab = 'CPPT Perawat'">CPPT Perawat
                            </label>
                        </li>
                        <li class="mr-2">
                            <label
                                class="inline-block p-2 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                :class="activeTab === 'CPPT Apoteker' ?
                                    'text-primary border-primary bg-gray-100' : ''"
                                @click="activeTab = 'CPPT Apoteker'">CPPT Apoteker
                            </label>
                        </li>
                        <li class="mr-2">
                            <label
                                class="inline-block p-2 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                :class="activeTab === 'CPPT Penunjang' ?
                                    'text-primary border-primary bg-gray-100' : ''"
                                @click="activeTab = 'CPPT Penunjang'">CPPT Penunjang
                            </label>
                        </li>
                    </ul>
                </div>

                {{-- Tab Content --}}
                <div class="p-2 rounded-lg bg-gray-50" :class="{ 'active': activeTab === 'CPPT Dokter' }"
                    x-show.transition.in.opacity.duration.600="activeTab === 'CPPT Dokter'">
                    @include('livewire.emr-r-i.mr-r-i.c-p-p-t.form-entry-c-p-p-t3-table-dokter')
                </div>

                <div class="p-2 rounded-lg bg-gray-50" :class="{ 'active': activeTab === 'CPPT Perawat' }"
                    x-show.transition.in.opacity.duration.600="activeTab === 'CPPT Perawat'">
                    @include('livewire.emr-r-i.mr-r-i.c-p-p-t.form-entry-c-p-p-t4-table-perawat')
                </div>

                <div class="p-2 rounded-lg bg-gray-50" :class="{ 'active': activeTab === 'CPPT Apoteker' }"
                    x-show.transition.in.opacity.duration.600="activeTab === 'CPPT Apoteker'">
                    @include('livewire.emr-r-i.mr-r-i.c-p-p-t.form-entry-c-p-p-t5-table-apoteker')
                </div>

                <div class="p-2 rounded-lg bg-gray-50" :class="{ 'active': activeTab === 'CPPT Penunjang' }"
                    x-show.transition.in.opacity.duration.600="activeTab === 'CPPT Penunjang'">
                    @include('livewire.emr-r-i.mr-r-i.c-p-p-t.form-entry-c-p-p-t6-table-penunjang')
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
                <x-green-button :disabled=false wire:click.prevent="store()" type="button" wire:loading.remove>
                    Simpan
                </x-green-button>
            </div>
        </div> --}}
    </div>
</div>
