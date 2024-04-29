<!DOCTYPE html>
<html>

<head>
    <style>
        @page {
            size: 11cm 18cm;
            margin: 2px;
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

                    <td class="p-1 m-1">Nama Pasien</td>
                    <td class="p-1 m-1">:</td>
                    <td class="p-1 m-1 font-semibold">
                        {{ isset($dataPasien['pasien']['regName']) ? strtoupper($dataPasien['pasien']['regName']) : '-' }}
                        <br>
                        {{ isset($dataPasien['pasien']['jenisKelamin']['jenisKelaminDesc']) ? $dataPasien['pasien']['jenisKelamin']['jenisKelaminDesc'] : '-' }}/
                        {{ isset($dataPasien['pasien']['thn']) ? $dataPasien['pasien']['thn'] : '-' }}
                    </td>
                    <td class="p-1 m-1">-</td>
                    <td class="p-1 m-1">No RM</td>
                    <td class="p-1 m-1">:</td>
                    <td class="p-1 m-1 font-semibold">
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
                    <td class="p-1 m-1">Tanggal Resep</td>
                    <td class="p-1 m-1">:</td>
                    <td class="p-1 m-1">
                        {{ isset($dataDaftarTxn['rjDate']) ? $dataDaftarTxn['rjDate'] : '-' }}
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
                            <td class="w-1/5 text-center">
                                {{ '(N)' }}
                            </td>

                            <td class="w-2/5 text-start">
                                {{ $eresep['productName'] }}
                            </td>

                            <td class="w-1/5 text-center">
                                {{ $eresep['qty'] }}
                            </td>

                            <td class="w-1/5 text-center">
                                {{ $eresep['catatanKhusus'] }}
                            </td>
                        </tr>
                    @endforeach
                @endisset
            </tbody>

            <tbody class="text-gray-900 ">
                @isset($dataDaftarPoliRJ['eresepRacikan'])
                    @foreach ($dataDaftarPoliRJ['eresepRacikan'] as $key => $eresepRacikan)
                        <tr>
                            <td class="w-1/5 text-center">
                                {{ '(' . $eresepRacikan['noRacikan'] . ')' }}
                            </td>

                            <td class="w-2/5 text-start">
                                {{ $eresepRacikan['productName'] . ' ' . $eresepRacikan['sedia'] }}
                            </td>

                            <td class="w-1/5 text-center">
                                {{ $eresepRacikan['qty'] }}
                            </td>

                            <td class="w-1/5 text-center">
                                {{ $eresepRacikan['catatan'] . '  ' . $eresepRacikan['catatanKhusus'] }}
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
    </div>

</body>

</html>
