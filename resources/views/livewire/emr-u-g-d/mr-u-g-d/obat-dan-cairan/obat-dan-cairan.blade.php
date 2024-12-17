<div>
    @php
        $disabledProperty = true;
        $disabledPropertyRjStatus = false;
    @endphp
    {{-- jika observasi kosong ngak usah di render --}}
    @if (isset($dataDaftarUgd['observasi']['obatDanCairan']))
        <div class="w-full mb-1">

            <div id="TransaksiRawatJalan" class="px-2">
                <div id="TransaksiRawatJalan" x-data="{ activeTab: false }">

                    <div class="px-2 mb-2 border-b border-gray-200 dark:border-gray-700">
                        <ul
                            class="flex flex-wrap -mb-px text-xs font-medium text-center text-gray-500 dark:text-gray-400">

                            <li class="mr-2">
                                <label
                                    class="inline-block p-2 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                    :class="activeTab === '{{ $dataDaftarUgd['observasi']['obatDanCairan']['pemberianObatDanCairanTab'] }}'
                                        ?
                                        'text-primary border-primary bg-gray-100' : ''"
                                    @click="activeTab =!activeTab?'{{ $dataDaftarUgd['observasi']['obatDanCairan']['pemberianObatDanCairanTab'] }}':false">{{ $dataDaftarUgd['observasi']['obatDanCairan']['pemberianObatDanCairanTab'] }}</label>
                            </li>
                        </ul>
                    </div>

                    <div class="p-2 rounded-lg bg-gray-50"
                        :class="{
                            'active': activeTab === '{{ $dataDaftarUgd['observasi']['obatDanCairan']['pemberianObatDanCairanTab'] }}'
                        }"
                        x-show.transition.in.opacity.duration.600="activeTab === '{{ $dataDaftarUgd['observasi']['obatDanCairan']['pemberianObatDanCairanTab'] }}'"
                        @click.outside="activeTab=false">
                        @include('livewire.emr-u-g-d.mr-u-g-d.obat-dan-cairan.obat-dan-cairan-tab')

                    </div>

                </div>
            </div>



            {{-- <div class="sticky bottom-0 flex justify-between px-4 py-3 bg-gray-50 sm:px-6">

                <div class="">

                </div>
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
    @endif

</div>
