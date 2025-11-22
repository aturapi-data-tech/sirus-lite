<div>
    <div class="w-full mb-1">
        <div class="w-full p-4 text-sm">
            <h2 class="text-2xl font-bold text-center">FORMULIR PERSETUJUAN UMUM RAWAT INAP</h2>
            </br>

            <div class="grid grid-cols-2 gap-2">
                <!-- Hak Pasien -->
                <div class="w-full p-2 m-2 mx-auto bg-white rounded-lg shadow-md">
                    <h3 class="mt-4 text-lg font-semibold">HAK ANDA SEBAGAI PASIEN:</h3>
                    <ol class="mb-4 text-gray-700 list-decimal list-inside">
                        <li class="mb-2 text-sm tracking-tracking-wide">Mendapat informasi tentang peraturan rumah sakit,
                            hak, dan kewajiban Anda.</li>
                        <li class="mb-2 text-sm tracking-tracking-wide">Mendapat pelayanan yang manusiawi, adil, jujur,
                            dan
                            tanpa diskriminasi.</li>
                        <li class="mb-2 text-sm tracking-tracking-wide">Mendapat pelayanan kesehatan berkualitas sesuai
                            standar medis.</li>
                        <li class="mb-2 text-sm tracking-tracking-wide">Memilih dokter dan kelas perawatan sesuai
                            keinginan
                            dan peraturan rumah sakit.</li>
                        <li class="mb-2 text-sm tracking-tracking-wide">Mendapat penjelasan tentang diagnosis, tindakan
                            medis, risiko, dan biaya.</li>
                        <li class="mb-2 text-sm tracking-tracking-wide">Memberikan persetujuan atau menolak tindakan
                            medis
                            yang akan dilakukan.</li>
                        <li class="mb-2 text-sm tracking-tracking-wide">Didampingi keluarga dalam keadaan kritis.</li>
                        <li class="mb-2 text-sm tracking-tracking-wide">Menjalankan ibadah sesuai agama/kepercayaan,
                            selama
                            tidak mengganggu pasien lain.</li>
                        <li class="mb-2 text-sm tracking-tracking-wide">Mengajukan keluhan jika pelayanan tidak sesuai
                            standar.</li>
                    </ol>
                </div>

                <!-- Kewajiban Pasien -->
                <div class="w-full p-2 m-2 mx-auto bg-white rounded-lg shadow-md">
                    <h3 class="mt-4 text-lg font-semibold">KEWAJIBAN ANDA SEBAGAI PASIEN:</h3>
                    <ol class="mb-4 text-gray-700 list-decimal list-inside">
                        <li class="mb-2 text-sm tracking-tracking-wide">Mematuhi peraturan rumah sakit.</li>
                        <li class="mb-2 text-sm tracking-tracking-wide">Menggunakan fasilitas rumah sakit dengan
                            bertanggung
                            jawab.</li>
                        <li class="mb-2 text-sm tracking-tracking-wide">Memberikan informasi yang jujur dan lengkap
                            tentang
                            kondisi kesehatan.</li>
                        <li class="mb-2 text-sm tracking-tracking-wide">Mematuhi rencana terapi yang disetujui setelah
                            mendapat penjelasan.</li>
                        <li class="mb-2 text-sm tracking-tracking-wide">Membayar biaya pelayanan sesuai ketentuan rumah
                            sakit.</li>
                    </ol>
                </div>
            </div>

            <!-- Garis Pemisah -->
            <x-theme-line></x-theme-line>

            <!-- Pernyataan dan Persetujuan -->
            <div class="w-full p-2 m-2 mx-auto bg-white rounded-lg shadow-md">
                <h3 class="mt-4 text-lg font-semibold">PERNYATAAN DAN PERSETUJUAN</h3>
                <p class="mb-4 text-gray-700">
                    Saya yang bertanda tangan di bawah ini menyatakan:
                </p>

                <h3 class="mt-4 text-lg font-semibold">PEMAHAMAN:</h3>
                <p class="mb-4 text-gray-700">
                    Saya sudah paham hak dan kewajiban saya sebagai pasien. Saya juga mengerti bahwa setiap tindakan
                    medis memiliki risiko dan manfaat.
                </p>

                <h3 class="mt-4 text-lg font-semibold">PERSETUJUAN:</h3>
                <p class="mb-4 text-gray-700">
                    Saya setuju untuk menjalani pemeriksaan, pengobatan, dan tindakan medis yang diperlukan oleh tim
                    medis.
                </p>

                <h3 class="mt-4 text-lg font-semibold">PELEPASAN INFORMASI:</h3>
                <p class="mb-4 text-gray-700">
                    Saya mengizinkan rumah sakit untuk membagikan informasi medis saya kepada keluarga, dokter rujukan,
                    atau pihak asuransi, jika diperlukan.
                </p>

                <h3 class="mt-4 text-lg font-semibold">BARANG BAWAAN:</h3>
                <p class="mb-4 text-gray-700">
                    Saya mengerti bahwa rumah sakit tidak bertanggung jawab atas kehilangan atau kerusakan barang
                    berharga yang saya bawa.
                </p>

                <h3 class="mt-4 text-lg font-semibold">BIAYA:</h3>
                <p class="mb-4 text-gray-700">
                    Saya bertanggung jawab atas semua biaya perawatan sesuai ketentuan rumah sakit.
                </p>

                <h3 class="mt-4 text-lg font-semibold">KERAHASIAAN:</h3>
                <p class="mb-4 text-gray-700">
                    Saya percaya bahwa rumah sakit akan menjaga kerahasiaan data medis saya.
                </p>
            </div>
        </div>

        <!-- Form Tanda Tangan dan Submit -->
        <div class="grid content-center grid-cols-2 gap-2 p-2 m-2">
            @if (!$dataDaftarRi['generalConsentPasienRI']['signature'] || !$dataDaftarRi['generalConsentPasienRI']['wali'])
                <div class="flex items-end justify-center w-full p-2 m-2 mx-auto bg-white rounded-lg shadow-md">
                    <div>
                        <div class="flex ml-2">
                            @foreach ($agreementOptions as $agreement)
                                <x-radio-button :label="__($agreement['agreementDesc'])" value="{{ $agreement['agreementId'] }}"
                                    wire:model="dataDaftarRi.generalConsentPasienRI.agreement" />
                            @endforeach
                        </div>

                        <div class="relative flex flex-col gap-4 p-6 bg-white rounded-lg shadow-xl">
                            <x-signature-pad wire:model.defer="signature"></x-signature-pad>
                            @error('dataDaftarRi.generalConsentPasienRI.signature')
                                <x-input-error :messages="$message" />
                            @enderror

                            <div>
                                <x-text-input id="dataDaftarRi.generalConsentPasienRI.wali" placeholder="Nama Wali"
                                    class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarRi.generalConsentPasienRI.wali'))"
                                    wire:model.lazy="dataDaftarRi.generalConsentPasienRI.wali" />
                            </div>

                            <x-primary-button wire:click="submit" class="text-white">
                                Submit
                            </x-primary-button>
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
                            {{ ' Ngunut , ' . $this->dataDaftarRi['generalConsentPasienRI']['signatureDate'] }}
                        </div>
                        <div class="flex items-center justify-center">
                            <object type="image/svg+xml"
                                data="data:image/svg+xml;utf8,{{ $this->dataDaftarRi['generalConsentPasienRI']['signature'] ?? $signature }}">
                                <img src="fallback.png" alt="Fallback image for browsers that don't support SVG">
                            </object>
                        </div>
                        <div class="mb-4 text-sm text-center">
                            {{ $this->dataDaftarRi['generalConsentPasienRI']['wali'] }}
                        </div>
                    </div>
                </div>
            @endif

            @if (
                !$dataDaftarRi['generalConsentPasienRI']['petugasPemeriksa'] ||
                    !$dataDaftarRi['generalConsentPasienRI']['petugasPemeriksaCode']
            )
                <div class="flex items-end justify-center w-full p-2 m-2 mx-auto bg-white rounded-lg shadow-md">
                    <div class="w-full mb-5">
                        <x-input-label for="dataDaftarRi.generalConsentPasienRI.petugasPemeriksa" :value="__('Petugas Pemeriksa')"
                            :required="__(true)" />
                        <div class="grid grid-cols-1 gap-2">
                            <x-text-input id="dataDaftarRi.generalConsentPasienRI.petugasPemeriksa"
                                placeholder="Petugas Pemeriksa" class="mt-1 mb-2" :errorshas="__($errors->has('dataDaftarRi.generalConsentPasienRI.petugasPemeriksa'))" :disabled="true"
                                wire:model.debounce.500ms="dataDaftarRi.generalConsentPasienRI.petugasPemeriksa" />

                            <x-yellow-button :disabled="false" wire:click.prevent="setPetugasPemeriksa()" type="button"
                                wire:loading.remove>
                                TTD Petugas Pemeriksa
                            </x-yellow-button>
                        </div>
                    </div>
                </div>
            @else
                <div class="flex items-end justify-center w-full p-2 m-2 mx-auto bg-white rounded-lg shadow-md">
                    <div class="w-56 h-auto">
                        <div class="text-sm text-center">Petugas</div>
                        @isset($dataDaftarRi['generalConsentPasienRI']['petugasPemeriksa'])
                            @if ($dataDaftarRi['generalConsentPasienRI']['petugasPemeriksa'])
                                @isset($dataDaftarRi['generalConsentPasienRI']['petugasPemeriksaCode'])
                                    @if ($dataDaftarRi['generalConsentPasienRI']['petugasPemeriksaCode'])
                                        @isset(App\Models\User::where('myuser_code', $dataDaftarRi['generalConsentPasienRI']['petugasPemeriksaCode'])->first()->myuser_ttd_image)
                                            <div class="flex items-center justify-center">
                                                <img class="h-24"
                                                    src="{{ asset('storage/' . App\Models\User::where('myuser_code', $dataDaftarRi['generalConsentPasienRI']['petugasPemeriksaCode'])->first()->myuser_ttd_image) }}"
                                                    alt="">
                                            </div>
                                        @endisset
                                    @endif
                                @endisset
                            @endif
                        @endisset

                        <div class="mb-4 text-sm text-center">
                            {{ $this->dataDaftarRi['generalConsentPasienRI']['petugasPemeriksa'] }}
                            </br>
                            {{ $this->dataDaftarRi['generalConsentPasienRI']['petugasPemeriksaDate'] }}
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="grid w-full grid-cols-1 px-4 pb-4">
            <x-primary-button wire:click="cetakGeneralConsentPasienRi()" wire:loading.attr="disabled"
                class="relative flex items-center justify-center gap-2 text-white">
                {{-- Saat loading tampil ikon spinner --}}
                <div wire:loading wire:target="cetakGeneralConsentPasienRi">
                    <x-loading />
                </div>

                {{-- Saat tidak loading tampil teks --}}
                <span wire:loading.remove wire:target="cetakGeneralConsentPasienRi">
                    Cetak Persetujuan Umum Rawat Inap
                </span>
            </x-primary-button>
        </div>
    </div>
</div>
