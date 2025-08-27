<div>
    <div class="p-3 text-sm">

        {{-- ======================= HALAMAN 1: RIWAYAT PENGOBATAN ======================= --}}
        <table class="w-full border-collapse">
            <tr>
                <td class="w-16 align-top border-0">
                    {{-- <img src="{{ asset('logo.png') }}" class="h-12" /> --}}
                </td>
                <td class="border-0">
                    <div class="text-lg font-bold tracking-wide text-center">RIWAYAT PENGOBATAN</div>
                </td>
                <td class="w-64 align-top border-0">
                    <table class="w-full border border-collapse border-black table-auto">
                        <tr>
                            <th class="px-2 py-1 text-left border border-black">No. Rekam Medis</th>
                            <td class="px-2 py-1 border border-black">
                                {{-- 3 kotak kecil sesuai template --}}
                                <div class="flex gap-1">
                                    <div class="w-8 h-6 text-center border border-black">
                                        {{ $identitas['rm_box_1'] ?? '' }}</div>
                                    <div class="w-8 h-6 text-center border border-black">
                                        {{ $identitas['rm_box_2'] ?? '' }}</div>
                                    <div class="w-8 h-6 text-center border border-black">
                                        {{ $identitas['rm_box_3'] ?? '' }}</div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table class="w-full mt-2 border border-collapse border-black table-auto">
            <tr>
                <th class="w-40 px-2 py-1 text-left border border-black">Nama pasien</th>
                <td class="px-2 py-1 border border-black">{{ $identitas['nama'] ?? '' }}</td>
                <th class="w-40 px-2 py-1 text-left border border-black">Ruang Perawatan</th>
                <td class="px-2 py-1 border border-black">{{ $identitas['ruang'] ?? '' }}</td>
                <th class="w-24 px-2 py-1 text-left border border-black">Kamar</th>
                <td class="px-2 py-1 border border-black">{{ $identitas['kamar'] ?? '' }}</td>
            </tr>
            <tr>
                <th class="px-2 py-1 text-left border border-black">Tanggal lahir</th>
                <td class="px-2 py-1 border border-black">{{ $identitas['tglLahir'] ?? '' }}</td>
                <th class="px-2 py-1 text-left border border-black">Tanggal keluar/meninggal*</th>
                <td class="px-2 py-1 border border-black" colspan="3">{{ $identitas['tglKeluar'] ?? '' }}</td>
            </tr>
            <tr>
                <th class="px-2 py-1 text-left border border-black">Tanggal masuk RS</th>
                <td class="px-2 py-1 border border-black">{{ $identitas['tglMasuk'] ?? '' }}</td>
                <th class="px-2 py-1 text-left border border-black">DPJP</th>
                <td class="px-2 py-1 border border-black" colspan="3">{{ $identitas['dpjp'] ?? '' }}</td>
            </tr>
            <tr>
                <th class="px-2 py-1 text-left border border-black">Diagnosis masuk</th>
                <td class="px-2 py-1 border border-black" colspan="5">{{ $ringkasan['diagnosisMasuk'] ?? '' }}</td>
            </tr>
            <tr>
                <th class="px-2 py-1 text-left border border-black">Indikasi Rawat Inap</th>
                <td class="px-2 py-1 border border-black" colspan="5">{{ $ringkasan['indikasiRawatInap'] ?? '' }}
                </td>
            </tr>
        </table>

        {{-- ANAMNESIS --}}
        <table class="w-full mt-2 border border-collapse border-black table-auto">
            <tr class="font-semibold bg-gray-100">
                <th colspan="6" class="px-2 py-1 text-left">ANAMNESIS</th>
            </tr>
            <tr>
                <th class="w-48 px-2 py-1 text-left border border-black">Keluhan utama</th>
                <td class="px-2 py-1 border border-black" colspan="5">{{ $ringkasan['keluhanUtama'] ?? '' }}</td>
            </tr>
            <tr>
                <th class="px-2 py-1 text-left border border-black">Riwayat penyakit</th>
                <td class="px-2 py-1 border border-black" colspan="5">{{ $ringkasan['riwayatPenyakit'] ?? '' }}</td>
            </tr>
        </table>

        {{-- PEMERIKSAAN FISIK --}}
        <table class="w-full mt-2 border border-collapse border-black table-auto">
            <tr class="font-semibold bg-gray-100">
                <th colspan="6" class="px-2 py-1 text-left">PEMERIKSAAN FISIK</th>
            </tr>
            <tr>
                <th class="w-48 px-2 py-1 text-left border border-black">Keadaan umum</th>
                <td class="px-2 py-1 border border-black" colspan="5">{{ $ringkasan['keadaanUmum'] ?? '' }}</td>
            </tr>
            <tr>
                <th class="px-2 py-1 text-left border border-black">Tanda vital</th>
                <td class="px-2 py-1 border border-black" colspan="5">
                    Tekanan darah : {{ $vitals['td'] ?? '' }} &nbsp;&nbsp;
                    Suhu : {{ $vitals['suhu'] ?? '' }} &nbsp;&nbsp;
                    Nadi : {{ $vitals['nadi'] ?? '' }} &nbsp;&nbsp;
                    Frekuensi napas : {{ $vitals['rr'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th class="px-2 py-1 text-left border border-black">Pemeriksaan Fisik</th>
                <td class="px-2 py-1 border border-black" colspan="5">{{ $ringkasan['pemeriksaanFisik'] ?? '' }}
                </td>
            </tr>
        </table>

        {{-- PEMERIKSAAN PENUNJANG --}}
        <table class="w-full mt-2 border border-collapse border-black table-auto">
            <tr class="font-semibold bg-gray-100">
                <th colspan="6" class="px-2 py-1 text-left">PEMERIKSAAN PENUNJANG</th>
            </tr>
            <tr>
                <th class="w-48 px-2 py-1 text-left border border-black">1. LABORATORIUM</th>
                <td class="px-2 py-1 border border-black" colspan="5">{{ $penunjang['lab'] ?? '' }}</td>
            </tr>
            <tr>
                <th class="px-2 py-1 text-left border border-black">2. RADIOLOGI</th>
                <td class="px-2 py-1 border border-black" colspan="5">{{ $penunjang['rad'] ?? '' }}</td>
            </tr>
            <tr>
                <th class="px-2 py-1 text-left border border-black">3. LAIN-LAIN</th>
                <td class="px-2 py-1 border border-black" colspan="5">{{ $penunjang['lain'] ?? '' }}</td>
            </tr>
        </table>

        {{-- TERAPI/TINDAKAN DI RS --}}
        <table class="w-full mt-2 border border-collapse border-black table-auto">
            <tr class="font-semibold bg-gray-100">
                <th colspan="6" class="px-2 py-1 text-left">TERAPI/TINDAKAN MEDIS SELAMA DI RUMAH SAKIT</th>
            </tr>
            <tr>
                <td class="px-2 py-8 border border-black" colspan="6">{{ $ringkasan['terapiRS'] ?? '' }}</td>
            </tr>
        </table>

        {{-- DIAGNOSIS & TINDAKAN + ICD --}}
        <table class="w-full mt-2 border border-collapse border-black table-auto">
            <tr>
                <th class="w-48 px-2 py-1 text-left border border-black">DIAGNOSIS UTAMA</th>
                <td class="px-2 py-1 border border-black">{{ $ringkasan['dxUtama'] ?? '' }}</td>
                <th class="w-24 px-2 py-1 text-left border border-black">ICD_10.</th>
                <td class="w-32 px-2 py-1 border border-black">{{ $ringkasan['dxUtamaICD'] ?? '' }}</td>
            </tr>
            <tr>
                <th class="px-2 py-1 text-left align-top border border-black">DIAGNOSIS SEKUNDER :</th>
                <td class="px-2 py-1 align-top border border-black">
                    <ol class="pl-6 leading-6 list-decimal">
                        <li>{{ $ringkasan['dxSek1'] ?? '' }}</li>
                        <li>{{ $ringkasan['dxSek2'] ?? '' }}</li>
                        <li>{{ $ringkasan['dxSek3'] ?? '' }}</li>
                        <li>{{ $ringkasan['dxSek4'] ?? '' }}</li>
                        <li>{{ $ringkasan['dxSek5'] ?? '' }}</li>
                    </ol>
                </td>
                <th class="px-2 py-1 text-left align-top border border-black">ICD_10.</th>
                <td class="px-2 py-1 align-top border border-black">
                    <ol class="pl-6 leading-6 list-decimal">
                        <li>{{ $ringkasan['dxSek1ICD'] ?? '' }}</li>
                        <li>{{ $ringkasan['dxSek2ICD'] ?? '' }}</li>
                        <li>{{ $ringkasan['dxSek3ICD'] ?? '' }}</li>
                        <li>{{ $ringkasan['dxSek4ICD'] ?? '' }}</li>
                        <li>{{ $ringkasan['dxSek5ICD'] ?? '' }}</li>
                    </ol>
                </td>
            </tr>
            <tr>
                <th class="px-2 py-1 text-left align-top border border-black">TINDAKAN/PROSEDUR :</th>
                <td class="px-2 py-1 align-top border border-black">
                    <ol class="pl-6 leading-6 list-decimal">
                        <li>{{ $ringkasan['tind1'] ?? '' }}</li>
                        <li>{{ $ringkasan['tind2'] ?? '' }}</li>
                        <li>{{ $ringkasan['tind3'] ?? '' }}</li>
                        <li>{{ $ringkasan['tind4'] ?? '' }}</li>
                        <li>{{ $ringkasan['tind5'] ?? '' }}</li>
                    </ol>
                </td>
                <th class="px-2 py-1 text-left align-top border border-black">ICD_10.</th>
                <td class="px-2 py-1 align-top border border-black">
                    <ol class="pl-6 leading-6 list-decimal">
                        <li>{{ $ringkasan['tind1ICD'] ?? '' }}</li>
                        <li>{{ $ringkasan['tind2ICD'] ?? '' }}</li>
                        <li>{{ $ringkasan['tind3ICD'] ?? '' }}</li>
                    </ol>
                </td>
            </tr>
            <tr>
                <th class="px-2 py-1 text-left border border-black">DIET</th>
                <td class="px-2 py-1 border border-black" colspan="3">{{ $edukasi['diet'] ?? '' }}</td>
            </tr>
            <tr>
                <th class="px-2 py-1 text-left border border-black">INSTRUKSI DAN EDUKASI (TINDAK LANJUT)</th>
                <td class="px-2 py-6 border border-black" colspan="3">{{ $edukasi['instruksi'] ?? '' }}</td>
            </tr>
        </table>

        <div class="mt-1 text-xs italic text-right">Bersambung ke hal 2</div>

        <div class="page-break"></div>


        {{-- ======================= HALAMAN 2: SAMBUNGAN RINGKASAN PULANG ======================= --}}
        <div class="font-semibold">Sambungan <span class="uppercase">RINGKASAN PULANG</span></div>

        <table class="w-full mt-1 border border-collapse border-black table-auto">
            <tr>
                <th class="w-40 px-2 py-1 text-left border border-black">Nama pasien :</th>
                <td class="px-2 py-1 border border-black">{{ $identitas['nama'] ?? '' }}</td>
                <th class="w-40 px-2 py-1 text-left border border-black">No. Rekam Medis :</th>
                <td class="px-2 py-1 border border-black">
                    <div class="flex gap-1">
                        <div class="w-8 h-6 text-center border border-black">{{ $identitas['rm_box_1'] ?? '' }}</div>
                        <div class="w-8 h-6 text-center border border-black">{{ $identitas['rm_box_2'] ?? '' }}</div>
                        <div class="w-8 h-6 text-center border border-black">{{ $identitas['rm_box_3'] ?? '' }}</div>
                    </div>
                </td>
            </tr>
        </table>

        {{-- KONDISI SAAT PULANG --}}
        <table class="w-full mt-2 border border-collapse border-black table-auto">
            <tr class="font-semibold bg-gray-100">
                <th colspan="4" class="px-2 py-1 text-left">KONDISI SAAT PULANG</th>
            </tr>
            <tr>
                <th class="w-48 px-2 py-1 text-left align-top border border-black">Keadaan umum</th>
                <td class="px-2 py-1 border border-black">{{ $ringkasan['keadaanUmumPulang'] ?? '' }}</td>
                <th class="w-24 px-2 py-1 text-left align-top border border-black">GCS</th>
                <td class="px-2 py-1 border border-black">{{ $ringkasan['gcs'] ?? '' }}</td>
            </tr>
            <tr>
                <th class="px-2 py-1 text-left border border-black">Tanda vital</th>
                <td class="px-2 py-1 border border-black" colspan="3">
                    Tekanan darah : {{ $vitals['td'] ?? '' }} &nbsp;&nbsp;
                    Suhu : {{ $vitals['suhu'] ?? '' }} &nbsp;&nbsp;
                    Nadi : {{ $vitals['nadi'] ?? '' }} &nbsp;&nbsp;
                    Frekuensi napas : {{ $vitals['rr'] ?? '' }}
                </td>
            </tr>
            <tr>
                <th class="px-2 py-1 text-left align-top border border-black">Catatan penting (kondisi saat ini)</th>
                <td class="px-2 py-6 border border-black" colspan="3">{{ $ringkasan['catatanPenting'] ?? '' }}
                </td>
            </tr>
        </table>

        {{-- CARA KELUAR RS --}}
        <table class="w-full mt-2 border border-collapse border-black table-auto">
            <tr class="font-semibold bg-gray-100">
                <th colspan="6" class="px-2 py-1 text-left">CARA KELUAR RS</th>
            </tr>
            <tr>
                <td class="px-2 py-1 border border-black">
                    <span class="inline-block w-3 h-3 mr-2 align-middle border border-black">
                        @if (!empty($disposisi['pulangPersetujuan']))
                            ✔
                        @endif
                    </span> Pulang Atas persetujuan
                </td>
                <td class="px-2 py-1 border border-black">
                    <span class="inline-block w-3 h-3 mr-2 align-middle border border-black">
                        @if (!empty($disposisi['pulangAPS']))
                            ✔
                        @endif
                    </span> Pulang Atas Permintaan Sendiri
                </td>
                <td class="px-2 py-1 border border-black">
                    <span class="inline-block w-3 h-3 mr-2 align-middle border border-black">
                        @if (!empty($disposisi['dirujuk']))
                            ✔
                        @endif
                    </span> Dirujuk
                </td>
                <td class="px-2 py-1 border border-black">Kabur</td>
                <td class="px-2 py-1 border border-black">
                    <span class="inline-block w-3 h-3 mr-2 align-middle border border-black">
                        @if (!empty($disposisi['meninggal']))
                            ✔
                        @endif
                    </span> Meninggal
                </td>
                <td class="px-2 py-1 border border-black">&nbsp;</td>
            </tr>
        </table>

        {{-- TINDAK LANJUT --}}
        <table class="w-full mt-2 border border-collapse border-black table-auto">
            <tr class="font-semibold bg-gray-100">
                <th colspan="6" class="px-2 py-1 text-left">TINDAK LANJUT</th>
            </tr>
            <tr>
                <td class="w-1/2 px-2 py-1 border border-black">
                    <span class="inline-block w-3 h-3 mr-2 align-middle border border-black">
                        @if (!empty($disposisi['kontrol']))
                            ✔
                        @endif
                    </span>
                    Kontrol rawat jalan, tanggal
                    <span class="inline-block w-56 align-middle border-b border-black border-dotted">
                        &nbsp;{{ $disposisi['tglKontrol'] ?? '' }}&nbsp;
                    </span>
                </td>
                <td class="w-1/2 px-2 py-1 border border-black">
                    <span class="inline-block w-3 h-3 mr-2 align-middle border border-black">
                        @if (!empty($disposisi['lainnya1']))
                            ✔
                        @endif
                    </span>
                    <span
                        class="inline-block w-56 border-b border-black border-dotted">&nbsp;{{ $disposisi['lainnya1Text'] ?? '' }}&nbsp;</span>
                </td>
            </tr>
            <tr>
                <td class="px-2 py-1 border border-black">
                    <span class="inline-block w-3 h-3 mr-2 align-middle border border-black">
                        @if (!empty($disposisi['rujuk']))
                            ✔
                        @endif
                    </span>
                    Dirujuk ke
                    <span
                        class="inline-block w-64 border-b border-black border-dotted">&nbsp;{{ $disposisi['rujukKe'] ?? '' }}&nbsp;</span>
                </td>
                <td class="px-2 py-1 border border-black">
                    <span class="inline-block w-3 h-3 mr-2 align-middle border border-black">
                        @if (!empty($disposisi['lainnya2']))
                            ✔
                        @endif
                    </span>
                    <span
                        class="inline-block w-56 border-b border-black border-dotted">&nbsp;{{ $disposisi['lainnya2Text'] ?? '' }}&nbsp;</span>
                </td>
            </tr>
        </table>

        {{-- TERAPI PULANG --}}
        <table class="w-full mt-2 border border-collapse border-black table-auto">
            <tr class="font-semibold bg-gray-100">
                <th colspan="4" class="px-2 py-1 text-left">TERAPI PULANG</th>
            </tr>
            <tr class="font-semibold bg-gray-100">
                <th class="px-2 py-1 text-left border border-black">Nama Obat</th>
                <th class="px-2 py-1 text-left border border-black">Jumlah</th>
                <th class="px-2 py-1 text-left border border-black">Dosis</th>
                <th class="px-2 py-1 text-left border border-black">Cara Pemberian</th>
            </tr>
            @forelse(($obatPulang ?? []) as $o)
                <tr>
                    <td class="px-2 py-1 border border-black">{{ $o['nama'] ?? '' }}</td>
                    <td class="px-2 py-1 border border-black">{{ $o['jumlah'] ?? '' }}</td>
                    <td class="px-2 py-1 border border-black">{{ $o['dosis'] ?? '' }}</td>
                    <td class="px-2 py-1 border border-black">{{ $o['cara'] ?? '' }}</td>
                </tr>
            @empty
                @for ($i = 0; $i < 8; $i++)
                    <tr>
                        <td class="px-2 py-3 border border-black">&nbsp;</td>
                        <td class="px-2 py-3 border border-black">&nbsp;</td>
                        <td class="px-2 py-3 border border-black">&nbsp;</td>
                        <td class="px-2 py-3 border border-black">&nbsp;</td>
                    </tr>
                @endfor
            @endforelse
        </table>

        {{-- TTD --}}
        <table class="w-full mt-3 border border-collapse border-black table-auto">
            <tr>
                <td class="px-2 py-10 align-bottom border border-black">
                    Tanda tangan pasien/keluarga,
                    <div class="mt-8">( ................................................ )</div>
                </td>
                <td class="px-2 py-10 align-bottom border border-black">
                    Jakarta,
                    <span
                        class="inline-block align-bottom border-b border-black border-dotted w-72">&nbsp;{{ $identitas['kotaTanggalTTD'] ?? '' }}&nbsp;</span>
                    <div class="mt-8 text-center">( ................................................ )<br />Tanda
                        tangan dan nama dokter</div>
                </td>
            </tr>
        </table>

        {{-- FOOTER --}}
        <div class="text-center text-[10px] mt-2 font-semibold">
            MOHON UNTUK TIDAK MENGGUNAKAN SINGKATAN DALAM PENULISAN DIAGNOSIS DAN TINDAKAN<br />
            SERTA DITULIS DENGAN RAPI
        </div>
        <div class="text-center text-[10px]">
            Jl. Danau Sunter Utara, Sunter Paradise I, Jakarta 14350 Telepon : (021) 6400261, 6459877 (Hunting) Fax :
            (021) 6400778
            &nbsp; E-Mail : info@royalprogress.com &nbsp; www.royalprogress.com
        </div>
        <div class="text-right text-[10px]">2/2</div>
    </div>
</div>
