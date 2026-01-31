<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">
            Coin Grants Log
        </h2>
    </x-slot>

    <div class="py-6 max-w-6xl mx-auto">
        <div class="bg-white shadow rounded-lg p-6">

            @if($grants->count())
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2">Date</th>
                            <th class="text-left py-2">User</th>
                            <th class="text-left py-2">Granted by</th>
                            <th class="text-right py-2">Amount</th>
                            <th class="text-left py-2">Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($grants as $grant)
                            <tr class="border-b">
                                <td class="py-2">
                                    {{ $grant->created_at->format('d M Y H:i') }}
                                </td>
                                <td class="py-2 font-bold">
                                    {{ $grant->user->name }}
                                </td>
                                <td class="py-2">
                                    {{ $grant->admin->name }}
                                </td>
                                <td class="py-2 text-right font-bold text-green-600">
                                    +{{ $grant->amount }}
                                </td>
                                <td class="py-2 text-gray-500">
                                    {{ $grant->reason ?? '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-gray-500 italic">
                    No coin grants yet.
                </p>
            @endif

        </div>
    </div>
</x-app-layout>
