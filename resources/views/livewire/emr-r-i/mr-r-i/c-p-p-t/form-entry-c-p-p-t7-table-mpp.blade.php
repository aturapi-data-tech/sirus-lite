<div>
    <div class="w-full mb-1">
        <div class="grid grid-cols-1">
            <div id="TabelMPP" class="px-4">

                @if (!empty($dataDaftarRi['formMPP']['formA']))
                    <div class="overflow-x-auto bg-white rounded-lg shadow">
                        <table class="w-full text-sm text-left text-gray-500 table-auto">
                            <thead class="sticky top-0 text-xs text-gray-900 uppercase bg-gray-100">
                                <tr>
                                    <th scope="col" class="px-4 py-3">No</th>
                                    <th scope="col" class="px-4 py-3">Form</th>
                                    <th scope="col" class="px-4 py-3">Keterangan</th>
                                    <th scope="col" class="px-4 py-3">Petugas</th>
                                </tr>
                            </thead>

                            <tbody class="bg-white">
                                @php
                                    $counter = 1;
                                @endphp

                                @foreach ($dataDaftarRi['formMPP']['formA'] as $formA)
                                    {{-- Baris Form A --}}
                                    <tr class="border-b group hover:bg-blue-50">
                                        <td class="px-4 py-3 font-semibold">{{ $counter++ }}</td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2 mb-8">
                                                <span class="font-semibold text-green-700">Form A Skrining
                                                    Awal</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="font-medium text-gray-900">{{ $formA['tanggal'] }}</div>

                                            @if (!empty($formA['indentifikasiKasus']))
                                                <div class="text-sm text-gray-900">
                                                    <span class="font-semibold text-red-600">Identifikasi Kasus:</span>
                                                    {{ $formA['indentifikasiKasus'] }}
                                                </div>
                                            @endif

                                            @if (!empty($formA['assessment']))
                                                <div class="mt-1 text-sm text-gray-900">
                                                    <span class="font-semibold text-gray-600">Assessment:</span>
                                                    {{ $formA['assessment'] }}
                                                </div>
                                            @endif

                                            @if (!empty($formA['perencanaan']))
                                                <div class="mt-1 text-sm text-gray-900">
                                                    <span class="font-semibold text-gray-600">Perencanaan:</span>
                                                    {{ $formA['perencanaan'] }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $formA['tandaTanganPetugas']['petugasName'] ?? '' }}</div>
                                            <div class="text-xs text-gray-500">
                                                {{ $formA['tandaTanganPetugas']['jabatan'] ?? '' }}</div>
                                            <div class="text-xs text-gray-400">
                                                {{ $formA['tandaTanganPetugas']['petugasCode'] ?? '' }}</div>
                                        </td>

                                    </tr>

                                    {{-- Baris Form B yang terkait --}}
                                    @php
                                        $relatedFormBs = collect($dataDaftarRi['formMPP']['formB'] ?? [])
                                            ->where('formA_id', $formA['formA_id'])
                                            ->values();
                                    @endphp

                                    @foreach ($relatedFormBs as $formB)
                                        <tr class="border-b group hover:bg-green-50">
                                            <td class="px-4 py-3 font-semibold">{{ $counter++ }}</td>
                                            <td class="px-4 py-3">
                                                <div class="flex items-center gap-2 mb-8">
                                                    <span class="font-semibold text-gray-500">Form B Tindak
                                                        Lanjut</span>
                                                </div>


                                            </td>

                                            <td class="px-4 py-3">
                                                <div class="font-medium text-gray-900">{{ $formB['tanggal'] }}
                                                </div>

                                                {{-- PELAKSANAAN MONITORING --}}
                                                @if (!empty($formB['pelaksanaanMonitoring']))
                                                    <div class="mb-1 text-sm text-gray-900">
                                                        <span class="font-semibold text-gray-600">Monitoring:</span>
                                                        {{ $formB['pelaksanaanMonitoring'] }}
                                                    </div>
                                                @endif

                                                {{-- ADVOKASI & KOLABORASI --}}
                                                @if (!empty($formB['advokasiKolaborasi']))
                                                    <div class="mb-1 text-sm text-gray-900">
                                                        <span class="font-semibold text-gray-600">Advokasi &
                                                            Kolaborasi:</span>
                                                        {{ $formB['advokasiKolaborasi'] }}
                                                    </div>
                                                @endif

                                                {{-- TERMINASI --}}
                                                @if (!empty($formB['terminasi']))
                                                    <div class="text-sm text-gray-900">
                                                        <span class="font-semibold text-gray-600">Terminasi:</span>
                                                        {{ $formB['terminasi'] }}
                                                    </div>
                                                @endif

                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $formB['tandaTanganPetugas']['petugasName'] ?? '' }}</div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $formB['tandaTanganPetugas']['jabatan'] ?? '' }}</div>
                                                <div class="text-xs text-gray-400">
                                                    {{ $formB['tandaTanganPetugas']['petugasCode'] ?? '' }}</div>
                                            </td>
                                        </tr>
                                    @endforeach

                                    {{-- Spacer antara grup --}}
                                    @if (!$loop->last)
                                        <tr>
                                            <td colspan="6" class="px-4 py-2">
                                                <div class="border-t border-gray-300"></div>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach

                                @if (empty($dataDaftarRi['formMPP']['formA']))
                                    <tr class="border-b">
                                        <td colspan="6" class="px-4 py-8 text-center text-gray-500 bg-gray-50">
                                            <div class="flex flex-col items-center justify-center">
                                                <svg class="w-12 h-12 mb-3 text-gray-400" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <p class="font-medium">Belum ada data Case Manager</p>
                                                <p class="text-sm">Silakan buat Form A terlebih dahulu</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-8 text-center text-gray-500 bg-gray-100 rounded-lg">
                        Belum ada data MPP. Silakan isi Form A terlebih dahulu.
                    </div>
                @endif



            </div>
        </div>
    </div>
</div>
