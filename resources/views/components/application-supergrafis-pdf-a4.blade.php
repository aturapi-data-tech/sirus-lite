{{-- resources/views/components/application-supergrafis-pdf-a4.blade.php --}}
@props([
    // opsional: kalau mau override judul dari luar
    'title' => null,
])

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">

    <title>{{ $title ?? 'Dokumen PDF A4' }}</title>

    {{-- pakai bundle Tailwind sirus (samakan path dengan project-mu) --}}
    <link href="{{ public_path('build/assets/sirus.css') }}" rel="stylesheet" />

    <style>
        @page {
            size: A4;
            margin: 20px;
        }

        body {
            font-size: 11px;
        }

        .pdf-background {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 0;
            pointer-events: none;
            /* biar ga ganggu konten */
        }

        .pdf-content {
            position: relative;
            z-index: 10;
        }

        .ornamen.kiri-atas {
            position: fixed;
            top: -30px;
            left: -30px;
            width: 150px;
        }

        .ornamen.kanan-bawah {
            position: fixed;
            bottom: -40px;
            right: -40px;
            width: 240px;
            transform: rotate(180deg);
        }

        .watermark {
            position: fixed;
            top: 190px;
            left: 120px;
            width: 420px;
            opacity: 0.15;
        }
    </style>


</head>

<body class="text-gray-900">

    {{-- BACKGROUND LAYER (repeat every page) --}}
    <div class="pdf-background">
        <img src="{{ public_path('bulansabit.png') }}" class="ornamen kiri-atas" alt="ornamen-kiri">
        {{-- <img src="{{ public_path('logogram.png') }}" class="watermark" alt="watermark"> --}}
        <img src="{{ public_path('bulansabit.png') }}" class="ornamen kanan-bawah" alt="ornamen-kanan">
    </div>

    {{-- CONTENT LAYER --}}
    <div class="pdf-content">
        <x-application-logo-header-identitas />

        <div class="mt-3 mb-2 text-sm font-bold text-center underline">
            {{ $title ?? 'Dokumen PDF A4' }}
        </div>

        {{ $slot }}
    </div>

</body>

</html>
