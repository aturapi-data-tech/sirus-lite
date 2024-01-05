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

                            <x-text-input placeholder="Isi dgn data yang sesuai" class="mt-1 ml-2" :errorshas="__($errors->has('SEPJsonReq.request.t_sep.diagAwal'))"
                                :disabled=$disabledProperty
                                value="{{ isset($dataDaftarPoliRJ['anamnesa']['keluhanUtama']['keluhanUtama'])
                                    ? ($dataDaftarPoliRJ['anamnesa']['keluhanUtama']['keluhanUtama']
                                        ? $dataDaftarPoliRJ['anamnesa']['keluhanUtama']['keluhanUtama']
                                        : '-')
                                    : '-' }}" />

                        </div>
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
                        Simpan
                    </x-green-button>
                </div>
            </div> --}}


        </div>
    @endif

</div>
