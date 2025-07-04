@php
    $disabledProperty = true;

    $disabledPropertyRj = $isOpenMode === 'tampil' ? true : false;

    $disabledPropertyRjStatus = $statusRjRef['statusId'] !== 'A' ? true : false;
@endphp


<div class="fixed inset-0 z-40 ease-out duration-400">

    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">

        <!-- This element is to trick the browser into transition-opacity. -->
        <div class="fixed inset-0 transition-opacity">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>



        <!-- This element is to trick the browser into centering the modal contents. -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>​

        {{-- click outsite close --}}
        <div x-data @click.outside="$wire.formRujukanRefBPJSStatus = false"
            class="inline-block overflow-auto text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:max-h-[35rem] sm:my-8 sm:align-middle sm:w-11/12"
            role="dialog" aria-modal="true" aria-labelledby="modal-headline">

            <div
                class="sticky top-0 flex items-center justify-between p-4 bg-opacity-75 border-b rounded-t bg-primary dark:border-gray-600">

                <h3 class="w-full text-2xl font-semibold text-white dark:text-white">
                    {{ 'Buat SEP UGD' }}
                </h3>




                {{-- Close Modal --}}
                <button wire:click="$set('formRujukanRefBPJSStatus',false)"
                    class="text-gray-400 bg-gray-50 hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd"></path>
                    </svg>
                </button>



            </div>



            <form class="scroll-smooth hover:scroll-auto">

                <x-border-form :title="__('')" :align="__('start')" :bgcolor="__('bg-gray-50')" class="pb-4 mx-4 space-y-4">
                    <!-- Spesialis/SubSpesialis -->
                    <div class="grid items-center grid-cols-4 gap-4">
                        <x-input-label for="spesialis" :value="__('Spesialis / SubSpesialis')" class="text-right" :required="__($errors->has('SEPJsonReq.request.t_sep.poli.tujuan'))" />

                        <div class="flex items-center col-span-3 gap-2">
                            <x-text-input id="spesialis" placeholder="Isi dengan data yang sesuai" class="flex-1"
                                :errorshas="__($errors->has('SEPJsonReq.request.t_sep.poli.tujuan'))" :disabled="$disabledProperty"
                                value="{{ (isset($SEPJsonReq['request']['t_sep']['poli']['tujuan']) ? $SEPJsonReq['request']['t_sep']['poli']['tujuan'] : 'Data tidak dapat di proses') . '   ' . (isset($SEPJsonReq['request']['t_sep']['poli']['tujuanNama']) ? $SEPJsonReq['request']['t_sep']['poli']['tujuanNama'] : 'Data tidak dapat di proses') }}" />

                            <x-check-box value="1" :label="__('Eksekutif')"
                                wire:model.debounce.500ms="SEPJsonReq.request.t_sep.poli.eksekutifRef"
                                wire:click="$set('SEPJsonReq.request.t_sep.poli.eksekutif', {{ (isset($SEPJsonReq['request']['t_sep']['poli']['eksekutif']) ? $SEPJsonReq['request']['t_sep']['poli']['eksekutif'] : '0') ? '0' : '1' }})" />
                        </div>

                        @error('SEPJsonReq.request.t_sep.poli.tujuan')
                            <x-input-error :messages="$message" class="col-span-3 col-start-2" />
                        @enderror
                    </div>

                    <!-- DPJP yang melayani -->
                    <div class="grid items-center grid-cols-4 gap-4">
                        <x-input-label for="dpjp" :value="__('DPJP yang melayani')" class="text-right" :required="__($errors->has('SEPJsonReq.request.t_sep.dpjpLayan'))" />

                        <div class="flex items-center col-span-3 gap-2">
                            <x-text-input id="dpjp" placeholder="Isi dengan data yang sesuai" class="flex-1"
                                :errorshas="__($errors->has('SEPJsonReq.request.t_sep.dpjpLayan'))" :disabled="$disabledProperty"
                                value="{{ (isset($SEPJsonReq['request']['t_sep']['dpjpLayan']) ? $SEPJsonReq['request']['t_sep']['dpjpLayan'] : 'Data tidak dapat di proses') . '   ' . (isset($SEPJsonReq['request']['t_sep']['kodeDPJP']['dpjpLayanNama']) ? $SEPJsonReq['request']['t_sep']['kodeDPJP']['dpjpLayanNama'] : '') }}" />

                            <x-green-button :disabled="false" class="px-3"
                                wire:click.prevent="clickdataDokterBPJSlov()">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path clip-rule="evenodd" fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                                </svg>
                            </x-green-button>
                        </div>

                        @error('SEPJsonReq.request.t_sep.dpjpLayan')
                            <x-input-error :messages="$message" class="col-span-3 col-start-2" />
                        @enderror

                        <!-- LOV Dokter -->
                        <div class="relative col-span-3 col-start-2 mt-0 bg-red-300">
                            @include('livewire.daftar-u-g-d.form-entry-u-g-d.list-of-value-caridatadokterBpjs')
                        </div>
                    </div>

                    <!-- Asal Rujukan -->
                    <div class="grid items-center grid-cols-4 gap-4">
                        <x-input-label for="asalRujukan" :value="__('Asal Rujukan')" class="text-right" :required="__($errors->has('SEPJsonReq.request.t_sep.rujukan.asalRujukan'))" />

                        <div class="col-span-3">
                            <x-text-input id="asalRujukan" placeholder="Isi dengan data yang sesuai" class="w-full"
                                :errorshas="__($errors->has('SEPJsonReq.request.t_sep.rujukan.asalRujukan'))" :disabled="$disabledProperty"
                                value="{{ (isset($SEPJsonReq['request']['t_sep']['rujukan']['asalRujukan']) ? $SEPJsonReq['request']['t_sep']['rujukan']['asalRujukan'] : 'Data tidak dapat di proses') . '   ' . (isset($SEPJsonReq['request']['t_sep']['rujukan']['asalRujukanNama']) ? $SEPJsonReq['request']['t_sep']['rujukan']['asalRujukanNama'] : 'Data tidak dapat di proses') }}" />
                        </div>

                        @error('SEPJsonReq.request.t_sep.rujukan.asalRujukan')
                            <x-input-error :messages="$message" class="col-span-3 col-start-2" />
                        @enderror
                    </div>

                    <!-- PPK Asal Rujukan -->
                    <div class="grid items-center grid-cols-4 gap-4">
                        <x-input-label for="ppkRujukan" :value="__('PPK Asal Rujukan')" class="text-right" :required="__($errors->has('SEPJsonReq.request.t_sep.rujukan.ppkRujukan'))" />

                        <div class="col-span-3">
                            <x-text-input id="ppkRujukan" placeholder="Isi dengan data yang sesuai" class="w-full"
                                :errorshas="__($errors->has('SEPJsonReq.request.t_sep.rujukan.ppkRujukan'))" :disabled="$disabledProperty"
                                value="{{ (isset($SEPJsonReq['request']['t_sep']['rujukan']['ppkRujukan']) ? $SEPJsonReq['request']['t_sep']['rujukan']['ppkRujukan'] : 'Data tidak dapat di proses') . '   ' . (isset($SEPJsonReq['request']['t_sep']['rujukan']['ppkRujukanNama']) ? $SEPJsonReq['request']['t_sep']['rujukan']['ppkRujukanNama'] : 'Data tidak dapat di proses') }}" />
                        </div>

                        @error('SEPJsonReq.request.t_sep.rujukan.ppkRujukan')
                            <x-input-error :messages="$message" class="col-span-3 col-start-2" />
                        @enderror
                    </div>

                    <!-- Tanggal Rujukan -->
                    <div class="grid items-center grid-cols-4 gap-4">
                        <x-input-label for="tglRujukan" :value="__('Tanggal Rujukan')" class="text-right" :required="__($errors->has('SEPJsonReq.request.t_sep.rujukan.tglRujukan'))" />

                        <div class="col-span-3">
                            <x-text-input id="tglRujukan" placeholder="Isi dengan data yang sesuai" class="w-full"
                                :errorshas="__($errors->has('SEPJsonReq.request.t_sep.rujukan.tglRujukan'))" :disabled="$disabledProperty"
                                wire:model.debounce.500ms="SEPJsonReq.request.t_sep.rujukan.tglRujukan" />
                        </div>

                        @error('SEPJsonReq.request.t_sep.rujukan.tglRujukan')
                            <x-input-error :messages="$message" class="col-span-3 col-start-2" />
                        @enderror
                    </div>

                    <!-- Nomor SKDP (Kondisional) -->
                    @if ($JenisKunjungan['JenisKunjunganId'] == 3)
                        <div class="grid items-center grid-cols-4 gap-4">
                            <x-input-label for="noSurat" :value="__('Nomor SKDP')" class="text-right" :required="__($errors->has('SEPJsonReq.request.t_sep.skdp.noSurat'))" />

                            <div class="col-span-3">
                                <x-text-input id="noSurat" placeholder="Isi dengan data yang sesuai" class="w-full"
                                    :errorshas="__($errors->has('SEPJsonReq.request.t_sep.skdp.noSurat'))" :disabled="false"
                                    wire:model.debounce.500ms="SEPJsonReq.request.t_sep.skdp.noSurat" />
                            </div>

                            @error('SEPJsonReq.request.t_sep.skdp.noSurat')
                                <x-input-error :messages="$message" class="col-span-3 col-start-2" />
                            @enderror
                        </div>
                    @endif

                    <!-- Tanggal SEP -->
                    <div class="grid items-center grid-cols-4 gap-4">
                        <x-input-label for="tglSep" :value="__('Tanggal SEP')" class="text-right" :required="__($errors->has('SEPJsonReq.request.t_sep.tglSep'))" />

                        <div class="col-span-3">
                            <x-text-input id="tglSep" placeholder="Isi dengan data yang sesuai" class="w-full"
                                :errorshas="__($errors->has('SEPJsonReq.request.t_sep.tglSep'))" :disabled="$disabledProperty"
                                wire:model.debounce.500ms="SEPJsonReq.request.t_sep.tglSep" />
                        </div>

                        @error('SEPJsonReq.request.t_sep.tglSep')
                            <x-input-error :messages="$message" class="col-span-3 col-start-2" />
                        @enderror
                    </div>

                    <!-- No MR dan Opsi Tambahan -->
                    <div class="grid grid-cols-4 gap-4">
                        <x-input-label for="noMR" :value="__('No MR')" class="self-center text-right"
                            :required="__($errors->has('SEPJsonReq.request.t_sep.noMR'))" />

                        <div class="col-span-3 space-y-2">
                            <x-text-input id="noMR" placeholder="Isi dengan data yang sesuai"
                                class="w-full md:w-1/4" :errorshas="__($errors->has('SEPJsonReq.request.t_sep.noMR'))" :disabled="false"
                                wire:model.debounce.500ms="SEPJsonReq.request.t_sep.noMR" />

                            <div class="flex flex-wrap items-center gap-4">
                                <x-check-box value="1" :label="__('Peserta COB')"
                                    wire:model.debounce.500ms="SEPJsonReq.request.t_sep.cob.cobRef"
                                    wire:click="$set('SEPJsonReq.request.t_sep.cob.cob', {{ (isset($SEPJsonReq['request']['t_sep']['cob']['cob']) ? $SEPJsonReq['request']['t_sep']['cob']['cob'] : '0') ? '0' : '1' }})" />

                                <div class="flex items-center space-x-4">
                                    <label class="inline-flex items-center">
                                        <input type="radio" class="form-radio text-primary"
                                            wire:model="SEPJsonReq.request.t_sep.katarak.katarak" value="0"
                                            @if ($disabledProperty) disabled @endif>
                                        <span class="ml-2">Non-Katarak</span>
                                    </label>

                                    <label class="inline-flex items-center">
                                        <input type="radio" class="form-radio text-primary"
                                            wire:model="SEPJsonReq.request.t_sep.katarak.katarak" value="1"
                                            @if ($disabledProperty) disabled @endif>
                                        <span class="ml-2">Katarak</span>
                                    </label>
                                </div>
                            </div>

                            @error('SEPJsonReq.request.t_sep.noMR')
                                <x-input-error :messages="$message" />
                            @enderror

                            @error('SEPJsonReq.request.t_sep.cob.cob')
                                <x-input-error :messages="$message" />
                            @enderror

                            @error('SEPJsonReq.request.t_sep.katarak.katarak')
                                <x-input-error :messages="$message" />
                            @enderror
                        </div>
                    </div>

                    <!-- Diagnosa -->
                    <div class="grid items-center grid-cols-4 gap-4">
                        <x-input-label for="diagAwal" :value="__('Diagnosa')" class="text-right" :required="__($errors->has('SEPJsonReq.request.t_sep.diagAwal'))" />

                        <div class="flex items-center col-span-3 gap-2">
                            <x-text-input id="diagAwal" placeholder="Isi dengan data yang sesuai" class="flex-1"
                                :errorshas="__($errors->has('SEPJsonReq.request.t_sep.diagAwal'))" :disabled="$disabledProperty"
                                value="{{ (isset($SEPJsonReq['request']['t_sep']['diagAwal']) ? $SEPJsonReq['request']['t_sep']['diagAwal'] : 'Data tidak dapat di proses') . '   ' . (isset($SEPJsonReq['request']['t_sep']['diagAwalNama']) ? $SEPJsonReq['request']['t_sep']['diagAwalNama'] : 'Data tidak dapat di proses') }}" />

                            <x-green-button :disabled="false" class="px-3"
                                wire:click.prevent="clickdataDiagnosaBPJSlov()">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path clip-rule="evenodd" fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                                </svg>
                            </x-green-button>
                        </div>

                        @error('SEPJsonReq.request.t_sep.diagAwal')
                            <x-input-error :messages="$message" class="col-span-3 col-start-2" />
                        @enderror

                        <!-- LOV Diagnosa -->
                        <div class="relative col-span-3 col-start-2 mt-0 bg-red-300">
                            @include('livewire.daftar-u-g-d.form-entry-u-g-d.list-of-value-caridatadiagnosaBpjs')
                        </div>
                    </div>

                    <!-- Nomor Telpon -->
                    <div class="grid items-center grid-cols-4 gap-4">
                        <x-input-label for="noTelp" :value="__('Nomor Telpon')" class="text-right" :required="__($errors->has('SEPJsonReq.request.t_sep.noTelp'))" />

                        <div class="col-span-3">
                            <x-text-input id="noTelp" placeholder="Isi dengan data yang sesuai" class="w-full"
                                :errorshas="__($errors->has('SEPJsonReq.request.t_sep.noTelp'))" :disabled="false"
                                wire:model.debounce.500ms="SEPJsonReq.request.t_sep.noTelp" />
                        </div>

                        @error('SEPJsonReq.request.t_sep.noTelp')
                            <x-input-error :messages="$message" class="col-span-3 col-start-2" />
                        @enderror
                    </div>

                    <!-- Catatan -->
                    <div class="grid items-center grid-cols-4 gap-4">
                        <x-input-label for="catatan" :value="__('Catatan')" class="text-right" :required="__($errors->has('SEPJsonReq.request.t_sep.catatan'))" />

                        <div class="col-span-3">
                            <x-text-input id="catatan" placeholder="Isi dengan data yang sesuai" class="w-full"
                                :errorshas="__($errors->has('SEPJsonReq.request.t_sep.catatan'))" :disabled="false"
                                wire:model.debounce.500ms="SEPJsonReq.request.t_sep.catatan" />
                        </div>

                        @error('SEPJsonReq.request.t_sep.catatan')
                            <x-input-error :messages="$message" class="col-span-3 col-start-2" />
                        @enderror
                    </div>

                    <!-- Status Kecelakaan -->
                    <div class="grid items-center grid-cols-4 gap-4">
                        <x-input-label for="lakaLantas" :value="__('Status Kecelakaan')" class="text-right" :required="__($errors->has('SEPJsonReq.request.t_sep.jaminan.lakaLantas'))" />

                        <div class="col-span-3">
                            <x-text-input id="lakaLantas" placeholder="Isi dengan data yang sesuai" class="w-full"
                                :errorshas="__($errors->has('SEPJsonReq.request.t_sep.jaminan.lakaLantas'))" :disabled="$disabledProperty"
                                wire:model.debounce.500ms="SEPJsonReq.request.t_sep.jaminan.lakaLantas" />
                        </div>

                        @error('SEPJsonReq.request.t_sep.jaminan.lakaLantas')
                            <x-input-error :messages="$message" class="col-span-3 col-start-2" />
                        @enderror
                    </div>

                    <!-- Bagian Kondisional untuk Jenis Kunjungan 2 atau 3 -->
                    @if ($JenisKunjungan['JenisKunjunganId'] == 2 || $JenisKunjungan['JenisKunjunganId'] == 3)
                        @php
                            $myTujuanKunj = isset($SEPJsonReq['request']['t_sep']['tujuanKunj'])
                                ? $SEPJsonReq['request']['t_sep']['tujuanKunj']
                                : '0';
                        @endphp

                        <!-- Tujuan Kunjungan -->
                        <div class="grid items-center grid-cols-4 gap-4">
                            <x-input-label for="tujuanKunj" :value="__('Tujuan Kunjungan')" class="text-right"
                                :required="__($errors->has('SEPJsonReq.request.t_sep.tujuanKunj'))" />

                            <div class="col-span-3">
                                <x-dropdown align="right" width="80">
                                    <x-slot name="trigger">
                                        <x-alternative-button wire:click.prevent=""
                                            class="flex items-center justify-between w-full">
                                            <span>
                                                {{ isset($SEPJsonReq['request']['t_sep']['tujuanKunj']) ? $SEPJsonReq['request']['t_sep']['tujuanKunj'] : 'Data tidak dapat di proses' }}
                                                {{ isset($SEPJsonReq['request']['t_sep']['tujuanKunjDesc']) ? $SEPJsonReq['request']['t_sep']['tujuanKunjDesc'] : 'Data tidak dapat diproses' }}
                                            </span>
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                                                xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                                <path clip-rule="evenodd" fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                                            </svg>
                                        </x-alternative-button>
                                    </x-slot>

                                    <x-slot name="content">
                                        @foreach ($SEPQuestionnaire['tujuanKunj'] as $tujuanKunj)
                                            <x-dropdown-link
                                                wire:click="settujuanKunj('{{ $tujuanKunj['tujuanKunjId'] }}','{{ $tujuanKunj['tujuanKunjDesc'] }}')">
                                                {{ $tujuanKunj['tujuanKunjId'] }} {{ $tujuanKunj['tujuanKunjDesc'] }}
                                            </x-dropdown-link>
                                        @endforeach
                                    </x-slot>
                                </x-dropdown>
                            </div>
                        </div>

                        <!-- Flag Procedure (Kondisional) -->
                        @if ($myTujuanKunj != '0')
                            <div class="grid items-center grid-cols-4 gap-4">
                                <x-input-label for="flagProcedure" :value="__('Flag Procedure')" class="text-right"
                                    :required="__($errors->has('SEPJsonReq.request.t_sep.flagProcedure'))" />

                                <div class="col-span-3">
                                    <x-dropdown align="right" width="80">
                                        <x-slot name="trigger">
                                            <x-alternative-button wire:click.prevent=""
                                                class="flex items-center justify-between w-full">
                                                <span>
                                                    {{ isset($SEPJsonReq['request']['t_sep']['flagProcedure']) ? $SEPJsonReq['request']['t_sep']['flagProcedure'] : 'Data tidak dapat di proses' }}
                                                    {{ isset($SEPJsonReq['request']['t_sep']['flagProcedureDesc']) ? $SEPJsonReq['request']['t_sep']['flagProcedureDesc'] : 'Data tidak dapat di proses' }}
                                                </span>
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                                                    xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                                    <path clip-rule="evenodd" fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                                                </svg>
                                            </x-alternative-button>
                                        </x-slot>

                                        <x-slot name="content">
                                            @foreach ($SEPQuestionnaire['flagProcedure'] as $flagProcedure)
                                                <x-dropdown-link
                                                    wire:click="setflagProcedure('{{ $flagProcedure['flagProcedureId'] }}','{{ $flagProcedure['flagProcedureDesc'] }}')">
                                                    {{ $flagProcedure['flagProcedureId'] }}
                                                    {{ $flagProcedure['flagProcedureDesc'] }}
                                                </x-dropdown-link>
                                            @endforeach
                                        </x-slot>
                                    </x-dropdown>
                                </div>
                            </div>

                            <!-- Kode Penunjang -->
                            <div class="grid items-center grid-cols-4 gap-4">
                                <x-input-label for="kdPenunjang" :value="__('Kode Penunjang')" class="text-right"
                                    :required="__($errors->has('SEPJsonReq.request.t_sep.kdPenunjang'))" />

                                <div class="col-span-3">
                                    <x-dropdown align="right" width="80">
                                        <x-slot name="trigger">
                                            <x-alternative-button wire:click.prevent=""
                                                class="flex items-center justify-between w-full">
                                                <span>
                                                    {{ isset($SEPJsonReq['request']['t_sep']['kdPenunjang']) ? $SEPJsonReq['request']['t_sep']['kdPenunjang'] : 'Data tidak dapat diproses' }}
                                                    {{ isset($SEPJsonReq['request']['t_sep']['kdPenunjangDesc']) ? $SEPJsonReq['request']['t_sep']['kdPenunjangDesc'] : 'Data tidak dapat di proses' }}
                                                </span>
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                                                    xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                                    <path clip-rule="evenodd" fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                                                </svg>
                                            </x-alternative-button>
                                        </x-slot>

                                        <x-slot name="content">
                                            @foreach ($SEPQuestionnaire['kdPenunjang'] as $kdPenunjang)
                                                <x-dropdown-link
                                                    wire:click="setkdPenunjang('{{ $kdPenunjang['kdPenunjangId'] }}','{{ $kdPenunjang['kdPenunjangDesc'] }}')">
                                                    {{ $kdPenunjang['kdPenunjangId'] }}
                                                    {{ $kdPenunjang['kdPenunjangDesc'] }}
                                                </x-dropdown-link>
                                            @endforeach
                                        </x-slot>
                                    </x-dropdown>
                                </div>
                            </div>
                        @endif

                        <!-- Assesment Pel (Kondisional) -->
                        @if ($myTujuanKunj == '0' || $myTujuanKunj == '2')
                            <div class="grid items-center grid-cols-4 gap-4">
                                <x-input-label for="assesmentPel" :value="__('Assesment Pel')" class="text-right"
                                    :required="__($errors->has('SEPJsonReq.request.t_sep.assesmentPel'))" />

                                <div class="col-span-3">
                                    <x-dropdown align="right" width="80">
                                        <x-slot name="trigger">
                                            <x-alternative-button wire:click.prevent=""
                                                class="flex items-center justify-between w-full">
                                                <span>
                                                    {{ isset($SEPJsonReq['request']['t_sep']['assesmentPel']) ? $SEPJsonReq['request']['t_sep']['assesmentPel'] : 'Data tidak dapat di proses' }}
                                                    {{ isset($SEPJsonReq['request']['t_sep']['assesmentPelDesc']) ? $SEPJsonReq['request']['t_sep']['assesmentPelDesc'] : 'Data tidak dapat di proses' }}
                                                </span>
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                                                    xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                                    <path clip-rule="evenodd" fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                                                </svg>
                                            </x-alternative-button>
                                        </x-slot>

                                        <x-slot name="content">
                                            @foreach ($SEPQuestionnaire['assesmentPel'] as $assesmentPel)
                                                <x-dropdown-link
                                                    wire:click="setassesmentPel('{{ $assesmentPel['assesmentPelId'] }}','{{ $assesmentPel['assesmentPelDesc'] }}')">
                                                    {{ $assesmentPel['assesmentPelId'] }}
                                                    {{ $assesmentPel['assesmentPelDesc'] }}
                                                </x-dropdown-link>
                                            @endforeach
                                        </x-slot>
                                    </x-dropdown>
                                </div>
                            </div>
                        @endif
                    @endif
                </x-border-form>




                <div class="sticky bottom-0 flex justify-between px-4 py-3 bg-gray-50 sm:px-6">

                    <div class="">
                        {{-- <x-primary-button :disabled=$disabledPropertyRj wire:click.prevent="callFormPasien()"
                            type="button" wire:loading.remove>
                            Master Pasien
                        </x-primary-button>
                        <div wire:loading wire:target="callFormPasien">
                            <x-loading />
                        </div> --}}
                    </div>
                    <div>
                        @if ($isOpenMode !== 'tampil')
                            <div wire:loading wire:target="storeDataSepReq">
                                <x-loading />
                            </div>


                            <x-green-button :disabled=$disabledPropertyRjStatus wire:click.prevent="storeDataSepReq()"
                                type="button" wire:loading.remove>
                                Simpan
                            </x-green-button>
                        @endif
                        <x-light-button wire:click.prevent="storeDataSepReq()" type="button">Keluar</x-light-button>
                    </div>
                </div>


            </form>

        </div>



    </div>

</div>
