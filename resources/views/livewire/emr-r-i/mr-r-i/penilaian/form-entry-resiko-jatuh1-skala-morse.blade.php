<div class="space-y-4">
    @if ($formEntryResikoJatuh['resikoJatuh']['resikoJatuhMetode']['resikoJatuhMetode'] === 'Skala Morse')
        <!-- Skala Morse -->
        <div class="space-y-4">
            <x-input-label :value="__(
                'Mengidentifikasi risiko jatuh pada pasien dewasa, terutama di rumah sakit atau fasilitas kesehatan.',
            )" />
            @foreach ($skalaMorseOptions as $kategori => $options)
                <div>
                    <!-- Mengubah $kategori menjadi format dengan spasi dan huruf besar di awal -->
                    <x-input-label :value="__(ucwords(str_replace('_', ' ', $kategori)))" :required="true" />
                    <div class="grid grid-cols-4 gap-2 mt-1">
                        @foreach ($options as $option)
                            <x-radio-button :label="$option[$kategori] . '   (Skor: ' . $option['score'] . ')'" :value="$option[$kategori]"
                                wire:model="formEntryResikoJatuh.resikoJatuh.resikoJatuhMetode.dataResikoJatuh.{{ $kategori }}"
                                wire:click="hitungSkorDanKategoriSkalaMorse()" />
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
