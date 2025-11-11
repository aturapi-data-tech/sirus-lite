<div class="w-full mb-1" wire:key="morse-form-{{ $formMorseNonce }}">
    <div class="pt-0">
        <div class="p-4 mb-6 border border-gray-300 rounded-lg">
            <h3 class="mb-4 text-lg font-semibold">Tambah Penilaian Morse Baru</h3>

            <div class="grid grid-cols-1 gap-4">
                <!-- Riwayat Jatuh -->
                <div>
                    <x-input-label for="newMorseEntry.riwayatJatuh3blnTerakhir" :value="__('Riwayat Jatuh (3 Bulan Terakhir)')" :required="true" />
                    <div class="grid grid-cols-2 gap-2 mt-2">
                        @foreach ($morseOptions['riwayatJatuh3blnTerakhirOptions'] ?? [] as $option)
                            <x-radio-button :label="__($option['riwayatJatuh3blnTerakhir'] ?? '-')" value="{{ $option['riwayatJatuh3blnTerakhir'] ?? '' }}"
                                wire:model="newMorseEntry.riwayatJatuh3blnTerakhir" />
                        @endforeach
                    </div>
                </div>

                <!-- Diagnosa Sekunder -->
                <div>
                    <x-input-label for="newMorseEntry.diagSekunder" :value="__('Diagnosa Sekunder')" />
                    <div class="grid grid-cols-2 gap-2 mt-2">
                        @foreach ($morseOptions['diagSekunderOptions'] ?? [] as $option)
                            <x-radio-button :label="__($option['diagSekunder'] ?? '-')" value="{{ $option['diagSekunder'] ?? '' }}"
                                wire:model="newMorseEntry.diagSekunder" />
                        @endforeach
                    </div>
                </div>

                <!-- Alat Bantu -->
                <div>
                    <x-input-label for="newMorseEntry.alatBantu" :value="__('Alat Bantu')" />
                    <div class="grid grid-cols-3 gap-2 mt-2">
                        @foreach ($morseOptions['alatBantuOptions'] ?? [] as $option)
                            <x-radio-button :label="__($option['alatBantu'] ?? '-')" value="{{ $option['alatBantu'] ?? '' }}"
                                wire:model="newMorseEntry.alatBantu" />
                        @endforeach
                    </div>
                </div>

                <!-- Heparin -->
                <div>
                    <x-input-label for="newMorseEntry.heparin" :value="__('Heparin')" />
                    <div class="grid grid-cols-2 gap-2 mt-2">
                        @foreach ($morseOptions['heparinOptions'] ?? [] as $option)
                            <x-radio-button :label="__($option['heparin'] ?? '-')" value="{{ $option['heparin'] ?? '' }}"
                                wire:model="newMorseEntry.heparin" />
                        @endforeach
                    </div>
                </div>

                <!-- Gaya Berjalan -->
                <div>
                    <x-input-label for="newMorseEntry.gayaBerjalan" :value="__('Gaya Berjalan')" />
                    <div class="grid grid-cols-3 gap-2 mt-2">
                        @foreach ($morseOptions['gayaBerjalanOptions'] ?? [] as $option)
                            <x-radio-button :label="__($option['gayaBerjalan'] ?? '-')" value="{{ $option['gayaBerjalan'] ?? '' }}"
                                wire:model="newMorseEntry.gayaBerjalan" />
                        @endforeach
                    </div>
                </div>

                <!-- Kesadaran -->
                <div>
                    <x-input-label for="newMorseEntry.kesadaran" :value="__('Kesadaran')" />
                    <div class="grid grid-cols-2 gap-2 mt-2">
                        @foreach ($morseOptions['kesadaranOptions'] ?? [] as $option)
                            <x-radio-button :label="__($option['kesadaran'] ?? '-')" value="{{ $option['kesadaran'] ?? '' }}"
                                wire:model="newMorseEntry.kesadaran" />
                        @endforeach
                    </div>
                </div>

                @php
                    $total = (int) ($newMorseEntry['totalScore'] ?? 0);
                    $kategori = $newMorseEntry['kategoriRisiko'] ?? '-';
                    $cls = $total >= 51 ? 'text-red-600' : ($total >= 25 ? 'text-yellow-600' : 'text-green-600');
                @endphp

                <!-- Total Score Display -->
                <div class="p-3 bg-gray-100 rounded">
                    <x-input-label :value="__('Total Skor: ' . $total)" />
                    <x-input-label :value="__('Kategori: ' . $kategori)" class="font-semibold {{ $cls }}" />
                </div>

                <!-- Tombol Tambah -->
                <div class="flex justify-end">
                    <x-green-button wire:click="tambahMorse" type="button" wire:loading.attr="disabled">
                        <span wire:loading.remove>Tambah Penilaian Morse</span>
                        <span wire:loading>Memproses...</span>
                    </x-green-button>
                </div>
            </div>
        </div>

        <!-- Daftar Penilaian Morse -->
        <div class="mt-6">
            <h3 class="mb-4 text-lg font-semibold">Daftar Penilaian Morse</h3>

            @php
                $listMorse = collect($dataDaftarUgd['penilaian']['resikoJatuh']['skalaMorse'] ?? [])
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

            @if (empty($listMorse))
                <p class="text-gray-500">Belum ada penilaian Morse.</p>
            @else
                @foreach ($listMorse as $index => $morse)
                    @php
                        $t = (int) ($morse['totalScore'] ?? 0);
                        $clsItem = $t >= 51 ? 'text-red-600' : ($t >= 25 ? 'text-yellow-600' : 'text-green-600');
                    @endphp

                    <div class="p-4 mb-4 border border-gray-300 rounded-lg">
                        <div class="flex justify-between mb-2">
                            <x-red-button wire:click="hapusMorse({{ $index }})" type="button" class="text-sm">
                                Hapus
                            </x-red-button>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div><strong>Tanggal:</strong> {{ $morse['tanggal'] ?? '-' }}</div>
                            <div><strong>Total Skor:</strong> {{ $t }}</div>
                            <div><strong>Kategori:</strong> <span
                                    class="{{ $clsItem }}">{{ $morse['kategoriRisiko'] ?? '-' }}</span></div>
                            <div><strong>Riwayat Jatuh:</strong> {{ $morse['riwayatJatuh3blnTerakhir'] ?? '-' }}</div>
                            <div><strong>Diagnosa Sekunder:</strong> {{ $morse['diagSekunder'] ?? '-' }}</div>
                            <div><strong>Alat Bantu:</strong> {{ $morse['alatBantu'] ?? '-' }}</div>
                            <div><strong>Heparin:</strong> {{ $morse['heparin'] ?? '-' }}</div>
                            <div><strong>Gaya Berjalan:</strong> {{ $morse['gayaBerjalan'] ?? '-' }}</div>
                            <div><strong>Kesadaran:</strong> {{ $morse['kesadaran'] ?? '-' }}</div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
