<div class="grid grid-cols-4 gap-1">



    <x-light-button wire:click.prevent="sendNewClaimToInaCbg()" type="button" wire:loading.remove>
        Kirim Klaim INA-CBG
    </x-light-button>

    <div wire:loading wire:target="sendNewClaimToInaCbg">
        <x-loading />
    </div>




    <x-light-button wire:click.prevent="setClaimDataToInaCbg()" type="button" wire:loading.remove>
        Kirim Detail Klaim
    </x-light-button>

    <div wire:loading wire:target="setClaimDataToInaCbg">
        <x-loading />
    </div>



</div>
