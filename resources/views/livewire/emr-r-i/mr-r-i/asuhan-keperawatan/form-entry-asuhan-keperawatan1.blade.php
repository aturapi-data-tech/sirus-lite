<div class="space-y-4">
    <div class="w-full mb-1">
        <div id="TransaksiAsuhanKeperawatan">
            <div class="grid grid-cols-6 gap-2" x-data>
                <!-- Tanggal Asuhan Keperawatan -->
                <div class="col-span-1">
                    <x-input-label for="formEntryAsuhanKeperawatan.tglAsuhanKeperawatan" :value="__('Tanggal')"
                        :required="true" />
                    <div>
                        <div class="flex items-center mb-2">
                            @if (!$formEntryAsuhanKeperawatan['tglAsuhanKeperawatan'])
                                <div class="w-full mt-2 ml-2">
                                    <div wire:loading wire:target="setTglAsuhanKeperawatan">
                                        <x-loading />
                                    </div>
                                    <x-green-button :disabled="false"
                                        wire:click.prevent="setTglAsuhanKeperawatan('{{ date('d/m/Y H:i:s') }}')"
                                        type="button" wire:loading.remove class="w-full">
                                        <div wire:poll.20s>
                                            {{ date('d/m/Y H:i:s') }}
                                        </div>
                                    </x-green-button>
                                </div>
                            @else
                                <x-text-input id="formEntryAsuhanKeperawatan.tglAsuhanKeperawatan" type="text"
                                    placeholder="dd/mm/yyyy hh24:mi:ss" class="mt-1 ml-2" :errorshas="$errors->has('formEntryAsuhanKeperawatan.tglAsuhanKeperawatan')"
                                    wire:model="formEntryAsuhanKeperawatan.tglAsuhanKeperawatan" :disabled="$disabledPropertyRjStatus" />
                            @endif
                        </div>
                        @error('formEntryAsuhanKeperawatan.tglAsuhanKeperawatan')
                            <x-input-error :messages="$message" />
                        @enderror
                    </div>
                </div>

                <!-- LOV Diagnosis Keperawatan -->
                <div class="col-span-2">
                    @if (empty($collectingMyDiagKep))
                        @include('livewire.component.l-o-v.list-of-value-diag-kep.list-of-value-diag-kep')
                    @else
                        <x-input-label for="formEntryAsuhanKeperawatan.diagKepDesc" :value="__('Diagnosis Keperawatan')"
                            :required="true" />
                        <div>
                            <x-text-input id="formEntryAsuhanKeperawatan.diagKepDesc"
                                placeholder="Diagnosis Keperawatan" class="mt-1 ml-2" :errorshas="$errors->has('formEntryAsuhanKeperawatan.diagKepDesc')"
                                wire:model="formEntryAsuhanKeperawatan.diagKepDesc" :disabled="true" />
                        </div>
                    @endif
                    @error('formEntryAsuhanKeperawatan.diagKepId')
                        <x-input-error :messages="$message" />
                    @enderror
                </div>

                <!-- Nama Petugas -->
                {{-- <div class="col-span-1">
                    <x-input-label for="formEntryAsuhanKeperawatan.petugasAsuhanKeperawatan" :value="__('Nama Petugas')"
                        :required="true" />
                    <div>
                        <x-text-input id="formEntryAsuhanKeperawatan.petugasAsuhanKeperawatan"
                            placeholder="Nama Petugas" class="mt-1 ml-2" :errorshas="$errors->has('formEntryAsuhanKeperawatan.petugasAsuhanKeperawatan')"
                            wire:model="formEntryAsuhanKeperawatan.petugasAsuhanKeperawatan" :disabled="$disabledPropertyRjStatus" />
                    </div>
                    @error('formEntryAsuhanKeperawatan.petugasAsuhanKeperawatan')
                        <x-input-error :messages="$message" />
                    @enderror
                </div> --}}

                <!-- Kode Petugas -->
                {{-- <div class="col-span-1">
                    <x-input-label for="formEntryAsuhanKeperawatan.petugasAsuhanKeperawatanCode" :value="__('Kode Petugas')"
                        :required="true" />
                    <div>
                        <x-text-input id="formEntryAsuhanKeperawatan.petugasAsuhanKeperawatanCode"
                            placeholder="Kode Petugas" class="mt-1 ml-2" :errorshas="$errors->has('formEntryAsuhanKeperawatan.petugasAsuhanKeperawatanCode')"
                            wire:model="formEntryAsuhanKeperawatan.petugasAsuhanKeperawatanCode" :disabled="$disabledPropertyRjStatus" />
                    </div>
                    @error('formEntryAsuhanKeperawatan.petugasAsuhanKeperawatanCode')
                        <x-input-error :messages="$message" />
                    @enderror
                </div> --}}

                <!-- Tombol Reset / Hapus -->
                <div class="col-span-1">
                    <x-input-label for="" :value="__('Hapus')" :required="true" />
                    <x-alternative-button class="inline-flex ml-2"
                        wire:click.prevent="resetFormEntryAsuhanKeperawatan()">
                        <svg class="w-5 h-5 text-gray-800 dark:text-white" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                            <path
                                d="M17 4h-4V2a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v2H1a1 1 0 0 0 0 2h1v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6h1a1 1 0 1 0 0-2ZM7 2h4v2H7V2Zm1 14a1 1 0 1 1-2 0V8a1 1 0 0 1 2 0v8Zm4 0a1 1 0 0 1-2 0V8a1 1 0 0 1 2 0v8Z" />
                        </svg>
                    </x-alternative-button>
                </div>
            </div>

            {{-- Form Diagnosa Keperawatan --}}
            @if ($formEntryAsuhanKeperawatan['diagKepJson'])
                <div>
                    @php
                        $diagnosa_keperawatan = json_decode($formEntryAsuhanKeperawatan['diagKepJson'], true) ?? [];
                    @endphp
                    @include('livewire.emr-r-i.mr-r-i.asuhan-keperawatan.form-entry-diagnosa-keperawatan')
                </div>
            @endif

            <!-- Tombol Simpan -->
            <div class="w-full mt-4">
                <div wire:loading wire:target="addAsuhanKeperawatan">
                    <x-loading />
                </div>
                <x-primary-button :disabled="false" wire:click.prevent="addAsuhanKeperawatan()" type="button"
                    wire:loading.remove class="w-full">
                    Simpan Asuhan Keperawatan
                </x-primary-button>
            </div>
        </div>
    </div>

    <!-- Tabel Data Asuhan Keperawatan -->
    @include('livewire.emr-r-i.mr-r-i.asuhan-keperawatan.form-entry-asuhan-keperawatan2-table')
</div>
