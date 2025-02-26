<div>
    <div class="w-full mb-1">
        <div class="grid grid-cols-1">
            <div id="TabelCPPT" class="px-4">
                <!-- Table untuk CPPT -->
                <table class="w-full text-sm text-left text-gray-500 table-auto">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                        <tr>
                            <th scope="col" class="px-4 py-3">
                                <x-sort-link :active="false" wire:click.prevent="" role="button" href="#">
                                    Tanggal & Petugas
                                </x-sort-link>
                            </th>
                            <th scope="col" class="px-4 py-3">
                                <x-sort-link :active="false" wire:click.prevent="" role="button" href="#">
                                    Diagnosis Keperawatan
                                </x-sort-link>
                            </th>
                            <th scope="col" class="w-8 px-4 py-3 text-center">
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @isset($dataDaftarRi['asuhanKeperawatan'])
                            @foreach ($dataDaftarRi['asuhanKeperawatan'] as $key => $asuhanKeperawatan)
                                <tr class="border-b group">
                                    <!-- Tanggal & Petugas -->
                                    <td
                                        class="w-1/2 px-2 py-2 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap">
                                        <div>
                                            <strong>Tanggal:</strong> {{ $asuhan['tglAsuhanKeperawatan'] ?? '-' }}
                                        </div>
                                        <div>
                                            <strong>Petugas:</strong> {{ $asuhan['petugasAsuhanKeperawatan'] ?? '-' }}
                                        </div>
                                        <div>
                                            <strong>Kode:</strong> {{ $asuhan['petugasAsuhanKeperawatanCode'] ?? '-' }}
                                        </div>
                                    </td>

                                    <!-- Diagnosis Keperawatan -->
                                    <td
                                        class="w-1/2 px-2 py-2 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap">
                                        <div>
                                            <strong>ID Diagnosis:</strong> {{ $asuhan['diagKepId'] ?? '-' }}
                                        </div>
                                        <div>
                                            <strong>Deskripsi:</strong> {{ $asuhan['diagKepDesc'] ?? '-' }}
                                        </div>
                                        <div>
                                            <strong>Diagnosis:</strong>
                                            @if (!empty($asuhan['diagKep']))
                                                <ul class="list-disc list-inside">
                                                    @foreach ($asuhan['diagKep'] as $diag)
                                                        <li>{{ $diag }}</li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                -
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Action -->
                                    <td
                                        class="px-2 py-2 font-normal text-gray-700 w-1/7 group-hover:bg-gray-50 whitespace-nowrap">
                                        <x-alternative-button class="inline-flex"
                                            wire:click.prevent="removeAsuhanKeperawatan('{{ $key }}')">
                                            <svg class="w-5 h-5 text-gray-800" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                                                <path
                                                    d="M17 4h-4V2a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v2H1a1 1 0 0 0 0 2h1v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6h1a1 1 0 1 0 0-2ZM7 2h4v2H7V2Zm1 14a1 1 0 1 1-2 0V8a1 1 0 0 1 2 0v8Zm4 0a1 1 0 0 1-2 0V8a1 1 0 0 1 2 0v8Z" />
                                            </svg>
                                            Hapus
                                        </x-alternative-button>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="3" class="px-4 py-2 text-center text-gray-500">
                                    Tidak ada data Asuhan Keperawatan.
                                </td>
                            </tr>
                        @endisset
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
