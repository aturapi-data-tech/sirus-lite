<!DOCTYPE html>
<html>

<head>
    <style>
        @page {
            size: 21cm 34cm;
            margin: 4px;
        }
    </style>


    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">



    <link href="build/assets/sirus.css" rel="stylesheet">
</head>

<body class="font-serif">





    {{-- Content --}}
    <div class="bg-white ">
        <table class="w-full table-auto">
            <tbody>
                <tr>
                    <td class="w-1/4 text-xs text-center border-2 border-black">
                        <img src="madinahlogopersegi.png" class="object-fill h-32 mx-auto">
                        <br>
                        {{-- {!! $myQueryIdentitas->int_name . '</br>' !!} --}}
                        {!! $myQueryIdentitas->int_address . '</br>' !!}
                        {!! $myQueryIdentitas->int_city . '</br>' !!}
                        {!! $myQueryIdentitas->int_phone1 . '</br>' !!}
                        {!! $myQueryIdentitas->int_phone2 . '</br>' !!}
                        {!! $myQueryIdentitas->int_fax . '</br>' !!}
                    </td>
                    <td class="w-3/4 text-xs border-2 border-black text-start">
                        <div>
                            <table class="w-full table-auto">
                                <tbody>
                                    <tr>

                                        <td class="p-1 m-1">Nama Pasien</td>
                                        <td class="p-1 m-1">:</td>
                                        <td class="p-1 m-1 text-xs font-semibold">
                                            {{ isset($dataPasien['pasien']['regName']) ? strtoupper($dataPasien['pasien']['regName']) : '-' }}/
                                            {{ isset($dataPasien['pasien']['jenisKelamin']['jenisKelaminDesc']) ? $dataPasien['pasien']['jenisKelamin']['jenisKelaminDesc'] : '-' }}/
                                            {{ isset($dataPasien['pasien']['thn']) ? $dataPasien['pasien']['thn'] : '-' }}
                                        </td>
                                        <td class="p-1 m-1">-</td>
                                        <td class="p-1 m-1">No RM</td>
                                        <td class="p-1 m-1">:</td>
                                        <td class="p-1 m-1 text-lg font-semibold">
                                            {{ isset($dataPasien['pasien']['regNo']) ? $dataPasien['pasien']['regNo'] : '-' }}
                                        </td>
                                    </tr>
                                    <tr>

                                        <td class="p-1 m-1">Tanggal Lahir</td>
                                        <td class="p-1 m-1">:</td>
                                        <td class="p-1 m-1">
                                            {{ isset($dataPasien['pasien']['tglLahir']) ? $dataPasien['pasien']['tglLahir'] : '-' }}
                                        </td>
                                        <td class="p-1 m-1">-</td>
                                        <td class="p-1 m-1">NIK</td>
                                        <td class="p-1 m-1">:</td>
                                        <td class="p-1 m-1">
                                            {{ isset($dataPasien['pasien']['identitas']['nik']) ? $dataPasien['pasien']['identitas']['nik'] : '-' }}

                                        </td>
                                    </tr>
                                    <tr>

                                        <td class="p-1 m-1">Alamat</td>
                                        <td class="p-1 m-1">:</td>
                                        <td class="p-1 m-1">
                                            {{ isset($dataPasien['pasien']['identitas']['alamat']) ? $dataPasien['pasien']['identitas']['alamat'] : '-' }}
                                        </td>
                                        <td class="p-1 m-1">-</td>
                                        <td class="p-1 m-1">Id BPJS</td>
                                        <td class="p-1 m-1">:</td>
                                        <td class="p-1 m-1">
                                            {{ isset($dataPasien['pasien']['identitas']['idbpjs']) ? $dataPasien['pasien']['identitas']['idbpjs'] : '-' }}
                                        </td>
                                    </tr>

                                    <tr>

                                        <td class="p-1 m-1"></td>
                                        <td class="p-1 m-1">:</td>
                                        <td class="p-1 m-1"> </td>
                                        <td class="p-1 m-1">-</td>
                                        <td class="p-1 m-1">Tanggal Masuk</td>
                                        <td class="p-1 m-1">:</td>
                                        <td class="p-1 m-1">
                                            {{ isset($dataDaftarTxn['rjDate']) ? $dataDaftarTxn['rjDate'] : '-' }}
                                        </td>
                                    </tr>
                                    <tr>

                                        <td class="p-1 m-1"></td>
                                        <td class="p-1 m-1">:</td>
                                        <td class="p-1 m-1"></td>
                                        <td class="p-1 m-1">-</td>
                                        <td class="p-1 m-1"></td>
                                        <td class="p-1 m-1">:</td>
                                        <td class="p-1 m-1">
                                            {{-- @isset($dataDaftarTxn['klaimId'])
                                                {{ $dataDaftarTxn['klaimId'] == 'UM'
                                                    ? 'UMUM'
                                                    : ($dataDaftarTxn['klaimId'] == 'JM'
                                                        ? 'BPJS'
                                                        : ($dataDaftarTxn['klaimId'] == 'KR'
                                                            ? 'Kronis'
                                                            : 'Asuransi Lain')) }}
                                            @endisset --}}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- assesment --}}
        <div>
            <table class="w-full table-auto">
                <tbody>
                    <tr>
                        <td
                            class="p-1 m-1 text-lg font-semibold text-center uppercase border-b-2 border-l-2 border-r-2 border-black">
                            resume rawat jalan
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        {{-- assesment --}}

        {{-- Pengkajian peerawatan --}}
        <div>
            <table class="w-full table-auto">
                <tbody>
                    <tr>
                        <td
                            class="p-2 m-2 text-xs font-semibold uppercase border-b-2 border-l-2 border-black text-start">
                            pengkajian perawat
                        </td>

                        <td class="p-2 m-2 text-xs border-b-2 border-r-2 border-black text-start">
                            {{-- <span class="font-semibold">
                                Cara Masuk Rj :
                            </span>
                            {{ isset($dataDaftarTxn['anamnesa']['pengkajianPerawatan']['caraMasukRj']) ? $dataDaftarTxn['anamnesa']['pengkajianPerawatan']['caraMasukRj'] : '-' }}
                            /
                            <span class="font-semibold">
                                Tingkat Kegawatan :
                            </span>
                            {{ isset($dataDaftarTxn['anamnesa']['pengkajianPerawatan']['tingkatKegawatan']) ? $dataDaftarTxn['anamnesa']['pengkajianPerawatan']['tingkatKegawatan'] : '-' }}

                            <br> --}}

                            <span class="font-semibold">
                                Status Psikologis :
                            </span>
                            {{ isset($dataDaftarTxn['anamnesa']['statusPsikologis']['tidakAdaKelainan'])
                                ? ($dataDaftarTxn['anamnesa']['statusPsikologis']['tidakAdaKelainan']
                                    ? 'Tidak ada kelainan'
                                    : '-')
                                : '-' }}
                            <span class="font-semibold">
                                /
                            </span>
                            {{ isset($dataDaftarTxn['anamnesa']['statusPsikologis']['marah'])
                                ? ($dataDaftarTxn['anamnesa']['statusPsikologis']['marah']
                                    ? 'Marah'
                                    : '-')
                                : '-' }}
                            <span class="font-semibold">
                                /
                            </span>
                            {{ isset($dataDaftarTxn['anamnesa']['statusPsikologis']['ccemas'])
                                ? ($dataDaftarTxn['anamnesa']['statusPsikologis']['ccemas']
                                    ? 'Cemas'
                                    : '-')
                                : '-' }}
                            <span class="font-semibold">
                                /
                            </span>
                            {{ isset($dataDaftarTxn['anamnesa']['statusPsikologis']['takut'])
                                ? ($dataDaftarTxn['anamnesa']['statusPsikologis']['takut']
                                    ? 'Takut'
                                    : '-')
                                : '-' }}
                            <span class="font-semibold">
                                /
                            </span>
                            {{ isset($dataDaftarTxn['anamnesa']['statusPsikologis']['sedih'])
                                ? ($dataDaftarTxn['anamnesa']['statusPsikologis']['sedih']
                                    ? 'Sedih'
                                    : '-')
                                : '-' }}
                            <span class="font-semibold">
                                /
                            </span>
                            {{ isset($dataDaftarTxn['anamnesa']['statusPsikologis']['cenderungBunuhDiri'])
                                ? ($dataDaftarTxn['anamnesa']['statusPsikologis']['cenderungBunuhDiri']
                                    ? 'Resiko Bunuh Diri'
                                    : '-')
                                : '-' }}
                            /
                            <span class="font-semibold">
                                Keterangan Status Psikologis
                            </span>
                            {{ isset($dataDaftarTxn['anamnesa']['statusPsikologis']['sebutstatusPsikologis']) ? $dataDaftarTxn['anamnesa']['statusPsikologis']['sebutstatusPsikologis'] : '-' }}

                            <br>

                            <span class="font-semibold">
                                Status Mental :
                            </span>
                            {{ isset($dataDaftarTxn['anamnesa']['statusMental']['statusMental'])
                                ? ($dataDaftarTxn['anamnesa']['statusMental']['statusMental']
                                    ? $dataDaftarTxn['anamnesa']['statusMental']['statusMental']
                                    : '-')
                                : '-' }}
                            /
                            <span class="font-semibold">
                                Keterangan Status Mental :
                            </span>
                            {{ isset($dataDaftarTxn['anamnesa']['statusMental']['sebutstatusPsikologis']) ? $dataDaftarTxn['anamnesa']['statusMental']['sebutstatusPsikologis'] : '-' }}
                        </td>

                    </tr>
                </tbody>
            </table>
        </div>
        {{-- Pengkajian peerawatan --}}


        {{-- Keadaan Umum --}}
        <div>
            <table class="w-full table-auto">
                <tbody>

                    <tr>
                        <td
                            class="w-1/4 p-2 m-2 text-xs font-semibold uppercase border-b-2 border-l-2 border-r-2 border-black text-start">
                            Anamnesa
                        </td>
                        <td class="w-3/4 p-2 m-2 text-xs text-center border-b-2 border-l-2 border-r-2 border-black">
                            Keluhan Utama :
                            <br>
                            {!! nl2br(
                                e(
                                    isset($dataDaftarTxn['anamnesa']['keluhanUtama']['keluhanUtama'])
                                        ? ($dataDaftarTxn['anamnesa']['keluhanUtama']['keluhanUtama']
                                            ? $dataDaftarTxn['anamnesa']['keluhanUtama']['keluhanUtama']
                                            : '-')
                                        : '-',
                                ),
                            ) !!}
                        </td>

                    </tr>



                    {{-- diagnosis --}}
                    <tr>
                        <td
                            class="p-2 m-2 text-xs font-semibold uppercase border-b-2 border-l-2 border-r-2 border-black text-start">
                            diagnosis
                        </td>

                        <td class="p-2 m-2 text-xs text-center border-b-2 border-l-2 border-r-2 border-black">
                            <table class="w-full text-xs table-auto">
                                <tbody>
                                    <tr>
                                        <td>
                                            {!! nl2br(
                                                e(
                                                    isset($dataDaftarTxn['diagnosisFreeText'])
                                                        ? ($dataDaftarTxn['diagnosisFreeText']
                                                            ? $dataDaftarTxn['diagnosisFreeText']
                                                            : '-')
                                                        : '-',
                                                ),
                                            ) !!}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                        </td>
                    </tr>
                    {{-- prosedur --}}
                    <tr>
                        <td
                            class="p-2 m-2 text-xs font-semibold uppercase border-b-2 border-l-2 border-r-2 border-black text-start">
                            prosedur
                        </td>

                        <td class="p-2 m-2 text-xs text-center border-b-2 border-l-2 border-r-2 border-black">
                            <table class="w-full text-xs table-auto">
                                <tbody>
                                    <tr>
                                        <td>
                                            {!! nl2br(
                                                e(
                                                    isset($dataDaftarTxn['procedureFreeText'])
                                                        ? ($dataDaftarTxn['procedureFreeText']
                                                            ? $dataDaftarTxn['procedureFreeText']
                                                            : '-')
                                                        : '-',
                                                ),
                                            ) !!}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>



                        </td>
                    </tr>
                    {{-- status medik dan tindak lanjut --}}
                    <tr>
                        <td
                            class="p-2 m-2 text-xs font-semibold uppercase border-b-2 border-l-2 border-r-2 border-black text-start">
                            tindak lanjut
                        </td>

                        <td class="p-2 m-2 text-xs text-center border-b-2 border-l-2 border-r-2 border-black">
                            <span class="font-semibold">
                                Tindak Lanjut :
                            </span>
                            {{ isset($dataDaftarTxn['perencanaan']['tindakLanjut']['tindakLanjut'])
                                ? ($dataDaftarTxn['perencanaan']['tindakLanjut']['tindakLanjut']
                                    ? $dataDaftarTxn['perencanaan']['tindakLanjut']['tindakLanjut']
                                    : '-')
                                : '-' }}
                            /
                            {{ isset($dataDaftarTxn['perencanaan']['tindakLanjut']['keteranganTindakLanjut'])
                                ? ($dataDaftarTxn['perencanaan']['tindakLanjut']['keteranganTindakLanjut']
                                    ? $dataDaftarTxn['perencanaan']['tindakLanjut']['keteranganTindakLanjut']
                                    : '-')
                                : '-' }}

                        </td>
                    </tr>
                    {{-- terapi --}}
                    <tr>
                        <td
                            class="p-2 m-2 text-xs font-semibold uppercase border-b-2 border-l-2 border-r-2 border-black text-start">
                            terapi
                        </td>
                        <td class="p-2 m-2 text-xs border-b-2 border-l-2 border-r-2 border-black text-start">

                            <table class="w-full text-xs table-auto">
                                <tbody>
                                    <td class="w-3/4">

                                        {!! nl2br(
                                            e(
                                                isset($dataDaftarTxn['perencanaan']['terapi']['terapi'])
                                                    ? ($dataDaftarTxn['perencanaan']['terapi']['terapi']
                                                        ? $dataDaftarTxn['perencanaan']['terapi']['terapi']
                                                        : '-')
                                                    : '-',
                                            ),
                                        ) !!}

                                    </td>
                                    @inject('carbon', 'Carbon\Carbon')
                                    <td class="w-1/4 ">
                                        <div class ="text-center">
                                            <span>
                                                Tulungagung,{{ isset($dataDaftarTxn['perencanaan']['pengkajianMedis']['selesaiPemeriksaan'])
                                                    ? ($dataDaftarTxn['perencanaan']['pengkajianMedis']['selesaiPemeriksaan']
                                                        ? $dataDaftarTxn['perencanaan']['pengkajianMedis']['selesaiPemeriksaan']
                                                        : 'Tanggal')
                                                    : 'Tanggal' }}
                                            </span>
                                            <div>
                                                @isset($dataDaftarTxn['perencanaan']['pengkajianMedis']['drPemeriksa'])
                                                    @if ($dataDaftarTxn['perencanaan']['pengkajianMedis']['drPemeriksa'])
                                                        @isset(App\Models\User::where('myuser_code', $dataDaftarTxn['drId'])->first()->myuser_ttd_image)
                                                            <img class="h-24 mx-auto"
                                                                src="{{ 'storage/' . App\Models\User::where('myuser_code', $dataDaftarTxn['drId'])->first()->myuser_ttd_image }}"
                                                                alt="">
                                                        @endisset
                                                    @endif
                                                @endisset
                                            </div>

                                            <span>
                                                {{ isset($dataDaftarTxn['perencanaan']['pengkajianMedis']['drPemeriksa'])
                                                    ? ($dataDaftarTxn['perencanaan']['pengkajianMedis']['drPemeriksa']
                                                        ? $dataDaftarTxn['perencanaan']['pengkajianMedis']['drPemeriksa']
                                                        : 'Dokter Pemeriksa')
                                                    : 'Dokter Pemeriksa' }}
                                            </span>

                                        </div>
                                    </td>
                                </tbody>
                            </table>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
        {{-- Keadaan Umum --}}

    </div>
    {{-- End Content --}}


</body>

</html>
