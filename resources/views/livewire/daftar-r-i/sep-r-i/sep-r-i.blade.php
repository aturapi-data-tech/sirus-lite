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
                        <div class='grid items-center grid-cols-2 gap-4 mb-2'>
                            <x-input-label for="dataDaftarRi.sep.reqSep.noKartu" :value="__('No Kartu BPJS')" :required="true" />
                            <x-input-label for="dataDaftarRi.sep.reqSep.noMR" :value="__('No Rekam Medis')" :required="true" />
                        </div>

                        <div class="grid items-center grid-cols-2 gap-2 mb-2">
                            <x-text-input id="dataDaftarRi.sep.reqSep.noKartu" placeholder="No Kartu BPJS"
                                class="mt-1 ml-2" :errorshas="$errors->has('dataDaftarRi.sep.reqSep.noKartu')" :disabled="$disabledPropertyRiStatus"
                                wire:model.debounce.500ms="dataDaftarRi.sep.reqSep.noKartu" />

                            <x-text-input id="dataDaftarRi.sep.reqSep.noMR" placeholder="No Rekam Medis"
                                class="mt-1 ml-2" :errorshas="$errors->has('dataDaftarRi.sep.reqSep.noMR')" :disabled="$disabledPropertyRiStatus"
                                wire:model.debounce.500ms="dataDaftarRi.sep.reqSep.noMR" />
                        </div>

                        {{-- Tanggal SEP & Tanggal Rujukan --}}
                        <div class="grid grid-cols-2 gap-2 mb-2">
                            <div>
                                <x-input-label for="dataDaftarRi.sep.reqSep.tglSep" :value="__('Tanggal SEP')"
                                    :required="true" />
                                <x-text-input id="dataDaftarRi.sep.reqSep.tglSep" placeholder="YYYY-MM-DD"
                                    class="mt-1 ml-2" :errorshas="$errors->has('dataDaftarRi.sep.reqSep.tglSep')" :disabled="$disabledPropertyRiStatus"
                                    wire:model.debounce.500ms="dataDaftarRi.sep.reqSep.tglSep" />
                            </div>
                            <div>
                                <x-input-label for="dataDaftarRi.sep.reqSep.rujukan.tglRujukan" :value="__('Tanggal Rujukan')"
                                    :required="true" />
                                <x-text-input id="dataDaftarRi.sep.reqSep.rujukan.tglRujukan" placeholder="YYYY-MM-DD"
                                    class="mt-1 ml-2" :errorshas="$errors->has('dataDaftarRi.sep.reqSep.rujukan.tglRujukan')" :disabled="$disabledPropertyRiStatus"
                                    wire:model.debounce.500ms="dataDaftarRi.sep.reqSep.rujukan.tglRujukan" />
                            </div>
                        </div>

                        {{-- No Rujukan & PPK Rujukan --}}
                        <div class="grid grid-cols-2 gap-2 mb-2">
                            <div>
                                <x-input-label for="dataDaftarRi.sep.reqSep.rujukan.noRujukan" :value="__('No Rujukan')"
                                    :required="true" />
                                <x-text-input id="dataDaftarRi.sep.reqSep.rujukan.noRujukan" placeholder="No Rujukan"
                                    class="mt-1 ml-2" :errorshas="$errors->has('dataDaftarRi.sep.reqSep.rujukan.noRujukan')" :disabled="$disabledPropertyRiStatus"
                                    wire:model.debounce.500ms="dataDaftarRi.sep.reqSep.rujukan.noRujukan" />
                            </div>
                            <div>
                                <x-input-label for="dataDaftarRi.sep.reqSep.rujukan.ppkRujukan" :value="__('PPK Rujukan')"
                                    :required="true" />
                                <x-text-input id="dataDaftarRi.sep.reqSep.rujukan.ppkRujukan" placeholder="Kode PPK"
                                    class="mt-1 ml-2" :errorshas="$errors->has('dataDaftarRi.sep.reqSep.rujukan.ppkRujukan')" :disabled="$disabledPropertyRiStatus"
                                    wire:model.debounce.500ms="dataDaftarRi.sep.reqSep.rujukan.ppkRujukan" />
                            </div>
                        </div>

                        {{-- Diagnosa Awal --}}
                        <div>
                            <x-input-label for="dataDaftarRi.sep.reqSep.diagAwal" :value="__('Diagnosa Awal')"
                                :required="true" />
                            <x-text-input id="dataDaftarRi.sep.reqSep.diagAwal" placeholder="ICD10" class="mt-1 ml-2"
                                :errorshas="$errors->has('dataDaftarRi.sep.reqSep.diagAwal')" :disabled="$disabledPropertyRiStatus"
                                wire:model.debounce.500ms="dataDaftarRi.sep.reqSep.diagAwal" />
                        </div>

                        {{-- SKDP --}}
                        <div class="grid grid-cols-2 gap-2 mb-2">
                            <div>
                                <x-input-label for="dataDaftarRi.sep.reqSep.skdp.noSurat" :value="__('No SKDP')"
                                    :required="true" />
                                <x-text-input id="dataDaftarRi.sep.reqSep.skdp.noSurat" placeholder="No Surat SKDP"
                                    class="mt-1 ml-2" :errorshas="$errors->has('dataDaftarRi.sep.reqSep.skdp.noSurat')" :disabled="$disabledPropertyRiStatus"
                                    wire:model.debounce.500ms="dataDaftarRi.sep.reqSep.skdp.noSurat" />
                            </div>
                            <div>
                                <x-input-label for="dataDaftarRi.sep.reqSep.skdp.kodeDPJP" :value="__('Kode DPJP')"
                                    :required="true" />
                                <x-text-input id="dataDaftarRi.sep.reqSep.skdp.kodeDPJP" placeholder="Kode DPJP"
                                    class="mt-1 ml-2" :errorshas="$errors->has('dataDaftarRi.sep.reqSep.skdp.kodeDPJP')" :disabled="$disabledPropertyRiStatus"
                                    wire:model.debounce.500ms="dataDaftarRi.sep.reqSep.skdp.kodeDPJP" />
                            </div>
                        </div>

                        {{-- Telepon & Catatan --}}
                        <div class="grid grid-cols-2 gap-2 mb-2">
                            <div>
                                <x-input-label for="dataDaftarRi.sep.reqSep.noTelp" :value="__('No Telepon')" />
                                <x-text-input id="dataDaftarRi.sep.reqSep.noTelp" placeholder="08xxxxxxxxxx"
                                    class="mt-1 ml-2" :errorshas="$errors->has('dataDaftarRi.sep.reqSep.noTelp')" :disabled="$disabledPropertyRiStatus"
                                    wire:model.debounce.500ms="dataDaftarRi.sep.reqSep.noTelp" />
                            </div>
                            <div>
                                <x-input-label for="dataDaftarRi.sep.reqSep.catatan" :value="__('Catatan')" />
                                <x-text-input id="dataDaftarRi.sep.reqSep.catatan" placeholder="Catatan"
                                    class="mt-1 ml-2" :errorshas="$errors->has('dataDaftarRi.sep.reqSep.catatan')" :disabled="$disabledPropertyRiStatus"
                                    wire:model.debounce.500ms="dataDaftarRi.sep.reqSep.catatan" />
                            </div>
                        </div>

                    </x-border-form>
                </div>
            </div>

            <div class="sticky bottom-0 flex justify-between px-4 py-3 bg-gray-50 sm:px-6">
                <div>
                    <div wire:loading wire:target="setSEPJsonReqRI">
                        <x-loading />
                    </div>
                    <x-yellow-button :disabled="$disabledPropertyRiStatus" wire:click.prevent="setSEPJsonReqRI('{{ $riHdrNoRef }}')"
                        type="button" wire:loading.remove>
                        setSEP RI
                    </x-yellow-button>
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
