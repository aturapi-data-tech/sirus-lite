<div>
    {{-- If you look to others for fulfillment, you will never truly be fulfilled. --}}
    <div id="DataPasien" class="sticky top-0 px-4 py-2 bg-white ">

        <div class="px-4 bg-white snap-mandatory snap-y">

            @php
                $pasieenTitle = 'Pasien RegNo : ' . $dataPasien['pasien']['regNo'];
            @endphp

            <div class="grid grid-cols-2 pl-3 bg-gray-100 rounded-lg">

                <div>
                    <div class="text-base font-semibold text-gray-700">
                        {{ $dataPasien['pasien']['regNo'] }}</div>
                    <div class="text-2xl font-semibold text-primary">
                        {{ strtoupper($dataPasien['pasien']['regName']) . ' / (' . $dataPasien['pasien']['jenisKelamin']['jenisKelaminDesc'] . ')' . ' / ' . $dataPasien['pasien']['thn'] }}
                    </div>
                    <div class="font-normal text-gray-900">
                        {{ $dataPasien['pasien']['identitas']['alamat'] }}
                    </div>
                </div>
                {{--  --}}
                <div class="grid">
                    <div class="px-2 font-semibold text-gray-700 justify-self-end">
                        {{ $dataDaftarRi['bangsalDesc'] ?? '-' }}
                        </br>
                        {{ $dataDaftarRi['roomDesc'] ?? '-' }}
                        {{ $dataDaftarRi['bedNo'] ?? '-' }}

                    </div>
                    <div class="px-2 font-semibold text-primary justify-self-end">
                        @php
                            $klaimId = isset($dataDaftarRi['klaimId']) ? $dataDaftarRi['klaimId'] : '-';
                        @endphp
                        {{ $dataDaftarRi['drDesc'] . ' / ' }}
                        {{ $klaimId == 'UM' ? 'UMUM' : ($klaimId == 'JM' ? 'BPJS' : ($klaimId == 'KR' ? 'Kronis' : 'Asuransi Lain')) }}
                    </div>
                    <div class="px-2 py-2 text-xs text-gray-700 justify-self-end">
                        {{ 'Tgl :' . $dataDaftarRi['entryDate'] }}
                    </div>
                </div>
            </div>




        </div>



    </div>


</div>
