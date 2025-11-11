<form wire:submit.prevent="uploadHasilPenunjang">
    <div class="my-2">
        @role(['Perawat', 'Admin'])
            <x-input-label for="uploadHasilPenunjang" :value="__('Upload Penunjang')" />
            <div class="grid grid-cols-3 gap-2">
                <x-text-input id="uploadHasilPenunjang" class="block w-full mt-1" :errorshas="$errors->has('filePDF')" :disabled="$disabledPropertyRjStatus"
                    wire:model="filePDF" type="file" />
                <x-text-input id="descPDF" placeholder="Keterangan Hasil Penunjang" class="mt-1 ml-2" :errorshas="$errors->has('descPDF')"
                    :disabled="$disabledPropertyRjStatus" wire:model.debounce.500ms="descPDF" />
                <div class="grid grid-cols-1 p-2 mt-1">
                    <div wire:loading wire:target="uploadHasilPenunjang">
                        <x-loading />
                    </div>
                    <x-primary-button :disabled="$disabledPropertyRjStatus" type="submit" wire:loading.remove>
                        Upload Data Penunjang
                    </x-primary-button>
                </div>
            </div>
        @endrole

        {{-- Tabel hasil upload --}}
        @if (isset($dataDaftarUgd['pemeriksaan']['uploadHasilPenunjang']) &&
                count($dataDaftarUgd['pemeriksaan']['uploadHasilPenunjang']) > 0)
            <table class="w-full mt-4 text-sm text-left text-gray-500 table-auto">
                <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                    <tr>
                        <th class="px-4 py-3">Tgl Upload</th>
                        <th class="px-4 py-3">Keterangan Penunjang</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @foreach ($dataDaftarUgd['pemeriksaan']['uploadHasilPenunjang'] as $key => $uploadHasilPenunjang)
                        <tr class="border-b group">
                            <td class="px-2 py-2 font-normal text-gray-700 whitespace-nowrap">
                                {{ $uploadHasilPenunjang['tglUpload'] ?? '' }}
                            </td>
                            <td class="px-2 py-2 font-normal text-gray-700">
                                {{ $uploadHasilPenunjang['desc'] ?? '' }}
                            </td>
                            <td class="px-2 py-2 font-normal text-gray-700">
                                <div class="flex justify-center space-x-2">
                                    @role(['Perawat', 'Admin', 'Dokter'])
                                        <x-yellow-button class="inline-flex" :disabled="$disabledPropertyRjStatus"
                                            wire:click.prevent="openModalHasilPenunjang('{{ $uploadHasilPenunjang['file'] ?? '' }}')">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        </x-yellow-button>
                                    @endrole
                                    @role(['Perawat', 'Admin'])
                                        <x-alternative-button class="inline-flex" :disabled="$disabledPropertyRjStatus"
                                            wire:click.prevent="deleteHasilPenunjang({{ $key }})">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        </x-alternative-button>
                                    @endrole
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</form>
