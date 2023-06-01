<div class="px-4 pt-6">

    <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
        <!-- Card header -->



        <div class="w-full mb-1">

            <div class="">
                {{-- text --}}

                <div class="mb-5">
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $myTitle }}</h3>
                    <span class="text-base font-normal text-gray-500 dark:text-gray-400">{{ $mySnipt }}</span>
                </div>
                {{-- end text --}}

                <div class="md:flex md:justify-between">
                    {{-- search --}}
                    <div class="relative pointer-events-auto dark:bg-slate-900 md:w-1/2">
                        <div class="absolute inset-y-0 left-0 flex items-center p-5 pl-3 pointer-events-none">
                            <svg width="24" height="24" fill="none" aria-hidden="true" class="flex-none mr-3">
                                <path d="m19 19-3.5-3.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"></path>
                                <circle cx="11" cy="11" r="6" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></circle>
                            </svg>
                        </div>
                        <x-text-input id="simpleSearch" name="namesimpleSearch" type="text" class="p-2 pl-10"
                            autofocus autocomplete="simpleSearch" placeholder="Cari Data" wire:model.lazy="search" />
                    </div>
                    {{-- end search --}}


                    {{-- two button --}}
                    <div class="flex justify-between mt-2 md:mt-0">
                        <x-primary-button wire:click="create()" class="flex justify-center flex-auto">
                            <svg class="w-5 h-5 mr-2 -ml-1" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            Tambah Data {{ $myProgram }}
                        </x-primary-button>

                        <x-dropdown align="right" width="48" class="">
                            <x-slot name="trigger" class="">
                                <x-alternative-button class="inline-flex">
                                    <svg class="-ml-1 mr-1.5 w-5 h-5" fill="currentColor" viewbox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path clip-rule="evenodd" fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                                    </svg>
                                    Tampil ({{ $limitPerPage }})
                                </x-alternative-button>
                            </x-slot>

                            {{-- Open user menu content --}}
                            <x-slot name="content">

                                @foreach ($myLimitPerPages as $myLimitPerPage)
                                    <x-dropdown-link wire:click="changeLimitPerPage({{ $myLimitPerPage }})">
                                        {{ __($myLimitPerPage) }}
                                    </x-dropdown-link>
                                @endforeach


                            </x-slot>

                        </x-dropdown>
                    </div>
                    {{-- end two button --}}

                </div>




                @if ($isOpen)
                    @include('livewire.post.create')
                @endif

            </div>



            <!-- Table -->
            <div class="flex flex-col mt-6">
                <div class="overflow-x-auto rounded-lg">
                    <div class="inline-block min-w-full align-middle">
                        <div class="overflow-hidden shadow sm:rounded-lg">
                            <table class="w-full text-sm text-left text-gray-500 table-auto dark:text-gray-400">
                                <thead
                                    class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-4 py-3">Id</th>
                                        <th scope="col" class="px-4 py-3">Title</th>
                                        <th scope="col" class="px-4 py-3">Body</th>
                                        <th scope="col" class="w-8 px-4 py-3 text-center">Alat
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800">


                                    @foreach ($posts as $post)
                                        <tr class="border-b group dark:border-gray-700">
                                            <th scope="row"
                                                class="px-4 py-3 font-medium text-gray-900 group-hover:bg-gray-100 group-hover:text-blue-700 whitespace-nowrap dark:text-white">
                                                {{ $post->id }}</th>
                                            <td class="px-4 py-3 group-hover:bg-gray-100 group-hover:text-blue-700">
                                                {{ $post->title }}</td>
                                            <td class="px-4 py-3 group-hover:bg-gray-100 group-hover:text-blue-700">
                                                {{ $post->body }}</td>
                                            <td
                                                class="flex items-center justify-center px-4 py-3 group-hover:bg-gray-100 group-hover:text-blue-700">
                                                <x-dropdown align="left" width="48">
                                                    <x-slot name="trigger">
                                                        <button
                                                            class="flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg md:w-auto focus:outline-none hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                                                            type="button">
                                                            <svg class="w-5 h-5" aria-hidden="true" fill="currentColor"
                                                                viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                                            </svg>
                                                    </x-slot>

                                                    {{-- Open user menu content --}}
                                                    <x-slot name="content">

                                                        <x-dropdown-link wire:click="edit({{ $post->id }})">
                                                            {{ __('Tampil | ' . $post->title) }}
                                                        </x-dropdown-link>
                                                        <x-dropdown-link wire:click="edit({{ $post->id }})">
                                                            {{ __('Ubah') }}
                                                        </x-dropdown-link>
                                                        <x-dropdown-link wire:click="delete({{ $post->id }})">
                                                            {{ __('Hapus') }}
                                                        </x-dropdown-link>


                                                    </x-slot>

                                                </x-dropdown>
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>

                            </table>
                            @if ($posts->count() == 0)
                                <div class="w-full p-4 text-sm text-center text-gray-500 dark:text-gray-400">
                                    {{ 'Data ' . $myProgram . ' Tidak ditemukan' }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <!-- Card Footer -->
            <div class="flex items-center justify-end pt-3 sm:pt-6">
                {{-- {{ $posts->links() }} --}}
                {{ $posts->links('vendor.livewire.tailwind') }}
            </div>


        </div>

    </div>
