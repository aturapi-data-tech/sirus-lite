<div class="space-y-4">
    <!-- Apakah Pasien Memiliki Risiko Jatuh? -->
    <div class="mb-4">
        <x-input-label for="resikoJatuh" :value="__('Apakah Pasien Memiliki Risiko Jatuh?')" :required="true" />
        <div class="grid grid-cols-2 gap-2 mt-1">
            <x-radio-button label="Ya" value="Ya" wire:model="formEntryResikoJatuh.resikoJatuh.resikoJatuh" />
            <x-radio-button label="Tidak" value="Tidak" wire:model="formEntryResikoJatuh.resikoJatuh.resikoJatuh" />
        </div>
        @error('formEntryResikoJatuh.resikoJatuh.resikoJatuh')
            <x-input-error :messages="$message" />
        @enderror
    </div>

    <!-- Jika "Ya" Dipilih -->
    @if ($formEntryResikoJatuh['resikoJatuh']['resikoJatuh'] === 'Ya')
        <!-- Metode Penilaian -->
        <div>
            <x-input-label for="resikoJatuhMetode" :value="__('Metode Penilaian')" :required="true" />
            <div class="grid grid-cols-4 gap-2 mt-1">
                <x-radio-button label="Skala Morse" value="Skala Morse"
                    wire:model="formEntryResikoJatuh.resikoJatuh.resikoJatuhMetode.resikoJatuhMetode" />
                <x-radio-button label="Skala Humpty Dumpty" value="Skala Humpty Dumpty"
                    wire:model="formEntryResikoJatuh.resikoJatuh.resikoJatuhMetode.resikoJatuhMetode" />
            </div>
            @error('formEntryResikoJatuh.resikoJatuh.resikoJatuhMetode.resikoJatuhMetode')
                <x-input-error :messages="$message" />
            @enderror
        </div>

        <!-- Input Data Berdasarkan Metode -->
        @include('livewire.emr-r-i.mr-r-i.penilaian.form-entry-resiko-jatuh1-skala-morse')
        @include('livewire.emr-r-i.mr-r-i.penilaian.form-entry-resiko-jatuh2-humpty-dumpty')

        <!-- Skor Risiko Jatuh dan Kategori Risiko -->
        <div class="grid grid-cols-2 gap-4">
            <div>
                <x-input-label :value="__('Skor Risiko Jatuh')" />
                <x-text-input id="skorRisikoJatuh" type="number" class="w-full mt-1" readonly
                    wire:model="formEntryResikoJatuh.resikoJatuh.resikoJatuhMetode.resikoJatuhMetodeScore" />
            </div>
            <div>
                <x-input-label :value="__('Kategori Risiko')" />
                <x-text-input id="kategoriRisiko" type="text" class="w-full mt-1" readonly
                    wire:model="formEntryResikoJatuh.resikoJatuh.kategoriResiko" />
            </div>
        </div>

        <!-- Rekomendasi (Textarea) -->
        <div>
            <x-input-label :value="__('Rekomendasi')" />
            <textarea id="rekomendasi" class="w-full mt-1 border-gray-300 rounded-md shadow-sm"
                wire:model.debounce.500ms="formEntryResikoJatuh.resikoJatuh.rekomendasi"></textarea>
            @error('formEntryResikoJatuh.resikoJatuh.rekomendasi')
                <x-input-error :messages="$message" />
            @enderror
        </div>

        <!-- Tanggal Penilaian -->
        <div>
            <x-input-label for="formEntryResikoJatuh.tglPenilaian" :value="__('Tanggal Penilaian')" :required="true" />
            <div class="mb-2">
                <div class="flex items-center mb-2">
                    <!-- Input Tanggal Penilaian -->
                    <x-text-input id="formEntryResikoJatuh.tglPenilaian" type="text"
                        placeholder="dd/mm/yyyy hh24:mi:ss" class="mt-1 ml-2" :errorshas="__($errors->has('formEntryResikoJatuh.tglPenilaian'))" :disabled="$disabledPropertyRjStatus"
                        wire:model.debounce.500ms="formEntryResikoJatuh.tglPenilaian" />

                    <!-- Tombol Set Tanggal Sekarang -->
                    @if (!$formEntryResikoJatuh['tglPenilaian'])
                        <div class="w-1/2 ml-2">
                            <!-- Loading Indicator -->
                            <div wire:loading wire:target="setTglPenilaianResikoJatuh">
                                <x-loading />
                            </div>

                            <!-- Tombol Set Tanggal -->
                            <x-green-button :disabled="false"
                                wire:click.prevent="setTglPenilaianResikoJatuh('{{ date('d/m/Y H:i:s') }}')"
                                type="button" wire:loading.remove>
                                <div wire:poll.20s>
                                    Set Tanggal Penilaian: {{ date('d/m/Y H:i:s') }}
                                </div>
                            </x-green-button>
                        </div>
                    @endif
                </div>
            </div>
            @error('formEntryResikoJatuh.tglPenilaian')
                <x-input-error :messages="$message" />
            @enderror
        </div>

        <!-- Petugas Penilai -->
        {{-- <div>
            <x-input-label for="petugasPenilai" :value="__('Petugas Penilai')" :required="true" />
            <x-text-input id="petugasPenilai" placeholder="Nama Petugas Penilai" class="w-full mt-1"
                wire:model.debounce.500ms="formEntryResikoJatuh.petugasPenilai" />
            @error('formEntryResikoJatuh.petugasPenilai')
                <x-input-error :messages="$message" />
            @enderror
        </div> --}}

        <!-- Kode Petugas Penilai -->
        {{-- <div>
            <x-input-label for="petugasPenilaiCode" :value="__('Kode Petugas Penilai')" :required="true" />
            <x-text-input id="petugasPenilaiCode" placeholder="Kode Petugas Penilai" class="w-full mt-1"
                wire:model.debounce.500ms="formEntryResikoJatuh.petugasPenilaiCode" />
            @error('formEntryResikoJatuh.petugasPenilaiCode')
                <x-input-error :messages="$message" />
            @enderror
        </div> --}}

        <!-- Tombol Simpan -->
        <div class="grid grid-cols-4 gap-2">
            <div class="grid grid-cols-1 ml-2">
                <!-- Loading Indicator -->
                <div wire:loading wire:target="addAssessmentResikoJatuh">
                    <x-loading />
                </div>

                <!-- Tombol Assessment Risiko Jatuh -->
                <x-yellow-button :disabled="$disabledPropertyRjStatus" wire:click.prevent="addAssessmentResikoJatuh()" type="button"
                    wire:loading.remove>
                    Assessment Risiko Jatuh
                </x-yellow-button>
            </div>
        </div>
    @endif

    <!-- Tabel Daftar Penilaian Risiko Jatuh -->
    @include('livewire.emr-r-i.mr-r-i.penilaian.form-entry-resiko-jatuh-table')
</div>
