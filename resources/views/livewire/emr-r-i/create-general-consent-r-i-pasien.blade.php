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
                    <h3 class="w-full text-2xl font-semibold text-white ">
                        {{ 'Form Persetujuan Pasien' }}
                    </h3>

                    {{-- rjDate & Shift Input Rj --}}
                    <div id="general-conset-pasien-shiftTanggal" class="flex justify-end w-full mr-4">


                        {{-- Close Modal --}}
                        <button wire:click="closeModalGeneralConsentPasienRI()"
                            class="text-gray-400 bg-gray-50 hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
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

                {{-- Display Pasien Componen --}}
                <div class="">
                    <livewire:emr-r-i.display-pasien.display-pasien
                        :wire:key="$regNoRef.'display-pasien-general-consent-pasien'" :riHdrNoRef="$riHdrNoRef">
                </div>


                {{-- Transasi EMR --}}
                <div id="general-conset-pasien-TransaksiEMR" x-data="{ activeTabGeneralConsentPasienRI: @entangle('activeTabGeneralConsentPasienRI') }" class="grid grid-cols-1">

                    <div class="px-2 mb-0 overflow-auto border-b border-gray-200">
                        <ul
                            class="flex flex-row flex-wrap justify-center -mb-px text-sm font-medium text-gray-500 text-start ">
                            @foreach ($EmrMenuGeneralConsentPasienRI as $EmrM)
                                <li wire:key="tab-{{ $EmrM['ermMenuId'] }}" class="mx-1 mr-0 rounded-t-lg "
                                    x-bind:class="activeTabGeneralConsentPasienRI
                                    === '{{ $EmrM['ermMenuId'] }}' ?
                                        'text-primary border-primary bg-gray-100' :
                                        'border border-gray-200'">
                                    <label
                                        class="inline-block p-2 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                        x-on:click="activeTabGeneralConsentPasienRI ='{{ $EmrM['ermMenuId'] }}'"
                                        wire:click="$set('activeTabGeneralConsentPasienRI', '{{ $EmrM['ermMenuId'] }}')">{{ $EmrM['ermMenuName'] }}</label>
                                </li>
                            @endforeach


                        </ul>
                    </div>




                    <div class="w-full mx-2 mr-2 rounded-lg bg-gray-50 "
                        :class="{
                            'active': activeTabGeneralConsentPasienRI === 'generalConsentPasienRI'
                        }"
                        x-show.transition.in.opacity.duration.600="activeTabGeneralConsentPasienRI === 'generalConsentPasienRI'">
                        <livewire:emr-r-i.general-consent-pasien-r-i.general-consent-pasien-r-i
                            :wire:key="'content-generalConsentPasienRI'.$riHdrNoRef" :riHdrNoRef="$riHdrNoRef"
                            :regNoRef="$regNoRef">
                    </div>

                    <div class="w-full mx-2 mr-2 rounded-lg bg-gray-50 "
                        :class="{
                            'active': activeTabGeneralConsentPasienRI === 'informConsentPasienRI'
                        }"
                        x-show.transition.in.opacity.duration.600="activeTabGeneralConsentPasienRI === 'informConsentPasienRI'">
                        <livewire:emr-r-i.inform-consent-pasien-r-i.inform-consent-pasien-r-i
                            :wire:key="'content-informConsentPasienRI'.$riHdrNoRef" :riHdrNoRef="$riHdrNoRef" :regNoRef="$regNoRef">
                    </div>

                </div>

            </div>



        </div>




    </div>

</div>
