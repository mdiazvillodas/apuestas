<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">
            Events
        </h2>
    </x-slot>

    <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto px-4">

            {{-- Create --}}
            <div class="mb-6">
                <a
                    href="{{ route('admin.events.create') }}"
                    class="inline-block px-4 py-2 bg-indigo-600 rounded-lg text-white"
                >
                    + Create Event
                </a>
            </div>

            {{-- Tabs --}}
            <div class="flex gap-2 mb-6">
                <button data-tab="draft"
                        class="tab-btn px-4 py-2 rounded-lg font-bold bg-white shadow">
                    Draft
                </button>

                <button data-tab="open"
                        class="tab-btn px-4 py-2 rounded-lg font-bold bg-gray-200">
                    Open / Settle
                </button>

                <button data-tab="finished"
                        class="tab-btn px-4 py-2 rounded-lg font-bold bg-gray-200">
                    Finished
                </button>
            </div>

            {{-- Draft --}}
            <div class="tab-content" id="draft">
                @include('admin.events.partials.list', [
                    'events' => $events->where('status', 'draft'),
                    'mode' => 'draft'
                ])
            </div>

            {{-- Open --}}
            <div class="tab-content hidden" id="open">
                @include('admin.events.partials.list', [
                    'events' => $events->where('status', 'open'),
                    'mode' => 'open'
                ])
            </div>

            {{-- Finished --}}
            <div class="tab-content hidden" id="finished">
                @include('admin.events.partials.list', [
                    'events' => $events->where('status', 'finished'),
                    'mode' => 'finished'
                ])
            </div>

        </div>
    </div>

    {{-- JS mínimo para tabs --}}
    <script>
        const buttons = document.querySelectorAll('.tab-btn');
        const contents = document.querySelectorAll('.tab-content');

        buttons.forEach(btn => {
            btn.addEventListener('click', () => {
                buttons.forEach(b => b.classList.replace('bg-white', 'bg-gray-200'));
                btn.classList.replace('bg-gray-200', 'bg-white');

                contents.forEach(c => c.classList.add('hidden'));
                document.getElementById(btn.dataset.tab).classList.remove('hidden');
            });
        });
    </script>
</x-app-layout>
