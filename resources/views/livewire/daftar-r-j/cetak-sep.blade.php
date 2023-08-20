<!DOCTYPE html>
<html>

<head>
    <style>
        @page {
            size: 15cm 28cm;
            margin: 8px;
        }
    </style>


    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">



    <link href="build/assets/sirus.css" rel="stylesheet">
</head>

</div>
<table class="w-full overflow-hidden ">
    <tr>
        <th class="text-center">
            <img src="bpjslogo.png" class="object-fill h-8">
        </th>
    </tr>
</table>


<div class="text-xl font-bold text-center">SURAT ELEGIBILITAS PESERTA</div>
<div class="text-lg font-normal text-center">RSI MADINAH</div>

<body class="bg-gray-50 dark:bg-gray-800">
    <div class="p-4">
        <table class="w-full overflow-hidden " cellspacing="0" cellpadding="0">
            <tbody>

                <tr style="height: 20px">
                    <th style="height: 20px;">
                    </th>
                    <td class="text-sm" dir="ltr">
                        {{ isset($data['informasi']['prolanisPRB'])
                            ? ($data['informasi']['prolanisPRB']
                                ? $data['informasi']['prolanisPRB']
                                : '-')
                            : '--' }}
                    </td>
                    <td class="text-sm"></td>
                    <td class="text-sm"></td>
                    <td class="text-sm"></td>
                    <td class="text-sm"></td>
                    <td class="text-sm"></td>
                    <td class="text-sm"></td>
                </tr>

                <tr style="height: 20px">
                    <th style="height: 20px;">
                    </th>
                    <td class="text-sm" dir="ltr">No. SEP</td>
                    <td class="text-sm" dir="ltr">:</td>
                    <td class="text-sm" dir="ltr">
                        {{ isset($data['noSep']) ? ($data['noSep'] ? $data['noSep'] : '-') : '--' }}
                    </td>

                    <td class="text-sm"></td>
                    <td class="text-sm"></td>
                    <td class="text-sm"></td>
                </tr>
                <tr style="height: 20px">
                    <th style="height: 20px;">
                    </th>
                    <td class="text-sm" dir="ltr">Tgl. SEP</td>
                    <td class="text-sm" dir="ltr">:</td>
                    <td class="text-sm" dir="ltr">
                        {{ isset($data['tglSep']) ? ($data['tglSep'] ? $data['tglSep'] : '-') : '--' }}

                    </td>

                    <td class="text-sm"></td>
                    <td class="text-sm"></td>
                    <td class="text-sm"></td>
                </tr>
                <tr style="height: 20px">
                    <th style="height: 20px;">
                    </th>
                    <td class="text-sm" dir="ltr">No. Kartu</td>
                    <td class="text-sm" dir="ltr">:</td>
                    <td class="text-sm" dir="ltr">
                        {{ isset($data['peserta']['noKartu']) ? ($data['peserta']['noKartu'] ? $data['peserta']['noKartu'] : '-') : '--' }}
                    </td>


                </tr>
                <tr>
                    <th style="height: 20px;">
                    </th>
                    <td class="text-sm" dir="ltr">Peserta</td>
                    <td class="text-sm" dir="ltr">:</td>
                    <td class="text-sm" dir="ltr">
                        {{ isset($data['peserta']['nama']) ? ($data['peserta']['nama'] ? $data['peserta']['nama'] : '-') : '--' }}

                    </td>
                </tr>
                <tr style="height: 20px">
                    <th style="height: 20px;">
                    </th>
                    <td class="text-sm" dir="ltr">Nama Peserta</td>
                    <td class="text-sm" dir="ltr">:</td>
                    <td class="text-sm" dir="ltr">
                        {{ isset($data['peserta']['nama']) ? ($data['peserta']['nama'] ? $data['peserta']['nama'] : '-') : '--' }}
                    </td>


                </tr>
                <tr>
                    <th style="height: 20px;">
                    </th>
                    <td class="text-sm" dir="ltr">COB</td>
                    <td class="text-sm" dir="ltr">:</td>
                    <td class="text-sm" dir="ltr">
                        {{ isset($reqData['request']['t_sep']['cob']['cob']) ? ($reqData['request']['t_sep']['cob']['cob'] ? $reqData['request']['t_sep']['cob']['cob'] : '-') : '--' }}
                    </td>
                </tr>
                <tr style="height: 20px">
                    <th style="height: 20px;">
                    </th>
                    <td class="text-sm" dir="ltr">Tgl. Lahir</td>
                    <td class="text-sm" dir="ltr">:</td>
                    <td class="text-sm" dir="ltr">
                        {{ isset($data['peserta']['tglLahir']) ? ($data['peserta']['tglLahir'] ? $data['peserta']['tglLahir'] : '-') : '--' }}
                    </td>


                </tr>
                <tr>
                    <th style="height: 20px;">
                    </th>
                    <td class="text-sm" dir="ltr">Jns. Rawat</td>
                    <td class="text-sm" dir="ltr">:</td>
                    <td class="text-sm" dir="ltr">
                        {{ (isset($data['jnsPelayanan']) ? ($data['jnsPelayanan'] ? $data['jnsPelayanan'] : '-') : '--') == 1 ? 'Rawat Inap' : 'Rawat Jalan' }}
                    </td>
                </tr>
                <tr style="height: 20px">
                    <th style="height: 20px;">
                    </th>
                    <td class="text-sm" dir="ltr">No. Telepon</td>
                    <td class="text-sm" dir="ltr">:</td>
                    <td class="text-sm" dir="ltr">
                        {{ isset($reqData['request']['t_sep']['noTelp']) ? ($reqData['request']['t_sep']['noTelp'] ? $reqData['request']['t_sep']['noTelp'] : '-') : '--' }}
                    </td>


                </tr>
                <tr>
                    <th style="height: 20px;">
                    </th>
                    <td class="text-sm" dir="ltr">Kls. Rawat</td>
                    <td class="text-sm" dir="ltr">:</td>
                    <td class="text-sm" dir="ltr">
                        {{ isset($reqData['request']['t_sep']['klsRawat']['klsRawatHak']) ? ($reqData['request']['t_sep']['klsRawat']['klsRawatHak'] ? $reqData['request']['t_sep']['klsRawat']['klsRawatHak'] : '-') : '--' }}
                    </td>
                </tr>
                <tr style="height: 20px">
                    <th style="height: 20px;">

                    </th>
                    <td class="text-sm softmerge" dir="ltr">
                        <div class="softmerge-inner" style="width:97px;left:-1px">Spesialis/Sub Spesialis</div>
                    </td>
                    <td class="text-sm" dir="ltr">:</td>
                    <td class="text-sm" dir="ltr">
                        {{ isset($data['poli']) ? ($data['poli'] ? $data['poli'] : '-') : '--' }}
                    </td>

                    <td class="text-sm"></td>
                    <td class="text-sm"></td>
                    <td class="text-sm"></td>
                </tr>
                <tr style="height: 20px">
                    <th style="height: 20px;">

                    </th>
                    <td class="text-sm softmerge" dir="ltr">
                        <div class="softmerge-inner" style="width:97px;left:-1px">DPJP Yg Melayani</div>
                    </td>
                    <td class="text-sm" dir="ltr">:</td>
                    <td class="text-sm" dir="ltr">
                        {{ isset($data['dpjp']['nmDPJP']) ? ($data['dpjp']['nmDPJP'] ? $data['dpjp']['nmDPJP'] : '-') : '--' }}
                    </td>

                    <td class="text-sm"></td>
                    <td class="text-sm"></td>
                    <td class="text-sm"></td>
                </tr>
                <tr style="height: 20px">
                    <th " style="height: 20px;">

                    </th>
                    <td class="text-sm" dir="ltr">Faskes Perujuk</td>
                    <td class="text-sm" dir="ltr">:</td>
                    <td class="text-sm" dir="ltr">
                        {{ isset($data['rujukan']['asalRujukanNama']) ? ($data['rujukan']['asalRujukanNama'] ? $data['rujukan']['asalRujukanNama'] : '-') : '--' }}
                    </td>

                    <td class="text-sm"></td>
                    <td class="text-sm"></td>
                    <td class="text-sm"></td>
                </tr>
                <tr style="height: 20px">
                    <th " style="height: 20px;">

                    </th>
                    <td class="text-sm" dir="ltr">Diagnosa Awal</td>
                    <td class="text-sm" dir="ltr">:</td>
                    <td class="text-sm" dir="ltr">
                        {{ isset($data['diagnosa']) ? ($data['diagnosa'] ? $data['diagnosa'] : '-') : '--' }}
                    </td>

                    <td class="text-sm"></td>
                    <td class="text-sm"></td>
                    <td class="text-sm"></td>
                </tr>



                <tr style="height: 20px">
                    <th " style="height: 20px;">

                    </th>
                    <td class="text-sm" dir="ltr">Catatan</td>
                    <td class="text-sm" dir="ltr">:</td>
                    <td class="text-sm" dir="ltr">
                        {{ isset($data['catatan']) ? ($data['catatan'] ? $data['catatan'] : '-') : '--' }}
                    </td>
                    <td class="text-sm italic softmerge" dir="ltr">
                        <div class="softmerge-inner" style="width:169px;left:-1px">Pasien/Keluarga Pasien</div>
                    </td>
                    <td class="s4"></td>
                    <td class="s5"></td>
                    <td class="text-sm"></td>
                </tr>

                {{-- space --}}

                <tr style="height: 20px">
                    <th style="height: 20px;">

                    </th>
                    <td class="text-sm" dir="ltr"></td>
                    <td class="text-sm" dir="ltr"></td>
                    <td class="text-sm" dir="ltr">

                    </td>

                    <td class="text-sm"></td>
                    <td class="text-sm"></td>
                    <td class="text-sm"></td>
                </tr>


                <tr style="height: 20px">
                    <th style="height: 20px;">

                    </th>
                    <td class="text-sm italic" dir="ltr" colspan="7">*Saya Menyetujui BPJS Kesehatan
                        menggunakan
                        informasi Medis Pasien
                        jika diperlukan.</td>
                </tr>
                <tr style="height: 20px">
                    <th style="height: 20px;">

                    </th>
                    <td class="text-sm italic" dir="ltr" colspan="7">**SEP bukan sebagai bukti penjaminan
                        peserta
                    </td>
                </tr>
                <tr style="height: 20px">
                    <th style="height: 20px;">

                    </th>
                    @inject('carbon', 'Carbon\Carbon')
                    <td class="text-sm italic" dir="ltr" colspan="7"> Cetakan ke 1 {{ $carbon::now()->format('d/m/Y H:i:s') }}
                    </td>
                </tr>
                <tr style="height: 20px" class="">
                    <th  style="height: 20px;">

                    </th>
                    @php
                        $tglRujukan = isset($reqData['request']['t_sep']['rujukan']['tglRujukan']) ? ($reqData['request']['t_sep']['rujukan']['tglRujukan'] ? $reqData['request']['t_sep']['rujukan']['tglRujukan'] : $carbon::now()->format('Y-m-d')) : $carbon::now()->format('Y-m-d');
                        $tglRujukanAwal = $carbon::createFromFormat('Y-m-d', $tglRujukan);
                        $tglBatasRujukan = $carbon::createFromFormat('Y-m-d', $tglRujukan)->addMonths(3);
                        
                        $diffInDays = $tglBatasRujukan->diffInDays($carbon::now());
                        $propertyDiffInDays = $diffInDays <= 20 ? 'red' : ($diffInDays <= 30 ? 'yellow' : '');
                    @endphp
                    <td style= " color : {{ $propertyDiffInDays }}"class="italic font-semibold " dir="ltr"
                        colspan="7">Masa
                        berlaku {{ $tglRujukanAwal->format('d/m/Y') }} s/d
                        {{ $tglBatasRujukan->format('d/m/Y') }}{{ '- - - sisa :' . $diffInDays . ' hari' }}
                        </td>
                </tr>
            </tbody>

        </table>
    </div>

    <table class="w-full overflow-hidden ">
        <tr>
            <th class="mt-4 text-center">
                @php
                    $noSep = isset($data['noSep']) ? ($data['noSep'] ? $data['noSep'] : '-') : '--';
                @endphp
                <{!! DNS2D::getBarcodeHTML($noSep, 'QRCODE', 5, 5) !!} </th>
        </tr>
    </table>


</body>

</html>
