<div>

    <div class="w-full mb-1 ">

        <div class="w-full p-4 text-sm ">
            <h2 class="text-2xl font-bold text-center">Formulir Persetujuan Umum</h2>
            </br>
            <p class="font-bold">Saya yang bertanda tangan di bawah ini menyatakan dengan sesungguhnya bahwa:
            </p>

            <div class="persetujuan" class="bg-red-100 ">
                <p>
                    <span class="font-bold">
                        Pemahaman:
                    </span>
                    </br>
                    Saya telah menerima penjelasan yang jelas dan lengkap dari petugas medis
                    mengenai hak
                    dan kewajiban saya sebagai pasien di
                    {{ env('SATUSEHAT_ORGANIZATION_NAMEX', 'RUMAH SAKIT ISLAM MADINAH') }}. Saya
                    memahami bahwa setiap
                    tindakan medis memiliki risiko dan manfaat.
                </p>
                </br>
                <p>
                    <span class="font-bold">
                        Persetujuan:
                    </span>
                    </br>
                    Saya menyetujui untuk menjalani semua pemeriksaan, pengobatan, dan tindakan
                    medis
                    yang dianggap perlu oleh tim medis untuk kepentingan kesehatan saya, sesuai dengan standar
                    prosedur operasional yang berlaku di rumah sakit ini.
                </p>
                </br>
                <p>
                    <span class="font-bold">
                        Pelepasan Informasi:
                    </span>
                    </br>
                    Saya menyetujui untuk memberikan izin kepada rumah sakit untuk
                    mengungkapkan
                    informasi medis saya kepada pihak-pihak yang berwenang, seperti keluarga terdekat, dokter
                    rujukan, dan pihak asuransi, sejauh diperlukan untuk kepentingan perawatan saya.
                </p>
                </br>
                <p>
                    <span class="font-bold">
                        Barang Benda:
                    </span>
                    </br>
                    Saya memahami bahwa rumah sakit tidak bertanggung jawab atas kehilangan
                    atau
                    kerusakan barang-barang berharga yang saya bawa.
                </p>
                </br>
                <p>
                    <span class="font-bold">
                        Biaya:
                    </span>
                    </br>
                    Saya memahami bahwa saya bertanggung jawab atas semua biaya yang timbul akibat
                    perawatan
                    medis yang saya terima, sesuai dengan ketentuan yang berlaku di rumah sakit ini.
                </p>
                </br>
                <p>
                    <span class="font-bold">
                        Kerahasiaan:
                    </span>
                    </br>
                    Saya memahami bahwa pihak rumah sakit akan menjaga kerahasiaan data medis
                    saya
                    sesuai dengan ketentuan yang berlaku.
                </p>
                </br>
            </div>
        </div>

        <div>
            @if (!$this->dataDaftarUgd['generalConsentPasienUGD']['signature'])
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
                                wire:model="dataDaftarUgd.generalConsentPasienUGD.wali" />
                        </div>

                        <x-primary-button wire:click="submit" class="text-white">
                            Submit
                        </x-primary-button>
                    </div>


                </div>
            @else
                <div class="w-full bg-white ">

                    <div class="w-64 h-40 ">
                        <div class="text-sm text-right ">
                            {{ env('SATUSEHAT_ORGANIZATION_NAMEX', 'RUMAH SAKIT ISLAM MADINAH') }}
                        </div>
                        <div class="text-sm text-right ">
                            {{ ' Ngunut , ' . $this->dataDaftarUgd['generalConsentPasienUGD']['signatureDate'] }}
                        </div>
                        <object type="image/svg+xml"
                            data="data:image/svg+xml;utf8,{{ $this->dataDaftarUgd['generalConsentPasienUGD']['signature'] ?? $signature }}">
                            <img src="fallback.png" alt="Fallback image for browsers that don't support SVG">
                        </object>

                        <div class="mb-4 text-sm text-center">
                            {{ $this->dataDaftarUgd['generalConsentPasienUGD']['wali'] }}

                        </div>
                    </div>
                </div>
            @endisset

    </div>


</div>


</div>
