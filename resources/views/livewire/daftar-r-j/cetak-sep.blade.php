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

<div class="text-xl font-bold text-center">SURAT ELEGIBILITAS PESERTA</div>
<div class="text-lg font-normal text-center">RSI MADINAH</div>

<body class="bg-gray-50 dark:bg-gray-800">

    <table class="w-full overflow-hidden bg-red-50" cellspacing="0" cellpadding="0">
        <tbody>

            <tr style="height: 20px">
                <th id="8942975R2" style="height: 20px;" class="row-headers-background">
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
                <th id="8942975R3" style="height: 20px;" class="row-headers-background">
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
                <th id="8942975R4" style="height: 20px;" class="row-headers-background">
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
                <th id="8942975R5" style="height: 20px;" class="row-headers-background">
                </th>
                <td class="text-sm" dir="ltr">No. Kartu</td>
                <td class="text-sm" dir="ltr">:</td>
                <td class="text-sm" dir="ltr">
                    {{ isset($data['peserta']['noKartu']) ? ($data['peserta']['noKartu'] ? $data['peserta']['noKartu'] : '-') : '--' }}
                </td>


            </tr>
            <tr>
                <th id="8942975R4" style="height: 20px;" class="row-headers-background">
                </th>
                <td class="text-sm" dir="ltr">Peserta</td>
                <td class="text-sm" dir="ltr">:</td>
                <td class="text-sm" dir="ltr">
                    {{ isset($data['peserta']['nama']) ? ($data['peserta']['nama'] ? $data['peserta']['nama'] : '-') : '--' }}

                </td>
            </tr>
            <tr style="height: 20px">
                <th id="8942975R6" style="height: 20px;" class="row-headers-background">
                </th>
                <td class="text-sm" dir="ltr">Nama Peserta</td>
                <td class="text-sm" dir="ltr">:</td>
                <td class="text-sm" dir="ltr">
                    {{ isset($data['peserta']['nama']) ? ($data['peserta']['nama'] ? $data['peserta']['nama'] : '-') : '--' }}
                </td>


            </tr>
            <tr>
                <th id="8942975R4" style="height: 20px;" class="row-headers-background">
                </th>
                <td class="text-sm" dir="ltr">COB</td>
                <td class="text-sm" dir="ltr">:</td>
                <td class="text-sm" dir="ltr">
                    {{ isset($data['cob']) ? ($data['cob'] ? $data['cob'] : '-') : '--' }}
                </td>
            </tr>
            <tr style="height: 20px">
                <th id="8942975R7" style="height: 20px;" class="row-headers-background">
                </th>
                <td class="text-sm" dir="ltr">Tgl. Lahir</td>
                <td class="text-sm" dir="ltr">:</td>
                <td class="text-sm" dir="ltr">
                    {{ isset($data['peserta']['tglLahir']) ? ($data['peserta']['tglLahir'] ? $data['peserta']['tglLahir'] : '-') : '--' }}
                </td>


            </tr>
            <tr>
                <th id="8942975R4" style="height: 20px;" class="row-headers-background">
                </th>
                <td class="text-sm" dir="ltr">Jns. Rawat</td>
                <td class="text-sm" dir="ltr">:</td>
                <td class="text-sm" dir="ltr">
                    {{ isset($data['jnsPelayanan']) ? ($data['jnsPelayanan'] ? $data['jnsPelayanan'] : '-') : '--' }}
                </td>
            </tr>
            <tr style="height: 20px">
                <th id="8942975R8" style="height: 20px;" class="row-headers-background">
                </th>
                <td class="text-sm" dir="ltr">No. Telepon</td>
                <td class="text-sm" dir="ltr">:</td>
                <td class="text-sm" dir="ltr">
                    {{ isset($data['jnsPelayanan']) ? ($data['jnsPelayanan'] ? $data['jnsPelayanan'] : '-') : '--' }}
                </td>


            </tr>
            <tr>
                <th id="8942975R4" style="height: 20px;" class="row-headers-background">
                </th>
                <td class="text-sm" dir="ltr">Kls. Rawat</td>
                <td class="text-sm" dir="ltr">:</td>
                <td class="text-sm" dir="ltr">
                    {{ isset($data['kelasRawat']) ? ($data['kelasRawat'] ? $data['kelasRawat'] : '-') : '--' }}
                </td>
            </tr>
            <tr style="height: 20px">
                <th id="8942975R9" style="height: 20px;" class="row-headers-background">

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
                <th id="8942975R10" style="height: 20px;" class="row-headers-background">

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
                <th id="8942975R11" style="height: 20px;" class="row-headers-background">

                </th>
                <td class="text-sm" dir="ltr">Faskes Perujuk</td>
                <td class="text-sm" dir="ltr">:</td>
                <td class="text-sm" dir="ltr">
                    {{ isset($data['dpjp']['nmDPJP']) ? ($data['dpjp']['nmDPJP'] ? $data['dpjp']['nmDPJP'] : '-') : '--' }}
                </td>

                <td class="text-sm"></td>
                <td class="text-sm"></td>
                <td class="text-sm"></td>
            </tr>
            <tr style="height: 20px">
                <th id="8942975R12" style="height: 20px;" class="row-headers-background">

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
                <th id="8942975R13" style="height: 20px;" class="row-headers-background">

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
                <th id="8942975R12" style="height: 20px;" class="row-headers-background">

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
                <th id="8942975R14" style="height: 20px;" class="row-headers-background">

                </th>
                <td class="text-sm italic" dir="ltr" colspan="7">*Saya Menyetujui BPJS Kesehatan menggunakan
                    informasi Medis Pasien
                    jika diperlukan.</td>
            </tr>
            <tr style="height: 20px">
                <th id="8942975R15" style="height: 20px;" class="row-headers-background">

                </th>
                <td class="text-sm italic" dir="ltr" colspan="7">**SEP bukan sebagai bukti penjaminan peserta
                </td>
            </tr>
            <tr style="height: 20px">
                <th id="8942975R16" style="height: 20px;" class="row-headers-background">

                </th>
                <td class="text-sm italic" dir="ltr" colspan="7">Cetakan ke 1 {?=date(&#39;d/m/Y
                    H:i:s&#39;)?} PM
                </td>
            </tr>
            <tr style="height: 20px">
                <th id="8942975R17" style="height: 20px;" class="row-headers-background">

                </th>
                <td class="text-sm italic" dir="ltr" colspan="7">Masa berlaku {$data_sep.tglrujukan} s/d
                    {$data_sep.batas_rujukan}
                </td>
            </tr>
        </tbody>

    </table>


</body>

</html>
