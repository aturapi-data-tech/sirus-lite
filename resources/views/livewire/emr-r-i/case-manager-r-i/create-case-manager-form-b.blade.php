<div class="fixed inset-0 z-40">
    <div class="">
        {{-- Backdrop --}}
        <div class="fixed inset-0 transition-opacity">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        {{-- Body --}}
        <div class="fixed inset-0 transition-opacity">
            <div class="absolute overflow-auto bg-white rounded-t-lg inset-4">

                {{-- Topbar --}}
                <div
                    class="sticky top-0 flex items-center justify-between p-4 bg-opacity-75 border-b rounded-t-lg bg-primary">

                    <h3 class="w-full text-2xl font-semibold text-white">
                        Form B
                    </h3>

                    <div id="case-manager-pasien-shiftTanggal" class="flex justify-end w-full mr-4">
                        {{-- Close Modal --}}
                        <button wire:click="$set('showFormB', false)"
                            class="text-gray-400 bg-gray-50 hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Display Pasien --}}
                <div>
                    <livewire:emr-r-i.display-pasien.display-pasien
                        :wire:key="$riHdrNoRef.'display-pasien-case-manager-pasien'" :riHdrNoRef="$riHdrNoRef">
                </div>

                {{-- Transaksi EMR --}}
                <div class="mx-8 mb-8">
                    <h3 class="mb-4 text-xl font-semibold">
                        Form B - Pelaksanaan, Monitoring, Advokasi, Terminasi
                    </h3>

                    {{-- 🔎 RINGKASAN IDENTIFIKASI KASUS DARI FORM A --}}
                    @php
                        $selectedFormA = collect($dataDaftarRi['formMPP']['formA'] ?? [])->firstWhere(
                            'formA_id',
                            $formB['formA_id'] ?? null,
                        );
                    @endphp

                    @if ($selectedFormA)
                        <div class="p-4 mb-4 border rounded-lg bg-gray-50">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-sm font-semibold text-gray-800">
                                    Ringkasan Form A – Identifikasi Kasus
                                </h4>
                                <span class="text-xs text-gray-500">
                                    Tanggal Form A: {{ $selectedFormA['tanggal'] ?? '-' }}
                                </span>
                            </div>

                            <textarea rows="3" class="w-full text-sm bg-gray-100 border-gray-300 rounded-md shadow-sm" disabled>{{ $selectedFormA['indentifikasiKasus'] ?? '-' }}</textarea>
                        </div>
                    @endif
                    {{-- 🔚 END RINGKASAN FORM A --}}

                    <form wire:submit.prevent="simpanFormB">
                        <div class="grid grid-cols-1 gap-4">

                            {{-- Tanggal Kegiatan --}}
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
                                                    Set Tanggal:
                                                    {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}
                                                </div>
                                            </x-green-button>
                                        </div>
                                    @endif
                                </div>
                                @error('formB.tanggal')
                                    <x-input-error :messages="$message" />
                                @enderror
                            </div>

                        </div>

                        {{-- ADVOKASI & KOLABORASI (STRING) --}}
                        <div class="grid grid-cols-3 gap-4 mb-4">

                            {{-- Pelaksanaan Monitoring --}}
                            <div>
                                <x-input-label for="formB.pelaksanaanMonitoring" :value="__('Pelaksanaan dan Monitoring')" />
                                <x-text-input-area rows="4" class="w-full mt-1"
                                    wire:model="formB.pelaksanaanMonitoring"
                                    placeholder="Deskripsi pelaksanaan dan monitoring yang dilakukan...">
                                </x-text-input-area>
                                @error('formB.pelaksanaanMonitoring')
                                    <x-input-error :messages="$message" />
                                @enderror
                            </div>

                            <div>
                                <x-input-label for="formB.advokasiKolaborasi" :value="__('Advokasi dan Kolaborasi')" />
                                <x-text-input-area rows="4" class="w-full mt-1"
                                    wire:model="formB.advokasiKolaborasi"
                                    placeholder="Ringkasan hambatan pasien, pihak yang diajak kolaborasi, bentuk advokasi, dan eskalasi (bila ada)...">
                                </x-text-input-area>
                                @error('formB.advokasiKolaborasi')
                                    <x-input-error :messages="$message" />
                                @enderror
                            </div>

                            {{-- TERMINASI --}}
                            <div>
                                <x-input-label for="formB.terminasi" :value="__('Ringkasan Terminasi')" />
                                <x-text-input-area rows="4" class="w-full mt-1" wire:model="formB.terminasi"
                                    placeholder="Ringkasan hasil terminasi...">
                                </x-text-input-area>
                                @error('formB.terminasi')
                                    <x-input-error :messages="$message" />
                                @enderror
                            </div>
                        </div>



                        {{-- TANDA TANGAN PETUGAS FORM B --}}
                        {{-- <div class="p-4 mt-6 border rounded-lg bg-gray-50">
                            <h4 class="mb-3 font-semibold">Tanda Tangan Petugas</h4>
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
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
                        </div> --}}

                        {{-- TOMBOL SIMPAN FORM B --}}
                        <div class="flex justify-between gap-4 mt-6">
                            <div></div>
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
