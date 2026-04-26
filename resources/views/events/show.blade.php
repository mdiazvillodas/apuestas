<x-app-layout>
    <x-slot name="header">
        <h2>
            Place your bet
        </h2>
    </x-slot>

    <div class="page-fade min-h-screen px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-xl space-y-5">
            <a
                href="{{ route('events.index') }}"
                class="inline-flex items-center text-sm font-semibold text-gray-500 hover:text-gray-800"
            >
                Back to events
            </a>

            @if($errors->any())
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-bold text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            @if(session('success'))
                <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-bold text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <section class="rounded-lg bg-white p-5 shadow sm:p-6">
                <h3 class="event-title text-center">
                    {{ $event->title }}
                </h3>

                <div class="mt-5 grid grid-cols-[1fr_auto_1fr] items-start gap-3">
                    <div class="flex min-w-0 flex-col items-center">
                        <div
                            class="mb-3 h-20 w-20"
                            style="
                                background: url('{{ $event->teamA->logo_path }}');
                                background-size: contain;
                                background-position: center;
                                background-repeat: no-repeat;
                            "
                        ></div>
                        <p class="event-team h-10 text-center">
                            {{ optional($event->teamA)->name ?? '-' }}
                        </p>
                        <p class="event-rate mt-1">
                            {{ number_format($event->payoutFor('team_a'), 2) }}x
                        </p>
                    </div>

                    <div class="pt-7">
                        <span class="event-vs">VS</span>
                    </div>

                    <div class="flex min-w-0 flex-col items-center">
                        <div
                            class="mb-3 h-20 w-20"
                            style="
                                background: url('{{ $event->teamB->logo_path }}');
                                background-size: contain;
                                background-position: center;
                                background-repeat: no-repeat;
                            "
                        ></div>
                        <p class="event-team h-10 text-center">
                            {{ optional($event->teamB)->name ?? '-' }}
                        </p>
                        <p class="event-rate mt-1">
                            {{ number_format($event->payoutFor('team_b'), 2) }}x
                        </p>
                    </div>
                </div>

                <div class="mt-5 border-t border-gray-100 pt-4 text-center">
                    <p class="text-xs font-bold uppercase text-gray-400">
                        Draw · {{ number_format($event->payoutFor('draw'), 2) }}x
                    </p>
                    <p class="mt-1 text-xs font-bold text-gray-400">
                        {{ $event->starts_at->timezone('Europe/Madrid')->format('M d, Y · H:i') }} CET
                    </p>
                </div>
            </section>

            @if($myBet)
                @php
                    $selectionLabel = match ($myBet->selection) {
                        'team_a' => optional($event->teamA)->name,
                        'team_b' => optional($event->teamB)->name,
                        'draw' => 'Draw',
                    };
                @endphp

                <section class="rounded-lg border border-gray-200 bg-white p-5 text-center shadow">
                    <p class="text-xs font-bold uppercase text-gray-400">You already placed a bet</p>
                    <p class="mt-2 text-lg font-black text-gray-800">
                        {{ strtoupper($selectionLabel) }} · {{ number_format($myBet->amount, 0) }} coins
                    </p>
                    <p class="mt-1 text-xs font-bold uppercase text-gray-500">
                        Status: {{ $myBet->status }}
                    </p>
                </section>
            @else
                <form
                    method="POST"
                    action="{{ route('bets.store', $event) }}"
                    class="space-y-5 rounded-lg bg-white p-5 shadow sm:p-6"
                    id="bet-form"
                >
                    @csrf

                    <div class="flex items-center justify-between gap-3">
                        <p class="text-xs font-bold uppercase text-gray-400">Available</p>
                        <p class="text-sm font-black text-yellow-600">
                            {{ number_format(auth()->user()->coins, 0) }} coins
                        </p>
                    </div>

                    <div class="grid grid-cols-3 gap-2">
                        @foreach([
                            'team_a' => [
                                'label' => optional($event->teamA)->name,
                                'payout' => $event->payoutFor('team_a'),
                            ],
                            'draw' => [
                                'label' => 'Draw',
                                'payout' => $event->payoutFor('draw'),
                            ],
                            'team_b' => [
                                'label' => optional($event->teamB)->name,
                                'payout' => $event->payoutFor('team_b'),
                            ],
                        ] as $value => $option)
                            <label class="cursor-pointer">
                                <input
                                    type="radio"
                                    name="selection"
                                    value="{{ $value }}"
                                    data-payout="{{ $option['payout'] }}"
                                    class="peer sr-only"
                                    required
                                    @checked(old('selection') === $value)
                                >
                                <span class="block min-h-20 rounded-lg border border-gray-200 p-3 text-center text-xs font-black uppercase text-gray-700 transition peer-checked:border-yellow-400 peer-checked:bg-yellow-300 peer-checked:text-gray-900">
                                    <span class="block truncate">{{ $option['label'] }}</span>
                                    <span class="mt-1 block text-sm">{{ number_format($option['payout'], 2) }}x</span>
                                </span>
                            </label>
                        @endforeach
                    </div>

                    <div>
                        <label for="amount" class="mb-2 block text-xs font-bold uppercase text-gray-400">
                            Coins to bet
                        </label>
                        <input
                            id="amount"
                            type="number"
                            name="amount"
                            min="1"
                            max="{{ auth()->user()->coins }}"
                            step="1"
                            inputmode="numeric"
                            placeholder="Enter amount"
                            value="{{ old('amount') }}"
                            required
                            class="w-full rounded-lg border border-gray-300 p-4 text-center text-xl font-black text-gray-800 focus:border-yellow-400 focus:ring-yellow-400"
                        >

                        <div class="mt-3 grid grid-cols-3 gap-2">
                            <button type="button" class="quick-amount rounded-lg bg-gray-100 py-2 text-xs font-black uppercase text-gray-700" data-percent="25">
                                25%
                            </button>
                            <button type="button" class="quick-amount rounded-lg bg-gray-100 py-2 text-xs font-black uppercase text-gray-700" data-percent="50">
                                50%
                            </button>
                            <button type="button" class="quick-amount rounded-lg bg-gray-100 py-2 text-xs font-black uppercase text-gray-700" data-percent="100">
                                Max
                            </button>
                        </div>
                    </div>

                    <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                        <div class="flex items-center justify-between">
                            <p class="text-xs font-bold uppercase text-gray-400">Potential payout</p>
                            <p class="text-lg font-black text-gray-800" id="potential-payout">-</p>
                        </div>
                        <div class="mt-2 flex items-center justify-between">
                            <p class="text-xs font-bold uppercase text-gray-400">Estimated profit</p>
                            <p class="text-lg font-black text-green-600" id="estimated-profit">-</p>
                        </div>
                    </div>

                    <button
                        type="submit"
                        id="bet-submit"
                        class="h-12 w-full rounded-lg bg-yellow-400 text-sm font-black uppercase text-gray-900 transition hover:bg-yellow-300 disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        Confirm Bet
                    </button>
                </form>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('bet-form');
            if (!form) return;

            const amountInput = document.getElementById('amount');
            const payoutEl = document.getElementById('potential-payout');
            const profitEl = document.getElementById('estimated-profit');
            const submitButton = document.getElementById('bet-submit');
            const maxCoins = Number(amountInput.max || 0);

            function selectedPayout() {
                const selected = form.querySelector('input[name="selection"]:checked');
                return selected ? Number(selected.dataset.payout || 0) : 0;
            }

            function updateEstimate() {
                const amount = Number(amountInput.value || 0);
                const payout = selectedPayout();

                if (!amount || !payout) {
                    payoutEl.textContent = '-';
                    profitEl.textContent = '-';
                    return;
                }

                const potential = Math.round(amount * payout);
                const profit = potential - amount;

                payoutEl.textContent = `${potential.toLocaleString()} coins`;
                profitEl.textContent = `${profit >= 0 ? '+' : ''}${profit.toLocaleString()} coins`;
                profitEl.classList.toggle('text-green-600', profit >= 0);
                profitEl.classList.toggle('text-red-600', profit < 0);
            }

            form.querySelectorAll('input[name="selection"]').forEach((input) => {
                input.addEventListener('change', updateEstimate);
            });

            amountInput.addEventListener('input', () => {
                if (Number(amountInput.value) > maxCoins) {
                    amountInput.value = maxCoins;
                }

                updateEstimate();
            });

            document.querySelectorAll('.quick-amount').forEach((button) => {
                button.addEventListener('click', () => {
                    const percent = Number(button.dataset.percent);
                    amountInput.value = Math.max(1, Math.floor(maxCoins * (percent / 100)));
                    updateEstimate();
                });
            });

            form.addEventListener('submit', () => {
                submitButton.disabled = true;
                submitButton.textContent = 'Placing bet...';
            });

            updateEstimate();
        });
    </script>
</x-app-layout>
