<!DOCTYPE html>
<html>

<head>
    <style>
        @page {
            size: 18cm 21cm;
            margin: 8px;
        }
    </style>


    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">



    <link href="build/assets/sirus.css" rel="stylesheet">
</head>

<body>

    <div style="font-size: 9px;">
        @php
            use Carbon\Carbon;
        @endphp

        @php
            // ====== Helper kecil di Blade (aman & ringkas) ======
            $pasien = $dataPasien['pasien'] ?? [];
            $ri = $dataDaftarRi ?? [];

            $rm = $pasien['regNo'] ?? '';

            // Identitas
            $nama = $pasien['regName'] ?? '';
            $tglLahir = $pasien['tglLahir'] ?? '';
            $ruang = $ri['bangsalDesc'] ?? '';
            $kamar = trim(($ri['roomDesc'] ?? '') . (isset($ri['bedNo']) ? ' / ' . $ri['bedNo'] : ''));
            $tglMasuk = $ri['entryDate'] ?? '';
            $tglKeluar = $ri['exitDate'] ?? '';

            $dokterUtama = collect($ri['pengkajianAwalPasienRawatInap']['levelingDokter'] ?? [])
                ->where('levelDokter', 'Utama')
                ->first();

            $dpjp = $dokterUtama['drName'] ?? 'DPJP';

            // Ringkasan masuk
            $diagnosaMasuk = data_get($ri, 'pengkajianAwalPasienRawatInap.bagian1DataUmum.diagnosaMasuk', '');
            $indikasiRawatInap = data_get(
                $ri,
                'pengkajianAwalPasienRawatInap.bagian5CatatanDanTandaTangan.catatanUmum',
                '',
            );

            // Anamnesis
            $keluhanUtama = data_get($ri, 'pengkajianDokter.anamnesa.keluhanUtama', '');

            $riwayatPenyakit =
                'Riwayat Penyakit Sekarang: ' .
                data_get($ri, 'pengkajianDokter.anamnesa.riwayatPenyakit.sekarang', '-') .
                "\n" .
                'Riwayat Penyakit Dahulu: ' .
                data_get($ri, 'pengkajianDokter.anamnesa.riwayatPenyakit.dahulu', '-') .
                "\n" .
                'Riwayat Penyakit Keluarga: ' .
                data_get($ri, 'pengkajianDokter.anamnesa.riwayatPenyakit.keluarga', '-') .
                "\n";

            // Pemeriksaan fisik awal
            $tv = data_get($ri, 'pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital', []);
            $td = trim(($tv['sistolik'] ?? '') . '/' . ($tv['distolik'] ?? ''));
            $suhu = $tv['suhu'] ?? '';
            $nadi = $tv['frekuensiNadi'] ?? '';
            $rr = $tv['frekuensiNafas'] ?? '';
            $gcsAwal = data_get($ri, 'pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.neurologi.gcs', ''); // fallback
            $pemeriksaanFisik = data_get($ri, 'pengkajianDokter.fisik', '');

            // Penunjang
            // ambil GDA awal & GDA terakhir observasi
            $gdaAwal = $tv['gda'] ?? '';
            $tandaObs = data_get($ri, 'observasi.observasiLanjutan.tandaVital', []);
            $gdaAkhir = collect($tandaObs)
                ->pluck('gda')
                ->filter(fn($v) => $v !== '' && $v !== '-' && $v !== '0')
                ->last();
            $labText = trim(
                'GDA awal: ' .
                    ($gdaAwal ?: '-') .
                    '; GDA terakhir: ' .
                    ($gdaAkhir ?: '-') .
                    ' ' .
                    data_get($ri, 'pengkajianDokter.hasilPemeriksaanPenunjang.laboratorium', ''),
            );

            // ==== RADIOLOGI ====
            $radText = trim(
                'Hasil radiologi: ' . data_get($ri, 'pengkajianDokter.hasilPemeriksaanPenunjang.radiologi', '-'),
            );

            // ==== PENUNJANG LAIN ====
            $lainText = trim(
                'Pemeriksaan penunjang lain: ' .
                    data_get($ri, 'pengkajianDokter.hasilPemeriksaanPenunjang.penunjangLain', '-'),
            );

            // Terapi/Tindakan selama di RS (dari pemberian obat & cairan)
            $cppt = data_get($ri, 'cppt', []);

            // 1) Ambil CPPT dokter: profession = "Dokter" ATAU nama petugas diawali "dr."
            $cpptDokter = collect($cppt)->filter(function ($row) {
                $isDokterByProfession = strcasecmp((string) ($row['profession'] ?? ''), 'Dokter') === 0;
                $nama = (string) ($row['petugasCPPT'] ?? '');
                $isDokterByName = preg_match('/^\s*dr\.?\s+/i', $nama); // "dr ", "dr. "
                return $isDokterByProfession || $isDokterByName;
            });

            // 2) Gabungkan semua field plan menjadi satu teks besar
            $plansGabung = $cpptDokter
                ->map(fn($row) => (string) data_get($row, 'soap.plan', ''))
                ->filter()
                ->implode(" \n");

            $terapiRS = $plansGabung;

            // ====== DIAGNOSIS (ICD + Free Text) ======
            $dxList = collect(data_get($ri, 'diagnosis', []));
            $dxFree = trim((string) data_get($ri, 'diagnosisFreeText', ''));

            // tentukan diagnosis utama (prioritas kategori Utama/Primer)
            $dxUtamaRow =
                $dxList->first(function ($d) {
                    $k = strtolower($d['kategoriDiagnosa'] ?? '');
                    return in_array($k, ['utama', 'primer', 'primary', 'utama/primer']);
                }) ?? $dxList->first();

            $dxUtama = $dxUtamaRow['diagDesc'] ?? '';
            $dxUtamaICD = $dxUtamaRow['icdX'] ?? '';

            // diagnosis sekunder (ICD) = selain yang utama
            $dxSekunderRows = $dxList
                ->reject(function ($d) use ($dxUtamaRow) {
                    return $dxUtamaRow && ($d['diagId'] ?? null) === ($dxUtamaRow['diagId'] ?? null);
                })
                ->values();

            $dxSekunder = $dxSekunderRows->pluck('diagDesc')->filter()->values()->all();
            $dxSekunderICD = $dxSekunderRows->pluck('icdX')->filter()->values()->all();

            // gabungkan free text → item sekunder tanpa kode
            if ($dxFree !== '') {
                $freeDxItems = collect(preg_split('/\r\n|\r|\n|;|\|/', $dxFree))->map('trim')->filter()->values();
                // jika utama kosong, ambil baris pertama free text sebagai utama
                if ($dxUtama === '' && $freeDxItems->isNotEmpty()) {
                    $dxUtama = $freeDxItems->shift();
                }
                // sisa free text masuk sekunder (tanpa kode)
                foreach ($freeDxItems as $item) {
                    $dxSekunder[] = $item;
                    $dxSekunderICD[] = '';
                }
            }

            // ====== TINDAKAN/PROSEDUR (ICD-9-CM + Free Text) ======
            $procList = collect(data_get($ri, 'procedure', []))
                ->map(function ($p) {
                    return [
                        'desc' => trim((string) ($p['procedureDesc'] ?? '')),
                        'code' => trim((string) ($p['procedureId'] ?? '')),
                    ];
                })
                ->filter(fn($x) => $x['desc'] !== '');

            $procFree = trim((string) data_get($ri, 'procedureFreeText', ''));
            if ($procFree !== '') {
                $freeProcItems = collect(preg_split('/\r\n|\r|\n|;|\|/', $procFree))->map('trim')->filter();
                foreach ($freeProcItems as $fp) {
                    $procList->push(['desc' => $fp, 'code' => '']);
                }
            }

            $tindakanDesc = $procList->pluck('desc')->values();
            $tindakanCode = $procList->pluck('code')->values();

            //DIET

            $diet = trim((string) data_get($ri, 'pengkajianDokter.rencana.diet', '-'));

            // Edukasi / Instruksi: ambil PLAN CPPT terakhir
            $lastPlan =
                collect($ri['cppt'] ?? [])
                    ->pluck('soap.plan')
                    ->filter()
                    ->last() ?? '';

            // Halaman 2 - kondisi saat pulang
            $gcsPulang = collect($tandaObs)->pluck('gcs')->filter()->last() ?: $gcsAwal;
            $catatanPenting =
                collect($ri['cppt'] ?? [])
                    ->pluck('soap.subjective')
                    ->filter()
                    ->last() ?? '';

            // Cara keluar RS / Disposisi
            // 1) Opsi master (seperti yang kamu punya)
            $tindakLanjutOptions = [
                ['tindakLanjut' => 'Pulang Sehat', 'tindakLanjutKode' => '371827001', 'tindakLanjutKodeBpjs' => 1],
                [
                    'tindakLanjut' => 'Pulang dengan Permintaan Sendiri',
                    'tindakLanjutKode' => '266707007',
                    'tindakLanjutKodeBpjs' => 3,
                ],
                [
                    'tindakLanjut' => 'Pulang Pindah / Rujuk',
                    'tindakLanjutKode' => '306206005',
                    'tindakLanjutKodeBpjs' => 5,
                ], // tidak ada padanan di BPJS SEP
                [
                    'tindakLanjut' => 'Pulang Tanpa Perbaikan',
                    'tindakLanjutKode' => '371828006',
                    'tindakLanjutKodeBpjs' => 5,
                ], // tidak ada padanan di BPJS SEP
                ['tindakLanjut' => 'Meninggal', 'tindakLanjutKode' => '419099009', 'tindakLanjutKodeBpjs' => 4],
                ['tindakLanjut' => 'Lain-lain', 'tindakLanjutKode' => '74964007', 'tindakLanjutKodeBpjs' => 5],
            ];

            // 2) Jadikan lookup by KODE (lebih gampang dipanggil)
            $tindakLanjutLookup = collect($tindakLanjutOptions)->keyBy('tindakLanjutKode');

            // 3) Ambil model data dari $ri (keduanya kode; pakai salah satu yang terisi)
            $modelTindakLanjut = data_get($ri, 'perencanaan.tindakLanjut', []);
            $selectedKodeTL =
                data_get($modelTindakLanjut, 'tindakLanjutKode') ?: data_get($modelTindakLanjut, 'tindakLanjut'); // ex: "419099009"
            $selectedTindakLanjut = $selectedKodeTL ? $tindakLanjutLookup->get($selectedKodeTL) : null;

            $labelTerpilihTindakLanjut = data_get($selectedTindakLanjut, 'tindakLanjut', ''); // ex: "Meninggal"
            $labelTerpilihTindakLanjutKode = data_get($selectedTindakLanjut, 'tindakLanjutKode', ''); // ex: "266707007"
            $kodeBpjsTerpilihTindakLanjut = data_get($selectedTindakLanjut, 'tindakLanjutKodeBpjs'); // ex: 4
            $keteranganTambahanTindakLanjut = trim(data_get($modelTindakLanjut, 'keteranganTindakLanjut', '-'));

            // 4) Sesuaikan $statusPulang (kalau field lama kosong, pakai hasil mapping)
            $statusPulang = $labelTerpilihTindakLanjut ?: '-';

            // 5) Tgl kontrol & aturan kontrol
            $tglKontrol = data_get($ri, 'kontrol.tglKontrol', '');
            // - ada tanggal kontrol, ATAU
            // - status mengandung "Pulang" (pulang sehat / permintaan sendiri / rujuk / tanpa perbaikan)
            $isKontrol = !empty($tglKontrol);
            // 6) Heuristik meninggal (dari label)
            $isMeninggal =
                stripos((string) $statusPulang, 'meninggal') !== false ||
                (string) $kodeBpjsTerpilihTindakLanjut === '4';

            // 7) (Opsional) Buat string final yang enak dipajang
            $statusPulangDisplay = 'Status Pulang: ' . ($statusPulang !== '' ? $statusPulang : '- (belum diisi)');
            if ($keteranganTambahanTindakLanjut !== '') {
                $statusPulangDisplay .= " — Keterangan: {$keteranganTambahanTindakLanjut}";
            }
        @endphp

        {{-- ======================= HEADER + NO. RM ======================= --}}
        <table class="w-full border-collapse">
            <tr>
                <td class="w-16 align-top border-0">
                    {{-- <img src="{{ asset('logo.png') }}" class="h-12" /> --}}
                    <img src="madinahlogopersegi.png" class="object-fill h-20 mx-4">
                </td>
                <td class="border-0">
                    <div class="text-lg font-bold tracking-wide text-center">RIWAYAT PENGOBATAN</div>
                </td>
                <td class="w-64 align-top border-0">
                    <table class="w-full border border-collapse border-black table-auto">
                        <tr>
                            <th class="px-2 py-1 text-left border border-black">No. Rekam Medis</th>
                            <td class="px-2 py-1 border border-black">
                                <div class="text-center border border-black">
                                    {{ $rm }}
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        {{-- ======================= IDENTITAS ======================= --}}
        <table class="w-full mt-2 border border-collapse border-black table-auto">
            <tr>
                <th class="w-40 px-2 py-1 text-left border border-black">Nama pasien</th>
                <td class="px-2 py-1 border border-black">{{ $nama }}</td>

                <th class="w-40 px-2 py-1 text-left border border-black">Ruang Perawatan</th>
                <td class="px-2 py-1 border border-black">{{ $ruang }}</td>

                <th class="w-24 px-2 py-1 text-left border border-black">Kamar</th>
                <td class="px-2 py-1 border border-black">{{ $kamar }}</td>
            </tr>
            <tr>
                <th class="px-2 py-1 text-left border border-black">Tanggal lahir</th>
                <td class="px-2 py-1 border border-black">{{ $tglLahir }}</td>

                <th class="px-2 py-1 text-left border border-black">Tanggal keluar</th>
                <td class="px-2 py-1 border border-black" colspan="3">
                    @if (strtolower($statusPulang) === 'meninggal')
                        Meninggal, {{ $tglKeluar }}
                    @else
                        {{ $tglKeluar }}
                    @endif
                </td>
            </tr>
            <tr>
                <th class="px-2 py-1 text-left border border-black">Tanggal masuk RS</th>
                <td class="px-2 py-1 border border-black">{{ $tglMasuk }}</td>

                <th class="px-2 py-1 text-left border border-black">DPJP</th>
                <td class="px-2 py-1 border border-black" colspan="3">{{ $dpjp }}</td>
            </tr>
            <tr>
                <th class="px-2 py-1 text-left border border-black">Diagnosis masuk</th>
                <td class="px-2 py-1 border border-black" colspan="5">{{ $diagnosaMasuk }}</td>
            </tr>
            <tr>
                <th class="px-2 py-1 text-left border border-black">Indikasi Rawat Inap</th>
                <td class="px-2 py-1 border border-black" colspan="5">{{ $indikasiRawatInap }}</td>
            </tr>
        </table>

        {{-- ======================= ANAMNESIS ======================= --}}
        <table class="w-full mt-2 border border-collapse border-black table-auto">
            <tr class="font-semibold bg-gray-100">
                <th colspan="6" class="px-2 py-1 text-left">ANAMNESIS</th>
            </tr>
            <tr>
                <th class="w-48 px-2 py-1 text-left border border-black">Keluhan utama</th>
                <td class="px-2 py-1 border border-black" colspan="5">{{ $keluhanUtama }}</td>
            </tr>
            <tr>
                <th class="px-2 py-1 text-left border border-black">Riwayat penyakit</th>
                <td class="px-2 py-1 border border-black" colspan="5">{{ $riwayatPenyakit }}</td>
            </tr>
        </table>

        {{-- ======================= PEMERIKSAAN FISIK ======================= --}}
        <table class="w-full mt-2 border border-collapse border-black table-auto">
            <tr class="font-semibold bg-gray-100">
                <th colspan="6" class="px-2 py-1 text-left">PEMERIKSAAN FISIK</th>
            </tr>
            <tr>
                <th class="w-48 px-2 py-1 text-left border border-black">Keadaan umum</th>
                <td class="px-2 py-1 border border-black" colspan="5">
                    {{ data_get($ri, 'pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.aktivitas.pilihan', '') ?: '-' }}
                </td>
            </tr>
            <tr>
                <th class="px-2 py-1 text-left border border-black">Tanda vital</th>
                <td class="px-2 py-1 border border-black" colspan="5">
                    Tekanan darah : {{ $td }} &nbsp;&nbsp;
                    Suhu : {{ $suhu }} &nbsp;&nbsp;
                    Nadi : {{ $nadi }} &nbsp;&nbsp;
                    Frekuensi napas : {{ $rr }}
                </td>
            </tr>
            <tr>
                <th class="px-2 py-1 text-left border border-black">Pemeriksaan Fisik</th>
                <td class="px-2 py-1 border border-black" colspan="5">{{ $pemeriksaanFisik }}</td>
            </tr>
        </table>

        {{-- ======================= PEMERIKSAAN PENUNJANG ======================= --}}
        <table class="w-full mt-2 border border-collapse border-black table-auto">
            <tr class="font-semibold bg-gray-100">
                <th colspan="6" class="px-2 py-1 text-left">PEMERIKSAAN PENUNJANG</th>
            </tr>
            <tr>
                <th class="w-48 px-2 py-1 text-left border border-black">1. LABORATORIUM</th>
                <td class="px-2 py-1 border border-black" colspan="5">{{ $labText }}</td>
            </tr>
            <tr>
                <th class="px-2 py-1 text-left border border-black">2. RADIOLOGI</th>
                <td class="px-2 py-1 border border-black" colspan="5">{{ $radText }}</td>
            </tr>
            <tr>
                <th class="px-2 py-1 text-left border border-black">3. LAIN-LAIN</th>
                <td class="px-2 py-1 border border-black" colspan="5">
                    {{ $lainText }}
                    {{-- contoh menampilkan pemakaian oksigen terakhir --}}
                    {{-- @php
                        $oks = collect(data_get($ri, 'observasi.pemakaianOksigen.pemakaianOksigenData', []))->last();
                    @endphp
                    @if ($oks)
                        Oksigen: {{ $oks['jenisAlatOksigen'] ?? '' }}; Dosis: {{ $oks['dosisOksigen'] ?? '' }}; Mulai:
                        {{ $oks['tanggalWaktuMulai'] ?? '' }}
                    @endif --}}
                </td>
            </tr>
        </table>

        {{-- ======================= TERAPI/TINDAKAN DI RS ======================= --}}
        <table class="w-full mt-2 border border-collapse border-black table-auto">
            <tr class="font-semibold bg-gray-100">
                <th colspan="6" class="px-2 py-1 text-left">TERAPI/TINDAKAN MEDIS SELAMA DI RUMAH SAKIT</th>
            </tr>
            <tr>
                <td class="break-words whitespace-pre-line border border-black" colspan="6">
                    <div class="p-2 m-2">
                        {{ $terapiRS }}
                    </div>
                </td>
            </tr>
        </table>

        {{-- ======================= DIAGNOSIS & TINDAKAN + ICD ======================= --}}
        <table class="w-full mt-2 border border-collapse border-black table-auto">
            <tr>
                <th class="w-48 px-2 py-1 text-left border border-black">DIAGNOSIS UTAMA</th>
                <td class="px-2 py-1 border border-black">{{ $dxUtama }}</td>
                <th class="w-24 px-2 py-1 text-left border border-black">ICD-10</th>
                <td class="w-32 px-2 py-1 border border-black">{{ $dxUtamaICD }}</td>
            </tr>

            <tr>
                <th class="px-2 py-1 text-left align-top border border-black">DIAGNOSIS SEKUNDER :</th>
                <td class="px-2 py-1 align-top border border-black">
                    <ol class="pl-6 leading-6 list-decimal">
                        @forelse($dxSekunder as $dx)
                            <li>{{ $dx }}</li>
                        @empty
                            <li>&nbsp;</li>
                        @endforelse
                    </ol>
                </td>
                <th class="px-2 py-1 text-left align-top border border-black">ICD-10</th>
                <td class="px-2 py-1 align-top border border-black">
                    <ol class="pl-6 leading-6 list-decimal">
                        @forelse($dxSekunderICD as $code)
                            <li>{{ $code }}</li>
                        @empty
                            <li>&nbsp;</li>
                        @endforelse
                    </ol>
                </td>
            </tr>

            <tr>
                <th class="px-2 py-1 text-left align-top border border-black">TINDAKAN/PROSEDUR :</th>
                <td class="px-2 py-1 align-top border border-black">
                    <ol class="pl-6 leading-6 list-decimal">
                        @forelse($tindakanDesc as $t)
                            <li>{{ $t }}</li>
                        @empty
                            <li>&nbsp;</li>
                        @endforelse
                    </ol>
                </td>
                <th class="px-2 py-1 text-left align-top border border-black">ICD-9-CM</th>
                <td class="px-2 py-1 align-top border border-black">
                    <ol class="pl-6 leading-6 list-decimal">
                        @forelse($tindakanCode as $c)
                            <li>{{ $c }}</li>
                        @empty
                            <li>&nbsp;</li>
                        @endforelse
                    </ol>
                </td>
            </tr>

            <tr>
                <th class="px-2 py-1 text-left border border-black">DIET</th>
                <td class="px-2 py-1 border border-black" colspan="3">{{ $diet }}</td>
            </tr>
            {{-- <tr>
                <th class="px-2 py-1 text-left border border-black">INSTRUKSI DAN EDUKASI (TINDAK LANJUT)</th>
                <td class="px-2 py-6 border border-black" colspan="3">{{ $lastPlan }}</td>
            </tr> --}}
        </table>

        <div class="mt-1 italic text-right">Bersambung ke hal 2</div>
        <div class="page-break"></div>

        {{-- ======================= HALAMAN 2 ======================= --}}
        <div class="font-semibold">Sambungan <span class="uppercase">RINGKASAN PULANG</span></div>

        <table class="w-full mt-1 border border-collapse border-black table-auto">
            <tr>
                <th class="w-40 px-2 py-1 text-left border border-black">Nama pasien :</th>
                <td class="px-2 py-1 border border-black">{{ $nama }}</td>
                <th class="w-40 px-2 py-1 text-left border border-black">No. Rekam Medis :</th>
                <td class="px-2 py-1 border border-black">
                    <div class="flex gap-1">
                        {{ $rm }}
                    </div>
                </td>
            </tr>
        </table>


        @php
            // kumpulan TTV
            $tandaObs = collect(data_get($ri, 'observasi.observasiLanjutan.tandaVital', []));

            // filter observasi pada tanggal exitDate (format "dd/mm/yyyy hh:mm:ss")
            $exitDateOnly = !empty($ri['exitDate']) ? Carbon::createFromFormat('d/m/Y H:i:s', $ri['exitDate']) : null;

            $lastObsExit = $exitDateOnly
                ? $tandaObs
                    ->filter(function ($r) use ($exitDateOnly) {
                        $waktuPemeriksaan = $r['waktuPemeriksaan'] ?? null;
                        if (!$waktuPemeriksaan) {
                            return false;
                        }

                        try {
                            $parsed = Carbon::createFromFormat('d/m/Y H:i:s', $waktuPemeriksaan);
                            return $parsed->isSameDay($exitDateOnly);
                        } catch (\Throwable $e) {
                            return false;
                        }
                    })
                    ->sortBy(function ($r) {
                        try {
                            return Carbon::createFromFormat('d/m/Y H:i:s', $r['waktuPemeriksaan'])->timestamp;
                        } catch (\Throwable $e) {
                            return 0;
                        }
                    })
                    ->last()
                : null;

            // fallback: kalau gak ada yang sama harinya, ambil yang paling akhir (opsional sort kalau urutan tidak terjamin)
            $obs = $lastObsExit ?: $tandaObs->sortBy('waktuPemeriksaan')->last() ?: [];

            // set variabel *pulang*
            $sis = trim((string) data_get($obs, 'sistolik', ''));
            $dis = trim((string) data_get($obs, 'distolik', ''));

            $tdPulang =
                $sis !== '' && $dis !== '' ? "{$sis}/{$dis}" : ($sis !== '' ? $sis : ($dis !== '' ? "/{$dis}" : '-'));
            $suhuPulang = ($tmp = trim((string) data_get($obs, 'suhu', ''))) !== '' ? $tmp : '-';
            $nadiPulang = ($tmp = trim((string) data_get($obs, 'frekuensiNadi', ''))) !== '' ? $tmp : '-';
            $rrPulang = ($tmp = trim((string) data_get($obs, 'frekuensiNafas', ''))) !== '' ? $tmp : '-';
            $gcsPulang = ($tmp = trim((string) data_get($obs, 'gcs', ''))) !== '' ? $tmp : ($isMeninggal ? '0' : '-');

            //ResepPulang
            // Ambil header terakhir by resepDate (format "d/m/Y H:i:s")
            $eresepHdrs = collect(data_get($ri, 'eresepHdr', []));

            if ($exitDateOnly) {
                // filter resep yang same-day
                $sameDay = $eresepHdrs->filter(function ($h) use ($exitDateOnly) {
                    $tgl = data_get($h, 'resepDate');
                    if (!$tgl) {
                        return false;
                    }
                    try {
                        $parsed = Carbon::createFromFormat('d/m/Y H:i:s', $tgl);
                        return $parsed->isSameDay($exitDateOnly);
                    } catch (\Throwable $e) {
                        return false;
                    }
                });

                // kalau ada di hari yang sama
                if ($sameDay->isNotEmpty()) {
                    $eresepLastHdr = $sameDay
                        ->sortByDesc(function ($h) {
                            try {
                                return Carbon::createFromFormat('d/m/Y H:i:s', data_get($h, 'resepDate'))->timestamp;
                            } catch (\Throwable $e) {
                                return 0;
                            }
                        })
                        ->first();
                } else {
                    // fallback: ambil record terakhir dari semua HDR
                    $eresepLastHdr = $eresepHdrs
                        ->sortByDesc(function ($h) {
                            try {
                                return Carbon::createFromFormat('d/m/Y H:i:s', data_get($h, 'resepDate'))->timestamp;
                            } catch (\Throwable $e) {
                                return 0;
                            }
                        })
                        ->first();
                }
            } else {
                // kalau exitDate kosong → langsung ambil HDR terakhir overall
                $eresepLastHdr = $eresepHdrs
                    ->sortByDesc(function ($h) {
                        try {
                            return Carbon::createFromFormat('d/m/Y H:i:s', data_get($h, 'resepDate'))->timestamp;
                        } catch (\Throwable $e) {
                            return 0;
                        }
                    })
                    ->first();
            }

            // No & Tgl resep dari HDR
            $noResep = (string) data_get($eresepLastHdr, 'resepNo', '-');
            $tglResep = (string) data_get($eresepLastHdr, 'resepDate', '-'); // sudah d/m/Y H:i:s di data kamu

            // Safety: pastikan array
            $racikList = (array) data_get($eresepLastHdr, 'eresepRacikan', []);
            $nonRacikList = (array) data_get($eresepLastHdr, 'eresep', []);

        @endphp

        {{-- KONDISI SAAT PULANG --}}
        <table class="w-full mt-2 border border-collapse border-black table-auto">
            <tr class="font-semibold bg-gray-100">
                <th colspan="4" class="px-2 py-1 text-left">KONDISI SAAT PULANG</th>
            </tr>
            <tr>
                <th class="w-48 px-2 py-1 text-left align-top border border-black">Keadaan umum</th>
                <td class="px-2 py-1 border border-black">
                    {{ $statusPulangDisplay }}
                </td>
                <th class="w-24 px-2 py-1 text-left align-top border border-black">GCS</th>
                <td class="px-2 py-1 border border-black">{{ $gcsPulang }}</td>
            </tr>
            <tr>
                <th class="px-2 py-1 text-left border border-black">Tanda vital</th>
                <td class="px-2 py-1 border border-black" colspan="3">
                    Tekanan darah : {{ $tdPulang }} &nbsp;&nbsp;
                    Suhu : {{ $suhuPulang }} &nbsp;&nbsp;
                    Nadi : {{ $nadiPulang }} &nbsp;&nbsp;
                    Frekuensi napas : {{ $rrPulang }}
                </td>
            </tr>
            <tr>
                <th class="px-2 py-1 text-left align-top border border-black">Catatan penting (kondisi saat ini)</th>
                <td class="px-2 py-6 border border-black" colspan="3">{{ $catatanPenting }}</td>
            </tr>
        </table>
        {{-- CARA KELUAR RS --}}
        <table class="w-full mt-2 border border-collapse border-black table-auto">
            <tr class="font-semibold bg-gray-100">
                <th colspan="6" class="px-2 py-1 text-left">CARA KELUAR RS</th>
            </tr>
            <tr>
                <td class="px-2 py-1 border border-black">
                    <span
                        class="inline-block w-3 h-3 mr-2 align-middle border border-black @if ($labelTerpilihTindakLanjutKode === '371827001') bg-gray-900 @endif">
                        &nbsp;
                    </span> Pulang Sehat
                </td>
                <td class="px-2 py-1 border border-black">
                    <span
                        class="inline-block w-3 h-3 mr-2 align-middle border border-black @if ($labelTerpilihTindakLanjutKode === '266707007') bg-gray-900 @endif">
                        &nbsp;
                    </span> Pulang Atas Permintaan Sendiri
                </td>
                <td class="px-2 py-1 border border-black">
                    <span
                        class="inline-block w-3 h-3 mr-2 align-middle border border-black @if ($labelTerpilihTindakLanjutKode === '306206005') bg-gray-900 @endif">
                        &nbsp;
                    </span> Dirujuk
                </td>
                <td class="px-2 py-1 border border-black">
                    <span
                        class="inline-block w-3 h-3 mr-2 align-middle border border-black @if ($labelTerpilihTindakLanjutKode === '371828006') bg-gray-900 @endif">
                        &nbsp;
                    </span> Pulang Tanpa Perbaikan
                </td>
                <td class="px-2 py-1 border border-black">
                    <span
                        class="inline-block w-3 h-3 mr-2 align-middle border border-black @if ($labelTerpilihTindakLanjutKode === '419099009') bg-gray-900 @endif">
                        &nbsp;
                    </span> Meninggal
                </td>
                <td class="px-2 py-1 border border-black">
                    <span
                        class="inline-block w-3 h-3 mr-2 align-middle border border-black @if ($labelTerpilihTindakLanjutKode === '74964007') bg-gray-900 @endif">
                        &nbsp;
                    </span> Lain-lain
                </td>
            </tr>
        </table>



        {{-- TINDAK LANJUT --}}
        <table class="w-full mt-2 border border-collapse border-black table-auto">
            <tr class="font-semibold bg-gray-100">
                <th colspan="6" class="px-2 py-1 text-left">TINDAK LANJUT</th>
            </tr>
            <tr>
                <td class="w-1/2 px-2 py-1 border border-black">
                    <span
                        class="inline-block w-3 h-3 mr-2 align-middle border border-black
                        @if ($isKontrol) bg-gray-900 @endif">
                        &nbsp;
                    </span>
                    Kontrol rawat jalan, tanggal
                    <span class="inline-block w-56 align-middle border-b border-black border-dotted">
                        &nbsp;{{ $tglKontrol }}&nbsp;
                    </span>
                </td>
                <td class="w-1/2 px-2 py-1 border border-black">
                    <span class="inline-block w-3 h-3 mr-2 align-middle border border-black">&nbsp;</span>
                    <span class="inline-block w-56 border-b border-black border-dotted">&nbsp;&nbsp;</span>
                </td>
            </tr>
            <tr>
                <td class="px-2 py-1 border border-black">
                    <span
                        class="inline-block w-3 h-3 mr-2 align-middle border border-black
                        @if ($statusPulang === 'Dirujuk') bg-gray-900 @endif">
                        &nbsp;
                    </span>
                    Dirujuk ke
                    <span class="inline-block w-64 border-b border-black border-dotted">&nbsp;&nbsp;</span>
                </td>
                <td class="px-2 py-1 border border-black">
                    <span class="inline-block w-3 h-3 mr-2 align-middle border border-black">&nbsp;</span>
                    <span class="inline-block w-56 border-b border-black border-dotted">&nbsp;&nbsp;</span>
                </td>
            </tr>
        </table>


        {{-- TERAPI PULANG --}}
        <table class="w-full mt-2 border border-collapse border-black table-auto">
            <tr class="font-semibold bg-gray-100">
                <th colspan="4" class="px-2 py-1 text-left">TERAPI PULANG</th>
            </tr>



            {{-- Baris info resep (bukan looping) --}}
            <tr>
                <th class="px-2 py-1 text-left border border-black">No Resep</th>
                <td class="px-2 py-1 border border-black">{{ $noResep }}</td>
                <th class="px-2 py-1 text-left border border-black">Tgl Resep</th>
                <td class="px-2 py-1 border border-black">{{ $tglResep }}</td>
            </tr>

            {{-- ================== NON-RACIKAN (setelah racikan) ================== --}}
            @if (!empty($nonRacikList))
                @foreach ($nonRacikList as $i => $detail)
                    @php
                        $rowId = $detail['riObatDtl'] ?? $i;
                        $hdrId = $tglResep ?? 'hdr';
                        $productName = $detail['productName'] ?? '-';
                        $qty = $detail['qty'] ?? '-';
                        $signaX = $detail['signaX'] ?? '';
                        $signaHari = $detail['signaHari'] ?? '';
                        $catatanKhusus = !empty($detail['catatanKhusus']) ? ' (' . $detail['catatanKhusus'] . ')' : '';

                        // Format string: "R/ {product} | No. {qty} | S {signaX}dd{signaHari}(catatanKhusus)"
                        $line =
                            'R/ ' .
                            $productName .
                            ' | No. ' .
                            $qty .
                            ' | S ' .
                            $signaX .
                            'dd' .
                            $signaHari .
                            $catatanKhusus;
                    @endphp

                    <tr wire:key="eresep-{{ $hdrId }}-{{ $rowId }}"
                        class="border-b dark:border-gray-700">
                        <td class="w-1/2 px-4 py-2">
                            {{ $line }}
                        </td>
                    </tr>
                @endforeach
            @else
                <tr class="border-b dark:border-gray-700">
                    <td class="w-1/2 px-4 py-2">Belum ada resep non racikan.</td>
                </tr>
            @endif


            {{-- ================== RACIKAN (ditampilkan lebih dulu) ================== --}}
            @if (!empty($racikList))
                @php $myPreviousRow = null; @endphp
                @foreach ($racikList as $i => $detail)
                    @php
                        // guard
                        if (!isset($detail['jenisKeterangan'])) {
                            // kalau tidak ada jenisKeterangan, lewati (menjaga konsistensi contohmu)
                            continue;
                        }

                        $rowId = $detail['riObatDtl'] ?? $i;
                        $hdrId = $tglResep ?? 'hdr';

                        $noRacikan = $detail['noRacikan'] ?? '';
                        $productName = $detail['productName'] ?? '';
                        $qty = $detail['qty'] ?? null; // bisa 0/null
                        $catatan = $detail['catatan'] ?? '';
                        $catatanKhusus = $detail['catatanKhusus'] ?? '';
                        $dosis = $detail['dosis'] ?? '';

                        // Border top saat ganti noRacikan (grouping)
                        $myRacikanBorder = $myPreviousRow !== $noRacikan ? 'border-t-2 ' : '';

                        // Baris detail: "noRacikan/ product - dosis"
                        $lineDetail = trim(
                            ($noRacikan !== '' ? $noRacikan : '') . '/ ' . $productName . ' - ' . $dosis,
                        );

                        // Baris ringkas racikan (hanya jika ada qty): "Jml Racikan {qty} | {catatan} | S {catatanKhusus}"
                        $jmlRacikan =
                            $qty !== null && $qty !== ''
                                ? 'Jml Racikan ' .
                                    $qty .
                                    ($catatan !== '' ? ' | ' . $catatan : '') .
                                    ($catatanKhusus !== '' ? ' | S ' . $catatanKhusus : '')
                                : '';
                    @endphp

                    <tr wire:key="eresep-racikan-{{ $hdrId }}-{{ $rowId }}"
                        class="{{ $myRacikanBorder }} group">
                        <td class="w-1/2 px-4 py-2 whitespace-pre-line">
                            {{ $lineDetail }}
                            @if ($jmlRacikan !== '')
                                {{ "\n" . $jmlRacikan }}
                            @endif
                        </td>
                    </tr>

                    @php $myPreviousRow = $noRacikan; @endphp
                @endforeach
            @else
                <tr class="border-b dark:border-gray-700">
                    <td class="w-1/2 px-4 py-2">Belum ada resep racikan.</td>
                </tr>
            @endif


        </table>

        {{-- TTD --}}
        <table class="w-full mt-3 border border-collapse border-black table-auto">
            <tr>
                <td class="px-2 py-10 align-bottom border border-black">
                    Tanda tangan pasien/keluarga,
                    <div class="mt-8 text-center">( ................................................ )<br />.</div>
                </td>
                <td class="px-2 py-10 align-bottom border border-black">
                    Tulungagung,{{ data_get($ri, 'exitDate', '-') }}
                    <div class="mt-8 text-center">
                        ( ................................................ )
                        <br />
                        {{ $dpjp }}
                    </div>
                </td>
            </tr>
        </table>

        {{-- FOOTER --}}
        <div class="text-center text-[10px] mt-2 font-semibold">
            MOHON UNTUK TIDAK MENGGUNAKAN SINGKATAN DALAM PENULISAN DIAGNOSIS DAN TINDAKAN<br />
            SERTA DITULIS DENGAN RAPI
        </div>
        <div class="text-center text-[10px]">
            Jl. Jatiwayang, RT.002/RW.001, Lingkungan 2, Ngunut, Kec. Ngunut, Kabupaten Tulungagung, Jawa Timur 66292
        </div>
        <div class="text-right text-[10px]">2/2</div>
    </div>


</body>

</html>
