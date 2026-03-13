<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Admin Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold mb-4">
                    Welcome Admin 👑
                </h3>

                <p>
                    Only admins should see this page.
                </p>
            </div>
                <div class="mb-6">
                    <a
                        href="/admin/run-fixture-sync"
                        class="inline-block px-4 py-2 bg-indigo-600 rounded-lg text-white"
                    >
                        Correr API
                    </a>
                </div>            
        </div>
    </div>
</x-app-layout>
