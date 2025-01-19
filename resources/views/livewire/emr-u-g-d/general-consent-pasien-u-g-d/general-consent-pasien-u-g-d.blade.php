<div>

    <div class="w-full mb-1 ">

        <div class="w-full p-4 text-sm ">
            <h2 class="text-2xl font-bold text-center">FORMULIR PERSETUJUAN UMUM UGD</h2>
            </br>

            <div class="w-full p-2 m-2 mx-auto bg-white rounded-lg shadow-md">
                <h2 class="mt-6 text-lg font-semibold">Pernyataan Persetujuan</h2>

                <p class="mb-4 text-sm text-gray-700 tracking-tracking-wide">Dengan ini, saya memberikan persetujuan
                    untuk menerima perawatan kesehatan
                    di
                    Unit Gawat Darurat (UGD) sesuai dengan kondisi saya.
                    </br>
                    Saya telah menerima penjelasan yang jelas mengenai
                    hak dan kewajiban saya sebagai pasien.</p>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div class="w-full p-2 m-2 mx-auto bg-white rounded-lg shadow-md">
                    <h3 class="mt-4 text-lg font-semibold">HAK SEBAGAI PASIEN</h3>

                    <ol class="mb-4 text-gray-700 list-decimal list-inside">

                        <li class="mb-2 text-sm tracking-tracking-wide">Mendapatkan informasi mengenai tata tertib UGD
                            dan hak pasien.</li>

                        <li class="mb-2 text-sm tracking-tracking-wide">Mendapatkan pelayanan kesehatan yang manusiawi,
                            adil, dan sesuai standar profesi medis.</li>

                        <li class="mb-2 text-sm tracking-tracking-wide">Mendapatkan penjelasan tentang kondisi medis,
                            tindakan yang akan dilakukan, dan risikonya, kecuali dalam keadaan darurat yang mengancam
                            nyawa.</li>

                        <li class="mb-2 text-sm tracking-tracking-wide">Mendapatkan privasi dan kerahasiaan informasi
                            medis.</li>

                        <li class="mb-2 text-sm tracking-tracking-wide">Memberikan persetujuan atau menolak tindakan
                            medis setelah mendapatkan penjelasan, kecuali dalam situasi darurat.
                        </li>

                        <li class="mb-2 text-sm tracking-tracking-wide">Didampingi oleh keluarga jika memungkinkan
                            sesuai kondisi medis.</li>

                        <li class="mb-2 text-sm tracking-tracking-wide">Memperoleh keamanan dan keselamatan selama
                            berada di UGD.</li>




                    </ol>
                </div>

                <div class="w-full p-2 m-2 mx-auto bg-white rounded-lg shadow-md">
                    <h3 class="mt-4 text-lg font-semibold">KEAWAJIBAN SEBAGAI PASIEN</h3>

                    <ol class="mb-4 text-gray-700 list-decimal list-inside">

                        <li class="mb-2 text-sm tracking-tracking-wide">Mematuhi peraturan UGD dan menghormati hak
                            pasien lain serta tenaga kesehatan.</li>

                        <li class="mb-2 text-sm tracking-tracking-wide">Memberikan informasi yang akurat dan jujur
                            tentang kondisi kesehatan dan riwayat medis.
                        </li>

                        <li class="mb-2 text-sm tracking-tracking-wide">Memberikan informasi terkait jaminan kesehatan
                            atau kemampuan finansial untuk perawatan.</li>

                        <li class="mb-2 text-sm tracking-tracking-wide">Mematuhi anjuran tenaga medis setelah penjelasan
                            diberikan.</li>


                        <li class="mb-2 text-sm tracking-tracking-wide">Membayar biaya perawatan sesuai dengan ketentuan
                            rumah sakit.</li>


                    </ol>
                </div>

            </div>
            <x-theme-line></x-theme-line>

            <div class="w-full p-2 m-2 mx-auto bg-white rounded-lg shadow-md">

                <h3 class="mt-4 text-lg font-semibold">PEMAHAMAN</h3>

                <p class="mb-4 text-gray-700">Saya telah menerima penjelasan singkat mengenai hak dan kewajiban saya
                    sebagai pasien UGD, serta risiko tindakan medis yang mungkin diperlukan.
                </p>


                <h3 class="mt-4 text-lg font-semibold">PERSETUJUAN</h3>

                <p class="mb-4 text-gray-700">Saya menyetujui pemeriksaan, pengobatan, atau tindakan medis yang dianggap
                    perlu oleh tim medis UGD dalam situasi darurat atau untuk menyelamatkan nyawa saya.
                </p>


                <h3 class="mt-4 text-lg font-semibold">PELEPASAN INFORMASI</h3>

                <p class="mb-4 text-gray-700">Saya memberikan izin kepada rumah sakit untuk berbagi informasi medis saya
                    kepada pihak-pihak terkait, seperti keluarga, dokter rujukan, atau penyedia asuransi, untuk
                    kepentingan penanganan medis.</p>


                <h3 class="mt-4 text-lg font-semibold">BARANG BENDA</h3>

                <p class="mb-4 text-gray-700">Saya memahami bahwa rumah sakit tidak bertanggung jawab atas kehilangan
                    atau kerusakan barang berharga yang saya bawa ke UGD.</p>


                <h3 class="mt-4 text-lg font-semibold">BIAYA</h3>

                <p class="mb-4 text-gray-700">Saya memahami bahwa saya bertanggung jawab atas biaya yang timbul selama
                    perawatan di UGD, sesuai dengan ketentuan yang berlaku.</p>

            </div>

        </div>

        <div class="grid content-center grid-cols-2 gap-2 p-2 m-2">
            @if (
                !$this->dataDaftarUgd['generalConsentPasienUGD']['signature'] ||
                    !$this->dataDaftarUgd['generalConsentPasienUGD']['wali']
            )
                <div class="flex items-end justify-center w-full p-2 m-2 mx-auto bg-white rounded-lg shadow-md">
                    <div>
                        <div class="flex ml-2">
                            @foreach ($agreementOptions as $agreement)
                                <x-radio-button :label="__($agreement['agreementDesc'])" value="{{ $agreement['agreementId'] }}"
                                    wire:model="dataDaftarUgd.generalConsentPasienUGD.agreement" />
                            @endforeach
                        </div>

                        <div class="relative flex flex-col gap-4 p-6 bg-white rounded-lg shadow-xl">
                            <x-signature-pad wire:model.defer="signature">

                            </x-signature-pad>
                            @error('dataDaftarUgd.generalConsentPasienUGD.signature')
                                <x-input-error :messages=$message />
                            @enderror

                            <div>
                                <x-text-input id="dataDaftarUgd.generalConsentPasienUGD.wali" placeholder="Nama Wali"
                                    class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarUgd.generalConsentPasienUGD.wali'))"
                                    wire:model.lazy="dataDaftarUgd.generalConsentPasienUGD.wali" />
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
                            {{ ' Ngunut , ' . $this->dataDaftarUgd['generalConsentPasienUGD']['signatureDate'] }}
                        </div>
                        <div class="flex items-center justify-center">
                            <object type="image/svg+xml"
                                data="data:image/svg+xml;utf8,{{ $this->dataDaftarUgd['generalConsentPasienUGD']['signature'] ?? $signature }}">
                                <img src="fallback.png" alt="Fallback image for browsers that don't support SVG">
                            </object>
                        </div>

                        <div class="mb-4 text-sm text-center">
                            {{ $this->dataDaftarUgd['generalConsentPasienUGD']['wali'] }}

                        </div>
                    </div>
                </div>
            @endisset


            @if (
                !$dataDaftarUgd['generalConsentPasienUGD']['petugasPemeriksa'] ||
                    !$dataDaftarUgd['generalConsentPasienUGD']['petugasPemeriksaCode']
            )
                <div class="flex items-end justify-center w-full p-2 m-2 mx-auto bg-white rounded-lg shadow-md">
                    <div class="w-full mb-5">
                        <x-input-label for="dataDaftarUgd.generalConsentPasienUGD.petugasPemeriksa"
                            :value="__('Petugas Pemeriksa')" :required="__(true)" />
                        <div class="grid grid-cols-1 gap-2 ">
                            <x-text-input id="dataDaftarUgd.generalConsentPasienUGD.petugasPemeriksa"
                                placeholder="Petugas Pemeriksa" class="mt-1 mb-2" :errorshas="__($errors->has('dataDaftarUgd.generalConsentPasienUGD.petugasPemeriksa'))" :disabled=true
                                wire:model.debounce.500ms="dataDaftarUgd.generalConsentPasienUGD.petugasPemeriksa" />

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
                        @isset($dataDaftarUgd['generalConsentPasienUGD']['petugasPemeriksa'])
                            @if ($dataDaftarUgd['generalConsentPasienUGD']['petugasPemeriksa'])
                                @isset($dataDaftarUgd['generalConsentPasienUGD']['petugasPemeriksaCode'])
                                    @if ($dataDaftarUgd['generalConsentPasienUGD']['petugasPemeriksaCode'])
                                        @isset(App\Models\User::where('myuser_code', $dataDaftarUgd['generalConsentPasienUGD']['petugasPemeriksaCode'])->first()->myuser_ttd_image)
                                            <div class="flex items-center justify-center">
                                                <img class="h-24"
                                                    src="{{ asset('storage/' . App\Models\User::where('myuser_code', $dataDaftarUgd['generalConsentPasienUGD']['petugasPemeriksaCode'])->first()->myuser_ttd_image) }}"
                                                    alt="">
                                            </div>
                                        @endisset
                                    @endif
                                @endisset
                            @endif
                        @endisset

                        <div class="mb-4 text-sm text-center">
                            {{ $this->dataDaftarUgd['generalConsentPasienUGD']['petugasPemeriksa'] }}
                            </br>
                            {{ $this->dataDaftarUgd['generalConsentPasienUGD']['petugasPemeriksaDate'] }}

                        </div>
                    </div>
                </div>
            @endif

    </div>


</div>
