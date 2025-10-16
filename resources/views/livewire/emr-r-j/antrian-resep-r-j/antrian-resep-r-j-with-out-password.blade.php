<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Antrian Resep RJ</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    @vite(['resources/css/app.css', 'resources/js/app.js']) {{-- Tailwind + app --}}
    @livewireStyles
</head>

<body class="bg-white">

    <div class="flex items-center justify-start my-2 ml-4">
        <x-application-logo class="block w-auto h-16 text-gray-800 fill-current dark:text-gray-200" />
    </div>


    {{-- Panggil komponen Livewire kamu --}}
    <livewire:emr-r-j.antrian-resep-r-j.antrian-resep-r-j />
    @livewireScripts
</body>

</html>
