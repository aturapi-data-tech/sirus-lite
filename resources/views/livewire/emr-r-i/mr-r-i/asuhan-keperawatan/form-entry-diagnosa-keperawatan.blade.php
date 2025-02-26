<div>
    <div class="w-full p-4 border rounded-lg shadow-lg">
        <h2 class="mb-4 text-xl font-bold">Form Entry Asuhan Keperawatan</h2>

        <!-- Diagnosa Keperawatan -->
        <div class="mb-4">
            <label class="font-semibold">Kode Diagnosa:</label>
            <input type="text" class="w-full p-2 border rounded"
                value="{{ $diagnosa_keperawatan['diagnosa_keperawatan'][0]['kode_diagnosa'] }}" disabled>
        </div>
        <div class="mb-4">
            <label class="font-semibold">Nama Diagnosa:</label>
            <input type="text" class="w-full p-2 border rounded"
                value="{{ $diagnosa_keperawatan['diagnosa_keperawatan'][0]['nama_diagnosa'] }}" disabled>
        </div>

        <!-- SDKI -->
        <div class="mb-4">
            <h3 class="text-lg font-bold">SDKI</h3>
            <p><strong>Kategori:</strong> {{ $diagnosa_keperawatan['diagnosa_keperawatan'][0]['SDKI']['kategori'] }}</p>
            <p><strong>Subkategori:</strong>
                {{ $diagnosa_keperawatan['diagnosa_keperawatan'][0]['SDKI']['subkategori'] }}</p>
            <p><strong>Definisi:</strong> {{ $diagnosa_keperawatan['diagnosa_keperawatan'][0]['SDKI']['definisi'] }}</p>
        </div>

        <!-- Penyebab -->
        <div class="mb-4">
            <h3 class="font-bold">Penyebab</h3>
            @foreach ($diagnosa_keperawatan['diagnosa_keperawatan'][0]['SDKI']['penyebab'] as $penyebab)
                <div class="flex items-center mb-2">
                    <input type="checkbox" class="mr-2" {{ $penyebab['status'] ? 'checked' : '' }}>
                    <label>{{ $penyebab['nama'] }}</label>
                </div>
            @endforeach
        </div>

        <!-- SIKI -->
        <div class="mb-4">
            <h3 class="text-lg font-bold">SIKI</h3>
            <p><strong>Tindakan:</strong></p>
        </div>

        <!-- Tindakan Keperawatan dengan Checkbox -->
        @foreach ($diagnosa_keperawatan['diagnosa_keperawatan'][0]['SIKI']['tindakan'] as $kategori => $tindakan_list)
            <div class="mb-4">
                <h3 class="font-bold">{{ ucfirst($kategori) }}</h3>
                @foreach ($tindakan_list as $tindakan)
                    <div class="flex items-center mb-2">
                        <input type="checkbox" class="mr-2">
                        <label>{{ $tindakan }}</label>
                    </div>
                @endforeach
            </div>
        @endforeach

        <!-- Rasional -->
        <div class="mb-4">
            <h3 class="font-bold">Rasional</h3>
            @foreach ($diagnosa_keperawatan['diagnosa_keperawatan'][0]['rasional'] as $kategori => $rasional_list)
                <div class="mb-4">
                    <h3 class="font-bold">{{ ucfirst($kategori) }}</h3>
                    @foreach ($rasional_list as $rasional)
                        <p>- {{ $rasional }}</p>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
</div>
