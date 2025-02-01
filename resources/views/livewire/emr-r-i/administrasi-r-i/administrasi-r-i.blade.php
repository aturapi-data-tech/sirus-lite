<div>

    {{-- jika anamnesa kosong ngak usah di render --}}
    <div class="w-full mb-1">

        <div id="TransaksiRawatJalan" class="px-2">
            {{-- Transasi EMR --}}
            <div class="flex justify-between m-4 m-x-auto">

                <div class="grid grid-cols-9 gap-2 mx-2">

                    <div class="relative">
                        <input type="text" id="usiaAdmin" disabled
                            class="block rounded-t-lg px-2.5 pb-1 pt-5 w-full text-sm text-gray-700 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-secondary focus:outline-none focus:ring-0 focus:border-primary peer"
                            placeholder=" " value="{{ number_format($sumAdminAge) }}" />
                        <label for="usiaAdmin"
                            class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-2.5 peer-focus:text-primary peer-focus:dark:text-secondary peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto">
                            Admin Usia
                        </label>
                    </div>

                    <div class="relative">
                        <input type="text" id="statusAdmin" disabled
                            class="block rounded-t-lg px-2.5 pb-1 pt-5 w-full text-sm text-gray-700 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-secondary focus:outline-none focus:ring-0 focus:border-primary peer"
                            placeholder=" " value="{{ number_format($sumAdminStatus) }}" />
                        <label for="statusAdmin"
                            class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-2.5 peer-focus:text-primary peer-focus:dark:text-secondary peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto">
                            Admin Status
                        </label>
                    </div>

                    <div class="relative">
                        <input type="text" id="totalRiVisit" disabled
                            class="block rounded-t-lg px-2.5 pb-1 pt-5 w-full text-sm text-gray-700 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-secondary focus:outline-none focus:ring-0 focus:border-primary peer"
                            placeholder=" " value="{{ number_format($sumRiVisit) }}" />
                        <label for="totalRiVisit"
                            class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-2.5 peer-focus:text-primary peer-focus:dark:text-secondary peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto">
                            Visit
                        </label>
                    </div>

                    <div class="relative">
                        <input type="text" id="konsultasiRi" disabled
                            class="block rounded-t-lg px-2.5 pb-1 pt-5 w-full text-sm text-gray-700 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-secondary focus:outline-none focus:ring-0 focus:border-primary peer"
                            placeholder=" " value="{{ number_format($sumRiKonsul) }}" />
                        <label for="konsultasiRi"
                            class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-2.5 peer-focus:text-primary peer-focus:dark:text-secondary peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto">
                            Konsul
                        </label>
                    </div>

                    <div class="relative">
                        <input type="text" id="aktivitasiRiParams" disabled
                            class="block rounded-t-lg px-2.5 pb-1 pt-5 w-full text-sm text-gray-700 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-secondary focus:outline-none focus:ring-0 focus:border-primary peer"
                            placeholder=" " value="{{ number_format($sumRiActParams) }}" />
                        <label for="aktivitasiRiParams"
                            class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-2.5 peer-focus:text-primary peer-focus:dark:text-secondary peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto">
                            JM
                        </label>
                    </div>

                    <div class="relative">
                        <input type="text" id="dokumenRiAktifitas" disabled
                            class="block rounded-t-lg px-2.5 pb-1 pt-5 w-full text-sm text-gray-700 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-secondary focus:outline-none focus:ring-0 focus:border-primary peer"
                            placeholder=" " value="{{ number_format($sumRiActDocs) }}" />
                        <label for="dokumenRiAktifitas"
                            class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-2.5 peer-focus:text-primary peer-focus:dark:text-secondary peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto">
                            JD
                        </label>
                    </div>

                    <div class="relative">
                        <input type="text" id="hasilLabRi" disabled
                            class="block rounded-t-lg px-2.5 pb-1 pt-5 w-full text-sm text-gray-700 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-secondary focus:outline-none focus:ring-0 focus:border-primary peer"
                            placeholder=" " value="{{ number_format($sumRiLab) }}" />
                        <label for="hasilLabRi"
                            class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-2.5 peer-focus:text-primary peer-focus:dark:text-secondary peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto">
                            Laboratorium
                        </label>
                    </div>

                    <div class="relative">
                        <input type="text" id="pemeriksaanRadRi" disabled
                            class="block rounded-t-lg px-2.5 pb-1 pt-5 w-full text-sm text-gray-700 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-secondary focus:outline-none focus:ring-0 focus:border-primary peer"
                            placeholder=" " value="{{ number_format($sumRiRad) }}" />
                        <label for="pemeriksaanRadRi"
                            class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-2.5 peer-focus:text-primary peer-focus:dark:text-secondary peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto">
                            Radiologi
                        </label>
                    </div>

                    <div class="relative">
                        <input type="text" id="totalUgdRj" disabled
                            class="block rounded-t-lg px-2.5 pb-1 pt-5 w-full text-sm text-gray-700 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-secondary focus:outline-none focus:ring-0 focus:border-primary peer"
                            placeholder=" " value="{{ number_format($sumUgdRj) }}" />
                        <label for="totalUgdRj"
                            class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-2.5 peer-focus:text-primary peer-focus:dark:text-secondary peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto">
                            Trf RJ/UGD
                        </label>
                    </div>

                    <div class="relative">
                        <input type="text" id="riLainnya" disabled
                            class="block rounded-t-lg px-2.5 pb-1 pt-5 w-full text-sm text-gray-700 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-secondary focus:outline-none focus:ring-0 focus:border-primary peer"
                            placeholder=" " value="{{ number_format($sumRiOther) }}" />
                        <label for="riLainnya"
                            class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-2.5 peer-focus:text-primary peer-focus:dark:text-secondary peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto">
                            Lain-Lain
                        </label>
                    </div>

                    <div class="relative">
                        <input type="text" id="sumRiOk" disabled
                            class="block rounded-t-lg px-2.5 pb-1 pt-5 w-full text-sm text-gray-700 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-secondary focus:outline-none focus:ring-0 focus:border-primary peer"
                            placeholder=" " value="{{ number_format($sumRiOk) }}" />
                        <label for="sumRiOk"
                            class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-2.5 peer-focus:text-primary peer-focus:dark:text-secondary peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto">
                            OK
                        </label>
                    </div>

                    <div class="relative">
                        <input type="text" id="sumRiRoom" disabled
                            class="block rounded-t-lg px-2.5 pb-1 pt-5 w-full text-sm text-gray-700 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-secondary focus:outline-none focus:ring-0 focus:border-primary peer"
                            placeholder=" " value="{{ number_format($sumRiRoom) }}" />
                        <label for="sumRiRoom"
                            class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-2.5 peer-focus:text-primary peer-focus:dark:text-secondary peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto">
                            Kamar
                        </label>
                    </div>

                    <div class="relative">
                        <input type="text" id="sumCService" disabled
                            class="block rounded-t-lg px-2.5 pb-1 pt-5 w-full text-sm text-gray-700 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-secondary focus:outline-none focus:ring-0 focus:border-primary peer"
                            placeholder=" " value="{{ number_format($sumCService) }}" />
                        <label for="sumCService"
                            class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-2.5 peer-focus:text-primary peer-focus:dark:text-secondary peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto">
                            Layanan
                        </label>
                    </div>

                    <div class="relative">
                        <input type="text" id="sumRiPerawatan" disabled
                            class="block rounded-t-lg px-2.5 pb-1 pt-5 w-full text-sm text-gray-700 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-secondary focus:outline-none focus:ring-0 focus:border-primary peer"
                            placeholder=" " value="{{ number_format($sumRiPerawatan) }}" />
                        <label for="sumRiPerawatan"
                            class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-2.5 peer-focus:text-primary peer-focus:dark:text-secondary peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto">
                            Perawatan
                        </label>
                    </div>

                    <div class="relative">
                        <input type="text" id="sumRiBonResep" disabled
                            class="block rounded-t-lg px-2.5 pb-1 pt-5 w-full text-sm text-gray-700 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-secondary focus:outline-none focus:ring-0 focus:border-primary peer"
                            placeholder=" " value="{{ number_format($sumRiBonResep) }}" />
                        <label for="sumRiBonResep"
                            class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-2.5 peer-focus:text-primary peer-focus:dark:text-secondary peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto">
                            Bon Resep
                        </label>
                    </div>

                    <div class="relative">
                        <input type="text" id="sumRiRtnObat" disabled
                            class="block rounded-t-lg px-2.5 pb-1 pt-5 w-full text-sm text-gray-700 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-secondary focus:outline-none focus:ring-0 focus:border-primary peer"
                            placeholder=" " value="{{ number_format($sumRiRtnObat) }}" />
                        <label for="sumRiRtnObat"
                            class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-2.5 peer-focus:text-primary peer-focus:dark:text-secondary peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto">
                            Retur Obat
                        </label>
                    </div>

                    <div class="relative">
                        <input type="text" id="sumRsObat" disabled
                            class="block rounded-t-lg px-2.5 pb-1 pt-5 w-full text-sm text-gray-700 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-secondary focus:outline-none focus:ring-0 focus:border-primary peer"
                            placeholder=" " value="{{ number_format($sumRsObat) }}" />
                        <label for="sumRsObat"
                            class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-2.5 peer-focus:text-primary peer-focus:dark:text-secondary peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto">
                            Obat Pinjam
                        </label>
                    </div>

                    <div class="relative">
                        <input type="text" id="sumTotalRI" disabled
                            class="block rounded-t-lg px-2.5 pb-1 pt-5 w-full text-lg font-semibold text-gray-700 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-secondary focus:outline-none focus:ring-0 focus:border-primary peer"
                            placeholder=" " value="{{ number_format($sumTotalRI) }}" />
                        <label for="sumTotalRI"
                            class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-2.5 peer-focus:text-primary peer-focus:dark:text-secondary peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto">
                            Total RI
                        </label>
                    </div>




                </div>


            </div>

            <div>
                {{-- jika anamnesa kosong ngak usah di render --}}
                <div class="w-full mb-1">

                    <div id="TransaksiRawatJalan" class="px-2">
                        <div id="TransaksiRawatJalan" x-data="{ activeTabAdministrasi: @entangle('activeTabAdministrasi') }">

                            <div class="px-2 mb-2 border-b border-gray-200 dark:border-gray-700">
                                <ul
                                    class="flex flex-wrap -mb-px text-xs font-medium text-center text-gray-500 dark:text-gray-400">

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
                                    'active': activeTabAdministrasi === 'RiVisit'
                                }"
                                x-show.transition.in.opacity.duration.600="activeTabAdministrasi === 'RiVisit'">
                                <livewire:emr-r-i.administrasi-r-i.visit-r-i :wire:key="'content-visit-r-i'"
                                    :riHdrNoRef="$riHdrNoRef">

                            </div>

                            <div class="p-2 rounded-lg bg-gray-50"
                                :class="{
                                    'active': activeTabAdministrasi === 'RiKonsul'
                                }"
                                x-show.transition.in.opacity.duration.600="activeTabAdministrasi === 'RiKonsul'">
                                <livewire:emr-r-i.administrasi-r-i.konsul-r-i :wire:key="'content-konsul-r-i'"
                                    :riHdrNoRef="$riHdrNoRef">

                            </div>

                            <div class="p-2 rounded-lg bg-gray-50"
                                :class="{
                                    'active': activeTabAdministrasi === 'RiActParams'
                                }"
                                x-show.transition.in.opacity.duration.600="activeTabAdministrasi === 'RiActParams'">
                                <livewire:emr-r-i.administrasi-r-i.jasa-medis-r-i :wire:key="'content-jasa-medis-r-i'"
                                    :riHdrNoRef="$riHdrNoRef">

                            </div>

                            <div class="p-2 rounded-lg bg-gray-50"
                                :class="{
                                    'active': activeTabAdministrasi === 'RiActDocs'
                                }"
                                x-show.transition.in.opacity.duration.600="activeTabAdministrasi === 'RiActDocs'">
                                <livewire:emr-r-i.administrasi-r-i.jasa-dokter-r-i :wire:key="'content-jasa-dokter-r-i'"
                                    :riHdrNoRef="$riHdrNoRef">

                            </div>

                            <div class="p-2 rounded-lg bg-gray-50"
                                :class="{
                                    'active': activeTabAdministrasi === 'RiLab'
                                }"
                                x-show.transition.in.opacity.duration.600="activeTabAdministrasi === 'RiLab'">
                                <livewire:emr-r-i.administrasi-r-i.laboratorium-r-i
                                    :wire:key="'content-laboratorium-r-i'" :riHdrNoRef="$riHdrNoRef">
                            </div>

                            {{-- <div class="p-2 rounded-lg bg-gray-50"
                                :class="{
                                    'active': activeTabAdministrasi === 'JasaDokter'
                                }"
                                x-show.transition.in.opacity.duration.600="activeTabAdministrasi === 'JasaDokter'">
                                <livewire:emr-r-i.administrasi-r-i.jasa-dokter-r-i
                                    :wire:key="'content-jasa-dokter-r-i2'" :riHdrNoRef="$riHdrNoRef">

                            </div>

                            <div class="p-2 rounded-lg bg-gray-50"
                                :class="{
                                    'active': activeTabAdministrasi === 'JasaMedis'
                                }"
                                x-show.transition.in.opacity.duration.600="activeTabAdministrasi === 'JasaMedis'">
                                <livewire:emr-r-i.administrasi-r-i.jasa-medis-r-i
                                    :wire:key="'content-jasa-medis-r-i'" :riHdrNoRef="$riHdrNoRef">

                            </div>

                            <div class="p-2 rounded-lg bg-gray-50"
                                :class="{
                                    'active': activeTabAdministrasi === 'Obat'
                                }"
                                x-show.transition.in.opacity.duration.600="activeTabAdministrasi === 'Obat'">
                                <livewire:emr-r-i.administrasi-r-i.obat-r-i :wire:key="'content-obat-r-i'"
                                    :riHdrNoRef="$riHdrNoRef">

                            </div>

                            <div class="p-2 rounded-lg bg-gray-50"
                                :class="{
                                    'active': activeTabAdministrasi === 'Laboratorium'
                                }"
                                x-show.transition.in.opacity.duration.600="activeTabAdministrasi === 'Laboratorium'">
                                <livewire:emr-r-i.administrasi-r-i.laboratorium-r-i
                                    :wire:key="'content-laboratorium-r-i'" :riHdrNoRef="$riHdrNoRef">

                            </div>

                            <div class="p-2 rounded-lg bg-gray-50"
                                :class="{
                                    'active': activeTabAdministrasi === 'Radiologi'
                                }"
                                x-show.transition.in.opacity.duration.600="activeTabAdministrasi === 'Radiologi'">
                                <livewire:emr-r-i.administrasi-r-i.radiologi-r-i
                                    :wire:key="'content-radiologi-r-i'" :riHdrNoRef="$riHdrNoRef">

                            </div>

                            <div class="p-2 rounded-lg bg-gray-50"
                                :class="{
                                    'active': activeTabAdministrasi === 'LainLain'
                                }"
                                x-show.transition.in.opacity.duration.600="activeTabAdministrasi === 'LainLain'">
                                <livewire:emr-r-i.administrasi-r-i.lain-lain-r-i
                                    :wire:key="'content-lain-lain-r-i2'" :riHdrNoRef="$riHdrNoRef">

                            </div> --}}


                        </div>
                    </div>









                </div>

            </div>

        </div>



        <div class="sticky bottom-0 flex justify-between px-4 py-3 bg-gray-50 sm:px-6">

            <div class="">
                {{-- null --}}
            </div>

        </div>


    </div>

</div>
