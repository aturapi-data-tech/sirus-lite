<div>
    <div class="space-y-4">
        <!-- Tanda Vital -->
        <div>
            <x-input-label for="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital"
                :value="__('Tanda Vital')" :required="__(true)" class="pt-2 sm:text-xl" />

            <!-- Tekanan Darah -->
            <div class="mb-2">
                <x-input-label
                    for="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.sistolik"
                    :value="__('Tekanan Darah')" :required="__(false)" />
                <div class="grid grid-cols-2 gap-2">
                    <x-text-input-mou
                        id="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.sistolik"
                        placeholder="Sistolik" class="mt-1 ml-2" :errorshas="__(
                            $errors->has(
                                'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.sistolik',
                            ),
                        )" :disabled="$disabledPropertyRjStatus" :mou_label="__('mmHg')"
                        wire:model.debounce.500ms="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.sistolik" />
                    <x-text-input-mou
                        id="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.distolik"
                        placeholder="Distolik" class="mt-1 ml-2" :errorshas="__(
                            $errors->has(
                                'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.distolik',
                            ),
                        )" :disabled="$disabledPropertyRjStatus" :mou_label="__('mmHg')"
                        wire:model.debounce.500ms="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.distolik" />
                </div>
            </div>

            <!-- Frekuensi Nadi dan Nafas -->
            <div class="mb-2">
                <div class="grid grid-cols-2 gap-2">
                    <x-input-label
                        for="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.frekuensiNadi"
                        :value="__('Frekuensi Nadi')" :required="__(false)" />
                    <x-input-label
                        for="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.frekuensiNafas"
                        :value="__('Frekuensi Nafas')" :required="__(false)" />
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <x-text-input-mou
                        id="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.frekuensiNadi"
                        placeholder="Frekuensi Nadi" class="mt-1 ml-2" :errorshas="__(
                            $errors->has(
                                'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.frekuensiNadi',
                            ),
                        )" :disabled="$disabledPropertyRjStatus"
                        :mou_label="__('X/Menit')"
                        wire:model.debounce.500ms="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.frekuensiNadi" />
                    <x-text-input-mou
                        id="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.frekuensiNafas"
                        placeholder="Frekuensi Nafas" class="mt-1 ml-2" :errorshas="__(
                            $errors->has(
                                'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.frekuensiNafas',
                            ),
                        )" :disabled="$disabledPropertyRjStatus"
                        :mou_label="__('X/Menit')"
                        wire:model.debounce.500ms="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.frekuensiNafas" />
                </div>
            </div>

            <!-- Suhu -->
            <div class="mb-2">
                <x-input-label for="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.suhu"
                    :value="__('Suhu')" :required="__(false)" />
                <x-text-input-mou
                    id="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.suhu"
                    placeholder="Suhu" class="mt-1 ml-2" :errorshas="__(
                        $errors->has(
                            'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.suhu',
                        ),
                    )" :disabled="$disabledPropertyRjStatus" :mou_label="__('°C')"
                    wire:model.debounce.500ms="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.suhu" />
            </div>

            <!-- SPO2 dan GDA -->
            <div class="mb-2">
                <div class="grid grid-cols-2 gap-2">
                    <x-input-label
                        for="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.spo2"
                        :value="__('SPO2')" :required="__(false)" />
                    <x-input-label
                        for="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.gda"
                        :value="__('GDA')" :required="__(false)" />
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <x-text-input-mou
                        id="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.spo2"
                        placeholder="SPO2" class="mt-1 ml-2" :errorshas="__(
                            $errors->has(
                                'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.spo2',
                            ),
                        )" :disabled="$disabledPropertyRjStatus" :mou_label="__('%')"
                        wire:model.debounce.500ms="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.spo2" />
                    <x-text-input-mou
                        id="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.gda"
                        placeholder="GDA" class="mt-1 ml-2" :errorshas="__(
                            $errors->has(
                                'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.gda',
                            ),
                        )" :disabled="$disabledPropertyRjStatus" :mou_label="__('g/dl')"
                        wire:model.debounce.500ms="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.gda" />
                </div>
            </div>

            <!-- TB (Tinggi Badan) dan BB (Berat Badan) -->
            <div class="mb-2">
                <div class="grid grid-cols-2 gap-2">
                    <x-input-label
                        for="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.tb"
                        :value="__('Tinggi Badan (TB)')" :required="__(false)" />
                    <x-input-label
                        for="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.bb"
                        :value="__('Berat Badan (BB)')" :required="__(false)" />
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <x-text-input-mou
                        id="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.tb"
                        placeholder="Tinggi Badan" class="mt-1 ml-2" :errorshas="__(
                            $errors->has(
                                'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.tb',
                            ),
                        )" :disabled="$disabledPropertyRjStatus"
                        :mou_label="__('cm')"
                        wire:model.debounce.500ms="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.tb" />
                    <x-text-input-mou
                        id="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.bb"
                        placeholder="Berat Badan" class="mt-1 ml-2" :errorshas="__(
                            $errors->has(
                                'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.bb',
                            ),
                        )" :disabled="$disabledPropertyRjStatus"
                        :mou_label="__('kg')"
                        wire:model.debounce.500ms="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital.bb" />
                </div>
            </div>
        </div>

        <!-- Keluhan Utama -->
        <div>
            <x-input-label for="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.keluhanUtama"
                :value="__('Keluhan Utama')" :required="__(true)" />
            <x-text-input id="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.keluhanUtama"
                placeholder="Keluhan Utama" class="mt-1" :errorshas="__(
                    $errors->has('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.keluhanUtama'),
                )" :disabled="$disabledPropertyRjStatus"
                wire:model.debounce.500ms="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.keluhanUtama" />
            @error('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.keluhanUtama')
                <x-input-error :messages="$message" />
            @enderror
        </div>

        <!-- Pemeriksaan Sistem Organ -->
        <div>
            <x-input-label
                for="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan"
                :value="__('Pemeriksaan Sistem Organ')" :required="__(true)" />
            <div class="mt-2 space-y-4">
                <!-- Mata, Telinga, Hidung, Tenggorokan -->
                <div>
                    <x-input-label
                        for="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.mataTelingaHidungTenggorokan.pilihan"
                        :value="__('Mata, Telinga, Hidung, Tenggorokan')" :required="__(true)" />
                    <div class="grid grid-cols-4 gap-2">
                        @foreach ($options['mataTelingaHidungTenggorokanOptions'] as $option)
                            <x-radio-button :label="__($option)" value="{{ $option }}"
                                wire:model="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.mataTelingaHidungTenggorokan.pilihan" />
                        @endforeach
                    </div>
                    <!-- Input teks untuk opsi "lainnya" -->
                    @if (
                        $dataDaftarRi['pengkajianAwalPasienRawatInap']['bagian4PemeriksaanFisik']['pemeriksaanSistemOrgan'][
                            'mataTelingaHidungTenggorokan'
                        ]['pilihan'] === 'lainnya')
                        <div class="mt-2">
                            <x-text-input
                                id="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.mataTelingaHidungTenggorokan.keterangan"
                                placeholder="Silakan jelaskan kondisi lainnya" class="mt-1"
                                wire:model.debounce.500ms="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.mataTelingaHidungTenggorokan.keterangan" />
                            @error('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.mataTelingaHidungTenggorokan.keterangan')
                                <x-input-error :messages="$message" />
                            @enderror
                        </div>
                    @endif
                    @error('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.mataTelingaHidungTenggorokan.pilihan')
                        <x-input-error :messages="$message" />
                    @enderror
                </div>

                <!-- Paru -->
                <div>
                    <x-input-label
                        for="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.paru.pilihan"
                        :value="__('Paru')" :required="__(true)" />
                    <div class="grid grid-cols-4 gap-2">
                        @foreach ($options['paruOptions'] as $option)
                            <x-radio-button :label="__($option)" value="{{ $option }}"
                                wire:model="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.paru.pilihan" />
                        @endforeach
                    </div>
                    <!-- Input teks untuk opsi "lainnya" -->
                    @if (
                        $dataDaftarRi['pengkajianAwalPasienRawatInap']['bagian4PemeriksaanFisik']['pemeriksaanSistemOrgan']['paru'][
                            'pilihan'
                        ] === 'lainnya')
                        <div class="mt-2">
                            <x-text-input
                                id="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.paru.keterangan"
                                placeholder="Silakan jelaskan kondisi lainnya" class="mt-1"
                                wire:model.debounce.500ms="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.paru.keterangan" />
                            @error('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.paru.keterangan')
                                <x-input-error :messages="$message" />
                            @enderror
                        </div>
                    @endif
                    @error('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.paru.pilihan')
                        <x-input-error :messages="$message" />
                    @enderror
                </div>

                <!-- Jantung -->
                <div>
                    <x-input-label
                        for="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.jantung.pilihan"
                        :value="__('Jantung')" :required="__(true)" />
                    <div class="grid grid-cols-4 gap-2">
                        @foreach ($options['jantungOptions'] as $option)
                            <x-radio-button :label="__($option)" value="{{ $option }}"
                                wire:model="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.jantung.pilihan" />
                        @endforeach
                    </div>
                    <!-- Input teks untuk opsi "lainnya" -->
                    @if (
                        $dataDaftarRi['pengkajianAwalPasienRawatInap']['bagian4PemeriksaanFisik']['pemeriksaanSistemOrgan']['jantung'][
                            'pilihan'
                        ] === 'lainnya')
                        <div class="mt-2">
                            <x-text-input
                                id="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.jantung.keterangan"
                                placeholder="Silakan jelaskan kondisi lainnya" class="mt-1"
                                wire:model.debounce.500ms="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.jantung.keterangan" />
                            @error('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.jantung.keterangan')
                                <x-input-error :messages="$message" />
                            @enderror
                        </div>
                    @endif
                    @error('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.jantung.pilihan')
                        <x-input-error :messages="$message" />
                    @enderror
                </div>

                <!-- Neurologi -->
                <div>
                    <x-input-label
                        for="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.neurologi.tingkatKesadaran.pilihan"
                        :value="__('Neurologi')" :required="__(true)" />
                    <div class="mt-2">
                        <x-input-label
                            for="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.neurologi.tingkatKesadaran.pilihan"
                            :value="__('Tingkat Kesadaran')" :required="__(true)" />
                        <div class="grid grid-cols-4 gap-2">
                            @foreach ($options['tingkatKesadaranOptions'] as $option)
                                <x-radio-button :label="__($option)" value="{{ $option }}"
                                    wire:model="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.neurologi.tingkatKesadaran.pilihan" />
                            @endforeach
                        </div>
                        @error('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.neurologi.tingkatKesadaran.pilihan')
                            <x-input-error :messages="$message" />
                        @enderror
                    </div>
                    <div class="mt-2">
                        <x-input-label
                            for="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.neurologi.gcs"
                            :value="__('GCS')" :required="__(true)" />
                        <x-text-input
                            id="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.neurologi.gcs"
                            placeholder="GCS" class="mt-1" :errorshas="__(
                                $errors->has(
                                    'dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.neurologi.gcs',
                                ),
                            )" :disabled="$disabledPropertyRjStatus"
                            wire:model.debounce.500ms="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.neurologi.gcs" />
                        @error('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.neurologi.gcs')
                            <x-input-error :messages="$message" />
                        @enderror
                    </div>
                </div>

                <!-- Gastrointestinal -->
                <div>
                    <x-input-label
                        for="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.gastrointestinal.pilihan"
                        :value="__('Gastrointestinal')" :required="__(true)" />
                    <div class="grid grid-cols-4 gap-2">
                        @foreach ($options['gastrointestinalOptions'] as $option)
                            <x-radio-button :label="__($option)" value="{{ $option }}"
                                wire:model="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.gastrointestinal.pilihan" />
                        @endforeach
                    </div>
                    <!-- Input teks untuk opsi "lainnya" -->
                    @if (
                        $dataDaftarRi['pengkajianAwalPasienRawatInap']['bagian4PemeriksaanFisik']['pemeriksaanSistemOrgan'][
                            'gastrointestinal'
                        ]['pilihan'] === 'lainnya')
                        <div class="mt-2">
                            <x-text-input
                                id="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.gastrointestinal.keterangan"
                                placeholder="Silakan jelaskan kondisi lainnya" class="mt-1"
                                wire:model.debounce.500ms="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.gastrointestinal.keterangan" />
                            @error('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.gastrointestinal.keterangan')
                                <x-input-error :messages="$message" />
                            @enderror
                        </div>
                    @endif
                    @error('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.gastrointestinal.pilihan')
                        <x-input-error :messages="$message" />
                    @enderror
                </div>

                <!-- Genitourinaria -->
                <div>
                    <x-input-label
                        for="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.genitourinaria.pilihan"
                        :value="__('Genitourinaria')" :required="__(true)" />
                    <div class="grid grid-cols-4 gap-2">
                        @foreach ($options['genitourinariaOptions'] as $option)
                            <x-radio-button :label="__($option)" value="{{ $option }}"
                                wire:model="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.genitourinaria.pilihan" />
                        @endforeach
                    </div>
                    <!-- Input teks untuk opsi "lainnya" -->
                    @if (
                        $dataDaftarRi['pengkajianAwalPasienRawatInap']['bagian4PemeriksaanFisik']['pemeriksaanSistemOrgan'][
                            'genitourinaria'
                        ]['pilihan'] === 'lainnya')
                        <div class="mt-2">
                            <x-text-input
                                id="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.genitourinaria.keterangan"
                                placeholder="Silakan jelaskan kondisi lainnya" class="mt-1"
                                wire:model.debounce.500ms="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.genitourinaria.keterangan" />
                            @error('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.genitourinaria.keterangan')
                                <x-input-error :messages="$message" />
                            @enderror
                        </div>
                    @endif
                    @error('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.genitourinaria.pilihan')
                        <x-input-error :messages="$message" />
                    @enderror
                </div>

                <!-- Muskuloskeletal dan Kulit -->
                <div>
                    <x-input-label
                        for="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.muskuloskeletalDanKulit.pilihan"
                        :value="__('Muskuloskeletal dan Kulit')" :required="__(true)" />
                    <div class="grid grid-cols-4 gap-2">
                        @foreach ($options['muskuloskeletalDanKulitOptions'] as $option)
                            <x-radio-button :label="__($option)" value="{{ $option }}"
                                wire:model="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.muskuloskeletalDanKulit.pilihan" />
                        @endforeach
                    </div>
                    <!-- Input teks untuk opsi "lainnya" -->
                    @if (
                        $dataDaftarRi['pengkajianAwalPasienRawatInap']['bagian4PemeriksaanFisik']['pemeriksaanSistemOrgan'][
                            'muskuloskeletalDanKulit'
                        ]['pilihan'] === 'lainnya')
                        <div class="mt-2">
                            <x-text-input
                                id="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.muskuloskeletalDanKulit.keterangan"
                                placeholder="Silakan jelaskan kondisi lainnya" class="mt-1"
                                wire:model.debounce.500ms="dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.muskuloskeletalDanKulit.keterangan" />
                            @error('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.muskuloskeletalDanKulit.keterangan')
                                <x-input-error :messages="$message" />
                            @enderror
                        </div>
                    @endif
                    @error('dataDaftarRi.pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.muskuloskeletalDanKulit.pilihan')
                        <x-input-error :messages="$message" />
                    @enderror
                </div>
            </div>
        </div>
    </div>
</div>
