<div class="w-full mb-1" wire:key="humpty-form-{{ $formHumptyNonce }}">
    <div class="pt-0">
        <div class="p-4 mb-6 border border-gray-300 rounded-lg">
            <h3 class="mb-4 text-lg font-semibold">Tambah Penilaian Humpty Dumpty Baru</h3>

            <div class="grid grid-cols-1 gap-4">
                <!-- Usia -->
                <div>
                    <x-input-label for="newHumptyDumptyEntry.umur" :value="__('Usia')" :required="true" />
                    <div class="grid grid-cols-2 gap-2 mt-2">
                        @foreach ($humptyDumptyOptions['umurOptions'] ?? [] as $option)
                            <x-radio-button :label="__($option['umur'] ?? '-')" value="{{ $option['umur'] ?? '' }}"
                                wire:model="newHumptyDumptyEntry.umur" />
                        @endforeach
                    </div>
                </div>

                <!-- Jenis Kelamin -->
                <div>
                    <x-input-label for="newHumptyDumptyEntry.sex" :value="__('Jenis Kelamin')" />
                    <div class="grid grid-cols-2 gap-2 mt-2">
                        @foreach ($humptyDumptyOptions['sexOptions'] ?? [] as $option)
                            <x-radio-button :label="__($option['sex'] ?? '-')" value="{{ $option['sex'] ?? '' }}"
                                wire:model="newHumptyDumptyEntry.sex" />
                        @endforeach
                    </div>
                </div>

                <!-- Diagnosa -->
                <div>
                    <x-input-label for="newHumptyDumptyEntry.diagnosa" :value="__('Diagnosa')" />
                    <div class="grid grid-cols-2 gap-2 mt-2">
                        @foreach ($humptyDumptyOptions['diagnosaOptions'] ?? [] as $option)
                            <x-radio-button :label="__($option['diagnosa'] ?? '-')" value="{{ $option['diagnosa'] ?? '' }}"
                                wire:model="newHumptyDumptyEntry.diagnosa" />
                        @endforeach
                    </div>
                </div>

                <!-- Gangguan Kognitif -->
                <div>
                    <x-input-label for="newHumptyDumptyEntry.gangguanKognitif" :value="__('Gangguan Kognitif')" />
                    <div class="grid grid-cols-3 gap-2 mt-2">
                        @foreach ($humptyDumptyOptions['gangguanKognitifOptions'] ?? [] as $option)
                            <x-radio-button :label="__($option['gangguanKognitif'] ?? '-')" value="{{ $option['gangguanKognitif'] ?? '' }}"
                                wire:model="newHumptyDumptyEntry.gangguanKognitif" />
                        @endforeach
                    </div>
                </div>

                <!-- Faktor Lingkungan -->
                <div>
                    <x-input-label for="newHumptyDumptyEntry.faktorLingkungan" :value="__('Faktor Lingkungan')" />
                    <div class="grid grid-cols-2 gap-2 mt-2">
                        @foreach ($humptyDumptyOptions['faktorLingkunganOptions'] ?? [] as $option)
                            <x-radio-button :label="__($option['faktorLingkungan'] ?? '-')" value="{{ $option['faktorLingkungan'] ?? '' }}"
                                wire:model="newHumptyDumptyEntry.faktorLingkungan" />
                        @endforeach
                    </div>
                </div>

                <!-- Penggunaan Obat -->
                <div>
                    <x-input-label for="newHumptyDumptyEntry.penggunaanObat" :value="__('Penggunaan Obat')" />
                    <div class="grid grid-cols-1 gap-2 mt-2">
                        @foreach ($humptyDumptyOptions['penggunaanObatOptions'] ?? [] as $option)
                            <x-radio-button :label="__($option['penggunaanObat'] ?? '-')" value="{{ $option['penggunaanObat'] ?? '' }}"
                                wire:model="newHumptyDumptyEntry.penggunaanObat" />
                        @endforeach
                    </div>
                </div>

                <!-- Respon Terhadap Operasi -->
                <div>
                    <x-input-label for="newHumptyDumptyEntry.responTerhadapOperasi" :value="__('Respon Terhadap Operasi')" />
                    <div class="grid grid-cols-3 gap-2 mt-2">
                        @foreach ($humptyDumptyOptions['responTerhadapOperasiOptions'] ?? [] as $option)
                            <x-radio-button :label="__($option['responTerhadapOperasi'] ?? '-')" value="{{ $option['responTerhadapOperasi'] ?? '' }}"
                                wire:model="newHumptyDumptyEntry.responTerhadapOperasi" />
                        @endforeach
                    </div>
                </div>

                @php
                    $total = (int) ($newHumptyDumptyEntry['totalScore'] ?? 0);
                    $kategori = $newHumptyDumptyEntry['kategoriRisiko'] ?? '-';
                    $cls = $total >= 12 ? 'text-red-600' : 'text-green-600';
                @endphp

                <!-- Total Score -->
                <div class="p-3 bg-gray-100 rounded">
                    <x-input-label :value="__('Total Skor: ' . $total)" />
                    <x-input-label :value="__('Kategori: ' . $kategori)" class="font-semibold {{ $cls }}" />
                </div>

                <!-- Tombol Tambah -->
                <div class="flex justify-end">
                    <x-green-button wire:click="tambahHumptyDumpty" type="button" wire:loading.attr="disabled">
                        <span wire:loading.remove>Tambah Penilaian Humpty Dumpty</span>
                        <span wire:loading>Memproses...</span>
                    </x-green-button>
                </div>
            </div>
        </div>

        <!-- Daftar Penilaian Humpty Dumpty -->
        <div class="mt-6">
            <h3 class="mb-4 text-lg font-semibold">Daftar Penilaian Humpty Dumpty</h3>

            @php
                $listHumpty = collect($dataDaftarUgd['penilaian']['resikoJatuh']['skalaHumptyDumpty'] ?? [])
                    ->filter(function ($item) {
                        return !empty($item['tanggal']); // hanya ambil yang ada tanggalnya
                    })
                    ->sortByDesc(function ($item) {
                        return \Carbon\Carbon::createFromFormat(
                            'd/m/Y H:i:s',
                            $item['tanggal'] ?? '01/01/1970 00:00:00',
                        )->timestamp;
                    })
                    ->values()
                    ->all();
            @endphp

            @if (empty($listHumpty))
                <p class="text-gray-500">Belum ada penilaian Humpty Dumpty.</p>
            @else
                @foreach ($listHumpty as $index => $humpty)
                    @php
                        $t = (int) ($humpty['totalScore'] ?? 0);
                        $clsItem = $t >= 12 ? 'text-red-600' : 'text-green-600';
                    @endphp

                    <div class="p-4 mb-4 border border-gray-300 rounded-lg">
                        <div class="flex justify-between mb-2">
                            <x-red-button wire:click="hapusHumptyDumpty({{ $index }})" type="button"
                                class="text-sm">
                                Hapus
                            </x-red-button>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div><strong>Tanggal:</strong> {{ $humpty['tanggal'] ?? '-' }}</div>
                            <div><strong>Total Skor:</strong> {{ $t }}</div>
                            <div><strong>Kategori:</strong> <span
                                    class="{{ $clsItem }}">{{ $humpty['kategoriRisiko'] ?? '-' }}</span></div>
                            <div><strong>Usia:</strong> {{ $humpty['umur'] ?? '-' }}</div>
                            <div><strong>Jenis Kelamin:</strong> {{ $humpty['sex'] ?? '-' }}</div>
                            <div><strong>Diagnosa:</strong> {{ $humpty['diagnosa'] ?? '-' }}</div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
