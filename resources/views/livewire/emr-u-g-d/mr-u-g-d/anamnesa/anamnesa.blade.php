<div>
    @php
        $disabledProperty = true;
        $disabledPropertyRjStatus = false;
    @endphp

    @if (isset($dataDaftarUgd['anamnesa']))
        <div class="w-full mb-1">
            <div id="TransaksiRawatJalan" class="px-2">
                <div id="TransaksiRawatJalan" x-data="{ activeTab: '{{ $dataDaftarUgd['anamnesa']['pengkajianPerawatanTab'] ?? 'pengkajianPerawatanTab' }}' }">

                    {{-- Tab Navigation --}}
                    <div class="px-2 mb-2 border-b border-gray-200 dark:border-gray-700">
                        <ul
                            class="flex flex-wrap -mb-px text-xs font-medium text-center text-gray-500 dark:text-gray-400">
                            @foreach (['pengkajianPerawatanTab', 'keluhanUtamaTab', 'riwayatPenyakitSekarangUmumTab', 'riwayatPenyakitDahuluTab', 'statusPsikologisTab', 'batukTab'] as $tab)
                                <li class="mr-2">
                                    <button
                                        class="inline-block p-2 transition-colors duration-200 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                        :class="activeTab === '{{ $dataDaftarUgd['anamnesa'][$tab] ?? $tab }}' ?
                                            'text-primary border-primary bg-gray-100' : ''"
                                        @click="activeTab ='{{ $dataDaftarUgd['anamnesa'][$tab] ?? $tab }}'">
                                        {{ $dataDaftarUgd['anamnesa'][$tab] ?? $tab }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Tab Contents --}}
                    <div class="p-2 rounded-lg bg-gray-50"
                        x-show.transition.in.opacity.duration.600="activeTab === '{{ $dataDaftarUgd['anamnesa']['pengkajianPerawatanTab'] ?? 'pengkajianPerawatanTab' }}'">
                        @include('livewire.emr-u-g-d.mr-u-g-d.anamnesa.pengkajianPerawatanTab')
                    </div>

                    <div class="p-2 rounded-lg bg-gray-50"
                        x-show.transition.in.opacity.duration.600="activeTab === '{{ $dataDaftarUgd['anamnesa']['keluhanUtamaTab'] ?? 'keluhanUtamaTab' }}'">
                        @include('livewire.emr-u-g-d.mr-u-g-d.anamnesa.keluhanUtamaTab')
                    </div>

                    <div class="p-2 rounded-lg bg-gray-50"
                        x-show.transition.in.opacity.duration.600="activeTab === '{{ $dataDaftarUgd['anamnesa']['riwayatPenyakitSekarangUmumTab'] ?? 'riwayatPenyakitSekarangUmumTab' }}'">
                        @include('livewire.emr-u-g-d.mr-u-g-d.anamnesa.riwayatPenyakitSekarangUmumTab')
                    </div>

                    <div class="p-2 rounded-lg bg-gray-50"
                        x-show.transition.in.opacity.duration.600="activeTab === '{{ $dataDaftarUgd['anamnesa']['riwayatPenyakitDahuluTab'] ?? 'riwayatPenyakitDahuluTab' }}'">
                        @include('livewire.emr-u-g-d.mr-u-g-d.anamnesa.riwayatPenyakitDahuluTab')
                    </div>

                    <div class="p-2 rounded-lg bg-gray-50"
                        x-show.transition.in.opacity.duration.600="activeTab === '{{ $dataDaftarUgd['anamnesa']['statusPsikologisTab'] ?? 'statusPsikologisTab' }}'">
                        @include('livewire.emr-u-g-d.mr-u-g-d.anamnesa.statusPsikologisTab')
                    </div>

                    <div class="p-2 rounded-lg bg-gray-50"
                        x-show.transition.in.opacity.duration.600="activeTab === '{{ $dataDaftarUgd['anamnesa']['batukTab'] ?? 'batukTab' }}'">
                        @include('livewire.emr-u-g-d.mr-u-g-d.anamnesa.batukTab')
                    </div>

                </div>
            </div>

            {{-- Action Buttons --}}
            {{-- <div class="sticky bottom-0 flex justify-between px-4 py-3 bg-gray-50 sm:px-6">
                <div class="flex gap-2">
                    <div wire:loading wire:target="resetForm">
                        <x-loading />
                    </div>
                    <x-light-button :disabled=$disabledPropertyRjStatus wire:click.prevent="resetForm()" type="button"
                        wire:loading.remove>
                        <i class="fas fa-redo me-1"></i>Reset Form
                    </x-light-button>
                </div>
                <div>
                    <div wire:loading wire:target="store">
                        <x-loading />
                    </div>
                    <x-green-button :disabled=$disabledPropertyRjStatus wire:click.prevent="store()" type="button"
                        wire:loading.remove>
                        <i class="fas fa-save me-1"></i>Simpan Anamnesa
                    </x-green-button>
                </div>
            </div> --}}
        </div>
    @else
        <div class="p-4 m-4 text-center text-gray-500 bg-yellow-100 rounded-lg">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Data anamnesa belum tersedia. Silakan refresh halaman.
        </div>
    @endif
</div>
