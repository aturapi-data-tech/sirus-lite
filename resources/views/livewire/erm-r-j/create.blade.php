@php
    $disabledProperty = $isOpenMode === 'tampil' ? true : false;
@endphp

<div class="fixed inset-0 z-40 ease-out duration-400">

    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">

        <!-- This element is to trick the browser into transition-opacity. -->
        <div class="fixed inset-0 transition-opacity">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <!-- This element is to trick the browser into centering the modal contents. -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>​

        {{-- this element is to sizing modal --}}
        <div class="inline-block overflow-auto text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:max-h-[35rem] sm:my-8 sm:align-middle  sm:w-11/12"
            role="dialog" aria-modal="true" aria-labelledby="modal-headline">

            <div
                class="sticky top-0 flex items-start justify-between p-4 bg-white border-b rounded-t dark:border-gray-600">

                <h3 class="text-2xl font-semibold text-gray-900 dark:text-white">
                    {{ $myTitle }}
                </h3>

                {{-- Close Modal --}}
                <button wire:click="closeModal()"
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd"></path>
                    </svg>
                </button>

            </div>


            <form>

                <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">

                    <x-border-form :title="__('Data Pasien')" :align="__('start')">
                        <div id="divTopModule" class="flex">

                            <div id="divDataPasien" class="mr-4 basis-1/2">
                                <div>
                                    <div class="flex items-center">
                                        <x-input-label :value="__('Pasien')" class="basis-1/3" />
                                        <x-text-input class="block sm:w-[100px] mt-1" required autofocus
                                            :disabled=$disabledProperty
                                            wire:model="collectDataScreening.pasien.regNo" />
                                        <x-text-input class="block mt-1 ml-2" required autofocus
                                            :disabled=$disabledProperty
                                            wire:model="collectDataScreening.pasien.regName" />
                                    </div>

                                </div>

                                <div>
                                    <div class="flex items-center">
                                        <x-input-label :value="__('Tgl_Lahir')" class="basis-1/3" />
                                        <x-text-input class="block mt-1" required autofocus :disabled=$disabledProperty
                                            wire:model="collectDataScreening.pasien.regName" />
                                    </div>

                                </div>

                                <div>
                                    <div class="flex items-center">
                                        <x-input-label :value="__('Jenis_Kelamin')" class="basis-1/3" />
                                        <x-text-input class="block mt-1" required autofocus :disabled=$disabledProperty
                                            wire:model="collectDataScreening.pasien.sex" />
                                    </div>

                                </div>

                                <div>
                                    <div class="flex items-center">
                                        <x-input-label :value="__('Alamat')" class="basis-1/3" />
                                        <x-text-input class="block mt-1" required autofocus :disabled=$disabledProperty
                                            wire:model="collectDataScreening.pasien.address" />
                                    </div>

                                </div>
                            </div>



                            <div id="divKetDatangPasien" class="basis-1/2">

                                <div>
                                    <div class="flex items-center">
                                        <x-input-label :value="__('Tgl_Datang')" class="basis-1/3" />
                                        <x-text-input class="block mt-1" required autofocus :disabled=$disabledProperty
                                            wire:model="collectDataScreening.pasien.rjDate" />
                                    </div>

                                </div>

                                <div>
                                    <div class="flex items-center">
                                        <x-input-label :value="__('Jam_Datang')" class="basis-1/3" />
                                        <x-text-input class="block mt-1" required autofocus :disabled=$disabledProperty
                                            wire:model="collectDataScreening.pasien.rjDate" />
                                    </div>

                                </div>

                                <div>
                                    <div class="flex items-center">
                                        <x-input-label :value="__('Asal_Rujukan')" class="basis-1/3" />
                                        <x-text-input class="block mt-1" required autofocus :disabled=$disabledProperty
                                            wire:model="collectDataScreening.pasien.regName" />
                                    </div>

                                </div>
                            </div>

                        </div>
                    </x-border-form>

                    {{-- --------------------------------------------------------------- --}}
                    @include('livewire.erm-r-j.create-screening-pasien')
                    {{-- --------------------------------------------------------------- --}}
                    @include('livewire.erm-r-j.create-screening-kesimpulan')




                </div>


                <div class="sticky bottom-0 px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                    @if ($isOpenMode !== 'tampil')
                        <x-green-button wire:click.prevent="store()" type="button">Simpan</x-green-button>
                    @endif
                    <x-light-button wire:click="closeModal()" type="button">Keluar</x-light-button>
                </div>


            </form>

        </div>



    </div>

</div>
