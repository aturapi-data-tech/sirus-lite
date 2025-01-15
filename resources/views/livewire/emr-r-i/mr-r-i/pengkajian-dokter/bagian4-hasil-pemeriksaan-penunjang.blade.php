<div>
    <div class="space-y-4">
        <div>
            <x-input-label for="dataDaftarRi.pengkajianDokter.hasilPemeriksaanPenunjang.laboratorium" :value="__('Laboratorium')"
                :required="__(false)" class="block text-sm font-medium text-gray-700" />
            <x-text-input-area id="dataDaftarRi.pengkajianDokter.hasilPemeriksaanPenunjang.laboratorium"
                placeholder="Laboratorium" class="mt-1"
                wire:model.debounce.500ms="dataDaftarRi.pengkajianDokter.hasilPemeriksaanPenunjang.laboratorium" />
        </div>
        <div>
            <x-input-label for="dataDaftarRi.pengkajianDokter.hasilPemeriksaanPenunjang.radiologi" :value="__('Radiologi')"
                :required="__(false)" class="block text-sm font-medium text-gray-700" />
            <x-text-input-area id="dataDaftarRi.pengkajianDokter.hasilPemeriksaanPenunjang.radiologi"
                placeholder="Radiologi" class="mt-1"
                wire:model.debounce.500ms="dataDaftarRi.pengkajianDokter.hasilPemeriksaanPenunjang.radiologi" />
        </div>
        <div>
            <x-input-label for="dataDaftarRi.pengkajianDokter.hasilPemeriksaanPenunjang.penunjangLain" :value="__('Penunjang Lain')"
                :required="__(false)" class="block text-sm font-medium text-gray-700" />
            <x-text-input-area id="dataDaftarRi.pengkajianDokter.hasilPemeriksaanPenunjang.penunjangLain"
                placeholder="Penunjang Lain" class="mt-1"
                wire:model.debounce.500ms="dataDaftarRi.pengkajianDokter.hasilPemeriksaanPenunjang.penunjangLain" />
        </div>
    </div>
</div>
