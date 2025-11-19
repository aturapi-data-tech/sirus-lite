<div>
    <div class="w-full mb-1">
        <div class="grid grid-cols-1">
            <div id="TabelCPPT" class="px-4">
                <!-- Table untuk CPPT -->
                <table class="w-full text-sm text-left text-gray-700 border-collapse table-fixed">
                    <colgroup>
                        <col class="w-[22%]">
                        <col class="w-[38%]">
                        <col class="w-[30%]">
                        <col class="w-[10%]">
                    </colgroup>

                    <thead class="text-xs font-semibold text-gray-700 uppercase bg-gray-100">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left">
                                <x-sort-link :active="false" wire:click.prevent="" role="button" href="#">
                                    Tanggal &amp; Petugas
                                </x-sort-link>
                            </th>
                            <th scope="col" class="px-4 py-3 text-left">
                                <x-sort-link :active="false" wire:click.prevent="" role="button" href="#">
                                    SOAP
                                </x-sort-link>
                            </th>
                            <th scope="col" class="px-4 py-3 text-left">
                                <x-sort-link :active="false" wire:click.prevent="" role="button" href="#">
                                    Instruksi &amp; Review
                                </x-sort-link>
                            </th>
                            <th scope="col" class="px-4 py-3 text-center">Action</th>
                        </tr>
                    </thead>

                    @php
                        use Carbon\Carbon;

                        $sortedCppt = collect($dataDaftarRi['cppt'] ?? [])
                            ->where('profession', '=', 'Dokter')
                            ->sortByDesc(function ($item) {
                                $tgl = $item['tglCPPT'] ?? '';
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
                                    return 0;
                                }
                            })
                            ->values();
                    @endphp

                    <tbody class="bg-white divide-y divide-gray-100">
                        @if ($sortedCppt->isNotEmpty())
                            @foreach ($sortedCppt as $key => $cppt)
                                <tr wire:key="cppt-dokter-row-{{ $cppt['cpptId'] ?? $key }}"
                                    class="transition-colors duration-150 hover:bg-gray-50">
                                    <!-- Tanggal & Petugas -->
                                    <td class="px-3 py-2 leading-relaxed whitespace-pre-line align-top">
                                        <div><span class="font-semibold">Tanggal:</span> {{ $cppt['tglCPPT'] ?? '-' }}
                                        </div>
                                        <div><span class="font-semibold">Petugas:</span>
                                            {{ $cppt['petugasCPPT'] ?? '-' }}</div>
                                        <div><span class="font-semibold">Kode:</span>
                                            {{ $cppt['petugasCPPTCode'] ?? '-' }}</div>
                                        <div><span class="font-semibold">Profesi:</span>
                                            {{ $cppt['profession'] ?? '-' }}</div>
                                    </td>

                                    <!-- SOAP -->
                                    <td class="px-3 py-2 leading-relaxed whitespace-pre-line align-top">
                                        <div><span class="font-semibold">S:</span>
                                            {{ $cppt['soap']['subjective'] ?? '-' }}</div>
                                        <div><span class="font-semibold">O:</span>
                                            {{ $cppt['soap']['objective'] ?? '-' }}</div>
                                        <div><span class="font-semibold">A:</span>
                                            {{ $cppt['soap']['assessment'] ?? '-' }}</div>
                                        <div><span class="font-semibold">P:</span> {{ $cppt['soap']['plan'] ?? '-' }}
                                        </div>
                                    </td>

                                    <!-- Instruksi & Review -->
                                    <td class="px-3 py-2 leading-relaxed whitespace-pre-line align-top">
                                        <div><span class="font-semibold">Instruksi:</span>
                                            {{ $cppt['instruction'] ?? '-' }}</div>
                                        <div><span class="font-semibold">Review:</span>
                                            {{ $cppt['review'] ?? '-' }}</div>
                                    </td>

                                    <!-- Action -->
                                    <td class="px-3 py-2 align-middle">
                                        <div class="flex items-center justify-center gap-2">
                                            <x-yellow-button class="px-2 py-1 text-xs"
                                                wire:click.prevent="copyCPPT('{{ $cppt['cpptId'] ?? '' }}')">
                                                Copy
                                            </x-yellow-button>

                                            <x-alternative-button class="px-2 py-1 text-xs"
                                                wire:click.prevent="removeCPPT('{{ $cppt['cpptId'] ?? '' }}')">
                                                Hapus
                                            </x-alternative-button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-gray-500">
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
