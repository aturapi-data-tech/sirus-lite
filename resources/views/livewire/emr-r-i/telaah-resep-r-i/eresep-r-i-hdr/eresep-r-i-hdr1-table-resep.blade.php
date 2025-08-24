<div class="flex flex-col my-2">
    <div class="overflow-x-auto rounded-lg">
        <div class="inline-block min-w-full align-middle">
            <div class="overflow-hidden shadow sm:rounded-lg">
                <table class="w-full text-sm text-left text-gray-500 table-fixed dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-2 py-1">Resep</th>
                        </tr>
                    </thead>

                    @php
                        $riHdrNo = data_get($dataDaftarRi, 'riHdrNo');
                        $headers = collect(data_get($dataDaftarRi, 'eresepHdr', []))->sortByDesc('resepDate')->values();
                    @endphp

                    <tbody class="bg-white dark:bg-gray-800">
                        @forelse ($headers as $idx => $header)
                            @php
                                $resepNo = data_get($header, 'resepNo');
                            @endphp

                            @continue(!$resepNo)

                            @php
                                $regNo = data_get($header, 'regNo', '-');
                                $resepDate = data_get($header, 'resepDate', '-');
                                $dokterTtd = data_get($header, 'tandaTanganDokter.dokterPeresep');
                                $ttdClass = $dokterTtd ? 'font-semibold text-primary' : 'text-gray-500';
                            @endphp

                            <tr wire:key="resep-hdr-{{ $riHdrNo }}-{{ $resepNo }}"
                                class="border-b group dark:border-gray-700">
                                <td class="px-2 py-1 whitespace-nowrap">
                                    <div class="space-y-2">

                                        <div>
                                            <span class="text-2xl font-semibold text-gray-700">
                                                {{ 'No. ' . $resepNo }}
                                            </span>
                                        </div>

                                        <div>
                                            <span class="font-semibold text-primary">{{ $regNo }}</span><br>
                                            <span>{{ $resepDate }}</span>
                                        </div>

                                        <div class="grid grid-cols-1 gap-2">
                                            <div>
                                                @include('livewire.emr-r-i.telaah-resep-r-i.eresep-r-i-hdr.eresep-r-i-hdr2-table-resep-data')
                                            </div>
                                            <div>
                                                @include('livewire.emr-r-i.telaah-resep-r-i.eresep-r-i-hdr.eresep-r-i-hdr3-table-resep-data-racikan')
                                            </div>
                                        </div>

                                        <div class="grid place-items-center">
                                            <span class="{{ $ttdClass }}">
                                                ({{ $dokterTtd ?? 'belum ada tandatangan' }})
                                            </span>
                                        </div>

                                        <div>
                                            <livewire:cetak.cetak-eresep-r-i :riHdrNoRef="$riHdrNo" :resepNoRef="$resepNo"
                                                wire:key="cetak-eresep-r-i-{{ $riHdrNo }}-{{ $resepNo }}" />
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-2 py-3 text-center text-gray-500">Tidak ada data resep.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
