@props(['disabled' => false, 'mou_label' => 'SetMou'])

@php
    $disabled ? ($class = 'rounded-none rounded-l-lg shadow-sm bg-gray-100 border border-gray-300 text-gray-900 sm:text-sm focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500') : ($class = 'rounded-none rounded-l-lg shadow-sm bg-white border border-gray-300 text-gray-900 sm:text-sm focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-500 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500');
@endphp

<div class='flex'>
    <input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge([
        'class' => $class,
    ]) !!}>
    <input disabled
        class='shadow-sm bg-gray-200 border border-gray-300 text-gray-700  sm:text-sm  focus:ring-primary-500 focus:border-primary-500 block w-20 p-2.5 dark:bg-gray-500 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500 rounded-none rounded-r-lg font-medium text-sm'
        value={{ $mou_label }}>
</div>
