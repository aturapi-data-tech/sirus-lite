<div class="h-[calc(100vh-250px)] mt-2 overflow-auto">
    <!-- Table -->
    <table class="w-full text-sm text-left text-gray-700 table-auto ">
        <thead class="text-xs text-gray-900 uppercase bg-gray-100 ">
            <tr>
                <th scope="col" class="px-4 py-3 ">
                    Tindakan Medis
                </th>

                <th scope="col" class="px-4 py-3 ">
                    Atas Persetujuan
                </th>

                <th scope="col" class="px-4 py-3 ">
                    Action
                </th>
            </tr>
        </thead>

        <tbody class="bg-white ">
            @isset($dataDaftarUgd['informConsentPasienUGD'])
                @foreach ($dataDaftarUgd['informConsentPasienUGD'] as $myQData)
                    <tr class="border-b group dark:border-gray-700"
                        wire:click="setInformConsentPasienUGD({{ json_encode($myQData, true) }})">


                        <td class="px-4 py-3 group-hover:bg-gray-100 whitespace-nowrap dark:text-white">
                            <div class="text-sm text-primary">
                                Tindakan :{{ $myQData['tindakan'] }}
                                </br>
                                Tujuan :{{ $myQData['tujuan'] }}
                                </br>
                                Resiko :{{ $myQData['resiko'] }}
                                </br>
                                Alternatif: {{ $myQData['alternatif'] }}
                                </br>
                                Dokter :{{ $myQData['dokter'] }}
                                </br>
                            </div>

                        </td>

                        <td class="px-4 py-3 group-hover:bg-gray-100 group-hover:text-primary">
                            {{ $myQData['agreement'] ? 'Setuju' : 'Tidak Setuju' }}
                            </br>
                            Wali :{{ $myQData['wali'] }}
                            </br>
                            Saksi :{{ $myQData['saksi'] }}
                            </br>
                            Petugas : {{ $myQData['petugasPemeriksa'] }}
                            </br>
                            Tanggal : {{ $myQData['signatureSaksiDate'] }}
                            </br>
                        </td>




                        <td class="px-4 py-3 group-hover:bg-gray-100 group-hover:text-primary">
                            {{-- delete Modal --}}
                            xxx


                        </td>
                    </tr>
                @endforeach
            @endisset

        </tbody>
    </table>

</div>
