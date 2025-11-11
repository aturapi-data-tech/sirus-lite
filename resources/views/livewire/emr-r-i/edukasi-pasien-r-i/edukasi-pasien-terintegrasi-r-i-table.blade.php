<tbody class="bg-white">
    @php
        use Illuminate\Support\Str;

        $list = $dataDaftarRi['edukasiPasienTerintegrasi'] ?? [];
    @endphp

    @forelse ($list as $key => $row)
        @php
            $form = $row['form'] ?? [];
            $tgl = $form['tglEdukasi'] ?? '-';

            $petugasName = data_get($form, 'pemberiInformasi.petugasName', '-');
            $petugasCode = data_get($form, 'pemberiInformasi.petugasCode', '-');

            $pasienNama = data_get($form, 'ttd.pasienKeluargaNama', '-');
            $pemberiEdu = data_get($form, 'ttd.pemberiEdukasiNama', '-');
            $sig = data_get($form, 'ttd.pasienKeluargaTTD');

            $tujuan = data_get($form, 'tujuan.opsi', []);
            $tujuanLain = data_get($form, 'tujuan.lainnya');

            $kebutuhan = data_get($form, 'kebutuhan.opsi', []);
            $kebLain = data_get($form, 'kebutuhan.lainnya');

            $metode = data_get($form, 'metodeMedia.opsi', []);
            $metodeLain = data_get($form, 'metodeMedia.lainnya');

            $hasil = data_get($form, 'hasil', []);
            $tlTgl = data_get($form, 'tindakLanjut.edukasiLanjutanTanggal');
            $tlRujuk = data_get($form, 'tindakLanjut.dirujukKe', []);
            $tlSkip = data_get($form, 'tindakLanjut.tidakPerluTL', false);

            // badges chips
            $renderChips = function ($items, $labelMap = null) {
                if (!is_array($items) || empty($items)) {
                    return '-';
                }
                $html = '';
                foreach ($items as $it) {
                    $txt = $labelMap[$it] ?? ucfirst(str_replace(['_', '-'], ' ', $it));
                    $html .=
                        '<span class="inline-block px-2 py-0.5 mr-1 mb-1 rounded-full bg-blue-100 text-blue-800">' .
                        $txt .
                        '</span>';
                }
                return $html;
            };
            $badgeHasil = function ($key, $valYa) {
                $isYa = in_array($valYa, [true, 1, '1'], true);
                $isTidak = in_array($valYa, [false, 0, '0'], true);
                if ($key === 'paham' && $isTidak) {
                    return 'inline-block px-2 py-0.5 mr-1 rounded border border-red-200 bg-red-100 text-red-800';
                }
                if ($isYa) {
                    return 'inline-block px-2 py-0.5 mr-1 rounded bg-green-100 text-green-800';
                }
                if ($isTidak) {
                    return 'inline-block px-2 py-0.5 mr-1 rounded bg-gray-200 text-gray-700';
                }
                return 'inline-block px-2 py-0.5 mr-1 rounded bg-gray-100 text-gray-600';
            };

            $mapTujuan = [
                'penyakit' => 'Pemahaman penyakit',
                'obat' => 'Penggunaan obat',
                'nutrisi' => 'Nutrisi/diet',
                'aktivitas' => 'Aktivitas',
                'perawatanRumah' => 'Perawatan di rumah',
                'pencegahan' => 'Pencegahan komplikasi',
                'lainnya' => 'Lainnya',
            ];
            $mapKebutuhan = [
                'penyakitHasil' => 'Penyakit & hasil',
                'prosedur' => 'Prosedur',
                'rencanaAsuhan' => 'Rencana asuhan',
                'obatEfek' => 'Obat & efek',
                'cuciTangan' => 'Cuci tangan',
                'alatRumah' => 'Alat di rumah',
                'warningSign' => 'Tanda bahaya',
                'lainnya' => 'Lainnya',
            ];
            $mapMetode = [
                'lisan' => 'Lisan',
                'demonstrasi' => 'Demonstrasi',
                'leaflet' => 'Leaflet',
                'video' => 'Video',
                'poster' => 'Poster',
                'lainnya' => 'Lainnya',
            ];

            // flags risiko
            $hambatanEmo = data_get($form, 'evaluasiAwal.hambatanEmosional.ada');
            $ketEmo = trim((string) data_get($form, 'evaluasiAwal.hambatanEmosional.keterangan', ''));
            $hambatanFk = data_get($form, 'evaluasiAwal.keterbatasanFisikKognitif.ada');
            $ketFk = trim((string) data_get($form, 'evaluasiAwal.keterbatasanFisikKognitif.keterangan', ''));

            $isEmo = in_array($hambatanEmo, [true, 1, '1'], true);
            $isFk = in_array($hambatanFk, [true, 1, '1'], true);
            $isPahamTidak = in_array(data_get($hasil, 'paham.ya'), [false, 0, '0'], true);
            $alertRow = $isPahamTidak || $isEmo || $isFk;
        @endphp

        {{-- BARIS UTAMA: 2 kolom saja, kolom kanan di-colspan 3 --}}
        <tr class="border-b group dark:border-gray-700 {{ $alertRow ? 'bg-red-50 border-l-4 border-l-red-400' : '' }}">
            {{-- Tanggal & identitas --}}
            <td class="px-3 py-3 align-top group-hover:bg-gray-100 whitespace-nowrap">
                <div class="text-sm text-primary"><strong>Tanggal:</strong> {{ $tgl }}</div>
                <div class="text-sm">
                    <strong>Petugas:</strong> <span class="font-semibold text-gray-900">{{ $petugasName }}</span><br>
                    {{-- <strong>Kode:</strong> {{ $petugasCode }}<br> --}}
                    <strong>Nama Pasien/Keluarga:</strong> <span
                        class="font-semibold text-gray-900">{{ $pasienNama }}</span><br>
                    <strong>Pemberi Edukasi:</strong> <span
                        class="font-semibold text-gray-900">{{ $pemberiEdu ?: '-' }}</span><br>
                    @if (!empty($row['created_at']))
                        <span class="text-xs text-gray-500">Created: {{ $row['created_at'] }}</span>
                    @endif
                </div>
            </td>

            {{-- Ringkasan (ambil 3 kolom — ganti Action jadi digabung ke baris bawah) --}}
            <td class="px-3 py-3 whitespace-normal align-top group-hover:bg-gray-100" colspan="3">
                <div class="grid grid-cols-6 gap-3">
                    {{-- Tujuan --}}
                    <div>
                        <div class="mb-1 text-xs font-semibold text-gray-600">Tujuan</div>
                        <div class="text-sm">{!! $renderChips($tujuan, $mapTujuan) !!}</div>
                        @if (in_array('lainnya', $tujuan ?? []) && !empty($tujuanLain))
                            <div class="mt-1 text-xs text-gray-600"><strong>Lainnya:</strong> {{ e($tujuanLain) }}</div>
                        @endif
                    </div>

                    {{-- Kebutuhan --}}
                    <div>
                        <div class="mb-1 text-xs font-semibold text-gray-600">Kebutuhan</div>
                        <div class="text-sm">{!! $renderChips($kebutuhan, $mapKebutuhan) !!}</div>
                        @if (in_array('lainnya', $kebutuhan ?? []) && !empty($kebLain))
                            <div class="mt-1 text-xs text-gray-600"><strong>Lainnya:</strong> {{ e($kebLain) }}</div>
                        @endif
                    </div>

                    {{-- Metode & Media --}}
                    <div>
                        <div class="mb-1 text-xs font-semibold text-gray-600">Metode & Media</div>
                        <div class="text-sm">{!! $renderChips($metode, $mapMetode) !!}</div>
                        @if (in_array('lainnya', $metode ?? []) && !empty($metodeLain))
                            <div class="mt-1 text-xs text-gray-600"><strong>Lainnya:</strong> {{ e($metodeLain) }}</div>
                        @endif
                    </div>

                    {{-- Hasil Edukasi --}}
                    <div>
                        <div class="mb-1 text-xs font-semibold text-gray-600">
                            Hasil Edukasi
                            @if ($alertRow)
                                <span
                                    class="ml-1 inline-flex items-center px-2 py-0.5 rounded text-red-800 bg-red-100 align-middle">⚠️
                                    Risiko</span>
                            @endif
                        </div>
                        @php
                            $hasilTampil = [];
                            $labelHasil = [
                                'paham' => 'Paham',
                                'mampuMengulang' => 'Mampu mengulang',
                                'tunjukkanSkill' => 'Menunjukkan skill',
                                'sesuaiNilai' => 'Sesuai nilai',
                                'perluEdukasiUlang' => 'Perlu edukasi ulang',
                            ];
                            foreach ($labelHasil as $hk => $hlbl) {
                                $rowH = $hasil[$hk] ?? null;
                                if (is_array($rowH) && array_key_exists('ya', $rowH)) {
                                    $val = $rowH['ya'];
                                    $status = in_array($val, [true, 1, '1'], true)
                                        ? 'Ya'
                                        : (in_array($val, [false, 0, '0'], true)
                                            ? 'Tidak'
                                            : '-');
                                    $ket = trim((string) ($rowH['keterangan'] ?? ''));
                                    $cls = $badgeHasil($hk, $val);
                                    $hasilTampil[] =
                                        '<div class="mb-1"><span class="' .
                                        $cls .
                                        '">' .
                                        $hlbl .
                                        ': ' .
                                        $status .
                                        '</span>' .
                                        ($ket ? '<span class="text-xs text-gray-600"> ' . e($ket) . '</span>' : '') .
                                        '</div>';
                                }
                            }
                        @endphp
                        <div class="text-sm">{!! $hasilTampil ? implode('', $hasilTampil) : '-' !!}</div>
                    </div>


                    {{-- Tindak Lanjut --}}
                    <div class="mt-2">
                        <div class="mb-1 text-xs font-semibold text-gray-600">Tindak Lanjut</div>
                        <div class="text-sm">
                            <div><strong>Tanggal:</strong> {{ $tlTgl ?: '-' }}</div>
                            <div><strong>Rujuk:</strong>
                                @if (!empty($tlRujuk))
                                    @foreach ($tlRujuk as $r)
                                        <span
                                            class="inline-block px-2 py-0.5 mr-1 mb-1 rounded bg-purple-100 text-purple-800">{{ ucfirst($r) }}</span>
                                    @endforeach
                                @else
                                    -
                                @endif
                            </div>
                            <div><strong>Perlu TL?</strong>
                                {{ $tlSkip ? 'Tidak (dinyatakan tidak perlu)' : 'Ya/menurut kebutuhan' }}</div>
                        </div>
                    </div>


                    {{-- Flag Risiko --}}
                    @if ($isEmo || $isFk)
                        <div class="mt-3">
                            <div class="mb-1 text-xs font-semibold text-gray-600">Flag Risiko</div>
                            <div class="text-sm">
                                @if ($isEmo)
                                    <div class="mb-1">
                                        <span
                                            class="inline-block px-2 py-0.5 mr-1 rounded bg-red-100 text-red-800">Hambatan
                                            emosional/motivasi</span>
                                        @if ($ketEmo)
                                            <span class="text-xs text-gray-600">{{ e($ketEmo) }}</span>
                                        @endif
                                    </div>
                                @endif
                                @if ($isFk)
                                    <div class="mb-1">
                                        <span
                                            class="inline-block px-2 py-0.5 mr-1 rounded bg-red-100 text-red-800">Keterbatasan
                                            fisik/kognitif</span>
                                        @if ($ketFk)
                                            <span class="text-xs text-gray-600">{{ e($ketFk) }}</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Kartu TTD + Nama (rapi & responsif) --}}
                    <div class="flex flex-col items-center gap-2 text-center">
                        @if (!empty($sig))
                            @php
                                $isSvg =
                                    is_string($sig) &&
                                    \Illuminate\Support\Str::startsWith($sig, ['<svg', '<?xml', 'data:image/svg']);
                                $isImg = is_string($sig) && \Illuminate\Support\Str::startsWith($sig, 'data:image/');
                            @endphp

                            <div class="w-40 max-w-full">
                                <div
                                    class="flex items-center justify-center h-20 p-1 overflow-hidden bg-white border border-gray-300 rounded-md shadow-sm">
                                    <div class="w-full h-full">
                                        @if ($isSvg && \Illuminate\Support\Str::startsWith($sig, '<svg'))
                                            {!! str_replace(
                                                ['<svg ', ' width="300"', ' height="150"'],
                                                ['<svg class="object-contain w-full h-full" ', ' width="100%"', ' height="100%"'],
                                                $sig,
                                            ) !!}
                                        @elseif ($isImg)
                                            <img class="object-contain w-full h-full" src="{{ $sig }}"
                                                alt="Tanda Tangan">
                                        @else
                                            <span class="text-xs text-gray-500">TTD tidak dapat ditampilkan</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Caption kecil --}}
                                <div class="mt-1 text-[11px] leading-tight text-gray-500">Tanda Tangan</div>
                            </div>
                        @else
                            {{-- Placeholder bila belum ada TTD --}}
                            <div class="w-40 max-w-full">
                                <div
                                    class="flex items-center justify-center h-20 p-1 text-gray-400 border-2 border-dashed rounded-md">
                                    <span class="text-xs">Belum ada TTD</span>
                                </div>
                            </div>
                        @endif

                        {{-- Nama tampil tebal & terang di bawah TTD --}}
                        <div class="mt-1">
                            <div class="text-[11px] uppercase tracking-wide text-gray-500">Nama Pasien/Keluarga</div>
                            <div class="text-base font-semibold leading-snug text-gray-900">
                                {{ $pasienNama ?: '-' }}
                            </div>
                        </div>
                    </div>


                </div>






            </td>
        </tr>

        {{-- BARIS BAWAH: tombol kiri, TTD kanan --}}
        @php $id = $row['id'] ?? null; @endphp
        <tr class="{{ $alertRow ? 'bg-red-50' : '' }}">
            <td colspan="4" class="px-3 pt-2 pb-4">
                <div class="flex items-start justify-between gap-4">
                    {{-- Tombol kiri --}}
                    <div class="flex flex-wrap gap-2">
                        @if ($id)
                            <x-primary-button class="inline-flex items-center gap-1" wire:loading.remove
                                wire:click.stop.prevent="cetakEdukasiPasienById('{{ $id }}')"
                                wire:loading.attr="disabled" wire:target="cetakEdukasiPasienById">
                                <svg class="w-5 h-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 9V2h12v7M6 18h12v4H6v-4zM6 14h12a2 2 0 002-2V9H4v3a2 2 0 002 2z" />
                                </svg>
                                Cetak
                            </x-primary-button>
                            <div wire:loading wire:target="cetakEdukasiPasienById('{{ $id }}')"><x-loading />
                            </div>

                            <x-red-button class="inline-flex items-center gap-1" wire:loading.remove
                                wire:click.stop.prevent="removeEdukasiPasienById('{{ $id }}')"
                                wire:loading.attr="disabled" wire:target="removeEdukasiPasienById">
                                <svg class="w-5 h-5 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                    fill="currentColor" viewBox="0 0 18 20">
                                    <path
                                        d="M17 4h-4V2a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v2H1a1 1 0 0 0 0 2h1v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6h1a1 1 0 1 0 0-2ZM7 2h4v2H7V2Zm1 14a1 1 0 1 1-2 0V8a1 1 0 0 1 2 0v8Zm4 0a1 1 0 0 1-2 0V8a1 1 0 0 1 2 0v8Z" />
                                </svg>
                                Hapus
                            </x-red-button>
                        @else
                            <x-red-button class="inline-flex items-center gap-1" wire:loading.remove
                                wire:click.stop.prevent="removeEdukasiPasien({{ $key }})">
                                <svg class="w-5 h-5 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                    fill="currentColor" viewBox="0 0 18 20">
                                    <path
                                        d="M17 4h-4V2a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v2H1a1 1 0 1 0 0-2ZM7 2h4v2H7V2Zm1 14a1 1 0 1 1-2 0V8a1 1 0 0 1 2 0v8Zm4 0a1 1 0 0 1-2 0V8a1 1 0 0 1 2 0v8Z" />
                                </svg>
                                Hapus
                            </x-red-button>
                        @endif
                    </div>


                </div>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="4" class="px-4 py-3 text-center text-gray-500">Tidak ada data edukasi pasien.</td>
        </tr>
    @endforelse
</tbody>
