<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Import Events (CSV)
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white rounded-xl p-6 shadow border border-gray-200">

                <p class="text-sm text-gray-600 mb-4">
                    Upload a CSV file to create multiple events at once.
                    <br>
                    <strong>Teams must already exist.</strong>
                </p>

                <form
                    method="POST"
                    action="/admin/events/import"
                    enctype="multipart/form-data"
                    class="space-y-4"
                >
                    @csrf

                    <div>
                        <input
                            type="file"
                            name="csv"
                            accept=".csv"
                            required
                            class="block w-full border rounded-lg p-2"
                        />
                    </div>

<button
    type="submit"
    class="w-full py-3 rounded-xl bg-red-500 font-black uppercase text-white"
>
    Import CSV V2
</button>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>
