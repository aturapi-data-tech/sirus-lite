<div>



    {{-- Start Coding  --}}

    {{-- Canvas
    Main BgColor /
    Size H/W --}}
    <div class="w-full h-[calc(100vh-68px)] bg-white border border-gray-200 px-4 pt-6">

        {{-- Title  --}}
        <div class="mb-2">
            <h3 class="text-3xl font-bold text-gray-900 ">{{ $myTitle }}</h3>
            {{-- <span class="text-base font-normal text-gray-700">{{ $mySnipt }}</span> --}}

            {{-- Tampilkan Bulan dan Tahun --}}
            <div class="mt-1 text-sm text-gray-600">
                Periode: {{ \Carbon\Carbon::createFromFormat('m', $myMonth)->translatedFormat('F') }}
                {{ $myYear }}
            </div>
        </div>
        {{-- Top Bar --}}
        <div class="grid grid-cols-5 gap-2">
            <div class="flex items-center justify-center gap-2">
                <x-input-label for="forxxx" :value="__('Bulan')" />
                {{-- Input Bulan --}}
                <x-text-input placeholder="Bulan Lahir [mm]" class="sm:w-14" wire:model.debounce="myMonth" />

                {{-- Input Tahun --}}
                <x-text-input placeholder="Tahun Lahir [yyyy]" class="sm:w-20" wire:model.debounce="myYear" />
            </div>
        </div>
        {{-- Top Bar --}}






        <div class="h-[calc(100vh-250px)] mt-2 overflow-auto">

            <!-- Show Data -->

            {{-- <div>
                <livewire:laporan.laporan-r-j.laporan-kunjungan-r-j-bulanan
                    :wire:key="'content-laporan.laporan-r-j.laporan-kunjungan-r-j-bulanan'.$myMonth.$myYear"
                    :myMonth="$myMonth" :myYear="$myYear" :>
            </div> --}}
        </div>









    </div>
    {{-- Canvas
    Main BgColor /
    Size H/W --}}

    {{-- End Coding --}}

</div>
