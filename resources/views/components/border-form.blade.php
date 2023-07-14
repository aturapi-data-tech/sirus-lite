@props(['title' => 'Judul', 'align' => 'start', 'bordercolor' => 'border-gray-300', 'bgcolor' => 'bg-white'])
@php
    if ($align == 'start') {
        $alignAttribute = 'text-start';
    } elseif ($align == 'center') {
        $alignAttribute = 'text-center';
    } else {
        $alignAttribute = 'text-end';
    }
@endphp
<div class="pt-4">
    <h2 class="w-full {{ $alignAttribute }}" style="line-height: 0.1">
        <span class="px-3 text-base font-medium bg-transparent text-primary dark:text-gray-300">
            {{ $title }}
        </span>
    </h2>
    <div
        {{ $attributes->merge(['class' => 'px-5 pt-4 pb-5 border-t border-b border-l border-r ' . $bordercolor . ' ' . $bgcolor . ' dark:border-gray-600 rounded-md']) }}>
        {{ $slot }}
    </div>
</div>
