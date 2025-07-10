<div>
    {{-- If you look to others for fulfillment, you will never truly be fulfilled. --}}
    <div id="DataPasien" class="sticky top-0 px-4 py-2 bg-white ">

        <div class="px-4 bg-white snap-mandatory snap-y">

            @php
                $pasieenTitle = 'Pasien RegNo : ' . $dataPasien['pasien']['regNo'];
                $datadaftar_json = $myQData;
                $klaimId = isset($myQData['klaimId']) ? $myQData['klaimId'] : '-';
            @endphp

            <div class="grid grid-cols-3 pl-3 bg-gray-100 rounded-lg">

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

                <div class="">
                    @include('livewire.emr-r-i.display-pasien.emr-r-i-leveling-dokter-table-display-pasien')
                </div>
                <div class="px-2 text-sm text-gray-900">
                    <p class="text-right">{{ $myQData['bangsalDesc'] ?? '-' }}</p>
                    <p class="font-semibold text-right">
                        {{ $myQData['roomDesc'] ?? '-' }} / Bed : {{ $myQData['bedNo'] ?? '-' }}
                    </p>
                    <p class="text-right">
                        Jenis Klaim:
                        {{ $klaimId == 'UM' ? 'UMUM' : ($klaimId == 'JM' ? 'BPJS' : ($klaimId == 'KR' ? 'Kronis' : 'Asuransi Lain')) }}
                        {{ $klaimId }}
                    </p>
                    <p class="text-right">
                        Tgl Masuk: {{ $myQData['entryDate'] }}
                    </p>
                </div>

            </div>




        </div>



    </div>


</div>
