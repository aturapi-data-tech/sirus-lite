<div>
    <div class="w-full mb-1">

        <div>
            <x-input-label for="dataDaftarRi.anamnesa.keluhanUtama.keluhanUtama" :value="__('Riwayat Penyakit Sekarang')" :required="__(true)"
                class="pt-2 sm:text-xl" />

            <div class="mb-2 ">
                <x-text-input-area id="dataDaftarRi.anamnesa.riwayatPenyakitSekarangUmum.riwayatPenyakitSekarangUmum"
                    placeholder="Deskripsi Anamnesis" class="mt-1 ml-2" :errorshas="__(
                        $errors->has('dataDaftarRi.anamnesa.riwayatPenyakitSekarangUmum.riwayatPenyakitSekarangUmum'),
                    )"
                    :disabled=$disabledPropertyRjStatus :rows=3
                    wire:model.debounce.500ms="dataDaftarRi.anamnesa.riwayatPenyakitSekarangUmum.riwayatPenyakitSekarangUmum" />

            </div>
            @error('dataDaftarRi.anamnesa.riwayatPenyakitSekarangUmum.riwayatPenyakitSekarangUmum')
                <x-input-error :messages=$message />
            @enderror
        </div>

    </div>
</div>
