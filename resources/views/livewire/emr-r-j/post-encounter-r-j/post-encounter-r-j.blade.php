<div class="grid grid-cols-4 gap-1">
    <div class="text-xs text-gray-700">
        {{ $EncounterID }}
    </div>


    <x-light-button wire:click.prevent="postEncounterRJ()" type="button" wire:loading.remove>
        Kirim Encounter
    </x-light-button>
    <div wire:loading wire:target="postEncounterRJ">
        <x-loading />
    </div>

    <x-light-button wire:click.prevent="getEncounterRJ('{{ $EncounterID }}')" type="button" wire:loading.remove>
        Get Encounter
    </x-light-button>
    <div wire:loading wire:target="getEncounterRJ">
        <x-loading />
    </div>


    <x-light-button wire:click.prevent="postKeluhanUtamaRJ()" type="button" wire:loading.remove>
        Kirim Keluhan Utama
    </x-light-button>
    <div wire:loading wire:target="postKeluhanUtamaRJ">
        <x-loading />
    </div>

    <x-light-button wire:click.prevent="postRiwayatPenyakitSekarangRJ()" type="button" wire:loading.remove>
        Kirim Riwayat Penyakit Sekarang
    </x-light-button>
    <div wire:loading wire:target="postRiwayatPenyakitSekarangRJ">
        <x-loading />
    </div>

    <x-light-button wire:click.prevent="postRiwayatPenyakitDahuluRJ()" type="button" wire:loading.remove>
        Kirim Riwayat Penyakit Dahulu
    </x-light-button>
    <div wire:loading wire:target="postRiwayatPenyakitDahuluRJ">
        <x-loading />
    </div>

    <x-light-button wire:click.prevent="postAlergiRJ()" type="button" wire:loading.remove>
        Kirim Alergi
    </x-light-button>
    <div wire:loading wire:target="postAlergiRJ">
        <x-loading />
    </div>

    <x-light-button wire:click.prevent="postTtvRJ()" type="button" wire:loading.remove>
        Kirim Ttv
    </x-light-button>
    <div wire:loading wire:target="postTtvRJ">
        <x-loading />
    </div>

    <x-light-button wire:click.prevent="postAntropometriRJ()" type="button" wire:loading.remove>
        Kirim Nutrisi
    </x-light-button>
    <div wire:loading wire:target="postAntropometriRJ">
        <x-loading />
    </div>






















    <x-light-button wire:click.prevent="getAlergiRJ()" type="button" wire:loading.remove>
        Get Alergi
    </x-light-button>
    <div wire:loading wire:target="getAlergiRJ">
        <x-loading />
    </div>


    <x-light-button wire:click.prevent="getConditionRJ('{{ $EncounterID }}')" type="button" wire:loading.remove>
        Get Condition
    </x-light-button>
    <div wire:loading wire:target="getConditionRJ">
        <x-loading />
    </div>

    <x-light-button wire:click.prevent="getObservationRJ('{{ $EncounterID }}')" type="button" wire:loading.remove>
        Get Observation
    </x-light-button>
    <div wire:loading wire:target="getObservationRJ">
        <x-loading />
    </div>


    <!-- Tombol Kirim Diagnosa -->
    <x-light-button wire:click.prevent="postDiagnosaRJ()" type="button" wire:loading.remove
        wire:target="postDiagnosaRJ">
        Kirim Diagnosa
    </x-light-button>
    <div wire:loading wire:target="postDiagnosaRJ">
        <x-loading />
    </div>

    <!-- Tombol Kirim Tindakan -->
    <x-light-button wire:click.prevent="postTindakanRJ()" type="button" wire:loading.remove
        wire:target="postTindakanRJ">
        Kirim Tindakan
    </x-light-button>
    <div wire:loading wire:target="postTindakanRJ">
        <x-loading />
    </div>

    {{--

    <x-light-button wire:click.prevent="postDiagnosis()" type="button" wire:loading.remove>
        Kirim Diagnosis
    </x-light-button>
    <div wire:loading wire:target="postDiagnosis">
        <x-loading />
    </div>

     <x-light-button wire:click.prevent="postPenunjang()" type="button" wire:loading.remove>
        Kirim Penunjang
    </x-light-button>
    <div wire:loading wire:target="postPenunjang">
        <x-loading />
    </div>


    <x-light-button wire:click.prevent="postObat()" type="button" wire:loading.remove>
        Kirim Obat
    </x-light-button>
    <div wire:loading wire:target="postObat">
        <x-loading />
    </div>


    <x-light-button wire:click.prevent="postStatusPulang()" type="button" wire:loading.remove>
        Kirim Status Pulang
    </x-light-button>
    <div wire:loading wire:target="postStatusPulang">
        <x-loading />
    </div> --}}

</div>
