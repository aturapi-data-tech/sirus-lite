<div class="space-y-4">
    <!-- Skor Nyeri (VAS) -->
    @if ($formEntryNyeri['nyeri']['nyeriMetode']['nyeriMetode'] === 'VAS')
        <div class="mt-2 text-sm text-gray-600">
            {{ 'Menilai Nyeri Prosedural Pada Pasien Dewasa Dan Anak Diatas 8 Tahun' }}
        </div>
        <div>
            <!-- SVG untuk Visual Analog Scale (VAS) -->
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 200" width="600" height="200">
                <!-- Area Warna untuk Skor -->
                <rect x="50" y="30" width="250" height="40" fill="#4CAF50" /> <!-- Hijau (0-5) -->
                <rect x="300" y="30" width="150" height="40" fill="#FFEB3B" /> <!-- Kuning (6-7) -->
                <rect x="450" y="30" width="100" height="40" fill="#F44336" /> <!-- Merah (8-10) -->

                <!-- Garis Skala -->
                <line x1="50" y1="50" x2="550" y2="50" stroke="#000" stroke-width="2" />

                <!-- Garis Vertikal untuk Setiap Skor -->
                @for ($i = 0; $i <= 10; $i++)
                    <line x1="{{ 50 + $i * 50 }}" y1="40" x2="{{ 50 + $i * 50 }}" y2="60" stroke="#000"
                        stroke-width="2" />
                @endfor

                <!-- Label Skor -->
                @for ($i = 0; $i <= 10; $i++)
                    <text x="{{ 45 + $i * 50 }}" y="80" font-size="12" fill="#000"
                        text-anchor="middle">{{ $i }}</text>
                @endfor

                <!-- Gambar Muka (Emoji) -->
                <text x="50" y="130" font-size="24" fill="#000" text-anchor="middle">😊</text> <!-- 0 -->
                <text x="100" y="130" font-size="24" fill="#000" text-anchor="middle">🙂</text> <!-- 1 -->
                <text x="150" y="130" font-size="24" fill="#000" text-anchor="middle">😐</text> <!-- 2 -->
                <text x="200" y="130" font-size="24" fill="#000" text-anchor="middle">😕</text> <!-- 3 -->
                <text x="250" y="130" font-size="24" fill="#000" text-anchor="middle">😟</text> <!-- 4 -->
                <text x="300" y="130" font-size="24" fill="#000" text-anchor="middle">😣</text> <!-- 5 -->
                <text x="350" y="130" font-size="24" fill="#000" text-anchor="middle">😖</text> <!-- 6 -->
                <text x="400" y="130" font-size="24" fill="#000" text-anchor="middle">😫</text> <!-- 7 -->
                <text x="450" y="130" font-size="24" fill="#000" text-anchor="middle">😩</text> <!-- 8 -->
                <text x="500" y="130" font-size="24" fill="#000" text-anchor="middle">😭</text> <!-- 9 -->
                <text x="550" y="130" font-size="24" fill="#000" text-anchor="middle">🤯</text> <!-- 10 -->

                <!-- Keterangan -->
                <text x="50" y="160" font-size="12" fill="#000" text-anchor="middle">Tidak Nyeri</text>
                <text x="550" y="160" font-size="12" fill="#000" text-anchor="middle">Nyeri Parah</text>
            </svg>

            <!-- Input Skor Nyeri (VAS) -->
            <x-input-label for="formEntryNyeri.nyeri.nyeriMetode.nyeriMetodeScore" :value="__('Skor Nyeri (VAS)')"
                :required="__(true)" />
            <div class="grid grid-cols-11 gap-2 mt-1">
                @foreach ($formEntryNyeri['nyeri']['nyeriMetode']['dataNyeri'] as $option)
                    <x-radio-button :label="$option['vas']" :value="$option['vas']"
                        wire:click="updateVasNyeriScore('{{ $option['vas'] }}')"
                        wire:model="formEntryNyeri.nyeri.nyeriMetode.nyeriMetodeScore" />
                @endforeach
            </div>
            @error('formEntryNyeri.nyeri.nyeriMetode.nyeriMetodeScore')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    @endif
</div>
