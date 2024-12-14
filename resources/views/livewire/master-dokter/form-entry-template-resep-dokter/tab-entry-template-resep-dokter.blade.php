{{-- Grid Eresep --}}
<div class="grid grid-cols-3 gap-2">

    <div class="col-span-3">
        {{-- Transasi EMR --}}
        <div id="TransaksiEMR" x-data="{ activeTabRacikanNonRacikan: @entangle('activeTabRacikanNonRacikan') }" class="grid grid-cols-1">

            <div class="px-2 mb-0 overflow-auto border-b border-gray-200">
                <ul class="flex flex-row flex-wrap justify-center -mb-px text-sm font-medium text-gray-500 text-start ">
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
                @if ($dokterId && $temprId)
                    <livewire:master-dokter.form-entry-template-resep-dokter.template-resep-dokter.template-resep-dokter
                        :wire:key="'template-resep-dokter-eresep-r-j'" :drIdRef="$dokterId ?? null" :temprIdRef="$temprId ? $temprId : 'xxxxx'">
                @endif
            </div>

            <div class="w-full mx-2 mr-2 rounded-lg bg-gray-50"
                :class="{
                    'active': activeTabRacikanNonRacikan === 'Racikan'
                }"
                x-show.transition.in.opacity.duration.600="activeTabRacikanNonRacikan === 'Racikan'">
                @if ($dokterId && $temprId)
                    <livewire:master-dokter.form-entry-template-resep-dokter.template-resep-dokter.template-resep-dokter-racikan
                        :wire:key="'template-resep-dokter-eresep-r-j-racikan'" :drIdRef="$dokterId ?? null" :temprIdRef="$temprId ? $temprId : 'xxxxx'">
                @endif
            </div>





        </div>
    </div>


</div>
