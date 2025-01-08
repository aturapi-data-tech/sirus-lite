<div>
    @php
        $disabledProperty = true;
        $disabledPropertyRjStatus = false;
    @endphp
    {{-- jika anamnesa kosong ngak usah di render --}}
    @if (isset($dataDaftarRi['anamnesa']))
        <div class="w-full mb-1">

            <div id="TransaksiRawatJalan" class="px-2">
                <div id="TransaksiRawatJalan" x-data="{ activeTab: 'Pengkajian Perawatan' }">

                    <div class="px-2 mb-2 border-b border-gray-200 dark:border-gray-700">
                        <ul
                            class="flex flex-wrap -mb-px text-xs font-medium text-center text-gray-500 dark:text-gray-400">

                            <li class="mr-2">
                                <label
                                    class="inline-block p-2 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                    :class="activeTab === '{{ $dataDaftarRi['anamnesa']['pengkajianPerawatanTab'] }}' ?
                                        'text-primary border-primary bg-gray-100' : ''"
                                    @click="activeTab ='{{ $dataDaftarRi['anamnesa']['pengkajianPerawatanTab'] }}'">{{ $dataDaftarRi['anamnesa']['pengkajianPerawatanTab'] }}</label>
                            </li>


                            <li class="mr-2">
                                <label
                                    class="inline-block p-2 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                    :class="activeTab === '{{ $dataDaftarRi['anamnesa']['keluhanUtamaTab'] }}' ?
                                        'text-primary border-primary bg-gray-100' : ''"
                                    @click="activeTab ='{{ $dataDaftarRi['anamnesa']['keluhanUtamaTab'] }}'">{{ $dataDaftarRi['anamnesa']['keluhanUtamaTab'] }}</label>
                            </li>

                            {{-- <li class="mr-2">
                                <label
                                    class="inline-block p-2 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                    :class="activeTab === '{{ $dataDaftarRi['anamnesa']['anamnesaDiperolehTab'] }}' ?
                                        'text-primary border-primary bg-gray-100' : ''"
                                    @click="activeTab ='{{ $dataDaftarRi['anamnesa']['anamnesaDiperolehTab'] }}'">{{ $dataDaftarRi['anamnesa']['anamnesaDiperolehTab'] }}</label>
                            </li> --}}

                            <li class="mr-2">
                                <label
                                    class="inline-block p-2 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                    :class="activeTab ===
                                        '{{ $dataDaftarRi['anamnesa']['riwayatPenyakitSekarangUmumTab'] }}'
                                        ?
                                        'text-primary border-primary bg-gray-100' : ''"
                                    @click="activeTab ='{{ $dataDaftarRi['anamnesa']['riwayatPenyakitSekarangUmumTab'] }}'">{{ $dataDaftarRi['anamnesa']['riwayatPenyakitSekarangUmumTab'] }}</label>
                            </li>

                            <li class="mr-2">
                                <label
                                    class="inline-block p-2 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                    :class="activeTab === '{{ $dataDaftarRi['anamnesa']['riwayatPenyakitDahuluTab'] }}' ?
                                        'text-primary border-primary bg-gray-100' : ''"
                                    @click="activeTab ='{{ $dataDaftarRi['anamnesa']['riwayatPenyakitDahuluTab'] }}'">{{ $dataDaftarRi['anamnesa']['riwayatPenyakitDahuluTab'] }}</label>
                            </li>

                            {{-- <li class="mr-2">
                                <label
                                    class="inline-block p-2 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                    :class="activeTab === '{{ $dataDaftarRi['anamnesa']['penyakitKeluargaTab'] }}' ?
                                        'text-primary border-primary bg-gray-100' : ''"
                                    @click="activeTab ='{{ $dataDaftarRi['anamnesa']['penyakitKeluargaTab'] }}'">{{ $dataDaftarRi['anamnesa']['penyakitKeluargaTab'] }}</label>
                            </li> --}}

                            {{-- <li class="mr-2">
                                <label
                                    class="inline-block p-2 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                    :class="activeTab === '{{ $dataDaftarRi['anamnesa']['statusFungsionalTab'] }}' ?
                                        'text-primary border-primary bg-gray-100' : ''"
                                    @click="activeTab ='{{ $dataDaftarRi['anamnesa']['statusFungsionalTab'] }}'">{{ $dataDaftarRi['anamnesa']['statusFungsionalTab'] }}</label>
                            </li> --}}

                            <li class="mr-2">
                                <label
                                    class="inline-block p-2 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                    :class="activeTab === '{{ $dataDaftarRi['anamnesa']['statusPsikologisTab'] }}' ?
                                        'text-primary border-primary bg-gray-100' : ''"
                                    @click="activeTab ='{{ $dataDaftarRi['anamnesa']['statusPsikologisTab'] }}'">{{ $dataDaftarRi['anamnesa']['statusPsikologisTab'] }}</label>
                            </li>


                            {{-- <li class="mr-2">
                                <label
                                    class="inline-block p-2 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                    :class="activeTab === '{{ $dataDaftarRi['anamnesa']['edukasiTab'] }}' ?
                                        'text-primary border-primary bg-gray-100' : ''"
                                    @click="activeTab ='{{ $dataDaftarRi['anamnesa']['edukasiTab'] }}'">{{ $dataDaftarRi['anamnesa']['edukasiTab'] }}</label>
                            </li> --}}

                            {{-- <li class="mr-2">
                                <label
                                    class="inline-block p-2 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                    :class="activeTab === '{{ $dataDaftarRi['anamnesa']['screeningGiziTab'] }}' ?
                                        'text-primary border-primary bg-gray-100' : ''"
                                    @click="activeTab ='{{ $dataDaftarRi['anamnesa']['screeningGiziTab'] }}'">{{ $dataDaftarRi['anamnesa']['screeningGiziTab'] }}</label>
                            </li> --}}

                            <li class="mr-2">
                                <label
                                    class="inline-block p-2 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                    :class="activeTab === '{{ $dataDaftarRi['anamnesa']['batukTab'] }}' ?
                                        'text-primary border-primary bg-gray-100' : ''"
                                    @click="activeTab ='{{ $dataDaftarRi['anamnesa']['batukTab'] }}'">{{ $dataDaftarRi['anamnesa']['batukTab'] }}</label>
                            </li>




                        </ul>
                    </div>

                    <div class="p-2 rounded-lg bg-gray-50"
                        :class="{
                            'active': activeTab === '{{ $dataDaftarRi['anamnesa']['pengkajianPerawatanTab'] }}'
                        }"
                        x-show.transition.in.opacity.duration.600="activeTab === '{{ $dataDaftarRi['anamnesa']['pengkajianPerawatanTab'] }}'">
                        @include('livewire.emr-r-i.mr-r-i.anamnesa.pengkajianPerawatanTab')

                    </div>

                    <div class="p-2 rounded-lg bg-gray-50"
                        :class="{
                            'active': activeTab === '{{ $dataDaftarRi['anamnesa']['keluhanUtamaTab'] }}'
                        }"
                        x-show.transition.in.opacity.duration.600="activeTab === '{{ $dataDaftarRi['anamnesa']['keluhanUtamaTab'] }}'">
                        @include('livewire.emr-r-i.mr-r-i.anamnesa.keluhanUtamaTab')

                    </div>

                    <div class="p-2 rounded-lg bg-gray-50"
                        :class="{
                            'active': activeTab === '{{ $dataDaftarRi['anamnesa']['anamnesaDiperolehTab'] }}'
                        }"
                        x-show.transition.in.opacity.duration.600="activeTab === '{{ $dataDaftarRi['anamnesa']['anamnesaDiperolehTab'] }}'">
                        @include('livewire.emr-r-i.mr-r-i.anamnesa.anamnesaDiperolehTab')

                    </div>

                    <div class="p-2 rounded-lg bg-gray-50"
                        :class="{
                            'active': activeTab ===
                                '{{ $dataDaftarRi['anamnesa']['riwayatPenyakitSekarangUmumTab'] }}'
                        }"
                        x-show.transition.in.opacity.duration.600="activeTab === '{{ $dataDaftarRi['anamnesa']['riwayatPenyakitSekarangUmumTab'] }}'">
                        @include('livewire.emr-r-i.mr-r-i.anamnesa.riwayatPenyakitSekarangUmumTab')

                    </div>

                    <div class="p-2 rounded-lg bg-gray-50"
                        :class="{
                            'active': activeTab === '{{ $dataDaftarRi['anamnesa']['riwayatPenyakitDahuluTab'] }}'
                        }"
                        x-show.transition.in.opacity.duration.600="activeTab === '{{ $dataDaftarRi['anamnesa']['riwayatPenyakitDahuluTab'] }}'">
                        @include('livewire.emr-r-i.mr-r-i.anamnesa.riwayatPenyakitDahuluTab')

                    </div>


                    <div class="p-2 rounded-lg bg-gray-50"
                        :class="{
                            'active': activeTab === '{{ $dataDaftarRi['anamnesa']['penyakitKeluargaTab'] }}'
                        }"
                        x-show.transition.in.opacity.duration.600="activeTab === '{{ $dataDaftarRi['anamnesa']['penyakitKeluargaTab'] }}'">
                        @include('livewire.emr-r-i.mr-r-i.anamnesa.penyakitKeluargaTab')

                    </div>

                    <div class="p-2 rounded-lg bg-gray-50"
                        :class="{
                            'active': activeTab === '{{ $dataDaftarRi['anamnesa']['statusFungsionalTab'] }}'
                        }"
                        x-show.transition.in.opacity.duration.600="activeTab === '{{ $dataDaftarRi['anamnesa']['statusFungsionalTab'] }}'">
                        @include('livewire.emr-r-i.mr-r-i.anamnesa.statusFungsionalTab')

                    </div>

                    <div class="p-2 rounded-lg bg-gray-50"
                        :class="{
                            'active': activeTab === '{{ $dataDaftarRi['anamnesa']['statusPsikologisTab'] }}'
                        }"
                        x-show.transition.in.opacity.duration.600="activeTab === '{{ $dataDaftarRi['anamnesa']['statusPsikologisTab'] }}'">
                        @include('livewire.emr-r-i.mr-r-i.anamnesa.statusPsikologisTab')

                    </div>

                    <div class="p-2 rounded-lg bg-gray-50"
                        :class="{
                            'active': activeTab === '{{ $dataDaftarRi['anamnesa']['edukasiTab'] }}'
                        }"
                        x-show.transition.in.opacity.duration.600="activeTab === '{{ $dataDaftarRi['anamnesa']['edukasiTab'] }}'">
                        @include('livewire.emr-r-i.mr-r-i.anamnesa.edukasiTab')

                    </div>

                    <div class="p-2 rounded-lg bg-gray-50"
                        :class="{
                            'active': activeTab === '{{ $dataDaftarRi['anamnesa']['screeningGiziTab'] }}'
                        }"
                        x-show.transition.in.opacity.duration.600="activeTab === '{{ $dataDaftarRi['anamnesa']['screeningGiziTab'] }}'">
                        @include('livewire.emr-r-i.mr-r-i.anamnesa.screeningGiziTab')

                    </div>

                    <div class="p-2 rounded-lg bg-gray-50"
                        :class="{
                            'active': activeTab === '{{ $dataDaftarRi['anamnesa']['batukTab'] }}'
                        }"
                        x-show.transition.in.opacity.duration.600="activeTab === '{{ $dataDaftarRi['anamnesa']['batukTab'] }}'">
                        @include('livewire.emr-r-i.mr-r-i.anamnesa.batukTab')

                    </div>


                </div>
            </div>



            <div class="sticky bottom-0 flex justify-between px-4 py-3 bg-gray-50 sm:px-6">

                <div class="">
                    {{-- null --}}
                </div>
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
