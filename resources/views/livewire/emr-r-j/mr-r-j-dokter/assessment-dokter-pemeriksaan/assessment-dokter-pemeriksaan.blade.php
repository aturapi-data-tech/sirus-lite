<div>
    @php
        $disabledProperty = true;

        $disabledPropertyRjStatus = false;

    @endphp

    @if (isset($dataDaftarPoliRJ['pemeriksaan']))
        <div class="w-full mb-1">



            {{-- <form class="scroll-smooth hover:scroll-auto"> --}}
            <div class="grid grid-cols-1">

                <div id="TransaksiRawatJalan" class="p-2">

                    <div class="p-2 rounded-lg bg-gray-50">
                        {{-- @include('livewire.emr-r-j.mr-r-j.anamnesa.pengkajianPerawatanTab') --}}
                        {{-- @include('livewire.emr-r-j.mr-r-j.anamnesa.keluhanUtamaTab') --}}

                        <div>

                            <table class="w-full text-sm table-auto">
                                <tbody>
                                    <tr>
                                        <td class="w-1/2 font-semibold uppercase align-text-top">
                                            {{-- Perawat / Terapis --}}
                                            Perawat / Terapis :
                                        </td>
                                        <td class="w-1/2">
                                            {{ isset($dataDaftarPoliRJ['anamnesa']['pengkajianPerawatan']['perawatPenerima'])
                                                ? ($dataDaftarPoliRJ['anamnesa']['pengkajianPerawatan']['perawatPenerima']
                                                    ? strtoupper($dataDaftarPoliRJ['anamnesa']['pengkajianPerawatan']['perawatPenerima'])
                                                    : 'Perawat Penerima')
                                                : 'Perawat Penerima' }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="w-1/2 font-semibold uppercase">
                                            {{-- Tanda Vital --}}
                                            Tanda Vital :
                                        </td>
                                        <td class="w-1/2">

                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="pr-4 text-end">
                                            TD :
                                        </td>
                                        <td>
                                            {{ isset($dataDaftarPoliRJ['pemeriksaan']['tandaVital']['sistolik'])
                                                ? ($dataDaftarPoliRJ['pemeriksaan']['tandaVital']['sistolik']
                                                    ? $dataDaftarPoliRJ['pemeriksaan']['tandaVital']['sistolik']
                                                    : '-')
                                                : '-' }}
                                            /
                                            {{ isset($dataDaftarPoliRJ['pemeriksaan']['tandaVital']['distolik'])
                                                ? ($dataDaftarPoliRJ['pemeriksaan']['tandaVital']['distolik']
                                                    ? $dataDaftarPoliRJ['pemeriksaan']['tandaVital']['distolik']
                                                    : '-')
                                                : '-' }}
                                            mmhg
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="pr-4 text-end">
                                            Nadi :
                                        </td>
                                        <td>
                                            {{ isset($dataDaftarPoliRJ['pemeriksaan']['tandaVital']['frekuensiNadi'])
                                                ? ($dataDaftarPoliRJ['pemeriksaan']['tandaVital']['frekuensiNadi']
                                                    ? $dataDaftarPoliRJ['pemeriksaan']['tandaVital']['frekuensiNadi']
                                                    : '-')
                                                : '-' }}
                                            x/mnt
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="pr-4 text-end">
                                            Suhu :
                                        </td>
                                        <td>
                                            {{ isset($dataDaftarPoliRJ['pemeriksaan']['tandaVital']['suhu'])
                                                ? ($dataDaftarPoliRJ['pemeriksaan']['tandaVital']['suhu']
                                                    ? $dataDaftarPoliRJ['pemeriksaan']['tandaVital']['suhu']
                                                    : '-')
                                                : '-' }}
                                            °C
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="pr-4 text-end">
                                            Pernafasan :
                                        </td>
                                        <td>
                                            {{ isset($dataDaftarPoliRJ['pemeriksaan']['tandaVital']['frekuensiNafas'])
                                                ? ($dataDaftarPoliRJ['pemeriksaan']['tandaVital']['frekuensiNafas']
                                                    ? $dataDaftarPoliRJ['pemeriksaan']['tandaVital']['frekuensiNafas']
                                                    : '-')
                                                : '-' }}
                                            x/mnt
                                        </td>
                                    </tr>
                                    {{-- <tr>
                                        <td class="pr-4 text-end">
                                            Saturasi O2 :
                                        </td>
                                        <td>
                                            {{ isset($dataDaftarPoliRJ['pemeriksaan']['tandaVital']['saturasiO2'])
                                                ? ($dataDaftarPoliRJ['pemeriksaan']['tandaVital']['saturasiO2']
                                                    ? $dataDaftarPoliRJ['pemeriksaan']['tandaVital']['saturasiO2']
                                                    : '-')
                                                : '-' }}
                                            Saturasi
                                        </td>
                                    </tr> --}}
                                    {{-- <tr>
                                    <td class="pr-4 text-end">
                                        Berat Badan :
                                    </td>
                                    <td>
                                        {{ isset($dataDaftarPoliRJ['pemeriksaan']['nutrisi']['bb'])
                                            ? ($dataDaftarPoliRJ['pemeriksaan']['nutrisi']['bb']
                                                ? $dataDaftarPoliRJ['pemeriksaan']['nutrisi']['bb']
                                                : '-')
                                            : '-' }}
                                        kg
                                    </td>
                                </tr> --}}
                                    <tr>
                                        <td class="pr-4 text-end">
                                            SPO2 :
                                        </td>
                                        <td>
                                            {{ isset($dataDaftarPoliRJ['pemeriksaan']['tandaVital']['spo2'])
                                                ? ($dataDaftarPoliRJ['pemeriksaan']['tandaVital']['spo2']
                                                    ? $dataDaftarPoliRJ['pemeriksaan']['tandaVital']['spo2']
                                                    : '-')
                                                : '-' }}
                                            %
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="pr-4 text-end">
                                            GDA :
                                        </td>
                                        <td>
                                            {{ isset($dataDaftarPoliRJ['pemeriksaan']['tandaVital']['gda'])
                                                ? ($dataDaftarPoliRJ['pemeriksaan']['tandaVital']['gda']
                                                    ? $dataDaftarPoliRJ['pemeriksaan']['tandaVital']['gda']
                                                    : '-')
                                                : '-' }}
                                            mg/dL
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="w-1/2 font-semibold uppercase">
                                            {{-- Nutrisi --}}
                                            Nutrisi :
                                        </td>
                                        <td class="w-1/2">

                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="pr-4 text-end">
                                            Berat Badan :
                                        </td>
                                        <td>
                                            {{ isset($dataDaftarPoliRJ['pemeriksaan']['nutrisi']['bb'])
                                                ? ($dataDaftarPoliRJ['pemeriksaan']['nutrisi']['bb']
                                                    ? $dataDaftarPoliRJ['pemeriksaan']['nutrisi']['bb']
                                                    : '-')
                                                : '-' }}
                                            Kg
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="pr-4 text-end">
                                            Tinggi Badan :
                                        </td>
                                        <td>
                                            {{ isset($dataDaftarPoliRJ['pemeriksaan']['nutrisi']['tb'])
                                                ? ($dataDaftarPoliRJ['pemeriksaan']['nutrisi']['tb']
                                                    ? $dataDaftarPoliRJ['pemeriksaan']['nutrisi']['tb']
                                                    : '-')
                                                : '-' }}
                                            cm
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="pr-4 text-end">
                                            Index Masa Tubuh :
                                        </td>
                                        <td>
                                            {{ isset($dataDaftarPoliRJ['pemeriksaan']['nutrisi']['imt'])
                                                ? ($dataDaftarPoliRJ['pemeriksaan']['nutrisi']['imt']
                                                    ? $dataDaftarPoliRJ['pemeriksaan']['nutrisi']['imt']
                                                    : '-')
                                                : '-' }}
                                            Kg/M2
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="pr-4 text-end">
                                            Lingkar Kepala :
                                        </td>
                                        <td>
                                            {{ isset($dataDaftarPoliRJ['pemeriksaan']['nutrisi']['lk'])
                                                ? ($dataDaftarPoliRJ['pemeriksaan']['nutrisi']['lk']
                                                    ? $dataDaftarPoliRJ['pemeriksaan']['nutrisi']['lk']
                                                    : '-')
                                                : '-' }}
                                            cm
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="pr-4 text-end">
                                            Lingkar Lengan Atas :
                                        </td>
                                        <td>
                                            {{ isset($dataDaftarPoliRJ['pemeriksaan']['nutrisi']['lila'])
                                                ? ($dataDaftarPoliRJ['pemeriksaan']['nutrisi']['lila']
                                                    ? $dataDaftarPoliRJ['pemeriksaan']['nutrisi']['lila']
                                                    : '-')
                                                : '-' }}
                                            cm
                                        </td>
                                    </tr>




                                </tbody>
                            </table>

                        </div>
                        {{-- @include('livewire.emr-r-j.mr-r-j.pemeriksaan.umumTab') --}}
                        @include('livewire.emr-r-j.mr-r-j.pemeriksaan.fisikTab')
                        {{-- @include('livewire.emr-r-j.mr-r-j.pemeriksaan.anatomiTab') --}}
                        @include('livewire.emr-r-j.mr-r-j.pemeriksaan.penunjangTab')

                    </div>

                </div>




                {{-- <div class="sticky bottom-0 flex justify-between px-4 py-3 bg-gray-50 sm:px-6">

                    <div class="">
                    </div>
                    <div>
                        <div wire:loading wire:target="store">
                            <x-loading />
                        </div>

                        <x-green-button :disabled=$disabledPropertyRjStatus wire:click.prevent="store()" type="button"
                            wire:loading.remove>
                            Simpan Objective
                        </x-green-button>
                    </div>
                </div> --}}




            </div>
























            {{-- push start ///////////////////////////////// --}}
            @push('scripts')
                {{-- script start --}}
                <script src="{{ url('assets/js/jquery.min.js') }}"></script>
                <script src="{{ url('assets/plugins/toastr/toastr.min.js') }}"></script>
                <script src="{{ url('assets/flowbite/dist/datepicker.js') }}"></script>

                {{-- script end --}}





                {{-- Disabling enter key for form --}}
                {{-- <script type="text/javascript">
                $(document).on("keydown", "form", function(event) {
                    return event.key != "Enter";
                });
            </script> --}}





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












                    // confirmation cari_Data_Pasien_Tidak_Ditemukan_Confirmation
                    window.livewire.on('cari_Data_Pasien_Tidak_Ditemukan_Confirmation', (msg) => {
                        let cfn = confirm('Data ' + msg +
                            ' tidak ditemuka, apakah anda ingin menambahkan menjadi pasien baru ?');

                        if (cfn) {
                            @this.set('callMasterPasien', true);
                        }
                    });




                    // confirmation rePush_Data_Antrian_Confirmation
                    window.livewire.on('rePush_Data_Antrian_Confirmation', () => {
                        let cfn = confirm('Apakah anda ingin mengulaingi Proses Kirim data Antrian ?');

                        if (cfn) {
                            // emit ke controller
                            window.livewire.emit('rePush_Data_Antrian');
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
                    // $("#dateRjRef").change(function() {
                    //     const datepickerEl = document.getElementById('dateRjRef');
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
    @endif
</div>
