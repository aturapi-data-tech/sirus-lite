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

        // --- Data kunjungan Rawat Inap ---
        $dataRi = (array) ($dataRi ?? ($dataDaftarRi ?? []));
        $klaim = (string) ($dataRi['klaim'] ?? 'BPJS');
        $tglMasuk = (string) ($dataRi['entryDate'] ?? '-');
        $noRawatInap = (string) ($dataRi['riHdrNo'] ?? $rm);

        // --- Data General Consent (sesuai struktur GeneralConsentPasienRI) ---
        $consent = (array) ($consent ?? ($dataRi['generalConsentPasienRI'] ?? []));

        $namaPenandatangan = (string) ($consent['wali'] ?? $nama);
        $hubunganPenandatangan = 'Pasien / Wali';
        $tglPersetujuan = (string) ($consent['signatureDate'] ?? ($tglMasuk ?? '-'));

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
                        <td class="py-1 pr-2 text-gray-700">Tanggal / Jam Masuk</td>
                        <td class="py-1">{{ $tglMasuk ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="py-1 pr-2 text-gray-700">No Rawat Inap</td>
                        <td class="py-1">{{ $noRawatInap ?: '-' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- =========================================
         JUDUL FORM
    ========================================== --}}
    <div class="mt-2 mb-1 text-sm font-bold text-center underline">
        FORMULIR PERSETUJUAN UMUM RAWAT INAP
    </div>

    {{-- =========================================
         ISI FORM
    ========================================== --}}

    {{-- HAK ANDA SEBAGAI PASIEN --}}
    <div class="mt-1 font-semibold text-[11px]">
        Hak Anda Sebagai Pasien
    </div>
    <ol class="mt-1 pl-4 text-[10px] leading-snug list-decimal text-justify">
        <li>Mendapat informasi tentang peraturan rumah sakit, hak, dan kewajiban Anda.</li>
        <li>Mendapat pelayanan yang manusiawi, adil, jujur, dan tanpa diskriminasi.</li>
        <li>Mendapat pelayanan kesehatan berkualitas sesuai standar medis.</li>
        <li>Memilih dokter dan kelas perawatan sesuai keinginan dan peraturan rumah sakit.</li>
        <li>Mendapat penjelasan tentang diagnosis, tindakan medis, risiko, dan biaya.</li>
        <li>Memberikan persetujuan atau menolak tindakan medis yang akan dilakukan.</li>
        <li>Didampingi keluarga dalam keadaan kritis.</li>
        <li>Menjalankan ibadah sesuai agama/kepercayaan, selama tidak mengganggu pasien lain.</li>
        <li>Mengajukan keluhan jika pelayanan tidak sesuai standar.</li>
    </ol>

    {{-- KEWAJIBAN ANDA SEBAGAI PASIEN --}}
    <div class="mt-2 font-semibold text-[11px]">
        Kewajiban Anda Sebagai Pasien
    </div>
    <ol class="mt-1 pl-4 text-[10px] leading-snug list-decimal text-justify">
        <li>Mematuhi peraturan rumah sakit.</li>
        <li>Menggunakan fasilitas rumah sakit dengan bertanggung jawab.</li>
        <li>Memberikan informasi yang jujur dan lengkap tentang kondisi kesehatan.</li>
        <li>Mematuhi rencana terapi yang disetujui setelah mendapat penjelasan.</li>
        <li>Membayar biaya pelayanan sesuai ketentuan rumah sakit.</li>
    </ol>

    {{-- PERNYATAAN DAN PERSETUJUAN --}}
    <div class="mt-2 font-semibold text-[11px]">
        Pernyataan dan Persetujuan
    </div>

    {{-- PEMAHAMAN --}}
    <div class="mt-1 font-semibold text-[10px]">
        Pemahaman
    </div>
    <div class="mt-1 text-[10px] text-justify leading-snug">
        Saya sudah paham hak dan kewajiban saya sebagai pasien. Saya juga mengerti bahwa setiap tindakan
        medis memiliki risiko dan manfaat.
    </div>

    {{-- PERSETUJUAN --}}
    <div class="mt-2 font-semibold text-[10px]">
        Persetujuan
    </div>
    <div class="mt-1 text-[10px] text-justify leading-snug">
        Saya setuju untuk menjalani pemeriksaan, pengobatan, dan tindakan medis yang diperlukan oleh tim medis.
    </div>

    {{-- PELEPASAN INFORMASI --}}
    <div class="mt-2 font-semibold text-[10px]">
        Pelepasan Informasi
    </div>
    <div class="mt-1 text-[10px] text-justify leading-snug">
        Saya mengizinkan rumah sakit untuk membagikan informasi medis saya kepada keluarga, dokter rujukan,
        atau pihak asuransi, jika diperlukan.
    </div>

    {{-- BARANG BAWAAN --}}
    <div class="mt-2 font-semibold text-[10px]">
        Barang Bawaan
    </div>
    <div class="mt-1 text-[10px] text-justify leading-snug">
        Saya mengerti bahwa rumah sakit tidak bertanggung jawab atas kehilangan atau kerusakan barang berharga
        yang saya bawa.
    </div>

    {{-- BIAYA --}}
    <div class="mt-2 font-semibold text-[10px]">
        Biaya
    </div>
    <div class="mt-1 text-[10px] text-justify leading-snug">
        Saya bertanggung jawab atas semua biaya perawatan sesuai ketentuan rumah sakit.
    </div>

    {{-- KERAHASIAAN --}}
    <div class="mt-2 font-semibold text-[10px]">
        Kerahasiaan
    </div>
    <div class="mt-1 text-[10px] text-justify leading-snug">
        Saya percaya bahwa rumah sakit akan menjaga kerahasiaan data medis saya.
    </div>

    {{-- =========================================
         OPSI PERSETUJUAN
    ========================================== --}}
    <div class="mt-3 text-[10px]">
        Status Persetujuan:
        @php
            $agreement = (string) ($consent['agreement'] ?? '1');
        @endphp

        @if ($agreement === '1')
            <span class="ml-2"> Setuju</span>
        @elseif ($agreement === '0')
            <span class="ml-4"> Tidak Setuju</span>
        @else
            <span class="ml-2"> Setuju</span>
            <span class="ml-4"> Tidak Setuju</span>
        @endif
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
                    Tanggal: {{ $petugasDate ?: '-' }}
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
