<div>

    <div class="w-full max-w-md p-2 mx-auto space-y-4 bg-white rounded shadow-sm">
        <h2 class="text-xl font-semibold">Kontrol CIFS Share</h2>

        {{-- Status apakah sudah ter-mount atau belum --}}
        <div class="flex items-center space-x-2">
            @if ($isMounted)
                <span class="inline-block w-3 h-3 bg-green-500 rounded-full"></span>
                <span class="font-medium text-green-700">Status: Ter-mount</span>
            @else
                <span class="inline-block w-3 h-3 bg-red-500 rounded-full"></span>
                <span class="font-medium text-red-700">Status: Belum ter-mount</span>
            @endif
        </div>

        {{-- Tampilkan pesan hasil aksi (error / sukses) --}}
        @if ($statusMessage)
            <div class="px-1 py-2 bg-gray-100 border border-gray-200 rounded">
                {{ $statusMessage }}
            </div>
        @endif

        {{-- Grid dua kolom untuk tombol Mount dan Unmount --}}
        <div class="grid grid-cols-3 gap-2">
            {{-- Kolom 1: Tombol Mount --}}
            <div class="col-span-1">
                <x-light-button class="w-full" wire:click.prevent="mountShare" type="button" wire:loading.remove>
                    Mount Share
                </x-light-button>

                {{-- Loading indicator untuk aksi mount --}}
                <div wire:loading wire:target="mountShare" class="flex justify-center mt-2">
                    <x-loading />
                </div>
            </div>

            {{-- Kolom 2: Tombol Unmount --}}
            <div class="col-span-1">
                <x-light-button class="w-full" wire:click.prevent="unmountShare" type="button" wire:loading.remove>
                    Unmount Share
                </x-light-button>

                {{-- Loading indicator untuk aksi unmount --}}
                <div wire:loading wire:target="unmountShare" class="flex justify-center mt-2">
                    <x-loading />
                </div>
            </div>

            <div class="col-span-1">
                <x-light-button class="w-full" wire:click.prevent="checkMounted" type="button" wire:loading.remove>
                    Cek Mount Share
                </x-light-button>

                {{-- Loading indicator untuk aksi mount --}}
                <div wire:loading wire:target="checkMounted" class="flex justify-center mt-2">
                    <x-loading />
                </div>
            </div>

        </div>
    </div>

</div>
