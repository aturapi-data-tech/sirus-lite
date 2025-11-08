<div>
    {{-- If you look to others for fulfillment, you will never truly be fulfilled. --}}
    <div id="DataPasien" class="sticky top-0 px-4 py-2 bg-white ">

        <div class="px-4 bg-white snap-mandatory snap-y">

            @php
                $pasieenTitle = 'Pasien RegNo : ' . $dataPasien['pasien']['regNo'];
                $datadaftar_json = $dataDaftarRi;
                $klaimId = isset($dataDaftarRi['klaimId']) ? $dataDaftarRi['klaimId'] : '-';
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
                    <p class="text-right">{{ $dataDaftarRi['bangsalDesc'] ?? '-' }}</p>
                    <p class="font-semibold text-right">
                        {{ $dataDaftarRi['roomDesc'] ?? '-' }} / Bed : {{ $dataDaftarRi['bedNo'] ?? '-' }}
                        Jenis Klaim:
                        @php
                            use Illuminate\Support\Facades\DB;

                            // Ambil klaim dari database
                            $klaim = DB::table('rsmst_klaimtypes')
                                ->where('klaim_id', $klaimId ?? null)
                                ->select('klaim_status', 'klaim_desc')
                                ->first();

                            // Deskripsi klaim (fallback)
                            $klaimDesc = $klaim->klaim_desc ?? 'Asuransi Lain';

                            // Tentukan warna badge berdasarkan klaim_id
                            $badgecolorKlaim = match ($klaimId ?? '') {
                                'UM' => 'green',
                                'JM' => 'default',
                                'KR' => 'yellow',
                                default => 'red',
                            };
                        @endphp

                        <x-badge :badgecolor="__($badgecolorKlaim)">
                            {{ $klaimDesc }}
                        </x-badge>
                    </p>
                    <p class="text-right">
                        Tgl Masuk: {{ $dataDaftarRi['entryDate'] }}
                    </p>
                </div>

            </div>




        </div>



    </div>


</div>
