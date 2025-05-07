<!-- Tabel Daftar Penilaian Risiko Jatuh -->
<div>
    <div class="w-full mb-1">
        <div class="grid grid-cols-1">
            <div id="AssessmentUlangResikoJatuh" class="px-4">
                <!-- Table untuk Assessment Ulang Risiko Jatuh -->
                <table class="w-full text-sm text-left text-gray-500 table-auto">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                        <tr>
                            <th scope="col" class="px-4 py-3">
                                <x-sort-link :active="false" wire:click.prevent="" role="button" href="#">
                                    Tanggal & Petugas Penilai
                                </x-sort-link>
                            </th>
                            <th scope="col" class="px-4 py-3">
                                <x-sort-link :active="false" wire:click.prevent="" role="button" href="#">
                                    Metode Penilaian, Skor, Kategori & Rekomendasi
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

                            $sortedResikoJatuh = collect($dataDaftarRi['penilaian']['resikoJatuh'] ?? [])
                                ->sortByDesc(function ($item) {
                                    $tgl = $item['tglPenilaian'] ?? '';
                                    if (!$tgl) {
                                        return 0; // jadikan paling bawah jika kosong
                                    }
                                    try {
                                        return Carbon::createFromFormat('d/m/Y', $tgl, env('APP_TIMEZONE'))->timestamp;
                                    } catch (\Exception $e) {
                                        return 0; // format salah, juga paling bawah
                                    }
                                })
                                ->values();
                        @endphp

                        @if ($sortedResikoJatuh->isNotEmpty())
                            @foreach ($sortedResikoJatuh as $key => $assessment)
                                <tr class="border-b group">
                                    <!-- Tanggal & Petugas Penilai -->
                                    <td
                                        class="px-2 py-2 font-normal text-gray-700 w-1/7 group-hover:bg-gray-50 whitespace-nowrap">
                                        <div>
                                            <strong>Tanggal:</strong> {{ $assessment['tglPenilaian'] ?? '-' }}
                                        </div>
                                        <div>
                                            <strong>Petugas:</strong> {{ $assessment['petugasPenilai'] ?? '-' }}
                                        </div>
                                        <div>
                                            <strong>Kode:</strong> {{ $assessment['petugasPenilaiCode'] ?? '-' }}
                                        </div>
                                    </td>
                                    <!-- Metode Penilaian, Skor, Kategori & Rekomendasi -->
                                    <td
                                        class="px-2 py-2 font-normal text-gray-700 w-1/7 group-hover:bg-gray-50 whitespace-nowrap">
                                        <div>
                                            <strong>Metode:</strong>
                                            {{ $assessment['resikoJatuh']['resikoJatuhMetode']['resikoJatuhMetode'] ?? '-' }}
                                        </div>
                                        <div>
                                            <strong>Skor:</strong>
                                            {{ $assessment['resikoJatuh']['resikoJatuhMetode']['resikoJatuhMetodeScore'] ?? '-' }}
                                        </div>
                                        <div>
                                            <strong>Kategori Risiko:</strong>
                                            {{ $assessment['resikoJatuh']['kategoriResiko'] ?? '-' }}
                                        </div>
                                        <div>
                                            <strong>Rekomendasi:</strong><br>
                                            {{ $assessment['resikoJatuh']['rekomendasi'] ?? '-' }}
                                        </div>
                                    </td>
                                    <!-- Action -->
                                    <td
                                        class="px-2 py-2 font-normal text-gray-700 w-1/7 group-hover:bg-gray-50 whitespace-nowrap">
                                        <x-alternative-button class="inline-flex"
                                            wire:click.prevent="removeAssessmentResikoJatuh('{{ $key }}')">
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
                                <td colspan="3" class="px-4 py-2 text-center text-gray-500">
                                    Tidak ada data penilaian risiko jatuh.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
