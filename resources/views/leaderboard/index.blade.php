<x-app-layout>
    <x-slot name="header">
        <h2>
            Leaderboard
        </h2>
    </x-slot>

    <div class="page-fade min-h-screen px-4 py-8 sm:px-6 lg:px-8">
        <div class="leaderboard mx-auto max-w-3xl space-y-5">
            <section class="rounded-lg bg-white p-5 shadow sm:p-6">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h3 class="font-['Bebas_Neue'] text-3xl text-[#444]">
                            {{ $activeLeague ? $activeLeague->name : 'General leaderboard' }}
                        </h3>
                        <p class="text-sm text-gray-500">
                            Ranked by coins won from bets
                        </p>
                    </div>

                    @auth
                        <form method="GET" action="{{ route('leaderboard.index') }}">
                            <select
                                name="league"
                                onchange="this.form.submit()"
                                class="w-full rounded-lg border border-gray-300 p-2 text-sm font-bold focus:border-yellow-400 focus:ring-yellow-400 sm:w-56"
                            >
                                <option value="">General</option>
                                @foreach($availableLeagues as $league)
                                    <option value="{{ $league->id }}" @selected($activeLeague?->id === $league->id)>
                                        {{ $league->name }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    @endauth
                </div>
            </section>

            <section class="rounded-lg bg-white p-5 shadow sm:p-6">
                @if($users->count())
                    @if(auth()->check() && auth()->user()->role === 'admin')
                        <form method="POST" action="{{ route('admin.coins.preview') }}">
                            @csrf
                    @endif

                    <div class="overflow-x-auto">
                        <table class="w-full text-md">
                            <thead>
                                <tr class="border-b">
                                    <th class="py-2 text-left">#</th>

                                    @if(auth()->check() && auth()->user()->role === 'admin')
                                        <th class="py-2 text-left"></th>
                                    @endif

                                    <th class="py-2 text-left">Player</th>
                                    <th class="text-right">Won</th>
                                    <th class="text-right">Lost</th>
                                    <th class="text-right">Net</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($users as $index => $user)
                                    <tr class="border-b {{ auth()->id() === $user->id ? 'font-bold' : '' }}">
                                        <td class="py-3">{{ $index + 1 }}</td>

                                        @if(auth()->check() && auth()->user()->role === 'admin')
                                            <td class="py-3">
                                                <input
                                                    type="checkbox"
                                                    name="users[]"
                                                    value="{{ $user->id }}"
                                                >
                                            </td>
                                        @endif

                                        <td class="py-3 capitalize">
                                            {{ $user->name }}
                                            @if($index === 0)
                                                <span class="ml-1 text-lg text-yellow-600">#1</span>
                                            @endif
                                        </td>

                                        <td class="text-right text-green-600">
                                            +{{ number_format($user->coins_won, 0) }}
                                        </td>

                                        <td class="text-right text-red-600">
                                            ({{ number_format($user->coins_lost, 0) }})
                                        </td>

                                        <td class="text-right">
                                            {{ number_format($user->balance, 0) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if(auth()->check() && auth()->user()->role === 'admin')
                            <div class="mt-4">
                                <button
                                    type="submit"
                                    class="rounded-lg bg-yellow-400 px-4 py-2 text-sm font-black uppercase text-gray-900"
                                >
                                    Add 1000 coins
                                </button>
                            </div>
                        </form>
                    @endif
                @else
                    <p class="py-8 text-center text-sm italic text-gray-500">
                        No players found.
                    </p>
                @endif
            </section>
        </div>
    </div>

    <div
        id="leaderboard-info-modal"
        class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/60 px-4"
        role="dialog"
        aria-modal="true"
        aria-labelledby="leaderboard-info-title"
    >
        <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-2xl">
            <h3 id="leaderboard-info-title" class="font-['Bebas_Neue'] text-3xl text-[#444]">
                How the leaderboard works
            </h3>

            <p class="mt-3 text-sm leading-6 text-gray-600">
                The winner is the player with the most coins won from bets, not the player with the highest coin balance.
            </p>

            <div class="mt-4 rounded-lg bg-gray-50 p-4 text-sm leading-6 text-gray-600">
                Net profit and balance can help you understand performance, but ranking is decided by total coins won. Keeping coins without betting does not move a player up the leaderboard.
            </div>

            <p class="mt-4 text-sm leading-6 text-gray-600">
                You can also create or join private leagues and compete with friends using the same bets.
            </p>

            <a
                href="{{ route('leagues.index') }}"
                class="mt-5 flex h-11 w-full items-center justify-center rounded-lg bg-yellow-400 text-sm font-black uppercase text-gray-900"
            >
                Try leagues
            </a>

            <button
                type="button"
                id="leaderboard-info-dismiss"
                class="mt-3 h-11 w-full rounded-lg border border-gray-200 text-sm font-black uppercase text-gray-600"
            >
                Got it
            </button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const storageKey = 'freepickbet:leaderboard-info-modal:v1';
            const modal = document.getElementById('leaderboard-info-modal');
            const dismissButton = document.getElementById('leaderboard-info-dismiss');

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
