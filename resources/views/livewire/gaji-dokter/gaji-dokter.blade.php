<div>


    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


    {{-- Start Coding  --}}

    {{-- Canvas
    Main BgColor /
    Size H/W --}}
    <div class="w-full h-[calc(100vh-68px)] bg-white border border-gray-200 px-4 pt-6">

        {{-- Title  --}}
        <div class="mb-2">
            <h3 class="text-3xl font-bold text-gray-900 ">{{ $myTitle }}</h3>
            <span class="text-base font-normal text-gray-700">{{ $mySnipt }}</span>
        </div>
        {{-- Title --}}

        {{-- Top Bar --}}
        <div class="flex justify-between">

            <div class="flex w-full item-end">
                {{-- Cari Data --}}
                <div class="relative w-1/3 py-2 mt-6 mr-2 pointer-events-auto">
                    <div class="absolute inset-y-0 left-0 flex p-5 pl-3 pointer-events-none item-center ">
                        <svg class="w-6 h-6 text-gray-700 dark:text-white" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                            viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 10h16m-8-3V4M7 7V4m10 3V4M5 20h14a1 1 0 0 0 1-1V7a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1Zm3-7h.01v.01H8V13Zm4 0h.01v.01H12V13Zm4 0h.01v.01H16V13Zm-8 4h.01v.01H8V17Zm4 0h.01v.01H12V17Zm4 0h.01v.01H16V17Z" />
                        </svg>
                    </div>

                    <x-text-input type="text" class="w-full p-2 pl-10" placeholder="Cari Data" autofocus
                        wire:model="myTopBar.refBulan" />
                </div>


                {{-- Dokter --}}
                <div class="py-2">
                    {{-- LOV Dokter --}}
                    @if (empty($collectingMyDokter))
                        @include('livewire.component.l-o-v.list-of-value-dokter.list-of-value-dokter')
                    @else
                        <x-input-label for="myTopBar.drName" :value="__('Nama Dokter')" :required="__(true)"
                            wire:click='resetDokter()' />
                        <div>
                            <x-text-input id="myTopBar.drName" placeholder="Nama Dokter" class="mt-1 ml-2"
                                :errorshas="__($errors->has('myTopBar.drName'))" wire:model="myTopBar.drName" :disabled="true" />

                        </div>
                    @endif
                </div>

            </div>



            <div class="flex justify-end w-1/2 pt-8">
                <x-dropdown align="right" :width="__('20')">
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
                            <x-dropdown-link wire:click="$set('limitPerPage', '{{ $myLimitPerPage }}')">
                                {{ __($myLimitPerPage) }}
                            </x-dropdown-link>
                        @endforeach
                    </x-slot>
                </x-dropdown>
            </div>


        </div>
        {{-- Top Bar --}}






        <div class="h-[calc(100vh-250px)] mt-2 overflow-auto">
            <!-- Table -->
            <table class="w-full text-sm text-left text-gray-700 table-auto">
                <thead class="sticky top-0 text-xs text-gray-900 uppercase bg-gray-100 ">
                    <tr>
                        <th class="w-1/5 px-4 py-3 text-left">Dokter</th>
                        <th class="w-1/5 px-4 py-3 text-left">Klaim</th>
                        <th class="w-1/5 px-4 py-3 text-left">Description</th>
                        <th class="w-1/5 px-4 py-3 text-right">Nominal</th>
                    </tr>
                </thead>

                <tbody class="bg-white">
                    @php
                        $currentDoctor = null;
                        $currentDocName = '';
                        $subtotalDoctor = 0;
                        $grandTotal = 0;
                    @endphp

                    @foreach ($myQueryData as $row)
                        {{-- Ketika dokter berubah, cetak subtotal dokter sebelumnya --}}
                        @if ($currentDoctor !== null && $currentDoctor !== $row->dr_id)
                            <tr class="font-semibold bg-gray-200">
                                <td colspan="3" class="px-4 py-3 text-right">
                                    Subtotal Dokter {{ $currentDocName }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    {{ number_format($subtotalDoctor, 0, ',', '.') }}
                                </td>
                            </tr>
                            @php
                                $subtotalDoctor = 0;
                            @endphp
                        @endif

                        {{-- Baris data --}}
                        <tr class="hover:bg-gray-100">
                            <td class="px-4 py-3 whitespace-nowrap">
                                {{ $row->dr_id }} – {{ $row->dr_name }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                {{ $row->klaim_status }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                {{ $row->desc_doc }}
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                {{ number_format($row->doc_nominal, 0, ',', '.') }}
                            </td>
                        </tr>

                        @php
                            // Update tracking
                            $currentDoctor = $row->dr_id;
                            $currentDocName = $row->dr_name;
                            $subtotalDoctor += $row->doc_nominal;
                            $grandTotal += $row->doc_nominal;
                        @endphp
                    @endforeach

                    {{-- Subtotal untuk dokter terakhir --}}
                    @if ($currentDoctor !== null)
                        <tr class="font-semibold bg-gray-200">
                            <td colspan="3" class="px-4 py-3 text-right">
                                Subtotal Dokter {{ $currentDocName }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                {{ number_format($subtotalDoctor, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endif

                    {{-- Grand total --}}
                    <tr class="font-semibold bg-gray-100">
                        <td colspan="3" class="px-4 py-3 text-right">
                            Total Semua Dokter
                        </td>
                        <td class="px-4 py-3 text-right">
                            {{ number_format($grandTotal, 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>

            </table>

            {{-- no data found start --}}
            @if ($myQueryData->count() == 0)
                <div class="w-full p-4 text-sm text-center text-gray-900 dark:text-gray-400">
                    {{ 'Data ' . $myProgram . ' Tidak ditemukan' }}
                </div>
            @endif
            {{-- no data found end --}}

        </div>

        {{-- {{ $myQueryData->links() }} --}}








    </div>



    {{-- Canvas
    Main BgColor /
    Size H/W --}}

    {{-- End Coding --}}




















    {{-- push start ///////////////////////////////// --}}
    @push('scripts')
        {{-- script start --}}
        <script src="{{ url('assets/js/jquery.min.js') }}"></script>
        <script src="{{ url('assets/plugins/toastr/toastr.min.js') }}"></script>
        <script src="{{ url('assets/flowbite/dist/datepicker.js') }}"></script>

        {{-- script end --}}

        {{-- Global Livewire JavaScript Object start --}}
        <script type="text/javascript">
            toastr.options = {
                "closeButton": false,
                "debug": false,
                "newestOnTop": false,
                "progressBar": false,
                "positionClass": "toast-top-left",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            }

            window.livewire.on('toastr-success', message => toastr.success(message));
            window.Livewire.on('toastr-info', (message) => {
                toastr.info(message)
            });
            window.livewire.on('toastr-error', message => toastr.error(message));




            // press_dropdownButton flowbite
            window.Livewire.on('pressDropdownButtonUgd', (key) => {
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

        <script src="assets/js/signature_pad.umd.min.js"></script>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('signaturePad', (value) => ({
                    signaturePadInstance: null,
                    value: value,
                    init() {

                        this.signaturePadInstance = new SignaturePad(this.$refs.signature_canvas, {
                                minWidth: 2,
                                maxWidth: 2,
                                penColor: "rgb(11, 73, 182)"
                            }

                        );
                        this.signaturePadInstance.addEventListener("endStroke", () => {
                            // this.value = this.signaturePadInstance.toDataURL('image/png');signaturePad.toSVG()
                            // https://github.com/aturapi-data-tech/signature_pad
                            // https://gist.github.com/jonneroelofs/a4a372fe4b55c5f9c0679d432f2c0231
                            this.value = this.signaturePadInstance.toSVG();

                            // console.log(this.signaturePadInstance)
                        });
                    },
                }))
            })
        </script>
    @endpush













    @push('styles')
        {{-- stylesheet start --}}
        <link rel="stylesheet" href="{{ url('assets/plugins/toastr/toastr.min.css') }}">

        {{-- stylesheet end --}}
    @endpush
    {{-- push end --}}

</div>
