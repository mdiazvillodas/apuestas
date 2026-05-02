@if($event->has_score && ($event->is_live || $event->has_final_score))
    <div class="mt-4 rounded-lg border {{ $event->has_final_score ? 'border-gray-200 bg-gray-50' : 'border-yellow-200 bg-yellow-50' }} p-4 text-center">
        <p class="text-xs font-black uppercase {{ $event->has_final_score ? 'text-gray-500' : 'text-yellow-700' }}">
            {{ $event->has_final_score ? 'Final result' : 'Live result' }}
        </p>

        <div class="mt-2 grid grid-cols-[1fr_auto_1fr] items-center gap-3">
            <p class="truncate text-sm font-black text-gray-800">
                {{ $event->teamA?->name ?? 'TBD' }}
            </p>

            <p class="rounded-lg bg-white px-4 py-2 text-xl font-black text-gray-900 shadow-sm">
                {{ $event->team_a_score }} - {{ $event->team_b_score }}
            </p>

            <p class="truncate text-sm font-black text-gray-800">
                {{ $event->teamB?->name ?? 'TBD' }}
            </p>
        </div>

        @if($event->is_live)
            <p class="mt-2 text-[11px] font-bold uppercase text-gray-500">
                Current score from the latest sync
            </p>
        @endif
    </div>
@endif
