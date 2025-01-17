<div class="space-y-4">
    <!-- Perubahan Berat Badan -->
    <div>
        <x-input-label for="formSkriningGiziAwal.perubahanBeratBadan" :value="__('Perubahan Berat Badan')" :required="__(true)" />
        <select id="formSkriningGiziAwal.perubahanBeratBadan"
            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            wire:model.debounce.500ms="formSkriningGiziAwal.perubahanBeratBadan">
            <option value="">Pilih Perubahan Berat Badan</option>
            @foreach ($skriningGiziAwalOptions['perubahanBeratBadan'] as $option)
                <option value="{{ $option['perubahan'] }}">{{ $option['perubahan'] }} (Skor: {{ $option['score'] }})
                </option>
            @endforeach
        </select>
        @error('formSkriningGiziAwal.perubahanBeratBadan')
            <x-input-error :messages="$message" />
        @enderror
    </div>

    <!-- Asupan Makanan -->
    <div>
        <x-input-label for="formSkriningGiziAwal.asupanMakanan" :value="__('Asupan Makanan')" :required="__(true)" />
        <select id="formSkriningGiziAwal.asupanMakanan"
            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            wire:model.debounce.500ms="formSkriningGiziAwal.asupanMakanan">
            <option value="">Pilih Asupan Makanan</option>
            @foreach ($skriningGiziAwalOptions['asupanMakanan'] as $option)
                <option value="{{ $option['asupan'] }}">{{ $option['asupan'] }} (Skor: {{ $option['score'] }})</option>
            @endforeach
        </select>
        @error('formSkriningGiziAwal.asupanMakanan')
            <x-input-error :messages="$message" />
        @enderror
    </div>

    <!-- Penyakit -->
    <div>
        <x-input-label for="formSkriningGiziAwal.penyakit" :value="__('Penyakit')" :required="__(true)" />
        <select id="formSkriningGiziAwal.penyakit"
            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            wire:model.debounce.500ms="formSkriningGiziAwal.penyakit">
            <option value="">Pilih Penyakit</option>
            @foreach ($skriningGiziAwalOptions['penyakit'] as $option)
                <option value="{{ $option['penyakit'] }}">{{ $option['penyakit'] }} (Skor: {{ $option['score'] }})
                </option>
            @endforeach
        </select>
        @error('formSkriningGiziAwal.penyakit')
            <x-input-error :messages="$message" />
        @enderror
    </div>

    <!-- Tombol Simpan -->
    <div class="mt-6">
        <button type="button" class="px-4 py-2 text-white bg-blue-500 rounded-md hover:bg-blue-600" wire:click="save">
            Simpan
        </button>
    </div>
</div>
