<div>
    <div class="w-full mb-1">
        @role(['Perawat', 'Dokter', 'Admin'])
            <div class="pt-0">
                {{-- Form Input Observasi Lanjutan (versi rapi + konsisten) --}}
                <div x-data class="grid grid-cols-12 gap-3">
                    <!-- BARIS 1 -->
                    <!-- Cairan -->
                    <div class="col-span-12 md:col-span-6">
                        <x-input-label for="observasiLanjutan.cairan" :value="__('Cairan')" />
                        <div class="relative mt-1">
                            <x-text-input id="observasiLanjutan.cairan" x-ref="seq1"
                                x-on:keydown.enter.prevent="$refs.seq2?.focus()" placeholder="Cairan" class="pr-10"
                                :errorshas="$errors->has('observasiLanjutan.cairan')" :disabled="$disabledPropertyRjStatus" wire:model="observasiLanjutan.cairan" />
                            <span
                                class="absolute inset-y-0 flex items-center text-xs text-gray-400 pointer-events-none right-2">ml</span>
                        </div>
                        @error('observasiLanjutan.cairan')
                            <span class="text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Tetesan -->
                    <div class="col-span-12 md:col-span-6">
                        <x-input-label for="observasiLanjutan.tetesan" :value="__('Tetesan')" />
                        <div class="relative mt-1">
                            <x-text-input id="observasiLanjutan.tetesan" x-ref="seq2"
                                x-on:keydown.enter.prevent="$refs.seq3?.focus()" placeholder="Tetesan/menit" class="pr-12"
                                :errorshas="$errors->has('observasiLanjutan.tetesan')" :disabled="$disabledPropertyRjStatus" wire:model="observasiLanjutan.tetesan" />
                            <span
                                class="absolute inset-y-0 flex items-center text-xs text-gray-400 pointer-events-none right-2">gtt/menit</span>
                        </div>
                        @error('observasiLanjutan.tetesan')
                            <span class="text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- BARIS 2 -->
                    <!-- TD Sistolik -->
                    <div class="col-span-12 md:col-span-2">
                        <x-input-label for="observasiLanjutan.sistolik" :value="__('TD Sistolik')" :required="true" />
                        <div class="relative mt-1">
                            <x-text-input id="observasiLanjutan.sistolik" x-ref="seq3"
                                x-on:keydown.enter.prevent="$refs.seq4?.focus()" placeholder="Sistolik" class="pr-12"
                                :errorshas="$errors->has('observasiLanjutan.sistolik')" :disabled="$disabledPropertyRjStatus" wire:model="observasiLanjutan.sistolik" />
                            <span
                                class="absolute inset-y-0 flex items-center text-xs text-gray-400 pointer-events-none right-2">mmHg</span>
                        </div>
                        @error('observasiLanjutan.sistolik')
                            <span class="text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- TD Diastolik -->
                    <div class="col-span-12 md:col-span-2">
                        <x-input-label for="observasiLanjutan.distolik" :value="__('TD Diastolik')" :required="true" />
                        <div class="relative mt-1">
                            <x-text-input id="observasiLanjutan.distolik" x-ref="seq4"
                                x-on:keydown.enter.prevent="$refs.seq5?.focus()" placeholder="Diastolik" class="pr-12"
                                :errorshas="$errors->has('observasiLanjutan.distolik')" :disabled="$disabledPropertyRjStatus" wire:model="observasiLanjutan.distolik" />
                            <span
                                class="absolute inset-y-0 flex items-center text-xs text-gray-400 pointer-events-none right-2">mmHg</span>
                        </div>
                        @error('observasiLanjutan.distolik')
                            <span class="text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Frekuensi Nafas -->
                    <div class="col-span-12 md:col-span-2">
                        <x-input-label for="observasiLanjutan.frekuensiNafas" :value="__('Frekuensi Nafas')" :required="true" />
                        <div class="relative mt-1">
                            <x-text-input id="observasiLanjutan.frekuensiNafas" x-ref="seq5"
                                x-on:keydown.enter.prevent="$refs.seq6?.focus()" placeholder="Nafas" class="pr-10"
                                :errorshas="$errors->has('observasiLanjutan.frekuensiNafas')" :disabled="$disabledPropertyRjStatus" wire:model="observasiLanjutan.frekuensiNafas" />
                            <span
                                class="absolute inset-y-0 flex items-center text-xs text-gray-400 pointer-events-none right-2">x/menit</span>
                        </div>
                        @error('observasiLanjutan.frekuensiNafas')
                            <span class="text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Frekuensi Nadi -->
                    <div class="col-span-12 md:col-span-2">
                        <x-input-label for="observasiLanjutan.frekuensiNadi" :value="__('Frekuensi Nadi')" :required="true" />
                        <div class="relative mt-1">
                            <x-text-input id="observasiLanjutan.frekuensiNadi" x-ref="seq6"
                                x-on:keydown.enter.prevent="$refs.seq7?.focus()" placeholder="Nadi" class="pr-10"
                                :errorshas="$errors->has('observasiLanjutan.frekuensiNadi')" :disabled="$disabledPropertyRjStatus" wire:model="observasiLanjutan.frekuensiNadi" />
                            <span
                                class="absolute inset-y-0 flex items-center text-xs text-gray-400 pointer-events-none right-2">x/menit</span>
                        </div>
                        @error('observasiLanjutan.frekuensiNadi')
                            <span class="text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Suhu -->
                    <div class="col-span-12 md:col-span-2">
                        <x-input-label for="observasiLanjutan.suhu" :value="__('Suhu')" :required="true" />
                        <div class="relative mt-1">
                            <x-text-input id="observasiLanjutan.suhu" x-ref="seq7"
                                x-on:keydown.enter.prevent="$refs.seq8?.focus()" placeholder="Suhu" class="pr-8"
                                :errorshas="$errors->has('observasiLanjutan.suhu')" :disabled="$disabledPropertyRjStatus" wire:model="observasiLanjutan.suhu" />
                            <span
                                class="absolute inset-y-0 flex items-center text-xs text-gray-400 pointer-events-none right-2">°C</span>
                        </div>
                        @error('observasiLanjutan.suhu')
                            <span class="text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- SpO2 -->
                    <div class="col-span-12 md:col-span-2">
                        <x-input-label for="observasiLanjutan.spo2" :value="__('SpO₂')" :required="true" />
                        <div class="relative mt-1">
                            <x-text-input id="observasiLanjutan.spo2" x-ref="seq8"
                                x-on:keydown.enter.prevent="$refs.seq9?.focus()" placeholder="SpO₂" class="pr-7"
                                :errorshas="$errors->has('observasiLanjutan.spo2')" :disabled="$disabledPropertyRjStatus" wire:model="observasiLanjutan.spo2" />
                            <span
                                class="absolute inset-y-0 flex items-center text-xs text-gray-400 pointer-events-none right-2">%</span>
                        </div>
                        @error('observasiLanjutan.spo2')
                            <span class="text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- SISANYA TETAP -->
                    <!-- GDA -->
                    <div class="col-span-12 md:col-span-3">
                        <x-input-label for="observasiLanjutan.gda" :value="__('GDA')" />
                        <div class="relative mt-1">
                            <x-text-input id="observasiLanjutan.gda" x-ref="seq9"
                                x-on:keydown.enter.prevent="$refs.seq10?.focus()" placeholder="Gula Darah Acak"
                                class="pr-9" :errorshas="$errors->has('observasiLanjutan.gda')" :disabled="$disabledPropertyRjStatus" wire:model="observasiLanjutan.gda" />
                            <span
                                class="absolute inset-y-0 flex items-center text-xs text-gray-400 pointer-events-none right-2">mg/dL</span>
                        </div>
                        @error('observasiLanjutan.gda')
                            <span class="text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- GCS -->
                    <div class="col-span-12 md:col-span-3">
                        <x-input-label for="observasiLanjutan.gcs" :value="__('GCS')" />
                        <x-text-input id="observasiLanjutan.gcs" x-ref="seq10"
                            x-on:keydown.enter.prevent="$refs.seq11?.focus()" placeholder="E V M" class="mt-1"
                            :errorshas="$errors->has('observasiLanjutan.gcs')" :disabled="$disabledPropertyRjStatus" wire:model="observasiLanjutan.gcs" />
                        @error('observasiLanjutan.gcs')
                            <span class="text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Waktu Pemeriksaan -->
                    <div class="col-span-12 md:col-span-6">
                        <x-input-label for="observasiLanjutan.waktuPemeriksaan" :value="__('Waktu Pemeriksaan')" :required="true" />
                        <div class="flex items-center gap-2 mt-1">
                            <x-text-input id="observasiLanjutan.waktuPemeriksaan" x-ref="seq11"
                                x-on:keydown.enter.prevent="$refs.seqAction?.focus()" placeholder="dd/mm/yyyy hh:mm:ss"
                                class="grow" :errorshas="$errors->has('observasiLanjutan.waktuPemeriksaan')" :disabled="$disabledPropertyRjStatus"
                                wire:model="observasiLanjutan.waktuPemeriksaan" />
                            @if (!$observasiLanjutan['waktuPemeriksaan'])
                                <x-secondary-button wire:click.prevent="setWaktuPemeriksaan" type="button"
                                    class="text-xs whitespace-nowrap">
                                    Set waktu sekarang
                                </x-secondary-button>
                            @endif
                        </div>
                        @error('observasiLanjutan.waktuPemeriksaan')
                            <span class="text-xs text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="flex items-end col-span-12 md:col-span-6">
                        <div>
                            <x-input-label :value="__('Aksi')" />
                            <div class="flex gap-2 mt-1">
                                <x-green-button x-ref="seqAction" class="inline-flex" wire:click="addObservasiLanjutan"
                                    wire:loading.attr="disabled" wire:target="addObservasiLanjutan">
                                    <svg class="w-4 h-4 mr-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 18 18">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="M9 1v16M1 9h16" />
                                    </svg>
                                    <span wire:loading.remove wire:target="addObservasiLanjutan">Tambah</span>
                                    <span wire:loading wire:target="addObservasiLanjutan">Menambah...</span>
                                </x-green-button>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        @endrole

        {{-- Tabel Data Observasi Lanjutan (rapi + sticky header + zebra) --}}
        {{-- Daftar Observasi Lanjutan — tema kartu sama seperti Obat & Cairan --}}
        <div class="flex flex-col my-3">
            <div class="overflow-hidden rounded-lg ring-1 ring-gray-200 dark:ring-gray-700">
                <div class="overflow-x-auto">
                    @php
                        use Carbon\Carbon;

                        $observasiData = $dataDaftarUgd['observasi']['observasiLanjutan']['tandaVital'] ?? [];
                        $sortedObservasi = collect($observasiData)
                            ->sortByDesc(function ($item) {
                                try {
                                    return Carbon::createFromFormat(
                                        'd/m/Y H:i:s',
                                        $item['waktuPemeriksaan'] ?? '01/01/2000 00:00:00',
                                    );
                                } catch (\Exception $e) {
                                    return Carbon::now();
                                }
                            })
                            ->values();
                    @endphp

                    <table class="w-full text-sm text-left text-gray-700 table-auto dark:text-gray-300">
                        {{-- HEADER: berkelompok --}}
                        <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-800">
                            <tr class="text-center border-b dark:border-gray-700">
                                <th colspan="2"
                                    class="px-4 py-2 text-gray-700 bg-gray-100 rounded-tl-lg dark:text-gray-300 dark:bg-gray-900">
                                    Informasi Umum
                                </th>
                                <th colspan="7"
                                    class="px-4 py-2 text-gray-700 bg-gray-100 dark:text-gray-300 dark:bg-gray-900">
                                    Tanda Vital
                                </th>
                                <th colspan="2"
                                    class="px-4 py-2 text-gray-700 bg-gray-100 rounded-tr-lg dark:text-gray-300 dark:bg-gray-900">
                                    Terapi / Aksi
                                </th>
                            </tr>
                            <tr class="text-center">
                                <th class="px-4 py-3">No</th>
                                <th class="px-4 py-3">Waktu Pemeriksaan</th>
                                <th class="px-4 py-3">TD</th>
                                <th class="px-4 py-3">Nadi</th>
                                <th class="px-4 py-3">Nafas</th>
                                <th class="px-4 py-3">Suhu</th>
                                <th class="px-4 py-3">SpO₂</th>
                                <th class="px-4 py-3">GDA</th>
                                <th class="px-4 py-3">GCS</th>
                                <th class="px-4 py-3">Cairan / Tetesan</th>
                                <th class="px-4 py-3">Action</th>
                            </tr>
                        </thead>

                        {{-- BODY --}}
                        <tbody class="bg-white dark:bg-gray-900">
                            @forelse ($sortedObservasi as $obs)
                                @php $rowId = $obs['id'] ?? ($obs['waktuPemeriksaan'] ?? uniqid()); @endphp
                                <tr wire:key="ttv-{{ $rowId }}"
                                    class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800/60">
                                    {{-- Informasi Umum --}}
                                    <td class="px-4 py-3 text-center">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex flex-col items-center">
                                            <span class="font-medium text-gray-900 dark:text-gray-100">
                                                {{ $obs['waktuPemeriksaan'] ?? '-' }}
                                            </span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $obs['pemeriksa'] ?? '-' }}
                                            </span>
                                        </div>
                                    </td>

                                    {{-- Tanda Vital --}}
                                    <td class="px-4 py-3 text-center">
                                        {{ ($obs['sistolik'] ?? '-') . '/' . ($obs['distolik'] ?? '-') }} <span
                                            class="text-xs">mmHg</span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        {{ $obs['frekuensiNadi'] ?? '-' }} <span class="text-xs">x/mnt</span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        {{ $obs['frekuensiNafas'] ?? '-' }} <span class="text-xs">x/mnt</span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        {{ $obs['suhu'] ?? '-' }} <span class="text-xs">°C</span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        {{ $obs['spo2'] ?? '-' }} <span class="text-xs">%</span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        {{ $obs['gda'] ?? '-' }} <span class="text-xs">mg/dL</span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        {{ $obs['gcs'] ?? '-' }}
                                    </td>

                                    {{-- Terapi / Aksi --}}
                                    <td class="px-4 py-3 text-center">
                                        <div class="leading-5">
                                            <div>{{ $obs['cairan'] ?? '-' }} <span class="text-xs">ml</span></div>
                                            <div class="text-xs text-gray-500">{{ $obs['tetesan'] ?? '-' }} gtt/mnt
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @role(['Perawat', 'Dokter', 'Admin'])
                                            <x-alternative-button class="inline-flex"
                                                wire:click="removeObservasiLanjutan('{{ $obs['waktuPemeriksaan'] }}')"
                                                wire:confirm="Apakah Anda yakin ingin menghapus data observasi ini?"
                                                wire:loading.attr="disabled">
                                                <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg"
                                                    fill="currentColor" viewBox="0 0 18 20">
                                                    <path
                                                        d="M17 4h-4V2a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v2H1a1 1 0 0 0 0 2h1v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6h1a1 1 0 1 0 0-2Z" />
                                                </svg>
                                                Hapus
                                            </x-alternative-button>
                                        @endrole
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="px-4 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-10 h-10 mb-2 text-gray-400" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <p class="text-base font-medium text-gray-600 dark:text-gray-300">Belum ada
                                                data tanda vital</p>
                                            <p class="text-sm text-gray-500">Silakan tambah observasi baru</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>



    </div>
</div>
