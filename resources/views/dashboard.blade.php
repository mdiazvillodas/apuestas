<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2>
                Dashboard
            </h2>

            @if(auth()->user()->role === 'admin')
                <div class="inline-flex rounded-lg border border-gray-200 bg-white p-1 shadow-sm">
                    <a
                        href="{{ route('dashboard') }}"
                        class="rounded-md bg-yellow-400 px-3 py-2 text-xs font-black uppercase text-gray-900"
                    >
                        Player
                    </a>
                    <a
                        href="{{ route('admin.dashboard') }}"
                        class="rounded-md px-3 py-2 text-xs font-black uppercase text-gray-500"
                    >
                        Admin
                    </a>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="page-fade min-h-screen px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-5xl space-y-6">
            <section class="rounded-lg bg-white p-5 shadow sm:p-6">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase text-gray-400">Current balance</p>
                        <div class="mt-1 flex items-baseline gap-2">
                            <span class="font-['Bebas_Neue'] text-5xl leading-none text-yellow-500">
                                {{ number_format($coins, 0) }}
                            </span>
                            <span class="text-sm font-bold uppercase text-gray-500">coins</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 sm:min-w-[320px]">
                        <div class="rounded-lg border border-gray-200 p-3">
                            <p class="text-[11px] font-bold uppercase text-gray-400">Bet profit</p>
                            <p class="mt-1 text-2xl font-black {{ $netProfit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $netProfit >= 0 ? '+' : '' }}{{ number_format($netProfit, 0) }}
                            </p>
                        </div>

                        <div class="rounded-lg border border-gray-200 p-3">
                            <p class="text-[11px] font-bold uppercase text-gray-400">Win rate</p>
                            <p class="mt-1 text-2xl font-black text-gray-800">
                                {{ $winRate }}%
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="rounded-lg bg-white p-5 shadow sm:p-6">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <div>
                        <h3 class="font-['Bebas_Neue'] text-3xl text-[#444]">Upcoming matches</h3>
                        <p class="text-sm text-gray-500">Next 5 events</p>
                    </div>

                    <a
                        href="{{ route('events.index') }}"
                        class="rounded-lg bg-yellow-400 px-3 py-2 text-xs font-black uppercase text-gray-900"
                    >
                        Events
                    </a>
                </div>

                @if($upcomingEvents->count())
                    <div class="space-y-3">
                        @foreach($upcomingEvents as $event)
                            <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-black text-gray-800">
                                            {{ $event->title }}
                                        </p>
                                        <p class="mt-1 text-xs text-gray-500">
                                            {{ $event->teamA?->name ?? 'TBD' }} vs {{ $event->teamB?->name ?? 'TBD' }}
                                        </p>
                                    </div>

                                    <div class="shrink-0 text-right">
                                        <p
                                            class="event-start text-xs font-bold uppercase text-gray-400"
                                            data-start="{{ $event->starts_at->toIso8601String() }}"
                                        ></p>
                                        <p
                                            class="event-countdown mt-1 text-sm font-black text-yellow-600"
                                            data-starts-at="{{ $event->starts_at->toIso8601String() }}"
                                        >
                                            -
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="py-8 text-center text-sm italic text-gray-500">
                        No upcoming matches.
                    </p>
                @endif
            </section>

            <section class="rounded-lg bg-white p-5 shadow sm:p-6">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <div>
                        <h3 class="font-['Bebas_Neue'] text-3xl text-[#444]">Coins trend</h3>
                        <p class="text-sm text-gray-500">Last 14 days</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-bold uppercase text-gray-400">Net</p>
                        <p class="text-lg font-black {{ $netProfit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $netProfit >= 0 ? '+' : '' }}{{ number_format($netProfit, 0) }}
                        </p>
                    </div>
                </div>

                <div class="h-64 sm:h-80">
                    <canvas
                        id="dashboard-profit-chart"
                        data-labels='@json($chartLabels)'
                        data-values='@json($chartValues)'
                    ></canvas>
                </div>
            </section>

            <section class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                <div class="rounded-lg bg-white p-4 shadow">
                    <p class="text-[11px] font-bold uppercase text-gray-400">Won</p>
                    <p class="mt-1 text-3xl font-black text-green-600">{{ $wonBets }}</p>
                </div>

                <div class="rounded-lg bg-white p-4 shadow">
                    <p class="text-[11px] font-bold uppercase text-gray-400">Lost</p>
                    <p class="mt-1 text-3xl font-black text-red-600">{{ $lostBets }}</p>
                </div>

                <div class="rounded-lg bg-white p-4 shadow">
                    <p class="text-[11px] font-bold uppercase text-gray-400">Won coins</p>
                    <p class="mt-1 text-3xl font-black text-green-600">+{{ number_format($coinsWon, 0) }}</p>
                </div>

                <div class="rounded-lg bg-white p-4 shadow">
                    <p class="text-[11px] font-bold uppercase text-gray-400">Lost coins</p>
                    <p class="mt-1 text-3xl font-black text-red-600">({{ number_format($coinsLost, 0) }})</p>
                </div>
            </section>

            @if($bestBet)
                @php
                    $bestSelectionLabel = match ($bestBet->selection) {
                        'team_a' => $bestBet->event->teamA?->name ?? 'Team A',
                        'team_b' => $bestBet->event->teamB?->name ?? 'Team B',
                        'draw' => 'Draw',
                        default => 'Unknown',
                    };
                @endphp

                <section class="rounded-lg bg-gray-900 p-5 text-white shadow sm:p-6">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                        <div class="min-w-0">
                            <p class="text-xs font-bold uppercase text-yellow-300">Best profit match</p>
                            <h3 class="mt-1 truncate font-['Bebas_Neue'] text-3xl text-white">
                                {{ $bestBet->event->title }}
                            </h3>
                            <p class="mt-1 text-sm text-gray-300">
                                Pick: {{ $bestSelectionLabel }} · Stake: {{ number_format($bestBet->amount, 0) }} coins
                            </p>
                        </div>

                        <div class="shrink-0 rounded-lg bg-yellow-400 px-4 py-3 text-gray-900">
                            <p class="text-[11px] font-black uppercase">Profit</p>
                            <p class="text-3xl font-black">
                                +{{ number_format($bestBet->profit, 0) }}
                            </p>
                        </div>
                    </div>
                </section>
            @endif

            <section class="rounded-lg bg-white p-5 shadow sm:p-6">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="font-['Bebas_Neue'] text-3xl text-[#444]">Recent bets</h3>
                    @if($pendingBets > 0)
                        <span class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-bold uppercase text-yellow-700">
                            {{ $pendingBets }} pending
                        </span>
                    @endif
                </div>

                @if($recentBets->count())
                    <div class="space-y-3">
                        @foreach($recentBets as $bet)
                            @php
                                $selectionLabel = match ($bet->selection) {
                                    'team_a' => $bet->event->teamA?->name ?? 'Team A',
                                    'team_b' => $bet->event->teamB?->name ?? 'Team B',
                                    'draw' => 'Draw',
                                    default => 'Unknown',
                                };
                            @endphp

                            <div class="flex items-center justify-between gap-3 border-b border-gray-100 pb-3 last:border-b-0 last:pb-0">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-black text-gray-800">
                                        {{ $bet->event->title }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ $selectionLabel }} · {{ number_format($bet->amount, 0) }} coins
                                    </p>
                                </div>

                                <div class="shrink-0 text-right">
                                    <p class="text-xs font-bold uppercase
                                        @if($bet->status === 'won') text-green-600
                                        @elseif($bet->status === 'lost') text-red-600
                                        @else text-gray-500
                                        @endif">
                                        {{ $bet->status }}
                                    </p>

                                    @if(! is_null($bet->profit))
                                        <p class="text-sm font-black {{ $bet->profit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $bet->profit >= 0 ? '+' : '' }}{{ number_format($bet->profit, 0) }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="py-8 text-center text-sm italic text-gray-500">
                        You have no bets yet.
                    </p>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
