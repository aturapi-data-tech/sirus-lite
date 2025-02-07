<div>
    <div class="w-full mb-1">
        <table class="w-full text-sm text-left text-gray-900 table-auto dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    {{-- <th scope="col" class="px-2 py-2 text-center">
                        Dokter ID
                    </th> --}}
                    <th scope="col" class="px-2 py-2 text-center">
                        Nama Dokter
                    </th>
                    {{-- <th scope="col" class="px-2 py-2 text-center">
                        Poli ID
                    </th> --}}
                    <th scope="col" class="px-2 py-2 text-center">
                        Deskripsi
                    </th>
                    <th scope="col" class="px-2 py-2 text-center">
                        Tanggal Entry
                    </th>
                    <th scope="col" class="px-2 py-2 text-center">
                        Level Dokter
                    </th>
                    <th scope="col" class="px-2 py-2 text-center">
                        Action
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800">
                @php
                    $levelingDokter = $this->dataDaftarRi['pengkajianAwalPasienRawatInap']['levelingDokter'] ?? [];
                @endphp

                @if (!empty($levelingDokter))
                    @foreach ($levelingDokter as $key => $lvlDokter)
                        <tr class="border-b group dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            {{-- <td class="px-2 py-2 text-center group-hover:bg-gray-100 group-hover:text-primary">
                                {{ $lvlDokter['drId'] ?? '-' }}
                            </td> --}}
                            <td class="px-2 py-2 text-center group-hover:bg-gray-100 group-hover:text-primary">
                                {{ $lvlDokter['drName'] ?? '-' }}
                            </td>
                            {{-- <td class="px-2 py-2 text-center group-hover:bg-gray-100 group-hover:text-primary">
                                {{ $lvlDokter['poliId'] ?? '-' }}
                            </td> --}}
                            <td class="px-2 py-2 text-center group-hover:bg-gray-100 group-hover:text-primary">
                                {{ $lvlDokter['poliDesc'] ?? '-' }}
                            </td>
                            <td class="px-2 py-2 text-center group-hover:bg-gray-100 group-hover:text-primary">
                                {{ $lvlDokter['tglEntry'] ?? '-' }}
                            </td>
                            <td class="px-2 py-2 text-center group-hover:bg-gray-100 group-hover:text-primary">
                                <div>
                                    {{ $lvlDokter['levelDokter'] ?? '-' }}
                                </div>

                                @if (!empty($lvlDokter['tglEntry']))
                                    <div class="grid grid-cols-2 gap-2 ml-2">
                                        <div class="grid grid-cols-1">
                                            <div wire:loading wire:target="setLevelingDokterUtama">
                                                <x-loading />
                                            </div>

                                            <x-green-button :disabled="$disabledPropertyRjStatus"
                                                wire:click.prevent="setLevelingDokterUtama({{ $key }})"
                                                type="button" wire:loading.remove>
                                                Set Utama
                                            </x-green-button>
                                        </div>
                                        <div class="grid grid-cols-1">
                                            <div wire:loading wire:target="setLevelingDokterRawatGabung">
                                                <x-loading />
                                            </div>

                                            <x-yellow-button :disabled="$disabledPropertyRjStatus"
                                                wire:click.prevent="setLevelingDokterRawatGabung({{ $key }})"
                                                type="button" wire:loading.remove>
                                                Set RawatGabung
                                            </x-yellow-button>
                                        </div>
                                    </div>
                                @endif

                            </td>
                            <td class="px-2 py-2 text-center group-hover:bg-gray-100 group-hover:text-primary">
                                @if (!empty($lvlDokter['tglEntry']))
                                    <x-alternative-button class="inline-flex"
                                        wire:click.prevent="removeLevelingDokter('{{ $lvlDokter['tglEntry'] ?? '' }}')">
                                        <svg class="w-5 h-5 text-gray-800 dark:text-gray-200" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                                            <path
                                                d="M17 4h-4V2a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v2H1a1 1 0 0 0 0 2h1v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6h1a1 1 0 1 0 0-2ZM7 2h4v2H7V2Zm1 14a1 1 0 1 1-2 0V8a1 1 0 0 1 2 0v8Zm4 0a1 1 0 0 1-2 0V8a1 1 0 0 1 2 0v8Z" />
                                        </svg>
                                    </x-alternative-button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr class="border-b group dark:border-gray-700">
                        <td colspan="7"
                            class="px-2 py-2 text-center group-hover:bg-gray-100 group-hover:text-primary">
                            Tidak ada data leveling dokter yang tersedia.
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
