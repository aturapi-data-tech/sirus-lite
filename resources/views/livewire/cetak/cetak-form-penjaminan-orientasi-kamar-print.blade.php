<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Form Pernyataan Kepemilikan Kartu Penjaminan Biaya</title>

    {{-- pakai bundle Tailwind sirus (samakan path dengan project-mu) --}}
    <link href="build/assets/sirus.css" rel="stylesheet" />

    <style>
        @page {
            size: A4;
            margin: 20px;
        }

        body {
            font-size: 11px;
        }

        .xs {
            font-size: 9px;
        }
    </style>
</head>

<body class="text-[11px] text-gray-900">

    @php
        // =============== IDENTITAS RS ==================
        $rs = $identitasRs ?? null;

        // =============== DATA PASIEN ===================
        $rawPasien = $dataPasien['pasien'] ?? ($dataPasien ?? []);
        $pasien = (array) $rawPasien;

        $rm = $pasien['regNo'] ?? '-';
        $namaPasien =
            trim(
                ($pasien['gelarDepan'] ?? '') .
                    ' ' .
                    ($pasien['regName'] ?? '') .
                    ' ' .
                    ($pasien['gelarBelakang'] ?? ''),
            ) ?:
            '-';
        $jkPasien = $pasien['jenisKelamin']['jenisKelaminDesc'] ?? '-';
        $thnUmur = $pasien['thn'] ?? '-';
        $tglLahir = $pasien['tglLahir'] ?? '-';

        $nik = $pasien['identitas']['nik'] ?? '-';
        $idBpjs = $pasien['identitas']['idbpjs'] ?? '-';
        $alamatPasien =
            ($pasien['identitas']['alamat'] ?? '-') .
                ', RT ' .
                ($pasien['identitas']['rt'] ?? '-') .
                '/RW ' .
                ($pasien['identitas']['rw'] ?? '-') .
                ' ' .
                $pasien['identitas']['desaName'] ??
            ('' . ' ' . $pasien['identitas']['kecamatanName'] ??
                ('' . ' ' . $pasien['identitas']['kotaName'] ??
                    ('' . ' ' . $pasien['identitas']['propinsiName'] ?? '')));

        // =============== DATA UGD / DAFTAR =============
        $dataDaftarRi = (array) ($dataUgd ?? []);

        // =============== DATA FORM =====================
        $f = $form ?? [];

        $tglForm = $f['tanggalFormPenjaminan'] ?? '-';
        $pembuat = $f['pembuatNama'] ?? '-';
        $umurPembuat = $f['pembuatUmur'] ?? '-';
        $jkPembuat = ($f['pembuatJenisKelamin'] ?? '') === 'L' ? 'Laki-laki' : 'Perempuan';
        $alamatPembuat = $f['pembuatAlamat'] ?? '-';
        $hubungan = $f['hubunganDenganPasien'] ?? '-';

        $jenisPenjamin = $f['jenisPenjamin'] ?? '';
        $asuransiLain = $f['asuransiLain'] ?? '-';

        $kelasKamar = $f['kelasKamar'] ?? '';
        $namaSaksi = $f['namaSaksiKeluarga'] ?? '-';
        $petugasNama = $f['namaPetugas'] ?? '-';
        $petugasDate = $f['petugasDate'] ?? '-';

        // ===== MASTER FASILITAS KAMAR (sama dengan di komponen) =====
        $kelasKamarOptions = [
            'VIP' => [
                'label' => 'VIP',
                'fasilitas' => [
                    '1 tempat tidur pasien',
                    'AC',
                    'Kamar mandi di dalam',
                    'Sofa bed penunggu',
                    'Kulkas',
                    'Televisi LED',
                    'Almari',
                    'Overbed table',
                    'Dispenser air minum',
                    'Makan siang 1 penunggu',
                ],
            ],
            'KELAS_I' => [
                'label' => 'Kelas I',
                'fasilitas' => [
                    '1 tempat tidur pasien',
                    'Kamar mandi di dalam',
                    'Sofa bed penunggu',
                    'Kulkas',
                    'Televisi LED',
                    'Almari',
                    'Kipas angin',
                    'Makan siang 1 penunggu',
                ],
            ],
            'KELAS_II' => [
                'label' => 'Kelas II',
                'fasilitas' => [
                    '2 tempat tidur pasien',
                    'Kamar mandi di dalam',
                    'Kursi penunggu',
                    'Televisi',
                    'Almari',
                    'Kipas angin',
                    'Makan siang 1 penunggu',
                ],
            ],
            'KELAS_III' => [
                'label' => 'Kelas III',
                'fasilitas' => [
                    '4 tempat tidur pasien',
                    'Kamar mandi di dalam',
                    'Televisi di luar ruangan',
                    'Kursi',
                    'Almari',
                    'Kipas angin',
                ],
            ],
        ];

        $kelasInfo = $kelasKamarOptions[$kelasKamar] ?? null;
        $kelasLabel = $kelasInfo['label'] ?? ($kelasKamar ?: '-');
        $fasilitas = $kelasInfo['fasilitas'] ?? [];

        // ===== TTD PEMBUAT =====
        $sigPembuatRaw = (string) ($f['signaturePembuat'] ?? '');
        if (\Illuminate\Support\Str::startsWith($sigPembuatRaw, '<svg')) {
            $signaturePembuat = 'data:image/svg+xml;base64,' . base64_encode($sigPembuatRaw);
        } else {
            $signaturePembuat = $sigPembuatRaw;
        }

        // ===== TTD SAKSI =====
        $sigSaksiRaw = (string) ($f['signatureSaksiKeluarga'] ?? '');
        if (\Illuminate\Support\Str::startsWith($sigSaksiRaw, '<svg')) {
            $signatureSaksi = 'data:image/svg+xml;base64,' . base64_encode($sigSaksiRaw);
        } else {
            $signatureSaksi = $sigSaksiRaw;
        }

        // ===== TTD PETUGAS (file image) =====
        $ttdPetugas = null;
        if (!empty($f['kodePetugas'] ?? null)) {
            $user = App\Models\User::where('myuser_code', $f['kodePetugas'])->first();
            if ($user && $user->myuser_ttd_image) {
                $ttdPetugas = public_path('storage/' . $user->myuser_ttd_image);
            }
        }
    @endphp


    {{-- ==========================================================
     HEADER – MODEL KOP SURAT SIRUS
=========================================================== --}}
    <table class="w-full p-1 border border-separate border-black rounded-md">
        <tr>
            {{-- kiri: logo & alamat RS --}}
            <td class="align-top w-[180px] border-0">
                <table class="w-full border-0 border-collapse">
                    <tr>
                        <td class="pb-1 text-center">
                            <img src="{{ public_path('madinahlogopersegi.png') }}" alt="Logo"
                                class="inline-block object-contain w-auto h-20">
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center leading-tight text-[10px]">
                            <div class="font-bold uppercase">
                                {{ $rs->int_name ?? 'RUMAH SAKIT' }}
                            </div>
                            <div class="mt-1">
                                {{ trim($rs->int_address ?? '-') }}
                                {{ strtoupper($rs->int_city ?? '') }}

                                @php
                                    $tel1 = $rs->int_phone1 ?? null;
                                    $tel2 = $rs->int_phone2 ?? null;
                                    $fax = $rs->int_fax ?? null;
                                @endphp
                                @if ($tel1)
                                    {{ $tel1 }}
                                @endif
                                @if ($tel2)
                                    {{ $tel2 }}
                                @endif
                                @if ($fax)
                                    Fax: {{ $fax }}
                                @endif
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- ==========================================================
     JUDUL FORM
