<div class="grid grid-cols-2 gap-1">


    <x-light-button class="col-span-2" wire:click.prevent="groupingAllToInaCbg()" type="button" wire:loading.remove>
        Grouping All INA-CBG
    </x-light-button>

    <div wire:loading wire:target="groupingAllToInaCbg">
        <x-loading />
    </div>


    {{-- <x-light-button wire:click.prevent="sendNewClaimToInaCbg()" type="button" wire:loading.remove>
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

    <x-light-button wire:click.prevent="groupingStage1ToInaCbg" type="button" wire:loading.remove>
        3.Grouping Stage 1
    </x-light-button>
    <div wire:loading wire:target="groupingStage1ToInaCbg">
        <x-loading />
    </div>

    <x-light-button wire:click.prevent="groupingStage2ToInaCbg" type="button" wire:loading.remove>
        4.Grouping Stage 2
    </x-light-button>
    <div wire:loading wire:target="groupingStage2ToInaCbg">
        <x-loading />
    </div>

    <x-light-button wire:click.prevent="finalizeClaimToInaCbg" type="button" wire:loading.remove>
        5.Finalisasi Klaim
    </x-light-button>
    <div wire:loading wire:target="finalizeClaimToInaCbg">
        <x-loading />
    </div> --}}
    @if (!$groupingCount)
        <x-primary-button class="col-span-2" wire:click.prevent="printClaimToInaCbg()" type="button" wire:loading.remove>
            Upload PDF Klaim
        </x-primary-button>
    @else
        <x-light-button class="col-span-2" wire:click.prevent="printClaimToInaCbg()" type="button" wire:loading.remove>
            Upload PDF Klaim
        </x-light-button>
    @endif

    <div wire:loading wire:target="printClaimToInaCbg">
        <x-loading />
    </div>

    <x-primary-button wire:click.prevent="editClaimToInaCbg" type="button" wire:loading.remove>
        6.Edit Ulang Klaim
    </x-primary-button>
    <div wire:loading wire:target="editClaimToInaCbg">
        <x-loading />
    </div>


    <x-yellow-button wire:click.prevent="deleteDiagnosisAndProcedureDataToInaCbg" type="button" wire:loading.remove>
        7.Hapus Diagnosa Dan Prosedur Klaim
    </x-yellow-button>
    <div wire:loading wire:target="deleteDiagnosisAndProcedureDataToInaCbg">
        <x-loading />
    </div>


    <x-red-button wire:click.prevent="deleteClaimToInaCbg" type="button" wire:loading.remove>
        8.Hapus Klaim
    </x-red-button>
    <div wire:loading wire:target="deleteClaimToInaCbg">
        <x-loading />
    </div>


    {{-- <x-light-button wire:click.prevent="getClaimDataToInaCbg()" type="button" wire:loading.remove>
        Get Detail Klaim
    </x-light-button>

    <div wire:loading wire:target="getClaimDataToInaCbg">
        <x-loading />
    </div> --}}



</div>
