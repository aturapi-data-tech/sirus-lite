<div class="space-y-4">
    <!-- SOAP -->
    <div class="grid grid-cols-4 gap-2">
        <!-- Subjective -->
        <div class="">
            <x-input-label for="subjective" :value="__('Subjective')" :required="true" />
            <x-text-input-area id="subjective" wire:model.debounce.500ms="formEntryCPPT.soap.subjective" :errorshas="$errors->has('formEntryCPPT.soap.subjective')"
                :disabled="$disabledPropertyRjStatus" />
            @error('formEntryCPPT.soap.subjective')
                <x-input-error :messages="__($message)" />
            @enderror
        </div>

        <!-- Objective -->
        <div class="">
            <x-input-label for="objective" :value="__('Objective')" :required="true" />
            <x-text-input-area id="objective" wire:model.debounce.500ms="formEntryCPPT.soap.objective" :errorshas="$errors->has('formEntryCPPT.soap.objective')"
                :disabled="$disabledPropertyRjStatus" />
            @error('formEntryCPPT.soap.objective')
                <x-input-error :messages="__($message)" />
            @enderror
        </div>

        <!-- Assessment -->
        <div class="">
            <x-input-label for="assessment" :value="__('Assessment')" :required="true" />
            <x-text-input-area id="assessment" wire:model.debounce.500ms="formEntryCPPT.soap.assessment"
                :errorshas="$errors->has('formEntryCPPT.soap.assessment')" :disabled="$disabledPropertyRjStatus" />
            @error('formEntryCPPT.soap.assessment')
                <x-input-error :messages="__($message)" />
            @enderror
        </div>

        <!-- Plan -->
        <div class="">
            <x-input-label for="plan" :value="__('Plan')" :required="true" />
            <x-text-input-area id="plan" wire:model.debounce.500ms="formEntryCPPT.soap.plan" :errorshas="$errors->has('formEntryCPPT.soap.plan')"
                :disabled="$disabledPropertyRjStatus" />
            @error('formEntryCPPT.soap.plan')
                <x-input-error :messages="__($message)" />
            @enderror

            @role(['Dokter', 'Admin'])
                <div class="grid grid-cols-1 gap-2 mt-2">
                    <x-yellow-button :disabled="false" wire:click="openModalEresepRI" type="button" wire:loading.remove>
                        E-resep
                    </x-yellow-button>

                    <div wire:loading wire:target="openModalEresepRI">
                        <x-loading />
                    </div>

                    @if ($isOpenEresepRI)
                        @include('livewire.emr-r-i.create-emr-r-i-racikan-nonracikan')
                    @endif
                </div>
            @endrole
        </div>


    </div>

    <!-- Instruksi dan Review -->
    <div class="grid grid-cols-2 gap-2">
        <!-- Instruksi -->
        <div>
            <x-input-label for="instruction" :value="__('Instruksi')" :required="false" />
            <x-text-input-area id="instruction" wire:model.debounce.500ms="formEntryCPPT.instruction" :errorshas="$errors->has('formEntryCPPT.instruction')"
                :disabled="$disabledPropertyRjStatus" />
            @error('formEntryCPPT.instruction')
                <x-input-error :messages="__($message)" />
            @enderror
        </div>

        <!-- Review -->
        <div>
            <x-input-label for="review" :value="__('Review')" :required="false" />
            <x-text-input-area id="review" wire:model.debounce.500ms="formEntryCPPT.review" :errorshas="$errors->has('formEntryCPPT.review')"
                :disabled="$disabledPropertyRjStatus" />
            @error('formEntryCPPT.review')
                <x-input-error :messages="__($message)" />
            @enderror
        </div>
    </div>

    <!-- Tanggal CPPT -->
    <div class="mt-2">
        <x-input-label for="formEntryCPPT.tglCPPT" :value="__('Tanggal CPPT')" :required="true" />
        <div class="flex items-center mb-2">
            <!-- Input Tanggal CPPT -->
            <x-text-input id="formEntryCPPT.tglCPPT" placeholder="dd/mm/yyyy hh24:mi:ss"
                wire:model.debounce.500ms="formEntryCPPT.tglCPPT" :errorshas="$errors->has('formEntryCPPT.tglCPPT')" :disabled="$disabledPropertyRjStatus" />

            <!-- Tombol Set Tanggal CPPT -->
            @if (!$formEntryCPPT['tglCPPT'])
                <div class="w-1/2 ml-2">
                    <!-- Loading Indicator -->
                    <div wire:loading wire:target="setTglCPPT">
                        <x-loading />
                    </div>

                    <!-- Tombol Set Tanggal -->
                    <x-green-button :disabled="false" wire:click.prevent="setTglCPPT('{{ date('d/m/Y H:i:s') }}')"
                        type="button" wire:loading.remove>
                        <div wire:poll.20s>
                            Set Tanggal CPPT: {{ date('d/m/Y H:i:s') }}
                        </div>
                    </x-green-button>
                </div>
            @endif
        </div>
        @error('formEntryCPPT.tglCPPT')
            <x-input-error :messages="__($message)" />
        @enderror
    </div>

    <!-- Nama Petugas CPPT -->
    {{-- <div>
        <x-input-label for="petugasCPPT" :value="__('Nama Petugas CPPT')" :required="true" />
        <x-text-input id="petugasCPPT" wire:model.debounce.500ms="formEntryCPPT.petugasCPPT" :errorshas="$errors->has('formEntryCPPT.petugasCPPT')"
            :disabled="$disabledPropertyRjStatus" />
        @error('formEntryCPPT.petugasCPPT')
            <x-input-error :messages="__($message)" />
        @enderror
    </div> --}}

    <!-- Kode Petugas CPPT -->
    {{-- <div>
        <x-input-label for="petugasCPPTCode" :value="__('Kode Petugas CPPT')" :required="true" />
        <x-text-input id="petugasCPPTCode" wire:model.debounce.500ms="formEntryCPPT.petugasCPPTCode" :errorshas="$errors->has('formEntryCPPT.petugasCPPTCode')"
            :disabled="$disabledPropertyRjStatus" />
        @error('formEntryCPPT.petugasCPPTCode')
            <x-input-error :messages="__($message)" />
        @enderror
    </div> --}}

    <!-- Profesi Petugas -->
    {{-- <div>
        <x-input-label for="profession" :value="__('Profesi Petugas')" :required="true" />
        <x-text-input id="profession" wire:model.debounce.500ms="formEntryCPPT.profession" :errorshas="$errors->has('formEntryCPPT.profession')"
            :disabled="$disabledPropertyRjStatus" />
        @error('formEntryCPPT.profession')
            <x-input-error :messages="__($message)" />
        @enderror
    </div> --}}

    <!-- Tombol Simpan -->
    <div class="mt-4">
        <x-yellow-button wire:click.prevent="addCPPT()" type="button">
            Simpan CPPT
        </x-yellow-button>
    </div>

    <!-- Tabel Data CPPT -->
    @include('livewire.emr-r-i.mr-r-i.c-p-p-t.form-entry-c-p-p-t2-table')
</div>
