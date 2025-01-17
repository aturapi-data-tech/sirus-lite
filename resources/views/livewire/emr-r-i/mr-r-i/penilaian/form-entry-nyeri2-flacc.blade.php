<div class="space-y-4">
    <!-- Pengecekan Metode Penilaian -->
    @if ($formEntryNyeri['nyeri']['nyeriMetode']['nyeriMetode'] === 'FLACC')
        <!-- Judul Form FLACC -->
        <div class="mt-2 text-sm text-gray-600">
            {{ 'Menilai Nyeri Pada Pasien Anak Dibawah 8 Tahun atau Pasien yang Tidak Dapat Mengkomunikasikan Nyeri' }}
        </div>

        <!-- Loop untuk Setiap Kategori FLACC -->
        @foreach ($flaccOptions as $category => $options)
            <div>
                <!-- Label Kategori -->
                <x-input-label :value="__('Kategori: ' . ucfirst($category))" :required="true" />

                <!-- Opsi untuk Setiap Kategori -->
                <div class="grid grid-cols-3 gap-2 mt-1">
                    @foreach ($options as $index => $option)
                        <x-radio-button :label="$option['score']" :value="$option['score'] . 'X'"
                            wire:click="updateFlaccScore('{{ $category }}', {{ $index }})"
                            wire:model="formEntryNyeri.nyeri.nyeriMetode.dataNyeri.{{ $category }}.{{ $index }}.active" />
                    @endforeach
                </div>

                <!-- Deskripsi Opsi -->
                <div class="mt-2 text-sm text-gray-600">
                    @foreach ($options as $option)
                        <p>{{ $option['score'] }}: {{ $option['description'] }}</p>
                    @endforeach
                </div>

                <!-- Error Handling -->
                @error("formEntryNyeri.nyeri.nyeriMetode.dataNyeri.{$category}")
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        @endforeach
    @endif
</div>
