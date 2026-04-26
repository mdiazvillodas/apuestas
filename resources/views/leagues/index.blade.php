<x-app-layout>
    <x-slot name="header">
        <h2>
            My Leagues
        </h2>
    </x-slot>

    <div class="page-fade min-h-screen px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-5xl space-y-6">
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

            <section class="grid gap-4 lg:grid-cols-[1fr_360px]">
                <div class="rounded-lg bg-white p-5 shadow sm:p-6">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <div>
                            <h3 class="font-['Bebas_Neue'] text-3xl text-[#444]">Your leagues</h3>
                            <p class="text-sm text-gray-500">
                                {{ $leagues->count() }} of {{ $maxPrivateLeagues }} private leagues
                            </p>
                        </div>
                    </div>

                    @if($leagues->count())
                        <div class="space-y-3">
                            @foreach($leagues as $league)
                                <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="truncate text-lg font-black text-gray-800">
                                                {{ $league->name }}
                                            </p>
                                            <p class="text-xs font-bold uppercase text-gray-400">
                                                ID #{{ $league->id }} · Owner: {{ $league->owner->name }}
                                            </p>
                                        </div>

                                        <a
                                            href="{{ route('leaderboard.index', ['league' => $league->id]) }}"
                                            class="shrink-0 rounded-lg bg-yellow-400 px-3 py-2 text-xs font-black uppercase text-gray-900"
                                        >
                                            Leaderboard
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="rounded-lg border border-dashed border-gray-200 px-4 py-8 text-center text-sm italic text-gray-500">
                            You are not in any private league yet.
                        </p>
                    @endif

                    @if($pendingRequests->count())
                        <div class="mt-5 rounded-lg border border-yellow-200 bg-yellow-50 p-4">
                            <p class="text-xs font-black uppercase text-yellow-700">Pending join requests</p>
                            <div class="mt-2 space-y-1">
                                @foreach($pendingRequests as $pendingRequest)
                                    <p class="text-sm font-bold text-gray-700">
                                        {{ $pendingRequest->league->name }} · ID #{{ $pendingRequest->league->id }}
                                    </p>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <div class="rounded-lg bg-white p-5 shadow sm:p-6">
                    <h3 class="font-['Bebas_Neue'] text-3xl text-[#444]">Create league</h3>

                    @if($ownedLeague)
                        <div class="mt-4 rounded-lg border border-gray-100 bg-gray-50 p-4">
                            <p class="text-xs font-bold uppercase text-gray-400">You own</p>
                            <p class="mt-1 text-lg font-black text-gray-800">
                                {{ $ownedLeague->name }}
                            </p>
                            <p class="text-sm text-gray-500">
                                ID #{{ $ownedLeague->id }}
                            </p>
                        </div>
                    @elseif($leagues->count() >= $maxPrivateLeagues)
                        <p class="mt-4 rounded-lg border border-gray-100 bg-gray-50 p-4 text-sm text-gray-500">
                            You reached the private league limit.
                        </p>
                    @else
                        <form method="POST" action="{{ route('leagues.store') }}" class="mt-4 space-y-3">
                            @csrf
                            <input
                                type="text"
                                name="name"
                                value="{{ old('name') }}"
                                maxlength="80"
                                placeholder="League name"
                                required
                                class="w-full rounded-lg border border-gray-300 p-3 text-sm font-bold focus:border-yellow-400 focus:ring-yellow-400"
                            >
                            <button class="h-11 w-full rounded-lg bg-yellow-400 text-sm font-black uppercase text-gray-900">
                                Create
                            </button>
                        </form>
                    @endif
                </div>
            </section>

            <section class="rounded-lg bg-white p-5 shadow sm:p-6">
                <h3 class="font-['Bebas_Neue'] text-3xl text-[#444]">Find league</h3>
                <form method="GET" action="{{ route('leagues.index') }}" class="mt-4 flex gap-2">
                    <input
                        type="text"
                        name="q"
                        value="{{ $searchQuery }}"
                        placeholder="Search by league name or ID"
                        class="min-w-0 flex-1 rounded-lg border border-gray-300 p-3 text-sm font-bold focus:border-yellow-400 focus:ring-yellow-400"
                    >
                    <button class="rounded-lg bg-gray-900 px-4 text-sm font-black uppercase text-white">
                        Search
                    </button>
                </form>

                @if($searchQuery !== '')
                    <div class="mt-5 space-y-3">
                        @forelse($searchResults as $league)
                            @php
                                $isMember = $memberLeagueIds->contains($league->id);
                                $requestStatus = $requestStatuses[$league->id] ?? null;
                            @endphp

                            <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="truncate text-base font-black text-gray-800">
                                            {{ $league->name }}
                                        </p>
                                        <p class="text-xs font-bold uppercase text-gray-400">
                                            ID #{{ $league->id }} · {{ $league->members_count }} members · Owner: {{ $league->owner->name }}
                                        </p>
                                    </div>

                                    @if($isMember)
                                        <span class="shrink-0 rounded-full bg-green-100 px-3 py-1 text-xs font-black uppercase text-green-700">
                                            Member
                                        </span>
                                    @elseif($requestStatus === 'pending')
                                        <span class="shrink-0 rounded-full bg-yellow-100 px-3 py-1 text-xs font-black uppercase text-yellow-700">
                                            Pending
                                        </span>
                                    @elseif($leagues->count() >= $maxPrivateLeagues)
                                        <span class="shrink-0 rounded-full bg-gray-200 px-3 py-1 text-xs font-black uppercase text-gray-600">
                                            Limit
                                        </span>
                                    @else
                                        <form method="POST" action="{{ route('leagues.join-requests.store', $league) }}">
                                            @csrf
                                            <button class="shrink-0 rounded-lg bg-yellow-400 px-3 py-2 text-xs font-black uppercase text-gray-900">
                                                Request
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="rounded-lg border border-dashed border-gray-200 px-4 py-8 text-center text-sm italic text-gray-500">
                                No leagues found.
                            </p>
                        @endforelse
                    </div>
                @endif
            </section>

            @if($ownedLeague)
                <section class="grid gap-4 lg:grid-cols-2">
                    <div class="rounded-lg bg-white p-5 shadow sm:p-6">
                        <h3 class="font-['Bebas_Neue'] text-3xl text-[#444]">Join requests</h3>

                        @if($ownedLeague->pendingJoinRequests->count())
                            <div class="mt-4 space-y-3">
                                @foreach($ownedLeague->pendingJoinRequests as $joinRequest)
                                    <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                                        <p class="font-black text-gray-800">
                                            {{ $joinRequest->user->name }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{ $joinRequest->user->email }}
                                        </p>

                                        <div class="mt-3 flex gap-2">
                                            <form method="POST" action="{{ route('leagues.join-requests.accept', [$ownedLeague, $joinRequest]) }}">
                                                @csrf
                                                <button class="rounded-lg bg-green-600 px-3 py-2 text-xs font-black uppercase text-white">
                                                    Accept
                                                </button>
                                            </form>

                                            <form method="POST" action="{{ route('leagues.join-requests.reject', [$ownedLeague, $joinRequest]) }}">
                                                @csrf
                                                <button class="rounded-lg bg-gray-200 px-3 py-2 text-xs font-black uppercase text-gray-700">
                                                    Reject
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="mt-4 rounded-lg border border-dashed border-gray-200 px-4 py-8 text-center text-sm italic text-gray-500">
                                No pending requests.
                            </p>
                        @endif
                    </div>

                    <div class="rounded-lg bg-white p-5 shadow sm:p-6">
                        <h3 class="font-['Bebas_Neue'] text-3xl text-[#444]">Members</h3>

                        <div class="mt-4 space-y-3">
                            @foreach($ownedLeague->members as $member)
                                <div class="flex items-center justify-between gap-3 rounded-lg border border-gray-100 bg-gray-50 p-4">
                                    <div class="min-w-0">
                                        <p class="truncate font-black text-gray-800">
                                            {{ $member->name }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{ $member->email }}
                                        </p>
                                    </div>

                                    @if($member->id === $ownedLeague->owner_id)
                                        <span class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-black uppercase text-yellow-700">
                                            Owner
                                        </span>
                                    @else
                                        <form method="POST" action="{{ route('leagues.members.destroy', [$ownedLeague, $member]) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="rounded-lg bg-red-50 px-3 py-2 text-xs font-black uppercase text-red-600">
                                                Remove
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>
            @endif
        </div>
    </div>
</x-app-layout>
