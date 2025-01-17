<div class="space-y-4">
    <!-- Perubahan Berat Badan -->
    <div>
        <x-input-label for="formPemantauanGizi.perubahanBeratBadan" :value="__('Perubahan Berat Badan')" :required="__(true)" />
        <select id="formPemantauanGizi.perubahanBeratBadan"
            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            wire:model.debounce.500ms="formPemantauanGizi.perubahanBeratBadan">
            <option value="">Pilih Perubahan Berat Badan</option>
            @foreach ($pemantauanGiziOptions['perubahanBeratBadan'] as $option)
                <option value="{{ $option['perubahan'] }}">{{ $option['perubahan'] }} (Skor: {{ $option['score'] }})
                </option>
            @endforeach
        </select>
        @error('formPemantauanGizi.perubahanBeratBadan')
            <x-input-error :messages="$message" />
        @enderror
    </div>

    <!-- Asupan Makanan -->
    <div>
        <x-input-label for="formPemantauanGizi.asupanMakanan" :value="__('Asupan Makanan')" :required="__(true)" />
        <select id="formPemantauanGizi.asupanMakanan"
            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            wire:model.debounce.500ms="formPemantauanGizi.asupanMakanan">
            <option value="">Pilih Asupan Makanan</option>
            @foreach ($pemantauanGiziOptions['asupanMakanan'] as $option)
                <option value="{{ $option['asupan'] }}">{{ $option['asupan'] }} (Skor: {{ $option['score'] }})</option>
            @endforeach
        </select>
        @error('formPemantauanGizi.asupanMakanan')
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
