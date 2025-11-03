<!DOCTYPE html>
<html>

<head>
    <style>
        @page {
            size: 21cm 18cm;
            margin: 8px;
        }
    </style>


    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">



    <link href="build/assets/sirus.css" rel="stylesheet">
</head>

<body>

    {{-- Judul --}}
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
                    <td class="p-1 m-1 text-lg font-semibold text-center uppercase"
                        style="text-decoration: underline; text-decoration-color: red;">
                        resep rawat inap
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="relative">
        {{-- Identifikasi Pasien --}}
        <div class="absolute top-0 left-0 w-1/2 ">
            {{-- Identifikasi Pasien --}}
            <div>
                <table style="font-size: 10px "class="w-1/2 table-auto ">
                    <tbody>
                        <tr>
                            <td class="p-1 m-1">No. Resep / Tanggal</td>
                            <td class="p-1 m-1">:</td>
                            <td class="p-1 m-1 text-lg font-semibold">
                                {{ isset($dataDaftarRi['eresepHdr'][$resepIndexRef]['slsNo']) ? $dataDaftarRi['eresepHdr'][$resepIndexRef]['slsNo'] : '-' }}
                                /
                                {{ isset($dataDaftarRi['eresepHdr'][$resepIndexRef]['resepDate']) ? $dataDaftarRi['eresepHdr'][$resepIndexRef]['resepDate'] : '-' }}
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
                            <td class="p-1 m-1">Klaim & Room</td>
                            <td class="p-1 m-1">:</td>
                            <td class="p-1 m-1 font-semibold">

                                @php
                                    // Cek klaim BPJS
                                    $klaim = DB::table('rsmst_klaimtypes')
                                        ->where('klaim_id', $dataDaftarRi['klaimId'])
                                        ->select('klaim_status', 'klaim_desc')
                                        ->first();
                                    // Boolean BPJS
                                    //$isBpjs = optional($klaim)->klaim_status === 'BPJS';

                                    // Deskripsi klaim (fallback jika null)
                                    $klaimDesc = $klaim->klaim_desc ?? 'Asuransi Lain';

                                @endphp

                                {{ $klaimDesc }}
                                /
                                {{ $klaim->klaim_status ?? '' }}
                                /
                                {{ $dataDaftarRi['roomDesc'] ?? '-' }}
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
                                {{ isset($dataDaftarRi['sep']['noSep']) ? $dataDaftarRi['sep']['noSep'] : '-' }}
                            </td>
                        </tr>



                    </tbody>
                </table>
            </div>

            {{-- Obat --}}
            <div class="w-1/2 h-5 bg-blue-500 ">
                <table style="font-size: 10px " class="w-1/2 table-auto">
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
                        @isset($dataDaftarRi['eresepHdr'][$resepIndexRef]['eresep'])
                            @foreach ($dataDaftarRi['eresepHdr'][$resepIndexRef]['eresep'] as $key => $eresep)
                                <tr class="border-b-2 border-black">
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
                                        {{ 'S ' . $eresep['signaX'] . 'dd' . $eresep['signaHari'] }}
                                        @if ($eresep['catatanKhusus'])
                                            {{ ' (' . $eresep['catatanKhusus'] . ')' }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endisset
                    </tbody>

                    <tbody class="text-gray-900 ">
                        @isset($dataDaftarRi['eresepHdr'][$resepIndexRef]['eresepRacikan'])
                            @php
                                $myPreviousRow = '';
                            @endphp
                            @foreach ($dataDaftarRi['eresepHdr'][$resepIndexRef]['eresepRacikan'] as $key => $eresepRacikan)
                                @isset($eresepRacikan['jenisKeterangan'])
                                    @php
                                        $myRacikanBorder =
                                            $myPreviousRow !== $eresepRacikan['noRacikan']
                                                ? 'border-t-2 border-black'
                                                : '';
                                    @endphp

                                    <tr class="{{ $myRacikanBorder }}">
                                        <td class="w-1/5 text-center uppercase">
                                            {{ $eresepRacikan['noRacikan'] . '/' }}
                                        </td>

                                        <td class="w-2/5 uppercase text-start">
                                            {{ $eresepRacikan['productName'] }}

                                            @isset($eresepRacikan['dosis'])
                                                {{ ' - ' . $eresepRacikan['dosis'] }}
                                            @endisset
                                        </td>

                                        <td class="w-1/5 text-center uppercase">
                                            @if ($eresepRacikan['qty'])
                                                {{ 'Jml Racikan ' . $eresepRacikan['qty'] }}
                                            @else
                                                {{ '' }}
                                            @endif
                                        </td>

                                        <td class="w-1/5 text-center uppercase">
                                            @if ($eresepRacikan['qty'])
                                                {{ '(' . $eresepRacikan['catatan'] . ') ' . 'S ' . $eresepRacikan['catatanKhusus'] }}
                                            @else
                                                {{ '' }}
                                            @endif
                                        </td>
                                    </tr>
                                    @php
                                        $myPreviousRow = $eresepRacikan['noRacikan'];
                                    @endphp
                                @endisset
                            @endforeach
                        @endisset
                    </tbody>
                </table>
            </div>
        </div>

        {{-- telaah Resep / Obat --}}
        <div class="absolute top-0 right-0 w-1/2 ">
            <table style="font-size: 10px " class="w-1/2 table-auto ">
                <tbody class="text-gray-900 ">

                    <tr>
                        {{-- telaah Resep --}}
                        <td class="align-text-top ">
                            <table style="font-size: 10px " class="w-1/2 table-auto">
                                <thead class="text-gray-900 ">
                                    <tr>
                                        <th class="text-start">
                                            Pengkajian Resep
                                        </th>

                                        <th class="text-start">
                                            *
                                        </th>

                                        <th class="text-start">
                                            Ket.
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-900 ">

                                    <tr>
                                        <td class="w-3/5 text-start">
                                            {{ 'Kejelasan Tulisan Resep' }}
                                        </td>

                                        <td class="w-1/5 text-start">
                                            {{ isset(
                                                $dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['kejelasanTulisanResep']['kejelasanTulisanResep'],
                                            )
                                                ? ($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['kejelasanTulisanResep']['kejelasanTulisanResep']
                                                    ? $dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['kejelasanTulisanResep']['kejelasanTulisanResep']
                                                    : '-')
                                                : '-' }}
                                        </td>

                                        <td class="w-1/5 text-start">
                                            {{ isset($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['kejelasanTulisanResep']['desc'])
                                                ? ($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['kejelasanTulisanResep']['desc']
                                                    ? $dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['kejelasanTulisanResep']['desc']
                                                    : '-')
                                                : '-' }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="w-3/5 text-start">
                                            {{ 'Tepat Obat' }}
                                        </td>

                                        <td class="w-1/5 text-start">
                                            {{ isset($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['tepatObat']['tepatObat'])
                                                ? ($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['tepatObat']['tepatObat']
                                                    ? $dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['tepatObat']['tepatObat']
                                                    : '-')
                                                : '-' }}
                                        </td>

                                        <td class="w-1/5 text-start">
                                            {{ isset($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['tepatObat']['desc'])
                                                ? ($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['tepatObat']['desc']
                                                    ? $dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['tepatObat']['desc']
                                                    : '-')
                                                : '-' }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="w-3/5 text-start">
                                            {{ 'Tepat Dosis' }}
                                        </td>

                                        <td class="w-1/5 text-start">
                                            {{ isset($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['tepatDosis']['tepatDosis'])
                                                ? ($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['tepatDosis']['tepatDosis']
                                                    ? $dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['tepatDosis']['tepatDosis']
                                                    : '-')
                                                : '-' }}
                                        </td>

                                        <td class="w-1/5 text-start">
                                            {{ isset($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['tepatDosis']['desc'])
                                                ? ($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['tepatDosis']['desc']
                                                    ? $dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['tepatDosis']['desc']
                                                    : '-')
                                                : '-' }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="w-3/5 text-start">
                                            {{ 'Tepat Rute' }}
                                        </td>

                                        <td class="w-1/5 text-start">
                                            {{ isset($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['tepatRute']['tepatRute'])
                                                ? ($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['tepatRute']['tepatRute']
                                                    ? $dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['tepatRute']['tepatRute']
                                                    : '-')
                                                : '-' }}
                                        </td>

                                        <td class="w-1/5 text-start">
                                            {{ isset($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['tepatRute']['desc'])
                                                ? ($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['tepatRute']['desc']
                                                    ? $dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['tepatRute']['desc']
                                                    : '-')
                                                : '-' }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="w-3/5 text-start">
                                            {{ 'Tepat Waktu' }}
                                        </td>

                                        <td class="w-1/5 text-start">
                                            {{ isset($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['tepatWaktu']['tepatWaktu'])
                                                ? ($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['tepatWaktu']['tepatWaktu']
                                                    ? $dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['tepatWaktu']['tepatWaktu']
                                                    : '-')
                                                : '-' }}
                                        </td>

                                        <td class="w-1/5 text-start">
                                            {{ isset($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['tepatWaktu']['desc'])
                                                ? ($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['tepatWaktu']['desc']
                                                    ? $dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['tepatWaktu']['desc']
                                                    : '-')
                                                : '-' }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="w-3/5 text-start">
                                            {{ 'Duplikasi' }}
                                        </td>

                                        <td class="w-1/5 text-start">
                                            {{ isset($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['duplikasi']['duplikasi'])
                                                ? ($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['duplikasi']['duplikasi']
                                                    ? $dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['duplikasi']['duplikasi']
                                                    : '-')
                                                : '-' }}
                                        </td>

                                        <td class="w-1/5 text-start">
                                            {{ isset($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['duplikasi']['desc'])
                                                ? ($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['duplikasi']['desc']
                                                    ? $dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['duplikasi']['desc']
                                                    : '-')
                                                : '-' }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="w-3/5 text-start">
                                            {{ 'Alergi' }}
                                        </td>

                                        <td class="w-1/5 text-start">
                                            {{ isset($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['alergi']['alergi'])
                                                ? ($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['alergi']['alergi']
                                                    ? $dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['alergi']['alergi']
                                                    : '-')
                                                : '-' }}
                                        </td>

                                        <td class="w-1/5 text-start">
                                            {{ isset($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['alergi']['desc'])
                                                ? ($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['alergi']['desc']
                                                    ? $dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['alergi']['desc']
                                                    : '-')
                                                : '-' }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="w-3/5 text-start">
                                            {{ 'Interaksi Obat' }}
                                        </td>

                                        <td class="w-1/5 text-start">
                                            {{ isset($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['interaksiObat']['interaksiObat'])
                                                ? ($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['interaksiObat']['interaksiObat']
                                                    ? $dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['interaksiObat']['interaksiObat']
                                                    : '-')
                                                : '-' }}
                                        </td>

                                        <td class="w-1/5 text-start">
                                            {{ isset($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['interaksiObat']['desc'])
                                                ? ($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['interaksiObat']['desc']
                                                    ? $dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['interaksiObat']['desc']
                                                    : '-')
                                                : '-' }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="w-3/5 text-start">
                                            {{ 'Berat Badan Pasien Anak' }}
                                        </td>

                                        <td class="w-1/5 text-start">
                                            {{ isset($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['bbPasienAnak']['bbPasienAnak'])
                                                ? ($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['bbPasienAnak']['bbPasienAnak']
                                                    ? $dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['bbPasienAnak']['bbPasienAnak']
                                                    : '-')
                                                : '-' }}
                                        </td>

                                        <td class="w-1/5 text-start">
                                            {{ isset($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['bbPasienAnak']['desc'])
                                                ? ($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['bbPasienAnak']['desc']
                                                    ? $dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['bbPasienAnak']['desc']
                                                    : '-')
                                                : '-' }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="w-3/5 text-start">
                                            {{ 'Kontra Indikasi Lain' }}
                                        </td>

                                        <td class="w-1/5 text-start">
                                            {{ isset($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['kontraIndikasiLain']['kontraIndikasiLain'])
                                                ? ($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['kontraIndikasiLain']['kontraIndikasiLain']
                                                    ? $dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['kontraIndikasiLain']['kontraIndikasiLain']
                                                    : '-')
                                                : '-' }}
                                        </td>

                                        <td class="w-1/5 text-start">
                                            {{ isset($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['kontraIndikasiLain']['desc'])
                                                ? ($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['kontraIndikasiLain']['desc']
                                                    ? $dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['kontraIndikasiLain']['desc']
                                                    : '-')
                                                : '-' }}
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        </td>

                        {{-- telaah Obat --}}
                        <td class="align-text-top">
                            <table style="font-size: 10px " class="w-1/2 ml-4 table-auto">
                                <thead class="text-gray-900 ">
                                    <tr>
                                        <th class="text-start">
                                            Pengkajian Obat
                                        </th>

                                        <th class="text-start">
                                            *
                                        </th>

                                        <th class="text-start">
                                            Ket.
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-900 ">

                                    <tr>
                                        <td class="w-3/5 text-start">
                                            {{ 'Obat dgn resep' }}
                                        </td>

                                        <td class="w-1/5 text-start">
                                            {{ isset($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahObat']['obatdgnResep']['obatdgnResep'])
                                                ? ($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahObat']['obatdgnResep']['obatdgnResep']
                                                    ? $dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahObat']['obatdgnResep']['obatdgnResep']
                                                    : '-')
                                                : '-' }}
                                        </td>

                                        <td class="w-1/5 text-start">
                                            {{ isset($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahObat']['obatdgnResep']['desc'])
                                                ? ($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahObat']['obatdgnResep']['desc']
                                                    ? $dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahObat']['obatdgnResep']['desc']
                                                    : '-')
                                                : '-' }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="w-3/5 text-start">
                                            {{ 'Jml / Dosis dgn resep' }}
                                        </td>

                                        <td class="w-1/5 text-start">
                                            {{ isset($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahObat']['jmlDosisdgnResep']['jmlDosisdgnResep'])
                                                ? ($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahObat']['jmlDosisdgnResep']['jmlDosisdgnResep']
                                                    ? $dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahObat']['jmlDosisdgnResep']['jmlDosisdgnResep']
                                                    : '-')
                                                : '-' }}
                                        </td>

                                        <td class="w-1/5 text-start">
                                            {{ isset($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahObat']['jmlDosisdgnResep']['desc'])
                                                ? ($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahObat']['jmlDosisdgnResep']['desc']
                                                    ? $dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahObat']['jmlDosisdgnResep']['desc']
                                                    : '-')
                                                : '-' }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="w-3/5 text-start">
                                            {{ 'Ruter dgn resep' }}
                                        </td>

                                        <td class="w-1/5 text-start">
                                            {{ isset($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahObat']['rutedgnResep']['rutedgnResep'])
                                                ? ($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahObat']['rutedgnResep']['rutedgnResep']
                                                    ? $dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahObat']['rutedgnResep']['rutedgnResep']
                                                    : '-')
                                                : '-' }}
                                        </td>

                                        <td class="w-1/5 text-start">
                                            {{ isset($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahObat']['rutedgnResep']['desc'])
                                                ? ($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahObat']['rutedgnResep']['desc']
                                                    ? $dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahObat']['rutedgnResep']['desc']
                                                    : '-')
                                                : '-' }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="w-3/5 text-start">
                                            {{ 'Waktu dan Frekuensi pemberian dgn resep' }}
                                        </td>

                                        <td class="w-1/5 text-start">
                                            {{ isset(
                                                $dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahObat']['waktuFrekPemberiandgnResep'][
                                                    'waktuFrekPemberiandgnResep'
                                                ],
                                            )
                                                ? ($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahObat']['waktuFrekPemberiandgnResep'][
                                                    'waktuFrekPemberiandgnResep'
                                                ]
                                                    ? $dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahObat']['waktuFrekPemberiandgnResep'][
                                                        'waktuFrekPemberiandgnResep'
                                                    ]
                                                    : '-')
                                                : '-' }}
                                        </td>

                                        <td class="w-1/5 text-start">
                                            {{ isset($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahObat']['waktuFrekPemberiandgnResep']['desc'])
                                                ? ($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahObat']['waktuFrekPemberiandgnResep']['desc']
                                                    ? $dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahObat']['waktuFrekPemberiandgnResep']['desc']
                                                    : '-')
                                                : '-' }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>

                </tbody>

            </table>
        </div>

        {{-- ttd --}}
        <div class="absolute left-0 w-full bottom-32">
            <table style="font-size: 10px " class="w-full table-auto">
                <tbody>

                    @inject('carbon', 'Carbon\Carbon')
                    <td class="w-1/4 ">
                        <div class ="text-center">
                            <span>
                                Tulungagung,{{ isset($dataDaftarRi['eresepHdr'][$resepIndexRef]['resepDate']) ? ($dataDaftarRi['eresepHdr'][$resepIndexRef]['resepDate'] ? $dataDaftarRi['eresepHdr'][$resepIndexRef]['resepDate'] : 'Tanggal') : 'Tanggal' }}
                            </span>
                            <div>
                                @isset($dataDaftarRi['eresepHdr'][$resepIndexRef]['tandaTanganDokter']['dokterPeresep'])
                                    @if ($dataDaftarRi['eresepHdr'][$resepIndexRef]['tandaTanganDokter']['dokterPeresep'])
                                        @isset(App\Models\User::where(
                                                'myuser_code',
                                                $dataDaftarRi['eresepHdr'][$resepIndexRef]['tandaTanganDokter']['dokterPeresepCode'])->first()->myuser_ttd_image)
                                            <img class="h-24 mx-auto"
                                                src="{{ 'storage/' . App\Models\User::where('myuser_code', $dataDaftarRi['eresepHdr'][$resepIndexRef]['tandaTanganDokter']['dokterPeresepCode'])->first()->myuser_ttd_image }}"
                                                alt="">
                                        @endisset
                                    @endif
                                @endisset
                            </div>

                            <span>
                                {{ isset($dataDaftarRi['eresepHdr'][$resepIndexRef]['tandaTanganDokter']['dokterPeresep'])
                                    ? ($dataDaftarRi['eresepHdr'][$resepIndexRef]['tandaTanganDokter']['dokterPeresep']
                                        ? $dataDaftarRi['eresepHdr'][$resepIndexRef]['tandaTanganDokter']['dokterPeresep']
                                        : 'Dokter Pemeriksa')
                                    : 'Dokter Pemeriksa' }}
                            </span>

                            <div>
                                @isset($dataDaftarRi['eresepHdr'][$resepIndexRef]['tandaTanganDokter']['dokterPeresep'])
                                    @if ($dataDaftarRi['eresepHdr'][$resepIndexRef]['tandaTanganDokter']['dokterPeresep'])
                                        @isset(App\Models\User::where(
                                                'myuser_code',
                                                $dataDaftarRi['eresepHdr'][$resepIndexRef]['tandaTanganDokter']['dokterPeresepCode'])->first()->myuser_sip)
                                            <span>
                                                {{ App\Models\User::where('myuser_code', $dataDaftarRi['eresepHdr'][$resepIndexRef]['tandaTanganDokter']['dokterPeresepCode'])->first()->myuser_sip }}
                                            </span>
                                        @endisset
                                    @endif
                                @endisset
                            </div>

                        </div>
                    </td>



                    <td class="w-1/4 align-text-bottom">
                        <div class ="text-center">
                            <span>
                                {{ 'Pengkajian Resep' }}
                            </span>
                            <br>
                            <div>
                                @isset($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['penanggungJawab']['userLog'])
                                    @if ($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['penanggungJawab']['userLog'])
                                        @isset(App\Models\User::where(
                                                'myuser_code',
                                                $dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['penanggungJawab']['userLogCode'])->first()->myuser_ttd_image)
                                            <img class="h-24 mx-auto"
                                                src="{{ 'storage/' . App\Models\User::where('myuser_code', $dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['penanggungJawab']['userLogCode'])->first()->myuser_ttd_image }}"
                                                alt="">
                                        @endisset
                                    @endif
                                @endisset
                            </div>
                            <br>
                            <span>
                                {{ isset($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['penanggungJawab']['userLog'])
                                    ? ($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['penanggungJawab']['userLog']
                                        ? $dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahResep']['penanggungJawab']['userLog']
                                        : 'Pengkajian Resep')
                                    : 'Pengkajian Resep' }}
                            </span>



                        </div>
                    </td>

                    <td class="w-1/4 align-text-bottom">
                        <div class ="text-center">
                            <span>
                                {{ 'Pengkajian Obat' }}
                            </span>
                            <br>
                            <div>
                                @isset($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahObat']['penanggungJawab']['userLog'])
                                    @if ($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahObat']['penanggungJawab']['userLog'])
                                        @isset(App\Models\User::where(
                                                'myuser_code',
                                                $dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahObat']['penanggungJawab']['userLogCode'])->first()->myuser_ttd_image)
                                            <img class="h-24 mx-auto"
                                                src="{{ 'storage/' . App\Models\User::where('myuser_code', $dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahObat']['penanggungJawab']['userLogCode'])->first()->myuser_ttd_image }}"
                                                alt="">
                                        @endisset
                                    @endif
                                @endisset
                            </div>
                            <br>
                            <span>
                                {{ isset($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahObat']['penanggungJawab']['userLog'])
                                    ? ($dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahObat']['penanggungJawab']['userLog']
                                        ? $dataDaftarRi['eresepHdr'][$resepIndexRef]['telaahObat']['penanggungJawab']['userLog']
                                        : 'Pengkajian Obat')
                                    : 'Pengkajian Obat' }}
                            </span>



                        </div>
                    </td>

                    <td class="w-1/4 align-text-bottom">
                        <div class ="text-center">
                            <span>
                                {{ 'ttd Pasien' }}
                            </span>
                            <br>
                            <br>
                        </div>
                    </td>

                </tbody>
            </table>
        </div>
    </div>










</body>

</html>
