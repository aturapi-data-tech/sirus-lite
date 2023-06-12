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

                    <div id="divTopModule" class="flex">

                        <div id="divDataPasien" class="mr-4 basis-1/2">
                            <div>
                                <div class="flex items-center">
                                    <x-input-label for="reg_no" :value="__('Pasien')" class="basis-1/3" />
                                    <x-text-input class="block sm:w-[100px] mt-1" required autofocus
                                        autocomplete="reg_no" :disabled=$disabledProperty
                                        wire:model="dataPasienPoli.regNo" />
                                    <x-text-input class="block mt-1 ml-2" required autofocus autocomplete="reg_name"
                                        :disabled=$disabledProperty wire:model="dataPasienPoli.regName" />
                                </div>
                                @error('reg_no')
                                    <span class="text-red-500">{{ $message }}</span>
                                @enderror
                                @error('reg_name')
                                    <span class="text-red-500">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <div class="flex items-center">
                                    <x-input-label for="reg_no" :value="__('Tgl_Lahir')" class="basis-1/3" />
                                    <x-text-input class="block mt-1" required autofocus autocomplete="reg_name"
                                        :disabled=$disabledProperty wire:model="dataPasienPoli.regName" />
                                </div>
                                @error('reg_no')
                                    <span class="text-red-500">{{ $message }}</span>
                                @enderror
                                @error('reg_name')
                                    <span class="text-red-500">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <div class="flex items-center">
                                    <x-input-label for="reg_no" :value="__('Jenis_Kelamin')" class="basis-1/3" />
                                    <x-text-input class="block mt-1" required autofocus autocomplete="reg_name"
                                        :disabled=$disabledProperty wire:model="dataPasienPoli.regName" />
                                </div>
                                @error('reg_no')
                                    <span class="text-red-500">{{ $message }}</span>
                                @enderror
                                @error('reg_name')
                                    <span class="text-red-500">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <div class="flex items-center">
                                    <x-input-label for="reg_no" :value="__('Alamat')" class="basis-1/3" />
                                    <x-text-input class="block mt-1" required autofocus autocomplete="reg_name"
                                        :disabled=$disabledProperty wire:model="dataPasienPoli.regName" />
                                </div>
                                @error('reg_no')
                                    <span class="text-red-500">{{ $message }}</span>
                                @enderror
                                @error('reg_name')
                                    <span class="text-red-500">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>



                        <div id="divKetDatangPasien" class="basis-1/2">

                            <div>
                                <div class="flex items-center">
                                    <x-input-label for="reg_no" :value="__('Tgl_Datang')" class="basis-1/3" />
                                    <x-text-input class="block mt-1" required autofocus autocomplete="reg_name"
                                        :disabled=$disabledProperty wire:model="dataPasienPoli.regName" />
                                </div>
                                @error('reg_no')
                                    <span class="text-red-500">{{ $message }}</span>
                                @enderror
                                @error('reg_name')
                                    <span class="text-red-500">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <div class="flex items-center">
                                    <x-input-label for="reg_no" :value="__('Jam_Datang')" class="basis-1/3" />
                                    <x-text-input class="block mt-1" required autofocus autocomplete="reg_name"
                                        :disabled=$disabledProperty wire:model="dataPasienPoli.regName" />
                                </div>
                                @error('reg_no')
                                    <span class="text-red-500">{{ $message }}</span>
                                @enderror
                                @error('reg_name')
                                    <span class="text-red-500">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <div class="flex items-center">
                                    <x-input-label for="reg_no" :value="__('Asal_Rujukan')" class="basis-1/3" />
                                    <x-text-input class="block mt-1" required autofocus autocomplete="reg_name"
                                        :disabled=$disabledProperty wire:model="dataPasienPoli.regName" />
                                </div>
                                @error('reg_no')
                                    <span class="text-red-500">{{ $message }}</span>
                                @enderror
                                @error('reg_name')
                                    <span class="text-red-500">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                    </div>

                    {{-- --------------------------------------------------------------- --}}

                    <div id="divScreeningPasien" class="flex flex-col w-full m-10 ">
                        <div>
                            <div class="flex items-center mt-2">
                                <x-input-label for="reg_no" class="basis-1/3" :value="__('Apakah_Pasien_Sadar?')" />
                                <div
                                    class="flex items-center pl-4 mr-4 border border-gray-200 rounded dark:border-gray-700 hover:bg-gray-100">
                                    <input id="klaimTypeUM" type="radio" value="UM" wire:model="klaimType"
                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="klaimTypeUM"
                                        class="w-full py-3 pr-4 ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">YA</label>
                                </div>
                                <div
                                    class="flex items-center pl-4 border border-gray-200 rounded dark:border-gray-700 hover:bg-gray-100">
                                    <input id="klaimTypeJM" type="radio" value="JM" wire:model="klaimType"
                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="klaimTypeJM"
                                        class="w-full py-3 pr-4 ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">TIDAK</label>
                                </div>
                            </div>
                            @error('reg_no')
                                <span class="text-red-500">{{ $message }}</span>
                            @enderror
                            @error('reg_name')
                                <span class="text-red-500">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <div class="flex items-center mt-2">
                                <x-input-label for="reg_no" class="basis-1/3" :value="__('Apakah_Pasien_Sesak_Nafas?')" />
                                <div
                                    class="flex items-center pl-4 mr-4 border border-gray-200 rounded dark:border-gray-700 hover:bg-gray-100">
                                    <input id="klaimTypeUM" type="radio" value="UM" wire:model="klaimType"
                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="klaimTypeUM"
                                        class="w-full py-3 pr-4 ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">YA</label>
                                </div>
                                <div
                                    class="flex items-center pl-4 border border-gray-200 rounded dark:border-gray-700 hover:bg-gray-100">
                                    <input id="klaimTypeJM" type="radio" value="JM" wire:model="klaimType"
                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="klaimTypeJM"
                                        class="w-full py-3 pr-4 ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">TIDAK</label>
                                </div>
                            </div>
                            @error('reg_no')
                                <span class="text-red-500">{{ $message }}</span>
                            @enderror
                            @error('reg_name')
                                <span class="text-red-500">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <div class="flex items-center mt-2">
                                <x-input-label for="reg_no" class="basis-1/3" :value="__('Apakah_Pasien_Beresiko_Jatuh?')" />
                                <div
                                    class="flex items-center pl-4 mr-4 border border-gray-200 rounded dark:border-gray-700 hover:bg-gray-100">
                                    <input id="klaimTypeUM" type="radio" value="UM" wire:model="klaimType"
                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="klaimTypeUM"
                                        class="w-full py-3 pr-4 ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">YA</label>
                                </div>
                                <div
                                    class="flex items-center pl-4 border border-gray-200 rounded dark:border-gray-700 hover:bg-gray-100">
                                    <input id="klaimTypeJM" type="radio" value="JM" wire:model="klaimType"
                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="klaimTypeJM"
                                        class="w-full py-3 pr-4 ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">TIDAK</label>
                                </div>
                            </div>
                            @error('reg_no')
                                <span class="text-red-500">{{ $message }}</span>
                            @enderror
                            @error('reg_name')
                                <span class="text-red-500">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <div class="flex items-center mt-2">
                                <x-input-label for="reg_no" class="basis-1/3" :value="__('Apakah_Pasien_Nyeri_Dada?')" />
                                <div
                                    class="flex items-center pl-4 mr-4 border border-gray-200 rounded dark:border-gray-700 hover:bg-gray-100">
                                    <input id="klaimTypeUM" type="radio" value="UM" wire:model="klaimType"
                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="klaimTypeUM"
                                        class="w-full py-3 pr-4 ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">YA</label>
                                </div>
                                <div
                                    class="flex items-center pl-4 border border-gray-200 rounded dark:border-gray-700 hover:bg-gray-100">
                                    <input id="klaimTypeJM" type="radio" value="JM" wire:model="klaimType"
                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="klaimTypeJM"
                                        class="w-full py-3 pr-4 ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">TIDAK</label>
                                </div>
                            </div>
                            @error('reg_no')
                                <span class="text-red-500">{{ $message }}</span>
                            @enderror
                            @error('reg_name')
                                <span class="text-red-500">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <div class="flex items-center mt-2">
                                <x-input-label for="reg_no" class="basis-1/3" :value="__('Apakah_Pasien_Batuk_Selama_2_Minggu_Terakhir?')" />
                                <div
                                    class="flex items-center pl-4 mr-4 border border-gray-200 rounded dark:border-gray-700 hover:bg-gray-100">
                                    <input id="klaimTypeUM" type="radio" value="UM" wire:model="klaimType"
                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="klaimTypeUM"
                                        class="w-full py-3 pr-4 ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">YA</label>
                                </div>
                                <div
                                    class="flex items-center pl-4 border border-gray-200 rounded dark:border-gray-700 hover:bg-gray-100">
                                    <input id="klaimTypeJM" type="radio" value="JM" wire:model="klaimType"
                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="klaimTypeJM"
                                        class="w-full py-3 pr-4 ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">TIDAK</label>
                                </div>
                            </div>
                            @error('reg_no')
                                <span class="text-red-500">{{ $message }}</span>
                            @enderror
                            @error('reg_name')
                                <span class="text-red-500">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <div class="">
                                <img src="pain_scale_level.jpg" class="object-fill h-auto w-1/2 ...">
                            </div>
                            <div class="flex items-center mt-2">
                                <x-input-label for="reg_no" class="basis-1/3" :value="__('Skala_Nyeri_Pasien?')" />

                                <div
                                    class="flex items-center pl-4 mr-4 border border-gray-200 rounded dark:border-gray-700 hover:bg-gray-100">
                                    <input id="klaimTypeUM" type="radio" value="UM" wire:model="klaimType"
                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="klaimTypeUM"
                                        class="w-full py-3 pr-4 ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">1</label>
                                </div>
                                <div
                                    class="flex items-center pl-4 mr-4 border border-gray-200 rounded dark:border-gray-700 hover:bg-gray-100">
                                    <input id="klaimTypeUM" type="radio" value="UM" wire:model="klaimType"
                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="klaimTypeUM"
                                        class="w-full py-3 pr-4 ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">2-3</label>
                                </div>
                                <div
                                    class="flex items-center pl-4 mr-4 border border-gray-200 rounded dark:border-gray-700 hover:bg-gray-100">
                                    <input id="klaimTypeJM" type="radio" value="JM" wire:model="klaimType"
                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="klaimTypeJM"
                                        class="w-full py-3 pr-4 ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">4-7</label>
                                </div>
                                <div
                                    class="flex items-center pl-4 border border-gray-200 rounded dark:border-gray-700 hover:bg-gray-100">
                                    <input id="klaimTypeJM" type="radio" value="JM" wire:model="klaimType"
                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="klaimTypeJM"
                                        class="w-full py-3 pr-4 ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">8-10</label>
                                </div>

                            </div>
                            @error('reg_no')
                                <span class="text-red-500">{{ $message }}</span>
                            @enderror
                            @error('reg_name')
                                <span class="text-red-500">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>
                    {{-- --------------------------------------------------------------- --}}

                    <div id="divKesimpulan" class="h-24 border border-blue-500 rounded-lg">
                        <div>
                            <div class="flex items-center mt-2">
                                <x-input-label for="reg_no" class="basis-1/3" :value="__('KESIMPULAN')" />
                                <div
                                    class="flex items-center pl-4 mr-4 border border-gray-200 rounded dark:border-gray-700 hover:bg-gray-100">
                                    <input id="klaimTypeUM" type="radio" value="UM" wire:model="klaimType"
                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="klaimTypeUM"
                                        class="w-full py-3 pr-4 ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">SESUAI_ANTRIAN</label>
                                </div>
                                <div
                                    class="flex items-center pl-4 mr-4 border border-gray-200 rounded dark:border-gray-700 hover:bg-gray-100">
                                    <input id="klaimTypeUM" type="radio" value="UM" wire:model="klaimType"
                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="klaimTypeUM"
                                        class="w-full py-3 pr-4 ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">DISEGERAKAN</label>
                                </div>
                                <div
                                    class="flex items-center pl-4 border border-gray-200 rounded dark:border-gray-700 hover:bg-gray-100">
                                    <input id="klaimTypeJM" type="radio" value="JM" wire:model="klaimType"
                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="klaimTypeJM"
                                        class="w-full py-3 pr-4 ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">IGD</label>
                                </div>
                            </div>
                            @error('reg_no')
                                <span class="text-red-500">{{ $message }}</span>
                            @enderror
                            @error('reg_name')
                                <span class="text-red-500">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>



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
