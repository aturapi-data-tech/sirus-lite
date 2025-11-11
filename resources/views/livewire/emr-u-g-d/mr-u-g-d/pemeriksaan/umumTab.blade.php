<div>
    <div class="w-full mb-1">
        <div class="pt-0">
            <!-- Keadaan Umum -->
            <x-input-label for="dataDaftarUgd.pemeriksaan.tandaVital.keadaanUmum" value="Keadaan Umum" :required="false"
                class="pt-2 sm:text-xl" />
            {{-- @dd($dataDaftarUgd) --}}
            <div class="mb-4">
                <x-text-input id="dataDaftarUgd.pemeriksaan.tandaVital.keadaanUmum"
                    placeholder="Deskripsikan keadaan umum pasien" class="w-full mt-1" :errorshas="$errors->has('dataDaftarUgd.pemeriksaan.tandaVital.keadaanUmum')" :disabled="$disabledPropertyRjStatus"
                    wire:model.debounce.500ms="dataDaftarUgd.pemeriksaan.tandaVital.keadaanUmum" />
                @error('dataDaftarUgd.pemeriksaan.tandaVital.keadaanUmum')
                    <x-input-error :messages="$message" class="mt-1" />
                @enderror
            </div>

            <!-- Tingkat Kesadaran -->
            <x-input-label for="dataDaftarUgd.pemeriksaan.tandaVital.tingkatKesadaran" value="Tingkat Kesadaran"
                :required="false" />

            <div class="mt-1">
                <div class="flex">
                    <x-text-input placeholder="Tingkat Kesadaran" class="flex-1 sm:rounded-none sm:rounded-l-lg"
                        :errorshas="$errors->has('dataDaftarUgd.pemeriksaan.tandaVital.tingkatKesadaran')" :disabled="true"
                        value="{{ $dataDaftarUgd['pemeriksaan']['tandaVital']['tingkatKesadaran'] ?? '' }}" />

                    <x-green-button :disabled="$disabledPropertyRjStatus" class="sm:rounded-none sm:rounded-r-lg sm:mb-0 sm:mr-0 sm:px-3"
                        wire:click.prevent="clickTingkatKesadaranLov()">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"
                            aria-hidden="true">
                            <path clip-rule="evenodd" fill-rule="evenodd"
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                        </svg>
                    </x-green-button>
                </div>
                @error('dataDaftarUgd.pemeriksaan.tandaVital.tingkatKesadaran')
                    <x-input-error :messages="$message" class="mt-1" />
                @enderror
                <!-- LOV tingkatKesadaran -->
                @include('livewire.emr-u-g-d.mr-u-g-d.pemeriksaan.list-of-value-tingkatKesadaran')
            </div>

            <!-- ABCD Assessment -->
            <div class="grid grid-cols-1 gap-4 mt-4">
                <!-- Jalan Nafas A -->
                <div class="p-4 border border-blue-200 rounded-lg bg-blue-50">
                    <x-input-label value="Jalan Nafas (A)" :required="false"
                        class="text-lg font-semibold text-blue-800" />
                    <div class="grid grid-cols-2 gap-3 my-3 md:grid-cols-4">
                        @foreach ($dataDaftarUgd['pemeriksaan']['tandaVital']['jalanNafas']['jalanNafasOptions'] as $jalanNafasOptions)
                            <x-radio-button :label="$jalanNafasOptions['jalanNafas']" value="{{ $jalanNafasOptions['jalanNafas'] }}"
                                wire:model="dataDaftarUgd.pemeriksaan.tandaVital.jalanNafas.jalanNafas" />
                        @endforeach
                    </div>
                    @error('dataDaftarUgd.pemeriksaan.tandaVital.jalanNafas.jalanNafas')
                        <x-input-error :messages="$message" class="mt-1" />
                    @enderror
                </div>

                <!-- Pernafasan B -->
                <div class="p-4 border border-green-200 rounded-lg bg-green-50">
                    <x-input-label value="Pernafasan (B)" :required="false"
                        class="text-lg font-semibold text-green-800" />
                    <div class="grid grid-cols-2 gap-3 my-3 md:grid-cols-5">
                        @foreach ($dataDaftarUgd['pemeriksaan']['tandaVital']['pernafasan']['pernafasanOptions'] as $pernafasanOptions)
                            <x-radio-button :label="$pernafasanOptions['pernafasan']" value="{{ $pernafasanOptions['pernafasan'] }}"
                                wire:model="dataDaftarUgd.pemeriksaan.tandaVital.pernafasan.pernafasan" />
                        @endforeach
                    </div>
                    @error('dataDaftarUgd.pemeriksaan.tandaVital.pernafasan.pernafasan')
                        <x-input-error :messages="$message" class="mt-1" />
                    @enderror

                    <x-input-label value="Gerak Dada" :required="false" class="mt-4 font-medium" />
                    <div class="grid grid-cols-1 gap-3 my-3 md:grid-cols-2">
                        @foreach ($dataDaftarUgd['pemeriksaan']['tandaVital']['gerakDada']['gerakDadaOptions'] as $gerakDadaOptions)
                            <x-radio-button :label="$gerakDadaOptions['gerakDada']" value="{{ $gerakDadaOptions['gerakDada'] }}"
                                wire:model="dataDaftarUgd.pemeriksaan.tandaVital.gerakDada.gerakDada" />
                        @endforeach
                    </div>
                    @error('dataDaftarUgd.pemeriksaan.tandaVital.gerakDada.gerakDada')
                        <x-input-error :messages="$message" class="mt-1" />
                    @enderror
                </div>

                <!-- Sirkulasi C -->
                <div class="p-4 border border-red-200 rounded-lg bg-red-50">
                    <x-input-label value="Sirkulasi (C)" :required="false"
                        class="text-lg font-semibold text-red-800" />
                    <div class="grid grid-cols-2 gap-3 my-3 md:grid-cols-5">
                        @foreach ($dataDaftarUgd['pemeriksaan']['tandaVital']['sirkulasi']['sirkulasiOptions'] as $sirkulasiOptions)
                            <x-radio-button :label="$sirkulasiOptions['sirkulasi']" value="{{ $sirkulasiOptions['sirkulasi'] }}"
                                wire:model="dataDaftarUgd.pemeriksaan.tandaVital.sirkulasi.sirkulasi" />
                        @endforeach
                    </div>
                    @error('dataDaftarUgd.pemeriksaan.tandaVital.sirkulasi.sirkulasi')
                        <x-input-error :messages="$message" class="mt-1" />
                    @enderror
                </div>

                <!-- Neurologis D -->
                <div class="p-4 border border-purple-200 rounded-lg bg-purple-50">
                    <x-input-label value="Neurologis (D)" :required="false"
                        class="text-lg font-semibold text-purple-800" />
                    <div class="grid grid-cols-1 gap-4 my-3 md:grid-cols-4">
                        <div>
                            <x-input-label for="dataDaftarUgd.pemeriksaan.tandaVital.e" value="E [Eye]"
                                :required="false" />
                            <x-text-input id="dataDaftarUgd.pemeriksaan.tandaVital.e" placeholder="Eye"
                                class="w-full mt-1" :errorshas="$errors->has('dataDaftarUgd.pemeriksaan.tandaVital.e')" :disabled="$disabledPropertyRjStatus"
                                wire:model.debounce.500ms="dataDaftarUgd.pemeriksaan.tandaVital.e" />
                            @error('dataDaftarUgd.pemeriksaan.tandaVital.e')
                                <x-input-error :messages="$message" class="mt-1" />
                            @enderror
                        </div>
                        <div>
                            <x-input-label for="dataDaftarUgd.pemeriksaan.tandaVital.v" value="V [Verbal]"
                                :required="false" />
                            <x-text-input id="dataDaftarUgd.pemeriksaan.tandaVital.v" placeholder="Verbal"
                                class="w-full mt-1" :errorshas="$errors->has('dataDaftarUgd.pemeriksaan.tandaVital.v')" :disabled="$disabledPropertyRjStatus"
                                wire:model.debounce.500ms="dataDaftarUgd.pemeriksaan.tandaVital.v" />
                            @error('dataDaftarUgd.pemeriksaan.tandaVital.v')
                                <x-input-error :messages="$message" class="mt-1" />
                            @enderror
                        </div>
                        <div>
                            <x-input-label for="dataDaftarUgd.pemeriksaan.tandaVital.m" value="M [Motorik]"
                                :required="false" />
                            <x-text-input id="dataDaftarUgd.pemeriksaan.tandaVital.m" placeholder="Motorik"
                                class="w-full mt-1" :errorshas="$errors->has('dataDaftarUgd.pemeriksaan.tandaVital.m')" :disabled="$disabledPropertyRjStatus"
                                wire:model.debounce.500ms="dataDaftarUgd.pemeriksaan.tandaVital.m" />
                            @error('dataDaftarUgd.pemeriksaan.tandaVital.m')
                                <x-input-error :messages="$message" class="mt-1" />
                            @enderror
                        </div>
                        <div>
                            <x-input-label for="dataDaftarUgd.pemeriksaan.tandaVital.gcs" value="GCS Total"
                                :required="false" />
                            <x-text-input id="dataDaftarUgd.pemeriksaan.tandaVital.gcs" placeholder="GCS"
                                class="w-full mt-1" :errorshas="$errors->has('dataDaftarUgd.pemeriksaan.tandaVital.gcs')" :disabled="$disabledPropertyRjStatus"
                                wire:model.debounce.500ms="dataDaftarUgd.pemeriksaan.tandaVital.gcs" />
                            @error('dataDaftarUgd.pemeriksaan.tandaVital.gcs')
                                <x-input-error :messages="$message" class="mt-1" />
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tanda Vital -->
            <x-input-label value="Tanda Vital" :required="false" class="pt-6 mt-6 border-t sm:text-xl" />

            <!-- Tekanan Darah -->
            <div class="mb-4">
                <x-input-label value="Tekanan Darah" :required="false" class="font-medium" />
                <div class="grid grid-cols-1 gap-4 mt-2 md:grid-cols-2">
                    <div>
                        <x-text-input-mou id="dataDaftarUgd.pemeriksaan.tandaVital.sistolik" placeholder="Sistolik"
                            class="w-full" :errorshas="$errors->has('dataDaftarUgd.pemeriksaan.tandaVital.sistolik')" :disabled="$disabledPropertyRjStatus" :mou_label="'mmHg'"
                            wire:model.debounce.500ms="dataDaftarUgd.pemeriksaan.tandaVital.sistolik" />
                        @error('dataDaftarUgd.pemeriksaan.tandaVital.sistolik')
                            <x-input-error :messages="$message" class="mt-1" />
                        @enderror
                    </div>
                    <div>
                        <x-text-input-mou id="dataDaftarUgd.pemeriksaan.tandaVital.distolik" placeholder="Distolik"
                            class="w-full" :errorshas="$errors->has('dataDaftarUgd.pemeriksaan.tandaVital.distolik')" :disabled="$disabledPropertyRjStatus" :mou_label="'mmHg'"
                            wire:model.debounce.500ms="dataDaftarUgd.pemeriksaan.tandaVital.distolik" />
                        @error('dataDaftarUgd.pemeriksaan.tandaVital.distolik')
                            <x-input-error :messages="$message" class="mt-1" />
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Frekuensi Nadi & Nafas -->
            <div class="mb-4">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <x-input-label value="Frekuensi Nadi" :required="false" />
                        <x-text-input-mou id="dataDaftarUgd.pemeriksaan.tandaVital.frekuensiNadi"
                            placeholder="Frekuensi Nadi" class="w-full mt-1" :errorshas="$errors->has('dataDaftarUgd.pemeriksaan.tandaVital.frekuensiNadi')" :disabled="$disabledPropertyRjStatus"
                            :mou_label="'x / menit'"
                            wire:model.debounce.500ms="dataDaftarUgd.pemeriksaan.tandaVital.frekuensiNadi" />
                        @error('dataDaftarUgd.pemeriksaan.tandaVital.frekuensiNadi')
                            <x-input-error :messages="$message" class="mt-1" />
                        @enderror
                    </div>
                    <div>
                        <x-input-label value="Frekuensi Nafas" :required="false" />
                        <x-text-input-mou id="dataDaftarUgd.pemeriksaan.tandaVital.frekuensiNafas"
                            placeholder="Frekuensi Nafas" class="w-full mt-1" :errorshas="$errors->has('dataDaftarUgd.pemeriksaan.tandaVital.frekuensiNafas')" :disabled="$disabledPropertyRjStatus"
                            :mou_label="'x / menit'"
                            wire:model.debounce.500ms="dataDaftarUgd.pemeriksaan.tandaVital.frekuensiNafas" />
                        @error('dataDaftarUgd.pemeriksaan.tandaVital.frekuensiNafas')
                            <x-input-error :messages="$message" class="mt-1" />
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Suhu -->
            <div class="mb-4">
                <x-input-label value="Suhu" :required="false" />
                <x-text-input-mou id="dataDaftarUgd.pemeriksaan.tandaVital.suhu" placeholder="Suhu"
                    class="w-full mt-1" :errorshas="$errors->has('dataDaftarUgd.pemeriksaan.tandaVital.suhu')" :disabled="$disabledPropertyRjStatus" :mou_label="'°C'"
                    wire:model.debounce.500ms="dataDaftarUgd.pemeriksaan.tandaVital.suhu" />
                @error('dataDaftarUgd.pemeriksaan.tandaVital.suhu')
                    <x-input-error :messages="$message" class="mt-1" />
                @enderror
            </div>

            <!-- SPO2 & GDA -->
            <div class="mb-6">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <x-input-label value="SPO2" :required="false" />
                        <x-text-input-mou id="dataDaftarUgd.pemeriksaan.tandaVital.spo2" placeholder="SPO2"
                            class="w-full mt-1" :errorshas="$errors->has('dataDaftarUgd.pemeriksaan.tandaVital.spo2')" :disabled="$disabledPropertyRjStatus" :mou_label="'%'"
                            wire:model.debounce.500ms="dataDaftarUgd.pemeriksaan.tandaVital.spo2" />
                        @error('dataDaftarUgd.pemeriksaan.tandaVital.spo2')
                            <x-input-error :messages="$message" class="mt-1" />
                        @enderror
                    </div>
                    <div>
                        <x-input-label value="Gula Darah Acak (GDA)" :required="false" />
                        <x-text-input-mou id="dataDaftarUgd.pemeriksaan.tandaVital.gda" placeholder="GDA"
                            class="w-full mt-1" :errorshas="$errors->has('dataDaftarUgd.pemeriksaan.tandaVital.gda')" :disabled="$disabledPropertyRjStatus" :mou_label="'mg / dL'"
                            wire:model.debounce.500ms="dataDaftarUgd.pemeriksaan.tandaVital.gda" />
                        @error('dataDaftarUgd.pemeriksaan.tandaVital.gda')
                            <x-input-error :messages="$message" class="mt-1" />
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Nutrisi -->
            <x-input-label value="Data Nutrisi" :required="false" class="pt-6 border-t sm:text-xl" />

            <div class="grid grid-cols-1 gap-4 pt-4 md:grid-cols-3">
                <div>
                    <x-input-label for="dataDaftarUgd.pemeriksaan.nutrisi.bb" value="Berat Badan"
                        :required="false" />
                    <x-text-input-mou id="dataDaftarUgd.pemeriksaan.nutrisi.bb" placeholder="Berat Badan"
                        class="w-full mt-1" :errorshas="$errors->has('dataDaftarUgd.pemeriksaan.nutrisi.bb')" :disabled="$disabledPropertyRjStatus"
                        wire:model.debounce.500ms="dataDaftarUgd.pemeriksaan.nutrisi.bb" :mou_label="'Kg'" />
                    @error('dataDaftarUgd.pemeriksaan.nutrisi.bb')
                        <x-input-error :messages="$message" class="mt-1" />
                    @enderror
                </div>

                <div>
                    <x-input-label for="dataDaftarUgd.pemeriksaan.nutrisi.tb" value="Tinggi Badan"
                        :required="false" />
                    <x-text-input-mou id="dataDaftarUgd.pemeriksaan.nutrisi.tb" placeholder="Tinggi Badan"
                        class="w-full mt-1" :errorshas="$errors->has('dataDaftarUgd.pemeriksaan.nutrisi.tb')" :disabled="$disabledPropertyRjStatus"
                        wire:model.debounce.500ms="dataDaftarUgd.pemeriksaan.nutrisi.tb" :mou_label="'Cm'" />
                    @error('dataDaftarUgd.pemeriksaan.nutrisi.tb')
                        <x-input-error :messages="$message" class="mt-1" />
                    @enderror
                </div>

                <div>
                    <x-input-label for="dataDaftarUgd.pemeriksaan.nutrisi.imt" value="Index Masa Tubuh"
                        :required="false" />
                    <x-text-input-mou id="dataDaftarUgd.pemeriksaan.nutrisi.imt" placeholder="IMT"
                        class="w-full mt-1" :errorshas="$errors->has('dataDaftarUgd.pemeriksaan.nutrisi.imt')" :disabled="$disabledPropertyRjStatus"
                        wire:model.debounce.500ms="dataDaftarUgd.pemeriksaan.nutrisi.imt" :mou_label="'Kg / M²'" />
                    @error('dataDaftarUgd.pemeriksaan.nutrisi.imt')
                        <x-input-error :messages="$message" class="mt-1" />
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 pt-4 md:grid-cols-2">
                <div>
                    <x-input-label for="dataDaftarUgd.pemeriksaan.nutrisi.lk" value="Lingkar Kepala"
                        :required="false" />
                    <x-text-input-mou id="dataDaftarUgd.pemeriksaan.nutrisi.lk" placeholder="Lingkar Kepala"
                        class="w-full mt-1" :errorshas="$errors->has('dataDaftarUgd.pemeriksaan.nutrisi.lk')" :disabled="$disabledPropertyRjStatus"
                        wire:model.debounce.500ms="dataDaftarUgd.pemeriksaan.nutrisi.lk" :mou_label="'Cm'" />
                    @error('dataDaftarUgd.pemeriksaan.nutrisi.lk')
                        <x-input-error :messages="$message" class="mt-1" />
                    @enderror
                </div>

                <div>
                    <x-input-label for="dataDaftarUgd.pemeriksaan.nutrisi.lila" value="Lingkar Lengan Atas"
                        :required="false" />
                    <x-text-input-mou id="dataDaftarUgd.pemeriksaan.nutrisi.lila" placeholder="Lingkar Lengan Atas"
                        class="w-full mt-1" :errorshas="$errors->has('dataDaftarUgd.pemeriksaan.nutrisi.lila')" :disabled="$disabledPropertyRjStatus"
                        wire:model.debounce.500ms="dataDaftarUgd.pemeriksaan.nutrisi.lila" :mou_label="'Cm'" />
                    @error('dataDaftarUgd.pemeriksaan.nutrisi.lila')
                        <x-input-error :messages="$message" class="mt-1" />
                    @enderror
                </div>
            </div>

            <!-- Fungsional -->
            <x-input-label value="Data Fungsional" :required="false" class="pt-6 border-t sm:text-xl" />

            <div class="grid grid-cols-1 gap-4 pt-4 md:grid-cols-3">
                <div>
                    <x-input-label for="dataDaftarUgd.pemeriksaan.fungsional.alatBantu" value="Alat Bantu"
                        :required="false" />
                    <x-text-input id="dataDaftarUgd.pemeriksaan.fungsional.alatBantu" placeholder="Alat Bantu"
                        class="w-full mt-1" :errorshas="$errors->has('dataDaftarUgd.pemeriksaan.fungsional.alatBantu')" :disabled="$disabledPropertyRjStatus"
                        wire:model.debounce.500ms="dataDaftarUgd.pemeriksaan.fungsional.alatBantu" />
                    @error('dataDaftarUgd.pemeriksaan.fungsional.alatBantu')
                        <x-input-error :messages="$message" class="mt-1" />
                    @enderror
                </div>

                <div>
                    <x-input-label for="dataDaftarUgd.pemeriksaan.fungsional.prothesa" value="Prothesa"
                        :required="false" />
                    <x-text-input id="dataDaftarUgd.pemeriksaan.fungsional.prothesa" placeholder="Prothesa"
                        class="w-full mt-1" :errorshas="$errors->has('dataDaftarUgd.pemeriksaan.fungsional.prothesa')" :disabled="$disabledPropertyRjStatus"
                        wire:model.debounce.500ms="dataDaftarUgd.pemeriksaan.fungsional.prothesa" />
                    @error('dataDaftarUgd.pemeriksaan.fungsional.prothesa')
                        <x-input-error :messages="$message" class="mt-1" />
                    @enderror
                </div>

                <div>
                    <x-input-label for="dataDaftarUgd.pemeriksaan.fungsional.cacatTubuh" value="Cacat Tubuh"
                        :required="false" />
                    <x-text-input id="dataDaftarUgd.pemeriksaan.fungsional.cacatTubuh" placeholder="Cacat Tubuh"
                        class="w-full mt-1" :errorshas="$errors->has('dataDaftarUgd.pemeriksaan.fungsional.cacatTubuh')" :disabled="$disabledPropertyRjStatus"
                        wire:model.debounce.500ms="dataDaftarUgd.pemeriksaan.fungsional.cacatTubuh" />
                    @error('dataDaftarUgd.pemeriksaan.fungsional.cacatTubuh')
                        <x-input-error :messages="$message" class="mt-1" />
                    @enderror
                </div>
            </div>
        </div>
    </div>
</div>
