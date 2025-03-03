<div>
    <!-- Bagian SDKI -->
    <div class="w-full mb-4">
        <h1 class="mb-2 text-2xl font-bold">SDKI</h1>
        @foreach ($formEntryAsuhanKeperawatan['diagKepJson']['SDKI'] as $sdk)
            <div class="p-4 mb-4 border rounded">
                <h2 class="text-xl font-semibold">{{ $sdk['nama'] }} (ID: {{ $sdk['id'] }})</h2>
                <p>
                    <strong>Kategori:</strong> {{ $sdk['kategori'] }} |
                    <strong>Subkategori:</strong> {{ $sdk['subkategori'] }}
                </p>
                <p class="mb-2"><strong>Definisi:</strong> {{ $sdk['definisi'] }}</p>

                <h3 class="font-semibold">Penyebab</h3>
                <!-- Fisiologis -->
                <h4 class="font-medium">Fisiologis</h4>
                <ul class="ml-6 list-disc">
                    @foreach ($sdk['penyebab']['fisiologis'] as $item)
                        <li>{{ $item['desc'] }} (status: {{ $item['status'] }})</li>
                    @endforeach
                </ul>

                <!-- Psikologis (jika ada) -->
                @if (isset($sdk['penyebab']['psikologis']))
                    <h4 class="mt-2 font-medium">Psikologis</h4>
                    <ul class="ml-6 list-disc">
                        @foreach ($sdk['penyebab']['psikologis'] as $item)
                            <li>{{ $item['desc'] }} (status: {{ $item['status'] }})</li>
                        @endforeach
                    </ul>
                @endif

                <!-- Situasional -->
                @if (isset($sdk['penyebab']['situasional']))
                    <h4 class="mt-2 font-medium">Situasional</h4>
                    <ul class="ml-6 list-disc">
                        @foreach ($sdk['penyebab']['situasional'] as $item)
                            <li>{{ $item['desc'] }} (status: {{ $item['status'] }})</li>
                        @endforeach
                    </ul>
                @endif

                <!-- Gejala dan Tanda -->
                <h3 class="mt-2 font-semibold">Gejala dan Tanda</h3>
                <h4 class="font-medium">Mayor</h4>
                @if (count($sdk['gejalaTanda']['mayor']['subjektif']))
                    <p>Subjektif:</p>
                    <ul class="ml-6 list-disc">
                        @foreach ($sdk['gejalaTanda']['mayor']['subjektif'] as $item)
                            <li>{{ $item['desc'] }} (status: {{ $item['status'] }})</li>
                        @endforeach
                    </ul>
                @endif
                <p>Objektif:</p>
                <ul class="ml-6 list-disc">
                    @foreach ($sdk['gejalaTanda']['mayor']['objektif'] as $item)
                        <li>{{ $item['desc'] }} (status: {{ $item['status'] }})</li>
                    @endforeach
                </ul>

                <h4 class="mt-2 font-medium">Minor</h4>
                @if (isset($sdk['gejalaTanda']['minor']['subjektif']))
                    <p>Subjektif:</p>
                    <ul class="ml-6 list-disc">
                        @foreach ($sdk['gejalaTanda']['minor']['subjektif'] as $item)
                            <li>{{ $item['desc'] }} (status: {{ $item['status'] }})</li>
                        @endforeach
                    </ul>
                @endif
                @if (isset($sdk['gejalaTanda']['minor']['objektif']))
                    <p>Objektif:</p>
                    <ul class="ml-6 list-disc">
                        @foreach ($sdk['gejalaTanda']['minor']['objektif'] as $item)
                            <li>{{ $item['desc'] }} (status: {{ $item['status'] }})</li>
                        @endforeach
                    </ul>
                @endif

                <!-- Kondisi Klinis Terkait -->
                <h3 class="mt-2 font-semibold">Kondisi Klinis Terkait</h3>
                <ul class="ml-6 list-disc">
                    @foreach ($sdk['kondisiKlinisTerkait'] as $item)
                        <li>{{ $item['desc'] }} (status: {{ $item['status'] }})</li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>

    <!-- Bagian SLKI -->
    <div class="w-full mb-4">
        <h1 class="mb-2 text-2xl font-bold">SLKI</h1>
        @foreach ($formEntryAsuhanKeperawatan['diagKepJson']['SLKI'] as $slki)
            <div class="p-4 mb-4 border rounded">
                <h2 class="text-xl font-semibold">{{ $slki['nama'] }} (ID: {{ $slki['id'] }})</h2>
                <p class="mb-2"><strong>Tujuan:</strong> {{ $slki['tujuan'] }}</p>

                <h3 class="font-semibold">Kriteria Hasil</h3>
                @foreach ($slki['kriteriaHasil'] as $kriteria)
                    <div class="mb-2">
                        <p>
                            <strong>Parameter:</strong> {{ $kriteria['parameter'] }} <br>
                            <em>{{ $kriteria['penjelasanSkala'] }}</em> <br>
                            Nilai awal: {{ $kriteria['penjelasanSkalaValue'] }}
                        </p>
                        <div class="grid grid-cols-5 gap-2 mt-2">
                            @foreach ($kriteria['scoringOptions'] as $option)
                                <!-- Menggunakan komponen x-radio-button untuk opsi -->
                                <x-radio-button :label="$option['desc']" value="{{ $option['desc'] }}"
                                    wire:model="selectedScores.{{ $kriteria['parameter'] }}" />
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>

    <!-- Bagian SIKI -->
    <div class="w-full mb-4">
        <h1 class="mb-2 text-2xl font-bold">SIKI</h1>
        @foreach ($formEntryAsuhanKeperawatan['diagKepJson']['SIKI'] as $siki)
            <div class="p-4 mb-4 border rounded">
                <h2 class="text-xl font-semibold">{{ $siki['nama'] }} (ID: {{ $siki['id'] }})</h2>
                <p class="mb-2"><strong>Definisi:</strong> {{ $siki['definisi'] }}</p>

                <h3 class="font-semibold">Tindakan</h3>
                <h4 class="font-medium">Observasi</h4>
                <ul class="ml-6 list-disc">
                    @foreach ($siki['tindakan']['observasi'] as $item)
                        <li>{{ $item['desc'] }} (status: {{ $item['status'] }})</li>
                    @endforeach
                </ul>

                <h4 class="mt-2 font-medium">Terapeutik</h4>
                <ul class="ml-6 list-disc">
                    @foreach ($siki['tindakan']['terapeutik'] as $item)
                        <li>{{ $item['desc'] }} (status: {{ $item['status'] }})</li>
                    @endforeach
                </ul>

                <h4 class="mt-2 font-medium">Edukasi</h4>
                <ul class="ml-6 list-disc">
                    @foreach ($siki['tindakan']['edukasi'] as $item)
                        <li>{{ $item['desc'] }} (status: {{ $item['status'] }})</li>
                    @endforeach
                </ul>

                <h4 class="mt-2 font-medium">Kolaborasi</h4>
                <ul class="ml-6 list-disc">
                    @foreach ($siki['tindakan']['kolaborasi'] as $item)
                        <li>{{ $item['desc'] }} (status: {{ $item['status'] }})</li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>

    <!-- Bagian Rasional -->
    <div class="w-full mb-4">
        <h1 class="mb-2 text-2xl font-bold">Rasional</h1>
        @foreach ($formEntryAsuhanKeperawatan['diagKepJson']['Rasional'] as $idIntervensi => $rasional)
            <div class="p-4 mb-4 border rounded">
                <h2 class="text-xl font-semibold">ID Intervensi: {{ $idIntervensi }}</h2>

                @if (isset($rasional['observasi']))
                    <h3 class="mt-2 font-medium">Observasi</h3>
                    <ul class="ml-6 list-disc">
                        @foreach ($rasional['observasi'] as $item)
                            <li>{{ $item['desc'] }} (status: {{ $item['status'] }})</li>
                        @endforeach
                    </ul>
                @endif

                @if (isset($rasional['terapeutik']))
                    <h3 class="mt-2 font-medium">Terapeutik</h3>
                    <ul class="ml-6 list-disc">
                        @foreach ($rasional['terapeutik'] as $item)
                            <li>{{ $item['desc'] }} (status: {{ $item['status'] }})</li>
                        @endforeach
                    </ul>
                @endif

                @if (isset($rasional['edukasi']))
                    <h3 class="mt-2 font-medium">Edukasi</h3>
                    <ul class="ml-6 list-disc">
                        @foreach ($rasional['edukasi'] as $item)
                            <li>{{ $item['desc'] }} (status: {{ $item['status'] }})</li>
                        @endforeach
                    </ul>
                @endif

                @if (isset($rasional['kolaborasi']) && count($rasional['kolaborasi']))
                    <h3 class="mt-2 font-medium">Kolaborasi</h3>
                    <ul class="ml-6 list-disc">
                        @foreach ($rasional['kolaborasi'] as $item)
                            <li>{{ $item['desc'] }} (status: {{ $item['status'] }})</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endforeach
    </div>
</div>
