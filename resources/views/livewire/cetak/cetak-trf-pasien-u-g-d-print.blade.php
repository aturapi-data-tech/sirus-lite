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
        // ==== DATA PASIEN & TRF UGD ====
        $pasien = (array) ($dataPasien['pasien'] ?? []);
        $rm = (string) ($pasien['regNo'] ?? '');
        $nama = (string) ($pasien['regName'] ?? '');

        $trf = (array) ($dataDaftarUgd['trfUgd'] ?? []);

        $keluhanUtama = trim((string) ($trf['keluhanUtama'] ?? ''));
        $temuanSignifikan = trim((string) ($trf['temuanSignifikan'] ?? ''));
        $alergi = trim((string) ($trf['alergi'] ?? ''));
        $dxFree = trim((string) ($trf['diagnosisFreeText'] ?? ''));
        $terapiUgd = (array) ($trf['terapiUgd'] ?? []);

        $levelingDokter = (array) ($trf['levelingDokter'] ?? []);

        $pindahDariRuangan = (string) ($trf['pindahDariRuangan'] ?? '');
        $pindahKeRuangan = (string) ($trf['pindahKeRuangan'] ?? '');
        $tglPindah = (string) ($trf['tglPindah'] ?? '');

        $kondisiKlinis = $trf['kondisiKlinis'] ?? null;
        $kondisiDerajatMap = [
            0 => 'Derajat 0 - Stabil, tanpa keluhan berat.',
            1 => 'Derajat 1 - Keluhan ringan-sedang, perlu observasi.',
            2 => 'Derajat 2 - Kondisi sedang, risiko memburuk, perlu tindakan.',
            3 => 'Derajat 3 - Gawat Darurat, mengancam jiwa, perlu tindakan segera.',
        ];
        $kondisiDerajatText =
            $kondisiKlinis !== null ? $kondisiDerajatMap[(int) $kondisiKlinis] ?? 'Derajat ' . $kondisiKlinis : '-';

        $fasilitasPendukung = trim((string) ($trf['fasilitasPendukung'] ?? ''));
        $fasilitas = trim((string) ($trf['fasilitas'] ?? ''));
        $alasanPindah = trim((string) ($trf['alasanPindah'] ?? ''));
        $metodePemindahanPasien = trim((string) ($trf['metodePemindahanPasien'] ?? ''));

        $rencana = (array) ($trf['rencanaPerawatan'] ?? []);
        $alatList = (array) ($trf['alatYangTerpasang'] ?? []);

        $petugasPengirim = (string) ($trf['petugasPengirim'] ?? '');
        $petugasPengirimCode = (string) ($trf['petugasPengirimCode'] ?? '');
        $petugasPengirimDate = (string) ($trf['petugasPengirimDate'] ?? '');

        $petugasPenerima = (string) ($trf['petugasPenerima'] ?? '');
        $petugasPenerimaCode = (string) ($trf['petugasPenerimaCode'] ?? '');
        $petugasPenerimaDate = (string) ($trf['petugasPenerimaDate'] ?? '');

        // ==== KONDISI SAAT DIKIRIM & DITERIMA (TTV) ====
        $kondisiSaatDikirimArr = (array) ($trf['kondisiSaatDikirim'] ?? []);
        $kondisiSaatDiterimaArr = (array) ($trf['kondisiSaatDiterima'] ?? []);

        $formatTtvHtml = function (array $ttv): string {
            $lines = [];

            $sys = trim((string) ($ttv['sistolik'] ?? ''));
            $dia = trim((string) ($ttv['diastolik'] ?? ''));

            if ($sys !== '' || $dia !== '') {
                $tekanan = trim($sys . ($dia !== '' ? '/' . $dia : ''));
                $lines[] = "TD: {$tekanan} mmHg";
            }

            $nadi = trim((string) ($ttv['frekuensiNadi'] ?? ''));
            if ($nadi !== '') {
                $lines[] = "Nadi: {$nadi} x/menit";
            }

            $rr = trim((string) ($ttv['frekuensiNafas'] ?? ''));
            if ($rr !== '') {
                $lines[] = "Resp: {$rr} x/menit";
            }

            $suhu = trim((string) ($ttv['suhu'] ?? ''));
            if ($suhu !== '') {
                $lines[] = "Suhu: {$suhu} °C";
            }

            $spo2 = trim((string) ($ttv['spo2'] ?? ''));
            if ($spo2 !== '') {
                $lines[] = "SpO2: {$spo2} %";
            }

            $gda = trim((string) ($ttv['gda'] ?? ''));
            if ($gda !== '') {
                $lines[] = "GDA: {$gda} mg/dL";
            }

            $gcs = trim((string) ($ttv['gcs'] ?? ''));
            if ($gcs !== '') {
                $lines[] = "GCS: {$gcs}";
            }

            $keadaan = trim((string) ($ttv['keadaanPasien'] ?? ''));
            if ($keadaan !== '') {
                $lines[] = "Keadaan umum: {$keadaan}";
            }

            if (empty($lines)) {
                return '-';
            }

            return implode(' ', $lines);
        };

        $kondisiSaatDikirim = $formatTtvHtml($kondisiSaatDikirimArr);
        $kondisiSaatDiterima = $formatTtvHtml($kondisiSaatDiterimaArr);

    @endphp

    {{-- ===== HEADER: IDENTITAS RS & PASIEN ===== --}}
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
                            <span class="ml-1">{{ $dataDaftarUgd['klaimDesc'] ?? '-' }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-1 pr-2 text-gray-700">Tanggal Kunjungan UGD :</td>
                        <td class="py-1">{{ $dataDaftarUgd['rjDate'] ?? '-' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- TITLE --}}
    <div class="mt-2 mb-1 text-center">
        <div class="text-[14px] font-bold underline">
            FORM TRANSFER PASIEN UGD
        </div>
    </div>

    {{-- ===== RINGKASAN KLINIS UGD ===== --}}
    <table class="w-full mt-2 border border-collapse border-black text-[11px]">
        <tr class="bg-gray-100">
            <th class="w-1/2 px-2 py-1 text-left border border-black">Keluhan Utama & Alergi</th>
            <th class="w-1/2 px-2 py-1 text-left border border-black">Diagnosis & Terapi UGD</th>
        </tr>
        <tr>
            <td class="px-2 py-1 align-top border border-black">
                <div class="mb-1">
                    <div class="font-semibold">Keluhan Utama</div>
                    <div class="mt-1">
                        {{ $keluhanUtama ?: '-' }}
                    </div>

                    <div class="font-semibold">Temuan Signifkan</div>
                    <div class="mt-1">
                        {{ $temuanSignifikan ?: '-' }}
                    </div>
                </div>
                <div>
                    <div class="font-semibold">Alergi</div>
                    <div class="mt-1">
                        {{ $alergi ?: '-' }}
                    </div>
                </div>
            </td>
            <td class="px-2 py-1 align-top border border-black">
                <div class="mb-1">
                    <div class="font-semibold">Diagnosis</div>
                    <div class="mt-1">
                        {{ $dxFree ?: '-' }}
                    </div>
                </div>
                <div>
                    <div class="font-semibold">Terapi UGD</div>
                    @if (!empty($terapiUgd))
                        <ul class="pl-4 mt-1 list-disc">
                            @foreach ($terapiUgd as $row)
                                @if (trim($row) !== '')
                                    <li>{{ $row }}</li>
                                @endif
                            @endforeach
                        </ul>
                    @else
                        <div class="mt-1 italic text-gray-600">Belum ada terapi terekam.</div>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    {{-- ===== LEVELING DOKTER ===== --}}
    <table class="w-full mt-2 border border-collapse border-black text-[11px]">

        <tr class="bg-gray-100">
            <th class="w-1/3 px-2 py-1 text-left border border-black">DPJP</th>
            <th class="w-1/6 px-2 py-1 text-center border border-black">Level</th>
            <th class="w-1/6 px-2 py-1 text-center border border-black">Tgl Entry</th>
        </tr>
        @if (!empty($levelingDokter))
            @foreach ($levelingDokter as $lvl)
                @if (!empty($lvl['drName']))
                    <tr>
                        <td class="px-2 py-1 border border-black">
                            {{ $lvl['drName'] ?? '-' }}
                        </td>
                        <td class="px-2 py-1 text-center border border-black">
                            {{ $lvl['levelDokter'] ?? '-' }}
                        </td>
                        <td class="px-2 py-1 text-center border border-black">
                            {{ $lvl['tglEntry'] ?? '-' }}
                        </td>
                    </tr>
                @endif
            @endforeach
        @else
            <tr>
                <td colspan="4" class="px-2 py-1 text-center border border-black">
                    Tidak ada data leveling dokter.
                </td>
            </tr>
        @endif
    </table>

    {{-- ===== DATA PEMINDAHAN & KONDISI ===== --}}
    <table class="w-full mt-2 border border-collapse border-black text-[11px]">
        <tr class="bg-gray-100">
            <th class="w-1/2 px-2 py-1 text-left border border-black">Data Pemindahan Pasien</th>
            <th class="w-1/2 px-2 py-1 text-left border border-black">Kondisi & Fasilitas</th>
        </tr>
        <tr>
            {{-- Data Pemindahan --}}
            <td class="px-2 py-1 align-top border border-black">
                <table class="w-full text-[11px]">
                    <tr>
                        <td class="w-[120px] align-top py-1 pr-1">Pindah dari Ruangan</td>
                        <td class="py-1">: {{ $pindahDariRuangan ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="py-1 pr-1 align-top">Pindah ke Ruangan</td>
                        <td class="py-1">: {{ $pindahKeRuangan ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="py-1 pr-1 align-top">Tanggal / Jam Pindah</td>
                        <td class="py-1">: {{ $tglPindah ?: '-' }}</td>
                    </tr>
                </table>
            </td>

            {{-- Kondisi & Fasilitas (versi hemat space) --}}
            <td class="px-2 py-1 align-top border border-black">
                <table class="w-full text-[11px]">
                    <tr>
                        <td class="w-[140px] align-top py-1 pr-1">Kondisi Klinis (0–3)</td>
                        <td class="py-1">: {{ $kondisiDerajatText }}</td>
                    </tr>
                    <tr>
                        <td class="py-1 pr-1 align-top">Fasilitas Pendukung</td>
                        <td class="py-1">: {{ $fasilitasPendukung ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="py-1 pr-1 align-top">Fasilitas yang Dibutuhkan</td>
                        <td class="py-1">: {{ $fasilitas ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="py-1 pr-1 align-top">Alasan Pindah</td>
                        <td class="py-1">: {{ $alasanPindah ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="py-1 pr-1 align-top">Metode Pemindahan</td>
                        <td class="py-1">: {{ $metodePemindahanPasien ?: '-' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- ===== RENCANA PERAWATAN ===== --}}
    <table class="w-full mt-2 border border-collapse border-black text-[11px]">
        <tr class="bg-gray-100">
            <th class="px-2 py-1 text-left border border-black">Rencana Perawatan</th>
        </tr>

        <tr>
            <td class="px-2 py-1 align-top border border-black">
                <table class="w-full text-[11px]">
                    <tr>
                        <td class="w-[160px] align-top py-1 pr-2">Observasi</td>
                        <td class="py-1">: {{ trim((string) ($rencana['observasi'] ?? '')) ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="py-1 pr-2 align-top">Pembatasan Cairan</td>
                        <td class="py-1">: {{ trim((string) ($rencana['pembatasanCairan'] ?? '')) ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="py-1 pr-2 align-top">Balance Cairan</td>
                        <td class="py-1">: {{ trim((string) ($rencana['balanceCairan'] ?? '')) ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="py-1 pr-2 align-top">Diet</td>
                        <td class="py-1">: {{ trim((string) ($rencana['diet'] ?? '')) ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="py-1 pr-2 align-top">Lain-lain</td>
                        <td class="py-1">: {{ trim((string) ($rencana['lainLain'] ?? '')) ?: '-' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- ===== KONDISI PASIEN SAAT DIKIRIM & DITERIMA (DARI TTV) ===== --}}
    <table class="w-full mt-2 border border-collapse border-black text-[11px]">
        <tr class="bg-gray-100">
            <th class="w-1/2 px-2 py-1 text-left border border-black">
                Kondisi Pasien Saat Dikirim
            </th>
            <th class="w-1/2 px-2 py-1 text-left border border-black">
                Kondisi Pasien Saat Diterima
            </th>
        </tr>
        <tr>
            <td class="px-2 py-1 align-top border border-black">
                {{ $kondisiSaatDikirim }}
            </td>
            <td class="px-2 py-1 align-top border border-black">
                {{ $kondisiSaatDiterima }}
            </td>
        </tr>
    </table>

    {{-- ===== ALAT YANG TERPASANG ===== --}}
    <table class="w-full mt-2 border border-collapse border-black text-[11px]">
        <tr class="bg-gray-100">
            <th colspan="4" class="px-2 py-1 text-left border border-black">
                Alat yang Terpasang
            </th>
        </tr>
        <tr class="bg-gray-100">
            <th class="w-1/4 px-2 py-1 text-left border border-black">Jenis Alat</th>
            <th class="w-1/4 px-2 py-1 text-left border border-black">Lokasi</th>
            <th class="w-1/6 px-2 py-1 text-left border border-black">Ukuran</th>
            <th class="w-1/3 px-2 py-1 text-left border border-black">Keterangan</th>
        </tr>
        @if (!empty($alatList))
            @foreach ($alatList as $item)
                <tr>
                    <td class="px-2 py-1 border border-black">
                        {{ $item['jenis'] ?? '-' }}
                    </td>
                    <td class="px-2 py-1 border border-black">
                        {{ $item['lokasi'] ?? '-' }}
                    </td>
                    <td class="px-2 py-1 border border-black">
                        {{ $item['ukuran'] ?? '-' }}
                    </td>
                    <td class="px-2 py-1 border border-black">
                        {{ $item['keterangan'] ?? '-' }}
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="4" class="px-2 py-1 text-center border border-black">
                    Belum ada alat terpasang yang dicatat.
                </td>
            </tr>
        @endif
    </table>

    {{-- ===== PETUGAS PENGIRIM & PENERIMA ===== --}}
    <table class="w-full mt-3 border border-collapse border-black text-[11px]">
        <tr class="bg-gray-100">
            <th class="w-1/2 px-2 py-1 text-center border border-black">
                Petugas Pengirim
            </th>
            <th class="w-1/2 px-2 py-1 text-center border border-black">
                Petugas Penerima
            </th>
        </tr>
        <tr>
            <td class="px-2 py-3 align-top border border-black">
                <div class="mb-2">
                    <div>Nama : <strong>{{ $petugasPengirim ?: '................................' }}</strong></div>
                    @if ($petugasPengirimDate)
                        <div>Tanggal : {{ $petugasPengirimDate }}</div>
                    @endif
                </div>
                <div class="mt-6 text-center">
                    <div class="mb-6">___________________________</div>
                    <div class="text-[10px]">Tanda Tangan</div>
                </div>
            </td>
            <td class="px-2 py-3 align-top border border-black">
                <div class="mb-2">
                    <div>Nama : <strong>{{ $petugasPenerima ?: '................................' }}</strong></div>
                    @if ($petugasPenerimaDate)
                        <div>Tanggal : {{ $petugasPenerimaDate }}</div>
                    @endif
                </div>
                <div class="mt-6 text-center">
                    <div class="mb-6">___________________________</div>
                    <div class="text-[10px]">Tanda Tangan</div>
                </div>
            </td>
        </tr>
    </table>

</body>

</html>
