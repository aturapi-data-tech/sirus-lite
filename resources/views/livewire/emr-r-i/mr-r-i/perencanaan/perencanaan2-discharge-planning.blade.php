<div class="space-y-4">
    <!-- Pelayanan Berkelanjutan -->
    <div>
        <x-input-label for="pelayananBerkelanjutan" :value="__('Pelayanan Berkelanjutan')" />
        <div class="grid grid-cols-2 gap-2 mt-1">
            <!-- Loop melalui opsi pelayanan berkelanjutan -->
            @foreach ($pelayananBerkelanjutanOptions as $option)
                <x-radio-button :label="$option['pelayananBerkelanjutan']" :value="$option['pelayananBerkelanjutan']"
                    wire:model="dataDaftarRi.perencanaan.dischargePlanning.pelayananBerkelanjutan.pelayananBerkelanjutan" />
            @endforeach
        </div>

        <!-- Input teks untuk keterangan pelayanan berkelanjutan -->
        @if ($dataDaftarRi['perencanaan']['dischargePlanning']['pelayananBerkelanjutan']['pelayananBerkelanjutan'] === 'Ada')
            <div class="mt-2">
                <x-text-input
                    id="dataDaftarRi.perencanaan.dischargePlanning.pelayananBerkelanjutan.ketPelayananBerkelanjutan"
                    placeholder="Silakan jelaskan pelayanan berkelanjutan" class="w-full mt-1"
                    wire:model.debounce.500ms="dataDaftarRi.perencanaan.dischargePlanning.pelayananBerkelanjutan.ketPelayananBerkelanjutan"
                    :disabled="$disabledPropertyRjStatus" :errorshas="$errors->has(
                        'dataDaftarRi.perencanaan.dischargePlanning.pelayananBerkelanjutan.ketPelayananBerkelanjutan',
                    )" />
                @error('dataDaftarRi.perencanaan.dischargePlanning.pelayananBerkelanjutan.ketPelayananBerkelanjutan')
                    <x-input-error :messages="$message" />
                @enderror
            </div>
        @endif
    </div>

    <!-- Opsi Pelayanan Berkelanjutan -->
    @if ($dataDaftarRi['perencanaan']['dischargePlanning']['pelayananBerkelanjutan']['pelayananBerkelanjutan'] === 'Ada')
        <div>
            <x-input-label :value="__('Opsi Pelayanan Berkelanjutan')" />
            <div class="grid grid-cols-3 gap-2 mt-2 md:grid-cols-2 lg:grid-cols-5">
                <!-- Loop melalui opsi pelayanan berkelanjutan (detail) -->
                @foreach ($pelayananBerkelanjutan as $index => $opsi)
                    <div class="space-y-2">
                        <x-check-box :id="'opsi-' . $index" :label="$opsi['deskripsi']"
                            wire:model="dataDaftarRi.perencanaan.dischargePlanning.pelayananBerkelanjutanData.{{ $index }}.status"
                            :disabled="$disabledPropertyRjStatus" :errorshas="$errors->has(
                                'dataDaftarRi.perencanaan.dischargePlanning.pelayananBerkelanjutanData.' .
                                    $index .
                                    '.status',
                            )" />
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Penggunaan Alat Bantu -->
    <div>
        <x-input-label for="penggunaanAlatBantu" :value="__('Penggunaan Alat Bantu')" />
        <div class="grid grid-cols-2 gap-2 mt-1">
            <!-- Loop melalui opsi penggunaan alat bantu -->
            @foreach ($penggunaanAlatBantuOptions as $option)
                <x-radio-button :label="$option['penggunaanAlatBantu']" :value="$option['penggunaanAlatBantu']"
                    wire:model="dataDaftarRi.perencanaan.dischargePlanning.penggunaanAlatBantu.penggunaanAlatBantu" />
            @endforeach
        </div>

        <!-- Input teks untuk keterangan penggunaan alat bantu -->
        @if ($dataDaftarRi['perencanaan']['dischargePlanning']['penggunaanAlatBantu']['penggunaanAlatBantu'] === 'Ada')
            <div class="mt-2">
                <x-text-input id="dataDaftarRi.perencanaan.dischargePlanning.penggunaanAlatBantu.ketPenggunaanAlatBantu"
                    placeholder="Silakan jelaskan penggunaan alat bantu" class="w-full mt-1"
                    wire:model.debounce.500ms="dataDaftarRi.perencanaan.dischargePlanning.penggunaanAlatBantu.ketPenggunaanAlatBantu"
                    :disabled="$disabledPropertyRjStatus" :errorshas="$errors->has(
                        'dataDaftarRi.perencanaan.dischargePlanning.penggunaanAlatBantu.ketPenggunaanAlatBantu',
                    )" />
                @error('dataDaftarRi.perencanaan.dischargePlanning.penggunaanAlatBantu.ketPenggunaanAlatBantu')
                    <x-input-error :messages="$message" />
                @enderror
            </div>
        @endif
    </div>

    <!-- Opsi Penggunaan Alat Bantu -->
    @if ($dataDaftarRi['perencanaan']['dischargePlanning']['penggunaanAlatBantu']['penggunaanAlatBantu'] === 'Ada')
        <div>
            <x-input-label :value="__('Opsi Penggunaan Alat Bantu')" />
            <div class="grid grid-cols-3 gap-4 mt-1 md:grid-cols-2">
                <!-- Loop melalui opsi penggunaan alat bantu (detail) -->
                @foreach ($penggunaanAlatBantu as $index => $opsi)
                    <div class="space-y-2">
                        <x-check-box :id="'alat-' . $index" :label="$opsi['deskripsi']"
                            wire:model="dataDaftarRi.perencanaan.dischargePlanning.penggunaanAlatBantuData.{{ $index }}.status"
                            :disabled="$disabledPropertyRjStatus" :errorshas="$errors->has(
                                'dataDaftarRi.perencanaan.dischargePlanning.penggunaanAlatBantuData.' .
                                    $index .
                                    '.status',
                            )" />
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
