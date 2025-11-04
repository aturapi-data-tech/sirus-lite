<div>
    @php
        $disabledProperty = $formEntryEdukasiPasien['sasaranEdukasiSignature'] ? true : false;
    @endphp
    <div class="w-full mb-1">
        <div class="w-full p-4 text-sm">
            <h2 class="text-2xl font-bold text-center">Formulir Edukasi Pasien</h2>
            </br>

            <div class="w-full p-2 m-2 mx-auto bg-white rounded-lg shadow-md">
                <!-- Input Tanggal Edukasi -->
                <div>
                    <x-input-label for="formEntryEdukasiPasien.tglEdukasi" :value="__('Tanggal Edukasi')" :required="true" />
                    <div class="mt-2">
                        <div class="flex items-center mt-2">
                            <!-- Input Tanggal Edukasi -->
                            <x-text-input id="formEntryEdukasiPasien.tglEdukasi" type="text"
                                placeholder="dd/mm/yyyy hh24:mi:ss" class="mt-1 ml-2" :errorshas="__($errors->has('formEntryEdukasiPasien.tglEdukasi'))" :disabled="$disabledProperty"
                                wire:model.debounce.500ms="formEntryEdukasiPasien.tglEdukasi" />

                            <!-- Tombol Set Tanggal Sekarang -->
                            @if (!$formEntryEdukasiPasien['tglEdukasi'])
                                <div class="w-1/2 ml-2">
                                    <!-- Loading Indicator -->
                                    <div wire:loading wire:target="setTglEdukasi">
                                        <x-loading />
                                    </div>

                                    <!-- Tombol Set Tanggal -->
                                    <x-green-button :disabled="false"
                                        wire:click.prevent="setTglEdukasi('{{ date('d/m/Y H:i:s') }}')" type="button"
                                        wire:loading.remove>
                                        <div wire:poll.20s>
                                            Set Tanggal Edukasi: {{ date('d/m/Y H:i:s') }}
                                        </div>
                                    </x-green-button>
                                </div>
                            @endif
                        </div>
                    </div>
                    @error('formEntryEdukasiPasien.tglEdukasi')
                        <x-input-error :messages="$message" />
                    @enderror
                </div>


                <!-- Checkbox Kategori Edukasi -->
                <div class="mt-2">
                    <x-input-label :value="__('Kategori Edukasi')" :required="true" />
                    <div class="grid grid-cols-1 gap-2 mt-2 sm:grid-cols-2 md:grid-cols-3">
                        @foreach ($edukasiOptions as $index => $option)
                            <div>
                                <x-check-box :id="'opsi-' . $index" :value="$option['kategoriEdukasi']" :label="__($option['kategoriEdukasi'])"
                                    wire:model.debounce.500ms="formEntryEdukasiPasien.edukasi.kategoriEdukasi.{{ $index }}.kategoriEdukasi" />
                            </div>
                        @endforeach
                    </div>
                    @error('formEntryEdukasiPasien.edukasi.kategoriEdukasi')
                        <x-input-error :messages="$message" />
                    @enderror
                </div>

                <!-- Input Keterangan Edukasi -->
                <div>
                    <x-input-label for="formEntryEdukasiPasien.edukasi.keteranganEdukasi" :value="__('Keterangan Edukasi')"
                        :required="true" />
                    <div class="mt-2">
                        <x-text-input :disabled=$disabledProperty id="formEntryEdukasiPasien.edukasi.keteranganEdukasi"
                            placeholder="Penjelasan atau keterangan edukasi yang diberikan" class="mt-1 ml-2"
                            :errorshas="__($errors->has('formEntryEdukasiPasien.edukasi.keteranganEdukasi'))" wire:model.lazy="formEntryEdukasiPasien.edukasi.keteranganEdukasi" />
                    </div>
                    @error('formEntryEdukasiPasien.edukasi.keteranganEdukasi')
                        <x-input-error :messages="$message" />
                    @enderror
                </div>

                <!-- Input Status Edukasi -->
                <div>
                    <x-input-label for="formEntryEdukasiPasien.edukasi.statusEdukasi" :value="__('Status Edukasi')"
                        :required="true" />
                    <div class="mt-2">
                        <x-text-input :disabled=$disabledProperty id="formEntryEdukasiPasien.edukasi.statusEdukasi"
                            placeholder="Misalnya: Mengerti, Tidak Mengerti" class="mt-1 ml-2" :errorshas="__($errors->has('formEntryEdukasiPasien.edukasi.statusEdukasi'))"
                            wire:model.lazy="formEntryEdukasiPasien.edukasi.statusEdukasi" />
                    </div>
                    @error('formEntryEdukasiPasien.edukasi.statusEdukasi')
                        <x-input-error :messages="$message" />
                    @enderror
                </div>

                <!-- Checkbox Perlu Re-Edukasi -->
                <div class="mt-2">
                    <x-check-box value='1' :label="__('Perlu Re-Edukasi')"
                        wire:model.debounce.500ms="formEntryEdukasiPasien.edukasi.reEdukasi.perlu" />
                </div>

                <!-- Tampilkan Input Tanggal Re-Edukasi dan Petugas Re-Edukasi jika Perlu Re-Edukasi true -->
                @if ($formEntryEdukasiPasien['edukasi']['reEdukasi']['perlu'])
                    <div>
                        <!-- Input Tanggal Re-Edukasi -->
                        <x-input-label for="formEntryEdukasiPasien.edukasi.reEdukasi.tglReEdukasi" :value="__('Tanggal Re-Edukasi')"
                            :required="true" />
                        <div class="mt-2">
                            <div class="flex items-center mt-2">
                                <x-text-input id="formEntryEdukasiPasien.edukasi.reEdukasi.tglReEdukasi" type="text"
                                    placeholder="dd/mm/yyyy hh24:mi:ss" class="mt-1 ml-2" :errorshas="__(
                                        $errors->has('formEntryEdukasiPasien.edukasi.reEdukasi.tglReEdukasi'),
                                    )"
                                    :disabled="$disabledProperty"
                                    wire:model.debounce.500ms="formEntryEdukasiPasien.edukasi.reEdukasi.tglReEdukasi" />

                                <!-- Tombol Set Tanggal Sekarang -->
                                @if (!$formEntryEdukasiPasien['edukasi']['reEdukasi']['tglReEdukasi'])
                                    <div class="w-1/2 ml-2">
                                        <!-- Loading Indicator -->
                                        <div wire:loading wire:target="setTglReEdukasi">
                                            <x-loading />
                                        </div>

                                        <!-- Tombol Set Tanggal -->
                                        <x-green-button :disabled="false"
                                            wire:click.prevent="setTglReEdukasi('{{ date('d/m/Y H:i:s') }}')"
                                            type="button" wire:loading.remove>
                                            <div wire:poll.20s>
                                                Set Tanggal Re-Edukasi: {{ date('d/m/Y H:i:s') }}
                                            </div>
                                        </x-green-button>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @error('formEntryEdukasiPasien.edukasi.reEdukasi.tglReEdukasi')
                            <x-input-error :messages="$message" />
                        @enderror
                    </div>

                    <!-- Input Petugas Re-Edukasi -->
                    <div>
                        <x-input-label for="formEntryEdukasiPasien.edukasi.reEdukasi.petugasReEdukasi"
                            :value="__('Petugas Re-Edukasi')" :required="true" />
                        <div class="mt-2">
                            <x-text-input id="formEntryEdukasiPasien.edukasi.reEdukasi.petugasReEdukasi" type="text"
                                placeholder="Nama petugas yang memberikan re-edukasi" class="mt-1 ml-2"
                                :errorshas="__(
                                    $errors->has('formEntryEdukasiPasien.edukasi.reEdukasi.petugasReEdukasi'),
                                )" :disabled="$disabledProperty"
                                wire:model.debounce.500ms="formEntryEdukasiPasien.edukasi.reEdukasi.petugasReEdukasi" />
                        </div>
                        @error('formEntryEdukasiPasien.edukasi.reEdukasi.petugasReEdukasi')
                            <x-input-error :messages="$message" />
                        @enderror
                    </div>
                @endif
            </div>

            <div class="grid content-center grid-cols-3 gap-2 p-2 m-2">
                @if (!$formEntryEdukasiPasien['sasaranEdukasiSignature'])
                    <div class="flex items-end justify-center w-full p-2 m-2 mx-auto bg-white rounded-lg shadow-md">
                        <div class="relative flex flex-col gap-4 p-6 bg-white rounded-lg shadow-xl">
                            <x-signature-pad wire:model.defer="sasaranEdukasiSignature">
                            </x-signature-pad>
                            @error('formEntryEdukasiPasien.sasaranEdukasiSignature')
                                <x-input-error :messages=$message />
                            @enderror

                            <div>
                                <x-text-input :disabled=$disabledProperty id="formEntryEdukasiPasien.sasaranEdukasi"
                                    placeholder="Nama keluarga atau wali pasien" class="mt-1 ml-2" :errorshas="__($errors->has('formEntryEdukasiPasien.sasaranEdukasi'))"
                                    wire:model.lazy="formEntryEdukasiPasien.sasaranEdukasi" />
                            </div>
                            <div>
                                <x-text-input :disabled=$disabledProperty
                                    id="formEntryEdukasiPasien.hubunganSasaranEdukasidgnPasien"
                                    placeholder="Hubungan Misalnya: Orang Tua, Suami/Istri, Anak" class="mt-1 ml-2"
                                    :errorshas="__(
                                        $errors->has('formEntryEdukasiPasien.hubunganSasaranEdukasidgnPasien'),
                                    )"
                                    wire:model.lazy="formEntryEdukasiPasien.hubunganSasaranEdukasidgnPasien" />
                            </div>

                            <x-primary-button wire:click="addEdukasiPasien" class="text-white">
                                Submit
                            </x-primary-button>
                        </div>
                    </div>
                @else
                    <div class="flex items-end justify-center w-full p-2 m-2 mx-auto bg-white rounded-lg shadow-md">
                        <div class="w-56 h-auto">
                            <div class="text-sm text-right">
                                {{ env('SATUSEHAT_ORGANIZATION_NAMEX', 'RUMAH SAKIT ISLAM MADINAH') }}
                            </div>
                            <div class="text-sm text-right">
                                {{ ' Ngunut , ' . $formEntryEdukasiPasien['tglEdukasi'] }}
                            </div>
                            <div class="flex items-center justify-center">
                                <object type="image/svg+xml"
                                    data="data:image/svg+xml;utf8,{{ $formEntryEdukasiPasien['sasaranEdukasiSignature'] ?? $sasaranEdukasiSignature }}">
                                    <img src="fallback.png" alt="Fallback image for browsers that don't support SVG">
                                </object>
                            </div>

                            <div class="mb-4 text-sm text-center">
                                {{ $formEntryEdukasiPasien['sasaranEdukasi'] }}
                                </br>
                                Sasaran Edukasi
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            @include('livewire.emr-r-i.edukasi-pasien-r-i.edukasi-pasien-r-i-table')
        </div>
    </div>
</div>
