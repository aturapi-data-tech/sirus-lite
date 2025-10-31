<div class="fixed inset-0 z-40 flex items-center justify-center bg-gray-500/75" role="dialog" aria-modal="true"
    aria-labelledby="judul-ri">
    <div class="relative w-full max-w-6xl max-h-[95vh] overflow-y-auto bg-white rounded-lg shadow-xl">

        {{-- Topbar (seragam seperti RJ) --}}
        <div
            class="sticky top-0 z-10 flex items-center justify-between p-4 text-white border-b rounded-t-lg bg-primary no-print">
            <h3 id="judul-ri" class="text-2xl font-semibold">{{ $myTitle ?? 'Ringkasan Pulang Rawat Inap' }}</h3>
            <button wire:click="closeModalLayanan()"
                class="text-white bg-gray-100/20 hover:bg-gray-200 hover:text-gray-900 rounded-lg p-1.5"
                aria-label="Tutup dialog">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                        clip-rule="evenodd" />
                </svg>
            </button>
        </div>

        {{-- Print styles (copas dari RJ) --}}
        <style>
            @media print {
                .no-print {
                    display: none !important;
                }

                .print-break {
                    page-break-before: always;
                }

                .print-avoid-break {
                    break-inside: avoid;
                    page-break-inside: avoid;
                }

                html,
                body {
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }

                table,
                th,
                td {
                    border: 1px solid #111 !important;
                    border-collapse: collapse !important;
                }

                thead {
                    display: table-header-group;
                }
            }
        </style>

        {{-- BODY --}}
        <div class="h-full overflow-y-auto bg-white">
            @php
                use Carbon\Carbon;

                /* ========= 1) Sumber data dasar ========= */
                $pasien = data_get($dataPasien, 'pasien', []);
                $ri = $dataDaftarTxn ?? [];

                /* ========= 2) Identitas ========= */
                $rm = (string) data_get($pasien, 'regNo', '');
                $nama = (string) data_get($pasien, 'regName', '');
                $tglLahir = (string) data_get($pasien, 'tglLahir', '');
                $ruang = (string) data_get($ri, 'bangsalDesc', '');
                $kamar = trim(
                    (string) data_get($ri, 'roomDesc', '') .
                        (data_get($ri, 'bedNo') ? ' / ' . data_get($ri, 'bedNo') : ''),
                );
                $tglMasuk = (string) data_get($ri, 'entryDate', '');
                $tglKeluar = (string) data_get($ri, 'exitDate', '');

                /* DPJP (levelingDokter = Utama) */
                $dokterUtama = collect(data_get($ri, 'pengkajianAwalPasienRawatInap.levelingDokter', []))->first(
                    fn($r) => strcasecmp((string) data_get($r, 'levelDokter', ''), 'Utama') === 0,
                );
                $dpjp = (string) data_get($dokterUtama, 'drName', 'DPJP');

                /* ========= 3) Ringkasan Masuk & Anamnesis ========= */
                $diagnosaMasuk = (string) data_get(
                    $ri,
                    'pengkajianAwalPasienRawatInap.bagian1DataUmum.diagnosaMasuk',
                    '',
                );
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

                /* ========= 5) Penunjang ========= */
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
                $scDxFree = trim((string) data_get($ri, 'secondaryDiagnosisFreeText', ''));

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

                // Free text → tumpangkan bila kosong
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

                /* ========= 7) Prosedur ========= */
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

                /* ========= 9) Tindak Lanjut ========= */
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

                /* ========= 10) Kondisi Saat Pulang ========= */
                $terapiPulang = (string) data_get($ri, 'pengkajianDokter.rencana.terapiPulang', '');

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
                                return Carbon::createFromFormat(
                                    'd/m/Y H:i:s',
                                    data_get($r, 'waktuPemeriksaan'),
                                )->timestamp;
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
                    $sis !== '' && $dis !== ''
                        ? "{$sis}/{$dis}"
                        : ($sis !== ''
                            ? $sis
                            : ($dis !== ''
                                ? "/{$dis}"
                                : '-'));
                $suhuPulang = ($tmp = trim((string) data_get($obs, 'suhu', ''))) !== '' ? $tmp : '-';
                $nadiPulang = ($tmp = trim((string) data_get($obs, 'frekuensiNadi', ''))) !== '' ? $tmp : '-';
                $rrPulang = ($tmp = trim((string) data_get($obs, 'frekuensiNafas', ''))) !== '' ? $tmp : '-';
                $gcsPulang =
                    ($tmp = trim((string) data_get($obs, 'gcs', ''))) !== '' ? $tmp : ($isMeninggal ? '0' : '-');

                // Helper aksesibilitas checkbox-like
                $checked = function (bool $state) {
                    return $state ? 'bg-gray-900' : '';
                };
                $aria = function (bool $state) {
                    return $state ? 'true' : 'false';
                };

                // Perbaikan: kunci "keadaan umum" yang benar (ganti dari keluhanUtama → keadaanUmum jika tersedia)
                $keadaanUmumAwal = data_get($ri, 'pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.keadaanUmum');
            @endphp

            {{-- IDENTITAS PASIEN (ringkas, supaya cocok cetak) --}}
            <div class="px-4 pt-4">
                <div class="grid grid-cols-4 gap-3 p-3 border border-gray-900 rounded-lg print-avoid-break">

                    <div class="flex flex-col items-center col-span-1 text-center">
                        <div class="flex flex-col items-center justify-start text-center md:col-span-1">
                            <img src="madinahlogopersegi.png" alt="Logo" class="object-contain h-28" />
                            <div class="mt-2 text-xs leading-5">
                                {!! ($myQueryIdentitas->int_address ?? '-') . '<br>' !!}
                                {!! ($myQueryIdentitas->int_city ?? '-') . '<br>' !!}
                                {!! ($myQueryIdentitas->int_phone1 ?? '-') . '<br>' !!}
                                {!! ($myQueryIdentitas->int_phone2 ?? '-') . '<br>' !!}
                                {!! ($myQueryIdentitas->int_fax ?? '-') . '<br>' !!}
                            </div>
                        </div>
                    </div>

                    <div class="w-full col-span-3 gap-2 space-y-1 text-sm ">
                        <dl class="divide-y divide-gray-100/60">
                            <!-- Nama Pasien -->
                            <div class="flex items-baseline gap-3 py-0.5">
                                <span class="w-32 shrink-0">Nama Pasien</span>
                                <span class="shrink-0">:</span>
                                <span class="flex-1 min-w-0 font-semibold truncate">
                                    {{ strtoupper($nama ?: '-') }}
                                </span>
                            </div>

                            <!-- No RM -->
                            <div class="flex items-baseline gap-3 py-0.5">
                                <span class="w-32 shrink-0">No RM</span>
                                <span class="shrink-0">:</span>
                                <span class="flex-1 min-w-0 text-lg font-semibold truncate">
                                    {{ $rm ?: '-' }}
                                </span>
                            </div>

                            <!-- Tanggal Lahir • DPJP (2 kolom fleksibel) -->
                            <div class="flex flex-col gap-2 md:flex-row md:items-baseline md:gap-8 py-0.5">
                                <div class="flex items-baseline min-w-0 gap-3 basis-1/2">
                                    <span class="w-32 shrink-0">Tanggal Lahir</span>
                                    <span class="shrink-0">:</span>
                                    <span class="flex-1 min-w-0 truncate">{{ $tglLahir ?: '-' }}</span>
                                </div>
                                <span class="hidden mx-1 text-gray-400 md:inline">•</span>
                                <div class="flex items-baseline min-w-0 gap-3 basis-1/2">
                                    <span class="w-20 shrink-0">DPJP</span>
                                    <span class="shrink-0">:</span>
                                    <span class="flex-1 min-w-0 truncate">{{ $dpjp ?: '-' }}</span>
                                </div>
                            </div>

                            <!-- Ruang/Kamar -->
                            <div class="flex items-baseline gap-3 py-0.5">
                                <span class="w-32 shrink-0">Ruang/Kamar</span>
                                <span class="shrink-0">:</span>
                                <span class="flex-1 min-w-0 truncate">
                                    {{ trim($ruang . ' | ' . $kamar) ?: '-' }}
                                </span>
                            </div>

                            <!-- Masuk • Keluar (2 kolom fleksibel) -->
                            <div class="flex flex-col gap-2 md:flex-row md:items-baseline md:gap-8 py-0.5">
                                <div class="flex items-baseline min-w-0 gap-3 basis-1/2">
                                    <span class="w-32 shrink-0">Masuk</span>
                                    <span class="shrink-0">:</span>
                                    <span class="flex-1 min-w-0 truncate">{{ $tglMasuk ?: '-' }}</span>
                                </div>
                                <span class="hidden mx-1 text-gray-400 md:inline">•</span>
                                <div class="flex items-baseline min-w-0 gap-3 basis-1/2">
                                    <span class="w-20 shrink-0">Keluar</span>
                                    <span class="shrink-0">:</span>
                                    <span class="flex-1 min-w-0 truncate">
                                        {{ strtolower($statusPulang) === 'meninggal' ? "Meninggal, $tglKeluar" : ($tglKeluar ?: '-') }}
                                    </span>
                                </div>
                            </div>
                        </dl>

                    </div>

                </div>
            </div>

            {{-- SECTION TITLE --}}
            <div class="px-4 mt-4">
                <div
                    class="px-3 py-2 text-2xl font-semibold text-center uppercase border-t border-gray-900 rounded-t-md border-x bg-gray-50">
                    Ringkasan Pasien Pulang Rawat Inap
                </div>
            </div>

            {{-- ANAMNESIS --}}
            <div class="px-4">
                <div class="border-b border-gray-900 rounded-b-md border-x print-avoid-break">
                    <div class="grid gap-0 md:grid-cols-4">
                        <div class="p-3 text-sm font-semibold uppercase bg-gray-100">Anamnesis</div>
                        <div class="p-3 text-sm leading-6 md:col-span-3">
                            <span class="font-semibold">Keluhan Utama :</span>
                            {!! nl2br(e($keluhanUtama ?: '-')) !!}<br>
                            <span class="font-semibold">Riwayat Penyakit :</span>
                            <span class="whitespace-pre-line">{{ $riwayatPenyakit }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- PEMERIKSAAN FISIK --}}
            <div class="px-4 mt-4">
                <div
                    class="px-3 py-2 text-sm font-semibold uppercase border-t border-gray-900 rounded-t-md border-x bg-gray-50">
                    pemeriksaan fisik
                </div>
                <div class="px-3 py-2 text-sm border-b border-gray-900 rounded-b-md border-x">
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <span class="font-semibold">Keadaan umum :</span>
                            {{ $keadaanUmumAwal ?: '-' }}
                        </div>
                        <div>
                            <span class="font-semibold">Tanda Vital :</span>
                            TD {{ $td }} • Suhu {{ $suhu }} • Nadi {{ $nadi }} • RR
                            {{ $rr }} • {{ $gdaText }} • GCS {{ $gcsAwal }}
                        </div>
                    </div>
                    <div class="mt-2">
                        <span class="font-semibold">Pemeriksaan Fisik :</span><br>
                        {!! nl2br(e($pemeriksaanFisik ?: '-')) !!}
                    </div>
                </div>
            </div>

            {{-- PENUNJANG --}}
            <div class="px-4 mt-2">
                <div
                    class="px-3 py-2 text-sm font-semibold uppercase border-t border-gray-900 rounded-t-md border-x bg-gray-50">
                    pemeriksaan penunjang
                </div>
                <div class="px-3 py-2 text-sm border-b border-gray-900 rounded-b-md border-x">
                    <div><span class="font-semibold">1. LABORATORIUM :</span> {{ $labText }}</div>
                    <div><span class="font-semibold">2. RADIOLOGI :</span> {{ $radText }}</div>
                    <div><span class="font-semibold">3. LAIN-LAIN :</span> {{ $lainText }}</div>
                </div>
            </div>

            {{-- DIAGNOSIS & TINDAKAN + ICD (PERBAIKAN PENAMPILAN) --}}
            @php
                $dxUtamaText = $dxUtama !== '' ? $dxUtama : ($dxFree !== '' ? trim(explode("\n", $dxFree)[0]) : '-');
                $splitList = function ($text) {
                    return collect(preg_split('/\r\n|\r|\n|;|\|/', (string) $text))->map('trim')->filter()->values();
                };
            @endphp
            <div class="px-4 mt-2 text-sm font-semibold">
                <div class="px-3 py-2 uppercase border-t border-gray-900 rounded-t-md border-x bg-gray-50">
                    diagnosis & tindakan
                </div>
                <div class="px-3 py-2 border-b border-gray-900 rounded-b-md border-x">
                    <table class="w-full table-auto">
                        <tr>
                            <th class="w-48 px-2 py-1 text-left">DIAGNOSIS UTAMA</th>
                            <td class="px-2 py-1">{{ $dxUtamaText }}</td>
                            <th class="w-24 px-2 py-1 text-left">ICD-10</th>
                            <td class="w-32 px-2 py-1">{{ $dxUtamaICD ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th class="px-2 py-1 text-left align-top">DIAGNOSIS SEKUNDER :</th>
                            <td class="px-2 py-1 align-top">
                                <ol class="pl-6 leading-6 list-decimal">
                                    @foreach ($splitList($scDxFree) as $item)
                                        <li>{{ $item }}</li>
                                    @endforeach
                                    @forelse($dxSekunder as $dx)
                                    <li>{{ $dx }}</li> @empty <li>-</li>
                                    @endforelse
                                </ol>
                            </td>
                            <th class="px-2 py-1 text-left align-top">ICD-10</th>
                            <td class="px-2 py-1 align-top">
                                <ol class="pl-6 leading-6 list-decimal">
                                    @forelse($dxSekunderICD as $code)
                                    <li>{{ $code !== '' ? $code : '-' }}</li> @empty <li>-</li>
                                    @endforelse
                                </ol>
                            </td>
                        </tr>
                        <tr>
                            <th class="px-2 py-1 text-left align-top">TINDAKAN/PROSEDUR :</th>
                            <td class="px-2 py-1 align-top">
                                <ol class="pl-6 leading-6 list-decimal">
                                    @foreach ($splitList($procFree) as $p)
                                        <li>{{ $p }}</li>
                                    @endforeach
                                    @forelse($tindakanDesc as $t)
                                    <li>{{ $t }}</li> @empty <li>-</li>
                                    @endforelse
                                </ol>
                            </td>
                            <th class="px-2 py-1 text-left align-top">ICD-9-CM</th>
                            <td class="px-2 py-1 align-top">
                                <ol class="pl-6 leading-6 list-decimal">
                                    @forelse($tindakanCode as $c)
                                    <li>{{ $c !== '' ? $c : '-' }}</li> @empty <li>-</li>
                                    @endforelse
                                </ol>
                            </td>
                        </tr>
                        <tr>
                            <th class="px-2 py-1 text-left">DIET</th>
                            <td class="px-2 py-1" colspan="3">{{ $diet !== '' ? $diet : '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- KONDISI SAAT PULANG --}}
            <div class="px-4 mt-2">
                <div
                    class="px-3 py-2 text-sm font-semibold uppercase border-t border-gray-900 rounded-t-md border-x bg-gray-50">
                    kondisi saat pulang
                </div>
                <div class="px-3 py-2 text-sm border-b border-gray-900 rounded-b-md border-x">
                    <table class="w-full table-auto">
                        <tr>
                            <th class="w-48 px-2 py-1 text-left align-top">Keadaan umum</th>
                            <td class="px-2 py-1">{{ $lastSubjective }}</td>
                            <th class="w-24 px-2 py-1 text-left align-top">GCS</th>
                            <td class="px-2 py-1">{{ $gcsPulang }}</td>
                        </tr>
                        <tr>
                            <th class="px-2 py-1 text-left">Tanda vital</th>
                            <td class="px-2 py-1" colspan="3">
                                TD {{ $tdPulang }} • Suhu {{ $suhuPulang }} • Nadi {{ $nadiPulang }} • RR
                                {{ $rrPulang }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- CARA KELUAR RS (checkbox semantik seperti saran) --}}
            <div class="px-4 mt-2">
                <div
                    class="px-3 py-2 text-sm font-semibold uppercase border-t border-gray-900 rounded-t-md border-x bg-gray-50">
                    cara keluar rs
                </div>
                <div class="px-3 py-2 border-b border-gray-900 rounded-b-md border-x">
                    <div class="grid grid-cols-3 gap-2 text-sm md:grid-cols-6">
                        @php
                            $opts = [
                                ['code' => '371827001', 'label' => 'Pulang Sehat'],
                                ['code' => '266707007', 'label' => 'Pulang Atas Permintaan Sendiri'],
                                ['code' => '306206005', 'label' => 'Dirujuk'],
                                ['code' => '371828006', 'label' => 'Pulang Tanpa Perbaikan'],
                                ['code' => '419099009', 'label' => 'Meninggal'],
                                ['code' => '74964007', 'label' => 'Lain-lain'],
                            ];
                        @endphp
                        @foreach ($opts as $o)
                            @php $on = $labelTerpilihTindakLanjutKode === $o['code']; @endphp
                            <div class="flex items-center">
                                <span role="checkbox" aria-checked="{{ $aria($on) }}"
                                    class="inline-block w-3 h-3 mr-2 align-middle border border-black {{ $checked($on) }}"></span>
                                {{ $o['label'] }}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- TINDAK LANJUT & TERAPI PULANG --}}
            <div class="px-4 mt-2 mb-16 print-break">
                <div
                    class="px-3 py-2 text-sm font-semibold uppercase border-t border-gray-900 rounded-t-md border-x bg-gray-50">
                    tindak lanjut & terapi pulang
                </div>
                <div class="px-3 py-2 text-sm border-b border-gray-900 rounded-b-md border-x">
                    <div class="mb-3">
                        <span class="font-semibold">Kontrol Rawat Jalan :</span>
                        {{ $isKontrol ? $tglKontrol : '-' }}
                    </div>
                    <div class="mb-3">
                        <span class="font-semibold">Dirujuk ke :</span>
                        {{ $labelTerpilihTindakLanjutKode === '306206005' ? ($keteranganTindakLanjut ?: '-') : '-' }}
                    </div>
                    <div class="grid gap-4 md:grid-cols-4">
                        <div class="md:col-span-3">
                            <div class="font-semibold">Terapi Pulang</div>
                            <div
                                class="p-2 break-words whitespace-pre-line border border-gray-200 rounded-md bg-gray-50">
                                {!! nl2br(e($terapiPulang ?: '-')) !!}
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- TTD Pasien --}}
            <div class="px-4">
                <table class="w-full mt-3 border border-collapse border-black table-auto">
                    <tr>
                        <td class="px-2 py-10 align-bottom border border-black">
                            Tanda tangan pasien/keluarga,
                            <div class="mt-8 text-center">( ................................................ )</div>
                        </td>
                        <td class="px-2 py-10 align-bottom border border-black">
                            <div class="flex flex-col justify-end text-center md:col-span-1">
                                <div>Tulungagung,<br>{{ data_get($ri, 'exitDate', 'Tanggal') }}</div>
                                {{-- TTD Dokter opsional seperti RJ --}}
                                @php
                                    $dokterCode = data_get($ri, 'drId') ?? data_get($dataDaftarTxn, 'drId');
                                    $imgDokter = optional(App\Models\User::where('myuser_code', $dokterCode)->first())
                                        ->myuser_ttd_image;
                                @endphp
                                @if ($imgDokter)
                                    <img class="h-24 mx-auto my-1"
                                        src="{{ asset('storage/' . $imgDokter) }}?v={{ \Illuminate\Support\Str::random(6) }}"
                                        alt="TTD Dokter" />
                                @else
                                    <div
                                        class="flex items-center justify-center h-24 my-1 text-xs text-gray-400 border border-dashed rounded">
                                        (TTD Dokter belum tersedia)
                                    </div>
                                @endif
                                <div class="mt-1">
                                    <span class="text-xs italic text-gray-500">ttd</span><br>
                                    <span
                                        class="text-xs font-semibold">{{ $dpjp ?: 'Dokter Penanggung Jawab' }}</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
                <div class="text-center text-[10px] mt-2 font-semibold">
                    MOHON UNTUK TIDAK MENGGUNAKAN SINGKATAN DALAM PENULISAN DIAGNOSIS DAN TINDAKAN<br />SERTA DITULIS
                    DENGAN RAPI
                </div>
                <div class="text-right text-[10px]">2/2</div>
            </div>


        </div>

        {{-- Footer (seragam seperti RJ) --}}
        <div class="sticky bottom-0 flex justify-end px-4 py-3 border-t bg-gray-50 no-print">
            <div class="px-4">
                <livewire:cetak.cetak-ringkasan-pasien-pulang-r-i :riHdrNoRef="$dataDaftarTxn['riHdrNo'] ?? null"
                    :wire:key="($dataDaftarTxn['riHdrNo'] ?? 'ri').' - cetak-ringkasan-pulang-ri'" />
            </div>
        </div>
    </div>
</div>
