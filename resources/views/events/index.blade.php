<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            {{ __('Sport Bets') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-[#1a2c42] min-h-screen">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">

            {{-- Balance --}}
            <div class="mb-8 flex justify-between items-center bg-[#0f1d2e] p-4 rounded-xl border border-slate-700">
                <span class="font-bold uppercase tracking-wider text-sm text-white">Eventos</span>
                <div class="text-gray-300 text-xs">
                    <span class="opacity-70">Balance:</span>
                    <strong class="ml-1">
                        {{ auth()->user()->coins }}
                        <span class="text-[10px] border border-gray-500 rounded-full px-1">s</span>
                    </strong>
                </div>
            </div>

            {{-- Lista de Eventos --}}
            <div class="space-y-6">
                @forelse($events as $event)

                    @php
                        $myBet = $event->bets->first();
                    @endphp

                    <div style="padding-top:20px;" class="bg-white rounded-xl p-8 shadow-2xl border border-gray-200">

                        {{-- Equipos --}}
                        <div class="flex justify-between items-start">

                            {{-- Team A --}}
                            <div class="flex flex-col items-center flex-1">
                                <div
                                    class="w-20 h-20 mb-4"
                                    style="
                                        @if($event->teamA)
                                            background: url('{{ asset('storage/'.$event->teamA->logo_path) }}');
                                            background-size: contain;
                                            background-position: center;
                                            background-repeat: no-repeat;
                                        @endif
                                    ">
                                </div>

                                <h3 class="text-[10px] font-bold uppercase text-center h-8 leading-tight">
                                    {{ $event->teamA?->name ?? 'TBD' }}
                                </h3>

                                <div class="text-lg font-black text-gray-900 mt-1">
                                    {{ number_format($event->payoutFor('team_a'), 2) }}x
                                </div>
                            </div>

                            {{-- VS --}}
                            <div class="pt-6 px-2">
                                <span class="text-2xl font-black italic text-gray-900">VS</span>
                            </div>

                            {{-- Team B --}}
                            <div class="flex flex-col items-center flex-1">
                                <div
                                    class="w-20 h-20 mb-4"
                                    style="
                                        @if($event->teamB)
                                            background: url('{{ asset('storage/'.$event->teamB->logo_path) }}');
                                            background-size: contain;
                                            background-position: center;
                                            background-repeat: no-repeat;
                                        @endif
                                    ">
                                </div>

                                <h3 class="text-[10px] font-bold uppercase text-center h-8 leading-tight">
                                    {{ $event->teamB?->name ?? 'TBD' }}
                                </h3>

                                <div class="text-lg font-black text-gray-900 mt-1">
                                    {{ number_format($event->payoutFor('team_b'), 2) }}x
                                </div>
                            </div>

                        </div>

                        {{-- Info secundaria --}}
                        <div class="mt-4 border-t border-gray-100 pt-4 text-center">
                            <div class="text-[10px] font-medium text-gray-400 uppercase tracking-tighter">
                                Draw:
                                <span class="font-bold text-gray-500">
                                    {{ number_format($event->payoutFor('draw'), 2) }}x
                                </span>
                            </div>

                            <div class="mt-2 text-[10px] font-bold text-gray-400">
                                {{ $event->starts_at->timezone('Europe/Madrid')->format('M d, Y · H:i') }} CET
                            </div>
                        </div>

                        {{-- Acción --}}
                        <div class="mt-6">
                            @php
                                $now = now();
                            @endphp

                            @if($myBet)

                                @php
                                    $selectionLabel = match ($myBet->selection) {
                                        'team_a' => $event->teamA?->name ?? 'Team A',
                                        'team_b' => $event->teamB?->name ?? 'Team B',
                                        'draw'   => 'Draw',
                                    };

                                    $hasResult = !is_null($event->result);
                                    $won = $hasResult && $myBet->selection === $event->result;
                                @endphp

                                <div class="p-3 text-center rounded-lg
                                    {{ $hasResult ? ($won ? 'bg-green-100' : 'bg-red-100') : 'bg-gray-100' }}">

                                    <div class="text-[10px] font-bold uppercase text-gray-400">
                                        Your bet
                                    </div>

                                    <div class="mt-1 text-sm font-black">
                                        {{ strtoupper($selectionLabel) }}
                                    </div>

                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ $myBet->amount }} coins
                                    </div>

                                    @if($hasResult)
                                        <div style="font-weight:700; padding-bottom:10px;" class="mt-2 text-xs font-bold uppercase
                                            {{ $won ? 'text-green-700' : 'text-red-700' }}">
                                            {{ $won ? 'You won' : 'You Lost' }}
                                        </div>
                                    @else
                                        <div class="mt-2 text-[10px] font-bold uppercase text-gray-400">
                                            Pending
                                        </div>
                                    @endif
                                </div>

                            @elseif($now->between($event->betting_opens_at, $event->betting_closes_at))

                                <a
                                    href="{{ route('events.show', $event) }}"
                                    class="block w-full h-[50px] leading-[50px]
                                        bg-yellow-400 text-center
                                        text-xs font-black uppercase tracking-widest
                                        text-gray-900 rounded-b-xl"
                                    style="
                                        height: 50px;
                                        background: gold;
                                        font-weight: 700;
                                        font-size: 1rem;
                                        line-height: 50px;
                                        border-radius: 0px 0px 10px 10px;"
                                >
                                    Bet Now
                                </a>

                            @else

                                <div class="w-full py-3 bg-gray-100 text-center text-[10px] font-bold uppercase">
                                    @if(is_null($event->result))
                                        <span class="text-yellow-500">
                                            Pending
                                        </span>
                                    @else
                                        <span class="text-blue-600">
                                            @if($event->result === 'team_a')
                                                Ganó {{ $event->teamA?->name }}
                                            @elseif($event->result === 'team_b')
                                                Ganó {{ $event->teamB?->name }}
                                            @else
                                                Draw
                                            @endif
                                        </span>
                                    @endif
                                </div>

                            @endif
                        </div>



                    </div>
                @empty
                    <div class="text-center py-10">
                        <p class="text-gray-500 italic">No events available at the moment.</p>
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>
