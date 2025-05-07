<!-- Tabel Assessment Ulang Nyeri -->
<div>
    <div class="w-full mb-1">
        <div class="grid grid-cols-1">
            <div id="AssessmentUlangNyeri" class="px-4">
                <!-- Table untuk Assessment Ulang Nyeri -->
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
                                    Metode Penilaian & TTV
                                </x-sort-link>
                            </th>
                            <th scope="col" class="px-4 py-3">
                                <x-sort-link :active="false" wire:click.prevent="" role="button" href="#">
                                    Pencetus, Durasi, Lokasi
                                </x-sort-link>
                            </th>
                            <th scope="col" class="px-4 py-3">
                                <x-sort-link :active="false" wire:click.prevent="" role="button" href="#">
                                    Waktu Nyeri, Kesadaran, Aktivitas
                                </x-sort-link>
                            </th>
                            <th scope="col" class="px-4 py-3">
                                <x-sort-link :active="false" wire:click.prevent="" role="button" href="#">
                                    Intervensi & Catatan Tambahan
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

                            $sortedNyeri = collect($dataDaftarRi['penilaian']['nyeri'] ?? [])
                                ->sortByDesc(function ($item) {
                                    $tgl = $item['tglPenilaian'] ?? '';
                                    if (!$tgl) {
                                        return 0;
                                    }
                                    try {
                                        return Carbon::createFromFormat('d/m/Y', $tgl, env('APP_TIMEZONE'))->timestamp;
                                    } catch (\Exception $e) {
                                        // Jika parsing gagal, jatuhkan ke urutan paling bawah
                                        return 0;
                                    }
                                })
                                ->values();
                        @endphp

                        @if ($sortedNyeri->isNotEmpty())
                            @foreach ($sortedNyeri as $key => $assessment)
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
                                    <!-- Metode Penilaian & TTV -->
                                    <td
                                        class="px-2 py-2 font-normal text-gray-700 w-1/7 group-hover:bg-gray-50 whitespace-nowrap">
                                        <div>
                                            <strong>Metode:</strong>
                                            {{ $assessment['nyeri']['nyeriMetode']['nyeriMetode'] ?? '-' }}
                                        </div>
                                        <div>
                                            <strong>Skor:</strong>
                                            {{ $assessment['nyeri']['nyeriMetode']['nyeriMetodeScore'] ?? '-' }}
                                        </div>
                                        <div>
                                            <strong>Keterangan:</strong> {{ $assessment['nyeri']['nyeriKet'] ?? '-' }}
                                        </div>
                                        <div>
                                            <strong>TTV:</strong>
                                        </div>
                                        <div>
                                            Tekanan Darah:
                                            {{ $assessment['nyeri']['sistolik'] ?? '-' }}/{{ $assessment['nyeri']['distolik'] ?? '-' }}
                                            mmHg
                                        </div>
                                        <div>
                                            Frekuensi Nafas: {{ $assessment['nyeri']['frekuensiNafas'] ?? '-' }} /menit
                                        </div>
                                        <div>
                                            Frekuensi Nadi: {{ $assessment['nyeri']['frekuensiNadi'] ?? '-' }} /menit
                                        </div>
                                        <div>
                                            Suhu: {{ $assessment['nyeri']['suhu'] ?? '-' }} °C
                                        </div>
                                    </td>
                                    <!-- Pencetus, Durasi, Lokasi -->
                                    <td
                                        class="px-2 py-2 font-normal text-gray-700 w-1/7 group-hover:bg-gray-50 whitespace-nowrap">
                                        <div>
                                            <strong>Pencetus:</strong><br>
                                            {{ $assessment['nyeri']['pencetus'] ?? '-' }}
                                        </div>
                                        <div>
                                            <strong>Durasi:</strong><br> {{ $assessment['nyeri']['durasi'] ?? '-' }}
                                        </div>
                                        <div>
                                            <strong>Lokasi:</strong><br> {{ $assessment['nyeri']['lokasi'] ?? '-' }}
                                        </div>
                                    </td>
                                    <!-- Waktu Nyeri, Tingkat Kesadaran, Tingkat Aktivitas -->
                                    <td
                                        class="px-2 py-2 font-normal text-gray-700 w-1/7 group-hover:bg-gray-50 whitespace-nowrap">
                                        <div>
                                            <strong>Waktu Nyeri:</strong><br>
                                            {{ $assessment['nyeri']['waktuNyeri'] ?? '-' }}
                                        </div>
                                        <div>
                                            <strong>Tingkat Kesadaran:</strong><br>
                                            {{ $assessment['nyeri']['tingkatKesadaran'] ?? '-' }}
                                        </div>
                                        <div>
                                            <strong>Tingkat Aktivitas:</strong><br>
                                            {{ $assessment['nyeri']['tingkatAktivitas'] ?? '-' }}
                                        </div>
                                    </td>
                                    <!-- Intervensi & Catatan Tambahan -->
                                    <td
                                        class="px-2 py-2 font-normal text-gray-700 w-1/7 group-hover:bg-gray-50 whitespace-nowrap">
                                        <div>
                                            <strong>Farmakologi:</strong><br>
                                            {{ $assessment['nyeri']['ketIntervensiFarmakologi'] ?? '-' }}
                                        </div>
                                        <div>
                                            <strong>Non-Farmakologi:</strong><br>
                                            {{ $assessment['nyeri']['ketIntervensiNonFarmakologi'] ?? '-' }}
                                        </div>
                                        <div>
                                            <strong>Catatan Tambahan:</strong><br>
                                            {{ $assessment['nyeri']['catatanTambahan'] ?? '-' }}
                                        </div>
                                    </td>
                                    <!-- Action -->
                                    <td
                                        class="px-2 py-2 font-normal text-gray-700 w-1/7 group-hover:bg-gray-50 whitespace-nowrap">
                                        <x-alternative-button class="inline-flex"
                                            wire:click.prevent="removeAssessmentNyeri('{{ $key }}')">
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
                                <td colspan="6" class="px-4 py-2 text-center text-gray-500">
                                    Tidak ada data assessment nyeri.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
