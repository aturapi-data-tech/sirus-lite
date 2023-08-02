@php
    $disabledProperty = $isOpenMode === 'tampil' ? false : false;
@endphp

<x-border-form :title="__('Antropometri')" :align="__('start')" class="m-1">
    <div id="divTandaVitalPasien">

        @foreach ($dataAntropometri as $key => $aP)
            <form>


                <div class="flex items-center mt-2 bg-gray-50">
                    <x-input-label class="basis-1/2" :value="$aP['ap_label']" />
                    <x-text-input-mou :disabled=$disabledProperty :mou_label="$aP['ap_mou']"
                        wire:model="dataAntropometri.{{ $key }}.ap_value" />
                </div>


            </form>
        @endforeach


    </div>
</x-border-form>
