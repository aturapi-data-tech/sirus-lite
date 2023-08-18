{{-- <html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<head>
    <style type="text/css">
        @page {
            size: 10cm 21cm;
            margin: 0px
        }


        .ritz .waffle a {
            color: inherit;
        }

        .ritz .waffle .s4 {
            border-left: none;
            background-color: #ffffff;
        }

        .ritz .waffle .s0 {
            background-color: #ffffff;
            text-align: center;
            font-weight: bold;
            color: #000000;
            font-family: 'Arial';
            font-size: 12pt;
            vertical-align: bottom;
            white-space: nowrap;
            direction: ltr;
            padding: 2px 3px 2px 3px;
        }

        .ritz .waffle .s1 {
            background-color: #ffffff;
            text-align: center;
            color: #000000;
            font-family: 'Arial';
            font-size: 10pt;
            vertical-align: bottom;
            white-space: nowrap;
            direction: ltr;
            padding: 2px 3px 2px 3px;
        }

        .ritz .waffle .s5 {
            border-left: none;
            background-color: #ffffff;
            text-align: left;
            color: #000000;
            font-family: 'Arial';
            font-size: 9pt;
            vertical-align: bottom;
            white-space: nowrap;
            direction: ltr;
            padding: 2px 3px 2px 3px;
        }

        .ritz .waffle .s2 {
            background-color: #ffffff;
            text-align: left;
            color: #000000;
            font-family: 'Arial';
            font-size: 8pt;
            vertical-align: bottom;
            white-space: nowrap;
            direction: ltr;
            padding: 2px 3px 2px 3px;
        }

        .ritz .waffle .s3 {
            border-right: none;
            background-color: #ffffff;
            text-align: left;
            color: #000000;
            font-family: 'Arial';
            font-size: 7pt;
            vertical-align: bottom;
            white-space: nowrap;
            direction: ltr;
            padding: 2px 3px 2px 3px;
        }
    </style>


</head>



<body>

    <div class="ritz " dir="ltr">
        <table class="waffle" cellspacing="0" cellpadding="0">
            <tbody>
                <tr style="height: 20px">
                    <th id="8942975R3" style="height: 20px;" class="row-headers-background">
                    </th>
                    <td class="s0" dir="ltr">SURAT ELEGIBILITAS PESERTA</td>
                    <td class="s0" dir="ltr"></td>
                    <td class="s0" dir="ltr">
                    </td>

                    <td class="s0"></td>
                    <td class="s0"></td>
                    <td class="s0"></td>
                </tr>
                <tr style="height: 20px">
                    <th id="8942975R3" style="height: 20px;" class="row-headers-background">
                    </th>
                    <td class="s1" dir="ltr">RSI MADINAH</td>
                    <td class="s1" dir="ltr"></td>
                    <td class="s1" dir="ltr">
                    </td>

                    <td class="s1"></td>
                    <td class="s1"></td>
                    <td class="s1"></td>
                </tr>
            </tbody>
            <tbody>

                <tr style="height: 20px">
                    <th id="8942975R2" style="height: 20px;" class="row-headers-background">
                    </th>
                    <td class="s2" dir="ltr">
                        {{ isset($data['informasi']['prolanisPRB'])
                            ? ($data['informasi']['prolanisPRB']
                                ? $data['informasi']['prolanisPRB']
                                : '-')
                            : '--' }}
                    </td>
                    <td class="s2"></td>
                    <td class="s2"></td>
                    <td class="s2"></td>
                    <td class="s2"></td>
                    <td class="s2"></td>
                    <td class="s2"></td>
                </tr>

                <tr style="height: 20px">
                    <th id="8942975R3" style="height: 20px;" class="row-headers-background">
                    </th>
                    <td class="s2" dir="ltr">No. SEP</td>
                    <td class="s2" dir="ltr">:</td>
                    <td class="s2" dir="ltr">
                        {{ isset($data['noSep']) ? ($data['noSep'] ? $data['noSep'] : '-') : '--' }}
                    </td>

                    <td class="s2"></td>
                    <td class="s2"></td>
                    <td class="s2"></td>
                </tr>
                <tr style="height: 20px">
                    <th id="8942975R4" style="height: 20px;" class="row-headers-background">
                    </th>
                    <td class="s2" dir="ltr">Tgl. SEP</td>
                    <td class="s2" dir="ltr">:</td>
                    <td class="s2" dir="ltr">
                        {{ isset($data['tglSep']) ? ($data['tglSep'] ? $data['tglSep'] : '-') : '--' }}

                    </td>

                    <td class="s2"></td>
                    <td class="s2"></td>
                    <td class="s2"></td>
                </tr>
                <tr style="height: 20px">
                    <th id="8942975R5" style="height: 20px;" class="row-headers-background">
                    </th>
                    <td class="s2" dir="ltr">No. Kartu</td>
                    <td class="s2" dir="ltr">:</td>
                    <td class="s2" dir="ltr">
                        {{ isset($data['peserta']['noKartu']) ? ($data['peserta']['noKartu'] ? $data['peserta']['noKartu'] : '-') : '--' }}
                    </td>


                </tr>
                <tr>
                    <th id="8942975R4" style="height: 20px;" class="row-headers-background">
                    </th>
                    <td class="s2" dir="ltr">Peserta</td>
                    <td class="s2" dir="ltr">:</td>
                    <td class="s2" dir="ltr">
                        {{ isset($data['peserta']['nama']) ? ($data['peserta']['nama'] ? $data['peserta']['nama'] : '-') : '--' }}

                    </td>
                </tr>
                <tr style="height: 20px">
                    <th id="8942975R6" style="height: 20px;" class="row-headers-background">
                    </th>
                    <td class="s2" dir="ltr">Nama Peserta</td>
                    <td class="s2" dir="ltr">:</td>
                    <td class="s2" dir="ltr">
                        {{ isset($data['peserta']['nama']) ? ($data['peserta']['nama'] ? $data['peserta']['nama'] : '-') : '--' }}
                    </td>


                </tr>
                <tr>
                    <th id="8942975R4" style="height: 20px;" class="row-headers-background">
                    </th>
                    <td class="s2" dir="ltr">COB</td>
                    <td class="s2" dir="ltr">:</td>
                    <td class="s2" dir="ltr">
                        {{ isset($data['cob']) ? ($data['cob'] ? $data['cob'] : '-') : '--' }}
                    </td>
                </tr>
                <tr style="height: 20px">
                    <th id="8942975R7" style="height: 20px;" class="row-headers-background">
                    </th>
                    <td class="s2" dir="ltr">Tgl. Lahir</td>
                    <td class="s2" dir="ltr">:</td>
                    <td class="s2" dir="ltr">
                        {{ isset($data['peserta']['tglLahir']) ? ($data['peserta']['tglLahir'] ? $data['peserta']['tglLahir'] : '-') : '--' }}
                    </td>


                </tr>
                <tr>
                    <th id="8942975R4" style="height: 20px;" class="row-headers-background">
                    </th>
                    <td class="s2" dir="ltr">Jns. Rawat</td>
                    <td class="s2" dir="ltr">:</td>
                    <td class="s2" dir="ltr">
                        {{ isset($data['jnsPelayanan']) ? ($data['jnsPelayanan'] ? $data['jnsPelayanan'] : '-') : '--' }}
                    </td>
                </tr>
                <tr style="height: 20px">
                    <th id="8942975R8" style="height: 20px;" class="row-headers-background">
                    </th>
                    <td class="s2" dir="ltr">No. Telepon</td>
                    <td class="s2" dir="ltr">:</td>
                    <td class="s2" dir="ltr">
                        {{ isset($data['jnsPelayanan']) ? ($data['jnsPelayanan'] ? $data['jnsPelayanan'] : '-') : '--' }}
                    </td>


                </tr>
                <tr>
                    <th id="8942975R4" style="height: 20px;" class="row-headers-background">
                    </th>
                    <td class="s2" dir="ltr">Kls. Rawat</td>
                    <td class="s2" dir="ltr">:</td>
                    <td class="s2" dir="ltr">
                        {{ isset($data['kelasRawat']) ? ($data['kelasRawat'] ? $data['kelasRawat'] : '-') : '--' }}
                    </td>
                </tr>
                <tr style="height: 20px">
                    <th id="8942975R9" style="height: 20px;" class="row-headers-background">

                    </th>
                    <td class="s2 softmerge" dir="ltr">
                        <div class="softmerge-inner" style="width:97px;left:-1px">Spesialis/Sub Spesialis</div>
                    </td>
                    <td class="s2" dir="ltr">:</td>
                    <td class="s2" dir="ltr">
                        {{ isset($data['poli']) ? ($data['poli'] ? $data['poli'] : '-') : '--' }}
                    </td>

                    <td class="s2"></td>
                    <td class="s2"></td>
                    <td class="s2"></td>
                </tr>
                <tr style="height: 20px">
                    <th id="8942975R10" style="height: 20px;" class="row-headers-background">

                    </th>
                    <td class="s2 softmerge" dir="ltr">
                        <div class="softmerge-inner" style="width:97px;left:-1px">DPJP Yg Melayani</div>
                    </td>
                    <td class="s2" dir="ltr">:</td>
                    <td class="s2" dir="ltr">
                        {{ isset($data['dpjp']['nmDPJP']) ? ($data['dpjp']['nmDPJP'] ? $data['dpjp']['nmDPJP'] : '-') : '--' }}
                    </td>

                    <td class="s2"></td>
                    <td class="s2"></td>
                    <td class="s2"></td>
                </tr>
                <tr style="height: 20px">
                    <th id="8942975R11" style="height: 20px;" class="row-headers-background">

                    </th>
                    <td class="s2" dir="ltr">Faskes Perujuk</td>
                    <td class="s2" dir="ltr">:</td>
                    <td class="s2" dir="ltr">
                        {{ isset($data['dpjp']['nmDPJP']) ? ($data['dpjp']['nmDPJP'] ? $data['dpjp']['nmDPJP'] : '-') : '--' }}
                    </td>

                    <td class="s2"></td>
                    <td class="s2"></td>
                    <td class="s2"></td>
                </tr>
                <tr style="height: 20px">
                    <th id="8942975R12" style="height: 20px;" class="row-headers-background">

                    </th>
                    <td class="s2" dir="ltr">Diagnosa Awal</td>
                    <td class="s2" dir="ltr">:</td>
                    <td class="s2" dir="ltr">
                        {{ isset($data['diagnosa']) ? ($data['diagnosa'] ? $data['diagnosa'] : '-') : '--' }}
                    </td>

                    <td class="s2"></td>
                    <td class="s2"></td>
                    <td class="s2"></td>
                </tr>
                <tr style="height: 20px">
                    <th id="8942975R13" style="height: 20px;" class="row-headers-background">

                    </th>
                    <td class="s2" dir="ltr">Catatan</td>
                    <td class="s2" dir="ltr">:</td>
                    <td class="s2" dir="ltr">
                        {{ isset($data['catatan']) ? ($data['catatan'] ? $data['catatan'] : '-') : '--' }}
                    </td>
                    <td class="s3 softmerge" dir="ltr">
                        <div class="softmerge-inner" style="width:169px;left:-1px">Pasien/Keluarga Pasien</div>
                    </td>
                    <td class="s4"></td>
                    <td class="s5"></td>
                    <td class="s2"></td>
                </tr>
                <tr style="height: 20px">
                    <th id="8942975R14" style="height: 20px;" class="row-headers-background">

                    </th>
                    <td class="s3" dir="ltr" colspan="7">*Saya Menyetujui BPJS Kesehatan menggunakan
                        informasi Medis Pasien
                        jika diperlukan.</td>
                </tr>
                <tr style="height: 20px">
                    <th id="8942975R15" style="height: 20px;" class="row-headers-background">

                    </th>
                    <td class="s3" dir="ltr" colspan="7">**SEP bukan sebagai bukti penjaminan peserta
                    </td>
                </tr>
                <tr style="height: 20px">
                    <th id="8942975R16" style="height: 20px;" class="row-headers-background">

                    </th>
                    <td class="s3" dir="ltr" colspan="7">Cetakan ke 1 {?=date(&#39;d/m/Y H:i:s&#39;)?} PM
                    </td>
                </tr>
                <tr style="height: 20px">
                    <th id="8942975R17" style="height: 20px;" class="row-headers-background">

                    </th>
                    <td class="s3" dir="ltr" colspan="7">Masa berlaku {$data_sep.tglrujukan} s/d
                        {$data_sep.batas_rujukan}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</body>



</html> --}}
@php
    // exec('c:\WINDOWS\system32\cmd.exe /c START C:\xampp\htdocs\myy\test.bat');
    // echo 'Game server has been started';
    
    $output = shell_exec('ls -lart');
    echo "<pre>$output</pre>";
@endphp
