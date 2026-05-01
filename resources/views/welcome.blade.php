<x-guest-layout>
    <div class="w-full flex flex-col items-center justify-center text-center px-6">
        <img
            src="{{ asset('images/logo.png') }}"
            alt="Sport Bets"
            class="h-20 mx-auto mb-4"
        />

        {{-- Logo / Nombre --}}
        <div class="mb-10">
            <div class="text-4xl text-white tracking-tight">
                Freepickbet
            </div>
            <div class="mt-2 text-sm text-gray-500 mb-2">
                Bet. Compete. Climb the leaderboard.
            </div>
        </div>

        {{-- CTA --}}
        <div class="w-full max-w-xs space-y-4">
            <a
                href="{{ route('login') }}"
                class="block w-full py-3 rounded-xl bg-yellow-400 text-gray-900 font-black uppercase tracking-wide"
            >
                Log in
            </a>

            <a
                href="{{ route('register') }}"
                class="block w-full py-3 rounded-xl border border-gray-300 text-white font-bold uppercase tracking-wide"
            >
                Register
            </a>
        </div>

        {{-- Footer --}}
        <div class="mt-10 text-xs text-gray-400 mt-2">
            © {{ date('Y') }} Freepickbet
        </div>
    </div>
</x-guest-layout>
