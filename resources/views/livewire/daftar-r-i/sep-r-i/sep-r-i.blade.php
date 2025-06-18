<div>

    <div class="w-full mb-1">

        @php
            $disabledPropertyRiStatus = false;
        @endphp

        <form class="scroll-smooth hover:scroll-auto">
            <div class="grid grid-cols-1">
                <div id="TransaksiRawatInap" class="px-4">
                    <x-border-form :title="__($myTitle)" :align="__('start')" :bgcolor="__('bg-white')" class="mr-0">

                        {{-- Nomor SEP (Ditampilkan, Tidak Bisa Diedit) --}}
                        <div class="mb-2">
                            <x-input-label for="noSep" :value="'Nomor SEP'" :required="true" />
                            <x-text-input id="noSep" placeholder="Nomor SEP" class="mt-1 ml-2"
                                wire:model="dataDaftarRi.sep.noSep" :disabled="true" />
                        </div>

                        {{-- No Kartu BPJS & No MR --}}
                        <div class='grid items-center grid-cols-2 gap-4 mb-2'>
                            <x-input-label for="noKartu" :value="'No Kartu BPJS'" :required="true" />
                            <x-input-label for="noMR" :value="'No Rekam Medis'" :required="true" />
                        </div>
                        <div class="grid items-center grid-cols-2 gap-2 mb-2">
                            <x-text-input id="noKartu" placeholder="No Kartu BPJS" class="mt-1 ml-2"
                                wire:model.debounce.500ms="dataDaftarRi.sep.reqSep.request.t_sep.noKartu" />
                            <x-text-input id="noMR" placeholder="No Rekam Medis" class="mt-1 ml-2"
                                wire:model.debounce.500ms="dataDaftarRi.sep.reqSep.request.t_sep.noMR" />
                        </div>

                        {{-- Tanggal SEP --}}
                        <div class="mb-2">
                            <x-input-label for="tglSep" :value="'Tanggal SEP'" :required="true" />
                            <x-text-input id="tglSep" placeholder="YYYY-MM-DD" class="mt-1 ml-2"
                                wire:model.debounce.500ms="dataDaftarRi.sep.reqSep.request.t_sep.tglSep" />
                        </div>

                        {{-- Kelas Rawat Hak --}}
                        <div class="mb-2">
                            <x-input-label for="klsRawatHak" :value="'Kelas Rawat (Hak)'" :required="true" />
                            <x-text-input id="klsRawatHak" placeholder="Isi 1 / 2 / 3" class="mt-1 ml-2"
                                wire:model="dataDaftarRi.sep.reqSep.request.t_sep.klsRawat.klsRawatHak" />
                        </div>

                        {{-- Rujukan --}}
                        <div class="grid grid-cols-2 gap-2 mb-2">
                            <div>
                                <x-input-label :value="'Tanggal Rujukan'" :required="true" />
                                <x-text-input placeholder="YYYY-MM-DD" class="mt-1 ml-2"
                                    wire:model="dataDaftarRi.sep.reqSep.request.t_sep.rujukan.tglRujukan" />
                            </div>
                            <div>
                                <x-input-label :value="'No Rujukan'" :required="true" />
                                <x-text-input placeholder="No Rujukan" class="mt-1 ml-2"
                                    wire:model="dataDaftarRi.sep.reqSep.request.t_sep.rujukan.noRujukan" />
                            </div>
                        </div>
                        <div class="mb-2">
                            <x-input-label :value="'PPK Rujukan'" :required="true" />
                            <x-text-input placeholder="Kode PPK" class="mt-1 ml-2"
                                wire:model="dataDaftarRi.sep.reqSep.request.t_sep.rujukan.ppkRujukan" />
                        </div>

                        {{-- Diagnosa Awal --}}
                        <div class="mb-2">
                            <x-input-label :value="'Diagnosa Awal (ICD10)'" :required="true" />
                            <x-text-input placeholder="Kode ICD10" class="mt-1 ml-2"
                                wire:model="dataDaftarRi.sep.reqSep.request.t_sep.diagAwal" />
                        </div>

                        {{-- SKDP --}}
                        <div class="grid grid-cols-2 gap-2 mb-2">
                            <div>
                                <x-input-label :value="'No SKDP'" :required="true" />
                                <x-text-input placeholder="Nomor SKDP" class="mt-1 ml-2"
                                    wire:model="dataDaftarRi.sep.reqSep.request.t_sep.skdp.noSurat" />
                            </div>
                            <div>
                                <x-input-label :value="'Kode DPJP'" :required="true" />
                                <x-text-input placeholder="Kode DPJP" class="mt-1 ml-2"
                                    wire:model="dataDaftarRi.sep.reqSep.request.t_sep.skdp.kodeDPJP" />
                            </div>
                        </div>

                        {{-- No Telp --}}
                        <div class="mb-4">
                            <x-input-label :value="'No Telepon'" :required="true" />
                            <x-text-input placeholder="08xxxxxxxxxx" class="mt-1 ml-2"
                                wire:model="dataDaftarRi.sep.reqSep.request.t_sep.noTelp" />
                        </div>

                    </x-border-form>
                </div>
            </div>

            <div class="sticky bottom-0 flex justify-between px-4 py-3 bg-gray-50 sm:px-6">
                <div>
                    @if (empty($dataDaftarRi['sep']['noSep']))
                        <div wire:loading wire:target="setSEPJsonReqRI()">
                            <x-loading />
                        </div>
                        <x-yellow-button :disabled="$disabledPropertyRiStatus" wire:click.prevent="setSEPJsonReqRI('{{ $riHdrNoRef }}')"
                            type="button" wire:loading.remove>
                            setSEP RI
                        </x-yellow-button>
                    @endif
                </div>
                <div>
                    <div wire:loading wire:target="store">
                        <x-loading />
                    </div>
                    <x-green-button :disabled="$disabledPropertyRiStatus" wire:click.prevent="store()" type="button" wire:loading.remove>
                        Buat SEP Rawat Inap
                    </x-green-button>
                </div>
            </div>
        </form>



    </div>
</div>
