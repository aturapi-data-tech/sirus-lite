<table class="w-full text-sm text-left text-gray-900 table-auto dark:text-gray-400">
    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
        <tr>
            <th class="px-4 py-2 text-center">No.</th>
            <th class="px-4 py-2 text-center">SNOMED Code</th>
            <th class="px-4 py-2 text-center">Display</th>
            <th class="px-4 py-2 text-center">Action</th>
        </tr>
    </thead>
    <tbody class="bg-white dark:bg-gray-800">
        @forelse($dataDaftarPoliRJ['anamnesa']['alergi']['alergiSnomed']??[] as $index => $alergi)
            <tr class="border-b hover:bg-gray-100 dark:border-gray-700">
                <td class="px-4 py-2 text-center">{{ $index + 1 }}</td>
                <td class="px-4 py-2 font-mono text-center">{{ $alergi['snomedCode'] ?? '-' }}</td>
                <td class="px-4 py-2">{{ $alergi['snomedDisplay'] ?? '-' }}</td>
                <td class="px-4 py-2 text-center">
                    <x-alternative-button wire:click.prevent="removeAlergiSnomed({{ $index }})"
                        class="inline-flex p-1">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 18 20">
                            <path
                                d="M17 4h-4V2a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v2H1a1 1 0 0 0 0 2h1v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6h1a1 1 0 1 0 0-2ZM7 2h4v2H7V2Zm1 14a1 1 0 1 1-2 0V8a1 1 0 0 1 2 0v8Zm4 0a1 1 0 0 1-2 0V8a1 1 0 0 1 2 0v8Z" />
                        </svg>
                    </x-alternative-button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="px-4 py-4 text-center text-gray-500">
                    Belum ada data alergi.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
