<div>
    <div class="w-full h-[calc(100vh-68px)] bg-white border border-gray-200 px-4 pt-6">
        {{-- Title --}}
        <div class="mb-2">
            <h3 class="text-3xl font-bold text-gray-900">{{ $myTitle }}</h3>
            <span class="text-base font-normal text-gray-700">{{ $mySnipt }}</span>
        </div>
        {{-- Title --}}

        {{-- Top Bar --}}
        <div class="flex justify-between">

            <div class="flex justify-between w-full">
                <x-light-button wire:click="createKelas()" class="flex justify-center" wire:loading.remove>
                    <svg class="w-5 h-5 mr-2 -ml-1" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                            clip-rule="evenodd"></path>
                    </svg>
                    Buat Kelas Kamar
                </x-light-button>

                <x-light-button wire:click="displayKelas()" class="flex justify-center" wire:loading.remove>
                    <svg class="w-5 h-5 mr-2 -ml-1" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                            clip-rule="evenodd"></path>
                    </svg>
                    Tampil Kelas Kamar
                </x-light-button>

                <x-primary-button wire:click="updateKelasAll()" class="flex justify-center" wire:loading.remove>
                    <svg class="w-5 h-5 mr-2 -ml-1" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                            clip-rule="evenodd"></path>
                    </svg>
                    Update Tersedia Kamar
                </x-primary-button>
            </div>


        </div>
        {{-- Top Bar --}}

        {{-- Tampilkan table jika data tersedia, jika tidak tampilkan pesan --}}
        @if (isset($myQueryData['response']['list']) && count($myQueryData['response']['list']) > 0)
            <div class="h-[calc(100vh-250px)] mt-2 overflow-auto">
                <!-- Table -->
                <table class="w-full text-sm text-left text-gray-700 table-auto">
                    <thead class="sticky top-0 text-xs text-gray-900 uppercase bg-gray-100">
                        <tr>
                            <th scope="col" class="w-1/4 px-4 py-3">Kode Ruang</th>
                            <th scope="col" class="w-1/4 px-4 py-3">Nama Ruang</th>
                            <th scope="col" class="w-1/4 px-4 py-3">Kelas</th>
                            <th scope="col" class="w-1/4 px-4 py-3">Action</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white">
                        @foreach ($myQueryData['response']['list'] as $item)
                            <tr class="border-b group dark:border-gray-700">
                                <td class="px-4 py-3 group-hover:bg-gray-100 whitespace-nowrap dark:text-white">
                                    {{ $item['koderuang'] }}
                                </td>
                                <td class="px-4 py-3 group-hover:bg-gray-100 whitespace-nowrap dark:text-white">
                                    {{ $item['namaruang'] }}
                                </td>
                                <td class="px-4 py-3 group-hover:bg-gray-100 whitespace-nowrap dark:text-white">
                                    <div>
                                        <strong>Kelas:</strong> {{ $item['namakelas'] }}
                                    </div>
                                    <div>
                                        <strong>Last Update:</strong> {{ $item['lastupdate'] }}
                                    </div>
                                    <div>
                                        <strong>Tersedia Pria Wanita:</strong> {{ $item['tersediapriawanita'] }}
                                    </div>
                                    <div>
                                        <strong>Tersedia:</strong> {{ $item['tersedia'] }}
                                    </div>
                                    <div>
                                        <strong>Tersedia Wanita:</strong> {{ $item['tersediawanita'] }}
                                    </div>
                                    <div>
                                        <strong>Tersedia Pria:</strong> {{ $item['tersediapria'] }}
                                    </div>
                                    <div>
                                        <strong>Kapasitas:</strong> {{ $item['kapasitas'] }}
                                    </div>
                                    <div>
                                        <strong>Kode Kelas:</strong> {{ $item['kodekelas'] }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 group-hover:bg-gray-100 group-hover:text-primary">
                                    <x-alternative-button class="inline-flex"
                                        wire:click.prevent="removeKelas('{{ $item['kodekelas'] }}','{{ $item['koderuang'] }}')">
                                        <svg class="w-5 h-5 text-gray-800" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                                            <path
                                                d="M17 4h-4V2a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v2H1a1 1 0 0 0 0 2h1v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6h1a1 1 0 1 0 0-2ZM7 2h4v2H7V2Zm1 14a1 1 0 1 1-2 0V8a1 1 0 0 1 2 0v8Zm4 0a1 1 0 0 1-2 0V8a1 1 0 0 1 2 0v8Z" />
                                        </svg>
                                        {{ $item['kodekelas'] }}{{ $item['koderuang'] }}
                                    </x-alternative-button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="w-full p-4 text-sm text-center text-gray-900 dark:text-gray-400">
                {{ 'Data ' . $myProgram . ' Tidak ditemukan' }}
            </div>
        @endif
    </div>
</div>
