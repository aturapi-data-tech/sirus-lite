<div>
    <div class="space-y-4">
        <!-- Agama/Kepercayaan -->
        <div>
            <x-input-label
                for="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.agamaKepercayaan.pilihan"
                :value="__('Agama/Kepercayaan')" :required="__(true)" />
            <div class="grid grid-cols-4 gap-2">
                @foreach ($options['agamaKepercayaanOptions'] as $option)
                    <x-radio-button :label="__($option)" value="{{ $option }}"
                        wire:model="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.agamaKepercayaan.pilihan" />
                @endforeach
            </div>
            <!-- Input teks untuk opsi "lainnya" -->
            @if (
                $dataDaftarRi['pengkajianAwalPasienRawatInap']['bagian3PsikososialDanEkonomi']['agamaKepercayaan']['pilihan'] ===
                    'lainnya')
                <div class="mt-2">
                    <x-text-input
                        id="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.agamaKepercayaan.keterangan"
                        placeholder="Silakan jelaskan agama/kepercayaan lainnya" class="mt-1"
                        wire:model.debounce.500ms="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.agamaKepercayaan.keterangan" />
                    @error('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.agamaKepercayaan.keterangan')
                        <x-input-error :messages="$message" />
                    @enderror
                </div>
            @endif
            @error('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.agamaKepercayaan.pilihan')
                <x-input-error :messages="$message" />
            @enderror
        </div>

        <!-- Status Pernikahan -->
        <div>
            <x-input-label
                for="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.statusPernikahan.pilihan"
                :value="__('Status Pernikahan')" :required="__(true)" />
            <div class="grid grid-cols-4 gap-2">
                @foreach ($options['statusPernikahanOptions'] as $option)
                    <x-radio-button :label="__($option)" value="{{ $option }}"
                        wire:model="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.statusPernikahan.pilihan" />
                @endforeach
            </div>
            @error('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.statusPernikahan.pilihan')
                <x-input-error :messages="$message" />
            @enderror
        </div>

        <!-- Tempat Tinggal -->
        <div>
            <x-input-label
                for="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.tempatTinggal.pilihan"
                :value="__('Tempat Tinggal')" :required="__(true)" />
            <div class="grid grid-cols-4 gap-2">
                @foreach ($options['tempatTinggalOptions'] as $option)
                    <x-radio-button :label="__($option)" value="{{ $option }}"
                        wire:model="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.tempatTinggal.pilihan" />
                @endforeach
            </div>
            <!-- Input teks untuk opsi "lainnya" -->
            @if (
                $dataDaftarRi['pengkajianAwalPasienRawatInap']['bagian3PsikososialDanEkonomi']['tempatTinggal']['pilihan'] ===
                    'lainnya')
                <div class="mt-2">
                    <x-text-input
                        id="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.tempatTinggal.keterangan"
                        placeholder="Silakan jelaskan tempat tinggal lainnya" class="mt-1"
                        wire:model.debounce.500ms="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.tempatTinggal.keterangan" />
                    @error('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.tempatTinggal.keterangan')
                        <x-input-error :messages="$message" />
                    @enderror
                </div>
            @endif
            @error('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.tempatTinggal.pilihan')
                <x-input-error :messages="$message" />
            @enderror
        </div>

        <!-- Aktivitas -->
        <div>
            <x-input-label
                for="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.aktivitas.pilihan"
                :value="__('Aktivitas')" :required="__(true)" />
            <div class="grid grid-cols-4 gap-2">
                @foreach ($options['aktivitasOptions'] as $option)
                    <x-radio-button :label="__($option)" value="{{ $option }}"
                        wire:model="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.aktivitas.pilihan" />
                @endforeach
            </div>
            @error('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.aktivitas.pilihan')
                <x-input-error :messages="$message" />
            @enderror
        </div>

        <!-- Status Emosional -->
        <div>
            <x-input-label
                for="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.statusEmosional.pilihan"
                :value="__('Status Emosional')" :required="__(true)" />
            <div class="grid grid-cols-4 gap-2">
                @foreach ($options['statusEmosionalOptions'] as $option)
                    <x-radio-button :label="__($option)" value="{{ $option }}"
                        wire:model="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.statusEmosional.pilihan" />
                @endforeach
            </div>
            <!-- Input teks untuk opsi "lainnya" -->
            @if (
                $dataDaftarRi['pengkajianAwalPasienRawatInap']['bagian3PsikososialDanEkonomi']['statusEmosional']['pilihan'] ===
                    'lainnya')
                <div class="mt-2">
                    <x-text-input
                        id="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.statusEmosional.keterangan"
                        placeholder="Silakan jelaskan status emosional lainnya" class="mt-1"
                        wire:model.debounce.500ms="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.statusEmosional.keterangan" />
                    @error('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.statusEmosional.keterangan')
                        <x-input-error :messages="$message" />
                    @enderror
                </div>
            @endif
            @error('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.statusEmosional.pilihan')
                <x-input-error :messages="$message" />
            @enderror
        </div>

        <!-- Keluarga Dekat -->
        <div>
            <x-input-label
                for="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.keluargaDekat.nama"
                :value="__('Keluarga Dekat')" :required="__(true)" />
            <div class="mt-2">
                <x-input-label
                    for="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.keluargaDekat.nama"
                    :value="__('Nama')" :required="__(true)" />
                <x-text-input
                    id="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.keluargaDekat.nama"
                    placeholder="Nama" class="mt-1" :errorshas="__(
                        $errors->has(
                            'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.keluargaDekat.nama',
                        ),
                    )" :disabled="$disabledPropertyRjStatus"
                    wire:model.debounce.500ms="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.keluargaDekat.nama" />
                @error('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.keluargaDekat.nama')
                    <x-input-error :messages="$message" />
                @enderror
            </div>
            <div class="mt-2">
                <x-input-label
                    for="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.keluargaDekat.hubungan"
                    :value="__('Hubungan')" :required="__(true)" />
                <x-text-input
                    id="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.keluargaDekat.hubungan"
                    placeholder="Hubungan" class="mt-1" :errorshas="__(
                        $errors->has(
                            'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.keluargaDekat.hubungan',
                        ),
                    )" :disabled="$disabledPropertyRjStatus"
                    wire:model.debounce.500ms="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.keluargaDekat.hubungan" />
                @error('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.keluargaDekat.hubungan')
                    <x-input-error :messages="$message" />
                @enderror
            </div>
            <div class="mt-2">
                <x-input-label
                    for="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.keluargaDekat.telp"
                    :value="__('Telepon')" :required="__(true)" />
                <x-text-input
                    id="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.keluargaDekat.telp"
                    placeholder="Telepon" class="mt-1" :errorshas="__(
                        $errors->has(
                            'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.keluargaDekat.telp',
                        ),
                    )" :disabled="$disabledPropertyRjStatus"
                    wire:model.debounce.500ms="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.keluargaDekat.telp" />
                @error('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.keluargaDekat.telp')
                    <x-input-error :messages="$message" />
                @enderror
            </div>
        </div>

        <!-- Informasi Didapat Dari -->
        <div>
            <x-input-label
                for="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.informasiDidapatDari.pilihan"
                :value="__('Informasi Didapat Dari')" :required="__(true)" />
            <div class="grid grid-cols-4 gap-2">
                @foreach ($options['informasiDidapatDariOptions'] as $option)
                    <x-radio-button :label="__($option)" value="{{ $option }}"
                        wire:model="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.informasiDidapatDari.pilihan" />
                @endforeach
            </div>
            <!-- Input teks untuk opsi "lainnya" -->
            @if (
                $dataDaftarRi['pengkajianAwalPasienRawatInap']['bagian3PsikososialDanEkonomi']['informasiDidapatDari'][
                    'pilihan'
                ] === 'lainnya')
                <div class="mt-2">
                    <x-text-input
                        id="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.informasiDidapatDari.keterangan"
                        placeholder="Silakan jelaskan sumber informasi lainnya" class="mt-1"
                        wire:model.debounce.500ms="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.informasiDidapatDari.keterangan" />
                    @error('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.informasiDidapatDari.keterangan')
                        <x-input-error :messages="$message" />
                    @enderror
                </div>
            @endif
            @error('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.informasiDidapatDari.pilihan')
                <x-input-error :messages="$message" />
            @enderror
        </div>
    </div>
</div>
