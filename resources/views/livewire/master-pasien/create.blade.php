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

        {{-- Modals --}}
        <div class="inline-block overflow-auto text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:max-h-[35rem] sm:my-8 sm:align-middle sm:w-11/12"
            role="dialog" aria-modal="true" aria-labelledby="modal-headline">

            <div
                class="sticky top-0 flex items-start justify-between p-4 bg-white border-b rounded-t dark:border-gray-600">

                <h3 class="text-2xl font-semibold text-gray-900 dark:text-white">
                    {{ $myTitle }}
                </h3>

                {{-- Close Modal --}}
                <button wire:click="closeModal()"
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd"></path>
                    </svg>
                </button>

            </div>


            <form>

                <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">



                    <div>

                        <div class="mb-1">
                            {{-- Pembungkus --}}
                            <div class="form-baris-1">
                                <div class="grid gap-6 md:grid-cols-2">
                                    {{-- Form Kanan --}}
                                    <div class="form-kiri">
                                        {{-- Reg. NO --}}
                                        <div class="mb-2">
                                            <x-input-label for="reg_no" class="ml-1" :value="__('No. Registrasi  :')" />
                                            <x-text-input class="block mr-1" :value="old('reg_no')" autofocus
                                                autocomplete="reg_no" :disabled=$disabledProperty wire:model="reg_no"
                                                placeholder="Masukkan No. Registrasi" tabindex="1" />
                                            @error('reg_no')
                                                <span class="text-red-500">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        {{-- Reg. Name --}}
                                        <div class="mb-2">
                                            <x-input-label for="reg_name" class="ml-1" :value="__('Nama :')" />
                                            <x-text-input class="block" :value="old('reg_name')" required autofocus
                                                autocomplete="reg_name" :disabled=$disabledProperty
                                                wire:model="reg_name" placeholder="Masukkan Nama" tabindex="2" />
                                            @error('reg_name')
                                                <span class="text-red-500">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        {{-- JKN --}}
                                        <div class="mb-2">
                                            <x-input-label for="nokartu_bpjs" class="ml-1" :value="__('JKN :')" />
                                            <x-text-input class="block" :value="old('nokartu_bpjs')" required autofocus
                                                autocomplete="nokartu_bpjs" :disabled=$disabledProperty
                                                wire:model="nokartu_bpjs" placeholder="Masukkan No. JKN"
                                                tabindex="3" />
                                            @error('nokartu_bpjs')
                                                <span class="text-red-500">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        {{-- NIK --}}
                                        <div class="mb-2">
                                            <x-input-label for="nik_bpjs" class="ml-1" :value="__('NIK :')" />
                                            <x-text-input class="block" :value="old('nik_bpjs')" required autofocus
                                                autocomplete="nik_bpjs" :disabled=$disabledProperty
                                                wire:model="nik_bpjs" placeholder="Masukkan NIK" tabindex="4" />
                                            @error('nik_bpjs')
                                                <span class="text-red-500">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        {{-- Jenis Kelamin --}}
                                        <div class="mb-2">
                                            <x-input-label for="sex" class="ml-1" :value="__('Jenis Kelamin :')" />
                                            <div class="grid gap-2 md:grid-cols-2">
                                                <div
                                                    class="flex items-center py-2 pl-4 border border-gray-300 rounded-lg dark:border-gray-700">
                                                    <input id="sexL" type="radio" wire:model='sex' tabindex="5"
                                                        value="L"
                                                        class="w-4 h-4 text-blue-600 bg-gray-100 border border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                                    <label for="sexL"
                                                        class="w-full pb-1 ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Laki-laki</label>
                                                </div>
                                                <div
                                                    class="flex items-center py-2 pl-4 border border-gray-300 rounded-lg dark:border-gray-700">
                                                    <input id="sexP" type="radio" value="P" wire:model='sex'
                                                        tabindex="6"
                                                        class="w-4 h-4 text-blue-600 bg-gray-100 border border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                                    <label for="sexP"
                                                        class="w-full pb-1 ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Perempuan</label>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Usia --}}
                                        <div class="grid gap-3 md:grid-cols-3">
                                            <div class="mb-2">
                                                <div class="flex">
                                                    <x-input-label class="ml-1" :value="__('Usia:')" />
                                                    <x-input-label for="thn" class="ml-20" :value="__('Tahun')" />
                                                </div>
                                                <x-text-input class="block" :value="old('thn')" required autofocus
                                                    autocomplete="thn" :disabled=$disabledProperty wire:model="thn"
                                                    placeholder="Tahun" tabindex="7" />
                                                @error('thn')
                                                    <span class="text-red-500">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="mb-2">
                                                <x-input-label for="bln" class="flex justify-end mr-2"
                                                    :value="__('Bulan')" />
                                                <x-text-input class="block" :value="old('bln')" required autofocus
                                                    autocomplete="bln" :disabled=$disabledProperty wire:model="bln"
                                                    placeholder="Bulan" tabindex="8" />
                                                @error('bln')
                                                    <span class="text-red-500">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="mb-2">
                                                <x-input-label for="hari" class="flex justify-end mr-2"
                                                    :value="__('Hari')" />
                                                <x-text-input class="block" :value="old('hari')" required autofocus
                                                    autocomplete="hari" :disabled=$disabledProperty wire:model="hari"
                                                    placeholder="Hari" tabindex="9" />
                                                @error('hari')
                                                    <span class="text-red-500">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Tgl. Lahir --}}
                                        <div class="mb-2">
                                            <x-input-label for="birth_date" class="ml-1" :value="__('Tgl. Lahir :')" />
                                            <x-text-input class="block" :value="old('birth_date')" required autofocus
                                                autocomplete="birth_date" :disabled=$disabledProperty tabindex="10"
                                                wire:model="birth_date" />
                                            @error('birth_date')
                                                <span class="text-red-500">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        {{-- Tempat Lahir --}}
                                        <div class="mb-2">
                                            <x-input-label for="birth_place" class="ml-1" :value="__('Tempat Lahir :')" />
                                            <x-text-input class="block" :value="old('birth_place')" required autofocus
                                                autocomplete="birth_place" :disabled=$disabledProperty tabindex="11"
                                                wire:model="birth_place" placeholder="Masukkan Tempat Lahir" />
                                            @error('birth_place')
                                                <span class="text-red-500">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        {{-- Gol. Darah --}}
                                        <div class="grid gap-6 md:grid-cols-2">
                                            {{-- Gol. Darah --}}
                                            <div class="mb-2">
                                                <x-input-label for="blood" class="ml-1" :value="__('Gol. Darah :')" />
                                                <x-text-input class="block" :value="old('blood')" required autofocus
                                                    placeholder="Gol. Darah" autocomplete="blood" tabindex="12"
                                                    :disabled=$disabledProperty wire:model="blood" />
                                                @error('blood')
                                                    <span class="text-red-500">{{ $message }}</span>
                                                @enderror
                                            </div>


                                            {{-- Marital Status --}}
                                            <div class="mb-2">
                                                <x-input-label for="marital_status" class="ml-1"
                                                    :value="__('Status :')" />
                                                <x-text-input class="block" :value="old('marital_status')" required autofocus
                                                    autocomplete="marital_status" :disabled=$disabledProperty
                                                    tabindex="13" placeholder="Status"
                                                    wire:model="marital_status" />
                                                @error('marital_status')
                                                    <span class="text-red-500">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Kepala Keluarga --}}
                                        <div class="mb-2">
                                            <x-input-label for="kk" class="ml-1" :value="__('Kepala Keluarga :')" />
                                            <x-text-input class="block" :value="old('kk')" required autofocus
                                                placeholder="Masukkan Nama Kepala Keluarga" autocomplete="kk"
                                                tabindex="14" :disabled=$disabledProperty wire:model="kk" />
                                            @error('kk')
                                                <span class="text-red-500">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        {{-- Nyonya --}}
                                        <div class="mb-2">
                                            <x-input-label for="nyonya" class="ml-1" :value="__('Ibu Keluarga :')" />
                                            <x-text-input class="block" :value="old('nyonya')" required autofocus
                                                tabindex="15" autocomplete="nyonya" :disabled=$disabledProperty
                                                wire:model="nyonya" placeholder="Masukkan Nama Ibu Keluarga" />
                                            @error('nyonya')
                                                <span class="text-red-500">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        {{-- No. KK --}}
                                        <div class="mb-2">
                                            <x-input-label for="no_kk" class="ml-1" :value="__('No. KK :')" />
                                            <x-text-input class="block" :value="old('no_kk')" required autofocus
                                                tabindex="16" autocomplete="no_kk" :disabled=$disabledProperty
                                                wire:model="no_kk" placeholder="Masukkan No. KK" />
                                            @error('no_kk')
                                                <span class="text-red-500">{{ $message }}</span>
                                            @enderror
                                        </div>

                                    </div>
                                    {{-- End Form Kiri --}}

                                    {{-- Form Kanan --}}
                                    <div class="form-kanan">
                                        {{-- Alamat --}}
                                        <div class="mb-2">
                                            <x-input-label for="reg_no" class="ml-1" :value="__('Alamat  :')" />
                                            <textarea wire:model='address' style="height: 119px;" tabindex="17"
                                                class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                                placeholder="Masukkan Alamat.."></textarea>
                                            @error('address')
                                                <span class="text-red-500">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        {{-- Desa --}}
                                        <div class="mb-2">
                                            <x-input-label for="des_id" class="ml-1" :value="__('Desa :')" />
                                            <div class="flex">
                                                <x-text-input class="block sm:w-[100px]" :value="old('des_id')" required
                                                    tabindex="18" autofocus autocomplete="des_id"
                                                    :disabled=$disabledProperty wire:model="des_id"
                                                    placeholder="Kode Desa" />
                                                <x-text-input class="block ml-2" :value="old('des_name')" required autofocus
                                                    autocomplete="des_name" :disabled=true wire:model="des_name"
                                                    placeholder="Nama Desa" />
                                            </div>
                                            @error('des_id')
                                                <span class="text-red-500">{{ $message }}</span>
                                            @enderror

                                            {{-- LOV --}}
                                            <div class="mt-2">
                                                @include('livewire.master-pasien.list-of-value-desa')
                                            </div>
                                        </div>

                                        {{-- RT / RW --}}
                                        <div class="grid gap-6 md:grid-cols-2">
                                            <div class="mb-2">
                                                <x-input-label for="rt" class="ml-1" :value="__('RT :')" />
                                                <x-text-input class="block" :value="old('rt')" required autofocus
                                                    autocomplete="rt" :disabled=$disabledProperty wire:model="rt"
                                                    placeholder="RT" tabindex="19" />
                                                @error('rt')
                                                    <span class="text-red-500">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="mb-2">
                                                <x-input-label for="rw" class="ml-1" :value="__('RW :')" />
                                                <x-text-input class="block" :value="old('rw')" required autofocus
                                                    autocomplete="rw" :disabled=$disabledProperty wire:model="rw"
                                                    placeholder="RW" tabindex="20" />
                                                @error('rw')
                                                    <span class="text-red-500">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Kecamatan --}}
                                        <div class="mb-2">
                                            <x-input-label for="kec_id" class="ml-1" :value="__('Kecamatan :')" />
                                            <div class="flex">
                                                <x-text-input class="block sm:w-[100px]" :value="old('kec_id')" required
                                                    autofocus autocomplete="kec_id" :disabled=$disabledProperty
                                                    tabindex="21" wire:model="kec_id"
                                                    placeholder="Kode Kecamatan" />
                                                <x-text-input class="block ml-2" :value="old('kec_name')" required autofocus
                                                    autocomplete="kec_name" :disabled=true wire:model="kec_name"
                                                    placeholder="Nama Kecamatan" />
                                            </div>
                                            @error('kec_id')
                                                <span class="text-red-500">{{ $message }}</span>
                                            @enderror

                                            {{-- LOV --}}
                                            <div class="mt-2">
                                                @include('livewire.master-pasien.list-of-value-kecamatan')
                                            </div>
                                        </div>

                                        {{-- Kabupaten --}}
                                        <div class="mb-2">
                                            <x-input-label for="kab_id" class="ml-1" :value="__('Kabupaten :')" />
                                            <div class="flex">
                                                <x-text-input class="block sm:w-[100px]" :value="old('kab_id')" required
                                                    autofocus autocomplete="kab_id" :disabled=$disabledProperty
                                                    tabindex="22" placeholder="Kode Kabupaten"
                                                    wire:model="kab_id" />
                                                <x-text-input class="block ml-2" :value="old('kab_name')" required autofocus
                                                    autocomplete="kab_name" :disabled=true wire:model="kab_name"
                                                    placeholder="Nama Kabupaten" />
                                            </div>
                                            @error('kab_id')
                                                <span class="text-red-500">{{ $message }}</span>
                                            @enderror

                                            {{-- LOV --}}
                                            <div class="mt-2">
                                                @include('livewire.master-pasien.list-of-value-kabupaten')
                                            </div>
                                        </div>

                                        {{-- Provinsi --}}
                                        <div class="mb-2">
                                            <x-input-label for="prop_id" class="ml-1" :value="__('Provinsi :')" />
                                            <div class="flex">
                                                <x-text-input class="block sm:w-[100px]" :value="old('prop_id')" required
                                                    autofocus autocomplete="prop_id" :disabled=$disabledProperty
                                                    tabindex="23" placeholder="Kode Provinsi"
                                                    wire:model="prop_id" />
                                                <x-text-input class="block ml-2" :value="old('prop_name')" required autofocus
                                                    autocomplete="prop_name" :disabled=true wire:model="prop_name"
                                                    placeholder="Nama Provinsi" />
                                            </div>
                                            @error('prop_id')
                                                <span class="text-red-500">{{ $message }}</span>
                                            @enderror

                                            {{-- LOV --}}
                                            <div class="mt-2">
                                                @include('livewire.master-pasien.list-of-value-provinsi')
                                            </div>
                                        </div>

                                        {{-- Agama --}}
                                        <div class="mb-2">
                                            <x-input-label for="rel_id" :value="__('Agama   :')" />
                                            <div class="flex">
                                                <x-text-input class="block sm:w-[100px]" :value="old('rel_id')" required
                                                    autofocus autocomplete="rel_id" :disabled=$disabledProperty
                                                    tabindex="24" wire:model="rel_id" placeholder="Kode Agama" />
                                                <x-text-input class="block ml-2" :value="old('rel_desc')" required autofocus
                                                    autocomplete="rel_desc" :disabled=true wire:model="rel_desc"
                                                    placeholder="Nama Agama" />
                                            </div>
                                            @error('rel_id')
                                                <span class="text-red-500">{{ $message }}</span>
                                            @enderror

                                            {{-- LOV --}}
                                            <div class="mt-2">
                                                @include('livewire.master-pasien.list-of-value-rel')
                                            </div>
                                        </div>

                                        {{-- Pendidikan --}}
                                        <div class="mb-2">
                                            <x-input-label for="edu_id" class="ml-1" :value="__('Pendidikan :')" />
                                            <div class="flex">
                                                <x-text-input class="block sm:w-[100px]" :value="old('edu_id')" required
                                                    autofocus autocomplete="edu_id" :disabled=$disabledProperty
                                                    tabindex="25" wire:model="edu_id"
                                                    placeholder="Kode Pendidikan" />
                                                <x-text-input class="block ml-2" :value="old('edu_desc')" required autofocus
                                                    autocomplete="edu_desc" :disabled=true wire:model="edu_desc"
                                                    placeholder="Nama Pendidikan" />
                                            </div>
                                            @error('edu_id')
                                                <span class="text-red-500">{{ $message }}</span>
                                            @enderror

                                            {{-- LOV --}}
                                            <div class="mt-2">
                                                @include('livewire.master-pasien.list-of-value-edu')
                                            </div>
                                        </div>

                                        {{-- Pekerjaan --}}
                                        <div class="mb-2">
                                            <x-input-label for="job_id" class="ml-1" :value="__('Pekerjaan :')" />
                                            <div class="flex">
                                                <x-text-input class="block sm:w-[100px]" :value="old('job_id')" required
                                                    autofocus autocomplete="job_id" :disabled=$disabledProperty
                                                    tabindex="26" wire:model="job_id"
                                                    placeholder="Kode Pekerjaan" />
                                                <x-text-input class="block ml-2" :value="old('job_name')" required autofocus
                                                    placeholder="Nama Pekerjaan" autocomplete="job_name"
                                                    :disabled=true wire:model="job_name" />
                                            </div>
                                            @error('job_id')
                                                <span class="text-red-500">{{ $message }}</span>
                                            @enderror

                                            {{-- LOV --}}
                                            <div class="mt-2">
                                                @include('livewire.master-pasien.list-of-value-job')
                                            </div>
                                        </div>

                                        {{-- Phone --}}
                                        <div class="mb-2">
                                            <x-input-label for="phone" class="ml-1" :value="__('No. Telepon :')" />
                                            <x-text-input class="block" :value="old('phone')" required autofocus
                                                autocomplete="phone" :disabled=$disabledProperty wire:model="phone" id
                                                placeholder="Masukkan No. Telepon" tabindex="27" />
                                            @error('phone')
                                                <span class="text-red-500">{{ $message }}</span>
                                            @enderror
                                        </div>

                                    </div>
                                    {{-- End Form Kanan --}}
                                </div>
                            </div>

                        </div>

                    </div>

                </div>

                <div
                    class="sticky bottom-0 px-4 py-3 border-t border-gray-200 rounded-b bg-gray-50 dark:border-gray-600 sm:px-6 sm:flex sm:flex-row-reverse">
                    @if ($isOpenMode !== 'tampil')
                        <x-green-button wire:click.prevent="store()" type="button">Simpan</x-green-button>
                    @endif
                    <x-light-button wire:click="closeModal()" type="button">Keluar</x-light-button>
                </div>


            </form>

        </div>



    </div>

</div>
