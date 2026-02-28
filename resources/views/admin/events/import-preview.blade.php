<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Import Preview
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow rounded-xl p-6">

                <table class="min-w-full border text-sm">
                    <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-3 py-2">Row</th>
                        <th class="border px-3 py-2">Title</th>
                        <th class="border px-3 py-2">Team A</th>
                        <th class="border px-3 py-2">Team B</th>
                        <th class="border px-3 py-2">Opens</th>
                        <th class="border px-3 py-2">Closes</th>
                        <th class="border px-3 py-2">Starts</th>
                        <th class="border px-3 py-2">Status</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach ($preview as $row)
                        <tr class="{{ $row['valid'] ? 'bg-green-50' : 'bg-red-50' }}">
                            <td class="border px-3 py-2 text-center">{{ $row['row'] }}</td>
                            <td class="border px-3 py-2">{{ $row['title'] }}</td>

                            <td class="border px-3 py-2">
                                {{ $row['team_a_name'] ?? '❌ Invalid team' }}
                            </td>

                            <td class="border px-3 py-2">
                                {{ $row['team_b_name'] ?? '❌ Invalid team' }}
                            </td>

                            <td class="border px-3 py-2 text-sm">
                                {{ $row['betting_opens_at'] ?? '—' }}
                            </td>

                            <td class="border px-3 py-2 text-sm">
                                {{ $row['betting_closes_at'] ?? '—' }}
                            </td>

                            <td class="border px-3 py-2 text-sm">
                                {{ $row['starts_at'] ?? '—' }}
                            </td>

                            <td class="border px-3 py-2 text-center font-bold">
                                {{ $row['valid'] ? 'OK' : 'ERROR' }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>

                </table>

                @if ($hasErrors)
                    <div class="mt-4 p-4 bg-red-100 text-red-800 rounded">
                        There are errors in the CSV. Fix them before importing.
                    </div>
                @else
                    <form
                        method="POST"
                        action="{{ route('admin.events.import.confirm') }}"
                        class="mt-6"
                    >
                        @csrf
                        <input type="hidden" name="csv_data" value="{{ $csvData }}">
                        <input type="hidden" name="csv_header" value="{{ $csvHeader }}">

                        <button
                            type="submit"
                            class="px-6 py-3 bg-green-600 text-white rounded-xl font-bold"
                        >
                            Confirm Import
                        </button>
                    </form>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
