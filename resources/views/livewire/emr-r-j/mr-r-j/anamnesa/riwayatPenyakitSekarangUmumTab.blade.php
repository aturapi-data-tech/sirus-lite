<div>
    <div class="w-full mb-1">

        <div>
            <x-input-label for="dataDaftarPoliRJ.anamnesa.keluhanUtama.keluhanUtama" :value="__('Riwayat Penyakit Sekarang')" :required="__(true)"
                class="pt-2 sm:text-xl" />

            <div class="mb-2 ">
                <x-text-input-area id="dataDaftarPoliRJ.anamnesa.riwayatPenyakitSekarangUmum.riwayatPenyakitSekarangUmum"
                    placeholder="Deskripsi Anamnesis" class="mt-1 ml-2" :errorshas="__(
                        $errors->has(
                            'dataDaftarPoliRJ.anamnesa.riwayatPenyakitSekarangUmum.riwayatPenyakitSekarangUmum',
                        ),
                    )"
                    :disabled=$disabledPropertyRjStatus :rows=3
                    wire:model.debounce.500ms="dataDaftarPoliRJ.anamnesa.riwayatPenyakitSekarangUmum.riwayatPenyakitSekarangUmum" />

            </div>
            @error('dataDaftarPoliRJ.anamnesa.riwayatPenyakitSekarangUmum.riwayatPenyakitSekarangUmum')
                <x-input-error :messages=$message />
            @enderror
        </div>



        @role(['Perawat', 'Mr', 'Admin'])
            {{-- LOV Snomed --}}
            <div class="">
                @if (empty($dataDaftarPoliRJ['anamnesa']['riwayatPenyakitSekarangUmum']['snomedCode']))
                    @if (empty($dataSnomedLovSearch))
                        <div>
                            <x-input-label for="lovRJRiwayatPenyakitSekarangUmum" :value="__('Cari Kode Snomed Riwayat Penyakit Sekarang')" :required="__(true)" />
                            <x-text-input id="lovRJRiwayatPenyakitSekarangUmum" placeholder="Cari Snomed" class="mt-1 ml-2"
                                :errorshas="__($errors->has('lovRJRiwayatPenyakitSekarangUmum'))" :disabled=false
                                wire:model.debounce.500ms="lovRJRiwayatPenyakitSekarangUmum" />
                        </div>
                    @else
                        @if ($LOVParentStatus === 'lovRJRiwayatPenyakitSekarangUmum')
                            <div class="col-span-2">
                                @include('livewire.component.l-o-v.list-of-value-snomed.list-of-value-snomed')
                            </div>
                        @endif
                    @endif
                @else
                    <x-input-label for="dataDaftarPoliRJ.anamnesa.riwayatPenyakitSekarangUmum.snomedDisplay"
                        :value="__('Display Snomed Riwayat Penyakit Sekarang')" :required="__(true)" wire:click="resetLovRJRiwayatPenyakitSekarangUmum()" />
                    <div>
                        <x-text-input id="dataDaftarPoliRJ.anamnesa.riwayatPenyakitSekarangUmum.snomedDisplay"
                            placeholder="Display Snomed" class="mt-1 ml-2" :errorshas="__(
                                $errors->has('dataDaftarPoliRJ.anamnesa.riwayatPenyakitSekarangUmum.snomedDisplay'),
                            )"
                            wire:model="dataDaftarPoliRJ.anamnesa.riwayatPenyakitSekarangUmum.snomedDisplay"
                            :disabled="true" />

                    </div>
                @endif

                @error('dataDaftarPoliRJ.anamnesa.riwayatPenyakitSekarangUmum.snomedCode')
                    <x-input-error :messages=$message />
                @enderror
            </div>
        @endrole
    </div>
</div>
