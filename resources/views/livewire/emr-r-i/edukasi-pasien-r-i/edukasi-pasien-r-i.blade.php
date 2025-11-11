<div>
    @php
        $disabledProperty = false;
    @endphp

    <div class="w-full mb-1">
        <div class="w-full p-4 text-sm">
            <h2 class="text-2xl font-bold text-center">Formulir Pemberian Informasi Pasien</h2>
            <br />

            <div class="w-full p-2 m-2 mx-auto bg-white rounded-lg shadow-md">
                {{-- Tanggal Edukasi --}}
                <div>
                    <x-input-label for="formEntryEdukasiPasien.tglEdukasi" :value="__('Tanggal Edukasi')" :required="true" />
                    <div class="flex items-center mt-2">
                        <x-text-input id="formEntryEdukasiPasien.tglEdukasi" type="text"
                            placeholder="dd/mm/yyyy hh24:mi:ss" class="mt-1 ml-2" :errorshas="$errors->has('formEntryEdukasiPasien.tglEdukasi')" :disabled="$disabledProperty"
                            wire:model.debounce.500ms="formEntryEdukasiPasien.tglEdukasi" />

                        @if (empty($formEntryEdukasiPasien['tglEdukasi']))
                            <div class="w-1/2 ml-2">
                                <div wire:loading wire:target="setTglEdukasi">
                                    <x-loading />
                                </div>
                                <x-green-button :disabled="false" wire:click.prevent="setTglEdukasi" type="button"
                                    wire:loading.remove>
                                    <div wire:poll.20s>
                                        Set Tanggal Edukasi: {{ date('d/m/Y H:i:s') }}
                                    </div>
                                </x-green-button>
                            </div>
                        @endif
                    </div>
                    @error('formEntryEdukasiPasien.tglEdukasi')
                        <x-input-error :messages="$message" />
                    @enderror
                </div>

                {{-- Dokter Pelaksana Tindakan --}}
                <div class="grid grid-cols-4 gap-2 mt-2">
                    <div>
                        <x-input-label for="formEntryEdukasiPasien.dokterPelaksanaTindakan.drName" :value="__('Dokter Pelaksana Tindakan')"
                            :required="true" />
                        <x-text-input id="formEntryEdukasiPasien.dokterPelaksanaTindakan.drName"
                            placeholder="Nama Dokter" class="mt-1 ml-2" :errorshas="$errors->has('formEntryEdukasiPasien.dokterPelaksanaTindakan.drName')" :disabled="$disabledProperty"
                            wire:model.lazy="formEntryEdukasiPasien.dokterPelaksanaTindakan.drName" />
                        @error('formEntryEdukasiPasien.dokterPelaksanaTindakan.drName')
                            <x-input-error :messages="$message" />
                        @enderror
                    </div>
                    {{-- <div>
                        <x-input-label for="formEntryEdukasiPasien.dokterPelaksanaTindakan.drId" :value="__('ID Dokter (opsional)')" />
                        <x-text-input id="formEntryEdukasiPasien.dokterPelaksanaTindakan.drId"
                            placeholder="Kode/ID Dokter" class="mt-1 ml-2" :errorshas="$errors->has('formEntryEdukasiPasien.dokterPelaksanaTindakan.drId')" :disabled="$disabledProperty"
                            wire:model.lazy="formEntryEdukasiPasien.dokterPelaksanaTindakan.drId" />
                        @error('formEntryEdukasiPasien.dokterPelaksanaTindakan.drId')
                            <x-input-error :messages="$message" />
                        @enderror
                    </div> --}}

                    {{-- Pemberi Informasi (auto dari user login, hanya tampil) --}}
                    <div>
                        <x-input-label :value="__('Pemberi Informasi (Petugas)')" />
                        <x-text-input disabled value="{{ auth()->user()->myuser_name ?? '' }}" class="mt-1 ml-2" />
                    </div>
                    {{-- <div>
                        <x-input-label :value="__('Kode Petugas')" />
                        <x-text-input disabled value="{{ auth()->user()->myuser_code ?? '' }}" class="mt-1 ml-2" />
                    </div> --}}
                </div>



                {{-- TABEL JENIS INFORMASI --}}
                <div class="mt-4">
                    @php
                        $rows = [
                            ['key' => 'diagnosis', 'label' => 'Diagnosis (WD & DD)'],
                            ['key' => 'dasar', 'label' => 'Dasar Diagnosis'],
                            ['key' => 'rencana', 'label' => 'Rencana Pengobatan / Tindakan'],
                            ['key' => 'indikasi', 'label' => 'Indikasi Pengobatan / Tindakan'],
                            ['key' => 'tindakan', 'label' => 'Tindakan Kedokteran'],
                            ['key' => 'tujuan', 'label' => 'Tujuan'],
                            ['key' => 'risiko', 'label' => 'Risiko'],
                            ['key' => 'komplikasi', 'label' => 'Komplikasi'],
                            ['key' => 'prognosis', 'label' => 'Prognosis'],
                            ['key' => 'alternatif', 'label' => 'Alternatif & Risiko'],
                        ];
                    @endphp

                    <table class="min-w-full border">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="w-10 px-3 py-2 border">No</th>
                                <th class="px-3 py-2 border">Jenis Informasi</th>
                                <th class="px-3 py-2 border">Isi Informasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rows as $i => $r)
                                <tr>
                                    <td class="px-3 py-2 align-top border">{{ $i + 1 }}</td>
                                    <td class="px-3 py-2 align-top border">{{ $r['label'] }}</td>
                                    <td class="px-3 py-2 border">
                                        <x-text-input-area rows="2" class="w-full px-2 py-1 border rounded"
                                            wire:model.lazy="formEntryEdukasiPasien.detailInformasi.{{ $r['key'] }}.desc"></x-text-input-area>
                                        @error("formEntryEdukasiPasien.detailInformasi.{$r['key']}.desc")
                                            <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
                                        @enderror
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Penerima Informasi (nama, hubungan, tanda tangan) --}}
                <div class="grid content-center grid-cols-1 gap-2 p-2 m-2 md:grid-cols-3">
                    <div
                        class="flex items-end justify-center w-full p-2 m-2 mx-auto bg-white rounded-lg shadow-md md:col-span-3">
                        <div class="relative flex flex-col gap-4 p-6 bg-white rounded-lg shadow-xl">
                            <x-signature-pad wire:model.defer="sasaranEdukasiSignature" />
                            @error('formEntryEdukasiPasien.penerimaInformasi.signature')
                                <x-input-error :messages="$message" />
                            @enderror

                            <div>
                                <x-input-label :value="__('Nama Penerima Informasi')" :required="true" />
                                <x-text-input :disabled="$disabledProperty" placeholder="Nama keluarga atau wali pasien"
                                    class="mt-1 ml-2" :errorshas="$errors->has('formEntryEdukasiPasien.penerimaInformasi.name')"
                                    wire:model.lazy="formEntryEdukasiPasien.penerimaInformasi.name" />
                                @error('formEntryEdukasiPasien.penerimaInformasi.name')
                                    <x-input-error :messages="$message" />
                                @enderror
                            </div>

                            <div>
                                <x-input-label :value="__('Hubungan dengan Pasien')" :required="true" />
                                <x-text-input :disabled="$disabledProperty" placeholder="Orang Tua / Suami/Istri / Anak / dll"
                                    class="mt-1 ml-2" :errorshas="$errors->has('formEntryEdukasiPasien.penerimaInformasi.hubungan')"
                                    wire:model.lazy="formEntryEdukasiPasien.penerimaInformasi.hubungan" />
                                @error('formEntryEdukasiPasien.penerimaInformasi.hubungan')
                                    <x-input-error :messages="$message" />
                                @enderror
                            </div>

                            <div class="flex items-center gap-2">
                                {{-- Tombol Submit --}}
                                <x-primary-button :disabled="false" wire:click.prevent="addEdukasiPasien"
                                    wire:loading.remove type="button" class="text-white">
                                    Simpan
                                </x-primary-button>

                                {{-- Loading Indicator --}}
                                <div wire:loading wire:target="addEdukasiPasien">
                                    <x-loading />
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

            </div>

            {{-- RIWAYAT --}}
            @include('livewire.emr-r-i.edukasi-pasien-r-i.edukasi-pasien-r-i-table')
        </div>
    </div>
</div>
