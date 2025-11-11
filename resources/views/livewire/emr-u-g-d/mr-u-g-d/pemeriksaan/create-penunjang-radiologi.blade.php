<div class="fixed inset-0 z-50">
    <!-- Background overlay -->
    <div class="fixed inset-0 transition-opacity">
        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
    </div>

    <!-- Modal -->
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex items-end justify-center min-h-full p-4 text-center sm:items-center sm:p-0">
            <div
                class="relative overflow-hidden text-left transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:w-full sm:max-w-7xl">

                <!-- Header -->
                <div class="sticky top-0 flex items-center justify-between p-4 border-b rounded-t-lg bg-primary">
                    <h3 class="w-full text-2xl font-semibold text-white">
                        {{ 'Pemeriksaan Radiologi' }}
                    </h3>
                    <div class="flex justify-end w-full mr-4">
                        <button wire:click="closeModalRadiologi()"
                            class="text-gray-400 bg-gray-50 hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Display Pasien -->
                <div class="p-4">
                    <livewire:emr-u-g-d.display-pasien.display-pasien :wire:key="$rjNoRef.'display-pasien'"
                        :rjNoRef="$rjNoRef" />
                </div>

                <!-- Selected Items -->
                @if (count($isPemeriksaanRadiologiSelected) > 0)
                    <div class="px-6">
                        <x-input-label value="Pemeriksaan Terpilih" class="text-lg font-semibold" />
                        <div
                            class="grid w-full gap-2 rounded-lg grid-cols-2 md:grid-cols-4 lg:grid-cols-6 min-h-[75px] p-2 bg-gray-50">
                            @foreach ($isPemeriksaanRadiologiSelected as $key => $isPemeriksaanRad)
                                <div
                                    class="inline-flex items-center justify-between w-full p-2 text-gray-900 bg-green-100 border border-green-300 rounded-lg">
                                    <div class="flex-1 block">
                                        <div class="w-auto text-sm font-medium">{{ $isPemeriksaanRad['rad_desc'] }}
                                        </div>
                                        <div class="w-auto text-xs text-gray-600">Rp
                                            {{ number_format($isPemeriksaanRad['rad_price']) }}</div>
                                    </div>
                                    <button type="button"
                                        class="inline-flex items-center p-1 ml-2 text-sm text-gray-400 bg-transparent rounded-lg hover:bg-gray-200 hover:text-red-500"
                                        wire:click.prevent="toggleRadiologi({{ array_search($isPemeriksaanRad, $isPemeriksaanRadiologi) }})">
                                        <svg aria-hidden="true" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd"
                                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="sr-only">Hapus</span>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Available Items -->
                <div class="p-6">
                    <x-input-label value="Daftar Pemeriksaan Radiologi" class="mb-4 text-lg font-semibold" />

                    <div class="flex flex-wrap -mx-2">
                        @foreach ($isPemeriksaanRadiologi as $key => $isPemeriksaanRad)
                            @php
                                $bgCardPropertyColor = $isPemeriksaanRad['radStatus']
                                    ? 'bg-green-100 border-green-300'
                                    : 'bg-white border-gray-200';
                                $textColor = $isPemeriksaanRad['radStatus'] ? 'text-green-800' : 'text-gray-900';
                            @endphp
                            <div class="w-full px-2 mb-4 sm:w-1/2 md:w-1/3 lg:w-1/4 xl:w-1/5">
                                <div wire:click.prevent="toggleRadiologi({{ $key }})"
                                    class="block p-4 {{ $bgCardPropertyColor }} border rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 cursor-pointer h-full">
                                    <div class="flex flex-col items-center text-center">
                                        <p class="text-sm font-semibold {{ $textColor }} mb-1 leading-tight">
                                            {{ $isPemeriksaanRad['rad_desc'] }}
                                        </p>
                                        <span class="text-xs font-medium text-gray-600">
                                            Rp {{ number_format($isPemeriksaanRad['rad_price']) }}
                                        </span>
                                        @if ($isPemeriksaanRad['radStatus'])
                                            <span class="px-2 py-1 mt-1 text-xs text-white bg-green-500 rounded-full">
                                                Terpilih
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if (empty($isPemeriksaanRadiologi))
                        <div class="py-8 text-center">
                            <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                            <p class="mt-2 text-gray-500">Tidak ada data pemeriksaan radiologi tersedia.</p>
                        </div>
                    @endif
                </div>

                <!-- Footer -->
                <div class="sticky bottom-0 flex justify-between px-6 py-4 border-t bg-gray-50">
                    <div class="text-sm text-gray-600">
                        @if (count($isPemeriksaanRadiologiSelected) > 0)
                            <span class="font-semibold">{{ count($isPemeriksaanRadiologiSelected) }} item
                                terpilih</span>
                        @else
                            Pilih pemeriksaan radiologi
                        @endif
                    </div>
                    <div class="flex space-x-2">
                        <x-alternative-button wire:click="closeModalRadiologi()" type="button">
                            Batal
                        </x-alternative-button>
                        <div wire:loading wire:target="kirimRadiologi">
                            <x-loading />
                        </div>
                        <x-green-button :disabled="empty($isPemeriksaanRadiologiSelected)" wire:click.prevent="kirimRadiologi()" type="button"
                            wire:loading.remove>
                            Kirim Radiologi
                        </x-green-button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
