<div>
    <div class="w-full mb-1">
        <div class="p-4 bg-white rounded-lg shadow-sm">
            <x-input-label for="dataDaftarUgd.anamnesa.riwayatPenyakitSekarangUmum.riwayatPenyakitSekarangUmum"
                :value="__('Riwayat Penyakit Sekarang')" :required="__(true)" class="mb-3 text-lg font-semibold" />
            <div>
                <x-text-input-area id="dataDaftarUgd.anamnesa.riwayatPenyakitSekarangUmum.riwayatPenyakitSekarangUmum"
                    placeholder="Deskripsikan riwayat penyakit yang sedang dialami pasien..." class="w-full"
                    :errorshas="__(
                        $errors->has('dataDaftarUgd.anamnesa.riwayatPenyakitSekarangUmum.riwayatPenyakitSekarangUmum'),
                    )" :disabled=$disabledPropertyRjStatus :rows=6
                    wire:model="dataDaftarUgd.anamnesa.riwayatPenyakitSekarangUmum.riwayatPenyakitSekarangUmum" />
            </div>
            @error('dataDaftarUgd.anamnesa.riwayatPenyakitSekarangUmum.riwayatPenyakitSekarangUmum')
                <x-input-error :messages=$message class="mt-2" />
            @enderror
        </div>
    </div>
</div>
