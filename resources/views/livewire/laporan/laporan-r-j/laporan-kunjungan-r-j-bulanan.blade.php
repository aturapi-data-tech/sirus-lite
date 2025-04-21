<div>



    <!-- Table -->
    <div class="mt-4">
        <div>
            <x-input-label :value="__('Jenis Kunjungan')" :required="__(true)" />
            <div class="grid grid-cols-8 gap-2 my-2">

                @foreach ($jenisKunjungan['jenisKunjunganOptions'] as $sRj)
                    {{-- @dd($sRj) --}}
                    <x-radio-button :label="__($sRj['jenisKunjunganDesc'])" value="{{ $sRj['jenisKunjunganId'] }}"
                        wire:model="jenisKunjungan.jenisKunjunganId" />
                @endforeach

                @if ($jenisKunjungan['jenisKunjunganId'] === '3')
                    <div class="mb-2">
                        <x-check-box value='1' :label="__('Post Inap')" wire:model="postInap" />
                    </div>
                @endif
            </div>

            {{-- Dokter --}}
            <div class="mb-2">
                {{-- LOV Dokter --}}
                @if (empty($collectingMyDokter))
                    @include('livewire.component.l-o-v.list-of-value-dokter.list-of-value-dokter')
                @else
                    <x-input-label for="myTopBar.drName" :value="__('Nama Dokter')" :required="__(true)" wire:click='resetDokter()' />
                    <div>
                        <x-text-input id="myTopBar.drName" placeholder="Nama Dokter" class="mt-1 ml-2" :errorshas="__($errors->has('myTopBar.drName'))"
                            wire:model="myTopBar.drName" :disabled="true" />

                    </div>
                @endif
            </div>





        </div>
        {{-- Loading Spinner --}}
        <table style="font-size: 12px" class="w-full border border-collapse table-auto">
            <thead class="text-gray-700 bg-gray-100">
                <tr>
                    <th class="w-1/3 px-2 py-1 border">Pasien</th>
                    <th class="w-1/3 px-2 py-1 border">Poli</th>
                    <th class="w-1/3 px-2 py-1 border">Status Layanan</th>
                </tr>
            </thead>
            <tbody class="text-xs text-gray-900">
                @foreach ($queryData ?? [] as $row)
                    @php
                        $datadaftar_json = json_decode($row->datadaftarpolirj_json, true);
                        $kunjunganId = $datadaftar_json['kunjunganId'] ?? null;

                        $jenisKunjunganDesc =
                            collect($jenisKunjungan['jenisKunjunganOptions'])->firstWhere(
                                'jenisKunjunganId',
                                $kunjunganId,
                            )['jenisKunjunganDesc'] ?? '-';

                        $postInap = $datadaftar_json['postInap'] ?? [];

                        if ($kunjunganId === '3' && in_array(1, $postInap)) {
                            $postInapDesc = 'Post Inap';
                        } else {
                            $postInapDesc = '';
                        }
                    @endphp
                    <tr class="border-b">
                        <!-- Kolom Pasien -->
                        <td class="w-1/3 px-2 py-1 align-top border">
                            <div class="font-bold text-gray-800">
                                {{ $row->reg_name }} / ({{ $row->sex }}) / {{ $row->thn }} th
                            </div>
                            <div class="text-gray-600">
                                No.Reg: {{ $row->reg_no }}<br>
                                Tgl Lahir: {{ $row->birth_date }}<br>
                                Alamat: {{ $row->address }}
                            </div>
                        </td>

                        <!-- Kolom Poli -->
                        <td class="w-1/3 px-2 py-1 align-top border">
                            <div class="font-bold text-primary">{{ $row->poli_desc }}</div>
                            <div class="text-gray-800">Dokter: {{ $row->dr_name }}</div>
                            <div>
                                Klaim:
                                @if ($row->klaim_id == 'UM')
                                    UMUM
                                @elseif ($row->klaim_id == 'JM')
                                    BPJS
                                @elseif ($row->klaim_id == 'KR')
                                    Kronis
                                @else
                                    Asuransi Lain
                                @endif
                                <span>{{ '( ' . $jenisKunjunganDesc . ' ' . $postInapDesc . ' )' }}</span>
                            </div>
                            <div class="text-gray-600">SEP: {{ $row->vno_sep }}</div>
                        </td>

                        <!-- Kolom Status Layanan -->
                        <td class="w-1/3 px-2 py-1 align-top border">
                            <div class="font-bold text-primary">
                                {{ $row->rj_date }}
                            </div>
                            <div>
                                Status:
                                @switch($row->rj_status)
                                    @case('A')
                                        Pelayanan
                                    @break

                                    @case('L')
                                        Selesai Pelayanan
                                    @break

                                    @case('I')
                                        Transfer Inap
                                    @break

                                    @case('F')
                                        Batal Transaksi
                                    @break

                                    @default
                                        -
                                @endswitch
                            </div>
                            <div class="text-gray-600">Booking: {{ $row->nobooking }}</div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if ($queryData->count() == 0)
            <div class="w-full p-4 text-sm text-center text-gray-900 dark:text-gray-400">
                {{ 'Data Tidak ditemukan' }}
            </div>
        @endif

    </div>

    {{ $queryData->links() }}
</div>
