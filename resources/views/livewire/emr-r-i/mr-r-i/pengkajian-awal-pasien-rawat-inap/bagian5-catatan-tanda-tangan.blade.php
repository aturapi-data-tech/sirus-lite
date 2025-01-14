<div>
    <div class="space-y-4">
        <!-- Catatan Umum -->
        <div>
            <x-input-label for="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian5CatatanDanTandaTangan.catatanUmum"
                :value="__('Catatan Umum')" :required="__(false)" />
            <x-text-input-area id="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian5CatatanDanTandaTangan.catatanUmum"
                placeholder="Catatan Umum" class="mt-1" :errorshas="__(
                    $errors->has('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian5CatatanDanTandaTangan.catatanUmum'),
                )" :disabled="$disabledPropertyRjStatus"
                wire:model.debounce.500ms="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian5CatatanDanTandaTangan.catatanUmum" />
            @error('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian5CatatanDanTandaTangan.catatanUmum')
                <x-input-error :messages="$message" />
            @enderror
        </div>

        <!-- Nama Petugas -->
        <div>
            <x-input-label for="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian5CatatanDanTandaTangan.namaPetugas"
                :value="__('Nama Petugas')" :required="__(true)" />
            <x-text-input id="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian5CatatanDanTandaTangan.namaPetugas"
                placeholder="Nama Petugas" class="mt-1" :errorshas="__(
                    $errors->has('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian5CatatanDanTandaTangan.namaPetugas'),
                )" :disabled="$disabledPropertyRjStatus"
                wire:model.debounce.500ms="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian5CatatanDanTandaTangan.namaPetugas" />
            @error('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian5CatatanDanTandaTangan.namaPetugas')
                <x-input-error :messages="$message" />
            @enderror
        </div>

        <!-- Tanda Tangan -->
        <div>
            <x-input-label for="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian5CatatanDanTandaTangan.tandaTangan"
                :value="__('Tanda Tangan')" :required="__(true)" />
            <x-text-input id="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian5CatatanDanTandaTangan.tandaTangan"
                placeholder="Tanda Tangan" class="mt-1" :errorshas="__(
                    $errors->has('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian5CatatanDanTandaTangan.tandaTangan'),
                )" :disabled="$disabledPropertyRjStatus"
                wire:model.debounce.500ms="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian5CatatanDanTandaTangan.tandaTangan" />
            @error('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian5CatatanDanTandaTangan.tandaTangan')
                <x-input-error :messages="$message" />
            @enderror
        </div>

        <!-- Tanggal -->
        <div>
            <x-input-label for="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian5CatatanDanTandaTangan.tanggal"
                :value="__('Tanggal')" :required="__(true)" />
            <x-text-input id="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian5CatatanDanTandaTangan.tanggal"
                placeholder="dd/mm/yyyy hh24:mi:ss" class="mt-1" :errorshas="__(
                    $errors->has('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian5CatatanDanTandaTangan.tanggal'),
                )" :disabled="$disabledPropertyRjStatus"
                wire:model.debounce.500ms="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian5CatatanDanTandaTangan.tanggal" />
            @error('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian5CatatanDanTandaTangan.tanggal')
                <x-input-error :messages="$message" />
            @enderror
        </div>
    </div>
</div>
