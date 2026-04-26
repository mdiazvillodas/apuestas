<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2>
                Admin Dashboard
            </h2>

            <div class="inline-flex rounded-lg border border-gray-200 bg-white p-1 shadow-sm">
                <a
                    href="{{ route('dashboard') }}"
                    class="rounded-md px-3 py-2 text-xs font-black uppercase text-gray-500"
                >
                    Player
                </a>
                <a
                    href="{{ route('admin.dashboard') }}"
                    class="rounded-md bg-yellow-400 px-3 py-2 text-xs font-black uppercase text-gray-900"
                >
                    Admin
                </a>
            </div>
        </div>
    </x-slot>

    <div class="page-fade min-h-screen px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-6xl space-y-6">
            <section class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                <div class="rounded-lg bg-white p-5 shadow">
                    <p class="text-[11px] font-bold uppercase text-gray-400">Total bets</p>
                    <p class="mt-1 text-4xl font-black text-gray-800">
                        {{ number_format($totalBets, 0) }}
                    </p>
                </div>

                <div class="rounded-lg bg-white p-5 shadow">
                    <p class="text-[11px] font-bold uppercase text-gray-400">Registered users</p>
                    <p class="mt-1 text-4xl font-black text-gray-800">
                        {{ number_format($totalUsers, 0) }}
                    </p>
                </div>

                <div class="rounded-lg bg-white p-5 shadow">
                    <p class="text-[11px] font-bold uppercase text-gray-400">Coins staked</p>
                    <p class="mt-1 text-4xl font-black text-yellow-500">
                        {{ number_format($totalCoinsStaked, 0) }}
                    </p>
                </div>
            </section>

            <section class="rounded-lg bg-white p-5 shadow sm:p-6">
                <div class="mb-4">
                    <h3 class="font-['Bebas_Neue'] text-3xl text-[#444]">Betting activity</h3>
                    <p class="text-sm text-gray-500">
                        Daily coins staked and number of bets over the last 14 days
                    </p>
                </div>

                <div class="h-72 sm:h-96">
                    <canvas
                        id="admin-activity-chart"
                        data-labels='@json($chartLabels)'
                        data-coins='@json($chartCoins)'
                        data-bets='@json($chartBets)'
                    ></canvas>
                </div>
            </section>

            <section class="rounded-lg bg-white p-5 shadow sm:p-6">
                <div class="mb-4 flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h3 class="font-['Bebas_Neue'] text-3xl text-[#444]">Unverified users</h3>
                        <p class="text-sm text-gray-500">
                            Users waiting for email verification
                        </p>
                    </div>

                    @if($unverifiedUsers->count())
                        <span class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-black uppercase text-yellow-700">
                            {{ $unverifiedUsers->count() }} pending
                        </span>
                    @endif
                </div>

                @if(session('success'))
                    <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-bold text-green-700">
                        {{ session('success') }}
                    </div>
                @endif

                @if($unverifiedUsers->count())
                    <div class="space-y-3">
                        @foreach($unverifiedUsers as $user)
                            <div class="flex items-center justify-between gap-3 rounded-lg border border-gray-100 bg-gray-50 p-4">
                                <div class="min-w-0">
                                    <p class="truncate font-black text-gray-800">
                                        {{ $user->name }}
                                    </p>
                                    <p class="truncate text-xs text-gray-500">
                                        {{ $user->email }}
                                    </p>
                                </div>

                                <form method="POST" action="{{ route('admin.users.verify-email', $user) }}">
                                    @csrf
                                    <button class="rounded-lg bg-yellow-400 px-3 py-2 text-xs font-black uppercase text-gray-900">
                                        Verify
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="py-8 text-center text-sm italic text-gray-500">
                        There are no unverified users.
                    </p>
                @endif
            </section>

            <section class="rounded-lg bg-white p-5 shadow sm:p-6">
                <div class="mb-4">
                    <h3 class="font-['Bebas_Neue'] text-3xl text-[#444]">Top bettors</h3>
                    <p class="text-sm text-gray-500">
                        Top 5 users by total coins staked
                    </p>
                </div>

                @if($topBettors->count())
                    <div class="space-y-3">
                        @foreach($topBettors as $index => $user)
                            <div class="flex items-center justify-between gap-3 rounded-lg border border-gray-100 bg-gray-50 p-4">
                                <div class="flex min-w-0 items-center gap-3">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gray-900 text-sm font-black text-white">
                                        {{ $index + 1 }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="truncate font-black text-gray-800">
                                            {{ $user->name }}
                                        </p>
                                        <p class="truncate text-xs text-gray-500">
                                            {{ $user->email }}
                                        </p>
                                    </div>
                                </div>

                                <div class="shrink-0 text-right">
                                    <p class="text-lg font-black text-yellow-500">
                                        {{ number_format($user->coins_staked, 0) }}
                                    </p>
                                    <p class="text-xs font-bold uppercase text-gray-400">
                                        {{ number_format($user->bets_count, 0) }} bets
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="py-8 text-center text-sm italic text-gray-500">
                        No bets yet.
                    </p>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
