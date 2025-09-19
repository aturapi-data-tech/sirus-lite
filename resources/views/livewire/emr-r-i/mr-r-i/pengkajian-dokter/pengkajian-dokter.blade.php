<div>
    @php
        $disabledProperty = true;
        $disabledPropertyRjStatus = false;
    @endphp

    {{-- Jika data pengkajian dokter ada --}}
    @if (isset($pengkajianDokter))
        <div class="w-full mb-1" x-data="{ activeTab: 'Bagian 1: Anamnesa' }">
            <div id="PengkajianDokter" class="px-2">
                <div id="PengkajianDokter">

                    {{-- Tab Navigation --}}
                    <div class="px-2 mb-2 border-b border-gray-200 dark:border-gray-700">
                        <ul
                            class="flex flex-wrap -mb-px text-xs font-medium text-center text-gray-500 dark:text-gray-400">
                            <li class="mr-2">
                                <label
                                    class="inline-block p-2 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                    :class="activeTab === 'Bagian 1: Anamnesa' ? 'text-primary border-primary bg-gray-100' : ''"
                                    @click="activeTab = 'Bagian 1: Anamnesa'">Bagian 1: Anamnesa</label>
                            </li>
                            <li class="mr-2">
                                <label
                                    class="inline-block p-2 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                    :class="activeTab === 'Bagian 2.1: Pemeriksaan Fisik' ?
                                        'text-primary border-primary bg-gray-100' : ''"
                                    @click="activeTab = 'Bagian 2.1: Pemeriksaan Fisik'">Bagian 2.1: Pemeriksaan
                                    Fisik</label>
                            </li>
                            <li class="mr-2">
                                <label
                                    class="inline-block p-2 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                    :class="activeTab === 'Bagian 2.2: Pemeriksaan Anatomi' ?
                                        'text-primary border-primary bg-gray-100' : ''"
                                    @click="activeTab = 'Bagian 2.2: Pemeriksaan Anatomi'">Bagian 2.2: Pemeriksaan
                                    Anatomi</label>
                            </li>
                            <li class="mr-2">
                                <label
                                    class="inline-block p-2 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                    :class="activeTab === 'Bagian 3: Status Lokalis' ?
                                        'text-primary border-primary bg-gray-100' : ''"
                                    @click="activeTab = 'Bagian 3: Status Lokalis'">Bagian 3: Status Lokalis</label>
                            </li>
                            <li class="mr-2">
                                <label
                                    class="inline-block p-2 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                    :class="activeTab === 'Bagian 4: Hasil Pemeriksaan Penunjang' ?
                                        'text-primary border-primary bg-gray-100' : ''"
                                    @click="activeTab = 'Bagian 4: Hasil Pemeriksaan Penunjang'">Bagian 4: Hasil
                                    Pemeriksaan
                                    Penunjang</label>
                            </li>
                            <li class="mr-2">
                                <label
                                    class="inline-block p-2 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                    :class="activeTab === 'Bagian 5: Diagnosa & Rencana' ?
                                        'text-primary border-primary bg-gray-100' : ''"
                                    @click="activeTab = 'Bagian 5: Diagnosa & Rencana'">Bagian 5: Diagnosa &
                                    Rencana</label>
                            </li>
                            <li class="mr-2">
                                <label
                                    class="inline-block p-2 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                    :class="activeTab === 'Bagian 6: Tanda Tangan Dokter' ?
                                        'text-primary border-primary bg-gray-100' : ''"
                                    @click="activeTab = 'Bagian 6: Tanda Tangan Dokter'">Bagian 6: Tanda Tangan
                                    Dokter</label>
                            </li>
                            <li class="mr-2">
                                <label
                                    class="inline-block p-2 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                    :class="activeTab === 'Bagian 7: Ringkasan Pasien Pulang' ?
                                        'text-primary border-primary bg-gray-100' : ''"
                                    @click="activeTab = 'Bagian 7: Ringkasan Pasien Pulang'">Bagian 7: Ringkasan Pasien
                                    Pulang</label>
                            </li>
                        </ul>
                    </div>

                    {{-- Tab Content --}}
                    <div class="p-2 rounded-lg bg-gray-50" :class="{ 'active': activeTab === 'Bagian 1: Anamnesa' }"
                        x-show.transition.in.opacity.duration.600="activeTab === 'Bagian 1: Anamnesa'">
                        @include('livewire.emr-r-i.mr-r-i.pengkajian-dokter.bagian1-anamnesa')
                    </div>

                    <div class="p-2 rounded-lg bg-gray-50"
                        :class="{ 'active': activeTab === 'Bagian 2.1: Pemeriksaan Fisik' }"
                        x-show.transition.in.opacity.duration.600="activeTab === 'Bagian 2.1: Pemeriksaan Fisik'">
                        @include('livewire.emr-r-i.mr-r-i.pengkajian-dokter.bagian2-1-pemeriksaan-fisik')
                    </div>

                    <div class="p-2 rounded-lg bg-gray-50"
                        :class="{ 'active': activeTab === 'Bagian 2.2: Pemeriksaan Anatomi' }"
                        x-show.transition.in.opacity.duration.600="activeTab === 'Bagian 2.2: Pemeriksaan Anatomi'">
                        @include('livewire.emr-r-i.mr-r-i.pengkajian-dokter.bagian2-2-pemeriksaan-anatomi')
                    </div>

                    <div class="p-2 rounded-lg bg-gray-50"
                        :class="{ 'active': activeTab === 'Bagian 3: Status Lokalis' }"
                        x-show.transition.in.opacity.duration.600="activeTab === 'Bagian 3: Status Lokalis'">
                        @include('livewire.emr-r-i.mr-r-i.pengkajian-dokter.bagian3-status-lokalis')
                    </div>

                    <div class="p-2 rounded-lg bg-gray-50"
                        :class="{ 'active': activeTab === 'Bagian 4: Hasil Pemeriksaan Penunjang' }"
                        x-show.transition.in.opacity.duration.600="activeTab === 'Bagian 4: Hasil Pemeriksaan Penunjang'">
                        @include('livewire.emr-r-i.mr-r-i.pengkajian-dokter.bagian4-hasil-pemeriksaan-penunjang')
                    </div>

                    <div class="p-2 rounded-lg bg-gray-50"
                        :class="{ 'active': activeTab === 'Bagian 5: Diagnosa & Rencana' }"
                        x-show.transition.in.opacity.duration.600="activeTab === 'Bagian 5: Diagnosa & Rencana'">
                        @include('livewire.emr-r-i.mr-r-i.pengkajian-dokter.bagian5-diagnosa-rencana')
                    </div>

                    <div class="p-2 rounded-lg bg-gray-50"
                        :class="{ 'active': activeTab === 'Bagian 6: Tanda Tangan Dokter' }"
                        x-show.transition.in.opacity.duration.600="activeTab === 'Bagian 6: Tanda Tangan Dokter'">
                        @include('livewire.emr-r-i.mr-r-i.pengkajian-dokter.bagian6-tanda-tangan-dokter')
                    </div>

                    <div class="p-2 rounded-lg bg-gray-50"
                        :class="{ 'active': activeTab === 'Bagian 7: Ringkasan Pasien Pulang' }"
                        x-show.transition.in.opacity.duration.600="activeTab === 'Bagian 7: Ringkasan Pasien Pulang'">
                        @include('livewire.emr-r-i.mr-r-i.perencanaan.perencanaan4-ringkasan-pasien-pulang')
                    </div>
                </div>
            </div>

            {{-- Tombol Simpan --}}
            <div class="sticky bottom-0 flex justify-between px-4 py-3 bg-gray-50 sm:px-6"
                x-show="activeTab !== 'Bagian 7: Ringkasan Pasien Pulang'" x-transition.opacity x-cloak>
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
