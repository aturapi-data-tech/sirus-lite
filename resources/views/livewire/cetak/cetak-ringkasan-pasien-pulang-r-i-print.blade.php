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

            /* ========= 1) Sumber data dasar ========= */
            $pasien = data_get($dataPasien, 'pasien', []);
            $ri = $dataDaftarRi ?? [];

            /* ========= 2) Identitas Pasien & Perawatan ========= */
            $rm = (string) data_get($pasien, 'regNo', '');
            $nama = (string) data_get($pasien, 'regName', '');
            $tglLahir = (string) data_get($pasien, 'tglLahir', '');
            $ruang = (string) data_get($ri, 'bangsalDesc', '');
            $kamar = trim(
                (string) data_get($ri, 'roomDesc', '') . (data_get($ri, 'bedNo') ? ' / ' . data_get($ri, 'bedNo') : ''),
            );
            $tglMasuk = (string) data_get($ri, 'entryDate', '');
            $tglKeluar = (string) data_get($ri, 'exitDate', '');

            /* DPJP (levelingDokter = Utama) */
            $dokterUtama = collect(data_get($ri, 'pengkajianAwalPasienRawatInap.levelingDokter', []))->first(
                fn($r) => strcasecmp((string) data_get($r, 'levelDokter', ''), 'Utama') === 0,
            );
            $dpjp = (string) data_get($dokterUtama, 'drName', 'DPJP');

            /* ========= 3) Ringkasan Masuk & Anamnesis ========= */
            $diagnosaMasuk = (string) data_get($ri, 'pengkajianAwalPasienRawatInap.bagian1DataUmum.diagnosaMasuk', '');
            $keluhanUtama = (string) data_get($ri, 'pengkajianDokter.anamnesa.keluhanUtama', '');
            $keluhanTambahanIndikasiInap = (string) data_get($ri, 'pengkajianDokter.anamnesa.keluhanTambahan', '');
            $riwayatPenyakit =
                'Riwayat Penyakit Sekarang: ' .
                (string) data_get($ri, 'pengkajianDokter.anamnesa.riwayatPenyakit.sekarang', '-') .
                "\n" .
                'Riwayat Penyakit Dahulu: ' .
                (string) data_get($ri, 'pengkajianDokter.anamnesa.riwayatPenyakit.dahulu', '-') .
                "\n" .
                'Riwayat Penyakit Keluarga: ' .
                (string) data_get($ri, 'pengkajianDokter.anamnesa.riwayatPenyakit.keluarga', '-') .
                "\n";

            /* ========= 4) Pemeriksaan Fisik Awal ========= */
            $tv = (array) data_get($ri, 'pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital', []);
            $td = trim((string) data_get($tv, 'sistolik', '') . '/' . (string) data_get($tv, 'distolik', ''));
            $suhu = (string) data_get($tv, 'suhu', '');
            $nadi = (string) data_get($tv, 'frekuensiNadi', '');
            $rr = (string) data_get($tv, 'frekuensiNafas', '');
            $gcsAwal = (string) data_get(
                $ri,
                'pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan.neurologi.gcs',
                '',
            );
            $pemeriksaanFisik = (string) data_get($ri, 'pengkajianDokter.fisik', '');

            /* ========= 5) Penunjang (GDA, Lab, Rad, Lain) ========= */
            $tandaObs = collect(data_get($ri, 'observasi.observasiLanjutan.tandaVital', []));
            $gdaAwal = (string) data_get($tv, 'gda', '');
            $gdaAkhir = $tandaObs->pluck('gda')->filter(fn($v) => $v !== '' && $v !== '-' && $v !== '0')->last();
            $gdaText = 'GDA awal: ' . ($gdaAwal ?: '-') . '; GDA terakhir: ' . ($gdaAkhir ?: '-') . ' ';

            $labText = trim((string) data_get($ri, 'pengkajianDokter.hasilPemeriksaanPenunjang.laboratorium', ''));
            $radText =
                'Hasil radiologi: ' .
                trim((string) data_get($ri, 'pengkajianDokter.hasilPemeriksaanPenunjang.radiologi', '-'));
            $lainText =
                'Pemeriksaan penunjang lain: ' .
                trim((string) data_get($ri, 'pengkajianDokter.hasilPemeriksaanPenunjang.penunjangLain', '-'));

            /* ========= 6) Diagnosis (ICD + Free Text) ========= */
            $dxList = collect(data_get($ri, 'diagnosis', []));
            $dxFree = trim((string) data_get($ri, 'diagnosisFreeText', ''));

            $dxUtamaRow =
                $dxList->first(function ($d) {
                    $k = strtolower($d['kategoriDiagnosa'] ?? '');
                    return in_array($k, ['utama', 'primer', 'primary', 'utama/primer'], true);
                }) ?:
                $dxList->first();

            $dxUtama = (string) data_get($dxUtamaRow, 'diagDesc', '');
            $dxUtamaICD = (string) data_get($dxUtamaRow, 'icdX', '');
            $dxSekunderRows = $dxList
                ->reject(fn($d) => $dxUtamaRow && data_get($d, 'diagId') === data_get($dxUtamaRow, 'diagId'))
                ->values();
            $dxSekunder = $dxSekunderRows->pluck('diagDesc')->filter()->values()->all();
            $dxSekunderICD = $dxSekunderRows->pluck('icdX')->filter()->values()->all();

            /* Free text -> utama/sekunder */
            if ($dxFree !== '') {
                $freeDxItems = collect(preg_split('/\r\n|\r|\n|;|\|/', $dxFree))->map('trim')->filter()->values();
                if ($dxUtama === '' && $freeDxItems->isNotEmpty()) {
                    $dxUtama = $freeDxItems->shift();
                }
                foreach ($freeDxItems as $item) {
                    $dxSekunder[] = $item;
                    $dxSekunderICD[] = '';
                }
            }

            /* ========= 7) Tindakan/Prosedur (ICD-9-CM + Free Text) ========= */
            $procList = collect(data_get($ri, 'procedure', []))
                ->map(
                    fn($p) => [
                        'desc' => trim((string) data_get($p, 'procedureDesc', '')),
                        'code' => trim((string) data_get($p, 'procedureId', '')),
                    ],
                )
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

            /* ========= 8) Diet ========= */
            $diet = trim((string) data_get($ri, 'pengkajianDokter.rencana.diet', '-'));

            /* ========= 9) Terapi/Tindakan Selama di RS (bukan resep pulang) ========= */
            $hdrs = collect((array) data_get($ri, 'eresepHdr', []));

            /* HDR terakhir (untuk dipisahkan) */
            $lastHdr = $hdrs
                ->sortByDesc(function ($h) {
                    try {
                        return Carbon::createFromFormat('d/m/Y H:i:s', (string) data_get($h, 'resepDate'))->timestamp;
                    } catch (\Throwable $e) {
                        return 0;
                    }
                })
                ->first();

            /* Buang HDR terakhir => yang tersisa untuk ringkasan "selama di RS" */
            $hdrsExceptLast = $lastHdr
                ? $hdrs->reject(fn($h) => data_get($h, 'resepNo') == data_get($lastHdr, 'resepNo'))
                : $hdrs;

            /* ========== Normalizer serbaguna (pakai di non-racik & racik) ========== */
            $normalize = function (?string $s) {
                $s = trim((string) $s);
                $s = preg_replace('/\s+/u', ' ', $s ?? '');
                return mb_strtolower($s ?? '', 'UTF-8');
            };

            /* =========================================================
            NON-RACIKAN — Flatten lalu GROUP BY obat (+ regimen) dan sum qty
            ========================================================= */
            // Flatten semua NON-RACIKAN (kecuali HDR terakhir)
            $allNonRacikRaw = $hdrsExceptLast->flatMap(fn($h) => (array) data_get($h, 'eresep', []))->values();

            /**
             * Kunci grouping:
             * - productId (utama) ATAU productName (fallback)
             * - signaX + signaHari (agar regimen berbeda tidak digabung)
             * - catatanKhusus (opsional — ikut digabungkan agar catatan berbeda tidak tercampur)
             *
             * Jika ingin murni "per obat saja", ubah $uniqKey cukup ke productId/productName.
             */
            $allNonRacik = collect($allNonRacikRaw)
                ->map(function ($x) use ($normalize) {
                    $pid = (string) data_get($x, 'productId', '');
                    $pname = (string) data_get($x, 'productName', '');
                    $sx = trim((string) data_get($x, 'signaX', ''));
                    $sh = trim((string) data_get($x, 'signaHari', ''));
                    $ck = (string) data_get($x, 'catatanKhusus', '');

                    $uniqKey = implode('|', [
                        $pid !== '' ? $normalize($pid) : $normalize($pname),
                        $normalize($sx),
                        $normalize($sh),
                        $normalize($ck),
                    ]);

                    $x['__uniq'] = $uniqKey;

                    // ==== SORT KEY (komposit & stabil) ====
                    $nameKey = $pid !== '' ? $normalize($pid) : $normalize($pname);
                    $sxKey = str_pad((string) $sx, 4, '0', STR_PAD_LEFT); // 0001, 0010, dst.
                    $shKey = str_pad((string) $sh, 4, '0', STR_PAD_LEFT);
                    $ckKey = $normalize($ck);

                    $x['__sort'] = $nameKey . '|' . $sxKey . '|' . $shKey . '|' . $ckKey;

                    return $x;
                })
                ->groupBy('__uniq')
                ->map(function ($rows) {
                    $first = $rows->first();
                    $sumQty = $rows->sum(function ($r) {
                        $q = data_get($r, 'qty');
                        return is_numeric($q) ? (float) $q : 0;
                    });
                    $first['qty'] = $sumQty > 0 ? $sumQty : data_get($first, 'qty', null);
                    return $first;
                })
                ->sortBy('__sort', SORT_NATURAL | SORT_FLAG_CASE)
                ->values();

            /* =========================================================
            RACIKAN — Flatten lalu GROUP BY obat + dosis dan sum qty
            ========================================================= */
            $allRacikRaw = $hdrsExceptLast->flatMap(fn($h) => (array) data_get($h, 'eresepRacikan', []))->values();

            $racikDistinct = collect($allRacikRaw)
                ->filter(fn($x) => isset($x['jenisKeterangan'])) // jaga-jaga konsistensi
                ->map(function ($x) use ($normalize) {
                    $pid = (string) data_get($x, 'productId', '');
                    $pname = (string) data_get($x, 'productName', '');
                    $dose = (string) data_get($x, 'dosis', '');
                    $noR = (string) data_get($x, 'noRacikan', '');

                    // Kunci nama (pakai productId kalau ada; fallback productName)
                    $nameKey = $pid !== '' ? $normalize($pid) : $normalize($pname);
                    $doseKey = $normalize($dose);

                    // Ambil angka dari noRacikan untuk sort yang natural (R/1, R/2, …)
                    $noRNum = (int) preg_replace('/\D+/', '', $noR ?: '0');
                    $noRKey = str_pad((string) $noRNum, 4, '0', STR_PAD_LEFT);

                    // ===== GROUP KEY: obat + dosis =====
                    $x['__uniq'] = $nameKey . '|' . $doseKey;

                    // ===== SORT KEY: nama → dosis → noRacikan(angka) =====
                    $x['__sort'] = $nameKey . '|' . $doseKey . '|' . $noRKey;

                    return $x;
                })
                ->groupBy('__uniq')
                ->map(function ($rows) {
                    $first = $rows->first();

                    // jumlahkan QTY aman untuk "4"/4/"4.0"
                    $sumQty = $rows->sum(function ($r) {
                        $q = data_get($r, 'qty');
                        return is_numeric($q) ? (float) $q : 0;
                    });
                    $first['qty'] = $sumQty > 0 ? $sumQty : data_get($first, 'qty', null);

                    // pilih noRacikan yang terkecil angkanya (biar rapi)
                    $minNoR = collect($rows)
                        ->sortBy(function ($r) {
                            $nr = (string) data_get($r, 'noRacikan', '');
                            return (int) preg_replace('/\D+/', '', $nr ?: '0');
                        })
                        ->first();
                    if ($minNoR) {
                        $first['noRacikan'] = data_get($minNoR, 'noRacikan', data_get($first, 'noRacikan', ''));
                    }

                    // simpan sort key dari baris pertama (semuanya sama di group)
                    $first['__sort'] = data_get($rows->first(), '__sort', '');

                    return $first;
                })
                ->sortBy('__sort', SORT_NATURAL | SORT_FLAG_CASE)
                ->values();

            /* =========================================================
            Helper format tampilan resep (untuk tabel halaman 1)
            ========================================================= */
            $fmtNonRacik = function ($d) {
                $name = trim((string) data_get($d, 'productName', '-'));
                $qty = trim((string) data_get($d, 'qty', '-'));
                $signaX = trim((string) data_get($d, 'signaX', ''));
                $signaHari = trim((string) data_get($d, 'signaHari', ''));
                $cat = trim((string) data_get($d, 'catatanKhusus', ''));

                return 'R/ ' .
                    $name .
                    ' | No. ' .
                    ($qty !== '' ? $qty : '-') .
                    ' | S ' .
                    ($signaX !== '' ? $signaX : '-') .
                    'dd' .
                    ($signaHari !== '' ? $signaHari : '-') .
                    ($cat !== '' ? ' (' . $cat . ')' : '');
            };

            $fmtRacik = function ($d) {
                $noR = trim((string) data_get($d, 'noRacikan', ''));
                $name = trim((string) data_get($d, 'productName', ''));
                $dose = trim((string) data_get($d, 'dosis', ''));
                $qty = data_get($d, 'qty');
                $cat = trim((string) data_get($d, 'catatan', ''));
                $ck = trim((string) data_get($d, 'catatanKhusus', ''));

                $line = ($noR !== '' ? $noR . '/ ' : '') . $name . ($dose !== '' ? ' - ' . $dose : '');
                if ($qty !== null && $qty !== '') {
                    $line .=
                        "\nJml Racikan " . $qty . ($cat !== '' ? ' | ' . $cat : '') . ($ck !== '' ? ' | S ' . $ck : '');
                }
                return $line;
            };

            /* ========= 10) Tindak Lanjut / Cara Keluar RS ========= */
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
                ],
                [
                    'tindakLanjut' => 'Pulang Tanpa Perbaikan',
                    'tindakLanjutKode' => '371828006',
                    'tindakLanjutKodeBpjs' => 5,
                ],
                ['tindakLanjut' => 'Meninggal', 'tindakLanjutKode' => '419099009', 'tindakLanjutKodeBpjs' => 4],
                ['tindakLanjut' => 'Lain-lain', 'tindakLanjutKode' => '74964007', 'tindakLanjutKodeBpjs' => 5],
            ];
            $tindakLanjutLookup = collect($tindakLanjutOptions)->keyBy('tindakLanjutKode');

            $modelTindakLanjut = (array) data_get($ri, 'perencanaan.tindakLanjut', []);
            $selectedKodeTL =
                (string) (data_get($modelTindakLanjut, 'tindakLanjutKode') ?:
                data_get($modelTindakLanjut, 'tindakLanjut'));
            $selectedTindakLanjut = $selectedKodeTL ? $tindakLanjutLookup->get($selectedKodeTL) : null;

            $labelTerpilihTindakLanjut = (string) data_get($selectedTindakLanjut, 'tindakLanjut', '');
            $labelTerpilihTindakLanjutKode = (string) data_get($selectedTindakLanjut, 'tindakLanjutKode', '');
            $kodeBpjsTerpilihTindakLanjut = data_get($selectedTindakLanjut, 'tindakLanjutKodeBpjs');
            $keteranganTindakLanjut = trim((string) data_get($modelTindakLanjut, 'keteranganTindakLanjut', ''));

            $statusPulang = $labelTerpilihTindakLanjut ?: '-';
            $tglKontrol = (string) data_get($ri, 'kontrol.tglKontrol', '');
            $isKontrol = !empty($tglKontrol);
            $isMeninggal =
                stripos((string) $statusPulang, 'meninggal') !== false ||
                (string) $kodeBpjsTerpilihTindakLanjut === '4';

            /* ========= 11) Halaman 2: Kondisi Saat Pulang ========= */
            $cppt = collect(data_get($ri, 'cppt', []));
            $exitDateOnly = !empty($tglKeluar) ? Carbon::createFromFormat('d/m/Y H:i:s', $tglKeluar) : null;

            if ($exitDateOnly) {
                $lastCppt = $cppt
                    ->filter(
                        fn($row) => !empty($row['tglCPPT']) &&
                            Carbon::createFromFormat('d/m/Y H:i:s', $row['tglCPPT'])->isSameDay($exitDateOnly),
                    )
                    ->sortByDesc(fn($row) => Carbon::createFromFormat('d/m/Y H:i:s', $row['tglCPPT']))
                    ->first();
            } else {
                $lastCppt = $cppt
                    ->sortByDesc(fn($row) => Carbon::createFromFormat('d/m/Y H:i:s', $row['tglCPPT']))
                    ->first();
            }
            $lastSubjective = (string) data_get($lastCppt, 'soap.subjective', '');

            /* Observasi terakhir di hari pulang (fallback: yang terakhir) */
            $lastObsExit = $exitDateOnly
                ? $tandaObs
                    ->filter(function ($r) use ($exitDateOnly) {
                        $w = data_get($r, 'waktuPemeriksaan');
                        if (!$w) {
                            return false;
                        }
                        try {
                            return Carbon::createFromFormat('d/m/Y H:i:s', $w)->isSameDay($exitDateOnly);
                        } catch (\Throwable $e) {
                            return false;
                        }
                    })
                    ->sortBy(function ($r) {
                        try {
                            return Carbon::createFromFormat('d/m/Y H:i:s', data_get($r, 'waktuPemeriksaan'))->timestamp;
                        } catch (\Throwable $e) {
                            return 0;
                        }
                    })
                    ->last()
                : null;

            $obs = $lastObsExit ?: $tandaObs->sortBy('waktuPemeriksaan')->last() ?: [];
            $sis = trim((string) data_get($obs, 'sistolik', ''));
            $dis = trim((string) data_get($obs, 'distolik', ''));
            $tdPulang =
                $sis !== '' && $dis !== '' ? "{$sis}/{$dis}" : ($sis !== '' ? $sis : ($dis !== '' ? "/{$dis}" : '-'));
            $suhuPulang = ($tmp = trim((string) data_get($obs, 'suhu', ''))) !== '' ? $tmp : '-';
            $nadiPulang = ($tmp = trim((string) data_get($obs, 'frekuensiNadi', ''))) !== '' ? $tmp : '-';
            $rrPulang = ($tmp = trim((string) data_get($obs, 'frekuensiNafas', ''))) !== '' ? $tmp : '-';
            $gcsPulang = ($tmp = trim((string) data_get($obs, 'gcs', ''))) !== '' ? $tmp : ($isMeninggal ? '0' : '-');

            /* ========= 12) TERAPI PULANG (HDR terakhir same-day exitDate, kalau ada; else last overall) ========= */
            $eresepHdrs = collect(data_get($ri, 'eresepHdr', []));

            if ($exitDateOnly) {
                $sameDay = $eresepHdrs->filter(function ($h) use ($exitDateOnly) {
                    $tgl = data_get($h, 'resepDate');
                    if (!$tgl) {
                        return false;
                    }
                    try {
                        return Carbon::createFromFormat('d/m/Y H:i:s', $tgl)->isSameDay($exitDateOnly);
                    } catch (\Throwable $e) {
                        return false;
                    }
                });
                $eresepLastHdr = $sameDay->isNotEmpty()
                    ? $sameDay
                        ->sortByDesc(
                            fn($h) => (function ($d) {
                                try {
                                    return Carbon::createFromFormat('d/m/Y H:i:s', $d)->timestamp;
                                } catch (\Throwable $e) {
                                    return 0;
                                }
                            })(data_get($h, 'resepDate')),
                        )
                        ->first()
                    : $eresepHdrs
                        ->sortByDesc(
                            fn($h) => (function ($d) {
                                try {
                                    return Carbon::createFromFormat('d/m/Y H:i:s', $d)->timestamp;
                                } catch (\Throwable $e) {
                                    return 0;
                                }
                            })(data_get($h, 'resepDate')),
                        )
                        ->first();
            } else {
                $eresepLastHdr = $eresepHdrs
                    ->sortByDesc(function ($h) {
                        try {
                            return Carbon::createFromFormat(
                                'd/m/Y H:i:s',
                                (string) data_get($h, 'resepDate'),
                            )->timestamp;
                        } catch (\Throwable $e) {
                            return 0;
                        }
                    })
                    ->first();
            }

            $noResep = (string) data_get($eresepLastHdr, 'resepNo', '-');
            $tglResep = (string) data_get($eresepLastHdr, 'resepDate', '-');
            $racikList = (array) data_get($eresepLastHdr, 'eresepRacikan', []);
            $nonRacikList = (array) data_get($eresepLastHdr, 'eresep', []);
        @endphp


        {{-- ======================= HEADER + NO. RM ======================= --}}
        <table class="w-full border-collapse">
            <tr>
                <td class="w-16 align-top border-0">
                    {{-- <img src="{{ asset('logo.png') }}" class="h-12" /> --}}
                    <img class="w-auto h-16" src="madinahlogopersegi.png" alt="user photo">
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
                <td class="px-2 py-1 border border-black" colspan="5">{{ $keluhanTambahanIndikasiInap }}</td>
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
                    {{ data_get($ri, 'pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.keluhanUtama', '') ?: '-' }}
                </td>
            </tr>
            <tr>
                <th class="px-2 py-1 text-left border border-black">Tanda vital</th>
                <td class="px-2 py-1 border border-black" colspan="5">
                    Tekanan darah : {{ $td }} &nbsp;&nbsp;
                    Suhu : {{ $suhu }} &nbsp;&nbsp;
                    Nadi : {{ $nadi }} &nbsp;&nbsp;
                    Frekuensi napas : {{ $rr }}&nbsp;&nbsp;
                    {{ $gdaText }}&nbsp;&nbsp;
                    GCS Awal :{{ $gcsAwal }}
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
                <th colspan="4" class="px-2 py-1 text-left">TERAPI / TINDAKAN MEDIS SELAMA DI RUMAH SAKIT
                </th>
            </tr>

            {{-- A. NON-RACIKAN --}}
            <tr class="bg-gray-50">
                <th colspan="4" class="px-2 py-1 text-left border border-black">A. Non-Racikan</th>
            </tr>
            @if ($allNonRacik->isNotEmpty())
                @foreach ($allNonRacik as $i => $d)
                    <tr>
                        <td class="px-3 py-0.5 break-words whitespace-pre-line border border-black" colspan="4">
                            {{ $fmtNonRacik($d) }}
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td class="px-4 py-1 border border-black" colspan="4">Belum ada resep non-racikan.
                    </td>
                </tr>
            @endif

            {{-- B. RACIKAN --}}
            <tr class="bg-gray-50">
                <th colspan="4" class="px-2 py-1 text-left border border-black">B. Racikan</th>
            </tr>
            @if ($racikDistinct->isNotEmpty())
                @foreach ($racikDistinct as $i => $d)
                    <tr>
                        <td class="px-3 py-0.5 break-words whitespace-pre-line border border-black" colspan="4">
                            {{ $fmtRacik($d) }}
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td class="px-4 py-2 border border-black" colspan="4">Belum ada resep racikan.</td>
                </tr>
            @endif
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
        <div class="">Sambungan <span class="uppercase">RINGKASAN PULANG</span></div>

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

        {{-- KONDISI SAAT PULANG --}}
        <table class="w-full mt-2 border border-collapse border-black table-auto">
            <tr class="font-semibold bg-gray-100">
                <th colspan="4" class="px-2 py-1 text-left">KONDISI SAAT PULANG</th>
            </tr>
            <tr>
                <th class="w-48 px-2 py-1 text-left align-top border border-black">Keadaan umum</th>
                <td class="px-2 py-1 border border-black">
                    {{ $lastSubjective }}
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
            {{-- <tr>
                <th class="px-2 py-1 text-left align-top border border-black">Catatan penting (kondisi saat ini)</th>
                <td class="px-2 py-6 border border-black" colspan="3">{{ $catatanPenting }}</td>
            </tr> --}}
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
                        @if ($labelTerpilihTindakLanjutKode === '306206005') bg-gray-900 @endif">
                        &nbsp;
                    </span>
                    Dirujuk ke
                    <span
                        class="inline-block w-64 border-b border-black border-dotted">&nbsp;&nbsp;{{ $keteranganTindakLanjut }}</span>
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
