<div>
    <div class="w-full mb-1">
        <div class="p-4 bg-white rounded-lg shadow-sm">
            {{-- Status Psikologis --}}
            <div class="mb-6">
                <x-input-label for="dataDaftarUgd.anamnesa.statusPsikologis.statusPsikologis" :value="__('Status Psikologis')"
                    :required="__(false)" class="mb-3 text-lg font-semibold" />
                <div class="grid grid-cols-2 gap-3 md:grid-cols-3">
                    <x-check-box value='1' :label="__('Tidak Ada Kelainan')"
                        wire:model.debounce.500ms="dataDaftarUgd.anamnesa.statusPsikologis.tidakAdaKelainan" />
                    <x-check-box value='1' :label="__('Marah')"
                        wire:model.debounce.500ms="dataDaftarUgd.anamnesa.statusPsikologis.marah" />
                    <x-check-box value='1' :label="__('Cemas')"
                        wire:model.debounce.500ms="dataDaftarUgd.anamnesa.statusPsikologis.ccemas" />
                    <x-check-box value='1' :label="__('Takut')"
                        wire:model.debounce.500ms="dataDaftarUgd.anamnesa.statusPsikologis.takut" />
                    <x-check-box value='1' :label="__('Sedih')"
                        wire:model.debounce.500ms="dataDaftarUgd.anamnesa.statusPsikologis.sedih" />
                    <x-check-box value='1' :label="__('Resiko Bunuh Diri')"
                        wire:model.debounce.500ms="dataDaftarUgd.anamnesa.statusPsikologis.cenderungBunuhDiri" />
                </div>
                <div class="mt-3">
                    <x-text-input id="dataDaftarUgd.anamnesa.statusPsikologis.sebutstatusPsikologis"
                        placeholder="Keterangan lain mengenai status psikologis..." class="w-full" :errorshas="__($errors->has('dataDaftarUgd.anamnesa.statusPsikologis.sebutstatusPsikologis'))"
                        :disabled=$disabledPropertyRjStatus
                        wire:model.debounce.500ms="dataDaftarUgd.anamnesa.statusPsikologis.sebutstatusPsikologis" />
                </div>
            </div>

            {{-- Status Mental --}}
            <div>
                <x-input-label for="dataDaftarUgd.anamnesa.statusMental.statusMental" :value="__('Status Mental')"
                    :required="__(false)" class="mb-3 text-lg font-semibold" />
                <div class="grid grid-cols-1 gap-3 mb-3 md:grid-cols-3">
                    @foreach ($dataDaftarUgd['anamnesa']['statusMental']['statusMentalOption'] as $statusMentalOption)
                        <x-radio-button :label="__($statusMentalOption['statusMental'])" value="{{ $statusMentalOption['statusMental'] }}"
                            wire:model="dataDaftarUgd.anamnesa.statusMental.statusMental" />
                    @endforeach
                </div>
                <div class="mt-3">
                    <x-text-input id="dataDaftarUgd.anamnesa.statusMental.keteranganStatusMental"
                        placeholder="Keterangan status mental..." class="w-full" :errorshas="__($errors->has('dataDaftarUgd.anamnesa.statusMental.keteranganStatusMental'))"
                        :disabled=$disabledPropertyRjStatus
                        wire:model.debounce.500ms="dataDaftarUgd.anamnesa.statusMental.keteranganStatusMental" />
                </div>
            </div>
        </div>
    </div>
</div>
