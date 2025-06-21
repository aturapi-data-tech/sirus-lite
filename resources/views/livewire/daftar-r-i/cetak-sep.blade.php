<!DOCTYPE html>
<html>

<head>
    <style>
        @page {
            size: 28cm 15cm;
            /* ± landscape A4 */
            margin: 8px;
        }
    </style>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">


    <link href='build/assets/sirus.css' rel="stylesheet">
</head>

<body>

    {{-- HEADER --}}
    <table class="w-full">
        <tr>
            <td class="w-44">
                <img src="{{ public_path('bpjslogo.png') }}" class="object-fill h-8">
            </td>
            <td>
                <p class="ml-2 text-base font-bold">SURAT ELEGIBILITAS PESERTA</p>
                <p class="ml-2 text-base font-bold">MADINAH (JST)</p>
            </td>
        </tr>
    </table>

    {{-- DETAIL – 2 kolom --}}
    <table class="w-full mt-2 text-sm">
        <tr>
            {{-- KOLOM KIRI --}}
            <td class="w-1/2 pr-4">
                <table class="w-full">
                    <tr>
                        <td>No.SEP</td>
                        <td>: {{ $data['noSep'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Tgl.SEP</td>
                        <td>: {{ $data['tglSep'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>No.Kartu</td>
                        <td>: {{ $data['peserta']['noKartu'] ?? '-' }}
                            ( MR. {{ $data['peserta']['noMr'] ?? '-' }} )
                        </td>
                    </tr>
                    <tr>
                        <td>Nama Peserta</td>
                        <td>: {{ $data['peserta']['nama'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Tgl.Lahir</td>
                        <td>: {{ $data['peserta']['tglLahir'] ?? '-' }}
                            &nbsp;Kelamin: {{ $data['peserta']['kelamin'] ?? '-' }}
                        </td>
                    </tr>
                    <tr>
                        <td>No.Telepon</td>
                        <td>: {{ $reqData['request']['t_sep']['noTelp'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Sub/Spesialis</td>
                        <td>: {{ $data['poli'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Dokter</td>
                        <td>: {{ $reqData['request']['t_sep']['dpjpLayanNama'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Faskes Perujuk</td>
                        <td>: {{ $reqData['request']['t_sep']['rujukan']['ppkRujukanNama'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Diagnosa Awal</td>
                        <td>: {{ $data['diagnosa'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Catatan</td>
                        <td>: {{ $data['catatan'] ?? '-' }}</td>
                    </tr>
                </table>
            </td>

            {{-- KOLOM KANAN --}}
            <td class="w-1/2 pl-4">
                <table class="w-full">
                    <tr>
                        <td>Peserta</td>
                        <td>: {{ $data['peserta']['jnsPeserta'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Jns.Rawat</td>
                        <td>: Rawat Inap</td>
                    </tr>
                    <tr>
                        <td>Jns.Kunjungan</td>
                        <td>: {{ $reqData['request']['t_sep']['tujuanKunjDesc'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Poli Perujuk</td>
                        <td>: {{ $data['poliPerujuk'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Kls.Hak</td>
                        <td>: {{ $reqData['request']['t_sep']['klsRawat']['klsRawatHak'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Kls.Rawat</td>
                        <td>: {{ $data['kelasRawat'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Penjamin</td>
                        <td>: {{ $reqData['request']['t_sep']['penjamin']['penjamin'] ?? '-' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- KETERANGAN & TANDA TANGAN --}}
    <table class="w-full mt-4 text-xs">
        <tr>
            {{-- paragraf keterangan --}}
            <td class="w-3/4 pr-2">
                <p>*Saya menyetujui BPJS Kesehatan untuk :</p>
                <p>a. membuka dan atau menggunakan informasi medis Pasien untuk keperluan administrasi,
                    pembayaran asuransi atau jaminan pembiayaan kesehatan</p>
                <p>b. memberikan akses informasi medis atau riwayat pelayanan kepada dokter/tenaga medis
                    pada MADINAH (JST) untuk kepentingan pemeliharaan kesehatan, pengobatan, penyembuhan,
                    dan perawatan Pasien</p>
                <p>*Saya mengetahui dan memahami :</p>
                <p>a. Rumah Sakit dapat melakukan koordinasi dengan PT Jasa Raharja / PT Taspen /
                    PT ASABRI / BPJS Ketenagakerjaan atau Penjamin lainnya, jika Peserta merupakan pasien yang
                    mengalami kecelakaan lalulintas dan/atau kecelakaan kerja</p>
                <p>b. SEP bukan sebagai bukti penjaminan peserta</p>
                <p>** Dengan tampilnya luaran SEP elektronik ini merupakan hasil validasi terhadap eligibilitas
                    Pasien secara elektronik (validasi finger print, biometrik, atau sistem lain) dan selanjutnya
                    Pasien dapat mengakses pelayanan kesehatan rujukan sesuai ketentuan berlaku.
                    Kebenaran dan keaslian atas data menjadi tanggung jawab penuh FKRTL</p>
            </td>

            {{-- blok tanda tangan + QR --}}
            <td class="w-1/4 pl-2 text-center">
                <p>Persetujuan<br>Pasien/Keluarga Pasien</p>
                <p class="font-semibold">{{ $data['peserta']['nama'] ?? '-' }}</p>

                @inject('carbon', 'Carbon\Carbon')
                <p class="mt-1">Cetakan ke {{ $cetakKe ?? 1 }}<br>
                    {{ $carbon::now(env('APP_TIMEZONE'))->format('d-m-Y H:i:s') }}</p>

                <div class="mt-1">
                    {{-- ukuran QR 3x3 cm seperti PDF --}}
                    {!! DNS2D::getBarcodeHTML($data['noSep'] ?? '-', 'QRCODE', 3, 3) !!}
                </div>
            </td>
        </tr>
    </table>

</body>

</html>
