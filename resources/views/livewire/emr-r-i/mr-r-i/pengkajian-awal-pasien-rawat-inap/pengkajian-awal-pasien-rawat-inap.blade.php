<div>
    @php
        $disabledProperty = true;
        $disabledPropertyRjStatus = false;
    @endphp

    {{-- Jika data pengkajian awal rawat inap ada --}}
    @if (isset($pengkajianAwalPasienRawatInap))
        <div class="w-full mb-1">
            <div id="PengkajianAwalRawatInap" class="px-2">
                <div id="PengkajianAwalRawatInap" x-data="{ activeTab: 'Bagian 1: Data Umum' }">

                    {{-- Tab Navigation --}}
                    <div class="px-2 mb-2 border-b border-gray-200 dark:border-gray-700">
                        <ul
                            class="flex flex-wrap -mb-px text-xs font-medium text-center text-gray-500 dark:text-gray-400">
                            <li class="mr-2">
                                <label
                                    class="inline-block p-2 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                    :class="activeTab === 'Bagian 1: Data Umum' ? 'text-primary border-primary bg-gray-100' : ''"
                                    @click="activeTab = 'Bagian 1: Data Umum'">Bagian 1: Data Umum</label>
                            </li>
                            <li class="mr-2">
                                <label
                                    class="inline-block p-2 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                    :class="activeTab === 'Bagian 2: Riwayat Pasien' ?
                                        'text-primary border-primary bg-gray-100' : ''"
                                    @click="activeTab = 'Bagian 2: Riwayat Pasien'">Bagian 2: Riwayat Pasien</label>
                            </li>
                            <li class="mr-2">
                                <label
                                    class="inline-block p-2 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                    :class="activeTab === 'Bagian 3: Psikososial dan Ekonomi' ?
                                        'text-primary border-primary bg-gray-100' : ''"
                                    @click="activeTab = 'Bagian 3: Psikososial dan Ekonomi'">Bagian 3: Psikososial dan
                                    Ekonomi</label>
                            </li>
                            <li class="mr-2">
                                <label
                                    class="inline-block p-2 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                    :class="activeTab === 'Bagian 4: Pemeriksaan Fisik' ?
                                        'text-primary border-primary bg-gray-100' : ''"
                                    @click="activeTab = 'Bagian 4: Pemeriksaan Fisik'">Bagian 4: Pemeriksaan
                                    Fisik</label>
                            </li>
                            <li class="mr-2">
                                <label
                                    class="inline-block p-2 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                    :class="activeTab === 'Bagian 5: Catatan dan Tanda Tangan' ?
                                        'text-primary border-primary bg-gray-100' : ''"
                                    @click="activeTab = 'Bagian 5: Catatan dan Tanda Tangan'">Bagian 5: Catatan dan
                                    Tanda Tangan</label>
                            </li>
                        </ul>
                    </div>

                    {{-- Tab Content --}}
                    <div class="p-2 rounded-lg bg-gray-50" :class="{ 'active': activeTab === 'Bagian 1: Data Umum' }"
                        x-show.transition.in.opacity.duration.600="activeTab === 'Bagian 1: Data Umum'">
                        @include('livewire.emr-r-i.mr-r-i.pengkajian-awal-pasien-rawat-inap.bagian1-data-umum')
                    </div>

                    <div class="p-2 rounded-lg bg-gray-50"
                        :class="{ 'active': activeTab === 'Bagian 2: Riwayat Pasien' }"
                        x-show.transition.in.opacity.duration.600="activeTab === 'Bagian 2: Riwayat Pasien'">
                        @include('livewire.emr-r-i.mr-r-i.pengkajian-awal-pasien-rawat-inap.bagian2-riwayat-pasien')
                    </div>

                    <div class="p-2 rounded-lg bg-gray-50"
                        :class="{ 'active': activeTab === 'Bagian 3: Psikososial dan Ekonomi' }"
                        x-show.transition.in.opacity.duration.600="activeTab === 'Bagian 3: Psikososial dan Ekonomi'">
                        @include('livewire.emr-r-i.mr-r-i.pengkajian-awal-pasien-rawat-inap.bagian3-psikososial-ekonomi')

                    </div>

                    <div class="p-2 rounded-lg bg-gray-50"
                        :class="{ 'active': activeTab === 'Bagian 4: Pemeriksaan Fisik' }"
                        x-show.transition.in.opacity.duration.600="activeTab === 'Bagian 4: Pemeriksaan Fisik'">
                        @include('livewire.emr-r-i.mr-r-i.pengkajian-awal-pasien-rawat-inap.bagian4-pemeriksaan-fisik')
                    </div>

                    <div class="p-2 rounded-lg bg-gray-50"
                        :class="{ 'active': activeTab === 'Bagian 5: Catatan dan Tanda Tangan' }"
                        x-show.transition.in.opacity.duration.600="activeTab === 'Bagian 5: Catatan dan Tanda Tangan'">
                        @include('livewire.emr-r-i.mr-r-i.pengkajian-awal-pasien-rawat-inap.bagian5-catatan-tanda-tangan')
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
                    <x-green-button :disabled=false wire:click.prevent="store()" type="button" wire:loading.remove>
                        Simpan
                    </x-green-button>
                </div>
            </div>
        </div>
    @endif
</div>
