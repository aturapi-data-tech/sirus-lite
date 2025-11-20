<!DOCTYPE html>
<html lang="id">

<head>
    <style>
        @page {
            size: A4;
            margin: 15px;
        }
    </style>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- Tailwind bundle kamu --}}
    <link href="build/assets/sirus.css" rel="stylesheet">
</head>

<body class="text-xs">

    @php
        // --- Data RS ---
        $rs = $identitasRs ?? null;

        // --- Data pasien (support 2 bentuk: ['pasien'=>[]] atau langsung []) ---
        $rawPasien = $dataPasien['pasien'] ?? ($dataPasien ?? []);
        $pasien = (array) $rawPasien;

        $rm = (string) ($pasien['regNo'] ?? '');
        $nama = (string) ($pasien['regName'] ?? '');
        $nik = (string) data_get($pasien, 'identitas.nik', '');
        $alamat = (string) data_get($pasien, 'identitas.alamat', '');
        $bpjs = (string) data_get($pasien, 'identitas.idbpjs', '');

        // --- Data kunjungan UGD ---
        $dataUgd = (array) ($dataUgd ?? ($dataDaftarUgd ?? []));
        $klaim = (string) ($dataUgd['klaim'] ?? 'BPJS');
        $tglKunjungan = (string) ($dataUgd['entryDate'] ?? '-');
        $noKunjungan = (string) ($dataUgd['regNo'] ?? $rm);

        // --- Data General Consent (sesuai struktur GeneralConsentPasienUGD) ---
        $consent = (array) ($consent ?? ($dataUgd['generalConsentPasienUGD'] ?? []));

        $namaPenandatangan = (string) ($consent['wali'] ?? $nama);
        $hubunganPenandatangan = 'Pasien / Wali';
        $tglPersetujuan = (string) ($consent['signatureDate'] ?? ($tglKunjungan ?? '-'));

        // tanda tangan pasien/wali (SVG string dari canvas)
        $sigRaw = trim((string) ($consent['signature'] ?? ''));
        if (\Illuminate\Support\Str::startsWith($sigRaw, '<svg')) {
            $sigSrc = 'data:image/svg+xml;base64,' . base64_encode($sigRaw);
        } else {
            $sigSrc = $sigRaw; // bisa sudah base64 png/jpg atau kosong
        }

        // data petugas pemeriksa
        $petugasName = (string) ($consent['petugasPemeriksa'] ?? '');
        $petugasCode = (string) ($consent['petugasPemeriksaCode'] ?? '');
        $petugasDate = (string) ($consent['petugasPemeriksaDate'] ?? '');

        $ttdPetugas = null;
        if (!empty($petugasCode)) {
            $user = App\Models\User::where('myuser_code', $petugasCode)->first();
            if ($user && $user->myuser_ttd_image) {
                $ttdPetugas = asset('storage/' . $user->myuser_ttd_image);
            }
        }
    @endphp

    {{-- =========================================
         HEADER: IDENTITAS RS + PASIEN
    ========================================== --}}
    <table class="w-full p-1 border border-separate border-black rounded-md">
        <tr>
            {{-- Kiri: logo & identitas RS --}}
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
                                {{ $rs->int_name ?? 'RUMAH SAKIT' }}
                            </div>
                            <div class="mt-1">
                                {{ trim($rs->int_address ?? '-') }}<br>
                                {{ strtoupper($rs->int_city ?? '') }}
                            </div>
                            @php
                                $tel1 = $rs->int_phone1 ?? null;
                                $tel2 = $rs->int_phone2 ?? null;
                                $fax = $rs->int_fax ?? null;
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

            {{-- Kanan: identitas pasien & kunjungan --}}
            <td class="align-top border-0">
                <table class="w-full border-0 border-collapse text-[11px]">
                    <tr class="border-b">
                        <td class="w-[140px] py-1 pr-2 text-gray-700">Nama Pasien</td>
                        <td class="py-1">
                            <span class="font-bold">{{ strtoupper($nama ?: '-') }}</span>
                            <span class="font-normal">
                                / {{ $pasien['jenisKelamin']['jenisKelaminDesc'] ?? '-' }} /
                                {{ $pasien['thn'] ?? '-' }}
                            </span>
                        </td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-1 pr-2 text-gray-700">No Rekam Medis</td>
                        <td class="py-1 font-extrabold text-[13px]">{{ $rm ?: '-' }}</td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-1 pr-2 text-gray-700">NIK</td>
                        <td class="py-1">{{ $nik ?: '-' }}</td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-1 pr-2 text-gray-700">Alamat</td>
                        <td class="py-1">{{ $alamat ?: '-' }}</td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-1 pr-2 text-gray-700">ID BPJS</td>
                        <td class="py-1">
                            {{ $bpjs ?: '-' }}
                            <span class="ml-6 text-gray-700">Klaim :</span>
                            <span class="ml-1">{{ $klaim }}</span>
                        </td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-1 pr-2 text-gray-700">Tanggal / Jam Kunjungan</td>
                        <td class="py-1">{{ $tglKunjungan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="py-1 pr-2 text-gray-700">No Kunjungan UGD</td>
                        <td class="py-1">{{ $noKunjungan ?: '-' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- =========================================
         JUDUL FORM
    ========================================== --}}
    <div class="mt-2 mb-1 text-sm font-bold text-center underline">
        FORMULIR PERSETUJUAN UMUM UGD
    </div>

    {{-- =========================================
         ISI FORM
    ========================================== --}}

    {{-- PERNYATAAN PERSETUJUAN --}}
    <div class="mt-1 font-semibold text-[11px]">
        Pernyataan Persetujuan
    </div>
    <div class="mt-1 text-[10px] text-justify leading-snug">
        Dengan ini, saya memberikan persetujuan untuk menerima perawatan kesehatan di Unit Gawat Darurat (UGD)
        sesuai dengan kondisi saya. Saya telah menerima penjelasan yang jelas mengenai hak dan kewajiban saya
        sebagai pasien.
    </div>

    {{-- HAK SEBAGAI PASIEN --}}
    <div class="mt-2 font-semibold text-[11px]">
        Hak Sebagai Pasien
    </div>
    <ol class="mt-1 pl-4 text-[10px] leading-snug list-decimal text-justify">
        <li>Mendapatkan informasi mengenai tata tertib UGD dan hak pasien.</li>
        <li>Mendapatkan pelayanan kesehatan yang manusiawi, adil, dan sesuai standar profesi medis.</li>
        <li>Mendapatkan penjelasan tentang kondisi medis, tindakan yang akan dilakukan, dan risikonya,
            kecuali dalam keadaan darurat yang mengancam nyawa.</li>
        <li>Mendapatkan privasi dan kerahasiaan informasi medis.</li>
        <li>Memberikan persetujuan atau menolak tindakan medis setelah mendapatkan penjelasan, kecuali
            dalam situasi darurat.</li>
        <li>Didampingi oleh keluarga jika memungkinkan sesuai kondisi medis.</li>
        <li>Memperoleh keamanan dan keselamatan selama berada di UGD.</li>
    </ol>

    {{-- KEWAJIBAN SEBAGAI PASIEN --}}
    <div class="mt-2 font-semibold text-[11px]">
        Kewajiban Sebagai Pasien
    </div>
    <ol class="mt-1 pl-4 text-[10px] leading-snug list-decimal text-justify">
        <li>Mematuhi peraturan UGD dan menghormati hak pasien lain serta tenaga kesehatan.</li>
        <li>Memberikan informasi yang akurat dan jujur tentang kondisi kesehatan dan riwayat medis.</li>
        <li>Memberikan informasi terkait jaminan kesehatan atau kemampuan finansial untuk perawatan.</li>
        <li>Mematuhi anjuran tenaga medis setelah penjelasan diberikan.</li>
        <li>Membayar biaya perawatan sesuai dengan ketentuan rumah sakit.</li>
    </ol>

    {{-- PEMAHAMAN --}}
    <div class="mt-2 font-semibold text-[11px]">
        Pemahaman
    </div>
    <div class="mt-1 text-[10px] text-justify leading-snug">
        Saya telah menerima penjelasan singkat mengenai hak dan kewajiban saya sebagai pasien UGD, serta
        risiko tindakan medis yang mungkin diperlukan.
    </div>

    {{-- PERSETUJUAN --}}
    <div class="mt-2 font-semibold text-[11px]">
        Persetujuan
    </div>
    <div class="mt-1 text-[10px] text-justify leading-snug">
        Saya menyetujui pemeriksaan, pengobatan, atau tindakan medis yang dianggap perlu oleh tim medis UGD
        dalam situasi darurat atau untuk menyelamatkan nyawa saya.
    </div>

    {{-- PELEPASAN INFORMASI --}}
    <div class="mt-2 font-semibold text-[11px]">
        Pelepasan Informasi
    </div>
    <div class="mt-1 text-[10px] text-justify leading-snug">
        Saya memberikan izin kepada rumah sakit untuk berbagi informasi medis saya kepada pihak-pihak terkait,
        seperti keluarga, dokter rujukan, atau penyedia asuransi, untuk kepentingan penanganan medis.
    </div>

    {{-- BARANG BENDA --}}
    <div class="mt-2 font-semibold text-[11px]">
        Barang Benda
    </div>
    <div class="mt-1 text-[10px] text-justify leading-snug">
        Saya memahami bahwa rumah sakit tidak bertanggung jawab atas kehilangan atau kerusakan barang berharga
        yang saya bawa ke UGD.
    </div>

    {{-- BIAYA --}}
    <div class="mt-2 font-semibold text-[11px]">
        Biaya
    </div>
    <div class="mt-1 text-[10px] text-justify leading-snug">
        Saya memahami bahwa saya bertanggung jawab atas biaya yang timbul selama perawatan di UGD, sesuai dengan
        ketentuan yang berlaku.
    </div>

    {{-- =========================================
         TANDA TANGAN PASIEN/WALI & PETUGAS
    ========================================== --}}
    <table class="w-full mt-4">
        <tr>
            {{-- Kolom pasien / wali --}}
            <td class="w-1/2 pr-4 align-top">
                <div class="text-[10px]">
                    Tanggal: {{ $tglPersetujuan ?: '-' }}
                </div>
                <div class="mt-1 text-[10px]">
                    Pasien / Keluarga,
                </div>

                <div class="flex items-center justify-center h-16 mt-2 bg-white border border-black">
                    @if (!empty($sigSrc))
                        {{-- <img src="{{ $sigSrc }}" alt="TTD Pasien/Keluarga"
                            class="block object-contain w-auto mx-auto max-h-16" /> --}}
                    @else
                        <span class="text-[9px] text-gray-600">TTD Pasien / Keluarga</span>
                    @endif
                </div>

                <div class="mt-1 text-center text-[10px]">
                    ( {{ $namaPenandatangan ?: '................................' }} )
                    <div class="text-[9px] text-gray-600">
                        {{ $hubunganPenandatangan ?: 'Pasien / Wali' }}
                    </div>
                </div>
            </td>

            {{-- Kolom petugas pemeriksa --}}
            <td class="w-1/2 pl-4 align-top">
                <div class="text-[10px]">
                    - {{-- Tanggal: {{ $petugasDate ?: '-' }} --}}
                </div>
                <div class="mt-1 text-[10px]">
                    Petugas Pemeriksa,
                </div>

                <div class="flex items-center justify-center h-16 mt-2 bg-white border border-black">
                    @if ($ttdPetugas)
                        {{-- <img src="{{ $ttdPetugas }}" alt="TTD Petugas"
                            class="block object-contain w-auto mx-auto max-h-16" /> --}}
                    @else
                        <span class="text-[9px] text-gray-600">TTD Petugas Pemeriksa</span>
                    @endif
                </div>

                <div class="mt-1 text-center text-[10px]">
                    ( {{ $petugasName ?: '................................' }} )
                    <div class="text-[9px] text-gray-600">
                        {{-- Kode: {{ $petugasCode ?: '-' }} --}}
                    </div>
                </div>
            </td>
        </tr>
    </table>

</body>

</html>
