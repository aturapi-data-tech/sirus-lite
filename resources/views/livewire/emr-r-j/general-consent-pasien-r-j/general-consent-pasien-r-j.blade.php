<div>

    <div class="w-full mb-1 ">

        <div class="w-full p-4 text-sm ">
            <h2 class="text-2xl font-bold text-center">Formulir Persetujuan Umum</h2>
            </br>

            <div class="w-full p-2 m-2 mx-auto bg-white rounded-lg shadow-md">
                <h2 class="mt-6 text-lg font-semibold">Pernyataan Persetujuan</h2>

                <p class="mb-4 text-sm text-gray-700 tracking-tracking-wide">Dengan ini, saya memberikan persetujuan
                    untuk menerima perawatan kesehatan
                    di
                    Rawat Jalan (RJ) sesuai dengan kondisi saya.
                    </br>
                    Saya telah menerima penjelasan yang jelas mengenai
                    hak dan kewajiban saya sebagai pasien.</p>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div class="w-full p-2 m-2 mx-auto bg-white rounded-lg shadow-md">
                    <h3 class="mt-4 text-lg font-semibold">HAK SEBAGAI PASIEN</h3>

                    <ol class="mb-4 text-gray-700 list-decimal list-inside">

                        <li class="mb-2 text-sm tracking-tracking-wide">Mendapatkan informasi yang jelas tentang
                            peraturan rumah sakit
                            dan hak serta
                            kewajiban saya
                            sebagai pasien.</li>

                        <li class="mb-2 text-sm tracking-tracking-wide">Mendapatkan layanan kesehatan yang baik, tanpa
                            diskriminasi
                            dan sesuai dengan
                            standar
                            profesional.</li>

                        <li class="mb-2 text-sm tracking-tracking-wide">Memilih dokter dan jenis perawatan yang saya
                            inginkan, sesuai
                            ketentuan rumah
                            sakit.</li>

                        <li class="mb-2 text-sm tracking-tracking-wide">Mendapatkan informasi tentang diagnosis,
                            prosedur medis,
                            tujuan, risiko, dan
                            alternatif
                            tindakan medis.</li>

                        <li class="mb-2 text-sm tracking-tracking-wide">Memberikan persetujuan atau menolak tindakan
                            medis yang akan
                            dilakukan oleh
                            tenaga
                            kesehatan.</li>

                        <li class="mb-2 text-sm tracking-tracking-wide">Mendapatkan privasi dan kerahasiaan terkait
                            penyakit dan data
                            medis saya.
                        </li>

                        <li class="mb-2 text-sm tracking-tracking-wide">Mengajukan keluhan atau saran mengenai pelayanan
                            rumah sakit
                            yang saya
                            terima.</li>

                        <li class="mb-2 text-sm tracking-tracking-wide">Meminta konsultasi dengan dokter lain yang
                            berizin jika
                            diperlukan.</li>

                    </ol>
                </div>

                <div class="w-full p-2 m-2 mx-auto bg-white rounded-lg shadow-md">
                    <h3 class="mt-4 text-lg font-semibold">KEAWAJIBAN SEBAGAI PASIEN</h3>

                    <ol class="mb-4 text-gray-700 list-decimal list-inside">

                        <li class="mb-2 text-sm tracking-tracking-wide">Mematuhi peraturan rumah sakit dan menggunakan
                            fasilitas
                            dengan bertanggung
                            jawab.</li>

                        <li class="mb-2 text-sm tracking-tracking-wide">Memberikan informasi yang akurat dan lengkap
                            tentang kondisi
                            kesehatan saya.
                        </li>

                        <li class="mb-2 text-sm tracking-tracking-wide">Mematuhi rencana terapi yang disarankan oleh
                            tenaga medis
                            setelah mendapatkan
                            penjelasan.
                        </li>

                        <li class="mb-2 text-sm tracking-tracking-wide">Menanggung biaya pengobatan yang saya terima
                            sesuai ketentuan
                            yang berlaku.
                        </li>

                        <li class="mb-2 text-sm tracking-tracking-wide">Menghormati hak pasien lain dan petugas medis
                            yang memberikan
                            pelayanan.</li>

                    </ol>
                </div>

            </div>
            <x-theme-line></x-theme-line>

            <div class="w-full p-2 m-2 mx-auto bg-white rounded-lg shadow-md">

                <h3 class="mt-4 text-lg font-semibold">PERSETUJUAN PELAYANAN KESEHATAN</h3>

                <p class="mb-4 text-gray-700">Saya menyetujui untuk menerima pemeriksaan, pengobatan, dan tindakan medis
                    yang
                    dianggap perlu oleh tim medis di rumah sakit ini.
                    </br>
                    Saya mengerti bahwa saya dapat menanyakan
                    lebih lanjut tentang prosedur atau menolak tindakan yang diajukan, jika saya merasa perlu.
                </p>


                <h3 class="mt-4 text-lg font-semibold">PELEPASAN INFORMASI</h3>

                <p class="mb-4 text-gray-700">Saya memberikan izin kepada rumah sakit untuk mengungkapkan informasi
                    medis
                    saya kepada pihak yang berwenang
                    </br>
                    (seperti keluarga terdekat atau dokter rujukan) hanya untuk
                    kepentingan perawatan saya.</p>


                <h3 class="mt-4 text-lg font-semibold">KERAHSIAAN DATA</h3>

                <p class="mb-4 text-gray-700">Saya memahami bahwa rumah sakit akan menjaga kerahasiaan informasi medis
                    saya
                    sesuai dengan ketentuan yang berlaku.</p>


                <h3 class="mt-4 text-lg font-semibold">TANGGUNG JAWAB ATAS BIAYA</h3>

                <p class="mb-4 text-gray-700">Saya memahami bahwa saya bertanggung jawab atas biaya yang timbul dari
                    pelayanan
                    medis yang saya terima.</p>

            </div>

        </div>

        <div class="grid content-center grid-cols-2 gap-2 p-2 m-2">
            @if (
                !$this->dataDaftarPoliRJ['generalConsentPasienRJ']['signature'] ||
                    !$this->dataDaftarPoliRJ['generalConsentPasienRJ']['wali']
            )
                <div class="flex items-end justify-center w-full p-2 m-2 mx-auto bg-white rounded-lg shadow-md">
                    <div>
                        <div class="flex ml-2">
                            @foreach ($agreementOptions as $agreement)
                                <x-radio-button :label="__($agreement['agreementDesc'])" value="{{ $agreement['agreementId'] }}"
                                    wire:model="dataDaftarPoliRJ.generalConsentPasienRJ.agreement" />
                            @endforeach
                        </div>

                        <div class="relative flex flex-col gap-4 p-6 bg-white rounded-lg shadow-xl">
                            <x-signature-pad wire:model.defer="signature">

                            </x-signature-pad>
                            @error('dataDaftarPoliRJ.generalConsentPasienRJ.signature')
                                <x-input-error :messages=$message />
                            @enderror

                            <div>
                                <x-text-input id="dataDaftarPoliRJ.generalConsentPasienRJ.wali" placeholder="Nama Wali"
                                    class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarPoliRJ.generalConsentPasienRJ.wali'))"
                                    wire:model.lazy="dataDaftarPoliRJ.generalConsentPasienRJ.wali" />
                            </div>

                            <x-primary-button wire:click="submit" class="text-white">
                                Submit
                            </x-primary-button>
                        </div>
                    </div>
                </div>
            @else
                <div class="flex items-end justify-center w-full p-2 m-2 mx-auto bg-white rounded-lg shadow-md ">

                    <div class="w-56 h-auto">
                        <div class="text-sm text-right ">
                            {{ env('SATUSEHAT_ORGANIZATION_NAMEX', 'RUMAH SAKIT ISLAM MADINAH') }}
                        </div>
                        <div class="text-sm text-right ">
                            {{ ' Ngunut , ' . $this->dataDaftarPoliRJ['generalConsentPasienRJ']['signatureDate'] }}
                        </div>
                        <div class="flex items-center justify-center">
                            <object type="image/svg+xml"
                                data="data:image/svg+xml;utf8,{{ $this->dataDaftarPoliRJ['generalConsentPasienRJ']['signature'] ?? $signature }}">
                                <img src="fallback.png" alt="Fallback image for browsers that don't support SVG">
                            </object>
                        </div>

                        <div class="mb-4 text-sm text-center">
                            {{ $this->dataDaftarPoliRJ['generalConsentPasienRJ']['wali'] }}

                        </div>
                    </div>
                </div>
            @endisset


            @if (
                !$dataDaftarPoliRJ['generalConsentPasienRJ']['petugasPemeriksa'] ||
                    !$dataDaftarPoliRJ['generalConsentPasienRJ']['petugasPemeriksaCode']
            )
                <div class="flex items-end justify-center w-full p-2 m-2 mx-auto bg-white rounded-lg shadow-md">
                    <div class="w-full mb-5">
                        <x-input-label for="dataDaftarPoliRJ.generalConsentPasienRJ.petugasPemeriksa"
                            :value="__('Petugas Pemeriksa')" :required="__(true)" />
                        <div class="grid grid-cols-1 gap-2 ">
                            <x-text-input id="dataDaftarPoliRJ.generalConsentPasienRJ.petugasPemeriksa"
                                placeholder="Petugas Pemeriksa" class="mt-1 mb-2" :errorshas="__($errors->has('dataDaftarPoliRJ.generalConsentPasienRJ.petugasPemeriksa'))" :disabled=true
                                wire:model.debounce.500ms="dataDaftarPoliRJ.generalConsentPasienRJ.petugasPemeriksa" />

                            <x-yellow-button :disabled=false wire:click.prevent="setPetugasPemeriksa()"
                                type="button" wire:loading.remove>
                                ttd Petugas Pemeriksa
                            </x-yellow-button>

                        </div>
                    </div>
                </div>
            @else
                <div class="flex items-end justify-center w-full p-2 m-2 mx-auto bg-white rounded-lg shadow-md">

                    <div class="w-56 h-auto">
                        <div class="text-sm text-center">
                            Petugas
                        </div>
                        @isset($dataDaftarPoliRJ['generalConsentPasienRJ']['petugasPemeriksa'])
                            @if ($dataDaftarPoliRJ['generalConsentPasienRJ']['petugasPemeriksa'])
                                @isset($dataDaftarPoliRJ['generalConsentPasienRJ']['petugasPemeriksaCode'])
                                    @if ($dataDaftarPoliRJ['generalConsentPasienRJ']['petugasPemeriksaCode'])
                                        @isset(App\Models\User::where('myuser_code', $dataDaftarPoliRJ['generalConsentPasienRJ']['petugasPemeriksaCode'])->first()->myuser_ttd_image)
                                            <div class="flex items-center justify-center">
                                                <img class="h-24"
                                                    src="{{ asset('storage/' . App\Models\User::where('myuser_code', $dataDaftarPoliRJ['generalConsentPasienRJ']['petugasPemeriksaCode'])->first()->myuser_ttd_image) }}"
                                                    alt="">
                                            </div>
                                        @endisset
                                    @endif
                                @endisset
                            @endif
                        @endisset

                        <div class="mb-4 text-sm text-center">
                            {{ $this->dataDaftarPoliRJ['generalConsentPasienRJ']['petugasPemeriksa'] }}
                            </br>
                            {{ $this->dataDaftarPoliRJ['generalConsentPasienRJ']['petugasPemeriksaDate'] }}

                        </div>
                    </div>
                </div>
            @endif

    </div>


</div>
