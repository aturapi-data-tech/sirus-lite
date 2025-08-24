<div>
    @php
        /** PROPS dari parent (disarankan):
         * :telaahObatHdr="data_get($dataDaftarRi, 'eresepHdr.'.$resepHeaderIndexForEdit.'.telaahObat')"
         * :bindPath="'dataDaftarRi.eresepHdr.'.$resepHeaderIndexForEdit.'.telaahObat'"
         * :disabled="$disabledPropertyRjStatusObat"
         * :ttdAction="'setttdTelaahObat('.$riHdrNoRef.','.$idx.')'"
         */

        // Ambil header aktif (bisa null)
        $hdr = $telaahObatHdr ?? [];

        // Path wire:model yang final (sudah dibentuk di parent)
        $bind = isset($bindPath) && is_string($bindPath) ? $bindPath : 'dataDaftarRi.telaahObat';

        // Disabled jika sudah TTD atau flag lain
        $disabled = isset($disabled) ? (bool) $disabled : false;

        // Nama penanggung jawab bila sudah TTD
        $penanggung = data_get($hdr, 'penanggungJawab.userLog');

        // Ambil options dengan aman (fallback ke array sederhana Ya/Tidak)
        $opsObat = data_get($hdr, 'obatdgnResep.obatdgnResepOptions', []) ?: [
            ['obatdgnResep' => 'Ya'],
            ['obatdgnResep' => 'Tidak'],
        ];
        $opsDosis = data_get($hdr, 'jmlDosisdgnResep.jmlDosisdgnResepOptions', []) ?: [
            ['jmlDosisdgnResep' => 'Ya'],
            ['jmlDosisdgnResep' => 'Tidak'],
        ];
        $opsRute = data_get($hdr, 'rutedgnResep.rutedgnResepOptions', []) ?: [
            ['rutedgnResep' => 'Ya'],
            ['rutedgnResep' => 'Tidak'],
        ];
        $opsWaktu = data_get($hdr, 'waktuFrekPemberiandgnResep.waktuFrekPemberiandgnResepOptions', []) ?: [
            ['waktuFrekPemberiandgnResep' => 'Ya'],
            ['waktuFrekPemberiandgnResep' => 'Tidak'],
        ];

        // Jika sudah TTD, paksa disabled
        if ($penanggung) {
            $disabled = true;
        }
    @endphp

    <div class="flex justify-between">
        <x-input-label :value="__('Telaah Obat')" :required="false" class="pt-2 sm:text-xl" />

        @role(['Apoteker', 'Admin'])
            @if ($penanggung)
                <x-badge badgecolor="green">{{ $penanggung }}</x-badge>
            @else
                {{-- PENTING: wire:click pakai string biasa --}}
                <x-green-button :disabled="$disabledPropertyRiStatusObat"
                wire:click.prevent="setttdTelaahObat(@js($dataDaftarRi['riHdrNo']), @js($resepHeaderIndexForEdit))">
                TTD Telaah Obat
            </x-green-button>
            @endif
        @endrole
    </div>

    {{-- Obat dengan resep/pesanan --}}
    <div class="pt-2">
        <x-input-label :value="__('Obat dengan resep / pesanan')" :required="true" />
        <div class="flex flex-wrap items-center gap-3 mt-2 ml-2">
            @foreach ($opsObat as $i => $opt)
                @php $val = $opt['obatdgnResep'] ?? ''; @endphp
                <x-radio-button :disabled="$disabled" :label="$val" value="{{ $val }}"
                    wire:model="{{ $bind }}.obatdgnResep.obatdgnResep"
                    wire:key="obatdgnResep-{{ $i }}-{{ $val }}" />
            @endforeach

            <x-text-input placeholder="Keterangan" class="w-full mt-1 md:w-1/2" :disabled="$disabled"
                wire:model.debounce.500ms="{{ $bind }}.obatdgnResep.desc" />
        </div>
    </div>

    {{-- Jumlah/Dosis --}}
    <div class="pt-2">
        <x-input-label :value="__('Jumlah / Dosis dengan resep / pesanan')" :required="true" />
        <div class="flex flex-wrap items-center gap-3 mt-2 ml-2">
            @foreach ($opsDosis as $i => $opt)
                @php $val = $opt['jmlDosisdgnResep'] ?? ''; @endphp
                <x-radio-button :disabled="$disabled" :label="$val" value="{{ $val }}"
                    wire:model="{{ $bind }}.jmlDosisdgnResep.jmlDosisdgnResep"
                    wire:key="jmlDosisdgnResep-{{ $i }}-{{ $val }}" />
            @endforeach

            <x-text-input placeholder="Keterangan" class="w-full mt-1 md:w-1/2" :disabled="$disabled"
                wire:model.debounce.500ms="{{ $bind }}.jmlDosisdgnResep.desc" />
        </div>
    </div>

    {{-- Rute --}}
    <div class="pt-2">
        <x-input-label :value="__('Rute dengan resep / pesanan')" :required="true" />
        <div class="flex flex-wrap items-center gap-3 mt-2 ml-2">
            @foreach ($opsRute as $i => $opt)
                @php $val = $opt['rutedgnResep'] ?? ''; @endphp
                <x-radio-button :disabled="$disabled" :label="$val" value="{{ $val }}"
                    wire:model="{{ $bind }}.rutedgnResep.rutedgnResep"
                    wire:key="rutedgnResep-{{ $i }}-{{ $val }}" />
            @endforeach

            <x-text-input placeholder="Keterangan" class="w-full mt-1 md:w-1/2" :disabled="$disabled"
                wire:model.debounce.500ms="{{ $bind }}.rutedgnResep.desc" />
        </div>
    </div>

    {{-- Waktu & Frekuensi --}}
    <div class="pt-2">
        <x-input-label :value="__('Waktu & Frekuensi pemberian dengan resep / pesanan')" :required="true" />
        <div class="flex flex-wrap items-center gap-3 mt-2 ml-2">
            @foreach ($opsWaktu as $i => $opt)
                @php $val = $opt['waktuFrekPemberiandgnResep'] ?? ''; @endphp
                <x-radio-button :disabled="$disabled" :label="$val" value="{{ $val }}"
                    wire:model="{{ $bind }}.waktuFrekPemberiandgnResep.waktuFrekPemberiandgnResep"
                    wire:key="waktuFrekPemberiandgnResep-{{ $i }}-{{ $val }}" />
            @endforeach

            <x-text-input placeholder="Keterangan" class="w-full mt-1 md:w-1/2" :disabled="$disabled"
                wire:model.debounce.500ms="{{ $bind }}.waktuFrekPemberiandgnResep.desc" />
        </div>
    </div>
</div>
