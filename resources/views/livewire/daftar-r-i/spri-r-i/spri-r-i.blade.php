<div>

    <div class="w-full mb-1">



        @php
            $disabledProperty = true;

            $disabledPropertyRiStatus = false;

        @endphp



        <form class="scroll-smooth hover:scroll-auto">
            <div class="grid grid-cols-1">





                <div id="TransaksiRawatJalan" class="px-4">
                    <x-border-form :title="__($myTitle)" :align="__('start')" :bgcolor="__('bg-white')" class="mr-0">


                        <div>
                            <div class='grid items-center grid-cols-2 gap-4 mb-2'>
                                <x-input-label for="dataDaftarRi.spri.noKontrolRS" :value="__('No Kontrol RS')" :required="__(true)" />

                                <x-input-label for="dataDaftarRi.spri.noKartu" :value="__('noKartu')" :required="__(false)" />
                            </div>

                            <div class="grid items-center grid-cols-2 gap-2 mb-2">
                                <x-text-input id="dataDaftarRi.spri.noKontrolRS" placeholder="No Kontrol RS"
                                    class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarRi.spri.noKontrolRS'))" :disabled=true
                                    wire:model.debounce.500ms="dataDaftarRi.spri.noKontrolRS" />

                                <x-text-input id="dataDaftarRi.spri.noKartu" placeholder="SEP" class="mt-1 ml-2"
                                    :errorshas="__($errors->has('dataDaftarRi.spri.noKartu'))" :disabled=false
                                    wire:model.debounce.500ms="dataDaftarRi.spri.noKartu" />

                            </div>
                            @error('dataDaftarRi.spri.noKontrolRS')
                                <x-input-error :messages=$message />
                            @enderror
                        </div>

                        <div class="grid grid-cols-4 gap-1">
                            <div class="col-span-2">
                                <x-input-label for="dataDaftarRi.spri.noSPRIBPJS" :value="__('No SPRI BPJS ')"
                                    :required="__(false)" />

                                <div class="flex items-center mb-2">
                                    <x-text-input id="dataDaftarRi.spri.noSPRIBPJS" placeholder="No SPRI BPJS "
                                        class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarRi.spri.noSPRIBPJS'))" :disabled=true
                                        wire:model.debounce.500ms="dataDaftarRi.spri.noSPRIBPJS" />

                                </div>
                                @error('dataDaftarRi.spri.noSPRIBPJS')
                                    <x-input-error :messages=$message />
                                @enderror
                            </div>


                            <div>
                                <x-input-label for="dataDaftarRi.spri.noAntrian" :value="__('No Antrian')"
                                    :required="__(false)" />

                                <div class="flex items-center mb-2">
                                    <x-text-input id="dataDaftarRi.spri.noAntrian" placeholder="No Antrian"
                                        class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarRi.spri.noAntrian'))" :disabled=$disabledPropertyRiStatus
                                        wire:model.debounce.500ms="dataDaftarRi.spri.noAntrian" />

                                </div>
                                @error('dataDaftarRi.spri.noAntrian')
                                    <x-input-error :messages=$message />
                                @enderror
                            </div>

                            <div>
                                <x-input-label for="dataDaftarRi.spri.tglKontrol" :value="__('Tgl Kontrol')"
                                    :required="__(true)" />

                                <div class="flex items-center mb-2">
                                    <x-text-input id="dataDaftarRi.spri.tglKontrol"
                                        placeholder="Tgl Kontrol [dd/mm/yyyy]" class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarRi.spri.tglKontrol'))"
                                        :disabled=$disabledPropertyRiStatus
                                        wire:model.debounce.500ms="dataDaftarRi.spri.tglKontrol" />

                                </div>
                                @error('dataDaftarRi.spri.tglKontrol')
                                    <x-input-error :messages=$message />
                                @enderror
                            </div>
                        </div>

                        <div>
                            <x-input-label :value="__('Dokter')" :required="__(true)" />
                            <div class="mt-1 ml-2 ">
                                <div class="flex ">
                                    <x-text-input placeholder="Dokter" class="sm:rounded-none sm:rounded-l-lg"
                                        :errorshas="__($errors->has('dataDaftarRi.spri.drKontrol'))" :disabled=true
                                        value="{{ $dataDaftarRi['spri']['drKontrol'] . ' / ' . $dataDaftarRi['spri']['drKontrolBPJS'] . ' ' . $dataDaftarRi['spri']['drKontrolDesc'] }}" />
                                    <x-green-button :disabled=$disabledPropertyRiStatus
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
                                    @include('livewire.mr-r-j.skdp-r-i.list-of-value-caridatadokter')
                                </div>

                            </div>
                            @error('dataDaftarRi.spri.drKontrol')
                                <x-input-error :messages=$message />
                            @enderror
                        </div>

                        <div>
                            <x-input-label :value="__('Poli')" :required="__(true)" />
                            <div class="flex items-center mb-2">
                                <x-text-input placeholder="Poli" class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarRi.spri.poliKontrol'))"
                                    :disabled=$disabledProperty
                                    value="{{ $dataDaftarRi['spri']['poliKontrol'] . ' / ' . $dataDaftarRi['spri']['poliKontrolBPJS'] . ' ' . $dataDaftarRi['spri']['poliKontrolDesc'] }}" />
                            </div>
                            @error('dataDaftarRi.spri.poliKontrol')
                                <x-input-error :messages=$message />
                            @enderror
                        </div>

                        <div>
                            <x-input-label for="dataDaftarRi.spri.catatan" :value="__('Keterangan')" :required="__(false)" />

                            <div class="flex items-center mb-2">
                                <x-text-input id="dataDaftarRi.spri.catatan" placeholder="Keterangan" class="mt-1 ml-2"
                                    :errorshas="__($errors->has('dataDaftarRi.spri.catatan'))" :disabled=$disabledPropertyRiStatus
                                    wire:model.debounce.500ms="dataDaftarRi.spri.catatan" />

                            </div>
                            @error('dataDaftarRi.spri.catatan')
                                <x-input-error :messages=$message />
                            @enderror
                        </div>






                    </x-border-form>
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

                    <x-green-button :disabled=$disabledPropertyRiStatus wire:click.prevent="store()" type="button"
                        wire:loading.remove>
                        Simpan
                    </x-green-button>
                </div>
            </div>


        </form>







    </div>
























    {{-- push start ///////////////////////////////// --}}
    @push('scripts')
        {{-- script start --}}
        <script src="{{ url('assets/js/jquery.min.js') }}"></script>
        <script src="{{ url('assets/plugins/toastr/toastr.min.js') }}"></script>
        <script src="{{ url('assets/flowbite/dist/datepicker.js') }}"></script>

        {{-- script end --}}





        {{-- Disabling enter key for form --}}
        <script type="text/javascript">
            $(document).on("keydown", "form", function(event) {
                return event.key != "Enter";
            });
        </script>





        {{-- Global Livewire JavaScript Object start --}}
        <script type="text/javascript">
            window.livewire.on('toastr-success', message => toastr.success(message));
            window.Livewire.on('toastr-info', (message) => {
                toastr.info(message)
            });
            window.livewire.on('toastr-error', message => toastr.error(message));













            // confirmation message remove record
            window.livewire.on('confirm_remove_record', (key, name) => {

                let cfn = confirm('Apakah anda ingin menghapus data ini ' + name + '?');

                if (cfn) {
                    window.livewire.emit('confirm_remove_record_RJp', key, name);
                }
            });












            // confirmation cari_Data_Pasien_Tidak_Ditemukan_Confirmation
            window.livewire.on('cari_Data_Pasien_Tidak_Ditemukan_Confirmation', (msg) => {
                let cfn = confirm('Data ' + msg +
                    ' tidak ditemuka, apakah anda ingin menambahkan menjadi pasien baru ?');

                if (cfn) {
                    @this.set('callMasterPasien', true);
                }
            });




            // confirmation rePush_Data_Antrian_Confirmation
            window.livewire.on('rePush_Data_Antrian_Confirmation', () => {
                let cfn = confirm('Apakah anda ingin mengulaingi Proses Kirim data Antrian ?');

                if (cfn) {
                    // emit ke controller
                    window.livewire.emit('rePush_Data_Antrian');
                }
            });













            // press_dropdownButton flowbite
            window.Livewire.on('pressDropdownButton', (key) => {
                    // set the dropdown menu element
                    const $targetEl = document.getElementById('dropdownMenu' + key);

                    // set the element that trigger the dropdown menu on click
                    const $triggerEl = document.getElementById('dropdownButton' + key);

                    // options with default values
                    const options = {
                        placement: 'left',
                        triggerType: 'click',
                        offsetSkidding: 0,
                        offsetDistance: 10,
                        delay: 300,
                        onHide: () => {
                            console.log('dropdown has been hidden');

                        },
                        onShow: () => {
                            console.log('dropdown has been shown');
                        },
                        onToggle: () => {
                            console.log('dropdown has been toggled');
                        }
                    };

                    /*
                     * $targetEl: required
                     * $triggerEl: required
                     * options: optional
                     */
                    const dropdown = new Dropdown($targetEl, $triggerEl, options);

                    dropdown.show();

                }

            );
        </script>
        <script>
            // $("#dateRjRef").change(function() {
            //     const datepickerEl = document.getElementById('dateRjRef');
            //     console.log(datepickerEl);
            // });
        </script>
        {{-- Global Livewire JavaScript Object end --}}
    @endpush













    @push('styles')
        {{-- stylesheet start --}}
        <link rel="stylesheet" href="{{ url('assets/plugins/toastr/toastr.min.css') }}">

        {{-- stylesheet end --}}
    @endpush
    {{-- push end --}}
</div>
