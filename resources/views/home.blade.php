<x-guest-layout>
    <div class="min-h-screen flex flex-col justify-center items-center text-center px-4">
        <h1 class="text-3xl font-bold mb-4">
            Apuestas entre amigos
        </h1>

        <p class="text-gray-400 mb-8 max-w-md">
            Jugá con coins ficticias, competí en el leaderboard
            y seguí los eventos en tiempo real.
        </p>

        <div class="flex gap-4">
            <a href="{{ route('login') }}"
               class="px-6 py-3 bg-white text-black rounded font-semibold">
                Entrar
            </a>

            <a href="{{ route('register') }}"
               class="px-6 py-3 border border-white rounded font-semibold">
                Registrarse
            </a>
        </div>
    </div>
</x-guest-layout>
