<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Dashboard
        </h2>
    </x-slot>

    <div style="margin-top:25px;" class="max-w-xl mx-auto sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto px-4">

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">

                {{-- Wins --}}
                <div class="bg-white rounded-xl shadow p-6 text-center">
                    <p class="text-sm uppercase text-gray-500 font-bold">
                        Bets Won
                    </p>
                    <p class="mt-2 text-3xl font-black text-green-600">
                        {{ $wonBets }}
                    </p>
                </div>

                {{-- Losses --}}
                <div class="bg-white rounded-xl shadow p-6 text-center">
                    <p class="text-sm uppercase text-gray-500 font-bold">
                        Bets Lost
                    </p>
                    <p class="mt-2 text-3xl font-black text-red-600">
                        {{ $lostBets }}
                    </p>
                </div>

                {{-- Coins --}}
                <div class="bg-white rounded-xl shadow p-6 text-center">
                    <p class="text-sm uppercase text-gray-500 font-bold">
                        Coins
                    </p>
                    <p class="mt-2 text-3xl font-black text-yellow-500">
                        {{ $coins }}
                    </p>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
