<div>



    <div class="px-1 pt-7">
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
            <!-- Card header -->
            <div class="w-full mb-1">

                <div id="TopBarMenuMaster" class="">

                    {{-- text Title --}}
                    <div class="mb-2">
                        <h3 class="text-2xl font-bold text-midnight dark:text-white">{{ $myTitle }}</h3>
                        <span class="text-base font-normal text-gray-500 dark:text-gray-400">{{ $mySnipt }}</span>
                    </div>
                    {{-- end text Title --}}


                    {{-- Tools --}}
                    <div class="flex justify-between">

                        {{-- search --}}
                        <div class="w-full">
                            <label for="mobile-search" class="sr-only">Search</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <x-text-input type="text" class="pl-10 p-2.5" placeholder="Cari Data"
                                    wire:model.lazy="search" autofocus />

                                <div wire:loading wire:target="search">
                                    <x-loading />
                                </div>

                                {{-- LOV hfis --}}
                                @include('livewire.setup-hfis-bpjs.list-of-value-hfis-bpjs')
                            </div>

                            {{-- date --}}
                            <div class="relative mt-2 ">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg aria-hidden="true" class="w-5 h-5 text-gray-900 dark:text-gray-400"
                                        fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </div>


                                <x-text-input id="dateRjRef" datepicker datepicker-autohide
                                    datepicker-format="dd/mm/yyyy" type="text" class="p-2 pl-10 sm:w-1/6"
                                    placeholder="dd/mm/yyyy" wire:model="dateRjRef" />
                            </div>

                        </div>
                        {{-- end search --}}



                    </div>
                    {{-- End Tools --}}


                    @if ($isOpen)
                        @include('livewire.master-level-satu.create')
                    @endif



                </div>



                <!-- Table -->
                <div class="flex flex-col mt-6">
                    <div class="overflow-x-auto rounded-lg">
                        <div class="inline-block min-w-full align-middle">
                            <div class="overflow-hidden shadow sm:rounded-lg">
                                <table class="w-full text-sm text-left text-gray-500 table-auto dark:text-gray-400">
                                    <thead
                                        class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                                        <tr>
                                            <th scope="col" class="w-2/12 px-4 py-3">
                                                @if ($sortField == 'hfs_id')
                                                    <x-sort-link :active=true wire:click.prevent="sortBy('hfs_id')"
                                                        role="button" href="#">
                                                        Kode Provinsi
                                                    </x-sort-link>
                                                @else
                                                    <x-sort-link :active=false wire:click.prevent="sortBy('hfs_id')"
                                                        role="button" href="#">
                                                        Kode Provinsi
                                                    </x-sort-link>
                                                @endif
                                            </th>
                                            <th scope="col" class="px-4 py-3">

                                                @if ($sortField == 'name')
                                                    <x-sort-link :active=true wire:click.prevent="sortBy('name')"
                                                        role="button" href="#">
                                                        Nama Kota
                                                    </x-sort-link>
                                                @else
                                                    <x-sort-link :active=false wire:click.prevent="sortBy('name')"
                                                        role="button" href="#">
                                                        Nama Kota
                                                    </x-sort-link>
                                                @endif
                                            </th>




                                            <th scope="col" class="w-8 px-4 py-3 text-center">Action
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800">


                                        @foreach ($hfis as $hfs)
                                            <tr class="border-b group dark:border-gray-700">
                                                <th scope="row"
                                                    class="px-4 py-3 font-medium text-gray-700 group-hover:bg-gray-50 group-hover:text-blue-700 whitespace-nowrap dark:text-white">
                                                    {{ $hfs->id }}</th>
                                                <td
                                                    class="px-4 py-3 text-gray-700 group-hover:bg-gray-50 group-hover:text-blue-700">
                                                    {{ $hfs->name }}</td>


                                                <td
                                                    class="flex items-center justify-center px-4 py-3 group-hover:bg-gray-50 group-hover:text-blue-700">



                                                    <!-- Dropdown Action menu Flowbite-->
                                                    <div>
                                                        <x-light-button id="dropdownButton{{ $hfs->id }}"
                                                            class="inline-flex"
                                                            wire:click="$emit('pressDropdownButton','{{ $hfs->id }}')">
                                                            <svg class="w-5 h-5" aria-hidden="true" fill="currentColor"
                                                                viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                                            </svg>
                                                        </x-light-button>

                                                        <!-- Dropdown Action Open menu -->
                                                        <div id="dropdownMenu{{ $hfs->id }}"
                                                            class="z-10 hidden w-auto bg-white divide-y divide-gray-100 rounded-lg shadow dark:bg-gray-700">
                                                            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200"
                                                                aria-labelledby="dropdownButton{{ $hfs->id }}">
                                                                <li>
                                                                    <x-dropdown-link
                                                                        wire:click="tampil('{{ $hfs->id }}')">
                                                                        {{ __('Tampil | ' . $hfs->name) }}
                                                                    </x-dropdown-link>
                                                                </li>
                                                                <li>
                                                                    <x-dropdown-link
                                                                        wire:click="edit('{{ $hfs->id }}')">
                                                                        {{ __('Ubah') }}
                                                                    </x-dropdown-link>
                                                                </li>
                                                                <li>
                                                                    <x-dropdown-link
                                                                        wire:click="$emit('confirm_remove_record', '{{ $hfs->id }}', '{{ $hfs->name }}')">
                                                                        {{ __('Hapus') }}
                                                                    </x-dropdown-link>
                                                                </li>

                                                            </ul>
                                                        </div>
                                                    </div>
                                                    <!-- End Dropdown Action Open menu -->





                                                </td>
                                            </tr>
                                        @endforeach



                                    </tbody>
                                </table>



                                {{-- no data found start --}}
                                @if ($hfis->count() == 0)
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
                {{-- <div class="flex items-center justify-end pt-3 sm:pt-6"> --}}
                {{-- {{ $hfis->links() }} --}}
                {{-- {{ $hfis->links('vendor.livewire.tailwind') }} --}}
                {{-- </div> --}}
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
                    window.livewire.emit('confirm_remove_record_hfs', key, name);
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
