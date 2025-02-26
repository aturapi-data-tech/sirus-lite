<div>
    @php
        $disabledProperty = true;
        $disabledPropertyRjStatus = false;
    @endphp
    {{-- Render hanya jika data AsuhanKeperawatan ada --}}
    @if (isset($dataDaftarRi['asuhanKeperawatan']))
        <div class="w-full mb-1">
            <div class="grid grid-cols-1">
                {{-- Tab Container --}}
                <div id="TabAsuhanKeperawatan" class="px-2">
                    <div x-data="{ activeTab: 'AsuhanKeperawatan' }">
                        <!-- Navigasi Tab -->
                        <div class="px-2 mb-2 border-b border-gray-200 dark:border-gray-700">
                            <ul
                                class="flex flex-wrap -mb-px text-xs font-medium text-center text-gray-500 dark:text-gray-400">
                                <!-- Tab AsuhanKeperawatan -->
                                <li class="mr-2">
                                    <label
                                        class="inline-block p-4 border-b-2 border-transparent rounded-t-lg cursor-pointer hover:text-gray-600 hover:border-gray-300"
                                        :class="activeTab === 'AsuhanKeperawatan' ? 'text-primary border-primary bg-gray-100' :
                                            ''"
                                        @click="activeTab = 'AsuhanKeperawatan'">
                                        Asuhan Keperawatan
                                    </label>
                                </li>
                            </ul>
                        </div>

                        <!-- Konten Tab AsuhanKeperawatan -->
                        <div class="p-2 rounded-lg bg-gray-50 dark:bg-gray-800">
                            <div x-show="activeTab === 'AsuhanKeperawatan'" x-transition:enter.opacity.duration.600>
                                @include('livewire.emr-r-i.mr-r-i.asuhan-keperawatan.form-entry-asuhan-keperawatan1')
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    @endif
</div>
