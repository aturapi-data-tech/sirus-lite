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

        $labels = [
            'diagnosis' => 'Diagnosis (WD & DD)',
            'dasar' => 'Dasar Diagnosis',
            'rencana' => 'Rencana Pengobatan / Tindakan',
            'indikasi' => 'Indikasi Pengobatan / Tindakan',
            'tujuan' => 'Tujuan',
            'risiko' => 'Risiko',
            'komplikasi' => 'Komplikasi',
            'prognosis' => 'Prognosis',
            'alternatif' => 'Alternatif & Risiko',
            'tanpaTindakan' => 'Kemungkinan Tanpa Pengobatan / Tindakan',
        ];

        $detail = (array) ($edukasi['detailInformasi'] ?? []);
        $sig = data_get($edukasi, 'penerimaInformasi.signature');
        $sigSvg = trim((string) $sig);
        if (\Illuminate\Support\Str::startsWith($sigSvg, '<svg')) {
            $sigSrc = 'data:image/svg+xml;base64,' . base64_encode($sigSvg);
        } else {
            $sigSrc = $sigSvg; // bisa jadi sudah base64 PNG/JPG
        }
    @endphp

    {{-- ===== HEADER (table only) ===== --}}
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

    {{-- ===== BLOK: Dokter / Pemberi / Penerima ===== --}}
    <table class="w-full mt-2 border border-collapse border-black">
        <tr>
            <th class="w-[260px] px-2 py-1 text-left border border-black bg-gray-100 text-[11px]">
                Dokter Pelaksana Tindakan
            </th>
            <td class="px-2 py-1 border border-black text-[11px]">
                {{ data_get($edukasi, 'dokterPelaksanaTindakan.drName', '-') }}
            </td>
        </tr>
        <tr>
            <th class="px-2 py-1 text-left border border-black bg-gray-100 text-[11px]">Pemberi Informasi</th>
            <td class="px-2 py-1 border border-black text-[11px]">
                {{ data_get($edukasi, 'pemberiInformasi.petugasName', '-') }}
                <span class="text-gray-600"> ({{ data_get($edukasi, 'pemberiInformasi.petugasCode', '-') }})</span>
            </td>
        </tr>
        <tr>
            <th class="px-2 py-1 text-left border border-black bg-gray-100 text-[11px]">Penerima Informasi</th>
            <td class="px-2 py-1 border border-black text-[11px]">
                {{ data_get($edukasi, 'penerimaInformasi.name', '-') }}
                <span class="text-gray-600"> • Hubungan:
                    {{ data_get($edukasi, 'penerimaInformasi.hubungan', '-') }}</span>
            </td>
        </tr>
    </table>

    {{-- ===== TABEL JENIS INFORMASI (tanpa kolom "Tandai") ===== --}}
    <table class="w-full mt-2 border border-collapse border-black">
        <tr class="bg-gray-100">
            <th class="w-8 px-2 py-1 text-center border border-black text-[11px]">No</th>
            <th class="w-[240px] px-2 py-1 text-left border border-black text-[11px]">JENIS INFORMASI</th>
            <th class="px-2 py-1 text-left border border-black text-[11px]">ISI INFORMASI</th>
        </tr>
        @php $i=1; @endphp
        @foreach ($labels as $key => $label)
            <tr>
                <td class="px-2 py-1 text-center align-top border border-black text-[11px]">{{ $i++ }}</td>
                <td class="px-2 py-1 align-top border border-black text-[11px]">{{ $label }}</td>
                <td class="px-2 py-1 align-top border border-black text-[11px]">
                    {{ trim((string) data_get($detail, "$key.desc", '')) }}
                </td>
            </tr>
        @endforeach
    </table>

    {{-- ===== PERNYATAAN + TTD (TTD pasien pasti tampil) ===== --}}
    <table class="w-full mt-2 border border-collapse border-black">
        <tr>
            <td class="px-2 py-2 align-top border border-black">
                <div class="font-bold text-[11px]">Pernyataan Pemberi Informasi</div>
                <div class="mt-1 text-[10px] text-justify">
                    Dengan ini menyatakan bahwa saya telah menerangkan hal-hal di atas secara benar dan jujur
                    dan memberikan kesempatan untuk bertanya dan/atau berdiskusi.
                </div>
            </td>

            {{-- kolom TTD petugas: lebar tetap pakai w-60 (~240px) --}}
            <td class="px-2 py-2 align-top border border-black w-60">
                <div class="text-right text-[9px] text-gray-600">Tanggal: {{ $edukasi['tglEdukasi'] ?? '-' }}</div>

                {{-- kotak TTD: tinggi 56px (h-14), konten fit --}}
                @php
                    $petugasCode = data_get($edukasi, 'pemberiInformasi.petugasCode');
                    $user = $petugasCode ? App\Models\User::where('myuser_code', $petugasCode)->first() : null;
                    $ttdPetugas = $user && $user->myuser_ttd_image ? asset('storage/' . $user->myuser_ttd_image) : null;
                @endphp
                <div class="flex items-center justify-center mt-1 overflow-hidden bg-white border border-black h-14">
                    @if ($ttdPetugas)
                        {{-- <img src="{{ $ttdPetugas }}" alt="TTD Petugas"
                            class="block object-contain w-auto mx-auto max-h-12"> --}}
                    @else
                        <span class="text-[9px] text-gray-600">TTD Petugas</span>
                    @endif
                </div>

                <div class="mt-1 text-center text-[10px]">
                    ( {{ data_get($edukasi, 'pemberiInformasi.petugasName', '................................') }} )
                    <div class="text-[9px] text-gray-600">Pemberi Informasi</div>
                </div>
            </td>
        </tr>

        <tr>
            <td class="px-2 py-2 align-top border border-black">
                <div class="font-bold text-[11px]">Pernyataan Penerima Informasi</div>
                <div class="mt-1 text-[10px] text-justify">
                    Dengan ini menyatakan bahwa saya telah menerima informasi sebagaimana di atas dan telah memahaminya.
                </div>
            </td>

            {{-- kolom TTD pasien: lebar sama (w-60) --}}
            <td class="px-2 py-2 align-top border border-black w-60">
                <div class="flex items-center justify-center w-full h-16 max-w-full bg-white border border-black">
                    {{-- <img src="{{ $sigSrc }}" alt="TTD"
                        class="block object-contain w-auto mx-auto max-h-16" /> --}}
                </div>

                <div class="mt-1 text-center text-[10px]">
                    ( {{ data_get($edukasi, 'penerimaInformasi.name', '................................') }} )
                    <div class="text-[9px] text-gray-600">Penerima Informasi</div>
                </div>
            </td>
        </tr>
    </table>



</body>

</html>
