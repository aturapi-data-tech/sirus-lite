@php
    $disabledProperty = true;
    
    $disabledPropertyRjStatus = $statusRjRef['statusId'] !== 'A' ? true : false;
    
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
                <div class="grid grid-cols-1">

                    {{-- Pasien --}}
                    <div id="DataPasien" class="sticky top-0 px-4 py-2 bg-white ">


                        <div class="px-4 bg-white snap-mandatory snap-y">

                            @php
                                $pasieenTitle = 'Pasien RegNo : ' . $dataPasien['pasien']['regNo'] . ' Nomer Pelayanan :' . $dataDaftarPoliRJ['noAntrian'];
                            @endphp

                            <div class="grid grid-cols-2 pl-3 bg-gray-100 rounded-lg">

                                <div>
                                    <div class="text-base font-semibold text-gray-700">
                                        {{ $dataPasien['pasien']['regNo'] }}</div>
                                    <div class="text-2xl font-semibold text-primary">
                                        {{ $dataPasien['pasien']['regName'] . ' / (' . $dataPasien['pasien']['jenisKelamin']['jenisKelaminDesc'] . ')' . ' / ' . $dataPasien['pasien']['thn'] }}
                                    </div>
                                    <div class="font-normal text-gray-900">
                                        {{ $dataPasien['pasien']['identitas']['alamat'] }}
                                    </div>
                                </div>
                                {{--  --}}
                                <div class="grid">
                                    <div class="px-2 font-semibold text-gray-700 justify-self-end">
                                        {{ $dataDaftarPoliRJ['poliDesc'] }}
                                    </div>
                                    <div class="px-2 font-semibold text-primary justify-self-end">
                                        {{ $dataDaftarPoliRJ['drDesc'] . ' / ' }}
                                        {{ $dataDaftarPoliRJ['klaimId'] == 'UM'
                                            ? 'UMUM'
                                            : ($dataDaftarPoliRJ['klaimId'] == 'JM'
                                                ? 'BPJS'
                                                : ($dataDaftarPoliRJ['klaimId'] == 'KR'
                                                    ? 'Kronis'
                                                    : 'Asuransi Lain')) }}
                                    </div>
                                    <div class="px-2 font-normal text-gray-900 justify-self-end">
                                        {{ 'Nomer Pelayanan ' . $dataDaftarPoliRJ['noAntrian'] }}
                                    </div>
                                    <div class="px-2 py-2 text-xs text-gray-700 justify-self-end">
                                        {{ 'Tgl :' . $dataDaftarPoliRJ['rjDate'] }}
                                    </div>
                                </div>
                            </div>




                        </div>



                    </div>







                    {{-- Transasi Rawat Jalan --}}
                    <div id="TransaksiRawatJalan" class="px-4">


                        {{-- call Program --}}
                        @livewire('mr-r-j.screening.screening', [
                            // 'isOpen' => true,
                            // 'isOpenMode' => 'insert',
                            'rjNoRef' => isset($dataDaftarPoliRJ['rjNo']) ? $dataDaftarPoliRJ['rjNo'] : '1',
                        ])


                    </div>



                </div>




            </form>

        </div>



    </div>

</div>