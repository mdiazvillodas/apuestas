<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Admin Settings
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                @if (session('success'))
                    <div class="mb-4 rounded-md bg-green-100 text-green-800 px-4 py-2 text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                    Registro de usuarios
                </h3>

                <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <label class="flex items-start gap-3">
                        <input
                            type="checkbox"
                            name="auto_register_coins_enabled"
                            value="1"
                            @checked($autoRegisterCoinsEnabled)
                            class="mt-1 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                        >
                        <span class="text-sm text-gray-700 dark:text-gray-300">
                            Otorgar automáticamente <strong>1000 coins</strong> a cada usuario nuevo al registrarse.
                        </span>
                    </label>

                    <x-primary-button>
                        Guardar configuración
                    </x-primary-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>