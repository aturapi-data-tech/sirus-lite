@php
    use Carbon\Carbon;

    // =============================
    //  LOGIC SECTION (di atas)
    // =============================
    $fmt = 'd/m/Y H:i:s';
    $regNo = $dataDaftarUgd['regNo'] ?? '-';

    // Ambil semua data waktu
    $jamDatang = data_get($dataDaftarUgd, 'anamnesa.pengkajianPerawatan.jamDatang');
    $waktuPemeriksaan = data_get($dataDaftarUgd, 'perencanaan.pengkajianMedis.waktuPemeriksaan');
    $selesaiPemeriksaan = data_get($dataDaftarUgd, 'perencanaan.pengkajianMedis.selesaiPemeriksaan');

    // Default tampilan
    $waktuResponse = '-';
    $durasiTotal = '-';

    // Hitung waktu response (datang → periksa)
    if (Carbon::hasFormat($jamDatang, $fmt) && Carbon::hasFormat($waktuPemeriksaan, $fmt)) {
        $datang = Carbon::createFromFormat($fmt, $jamDatang);
        $periksa = Carbon::createFromFormat($fmt, $waktuPemeriksaan);
        $waktuResponse = $datang->diff($periksa)->format('%H:%I:%S');
    }

    // Hitung durasi total (periksa → selesai)
    if (Carbon::hasFormat($waktuPemeriksaan, $fmt) && Carbon::hasFormat($selesaiPemeriksaan, $fmt)) {
        $periksa = Carbon::createFromFormat($fmt, $waktuPemeriksaan);
        $selesai = Carbon::createFromFormat($fmt, $selesaiPemeriksaan);
        $durasiTotal = $periksa->diff($selesai)->format('%H:%I:%S');
    }
@endphp

<div>
    <div class="w-full mb-1">

        {{-- ===========================
            WAKTU DATANG
        ============================ --}}
        <div class="mb-2">
            <p class="text-sm font-medium text-gray-900">
                Waktu Datang :
                {{ $jamDatang ?? '-' }}
            </p>
        </div>

        {{-- ===========================
            WAKTU PEMERIKSAAN
        ============================ --}}
        <div class="mb-2">
            <x-input-label for="dataDaftarUgd.perencanaan.pengkajianMedis.waktuPemeriksaan" :value="__('Waktu Pemeriksaan')"
                :required="__(true)" />

            <div class="flex items-center mb-2">
                <x-text-input id="dataDaftarUgd.perencanaan.pengkajianMedis.waktuPemeriksaan"
                    placeholder="Waktu Pemeriksaan [dd/mm/yyyy hh24:mi:ss]" class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarUgd.perencanaan.pengkajianMedis.waktuPemeriksaan'))"
                    :disabled="$disabledPropertyRjStatus" wire:model="dataDaftarUgd.perencanaan.pengkajianMedis.waktuPemeriksaan" />

                @empty($waktuPemeriksaan)
                    <div class="w-1/2 ml-2">
                        <div wire:loading wire:target="setWaktuPemeriksaan">
                            <x-loading />
                        </div>

                        <x-green-button :disabled="false"
                            wire:click.prevent="setWaktuPemeriksaan('{{ now()->format('d/m/Y H:i:s') }}')"
                            wire:loading.remove wire:target="setWaktuPemeriksaan">
                            Set Waktu Pemeriksaan ({{ now()->format('d/m/Y H:i:s') }})
                        </x-green-button>
                    </div>
                @endempty
            </div>
            @error('dataDaftarUgd.perencanaan.pengkajianMedis.waktuPemeriksaan')
                <x-input-error :messages="$message" />
            @enderror
        </div>

        {{-- ===========================
            SELESAI PEMERIKSAAN
        ============================ --}}
        <div class="mb-2">
            <x-input-label for="dataDaftarUgd.perencanaan.pengkajianMedis.selesaiPemeriksaan" :value="__('Selesai Pemeriksaan')"
                :required="__(true)" />

            <div class="flex items-center mb-2">
                <x-text-input id="dataDaftarUgd.perencanaan.pengkajianMedis.selesaiPemeriksaan"
                    placeholder="Selesai Pemeriksaan [dd/mm/yyyy hh24:mi:ss]" class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarUgd.perencanaan.pengkajianMedis.selesaiPemeriksaan'))"
                    :disabled="$disabledPropertyRjStatus" wire:model="dataDaftarUgd.perencanaan.pengkajianMedis.selesaiPemeriksaan" />

                @empty($selesaiPemeriksaan)
                    <div class="w-1/2 ml-2">
                        <div wire:loading wire:target="setSelesaiPemeriksaan">
                            <x-loading />
                        </div>

                        <x-green-button :disabled="false"
                            wire:click.prevent="setSelesaiPemeriksaan('{{ now()->format('d/m/Y H:i:s') }}')"
                            wire:loading.remove wire:target="setSelesaiPemeriksaan">
                            Set Selesai Pemeriksaan ({{ now()->format('d/m/Y H:i:s') }})
                        </x-green-button>
                    </div>
                @endempty
            </div>
            @error('dataDaftarUgd.perencanaan.pengkajianMedis.selesaiPemeriksaan')
                <x-input-error :messages="$message" />
            @enderror
        </div>

        {{-- ===========================
            DOKTER PEMERIKSA
        ============================ --}}
        <div class="mb-2">
            <x-input-label for="dataDaftarUgd.perencanaan.pengkajianMedis.drPemeriksa" :value="__('Dokter Pemeriksa')"
                :required="__(true)" />
            <div class="grid grid-cols-1 gap-2">
                <x-text-input id="dataDaftarUgd.perencanaan.pengkajianMedis.drPemeriksa" placeholder="Dokter Pemeriksa"
                    class="mt-1 ml-2" :errorshas="__($errors->has('dataDaftarUgd.perencanaan.pengkajianMedis.drPemeriksa'))" :disabled="true"
                    wire:model="dataDaftarUgd.perencanaan.pengkajianMedis.drPemeriksa" />

                <x-yellow-button :disabled="false" wire:click.prevent="setDrPemeriksa()" type="button"
                    wire:loading.remove>
                    TTD Dokter
                </x-yellow-button>

                <livewire:cetak.cetak-eresep-u-g-d :rjNoRef="$rjNoRef"
                    wire:key="cetak.cetak-eresep-u-g-d-{{ $rjNoRef }}">
            </div>
            @error('dataDaftarUgd.perencanaan.pengkajianMedis.drPemeriksa')
                <x-input-error :messages="$message" />
            @enderror
        </div>

        {{-- ===========================
            HASIL WAKTU
        ============================ --}}
        <div class="mb-2">
            <p class="text-sm font-medium text-gray-900">
                🕒 Waktu Response untuk pasien {{ $regNo }} adalah:
                <span class="font-semibold">{{ $waktuResponse }}</span>
            </p>
            <p class="text-sm font-medium text-gray-900">
                ⏱️ Durasi Pemeriksaan hingga selesai:
                <span class="font-semibold">{{ $durasiTotal }}</span>
            </p>
        </div>
    </div>
</div>
