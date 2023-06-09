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
                        <div class="relative pointer-events-auto md:w-1/2">
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

                    <div class=" md:flex md:justify-start">

                        {{-- date picker --}}
                        <div class="relative w-full md:w-1/5">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400"
                                    fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </div>


                            <x-text-input id="rjDateRef" datepicker datepicker-autohide datepicker-format="dd/mm/yyyy"
                                type="text" class="p-2 pl-10 " placeholder="dd/mm/yyyy" wire:model="rjDateRef" />
                        </div>
                        {{-- date --}}




                        {{-- radio --}}
                        <div class="flex mt-2 md:w-full md:mt-0 md:ml-4">

                            <div for="refStausA"
                                class="flex items-center w-1/2 pl-4 mr-2 border border-gray-200 rounded-lg md:w-auto md:p-2 h-11 dark:border-gray-700 hover:bg-gray-100">
                                <input id="refStausA" type="radio" value="A" wire:model="ermStatusRef"
                                    class="mr-2 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                <x-input-label for="refStausA" :value="__('Perawatan')" />

                            </div>
                            <div for="refStausL"
                                class="flex items-center w-1/2 pl-4 border border-gray-200 rounded-lg md:w-auto md:p-2 h-11 dark:border-gray-700 hover:bg-gray-100">
                                <input id="refStausL" type="radio" value="L" wire:model="ermStatusRef"
                                    class="mr-2 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                <x-input-label for="refStausL" :value="__('Selesai')" />
                            </div>


                        </div>
                        {{-- radio --}}
                    </div>


                    @if ($isOpen)
                        @include('livewire.erm-r-j.create')
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
                                            <th scope="col" class="px-4 py-3 ">
                                                @if ($sortField == 'RJp_id')
                                                    <x-sort-link :active=true wire:click.prevent="sortBy('RJp_id')"
                                                        role="button" href="#">
                                                        Pasien
                                                    </x-sort-link>
                                                @else
                                                    <x-sort-link :active=false wire:click.prevent="sortBy('RJp_id')"
                                                        role="button" href="#">
                                                        Pasien
                                                    </x-sort-link>
                                                @endif
                                            </th>
                                            <th scope="col" class="px-4 py-3">

                                                @if ($sortField == 'name')
                                                    <x-sort-link :active=true wire:click.prevent="sortBy('name')"
                                                        role="button" href="#">
                                                        TglLahir
                                                    </x-sort-link>
                                                @else
                                                    <x-sort-link :active=false wire:click.prevent="sortBy('name')"
                                                        role="button" href="#">
                                                        TglLahir
                                                    </x-sort-link>
                                                @endif
                                            </th>
                                            <th scope="col" class="px-4 py-3">

                                                @if ($sortField == 'name')
                                                    <x-sort-link :active=true wire:click.prevent="sortBy('name')"
                                                        role="button" href="#">
                                                        SEP
                                                    </x-sort-link>
                                                @else
                                                    <x-sort-link :active=false wire:click.prevent="sortBy('name')"
                                                        role="button" href="#">
                                                        SEP
                                                    </x-sort-link>
                                                @endif
                                            </th>
                                            <th scope="col" class="px-4 py-3">

                                                @if ($sortField == 'name')
                                                    <x-sort-link :active=true wire:click.prevent="sortBy('name')"
                                                        role="button" href="#">
                                                        Poli
                                                    </x-sort-link>
                                                @else
                                                    <x-sort-link :active=false wire:click.prevent="sortBy('name')"
                                                        role="button" href="#">
                                                        Poli
                                                    </x-sort-link>
                                                @endif
                                            </th>
                                            <th scope="col" class="px-4 py-3">

                                                @if ($sortField == 'name')
                                                    <x-sort-link :active=true wire:click.prevent="sortBy('name')"
                                                        role="button" href="#">
                                                        Shift
                                                    </x-sort-link>
                                                @else
                                                    <x-sort-link :active=false wire:click.prevent="sortBy('name')"
                                                        role="button" href="#">
                                                        Shift
                                                    </x-sort-link>
                                                @endif
                                            </th>




                                            <th scope="col" class="w-8 px-4 py-3 text-center">Action
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800">


                                        @foreach ($RJpasiens as $RJp)
                                            <tr class="border-b group dark:border-gray-700">

                                                <td
                                                    class="flex px-4 py-3 font-medium group-hover:bg-gray-100 group-hover:text-blue-700 whitespace-nowrap dark:text-white">
                                                    <img class="w-10 h-10 rounded-full" src="profile-picture-1.jpg"
                                                        alt="Jese image">
                                                    <div class="pl-3">
                                                        <div class="text-base font-semibold text-gray-700">
                                                            {{ $RJp->reg_no }}</div>
                                                        <div class="font-normal text-blue-700">
                                                            {{ $RJp->reg_name . ' / (' . $RJp->sex . ')' . ' / ' . $RJp->thn }}
                                                        </div>
                                                        <div class="font-normal text-gray-500">
                                                            {{ $RJp->address }}
                                                        </div>
                                                    </div>
                                                </td>

                                                <td
                                                    class="px-4 py-3 group-hover:bg-gray-100 whitespace-nowrap dark:text-white">
                                                    {{ $RJp->birth_date }}
                                                </td>
                                                <td
                                                    class="px-4 py-3 group-hover:bg-gray-100 group-hover:text-blue-700 whitespace-nowrap dark:text-white">
                                                    {{ $RJp->vno_sep }}
                                                </td>

                                                <td
                                                    class="px-4 py-3 font-medium text-gray-900 group-hover:bg-gray-100 group-hover:text-blue-700 whitespace-nowrap dark:text-white">
                                                    <div class="">
                                                        <div class="font-normal text-blue-700">{{ $RJp->poli_desc }}
                                                        </div>
                                                        <div class="font-normal text-gray-500">
                                                            {{ $RJp->dr_name . ' / ' . $RJp->klaim_id }}
                                                        </div>
                                                    </div>
                                                </td>

                                                <td
                                                    class="px-4 py-3 group-hover:bg-gray-100 whitespace-nowrap dark:text-white">
                                                    {{ $RJp->shift }}
                                                </td>

                                                <td
                                                    class="px-4 py-3 group-hover:bg-gray-100 group-hover:text-blue-700">



                                                    <!-- Dropdown Action menu Flowbite-->
                                                    <div>
                                                        <x-light-button id="dropdownButton{{ $RJp->rj_no }}"
                                                            class="inline-flex"
                                                            wire:click="$emit('pressDropdownButton','{{ $RJp->rj_no }}')">
                                                            <svg class="w-5 h-5" aria-hidden="true"
                                                                fill="currentColor" viewbox="0 0 20 20"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                                            </svg>
                                                        </x-light-button>

                                                        <!-- Dropdown Action Open menu -->
                                                        <div id="dropdownMenu{{ $RJp->rj_no }}"
                                                            class="z-10 hidden w-auto bg-white divide-y divide-gray-100 rounded-lg shadow dark:bg-gray-700">
                                                            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200"
                                                                aria-labelledby="dropdownButton{{ $RJp->rj_no }}">
                                                                <li>
                                                                    <x-dropdown-link
                                                                        wire:click="tampil('{{ $RJp->rj_no }}')">
                                                                        {{ __('Tampil | ' . $RJp->reg_name) }}
                                                                    </x-dropdown-link>
                                                                </li>
                                                                <li>
                                                                    <x-dropdown-link
                                                                        wire:click="edit('{{ $RJp->rj_no }}')">
                                                                        {{ __('Ubah') }}
                                                                    </x-dropdown-link>
                                                                </li>
                                                                <li>
                                                                    <x-dropdown-link
                                                                        wire:click="$emit('confirm_remove_record', '{{ $RJp->rj_no }}', '{{ $RJp->reg_name }}')">
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
                                @if ($RJpasiens->count() == 0)
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
                    {{-- {{ $RJpasiens->links() }} --}}
                    {{ $RJpasiens->links('vendor.livewire.tailwind') }}
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
        <script src="{{ url('assets/flowbite/dist/datepicker.js') }}"></script>

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
                    window.livewire.emit('confirm_remove_record_RJp', key, name);
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

                }

            );
        </script>
        <script>
            // $("#rjDateRef").change(function() {
            //     const datepickerEl = document.getElementById('rjDateRef');
            //     console.log(datepickerEl);
            // });
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
