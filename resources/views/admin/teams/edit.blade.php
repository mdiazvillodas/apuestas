<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Edit Team</h2>
    </x-slot>

    <div class="py-6 max-w-md mx-auto">
        <form method="POST"
              action="{{ route('admin.teams.update', $team) }}"
              enctype="multipart/form-data"
              class="bg-white p-6 rounded shadow space-y-4">
            @csrf @method('PUT')

            <div>
                <label class="block text-sm font-medium">Name</label>
                <input name="name"
                       value="{{ $team->name }}"
                       class="w-full border rounded p-2"
                       required>
            </div>

            <div>
                <label class="block text-sm font-medium">Current logo</label>
                <img src="{{ asset('storage/'.$team->logo_path) }}"
                     class="w-16 h-16 object-contain mb-2">
                <input type="file" name="logo" accept="image/*">
            </div>

            <button class="w-full bg-indigo-600 text-white py-2 rounded">
                Update
            </button>
        </form>
    </div>
</x-app-layout>
