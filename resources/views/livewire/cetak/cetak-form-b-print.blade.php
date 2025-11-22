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
        use Illuminate\Support\Str;

        $pasien = (array) ($dataPasien['pasien'] ?? []);
        $rm = (string) ($pasien['regNo'] ?? '');
        $nama = (string) ($pasien['regName'] ?? '');
        $formB = $dataFormB ?? [];

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
                <h1 class="text-lg font-bold">FORM B - PELAKSANAAN KEGIATAN</h1>
                <p class="text-sm">Hasil Pelayanan, Monitoring, Koordinasi, Komunikasi, Advokasi dan Terminasi</p>
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
                @if ($formA)
                    {{-- {{ $formA['tanggal'] ?? '-' }} - --}}
                    {{ Str::limit($formA['indentifikasiKasus'] ?? ($formA['assessment'] ?? ''), 70) }}
                @else
                    -
                @endif
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
                <td class="px-2 py-1 border border-black">
                    {{ $formB['pelaksanaanMonitoring'] }}
                </td>
            </tr>
        </table>
    @endif

    {{-- ===== ADVOKASI dan KOLABORASI (STRING) ===== --}}
    @if (!empty($formB['advokasiKolaborasi']))
        <table class="w-full mt-2 border border-collapse border-black">
            <tr>
                <th class="px-2 py-1 text-left bg-gray-100 border border-black">Advokasi dan Kolaborasi</th>
            </tr>
            <tr>
                <td class="px-2 py-1 border border-black">
                    {{ $formB['advokasiKolaborasi'] }}
                </td>
            </tr>
        </table>
    @endif

    {{-- ===== TERMINASI ===== --}}
    @if (!empty($formB['terminasi']))
        <table class="w-full mt-2 border border-collapse border-black">
            <tr>
                <th class="px-2 py-1 text-left bg-gray-100 border border-black">Ringkasan Terminasi</th>
            </tr>
            <tr>
                <td class="px-2 py-1 border border-black">
                    {{ $formB['terminasi'] }}
                </td>
            </tr>
        </table>
    @endif


    {{-- ===== TTD PETUGAS (GAMBAR) ===== --}}
    <table class="w-full mt-8">
        <tr>
            <td class="pr-8 text-right">

                <div class="inline-block text-center">

                    <div class="mb-1 text-[9px] text-gray-600">
                        Tanggal: {{ $formB['tanggal'] ?? '-' }}
                    </div>

                    @php
                        $petugasCode = $formB['tandaTanganPetugas']['petugasCode'] ?? '';
                        $user = $petugasCode ? App\Models\User::where('myuser_code', $petugasCode)->first() : null;
                        $ttdPetugas =
                            $user && $user->myuser_ttd_image ? asset('storage/' . $user->myuser_ttd_image) : null;
                    @endphp

                    {{-- Kotak TTD --}}
                    <div
                        class="flex items-center justify-center w-40 h-20 mx-auto overflow-hidden bg-white border border-black">
                        @if ($ttdPetugas)
                            <img src="{{ $ttdPetugas }}" alt="TTD Petugas"
                                class="block object-contain w-auto max-h-16">
                        @else
                            <span class="text-[10px] text-gray-600">TTD Petugas</span>
                        @endif
                    </div>

                    {{-- Nama Petugas --}}
                    <div class="w-40 pt-1 mx-auto mt-2 text-center border-t border-black text-[11px]">
                        ( {{ $formB['tandaTanganPetugas']['petugasName'] ?? '................................' }} )
                        <div class="text-[10px] text-gray-600">Petugas MPP</div>
                    </div>

                </div>

            </td>
        </tr>
    </table>

</body>

</html>
