<div>
    <input type="file" wire:model="file" accept=".xlsx">

    @if ($rows)
        <table class="w-full mt-4 border-collapse table-auto">
            <thead>
                <tr>
                    @foreach ($rows[0] as $colIndex => $col)
                        <th class="px-4 py-2 border">Kolom {{ $colIndex + 1 }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $row)
                    <tr>
                        @foreach ($row as $cell)
                            <td class="px-4 py-2 border">{{ $cell }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
