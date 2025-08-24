<div class="fixed inset-0 z-40">
    @php
        // ----- Ambil header aktif -----
        $ri = $dataDaftarRi ?? [];
        $headers = $ri['eresepHdr'] ?? [];

        // Prioritas pemilihan header:
        // 1) Jika parent sudah menyiapkan $slsNoRef / $resepNoRef → pilih itu
        // 2) Fallback: header terbaru berdasarkan resepNo

        $activeHdr = $headers[$resepHeaderIndexForEdit] ?? [];
        // Counts untuk badge tab
        $nonCount = collect($activeHdr['eresep'] ?? [])->count();
        $racCount = collect($activeHdr['eresepRacikan'] ?? [])->count();

        // Disabled state (berdasarkan header aktif)
        $disabledPropertyRiStatusResep = !empty($activeHdr['telaahResep']['penanggungJawab'] ?? null);
        $disabledPropertyRiStatusObat = !empty($activeHdr['telaahObat']['penanggungJawab'] ?? null);

        // Base binding untuk wire:model
        $bindBase = "dataDaftarRi.eresepHdr.$resepHeaderIndexForEdit";

    @endphp
    {{-- Backdrop --}}
    <div class="fixed inset-0 transition-opacity">
        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
    </div>

    {{-- Modal --}}
    <div class="fixed inset-0 transition-opacity">
        <div class="absolute overflow-auto bg-white rounded-t-lg inset-4">

            {{-- Topbar --}}
            <div
                class="sticky top-0 flex items-center justify-between p-4 bg-opacity-75 border-b rounded-t-lg bg-primary">
                <h3 class="w-full text-2xl font-semibold text-white">Terapi RI</h3>

                <div class="flex justify-end w-full mr-4">
                    <button wire:click="closeModalTelaahResep()"
                        class="text-gray-400 bg-gray-50 hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Display Pasien (RI) --}}
            <div>
                <livewire:emr-r-i.display-pasien.display-pasien
                    :wire:key="$dataDaftarRi['riHdrNo'].'-display-pasien-eresep-ri'" :riHdrNoRef="$dataDaftarRi['riHdrNo']" />
            </div>

            {{-- Grid Eresep + Telaah --}}
            <div class="grid grid-cols-3 gap-4">
                <div class="col-span-2">
                    <div class="grid w-full grid-cols-3 gap-4 mx-2 mr-2 rounded-lg bg-gray-50">
                        <div>
                            @include('livewire.emr-r-i.telaah-resep-r-i.eresep-r-i-hdr-active.eresep-r-i-hdr1-table-resep')
                        </div>
                        <div class="grid grid-cols-2 col-span-2 gap-2 mt-2">
                            <div>
                                @include('livewire.emr-r-i.telaah-resep-r-i.eresep-r-i-hdr-active.eresep-r-i-hdr2-table-resep-data')
                            </div>
                            <div>
                                @include('livewire.emr-r-i.telaah-resep-r-i.eresep-r-i-hdr-active.eresep-r-i-hdr3-table-resep-data-racikan')
                            </div>
                        </div>
                    </div>

                    {{-- Telaah Resep / Telaah Obat (HEADER) --}}
                    @role(['Apoteker', 'Admin'])
                        <div class="grid w-full grid-cols-2 gap-8 px-8 mx-2 my-2 mr-2 rounded-lg bg-gray-50">
                            {{-- TELA'AH RESEP --}}
                            @include('livewire.emr-r-i.telaah-resep-r-i.radio-telaahresep-ri', [
                                'telaahResepHdr' => $activeHdr['telaahResep'] ?? [],
                                'bindPath' => $bindBase . '.telaahResep',
                                'disabled' => $disabledPropertyRiStatusResep,
                            ])

                            {{-- TELA'AH OBAT --}}
                            @include('livewire.emr-r-i.telaah-resep-r-i.radio-telaahobat-ri', [
                                'telaahObatHdr' => $activeHdr['telaahObat'] ?? [],
                                'bindPath' => $bindBase . '.telaahObat',
                                'disabled' => $disabledPropertyRiStatusObat,
                            ])
                        </div>
                    @endrole
                </div>



                {{-- Resume Rekam Medis --}}
                <div class="col-span-1">

                    @include('livewire.emr-r-i.telaah-resep-r-i.eresep-r-i-hdr.eresep-r-i-hdr1-table-resep')

                </div>
            </div>







        </div>
    </div>
</div>
</div>
