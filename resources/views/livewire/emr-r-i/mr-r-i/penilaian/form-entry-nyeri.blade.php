<div class="space-y-4">
    <!-- Apakah Pasien Mengalami Nyeri? -->
    <div>
        <x-input-label for="formEntryNyeri.nyeri.nyeri" :value="__('Apakah Pasien Mengalami Nyeri?')" :required="__(true)" />
        <div class="grid grid-cols-2 gap-2">
            <x-radio-button label="Ya" value="Ya" wire:model="formEntryNyeri.nyeri.nyeri" />
            <x-radio-button label="Tidak" value="Tidak" wire:model="formEntryNyeri.nyeri.nyeri" />
        </div>
        @error('formEntryNyeri.nyeri.nyeri')
            <x-input-error :messages="$message" />
        @enderror
    </div>

    <!-- Logika Kondisional: Jika " Ya" dipilih -->
    @if ($formEntryNyeri['nyeri']['nyeri'] === 'Ya')
        <div class="grid grid-cols-2 gap-2">
            <!-- Tekanan Darah -->
            <div class="mb-2">
                <x-input-label for="formEntryNyeri.nyeri.sistolik" :value="__('Tekanan Darah')" :required="__(false)" />
                <div class="grid grid-cols-2 gap-2">
                    <x-text-input-mou id="formEntryNyeri.nyeri.sistolik" placeholder="Sistolik" class="mt-1 ml-2"
                        :errorshas="__($errors->has('formEntryNyeri.nyeri.sistolik'))" :disabled="$disabledPropertyRjStatus" :mou_label="__('mmHg')"
                        wire:model.debounce.500ms="formEntryNyeri.nyeri.sistolik" />
                    <x-text-input-mou id="formEntryNyeri.nyeri.distolik" placeholder="Distolik" class="mt-1 ml-2"
                        :errorshas="__($errors->has('formEntryNyeri.nyeri.distolik'))" :disabled="$disabledPropertyRjStatus" :mou_label="__('mmHg')"
                        wire:model.debounce.500ms="formEntryNyeri.nyeri.distolik" />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <!-- Frekuensi Nafas -->
                <div class="mb-2">
                    <x-input-label for="formEntryNyeri.nyeri.frekuensiNafas" :value="__('Frekuensi Nafas')" :required="__(false)" />
                    <x-text-input-mou id="formEntryNyeri.nyeri.frekuensiNafas" placeholder="Frekuensi Nafas"
                        class="mt-1 ml-2" :errorshas="__($errors->has('formEntryNyeri.nyeri.frekuensiNafas'))" :disabled="$disabledPropertyRjStatus" :mou_label="__('X/Menit')"
                        wire:model.debounce.500ms="formEntryNyeri.nyeri.frekuensiNafas" />
                </div>

                <!-- Frekuensi Nadi -->
                <div class="mb-2">
                    <x-input-label for="formEntryNyeri.nyeri.frekuensiNadi" :value="__('Frekuensi Nadi')" :required="__(false)" />
                    <x-text-input-mou id="formEntryNyeri.nyeri.frekuensiNadi" placeholder="Frekuensi Nadi"
                        class="mt-1 ml-2" :errorshas="__($errors->has('formEntryNyeri.nyeri.frekuensiNadi'))" :disabled="$disabledPropertyRjStatus" :mou_label="__('X/Menit')"
                        wire:model.debounce.500ms="formEntryNyeri.nyeri.frekuensiNadi" />
                </div>
            </div>
        </div>

        <!-- Suhu -->
        <div class="mb-2">
            <x-input-label for="formEntryNyeri.nyeri.suhu" :value="__('Suhu')" :required="__(false)" />
            <x-text-input-mou id="formEntryNyeri.nyeri.suhu" placeholder="Suhu" class="mt-1 ml-2" :errorshas="__($errors->has('formEntryNyeri.nyeri.suhu'))"
                :disabled="$disabledPropertyRjStatus" :mou_label="__('°C')" wire:model.debounce.500ms="formEntryNyeri.nyeri.suhu" />
        </div>

        <!-- Keterangan Nyeri -->
        <div>
            <!-- Metode Penilaian Nyeri -->
            <div>
                <x-input-label for="formEntryNyeri.nyeri.nyeriMetode" :value="__('Metode Penilaian Nyeri')" :required="__(true)" />
                <div class="space-y-2">
                    <div class="grid grid-cols-5 gap-2">
                        @foreach ($nyeriMetodeOptions as $index => $metode)
                            <x-radio-button :label="$metode['nyeriMetode']" :value="$metode['nyeriMetode']"
                                wire:model="formEntryNyeri.nyeri.nyeriMetode.nyeriMetode" />
                        @endforeach
                    </div>
                </div>
                @error('formEntryNyeri.nyeri.nyeriMetode.nyeriMetode')
                    <x-input-error :messages="$message" />
                @enderror
            </div>

            @include('livewire.emr-r-i.mr-r-i.penilaian.form-entry-nyeri1-vas')
            @include('livewire.emr-r-i.mr-r-i.penilaian.form-entry-nyeri2-flacc')



            <x-input-label for="formEntryNyeri.nyeri.nyeriMetode.nyeriMetodeScore" :value="__('Skor Jenis Nyeri')" />
            <x-text-input id="formEntryNyeri.nyeri.nyeriMetode.nyeriMetodeScore" type="number" placeholder="Skor"
                class="mt-1" wire:model.debounce.500ms="formEntryNyeri.nyeri.nyeriMetode.nyeriMetodeScore" />
            @error('formEntryNyeri.nyeri.nyeriMetode.nyeriMetodeScore')
                <x-input-error :messages="$message" />
            @enderror

            <x-input-label for="formEntryNyeri.nyeri.nyeriKet" :value="__('Jenis Nyeri')" :required="__(true)" />
            <x-text-input id="formEntryNyeri.nyeri.nyeriKet" placeholder="Jenis Nyeri (misalnya, Akut, Kronis)"
                class="mt-1" wire:model.debounce.500ms="formEntryNyeri.nyeri.nyeriKet" />
            @error('formEntryNyeri.nyeri.nyeriKet')
                <x-input-error :messages="$message" />
            @enderror
        </div>

        <!-- Pencetus Nyeri -->
        <div>
            <x-input-label for="formEntryNyeri.nyeri.pencetus" :value="__('Pencetus Nyeri')" />
            <x-text-input-area id="formEntryNyeri.nyeri.pencetus" placeholder="Pencetus Nyeri" class="mt-1"
                wire:model.debounce.500ms="formEntryNyeri.nyeri.pencetus" />
            @error('formEntryNyeri.nyeri.pencetus')
                <x-input-error :messages="$message" />
            @enderror
        </div>

        <div class="grid grid-cols-5 gap-2">
            <!-- Durasi Nyeri -->
            <div>
                <x-input-label for="formEntryNyeri.nyeri.durasi" :value="__('Durasi Nyeri')" />
                <x-text-input id="formEntryNyeri.nyeri.durasi" placeholder="Durasi Nyeri" class="mt-1"
                    wire:model.debounce.500ms="formEntryNyeri.nyeri.durasi" />
                @error('formEntryNyeri.nyeri.durasi')
                    <x-input-error :messages="$message" />
                @enderror
            </div>

            <!-- Lokasi Nyeri -->
            <div>
                <x-input-label for="formEntryNyeri.nyeri.lokasi" :value="__('Lokasi Nyeri')" />
                <x-text-input id="formEntryNyeri.nyeri.lokasi" placeholder="Lokasi Nyeri" class="mt-1"
                    wire:model.debounce.500ms="formEntryNyeri.nyeri.lokasi" />
                @error('formEntryNyeri.nyeri.lokasi')
                    <x-input-error :messages="$message" />
                @enderror
            </div>

            <!-- Waktu Nyeri -->
            <div>
                <x-input-label for="formEntryNyeri.nyeri.waktuNyeri" :value="__('Waktu Nyeri')" />
                <x-text-input id="formEntryNyeri.nyeri.waktuNyeri"
                    placeholder="Waktu Nyeri (misalnya, Pagi, Siang, Malam)" class="mt-1"
                    wire:model.debounce.500ms="formEntryNyeri.nyeri.waktuNyeri" />
                @error('formEntryNyeri.nyeri.waktuNyeri')
                    <x-input-error :messages="$message" />
                @enderror
            </div>

            <!-- Tingkat Kesadaran -->
            <div>
                <x-input-label for="formEntryNyeri.nyeri.tingkatKesadaran" :value="__('Tingkat Kesadaran')" />
                <x-text-input id="formEntryNyeri.nyeri.tingkatKesadaran"
                    placeholder="Tingkat Kesadaran (misalnya, Sadar, Bingung)" class="mt-1"
                    wire:model.debounce.500ms="formEntryNyeri.nyeri.tingkatKesadaran" />
                @error('formEntryNyeri.nyeri.tingkatKesadaran')
                    <x-input-error :messages="$message" />
                @enderror
            </div>

            <!-- Tingkat Aktivitas -->
            <div>
                <x-input-label for="formEntryNyeri.nyeri.tingkatAktivitas" :value="__('Tingkat Aktivitas')" />
                <x-text-input id="formEntryNyeri.nyeri.tingkatAktivitas"
                    placeholder="Tingkat Aktivitas (misalnya, Aktif, Pasif)" class="mt-1"
                    wire:model.debounce.500ms="formEntryNyeri.nyeri.tingkatAktivitas" />
                @error('formEntryNyeri.nyeri.tingkatAktivitas')
                    <x-input-error :messages="$message" />
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-3 gap-2">
            <!-- Intervensi Farmakologi -->
            <div>
                <x-input-label for="formEntryNyeri.nyeri.ketIntervensiFarmakologi" :value="__('Intervensi Farmakologi')" />
                <x-text-input-area id="formEntryNyeri.nyeri.ketIntervensiFarmakologi"
                    placeholder="Keterangan Intervensi Farmakologi" class="mt-1"
                    wire:model.debounce.500ms="formEntryNyeri.nyeri.ketIntervensiFarmakologi" />
                @error('formEntryNyeri.nyeri.ketIntervensiFarmakologi')
                    <x-input-error :messages="$message" />
                @enderror
            </div>

            <!-- Intervensi Non-Farmakologi -->
            <div>
                <x-input-label for="formEntryNyeri.nyeri.ketIntervensiNonFarmakologi" :value="__('Intervensi Non-Farmakologi')" />
                <x-text-input-area id="formEntryNyeri.nyeri.ketIntervensiNonFarmakologi"
                    placeholder="Keterangan Intervensi Non-Farmakologi" class="mt-1"
                    wire:model.debounce.500ms="formEntryNyeri.nyeri.ketIntervensiNonFarmakologi" />
                @error('formEntryNyeri.nyeri.ketIntervensiNonFarmakologi')
                    <x-input-error :messages="$message" />
                @enderror
            </div>

            <!-- Catatan Tambahan -->
            <div>
                <x-input-label for="formEntryNyeri.nyeri.catatanTambahan" :value="__('Catatan Tambahan')" />
                <x-text-input-area id="formEntryNyeri.nyeri.catatanTambahan" placeholder="Catatan Tambahan"
                    class="mt-1" wire:model.debounce.500ms="formEntryNyeri.nyeri.catatanTambahan" />
                @error('formEntryNyeri.nyeri.catatanTambahan')
                    <x-input-error :messages="$message" />
                @enderror
            </div>
        </div>

        <!-- Tanggal Penilaian -->
        <div>
            <x-input-label for="formEntryNyeri.tglPenilaian" :value="__('Tanggal Penilaian')" :required="__(true)" />
            <div class="mb-2">
                <div class="flex items-center mb-2">
                    <x-text-input id="formEntryNyeri.tglPenilaian" type="text" placeholder="dd/mm/yyyy hh24:mi:ss"
                        class="mt-1 ml-2" :errorshas="__($errors->has('formEntryNyeri.tglPenilaian'))" :disabled="$disabledPropertyRjStatus"
                        wire:model.debounce.500ms="formEntryNyeri.tglPenilaian" />

                    @if (!$formEntryNyeri['tglPenilaian'])
                        <div class="w-1/2 ml-2">
                            <div wire:loading wire:target="setTglPenilaianNyeri">
                                <x-loading />
                            </div>

                            <x-green-button :disabled="false"
                                wire:click.prevent="setTglPenilaianNyeri('{{ date('d/m/Y H:i:s') }}')" type="button"
                                wire:loading.remove>
                                <div wire:poll>
                                    Set Tanggal Penilaian: {{ date('d/m/Y H:i:s') }}
                                </div>
                            </x-green-button>
                        </div>
                    @endif
                </div>
            </div>
            @error('formEntryNyeri.tglPenilaian')
                <x-input-error :messages="$message" />
            @enderror
        </div>

        <!-- Petugas Penilai -->
        {{-- <div>
            <x-input-label for="formEntryNyeri.petugasPenilai" :value="__('Petugas Penilai')" :required="__(true)" />
            <x-text-input id="formEntryNyeri.petugasPenilai" placeholder="Nama Petugas Penilai" class="mt-1"
                :errorshas="__($errors->has('formEntryNyeri.petugasPenilai'))" wire:model.debounce.500ms="formEntryNyeri.petugasPenilai" />
            @error('formEntryNyeri.petugasPenilai')
                <x-input-error :messages="$message" />
            @enderror
        </div> --}}

        <!-- Kode Petugas Penilai -->
        {{-- <div>
            <x-input-label for="formEntryNyeri.petugasPenilaiCode" :value="__('Kode Petugas Penilai')" :required="__(true)" />
            <x-text-input id="formEntryNyeri.petugasPenilaiCode" placeholder="Kode Petugas Penilai" class="mt-1"
                :errorshas="__($errors->has('formEntryNyeri.petugasPenilaiCode'))" wire:model.debounce.500ms="formEntryNyeri.petugasPenilaiCode" />
            @error('formEntryNyeri.petugasPenilaiCode')
                <x-input-error :messages="$message" />
            @enderror
        </div> --}}

        <div class="grid grid-cols-4 gap-2">
            <div class="grid grid-cols-1 ml-2">
                <div wire:loading wire:target="addAssessmentNyeri">
                    <x-loading />
                </div>
                <x-yellow-button :disabled="$disabledPropertyRjStatus" wire:click.prevent="addAssessmentNyeri()" type="button"
                    wire:loading.remove>
                    Assessment Ulang Nyeri
                </x-yellow-button>
            </div>
        </div>

    @endif

    @include('livewire.emr-r-i.mr-r-i.penilaian.form-entry-nyeri-table')
</div>
