<div>
    <div class="w-full mb-1">


        <div class="pt-0">

            <x-input-label for="dataDaftarPoliRJ.pemeriksaan.tandaVital.keadaanUmum" :value="__('Keadaan Umum')" :required="__(false)"
                class="pt-2 sm:text-xl" />


            <div class="mb-2 ">
                <x-text-input id="dataDaftarPoliRJ.pemeriksaan.tandaVital.keadaanUmum" placeholder="Keadaan Umum"
                    class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarPoliRJ.pemeriksaan.tandaVital.keadaanUmum'))" :disabled=$disabledPropertyRjStatus
                    wire:model="dataDaftarPoliRJ.pemeriksaan.tandaVital.keadaanUmum" />

            </div>

            <x-input-label for="dataDaftarPoliRJ.pemeriksaan.tandaVital.tingkatKesadaran" :value="__('Tingkat Kesadaran')"
                :required="__(false)" />

            <div class="mt-1">
                <div class="flex ">
                    <x-text-input placeholder="Tingkat Kesadaran" class="sm:rounded-none sm:rounded-l-lg"
                        :errorshas="__($errors->has('dataDaftarPoliRJ.pemeriksaan.tandaVital.tingkatKesadaran'))" :disabled=true
                        value="{{ $dataDaftarPoliRJ['pemeriksaan']['tandaVital']['tingkatKesadaran'] }}" />



                    <x-green-button :disabled=$disabledPropertyRjStatus
                        class="sm:rounded-none sm:rounded-r-lg sm:mb-0 sm:mr-0 sm:px-2"
                        wire:click.prevent="clicktingkatKesadaranlov()">
                        <svg class="-ml-1 mr-1.5 w-5 h-5" fill="currentColor" viewbox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path clip-rule="evenodd" fill-rule="evenodd"
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                        </svg>
                    </x-green-button>
                </div>
                {{-- LOV tingkatKesadaran --}}
                @include('livewire.emr-r-j.mr-r-j.pemeriksaan.list-of-value-tingkatKesadaran')
            </div>


            {{-- Jalan Nafas A --}}
            {{-- <x-input-label for="dataDaftarPoliRJ.pemeriksaan.tandaVital.e" :value="__('Jalan Nafas (A)')" :required="__(false)"
                class="pt-2 sm:text-xl" />

            <div class="grid grid-cols-4 gap-2 my-2">
                @foreach ($dataDaftarPoliRJ['pemeriksaan']['tandaVital']['jalanNafas']['jalanNafasOptions'] as $jalanNafasOptions)
                    <x-radio-button :label="__($jalanNafasOptions['jalanNafas'])" value="{{ $jalanNafasOptions['jalanNafas'] }}"
                        wire:model="dataDaftarPoliRJ.pemeriksaan.tandaVital.jalanNafas.jalanNafas" />
                @endforeach

            </div> --}}

            {{-- Pernafasan B --}}
            {{-- <x-input-label for="dataDaftarPoliRJ.pemeriksaan.tandaVital.e" :value="__('Pernafasan (B)')" :required="__(false)"
                class="pt-2 sm:text-xl" />

            <div class="grid grid-cols-5 gap-2 my-2">
                @foreach ($dataDaftarPoliRJ['pemeriksaan']['tandaVital']['pernafasan']['pernafasanOptions'] as $pernafasanOptions)
                    <x-radio-button :label="__($pernafasanOptions['pernafasan'])" value="{{ $pernafasanOptions['pernafasan'] }}"
                        wire:model="dataDaftarPoliRJ.pemeriksaan.tandaVital.pernafasan.pernafasan" />
                @endforeach

            </div> --}}

            {{-- <x-input-label for="dataDaftarPoliRJ.pemeriksaan.tandaVital.e" :value="__('Gerak Dada')" :required="__(false)" />

            <div class="grid grid-cols-2 gap-2 my-2">
                @foreach ($dataDaftarPoliRJ['pemeriksaan']['tandaVital']['gerakDada']['gerakDadaOptions'] as $gerakDadaOptions)
                    <x-radio-button :label="__($gerakDadaOptions['gerakDada'])" value="{{ $gerakDadaOptions['gerakDada'] }}"
                        wire:model="dataDaftarPoliRJ.pemeriksaan.tandaVital.gerakDada.gerakDada" />
                @endforeach

            </div> --}}

            {{-- Sirkulasi C --}}
            {{-- <x-input-label for="dataDaftarPoliRJ.pemeriksaan.tandaVital.e" :value="__('Sirkulasi (C)')" :required="__(false)"
                class="pt-2 sm:text-xl" />

            <div class="grid grid-cols-5 gap-2 my-2">
                @foreach ($dataDaftarPoliRJ['pemeriksaan']['tandaVital']['sirkulasi']['sirkulasiOptions'] as $sirkulasiOptions)
                    <x-radio-button :label="__($sirkulasiOptions['sirkulasi'])" value="{{ $sirkulasiOptions['sirkulasi'] }}"
                        wire:model="dataDaftarPoliRJ.pemeriksaan.tandaVital.sirkulasi.sirkulasi" />
                @endforeach

            </div> --}}

            {{-- Neurologis D --}}
            {{-- <x-input-label for="dataDaftarPoliRJ.pemeriksaan.tandaVital.e" :value="__('Neurologis (D)')" :required="__(false)"
                class="pt-2 sm:text-xl" />

            <div class="grid grid-cols-4 gap-2 my-2">
                <div class="mb-2 ">
                    <x-input-label for="dataDaftarPoliRJ.pemeriksaan.tandaVital.e" :value="__('E [Eye]')"
                        :required="__(false)" />
                    <x-text-input id="dataDaftarPoliRJ.pemeriksaan.tandaVital.e" placeholder="E [Eye]" class="mt-1 ml-2"
                        :errorshas="__($errors->has('dataDaftarPoliRJ.pemeriksaan.tandaVital.e'))" :disabled=$disabledPropertyRjStatus
                        wire:model="dataDaftarPoliRJ.pemeriksaan.tandaVital.e" />
                </div>

                <div class="mb-2 ">
                    <x-input-label for="dataDaftarPoliRJ.pemeriksaan.tandaVital.m" :value="__('M [Motorik]')"
                        :required="__(false)" />
                    <x-text-input id="dataDaftarPoliRJ.pemeriksaan.tandaVital.m" placeholder="M [Motorik]"
                        class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarPoliRJ.pemeriksaan.tandaVital.m'))" :disabled=$disabledPropertyRjStatus
                        wire:model="dataDaftarPoliRJ.pemeriksaan.tandaVital.m" />
                </div>

                <div class="mb-2 ">
                    <x-input-label for="dataDaftarPoliRJ.pemeriksaan.tandaVital.v" :value="__('V [Verbal]')"
                        :required="__(false)" />
                    <x-text-input id="dataDaftarPoliRJ.pemeriksaan.tandaVital.v" placeholder="V [Verbal]"
                        class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarPoliRJ.pemeriksaan.tandaVital.v'))" :disabled=$disabledPropertyRjStatus
                        wire:model="dataDaftarPoliRJ.pemeriksaan.tandaVital.v" />
                </div>

                <div class="mb-2 ">
                    <x-input-label for="dataDaftarPoliRJ.pemeriksaan.tandaVital.gcs" :value="__('GCS')"
                        :required="__(false)" />
                    <x-text-input id="dataDaftarPoliRJ.pemeriksaan.tandaVital.gcs" placeholder="GCS" class="mt-1 ml-2"
                        :errorshas="__($errors->has('dataDaftarPoliRJ.pemeriksaan.tandaVital.gcs'))" :disabled=$disabledPropertyRjStatus
                        wire:model="dataDaftarPoliRJ.pemeriksaan.tandaVital.gcs" />
                </div>
            </div> --}}


            <x-input-label for="dataDaftarPoliRJ.pemeriksaan.tandaVital.sistolik" :value="__('Tanda Vital')" :required="__(false)"
                class="pt-2 sm:text-xl" />

            <div class="mb-2 ">
                <x-input-label for="dataDaftarPoliRJ.pemeriksaan.tandaVital.sistolik" :value="__('Tekanan Darah')"
                    :required="__(false)" />
                <div class="grid grid-cols-2 gap-2">
                    <x-text-input-mou id="dataDaftarPoliRJ.pemeriksaan.tandaVital.sistolik" placeholder="Sistolik"
                        class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarPoliRJ.pemeriksaan.tandaVital.sistolik'))" :disabled=$disabledPropertyRjStatus :mou_label="__('mmHg')"
                        wire:model="dataDaftarPoliRJ.pemeriksaan.tandaVital.sistolik" />
                    <x-text-input-mou id="dataDaftarPoliRJ.pemeriksaan.tandaVital.distolik" placeholder="Distolik"
                        class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarPoliRJ.pemeriksaan.tandaVital.distolik'))" :disabled=$disabledPropertyRjStatus :mou_label="__('mmHg')"
                        wire:model="dataDaftarPoliRJ.pemeriksaan.tandaVital.distolik" />
                </div>
            </div>

            <div class="mb-2 ">
                <div class="grid grid-cols-2 gap-2">
                    <x-input-label for="dataDaftarPoliRJ.pemeriksaan.tandaVital.frekuensiNafas" :value="__('Frekuensi Nadi')"
                        :required="__(false)" />
                    <x-input-label for="dataDaftarPoliRJ.pemeriksaan.tandaVital.frekuensiNafas" :value="__('Frekuensi Nafas')"
                        :required="__(false)" />
                </div>

                <div class="grid grid-cols-2 gap-2">

                    <x-text-input-mou id="dataDaftarPoliRJ.pemeriksaan.tandaVital.frekuensiNadi"
                        placeholder="Frekuensi Nadi" class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarPoliRJ.pemeriksaan.tandaVital.frekuensiNadi'))"
                        :disabled=$disabledPropertyRjStatus :mou_label="__('X/Menit')"
                        wire:model="dataDaftarPoliRJ.pemeriksaan.tandaVital.frekuensiNadi" />

                    <x-text-input-mou id="dataDaftarPoliRJ.pemeriksaan.tandaVital.frekuensiNafas"
                        placeholder="Frekuensi Nafas" class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarPoliRJ.pemeriksaan.tandaVital.frekuensiNafas'))"
                        :disabled=$disabledPropertyRjStatus :mou_label="__('X/Menit')"
                        wire:model="dataDaftarPoliRJ.pemeriksaan.tandaVital.frekuensiNafas" />
                </div>
            </div>

            <div class="mb-2 ">
                <div class="grid grid-cols-2 gap-2">
                    <x-input-label for="dataDaftarPoliRJ.pemeriksaan.tandaVital.frekuensiNafas" :value="__('Suhu')"
                        :required="__(false)" />
                    {{-- <x-input-label for="dataDaftarPoliRJ.pemeriksaan.tandaVital.frekuensiNafas" :value="__('Saturasi O2')"
                        :required="__(false)" /> --}}
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <x-text-input-mou id="dataDaftarPoliRJ.pemeriksaan.tandaVital.suhu" placeholder="Suhu"
                        class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarPoliRJ.pemeriksaan.tandaVital.suhu'))" :disabled=$disabledPropertyRjStatus :mou_label="__('°C')"
                        wire:model="dataDaftarPoliRJ.pemeriksaan.tandaVital.suhu" />
                    {{-- <x-text-input-mou id="dataDaftarPoliRJ.pemeriksaan.tandaVital.saturasiO2"
                        placeholder="Saturasi O2" class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarPoliRJ.pemeriksaan.tandaVital.saturasiO2'))"
                        :disabled=$disabledPropertyRjStatus :mou_label="__('Saturasi O2 ')"
                        wire:model="dataDaftarPoliRJ.pemeriksaan.tandaVital.saturasiO2" /> --}}
                </div>
            </div>

            <div class="mb-2 ">
                <div class="grid grid-cols-2 gap-2">
                    <x-input-label for="dataDaftarPoliRJ.pemeriksaan.tandaVital.frekuensiNafas" :value="__('SPO2')"
                        :required="__(false)" />
                    <x-input-label for="dataDaftarPoliRJ.pemeriksaan.tandaVital.frekuensiNafas" :value="__('GDA')"
                        :required="__(false)" />
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <x-text-input-mou id="dataDaftarPoliRJ.pemeriksaan.tandaVital.spo2" placeholder="SPO2"
                        class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarPoliRJ.pemeriksaan.tandaVital.spo2'))" :disabled=$disabledPropertyRjStatus :mou_label="__('%')"
                        wire:model="dataDaftarPoliRJ.pemeriksaan.tandaVital.spo2" />
                    <x-text-input-mou id="dataDaftarPoliRJ.pemeriksaan.tandaVital.gda" placeholder="GDA"
                        class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarPoliRJ.pemeriksaan.tandaVital.gda'))" :disabled=$disabledPropertyRjStatus :mou_label="__('g/dl')"
                        wire:model="dataDaftarPoliRJ.pemeriksaan.tandaVital.gda" />
                </div>
            </div>

            {{-- <div class="mb-2 ">
                <x-input-label for="dataDaftarPoliRJ.pemeriksaan.tandaVital.waktuPemeriksaan" :value="__('Waktu Pemeriksaan')"
                    :required="__(false)" />
                <div class="grid grid-cols-1 gap-0">
                    <x-text-input id="dataDaftarPoliRJ.pemeriksaan.tandaVital.waktuPemeriksaan"
                        placeholder="Waktu Pemeriksaan [dd/mm/yyyy hh24:mi:ss]" class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarPoliRJ.pemeriksaan.tandaVital.waktuPemeriksaan'))"
                        :disabled=$disabledPropertyRjStatus
                        wire:model="dataDaftarPoliRJ.pemeriksaan.tandaVital.waktuPemeriksaan" />
                </div>
            </div> --}}



        </div>



        <x-input-label for="dataDaftarPoliRJ.pemeriksaan.nutrisi.bb" :value="__('Nutrisi')" :required="__(false)"
            class="pt-2 sm:text-xl" />

        <div class="grid grid-cols-3 gap-2 pt-2">

            <div class="mb-2 ">
                <x-input-label for="dataDaftarPoliRJ.pemeriksaan.nutrisi.bb" :value="__('Berat Badan')" :required="__(false)" />
                <x-text-input-mou id="dataDaftarPoliRJ.pemeriksaan.nutrisi.bb" placeholder="Berat Badan"
                    class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarPoliRJ.pemeriksaan.nutrisi.bb'))" :disabled=$disabledPropertyRjStatus
                    wire:model="dataDaftarPoliRJ.pemeriksaan.nutrisi.bb" :mou_label="__('Kg')" />
                @error('dataDaftarPoliRJ.pemeriksaan.nutrisi.bb')
                    <x-input-error :messages=$message />
                @enderror
            </div>

            <div class="mb-2 ">
                <x-input-label for="dataDaftarPoliRJ.pemeriksaan.nutrisi.tb" :value="__('Tinggi Badan')" :required="__(false)" />
                <x-text-input-mou id="dataDaftarPoliRJ.pemeriksaan.nutrisi.tb" placeholder="Tinggi Badan"
                    class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarPoliRJ.pemeriksaan.nutrisi.tb'))" :disabled=$disabledPropertyRjStatus
                    wire:model="dataDaftarPoliRJ.pemeriksaan.nutrisi.tb" :mou_label="__('Cm')" />
                @error('dataDaftarPoliRJ.pemeriksaan.nutrisi.tb')
                    <x-input-error :messages=$message />
                @enderror
            </div>

            <div class="mb-2 ">
                <x-input-label for="dataDaftarPoliRJ.pemeriksaan.nutrisi.imt" :value="__('Index Masa Tubuh')" :required="__(false)" />
                <x-text-input-mou id="dataDaftarPoliRJ.pemeriksaan.nutrisi.imt" placeholder="Index Masa Tubuh"
                    class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarPoliRJ.pemeriksaan.nutrisi.imt'))" :disabled=$disabledPropertyRjStatus
                    wire:model="dataDaftarPoliRJ.pemeriksaan.nutrisi.imt" :mou_label="__('Kg/M2')" />
                @error('dataDaftarPoliRJ.pemeriksaan.nutrisi.imt')
                    <x-input-error :messages=$message />
                @enderror
            </div>



        </div>

        <div class="grid grid-cols-2 gap-2 pt-2">
            <div class="mb-2 ">
                <x-input-label for="dataDaftarPoliRJ.pemeriksaan.nutrisi.lk" :value="__('Lingkar Kepala')" :required="__(false)" />
                <x-text-input-mou id="dataDaftarPoliRJ.pemeriksaan.nutrisi.lk" placeholder="Lingkar Kepala"
                    class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarPoliRJ.pemeriksaan.nutrisi.lk'))" :disabled=$disabledPropertyRjStatus
                    wire:model="dataDaftarPoliRJ.pemeriksaan.nutrisi.lk" :mou_label="__('Cm')" />
                @error('dataDaftarPoliRJ.pemeriksaan.nutrisi.lk')
                    <x-input-error :messages=$message />
                @enderror
            </div>

            <div class="mb-2 ">
                <x-input-label for="dataDaftarPoliRJ.pemeriksaan.nutrisi.lila" :value="__('Lingkar Lengan Atas')" :required="__(false)" />
                <x-text-input-mou id="dataDaftarPoliRJ.pemeriksaan.nutrisi.lila" placeholder="Lingkar Lengan Atas"
                    class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarPoliRJ.pemeriksaan.nutrisi.lila'))" :disabled=$disabledPropertyRjStatus
                    wire:model="dataDaftarPoliRJ.pemeriksaan.nutrisi.lila" :mou_label="__('Cm')" />
                @error('dataDaftarPoliRJ.pemeriksaan.nutrisi.lila')
                    <x-input-error :messages=$message />
                @enderror
            </div>
        </div>

        <x-input-label for="dataDaftarPoliRJ.pemeriksaan.fungsional.bb" :value="__('Fungsional')" :required="__(false)"
            class="pt-2 sm:text-xl" />

        <div class="grid grid-cols-3 gap-2 pt-2">



            <div class="mb-2 ">
                <x-input-label for="dataDaftarPoliRJ.pemeriksaan.fungsional.alatBantu" :value="__('Alat Bantu')"
                    :required="__(false)" />
                <x-text-input id="dataDaftarPoliRJ.pemeriksaan.fungsional.alatBantu" placeholder="Alat Bantu"
                    class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarPoliRJ.pemeriksaan.fungsional.alatBantu'))" :disabled=$disabledPropertyRjStatus
                    wire:model="dataDaftarPoliRJ.pemeriksaan.fungsional.alatBantu" />

            </div>

            <div class="mb-2 ">
                <x-input-label for="dataDaftarPoliRJ.pemeriksaan.fungsional.prothesa" :value="__('Prothesa')"
                    :required="__(false)" />
                <x-text-input id="dataDaftarPoliRJ.pemeriksaan.fungsional.prothesa" placeholder="Prothesa"
                    class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarPoliRJ.pemeriksaan.fungsional.prothesa'))" :disabled=$disabledPropertyRjStatus
                    wire:model="dataDaftarPoliRJ.pemeriksaan.fungsional.prothesa" />

            </div>

            <div class="mb-2 ">
                <x-input-label for="dataDaftarPoliRJ.pemeriksaan.fungsional.cacatTubuh" :value="__('Cacat Tubuh')"
                    :required="__(false)" />
                <x-text-input id="dataDaftarPoliRJ.pemeriksaan.fungsional.cacatTubuh" placeholder="Cacat Tubuh"
                    class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarPoliRJ.pemeriksaan.fungsional.cacatTubuh'))" :disabled=$disabledPropertyRjStatus
                    wire:model="dataDaftarPoliRJ.pemeriksaan.fungsional.cacatTubuh" />

            </div>


        </div>

        <div class="mb-2 ">
            <x-input-label for="dataDaftarPoliRJ.pemeriksaan.suspekAkibatKerja.KeteranganSuspekAkibatKerja"
                :value="__('Suspek Penyakit Akibat Kecelakaan Kerja')" :required="__(false)" />

            <div class="grid grid-cols-3 gap-2 mb-2">
                @isset($dataDaftarPoliRJ['pemeriksaan']['suspekAkibatKerja']['suspekAkibatKerjaOptions'])
                    @foreach ($dataDaftarPoliRJ['pemeriksaan']['suspekAkibatKerja']['suspekAkibatKerjaOptions'] as $suspekAkibatKerjaOptions)
                        {{-- @dd($sRj) --}}
                        <x-radio-button :label="__($suspekAkibatKerjaOptions['suspekAkibatKerja'])" value="{{ $suspekAkibatKerjaOptions['suspekAkibatKerja'] }}"
                            wire:model="dataDaftarPoliRJ.pemeriksaan.suspekAkibatKerja.suspekAkibatKerja" />
                    @endforeach
                @endisset

                <x-text-input id="dataDaftarPoliRJ.pemeriksaan.suspekAkibatKerja.keteranganSuspekAkibatKerja"
                    placeholder="Keterangan" class="mt-1 ml-2" :errorshas="__(
                        $errors->has('dataDaftarPoliRJ.pemeriksaan.suspekAkibatKerja.keteranganSuspekAkibatKerja'),
                    )" :disabled=$disabledPropertyRjStatus
                    wire:model="dataDaftarPoliRJ.pemeriksaan.suspekAkibatKerja.keteranganSuspekAkibatKerja" />
            </div>

        </div>


    </div>


</div>
