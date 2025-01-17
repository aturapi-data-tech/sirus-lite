<div class="space-y-4">
    @if ($formEntryResikoJatuh['resikoJatuh']['resikoJatuhMetode']['resikoJatuhMetode'] === 'Skala Humpty Dumpty')
        <!-- Skala Humpty Dumpty -->
        <div class="space-y-4">
            <!-- Judul Penjelasan -->
            <x-input-label :value="__(
                'Mengidentifikasi risiko jatuh pada anak-anak, terutama di rumah sakit atau fasilitas pediatrik.',
            )" />

            <!-- Loop melalui setiap kategori dalam Skala Humpty Dumpty -->
            @foreach ($humptyDumptyOptions as $kategori => $options)
                <div>
                    <!-- Label Kategori -->
                    <x-input-label :value="__(ucwords(str_replace('_', ' ', $kategori)))" :required="true" />

                    <!-- Opsi Radio Button -->
                    <div class="grid grid-cols-4 gap-2 mt-1">
                        @foreach ($options as $option)
                            <x-radio-button :label="$option[$kategori] . '   (Skor: ' . $option['score'] . ')'" :value="$option[$kategori]"
                                wire:model="formEntryResikoJatuh.resikoJatuh.resikoJatuhMetode.dataResikoJatuh.{{ $kategori }}"
                                wire:click="hitungSkorDanKategoriSkalaHumptyDumpty()" />
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
