<div>

    @php
        $disabledProperty = true;
        $disabledPropertyRjStatus = false;
    @endphp
    {{-- jika anamnesa kosong ngak usah di render --}}
    @if (isset($dataDaftarUgd['suket']))
        <div class="w-full mb-1">

            {{-- <form class="scroll-smooth hover:scroll-auto"> --}}
            <div class="grid grid-cols-1" x-data="{ activeTab: 'Suket Sehat' }">

                <div id="TransaksiRawatJalan" class="px-2">
                    <div id="TransaksiRawatJalan">

                        <div class="px-2 mb-2 border-b border-gray-200 dark:border-gray-700">
                            <ul
                                class="flex flex-wrap -mb-px text-xs font-medium text-center text-gray-500 dark:text-gray-400">

                                <li class="mr-2">
                                    <label
                                        class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                        :class="activeTab === '{{ $dataDaftarUgd['suket']['suketSehatTab'] }}' ?
                                            'text-primary border-primary bg-gray-100' : ''"
                                        @click="activeTab ='{{ $dataDaftarUgd['suket']['suketSehatTab'] }}'">{{ $dataDaftarUgd['suket']['suketSehatTab'] }}</label>
                                </li>

                                <li class="mr-2">
                                    <label
                                        class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                        :class="activeTab === '{{ $dataDaftarUgd['suket']['suketIstirahatTab'] }}' ?
                                            'text-primary border-primary bg-gray-100' : ''"
                                        @click="activeTab ='{{ $dataDaftarUgd['suket']['suketIstirahatTab'] }}'">{{ $dataDaftarUgd['suket']['suketIstirahatTab'] }}</label>
                                </li>



                            </ul>
                        </div>

                        <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800"
                            :class="{
                                'active': activeTab === '{{ $dataDaftarUgd['suket']['suketSehatTab'] }}'
                            }"
                            x-show.transition.in.opacity.duration.600="activeTab === '{{ $dataDaftarUgd['suket']['suketSehatTab'] }}'">
                            @include('livewire.emr-u-g-d.mr-u-g-d.suket.suketSehatTab')

                        </div>

                        <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800"
                            :class="{
                                'active': activeTab === '{{ $dataDaftarUgd['suket']['suketIstirahatTab'] }}'
                            }"
                            x-show.transition.in.opacity.duration.600="activeTab === '{{ $dataDaftarUgd['suket']['suketIstirahatTab'] }}'">
                            @include('livewire.emr-u-g-d.mr-u-g-d.suket.suketIstirahatTab')

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

        </div>
    @endif

</div>
