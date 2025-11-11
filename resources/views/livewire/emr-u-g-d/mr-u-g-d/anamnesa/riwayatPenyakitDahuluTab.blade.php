<div>
    <div class="w-full mb-1">
        <div class="p-4 bg-white rounded-lg shadow-sm">
            {{-- Riwayat Penyakit Dahulu --}}
            <div class="mb-4">
                <x-input-label for="dataDaftarUgd.anamnesa.riwayatPenyakitDahulu.riwayatPenyakitDahulu" :value="__('Riwayat Penyakit Dahulu')"
                    :required="__(true)" class="mb-3 text-lg font-semibold" />
                <div>
                    <x-text-input-area id="dataDaftarUgd.anamnesa.riwayatPenyakitDahulu.riwayatPenyakitDahulu"
                        placeholder="Riwayat penyakit yang pernah diderita sebelumnya..." class="w-full" :errorshas="__(
                            $errors->has('dataDaftarUgd.anamnesa.riwayatPenyakitDahulu.riwayatPenyakitDahulu'),
                        )"
                        :disabled=$disabledPropertyRjStatus :rows=4
                        wire:model="dataDaftarUgd.anamnesa.riwayatPenyakitDahulu.riwayatPenyakitDahulu" />
                </div>
                @error('dataDaftarUgd.anamnesa.riwayatPenyakitDahulu.riwayatPenyakitDahulu')
                    <x-input-error :messages=$message class="mt-2" />
                @enderror
            </div>

            {{-- Alergi --}}
            <div class="mb-4">
                <x-input-label for="dataDaftarUgd.anamnesa.alergi.alergi" :value="__('Alergi')" :required="__(false)"
                    class="mb-3 text-lg font-semibold" />
                <div>
                    <x-text-input-area id="dataDaftarUgd.anamnesa.alergi.alergi"
                        placeholder="Jenis alergi [Makanan / Obat / Udara / Lainnya]..." class="w-full"
                        :errorshas="__($errors->has('dataDaftarUgd.anamnesa.alergi.alergi'))" :disabled=$disabledPropertyRjStatus :rows=3
                        wire:model="dataDaftarUgd.anamnesa.alergi.alergi" />
                </div>
                @error('dataDaftarUgd.anamnesa.alergi.alergi')
                    <x-input-error :messages=$message class="mt-2" />
                @enderror
            </div>

            {{-- Rekonsiliasi Obat --}}
            <div>
                <x-input-label :value="__('Rekonsiliasi Obat')" :required="__(false)" class="mb-3 text-lg font-semibold" />
                @include('livewire.emr-u-g-d.mr-u-g-d.anamnesa.rekonsiliasiObat')
            </div>
        </div>
    </div>
</div>
