<x-app-layout>
    <x-slot name="header">
            <div class="flex justify-between items-center">
                <h2 class="text-xl">
                    {{ __('Events') }}
                </h2>
                <div class="text-md">

                </div>  
            </div>          
    </x-slot>

    <div class="page-fade py-12 min-h-screen">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            {{-- Lista de Eventos --}}
            <div class="space-y-6">
                @forelse($events as $event)

                    @php
                        $myBet = $event->bets->first();
                    @endphp

                    <div style="padding-top:20px;" class="leaderboard bg-white rounded-xl p-8 shadow-2xl border border-gray-200">
                        <h3 style="font-family:'Bebas Neue'" class="event-title text-center text-sm uppercase tracking-wider mb-4 text-xl text-gray-600">
                            {{ $event->title }}
                        </h3>
                        {{-- Equipos --}}
                        <div class="flex justify-between items-start">

                            {{-- Team A --}}
                            <div class="flex flex-col items-center flex-1">
                                <div
                                    class="w-20 h-20 mb-4"
                                    style="
                                        @if($event->teamA)
                                            background: url('{{ $event->teamA->logo_path }}');
                                            background-size: contain;
                                            background-position: center;
                                            background-repeat: no-repeat;
                                        @endif
                                    ">
                                </div>

                                <h3 class="event-team text-[10px] font-bold uppercase text-center h-8 leading-tight">
                                    {{ $event->teamA?->name ?? 'TBD' }}
                                </h3>

                                <div class="event-rate text-lg font-black text-gray-900 mt-1">
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
                                            background: url('{{ $event->teamB->logo_path }}');
                                            background-size: contain;
                                            background-position: center;
                                            background-repeat: no-repeat;
                                        @endif
                                    ">
                                </div>

                                <h3 class="event-team text-[10px] font-bold uppercase text-center h-8 leading-tight">
                                    {{ $event->teamB?->name ?? 'TBD' }}
                                </h3>

                                <div class="event-rate text-lg font-black text-gray-900 mt-1">
                                    {{ number_format($event->payoutFor('team_b'), 2) }}x
                                </div>
                            </div>

                        </div>

                        {{-- Info secundaria --}}
                        <div class="mt-4 border-t border-gray-100 pt-4 text-center">
                            <div class="text-[10px] font-medium text-gray-400 uppercase tracking-tighter">
                                Draw:
                                <span class=" text-gray-500">
                                    {{ number_format($event->payoutFor('draw'), 2) }}x
                                </span>
                                
                            </div>

                        <div class="mt-2 text-[10px] text-gray-400">
                        <span
                            class="event-start"
                            data-start="{{ $event->starts_at->toIso8601String() }}"
                        >
                        </span>

                            <div
                                class="mt-1 text-[11px] text-gray-500 countdown"
                                data-closes-at="{{ $event->betting_closes_at?->toIso8601String() }}"
                            >
                                —
                            </div>

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
                            @elseif($event->computed_status === 'open')
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
                                                {{ $event->teamA?->name }} Won
                                            @elseif($event->result === 'team_b')
                                                {{ $event->teamB?->name }} Won
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
