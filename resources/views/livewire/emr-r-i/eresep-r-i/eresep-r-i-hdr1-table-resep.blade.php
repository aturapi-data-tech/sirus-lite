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
                        @foreach (collect($dataDaftarRi['eresepHdr'] ?? [])->sortByDesc('resepDate') as $key => $header)
                            @if (isset($header['resepNo']))
                                <tr wire:key="resep-hdr-{{ $riHdrNoRef }}-{{ $header['resepNo'] }}"
                                    class="border-b group dark:border-gray-700">
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
                                                <div class="grid grid-cols-3 gap-2">
                                                    {{-- Tampil Data --}}
                                                    <x-yellow-button wire:click="showResepHdr({{ $header['resepNo'] }})"
                                                        wire:loading.remove>
                                                        @if (isset($header['tandaTanganDokter']['dokterPeresep']))
                                                            {{-- Icon mata untuk Tampil --}}
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                class="inline-block w-4 h-4 mr-1" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943
                                                                           9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                            </svg>
                                                            Resep {{ $header['resepNo'] }}
                                                        @else
                                                            {{-- Icon pensil untuk Edit --}}
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                class="inline-block w-4 h-4 mr-1" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M11 5h2m2 0h2a2 2 0 012 2v2m0 2v2m0 2v2a2 2 0 01-2 2h-2m-2
                                                                           0h-2m-2 0H7a2 2 0 01-2-2v-2m0-2v-2m0-2V7a2 2 0 012-2h2" />
                                                            </svg>
                                                            Edit Resep {{ $header['resepNo'] }}
                                                        @endif
                                                    </x-yellow-button>

                                                    <div wire:loading wire:target="showResepHdr">
                                                        <x-loading />
                                                    </div>

                                                    {{-- Simpan Plan Cppt --}}
                                                    <x-green-button
                                                        wire:click="simpanPlanCppt({{ $header['resepNo'] }})"
                                                        wire:loading.remove>
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            class="inline-block w-4 h-4 mr-1" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        Simpan Cppt
                                                    </x-green-button>
                                                    <div wire:loading wire:target="simpanPlanCppt">
                                                        <x-loading />
                                                    </div>

                                                    {{-- Copy Resep --}}
                                                    <x-yellow-button
                                                        wire:click="copyResepHdrInap({{ $header['resepNo'] }})"
                                                        wire:loading.remove>
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            class="inline-block w-4 h-4 mr-1" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M8 16h8M8 12h8m-6 8h6a2 2 0 002-2V8l-6-6H8a2 2 0 00-2 2v4" />
                                                        </svg>
                                                        Copy Resep
                                                    </x-yellow-button>
                                                    <div wire:loading wire:target="copyResepHdrInap">
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
