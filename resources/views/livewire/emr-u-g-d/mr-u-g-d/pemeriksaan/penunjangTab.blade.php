<div>
    <div class="w-full mb-1">


        <div class="pt-0">

            <x-input-label for="dataDaftarUgd.pemeriksaan.penunjang" :value="__('Penunjang')" :required="__(false)"
                class="pt-2 sm:text-xl" />

            <div class="mb-2">
                <x-input-label for="dataDaftarUgd.pemeriksaan.penunjang" :value="__('Pemeriksaan Penunjang Lab / Foto / EKG / Lan-lain')" :required="__(false)" />

                <x-text-input-area id="dataDaftarUgd.pemeriksaan.penunjang" placeholder="Penunjang" class="mt-1 ml-2"
                    :errorshas="__($errors->has('dataDaftarUgd.pemeriksaan.penunjang'))" :disabled=$disabledPropertyRjStatus
                    wire:model.debounce.500ms="dataDaftarUgd.pemeriksaan.penunjang" :rows="__('10')" />
            </div>

            {{-- tab Penunjang --}}

            {{-- <div id="TransaksiRawatJalan" x-data="{ activeTab: 'EEG' }" class="flex">
                <div class="w-[200px] h-80 overflow-auto">

                    <div class="flex px-2 mb-2 border-b border-gray-200 dark:border-gray-700 ">

                        <ul class="inline -mb-px text-xs font-medium text-center text-gray-500 ">
                            <li class="mr-2">
                                <label
                                    class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                    :class="activeTab === 'EEG' ?
                                        'text-primary border-primary bg-gray-100' : ''"
                                    @click="activeTab ='EEG'">{{ 'EEG' }}</label>
                            </li>

                            <li class="mr-2">
                                <label
                                    class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                    :class="activeTab === 'EMG' ?
                                        'text-primary border-primary bg-gray-100' : ''"
                                    @click="activeTab ='EMG'">{{ 'EMG' }}</label>
                            </li>

                            <li class="mr-2">
                                <label
                                    class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                    :class="activeTab === 'ravenTest' ?
                                        'text-primary border-primary bg-gray-100' : ''"
                                    @click="activeTab ='ravenTest'">{{ 'Raven Test' }}</label>
                            </li>

                            <li class="mr-2">
                                <label
                                    class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                    :class="activeTab === 'Ekg' ?
                                        'text-primary border-primary bg-gray-100' : ''"
                                    @click="activeTab ='Ekg'">{{ 'Ekg' }}</label>
                            </li>



                        </ul>

                    </div>

                </div>


                <div class="w-full">

                    <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800"
                        :class="{
                            'active': activeTab === 'EEG'
                        }"
                        x-show.transition.in.opacity.duration.600="activeTab === 'EEG'">

                        @include('livewire.mr-r-j.pemeriksaan.penunjang.eeg')


                    </div>

                    <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800"
                        :class="{
                            'active': activeTab === 'EMG'
                        }"
                        x-show.transition.in.opacity.duration.600="activeTab === 'EMG'">

                        @include('livewire.mr-r-j.pemeriksaan.penunjang.emg')


                    </div>

                    <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800"
                        :class="{
                            'active': activeTab === 'ravenTest'
                        }"
                        x-show.transition.in.opacity.duration.600="activeTab === 'ravenTest'">

                        @include('livewire.mr-r-j.pemeriksaan.penunjang.ravenTest')


                    </div>



                </div>
            </div> --}}



        </div>




    </div>


</div>
