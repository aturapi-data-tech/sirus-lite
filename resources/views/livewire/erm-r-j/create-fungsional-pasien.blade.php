@php
    $disabledProperty = $isOpenMode === 'tampil' ? false : false;
@endphp

<x-border-form :title="__('Fungsional')" :align="__('start')" class="m-1">
    <div id="divTandaVitalPasien">

        @foreach ($dataFungsional as $key => $fU)
            <form>


                <div class="flex items-center mt-2 bg-gray-50">
                    <x-input-label class="basis-1/2" :value="$fU['fu_label']" />
                    <x-text-input :disabled=$disabledProperty wire:model="dataFungsional.{{ $key }}.fu_value" />
                </div>


            </form>
        @endforeach


    </div>
</x-border-form>
