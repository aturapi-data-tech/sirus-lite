<div>
    @php
        // Control interaksi (mis. kunci saat status tertentu)
        $disabledProperty = false;
        $disabledPropertyRiStatus = false; // set true kalau mau lock form
    @endphp

    <div class="w-full mb-1">
        <div class="w-full p-4 text-sm">
            <h2 class="text-2xl font-bold text-center">Formulir Edukasi Terintegrasi Pasien & Keluarga</h2>
            <br />

            <div class="w-full p-2 m-2 mx-auto bg-white rounded-lg shadow-md">

                {{-- =========================
                     1) TUJUAN EDUKASI
                   ========================= --}}
                <div class="space-y-2">
                    {{-- =========================
                        HEADER: Waktu & Petugas
                    ========================= --}}
                    <div class="space-y-3">
                        {{-- Tanggal Edukasi --}}
                        <div>
                            <x-input-label :value="__('Tanggal Edukasi (dd/mm/yyyy hh:ii:ss)')" :required="true" />
                            <div class="flex gap-2">
                                <x-text-input placeholder="dd/mm/yyyy hh:ii:ss" class="flex-1 mt-1 ml-2" :errorshas="$errors->has('formEdukasiPasienTerintegrasi.tglEdukasi')"
                                    wire:model.debounce.500ms="formEdukasiPasienTerintegrasi.tglEdukasi"
                                    :disabled="$disabledPropertyRiStatus" />
                                <x-primary-button type="button" class="mt-1 whitespace-nowrap"
                                    wire:click="$set('formEdukasiPasienTerintegrasi.tglEdukasi','{{ now()->format('d/m/Y H:i:s') }}')"
                                    :disabled="$disabledPropertyRiStatus">
                                    Waktu Sekarang
                                </x-primary-button>
                            </div>
                            @error('formEdukasiPasienTerintegrasi.tglEdukasi')
                                <x-input-error :messages="$message" />
                            @enderror
                        </div>

                        {{-- Pemberi Informasi (auto dari user login, bisa di-edit jika perlu) --}}
                        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                            {{-- <div>
                                <x-input-label :value="__('Kode Petugas')" :required="true" />
                                <x-text-input hidden ="Kode Petugas" class="mt-1 ml-2" :errorshas="$errors->has('formEdukasiPasienTerintegrasi.pemberiInformasi.petugasCode')"
                                    wire:model.lazy="formEdukasiPasienTerintegrasi.pemberiInformasi.petugasCode"
                                    :disabled="$disabledPropertyRiStatus" />
                                @error('formEdukasiPasienTerintegrasi.pemberiInformasi.petugasCode')
                                    <x-input-error :messages="$message" />
                                @enderror
                            </div> --}}
                            <div>
                                <x-input-label :value="__('Nama Petugas')" :required="true" />
                                <x-text-input placeholder="Nama Petugas" class="mt-1 ml-2" :errorshas="$errors->has('formEdukasiPasienTerintegrasi.pemberiInformasi.petugasName')"
                                    wire:model.lazy="formEdukasiPasienTerintegrasi.pemberiInformasi.petugasName"
                                    :disabled="$disabledPropertyRiStatus" />
                                @error('formEdukasiPasienTerintegrasi.pemberiInformasi.petugasName')
                                    <x-input-error :messages="$message" />
                                @enderror
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">


                    <x-input-label :value="__('1) Tujuan Edukasi (boleh lebih dari satu)')" class="sm:text-lg" />
                    @php
                        $tujuanList = [
                            'penyakit' => 'Pemahaman penyakit/diagnosis',
                            'obat' => 'Penggunaan obat yang aman',
                            'nutrisi' => 'Nutrisi & diet',
                            'aktivitas' => 'Aktivitas & latihan',
                            'perawatanRumah' => 'Perawatan di rumah',
                            'pencegahan' => 'Pencegahan komplikasi',
                            'lainnya' => 'Lainnya',
                        ];
                    @endphp
                    <div class="grid grid-cols-1 gap-2 md:grid-cols-3">
                        @foreach ($tujuanList as $key => $label)
                            <label class="inline-flex items-center gap-2" wire:key="tujuan-{{ $key }}">
                                <input type="checkbox" class="rounded" value="{{ $key }}"
                                    wire:model="formEdukasiPasienTerintegrasi.tujuan.opsi">
                                <span>{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>

                    @if (in_array('lainnya', $formEdukasiPasienTerintegrasi['tujuan']['opsi'] ?? []))
                        <x-text-input placeholder="Sebutkan tujuan lainnya" class="mt-1 ml-2" :errorshas="$errors->has('formEdukasiPasienTerintegrasi.tujuan.lainnya')"
                            wire:model.lazy="formEdukasiPasienTerintegrasi.tujuan.lainnya" />
                        @error('formEdukasiPasienTerintegrasi.tujuan.lainnya')
                            <x-input-error :messages="$message" />
                        @enderror
                    @endif
                </div>

                <hr class="my-4">

                {{-- =========================
                     2) EVALUASI AWAL & NILAI
                   ========================= --}}
                <div class="space-y-3">
                    <x-input-label :value="__('2) Evaluasi Awal Kemampuan & Nilai')" class="sm:text-lg" />

                    <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                        {{-- Literasi --}}
                        <div>
                            <x-input-label :value="__('Kemampuan membaca/menulis')" />
                            <div class="flex flex-wrap gap-3 mt-1 ml-2">
                                @foreach (['Baik', 'Cukup', 'Kurang'] as $opt)
                                    <x-radio-button :label="$opt" value="{{ $opt }}"
                                        wire:model="formEdukasiPasienTerintegrasi.evaluasiAwal.literasi"
                                        :disabled="$disabledPropertyRiStatus"
                                        wire:key="literasi-{{ \Illuminate\Support\Str::slug($opt) }}" />
                                @endforeach
                            </div>
                            @error('formEdukasiPasienTerintegrasi.evaluasiAwal.literasi')
                                <x-input-error :messages="$message" />
                            @enderror
                        </div>

                        {{-- Bahasa/pendidikan --}}
                        <div>
                            <x-input-label :value="__('Bahasa yang digunakan / tingkat pendidikan')" />
                            <x-text-input placeholder="Contoh: Indonesia / SMA" class="mt-1 ml-2" :errorshas="$errors->has('formEdukasiPasienTerintegrasi.evaluasiAwal.bahasaAtauPendidikan')"
                                wire:model.lazy="formEdukasiPasienTerintegrasi.evaluasiAwal.bahasaAtauPendidikan"
                                :disabled="$disabledPropertyRiStatus" />
                            @error('formEdukasiPasienTerintegrasi.evaluasiAwal.bahasaAtauPendidikan')
                                <x-input-error :messages="$message" />
                            @enderror
                        </div>
                    </div>

                    {{-- Hambatan emosional & Keterbatasan fisik/kognitif --}}
                    <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                        <div>
                            <x-input-label :value="__('Hambatan emosional / motivasi')" />
                            <div class="flex flex-wrap gap-3 mt-1 ml-2">
                                <x-radio-button label="Ada" value="1"
                                    wire:model="formEdukasiPasienTerintegrasi.evaluasiAwal.hambatanEmosional.ada"
                                    :disabled="$disabledPropertyRiStatus" />
                                <x-radio-button label="Tidak ada" value="0"
                                    wire:model="formEdukasiPasienTerintegrasi.evaluasiAwal.hambatanEmosional.ada"
                                    :disabled="$disabledPropertyRiStatus" />
                            </div>
                            <x-text-input placeholder="Keterangan jika ada hambatan" class="mt-1 ml-2"
                                :errorshas="$errors->has(
                                    'formEdukasiPasienTerintegrasi.evaluasiAwal.hambatanEmosional.keterangan',
                                )"
                                wire:model.lazy="formEdukasiPasienTerintegrasi.evaluasiAwal.hambatanEmosional.keterangan"
                                :disabled="$disabledPropertyRiStatus" />
                            @error('formEdukasiPasienTerintegrasi.evaluasiAwal.hambatanEmosional.keterangan')
                                <x-input-error :messages="$message" />
                            @enderror
                        </div>

                        <div>
                            <x-input-label :value="__('Keterbatasan fisik / kognitif')" />
                            <div class="flex flex-wrap gap-3 mt-1 ml-2">
                                <x-radio-button label="Ada" value="1"
                                    wire:model="formEdukasiPasienTerintegrasi.evaluasiAwal.keterbatasanFisikKognitif.ada"
                                    :disabled="$disabledPropertyRiStatus" />
                                <x-radio-button label="Tidak ada" value="0"
                                    wire:model="formEdukasiPasienTerintegrasi.evaluasiAwal.keterbatasanFisikKognitif.ada"
                                    :disabled="$disabledPropertyRiStatus" />
                            </div>
                            <x-text-input placeholder="Keterangan jika ada keterbatasan" class="mt-1 ml-2"
                                :errorshas="$errors->has(
                                    'formEdukasiPasienTerintegrasi.evaluasiAwal.keterbatasanFisikKognitif.keterangan',
                                )"
                                wire:model.lazy="formEdukasiPasienTerintegrasi.evaluasiAwal.keterbatasanFisikKognitif.keterangan"
                                :disabled="$disabledPropertyRiStatus" />
                            @error('formEdukasiPasienTerintegrasi.evaluasiAwal.keterbatasanFisikKognitif.keterangan')
                                <x-input-error :messages="$message" />
                            @enderror
                        </div>
                    </div>

                    {{-- Nilai/Keyakinan/Budaya --}}
                    <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                        <div>
                            <x-input-label :value="__('Nilai, keyakinan, dan budaya yang dianut')" />
                            <div class="flex flex-wrap gap-3 mt-1 ml-2">
                                <x-radio-button label="Ada" value="1"
                                    wire:model="formEdukasiPasienTerintegrasi.evaluasiAwal.nilaiKeyakinanBudaya.ada"
                                    :disabled="$disabledPropertyRiStatus" />
                                <x-radio-button label="Tidak ada" value="0"
                                    wire:model="formEdukasiPasienTerintegrasi.evaluasiAwal.nilaiKeyakinanBudaya.ada"
                                    :disabled="$disabledPropertyRiStatus" />
                            </div>
                            <x-text-input-area rows="2" class="w-full mt-1 ml-2"
                                placeholder="Jelaskan nilai/kepercayaan/budaya yang relevan"
                                wire:model.lazy="formEdukasiPasienTerintegrasi.evaluasiAwal.nilaiKeyakinanBudaya.deskripsi"
                                :disabled="$disabledPropertyRiStatus"></x-text-input-area>
                            @error('formEdukasiPasienTerintegrasi.evaluasiAwal.nilaiKeyakinanBudaya.deskripsi')
                                <x-input-error :messages="$message" />
                            @enderror
                        </div>

                        {{-- Preferensi penerimaan informasi --}}
                        <div>
                            <x-input-label :value="__('Preferensi menerima informasi')" />
                            @php
                                $prefList = [
                                    'lisan' => 'Lisan',
                                    'tulisan' => 'Tulisan',
                                    'demonstrasi' => 'Demonstrasi',
                                    'video' => 'Video',
                                    'poster' => 'Poster',
                                    'lainnya' => 'Lainnya',
                                ];
                            @endphp
                            <div class="flex flex-wrap gap-3 mt-1 ml-2">
                                @foreach ($prefList as $k => $lbl)
                                    <label class="inline-flex items-center gap-2" wire:key="pref-{{ $k }}">
                                        <input type="checkbox" class="rounded" value="{{ $k }}"
                                            wire:model="formEdukasiPasienTerintegrasi.evaluasiAwal.preferensiInformasi.opsi"
                                            @checked(in_array($k, $formEdukasiPasienTerintegrasi['evaluasiAwal']['preferensiInformasi']['opsi'] ?? []))>
                                        <span>{{ $lbl }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @if (in_array('lainnya', $formEdukasiPasienTerintegrasi['evaluasiAwal']['preferensiInformasi']['opsi'] ?? []))
                                <x-text-input placeholder="Sebutkan preferensi lainnya" class="mt-1 ml-2"
                                    :errorshas="$errors->has(
                                        'formEdukasiPasienTerintegrasi.evaluasiAwal.preferensiInformasi.lainnya',
                                    )"
                                    wire:model.lazy="formEdukasiPasienTerintegrasi.evaluasiAwal.preferensiInformasi.lainnya"
                                    :disabled="$disabledPropertyRiStatus" />
                                @error('formEdukasiPasienTerintegrasi.evaluasiAwal.preferensiInformasi.lainnya')
                                    <x-input-error :messages="$message" />
                                @enderror
                            @endif
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                {{-- =========================
                     3) KEBUTUHAN EDUKASI
                   ========================= --}}
                <div class="space-y-2">
                    <x-input-label :value="__('3) Kebutuhan Edukasi (boleh lebih dari satu)')" class="sm:text-lg" />
                    @php
                        $kebutuhanList = [
                            'penyakitHasil' => 'Penjelasan penyakit & hasil pemeriksaan',
                            'prosedur' => 'Prosedur/tindakan medis',
                            'rencanaAsuhan' => 'Rencana asuhan & tindak lanjut',
                            'obatEfek' => 'Penggunaan obat & efek samping',
                            'cuciTangan' => 'Cuci tangan & pencegahan infeksi',
                            'alatRumah' => 'Penggunaan alat medis di rumah',
                            'warningSign' => 'Tanda bahaya yang perlu diwaspadai',
                            'lainnya' => 'Lainnya',
                        ];
                    @endphp
                    <div class="grid grid-cols-1 gap-2 md:grid-cols-2">
                        @foreach ($kebutuhanList as $key => $label)
                            <label class="inline-flex items-center gap-2" wire:key="need-{{ $key }}">
                                <input type="checkbox" class="rounded" value="{{ $key }}"
                                    wire:model="formEdukasiPasienTerintegrasi.kebutuhan.opsi">
                                <span>{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>

                    @if (in_array('lainnya', $formEdukasiPasienTerintegrasi['kebutuhan']['opsi'] ?? []))
                        <x-text-input placeholder="Sebutkan kebutuhan lainnya" class="mt-1 ml-2" :errorshas="$errors->has('formEdukasiPasienTerintegrasi.kebutuhan.lainnya')"
                            wire:model.lazy="formEdukasiPasienTerintegrasi.kebutuhan.lainnya" />
                        @error('formEdukasiPasienTerintegrasi.kebutuhan.lainnya')
                            <x-input-error :messages="$message" />
                        @enderror
                    @endif
                </div>

                <hr class="my-4">

                {{-- =========================
                     4) METODE & MEDIA
                   ========================= --}}
                <div class="space-y-2">
                    <x-input-label :value="__('4) Metode & Media Edukasi')" class="sm:text-lg" />
                    @php
                        $metodeList = [
                            'lisan' => 'Penjelasan lisan',
                            'demonstrasi' => 'Demonstrasi/praktik langsung',
                            'leaflet' => 'Leaflet/brosur',
                            'video' => 'Video edukasi',
                            'poster' => 'Poster/peraga',
                            'lainnya' => 'Lainnya',
                        ];
                    @endphp
                    <div class="grid grid-cols-1 gap-2 md:grid-cols-3">
                        @foreach ($metodeList as $key => $label)
                            <label class="inline-flex items-center gap-2" wire:key="metode-{{ $key }}">
                                <input type="checkbox" class="rounded" value="{{ $key }}"
                                    wire:model="formEdukasiPasienTerintegrasi.metodeMedia.opsi">
                                <span>{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>

                    @if (in_array('lainnya', $formEdukasiPasienTerintegrasi['metodeMedia']['opsi'] ?? []))
                        <x-text-input placeholder="Sebutkan metode/media lainnya" class="mt-1 ml-2" :errorshas="$errors->has('formEdukasiPasienTerintegrasi.metodeMedia.lainnya')"
                            wire:model.lazy="formEdukasiPasienTerintegrasi.metodeMedia.lainnya" />
                        @error('formEdukasiPasienTerintegrasi.metodeMedia.lainnya')
                            <x-input-error :messages="$message" />
                        @enderror
                    @endif
                </div>

                <hr class="my-4">

                {{-- =========================
                     5) HASIL EDUKASI
                   ========================= --}}
                <div class="space-y-2">
                    <x-input-label :value="__('5) Hasil Edukasi')" class="sm:text-lg" />
                    @php
                        $hasilList = [
                            'paham' => 'Pasien/keluarga memahami informasi',
                            'mampuMengulang' => 'Dapat mengulang kembali informasi',
                            'tunjukkanSkill' => 'Menunjukkan keterampilan yang diajarkan',
                            'sesuaiNilai' => 'Edukasi sesuai nilai & keyakinan pasien',
                            'perluEdukasiUlang' => 'Diperlukan edukasi ulang',
                        ];
                    @endphp

                    <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                        @foreach ($hasilList as $key => $label)
                            <div class="p-3 border rounded-lg bg-gray-50" wire:key="hasil-{{ $key }}">
                                <div class="flex items-center justify-between">
                                    <span>{{ $label }}</span>
                                    <div class="flex items-center gap-3">
                                        <x-radio-button label="Ya" value="1"
                                            wire:model="formEdukasiPasienTerintegrasi.hasil.{{ $key }}.ya"
                                            :disabled="$disabledPropertyRiStatus" />
                                        <x-radio-button label="Tidak" value="0"
                                            wire:model="formEdukasiPasienTerintegrasi.hasil.{{ $key }}.ya"
                                            :disabled="$disabledPropertyRiStatus" />
                                    </div>
                                </div>
                                <x-text-input placeholder="Keterangan" class="w-full mt-2" :errorshas="$errors->has('formEdukasiPasienTerintegrasi.hasil.' . $key . '.keterangan')"
                                    wire:model.lazy="formEdukasiPasienTerintegrasi.hasil.{{ $key }}.keterangan"
                                    :disabled="$disabledPropertyRiStatus" />
                                @error('formEdukasiPasienTerintegrasi.hasil.' . $key . '.keterangan')
                                    <x-input-error :messages="$message" />
                                @enderror
                            </div>
                        @endforeach
                    </div>
                </div>

                <hr class="my-4">

                {{-- =========================
                     6) TINDAK LANJUT
                   ========================= --}}
                <div class="space-y-2">
                    <x-input-label :value="__('6) Tindak Lanjut')" class="sm:text-lg" />
                    <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
                        <div>
                            <x-input-label :value="__('Edukasi lanjutan (dd/mm/yyyy)')" />
                            <div class="flex gap-2">
                                <x-text-input placeholder="dd/mm/yyyy" class="flex-1 mt-1 ml-2" :errorshas="$errors->has(
                                    'formEdukasiPasienTerintegrasi.tindakLanjut.edukasiLanjutanTanggal',
                                )"
                                    wire:model.lazy="formEdukasiPasienTerintegrasi.tindakLanjut.edukasiLanjutanTanggal"
                                    :disabled="$disabledPropertyRiStatus" />
                                <x-primary-button type="button" class="mt-1 whitespace-nowrap"
                                    wire:click="$set('formEdukasiPasienTerintegrasi.tindakLanjut.edukasiLanjutanTanggal','{{ now()->format('d/m/Y') }}')"
                                    :disabled="$disabledPropertyRiStatus">
                                    Hari Ini
                                </x-primary-button>
                            </div>
                            @error('formEdukasiPasienTerintegrasi.tindakLanjut.edukasiLanjutanTanggal')
                                <x-input-error :messages="$message" />
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label :value="__('Rujuk ke (boleh lebih dari satu)')" />
                            @php
                                $rujukList = [
                                    'dietisien' => 'Dietisien',
                                    'farmasi' => 'Farmasi',
                                    'rehabilitasi' => 'Rehabilitasi',
                                    'psikologi' => 'Psikologi',
                                    'lainnya' => 'Lainnya',
                                ];
                            @endphp
                            <div class="flex flex-wrap gap-3 mt-1 ml-2">
                                @foreach ($rujukList as $k => $lbl)
                                    <label class="inline-flex items-center gap-2"
                                        wire:key="rujuk-{{ $k }}">
                                        <input type="checkbox" class="rounded" value="{{ $k }}"
                                            wire:model="formEdukasiPasienTerintegrasi.tindakLanjut.dirujukKe">
                                        <span>{{ $lbl }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('formEdukasiPasienTerintegrasi.tindakLanjut.dirujukKe.*')
                                <x-input-error :messages="$message" />
                            @enderror
                        </div>

                        <div class="md:col-span-3">
                            <label class="inline-flex items-center gap-2">
                                <input type="checkbox"
                                    wire:model="formEdukasiPasienTerintegrasi.tindakLanjut.tidakPerluTL"
                                    @checked(($formEdukasiPasienTerintegrasi['tindakLanjut']['tidakPerluTL'] ?? false) === true)>
                                <span>Tidak diperlukan tindak lanjut</span>
                            </label>
                            @error('formEdukasiPasienTerintegrasi.tindakLanjut.tidakPerluTL')
                                <x-input-error :messages="$message" />
                            @enderror
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                {{-- =========================
                     7) TANDA TANGAN
                   ========================= --}}
                <div class="grid content-center grid-cols-1 gap-2 p-2 m-2 md:grid-cols-3">
                    <div
                        class="flex items-end justify-center w-full p-2 m-2 mx-auto bg-white rounded-lg shadow-md md:col-span-3">
                        <div class="relative flex flex-col w-full gap-4 p-6 bg-white rounded-lg shadow-xl">
                            <x-input-label :value="__('Tanda Tangan Pasien/Keluarga')" />
                            <x-signature-pad wire:model.defer="sasaranEdukasiSignature" />

                            <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                                <div>
                                    <x-input-label :value="__('Nama Pasien/Keluarga')" :required="true" />
                                    <x-text-input placeholder="Nama pasien/keluarga" class="mt-1 ml-2"
                                        :errorshas="$errors->has('formEdukasiPasienTerintegrasi.ttd.pasienKeluargaNama')"
                                        wire:model.lazy="formEdukasiPasienTerintegrasi.ttd.pasienKeluargaNama"
                                        :disabled="$disabledPropertyRiStatus" />
                                    @error('formEdukasiPasienTerintegrasi.ttd.pasienKeluargaNama')
                                        <x-input-error :messages="$message" />
                                    @enderror
                                </div>


                            </div>

                            {{-- Sticky Actions --}}
                            <div class="sticky bottom-0 flex items-center gap-2 pt-2 bg-white">
                                <div wire:loading wire:target="addEdukasiPasien">
                                    <x-loading />
                                </div>
                                <x-primary-button :disabled="$disabledPropertyRiStatus" wire:click.prevent="addEdukasiPasien"
                                    type="button" class="text-white" wire:loading.remove>
                                    <i class="fas fa-save me-1"></i> Simpan
                                </x-primary-button>

                                {{-- Reset: panggil method reset khusus di component bila ada --}}
                                <x-light-button :disabled="$disabledPropertyRiStatus" wire:click="resetFormEdukasi()" type="button">
                                    <i class="fas fa-redo me-1"></i> Reset
                                </x-light-button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- RIWAYAT --}}
            @include('livewire.emr-r-i.edukasi-pasien-r-i.edukasi-pasien-terintegrasi-r-i-table')
        </div>
    </div>
</div>
