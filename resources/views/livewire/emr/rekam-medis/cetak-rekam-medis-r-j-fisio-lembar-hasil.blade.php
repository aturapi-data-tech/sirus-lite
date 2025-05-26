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

        {{-- Anamnesa Pemeriksaan Fisik --}}
        <div>
            <table class="w-full table-auto">
                <tbody>


                    <tr>
                        <td
                            class="p-2 m-2 text-xs font-semibold uppercase border-b-2 border-l-2 border-black text-start">
                            Instrumen Uji Fungsi / Prosedur KFR :
                            <br>
                            Hasil yabng didapat :
                        </td>
                        <td class="p-2 m-2 border-b-2 border-l-2 border-r-2 border-black">
                            <br>
                            {!! nl2br(
                                e(
                                    !empty($dataDaftarTxn['pemeriksaan']['FisikujiFungsi']['FisikujiFungsi'])
                                        ? $dataDaftarTxn['pemeriksaan']['FisikujiFungsi']['FisikujiFungsi']
                                        : '-',
                                ),
                            ) !!}
                        </td>
                    </tr>

                    <tr>
                        <td
                            class="p-2 m-2 text-xs font-semibold uppercase border-b-2 border-l-2 border-black text-start">
                            Kesimpulan
                        </td>
                        <td class="p-2 m-2 border-b-2 border-l-2 border-r-2 border-black">
                            {!! nl2br(e(!empty($dataDaftarTxn['diagnosisFreeText']) ? $dataDaftarTxn['diagnosisFreeText'] : '-')) !!}
                        </td>
                    </tr>



                    <tr>
                        <td
                            class="p-2 m-2 text-xs font-semibold uppercase border-b-2 border-l-2 border-black text-start">
                            Rekomendasi
                        </td>
                        <td class="p-2 m-2 border-b-2 border-l-2 border-r-2 border-black">
                            {!! nl2br(
                                e(
                                    !empty($dataDaftarTxn['perencanaan']['terapi']['terapi'])
                                        ? $dataDaftarTxn['perencanaan']['terapi']['terapi']
                                        : '-',
                                ),
                            ) !!}
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>




        <div>
            <table class="w-full table-auto">
                <tbody>
                    <tr>
                        <td class="p-2 m-2 text-xs border-b-2 border-l-2 border-r-2 border-black text-start">
                            <table class="w-full text-xs table-auto">
                                <tbody>

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

    </div>


</body>

</html>
