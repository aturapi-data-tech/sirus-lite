<div class="space-y-4">
    <!-- Antropometri -->
    <div>
        <x-input-label for="formPenilaianGiziLengkap.antropometri" :value="__('Antropometri')" :required="__(true)" />
        <select id="formPenilaianGiziLengkap.antropometri"
            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            wire:model.debounce.500ms="formPenilaianGiziLengkap.antropometri">
            <option value="">Pilih Kategori Antropometri</option>
            @foreach ($penilaianGiziLengkapOptions['antropometri'] as $option)
                <option value="{{ $option['kategori'] }}">{{ $option['kategori'] }} (Skor: {{ $option['score'] }})
                </option>
            @endforeach
        </select>
        @error('formPenilaianGiziLengkap.antropometri')
            <x-input-error :messages="$message" />
        @enderror
    </div>

    <!-- Biokimia -->
    <div>
        <x-input-label for="formPenilaianGiziLengkap.biokimia" :value="__('Biokimia')" :required="__(true)" />
        <select id="formPenilaianGiziLengkap.biokimia"
            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            wire:model.debounce.500ms="formPenilaianGiziLengkap.biokimia">
            <option value="">Pilih Kategori Biokimia</option>
            @foreach ($penilaianGiziLengkapOptions['biokimia'] as $option)
                <option value="{{ $option['kategori'] }}">{{ $option['kategori'] }} (Skor: {{ $option['score'] }})
                </option>
            @endforeach
        </select>
        @error('formPenilaianGiziLengkap.biokimia')
            <x-input-error :messages="$message" />
        @enderror
    </div>

    <!-- Klinis -->
    <div>
        <x-input-label for="formPenilaianGiziLengkap.klinis" :value="__('Klinis')" :required="__(true)" />
        <select id="formPenilaianGiziLengkap.klinis"
            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            wire:model.debounce.500ms="formPenilaianGiziLengkap.klinis">
            <option value="">Pilih Kategori Klinis</option>
            @foreach ($penilaianGiziLengkapOptions['klinis'] as $option)
                <option value="{{ $option['kategori'] }}">{{ $option['kategori'] }} (Skor: {{ $option['score'] }})
                </option>
            @endforeach
        </select>
        @error('formPenilaianGiziLengkap.klinis')
            <x-input-error :messages="$message" />
        @enderror
    </div>

    <!-- Skor Total -->
    <div class="mt-4">
        {{-- <p class="font-semibold">Skor Total: {{ $this->calculateTotalScore() }}</p> --}}
    </div>

    <!-- Tombol Simpan -->
    <div class="mt-6">
        <button type="button" class="px-4 py-2 text-white bg-blue-500 rounded-md hover:bg-blue-600" wire:click="save">
            Simpan
        </button>
    </div>
</div>
