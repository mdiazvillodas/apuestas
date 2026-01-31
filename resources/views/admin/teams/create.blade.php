<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Create Team</h2>
    </x-slot>

    <div style="margin-top: 25px;" class="max-w-xl mx-auto sm:px-6 lg:px-8">
        <form method="POST"
              action="{{ route('admin.teams.store') }}"
              enctype="multipart/form-data"
              class="bg-white p-6 rounded shadow space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium">Name</label>
                <input name="name" class="w-full border rounded p-2" required>
            </div>

            <div>
                <label class="block text-sm font-medium">Logo</label>
                <input type="file" name="logo" accept="image/*" required>
            </div>

            <button class="w-full bg-indigo-600 py-2 rounded">
                Save
            </button>
        </form>
    </div>
</x-app-layout>
