<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Teams</h2>
    </x-slot>

    <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
        <a href="{{ route('admin.teams.create') }}"
           class="inline-block mb-4 px-4 py-2 bg-indigo-600 rounded text-white">
            + Create Team
        </a>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white shadow rounded">
            <table class="w-full text-sm">
                <thead class="border-b">
                    <tr>
                        <th class="text-left p-3">Logo</th>
                        <th class="text-left p-3">Name</th>
                        <th class="text-right p-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($teams as $team)
                        <tr class="border-b">
                            <td class="p-3">
                                <img src="{{ asset('storage/'.$team->logo_path) }}"
                                     class="w-8 h-8 object-contain">
                            </td>
                            <td class="p-3 font-bold">{{ $team->name }}</td>
                            <td class="p-3 text-right space-x-2">
                                <a href="{{ route('admin.teams.edit', $team) }}"
                                   class="text-blue-600">Edit</a>

                                <form method="POST"
                                      action="{{ route('admin.teams.destroy', $team) }}"
                                      class="inline">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
