<div>
    <!-- Label untuk input Tindak Lanjut -->
    <x-input-label for="dataDaftarRi.perencanaan.tindakLanjut.tindakLanjut" :value="__('Tindak Lanjut')" :required="true" />

    <!-- Grid untuk menampilkan opsi radio button -->
    <div class="grid grid-cols-4 gap-2 mt-1">
        <!-- Loop melalui opsi tindak lanjut yang tersedia -->
        @foreach ($tindakLanjutOptions as $option)
            <x-radio-button :label="__($option['tindakLanjut'])" :value="$option['tindakLanjutKode']"
                wire:model="dataDaftarRi.perencanaan.tindakLanjut.tindakLanjut" />
        @endforeach
    </div>


    <!-- Input teks untuk opsi "Lain-lain" -->
    <div class="mt-2">
        <x-text-input id="dataDaftarRi.perencanaan.tindakLanjut.keteranganTindakLanjut"
            placeholder="Silakan jelaskan tindak lanjut" class="w-full mt-1"
            wire:model="dataDaftarRi.perencanaan.tindakLanjut.keteranganTindakLanjut" :disabled="$disabledPropertyRjStatus"
            :errorshas="$errors->has('dataDaftarRi.perencanaan.tindakLanjut.keteranganTindakLanjut')" />
        <!-- Menampilkan pesan error jika ada -->
        @error('dataDaftarRi.perencanaan.tindakLanjut.keteranganTindakLanjut')
            <x-input-error :messages="$message" />
        @enderror
    </div>

    <!-- Menampilkan pesan error untuk input Tindak Lanjut -->
    @error('dataDaftarRi.perencanaan.tindakLanjut.tindakLanjut')
        <x-input-error :messages="$message" />
    @enderror




    {{-- noSep --}}
    <div>
        <x-input-label for="dataDaftarRi.perencanaan.tindakLanjut.noSep" :value="__('No. SEP')" />
        <x-text-input id="dataDaftarRi.perencanaan.tindakLanjut.noSep" placeholder="Contoh: xxxxxxxxxxxxxx"
            class="w-full mt-1" wire:model="dataDaftarRi.perencanaan.tindakLanjut.noSep" :errorshas="$errors->has('dataDaftarRi.perencanaan.tindakLanjut.noSep')" />
        @error('dataDaftarRi.perencanaan.tindakLanjut.noSep')
            <x-input-error :messages="$message" />
        @enderror
    </div>


    {{-- noSuratMeninggal (muncul bila statusPulang = 4) --}}
    @if (($dataDaftarRi['perencanaan']['tindakLanjut']['statusPulang'] ?? '') == 4)
        <div>
            <x-input-label :value="__('No. Surat Meninggal')" />
            <x-text-input placeholder="Minimal 5 karakter" class="w-full mt-1"
                wire:model="dataDaftarRi.perencanaan.tindakLanjut.noSuratMeninggal" :errorshas="$errors->has('dataDaftarRi.perencanaan.tindakLanjut.noSuratMeninggal')" />
            @error('dataDaftarRi.perencanaan.tindakLanjut.noSuratMeninggal')
                <x-input-error :messages="$message" />
            @enderror
        </div>
    @endif

    {{-- Tanggal meninggal --}}
    @if (($dataDaftarRi['perencanaan']['tindakLanjut']['statusPulang'] ?? '') == 4)
        <div>
            <x-input-label :value="__('Tanggal Meninggal')" />
            <div class="mb-2">
                <div class="flex items-center mb-2">
                    <x-text-input id="dataDaftarRi.perencanaan.tindakLanjut.tglMeninggal"
                        placeholder="Tanggal Meninggal [dd/mm/yyyy]" class="mt-1 ml-2" :disabled="$disabledPropertyRjStatus"
                        :errorshas="$errors->has('dataDaftarRi.perencanaan.tindakLanjut.tglMeninggal')" wire:model="dataDaftarRi.perencanaan.tindakLanjut.tglMeninggal" />

                    @if (empty($dataDaftarRi['perencanaan']['tindakLanjut']['tglMeninggal']))
                        <div class="w-1/2 ml-2">
                            <div wire:loading wire:target="setTglMeninggal">
                                <x-loading />
                            </div>

                            <x-green-button :disabled="$disabledPropertyRjStatus"
                                wire:click.prevent="setTglMeninggal('{{ now('Asia/Jakarta')->format('d/m/Y') }}')"
                                type="button" wire:loading.remove>
                                Set Tanggal Meninggal: {{ now('Asia/Jakarta')->format('d/m/Y') }}
                            </x-green-button>
                        </div>
                    @endif
                </div>

                @error('dataDaftarRi.perencanaan.tindakLanjut.tglMeninggal')
                    <x-input-error :messages="$message" />
                @enderror
            </div>
        </div>
    @endif

    {{-- Tanggal Pulang --}}
    <div>
        <x-input-label :value="__('Tanggal Pulang')" />
        <div class="mb-2">
            <div class="flex items-center mb-2">
                <x-text-input id="dataDaftarRi.perencanaan.tindakLanjut.tglPulang"
                    placeholder="Tanggal Pulang [dd/mm/yyyy]" class="mt-1 ml-2" :disabled="$disabledPropertyRjStatus" :errorshas="$errors->has('dataDaftarRi.perencanaan.tindakLanjut.tglPulang')"
                    wire:model="dataDaftarRi.perencanaan.tindakLanjut.tglPulang" />

                @if (empty($dataDaftarRi['perencanaan']['tindakLanjut']['tglPulang']))
                    <div class="w-1/2 ml-2">
                        <div wire:loading wire:target="setTglPulang">
                            <x-loading />
                        </div>

                        <x-green-button :disabled="$disabledPropertyRjStatus"
                            wire:click.prevent="setTglPulang('{{ now('Asia/Jakarta')->format('d/m/Y') }}')"
                            type="button" wire:loading.remove>
                            Set Tanggal Pulang: {{ now('Asia/Jakarta')->format('d/m/Y') }}
                        </x-green-button>
                    </div>
                @endif
            </div>

            @error('dataDaftarRi.perencanaan.tindakLanjut.tglPulang')
                <x-input-error :messages="$message" />
            @enderror
        </div>
    </div>

    <div>
        {{-- ==================== KLL (True / False) ==================== --}}
        <x-input-label :value="__('Kasus Kecelakaan Lalu Lintas?')" :required="true" />

        <div class="flex gap-4 mt-1">
            {{-- Ya = true (1) --}}
            <x-radio-button label="Ya" value="1" {{-- simpan sebagai 1 --}}
                wire:model="dataDaftarRi.perencanaan.tindakLanjut.isKLL" />

            {{-- Tidak = false (0) --}}
            <x-radio-button label="Tidak" value="0" {{-- simpan sebagai 0 --}}
                wire:model="dataDaftarRi.perencanaan.tindakLanjut.isKLL" />
        </div>

        @error('dataDaftarRi.perencanaan.tindakLanjut.isKLL')
            <x-input-error :messages="$message" />
        @enderror
    </div>


    {{-- noLPManual (muncul jika KLL) --}}
    @if ($dataDaftarRi['perencanaan']['tindakLanjut']['isKLL'] ?? false)
        <div>
            <x-input-label :value="__('No. Laporan Polisi (KLL)')" />
            <x-text-input placeholder="Minimal 5 karakter" class="w-full mt-1"
                wire:model="dataDaftarRi.perencanaan.tindakLanjut.noLPManual" :errorshas="$errors->has('dataDaftarRi.perencanaan.tindakLanjut.noLPManual')" />
            @error('dataDaftarRi.perencanaan.tindakLanjut.noLPManual')
                <x-input-error :messages="$message" />
            @enderror
        </div>
    @endif

    <div class="flex justify-end mt-4">
        {{-- Tombol utama --}}
        <x-yellow-button :disabled="$disabledPropertyRjStatus" wire:click.prevent="updateTglPulangBPJS()" type="button"
            wire:loading.remove wire:target="updateTglPulangBPJS">
            Update Tgl Pulang BPJS
        </x-yellow-button>

        {{-- Loading indicator --}}
        <div wire:loading wire:target="updateTglPulangBPJS">
            <x-loading />
        </div>
    </div>

</div>
