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

        $form = (array) ($edukasi['form'] ?? []);
        $tglEdukasi = (string) ($form['tglEdukasi'] ?? '-');
        $pemberiInfo = (array) ($form['pemberiInformasi'] ?? []);
        $tujuan = (array) ($form['tujuan'] ?? []);
        $evaluasi = (array) ($form['evaluasiAwal'] ?? []);
        $kebutuhan = (array) ($form['kebutuhan'] ?? []);
        $metodeMedia = (array) ($form['metodeMedia'] ?? []);
        $hasil = (array) ($form['hasil'] ?? []);
        $tindakLanjut = (array) ($form['tindakLanjut'] ?? []);
        $ttd = (array) ($form['ttd'] ?? []);

        $opsiToString = function ($arr) {
            if (is_array($arr)) {
                return implode(', ', $arr);
            }
            return (string) $arr;
        };

        // signature pasien/keluarga (svg atau base64 image)
        $sigSvg = trim((string) ($ttd['pasienKeluargaTTD'] ?? ''));
        if (\Illuminate\Support\Str::startsWith($sigSvg, '<svg')) {
            $sigSrc = 'data:image/svg+xml;base64,' . base64_encode($sigSvg);
        } else {
            $sigSrc = $sigSvg; // bisa jadi sudah base64 PNG/JPG
        }
    @endphp

    {{-- ===== HEADER (identitas RS + pasien) ===== --}}
    <table class="w-full p-1 border border-separate border-black rounded-md">
        <tr>
            {{-- Kiri: logo & alamat RS --}}
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

            {{-- Kanan: data pasien --}}
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

    {{-- ===== JUDUL FORM ===== --}}
    <div class="mt-2 text-sm font-bold text-center underline">
        FORM EDUKASI PASIEN TERINTEGRASI
    </div>

    {{-- ===== INFO UMUM EDUKASI ===== --}}
    <table class="w-full mt-2 border border-collapse border-black text-[11px]">
        <tr>
            <th class="w-[160px] px-2 py-1 text-left border border-black bg-gray-100">Tanggal Edukasi</th>
            <td class="px-2 py-1 border border-black">{{ $tglEdukasi }}</td>
        </tr>
        <tr>
            <th class="px-2 py-1 text-left bg-gray-100 border border-black">Pemberi Informasi</th>
            <td class="px-2 py-1 border border-black">
                {{ $pemberiInfo['petugasName'] ?? '-' }}
                <span class="text-gray-600">
                    ({{ $pemberiInfo['petugasCode'] ?? '-' }})
                </span>
            </td>
        </tr>
        <tr>
            <th class="px-2 py-1 text-left bg-gray-100 border border-black">Tujuan Edukasi</th>
            <td class="px-2 py-1 border border-black">
                {{ $opsiToString($tujuan['opsi'] ?? []) }}
                @if (!empty($tujuan['lainnya']))
                    <span class="ml-1">- {{ $tujuan['lainnya'] }}</span>
                @endif
            </td>
        </tr>
        <tr>
            <th class="px-2 py-1 text-left bg-gray-100 border border-black">Kebutuhan Edukasi</th>
            <td class="px-2 py-1 border border-black">
                {{ $opsiToString($kebutuhan['opsi'] ?? []) }}
                @if (!empty($kebutuhan['lainnya']))
                    <span class="ml-1">- {{ $kebutuhan['lainnya'] }}</span>
                @endif
            </td>
        </tr>
        <tr>
            <th class="px-2 py-1 text-left bg-gray-100 border border-black">Metode / Media</th>
            <td class="px-2 py-1 border border-black">
                {{ $opsiToString($metodeMedia['opsi'] ?? []) }}
                @if (!empty($metodeMedia['lainnya']))
                    <span class="ml-1">- {{ $metodeMedia['lainnya'] }}</span>
                @endif
            </td>
        </tr>
    </table>

    {{-- ===== EVALUASI AWAL ===== --}}
    <div class="mt-2 font-semibold text-[11px]">
        A. EVALUASI AWAL PEMBELAJARAN
    </div>
    <table class="w-full mt-1 border border-collapse border-black text-[11px]">
        <tr>
            <th class="w-[220px] px-2 py-1 text-left border border-black bg-gray-100">Literasi (Kemampuan Membaca /
                Menulis)</th>
            <td class="px-2 py-1 border border-black">
                {{ $evaluasi['literasi'] ?? '-' }}
            </td>
        </tr>
        <tr>
            <th class="px-2 py-1 text-left bg-gray-100 border border-black">
                Bahasa / Pendidikan
            </th>
            <td class="px-2 py-1 border border-black">
                {{ $evaluasi['bahasaAtauPendidikan'] ?? '-' }}
            </td>
        </tr>
        @php
            $he = (array) ($evaluasi['hambatanEmosional'] ?? []);
            $hf = (array) ($evaluasi['keterbatasanFisikKognitif'] ?? []);
            $nb = (array) ($evaluasi['nilaiKeyakinanBudaya'] ?? []);
            $pi = (array) ($evaluasi['preferensiInformasi'] ?? []);
        @endphp
        <tr>
            <th class="px-2 py-1 text-left bg-gray-100 border border-black">
                Hambatan Emosional
            </th>
            <td class="px-2 py-1 border border-black">
                {{ !empty($he['ada']) ? 'Ada' : 'Tidak ada' }}
                @if (!empty($he['keterangan']))
                    - {{ $he['keterangan'] }}
                @endif
            </td>
        </tr>
        <tr>
            <th class="px-2 py-1 text-left bg-gray-100 border border-black">
                Keterbatasan Fisik / Kognitif
            </th>
            <td class="px-2 py-1 border border-black">
                {{ !empty($hf['ada']) ? 'Ada' : 'Tidak ada' }}
                @if (!empty($hf['keterangan']))
                    - {{ $hf['keterangan'] }}
                @endif
            </td>
        </tr>
        <tr>
            <th class="px-2 py-1 text-left bg-gray-100 border border-black">
                Nilai / Keyakinan / Budaya
            </th>
            <td class="px-2 py-1 border border-black">
                {{ !empty($nb['ada']) ? 'Ada' : 'Tidak ada' }}
                @if (!empty($nb['deskripsi']))
                    - {{ $nb['deskripsi'] }}
                @endif
            </td>
        </tr>
        <tr>
            <th class="px-2 py-1 text-left bg-gray-100 border border-black">
                Preferensi Informasi
            </th>
            <td class="px-2 py-1 border border-black">
                {{ $opsiToString($pi['opsi'] ?? []) }}
                @if (!empty($pi['lainnya']))
                    - {{ $pi['lainnya'] }}
                @endif
            </td>
        </tr>
    </table>

    {{-- ===== HASIL EDUKASI ===== --}}
    <div class="mt-2 font-semibold text-[11px]">
        B. HASIL EDUKASI
    </div>
    @php
        $row = function ($label, $data) {
            $ya = !empty($data['ya']);
            $ket = $data['keterangan'] ?? '';
            return [$label, $ya ? 'Ya' : 'Tidak', $ket];
        };

        $rowsHasil = [
            $row('Pasien/keluarga paham', (array) ($hasil['paham'] ?? [])),
            $row('Mampu mengulang informasi', (array) ($hasil['mampuMengulang'] ?? [])),
            $row('Mampu menunjukkan keterampilan/skill', (array) ($hasil['tunjukkanSkill'] ?? [])),
            $row('Sesuai dengan nilai/keyakinan pasien', (array) ($hasil['sesuaiNilai'] ?? [])),
            $row('Perlu edukasi ulang', (array) ($hasil['perluEdukasiUlang'] ?? [])),
        ];
    @endphp
    <table class="w-full mt-1 border border-collapse border-black text-[11px]">
        <tr class="bg-gray-100">
            <th class="w-[260px] px-2 py-1 text-left border border-black">Aspek</th>
            <th class="w-16 px-2 py-1 text-center border border-black">Ya / Tidak</th>
            <th class="px-2 py-1 text-left border border-black">Keterangan</th>
        </tr>
        @foreach ($rowsHasil as $r)
            <tr>
                <td class="px-2 py-1 border border-black">{{ $r[0] }}</td>
                <td class="px-2 py-1 text-center border border-black">{{ $r[1] }}</td>
                <td class="px-2 py-1 border border-black">{{ $r[2] }}</td>
            </tr>
        @endforeach
    </table>

    {{-- ===== TINDAK LANJUT ===== --}}
    <div class="mt-2 font-semibold text-[11px]">
        C. TINDAK LANJUT
    </div>
    <table class="w-full mt-1 border border-collapse border-black text-[11px]">
        <tr>
            <th class="w-[180px] px-2 py-1 text-left border border-black bg-gray-100">Edukasi lanjutan tanggal</th>
            <td class="px-2 py-1 border border-black">
                {{ $tindakLanjut['edukasiLanjutanTanggal'] ?? '-' }}
            </td>
        </tr>
        <tr>
            <th class="px-2 py-1 text-left bg-gray-100 border border-black">Dirujuk ke</th>
            <td class="px-2 py-1 border border-black">
                {{ $opsiToString($tindakLanjut['dirujukKe'] ?? []) }}
            </td>
        </tr>
        <tr>
            <th class="px-2 py-1 text-left bg-gray-100 border border-black">Keterangan tindak lanjut</th>
            <td class="px-2 py-1 border border-black">
                @if (!empty($tindakLanjut['tidakPerluTL']))
                    Tidak perlu tindak lanjut
                @else
                    -
                @endif
            </td>
        </tr>
    </table>

    {{-- ===== TTD PEMBERI & PENERIMA INFORMASI ===== --}}
    <table class="w-full mt-2 border border-collapse border-black">
        <tr>
            {{-- kolom pemberi informasi --}}
            <td class="w-1/2 px-2 py-2 align-top border border-black">
                <div class="font-bold text-[11px]">Pemberi Informasi</div>
                <div class="text-[9px] text-gray-600">
                    Tanggal: {{ $tglEdukasi }}
                </div>

                @php
                    $petugasCode = $pemberiInfo['petugasCode'] ?? null;
                    $user = $petugasCode ? App\Models\User::where('myuser_code', $petugasCode)->first() : null;
                    $ttdPetugas = $user && $user->myuser_ttd_image ? asset('storage/' . $user->myuser_ttd_image) : null;
                @endphp

                <div class="flex items-center justify-center h-16 mt-2 overflow-hidden bg-white border border-black">
                    @if ($ttdPetugas)
                        {{-- <img src="{{ $ttdPetugas }}" alt="TTD Petugas"
                            class="block object-contain w-auto mx-auto max-h-16">
                    @else --}}
                        <span class="text-[9px] text-gray-600">TTD Petugas</span>
                    @endif
                </div>

                <div class="mt-1 text-center text-[10px]">
                    ( {{ $pemberiInfo['petugasName'] ?? '................................' }} )
                    <div class="text-[9px] text-gray-600">
                        Pemberi Informasi
                    </div>
                </div>
            </td>

            {{-- kolom pasien/keluarga --}}
            <td class="w-1/2 px-2 py-2 align-top border border-black">
                <div class="font-bold text-[11px]">Pasien / Keluarga</div>
                <div class="text-[9px] text-gray-600">
                    Menyatakan telah menerima dan memahami informasi di atas.
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
                    ( {{ $ttd['pasienKeluargaNama'] ?? '................................' }} )
                    <div class="text-[9px] text-gray-600">
                        Pasien / Keluarga
                    </div>
                </div>
            </td>
        </tr>
    </table>

</body>

</html>
