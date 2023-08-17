@php
    $disabledProperty = true;
    
    $disabledPropertyRjStatus = false;
    
@endphp


<div class="fixed inset-0 z-40 ease-out duration-400">

    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">

        <!-- This element is to trick the browser into transition-opacity. -->
        <div class="fixed inset-0 transition-opacity">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>



        <!-- This element is to trick the browser into centering the modal contents. -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>​


        <div class="inline-block overflow-auto text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:max-h-[35rem] sm:my-8 sm:align-middle sm:w-11/12"
            role="dialog" aria-modal="true" aria-labelledby="modal-headline">

            <div
                class="sticky top-0 flex items-center justify-between p-4 bg-opacity-75 border-b rounded-t bg-primary dark:border-gray-600">

                <h3 class="w-full text-2xl font-semibold text-white dark:text-white">
                    {{ $myTitle }}
                </h3>


                {{-- Close Modal --}}
                <button wire:click="closeModal()"
                    class="text-gray-400 bg-gray-50 hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd"></path>
                    </svg>
                </button>



            </div>



            <form class="scroll-smooth hover:scroll-auto">
                <div class="grid grid-cols-2">









                    {{-- Transasi Rawat Jalan --}}
                    <div id="TransaksiRawatJalan" class="px-4">
                        <x-border-form :title="__('Jadwal Kontrol')" :align="__('start')" :bgcolor="__('bg-white')" class="mr-0">


                            <div>
                                <x-input-label for="dataDaftarPoliRJ.kontrol.noKontrolRS" :value="__('No Kontrol RS')"
                                    :required="__(true)" />

                                <div class="flex items-center mb-2">
                                    <x-text-input id="dataDaftarPoliRJ.kontrol.noKontrolRS" placeholder="No Kontrol RS"
                                        class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarPoliRJ.kontrol.noKontrolRS'))" :disabled=true
                                        wire:model.debounce.500ms="dataDaftarPoliRJ.kontrol.noKontrolRS" />

                                </div>
                                @error('dataDaftarPoliRJ.kontrol.noKontrolRS')
                                    <x-input-error :messages=$message />
                                @enderror
                            </div>

                            <div>
                                <x-input-label for="dataDaftarPoliRJ.kontrol.noSKDPBPJS" :value="__('No SKDP BPJS ')"
                                    :required="__(false)" />

                                <div class="flex items-center mb-2">
                                    <x-text-input id="dataDaftarPoliRJ.kontrol.noSKDPBPJS" placeholder="No SKDP BPJS "
                                        class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarPoliRJ.kontrol.noSKDPBPJS'))" :disabled=true
                                        wire:model.debounce.500ms="dataDaftarPoliRJ.kontrol.noSKDPBPJS" />

                                </div>
                                @error('dataDaftarPoliRJ.kontrol.noSKDPBPJS')
                                    <x-input-error :messages=$message />
                                @enderror
                            </div>

                            <div>
                                <x-input-label for="dataDaftarPoliRJ.kontrol.noAntrian" :value="__('No Antrian')"
                                    :required="__(false)" />

                                <div class="flex items-center mb-2">
                                    <x-text-input id="dataDaftarPoliRJ.kontrol.noAntrian" placeholder="No Antrian"
                                        class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarPoliRJ.kontrol.noAntrian'))" :disabled=$disabledPropertyRjStatus
                                        wire:model.debounce.500ms="dataDaftarPoliRJ.kontrol.noAntrian" />

                                </div>
                                @error('dataDaftarPoliRJ.kontrol.noAntrian')
                                    <x-input-error :messages=$message />
                                @enderror
                            </div>

                            <div>
                                <x-input-label for="dataDaftarPoliRJ.kontrol.tglKontrol" :value="__('Tgl Kontrol')"
                                    :required="__(true)" />

                                <div class="flex items-center mb-2">
                                    <x-text-input id="dataDaftarPoliRJ.kontrol.tglKontrol"
                                        placeholder="Tgl Kontrol [dd/mm/yyyy]" class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarPoliRJ.kontrol.tglKontrol'))"
                                        :disabled=$disabledPropertyRjStatus
                                        wire:model.debounce.500ms="dataDaftarPoliRJ.kontrol.tglKontrol" />

                                </div>
                                @error('dataDaftarPoliRJ.kontrol.tglKontrol')
                                    <x-input-error :messages=$message />
                                @enderror
                            </div>

                            <div>
                                <x-input-label :value="__('Dokter')" :required="__(true)" />
                                <div class="mt-1 ml-2 ">
                                    <div class="flex ">
                                        <x-text-input placeholder="Dokter" class="sm:rounded-none sm:rounded-l-lg"
                                            :errorshas="__($errors->has('dataDaftarPoliRJ.kontrol.drKontrol'))" :disabled=true
                                            value="{{ $dataDaftarPoliRJ['kontrol']['drKontrol'] . ' / ' . $dataDaftarPoliRJ['kontrol']['drKontrolBPJS'] . ' ' . $dataDaftarPoliRJ['kontrol']['drKontrolDesc'] }}" />
                                        <x-green-button :disabled=$disabledPropertyRjStatus
                                            class="sm:rounded-none sm:rounded-r-lg sm:mb-0 sm:mr-0 sm:px-2"
                                            wire:click.prevent="clickdataDokterlov()">
                                            <svg class="-ml-1 mr-1.5 w-5 h-5" fill="currentColor" viewbox="0 0 20 20"
                                                xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                                <path clip-rule="evenodd" fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                                            </svg>
                                        </x-green-button>
                                    </div>

                                    {{-- <div wire:loading wire:target="dataDokterLovSearch">
                                        <x-loading />
                                    </div> --}}
                                    {{-- LOV Dokter --}}
                                    <div class="mt-2">
                                        @include('livewire.r-jskdp.list-of-value-caridatadokter')
                                    </div>

                                </div>
                                @error('dataDaftarPoliRJ.kontrol.drKontrol')
                                    <x-input-error :messages=$message />
                                @enderror
                            </div>

                            <div>
                                <x-input-label :value="__('Poli')" :required="__(true)" />
                                <div class="flex items-center mb-2">
                                    <x-text-input placeholder="Poli" class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarPoliRJ.kontrol.poliKontrol'))"
                                        :disabled=$disabledProperty
                                        value="{{ $dataDaftarPoliRJ['kontrol']['poliKontrol'] . ' / ' . $dataDaftarPoliRJ['kontrol']['poliKontrolBPJS'] . ' ' . $dataDaftarPoliRJ['kontrol']['poliKontrolDesc'] }}" />
                                </div>
                                @error('dataDaftarPoliRJ.kontrol.poliKontrol')
                                    <x-input-error :messages=$message />
                                @enderror
                            </div>

                            <div>
                                <x-input-label for="dataDaftarPoliRJ.kontrol.catatan" :value="__('Keterangan')"
                                    :required="__(false)" />

                                <div class="flex items-center mb-2">
                                    <x-text-input id="dataDaftarPoliRJ.kontrol.catatan" placeholder="Keterangan"
                                        class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarPoliRJ.kontrol.catatan'))" :disabled=$disabledPropertyRjStatus
                                        wire:model.debounce.500ms="dataDaftarPoliRJ.kontrol.catatan" />

                                </div>
                                @error('dataDaftarPoliRJ.kontrol.catatan')
                                    <x-input-error :messages=$message />
                                @enderror
                            </div>






                        </x-border-form>
                    </div>



                </div>

                <div class="sticky bottom-0 flex justify-between px-4 py-3 bg-gray-50 sm:px-6">

                    <div class="">
                        {{-- <x-primary-button :disabled=$disabledPropertyRjStatus wire:click.prevent="callFormPasien()"
                            type="button" wire:loading.remove>
                            Master Pasien
                        </x-primary-button>
                        <div wire:loading wire:target="callFormPasien">
                            <x-loading />
                        </div> --}}
                    </div>
                    <div>
                        <div wire:loading wire:target="store">
                            <x-loading />
                        </div>

                        <x-green-button :disabled=$disabledPropertyRjStatus wire:click.prevent="store()" type="button"
                            wire:loading.remove>
                            Simpan
                        </x-green-button>
                        <x-light-button wire:click="closeModal()" type="button">Keluar</x-light-button>
                    </div>
                </div>


            </form>

        </div>



    </div>

</div>
