<div class="fixed inset-0 z-40 flex items-center justify-center bg-gray-500/75">
    <div class="relative w-full max-w-6xl max-h-[95vh] overflow-y-auto bg-white rounded-lg shadow-xl">
        {{-- Topbar --}}
        <div class="sticky top-0 z-10 flex items-center justify-between p-4 text-white border-b rounded-t-lg bg-primary">
            <h3 class="text-2xl font-semibold">{{ $myTitle }}</h3>
            <button wire:click="closeModalLayanan()"
                class="text-white bg-gray-100/20 hover:bg-gray-200 hover:text-gray-900 rounded-lg p-1.5">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293
                        4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293
                        4.293a1 1 0 01-1.414-1.414L8.586 10
                        4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>

        {{-- BODY --}}
        <div class="h-full overflow-y-auto bg-white">

            {{-- IDENTITAS PASIEN --}}
            <div class="px-4 pt-4">
                <div class="grid gap-3 p-3 border border-gray-900 rounded-lg md:grid-cols-4">
                    <div class="flex flex-col items-center justify-start text-center md:col-span-1">
                        <img src="madinahlogopersegi.png" alt="Logo" class="object-contain h-28" />
                        <div class="mt-2 text-xs leading-5">
                            {{-- {!! $myQueryIdentitas->int_name . '</br>' !!} --}}
                            {!! ($myQueryIdentitas->int_address ?? '-') . '</br>' !!}
                            {!! ($myQueryIdentitas->int_city ?? '-') . '</br>' !!}
                            {!! ($myQueryIdentitas->int_phone1 ?? '-') . '</br>' !!}
                            {!! ($myQueryIdentitas->int_phone2 ?? '-') . '</br>' !!}
                            {!! ($myQueryIdentitas->int_fax ?? '-') . '</br>' !!}
                        </div>
                    </div>

                    <div class="md:col-span-3">
                        <div class="text-sm print:text-[11px]">
                            <dl class="divide-y divide-gray-100/60">
                                <!-- Nama Pasien -->
                                <div class="flex items-baseline gap-2 py-0.5">
                                    <dt class="w-32 shrink-0  after:content-[':'] after:ml-1">Nama Pasien
                                    </dt>
                                    <dd
                                        class="flex-1 min-w-0 font-semibold truncate print:whitespace-normal print:overflow-visible">
                                        {{ strtoupper($dataPasien['pasien']['regName'] ?? '-') }}
                                        <span class="font-normal">
                                            / {{ $dataPasien['pasien']['jenisKelamin']['jenisKelaminDesc'] ?? '-' }}
                                            / {{ $dataPasien['pasien']['thn'] ?? '-' }}
                                        </span>
                                    </dd>
                                </div>

                                <!-- No RM -->
                                <div class="flex items-baseline gap-2 py-0.5">
                                    <dt class="w-32 shrink-0  after:content-[':'] after:ml-1">No RM</dt>
                                    <dd
                                        class="flex-1 min-w-0 text-lg font-semibold truncate tabular-nums print:whitespace-normal print:overflow-visible">
                                        {{ $dataPasien['pasien']['regNo'] ?? '-' }}
                                    </dd>
                                </div>

                                <!-- Tgl Lahir • NIK (2 kolom) -->
                                <div class="flex flex-col gap-1 mt-1 md:flex-row md:gap-8">
                                    <dl class="flex-1 space-y-1">
                                        <div class="flex items-baseline gap-2 py-0.5">
                                            <dt class="w-32 shrink-0  after:content-[':'] after:ml-1">Tanggal
                                                Lahir</dt>
                                            <dd
                                                class="flex-1 min-w-0 truncate print:whitespace-normal print:overflow-visible">
                                                {{ $dataPasien['pasien']['tglLahir'] ?? '-' }}
                                            </dd>
                                        </div>
                                    </dl>

                                    <dl class="flex-1 space-y-1">
                                        <div class="flex items-baseline gap-2 py-0.5">
                                            <dt class="w-20 shrink-0  after:content-[':'] after:ml-1">NIK</dt>
                                            <dd
                                                class="flex-1 min-w-0 break-words md:truncate print:whitespace-normal print:overflow-visible">
                                                {{ $dataPasien['pasien']['identitas']['nik'] ?? '-' }}
                                            </dd>
                                        </div>
                                    </dl>
                                </div>

                                <!-- Alamat (full width) -->
                                <div class="mt-1">
                                    <div class="flex items-baseline gap-2 py-0.5">
                                        <dt class="w-32 shrink-0  after:content-[':'] after:ml-1">Alamat</dt>
                                        <dd class="flex-1 min-w-0 break-words print:whitespace-normal">
                                            {{ $dataPasien['pasien']['identitas']['alamat'] ?? '-' }}
                                        </dd>
                                    </div>
                                </div>

                                <!-- ID BPJS • Klaim (2 kolom) -->
                                <div class="flex flex-col gap-1 mt-1 md:flex-row md:gap-8">
                                    <dl class="flex-1 space-y-1">
                                        <div class="flex items-baseline gap-2 py-0.5">
                                            <dt class="w-32 shrink-0  after:content-[':'] after:ml-1">ID BPJS
                                            </dt>
                                            <dd
                                                class="flex-1 min-w-0 truncate print:whitespace-normal print:overflow-visible">
                                                {{ $dataPasien['pasien']['identitas']['idbpjs'] ?? '-' }}
                                            </dd>
                                        </div>
                                    </dl>

                                    <dl class="flex-1 space-y-1">
                                        <div class="flex items-baseline gap-2 py-0.5">
                                            <dt class="w-20 shrink-0  after:content-[':'] after:ml-1">Klaim
                                            </dt>
                                            <dd class="flex-1 min-w-0">
                                                @php $k = $dataDaftarTxn['klaimId'] ?? null; @endphp
                                                {{ $k === 'UM' ? 'UMUM' : ($k === 'JM' ? 'BPJS' : ($k === 'KR' ? 'Kronis' : ($k ? 'Asuransi Lain' : '-'))) }}
                                            </dd>
                                        </div>
                                    </dl>
                                </div>

                                <!-- Tanggal Masuk -->
                                <div class="mt-1">
                                    <div class="flex items-baseline gap-2 py-0.5">
                                        <dt class="w-32 shrink-0  after:content-[':'] after:ml-1">Tanggal Masuk
                                        </dt>
                                        <dd
                                            class="flex-1 min-w-0 truncate print:whitespace-normal print:overflow-visible">
                                            {{ $dataDaftarTxn['rjDate'] ?? '-' }}
                                        </dd>
                                    </div>
                                </div>
                            </dl>
                        </div>

                    </div>

                </div>
            </div>

            {{-- SECTION TITLE --}}
            <div class="px-4 mt-4">
                <div
                    class="px-3 py-2 text-2xl font-semibold text-center uppercase border-t border-gray-900 rounded-t-md border-x bg-gray-50">
                    assesment awal instalasi gawat darurat
                </div>
            </div>

            {{-- PENGKAJIAN PERAWAT + TRIASE --}}
            @php
                $triase = data_get($dataDaftarTxn, 'anamnesa.pengkajianPerawatan.tingkatKegawatan') ?? 'P0';
                $triaseBg = match ($triase) {
                    'P1' => 'bg-red-500 text-white',
                    'P2' => 'bg-yellow-400',
                    'P3' => 'bg-green-500 text-white',
                    'P0' => 'bg-gray-400 text-white',
                    default => 'bg-white',
                };
                $b = fn($v) => $v ?? false ? 'Ya' : '-';
            @endphp
            <div class="px-4">
                <div class="border-b border-gray-900 rounded-b-md border-x">
                    <div class="grid gap-0 md:grid-cols-4">
                        <div class="p-3 text-sm font-semibold uppercase {{ $triaseBg }}">
                            pengkajian perawat
                            <div class="mt-1 text-xs font-normal opacity-90">
                                Cara Masuk IGD:
                                {{ data_get($dataDaftarTxn, 'anamnesa.pengkajianPerawatan.caraMasukIgd') ?? '-' }} /
                                Triase: {{ $triase }}
                            </div>
                        </div>
                        <div class="p-3 text-sm leading-6 md:col-span-3">
                            <span class="font-semibold">Status Psikologis :</span>
                            {{ $b(data_get($dataDaftarTxn, 'anamnesa.statusPsikologis.tidakAdaKelainan')) === 'Ya' ? 'Tidak ada kelainan' : '-' }}
                            /
                            {{ $b(data_get($dataDaftarTxn, 'anamnesa.statusPsikologis.marah')) === 'Ya' ? 'Marah' : '-' }}
                            /
                            {{ $b(data_get($dataDaftarTxn, 'anamnesa.statusPsikologis.ccemas')) === 'Ya' ? 'Cemas' : '-' }}
                            /
                            {{ $b(data_get($dataDaftarTxn, 'anamnesa.statusPsikologis.takut')) === 'Ya' ? 'Takut' : '-' }}
                            /
                            {{ $b(data_get($dataDaftarTxn, 'anamnesa.statusPsikologis.sedih')) === 'Ya' ? 'Sedih' : '-' }}
                            /
                            {{ $b(data_get($dataDaftarTxn, 'anamnesa.statusPsikologis.cenderungBunuhDiri')) === 'Ya' ? 'Resiko Bunuh Diri' : '-' }}
                            /
                            <span class="font-semibold">Keterangan :</span>
                            {{ data_get($dataDaftarTxn, 'anamnesa.statusPsikologis.sebutstatusPsikologis') ?? '-' }}
                            <br>
                            <span class="font-semibold">Status Mental :</span>
                            {{ data_get($dataDaftarTxn, 'anamnesa.statusMental.statusMental') ?? '-' }} /
                            <span class="font-semibold">Keterangan :</span>
                            {{ data_get($dataDaftarTxn, 'anamnesa.statusMental.sebutstatusPsikologis') ?? '-' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- ANAMNESA & PEMERIKSAAN FISIK --}}
            <div class="grid gap-0 px-4 mt-4 md:grid-cols-4">
                {{-- kiri 3/4 --}}
                <div class="border border-gray-900 md:col-span-3 rounded-l-md">
                    <div class="text-sm divide-y divide-gray-200">
                        {{-- Anamnesa: Keluhan Utama --}}
                        <div class="grid grid-cols-4 gap-2 p-3">
                            <div class="col-span-1 font-semibold uppercase">Anamnesa</div>
                            <div class="col-span-1">Keluhan Utama :</div>
                            <div class="col-span-2 break-words whitespace-pre-line">
                                {!! nl2br(e(data_get($dataDaftarTxn, 'anamnesa.keluhanUtama.keluhanUtama') ?? '-')) !!}
                            </div>
                        </div>

                        {{-- Screening Batuk --}}
                        <div class="grid grid-cols-4 gap-2 p-3">
                            <div></div>
                            <div>Screening Batuk :</div>
                            <div class="col-span-2">
                                @php $batuk = data_get($dataDaftarTxn,'anamnesa.batuk', []); @endphp
                                @if (data_get($batuk, 'riwayatDemam'))
                                    Riwayat Demam? : Ya / {{ data_get($batuk, 'keteranganriwayatDemam', '-') }}<br>
                                @endif
                                @if (data_get($batuk, 'berkeringatMlmHari'))
                                    Berkeringat Malam Tanpa Aktivitas? : Ya /
                                    {{ data_get($batuk, 'keteranganberkeringatMlmHari', '-') }}<br>
                                @endif
                                @if (data_get($batuk, 'bepergianDaerahWabah'))
                                    Bepergian ke Daerah Wabah? : Ya /
                                    {{ data_get($batuk, 'KeteranganbepergianDaerahWabah', '-') }}<br>
                                @endif
                                @if (data_get($batuk, 'riwayatPakaiObatJangkaPanjangan'))
                                    Pemakaian Obat Jangka Panjang? : Ya /
                                    {{ data_get($batuk, 'keteranganriwayatPakaiObatJangkaPanjangan', '-') }}<br>
                                @endif
                                @if (data_get($batuk, 'BBTurunTanpaSebab'))
                                    BB Turun Tanpa Sebab? : Ya /
                                    {{ data_get($batuk, 'keteranganBBTurunTanpaSebab', '-') }}<br>
                                @endif
                                @if (data_get($batuk, 'pembesaranGetahBening'))
                                    Pembesaran KGB? : Ya /
                                    {{ data_get($batuk, 'keteranganpembesaranGetahBening', '-') }}<br>
                                @endif
                                -
                            </div>
                        </div>

                        {{-- Skala Nyeri --}}
                        <div class="grid grid-cols-4 gap-2 p-3">
                            <div></div>
                            <div>Skala Nyeri :</div>
                            <div class="col-span-2">
                                VAS : {{ data_get($dataDaftarTxn, 'penilaian.nyeri.vas.vas') ?? '-' }} /
                                Pencetus : {{ data_get($dataDaftarTxn, 'penilaian.nyeri.pencetus') ?? '-' }} /
                                Durasi : {{ data_get($dataDaftarTxn, 'penilaian.nyeri.durasi') ?? '-' }} /
                                Lokasi : {{ data_get($dataDaftarTxn, 'penilaian.nyeri.lokasi') ?? '-' }}
                            </div>
                        </div>

                        {{-- Resiko Jatuh --}}
                        <div class="grid grid-cols-4 gap-2 p-3">
                            <div></div>
                            <div>Resiko Jatuh :</div>
                            <div class="col-span-2">
                                Skala Humpty Dumpty / Total Skor :
                                {{ data_get($dataDaftarTxn, 'penilaian.resikoJatuh.skalaHumptyDumpty.skalaHumptyDumptyScore') ?? '-' }}
                                /
                                {{ data_get($dataDaftarTxn, 'penilaian.resikoJatuh.skalaHumptyDumpty.skalaHumptyDumptyDesc') ?? '-' }}
                                <br>
                                Skala Morse / Total Skor :
                                {{ data_get($dataDaftarTxn, 'penilaian.resikoJatuh.skalaMorse.skalaMorseScore') ?? '-' }}
                                /
                                {{ data_get($dataDaftarTxn, 'penilaian.resikoJatuh.skalaMorse.skalaMorseDesc') ?? '-' }}
                            </div>
                        </div>

                        {{-- Riwayat + Alergi --}}
                        <div class="grid grid-cols-4 gap-2 p-3">
                            <div></div>
                            <div>Riwayat Penyakit Sekarang :</div>
                            <div class="col-span-2 break-words whitespace-pre-line">
                                {!! nl2br(
                                    e(data_get($dataDaftarTxn, 'anamnesa.riwayatPenyakitSekarangUmum.riwayatPenyakitSekarangUmum') ?? '-'),
                                ) !!}
                            </div>
                        </div>
                        <div class="grid grid-cols-4 gap-2 p-3">
                            <div></div>
                            <div>Riwayat Penyakit Dahulu :</div>
                            <div class="col-span-2 break-words whitespace-pre-line">
                                {!! nl2br(e(data_get($dataDaftarTxn, 'anamnesa.riwayatPenyakitDahulu.riwayatPenyakitDahulu') ?? '-')) !!}
                            </div>
                        </div>
                        <div class="grid grid-cols-4 gap-2 p-3">
                            <div></div>
                            <div>Alergi :</div>
                            <div class="col-span-2 break-words whitespace-pre-line">
                                {!! nl2br(e(data_get($dataDaftarTxn, 'anamnesa.alergi.alergi') ?? '-')) !!}
                            </div>
                        </div>

                        {{-- Rekonsiliasi Obat --}}
                        <div class="grid grid-cols-4 gap-2 p-3">
                            <div></div>
                            <div>Rekonsiliasi Obat :</div>
                            <div class="col-span-2">
                                <div class="overflow-hidden rounded-md ring-1 ring-gray-300">
                                    <table class="w-full table-fixed">
                                        <thead class="text-xs uppercase bg-gray-50">
                                            <tr class="divide-x divide-gray-200">
                                                <th class="px-2 py-1 text-left">Nama Obat</th>
                                                <th class="px-2 py-1 text-left">Dosis</th>
                                                <th class="px-2 py-1 text-left">Rute</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-sm divide-y divide-gray-200">
                                            @foreach (data_get($dataDaftarTxn, 'anamnesa.rekonsiliasiObat', []) as $rek)
                                                <tr class="divide-x divide-gray-200">
                                                    <td class="px-2 py-1">{{ data_get($rek, 'namaObat', '-') }}</td>
                                                    <td class="px-2 py-1">{{ data_get($rek, 'dosis', '-') }}</td>
                                                    <td class="px-2 py-1">{{ data_get($rek, 'rute', '-') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- kanan 1/4 --}}
                <div class="border border-gray-900 md:col-span-1 rounded-r-md">
                    <div class="p-3 space-y-1 text-sm">
                        <div class="font-semibold uppercase">Perawat / Terapis :</div>
                        <div class="text-center">
                            @php
                                $imgPerawat =
                                    optional(
                                        App\Models\User::where(
                                            'myuser_code',
                                            data_get(
                                                $dataDaftarTxn,
                                                'anamnesa.pengkajianPerawatan.perawatPenerimaCode',
                                            ),
                                        )->first(),
                                    )->myuser_ttd_image ?? null;
                            @endphp
                            @if ($imgPerawat)
                                <img class="h-24 mx-auto" src="{{ asset('storage/' . $imgPerawat) }}"
                                    alt="TTD Perawat" />
                            @endif
                            <div class="mt-2 text-xs italic text-gray-500">ttd</div>
                            <div class="font-semibold">
                                {{ strtoupper(data_get($dataDaftarTxn, 'anamnesa.pengkajianPerawatan.perawatPenerima') ?? 'Perawat Penerima') }}
                            </div>
                        </div>

                        <div class="pt-2 font-semibold uppercase">Tanda Vital :</div>
                        <div class="grid grid-cols-2 gap-x-3">
                            <div class="text-right">TD :</div>
                            <div>{{ data_get($dataDaftarTxn, 'pemeriksaan.tandaVital.sistolik', '-') }} /
                                {{ data_get($dataDaftarTxn, 'pemeriksaan.tandaVital.distolik', '-') }} mmHg</div>

                            <div class="text-right">Nadi :</div>
                            <div>{{ data_get($dataDaftarTxn, 'pemeriksaan.tandaVital.frekuensiNadi', '-') }} x/mnt
                            </div>

                            <div class="text-right">Suhu :</div>
                            <div>{{ data_get($dataDaftarTxn, 'pemeriksaan.tandaVital.suhu', '-') }} °C</div>

                            <div class="text-right">Pernafasan :</div>
                            <div>{{ data_get($dataDaftarTxn, 'pemeriksaan.tandaVital.frekuensiNafas', '-') }} x/mnt
                            </div>

                            <div class="text-right">SPO₂ :</div>
                            <div>{{ data_get($dataDaftarTxn, 'pemeriksaan.tandaVital.spo2', '-') }} %</div>

                            <div class="text-right">GDA :</div>
                            <div>{{ data_get($dataDaftarTxn, 'pemeriksaan.tandaVital.gda', '-') }} mg/dL</div>
                        </div>

                        <div class="pt-2 font-semibold uppercase">Nutrisi :</div>
                        <div class="grid grid-cols-2 gap-x-3">
                            <div class="text-right">Berat Badan :</div>
                            <div>{{ data_get($dataDaftarTxn, 'pemeriksaan.nutrisi.bb', '-') }} kg</div>

                            <div class="text-right">Tinggi Badan :</div>
                            <div>{{ data_get($dataDaftarTxn, 'pemeriksaan.nutrisi.tb', '-') }} cm</div>

                            <div class="text-right">IMT :</div>
                            <div>{{ data_get($dataDaftarTxn, 'pemeriksaan.nutrisi.imt', '-') }} Kg/M²</div>

                            <div class="text-right">Lingkar Kepala :</div>
                            <div>{{ data_get($dataDaftarTxn, 'pemeriksaan.nutrisi.lk', '-') }} cm</div>

                            <div class="text-right">LILA :</div>
                            <div>{{ data_get($dataDaftarTxn, 'pemeriksaan.nutrisi.lila', '-') }} cm</div>

                            <div class="text-right">Lingkar Perut :</div>
                            <div>{{ data_get($dataDaftarTxn, 'pemeriksaan.nutrisi.liPerut', '-') }} cm</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KEADAAN UMUM --}}
            <div class="px-4 mt-4">
                <div
                    class="px-3 py-2 text-sm font-semibold uppercase border-t border-gray-900 rounded-t-md border-x bg-gray-50">
                    keadaan umum</div>
                <div class="px-3 py-2 text-sm text-center border-b border-gray-900 rounded-b-md border-x">
                    {{ data_get($dataDaftarTxn, 'pemeriksaan.tandaVital.keadaanUmum') ?? '-' }} /
                    <span class="font-semibold">Tingkat Kesadaran :</span>
                    {{ data_get($dataDaftarTxn, 'pemeriksaan.tandaVital.tingkatKesadaran') ?? '-' }}
                </div>
            </div>

            {{-- ABCD --}}
            <div class="px-4 mt-2">
                <div
                    class="px-3 py-2 text-sm font-semibold uppercase border-t border-gray-900 rounded-t-md border-x bg-gray-50">
                    ABCD (Airway, Breathing, Circulation, Disability)
                </div>
                <div class="px-3 py-2 text-sm text-center border-b border-gray-900 rounded-b-md border-x">
                    <span class="font-semibold">Jalan Nafas (A):</span>
                    {{ data_get($dataDaftarTxn, 'pemeriksaan.tandaVital.jalanNafas.jalanNafas') ?? '-' }} /
                    <span class="font-semibold">Pernafasan (B):</span>
                    {{ data_get($dataDaftarTxn, 'pemeriksaan.tandaVital.pernafasan.pernafasan') ?? '-' }}
                    <span class="font-semibold">Gerak Dada:</span>
                    {{ data_get($dataDaftarTxn, 'pemeriksaan.tandaVital.gerakDada.gerakDada') ?? '-' }}
                    <br>
                    <span class="font-semibold">Sirkulasi (C):</span>
                    {{ data_get($dataDaftarTxn, 'pemeriksaan.tandaVital.sirkulasi.sirkulasi') ?? '-' }} /
                    <span class="font-semibold">Neurologi (D):</span>
                    E: {{ data_get($dataDaftarTxn, 'pemeriksaan.tandaVital.e') ?? '-' }} -
                    V: {{ data_get($dataDaftarTxn, 'pemeriksaan.tandaVital.v') ?? '-' }} -
                    M: {{ data_get($dataDaftarTxn, 'pemeriksaan.tandaVital.m') ?? '-' }} -
                    GCS: {{ data_get($dataDaftarTxn, 'pemeriksaan.tandaVital.gcs') ?? '-' }}
                </div>
            </div>

            {{-- PEMERIKSAAN FISIK & ANATOMI --}}
            <div class="px-4 mt-2">
                <div
                    class="px-3 py-2 text-sm font-semibold uppercase border-t border-gray-900 rounded-t-md border-x bg-gray-50">
                    pemeriksaan fisik & anatomi
                </div>
                <div class="px-3 py-2 text-sm border-b border-gray-900 rounded-b-md border-x">
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <span class="font-semibold">Fisik :</span><br>
                            {!! nl2br(e(data_get($dataDaftarTxn, 'pemeriksaan.fisik') ?? '-')) !!}
                        </div>
                        <div>
                            <span class="font-semibold">Anatomi :</span><br>
                            @foreach (data_get($dataDaftarTxn, 'pemeriksaan.anatomi', []) as $key => $a)
                                @php $kelainan = data_get($a,'kelainan'); @endphp
                                @if ($kelainan && $kelainan !== 'Tidak Diperiksa')
                                    <span class="font-normal">{{ strtoupper($key) }} : {{ $kelainan }}</span> /
                                    {!! nl2br(e(data_get($a, 'desc', '-'))) !!}<br>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- PENUNJANG --}}
            <div class="px-4 mt-2">
                <div
                    class="px-3 py-2 text-sm font-semibold uppercase border-t border-gray-900 rounded-t-md border-x bg-gray-50">
                    pemeriksaan penunjang</div>
                <div class="px-3 py-2 text-sm border-b border-gray-900 rounded-b-md border-x">
                    <span class="font-semibold">Lab / Foto / EKG / Lain-lain :</span><br>
                    {!! nl2br(e((string) (data_get($dataDaftarTxn, 'pemeriksaan.penunjang') ?? '-'))) !!}
                </div>
            </div>

            {{-- DIAGNOSIS --}}
            <div class="px-4 mt-2">
                <div
                    class="px-3 py-2 text-sm font-semibold uppercase border-t border-gray-900 rounded-t-md border-x bg-gray-50">
                    diagnosis</div>
                <div class="px-3 py-2 border-b border-gray-900 rounded-b-md border-x">
                    <div class="overflow-hidden rounded-md ring-1 ring-gray-300">
                        <table class="w-full table-fixed">
                            <thead class="text-sm bg-gray-50">
                                <tr class="divide-x divide-gray-200">
                                    <th class="px-2 py-1 text-left">Kode (ICD 10)</th>
                                    <th class="px-2 py-1 text-left">Diagnosa</th>
                                    <th class="px-2 py-1 text-left">Kategori</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-gray-200">
                                @foreach (data_get($dataDaftarTxn, 'diagnosis', []) as $diag)
                                    <tr class="divide-x divide-gray-200">
                                        <td class="px-2 py-1">{{ data_get($diag, 'icdX', '-') }}</td>
                                        <td class="px-2 py-1">{{ data_get($diag, 'diagDesc', '-') }}</td>
                                        <td class="px-2 py-1">{{ data_get($diag, 'kategoriDiagnosa', '-') }}</td>
                                    </tr>
                                @endforeach
                                @if (empty(data_get($dataDaftarTxn, 'diagnosis')))
                                    <tr>
                                        <td colspan="3" class="px-2 py-2 text-center">-</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- PROSEDUR --}}
            <div class="px-4 mt-2">
                <div
                    class="px-3 py-2 text-sm font-semibold uppercase border-t border-gray-900 rounded-t-md border-x bg-gray-50">
                    prosedur</div>
                <div class="px-3 py-2 border-b border-gray-900 rounded-b-md border-x">
                    <div class="overflow-hidden rounded-md ring-1 ring-gray-300">
                        <table class="w-full table-fixed">
                            <thead class="text-sm bg-gray-50">
                                <tr class="divide-x divide-gray-200">
                                    <th class="px-2 py-1 text-left">Kode (ICD 9 CM)</th>
                                    <th class="px-2 py-1 text-left">Prosedur</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-gray-200">
                                @foreach (data_get($dataDaftarTxn, 'procedure', []) as $p)
                                    <tr class="divide-x divide-gray-200">
                                        <td class="px-2 py-1">{{ data_get($p, 'procedureId', '-') }}</td>
                                        <td class="px-2 py-1">{{ data_get($p, 'procedureDesc', '-') }}</td>
                                    </tr>
                                @endforeach
                                @if (empty(data_get($dataDaftarTxn, 'procedure')))
                                    <tr>
                                        <td colspan="2" class="px-2 py-2 text-center">-</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- STATUS MEDIK & TINDAK LANJUT & TERAPI & TTD DOKTER --}}
            <div class="px-4 mt-2 mb-16">
                <div
                    class="px-3 py-2 text-sm font-semibold uppercase border-t border-gray-900 rounded-t-md border-x bg-gray-50">
                    status medik & tindak lanjut</div>
                <div class="px-3 py-2 text-sm border-b border-gray-900 rounded-b-md border-x">
                    <div class="mb-3">
                        <span class="font-semibold">Status Medik :</span>
                        {{ data_get($dataDaftarTxn, 'penilaian.statusMedik.statusMedik') ?? '-' }}
                    </div>

                    <div class="mb-3">
                        <span class="font-semibold">Tindak Lanjut :</span>
                        {{ data_get($dataDaftarTxn, 'perencanaan.tindakLanjut.tindakLanjut') ?? '-' }} /
                        {{ data_get($dataDaftarTxn, 'perencanaan.tindakLanjut.keteranganTindakLanjut') ?? '-' }}
                    </div>

                    <div class="grid gap-4 md:grid-cols-4">
                        <div class="md:col-span-3">
                            <div class="font-semibold">Terapi (Obat)</div>
                            <div
                                class="p-2 break-words whitespace-pre-line border border-gray-200 rounded-md bg-gray-50">
                                {!! nl2br(e(data_get($dataDaftarTxn, 'perencanaan.terapi.terapi') ?? '-')) !!}
                            </div>
                        </div>

                        <div class="flex flex-col justify-end text-center md:col-span-1">
                            @inject('carbon', 'Carbon\Carbon')
                            <div>
                                Tulungagung,<br>
                                {{ data_get($dataDaftarTxn, 'perencanaan.pengkajianMedis.selesaiPemeriksaan') ?? 'Tanggal' }}
                            </div>

                            @php
                                $drImg =
                                    optional(
                                        App\Models\User::where(
                                            'myuser_code',
                                            data_get($dataDaftarTxn, 'drId'),
                                        )->first(),
                                    )->myuser_ttd_image ?? null;
                            @endphp

                            @if ($drImg)
                                <img class="h-24 mx-auto my-1" src="{{ asset('storage/' . $drImg) }}"
                                    alt="TTD Dokter" />
                            @endif

                            <div class="mt-1">
                                <span class="text-xs italic text-gray-500">ttd</span><br>
                                <span class="text-xs font-semibold">
                                    {{ data_get($dataDaftarTxn, 'perencanaan.pengkajianMedis.drPemeriksa') ?? 'Dokter Pemeriksa' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Footer --}}
        <div class="sticky bottom-0 flex justify-end px-4 py-3 border-t bg-gray-50">
            <div wire:loading wire:target="cetakRekamMedisUGD">
                <x-loading />
            </div>
            <x-green-button wire:click.prevent="cetakRekamMedisUGD()" wire:loading.remove>
                Cetak RM UGD
            </x-green-button>
        </div>
    </div>
</div>
