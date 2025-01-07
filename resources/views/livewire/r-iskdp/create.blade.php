@php
    $disabledProperty = true;

    $disabledPropertyRjStatus = false;

@endphp

<div class="fixed inset-0 z-40">

    <div class="">

        <!-- This element is to trick the browser into transition-opacity. -->
        <div class="fixed inset-0 transition-opacity">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <!-- This element is to trick the browser into transition-opacity. Body-->
        <div class="fixed inset-0 transition-opacity">
            <div class="absolute overflow-auto bg-white rounded-t-lg inset-4">

                {{-- Topbar --}}
                <div
                    class="sticky top-0 flex items-center justify-between p-4 bg-opacity-75 border-b rounded-t-lg bg-primary">

                    <!-- myTitle-->
                    @php
                        $pasieenTitle =
                            'Pasien : (' .
                            $dataPasien['pasien']['regNo'] .
                            ') ' .
                            $dataPasien['pasien']['regName'] .
                            '/' .
                            $dataPasien['pasien']['jenisKelamin']['jenisKelaminDesc'] .
                            '/' .
                            $dataPasien['pasien']['identitas']['alamat'];
                    @endphp
                    <h3 class="w-full text-2xl font-semibold text-white dark:text-white">
                        {{ $pasieenTitle }}
                    </h3>


                    {{-- rjDate & Shift Input Rj --}}
                    <div id="shiftTanggal" class="flex justify-end w-full mr-4">


                        {{-- Close Modal --}}
                        <button wire:click="closeModal()"
                            class="text-gray-400 bg-gray-50 hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>

                    {{-- Pasien --}}





                </div>



                {{-- Transasi --}}
                <form class="scroll-smooth hover:scroll-auto">
                    <div class="grid grid-cols-2">

                        {{-- Transasi Rawat Jalan --}}
                        <div id="TransaksiRawatJalan" class="px-4">

                            <livewire:mr-r-j.skdp-r-i.skdp-r-i :riHdrNoRef="isset($dataDaftarRi['riHdrNo']) ? $dataDaftarRi['riHdrNo'] : '1'"
                                :wire:key="'skdp-r-i-'.$dataDaftarRi['riHdrNo']">

                        </div>



                    </div>

                    <div class="sticky bottom-0 flex justify-between px-4 py-3 bg-gray-50 sm:px-6">

                        <div class="">

                        </div>
                        <div>

                            <x-light-button wire:click="closeModal()" type="button">Keluar</x-light-button>
                        </div>
                    </div>


                </form>

            </div>



        </div>




    </div>

</div>
