<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2>
                Bet Now
            </h2>
        </div>
    </x-slot>

    <div class="page-fade py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="space-y-6">
                @forelse($events as $event)
                    @php
                        $myBet = $event->bets->first();
                    @endphp

                    <div class="leaderboard bg-white shadow-2xl sm:rounded-lg overflow-hidden">
                        <div class="p-6">
                            <h3 class="event-title text-center">
                                {{ $event->title }}
                            </h3>

                            <div class="mt-6 grid grid-cols-[1fr_auto_1fr] gap-4 items-start">
                                <div class="flex flex-col items-center min-w-0">
                                    <div
                                        class="w-20 h-20 mb-3"
                                        style="
                                            @if($event->teamA)
                                                background: url('{{ $event->teamA->logo_path }}');
                                                background-size: contain;
                                                background-position: center;
                                                background-repeat: no-repeat;
                                            @endif
                                        "
                                    ></div>

                                    <h4 class="event-team text-center">
                                        {{ $event->teamA?->name ?? 'TBD' }}
                                    </h4>

                                    <div class="event-rate mt-1">
                                        {{ number_format($event->payoutFor('team_a'), 2) }}x
                                    </div>
                                </div>

                                <div class="pt-8">
                                    <span class="event-vs">VS</span>
                                </div>

                                <div class="flex flex-col items-center min-w-0">
                                    <div
                                        class="w-20 h-20 mb-3"
                                        style="
                                            @if($event->teamB)
                                                background: url('{{ $event->teamB->logo_path }}');
                                                background-size: contain;
                                                background-position: center;
                                                background-repeat: no-repeat;
                                            @endif
                                        "
                                    ></div>

                                    <h4 class="event-team text-center">
                                        {{ $event->teamB?->name ?? 'TBD' }}
                                    </h4>

                                    <div class="event-rate mt-1">
                                        {{ number_format($event->payoutFor('team_b'), 2) }}x
                                    </div>
                                </div>
                            </div>

                            <div class="mt-5 text-center">
                                <div class="text-xs font-bold text-gray-400 uppercase">
                                    Draw:
                                    <span class="text-gray-500">
                                        {{ number_format($event->payoutFor('draw'), 2) }}x
                                    </span>
                                </div>

                                <div class="mt-2 text-xs text-gray-400">
                                    <span
                                        class="event-start"
                                        data-start="{{ $event->starts_at->toIso8601String() }}"
                                    ></span>

                                    <div
                                        class="countdown mt-1 inline-block text-[11px] font-bold text-gray-500"
                                        data-closes-at="{{ $event->betting_closes_at?->toIso8601String() }}"
                                    >
                                        -
                                    </div>
                                </div>
                            </div>

                            @include('events.partials.score-status', ['event' => $event])

                            @if($myBet)
                                @php
                                    $selectionLabel = match ($myBet->selection) {
                                        'team_a' => $event->teamA?->name ?? 'Team A',
                                        'team_b' => $event->teamB?->name ?? 'Team B',
                                        'draw' => 'Draw',
                                    };
                                @endphp

                                <div class="mt-5 rounded-lg bg-gray-50 p-4 text-center">
                                    <div class="text-xs font-black uppercase text-gray-400">
                                        Your bet
                                    </div>

                                    <div class="mt-1 text-sm font-black text-gray-800">
                                        {{ $selectionLabel }} - {{ number_format($myBet->amount, 0) }} coins
                                    </div>

                                    <a
                                        href="{{ route('events.show', $event) }}"
                                        class="mt-3 inline-flex rounded-lg border border-gray-200 px-4 py-2 text-xs font-black uppercase text-gray-600 hover:border-yellow-400 hover:text-gray-900"
                                    >
                                        View bet
                                    </a>
                                </div>
                            @elseif($event->computed_status === 'open')
                                <a
                                    href="{{ route('events.show', $event) }}"
                                    class="mt-5 block h-12 rounded-lg bg-yellow-400 text-center text-sm font-black uppercase leading-[48px] tracking-widest text-gray-900 hover:bg-yellow-300"
                                >
                                    Bet Now
                                </a>
                            @elseif($event->computed_status === 'pending')
                                <div class="mt-5 rounded-lg bg-gray-100 p-4 text-center text-sm font-bold text-gray-500">
                                    Betting opens 5 days before kickoff.
                                </div>
                            @else
                                <div class="mt-5 rounded-lg bg-gray-100 p-4 text-center text-sm font-bold text-gray-500">
                                    Betting closed.
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="leaderboard bg-white shadow sm:rounded-lg p-8 text-center">
                        <p class="text-gray-500 italic">
                            No events available.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div
        id="payout-info-modal"
        class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/60 px-4"
        role="dialog"
        aria-modal="true"
        aria-labelledby="payout-info-title"
    >
        <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-2xl">
            <h3 id="payout-info-title" class="font-['Bebas_Neue'] text-3xl text-[#444]">
                About 1x payouts
            </h3>

            <p class="mt-3 text-sm leading-6 text-gray-600">
                If both teams appear to be paying 1x, it usually means one of two things:
            </p>

            <ul class="mt-4 space-y-2 text-sm leading-6 text-gray-600">
                <li class="rounded-lg bg-gray-50 p-3">
                    No one has placed a bet on either team yet.
                </li>
                <li class="rounded-lg bg-gray-50 p-3">
                    Everyone who has bet so far placed their bet on the same team.
                </li>
            </ul>

            <p class="mt-4 text-xs text-gray-500">
                Payouts can change as more users place bets before betting closes.
            </p>

            <button
                type="button"
                id="payout-info-dismiss"
                class="mt-5 h-11 w-full rounded-lg bg-yellow-400 text-sm font-black uppercase text-gray-900"
            >
                Got it
            </button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const storageKey = 'freepickbet:payout-info-modal:v1';
            const modal = document.getElementById('payout-info-modal');
            const dismissButton = document.getElementById('payout-info-dismiss');

            if (!modal || !dismissButton || localStorage.getItem(storageKey)) {
                return;
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex');

            dismissButton.addEventListener('click', () => {
                localStorage.setItem(storageKey, 'seen');
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            });
        });
    </script>
</x-app-layout>
