<!DOCTYPE html>
<html lang="id">

<head>
    <style>
        @page {
            size: A4;
            margin: 8px;
        }
    </style>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- Tailwind bundle kamu --}}
    <link href="build/assets/sirus.css" rel="stylesheet">
</head>

<body class="text-xs">

    @php
        $pasien = (array) ($dataPasien['pasien'] ?? []);
        $rm = (string) ($pasien['regNo'] ?? '');
        $nama = (string) ($pasien['regName'] ?? '');
        $formB = $dataFormB;

        // Cari Form A yang terkait
        $formA = collect($dataDaftarRi['formMPP']['formA'] ?? [])->firstWhere('formA_id', $formB['formA_id'] ?? '');
    @endphp

    {{-- ===== HEADER ===== --}}
    <table class="w-full p-1 border border-separate border-black rounded-md">
        <tr>
            {{-- kiri: logo & alamat RS --}}
            <td class="align-top w-[180px] border-0">
                <table class="w-full border-0 border-collapse">
                    <tr>
                        <td class="pb-1 text-center">
                            <img src="madinahlogopersegi.png" alt="Logo"
                                class="inline-block object-contain w-auto h-20">
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center leading-tight text-[10px]">
                            <div class="font-semibold uppercase">
                                {{ $identitasRs->int_name ?? 'RUMAH SAKIT' }}
                            </div>
                            <div class="mt-1">
                                {{ trim($identitasRs->int_address ?? '-') }}<br>
                                {{ strtoupper($identitasRs->int_city ?? '') }}
                            </div>
                            @php
                                $tel1 = $identitasRs->int_phone1 ?? null;
                                $tel2 = $identitasRs->int_phone2 ?? null;
                                $fax = $identitasRs->int_fax ?? null;
                            @endphp
                            @if ($tel1)
                                <div>{{ $tel1 }}</div>
                            @endif
                            @if ($tel2)
                                <div>{{ $tel2 }}</div>
                            @endif
                            @if ($fax)
                                <div>Fax: {{ $fax }}</div>
                            @endif
                        </td>
                    </tr>
                </table>
            </td>

            {{-- kanan: identitas pasien --}}
            <td class="align-top border-0">
                <table class="w-full border-0 border-collapse text-[11px]">
                    <tr class="border-b">
                        <td class="py-1 pr-2 w-[140px] text-gray-700">Nama Pasien :</td>
                        <td class="py-1">
                            <span class="font-bold">{{ strtoupper($pasien['regName'] ?? '-') }}</span>
                            <span class="font-normal">
                                / {{ $pasien['jenisKelamin']['jenisKelaminDesc'] ?? '-' }} /
                                {{ $pasien['thn'] ?? '-' }}
                            </span>
                        </td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-1 pr-2 text-gray-700">No RM :</td>
                        <td class="py-1 font-extrabold text-[13px]">{{ $rm }}</td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-1 pr-2 text-gray-700">Tanggal Lahir :</td>
                        <td class="py-1">
                            {{ $pasien['tglLahir'] ?? '-' }}
                            <span class="ml-6 text-gray-700">NIK :</span>
                            <span class="ml-1">{{ $pasien['identitas']['nik'] ?? '-' }}</span>
                        </td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-1 pr-2 text-gray-700">Alamat :</td>
                        <td class="py-1">{{ $pasien['identitas']['alamat'] ?? '-' }}</td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-1 pr-2 text-gray-700">ID BPJS :</td>
                        <td class="py-1">
                            {{ $pasien['identitas']['idbpjs'] ?? '-' }}
                            <span class="ml-6 text-gray-700">Klaim :</span>
                            <span class="ml-1">{{ $dataDaftarRi['klaim'] ?? 'BPJS' }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-1 pr-2 text-gray-700">Tanggal Masuk :</td>
                        <td class="py-1">{{ $dataDaftarRi['entryDate'] ?? '-' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- ===== JUDUL FORM B ===== --}}
    <table class="w-full mt-4">
        <tr>
            <td class="text-center">
                <h1 class="text-lg font-bold">FORM B - PELAKSANAAN, MONITORING, ADVOKASI, TERMINASI</h1>
                <p class="text-sm">Case Manager Rawat Inap</p>
            </td>
        </tr>
    </table>

    {{-- ===== INFORMASI UMUM ===== --}}
    <table class="w-full mt-2 border border-collapse border-black">
        <tr>
            <th class="w-[180px] px-2 py-1 text-left border border-black bg-gray-100">Tanggal Kegiatan</th>
            <td class="px-2 py-1 border border-black">{{ $formB['tanggal'] ?? '-' }}</td>
        </tr>
        <tr>
            <th class="px-2 py-1 text-left bg-gray-100 border border-black">Referensi Form A</th>
            <td class="px-2 py-1 border border-black">
                {{ $formA['tanggal'] ?? '-' }} -
                {{ Str::limit($formA['perencanaanAwal']['tujuanPendampingan'] ?? '', 50) }}
            </td>
        </tr>
    </table>

    {{-- ===== PELAKSANAAN MONITORING ===== --}}
    @if (!empty($formB['pelaksanaanMonitoring']))
        <table class="w-full mt-2 border border-collapse border-black">
            <tr>
                <th class="px-2 py-1 text-left bg-gray-100 border border-black">Pelaksanaan dan Monitoring</th>
            </tr>
            <tr>
                <td class="px-2 py-1 border border-black">{{ $formB['pelaksanaanMonitoring'] }}</td>
            </tr>
        </table>
    @endif

    {{-- ===== ADVOKASI & KOLABORASI ===== --}}
    @if (
        !empty($formB['advokasiKolaborasi']['hambatanPasien']) ||
            !empty($formB['advokasiKolaborasi']['kolaborasiDengan']) ||
            !empty($formB['advokasiKolaborasi']['advokasiDilakukan']) ||
            !empty($formB['advokasiKolaborasi']['eskalasi']))
        <table class="w-full mt-2 border border-collapse border-black">
            <tr>
                <th colspan="2" class="px-2 py-1 text-center bg-gray-100 border border-black">ADVOKASI & KOLABORASI
                </th>
            </tr>

            {{-- Hambatan Pasien --}}
            @if (!empty($formB['advokasiKolaborasi']['hambatanPasien']))
                <tr>
                    <th class="w-[180px] px-2 py-1 text-left border border-black bg-gray-100">Hambatan Pasien</th>
                    <td class="px-2 py-1 border border-black">{{ $formB['advokasiKolaborasi']['hambatanPasien'] }}</td>
                </tr>
            @endif

            {{-- Kolaborasi Dengan --}}
            @if (!empty($formB['advokasiKolaborasi']['kolaborasiDengan']))
                <tr>
                    <th class="px-2 py-1 text-left bg-gray-100 border border-black">Kolaborasi Dengan</th>
                    <td class="px-2 py-1 border border-black">{{ $formB['advokasiKolaborasi']['kolaborasiDengan'] }}
                    </td>
                </tr>
            @endif

            {{-- Advokasi Dilakukan --}}
            @if (!empty($formB['advokasiKolaborasi']['advokasiDilakukan']))
                <tr>
                    <th class="px-2 py-1 text-left bg-gray-100 border border-black">Advokasi Dilakukan</th>
                    <td class="px-2 py-1 border border-black">{{ $formB['advokasiKolaborasi']['advokasiDilakukan'] }}
                    </td>
                </tr>
            @endif

            {{-- Eskalasi --}}
            @if (!empty($formB['advokasiKolaborasi']['eskalasi']))
                <tr>
                    <th class="px-2 py-1 text-left bg-gray-100 border border-black">Eskalasi</th>
                    <td class="px-2 py-1 border border-black">{{ $formB['advokasiKolaborasi']['eskalasi'] }}</td>
                </tr>
            @endif
        </table>
    @endif

    {{-- ===== TERMINASI ===== --}}
    @if (!empty($formB['terminasi']))
        <table class="w-full mt-2 border border-collapse border-black">
            <tr>
                <th class="px-2 py-1 text-left bg-gray-100 border border-black">Ringkasan Terminasi</th>
            </tr>
            <tr>
                <td class="px-2 py-1 border border-black">{{ $formB['terminasi'] }}</td>
            </tr>
        </table>
    @endif

    {{-- ===== TANDA TANGAN PETUGAS ===== --}}
    <table class="w-full mt-4 border border-collapse border-black">
        <tr>
            <th colspan="2" class="px-2 py-1 text-center bg-gray-100 border border-black">TANDA TANGAN PETUGAS</th>
        </tr>
        <tr>
            <th class="w-[180px] px-2 py-1 text-left border border-black bg-gray-100">Nama Petugas</th>
            <td class="px-2 py-1 border border-black">{{ $formB['tandaTanganPetugas']['petugasName'] ?? '-' }}</td>
        </tr>
        <tr>
            <th class="px-2 py-1 text-left bg-gray-100 border border-black">Kode Petugas</th>
            <td class="px-2 py-1 border border-black">{{ $formB['tandaTanganPetugas']['petugasCode'] ?? '-' }}</td>
        </tr>
        <tr>
            <th class="px-2 py-1 text-left bg-gray-100 border border-black">Jabatan</th>
            <td class="px-2 py-1 border border-black">{{ $formB['tandaTanganPetugas']['jabatan'] ?? 'MPP' }}</td>
        </tr>
    </table>

    {{-- ===== TTD PETUGAS ===== --}}
    <table class="w-full mt-8">
        <tr>
            <td class="text-center w-60">
                <div class="text-right text-[9px] text-gray-600">Tanggal: {{ $formB['tanggal'] ?? '-' }}</div>

                {{-- kotak TTD --}}
                @php
                    $petugasCode = $formB['tandaTanganPetugas']['petugasCode'] ?? '';
                    $user = $petugasCode ? App\Models\User::where('myuser_code', $petugasCode)->first() : null;
                    $ttdPetugas = $user && $user->myuser_ttd_image ? asset('storage/' . $user->myuser_ttd_image) : null;
                @endphp
                <div class="flex items-center justify-center h-20 mt-4 overflow-hidden bg-white border border-black">
                    @if ($ttdPetugas)
                        <img src="{{ $ttdPetugas }}" alt="TTD Petugas"
                            class="block object-contain w-auto mx-auto max-h-16">
                    @else
                        <span class="text-[10px] text-gray-600">TTD Petugas</span>
                    @endif
                </div>

                <div class="mt-2 text-center text-[11px] border-t border-black pt-1">
                    ( {{ $formB['tandaTanganPetugas']['petugasName'] ?? '................................' }} )
                    <div class="text-[10px] text-gray-600">Petugas MPP</div>
                </div>
            </td>
        </tr>
    </table>

</body>


</html>
