<div>
    @php
        $disabledProperty = false;
    @endphp

    <div class="w-full mb-1">
        <div class="w-full p-4 text-sm">

            {{-- JUDUL FORM --}}
            <h2 class="text-2xl font-bold text-center">
                FORM DATA PENJAMINAN BIAYA & ORIENTASI KAMAR PASIEN
            </h2>
            <br>

            {{-- DATA PASIEN --}}
            <div class="w-full p-2 m-2 mx-auto bg-white rounded-lg shadow-md">
                <x-input-label for="formPenjaminanOrientasiKamar.dataPasien" :value="__('Data Pasien')"
                    class="pb-1 mt-2 mb-3 font-semibold text-gray-800 border-b sm:text-lg" />

                @if (!empty($dataPasien['pasien']))
                    @php
                        $p = $dataPasien['pasien'];
                    @endphp

                    <div class="grid grid-cols-1 gap-4 mx-2 text-sm md:grid-cols-2">

                        <div class="space-y-3">
                            {{-- No. Rekam Medis --}}
                            <div>
                                <x-input-label for="formPenjaminanOrientasiKamar.pasien.regNo" :value="__('No. Rekam Medis')"
                                    class="text-[11px] font-semibold tracking-wide text-gray-500" />
                                <div class="text-sm text-gray-900 md:text-base">
                                    {{ $p['regNo'] ?? '-' }}
                                </div>
                            </div>

                            {{-- Nama Pasien --}}
                            <div>
                                <x-input-label for="formPenjaminanOrientasiKamar.pasien.regName" :value="__('Nama Pasien')"
                                    class="text-[11px] font-semibold tracking-wide text-gray-500" />
                                <div class="text-sm font-semibold text-gray-900 md:text-base">
                                    {{ trim(($p['gelarDepan'] ?? '') . ' ' . ($p['regName'] ?? '') . ' ' . ($p['gelarBelakang'] ?? '')) ?: '-' }}
                                </div>
                            </div>

                            {{-- Jenis Kelamin --}}
                            <div>
                                <x-input-label for="formPenjaminanOrientasiKamar.pasien.jenisKelamin" :value="__('Jenis Kelamin')"
                                    class="text-[11px] font-semibold tracking-wide text-gray-500" />
                                <div class="text-sm text-gray-900 md:text-base">
                                    {{ $p['jenisKelamin']['jenisKelaminDesc'] ?? '-' }}
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3">
                            {{-- Tanggal Lahir --}}
                            <div>
                                <x-input-label for="formPenjaminanOrientasiKamar.pasien.tglLahir" :value="__('Tanggal Lahir')"
                                    class="text-[11px] font-semibold tracking-wide text-gray-500" />
                                <div class="text-sm text-gray-900 md:text-base">
                                    {{ $p['tglLahir'] ?? '-' }}
                                </div>
                            </div>

                            {{-- Alamat Lengkap --}}
                            <div>
                                <x-input-label for="formPenjaminanOrientasiKamar.pasien.alamat" :value="__('Alamat Lengkap')"
                                    class="text-[11px] font-semibold tracking-wide text-gray-500" />
                                <div class="text-sm leading-snug text-gray-900 md:text-base">
                                    {{ $p['identitas']['alamat'] ?? '-' }},
                                    RT {{ $p['identitas']['rt'] ?? '-' }}/RW {{ $p['identitas']['rw'] ?? '-' }},
                                    {{ $p['identitas']['desaName'] ?? '' }},
                                    {{ $p['identitas']['kecamatanName'] ?? '' }},
                                    {{ $p['identitas']['kotaName'] ?? '' }},
                                    {{ $p['identitas']['propinsiName'] ?? '' }}
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="p-3 text-sm text-red-600 rounded-md bg-red-50">
                        Data pasien belum ditemukan.
                    </div>
                @endif
            </div>

            {{-- TANGGAL FORM --}}
            <div class="w-full p-2 m-2 mx-auto bg-white rounded-lg shadow-md">
                <div class="w-full px-2">
                    <x-input-label for="formPenjaminanOrientasiKamar.tanggalFormPenjaminan" :value="__('Tanggal Form Penjaminan')"
                        class="text-sm font-semibold text-gray-700" />
                </div>
                <div class="flex items-center gap-2">
                    <x-text-input id="formPenjaminanOrientasiKamar.tanggalFormPenjaminan"
                        placeholder="dd/mm/yyyy hh:mm:ss" class="w-full mt-1 ml-2"
                        wire:model.defer="formPenjaminanOrientasiKamar.tanggalFormPenjaminan" :disabled="$disabledProperty"
                        :errorshas="__($errors->has('formPenjaminanOrientasiKamar.tanggalFormPenjaminan'))" />

                    <x-green-button wire:click="setTanggalFormPenjaminan" class="mt-1 text-xs" :disabled="$disabledProperty">
                        Set Tanggal Form
                    </x-green-button>
                </div>
            </div>

            {{-- DATA PEMBUAT PERNYATAAN --}}
            <div class="w-full p-2 m-2 mx-auto bg-white rounded-lg shadow-md">
                <x-input-label for="formPenjaminanOrientasiKamar.pembuat" :value="__('Data Pembuat Pernyataan')"
                    class="pb-1 mt-2 mb-3 font-semibold text-gray-800 border-b sm:text-lg" />

                <div class="grid grid-cols-1 gap-4 mx-2 md:grid-cols-2">

                    {{-- KOLOM KIRI --}}
                    <div class="space-y-4">

                        {{-- Nama Pembuat --}}
                        <div>
                            <x-input-label for="formPenjaminanOrientasiKamar.pembuatNama" :value="__('Nama Pembuat Pernyataan')"
                                class="text-[11px] font-semibold tracking-wide text-gray-500 ml-1" />
                            <x-text-input :disabled="$disabledProperty" id="formPenjaminanOrientasiKamar.pembuatNama"
                                placeholder="Masukkan nama lengkap" class="mt-1 ml-1" :errorshas="__($errors->has('formPenjaminanOrientasiKamar.pembuatNama'))"
                                wire:model.lazy="formPenjaminanOrientasiKamar.pembuatNama" />
                        </div>

                        {{-- Hubungan Dengan Pasien --}}
                        <div>
                            <x-input-label for="formPenjaminanOrientasiKamar.hubunganDenganPasien" :value="__('Hubungan Dengan Pasien')"
                                class="text-[11px] font-semibold tracking-wide text-gray-500 ml-1" />
                            <x-text-input :disabled="$disabledProperty" id="formPenjaminanOrientasiKamar.hubunganDenganPasien"
                                placeholder="Contoh: diri sendiri, istri, anak" class="mt-1 ml-1" :errorshas="__($errors->has('formPenjaminanOrientasiKamar.hubunganDenganPasien'))"
                                wire:model.lazy="formPenjaminanOrientasiKamar.hubunganDenganPasien" />
                        </div>

                    </div>

                    {{-- KOLOM KANAN --}}
                    <div class="space-y-4">

                        {{-- Umur Pembuat --}}
                        <div>
                            <x-input-label for="formPenjaminanOrientasiKamar.pembuatUmur" :value="__('Umur Pembuat Pernyataan')"
                                class="text-[11px] font-semibold tracking-wide text-gray-500 ml-1" />
                            <x-text-input :disabled="$disabledProperty" id="formPenjaminanOrientasiKamar.pembuatUmur"
                                placeholder="Umur dalam tahun" class="mt-1 ml-1" :errorshas="__($errors->has('formPenjaminanOrientasiKamar.pembuatUmur'))"
                                wire:model.lazy="formPenjaminanOrientasiKamar.pembuatUmur" />
                        </div>

                        {{-- Jenis Kelamin --}}
                        <div>
                            <x-input-label for="formPenjaminanOrientasiKamar.pembuatJenisKelamin" :value="__('Jenis Kelamin')"
                                class="text-[11px] font-semibold tracking-wide text-gray-500 ml-1" />
                            <div class="flex gap-6 mt-2 ml-1">
                                <x-radio-button :label="__('Laki-laki')" value="L"
                                    wire:model="formPenjaminanOrientasiKamar.pembuatJenisKelamin" />
                                <x-radio-button :label="__('Perempuan')" value="P"
                                    wire:model="formPenjaminanOrientasiKamar.pembuatJenisKelamin" />
                            </div>
                            @error('formPenjaminanOrientasiKamar.pembuatJenisKelamin')
                                <x-input-error :messages="$message" />
                            @enderror
                        </div>

                        {{-- Alamat Pembuat --}}
                        <div>
                            <x-input-label for="formPenjaminanOrientasiKamar.pembuatAlamat" :value="__('Alamat Pembuat Pernyataan')"
                                class="text-[11px] font-semibold tracking-wide text-gray-500 ml-1" />
                            <x-text-input :disabled="$disabledProperty" id="formPenjaminanOrientasiKamar.pembuatAlamat"
                                placeholder="Alamat lengkap sesuai KTP" class="mt-1 ml-1" :errorshas="__($errors->has('formPenjaminanOrientasiKamar.pembuatAlamat'))"
                                wire:model.lazy="formPenjaminanOrientasiKamar.pembuatAlamat" />
                        </div>

                    </div>

                </div>
            </div>

            {{-- DATA PENJAMINAN BIAYA --}}
            <div class="w-full p-3 m-2 mx-auto bg-white rounded-lg shadow-md">
                <x-input-label for="formPenjaminanOrientasiKamar.penjaminan" :value="__('Data Penjaminan Biaya')"
                    class="pb-1 mt-2 mb-3 font-semibold text-gray-800 border-b sm:text-lg" />

                <div class="mx-2">

                    {{-- Kepemilikan Kartu Penjaminan --}}
                    <x-input-label for="formPenjaminanOrientasiKamar.jenisPenjamin" :value="__('Kepemilikan Kartu Penjaminan')"
                        class="text-[11px] font-semibold tracking-wide text-gray-500 mb-2" />

                    <div class="grid grid-cols-1 gap-3 ml-1 md:grid-cols-2">
                        @foreach ($jenisPenjaminOptions as $opt)
                            <div class="flex items-center gap-2">
                                <x-radio-button :label="__($opt['desc'])" value="{{ $opt['id'] }}"
                                    wire:model="formPenjaminanOrientasiKamar.jenisPenjamin" />
                            </div>
                        @endforeach
                    </div>

                    @error('formPenjaminanOrientasiKamar.jenisPenjamin')
                        <x-input-error :messages="$message" />
                    @enderror

                    {{-- ASURANSI LAIN --}}
                    <div class="mt-4">
                        <x-input-label for="formPenjaminanOrientasiKamar.asuransiLain" :value="__('Nama Asuransi Lain (Jika Dipilih)')"
                            class="text-[11px] font-semibold tracking-wide text-gray-500 ml-1" />

                        <x-text-input :disabled="$disabledProperty || ( ($formPenjaminanOrientasiKamar['jenisPenjamin'] ?? '') !== 'ASURANSI_LAIN')" id="formPenjaminanOrientasiKamar.asuransiLain"
                            placeholder="Isi nama perusahaan asuransi" class="mt-1 ml-1" :errorshas="__($errors->has('formPenjaminanOrientasiKamar.asuransiLain'))"
                            wire:model.lazy="formPenjaminanOrientasiKamar.asuransiLain" />
                    </div>

                </div>
            </div>

            {{-- ORIENTASI KAMAR PASIEN --}}
            <div class="w-full p-3 m-2 mx-auto bg-white rounded-lg shadow-md">
                <x-input-label for="formPenjaminanOrientasiKamar.orientasiKamar" :value="__('Orientasi Kamar Pasien')"
                    class="pb-1 mt-2 mb-3 font-semibold text-gray-800 border-b sm:text-lg" />

                <x-input-label for="formPenjaminanOrientasiKamar.kelasKamar" :value="__('Pilihan Kelas Kamar & Fasilitas')"
                    class="mb-2 ml-2 text-[11px] font-semibold tracking-wide text-gray-500" />

                <div class="grid grid-cols-1 gap-3 ml-2 md:grid-cols-2">
                    @foreach ($kelasKamarOptions as $kode => $opt)
                        <div class="p-3 border rounded-md bg-gray-50">
                            <div class="flex items-start gap-2">
                                <x-radio-button :label="__($opt['nama'] . ' - ' . $opt['tarifLabel'])" value="{{ $kode }}"
                                    wire:model="formPenjaminanOrientasiKamar.kelasKamar" />
                            </div>
                            <div class="mt-2 text-[11px] text-gray-600 ml-7 leading-snug">
                                <span class="font-semibold">Fasilitas:</span><br>
                                {{ implode(', ', $opt['fasilitas']) }}
                            </div>
                        </div>
                    @endforeach
                </div>

                @error('formPenjaminanOrientasiKamar.kelasKamar')
                    <x-input-error :messages="$message" />
                @enderror

                {{-- CHECKBOX KONFIRMASI ORIENTASI --}}
                <div class="mt-4 ml-2">
                    <label class="inline-flex items-start gap-2">
                        <input type="checkbox" class="mt-1"
                            wire:model="formPenjaminanOrientasiKamar.orientasiKamarDijelaskan"
                            @if ($disabledProperty) disabled @endif>
                        <span class="text-sm leading-snug text-gray-800 md:text-base">
                            Saya / keluarga telah menerima penjelasan mengenai tarif dan fasilitas kamar yang dipilih di
                            atas.
                        </span>
                    </label>
                    @error('formPenjaminanOrientasiKamar.orientasiKamarDijelaskan')
                        <x-input-error :messages="$message" />
                    @enderror
                </div>
            </div>

        </div>

        {{-- TIGA KOLOM: PEMBUAT, SAKSI, PETUGAS --}}
        <div class="grid content-center grid-cols-1 gap-2 p-2 m-2 md:grid-cols-3">

            {{-- KOLOM 1: PEMBUAT PERNYATAAN --}}
            @if (empty($formPenjaminanOrientasiKamar['signaturePembuat'] ?? null) ||
                    empty($formPenjaminanOrientasiKamar['pembuatNama'] ?? null))
                <div class="flex items-end justify-center w-full p-2 m-2 mx-auto bg-white rounded-lg shadow-md">
                    <div class="relative flex flex-col gap-4 p-6 bg-white rounded-lg shadow-xl">

                        <x-input-label for="formPenjaminanOrientasiKamar.signaturePembuat" :value="__('Pembuat Pernyataan')"
                            class="mb-2 text-sm font-semibold text-center" />

                        <x-signature-pad wire:model.defer="signature"></x-signature-pad>
                        @error('formPenjaminanOrientasiKamar.signaturePembuat')
                            <x-input-error :messages="$message" />
                        @enderror

                        <div>
                            <x-input-label for="formPenjaminanOrientasiKamar.pembuatNama" :value="__('Nama Pembuat Pernyataan')"
                                class="text-[11px] font-semibold tracking-wide text-gray-500 ml-1" />
                            <x-text-input :disabled="$disabledProperty" id="formPenjaminanOrientasiKamar.pembuatNama"
                                placeholder="Nama Pembuat Pernyataan" class="mt-1 ml-2" :errorshas="__($errors->has('formPenjaminanOrientasiKamar.pembuatNama'))"
                                wire:model.lazy="formPenjaminanOrientasiKamar.pembuatNama" />
                        </div>

                    </div>
                </div>
            @else
                <div class="flex items-end justify-center w-full p-2 m-2 mx-auto bg-white rounded-lg shadow-md">
                    <div class="w-56 h-auto">
                        <div class="text-sm text-right">
                            {{ env('SATUSEHAT_ORGANIZATION_NAMEX', 'RUMAH SAKIT ISLAM MADINAH') }}
                        </div>
                        <div class="text-sm text-right">
                            {{ ' Ngunut , ' . ($formPenjaminanOrientasiKamar['signaturePembuatDate'] ?? '') }}
                        </div>

                        <div class="flex items-center justify-center">
                            <object type="image/svg+xml"
                                data="data:image/svg+xml;utf8,{{ $formPenjaminanOrientasiKamar['signaturePembuat'] ?? $signature }}">
                                <img src="fallback.png" alt="Fallback image for browsers that don't support SVG">
                            </object>
                        </div>

                        <div class="mb-4 text-sm text-center">
                            {{ $formPenjaminanOrientasiKamar['pembuatNama'] ?? '' }} <br>
                            Pembuat Pernyataan
                        </div>
                    </div>
                </div>
            @endif

            {{-- KOLOM 2: SAKSI KELUARGA --}}
            @if (empty($formPenjaminanOrientasiKamar['signatureSaksiKeluarga'] ?? null) ||
                    empty($formPenjaminanOrientasiKamar['namaSaksiKeluarga'] ?? null))
                <div class="flex items-end justify-center w-full p-2 m-2 mx-auto bg-white rounded-lg shadow-md">
                    <div class="relative flex flex-col gap-4 p-6 bg-white rounded-lg shadow-xl">

                        <x-input-label for="formPenjaminanOrientasiKamar.signatureSaksiKeluarga" :value="__('Saksi Keluarga')"
                            class="mb-2 text-sm font-semibold text-center" />

                        <x-signature-pad wire:model.defer="signatureSaksi"></x-signature-pad>
                        @error('formPenjaminanOrientasiKamar.signatureSaksiKeluarga')
                            <x-input-error :messages="$message" />
                        @enderror

                        <div>
                            <x-input-label for="formPenjaminanOrientasiKamar.namaSaksiKeluarga" :value="__('Nama Saksi Keluarga')"
                                class="text-[11px] font-semibold tracking-wide text-gray-500 ml-1" />
                            <x-text-input :disabled="$disabledProperty" id="formPenjaminanOrientasiKamar.namaSaksiKeluarga"
                                placeholder="Nama Saksi Keluarga" class="mt-1 ml-2" :errorshas="__($errors->has('formPenjaminanOrientasiKamar.namaSaksiKeluarga'))"
                                wire:model.lazy="formPenjaminanOrientasiKamar.namaSaksiKeluarga" />
                        </div>

                    </div>
                </div>
            @else
                <div class="flex items-end justify-center w-full p-2 m-2 mx-auto bg-white rounded-lg shadow-md">
                    <div class="w-56 h-auto">
                        <div class="text-sm text-right">
                            {{ env('SATUSEHAT_ORGANIZATION_NAMEX', 'RUMAH SAKIT ISLAM MADINAH') }}
                        </div>
                        <div class="text-sm text-right">
                            {{ ' Ngunut , ' . ($formPenjaminanOrientasiKamar['signatureSaksiKeluargaDate'] ?? '') }}
                        </div>

                        <div class="flex items-center justify-center">
                            <object type="image/svg+xml"
                                data="data:image/svg+xml;utf8,{{ $formPenjaminanOrientasiKamar['signatureSaksiKeluarga'] ?? $signatureSaksi }}">
                                <img src="fallback.png" alt="Fallback image for browsers that don't support SVG">
                            </object>
                        </div>

                        <div class="mb-4 text-sm text-center">
                            {{ $formPenjaminanOrientasiKamar['namaSaksiKeluarga'] ?? '' }} <br>
                            Saksi Keluarga
                        </div>
                    </div>
                </div>
            @endif

            {{-- KOLOM 3: PETUGAS RS --}}
            @if (empty($formPenjaminanOrientasiKamar['namaPetugas'] ?? null) ||
                    empty($formPenjaminanOrientasiKamar['kodePetugas'] ?? null))
                <div class="flex items-end justify-center w-full p-2 m-2 mx-auto bg-white rounded-lg shadow-md">
                    <div class="w-full mb-5">
                        <x-input-label for="formPenjaminanOrientasiKamar.namaPetugas" :value="__('Petugas Rumah Sakit')"
                            :required="__(true)" />

                        <div class="grid grid-cols-1 gap-2">
                            <x-text-input :disabled="true" id="formPenjaminanOrientasiKamar.namaPetugas"
                                placeholder="Petugas Rumah Sakit" class="mt-1 mb-2" :errorshas="__($errors->has('formPenjaminanOrientasiKamar.namaPetugas'))"
                                wire:model.debounce.500ms="formPenjaminanOrientasiKamar.namaPetugas" />

                            <x-yellow-button :disabled="false" wire:click.prevent="setPetugasPemeriksa()"
                                type="button" wire:loading.remove>
                                ttd Petugas Rumah Sakit
                            </x-yellow-button>
                        </div>
                    </div>
                </div>
            @else
                <div class="flex items-end justify-center w-full p-2 m-2 mx-auto bg-white rounded-lg shadow-md">
                    <div class="w-56 h-auto">
                        <div class="text-sm text-center">
                            Petugas Rumah Sakit
                        </div>

                        @php
                            $user = null;
                            if (!empty($formPenjaminanOrientasiKamar['kodePetugas'] ?? null)) {
                                $user = App\Models\User::where(
                                    'myuser_code',
                                    $formPenjaminanOrientasiKamar['kodePetugas'],
                                )->first();
                            }
                        @endphp

                        @isset($user->myuser_ttd_image)
                            <div class="flex items-center justify-center">
                                <img class="h-24" src="{{ asset('storage/' . $user->myuser_ttd_image) }}"
                                    alt="Tanda tangan petugas">
                            </div>
                        @endisset

                        <div class="mb-4 text-sm text-center">
                            {{ $formPenjaminanOrientasiKamar['namaPetugas'] ?? '' }} <br>
                            {{ $formPenjaminanOrientasiKamar['petugasDate'] ?? '' }}
                        </div>
                    </div>
                </div>
            @endif

            <div class="flex items-center">
                <x-primary-button wire:click="submit" class="text-white">
                    Simpan
                </x-primary-button>
            </div>
        </div>

        <div class="mt-4">
            @include('livewire.emr-u-g-d.form-penjaminan-orientasi-kamar.table-form-penjaminan-orientasi-kamar')
        </div>
    </div>
</div>
