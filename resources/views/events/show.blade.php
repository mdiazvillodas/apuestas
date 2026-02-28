<x-app-layout>
    <x-slot name="header">
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800">
                    {{ __('Place your bet') }}
                </h2>
            </div>     
    </x-slot>

    <div class="page-fade py-12 min-h-screen">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <a
                href="{{ route('events.index') }}"
                class="inline-flex items-center mb-4 text-sm font-semibold text-gray-500 hover:text-gray-800"
            >
                ← Back to events
            </a>




            {{-- Event card --}}
            <div class="bg-white rounded-xl p-8 shadow-2xl mb-6 border border-gray-200">
                <h3 class="event-title text-center text-xl font-black text-gray-900 mb-4">
                    {{ $event->title }}
                </h3>
                <div class="flex justify-between items-start">


                    {{-- Team A --}}
                    <div class="flex flex-col items-center flex-1">
                        <div
                            class="w-20 h-20 mb-4"
                            style="
                                background: url('{{ asset('storage/' . optional($event->teamA)->logo_path) }}');
                                background-size: contain;
                                background-position: center;
                                background-repeat: no-repeat;
                            ">
                        </div>

                        <h3 class="event-team text-[10px] font-bold uppercase text-center h-8 leading-tight">
                            {{ optional($event->teamA)->name ?? '—' }}
                        </h3>

                        <div class="event-rate text-lg font-black text-gray-900 mt-1">
                            {{ number_format($event->payoutFor('team_a'), 2) }}x
                        </div>
                    </div>

                    <div class="pt-6 px-2">
                        <span class="text-2xl font-black italic text-gray-900">VS</span>
                    </div>

                    {{-- Team B --}}
                    <div class="flex flex-col items-center flex-1">
                        <div
                            class="w-20 h-20 mb-4"
                            style="
                                background: url('{{ asset('storage/' . optional($event->teamB)->logo_path) }}');
                                background-size: contain;
                                background-position: center;
                                background-repeat: no-repeat;
                            ">
                        </div>

                        <h3 class="event-team text-[10px] font-bold uppercase text-center h-8 leading-tight">
                            {{ optional($event->teamB)->name ?? '—' }}
                        </h3>

                        <div class="event-rate text-lg font-black text-gray-900 mt-1">
                            {{ number_format($event->payoutFor('team_b'), 2) }}x
                        </div>
                    </div>
                </div>

                {{-- Meta --}}
                <div class="mt-4 border-t border-gray-100 pt-4 text-center">
                    <div class="text-[10px] font-medium text-gray-400 uppercase">
                        Draw · {{ number_format($event->payoutFor('draw'), 2) }}x
                    </div>

                    <div class="mt-2 text-[10px] font-bold text-gray-400">
                        {{ $event->starts_at->timezone('Europe/Madrid')->format('M d, Y · H:i') }} CET
                    </div>
                </div>
            </div>

            {{-- Already bet --}}
            @if($myBet)
                <div class="border border-slate-700 rounded-xl p-4 text-center">
                    <p class="font-bold uppercase text-xs">You already placed a bet</p>

                    <p class="mt-2 text-sm font-black">
                        @php
                            $selectionLabel = match ($myBet->selection) {
                                'team_a' => optional($event->teamA)->name,
                                'team_b' => optional($event->teamB)->name,
                                'draw'   => 'Draw',
                            };
                        @endphp

                        {{ strtoupper($selectionLabel) }} · {{ $myBet->amount }} coins
                    </p>

                    <p class="text-xs opacity-70 mt-1">
                        Status: {{ strtoupper($myBet->status) }}
                    </p>
                </div>

            {{-- Bet form --}}
            @else
                <form method="POST" action="{{ route('bets.store', $event) }}" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-3 gap-3">
                        @foreach([
                            'team_a' => optional($event->teamA)->name,
                            'draw'   => 'Draw',
                            'team_b' => optional($event->teamB)->name
                        ] as $value => $label)
                            <label class="cursor-pointer">
                                <input type="radio" name="selection" value="{{ $value }}" class="hidden" required>

                                <div class="bet-option p-4 rounded-xl border text-center text-xs font-bold uppercase border-gray-300 text-gray-700">
                                    {{ $label }}
                                </div>
                            </label>
                        @endforeach
                    </div>

                    <input
                        type="number"
                        name="amount"
                        min="1"
                        max="{{ auth()->user()->coins }}"
                        placeholder="Coins to bet"
                        required
                        class="w-full rounded-xl border p-4 text-center font-bold"
                    >

                    <button class="w-full h-[50px] bg-yellow-400 rounded-xl font-black uppercase text-gray-900">
                        Confirm Bet
                    </button>
                </form>
            @endif
        </div>
    </div>

    <style>
        .bet-option.selected {
            background-color: #fde047;
            border-color: #facc15;
            color: #111827;
            box-shadow: 0 4px 10px rgba(0,0,0,.15);
        }
    </style>

    <script>
        document.querySelectorAll('.bet-option').forEach(option => {
            option.addEventListener('click', () => {
                document.querySelectorAll('.bet-option').forEach(o => o.classList.remove('selected'));
                option.classList.add('selected');
            });
        });
    </script>

</x-app-layout>
