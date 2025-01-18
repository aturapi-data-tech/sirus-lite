<div>
    @php
        $disabledProperty = true;
        $disabledPropertyRjStatus = false;
    @endphp
    {{-- Render hanya jika data penilaian ada --}}
    @if (isset($dataDaftarRi['penilaian']))
        <div class="w-full mb-1">
            <div class="grid grid-cols-1">
                {{-- Tab Container --}}
                <div id="TransaksiRawatJalan" class="px-2">
                    <div x-data="{ activeTab: 'Assessment Ulang Nyeri' }">
                        <!-- Navigasi Tab -->
                        <div class="px-2 mb-2 border-b border-gray-200 dark:border-gray-700">
                            <ul
                                class="flex flex-wrap -mb-px text-xs font-medium text-center text-gray-500 dark:text-gray-400">
                                <!-- Tab Assessment Ulang Nyeri -->
                                <li class="mr-2">
                                    <label
                                        class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                        :class="activeTab === 'Assessment Ulang Nyeri' ?
                                            'text-primary border-primary bg-gray-100' : ''"
                                        @click="activeTab = 'Assessment Ulang Nyeri'">
                                        Assessment Ulang Nyeri
                                    </label>
                                </li>

                                <!-- Tab Status Pediatrik -->
                                <li class="mr-2">
                                    <label
                                        class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                        :class="activeTab === 'Status Pediatrik' ? 'text-primary border-primary bg-gray-100' :
                                            ''"
                                        @click="activeTab = 'Status Pediatrik'">
                                        Status Pediatrik
                                    </label>
                                </li>

                                <!-- Tab Diagnosis -->
                                <li class="mr-2">
                                    <label
                                        class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                        :class="activeTab === 'Diagnosis' ? 'text-primary border-primary bg-gray-100' : ''"
                                        @click="activeTab = 'Diagnosis'">
                                        Diagnosis
                                    </label>
                                </li>

                                <!-- Tab Resiko Jatuh -->
                                <li class="mr-2">
                                    <label
                                        class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                        :class="activeTab === 'Resiko Jatuh' ? 'text-primary border-primary bg-gray-100' : ''"
                                        @click="activeTab = 'Resiko Jatuh'">
                                        Resiko Jatuh
                                    </label>
                                </li>

                                <!-- Tab Dekubitus -->
                                <li class="mr-2">
                                    <label
                                        class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                        :class="activeTab === 'Dekubitus' ? 'text-primary border-primary bg-gray-100' : ''"
                                        @click="activeTab = 'Dekubitus'">
                                        Dekubitus
                                    </label>
                                </li>

                                <!-- Tab Gizi -->
                                <li class="mr-2">
                                    <label
                                        class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                        :class="activeTab === 'Gizi' ? 'text-primary border-primary bg-gray-100' : ''"
                                        @click="activeTab = 'Gizi'">
                                        Gizi
                                    </label>
                                </li>
                            </ul>
                        </div>

                        <!-- Konten Tab -->
                        <div class="p-2 rounded-lg bg-gray-50 dark:bg-gray-800">
                            <!-- Konten Assessment Ulang Nyeri -->
                            <div x-show="activeTab === 'Assessment Ulang Nyeri'"
                                x-transition:enter.opacity.duration.600>
                                <!-- Tambahkan form atau tabel sesuai kebutuhan -->
                                @include('livewire.emr-r-i.mr-r-i.penilaian.form-entry-nyeri')

                            </div>

                            <!-- Konten Status Pediatrik -->
                            <div x-show="activeTab === 'Status Pediatrik'" x-transition:enter.opacity.duration.600>
                                <h3 class="mb-4 text-lg font-semibold">Status Pediatrik</h3>
                                <p>Ini adalah konten untuk tab Status Pediatrik.</p>
                                <!-- Tambahkan form atau tabel sesuai kebutuhan -->
                            </div>

                            <!-- Konten Diagnosis -->
                            <div x-show="activeTab === 'Diagnosis'" x-transition:enter.opacity.duration.600>
                                <h3 class="mb-4 text-lg font-semibold">Diagnosis</h3>
                                <p>Ini adalah konten untuk tab Diagnosis.</p>
                                <!-- Tambahkan form atau tabel sesuai kebutuhan -->
                            </div>

                            <!-- Konten Resiko Jatuh -->
                            <div x-show="activeTab === 'Resiko Jatuh'" x-transition:enter.opacity.duration.600>
                                @include('livewire.emr-r-i.mr-r-i.penilaian.form-entry-resiko-jatuh')
                                <!-- Tambahkan form atau tabel sesuai kebutuhan -->
                            </div>

                            <!-- Konten Dekubitus -->
                            <div x-show="activeTab === 'Dekubitus'" x-transition:enter.opacity.duration.600>
                                <h3 class="mb-4 text-lg font-semibold">Dekubitus</h3>
                                <p>Ini adalah konten untuk tab Dekubitus.</p>
                                <!-- Tambahkan form atau tabel sesuai kebutuhan -->
                            </div>

                            <!-- Konten Gizi -->
                            <div x-show="activeTab === 'Gizi'" x-transition:enter.opacity.duration.600>
                                <h3 class="mb-4 text-lg font-semibold">Gizi</h3>
                                <p>Ini adalah konten untuk tab Gizi.</p>
                                <!-- Tambahkan form atau tabel sesuai kebutuhan -->
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tombol Simpan --}}
                <div class="sticky bottom-0 flex justify-between px-4 py-3 bg-gray-50 sm:px-6">
                    <div></div>
                    <div>
                        <div wire:loading wire:target="store">
                            <x-loading />
                        </div>
                        <x-green-button :disabled=$disabledPropertyRjStatus wire:click.prevent="store()" type="button"
                            wire:loading.remove>
                            Simpan
                        </x-green-button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- @include('livewire.emr-r-i.mr-r-i.penilaian.form-entry-resiko-jatuh') --}}
    {{-- @include('livewire.emr-r-i.mr-r-i.penilaian.form-entry-status-pediatrik') --}}
    {{-- @include('livewire.emr-r-i.mr-r-i.penilaian.form-entry-dekubitus') --}}


    {{-- @include('livewire.emr-r-i.mr-r-i.penilaian.form-entry-gizi1')
    @include('livewire.emr-r-i.mr-r-i.penilaian.form-entry-gizi2-screening')
    @include('livewire.emr-r-i.mr-r-i.penilaian.form-entry-gizi3-status-pediatrik')
    @include('livewire.emr-r-i.mr-r-i.penilaian.form-entry-gizi4-gizi-lengkap')
    @include('livewire.emr-r-i.mr-r-i.penilaian.form-entry-gizi5-pemantauan')
    @include('livewire.emr-r-i.mr-r-i.penilaian.form-entry-gizi6-intervensi') --}}


</div>
