@props(['disabled' => false])

@php
    $disabled ? ($class = 'shadow-sm bg-gray-100 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary dark:focus:border-primary') : ($class = 'shadow-sm bg-white border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5 dark:bg-gray-500 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary dark:focus:border-primary');
@endphp

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge([
    'class' => $class,
]) !!}>
