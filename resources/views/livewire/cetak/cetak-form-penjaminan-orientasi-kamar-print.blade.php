<x-application-supergrafis-pdf-a4 title="FORM DATA PENJAMINAN BIAYA & ORIENTASI KAMAR PASIEN">
    <div>
        @php
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
        DATA PASIEN (RINGKAS) + PEMBUAT
        =========================================================== --}}
        <div class="mb-1 font-bold">Data Pasien</div>
        <table class="w-full mb-2 border border-collapse border-black">
            <tr>
                <td class="w-32 border border-black">Nama Pasien</td>
                <td class="w-40 border border-black">: {{ $namaPasien }}</td>

                <td class="border border-black">Jenis Kelamin</td>
                <td class="border border-black">: {{ $jkPasien }}</td>
            </tr>

            <tr>
                <td class="border border-black">No. Rekam Medis</td>
                <td class="border border-black">: {{ $rm }}</td>

                <td class="border border-black">Tanggal Lahir</td>
                <td class="border border-black">: {{ $tglLahir }}</td>
            </tr>
        </table>



        <div class="mb-1 font-bold">Saya yang bertanda tangan di bawah ini:</div>
        <table class="w-full border border-collapse border-black ">
            <tr>
                <td class="w-32 border border-black">Nama</td>
                <td class="w-40 border border-black">: {{ $pembuat }}</td>

                <td class="w-24 border border-black">Umur</td>
                <td class="border border-black">: {{ $umurPembuat }} tahun</td>

                <td class="border border-black">Jenis Kelamin</td>
                <td class="border border-black">: {{ $jkPembuat }}</td>
            </tr>

            <tr>
                <td class="align-top border border-black">Alamat</td>
                <td colspan="3" class="border border-black">
                    : {{ $alamatPembuat }}
                </td>
                <td class="border border-black"></td>
                <td class="border border-black"></td>
            </tr>
        </table>



        <p class="mt-1 ">
            Terhadap diri <span class="font-bold">{{ $hubungan }}</span> dari pasien.
        </p>

        {{-- ==========================================================
        PERNYATAAN PENJAMINAN
        =========================================================== --}}
        <p class="mt-1 ">
            Dengan ini menyatakan bahwa saya:
        </p>

        @php
            // Gabungkan dengan nama asuransi kalau tipenya Asuransi Lain
            $jenisPenjaminDisplay = $jenisPenjamin;

            if (!empty($jenisPenjamin) && stripos($jenisPenjamin, 'asuransi') !== false && !empty($asuransiLain)) {
                $jenisPenjaminDisplay .= ' - ' . $asuransiLain;
            }
        @endphp

        <p class="mt-1 text-justify">
            Memiliki kartu penjaminan berupa
            <span class="font-bold underline">
                {{ $jenisPenjaminDisplay ?: '......................................................' }}
            </span>
            untuk dipergunakan sebagai penjaminan biaya pelayanan medis di Rumah Sakit Islam Madinah.
        </p>

        {{-- ==========================================================
        Ketentuan Penjaminan BPJS Kesehatan (HANYA JIKA JENIS PENJAMIN = BPJS_KESEHATAN)
        =========================================================== --}}
        @if (($jenisPenjamin ?? '') === 'BPJS_KESEHATAN')
            <div class="mt-3">
                <div class="font-bold underline">
                    Ketentuan Penjaminan BPJS Kesehatan
                </div>

                <p class="mt-1 text-justify">
                    BPJS Kesehatan hanya menjamin pelayanan kesehatan peserta JKN yang sesuai dengan ketentuan yang
                    berlaku.
                    Pelayanan yang tidak sesuai dengan ketentuan tersebut tidak menjadi tanggungan BPJS Kesehatan,
                    antara
                    lain:
                </p>

                <ol class="mt-2 ml-4 list-decimal">
                    <li class="mb-1">
                        Pelayanan di luar ketentuan/prosedur yang diatur dalam Program Jaminan Kesehatan Nasional (JKN)
                    </li>

                    <li class="mb-1">
                        Pelayanan yang tidak sesuai dengan ketentuan:
                        <div class="mt-1 ml-4">
                            <div>a. Permintaan rawat jalan dan/atau rawat inap atas permintaan sendiri (APS)</div>
                            <div>
                                b. Penolakan/tidak mematuhi rencana terapi yang direkomendasikan yang sudah disetujui
                                oleh
                                pasien
                                sampai dengan direkomendasikan diperbolehkan pulang oleh Dokter Penanggung Jawab Pasien
                                (meminta pulang atas permintaan sendiri) dan menerima segala konsekuensi atas keputusan
                                pribadinya
                                ketika menolak rencana terapi
                            </div>
                        </div>
                    </li>

                    <li class="mb-1">
                        Pelayanan di luar lingkup penjaminan yang tertuang dalam Perjanjian Kerja Sama
                    </li>

                    <li class="mb-1">
                        Pelayanan homecare di rumah (tidak diatur dalam lingkup yang dijamin dalam Perjanjian Kerja Sama
                        dengan
                        Fasilitas Kesehatan Rujukan Tingkat Lanjutan)
                    </li>

                    <li class="mb-1">
                        Pelayanan kasus Kecelakaan Lalu Lintas dengan kondisi tidak sesuai ketentuan, misalnya: tidak
                        mengurus LP
                        (damai), KLL karena intoksikasi miras
                    </li>

                    <li class="mb-1">
                        Pelayanan atas instruksi dari Fasilitas Kesehatan yang tidak bekerja sama dengan BPJS Kesehatan
                        (Praktek
                        Pribadi) maupun Fasilitas Kesehatan yang bekerja sama agar dilakukan assessment ulang di FKTP.
                        Apabila
                        sesuai dengan kebutuhan medis dan memiliki indikasi untuk dirujuk maka dapat dilakukan rujukan
                        sesuai
                        ketentuan prosedur yang berlaku dalam Program Jaminan Kesehatan Nasional (JKN)
                    </li>

                    <li class="mb-1">
                        Apabila peserta memilih/menjalani pelayanan yang termasuk kategori tidak sesuai ketentuan di
                        atas,
                        maka
                        peserta memahami dan menyetujui bahwa biaya pelayanan tersebut menjadi tanggungan pribadi/pihak
                        keluarga.
                    </li>
                </ol>
            </div>
        @endif





        {{-- ==========================================================
        ORIENTASI KAMAR PASIEN
        =========================================================== --}}
        <div class="mt-4 mb-1 font-bold">
            ORIENTASI KAMAR PASIEN
        </div>

        <table class="">
            <tr>
                <td class="w-40">Kelas Kamar</td>
                <td>: {{ $kelasLabel }}</td>
            </tr>
            <tr>
                <td class="align-top">Fasilitas</td>
                <td class="align-top">
                    :
                    @if (!empty($fasilitas))
                        @php
                            // 3 item per kolom (vertikal), sisanya lanjut kolom kanan
                            $chunks = array_chunk($fasilitas, 3);
                        @endphp

                        <table class="ml-3" cellpadding="0" cellspacing="0">
                            <tr>
                                <td class="align-top">
                                    <ul class="pl-4 m-0 list-disc">
                                        @foreach ($chunks[0] ?? [] as $fas)
                                            <li class="leading-snug">{{ $fas }}</li>
                                        @endforeach
                                    </ul>
                                </td>

                                <td class="pl-8 align-top">
                                    <ul class="pl-4 m-0 list-disc">
                                        @foreach ($chunks[1] ?? [] as $fas)
                                            <li class="leading-snug">{{ $fas }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                            </tr>
                        </table>
                    @else
                        ............................................................
                    @endif
                </td>
            </tr>

        </table>

        <p class="mt-2 ">
            Pasien / keluarga telah menerima penjelasan mengenai tarif dan fasilitas kamar sebagaimana tersebut di atas.
        </p>

        {{-- ==========================================================
        TANDA TANGAN
        =========================================================== --}}
        <table class="w-full mt-8 " cellpadding="0" cellspacing="0">
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
    </div>

</x-application-supergrafis-pdf-a4>
