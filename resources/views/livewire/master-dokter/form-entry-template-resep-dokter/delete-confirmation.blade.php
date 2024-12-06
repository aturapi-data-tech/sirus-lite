    <div x-data="{ deleteConfirmation{{ addslashes($myQData->tempr_id) . addslashes($myQData->dr_id) }}: false }" class =''>

        <div x-show="deleteConfirmation{{ addslashes($myQData->tempr_id) . addslashes($myQData->dr_id) }}">
            <x-confirmation-modal>

                <x-slot name="closeButton">
                    <button type="button"
                        class="text-gray-400 absolute top-2.5 right-2.5 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white"
                        @click="deleteConfirmation{{ addslashes($myQData->tempr_id) . addslashes($myQData->dr_id) }} = false">

                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>


                </x-slot>

                <x-slot name="descriptionText">
                    <p class="mb-4 text-gray-500 dark:text-gray-300">
                        Apakah anda yakin akan menghapus data
                        {{ $myQData->tempr_desc }}?
                    </p>
                </x-slot>

                <x-slot name="yesButton">
                    <x-red-button
                        @click="deleteConfirmation{{ addslashes($myQData->tempr_id) . addslashes($myQData->dr_id) }} = false"
                        wire:click="delete('{{ addslashes($myQData->tempr_id) }}','{{ addslashes($myQData->dr_id) }}')"
                        wire:loading.remove>
                        Ya, Saya Yakin!
                    </x-red-button>

                    <div wire:loading wire:target="batalPoli">
                        <x-loading />
                    </div>
                </x-slot>

                <x-slot name="noButton">
                    <x-light-button
                        @click="deleteConfirmation{{ addslashes($myQData->tempr_id) . addslashes($myQData->dr_id) }} = false"
                        type="button">
                        Tidak
                    </x-light-button>
                </x-slot>

            </x-confirmation-modal>
        </div>

        <x-alternative-button class="inline-flex" :disabled=$disabledProperty
            @click="deleteConfirmation{{ addslashes($myQData->tempr_id) . addslashes($myQData->dr_id) }} = true">
            <svg class="w-5 h-5 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                fill="currentColor" viewBox="0 0 18 20">
                <path
                    d="M17 4h-4V2a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v2H1a1 1 0 0 0 0 2h1v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6h1a1 1 0 1 0 0-2ZM7 2h4v2H7V2Zm1 14a1 1 0 1 1-2 0V8a1 1 0 0 1 2 0v8Zm4 0a1 1 0 0 1-2 0V8a1 1 0 0 1 2 0v8Z" />
            </svg>
            {{ '' }}
        </x-alternative-button>


    </div>
