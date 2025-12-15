<div class="fixed inset-0 z-40" x-data @keydown.escape.window="$wire.closeModalVclaimBpjs()">

    {{-- Backdrop --}}
    <div class="fixed inset-0 transition-opacity">
        <div class="absolute inset-0 bg-gray-500 opacity-75" wire:click="closeModalVclaimBpjs()"></div>
    </div>

    {{-- Body --}}
    <div class="fixed inset-0 transition-opacity">
        <div class="absolute overflow-auto bg-white rounded-t-lg inset-4">

            {{-- Topbar --}}
            <div
                class="sticky top-0 flex items-center justify-between p-4 bg-opacity-75 border-b rounded-t-lg bg-primary">

                <h3 class="w-full text-2xl font-semibold text-white">
                    Data Peserta BPJS (VClaim)
                </h3>

                {{-- Close --}}
                <button wire:click="closeModalVclaimBpjs()"
                    class="p-1.5 ml-auto text-sm text-gray-400 bg-gray-50 rounded-lg hover:bg-gray-200 hover:text-gray-900">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
            </div>

            {{-- Input Cari --}}
            <div class="p-4 border-b bg-gray-50">
                <form wire:submit.prevent="pesertaNomorKartu">
                    <x-text-input type="text" class="w-full"
                        placeholder="Masukkan No Kartu BPJS (13 digit) / NIK (16 digit)" wire:model.defer="noKartuBPJS"
                        autofocus />
                </form>
                <p class="mt-1 text-xs text-gray-500">
                    Tekan <b>Enter</b> untuk mencari peserta
                </p>
            </div>

            {{-- Kartu Data BPJS --}}
            @if (!empty($pesertaBPJS))
                <div class="p-4">
                    <div class="p-4 border rounded-lg bg-gray-50">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <div class="text-lg font-semibold text-gray-900">
                                    {{ data_get($pesertaBPJS, 'nama') }}
                                </div>
                                <div class="text-sm text-gray-600">
                                    NIK:
                                    <span class="font-medium">{{ data_get($pesertaBPJS, 'nik') }}</span>
                                    • No Kartu:
                                    <span class="font-medium">{{ data_get($pesertaBPJS, 'noKartu') }}</span>
                                </div>
                                <div class="mt-1 text-sm text-gray-600">
                                    Lahir:
                                    <span class="font-medium">{{ data_get($pesertaBPJS, 'tglLahir') }}</span>
                                    • Umur:
                                    <span class="font-medium">{{ data_get($pesertaBPJS, 'umur.umurSekarang') }}</span>
                                    • JK:
                                    <span class="font-medium">{{ data_get($pesertaBPJS, 'sex') }}</span>
                                </div>
                            </div>

                            <div class="text-right">
                                <div class="text-sm text-gray-600">Status</div>
                                <span
                                    class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                                    {{ data_get($pesertaBPJS, 'statusPeserta.keterangan') === 'AKTIF'
                                        ? 'bg-green-100 text-green-800'
                                        : 'bg-red-100 text-red-800' }}">
                                    {{ data_get($pesertaBPJS, 'statusPeserta.keterangan') }}
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-3 mt-4 md:grid-cols-2">

                            <div class="p-3 bg-white border rounded-lg">
                                <span class="block text-xs text-gray-500">Jenis Peserta</span>
                                <span class="block font-medium text-gray-900">
                                    {{ data_get($pesertaBPJS, 'jenisPeserta.keterangan', '-') }}
                                </span>
                            </div>

                            <div class="p-3 bg-white border rounded-lg">
                                <span class="block text-xs text-gray-500">Hak Kelas</span>
                                <span class="block font-medium text-gray-900">
                                    {{ data_get($pesertaBPJS, 'hakKelas.keterangan', '-') }}
                                </span>
                            </div>

                            <div class="p-3 bg-white border rounded-lg">
                                <span class="block text-xs text-gray-500">Faskes</span>
                                <span class="block font-medium text-gray-900">
                                    {{ data_get($pesertaBPJS, 'provUmum.nmProvider', '-') }}
                                </span>
                            </div>

                            <div class="p-3 bg-white border rounded-lg">
                                <span class="block text-xs text-gray-500">Masa Berlaku</span>
                                <span class="block text-sm text-gray-700">
                                    TMT
                                    <span class="font-medium">
                                        {{ data_get($pesertaBPJS, 'tglTMT', '-') }}
                                    </span>
                                    –
                                    TAT
                                    <span class="font-medium">
                                        {{ data_get($pesertaBPJS, 'tglTAT', '-') }}
                                    </span>
                                </span>
                            </div>

                        </div>

                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
