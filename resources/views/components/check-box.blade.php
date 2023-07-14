@props(['valuechecked' => 1, 'valueunchecked' => 0, 'label' => 'Label'])

@php
    $value = $valuechecked ? $valueunchecked : $valuechecked;
@endphp


<div class="flex items-center px-4 bg-white border border-gray-300 rounded-lg w-max dark:border-gray-700">
    {{-- input --}}
    <input id="{{ $label }}" type="checkbox" value="{{ $value }}"
        class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-secondary focus:ring-secondary dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">

    {{-- label --}}
    <label for="{{ $label }}" class="w-auto py-3 ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
        {{ $label }}
    </label>

</div>
