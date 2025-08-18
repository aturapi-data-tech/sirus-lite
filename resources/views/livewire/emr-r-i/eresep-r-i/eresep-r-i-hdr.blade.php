<div>
    @php
        $headerResep = collect($dataDaftarRi['eresepHdr'])->firstWhere(
            'resepNo',
            '=',
            $formEntryEresepRIHdr['resepNo'],
        );
        $disabledPropertyResepStatus = $headerResep ? true : false;

        $headerResepTtd = isset($dataDaftarRi['eresepHdr'][$resepIndexRef]['tandaTanganDokter']['dokterPeresep']);
        $disabledPropertyResepTtdDokter = $headerResepTtd ? true : false;
    @endphp

    <div class="w-full mb-1">
        <div id="EresepHeader" class="p-2">
            <div class="p-2 rounded-lg bg-gray-50">
                <div class="grid grid-cols-4 gap-2 px-4">
                    {{-- <div>
                        <x-input-label for="formEntryEresepRIHdr.regNo" :value="__('Nomor Registrasi')" :required="true"
                            class="pt-2" />
                        <x-text-input id="formEntryEresepRIHdr.regNo" name="formEntryEresepRIHdr.regNo"
                            placeholder="Nomor Registrasi" class="block w-full mt-1" :errorshas="__($errors->has('formEntryEresepRIHdr.regNo'))" :disabled="true"
                            wire:model.debounce.500ms="formEntryEresepRIHdr.regNo" />
                        @error('formEntryEresepRIHdr.regNo')
                            <x-input-error :messages="$message" />
                        @enderror
                    </div> --}}

                    {{-- <div>
                        <x-input-label for="formEntryEresepRIHdr.riHdrNo" :value="__('Nomor RI Header')" :required="true"
                            class="pt-2" />
                        <x-text-input id="formEntryEresepRIHdr.riHdrNo" name="formEntryEresepRIHdr.riHdrNo"
                            placeholder="Nomor RI Header" class="block w-full mt-1" :errorshas="__($errors->has('formEntryEresepRIHdr.riHdrNo'))" :disabled="true"
                            wire:model.debounce.500ms="formEntryEresepRIHdr.riHdrNo" />
                        @error('formEntryEresepRIHdr.riHdrNo')
                            <x-input-error :messages="$message" />
                        @enderror
                    </div> --}}

                    <div class="hidden">
                        {{-- Nomor Resep --}}
                        <x-input-label for="formEntryEresepRIHdr.resepNo" :value="__('Nomor Resep')" :required="true"
                            class="pt-2" />
                        <x-text-input id="formEntryEresepRIHdr.resepNo" name="formEntryEresepRIHdr.resepNo"
                            placeholder="Nomor Resep" class="block w-full mt-1" :errorshas="__($errors->has('formEntryEresepRIHdr.resepNo'))" :disabled="true"
                            wire:model.debounce.500ms="formEntryEresepRIHdr.resepNo" />
                        @error('formEntryEresepRIHdr.resepNo')
                            <x-input-error :messages="$message" />
                        @enderror
                    </div>

                    <div>
                        {{-- Tanggal Resep --}}
                        <x-input-label for="formEntryEresepRIHdr.resepDate" :value="__('Tanggal Resep')" :required="true"
                            class="pt-2" />
                        <x-text-input id="formEntryEresepRIHdr.resepDate" name="formEntryEresepRIHdr.resepDate"
                            placeholder="Tanggal Resep" class="block w-full mt-1" :errorshas="__($errors->has('formEntryEresepRIHdr.resepDate'))" :disabled="$disabledPropertyResepStatus"
                            wire:model.debounce.500ms="formEntryEresepRIHdr.resepDate" />
                        @error('formEntryEresepRIHdr.resepDate')
                            <x-input-error :messages="$message" />
                        @enderror
                    </div>


                </div>
                @if (!$headerResep)
                    {{-- Tombol Buat Resep --}}
                    <div class="grid grid-cols-1 mt-2 ml-2">
                        <div wire:loading wire:target="insertResepHdr">
                            <x-loading />
                        </div>
                        <x-green-button :disabled=false wire:click="insertResepHdr()" type="button"
                            wire:loading.remove>
                            Buat Resep
                        </x-green-button>
                    </div>
                @else
                    <div class="grid grid-cols-3 gap-2">

                        <div class="col-span-2">
                            {{-- Transasi EMR --}}
                            <div id="TransaksiEMR" x-data="{ activeTabRacikanNonRacikan: @entangle('activeTabRacikanNonRacikan') }" class="grid grid-cols-1">

                                <div class="px-2 mb-0 overflow-auto border-b border-gray-200">
                                    <ul
                                        class="flex flex-row flex-wrap justify-center -mb-px text-sm font-medium text-gray-500 text-start ">
                                        @foreach ($EmrMenuRacikanNonRacikan as $EmrM)
                                            <li wire:key="tab-{{ $EmrM['ermMenuId'] }}" class="mx-1 mr-0 rounded-t-lg"
                                                :class="'{{ $activeTabRacikanNonRacikan }}'
                                                === '{{ $EmrM['ermMenuId'] }}' ?
                                                    'text-primary border-primary bg-gray-100' :
                                                    'border border-gray-200'">
                                                <label
                                                    class="inline-block p-2 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                                    x-on:click="activeTabRacikanNonRacikan ='{{ $EmrM['ermMenuId'] }}'"
                                                    wire:click="$set('activeTabRacikanNonRacikan', '{{ $EmrM['ermMenuId'] }}')">{{ $EmrM['ermMenuName'] }}</label>
                                            </li>
                                        @endforeach


                                    </ul>
                                </div>
                                <div class="w-full mx-2 mr-2 rounded-lg bg-gray-50"
                                    :class="{
                                        'active': activeTabRacikanNonRacikan === 'NonRacikan'
                                    }"
                                    x-show.transition.in.opacity.duration.600="activeTabRacikanNonRacikan === 'NonRacikan'">
                                    <livewire:emr-r-i.eresep-r-i.eresep-r-i-non-racikan
                                        :wire:key="'eresep-r-i-non-racikan'" :riHdrNoRef="$riHdrNoRef" :resepNoRef="$formEntryEresepRIHdr['resepNo']">

                                </div>

                                <div class="w-full mx-2 mr-2 rounded-lg bg-gray-50"
                                    :class="{
                                        'active': activeTabRacikanNonRacikan === 'Racikan'
                                    }"
                                    x-show.transition.in.opacity.duration.600="activeTabRacikanNonRacikan === 'Racikan'">
                                    <livewire:emr-r-i.eresep-r-i.eresep-r-i-racikan :wire:key="'eresep-r-i-racikan'"
                                        :riHdrNoRef="$riHdrNoRef" :resepNoRef="$formEntryEresepRIHdr['resepNo']">

                                </div>

                            </div>
                        </div>

                        {{-- Resume --}}
                        <div>
                            {{-- <livewire:emr.rekam-medis.rekam-medis-display :wire:key="'content-rekamMedisDisplay'"
                            :riHdrNoRefCopyTo="$riHdrNoRef" :regNoRef="$dataDaftarRi['regNo']"> --}}
                        </div>

                        @if (!$headerResepTtd)
                            <div class="grid grid-cols-1 gap-2">
                                <div wire:loading wire:target="setDokterPeresep">
                                    <x-loading />
                                </div>

                                <x-green-button :disabled=$disabledPropertyResepTtdDokter
                                    wire:click="setDokterPeresep()" type="button" wire:loading.remove>
                                    {{ 'Ttd Dokter' }}
                                </x-green-button>
                            </div>
                        @else
                            <div class="text-sm text-center">
                                <p>
                                    Tanda Tangan Dokter
                                    </br>
                                    {{ $dataDaftarRi['eresepHdr'][$resepIndexRef]['tandaTanganDokter']['dokterPeresep'] ?? 'Belum ditandatangani' }}
                                </p>

                            </div>
                        @endif

                    </div>
                @endif

                {{-- Tapilkan data hdr resep --}}
                @include('livewire.emr-r-i.eresep-r-i.eresep-r-i-hdr1-table-resep')

            </div>
        </div>
    </div>
</div>
