<div>

    <div class="w-full mb-1">

        @php
            $disabledPropertyRiStatus = false;
        @endphp

        <form class="scroll-smooth hover:scroll-auto">
            <div class="grid grid-cols-1">
                <div id="TransaksiRawatInap" class="px-4">
                    <x-border-form :title="__($myTitle)" :align="__('start')" :bgcolor="__('bg-white')" class="mr-0">

                        {{-- No Kartu & No MR --}}
                        <div class='grid items-center grid-cols-3 gap-4 mb-2'>
                            <x-input-label for="dataDaftarRi.sep.reqSep.request.t_sep.noKartu" :value="__('No Kartu BPJS')"
                                :required="true" />
                            <x-input-label for="dataDaftarRi.sep.reqSep.request.t_sep.noMR" :value="__('No Rekam Medis')"
                                :required="true" />
                            <x-input-label for="dataDaftarRi.sep.noSep" :value="'Nomor SEP'" :required="true" />
                        </div>

                        <div class="grid items-center grid-cols-3 gap-2 mb-2">
                            <x-text-input id="dataDaftarRi.sep.reqSep.request.t_sep.noKartu" placeholder="No Kartu BPJS"
                                class="mt-1 ml-2" :errorshas="$errors->has('dataDaftarRi.sep.reqSep.request.t_sep.noKartu')" :disabled="$disabledPropertyRiStatus"
                                wire:model.debounce.500ms="dataDaftarRi.sep.reqSep.request.t_sep.noKartu" />

                            <x-text-input id="dataDaftarRi.sep.reqSep.request.t_sep.noMR" placeholder="No Rekam Medis"
                                class="mt-1 ml-2" :errorshas="$errors->has('dataDaftarRi.sep.reqSep.request.t_sep.noMR')" :disabled="$disabledPropertyRiStatus"
                                wire:model.debounce.500ms="dataDaftarRi.sep.reqSep.request.t_sep.noMR" />

                            <x-text-input id="dataDaftarRi.sep.noSep" placeholder="Nomor SEP" class="mt-1 ml-2"
                                :errorshas="$errors->has('dataDaftarRi.sep.noSep')" :disabled="true"
                                wire:model.debounce.500ms="dataDaftarRi.sep.noSep" />
                        </div>

                        {{-- Tanggal SEP & Tanggal Rujukan --}}
                        <div class="grid grid-cols-2 gap-2 mb-2">
                            <div>
                                <x-input-label for="dataDaftarRi.sep.reqSep.request.t_sep.tglSep" :value="__('Tanggal SEP')"
                                    :required="true" />
                                <x-text-input id="dataDaftarRi.sep.reqSep.request.t_sep.tglSep" placeholder="YYYY-MM-DD"
                                    class="mt-1 ml-2" :errorshas="$errors->has('dataDaftarRi.sep.reqSep.request.t_sep.tglSep')" :disabled="$disabledPropertyRiStatus"
                                    wire:model.debounce.500ms="dataDaftarRi.sep.reqSep.request.t_sep.tglSep" />
                            </div>
                            <div>
                                <x-input-label for="dataDaftarRi.sep.reqSep.request.t_sep.rujukan.tglRujukan"
                                    :value="__('Tanggal Rujukan')" :required="true" />
                                <x-text-input id="dataDaftarRi.sep.reqSep.request.t_sep.rujukan.tglRujukan"
                                    placeholder="YYYY-MM-DD" class="mt-1 ml-2" :errorshas="$errors->has('dataDaftarRi.sep.reqSep.request.t_sep.rujukan.tglRujukan')" :disabled="$disabledPropertyRiStatus"
                                    wire:model.debounce.500ms="dataDaftarRi.sep.reqSep.request.t_sep.rujukan.tglRujukan" />
                            </div>
                        </div>

                        {{-- No Rujukan & PPK Rujukan --}}
                        <div class="grid grid-cols-2 gap-2 mb-2">
                            <div>
                                <x-input-label for="dataDaftarRi.sep.reqSep.request.t_sep.rujukan.noRujukan"
                                    :value="__('No Rujukan')" :required="true" />
                                <x-text-input id="dataDaftarRi.sep.reqSep.request.t_sep.rujukan.noRujukan"
                                    placeholder="No Rujukan" class="mt-1 ml-2" :errorshas="$errors->has('dataDaftarRi.sep.reqSep.request.t_sep.rujukan.noRujukan')" :disabled="$disabledPropertyRiStatus"
                                    wire:model.debounce.500ms="dataDaftarRi.sep.reqSep.request.t_sep.rujukan.noRujukan" />
                            </div>
                            <div>
                                <x-input-label for="dataDaftarRi.sep.reqSep.request.t_sep.rujukan.ppkRujukan"
                                    :value="__('PPK Rujukan')" :required="true" />
                                <x-text-input id="dataDaftarRi.sep.reqSep.request.t_sep.rujukan.ppkRujukan"
                                    placeholder="Kode PPK" class="mt-1 ml-2" :errorshas="$errors->has('dataDaftarRi.sep.reqSep.request.t_sep.rujukan.ppkRujukan')" :disabled="$disabledPropertyRiStatus"
                                    wire:model.debounce.500ms="dataDaftarRi.sep.reqSep.request.t_sep.rujukan.ppkRujukan" />
                            </div>
                        </div>

                        {{-- Diagnosa Awal --}}
                        <div>
                            <x-input-label for="dataDaftarRi.sep.reqSep.request.t_sep.diagAwal" :value="__('Diagnosa Awal')"
                                :required="true" />
                            <x-text-input id="dataDaftarRi.sep.reqSep.request.t_sep.diagAwal" placeholder="ICD10"
                                class="mt-1 ml-2" :errorshas="$errors->has('dataDaftarRi.sep.reqSep.request.t_sep.diagAwal')" :disabled="$disabledPropertyRiStatus"
                                wire:model.debounce.500ms="dataDaftarRi.sep.reqSep.request.t_sep.diagAwal" />
                        </div>

                        {{-- SKDP --}}
                        <div class="grid grid-cols-2 gap-2 mb-2">
                            <div>
                                <x-input-label for="dataDaftarRi.sep.reqSep.request.t_sep.skdp.noSurat"
                                    :value="__('No SKDP')" :required="true" />
                                <x-text-input id="dataDaftarRi.sep.reqSep.request.t_sep.skdp.noSurat"
                                    placeholder="No Surat SKDP" class="mt-1 ml-2" :errorshas="$errors->has('dataDaftarRi.sep.reqSep.request.t_sep.skdp.noSurat')" :disabled="$disabledPropertyRiStatus"
                                    wire:model.debounce.500ms="dataDaftarRi.sep.reqSep.request.t_sep.skdp.noSurat" />
                            </div>
                            <div>
                                <x-input-label for="dataDaftarRi.sep.reqSep.request.t_sep.skdp.kodeDPJP"
                                    :value="__('Kode DPJP')" :required="true" />
                                <x-text-input id="dataDaftarRi.sep.reqSep.request.t_sep.skdp.kodeDPJP"
                                    placeholder="Kode DPJP" class="mt-1 ml-2" :errorshas="$errors->has('dataDaftarRi.sep.reqSep.request.t_sep.skdp.kodeDPJP')" :disabled="$disabledPropertyRiStatus"
                                    wire:model.debounce.500ms="dataDaftarRi.sep.reqSep.request.t_sep.skdp.kodeDPJP" />
                            </div>
                        </div>

                        {{-- Telepon & Catatan --}}
                        <div class="grid grid-cols-2 gap-2 mb-2">
                            <div>
                                <x-input-label for="dataDaftarRi.sep.reqSep.request.t_sep.noTelp" :value="__('No Telepon')" />
                                <x-text-input id="dataDaftarRi.sep.reqSep.request.t_sep.noTelp"
                                    placeholder="08xxxxxxxxxx" class="mt-1 ml-2" :errorshas="$errors->has('dataDaftarRi.sep.reqSep.request.t_sep.noTelp')" :disabled="$disabledPropertyRiStatus"
                                    wire:model.debounce.500ms="dataDaftarRi.sep.reqSep.request.t_sep.noTelp" />
                            </div>
                            <div>
                                <x-input-label for="dataDaftarRi.sep.reqSep.request.t_sep.catatan" :value="__('Catatan')" />
                                <x-text-input id="dataDaftarRi.sep.reqSep.request.t_sep.catatan" placeholder="Catatan"
                                    class="mt-1 ml-2" :errorshas="$errors->has('dataDaftarRi.sep.reqSep.request.t_sep.catatan')" :disabled="$disabledPropertyRiStatus"
                                    wire:model.debounce.500ms="dataDaftarRi.sep.reqSep.request.t_sep.catatan" />
                            </div>
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
