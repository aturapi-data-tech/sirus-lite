<div class="space-y-4">
    <!-- Diet -->
    <div>
        <x-input-label for="formIntervensiGizi.diet" :value="__('Diet')" />
        <select id="formIntervensiGizi.diet"
            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            wire:model.debounce.500ms="formIntervensiGizi.diet">
            <option value="">Pilih Jenis Diet</option>
            @foreach ($intervensiGiziOptions['diet'] as $option)
                <option value="{{ $option['jenis'] }}">{{ $option['jenis'] }}</option>
            @endforeach
        </select>
        @error('formIntervensiGizi.diet')
            <x-input-error :messages="$message" />
        @enderror
    </div>

    <!-- Suplementasi -->
    <div>
        <x-input-label for="formIntervensiGizi.suplementasi" :value="__('Suplementasi')" />
        <select id="formIntervensiGizi.suplementasi"
            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            wire:model.debounce.500ms="formIntervensiGizi.suplementasi">
            <option value="">Pilih Jenis Suplementasi</option>
            @foreach ($intervensiGiziOptions['suplementasi'] as $option)
                <option value="{{ $option['jenis'] }}">{{ $option['jenis'] }}</option>
            @endforeach
        </select>
        @error('formIntervensiGizi.suplementasi')
            <x-input-error :messages="$message" />
        @enderror
    </div>

    <!-- Nutrisi Enteral -->
    <div>
        <x-input-label for="formIntervensiGizi.nutrisiEnteral" :value="__('Nutrisi Enteral')" />
        <select id="formIntervensiGizi.nutrisiEnteral"
            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            wire:model.debounce.500ms="formIntervensiGizi.nutrisiEnteral">
            <option value="">Pilih Jenis Nutrisi Enteral</option>
            @foreach ($intervensiGiziOptions['nutrisiEnteral'] as $option)
                <option value="{{ $option['jenis'] }}">{{ $option['jenis'] }}</option>
            @endforeach
        </select>
        @error('formIntervensiGizi.nutrisiEnteral')
            <x-input-error :messages="$message" />
        @enderror
    </div>

    <!-- Nutrisi Parenteral -->
    <div>
        <x-input-label for="formIntervensiGizi.nutrisiParenteral" :value="__('Nutrisi Parenteral')" />
        <select id="formIntervensiGizi.nutrisiParenteral"
            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            wire:model.debounce.500ms="formIntervensiGizi.nutrisiParenteral">
            <option value="">Pilih Jenis Nutrisi Parenteral</option>
            @foreach ($intervensiGiziOptions['nutrisiParenteral'] as $option)
                <option value="{{ $option['jenis'] }}">{{ $option['jenis'] }}</option>
            @endforeach
        </select>
        @error('formIntervensiGizi.nutrisiParenteral')
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
