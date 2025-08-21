<div class="fixed inset-0 z-40">

    <div class="">

        <!-- This element is to trick the browser into transition-opacity. -->
        <div class="fixed inset-0 transition-opacity">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <!-- This element is to trick the browser into transition-opacity. Body-->
        <div class="fixed inset-0 transition-opacity">
            <div class="absolute overflow-auto bg-white rounded-t-lg inset-4">

                {{-- Topbar --}}
                <div
                    class="sticky top-0 flex items-center justify-between p-4 bg-opacity-75 border-b rounded-t-lg bg-primary">

                    <!-- myTitle-->
                    <h3 class="w-full text-2xl font-semibold text-white ">
                        {{ $myTitle }}
                    </h3>

                    {{-- rjDate & Shift Input Rj --}}
                    <div id="shiftTanggal" class="flex justify-end w-full mr-4">


                        {{-- Close Modal --}}
                        <button wire:click="closeModalLayanan()"
                            class="text-gray-400 bg-gray-50 hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                {{-- Content --}}
                <div class="bg-white ">
                    <div class="mx-[200px]">
                        <table class="w-full table-auto">
                            <tbody>
                                <tr>
                                    <td class="w-1/4 text-xs text-center border-2 border-gray-300">
                                        <img src="madinahlogopersegi.png" class="object-fill h-32 mx-auto">
                                        {{-- {!! $myQueryIdentitas->int_name . '</br>' !!} --}}
                                        {!! $myQueryIdentitas->int_address . '</br>' !!}
                                        {!! $myQueryIdentitas->int_city . '</br>' !!}
                                        {!! $myQueryIdentitas->int_phone1 . '</br>' !!}
                                        {!! $myQueryIdentitas->int_phone2 . '</br>' !!}
                                        {!! $myQueryIdentitas->int_fax . '</br>' !!}
                                    </td>
                                    <td class="w-3/4 text-sm border-2 border-gray-300 text-start">
                                        <div>
                                            <table class="w-full table-auto">
                                                <tbody>
                                                    <tr>

                                                        <td class="p-1 m-1">Nama Pasien</td>
                                                        <td class="p-1 m-1">:</td>
                                                        <td class="p-1 m-1 text-sm font-semibold">
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
                                                        <td class="p-1 m-1"></td>
                                                        <td class="p-1 m-1">-</td>
                                                        <td class="p-1 m-1">Dokter Pengirim</td>
                                                        <td class="p-1 m-1">:</td>
                                                        <td class="p-1 m-1">
                                                            {{ isset($dataDaftarTxnHeader['dr_name']) ? $dataDaftarTxnHeader['dr_name'] : '-' }}

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
                                                            {{ isset($dataDaftarTxnHeader['checkup_date']) ? $dataDaftarTxnHeader['checkup_date'] : '-' }}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                    </div>

                    {{-- hasil pemeriksaan laborat --}}
                    <div class="mx-[200px]">
                        <table class="w-full table-auto">
                            <tbody>
                                <tr>
                                    <td
                                        class="grid grid-cols-2 gap-4 p-2 m-2 text-2xl font-semibold text-center uppercase border-b-2 border-l-2 border-r-2 border-gray-300">
                                        hasil pemeriksaan laborat
                                        <x-primary-button wire:click="emitSelectedRowsText" wire:loading.attr="disabled"
                                            wire:target="emitSelectedRowsText" :disabled="empty($selectedRows)">
                                            Kirim hasil lab ke Assessment Dokter
                                        </x-primary-button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    {{-- assesment --}}

                    {{-- Pengkajian peerawatan --}}
                    <div class="mx-[200px]">
                        <table class="w-full text-sm text-center table-auto ">
                            <thead class="border-l-2 border-r-2 border-gray-300 ">
                                <tr>
                                    <th class="p-1 m-1">Jenis Pemeriksaan</th>
                                    <th class="p-1 m-1"></th>
                                    <th class="p-1 m-1">Hasil Lab</th>
                                    <th class="p-1 m-1">Normal</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Data Internal --}}
                                @foreach ($dataDaftarTxn as $txn)
                                    @php
                                        $propertyUpNormal = data_get($txn, 'lab_result_status')
                                            ? 'bg-red-50 text-red-700 font-semibold'
                                            : '';

                                        $jenisKelamin =
                                            $dataPasien['pasien']['jenisKelamin']['jenisKelaminDesc'] ?? '-';

                                        if (data_get($txn, 'lowhigh_status') === 'Y') {
                                            $unit_convert = data_get($txn, 'unit_convert', 1) ?: 1;

                                            $lab_result = data_get($txn, 'lab_result', 0) * $unit_convert;
                                            $lab_result =
                                                fmod($lab_result, 1) !== 0.0 ? $lab_result : number_format($lab_result);

                                            $low_limit_m = data_get($txn, 'low_limit_m', 0) * $unit_convert;
                                            $low_limit_f = data_get($txn, 'low_limit_f', 0) * $unit_convert;
                                            $high_limit_m = data_get($txn, 'high_limit_m', 0) * $unit_convert;
                                            $high_limit_f = data_get($txn, 'high_limit_f', 0) * $unit_convert;

                                            $low_limit_m =
                                                fmod($low_limit_m, 1) !== 0.0
                                                    ? $low_limit_m
                                                    : number_format($low_limit_m);
                                            $low_limit_f =
                                                fmod($low_limit_f, 1) !== 0.0
                                                    ? $low_limit_f
                                                    : number_format($low_limit_f);
                                            $high_limit_m =
                                                fmod($high_limit_m, 1) !== 0.0
                                                    ? $high_limit_m
                                                    : number_format($high_limit_m);
                                            $high_limit_f =
                                                fmod($high_limit_f, 1) !== 0.0
                                                    ? $high_limit_f
                                                    : number_format($high_limit_f);
                                        } else {
                                            $lab_result = data_get($txn, 'lab_result');
                                            $low_limit_m = data_get($txn, 'low_limit_m');
                                            $low_limit_f = data_get($txn, 'low_limit_f');
                                            $high_limit_m = data_get($txn, 'high_limit_m');
                                            $high_limit_f = data_get($txn, 'high_limit_f');
                                        }

                                        // definisikan variabel dulu
                                        $id = data_get($txn, 'clabitem_id');
                                        $desc = data_get($txn, 'clabitem_desc');
                                        $hasil = $lab_result ?? data_get($txn, 'lab_result');
                                        $unit = data_get($txn, 'unit_desc');

                                        // Cek abnormal
                                        $isAbnormal = data_get($txn, 'lab_result_status') ? true : false;

                                        // Cek apakah row ini selected
                                        $isSelected = collect($selectedRows)->contains(fn($row) => $row['id'] == $id);

                                        // Tentukan warna row
                                        $rowClass = $isSelected
                                            ? 'bg-blue-200 font-bold'
                                            : ($isAbnormal
                                                ? 'bg-red-50 text-red-700 font-semibold'
                                                : '');
                                    @endphp

                                    <tr x-on:click="$wire.rowSelected(@js(compact('id', 'desc', 'hasil', 'unit')))"
                                        class="cursor-pointer hover:bg-blue-100 border-b border-gray-300 border-l-2 border-r-2 {{ $rowClass }}">
                                        <td class="px-3 py-2 text-left">
                                            {{ data_get($txn, 'clabitem_id') }}/
                                            {{ data_get($txn, 'clabitem_desc') }}
                                        </td>
                                        <td class="px-3 py-2 text-center">{{ data_get($txn, 'lab_result_status') }}
                                        </td>
                                        <td class="px-3 py-2 text-center">{{ $lab_result }}
                                            {{ data_get($txn, 'unit_desc') }}</td>
                                        <td class="px-3 py-2 text-center">
                                            @if ($jenisKelamin == 'L')
                                                {{ $low_limit_m }} - {{ $high_limit_m }}
                                                {{ data_get($txn, 'unit_desc') }}
                                            @else
                                                {{ $low_limit_f }} - {{ $high_limit_f }}
                                                {{ data_get($txn, 'unit_desc') }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach


                                {{-- Data Luar --}}
                                @foreach ($dataDaftarTxnLuar as $txnLuar)
                                    @php
                                        $isAbnormal =
                                            data_get($txnLuar, 'labout_result') != data_get($txnLuar, 'labout_normal');
                                        $txnLuarLab = [
                                            'id' => data_get($txnLuar, 'labout_desc'), // pakai desc sebagai ID unik
                                            'desc' => data_get($txnLuar, 'labout_desc'),
                                            'hasil' => data_get($txnLuar, 'labout_result'),
                                            'unit' => '-', // data luar tidak ada unit → isi default '-'
                                        ];

                                        $isSelected = in_array($payload['id'], $selectedRows ?? []);
                                        $rowClass = $isSelected
                                            ? 'bg-blue-200 font-bold' // style kalau selected
                                            : ($isAbnormal
                                                ? 'bg-red-50 text-red-700 font-semibold'
                                                : '');
                                    @endphp

                                    <tr x-on:click="$wire.rowSelected(@js($txnLuarLab))"
                                        class="cursor-pointer hover:bg-blue-100 border-b border-gray-300 border-l-2 border-r-2 {{ $rowClass }}">
                                        <td class="px-3 py-2 text-left">{{ data_get($txnLuar, 'labout_desc') }}</td>
                                        <td class="px-3 py-2 text-center">-</td>
                                        <td class="px-3 py-2 text-center">{{ data_get($txnLuar, 'labout_result') }}
                                        </td>
                                        <td class="px-3 py-2 text-center">{{ data_get($txnLuar, 'labout_normal') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mx-[200px]">
                        <table class="w-full mt-4 text-sm text-center table-auto">

                            <tbody>

                                <tr class="border-l-2 border-r-2 border-gray-300 ">
                                    <td class="p-1 m-1 text-left">
                                        <p class="font-semibold">{{ 'Kesimpulan' }}</p>
                                        {{ isset($dataDaftarTxnHeader['checkup_kesimpulan']) ? $dataDaftarTxnHeader['checkup_kesimpulan'] : '-' }}
                                    </td>


                                </tr>
                            </tbody>
                        </table>
                    </div>


                    <div class="mx-[200px]">
                        <table class="w-full mt-4 text-sm text-center table-auto">

                            <tbody>

                                <tr class="border-l-2 border-r-2 border-gray-300 ">
                                    <td class="p-1 m-1 text-left">
                                        {{ 'Penanggung Jawab Laboratorium' }}
                                        <br>
                                        <div class="pl-[80px]">
                                            {!! DNS2D::getBarcodeHTML(
                                                isset($dataDaftarTxnHeader['dr_penanggungjawab']) ? $dataDaftarTxnHeader['dr_penanggungjawab'] : '-',
                                                'QRCODE',
                                                2,
                                                2,
                                            ) !!}
                                        </div>
                                        {{ isset($dataDaftarTxnHeader['dr_penanggungjawab']) ? $dataDaftarTxnHeader['dr_penanggungjawab'] : '-' }}
                                    </td>
                                    <td class="p-1 m-1">
                                        {{ 'Ngunut, ' }}{{ isset($dataDaftarTxnHeader['checkup_date']) ? $dataDaftarTxnHeader['checkup_date'] : '-' }}
                                        <br>
                                        {{ 'Pemeriksa' }}
                                        <br>
                                        <div class="pl-[230px]">
                                            {!! DNS2D::getBarcodeHTML(
                                                isset($dataDaftarTxnHeader['emp_name']) ? $dataDaftarTxnHeader['emp_name'] : '-',
                                                'QRCODE',
                                                2,
                                                2,
                                            ) !!}
                                        </div>
                                        {{ isset($dataDaftarTxnHeader['emp_name']) ? $dataDaftarTxnHeader['emp_name'] : '-' }}
                                        <br>
                                        {{ 'Selesai Pemeriksaan ' }}{{ isset($dataDaftarTxnHeader['waktu_selesai_pelayanan']) ? $dataDaftarTxnHeader['waktu_selesai_pelayanan'] : '-' }}
                                    </td>

                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- End Content --}}



                </div>

            </div>

        </div>
