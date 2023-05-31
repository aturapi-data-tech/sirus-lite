<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Sirus (System Informasi Rumah Sakit dan E-Rekam Medis)</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.6.5/flowbite.min.css" rel="stylesheet" />
    <link rel="icon" href="{{ URL::asset('favicon.ico') }}" type="image/x-icon" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body>

    {{-- Navbar --}}
    <nav class="bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
        <div class="flex flex-wrap items-center justify-between max-w-screen-xl p-4 mx-auto">
            <a href="https://flowbite.com/" class="flex items-center">
                <img src="https://flowbite.com/docs/images/logo.svg" class="h-8 mr-3" alt="Flowbite Logo" />
                <span class="self-center text-2xl font-semibold whitespace-nowrap dark:text-white">SIRus</span>
            </a>
            <button data-collapse-toggle="navbar-default" type="button"
                class="inline-flex items-center p-2 ml-3 text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600"
                aria-controls="navbar-default" aria-expanded="false">
                <span class="sr-only">Open main menu</span>
                <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                        d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                        clip-rule="evenodd"></path>
                </svg>
            </button>
            <div class="hidden w-full md:block md:w-auto" id="navbar-default">
                <ul
                    class="flex flex-col p-4 mt-4 font-medium border border-gray-100 rounded-lg md:p-0 bg-gray-50 md:flex-row md:space-x-8 md:mt-0 md:border-0 md:bg-white dark:bg-gray-800 md:dark:bg-gray-900 dark:border-gray-700">


                    @if (Route::has('login'))

                        @auth
                            <li>
                                <a href="{{ url('/dashboard') }}"
                                    class="block py-2 pl-3 pr-4 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent">Dashboard</a>
                            </li>
                        @else
                            <li>
                                <a href="{{ route('login') }}"
                                    class="block py-2 pl-3 pr-4 text-blue-500 rounded hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent">Login</a>
                            </li>


                            @if (Route::has('register'))
                                <li>
                                    <a href="{{ route('register') }}"
                                        class="block py-2 pl-3 pr-4 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent">Register</a>
                                </li>
                            @endif
                        @endauth
            </div>
            @endif


            </ul>
        </div>
        </div>
    </nav>

    {{-- Hero --}}
    <section class="bg-white dark:bg-gray-900">
        <div class="grid max-w-screen-xl px-4 py-3 mx-auto lg:gap-8 lg:py-4 lg:grid-cols-2 xl:gap-0">
            <div class="mr-auto place-self-center lg:col-span-1">
                <h1
                    class="max-w-2xl mb-4 text-4xl font-extrabold leading-none tracking-tight md:text-5xl xl:text-6xl dark:text-white">
                    Selamat Datang
                </h1>
                <p class="max-w-2xl mb-6 font-light text-gray-500 lg:mb-8 md:text-lg lg:text-xl dark:text-gray-400">
                    SIRus (Sistem Informasi Rumah Sakit dan E-Rekam Medis)
                    <br>
                    <br>
                    Rekam Medis adalah berkas, catatan, dan dokumen tentang pasien yang berisi identitas, pemeriksaan,
                    pengobatan, tindakan medis lain pada sarana pelayanan kesehatan untuk rawat jalan maupun rawat inap
                </p>

                <a href="#"
                    class="flex inline-flex items-center justify-center px-5 py-3 text-base font-medium text-center text-gray-900 border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 dark:text-white dark:border-gray-700 dark:hover:bg-gray-700 dark:focus:ring-gray-800">
                    Speak to Sales
                </a>
                {{-- button --}}
                <div class="pt-2">
                    <x-primary-button>Simpan</x-primary-button>
                    <x-alternative-button>Alternative</x-alternative-button>
                    <x-red-button>Hapus</x-red-button>
                    <x-yellow-button>Edit</x-yellow-button>
                    <x-light-button>Keluar</x-light-button>
                </div>
            </div>

            <div class="lg:mt-0 lg:col-span-1 lg:flex ">
                {{-- @include('welcomeimage') --}}
                <lottie-player src="https://assets10.lottiefiles.com/packages/lf20_Zus25A5BNo.json"
                    background="transparent" speed="1" style="width: 500px; height: 400px;" loop autoplay>
                </lottie-player>
            </div>

        </div>
    </section>


    <footer class="">
        <div class="w-full max-w-screen-xl p-8 mx-auto md:py-3">

            <hr class="my-4 border-gray-200 sm:mx-auto dark:border-gray-700 lg:my-3" />
            <span class="block text-sm text-gray-500 sm:text-center dark:text-gray-400">© 2023 <a
                    href="https://flowbite.com/" class="hover:underline">Flowbite™</a>. All Rights Reserved.</span>


        </div>

    </footer>







    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.6.5/flowbite.min.js"></script>
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
</body>

</html>
