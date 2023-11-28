<div>
    <div class="w-full mb-1">

        <div>
            <x-input-label for="dataDaftarUgd.anamnesia.keluhanUtama.keluhanUtama" :value="__('Riwayat Penyakit Sekarang')" :required="__(true)"
                class="pt-2 sm:text-xl" />

            <div class="mb-2 ">
                <x-text-input-area id="dataDaftarUgd.anamnesia.riwayatPenyakitSekarangUmum.riwayatPenyakitSekarangUmum"
                    placeholder="Deskripsi Anamnesis" class="mt-1 ml-2" :errorshas="__(
                        $errors->has('dataDaftarUgd.anamnesia.riwayatPenyakitSekarangUmum.riwayatPenyakitSekarangUmum'),
                    )"
                    :disabled=$disabledPropertyRjStatus :rows=7
                    wire:model.debounce.500ms="dataDaftarUgd.anamnesia.riwayatPenyakitSekarangUmum.riwayatPenyakitSekarangUmum" />

            </div>
            @error('dataDaftarUgd.anamnesia.riwayatPenyakitSekarangUmum.riwayatPenyakitSekarangUmum')
                <x-input-error :messages=$message />
            @enderror
        </div>

    </div>
</div>
