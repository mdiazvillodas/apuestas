<x-app-layout>
    <x-slot name="header">
        <h2>
            My Bets
        </h2>
    </x-slot>

    <div class="page-fade min-h-screen px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-4xl space-y-4">
            @if($bets->count())
                @foreach($bets as $bet)
                    @php
                        $selectionLabel = match ($bet->selection) {
                            'team_a' => $bet->event->teamA?->name ?? 'Team A',
                            'team_b' => $bet->event->teamB?->name ?? 'Team B',
                            'draw' => 'Draw',
                            default => 'Unknown',
                        };

                        $statusClasses = match ($bet->status) {
                            'won' => 'bg-green-100 text-green-700 border-green-200',
                            'lost' => 'bg-red-100 text-red-700 border-red-200',
                            default => 'bg-gray-100 text-gray-600 border-gray-200',
                        };
                    @endphp

                    <article class="rounded-lg bg-white p-4 shadow sm:p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate text-base font-black text-gray-800">
                                    {{ $bet->event->title }}
                                </p>
                                <p class="mt-1 text-xs font-medium text-gray-500">
                                    {{ $bet->event->starts_at->format('M d, Y · H:i') }} CET
                                </p>
                            </div>

                            <span class="shrink-0 rounded-full border px-3 py-1 text-[11px] font-black uppercase {{ $statusClasses }}">
                                {{ $bet->status }}
                            </span>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4">
                            <div class="rounded-lg border border-gray-100 bg-gray-50 p-3">
                                <p class="text-[11px] font-bold uppercase text-gray-400">Pick</p>
                                <p class="mt-1 truncate text-sm font-black text-gray-800">
                                    {{ $selectionLabel }}
                                </p>
                            </div>

                            <div class="rounded-lg border border-gray-100 bg-gray-50 p-3">
                                <p class="text-[11px] font-bold uppercase text-gray-400">Stake</p>
                                <p class="mt-1 text-sm font-black text-gray-800">
                                    {{ number_format($bet->amount, 0) }} coins
                                </p>
                            </div>

                            <div class="rounded-lg border border-gray-100 bg-gray-50 p-3">
                                <p class="text-[11px] font-bold uppercase text-gray-400">Payout</p>
                                @if($bet->status === 'won' && ! is_null($bet->payout_amount))
                                    <p class="mt-1 text-sm font-black text-green-600">
                                        +{{ number_format($bet->payout_amount, 0) }}
                                    </p>
                                @elseif($bet->status === 'lost')
                                    <p class="mt-1 text-sm font-black text-red-600">
                                        0
                                    </p>
                                @else
                                    <p class="mt-1 text-sm font-black text-gray-400">
                                        Pending
                                    </p>
                                @endif
                            </div>

                            <div class="rounded-lg border border-gray-100 bg-gray-50 p-3">
                                <p class="text-[11px] font-bold uppercase text-gray-400">Profit</p>
                                @if(! is_null($bet->profit))
                                    <p class="mt-1 text-sm font-black {{ $bet->profit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $bet->profit >= 0 ? '+' : '' }}{{ number_format($bet->profit, 0) }}
                                    </p>
                                @else
                                    <p class="mt-1 text-sm font-black text-gray-400">
                                        Pending
                                    </p>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            @else
                <section class="rounded-lg bg-white px-6 py-12 text-center shadow">
                    <p class="font-['Bebas_Neue'] text-3xl text-[#444]">
                        No bets yet
                    </p>
                    <p class="mt-2 text-sm text-gray-500">
                        Your picks will appear here once you place your first bet.
                    </p>
                    <a
                        href="{{ route('events.index') }}"
                        class="mt-6 inline-flex h-11 items-center justify-center rounded-lg bg-yellow-400 px-5 text-sm font-black uppercase text-gray-900"
                    >
                        View events
                    </a>
                </section>
            @endif
        </div>
    </div>
</x-app-layout>
