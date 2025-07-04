<div>

    <div class="w-full mb-1">

        <form class="scroll-smooth hover:scroll-auto">
            <div class="grid grid-cols-1">
                <div id="TransaksiRawatInap" class="px-4">
                    <x-border-form :title="__($myTitle)" :align="__('start')" :bgcolor="__('bg-white')" class="p-4 mr-0">
                        {{-- Gunakan grid utama: 1 kolom di mobile, 2 kolom di md ke atas --}}
                        <div class="grid grid-cols-1 gap-2 md:grid-cols-3">

                            {{-- Nomor SEP --}}
                            <div>
                                <x-input-label for="noSep" :value="'Nomor SEP'" :required="true" />
                                <x-text-input id="noSep" placeholder="Nomor SEP" class="w-full mt-1"
                                    wire:model="dataDaftarRi.sep.noSep" :disabled="true" />
                            </div>

                            {{-- No Kartu BPJS --}}
                            <div>
                                <x-input-label for="noKartu" :value="'No Kartu BPJS'" :required="true" />
                                <x-text-input id="noKartu" placeholder="No Kartu BPJS" class="w-full mt-1"
                                    wire:model.debounce.500ms="dataDaftarRi.sep.reqSep.request.t_sep.noKartu"
                                    :disabled="true" />
                            </div>

                            {{-- No MR --}}
                            <div>
                                <x-input-label for="noMR" :value="'No Rekam Medis'" :required="true" />
                                <x-text-input id="noMR" placeholder="No Rekam Medis" class="w-full mt-1"
                                    wire:model.debounce.500ms="dataDaftarRi.sep.reqSep.request.t_sep.noMR"
                                    :disabled="true" />
                            </div>

                            {{-- Tanggal SEP --}}
                            <div>
                                <x-input-label for="tglSep" :value="'Tanggal SEP'" />
                                <x-text-input id="tglSep" placeholder="YYYY-MM-DD" class="w-full mt-1"
                                    wire:model.debounce.500ms="dataDaftarRi.sep.reqSep.request.t_sep.tglSep"
                                    :disabled="true" />
                            </div>

                            {{-- Kelas Rawat Hak --}}
                            <div>
                                <x-input-label for="klsRawatHak" :value="'Kelas Rawat (Hak)'" :required="true" />
                                <x-text-input id="klsRawatHak" placeholder="1 / 2 / 3" class="w-full mt-1"
                                    wire:model="dataDaftarRi.sep.reqSep.request.t_sep.klsRawat.klsRawatHak"
                                    :disabled="true" />
                            </div>

                            {{-- Katarak --}}
                            <div>
                                <x-input-label :value="'Katarak'" />
                                <div class="grid grid-cols-2 gap-4 mt-1">
                                    <x-radio-button label="Ya" value="1"
                                        wire:model="dataDaftarRi.sep.reqSep.request.t_sep.katarak.katarak" />
                                    <x-radio-button label="Tidak" value="0"
                                        wire:model="dataDaftarRi.sep.reqSep.request.t_sep.katarak.katarak" />
                                </div>
                            </div>

                            {{-- Tanggal & No Rujukan --}}
                            <div class="grid grid-cols-1 col-span-1 gap-2 md:col-span-2 md:grid-cols-2">
                                <div>
                                    <x-input-label :value="'Tanggal Rujukan'" />
                                    <x-text-input placeholder="YYYY-MM-DD" class="w-full mt-1"
                                        wire:model="dataDaftarRi.sep.reqSep.request.t_sep.rujukan.tglRujukan"
                                        :disabled="true" />
                                </div>
                                <div>
                                    <x-input-label :value="'No Rujukan'" :required="true" />
                                    <x-text-input placeholder="No Rujukan" class="w-full mt-1"
                                        wire:model="dataDaftarRi.sep.reqSep.request.t_sep.rujukan.noRujukan"
                                        :disabled="true" />
                                </div>
                            </div>

                            {{-- PPK Rujukan --}}
                            <div>
                                <x-input-label :value="'PPK Rujukan'" :required="true" />
                                <x-text-input placeholder="Kode PPK" class="w-full mt-1"
                                    wire:model="dataDaftarRi.sep.reqSep.request.t_sep.rujukan.ppkRujukan"
                                    :disabled="true" />
                            </div>

                            {{-- Diagnosa Awal --}}
                            <div>
                                <x-input-label :value="'Diagnosa Awal (ICD10)'" :required="true" />
                                <x-text-input placeholder="Kode ICD10" class="w-full mt-1"
                                    wire:model="dataDaftarRi.sep.reqSep.request.t_sep.diagAwal" />
                            </div>

                            {{-- No SKDP & Kode DPJP --}}
                            <div class="grid grid-cols-1 col-span-1 gap-2 md:col-span-2 md:grid-cols-2">
                                <div>
                                    <x-input-label :value="'No SKDP'" :required="true" />
                                    <x-text-input placeholder="Nomor SKDP" class="w-full mt-1"
                                        wire:model="dataDaftarRi.sep.reqSep.request.t_sep.skdp.noSurat"
                                        :disabled="true" />
                                </div>
                                <div>
                                    <x-input-label :value="'Kode DPJP'" :required="true" />
                                    <x-text-input placeholder="Kode DPJP" class="w-full mt-1"
                                        wire:model="dataDaftarRi.sep.reqSep.request.t_sep.skdp.kodeDPJP"
                                        :disabled="true" />
                                </div>
                            </div>

                            {{-- No Telepon --}}
                            <div>
                                <x-input-label :value="'No Telepon'" />
                                <x-text-input placeholder="08xxxxxxxxxx" class="w-full mt-1"
                                    wire:model="dataDaftarRi.sep.reqSep.request.t_sep.noTelp" :disabled="true" />
                            </div>

                            {{-- Keterangan --}}
                            <div class="col-span-1 md:col-span-2">
                                <x-input-label for="catatan" :value="__('Keterangan')" />
                                <x-text-input id="catatan" placeholder="Keterangan" class="w-full mt-1"
                                    wire:model="dataDaftarRi.sep.reqSep.request.t_sep.catatan" />
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
                        <x-yellow-button :disabled="false" wire:click.prevent="setSEPJsonReqRI('{{ $riHdrNoRef }}')"
                            type="button" wire:loading.remove>
                            setSEP RI
                        </x-yellow-button>
                    @endif
                </div>
                <div>
                    <div wire:loading wire:target="store">
                        <x-loading />
                    </div>
                    @if (!empty($dataDaftarRi['sep']['noSep']))
                        <x-yellow-button :disabled="false" wire:click.prevent="store()" type="button"
                            wire:loading.remove>
                            Update SEP Rawat Inap
                        </x-yellow-button>
                    @else
                        <x-green-button :disabled="false" wire:click.prevent="store()" type="button"
                            wire:loading.remove>
                            Buat SEP Rawat Inap
                        </x-green-button>
                    @endif
                </div>
            </div>
        </form>



    </div>
</div>
