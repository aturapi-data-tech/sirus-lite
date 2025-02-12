@props([
    'align' => 'right', // Alignment dropdown (left, top, right)
    'width' => '48', // Lebar dropdown (dalam satuan Tailwind CSS, misalnya 48 untuk w-48)
    'contentclasses' => 'py-1 bg-white dark:bg-gray-700', // Kelas CSS untuk konten dropdown
])

@php
    // Menentukan kelas alignment berdasarkan prop `align`
    switch ($align) {
        case 'left':
            $alignmentClasses = 'origin-top-left left-0';
            break;
        case 'top':
            $alignmentClasses = 'origin-top';
            break;
        case 'right':
        default:
            $alignmentClasses = 'origin-top-right right-0';
            break;
    }

    // Menentukan lebar dropdown berdasarkan prop `width`
    // Pastikan nilai width valid untuk Tailwind CSS (misalnya 48 untuk w-48)
    $dropdownWidth =
        [
            '48' => 'w-48',
            '64' => 'w-64',
            '96' => 'w-96',
        ][$width] ?? 'w-48'; // Default ke w-48 jika width tidak valid

@endphp

<div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
    <!-- Trigger untuk dropdown -->
    <div @click="open = ! open">
        {{ $trigger }}
    </div>

    <!-- Dropdown Content -->
    <div x-show="open" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute z-50 mt-2 {{ $dropdownWidth }} rounded-md shadow-lg {{ $alignmentClasses }}"
        style="display: none;" @click="open = false">
        <div class="rounded-md ring-1 ring-black ring-opacity-5 max-h-96 overflow-auto {{ $contentclasses }}">
            {{ $content }}
        </div>
    </div>
</div>
