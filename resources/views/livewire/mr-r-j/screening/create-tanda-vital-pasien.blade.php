@php
    // $disabledProperty = $isOpenMode === 'tampil' ? true : false;
@endphp

<x-border-form :title="__('Tanda Vital')" :align="__('start')" class="m-1">
    <div id="divTandaVitalPasien">

        @foreach ($dataTandaVital as $key => $tV)
            <form>


                <div class="flex items-center mt-2 bg-gray-50">
                    <x-input-label class="basis-1/2" :value="$tV['tv_label']" />
                    <x-text-input-mou :disabled=$disabledProperty :mou_label="$tV['tv_mou']"
                        wire:model="dataTandaVital.{{ $key }}.tv_value" />
                </div>


            </form>
        @endforeach


    </div>
</x-border-form>
