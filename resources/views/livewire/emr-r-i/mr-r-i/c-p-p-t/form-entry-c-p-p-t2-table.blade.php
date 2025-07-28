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
                                    Instruksi
                                </x-sort-link>
                            </th>
                            <th scope="col" class="px-4 py-3">
                                <x-sort-link :active="false" wire:click.prevent="" role="button" href="#">
                                    Review
                                </x-sort-link>
                            </th>
                            <th scope="col" class="w-8 px-4 py-3 text-center">
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @php
                            use Carbon\Carbon;

                            $sortedCppt = collect($dataDaftarRi['cppt'] ?? [])
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

                                    <!-- Instruksi -->
                                    <td
                                        class="px-2 py-2 font-normal text-gray-700 whitespace-normal align-top w-1/7 group-hover:bg-gray-50">
                                        <div>
                                            {{ $cppt['instruction'] ?? '-' }}
                                        </div>
                                    </td>

                                    <!-- Review -->
                                    <td
                                        class="px-2 py-2 font-normal text-gray-700 whitespace-normal align-top w-1/7 group-hover:bg-gray-50">
                                        <div>
                                            {{ $cppt['review'] ?? '-' }}
                                        </div>
                                    </td>

                                    <!-- Action -->
                                    <td
                                        class="px-2 py-2 font-normal text-gray-700 whitespace-normal align-top w-1/7 group-hover:bg-gray-50">
                                        <x-alternative-button class="inline-flex"
                                            wire:click.prevent="removeCPPT('{{ $key }}')">
                                            <svg class="w-5 h-5 text-gray-800" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                viewBox="0 0 18 20">
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
