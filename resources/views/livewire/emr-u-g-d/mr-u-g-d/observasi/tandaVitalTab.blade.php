<div>
    <div class="w-full mb-1">

        @role(['Perawat', 'Admin'])
            <div class="pt-0">

                <div>
                    <x-input-label for="observasiLanjutan.waktuPemeriksaan" :value="__('Waktu Pemeriksaan')" :required="__(true)" />

                    <div class="mb-2 ">
                        <div class="flex items-center mb-2 ">
                            <x-text-input id="observasiLanjutan.waktuPemeriksaan"
                                placeholder="Waktu Pemeriksaan [dd/mm/yyyy hh24:mi:ss]" class="mt-1 ml-2" :errorshas="__($errors->has('observasiLanjutan.waktuPemeriksaan'))"
                                :disabled=$disabledPropertyRjStatus
                                wire:model.debounce.500ms="observasiLanjutan.waktuPemeriksaan" />

                            @if (!$observasiLanjutan['waktuPemeriksaan'])
                                <div class="w-1/2 ml-2">
                                    <div wire:loading wire:target="setWaktuPemeriksaan">
                                        <x-loading />
                                    </div>

                                    <x-green-button :disabled=false
                                        wire:click.prevent="setWaktuPemeriksaan('{{ date('d/m/Y H:i:s') }}')" type="button"
                                        wire:loading.remove>
                                        <div wire:poll>

                                            Set Waktu Pemeriksaan: {{ date('d/m/Y H:i:s') }}

                                        </div>
                                    </x-green-button>
                                </div>
                            @endif
                        </div>

                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <x-input-label for="observasiLanjutan.cairan" :value="__('cairan')" :required="__(false)" />
                        <div class="mb-2 ">
                            <x-text-input id="observasiLanjutan.cairan" placeholder="cairan" class="mt-1 ml-2"
                                :errorshas="__($errors->has('observasiLanjutan.cairan'))" :disabled=$disabledPropertyRjStatus
                                wire:model.debounce.500ms="observasiLanjutan.cairan" />

                        </div>
                    </div>

                    <div>
                        <x-input-label for="observasiLanjutan.tetesan" :value="__('tetesan')" :required="__(false)" />
                        <div class="mb-2 ">
                            <x-text-input id="observasiLanjutan.tetesan" placeholder="tetesan" class="mt-1 ml-2"
                                :errorshas="__($errors->has('observasiLanjutan.tetesan'))" :disabled=$disabledPropertyRjStatus
                                wire:model.debounce.500ms="observasiLanjutan.tetesan" />

                        </div>
                    </div>
                </div>

                <x-input-label for="observasiLanjutan.sistolik" :value="__('Tanda Vital')" :required="__(false)"
                    class="pt-2 sm:text-xl" />

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <div class="mb-2 ">
                            <x-input-label for="observasiLanjutan.sistolik" :value="__('Tekanan Darah')" :required="__(false)" />
                            <div class="grid grid-cols-2 gap-2">
                                <x-text-input-mou id="observasiLanjutan.sistolik" placeholder="Sistolik" class="mt-1 ml-2"
                                    :errorshas="__($errors->has('observasiLanjutan.sistolik'))" :disabled=$disabledPropertyRjStatus :mou_label="__('mmHg')"
                                    wire:model.debounce.500ms="observasiLanjutan.sistolik" />
                                <x-text-input-mou id="observasiLanjutan.distolik" placeholder="Distolik" class="mt-1 ml-2"
                                    :errorshas="__($errors->has('observasiLanjutan.distolik'))" :disabled=$disabledPropertyRjStatus :mou_label="__('mmHg')"
                                    wire:model.debounce.500ms="observasiLanjutan.distolik" />
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="mb-2 ">
                            <div class="grid grid-cols-2 gap-2">
                                <x-input-label for="observasiLanjutan.frekuensiNafas" :value="__('Frekuensi Nadi')"
                                    :required="__(false)" />
                                <x-input-label for="observasiLanjutan.frekuensiNafas" :value="__('Frekuensi Nafas')"
                                    :required="__(false)" />
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <x-text-input-mou id="observasiLanjutan.frekuensiNadi" placeholder="Frekuensi Nadi"
                                    class="mt-1 ml-2" :errorshas="__($errors->has('observasiLanjutan.frekuensiNadi'))" :disabled=$disabledPropertyRjStatus
                                    :mou_label="__('X/Menit')" wire:model.debounce.500ms="observasiLanjutan.frekuensiNadi" />
                                <x-text-input-mou id="observasiLanjutan.frekuensiNafas" placeholder="Frekuensi Nafas"
                                    class="mt-1 ml-2" :errorshas="__($errors->has('observasiLanjutan.frekuensiNafas'))" :disabled=$disabledPropertyRjStatus
                                    :mou_label="__('X/Menit')" wire:model.debounce.500ms="observasiLanjutan.frekuensiNafas" />
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="mb-2 ">
                        <div class="grid grid-cols-2 gap-2">
                            <x-input-label for="observasiLanjutan.frekuensiNafas" :value="__('Suhu')" :required="__(false)" />
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <x-text-input-mou id="observasiLanjutan.suhu" placeholder="Suhu" class="mt-1 ml-2"
                                :errorshas="__($errors->has('observasiLanjutan.suhu'))" :disabled=$disabledPropertyRjStatus :mou_label="__('°C')"
                                wire:model.debounce.500ms="observasiLanjutan.suhu" />
                        </div>
                    </div>
                </div>

                <div>
                    <div class="mb-2 ">
                        <div class="grid grid-cols-2 gap-2">
                            <x-input-label for="observasiLanjutan.frekuensiNafas" :value="__('SPO2')" :required="__(false)" />
                            <x-input-label for="observasiLanjutan.frekuensiNafas" :value="__('GDA')" :required="__(false)" />
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <x-text-input-mou id="observasiLanjutan.spo2" placeholder="SPO2" class="mt-1 ml-2"
                                :errorshas="__($errors->has('observasiLanjutan.spo2'))" :disabled=$disabledPropertyRjStatus :mou_label="__('%')"
                                wire:model.debounce.500ms="observasiLanjutan.spo2" />
                            <x-text-input-mou id="observasiLanjutan.gda" placeholder="GDA" class="mt-1 ml-2"
                                :errorshas="__($errors->has('observasiLanjutan.gda'))" :disabled=$disabledPropertyRjStatus :mou_label="__('g/dl')"
                                wire:model.debounce.500ms="observasiLanjutan.gda" />
                        </div>
                    </div>
                </div>


                <div class="grid grid-cols-1 ml-2">
                    <div wire:loading wire:target="addObservasiLanjutan">
                        <x-loading />
                    </div>

                    <x-green-button :disabled=$disabledPropertyRjStatus wire:click.prevent="addObservasiLanjutan()"
                        type="button" wire:loading.remove>
                        Simpan Observasi Lanjutan
                    </x-green-button>
                </div>



            </div>
        @endrole

        <div class="w-full my-2">
            @include('livewire.emr-u-g-d.mr-u-g-d.observasi.tandaVitalTable')
        </div>
    </div>


</div>
