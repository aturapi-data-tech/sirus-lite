<div>
    @php
        /** Disarankan dari parent:
         * :telaahResepHdr="data_get($dataDaftarRi, 'eresepHdr.'.$resepHeaderIndexForEdit.'.telaahResep')"
         * :bindPath="'dataDaftarRi.eresepHdr.'.$resepHeaderIndexForEdit.'.telaahResep'"
         * :disabled="$disabledPropertyRjStatusObat"

         */

        $hdr = $telaahResepHdr ?? [];
        $bind = isset($bindPath) && is_string($bindPath) ? $bindPath : 'dataDaftarRi.telaahResep';
        $disabled = isset($disabled) ? (bool) $disabled : false;
        $penanggung = data_get($hdr, 'penanggungJawab.userLog');

        // Fallback opsi Ya/Tidak bila belum ada di header
        $opsKejelasan = data_get($hdr, 'kejelasanTulisanResep.kejelasanTulisanResepOptions', []) ?: [
            ['kejelasanTulisanResep' => 'Ya'],
            ['kejelasanTulisanResep' => 'Tidak'],
        ];
        $opsTepatObat = data_get($hdr, 'tepatObat.tepatObatOptions', []) ?: [
            ['tepatObat' => 'Ya'],
            ['tepatObat' => 'Tidak'],
        ];
        $opsTepatDosis = data_get($hdr, 'tepatDosis.tepatDosisOptions', []) ?: [
            ['tepatDosis' => 'Ya'],
            ['tepatDosis' => 'Tidak'],
        ];
        $opsTepatRute = data_get($hdr, 'tepatRute.tepatRuteOptions', []) ?: [
            ['tepatRute' => 'Ya'],
            ['tepatRute' => 'Tidak'],
        ];
        $opsTepatWaktu = data_get($hdr, 'tepatWaktu.tepatWaktuOptions', []) ?: [
            ['tepatWaktu' => 'Ya'],
            ['tepatWaktu' => 'Tidak'],
        ];
        $opsDuplikasi = data_get($hdr, 'duplikasi.duplikasiOptions', []) ?: [
            ['duplikasi' => 'Ya'],
            ['duplikasi' => 'Tidak'],
        ];
        $opsAlergi = data_get($hdr, 'alergi.alergiOptions', []) ?: [['alergi' => 'Ya'], ['alergi' => 'Tidak']];
        $opsInteraksi = data_get($hdr, 'interaksiObat.interaksiObatOptions', []) ?: [
            ['interaksiObat' => 'Ya'],
            ['interaksiObat' => 'Tidak'],
        ];
        $opsBBAnak = data_get($hdr, 'bbPasienAnak.bbPasienAnakOptions', []) ?: [
            ['bbPasienAnak' => 'Ya'],
            ['bbPasienAnak' => 'Tidak'],
        ];
        $opsKontra = data_get($hdr, 'kontraIndikasiLain.kontraIndikasiLainOptions', []) ?: [
            ['kontraIndikasiLain' => 'Ya'],
            ['kontraIndikasiLain' => 'Tidak'],
        ];

        // paksa disabled bila sudah TTD
        if ($penanggung) {
            $disabled = true;
        }
    @endphp

    <div class="flex justify-between">
        <x-input-label :value="__('Telaah Resep')" :required="false" class="pt-2 sm:text-xl" />
        @role(['Apoteker', 'Admin'])
            @if ($penanggung)
                <x-badge badgecolor="green">{{ $penanggung }}</x-badge>
            @else
                <x-green-button :disabled="$disabledPropertyRiStatusResep"
                    wire:click.prevent="setttdTelaahResep('{{ $dataDaftarRi['riHdrNo'] }}', '{{ $resepHeaderIndexForEdit }}')">
                    TTD Telaah Resep
                </x-green-button>
            @endif
        @endrole
    </div>

    {{-- Kejelasan tulisan --}}
    <div class="pt-2">
        <x-input-label :value="__('Kejelasan Tulisan Resep')" :required="true" />
        <div class="flex flex-wrap items-center gap-3 mt-2 ml-2">
            @foreach ($opsKejelasan as $i => $opt)
                @php $val = $opt['kejelasanTulisanResep'] ?? ''; @endphp
                <x-radio-button :disabled="$disabled" :label="$val" value="{{ $val }}"
                    wire:model="{{ $bind }}.kejelasanTulisanResep.kejelasanTulisanResep"
                    wire:key="kejelasanTulisanResep-{{ $i }}-{{ $val }}" />
            @endforeach
            <x-text-input class="w-full mt-1 md:w-1/2" placeholder="Keterangan" :disabled="$disabled"
                wire:model.debounce.500ms="{{ $bind }}.kejelasanTulisanResep.desc" />
        </div>
    </div>

    {{-- Tepat Obat --}}
    <div class="pt-2">
        <x-input-label :value="__('Tepat Obat')" :required="true" />
        <div class="flex flex-wrap items-center gap-3 mt-2 ml-2">
            @foreach ($opsTepatObat as $i => $opt)
                @php $val = $opt['tepatObat'] ?? ''; @endphp
                <x-radio-button :disabled="$disabled" :label="$val" value="{{ $val }}"
                    wire:model="{{ $bind }}.tepatObat.tepatObat"
                    wire:key="tepatObat-{{ $i }}-{{ $val }}" />
            @endforeach
            <x-text-input class="w-full mt-1 md:w-1/2" placeholder="Keterangan" :disabled="$disabled"
                wire:model.debounce.500ms="{{ $bind }}.tepatObat.desc" />
        </div>
    </div>

    {{-- Tepat Dosis --}}
    <div class="pt-2">
        <x-input-label :value="__('Tepat Dosis')" :required="true" />
        <div class="flex flex-wrap items-center gap-3 mt-2 ml-2">
            @foreach ($opsTepatDosis as $i => $opt)
                @php $val = $opt['tepatDosis'] ?? ''; @endphp
                <x-radio-button :disabled="$disabled" :label="$val" value="{{ $val }}"
                    wire:model="{{ $bind }}.tepatDosis.tepatDosis"
                    wire:key="tepatDosis-{{ $i }}-{{ $val }}" />
            @endforeach
            <x-text-input class="w-full mt-1 md:w-1/2" placeholder="Keterangan" :disabled="$disabled"
                wire:model.debounce.500ms="{{ $bind }}.tepatDosis.desc" />
        </div>
    </div>

    {{-- Tepat Rute --}}
    <div class="pt-2">
        <x-input-label :value="__('Tepat Rute')" :required="true" />
        <div class="flex flex-wrap items-center gap-3 mt-2 ml-2">
            @foreach ($opsTepatRute as $i => $opt)
                @php $val = $opt['tepatRute'] ?? ''; @endphp
                <x-radio-button :disabled="$disabled" :label="$val" value="{{ $val }}"
                    wire:model="{{ $bind }}.tepatRute.tepatRute"
                    wire:key="tepatRute-{{ $i }}-{{ $val }}" />
            @endforeach
            <x-text-input class="w-full mt-1 md:w-1/2" placeholder="Keterangan" :disabled="$disabled"
                wire:model.debounce.500ms="{{ $bind }}.tepatRute.desc" />
        </div>
    </div>

    {{-- Tepat Waktu --}}
    <div class="pt-2">
        <x-input-label :value="__('Tepat Waktu')" :required="true" />
        <div class="flex flex-wrap items-center gap-3 mt-2 ml-2">
            @foreach ($opsTepatWaktu as $i => $opt)
                @php $val = $opt['tepatWaktu'] ?? ''; @endphp
                <x-radio-button :disabled="$disabled" :label="$val" value="{{ $val }}"
                    wire:model="{{ $bind }}.tepatWaktu.tepatWaktu"
                    wire:key="tepatWaktu-{{ $i }}-{{ $val }}" />
            @endforeach
            <x-text-input class="w-full mt-1 md:w-1/2" placeholder="Keterangan" :disabled="$disabled"
                wire:model.debounce.500ms="{{ $bind }}.tepatWaktu.desc" />
        </div>
    </div>

    {{-- Duplikasi --}}
    <div class="pt-2">
        <x-input-label :value="__('Duplikasi')" :required="true" />
        <div class="flex flex-wrap items-center gap-3 mt-2 ml-2">
            @foreach ($opsDuplikasi as $i => $opt)
                @php $val = $opt['duplikasi'] ?? ''; @endphp
                <x-radio-button :disabled="$disabled" :label="$val" value="{{ $val }}"
                    wire:model="{{ $bind }}.duplikasi.duplikasi"
                    wire:key="duplikasi-{{ $i }}-{{ $val }}" />
            @endforeach
            <x-text-input class="w-full mt-1 md:w-1/2" placeholder="Keterangan" :disabled="$disabled"
                wire:model.debounce.500ms="{{ $bind }}.duplikasi.desc" />
        </div>
    </div>

    {{-- Alergi --}}
    <div class="pt-2">
        <x-input-label :value="__('Alergi')" :required="true" />
        <div class="flex flex-wrap items-center gap-3 mt-2 ml-2">
            @foreach ($opsAlergi as $i => $opt)
                @php $val = $opt['alergi'] ?? ''; @endphp
                <x-radio-button :disabled="$disabled" :label="$val" value="{{ $val }}"
                    wire:model="{{ $bind }}.alergi.alergi"
                    wire:key="alergi-{{ $i }}-{{ $val }}" />
            @endforeach
            <x-text-input class="w-full mt-1 md:w-1/2" placeholder="Keterangan" :disabled="$disabled"
                wire:model.debounce.500ms="{{ $bind }}.alergi.desc" />
        </div>
    </div>

    {{-- Interaksi Obat --}}
    <div class="pt-2">
        <x-input-label :value="__('Interaksi Obat')" :required="true" />
        <div class="flex flex-wrap items-center gap-3 mt-2 ml-2">
            @foreach ($opsInteraksi as $i => $opt)
                @php $val = $opt['interaksiObat'] ?? ''; @endphp
                <x-radio-button :disabled="$disabled" :label="$val" value="{{ $val }}"
                    wire:model="{{ $bind }}.interaksiObat.interaksiObat"
                    wire:key="interaksiObat-{{ $i }}-{{ $val }}" />
            @endforeach
            <x-text-input class="w-full mt-1 md:w-1/2" placeholder="Keterangan" :disabled="$disabled"
                wire:model.debounce.500ms="{{ $bind }}.interaksiObat.desc" />
        </div>
    </div>

    {{-- BB Pasien Anak --}}
    <div class="pt-2">
        <x-input-label :value="__('Berat Badan Pasien Anak')" :required="true" />
        <div class="flex flex-wrap items-center gap-3 mt-2 ml-2">
            @foreach ($opsBBAnak as $i => $opt)
                @php $val = $opt['bbPasienAnak'] ?? ''; @endphp
                <x-radio-button :disabled="$disabled" :label="$val" value="{{ $val }}"
                    wire:model="{{ $bind }}.bbPasienAnak.bbPasienAnak"
                    wire:key="bbPasienAnak-{{ $i }}-{{ $val }}" />
            @endforeach
            <x-text-input class="w-full mt-1 md:w-1/2" placeholder="Keterangan" :disabled="$disabled"
                wire:model.debounce.500ms="{{ $bind }}.bbPasienAnak.desc" />
        </div>
    </div>

    {{-- Kontra Indikasi Lain --}}
    <div class="pt-2">
        <x-input-label :value="__('Kontra Indikasi Lain')" :required="true" />
        <div class="flex flex-wrap items-center gap-3 mt-2 ml-2">
            @foreach ($opsKontra as $i => $opt)
                @php $val = $opt['kontraIndikasiLain'] ?? ''; @endphp
                <x-radio-button :disabled="$disabled" :label="$val" value="{{ $val }}"
                    wire:model="{{ $bind }}.kontraIndikasiLain.kontraIndikasiLain"
                    wire:key="kontraIndikasiLain-{{ $i }}-{{ $val }}" />
            @endforeach
            <x-text-input class="w-full mt-1 md:w-1/2" placeholder="Keterangan" :disabled="$disabled"
                wire:model.debounce.500ms="{{ $bind }}.kontraIndikasiLain.desc" />
        </div>
    </div>
</div>
