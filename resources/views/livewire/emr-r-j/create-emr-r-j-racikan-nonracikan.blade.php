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
                        {{ 'Terapi' }}
                    </h3>

                    {{-- rjDate & Shift Input Rj --}}
                    <div id="shiftTanggal" class="flex justify-end w-full mr-4">


                        {{-- Close Modal --}}
                        <button wire:click="closeModalEresepRJ()"
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
                    {{-- Display Pasien --}}
                    {{-- :rjNo="" disi dari emit ListeneropenModalEditRj --}}

                    <livewire:emr-r-j.display-pasien.display-pasien :wire:key="$rjNoRef.'display-pasienEresep'"
                        :rjNoRef="$rjNoRef">

                        {{-- <livewire:emr-r-j.form-entry-r-j.form-entry-r-j :rjNo="$regNo"
                            :wire:key="$regNo.'form-entry-r-j'"> --}}
                        <div class="mx-8">

                            {{-- Status PRB --}}
                            @isset($dataDaftarPoliRJ['statusPRB']['penanggungJawab']['statusPRB'])
                                @if ($dataDaftarPoliRJ['statusPRB']['penanggungJawab']['statusPRB'])
                                    <x-badge :badgecolor="__('dark')">
                                        PRB
                                    </x-badge>
                                @else
                                    {{-- <x-badge :badgecolor="__('dark')">
                                        NonPRB
                                    </x-badge> --}}
                                @endif
                            @else
                                {{-- <x-badge :badgecolor="__('dark')">
                                    NonPRB
                                </x-badge> --}}
                            @endisset
                        </div>

                </div>

                {{-- Grid Eresep --}}
                <div class="grid grid-cols-3 gap-2">

                    <div class="col-span-2">
                        {{-- Transasi EMR --}}
                        <div id="TransaksiEMR" x-data="{ activeTabRacikanNonRacikan: @entangle('activeTabRacikanNonRacikan') }" class="grid grid-cols-1">

                            <div class="px-2 mb-0 overflow-auto border-b border-gray-200">
                                <ul
                                    class="flex flex-row flex-wrap justify-center -mb-px text-sm font-medium text-gray-500 text-start ">
                                    @foreach ($EmrMenuRacikanNonRacikan as $EmrM)
                                        <li wire:key="tab-{{ $EmrM['ermMenuId'] }}" class="mx-1 mr-0 rounded-t-lg"
                                            :class="'{{ $activeTabRacikanNonRacikan }}'
                                            === '{{ $EmrM['ermMenuId'] }}' ?
                                                'text-primary border-primary bg-gray-100' :
                                                'border border-gray-200'">
                                            <label
                                                class="inline-block p-2 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                                x-on:click="activeTabRacikanNonRacikan ='{{ $EmrM['ermMenuId'] }}'"
                                                wire:click="$set('activeTabRacikanNonRacikan', '{{ $EmrM['ermMenuId'] }}')">{{ $EmrM['ermMenuName'] }}</label>
                                        </li>
                                    @endforeach


                                </ul>
                            </div>



                            <div class="w-full mx-2 mr-2 rounded-lg bg-gray-50"
                                :class="{
                                    'active': activeTabRacikanNonRacikan === 'NonRacikan'
                                }"
                                x-show.transition.in.opacity.duration.600="activeTabRacikanNonRacikan === 'NonRacikan'">
                                <livewire:emr-r-j.eresep-r-j.eresep-r-j :wire:key="'eresep-r-j'" :rjNoRef="$rjNoRef">

                            </div>

                            <div class="w-full mx-2 mr-2 rounded-lg bg-gray-50"
                                :class="{
                                    'active': activeTabRacikanNonRacikan === 'Racikan'
                                }"
                                x-show.transition.in.opacity.duration.600="activeTabRacikanNonRacikan === 'Racikan'">
                                <livewire:emr-r-j.eresep-r-j.eresep-r-j-racikan :wire:key="'eresep-r-j-racikan'"
                                    :rjNoRef="$rjNoRef">

                            </div>

                            @isset($myQueryData)
                                @if ($myQueryData->count() > 0)
                                    <div class="w-full mx-2 bg-gray-100 rounded-lg">
                                        <div class="text-lg font-bold text-center text-primary">Template Rersep</div>
                                        <div class="flex justify-center">
                                            @foreach ($myQueryData as $key => $myData)
                                                <div class="col-span-1">
                                                    <x-light-button
                                                        wire:click="copyResepFromTemplate('{{ $rjNoRef }}','{{ $myData->temp_json_nonracikan ?? '{}' }}','{{ $myData->temp_json_racikan ?? '{}' }}')">
                                                        {{ $key + 1 }} {{ $myData->tempr_desc }}
                                                    </x-light-button>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endisset


                        </div>
                    </div>

                    {{-- Resume --}}
                    <div>
                        <livewire:emr.rekam-medis.rekam-medis-display :wire:key="'content-rekamMedisDisplay'"
                            :rjNoRefCopyTo="$rjNoRef" :regNoRef="$dataDaftarPoliRJ['regNo']">
                    </div>

                </div>


                <div class="sticky bottom-0 flex justify-between px-4 py-3 bg-opacity-75 bg-gray-50 sm:px-6">

                    <div class="">

                        <div>
                            <div wire:loading wire:target="setstatusPRB">
                                <x-loading />
                            </div>


                            @isset($dataDaftarPoliRJ['statusPRB']['penanggungJawab']['statusPRB'])
                                @if ($dataDaftarPoliRJ['statusPRB']['penanggungJawab']['statusPRB'])
                                    <x-red-button :disabled=false wire:click.prevent="setstatusPRB()" type="button"
                                        wire:loading.remove>
                                        Set Status NonPRB
                                    </x-red-button>
                                @else
                                    <x-light-button :disabled=false wire:click.prevent="setstatusPRB()" type="button"
                                        wire:loading.remove>
                                        Set Status PRB
                                    </x-light-button>
                                @endif
                            @else
                                <x-light-button :disabled=false wire:click.prevent="setstatusPRB()" type="button"
                                    wire:loading.remove>
                                    Set Status PRB
                                </x-light-button>
                            @endisset
                        </div>

                    </div>
                    <div>
                        <div wire:loading wire:target="simpanTerapi">
                            <x-loading />
                        </div>

                        <x-green-button :disabled=false wire:click.prevent="simpanTerapi()" type="button"
                            wire:loading.remove>
                            Simpan Terapi
                        </x-green-button>
                    </div>
                </div>





            </div>
        </div>
    </div>

</div>
