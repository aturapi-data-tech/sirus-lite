<div class="space-y-4">
    <!-- Status Gizi -->
    <div>
        <x-input-label for="formStatusPediatrik.statusGizi" :value="__('Status Gizi')" :required="__(true)" />
        <select id="formStatusPediatrik.statusGizi"
            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            wire:model.debounce.500ms="formStatusPediatrik.statusGizi">
            <option value="">Pilih Status Gizi</option>
            @foreach ($statusPediatrikOptions['statusGizi'] as $option)
                <option value="{{ $option['statusGizi'] }}">{{ $option['statusGizi'] }} (Skor: {{ $option['score'] }})
                </option>
            @endforeach
        </select>
        @error('formStatusPediatrik.statusGizi')
            <x-input-error :messages="$message" />
        @enderror
    </div>

    <!-- Perkembangan -->
    <div>
        <x-input-label for="formStatusPediatrik.perkembangan" :value="__('Perkembangan')" :required="__(true)" />
        <select id="formStatusPediatrik.perkembangan"
            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            wire:model.debounce.500ms="formStatusPediatrik.perkembangan">
            <option value="">Pilih Perkembangan</option>
            @foreach ($statusPediatrikOptions['perkembangan'] as $option)
                <option value="{{ $option['perkembangan'] }}">{{ $option['perkembangan'] }} (Skor:
                    {{ $option['score'] }})</option>
            @endforeach
        </select>
        @error('formStatusPediatrik.perkembangan')
            <x-input-error :messages="$message" />
        @enderror
    </div>

    <!-- Kesehatan Umum -->
    <div>
        <x-input-label for="formStatusPediatrik.kesehatanUmum" :value="__('Kesehatan Umum')" :required="__(true)" />
        <select id="formStatusPediatrik.kesehatanUmum"
            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            wire:model.debounce.500ms="formStatusPediatrik.kesehatanUmum">
            <option value="">Pilih Kesehatan Umum</option>
            @foreach ($statusPediatrikOptions['kesehatanUmum'] as $option)
                <option value="{{ $option['kesehatanUmum'] }}">{{ $option['kesehatanUmum'] }} (Skor:
                    {{ $option['score'] }})</option>
            @endforeach
        </select>
        @error('formStatusPediatrik.kesehatanUmum')
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
