<div>
    <div class="w-full mb-1">


        <div class="pt-0">

            <div class="flex justify-between my-4">
                @php
                    $myskalaHumptyDumptyColor =
                        $this->dataDaftarRi['penilaian']['resikoJatuh']['skalaHumptyDumpty'][
                            'skalaHumptyDumptyScore'
                        ] >= 0 &&
                        $this->dataDaftarRi['penilaian']['resikoJatuh']['skalaHumptyDumpty'][
                            'skalaHumptyDumptyScore'
                        ] <= 11
                            ? 'text-green-500'
                            : ($this->dataDaftarRi['penilaian']['resikoJatuh']['skalaHumptyDumpty'][
                                'skalaHumptyDumptyScore'
                            ] >= 12 &&
                            $this->dataDaftarRi['penilaian']['resikoJatuh']['skalaHumptyDumpty'][
                                'skalaHumptyDumptyScore'
                            ] <= 12
                                ? 'text-red-500'
                                : 'text-red-500');
                @endphp
                <p class="text-2xl font-bold {{ $myskalaHumptyDumptyColor }}">
                    {{ 'Total Skor :  ' . $this->dataDaftarRi['penilaian']['resikoJatuh']['skalaHumptyDumpty']['skalaHumptyDumptyScore'] }}
                    /
                    {{ isset($this->dataDaftarRi['penilaian']['resikoJatuh']['skalaHumptyDumpty']['skalaHumptyDumptyDesc']) ? $this->dataDaftarRi['penilaian']['resikoJatuh']['skalaHumptyDumpty']['skalaHumptyDumptyDesc'] : '' }}
                </p>
                <br>
                <p class="text-xs">
                    Skor 7 - 11 : Risiko rendah untuk jatuh
                    <br>
                    Skor ≥ 12 : Risiko tinggi untuk jatuh
                    <br>
                    Skor minimal : 7
                    <br>
                    Skor maksimal : 23
                </p>
            </div>


            <x-input-label for="" :value="__('Usia')" :required="__(false)" class="pt-0 sm:text-2xl" />
            <div class="grid grid-cols-4 gap-2 mt-2 ml-2">

                @foreach ($dataDaftarRi['penilaian']['resikoJatuh']['skalaHumptyDumpty']['umurOptions'] as $umurOptions)
                    {{-- @dd($sRj) --}}
                    <x-radio-button :label="__($umurOptions['umur'])" value="{{ $umurOptions['umur'] }}"
                        wire:model="dataDaftarRi.penilaian.resikoJatuh.skalaHumptyDumpty.umur"
                        wire:click="$set('dataDaftarRi.penilaian.resikoJatuh.skalaHumptyDumpty.umurScore', {{ $umurOptions['score'] }})" />
                @endforeach

            </div>

            <x-input-label for="" :value="__('Jenis Kelamin')" :required="__(false)" class="pt-4 sm:text-2xl" />
            <div class="grid grid-cols-4 gap-2 mt-2 ml-2">

                @foreach ($dataDaftarRi['penilaian']['resikoJatuh']['skalaHumptyDumpty']['sexOptions'] as $sexOptions)
                    {{-- @dd($sRj) --}}
                    <x-radio-button :label="__($sexOptions['sex'])" value="{{ $sexOptions['sex'] }}"
                        wire:model="dataDaftarRi.penilaian.resikoJatuh.skalaHumptyDumpty.sex"
                        wire:click="$set('dataDaftarRi.penilaian.resikoJatuh.skalaHumptyDumpty.sexScore', {{ $sexOptions['score'] }})" />
                @endforeach

            </div>

            <x-input-label for="" :value="__('Diagnosa')" :required="__(false)" class="pt-4 sm:text-2xl" />
            <div class="grid grid-cols-2 gap-2 mt-2 ml-2">

                @foreach ($dataDaftarRi['penilaian']['resikoJatuh']['skalaHumptyDumpty']['diagnosaOptions'] as $diagnosaOptions)
                    {{-- @dd($sRj) --}}
                    <x-radio-button :label="__($diagnosaOptions['diagnosa'])" value="{{ $diagnosaOptions['diagnosa'] }}"
                        wire:model="dataDaftarRi.penilaian.resikoJatuh.skalaHumptyDumpty.diagnosa"
                        wire:click="$set('dataDaftarRi.penilaian.resikoJatuh.skalaHumptyDumpty.diagnosaScore', {{ $diagnosaOptions['score'] }})" />
                @endforeach

            </div>

            <x-input-label for="" :value="__('Gangguan Kognitif')" :required="__(false)" class="pt-4 sm:text-2xl" />
            <div class="grid grid-cols-4 gap-2 mt-2 ml-2">

                @foreach ($dataDaftarRi['penilaian']['resikoJatuh']['skalaHumptyDumpty']['gangguanKognitifOptions'] as $gangguanKognitifOptions)
                    {{-- @dd($sRj) --}}
                    <x-radio-button :label="__($gangguanKognitifOptions['gangguanKognitif'])" value="{{ $gangguanKognitifOptions['gangguanKognitif'] }}"
                        wire:model="dataDaftarRi.penilaian.resikoJatuh.skalaHumptyDumpty.gangguanKognitif"
                        wire:click="$set('dataDaftarRi.penilaian.resikoJatuh.skalaHumptyDumpty.gangguanKognitifScore', {{ $gangguanKognitifOptions['score'] }})" />
                @endforeach

            </div>

            <x-input-label for="" :value="__('Faktor Lingkungan')" :required="__(false)" class="pt-4 sm:text-2xl" />
            <div class="grid grid-cols-4 gap-2 mt-2 ml-2">

                @foreach ($dataDaftarRi['penilaian']['resikoJatuh']['skalaHumptyDumpty']['faktorLingkunganOptions'] as $faktorLingkunganOptions)
                    {{-- @dd($sRj) --}}
                    <x-radio-button :label="__($faktorLingkunganOptions['faktorLingkungan'])" value="{{ $faktorLingkunganOptions['faktorLingkungan'] }}"
                        wire:model="dataDaftarRi.penilaian.resikoJatuh.skalaHumptyDumpty.faktorLingkungan"
                        wire:click="$set('dataDaftarRi.penilaian.resikoJatuh.skalaHumptyDumpty.faktorLingkunganScore', {{ $faktorLingkunganOptions['score'] }})" />
                @endforeach

            </div>

            <x-input-label for="" :value="__('Penggunaan Obat')" :required="__(false)" class="pt-4 sm:text-2xl" />
            <div class="grid grid-flow-row gap-2 mt-2 ml-2">

                @foreach ($dataDaftarRi['penilaian']['resikoJatuh']['skalaHumptyDumpty']['penggunaanObatOptions'] as $penggunaanObatOptions)
                    {{-- @dd($sRj) --}}
                    <x-radio-button :label="__($penggunaanObatOptions['penggunaanObat'])" value="{{ $penggunaanObatOptions['penggunaanObat'] }}"
                        wire:model="dataDaftarRi.penilaian.resikoJatuh.skalaHumptyDumpty.penggunaanObat"
                        wire:click="$set('dataDaftarRi.penilaian.resikoJatuh.skalaHumptyDumpty.penggunaanObatScore', {{ $penggunaanObatOptions['score'] }})" />
                @endforeach

            </div>

            <x-input-label for="" :value="__('Respon Terhadap Operasi / Obat Penenang/ Efek Anastesi')" :required="__(false)" class="pt-4 sm:text-2xl" />
            <div class="grid grid-cols-4 gap-2 mt-2 ml-2 ">

                @foreach ($dataDaftarRi['penilaian']['resikoJatuh']['skalaHumptyDumpty']['responTerhadapOperasiOptions'] as $responTerhadapOperasiOptions)
                    {{-- @dd($sRj) --}}
                    <x-radio-button :label="__($responTerhadapOperasiOptions['responTerhadapOperasi'])"
                        value="{{ $responTerhadapOperasiOptions['responTerhadapOperasi'] }}"
                        wire:model="dataDaftarRi.penilaian.resikoJatuh.skalaHumptyDumpty.responTerhadapOperasi"
                        wire:click="$set('dataDaftarRi.penilaian.resikoJatuh.skalaHumptyDumpty.responTerhadapOperasiScore', {{ $responTerhadapOperasiOptions['score'] }})" />
                @endforeach

            </div>



        </div>




    </div>


</div>
