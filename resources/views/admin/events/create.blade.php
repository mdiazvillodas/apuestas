<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">
            Create Event
        </h2>
    </x-slot>

    <div class="py-10 bg-gray-100 min-h-screen">
        <div class="max-w-xl mx-auto px-4">

            <div style="margin-top:25px" class="bg-white rounded-xl shadow-lg p-6">

                <form
                    method="POST"
                    action="{{ route('admin.events.store') }}"
                    enctype="multipart/form-data"
                    class="space-y-6"
                >
                    @csrf

                    {{-- Title --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">
                            Event title
                        </label>
                        <input
                            name="title"
                            required
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                        >
                    </div>

                    {{-- Dates --}}
                    <div class="grid grid-cols-1 gap-4">

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">
                                Betting opens at
                            </label>
                            <input
                                type="datetime-local"
                                name="betting_opens_at"
                                required
                                class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                            >
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">
                                Betting closes at
                            </label>
                            <input
                                type="datetime-local"
                                name="betting_closes_at"
                                required
                                class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                            >
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">
                                Event starts at
                            </label>
                            <input
                                type="datetime-local"
                                name="starts_at"
                                required
                                class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                            >
                        </div>

                    </div>

                    {{-- Teams --}}
                    <div class="grid grid-cols-1 gap-6 pt-4 border-t">

                        {{-- Team A --}}
                    <div class="mb-6 relative">
                        <label class="block text-sm font-medium mb-1">Team A</label>

                        <input
                            type="text"
                            id="team-a-input"
                            class="w-full rounded-lg border px-3 py-2"
                            placeholder="Start typing team name..."
                            autocomplete="off"
                        >

                        <input type="hidden" name="team_a_id" id="team-a-id">

                        {{-- Dropdown --}}
                        <div
                            id="team-a-results"
                            class="absolute z-10 w-full bg-white border rounded-lg shadow mt-1 hidden"
                        ></div>

                        <p id="team-a-hint" class="text-xs text-gray-500 mt-1 hidden">
                            Team not found. You’ll be able to create it.
                        </p>
                    </div>

                                            {{-- Team B --}}
                    <div class="mb-6 relative">
                        <label class="block text-sm font-medium mb-1">Team B</label>

                        <input
                            type="text"
                            id="team-b-input"
                            class="w-full rounded-lg border px-3 py-2"
                            placeholder="Start typing team name..."
                            autocomplete="off"
                        >

                        <input type="hidden" name="team_b_id" id="team-b-id">

                        {{-- Dropdown --}}
                        <div
                            id="team-b-results"
                            class="absolute z-10 w-full bg-white border rounded-lg shadow mt-1 hidden"
                        ></div>

                        <p id="team-b-hint" class="text-xs text-gray-500 mt-1 hidden">
                            Team not found. You’ll be able to create it.
                        </p>
                    </div>


                    </div>

                    {{-- Submit --}}
                    <div class="pt-6">
                        <button
                            type="submit"
                            class="text-white w-full h-12 bg-indigo-600 hover:bg-indigo-700 font-bold rounded-xl transition"
                        >
                            Save event
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
<script>
    const inputA = document.getElementById('team-a-input');
    const resultsA = document.getElementById('team-a-results');
    const hiddenA = document.getElementById('team-a-id');
    const hintA = document.getElementById('team-a-hint');

    let controller;

    inputA.addEventListener('input', async () => {
        const q = inputA.value.trim();
        hiddenA.value = '';
        hintA.classList.add('hidden');

        if (q.length < 2) {
            resultsA.classList.add('hidden');
            return;
        }

        if (controller) controller.abort();
        controller = new AbortController();

        const res = await fetch(`/admin/teams/search?q=${encodeURIComponent(q)}`, {
            signal: controller.signal
        });

        const teams = await res.json();

        resultsA.innerHTML = '';

        if (teams.length === 0) {
            hintA.classList.remove('hidden');
            resultsA.classList.add('hidden');
            return;
        }

        teams.forEach(team => {
            const item = document.createElement('div');
            item.className = 'px-3 py-2 cursor-pointer hover:bg-gray-100 flex items-center gap-2';
            item.innerHTML = `
                <img src="/storage/${team.logo_path}" class="w-6 h-6 object-contain">
                <span>${team.name}</span>
            `;

            item.addEventListener('click', () => {
                inputA.value = team.name;
                hiddenA.value = team.id;
                resultsA.classList.add('hidden');
            });

            resultsA.appendChild(item);
        });

        resultsA.classList.remove('hidden');
    });

    document.addEventListener('click', e => {
        if (!resultsA.contains(e.target) && e.target !== inputA) {
            resultsA.classList.add('hidden');
        }
    });
</script>
<script>
    const inputB = document.getElementById('team-b-input');
    const resultsB = document.getElementById('team-b-results');
    const hiddenB = document.getElementById('team-b-id');
    const hintB = document.getElementById('team-b-hint');

    let controllerB;

    inputB.addEventListener('input', async () => {
        const q = inputB.value.trim();
        hiddenB.value = '';
        hintB.classList.add('hidden');

        if (q.length < 2) {
            resultsB.classList.add('hidden');
            return;
        }

        if (controllerB) controllerB.abort();
        controllerB = new AbortController();

        const res = await fetch(`/admin/teams/search?q=${encodeURIComponent(q)}`, {
            signal: controllerB.signal
        });

        const teams = await res.json();

        resultsB.innerHTML = '';

        if (teams.length === 0) {
            hintB.classList.remove('hidden');
            resultsB.classList.add('hidden');
            return;
        }

        teams.forEach(team => {
            const item = document.createElement('div');
            item.className = 'px-3 py-2 cursor-pointer hover:bg-gray-100 flex items-center gap-2';
            item.innerHTML = `
                <img src="/storage/${team.logo_path}" class="w-6 h-6 object-contain">
                <span>${team.name}</span>
            `;

            item.addEventListener('click', () => {
                inputB.value = team.name;
                hiddenB.value = team.id;
                resultsB.classList.add('hidden');
            });

            resultsB.appendChild(item);
        });

        resultsB.classList.remove('hidden');
    });

    document.addEventListener('click', e => {
        if (!resultsB.contains(e.target) && e.target !== inputB) {
            resultsB.classList.add('hidden');
        }
    });
</script>


</x-app-layout>
