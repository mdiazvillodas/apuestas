<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">
            My Bets
        </h2>
    </x-slot>

    <div class="py-8 bg-gray-100 min-h-screen">
        <div style="margin-top:25px;" class="max-w-xl mx-auto sm:px-6 lg:px-8">

            @if($bets->count())
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

                    @foreach($bets as $bet)
                        <div class="bg-white rounded-xl shadow p-5 flex flex-col justify-between">

                            {{-- Header: Evento --}}
                            <div>
                                <h3 class="font-bold text-sm uppercase text-gray-800">
                                    {{ $bet->event->title }}
                                </h3>

                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $bet->event->starts_at->format('M d, Y · H:i') }} CET
                                </p>
                            </div>

                            {{-- Selección --}}
                            <div class="mt-4">
                                <p class="text-xs uppercase text-gray-400">
                                    Your pick
                                </p>

                                <p class="font-bold text-gray-900">
                                    @if($bet->selection === 'team_a')
                                        {{ $bet->event->teamA?->name ?? 'Team A' }}
                                    @elseif($bet->selection === 'team_b')
                                        {{ $bet->event->teamB?->name ?? 'Team B' }}
                                    @else
                                        Draw
                                    @endif

                                </p>
                            </div>
                            {{-- Amount --}}
                            <div class="mt-3">
                                <p class="text-xs uppercase text-gray-400">
                                    Bet amount
                                </p>

                                <p class="font-bold">
                                    {{ $bet->amount }} coins
                                </p>

                                @if($bet->status === 'won')
                                    @php
                                        $payout = $bet->event->payoutFor($bet->selection);
                                        $wonCoins = round($bet->amount * $payout);
                                    @endphp

                                    <p style="color:green;" class="mt-1 text-sm font-bold text-green-700">
                                        +{{ $wonCoins }} coins
                                    </p>
                                @endif
                            </div>


                            {{-- Status --}}
                            <div class="mt-4 pt-4 border-t flex justify-between items-center">

                                <span class="text-xs uppercase text-gray-400">
                                    Status
                                </span>

                                @if($bet->status === 'won')
                                    <span style="color:green" class="text-xs font-bold px-3 py-1 rounded-full bg-green-100 text-green-700">
                                        WON
                                    </span>
                                @elseif($bet->status === 'lost')
                                    <span style="color:red" class="text-xs font-bold px-3 py-1 rounded-full bg-red-100 text-red-700">
                                        LOST
                                    </span>
                                @else
                                    <span class="text-xs font-bold px-3 py-1 rounded-full bg-gray-200 text-gray-600">
                                        PENDING
                                    </span>
                                @endif

                            </div>
                        </div>
                    @endforeach

                </div>
            @else
                <div class="text-center py-12 text-gray-500 italic">
                    You have no bets yet.
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
