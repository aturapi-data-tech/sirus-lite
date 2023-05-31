<div>

    <div class="px-4 pt-6">
        <div
            class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
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
                                <svg width="24" height="24" fill="none" aria-hidden="true"
                                    class="flex-none mr-3">
                                    <path d="m19 19-3.5-3.5" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"></path>
                                    <circle cx="11" cy="11" r="6" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></circle>
                                </svg>
                            </div>
                            <x-text-input id="simpleSearch" name="namesimpleSearch" type="text" class="p-2 pl-10"
                                autofocus autocomplete="simpleSearch" placeholder="Cari Data"
                                wire:model.lazy="search" />
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



                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    {{-- Button myLimitPerPage --}}
                                    <x-alternative-button class="inline-flex">
                                        <svg class="-ml-1 mr-1.5 w-5 h-5" fill="currentColor" viewbox="0 0 20 20"
                                            xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                            <path clip-rule="evenodd" fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                                        </svg>
                                        Tampil ({{ $limitPerPage }})
                                    </x-alternative-button>
                                </x-slot>
                                {{-- Open myLimitPerPagecontent --}}
                                <x-slot name="content">

                                    @foreach ($myLimitPerPages as $myLimitPerPage)
                                        <x-dropdown-link wire:click="setLimitPerPage({{ $myLimitPerPage }})">
                                            {{ __($myLimitPerPage) }}
                                        </x-dropdown-link>
                                    @endforeach
                                </x-slot>
                            </x-dropdown>
                        </div>
                        {{-- end two button --}}



                    </div>



                    @if ($isOpen)
                        @include('livewire.master-level-dua.create')
                    @endif
                    @if ($tampilIsOpen)
                        @include('livewire.master-level-dua.tampil')
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
                                            <th scope="col" class="w-2/12 px-4 py-3">
                                                @if ($sortField == 'regencies.id')
                                                    <x-sort-link :active=true
                                                        wire:click.prevent="sortBy('regencies.id')" role="button"
                                                        href="#">
                                                        Kode Provinsi
                                                    </x-sort-link>
                                                @else
                                                    <x-sort-link :active=false
                                                        wire:click.prevent="sortBy('regencies.id')" role="button"
                                                        href="#">
                                                        Kode Provinsi
                                                    </x-sort-link>
                                                @endif
                                            </th>
                                            <th scope="col" class="px-4 py-3">

                                                @if ($sortField == 'regencies.name')
                                                    <x-sort-link :active=true
                                                        wire:click.prevent="sortBy('regencies.name')" role="button"
                                                        href="#">
                                                        Nama Kota
                                                    </x-sort-link>
                                                @else
                                                    <x-sort-link :active=false
                                                        wire:click.prevent="sortBy('regencies.name')" role="button"
                                                        href="#">
                                                        Nama Kota
                                                    </x-sort-link>
                                                @endif
                                            </th>
                                            <th scope="col" class="px-4 py-3">

                                                @if ($sortField == 'provinces.name')
                                                    <x-sort-link :active=true
                                                        wire:click.prevent="sortBy('provinces.name')" role="button"
                                                        href="#">
                                                        Nama Provinsi
                                                    </x-sort-link>
                                                @else
                                                    <x-sort-link :active=false
                                                        wire:click.prevent="sortBy('provinces.name')" role="button"
                                                        href="#">
                                                        Nama Provinsi
                                                    </x-sort-link>
                                                @endif
                                            </th>



                                            <th scope="col" class="w-8 px-4 py-3 text-center">Action
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800">


                                        @foreach ($regencys as $regency)
                                            <tr class="border-b group dark:border-gray-700">
                                                <th scope="row"
                                                    class="px-4 py-3 font-medium text-gray-900 group-hover:bg-gray-100 group-hover:text-blue-700 whitespace-nowrap dark:text-white">
                                                    {{ $regency->id }}</th>
                                                <td class="px-4 py-3 group-hover:bg-gray-100 group-hover:text-blue-700">
                                                    {{ $regency->name }}</td>
                                                <td class="px-4 py-3 group-hover:bg-gray-100 group-hover:text-blue-700">
                                                    {{ $regency->province_name }}</td>

                                                <td
                                                    class="flex items-center justify-center px-4 py-3 group-hover:bg-gray-100 group-hover:text-blue-700">



                                                    <!-- Dropdown Action menu Flowbite-->
                                                    <div>
                                                        <x-light-button id="dropdownButton{{ $regency->id }}"
                                                            class="inline-flex"
                                                            wire:click="$emit('pressDropdownButton','{{ $regency->id }}')">
                                                            <svg class="w-5 h-5" aria-hidden="true"
                                                                fill="currentColor" viewbox="0 0 20 20"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                                            </svg>
                                                        </x-light-button>

                                                        <!-- Dropdown Action Open menu -->
                                                        <div id="dropdownMenu{{ $regency->id }}"
                                                            class="z-10 hidden w-auto bg-white divide-y divide-gray-100 rounded-lg shadow dark:bg-gray-700">
                                                            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200"
                                                                aria-labelledby="dropdownButton{{ $regency->id }}">
                                                                <li>
                                                                    <x-dropdown-link
                                                                        wire:click="tampil('{{ $regency->id }}')">
                                                                        {{ __('Tampil | ' . $regency->name) }}
                                                                    </x-dropdown-link>
                                                                </li>
                                                                <li>
                                                                    <x-dropdown-link
                                                                        wire:click="edit('{{ $regency->id }}')">
                                                                        {{ __('Ubah') }}
                                                                    </x-dropdown-link>
                                                                </li>
                                                                <li>
                                                                    <x-dropdown-link
                                                                        wire:click="$emit('confirm_remove_record', '{{ $regency->id }}', '{{ $regency->name }}')">
                                                                        {{ __('Hapus') }}
                                                                    </x-dropdown-link>
                                                                </li>

                                                            </ul>
                                                        </div>
                                                    </div>
                                                    <!-- End Dropdown Action Open menu -->



                                                    {{-- Dropdown Action menu from Breeze off --}}
                                                    {{-- <div class="">
                                                        <x-dropdown align="right" width="48">
                                                            <x-slot name="trigger">

                                                                <x-light-button>
                                                                    <svg class="w-5 h-5" aria-hidden="true"
                                                                        fill="currentColor" viewbox="0 0 20 20"
                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                        <path
                                                                            d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                                                    </svg>
                                                                </x-light-button>

                                                            </x-slot>

                                                            <x-slot name="content">


                                                                <x-dropdown-link
                                                                    wire:click="tampil({{ $regency->id }})">
                                                                    {{ __('Tampil | ' . $regency->name) }}
                                                                </x-dropdown-link>
                                                                <x-dropdown-link
                                                                    wire:click="edit({{ $regency->id }})">
                                                                    {{ __('Ubah') }}
                                                                </x-dropdown-link>
                                                                <x-dropdown-link
                                                                    wire:click="$emit('confirm_remove_record', {{ $regency->id }}, '{{ $regency->name }}')">
                                                                    {{ __('Hapus') }}
                                                                </x-dropdown-link>


                                                            </x-slot>

                                                        </x-dropdown>
                                                    </div> --}}
                                                    {{-- Dropdown Action menu from Breeze --}}



                                                </td>
                                            </tr>
                                        @endforeach



                                    </tbody>
                                </table>



                                {{-- no data found start --}}
                                @if ($regencys->count() == 0)
                                    <div class="w-full p-4 text-sm text-center text-gray-500 dark:text-gray-400">
                                        {{ 'Data ' . $myProgram . ' Tidak ditemukan' }}
                                    </div>
                                @endif
                                {{-- no data found end --}}



                            </div>
                        </div>
                    </div>
                </div>



                <!-- Pagination start -->
                <div class="flex items-center justify-end pt-3 sm:pt-6">
                    {{-- {{ $regencys->links() }} --}}
                    {{ $regencys->links('vendor.livewire.tailwind') }}
                </div>
                <!-- Pagination end -->



            </div>
        </div>
    </div>




























    {{-- push start ///////////////////////////////// --}}
    @push('scripts')
        {{-- script start --}}
        <script src="{{ url('assets/js/jquery.min.js') }}"></script>
        <script src="{{ url('assets/plugins/toastr/toastr.min.js') }}"></script>
        {{-- script end --}}










        {{-- Global Livewire JavaScript Object start --}}
        <script type="text/javascript">
            window.livewire.on('toastr-success', message => toastr.success(message));
            window.Livewire.on('toastr-info', (message) => {
                toastr.info(message)
            });
            window.livewire.on('toastr-error', message => toastr.error(message));













            // confirmation message remove record
            window.livewire.on('confirm_remove_record', (key, name) => {

                let cfn = confirm('Apakah anda ingin menghapus data ini ' + name + '?');

                if (cfn) {
                    window.livewire.emit('confirm_remove_record_regency', key, name);
                }
            });
















            // press_dropdownButton flowbite
            window.Livewire.on('pressDropdownButton', (key) => {
                // set the dropdown menu element
                const $targetEl = document.getElementById('dropdownMenu' + key);

                // set the element that trigger the dropdown menu on click
                const $triggerEl = document.getElementById('dropdownButton' + key);

                // options with default values
                const options = {
                    placement: 'left',
                    triggerType: 'click',
                    offsetSkidding: 0,
                    offsetDistance: 10,
                    delay: 300,
                    onHide: () => {
                        console.log('dropdown has been hidden');

                    },
                    onShow: () => {
                        console.log('dropdown has been shown');
                    },
                    onToggle: () => {
                        console.log('dropdown has been toggled');
                    }
                };

                /*
                 * $targetEl: required
                 * $triggerEl: required
                 * options: optional
                 */
                const dropdown = new Dropdown($targetEl, $triggerEl, options);

                dropdown.show();

            });
        </script>
        {{-- Global Livewire JavaScript Object end --}}
    @endpush













    @push('styles')
        {{-- stylesheet start --}}
        <link rel="stylesheet" href="{{ url('assets/plugins/toastr/toastr.min.css') }}">
        {{-- stylesheet end --}}
    @endpush
    {{-- push end --}}
</div>
