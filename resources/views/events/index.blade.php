<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2>
                Bet Now
            </h2>
        </div>
    </x-slot>

    @php
        $availableEvents = $events->filter(fn ($event) => ! $event->bets->first() && $event->computed_status === 'open');
        $pickedEvents = $events->filter(fn ($event) => $event->bets->first());
    @endphp

    <div class="page-fade min-h-screen px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-xl space-y-8">
            <section>
                <div class="mb-4 flex items-end justify-between">
                    <div>
                        <h3 class="font-['Bebas_Neue'] text-3xl text-[#444]">
                            Available Matches
                        </h3>
                        <p class="text-sm text-gray-500">
                            Pick your side before betting closes.
                        </p>
                    </div>

                    @if($availableEvents->count())
                        <span class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-black uppercase text-yellow-700">
                            {{ $availableEvents->count() }} open
                        </span>
                    @endif
                </div>

                <div class="space-y-6">
                    @forelse($availableEvents as $event)
                        <article class="leaderboard overflow-hidden rounded-lg border border-gray-200 bg-white shadow-2xl">
                            <div class="p-6">
                                <h3 class="event-title text-center">
                                    {{ $event->title }}
                                </h3>

                                <div class="mt-5 grid grid-cols-[1fr_auto_1fr] items-start gap-3">
                                    <div class="flex min-w-0 flex-col items-center">
                                        <div
                                            class="mb-3 h-20 w-20"
                                            style="
                                                @if($event->teamA)
                                                    background: url('{{ $event->teamA->logo_path }}');
                                                    background-size: contain;
                                                    background-position: center;
                                                    background-repeat: no-repeat;
                                                @endif
                                            "
                                        ></div>

                                        <h4 class="event-team h-10 text-center">
                                            {{ $event->teamA?->name ?? 'TBD' }}
                                        </h4>

                                        <div class="event-rate mt-1">
                                            {{ number_format($event->payoutFor('team_a'), 2) }}x
                                        </div>
                                    </div>

                                    <div class="pt-7">
                                        <span class="event-vs">VS</span>
                                    </div>

                                    <div class="flex min-w-0 flex-col items-center">
                                        <div
                                            class="mb-3 h-20 w-20"
                                            style="
                                                @if($event->teamB)
                                                    background: url('{{ $event->teamB->logo_path }}');
                                                    background-size: contain;
                                                    background-position: center;
                                                    background-repeat: no-repeat;
                                                @endif
                                            "
                                        ></div>

                                        <h4 class="event-team h-10 text-center">
                                            {{ $event->teamB?->name ?? 'TBD' }}
                                        </h4>

                                        <div class="event-rate mt-1">
                                            {{ number_format($event->payoutFor('team_b'), 2) }}x
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-5 border-t border-gray-100 pt-4 text-center">
                                    <div class="text-xs font-bold uppercase text-gray-400">
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
                            </div>

                            <a
                                href="{{ route('events.show', $event) }}"
                                class="block h-12 bg-yellow-400 text-center text-sm font-black uppercase leading-[48px] tracking-widest text-gray-900 hover:bg-yellow-300"
                            >
                                Bet Now
                            </a>
                        </article>
                    @empty
                        <div class="rounded-lg border border-dashed border-gray-300 bg-white px-6 py-10 text-center shadow">
                            <p class="font-['Bebas_Neue'] text-3xl text-[#444]">
                                No open matches
                            </p>
                            <p class="mt-2 text-sm text-gray-500">
                                Check back soon for new betting opportunities.
                            </p>
                        </div>
                    @endforelse
                </div>
            </section>

            <section>
                <div class="mb-4 flex items-end justify-between">
                    <div>
                        <h3 class="font-['Bebas_Neue'] text-3xl text-[#444]">
                            Your Picks
                        </h3>
                        <p class="text-sm text-gray-500">
                            Your live bets in play.
                        </p>
                    </div>

                    @if($pickedEvents->count())
                        <span class="rounded-full bg-gray-900 px-3 py-1 text-xs font-black uppercase text-white">
                            {{ $pickedEvents->count() }} picks
                        </span>
                    @endif
                </div>

                <div class="space-y-3">
                    @forelse($pickedEvents as $event)
                        @php
                            $myBet = $event->bets->first();
                            $selectionLabel = match ($myBet->selection) {
                                'team_a' => $event->teamA?->name ?? 'Team A',
                                'team_b' => $event->teamB?->name ?? 'Team B',
                                'draw' => 'Draw',
                            };
                            $hasResult = ! is_null($event->result);
                            $won = $hasResult && $myBet->selection === $event->result;
                            $statusClasses = $hasResult
                                ? ($won ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700')
                                : 'bg-yellow-100 text-yellow-700';
                            $statusLabel = $hasResult ? ($won ? 'Won' : 'Lost') : 'Pending';
                        @endphp

                        <article class="rounded-lg border border-gray-200 bg-white p-4 shadow">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="truncate text-base font-black text-gray-800">
                                        {{ $event->title }}
                                    </p>
                                    <p class="mt-1 text-xs text-gray-500">
                                        {{ $event->teamA?->name ?? 'TBD' }} vs {{ $event->teamB?->name ?? 'TBD' }}
                                    </p>
                                </div>

                                <span class="shrink-0 rounded-full px-3 py-1 text-xs font-black uppercase {{ $statusClasses }}">
                                    {{ $statusLabel }}
                                </span>
                            </div>

                            <div class="mt-4 grid grid-cols-2 gap-3">
                                <div class="rounded-lg bg-gray-50 p-3">
                                    <p class="text-[11px] font-bold uppercase text-gray-400">Your pick</p>
                                    <p class="mt-1 truncate text-sm font-black text-gray-800">
                                        {{ $selectionLabel }}
                                    </p>
                                </div>

                                <div class="rounded-lg bg-gray-50 p-3">
                                    <p class="text-[11px] font-bold uppercase text-gray-400">Stake</p>
                                    <p class="mt-1 text-sm font-black text-gray-800">
                                        {{ number_format($myBet->amount, 0) }} coins
                                    </p>
                                </div>
                            </div>

                            <div class="mt-3 flex items-center justify-between border-t border-gray-100 pt-3">
                                <div>
                                    <p
                                        class="event-start text-xs font-bold uppercase text-gray-400"
                                        data-start="{{ $event->starts_at->toIso8601String() }}"
                                    ></p>
                                    @if(! $hasResult)
                                        <p
                                            class="event-countdown mt-1 text-sm font-black text-yellow-600"
                                            data-starts-at="{{ $event->starts_at->toIso8601String() }}"
                                        >
                                            -
                                        </p>
                                    @endif
                                </div>

                                <a
                                    href="{{ route('events.show', $event) }}"
                                    class="rounded-lg border border-gray-200 px-3 py-2 text-xs font-black uppercase text-gray-600"
                                >
                                    View
                                </a>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-lg border border-dashed border-gray-300 bg-white px-6 py-8 text-center shadow">
                            <p class="text-sm italic text-gray-500">
                                Your picks will appear here after you place a bet.
                            </p>
                        </div>
                    @endforelse
                </div>
            </section>
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
