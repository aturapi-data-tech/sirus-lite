<div>
    <div class="space-y-4">
        <!-- Kondisi Saat Masuk -->
        <div>
            <x-input-label for="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian1DataUmum.kondisiSaatMasuk"
                :value="__('Kondisi Saat Masuk')" :required="__(true)" />
            <div class="grid grid-cols-4 gap-2">
                @foreach ($options['kondisiSaatMasukOptions'] as $option)
                    <x-radio-button :label="__($option)" value="{{ $option }}"
                        wire:model="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian1DataUmum.kondisiSaatMasuk" />
                @endforeach
            </div>
            @error('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian1DataUmum.kondisiSaatMasuk')
                <x-input-error :messages="$message" />
            @enderror
        </div>

        <!-- Asal Pasien -->
        <div>
            <x-input-label for="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian1DataUmum.asalPasien" :value="__('Asal Pasien')"
                :required="__(true)" />
            <div class="grid grid-cols-4 gap-2">
                @foreach ($options['asalPasienOptions'] as $option)
                    <x-radio-button :label="__($option)" value="{{ $option }}"
                        wire:model="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian1DataUmum.asalPasien.pilihan" />
                @endforeach
            </div>
            <!-- Input teks untuk opsi "lainnya" -->
            @if ($dataDaftarRi['pengkajianAwalPasienRawatInap']['bagian1DataUmum']['asalPasien']['pilihan'] === 'lainnya')
                <div class="mt-2">
                    <x-text-input id="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian1DataUmum.asalPasien.keterangan"
                        placeholder="Silakan jelaskan asal pasien" class="mt-1"
                        wire:model.debounce.500ms="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian1DataUmum.asalPasien.keterangan" />
                    @error('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian1DataUmum.asalPasien.keterangan')
                        <x-input-error :messages="$message" />
                    @enderror
                </div>
            @endif
            @error('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian1DataUmum.asalPasien.pilihan')
                <x-input-error :messages="$message" />
            @enderror
        </div>

        <!-- Diagnosa Masuk -->
        <div>
            <x-input-label for="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian1DataUmum.diagnosaMasuk"
                :value="__('Diagnosa Masuk')" :required="__(true)" />
            <x-text-input id="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian1DataUmum.diagnosaMasuk"
                placeholder="Diagnosa Masuk" class="mt-1" :errorshas="__($errors->has('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian1DataUmum.diagnosaMasuk'))" :disabled="$disabledPropertyRjStatus"
                wire:model.debounce.500ms="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian1DataUmum.diagnosaMasuk" />
            @error('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian1DataUmum.diagnosaMasuk')
                <x-input-error :messages="$message" />
            @enderror
        </div>

        <!-- DPJP -->
        <div>
            <x-input-label for="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian1DataUmum.dpjp" :value="__('DPJP')"
                :required="__(true)" />
            <x-text-input id="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian1DataUmum.dpjp" placeholder="DPJP"
                class="mt-1" :errorshas="__($errors->has('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian1DataUmum.dpjp'))" :disabled="$disabledPropertyRjStatus"
                wire:model.debounce.500ms="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian1DataUmum.dpjp" />
            @error('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian1DataUmum.dpjp')
                <x-input-error :messages="$message" />
            @enderror
        </div>
    </div>
</div>
