<div>

    {{-- jika anamnesa kosong ngak usah di render --}}
    <div class="w-full mb-1">

        <div id="TransaksiRawatJalan" class="px-2">
            <div id="TransaksiRawatJalan" x-data="{ activeTabAdministrasi: @entangle('activeTabAdministrasi') }">

                <div class="px-2 mb-2 border-b border-gray-200 dark:border-gray-700">
                    <ul class="flex flex-wrap -mb-px text-xs font-medium text-center text-gray-500 dark:text-gray-400">

                        @foreach ($EmrMenuAdministrasi as $EmrMenu)
                            <li class="mr-2">
                                <label
                                    class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                    x-bind:class="activeTabAdministrasi === '{{ $EmrMenu['ermMenuId'] }}'
                                        ?
                                        'text-primary border-primary bg-gray-100' : ''"
                                    x-on:click="activeTabAdministrasi ='{{ $EmrMenu['ermMenuId'] }}'">{{ $EmrMenu['ermMenuName'] }}</label>
                            </li>
                        @endforeach

                    </ul>
                </div>

                <div class="p-2 rounded-lg bg-gray-50"
                    :class="{
                        'active': activeTabAdministrasi === 'JasaKaryawan'
                    }"
                    x-show.transition.in.opacity.duration.600="activeTabAdministrasi === 'JasaKaryawan'">
                    <livewire:emr-r-j.administrasi-r-j.jasa-karyawan-r-j :wire:key="'content-jasa-karyawan-r-j'"
                        :rjNoRef="$rjNoRef">

                </div>

                <div class="p-2 rounded-lg bg-gray-50"
                    :class="{
                        'active': activeTabAdministrasi === 'JasaDokter'
                    }"
                    x-show.transition.in.opacity.duration.600="activeTabAdministrasi === 'JasaDokter'">
                    <livewire:emr-r-j.administrasi-r-j.jasa-dokter-r-j :wire:key="'content-jasa-dokter-r-j2'"
                        :rjNoRef="$rjNoRef">

                </div>

                <div class="p-2 rounded-lg bg-gray-50"
                    :class="{
                        'active': activeTabAdministrasi === 'JasaMedis'
                    }"
                    x-show.transition.in.opacity.duration.600="activeTabAdministrasi === 'JasaMedis'">
                    <livewire:emr-r-j.administrasi-r-j.jasa-medis-r-j :wire:key="'content-jasa-medis-r-j'"
                        :rjNoRef="$rjNoRef">

                </div>

                <div class="p-2 rounded-lg bg-gray-50"
                    :class="{
                        'active': activeTabAdministrasi === 'LainLain'
                    }"
                    x-show.transition.in.opacity.duration.600="activeTabAdministrasi === 'LainLain'">
                    <livewire:emr-r-j.administrasi-r-j.lain-lain-r-j :wire:key="'content-lain-lain-r-j2'"
                        :rjNoRef="$rjNoRef">

                </div>


            </div>
        </div>



        <div class="sticky bottom-0 flex justify-between px-4 py-3 bg-gray-50 sm:px-6">

            <div class="">
                {{-- null --}}
            </div>
            <div>
                <div wire:loading wire:target="">
                    <x-loading />
                </div>

                <x-green-button :disabled=false wire:click.prevent="" type="button" wire:loading.remove>
                    Simpan
                </x-green-button>
            </div>
        </div>


    </div>

</div>
