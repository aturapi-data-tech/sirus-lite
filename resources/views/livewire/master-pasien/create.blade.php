@php
    $disabledProperty = $isOpenMode === 'tampil' ? true : false;
@endphp

<div class="fixed inset-0 z-40 ease-out duration-400">

    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">

        <!-- This element is to trick the browser into transition-opacity. -->
        <div class="fixed inset-0 transition-opacity">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>



        <!-- This element is to trick the browser into centering the modal contents. -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>​


        <div class="inline-block overflow-auto text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:max-h-[35rem] sm:my-8 sm:align-middle sm:w-11/12"
            role="dialog" aria-modal="true" aria-labelledby="modal-headline">

            <div
                class="sticky top-0 flex items-start justify-between p-4 bg-opacity-75 border-b rounded-t bg-primary dark:border-gray-600">

                <h3 class="text-2xl font-semibold text-white dark:text-white">
                    {{ $myTitle }}
                </h3>

                {{-- Close Modal --}}
                <button wire:click="closeModal()"
                    class="text-gray-400 bg-gray-50 hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd"></path>
                    </svg>
                </button>

            </div>

            <form>

                <div class="px-4 bg-white ">
                    <div class="absolute right-4 top-16">
                        <x-check-box :valueunchecked="__('0')" :valuechecked="__('1')" :label="__('Pasien Tidak Dikenal')" />
                    </div>

                    <x-border-form :title="__('Identitas Pasien')" :align="__('start')">
                        <div>
                            <x-input-label for="regName" :value="__('Nama Pasien')" :required=true />
                            <div class="flex items-center mb-2">
                                <x-text-input placeholder="Gelar depan" class="mt-1 ml-2 sm:w-1/4"
                                    :disabled=$disabledProperty wire:model="dataPasien.pasien.gelarDepan" />
                                <x-text-input id="regName" placeholder="Nama" class="mt-1 ml-2 sm:w-3/4"
                                    :disabled=$disabledProperty wire:model="dataPasien.pasien.regName" />
                                <x-text-input placeholder="Gelar Belakang" class="mt-1 ml-2 sm:w-1/4"
                                    :disabled=$disabledProperty wire:model="dataPasien.pasien.gelarBelakang" />
                                <span> {{ ' / ' }}</span>
                                <x-text-input placeholder="Nama Panggilan" class="mt-1 ml-2 sm:w-1/4"
                                    :disabled=$disabledProperty wire:model="dataPasien.pasien.namaPanggilan" />
                            </div>
                            {{-- Error Message Start --}}
                            <div class="flex items-center mb-2">
                                <div class="mt-1 ml-2 truncate sm:w-1/4">
                                    @error('dataPasien.pasien.gelarDepan')
                                        <x-input-error :messages=$message />
                                    @enderror
                                </div>
                                <div class="mt-1 ml-2 truncate sm:w-3/4">
                                    @error('dataPasien.pasien.regName')
                                        <x-input-error :messages=$message />
                                    @enderror
                                </div>
                                <div class="mt-1 ml-2 truncate sm:w-1/4">
                                    @error('dataPasien.pasien.gelarBelakang')
                                        <x-input-error :messages=$message />
                                    @enderror
                                </div>
                                <div class="mt-1 ml-2 truncate sm:w-1/4">
                                    @error('dataPasien.pasien.namaPanggilan')
                                        <x-input-error :messages=$message />
                                    @enderror
                                </div>
                            </div>
                            {{-- Error Message End --}}
                        </div>

                        <div>
                            <x-input-label for="tempatLahir" :value="__('Tempat Tanggal Lahir')" :required=false />
                            <div class="flex items-center mb-2">
                                <x-text-input id="tempatLahir" placeholder="Tempat Lahir" class="mt-1 ml-2 sm:w-1/4"
                                    :disabled=$disabledProperty wire:model="dataPasien.pasien.tempatLahir" />
                                <x-text-input placeholder="Tgl Lahir [dd/mm/yyyy]" class="mt-1 ml-2 sm:w-3/4"
                                    :disabled=$disabledProperty wire:model="dataPasien.pasien.tglLahir" />

                                <span> {{ ' / ' }}</span>
                                <x-text-input placeholder="Umur Thn" class="mt-1 ml-2 sm:w-1/4"
                                    :disabled=$disabledProperty wire:model="dataPasien.pasien.thn" />
                                <x-text-input placeholder="Umur Bln" class="mt-1 ml-2 sm:w-1/4"
                                    :disabled=$disabledProperty wire:model="dataPasien.pasien.bln" />
                                <x-text-input placeholder="Umur Hari" class="mt-1 ml-2 sm:w-1/4"
                                    :disabled=$disabledProperty wire:model="dataPasien.pasien.hari" />
                            </div>
                            {{-- Error Message Start --}}
                            <div class="flex items-center mb-2">
                                <div class="mt-1 ml-2 truncate sm:w-1/4">
                                    @error('dataPasien.pasien.tempatLahir')
                                        <x-input-error :messages=$message />
                                    @enderror
                                </div>
                                <div class="mt-1 ml-2 truncate sm:w-3/4">
                                    @error('dataPasien.pasien.tglLahir')
                                        <x-input-error :messages=$message />
                                    @enderror
                                </div>
                                <div class="mt-1 ml-2 truncate sm:w-1/4">
                                    @error('dataPasien.pasien.thn')
                                        <x-input-error :messages=$message />
                                    @enderror
                                </div>
                                <div class="mt-1 ml-2 truncate sm:w-1/4">
                                    @error('dataPasien.pasien.bln')
                                        <x-input-error :messages=$message />
                                    @enderror
                                </div>
                                <div class="mt-1 ml-2 truncate sm:w-1/4">
                                    @error('dataPasien.pasien.hari')
                                        <x-input-error :messages=$message />
                                    @enderror
                                </div>
                            </div>
                            {{-- Error Message End --}}
                        </div>

                        <x-border-form :title="__('Sosial')" :align="__('start')" :bgcolor="__('bg-gray-50')">
                            <div class="flex items-center">
                                <x-input-label for="JenisKelamin" :value="__('Jenis Kelamin')" :required=false class="sm:w-1/5" />
                                <x-input-label for="Agama" :value="__('Agama')" :required=false class="sm:w-1/5" />
                                <x-input-label for="SPerkawinan" :value="__('Status Perkawinan')" :required=false class="sm:w-1/5" />
                                <x-input-label for="Pendidikan" :value="__('Pendidikan')" :required=false class="sm:w-1/5" />
                                <x-input-label for="Pekerjaan" :value="__('Pekerjaan')" :required=false class="sm:w-1/5" />
                            </div>

                            <div class="flex items-center mb-2">
                                <x-text-input id="JenisKelamin" placeholder="Jenis Kelamin" class="mt-1 ml-2 sm:w-1/5"
                                    :disabled=$disabledProperty
                                    wire:model="dataPasien.pasien.jenisKelamin.jenisKelaminId" />
                                <x-text-input id="Agama" placeholder="Agama" class="mt-1 ml-2 sm:w-1/5"
                                    :disabled=$disabledProperty wire:model="dataPasien.pasien.agama.agamaId" />
                                <x-text-input id="SPerkawinan" placeholder="Status Perkawinan"
                                    class="mt-1 ml-2 sm:w-1/5" :disabled=$disabledProperty
                                    wire:model="dataPasien.pasien.statusPerkawinan.statusPerkawinanId" />
                                <x-text-input id="Pendidikan" placeholder="Pendidikan" class="mt-1 ml-2 sm:w-1/5"
                                    :disabled=$disabledProperty
                                    wire:model="dataPasien.pasien.pendidikan.pendidikanId" />
                                <x-text-input id="Pekerjaan" placeholder="Pekerjaan" class="mt-1 ml-2 sm:w-1/5"
                                    :disabled=$disabledProperty wire:model="dataPasien.pasien.pekerjaan.pekerjaanId" />
                            </div>
                            {{-- Error Message Start --}}
                            <div class="flex items-center mb-2">
                                <div class="mt-1 ml-2 truncate sm:w-1/5">
                                    @error('dataPasien.pasien.jenisKelamin.jenisKelaminId')
                                        <x-input-error :messages=$message />
                                    @enderror
                                </div>
                                <div class="mt-1 ml-2 truncate sm:w-1/5">
                                    @error('dataPasien.pasien.agama.agamaId')
                                        <x-input-error :messages=$message />
                                    @enderror
                                </div>
                                <div class="mt-1 ml-2 truncate sm:w-1/5">
                                    @error('dataPasien.pasien.statusPerkawinan.statusPerkawinanId')
                                        <x-input-error :messages=$message />
                                    @enderror
                                </div>
                                <div class="mt-1 ml-2 truncate sm:w-1/5">
                                    @error('dataPasien.pasien.pendidikan.pendidikanId')
                                        <x-input-error :messages=$message />
                                    @enderror
                                </div>
                                <div class="mt-1 ml-2 truncate sm:w-1/5">
                                    @error('dataPasien.pasien.pekerjaan.pekerjaanId')
                                        <x-input-error :messages=$message />
                                    @enderror
                                </div>
                            </div>
                            {{-- Error Message End --}}
                        </x-border-form>


                        <x-border-form :title="__('Budaya')" :align="__('start')" :bgcolor="__('bg-gray-50')">
                            <div>
                                <div class="flex items-center mb-2">
                                    <x-input-label for="GolonganDarah" :value="__('Golongan Darah')" :required=false
                                        class="sm:w-1/4" />
                                    <x-input-label for="Kewarganegaraan" :value="__('Kewarganegaraan')" :required=true
                                        class="sm:w-3/4" />
                                    <x-input-label for="Suku" :value="__('Suku')" :required=true
                                        class="sm:w-1/4" />
                                    <x-input-label for="Bahasa" :value="__('Bahasa')" :required=true
                                        class="sm:w-1/4" />
                                    <x-input-label for="Status" :value="__('Status')" :required=true
                                        class="sm:w-1/4" />
                                </div>
                                <div class="flex items-center mb-2">
                                    <x-text-input id="GolonganDarah" placeholder="Golongan Darah"
                                        class="mt-1 ml-2 sm:w-1/4" :disabled=$disabledProperty
                                        wire:model="dataPasien.pasien.golonganDarah.golonganDarahId" />
                                    <x-text-input id="Kewarganegaraan" placeholder="Kewarganegaraan"
                                        class="mt-1 ml-2 sm:w-3/4" :disabled=$disabledProperty
                                        wire:model="dataPasien.pasien.kewarganegaraan" />
                                    <x-text-input id="Suku" placeholder="Suku" class="mt-1 ml-2 sm:w-1/4"
                                        :disabled=$disabledProperty wire:model="dataPasien.pasien.suku" />
                                    <x-text-input id="Bahasa" placeholder="Bahasa yang digunakan"
                                        class="mt-1 ml-2 sm:w-1/4" :disabled=$disabledProperty
                                        wire:model="dataPasien.pasien.bahasa" />
                                    <x-text-input id="Status" placeholder="Status" class="mt-1 ml-2 sm:w-1/4"
                                        :disabled=$disabledProperty wire:model="dataPasien.pasien.status.statusId" />
                                </div>
                                {{-- Error Message Start --}}
                                <div class="flex items-center mb-2">
                                    <div class="mt-1 ml-2 truncate sm:w-1/4">
                                        @error('dataPasien.pasien.golonganDarah.golonganDarahId')
                                            <x-input-error :messages=$message />
                                        @enderror
                                    </div>
                                    <div class="mt-1 ml-2 truncate sm:w-3/4">
                                        @error('dataPasien.pasien.kewarganegaraan.kewarganegaraanId')
                                            <x-input-error :messages=$message />
                                        @enderror
                                    </div>
                                    <div class="mt-1 ml-2 truncate sm:w-1/4">
                                        @error('dataPasien.pasien.suku')
                                            <x-input-error :messages=$message />
                                        @enderror
                                    </div>
                                    <div class="mt-1 ml-2 truncate sm:w-1/4">
                                        @error('dataPasien.pasien.bahasa')
                                            <x-input-error :messages=$message />
                                        @enderror
                                    </div>
                                    <div class="mt-1 ml-2 truncate sm:w-1/4">
                                        @error('dataPasien.pasien.status.statusId')
                                            <x-input-error :messages=$message />
                                        @enderror
                                    </div>
                                </div>
                                {{-- Error Message End --}}
                            </div>
                        </x-border-form>
                    </x-border-form>

                    <div id="AlamatIdentitas" class="inline-flex">
                        <x-border-form :title="__('Alamat Domisil')" :align="__('start')" :bgcolor="__('bg-red-50')" class="mr-2">
                            <div>
                                <x-input-label for="AlamatDomisil" :value="__('Alamat')" :required=false />
                                <div class="flex items-center mb-2">
                                    <x-text-input id="AlamatDomisil" placeholder="Alamat" class="mt-1 ml-2"
                                        :disabled=$disabledProperty wire:model="dataPasien.pasien.domisil.alamat" />
                                </div>
                                @error('dataPasien.pasien.domisil.alamat')
                                    <x-input-error :messages=$message />
                                @enderror
                            </div>
                            <div>
                                <x-input-label for="rtrwDomisil" :value="__('RT/RW')" :required=false />
                                <div class="flex items-center mb-2">
                                    <x-text-input id="rtrwDomisil" placeholder="RT" class="mt-1 ml-2 sm:w-1/3"
                                        :disabled=$disabledProperty wire:model="dataPasien.pasien.domisil.rt" />
                                    <x-text-input placeholder="RW" class="mt-1 ml-2 sm:w-1/3"
                                        :disabled=$disabledProperty wire:model="dataPasien.pasien.domisil.rw" />
                                    <x-text-input placeholder="Kode Pos" class="mt-1 ml-2 sm:w-1/3"
                                        :disabled=$disabledProperty wire:model="dataPasien.pasien.domisil.kodepos" />
                                </div>
                                {{-- Error Message Start --}}
                                <div class="flex items-center mb-2">
                                    <div class="mt-1 ml-2 truncate sm:w-1/3">
                                        @error('dataPasien.pasien.domisil.rt')
                                            <x-input-error :messages=$message />
                                        @enderror
                                    </div>
                                    <div class="mt-1 ml-2 truncate sm:w-1/3">
                                        @error('dataPasien.pasien.domisil.rw')
                                            <x-input-error :messages=$message />
                                        @enderror
                                    </div>
                                    <div class="mt-1 ml-2 truncate sm:w-1/3">
                                        @error('dataPasien.pasien.domisil.kodepos')
                                            <x-input-error :messages=$message />
                                        @enderror
                                    </div>

                                </div>
                                {{-- Error Message End --}}
                            </div>
                            <div>
                                <x-input-label for="DesaDomisil" :value="__('Desa')" :required=false />
                                <div class="flex items-center mb-2">
                                    <x-text-input id="DesaDomisil" placeholder="Desa" class="mt-1 ml-2"
                                        :disabled=$disabledProperty wire:model="dataPasien.pasien.domisil.desa" />
                                </div>
                                @error('dataPasien.pasien.domisil.desa')
                                    <x-input-error :messages=$message />
                                @enderror
                            </div>
                            <div>
                                <x-input-label for="KecamatanDomisil" :value="__('Kecamatan')" :required=false />
                                <div class="flex items-center mb-2">
                                    <x-text-input id="KecamatanDomisil" placeholder="Kecamatan" class="mt-1 ml-2"
                                        :disabled=$disabledProperty wire:model="dataPasien.pasien.domisil.kecamatan" />
                                </div>
                                @error('dataPasien.pasien.domisil.kecamatan')
                                    <x-input-error :messages=$message />
                                @enderror
                            </div>
                            <div>
                                <x-input-label for="KotaDomisil" :value="__('Kota')" :required=false />
                                <div class="flex items-center mb-2">
                                    <x-text-input placeholder="Kota" class="mt-1 ml-2" :disabled=$disabledProperty
                                        wire:model="dataPasien.pasien.domisil.kota" />
                                </div>
                                @error('dataPasien.pasien.domisil.kota')
                                    <x-input-error :messages=$message />
                                @enderror
                            </div>
                            <div>
                                <x-input-label for="PropinsiDomisil" :value="__('Propinsi')" :required=false />
                                <div class="flex items-center mb-2">
                                    <x-text-input id="PropinsiDomisil" placeholder="Propinsi" class="mt-1 ml-2"
                                        :disabled=$disabledProperty wire:model="dataPasien.pasien.domisil.propinsi" />
                                </div>
                                @error('dataPasien.pasien.domisil.propinsi')
                                    <x-input-error :messages=$message />
                                @enderror
                            </div>
                        </x-border-form>

                        <x-border-form :title="__('Identitas')" :align="__('start')" :bgcolor="__('bg-gray-50')" class="">
                            <div class="flex justify-end">
                                <x-check-box :valueunchecked="__('0')" :valuechecked="__('1')" :label="__('Sama dgn Domisil')" />
                            </div>
                            <div class="grid grid-cols-2">
                                {{-- grid 1 Identitas --}}
                                <div>
                                    <div>
                                        <x-input-label for="nikidentitas" :value="__('NIK')" :required=false />
                                        <div class="flex items-center mb-2">
                                            <x-text-input id="nikidentitas" placeholder="NIK" class="mt-1 ml-2"
                                                :disabled=$disabledProperty
                                                wire:model="dataPasien.pasien.identitas.nik" />
                                        </div>
                                        @error('dataPasien.pasien.identitas.nik')
                                            <x-input-error :messages=$message />
                                        @enderror
                                    </div>
                                    <div>
                                        <x-input-label for="idBpjsidentitas" :value="__('Id BPJS')" :required=false />
                                        <div class="flex items-center mb-2">
                                            <x-text-input id="idBpjsidentitas" placeholder="Id BPJS"
                                                class="mt-1 ml-2" :disabled=$disabledProperty
                                                wire:model="dataPasien.pasien.identitas.idbpjs" />
                                        </div>
                                        @error('dataPasien.pasien.identitas.idbpjs')
                                            <x-input-error :messages=$message />
                                        @enderror
                                    </div>
                                    <div>
                                        <x-input-label for="pasporidentitas" :value="__('Paspor')" :required=false />
                                        <div class="flex items-center mb-2">
                                            <x-text-input id="pasporidentitas" placeholder="Paspor [untuk WNA / WNI]"
                                                class="mt-1 ml-2" :disabled=$disabledProperty
                                                wire:model="dataPasien.pasien.identitas.paspor" />
                                        </div>
                                        @error('dataPasien.pasien.identitas.paspor')
                                            <x-input-error :messages=$message />
                                        @enderror
                                    </div>
                                    <div>
                                        <x-input-label for="Alamatidentitas" :value="__('Alamat')" :required=false />
                                        <div class="flex items-center mb-2">
                                            <x-text-input id="Alamatidentitas" placeholder="Alamat" class="mt-1 ml-2"
                                                :disabled=$disabledProperty
                                                wire:model="dataPasien.pasien.identitas.alamat" />
                                        </div>
                                        @error('dataPasien.pasien.identitas.alamat')
                                            <x-input-error :messages=$message />
                                        @enderror
                                    </div>
                                    <div>
                                        <x-input-label for="rtrwidentitas" :value="__('RT/RW')" :required=false />
                                        <div class="flex items-center mb-2">
                                            <x-text-input id="rtrwidentitas" placeholder="RT"
                                                class="mt-1 ml-2 sm:w-1/3" :disabled=$disabledProperty
                                                wire:model="dataPasien.pasien.identitas.rt" />
                                            <x-text-input placeholder="RW" class="mt-1 ml-2 sm:w-1/3"
                                                :disabled=$disabledProperty
                                                wire:model="dataPasien.pasien.identitas.rw" />
                                            <x-text-input placeholder="Kode Pos" class="mt-1 ml-2 sm:w-1/3"
                                                :disabled=$disabledProperty
                                                wire:model="dataPasien.pasien.identitas.kodepos" />
                                        </div>
                                        {{-- Error Message Start --}}
                                        <div class="flex items-center mb-2">
                                            <div class="mt-1 ml-2 truncate sm:w-1/3">
                                                @error('dataPasien.pasien.identitas.rt')
                                                    <x-input-error :messages=$message />
                                                @enderror
                                            </div>
                                            <div class="mt-1 ml-2 truncate sm:w-1/3">
                                                @error('dataPasien.pasien.identitas.rw')
                                                    <x-input-error :messages=$message />
                                                @enderror
                                            </div>
                                            <div class="mt-1 ml-2 truncate sm:w-1/3">
                                                @error('dataPasien.pasien.identitas.kodepos')
                                                    <x-input-error :messages=$message />
                                                @enderror
                                            </div>

                                        </div>
                                        {{-- Error Message End --}}
                                    </div>
                                </div>
                                {{-- grid 2 Identitas --}}
                                <div>
                                    <div>
                                        <x-input-label for="Desaidentitas" :value="__('Desa')" :required=false />
                                        <div class="flex items-center mb-2">
                                            <x-text-input id="Desaidentitas" placeholder="Desa" class="mt-1 ml-2"
                                                :disabled=$disabledProperty
                                                wire:model="dataPasien.pasien.identitas.desa" />
                                        </div>
                                        @error('dataPasien.pasien.identitas.desa')
                                            <x-input-error :messages=$message />
                                        @enderror
                                    </div>
                                    <div>
                                        <x-input-label for="Kecamatanidentitas" :value="__('Kecamatan')" :required=false />
                                        <div class="flex items-center mb-2">
                                            <x-text-input id="Kecamatanidentitas" placeholder="Kecamatan"
                                                class="mt-1 ml-2" :disabled=$disabledProperty
                                                wire:model="dataPasien.pasien.identitas.kecamatan" />
                                        </div>
                                        @error('dataPasien.pasien.identitas.kecamatan')
                                            <x-input-error :messages=$message />
                                        @enderror
                                    </div>
                                    <div>
                                        <x-input-label for="Kotaidentitas" :value="__('Kota')" :required=false />
                                        <div class="flex items-center mb-2">
                                            <x-text-input placeholder="Kota" class="mt-1 ml-2"
                                                :disabled=$disabledProperty
                                                wire:model="dataPasien.pasien.identitas.kota" />
                                        </div>
                                        @error('dataPasien.pasien.identitas.kota')
                                            <x-input-error :messages=$message />
                                        @enderror
                                    </div>
                                    <div>
                                        <x-input-label for="Propinsiidentitas" :value="__('Propinsi')" :required=false />
                                        <div class="flex items-center mb-2">
                                            <x-text-input id="Propinsiidentitas" placeholder="Propinsi"
                                                class="mt-1 ml-2" :disabled=$disabledProperty
                                                wire:model="dataPasien.pasien.identitas.propinsi" />
                                        </div>
                                        @error('dataPasien.pasien.identitas.propinsi')
                                            <x-input-error :messages=$message />
                                        @enderror
                                    </div>
                                    <div>
                                        <x-input-label for="negaraidentitas" :value="__('Negara')" :required=false />
                                        <div class="flex items-center mb-2">
                                            <x-text-input id="negaraidentitas" placeholder="Negara [isi dgn ID]"
                                                class="mt-1 ml-2" :disabled=$disabledProperty
                                                wire:model="dataPasien.pasien.identitas.negara" />
                                        </div>
                                        @error('dataPasien.pasien.identitas.negara')
                                            <x-input-error :messages=$message />
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </x-border-form>
                    </div>

                    <x-border-form :title="__('Kontak')" :align="__('start')">
                        <div>
                            <x-input-label for="province_id" :value="__('No HP')" :required=false />
                            <div class="flex items-center mb-2">
                                <x-text-input placeholder="No HP" class="mt-1 ml-2" :disabled=$disabledProperty
                                    wire:model="name" />
                            </div>
                            @error('province_id')
                                <x-input-error :messages=$message />
                            @enderror
                        </div>
                    </x-border-form>

                    <x-border-form :title="__('Keluarga')" :align="__('start')">
                        <div>
                            <x-input-label for="province_id" :value="__('Nama Ayah')" :required=false />
                            <div class="flex items-center mb-2">
                                <x-text-input placeholder="Nama Ayah" class="mt-1 ml-2" :disabled=$disabledProperty
                                    wire:model="name" />
                                <x-text-input placeholder="No Telp. Ayah" class="mt-1 ml-2"
                                    :disabled=$disabledProperty wire:model="name" />
                            </div>
                            @error('province_id')
                                <x-input-error :messages=$message />
                            @enderror
                        </div>
                        <div>
                            <x-input-label for="province_id" :value="__('Nama Ibu')" :required=false />
                            <div class="flex items-center mb-2">
                                <x-text-input placeholder="Nama Ibu" class="mt-1 ml-2" :disabled=$disabledProperty
                                    wire:model="name" />
                                <x-text-input placeholder="No Telp. Ibu" class="mt-1 ml-2" :disabled=$disabledProperty
                                    wire:model="name" />
                            </div>
                            @error('province_id')
                                <x-input-error :messages=$message />
                            @enderror
                        </div>
                    </x-border-form>

                </div>


                <div class="sticky bottom-0 px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                    @if ($isOpenMode !== 'tampil')
                        <x-green-button wire:click.prevent="store()" type="button">Simpan</x-green-button>
                    @endif
                    <x-light-button wire:click="closeModal()" type="button">Keluar</x-light-button>
                </div>


            </form>

        </div>



    </div>

</div>
