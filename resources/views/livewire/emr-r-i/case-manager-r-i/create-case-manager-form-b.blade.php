<div class="fixed inset-0 z-40">

    <div class="">

        <!-- This element is to trick the browser into transition-opacity. -->
        <div class="fixed inset-0 transition-opacity">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <!-- This element is to trick the browser into transition-opacity. Body-->
        <div class="fixed inset-0 transition-opacity">
            <div class="absolute overflow-auto bg-white rounded-t-lg inset-4">

                {{-- Topbar --}}
                <div
                    class="sticky top-0 flex items-center justify-between p-4 bg-opacity-75 border-b rounded-t-lg bg-primary">

                    <!-- myTitle-->
                    <h3 class="w-full text-2xl font-semibold text-white ">
                        {{ 'Case Manager' }}
                    </h3>

                    {{-- rjDate & Shift Input Rj --}}
                    <div id="case-manager-pasien-shiftTanggal" class="flex justify-end w-full mr-4">


                        {{-- Close Modal --}}
                        <button wire:click="closeModalCaseManagerRI()"
                            class="text-gray-400 bg-gray-50 hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>

                    {{-- Pasien --}}





                </div>

                {{-- Display Pasien Componen --}}
                <div class="">
                    <livewire:emr-r-i.display-pasien.display-pasien
                        :wire:key="$riHdrNoRef.'display-pasien-case-manager-pasien'" :riHdrNoRef="$riHdrNoRef">
                </div>


                {{-- Transasi EMR --}}
                <div class="mx-8 mb-8">
                    <h3 class="mb-4 text-xl font-semibold">Form B - Pelaksanaan, Monitoring, Advokasi, Terminasi
                    </h3>

                    <form wire:submit.prevent="simpanFormB">
                        <div class="grid grid-cols-1 gap-4">
                            <!-- Pilih Form A yang akan dilanjutkan -->
                            <div>
                                <x-input-label :value="__('Referensi Form A')" />
                                {{-- Teks baca-saja untuk ditampilkan ke user --}}
                                <input type="text"
                                    class="w-full mt-1 text-gray-700 bg-gray-100 border-gray-300 rounded-md shadow-sm"
                                    value="{{ $formB['formA_id'] }}" disabled>

                                {{-- Hidden input supaya nilai id tetap ter-bind ke Livewire --}}
                                <input type="hidden" wire:model="formB.formA_id">

                                @error('formB.formA_id')
                                    <x-input-error :messages="$message" />
                                @enderror
                                @error('formB.formA_id')
                                    <x-input-error :messages="$message" />
                                @enderror
                            </div>

                            <!-- Tanggal Kegiatan -->
                            <div>
                                <x-input-label for="formB.tanggal" :value="__('Tanggal Kegiatan')" :required="true" />
                                <div class="flex items-center mt-1">
                                    <x-text-input id="formB.tanggal" type="text" placeholder="dd/mm/yyyy HH:MM:SS"
                                        class="w-full" wire:model="formB.tanggal" />
                                    @if (empty($formB['tanggal']))
                                        <div class="w-1/2 ml-2">
                                            <div wire:loading wire:target="setTanggalFormB">
                                                <x-loading />
                                            </div>
                                            <x-green-button :disabled="false" wire:click.prevent="setTanggalFormB"
                                                type="button" wire:loading.remove>
                                                <div wire:poll.20s>
                                                    Set Tanggal: {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}
                                                </div>
                                            </x-green-button>
                                        </div>
                                    @endif
                                </div>
                                @error('formB.tanggal')
                                    <x-input-error :messages="$message" />
                                @enderror
                            </div>

                            <!-- Pelaksanaan Monitoring -->
                            <div>
                                <x-input-label for="formB.pelaksanaanMonitoring" :value="__('Pelaksanaan dan Monitoring')" />
                                <x-text-input-area rows="4" class="w-full mt-1"
                                    wire:model="formB.pelaksanaanMonitoring"
                                    placeholder="Deskripsi pelaksanaan dan monitoring yang dilakukan..."></x-text-input-area>
                                @error('formB.pelaksanaanMonitoring')
                                    <x-input-error :messages="$message" />
                                @enderror
                            </div>
                        </div>

                        <!-- ADVOKASI & KOLABORASI -->
                        <div class="p-4 mt-6 border rounded-lg bg-gray-50">
                            <h4 class="mb-3 font-semibold">Advokasi & Kolaborasi</h4>

                            <div class="grid grid-cols-1 gap-4">
                                <!-- Hambatan Pasien -->
                                <div>
                                    <x-input-label for="formB.advokasiKolaborasi.hambatanPasien" :value="__('Hambatan Pasien')" />
                                    <x-text-input-area rows="2" class="w-full mt-1"
                                        wire:model="formB.advokasiKolaborasi.hambatanPasien"
                                        placeholder="Hambatan yang dialami pasien..."></x-text-input-area>
                                    @error('formB.advokasiKolaborasi.hambatanPasien')
                                        <x-input-error :messages="$message" />
                                    @enderror
                                </div>

                                <!-- Kolaborasi Dengan -->
                                <div>
                                    <x-input-label for="formB.advokasiKolaborasi.kolaborasiDengan" :value="__('Kolaborasi Dengan')" />
                                    <x-text-input id="formB.advokasiKolaborasi.kolaborasiDengan" class="w-full mt-1"
                                        wire:model="formB.advokasiKolaborasi.kolaborasiDengan"
                                        placeholder="Unit/bagian yang diajak kolaborasi..." />
                                    @error('formB.advokasiKolaborasi.kolaborasiDengan')
                                        <x-input-error :messages="$message" />
                                    @enderror
                                </div>

                                <!-- Advokasi Dilakukan -->
                                <div>
                                    <x-input-label for="formB.advokasiKolaborasi.advokasiDilakukan"
                                        :value="__('Advokasi Dilakukan')" />
                                    <x-text-input-area rows="2" class="w-full mt-1"
                                        wire:model="formB.advokasiKolaborasi.advokasiDilakukan"
                                        placeholder="Jenis advokasi yang dilakukan..."></x-text-input-area>
                                    @error('formB.advokasiKolaborasi.advokasiDilakukan')
                                        <x-input-error :messages="$message" />
                                    @enderror
                                </div>

                                <!-- Eskalasi -->
                                <div>
                                    <x-input-label for="formB.advokasiKolaborasi.eskalasi" :value="__('Eskalasi')" />
                                    <x-text-input id="formB.advokasiKolaborasi.eskalasi" class="w-full mt-1"
                                        wire:model="formB.advokasiKolaborasi.eskalasi"
                                        placeholder="Eskalasi ke level mana..." />
                                    @error('formB.advokasiKolaborasi.eskalasi')
                                        <x-input-error :messages="$message" />
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- TERMINASI -->
                        <div class="mt-6">
                            <x-input-label for="formB.terminasi" :value="__('Ringkasan Terminasi')" />
                            <x-text-input-area rows="3" class="w-full mt-1" wire:model="formB.terminasi"
                                placeholder="Ringkasan hasil terminasi..."></x-text-input-area>
                            @error('formB.terminasi')
                                <x-input-error :messages="$message" />
                            @enderror
                        </div>

                        <!-- TANDA TANGAN PETUGAS FORM B -->
                        <div class="p-4 mt-6 border rounded-lg bg-gray-50">
                            <h4 class="mb-3 font-semibold">Tanda Tangan Petugas</h4>
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <!-- HAPUS HIDDEN INPUTS DI SINI -->
                                <div>
                                    <x-input-label :value="__('Nama Petugas')" />
                                    <x-text-input value="{{ auth()->user()->myuser_name ?? '' }}" class="w-full mt-1"
                                        disabled />
                                </div>
                                <div>
                                    <x-input-label :value="__('Kode Petugas')" />
                                    <x-text-input value="{{ auth()->user()->myuser_code ?? '' }}" class="w-full mt-1"
                                        disabled />
                                </div>
                                <div class="md:col-span-2">
                                    <x-input-label :value="__('Jabatan')" />
                                    <x-text-input value="MPP" class="w-full mt-1" disabled />
                                </div>
                            </div>
                        </div>

                        <!-- TOMBOL SIMPAN FORM B -->
                        <div class="flex justify-end gap-4 mt-6">
                            <x-secondary-button wire:click="$set('showFormB', false)" type="button" class="px-6 py-2">
                                Batal
                            </x-secondary-button>
                            <x-primary-button type="submit" class="px-6 py-2">
                                Simpan Form B
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>



        </div>




    </div>

</div>
