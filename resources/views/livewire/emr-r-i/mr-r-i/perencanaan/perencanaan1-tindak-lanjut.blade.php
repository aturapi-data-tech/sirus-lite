<div>
    <!-- Label untuk input Tindak Lanjut -->
    <x-input-label for="dataDaftarRi.perencanaan.tindakLanjut.tindakLanjut" :value="__('Tindak Lanjut')" :required="true" />

    <!-- Grid untuk menampilkan opsi radio button -->
    <div class="grid grid-cols-4 gap-2 mt-1">
        <!-- Loop melalui opsi tindak lanjut yang tersedia -->
        @foreach ($tindakLanjutOptions as $option)
            <x-radio-button :label="__($option['tindakLanjut'])" :value="$option['tindakLanjutKode']"
                wire:model="dataDaftarRi.perencanaan.tindakLanjut.tindakLanjut" />
        @endforeach
    </div>

    <!-- Input teks untuk opsi "Lain-lain" -->
    @if ($dataDaftarRi['perencanaan']['tindakLanjut']['tindakLanjut'] === '74964007')
        <div class="mt-2">
            <x-text-input id="dataDaftarRi.perencanaan.tindakLanjut.keteranganTindakLanjut"
                placeholder="Silakan jelaskan tindak lanjut" class="w-full mt-1"
                wire:model.debounce.500ms="dataDaftarRi.perencanaan.tindakLanjut.keteranganTindakLanjut"
                :disabled="$disabledPropertyRjStatus" :errorshas="$errors->has('dataDaftarRi.perencanaan.tindakLanjut.keteranganTindakLanjut')" />
            <!-- Menampilkan pesan error jika ada -->
            @error('dataDaftarRi.perencanaan.tindakLanjut.keteranganTindakLanjut')
                <x-input-error :messages="$message" />
            @enderror
        </div>
    @endif

    <!-- Menampilkan pesan error untuk input Tindak Lanjut -->
    @error('dataDaftarRi.perencanaan.tindakLanjut.tindakLanjut')
        <x-input-error :messages="$message" />
    @enderror
</div>
