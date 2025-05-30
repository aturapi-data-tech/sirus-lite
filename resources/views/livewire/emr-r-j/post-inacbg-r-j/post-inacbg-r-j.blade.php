<div class="grid grid-cols-4 gap-1">



    <x-light-button wire:click.prevent="groupingAllToInaCbg()" type="button" wire:loading.remove>
        Grouping All INA-CBG
    </x-light-button>

    <div wire:loading wire:target="groupingAllToInaCbg">
        <x-loading />
    </div>



    <x-light-button wire:click.prevent="sendNewClaimToInaCbg()" type="button" wire:loading.remove>
        1.Kirim Klaim INA-CBG
    </x-light-button>

    <div wire:loading wire:target="sendNewClaimToInaCbg">
        <x-loading />
    </div>

    <x-light-button wire:click.prevent="setClaimDataToInaCbg()" type="button" wire:loading.remove>
        2.Kirim Detail Klaim
    </x-light-button>

    <div wire:loading wire:target="setClaimDataToInaCbg">
        <x-loading />
    </div>

    {{-- Grouping Stage 1 --}}
    <x-light-button wire:click.prevent="groupingStage1ToInaCbg" type="button" wire:loading.remove>
        3.Grouping Stage 1
    </x-light-button>
    <div wire:loading wire:target="groupingStage1ToInaCbg">
        <x-loading />
    </div>

    {{-- Grouping Stage 2 --}}
    <x-light-button wire:click.prevent="groupingStage2ToInaCbg" type="button" wire:loading.remove>
        4.Grouping Stage 2
    </x-light-button>
    <div wire:loading wire:target="groupingStage2ToInaCbg">
        <x-loading />
    </div>

    {{-- Finalisasi Claim --}}
    <x-light-button wire:click.prevent="finalizeClaimToInaCbg" type="button" wire:loading.remove>
        5.Finalisasi Klaim
    </x-light-button>
    <div wire:loading wire:target="finalizeClaimToInaCbg">
        <x-loading />
    </div>


    <x-light-button wire:click.prevent="getClaimDataToInaCbg()" type="button" wire:loading.remove>
        Get Detail Klaim
    </x-light-button>

    <div wire:loading wire:target="getClaimDataToInaCbg">
        <x-loading />
    </div>



</div>
