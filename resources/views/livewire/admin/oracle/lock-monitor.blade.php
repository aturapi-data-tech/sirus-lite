<div>
    <div class="px-1 pt-7">
        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
            <!-- Card header -->
            <div class="w-full mb-1">

                <div id="TopBarMenuMaster" class="">

                    {{-- text Title --}}
                    <div class="mb-2">
                        <h3 class="text-2xl font-bold text-midnight dark:text-white">
                            Oracle Session Lock Monitor
                        </h3>
                        <span class="text-base font-normal text-gray-500 dark:text-gray-400">
                            Monitoring & Kill Blocking Session
                        </span>
                    </div>

                    {{-- end text Title --}}





                </div>



                <!-- Table -->
                <div class="flex flex-col mt-6">
                    <div class="overflow-x-auto rounded-lg">
                        <div class="inline-block min-w-full align-middle">
                            <div class="overflow-hidden shadow sm:rounded-lg">

                                <div class="overflow-auto border rounded" wire:poll.keep-alive.5s="refreshData">
                                    <table class="w-full text-sm table-auto">
                                        <thead class="text-left bg-gray-100">
                                            <tr>
                                                <th class="px-2 py-2">Waiter (SID,SER)</th>
                                                <th class="px-2 py-2">Waiter User</th>
                                                <th class="px-2 py-2">Waiter Program</th>
                                                <th class="px-2 py-2">Wait Event</th>
                                                <th class="px-2 py-2">Wait (s)</th>
                                                <th class="px-2 py-2">Blocker (SID,SER)</th>
                                                <th class="px-2 py-2">Blocker User</th>
                                                <th class="px-2 py-2">Blocker Program</th>
                                                <th class="px-2 py-2">Locked Object</th>
                                                <th class="px-2 py-2">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($rows as $r)
                                                @php
                                                    $bOk =
                                                        isset($r['blocker_sid'], $r['blocker_serial']) &&
                                                        is_numeric($r['blocker_sid']) &&
                                                        is_numeric($r['blocker_serial']);
                                                    $wOk =
                                                        isset($r['waiter_sid'], $r['waiter_serial']) &&
                                                        is_numeric($r['waiter_sid']) &&
                                                        is_numeric($r['waiter_serial']);
                                                @endphp

                                                <tr class="border-t">
                                                    <td class="px-2 py-2 font-mono">
                                                        {{ $r['waiter_sid'] ?? '' }},{{ $r['waiter_serial'] ?? '' }}
                                                    </td>
                                                    <td class="px-2 py-2">{{ $r['waiter_user'] ?? '-' }}</td>
                                                    <td class="px-2 py-2">{{ $r['waiter_program'] ?? '-' }}</td>
                                                    <td class="px-2 py-2">{{ $r['waiter_event'] ?? '-' }}</td>
                                                    <td class="px-2 py-2">{{ $r['waiter_seconds_wait'] ?? 0 }}</td>

                                                    <td class="px-2 py-2 font-mono">
                                                        {{ $r['blocker_sid'] ?? '' }},{{ $r['blocker_serial'] ?? '' }}
                                                    </td>
                                                    <td class="px-2 py-2">{{ $r['blocker_user'] ?? '-' }}</td>
                                                    <td class="px-2 py-2">{{ $r['blocker_program'] ?? '-' }}</td>
                                                    <td class="px-2 py-2">{{ $r['locked_object'] ?? '-' }}</td>

                                                    <td class="px-2 py-2">
                                                        <div class="flex gap-2">
                                                            <button
                                                                class="px-3 py-1 text-white bg-red-600 rounded disabled:opacity-50"
                                                                wire:click="confirmKill(@json($r['blocker_sid'] ?? null), @json($r['blocker_serial'] ?? null))"
                                                                @if (!$bOk) disabled @endif
                                                                title="{{ $bOk ? 'Kill blocker' : 'SID/SERIAL tidak tersedia' }}">
                                                                Kill Blocker
                                                            </button>

                                                            <button
                                                                class="px-3 py-1 bg-gray-200 rounded disabled:opacity-50"
                                                                wire:click="confirmKill(@json($r['waiter_sid'] ?? null), @json($r['waiter_serial'] ?? null))"
                                                                @if (!$wOk) disabled @endif
                                                                title="{{ $wOk ? 'Kill waiter' : 'SID/SERIAL tidak tersedia' }}">
                                                                Kill Waiter
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="10" class="px-2 py-6 text-center text-gray-500">
                                                        Tidak ada blocking rows terdeteksi.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>

                                </div>

                                {{-- Modal konfirmasi --}}
                                @if ($showConfirm)
                                    <div class="fixed inset-0 z-40 flex items-center justify-center bg-black/40">
                                        <div class="w-full max-w-md p-4 bg-white shadow-xl rounded-xl">
                                            <div class="text-lg font-semibold">Konfirmasi Kill Session</div>
                                            <div class="mt-2 text-sm">
                                                Anda akan menghentikan sesi
                                                <span
                                                    class="font-mono">{{ $killSid }},{{ $killSerial }}</span>.
                                                Lanjutkan?
                                            </div>
                                            <div class="flex justify-end gap-2 mt-4">
                                                <x-secondary-button
                                                    wire:click="$set('showConfirm', false)">Batal</x-secondary-button>
                                                <x-red-button wire:click="$emit('confirmKill')">Kill</x-red-button>
                                            </div>
                                        </div>
                                    </div>
                                @endif



                            </div>
                        </div>
                    </div>
                </div>




            </div>
        </div>
    </div>


</div>
