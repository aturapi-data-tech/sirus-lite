{{-- @php
    $EncounterID =
        collect($dataDaftarPoliRJ['satuSehatUuidRJ'])
            ->filter(function ($item) {
                return $item['response']['resourceType'] === 'Encounter';
            })
            ->first()['response']['resourceID'] ?? '';
@endphp --}}
<div>
    <x-light-button wire:click.prevent="PostEncounterSatuSehat()" type="button" wire:loading.remove>
        Kirim Satu Sehat
    </x-light-button>
    <div wire:loading wire:target="PostEncounterSatuSehat">
        <x-loading />
    </div>
    <div class="text-xs text-gray-700">
        {{ $EncounterID }}
    </div>
</div>
