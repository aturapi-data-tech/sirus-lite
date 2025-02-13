<div>
    <div class="w-full mb-1">
        <table class="w-full text-sm text-left text-gray-900 table-auto dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="text-center">Tanggal / Jam </th>
                    <th scope="col" class="text-center">Model Penggunaan - Durasi Penggunaan</th>
                    <th scope="col" class="text-center">Jenis Alat Oksigen</th>
                    <th scope="col" class="text-center">Dosis Oksigen</th>
                    <th scope="col" class="text-center">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800">
                @php
                    use Carbon\Carbon;

                    $sortedPemakaianOksigen = collect(
                        $dataDaftarRi['observasi']['pemakaianOksigen']['pemakaianOksigenData'] ?? [],
                    )->sortByDesc(function ($item) {
                        return Carbon::createFromFormat('d/m/Y H:i:s', $item['tanggalWaktuMulai'], env('APP_TIMEZONE'));
                    });
                @endphp

                @foreach ($sortedPemakaianOksigen ?? [] as $key => $pemakaian)
                    <tr class="border-b group dark:border-gray-700">
                        <td class="text-center group-hover:bg-gray-100 group-hover:text-primary">
                            Mulai :{{ $pemakaian['tanggalWaktuMulai'] }}
                            </br>
                            @if (!$pemakaian['tanggalWaktuSelesai'])
                                <div class="">
                                    <x-green-button :disabled=false
                                        wire:click.prevent="setTanggalWaktuSelesai('{{ $key }}','{{ date('d/m/Y H:i:s') }}')"
                                        type="button">
                                        Set Tanggal Waktu Selesai: {{ date('d/m/Y H:i:s') }}
                                    </x-green-button>
                                </div>
                            @else
                                {{-- {{ $pemakaian['tanggalWaktuSelesai'] }} --}}
                                <div class="flex flex-row justify-center">
                                    <div>
                                        Selesai :
                                    </div>
                                    <x-text-input
                                        id="dataDaftarRi.observasi.pemakaianOksigen.pemakaianOksigenData.{{ $key }}.tanggalWaktuSelesai"
                                        placeholder="Tanggal [ dd/mm/yyyy hh24:mi:ss ]" class="mt-1 ml-2 sm:w-40"
                                        :errorshas="__($errors->has('tanggalWaktuSelesai'))" :disabled=false
                                        value="{{ $pemakaian['tanggalWaktuSelesai'] }}" x-data
                                        x-on:keydown.enter="$wire.updateTanggalWaktuSelesai({{ $key }}, '{{ $pemakaian['tanggalWaktuMulai'] }}', $event.target.value)" />
                                </div>
                        </td>
                        <td class="text-center group-hover:bg-gray-100 group-hover:text-primary">
                @endif

                {{ $pemakaian['modelPenggunaan'] }}
                </br>
                {{ $pemakaian['durasiPenggunaan'] }}



                </td>
                <td class="text-center group-hover:bg-gray-100 group-hover:text-primary">
                    {{ $pemakaian['jenisAlatOksigen'] }}
                    @if ($pemakaian['jenisAlatOksigen'] === 'Lainnya')
                        <br> <span class="text-xs text-gray-500">({{ $pemakaian['jenisAlatOksigenDetail'] }})</span>
                    @endif
                </td>
                <td class="text-center group-hover:bg-gray-100 group-hover:text-primary">
                    {{ $pemakaian['dosisOksigen'] }}
                    @if ($pemakaian['dosisOksigen'] === 'Lainnya')
                        <br> <span class="text-xs text-gray-500">({{ $pemakaian['dosisOksigenDetail'] }})</span>
                    @endif
                </td>
                <td class="text-center group-hover:bg-gray-100 group-hover:text-primary">
                    <x-alternative-button class="inline-flex"
                        wire:click.prevent="removePemakaianOksigen('{{ $pemakaian['tanggalWaktuMulai'] }}')">
                        <svg class="w-5 h-5 text-gray-800" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            fill="currentColor" viewBox="0 0 18 20">
                            <path
                                d="M17 4h-4V2a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v2H1a1 1 0 0 0 0 2h1v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6h1a1 1 0 1 0 0-2ZM7 2h4v2H7V2Zm1 14a1 1 0 1 1-2 0V8a1 1 0 0 1 2 0v8Zm4 0a1 1 0 0 1-2 0V8a1 1 0 0 1 2 0v8Z" />
                        </svg>
                        Hapus
                    </x-alternative-button>
                </td>
                </tr>
                @endforeach

            </tbody>
        </table>
    </div>
</div>
