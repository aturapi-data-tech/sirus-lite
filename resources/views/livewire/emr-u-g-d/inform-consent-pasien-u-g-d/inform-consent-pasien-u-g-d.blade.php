<div>
    @php
        $disabledProperty = $informConsentPasienUGD['signature'] ? true : false;
    @endphp
    <div class="w-full mb-1 ">

        <div class="w-full p-4 text-sm ">
            @if ($informConsentPasienUGD['agreement'] === '1')
                <h2 class="text-2xl font-bold text-center">Persetujuan Tindakan Medis</h2>
                </br>
            @else
                <h2 class="text-2xl font-bold text-center">Penolakan Tindakan Medis</h2>
                </br>
            @endif

            <div class="w-full p-2 m-2 mx-auto bg-white rounded-lg shadow-md">
                <div>
                    <x-text-input :disabled=$disabledProperty id="informConsentPasienUGD.tindakan"
                        placeholder="Tindakan yang Akan Dilakuka" class="mt-1 ml-2" :errorshas="__($errors->has('informConsentPasienUGD.tindakan'))"
                        wire:model.lazy="informConsentPasienUGD.tindakan" />
                </div>
                <div>
                    <x-text-input :disabled=$disabledProperty id="informConsentPasienUGD.tujuan"
                        placeholder="Tujuan Tindakan" class="mt-1 ml-2" :errorshas="__($errors->has('informConsentPasienUGD.tujuan'))"
                        wire:model.lazy="informConsentPasienUGD.tujuan" />
                </div>
                <div>
                    <x-text-input :disabled=$disabledProperty id="informConsentPasienUGD.resiko"
                        placeholder="Risiko dan Komplikasi yang Mungkin Terjadi" class="mt-1 ml-2" :errorshas="__($errors->has('informConsentPasienUGD.resiko'))"
                        wire:model.lazy="informConsentPasienUGD.resiko" />
                </div>
                <div>
                    <x-text-input :disabled=$disabledProperty id="informConsentPasienUGD.alternatif"
                        placeholder="Alternatif Tindakan (jika ada)" class="mt-1 ml-2" :errorshas="__($errors->has('informConsentPasienUGD.alternatif'))"
                        wire:model.lazy="informConsentPasienUGD.alternatif" />
                </div>
                <div>
                    <x-text-input :disabled=$disabledProperty id="informConsentPasienUGD.dokter"
                        placeholder="Dokter yang melakukan tindakan" class="mt-1 ml-2" :errorshas="__($errors->has('informConsentPasienUGD.dokter'))"
                        wire:model.lazy="informConsentPasienUGD.dokter" />
                </div>
            </div>

            @if ($informConsentPasienUGD['agreement'] === '1')
                <div class="grid grid-cols-1 gap-2">
                    <div class="w-full p-2 m-2 mx-auto bg-white rounded-lg shadow-md">
                        <h3 class="mt-4 text-lg font-semibold">HAK DAN KEWAJIBAN PASIEN</h3>

                        <ol class="mb-4 text-gray-700 list-decimal list-inside">

                            <li class="mb-2 text-sm tracking-tracking-wide">Hak dan kewajiban saya sebagai pasien
                                sebagaimana
                                telah dijelaskan dalam Formulir Persetujuan Umum tetap berlaku dan menjadi bagian dari
                                dokumen ini.</li>

                        </ol>
                    </div>
                </div>
                <x-theme-line></x-theme-line>
                <div class="w-full p-2 m-2 mx-auto bg-white rounded-lg shadow-md">

                    <h3 class="mt-4 text-lg font-semibold">PERNYATAAN PERSETUJUAN</h3>

                    <p class="mb-4 text-gray-700">Saya yang bertanda tangan di bawah ini menyatakan bahwa:
                    </p>

                    <ol class="mb-4 text-gray-700 list-decimal list-inside">

                        <li class="mb-2 text-sm tracking-tracking-wide">Saya telah menerima penjelasan yang jelas dan
                            lengkap dari petugas
                            medis mengenai tindakan medis yang akan dilakukan, termasuk tujuan, risiko, manfaat, dan
                            alternatifnya.</li>
                        <li class="mb-2 text-sm tracking-tracking-wide">Saya memahami bahwa tindakan medis memiliki
                            risiko dan komplikasi yang dapat terjadi, namun
                            semua upaya terbaik akan dilakukan untuk keselamatan saya.</li>
                        <li class="mb-2 text-sm tracking-tracking-wide">Saya menyetujui untuk menjalani tindakan medis
                            yang direncanakan
                            sesuai dengan standar prosedur operasional yang berlaku di rumah sakit ini.</li>
                        <li class="mb-2 text-sm tracking-tracking-wide">Saya memberikan izin kepada rumah sakit untuk
                            mengungkapkan informasi
                            medis saya kepada pihak yang relevan, seperti keluarga terdekat, dokter rujukan, atau pihak
                            asuransi, jika diperlukan untuk kepentingan perawatan saya.</li>

                    </ol>
                </div>
            @else
                <div class="w-full p-2 m-2 mx-auto bg-white rounded-lg shadow-md">

                    <h3 class="mt-4 text-lg font-semibold">PERNYATAAN PENOLAKAN TINDAKAN MEDIS</h3>

                    <p class="mb-4 text-gray-700">Saya yang bertanda tangan di bawah ini menyatakan bahwa:
                    </p>

                    <ol class="mb-4 text-gray-700 list-decimal list-inside">

                        <li class="mb-2 text-sm tracking-tracking-wide">Saya telah menerima penjelasan yang jelas dan
                            lengkap dari petugas medis mengenai tindakan medis yang direncanakan, termasuk tujuan,
                            risiko, manfaat, dan alternatifnya.</li>
                        <li class="mb-2 text-sm tracking-tracking-wide">Saya memahami bahwa dengan menolak tindakan
                            medis ini, saya dapat menghadapi konsekuensi atau risiko terhadap kesehatan saya, termasuk
                            komplikasi atau kondisi yang memburuk.</li>
                        <li class="mb-2 text-sm tracking-tracking-wide">Saya secara sadar dan tanpa paksaan memutuskan
                            untuk menolak tindakan medis yang direncanakan, dan saya bertanggung jawab penuh atas segala
                            konsekuensi yang mungkin timbul dari keputusan ini.</li>
                    </ol>
                </div>
            @endif

        </div>

        <div class="grid content-center grid-cols-3 gap-2 p-2 m-2">
            @if (!$informConsentPasienUGD['signature'] || !$informConsentPasienUGD['wali'])
                <div class="flex items-end justify-center w-full p-2 m-2 mx-auto bg-white rounded-lg shadow-md">
                    <div>
                        <div class="flex ml-2">
                            @foreach ($agreementOptions as $agreement)
                                <x-radio-button :label="__($agreement['agreementDesc'])" value="{{ $agreement['agreementId'] }}"
                                    wire:model="informConsentPasienUGD.agreement" />
                            @endforeach
                        </div>

                        <div class="relative flex flex-col gap-4 p-6 bg-white rounded-lg shadow-xl">
                            <x-signature-pad wire:model.defer="signature">

                            </x-signature-pad>
                            @error('informConsentPasienUGD.signature')
                                <x-input-error :messages=$message />
                            @enderror

                            <div>
                                <x-text-input :disabled=$disabledProperty id="informConsentPasienUGD.wali"
                                    placeholder="Nama Pasien / Wali" class="mt-1 ml-2" :errorshas="__($errors->has('informConsentPasienUGD.wali'))"
                                    wire:model.lazy="informConsentPasienUGD.wali" />
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
                            {{ ' Ngunut , ' . $informConsentPasienUGD['signatureDate'] }}
                        </div>
                        <div class="flex items-center justify-center">
                            <object type="image/svg+xml"
                                data="data:image/svg+xml;utf8,{{ $informConsentPasienUGD['signature'] ?? $signature }}">
                                <img src="fallback.png" alt="Fallback image for browsers that don't support SVG">
                            </object>
                        </div>

                        <div class="mb-4 text-sm text-center">
                            {{ $informConsentPasienUGD['wali'] }}
                            </br>
                            Pasien / Wali
                        </div>
                    </div>
                </div>
            @endisset



            @if (!$informConsentPasienUGD['signatureSaksi'] || !$informConsentPasienUGD['saksi'])
                <div class="flex items-end justify-center w-full p-2 m-2 mx-auto bg-white rounded-lg shadow-md">
                    <div>
                        <div class="flex ml-2">
                            @foreach ($agreementOptions as $agreement)
                                <x-radio-button :label="__($agreement['agreementDesc'])" value="{{ $agreement['agreementId'] }}"
                                    wire:model="informConsentPasienUGD.agreement" />
                            @endforeach
                        </div>

                        <div class="relative flex flex-col gap-4 p-6 bg-white rounded-lg shadow-xl">
                            <x-signature-pad wire:model.defer="signatureSaksi">

                            </x-signature-pad>
                            @error('informConsentPasienUGD.signatureSaksi')
                                <x-input-error :messages=$message />
                            @enderror

                            <div>
                                <x-text-input :disabled=$disabledProperty id="informConsentPasienUGD.saksi"
                                    placeholder="Nama Saksi" class="mt-1 ml-2" :errorshas="__($errors->has('informConsentPasienUGD.saksi'))"
                                    wire:model.lazy="informConsentPasienUGD.saksi" />
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
                            {{ ' Ngunut , ' . $informConsentPasienUGD['signatureSaksiDate'] }}
                        </div>
                        <div class="flex items-center justify-center">
                            <object type="image/svg+xml"
                                data="data:image/svg+xml;utf8,{{ $informConsentPasienUGD['signatureSaksi'] ?? $signatureSaksi }}">
                                <img src="fallback.png" alt="Fallback image for browsers that don't support SVG">
                            </object>
                        </div>

                        <div class="mb-4 text-sm text-center">
                            {{ $informConsentPasienUGD['saksi'] }}
                            </br>
                            Saksi
                        </div>
                    </div>
                </div>
            @endisset


            @if (!$informConsentPasienUGD['petugasPemeriksa'] || !$informConsentPasienUGD['petugasPemeriksaCode'])
                <div class="flex items-end justify-center w-full p-2 m-2 mx-auto bg-white rounded-lg shadow-md">
                    <div class="w-full mb-5">
                        <x-input-label for="informConsentPasienUGD.petugasPemeriksa" :value="__('Petugas Pemeriksa')"
                            :required="__(true)" />
                        <div class="grid grid-cols-1 gap-2 ">
                            <x-text-input :disabled=$disabledProperty
                                id="informConsentPasienUGD.petugasPemeriksa" placeholder="Petugas Pemeriksa"
                                class="mt-1 mb-2" :errorshas="__($errors->has('informConsentPasienUGD.petugasPemeriksa'))" :disabled=true
                                wire:model.debounce.500ms="informConsentPasienUGD.petugasPemeriksa" />

                            <x-yellow-button :disabled=false wire:click.prevent="setPetugasPemeriksa()"
                                type="button" wire:loading.remove>
                                ttd Petugas Pemeriksa
                            </x-yellow-button>

                        </div>
                    </div>
                </div>
            @else
                <div
                    class="flex items-end justify-center w-full p-2 m-2 mx-auto bg-white rounded-lg shadow-md">

                    <div class="w-56 h-auto">
                        <div class="text-sm text-center">
                            Petugas
                        </div>
                        @isset($informConsentPasienUGD['petugasPemeriksa'])
                            @if ($informConsentPasienUGD['petugasPemeriksa'])
                                @isset($informConsentPasienUGD['petugasPemeriksaCode'])
                                    @if ($informConsentPasienUGD['petugasPemeriksaCode'])
                                        @isset(App\Models\User::where('myuser_code', $informConsentPasienUGD['petugasPemeriksaCode'])->first()->myuser_ttd_image)
                                            <div class="flex items-center justify-center">
                                                <img class="h-24"
                                                    src="{{ asset('storage/' . App\Models\User::where('myuser_code', $informConsentPasienUGD['petugasPemeriksaCode'])->first()->myuser_ttd_image) }}"
                                                    alt="">
                                            </div>
                                        @endisset
                                    @endif
                                @endisset
                            @endif
                        @endisset

                        <div class="mb-4 text-sm text-center">
                            {{ $informConsentPasienUGD['petugasPemeriksa'] }}
                            </br>
                            {{ $informConsentPasienUGD['petugasPemeriksaDate'] }}

                        </div>
                    </div>
                </div>

            @endif

</div>
<div>
    @include('livewire.emr-u-g-d.inform-consent-pasien-u-g-d.table-inform-consent-pasien-u-g-d')
</div>

</div>
</div>
