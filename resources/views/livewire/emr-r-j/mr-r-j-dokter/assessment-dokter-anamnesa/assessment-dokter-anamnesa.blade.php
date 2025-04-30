<div>
    @php
        $disabledProperty = true;
        $disabledPropertyRjStatus = false;
    @endphp
    {{-- jika anamnesa kosong ngak usah di render --}}
    @if (isset($dataDaftarPoliRJ['anamnesa']))
        <div class="w-full mb-1">

            <div id="TransaksiRawatJalan" class="p-2">
                <div id="TransaksiRawatJalan">

                    <div class="p-2 rounded-lg bg-gray-50">
                        {{-- @include('livewire.emr-r-j.mr-r-j.anamnesa.pengkajianPerawatanTab') --}}
                        {{-- @include('livewire.emr-r-j.mr-r-j.anamnesa.keluhanUtamaTab') --}}

                        <div>

                            <x-input-label :value="__('Keluhan Utama')" :required="__(true)" class="pt-2 sm:text-xl" />

                            <p class="w-full ml-2 text-sm">
                                {!! nl2br(
                                    e(
                                        isset($dataDaftarPoliRJ['anamnesa']['keluhanUtama']['keluhanUtama'])
                                            ? ($dataDaftarPoliRJ['anamnesa']['keluhanUtama']['keluhanUtama']
                                                ? $dataDaftarPoliRJ['anamnesa']['keluhanUtama']['keluhanUtama']
                                                : '-')
                                            : '-',
                                    ),
                                ) !!}
                            </p>

                        </div>

                        @role(['Perawat', 'Mr', 'Admin'])
                            {{-- LOV Snomed --}}
                            <div class="">
                                @if (empty($dataDaftarPoliRJ['anamnesa']['keluhanUtama']['snomedCode']))
                                    @if (empty($dataSnomedLovSearch))
                                        <div>
                                            <x-input-label for="lovRJKeluhanUtama" :value="__('Cari Kode Snomed Keluhan Utama')" :required="__(true)" />
                                            <x-text-input id="lovRJKeluhanUtama" placeholder="Cari Snomed" class="mt-1 ml-2"
                                                :errorshas="__($errors->has('lovRJKeluhanUtama'))" :disabled=false
                                                wire:model.debounce.500ms="lovRJKeluhanUtama" />
                                        </div>
                                    @else
                                        @if ($LOVParentStatus === 'lovRJKeluhanUtama')
                                            <div class="col-span-2">
                                                @include('livewire.component.l-o-v.list-of-value-snomed.list-of-value-snomed')
                                            </div>
                                        @endif
                                    @endif
                                @else
                                    <x-input-label for="dataDaftarPoliRJ.anamnesa.keluhanUtama.snomedDisplay"
                                        :value="__('Display Snomed Keluhan Utama')" :required="__(true)" wire:click="resetLovRJKeluhanUtama()" />
                                    <div>
                                        <x-text-input id="dataDaftarPoliRJ.anamnesa.keluhanUtama.snomedDisplay"
                                            placeholder="Display Snomed" class="mt-1 ml-2" :errorshas="__(
                                                $errors->has('dataDaftarPoliRJ.anamnesa.keluhanUtama.snomedDisplay'),
                                            )"
                                            wire:model="dataDaftarPoliRJ.anamnesa.keluhanUtama.snomedDisplay"
                                            :disabled="true" />

                                    </div>
                                @endif

                                @error('dataDaftarPoliRJ.anamnesa.keluhanUtama.snomedCode')
                                    <x-input-error :messages=$message />
                                @enderror
                            </div>
                        @endrole
                        {{-- @include('livewire.emr-r-j.mr-r-j.anamnesa.anamnesaDiperolehTab') --}}
                        @include('livewire.emr-r-j.mr-r-j.anamnesa.riwayatPenyakitSekarangUmumTab')
                        @include('livewire.emr-r-j.mr-r-j.anamnesa.riwayatPenyakitDahuluTab')
                        {{-- @include('livewire.emr-r-j.mr-r-j.anamnesa.penyakitKeluargaTab') --}}
                        {{-- @include('livewire.emr-r-j.mr-r-j.anamnesa.statusFungsionalTab') --}}
                        {{-- @include('livewire.emr-r-j.mr-r-j.anamnesa.statusPsikologisTab') --}}
                        {{-- @include('livewire.emr-r-j.mr-r-j.anamnesa.edukasiTab')
                        @include('livewire.emr-r-j.mr-r-j.anamnesa.screeningGiziTab') --}}
                        {{-- @include('livewire.emr-r-j.mr-r-j.anamnesa.batukTab') --}}
                    </div>



                </div>
            </div>



            {{-- <div class="sticky bottom-0 flex justify-between px-4 py-3 bg-gray-50 sm:px-6">

                <div class="">
                </div>
                <div>
                    <div wire:loading wire:target="store">
                        <x-loading />
                    </div>

                    <x-green-button :disabled=false wire:click.prevent="store()" type="button" wire:loading.remove>
                        Simpan Subjective
                    </x-green-button>
                </div>
            </div> --}}


        </div>
    @endif

</div>
