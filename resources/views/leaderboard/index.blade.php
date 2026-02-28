<x-app-layout>
    <x-slot name="header">
        <h2 class=" text-xl">
            Leaderboard
        </h2>
    </x-slot>

    <div style="margin-top:25px;" class="leaderboard max-w-xl mx-auto px-2">
        <div class="bg-white shadow rounded-lg p-6">

            <h3 class="text-lg font-bold mb-4">Top Players</h3>

            @if($users->count())

                {{-- SOLO ADMIN: abrimos el form --}}
                @if(auth()->check() && auth()->user()->role === 'admin')
                    <form method="POST" action="{{ route('admin.coins.preview') }}">
                        @csrf
                @endif

                <table class="w-full text-md">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2">#</th>

                            {{-- SOLO ADMIN: columna checkbox --}}
                            @if(auth()->check() && auth()->user()->role === 'admin')
                                <th class="text-left py-2"></th>
                            @endif

                            <th class="text-left py-2">Player</th>
                            <th class="text-right">Won</th>
                            <th class="text-right">Lost</th>
                            <th class="text-right">Balance</th>
                            <!--th class="text-right">Coins</th-->                            

                        </tr>
                    </thead>

                    <tbody>
                        @foreach($users as $index => $user)
                            <tr class="
                            border-b
                            {{ $index === 0 ? '' : '' }}
                             {{ auth()->id() === $user->id ? 'font-bold' : '' }}
                            ">
                                <td class="py-2">{{ $index + 1 }}</td>
                                {{-- SOLO ADMIN: checkbox --}}
                                @if(auth()->check() && auth()->user()->role === 'admin')
                                    <td class="py-2">
                                        <input
                                            type="checkbox"
                                            name="users[]"
                                            value="{{ $user->id }}"
                                        >
                                    </td>
                                @endif

                                <td class="py-2 capitalize">
                                {{ $user->name }}
                                @if($index === 0)
                                    <span class="mr-2 text-yellow-600 text-lg">👑</span>
                                @endif    
                                </td>
                                <td class="text-green-600 text-right">
                                    +{{ number_format($user->coins_won, 0) }}
                                </td>
                                <td class="text-red-600 text-right">
                                    ({{ number_format($user->coins_lost, 0) }})
                                </td>

                                <td class="text-right">
                                    {{ number_format($user->balance, 0) }}
                                </td>                                
                                <!--td class="py-2 text-right font-bold">
                                    {{ $user->coins }}
                                </td-->

                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- SOLO ADMIN: botón --}}
                @if(auth()->check() && auth()->user()->role === 'admin')
                        <div class="mt-4">
                            <button
                                type="submit"
                                class="px-4 py-2 bg-yellow-400 font-bold rounded"
                            >
                                Add 1000 coins
                            </button>
                        </div>
                    </form>
                @endif

            @else
                <p>No players found.</p>
            @endif

        </div>
    </div>
</x-app-layout>
