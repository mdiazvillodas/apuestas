<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">
            Confirm coin distribution
        </h2>
    </x-slot>

    <div style="margin-top:25px;" class="max-w-xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl shadow p-6 space-y-4">

            <p class="font-bold text-sm">
                You are about to add <span class="text-yellow-600">{{ $amount }}</span> coins to the following users:
            </p>

            <ul class="list-disc pl-6 text-sm">
                @foreach($users as $user)
                    <li>{{ $user->name }}</li>
                @endforeach
            </ul>

            <form method="POST" action="{{ route('admin.coins.confirm') }}">
                @csrf

                {{-- reenviamos los usuarios seleccionados --}}
                @foreach($users as $user)
                    <input type="hidden" name="users[]" value="{{ $user->id }}">
                @endforeach

                <div class="flex gap-3 mt-6">
                    <button
                        type="submit"
                        class="flex-1 bg-yellow-400 py-3 rounded-xl font-bold uppercase text-gray-900"
                    >
                        Confirm
                    </button>

                    <a
                        href="{{ route('leaderboard.index') }}"
                        class="flex-1 text-center py-3 rounded-xl border font-bold uppercase"
                    >
                        Cancel
                    </a>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
