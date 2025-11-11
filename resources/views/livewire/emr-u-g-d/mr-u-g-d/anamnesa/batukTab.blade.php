<div>
    <div class="w-full mb-1">
        <div class="p-4 bg-white rounded-lg shadow-sm">
            <x-input-label :value="__('Screening Batuk')" :required="__(false)" class="mb-4 text-lg font-semibold" />

            {{-- Riwayat Demam --}}
            <div class="grid grid-cols-1 gap-4 mb-4 md:grid-cols-2">
                <x-check-box value='1' :label="__('Riwayat Demam?')"
                    wire:model.debounce.500ms="dataDaftarUgd.anamnesa.batuk.riwayatDemam" />
                <x-text-input id="dataDaftarUgd.anamnesa.batuk.keteranganRiwayatDemam"
                    placeholder="Keterangan Riwayat Demam" class="w-full" :errorshas="__($errors->has('dataDaftarUgd.anamnesa.batuk.keteranganRiwayatDemam'))"
                    :disabled=$disabledPropertyRjStatus
                    wire:model.debounce.500ms="dataDaftarUgd.anamnesa.batuk.keteranganRiwayatDemam" />
            </div>

            {{-- Berkeringat Malam Hari --}}
            <div class="grid grid-cols-1 gap-4 mb-4 md:grid-cols-2">
                <x-check-box value='1' :label="__('Riwayat Berkeringat Malam Hari Tanpa Aktifitas?')"
                    wire:model.debounce.500ms="dataDaftarUgd.anamnesa.batuk.berkeringatMlmHari" />
                <x-text-input id="dataDaftarUgd.anamnesa.batuk.keteranganBerkeringatMlmHari"
                    placeholder="Keterangan Berkeringat Malam Hari" class="w-full" :errorshas="__($errors->has('dataDaftarUgd.anamnesa.batuk.keteranganBerkeringatMlmHari'))"
                    :disabled=$disabledPropertyRjStatus
                    wire:model.debounce.500ms="dataDaftarUgd.anamnesa.batuk.keteranganBerkeringatMlmHari" />
            </div>

            {{-- Bepergian Daerah Wabah --}}
            <div class="grid grid-cols-1 gap-4 mb-4 md:grid-cols-2">
                <x-check-box value='1' :label="__('Riwayat Bepergian Daerah Wabah?')"
                    wire:model.debounce.500ms="dataDaftarUgd.anamnesa.batuk.bepergianDaerahWabah" />
                <x-text-input id="dataDaftarUgd.anamnesa.batuk.keteranganBepergianDaerahWabah"
                    placeholder="Keterangan Bepergian Daerah Wabah" class="w-full" :errorshas="__($errors->has('dataDaftarUgd.anamnesa.batuk.keteranganBepergianDaerahWabah'))"
                    :disabled=$disabledPropertyRjStatus
                    wire:model.debounce.500ms="dataDaftarUgd.anamnesa.batuk.keteranganBepergianDaerahWabah" />
            </div>

            {{-- Pemakaian Obat Jangka Panjang --}}
            <div class="grid grid-cols-1 gap-4 mb-4 md:grid-cols-2">
                <x-check-box value='1' :label="__('Riwayat Pemakaian Obat dalam Jangka Panjang?')"
                    wire:model.debounce.500ms="dataDaftarUgd.anamnesa.batuk.riwayatPakaiObatJangkaPanjangan" />
                <x-text-input id="dataDaftarUgd.anamnesa.batuk.keteranganRiwayatPakaiObatJangkaPanjangan"
                    placeholder="Keterangan Pemakaian Obat" class="w-full" :errorshas="__(
                        $errors->has('dataDaftarUgd.anamnesa.batuk.keteranganRiwayatPakaiObatJangkaPanjangan'),
                    )"
                    :disabled=$disabledPropertyRjStatus
                    wire:model.debounce.500ms="dataDaftarUgd.anamnesa.batuk.keteranganRiwayatPakaiObatJangkaPanjangan" />
            </div>

            {{-- Berat Badan Turun --}}
            <div class="grid grid-cols-1 gap-4 mb-4 md:grid-cols-2">
                <x-check-box value='1' :label="__('Riwayat Berat Badan Turun Tanpa Sebab?')"
                    wire:model.debounce.500ms="dataDaftarUgd.anamnesa.batuk.BBTurunTanpaSebab" />
                <x-text-input id="dataDaftarUgd.anamnesa.batuk.keteranganBBTurunTanpaSebab"
                    placeholder="Keterangan Berat Badan Turun" class="w-full" :errorshas="__($errors->has('dataDaftarUgd.anamnesa.batuk.keteranganBBTurunTanpaSebab'))"
                    :disabled=$disabledPropertyRjStatus
                    wire:model.debounce.500ms="dataDaftarUgd.anamnesa.batuk.keteranganBBTurunTanpaSebab" />
            </div>

            {{-- Pembesaran Getah Bening --}}
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <x-check-box value='1' :label="__('Ada Pembesaran Kelenjar Getah Bening?')"
                    wire:model.debounce.500ms="dataDaftarUgd.anamnesa.batuk.pembesaranGetahBening" />
                <x-text-input id="dataDaftarUgd.anamnesa.batuk.keteranganPembesaranGetahBening"
                    placeholder="Keterangan Pembesaran Kelenjar Getah Bening" class="w-full" :errorshas="__($errors->has('dataDaftarUgd.anamnesa.batuk.keteranganPembesaranGetahBening'))"
                    :disabled=$disabledPropertyRjStatus
                    wire:model.debounce.500ms="dataDaftarUgd.anamnesa.batuk.keteranganPembesaranGetahBening" />
            </div>
        </div>
    </div>
</div>
