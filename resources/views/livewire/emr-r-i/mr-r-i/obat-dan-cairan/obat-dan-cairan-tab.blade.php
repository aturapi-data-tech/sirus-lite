<div>
    <div class="w-full mb-1">

        @role(['Perawat', 'Admin'])
            <div class="pt-0">

                <div>
                    <x-input-label for="obatDanCairan.waktuPemberian" :value="__('Waktu Pemberian')" :required="__(true)" />

                    <div class="mb-2 ">
                        <div class="flex items-center mb-2 ">
                            <x-text-input id="obatDanCairan.waktuPemberian"
                                placeholder="Waktu Pemberian [dd/mm/yyyy hh24:mi:ss]" class="mt-1 ml-2" :errorshas="__($errors->has('obatDanCairan.waktuPemberian'))"
                                :disabled=$disabledPropertyRjStatus
                                wire:model.debounce.500ms="obatDanCairan.waktuPemberian" />
                            @if (!$obatDanCairan['waktuPemberian'])
                                <div class="w-1/2 ml-2">
                                    <div wire:loading wire:target="setWaktuPemberian">
                                        <x-loading />
                                    </div>

                                    <x-green-button :disabled=false
                                        wire:click.prevent="setWaktuPemberian('{{ date('d/m/Y H:i:s') }}')" type="button"
                                        wire:loading.remove>
                                        <div wire:poll.20s>

                                            Set Waktu Pemberian: {{ date('d/m/Y H:i:s') }}

                                        </div>
                                    </x-green-button>
                                </div>
                            @endif
                        </div>

                    </div>
                </div>

                <div>
                    <div class="mb-2 ">
                        <div class="grid grid-cols-7 gap-2">

                            <div class="col-span-2">
                                <x-input-label for="obatDanCairan.namaObatAtauJenisCairan" :value="__('Nama Obat Atau Jenis Cairan')"
                                    :required="__(false)" />
                            </div>
                            <div>
                                <x-input-label for="obatDanCairan.jumlah" :value="__('Jumlah')" :required="__(false)" />
                            </div>
                            <div>
                                <x-input-label for="obatDanCairan.dosis" :value="__('Dosis')" :required="__(false)" />
                            </div>
                            <div>
                                <x-input-label for="obatDanCairan.rute" :value="__('Rute')" :required="__(false)" />
                            </div>
                            <div class="col-span-2">
                                <x-input-label for="obatDanCairan.keterangan" :value="__('Keterangan')" :required="__(false)" />
                            </div>

                        </div>

                        <div class="grid grid-cols-7 gap-2">
                            @if (!$collectingMyProduct)
                                <div class="col-span-2">
                                    @include('livewire.emr-r-i.mr-r-i.obat-dan-cairan.obat-dan-cairan-lov')
                                </div>
                            @else
                                <div class="col-span-2">
                                    <x-text-input id="obatDanCairan.namaObatAtauJenisCairan"
                                        placeholder="Nama Obat Atau Jenis Cairan" class="mt-1 ml-2" :errorshas="__($errors->has('obatDanCairan.namaObatAtauJenisCairan'))"
                                        :disabled=$disabledPropertyRjStatus
                                        wire:model.debounce.500ms="obatDanCairan.namaObatAtauJenisCairan" />
                                </div>
                            @endif
                            <div>
                                <x-text-input id="obatDanCairan.jumlah" placeholder="Jumlah" class="mt-1 ml-2"
                                    :errorshas="__($errors->has('obatDanCairan.jumlah'))" :disabled=$disabledPropertyRjStatus
                                    wire:model.debounce.500ms="obatDanCairan.jumlah" />
                            </div>
                            <div>
                                <x-text-input id="obatDanCairan.dosis" placeholder="Dosis" class="mt-1 ml-2"
                                    :errorshas="__($errors->has('obatDanCairan.dosis'))" :disabled=$disabledPropertyRjStatus
                                    wire:model.debounce.500ms="obatDanCairan.dosis" />
                            </div>
                            <div>
                                <x-text-input id="obatDanCairan.rute" placeholder="Rute" class="mt-1 ml-2"
                                    :errorshas="__($errors->has('obatDanCairan.rute'))" :disabled=$disabledPropertyRjStatus
                                    wire:model.debounce.500ms="obatDanCairan.rute" />
                            </div>
                            <div class="col-span-2">
                                <x-text-input id="obatDanCairan.keterangan" placeholder="Keterangan" class="mt-1 ml-2"
                                    :errorshas="__($errors->has('obatDanCairan.keterangan'))" :disabled=$disabledPropertyRjStatus
                                    wire:model.debounce.500ms="obatDanCairan.keterangan" />
                            </div>
                        </div>
                    </div>
                </div>


                <div class="grid grid-cols-1 ml-2">
                    <div wire:loading wire:target="addObatDanCairan">
                        <x-loading />
                    </div>

                    <x-green-button :disabled=$disabledPropertyRjStatus wire:click.prevent="addObatDanCairan()"
                        type="button" wire:loading.remove>
                        Simpan Pemberian Obat Atau Jenis Cairan
                    </x-green-button>
                </div>



            </div>
        @endrole

        <div class="w-full my-2">
            @include('livewire.emr-r-i.mr-r-i.obat-dan-cairan.obat-dan-cairan-table')
        </div>
    </div>


</div>
