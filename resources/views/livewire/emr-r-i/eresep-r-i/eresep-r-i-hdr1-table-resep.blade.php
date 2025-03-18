<div class="flex flex-col my-2">
    <div class="overflow-x-auto rounded-lg">
        <div class="inline-block min-w-full align-middle">
            <div class="overflow-hidden shadow sm:rounded-lg">
                <table class="w-full text-sm text-left text-gray-500 table-fixed dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="w-1/6 px-2 py-1">
                                Keterangan
                            </th>
                            <th scope="col" class="w-4/6 px-2 py-1">
                                Resep
                            </th>
                            <th scope="col" class="w-1/6 px-2 py-1 text-center">
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800">
                        @foreach ($dataDaftarRi['eresepHdr'] ?? [] as $key => $header)
                            @if (isset($header['resepNo']))
                                <tr class="border-b group dark:border-gray-700">
                                    <td class="w-1/6 px-2 py-1 whitespace-nowrap">
                                        <span class="font-semibold text-primary">{{ $header['regNo'] }}</span>
                                        </br>
                                        <span>
                                            {{ $header['resepDate'] }}
                                        </span>
                                    </td>
                                    <td class="w-4/6 px-2 py-1 whitespace-nowrap">
                                        <div>
                                            <div>
                                                <span class="font-semibold text-gray-900">
                                                    {{ 'No. ' . $header['resepNo'] }}
                                                </span>
                                            </div>
                                            <!-- Tampilkan detail resep (eresep) jika ada -->
                                            <div class="grid grid-cols-2 gap-2">
                                                <div>
                                                    @include('livewire.emr-r-i.eresep-r-i.eresep-r-i-hdr2-table-resep-data')
                                                </div>
                                                <div>
                                                    @include('livewire.emr-r-i.eresep-r-i.eresep-r-i-hdr3-table-resep-data-racikan')
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-2 gap-2">
                                                <div class="">
                                                    <x-yellow-button
                                                        wire:click="showResepHdr('{{ $header['resepNo'] }}')"
                                                        class="" wire:loading.remove>
                                                        Tampil Data {{ ' No. ' . $header['resepNo'] }}
                                                    </x-yellow-button>
                                                    <div wire:loading wire:target="showResepHdr">
                                                        <x-loading />
                                                    </div>
                                                </div>

                                                <div class="grid place-items-center">
                                                    @php
                                                        $ttdDokterBgProperty = isset(
                                                            $header['tandaTanganDokter']['dokterPeresep'],
                                                        )
                                                            ? 'font-semibold text-primary'
                                                            : '';
                                                    @endphp
                                                    <span class="{{ $ttdDokterBgProperty }}">
                                                        {{ '(' }}
                                                        {{ $header['tandaTanganDokter']['dokterPeresep'] ?? 'belum ada tandatangan' }}
                                                        {{ ')' }}
                                                    </span>
                                                </div>

                                                <div>
                                                    <livewire:cetak.cetak-eresep-r-i :riHdrNoRef="$riHdrNoRef" :resepNoRef="$header['resepNo']"
                                                        wire:key="cetak.cetak-eresep-r-i-{{ $riHdrNoRef }}-{{ $header['resepNo'] }}">
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="w-1/6 px-2 py-1 text-center">
                                        <div class="">
                                            <x-alternative-button
                                                wire:click="removeResepHdr('{{ $header['resepNo'] }}')" class="ml-2"
                                                wire:loading.remove>
                                                <svg class="w-5 h-5 text-gray-800" aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                    viewBox="0 0 18 20">
                                                    <path
                                                        d="M17 4h-4V2a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v2H1a1 1 0 0 0 0 2h1v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6h1a1 1 0 1 0 0-2ZM7 2h4v2H7V2Zm1 14a1 1 0 1 1-2 0V8a1 1 0 0 1 2 0v8Zm4 0a1 1 0 0 1-2 0V8a1 1 0 0 1 2 0v8Z" />
                                                </svg>
                                            </x-alternative-button>
                                            <div wire:loading wire:target="removeResepHdr">
                                                <x-loading />
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach

                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>
