<div>
    <div class="w-full mb-1">
        @role(['Perawat', 'Admin'])
            <div class="pt-0">
                <div>
                    <x-input-label for="pemakaianOksigen.tanggalWaktuMulai" :value="__('Tanggal Waktu Mulai')" :required="__(true)" />
                    <div class="mb-2">
                        <div class="flex items-center mb-2">
                            <x-text-input id="pemakaianOksigen.tanggalWaktuMulai"
                                placeholder="Tanggal Waktu Mulai [dd/mm/yyyy hh24:mi:ss]" class="mt-1 ml-2" :errorshas="__($errors->has('pemakaianOksigen.tanggalWaktuMulai'))"
                                :disabled=$disabledPropertyRjStatus
                                wire:model.debounce.500ms="pemakaianOksigen.tanggalWaktuMulai" />
                            @if (!$pemakaianOksigen['tanggalWaktuMulai'])
                                <div class="w-1/2 ml-2">
                                    <x-green-button :disabled=false
                                        wire:click.prevent="setTanggalWaktuMulai('{{ date('d/m/Y H:i:s') }}')"
                                        type="button">
                                        Set Tanggal Waktu Mulai: {{ date('d/m/Y H:i:s') }}
                                    </x-green-button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div>
                    <x-input-label for="pemakaianOksigen.jenisAlatOksigen" :value="__('Jenis Alat Oksigen')" :required="__(true)" />
                    <div class="space-y-2">
                        <div class="grid grid-cols-2 gap-2">
                            @foreach (['Nasal Kanul', 'Masker Sederhana', 'Ventilator Non-Invasif', 'Lainnya'] as $alat)
                                <x-radio-button :label="$alat" :value="$alat"
                                    wire:model="pemakaianOksigen.jenisAlatOksigen" />
                            @endforeach
                        </div>
                    </div>
                    @if ($pemakaianOksigen['jenisAlatOksigen'] === 'Lainnya')
                        <x-text-input id="pemakaianOksigen.jenisAlatOksigenDetail" placeholder="Detail Alat Oksigen"
                            class="mt-1 ml-2" wire:model="pemakaianOksigen.jenisAlatOksigenDetail" />
                    @endif
                </div>

                <div>
                    <x-input-label for="pemakaianOksigen.dosisOksigen" :value="__('Dosis Oksigen')" :required="__(true)" />
                    <div class="space-y-2">
                        <div class="grid grid-cols-2 gap-2">
                            @foreach (['1-2 L/menit', '3-4 L/menit', '2-6 L/menit (Nasal Kanul)', '5-10 L/menit (Masker)', 'Lainnya'] as $dosis)
                                <x-radio-button :label="$dosis" :value="$dosis"
                                    wire:model="pemakaianOksigen.dosisOksigen" />
                            @endforeach
                        </div>
                    </div>
                    @if ($pemakaianOksigen['dosisOksigen'] === 'Lainnya')
                        <x-text-input id="pemakaianOksigen.dosisOksigenDetail" placeholder="Detail Dosis Oksigen"
                            class="mt-1 ml-2" wire:model="pemakaianOksigen.dosisOksigenDetail" />
                    @endif
                </div>

                <div>
                    <x-input-label for="pemakaianOksigen.durasiPenggunaan" :value="__('Durasi Penggunaan')" :required="__(false)" />
                    <div class="space-y-2">
                        <div class="grid grid-cols-2 gap-2">
                            @foreach (['Kontinu', 'Intermiten'] as $durasi)
                                <x-radio-button :label="$durasi" :value="$durasi"
                                    wire:model="pemakaianOksigen.durasiPenggunaan" />
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 mt-2 ml-2">
                    <x-green-button :disabled=$disabledPropertyRjStatus wire:click.prevent="addPemakaianOksigen()"
                        type="button">
                        Simpan Pemakaian Oksigen
                    </x-green-button>
                </div>
            </div>
        @endrole

        <div class="w-full my-2">
            @include('livewire.emr-r-i.mr-r-i.pemakaian-oksigen.pemakaian-oksigen-table')
        </div>
    </div>
</div>
