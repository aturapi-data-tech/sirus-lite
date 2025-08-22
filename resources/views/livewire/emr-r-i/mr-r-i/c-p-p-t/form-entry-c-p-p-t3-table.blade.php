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
                                    SOAP
                                </x-sort-link>
                            </th>
                            <th scope="col" class="px-4 py-3">
                                <x-sort-link :active="false" wire:click.prevent="" role="button" href="#">
                                    Instruksi &Plan Edukasi
                                </x-sort-link>
                            </th>

                            <th scope="col" class="px-4 py-3 text-center">
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @php
                            use Carbon\Carbon;

                            $sortedCppt = collect($dataDaftarRi['cppt'] ?? [])
                                ->where('profession', '!=', 'Dokter')
                                ->sortByDesc(function ($item) {
                                    $tgl = $item['tglCPPT'] ?? '';

                                    // Jika kosong, kembalikan 0 agar muncul paling bawah
                                    if (!$tgl) {
                                        return 0;
                                    }

                                    try {
                                        return Carbon::createFromFormat(
                                            'd/m/Y H:i:s',
                                            $tgl,
                                            env('APP_TIMEZONE'),
                                        )->timestamp;
                                    } catch (\Exception $e) {
                                        // Jika format salah, tetap kembalikan 0
                                        return 0;
                                    }
                                })
                                ->values();
                        @endphp

                        @if ($sortedCppt->isNotEmpty())
                            @foreach ($sortedCppt as $key => $cppt)
                                <tr class="border-b group">
                                    <!-- Tanggal & Petugas -->
                                    <td
                                        class="px-2 py-2 font-normal text-gray-700 whitespace-normal align-top w-1/7 group-hover:bg-gray-50">
                                        <div>
                                            <strong>Tanggal:</strong> {{ $cppt['tglCPPT'] ?? '-' }}
                                        </div>
                                        <div>
                                            <strong>Petugas:</strong> {{ $cppt['petugasCPPT'] ?? '-' }}
                                        </div>
                                        <div>
                                            <strong>Kode:</strong> {{ $cppt['petugasCPPTCode'] ?? '-' }}
                                        </div>
                                        <div>
                                            <strong>Profesi:</strong> {{ $cppt['profession'] ?? '-' }}
                                        </div>
                                    </td>

                                    <!-- SOAP -->
                                    <td
                                        class="px-2 py-2 font-normal text-gray-700 whitespace-normal align-top w-1/7 group-hover:bg-gray-50">
                                        <div>
                                            <strong>S:</strong> {{ $cppt['soap']['subjective'] ?? '-' }}<br>
                                            <strong>O:</strong> {{ $cppt['soap']['objective'] ?? '-' }}<br>
                                            <strong>A:</strong> {{ $cppt['soap']['assessment'] ?? '-' }}<br>
                                            <strong>P:</strong> {{ $cppt['soap']['plan'] ?? '-' }}
                                        </div>
                                    </td>

                                    <!-- Instruksi Plan Edukasi-->
                                    <td
                                        class="px-2 py-2 font-normal text-gray-700 whitespace-normal align-top w-1/7 group-hover:bg-gray-50">
                                        <div>
                                            <strong>Instruksi:</strong>{{ $cppt['instruction'] ?? '-' }}
                                        </div>
                                        <div>
                                            <strong>Plan Edukasi:</strong> {{ $cppt['review'] ?? '-' }}
                                        </div>
                                    </td>

                                    <!-- Action -->
                                    <td
                                        class="px-2 py-2 font-normal text-gray-700 whitespace-normal align-top w-1/7 group-hover:bg-gray-50">
                                        <div class="grid grid-cols-2 gap-4">

                                            <x-yellow-button class="inline-flex"
                                                wire:click.prevent="copyCPPT('{{ $cppt['tglCPPT'] ?? '-' }}')">
                                                <svg class="w-5 h-5 text-white" aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    fill="currentColor" viewBox="0 0 24 24">
                                                    <path fill-rule="evenodd"
                                                        d="M18 3a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-1V9a4 4 0 0 0-4-4h-3a1.99 1.99 0 0 0-1 .267V5a2 2 0 0 1 2-2h7Z"
                                                        clip-rule="evenodd" />
                                                    <path fill-rule="evenodd"
                                                        d="M8 7.054V11H4.2a2 2 0 0 1 .281-.432l2.46-2.87A2 2 0 0 1 8 7.054ZM10 7v4a2 2 0 0 1-2 2H4v6a2 2 0 0 0 2 2h7a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3Z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                Copy
                                            </x-yellow-button>

                                            <x-alternative-button class="inline-flex"
                                                wire:click.prevent="removeCPPT('{{ $cppt['tglCPPT'] ?? '-' }}')">
                                                <svg class="w-5 h-5 text-gray-800" aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                    viewBox="0 0 18 20">
                                                    <path
                                                        d="M17 4h-4V2a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v2H1a1 1 0 0 0 0 2h1v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6h1a1 1 0 1 0 0-2ZM7 2h4v2H7V2Zm1 14a1 1 0 1 1-2 0V8a1 1 0 0 1 2 0v8Zm4 0a1 1 0 0 1-2 0V8a1 1 0 0 1 2 0v8Z" />
                                                </svg>
                                                Hapus
                                            </x-alternative-button>
                                        </div>

                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="px-4 py-2 text-center text-gray-500">
                                    Tidak ada data CPPT.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
