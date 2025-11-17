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

                    {{-- Pasien --}}





                </div>

                {{-- Display Pasien Componen --}}
                <div class="">
                    <livewire:emr-r-i.display-pasien.display-pasien
                        :wire:key="$riHdrNoRef.'display-pasien-case-manager-pasien'" :riHdrNoRef="$riHdrNoRef">
                </div>


                {{-- Transasi EMR --}}
                <div class="pt-6 mx-8 mt-6 mb-8 border-t">

                    <form wire:submit.prevent="simpanFormB">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <!-- Tanggal -->
                            <div>
                                <x-input-label for="formIntervensiImplementasi.tanggal" :value="__('Tanggal')"
                                    :required="true" />
                                <div class="grid items-center grid-cols-3 mt-1">
                                    <div class="col-span-2">
                                        <x-text-input id="formIntervensiImplementasi.tanggal" type="text"
                                            placeholder="dd/mm/yyyy HH:MM:SS" class="w-full"
                                            wire:model="formIntervensiImplementasi.tanggal" />
                                    </div>
                                    @if (empty($formIntervensiImplementasi['tanggal']))
                                        <div class="col-span-1 ml-2">
                                            <div wire:loading wire:target="setTanggalFormB">
                                                <x-loading />
                                            </div>
                                            <x-green-button :disabled="false" wire:click.prevent="setTanggalFormB"
                                                type="button" wire:loading.remove>
                                                Set Tanggal
                                            </x-green-button>
                                        </div>
                                    @endif
                                </div>
                                @error('formIntervensiImplementasi.tanggal')
                                    <x-input-error :messages="$message" />
                                @enderror
                            </div>

                            <!-- Referensi Diagnosa -->
                            <div>
                                @php
                                    $selectedDiagnosa = null;
                                    if (!empty($formIntervensiImplementasi['formDiagnosaKeperawatan_id'] ?? null)) {
                                        $selectedDiagnosa = collect(
                                            $dataDaftarRi['diagKeperawatan']['formDiagnosaKeperawatan'] ?? [],
                                        )->firstWhere(
                                            'formDiagnosaKeperawatan_id',
                                            $formIntervensiImplementasi['formDiagnosaKeperawatan_id'],
                                        );
                                    }
                                @endphp

                                <x-input-label :value="__('Diagnosa Terkait')" />
                                <x-text-input class="w-full mt-1"
                                    value="{{ $selectedDiagnosa['diagnosaKeperawatan'] ?? '-' }}" disabled />
                            </div>


                            <!-- Intervensi -->
                            <div class="md:col-span-2">
                                <x-input-label for="formIntervensiImplementasi.intervensi" :value="__('Intervensi')" />
                                <x-text-input-area rows="3" class="w-full mt-1"
                                    wire:model="formIntervensiImplementasi.intervensi"
                                    placeholder="Rencana / intervensi keperawatan..."></x-text-input-area>
                                @error('formIntervensiImplementasi.intervensi')
                                    <x-input-error :messages="$message" />
                                @enderror
                            </div>

                            <!-- Implementasi -->
                            <div class="md:col-span-2">
                                <x-input-label for="formIntervensiImplementasi.implementasi" :value="__('Implementasi')" />
                                <x-text-input-area rows="3" class="w-full mt-1"
                                    wire:model="formIntervensiImplementasi.implementasi"
                                    placeholder="Tindakan yang sudah dilakukan..."></x-text-input-area>
                                @error('formIntervensiImplementasi.implementasi')
                                    <x-input-error :messages="$message" />
                                @enderror
                            </div>
                        </div>

                        <!-- TANDA TANGAN PETUGAS FORM INTERVENSI -->
                        {{-- <div class="p-4 mt-6 border rounded-lg bg-gray-50">
                            <h4 class="mb-3 font-semibold">Tanda Tangan Petugas</h4>
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <x-input-label :value="__('Nama Petugas')" />
                                    <x-text-input value="{{ auth()->user()->myuser_name ?? '' }}"
                                        class="w-full mt-1" disabled />
                                </div>
                                <div>
                                    <x-input-label :value="__('Kode Petugas')" />
                                    <x-text-input value="{{ auth()->user()->myuser_code ?? '' }}"
                                        class="w-full mt-1" disabled />
                                </div>
                                <div class="md:col-span-2">
                                    <x-input-label :value="__('Jabatan')" />
                                    <x-text-input value="Perawat" class="w-full mt-1" disabled />
                                </div>
                            </div>
                        </div> --}}

                        <!-- TOMBOL SIMPAN FORM B -->
                        <div class="flex justify-end mt-6">
                            <x-primary-button type="submit" class="px-6 py-2">
                                Simpan Intervensi / Implementasi
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>



        </div>




    </div>

</div>
