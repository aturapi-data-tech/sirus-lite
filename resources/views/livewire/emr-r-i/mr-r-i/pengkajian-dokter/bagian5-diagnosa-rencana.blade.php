<div>
    <div class="space-y-4">
        {{-- Diagnosa / Assessment --}}
        <x-input-label for="dataDaftarRi.pengkajianDokter.diagnosaAssesment.diagnosaAwal" :value="__('Diagnosa / Assessment')"
            :required="__(false)" class="pt-2 sm:text-xl" />

        <div>
            <x-input-label for="dataDaftarRi.pengkajianDokter.diagnosaAssesment.diagnosaAwal" :value="__('Diagnosa Awal')"
                :required="__(false)" class="block text-sm font-medium text-gray-700" />
            <textarea wire:model="pengkajianDokter.diagnosaAssesment.diagnosaAwal"
                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary sm:text-sm"
                rows="3"></textarea>
        </div>

        {{-- Rencana --}}
        <x-input-label for="dataDaftarRi.pengkajianDokter.rencana.penegakanDiagnosa" :value="__('Rencana')" :required="__(false)"
            class="pt-2 sm:text-xl" />

        <div>
            <x-input-label for="dataDaftarRi.pengkajianDokter.rencana.penegakanDiagnosa" :value="__('Penegakan Diagnosa')"
                :required="__(false)" class="block text-sm font-medium text-gray-700" />
            <textarea wire:model="pengkajianDokter.rencana.penegakanDiagnosa"
                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary sm:text-sm"
                rows="3"></textarea>
        </div>

        <div>
            <x-input-label for="dataDaftarRi.pengkajianDokter.rencana.terapi" :value="__('Terapi')" :required="__(false)"
                class="block text-sm font-medium text-gray-700" />
            <textarea wire:model="pengkajianDokter.rencana.terapi"
                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary sm:text-sm"
                rows="3"></textarea>
        </div>

        <div>
            <x-input-label for="dataDaftarRi.pengkajianDokter.rencana.diet" :value="__('Diet')" :required="__(false)"
                class="block text-sm font-medium text-gray-700" />
            <textarea wire:model="pengkajianDokter.rencana.diet"
                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary sm:text-sm"
                rows="3"></textarea>
        </div>

        <div>
            <x-input-label for="dataDaftarRi.pengkajianDokter.rencana.edukasi" :value="__('Edukasi')" :required="__(false)"
                class="block text-sm font-medium text-gray-700" />
            <textarea wire:model="pengkajianDokter.rencana.edukasi"
                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary sm:text-sm"
                rows="3"></textarea>
        </div>

        <div>
            <x-input-label for="dataDaftarRi.pengkajianDokter.rencana.monitoring" :value="__('Monitoring')" :required="__(false)"
                class="block text-sm font-medium text-gray-700" />
            <textarea wire:model="pengkajianDokter.rencana.monitoring"
                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary sm:text-sm"
                rows="3"></textarea>
        </div>
    </div>
</div>
