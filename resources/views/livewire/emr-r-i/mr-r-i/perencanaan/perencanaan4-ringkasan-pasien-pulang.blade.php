<div>
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
        $indikasiRawatInap = data_get($ri, 'pengkajianAwalPasienRawatInap.bagian1DataUmum.kondisiSaatMasuk', '');

        // Anamnesis
        $keluhanUtama = data_get($ri, 'pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.keluhanUtama', '');
        $riwayatPenyakit = collect([
            data_get(
                $ri,
                'pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.riwayatPenyakitOperasiCedera.pilihan',
                '',
            ),
            data_get($ri, 'pengkajianAwalPasienRawatInap.bagian2RiwayatPasien.riwayatKeluarga.pilihan', ''),
        ])
            ->filter()
            ->implode(', ');

        // Pemeriksaan fisik awal
        $tv = data_get($ri, 'pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.tandaVital', []);
        $td = trim(($tv['sistolik'] ?? '') . '/' . ($tv['distolik'] ?? ''));
        $suhu = $tv['suhu'] ?? '';
        $nadi = $tv['frekuensiNadi'] ?? '';
        $rr = $tv['frekuensiNafas'] ?? '';
        $gcsAwal = data_get($ri, 'pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.neurologi.gcs', ''); // fallback
        $pemeriksaanFisik = data_get(
            $ri,
            'pengkajianAwalPasienRawatInap.bagian4PemeriksaanFisik.pemeriksaanSistemOrgan',
            [],
        );
        // ringkas: tampilkan yang tidak "normal"
        $fisikRingkas = collect($pemeriksaanFisik ?? [])
            ->map(function ($v, $k) {
                $pil = $v['pilihan'] ?? '';
                $ket = $v['keterangan'] ?? '';
                return strtolower($pil) !== 'normal' && $pil !== ''
                    ? strtoupper($k) . ': ' . $pil . ($ket ? ' (' . $ket . ')' : '')
                    : null;
            })
            ->filter()
            ->implode('; ');
        if ($fisikRingkas === '') {
            $fisikRingkas = 'Dalam batas normal';
        }

        // Penunjang
        // ambil GDA awal & GDA terakhir observasi
        $gdaAwal = $tv['gda'] ?? '';
        $tandaObs = data_get($ri, 'observasi.observasiLanjutan.tandaVital', []);
        $gdaAkhir = collect($tandaObs)->pluck('gda')->filter(fn($v) => $v !== '' && $v !== '-' && $v !== '0')->last();
        $labText = trim(
            'GDA awal: ' .
                ($gdaAwal ?: '-') .
                '; GDA terakhir: ' .
                ($gdaAkhir ?: '-') .
                ' ' .
                ($ri['pengkajianDokter']['hasilPemeriksaanPenunjang']['laboratorium'] ?? ''),
        );

        // ==== RADIOLOGI ====
        $radText = trim(
            'Hasil radiologi: ' . ($ri['pengkajianDokter']['hasilPemeriksaanPenunjang']['radiologi'] ?? '-'),
        );

        // ==== PENUNJANG LAIN ====
        $lainText = trim(
            'Pemeriksaan penunjang lain: ' .
                ($ri['pengkajianDokter']['hasilPemeriksaanPenunjang']['penunjangLain'] ?? '-'),
        );

        // Terapi/Tindakan selama di RS (dari pemberian obat & cairan)
        //$obc = data_get($ri, 'observasi.obatDanCairan.pemberianObatDanCairan', []);
        //$terapiRS = collect($obc)
        //    ->map(function ($o) {
        //        $nama = $o['namaObatAtauJenisCairan'] ?? '';
        //        $d = $o['dosis'] ?? '';
        //        $r = $o['rute'] ?? '';
        //        return trim($nama . ' ' . $d . ($r ? ' (' . $r . ')' : ''));
        //    })
        //    ->filter()
        //    ->implode('; ');

        $cppt = data_get($ri, 'cppt', []);
        // ambil CPPT yang jelas-jelas dokter (profession berisi 'Dokter' atau nama petugas diawali 'dr.')
        $plansDokter = collect($cppt)
            ->filter(fn($row) => strcasecmp($row['profession'] ?? '', 'Dokter') === 0)
            ->pluck('soap.plan')
            ->filter()
            ->map(fn($p) => trim($p))
            ->unique()
            ->implode(' | ');

        $terapiRS = $plansDokter ?: '';

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
        $statusPulang = data_get($ri, 'perencanaan.tindakLanjut.statusPulang', '');
        $isKontrol = !empty(data_get($ri, 'kontrol.tglKontrol')) || $statusPulang === 'Pulang';
        $tglKontrol = data_get($ri, 'kontrol.tglKontrol', '');

        // Heuristik ringan (opsional, boleh dihapus): jika ada "PERAWATAN JENAZAH" di log, tandai meninggal
        $logDescs = collect($ri['AdministrasiRI']['userLogs'] ?? [])
            ->pluck('userLogDesc')
            ->implode(' | ');
        $isMeninggal =
            stripos($statusPulang, 'meninggal') !== false || stripos($logDescs, 'PERAWATAN JENAZAH') !== false;
    @endphp

    {{-- ======================= HEADER + NO. RM ======================= --}}
    <table class="w-full border-collapse">
        <tr>
            <td class="w-16 align-top border-0">
                {{-- <img src="{{ asset('logo.png') }}" class="h-12" /> --}}
                <x-application-logo class="block w-auto h-16 text-gray-800 fill-current dark:text-gray-200" />
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
            <td class="px-2 py-1 border border-black" colspan="5">{{ $fisikRingkas }}</td>
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
            <td class="px-2 py-8 border border-black" colspan="6">{{ $terapiRS }}</td>
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

    <div class="mt-1 text-xs italic text-right">Bersambung ke hal 2</div>
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

        $eresepLastHdr = $eresepHdrs
            ->sortBy(function ($h) {
                $d = data_get($h, 'resepDate');
                try {
                    return $d ? Carbon::createFromFormat('d/m/Y H:i:s', $d)->timestamp : 0;
                } catch (\Throwable $e) {
                    return 0;
                }
            })
            ->last();

        // --- Non-racikan seperti sebelumnya ---
        $nonRacik = collect(data_get($eresepLastHdr, 'eresep', []))->map(function ($e) {
            $nama = trim((string) ($e['productName'] ?? ''));
            $jumlah = trim((string) ($e['qty'] ?? ''));

            $x = trim((string) ($e['signaX'] ?? ''));
            $h = trim((string) ($e['signaHari'] ?? ''));
            $dosis = trim($x . ($x !== '' && $h !== '' ? ' x ' : '') . $h);

            $lower = strtolower($nama);
            $cara = '';
            if (str_contains($lower, 'inj')) {
                $cara = 'inj';
            } elseif (str_contains($lower, 'inf') || str_contains($lower, 'infus')) {
                $cara = 'inf';
            } elseif (
                str_contains($lower, 'tab') ||
                str_contains($lower, 'capsul') ||
                str_contains($lower, 'cap') ||
                str_contains($lower, 'syr')
            ) {
                $cara = 'po';
            }

            return [
                'nama' => $nama,
                'jumlah' => $jumlah,
                'dosis' => $dosis,
                'cara' => $cara,
            ];
        });

        // --- Racikan: pakai field yang kamu simpan di array racikan ---
        $racik = collect(data_get($eresepLastHdr, 'eresepRacikan', []))->map(function ($r) {
            $noRacik = trim((string) ($r['noRacikan'] ?? ''));
            $nama = trim((string) ($r['productName'] ?? 'Racikan'));
            $jumlah = trim((string) ($r['qty'] ?? ''));

            // dosis prioritaskan field 'dosis' dari racikan; kalau kosong, format dari signa
            $dosisDok = trim((string) ($r['dosis'] ?? ''));
            $x = trim((string) ($r['signaX'] ?? ''));
            $h = trim((string) ($r['signaHari'] ?? ''));
            $dosis = $dosisDok !== '' ? $dosisDok : trim($x . ($x !== '' && $h !== '' ? ' x ' : '') . $h);

            // cara pemberian dari 'sedia' atau nama
            $sedia = strtolower(trim((string) ($r['sedia'] ?? '')));
            $lower = strtolower($nama . ' ' . $sedia);
            $cara = 'po';
            if (str_contains($lower, 'inj')) {
                $cara = 'inj';
            } elseif (str_contains($lower, 'inf')) {
                $cara = 'inf';
            } elseif (str_contains($lower, 'salep') || str_contains($lower, 'topikal')) {
                $cara = 'top';
            }
            // (default tetap 'po')

            // tambahkan label racikan biar jelas
            $namaTampil = ($noRacik !== '' ? "Racikan #{$noRacik} - " : 'Racikan - ') . $nama;

            return [
                'nama' => $namaTampil,
                'jumlah' => $jumlah,
                'dosis' => $dosis,
                'cara' => $cara,
            ];
        });

        // --- Gabungkan untuk ditampilkan di tabel TERAPI PULANG ---
        $obatPulang = $nonRacik->merge($racik)->values()->all();
    @endphp

    {{-- KONDISI SAAT PULANG --}}
    <table class="w-full mt-2 border border-collapse border-black table-auto">
        <tr class="font-semibold bg-gray-100">
            <th colspan="4" class="px-2 py-1 text-left">KONDISI SAAT PULANGX</th>
        </tr>
        <tr>
            <th class="w-48 px-2 py-1 text-left align-top border border-black">Keadaan umum</th>
            <td class="px-2 py-1 border border-black">
                {{ $isMeninggal ? 'Meninggal' : data_get($ri, 'pengkajianAwalPasienRawatInap.bagian3PsikososialDanEkonomi.aktivitas.pilihan', '') }}
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
                <span class="inline-block w-3 h-3 mr-2 align-middle border border-black">
                    @if ($statusPulang === 'Pulang')
                        ✔
                    @endif
                </span> Pulang Atas persetujuan
            </td>
            <td class="px-2 py-1 border border-black">
                <span class="inline-block w-3 h-3 mr-2 align-middle border border-black">
                    @if ($statusPulang === 'APS')
                        ✔
                    @endif
                </span> Pulang Atas Permintaan Sendiri
            </td>
            <td class="px-2 py-1 border border-black">
                <span class="inline-block w-3 h-3 mr-2 align-middle border border-black">
                    @if ($statusPulang === 'Dirujuk')
                        ✔
                    @endif
                </span> Dirujuk
            </td>
            <td class="px-2 py-1 border border-black">Kabur</td>
            <td class="px-2 py-1 border border-black">
                <span class="inline-block w-3 h-3 mr-2 align-middle border border-black">
                    @if ($isMeninggal)
                        ✔
                    @endif
                </span> Meninggal
            </td>
            <td class="px-2 py-1 border border-black">&nbsp;</td>
        </tr>
    </table>

    {{-- TINDAK LANJUT --}}
    <table class="w-full mt-2 border border-collapse border-black table-auto">
        <tr class="font-semibold bg-gray-100">
            <th colspan="6" class="px-2 py-1 text-left">TINDAK LANJUT</th>
        </tr>
        <tr>
            <td class="w-1/2 px-2 py-1 border border-black">
                <span class="inline-block w-3 h-3 mr-2 align-middle border border-black">
                    @if ($isKontrol)
                        ✔
                    @endif
                </span>
                Kontrol rawat jalan, tanggal
                <span class="inline-block w-56 align-middle border-b border-black border-dotted">
                    &nbsp;{{ $tglKontrol }}&nbsp;
                </span>
            </td>
            <td class="w-1/2 px-2 py-1 border border-black">
                <span class="inline-block w-3 h-3 mr-2 align-middle border border-black"></span>
                <span class="inline-block w-56 border-b border-black border-dotted">&nbsp;&nbsp;</span>
            </td>
        </tr>
        <tr>
            <td class="px-2 py-1 border border-black">
                <span class="inline-block w-3 h-3 mr-2 align-middle border border-black">
                    @if ($statusPulang === 'Dirujuk')
                        ✔
                    @endif
                </span>
                Dirujuk ke
                <span class="inline-block w-64 border-b border-black border-dotted">&nbsp;&nbsp;</span>
            </td>
            <td class="px-2 py-1 border border-black">
                <span class="inline-block w-3 h-3 mr-2 align-middle border border-black"></span>
                <span class="inline-block w-56 border-b border-black border-dotted">&nbsp;&nbsp;</span>
            </td>
        </tr>
    </table>

    {{-- TERAPI PULANG --}}
    <table class="w-full mt-2 border border-collapse border-black table-auto">
        <tr class="font-semibold bg-gray-100">
            <th colspan="4" class="px-2 py-1 text-left">TERAPI PULANG</th>
        </tr>

        @php
            $obatPulang = (array) data_get($ri, 'obatPulang', []);
            // Ambil no & tgl resep dari item pertama (karena semua dari HDR yang sama)
            $noResep = $obatPulang[0]['resepNo'] ?? '-';
            $tglResep = $obatPulang[0]['resepDate'] ?? '-';
        @endphp

        {{-- Baris info resep (bukan looping) --}}
        <tr>
            <th class="px-2 py-1 text-left border border-black">No Resep</th>
            <td class="px-2 py-1 border border-black">{{ $noResep }}</td>
            <th class="px-2 py-1 text-left border border-black">Tgl Resep</th>
            <td class="px-2 py-1 border border-black">{{ $tglResep }}</td>
        </tr>


        <tr class="font-semibold bg-gray-100">
            <th class="px-2 py-1 text-left border border-black">Nama Obat</th>
            <th class="px-2 py-1 text-left border border-black">Jumlah</th>
            <th class="px-2 py-1 text-left border border-black">Dosis</th>
            <th class="px-2 py-1 text-left border border-black">Cara Pemberian</th>
        </tr>



        @if (!empty($obatPulang))
            @foreach ($obatPulang as $o)
                <tr>
                    <td class="px-2 py-1 border border-black">{{ $o['nama'] ?? '' }}</td>
                    <td class="px-2 py-1 border border-black">{{ $o['jumlah'] ?? '' }}</td>
                    <td class="px-2 py-1 border border-black">{{ $o['dosis'] ?? '' }}</td>
                    <td class="px-2 py-1 border border-black">{{ $o['cara'] ?? '' }}</td>
                </tr>
            @endforeach
        @else
            @for ($i = 0; $i < 2; $i++)
                <tr>
                    <td class="px-2 py-3 border border-black">&nbsp;</td>
                    <td class="px-2 py-3 border border-black">&nbsp;</td>
                    <td class="px-2 py-3 border border-black">&nbsp;</td>
                    <td class="px-2 py-3 border border-black">&nbsp;</td>
                </tr>
            @endfor
        @endif
    </table>

    {{-- TTD --}}
    <table class="w-full mt-3 border border-collapse border-black table-auto">
        <tr>
            <td class="px-2 py-10 align-bottom border border-black">
                Tanda tangan pasien/keluarga,
                <div class="mt-8">( ................................................ )</div>
            </td>
            <td class="px-2 py-10 align-bottom border border-black">
                Tulungagung,
                <span class="inline-block align-bottom border-b border-black border-dotted w-72">&nbsp;&nbsp;</span>
                <div class="mt-8 text-center">
                    ( ................................................ )<br />Tanda tangan dan nama dokter
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
