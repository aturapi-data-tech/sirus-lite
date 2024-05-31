<!DOCTYPE html>
<html>

<head>
    <style>
        @page {
            size: 11cm 18cm;
            margin: 3px;
        }
    </style>


    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">



    <link href="build/assets/sirus.css" rel="stylesheet">
</head>

<body>


    <div>
        <table style="font-size: 12px " class="w-full table-auto">
            <tbody>
                <tr>
                    <td class="text-center border-b-2 border-black ">

                        <table class="table-auto ">
                            <tbody>
                                <tr>
                                    <td class="">
                                        <img src="madinahlogopersegi.png" class="object-fill h-20 mx-4">
                                    </td>
                                    <td class="text-left ">
                                        <span class="font-semibold">
                                            {!! $myQueryIdentitas->int_name . '</br>' !!}
                                        </span>
                                        {!! $myQueryIdentitas->int_address . '</br>' !!}
                                        {!! $myQueryIdentitas->int_city . '</br>' !!}
                                        {!! $myQueryIdentitas->int_phone1 . '-' !!}
                                        {!! $myQueryIdentitas->int_phone2 . '' !!}
                                        {{-- {!! $myQueryIdentitas->int_fax . '-' !!} --}}
                                    </td>

                                </tr>
                            </tbody>
                        </table>

                    </td>
                </tr>

                <tr>
                    <td class="p-1 m-1 text-lg font-semibold text-center uppercase ">
                        resep rawat jalan
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div>
        <table style="font-size: 10px "class="w-full table-auto ">
            <tbody>
                <tr>
                    <td class="p-1 m-1">No. Resep / Tanggal</td>
                    <td class="p-1 m-1">:</td>
                    <td class="p-1 m-1 font-semibold">
                        {{ isset($dataDaftarPoliRJ['rjNo']) ? $dataDaftarPoliRJ['rjNo'] : '-' }} /
                        {{ isset($dataDaftarPoliRJ['rjDate']) ? $dataDaftarPoliRJ['rjDate'] : '-' }}
                    </td>
                </tr>

                <tr>

                    <td class="p-1 m-1">Pasien</td>
                    <td class="p-1 m-1">:</td>
                    <td class="p-1 m-1 font-semibold">
                        {{ isset($dataPasien['pasien']['regName']) ? strtoupper($dataPasien['pasien']['regName']) : '-' }}
                        <br>
                        {{ isset($dataPasien['pasien']['jenisKelamin']['jenisKelaminDesc']) ? $dataPasien['pasien']['jenisKelamin']['jenisKelaminDesc'] : '-' }}/
                        {{ isset($dataPasien['pasien']['thn']) ? $dataPasien['pasien']['thn'] : '-' }}/
                        {{ isset($dataPasien['pasien']['regNo']) ? $dataPasien['pasien']['regNo'] : '-' }}
                    </td>
                </tr>

                <tr>
                    <td class="p-1 m-1">Tanggal Lahir</td>
                    <td class="p-1 m-1">:</td>
                    <td class="p-1 m-1 font-semibold">
                        {{ isset($dataPasien['pasien']['tglLahir']) ? $dataPasien['pasien']['tglLahir'] : '-' }}
                    </td>
                </tr>

                <tr>
                    <td class="p-1 m-1">Alamat</td>
                    <td class="p-1 m-1">:</td>
                    <td class="p-1 m-1 font-semibold">
                        {{ isset($dataPasien['pasien']['identitas']['alamat']) ? $dataPasien['pasien']['identitas']['alamat'] : '-' }}
                    </td>
                </tr>

                <tr>
                    <td class="p-1 m-1">Klaim</td>
                    <td class="p-1 m-1">:</td>
                    <td class="p-1 m-1 font-semibold">
                        {{ $dataDaftarPoliRJ['klaimId'] == 'UM'
                            ? 'UMUM'
                            : ($dataDaftarPoliRJ['klaimId'] == 'JM'
                                ? 'BPJS'
                                : ($dataDaftarPoliRJ['klaimId'] == 'KR'
                                    ? 'Kronis'
                                    : 'Asuransi Lain')) }}
                        /
                        {{ isset($dataDaftarPoliRJ['poliDesc']) ? $dataDaftarPoliRJ['poliDesc'] : '-' }}
                    </td>
                </tr>

                <tr>
                    <td class="p-1 m-1">NIK/Id BPJS</td>
                    <td class="p-1 m-1">:</td>
                    <td class="p-1 m-1 font-semibold">
                        {{ isset($dataPasien['pasien']['identitas']['nik']) ? $dataPasien['pasien']['identitas']['nik'] : '-' }}
                        /
                        {{ isset($dataPasien['pasien']['identitas']['idbpjs']) ? $dataPasien['pasien']['identitas']['idbpjs'] : '-' }}
                    </td>
                </tr>

                <tr>
                    <td class="p-1 m-1">No SEP</td>
                    <td class="p-1 m-1">:</td>
                    <td class="p-1 m-1 font-semibold">
                        {{ isset($dataDaftarPoliRJ['sep']['noSep']) ? $dataDaftarPoliRJ['sep']['noSep'] : '-' }}
                    </td>
                </tr>



            </tbody>
        </table>
    </div>

    <div>
        <table style="font-size: 10px " class="w-full table-auto">
            <thead class="text-gray-900 ">
                <tr>
                    <th>
                        Jenis
                    </th>

                    <th class="text-start">
                        Nama Obat
                    </th>

                    <th>
                        Jumlah
                    </th>

                    <th>
                        Signa
                    </th>
                </tr>
            </thead>
            <tbody class="text-gray-900 ">
                @isset($dataDaftarPoliRJ['eresep'])
                    @foreach ($dataDaftarPoliRJ['eresep'] as $key => $eresep)
                        <tr>
                            <td class="w-1/5 text-center uppercase">
                                {{ 'R/' }}
                            </td>

                            <td class="w-2/5 uppercase text-start">
                                {{ $eresep['productName'] }}
                            </td>

                            <td class="w-1/5 text-center uppercase">
                                {{ 'No. ' . $eresep['qty'] }}
                            </td>

                            <td class="w-1/5 text-center uppercase">
                                {{ 'S ' . $eresep['signaX'] . 'dd' . $eresep['signaHari'] . '  (' . $eresep['catatanKhusus'] . ')' }}
                            </td>
                        </tr>
                    @endforeach
                @endisset
            </tbody>

            <tbody class="text-gray-900 ">
                @isset($dataDaftarPoliRJ['eresepRacikan'])
                    @foreach ($dataDaftarPoliRJ['eresepRacikan'] as $key => $eresepRacikan)
                        <tr>
                            <td class="w-1/5 text-center uppercase">
                                {{ $eresepRacikan['noRacikan'] . '/' }}
                            </td>

                            <td class="w-2/5 uppercase text-start">
                                {{ $eresepRacikan['productName'] . ' ' . $eresepRacikan['sedia'] }}
                            </td>

                            <td class="w-1/5 text-center uppercase">
                                {{ 'No. ' . $eresepRacikan['qty'] }}
                            </td>

                            <td class="w-1/5 text-center uppercase">
                                {{ 'S ' . $eresepRacikan['catatan'] . '  ' . $eresepRacikan['catatanKhusus'] }}
                            </td>
                        </tr>
                    @endforeach
                @endisset
            </tbody>
        </table>
    </div>


    <div>
        <table style="font-size: 10px " class="w-full table-auto">
            <tbody>
                <td class="w-3/4">

                    x

                </td>
                @inject('carbon', 'Carbon\Carbon')
                <td class="w-1/4 ">
                    <div class ="text-center">
                        <span>
                            Tulungagung,{{ isset($dataDaftarPoliRJ['perencanaan']['pengkajianMedis']['selesaiPemeriksaan'])
                                ? ($dataDaftarPoliRJ['perencanaan']['pengkajianMedis']['selesaiPemeriksaan']
                                    ? $dataDaftarPoliRJ['perencanaan']['pengkajianMedis']['selesaiPemeriksaan']
                                    : 'Tanggal')
                                : 'Tanggal' }}
                        </span>
                        <div>
                            @isset($dataDaftarPoliRJ['perencanaan']['pengkajianMedis']['drPemeriksa'])
                                @if ($dataDaftarPoliRJ['perencanaan']['pengkajianMedis']['drPemeriksa'])
                                    @isset(App\Models\User::where('myuser_code', $dataDaftarPoliRJ['drId'])->first()->myuser_ttd_image)
                                        <img class="h-24 mx-auto"
                                            src="{{ 'storage/' . App\Models\User::where('myuser_code', $dataDaftarPoliRJ['drId'])->first()->myuser_ttd_image }}"
                                            alt="">
                                    @endisset
                                @endif
                            @endisset
                        </div>

                        <span>
                            {{ isset($dataDaftarPoliRJ['perencanaan']['pengkajianMedis']['drPemeriksa'])
                                ? ($dataDaftarPoliRJ['perencanaan']['pengkajianMedis']['drPemeriksa']
                                    ? $dataDaftarPoliRJ['perencanaan']['pengkajianMedis']['drPemeriksa']
                                    : 'Dokter Pemeriksa')
                                : 'Dokter Pemeriksa' }}
                        </span>

                        <div>
                            @isset($dataDaftarPoliRJ['perencanaan']['pengkajianMedis']['drPemeriksa'])
                                @if ($dataDaftarPoliRJ['perencanaan']['pengkajianMedis']['drPemeriksa'])
                                    @isset(App\Models\User::where('myuser_code', $dataDaftarPoliRJ['drId'])->first()->myuser_sip)
                                        <span>
                                            {{ App\Models\User::where('myuser_code', $dataDaftarPoliRJ['drId'])->first()->myuser_sip }}
                                        </span>
                                    @endisset
                                @endif
                            @endisset
                        </div>

                    </div>
                </td>
            </tbody>
        </table>
    </div>

</body>

</html>