=========================================================== --}}
    <div class="mt-3 mb-2 text-sm font-bold text-center underline">
        FORM DATA PENJAMINAN BIAYA & ORIENTASI KAMAR PASIEN
    </div>

    {{-- ==========================================================
     DATA PASIEN (RINGKAS) + PEMBUAT
=========================================================== --}}
    <div class="mb-1 font-bold">Data Pasien</div>
    <table class="mb-2 text-[11px]">
        <tr>
            <td class="w-40">Nama Pasien</td>
            <td>: {{ $namaPasien }}</td>
        </tr>
        <tr>
            <td>No. Rekam Medis</td>
            <td>: {{ $rm }}</td>
        </tr>
        <tr>
            <td>Jenis Kelamin</td>
            <td>: {{ $jkPasien }}</td>
        </tr>
        <tr>
            <td>Tanggal Lahir</td>
            <td>: {{ $tglLahir }}</td>
        </tr>
    </table>

    <div class="mb-1 font-bold">Saya yang bertanda tangan di bawah ini:</div>
    <table class="text-[11px]">
        <tr>
            <td class="w-40">Nama</td>
            <td>: {{ $pembuat }}</td>
        </tr>
        <tr>
            <td>Umur</td>
            <td>: {{ $umurPembuat }} tahun</td>
        </tr>
        <tr>
            <td>Jenis Kelamin</td>
            <td>: {{ $jkPembuat }}</td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td>: {{ $alamatPembuat }}</td>
        </tr>
    </table>

    <p class="mt-2 text-[11px]">
        Terhadap diri <span class="font-bold">{{ $hubungan }}</span> dari pasien.
    </p>

    {{-- ==========================================================
     PERNYATAAN PENJAMINAN
=========================================================== --}}
    <p class="mt-3 text-[11px]">
        Dengan ini menyatakan bahwa saya:
    </p>

    @php
        // Gabungkan dengan nama asuransi kalau tipenya Asuransi Lain
        $jenisPenjaminDisplay = $jenisPenjamin;

        if (!empty($jenisPenjamin) && stripos($jenisPenjamin, 'asuransi') !== false && !empty($asuransiLain)) {
            $jenisPenjaminDisplay .= ' - ' . $asuransiLain;
        }
    @endphp

    <p class="mt-1 text-[11px] text-justify">
        Memiliki kartu penjaminan berupa
        <span class="font-bold underline">
            {{ $jenisPenjaminDisplay ?: '......................................................' }}
        </span>
        untuk dipergunakan sebagai penjaminan biaya pelayanan medis di Rumah Sakit Islam Madinah.
    </p>

    <p class="mt-3 text-[11px] text-justify">
        Demikian pernyataan ini saya buat dengan sebenar-benarnya secara sadar tanpa paksaan, untuk diketahui
        dan digunakan sebagaimana mestinya.
    </p>


    {{-- ==========================================================
     ORIENTASI KAMAR PASIEN
=========================================================== --}}
    <div class="mt-4 mb-1 text-[11px] font-bold">
        ORIENTASI KAMAR PASIEN
    </div>

    <table class="text-[11px]">
        <tr>
            <td class="w-40">Kelas Kamar</td>
            <td>: {{ $kelasLabel }}</td>
        </tr>
        <tr>
            <td class="align-top">Fasilitas</td>
            <td>
                :
                @if (!empty($fasilitas))
                    <ul class="mt-0 mb-0 ml-3">
                        @foreach ($fasilitas as $fas)
                            <li>{{ $fas }}</li>
                        @endforeach
                    </ul>
                @else
                    ............................................................
                @endif
            </td>
        </tr>
    </table>

    <p class="mt-2 text-[11px]">
        Pasien / keluarga telah menerima penjelasan mengenai tarif dan fasilitas kamar sebagaimana tersebut di atas.
    </p>

    {{-- ==========================================================
     TANDA TANGAN
=========================================================== --}}
    <table class="w-full mt-8 text-[11px]" cellpadding="0" cellspacing="0">
        <tr style="height:220px;"> {{-- Tinggi dipaksa agar sejajar --}}

            {{-- PEMBUAT --}}
            <td width="33%" valign="bottom" align="center">

                Tulungagung, {{ $tglForm ?: '.....................' }}<br>
                Yang Membuat Pernyataan,<br><br>

                <table width="80%" cellspacing="0" cellpadding="0" style="margin:auto;">
                    <tr>
                        <td style="border:1px solid #000; height:70px; text-align:center;">
                            @if (!empty($signaturePembuat))
                                {{-- <img src="{{ $signaturePembuat }}" style="max-height:65px; margin-top:3px;"> --}}
                            @endif
                        </td>
                    </tr>
                </table>

                <br>
                ( {{ $pembuat }} )

            </td>



            {{-- SAKSI KELUARGA --}}
            <td width="33%" valign="bottom" align="center">

                Saksi : Keluarga<br><br>

                <table width="80%" cellspacing="0" cellpadding="0" style="margin:auto;">
                    <tr>
                        <td style="border:1px solid #000; height:70px; text-align:center;">
                            @if (!empty($signatureSaksi))
                                {{-- <img src="{{ $signatureSaksi }}" style="max-height:65px; margin-top:3px;"> --}}
                            @endif
                        </td>
                    </tr>
                </table>

                <br>
                ( {{ $namaSaksi }} )

            </td>



            {{-- PETUGAS RS --}}
            <td width="33%" valign="bottom" align="center">

                Petugas Rumah Sakit<br><br>

                <table width="80%" cellspacing="0" cellpadding="0" style="margin:auto;">
                    <tr>
                        <td style="border:1px solid #000; height:70px; text-align:center;">
                            @if ($ttdPetugas)
                                {{-- <img src="{{ $ttdPetugas }}" style="max-height:65px; margin-top:3px;"> --}}
                            @endif
                        </td>
                    </tr>
                </table>

                <br>
                ( {{ $petugasNama }} )<br>

            </td>

        </tr>
    </table>


</body>

</html>
