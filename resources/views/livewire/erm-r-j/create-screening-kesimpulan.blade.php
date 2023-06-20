<x-border-form :title="__('Kesimpulan')" :align="__('center')">
    <div id="divKesimpulan" class="">
        <div>
            <div class="flex items-center mt-2">
                @foreach ($collectDataScreening['kesimpulan']['sck_option'] as $key => $scK)
                    <div
                        class="flex items-center pl-4 mr-4 border border-gray-200 rounded basis-full dark:border-gray-700 hover:bg-gray-100">
                        <input id="kesimpulan{{ $key }}" type="radio" value="{{ $scK['option_value'] }}"
                            wire:click="prosesDataKesimpulan(
                            {{ $scK['option_value'] }},
                            '{{ $scK['option_label'] }}')"
                            wire:model="collectDataScreening.kesimpulan.sck_value"
                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        <label for="{{ $scK['option_label'] }}"
                            class="w-full py-3 pr-4 ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                            {{ $scK['option_label'] }}
                        </label>
                    </div>
                @endforeach

            </div>

        </div>
    </div>
</x-border-form>
