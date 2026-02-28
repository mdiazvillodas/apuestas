<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">
            Editar evento
        </h2>
    </x-slot>

    <div class="py-6 max-w-xl mx-auto">
        <div class="bg-white p-6 rounded shadow">

            <h3 class="font-bold mb-4">
                {{ $event->title }}
            </h3>

            <form method="POST" action="{{ route('admin.events.update', $event) }}">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block text-sm font-bold mb-1">
                        Betting opens at
                    </label>
                    <input
                        type="datetime-local"
                        name="betting_opens_at"
                        value="{{ optional($event->betting_opens_at)->format('Y-m-d\TH:i') }}"
                        class="w-full border rounded px-3 py-2"
                    >
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold mb-1">
                        Betting closes at
                    </label>
                    <input
                        type="datetime-local"
                        name="betting_closes_at"
                        value="{{ optional($event->betting_closes_at)->format('Y-m-d\TH:i') }}"
                        class="w-full border rounded px-3 py-2"
                    >
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-bold mb-1">
                        Event starts at
                    </label>
                    <input
                        type="datetime-local"
                        name="starts_at"
                        value="{{ optional($event->starts_at)->format('Y-m-d\TH:i') }}"
                        class="w-full border rounded px-3 py-2"
                    >
                </div>

                <div class="flex gap-2">
                    <button
                        type="submit"
                        class="px-4 py-2 bg-blue-600 text-white font-bold rounded"
                    >
                        Guardar cambios
                    </button>

                    <a
                        href="{{ route('admin.events.index') }}"
                        class="px-4 py-2 bg-gray-200 rounded"
                    >
                        Cancelar
                    </a>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
