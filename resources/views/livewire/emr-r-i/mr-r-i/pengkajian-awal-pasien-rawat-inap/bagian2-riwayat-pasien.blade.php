<div>
    <div class="space-y-4">
        <!-- Riwayat Penyakit, Operasi, atau Cedera -->
        <div>
            <x-input-label
                for="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.riwayatPenyakitOperasiCedera.pilihan"
                :value="__('Riwayat Penyakit, Operasi, atau Cedera')" :required="__(true)" />
            <div class="grid grid-cols-4 gap-2">
                @foreach ($options['riwayatPenyakitOptions'] as $option)
                    <x-radio-button :label="__($option)" value="{{ $option }}"
                        wire:model="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.riwayatPenyakitOperasiCedera.pilihan" />
                @endforeach
            </div>
            <!-- Input teks untuk opsi "lainnya" -->
            @if (
                $dataDaftarRi['pengkajianAwalPasienRawatInap']['bagian2RiwayatPasien']['riwayatPenyakitOperasiCedera'][
                    'pilihan'
                ] === 'lainnya')
                <div class="mt-2">
                    <x-text-input
                        id="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.riwayatPenyakitOperasiCedera.keterangan"
                        placeholder="Silakan jelaskan riwayat lainnya" class="mt-1"
                        wire:model.debounce.500ms="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.riwayatPenyakitOperasiCedera.keterangan" />
                    @error('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.riwayatPenyakitOperasiCedera.keterangan')
                        <x-input-error :messages="$message" />
                    @enderror
                </div>
            @endif
            @error('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.riwayatPenyakitOperasiCedera.pilihan')
                <x-input-error :messages="$message" />
            @enderror

            <x-input-label
                for="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.riwayatPenyakitOperasiCedera.deskripsi"
                :value="__('Deskripsi')" :required="__(false)" />
            <x-text-input
                id="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.riwayatPenyakitOperasiCedera.deskripsi"
                placeholder="Deskripsi" class="mt-1" :errorshas="__(
                    $errors->has(
                        'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.riwayatPenyakitOperasiCedera.deskripsi',
                    ),
                )" :disabled="$disabledPropertyRjStatus"
                wire:model.debounce.500ms="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.riwayatPenyakitOperasiCedera.deskripsi" />
            @error('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.riwayatPenyakitOperasiCedera.deskripsi')
                <x-input-error :messages="$message" />
            @enderror
        </div>

        <!-- Kebiasaan Merokok -->
        <div>
            <x-input-label
                for="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.kebiasaan.merokok.pilihan"
                :value="__('Kebiasaan Merokok')" :required="__(true)" />
            <div class="grid grid-cols-4 gap-2">
                @foreach ($options['merokokOptions'] as $option)
                    <x-radio-button :label="__($option)" value="{{ $option }}"
                        wire:model="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.kebiasaan.merokok.pilihan" />
                @endforeach
            </div>
            @error('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.kebiasaan.merokok.pilihan')
                <x-input-error :messages="$message" />
            @enderror

            <div class="mt-2">
                <x-input-label
                    for="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.kebiasaan.merokok.detail.jenis"
                    :value="__('Jenis Rokok')" :required="__(false)" />
                <x-text-input
                    id="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.kebiasaan.merokok.detail.jenis"
                    placeholder="Jenis Rokok" class="mt-1" :errorshas="__(
                        $errors->has(
                            'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.kebiasaan.merokok.detail.jenis',
                        ),
                    )" :disabled="$disabledPropertyRjStatus"
                    wire:model.debounce.500ms="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.kebiasaan.merokok.detail.jenis" />
                @error('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.kebiasaan.merokok.detail.jenis')
                    <x-input-error :messages="$message" />
                @enderror
            </div>

            <div class="mt-2">
                <x-input-label
                    for="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.kebiasaan.merokok.detail.jumlahPerHari"
                    :value="__('Jumlah per Hari')" :required="__(false)" />
                <x-text-input
                    id="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.kebiasaan.merokok.detail.jumlahPerHari"
                    placeholder="Jumlah per Hari" class="mt-1" :errorshas="__(
                        $errors->has(
                            'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.kebiasaan.merokok.detail.jumlahPerHari',
                        ),
                    )" :disabled="$disabledPropertyRjStatus"
                    wire:model.debounce.500ms="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.kebiasaan.merokok.detail.jumlahPerHari" />
                @error('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.kebiasaan.merokok.detail.jumlahPerHari')
                    <x-input-error :messages="$message" />
                @enderror
            </div>
        </div>

        <!-- Kebiasaan Alkohol atau Obat -->
        <div>
            <x-input-label
                for="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.kebiasaan.alkoholObat.pilihan"
                :value="__('Kebiasaan Alkohol atau Obat')" :required="__(true)" />
            <div class="grid grid-cols-4 gap-2">
                @foreach ($options['alkoholObatOptions'] as $option)
                    <x-radio-button :label="__($option)" value="{{ $option }}"
                        wire:model="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.kebiasaan.alkoholObat.pilihan" />
                @endforeach
            </div>
            @error('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.kebiasaan.alkoholObat.pilihan')
                <x-input-error :messages="$message" />
            @enderror

            <div class="mt-2">
                <x-input-label
                    for="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.kebiasaan.alkoholObat.detail.jenis"
                    :value="__('Jenis Alkohol/Obat')" :required="__(false)" />
                <x-text-input
                    id="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.kebiasaan.alkoholObat.detail.jenis"
                    placeholder="Jenis Alkohol/Obat" class="mt-1" :errorshas="__(
                        $errors->has(
                            'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.kebiasaan.alkoholObat.detail.jenis',
                        ),
                    )" :disabled="$disabledPropertyRjStatus"
                    wire:model.debounce.500ms="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.kebiasaan.alkoholObat.detail.jenis" />
                @error('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.kebiasaan.alkoholObat.detail.jenis')
                    <x-input-error :messages="$message" />
                @enderror
            </div>

            <div class="mt-2">
                <x-input-label
                    for="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.kebiasaan.alkoholObat.detail.jumlahPerHari"
                    :value="__('Jumlah per Hari')" :required="__(false)" />
                <x-text-input
                    id="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.kebiasaan.alkoholObat.detail.jumlahPerHari"
                    placeholder="Jumlah per Hari" class="mt-1" :errorshas="__(
                        $errors->has(
                            'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.kebiasaan.alkoholObat.detail.jumlahPerHari',
                        ),
                    )" :disabled="$disabledPropertyRjStatus"
                    wire:model.debounce.500ms="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.kebiasaan.alkoholObat.detail.jumlahPerHari" />
                @error('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.kebiasaan.alkoholObat.detail.jumlahPerHari')
                    <x-input-error :messages="$message" />
                @enderror
            </div>
        </div>

        <!-- Vaksinasi Influenza -->
        <div>
            <x-input-label
                for="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.vaksinasi.influenza.pilihan"
                :value="__('Vaksinasi Influenza')" :required="__(true)" />
            <div class="grid grid-cols-4 gap-2">
                @foreach ($options['vaksinasiOptions'] as $option)
                    <x-radio-button :label="__($option)" value="{{ $option }}"
                        wire:model="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.vaksinasi.influenza.pilihan" />
                @endforeach
            </div>
            @error('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.vaksinasi.influenza.pilihan')
                <x-input-error :messages="$message" />
            @enderror
        </div>

        <!-- Vaksinasi Pneumonia -->
        <div>
            <x-input-label
                for="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.vaksinasi.pneumonia.pilihan"
                :value="__('Vaksinasi Pneumonia')" :required="__(true)" />
            <div class="grid grid-cols-4 gap-2">
                @foreach ($options['vaksinasiOptions'] as $option)
                    <x-radio-button :label="__($option)" value="{{ $option }}"
                        wire:model="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.vaksinasi.pneumonia.pilihan" />
                @endforeach
            </div>
            @error('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.vaksinasi.pneumonia.pilihan')
                <x-input-error :messages="$message" />
            @enderror
        </div>

        <!-- Riwayat Keluarga -->
        <div>
            <x-input-label for="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.riwayatKeluarga.pilihan"
                :value="__('Riwayat Keluarga')" :required="__(true)" />
            <div class="grid grid-cols-4 gap-2">
                @foreach ($options['riwayatKeluargaOptions'] as $option)
                    <x-radio-button :label="__($option)" value="{{ $option }}"
                        wire:model="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.riwayatKeluarga.pilihan" />
                @endforeach
            </div>
            <!-- Input teks untuk opsi "lainnya" -->
            @if ($dataDaftarRi['pengkajianAwalPasienRawatInap']['bagian2RiwayatPasien']['riwayatKeluarga']['pilihan'] === 'lainnya')
                <div class="mt-2">
                    <x-text-input
                        id="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.riwayatKeluarga.keterangan"
                        placeholder="Silakan jelaskan riwayat keluarga lainnya" class="mt-1"
                        wire:model.debounce.500ms="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.riwayatKeluarga.keterangan" />
                    @error('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.riwayatKeluarga.keterangan')
                        <x-input-error :messages="$message" />
                    @enderror
                </div>
            @endif
            @error('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.riwayatKeluarga.pilihan')
                <x-input-error :messages="$message" />
            @enderror
        </div>
    </div>
</div>
