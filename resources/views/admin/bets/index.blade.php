<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">
            Bets log
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 overflow-x-auto">

                @if($bets->count())
                    <table class="min-w-full border text-sm">
                        <thead class="bg-gray-100 ">
                            <tr>
                                <th class="p-2 border">Date</th>
                                <th class="p-2 border">User</th>
                                <th class="p-2 border">Event</th>
                                <th class="p-2 border">Selection</th>
                                <th class="p-2 border">Amount</th>
                                <th class="p-2 border">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bets as $bet)
                                <tr>
                                    <td class="p-2 border">
                                        {{ $bet->created_at }}
                                    </td>

                                    <td class="p-2 border">
                                        {{ $bet->user->email }}
                                    </td>

                                    <td class="p-2 border">
                                        {{ $bet->event->title }}
                                    </td>

                                    <td class="p-2 border">
                                        @if($bet->selection === 'team_a')
                                            {{ $bet->event->team_a_name }}
                                        @elseif($bet->selection === 'team_b')
                                            {{ $bet->event->team_b_name }}
                                        @else
                                            Draw
                                        @endif
                                    </td>

                                    <td class="p-2 border">
                                        {{ $bet->amount }}
                                    </td>

                                    <td class="p-2 border font-semibold">
                                        {{ strtoupper($bet->status) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p>No bets yet.</p>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
